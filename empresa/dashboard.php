<?php
session_start();
include "session.php";
require_once '../app/controller/CidadeEstado.php';
$estado = new CidadeEstado();

ob_start();  
?>
<style>
    
body {
  background-color: #eff4f7;
  color: #777;
  font-family: 'Titillium Web', Arial, Helvetica, sans-serif
}

h1, h2, h3, h4, h5, h6, strong {
  font-weight: 600;
}

.content-area {
  max-width: 1070px;
  margin: 0 auto;
}

#topnav {
  background: #37474f;
  height: 60px;
  display: flex;
  flex-direction: row;
  align-items: center;
  font-size: 14px;
}

.admin-menu {
  color: #fff;
  font-size: 16px;
  display: flex;
  justify-content: center;
  align-items: center;
  padding: 15px;
  flex: 0.05 0 0;
}

.logo {
  display: flex;
  flex-direction: row;
  align-items: center;
  flex: 1 0 0;
}

.logo-t {
  width: 32px;
  height: 32px;
  border: 2px solid #26c6da;
  text-align: center;
  line-height: 28px;
  border-radius: 50%;
  margin-right: 15px;
  margin-left: 5px;
  padding-left: 3px;
}

.search-bar {
  flex: 2 0 0;
  align-items: center;
  justify-content: space-between;
  background: #232e34;
  overflow: hidden;
  display: flex;
  height: 36px;
  border-radius: 35px;
  color: rgba(255,255,255,0.5);
}

.search-bar-dropdown {
  flex: 1 0 0;
  height: 40px;
  line-height: 40px;
  padding: 0 18px;
  margin-right: 15px;
  background: #2c393f;

}
.search-bar-input {
  flex: 2 0 0;
  display: flex;
  justify-content: flex-end;
  padding: 0 18px;
  line-height: 40px;
  align-items: center;
}

.search-bar-input input[type="text"] {
  width: 100%;
  background: transparent;
  border: 0;
  color: rgba(255,255,255,0.5);
}
.search-bar-input input:focus{
  outline: none;
}

.box.banana_map {
  color: #fff;
  background: #eff4f7;
  padding: 0;
  box-shadow: none;
}
.box.banana_map .title {
  padding-top: 40px;
  padding-left: 25px;
  font-size: 16px;
}
.box.banana_map .subtitle {
  font-weight: 700;
  padding-top: 10px;
  padding-left: 25px;
  font-size: 22px;
}

.box {
  max-height: 444px;
}

.box .banana {
  min-height: 404px;
  background-image: url('img/banana.png');
  background-size: cover;
}
.box .map {
  min-height: 404px;
  background-image: url('img/map.png');
  background-size: cover;
}
.box .cog-icon {
  cursor: pointer;
  position: absolute;
  right: 55px;
  top: 25px;
  z-index: 10;
}

@media screen and (max-width:760px) {
  #topnav { flex-wrap: wrap; }
  .admin-menu { flex-basis: 20%; }
  .logo { justify-content: flex-end; padding-right: 10px; }
  .logo { flex-basis: 80%; }
  .topnav-rightmenu, .search-bar { display: none; }
}

.box {
  box-shadow: 0px 1px 22px -12px #607D8B;
  background-color: #fff;
  padding: 25px 35px 25px 30px;
}

#monthly-earnings-chart #apexcharts-canvas {
  position: relative;
}
#monthly-earnings-chart #apexcharts-canvas:after {
  content: "";
  position: absolute;
  left: 0;
  right: 58%;
  top: 0;
  bottom: 0;
  background: #24bdd3;
  opacity: 0.65;
}
#monthly-earnings-chart #apexcharts-title-text {
  font-weight: 600;
}
#monthly-earnings-chart #apexcharts-subtitle-text {
  font-weight: 700;
}
.monthly-earnings-text {
  position: absolute;
  left: 70px;
  top: 187px;
  color: #fff;
  z-index: 10;
}

.monthly-earnings-text h6 {
  font-size: 16px;
}
.chart-title h5 {
  font-size: 18px;
  color: rgba(51,51,51,1);
  margin-bottom: 38px;
}


@media screen and (max-width:760px) {
  .monthly-earnings-text {
    left: 30px;
  }
  .box {
    padding: 25px 0;
  }
}

.sparkboxes .box {
  padding: 3px 0 0 0;
  position: relative;
}

#spark1, #spark2, #spark3, #spark4 {
  position: relative;
  padding-top: 15px;
}


/* overrides */
.sparkboxes #apexcharts-subtitle-text { fill: #8799a2 !important; }


.spinner-border {
  display: none;
}
</style>
<script src="../app/public/js/apexcharts.min.js"></script>
<div id="wrapper">
      <div class="content-area">
          <div class="main">



            <div class="row mt-5 mb-4">
              <div class="col-md-6">
                <div class="box">
                  <div id="chart">

                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
    </div>

<script>
    $.ajax({
        type: "POST",
        url: "../app/requests/graph.php",
        data: {
            acao: 'dashboardEmpresa'
        },
        dataType: "JSON",
        success: function (response) {
          dashboardAluno(response);
        }
    });



    function dashboardAluno(data){
        let funcionario = '';
        let estagiarios = '';

        data.forEach(function(item) {
          funcionario = (item.qtdeFunc);
          estagiarios = (item.qtdeEstagiario);
        });

        var options = {
          series: [funcionario, estagiarios],
            chart: {
            width: 380,
            type: 'pie',
          },
          title: {
            text: 'Supervisores e estagiários',
            align: 'left',
            margin: 10,
            offsetX: 0,
            offsetY: 0,
            floating: false,
            style: {
              fontSize:  '14px',
              fontWeight:  'bold',
              fontFamily:  undefined,
              color:  '#263238'
            },
          },
          labels: ['Supervisores', 'Estagiarios'],
          responsive: [{
            breakpoint: 480,
            options: {
              chart: {
                width: 200
              },
              legend: {
                position: 'bottom'
              }
            }
          }]
        };

        // Renderiza o gráfico com as novas opções
        var chart = new ApexCharts(document.querySelector("#chart"), options);
        chart.render();
    }
</script>
<?php
$content = ob_get_clean(); 
$pageTitle = "Dashboard"; 
include('../app/public/html/template.php'); 
?>
