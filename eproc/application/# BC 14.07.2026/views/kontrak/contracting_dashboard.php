<?php 
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 * 
 */

$this->libsession->cekSession();
$this->load->model("Contracting");
$this->load->model("Contractingrekanan");


//kauth
if (!$this->kauth->getInstance()->hasIdentity())
{
  // trow to unauthenticated page!
  //redirect('Login');
}
$this->USER_LOGIN_ID =  $this->kauth->getInstance()->getIdentity()->USER_LOGIN_ID;
$this->USER_LOGIN =  $this->kauth->getInstance()->getIdentity()->USER_LOGIN;
$this->LEGAL =  $this->kauth->getInstance()->getIdentity()->LEGAL;

$count1 = new Contracting();
$sum1 = new Contracting();
$count2 = new Contractingrekanan();
$sum2 = new Contractingrekanan();
$count3 = new Contractingrekanan();
$sum3 = new Contractingrekanan();

$getTahun = isset($_GET['tahun']) ? $_GET['tahun'] : 'all';
// set session untuk set tahun yang di pilih pada dashboard kontrak
$this->session->set_userdata('setTahunKontrak',$getTahun);
// echo $this->session->userdata('setTahunKontrak');

if ($getTahun != 'all'){
  $tahun = 'Tahun '.$getTahun;

  switch ($this->USER_TYPE_ID) {
    case '12': // Legal
      if ($this->LEGAL == '1') {
        $count1 = $count1->getCountByParams(array("A.SELESAI" => '1', "A.PAKET_METODE_LELANG_ID|| IN" => "(1,2,5,6,8)", "TAHUN" => $getTahun,"STATUS_KONTRAK" => "Belum dibuat")); 
        $sum1 = $sum1->getSumByParams(array("A.SELESAI" => '1', "A.PAKET_METODE_LELANG_ID|| IN" => "(1,2,5,6,8)", "TAHUN" => $getTahun, "STATUS_KONTRAK" => "Belum dibuat")); 
        $count2 = $count2->getCountByParams(array("A.CONTRACTINGPROSESID|| IN " => "(1,2,3,4,5)", "extract(year from A.TAHUN_SPPBJ)" => $getTahun)); 
        $sum2 = $sum2->getSumByParams(array("A.CONTRACTINGPROSESID|| IN " => "(1,2,3,4,5)", "extract(year from A.TAHUN_SPPBJ)" => $getTahun)); 
        $count3 = $count3->getCountByParams(array("A.CONTRACTINGPROSESID|| IN " => "(6)", "extract(year from A.TAHUN_SPPBJ)" => $getTahun)); 
        $sum3 = $sum3->getSumByParams(array("A.CONTRACTINGPROSESID|| IN " => "(6)", "extract(year from A.TAHUN_SPPBJ)" => $getTahun)); 
      } else {
        $count1 = $count1->getCountByParams(array("A.SELESAI" => '1', "A.PAKET_METODE_LELANG_ID|| IN" => "(1,2,5,6,8)", "TAHUN" => $getTahun,"STATUS_KONTRAK" => "Belum dibuat","A.PPK" => $this->USER_LOGIN_ID)); 
        $sum1 = $sum1->getSumByParams(array("A.SELESAI" => '1', "A.PAKET_METODE_LELANG_ID|| IN" => "(1,2,5,6,8)", "TAHUN" => $getTahun, "STATUS_KONTRAK" => "Belum dibuat","A.PPK" => $this->USER_LOGIN_ID)); 
        $count2 = $count2->getCountByParams(array("A.CONTRACTINGPROSESID|| IN " => "(1,2,3,4,5)", "A.CREATED_BY" => $this->USER_LOGIN_ID, "extract(year from A.TAHUN_SPPBJ)" => $getTahun)); 
        $sum2 = $sum2->getSumByParams(array("A.CONTRACTINGPROSESID|| IN " => "(1,2,3,4,5)", "A.CREATED_BY" => $this->USER_LOGIN_ID, "extract(year from A.TAHUN_SPPBJ)" => $getTahun)); 
        $count3 = $count3->getCountByParams(array("A.CONTRACTINGPROSESID|| IN " => "(6)", "A.CREATED_BY" => $this->USER_LOGIN_ID, "extract(year from A.TAHUN_SPPBJ)" => $getTahun)); 
        $sum3 = $sum3->getSumByParams(array("A.CONTRACTINGPROSESID|| IN " => "(6)", "A.CREATED_BY" => $this->USER_LOGIN_ID, "extract(year from A.TAHUN_SPPBJ)" => $getTahun)); 
      }
      break;
    
    default: // SELAIN PENGELOLA KONTRAK
      break;
  } 
} else {
  $tahun = '';

  switch ($this->USER_TYPE_ID) {
    case '12': // Legal 
      if ($this->LEGAL == '1') {
        $count1 = $count1->getCountByParams(array("A.SELESAI" => '1', "A.PAKET_METODE_LELANG_ID|| IN" => "(1,2,5,6,8)","STATUS_KONTRAK" => "Belum dibuat")); 
        $sum1 = $sum1->getSumByParams(array("A.SELESAI" => '1', "A.PAKET_METODE_LELANG_ID|| IN" => "(1,2,5,6,8)","STATUS_KONTRAK" => "Belum dibuat")); 
        $count2 = $count2->getCountByParams(array("A.CONTRACTINGPROSESID|| IN " => "(1,2,3,4,5)")); 
        $sum2 = $sum2->getSumByParams(array("A.CONTRACTINGPROSESID|| IN " => "(1,2,3,4,5)")); 
        $count3 = $count3->getCountByParams(array("A.CONTRACTINGPROSESID|| IN " => "(6)")); 
        $sum3 = $sum3->getSumByParams(array("A.CONTRACTINGPROSESID|| IN " => "(6)")); 
      } else {
        $count1 = $count1->getCountByParams(array("A.SELESAI" => '1', "A.PAKET_METODE_LELANG_ID|| IN" => "(1,2,5,6,8)", "A.PPK" => $this->USER_LOGIN_ID,"STATUS_KONTRAK" => "Belum dibuat")); 
        $sum1 = $sum1->getSumByParams(array("A.SELESAI" => '1', "A.PAKET_METODE_LELANG_ID|| IN" => "(1,2,5,6,8)", "A.PPK" => $this->USER_LOGIN_ID,"STATUS_KONTRAK" => "Belum dibuat")); 
        $count2 = $count2->getCountByParams(array("A.CONTRACTINGPROSESID|| IN " => "(1,2,3,4,5)", "A.CREATED_BY" => $this->USER_LOGIN_ID)); 
        $sum2 = $sum2->getSumByParams(array("A.CONTRACTINGPROSESID|| IN " => "(1,2,3,4,5)", "A.CREATED_BY" => $this->USER_LOGIN_ID)); 
        $count3 = $count3->getCountByParams(array("A.CONTRACTINGPROSESID|| IN " => "(6)", "A.CREATED_BY" => $this->USER_LOGIN_ID)); 
        $sum3 = $sum3->getSumByParams(array("A.CONTRACTINGPROSESID|| IN " => "(6)", "A.CREATED_BY" => $this->USER_LOGIN_ID));       
      }
      break;
    
    default: // SELAIN PENGELOLA KONTRAK 
      break;
  } 

}

 ?>
