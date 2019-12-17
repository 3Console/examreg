<template>
  <div class="content_usersetting">
    <div class="filter_container clearfix">
      <span class="title_item">
        <span class="title_text">{{ $t('user.user') }}</span>
      </span>
      <div class="search_box form-group col-xs-6">
        <input type="text" placeholder="Search" v-on:keyup.enter="search" class="form-control search_input" name="searchKey" v-model="searchKey"/>
      </div>
    </div>
    <div class="row">
      <div class="col-xs-6">
        <div class="datatable">
          <data-table :getData="getData" ref="datatable" :limit="10" :column="5" class="scroll">
            <th class="text-left cl_110" data-sort-field="id">{{ $t('user.id') }}</th>
            <th class="text-left sort" data-sort-field="email">{{ $t('user.email') }}</th>
            <th class="text-left cl_110">{{$t('user.otp')}}</th>
            <th class="text-right cl_110">{{$t('user_setting.balance')}}</th>
            <template slot="body" slot-scope="props">
              <tr v-bind:class="{inactive: props.item.status === 'inactive'}">
                <td class="text-left">{{ props.item.id }}</td>
                <td class="text-left">
                  <div class="item_email_setting">
                    <span class="txt_email_setting">{{ props.item.email }}</span>
                    <span class="tooltip_email_setting">{{ props.item.email }}</span>
                  </div>
                </td>
                <td class="text-left cl_110">
                  <button class="btn" :class="props.item.security_setting.otp_verified ? 'btn_disable' : 'btn_enable'"
                    @click.stop="onClickedUpdateOtp(props.item.security_setting)">
                    {{ props.item.security_setting.otp_verified ? $t('withdraw_setting.disable') : $t('withdraw_setting.enable') }}
                  </button>
                </td>
                <td class="text-right cl_110">
                  <button @click="getBalances(props.item.id)" class="btn btn_view">View</button>
                </td>
              </tr>
            </template>
          </data-table>
        </div>
      </div>
      <div class="col-md-6 col-md-6 col-sm-6">

        <div class="box_table_UserSetting_right">
          <table class="table table_UserSetting_right" :limit="10" :column="4">
            <thead>
              <tr>
                <th class="text-left cl_coin">{{ $t('user_setting.coin') }}</th>
                <th class="text-left cl_address">{{ $t('user_setting.blockchain_address') }}</th>
                <th class="text-left cl_balance">{{ $t('user_setting.balance') }}</th>
                <th class="text-right cl_lable_balance">{{ $t('user_setting.available_balance') }}</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="(item, key) in balances">
                  <td class="text-left">{{ (item.balance ? key : '') | uppercase }}</td>
                  <td class="text-left">{{ item.blockchain_address }}</td>
                  <td class="text-left">{{ item.balance }}</td>
                  <td class="text-right">{{ item.available_balance }}</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
</div>
</template>

<script>
  import rf from '../../lib/RequestFactory';

  export default {
    components: {
    },
    data() {
      return {
        titlePage: this.$t('user.user_setting'),
        searchKey: '',
        balances: []
      }
    },
    methods: {
      showModal (config) {
        const type = config.type;
        const title = config.title;
        const content = config.content;
        const customContent = config.customContent;

        let btnCancelLabel = config.btnCancelLabel || this.$t('common.action.no');
        let btnConfirmLabel = config.btnConfirmLabel || this.$t('common.action.yes');
        if (config.noAction) {
          btnCancelLabel = null;
          btnConfirmLabel = null;
        }
        const onConfirm = config.onConfirm;
        const onCancel = config.onCancel;

        window.ConfirmationModal.show({
          type: type,
          title: title,
          content: content,
          customContent: customContent,
          btnCancelLabel: btnCancelLabel,
          btnConfirmLabel: btnConfirmLabel,
          onConfirm: onConfirm,
          onCancel: onCancel
        });
      },

      search() {
        this.$refs.datatable.$emit('DataTable:filter', {search_key: this.searchKey});
      },

      getData(params) {
        return rf.getRequest('UserRequest').getUsers(params);
      }, 

      getBalances(id) {
        rf.getRequest('AdminRequest').getUserBalances({user_id: id}).then(res => {
          this.balances = res.data;
        });
      },

      onClickedUpdateOtp(setting) {
        const title = setting.otp_verified ? this.$t('user_setting.confirm_off_otp') : this.$t('user_setting.confirm_on_otp');
        this.showModal({
          type: 'confirm',
          title: title,
          onConfirm: () => {
            rf.getRequest('UserRequest').updateSettingOtp(setting.id, !setting.otp_verified).then(res => {
              this.$toastr('success', window.i18n.t('user_setting.update_otp_msg'));
              setting.otp_verified = !setting.otp_verified;
            });
          }
        });
      },

      initEmptyList() {
        let i = 0;
        this.balances = [];
        while (i++ < 10) {
          this.balances.push({});
        }
      }
    },

    mounted() {
      this.initEmptyList();
      this.$emit('EVENT_PAGE_CHANGE', this);
    }
  }
