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

$this->load->model(array("Dashpaket","Queryfree","Userlogin"));

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

$getTahun = isset($_GET['tahun']) ? $_GET['tahun'] : date('Y'); 
// echo "<pre>"; print_r($pieValue); die();
?>
 <!-- <script src="https://unpkg.com/gauge-chart@latest/dist/bundle.js"></script> -->
<script src="<?=base_url()?>assets/new/vendors/js/charts/chart.min.js"></script>
<script src="https://unpkg.com/emodal@1.2.69/dist/eModal.min.js" /></script>
<!-- <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.7.0/chart.min.js"></script> -->
<!-- https://codepen.io/b1tn3r/pen/erLqbQ -->
 
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

<!-- 
*******************
*****  KOTAK-KOTAK
*******************
 -->
<?php 
// Laporan Berdasarkan Metode Pengadaan
$countPerencanaan = new Dashpaket();
$sumPerencanaan = new Dashpaket(); 
$countPersiapan = new Dashpaket();
$sumPersiapan = new Dashpaket(); 
$countPemilihan = new Dashpaket();
$sumPemilihan = new Dashpaket(); 
$countKontrakProses = new Dashpaket();
$sumKontrakProses = new Dashpaket(); 
$countKontrakSelesai = new Dashpaket();
$sumKontrakSelesai = new Dashpaket(); 

if ($getTahun != 'all'){
    $tahun = 'Tahun '.$getTahun;
    $countPerencanaan->selectPermohonan(array("A.APPROVAL" => "1", "A.TAHUN_ANGGARAN" => $getTahun),-1,-1,"");
    $sumPerencanaan = $sumPerencanaan->selectPermohonanSum(array("A.APPROVAL" => "1", "A.TAHUN_ANGGARAN" => $getTahun),""); 
    $countPersiapan->selectPermohonan(array("A.TAHUN_ANGGARAN" => $getTahun),-1,-1," AND KODE_PR IS NOT NULL");
    $sumPersiapan = $sumPersiapan->selectPersiapanSum(array("A.TAHUN_ANGGARAN" => $getTahun)," AND KODE_PR IS NOT NULL"); 
    $countPemilihan->selectPemilihan(array("A.TAHUN_ANGGARAN" => $getTahun),-1,-1,"");
    $sumPemilihan = $sumPemilihan->selectPemilihanSum(array("A.TAHUN_ANGGARAN" => $getTahun),""); 
    $countKontrakProses->selectKontrakProses(array("A.TAHUN_ANGGARAN" => $getTahun),-1,-1,"");
    $sumKontrakProses = $sumKontrakProses->selectKontrakProsesSum(array("A.TAHUN_ANGGARAN" => $getTahun),""); 
    $countKontrakSelesai->selectKontrakSelesai(array("A.TAHUN_ANGGARAN" => $getTahun),-1,-1,"");
    $sumKontrakSelesai = $sumKontrakSelesai->selectKontrakSelesaiSum(array("A.TAHUN_ANGGARAN" => $getTahun),""); 
} else {
    $tahun = '';
    $countPerencanaan->selectPermohonan(array("A.APPROVAL" => "1"),-1,-1,"");
    $sumPerencanaan = $sumPerencanaan->selectPermohonanSum(array("A.APPROVAL" => "1"),""); 
    $countPersiapan->selectPermohonan(array(),-1,-1," AND KODE_PR IS NOT NULL");
    $sumPersiapan = $sumPersiapan->selectPersiapanSum(array()," AND KODE_PR IS NOT NULL"); 
    $countPemilihan->selectPemilihan(array(),-1,-1,"");
    $sumPemilihan = $sumPemilihan->selectPemilihanSum(array(),""); 
    $countKontrakProses->selectKontrakProses(array(),-1,-1,"");
    $sumKontrakProses = $sumKontrakProses->selectKontrakProsesSum(array(),""); 
    $countKontrakSelesai->selectKontrakSelesai(array(),-1,-1,"");
    $sumKontrakSelesai = $sumKontrakSelesai->selectKontrakSelesaiSum(array(),""); 
} ?>

