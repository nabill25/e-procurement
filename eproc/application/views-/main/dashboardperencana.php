<?php
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 * 
 */
$this->libsession->cekSession();

include_once("functions/string.func.php");
include_once("functions/date.func.php");

// $this->load->model("Paketperencanadash");
$this->load->model("DashPermohonanPaketui");
$this->load->model("Dashpaket");
$unitkerja  = $this->UNIT_KERJA_ID;


$sumPaguRUP = new DashPermohonanPaketui();
$countRUP = new DashPermohonanPaketui();
$countPersiapan = new DashPermohonanPaketui();
$sumHPSRUP = new DashPermohonanPaketui();

$getTahun = isset($_GET['tahun']) ? $_GET['tahun'] : date('Y');

if ($this->KODE_SA && $this->KODE_DPSJ)
{
    // $getTahun = $_GET['tahun'];
    if ($getTahun != 'all'){
     $tahun = 'Tahun '.$getTahun; 
     $countRUP->selectByParams(array(),-1,-1," AND A.PERMOHONAN_PAKET_ID IS NULL AND A.KODE_SA IN (".$this->KODE_SA.") AND A.KODE_DPSJ IN (".$this->KODE_DPSJ.") AND A.TAHUN = '".$getTahun."' ");
     $countTotalRUP = $countRUP->countRow();
     $sumPaguRUP->sumPaguByParams(array(),-1,-1," AND A.PERMOHONAN_PAKET_ID IS NULL AND A.KODE_SA IN (".$this->KODE_SA.") AND A.KODE_DPSJ IN (".$this->KODE_DPSJ.") AND A.TAHUN = '".$getTahun."' ");
     $sumPaguRUP->firstRow();
     $sumTotalPaguRUP = $sumPaguRUP->getField('TOTAL_PAGU');
     $sumTotalRABRUP = $sumPaguRUP->getField('TOTAL_RAB');

     $sumHPSRUP->sumHpsByParams(array("A.CREATED_BY" => $this->USER_LOGIN_ID, "A.TAHUN_ANGGARAN" => $getTahun),-1,-1);
     $sumHPSRUP->firstRow();
     $sumTotalHPSRUP = $sumHPSRUP->getField('NILAI_HPS_PR');

     // Badge
     $countPersiapan->selectByParamsPersiapan(array("A.CREATED_BY" => $this->USER_LOGIN_ID),-1,-1," AND A.APPROVAL_PERMOHONAN_PAKET_ANALISA IN ('1') AND A.TAHUN_ANGGARAN = '".$getTahun."' ");
     $status1 = $countPersiapan->countRow();
     $countPersiapan->selectByParamsPersiapan(array("A.CREATED_BY" => $this->USER_LOGIN_ID),-1,-1," AND A.APPROVAL_PERMOHONAN_PAKET_ANALISA IN ('2') AND A.TAHUN_ANGGARAN = '".$getTahun."' ");
     $status2 = $countPersiapan->countRow();
     $countPersiapan->selectByParamsPersiapan(array("A.CREATED_BY" => $this->USER_LOGIN_ID),-1,-1," AND A.APPROVAL_PERMOHONAN_PAKET_ANALISA IN ('3') AND A.TAHUN_ANGGARAN = '".$getTahun."' ");
     $status3 = $countPersiapan->countRow();
     $countPersiapan->selectByParamsPersiapan(array("A.CREATED_BY" => $this->USER_LOGIN_ID),-1,-1," AND A.APPROVAL_PERMOHONAN_PAKET_ANALISA IN ('4') AND A.TAHUN_ANGGARAN = '".$getTahun."' ");
     $status4 = $countPersiapan->countRow();
     $countPersiapan->selectByParamsPersiapan(array("A.CREATED_BY" => $this->USER_LOGIN_ID),-1,-1," AND A.APPROVAL_PERMOHONAN_PAKET_ANALISA IN ('5') AND A.TAHUN_ANGGARAN = '".$getTahun."' ");
     $status5 = $countPersiapan->countRow();
     $countPersiapan->selectByParamsPersiapan(array("A.CREATED_BY" => $this->USER_LOGIN_ID),-1,-1," AND A.APPROVAL_PERMOHONAN_PAKET_ANALISA IN ('6') AND A.TAHUN_ANGGARAN = '".$getTahun."' ");
     $status6 = $countPersiapan->countRow();

    } else {
     $tahun = '';
     $countRUP->selectByParams(array(),-1,-1,"  AND A.PERMOHONAN_PAKET_ID IS NULL AND A.KODE_SA IN (".$this->KODE_SA.") AND A.KODE_DPSJ IN (".$this->KODE_DPSJ.")");
     $countTotalRUP = $countRUP->countRow();
     $sumPaguRUP->sumPaguByParams(array(),-1,-1," AND A.PERMOHONAN_PAKET_ID IS NULL AND A.KODE_SA IN (".$this->KODE_SA.") AND A.KODE_DPSJ IN (".$this->KODE_DPSJ.") ");
     $sumPaguRUP->firstRow();
     $sumTotalPaguRUP = $sumPaguRUP->getField('TOTAL_PAGU');
     $sumTotalRABRUP = $sumPaguRUP->getField('TOTAL_RAB');

     $sumHPSRUP->sumHpsByParams(array("A.CREATED_BY" => $this->USER_LOGIN_ID),-1,-1);
     $sumHPSRUP->firstRow();
     $sumTotalHPSRUP = $sumHPSRUP->getField('NILAI_HPS_PR');

     // Badge
     $countPersiapan->selectByParamsPersiapan(array("A.CREATED_BY" => $this->USER_LOGIN_ID),-1,-1," AND A.APPROVAL_PERMOHONAN_PAKET_ANALISA IN ('1')");
     $status1 = $countPersiapan->countRow();
     $countPersiapan->selectByParamsPersiapan(array("A.CREATED_BY" => $this->USER_LOGIN_ID),-1,-1," AND A.APPROVAL_PERMOHONAN_PAKET_ANALISA IN ('2')");
     $status2 = $countPersiapan->countRow();
     $countPersiapan->selectByParamsPersiapan(array("A.CREATED_BY" => $this->USER_LOGIN_ID),-1,-1," AND A.APPROVAL_PERMOHONAN_PAKET_ANALISA IN ('3')");
     $status3 = $countPersiapan->countRow();
     $countPersiapan->selectByParamsPersiapan(array("A.CREATED_BY" => $this->USER_LOGIN_ID),-1,-1," AND A.APPROVAL_PERMOHONAN_PAKET_ANALISA IN ('4')");
     $status4 = $countPersiapan->countRow();
     $countPersiapan->selectByParamsPersiapan(array("A.CREATED_BY" => $this->USER_LOGIN_ID),-1,-1," AND A.APPROVAL_PERMOHONAN_PAKET_ANALISA IN ('5')");
     $status5 = $countPersiapan->countRow();
     $countPersiapan->selectByParamsPersiapan(array("A.CREATED_BY" => $this->USER_LOGIN_ID),-1,-1," AND A.APPROVAL_PERMOHONAN_PAKET_ANALISA IN ('6')");
     $status6 = $countPersiapan->countRow();
    }
}
 
