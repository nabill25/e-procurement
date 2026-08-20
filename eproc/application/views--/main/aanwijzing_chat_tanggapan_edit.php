<?php
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 * 
 */

/* INCLUDE FILE */
$this->load->model("PaketAanwijzing");
include_once("functions/string.func.php");
$this->load->library("FileHandler");
include_once("functions/date.func.php");
include_once("functions/default.func.php");

/* create objects */
$paket_aanwijzing = new PaketAanwijzing();
$file = new FileHandler();

/* VARIABLE */
$reqId  = $this->input->get("reqId");
$paketaanwijzingid  = $this->input->get("paketaanwijzingid");
if($reqId=='')
  $reqMode= 'insert';
else
  $reqMode ='update';

$paket_aanwijzing->selectByParams(array("PAKET_ID" => $reqId, "paket_aanwijzing_id" => $paketaanwijzingid));
$paket_aanwijzing->firstRow();
$reqKeterangan = $paket_aanwijzing->getField("KETERANGAN");
$reqFileOld = $paket_aanwijzing->getField("PATH_FILE");
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

    <!-- PAGINATION -->
    <link rel="stylesheet" type="text/css" href="lib/drupal/index.css" />
    <script src="lib/emodal/eModal.js"></script>
    <script>
    function openAdd(pageUrl) {
        eModal.iframe(pageUrl, '<?= SYSTEM_NAME.' | '.SYSTEM_NAME_PT ?>')
      }
    function closePopup() {
      eModal.close();
    }
      </script>
      <script type="text/javascript">
    $(function(){
      $('#ff').form({
        url:'aanwijzing_chat_json/dokumen_aanwijzing_tanggapan_edit',
        onSubmit:function(){
          return $(this).form('validate');
        },
        success:function(data){
          $.messager.alert('Info', data, 'info');
          top.reloadMonitoringHere();
        }
      });
    });
    </script>

  </head>

<body class="body-popup">

  <div class="card mb-1 border-darken-1">
    <div class="card-content">
      <div class="p-1">
        <div class="alert bg-primary alert-icon-left alert-arrow-left alert-dismissible mb-2" role="alert">
          <span class="alert-icon"><i class="fa fa-th"></i></span>  <strong>Edit Jawaban Aanwijzing</strong>
        </div>
        <div class="p-1">
          <form id="ff" class="easyui-form " method="post" novalidate enctype="multipart/form-data"> 
              <div class="row">
                <div class="form-group col-md-12">
                  <label>Jawab</label>
                  <textarea name="reqKeterangan" id="reqKeterangan" cols="45" rows="5" class="easyui-validatebox form-control" required><?= $reqKeterangan ?></textarea>
                </div> 
              </div>

              <div class="row">
                <div class="form-group col-md-12">
                  <label>Lampiran <?= UPLOAD_PDF_2MB ?></label>
                  <input type="file" class="form-control" name="reqLinkFile" id="reqLinkFilePDF" required validType="fileType['pdf', 'jpg']" />
                  <input type="hidden" name="reqLinkFileOld" value="<?=$reqFileOld?>" />
                </div> 
              </div>

              <div class="form-actions">
                <input type="hidden" name="reqId" value="<?=$reqId?>" />
                <input type="hidden" name="reqPaketAanwijzingId" value="<?=$paketaanwijzingid?>" />
                <a href="main/index/aanwijzing_chat/?reqId=<?=$reqId?>" class="<?= CLASS_BTN_DANGER ?> mr-1"><i class="fa fa-arrow-left"></i> Kembali</a>
                <button type="submit" class="<?= CLASS_BTN_PRIMARY ?>"><i class="fa fa-check-square-o"></i> Kirim Jawaban</button>
              </div> 
          </form>
        </div>
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
