<?php
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 * 
 */

$this->libsession->cekSession(); // hanya untuk 1:admin, 7:kepala pengadaan, 10:audit

$unitkerja  = 'all';

$this->load->model("Paket");

// Laporan Berdasarkan Metode Pengadaan
$countTender = new Paket();
$sumTender = new Paket();
$countTenderTerbatas = new Paket();
$sumTenderTerbatas = new Paket();
$countTenderCepat = new Paket();
$sumTenderCepat = new Paket();
$countPengadaanLangsung = new Paket();
$sumPengadaanLangsung = new Paket();
$countPenunjukanLangsung = new Paket();
$sumPenunjukanLangsung = new Paket();
$countPembelianLangsung = new Paket();
$sumPembelianLangsung = new Paket();
$countTenderKualifikasi = new Paket();
$sumTenderKualifikasi = new Paket();
$countOffline = new Paket();
$sumOffline = new Paket();
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
</style>

<div class="row">
  <div class="form-group col-md-12">
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
    <div class="col-md-6">
        <div class="card box-shadow-0 animated zoomIn" data-appear="appear" data-animation="zoomIn">
            <div class="card-content" style="padding: 20px;">
                 <canvas id="myChart"width="300" height="100"></canvas>
            </div>
        </div>
    </div>

<script>
// Bar chart
new Chart(document.getElementById("myChart"), {
  type: 'horizontalBar',  
  data: {
    labels: ["Africa", "Asia", "Europe", "Latin America", "North America"],
    datasets: [
      {
        label: "Population (millions)",
        backgroundColor: ["#3e95cd", "#8e5ea2","#3cba9f","#e8c3b9","#c45850"],
        data: [110,200,734,784,433]
      }
    ]
  },
  options: {
    legend: { display: false },
    title: {
      display: true,
      text: 'Predicted world population (millions) in 2050'
    },
    layout: {      
      padding: {
        left: 50
      }
    }
  }
});

