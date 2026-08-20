<?php
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 * 
 */
if($this->USER_TYPE_ID == "")
    redirect("main");

include_once("functions/string.func.php");
include_once("functions/default.func.php");
include_once("functions/encrypt2.func.php");
include_once("functions/date.func.php");
$this->load->library("paketinfo"); $paketInfo = new paketinfo();
$this->load->model("PaketEvaluasiAdminTawar");
$this->load->model("PaketEvaluasiTeknisTawar");
$this->load->model("PaketEvaluasiHargaTawar");
$this->load->model("PaketRekanan");
$this->load->model("PaketDokumen");
$this->load->model("RekananEvaluasiAdminTawar");
$this->load->model("RekananEvaluasiTeknisTawar");
$this->load->model("RekananEvaluasiHargaTawar");
$this->load->model("Paket");
$this->load->model("PaketTahap");

$paket_evaluasi_admin = new PaketEvaluasiAdminTawar();
$paket_evaluasi_teknis = new PaketEvaluasiTeknisTawar();
$paket_evaluasi_harga = new PaketEvaluasiHargaTawar();
$paket_rekanan = new PaketRekanan();
$paket_nilai = new Paket();
$paket_nilai_estimate = new Paket();
$paket_tahap_metode = new PaketTahap();
$paket_tahap = new PaketTahap();

$reqId = httpFilterRequest("reqId");
$reqNilaiEstimate = httpFilterPost("reqNilaiEstimate");
$reqDataPenawaranHarga = $_POST["reqDataPenawaranHarga"];
$reqRekananIdArray =unserialize(stripslashes($_POST['reqRekananIdArray']));
$submitSimpan = httpFilterPost("submitSimpan");

$paket_rekanan_check = new PaketRekanan();
$reqCheckPaketRekananId = $paket_rekanan_check->getPaketRekananId($reqId, $this->ID);
if($reqCheckPaketRekananId == "")
	exit;

$paketInfo->getPaket($reqId);
$reqNamaPaket = $paketInfo->nama;
$reqSistemSampul = $paketInfo->sistem_sampul;
$reqUUID = $paketInfo->uuid;

if($reqSistemSampul == "1")
	exit;

$paket_rekanan->selectByParamsPaketLelang(array("PAKET_ID" => $reqId, "REKANAN_ID" => $this->ID), -1, -1, " AND TANGGAL_DAFTAR IS NOT NULL AND A.LULUS_PENDAFTARAN = 1 AND A.LULUS_KUALIFIKASI = 1 ");
$paket_rekanan->firstRow();
if($paket_rekanan->getField("PAKET_REKANAN_ID") == "")
	exit;

/* CEK APAKAH SUDAH MEMASUKKAN DOKUMEN PENAWARAN */
$paket_dokumen_validasi = new PaketDokumen();
$sudahUpload = $paket_dokumen_validasi->getCountByParams(array("PAKET_ID" => $reqId, "REKANAN_USER_ID" => $this->ID), " AND JENIS_DOKUMEN LIKE 'PENAWARAN%' ");
if($sudahUpload <= 0)
	exit;

if($paketInfo->publish_ba_penawaran == "")
	exit;

$paket_rekanan->selectByParams(array("PAKET_ID" => $reqId), -1, -1, " AND A.TANGGAL_DAFTAR IS NOT NULL AND A.LULUS_PENDAFTARAN = 1 AND A.LULUS_KUALIFIKASI = 1 AND A.KIRIM_PENAWARAN = 1 AND A.REKANAN_ID = '".$this->REKANAN_ID."'");
while($paket_rekanan->nextRow())
{
	$arrRekanan[] = $paket_rekanan->getField("REKANAN");
	$arrRekananId[] = $paket_rekanan->getField("REKANAN_ID");
	$arrPaketRekananId[] = $paket_rekanan->getField("PAKET_REKANAN_ID");
	$arrPaketRekananNilai[] = $paket_rekanan->getField("NILAI_PENAWARAN");
	$arrRekananHadirPembukaan[] = $paket_rekanan->getField("HADIR_PEMBUKAAN_PENAWARAN");
	$arrPasswordDokumen[] = $paket_rekanan->getField("KIRIM_PENAWARAN_PASSWORD");
	$arrKelengkapan[] = $paket_rekanan->getField("KIRIM_PENAWARAN_LENGKAP");
	$arrKelengkapanAlasan[] = $paket_rekanan->getField("KIRIM_PENAWARAN_ALASAN");
}

$paket_nilai->selectByParams(array("PAKET_ID" => $reqId));
$paket_nilai->firstRow();
$reqNilaiEstimate = $paket_nilai->getField("NILAI_OWNER_ESTIMATE");

$paket_evaluasi_admin->selectByParams(array("PAKET_ID" => $reqId));
$paket_evaluasi_teknis->selectByParams(array("PAKET_ID" => $reqId));
$paket_evaluasi_harga->selectByParams(array("PAKET_ID" => $reqId));
?>

<style type="text/css">
  table th {
    background-color: #967adc;
    color: #fff;
  }
</style>

