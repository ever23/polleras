
import store from '@/store'
export default (to, from, next) => 
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

    
}