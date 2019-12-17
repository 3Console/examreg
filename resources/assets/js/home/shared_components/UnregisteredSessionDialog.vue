<template>
  <div>
    <transition name="modal" v-if="isUnregisteredSession">
      <div class="modal-mask">
        <div class="modal-wrapper">
          <div class="modal-container">
            <p name="body">{{$t('unregistered_session.text')}}</p>
            <div class="modal-footer">
              <div name="footer" class="text-center btn-control">
                <a href="#" @click="login" class="btn btn-primary btn-sm">{{$t('common.login')}}</a>
              </div>
            </div>
          </div>
        </div>
      </div>
    </transition>
  </div>
</template>

<script>
  export default {
    data() {
      return {
        isUnregisteredSession: false,
      }
    },
    methods: {
      login() {
        window.location.href = '/login?destination=' + this.$route.path;
      }
    },
    created () {
      this.$on('UserSessionRegistered', (data) => {
        this.isUnregisteredSession = true;
      });
    },
  }
</script>

<style lang="scss" scoped>
  @import "../../../sass/common";

  .modal-mask {
    position: fixed;
    z-index: 9998;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, .5);
    display: table;
    transition: opacity .3s ease;
  }

  .modal-wrapper {
    display: table-cell;
    vertical-align: middle;
  }

  .modal-container {
    width: 350px;
    margin: 0px auto;
    padding: 20px 30px;
    background-color: $color_black_russian;
    border-radius: 2px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, .33);
    transition: all .3s ease;
    font-family: Helvetica, Arial, sans-serif;

    p {
      color: $color_white;
      padding-top: 10px;
    }
  }

  .modal-header{
    border-bottom: none !important;
  }

  .modal-footer{
    border-top: none !important;
    display: block;
  }

  .modal-header h3 {
    margin-top: 0;
    color: #42b983;
  }

  .modal-enter {
    opacity: 0;
  }

  .modal-leave-active {
    opacity: 0;
  }

  .modal-enter .modal-container,
  .modal-leave-active .modal-container {
    -webkit-transform: scale(1.1);
    transform: scale(1.1);
  }
  .btn-primary {
    color: white;
    font-size: 14px;
    @include btn-define ();
    padding: 10px 30px;
    border: none;
  }
  // .btn-primary:hover,
  // .btn-primary:active{
  //   border: none!important;
  //   background-color: #040ee2!important;
  //   color: white!important;
  // }
</style>
