import Vue from 'vue'
//import './store/mixin';
import store from './store/store';
//import router from './store/router';


//Templates
import app from './App';

window.addEventListener('DOMContentLoaded',() => {
  const main = document.querySelector('#app');
  if (main) {
    new Vue({
      el: main,
      render: h => h(app),
      store,
      // router,
    });
  }
});


export default () => {
  return {store}
};