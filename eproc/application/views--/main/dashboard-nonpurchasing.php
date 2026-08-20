<?php 
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 * 
 */

$this->libsession->cekSession();   

// if($this->USER_TYPE_ID == "")
//     redirect("app");

$this->load->model("Paket");

// Laporan Berdasarkan Metode Pengadaan
$countTender = new Paket();
$sumTender = new Paket();
$countTenderCepat = new Paket();
$sumTenderCepat = new Paket();
$countPengadaanLangsung = new Paket();
$sumPengadaanLangsung = new Paket();
$countPenunjukanLangsung = new Paket();
$sumPenunjukanLangsung = new Paket();

// 1-e-Tender ,7-e-Tender Cepat, 2-Pengadaan Langsung ,5-Penunjukan Langsung Cepat
$getTahun = isset($_GET['tahun']) ? $_GET['tahun'] : 'all';
// $getTahun = $_GET['tahun'];
if ($getTahun != 'all'){
// if ($getTahun){
  $tahun = 'Tahun '.$getTahun;
  $countTender = $countTender->getCountByParams(array("A.PAKET_METODE_LELANG_ID" => "1", "extract(year from A.TANGGAL)" => $getTahun)); 
  $sumTender = $sumTender->getSumByParams(array("A.PAKET_METODE_LELANG_ID" => "1", "extract(year from A.TANGGAL)" => $getTahun)); 
  $countTenderCepat = $countTenderCepat->getCountByParams(array("A.PAKET_METODE_LELANG_ID" => "7", "extract(year from A.TANGGAL)" => $getTahun)); 
  $sumTenderCepat = $sumTenderCepat->getSumByParams(array("A.PAKET_METODE_LELANG_ID" => "7", "extract(year from A.TANGGAL)" => $getTahun)); 
  $countPengadaanLangsung = $countPengadaanLangsung->getCountByParams(array("A.PAKET_METODE_LELANG_ID" => "2", "extract(year from A.TANGGAL)" => $getTahun)); 
  $sumPengadaanLangsung = $sumPengadaanLangsung->getSumByParams(array("A.PAKET_METODE_LELANG_ID" => "2", "extract(year from A.TANGGAL)" => $getTahun)); 
  $countPenunjukanLangsung = $countPenunjukanLangsung->getCountByParams(array("A.PAKET_METODE_LELANG_ID" => "5", "extract(year from A.TANGGAL)" => $getTahun)); 
  $sumPenunjukanLangsung = $sumPenunjukanLangsung->getSumByParams(array("A.PAKET_METODE_LELANG_ID" => "5", "extract(year from A.TANGGAL)" => $getTahun)); 
} else { 
  $tahun = ''; 
  $countTender = $countTender->getCountByParams(array("A.PAKET_METODE_LELANG_ID" => "1")); 
  $sumTender = $sumTender->getSumByParams(array("A.PAKET_METODE_LELANG_ID" => "1")); 
  $countTenderCepat = $countTenderCepat->getCountByParams(array("A.PAKET_METODE_LELANG_ID" => "7")); 
  $sumTenderCepat = $sumTenderCepat->getSumByParams(array("A.PAKET_METODE_LELANG_ID" => "7")); 
  $countPengadaanLangsung = $countPengadaanLangsung->getCountByParams(array("A.PAKET_METODE_LELANG_ID" => "2")); 
  $sumPengadaanLangsung = $sumPengadaanLangsung->getSumByParams(array("A.PAKET_METODE_LELANG_ID" => "2")); 
  $countPenunjukanLangsung = $countPenunjukanLangsung->getCountByParams(array("A.PAKET_METODE_LELANG_ID" => "5")); 
  $sumPenunjukanLangsung = $sumPenunjukanLangsung->getSumByParams(array("A.PAKET_METODE_LELANG_ID" => "5")); 
}
// End Laporan Berdasarkan Metode Pengadaan

$paketChart = new Paket();

// Laporan Berdasarkan Jenis Pengadaan
$getDataChartPie = new Paket();
if ($getTahun != 'all'){
  $getDataChartPie->getDashboardPie($getTahun); 
} else {
  $getDataChartPie->getDashboardPie(); 
}

while($getDataChartPie->nextRow())
  {  
    $pieLabel[] = $getDataChartPie->getField('paket_jenis_id_nama');
    $pieValue[] = $getDataChartPie->getField('total');
}
// EndLaporan Berdasarkan Jenis Pengadaan


