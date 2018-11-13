<template>
<div>
     <router-view @mounted="mounted"></router-view>
     <loader :active="loader"></loader> 
</div>
</template>

<script>
    export default 
    {
        name: 'App',
       
        data()
        {
            return {
             transitionName:''
            }
        },
        created()
        {
          this.$store.dispatch('fetch').catch(AxiosCatch);
        },
        watch: {
          '$route' (to, from) 
          {
//this.$store.commit('loaing',true);
          }
        }, computed:
        {
          loader()
          {
            return this.$store.state.loader;
          }
        },
        methods:{
            mounted()
            {

            }
        }

    }
</script>



