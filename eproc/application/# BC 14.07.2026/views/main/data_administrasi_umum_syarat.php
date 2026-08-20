<?php
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 *
 */

/* INCLUDE FILE */
$this->load->model("Rekanan");
$this->load->library("paketinfo"); $paketInfo = new paketinfo();
include_once("functions/string.func.php");
include_once("functions/default.func.php");

/* create objects */
$rekanan = new Rekanan();

$reqId			= $this->input->get("reqId");
$reqKualifikasi	= $this->input->get("reqKualifikasi");
$reqPaketId= $this->input->get("reqPaketId");

$paketInfo->getPaket($reqPaketId);

$rekanan->selectByParams(array("A.REKANAN_ID"=>$this->ID),-1,-1);
$rekanan->firstRow();

$reqKualifikasi = $rekanan->getField("REKANAN_KUALIFIKASI_ID");
$reqMail = $rekanan->getField("EMAIL");
$reqFaxKode = $rekanan->getField("FAX_KODE");
$reqFaxNo = $rekanan->getField("FAX");
$reqTeleponKode = $rekanan->getField("TELEPON_KODE");
$reqTeleponNo = $rekanan->getField("TELEPON");
$reqKota = $rekanan->getField("KOTA");
$reqAlamat = $rekanan->getField("ALAMAT");
$reqStatus = $rekanan->getField("STATUS_PERUSAHAAN");
$reqNPWP = $rekanan->getField("NPWP");
$reqRekananTipeID= $rekanan->getField("REKANAN_TIPE_ID");
$reqNama= $rekanan->getField("REKANAN_NAMA");

$reqMailPusat = $rekanan->getField("EMAIL_PUSAT");
$reqFaxKodePusat = $rekanan->getField("FAX_KODE_PUSAT");
$reqFaxNoPusat = $rekanan->getField("FAX_PUSAT");
$reqTeleponKodePusat = $rekanan->getField("TELEPON_KODE_PUSAT");
$reqTeleponNoPusat = $rekanan->getField("TELEPON_PUSAT");
$reqAlamatPusat = $rekanan->getField("ALAMAT_PUSAT");

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
                url:'rekanan_json/data_administrasi_umum_syarat',
                onSubmit:function(){
                    return $(this).form('validate');
                },
                success:function(data){

				if(data == "1")
					top.setElementValue('reqDataKlasifikasiLabel','Data Lengkap');

					top.reloadKualifikasi();
					top.closePopup();

                }
            });

        });

    });
    </script>

    <style>
    span.combo{
        *border:2px solid red !important;
        *width:100% !important;
    }
    span.combo input{
        *width:calc(100% - 18px) !important;
    }
    </style>
 </head>


<body class="body-popup">
	<div class="container-fluid">

        <div class="row">
            <div class="col-md-12">
                <div class="area-main popup">

                    <div class="judul-halaman">Data Administrasi - Umum</div>

                    <div class="inner">
                        <div class="area-konten">

                            <div class="area-konten-inner">

                                <form id="ff" class="easyui-form form-horizontal" method="post" novalidate enctype="multipart/form-data">

                                    <div class="judul-grup">Profil Perusahaan</div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="inputEmail" class="col-md-2 control-label">Nama Perusahaan :</label>

                                                <div class="form-kolom">
												<?php
                                                if($reqRekananTipeID == '1') echo 'PT';
                                                elseif($reqRekananTipeID == '2') echo 'CV';
                                                elseif($reqRekananTipeID == '3') echo 'Firma';
                                                elseif($reqRekananTipeID == '4') echo 'Koperasi';
                                                elseif($reqRekananTipeID == '5') echo 'UD';
                                                elseif($reqRekananTipeID == '6') echo 'Lain-lain';
                                                ?>
                                                <?=' '.$reqNama?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="inputEmail" class="col-md-2 control-label">NPWP :</label>
                                                <div class="col-md-5">
                                                    <?=$reqNPWP?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                     <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="inputEmail" class="col-md-2 control-label">Kualifikasi :</label>
                                                <div class="col-md-5">
                                                     <input type="radio" <?php if($reqKualifikasi == '1') echo 'checked';?>  name="reqKualifikasi" value="1" /> Kecil &nbsp;&nbsp;&nbsp;
                                                     <input type="radio" <?php if($reqKualifikasi == '2') echo 'checked';?> name="reqKualifikasi" value="2" /> Non Kecil
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="inputEmail" class="col-md-2 control-label">&nbsp;</label>
                                                <div class="col-md-4">
                                                	<input type="hidden" name="reqKualifikasiSyarat" value="<?=$paketInfo->kualifikasi_id?>">
                                                    <button type="submit" class="btn-simpan">Simpan</button>
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
