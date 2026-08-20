<?php
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 * 
 */

/* INCLUDE FILE */
$this->load->library("kauth");  $userLogin = new kauth();
$this->load->model(array("Blacklistkontrak","Rekanan"));
include_once("functions/string.func.php");
include_once("functions/date.func.php");
include_once("functions/default.func.php");

$reqId  = $this->input->get("reqAidi"); // contractingrekananid
$reqRekananId  = $this->input->get("reqRekananId"); // Yang upload adalah penyedia
/* create objects */
$blacklistkontrak = new Blacklistkontrak();
$rekanan = new Rekanan();

// Get Rekanan
$rekanan->selectByParams(array("A.REKANAN_ID" => $reqRekananId), -1, -1);
$rekanan->firstRow();
$rekanan_nama = $rekanan->getField("NAMA");
$rekanan_npwp = $rekanan->getField("NPWP");
$rekanan_alamat = $rekanan->getField("ALAMAT");
$rekanan_telepon = $rekanan->getField("TELEPON_FULL");
$rekanan_email = $rekanan->getField("EMAIL");
$rekanan_kota = $rekanan->getField("KOTA");
$rekanan_kodepos = $rekanan->getField("KODEPOS");


$blacklistkontrak->selectByParams(array("REKANAN_ID" => $reqRekananId, "CONTRACTING_REKANAN_ID" => $reqId), -1, -1);

if ($blacklistkontrak->countRow() > 0) {
  $reqSubmit = 'update';
} else {
  $reqSubmit = 'insert';
}

$blacklistkontrak->firstRow();
$reqJudul = $blacklistkontrak->getField("JUDUL");
$reqKeterangan = $blacklistkontrak->getField("KETERANGAN");
$reqFile = $blacklistkontrak->getField("FILE");
$reqNoSK = $blacklistkontrak->getField("NO_SK");
$reqTanggalBerlaku = $blacklistkontrak->getField("TANGGAL_BERLAKU");

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
    <link rel="stylesheet" href="lib/font-awesome-4.7.0/css/font-awesome.css" type="text/css">

    <script type="text/javascript" src="js/jquery-1.6.1.min.js"></script>
    <!-- PAGINATION -->
    <link rel="stylesheet" type="text/css" href="lib/drupal/index.css" />
     <script src="lib/emodal/eModal.js"></script>
     <style type="text/css">
       #reqKodeSeachPenyediaautocomplete-list {
          position: relative;
          margin-top: 10px;
          background: #fff;
          width: 100%;
        }
        #reqKodeSeachPenyediaautocomplete-list div {
          margin: 5px;
        }
     </style>
    <script>
    function openAdd(pageUrl) {
        eModal.iframe(pageUrl, 'Eprocurement | <?= SYSTEM_NAME_PT ?>')
    }

    function closePopup() {
      eModal.close();
    }

    function closePopupReload() {
      eModal.close();
      location.reload();
    }
    </script>
    <script type="text/javascript">
    $(function(){
      $('#ffAddFileKontrak').form({
        url:'contracting_json/addfileDaftarHitam',
        onSubmit:function(){ 
          return $(this).form('validate');
        },
        success:function(data){
          $.messager.alert('Info', data, 'info');
          setTimeout(function () {
            window.top.location.reload();
          }, 2000);
        }
      });

    });

    $(document).ready(function() {
      $('#reqTanggalBerlaku, #reqTanggalBerlakuTerima').datebox({
        editable: false
      });

    });

  </script>
  </head>

