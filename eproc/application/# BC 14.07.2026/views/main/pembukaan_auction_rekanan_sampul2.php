<?php
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 * 
 */
$this->load->library("paketinfo"); $paketInfo = new paketinfo();
include_once("functions/string.func.php");
include_once("functions/default.func.php");
include_once("functions/encrypt2.func.php");
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

$paket_rekanan->selectByParamsPaketLelang(array("PAKET_ID" => $reqId, "REKANAN_ID" => $this->ID), -1, -1, " AND TANGGAL_DAFTAR IS NOT NULL AND A.LULUS_PENDAFTARAN = 1 AND A.LULUS_KUALIFIKASI = 1  AND A.LULUS_PENAWARAN_SAMPUL1 = 1 ");
$paket_rekanan->firstRow();
if($paket_rekanan->getField("PAKET_REKANAN_ID") == "")
	exit;

/* CEK APAKAH SUDAH MEMASUKKAN DOKUMEN PENAWARAN */
$paket_dokumen_validasi = new PaketDokumen();
$sudahUpload = $paket_dokumen_validasi->getCountByParams(array("PAKET_ID" => $reqId, "REKANAN_USER_ID" => $this->ID), " AND JENIS_DOKUMEN LIKE 'PENAWARAN%' ");
if($sudahUpload <= 0)
	exit;

if($paketInfo->publish_ba_penawaran_sampul2 == "")
	exit;

$paket_rekanan->selectByParams(array("PAKET_ID" => $reqId), -1, -1, " AND A.TANGGAL_DAFTAR IS NOT NULL AND A.LULUS_PENDAFTARAN = 1 AND A.LULUS_KUALIFIKASI = 1 AND A.KIRIM_PENAWARAN = 1 AND A.LULUS_PENAWARAN_SAMPUL1 = 1 AND A.REKANAN_ID = '".$this->REKANAN_ID."'");
while($paket_rekanan->nextRow())
{
	$arrRekanan[] = $paket_rekanan->getField("REKANAN");
	$arrRekananId[] = $paket_rekanan->getField("REKANAN_ID");
	$arrPaketRekananId[] = $paket_rekanan->getField("PAKET_REKANAN_ID");
	$arrPaketRekananNilai[] = $paket_rekanan->getField("NILAI_PENAWARAN");
	$arrRekananHadirPembukaan[] = $paket_rekanan->getField("HADIR_PEMBUKAAN_PENAWARAN");
	$arrPasswordDokumen[] = $paket_rekanan->getField("KIRIM_PENAWARAN_PASSWORD");
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
        <h4 class="card-title text-white">Pembukaan Penawaran File 2 
        </h4>
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
            <table class="table table-bordered">
              <tr>
                <th style="width: 2%">No.</th>
                <th style="width: 25%">Uraian</th>
                <?php
                for($i=0;$i<count($arrRekanan);$i++)
                {
                ?>
                <th style="width:auto; text-align:center"><?=$arrRekanan[$i]?></th>
                <?php
                }
                ?>
              </tr>
              <tr>
                <td colspan="<?=2+(count($arrRekanan))?>"><strong> DATA HARGA</strong></td>
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
              <tr>
                <td align="center"><?=$i?>.</td>
                <td> <?=$paket_evaluasi_harga->getField("NAMA")?></td>
                <?php
                for($j=0;$j<count($arrRekanan);$j++)
                {
                    //set up 16-10-2012
                    $paket_dokumen = new PaketDokumen();
                    $paket_dokumen->selectByParams(array("PAKET_ID" => $reqId, "REKANAN_USER_ID" => $arrRekananId[$j], "NAMA" => $paket_evaluasi_harga->getField("NAMA")));
                    $paket_dokumen->firstRow();
                    ?>
                    <td align="center">
                      <?php //$paket_dokumen->getField("KETERANGAN")?>
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


              <tr>
                <td colspan="<?=2+(count($arrRekanan))?>"><strong> REKAPITULASI</strong></td>
              </tr>

              <!-- <tr>
                <td>1.</td>
                <td> Copy Surat Penawaran </td> -->
                <?php
                // for($j=0;$j<count($arrRekanan);$j++)
                // {
                ?>
                    <!-- <td style="text-align: center;"> -->
                    <?php
                    // $paket_dokumen = new PaketDokumen();
                    // $paket_dokumen->selectByParams(array("PAKET_ID" => $reqId, "REKANAN_USER_ID" => $arrRekananId[$j], "JENIS_DOKUMEN" => 'PEMBUKAAN_PENAWARAN'));
                    // $paket_dokumen->firstRow();
                    // $file_penawaran_rekanan = $paket_dokumen->getField("PATH_FILE");
                    // if($file_penawaran_rekanan == "")
                    // {
                    //   echo '<img src="images/delete-icon.png">';
                    // }
                    // else
                    // {
                    ?>
                    <!-- <div align="center">
                    <a href="uploads/penawaran/<?php //$paket_dokumen->getField("PATH_FILE")?>" target="_blank">download <img src="images/icon-download.png" alt="" width="16" height="16" border="0" /></a>
                    </div>
                    </td> -->
                    <?php
                //     }
                //     unset($paket_dokumen);
                // }
                ?>
              <!-- </tr> -->
              <form action="" name="frmInformasiAdd" method="post">
              <tr>
                <td>1.</td>
                <td> Nilai Penawaran </td>
                <?php
                for($j=0;$j<count($arrRekanan);$j++)
                {
                ?>
                <td align="center">
                  <?php
                  if($arrPaketRekananNilai[$j] == "")
                      echo "DOKUMEN TIDAK DAPAT DIBUKA / TIDAK LENGKAP";
                  else
                      echo numberToIna($arrPaketRekananNilai[$j])."<br><small>(".terbilang($arrPaketRekananNilai[$j]).") </small>";
                  ?>
                </td>
                <?php
                }
                ?>
              </tr>
              <tr>
                <td>2.</td>
                <td>Harga Perkiraan</td>
                <td colspan="<?=count($arrRekanan)?>" align="center">
                  <?=numberToIna($reqNilaiEstimate)?><br>
                  (<?=terbilang($reqNilaiEstimate)?>)
                </td>
              </tr> 
            </table> 

            <div class="form-actions">
              <a href="main/index/paket_detil/?eid=<?=$reqId?>&key=<?=$reqUUID?>" class="<?= CLASS_BTN_DANGER ?> mr-1"> <?= BTN_KEMBALI ?> </a> 
              <a href="main/loadUrl/report/dokumen_pembukaan_penawaran_sampul2_ba_pdf?reqId=<?=$reqId?>"  target="_blank" class="<?= CLASS_BTN_PRIMARY ?>"><?= BTN_PRINT ?> Hasil Pembukaan </a>  
            </div> 
          </div>
        </div>
      </div>
    </div>
  </div> 
</div>  