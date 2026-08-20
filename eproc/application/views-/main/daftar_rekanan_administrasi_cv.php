<?php
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 *
 */

$this->load->library("kauth");
$userLogin = new kauth();
$this->load->model("Rekanan");
include_once("functions/string.func.php");
include_once("functions/date.func.php");
include_once("functions/default.func.php");
$rekanan = new Rekanan();

$reqId = $this->input->get("reqId");

$FILE_DIR = "uploads/rekanan/";

$rekanan->selectByParams(array("A.REKANAN_ID"=>$reqId),-1,-1);
$rekanan->firstRow();

$tempKualifikasi = $rekanan->getField("REKANAN_KUALIFIKASI");
$tempMail = $rekanan->getField("EMAIL");
$tempFax = $rekanan->getField("FAX_KODE")."-".$rekanan->getField("FAX");
$tempTelepon = $rekanan->getField("TELEPON_KODE")."-".$rekanan->getField("TELEPON");
$tempKota = $rekanan->getField("KOTA");
$tempAlamat = $rekanan->getField("ALAMAT");
$tempKodepos = $rekanan->getField("KODEPOS");
if($rekanan->getField("STATUS_PERUSAHAAN") == 0)
{
	$tempStatus = "Pusat";
} else {
	$tempStatus = "Cabang";
}
$tempNPWP = $rekanan->getField("NPWP");
$tempNPWPFILE = $rekanan->getField("NPWP_FILE");
$tempNama= $rekanan->getField("NAMA");
$tempKontakPerson= $rekanan->getField("KONTAK_PERSON");
$tempKontakPersonHp= $rekanan->getField("KONTAK_PERSON_HP");
$tempWebsite= $rekanan->getField("WEBSITE");

$reqNamaPerusahaan = $rekanan->getField("NAMA");
$reqAlamat = $rekanan->getField("ALAMAT");
$reqKota = $rekanan->getField("KOTA");
$reqProvinsi = $rekanan->getField("REGION");
$reqKodePos = $rekanan->getField("KODEPOS");
$reqStatus = $rekanan->getField("STATUS_CP");
$reqNPWP = $rekanan->getField("NPWP");
$reqLinkFileNPWPTemp = $rekanan->getField("NAMA_FILE_NPWP");
$reqPKP = $rekanan->getField("PKP");
$reqKTP = $rekanan->getField("KTP");
$reqKTPFile = $rekanan->getField("KTP_FILE");
$reqNamaFileKTP = $rekanan->getField("NAMA_FILE_KTP");
$reqMasaBerlakuPKP = $rekanan->getField("PKP_TANGGAL");
$reqLinkFileTemp = $rekanan->getField("PKP_TANGGAL");
$reqMasaBerlakuPKP = $rekanan->getField("PKP_TANGGAL");
$reqLinkFileTemp = $rekanan->getField("NAMA_FILE_PKP");
$reqCVFile = $rekanan->getField("CV_FILE");
$reqNomorTelepon = $rekanan->getField("TELEPON_FULL");
$reqNomorFax = $rekanan->getField("FAX_FULL");
$reqEmail = $rekanan->getField("EMAIL");
$reqKualifikasi = $rekanan->getField("REKANAN_KUALIFIKASI");
$reqKontakPerson = $rekanan->getField("KONTAK_PERSON");
$reqKontakPersonHp = $rekanan->getField("KONTAK_PERSON_HP");
$reqWebsite = $rekanan->getField("WEBSITE");
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

  </head>

<body class="body-popup">

    <div class="card mb-1">
      <div class="card-content">
        <div class="p-1">
          <div class="alert bg-primary alert-icon-left alert-arrow-left alert-dismissible mb-2" role="alert">
            <span class="alert-icon"><i class="fa fa-th"></i></span>  <strong>CV (Daftar Riwayat Hidup)</strong>
          </div>
          <div class="table-responsive">
                    <iframe src="<?=$FILE_DIR.str_replace("'", "''", $reqCVFile)?>" style="width:100%; height:600px;"></iframe>
          </div>
					<form id="ff2" class="easyui-form " method="post" novalidate enctype="multipart/form-data">
						<div class="form-actions card-content collapse show border-info border-darken-2 mt-2">
							<div class="card-body">
								<?php
								$checked = '';
								$cekData = new Rekanan();
								$cekData->selectByParamsRekananChecklist(array("REKANANID"=>$reqId),-1,-1);
								$cekData->firstRow();
								if ($cekData->getField("cv") == '1') {
									$checked = 'checked';
								}
								echo '<input class="mb-1" type="checkbox" name="checknpwp" id="checkjenis" onclick="return updateChecklist(\''.$reqId.'\',\'cv\')" style="cursor:pointer" '.$checked.'> Ya, Lengkap ';
								?>
								<input type="" class="form-control easyui-validatebox span2" required maxlength="255" style="height: 30px !important;" name="" placeholder="catatan" id="catatanjenis" value="<?=$cekData->getField("cv_note")?>" onChange="return updateChecklist('<?= $reqId ?>','cv')">
								<small><sup>*</sup>&nbsp;Tekan enter setalah mengisi catatan</small>
							</div>
						</div>
					</form>
        </div>
      </div>
    </div>
		<!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="lib/bootstrap/libs/jquery/1.12.4/jquery.min.js"></script>
    <script>window.jQuery || document.write('<script src="lib/bootstrap/assets/js/vendor/jquery.min.js"><\/script>')</script>
    <script src="lib/bootstrap/dist/js/bootstrap.min.js"></script>
    <link rel="stylesheet" type="text/css" href="<?=base_url()?>lib/eproc/themes/default/easyui.css">
    <script type="text/javascript" src="<?=base_url()?>lib/eproc/jquery.easyui.min.js"></script>
    <script type="text/javascript" src="<?=base_url()?>lib/eproc/kalender-easyui.js"></script>
    <script type="text/javascript" src="<?=base_url()?>lib/eproc/allfunc.js"></script>

    <script type="text/javascript">
    function updateChecklist(rekananid,jenis) {
      var n = $("#checkjenis:checked").length;
      var c = $("#catatanjenis").val();
      // alert(n+'-'+c+'-'+rekananid+'-'+jenis); return false;
        $.getJSON("rekanan_json/updateChecklist2/?rekananid="+rekananid+"&jenis="+jenis+"&status="+n+"&catatan="+c,
          function(data){
            if (data.RESPONSE === 'Gagal') {
              $.messager.alert('Info', data.PESAN, 'info');
              if (n === 0) { // kalau gagal balik ke awal
                $("#checkjenis").prop("checked", true);
              } else {
                $("#checkjenis").prop("checked", false);
              }
            }
        });
    }
    </script>
</body>
