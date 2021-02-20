import axios from 'axios';
import router from '../../router'

const state = {
  isLoggedIn: false,
  previous: ''
}

const mutations = {
  SET_LOGGED_IN(state, loggedstate) {
    state.isLoggedIn = loggedstate;
  },
  SET_PREVIOUS(state, previous) {
    state.previous = previous;
  }
}

const getters = {
}

const actions = {
  Login({ commit, state }, args) {
    return axios.get('http://localhost/api/login/gettoken?email='+args.email+'&password='+args.password).then(response => {
      if (response.data.result == 1) {
        localStorage.setItem('token', response.data.content);
        if (state.previous != '')
          router.push(state.previous);
        else 
          router.push('/client');
      } else {
        throw response.data;
      }
    });
  },
  ValideToken({ commit }) {
    var $token = localStorage.getItem('token');
    const axiosOption = {
      headers: {
        'Content-Type': 'application/json',
        'Authorization': 'Bearer <' + $token + '>'
      }
    }
    return axios.post('http://localhost/api/login/validatetoken', {}, axiosOption).then(response => {
      if (response.data.result == 1) {
        commit('SET_LOGGED_IN', true);
      } else {
        commit('SET_LOGGED_IN', false);
      }
      return response;
    });
  },
  Logout({ commit }) {
    localStorage.removeItem('token');
    commit('SET_LOGGED_IN', false);
    commit('SET_PREVIOUS', '');
    router.push('/');
  },
  SetPrevious({ commit }, previous) {
    commit('SET_PREVIOUS', previous);
  }
}

export default {
  state, mutations, getters, actions
}