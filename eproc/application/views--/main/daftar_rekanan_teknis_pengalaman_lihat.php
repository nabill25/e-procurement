<?php
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 *
 */

/* INCLUDE FILE */
$this->load->library("kauth");  $userLogin = new kauth();
$this->load->model("RekananPengalaman");
$this->load->model("Rekanan");
$this->load->model("RekananDaftarPengalaman");
include_once("functions/string.func.php");
include_once("functions/date.func.php");
include_once("functions/default.func.php");

/* create objects */
$rekanan_pengalaman       = new RekananPengalaman(); // tipe 0
$rekanan_pengalaman_progress  = new RekananPengalaman(); // tipe 0
$rekanan_get_nama = new Rekanan();

/* VARIABLE */
$reqId = $this->input->get("reqId");
$reqPaketId =  $this->input->get("reqPaketId");
$reqCari = $this->input->post("reqCari");

$reqSubmit = httpFilterPost("reqSubmit");
$reqCatatan = $_POST["reqCatatan"];
$reqDaftarId = $_POST["reqDaftarId"];
/* VALIDATION */
$FILE_DIR = "uploads/pengalaman/";
// trigger the validation

if($reqSubmit == "Submit")
{
  for($i=0;$i<count($reqDaftarId);$i++)
  {
    $rekanan_daftar_pengalaman = new RekananDaftarPengalaman();
    $rekanan_daftar_pengalaman->setField("CATATAN", $reqCatatan[$i]);
    $rekanan_daftar_pengalaman->setField("REKANAN_DAFTAR_PENGALAMAN_ID", $reqDaftarId[$i]);
    $rekanan_daftar_pengalaman->updateCatatan();
    unset($rekanan_daftar_pengalaman);
  }
}

/* ACTION BY REQMODE */
if($reqPaketId == "")
  $statement = "";
else
  $statement = " AND EXISTS(SELECT 1 FROM REKANAN_DAFTAR_PENGALAMAN X WHERE X.REKANAN_PENGALAMAN_ID = A.REKANAN_PENGALAMAN_ID AND X.PAKET_ID = '".$reqPaketId."') ";

$allRecord_K = $rekanan_pengalaman->getCountByParams(array("REKANAN_ID"=>$reqId),  $statement);
$rekanan_pengalaman->selectByParams(array("REKANAN_ID"=>$reqId), -1, -1, $statement);

$allRecord_progress = $rekanan_pengalaman_progress->getCountByParams(array("REKANAN_ID"=>$reqId),  $statement);
$rekanan_pengalaman_progress->selectByParams(array("REKANAN_ID"=>$reqId), -1, -1, $statement);

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
    <link rel="stylesheet" type="text/css" href="<?=base_url() ?>assets/new/css/app.css">
    <link rel="stylesheet" type="text/css" href="<?=base_url() ?>assets/new/css/core/menu/menu-types/horizontal-menu.css">
    <link rel="stylesheet" type="text/css" href="<?=base_url() ?>assets/new/css/core/colors/palette-gradient.css">
    <link rel="stylesheet" type="text/css" href="<?=base_url() ?>assets/new/css/core/colors/palette-callout.css">
    <link rel="stylesheet" type="text/css" href="<?=base_url() ?>assets/new/css/style.css">
    <script language="JavaScript" src="jslib/elementDis.js"></script>
  </head>

