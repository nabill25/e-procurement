<?php

/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 * 
 */
$this->libsession->cekSession();

$this->load->library("kauth");  $userLogin = new kauth();
$this->load->library("paketinfo"); $paketInfo = new paketinfo();
$this->load->model(array("Paket","Paketpemenangpurchasing"));

include_once("functions/string.func.php");
include_once("functions/date.func.php");
include_once("functions/default.func.php");

$reqId = httpFilterRequest("reqId"); // paket_id
$reqProses = httpFilterRequest("reqProses");
 
$pemenang = new Paketpemenangpurchasing();
$pemenang->selectByParams(array("PAKET_ID" => $reqId));
$pemenang->firstRow();
$id = $pemenang->getField("PAKET_PEMENANG_PURCHASING_ID");
$reqRekananId = $pemenang->getField("REKANAN_ID");
$reqPaketId = $pemenang->getField("PAKET_ID");
$reqNamaPenyedia = $pemenang->getField("NAMA");
$reqNPWP = $pemenang->getField("NPWP");
$reqTelepon = $pemenang->getField("TELEPON");
$reqAlamat = $pemenang->getField("ALAMAT");
$reqEmail = $pemenang->getField("EMAIL");
$reqJenis = $pemenang->getField("JENIS");
$reqNilaiPembelian = $pemenang->getField("NILAI_PEMBELIAN");

$paketInfo->getPaket($reqId);
$reqNama = $paketInfo->nama;
$reqMetodeLelang = $paketInfo->metode_lelang_nama;
$reqPermohonanId = $paketInfo->permohonan_paket_id;

$wajib = '<span class="badge badge-primary"><small style="font-size: 10px">wajib dilengkapi</small>';
$optional = '<span class="badge badge-success"><small style="font-size: 10px">Optional</small>';
$sudahwajib = '<span class="badge badge-success"><small style="font-size: 10px"><i class="fa fa-check"></i> lengkap</small>';

$paket = new Paket();
$paket->selectByParamsMonitoring2(array(), -1, -1, " AND PAKET_ID = '".$reqId."' ");
$paket->firstRow();
$alasan = $paket->getField("ALASAN");
$alasan_ulang = $paket->getField("ALASAN_ULANG");
$multi_pemenang = $paket->getField("MULTI_PEMENANG");
$paket_metode_lelang_id = $paket->getField("PAKET_METODE_LELANG_ID");
$ppk = $paket->getField("PPK");

?>
<link rel="stylesheet" type="text/css" href="lib/DataTables-1.10.7/media/css/jquery.dataTables.css">
<link rel="stylesheet" type="text/css" href="lib/DataTables-1.10.7/extensions/Responsive/css/dataTables.responsive.css">
<link rel="stylesheet" type="text/css" href="<?=base_url() ?>assets/new/vendors/css/ui/jquery-ui.min.css">
<link rel="stylesheet" type="text/css" href="<?=base_url() ?>assets/new/css/plugins/ui/jqueryui.css">
<script type="text/javascript" language="javascript" src="lib/DataTables-1.10.7/media/js/jquery.dataTables.js"></script>
<script type="text/javascript" language="javascript" src="lib/DataTables-1.10.7/extensions/Responsive/js/dataTables.responsive.js"></script>

<script type="text/javascript">
  $(document).ready(function() {
    $('#dokumenFileIdTable').DataTable({
      // "aaSorting": [[1, 'desc']],
      "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
    });
  });
</script>

