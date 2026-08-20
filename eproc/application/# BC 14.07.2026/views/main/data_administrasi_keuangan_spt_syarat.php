<?php
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 *
 */

/* INCLUDE FILE */
include_once("functions/string.func.php");
include_once("functions/date.func.php");
include_once("functions/default.func.php");
$this->load->library("paketinfo"); $paketInfo = new paketinfo();
$this->load->model("Rekanan");
$this->load->model("Paket");
$this->load->model("RekananPajak");

/* create objects */
$rekanan = new Rekanan();
$rekanan_spt	= new RekananPajak(); // tipe 1

$reqPaketId = $this->input->get("reqPaketId");
$reqRekananPajakId= $this->input->get("reqRekananPajakId");
$reqTahunPajak= $this->input->get("reqTahunPajak");

// $rekanan->selectByParams(array("REKANAN_ID"=>$this->ID),-1,-1);
// $rekanan->firstRow();

$paketInfo->getPaket($reqPaketId);

if($reqTahunPajak == '')
	$reqTahunPajak = $paketInfo->syarat_keuangan_spt_tahun;

$rekanan_spt_view = new RekananPajak();
$rekanan_spt_view->selectByParams(array("TAHUN"=>$reqTahunPajak, "REKANAN_ID"=>$this->ID, "TIPE"=>1), -1, -1, "", "");
$rekanan_spt_view->firstRow();
$reqTahun = $rekanan_spt_view->getField('TAHUN');
$reqNomor = $rekanan_spt_view->getField('NOMOR');
$reqTanggal = dateToPageCheck($rekanan_spt_view->getField('TANGGAL'));
$reqRekananPajakId = $rekanan_spt_view->getField('REKANAN_PAJAK_ID');

if($reqRekananPajakId == '')
	$reqMode='insert';
else
	$reqMode='update'

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
                url:'rekanan_pajak_json/data_administrasi_keuangan_spt_syarat',
                onSubmit:function(){
                    return $(this).form('validate');
                },
                success:function(data){
					if(data == "1")
						top.setElementValue('reqDataKeuanganSPT','Data Lengkap');

					top.reloadSPT();
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


                                <div class="informasi">
                                    <ul>
                                        <li>Silahkan melengkapi data SPT Tahunan <?=$paketInfo->syarat_keuangan_spt_tahun?>.</li>
                                    </ul>
                                </div>
                                <form id="ff" class="easyui-form form-horizontal" method="post" novalidate enctype="multipart/form-data">

                                    <div class="judul-grup">Data SPT Tahunan</div>

                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="inputEmail" class="col-md-2 control-label">a. Tahun *</label>
                                                <div class="col-md-8">
                                                <input type="hidden" name="reqTahunPajak_last" value="<?=$reqTahun?>" />
                                                <select name="reqTahun" id="reqTahun" onchange="document.location.href='main/loadUrl/main/data_administrasi_keuangan_spt_syarat/?reqPaketId=<?=$reqPaketId?>&reqTahunPajak='+this.value" class="form-control" style="width:100px;">
                                                    <?php
                                                    for($i=date('Y')-2;$i<=date("Y")+1; $i++)
                                                    {
                                                    ?>
                                                      <option value="<?=$i?>" <?php if($i == $reqTahunPajak) { ?> selected="selected" <?php } ?>><?=$i?></option>
                                                    <?php
                                                    }
                                                    ?>
                                                </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="inputEmail" class="col-md-2 control-label">b. Nomor *</label>
                                                <div class="col-md-8">
                                                  <input title="Nomor SPT harus diisi" class="form-control easyui-validatebox" required type="text" name="reqNomor" id="reqNomor" style="width:250px" value="<?=$reqNomor?>"/>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="inputEmail" class="col-md-2 control-label">c. Tanggal *</label>
                                                <div class="col-md-8">
                                                  <input title="Tanggal harus diisi" class="form-control easyui-datebox" required  type="text" style="width:120px" name="reqTanggal" id="reqTanggal" value="<?=$reqTanggal?>" />
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="inputEmail" class="col-md-2 control-label">&nbsp;</label>
                                                <div class="col-md-4">
                                                    <input type="hidden" name="reqMode" value="<?=$reqMode?>" />
                                                    <input type="hidden" name="reqRekananPajakId" value="<?=$reqRekananPajakId?>" />
                                                    <input type="hidden" name="reqTahunSPT" value="<?=$paketInfo->syarat_keuangan_spt_tahun?>" />

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
