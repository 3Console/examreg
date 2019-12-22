<template>
  <aside id="slider" class="main-sidebar">
    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">

      <div class="title_setting_sidebar">
        
      </div>
      <!-- sidebar menu: : style can be found in sidebar.less -->
      <ul data-widget="tree" class="sidebar-menu">
        <slide-item
          v-for="(item, index) in slideMenuItems" v-if="item.meta.hasPermission"
          :data="item"
          :key="index"
          :type="item.meta.type"
          :icon="item.meta.icon"
          :name="$t(`router.name_${item.meta.routerNameDisp}`)"
          :badge="item.meta.badge"
          :items="item.children"
          :link="item.path" />
      </ul>
      <div class="language-popup" v-click-outside="onClickOutside">
        <button class="btn btn-language" @click.stop="onClickLanguage()">
          <div class="user-avatar">
            <i class="fa fa-globe"></i>
            <i class="fa fa-caret-down"></i>
          </div>
        </button>
        <span class="language-text">{{ $t(`common.lang_${language}`) }}</span>
      </div>
      <div class="dropdown-menu list-language-head" :class="{ 'show': visibleDropdown }">
        <div class="language-item"
             v-for="locale in supportedLocales"
             :class="{ active: $i18n.locale === locale }"
             @click="updateUserLocale(locale)">
          <span>{{ $t(`common.lang_${locale}`) }}</span>
        </div>
      </div>
    </section>
    <!-- /.sidebar -->
  </aside>
</template>

<script>
  import { mapGetters } from 'vuex';
  import SlideItem from './components/SlideItem';
  import rf from './lib/RequestFactory';
  import Const from './common/Const';
  import RouterUtils from './common/RouterUtils';
  import AuthenticationUtils from './common/AuthenticationUtils';

  export default {
    components: {
      SlideItem
    },
    props: {
      slideMenuItems: {
        type: Array,
        default: []
      }
    },
    data() {
      return {
        language: '',
        visibleDropdown: false,
        supportedLocales: ['en', 'vi'],
      }
    },
    computed: {
      ...mapGetters([
        'currentUser'
      ])
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
    // beforeMount() {
    //   // RouterUtils.getRouterPermission().then(res => {
    //   //   this.slideMenuItems = res;
    //   // });
    //   // this.slideMenuItems = this.$router.options.routes;
    // }
  }
</script>

<style lang="scss" scoped>
  @import "../../sass/variables";
  
  .main-sidebar {
    background-color: $color_bright_gray;
  }

  .language-popup {
    margin-top: 20px;

    .btn-language {
      background: #d8d8d8;
      margin-left: 5px;
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

    .language-text {
      margin-left: 20px;
      color: #55d184;
    }
  }

  .list-language-head {
    position: relative;
    left: 5px;
    // right: 15px;
    padding:6px 12px;

    .language-item {
      cursor: pointer;
      color: #535a60;
      &:hover {
        color: #0c0d0e;
      }
    }
  }
</style>

<style lang="scss">
  @import "../../sass/variables";
  #slider {
    .sidebar-menu {
      .RouteAlone {

        .pull-right-container {
          display: none;
        }
        &.activeRouteAlone {

          .name_item {
            color: $color_corn_pale;
          }
        }
      }
    }
  }
</style>
