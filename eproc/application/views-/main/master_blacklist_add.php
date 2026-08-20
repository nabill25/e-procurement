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

$reqStatus = $this->input->get("reqStatus");
$reqId = $this->input->get("reqId");
$reqKeterangan = $this->input->get("reqKeterangan");
$reqTanggalMulai = date('d-m-Y');
$reqTanggalSelesai = manipulasiTanggal($reqTanggalMulai,'24','months');

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
    <script type="text/javascript" src="js/jquery-1.9.1.js"></script>
    <script src="lib/bootstrap/dist/js/bootstrap.min.js"></script>
    <link rel="stylesheet" type="text/css" href="<?=base_url() ?>assets/new/css/vendors.css">
    <link rel="stylesheet" type="text/css" href="<?=base_url() ?>assets/new/vendors/css/ui/prism.min.css">
    <link rel="stylesheet" type="text/css" href="<?=base_url() ?>assets/new/css/app.css">
    <link rel="stylesheet" type="text/css" href="<?=base_url() ?>assets/new/css/core/menu/menu-types/horizontal-menu.css">
    <link rel="stylesheet" type="text/css" href="<?=base_url() ?>assets/new/css/core/colors/palette-gradient.css">
    <link rel="stylesheet" type="text/css" href="<?=base_url() ?>assets/new/css/core/colors/palette-callout.css">
    <link href="lib/bootstrap/dist/css/bootstrap.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="<?=base_url() ?>assets/new/css/style.css">

    <link rel="stylesheet" type="text/css" href="lib/drupal/index.css" />
    <script src="lib/emodal/eModal.js"></script>
    <script>

    function openAdd(pageUrl) {
        eModal.iframe(pageUrl, '<?php SYSTEM_NAME.' '.SYSTEM_NAME_PT ?>')
      }
    function closePopup() {
      eModal.close();
    }
    </script>

    <script type="text/javascript">
      $(function(){
        $('#ff').form({
          url:'blacklist_json/blacklist_add_coba',
          onSubmit:function(){
            return $(this).form('validate');
          },
          success:function(data){
            //alert(data);
             $.messager.alert('Info', data, 'info');
             setTimeout(function () {
               top.reloadMonitoring();
               top.closePopup();
              }, 2000);
             // top.frames['mainFrame'].location.reload();
          }
        });
      });

      function tambahRekananBlacklist(rekanan_id, nama, alamat, kota, npwp)
      {
        $("#reqRekananId").val(rekanan_id);
        $("#reqNama").val(nama);
        $("#reqAlamat").val(alamat);
        $("#reqKota").val(kota);
        $("#reqNPWP").val(npwp);
      }
      $(document).ready(function() {
        $("#reqTanggung").click(countChecked);
        $('#reqTanggalMulai, #reqTanggalSelesai').datebox({
          editable: false
        });
      });

      function countChecked() {
          var n = $("#reqTanggung:checked").length;
          if(n){
            $("#btnSubmit").show(0);
          }else{
            $("#btnSubmit").hide(0);
          }
      }

    </script>
  </head>

<body class="body-popup">

