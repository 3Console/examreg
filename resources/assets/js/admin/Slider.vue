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
          :name="item.meta.routerNameDisp"
          :badge="item.meta.badge"
          :items="item.children"
          :link="item.path" />
      </ul>
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
    computed: {
      ...mapGetters([
        'currentUser'
      ])
    },
    beforeMount() {
      // RouterUtils.getRouterPermission().then(res => {
      //   this.slideMenuItems = res;
      // });
      // this.slideMenuItems = this.$router.options.routes;
    }
  }
</script>

<style lang="scss" scoped>
  @import "../../sass/variables";
  
  .main-sidebar {
    background-color: $color_bright_gray;
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
