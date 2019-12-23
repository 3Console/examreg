<template>
  <div class="boxCore" id="user_information">
    <section class="clearfix">
      
    <div class="filter_container clearfix">
      <button type="button" class="btn btn-create-user" @click.stop="showFileInput">
        <span class="icon-plus"></span> {{ $t('user.import_csv') }}
      </button>
      <span class="file-name">{{ fileCSV ? fileCSV.name : '' }}</span>
      <input type="file" class="custom-file-input"
             id="customFile"
             @change="onChangeUploadCSV"
             accept=".csv"
             v-validate="'required'"
             name="fileCSV"
             @focus="resetError"
             ref="input"
             data-vv-validate-on="none">
      <div v-show="errors.has('fileCSV')" class="error has-error" id="error">
        {{ errors.first('fileCSV') }}
      </div>
      <div v-show="errors.has('file')" class="error has-error" id="error">
        {{ errors.first('file') }}
      </div>
      <button type="button" class="btn btn-create-user" v-if="fileCSV" @click="uploadCSV">{{ $t('common.action.submit') }}</button>
      <span class="search_box">
        <input type="text"
               :placeholder="$t('common.placeholders.search')"
               v-on:keyup.enter="search"
               class="form-control search_input"
               name="searchKey"
               v-model="searchKey"/>
      </span>
    </div>

    <div class="clearfix"></div>

    <div class="datatable">
        <data-table :getData="getData" ref="datatable" :limit="10" :column="6" @DataTable:finish="onDatatableFinish" class="scroll">
          <th class="cl0 text-left">{{ $t('user.id') }}</th>
          <th class="cl1 text-left" data-sort-field="msv">{{ $t('user.msv') }}</th>
          <th class="cl1 text-left" data-sort-field="full_name">{{ $t('user.full_name') }}</th>
          <th class="cl1 text-left" data-sort-field="username">{{ $t('user.username') }}</th>
          <th class="cl2 text-left" data-sort-field="email">{{ $t('user.email') }}</th>
          <th class="cl3 text-left" data-sort-field="unit">{{ $t('user.unit') }}</th>
          <template slot="body" slot-scope="props">
            <tr>
              <td class="cl0 text-left">
                {{ props.realIndex }}
              </td>
              <td class="cl1 text-left">
                {{ props.item.msv }}
              </td>
              <td class="cl1 text-left">
                {{ props.item.full_name }}
              </td>
              <td class="cl1 text-left">
                {{ props.item.username }}
              </td>
              <td class="cl2 text-left">
                {{ props.item.email }}
              </td>
              <td class="cl3 text-left">
                {{ props.item.unit }}
              </td>
            </tr>
          </template>
        </data-table>
      </div>
      <div id="loading" v-if="isLoading === true">
        <div id="loading-content">
          <i class="fa fa-spinner fa-spin"></i>
        </div>
      </div>
    </section>
  </div>
</template>