<div class="card mb-1 border-darken-1">
    <div class="card-content">
      <div class="p-1">
        <div class="alert bg-primary alert-icon-left alert-arrow-left alert-dismissible mb-2" role="alert">
          <span class="alert-icon"><i class="fa fa-th"></i></span>  <strong>Blacklist </strong>
        </div>
        <div class="p-1">
          <form id="ff" class="form-horizontal" role="form" method="post" class="easyui-form form-horizontal" method="post" novalidate enctype="multipart/form-data" style="padding:0 50px">
            <div class="alert alert-warning" style="margin-right:35px; padding: 10px !important; font-size: 1.4em;">. : : Masa Blacklist Selama 2 Tahun : : .</div>
            <div class="row">
              <div class="form-group col-md-11 mb-2">
                <label>Nama Perusahaan</label>
                <input type="text" class="form-control easyui-validatebox span9" name="reqNama" id="reqNama" value="<?=$reqNama?>" title="Nama perusahaan harus diisi"  required >
                <input type="hidden" name="reqRekananId" id="reqRekananId" value="<?=$reqRekananId?>"/>
                <div class="control-group">
                </div>
              </div>
              <div class="form-group col-md-1 mb-2">
                <a title="Tambah" class="btn btn-primary text-white" style="margin: 20px 0 0 10px" id="btnAdd" onClick="openAdd('main/loadUrl/main/rekanan');"><span class="fa fa-plus"></span> Tambah</a>
              </div>
            </div>
            <div class="row">
              <div class="form-group col-md-12 mb-2">
                <label>NPWP</label>
                <input type="text" name="reqNPWP" required id="reqNPWP" onkeydown="return format_npwp(event, 'reqNPWP');" maxlength="20" value="<?=$reqNPWP?>" class="form-control easyui-validatebox span9" />
              </div>
            </div>
            <div class="row">
              <div class="form-group col-md-12 mb-2">
                <label>Alamat</label>
                <textarea name="reqAlamat" id="reqAlamat" title="Alamat harus diisi" class="form-control easyui-validatebox span9" required><?=$reqAlamat?></textarea>
              </div>
            </div>
            <div class="row">
              <div class="form-group col-md-12 mb-2">
                <label>Kota</label>
                <input type="text" name="reqKota" id="reqKota" value="<?=$reqKota?>" required title="Kota harus diisi" class="form-control easyui-validatebox span9"/>
              </div>
            </div>
            <div class="row">
              <div class="form-group col-md-7 mb-2">
                <label>No SK Blacklist</label>
                <input type="text" name="reqNoSk"  value="<?=$reqNoSk?>" title="No sk harus diisi" class="form-control easyui-validatebox span9"/>
              </div>
              <div class="form-group col-md-1 mb-2"></div>
              <div class="form-group col-md-2 mb-2">
                <label style="width: 100%">Tanggal Mulai</label>
                <input type="text" style="width:120px" name="reqTanggalMulai" required id="reqTanggalMulai" value="<?=$reqTanggalMulai?>" title="Tanggal mulai harus diisi" class="easyui-datebox" />
              </div>
              <div class="form-group col-md-2 mb-2">
                <label style="width: 100%">Tanggal Selesai</label>
                <input type="text" style="width:120px" name="reqTanggalSelesai" required id="reqTanggalSelesai" value="<?=$reqTanggalSelesai?>" title="Tanggal selesai harus diisi" class="easyui-datebox"/>
              </div>
            </div>
            <div class="row">
              <div class="form-group col-md-12 mb-2">
                <label>File
                  <?= UPLOAD_PDF_ZIP_10MB ?>
                </label>
                <input type="file" name="reqLinkFile" id="reqLinkFilePDF" class="form-control easyui-validatebox" required validType="fileType['zip', 'pdf']" />
                <input type="checkbox" name="reqPublishPenyedia" id="reqPublishPenyedia" value="1" style="cursor: pointer;"> File Share ke Public ?
              </div>
            </div>
            <div class="row">
              <div class="form-group col-md-12 mb-2">
                <!-- <label>Alasan</label>
                <textarea name="reqAlasan" class="form-control easyui-validatebox span9" required title="Alasan harus diisi" ><?=$reqAlasan;?></textarea> -->
                <input type="checkbox" style="cursor:pointer" id="reqTanggung" required name="reqTanggung"/> Saya yakin bahwa data yang telah saya isi di atas benar.
              </div>
            </div>
            <div class="form-actions">
              <input type="hidden" name="reqId" value="<?=$reqId?>">
              <input type="hidden" name="reqSubmit" id="submitId" value="Simpan"/>
              <a href="#" onClick="top.closePopup()" class="<?= CLASS_BTN_DANGER ?>"> <i class="fa fa-close"></i> Tutup</a>
              <button type="submit" id="btnSubmit" style="display:none" class="<?= CLASS_BTN_PRIMARY ?>"><i class="fa fa-check-square-o"></i> Simpan</button>
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
