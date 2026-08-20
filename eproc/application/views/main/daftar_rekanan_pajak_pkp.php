<?php
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 *
 */

$this->load->library("kauth");  $userLogin = new kauth();
$this->load->model("Rekanan");
//$this->load->library("rekananijinusahainfo");  $ijin_usaha_tanggal_berakhir = new rekananijinusahainfo();
include_once("functions/string.func.php");
include_once("functions/date.func.php");
include_once("functions/default.func.php");


$rekanan = new Rekanan();
$rekanan_pkp 	= new Rekanan(); // tipe ?
$rekanan_get_nama = new Rekanan();

/* VARIABLE */
$reqId = $this->input->get("reqId");
$reqPaketId = $this->input->get("reqPaketId");
$reqKeuangan = $this->input->get("reqKeuangan");
$reqMode = $this->input->get("reqMode");
$reqKoreksi = httpFilterRequest("reqKoreksi");
$reqSubmit = httpFilterRequest("reqSubmit");

$rekanan_pkp->selectByParams(array("A.REKANAN_ID"=>$reqId), -1, -1);
$rekanan_pkp->firstRow();
$reqNoSuratPKP = $rekanan_pkp->getField("PKP");
$reqTanggalPKP = getFormattedDateJson($rekanan_pkp->getField("PKP_TANGGAL"));
$reqNPWP = $rekanan_pkp->getField("NPWP");
$reqStatusPKP = $rekanan_pkp->getField("STATUS_PKP");
$reqSKTPKP = $rekanan_pkp->getField("SKT_PKP_NOMOR");
$reqSKTPKPFileTemp = $rekanan_pkp->getField("SKT_PKP_FILE");
$reqNamaFileSKTPKP = $rekanan_pkp->getField("NAMA_SKT_PKP_FILE");

$tempNPWPFILE = $rekanan_pkp->getField("NPWP_FILE");
$tempKontakPerson= $rekanan_pkp->getField("KONTAK_PERSON");
$tempKontakPersonHp= $rekanan_pkp->getField("KONTAK_PERSON_HP");
$tempWebsite= $rekanan_pkp->getField("WEBSITE");


$reqPKP = $rekanan_pkp->getField("PKP");
$reqMasaBerlakuPKP = $rekanan_pkp->getField("PKP_TANGGAL");
$reqLinkFileTemp = $rekanan_pkp->getField("PKP_TANGGAL");
$reqMasaBerlakuPKP = $rekanan_pkp->getField("PKP_TANGGAL");
$reqLinkFileTemp = $rekanan_pkp->getField("NAMA_FILE_PKP");


$reqStatusPKP = $rekanan_pkp->getField("STATUS_PKP");
$reqSKTPKP = $rekanan_pkp->getField("SKT_PKP_NOMOR");
$reqSKTPKPFileTemp = $rekanan_pkp->getField("SKT_PKP_FILE");
$reqNamaFileSKTPKP = $rekanan_pkp->getField("NAMA_SKT_PKP_FILE");
$reqNONPKPFileTemp = $rekanan_pkp->getField("NON_PKP_FILE");
$reqNamaFileNONPKP = $rekanan_pkp->getField("NAMA_NON_PKP_FILE");

$rekanan_get_nama->selectByParams(array("A.REKANAN_ID"=>$reqId),-1,-1);
$rekanan_get_nama->firstRow();
$tempNama_getNama= $rekanan_get_nama->getField("NAMA");
$reqStatusValidasi = $rekanan_get_nama->getField("STATUS_VALIDASI");
$reqUserStatus = $rekanan_get_nama->getField("USER_STATUS");

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

