/* eslint-disable */
import Vue from 'vue';
import Vuex from 'vuex';

Vue.use(Vuex);

// console.log(window);

export default new Vuex.Store({
  state: {
    viewData: window.viewData,
  },
  mutations: {
    setter(state, object) {
      Object.entries(object).forEach(([key, value]) => {
        Vue.set(state, key, value);
      });
    },
  },
  actions: {
    sendRequest({commit}, requestBody) {
      return new Promise(function (resolve) {
        $.post(window.ajaxurl, requestBody, function (res) {
          console.warn('requestBody', res);
          commit('setter', {response: res});
          resolve(res);
        });
      });
    },
    setupData({state, commit, dispatch}, element) {
      Object.keys(state).forEach(key => {
        if (element.dataset.hasOwnProperty(key)) {
          let data = {};

          try {
            data[key] = JSON.parse(element.dataset[key]);
          } catch (e) {
            data[key] = element.dataset[key];
          }

          commit('setter', data)
        }
      });
    },
  },
  getters: {
  }
});