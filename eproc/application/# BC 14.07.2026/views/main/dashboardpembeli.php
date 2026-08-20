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

$this->load->model(array("Paketpanitiadash","Dashpaket"));
$this->load->model("Queryfree");

// Laporan Berdasarkan Metode Pengadaan
$countPembelianLangsung = new Paketpanitiadash();
$sumPembelianLangsung = new Paketpanitiadash();
$countKompetisi = new Paketpanitiadash();
$sumKompetisi = new Paketpanitiadash();
$countOffline = new Paketpanitiadash();
$sumOffline = new Paketpanitiadash();
$countPemerintah = new Paketpanitiadash();
$sumPemerintah = new Paketpanitiadash();
$getDataHPS = new Queryfree();

// 1-e-Tender ,7-e-Tender Cepat, 2-Pengadaan Langsung ,5-Penunjukan Langsung Cepat, 6:Pembelian langsung, 8:Kompetisi, 9:Pembelian Langsung Offline
$getTahun = isset($_GET['tahun']) ? $_GET['tahun'] : date('Y');
// $getTahun = $_GET['tahun'];
if ($getTahun != 'all'){
  $tahun = 'Tahun '.$getTahun;

  $countPembelianLangsung = $countPembelianLangsung->getCountByParams(array("A.PAKET_METODE_LELANG_ID" => "6", "A.UNIT_KERJA_ID" => $unitkerja, "A.TAHUN_ANGGARAN" => $getTahun, "A.USER_LOGIN_ID" =>$this->USER_LOGIN_ID));
  $sumPembelianLangsung = $sumPembelianLangsung->getSumByParams(array("A.PAKET_METODE_LELANG_ID" => "6", "A.UNIT_KERJA_ID" => $unitkerja, "A.TAHUN_ANGGARAN" => $getTahun, "A.USER_LOGIN_ID" =>$this->USER_LOGIN_ID));
  $countOffline = $countOffline->getCountByParams(array("A.PAKET_METODE_LELANG_ID" => "9", "A.UNIT_KERJA_ID" => $unitkerja, "A.TAHUN_ANGGARAN" => $getTahun, "A.USER_LOGIN_ID" =>$this->USER_LOGIN_ID));
  $sumOffline = $sumOffline->getSumByParams(array("A.PAKET_METODE_LELANG_ID" => "9", "A.UNIT_KERJA_ID" => $unitkerja, "A.TAHUN_ANGGARAN" => $getTahun, "A.USER_LOGIN_ID" =>$this->USER_LOGIN_ID));
  $countPemerintah = $countPemerintah->getCountByParams(array("A.PAKET_METODE_LELANG_ID" => "12", "A.UNIT_KERJA_ID" => $unitkerja, "A.TAHUN_ANGGARAN" => $getTahun, "A.USER_LOGIN_ID" =>$this->USER_LOGIN_ID));
  $sumPemerintah = $sumPemerintah->getSumByParams(array("A.PAKET_METODE_LELANG_ID" => "12", "A.UNIT_KERJA_ID" => $unitkerja, "A.TAHUN_ANGGARAN" => $getTahun, "A.USER_LOGIN_ID" =>$this->USER_LOGIN_ID));

  $getDataHPS->selectByParams("SELECT (SELECT sum(nilai) nilai_hps  from view_paket_dashboard
                               WHERE unit_kerja_id='".$unitkerja."' AND user_login_id = ".$this->USER_LOGIN_ID." AND tahun = '".$getTahun."') nilai_hps,
                               (
                                SELECT (a.ongkos_kirim +
                                a.harga_nego + (SELECT sum(nilai) from view_paket_dashboard where unit_kerja_id='".$unitkerja."' AND user_login_id = ".$this->USER_LOGIN_ID." AND TAHUN_ANGGARAN = '".$getTahun."' and paket_metode_lelang_id = 9)) nilai_kontrak from (
                                    SELECT
                                    (SELECT sum(ongkos_kirim) as ongkos_kirim from katalog_logistik where paket_id in (
                                    SELECT paket_id from view_paket_dashboard where unit_kerja_id='".$unitkerja."' AND user_login_id = ".$this->USER_LOGIN_ID." AND TAHUN_ANGGARAN = '".$getTahun."' and paket_metode_lelang_id in (6,9)
                                    )),
                                    (SELECT sum((harga_nego * qty)) harga_nego from katalog_rekanan where paket_id in (
                                    SELECT paket_id from view_paket_dashboard where unit_kerja_id='".$unitkerja."' AND user_login_id = ".$this->USER_LOGIN_ID." AND TAHUN_ANGGARAN = '".$getTahun."' and paket_metode_lelang_id in (6,9)
                                    ))
                                ) a
                               ) nilai_kontrak
                               "
                               );

} else {
  $tahun = '';

  $countPembelianLangsung = $countPembelianLangsung->getCountByParams(array("A.PAKET_METODE_LELANG_ID" => "6", "A.UNIT_KERJA_ID" => $unitkerja, "A.USER_LOGIN_ID" =>$this->USER_LOGIN_ID));
  $sumPembelianLangsung = $sumPembelianLangsung->getSumByParams(array("A.PAKET_METODE_LELANG_ID" => "6", "A.UNIT_KERJA_ID" => $unitkerja, "A.USER_LOGIN_ID" =>$this->USER_LOGIN_ID));
  $countOffline = $countOffline->getCountByParams(array("A.PAKET_METODE_LELANG_ID" => "9", "A.UNIT_KERJA_ID" => $unitkerja, "A.USER_LOGIN_ID" =>$this->USER_LOGIN_ID));
  $sumOffline = $sumOffline->getSumByParams(array("A.PAKET_METODE_LELANG_ID" => "9", "A.UNIT_KERJA_ID" => $unitkerja, "A.USER_LOGIN_ID" =>$this->USER_LOGIN_ID));
  $countPemerintah = $countPemerintah->getCountByParams(array("A.PAKET_METODE_LELANG_ID" => "12", "A.UNIT_KERJA_ID" => $unitkerja, "A.USER_LOGIN_ID" =>$this->USER_LOGIN_ID));
  $sumPemerintah = $sumPemerintah->getSumByParams(array("A.PAKET_METODE_LELANG_ID" => "12", "A.UNIT_KERJA_ID" => $unitkerja, "A.USER_LOGIN_ID" =>$this->USER_LOGIN_ID));

 // Ambil nilai HPS untuk Pembelian Offline tambah (harga nego + ongkir)
  $getDataHPS->selectByParams("SELECT (SELECT sum(nilai) nilai_hps  from view_paket_dashboard
                               WHERE unit_kerja_id='".$unitkerja."' AND user_login_id = ".$this->USER_LOGIN_ID.") nilai_hps,
                               (
                                SELECT (a.ongkos_kirim +
                                a.harga_nego + (SELECT sum(nilai) from view_paket_dashboard where unit_kerja_id='".$unitkerja."' AND user_login_id = ".$this->USER_LOGIN_ID." and paket_metode_lelang_id = 9)) nilai_kontrak from (
                                    SELECT
                                    (SELECT sum(ongkos_kirim) as ongkos_kirim from katalog_logistik where paket_id in (
                                    SELECT paket_id from view_paket_dashboard where unit_kerja_id='".$unitkerja."' AND user_login_id = ".$this->USER_LOGIN_ID." and paket_metode_lelang_id in (6,9)
                                    )),
                                    (SELECT sum((harga_nego * qty)) harga_nego from katalog_rekanan where paket_id in (
                                    SELECT paket_id from view_paket_dashboard where unit_kerja_id='".$unitkerja."' AND user_login_id = ".$this->USER_LOGIN_ID." and paket_metode_lelang_id in (6,9)
                                    ))
                                ) a
                               ) nilai_kontrak
                               "
                               );
}
// echo $getDataHPS->query(); die;
// End Laporan Berdasarkan Metode Pengadaan
// echo "<pre>"; print_r($pieValue); die();
?>
 <script src="https://unpkg.com/gauge-chart@latest/dist/bundle.js"></script>
