<?php
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 *
 */

/* INCLUDE FILE */
$this->load->model("Vendorretail");
include_once("functions/string.func.php");
include_once("functions/date.func.php");
include_once("functions/default.func.php");

$vendor_retail = new Vendorretail();

$reqId	= $this->input->get("reqId") ?: '0';

if($reqId=='0')
	$reqMode= 'insert';
else
	$reqMode ='update';

$vendor_retail->selectByParams(array("REKANAN_RETAIL_ID" => $reqId));
$vendor_retail->firstRow();
$reqRekananTipe = $vendor_retail->getField("REKANAN_TIPE_ID");
$reqNama = $vendor_retail->getField("NAMA");
$reqNPWP = $vendor_retail->getField("NPWP");
$reqTeleponKode = $vendor_retail->getField("TELEPON_KODE");
$reqTelepon = $vendor_retail->getField("TELEPON");
$reqWhatsapp = $vendor_retail->getField("WHATSAPP");
$reqTanggalDaftar = $vendor_retail->getField("TANGGAL_DAFTAR");
$reqRegionId = $vendor_retail->getField("REGION_ID");
$reqKota = $vendor_retail->getField("KOTA");
$reqKontakPerson = $vendor_retail->getField("KONTAK_PERSON");
$reqKontakPersonHP = $vendor_retail->getField("KONTAK_PERSON_HP");
$reqAlamat = $vendor_retail->getField("ALAMAT");
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <base href="<?=base_url();?>" />
    <link rel="icon" href="../../favicon.ico">
    <title><?= SYSTEM_NAME_PT ?></title>
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
  	$(function(){
  		$('#ff').form({
  			url:'vendor_retail_json/add',
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

    $(function() {
        $('#npwpToggle').on('change', function() {
          $('#reqNPWP').val('');
            if ($(this).is(':checked')) {
                // Hapus onkeydown
                $('#reqNPWP').removeAttr('onkeydown');
            } else {
                // Kembalikan fungsi onkeydown
                $('#reqNPWP').attr('onkeydown', "return format_npwp(event, 'reqNPWP');");
            }
        });
    });
    $(document).ready(function() {
      $('#reqTanggalDaftar').datebox({
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
          <span class="alert-icon"><i class="fa fa-th"></i></span>  <strong>Vendor Retail</strong>
        </div>
        <div class="p-1">
          <form id="ff" class="easyui-form form-horizontal" role="form" method="post" novalidate style="padding:0 50px">
            <div class="row">
              <div class="form-group col-md-3 mb-2">
                <label style="width: 100%">Bentuk Usaha</label>
                <input type="text" name="reqRekananTipe" id="reqRekananTipe" class="form-control easyui-combobox span1" value="<?= $reqRekananTipe ?>"
                  data-options="valueField:'id',textField:'text',url:'rekanan_tipe_json/combo',
                  onSelect: function(rec){
                  if(rec.id === '7') {
                    $('#labelNamaPerusahaan').text('Nama Perorangan');
    					    $('#labelNPWP').text('Perorangan');
    					    $('#labelEmail').text('Perorangan');
                                                $('#fstatus').hide();
                                                $('#fkualifikasi').hide();
                                                $('#kualifikasiKecil').attr('checked', 'checked');
                                              } else {
                                                $('#labelNamaPerusahaan').text('Nama Perusahaan');
    					    $('#labelNPWP').text('Perusahaan');
    					    $('#labelEmail').text('Official Perusahaan');
                                                $('#fstatus').show();
                                                $('#fkualifikasi').show();
                                              }
                                              }"
                style="width: 275% !important" required />
              </div>
              <div class="form-group col-md-9 mb-2">
                <label>Nama Perusahaan</label>
                <input type="text" name="reqNama" value="<?=$reqNama?>" title="" class="form-control easyui-validatebox span9" required >
              </div>
            </div>
            <div class="row">
              <div class="form-group col-md-3 mb-2">
                <label>NPWP <sup id="labelNPWP">Perusahaan</sup></label>
                  <?php
                  if ($reqMode == 'insert' ) { ?>
                    <input type="text" id="reqNPWP" name="reqNPWP"  class="form-control easyui-validatebox span3" accesskey="n" value="<?=isset($reqNPWP)?$reqNPWP:''?>" onkeydown="return format_npwp(event, 'reqNPWP');" maxlength="20" validType="remote['fungsi_json/check_npwp_double','reqNPWP', $('input[name=\'reqStatus\']:checked').val()]" invalidMessage="NPWP sudah digunakan." required />
                  <label for="npwpToggle" style="cursor: pointer; user-select: none;">
                    <input type="checkbox" id="npwpToggle" style="margin-right: 6px;">
                    16 Digit ?
                  </label>
                  <?php
                } else { ?>
                  <input type="text" id="reqNPWP" name="reqNPWP"  class="form-control easyui-validatebox span3" accesskey="n" value="<?=isset($reqNPWP)?$reqNPWP:''?>" readonly />
                  <?php
                } ?>
              </div>
              <div class="form-group col-md-1 mb-2">
                <label>Telepon</label>
                <input type="text" name="reqTeleponKode" value="<?=$reqTeleponKode?>" title="" class="form-control easyui-validatebox span9" onkeypress="return isNumberKey(event)" >
              </div>
              <div class="form-group col-md-4 mb-2">
                <label>&nbsp;</label>
                <input type="text" name="reqTelepon" value="<?=$reqTelepon?>" title="" class="form-control easyui-validatebox span9" onkeypress="return isNumberKey(event)" >
              </div>
              <div class="form-group col-md-4 mb-2">
                <label>Whatsapp</label>
                <input type="text" name="reqWhatsapp" value="<?=$reqWhatsapp?>" title="" class="form-control easyui-validatebox span9" required >
              </div>
            </div>
            <div class="row">
              <div class="form-group col-md-2 mb-2">
                <label style="width:100%">Tanggal Daftar</label>
                <input type="text" title="Tanggal harus diisi" class="form-control easyui-datebox span2" name="reqTanggalDaftar" id="reqTanggalDaftar" value="<?=dateToPageCheck($reqTanggalDaftar)?>" required style="width: 180%"/>
              </div>
              <div class="form-group col-md-3 mb-2">
                <label style="width:100%">Provinsi</label>
                <input type="text" name="reqRegionId" class="easyui-combobox span4" data-options="valueField:'id',textField:'text',url:'region_json/combo'"  value="<?=$reqRegionId?>" style="width: 280% !important" />
              </div>
              <div class="form-group col-md-7 mb-2">
                <label>Kota</label>
                <input type="text" name="reqKota" value="<?=$reqKota?>" title="" class="form-control easyui-validatebox span9" required >
              </div>
            </div>
            <div class="row">
              <div class="form-group col-md-6 mb-2">
                <label>Penanggung Jawab</label>
                <input type="text" name="reqKontakPerson" value="<?=$reqKontakPerson?>" title="" class="form-control easyui-validatebox span9" required >
              </div>
              <div class="form-group col-md-6 mb-2">
                <label>Nomor Kontak Penanggung Jawab</label>
                <input type="text" name="reqKontakPersonHP" value="<?=$reqKontakPersonHP?>" title="" class="form-control easyui-validatebox span9" required >
              </div>
            </div>
            <div class="row">
              <div class="form-group col-md-12 mb-2">
                <label>Alamat</label>
              	<textarea name="reqAlamat" cols="50" class="form-control easyui-validatebox span9" rows="5" required ><?=$reqAlamat?></textarea>
              </div>
            </div>
            <div class="form-actions">
            	<input type="hidden" name="reqId" value="<?=$reqId?>">
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
