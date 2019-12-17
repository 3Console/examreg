<template>
  <div id="admin_page">
    <header v-bind:style="{minWidth: minWidthPage + 'px'}">
      <nav class="admin_page_header">
        <div class="admin_page_header_left">
          <div class="logo">
            AIS-X
          </div>
        </div>
        <div class="admin_page_header_center">
          <span class="label_logo">ADMIN PAGE</span>
        </div>
        <div class="admin_page_header_right">
          <div class="item notification" @click.stop="showNotifications()">
            <img src="/images/admin/icon_email.png" class="icon_email">
            <span class="count_email">{{unreadNotificationCount || 0}}</span>
            <div v-show="isShowNotifications">
              <div id="notificationContainer" v-click-outside="hideNotifications">
                <div id="notificationTitle">알림</div>
                <div id="notificationsBody" class="notifications">
                  <div v-for="notification in notifications"
                       :class="{unread: !notification.read_at}"
                      @click.stop="markAsRead(notification)">
                    <p> {{ notification.data.data }}</p>
                    <i> {{ notification.created_at }} </i>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="item user_email">
            <span>{{ user.email }}</span>
          </div>
          <div class="item logout">
            <form action="/admin/logout" method="POST">
              <input type="hidden" name="_token" :value="csrfToken"/>
              <button type="submit" class="btn-logout">
                로그아웃
              </button>
            </form>
          </div>
        </div>
      </nav>
    </header>
    <div id="admin_page_content" v-bind:style="{minWidth: minWidthPage + 'px'}">
      <div class="admin_page_navbar" v-bind:style="{minHeight: minHeightPage + 'px'}">
        <div class="admin_page_navbar_header">Menu</div>
        <div class="admin_page_navbar_menu">
          <ul>
            <template v-for="route in routes">
              <template v-if="route.nameRouteDisplay && route.children && route.children.length > 0">
                <li @click="route.expandChildren  = !route.expandChildren">
                  <a href="javascript:void(0)">
                    <span class="glyphicon glyphicon-stop"></span>
                    {{route.nameRouteDisplay}}
                  </a>
                  <a href="javascript:void(0)" class="icon_expand_child"
                     v-if="route.children && route.children.length > 0">
                    <span class="glyphicon glyphicon-triangle-bottom" v-if="!route.expandChildren"></span>
                    <span class="glyphicon glyphicon-triangle-top" v-if="route.expandChildren"></span>
                  </a>
                </li>
                <template v-for="childrenRoute in route.children"
                          v-if="route.expandChildren && route.children && route.children.length > 0">
                  <router-link :to="{ name: childrenRoute.name}">
                    <li class="children_route"
                        :class="{'active' : isActivePage(`${route.path}/${childrenRoute.path}`)}">
                      <a href="javascript:void(0)">
                        {{childrenRoute.nameRouteDisplay}}
                      </a>
                    </li>
                  </router-link>
                </template>
              </template>
              <template v-else>
                <router-link :to="{ name: route.name}" v-if="route.nameRouteDisplay">
                  <li :class="{'active' : isActivePage(route.path)}">
                    <a href="javascript:void(0)">
                      <span class="glyphicon glyphicon-stop"></span>
                      {{route.nameRouteDisplay}} 
                      <span class="font_open_sans text_bold" v-if="route.badge">({{route.badge}})</span>
                    </a>
                  </li>
                </router-link>
              </template>
            </template>
          </ul>
        </div>
      </div>
      <div class="admin_page_content_main" v-bind:style="{minWidth: minWidthPage - 268 + 'px', minHeight: minHeightPage + 'px'}">
        <template>
          <div class="admin_page_content_main_title_page">{{ titlePage }}</div>
        </template>
        <div class="admin_page_content_main_body">
          <router-view v-on:EVENT_PAGE_CHANGE="onPageChange"></router-view>
        </div>
      </div>
    </div>
    <message></message>
    <confirmation-modal></confirmation-modal>
    <alert-modal></alert-modal>
  </div>