<body>

  <div class="card mb-1">
    <div class="card-content">
      <div class="p-1">
        <div class="alert bg-primary alert-icon-left alert-arrow-left alert-dismissible mb-2" role="alert">
          <span class="alert-icon"><i class="fa fa-th"></i></span>  <strong>PENGALAMAN</strong>
        </div>
        <p class="alert alert-primary" style="background-color: transparent !important;"><b><?= $tempNama_getNama ?></b></p>
        <div class="table-responsive">
          <form action="" name="frmDaftarAlamat" method="post" enctype="multipart/form-data">
            <!-- <div class="alert alert-info">Pekerjaan  </div> -->
            <table width="100%" class="table table-bordered" border="0" cellpadding="2" cellspacing="1" id="tbl_bidang">
              <tbody>
                <tr class="judul-kolom">
                  <th>No</th>
                  <th>Nama Pekerjaan </th>
                  <th>Bidang Pekerjaan</th>
                  <th>Lokasi</th>
                  <th>File Kontrak</th>
                  <th>File BAST</th>
                </tr>
                <?php if($allRecord_K > 0)
                  {
                    $i = 0;
                    while($rekanan_pengalaman->nextRow())
                    {
                  ?>
                      <tr>
                          <td><?=$rcI+1?></td>
                          <td><a class="taut" onclick="displayElement('reqDetil<?=$i?>')" style="cursor:pointer" id="rekanan<?=$i?>">
                            <?php
                            if ($rekanan_pengalaman->getField("KONTRAK_STATUS") == '1') {
                              echo '<span class="badge badge-primary">Selesai</span><br>';
                            } else {
                              echo '<span class="badge badge-danger">Progres '.$rekanan_pengalaman->getField("PROGRESS").'%</span><br>';
                            } ?>
                            <?=$rekanan_pengalaman->getField("NAMA")?> <span class="badge badge-primary"><i class="fa fa-eye"></i> <small>Lihat detil</small></span></a></td>
                          <input type="hidden" id="valTem<?=$i?>">
                          <td><?=$rekanan_pengalaman->getField("PENGALAMAN_BIDANG")?></td>
                          <td><?=$rekanan_pengalaman->getField("LOKASI")?></td>
                          <td class="text-center">
                            <?php
                            if ($rekanan_pengalaman->getField("PATH_FILE")) { ?>
                              <a href="<?=$FILE_DIR.$rekanan_pengalaman->getField("PATH_FILE")?>" class="badge badge-primary" target="_blank"><span class="fa fa-download"></span> Download</a>
                            <?php
                            } ?>
                          </td>
                          <td class="text-center">
                            <?php
                            if ($rekanan_pengalaman->getField("PATH_FILE_BA")) { ?>
                            <a href="<?=$FILE_DIR.$rekanan_pengalaman->getField("PATH_FILE_BA")?>" class="badge badge-primary" target="_blank"><span class="fa fa-download"></span> Download</a>
                            <?php
                          } ?>
                          </td>
                      </tr>
                      <tr id="reqDetil<?=$i?>" style="display:none">
                        <td colspan="6">
                          <table id="keahlian" width="100%" border="0" cellpadding="2" cellspacing="1">
                            <tr class="baris-spek">
                              <td colspan="4">Pemberi Tugas</td>
                            </tr>
                            <tr class="baris-ket">
                              <td colspan="2">Nama</td>
                              <td width="3%">:</td>
                              <td width="76%"><?=$rekanan_pengalaman->getField("PEMBERI_TUGAS")?></td>
                            </tr>
                            <tr class="baris-ket">
                              <td colspan="2">Alamat</td>
                              <td>:</td>
                              <td><?=$rekanan_pengalaman->getField("PEMBERI_TUGAS_ALAMAT")?></td>
                            </tr>
                            <tr>
                              <td colspan="4">&nbsp;</td>
                            </tr>
                            <tr class="baris-spek">
                              <td colspan="4">Kontrak</td>
                            </tr>
                            <tr class="baris-ket">
                              <td colspan="2">No</td>
                              <td>:</td>
                              <td><?=$rekanan_pengalaman->getField("KONTRAK_NOMOR")?></td>
                            </tr>
                            <tr class="baris-ket">
                              <td colspan="2">Tanggal</td>
                              <td>:</td>
                              <td><?=getFormattedDate($rekanan_pengalaman->getField("KONTRAK_TANGGAL"))?></td>
                            </tr>
                            <tr class="baris-ket">
                              <td colspan="2">Nilai</td>
                              <td>:</td>
                              <td><?=currencyToPage($rekanan_pengalaman->getField("KONTRAK_NILAI"))?></td>
                            </tr>
                            <tr>
                              <td colspan="4">&nbsp;</td>
                            </tr> 
                            <tr class="baris-ket">
                              <td colspan="2">Tanggal Selesai</td>
                              <td>:</td>
                              <td><?=getFormattedDate($rekanan_pengalaman->getField("BA_TANGGAL"))?></td>
                            </tr>
                          </table>
                        </td>
                      </tr>
                        <?php
                      $i++;$rcI++;
                    }
                  } else
                  {
                  ?>
                    <tr>
                        <td colspan="6">.: data belum ada :.</td>
                    </tr>
                  <?php
                    }
                  ?>
              </tbody>
            </table>

          </form>
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
              if ($cekData->getField("pengalaman") == '1') {
                $checked = 'checked';
              }
              echo '<input class="mb-1" type="checkbox" name="checkpengalaman" id="checkjenis" onclick="return updateChecklist(\''.$reqId.'\',\'pengalaman\')" style="cursor:pointer" '.$checked.'> Ya, Lengkap ';
              ?>
              <input type="" class="form-control easyui-validatebox span2" required maxlength="255" style="height: 30px !important;" name="" placeholder="catatan" id="catatanjenis" value="<?=$cekData->getField("pengalaman_note")?>" onChange="return updateChecklist('<?= $reqId ?>','pengalaman')">
              <small><sup>*</sup>&nbsp;Tekan enter setalah mengisi catatan</small>
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
