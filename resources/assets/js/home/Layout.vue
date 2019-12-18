<template>
  <div id="app-layout">
    <header-page></header-page>

    <div class="cleatfix"></div>

    <main style="padding-top: 55px;">
      <router-view></router-view>
    </main>

    <footer>
      <footer-page/>
    </footer>

    <message/>
    <confirmation-modal/>
  </div>
</template>
<script>
  import HeaderPage from './HeaderPage.vue';
  import FooterPage from './FooterPage.vue';
  import Message from './shared_components/Message';
  import ConfirmationModal from './shared_components/ConfirmationModal';

  export default {
    components: {
      HeaderPage,
      FooterPage,
      Message,
      ConfirmationModal,
    }
  }
</script>