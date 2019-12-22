<template>
  <div class="header-fixed">
    <header id="header">
      <div class="container clearfix">
        <div class="row">
          <div class="col-5">
            <div class="row">
              <div class="logo">
                <a @click.stop="$router.push({ name: 'Landing' })" class="box_logo">
                  <img src="/images/eaton-logo-design.jpg" alt="">
                </a>
              </div>
              <div class="nav-item" v-if="isAuthenticated">
                <a @click.stop="$router.push({ name: 'Exam Register' })">{{ $t('header_page.exam_register') }}</a>
              </div>
            </div>
          </div>

          <div class="col-2">
            <div class="row"></div>
          </div>

          <div class="col-5">
            <div class="row" style="float: right;">
              <div class="user-header" v-click-outside="onClickOutside">
                <template v-if="isAuthenticated">
                  <button class="btn btn-profile" @click.stop="onClickProfile()">
                    <div class="user-avatar">
                      <i class="fa fa-user-circle-o"></i>
                    </div>
                  </button>
                  <div class="dropdown-menu list-profile-head" :class="{ 'show': visibleDropdown}">
                    <div class="panel-body" @click.stop="$router.push({ name: 'Profile' })">
                      <div class="profile-full-name">
                        <i class="fa fa-user"></i>
                        <span>{{ params.full_name }}</span>
                      </div>
                      <div class="profile-user-name">@ {{ params.username }}</div>
                    </div>
                    <div class="panel-bottom" @click.stop="onClickedLogout()">
                      <i class="fa fa-sign-out"></i>
                      <span class="btn-logout">{{ $t('common.logout') }}</span>
                    </div>
                  </div>
                  <div class="language-popup" v-click-outside="onClickOutside">
                    <button class="btn btn-language" @click.stop="onClickLanguage()">
                      <div class="user-avatar">
                        <i class="fa fa-globe"></i>
                        <i class="fa fa-caret-down"></i>
                      </div>
                    </button>
                    <div class="dropdown-menu list-language-head" :class="{ 'show': visibleDropdown2 }">
                      <div class="language-item"
                           v-for="locale in supportedLocales"
                           :class="{ active: $i18n.locale === locale }"
                           @click="updateUserLocale(locale)">
                        <span>{{ $t(`common.lang_${locale}`) }}</span>
                      </div>
                    </div>
                  </div>
                  <!-- <button class="btn btn-profile" @click.stop="onClickedLogout()">Logout</button> -->
                </template>
                <template v-else>
<!--                   <router-link class="btn btn-default btn-register" to="/register"
                    :class="{'active': $route.name === 'Register'}" >
                    <span>Sign Up</span>
                    <img class="icon_user" src="images/user.svg" alt="">
                  </router-link> -->
                  <!-- <button class="btn btn-register" @click.stop="$router.push({ name: 'Register' })"
                    :class="{'active': $route.name === 'Register'}">Register</button> -->
                  <!-- <div class="cross-bar"></div> -->
                  <button class="btn btn-login" @click.stop="$router.push({ name: 'Login' })"
                    :class="{'active': $route.name === 'Login'}">{{ $t('common.login') }}</button>
                </template>
              </div>
            </div>
          </div>
        </div>
      </div>
    </header>
  </div>
</template>
<script>
  import AuthenticationUtils from 'common/AuthenticationUtils';
  import Const from 'common/Const';
  import rf from 'requestfactory';
  
  export default {
    data() {
      return {
        navShow: false,
        visibleDropdown: false,
        visibleDropdown2: false,
        params: {},
        isAuthenticated: window.isAuthenticated,
        supportedLocales: ['en', 'vi'],
      }
    },
    watch: {
      '$route' (to, from) {
        this.onClickOutside();
      },
    },
    methods: {
      onClickProfile() {
        this.visibleDropdown2 = false;
        this.visibleDropdown = !this.visibleDropdown;
      },
      onClickLanguage() {
        this.visibleDropdown = false;
        this.visibleDropdown2 = !this.visibleDropdown2;
      },
      onClickOutside() {
        this.visibleDropdown = false;
        this.visibleDropdown2 = false;
      },
      onClickedLogout() {
        AuthenticationUtils.removeAuthenticationData();
        window.location.href = '/';
      },
      updateUserLocale(locale) {
        this.language = locale;
        this.visibleDropdown2 = false;
        AuthenticationUtils.setLocale(locale);
      },
      getProfile() {
        rf.getRequest('UserRequest').getProfile().then((res) => {
          this.params = res.data;
        });
      }
    },
    created() {
      this.getProfile();
    }
  }
</script>
<style lang="scss" scoped>
  @import "../../sass/_variables.scss";
  .header-fixed {
    position: fixed;
    width: 100%;
    z-index: 999;
  }

  #header {
    background-color: #fff;
    height: 55px;
    box-shadow: 0 0.25rem 0.75rem rgba(0, 0, 0, .05);
    .container {
      .box_logo {
        width: 100%;
        img {
          height: 55px;
          width: 190px;
          cursor: pointer;
        }
        &:hover {
          text-decoration: inherit;
        }
      }
      .nav-item {
        margin-left: 10px;
        padding: 12px;
        padding-top: 17px;
        color: #535a60;
        cursor: pointer;
        &:hover {
          background: #e4e6e8;
          color: #0c0d0e;
        }
      }
      .user-header {
        display: flex;
        .form-input {
          display: inline-block;
          margin-top: 18px;
        }
        .btn-login, .btn-profile, .btn-register {
          height: 55px;
          float: right;
          position:relative;
          padding: 10px 30px;
          border: 1px solid #0a3e69;
          border-radius: 0px !important;
          color: #cccccc;
          font-size: 13px;
          font-weight: bold;
          letter-spacing: 0.5px;
          background-color: #0a3e69;
          cursor: pointer;
          &:hover, active, focus {
            color: #053064;
            background: #fff;
            text-decoration: none;
          }
        }
        .btn-login {
          border-right:1px solid #c1c1c1;
        }

        .btn-profile {
          .user-avatar {
            width: 100%;

            .fa-user-circle-o {
              font-size: 35px;
            }
          }
        }

        .list-profile-head {
          position: absolute;
          right: 77px;
          left: auto;
          padding-bottom: 0px;
          width:250px;
          .panel-body {
            padding: 12px;
            cursor: pointer;
            color: #0064bd;
            &:hover {
              color: #0095ff;
            }

            .profile-user-name {
              font-size: 12px;
              color: #8e8686;
            }
          }

          .panel-bottom {
            border-top: 1px solid #d8d8d8;
            text-align: center;
            padding: 12px;
            background: #0a3e69;
            color: #fff;
            cursor: pointer;
            opacity: 0.9;
            &:hover {
              opacity: 1;
            }
          }
        }
      }
    }
  }

  .btn-language {
    background: #fff;
    margin-top: 10px;
    margin-left: 10px;
    .fa-globe {
      color: #0a3e69;
      font-size: 21px;
    }
    .fa-caret-down {
      color: #0a3e69;
      font-size: 16px;
      margin-left: 10px;
    }
  }

  .list-language-head {
    position: absolute;
    left: auto;
    right: 15px;

    .language-item {
      cursor: pointer;
      color: #535a60;
      &:hover {
        color: #0c0d0e;
      }

      span {
        margin-left: 12px;
      }
    }

    .active {
      background: #e4e6e8;
      color: #0c0d0e;
    }
  }
</style>
