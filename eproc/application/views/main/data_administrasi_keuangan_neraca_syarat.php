<?php
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 *
 */

/* INCLUDE FILE */
$this->load->library("paketinfo"); $paketInfo = new paketinfo();
$this->load->model("Rekanan");
$this->load->model("RekananNeraca");

include_once("functions/string.func.php");
include_once("functions/date.func.php");
include_once("functions/default.func.php");

ini_set("memory_limit","500M");
ini_set('max_execution_time', 520);

/* create objects */
$rekanan = new Rekanan();
$rekanan_neraca	= new RekananNeraca();

$reqPaketId = $this->input->get("reqPaketId");
$reqTahunNeraca = $this->input->get("reqTahunNeraca");

$paketInfo->getPaket($reqPaketId);

$tahun = substr($paketInfo->syarat_neraca_tahun, 0, 4);
if($reqTahunNeraca == ""){
	$reqTahunNeraca = $tahun;
}
// 
// $rekanan->selectByParams(array("REKANAN_ID"=>$this->ID),-1,-1);
// $rekanan->firstRow();

$rekanan_neraca->selectByParams(array("REKANAN_ID"=>$this->ID, "TAHUN"=>$reqTahunNeraca), -1, -1);
$rekanan_neraca->firstRow();
$reqModalNeraca = $rekanan_neraca->getField("MODAL");
$reqAuditNamaNeraca = $rekanan_neraca->getField("AUDIT_NAMA");
$reqAuditNomorNeraca = $rekanan_neraca->getField("AUDIT_NOMOR");
$reqAuditTanggalNeraca = dateToPageCheck($rekanan_neraca->getField("AUDIT_TANGGAL"));
$reqAuditKeteranganNeraca = $rekanan_neraca->getField("AUDIT_KESIMPULAN");
$reqLinkFileTemp= $rekanan_neraca->getField("PATH_FILE");
$reqLinkFileTempTipe= $rekanan_neraca->getField("TIPE");
$reqLinkFileTempUkuran= $rekanan_neraca->getField("UKURAN");
$reqLinkFileTempNama = $rekanan_neraca->getField("NAMA_FILE");
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
                url:'rekanan_neraca_json/data_administrasi_keuangan_neraca_syarat',
                onSubmit:function(){
                    return $(this).form('validate');
                },
                success:function(data){
					if(data == "1")
						top.setElementValue("reqDataKeuanganNeraca", "Data Lengkap");

                    top.reloadNeraca();
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
                                        <li>Silahkan melengkapi terlebih dahulu Neraca Keuangan Tahun <?=$paketInfo->syarat_neraca_tahun?>.</li>
                                    </ul>
                                </div>
                                <form id="ff" class="easyui-form form-horizontal" method="post" novalidate enctype="multipart/form-data">
                                    <div class="judul-grup">Neraca</div>

                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="inputEmail" class="col-md-2 control-label">a. Tahun</label>
                                                <div class="col-md-8">
                                                    <select name="reqTahunNeraca" id="reqTahunNeraca" onChange="document.location.href='main/loadUrl/main/data_administrasi_keuangan_neraca_syarat/?reqPaketId=<?=$reqPaketId?>&reqTahunNeraca='+this.value" >
                                                        <?php
                                                        for($i=date('Y')-2;$i<=date('Y')+1; $i++)
                                                        {
                                                        ?>
                                                          <option value="<?=$i?>" <?php if($i == $reqTahunNeraca) { ?> selected="selected" <?php } ?>><?=$i?></option>
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
                                                <label for="inputEmail" class="col-md-2 control-label harus-diisi">b. Kekayaan Bersih</label>
                                                <div class="col-md-8">
                                                    <input name="reqKekayaanBersih" type="text"  class="form-control easyui-validatebox" id="reqKekayaanBersih" value="<?=numberToIna($reqModalNeraca)?>" OnFocus="FormatAngka('reqKekayaanBersih')" OnKeyUp="FormatUang('reqKekayaanBersih')" OnBlur="FormatUang('reqKekayaanBersih')" required />
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    Audit (Wajib di isi jika ingin mengikuti pelelangan dengan nilai di atas 2M)
                                     <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="inputEmail" class="col-md-2 control-label">c. Auditor</label>
                                                <div class="col-md-8">
                                                    <input type="text" name="reqAuditor" id="reqAuditor" class="form-control" value="<?=$reqAuditNamaNeraca?>" />
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="inputEmail" class="col-md-2 control-label">d. Nomor</label>
                                                <div class="col-md-8">
                                                    <input type="text" name="reqNomor" id="reqNomor" class="form-control" style="width:250px" value="<?=$reqAuditNomorNeraca?>" />
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="inputEmail" class="col-md-2 control-label">e. Tanggal</label>
                                                <div class="col-md-8">
                                                    <input type="text" style="width:120px" name="reqTanggal" class="form-control easyui-datebox" id="reqTanggal" value="<?=$reqAuditTanggalNeraca?>" />
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="inputEmail" class="col-md-2 control-label">f. Kesimpulan</label>
                                                <div class="col-md-8">
                                                    <textarea name="reqKesimpulan" id="reqKesimpulan" cols="45" class="form-control" rows="5"><?=$reqAuditKeteranganNeraca?></textarea>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="inputEmail" class="col-md-2 control-label harus-diisi">g. File </label>
                                                <div class="col-md-8">
                                                   <input type="hidden" name="reqLinkFileTemp" value="<?=$reqLinkFileTemp?>" />
                                                    <input type="hidden" name="reqLinkFileTempTipe" value="<?=$reqLinkFileTempTipe?>" />
                                                    <input type="hidden" name="reqLinkFileTempUkuran" value="<?=$reqLinkFileTempUkuran?>" />
                                                    <input type="file" name="reqLinkFile" id="reqLinkFilePDF" size="30" <?php if($reqLinkFileTemp == "") { ?> required <?php } ?>  class="easyui-validatebox"  validType="fileType['pdf']" />
                                                     <input type="hidden" name="reqLinkFileTempNama" value="<?=$reqLinkFileTempNama?>">
                                                      temp : <?=$reqLinkFileTempNama?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="inputEmail" class="col-md-2 control-label">&nbsp;</label>
                                                <div class="col-md-4">

                                                    <input type="hidden" name="reqTahunNeracaSyarat" value="<?=$paketInfo->syarat_neraca_tahun?>" />
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
