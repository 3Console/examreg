<template>
  <div id="page-footer">
    <div class="container">
      <div class="row">
        <div class="col-md-9">
          <span class="footer-text">FOOTER HERE WITH SOCIAL MEDIA TAGS, COPYRIGHT ETC.</span>
        </div>
        <div class="col-md-3">
          <div class="language-popup" v-click-outside="onClickOutside">
            <!-- <span class="language-text">{{ $t(`common.lang_${language}`) }}</span> -->
            <button class="btn btn-language" @click.stop="onClickLanguage()">
              <div class="user-avatar">
                <i class="fa fa-globe"></i>
                <i class="fa fa-caret-down"></i>
              </div>
            </button>
            <div class="dropdown-menu list-language-head" :class="{ 'show': visibleDropdown }">
              <div class="language-item" v-for="locale in supportedLocales">
                <span @click="updateUserLocale(locale)">{{ $t(`common.lang_${locale}`) }}</span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
  import rf from 'requestfactory';
  import AuthenticationUtils from 'common/AuthenticationUtils';

  export default {
    data() {
      return {
        visibleDropdown: false,
        params: {},
        language: '',
        supportedLocales: ['en', 'vi'],
      }
    },
    methods: {
      onClickLanguage() {
        this.visibleDropdown = !this.visibleDropdown;
      },
      onClickOutside() {
        this.visibleDropdown = false;
      },
      updateUserLocale(locale) {
        this.language = locale;
        this.visibleDropdown = false;
        AuthenticationUtils.setLocale(locale);
      }
    },
    mounted() {
      this.language = AuthenticationUtils.getLocale(document.documentElement.lang);
    }
  }
</script>

<style lang="scss" scoped>
  @import "../../sass/_variables";

  #page-footer {
    padding: 70px 0px 70px 0px;
    background-color: #242729;
    // text-align: center;
    .footer-text {
      font-size: 24.7px;
      font-weight: bold;
      line-height: 1.29;
      letter-spacing: 0.5px;
      color: #607d9d;
    }

    .language-popup {
      float: right;

      .language-text {
        color: #fff;
        margin-right: 20px;
      }

      .btn-language {
        background: #d8d8d8;
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
        padding:6px 12px;

        .language-item {
          cursor: pointer;
          color: #535a60;
          &:hover {
            color: #0c0d0e;
          }
        }
      }
    }
  }
</style>
