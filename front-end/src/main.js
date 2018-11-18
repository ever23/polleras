// The Vue build version to load with the `import` command
// (runtime-only or standalone) has been set in webpack.base.conf with an alias.
import Vue from 'vue'
import App from './App'
//import BootstrapVue from 'bootstrap-vue' 
import router from './router'
import store from './store'
import Reusables from './components/reusables/index.js'
//import 'bootstrap-vue/dist/bootstrap-vue.css'

import './assets/js/main.js'
import axios from 'axios';
import auth from './auth.js'


axios.defaults.baseURL =store.getters.ApiServer;

router.beforeEach(auth);

Vue.config.productionTip = false
//Vue.use(BootstrapVue);

/* eslint-disable no-new */
const app =new Vue({
    el:'#app',
    store,
    router,
    components:{App},
    template: '<App/>'
})//.$mount('#app');
