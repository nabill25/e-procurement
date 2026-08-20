<?php
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 * 
 */

$this->libsession->cekSession(); // hanya untuk 1:admin, 7:kepala pengadaan, 10:audit

include_once("functions/string.func.php");
include_once("functions/date.func.php");

$unitkerja  = 'all';

$this->load->model("Dashpaket");

// Laporan Berdasarkan Metode Pengadaan
$countTender = new Dashpaket();
$sumTender = new Dashpaket();
$countTenderTerbatas = new Dashpaket();
$sumTenderTerbatas = new Dashpaket();
$countTenderCepat = new Dashpaket();
$sumTenderCepat = new Dashpaket();
$countPengadaanLangsung = new Dashpaket();
$sumPengadaanLangsung = new Dashpaket();
$countPenunjukanLangsung = new Dashpaket();
$sumPenunjukanLangsung = new Dashpaket();
$countPembelianLangsung = new Dashpaket();
$sumPembelianLangsung = new Dashpaket();
$countTenderKualifikasi = new Dashpaket();
$sumTenderKualifikasi = new Dashpaket();
$countOffline = new Dashpaket();
$sumOffline = new Dashpaket();
/*
x1:Tender, 
x3:Tender Terbatas, 
x7:Tender Cepat, 
x10:Tender Kualifikasi, 
x2:Pengadaan langsung, 
x5:Penunjukan Langsung, 
6:e-Purchasing, 
x9:Pembelian Langsung Offline, 
11:Penunjukan Langsung Khusus
*/

