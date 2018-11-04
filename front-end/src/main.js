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
import { getIdToken, login, logout, isLoggedIn}  from './auth.js'

router.beforeEach((to, from, next) => 
{
  //return next();
   if(!to.matched.some(record => record.meta.isPublic))
   {
       store.dispatch('user').then(data=>
        {
          if(data.data.permisos!='root' && to.matched.some(record => record.meta.root))
          {
             return next({name: '401',params:{location:to.fullPath}});
          }
          if(data.data.login)
          {
             next(); 
          }else
          {
            return next({name: 'login', query: { redirect: to.fullPath }});
          }
        }).catch(e=>
        {
           AxiosCatch(e);
           return next({name: 'login', query: { redirect: to.fullPath }});
        });

      ///return next();
   }else
   {
      return next();
   }

    
});

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
