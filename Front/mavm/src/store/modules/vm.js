import axios from 'axios';

const state = {
  myvm: {}
}

const mutations = {
  SET_VM(state, vm){
    state.myvm = vm
  }
}

const getters = {
  vm: (state) => state.vm
}

const actions = {
  getVms({ commit }) {
    var $token = localStorage.getItem('token');
    const axiosOption = {
      headers: {
        'Content-Type': 'application/json',
        'Authorization': 'Bearer <' + $token + '>'
      }
    }
    return axios.get('http://localhost/api/vms/list', axiosOption).then(response => {
      if (response.data.result == 1) {
        console.log(response);
        commit('SET_VM', response.data.data);
        return response;
      } else {
        throw response.data;
      } 
    });
  },
  createVm({commit}) {
    var $token = localStorage.getItem('token');
    const axiosOption = {
      headers: {
        'Content-Type': 'application/json',
        'Authorization': 'Bearer <' + $token + '>'
      }
    }
    let formData = new FormData();
    formData.append('size_in_gb','20');
    formData.append('instance_volume_type','l_ssd');
    formData.append('inbound_default_policy','drop');
    formData.append('outbound_default_policy','accept');
    formData.append('all_inbound_rules','[{"action" : "accept", "port" : "22", "ip" : "localhost"},{"action" : "accept", "port" : "80"},{"action" : "accept", "port" : "443"}]');
    formData.append('all_outbound_rules',"[]");
    formData.append('instance_server_type','DEV1-L');
    formData.append('instance_server_image','ubuntu-focal');
    formData.append('instance_server_tags','{"tags": ["FocalFossa", "MyUbuntuInstance" ]}');

    return axios.post('http://localhost/api/vms/create', formData, axiosOption).then(response => {
      if (response.data.result == 1) {
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