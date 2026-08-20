<?php
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 *
 */

/* INCLUDE FILE */
$this->load->model("Berita");
include_once("functions/string.func.php");
include_once("functions/date.func.php");
include_once("functions/default.func.php");

$berita = new Berita();

$reqId	= $this->input->get("reqId") ?: '0';

if($reqId=='0')
	$reqMode= 'insert';
else
	$reqMode ='update';

$berita->selectByParams(array("BERITA_ID" => $reqId));
$berita->firstRow();

$reqNama = $berita->getField("NAMA");
$reqKeterangan = $berita->getField("KETERANGAN");
$reqLinkFileTemp = $berita->getField("LAMPIRAN");
$reqTanggal = dateToPageCheck($berita->getField("TANGGAL"));
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
    <script type="text/javascript">
    tinymce.init({
        selector: "textarea.textarea-tinymce",
    plugins: "image",
        toolbar: "insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image",
        menubar: false,

    });
    </script>

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
  			url:'berita_json/add',
  			onSubmit:function(){
  				return $(this).form('validate');
  			},
  			success:function(data){
  			    $.messager.alert('Info', data, 'info');
  				top.reloadMonitoring();
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
          <span class="alert-icon"><i class="fa fa-th"></i></span>  <strong>Berita</strong>
        </div>
        <div class="p-1">
          <form id="ff" class="easyui-form form-horizontal" method="post" novalidate enctype="multipart/form-data" style="padding:0 50px">
            <div class="row">
              <div class="form-group col-md-12 mb-2">
                <label>Nama</label>
                <input type="text" name="reqNama" value="<?=$reqNama?>" title="" class="form-control easyui-validatebox span9" required >
              </div>
            </div>
            <div class="row">
              <div class="form-group col-md-12 mb-2">
                <label>Keterangan</label>
                <textarea name="reqKeterangan" cols="50" rows="5" title="Alamat harus diisi" class="textarea-tinymce form-control easyui-validatebox span9" required ><?=$reqKeterangan;?></textarea>
              </div>
            </div>
            <div class="row">
              <div class="form-group col-md-2 mb-2">
                <label style="width: 100%">Tanggal</label>
                <input type="text" style="width:160px" name="reqTanggal" id="reqTanggal" title="Tanggal harus diisi" class="form-control easyui-datebox" value="<?=$reqTanggal?>" required />
              </div>
              <div class="form-group col-md-10 mb-2">
                <label>Lampiran<sup>*</sup> <small><?= UPLOAD_PDF_2MB ?> </small> </label><br>
                <input type="file" name="reqLinkFile" id="reqLinkFile" size="30" <?php if($reqLinkFileTemp == "") { ?> class="easyui-validatebox span9" <?php } ?> maxlength="1" validType="fileType['pdf']"/>
                 <input type="hidden" name="reqLinkFileTemp" value="<?=$reqLinkFileTemp?>">
                 <?php
                 if ($reqLinkFileTemp) {
                    echo "<br>file: ".$reqLinkFileTemp;
                  } ?>
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


    <link rel="stylesheet" type="text/css" href="<?=base_url()?>lib/eproc/themes/default/easyui.css">
    <script type="text/javascript" src="js/jquery-1.6.1.min.js"></script>
    <script type="text/javascript" src="<?=base_url()?>lib/eproc/jquery.easyui.min.js"></script>
    <script type="text/javascript" src="<?=base_url()?>lib/eproc/kalender-easyui.js"></script>
    <script type="text/javascript" src="<?=base_url()?>lib/eproc/allfunc.js"></script>

  </body>
</html>