<div class="row">
  <div class="form-group col-md-12">
    <!-- <label>Pilih Tahun</label> -->
    <select class="form-control" id="setyear" onChange="return window.location = $(this).val()">
      <?php 
      $selected = '';
      $url = base_url('kontrak/index/contracting_dashboard?tahun=');
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

  <div class="col-md-4">
    <div class="card box-shadow-0 animated zoomIn" data-appear="appear" data-animation="zoomIn">
      <div class="card-content">
        <a href="<?= base_url('kontrak/index/contracting_paket?tahun=').$getTahun ?>" style="color: #000">
          <div class="card-body" style="cursor: pointer; padding: 1.7em">
            <div class="media">
              <div class="media-body text-left">
                <h3 class="orange"><?= $count1 ?></h3>
                <span>Paket Pengadaan Selesai Pemilihan </span>
              </div>
              <div class="media-right media-middle">
                <i class="ft-package success font-large-1 float-right"></i>
              </div>
            </div>
            <div class="mb-0">
              <?= '<small style="font-weight:bold">Rp. '.number_format($sum1,'0',',','.').'</small>'; ?>
            </div>
          </div>
        </a>
      </div>
    </div>
  </div>

  <div class="col-md-4">
    <div class="card box-shadow-0 animated zoomIn" data-appear="appear" data-animation="zoomIn">
      <div class="card-content">
        <a href="<?= base_url('kontrak/index/contracting_persiapan?tahun=').$getTahun ?>" style="color: #000">
          <div class="card-body" style="cursor: pointer; padding: 1.7em">
            <div class="media">
              <div class="media-body text-left">
                <h3 class="orange"><?= $count2 ?></h3>
                <span>Proses Kontrak </span>
              </div>
              <div class="media-right media-middle">
                <i class="ft-package success font-large-1 float-right"></i>
              </div>
            </div>
            <div class="mb-0">
              <?= '<small style="font-weight:bold">Rp. '.number_format($sum2,'0',',','.').'</small>'; ?>
            </div>
          </div>
        </a>
      </div>
    </div>
  </div>

  <div class="col-md-4">
    <div class="card box-shadow-0 animated zoomIn" data-appear="appear" data-animation="zoomIn">
      <div class="card-content">
        <a href="<?= base_url('kontrak/index/contracting_selesai') ?>" style="color: #000">
          <div class="card-body" style="cursor: pointer; padding: 1.7em">
            <div class="media">
              <div class="media-body text-left">
                <h3 class="orange"><?= $count3 ?></h3>
                <span>Selesai Kontrak </span>
              </div>
              <div class="media-right media-middle">
                <i class="ft-package success font-large-1 float-right"></i>
              </div>
            </div>
            <div class="mb-0">
              <?= '<small style="font-weight:bold">Rp. '.number_format($sum3,'0',',','.').'</small>'; ?>
            </div>
          </div>
        </a>
      </div>
    </div>
  </div>


</div>