?>

<script type="text/javascript"> 

$(function () {
    $('[data-toggle="tooltip"]').tooltip({
        placement: 'right'
    });
});

</script>

<style type="text/css">
.chart-content { padding: 5px; margin: 10px; }
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
.wfont, .ft-info, a .wfont, a .ft-info, .media-body, .fa-question-circle, .card-body small { color: #fff !important; }
.border-right { border-right: 1px solid #dee2e6!important; }
.description-block { display: block; margin: 10px 0; text-align: center; }
</style>

<div class="row">
  <div class="form-group col-md-3">
    <!-- <label>Pilih Tahun</label> -->
    <select class="form-control" id="setyear" onChange="return window.location = $(this).val()">
      <?php
      $selected = '';
      $url = base_url('main/index/dashboardperencana?tahun=');
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
        <h5><b>Dashboard Pengguna 
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
  <div class="col-md-6">
      <div class="card">
        <div class="row">
            <div class="col-md-3 border-right">
                <i class="fa fa-question-circle" data-toggle="tooltip" title="Total RUP <?php if ($getTahun != 'all'){ echo 'Tahun Anggaran '.$getTahun; } ?>" data-placement="top" aria-hidden="true" style="position:absolute;right:10px;padding-top:5px;color:black"></i>
                <div class="description-block">
                    <div class="description-header" style="font-size:2rem;"><?= numberToSimbol($countTotalRUP) ?></div>
                    <span class="description-text">TOTAL RUP</span>
                </div>
            </div> 
            <div class="col-md-4 border-right">
                <i class="fa fa-question-circle" data-toggle="tooltip" title="Total Pagu RUP <?php if ($getTahun != 'all'){ echo 'Tahun Anggaran '.$getTahun; } ?>" data-placement="top" aria-hidden="true" style="position:absolute;right:15px;padding-top:10px;color:black"></i>
                <div class="description-block">
                    <div class="description-header" style="font-size:2rem;" data-toggle="tooltip" data-placement="top" data-original-title="">Rp <?php echo numberToSimbol(round($sumTotalPaguRUP)) ?></div>
                    <span class="description-text">TOTAL PAGU RUP</span>
                </div>
            </div>
            <div class="col-md-4">
                <i class="fa fa-question-circle" data-toggle="tooltip" title="Total Nilai RAB <?php if ($getTahun != 'all'){ echo 'Tahun Anggaran '.$getTahun; } ?>" data-placement="top" aria-hidden="true" style="position:absolute;right:15px;padding-top:10px;color:black"></i> 
                <div class="description-block">
                    <div class="description-header" style="font-size:2rem;" data-toggle="tooltip" data-placement="top" data-original-title="">Rp <?php echo numberToSimbol(round($sumTotalRABRUP)) ?></div>
                    <span class="description-text">TOTAL NILAI RAB</span>
                </div>
            </div>
        </div>
    </div>
  </div> 
  <div class="col-md-2"></div>
  <div class="col-md-4"> 
    <div class="card">
        <div class="row">
            <div class="col-md-5 border-right">
                <i class="fa fa-question-circle" data-toggle="tooltip" title="Total Persiapan <?php if ($getTahun != 'all'){ echo 'Tahun Anggaran '.$getTahun; } ?>" data-placement="top" aria-hidden="true" style="position:absolute;right:10px;padding-top:5px;color:black"></i>
                <div class="description-block">
                    <div class="description-header" style="font-size:2rem;"><?= numberToSimbol($status1 + $status2 + $status3 + $status4 + $status5 + $status6) ?></div>
                    <span class="description-text">TOTAL PERSIAPAN</span>
                </div>
            </div> 
            <div class="col-md-7">
                <i class="fa fa-question-circle" data-toggle="tooltip" title="Total Nilai HPS <?php if ($getTahun != 'all'){ echo 'Tahun Anggaran '.$getTahun; } ?>" data-placement="top" aria-hidden="true" style="position:absolute;right:15px;padding-top:10px;color:black"></i> 
                <div class="description-block">
                    <div class="description-header" style="font-size:2rem;" data-toggle="tooltip" data-placement="top" data-original-title="">Rp <?php echo numberToSimbol(round($sumTotalHPSRUP)) ?></div>
                    <span class="description-text">TOTAL NILAI HPS</span>
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
    <!-- <div class="card"> -->
    <div class="card box-shadow-0 animated zoomIn wfont" data-appear="appear" data-animation="zoomIn" style="background-color: #6C946F;">
        <div class="card-content">
          <a href="<?= base_url('main/index/rencana_umum_pengadaan_persiapan?reqStatus=1') ?>"> 
            <div class="card-body">
                <div class="media">
                    <div class="media-body text-center">
                        <span style="margin-top: 15%;">Approved<br>&nbsp;</span>
                        <h2 class="wfont mt-1"><b><?= $status1 ?> </b></h2>
                    </div>
                </div>
                <div class="text-center">
                  <small style="font-weight:bold">Paket</small>
                </div>
            </div>
          </a>
        </div>
    </div>
  </div>  

  <div class="col-md-2">
    <!-- <div class="card"> -->
    <div class="card box-shadow-0 animated zoomIn wfont" data-appear="appear" data-animation="zoomIn" style="background-color: #7A3E29;">
        <div class="card-content">
          <a href="<?= base_url('main/index/rencana_umum_pengadaan_persiapan?reqStatus=2') ?>"> 
            <div class="card-body">
                <div class="media">
                    <div class="media-body text-center">
                        <span style="margin-top: 15%;">Revisi <br>oleh Unit Kerja</span>
                        <h2 class="wfont mt-1"><b><?= $status2 ?> </b></h2>
                    </div>
                </div>
                <div class="text-center">
                  <small style="font-weight:bold">Paket</small>
                </div>
            </div>
          </a>
        </div>
    </div>
  </div> 

  <div class="col-md-2">
      <div class="card box-shadow-0 animated zoomIn wfont" data-appear="appear" data-animation="zoomIn" style="background-color: #F7941D;">
        <div class="card-content">
            <a href="<?= base_url('main/index/rencana_umum_pengadaan_persiapan?reqStatus=3') ?>"> 
            <div class="card-body">
                <div class="media">
                    <div class="media-body text-center">
                        <span style="margin-top: 15%;">Pengecekan <br> Perencanaan</span>
                        <h2 class="wfont mt-1"><b><?= $status3 ?> </b></h2>
                    </div>
                </div>
                <div class="text-center">
                  <small style="font-weight:bold">Paket</small>
                </div>
            </div>
          </a>
        </div>
      </div>
    </div>   

    <div class="col-md-2">
    <!-- <div class="card"> -->
    <div class="card box-shadow-0 animated zoomIn wfont" data-appear="appear" data-animation="zoomIn" style="background-color: #FF420E;">
        <div class="card-content">
            <a href="<?= base_url('main/index/rencana_umum_pengadaan_persiapan?reqStatus=4') ?>"> 
            <div class="card-body">
                <div class="media">
                    <div class="media-body text-center">
                        <span style="margin-top: 15%;">Revisi Pengecekan <br> Perencanaan</span>
                        <h2 class="wfont mt-1"><b><?= $status4 ?> </b></h2>
                    </div>
                </div>
                <div class="text-center">
                  <small style="font-weight:bold">Paket</small>
                </div>
            </div>
          </a>
        </div>
    </div>
  </div> 

  <div class="col-md-2">
    <!-- <div class="card"> -->
    <div class="card box-shadow-0 animated zoomIn wfont" data-appear="appear" data-animation="zoomIn" style="background-color: #336B87;">
        <div class="card-content">
            <a href="<?= base_url('main/index/rencana_umum_pengadaan_persiapan?reqStatus=5') ?>"> 
            <div class="card-body">
                <div class="media">
                    <div class="media-body text-center">
                        <span style="margin-top: 15%;">Pengecekan <br> Kasubdit</span>
                        <h2 class="wfont mt-1"><b><?= $status5 ?> </b></h2>
                    </div>
                </div>
                <div class="text-center">
                  <small style="font-weight:bold">Paket</small>
                </div>
            </div>
          </a>
        </div>
    </div>
  </div> 

  <div class="col-md-2">
    <!-- <div class="card"> -->
    <div class="card box-shadow-0 animated zoomIn wfont" data-appear="appear" data-animation="zoomIn" style="background-color: #BA5536;">
        <div class="card-content">
            <a href="<?= base_url('main/index/rencana_umum_pengadaan_persiapan?reqStatus=6') ?>"> 
            <div class="card-body">
                <div class="media">
                    <div class="media-body text-center">
                        <span style="margin-top: 15%;">Persetujuan <br>PPK</span>
                        <h2 class="wfont mt-1"><b><?= $status6 ?> </b></h2>
                    </div>
                </div>
                <div class="text-center">
                  <small style="font-weight:bold">Paket</small>
                </div>
            </div>
          </a>
        </div>
    </div>
  </div> 

</div>