// Laporan Berdasarkan Realisasi Paket
$getDataChartBar2 = new Paket();
if ($getTahun != 'all'){
  $getDataChartBar2->getDashboardBar2($getTahun); 
} else {
  $getDataChartBar2->getDashboardBar2(); 
}

while($getDataChartBar2->nextRow())
  {  
    // $bar2Label[] = $getDataChartBar2->getField('user_nama').' ('.$getDataChartBar2->getField('total_rencana').' Paket)';
    $bar2Label[] = $getDataChartBar2->getField('user_jabatan');
    $bar2PaketRencana[] = $getDataChartBar2->getField('total_rencana');
    $bar2PaketRealisasi[] = $getDataChartBar2->getField('total_realisasi');
    $bar2SisaPaketRencana[] = $getDataChartBar2->getField('total_rencana') - $getDataChartBar2->getField('total_realisasi');
}
// EndLaporan Berdasarkan Realisasi Paket


// Laporan Berdasarkan Beban Kerja Paket
$getDataChartGauge = new Paket();
if ($getTahun != 'all'){
  $getDataChartGauge->getDashboardGauge($getTahun); 
  $getDataChartGauge->firstRow(); 
  $totalPaketGauge = $getDataChartGauge->getField('TOTAL_PAKET');
  $totalPaketProsesGauge = $getDataChartGauge->getField('TOTAL_PAKET_PROSES');
} else {
  $getDataChartGauge->getDashboardGauge(); 
  $getDataChartGauge->firstRow(); 
  $totalPaketGauge = $getDataChartGauge->getField('TOTAL_PAKET');
  $totalPaketProsesGauge = $getDataChartGauge->getField('TOTAL_PAKET_PROSES');
}
// EndLaporan Berdasarkan Beban Kerja Paket

// echo "<pre>"; print_r($pieValue); die();
?>
 <script src="https://unpkg.com/gauge-chart@latest/dist/bundle.js"></script>
<script src="<?=base_url()?>assets/new/vendors/js/charts/chart.min.js"></script>
<!-- https://codepen.io/b1tn3r/pen/erLqbQ -->
<!-- <script src="<?=base_url()?>assets/new/js/scripts/ui/breadcrumbs-with-stats.js"></script> -->
<!-- <script src="<?=base_url()?>assets/new/js/scripts/charts/chartjs/bar/bar.js"></script> -->
<!-- <script src="<?=base_url()?>assets/new/js/scripts/charts/chartjs/bar/bar-stacked.js"></script> -->
<!-- <script src="<?=base_url()?>assets/new/js/scripts/charts/chartjs/bar/bar-multi-axis.js"></script> -->
<!-- <script src="<?=base_url()?>assets/new/js/scripts/charts/chartjs/bar/column.js"></script> -->
<!-- <script src="<?=base_url()?>assets/new/js/scripts/charts/chartjs/bar/column-stacked.js"></script> -->
<!-- <script src="<?=base_url()?>assets/new/js/scripts/charts/chartjs/bar/column-multi-axis.js"></script> -->

<script type="text/javascript">
/*
$(function(){
  $('#setyear').on('change', function () {
      var url = $(this).val(); // get selected value
      if (url) { // require a URL
          window.location = url; // redirect
      }
      return false;
  });
});
*/

