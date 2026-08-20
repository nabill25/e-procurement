<?php

/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 *
 */
$this->libsession->cekSession();  // Hanya untuk 3:panitia, 11:pejabat pengadaan

include_once("functions/string.func.php");
include_once("functions/date.func.php");

$unitkerja  = $this->UNIT_KERJA_ID;
$userjabatanpanitia = $this->USER_JABATAN_PANITIA;

$this->load->model("Paketpanitiadash");
$this->load->model("Queryfree");

// Laporan Berdasarkan Metode Pengadaan
$countTender = new Paketpanitiadash();
$sumTender = new Paketpanitiadash();
$countTenderTerbatas = new Paketpanitiadash();
$sumTenderTerbatas = new Paketpanitiadash();
$countTenderCepat = new Paketpanitiadash();
$sumTenderCepat = new Paketpanitiadash();
$countPengadaanLangsung = new Paketpanitiadash();
$sumPengadaanLangsung = new Paketpanitiadash();
$countPenunjukanLangsung = new Paketpanitiadash();
$sumPenunjukanLangsung = new Paketpanitiadash();
$countPembelianLangsung = new Paketpanitiadash();
$sumPembelianLangsung = new Paketpanitiadash();
$countTenderKualifikasi = new Paketpanitiadash();
$sumTenderKualifikasi = new Paketpanitiadash();
$countKompetisi = new Paketpanitiadash();
$sumKompetisi = new Paketpanitiadash();
$countOffline = new Paketpanitiadash();
$sumOffline = new Paketpanitiadash();
$getDataHPS = new Queryfree();
$getDataHPSTotal = new Queryfree();

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