</template>
<script>
  import Message from '../../desktop/components/common/Message';
  import ConfirmationModal from '../common/ConfirmationModal.vue';
  import AlertModal from '../../home/components/desktop/AlertModal.vue';
  import rf from '../lib/RequestFactory';
  export default {
    components: {
      Message,
      AlertModal,
      ConfirmationModal,
    },

    data() {
      return {
        titlePage: "",
        minHeightPage: 1000,
        minWidthPage: 1000,
        csrfToken: window.csrf_token,
        routes: this.$router.options.routes,
        notifications: [],
        isShowNotifications: false,
        user: {}
      }
    },

    watch: {
      '$route'(to, from) {
        this.expandMenuIfHasSubMenu();
      }
    },

    computed: {
      unreadNotificationCount() {
        return _.chain(this.notifications)
          .filter((item) => {
            return item.read_at === null;
          })
          .size()
          .value();
      }
    },

    methods: {
      expandMenuIfHasSubMenu() {
        _.forEach(this.routes, (route) => {
          if(route.path === "*") {
            return;
          }
          if (this.isActivePage(route.path) && route.children && route.children.length > 0) {
            route.expandChildren = true;
          }
        });
      },

      isActivePage(checkPath) {
        const reg = new RegExp(checkPath);
        let pagePath = this.$route.path;
        return reg.test(pagePath);
      },

      onPageChange(context) {
        this.titlePage = context.titlePage || "";
        this.minHeightPage = Math.max(Number(context.$el.clientHeight) + 250 || 0, document.querySelector('body').clientHeight);
        this.minWidthPage = Math.max(Number(context.$el.clientWidth) + 388 || 0, document.querySelector('body').clientWidth);
      },

      listenForNotification() {
        window.Echo.channel('App.Models.Admin')
          .listen('AdminNotificationUpdated', () => {
            this.getNotifications();
            this.getDepositPageBadge();
            this.getWithdrawPageBadge();
          });
      },

      markAsRead(notification) {
        if (notification.read_at) {
          return;
        }
        rf.getRequest('NotificationRequest').markAsRead(notification.id).then(res => {
          notification.read_at = res.data.read_at;
        });
      },

      showNotifications() {
        this.isShowNotifications = true;
      },

      hideNotifications() {
        this.isShowNotifications = false;
      },

      getNotifications() {
        rf.getRequest('NotificationRequest').getNotifications().then(res => {
          this.notifications = res.data.data;
        });
      },
      getDepositPageBadge() {
        rf.getRequest('AdminRequest').getDepositPageBadge().then((res) => {
          this.setRouteBadge("DepositApplication", res.data);
        });
      },
      getWithdrawPageBadge() {
        rf.getRequest('AdminRequest').getWithdrawPageBadge().then((res) => {
          this.setRouteBadge("WithdrawalApplication", res.data);
        });
      },

      listenForTransactionUpdate() {
        this.$on('UsdTransactionUpdated', () => {
          this.getDepositPageBadge();
          this.getWithdrawPageBadge();
        });
      },

      setRouteBadge(name, badge) {
        const route = _.find(this.routes, (route) => {
          return route.name === name;
        });
        if (route) {
          this.$set(route, 'badge', badge || '');
        }
      }
    },

    mounted() {

      
      rf.getRequest('UserRequest').getCurrentAdmin().then(res => {
        this.user = res.data;
      });
      this.expandMenuIfHasSubMenu();
      this.listenForNotification();
      this.getNotifications();
      this.getDepositPageBadge();
      this.getWithdrawPageBadge();
      this.listenForTransactionUpdate();
    }
  }
</script>

