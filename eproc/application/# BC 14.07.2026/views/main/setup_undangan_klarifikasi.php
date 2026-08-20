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

$reqTanggalUndangan = dateToPageCheck($paketundangan->getField("TANGGAL_UNDANGAN"));
$reqJamUndangan = $paketundangan->getField("JAM");
$reqTempat = $paketundangan->getField("TEMPAT");
$reqPelaksanaan = $paketundangan->getField("PELAKSANAAN");
$reqDokumenDibawa = $paketundangan->getField("DOKUMEN_DIBAWA");
$reqPeserta = $paketundangan->getField("PESERTA");
$reqKeterangan = $paketundangan->getField("KETERANGAN");

if($paketundangan->countRow() > 0)
  $reqMode= 'update';
else
  $reqMode ='insert';

$rekanan_get_nama->selectByParams(array("A.REKANAN_ID"=>$reqRekId),-1,-1);
$rekanan_get_nama->firstRow();
$tempNama_getNama= $rekanan_get_nama->getField("NAMA");
$reqStatusValidasi = $rekanan_get_nama->getField("STATUS_VALIDASI");
$reqUserStatus = $rekanan_get_nama->getField("USER_STATUS");
$reqEmail = $rekanan_get_nama->getField("EMAIL");
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
    <script type="text/javascript">
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
    
    $(function(){
      $('#ff').form({
        url:'paket_undangan_klarifikasi_json/add',
        onSubmit:function(){
          return $(this).form('validate');
        },
        success:function(data){
          $.messager.alert('Info', data, 'info');
          setTimeout(function () {
            window.top.location.reload();
          }, 1000);
        }
      });
    });
    $(document).ready(function() {
      $('#reqTanggalUndangan').datebox({
        editable: false
      });
    });
    </script>

  </head>

<body class="body-popup">

  <div class="card mb-1 border-darken-1">
    <div class="card-content">
      <div class="p-1"> 
        <form id="ff" class="easyui-form form-horizontal" method="post" novalidate enctype="multipart/form-data" style="padding:0 50px">
          <div class="row">
            <div class="form-group col-md-12 mb-2">
              <p class="alert alert-primary" style="background-color: transparent !important;"><b><?= $tempNama_getNama ?></b></p>
            </div>
          </div>
          <div class="row">
            <div class="form-group col-md-3 mb-2">
              <label style="width: 100%">Tanggal Undangan</label>
              <input type="text" style="width:160px" name="reqTanggalUndangan" id="reqTanggalUndangan" title="Tanggal Undangan harus diisi" class="form-control easyui-datebox" value="<?=$reqTanggalUndangan?>" required />
            </div>
            <div class="form-group col-md-4 mb-2">
              <label style="width: 100%">Jam</label>
              <input type="text" name="reqJamUndangan" id="reqJamUndangan" title="Jam Undangan harus diisi" class="form-control easyui-validatebox" maxlength="20" value="<?=$reqJamUndangan?>" />
            </div>
          </div> 
          <div class="row">
            <div class="form-group col-md-12 mb-2">
              <label>Peserta</label>
              <input type="text" name="reqPeserta" value="<?=$reqPeserta ?: $tempNama_getNama?>" title="" class="form-control easyui-validatebox span9" required >
            </div>
          </div>
          <div class="row">
            <div class="form-group col-md-12 mb-2">
              <label>Email</label>
              <input type="text" name="reqEmail" value="<?=$reqEmail?>" title="" class="form-control easyui-validatebox span9" required readonly >
            </div>
          </div>
          <div class="row">
            <div class="form-group col-md-12 mb-2">
              <label>Pelaksanaan</label><br>
              <select name="reqPelaksanaan" class="easyui-combobox span2" data-options="
                                        onSelect: function(rec){
                                            if(rec.value == 'online')
                                            {
                                                $('#labelTempat').text('Link Zoom / GMeet'); 
                                            }
                                            else
                                            {
                                                $('#labelTempat').text('Tempat / Lokasi'); 
                                            }

                                        }" style="width: 200%">
                <option value="offline" <?php if($reqPelaksanaan == "offline") { ?> selected <?php } ?>>Offline</option>
                <option value="online" <?php if($reqPelaksanaan == "online") { ?> selected <?php } ?>>Online</option>
                </select>
            </div>
          </div>
          <div class="row">
            <div class="form-group col-md-12 mb-2">
              <label id="labelTempat">Tempat / Lokasi</label>
              <input type="text" name="reqTempat" value="<?=$reqTempat?>" title="" class="form-control easyui-validatebox span9" required >
            </div>
          </div>
          <div class="row">
            <div class="form-group col-md-12 mb-2">
              <label>Keterangan</label>
              <textarea name="reqKeterangan" cols="50" rows="5" title="Alamat harus diisi" class="textarea-tinymce form-control easyui-validatebox span9" required ><?=$reqKeterangan;?></textarea>
            </div>
          </div>
          <div class="form-actions">
            <input type="hidden" name="reqId" value="<?=$reqId?>"> <!-- paket_id -->
            <input type="hidden" name="reqRekId" value="<?=$reqRekId?>"> <!-- rekanan_id -->
            <input type="hidden" name="reqMode" value="<?=$reqMode?>">
            <a href="#" onClick="top.closePopup()" class="btn round btn-min-width box-shadow-1 btn-danger mr-1 text-white"> <i class="fa fa-close"></i> Tutup</a>
            <button type="submit" class="btn round btn-min-width box-shadow-1 btn-primary"><i class="fa fa-send-o"></i> Simpan dan Kirim via email</button>
          </div>
        </form>
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