// 1-e-Tender ,7-e-Tender Cepat, 2-Pengadaan Langsung ,5-Penunjukan Langsung Cepat, 6:Pembelian langsung, 8:Kompetisi, 9:Pembelian Langsung Offline
$getTahun = isset($_GET['tahun']) ? $_GET['tahun'] : date('Y');
// $getTahun = $_GET['tahun'];
// echo $getTahun; die;
if ($getTahun != 'all'){
  $tahun = 'Tahun '.$getTahun;

  $countTender = $countTender->getCountByParams(array("A.PAKET_METODE_LELANG_ID" => "1", "A.UNIT_KERJA_ID" => $unitkerja, "A.TAHUN_ANGGARAN" => $getTahun)," AND (A.USER_LOGIN_ID = '".$this->USER_LOGIN_ID."' OR A.USER_TIM_PENGADAAN && ARRAY[".$this->USER_LOGIN_ID."])");
  $sumTender = $sumTender->getSumByParams(array("A.PAKET_METODE_LELANG_ID" => "1", "A.UNIT_KERJA_ID" => $unitkerja, "A.TAHUN_ANGGARAN" => $getTahun)," AND (A.USER_LOGIN_ID = '".$this->USER_LOGIN_ID."' OR A.USER_TIM_PENGADAAN && ARRAY[".$this->USER_LOGIN_ID."])");
  $countTenderTerbatas = $countTenderTerbatas->getCountByParams(array("A.PAKET_METODE_LELANG_ID" => "3", "A.UNIT_KERJA_ID" => $unitkerja, "A.TAHUN_ANGGARAN" => $getTahun)," AND (A.USER_LOGIN_ID = '".$this->USER_LOGIN_ID."' OR A.USER_TIM_PENGADAAN && ARRAY[".$this->USER_LOGIN_ID."])");
  $sumTenderTerbatas = $sumTenderTerbatas->getSumByParams(array("A.PAKET_METODE_LELANG_ID" => "3", "A.UNIT_KERJA_ID" => $unitkerja, "A.TAHUN_ANGGARAN" => $getTahun)," AND (A.USER_LOGIN_ID = '".$this->USER_LOGIN_ID."' OR A.USER_TIM_PENGADAAN && ARRAY[".$this->USER_LOGIN_ID."])");
  $countTenderCepat = $countTenderCepat->getCountByParams(array("A.PAKET_METODE_LELANG_ID" => "7", "A.UNIT_KERJA_ID" => $unitkerja, "A.TAHUN_ANGGARAN" => $getTahun)," AND (A.USER_LOGIN_ID = '".$this->USER_LOGIN_ID."' OR A.USER_TIM_PENGADAAN && ARRAY[".$this->USER_LOGIN_ID."])");
  $sumTenderCepat = $sumTenderCepat->getSumByParams(array("A.PAKET_METODE_LELANG_ID" => "7", "A.UNIT_KERJA_ID" => $unitkerja, "A.TAHUN_ANGGARAN" => $getTahun)," AND (A.USER_LOGIN_ID = '".$this->USER_LOGIN_ID."' OR A.USER_TIM_PENGADAAN && ARRAY[".$this->USER_LOGIN_ID."])");
  $countPengadaanLangsung = $countPengadaanLangsung->getCountByParams(array("A.PAKET_METODE_LELANG_ID" => "2", "A.UNIT_KERJA_ID" => $unitkerja, "A.TAHUN_ANGGARAN" => $getTahun)," AND (A.USER_LOGIN_ID = '".$this->USER_LOGIN_ID."' OR A.USER_TIM_PENGADAAN && ARRAY[".$this->USER_LOGIN_ID."])");
  $sumPengadaanLangsung = $sumPengadaanLangsung->getSumByParams(array("A.PAKET_METODE_LELANG_ID" => "2", "A.UNIT_KERJA_ID" => $unitkerja, "A.TAHUN_ANGGARAN" => $getTahun)," AND (A.USER_LOGIN_ID = '".$this->USER_LOGIN_ID."' OR A.USER_TIM_PENGADAAN && ARRAY[".$this->USER_LOGIN_ID."])");
  $countPenunjukanLangsung = $countPenunjukanLangsung->getCountByParams(array("A.PAKET_METODE_LELANG_ID" => "5", "A.UNIT_KERJA_ID" => $unitkerja, "A.TAHUN_ANGGARAN" => $getTahun)," AND (A.USER_LOGIN_ID = '".$this->USER_LOGIN_ID."' OR A.USER_TIM_PENGADAAN && ARRAY[".$this->USER_LOGIN_ID."])");
  $sumPenunjukanLangsung = $sumPenunjukanLangsung->getSumByParams(array("A.PAKET_METODE_LELANG_ID" => "5", "A.UNIT_KERJA_ID" => $unitkerja, "A.TAHUN_ANGGARAN" => $getTahun)," AND (A.USER_LOGIN_ID = '".$this->USER_LOGIN_ID."' OR A.USER_TIM_PENGADAAN && ARRAY[".$this->USER_LOGIN_ID."])");
  $countPembelianLangsung = $countPembelianLangsung->getCountByParams(array("A.PAKET_METODE_LELANG_ID" => "6", "A.UNIT_KERJA_ID" => $unitkerja, "A.TAHUN_ANGGARAN" => $getTahun)," AND (A.USER_LOGIN_ID = '".$this->USER_LOGIN_ID."' OR A.USER_TIM_PENGADAAN && ARRAY[".$this->USER_LOGIN_ID."])");
  $sumPembelianLangsung = $sumPembelianLangsung->getSumByParams(array("A.PAKET_METODE_LELANG_ID" => "6", "A.UNIT_KERJA_ID" => $unitkerja, "A.TAHUN_ANGGARAN" => $getTahun)," AND (A.USER_LOGIN_ID = '".$this->USER_LOGIN_ID."' OR A.USER_TIM_PENGADAAN && ARRAY[".$this->USER_LOGIN_ID."])");
  $countTenderKualifikasi = $countTenderKualifikasi->getCountByParams(array("A.PAKET_METODE_LELANG_ID" => "10", "A.UNIT_KERJA_ID" => $unitkerja, "TAHUN_ANGGARAN" => $getTahun)," AND (A.USER_LOGIN_ID = '".$this->USER_LOGIN_ID."' OR A.USER_TIM_PENGADAAN && ARRAY[".$this->USER_LOGIN_ID."])");
  $sumTenderKualifikasi = $sumTenderKualifikasi->getSumByParams(array("A.PAKET_METODE_LELANG_ID" => "10", "A.UNIT_KERJA_ID" => $unitkerja, "TAHUN_ANGGARAN" => $getTahun)," AND (A.USER_LOGIN_ID = '".$this->USER_LOGIN_ID."' OR A.USER_TIM_PENGADAAN && ARRAY[".$this->USER_LOGIN_ID."])");
  $countKompetisi = $countKompetisi->getCountByParams(array("A.PAKET_METODE_LELANG_ID" => "8", "A.UNIT_KERJA_ID" => $unitkerja, "A.TAHUN_ANGGARAN" => $getTahun)," AND (A.USER_LOGIN_ID = '".$this->USER_LOGIN_ID."' OR A.USER_TIM_PENGADAAN && ARRAY[".$this->USER_LOGIN_ID."])");
  $sumKompetisi = $sumKompetisi->getSumByParams(array("A.PAKET_METODE_LELANG_ID" => "8", "A.UNIT_KERJA_ID" => $unitkerja, "A.TAHUN_ANGGARAN" => $getTahun)," AND (A.USER_LOGIN_ID = '".$this->USER_LOGIN_ID."' OR A.USER_TIM_PENGADAAN && ARRAY[".$this->USER_LOGIN_ID."])");
  $countOffline = $countOffline->getCountByParams(array("A.PAKET_METODE_LELANG_ID" => "9", "A.UNIT_KERJA_ID" => $unitkerja, "A.TAHUN_ANGGARAN" => $getTahun)," AND (A.USER_LOGIN_ID = '".$this->USER_LOGIN_ID."' OR A.USER_TIM_PENGADAAN && ARRAY[".$this->USER_LOGIN_ID."])");
  $sumOffline = $sumOffline->getSumByParams(array("A.PAKET_METODE_LELANG_ID" => "9", "A.UNIT_KERJA_ID" => $unitkerja, "A.TAHUN_ANGGARAN" => $getTahun)," AND (A.USER_LOGIN_ID = '".$this->USER_LOGIN_ID."' OR A.USER_TIM_PENGADAAN && ARRAY[".$this->USER_LOGIN_ID."])");

  $getDataHPS->selectByParams("SELECT * FROM view_dash_efesiensi WHERE unit_kerja_id='".$unitkerja."' AND tahun_anggaran = '".$getTahun."' AND (user_login_id = ".$this->USER_LOGIN_ID." OR user_tim_pengadaan && ARRAY[".$this->USER_LOGIN_ID."] )");
  // echo $getDataHPS->query;
    $nilaiHps = 0;
    $nilaiNegosiasi = 0;
    while($getDataHPS->nextRow())
    {
      $nilaiHps += $getDataHPS->getField('nilai');
      $nilaiNegosiasi += $getDataHPS->getField('harga_negosiasi');

    }

} else {
  $tahun = '';
  $countTender = $countTender->getCountByParams(array("A.PAKET_METODE_LELANG_ID" => "1", "A.UNIT_KERJA_ID" => $unitkerja)," AND (A.USER_LOGIN_ID = '".$this->USER_LOGIN_ID."' OR A.USER_TIM_PENGADAAN && ARRAY[".$this->USER_LOGIN_ID."])");
  $sumTender = $sumTender->getSumByParams(array("A.PAKET_METODE_LELANG_ID" => "1", "A.UNIT_KERJA_ID" => $unitkerja)," AND (A.USER_LOGIN_ID = '".$this->USER_LOGIN_ID."' OR A.USER_TIM_PENGADAAN && ARRAY[".$this->USER_LOGIN_ID."])");
  $countTenderTerbatas = $countTenderTerbatas->getCountByParams(array("A.PAKET_METODE_LELANG_ID" => "3", "A.UNIT_KERJA_ID" => $unitkerja)," AND (A.USER_LOGIN_ID = '".$this->USER_LOGIN_ID."' OR A.USER_TIM_PENGADAAN && ARRAY[".$this->USER_LOGIN_ID."])");
  $sumTenderTerbatas = $sumTenderTerbatas->getSumByParams(array("A.PAKET_METODE_LELANG_ID" => "3", "A.UNIT_KERJA_ID" => $unitkerja)," AND (A.USER_LOGIN_ID = '".$this->USER_LOGIN_ID."' OR A.USER_TIM_PENGADAAN && ARRAY[".$this->USER_LOGIN_ID."])");
  $countTenderCepat = $countTenderCepat->getCountByParams(array("A.PAKET_METODE_LELANG_ID" => "7", "A.UNIT_KERJA_ID" => $unitkerja)," AND (A.USER_LOGIN_ID = '".$this->USER_LOGIN_ID."' OR A.USER_TIM_PENGADAAN && ARRAY[".$this->USER_LOGIN_ID."])");
  $sumTenderCepat = $sumTenderCepat->getSumByParams(array("A.PAKET_METODE_LELANG_ID" => "7", "A.UNIT_KERJA_ID" => $unitkerja)," AND (A.USER_LOGIN_ID = '".$this->USER_LOGIN_ID."' OR A.USER_TIM_PENGADAAN && ARRAY[".$this->USER_LOGIN_ID."])");
  $countPengadaanLangsung = $countPengadaanLangsung->getCountByParams(array("A.PAKET_METODE_LELANG_ID" => "2", "A.UNIT_KERJA_ID" => $unitkerja)," AND (A.USER_LOGIN_ID = '".$this->USER_LOGIN_ID."' OR A.USER_TIM_PENGADAAN && ARRAY[".$this->USER_LOGIN_ID."])");
  $sumPengadaanLangsung = $sumPengadaanLangsung->getSumByParams(array("A.PAKET_METODE_LELANG_ID" => "2", "A.UNIT_KERJA_ID" => $unitkerja)," AND (A.USER_LOGIN_ID = '".$this->USER_LOGIN_ID."' OR A.USER_TIM_PENGADAAN && ARRAY[".$this->USER_LOGIN_ID."])");
  $countPenunjukanLangsung = $countPenunjukanLangsung->getCountByParams(array("A.PAKET_METODE_LELANG_ID" => "5", "A.UNIT_KERJA_ID" => $unitkerja)," AND (A.USER_LOGIN_ID = '".$this->USER_LOGIN_ID."' OR A.USER_TIM_PENGADAAN && ARRAY[".$this->USER_LOGIN_ID."])");
  $sumPenunjukanLangsung = $sumPenunjukanLangsung->getSumByParams(array("A.PAKET_METODE_LELANG_ID" => "5", "A.UNIT_KERJA_ID" => $unitkerja)," AND (A.USER_LOGIN_ID = '".$this->USER_LOGIN_ID."' OR A.USER_TIM_PENGADAAN && ARRAY[".$this->USER_LOGIN_ID."])");
  $countPembelianLangsung = $countPembelianLangsung->getCountByParams(array("A.PAKET_METODE_LELANG_ID" => "6", "A.UNIT_KERJA_ID" => $unitkerja)," AND (A.USER_LOGIN_ID = '".$this->USER_LOGIN_ID."' OR A.USER_TIM_PENGADAAN && ARRAY[".$this->USER_LOGIN_ID."])");
  $sumPembelianLangsung = $sumPembelianLangsung->getSumByParams(array("A.PAKET_METODE_LELANG_ID" => "6", "A.UNIT_KERJA_ID" => $unitkerja)," AND (A.USER_LOGIN_ID = '".$this->USER_LOGIN_ID."' OR A.USER_TIM_PENGADAAN && ARRAY[".$this->USER_LOGIN_ID."])");
  $countTenderKualifikasi = $countTenderKualifikasi->getCountByParams(array("A.PAKET_METODE_LELANG_ID" => "10", "A.UNIT_KERJA_ID" => $unitkerja)," AND (A.USER_LOGIN_ID = '".$this->USER_LOGIN_ID."' OR A.USER_TIM_PENGADAAN && ARRAY[".$this->USER_LOGIN_ID."])");
  $sumTenderKualifikasi = $sumTenderKualifikasi->getSumByParams(array("A.PAKET_METODE_LELANG_ID" => "10", "A.UNIT_KERJA_ID" => $unitkerja)," AND (A.USER_LOGIN_ID = '".$this->USER_LOGIN_ID."' OR A.USER_TIM_PENGADAAN && ARRAY[".$this->USER_LOGIN_ID."])");
  $countKompetisi = $countKompetisi->getCountByParams(array("A.PAKET_METODE_LELANG_ID" => "8", "A.UNIT_KERJA_ID" => $unitkerja)," AND (A.USER_LOGIN_ID = '".$this->USER_LOGIN_ID."' OR A.USER_TIM_PENGADAAN && ARRAY[".$this->USER_LOGIN_ID."])");
  $sumKompetisi = $sumKompetisi->getSumByParams(array("A.PAKET_METODE_LELANG_ID" => "8", "A.UNIT_KERJA_ID" => $unitkerja)," AND (A.USER_LOGIN_ID = '".$this->USER_LOGIN_ID."' OR A.USER_TIM_PENGADAAN && ARRAY[".$this->USER_LOGIN_ID."])");
  $countOffline = $countOffline->getCountByParams(array("A.PAKET_METODE_LELANG_ID" => "9", "A.UNIT_KERJA_ID" => $unitkerja)," AND (A.USER_LOGIN_ID = '".$this->USER_LOGIN_ID."' OR A.USER_TIM_PENGADAAN && ARRAY[".$this->USER_LOGIN_ID."])");
  $sumOffline = $sumOffline->getSumByParams(array("A.PAKET_METODE_LELANG_ID" => "9", "A.UNIT_KERJA_ID" => $unitkerja)," AND (A.USER_LOGIN_ID = '".$this->USER_LOGIN_ID."' OR A.USER_TIM_PENGADAAN && ARRAY[".$this->USER_LOGIN_ID."])");

  $getDataHPS->selectByParams("SELECT * FROM view_dash_efesiensi WHERE unit_kerja_id='".$unitkerja."' AND (user_login_id = ".$this->USER_LOGIN_ID." OR user_tim_pengadaan && ARRAY[".$this->USER_LOGIN_ID."]) ");
    $nilaiHps = 0;
    $nilaiNegosiasi = 0;
    while($getDataHPS->nextRow())
    {
      $nilaiHps += $getDataHPS->getField('nilai');
      $nilaiNegosiasi += $getDataHPS->getField('harga_negosiasi');

    }
  // echo $getDataHPS->query; die;
}
// End Laporan Berdasarkan Metode Pengadaan
// echo "<pre>"; print_r($pieValue); die();
?>
 <!-- <script src="https://unpkg.com/gauge-chart@latest/dist/bundle.js"></script> -->