<script src="<?=base_url()?>assets/new/vendors/js/charts/chart.min.js"></script>


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
      $url = base_url('main/index/dashboardpembeli?tahun=');
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
  <div class="col-md-8"> </div>
  <div class="col-md-4">
      <div class="card">
        <div class="row">
            <div class="col-md-4 border-right">
                <i class="fa fa-question-circle" data-toggle="tooltip" title="Total Paket <?php if ($getTahun != 'all'){ echo 'Tahun Anggaran '.$getTahun; } ?>" data-placement="top" aria-hidden="true" style="position:absolute;right:10px;padding-top:10px;color:black"></i>
                <div class="description-block">
                    <div class="description-header" style="font-size:2rem;"><?= $countPembelianLangsung + $countOffline + $countPemerintah ?></div>
                    <span class="description-text">TOTAL PAKET</span>
                </div>
            </div>
            <div class="col-md-8">
                <i class="fa fa-question-circle" data-toggle="tooltip" title="Total Harga Perkiraan <?php if ($getTahun != 'all'){ echo 'Tahun Anggaran '.$getTahun; } ?>" data-placement="top" aria-hidden="true" style="position:absolute;right:25px;padding-top:10px;color:black"></i>
                <div class="description-block">
                    <div class="description-header" style="font-size:2rem;" data-toggle="tooltip" data-placement="top" data-original-title="">Rp <?php echo numberToSimbol(round($sumPembelianLangsung + $sumOffline + $sumPemerintah)) ?></div>
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

    <div class="col-md-12">
        <div class="row">
            <div class="col-md-3">
              <div class="card box-shadow-0 animated zoomIn wfont" data-appear="appear" data-animation="zoomIn" style="background-color: #4897D8;">
                <div class="card-content">
                    <div class="card-body" onclick="openAdd('main/loadUrl/main/dashboard_detail/?metode=6&tahun=<?=$getTahun?>&uki=<?=$unitkerja?>&uid=<?=$this->USER_LOGIN_ID?>&jenis=panitia')" style="cursor: pointer">
                        <div class="media">
                            <div class="media-body text-center">
                                <span style="margin-top: 15%;">Pembelian Katalog</span>
                                <h2 class="wfont mt-2"><b>Rp. <?php echo numberToSimbol(round($sumPembelianLangsung)) ?> </b></h2>
                                <!-- <small style="font-size: .8em; top: -12px; position: relative;">Harga Perkiraan</small> -->
                            </div>
                            <div class="media-right media-middle">
                                <i class="fa fa-question-circle dark" data-toggle="tooltip" data-placement="top" title="Total Pembelian Katalog" style="font-size:1.3em !important; cursor: pointer;"></i>
                            </div>
                        </div>
                        <div class="mt-1 text-center">
                          <?= '<small style="font-weight:bold">'.$countPembelianLangsung.' Paket</small>'; ?> <i class="fa fa-arrow-circle-right"></i>
                        </div>
                    </div>
                </div>
              </div>
            </div>

            <div class="col-md-3">
              <div class="card box-shadow-0 animated zoomIn wfont" data-appear="appear" data-animation="zoomIn" style="background-color: #C9A66B;">
                <div class="card-content">
                    <div class="card-body" onclick="openAdd('main/loadUrl/main/dashboard_detail/?metode=9&tahun=<?=$getTahun?>&uki=<?=$unitkerja?>&uid=<?=$this->USER_LOGIN_ID?>&jenis=panitia')" style="cursor: pointer">
                        <div class="media">
                            <div class="media-body text-center">
                                <span style="margin-top: 15%;">Pembelian Langsung</span>
                                <h2 class="wfont mt-2"><b>Rp. <?php echo numberToSimbol(round($sumOffline)) ?> </b></h2>
                                <!-- <small style="font-size: .8em; top: -12px; position: relative;">Harga Perkiraan</small> -->
                            </div>
                            <div class="media-right media-middle">
                                <i class="fa fa-question-circle dark" data-toggle="tooltip" data-placement="top" title="Total Pembelian Langsung" style="font-size:1.3em !important; cursor: pointer;"></i>
                            </div>
                        </div>
                        <div class="mt-1 text-center">
                          <?= '<small style="font-weight:bold">'.$countOffline.' Paket</small>'; ?> <i class="fa fa-arrow-circle-right"></i>
                        </div>
                    </div>
                </div>
              </div>
            </div>

            <div class="col-md-3">
              <div class="card box-shadow-0 animated zoomIn wfont" data-appear="appear" data-animation="zoomIn" style="background-color: #F8A055;">
                <div class="card-content">
                    <div class="card-body" onclick="openAdd('main/loadUrl/main/dashboard_detail/?metode=12&tahun=<?=$getTahun?>&uki=<?=$unitkerja?>&uid=<?=$this->USER_LOGIN_ID?>&jenis=panitia')" style="cursor: pointer">
                        <div class="media">
                            <div class="media-body text-center">
                                <span style="margin-top: 15%;">Pembelian Katalog Pemerintah</span>
                                <h2 class="wfont mt-2"><b>Rp. <?php echo numberToSimbol(round($sumPemerintah)) ?> </b></h2>
                                <!-- <small style="font-size: .8em; top: -12px; position: relative;">Harga Perkiraan</small> -->
                            </div>
                            <div class="media-right media-middle">
                                <i class="fa fa-question-circle dark" data-toggle="tooltip" data-placement="top" title="Total Pembelian Katalog Pemerintah" style="font-size:1.3em !important; cursor: pointer;"></i>
                            </div>
                        </div>
                        <div class="mt-1 text-center">
                          <?= '<small style="font-weight:bold">'.$countPemerintah.' Paket</small>'; ?> <i class="fa fa-arrow-circle-right"></i>
                        </div>
                    </div>
                </div>
              </div>
            </div>

        </div>
    </div>

        <!-- ***********************************
    ******
    *************************************-->
    <?php
    $countPembelianLangsung = new Dashpaket();
    $sumPembelianLangsung = new Dashpaket();
    $sumPembelianLangsungFinal = new Dashpaket();
    $countOffline = new Dashpaket();
    $sumOffline = new Dashpaket();
    $sumOfflineFinal = new Dashpaket();
    $countPemerintah = new Dashpaket();
    $sumPemerintah = new Dashpaket();
    $sumPemerintahFinal = new Dashpaket();

    if ($getTahun != 'all'){
        $countPembelianLangsung = $countPembelianLangsung->getCountByParams(array("A.PAKET_METODE_LELANG_ID" => "6", "TAHUN_ANGGARAN" => $getTahun, "A.USER_LOGIN_ID" =>$this->USER_LOGIN_ID));
        $sumPembelianLangsung = $sumPembelianLangsung->getSumByParams(array("A.PAKET_METODE_LELANG_ID" => "6", "TAHUN_ANGGARAN" => $getTahun, "A.USER_LOGIN_ID" =>$this->USER_LOGIN_ID));
        $sumPembelianLangsungFinal = $sumPembelianLangsungFinal->getSumFinalKatalogByParams(array("A.PAKET_METODE_LELANG_ID" => "6", "TAHUN_ANGGARAN" => $getTahun, "A.USER_LOGIN_ID" =>$this->USER_LOGIN_ID));

        $countOffline = $countOffline->getCountByParams(array("A.PAKET_METODE_LELANG_ID" => "9", "TAHUN_ANGGARAN" => $getTahun, "A.USER_LOGIN_ID" =>$this->USER_LOGIN_ID));
        $sumOffline = $sumOffline->getSumByParams(array("A.PAKET_METODE_LELANG_ID" => "9", "TAHUN_ANGGARAN" => $getTahun, "A.USER_LOGIN_ID" =>$this->USER_LOGIN_ID));
        $sumOfflineFinal = 0;

        $countPemerintah = $countPemerintah->getCountByParams(array("A.PAKET_METODE_LELANG_ID" => "12", "TAHUN_ANGGARAN" => $getTahun, "A.USER_LOGIN_ID" =>$this->USER_LOGIN_ID));
        $sumPemerintah = $sumPemerintah->getSumByParams(array("A.PAKET_METODE_LELANG_ID" => "12", "TAHUN_ANGGARAN" => $getTahun, "A.USER_LOGIN_ID" =>$this->USER_LOGIN_ID));
        $sumPemerintahFinal = 0;
    } else {
        $tahun = '';
        $countPembelianLangsung = $countPembelianLangsung->getCountByParams(array("A.PAKET_METODE_LELANG_ID" => "6", "A.USER_LOGIN_ID" =>$this->USER_LOGIN_ID));
        $sumPembelianLangsung = $sumPembelianLangsung->getSumByParams(array("A.PAKET_METODE_LELANG_ID" => "6", "A.USER_LOGIN_ID" =>$this->USER_LOGIN_ID));
        $sumPembelianLangsungFinal = $sumPembelianLangsungFinal->getSumFinalKatalogByParams(array("A.PAKET_METODE_LELANG_ID" => "6", "A.USER_LOGIN_ID" =>$this->USER_LOGIN_ID));

        $countOffline = $countOffline->getCountByParams(array("A.PAKET_METODE_LELANG_ID" => "9", "A.USER_LOGIN_ID" =>$this->USER_LOGIN_ID));
        $sumOffline = $sumOffline->getSumByParams(array("A.PAKET_METODE_LELANG_ID" => "9", "A.USER_LOGIN_ID" =>$this->USER_LOGIN_ID));
        $sumOfflineFinal = $sumOfflineFinal->getSumFinalByParams(array("A.PAKET_METODE_LELANG_ID" => "9", "A.USER_LOGIN_ID" =>$this->USER_LOGIN_ID));

        $countPemerintah = $countPemerintah->getCountByParams(array("A.PAKET_METODE_LELANG_ID" => "12", "A.USER_LOGIN_ID" =>$this->USER_LOGIN_ID));
        $sumPemerintah = $sumPemerintah->getSumByParams(array("A.PAKET_METODE_LELANG_ID" => "12", "A.USER_LOGIN_ID" =>$this->USER_LOGIN_ID));
        $sumPemerintahFinal = $sumPemerintahFinal->getSumFinalByParams(array("A.PAKET_METODE_LELANG_ID" => "12", "A.USER_LOGIN_ID" =>$this->USER_LOGIN_ID));
    }
    $sum =  $sumTenderKualifikasi + $sumOffline + $sumPemerintah;
    $sumFinal =  $sumTenderKualifikasiFinal + $sumOfflineFinal + $sumPemerintahFinal;
    $count =  $countPembelianKatalogKualifikasi + $countOffline + $countPemerintah;
        // echo $countPembelianLangsung->query;
    // echo $count;
    ?>

    <div class="col-md-7">
      <div class="card">
        <div class="card-header card-head-inverse bg-primary">
            <h4 class="card-title text-white" style="font-size:.9em !important">Laporan Berdasarkan Metode Pengadaan <?= $tahun ?> </h4>
            <a class="heading-elements-toggle"><i class="fa fa-ellipsis-v font-medium-3"></i></a>
            <div class="heading-elements">
                <ul class="list-inline mb-0">
                  <li><a data-action="collapse"><i class="ft-minus"></i></a></li>
                  <li><a data-action="expand"><i class="ft-maximize"></i></a></li>
                </ul>
            </div>
        </div>
        <div class="card-content collapse show border-info border-darken-2">
          <div class="chart-content">
            <div class="col-md-12">

            <ul class="nav nav-tabs nav-linetriangle no-hover-bg">
                <li class="nav-item">
                    <a class="nav-link active" id="base-tab31" data-toggle="tab" aria-controls="tab31" href="#tab31" aria-expanded="true">
                    HARGA PERKIRAAN</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="base-tab32" data-toggle="tab" aria-controls="tab32" href="#tab32" aria-expanded="false">
                    PAKET</a>
                </li>
            </ul>
            </div>
            <div class="tab-content px-1 pt-1">
                <div role="tabpanel" class="tab-pane active" id="tab31" aria-expanded="true" aria-labelledby="base-tab31">
                    <canvas id="myChart"width="300" height="100"></canvas>
                </div>
                <div class="tab-pane" id="tab32" aria-labelledby="base-tab32">
                    <canvas id="myChart2"width="300" height="100"></canvas>
                </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <script>
        // Bar chart Harga Perkiraan
        new Chart(document.getElementById("myChart"), {
          type: 'horizontalBar',
          data: {
            labels: ["Pembelian Katalog", "Pembelian Langsung", "Pembelian Katalog Pemerintah"],
            datasets: [
              {
                label: "Harga Perkiraan",
                data: [<?= $sumPembelianLangsung ?>,<?= $sumOffline ?>,<?= $sumPemerintah ?>],
                backgroundColor: ["#009ab0","#82efee","#c9a377","#128a08","#de9932","#be6d40"],
              },
              {
                label: "Harga Final/Fix",
                data: [<?= $sumPembelianLangsungFinal ?>,<?= $sumOfflineFinal ?>,<?= $sumPemerintahFinal ?>],
                backgroundColor: ["#14b0c6","#b6fbfa","#cfbca6","#41ab38","#f3b75c","#e79465"],
              }
            ]
          },
          options: {
            tooltips: {
                enabled: true,
                mode: 'single',
                callbacks: {
                    label: function(tooltipItems, data) {
                        return data.datasets[tooltipItems.datasetIndex].label +': ' + number_format(tooltipItems.xLabel, 0, ',', '.');
                    }
                }
            },
            legend: { display: false },
            title: {
              display: true,
              text: ''
            },
            animation: {
                duration: 2500,
            },
            layout: {
              padding: {
                left: 10
              }
            },
            scales: {
                yAxes: [{
                    ticks: {
                        fontSize: 13
                    }
                }],
                xAxes: [{
                    ticks: {
                        fontSize: 10
                    }
                }]
            },
          }
        });

        new Chart(document.getElementById("myChart2"), {
          type: 'horizontalBar',
          data: {
            labels: ["Pembelian Katalog", "Pembelian Langsung", "Pembelian Katalog Pemerintah"],
            datasets: [
              {
                label: "Total",
                data: [<?= $countPembelianLangsung ?>,<?= $countOffline ?>, <?= $countPemerintah ?>],
                backgroundColor: ["#de9932","#be6d40","#c9a377"],
              }
            ]
          },
          options: {
            tooltips: {
                enabled: true,
                mode: 'single',
                callbacks: {
                    label: function(tooltipItems, data) {
                        return data.datasets[tooltipItems.datasetIndex].label +': ' + number_format(tooltipItems.xLabel, 0, ',', '.') + ' Paket';
                    }
                }
            },
            legend: { display: false },
            title: {
              display: true,
              text: ''
            },
            layout: {
              padding: {
                left: 10
              }
            },
            scales: {
                yAxes: [{
                    ticks: {
                        fontSize: 13
                    }
                }],
                xAxes: [{
                    ticks: {
                        fontSize: 10
                    }
                }]
            },
          }
        });

        number_format = function (number, decimals, dec_point, thousands_sep) {
        number = number.toFixed(decimals);

        var nstr = number.toString();
        nstr += '';
        x = nstr.split('.');
        x1 = x[0];
        x2 = x.length > 1 ? dec_point + x[1] : '';
        var rgx = /(\d+)(\d{3})/;

        while (rgx.test(x1))
            x1 = x1.replace(rgx, '$1' + thousands_sep + '$2');

        return x1 + x2;
    }
    </script>

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
         url: "dashboard_json/setHPSValPembelian/?unitkerja=<?= $unitkerja ?>&userloginid=<?= $this->USER_LOGIN_ID ?>&tahun=<?= $getTahun ?>&bulan="+a,
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
        <div class="card-header card-head-inverse bg-primary">
            <h4 class="card-title text-white" style="font-size:.9em !important">Efesiensi Pengadaan Berdasarkan Harga Perkiraan <?= $tahun ?> </h4>
            <a class="heading-elements-toggle"><i class="fa fa-ellipsis-v font-medium-3"></i></a>
            <div class="heading-elements">
                <ul class="list-inline mb-0">
                  <li><a data-action="collapse"><i class="ft-minus"></i></a></li>
                  <li><a data-action="expand"><i class="ft-maximize"></i></a></li>
                </ul>
            </div>
        </div>

        <div class="card-content collapse show">
          <div class="text-center">
            <div class="col-md-12">
              <?php
              $getMonth = new Queryfree();
              $getMonth->selectByParams("SELECT * FROM MONTH");
              ?>
              <select class="form-control mt-2" onChange="return setHPSVal($(this).val())">
                <?php
                echo '<option value="">-- Pilih Bulan --</option>';
                while($getMonth->nextRow())
                {
                  echo '<option value="'.$getMonth->getField('month_angka').'">'.$getMonth->getField('month_ina').'</option>';
                } ?>
              </select>
              <div class="m-1">
                <?php

                if ($getTahun != 'all'){
                  $tahun = 'Tahun '.$getTahun;

                  $getDataHPS->selectByParams("SELECT (SELECT sum(nilai) nilai_hps  from view_paket_dashboard
                                               WHERE unit_kerja_id='".$unitkerja."' AND user_login_id = ".$this->USER_LOGIN_ID." AND tahun_permohonan = '".$getTahun."') nilai_hps,
                                               (
                                                SELECT (a.ongkos_kirim +
                                                a.harga_nego + (SELECT sum(nilai) from view_paket_dashboard where unit_kerja_id='".$unitkerja."' AND user_login_id = ".$this->USER_LOGIN_ID." AND tahun_permohonan = '".$getTahun."' and paket_metode_lelang_id = 9)) nilai_kontrak from (
                                                    SELECT
                                                    (SELECT sum(ongkos_kirim) as ongkos_kirim from katalog_logistik where paket_id in (
                                                    SELECT paket_id from view_paket_dashboard where unit_kerja_id='".$unitkerja."' AND user_login_id = ".$this->USER_LOGIN_ID." AND tahun_permohonan = '".$getTahun."' and paket_metode_lelang_id in (6,9)
                                                    )),
                                                    (SELECT sum((harga_nego * qty)) harga_nego from katalog_rekanan where paket_id in (
                                                    SELECT paket_id from view_paket_dashboard where unit_kerja_id='".$unitkerja."' AND user_login_id = ".$this->USER_LOGIN_ID." AND tahun_permohonan = '".$getTahun."' and paket_metode_lelang_id in (6,9)
                                                    ))
                                                ) a
                                               ) nilai_kontrak
                                               "
                                               );

                } else {
                  $tahun = '';

                  $getDataHPS->selectByParams("SELECT (SELECT sum(nilai) nilai_hps  from view_paket_dashboard
                                               WHERE unit_kerja_id='".$unitkerja."' AND user_login_id = ".$this->USER_LOGIN_ID.") nilai_hps,
                                               (
                                                SELECT (a.ongkos_kirim +
                                                a.harga_nego + (SELECT sum(nilai) from view_paket_dashboard where unit_kerja_id='".$unitkerja."' AND user_login_id = ".$this->USER_LOGIN_ID." and paket_metode_lelang_id = 9)) nilai_kontrak from (
                                                    SELECT
                                                    (SELECT sum(ongkos_kirim) as ongkos_kirim from katalog_logistik where paket_id in (
                                                    SELECT paket_id from view_paket_dashboard where unit_kerja_id='".$unitkerja."' AND user_login_id = ".$this->USER_LOGIN_ID." and paket_metode_lelang_id in (6,9)
                                                    )),
                                                    (SELECT sum((harga_nego * qty)) harga_nego from katalog_rekanan where paket_id in (
                                                    SELECT paket_id from view_paket_dashboard where unit_kerja_id='".$unitkerja."' AND user_login_id = ".$this->USER_LOGIN_ID." and paket_metode_lelang_id in (6,9)
                                                    ))
                                                ) a
                                               ) nilai_kontrak
                                               "
                                               );
                }
                // echo $getDataHPS->query; die;

                $getDataHPS->firstRow();
                $nilaiHps = $getDataHPS->getField('nilai_hps');
                $nilaiKontrak = $getDataHPS->getField('nilai_kontrak');
                $nilaiEfesiensi = $nilaiHps - $nilaiKontrak;
                // $nilaiEfesiensi = $nilaiHps - 22660000000;
                if ($nilaiEfesiensi > 0) {
                  $persentaseEfesiensi = round($nilaiEfesiensi/$nilaiHps * 100,2).' %';
                  $backColor = ' background-color:#967adc;';
                  $ketEfesiensi1 = 'Efesien';
                  $ketEfesiensi2 = '<span class="white darken-1 block"><i class="ft-arrow-down white" ></i> '.$persentaseEfesiensi.' dari nilai Harga Perkiraan</span>';
                } else {
                  $persentaseEfesiensi = 0;
                  $backColor = ' background-color:#da4453;';
                  $ketEfesiensi1 = 'Tidak Efesien';
                  $ketEfesiensi2 = '<span class="white darken-1 block"><i class="ft-arrow-up white" ></i>  '.$persentaseEfesiensi.' dari nilai Harga Perkiraan</span>';
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
                              <span class="font-large-2 line-height-1 text-bold-300"><?= number_format($nilaiKontrak,'0',',','.') ?></span>
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
