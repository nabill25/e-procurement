<?php
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 *
 */

/* INCLUDE FILE */
$this->load->model(array("Paketundanganklarifikasi","Rekanan"));
include_once("functions/string.func.php");
include_once("functions/date.func.php");
include_once("functions/default.func.php");

$paketundangan = new Paketundanganklarifikasi();
$rekanan_get_nama = new Rekanan();

$reqId  = $this->input->get("reqId") ?: '0'; // Paket_id
$reqRekId  = $this->input->get("rekanan") ?: '0'; // rekanan_id


$paketundangan->selectByParams(array("PAKET_ID" => $reqId, "REKANAN_ID" => $reqRekId));
$paketundangan->firstRow();

$reqTanggalUndangan = $paketundangan->getField("TANGGAL_UNDANGAN");
$reqJam = $paketundangan->getField("JAM");
$reqTempat = $paketundangan->getField("TEMPAT");
$reqPelaksanaan = $paketundangan->getField("PELAKSANAAN");
$reqDokumenDibawa = $paketundangan->getField("DOKUMEN_DIBAWA");
$reqPeserta = $paketundangan->getField("PESERTA");
$reqTempat = $paketundangan->getField("TEMPAT"); 
$reqKeterangan = $paketundangan->getField("KETERANGAN"); 

$rekanan_get_nama->selectByParams(array("A.REKANAN_ID"=>$reqRekId),-1,-1);
$rekanan_get_nama->firstRow();
$tempNama_getNama= $rekanan_get_nama->getField("NAMA");
$reqStatusValidasi = $rekanan_get_nama->getField("STATUS_VALIDASI");
$reqUserStatus = $rekanan_get_nama->getField("USER_STATUS");
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <base href="<?=base_url();?>" />
    <link rel="icon" href="../../favicon.ico">
    <title><?= SYSTEM_NAME.' | '.SYSTEM_NAME_PT ?></title>
    <link rel="stylesheet" type="text/css" href="<?=base_url() ?>assets/new/css/vendors.css">
    <link rel="stylesheet" type="text/css" href="<?=base_url() ?>assets/new/vendors/css/ui/prism.min.css">
    <link rel="stylesheet" type="text/css" href="<?=base_url() ?>assets/new/css/app.css">
    <link rel="stylesheet" type="text/css" href="<?=base_url() ?>assets/new/css/core/menu/menu-types/horizontal-menu.css">
    <link rel="stylesheet" type="text/css" href="<?=base_url() ?>assets/new/css/core/colors/palette-gradient.css">
    <link rel="stylesheet" type="text/css" href="<?=base_url() ?>assets/new/css/core/colors/palette-callout.css">
    <link rel="stylesheet" type="text/css" href="<?=base_url() ?>assets/new/css/style.css">
    <script type="text/javascript" src="js/jquery-1.6.1.min.js"></script>
    <script src="<?=base_url()?>lib/tinyMCE/tinymce.min.js"></script> 
    <link rel="stylesheet" type="text/css" href="lib/drupal/index.css" />
    <script src="lib/emodal/eModal.js"></script> 
  </head>

<body class="body-popup" style="background:#fff">

  <div class="card mb-1 border-darken-1">
    <div class="card-content">
      <div class="p-1"> 
          <div class="row">
            <div class="form-group col-md-12 mb-2">
              <p class="alert alert-primary" style="background-color: transparent !important;"><b><?= $tempNama_getNama ?></b></p>
            </div>
          </div>
          <table class="table table-bordered">
            <tr>
              <td width="30%" style="background: #f6db00; color:#000"><b>Tanggal Undangan</b></td>
              <td><?=getFormattedDate($reqTanggalUndangan) . ' '.$reqJam ?></td>
            </tr>
            <tr>
              <td width="30%" style="background: #f6db00; color:#000"><b>Peserta</b></td>
              <td><?=$reqPeserta?></td>
            </tr>
            <tr>
              <td width="30%" style="background: #f6db00; color:#000"><b>Pelaksanaan</b></td>
              <td><?=$reqPelaksanaan?></td>
            </tr>
            <tr>
              <td width="30%" style="background: #f6db00; color:#000"><b><?php if($reqPelaksanaan == "offline") { echo 'Tempat / Lokasi'; } else { echo 'Link Zoom / GMeet'; } ?></b></td>
              <td><?=$reqTempat?></td>
            </tr>
            <tr>
              <td width="30%" style="background: #f6db00; color:#000"><b>Keterangan</b></td>
              <td><?=$reqKeterangan?></td>
            </tr>
          </table>  
      </div>
    </div>
  </div>


    <link rel="stylesheet" type="text/css" href="<?=base_url()?>lib/eproc/themes/default/easyui.css">
    <script type="text/javascript" src="js/jquery-1.6.1.min.js"></script>
    <script type="text/javascript" src="<?=base_url()?>lib/eproc/jquery.easyui.min.js"></script>
    <script type="text/javascript" src="<?=base_url()?>lib/eproc/kalender-easyui.js"></script>
    <script type="text/javascript" src="<?=base_url()?>lib/eproc/allfunc.js"></script>

  </body>
</html>