<style type="text/css">
  body {
    background: #fafafa;
    font-family: "Nanum Gothic", sans-serif;
    color: #191919;
  }
 .pagination > li > a {
    padding: 4px 12px;
    line-height: 1.6;
    color: #232323;
    background-color: #fff;
    border: 1px solid #ddd;
    margin-left: 5px;
    margin-right: 5px;
    border-radius: 3px;
  }
   .pagination > li > a:hover {
    background-color: #FFF;
    border: solid 1px #0570bf;
    color: #0570bf;
    }
  .pagination > .active > a,
  .pagination > .active > a:hover,
  .pagination > .active > a:focus,
  .pagination > .active > span,
  .pagination > .active > span:hover,
  .pagination > .active > span:focus {
    background-color: #0570bf;
    border: solid 1px #0570bf;
    color: #FFF;
  }



  *:focus {
    outline: none !important;
  }
  .form-control {
    display: block;
    width: 100%;
    height: 28px;
    padding: 0px 12px;
    font-size: 12px;
    line-height: 1.6;
    color: #555555;
    background-color: #fff;
    background-image: none;
    border: 1px solid #ccd0d2;
    border-radius: 4px;
    -webkit-box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.075);
    box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.075);
    -webkit-transition: border-color ease-in-out 0.15s, box-shadow ease-in-out 0.15s;
    -webkit-transition: border-color ease-in-out 0.15s, -webkit-box-shadow ease-in-out 0.15s;
    transition: border-color ease-in-out 0.15s, -webkit-box-shadow ease-in-out 0.15s;
    transition: border-color ease-in-out 0.15s, box-shadow ease-in-out 0.15s;
    transition: border-color ease-in-out 0.15s, box-shadow ease-in-out 0.15s, -webkit-box-shadow ease-in-out 0.15s;
}
</style>

