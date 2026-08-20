<?php
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 * 
 */

/* INCLUDE FILE */
$this->load->library("kauth");  $userLogin = new kauth();
$this->load->model("Contractingjaminan");
include_once("functions/string.func.php");
include_once("functions/date.func.php");
include_once("functions/default.func.php");

$reqId  = $this->input->get("reqId"); // contractingrekananid
$reqPaketId  = $this->input->get("reqPaketId"); // paketid
$reqJaminanId  = $this->input->get("reqJaminanId") ?: 0; // contracting_jaminan_id 

if($reqJaminanId=='0')
  $reqMode= 'simpan';
else
  $reqMode ='update';

$datajaminan = new Contractingjaminan();
$datajaminan->selectByParams(array("CONTRACTING_JAMINAN_ID"=>$reqJaminanId));
$datajaminan->firstRow();

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
        url:'contracting_json/addfilejaminanAll',
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
      $('#reqTanggal, #reqTanggalKonfirmasiKebank, #reqTanggalKonfirmasiOlehBank').datebox({
        editable: false
      }); 
    }); 

  </script>
  </head>

<body class="body-popup" style="background: #fff;">

     <div class="card mb-1 border-darken-1">
      <div class="card-content">
        <div class="p-1">
          <div class="alert bg-primary alert-icon-left alert-arrow-left alert-dismissible mb-2" role="alert">
            <span class="alert-icon"><i class="fa fa-th"></i></span>  <strong>Tambah Jaminan</strong>
          </div>
          <div class="p-1" >
            <form id="ffAddFileKontrak" class="easyui-form form-horizontal" method="post" novalidate enctype="multipart/form-data" style="padding:0 10px">
              <div class="row">
                <div class="form-group col-md-12 mb-2">
                  <label>Nomor Jaminan</label>
                  <input type="text" name="reqNomor" id="reqNomor" class="form-control easyui-validatebox span9" value="<?= $datajaminan->getField("NOMOR") ?>" required/>
                </div>
              </div>  
              <div class="row">
                <div class="form-group col-md-6 mb-2">
                  <label>Tanggal Terbit</label><br>
                  <input type="text" name="reqTanggal" id="reqTanggal" class="form-control easyui-datebox span9" value="<?= dateToPageCheck($datajaminan->getField('TANGGAL_JAMINAN'))?>" required style="width: 210%"/>  
                </div>
                <div class="form-group col-md-6 mb-2">
                  <label>File Jaminan</label><br>
                  <a href="uploads/kontrak/<?= $datajaminan->getField("FILE_JAMINAN") ?>" target="_blank" class="badge badge-primary"><span class="fa fa-download"> Download</span></a>
                </div>
              </div>
              <div class="row">
                <div class="form-group col-md-6 mb-2">
                  <label>Tanggal Konfirmasi ke Bank</label><br>
                  <input type="text" name="reqTanggalKonfirmasiKebank" id="reqTanggalKonfirmasiKebank" class="form-control easyui-datebox span9" value="<?= dateToPageCheck($datajaminan->getField('TANGGAL_KONFIRMASI_KEBANK'))?>" required style="width: 210%"/>  
                </div>
                <div class="form-group col-md-6 mb-2">
                  <label>Tanggal Konfirmasi oleh Bank</label><br>
                  <input type="text" name="reqTanggalKonfirmasiOlehBank" id="reqTanggalKonfirmasiOlehBank" class="form-control easyui-datebox span9" value="<?= dateToPageCheck($datajaminan->getField('TANGGAL_KONFIRMASI_OLEH_BANK'))?>" required style="width: 210%"/>  
                </div>
              </div>  
              <div class="row">
                <div class="form-group col-md-6 mb-2">
                  <label style="width:100%">Bukti Konfirmasi Keabsahan</label>
                  <input type="file" name="reqLinkFile2" id="reqLinkFilePDF" class="easyui-validatebox" <?php if ($datajaminan->getField('FILE_KONFIRMASI')) { } else { echo 'required'; } ?> validType="fileType['docx','zip', 'pdf']" />
                  <?= UPLOAD_PDF_ZIP_DOC_10MB ?>
                  <input type="hidden" name="reqLinkFile2Temp" id="reqLinkFile2Temp" value="<?= $datajaminan->getField('FILE_KONFIRMASI') ?>" />
                </div>
                <div class="form-group col-md-6 mb-2">
                  <label>Status</label><br>
                   <select class="form-control" name="konfirmasi" style="width:60%">
                     <option <?php if ($datajaminan->getField('KONFIRMASI') == '1') { echo "selected"; } ?> value="1">Sesuai</option>
                     <option <?php if ($datajaminan->getField('KONFIRMASI') == '0') { echo "selected"; } ?> value="0">Tidak Sesuai</option>
                    </select>
                </div>
              </div>  

              <div class="form-actions">
                <input type="hidden" name="contractingrekananid" id="contractingrekananid" value="<?=$reqId?>"/>
                <input type="hidden" name="paketid" id="paketid" value="<?=$reqPaketId?>"/>
                <input type="hidden" name="contractingjaminanid" id="contractingjaminanid" value="<?= $datajaminan->getField('CONTRACTING_JAMINAN_ID') ?>"/>
                <input type="hidden" name="reqMode" value="<?=$reqMode?>">
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
