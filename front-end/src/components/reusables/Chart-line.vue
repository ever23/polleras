<template>
  <div class="embed-responsive embed-responsive-16by9" >
    <canvas class="embed-responsive-item" ref="cha"></canvas>
  </div>
</template>

<script>
 import  '@/assets/js/plugins/chart.js'
    export default {
        name :'chart-line',
        
        props:{
            labels:{required:true},
            datasets:{required:true},
          
        },
       data () {
            return {
              Chart:null,
              Canvas:null,
            }
        },
        created()
        {
         
        },
        updated()
        {
            
        },
        mounted()
        {
          console.log('m')
          this.ejecutar();
        },
        watch:
        {
          datasets()
          {
            if(this.Chart)
            {
            //  this.$refs.canvas.removeChild( this.canvas);
             // this.Chart.destroy();
            }
           // this.ejecutar();
          }
        },
        methods: {
            ejecutar()
            {
              /* this.canvas =document.createElement('canvas');
              this.canvas.clawss="embed-responsive-item"
               this.$refs.canvas.appendChild( this.canvas);*/
              if(this.datasets.length>0)
               this.Chart= new Chart(this.$refs.cha.getContext("2d")).Line({
                      labels: this.labels,
                      datasets: this.datasets
                    });
            }
          },
        

        
    }
</script>

<style>
    
</style>