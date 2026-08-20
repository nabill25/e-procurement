<?php
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 * 
 */

$this->load->library("paketinfo"); $paketInfo = new paketinfo();
$this->load->library('zip');
include_once("functions/string.func.php");
include_once("functions/default.func.php");
include_once("functions/date.func.php");
$this->load->model("PaketEvaluasiAdminTawar");
$this->load->model("PaketEvaluasiTeknisTawar");
$this->load->model("PaketEvaluasiHargaTawar");
$this->load->model("PaketEvaluasiKualifikasi");
$this->load->model("PaketRekanan");
$this->load->model("PaketDokumen");

$paket_evaluasi_admin = new PaketEvaluasiAdminTawar();
$paket_evaluasi_teknis = new PaketEvaluasiTeknisTawar();
$paket_evaluasi_harga = new PaketEvaluasiHargaTawar();
$paket_evaluasi_kualifikasi = new PaketEvaluasiKualifikasi();
$paket_rekanan = new PaketRekanan();

$reqId = $this->input->get("reqId");
$reqRekanan = $this->input->get("rekanan");
$reqFile = $this->input->get("file");
$reqTahap = $this->input->get("tahap");


$paketInfo->getPaket($reqId);
$reqNamaPaket = $paketInfo->nama;
$reqSistemSampul = $paketInfo->sistem_sampul;
$reqMetodeEvaluasiId = $paketInfo->metode_lelang_id;

$paket_evaluasi_admin->selectByParams(array("PAKET_ID" => $reqId));
$paket_evaluasi_teknis->selectByParams(array("PAKET_ID" => $reqId));
$paket_evaluasi_harga->selectByParams(array("PAKET_ID" => $reqId));
$paket_evaluasi_kualifikasi->selectByParams(array("PAKET_ID" => $reqId));

