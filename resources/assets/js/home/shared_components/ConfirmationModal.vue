<template>
  <div id="confirmationModal" class="modal fade" tabindex="-1" role="dialog" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog " role="document">
      <div class="modal-content"  >
        <div class="modal-header">
          <span class=" symbol message-icon"></span>
          <button type="button" class="icon_close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true" class="icon-close1"></span></button>
          <!-- <h4 class="modal-title">{{configs.title}}</h4> -->
        </div>
        <div class="modal-body">
          <div class="t-center">
            <div >
              <div v-if="configs.type === 'primary'" class="fo_light_blue">
                <span class="icon-info2 icon_modal_confir"></span>
                <h3 class="tit_modal_confir" v-html="configs.title"></h3>
              </div>
              <div v-else-if="configs.type === 'success'" class="fo_green">
                <span class="icon-checked icon_modal_confir"></span>
                <h3 class="tit_modal_confir" v-html="configs.title"></h3>
              </div>
              <div v-else-if="configs.type === 'warning'" class="fo_yellow">
                <span class="icon-info2 icon_modal_confir"></span>
                <h3 class="tit_modal_confir" v-html="configs.title"></h3>
              </div>
              <div v-else-if="configs.type === 'danger'" class="fo_red">
                <img src="/images/icon/cancel-gi.svg" width="40px" height="40px"/>
                <h3 class="tit_modal_confir" v-html="configs.title"></h3>
              </div>
              <div v-else-if="configs.type === 'confirm'">
                <h3 class="tit_modal_confir" v-html="configs.title"></h3>
              </div>
              <div v-else class="fo_light_blue">
                <span class="icon-info2 icon_modal_confir"></span>
                <h3 class="tit_modal_confir"> asdasd </h3>
              </div>
            </div>
            <template v-if="configs.customContent">
              <p class="content_text"><slot name="content"></slot></p>
            </template>
            <template v-else>
              <p class="content_text" v-html="configs.content"></p>
            </template>
            <div class="mart50 text-center" v-if="configs.btnCancelLabel || configs.btnConfirmLabel">
              <button type="button" data-dismiss="modal" class="btn btn-cancel btn-space btn-secondary">
                {{ configs.btnCancelLabel || 'Cancel' }}</button>
              <button type="button" data-dismiss="modal" class="btn btn-confirm btn-space btn-primary">
                {{ configs.btnConfirmLabel || 'Confirm' }}</button>
            </div>
          </div>
        </div>
      </div>
    </div><!-- /.modal-dialog -->
  </div><!-- /.modal -->
</template>

<script>
  window.ConfirmationModal = {
    show: function(configs){
      window.app.$broadcast('showModal', configs);
    }
  }
  export default {
    data(){
      return{
        configs: {
          type            : '',
          title           : '',
          content         : '',
          btnCancelLabel  : 'cancel',
          btnConfirmLabel : 'confirm',
          onConfirm       : () => {},
          onCancel        : () => {},
          size            : 'small'
        },
      }
    },
    methods: {
      show () {
        const modal = $('#confirmationModal');
        modal.find('.close').unbind('click').click(this.configs.onCancel);
        modal.find('.btn-cancel').unbind('click').click(this.configs.onCancel);
        modal.find('.btn-confirm').unbind('click').click(this.configs.onConfirm);
        modal.modal('show');
      },
      hide () {
        $('#confirmationModal').modal('hide');
      }
    },
    created () {
      let self = this;

      this.$on('showModal', (userConfigs) => {
        self.configs = Object.assign(self.configs, userConfigs);
        this.show();
      });
    }
  };