<script src="<?=base_url()?>assets/new/vendors/js/charts/chart.min.js"></script>
<!-- https://codepen.io/b1tn3r/pen/erLqbQ -->

<script type="text/javascript">

// $(document).ready(function() {
  // window.onload=function(){
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
          $paketChart = new Paketpanitiadash();
          for ($i=0; $i < 4 ; $i++) {
            $color = array('#76b82a','#ea5b1b','#F98E76','#ffcc33');
            // $color = array('#76b82a','#ea5b1b','#F98E76','#ffcc33','#010066');
            // $label = array('Tender','Tender_Cepat','Penunjukan_Langsung','Pengadaan_Langsung');
            $label = array('Tender','Tender Kualifikasi','Penunjukan_Langsung','Pengadaan_Langsung');
          ?>
          {
              label: "<?= str_replace("_", " ", $label[$i]) ?>",
              data: [
              <?php
              $datanya = '';
              $paketChart->getDashboard($unitkerja,$getTahun,$this->USER_LOGIN_ID);
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
  // });

});

function getDashboardDetail(a,b) {
  var bulan   = a;
  var metode  = b;
  var tahun   = $('#tahun').val();
  var url = 'main/loadUrl/main/dashboard_detail/?metode='+metode+'&bulan='+bulan+'&tahun='+tahun+'&uid='+<?= $this->USER_LOGIN_ID ?>+'&uki='+<?=$unitkerja?>+'&uid=<?=$this->USER_LOGIN_ID?>&jenis=panitia';
  openAdd(url);
  // alert(a+'-'+tahun+'-'+b);
}

</script>

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
    margin: 10px 0;
    text-align: center;
}
</style>