<body>

  <div class="card mb-1">
    <div class="card-content">
      <div class="p-1">
        <div class="alert bg-primary alert-icon-left alert-arrow-left alert-dismissible mb-2" role="alert">
          <span class="alert-icon"><i class="fa fa-th"></i></span>
          <strong>
            <?php
            if($reqStatusPKP == '0') { echo "Non PKP";
            } else { echo "PKP"; }  
            ?>
          </strong>
        </div>
        <p class="alert alert-primary" style="background-color: transparent !important;"><b><?= $tempNama_getNama ?></b></p>
        <div class="table-responsive">
            <table class="table table-striped table-hover" style="width: 100%">

                <tr>
                    <td>Status PKP:</td> <td><?php if($reqStatusPKP == '1') { echo "PKP"; } else { echo "Non PKP"; } ?></td>
                </tr>
                  <?php
                    if($reqStatusPKP == '0')
                    {
                    ?>
                      <tr>
                        <td style="width:25%">File Non PKP:</td>
                        <td>
                        <?php
                            $arrFile = explode(";", $rekanan_pkp->getField("NAMA_NON_PKP_FILE"));
                            for($iFile=0;$iFile<count($arrFile);$iFile++)
                            {
                        ?>
                                <?php if (file_exists('uploads/rekanan/'.$rekanan_pkp->getField("NON_PKP_FILE")) && $rekanan_pkp->getField("NON_PKP_FILE") != '' ) {
                                echo $arrFile[$iFile]; ?> <br>
                                <a href="<?= base_url('uploads/rekanan').'/'.$rekanan_pkp->getField("NON_PKP_FILE") ?>" class="badge badge-primary" target="_blank"><span class="fa fa-download"></span> Download file PKP</a>
                                <?php } else { echo '<span class="badge badge-danger">Belum upload</span>';} ?>
                        <?php
                            }
                        ?>
                        </td>
                      </tr>
                    <?php
                    }
                    else
                    {
                    ?>
                    <tr>
                        <td style="width:25%"><?=translate("Nomor SPPKP", "PKP")?>:</td>
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
                        <td>File SPPKP:</td>
                        <td>
                        <?php
                            $arrFile = explode(";", $rekanan_pkp->getField("NAMA_FILE_PKP"));
                            for($iFile=0;$iFile<count($arrFile);$iFile++)
                            {
                        ?>
                        <?php if (file_exists('uploads/rekanan/'.$rekanan_pkp->getField("PKP_FILE")) && $rekanan_pkp->getField("PKP_FILE") != '' ) {
                                echo $arrFile[$iFile]; ?> <br>
                                <a href="<?= base_url('uploads/rekanan').'/'.$rekanan_pkp->getField("PKP_FILE") ?>" class="badge badge-primary" target="_blank"><span class="fa fa-download"></span> Download file PKP</a>
                                <?php } else { echo '<span class="badge badge-danger">Belum upload</span>';} ?>
                        <?php
                            }
                        ?>
                        </td>
                    </tr>
                    <!-- <tr>
                        <td>Nomor. SKT PKP:</td> <td><?php // echo $reqSKTPKP ?></td>
                    </tr>
                    <tr>
                        <td>File SKT PKP:</td>
                        <td>
                          <?php // if (file_exists('uploads/rekanan/'.$rekanan_pkp->getField("SKT_PKP_FILE")) && $rekanan_pkp->getField("SKT_PKP_FILE") != '' ) { ?>
                          <?php // echo $rekanan_pkp->getField("NAMA_SKT_PKP_FILE") ?> <br>
                          <a href="<?php // echo base_url('uploads/rekanan').'/'.$reqSKTPKPFileTemp ?>" class="badge badge-primary" target="_blank"><span class="fa fa-download"></span> Download file SKT PKP</a>
                          <?php // } else { echo '<span class="badge badge-danger">Belum upload</span>';} ?>
                        </td>
                    </tr> -->
                    <?php
                    }
                    ?>
            </table>

        </div>

        <?php 
        if ($this->USER_TYPE_ID == '2' && ($reqStatusValidasi != '1' || $reqUserStatus == '0')) { ?>
        <form id="ff2" class="easyui-form " method="post" novalidate enctype="multipart/form-data">
          <div class="form-actions card-content collapse show border-info border-darken-2 mt-2">
            <div class="card-body">
              <?php
              $checked = '';
              $cekData = new Rekanan();
              $cekData->selectByParamsRekananChecklist(array("REKANANID"=>$reqId),-1,-1);
              $cekData->firstRow();
              if ($cekData->getField("pkp") == '1') {
                $checked = 'checked';
              }
              echo '<input class="mb-1" type="checkbox" name="checkpkp" id="checkjenis" onclick="return updateChecklist(\''.$reqId.'\',\'pkp\')" style="cursor:pointer" '.$checked.'> Ya, Lengkap ';
              ?>
              <input type="" class="form-control easyui-validatebox span2" required maxlength="255" style="height: 30px !important;" name="" placeholder="catatan" id="catatanjenis" value="<?=$cekData->getField("pkp_note")?>" onChange="return updateChecklist('<?= $reqId ?>','pkp')">
              <small><sup>*</sup>&nbsp;Tekan enter setelah mengisi catatan</small>
            </div>
          </div>
        </form>
        <?php 
        } ?>
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

  </body>
</html>