<script>
  import rf from '../../lib/RequestFactory';
  import InputOnlyNumber from '../../common/InputOnlyNumber';
  import SelectBox from '../../components/SelectBox';
  import Modal from '../../components/Modal';
  import RemoveErrorsMixin from '../../common/RemoveErrorsMixin';

  export default {
    components: {
      InputOnlyNumber,
      SelectBox,
      Modal
    },
    mixins: [RemoveErrorsMixin],
    data() {
      return {
        titlePage: this.$t('user.user_information'),
        searchKey: '',
        userTypeList: [
          this.$t('user.normal'),
          this.$t('user.bot'),
          this.$t('user.referrer'),
        ],
        userStatusList: [
          this.$t('user.inactive'),
          this.$t('user.active'),
        ],
        maxSecurityLevels: [1, 2, 3, 4, 5],
        params: {},
        userModalName: 'UserModal',
        rows: [],
        fileCSV: null,
        isLoading: false,
      }
    },
    filters: {
      formatDate: function (val) {
        return val ? val.substring(0, 10) : '';
      },
      formatSex: function (val) {
        if(val == 1)
          return 'Male';
        else return 'Female';
      },
      statusLabel: function (val) {
        if(val === 'inactive') return window.i18n.t('user.inactive');
        return window.i18n.t('user.active');
      },

      labelUserType: function (val) {
        switch (val) {
          case 'bot': return 'bot';
          default: return window.i18n.t('user.normal');
        }
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
        return rf.getRequest('AdminRequest').getUsers(params);
      },

      showFileInput() {
        this.$refs.input.click();
      },

      onChangeUploadCSV(e) {
        this.fileCSV = null;
        this.resetError();
        let files = e.target.files || e.dataTransfer.files;
        if (!files.length) {
          return;
        }
        this.fileCSV = files[0];
      },

      async uploadCSV() {
        this.resetError();
        if(this.isSubmitting) {
          return;
        }
        await this.$validator.validate('fileCSV');
        if (this.errors.any()) {
          return;
        };

        if(this.fileCSV.name.split(".").pop() != 'csv'){
          this.errors.add({field: 'fileCSV', msg: 'Please select CSV file type'});
          return;
        }

        let formData = new FormData();
        formData.append('file', this.fileCSV, this.fileCSV.name);


        this.data = rf.getRequest('AdminRequest').uploadCSV(formData);
        this.data.then(res => {
          this.showSuccess('Submit file CSV successful');
          this.refresh();
        }).catch(error => {
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

      onClickedUpdate(index) {
        this.rows[index].editable = true;
        for(let i = 0; i < this.rows.length; i++) {
          if(i !== index) {
            this.rows[i].editable = false;
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

        await this.$validator.validate('level'); 
        if (this.errors.any()) {
          return;
        }
        this.isLoading = true;
        this.updateUser();

      },
      updateUser() {
        return this.requestHandler(rf.getRequest('AdminRequest').updateUser(this.params));
      },

      requestHandler(promise) {
        this.startSubmit();
        promise.then(res => {
          this.endSubmit();
          this.refresh();
          this.isLoading = false;
          this.showSuccess(this.$t('common.update_sucessful'));
        })
        .catch(error => {
          this.endSubmit();
          if (!error.response) {
            this.showError(window.i18n.t("common.message.network_error"));
            return;
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
  @import "../../../../sass/variables";
  #loading {
    width: 100%;
    height: 100%;
    top: 0px;
    left: 0px;
    position: fixed;
    display: block;
    opacity: 0.7;
    background-color: #fff;
    z-index: 9999;
    text-align: center;
  }

  #loading-content {
    position: absolute;
    top: 50%;
    left: 50%;
    text-align: center;
    z-index: 100;
    i {
      font-size: 40px;
    }
  }
  .box_select_level {
    width: 40px;
  }
  .box_select_user {
    width: 85px;
  }
  .cl1{
    width: 300px;
  }
  .cl3 {
    input {
      width: 40px;
      padding: 0px 0px 0px 15px;
      height: 30px;
    }
  }
  .cl5 {
    select{
      background: $color_white;
    }
  }
  .cl6 {
    width: 200px;
  }
  .text-left, .text-right{
    &:after {
      content: none;
    }
  }
  .switch {
  position: relative;
  display: inline-block;
  width: 60px;
  height: 34px;
  left: 40px;
  top: -5px;
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
    .radio-group {
        display: inline;
        padding-left: 5px;
      }
    .submit {
      text-align: center;
      margin-top: 30px;
      margin-bottom: 10px;
      cursor: pointer;
      a {
        border: 3px solid $color_green_vogue;
        border-radius: 50px;
        color: $color_white;
        background: $color_green_vogue;
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
    .btn-create-user {
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

    .file-name {
      margin-left: 10px;
      font-size: 14px;
    }

    #error {
      display: inline;
      position: relative;
      top: 25px;
      left: -83px;
    }

  #user_information {
    width: 1550px;
  
    .edit-input-number {
      input {
        width: 80px;
        padding-left: 5px;
        background: transparent;
        box-shadow: inset 0 0px 0px rgba(0, 0, 0, 0);
        border-radius: 0px;
        border: 1px solid $color_alto;
      }
    }
    .filter_container {
      margin: 12px 0px;
      margin-bottom: 25px;
      .title_item {
        color: $color_mine_shaft;
        font-size: $font_big_20;
        font-weight: 500;
        line-height: 28px;
        float: left;
      }

      // .custom-file {
      //   cursor: pointer;
      //   .custom-input-file {
      //     @include custom-file-input();
      //     width: 50%;
      //     top: -115px;
      //     left: 200px;
      //     text-align: left;
      //   }

        
      //   .custom-file-label {
      //     cursor: pointer;
      //   }
      // }
      input[type='file'] {
        width: 0px;
        display: inline;
        visibility: hidden;
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
    .select_user {
      box-shadow: inset 0 0px 0px rgba(0, 0, 0, 0);
      border-radius: 0px;
      width: 80px;
      height: 27px;
      border: 1px solid $color_alto;
      line-height: 20px;
      padding: 3px 5px;
      background-color: transparent;
    }
    .item_email_user {
      display: inline-block;
      float: left;
      position: relative;
      .txt_email_user {
        display: block;
        max-width: 95px;
        overflow: hidden;
        white-space: nowrap;
        text-overflow: ellipsis;
      }
      .tooltip_email_user {
        position: absolute;
        top: 0px;
        left: 0px;
        line-height: 20px;
        padding: 5px 20px;
        left: 100%;
        background-color: $color_white;
        white-space: nowrap;
        width: auto;
        z-index: 10;
        font-size: $font_root;
        font-weight: 500;
        color: $color_mine_shaft;
        transition: 0.5s;
        display: none;
        box-shadow: 1px 1px 15px rgba(0, 0, 0, 0.4);
        &:after {
          right: 100%;
          top: 50%;
          border: solid transparent;
          content: " ";
          height: 0;
          width: 0;
          position: absolute;
          pointer-events: none;
          border-color: rgba(136, 183, 213, 0);
          border-right-color: $color_white;
          border-width: 5px;
          margin-top: -5px;
        }
      }
      &:hover {
        .tooltip_email_user {
          display: block;
          transition: 0.5s;
        }
      }
    }
    table {
      thead {
      }
      td {
        img {
          height: 100px;
        }
      }
      tbody {
        tr:hover {
          .btn_update_user, .btn_save_user {
            background-color: $color_champagne;
          }
        }
        tr {
          .btn_update_user:active,.btn_update_user:hover, 
          .btn_save_user:active, .btn_save_user:hover {
            background-color: $color_eden;
            color: $color_white;
          }
        }
      }
    }
  }

  @media only screen and (min-width: 1399px) {
   .userInformation .item_email_user .txt_email_user {
      max-width: 250px;
   }
  }
</style>
