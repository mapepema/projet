import axios from 'axios';

const state = {
  user: {},
  allusers: {},
  selecteduser: {}
}

const mutations = {
  SET_USER(state, user){
    state.user = user
  },
  SET_ALLUSER(state, allusers) {
    state.user = allusers
  },
  SET_SELECTED_USER(state, user) {
    state.selecteduser = user
  }
}

const getters = {
  allUsers: (state) => state.user,
}

const actions = {
  getUser({ commit }) {
    var $token = localStorage.getItem('token');
    const axiosOption = {
      headers: {
        'Content-Type': 'application/json',
        'Authorization': 'Bearer <' + $token + '>'
      }
    }
    return axios.get('http://localhost/api/user/get', axiosOption).then(response => {
      if (response.data.result == 1) {
        commit('SET_USER', response.data.data);
        return response;
      } else {
        throw response.data;
      } 
    });
  },
  getAllUsers({ commit }) {
    var $token = localStorage.getItem('token');
    const axiosOption = {
      headers: {
        'Content-Type': 'application/json',
        'Authorization': 'Bearer <' + $token + '>'
      }
    }
    return axios.get('http://localhost/api/user/list', axiosOption).then(response => {
      if (response.data.result == 1) {
        commit('SET_ALLUSER', response.data.data);
        return response;
      } else {
        throw response.data;
      }
    });
  },
  getSelectedUser({ commit }, id) {
    var $token = localStorage.getItem('token');
    const axiosOption = {
      headers: {
        'Content-Type': 'application/json',
        'Authorization': 'Bearer <' + $token + '>'
      }
    }
    return axios.get('http://localhost/api/user/get/'+id, axiosOption).then(response => {
      if (response.data.result == 1) {
        commit('SET_SELECTED_USER', response.data.data);
        return response;
      } else {
        throw response.data;
      }
    });
  }
}

export default {
  state, mutations, getters, actions
}