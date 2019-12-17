/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

// require('./bootstrap');

// window.Vue = require('vue');

import 'common/bootstrap';
import 'common/Filters';

import Vue from 'vue';
import VueBroadcast from 'common/VueBroadcast';
import VueRouter from 'vue-router';
import VeeValidate from 'vee-validate';
import Routers from './routes';


import VueAxios from 'vue-axios';
import axios from 'axios';

import App from './App.vue';
import AuthenticationUtils from 'common/AuthenticationUtils';
import ClickOutside from 'vue-click-outside';

import DataTable from './shared_components/datatable/DataTable';

import VueHtmlToPaper from 'vue-html-to-paper';

/**
 * The following block of code may be used to automatically register your
 * Vue components. It will recursively scan this directory for the Vue
 * components and automatically register them with their "basename".
 *
 * Eg. ./components/ExampleComponent.vue -> <example-component></example-component>
 */

// const files = require.context('./', true, /\.vue$/i);
// files.keys().map(key => Vue.component(key.split('/').pop().split('.')[0], files(key).default));

const options = {
  name: '',
  specs: [
    'fullscreen=yes',
    'titlebar=no',
    'scrollbars=no'
  ],
  styles: [
    'https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css',
    'https://unpkg.com/kidlat-css/css/kidlat.css'
  ]
}

Vue.component('data-table', DataTable);
Vue.directive('click-outside', ClickOutside);

Vue.use(VueBroadcast);
Vue.use(VueRouter);
Vue.use(VueAxios, axios);
Vue.use(VeeValidate);
Vue.use(VueHtmlToPaper, options);

const router = new VueRouter(Routers);

const i18n = window.i18n;

router.beforeEach((to, from, next) => {
  window.i18n.locale = AuthenticationUtils.getLocale(document.documentElement.lang);
  document.title = window.i18n.t('common.window_title');

  if (to.meta.auth && !window.isAuthenticated) {
    return next({ path: '/login' });
  }
  if (to.meta.guest && window.isAuthenticated) {
    return next({ path: '/' });
  }
  return next();
});

Vue.mixin({
  data () {
    return {
      tabTitle: 'Examreg',
      isSubmitting: false,
    };
  },
  methods: {
    startSubmit () {
      this.isSubmitting = true;
    },

    endSubmit () {
      this.isSubmitting = false;
    },

    getSubmitName (name) {
      return this.isSubmitting ? this.$t('common.processing') : name;
    },

    showError(message) {
      window.Message.error(message, {}, { position: "bottom_left" });
    },

    showSuccess(message) {
      window.Message.success(message, {}, { position: "bottom_left" });
    },
  }
});

window.app = new Vue({
  i18n,
  router,
  render: function(createElement) {
    return createElement(App);
  }
}).$mount('#app');
