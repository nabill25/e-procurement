<?php
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 *
 */
/* INCLUDE FILE */
$this->load->model("Importsirup");
include_once("functions/string.func.php");
include_once("functions/date.func.php");
include_once("functions/default.func.php");
include_once("functions/blob.func.php");

/* VARIABLE */
$reqId  = $this->input->get("reqId"); // permohonan_paket_analisa_id
$sirupId  = $this->input->get("sirupId"); // kode_sirup

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
    <link rel="stylesheet" type="text/css" href="lib/drupal/index.css" />
    <script src="lib/emodal/eModal.js"></script>
    <script type="text/javascript">
    function openAdd(pageUrl) {
        eModal.iframe(pageUrl, 'Eprocurement')
    }
    function closePopup() {
        eModal.close();
    }
    $(function(){
      $('#ff').form({
        url:'permohonan_paket_usulan_json/permohonan_usulan_add_file_kirim',
        onSubmit:function(){
          $('#btnSubmit').html('<i class="fa fa-send"></i> Proses . . .');
          return $(this).form('validate');
        },
        success:function(data){
          $('#btnSubmit').html('<i class="fa fa-send"></i> Kirim ke eSign');

          $.messager.alert('Info', data, 'info');
          setTimeout(function () {
            top.reloadMonitoringReload();
            top.closePopup();
           }, 3000); 
        }
      });
    });
    </script>
  </head>

<body class="body-popup" style="background: #fff;">

<div class="card mb-1 border-darken-1">
  <div class="card-content">
    <div class="p-1">
      <div class="p-1">
        <form id="ff" class="easyui-form form-horizontal" method="post" novalidate enctype="multipart/form-data" style="width:100%">

          <?php 
          $this->load->library(array("libplanning"));
          $libplanning = new libplanning();
          echo $libplanning->headerPermohonanDokumenTTE($reqId,$totalKirim);
          ?>

          <?php 
          // if ($totalKirim > 0) {
          //    echo '<div class="alert alert-info"><b>Dokumen sudah dikirim ke eSign, periksa untuk TTE</div>';
          // } else 
          // { 
            ?>

          <div class="alert alert-warning"><b>Harap Periksa Kembali...!</b> <br>Dokumen Final ini akan dikirim ke e-Sign untuk di tandatangani oleh PPK</div>

          <div class="form-actions">
            <input type="hidden" name="sirupId" value="<?=$sirupId?>" />
            <input type="hidden" name="reqId" value="<?=$reqId?>" />
            <button id="btnSubmit" type="submit" class="<?= CLASS_BTN_PRIMARY ?>"> <i class="fa fa-send"></i> Kirim ke eSign</button>
          </div>
          <?php 
          // } ?>

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