<div class="row">
  <div class="form-group col-md-3">
    <!-- <label>Pilih Tahun</label> -->
    <select class="form-control" id="setyear" onChange="return window.location = $(this).val()">
      <?php
      $selected = '';
      $url = base_url('main/index/dashboard?tahun=');
      $kurangdari = date('Y') - 5;
            echo '<option value="'.$url.'all">-- Pilih Tahun --</option>';
      for ($i= date('Y')+1; $i > $kurangdari   ; $i--) {
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
        <h5><b>Dashboard Pelaksana Pengadaan
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
  <div class="col-md-8"> </div>
  <div class="col-md-4">
      <div class="card">
        <div class="row">
            <div class="col-md-4 border-right">
                <i class="fa fa-question-circle" data-toggle="tooltip" title="Total Paket <?php if ($getTahun != 'all'){ echo 'Tahun Anggaran '.$getTahun; } ?>" data-placement="top" aria-hidden="true" style="position:absolute;right:10px;padding-top:10px;color:black"></i>
                <div class="description-block">
                    <div class="description-header" style="font-size:2rem;"><?= $countTender + $countTenderCepat + $countTenderTerbatas + $countTenderKualifikasi + $countPenunjukanLangsung + $countPengadaanLangsung ?></div>
                    <span class="description-text">TOTAL PAKET</span>
                </div>
            </div>
            <div class="col-md-8">
                <i class="fa fa-question-circle" data-toggle="tooltip" title="Total Harga Perkiraan <?php if ($getTahun != 'all'){ echo 'Tahun Anggaran '.$getTahun; } ?>" data-placement="top" aria-hidden="true" style="position:absolute;right:25px;padding-top:10px;color:black"></i>
                <div class="description-block">
                    <div class="description-header" style="font-size:2rem;" data-toggle="tooltip" data-placement="top" data-original-title="">Rp <?php echo numberToSimbol(round($nilaiHps)) ?></div>
                    <span class="description-text">TOTAL HARGA PERKIRAAN</span>
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
      <div class="card box-shadow-0 animated zoomIn wfont" data-appear="appear" data-animation="zoomIn" style="background-color: #336B87;">
        <div class="card-content">
            <div class="card-body" onclick="openAdd('main/loadUrl/main/dashboard_detail/?metode=1&tahun=<?=$getTahun?>&uki=<?=$unitkerja?>&uid=<?=$this->USER_LOGIN_ID?>&jenis=panitia')" style="cursor: pointer">
                <div class="media">
                    <div class="media-body text-center">
                        <span style="margin-top: 15%;">Tender</span>
                        <h2 class="wfont mt-2"><b>Rp. <?php echo numberToSimbol(round($sumTender)) ?> </b></h2>
                        <!-- <small style="font-size: .8em; top: -12px; position: relative;">Harga Perkiraan</small> -->
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
      <div class="card box-shadow-0 animated zoomIn wfont" data-appear="appear" data-animation="zoomIn" style="background-color: #BA5536;">
        <div class="card-content">
            <div class="card-body" onclick="openAdd('main/loadUrl/main/dashboard_detail/?metode=7&tahun=<?=$getTahun?>&uki=<?=$unitkerja?>&uid=<?=$this->USER_LOGIN_ID?>&jenis=panitia')" style="cursor: pointer">
                <div class="media">
                    <div class="media-body text-center">
                        <span style="margin-top: 15%;">Tender Cepat</span>
                        <h2 class="wfont mt-2"><b>Rp. <?php echo numberToSimbol(round($sumTenderCepat)) ?> </b></h2>
                        <!-- <small style="font-size: .8em; top: -12px; position: relative;">Harga Perkiraan</small> -->
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
      <div class="card box-shadow-0 animated zoomIn wfont" data-appear="appear" data-animation="zoomIn" style="background-color: #07575B;">
        <div class="card-content">
            <div class="card-body" onclick="openAdd('main/loadUrl/main/dashboard_detail/?metode=3&tahun=<?=$getTahun?>&uki=<?=$unitkerja?>&uid=<?=$this->USER_LOGIN_ID?>&jenis=panitia')" style="cursor: pointer">
                <div class="media">
                    <div class="media-body text-center">
                        <span style="margin-top: 15%;">Tender Terbatas</span>
                        <h2 class="wfont mt-2"><b>Rp. <?php echo numberToSimbol(round($sumTenderTerbatas)) ?> </b></h2>
                        <!-- <small style="font-size: .8em; top: -12px; position: relative;">Harga Perkiraan</small> -->
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
      <div class="card box-shadow-0 animated zoomIn wfont" data-appear="appear" data-animation="zoomIn" style="background-color: #FB6542;">
        <div class="card-content">
            <div class="card-body" onclick="openAdd('main/loadUrl/main/dashboard_detail/?metode=10&tahun=<?=$getTahun?>&uki=<?=$unitkerja?>&uid=<?=$this->USER_LOGIN_ID?>&jenis=panitia')" style="cursor: pointer">
                <div class="media">
                    <div class="media-body text-center">
                        <span style="margin-top: 15%;">Tender Kualifikasi</span>
                        <h2 class="wfont mt-2"><b>Rp. <?php echo numberToSimbol(round($sumTenderKualifikasi)) ?> </b></h2>
                        <!-- <small style="font-size: .8em; top: -12px; position: relative;">Harga Perkiraan</small> -->
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

    <div class="col-md-2">
      <div class="card box-shadow-0 animated zoomIn wfont" data-appear="appear" data-animation="zoomIn" style="background-color: #4CB5F5;">
        <div class="card-content">
            <div class="card-body" onclick="openAdd('main/loadUrl/main/dashboard_detail/?metode=5&tahun=<?=$getTahun?>&uki=<?=$unitkerja?>&uid=<?=$this->USER_LOGIN_ID?>&jenis=panitia')" style="cursor: pointer">
                <div class="media">
                    <div class="media-body text-center">
                        <span style="margin-top: 15%;">Penunjukan Langsung</span>
                        <h2 class="wfont mt-2"><b>Rp. <?php echo numberToSimbol(round($sumPenunjukanLangsung)) ?> </b></h2>
                        <!-- <small style="font-size: .8em; top: -12px; position: relative;">Harga Perkiraan</small> -->
                    </div>
                    <div class="media-right media-middle">
                        <i class="fa fa-question-circle dark" data-toggle="tooltip" data-placement="top" title="Total Penunjukan Langsung" style="font-size:1.3em !important; cursor: pointer;"></i>
                    </div>
                </div>
                <div class="mt-1 text-center">
                  <?= '<small style="font-weight:bold">'.$countPenunjukanLangsung.' Paket</small>'; ?> <i class="fa fa-arrow-circle-right"></i>
                </div>
            </div>
        </div>
      </div>
    </div>

    <div class="col-md-2">
      <div class="card box-shadow-0 animated zoomIn wfont" data-appear="appear" data-animation="zoomIn" style="background-color: #20948B;">
        <div class="card-content">
            <div class="card-body" onclick="openAdd('main/loadUrl/main/dashboard_detail/?metode=2&tahun=<?=$getTahun?>&uki=<?=$unitkerja?>&uid=<?=$this->USER_LOGIN_ID?>&jenis=panitia')" style="cursor: pointer">
                <div class="media">
                    <div class="media-body text-center">
                        <span style="margin-top: 15%;">Pengadaan Langsung</span>
                        <h2 class="wfont mt-2"><b>Rp. <?php echo numberToSimbol(round($sumPengadaanLangsung)) ?> </b></h2>
                        <!-- <small style="font-size: .8em; top: -12px; position: relative;">Harga Perkiraan</small> -->
                    </div>
                    <div class="media-right media-middle">
                        <i class="fa fa-question-circle dark" data-toggle="tooltip" data-placement="top" title="Total Pengadaan Langsung" style="font-size:1.3em !important; cursor: pointer;"></i>
                    </div>
                </div>
                <div class="mt-1 text-center">
                  <?= '<small style="font-weight:bold">'.$countPengadaanLangsung.' Paket</small>'; ?> <i class="fa fa-arrow-circle-right"></i>
                </div>
            </div>
        </div>
      </div>
    </div>

<!--
----------------------------------------------------------------------------------------------------------------
  LAPORAN BERDASARKAN EFESIENSI
----------------------------------------------------------------------------------------------------------------
-->
<script type="text/javascript">
function setHPSVal(a) {
  var a = parseInt(a);
  $.ajax({
     type: "POST",
     dataType: "html",
     url: "dashboard_json/setHPSVal/?unitkerja=<?= $unitkerja ?>&userloginid=<?= $this->USER_LOGIN_ID ?>&tahun=<?= $getTahun ?>&bulan="+a,
     // data: "prov="+a,
     success: function(msg){
      var msg = JSON.parse(msg);
      $('#req-set-nilai').html(msg.message);
    }
  });
}

</script>
<div class="col-md-5">
  <div class="card">
    <div class="card-content collapse show">
      <div class="card-header card-head-inverse bg-primary">
        <h4 class="card-title text-white" style="font-size:.9em !important">Efesiensi Pengadaan Berdasarkan Harga Perkiraan <?= $tahun ?> </h4>
        <a class="heading-elements-toggle"><i class="fa fa-ellipsis-v font-medium-3"></i></a>
        <div class="heading-elements">
            <ul class="list-inline mb-0">
              <li><a data-action="expand"><i class="ft-maximize"></i></a></li>
              <!-- <li><a data-action="close"><i class="ft-x"></i></a></li> -->
            </ul>
        </div>
      </div>
      <div class="card-content collapse show border-info border-darken-2">
        <div class="card-body">
          <?php
          $getMonth = new Queryfree();
          $getMonth->selectByParams("SELECT * FROM MONTH");
          ?>
          <select class="form-control mt-2" onChange="return setHPSVal($(this).val())">
            <?php
            echo '<option value="101">-- Pilih Bulan --</option>';
            while($getMonth->nextRow())
            {
              echo '<option value="'.$getMonth->getField('month_angka').'">'.$getMonth->getField('month_ina').'</option>';
            } ?>
          </select>
          <div class="m-1">
            <?php
            $nilaiEfesiensi = $nilaiHps - $nilaiNegosiasi;
            // $nilaiEfesiensi = $nilaiHps - 22660000000;
            if ($nilaiEfesiensi > 0) {
              $persentaseEfesiensi = round($nilaiEfesiensi/$nilaiHps * 100,2).' %';
              $backColor = ' background-color:#967adc;';
              $ketEfesiensi1 = 'Efesien';
              $ketEfesiensi2 = '<span class="white darken-1 block"><i class="ft-arrow-down white" ></i> '.$persentaseEfesiensi.' dari Harga Perkiraan</span>';
            } else {
              $persentaseEfesiensi = 0;
              $backColor = ' background-color:#da4453;';
              $ketEfesiensi1 = 'Tidak Efesien';
              $ketEfesiensi2 = '<span class="white darken-1 block"><i class="ft-arrow-up white" ></i>  '.$persentaseEfesiensi.' dari Harga Perkiraan</span>';
            }
             ?>
            <div id="req-set-nilai">
                <table class="table table-bordered mb-3">
                  <tr>
                    <td>
                      <div class="float-center pl-2">
                          <span class="grey darken-1 block">Harga Perkiraan</span>
                          <span class="font-large-2 line-height-1 text-bold-300"><?= number_format($nilaiHps,'0',',','.') ?></span>
                      </div>
                    </td>
                  </tr>
                  <tr>
                    <td>
                      <div class="float-center pl-2">
                          <span class="grey darken-1 block">Harga Final/Akhir</span>
                          <span class="font-large-2 line-height-1 text-bold-300"><?= number_format($nilaiNegosiasi,'0',',','.') ?></span>
                      </div>
                    </td>
                  </tr>
                  <tr style="<?= $backColor ?>">
                    <td>
                      <div class="float-center pl-2">
                          <span class="white darken-1 block" style="color:#fff"><?= $ketEfesiensi1 ?></span>
                          <span class="font-large-1 line-height-1 text-bold-300" style="color:#fff"><?= number_format($nilaiEfesiensi,'0',',','.') ?></span>
                          <?= $ketEfesiensi2 ?>
                      </div>
                    </td>
                  </tr>
                  <!-- <small><b>Dalam hitungan rupiah</b></small> -->
                </table>
            </div>
          </div>
        </div>
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
$getDataChartPie = new Paketpanitiadash();
$getDataChartPie->getDashboardPie($unitkerja,$this->USER_LOGIN_ID,$getTahun);

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
      $pieLabel = array('0' => 'Barang', '1' => 'Jasa Konsultansi', '2' => 'Jasa Lainnya', '3' => 'Katalog', '4' => 'Pekerjaan Konstruksi');
      $pieValue = array('0' => 0, '1' => 0, '2' => 0, '3' => 0, '4' => 0);
    }
} else {
    $pieLabel = array('0' => 'Barang', '1' => 'Jasa Konsultansi', '2' => 'Jasa Lainnya', '3' => 'Katalog', '4' => 'Pekerjaan Konstruksi');
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

// pengadaanColors=["#009ab0","#82efee","#cfbca6","#128a08","#de9932","#be6d40","#f0d09f","#266caa","#182d48"];

          fill: true,
          backgroundColor: ['#009ab0','#82efee','#cfbca6','#128a08','de9932'],
          data: [
          <?php
           foreach ($pieValue as $valpieValue):
            echo '"'.$valpieValue.'",';
           endforeach ?>
           ],
          // Notice the borderColor
          borderColor:  ['#009ab0','#82efee','#cfbca6','#128a08','de9932'],
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
      var url = 'main/loadUrl/main/dashboard_detail_pie/?jenis='+label+'&jumlah='+value+'&tahun='+tahun+'&uki=<?= $unitkerja ?>'+'&uid='+<?= $this->USER_LOGIN_ID ?>+'&type=panitia';
      openAdd(url);
    }
  };
});
</script>
 <div class="col-md-7">
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

