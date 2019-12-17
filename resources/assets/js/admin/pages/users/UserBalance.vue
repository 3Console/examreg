<template>
  <div id="balance">
    <div class="userInformation boxCore">
      <section class="clearfix">
        <!-- Search -->
        <div class="filter_container clearfix">
          <span class="search_box">
            <input type="text" placeholder="Search"v-on:keyup.enter="search" class="form-control search_input" name="searchKey" v-model="searchKey"/>
          </span>
        </div>

        <div class="clearfix"></div>
        <!-- User Balance -->
        <div class="data_box">
          <data-table :getData="getData"
                      :limit="limit"
                      :widthTable="'100%'"
                      :column="column"
                      ref="datatable"
                      @DataTable:finish="onDatatableFinish">
            <th class="col1 title_name" data-sort-field="full_name">Full Name</th>
            <th class="col2 title_name" data-sort-field="coin">Coin</th>
            <th class="col3 title_name" data-sort-field="bar">Bar</th>
            <th class="col4 title_name text-right">Action</th>
            <template slot="body" slot-scope="props">
              <template v-if="rows[ props.index ].editable === false">
                <tr>
                  <td class="col1">
                    {{ rows[ props.index ].full_name }}
                  </td>
                  <td class="col2">
                    {{ rows[ props.index ].coin | number }}
                  </td>
                  <td class="col3">
                    {{ rows[ props.index ].bar | number }}
                  </td>
                  <td class="col4 text-right">
                    <button type="button" class="btn btn_save_user" @click.stop="onClickEdit(props.index)">
                      <i class="fa fa-pencil"></i>
                    </button>
                  </td>
                </tr>
              </template>
              <template v-else>
                <tr>
                  <td class="col1">
                    {{ rows[ props.index ].full_name }}
                  </td>
                  <td class="col2">
                    <input class="form-control" 
                           type="text"
                           maxlength="150"
                           name="coin"
                           data-vv-as="coin"
                           v-validate="'required|max:255'"
                           data-vv-validate-on="none"
                           @focus="resetError"
                           v-model="params.coin" />
                    <span v-show="errors.has('coin')" class="error has-error">
                      {{ errors.first('coin') }}
                    </span>
                  </td>
                  <td class="col3">
                    <input class="form-control" 
                           type="text"
                           maxlength="150"
                           name="bar"
                           data-vv-as="bar"
                           v-validate="'required|max:255'"
                           data-vv-validate-on="none"
                           @focus="resetError"
                           v-model="params.bar" />
                    <span v-show="errors.has('bar')" class="error has-error">
                      {{ errors.first('bar') }}
                    </span>
                  </td>
                  <td class="col4 text-right">
                    <button type="button" class="btn btn_edit_user" @click.stop="onClickCancel(props.index)">
                      <i class="icon-close"></i>
                    </button>
                    <button type="button" class="btn btn_save_user" @click.stop="onClickSubmit()">
                      <i class="icon-save"></i>
                    </button>
                  </td>
                </tr>
              </template>
            </template>
          </data-table>
        </div>
      </section>
    </div>
  </div>
</template>

<script>
  import rf from '../../lib/RequestFactory';
  import RemoveErrorsMixin from '../../common/RemoveErrorsMixin';

  export default {
    mixins: [RemoveErrorsMixin],
    data() {
      return {
        limit: 10,
        column: 4,
        titlePage: this.$t('user.balances'),
        params: {},
        searchKey: '',
        rows: [],
      }
    },
    methods: {
      onDatatableFinish() {
        this.rows = this.$refs.datatable.rows;
        window._.each(this.rows, item => {
          this.$set(item, 'editable', false);
        });
      },

      onClickEdit(index) {
        this.rows[index].editable = true;
        for(let i = 0; i < this.rows.length; i++) {
          if(i !== index) {
            this.rows[i].editable = false;
          }
        }
        this.params = this.rows[index];
      },

      onClickCancel(index) {
        this.rows[index].editable = false;
        this.refresh();
      },

      async onClickSubmit() {

        if (this.isSubmitting) {
          return;
        }

        await this.$validator.validate('coin');
        await this.$validator.validate('bar');
        if (this.errors.any()) {
          return;
        }
        this.updateUserBalance();

      },

      updateUserBalance() {
        return this.requestHandler(rf.getRequest('AdminRequest').updateUserBalance(this.params));
      },

      requestHandler(promise) {
        this.startSubmit();
        promise.then(res => {
          this.endSubmit();

          this.showSuccess(this.$t('common.update_sucessful'));
          this.refresh();
        })
        .catch(error => {
          this.endSubmit();
          if (!error.response) {
            this.showError(window.i18n.t("common.message.network_error"));
            return;
          }
          this.convertRemoteErrors(error);
        });
      },

      search() {
          this.$refs.datatable.$emit('DataTable:filter', Object.assign(this.params, {search_key: this.searchKey}));
      },

      getData(params) {
        return rf.getRequest('AdminRequest').getUserBalances(params);
      },

      refresh() {
        this.$refs.datatable.refresh();
      },
    },

    mounted() {
      this.$emit('EVENT_PAGE_CHANGE', this);
    }
  }
</script>

<style lang="scss" scoped>
  @import "../../../../sass/variables";
  .col1 {
    width: 250px;
  }

  .col2 {
    width: 150px;
  }

  .col3 {
    width: 150px;
  }

  .col4 {
    width: 50px;
  }

  .col4::after {
    content: none;
  }

  .userInformation {
    width: 750px;
    .filter_container {
      margin: 12px 0px;
      .search_box {
        display: inline-block;
        float: right;
        width: 215px;
        max-width: 100%;
        .search_input {
          background-color: transparent;
          height: 28px;
          border: 1px solid $color_alto;
          padding: 4px 15px;
          line-height: 20px;
          width: 100%;
          overflow: hidden;
          white-space: nowrap;
          text-overflow: ellipsis;
          font-size: $font-small;
        }
      }
    }
    .data_box {
      font-family: "Montserrat", sans-serif;
      .title_name {
        font-size: 14px;
        font-weight: normal;
      }
    }
    table {
      thead {
      }
      tbody {
        tr:hover {
          .btn_edit_user, .btn_save_user {
            background-color: $color_champagne;
          }
        }
        tr {
          .btn_save_user {
            font-size: 12px;
          }
          .btn_edit_user:active,.btn_edit_user:hover, 
          .btn_save_user:active, .btn_save_user:hover {
            background-color: $color_eden;
            color: $color_white;
          }
        }
      }
    }
    .btn_edit_user {
      background-color: transparent !important;
      font-size: $font_semi_big;
      height: 27px;
      padding: 2px 0px;
      line-height: 23px;
      float: right;
      margin-left: 15px;
      margin-right: 15px;
      .icon-save:before {
      color: $color_dove_gray;
      }
      &:active {
        box-shadow: inset 0 0px 0px rgba(0, 0, 0, 0);
      }
      &:hover {
        color: #55d184;
        .icon-save:before {
          color: #55d184;
        }
      }
    }

    .error {
      color: $red;
    }
  }
</style>