</script>
<style lang="scss" scoped>
  @import "../../../sass/_variables.scss";

  #confirmationModal {
    position   : fixed;
    text-align : center;
    padding    : 0!important;
    transition-duration: 0.3s;
    .icon_modal_confir{
      font-size: 40px;
      width: 40px;
      height: 40px;
      color: $color-blue-cerulean-lighter;
      margin-top: 0px;
    }
    .tit_modal_confir{
      // color: $color-blue-cerulean-lighter;
      font-size: $font_title_size_small_25;
      line-height: 29px;
      margin-bottom: 15px;
      margin-top: 25px;
      text-align: center;
    }
    .modal-dialog {
      width: 440px;
      max-width: 80%;
      max-height: 80%;
      vertical-align : middle;
      display        : inline-block;
      text-align     : left;
      margin: 13% auto 0px auto;

      .modal-content {
        background-color: $color_black_russian;
        border-radius: 0px;
        position:unset;
        border: 0;
        height: 100%;
          .modal-title {
            color: $color_dark_gray;
            display: inline-block;
            font-size: $font_big_20;
          }
          .icon_close {
            width: 14px;
            height: 14px;
            position: absolute;
            cursor: pointer;
            border: 0;
            background: none;
            padding: 0;
            float: right;
            top: 14px;
            right: 14px;
            &:after, &:before {
              background-color: #8aa2b2;
              opacity: 0.8;
              left: 5px;
              top: 0px;
              height: 15px;
              content: "";
              position: absolute;
              width: 2px;
            }
            &:before {
              -webkit-transform: rotate(45deg);
              transform: rotate(45deg);
            }
            &:after {
              -webkit-transform: rotate(-45deg);
              transform: rotate(-45deg);
            }
            &:hover {
              &:after, &:before {
                opacity: 1;
              }
            }
          }
        .modal-header{
          position: relative;
          padding: 15px;
          border-bottom: none;
        }
        .modal-body{
          padding: 0 75px 30px !important;
          border: 0px;
          color: rgb(51,51,51);
          position: unset;
          padding: 10px 25px;
          font-size: $font_small;
          .content_text{
            font-size: $font_medium-bigger;
            line-height: 20px;
            margin: 4px auto 13px auto;
            display: block;
            max-width: 298px;
            min-height: 60px;
            font-size: $font_small;
            color: $color_green_vogue;//$color_dark_gray;
            text-align: center;
            strong{
              font-size: $font_medium-bigger;
              color: $color-grey;
              font-weight: 400;
              line-height: 24px;
            }
          }
          .primary{
            margin-top: 30px;
            margin-bottom: 20px;
            .btn-primary{
              background-color: $color-blue-cerulean-lighter;
              border-color: $color-blue-cerulean-lighter;
              color: $color_white;
              -webkit-transition: 0.5s;
              transition: 0.5s;
              &:hover, &:active, &:focus{
                background: $color-blue-cerulean-lighter;
                border-color: $color-blue-cerulean-lighter;
                color: $color-white;
                -webkit-transition: 0.5s;
                transition: 0.5s;
              }
            }
          }
          .success{
            .btn-primary{
              background-color: $color-grey;
              border-color: $color-grey;
              color: $color_white;
              -webkit-transition: 0.5s;
              transition: 0.5s;
              &:hover, &:active, &:focus{
                background: $color-blue-cerulean-lighter;
                border-color: $color-blue-cerulean-lighter;
                color: $color-white;
                -webkit-transition: 0.5s;
                transition: 0.5s;
              }
            }
          }
          .warning{
            .btn-primary{
              background-color: $color_caribbean_green;
              border-color: $color_caribbean_green;
              color: $color_white;
              -webkit-transition: 0.5s;
              transition: 0.5s;
              &:hover, &:active, &:focus{
                background: $color-blue-cerulean-lighter;
                border-color: $color-blue-cerulean-lighter;
                color: $color-white;
                -webkit-transition: 0.5s;
                transition: 0.5s;
              }
            }
          }
          .danger{
            .btn-primary{
              background-color: $red;
              border-color: $red;
              color: $color_white;
              -webkit-transition: 0.5s;
              transition: 0.5s;
              &:hover, &:active, &:focus{
                background: $color-blue-cerulean-lighter;
                border-color: $color-blue-cerulean-lighter;
                color: $color-white;
                -webkit-transition: 0.5s;
                transition: 0.5s;
              }
            }
          }
          button{
            box-shadow: 0 1px 0 rgba(0,0,0,.05);
            // border: 1px solid $color_slate_blue;
            padding: 10px 10px;
            font-size: $font_root;
            line-height: 20px;
            height: 40px;
            border-radius: 25px;
            font-weight: 500;
            min-width: 130px;
            margin: 5px;
            font-weight: 700;
            text-transform: uppercase;
            background-color: $color-blue-cerulean-lighter;
            color: $color_white;
            -webkit-transition: 0.5s;
            transition: 0.5s;
            &:hover, &:active, &:focus{
              background: $color-blue-cerulean-lighter;//$color_slate_blue;
              // border-color: $color-blue-cerulean-lighter;
              color: $color-white;
              -webkit-transition: 0.5s;
              transition: 0.5s;
            }
            &.btn-secondary {
              background: transparent;
              color: $color-blue-cerulean-lighter;
              -webkit-transition: 0.5s;
              transition: 0.5s;
              border: 1px solid $color-blue-cerulean-lighter;
              &:hover, &:active, &:focus{
                background: $color-blue-cerulean-lighter;//$color_slate_blue;
                // border-color: $color-blue-cerulean-lighter;
                color: $color-white;
                -webkit-transition: 0.5s;
                transition: 0.5s;
              }
            }
          }
        }
      }
      .modal-footer {
        padding: 15px;
        background-color: $color-white;
        border-top: none;
        .btn {
          border-radius : 0px;
          padding       : 7px 30px;
          color         : $color-white;
          border        : none;
          font-size     : 13px;
        }
        .btn-cancel {
          background: $yellow;
          &:hover {
            opacity: 0.2;
          }
        }

      }
      .btn-confirm {
        float: none;
        background-color: #010788 !important;
        &:hover, &:focus, &:active {
          background-color: #0a11a7 !important;
        }
      }
      .btn-cancel {
        background: $color_black_russian !important;
        color: $color_white !important;
        border: 1px solid $color_white !important;
        &:hover, &:focus, &:active {
          background-color: $color_white!important;
          color: #6369de !important;
          border: 1px solid #0a11a7 !important;
        }
      }
    }
  }
</style>
