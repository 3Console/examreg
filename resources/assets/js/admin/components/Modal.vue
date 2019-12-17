<template>
  <div>
    <transition name="modal" v-if="show">
      <div>
      <div class="modal-mask" v-if="configs.mask == true">
      </div>
        <div class="modal show" tabindex="-1" role="dialog">
          <div class="modal-dialog" v-bind:class="configs.position" role="document" v-bind:style="{ width: width + 'px'}">
            <div class="modal-content">
              <div class="modal-header" v-if="title">
                  <button class="icon-close2 " @click="hideModal()" v-if="enableClose"></button>
                <h4 class="modal-title" v-bind:style="cssTitle" v-html="title"></h4>
              </div>
              <div class="modal-body">
                <slot name="body"></slot>
              </div>
              <div class="modal-footer">
                <slot name="footer">
                  <div ref="buttons" class="list_button_modal">
                    <div v-for="button in configs.buttons">
                      <button type="button" class="btn btn-modal" v-bind:class="button.class" @click="button.callback">{{ button.label }}</button>
                    </div>
                  </div>
                </slot>
              </div>
            </div><!-- /.modal-content -->
          </div><!-- /.modal-dialog -->
        </div><!-- /.modal -->
        </div>
    </transition>
  </div>
</template>

<script>
  window.CommonModal = {
    show: function(modalName, configs){
      window.app.$broadcast('showCommonModal', modalName, configs);
    },
    hide: function(modalName){
      window.app.$broadcast('hideCommonModal', modalName);
    }
  }
  export default {
    props: {
      name: {
        default : 'defaultModal',
        type    : String
      },
      title: {
        default : '',
        type    : String
      },
      cssTitle: {
        default : () => {},
        type    : Object
      },
      enableClose: {
        default : true,
        type    : Boolean
      },
      width: {
        default : '',
        type    : String
      }
    },
    data() {
      return {
        show: false,
        configs: {
          mask: true,
          buttons: [],
          close: null,
        }
      }
    },
    methods: {
      hideModal() {
        window.app.$broadcast('hideCommonModal', this.name);
      },

      focusButton() {
        if (!this.$refs.buttons) {
          return;
        }
        let buttons = this.$refs.buttons.getElementsByTagName('button');
        let index = window._.findIndex(this.configs.buttons, (button) => { return button.focused; });
        if (index >= 0) {
          buttons[index].focus();
        }
      }
    },
    created () {
      let self = this;
      this.$on('showCommonModal', (modalName, userConfigs) => {
        if (modalName == self.name){
          self.show = true;
          self.configs = Object.assign(self.configs, userConfigs);
          if (self.configs.onShown) {
            window.setTimeout(function() {
              self.configs.onShown();
            }, 0);
          }

          this.$nextTick(() => {
            this.focusButton();
          });
        }
      });
      this.$on('hideCommonModal', (modalName) => {
        if (modalName == self.name){
          self.show = false;

          if (self.configs.close) {
            self.configs.close();
          }
        }
      });
    }
  }
</script>

<style lang="scss" scoped>
  @import "../../../sass/variables";
  .modal-mask {
    position: fixed;
    z-index: 9998;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, .3);
    display: table;
    transition: opacity .3s ease;
  }

  .modal {
    text-align     : center;
    padding        : 0!important;
    z-index        : 10000;
    pointer-events : none;

    &:before {
      content        : '';
      display        : inline-block;
      height         : 100%;
      vertical-align : middle;
      margin-right   : -4px;
    }

    .modal-dialog {
      vertical-align : middle;
      display        : inline-block;
      text-align     : left;

      &.bottom-left {
        position : absolute;
        left     : 15px;
        bottom   : 15px;
        margin   : 0;
      }

      &.bottom-right {
        position : absolute;
        right    : 15px;
        bottom   : 15px;
        margin   : 0;
      }

      .modal-content {
        border-radius: 0px;
        pointer-events: auto;

        .modal-header {
          padding: 16px 16px 16px 45px;
          position: relative;
          .icon-close2 {
            width: 24px;
            height: 24px;
            position: absolute;
            cursor: pointer;
            border: 0;
            background: none;
            padding: 0;
            float: right;
            top: 24px;
            right: 16px;
            &:after, &:before {
              background-color: #8aa2b2;
              opacity: 0.8;
              left: 11px;
              top: 0;
              height: 24px;
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
        }
        .modal-title {
          color: $color_mine_shaft;
          font-size: 20px;
          text-transform: uppercase;
        }

        .modal-body {
          padding: 15px 15px 11px 15px;
        }

        .modal-footer {
          border-top: 0px;
          padding-top: 10px;
          padding-bottom: 55px;
          padding-left: 10px;
          padding-right: 10px;
          text-align: center;
          margin-top: 0px
        }
        .list_button_modal {
          >div:last-child {
            display: inline-block;
            float: none;
            .btn-modal {
              margin: 0px 5px;
              background-color: #55d184;
              border: 1px solid #55d184;
              line-height: 20px;
              height: 35px;
              padding: 7px 7px;
              text-align: center;
              width: 100px;
              border-radius: 20px;
              font-size: $font_root;
              font-weight: 600;
              color: $color_white;
              text-align: center;
              text-transform: uppercase;
              transition: 0.5s;
              &:hover {
                background-color: #55d184;
                border-color: #55d184;
                color: $color_white;
                transition: 0.5s;
              }
            }
          }
          >div:first-child {
            display: inline-block;
            float: none;
            .btn-modal {
              margin: 0px 5px;
              background-color: $color_concrete;
              border: 1px solid $color_alto;
              line-height: 20px;
              height: 35px;
              padding: 7px 7px;
              text-align: center;
              width: 100px;
              border-radius: 20px;
              font-size: $font_root;
              font-weight: 600;
              color: $color_dove_gray;
              text-align: center;
              text-transform: uppercase;
              transition: 0.5s;
              &:hover {
                background-color: #55d184;
                border-color: #55d184;
                color: $color_white;
                transition: 0.5s;
              }
            }
          }
        }
      }
    }
  }
</style>