<style type="text/css">
  small { font-weight: bold; font-size: 11.5px }
  .dataTables_length { display: none; }
  a.list-group-item { color: #000 !important; }
  .list-group-item { padding: 0.5rem 1.25rem !important; border: transparent !important; }
</style>

<div class="row">
  <div class="col-md-3 col-sm-3">
    <div class="jqueryui-ele-container">
      <div class="list-group">
        <a class="btn-primary text-white list-group-item disabled" style="color:#fff !important;"> Info Detail <?= $reqMetodeLelang ?></a>
        <?php 
        if ($ppk != $this->USER_LOGIN_ID) 
        {  
          $paket_ppk = new Paket();
          $paket_ppk->selectByParams(array("A.PAKET_ID" => coalesce($reqId, 0)));
          $paket_ppk->firstRow();
          $ppknya = $paket_ppk->getField("PPK");
        ?>
        <a class="list-group-item" href="main/index/paket_lelang_tambah/?reqId=<?=$reqId?>" style="cursor: pointer;">
          <span class="fa fa-angle-double-right"></span> Edit Paket Pengadaan
        </a>
        <a onclick="openAddFrame('main/loadUrl/main/tunjuk_penyedia?paketid=<?= $reqId ?>')" class="list-group-item"> <span class="fa fa-angle-double-right"></span> Tunjuk Penyedia
        </a>
        <a onclick="openAddFrame('main/loadUrl/main/tunjuk_pengelola_kontrak_pembelian?paketid=<?= $reqId ?>')" class="list-group-item"> <span class="fa fa-angle-double-right"></span> Pilih PJK 
         <?php if ($ppknya == '') { echo $wajib; } else { echo $sudahwajib; }  ?>
        </a>
        <?php 
        } ?> 
        <a class="list-group-item" href="main/index/purchasing_file/?reqId=<?=$reqId?>" style="cursor: pointer;background-color: #fff !important; opacity: 0.5;">
          <span class="fa fa-angle-double-right"></span> Upload Dokumen
        </a>
	     <?php 
        if(trim($alasan_ulang) == "" && trim($alasan) == "")
        { 
          if ($ppk != $this->USER_LOGIN_ID) { 
        ?>
        <a class="list-group-item" onClick="openAdd('main/loadUrl/main/paket_lelang_batal/?reqId=<?=$reqId?>');">
          <span class="fa fa-angle-double-right"></span>  Batalkan Paket
        </a>
       <?php 
          }
        } ?>
        <a onclick="openAddLg('main/loadUrl/main/rekam_jejak_view?id=<?= $reqPermohonanId ?>&paketid=<?= $reqId ?>')" class="list-group-item">
          <span class="fa fa-angle-double-right"></span>  Rekam Jejak
        </a>
      </div>
    </div>
  </div>
  <div class="col-md-9 col-sm-9">
    <div class="card">
      <div class="card-content collapse show border-info border-darken-2">
        <div class="card-body">
          <div class="form-group col-md-12 mb-1 border-blue border-darken-1 mb-1" style="margin-bottom: 1px solid #b7b7b7; padding:10px; border-radius:10px">
            <div class="row">
              <div class="col-md-8"><h3><b>Informasi Penyedia</b></h3></div>
              <div class="col-md-4 text-right"><small class="badge badge-info">Nilai </small><h3><b><?= numberToIna($reqNilaiPembelian) ?></b></h3></div>
            </div>
            <h2><?= $reqNamaPenyedia ?></h2>
            <table style="width: 100%">
              <tr> <td><i class="fa fa-id-card"></i> <?= $reqNPWP ?> <span class="badge badge-info">NPWP</span></td> </tr>
              <tr> <td><i class="fa fa-phone"></i> Telepon: <?= $reqTelepon ?></td> </tr>
              <tr> <td><i class="fa fa-envelope"></i> Email: <?= $reqEmail ?></td> </tr>
              <tr> <td><i class="fa fa-map-marker"></i> <?= $reqAlamat; ?></td> </tr>
            </table>
          </div>
          <a onclick="openAdd('main/loadUrlKontrak/main/purchasing_file_add?reqAidi=<?= $reqId ?>&reqJenis=all')" class="<?= CLASS_BTN_PRIMARY ?>"> <i class="fa fa-plus-circle"></i> Tambah Dokumen 
          </a>
          <table id="dokumenFileIdTable" class="border-double table mb-0 table-bordered mb-2" style="width: 100%">
            <?= $this->libpurchasing->getTableFile($reqId) ?>
          </table>
          <!-- <div class="form-actions">
            <a href="main/index/paket_detil?reqId=<?php // $reqId; ?>" class="<?php // CLASS_BTN_DANGER ?>"> <i class="fa fa-arrow-left"></i> Kembali </a>
          </div> -->

        </div>
      </div>
    </div>
  </div>
</div>
