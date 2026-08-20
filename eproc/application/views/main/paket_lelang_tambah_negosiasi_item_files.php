<?php
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 *
 */

$this->load->model("Paketnegosiasiitem");
include_once("functions/string.func.php");
include_once("functions/date.func.php");
include_once("functions/default.func.php");

/* create objects */
$negosiasiitem = new Paketnegosiasiitem();

/* VARIABLE */
$reqId  = $this->input->get("reqId") ?: '0'; // Paket ID
 
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
  			url:'paket_lelang_tambah_negosiasi_item_json/addfileImportExcel',
  			onSubmit:function(){
  				return $(this).form('validate');
  			},
  			success:function(data){
			    $.messager.alert('Info', data, 'info');
          setTimeout(function () {
            top.reloadMonitoringReload();
            top.closePopup();
           }, 2000);
  			}
  		});
  	});
    </script>

  </head>

<body class="body-popup" style="background-color: #fff;">

  <div class="card mb-1 border-darken-1">
    <div class="card-content">
      <div class="p-1">
        <div class="alert bg-primary alert-icon-left alert-arrow-left alert-dismissible mb-2" role="alert">
          <span class="alert-icon"><i class="fa fa-th"></i></span>  <strong>Import File Item Negosiasi</strong>
        </div>
        <div class="p-1">
          <form id="ff" class="easyui-form form-horizontal" method="post" novalidate enctype="multipart/form-data" style="padding:0 50px"> 
            <div class="row">
              <div class="form-group col-md-12">
                <label>File Excel</label><br>
                <input type="file" name="reqLinkFile" id="reqLinkFile" size="30" class="easyui-validatebox span9" maxlength="1" validType="fileType['xls','xlsx']" required/>
                 <input type="hidden" name="reqLinkFileTemp" value=""> <br>
                 <span class="badge badge-dark mt-2"><a href="<?= base_url('uploads/dokumen_template/import_nego_item_template.xlsx') ?>">Download Template</a> <i class="fa fa-file-excel-o"></i></span>
              </div> 
            </div> 
            <div class="form-actions">
              <input type="hidden" name="reqId" value="<?=$reqId?>"> 
              <a href="#" onClick="top.closePopup()" class="btn round btn-min-width box-shadow-1 btn-danger mr-1 text-white"> <i class="fa fa-close"></i> Tutup</a>
              <button type="submit" class="btn round btn-min-width box-shadow-1 btn-primary"><i class="fa fa-check-square-o"></i> Simpan</button>
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
