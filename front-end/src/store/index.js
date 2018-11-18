import Vue from 'vue';
import Vuex from 'vuex';

import modules from './modules'
Vue.use(Vuex);
export default new Vuex.Store({
    modules:modules,
    state:{
        ApiServer:window.appPublicPath,
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