$(window).on("load", function(){

    //Get the context of the Chart canvas element we want to select
    var ctx = $("#column-chart"); 
    // Chart Options
    var chartOptions = {
        // Elements options apply to all of the options unless overridden in a dataset
        // In this case, we are setting the border of each bar to be 2px wide and green
        elements: {
            rectangle: {
                borderWidth: 2,
                borderColor: 'rgb(0, 255, 0)',
                borderSkipped: 'bottom'
            }
        },
        responsive: true,
        maintainAspectRatio: false,
        responsiveAnimationDuration:2500,
        legend: {
            position: 'top',
        },
        scales: {
            xAxes: [{
                display: true,
                gridLines: {
                    color: "#f3f3f3",
                    drawTicks: false,
                },
                scaleLabel: {
                    display: true,
                }
            }],
            yAxes: [{
                display: true,
                gridLines: {
                    color: "#f3f3f3",
                    drawTicks: false,
                },
                scaleLabel: {
                    display: true,
                }
            }]
        },
        title: {
            display: true,
            text: 'Laporan Berdasarkan Metode Pengadaan <?= $tahun ?>'
        },  
        onClick: function(e){
        }
    };

    // Chart Data
    var chartData = {
        labels: ["Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"],
        datasets: [
          <?php 
          for ($i=0; $i < 4 ; $i++) { 
            $color = array('#76b82a','#38H076','#F98E76','#ffcc33');
            $label = array('Tender','Tender_Cepat','Penunjukan_Langsung','Pengadaan_Langsung');
          ?>
          {
              label: "<?= str_replace("_", " ", $label[$i]) ?>",
              data: [ 
              <?php 
              $datanya = '';
              $paketChart->getDashboard($getTahun); 
              while($paketChart->nextRow())
                {  
                  $datanya .= $paketChart->getField($label[$i]).',';
              // 65, 59, 80, 81, 56, 76, 24, 67, 90, 109, 36, 89, 
              }
              echo $datanya; 
              ?>
              ],
              backgroundColor: "<?= $color[$i] ?>",
              // hoverBackgroundColor: "rgba(22,211,154,.9)",
              borderColor: "transparent"
          },  
          <?php 
          } ?>
        ],
    };

    var config = {
        type: 'bar',

        // Chart Options
        options : chartOptions,

        data : chartData, 

    };


    // Create the chart
    var lineChart = new Chart(ctx, config);

    document.getElementById("column-chart").onclick = function (evt) {
        var activePoints = lineChart.getElementsAtEventForMode(evt, 'point', lineChart.options);
        var firstPoint = activePoints[0];
        var label = lineChart.data.labels[firstPoint._index];
        // var value = lineChart.data.datasets[firstPoint._datasetIndex].data[firstPoint._index];
        var value = lineChart.data.datasets[firstPoint._datasetIndex].data[firstPoint._index];
        var value2 = lineChart.data.datasets[firstPoint._datasetIndex].label;
        // alert(label + ":" + value + ":" + value2);
        getDashboardDetail(label,value2);
    };
});
  
function getDashboardDetail(a,b) {
  var bulan   = a;
  var metode  = b;
  var tahun   = $('#tahun').val();
  var url = 'main/loadUrl/main/dashboard_detail2/?metode='+metode+'&bulan='+bulan+'&tahun='+tahun;
  openAdd(url);
  // alert(a+'-'+tahun+'-'+b);
}
 
$(function() {
  var canvas = document.getElementById("barChart");
  var ctx = canvas.getContext('2d');

  // Global Options:
  Chart.defaults.global.defaultFontColor = 'black';
  Chart.defaults.global.defaultFontSize = 16;

  var data = {
      labels: [
       <?php 
       foreach ($pieLabel as $valpieLabel): 
        echo '"'.$valpieLabel.'",';
       endforeach ?>
      ],
      datasets: 
      [{
          fill: true,
          backgroundColor: ['#76b82a','#ea5b1b','#ffcc33','#cc3333',],
          data: [
          <?php 
           foreach ($pieValue as $valpieValue): 
            echo '"'.$valpieValue.'",';
           endforeach ?>
           ],
          // Notice the borderColor 
          borderColor:  ['#76b82a','#ea5b1b','#ffcc33','#cc3333',],
          borderWidth: [2,2,2,2]
        }
      ]
  };
  // Notice the rotation from the documentation.
  var options = 
      {
        title: 
        {
          display: true,
          text: 'Laporan Berdasarkan Jenis Pengadaan <?= $tahun ?>',
          position: 'top'
        },
          animation: {
            duration: 2500,
          },
    rotation: -0.7 * Math.PI
  };

  // Chart declaration:
  var myBarChart = new Chart(ctx, {
      type: 'pie',
      data: data,
      options: options
  });

  canvas.onclick = function(evt) {
  var tahun   = $('#tahun').val();
    var activePoints = myBarChart.getElementsAtEvent(evt);
    if (activePoints[0]) {
      var chartData = activePoints[0]['_chart'].config.data;
      var idx = activePoints[0]['_index'];

      var label = chartData.labels[idx];
      var value = chartData.datasets[0].data[idx];
      // alert(label+'-'+value);
      var url = 'main/loadUrl/main/dashboard_detail_pie/?jenis='+label+'&jumlah='+value+'&tahun='+tahun;
      openAdd(url);
    }
  };
});