<!--
----------------------------------------------------------------------------------------------------------------
  LAPORAN BERDASARKAN LAMA PROSES PAKET
----------------------------------------------------------------------------------------------------------------
-->
<link rel="stylesheet" type="text/css" href="lib/DataTables-1.10.7/media/css/jquery.dataTables.css">
<link rel="stylesheet" type="text/css" href="lib/DataTables-1.10.7/extensions/Responsive/css/dataTables.responsive.css">
<link rel="stylesheet" type="text/css" href="lib/DataTables-1.10.7/examples/resources/syntax/shCore.css">
<link rel="stylesheet" type="text/css" href="lib/DataTables-1.10.7/examples/resources/demo.css">
<script type="text/javascript" language="javascript" src="lib/DataTables-1.10.7/media/js/jquery.dataTables.js"></script>
<script type="text/javascript" language="javascript" src="lib/DataTables-1.10.7/extensions/Responsive/js/dataTables.responsive.js"></script>
<script type="text/javascript" language="javascript" src="lib/DataTables-1.10.7/examples/resources/syntax/shCore.js"></script>
<script type="text/javascript" language="javascript" src="lib/DataTables-1.10.7/examples/resources/demo.js"></script>
<script type="text/javascript" language="javascript" class="init">
  $(document).ready(function() {
    $('#prosesDash').DataTable({
      "iDisplayLength": 5,
      "aaSorting": [[0, 'desc']],
      "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
    });
  });
