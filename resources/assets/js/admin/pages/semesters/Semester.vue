<template>
  <div class="boxCore" id="semester-information">
    <section class="clearfix">
      <div class="filter_container clearfix">
        <button type="button" class="btn btn-create-semester" @click.stop="onClickCreateSemester()">
          <span class="icon-plus"></span> Create new semester
        </button>
        <span class="search_box">
          <input type="text"
                 placeholder="Search"
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
          <th class="col1 text-left">ID</th>
          <th class="col2 text-left" data-sort-field="name">Name</th>
          <th class="col3 text-left" data-sort-field="start_time">Start_time</th>
          <th class="col4 text-left" data-sort-field="end_time">End_time</th>
          <th class="col5 text-left" data-sort-field="is_register">Status</th>
          <th class="col6 text-right">Actions</th>

          <template slot="body" slot-scope="props">
            <template v-if="rows[ props.index ].editable === false">
              <tr>
                <td class="col1 text-left">
                  {{ props.realIndex }}
                </td>
                <td class="col2 text-left" @click="onClickSemesterClass(rows[ props.index ])" style="cursor: pointer;">
                  {{ rows[ props.index ].name }}
                </td>
                <td class="col3 text-left">
                  {{ rows[ props.index ].start_time }}
                </td>
                <td class="col4 text-left">
                  {{ rows[ props.index ].end_time }}
                </td>
                <td class="col5 text-left">
                  {{ rows[ props.index ].is_register | formatStatus }}
                </td>
                <td class="col6 text-right">
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
                <td class="col2-edit text-left">
                  <input class="form-control" 
                         type="text"
                         name="name"
                         data-vv-as="name"
                         v-validate="'required'"
                         data-vv-validate-on="none"
                         @focus="resetError"
                         v-model="params.name" />
                  <span v-show="errors.has('name')" class="error has-error">
                    {{ errors.first('name') }}
                  </span>
                </td>
                <td class="col3 text-left">
                  <date-picker
                      id="date-picker"
                      width="160px"
                      name="start_time"
                      data-vv-as="start_time"
                      data-vv-validate-on="none"
                      placeholder="YYYY/MM/DD"
                      format="YYYY/MM/DD"
                      v-validate="'required'"
                      v-model="params.start_time"
                      valueType="format"
                      lang="en"
                      @focus="resetError"
                      input-class="form-control"/>
                    <span v-show="errors.has('start_time')" class="error has-error">
                      {{ errors.first('start_time') }}
                    </span>
                </td>
                <td class="col4 text-left">
                  <date-picker
                      id="date-picker"
                      width="160px"
                      name="end_time"
                      data-vv-as="end_time"
                      data-vv-validate-on="none"
                      placeholder="YYYY/MM/DD"
                      format="YYYY/MM/DD"
                      v-validate="'required'"
                      v-model="params.end_time"
                      valueType="format"
                      lang="en"
                      @focus="resetError"
                      input-class="form-control"/>
                    <span v-show="errors.has('end_time')" class="error has-error">
                      {{ errors.first('end_time') }}
                    </span>
                </td>
                <td class="col5 text-left">
                  <label class="switch">
                    <input type="checkbox"
                           v-model="params.is_register"
                           :checked="params.is_register === 1 ? true : false">
                    <span class="slider round"></span>
                  </label>
                  <span class="bonus-status">
                    {{ status === 1 ? 'ON' : 'OFF' }}
                  </span>
                </td>
                <td class="col6 text-right">
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
        titlePage: 'Semester',
        searchKey: '',
        params: {},
        limit: 10,
        column: 6,
        rows: [],
        isLoading: false,
        isUpdate: false,
        status: 0,
      }
    },
    filters: {
      formatStatus: function (val) {
        if(val == 1)
          return 'ON';
        else return 'OFF';
      },
    },
    watch: {
      'params.is_register' () {
        if (this.params.is_register === true || this.params.is_register === 1) {
          this.status = 1;
        }
        else this.status = 0;
      },
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
        return rf.getRequest('SemesterRequest').getSemesters(params);
      },

      onClickSemesterClass(semesterClass) {
        this.$router.push({ name: 'Semester Class', params: { id: semesterClass.id } });
      },

      onClickCreateSemester() {
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

        await this.$validator.validate('name');
        await this.$validator.validate('start_time');
        await this.$validator.validate('end_time');

        if (this.errors.any()) {
          return;
        }
        this.createOrUpdateSemester();

      },

      createOrUpdateSemester() {
        if (!this.isUpdate) {
          this.params.is_register = this.status;
          return this.requestHandler(rf.getRequest('SemesterRequest').createSemester(this.params));
        }
        return this.requestHandler(rf.getRequest('SemesterRequest').updateSemester(this.params));
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
          content     : 'Do you want to remove this semester?',
          onConfirm   :  () => {
            this.onClickRemoveSemester();
          },
          onCancel    : () => {}
        });
      },

      onClickRemoveSemester() {
        this.startSubmit();
        rf.getRequest('SemesterRequest').removeSemester({id: this.params}).then(res => {
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
  .btn-create-semester {
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

  .switch {
    position: relative;
    display: inline-block;
    width: 50px;
    height: 20px;
    vertical-align: sub;
  }

  .switch input {
    opacity: 0;
    width: 0;
    height: 0;
  }

  .slider {
    position: absolute;
    cursor: pointer;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: #ccc;
    -webkit-transition: .4s;
    transition: .4s;
  }

  .slider:before {
    position: absolute;
    content: "";
    height: 20px;
    width: 20px;
    background-color: white;
    -webkit-transition: .4s;
    transition: .4s;
  }

  input:checked + .slider {
    background-color: #2196F3;
  }

  input:focus + .slider {
    box-shadow: 0 0 1px #2196F3;
  }

  input:checked + .slider:before {
    -webkit-transform: translateX(35px);
    -ms-transform: translateX(35px);
    transform: translateX(35px);
  }

  .slider.round {
    border-radius: 34px;
  }

  .slider.round:before {
    border-radius: 50%;
  }

  .bonus-status {
    vertical-align: -webkit-baseline-middle;
    margin-left: 10px;
    cursor: pointer;
  }

  #semester-information {
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