if ($reqTahap == 'kualifikasi') { 
  // Lulus Pendaftaran Saat Daftar 0:gagal, 2:Proses, 1:lulus
  // $paket_rekanan->selectByParams(array("PAKET_ID" => $reqId, "A.REKANAN_ID" => $reqRekanan), -1, -1, " AND A.TANGGAL_DAFTAR IS NOT NULL AND A.LULUS_PENDAFTARAN = 2 ");
  $paket_rekanan->selectByParams(array("PAKET_ID" => $reqId, "A.REKANAN_ID" => $reqRekanan), -1, -1, " AND A.TANGGAL_DAFTAR IS NOT NULL ");
  while($paket_rekanan->nextRow())
  {
    $arrRekanan[] = $paket_rekanan->getField("REKANAN");
    $arrRekananId[] = $paket_rekanan->getField("REKANAN_ID");
    $arrPaketRekananId[] = $paket_rekanan->getField("PAKET_REKANAN_ID");
    $arrPaketRekananNilai[] = $paket_rekanan->getField("NILAI_PENAWARAN");
    $arrPaketRekananNilaiSebelumnya[] = $paket_rekanan->getField("NILAI_PENAWARAN_SEBELUMNYA");
    $arrRekananHadirPembukaan[] = $paket_rekanan->getField("HADIR_PEMBUKAAN_PENAWARAN");
    $arrPasswordDokumen[] = $paket_rekanan->getField("KIRIM_PENAWARAN_PASSWORD");
    $arrPasswordDokumen2[] = $paket_rekanan->getField("KIRIM_PENAWARAN_PASSWORD2");
  }

  if (is_array($arrRekanan)) {
    $arrRekanan = $arrRekanan;
    $arrRekananId = $arrRekananId;
    $arrPaketRekananId = $arrPaketRekananId;
    $arrPaketRekananNilai = $arrPaketRekananNilai;
    $arrPaketRekananNilaiSebelumnya = $arrPaketRekananNilaiSebelumnya;
    $arrRekananHadirPembukaan = $arrRekananHadirPembukaan;
    $arrPasswordDokumen = $arrPasswordDokumen;
    $arrPasswordDokumen2 = $arrPasswordDokumen2;
  } else {
    $arrRekanan = array();
    $arrRekananId = array();
    $arrPaketRekananId = array();
    $arrPaketRekananNilai = array();
    $arrPaketRekananNilaiSebelumnya = array();
    $arrRekananHadirPembukaan = array();
    $arrPasswordDokumen = array(); 
    $arrPasswordDokumen2 = array(); 
  }

} 
else 
{

  $paket_rekanan->selectByParams(array("PAKET_ID" => $reqId, "A.REKANAN_ID" => $reqRekanan), -1, -1, " AND A.TANGGAL_DAFTAR IS NOT NULL AND A.LULUS_PENDAFTARAN = 1 AND A.LULUS_KUALIFIKASI = 1 AND A.KIRIM_PENAWARAN = 1 ");
  while($paket_rekanan->nextRow())
  {
  	$arrRekanan[] = $paket_rekanan->getField("REKANAN");
  	$arrRekananId[] = $paket_rekanan->getField("REKANAN_ID");
  	$arrPaketRekananId[] = $paket_rekanan->getField("PAKET_REKANAN_ID");
  	$arrPaketRekananNilai[] = $paket_rekanan->getField("NILAI_PENAWARAN");
  	$arrPaketRekananNilaiSebelumnya[] = $paket_rekanan->getField("NILAI_PENAWARAN_SEBELUMNYA");
  	$arrRekananHadirPembukaan[] = $paket_rekanan->getField("HADIR_PEMBUKAAN_PENAWARAN");
    $arrPasswordDokumen[] = $paket_rekanan->getField("KIRIM_PENAWARAN_PASSWORD");
  	$arrPasswordDokumen2[] = $paket_rekanan->getField("KIRIM_PENAWARAN_PASSWORD2");
  }

  if (is_array($arrRekanan)) {
  	$arrRekanan = $arrRekanan;
  	$arrRekananId = $arrRekananId;
  	$arrPaketRekananId = $arrPaketRekananId;
  	$arrPaketRekananNilai = $arrPaketRekananNilai;
  	$arrPaketRekananNilaiSebelumnya = $arrPaketRekananNilaiSebelumnya;
  	$arrRekananHadirPembukaan = $arrRekananHadirPembukaan;
    $arrPasswordDokumen = $arrPasswordDokumen;
  	$arrPasswordDokumen2 = $arrPasswordDokumen2;
  } else {
  	$arrRekanan = array();
  	$arrRekananId = array();
  	$arrPaketRekananId = array();
  	$arrPaketRekananNilai = array();
  	$arrPaketRekananNilaiSebelumnya = array();
  	$arrRekananHadirPembukaan = array();
    $arrPasswordDokumen = array(); 
  	$arrPasswordDokumen2 = array(); 
  }
}
// if($reqSistemSampul == "2")
// 	exit;
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
    <link rel="stylesheet" type="text/css" href="<?=base_url() ?>assets/new/vendors/css/ui/jquery-ui.min.css">
    <link rel="stylesheet" type="text/css" href="<?=base_url() ?>assets/new/css/plugins/ui/jqueryui.css">
    <script src="<?=base_url()?>assets/new/js/core/libraries/jquery_ui/jquery-ui.min.js"></script>
    <script src="<?=base_url()?>assets/new/js/scripts/ui/jquery-ui/navigations.js"></script>
    <style type="text/css">
      ul.menu-icons li {list-style-type:none;}
      ul { padding-left: 2px; }
    </style>
    
  </head>
  <script type="text/javascript">
    function myFunction(a) {
      var id = "myPass"+a;
      var copyText = document.getElementById(id);
      copyText.select();
      copyText.setSelectionRange(0, 99999)
      document.execCommand("copy");
      // alert("Copied the text: " + copyText.value);
      alert("Password disalin "+copyText.value);
    }
  </script>

<style type="text/css">
  table th {
    background-color: #967adc;
    color: #fff;
  }