</script>

<style lang="scss" scoped>
  @import "../../../../sass/variables";
  .cl_address {
    width: 350px;
  }
  .cl_80 {
    width: 80px;
  }
  .cl_110 {
    width: 110px;
  }
  .content_modal_withdrawSetting {
    color: $color_mine_shaft;
    font-size: 20px;
    font-weight: 500;
    text-align: center;
    line-height: $font_big_24;
  }

  .content_usersetting {

    .btn_enable {
      background-color: transparent;
      text-transform: uppercase;
      width: 65px;
      height: 23px;
      line-height: 20px;
      padding: 0px 0px;
      text-align: center;
      font-size: $font-smaller;
      font-weight: 600;
      color: $color_cerulean_blue;
      border: 1px solid $color_cerulean_blue;
      border-radius: 20px;
      transition: 0.5s;
      &:hover {
        background-color: $color_cerulean_blue;
        border-color: $color_cerulean_blue;
        color: $color_white;
        transition: 0.5s;
      }
    }
    .btn_disable {
      background-color: transparent;
      text-transform: uppercase;
      width: 65px;
      height: 23px;
      line-height: 20px;
      padding: 0px 0px;
      text-align: center;
      font-size: $font-smaller;
      font-weight: 600;
      color: $color_alizarin_crimson;
      border: 1px solid $color_alizarin_crimson;
      border-radius: 20px;
      transition: 0.5s;
      &:hover {
        background-color: $color_alizarin_crimson;
        border-color: $color_alizarin_crimson;
        color: $color_white;
        transition: 0.5s;
      }
    }
    .btn_view {
      float: right;
      background-color: transparent;
      text-transform: uppercase;
      width: 50px;
      height: 23px;
      line-height: 20px;
      padding: 0px 9px;
      text-align: center;
      font-size: $font-smaller;
      font-weight: 600;
      color: #55d184;
      border: 1px solid #55d184;
      border-radius: 20px;
      transition: 0.5s;
      &:hover {
        background-color: #55d184;
        border-color: #55d184;
        color: $color_white;
        transition: 0.5s;
      }
    }

    .filter_container {
      margin: 12px 0px;

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
    .btn_detail_user {
      float: right;
      background-color: transparent;
      text-transform: uppercase;
      width: 65px;
      height: 23px;
      line-height: 20px;
      padding: 0px 9px;
      text-align: center;
      font-size: $font-smaller;
      font-weight: 600;
      color: #55d184;
      border: 1px solid #55d184;
      border-radius: 20px;
      margin-left: 15px;
      transition: 0.5s;
      &:hover {
        background-color: #55d184;
        border-color: #55d184;
        color: $color_white;
        transition: 0.5s;
      }
    }

    .item_email_setting {
      display: inline-block;
      float: left;
      position: relative;
      .txt_email_setting {
        display: block;
        max-width: 100px;
        overflow: hidden;
        white-space: nowrap;
        text-overflow: ellipsis;
      }
      .tooltip_email_setting {
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
        .tooltip_email_setting {
          display: block;
          transition: 0.5s;
        }
      }
    }
  }
  

  @media only screen and (min-width: 1399px) {
     .content_usersetting .item_email_setting  .txt_email_setting {
        max-width: 250px;
     }
  }
  .box_table_UserSetting_right {
    height: 440px;
    background-color: $color_white; 
    max-height: 440px;
    overflow-y: auto; 
    .table_UserSetting_right {
      tbody {
        tr {
          &:hover {
            background-color: $color_champagne;
          }
        }
      }
    }
  }
  .table_UserSetting_right {
    background-color: $color_white;
    margin: 0;

    thead {
      th{
        position: relative;
        background-color: $color_white;
        line-height: 15px;
        font-size: $font_root;
        font-weight: 500;
        color: $color_grey;
        padding: 5px 15px;
        border-bottom: 1px solid $color_alto;
        height: 40px; 
        display: table-cell;
        vertical-align: inherit;
      }
    }
    tbody {
       tr {
        vertical-align: top;
        overflow-y: hidden;
        transition-property: height;
        transition-duration: 0.3s, 0.3s;
        transition-timing-function: ease, ease-in;
        height: auto;
        background-color: $color_white;

        td {
          height: 40px;
          overflow: initial;
          line-height: 23px;
          font-size: $font_root;
          font-weight: 500;
          color: $color_mine_shaft;
          padding: 8px 15px 4px 15px;
          border-top: 1px solid $color_catskill_white;
        }
      }
    }

  }
</style>
