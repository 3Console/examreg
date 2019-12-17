<template>
  <router-link tag="li" class="item_navbar_right"
      :class="{ 'treeview': isGroupRouter, 'active': !isGroupRouter && $route.path.includes(link),
        'menu-open': $route.path.includes(link) }"
      :to="onClickItem()">
    <a href="#">
      <i class="icon_item" :class="icon"></i> <span class="name_item">{{ name }}</span>
      <i class="fa fa-angle-down pull-right" v-if="isGroupRouter"></i>
      <span class="pull-right-container">
        <small class="label pull-right" :class="[badge.type==='String' ? 'bg-green': 'label-primary']" v-if="badge && badge.data">
          {{ badge.data }}
        </small>
      </span>
    </a>
    <ul class="treeview-menu" :class="{ 'd-block': $router.currentRoute.fullPath.includes(link) }" v-if="isGroupRouter">
      <router-link tag="li" v-for="(item, index) in items"
              :class="{active: $route.name === item.name}"
              :key="index"
              :to="{ path: item.fullPath }" v-if="item">
        <a><i :class="item.meta.icon"></i> {{ item.meta.routerNameDisp }}</a>
      </router-link>
      <li v-else>
        <a><i :class="item.meta.icon"></i> {{ item.meta.routerNameDisp }}</a>
      </li>
    </ul>
  </router-link>
</template>

<script>
  export default {
    props: {
      type: {
        type: String,
        default: 'item'
      },
      icon: {
        type: String,
        default: ''
      },
      name: {
        type: String
      },
      badge: {
        type: Object,
        default () {
          return {}
        }
      },
      items: {
        type: Array,
        default () {
          return []
        }
      },
      router: {
        type: Object,
        default () {
          return {
            name: '',
            subRoutes:''
          }
        }
      },
      link: {
        type: String
      }
    },
    computed: {
      isGroupRouter() {
        return this.type !== 'item';
      },
    },
    methods: {
      onClickItem() {
        if (this.isGroupRouter) {
          return '';
        }
        return this.link;
      }
    }
  }
</script>

<style lang="scss" scoped>
  @import "../../../sass/variables";

  .item_navbar_right {
    position: relative;
    margin: 0;
    padding: 0;
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    // &.treeview {
    //   .treeview-menu {
    //     display: none !important;
    //   }
    //   &.menu-open {
    //     .treeview-menu {
    //       display: block !important;
    //     }
    //   }
    // }
    >a {
      position: relative;
      overflow: hidden;
      display: block;
      color: $color_white;
      font-size: $font_root;
      line-height: 20px;
      padding: 15px 20px;
      border-left: 0px solid transparent !important;

      .icon_item {
        width: 20px;
        font-size: $font_big_19;
        line-height: 20px;
        float: left;
        color: $color_silver;
        margin-right: 10px;
      }
      .name_item {
        color: $color_white;
        display: inline-block;
        overflow: hidden;
        max-width: calc(100% - 50px);
        text-overflow: ellipsis;
        float: left;
      }
      .pull-right-container {
        position: absolute;
        right: 10px;
        top: 50%;
        margin-top: -7px;

        .fa-angle-down {
          width: auto;
          height: auto;
          padding: 0;
          margin-right: 10px;
          transition: transform 0.5s ease, -webkit-transform 0.5s ease;
          transform: rotate(0deg);
          color: $color_silver;
        }
      }
    }
    .treeview-menu {
      background-color: $color_river_bed !important;
      display: none;
      list-style: none;
      padding: 0 0px 20px 0px;
      margin: 0;
      padding-left: 0px;
      margin: 0 0px;

      li {

        a {

          font-size: $font-small;
          color: $color_silver;
          line-height: 20px;
          padding: 2px 20px 2px 55px;

          &.active {
            color: #55d184;
          }
          &:hover {
            color: #55d184;
          }
        }
        &.active {
          a {
            color: #55d184;
          }
        }
      }
    }
    &:hover {

      >a {
        color: $color_white !important;
        background-color: $color_river_bed !important;
        border-left: 0px solid transparent;
      }
    }
    &.active {

      >a {
        color: $color_white !important;
        background-color: $color_river_bed !important;
        border-left: 0px solid transparent;
        i, span {
          color: #55d184;
        }
      }
    }
    &.menu-open {

      >a {
        color: $color_white !important;
        background-color: $color_river_bed !important;

        .pull-right-container {

          .fa-angle-down {
            transition: transform 0.5s ease, -webkit-transform 0.5s ease;
            transform: rotate(180deg);
            color: #55d184;
          }
        }
      }
    }
    &.active.menu-open {

      .treeview-menu {
        display: block;
      }
    }
  }

  .d-block {
    display: block !important;
  }
</style>

<style lang="scss">
  @import "../../../sass/variables";

  .sidebar-mini:not(.sidebar-mini-expand-feature).sidebar-collapse {
    .sidebar-menu{
      .item_navbar_right {

        >a {
          padding: 15px 15px;
          height: 50px;
        }
        .treeview-menu {
          padding: 10px 0px 20px 0px;
          top: 0px;

          a {
            padding: 5px 20px 5px 20px;
          }
        }
        &:hover {
          .treeview-menu {
            top: 0px;
          }
        }
      }
    }
  }

</style>