</style>
<!-- <body class="body-popup"> -->
<body>
  <div class="card border-darken-1">
    <div class="card-content">
      <div class="p-1">  
        <div class="row"> 
          <div class="col-md-12 col-sm-12">
            <table class="table table-bordered table-responsive">
              <tr>
                <th width="1%" class="text-center">No.</th>
                <th style="width: 28%">Uraian</th>
                <?php
                for($i=0;$i<count($arrRekanan);$i++)
                {
                ?>
                  <th class="alert" style="font-size: 15px; font-weight: bold; text-align: center"><?=$arrRekanan[$i]?></th>
                <?php
                }
                ?>
                <tr class="gelap">
                  <?php 
                  if ($reqTahap != 'kualifikasi') {
                   ?>
                  <td colspan="2"><b>Password</b></td>
                  <?php
                    for($i=0;$i<count($arrRekanan);$i++)
                    {
                    ?>
                    <td align="center" style="text-align: center" width="20%">
                      <?php
                      if($reqSistemSampul == "1") {
                        if ($arrPasswordDokumen[$i]) {
                          echo  '<a onClick="return myFunction(\''.$arrRekanan[$i].'\')">
                                <div class="input-group" style="margin-top:1%">
                                  <div class="input-group-prepend">
                                    <i class="fa fa-copy"></i> &nbsp;&nbsp;
                                  </div>
                                  <input class="form-control" type="text" value="'.$arrPasswordDokumen[$i].'" id="myPass'.$arrRekanan[$i].'" style="border:none; height:10px; cursor:copy;" readonly>
                                </div>
                                </a>';
                        } else {
                          echo "Enkripsi Penawaran tidak di upload";
                        }
                      } else { // 2 File
                        if ($reqTahap == 'admin' || $reqTahap == 'teknis') {
                          if ($arrPasswordDokumen[$i]) {
                            echo  '<a onClick="return myFunction(\''.$arrRekanan[$i].'\')">
                                  <div class="input-group" style="margin-top:1%">
                                    <div class="input-group-prepend">
                                      <i class="fa fa-copy"></i> &nbsp;&nbsp;
                                    </div>
                                    <input class="form-control" type="text" value="'.$arrPasswordDokumen[$i].'" id="myPass'.$arrRekanan[$i].'" style="border:none; height:10px; cursor:copy;" readonly>
                                  </div>
                                  </a>';
                          } else {
                            echo "Enkripsi Penawaran tidak di upload";
                          }
                        } else { // Harga
                          if ($arrPasswordDokumen2[$i]) {
                            echo  '<a onClick="return myFunction(\''.$arrRekanan[$i].'\')">
                                  <div class="input-group" style="margin-top:1%">
                                    <div class="input-group-prepend">
                                      <i class="fa fa-copy"></i> &nbsp;&nbsp;
                                    </div>
                                    <input class="form-control" type="text" value="'.$arrPasswordDokumen2[$i].'" id="myPass'.$arrRekanan[$i].'" style="border:none; height:10px; cursor:copy;" readonly>
                                  </div>
                                  </a>';
                          } else {
                            echo "Enkripsi Penawaran tidak di upload";
                          }
                        }
                      }
                      ?>
                    </td>
                    <?php
                    }
                  }
                 ?>
                </tr>
                
                <?php 
                $pathDok = 'uploads/penawaran/';
                if ($reqTahap == 'admin') { ?>
                  <!-- DATA ADMINISTRASI  -->
                  <tr class="gelap">
                    <td colspan="<?=2+(count($arrRekanan))?>"><strong>DOKUMEN ADMINISTRASI</strong></td>
                  </tr>
                  <?php
                  $i = 1;
                  $check = 0;
                  $style="gelap";

                  //set up 31-10-2012
                  $total_administrasi=$data_administrasi='';

                  while($paket_evaluasi_admin->nextRow())
                  {
                  ?>
                    <tr class="terang">
                      <td class="text-center"><?=$i?>.</div></td>
                      <td style="width: 28%">
                        <?=$paket_evaluasi_admin->getField("NAMA")?> <?php if($paket_evaluasi_admin->getField("WAJIB") == '1'){ ?><font color="#FF0000">* </font><?php } ?>
                      </td>
                      <?php
                      for($j=0;$j<count($arrRekanan);$j++)
                      {
                        //set up 16-10-2012
                        $paket_dokumen = new PaketDokumen();
                        $paket_dokumen->selectByParams(array("PAKET_ID" => $reqId, "REKANAN_USER_ID" => $arrRekananId[$j], "TRIM(NAMA)" => trim($paket_evaluasi_admin->getField("NAMA"))));
                        $paket_dokumen->firstRow();
                        ?>
                        <td align="center">
                          <div class="data-rekanan">
                          <?php
                          if($paket_dokumen->getField("PATH_FILE") == "")
                          {}
                          else
                          {
                            if($info == "0")
                              echo "-";
                            else
                            {
                              $dokAdministrasi[] = $pathDok.$paket_dokumen->getField("PATH_FILE");
                            ?>
                              <a href="uploads/penawaran/<?=$paket_dokumen->getField("PATH_FILE")?>" target="_blank">
                                <?= ICON_DOWNLOAD ?>
                              </a>
                            <?php
                            }
                          }
                          unset($paket_dokumen);
                          ?>

                          </div>
                        </td>
                        <?php
                         $check++;
                      }
                      ?>

                    </tr>
                  <?php
                    $i++;
                  }
                  // echo "<pre>"; print_r($dokAdministrasi);
                  // $this->zip->add_data($dokAdministrasi);
                  // $this->zip->download('my_backup.zip');
                  ?>
                  <a href="<?= base_url('evaluasi_download_json/aanwijzing_publish_json?reqId='.$reqId.'&rekanan='.$reqRekanan.'&file='.$reqFile.'&tahap='.$reqTahap) ?>" class="btn round btn-min-width box-shadow-1 btn-success text-white mr-1 mb-1 pull-right" target="_blank">
                    <?= ICON_DOWNLOAD ?> Download semua dokumen</a>
                <?php 
                } ?>

                <?php 
                if ($reqTahap == 'teknis') { ?>
                  <!-- DATA TEKNIS -->
                  <tr class="gelap">
                    <td colspan="<?=2+(count($arrRekanan))?>"><strong> DOKUMEN TEKNIS</strong></td>
                  </tr>
                  <?php
                  $i = 1;
                  $check = 0;
                  $style="gelap";

                  //set up 31-10-2012
                  $total_teknis=$data_teknis;

                  while($paket_evaluasi_teknis->nextRow())
                  {
                  ?>
                    <tr class="terang">
                      <td align="center"><?=$i?>.</td>
                      <td>
                        <?=$paket_evaluasi_teknis->getField("NAMA")?> <?php if($paket_evaluasi_teknis->getField("WAJIB") == '1'){ ?> <font color="#FF0000">* </font><?php } ?>
                      </td>
                      <?php
                      for($j=0;$j<count($arrRekanan);$j++)
                      {
                        //set up 16-10-2012
                        $paket_dokumen = new PaketDokumen();
                        $paket_dokumen->selectByParams(array("PAKET_ID" => $reqId, "REKANAN_USER_ID" => $arrRekananId[$j], "TRIM(NAMA)" => trim($paket_evaluasi_teknis->getField("NAMA"))));
                        $paket_dokumen->firstRow();
                        ?>
                        <td align="center">
                          <?php
                          if($paket_dokumen->getField("PATH_FILE") == "")
                          {}
                          else
                          {
                            if($info == "0")
                              echo "-";
                            else
                            {
                            ?>
                              <a href="uploads/penawaran/<?=$paket_dokumen->getField("PATH_FILE")?>" target="_blank">
                                <?= ICON_DOWNLOAD ?>
                              </a>
                            <?php
                            }
                          }
                          unset($paket_dokumen);

                          ?>
                        </td>
                        <?php
                         $check++;
                      }
                      ?>
                    </tr>
                  <?php
                    $i++;
                  }
                  ?> 
                  <a href="<?= base_url('evaluasi_download_json/aanwijzing_publish_json?reqId='.$reqId.'&rekanan='.$reqRekanan.'&file='.$reqFile.'&tahap='.$reqTahap) ?>" class="btn round btn-min-width box-shadow-1 btn-success text-white mr-1 mb-1 pull-right" target="_blank"><?= ICON_DOWNLOAD ?> Download semua dokumen</a>
                <?php 
                } ?>

                <?php 
                if ($reqTahap == 'harga') { ?>
                  <!-- DATA HARGA -->
                  <tr class="gelap">
                    <td colspan="<?=2+(count($arrRekanan))?>"><strong> DOKUMEN HARGA</strong></td>
                  </tr>
                  <?php
                  $i = 1;
                  $check = 0;
                  $style="gelap";

                  //set up 31-10-2012
                  $total_harga=$data_harga;

                  while($paket_evaluasi_harga->nextRow())
                  {
                  ?>
                    <tr class="terang">
                      <td align="center"><?=$i?>.</td>
                      <td>
                        <?=$paket_evaluasi_harga->getField("NAMA")?> <?php if($paket_evaluasi_harga->getField("WAJIB") == '1'){ ?> <font color="#FF0000">* </font><?php } ?>
                      </td>
                      <?php
                      for($j=0;$j<count($arrRekanan);$j++)
                      {
                        //set up 16-10-2012
                        $paket_dokumen = new PaketDokumen();
                        $paket_dokumen->selectByParams(array("PAKET_ID" => $reqId, "REKANAN_USER_ID" => $arrRekananId[$j], "TRIM(NAMA)" => trim($paket_evaluasi_harga->getField("NAMA"))));
                        $paket_dokumen->firstRow();
                        ?>
                        <td align="center">
                        <?php
                        if($paket_dokumen->getField("PATH_FILE") == "")
                        {}
                        else
                        {
                          if($info == "0")
                              echo "-";
                          else
                          {
                          ?>
                            <a href="uploads/penawaran/<?=$paket_dokumen->getField("PATH_FILE")?>" target="_blank">
                              <?= ICON_DOWNLOAD ?>
                            </a>
                          <?php
                          }
                        }
                        unset($paket_dokumen);
                        ?>
                        </td>
                        <?php
                         $check++;
                         unset($paket_dokumen);
                      }
                      ?>
                    </tr>
                  <?php
                    $i++;
                  }
                  ?>
                  <a href="<?= base_url('evaluasi_download_json/aanwijzing_publish_json?reqId='.$reqId.'&rekanan='.$reqRekanan.'&file='.$reqFile.'&tahap='.$reqTahap) ?>" class="btn round btn-min-width box-shadow-1 btn-success text-white mr-1 mb-1 pull-right" target="_blank"><?= ICON_DOWNLOAD ?> Download semua dokumen</a>
                <?php 
                } ?>

                <?php 
                if ($reqTahap == 'kualifikasi') {  
                ?>
                  <!-- DATA HARGA -->
                  <tr class="gelap">
                    <td colspan="<?=2+(count($arrRekanan))?>"><strong> DOKUMEN KUALIFIKASI</strong></td>
                  </tr>
                  <?php
                  $i = 1;
                  $check = 0;
                  $style="gelap";

                  //set up 31-10-2012
                  $total_harga=$data_harga;

                  while($paket_evaluasi_kualifikasi->nextRow())
                  {
                  ?>
                    <tr class="terang">
                      <td align="center"><?=$i?>.</td>
                      <td>
                        <?=$paket_evaluasi_kualifikasi->getField("NAMA")?> <?php if($paket_evaluasi_kualifikasi->getField("WAJIB") == '1'){ ?> <font color="#FF0000">* </font><?php } ?>
                      </td>
                      <?php
                      for($j=0;$j<count($arrRekanan);$j++)
                      { 
                        $paket_dokumen = new PaketDokumen();
                        $paket_dokumen->selectByParams(array("PAKET_ID" => $reqId, "REKANAN_USER_ID" => $arrRekananId[$j], "TRIM(NAMA)" => trim($paket_evaluasi_kualifikasi->getField("NAMA"))));
                        $paket_dokumen->firstRow();
                        ?>
                        <td align="center">
                        <?php
                        if($paket_dokumen->getField("PATH_FILE") == "")
                        {}
                        else
                        {
                          if($info == "0")
                              echo "-";
                          else
                          {
                          ?>
                            <a href="uploads/penawaran/<?=$paket_dokumen->getField("PATH_FILE")?>" target="_blank">
                              <?= ICON_DOWNLOAD ?>
                            </a>
                          <?php
                          }
                        }
                        unset($paket_dokumen);
                        ?>
                        </td>
                        <?php
                         $check++;
                         unset($paket_dokumen);
                      }
                      ?>
                    </tr>
                  <?php
                    $i++;
                  }
                  ?>
                  <a href="<?= base_url('evaluasi_download_json/aanwijzing_publish_json?reqId='.$reqId.'&rekanan='.$reqRekanan.'&file='.$reqFile.'&tahap='.$reqTahap) ?>" class="btn round btn-min-width box-shadow-1 btn-success text-white mr-1 mb-1 pull-right" target="_blank"><?= ICON_DOWNLOAD ?> Download semua dokumen</a>
                <?php 
                } ?>

            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
