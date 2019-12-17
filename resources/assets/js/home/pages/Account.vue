<template>
  <div id="account">
    <section class="container">
      <div class="body-contain">
        <div class="row">
          <div class="col-md-2" style="border-right: 1px solid #d8d8d8; padding: 0px; padding-top: 30px;">
            <side-bar />
          </div>
          <div class="col-md-10" style="padding-top: 30px;">
            <router-view/>
          </div>
        </div>
      </div>
    </section>
  </div>
</template>
<script>
  import SideBar from './SideBar.vue';

  export default {
    components: {
      SideBar,
    },
  }
</script>
<style lang="scss" scoped>
  @import "../../../sass/_variables.scss";
  
</style>
