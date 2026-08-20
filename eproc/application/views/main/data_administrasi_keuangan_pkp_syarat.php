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

include_once("functions/string.func.php");
include_once("functions/date.func.php");
include_once("functions/default.func.php");

/* create objects */
$rekanan = new Rekanan();
$rekanan_pkp 	= new Rekanan(); // tipe ?

$reqId = $this->ID;

// $rekanan->selectByParams(array("REKANAN_ID"=>$this->ID),-1,-1);
// $rekanan->firstRow();

$reqNoSurat= httpFilterPost("reqNoSurat");
$reqTanggal= httpFilterPost("reqTanggal");
$reqJabatan= httpFilterPost("reqJabatan");
$reqSubmit= httpFilterPost('reqSubmit');

$rekanan_pkp->selectByParams(array("REKANAN_ID"=>$this->ID), -1, -1);
$rekanan_pkp->firstRow();
$reqNoSurat = $rekanan_pkp->getField("PKP");
$reqTanggal = dateToPageCheck($rekanan_pkp->getField("PKP_TANGGAL"));
$reqJabatan = $rekanan_pkp->getField("NPWP");

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

    <!-- Bootstrap core CSS -->
    <link href="lib/bootstrap/dist/css/bootstrap.css" rel="stylesheet">


    <link rel="stylesheet" href="css/gaya.css" type="text/css">
    <link rel="stylesheet" href="css/gaya-bootstrap.css" type="text/css">

    <!-- FONT AWESOME -->
    <link rel="stylesheet" href="lib/font-awesome-4.7.0/css/font-awesome.css" type="text/css">

    <!-- PAGINATION -->
    <link rel="stylesheet" type="text/css" href="lib/drupal/index.css" />

    <!-- EASYUI -->
    <link rel="stylesheet" type="text/css" href="lib/eproc/themes/default/easyui.css">
	<script type="text/javascript" src="js/jquery-1.6.1.min.js"></script>
    <script type="text/javascript" src="lib/eproc/jquery.easyui.min.js"></script>
    <script type="text/javascript" src="lib/eproc/kalender-easyui.js"></script>
    <script type="text/javascript" src="lib/eproc/allfunc.js"></script>

	<script type="text/javascript">
    $(document).ready(function() {

        $(function(){
            $('#ff').form({
                url:'rekanan_json/data_administrasi_keuangan_pkp_ubah',
                onSubmit:function(){
                    return $(this).form('validate');
                },
                success:function(data){
					top.setElementValue('reqDataKeuanganPKP','Data Lengkap');
                    top.reloadPKP();
					top.closePopup();
                }
            });

        });

    });
    </script>

</head>


<body class="body-popup">
	<div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="area-main popup">
                    <div class="judul-halaman">Data Administrasi - Keuangan </div>
                    <div class="inner">
                        <div class="area-konten">
                            <div class="area-konten-inner">

                                <form id="ff" class="easyui-form form-horizontal" method="post" data-options="novalidate:true" enctype="multipart/form-data">

                                    <div class="judul-grup">Data PKP</div>

                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="inputEmail" class="col-md-2 control-label harus-diisi">a. No. Surat</label>
                                                <div class="col-md-8">
                                                <input type="text" name="reqNoSurat" id="reqNoSurat" title="No surat harus diisi" class="form-control" style="width:250px" value="<?=$reqNoSurat?>" />
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="inputEmail" class="col-md-2 control-label harus-diisi">b. Tanggal</label>
                                                <div class="col-md-8">
                                                  <input type="text" style="width:120px" name="reqTanggal" title="Tanggal PKP harus diisi" class="form-control easyui-datebox" id="reqTanggal" value="<?=$reqTanggal?>" />
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="inputEmail" class="col-md-2 control-label harus-diisi">c. NPWP</label>
                                                <div class="col-md-8">
                                                   <input type="text" name="reqJabatan" id="reqJabatan" value="<?=$reqJabatan?>" onkeydown="return format_npwp(event, 'reqJabatan');" class="form-control" maxlength="20" />
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="inputEmail" class="col-md-2 control-label">&nbsp;</label>
                                                <div class="col-md-4">
                                                    <button type="submit" class="btn btn-primary">Simpan</button>
                                                    <button type="button" class="btn btn-primary" onClick="top.closePopup()">Batal</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
 	</div>
</body>
