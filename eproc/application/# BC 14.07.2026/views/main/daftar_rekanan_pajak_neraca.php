<?php
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 *
 */

$this->load->library("paketinfo"); $paketInfo = new paketinfo();
$this->load->model("Rekanan");
$this->load->model("RekananSaham");
$this->load->model("RekananPajak");
$this->load->model("RekananNeraca");
include_once("functions/string.func.php");
include_once("functions/date.func.php");
include_once("functions/default.func.php");

$rekanan = new Rekanan();
$rekanan_neraca = new RekananNeraca();

/* VARIABLE */
$reqId = $this->input->get("reqId");
$reqPaketId =  $this->input->get("reqPaketId");
$reqMode = $this->input->get("reqMode");
$reqKeuangan = $this->input->get("reqKeuangan");
$reqKoreksi = $this->input->get("reqKoreksi");
$reqSubmit = $this->input->get("reqSubmit");

$rekanan->selectByParams(array("A.REKANAN_ID"=>$reqId),-1,-1);
$rekanan->firstRow();
$tempNama_getNama= $rekanan->getField("NAMA");
$reqStatusValidasi = $rekanan->getField("STATUS_VALIDASI");
$reqUserStatus = $rekanan->getField("USER_STATUS");

//echo $rekanan_ppn->query;exit;
$tahun= date("Y");
$reqTahunNeraca = $this->input->get("reqTahunNeraca");


if($reqPaketId == "")
{}
else
{
    $paketInfo->getPaket($reqPaketId);
    $info = "Neraca Keuangan yang harus dipenuhi adalah tahun : ".$paketInfo->syarat_neraca_tahun;
    $reqTahunNeraca  = $paketInfo->syarat_neraca_tahun;
}

if($reqTahunNeraca == ""){
    $reqTahunNeraca = $tahun;
}

$rekanan_neraca->selectByParams(array("REKANAN_ID"=>$reqId, "TAHUN"=>$reqTahunNeraca), -1, -1);
$rekanan_neraca->firstRow();
//echo $rekanan_neraca->query;exit;
$reqModalNeraca = numberToIna($rekanan_neraca->getField("MODAL"));
$reqAuditNamaNeraca = $rekanan_neraca->getField("AUDIT_NAMA");
$reqAuditNomorNeraca = $rekanan_neraca->getField("AUDIT_NOMOR");
$reqAuditTanggalNeraca = getFormattedDateJson($rekanan_neraca->getField("AUDIT_TANGGAL"));
$reqAuditKeteranganNeraca = $rekanan_neraca->getField("AUDIT_KESIMPULAN");
$reqLinkFileTemp = $rekanan_neraca->getField("NAMA_FILE");
$tempFileTemp = $rekanan_neraca->getField("PATH_FILE");
$reqLinkFileTemp2 = $rekanan_neraca->getField("NAMA_FILE2");
$tempFileTemp2 = $rekanan_neraca->getField("PATH_FILE2");

$allrecord_neraca_tahun = $rekanan_neraca->getCountByParamsTahun(array("REKANAN_ID"=>$reqId, "TAHUN"=>$reqTahunNeraca));
$rekanan_neraca->selectByParamsTahun(array("REKANAN_ID"=>$reqId, "TAHUN"=>$reqTahunNeraca), -1, -1);

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
          <span class="alert-icon"><i class="fa fa-th"></i></span>  <strong>NERACA </strong>
        </div>
        <p class="alert alert-primary" style="background-color: transparent !important;"><b><?= $tempNama_getNama ?></b></p>
        <div class="table-responsive">
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

                
             <div class="row">
              <div class="col-md-2"> 
                <select name="reqTahunNeraca" id="reqTahunNeraca" class="form-control mb-1" onChange="document.location.href='main/loadUrl/main/daftar_rekanan_pajak_neraca/?reqPaketId=<?=$reqPaketId?>&reqId=<?=$reqId?>&reqTahunNeraca='+this.value" >
                    <?php
                    for($i=date('Y')-5;$i<=date('Y')+1; $i++)
                    {
                    ?>
                      <option value="<?=$i?>" <?php if($i == $reqTahunNeraca) { ?> selected="selected" <?php } ?>><?=$i?></option>
                    <?php
                    }
                    ?>
                </select>
              </div>
            </div>

            <div class="alert alert-info">NERACA</div>

            <table class="table table-bordered table-hover">
                <tr>
                    <td width="20%">Modal (kekayaan bersih):</td>
                    <td><?=$reqModalNeraca?> </td>
                </tr>
            </table>

            <div class="alert alert-info">AUDIT</div>

            <table class="table table-bordered table-hover">
                <tr>
                    <td width="20%">KAP</td>
                    <td><?=$reqAuditNamaNeraca?> </td>
                </tr>
                <tr>
                    <td>Nomor</td>
                    <td><?=$reqAuditNomorNeraca?> </td>
                </tr>
                <tr>
                    <td>Tanggal</td>
                    <td><?=$reqAuditTanggalNeraca?> </td>
                </tr>
                <tr>
                    <td>Kesimpulan</td>
                    <td><?=$reqAuditKeteranganNeraca?> </td>
                </tr>
                <tr>
                    <td>File Neraca / K A P</td>
                    <td>
                        <?php
                        if($tempFileTemp == "")
                        {}
                        else
                        {
                        ?>
                            <a href="uploads/neraca_keuangan/<?=str_replace("'", "''", $tempFileTemp)?>" class="badge badge-primary" target="_blank"><span class="fa fa-download"></span> Download</a>
                        <?php
                        }
                        ?>
                    </td>
                </tr>
                <tr>
                    <td>File Laba Rugi</td>
                    <td>
                        <?php
                        if($tempFileTemp2 == "")
                        {}
                        else
                        {
                        ?>
                            <a href="uploads/neraca_keuangan/<?=str_replace("'", "''", $tempFileTemp2)?>" target="_blank" class="badge badge-primary"><span class="fa fa-download"></span> Download</a>
                        <?php
                        }
                        ?>
                    </td>
                </tr>
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
                     $paket_rekanan_daftar->setField("CREATED_BY", $userLogin->UID);
                     $paket_rekanan_daftar->delete();
                     $paket_rekanan_daftar->insert();
                 }

                 ?>
                 <form name="frm" method="post">
                 <fieldset style="margin-top:10px;">
                  <table width="100%">
                    <tr>
                        <td>
                        Koreksi
                        <textarea name="reqKoreksi" class="form-control" style="width:99%"><?=$paket_rekanan_daftar->getCatatan($reqPaketId, $reqId, $kodeKoreksi)?></textarea>
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
                        if ($cekData->getField("neraca") == '1') {
                            $checked = 'checked';
                        }
                        echo '<input class="mb-1" type="checkbox" name="checkneraca" id="checkjenis" onclick="return updateChecklist(\''.$reqId.'\',\'neraca\')" style="cursor:pointer" '.$checked.'> Ya, Lengkap ';
                        ?>
                        <input type="" class="form-control easyui-validatebox span2" required maxlength="255" style="height: 30px !important;" name="" placeholder="catatan" id="catatanjenis" value="<?=$cekData->getField("neraca_note")?>" onChange="return updateChecklist('<?= $reqId ?>','neraca')">
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
