<?php
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 *
 */

/* INCLUDE FILE */
$this->load->library("kauth");  $userLogin = new kauth();
$this->load->model("Rekanan");
$this->load->model("RekananAkta");

include_once("functions/string.func.php");
include_once("functions/date.func.php");
include_once("functions/default.func.php");

/* create objects */
$rekanan_akta = new RekananAkta();
$rekanan = new Rekanan();

$reqId			= httpFilterPost("reqId");
$reqAktaType = httpFilterRequest("reqAktaType");
$reqNamaNotaris			= httpFilterPost("reqNamaNotaris");
$reqTanggal			= httpFilterPost("reqTanggal");
$reqNomorAkta			= httpFilterPost("reqNomorAkta");
$reqRekananAktaId= httpFilterPost('reqRekananAktaId');
$reqSubmit= httpFilterPost('reqSubmit');
$reqLinkFile= $_FILES['reqLinkFile'];
$reqLinkFileTemp = httpFilterPost("reqLinkFileTemp");
$reqLinkFileTempTipe = httpFilterPost("reqLinkFileTempTipe");
$reqLinkFileTempUkuran = httpFilterPost("reqLinkFileTempUkuran");
$reqId = $this->ID;

$FILE_DIR = "uploads/landasan_hukum/";

// $rekanan->selectByParams(array("REKANAN_ID"=>$this->ID),-1,-1);
// $rekanan->firstRow();

if($reqAktaType == 3){
	$reqNomor = $rekanan->getField("SURAT_KUASA");
	$reqTanggal = dateToPageCheck($rekanan->getField("SURAT_KUASA_TANGGAL"));
	$reqNotaris = $rekanan->getField("SURAT_KUASA_NOTARIS");
}else{
	$rekanan_akta->selectByParams(array("REKANAN_ID"=>$this->ID, "AKTA_TYPE_ID"=>$reqAktaType),-1,-1);
	$rekanan_akta->firstRow();
	if($rekanan_akta->getField("NOMOR") != ''){
		$reqNomor = $rekanan_akta->getField("NOMOR");
		$reqTanggal = dateToPageCheck($rekanan_akta->getField("TANGGAL"));
		$reqNotaris = $rekanan_akta->getField("NOTARIS");
		$reqRekananAktaId = $rekanan_akta->getField("REKANAN_AKTA_ID");
		$reqLinkFileTemp= $rekanan_akta->getField("PATH_FILE");
		$reqLinkFileTempTipe= $rekanan_akta->getField("TIPE");
		$reqLinkFileTempUkuran= $rekanan_akta->getField("UKURAN");
		$reqLinkFileTempNama= $rekanan_akta->getField("NAMA_FILE");
	}else{
		$reqNomor = $reqNomorAkta;
		$reqTanggal = $reqTanggal;
		$reqNotaris = $reqNamaNotaris;
	}
}

$reqMode = "update";
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

    <script type="text/javascript" src="js/jquery-1.9.1.js"></script>
    <script src="lib/bootstrap/dist/js/bootstrap.min.js"></script>

    <!-- Bootstrap core CSS -->
    <link rel="stylesheet" type="text/css" href="<?=base_url() ?>assets/new/css/vendors.css">

    <link href="lib/bootstrap/dist/css/bootstrap.css" rel="stylesheet">

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

    <!-- EASYUI -->
    <link rel="stylesheet" type="text/css" href="lib/eproc/themes/default/easyui.css">
  <!--<script type="text/javascript" src="js/jquery-1.6.1.min.js"></script>-->
    <script type="text/javascript" src="lib/eproc/jquery.easyui.min.js"></script>
    <script type="text/javascript" src="lib/eproc/kalender-easyui.js"></script>
    <script type="text/javascript" src="lib/eproc/allfunc.js"></script>

    <!-- EMODAL -->
    <script src="lib/emodal/eModal.js"></script>
	<script type="text/javascript">
    $(document).ready(function() {

        $(function(){
            $('#ff').form({
				url:'rekanan_akta_json/data_administrasi_landasan_hukum_ubah',
                onSubmit:function(){
                    return $(this).form('validate');
                },
                success:function(data){
					top.setElementValue('reqDataLandasanHukumLabel','Data Lengkap');
					top.reloadAkta();
                    top.closePopup();
                }
            });

        });

    });
    </script>
	<script src="lib/multifile-master/jquery.MultiFile.js"></script>

  </head>

<body class="body-popup">

      <div class="card mb-1 border-darken-1">
    <div class="card-content">
      <div class="p-1">
        <div class="alert bg-primary alert-icon-left alert-arrow-left alert-dismissible mb-2" role="alert">
          <span class="alert-icon"><i class="fa fa-th"></i></span>  <strong>Data Administrasi - <?php if($reqAktaType == 1) { ?>Akta Pendirian<?php } else { ?>Akta Perubahan Terakhir<?php } ?></strong>
        </div>
        <div class="p-1">
          <form id="ff" class="easyui-form form-horizontal" method="post" novalidate enctype="multipart/form-data">
            <div class="col-md-12">

              <div class="row">
                <div class="form-group col-md-12 mb-2">
                  <label> Nomor Akta</label>
                  <input type="text" name="reqNomorAkta" id="reqNomorAkta" title="Nomor Akta harus diisi" class="form-control easyui-validatebox" value="<?=$reqNomor?>" required />
                </div>
              </div>

              <div class="row">
                <div class="form-group col-md-12 mb-2">
                  <label style="width: 100%"> Tanggal</label>
                  <input type="text" style="width:100px" name="reqTanggal" id="reqTanggal" title="Tanggal harus diisi" class="form-control easyui-datebox" value="<?=$reqTanggal?>" required />
                </div>
              </div>

              <div class="row">
                <div class="form-group col-md-12 mb-2">
                  <label> Nama Notaris</label>
                  <input type="text" name="reqNamaNotaris" id="reqNamaNotaris" title="Nama Notaris harus diisi" class="form-control easyui-validatebox" value="<?=$reqNotaris?>" required />
                </div>
              </div>

              <div class="row">
                <div class="form-group col-md-12 mb-2">
                  <label> File</label>
                  <input type="file" name="reqLinkFile" id="reqLinkFilePDF" size="30" class="easyui-validatebox" <?php if($reqLinkFileTemp == "") { ?> required <?php } ?>  validType="fileType['pdf']" />
                   <input type="hidden" name="reqLinkFileTemp" value="<?=$reqLinkFileTemp?>">
                   <input type="hidden" name="reqLinkFileTempTipe" value="<?=$reqLinkFileTempTipe?>">
                   <input type="hidden" name="reqLinkFileTempUkuran" value="<?=$reqLinkFileTempUkuran?>">
                   <input type="hidden" name="reqLinkFileTempNama" value="<?=$reqLinkFileTempNama?>">
                      file : <?=$reqLinkFileTempNama?>
                </div>
              </div>

            <div class="form-actions">
                <input type="hidden" name="reqAktaType" value="<?=$reqAktaType?>" />
                <input type="hidden" name="reqId" value="<?=$reqId?>" />
                <input type="hidden" name="reqRekananAktaId" value="<?=$reqRekananAktaId?>"/>
                <input type="hidden" name="reqMode" value="<?=$reqMode?>" />
                <a onClick="top.closePopup();" class="btn btn-danger text-white"><i class="fa fa-close"></i> Tutup</a>
                <button type="submit" class="btn btn-primary"><i class="fa fa-check-square-o"></i> Simpan</button>

            </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</body>