<style lang="scss" scoped>
  a {
    &:hover,
    &:active,
    &:link {
      text-decoration: none !important;
    }
  }

  #admin_page {
    display: flex;
    flex-direction: column;
    #admin_page_content {
      position: relative;
      display: flex;
      background: #2d323a;
    }
    header {
      width: 100%;
      min-width: 1312px;
      height: 60px;
      background: #FFF;
      //border-bottom: 1px solid rgb(217, 217, 217);
      position: relative;
      font-size: 13pt;
      z-index: 2;
    }
  }

  .admin_page_header {
    width: 100%;
    height: 100%;
    display: flex;
    .admin_page_header_left {
      width: 265px;
      height: 100%;
      min-width: 265px;
      .logo {
        height: 100%;
        width: auto;
        text-transform: uppercase;
        font-size: 17pt;
        font-family: "Nanum Square OTF ExtraBold", sans-serif;
        line-height: 60px;
        margin-left: 45px;
        color: #424242;
        img {
          padding: 11px 10px 11px 15px;
        }
      }
    }
    .admin_page_header_center {
      width: 100%;
      height: 100%;
      padding: 0 10px;
      position: relative;
      .label_logo {
        display: block;
        line-height: 60px;
        font-size: 11pt;
        font-weight: bold;
        color: #424242;
        text-transform: uppercase;
        text-align: center;
        font-family: "Nanum Gothic", sans-serif;
      }
    }
    .admin_page_header_right {
      width: 220px;
      height: 100%;
      min-width: 220px;
      padding: 15px 0px;
      font-size: 9pt;
      .item {
        display: inline-block;
      }
      .notification {
        position: relative;
        cursor: pointer;
        font-family: 'Malgun Gothic', sans-serif;
        margin-top: 3px;
        .icon_email {
          margin: 0 -17px;
          width: 19px;
          color: #FFF;
          height: 17px;
        }
        .count_email {
          position: absolute;
          padding: 1px 6px;
          font-size: 7pt;
          font-weight: bold;
          border: 1px solid #FFF;
          color: #FFF;
          top: -5px;
          right: -10px;
          background: #be0b0b;
          border-radius: 50%;
        }
      }
    }
    .user_email {
      max-width: 116px;
      width: 116px;
      text-overflow: ellipsis;
      overflow: hidden;
      white-space: nowrap;
      font-family: 'Malgun Gothic', sans-serif;
      position: absolute;
      top: 19px;
      right: 90px;
      color: #191919;
    }
    .logout {
      float: right;
      margin-right: 20px;;
      button {
        font-family: "Nanum Gothic", sans-serif;
        background: transparent;
        border: 1px solid #bebebe;
        border-radius: 4px;
        padding: 4px 9px;
        font-size: 9pt;
        line-height: 18px;
        color: #191919;
      }
    }
  }

  .admin_page_navbar {
    overflow: auto;
    font-size: 12px;
    font-weight: 200;
    top: 0px;
    width: 265px;
    min-width: 265px;
    height: 100%;
    color: #e1ffff;
    z-index: 0;
    min-height: 1300px;
    cursor: default;
    background-color:#2d323a;
    .admin_page_navbar_header {
      background-color: #2a2a2a;
      height: 94px;
      line-height: 94px;
      display: block;
      text-align: center;
      font-size: 14pt;
      text-transform: uppercase;
      font-weight: bold;
      color: white;
      z-index: 2;
      font-family: 'Open Sans', sans-serif;
      border-bottom: 1px solid #363b43;
    }
    .admin_page_navbar_menu ul {
      background-color: #2d323a;
      list-style: none;
      padding: 0px;
      margin: 0px;
      line-height: 58px;
      cursor: pointer;
      color: #191919;
      display: block;
      z-index: 2;
    }

    .admin_page_navbar_menu li {
      list-style: none;
      margin: 0px;
      line-height: 58px;
      cursor: pointer;
      font-size: 11pt;
      text-align: left;
      display: block;
      width: 100%;
      height: 100%;
      color: #FFF;
      padding: 0px 0px 0px 17px;
      border-bottom: 1px solid #3a3f4c;
      &:hover {
        background-color: #1e2228 !important;
        border-bottom: 1px solid rgb(242, 242, 242);
        border-bottom: 1px solid #13151b;
      }
      &.active{
        background-color: rgb(242, 242, 242) !important;
        border-bottom: 1px solid rgb(242, 242, 242);
        a {
          color: black;
        }
      }
      a {
        color: white;
        transform: scaleY(1.75);
        font-size: 11pt;
      }
      .glyphicon-stop{
        margin-right: 8px;
      }
      .icon_expand_child{
        float: right;
        margin-right: 24px;

      }
      &.children_route{
        padding-left: 36px;
        background-color: rgb(32, 36, 44);
        border-bottom: 1px solid rgb(39, 44, 54);

        &:hover{
          background: #13151b;
          border-bottom: 1px solid #13151b;
        }
      }
    }
  }

  .admin_page_content_main {
    width: 100%;
    height: 100%;
    background: rgb(242, 242, 242);
    .admin_page_content_main_title_page {
      height: 94px;
      min-height: 94px;
      width: 100%;
      border-bottom: 1px solid #ffffff;
      font-size: 16pt;
      font-family: "Nanum Square OTF ExtraBold", sans-serif;
      line-height: 94px;
      padding-left: 26px;
      background: #d8d8d8;
      color: #191919;
    }
    .admin_page_content_main_body {
      padding: 32px 50px 110px 20px;
      width: 100%;
      display: inline-block;
    }
  }

  #notificationContainer {
    background-color: #fff;
    color: black;
    border: 1px solid rgba(100, 100, 100, 0.4);
    box-shadow: 0 3px 8px rgba(0, 0, 0, 0.25);
    overflow: visible;
    position: absolute;
    top: 30px;
    margin-left: -220px;
    width: 400px;
    z-index: 3;

    // Popup Arrow
    &:before {
      content: '';
      display: block;
      position: absolute;
      width: 0;
      height: 0;
      color: transparent;
      border: 10px solid;
      border-color: transparent transparent #ddd;
      margin-top: -20px;
      margin-left: 200px;
    }
    #notificationTitle {
      text-align: left;
      font-weight: bold;
      padding: 8px;
      font-size: 13px;
      background-color: #ffffff;
      border-bottom: 1px solid #dddddd;
    }
    #notificationsBody {
      padding: 3px;
      max-height: 300px;
      overflow-y: auto;
      overflow-x: hidden;
      :hover {
        background-color: #e9eaed;
        cursor: pointer;
      }
      .unread {
        background-color: #e9eaed;
        cursor: pointer;
      }
      div {
        padding: 4px;
        p {
          text-align: left;
        }
      }
    }
  }
  .font_open_sans {
    font-family: 'Open Sans', sans-serif;
  }
  .text_bold {
    font-weight: bold;
  }
</style>
