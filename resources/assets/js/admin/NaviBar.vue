<template>
  
  <header class="main-header">
    <!-- Logo -->
    <a href="/" class="logo">
      <!-- mini logo for sidebar mini 50x50 pixels -->
      <!-- <span class="logo-mini"><img src="/images/logo.png" alt=""></span> -->
      <!-- logo for regular state and mobile devices -->
      <span class="logo-lg">
        <img src="/images/default-logo.png">
        <span>ExamReg</span>
      </span>
    </a>
    
    <!-- Header Navbar: style can be found in header.less -->
    <nav class="navbar_admin">
      <!-- Sidebar toggle button-->
      <a href="#" class="icon-menu button_navbar" data-toggle="push-menu" role="button">
        <span class="sr-only">Toggle navigation</span>
      </a>

      <!-- Navbar Right Menu -->
      <div class="navbar_admin_custom">
        <ul class="navbar_nav">

          <!-- <li class="item dropdown notifications-menu">
            <a href="#" class="dropdown-toggle notify_head" data-toggle="dropdown">
              <i class="icon-bell"></i>
              <span class="number_warning">{{ unreadNotificationsCount }}</span>
            </a>
            <div class="dropdown-menu">
              <ul class="list_warning">
                <li class="header"><span>You have {{ unreadNotificationsCount }} notifications</span></li>
                <li>
                 <span>inner menu: contains the actual data</span>
                 <ul class="menu">
                   <li v-for="notification in notifications"
                       :class="{unread: !notification.read_at}"
                       @click.stop="markAsRead(notification)">
                     <a href="javascript:void(0)" :title="notification.data.data">
                       {{notification.data.data}}
                     </a>
                   </li>
                 </ul>
                </li>
                <li class="footer"><a href="#">View all</a></li>
              </ul>
            </div>
          </li> -->
          <li class="item user_navbar">
            <i class="icon-avatar"></i>
            <span class="email_user_navbar">{{ currentAdmin.email }}</span>
          </li>
          
          <li class="item dropdown notifications-menu">
            <!-- <a href="#" class="dropdown-toggle lang_show"  data-toggle="dropdown">  -->
              <!-- <span class="txt_lang_show">EN</span> -->
              <!-- <i class="icon_dropdow icon-arrow1"></i> -->
            <!-- </a> -->
            <!-- <div class="dropdown-menu">
              <ul class="list_lang">
                <li>
                  <a @click="changeLanguage('en')" :class="{'active': lang == 'en'}">
                   <img class="img_lang_navbar" src="/images/flags/uk_24_36.png" height="14px"/> English
                  </a>
                </li>
                <li>
                  <a @click="changeLanguage('vi')" :class="{'active': lang == 'vi'}">
                   <img class="img_lang_navbar" src="/images/flags/vietnam_24_36.png" height="14px"/> Tiếng Việt
                  </a>
                </li>
              </ul>
            </div> -->
          </li>
          
          <i class="icon-checked"></i>
          <li class="item dropdown notifications-menu">
           <a href="#" class="dropdown-toggle button_out_admin">
             <i class="" @click.stop="$refs.logout.submit()">Logout</i>
           </a>
           <form action="/admin/logout" method="POST" ref=logout>
             <input type="hidden" name="_token" :value="csrfToken"/>
           </form>
         </li>

        </ul>
      </div>

    </nav>

  </header>
</template>

<script>
  import {mapGetters, mapActions} from 'vuex';
  import rf from './lib/RequestFactory';
  import RemoveErrorsMixin from './common/RemoveErrorsMixin';

  export default {
    mixins: [RemoveErrorsMixin],
    data() {
      return {
        csrfToken: window.csrf_token,
        notifications: [],
        lang: this.$route.query.lang || document.documentElement.lang,
        currentAdmin: {},
      }
    },

    computed: {
      unreadNotificationsCount() {
        return _.chain(this.notifications)
          .filter((item) => {
            return item.read_at === null;
          })
          .size()
          .value();
      }
    },

    methods: {
      markAsRead(notification) {
        if (notification.read_at) {
          return;
        }
        rf.getRequest('NotificationRequest').markAsRead(notification.id).then(res => {
          notification.read_at = res.data.read_at;
        });
      },

      listenForNotification() {
        window.Echo.channel('App.Models.Admin')
          .listen('AdminNotificationUpdated', () => {
            this.getNotifications();
          });
      },

      getNotifications() {
        rf.getRequest('NotificationRequest').getNotifications().then(res => {
          this.notifications = res.data.data;
        });
      },

      changeLanguage(lang) {
        const currentRouter = this.$router.currentRoute;
        const newQuery = Object.assign(currentRouter.query, {lang: lang});

        const params = this.toParams(newQuery);
        window.location.href = `${window.location.origin}${window.location.pathname}?${params}`; 
      },

      toParams(obj) {
        return Object.keys(obj).map(function (key) {
          return `${key}=${obj[key]}`;
        }).join('&');
      },

      getCurrentAdmin() {
        rf.getRequest('UserRequest').getCurrentAdmin().then(res => {
          this.currentAdmin = res.data || {};
        }).catch(error => {
          if (!error.response) {
            Message.error(window.i18n.t('common.message.network_error'), {}, { position: "bottom_left" });
            return;
          }
          this.convertRemoteErrors(error);
        });
      }
    },
    mounted() {
      this.getCurrentAdmin();
      // this.getNotifications();
      // this.listenForNotification();
    }
  }
