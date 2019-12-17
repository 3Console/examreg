<template>
  <div id="confirmationModal" class="modal fade" tabindex="-1" role="dialog" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog " role="document">
      <div class="modal-content"  >
        <div class="modal-header">
          <span class=" symbol message-icon"></span>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true" class="icon-close2"></span></button>
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
                  <h3 class="tit_modal_confir">{{ $t('shared_components.common.confirmation_modal.info') }}</h3>
                </div>
              </div>
              <template v-if="configs.customContent">
                <p class="content_text"><slot name="content"></slot></p>
              </template>
              <template v-else>
                <p class="content_text" v-html="configs.content"></p>
              </template>
              <div class="text-center mgb_60" v-if="configs.btnCancelLabel || configs.btnConfirmLabel">
                <button type="button" data-dismiss="modal" class="btn btn-modal btn-cancel">{{ configs.btnCancelLabel }}</button>
                <button type="button" data-dismiss="modal" class="btn btn-modal btn-confirm">{{ configs.btnConfirmLabel }}</button>
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
          btnCancelLabel  : this.$t('common.action.cancel'),
          btnConfirmLabel : this.$t('common.action.confirm'),
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
  @import "../../../sass/variables";
  .mgb_60 {
    margin-bottom: 60px;
  }
  #confirmationModal {
    position   : fixed;
    text-align : center;
    padding    : 0!important;
    transition-duration: 0.3s;
    .icon_modal_confir{
      font-size: 40px;
      width: 40px;
      height: 40px;
      margin-top: 0px;
    }
    .tit_modal_confir{
      margin-bottom: 30px;
      margin-top: 10px;
      line-height: 25px;
      text-align: center;
      font-size: 20px;
      color: #333333;
    }
    .modal-dialog {
      width: 430px;
      max-width: 80%;
      max-height: 80%;
      vertical-align : middle;
      display        : inline-block;
      text-align     : left;
      margin: 13% auto 0px auto;

      .modal-content {
        border-radius: 0px;
        position:unset;
        border: 0;
        height: 100%;
          .modal-title {
            display: inline-block;
          }
          .close {
            height: 20px;
            width: 20px;
            opacity: 0.9;
            z-index: 100;
            position: relative;
            outline: none;
            background: transparent !important;
            &:hover {
              opacity: 1;
            }
            .icon-close1 {
              border-radius: 50%;
              line-height: 32px;
              overflow: hidden;
              text-align: center;
              display: inline-block;
              float: right;
              width: 30px;
              height: 30px;
              margin: 0px 0px 0 0;
              cursor: pointer;
            }
          }
        .modal-header{
          position: relative;
          padding: 15px;
          border-bottom: none;
        }
        .modal-body{
          border: 0px;
          position: unset;
          padding: 10px 25px;
          .content_text{
            margin-bottom: 10px;
            margin-top: 10px;
            line-height: 25px;
            text-align: center;
            font-size: 20px;
            color: #333333;
            strong{
              font-weight: 400;
              line-height: 20px;
            }
          }
          .primary{
            margin-top: 30px;
            margin-bottom: 20px;
            .btn-primary{
              -webkit-transition: 0.5s;
              transition: 0.5s;
              &:hover, &:active, &:focus{
                -webkit-transition: 0.5s;
                transition: 0.5s;
              }
            }
          }
          .success{
            .btn-primary{
              -webkit-transition: 0.5s;
              transition: 0.5s;
              &:hover, &:active, &:focus{
                -webkit-transition: 0.5s;
                transition: 0.5s;
              }
            }
          }
          .warning{
            .btn-primary{
              -webkit-transition: 0.5s;
              transition: 0.5s;
              &:hover, &:active, &:focus{
                -webkit-transition: 0.5s;
                transition: 0.5s;
              }
            }
          }
          .danger{
            .btn-primary{
              -webkit-transition: 0.5s;
              transition: 0.5s;
              &:hover, &:active, &:focus{
                -webkit-transition: 0.5s;
                transition: 0.5s;
              }
            }
          }
          .btn-modal {
            margin: 0px 5px;
            line-height: 20px;
            height: 35px;
            padding: 7px 7px;
            text-align: center;
            width: 100px;
            border-radius: 20px;
            font-size: $font_root;
            font-weight: 600;
            text-align: center;
            text-transform: uppercase;
            transition: 0.5s;
            &.btn-cancel {
              background-color: $color_concrete;
              border: 1px solid $color_alto;
              color: $color_dove_gray;
            }
            &.btn-confirm {
              background-color: #55d184;
              border: 1px solid #55d184;
              color: $color_white;
            }
            &:hover {
              background-color: #55d184;
              border-color: #55d184;
              color: $color_white;
              transition: 0.5s;
            }
          }
        }
      }
      .modal-footer {
        padding: 15px;
        border-top: none;
        .btn {
          border-radius : 0px;
          padding       : 7px 30px;
          color         : $color-white;
          border        : none;
          font-size     : 13px;
        }
        .btn-cancel {
          &:hover {
            opacity: 0.2;
          }
        }

      }
    }
  }
</style>
