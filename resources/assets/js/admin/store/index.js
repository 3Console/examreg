import Vue from 'vue'
import Vuex from 'vuex'
import * as getters from './getters'
import * as actions from './actions'
import mutations from './mutations'

Vue.use(Vuex);

const state = {
  usdTransactions: {},
  unreadMessagesCount: 0,
  unreadNotificationsCount: 0,
  remainTasksCount: 0,
  currentUser: {},
  classes: [],
  shifts: [],
  locations: [],
};

export default new Vuex.Store({
  state,
  getters,
  actions,
  mutations
})