$getTahun = isset($_GET['tahun']) ? $_GET['tahun'] : 'all';
// $getTahun = $_GET['tahun'];
if ($getTahun != 'all'){
    $tahun = 'Tahun '.$getTahun;
    $countTender = $countTender->getCountByParams(array("A.PAKET_METODE_LELANG_ID" => "1", "TAHUN_PERMOHONAN" => $getTahun));
    $sumTender = $sumTender->getSumByParams(array("A.PAKET_METODE_LELANG_ID" => "1", "TAHUN_PERMOHONAN" => $getTahun));
    $countTenderTerbatas = $countTenderTerbatas->getCountByParams(array("A.PAKET_METODE_LELANG_ID" => "3", "TAHUN_PERMOHONAN" => $getTahun));
    $sumTenderTerbatas = $sumTenderTerbatas->getSumByParams(array("A.PAKET_METODE_LELANG_ID" => "3", "TAHUN_PERMOHONAN" => $getTahun));
    $countTenderCepat = $countTenderCepat->getCountByParams(array("A.PAKET_METODE_LELANG_ID" => "7", "TAHUN_PERMOHONAN" => $getTahun));
    $sumTenderCepat = $sumTenderCepat->getSumByParams(array("A.PAKET_METODE_LELANG_ID" => "7", "TAHUN_PERMOHONAN" => $getTahun));
    $countPengadaanLangsung = $countPengadaanLangsung->getCountByParams(array("A.PAKET_METODE_LELANG_ID" => "2", "TAHUN_PERMOHONAN" => $getTahun));
    $sumPengadaanLangsung = $sumPengadaanLangsung->getSumByParams(array("A.PAKET_METODE_LELANG_ID" => "2", "TAHUN_PERMOHONAN" => $getTahun));
    $countPenunjukanLangsung = $countPenunjukanLangsung->getCountByParams(array("A.PAKET_METODE_LELANG_ID" => "5", "TAHUN_PERMOHONAN" => $getTahun));
    $sumPenunjukanLangsung = $sumPenunjukanLangsung->getSumByParams(array("A.PAKET_METODE_LELANG_ID" => "5", "TAHUN_PERMOHONAN" => $getTahun));
    $countPembelianLangsung = $countPembelianLangsung->getCountByParams(array("A.PAKET_METODE_LELANG_ID" => "6", "TAHUN_PERMOHONAN" => $getTahun));
    $sumPembelianLangsung = $sumPembelianLangsung->getSumByParams(array("A.PAKET_METODE_LELANG_ID" => "6", "TAHUN_PERMOHONAN" => $getTahun));
    $countTenderKualifikasi = $countTenderKualifikasi->getCountByParams(array("A.PAKET_METODE_LELANG_ID" => "10", "TAHUN_PERMOHONAN" => $getTahun));
    $sumTenderKualifikasi = $sumTenderKualifikasi->getSumByParams(array("A.PAKET_METODE_LELANG_ID" => "10", "TAHUN_PERMOHONAN" => $getTahun));
    $countOffline = $countOffline->getCountByParams(array("A.PAKET_METODE_LELANG_ID" => "9", "TAHUN_PERMOHONAN" => $getTahun));
    $sumOffline = $sumOffline->getSumByParams(array("A.PAKET_METODE_LELANG_ID" => "9", "TAHUN_PERMOHONAN" => $getTahun));
} else {
    $tahun = '';
    $countTender = $countTender->getCountByParams(array("A.PAKET_METODE_LELANG_ID" => "1"));
    $sumTender = $sumTender->getSumByParams(array("A.PAKET_METODE_LELANG_ID" => "1"));
    $countTenderTerbatas = $countTenderTerbatas->getCountByParams(array("A.PAKET_METODE_LELANG_ID" => "3"));
    $sumTenderTerbatas = $sumTenderTerbatas->getSumByParams(array("A.PAKET_METODE_LELANG_ID" => "3"));
    $countTenderCepat = $countTenderCepat->getCountByParams(array("A.PAKET_METODE_LELANG_ID" => "7"));
    $sumTenderCepat = $sumTenderCepat->getSumByParams(array("A.PAKET_METODE_LELANG_ID" => "7"));
    $countPengadaanLangsung = $countPengadaanLangsung->getCountByParams(array("A.PAKET_METODE_LELANG_ID" => "2"));
    $sumPengadaanLangsung = $sumPengadaanLangsung->getSumByParams(array("A.PAKET_METODE_LELANG_ID" => "2"));
    $countPenunjukanLangsung = $countPenunjukanLangsung->getCountByParams(array("A.PAKET_METODE_LELANG_ID" => "5"));
    $sumPenunjukanLangsung = $sumPenunjukanLangsung->getSumByParams(array("A.PAKET_METODE_LELANG_ID" => "5"));
    $countPembelianLangsung = $countPembelianLangsung->getCountByParams(array("A.PAKET_METODE_LELANG_ID" => "6"));
    $sumPembelianLangsung = $sumPembelianLangsung->getSumByParams(array("A.PAKET_METODE_LELANG_ID" => "6"));
    $countTenderKualifikasi = $countTenderKualifikasi->getCountByParams(array("A.PAKET_METODE_LELANG_ID" => "10"));
    $sumTenderKualifikasi = $sumTenderKualifikasi->getSumByParams(array("A.PAKET_METODE_LELANG_ID" => "10"));
    $countOffline = $countOffline->getCountByParams(array("A.PAKET_METODE_LELANG_ID" => "9"));
    $sumOffline = $sumOffline->getSumByParams(array("A.PAKET_METODE_LELANG_ID" => "9"));
}
// End Laporan Berdasarkan Metode Pengadaan
// echo "<pre>"; print_r($pieValue); die();
?>
 <!-- <script src="https://unpkg.com/gauge-chart@latest/dist/bundle.js"></script> -->
