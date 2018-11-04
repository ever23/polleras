import Vue from 'vue';
import Vuex from 'vuex';

import auth from './auth.js'
import settings from './settings.js'
Vue.use(Vuex);
export default new Vuex.Store({
    modules:{
        auth,settings,
    },
    state:{
        ApiServer:'/api/',
        loader:false
    },
    getters:
    {
       ApiServer(state)
        {
            return state.ApiServer;
        },
    },
    mutations:
    {
        loading(state,load)
        {
            state.loader=load;
            //console.log(load);
        }
    },
    actions:
    {
        
    }
});