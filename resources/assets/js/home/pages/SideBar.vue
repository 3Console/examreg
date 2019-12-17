<template>
  <div>
    <ul class="list-menu">
      <li v-for="(item, index) in listMenu" :key="index"
          :class="{'active': (item.activeMenu || item.openLink) }"
          class="menu-item">
        <!-- <div class="inline-link"> -->
        <router-link :to="item.link" v-if="item.link" class="main-link">
          {{ $t(item.text) }}
        </router-link>
        <!-- <a href="javascript:void(0)" v-else @click="toggle(item)" class="main-link">{{ $t(item.text) }}</a> -->
        <!-- </div> -->
      </li>
    </ul>
  </div>
</template>
<script>
  export default {
    name: 'SideBar',
    data() {
      return {
        listMenu: [
          {
            text: 'Profile',
            link: '/profile',
          },
          {
            text: 'My Schedule',
            link: '/schedule',
          },
        ],
      }
    },
    created() {
      this.initMenu(this.$route.path);
    },
    watch: {
      '$route'(to) {
        this.initMenu(to.path);
        window.scrollTo({ top: 0, behavior: 'smooth' });
      },
    },
    methods: {
      initMenu(link) {
        _.forEach(this.listMenu, (menu, key) => {
          this.$set(this.listMenu[key], 'activeMenu', false);
          this.$set(this.listMenu[key], 'openLink', false);
        });

        for (var i = 0; i < this.listMenu.length; i++) {
          if (this.listMenu[i].link === link || this.getPrefixPath(this.listMenu[i].link) === this.getPrefixPath(link)) {
            this.listMenu[i].activeMenu = true;
          }
        }
      },
      getPrefixPath (path) {
        if (!!path) {
          return path.split('/')[1];
        }
      },
    },
  }
</script>
<style lang="scss" scoped>
  @import "../../../sass/_variables";
  .list-menu {
    list-style: none;
    padding: 0px;
    position: fixed;
    width: 100%;
    max-width: 200px;

    .menu-item {
      padding: 10px 12px;

      .main-link {
        color: #535a60;
        &:hover {
          text-decoration: none;
        }
      }
    }
  }

  .active {
    background: #0c0d0e0d;
    border-right: 3px solid #0a3e69;
    font-weight: bold;
    .main-link {
      color: #0c0d0e !important;
    }
  }
</style>
