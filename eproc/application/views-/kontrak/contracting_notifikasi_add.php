<?php
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 *
 */
include_once("functions/string.func.php");
include_once("functions/date.func.php");
include_once("functions/default.func.php");

$this->load->model("Contractingnotifikasi");
$contractingnotif = new Contractingnotifikasi();

$reqId  = $this->input->get("reqId") ?: '0';
$reqPaketId	= $this->input->get("reqPaketId");

if($reqId=='0')
	$reqMode= 'insert';
else
	$reqMode ='update';

$contractingnotif->selectByParams(array("CONTRACTING_NOTIFIKASI_ID" => $reqId));
$contractingnotif->firstRow();

$reqJudul = $contractingnotif->getField("JUDUL");
$reqTanggalNotifikasiDari = dateToPageCheck($contractingnotif->getField("TANGGAL_NOTIFIKASI_DARI"));
$reqTanggalNotifikasiSampai = dateToPageCheck($contractingnotif->getField("TANGGAL_NOTIFIKASI_SAMPAI"));

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
        eModal.iframe(pageUrl, '<? SYSTEM_NAME.' '.SYSTEM_NAME_PT ?>')
      }
  	function closePopup() {
  		eModal.close();
  	}
    </script>
    <script type="text/javascript">
    $(document).ready(function() {

    	$(function(){
    		$('#ff').form({
    			url:'contracting_notifikasi_json/add',
    			onSubmit:function(){
    				return $(this).form('validate');
    			},
    			success:function(data){
    				$.messager.alert('Info', data, 'info');
			  	  setTimeout(function () {
             top.reloadMonitoring();
             top.closePopup();
            }, 2000);
    			}
    		});
      }); 

      $('#reqTanggalNotifikasiDari, #reqTanggalNotifikasiSampai').datebox({
        editable: false
      });
    });
    </script>

  </head>

<body class="body-popup" style="background-color:#fff">

    <div class="card mb-1 border-darken-1">
      <div class="card-content">
        <div class="p-1">
          <div class="alert bg-primary alert-icon-left alert-arrow-left alert-dismissible mb-2" role="alert">
            <span class="alert-icon"><i class="fa fa-th"></i></span>  <strong>Notifikasi</strong>
          </div>
          <div class="p-1">
            <form id="ff" class="form-horizontal" role="form" method="post" novalidate style="padding:0 50px">
              <div class="row">
                <div class="form-group col-md-12 mb-2">
                  <label>Judul</label>
                  <input type="text" name="reqJudul" value="<?=$reqJudul?>" title="" class="form-control easyui-validatebox span9" required >
                </div>
              </div>
              <div class="row">
                <div class="form-group col-md-4 mb-2">
                  <label style="width: 100%">Tanggal</label>
                  <input type="text" style="width:160px" name="reqTanggalNotifikasiDari" id="reqTanggalNotifikasiDari" title="Tanggal harus diisi" class="form-control easyui-datebox" value="<?=$reqTanggalNotifikasiDari?>" required />
                </div>
                <!-- <div class="form-group col-md-4 mb-2">
                  <label style="width: 100%">Tanggal Sampai</label>
                  <input type="text" style="width:160px" name="reqTanggalNotifikasiSampai" id="reqTanggalNotifikasiSampai" title="Tanggal harus diisi" class="form-control easyui-datebox" value="<?php // echo $reqTanggalNotifikasiSampai?>" required />
                </div> -->
              </div>
              <div class="form-actions">
            	  <input type="hidden" name="reqId" value="<?=$reqId?>">
                <input type="hidden" name="reqPaketId" value="<?=$reqPaketId?>">
                <input type="hidden" name="reqMode" value="<?=$reqMode?>">
                <a href="#" onClick="top.closePopup()" class="btn round btn-min-width box-shadow-1 btn-danger mr-1 text-white"> <i class="fa fa-close"></i> Tutup</a>
                <button type="submit" class="btn round btn-min-width box-shadow-1 btn-primary"><i class="fa fa-check-square-o"></i> Simpan</button>
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
