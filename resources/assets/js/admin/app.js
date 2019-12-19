/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */
import './common/bootstrap';
import Vue from 'vue';
import VueBroadcast from 'common/VueBroadcast';
import router from './routes';
import VeeValidate from 'vee-validate';
import App from './App.vue';
import DataTable from './components/DataTable';
import './common/Filters.js';
import ClickOutside from 'vue-click-outside';
import store from './store/index.js';
import CKEditor from '@ckeditor/ckeditor5-vue';
import RouterUtils from './common/RouterUtils';
import AuthenticationUtils from './common/AuthenticationUtils';
import VTooltip from 'v-tooltip';
import DatePicker from 'vue2-datepicker';
import VueHtmlToPaper from 'vue-html-to-paper';

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


Vue.use( CKEditor );

Vue.use(VueBroadcast);
Vue.use(VeeValidate);
Vue.use(VTooltip);
Vue.use(VueHtmlToPaper, options);


Vue.component('data-table', DataTable);
Vue.component('date-picker', DatePicker);
Vue.directive('click-outside', ClickOutside);

const i18n = window.i18n;
router.beforeEach((to, from, next) => {
  window.i18n.locale = AuthenticationUtils.getLocale(document.documentElement.lang);

  // if (to.path === 'login') {
  //   return next();
  // }

  // RouterUtils.validRouter(to).then(availableRoutePath => {
  //   if (to.name === RouterUtils.ROUTER_NOT_FOUND_NAME) {
  //     return next();
  //   }
  //   if (availableRoutePath) {
  //     // Sub Route must be has prefix router of parent.
  //     if (!to.path.includes(availableRoutePath)) {
  //       return next({path: availableRoutePath});
  //     }
  //   } else {
  //     return next({ name: RouterUtils.ROUTER_NOT_FOUND_NAME });
  //   }

    return next();
  // });
});

Vue.mixin({
  data () {
    return {
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
  store,
  render: h => h(App)
}).$mount('#app');
