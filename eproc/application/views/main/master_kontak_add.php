<?php
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 * 
 */

$this->load->library("kauth");  $userLogin = new kauth(); 
$this->load->model("Kontak");
include_once("functions/string.func.php");
include_once("functions/date.func.php");
include_once("functions/default.func.php");


//$peserta = new PesertaLomba();
$reqId = $this->input->get("reqId");

$kontak = new Kontak();
$kontak->selectByParams(array("KONTAK_ID" => $reqId));
$kontak->firstRow();

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
    <!-- END VENDOR CSS-->
    <!-- BEGIN ROBUST CSS-->
    <link rel="stylesheet" type="text/css" href="<?=base_url() ?>assets/new/css/app.css">
    <!-- <link rel="stylesheet" type="text/css" href="<?=base_url() ?>css/core.css"> -->
    <!-- END ROBUST CSS-->
    <!-- BEGIN Page Level CSS-->
    <link rel="stylesheet" type="text/css" href="<?=base_url() ?>assets/new/css/core/menu/menu-types/horizontal-menu.css">
    <link rel="stylesheet" type="text/css" href="<?=base_url() ?>assets/new/css/core/colors/palette-gradient.css">
    <link rel="stylesheet" type="text/css" href="<?=base_url() ?>assets/new/css/core/colors/palette-callout.css">
    <!-- END Page Level CSS-->
    <!-- BEGIN Custom CSS-->
    <link rel="stylesheet" type="text/css" href="<?=base_url() ?>assets/new/css/style.css">
    
	 <script type="text/javascript" src="js/jquery-1.6.1.min.js"></script>
    
    <!-- PAGINATION -->
    <link rel="stylesheet" type="text/css" href="lib/drupal/index.css" />  
    <script type="text/javascript">	
	$(function(){
		$('#ff').form({
			url:'sk_panitia_json/sk_panitia_add',
			onSubmit:function(){
				return $(this).form('validate');
			},
			success:function(data){
				//alert(data);return false;
				 $.messager.alert('Info', data, 'info');	
			  	top.reloadMonitoring();
			}
		});
		
	});
	
	function createRowDokumenPanitia()
	{
		$(function () {
			$.get("main/loadUrl/main/panitia_add_template", function (data) {
				$("#tbDataDokumenPanitia").append(data);
			});
		});	
	}
    </script>	 
  </head>

<body class="body-popup">

  <div class="card mb-1 border-darken-1">
    <div class="card-content">
      <div class="p-1">
        <div class="alert bg-primary alert-icon-left alert-arrow-left alert-dismissible mb-2" role="alert">
          <span class="alert-icon"><i class="fa fa-th"></i></span>  <strong>Kritik dan Saran</strong>
        </div> 
        <div class="p-1">
          <table class="table table-striped table-hover">
              <tbody>
                  <tr>
                      <td>Nama:</td>
                      <td>
                        <?=$kontak->getField("NAMA")?>
                      </td>
                  </tr>
                  <tr>
                      <td>e-Mail:</td>
                      <td>
                       	<?=$kontak->getField("EMAIL")?>
                      </td>
                  </tr>
                  <tr>
                      <td width="20%">Telpon/HP:</td>
                      <td>
                          <?=$kontak->getField("TELEPON")?>
                      </td>
                  </tr>
                  <tr>
                      <td>Subyek:</td>
                      <td>
                       	<?=$kontak->getField("SUBYEK")?>
                      </td>
                  </tr>
                  <tr>
                      <td>Pesan:</td>
                      <td>
                        <?=$kontak->getField("PESAN")?>
                      </td>
                  </tr> 
                  <tr>
                      <td>Tanggal:</td>
                      <td>
                        <?=getFormattedDate($kontak->getField("TANGGAL"))?>
                      </td>
                  </tr>
              </tbody> 
          </table>  
          <a href="#" onClick="top.closePopup()" class="<?= CLASS_BTN_DANGER ?>"> <i class="fa fa-close"></i> Tutup</a> 
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
