<template>
  <div class="boxCore" id="location-information">
    <section class="clearfix">
      <div class="filter_container clearfix">
        <button type="button" class="btn btn-create-location" @click.stop="onClickCreateLocation()">
          <span class="icon-plus"></span> {{ $t('location.btn_create') }}
        </button>
        <span class="search_box">
          <input type="text"
                 :placeholder="$t('common.placeholders.search')"
                 v-on:keyup.enter="search"
                 class="form-control search_input"
                 name="searchKey"
                 v-model="searchKey"/>
        </span>
      </div>

      <div class="datatable">
        <data-table :getData="getData"
                    :limit="limit"
                    :widthTable="'100%'"
                    :column="column"
                    ref="datatable"
                    @DataTable:finish="onDatatableFinish">
          <th class="col1 text-left">{{ $t('location.id') }}</th>
          <th class="col2 text-left" data-sort-field="room">{{ $t('location.room') }}</th>
          <th class="col3 text-left" data-sort-field="address">{{ $t('location.address') }}</th>
          <th class="col4 text-left" data-sort-field="slot">{{ $t('location.slot') }}</th>
          <th class="col5 text-right">{{ $t('location.action') }}</th>

          <template slot="body" slot-scope="props">
            <template v-if="rows[ props.index ].editable === false">
              <tr>
                <td class="col1 text-left">
                  {{ props.realIndex }}
                </td>
                <td class="col2 text-left">
                  {{ rows[ props.index ].room }}
                </td>
                <td class="col3 text-left">
                  {{ rows[ props.index ].address }}
                </td>
                <td class="col4 text-left">
                  {{ rows[ props.index ].slot }}
                </td>
                <td class="col5 text-right">
                  <button type="button" class="btn btn_edit_user" @click.stop="onClickRemove(rows[ props.index ].id)">
                    <i class="fa fa-trash-o"></i>
                  </button>
                  <button type="button" class="btn btn_save_user" @click.stop="onClickEdit(props.index)">
                    <i class="fa fa-pencil"></i>
                  </button>
                </td>
              </tr>
            </template>
            <template v-else>
              <tr>
                <td class="col1"></td>
                <td class="col2 text-left">
                  <input class="form-control" 
                         type="text"
                         name="room"
                         data-vv-as="room"
                         v-validate="'required'"
                         data-vv-validate-on="none"
                         @focus="resetError"
                         v-model="params.room" />
                  <span v-show="errors.has('room')" class="error has-error">
                    {{ errors.first('room') }}
                  </span>
                </td>
                <td class="col3 text-left">
                  <input class="form-control" 
                         type="text"
                         name="address"
                         data-vv-as="address"
                         v-validate="'required'"
                         data-vv-validate-on="none"
                         @focus="resetError"
                         v-model="params.address" />
                  <span v-show="errors.has('address')" class="error has-error">
                    {{ errors.first('address') }}
                  </span>
                </td>
                <td class="col4 text-left">
                  <input class="form-control" 
                         type="text"
                         name="slot"
                         data-vv-as="slot"
                         v-validate="'required'"
                         data-vv-validate-on="none"
                         @focus="resetError"
                         v-model="params.slot" />
                  <span v-show="errors.has('slot')" class="error has-error">
                    {{ errors.first('slot') }}
                  </span>
                </td>
                <td class="col5 text-right">
                  <button type="button" class="btn btn_edit_user" @click.stop="onClickCancel()">
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
</template>

