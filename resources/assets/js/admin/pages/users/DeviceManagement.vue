<template>
  <div id="deviceManagement" class="deviceManagement boxCore">
    <div class="filter_container clearfix">
      <span class="title_item">{{ $t('device_management.title_device_management') }}</span>
      <span class="search_box">
        <input type="text" placeholder="Search" v-on:keyup.enter="search" class="form-control search_input" name="searchKey" v-model="searchKey"/>
      </span>
    </div>

    <div class="clearfix"></div>

    <div class="content_device_management clearfix">
      <div class="row">
        <div class="col-xs-4">
          <div class="datatable">
            <data-table :getData="getData" ref="datatable" :limit="10" :column="2"  class="scroll">
              <th class="text-left">{{ $t('user.email') }}</th>
              <th class="text-right"><!-- {{$t('user.balance')}} --></th>
              <template slot="body" slot-scope="props">
                <tr v-bind:class="{inactive: props.item.status === 'inactive'}">
                  <td class="text-left">
                    <div class="item_email_setting">
                      <span class="txt_email_setting">{{ props.item.email }}</span>
                      <span class="tooltip_email_setting">{{ props.item.email }}</span>
                    </div>
                  </td>
                  <td class="text-right">
                    <button class="btn btn_view" @click="onClickedView(props.item)">View</button>
                  </td>
                </tr>
              </template>
            </data-table>
          </div>
        </div>
        <div class="col-xs-8">
          <user-login-history :userId="selectedUserId"/>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
  import rf from '../../lib/RequestFactory';
  import InputOnlyNumber from '../../common/InputOnlyNumber';
  import UserLoginHistory from './UserLoginHistory';
  
  export default {
    components: {
      UserLoginHistory,
      InputOnlyNumber
    },
    data() {
      return {
        titlePage: this.$t('device_management.title_device_management'),
        searchKey: '',
        selectedUserId: '',
      }
    },
    methods: {
      onClickedView(item) {
        this.selectedUserId = item.id;
      },
      getData(params) {
          return rf.getRequest('UserRequest').getUsers(params);
      },
      search() {
        this.$refs.datatable.$emit('DataTable:filter', {search_key: this.searchKey});
      },
      onSearchReferrer() {
        let params = { user_id: this.userSelecting.id};
        if (this.searchKey && !!this.searchKey.trim()) {
          params.search_key = this.searchKey;
        }
        rf.getRequest('UserRequest').getReferrers(params).then(res => {
            this.userReferrers = res.data.data;
          }
        );
      },
    },
    mounted() {
      this.$emit('EVENT_PAGE_CHANGE', this);
    }
  }
</script>

<style lang="scss" scoped>
  @import "../../../../sass/variables";
  .deviceManagement {
    width: 100%;
  
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
        margin-right: 5px;
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
   .deviceManagement .item_email_setting  .txt_email_setting {
      max-width: 300px;
   }
  }
</style>

<style lang="scss">
  #deviceManagement {
    .txt_nb_itme {
      display: none;
    }
  }
</style>