<script src="<?=base_url()?>assets/new/vendors/js/charts/chart.min.js"></script>
<!-- <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.7.0/chart.min.js"></script> -->
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
// $(document).ready(function() {
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
            // text: 'Laporan Berdasarkan Metode Pengadaan <?= $tahun ?>'
            text: ''
        },
        onClick: function(e){
        }
    };

    // Chart Data
    var chartData = {
        labels: ["Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"],
        datasets: [
          <?php
          $paketChart = new Paket();
          for ($i=0; $i < 8 ; $i++) {
            $color = array('#76b82a','#ea5b1b','#F98E76','#ffcc33','#010066','#3bafda','#76b82a','#ea5b1b');
            // $label = array('Tender','Tender_Cepat','Penunjukan_Langsung','Pengadaan_Langsung');
            $label = array('Tender','Tender_Cepat','Tender_Terbatas','Tender_Kualifikasi','Penunjukan_Langsung','Pengadaan_Langsung','Pembelian_Online','Pembelian_Offline');
          ?>
          {
              label: "<?= str_replace("_", " ", $label[$i]) ?>",
              data: [
              <?php
              $datanya = '';
              $paketChart->getDashboard($unitkerja,$getTahun);
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
  var url = 'main/loadUrl/main/dashboard_detail/?metode='+metode+'&bulan='+bulan+'&tahun='+tahun+'&jenis=all';

  openAdd(url);
  // alert(a+'-'+tahun+'-'+b);
}

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
.wfont, .ft-info { color: #fff !important; }
.border-right {
    border-right: 1px solid #dee2e6!important;
}
.description-block {
    display: block;
    margin: 0px 0;
    text-align: center;
}
</style>

<div class="row">
  <div class="form-group col-md-3">
    <!-- <label>Pilih Tahun</label> -->
    <select class="form-control" id="setyear" onChange="return window.location = $(this).val()">
      <?php
      $selected = '';
      $url = base_url('main/index/dashboardall?tahun=');
      $kurangdari = date('Y') - 5;
            echo '<option value="'.$url.'all">-- Pilih Tahun --</option>';
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
  <div class="col-md-12">
    <div class="form-group alert" style="border: 1px solid #dee2e6!important">
      <div class="float-left">
        <h5><b>Dashboard 
        <?php 
          if ($getTahun != 'all'){
            echo 'Tahun Anggaran '.$getTahun; 
          } else {
            echo 'Tahun Anggaran All'; 
          }
        ?> 
        </b></h5>
      </div>
      &nbsp;
    </div>
  </div>
</div>

<div class="row">
  <div class="col-md-6"> </div>
  <div class="col-md-6" >
      <div class="card border-info border-darken-2">
        <div class="row">
            <div class="col-md-3 border-right">
                <i class="fa fa-question-circle" data-toggle="tooltip" title="Total Paket <?php if ($getTahun != 'all'){ echo 'Tahun Anggaran '.$getTahun; } ?>" data-placement="top" aria-hidden="true" style="position:absolute;right:10px;padding-top:10px;color:black"></i>
                <div class="description-block">
                    <div class="description-header" style="font-size:2rem;"><?= $countTender + $countTenderCepat + $countTenderTerbatas + $countTenderKualifikasi + $countPenunjukanLangsung + $countPengadaanLangsung + $countPembelianLangsung + $countOffline ?></div>
                    <span class="description-text">PAKET</span>
                </div>
            </div> 
            <div class="col-md-4">
                <i class="fa fa-question-circle" data-toggle="tooltip" title="Harga Perkiraan <?php if ($getTahun != 'all'){ echo 'Tahun Anggaran '.$getTahun; } ?>" data-placement="top" aria-hidden="true" style="position:absolute;right:25px;padding-top:10px;color:black"></i>
                <div class="description-block">
                    <div class="description-header" style="font-size:2rem;" data-toggle="tooltip" data-placement="top" data-original-title="">Rp <?php echo numberToSimbol(round($sumTender + $sumTenderCepat + $sumTenderTerbatas + $sumTenderKualifikasi + $sumPenunjukanLangsung + $sumPengadaanLangsung + $sumPembelianLangsung + $sumOfflin)) ?></div>
                    <span class="description-text">HARGA PERKIRAAN</span>
                </div>
            </div>
            <div class="col-md-4">
                <i class="fa fa-question-circle" data-toggle="tooltip" title="Harga Perkiraan <?php if ($getTahun != 'all'){ echo 'Tahun Anggaran '.$getTahun; } ?>" data-placement="top" aria-hidden="true" style="position:absolute;right:25px;padding-top:10px;color:black"></i>
                <div class="description-block">
                    <div class="description-header" style="font-size:2rem;" data-toggle="tooltip" data-placement="top" data-original-title="">Rp <?php echo numberToSimbol(round($sumTender + $sumTenderCepat + $sumTenderTerbatas + $sumTenderKualifikasi + $sumPenunjukanLangsung + $sumPengadaanLangsung + $sumPembelianLangsung + $sumOfflin)) ?></div>
                    <span class="description-text">HARGA FINAL/AKHIR</span>
                </div>
            </div>
        </div>
    </div>
  </div> 
</div>

<div class="row">
  <input type="hidden" id="tahun" value="<?=$getTahun?>">
    <input type="hidden" id="metode" value="">
    <input type="hidden" id="bulan" value="">

    <div class="col-md-2">
      <div class="card box-shadow-0 animated zoomIn wfont" data-appear="appear" data-animation="zoomIn" style="background-color: #7A3E29;">
        <div class="card-content">
            <div class="card-body" onclick="openAdd('main/loadUrl/main/dashboard_detail/?metode=1&tahun=<?=$getTahun?>&uki=<?=$unitkerja?>&uid=<?=$this->USER_LOGIN_ID?>&jenis=all')" style="cursor: pointer">
                <div class="media">
                    <div class="media-body text-center">
                        <span style="margin-top: 15%;">PERENCANAAN</span>
                        <h2 class="wfont mt-2"><b>Rp <?php echo numberToSimbol(round($sumTender)) ?> </b></h2>
                    </div>
                    <div class="media-right media-middle">
                        <i class="fa fa-question-circle dark" data-toggle="tooltip" data-placement="top" title="Total Tender" style="font-size:1.3em !important; cursor: pointer;"></i>
                    </div>
                </div>
                <div class="mt-1 text-center">
                  <?= '<small style="font-weight:bold">'.$countTender.' Paket</small>'; ?> <i class="fa fa-arrow-circle-right"></i>
                </div>
            </div>
        </div>
      </div>
    </div>   

    <div class="col-md-2">
      <div class="card box-shadow-0 animated zoomIn wfont" data-appear="appear" data-animation="zoomIn" style="background-color: #F7941D;">
        <div class="card-content">
            <div class="card-body" onclick="openAdd('main/loadUrl/main/dashboard_detail/?metode=7&tahun=<?=$getTahun?>&uki=<?=$unitkerja?>&uid=<?=$this->USER_LOGIN_ID?>&jenis=all')" style="cursor: pointer">
                <div class="media">
                    <div class="media-body text-center">
                        <span style="margin-top: 15%;">PEMILIHAN</span>
                        <h2 class="wfont mt-2"><b>Rp <?php echo numberToSimbol(round($sumTenderCepat)) ?> </b></h2>
                    </div>
                    <div class="media-right media-middle">
                        <i class="fa fa-question-circle dark" data-toggle="tooltip" data-placement="top" title="Total Tender Cepat" style="font-size:1.3em !important; cursor: pointer;"></i>
                    </div>
                </div>
                <div class="mt-1 text-center">
                  <?= '<small style="font-weight:bold">'.$countTenderCepat.' Paket</small>'; ?> <i class="fa fa-arrow-circle-right"></i>
                </div>
            </div>
        </div>
      </div>
    </div>   

    <div class="col-md-2">
      <div class="card box-shadow-0 animated zoomIn wfont" data-appear="appear" data-animation="zoomIn" style="background-color: #6F00FF;">
        <div class="card-content">
            <div class="card-body" onclick="openAdd('main/loadUrl/main/dashboard_detail/?metode=3&tahun=<?=$getTahun?>&uki=<?=$unitkerja?>&uid=<?=$this->USER_LOGIN_ID?>&jenis=all')" style="cursor: pointer">
                <div class="media">
                    <div class="media-body text-center">
                        <span style="margin-top: 15%;">KONTRAK PROSES</span>
                        <h2 class="wfont mt-2"><b>Rp <?php echo numberToSimbol(round($sumTenderTerbatas)) ?> </b></h2>
                    </div>
                    <div class="media-right media-middle">
                        <i class="fa fa-question-circle dark" data-toggle="tooltip" data-placement="top" title="Total Tender Terbatas" style="font-size:1.3em !important; cursor: pointer;"></i>
                    </div>
                </div>
                <div class="mt-1 text-center">
                  <?= '<small style="font-weight:bold">'.$countTenderTerbatas.' Paket</small>'; ?> <i class="fa fa-arrow-circle-right"></i>
                </div>
            </div>
        </div>
      </div>
    </div>     

    <div class="col-md-2">
      <div class="card box-shadow-0 animated zoomIn wfont" data-appear="appear" data-animation="zoomIn" style="background-color: #CF2E26;">
        <div class="card-content">
            <div class="card-body" onclick="openAdd('main/loadUrl/main/dashboard_detail/?metode=10&tahun=<?=$getTahun?>&uki=<?=$unitkerja?>&uid=<?=$this->USER_LOGIN_ID?>&jenis=all')" style="cursor: pointer">
                <div class="media">
                    <div class="media-body text-center">
                        <span style="margin-top: 15%;">KONTRAK SELESAI</span>
                        <h2 class="wfont mt-2"><b>Rp <?php echo numberToSimbol(round($sumTenderKualifikasi)) ?> </b></h2>
                    </div>
                    <div class="media-right media-middle">
                        <i class="fa fa-question-circle dark" data-toggle="tooltip" data-placement="top" title="Total Tender Kualifikasi" style="font-size:1.3em !important; cursor: pointer;"></i>
                    </div>
                </div>
                <div class="mt-1 text-center">
                  <?= '<small style="font-weight:bold">'.$countTenderKualifikasi.' Paket</small>'; ?> <i class="fa fa-arrow-circle-right"></i>
                </div>
            </div>
        </div>
      </div>
    </div>    
  



<!--
----------------------------------------------------------------------------------------------------------------
  LAPORAN BERDASARKAN BEBAN KERJA PAKET
----------------------------------------------------------------------------------------------------------------
-->
<?php
// Laporan Berdasarkan Beban Kerja Paket
$getDataChartGauge = new Paket();
if ($getTahun != 'all'){
  $getDataChartGauge->getDashboardGauge($unitkerja,$getTahun);
  $getDataChartGauge->firstRow();
  $totalPaketGauge = $getDataChartGauge->getField('TOTAL_PAKET') ?: 0;
  $totalPaketProsesGauge = $getDataChartGauge->getField('TOTAL_PAKET_PROSES') ?: 0;
} else {
  $getDataChartGauge->getDashboardGauge($unitkerja);
  $getDataChartGauge->firstRow();
  $totalPaketGauge = $getDataChartGauge->getField('TOTAL_PAKET') ?: 0;
  $totalPaketProsesGauge = $getDataChartGauge->getField('TOTAL_PAKET_PROSES') ?: 0;
}
// EndLaporan Berdasarkan Beban Kerja Paket
?>
<script type="module">
import * as GaugeChart from 'https://unpkg.com/gauge-chart@next/dist/bundle.mjs'
$(function() {
  // https://github.com/recogizer/gauge-chart
  // https://recogizer.github.io/gauge-chart/examples/samples/
  // Element inside which you want to see the chart
  let element = document.querySelector('#gaugeArea')
  let options = {
    hasNeedle: true,
    needleColor: "black",
    // needleStartValue: 50,
    // arcColors: ["rgb(255,84,84)","rgb(239,214,19)","rgb(61,204,91)"],
    arcColors: ["rgb(61,204,91)","rgb(239,214,19)","rgb(255,84,84)"],
    arcDelimiters: [30,60],
    rangeLabel: ["0","<?= $totalPaketGauge ?>"],
    centralLabel: '<?= $totalPaketProsesGauge ?>',
  }
  // Drawing and updating the chart
  // GaugeChart.gaugeChart(element, 533, options).updateNeedle(<?= $totalPaketProsesGauge ?>0);
  GaugeChart.gaugeChart(element, 533, options).updateNeedle(<?= $totalPaketProsesGauge ?>);

  document.getElementById("gaugeArea").onclick = function (evt) {
    var tahun   = $('#tahun').val();
    var url = 'main/loadUrl/main/dashboard_detail_gauge/?tahun='+tahun+'&uki=<?= $unitkerja ?>&type=all';
    openAdd(url);
  };
});
</script>

  <div class="col-md-6">
    <div class="card">
      <div class="card-header card-head-inverse bg-primary">
        <h4 class="card-title text-white" style="font-size:.9em !important">Laporan Berdasarkan Beban Kerja P1 Paket <?= $tahun ?> </h4>
        <a class="heading-elements-toggle"><i class="fa fa-ellipsis-v font-medium-3"></i></a>
        <div class="heading-elements">
            <ul class="list-inline mb-0">
              <li><a data-action="expand"><i class="ft-maximize"></i></a></li>
              <!-- <li><a data-action="close"><i class="ft-x"></i></a></li> -->
            </ul>
        </div>
      </div>
      <div class="card-content collapse show text-center border-info border-darken-2" style="padding-bottom: 33px">
        <!-- <h5 style="margin-top: 6%; font-weight: bold;"><b>Laporan Berdasarkan Beban Kerja Paket <?= $tahun ?></b></h5> -->
          <div id="gaugeArea" style="margin-bottom: -50px"></div>
        <small><b>Jumlah Paket Yang Sedang Diproses P1 </b></small>
      </div>
    </div>
  </div>


<?php
// Laporan Berdasarkan Beban Kerja Paket
$getDataChartGauge2 = new Paket();
if ($getTahun != 'all'){
  $getDataChartGauge2->getDashboardGauge($unitkerja,$getTahun);
  $getDataChartGauge2->firstRow();
  $totalPaketGauge2 = $getDataChartGauge2->getField('TOTAL_PAKET') ?: 0;
  $totalPaketProsesGauge2 = $getDataChartGauge2->getField('TOTAL_PAKET_PROSES') ?: 0;
} else {
  $getDataChartGauge2->getDashboardGauge($unitkerja);
  $getDataChartGauge2->firstRow();
  $totalPaketGauge2 = $getDataChartGauge2->getField('TOTAL_PAKET') ?: 0;
  $totalPaketProsesGauge2 = $getDataChartGauge2->getField('TOTAL_PAKET_PROSES') ?: 0;
}
// EndLaporan Berdasarkan Beban Kerja Paket
?>
<script type="module">
import * as GaugeChart from 'https://unpkg.com/gauge-chart@next/dist/bundle.mjs'
$(function() {
  // https://github.com/recogizer/gauge-chart
  // https://recogizer.github.io/gauge-chart/examples/samples/
  // Element inside which you want to see the chart
  let element = document.querySelector('#gaugeArea2')
  let options = {
    hasNeedle: true,
    needleColor: "black",
    // needleStartValue: 50,
    // arcColors: ["rgb(255,84,84)","rgb(239,214,19)","rgb(61,204,91)"],
    arcColors: ["rgb(61,204,91)","rgb(239,214,19)","rgb(255,84,84)"],
    arcDelimiters: [30,60],
    rangeLabel: ["0","<?= $totalPaketGauge2 ?>"],
    centralLabel: '<?= $totalPaketProsesGauge2 ?>',
  }
  // Drawing and updating the chart
  // GaugeChart.gaugeChart(element, 533, options).updateNeedle(<?= $totalPaketProsesGauge2 ?>0);
  GaugeChart.gaugeChart(element, 533, options).updateNeedle(<?= $totalPaketProsesGauge2 ?>);

  document.getElementById("gaugeArea2").onclick = function (evt) {
    var tahun   = $('#tahun').val();
    var url = 'main/loadUrl/main/dashboard_detail_gauge/?tahun='+tahun+'&uki=<?= $unitkerja ?>&type=all';
    openAdd(url);
  };
});
</script>

  <div class="col-md-6">
    <div class="card">
      <div class="card-header card-head-inverse bg-primary">
        <h4 class="card-title text-white" style="font-size:.9em !important">Laporan Berdasarkan Beban Kerja P2 Paket <?= $tahun ?> </h4>
        <a class="heading-elements-toggle"><i class="fa fa-ellipsis-v font-medium-3"></i></a>
        <div class="heading-elements">
            <ul class="list-inline mb-0">
              <li><a data-action="expand"><i class="ft-maximize"></i></a></li>
              <!-- <li><a data-action="close"><i class="ft-x"></i></a></li> -->
            </ul>
        </div>
      </div>
      <div class="card-content collapse show text-center border-info border-darken-2" style="padding-bottom: 33px">
        <!-- <h5 style="margin-top: 6%; font-weight: bold;"><b>Laporan Berdasarkan Beban Kerja Paket <?= $tahun ?></b></h5> -->
          <div id="gaugeArea2" style="margin-bottom: -50px"></div>
        <small><b>Jumlah Paket Yang Sedang Diproses p2 </b></small>
      </div>
    </div>
  </div>

<!--
----------------------------------------------------------------------------------------------------------------
  LAPORAN BERDASARKAN JENIS PENGADAAN
----------------------------------------------------------------------------------------------------------------
-->
<?php
// Laporan Berdasarkan Jenis Pengadaan
$getDataChartPie = new Paket();
// if ($getTahun != 'all'){
  $getDataChartPie->getDashboardPie($unitkerja,$getTahun);
// } else {
//   $getDataChartPie->getDashboardPie($unitkerja);
// }
while($getDataChartPie->nextRow())
  {
    $pieLabel[] = $getDataChartPie->getField('paket_jenis_id_nama');
    $pieValue[] = $getDataChartPie->getField('total');
}
if (isset($pieLabel)) {
    if (count($pieLabel) > 0 && count($pieValue) > 0) {
      $pieLabel = $pieLabel;
      $pieValue = $pieValue;
    } else {
      $pieLabel = array('0' => 'Pekerjaan Konstruksi', '1' => 'Jasa Konsultansi', '2' => 'Barang', '3' => 'Jasa Lainnya', '4' => 'Katalog');
      $pieValue = array('0' => 0, '1' => 0, '2' => 0, '3' => 0, '4' => 0);
    }
} else {
    $pieLabel = array('0' => 'Pekerjaan Konstruksi', '1' => 'Jasa Konsultansi', '2' => 'Barang', '3' => 'Jasa Lainnya', '4' => 'Katalog');
    $pieValue = array('0' => 0, '1' => 0, '2' => 0, '3' => 0, '4' => 0);
}
// EndLaporan Berdasarkan Jenis Pengadaan
// echo "<pre>"; print_r($pieValue); die();
?>

<script type="text/javascript">
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
          // text: 'Laporan Berdasarkan Jenis Pengadaan <?= $tahun ?>',
          text: '',
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
      var url = 'main/loadUrl/main/dashboard_detail_pie/?jenis='+label+'&jumlah='+value+'&tahun='+tahun+'&uki=<?= $unitkerja ?>&type=all';
      openAdd(url);
    }
  };
});
</script>
 <div class="col-md-6">
  <div class="card">
    <div class="card-header card-head-inverse bg-primary">
        <h4 class="card-title text-white" style="font-size:.9em !important">Laporan Berdasarkan Jenis Pengadaan <?= $tahun ?> </h4>
        <a class="heading-elements-toggle"><i class="fa fa-ellipsis-v font-medium-3"></i></a>
        <div class="heading-elements">
            <ul class="list-inline mb-0">
              <li><a data-action="expand"><i class="ft-maximize"></i></a></li>
              <!-- <li><a data-action="close"><i class="ft-x"></i></a></li> -->
            </ul>
        </div>
    </div>
    <div class="card-content collapse show border-info border-darken-2">
      <div class="chart-content">
        <canvas id="barChart"></canvas>
      </div>
    </div>
  </div>
</div>


<script type="text/javascript">
$(function() {
  var canvas = document.getElementById("barChart2");
  var ctx = canvas.getContext('2d');

  // Global Options:
  Chart.defaults.global.defaultFontColor = 'black';
  Chart.defaults.global.defaultFontSize = 16;

  var data = {
      labels: [
       <?php
       $pieLabel2 = array("Swakelola","Penyedia");
       foreach ($pieLabel2 as $valpieLabel):
        echo '"'.$valpieLabel.'",';
       endforeach ?>
      ],
      datasets:
      [{
          fill: true,
          backgroundColor: ['#76b82a','#ea5b1b',],
          data: [
          <?php
           foreach ($pieValue as $valpieValue):
            echo '"'.$valpieValue.'",';
           endforeach ?>
           ],
          // Notice the borderColor
          borderColor:  ['#76b82a','#ea5b1b',],
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
          // text: 'Laporan Berdasarkan Jenis Pengadaan <?= $tahun ?>',
          text: '',
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
      var url = 'main/loadUrl/main/dashboard_detail_pie/?jenis='+label+'&jumlah='+value+'&tahun='+tahun+'&uki=<?= $unitkerja ?>&type=all';
      openAdd(url);
    }
  };
});
</script>
<div class="col-md-6">
  <div class="card">
    <div class="card-header card-head-inverse bg-primary">
        <h4 class="card-title text-white" style="font-size:.9em !important">Laporan Berdasarkan Cara Pengadaan <?= $tahun ?> </h4>
        <a class="heading-elements-toggle"><i class="fa fa-ellipsis-v font-medium-3"></i></a>
        <div class="heading-elements">
            <ul class="list-inline mb-0">
              <li><a data-action="expand"><i class="ft-maximize"></i></a></li>
              <!-- <li><a data-action="close"><i class="ft-x"></i></a></li> -->
            </ul>
        </div>
    </div>
    <div class="card-content collapse show border-info border-darken-2">
      <div class="chart-content">
        <canvas id="barChart2"></canvas>
      </div>
    </div>
  </div>
</div>

<!--
----------------------------------------------------------------------------------------------------------------
  LAPORAN BERDASARKAN REALISASI PAKET
----------------------------------------------------------------------------------------------------------------
-->
<?php
// Laporan Berdasarkan Realisasi Paket
$getDataChartBar2 = new Paket();
if ($getTahun != 'all'){
  $getDataChartBar2->getDashboardBar2($unitkerja,$getTahun);
} else {
  $getDataChartBar2->getDashboardBar2($unitkerja);
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
// echo "<pre>"; print_r($bar2SisaPaketRencana); die();
 ?>

 <script type="text/javascript">
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
            // text: 'Laporan Berdasarkan Realisasi Paket <?= $tahun ?>',
            text: '',
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

  function getDashboardDetailBarChart(a,b,c,d) {
    // alert(a+'-'+b+'-'+c+'-'+d);
    var url = 'main/loadUrl/main/dashboard_detail_bar/?pengguna='+a+'&total='+b+'&jenis='+c+'&tahun='+d;
    openAdd(url);
  }

});
</script>
  <div class="col-md-12">
    <div class="card">
      <div class="card-header card-head-inverse bg-primary">
        <h4 class="card-title text-white" style="font-size:.9em !important">Laporan Berdasarkan Realisasi Paket <?= $tahun ?> </h4>
        <a class="heading-elements-toggle"><i class="fa fa-ellipsis-v font-medium-3"></i></a>
        <div class="heading-elements">
            <ul class="list-inline mb-0">
              <li><a data-action="expand"><i class="ft-maximize"></i></a></li>
              <!-- <li><a data-action="close"><i class="ft-x"></i></a></li> -->
            </ul>
        </div>
      </div>
      <div class="card-content collapse show text-center border-info border-darken-2">
        <div class="chart-content">
          <canvas id="bar-chart" width="600" height="200"></canvas>
        </div>
      </div>
    </div>
  </div>

</div>