<script>
  import rf from '../../lib/RequestFactory';
  import RemoveErrorsMixin from '../../common/RemoveErrorsMixin';

  export default {
    mixins: [RemoveErrorsMixin],
    data() {
      return {
        titlePage: this.$t('location.header'),
        searchKey: '',
        params: {},
        limit: 10,
        column: 5,
        rows: [],
        isLoading: false,
        isUpdate: false,
      }
    },
    methods: {
      onDatatableFinish() {
        this.rows = this.$refs.datatable.rows;
        window._.each(this.rows, item => {
          this.$set(item, 'editable', false);
        });
      },

      search() {
        this.$refs.datatable.$emit('DataTable:filter', Object.assign(this.params, {search_key: this.searchKey}));
      },

      getData(params) {
        return rf.getRequest('LocationRequest').getLocations(params);
      },

      onClickCreateLocation() {
        this.isUpdate = false;
        for(let i = 0; i < this.rows.length; i++) {
          this.rows[i].editable = false;
        }
        this.params = {};
        if (this.rows.length === 0) {
          this.rows.push({ editable: true });
        }
        else if(this.rows[this.rows.length -1].id) {
          this.rows.push({ editable: true });
        }
        else {
          this.rows.splice(this.rows.length -1 , 1);
          this.rows.push({ editable: true });
        }
      },

      onClickEdit(index) {
        this.rows[index].editable = true;
        this.isUpdate = true;
        for(let i = 0; i < this.rows.length; i++) {
          if(i !== index) {
            this.rows[i].editable = false;
          }
          if( !this.rows[i].id) {
            this.rows.splice(i, 1);
          }
        }
        this.params = JSON.parse(JSON.stringify(this.rows[index]));
      },

      onClickCancel() {
        this.refresh();
      },

      async onClickSubmit() {

        if (this.isSubmitting) {
          return;
        }

        await this.$validator.validate('room');
        await this.$validator.validate('address');
        await this.$validator.validate('slot');
        if (this.errors.any()) {
          return;
        }
        this.createOrUpdateLocation();

      },

      createOrUpdateLocation() {
        if (!this.isUpdate) {
          return this.requestHandler(rf.getRequest('LocationRequest').createLocation(this.params));
        }
        return this.requestHandler(rf.getRequest('LocationRequest').updateLocation(this.params));
      },

      requestHandler(promise) {
        this.startSubmit();
        promise.then(res => {
          this.endSubmit();

          if (!this.isUpdate) {
            this.showSuccess(this.$t('common.create_sucessful'));
            this.refresh();
          } else {
            this.showSuccess(this.$t('common.update_sucessful'));
            this.refresh();
          }
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

      onClickRemove(params) {
        this.params = params;
        window.ConfirmationModal.show({
          type        : 'confirm',
          title       : '',
          content     : window.i18n.t("location.message_remove"),
          onConfirm   :  () => {
            this.onClickRemoveLocation();
          },
          onCancel    : () => {}
        });
      },

      onClickRemoveLocation() {
        this.startSubmit();
        rf.getRequest('LocationRequest').removeLocation({id: this.params}).then(res => {
          this.endSubmit();
          this.showSuccess(this.$t('common.remove_sucessful'));
          this.refresh();
        })
        .catch(error => {
          this.endSubmit();
          if (!error.response) {
            this.showError(window.i18n.t("common.message.network_error"));
            return;
          }
          this.convertRemoteErrors(error);
          if (this.errors.has('error')) {
            this.showError(error.response.data.message);
          }
        });
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
  @import "../../../../sass/common";
  .btn-create-location {
    border: 1px solid $color_eden;
    line-height: 20px;
    padding: 3px 12px;
    font-size: $font_root;
    font-weight: bold;
    border-radius: 22px;
    text-align: center;
    color: $color_eden;
    transition: 0.5s;
    min-width: 86px;
    cursor: pointer;
    text-transform: uppercase;
    &:focus,
    &:active,
    &:hover {
      background-color: $color_eden;
      border-color: $color_eden;
      color: $color_white;
      transition: 0.5s;
    }
    .icon-plus {
      font-size: $font_mini_mini;
      float: left;
      margin-right: 5px;
      line-height: 20px;
    }
  }

  #location-information {
    .filter_container {
      margin: 12px 0px;
      width: 100%;
      .title_item {
        color: $color_mine_shaft;
        font-size: $font_big_20;
        font-weight: 500;
        line-height: 28px;
        float: left;
      }
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
          .btn_edit_user:active,.btn_edit_user:hover, 
          .btn_save_user:active, .btn_save_user:hover {
            background-color: $color_eden;
            color: $color_white;
          }
        }
      }
      .col1 {
        width: 60px;
      }
      .col2 {
        width: 150px;
      }
      .col3 {
        width: 200px;
      }
      .col4 {
        width: 150px;
      }
      .col5 {
        width: 150px;
        .icon-close {
          font-size: 11px;
        }
        .icon-save {
          font-size: 12px;
        }
      }
      .col6 {
        width: 140px;
      }
      .col7 {
        width: 100px;
        .icon-close {
          font-size: 11px;
        }
        .icon-save {
          font-size: 12px;
        }
      }
    }
    .modal-title {
      padding-bottom: 10px;
    }

    .error {
      color: $red;
    }
    .row-item {
      margin-bottom: 15px;
      .required {
        color: red;
      }
    }
    .submit {
      text-align: center;
      margin-top: 30px;
      margin-bottom: 10px;
      cursor: pointer;
      a {
        border: 3px solid #55d184;
        border-radius: 50px;
        color: $color_white;
        background: #55d184;
        margin: 0px 15px 0px 10px;
        cursor: pointer;
        &:hover, active, focus {
          color: $deep_sapphire;
          background: $color_white;
          text-decoration: none;
        }
        width: 60%;
        height: 40px;
        padding: 10px 50px;
      }
    }

  }
</style>