</script>
<style>
#prosesDash_length { display: none;}
</style>
<div class="col-md-12">
  <div class="card">
    <div class="card-header card-head-inverse bg-primary">
     <h4 class="card-title text-white" style="font-size:.9em !important">Lama Proses Pengadaan Berdasarkan Hari <?= $tahun ?></h4>
     <a class="heading-elements-toggle"><i class="fa fa-ellipsis-v font-medium-3"></i></a>
      <div class="heading-elements">
        <ul class="list-inline mb-0">
          <li><a data-action="expand"><i class="ft-maximize"></i></a></li>
          <!-- <li><a data-action="close"><i class="ft-x"></i></a></li> -->
        </ul>
      </div>
    </div>
    <div class="card-content collapse show border-info border-darken-2">
      <div class="text-center">
        <div class="col-md-12" style="margin:1% 0">
          <table id="prosesDash" class="border-double table mb-0 table-bordered" style="width: 100%">
            <thead>
              <tr>
                <th class="text-left">Pengadaan</th>
                <th width="10%">Planning</th>
                <th width="10%">Pemilihan</th>
                <th width="10%">Kontrak</th>
                <th width="10%">Total</th>
              </tr>
            </thead>
            <tbody>
              <?php
              $getDataProses = new Queryfree();
              if ($getTahun != 'all') {
                $getDataProses->selectByParams("SELECT * from view_dash_lama_proses_new2 WHERE TAHUN_ANGGARAN = '$getTahun' and (user_login_id=$this->USER_LOGIN_ID OR user_tim_pengadaan && ARRAY[$this->USER_LOGIN_ID]) ");
              } else {
                $getDataProses->selectByParams("SELECT * from view_dash_lama_proses_new2 WHERE (user_login_id=$this->USER_LOGIN_ID OR user_tim_pengadaan && ARRAY[$this->USER_LOGIN_ID]) ");
                // echo $getDataProses->query;
              }
              while($getDataProses->nextRow())
              {
                $total_hari = $getDataProses->getField('lama_permohonan') + $getDataProses->getField('lama_sourcing') + $getDataProses->getField('lama_kontrak') .' Hari';

                ?>
              <tr>
                <td class="text-left"><?= $getDataProses->getField('NAMA'); ?></td>
                <td>
                  <?php
                  if($getDataProses->getField('lama_permohonan') == '00:00:00' || $getDataProses->getField('lama_permohonan') == '') {
                    echo "-";
                  } else {
                    echo $getDataProses->getField('lama_permohonan').' Hari';
                  }
                  ?>
                </td>
                <td>
                  <?php
                  if($getDataProses->getField('lama_sourcing') == '00:00:00' || $getDataProses->getField('lama_sourcing') == '') {
                    echo "-";
                  } else {
                    echo $getDataProses->getField('lama_sourcing').' Hari';
                  }
                  ?>
                </td>
                <td>
                  <?php
                  if($getDataProses->getField('lama_kontrak') == '00:00:00' || $getDataProses->getField('lama_kontrak') == '') {
                    echo "-";
                  } else {
                    echo $getDataProses->getField('lama_kontrak').' Hari';
                  }
                  ?>
                </td>
                <td>
                    <?php
                        echo $total_hari;
                    ?>
                </td>
              </tr>
              <?php
              } ?>
            </tbody>
          </table>
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
$getDataChartGauge = new Paketpanitiadash();
$getDataChartGauge->getDashboardGauge($unitkerja,$this->USER_LOGIN_ID,$getTahun);
$getDataChartGauge->firstRow();
$totalPaketGauge = $getDataChartGauge->getField('TOTAL_PAKET');
$totalPaketProsesGauge = $getDataChartGauge->getField('TOTAL_PAKET_PROSES');
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
    arcColors: ["#007f5f","#eeef20","#c9184a"],
    arcDelimiters: [30,60],
    rangeLabel: ["0","<?= $totalPaketGauge ?>"],
    centralLabel: '<?= $totalPaketProsesGauge ?>',
  }
    if ($totalPaketProsesGauge > 0) {
        $persen = $totalPaketProsesGauge / $totalPaketGauge * 100;
    } else {
        $persen = 0;
    }
  // Drawing and updating the chart
  // GaugeChart.gaugeChart(element, 533, options).updateNeedle(<?= $totalPaketProsesGauge ?>0);
  GaugeChart.gaugeChart(element, 533, options).updateNeedle(<?= $persen ?>);

  document.getElementById("gaugeArea").onclick = function (evt) {
    var tahun   = $('#tahun').val();
    var url = 'main/loadUrl/main/dashboard_detail_gauge/?tahun='+tahun+'&uki=<?= $unitkerja ?>'+'&uid=<?= $this->USER_LOGIN_ID ?>';
    openAdd(url);
  };
});
</script>
  <div class="col-md-5">
    <div class="card">
      <div class="card-header card-head-inverse bg-primary">
         <h4 class="card-title text-white" style="font-size:.9em !important">Laporan Berdasarkan Beban Kerja Paket <?= $tahun ?></h4>
         <a class="heading-elements-toggle"><i class="fa fa-ellipsis-v font-medium-3"></i></a>
          <div class="heading-elements">
            <ul class="list-inline mb-0">
              <li><a data-action="expand"><i class="ft-maximize"></i></a></li>
              <!-- <li><a data-action="close"><i class="ft-x"></i></a></li> -->
            </ul>
          </div>
      </div>
      <div class="card-content collapse show text-center border-info border-darken-2" style="padding-bottom: 33px">
          <div id="gaugeArea" style="margin-bottom: -50px"></div>
        <small><b>Jumlah Paket Yang Sedang Diproses</b></small>
      </div>
    </div>
  </div>


  <!--
  ----------------------------------------------------------------------------------------------------------------
    LAPORAN BERDASARKAN PENYEDIA
  ----------------------------------------------------------------------------------------------------------------
  -->
  <script type="text/javascript" language="javascript" class="init">
    $(document).ready(function() {
      $('#penyediaDash').DataTable({
        "iDisplayLength": 6,
        "aaSorting": [[2, 'desc']],
        "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
      });
    });
  </script>
  <style>
  #penyediaDash_length { display: none;}
  </style>
  <div class="col-md-7">
    <div class="card">
      <div class="card-header card-head-inverse bg-primary">
         <h4 class="card-title text-white" style="font-size:.9em !important">Laporan Penyedia Ikut Tender Keseluruhan</h4>
         <a class="heading-elements-toggle"><i class="fa fa-ellipsis-v font-medium-3"></i></a>
          <div class="heading-elements">
            <ul class="list-inline mb-0">
              <li><a data-action="expand"><i class="ft-maximize"></i></a></li>
              <!-- <li><a data-action="close"><i class="ft-x"></i></a></li> -->
            </ul>
          </div>
      </div>
      <div class="card-content collapse show border-info border-darken-2">
        <div class="text-center">
          <div class="col-md-12" style="margin:1% 0">
            <table id="penyediaDash" class="border-double table mb-0 table-bordered" style="width: 100%">
              <thead>
                <tr>
                  <th class="text-left">Penyedia</th>
                  <th width="10%">Daftar</th>
                  <th width="10%">Menang</th>
                </tr>
              </thead>
              <tbody>
                <?php
                $getDataPenyediaTender = new Queryfree();
                $getDataPenyediaTender->selectByParams("SELECT a.*,
                                                        c.nama || '. ' || b.nama penyedia
                                                        from (
                                                        	select count(a.rekanan_id) total_ikut_pengadaan,
                                                        	(select count(b.rekanan_id) total_jadi_pemenang from paket_pemenang b
                                                        	 where b.rekanan_id=a.rekanan_id and peringkat='1' and publish='1' group by b.rekanan_id),
                                                        	a.rekanan_id
                                                        	from paket_rekanan a
                                                        	group by a.rekanan_id
                                                        ) a
                                                        join rekanan b on a.rekanan_id=b.rekanan_id
                                                        join rekanan_tipe c on b.rekanan_tipe_id=c.rekanan_tipe_id
                                                        ");
                // $getDataPenyediaTender->firstRow();
                while($getDataPenyediaTender->nextRow())
                { ?>
                <tr>
                  <td class="text-left"><?= $getDataPenyediaTender->getField('penyedia'); ?></td>
                  <td>
                    <?php  if($getDataPenyediaTender->getField('total_ikut_pengadaan') > 0 ) { echo $getDataPenyediaTender->getField('total_ikut_pengadaan').' kali'; } else { echo '0'; } ?>
                  </td>
                  <td>
                    <?php  if($getDataPenyediaTender->getField('total_jadi_pemenang') > 0 ) { echo $getDataPenyediaTender->getField('total_jadi_pemenang').' kali'; } else { echo '0'; } ?>
                  </td>
                </tr>
                <?php
                } ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
