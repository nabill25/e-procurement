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
            <span class="alert-icon"><i class="fa fa-th"></i></span>  <strong>Profil Perusahaan</strong>
          </div>
          <div class="table-responsive">

                <table class="table table-bordered table-hover">
                    <tbody>
                        <tr>
                            <td width="23%">Nama Perorangan:</td>
                            <td>
                                <?=str_replace('Konsultan Perorangan ', '', $reqNamaPerusahaan)?>
                            </td>
                        </tr>
                        <tr>
                            <td><?=translate("Alamat", "Address")?>:</td>
                            <td>
                                <?=$reqAlamat?>
                            </td>
                        </tr>
                        <tr>
                            <td><?=translate("Kota", "City")?>:</td>
                            <td>
                                <?=$reqKota?>
                            </td>
                        </tr>
                        <tr>
                            <td><?=translate("Provinsi", "Province")?>:</td>
                            <td>
                                <?=$reqProvinsi?>
                            </td>
                        </tr>
                        <tr>
                            <td><?=translate("Kode Pos", "Postal Code")?>:</td>
                            <td>
                                <?=$reqKodePos?>
                            </td>
                        </tr>
                        <tr>
                            <td><?=translate("NPWP", "Taxpayer Registration Number")?>:</td>
                            <td>
                                <?=$reqNPWP?>
                            </td>
                        </tr>
                        <tr>
                            <td>File NPWP:</td>
                            <td>
                                <?php
                                    $arrFile = explode(";", $rekanan->getField("NAMA_FILE_NPWP"));
                                    for($iFile=0;$iFile<count($arrFile);$iFile++)
                                    {
                                ?>
                                        <?=$arrFile[$iFile]?>
                                        <?php if ($arrFile[$iFile]) { ?>
                                        <a href="<?= base_url().'uploads/rekanan/'.$rekanan->getField("NPWP_FILE") ?>" class="badge badge-primary"><span class="fa fa-download"></span> Download file NPWP</a>
                                        <?php } ?>
                                <?php
                                    }
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <td>KTP:</td>
                            <td>
                                <?=$reqKTP?>
                            </td>
                        </tr>
                        <tr>
                            <td>File KTP:</td>
                            <td>
                                <?= $reqNamaFileKTP ?>
                                <?php if ($reqKTP) { ?>
                                <a href="<?= base_url('uploads/rekanan/').$reqKTPFile ?>" class="badge badge-primary"><span class="fa fa-download"></span> Download file KTP</a>
                                <?php } ?>
                            </td>
                        </tr>
                        <?php
                        if($reqPKP == '')
                        {}
                        else
                        {
                        ?>
                        <tr>
                            <td><?=translate("PKP", "PKP")?>:</td>
                            <td>
                                <?=$reqPKP?>
                            </td>
                        </tr>
                        <tr>
                            <td><?=translate("Tanggal", "Date")?>:</td>
                            <td>
                                <?=getFormattedDateJson($reqMasaBerlakuPKP)?>
                            </td>
                        </tr>
                        <tr>
                            <td>File:</td>
                            <td>
                            <?php
                                $arrFile = explode(";", $rekanan->getField("NAMA_FILE_PKP"));
                                for($iFile=0;$iFile<count($arrFile);$iFile++)
                                {
                            ?>
                                    <?=$arrFile[$iFile]?>
                                    <?php if ($arrFile[$iFile]) { ?>
                                    <a href="<?= base_url('uploads/rekanan/').$rekanan->getField("PKP_FILE") ?>" class="badge badge-primary"><span class="fa fa-download"></span> Download file PKP</a>
                                    <?php } else { echo "-";} ?>
                            <?php
                                }
                            ?>
                            </td>
                        </tr>
                        <?php
                        }
                        ?>
                        <tr>
                            <td><?=translate("No. telepon", "Telephone")?>:</td>
                            <td>
                                <?=$reqNomorTelepon?>
                            </td>
                        </tr>
                        <tr>
                            <td><?=translate("No. fax", "Faximile")?>:</td>
                            <td>
                                <?=$reqNomorFax?>
                            </td>
                        </tr>
                        <tr>
                            <td><?=translate("Kontak", "Contact")?>:</td>
                            <td>
                                <?=$reqKontakPerson?>
                            </td>
                        </tr>
                        <tr>
                            <td><?=translate("Hp.", "Handphone")?>:</td>
                            <td>
                                <?=$reqKontakPersonHp?>
                            </td>
                        </tr>
                        <tr>
                            <td>E-mail:</td>
                            <td>
                                <?=$reqEmail?>
                            </td>
                        </tr>
                        <tr>
                            <td>Website:</td>
                            <td>
                                <?=$reqWebsite?>
                            </td>
                        </tr>
                        <tr>
                            <td><?=translate("Kualifikasi", "Qualification")?>:</td>
                            <td>
                                <?=$reqKualifikasi?>
                            </td>
                        </tr>
                    </tbody>
                </table>


                <div class="card mb-1 border-blue border-darken-1">
                    <div class="card-content">
                        <div class="p-1">
                            <div class="alert bg-primary alert-icon-left alert-arrow-left alert-dismissible mb-2" role="alert">
                                <span class="alert-icon"><i class="fa fa-th"></i></span>
                                <strong>Informasi Pembayaran (Nomor Rekening)</strong>
                            </div>
                            <div class="table-responsive">
                                <table class="border-double table mb-0">
                                    <tbody>
                                        <tr>
                                            <td style="width: 20%">Bank :</td>
                                            <td>
                                                <?=$rekanan->getField("BANK")?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="width: 20%">No. Rekening :</td>
                                            <td>
                                                <?=$rekanan->getField("BANK_REKENING")?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="width: 20%">Atas Nama :</td>
                                            <td>
                                                <?=$rekanan->getField("BANK_PEMILIK")?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="width: 20%">Cabang :</td>
                                            <td>
                                                <?=$rekanan->getField("BANK_CABANG")?>
                                            </td>
                                        </tr> 
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <?php 
                if ($this->USER_TYPE_ID == '2') { ?>
				<form id="ff2" class="easyui-form " method="post" novalidate enctype="multipart/form-data">
		            <div class="form-actions card-content collapse show border-info border-darken-2 mt-2">
		              <div class="card-body">
		                <?php
		                $checked = '';
		                $cekData = new Rekanan();
		                $cekData->selectByParamsRekananChecklist(array("REKANANID"=>$reqId),-1,-1);
		                $cekData->firstRow();
		                if ($cekData->getField("npwp") == '1') {
		                  $checked = 'checked';
		                }
		                echo '<input class="mb-1" type="checkbox" name="checknpwp" id="checkjenis" onclick="return updateChecklist(\''.$reqId.'\',\'npwp\')" style="cursor:pointer" '.$checked.'> Ya, Lengkap ';
		                ?>
		                <input type="" class="form-control easyui-validatebox span2" required maxlength="255" style="height: 30px !important;" name="" placeholder="catatan" id="catatanjenis" value="<?=$cekData->getField("npwp_note")?>" onChange="return updateChecklist('<?= $reqId ?>','npwp')">
		                <small><sup>*</sup>&nbsp;Tekan enter setalah mengisi catatan</small>
		              </div>
		            </div>
		          </form>
                <?php 
                } ?>

          </div>
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
      if (n == 1) {
          $('#catatanjenis').validatebox({ required:false  });
          $('#catatanjenis').val('');
      } else {
          $('#catatanjenis').validatebox({ required:true  });
      }
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
