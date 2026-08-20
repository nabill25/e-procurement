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
$this->load->model("Rekanan");
$this->load->model("RekananSaham");
$this->load->model("RekananPajak");
$this->load->model("RekananNeraca");
$this->load->library("paketinfo"); $paketInfo = new paketinfo();

$reqId = $this->input->get("reqId");
$reqPaketId =  $this->input->get("reqPaketId");
$reqKeuangan = $this->input->get("reqKeuangan");
$reqMode = $this->input->get("reqMode");
$reqKoreksi = $this->input->get("reqKoreksi");
$reqSubmit = httpFilterRequest("reqSubmit");

$rekanan = new Rekanan();
$rekanan_spt	= new RekananPajak(); // tipe 1

$rekanan->selectByParams(array("A.REKANAN_ID"=>$reqId),-1,-1);
$rekanan->firstRow();
$tempNama_getNama= $rekanan->getField("NAMA");
$reqStatusValidasi = $rekanan->getField("STATUS_VALIDASI");
$reqUserStatus = $rekanan->getField("USER_STATUS");

$allRecord_SPT = $rekanan_spt->getCountByParams(array("TIPE"=>1, "REKANAN_ID"=>$reqId));
$rekanan_spt->selectByParams(array("TIPE"=>1, "REKANAN_ID"=>$reqId), -1, -1, "", " ORDER BY TAHUN ASC ");

if($reqPaketId == "")
{}
else
{
	$paketInfo->getPaket($reqPaketId);
	$info = "SPT Tahunan yang harus dipenuhi adalah tahun : ".$paketInfo->syarat_keuangan_spt_tahun;
}

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
  </head>

<body>

    <div class="card mb-1">
        <div class="card-content">
          <div class="p-1">
            <div class="alert bg-primary alert-icon-left alert-arrow-left alert-dismissible mb-2" role="alert">
              <span class="alert-icon"><i class="fa fa-th"></i></span>  <strong>SPT Tahunan</strong>
            </div>
            <p class="alert alert-primary" style="background-color: transparent !important;"><b><?= $tempNama_getNama ?></b></p>
            <?php
            if($info == "")
            {}
            else
            {
            ?>
            <div class="informasi">
                <ul>
                    <li><?=$info?></li>
                </ul>
            </div>
            <?php
            }
            ?>
            <div class="table-responsive">
              <table class="table table-bordered table-hover">
                 <tbody>
                    <tr>
                      <th>Tahun</th>
                      <th>Nomor Tanda Terima Elektronik</th>
                      <th>Tanggal Penyampaian</th>
                      <th width="10px">File SPT</th>
                    </tr>
                    <?php if($allRecord_SPT > 0){
                        $i = 0;
                        while($rekanan_spt->nextRow()){
                    ?>
                    <tr>
                        <td><?=$rekanan_spt->getField("TAHUN")?></td>
                        <td><?=$rekanan_spt->getField("NOMOR")?></td>
                        <td><?=getFormattedDateJson($rekanan_spt->getField("TANGGAL"))?></td>
                        <td><a href="<?= base_url('uploads/spt').'/'.$rekanan_spt->getField("PATH_FILE") ?>" target="_blank" class="badge badge-primary"><span class="fa fa-download"></span> Download</a></td>
                    </tr>
                    <?php $i++;}}else{
                    ?>
                    <tr>
                        <td colspan="4">.: <?=translate("data belum ada", "no data found")?> :.</td>
                    </tr>
                    <?php }?>
                  </tbody>
              </table>
              <?php
                 $kodeKoreksi = $reqKeuangan;
                 if($reqMode == 'koreksi')
                 {
                     $this->load->model("PaketRekananDaftar");
                     $paket_rekanan_daftar = new PaketRekananDaftar();

                     if($reqSubmit == "Submit")
                     {
                         $paket_rekanan_daftar->setField("PAKET_ID", $reqPaketId);
                         $paket_rekanan_daftar->setField("REKANAN_ID", $reqId);
                         $paket_rekanan_daftar->setField("KODE", $kodeKoreksi);
                         $paket_rekanan_daftar->setField("CATATAN", $reqKoreksi);
                         $paket_rekanan_daftar->setField("CREATED_BY", $this->USER_LOGIN_ID);
                         $paket_rekanan_daftar->delete();
                         $paket_rekanan_daftar->insert();
                     }

                     ?>
                     <form name="frm" method="post">
                     <fieldset style="margin-top:10px;">
                      <hr> Koreksi
                      <table class="" cellspacing="1" width="100%">
                        <tr>
                            <td>
                            <textarea class="form-control" name="reqKoreksi" style="width:99%"><?=$paket_rekanan_daftar->getCatatan($reqPaketId, $reqId, $kodeKoreksi)?></textarea>
                            <input type="hidden" name="reqPaketId" value="<?=$reqPaketId?>" />
                            <input type="hidden" name="reqId" value="<?=$reqId?>" />
                            <input type="hidden" name="reqMode" value="<?=$reqMode?>" />
                            <input type="hidden" name="reqKeuangan" value="<?=$reqKeuangan?>" />
                            <br>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <input type="submit" name="reqSubmit" class="btn btn-primary" value="Submit" />
                            </td>
                        </tr>
                    </table>
                    </fieldset>
                    </form>
                 <?php
                 }
                 ?>
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
	                if ($cekData->getField("spt_tahunan") == '1') {
	                  $checked = 'checked';
	                }
	                echo '<input class="mb-1" type="checkbox" name="checkspt_tahunan" id="checkjenis" onclick="return updateChecklist(\''.$reqId.'\',\'spt_tahunan\')" style="cursor:pointer" '.$checked.'> Ya, Lengkap ';
	                ?>
	                <input type="" class="form-control easyui-validatebox span2" required maxlength="255" style="height: 30px !important;" name="" placeholder="catatan" id="catatanjenis" value="<?=$cekData->getField("spt_tahunan_note")?>" onChange="return updateChecklist('<?= $reqId ?>','spt_tahunan')">
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