<body class="body-popup">

     <div class="card mb-1 border-darken-1">
      <div class="card-content">
        <div class="p-1">
          <div class="alert bg-primary alert-icon-left alert-arrow-left alert-dismissible mb-2" role="alert">
            <span class="alert-icon"><i class="fa fa-th"></i></span>  <strong>Daftar Hitam</strong>
          </div>
          <div class="p-1" >
            <table class="table table-bordered table-hover">
              <tbody> 
                <tr>
                  <td colspan="4">
                      <h2><?= $rekanan_nama ?></h2>
                      <i class="fa fa-id-card"></i> <?= $rekanan_npwp ?> <span class="badge badge-info">NPWP</span> </br>
                      <i class="fa fa-phone"></i> Telepon: <?= $rekanan_telepon ?> </br>
                      <i class="fa fa-envelope"></i> Email: <?= $rekanan_email ?> </br>
                      <i class="fa fa-map-marker"></i> <?= $rekanan_alamat.' '.$rekanan_kota.' '.$rekanan_kodepos ?></br>
                  </td>
                </tr>
              </tbody>
            </table>

            <form id="ffAddFileKontrak" class="easyui-form form-horizontal" method="post" novalidate enctype="multipart/form-data" style="padding:0 10px">
              <div class="row">
                <div class="form-group col-md-12 mb-2">
                  <label>No. SK</label>
                  <input type="text" name="reqNoSK" id="reqNoSK" class="form-control easyui-validatebox span9" value="<?= $reqNoSK ?>" required/>
                </div>
              </div> 
              <div class="row">
                <div class="form-group col-md-12 mb-2">
                  <label style="width: 100%">Tanggal Masa Berlaku</label>
                  <input type="text" name="reqTanggalBerlaku" id="reqTanggalBerlaku" title="" class="form-control easyui-datebox span2" value="<?= dateToPageCheck($reqTanggalBerlaku)?>" required style="width: 200% !important" />
                </div>
              </div> 
              <div class="row">
                <div class="form-group col-md-12 mb-2">
                  <label>Judul</label>
                  <input type="text" name="reqJudul" id="reqJudul" class="form-control easyui-validatebox span9" value="<?= $reqJudul ?>" required/>
                </div>
              </div> 
              <div class="row">
                <div class="form-group col-md-12 mb-2">
                  <label style="width: 100%">Keterangan</label>
                  <textarea name="reqKeterangan" id="reqKeterangan" cols="45" rows="5" class="easyui-validatebox span9" value="<?= $reqKeterangan ?>" style="width: 100%"><?= $reqKeterangan ?></textarea>
                </div>
              </div>
              <div class="row">
                <div class="form-group col-md-12 mb-2">
                  <label>File</label>
                  <input type="file" name="reqLinkFile" id="reqLinkFilePDF" class="easyui-validatebox" validType="fileType['docx','zip', 'pdf']" />
                  <input type="hidden" name="reqLinkFileTemp" id="reqLinkFileTemp" value="<?= $reqFile ?>" />
                  <?= UPLOAD_PDF_ZIP_DOC_10MB ?>
                  <?php 
                  if ($reqFile) {
                     echo '<br><a href="uploads/kontrak/'.$reqFile.'" target="_blank" class="badge badge-primary"><span class="fa fa-download"></span> Download</a>';
                   } ?>
                </div>
              </div>

              <div class="form-actions">
                <input type="hidden" name="contractingrekananid" id="contractingrekananid" value="<?=$reqId?>"/>
                <input type="hidden" name="reqRekananId" id="reqRekananId" value="<?=$reqRekananId?>"/>
                <input type="hidden" name="reqSubmit" id="reqSubmit" value="<?= $reqSubmit ?>"/>
                <button type="submit" class="<?= CLASS_BTN_PRIMARY ?>"><?= BTN_SIMPAN ?></button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>

    <script src="lib/bootstrap/libs/jquery/1.12.4/jquery.min.js"></script>
    <script>window.jQuery || document.write('<script src="lib/bootstrap/assets/js/vendor/jquery.min.js"><\/script>')</script>
    <script src="lib/bootstrap/dist/js/bootstrap.min.js"></script>
    <link rel="stylesheet" type="text/css" href="<?=base_url()?>lib/eproc/themes/default/easyui.css">
    <script type="text/javascript" src="<?=base_url()?>lib/eproc/jquery.easyui.min.js"></script>
    <script type="text/javascript" src="<?=base_url()?>lib/eproc/kalender-easyui.js"></script>
    <script type="text/javascript" src="<?=base_url()?>lib/eproc/allfunc.js"></script>

  </body>
</html>
