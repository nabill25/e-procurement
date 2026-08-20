<?php
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 * 
 */
$this->load->model("Paket");
include_once("functions/string.func.php");
include_once("functions/date.func.php");
include_once("functions/default.func.php");

$paket = new Paket();
$reqId = $this->input->get("reqId");

$paket->selectByParamsMonitoring(array(), -1, -1, " AND PAKET_ID = '".$reqId."' ");
$paket->firstRow();
$reqPermohonanId = $paket->getField("PERMOHONAN_PAKET_ID");
$reqPermohonanAnalisaId = $paket->getField("PERMOHONAN_PAKET_ANALISA_ID");
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <base href="<?=base_url();?>" />
    <link rel="icon" href="../../favicon.ico">
    <title><?= SYSTEM_NAME ?></title>

    <link href="lib/bootstrap/dist/css/bootstrap.css" rel="stylesheet">
    <link rel="stylesheet" href="css/core.css" type="text/css">
    <link href='http://fonts.googleapis.com/css?family=Roboto:400,300,700' rel='stylesheet' type='text/css'>
    <link rel="stylesheet" type="text/css" href="<?=base_url() ?>assets/new/css/vendors.css">
    <link rel="stylesheet" type="text/css" href="<?=base_url() ?>assets/new/vendors/css/ui/prism.min.css">
    <link rel="stylesheet" type="text/css" href="<?=base_url() ?>assets/new/css/app.css">
    <link rel="stylesheet" type="text/css" href="<?=base_url() ?>assets/new/css/core/menu/menu-types/horizontal-menu.css">
    <link rel="stylesheet" type="text/css" href="<?=base_url() ?>assets/new/css/core/colors/palette-gradient.css">
    <link rel="stylesheet" type="text/css" href="<?=base_url() ?>assets/new/css/core/colors/palette-callout.css">
    <link rel="stylesheet" type="text/css" href="<?=base_url() ?>assets/new/css/style.css">
    <link rel="stylesheet" href="lib/font-awesome-4.7.0/css/font-awesome.css" type="text/css">
    <script type="text/javascript" src="js/jquery-1.6.1.min.js"></script>
    <script type="text/javascript">
  	$(function(){
  		$('#ff').form({
  			url:'paket_json/paket_lelang_ulang',
  			onSubmit:function(){
  				return $(this).form('validate');
  			},
  			success:function(data){
  				//alert(data);return false;
  				$.messager.alert('Info', data, 'info');
  				//top.reloadMonitoring();
          setTimeout(function() {
            top.closePopup(); location.reload(); 
            }, 1800);
  			}
  		});

  	});
  </script>
  </head>

<body style="background: #fff">

 <div class="card mb-1 border-darken-1">
  <div class="card-content">
    <div class="p-1">
      <div class="alert bg-primary alert-icon-left alert-arrow-left alert-dismissible mb-2" role="alert">
        <span class="alert-icon"><i class="fa fa-th"></i></span>  <strong>Gagal paket </strong>  <small><?=$paket->getField("NAMA")?></small>
      </div>
      <div class="p-1">
        <form id="ff" class="form-horizontal" role="form" method="post" novalidate >
          <div class="row">
            <div class="form-group col-md-12 mb-2">
              <label>Alasan <sup>*wajib diisi</sup></label>
              <textarea name="reqAlasan" style="width: 100%" id="reqAlasan" cols="50" rows="5" title="Alasan harus diisi" class="easyui-validatebox required" required=""><?=$reqAlasan?></textarea>
              <p><b>Catatan: <span style="color:red"><i>Refresh halaman setelah melakukan gagal paket</i></span></b></p> 
            </div>
          </div>
          <div class="form-actions">
            <input type="hidden" name="reqId" id="reqId" value="<?=$reqId?>"/>
            <input type="hidden" name="reqPermohonanId" id="reqPermohonanId" value="<?=$reqPermohonanId?>"/>
            <input type="hidden" name="reqPermohonanAnalisaId" id="reqPermohonanAnalisaId" value="<?=$reqPermohonanAnalisaId?>"/>
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
<script src="<?=base_url()?>lib/tinyMCE/tinymce.min.js"></script>
<script type="text/javascript">
tinymce.init({
    selector: "textarea.textarea-tinymce",
    plugins: "image",
    toolbar: "insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image",
    menubar: false,

});
</script>

  </body>
</html>
