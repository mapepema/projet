import Vue from 'vue'
import Vuex from 'vuex'

import User from './modules/user.js';
import Login from './modules/login.js';
import Vm from './modules/vm.js';

Vue.use(Vuex)

export default new Vuex.Store({
  state: {
  },
  mutations: {
  },
  actions: {
  },
  modules: {
    user: User,
    login: Login,
    vm: Vm
  }
})
