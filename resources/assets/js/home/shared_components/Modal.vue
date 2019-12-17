<template>
  <div>
    <transition name="modal" v-if="show">
      <div>
      <div class="modal-mask" v-if="configs.mask == true">
      </div>
        <div class="modal show" tabindex="-1" role="dialog" :id="name">
          <div class="modal-dialog" v-bind:class="configs.position" role="document" v-bind:style="{ width: width + 'px'}">
            <div class="modal-content">
              <div class="modal-header" v-if="enableClose || title">
                  <button type="button" class="icon_close" @click="hideModal()" v-if="enableClose">
                    <span aria-hidden="true" class="icon-close1"></span>
                  </button>
                <h4 class="modal-title" v-if="title" v-bind:style="cssTitle" v-html="title"></h4>
              </div>
              <div class="modal-body">
                <slot name="body" :configs="configs"></slot>
              </div>
              <div class="modal-footer" v-if="hasModalFooter">
                <table>
                  <tbody>
                    <tr>
                      <td v-for="button in configs.buttons">
                        <button type="button" class="btn-cancel btn btn-default" v-bind:style="button.style" v-bind:class="button.class" @click="button.callback">{{ button.label }}</button>
                      </td>
                    </tr>
                  </tbody>
                </table>
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
      },
      hasModalFooter: {
        default : true,
        type    : Boolean
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
        this.$emit('closeModal');
      },
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
        }
      });
      this.$on('hideCommonModal', (modalName) => {
        if (modalName == self.name) {
          self.$emit('close-modal', true);
          self.show = false;

          if (self.configs.close) {
            self.configs.close();
          }
        }
      });
    }
  }
</script>

<style lang="scss"  scoped>
  @import "../../../sass/_variables.scss";

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

    transform: translate(0px, 0px, 0px);
    background-image: -webkit-linear-gradient(bottom left, rgba(43, 40, 50, 0.8) 0%, rgba(83, 86, 99, 0.8) 45%, rgba(69, 77, 91, 0.6) 60%);
    background-image: -moz-linear-gradient(bottom left, rgba(43, 40, 50, 0.8) 0%, rgba(83, 86, 99, 0.8) 45%, rgba(69, 77, 91, 0.6) 60%);
    background-image: -o-linear-gradient(bottom left, rgba(43, 40, 50, 0.8) 0%, rgba(83, 86, 99, 0.8) 45%, rgba(69, 77, 91, 0.6) 60%);
    // background-image: linear-gradient(to top right, rgba(43, 40, 50, 0.8) 0%, rgba(83, 86, 99, 0.8) 45%, rgba(69, 77, 91, 0.6) 60%);
    background-image: linear-gradient(to top right, rgba(43, 40, 50, 0.8) 100%, rgba(83, 86, 99, 0.8) 70%, rgba(0,0,0,0.55) 100%);
  }

  .modal {
    text-align     : center;
    padding        : 0 !important;
    z-index        : 10000;
    display        : inline-block !important;
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
        border-radius: 0;
        pointer-events: auto;
        border: none;
        background-color: $color_black_russian;
        color: $color_white;
        border-radius: 4px;

        .modal-header {
          padding: 40px 60px 0;
          position: relative;
          border: 0;

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
        }
        .modal-title {
          width: 100%;
          margin: 0;
          padding: 4px;
          text-align: center;
          line-height: 32px;
          font-weight: 600;
          color: $color_white;
          font-size: $font_semi_big;
          background-color: rgba(216,216,216,0.1);
          border-radius: 4px;
        }

        .modal-body {
          padding: 15px 0 40px;
          .form-control {
            opacity: 0.18px;
          }
        }

        .modal-footer {
          .btn {
            border-radius : 4px;
            padding       : 9px 30px;
            color         : white;
            border        : none;
            font-size     : 13px;
          }
          border-top: 0px;
          padding-top: 8px;
          padding-bottom: 40px;
          padding-left: 20px;
          padding-right: 20px;

          table {
            width: 100%;
          }

          td {
            padding-left: 10px;
            padding-right: 10px;
          }

          button {
            width: 100%;
          }
        }
        .btn-cancel {
          background: #808080;
          &:hover {
            background: #8d8d8d;
          }
        }
        .btn-confirm {
          background: #0070C0;
          transition: all ease-out 0.3s;

          &:hover {
            background: #005EA4;
          }
        }
      }
    }
  }
</style>
<style lang="scss">
@import "../../../sass/common.scss";
// .modal-body .modal-body {
//  padding: 5px 30px !important;
// }
.modal-body {
  padding: 30px 60px !important;
  h1, h2, h3, h4, h5, h6, p, span:not(.required) {
    color: $color_white;
  }
  .label, .content_text {
    color: $color_white !important;
  }
  .row-item {
    margin-bottom: 20px;
  }
  .modal-title {
    padding-bottom: 5px;
    strong {
      opacity: 0.8;
    }
  }
  input {
    @include input-gamelancer('input');
  }
  select {
    @include input-gamelancer('select');
  }
  textarea {
    @include input-gamelancer('textarea');
  }
    /* Base for label styling */
  [type="checkbox"]:not(:checked),
  [type="checkbox"]:checked {
    position: absolute;
    left: -9999px;
  }
  [type="checkbox"]:not(:checked) + label,
  [type="checkbox"]:checked + label {
    position: relative;
    padding-left: 1.95em;
    cursor: pointer;
  }

  /* checkbox aspect */
  [type="checkbox"]:not(:checked) + label:before,
  [type="checkbox"]:checked + label:before {
    content: '';
    position: absolute;
    left: 0; top: 0;
    width: 1.25em; height: 1.25em;
    border: 2px solid #ccc;
    background: #fff;
    border-radius: 4px;
    box-shadow: inset 0 1px 3px rgba(0,0,0,.1);
  }
  /* checked mark aspect */
  [type="checkbox"]:not(:checked) + label:after,
  [type="checkbox"]:checked + label:after {
    content: '\2713\0020';
    position: absolute;
    top: .15em; left: .13em;
    font-size: 1.3em;
    line-height: 0.8;
    color: #09ad7e;
    transition: all .2s;
    font-family: 'Lucida Sans Unicode', 'Arial Unicode MS', Arial;
  }
  /* checked mark aspect changes */
  [type="checkbox"]:not(:checked) + label:after {
    opacity: 0;
    transform: scale(0);
  }
  [type="checkbox"]:checked + label:after {
    opacity: 1;
    transform: scale(1);
  }
  /* disabled checkbox */
  [type="checkbox"]:disabled:not(:checked) + label:before,
  [type="checkbox"]:disabled:checked + label:before {
    box-shadow: none;
    border-color: #bbb;
    background-color: #ddd;
  }
  [type="checkbox"]:disabled:checked + label:after {
    color: #999;
  }
  [type="checkbox"]:disabled + label {
    color: #aaa;
  }
  /* accessibility */
  [type="checkbox"]:checked:focus + label:before,
  [type="checkbox"]:not(:checked):focus + label:before {
    border: 2px dotted blue;
  }

  /* hover style just for information */
  label:hover:before {
    border: 2px solid #4778d9!important;
  }

}
</style>
