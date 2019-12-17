<template>
  <!-- Content Wrapper. Contains page content -->
  <div id="content-wrap" class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        {{titlePage}}
        <small></small>
      </h1>
      <!-- <ol class="breadcrumb">
        <li><a href="/admin"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">{{titlePage}}</li>
      </ol> -->
    </section>

    <section class="content clearfix">
      <transition name="page" mode="out-in">
        <router-view v-on:EVENT_PAGE_CHANGE="onPageChange"/>
      </transition>
    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->
</template>

<script>
  export default {
    data() {
      return {
        titlePage: ''
      }
    },

    methods: {
      onPageChange(context) {
        this.titlePage = context.titlePage || "";
      }
    }
  }
</script>

<style>
  .page-enter-active, .page-leave-active {
    transition: opacity 0.5s, transform 0.5s;
  }
  .page-enter, .page-leave-to {
    opacity: 0;
  }
</style>

<style lang="scss" scoped>
  @import "../../sass/variables";
  
  .content-wrapper {
    .content-header {
      background: transparent;
      position: relative;
      padding: 22px 20px 0px 25px;

      h1 {
        margin: 0px 0px 0px 0px;
        padding: 0px 0px;
        border-bottom: 1px solid $color_alto;
        padding-bottom: 15px;
        font-size: $font_title_size_small_25;
        font-weight: 500;
        color: #12575f;
        text-transform: uppercase;
        line-height: 30px;
      }
    }
  }
</style>