</script>

<style lang="scss">
  @import "../../sass/variables";
  .sidebar-mini.sidebar-collapse .main-header {

    .main-header {

      .logo {
        width: 50px;

        .logo-mini {
          display: block;
          margin-left: -15px;
          margin-right: -15px;
          font-size: $font_big_20;
        }
        .logo-lg {
          display: none;
        }
      }
    }
  }
</style>

<style lang="scss" scoped>
  @import "../../sass/variables";
  
  
  .main-header {
    position: relative;
    max-height: 100px;
    z-index: 1030;

    .logo {
      background-color: $color_bright_gray;
      border-bottom: 1px solid rgba(255, 255, 255, 0.1);
      width: 50px;
      transition: width 0.3s ease-in-out;
      display: block;
      float: left;
      height: 50px;
      font-size: $font_big_20;
      line-height: 50px;
      text-align: center;
      width: 230px;
      font-family: $font_family_default;
      padding: 0 15px;
      font-weight: 300;
      overflow: hidden;
      
      &:hover {
        background-color: $color_bright_gray;
      }
      .logo-mini {
        display: none;

        img {
          width: 32px;
          height: 32px;
        }
      }
      .logo-lg {
        font-family: "OpenSans-Bold", sans-serif;
        letter-spacing: 2px;
        img {
          width: 32px;
          height: 32px;
          position: relative;
          top: -2px;
        }
        span {
          color: #ffffff;
          font-size: $font_semi_big;
          font-weight: 600;
          display: inline-block;
          line-height: 32px;
        }
      }
    }
    .navbar_admin {

      .button_navbar {
        display: block;
        width: 50px;
        height: 50px;
        float: left;
        text-align: center;
        line-height: 50px;
        overflow: hidden;
        color: #55d184;
        font-size: $font_big_20;

        &:hover {
          color: #55d184;
        }
      }
      .navbar_admin_custom {
        float: right;
        margin-right: 20px;

        .navbar_nav {
          margin: 13px 0px 10px 0px;

          .item {
            display: inline-block;
            float: left;
            font-size: $font_small;
            color: $color_white;
            margin-left: 8px;
            
            a {
              color: $color_white;
              display: inline-block;
            }
            .notify_head {
              position: relative;
              padding-left: 10px;

              .icon-bell {
                font-size: $font_big_23;
              }
              .number_warning{
                position: absolute;
                display: block;
                min-width: 15px;
                height: 15px;
                padding: 0px 3px;
                border-radius: 8px;
                background-color: #55d184;
                text-align: center;    
                line-height: 15px;
                overflow: hidden;
                top: 0px;
                left: 0px;
              }
            }
            .lang_show {
              display: inline-block;
              float: left;
              line-height: 20px;
              height: 23px;
              border: solid 1px $color_white;
              border-radius: 15px;
              padding: 0px 10px;
              text-transform: uppercase;
              min-width: 47px;

              .icon_dropdow {
                color: $color_silver;
                font-size: 8px;
              }
            }
            .button_out_admin {
              height: 23px;
              line-height: 23px;
              color: $color_white;
              background-color: #55d184;
              padding: 0px 9px;
              border-radius: 15px;
              font-weight: 600;
              min-width: 60px;

              i {
                font-style: initial;
                text-transform: uppercase;
              }
              &:hover {
                border-color: #3dac91;
                background-color: #3dac91;
                color: $color_white;
              }
            }
            .dropdown-menu {
              color: $color_grey_text;
              left: auto;
              right: 0px;
              background: transparent;
              border: 0px;
              border-radius: 0px;

              >ul {
                background-color: $color_white;
                box-shadow: 1px 1px 15px rgba(0, 0, 0, 0.4);
                position: relative;
                z-index: 999;
                padding: 10px 0px;
                list-style-type: none;
      
                &:after {
                  bottom: 100%;
                  right: 10%;
                  border: solid transparent;
                  content: " ";
                  height: 0;
                  width: 0;
                  position: absolute;
                  pointer-events: none;
                  border-color: rgba(136, 183, 213, 0);
                  border-bottom-color: $color_white;
                  border-width: 5px;
                  margin-right: 0px;
                }
                li {
                  line-height: 20px;
                  font-size: $font-small;
                  
                  span {
                    padding: 0px 10px;
                    display: block;
                  }
                  a {
                    display: block;
                    background-color: transparent;
                    color: $color_grey_text;
                    cursor: pointer;
                    padding: 0px 10px;
                    line-height: 20px;

                    &:hover {
                      background-color: $color_champagne;
                    }
                  }
                }
              }
            }

            &.open {

              .lang_show {
                border-color: $color_corn ;
                background-color: $color_corn;
                color: $color_white;

                .icon_dropdow {
                  color: $color_white;
                }
              }
            }
          }
          .user_navbar {
            margin-left: 10px;

            .icon-avatar {
              font-size: $font_big_23;
              float: left;
              margin-right: 6px;
            }
            .email_user_navbar {
              line-height: 23px;
              display: inline-block;
              float: left;
            }
          }
        }
      }
    }
  }

  .list_lang {
    
    .img_lang_navbar {
      height: 14px;
      width: 21px;
      margin-right: 5px;
      position: relative;
      top: -1px;
    }
  }
</style>
