<template>
    <div class="admin_page">
        <div class="wrapper">
            <navi-bar/>
            <slider :slide-menu-items="slideMenuItems"/>
            <content-wrap/>
            <modal/>
            <confirmation-modal />
            <message />
        </div>
    </div>
</template>

<script>
    import NaviBar from './NaviBar';
    import Slider from './Slider';
    import ContentWrap from './ContentWrap';
    import Modal from './components/Modal';
    import ConfirmationModal from './common/ConfirmationModal';
    import Message from './components/Message';
    import Const from './common/Const';

    export default {
      components: {
          NaviBar,
          Slider,
          ContentWrap,
          Modal,
          ConfirmationModal,
          Message
      },
      data() {
        return {
          slideMenuItems: this.$router.options.routes,//Const.MENU
        }
      }
  
    }
</script>


<style lang="scss" scoped>
  .admin_page {
    height: 100%;
  }
 .wrapper {
    background-color: #12575f
  }

    ::-webkit-scrollbar {
        width: 8px;
    }

    ::-webkit-scrollbar-track {
        background: #f1f1f1;
    }

    ::-webkit-scrollbar-thumb {
        background: #ddd;
    }

    ::-webkit-scrollbar-thumb:hover {
        background: #ccc;
    }
</style>
