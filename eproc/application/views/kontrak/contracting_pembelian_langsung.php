<?php
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 * 
 */

$this->libsession->cekSession();   

$getTahun = isset($_GET['tahun']) ? $_GET['tahun'] : 'all';


include_once("functions/string.func.php");
include_once("functions/default.func.php");
include_once("functions/date.func.php");
$this->load->model("Paket");
$this->load->model("Katalogrekanan");
$this->load->model("Kataloglogistik");

$paket = new Paket();
$katalogrekanan = new Katalogrekanan();
$kataloglogistik = new Kataloglogistik();
$katalogrekananRow = new Katalogrekanan();
$katalogrekananGroupPenyedia = new Katalogrekanan();

$reqId = $this->input->get("reqId"); 
 

$paket->selectByParamsMonitoring(array("A.PAKET_ID" => coalesce($reqId, 0)));
$paket->firstRow();

$kataloglogistik->selectByParams(array("A.PAKET_ID" => coalesce($reqId, 0)));
$kataloglogistik->firstRow();
$kataloglogistikOngkosKirim = $kataloglogistik->getField('ONGKOS_KIRIM');
$kataloglogistikEstimasiSampai = $kataloglogistik->getField('ESTIMASI_SAMPAI');
$kataloglogistikFileSuratPesanan = $kataloglogistik->getField('FILE_SURAT_PESANAN');
$kataloglogistikPathFileSuratPesanan = $kataloglogistik->getField('PATH_FILE_SURAT_PESANAN');
$kataloglogistikFileBuktiKirim = $kataloglogistik->getField('FILE_BUKTI_KIRIM');
$kataloglogistikPathFileBuktiKirim = $kataloglogistik->getField('PATH_FILE_BUKTI_KIRIM');
$kataloglogistikOngkosKirim = $kataloglogistik->getField('ONGKOS_KIRIM');

  $reqMetodePengadaan = $paket->getField("PAKET_METODE_LELANG_ID");
  $reqNamaPaket = $paket->getField("NAMA");
  $reqLokasiPekerjaan = $paket->getField("LOKASI");
  $reqNilaiPekerjaan = $paket->getField("NILAI");

  if ($reqId == '' || $reqMetodePengadaan != '6')
    redirect(base_url('main'));

  $katalogrekanan->selectByParams(array('A.PAKET_ID' => $reqId));
  $katalogrekananRow->selectByParams(array('A.PAKET_ID' => $reqId));
  $katalogrekananRow->firstRow();
  // echo $katalogrekananRow->getField('STATUS'); die();
  if ($katalogrekananRow->getField('STATUS') == '' || $katalogrekananRow->getField('STATUS') == '0')
    redirect(base_url('app')); 

?>

<script type="text/javascript"> 
 
function statusupdate(c) {
  var katalog = $(c).data("katalog");
  var paket = $(c).data("paket");
  var katalogrekanan = $(c).data("katalogrekanan");
  var status = $(c).data("status");
  // alert(katalog+'-'+paket+'-'+katalogrekanan+'-'+status); return false;
  if (status === 0) {
    var alertMessage = 'Apakah akan melakukan Negosiasi dengan Penyedia?'; 
  } else if (status === 1) {
    var alertMessage = 'Apakah anda setuju dengan hasil Negosiasi?'; 
  } else if (status === 3) {
    var alertMessage = 'Proses Pesanan?'; 
  } else if (status === 4) {
    var alertMessage = 'Kirim Pesanan?'; 
  } else if (status === 5) {
    var alertMessage = 'Terima Pesanan?'; 
  }
  if (confirm(alertMessage)) {
    $.post("katalog_json/statusupdate",
      {
        katalog: katalog,
        paket: paket,
        katalogrekanan: katalogrekanan,
        status: status
      },
      function(data, status){
          var str = data;
          var isNotif = str.split("||"); 
          if (isNotif[0] === 'Gagal') {
            alertError2(isNotif[1]);
          } else {
            alertSuccess2(isNotif[1]);
          }
          setTimeout(function() {
            location.reload(); }, 1800);
      });
  } else {
    return false;
  }
} 

$(function(){
  $('#ff').form({
    url:'<?= base_url('katalog_json/prosespesanan') ?>',
    onSubmit:function(){
      var v=$(this).form('validate');
      if(v) { 
        showLoad();
        return v;
      } else {
        hideLoad();
        return false;
      }
    },
    success:function(data){
      // $.messager.alert('Info', data, 'info'); 
      location.reload();
    }
  });
  
});
 
</script> 

<style type="text/css">
  .card-text a {
    font-size: 11px;
  }
</style>

