<?php
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 *
 */

/* INCLUDE FILE */
$this->load->library("kauth");  $userLogin = new kauth();
$this->load->model("RekananPengurus");
$this->load->model(array("Rekanan","Negara"));
include_once("functions/string.func.php");
include_once("functions/date.func.php");
include_once("functions/default.func.php");

/* create objects */
$rekanan_komisaris = new RekananPengurus(); // tipe 0
$rekanan_direksi = new RekananPengurus(); // tipe 1
$rekanan_get_nama = new Rekanan();

$FILE_DIR = "uploads/pemimpin_perusahaan/";

/* VARIABLE */
$reqId = $this->input->get("reqId");


$rcBright = "table_list_bright";
$rcDark = "table_list_dark";
$rcI = 0;
$rcI1 = 0;

$allRecord_K = $rekanan_komisaris->getCountByParams(array("TIPE"=>1,"REKANAN_ID" => $reqId));
$rekanan_komisaris->selectByParams(array("TIPE"=>1,"REKANAN_ID" => $reqId), -1, -1);

$allRecord_D = $rekanan_direksi->getCountByParams(array("TIPE"=>2,"REKANAN_ID" => $reqId));
$rekanan_direksi->selectByParams(array("TIPE"=>2,"REKANAN_ID" => $reqId), -1, -1);

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
    <style type="text/css">
      body { background: #fff !important; } 
      .table-responsive { overflow-x: scroll !important; }
      .table-responsive::-webkit-scrollbar { -webkit-appearance: none; height: 7px;        /* tinggi scrollbar horizontal */ }
      .table-responsive::-webkit-scrollbar-thumb { border-radius: 6px; background-color: rgba(0,0,0,0.3);  /* warna thumb */ }
      .table-responsive::-webkit-scrollbar-track { background-color: #f1f1f1;  /* warna track */ }
    </style>
  </head>

<body class="body-popup">
    <div class="card mb-1">
      <div class="card-content">
        <div class="p-1">
            <div class="alert bg-primary alert-icon-left alert-arrow-left alert-dismissible mb-2" role="alert">
                <span class="alert-icon"><i class="fa fa-th"></i></span>  <strong>Komisaris</strong>
            </div>
            <p class="alert alert-primary" style="background-color: transparent !important;"><b><?= $tempNama_getNama ?></b></p>
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <tr class="judul-kolom">
                        <th>No.</th>
                        <th>Nama</th>
                        <th>KTP/Passport/KITAS</th>
                        <th>Nomor NPWP</th>
                        <th>Jabatan dalam Perusahaan</th>
                        <th>Kewarganegaraan</th>
                        <th>Negara</th>
                        <th>Alamat KTP/Passport/KITAS</th>
                        <th>Domisili</th>
                        <th width="10%">File KTP/Identitas</th>
                        <th width="10%">File NPWP</th>
                    </tr>
                    <?php if($allRecord_K > 0){
                        while($rekanan_komisaris->nextRow()){
                    ?>
                    <tr>
                        <td><?=$rcI+1?></td>
                        <td>
                          <?=$rekanan_komisaris->getField("NAMA")?><br>
                          <?php
                          if ($rekanan_komisaris->getField("JENIS_KELAMIN") == 'L') {
                            echo '<span class="badge badge-danger btn-xs"><b>Laki-Laki</b></span>';
                          } else {
                            echo '<span class="badge badge-primary btn-xs"><b>Perempuan</b></span>';
                          }
                         ?>
                        </td>
                        <td> <?=$rekanan_komisaris->getField("KTP")?></td>
                        <td><?=$rekanan_komisaris->getField("NPWP")?></td>
                        <td><?=$rekanan_komisaris->getField("JABATAN")?></td>
                        <td><?=$rekanan_komisaris->getField("KEWARGANEGARAAN")?></td>
                        <td>
                          <?php
                          if ($rekanan_komisaris->getField("KEWARGANEGARAAN") == 'Asing') {
                                $negara = new negara();
                                $negara->selectByParams(array("NAMA" => $rekanan_komisaris->getField("NEGARA")), -1, -1);
                                $negara->firstRow();
                                echo $negara->getField("NAMA");
                          }
                          ?>
                        </td>
                        <td><?=$rekanan_komisaris->getField("ALAMAT_KTP")?></td>
                        <td><?=$rekanan_komisaris->getField("DOMISILI")?></td>
                        <td class="text-center">
                            <?php
                              if ($rekanan_komisaris->getField("PATH_FILE") != '') {
                             ?>
                            <a href="<?= base_url('uploads/pengurus').'/'.$rekanan_komisaris->getField("PATH_FILE") ?>" target="_blank" class="badge badge-primary"><span class="fa fa-download"></span> Download</a>
                            <?php
                            } else {
                                echo "-";
                            } ?>
                        </td>
                        <td class="text-center">
                            <?php
                              if ($rekanan_komisaris->getField("PATH_FILE2") != '') {
                             ?>
                            <a href="<?= base_url('uploads/pengurus').'/'.$rekanan_komisaris->getField("PATH_FILE2") ?>" target="_blank" class="badge badge-primary"><span class="fa fa-download"></span> Download</a>
                            <?php
                            } else {
                                echo "-";
                            } ?>
                        </td>
                    </tr>
                    <?php $rcI++;}}else{?>
                    <tr >
                        <td colspan="5">.: data belum ada :.</td>
                    </tr>
                    <?php }?>
                </table>
            </div>
            <div class="alert bg-primary alert-icon-left alert-arrow-left alert-dismissible mb-2" role="alert">
                <span class="alert-icon"><i class="fa fa-th"></i></span>  <strong>Direksi</strong>
            </div>
            <p class="alert alert-primary" style="background-color: transparent !important;"><b><?= $tempNama_getNama ?></b></p>
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <tr class="judul-kolom">
                        <th>No.</th>
                        <th>Nama</th>
                        <th>KTP/Passport/KITAS</th>
                        <th>Nomor NPWP</th>
                        <th>Jabatan dalam Perusahaan</th>
                        <th>Kewarganegaraan</th>
                        <th>Negara</th>
                        <th>Alamat KTP/Passport/KITAS</th>
                        <th>Domisili</th>
                        <th>No. HP</th>
                        <th width="10%">File KTP atau Identitas</th>
                        <th width="10%">File NPWP</th>
                    </tr>
                    <?php if($allRecord_D > 0){
                        while($rekanan_direksi->nextRow()){
                    ?>
                    <tr>
                        <td><?=$rcI1+1?></td>
                        <td>
                          <?=$rekanan_direksi->getField("NAMA")?><br>
                          <?php
                          if ($rekanan_direksi->getField("JENIS_KELAMIN") == 'L') {
                            echo '<span class="badge badge-danger btn-xs"><b>Laki-Laki</b></span>';
                          } else {
                            echo '<span class="badge badge-primary btn-xs"><b>Perempuan</b></span>';
                          }
                         ?>
                        </td>
                        <td> <?=$rekanan_direksi->getField("KTP")?></td>
                        <td><?=$rekanan_direksi->getField("NPWP")?></td>
                        <td><?=$rekanan_direksi->getField("JABATAN")?></td>
                        <td><?=$rekanan_direksi->getField("KEWARGANEGARAAN")?></td>
                        <td>
                          <?php
                          if ($rekanan_direksi->getField("KEWARGANEGARAAN") == 'Asing') {
                                $negara = new negara();
                                $negara->selectByParams(array("NAMA" => $rekanan_direksi->getField("NEGARA")), -1, -1);
                                $negara->firstRow();
                                echo $negara->getField("NAMA");
                          }
                          // if (is_numeric($rekanan_komisaris->getField("NEGARA"))) {
                          //   $negara = new negara();
                          //   $negara->selectByParams(array("ID" => $rekanan_direksi->getField("NEGARA")), -1, -1);
                          //   $negara->firstRow();
                          //   echo $negara->getField("NAMA");
                          // } else {
                          //   echo $rekanan_komisaris->getField("NEGARA");
                          // }
                          ?>
                        </td>
                        <td><?=$rekanan_direksi->getField("ALAMAT_KTP")?></td>
                        <td><?=$rekanan_direksi->getField("DOMISILI")?></td>
                        <td><?=$rekanan_direksi->getField("NOMOR_HP_DIREKTUR")?></td>
                        <td>
                            <?php
                            if ($rekanan_direksi->getField("PATH_FILE") != '') {
                             ?>
                                <a href="<?= base_url('uploads/pengurus').'/'.$rekanan_direksi->getField("PATH_FILE") ?>" target="_blank" class="badge badge-primary"><span class="fa fa-download"></span> Download</a>
                            <?php
                            } else {
                                echo "-";
                            } ?>
                        </td>
                        <td>
                            <?php
                            if ($rekanan_direksi->getField("PATH_FILE2") != '') {
                             ?>
                                <a href="<?= base_url('uploads/pengurus').'/'.$rekanan_direksi->getField("PATH_FILE2") ?>" target="_blank" class="badge badge-primary"><span class="fa fa-download"></span> Download</a>
                            <?php
                            } else {
                                echo "-";
                            } ?>
                        </td>
                    </tr>
                    <?php $rcI1++;}}else{?>
                    <tr>
                        <td colspan="5">.: data belum ada :.</td>
                    </tr>
                    <?php }?>
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
                  if ($cekData->getField("pengurus") == '1') {
                    $checked = 'checked';
                  }
                  echo '<input class="mb-1" type="checkbox" name="checkpengurus" id="checkjenis" onclick="return updateChecklist(\''.$reqId.'\',\'pengurus\')" style="cursor:pointer" '.$checked.'> Ya, Lengkap ';
                  ?>
                  <input type="" class="form-control easyui-validatebox span2" required maxlength="255" style="height: 30px !important;" name="" placeholder="catatan" id="catatanjenis" value="<?=$cekData->getField("pengurus_note")?>" onChange="return updateChecklist('<?= $reqId ?>','pengurus')">
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
    <!-- EASYUI -->
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