<div class="row">
  <div class="form-group col-md-3">
    <!-- <label>Pilih Tahun</label> -->
    <select class="form-control" id="setyear" onChange="return window.location = $(this).val()">
      <?php
      $selected = '';
      $url = base_url('main/index/dashboardall?tahun=');
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
  <div class="col-md-6"> </div>
  <div class="col-md-6" >
      <div class="card border-info border-darken-2 animated zoomIn">
        <div class="row">
            <div class="col-md-3 border-right">
                <i class="fa fa-question-circle" data-toggle="tooltip" title="Total Paket <?php if ($getTahun != 'all'){ echo 'Tahun Anggaran '.$getTahun; } ?>" data-placement="top" aria-hidden="true" style="position:absolute;right:10px;padding-top:10px;color:black"></i>
                <div class="description-block">
                    <div class="description-header" style="font-size:2rem;"><?= $countPerencanaan->countRow()?></div>
                    <span class="description-text">TOTAL PAKET</span>
                </div>
            </div> 
            <div class="col-md-4">
                <i class="fa fa-question-circle" data-toggle="tooltip" title="Total Harga Perkiraan <?php if ($getTahun != 'all'){ echo 'Tahun Anggaran '.$getTahun; } ?>" data-placement="top" aria-hidden="true" style="position:absolute;right:25px;padding-top:10px;color:black"></i>
                <div class="description-block">
                    <div class="description-header" style="font-size:2rem;" data-toggle="tooltip" data-placement="top" data-original-title="">Rp <?php echo numberToSimbol(round($sumPerencanaan)) ?></div>
                    <span class="description-text">TOTAL HARGA PERKIRAAN</span>
                </div>
            </div>
            <div class="col-md-4">
                <i class="fa fa-question-circle" data-toggle="tooltip" title="Total Harga Final/Akhir <?php if ($getTahun != 'all'){ echo 'Tahun Anggaran '.$getTahun; } ?>" data-placement="top" aria-hidden="true" style="position:absolute;right:25px;padding-top:10px;color:black"></i>
                <div class="description-block">
                    <div class="description-header" style="font-size:2rem;" data-toggle="tooltip" data-placement="top" data-original-title="">Rp <?php echo numberToSimbol(round($sumKontrakProses)) ?></div>
                    <span class="description-text">TOTAL HARGA FINAL/AKHIR</span>
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
            <div class="card-body" onclick="openAdd('main/loadUrl/main/dashboard_detail_new/?jenis=perencanaan&tahun=<?=$getTahun?>&uki=<?=$unitkerja?>&uid=<?=$this->USER_LOGIN_ID?>')" style="cursor: pointer">
                <div class="media">
                    <div class="media-body text-center">
                        <span style="margin-top: 15%;">PERENCANAAN</span>
                        <h2 class="wfont mt-2"><b>Rp <?php echo numberToSimbol(round($sumPerencanaan)) ?> </b></h2>
                        <!-- <small style="font-size: .8em; top: -12px; position: relative;">Harga Perkiraan</small> -->
                    </div>
                    <div class="media-right media-middle">
                        <i class="fa fa-question-circle dark" data-toggle="tooltip" data-placement="top" title="Total Perencanaan" style="font-size:1.3em !important; cursor: pointer;"></i>
                    </div>
                </div>
                <div class="mt-1 text-center">
                  <?= '<small style="font-weight:bold">'.$countPerencanaan->countRow().' Paket</small>'; ?> <i class="fa fa-arrow-circle-right"></i>
                </div>
            </div>
        </div>
      </div>
    </div>   

    <div class="col-md-2">
      <div class="card box-shadow-0 animated zoomIn wfont" data-appear="appear" data-animation="zoomIn" style="background-color: #0000FF;">
        <div class="card-content">
            <div class="card-body" onclick="openAdd('main/loadUrl/main/dashboard_detail_new/?jenis=persiapan&tahun=<?=$getTahun?>&uki=<?=$unitkerja?>&uid=<?=$this->USER_LOGIN_ID?>')" style="cursor: pointer">
                <div class="media">
                    <div class="media-body text-center">
                        <span style="margin-top: 15%;">PERSIAPAN</span>
                        <h2 class="wfont mt-2"><b>Rp <?php echo numberToSimbol(round($sumPersiapan)) ?> </b></h2>
                        <!-- <small style="font-size: .8em; top: -12px; position: relative;">Harga Perkiraan</small> -->
                    </div>
                    <div class="media-right media-middle">
                        <i class="fa fa-question-circle dark" data-toggle="tooltip" data-placement="top" title="Total Perencanaan" style="font-size:1.3em !important; cursor: pointer;"></i>
                    </div>
                </div>
                <div class="mt-1 text-center">
                  <?= '<small style="font-weight:bold">'.$countPersiapan->countRow().' Paket</small>'; ?> <i class="fa fa-arrow-circle-right"></i>
                </div>
            </div>
        </div>
      </div>
    </div>   

    <div class="col-md-2">
      <div class="card box-shadow-0 animated zoomIn wfont" data-appear="appear" data-animation="zoomIn" style="background-color: #F7941D;">
        <div class="card-content">
            <div class="card-body" onclick="openAdd('main/loadUrl/main/dashboard_detail_new/?jenis=pemilihan&tahun=<?=$getTahun?>&uki=<?=$unitkerja?>&uid=<?=$this->USER_LOGIN_ID?>')" style="cursor: pointer">
                <div class="media">
                    <div class="media-body text-center">
                        <span style="margin-top: 15%;">PEMILIHAN</span>
                        <h2 class="wfont mt-2"><b>Rp <?php echo numberToSimbol(round($sumPemilihan)) ?> </b></h2>
                        <!-- <small style="font-size: .8em; top: -12px; position: relative;">Harga Perkiraan</small> -->
                    </div>
                    <div class="media-right media-middle">
                        <i class="fa fa-question-circle dark" data-toggle="tooltip" data-placement="top" title="Total Pemilihan" style="font-size:1.3em !important; cursor: pointer;"></i>
                    </div>
                </div>
                <div class="mt-1 text-center">
                  <?= '<small style="font-weight:bold">'.$countPemilihan->countRow().' Paket</small>'; ?> <i class="fa fa-arrow-circle-right"></i>
                </div>
            </div>
        </div>
      </div>
    </div>   

    <div class="col-md-2">
      <div class="card box-shadow-0 animated zoomIn wfont" data-appear="appear" data-animation="zoomIn" style="background-color: #6F00FF;">
        <div class="card-content">
            <div class="card-body" onclick="openAdd('main/loadUrl/main/dashboard_detail_new/?jenis=kontrak&tahun=<?=$getTahun?>&uki=<?=$unitkerja?>&uid=<?=$this->USER_LOGIN_ID?>')" style="cursor: pointer">
                <div class="media">
                    <div class="media-body text-center">
                        <span style="margin-top: 15%;">KONTRAK</span>
                        <h2 class="wfont mt-2"><b>Rp <?php echo numberToSimbol(round($sumKontrakProses)) ?> </b></h2>
                        <!-- <small style="font-size: .8em; top: -12px; position: relative;">Nilai Kontrak</small> -->
                    </div>
                    <div class="media-right media-middle">
                        <i class="fa fa-question-circle dark" data-toggle="tooltip" data-placement="top" title="Total Kontrak" style="font-size:1.3em !important; cursor: pointer;"></i>
                    </div>
                </div>
                <div class="mt-1 text-center">
                  <?= '<small style="font-weight:bold">'.$countKontrakProses->countRow().' Paket</small>'; ?> <i class="fa fa-arrow-circle-right"></i>
                </div>
            </div>
        </div>
      </div>
    </div>     

    <div class="col-md-2">
      <div class="card box-shadow-0 animated zoomIn wfont" data-appear="appear" data-animation="zoomIn" style="background-color: #CF2E26;">
        <div class="card-content">
            <div class="card-body" onclick="openAdd('main/loadUrl/main/dashboard_detail_new/?jenis=selesai&tahun=<?=$getTahun?>&uki=<?=$unitkerja?>&uid=<?=$this->USER_LOGIN_ID?>')" style="cursor: pointer">
                <div class="media">
                    <div class="media-body text-center">
                        <span style="margin-top: 15%;">SERAH TERIMA</span>
                        <h2 class="wfont mt-2"><b>Rp <?php echo numberToSimbol(round($sumKontrakSelesai)) ?> </b></h2>
                        <!-- <small style="font-size: .8em; top: -12px; position: relative;">Nilai Kontrak</small> -->
                    </div>
                    <div class="media-right media-middle">
                        <i class="fa fa-question-circle dark" data-toggle="tooltip" data-placement="top" title="Total Serah Terima" style="font-size:1.3em !important; cursor: pointer;"></i>
                    </div>
                </div>
                <div class="mt-1 text-center">
                  <?= '<small style="font-weight:bold">'.$countKontrakSelesai->countRow().' Paket</small>'; ?> <i class="fa fa-arrow-circle-right"></i>
                </div>
            </div>
        </div>
      </div>
    </div>    

    <!-- <div class="col-md-2">
      <div class="card box-shadow-0 animated zoomIn wfont" data-appear="appear" data-animation="zoomIn" style="background-color: #68A800;">
        <div class="card-content">
            <div class="card-body" style="cursor: pointer">
                <div class="media">
                    <div class="media-body text-center">
                        <span style="margin-top: 15%;">PEMBAYARAN</span>
                        <h2 class="wfont mt-2">
                            <div class="border border-light" style="font-size:0.85rem;border-radius:4px;color:white; padding:10px">Belum Terintegrasi dengan Sistem Pembayaran</div>
                        </h2>
                    </div> 
                </div>
                <div class="text-center">
                  &nbsp;
                </div>
            </div>
        </div>
      </div>
    </div>   --> 

    <!-- ***********************************
    ******  
    *************************************-->
    <?php 
    $countTender = new Dashpaket();
    $sumTender = new Dashpaket();
    $sumTenderFinal = new Dashpaket();
    $countTenderTerbatas = new Dashpaket();
    $sumTenderTerbatas = new Dashpaket();
    $sumTenderTerbatasFinal = new Dashpaket();
    $countTenderCepat = new Dashpaket();
    $sumTenderCepat = new Dashpaket();
    $sumTenderCepatFinal = new Dashpaket();
    $countPengadaanLangsung = new Dashpaket();
    $sumPengadaanLangsung = new Dashpaket();
    $sumPengadaanLangsungFinal = new Dashpaket();
    $countPenunjukanLangsung = new Dashpaket();
    $sumPenunjukanLangsung = new Dashpaket();
    $sumPenunjukanLangsungFinal = new Dashpaket();
    $countPembelianLangsung = new Dashpaket();
    $sumPembelianLangsung = new Dashpaket();
    $sumPembelianLangsungFinal = new Dashpaket();
    $countTenderKualifikasi = new Dashpaket();
    $sumTenderKualifikasi = new Dashpaket();
    $sumTenderKualifikasiFinal = new Dashpaket();
    $countPurchasingPemerintah = new Dashpaket();
    $sumPurchasingPemerintah = new Dashpaket();
    $sumPurchasingPemerintahFinal = new Dashpaket();
    $countOffline = new Dashpaket();
    $sumOffline = new Dashpaket();
    $sumOfflineFinal = new Dashpaket();

    if ($getTahun != 'all'){
        $countTender = $countTender->getCountByParams(array("A.PAKET_METODE_LELANG_ID" => "1", "TAHUN_ANGGARAN" => $getTahun));
        $sumTender = $sumTender->getSumByParams(array("A.PAKET_METODE_LELANG_ID" => "1", "TAHUN_ANGGARAN" => $getTahun));
        $sumTenderFinal = $sumTenderFinal->getSumFinalByParams(array("A.PAKET_METODE_LELANG_ID" => "1", "TAHUN_ANGGARAN" => $getTahun));
        // echo $countTender; die;
        $countTenderTerbatas = $countTenderTerbatas->getCountByParams(array("A.PAKET_METODE_LELANG_ID" => "3", "TAHUN_ANGGARAN" => $getTahun));
        $sumTenderTerbatas = $sumTenderTerbatas->getSumByParams(array("A.PAKET_METODE_LELANG_ID" => "3", "TAHUN_ANGGARAN" => $getTahun));
        $sumTenderTerbatasFinal = $sumTenderTerbatasFinal->getSumFinalByParams(array("A.PAKET_METODE_LELANG_ID" => "3", "TAHUN_ANGGARAN" => $getTahun));
        
        $countTenderCepat = $countTenderCepat->getCountByParams(array("A.PAKET_METODE_LELANG_ID" => "7", "TAHUN_ANGGARAN" => $getTahun));
        $sumTenderCepat = $sumTenderCepat->getSumByParams(array("A.PAKET_METODE_LELANG_ID" => "7", "TAHUN_ANGGARAN" => $getTahun));
        $sumTenderCepatFinal = $sumTenderCepatFinal->getSumFinalByParams(array("A.PAKET_METODE_LELANG_ID" => "7", "TAHUN_ANGGARAN" => $getTahun));
        
        $countPengadaanLangsung = $countPengadaanLangsung->getCountByParams(array("A.PAKET_METODE_LELANG_ID" => "2", "TAHUN_ANGGARAN" => $getTahun));
        $sumPengadaanLangsung = $sumPengadaanLangsung->getSumByParams(array("A.PAKET_METODE_LELANG_ID" => "2", "TAHUN_ANGGARAN" => $getTahun));
        $sumPengadaanLangsungFinal = $sumPengadaanLangsungFinal->getSumFinalByParams(array("A.PAKET_METODE_LELANG_ID" => "2", "TAHUN_ANGGARAN" => $getTahun));
        
        $countPenunjukanLangsung = $countPenunjukanLangsung->getCountByParams(array("A.PAKET_METODE_LELANG_ID" => "5", "TAHUN_ANGGARAN" => $getTahun));
        $sumPenunjukanLangsung = $sumPenunjukanLangsung->getSumByParams(array("A.PAKET_METODE_LELANG_ID" => "5", "TAHUN_ANGGARAN" => $getTahun));
        $sumPenunjukanLangsungFinal = $sumPenunjukanLangsungFinal->getSumFinalByParams(array("A.PAKET_METODE_LELANG_ID" => "5", "TAHUN_ANGGARAN" => $getTahun));
        
        $countPembelianLangsung = $countPembelianLangsung->getCountByParams(array("A.PAKET_METODE_LELANG_ID" => "6", "TAHUN_ANGGARAN" => $getTahun));
        $sumPembelianLangsung = $sumPembelianLangsung->getSumByParams(array("A.PAKET_METODE_LELANG_ID" => "6", "TAHUN_ANGGARAN" => $getTahun));
        $sumPembelianLangsungFinal = $sumPembelianLangsungFinal->getSumFinalByParams(array("A.PAKET_METODE_LELANG_ID" => "6", "TAHUN_ANGGARAN" => $getTahun));
        
        $countTenderKualifikasi = $countTenderKualifikasi->getCountByParams(array("A.PAKET_METODE_LELANG_ID" => "10", "TAHUN_ANGGARAN" => $getTahun));
        $sumTenderKualifikasi = $sumTenderKualifikasi->getSumByParams(array("A.PAKET_METODE_LELANG_ID" => "10", "TAHUN_ANGGARAN" => $getTahun));
        $sumTenderKualifikasiFinal = $sumTenderKualifikasiFinal->getSumFinalByParams(array("A.PAKET_METODE_LELANG_ID" => "10", "TAHUN_ANGGARAN" => $getTahun));
        
        $countOffline = $countOffline->getCountByParams(array("A.PAKET_METODE_LELANG_ID" => "9", "TAHUN_ANGGARAN" => $getTahun));
        $sumOffline = $sumOffline->getSumByParams(array("A.PAKET_METODE_LELANG_ID" => "9", "TAHUN_ANGGARAN" => $getTahun));
        $sumOfflineFinal = $sumOfflineFinal->getSumFinalByParams(array("A.PAKET_METODE_LELANG_ID" => "9", "TAHUN_ANGGARAN" => $getTahun));

        $countPurchasingPemerintah = $countPurchasingPemerintah->getCountByParams(array("A.PAKET_METODE_LELANG_ID" => "12", "TAHUN_ANGGARAN" => $getTahun));
        $sumPurchasingPemerintah = $sumPurchasingPemerintah->getSumByParams(array("A.PAKET_METODE_LELANG_ID" => "12", "TAHUN_ANGGARAN" => $getTahun));
        $sumPurchasingPemerintahFinal = $sumPurchasingPemerintahFinal->getSumFinalByParams(array("A.PAKET_METODE_LELANG_ID" => "12", "TAHUN_ANGGARAN" => $getTahun));
    } else {
        $tahun = '';
        $countTender = $countTender->getCountByParams(array("A.PAKET_METODE_LELANG_ID" => "1"));
        $sumTender = $sumTender->getSumByParams(array("A.PAKET_METODE_LELANG_ID" => "1"));
        $sumTenderFinal = $sumTenderFinal->getSumFinalByParams(array("A.PAKET_METODE_LELANG_ID" => "1"));

        $countTenderTerbatas = $countTenderTerbatas->getCountByParams(array("A.PAKET_METODE_LELANG_ID" => "3"));
        $sumTenderTerbatas = $sumTenderTerbatas->getSumByParams(array("A.PAKET_METODE_LELANG_ID" => "3"));
        $sumTenderTerbatasFinal = $sumTenderTerbatasFinal->getSumFinalByParams(array("A.PAKET_METODE_LELANG_ID" => "3"));

        $countTenderCepat = $countTenderCepat->getCountByParams(array("A.PAKET_METODE_LELANG_ID" => "7"));
        $sumTenderCepat = $sumTenderCepat->getSumByParams(array("A.PAKET_METODE_LELANG_ID" => "7"));
        $sumTenderCepatFinal = $sumTenderCepatFinal->getSumFinalByParams(array("A.PAKET_METODE_LELANG_ID" => "7"));

        $countPengadaanLangsung = $countPengadaanLangsung->getCountByParams(array("A.PAKET_METODE_LELANG_ID" => "2"));
        $sumPengadaanLangsung = $sumPengadaanLangsung->getSumByParams(array("A.PAKET_METODE_LELANG_ID" => "2"));
        $sumPengadaanLangsungFinal = $sumPengadaanLangsungFinal->getSumFinalByParams(array("A.PAKET_METODE_LELANG_ID" => "2"));

        $countPenunjukanLangsung = $countPenunjukanLangsung->getCountByParams(array("A.PAKET_METODE_LELANG_ID" => "5"));
        $sumPenunjukanLangsung = $sumPenunjukanLangsung->getSumByParams(array("A.PAKET_METODE_LELANG_ID" => "5"));
        $sumPenunjukanLangsungFinal = $sumPenunjukanLangsungFinal->getSumFinalByParams(array("A.PAKET_METODE_LELANG_ID" => "5"));

        $countPembelianLangsung = $countPembelianLangsung->getCountByParams(array("A.PAKET_METODE_LELANG_ID" => "6"));
        $sumPembelianLangsung = $sumPembelianLangsung->getSumByParams(array("A.PAKET_METODE_LELANG_ID" => "6"));
        $sumPembelianLangsungFinal = $sumPembelianLangsungFinal->getSumFinalByParams(array("A.PAKET_METODE_LELANG_ID" => "6"));

        $countTenderKualifikasi = $countTenderKualifikasi->getCountByParams(array("A.PAKET_METODE_LELANG_ID" => "10"));
        $sumTenderKualifikasi = $sumTenderKualifikasi->getSumByParams(array("A.PAKET_METODE_LELANG_ID" => "10"));
        $sumTenderKualifikasiFinal = $sumTenderKualifikasiFinal->getSumFinalByParams(array("A.PAKET_METODE_LELANG_ID" => "10"));

        $countOffline = $countOffline->getCountByParams(array("A.PAKET_METODE_LELANG_ID" => "9"));
        $sumOffline = $sumOffline->getSumByParams(array("A.PAKET_METODE_LELANG_ID" => "9"));
        $sumOfflineFinal = $sumOfflineFinal->getSumFinalByParams(array("A.PAKET_METODE_LELANG_ID" => "9"));

        $countPurchasingPemerintah = $countPurchasingPemerintah->getCountByParams(array("A.PAKET_METODE_LELANG_ID" => "12"));
        $sumPurchasingPemerintah = $sumPurchasingPemerintah->getSumByParams(array("A.PAKET_METODE_LELANG_ID" => "12"));
        $sumPurchasingPemerintahFinal = $sumPurchasingPemerintahFinal->getSumFinalByParams(array("A.PAKET_METODE_LELANG_ID" => "12"));

    }
    $sum =  $sumTender + $sumTenderTerbatas + $sumTenderCepat + $sumPengadaanLangsung + $sumPenunjukanLangsung + $sumPembelianLangsung + $sumTenderKualifikasi + $sumOffline + $sumPurchasingPemerintah;
    $sumFinal =  $sumTenderFinal + $sumTenderTerbatasFinal + $sumTenderCepatFinal + $sumPengadaanLangsungFinal + $sumPenunjukanLangsungFinal + $sumPembelianLangsungFinal + $sumTenderKualifikasiFinal + $sumOfflineFinal + $sumPurchasingPemerintah;
    $count =  $countTender + $countTenderTerbatas + $countTenderCepat + $countPengadaanLangsung + $countPenunjukanLangsung + $countPembelianLangsung + $countTenderKualifikasi + $countOffline + $countPurchasingPemerintah;
    // echo $count;
    ?>

    <div class="col-md-12">
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
                    HARGA PERKIRAAN & FINAL/AKHIR</a>
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
            labels: ["Tender", "Tender Cepat", "Tender Terbatas", "Tender Kualifikasi", "Penunjukan Langsung", "Pengadaan Langsung", "Pembelian Katalog", "Pembelian Langsung","Pembelian Katalog Pemerintah"],
            datasets: [
              {
                label: "Harga Perkiraan",
                data: [<?= $sumTender ?>,<?= $sumTenderCepat ?>,<?= $sumTenderTerbatas ?>,<?= $sumTenderKualifikasi ?>,<?= $sumPenunjukanLangsung ?>,<?= $sumPengadaanLangsung ?>,<?= $sumPembelianLangsung ?>,<?= $sumOffline ?>,<?= $sumPurchasingPemerintah ?>],
                backgroundColor: ["#009ab0","#82efee","#c9a377","#128a08","#de9932","#be6d40","3057e3","#e3d805","#0cc7c4"],
              },
              {
                label: "Harga Final/Fix",
                data: [<?= $sumTenderFinal ?>,<?= $sumTenderCepatFinal ?>,<?= $sumTenderTerbatasFinal ?>,<?= $sumTenderKualifikasiFinal ?>,<?= $sumPenunjukanLangsungFinal ?>,<?= $sumPengadaanLangsungFinal ?>,<?= $sumPembelianLangsungFinal ?>,<?= $sumOfflineFinal ?>,<?= $sumPurchasingPemerintah ?>],
                backgroundColor: ["#14b0c6","#b6fbfa","#cfbca6","#41ab38","#f3b75c","#e79465","6d8af2","#faf5a0","#5aedeb"],
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
            labels: ["Tender", "Tender Cepat", "Tender Terbatas", "Tender Kualifikasi", "Penunjukan Langsung", "Pengadaan Langsung", "Pembelian Katalog", "Pembelian Langsung","Pembelian Katalog Pemerintah"],
            datasets: [
              {
                label: "Total",
                data: [<?= $countTender ?>,<?= $countTenderCepat ?>,<?= $countTenderTerbatas ?>,<?= $countTenderKualifikasi ?>,<?= $countPenunjukanLangsung ?>,<?= $countPengadaanLangsung ?>,<?= $countPembelianLangsung ?>,<?= $countOffline ?>,<?= $countPurchasingPemerintah ?>],
                backgroundColor: ["#009ab0","#82efee","#c9a377","#128a08","#de9932","#be6d40","3057e3","#e3d805","#0cc7c4"],
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
  LAPORAN BERDASARKAN BEBAN KERJA PAKET
----------------------------------------------------------------------------------------------------------------
-->
    <?php
    $arrayIdManager = array(571,572);

    foreach ($arrayIdManager as $key => $value) { 
        // Laporan Berdasarkan Beban Kerja Paket
        $getUser = new UserLogin();
        $getUser->selectByParams(array("USER_LOGIN_ID" => $value));
        $getUser->firstRow();
        $userVPPengadaan1 = $getUser->getField("USER_NAMA");
        $getDataChartGauge = new Dashpaket();
        if ($getTahun != 'all'){
          $getDataChartGauge->getDashboardGauge($unitkerja,$value,$getTahun);
          $getDataChartGauge->firstRow();
          $totalPaketGauge = $getDataChartGauge->getField('TOTAL_PAKET') ?: 0;
          $totalPaketProsesGauge = $getDataChartGauge->getField('TOTAL_PAKET_PROSES') ?: 0;
        } else {
          $getDataChartGauge->getDashboardGauge($unitkerja,$value);
          $getDataChartGauge->firstRow();
          $totalPaketGauge = $getDataChartGauge->getField('TOTAL_PAKET') ?: 0;
          $totalPaketProsesGauge = $getDataChartGauge->getField('TOTAL_PAKET_PROSES') ?: 0;
        }
        if ($totalPaketProsesGauge > 0) {
            $persen = $totalPaketProsesGauge / $totalPaketGauge * 100;
        } else {
            $persen = 0;
        }
        // EndLaporan Berdasarkan Beban Kerja Paket
        ?>
        <script type="module">
        import * as GaugeChart from 'https://unpkg.com/gauge-chart@next/dist/bundle.mjs'
        $(function() {
          // https://github.com/recogizer/gauge-chart
          // https://recogizer.github.io/gauge-chart/examples/samples/
          // Element inside which you want to see the chart
          let element = document.querySelector('#gaugeArea<?= $key ?>')
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
          // Drawing and updating the chart
          GaugeChart.gaugeChart(element, 533, options).updateNeedle($persen);

          $(window).on("load", function(){
              document.getElementById("gaugeArea<?= $key ?>").onclick = function (evt) {
                var tahun   = $('#tahun').val();
                var url = 'main/loadUrl/main/dashboard_detail_gauge_new/?tahun='+tahun+'&uki=<?= $unitkerja ?>&type='.$value;
                openAdd(url);
              };
          });
        });
        </script>

        <div class="col-md-6">
            <div class="card">
              <div class="card-header card-head-inverse bg-primary">
                <h4 class="card-title text-white" style="font-size:.9em !important">Laporan Berdasarkan Beban Kerja P1 = <?= $totalPaketGauge  ?> Paket <?= $tahun ?> </h4>
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
                  <div id="gaugeArea<?= $key ?>" style="margin-bottom: -50px"></div>
                <small><b>Jumlah Paket Yang Sedang Diproses P1 (<?= $userVPPengadaan1 ?>) </b></small>
              </div>
            </div>
        </div>
    <?php 
    } ?>
     

<!--
----------------------------------------------------------------------------------------------------------------
  LAPORAN BERDASARKAN REALISASI PAKET
----------------------------------------------------------------------------------------------------------------
-->
    <?php
    // Laporan Berdasarkan Realisasi Paket
    $getDataChartBar2 = new Dashpaket();
    if ($getTahun != 'all'){
      $getDataChartBar2->getDashboardBar2($unitkerja,$getTahun);
    } else {
      $getDataChartBar2->getDashboardBar2($unitkerja);
    } 
    // echo $getDataChartBar2->query;
    while($getDataChartBar2->nextRow())
    {
        // $bar2Label[] = $getDataChartBar2->getField('user_nama').' ('.$getDataChartBar2->getField('total_rencana').' Paket)';
        // $bar2Label[] = $getDataChartBar2->getField('user_nama');
        // $bar2Label2[] = $getDataChartBar2->getField('department');
        $bar2Label2[] = $getDataChartBar2->getField('nama');
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
                     $key=0;
                     foreach ($bar2Label2 as $valBar2Label):
                      // echo '["'.$valBar2Label.' ('.$bar2Label2[$key].')"],';
                      echo '["'.$valBar2Label.'"],';
                     $key++;
                     endforeach ?>
                  ];

      var bar_ctx = document.getElementById('bar-chart');

      var bar_chart = new Chart(bar_ctx, {
          type: 'horizontalBar',
          data: {
              labels: dates,
              datasets: [
              {
                  label: 'Sisa Paket Rencana',
                  data: dataPack1,
                  backgroundColor: "#dd2d4a",
                  hoverBackgroundColor: "#880d1e",
                  hoverBorderWidth: 0,
              },
              {
                  label: 'Paket Realisasi',
                  data: dataPack2,
                  backgroundColor: "#588157",
                  hoverBackgroundColor: "#3a5a40",
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
                label: function(tooltipItems, data) {
                  // return data.datasets[tooltipItem.datasetIndex].label + ": " + numberWithCommas(tooltipItem.yLabel);
                  return data.datasets[tooltipItems.datasetIndex].label +': ' + number_format(tooltipItems.xLabel, 0, ',', '.')+' Paket';
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
        var url = 'main/loadUrl/main/dashboard_detail_bar/?direktorat='+a+'&total='+b+'&jenis='+c+'&tahun='+d;
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

<!--
----------------------------------------------------------------------------------------------------------------
  LAPORAN BERDASARKAN JENIS PENGADAAN
----------------------------------------------------------------------------------------------------------------
-->

    <?php 
    $dash1 = new Dashpaket();
    $dash2 = new Dashpaket();
    $dash3 = new Dashpaket();
    $dash4 = new Dashpaket();
    $dash5 = new Dashpaket();

    if ($getTahun != 'all'){
        $total_jenis_barangjasa_katalog = $dash5->getCountByParams(array('PAKET_JENIS_ID' => '5', 'TAHUN_ANGGARAN' => $getTahun));
        $total_jenis_barangjasa_jasa_lain = $dash1->getCountByParams(array('PAKET_JENIS_ID' => '4', 'TAHUN_ANGGARAN' => $getTahun));
        $total_jenis_barangjasa_barang = $dash2->getCountByParams(array('PAKET_JENIS_ID' => '3', 'TAHUN_ANGGARAN' => $getTahun));
        $total_jenis_barangjasa_konsultansi = $dash3->getCountByParams(array('PAKET_JENIS_ID' => '2', 'TAHUN_ANGGARAN' => $getTahun));
        $total_jenis_barangjasa_konstruksi = $dash4->getCountByParams(array('PAKET_JENIS_ID' => '1', 'TAHUN_ANGGARAN' => $getTahun));
    } else {
        $total_jenis_barangjasa_katalog = $dash5->getCountByParams(array('PAKET_JENIS_ID' => '5'));
        $total_jenis_barangjasa_jasa_lain = $dash1->getCountByParams(array('PAKET_JENIS_ID' => '4'));
        $total_jenis_barangjasa_barang = $dash2->getCountByParams(array('PAKET_JENIS_ID' => '3'));
        $total_jenis_barangjasa_konsultansi = $dash3->getCountByParams(array('PAKET_JENIS_ID' => '2'));
        $total_jenis_barangjasa_konstruksi = $dash4->getCountByParams(array('PAKET_JENIS_ID' => '1'));
    }

    ?>

    <script type="text/javascript"> 
      $(window).on("load", function(){
          require.config({
              paths: {
                  echarts: '<?=base_url()?>assets/new/vendors/js/charts/echarts'
              }
          });

          require(
              [ 'echarts', 'echarts/chart/pie', 'echarts/chart/funnel', 'echarts/chart/bar' ],

              // Bug charts setup
              function (ec) {
                  var bugChart = ec.init(document.getElementById('bug-pie-chart'));

                  chartOptions = {
                      tooltip: {
                          trigger: 'item',
                          // formatter: "{a} <br/>{b}: {c} ({d}%)"
                          formatter: "{b}: {c} ({d}%)"
                      },

                      // Add legend 
                      legend: {
                          orient: 'horizontal',
                          x: 'left',
                          data: ['Barang', 'Jasa Konsultansi', 'Pekerjaan Konstruksi', 'Jasa Lainnya','Katalog'],
                          show: false
                      },

                      // Add custom colors
                      // color: ['#FECEA8', '#FF847C', '#E84A5F','#759773', '#99B898','#afc8ae'],
                      color: ['#FECEA8', '#FF847C', '#E84A5F','#759773','#89c2d9'],

                      // Display toolbox
                      toolbox: {
                          show: true,
                          orient: 'horizontal',
                          x: 'left',
                          //Enable if you need
                          feature: {
                              magicType: {
                                  show: true,
                                  title: {
                                      pie: 'Switch to pies',
                                      funnel: 'Switch to funnel',
                                  },
                                  type: ['pie', 'funnel'],
                                  option: {
                                      funnel: {
                                          x: '25%',
                                          y: '20%',
                                          width: '50%',
                                          height: '70%',
                                          funnelAlign: 'left',
                                          max: 1548
                                      } 
                                  }
                              },
                              restore: {
                                  show: true,
                                  title: 'Restore'
                              },
                              saveAsImage: {
                                  show: true,
                                  title: 'Same as image',
                                  lang: ['Save']
                              }
                          }
                      },

                      // Enable drag recalculate
                      calculable: true,

                      // Add series
                      series: [{
                          name: '',
                          type: 'pie',
                          radius: '70%',
                          center: ['53%', '50.5%'],
                          data: [
                              {value: <?= $total_jenis_barangjasa_barang ?>, name: 'Barang'},
                              {value: <?= $total_jenis_barangjasa_konsultansi ?>, name: 'Jasa Konsultansi'},
                              {value: <?= $total_jenis_barangjasa_konstruksi ?>, name: 'Pekerjaan Konstruksi'},
                              {value: <?= $total_jenis_barangjasa_jasa_lain ?>, name: 'Jasa Lainnya'},
                              {value: <?= $total_jenis_barangjasa_katalog ?>, name: 'Katalog'},
                          ]
                      }]
                  };

                  bugChart.setOption(chartOptions);

                  $(function () {
                      // Resize chart on menu width change and window resize
                      $(window).on('resize', resize);
                      $(".menu-toggle").on('click', resize);
                        // Resize function
                      function resize() {
                          setTimeout(function() {
                              bugChart.resize();
                          }, 100);
                      }
                  });
              }
          );
      });
    </script> 

    <div class="col-md-5">
        <div class="card">
          <div class="card-header card-head-inverse bg-primary">
            <h4 class="card-title text-white" style="font-size:.9em !important">Laporan Berdasarkan Jenis Barang/Jasa <?= $tahun ?> </h4>
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
                <h4 class="text-center">Jenis Barang/Jasa <br><small><?= $tahun ?></small></h4>
                <div id="bug-pie-chart" class="height-300 echart-container"></div>
            </div>
          </div>
        </div>
    </div>

    <!--
    ----------------------------------------------------------------------------------------------------------------
    LAPORAN BERDASARKAN PENYEDIA
    ----------------------------------------------------------------------------------------------------------------
    -->
    <link rel="stylesheet" type="text/css" href="<?=base_url() ?>lib/DataTables-1.10.7/media/css/jquery.dataTables.css">
    <link rel="stylesheet" type="text/css" href="<?=base_url() ?>lib/DataTables-1.10.7/extensions/Responsive/css/dataTables.responsive.css">
    <link rel="stylesheet" type="text/css" href="<?=base_url() ?>lib/DataTables-1.10.7/examples/resources/syntax/shCore.css">
    <link rel="stylesheet" type="text/css" href="<?=base_url() ?>lib/DataTables-1.10.7/examples/resources/demo.css">
    <script type="text/javascript" language="javascript" src="<?=base_url() ?>lib/DataTables-1.10.7/media/js/jquery.dataTables.js"></script>
    <script type="text/javascript" language="javascript" src="<?=base_url() ?>lib/DataTables-1.10.7/extensions/Responsive/js/dataTables.responsive.js"></script>
    <script type="text/javascript" language="javascript" src="<?=base_url() ?>lib/DataTables-1.10.7/examples/resources/syntax/shCore.js"></script>
    <script type="text/javascript" language="javascript" src="<?=base_url() ?>lib/DataTables-1.10.7/examples/resources/demo.js"></script>
      <script type="text/javascript" language="javascript" class="init">
        $(document).ready(function() {
          $('#penyediaDash').DataTable({
            "iDisplayLength": 5,
            "aaSorting": [[1, 'desc']],
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
             <h4 class="card-title text-white" style="font-size:.9em !important">Laporan Penyedia Terkontrak</h4>
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
                <table id="penyediaDash" class="border-double table mb-0 table-bordered" style="width: 100%;">
                  <thead>
                    <tr>
                      <th class="text-left">Penyedia</th>
                      <th width="10%">Kontrak</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                    $getDataPenyediaKontrak = new Queryfree(); 
                    if ($getTahun == 'all') {
                        $getDataPenyediaKontrak->selectByParams("SELECT a.*, c.nama || '. ' || b.nama penyedia 
                                                             FROM (
                                                                select count(a.paket_id) total, a.rekanan_id 
                                                                from contracting_rekanan_proses1 a  
                                                                group by a.rekanan_id
                                                             ) a
                                                             JOIN rekanan b ON a.rekanan_id=b.rekanan_id
                                                             JOIN rekanan_tipe c on b.rekanan_tipe_id=c.rekanan_tipe_id
                                                            ");
                    } else {
                        $getDataPenyediaKontrak->selectByParams("SELECT a.*, c.nama || '. ' || b.nama penyedia 
                                                             FROM (
                                                                select count(a.paket_id) total, a.rekanan_id 
                                                                from contracting_rekanan_proses1 a
                                                                where date_part('year'::text, a.cr_sppbj_tanggal) = '".$getTahun."' 
                                                                group by a.rekanan_id
                                                             ) a
                                                             JOIN rekanan b ON a.rekanan_id=b.rekanan_id
                                                             JOIN rekanan_tipe c on b.rekanan_tipe_id=c.rekanan_tipe_id
                                                            ");
                    }
                    // $getDataPenyediaKontrak->firstRow();
                    while($getDataPenyediaKontrak->nextRow())
                    { ?>
                    <tr>
                      <td class="text-left">
                        <a onclick="openAdd('main/loadUrl/main/dashboard_detail_new/?jenis=terkontrak&tahun=<?= $getTahun ?>&rekananid=<?=$getDataPenyediaKontrak->getField('rekanan_id')?>')"><?= $getDataPenyediaKontrak->getField('penyedia'); ?>
                        </a>
                     </td> 
                      <td>
                        <?php  if($getDataPenyediaKontrak->getField('total') > 0 ) { echo $getDataPenyediaKontrak->getField('total').' kali'; } else { echo '0'; } ?>
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
 
</div>

<script src="<?=base_url()?>assets/new/vendors/js/charts/echarts/echarts.js"></script>