<div class="row"> 
  <div class="col-md-12 col-sm-12">
    <div class="card">
      <div class="card-header card-head-inverse bg-primary">
        <h4 class="card-title text-white">Katalog Tracking Pesanan</h4>
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

          <div class="col-md-12"> 
            <ul class="nav nav-tabs process-model more-icon-preocess" role="tablist">
              <li role="presentation" class="<?php if($katalogrekananRow->getField('STATUS') == '4') { echo 'active'; } ?>" style="width: 33% !important">
                <i class="fa fa-cogs" aria-hidden="true"></i> <p>Diproses</p>
              </li>
              <li role="presentation" class="<?php if($katalogrekananRow->getField('STATUS') == '5') { echo 'active'; } ?>" style="width: 33% !important">
                <i class="fa fa-truck" aria-hidden="true"></i> <p>Dikirim</p>
              </li> 
              <li role="presentation" class="<?php if($katalogrekananRow->getField('STATUS') == '6') { echo 'active'; } ?>" style="width: 33% !important">
                <i class="fa fa-check-square-o" aria-hidden="true"></i><p>Diterima</p>
              </li> 
            </ul>
          </div>
          <hr>
          <?php 
          if($katalogrekananRow->getField('STATUS') == '5') 
          { ?>
          <div class="col-md-12"> 
            <table class="table table-bordered table-hover mt-1 mb-1">
              <tr>
                <td width="20%">Estimasi Sampai</td>
                <td>
                  <?= getFormattedDate($kataloglogistikEstimasiSampai) ?> 
                  <?php 
                  $tgl1 = new DateTime($kataloglogistikEstimasiSampai);
                  $tgl2 = new DateTime(date("Y-m-d"));
                  $d = $tgl1->diff($tgl2)->days;
                  if ($kataloglogistikEstimasiSampai >= date('Y-m-d')) { 
                    if ($d >=1) {
                      echo '<span class="badge badge-primary">pesanan sampai '.$d.' hari lagi</span>';
                    } else if ($d == 0) {
                      echo '<span class="badge badge-warning"> pesanan sampai hari ini </span>';
                    }
                  } else {
                    echo '<span class="badge badge-danger"> pesanan melebihi '.$d.' hari ini </span>';
                  }
                  ?>
                </td>
              </tr>
              <tr>
                <td>Bukti Kirim</td>
                <td>
                  <a href="<?= base_url('images/katalog').'/'.$kataloglogistikPathFileBuktiKirim ?>" target="_blank" class="btn btn-primary btn-sm"> 
                    <span class="fa fa-download"></span> Download
                  </a>
                </td>
              </tr>
            </table>
          </div>
          <?php 
          } ?> 

          <?php 
            if($katalogrekananRow->getField('STATUS') == '6') 
            { ?>
            <div class="col-md-12"> 
              <table class="table table-bordered table-hover mt-1 mb-1"> 
                <tr>
                  <td width="20%">Bukti Kirim</td>
                  <td>
                    <a href="<?= base_url('images/katalog').'/'.$kataloglogistikPathFileBuktiKirim ?>" target="_blank" class="btn btn-primary btn-sm"> 
                      <span class="fa fa-download"></span> Download
                    </a>
                  </td>
                </tr>
                <tr>
                  <td width="20%">Cetak Chat Negosiasi</td>
                  <td>
                    <a href="<?= base_url('main/loadUrl/report/katalog_cetak_chat_pdf/?reqId='.$reqId) ?>" target="_blank" class="btn btn-primary mr-1 btn-sm"><i class="fa fa-print"></i> </a>
                  </td>
                </tr>
                <tr>
                  <td width="20%">Surat Pesanan</td>
                  <td>
                    <a style="text-align: left" href="images/katalog/<?= $kataloglogistikPathFileSuratPesanan ?>" class="btn btn-primary btn-sm" target="_blank" /><span class="fa fa-download"></span> Download Surat Pesanan </a>
                  </td>
                </tr>
              </table>
            </div>
            <?php 
            } ?> 

          <div class="form-actions mt-2">
            <a href="main/index/contracting_paket?tahun=<?= $getTahun ?>" class="btn btn-danger mr-1 text-white"> <i class="fa fa-arrow-left"></i> Kembali </a> 
            <?php 
            if($katalogrekananRow->getField('STATUS') == '5') 
            {
              echo '<a style="color:#fff" onclick="statusupdate(this)" class="btn btn-warning" 
                            data-katalogrekanan="'.$katalogrekananRow->getField('KATALOGREKANANID').'"
                            data-status="5"
                            data-katalog="'.$katalogrekananRow->getField('KATALOGID').'"
                            data-paket="'.$katalogrekananRow->getField('PAKET_ID').'"> <i class="fa fa-check-square-o"></i>
                            Terima Pesanan
                          </a>'; 
            }?>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