$(function() {
 // Return with commas in between
  var numberWithCommas = function(x) {
    return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
  };

  var dataPack1 = [ // sisa paket rencana
                   <?php 
                   foreach ($bar2SisaPaketRencana as $valBar2PaketRencana): 
                    echo $valBar2PaketRencana.',';
                   endforeach ?>
                  ]; //
  var dataPack2 = [ // paket realisasi
                   <?php 
                   foreach ($bar2PaketRealisasi as $valBar2PaketRealisasi): 
                    echo $valBar2PaketRealisasi.',';
                   endforeach ?>
                  ]; //
  var dates = [
                <?php 
                 foreach ($bar2Label as $valBar2Label): 
                  echo '["'.$valBar2Label.'"],';
                 endforeach ?>
              ];

  var bar_ctx = document.getElementById('bar-chart');

  var bar_chart = new Chart(bar_ctx, {
      type: 'bar',
      data: {
          labels: dates,
          datasets: [
          {
              label: 'Sisa Paket Rencana',
              data: dataPack1,
              backgroundColor: "#a40023",
              hoverBackgroundColor: "#e90036",
              hoverBorderWidth: 0,
          },
          {
              label: 'Paket Realisasi',
              data: dataPack2,
              backgroundColor: "#5fc91f",
              hoverBackgroundColor: "#0ad400",
              hoverBorderWidth: 0
          }, 
          ]
      },
      options: {
        title: 
          {
            display: true,
            text: 'Laporan Berdasarkan Realisasi Paket <?= $tahun ?>',
            position: 'top',
          },
          animation: {
            duration: 2500,
          },
          tooltips: {
            mode: 'label',
            callbacks: {
            label: function(tooltipItem, data) { 
              return data.datasets[tooltipItem.datasetIndex].label + ": " + numberWithCommas(tooltipItem.yLabel);
            }
            }
           },
          scales: {
            xAxes: [{ 
              stacked: true, 
              gridLines: { display: false },
              }],
            yAxes: [{ 
              stacked: true, 
              ticks: {
                callback: function(value) { return numberWithCommas(value); },
              }, 
              }],
          },
          legend: {display: true}
      }
     }
  );

  document.getElementById("bar-chart").onclick = function (evt) {
    var tahun   = $('#tahun').val();
    var activePoints = bar_chart.getElementsAtEventForMode(evt, 'point', bar_chart.options);
    var firstPoint = activePoints[0];
    var label = bar_chart.data.labels[firstPoint._index];
    var value = bar_chart.data.datasets[firstPoint._datasetIndex].data[firstPoint._index];
    var value2 = bar_chart.data.datasets[firstPoint._datasetIndex].label;
    getDashboardDetailBarChart(label,value,value2,tahun);
  };
});

function getDashboardDetailBarChart(a,b,c,d) {
  // alert(a+'-'+b+'-'+c+'-'+d);
  var url = 'main/loadUrl/main/dashboard_detail_bar/?pengguna='+a+'&total='+b+'&jenis='+c+'&tahun='+d;
  openAdd(url);
}

$(function() {
  // https://github.com/recogizer/gauge-chart
  // https://recogizer.github.io/gauge-chart/examples/samples/
  // Element inside which you want to see the chart
  let element = document.querySelector('#gaugeArea')
  let options = { 
    hasNeedle: true,
    needleColor: "black",
    // needleStartValue: 50,
    arcColors: ["rgb(255,84,84)","rgb(239,214,19)","rgb(61,204,91)"],
    arcDelimiters: [30,60],
    rangeLabel: ["0","<?= $totalPaketGauge ?>"],
  }
  // Drawing and updating the chart
  GaugeChart.gaugeChart(element, 533, options).updateNeedle(<?= $totalPaketProsesGauge ?>0); 

  document.getElementById("gaugeArea").onclick = function (evt) {
    var tahun   = $('#tahun').val();
    var url = 'main/loadUrl/main/dashboard_detail_gauge/?tahun='+tahun;
    openAdd(url);
  };
});

</script>

<!-- <style type="text/css">
  #column-chart {
    cursor: pointer;
  }
</style>  -->

<style type="text/css">
.chart-content {
  padding: 5px;
  /*background-color: #f9f9f9;*/
  /*width: 700px;*/
  margin: 10px; 
  /*box-shadow: 0px 0px 2px #ccc;*/
}

.chart-legend{
  font-size: 0.8em;
  li {
    list-style: none;
    span {
      display: inline-block;
      width: 8px;
      height: 8px;
      margin-right: 5px;
    }
  }
} 
</style>
 