</script>


  <input type="hidden" id="tahun" value="<?=$getTahun?>">
    <input type="hidden" id="metode" value="">
    <input type="hidden" id="bulan" value="">

  <div class="col-md-3">
    <div class="card box-shadow-0 animated zoomIn" data-appear="appear" data-animation="zoomIn">
        <div class="card-content">
            <div class="card-body" onclick="openAdd('main/loadUrl/main/dashboard_detail/?metode=1&tahun=<?=$getTahun?>&uki=<?=$unitkerja?>&jenis=all')" style="cursor: pointer; padding: .7em">
                <div class="media">
                    <div class="media-body text-left">
                        <h3 class="orange"><?= $countTender ?></h3>
                        <span>Tender</span>
                    </div>
                    <div class="media-right media-middle">
                        <i class="ft-package success font-large-1 float-right"></i>
                    </div>
                </div>
                <div class="mb-0">
                  <?= '<small style="font-weight:bold">Rp. '.number_format($sumTender,'0',',','.').'</small>'; ?>
                </div>
            </div>
        </div>
    </div>
  </div>

  <div class="col-md-3">
    <div class="card box-shadow-0 animated zoomIn" data-appear="appear" data-animation="zoomIn">
        <div class="card-content">
            <div class="card-body" onclick="openAdd('main/loadUrl/main/dashboard_detail/?metode=7&tahun=<?=$getTahun?>&uki=<?=$unitkerja?>&jenis=all')" style="cursor: pointer; padding: .7em">
                <div class="media">
                    <div class="media-body text-left">
                        <h3 class="orange"><?= $countTenderCepat ?></h3>
                        <span>Tender Cepat</span>
                    </div>
                    <div class="media-right media-middle">
                        <i class="ft-package success font-large-1 float-right"></i>
                    </div>
                </div>
                <div class="mb-0">
                  <?= '<small style="font-weight:bold">Rp. '.number_format($sumTenderCepat,'0',',','.').'</small>'; ?>
                </div>
            </div>
        </div>
    </div>
  </div>

  <div class="col-md-3">
    <div class="card box-shadow-0 animated zoomIn" data-appear="appear" data-animation="zoomIn">
        <div class="card-content">
            <div class="card-body" onclick="openAdd('main/loadUrl/main/dashboard_detail/?metode=3&tahun=<?=$getTahun?>&uki=<?=$unitkerja?>&jenis=all')" style="cursor: pointer; padding: .7em">
                <div class="media">
                    <div class="media-body text-left">
                        <h3 class="orange"><?= $countTenderTerbatas ?></h3>
                        <span>Tender Terbatas</span>
                    </div>
                    <div class="media-right media-middle">
                        <i class="ft-package success font-large-1 float-right"></i>
                    </div>
                </div>
                <div class="mb-0">
                  <?= '<small style="font-weight:bold">Rp. '.number_format($sumTenderTerbatas,'0',',','.').'</small>'; ?>
                </div>
            </div>
        </div>
    </div>
  </div> 

  <div class="col-md-3">
    <div class="card box-shadow-0 animated zoomIn" data-appear="appear" data-animation="zoomIn">
        <div class="card-content">
            <div class="card-body" onclick="openAdd('main/loadUrl/main/dashboard_detail/?metode=10&tahun=<?=$getTahun?>&uki=<?=$unitkerja?>&jenis=all')" style="cursor: pointer; padding: .7em">
                <div class="media">
                    <div class="media-body text-left">
                        <h3 class="orange"><?= $countTenderKualifikasi ?></h3>
                        <span>Tender Kualifikasi</span>
                    </div>
                    <div class="media-right media-middle">
                        <i class="ft-package success font-large-1 float-right"></i>
                    </div>
                </div>
                <div class="mb-0">
                  <?= '<small style="font-weight:bold">Rp. '.number_format($sumTenderKualifikasi,'0',',','.').'</small>'; ?>
                </div>
            </div>
        </div>
    </div>
  </div>

  <div class="col-md-3">
    <div class="card box-shadow-0 animated zoomIn" data-appear="appear" data-animation="zoomIn">
        <div class="card-content">
            <div class="card-body" onclick="openAdd('main/loadUrl/main/dashboard_detail/?metode=5&tahun=<?=$getTahun?>&uki=<?=$unitkerja?>&jenis=all')" style="cursor: pointer; padding: .7em">
                <div class="media">
                    <div class="media-body text-left">
                        <h3 class="orange"><?= $countPenunjukanLangsung ?></h3>
                        <span>Penunjukan Langsung</span>
                    </div>
                    <div class="media-right media-middle">
                        <i class="ft-user-check orange font-large-1 float-right"></i>
                    </div>
                </div>
                <div class="mb-0">
                  <?= '<small style="font-weight:bold">Rp. '.number_format($sumPenunjukanLangsung,'0',',','.').'</small>'; ?>
                </div>
            </div>
        </div>
    </div>
  </div>

  <div class="col-md-3">
    <div class="card box-shadow-0 animated zoomIn" data-appear="appear" data-animation="zoomIn">
        <div class="card-content">
            <div class="card-body" onclick="openAdd('main/loadUrl/main/dashboard_detail/?metode=2&tahun=<?=$getTahun?>&uki=<?=$unitkerja?>&jenis=all')" style="cursor: pointer; padding: .7em">
                <div class="media">
                    <div class="media-body text-left">
                        <h3 class="info"><?= $countPengadaanLangsung ?></h3>
                        <span>Pengadaan Langsung</span>
                    </div>
                    <div class="media-right media-middle">
                        <i class="ft-check-circle info font-large-1 float-right"></i>
                    </div>
                </div>
                <div class="mb-0">
                  <?= '<small style="font-weight:bold">Rp. '.number_format($sumPengadaanLangsung,'0',',','.').'</small>'; ?>
                </div>
            </div>
        </div>
    </div>
  </div>

  <div class="col-md-3">
    <div class="card box-shadow-0 animated zoomIn" data-appear="appear" data-animation="zoomIn">
        <div class="card-content">
            <div class="card-body" onclick="openAdd('main/loadUrl/main/dashboard_detail/?metode=6&tahun=<?=$getTahun?>&uki=<?=$unitkerja?>&jenis=all')" style="cursor: pointer; padding: .7em">
                <div class="media">
                    <div class="media-body text-left">
                        <h3 class="daek"><?= $countPembelianLangsung ?></h3>
                        <span>Pembelian Online</span>
                    </div>
                    <div class="media-right media-middle">
                        <i class="icon-basket-loaded dark font-large-1 float-right"></i>
                    </div>
                </div>
                <div class="mb-0">
                  <?= '<small style="font-weight:bold">Rp. '.number_format($sumPembelianLangsung,'0',',','.').'</small>'; ?>
                </div>
            </div>
        </div>
    </div>
  </div>

  <div class="col-md-3">
    <div class="card box-shadow-0 animated zoomIn" data-appear="appear" data-animation="zoomIn">
        <div class="card-content">
            <div class="card-body" onclick="openAdd('main/loadUrl/main/dashboard_detail/?metode=9&tahun=<?=$getTahun?>&uki=<?=$unitkerja?>&jenis=all')" style="cursor: pointer; padding: .7em">
                <div class="media">
                    <div class="media-body text-left">
                        <h3 class="daek"><?= $countOffline ?></h3>
                        <span>Pembelian Offline</span>
                    </div>
                    <div class="media-right media-middle">
                        <i class="icon-basket-loaded dark font-large-1 float-right"></i>
                    </div>
                </div>
                <div class="mb-0">
                  <?= '<small style="font-weight:bold">Rp. '.number_format($sumOffline,'0',',','.').'</small>'; ?>
                </div>
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
      var url = 'main/loadUrl/main/dashboard_detail_pie/?jenis='+label+'&jumlah='+value+'&tahun='+tahun+'&uki=<?= $unitkerja ?>&type=all';
      openAdd(url);
    }
  };
});
</script>
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

  function getDashboardDetailBarChart(a,b,c,d) {
    // alert(a+'-'+b+'-'+c+'-'+d);
    var url = 'main/loadUrl/main/dashboard_detail_bar/?pengguna='+a+'&total='+b+'&jenis='+c+'&tahun='+d;
    openAdd(url);
  }

});
</script>
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
