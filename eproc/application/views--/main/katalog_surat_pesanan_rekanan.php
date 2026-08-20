<?php
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 * 
 */

$this->libsession->cekSession();   

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

  $reqMetodePengadaan = $paket->getField("PAKET_METODE_LELANG_ID");
  $reqNamaPaket = $paket->getField("NAMA");
  $reqLokasiPekerjaan = $paket->getField("LOKASI");
  $reqNilaiPekerjaan = $paket->getField("NILAI");

  if ($reqId == '' || $reqMetodePengadaan != '6')
    redirect(base_url('app'));

  $katalogrekanan->selectByParams(array('A.PAKET_ID' => $reqId));
  $katalogrekananRow->selectByParams(array('A.PAKET_ID' => $reqId));
  $katalogrekananRow->firstRow();
  // echo $katalogrekananRow->getField('STATUS'); die();
  if ($katalogrekananRow->getField('STATUS') == '' || $katalogrekananRow->getField('STATUS') == '0')
    redirect(base_url('app'));

  if ($katalogrekananRow->getField('REKANAN_ID') != $this->ID)
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
            setTimeout(function() {
              location.reload(); }, 1800);
          } else {
            alertSuccess2(isNotif[1]);
            document.location.href = 'main/index/katalog_tracking_pesanan_rekanan/?reqId=<?= $reqId ?>';   
          }
      });
  } else {
    return false;
  }
} 
 
</script> 

<style type="text/css">
  .card-text a {
    font-size: 11px;
  }
</style>

<section id="backColor">
  <div class="row"> 

    <div class="col-md-2 col-sm-12">
      <div class="card border-bottom-primary" style="zoom: 1;">  
        <div class="card-body"> 
          <div class="card-text">
           <?php 
            if($this->USER_TYPE_ID == "6") {  
              // get Notification Penawaran
              $this->load->model("Katalog");
              $katalog = new Katalog();
              $statement = ' AND A.REKANAN_ID = '.$this->ID.' AND A.STATUS=\'1\' OR A.STATUS=\'3\' OR A.STATUS=\'4\' OR A.STATUS=\'5\' ';
              $totalPenawaran = $katalog->getCountByParamsPenawaran(array(), $statement);?>
              <a href="<?= base_url() ?>main/index/katalog_rekanan" class="btn btn-success btn-lg mb-1 text-left" style="width: 100%;"><i class="fa fa-cogs fa-lg pull-right"></i> Katalog</a>
              <a href="<?= base_url() ?>main/index/katalog_rekanan_add" class="btn btn-success btn-lg mb-1 text-left" style="width: 100%;"><i class="fa fa-plus fa-lg pull-right"></i> Tambah Katalog</a>
              <a href="<?= base_url() ?>main/index/katalog_penawaran" class="btn btn-success btn-lg mb-1 text-left" style="width: 100%; opacity: .3"><i class="fa fa-shopping-basket fa-lg pull-right"></i> Penawaran <?= '<span class="badge badge-danger" style="opacity: 1">'.$totalPenawaran.'</span>'; ?></a>
              <a href="<?= base_url() ?>main/index/katalog_pernyataan" class="btn btn-success btn-lg mb-1 text-left" style="width: 100%"><i class="fa fa-upload fa-lg pull-right"></i> Upload <br> Kontrak Katalog</a>
            <?php 
            } ?> 

            <?php 
            if($this->USER_TYPE_ID == "1" || $this->USER_TYPE_ID == "2"){  // 1:admin, 2:validator?>
              <a href="<?= base_url() ?>main/index/katalog_rekanan" class="btn btn-success btn-lg mb-1 text-left" style="width: 100%; opacity: .3"><i class="fa fa-cogs fa-lg pull-right"></i> Katalog</a>
              <a href="<?= base_url() ?>main/index/katalog_validasi" class="btn btn-success btn-lg mb-1 text-left" style="width: 100%;"><i class="fa fa-edit fa-lg pull-right"></i> Validasi</a>
              <a href="<?= base_url() ?>main/index/katalog_laporan" class="btn btn-success btn-lg mb-1 text-left" style="width: 100%;"><i class="fa fa-flag fa-lg pull-right"></i> Laporan</a>
            <?php 
            } ?> 
          </div>
        </div>
      </div>
    </div> 

    <div class="col-md-10 col-sm-12">
      <div class="card border-bottom-primary" style="zoom: 1;"> 
        <div class="card-header">
          <h4 class="card-title">Katalog <small> Surat Pesanan</small></h4>
          <div class="heading-elements" id="tombol"> 
            <span></span>
          </div>
        </div>
        <div class="card-body area-datatable"> 
          <div class="form-body"> 

            <?php
            $this->load->model("Kataloglogistik"); 
            $Kataloglogistik = new Kataloglogistik();
            $Kataloglogistik->selectByParams(array('A.PAKET_ID' => $reqId ));
            $btnDownload = '';
            while($Kataloglogistik->nextRow())
            { 
              if (file_exists('images/katalog/'.$Kataloglogistik->getField("path_file_surat_pesanan"))) {
                $filenya = $Kataloglogistik->getField("path_file_surat_pesanan");
              } else {
                $filenya = '';
              }
              // echo $filenya;
              $btnDownload .= '<a style="text-align: left" href="images/katalog/'.$filenya.'" class="'.CLASS_BTN_PRIMARY.' mr-1" target="_blank" /><span class="fa fa-download"></span> Download Surat Pesanan </a>';
              ?>
                <object data="images/katalog/<?= $filenya ?>" type="application/pdf" style="width: 100%; height: 500px">
                  <embed src="images/katalog/<?= $filenya ?>" type="application/pdf" />
                </object> 
            <?php 
            } ?>
            <hr>

            <div class="form-actions mt-2">
              <a href="main/index/katalog_penawaran" class="<?= CLASS_BTN_DANGER ?> mr-1"> <i class="fa fa-arrow-left"></i> Kembali </a> 
              <!-- <button type="submit" class="btn btn-primary mr-1"><i class="fa fa-check-square-o"></i> Simpan</button> -->
              <?php 
                switch ($katalogrekananRow->getField('STATUS')) {
                  case '3':
                    echo $btnDownload;
                    echo '<a style="color:#fff" onclick="statusupdate(this)" class="'.CLASS_BTN_WARNING.'" 
                            data-katalogrekanan="'.$katalogrekananRow->getField('KATALOGREKANANID').'"
                            data-status="3"
                            data-katalog="'.$katalogrekananRow->getField('KATALOGID').'"
                            data-paket="'.$katalogrekananRow->getField('PAKET_ID').'"> <i class="fa fa-cogs"></i>
                            Proses Pesanan
                          </a>';
                    break; 
                  
                  default:
                    break;
                }
              ?>

            </div> 

          </div>
        </div>
      </div>
    </div>
  </div> 
</section>  