<div class="row"> 
  <div class="col-md-12 col-sm-12">
    <div class="card">
      <div class="card-header card-head-inverse bg-primary">
        <h4 class="card-title text-white">Pembukaan Penawaran File 1</h4>
        <a class="heading-elements-toggle"><i class="fa fa-ellipsis-v font-medium-3"></i></a> 
        <div class="heading-elements">
            <ul class="list-inline mb-0">
              <li><a data-action="expand"><i class="ft-maximize"></i></a></li>
              <!-- <li><a data-action="close"><i class="ft-x"></i></a></li> -->
            </ul>
        </div>
      </div>
      <div class="card-content collapse show border-info border-darken-2">
        <div class="card-body area-datatable"> 
          <div class="table-responsive">  
            <!-- <div class="alert alert-info">
              Status Pembukaan Penawaran Sampul 1 Anda : <br> -->
                <?php

                // for($j=0;$j<count($arrRekanan);$j++)
                // {
                //   if($arrRekananId[$j] == $this->ID)
                //   {
                //     $statusUserLogin =  $arrKelengkapan[$j];
                //     $statusUserLoginAlasan =  $arrKelengkapanAlasan[$j];
                //   }
                // }
                // if($statusUserLogin == "0")
                // {
                // ?>
                 <!-- <strong><u>Anda tidak dapat melanjutkan proses selanjutnya -->
                <?php
                // if($statusUserLoginAlasan == "")
                // {}
                // else
                //     echo "Karena, ".strtoupper($statusUserLoginAlasan);
                ?>
                <!-- </u></strong> -->
                <?php
                // }
                // else
                // {
                ?>
                    <!-- <strong><u>DOKUMEN PENAWARAN ANDA LENGKAP</u></strong> -->
                <?php
                // }
                ?>
            <!-- </div> -->

            <table class="table table-bordered table-hover">
              <tr class="judul-kolom">
                <th style="width: 2%">No.</th>
                <th style="width: 25%">Uraian</th>
                <?php
                for($i=0;$i<count($arrRekanan);$i++)
                {
                ?>
                <th style="text-align:center;""><?=$arrRekanan[$i]?></th>
                <?php
                }
                ?>
              </tr>
              <tr class="gelap">
                <td colspan="<?=2+(count($arrRekanan))?>"><strong>DATA ADMINISTRASI</strong></td>
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
                <td align="center"><?=$i?>.</td>
                <td> <?=$paket_evaluasi_admin->getField("NAMA")?></td>
                <?php
                for($j=0;$j<count($arrRekanan);$j++)
                {
                    //set up 16-10-2012
                    $paket_dokumen = new PaketDokumen();
                    $paket_dokumen->selectByParams(array("PAKET_ID" => $reqId, "REKANAN_USER_ID" => $arrRekananId[$j], "NAMA" => $paket_evaluasi_admin->getField("NAMA")));
                    $paket_dokumen->firstRow();
                    ?>
                    <td align="center">
                      <?php 
                      if($paket_dokumen->getField("KETERANGAN"))
                        echo '<img src="images/centang.png">';
                      else
                        echo '<img src="images/delete-icon.png">';
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

              <tr class="gelap">
                <td colspan="<?=2+(count($arrRekanan))?>"><strong> DATA TEKNIS</strong></td>
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
                <td> <?=$paket_evaluasi_teknis->getField("NAMA")?></td>
                <?php
                for($j=0;$j<count($arrRekanan);$j++)
                {
                    //set up 16-10-2012
                    $paket_dokumen = new PaketDokumen();
                    $paket_dokumen->selectByParams(array("PAKET_ID" => $reqId, "REKANAN_USER_ID" => $arrRekananId[$j], "NAMA" => $paket_evaluasi_teknis->getField("NAMA")));
                    $paket_dokumen->firstRow();
                    ?>
                    <td align="center">
                      <?php 
                      if($paket_dokumen->getField("KETERANGAN"))
                        echo '<img src="images/centang.png">';
                      else
                        echo '<img src="images/delete-icon.png">';
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

              <tr class="gelap">
               <!--  <td colspan="<?php //(count($arrRekanan)-1)?>"><strong> REKAPITULASI</strong></td> -->
                <td colspan="2"><strong> REKAPITULASI</strong></td>
                <?php
                $statusUserLogin = "";
                $statusUserLoginAlasan = "";
                for($j=0;$j<count($arrRekanan);$j++)
                {
                  if($arrRekananId[$j] == $this->ID)
                  {
                    $statusUserLogin =  $arrKelengkapan[$j];
                    $statusUserLoginAlasan =  $arrKelengkapanAlasan[$j];
                  }

                  ?>
                  <td align="center">
                    <?php
                    if($arrKelengkapan[$j] == "0")
                    {
                    ?>
                      <div class="alert alert-danger" style="font-size: 12px">Dok. Penawaran Tidak Lengkap <br>
                    <?php
                    if($arrKelengkapanAlasan[$j] == "")
                    {}
                    else
                        echo "Alasan : ".strtoupper($arrKelengkapanAlasan[$j]);
                    ?>
                    </div>
                    <?php
                    }
                    else
                    {
                    ?>
                        <div class="alert alert-primary" style="font-size: 12px">Dok. Penawaran Lengkap</div>
                    <?php
                    }
                    ?>
                  </td>
                  <?php
                }
                ?>
              </tr>    
            </table>

            <div class="form-actions">
              <a href="main/index/paket_detil/?eid=<?=$reqId?>&key=<?=$reqUUID?>" class="<?= CLASS_BTN_DANGER ?> mr-1"><?= BTN_KEMBALI ?></a>
               <!-- <a href="main/loadUrl/report/dokumen_pembukaan_penawaran_ba_pdf?reqId=<?=$reqId?>" target="_blank" class="btn btn-info"><i class="fa fa-download"></i> BA Pembukaan</a>   -->
               <a href="main/loadUrl/report/dokumen_pembukaan_penawaran_ba_pdf_rekanan?reqId=<?=$reqId?>" target="_blank" class="<?= CLASS_BTN_INFO ?>"><?= BTN_PRINT ?> Hasil Pembukaan</a>  
            </div>

          </div>
        </div>
      </div>
    </div> 
  </div>    
  </div>
</div>