<div class="row">
  <div class="form-group col-md-12">
    <!-- <label>Pilih Tahun</label> -->
    <select class="form-control" id="setyear" onChange="return window.location = $(this).val()">
      <?php 
      $selected = '';
      $url = base_url('main/index/dashboard?tahun=');
      $kurangdari = date('Y') - 5;
            echo '<option value="">-- Pilih Tahun --</option>';
      for ($i= date('Y'); $i > $kurangdari   ; $i--) {  
           // if ($i == date('Y') || $i == $getTahun) {
           if ($i == $getTahun) {
            $selected = 'selected';
           } else {
            $selected = '';
           }
            echo '<option value="'.$url.$i.'" '.$selected.'>'.$i.'</option>';
      } 
      ?>
    </select>
  </div> 
</div> 

<div class="row">
  <input type="hidden" id="tahun" value="<?=$getTahun?>">
    <input type="hidden" id="metode" value="">
    <input type="hidden" id="bulan" value="">

  <div class="col-md-3">
    <!-- <div class="card"> -->
    <div class="card box-shadow-0 animated zoomIn" data-appear="appear" data-animation="zoomIn">
        <div class="card-content">
            <div class="card-body" onclick="openAdd('main/loadUrl/main/dashboard_detail/?metode=1&tahun=<?=$getTahun?>')" style="cursor: pointer;">
                <div class="media">
                    <div class="media-body text-left">
                        <h3 class="orange"><?= $countTender ?></h3>
                        <span>e-Tender</span>
                    </div>
                    <div class="media-right media-middle">
                        <i class="ft-package success font-large-2 float-right"></i>
                    </div>
                </div>
                <div class="mb-0">
                  <?= '<small style="font-weight:bold">Rp. '.number_format($sumTender,'0',',','.').'</small>'; ?>
                </div>

                <!-- <div class="progress mt-1 mb-0" style="height: 7px;">
                    <div class="progress-bar bg-success" role="progressbar" style="width: 80%" aria-valuenow="80" aria-valuemin="0" aria-valuemax="100"></div>
                </div> -->
            </div>
        </div>
    </div>
  </div>

  <div class="col-md-3">
    <!-- <div class="card"> -->
    <div class="card box-shadow-0 animated zoomIn" data-appear="appear" data-animation="zoomIn">

        <div class="card-content">
            <div class="card-body" onclick="openAdd('main/loadUrl/main/dashboard_detail/?metode=7&tahun=<?=$getTahun?>')" style="cursor: pointer;">
                <div class="media">
                    <div class="media-body text-left">
                        <h3 class="deep-orange"><?= $countTenderCepat ?></h3>
                        <span>e-Tender Cepat</span>
                    </div>
                    <div class="media-right media-middle">
                        <i class="ft-clock deep-orange font-large-2 float-right"></i>
                    </div>
                </div>
                <div class="mb-0">
                  <?= '<small style="font-weight:bold">Rp. '.number_format($sumTenderCepat,'0',',','.').'</small>'; ?>
                </div>
                <!-- <div class="progress mt-1 mb-0" style="height: 7px;">
                    <div class="progress-bar bg-deep-orange" role="progressbar" style="width: 35%" aria-valuenow="35" aria-valuemin="0" aria-valuemax="100"></div>
                </div> -->
            </div>
        </div>
    </div>
  </div>

  <div class="col-md-3">
    <!-- <div class="card"> -->
    <div class="card box-shadow-0 animated zoomIn" data-appear="appear" data-animation="zoomIn">
        <div class="card-content">
            <div class="card-body" onclick="openAdd('main/loadUrl/main/dashboard_detail/?metode=5&tahun=<?=$getTahun?>')" style="cursor: pointer;">
                <div class="media">
                    <div class="media-body text-left">
                        <h3 class="orange"><?= $countPenunjukanLangsung ?></h3>
                        <span>Penunjukan Langsung</span>
                    </div>
                    <div class="media-right media-middle">
                        <i class="ft-user-check orange font-large-2 float-right"></i>
                    </div>
                </div>
                <div class="mb-0">
                  <?= '<small style="font-weight:bold">Rp. '.number_format($sumPenunjukanLangsung,'0',',','.').'</small>'; ?>
                </div>
                <!-- <div class="progress mt-1 mb-0" style="height: 7px;">
                    <div class="progress-bar bg-orange" role="progressbar" style="width: 35%" aria-valuenow="35" aria-valuemin="0" aria-valuemax="100"></div>
                </div> -->
            </div>
        </div>
    </div>
  </div>

  <div class="col-md-3">
    <!-- <div class="card"> -->
    <div class="card box-shadow-0 animated zoomIn" data-appear="appear" data-animation="zoomIn">
        <div class="card-content">
            <div class="card-body" onclick="openAdd('main/loadUrl/main/dashboard_detail/?metode=2&tahun=<?=$getTahun?>')" style="cursor: pointer;">
                <div class="media">
                    <div class="media-body text-left">
                        <h3 class="info"><?= $countPengadaanLangsung ?></h3>
                        <span>Pengadaan Langsung</span>
                    </div>
                    <div class="media-right media-middle">
                        <i class="ft-check-circle info font-large-2 float-right"></i>
                    </div>
                </div>
                <div class="mb-0">
                  <?= '<small style="font-weight:bold">Rp. '.number_format($sumPengadaanLangsung,'0',',','.').'</small>'; ?>
                </div>
                <!-- <div class="progress mt-1 mb-0" style="height: 7px;">
                    <div class="progress-bar bg-info" role="progressbar" style="width: 35%" aria-valuenow="35" aria-valuemin="0" aria-valuemax="100"></div>
                </div> -->
            </div>
        </div>
    </div>
  </div>

  <!-- Column Chart -->
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <!-- <h4 class="card-title">Column Chart</h4> -->
                <a class="heading-elements-toggle"><i class="fa fa-ellipsis-v font-medium-3"></i></a>
                <div class="heading-elements">
                    <ul class="list-inline mb-0">
                        <li><a data-action="collapse"><i class="ft-minus"></i></a></li>
                        <li><a data-action="expand"><i class="ft-maximize"></i></a></li>
                        <li><a data-action="close"><i class="ft-x"></i></a></li>
                    </ul>
                </div>
            </div>
            <div class="card-content collapse show">
                <div class="height-400">
                        <canvas id="column-chart"></canvas>
                    </div>
            </div>
        </div>
    </div>

     <div class="col-md-7">
        <div class="card">
            <div class="card-header">
                <a class="heading-elements-toggle"><i class="fa fa-ellipsis-v font-medium-3"></i></a>
                <div class="heading-elements">
                    <ul class="list-inline mb-0">
                        <li><a data-action="collapse"><i class="ft-minus"></i></a></li>
                        <li><a data-action="expand"><i class="ft-maximize"></i></a></li>
                        <li><a data-action="close"><i class="ft-x"></i></a></li>
                    </ul>
                </div>
            </div>
            <div class="card-content collapse show">
                <div class="chart-content"> 
                  <canvas id="barChart"></canvas>
                </div> 
            </div>
        </div> 
    </div>

    <div class="col-md-5">
        <div class="card">
            <div class="card-header">
                <a class="heading-elements-toggle"><i class="fa fa-ellipsis-v font-medium-3"></i></a>
                <div class="heading-elements">
                    <ul class="list-inline mb-0">
                        <li><a data-action="collapse"><i class="ft-minus"></i></a></li>
                        <li><a data-action="expand"><i class="ft-maximize"></i></a></li>
                        <li><a data-action="close"><i class="ft-x"></i></a></li>
                    </ul>
                </div>
            </div>
            <div class="card-content collapse show text-center" style="padding-bottom: 33px"> 
              <h5 style="margin-top: 6%; font-weight: bold;"><b>Laporan Berdasarkan Beban Kerja Paket <?= $tahun ?></b></h5>
                <div id="gaugeArea" style="margin-bottom: -50px"></div>
              <small><b>Jumlah Paket Yang Sedang Diproses</b></small>
            </div>
        </div> 
    </div>

    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <a class="heading-elements-toggle"><i class="fa fa-ellipsis-v font-medium-3"></i></a>
                <div class="heading-elements">
                    <ul class="list-inline mb-0">
                        <li><a data-action="collapse"><i class="ft-minus"></i></a></li>
                        <li><a data-action="expand"><i class="ft-maximize"></i></a></li>
                        <li><a data-action="close"><i class="ft-x"></i></a></li>
                    </ul>
                </div>
            </div>
            <div class="card-content collapse show"> 
                <div class="chart-content"> 
                  <canvas id="bar-chart" width="600" height="200"></canvas>
                </div>
            </div>
        </div> 
    </div>

</div>