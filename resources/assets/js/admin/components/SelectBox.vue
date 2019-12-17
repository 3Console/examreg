<template>
  <div class="sc_search_select" v-click-outside="turnOffSelect">
    <div class="group_search_select">
      <div class="button_drop_search" :class="[{active: isShow}]" @click="toggleSelect()">
        <slot name="label_selected" v-if="customLabel" :selected="value"></slot>
        <span v-else v-html="label"/>
        <i class="icon-arrow1"></i>
      </div>
      <div class="box_list_search_select active" v-show="isShow">
        <ul class="list_search_select">
          <li v-for="option in options" @click="select(option)">
            <slot v-if="customLabel" name="option" :option="option"></slot>
            <span v-else class="full_name_coin_select" v-html="getSelectedValue(option)"/>
          </li>
        </ul>
      </div>
    </div>
  </div>
</template>

<script>
  export default {
    props: {
      placeholder: {
        type: String,
      },
      customLabel: {
        type: Boolean,
        default: false,
      },
      options: {
        type: Array,
        default: () => [],
      },
      value: { default: '' },
    },
    data () {
      return {
        isShow: false,
      };
    },
    computed: {
      label () {
        const selected = this.getSelectedValue(this.value);
        return selected || this.placeholder;
      },
    },
    watch: {
    },
    methods: {
      toggleSelect () {
        this.isShow = !this.isShow;
      },
      turnOffSelect () {
        this.isShow = false;
      },
      select (option) {
        this.$emit('input', option);
        this.turnOffSelect();
      },
      getSelectedValue(option) {
        const selected = this.options.find(item => {
          if (window._.isObject(option)) {
            return item.id === option.id
          }
          return item === option;
        });
        return window._.isObject(option) ? selected.name : selected;
      },
      includes (str, query) {
        /* istanbul ignore else */
        if (str === undefined) str = 'undefined';
        if (str === null) str = 'null';
        if (str === false) str = 'false';
        const text = str.toString().toLowerCase();
        const value = query.trim().toString().toLowerCase();
        return text.includes(value);
      },
    }
  };
</script>

<style lang="scss" scoped>
  @import "../../../sass/_variables";
  .pr-5 {
    padding-right: 5px;
  }
  ::-webkit-scrollbar {
        width: 5px;
    }
  .icon-class {
    .sc_search_select {
      .group_search_select {
        .box_list_search_select {
          min-width: 150px;
          left: 50%;
          transform: translateX(-50%);
          .list_search_select {
            max-height: 150px;
            overflow-y: auto;
          }
        }
      }
    }
  }
  .icon-type {
    .sc_search_select {
      .group_search_select {
        .box_list_search_select {
          .list_search_select {
            max-height: 150px;
            overflow-y: auto;
          }
        }
      }
    }
  }
  .sc_search_select{
    .tit_search_select{
      font-size: $font-title-size-big;
      color: $color-white;
      margin-bottom: 20px;
      line-height: 44px;
    }
    .group_search_select{
      position: relative;
      .button_drop_search {
        cursor: pointer;
        height: 27px;
        overflow: hidden;
        line-height: 18px;
        padding: 3px 5px;
        border: 1px solid $color-alto;
        // &.active{
        //   background: -moz-linear-gradient(180deg, $background-default 60%, #CCCFD1 100%);/* FF3.6+ */
        //   background: -webkit-gradient(linear, 180deg, color-stop(60%, FFFFFF), color-stop(100%, CCCFD1));/* Chrome,Safari4+ */
        //   background: -webkit-linear-gradient(180deg, $background-default 60%, #CCCFD1 100%);/* Chrome10+,Safari5.1+ */
        //   background: -o-linear-gradient(180deg, $background-default 60%, #CCCFD1 100%);/* Opera 11.10+ */
        //   background: -ms-linear-gradient(180deg, $background-default 60%, #CCCFD1 100%);/* IE10+ */
        //   filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#1301FE', endColorstr='#F4F60C', GradientType='1'); /* for IE */
        //   background: linear-gradient(180deg, $background-default 60%, #CCCFD1 100%);/* W3C */
        // }
        span{
          display: inline-block;
          float: left;
          color: $color-grey-dusty;
          font-size: $font-smaller;
          line-height: 18px;
        }
        .icon-arrow1 {
          padding: 4px 0px;
          font-size: 11px;
          float: right;
        }
        &.active{
          display: block;
          visibility: visible;
          opacity: 1;
        }
      }
      .box_list_search_select {
        padding-top: 5px;
        position: absolute;
        display: none;
        width: 100%;
        top: 100%;
        left: 0px;
        z-index: 111;
        float: left;
        visibility: hidden;
        opacity: 0;
        max-height: 470px;
        ul {
          margin: 0;
        }
        &.active {
          display: block;
          visibility: visible;
          opacity: 1;
        }
      }
      .list_search_select{
        background: $background-default;
        box-shadow: 0px 3px 12px rgba(49, 49, 49, 0.51);
        list-style-type: none;
        max-height: 200px;
        position: relative;
        padding: 12px 0px;
        &:after{
          bottom: 100%;
          left: 50%;
          border: solid transparent;
          content: " ";
          display: block;
          height: 0;
          width: 0;
          position: absolute;
          pointer-events: none;
          border-color: rgba(136, 183, 213, 0);
          border-bottom-color: $color-white;
          border-width: 5px;
          margin-left: -5px;
        }
        li{
          display: block;
          width: 100%;
          line-height: 20px;
          cursor: pointer;
          padding: 3px 14px;
          font-size: $font-root;
          overflow: hidden;
          &:hover{
            background: $color-yellow-light;
          }
        }
      }
    }
  }
</style>
