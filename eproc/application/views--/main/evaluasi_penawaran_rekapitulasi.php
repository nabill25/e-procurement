<?php
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 *
 */

$reqId = $this->input->get("reqId");
$this->libsession->cekSession($reqId);

$this->load->library("paketinfo"); $paketInfo = new paketinfo();
include_once("functions/string.func.php");
include_once("functions/default.func.php");
include_once("functions/date.func.php");
$this->load->model("Paket");
$this->load->model("PaketRekanan");
$this->load->model("PaketDokumen");
$this->load->model("RekananEvaluasiTeknisTawar");
$this->load->model("RekananEvaluasiAdminTawar");
$this->load->model("RekananEvaluasiHargaTawar");
$this->load->model("MatrixEvaluasi");
$this->load->model("RekananPaketPenawaran");
$this->load->model("PaketPenawaran");


$paket_rekanan = new PaketRekanan();
$paket_rekanan_nilai = new PaketRekanan();
$matrix_evaluasi = new MatrixEvaluasi();
$paket_penawaran = new PaketPenawaran();
$paket_dokumen = new PaketDokumen();

$submitSimpan = $this->input->post("submitSimpan");
$reqUndangan = $this->input->post("reqUndangan");
$reqEvaluasiPenilaian = $_POST["reqEvaluasiPenilaian"];
$reqPaketRekananUrutId = $_POST["reqPaketRekananUrutId"];
$reqUrutan = $_POST["reqUrutan"];
$reqEvaluasiPenilaianKeterangan = $_POST["reqEvaluasiPenilaianKeterangan"];
$reqPaketRekananId = $_POST["reqPaketRekananId"];
$reqPaketRekananUrutArray =unserialize(stripslashes($_POST['reqPaketRekananUrutArray']));

$paketInfo->getPaket($reqId);
$reqNamaPaket = $paketInfo->nama;
$reqJenisPekerjaan = $paketInfo->jenis;
$reqJenisPekerjaanId = $paketInfo->jenis_id;
$reqMetodeEvaluasi = $paketInfo->metode_evaluasi;
$reqMetodeEvaluasiId = $paketInfo->metode_evaluasi_id;
$reqOwnerEstimate  = $paketInfo->nilai_owner_estimate;
$reqRekananIdPemenang = $paketInfo->rekanan_id_pemenang;
$reqMetodePengadaan = $paketInfo->metode_lelang_id;
$reqJenisPegadaaan = $paketInfo->jenis_pengadaan;
$reqBidding = $paketInfo->bidding;
$reqMultiPemenang = $paketInfo->multi_pemenang;
$reqUUID = $paketInfo->uuid;

if ($reqMetodeEvaluasiId == '2') { // sistem nilai
$paket_rekanan->selectByParams2(array("PAKET_ID" => $reqId), -1, -1, " AND A.TANGGAL_DAFTAR IS NOT NULL AND A.LULUS_PENDAFTARAN = 1 AND A.LULUS_KUALIFIKASI = 1 ", '', 'ORDER BY A.LULUS_PENAWARAN_URUT ASC');
  // code...
} else { // Harga Terendah
$paket_rekanan->selectByParams2(array("PAKET_ID" => $reqId), -1, -1, " AND A.TANGGAL_DAFTAR IS NOT NULL AND A.LULUS_PENDAFTARAN = 1 AND A.LULUS_KUALIFIKASI = 1 ");
}

while($paket_rekanan->nextRow())
{

  // ambil nilai koreksi
  $paket_penawaran->selectByParamsRekananPaketPenawaran(array('B.PAKET_REKANAN_ID' => $paket_rekanan->getField("PAKET_REKANAN_ID")), -1, -1, " AND 1=1");
  $paket_penawaran->firstRow();

  $arrRekananId[] = $paket_rekanan->getField("REKANAN_ID");
  $arrRekanan[] = $paket_rekanan->getField("REKANAN");
  $arrPaketRekananId[] = $paket_rekanan->getField("PAKET_REKANAN_ID");
  $arrPaketRekananNilaiAuction[] = $paket_rekanan->getField("NILAI_PENAWARAN");
  $arrPaketRekananNilaiSebelumnya[] = $paket_rekanan->getField("UNIT_PRICE");
  $arrPaketRekananNilai[] = $paket_penawaran->getField("JUMLAH_KOREKSI");
  $arrPaketRekananLulus[] = $paket_rekanan->getField("LULUS_PENAWARAN");
}
$i = 0;

if ($reqBidding == 1) {
  $paket_rekanan_nilai->selectNilaiPenawaran3(array("PAKET_ID" => $reqId));
  while($paket_rekanan_nilai->nextRow())
  {
    $arrUrutan[] = $paket_rekanan_nilai->getField("PAKET_REKANAN_ID");
  }
} else {
  $paket_rekanan_nilai->selectNilaiPenawaran2(array("PAKET_ID" => $reqId));
  while($paket_rekanan_nilai->nextRow())
  {
    $arrUrutan[] = $paket_rekanan_nilai->getField("PAKET_REKANAN_ID");
  }
}

if (is_array($arrRekanan)) {
  $arrRekananId = $arrRekananId;
  $arrRekanan = $arrRekanan;
  $arrPaketRekananId = $arrPaketRekananId;
  $arrPaketRekananNilaiAuction = $arrPaketRekananNilaiAuction;
  $arrPaketRekananNilaiSebelumnya = $arrPaketRekananNilaiSebelumnya;
  $arrPaketRekananNilai = $arrPaketRekananNilai;
  $arrPaketRekananLulus = $arrPaketRekananLulus;
  $reqPaketRekananId = $reqPaketRekananId;
} else {
  $arrRekananId = array();
  $arrRekanan = array();
  $arrPaketRekananId = array();
  $arrPaketRekananNilaiAuction = array();
  $arrPaketRekananNilaiSebelumnya = array();
  $arrPaketRekananNilai = array();
  $arrPaketRekananLulus = array();
  $reqPaketRekananId = array();
}
if (is_array($arrUrutan)) {
  $arrUrutan = $arrUrutan;
} else {
  $arrUrutan = array();
}
function getUrutan($reqPaketRekananId, $arrUrutan)
{
  $key = array_search($reqPaketRekananId, $arrUrutan);
  return $key + 1;
}

$matrix_evaluasi->selectByParams(array("A.PAKET_JENIS_ID" => $reqJenisPekerjaanId, "A.PAKET_METODE_EVALUASI_ID" => $reqMetodeEvaluasiId));
$matrix_evaluasi->firstRow();

?>
<script>
function setEvaluasiPenawaran(ctrl, ctrl_change)
{
  //get the state of the check box
  if (ctrl.checked == true) {
    //the box is checked, so show the table
    document.getElementById(ctrl_change).value = 1;
  } else {
    //hide the table
    document.getElementById(ctrl_change).value = 0;
  }
}
</script>
<script type="text/javascript">
$(function(){
  $('#ff').form({
    url:'paket_rekanan_json/evaluasi_penawaran_rekapitulasi',
    onSubmit:function(){
      return $(this).form('validate');
    },
    success:function(data){
      // $.messager.alert('Info', data, 'info');
      alertSuccess2(data);
      $('#loading').hide();
    }
  });
});
</script>

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
        <h4 class="card-title text-white">Rekapitulasi Hasil Evaluasi</h4>
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
          <div class="form-group col-md-12 mb-2">
            <div class="col-md-12">
              <ul class="nav nav-tabs process-model more-icon-preocess" role="tablist">
                <?php
                  if ($reqMetodePengadaan == 7) {
                ?>
                <style type="text/css">
                  .process-model li {
                    display: inline-block;
                    width: 50% !important;
                    text-align: center;
                    float: none;
                  }
                </style>
                <?php
                } ?>
                <?php
                // 1-e-Tender ,2-Pengadaan Langsung ,5-Penunjukan Langsung Cepat,7-e-Tender Cepat
                if ($reqMetodePengadaan != 7) { ?>
                <li role="presentation"><a href="main/index/evaluasi_penawaran_administrasi/?reqId=<?=$reqId?>"><i class="fa fa-check" aria-hidden="true"></i>
                  <p>Evaluasi Administrasi</p>
                  </a></li>
                <?php
                } ?>
                <?php
                // 1-e-Tender ,2-Pengadaan Langsung ,5-Penunjukan Langsung Cepat,7-e-Tender Cepat
                if ($reqMetodePengadaan != 7) { ?>
                <li role="presentation"><a href="main/index/evaluasi_penawaran_teknis/?reqId=<?=$reqId?>" ><i class="fa fa-pencil" aria-hidden="true"></i>
                  <p>Evaluasi Teknis</p>
                  </a></li>
                <?php
                } ?>
                <li role="presentation"><a href="main/index/evaluasi_penawaran_harga/?reqId=<?=$reqId?>"><i class="fa fa-money" aria-hidden="true"></i>
                  <p>Evaluasi Harga</p>
                  </a></li>
                <li role="presentation" class="active"><a href="main/index/evaluasi_penawaran_rekapitulasi/?reqId=<?=$reqId?>"><i class="fa fa-list-alt" aria-hidden="true"></i>
                  <p>Rekapitulasi</p>
                  </a></li>
              </ul>
            </div>
          </div>

          <form id="ff" class="easyui-form form-horizontal" method="post" data-options="novalidate:true" enctype="multipart/form-data">

            <div class="table-responsive">
              <table class="table table-bordered table-hover">
                <tr>
                  <td width="30%"> Pekerjaan</td>
                  <td> <?=$reqNamaPaket?> </td>
                </tr>
                <tr>
                  <td width="20%"> Jenis Pekerjaan</td>
                  <td> <?=$reqJenisPekerjaan?> </td>
                </tr>
                <tr>
                  <td width="20%"> Metode Evaluasi</td>
                  <td> <?=$reqMetodeEvaluasi?> </td>
                </tr>
                <!-- <tr>
                  <?php
                  $paket_dokumen = new PaketDokumen();
                  $paket_dokumen->selectByParams(array("PAKET_ID" => $reqId, "JENIS_DOKUMEN" => "EVALUASI_PENAWARAN_ADMINISTRASI"));
                  $paket_dokumen->firstRow();
                  $dokumen = $paket_dokumen->getField("PATH_FILE");
                  if($dokumen == "")
                  {}
                  else
                  {
                  ?>
                  <td>Download Berita Acara Evaluasi Administrasi</td>
                  <td>
                  <a href="uploads/penawaran/<?=$dokumen?>" target="_blank"><img src="images/icon-download.png"></a>
                  </td>
                  <?php
                  }
                  ?>
                </tr>
                <tr>
                  <?php
                  $paket_dokumen_teknis = new PaketDokumen();
                  $paket_dokumen_teknis->selectByParams(array("PAKET_ID" => $reqId, "JENIS_DOKUMEN" => "EVALUASI_PENAWARAN_TEKNIS"));
                  $paket_dokumen_teknis->firstRow();
                  $dokumen_teknis = $paket_dokumen_teknis->getField("PATH_FILE");
                  if($dokumen_teknis == "")
                  {}
                  else
                  {
                  ?>
                  <td>Download Berita Acara Evaluasi Teknis</td>
                  <td>
                  <a href="uploads/penawaran/<?=$dokumen_teknis?>" target="_blank"><img src="images/icon-download.png"></a>
                  </td>
                  <?php
                  }
                  ?>
                </tr>
                <tr>
                  <?php
                  $paket_dokumen_harga = new PaketDokumen();
                  $paket_dokumen_harga->selectByParams(array("PAKET_ID" => $reqId, "JENIS_DOKUMEN" => "EVALUASI_PENAWARAN_HARGA"));
                  $paket_dokumen_harga->firstRow();
                  $dokumen_harga = $paket_dokumen_harga->getField("PATH_FILE");
                  if($dokumen_harga == "")
                  {}
                  else
                  {
                  ?>
                  <td>Download Berita Acara Evaluasi Harga</td>
                  <td>
                  <a href="uploads/penawaran/<?=$dokumen_harga?>" target="_blank"><img src="images/icon-download.png"></a>
                  </td>
                  <?php
                  }
                  ?>
                </tr> -->
              </table>

              <table class="table table-bordered table-hover table-responsive">
                <tr class="judul-kolom">
                  <th align="center" valign="middle" width="2%">No.</th>
                  <th colspan="2" align="center" valign="middle">Uraian</th>
                  <?php
                  for($i=0;$i<count($arrRekanan);$i++)
                  {
                  ?>
                  <th style="text-align: center;"><?=$arrRekanan[$i]?></th>
                  <?php
                  }
                  ?>
                </tr>
                <?php
                // 1-e-Tender ,2-Pengadaan Langsung ,5-Penunjukan Langsung Cepat,7-e-Tender Cepat
                if ($reqMetodePengadaan != 7) { ?>
                <tr>
                  <td valign="top"><strong>I</strong></td>
                  <td valign="top">&nbsp;</td>
                  <td valign="top"><strong> EVALUASI ADMINISTRASI </strong></td>
                  <?php
                  for($i=0;$i<count($arrRekanan);$i++)
                  {
                      $rekanan_evaluasi_admin = new RekananEvaluasiAdminTawar();
                      $rekanan_evaluasi_admin->selectMemenuhiSyarat($reqId, $arrPaketRekananId[$i]);
                      $rekanan_evaluasi_admin->firstRow();
                      // echo $rekanan_evaluasi_admin->getField("JUMLAH_DILENGKAPI");
                      // if($rekanan_evaluasi_admin->getField("JUMLAH_EVALUASI") <= $rekanan_evaluasi_admin->getField("JUMLAH_DILENGKAPI"))
                      if($rekanan_evaluasi_admin->getField("JUMLAH_DILENGKAPI") >= 1)
                      {
                        $status_admin = '<img src="images/centang.png">';
                        $keterangan_admin = $rekanan_evaluasi_admin->getField("KETERANGAN_LULUS");
                        $arrEvaluasiAdmin[$i] = 1;
                      }
                      else
                      {
                        $status_admin = '<img src="images/uncentang.png">';
                        $keterangan_admin = $rekanan_evaluasi_admin->getField("KETERANGAN_GAGAL");
                        $arrEvaluasiAdmin[$i] = 0;
                      }
                  ?>
                      <td align="center">
                        <?= $status_admin.'<br><small>'.$keterangan_admin.'</small>'; ?>
                      </td>
                  <?php
                      unset($rekanan_evaluasi_admin);
                  }
                  ?>
                </tr>
                <tr>
                  <td valign="top"><strong>II</strong></td>
                  <td valign="top">&nbsp;</td>
                  <td valign="top"><strong> EVALUASI TEKNIS </strong></td>
                  <?php
                  for($i=0;$i<count($arrRekanan);$i++)
                  {
                      $rekanan_evaluasi_teknis = new RekananEvaluasiTeknisTawar();
                      $rekanan_evaluasi_teknis->selectMemenuhiSyarat($reqId, $arrPaketRekananId[$i]);
                      $rekanan_evaluasi_teknis->firstRow();
                      // if($rekanan_evaluasi_teknis->getField("JUMLAH_EVALUASI") <= $rekanan_evaluasi_teknis->getField("JUMLAH_DILENGKAPI"))
                      if($rekanan_evaluasi_teknis->getField("JUMLAH_DILENGKAPI") >= 1 && $arrEvaluasiAdmin[$i] == 1)
                      {
                        $status_teknis = '<img src="images/centang.png">';
                        $arrEvaluasiTeknis[$i] = 1;
                        if ($reqMetodeEvaluasiId == '2' || $reqMetodeEvaluasiId == '10') {
                          $skor_teknis = 'Nilai Teknis <b>'.$rekanan_evaluasi_teknis->getField("NILAI_TEKNIS").'</b><br>'.$rekanan_evaluasi_teknis->getField("KETERANGAN_LULUS");
                          $skor_teknis_angka[$arrPaketRekananId[$i]] = $rekanan_evaluasi_teknis->getField("NILAI_TEKNIS");
                        } else {
                          $keterangan_teknis = $rekanan_evaluasi_teknis->getField("KETERANGAN_LULUS");
                        }

                      }
                      else
                      {
                        $status_teknis = '<img src="images/uncentang.png">';
                        $keterangan_teknis = $rekanan_evaluasi_teknis->getField("KETERANGAN_GAGAL");
                        $arrEvaluasiTeknis[$i] = 0;
                        $skor_teknis = 'Nilai Teknis <b>'.$rekanan_evaluasi_teknis->getField("NILAI_TEKNIS").'</b><br>'.$rekanan_evaluasi_teknis->getField("KETERANGAN_GAGAL");
                      }
                  ?>
                      <td align="center">
                        <?php
                        if ($reqMetodeEvaluasiId == '2' || $reqMetodeEvaluasiId == '10') { ?>
                          <?= $status_teknis.'<br><small>'.$skor_teknis.'</small>'; ?>
                        <?php
                        } else { ?>
                          <?= $status_teknis.'<br><small>'.$keterangan_teknis.'</small>'; ?>
                        <?php
                        } ?>
                      </td>
                  <?php
                      unset($rekanan_evaluasi_teknis);
                  }
                  ?>
                </tr>
                <?php
                } ?>
                <tr>
                  <td valign="top">
                  <?php
                  // 1-e-Tender ,2-Pengadaan Langsung ,5-Penunjukan Langsung Cepat,7-e-Tender Cepat
                  if ($reqMetodePengadaan != 7) { ?>
                    <strong>III</strong>
                  <?php
                  } else { ?>
                    <strong>I</strong>
                  <?php
                  } ?>
                  </td>
                  <td valign="top">&nbsp;</td>
                  <td valign="top"><strong> EVALUASI HARGA </strong></td>
                  <?php
                  for($i=0;$i<count($arrRekanan);$i++)
                  {
                      $rekanan_evaluasi_harga = new RekananEvaluasiHargaTawar();
                      $rekanan_evaluasi_harga->selectMemenuhiSyarat2($reqId, $arrPaketRekananId[$i]);
                      $rekanan_evaluasi_harga->firstRow();
                      // if($rekanan_evaluasi_harga->getField("JUMLAH_EVALUASI") <= $rekanan_evaluasi_harga->getField("JUMLAH_DILENGKAPI"))

                      // 1-e-Tender ,2-Pengadaan Langsung ,5-Penunjukan Langsung Cepat,7-e-Tender Cepat
                      if ($reqMetodePengadaan != 7) {
                        if($rekanan_evaluasi_harga->getField("JUMLAH_DILENGKAPI") >= 1 && $arrEvaluasiAdmin[$i] == 1 && $arrEvaluasiTeknis[$i] == 1)
                        {
                          $status_harga = '<img src="images/centang.png">';
                          $arrEvaluasiHarga[$i] = 1;
                          if ($reqMetodeEvaluasiId == '2') {
                            $skor_harga = 'Nilai Harga <b>'.$rekanan_evaluasi_harga->getField("NILAI_HARGA").'</b><br>'.$rekanan_evaluasi_harga->getField("KETERANGAN_LULUS");
                            $skor_harga_angka[$arrPaketRekananId[$i]] = $rekanan_evaluasi_harga->getField("NILAI_HARGA");
                          } else {
                            $keterangan_harga = $rekanan_evaluasi_harga->getField("KETERANGAN_LULUS");
                          }

                        }
                        else
                        {
                          $status_harga = '<img src="images/uncentang.png">';
                          $keterangan_harga = $rekanan_evaluasi_harga->getField("KETERANGAN_GAGAL");
                          $arrEvaluasiHarga[$i] = 0;
                          $skor_harga = 'Nilai Harga <b>'.$rekanan_evaluasi_harga->getField("NILAI_HARGA").'</b><br>'.$rekanan_evaluasi_harga->getField("KETERANGAN_GAGAL");
                        }
                      } else
                      {
                        if($rekanan_evaluasi_harga->getField("JUMLAH_DILENGKAPI") >= 1)
                        {
                          $status_harga = '<img src="images/centang.png">';
                          $arrEvaluasiHarga[$i] = 1;
                          if ($reqMetodeEvaluasiId == '2') {
                            $skor_harga = 'Nilai Harga <b>'.$rekanan_evaluasi_harga->getField("NILAI_HARGA").'</b>';
                          } else {
                            $keterangan_harga = $rekanan_evaluasi_harga->getField("KETERANGAN_LULUS");
                          }
                        }
                        else
                        {
                          $status_harga = '<img src="images/uncentang.png">';
                          $keterangan_harga = $rekanan_evaluasi_harga->getField("KETERANGAN_GAGAL");
                          $arrEvaluasiHarga[$i] = 0;
                          $skor_harga = '';
                        }
                      }
                  ?>
                      <td align="center">
                        <?php
                        if ($reqMetodeEvaluasiId == '2') { ?>
                          <?= $status_harga.'<br><small>'.$skor_harga.'</small>'; ?>
                        <?php
                        } else { ?>
                          <?= $status_harga.'<br><small>'.$keterangan_harga.'</small>'; ?>
                        <?php
                        } ?>
                      </td>
                  <?php
                      unset($rekanan_evaluasi_harga);
                  }
                  ?>
                </tr>

                <!-- <tr>
                  <td valign="top">&nbsp;</td>
                  <td valign="top"> -->
                    <?php
                    // 1-e-Tender ,2-Pengadaan Langsung ,5-Penunjukan Langsung Cepat,7-e-Tender Cepat
                    if ($reqMetodePengadaan != 7) { ?>
                      <!-- I -->
                    <?php
                    } else {
                      // echo "1.";
                    } ?>
                  </td>
                  <!-- <td valign="top"> EVALUASI KEWAJARAN HARGA </td> -->
                  <?php
                  for($i=0;$i<count($arrRekanan);$i++)
                  {
                  ?>
                      <!-- <td valign="top">&nbsp;</td> -->
                  <?php
                  }
                  ?>
                <!-- </tr> -->
                </tr>
                <tr>
                  <td valign="top">&nbsp;</td>
                  <td valign="top">&nbsp;</td>
                  <td valign="top"> a. Penawaran Terkoreksi </td>
                  <?php
                  for($i=0;$i<count($arrRekanan);$i++)
                  {
                  ?>
                      <td align="center"><strong><?=$paketInfo->mata_uang?> <?=numberToIna($arrPaketRekananNilai[$i])?></strong></td>
                      <!-- <td align="center"><strong><?=$paketInfo->mata_uang?> <?php //numberToIna($paket_penawaran->getField("JUMLAH_KOREKSI"))?></strong></td> -->
                  <?php
                  }
                  ?>
                </tr>
                <tr>
                  <td valign="top">&nbsp;</td>
                  <td valign="top">&nbsp;</td>
                  <td valign="top"> b. Harga Perkiraan Sendiri </td>
                  <td valign="top" colspan="<?=count($arrRekanan)?>" align="center"> <strong><?=$paketInfo->mata_uang?> <?= str_replace(",00","",numberToIna($reqOwnerEstimate))?></strong> </td>
                </tr>
                <!-- <tr>
                  <td valign="top">&nbsp;</td>
                  <td valign="top">&nbsp;</td>
                  <td valign="top"> e. Persentase Kesalahan Penawaran </td>
                  <?php
                  for($i=0;$i<count($arrRekanan);$i++)
                  {
                  ?>
                  <td valign="top" align="center"> 0,00% </td>
                  <?php
                  }
                  ?>
                </tr> -->
                <tr>
                  <td valign="top">&nbsp;</td>
                  <td valign="top">&nbsp;</td>
                  <td valign="top"> c. % penawaran terkoreksi terhadap Harga Perkiraan Sendiri </td>
                  <?php
                  for($i=0;$i<count($arrRekanan);$i++)
                  {
                      if((int)$reqOwnerEstimate == 0)
                        $presentase = 0;
                      else
                        $presentase = round(($arrPaketRekananNilai[$i] / $reqOwnerEstimate) * 100,2);

                      $arrEvaluasiPresentase[$i] = $presentase;
                  ?>
                      <td align="center"><strong><?=$presentase?>%</strong></td>
                  <?php
                  }
                  ?>
                </tr>
                <tr>
                  <!-- <td valign="top">&nbsp;</td>
                  <td valign="top">&nbsp;</td>
                  <td valign="top"> d. Penilaian </td> -->
                  <?php
                  for($i=0;$i<count($arrRekanan);$i++)
                  {
                    // if($arrEvaluasiAdmin[$i] == 0 || $arrEvaluasiTeknis[$i] == 0 || $arrEvaluasiHarga[$i] == 0 || $arrEvaluasiPresentase[$i] == 0 || $arrEvaluasiPresentase[$i] > 100)
                    if ($reqMetodePengadaan != 7) {
                      if($arrEvaluasiAdmin[$i] == 0 || $arrEvaluasiTeknis[$i] == 0 || $arrEvaluasiHarga[$i] == 0)
                        $rekananLulus = "0";
                      else
                        $rekananLulus = "1";
                    } else { // Tender Cepat
                      if($arrEvaluasiHarga[$i] == 0)
                        $rekananLulus = "0";
                      else
                        $rekananLulus = "1";
                    }
                  ?>
                      <!-- <td valign="top" align="center"> -->
                      <input type="hidden" name="reqPaketRekananId[]" value="<?=$arrPaketRekananId[$i]?>" />
                      <input type="hidden" name="reqEvaluasiPenilaian[]" id="reqEvaluasiPenilaian<?=$i?>" value="<?=(int)$rekananLulus?>" />
                      <?php
                      // if($arrEvaluasiHarga[$i] == 0 || $arrEvaluasiPresentase[$i] == 0 || $arrEvaluasiPresentase[$i] > 110)
                      // {} else {
                        ?>
                      <!-- <input name="checkbox[]" style="cursor: pointer;" type="checkbox" value="1" id="reqEvaluasiPenilaianCheckbox<?php //$i?>" onchange="setEvaluasiPenawaran(this, 'reqEvaluasiPenilaian<?php // $i?>')" <?php //if($arrPaketRekananLulus[$i] == 1) { ?> checked="checked" <?php // } ?> />
                      <label for="reqPenilaian">Memenuhi Syarat</label></td> -->
                      <?php
                      // } ?>
                  <?php
                  }
                  ?>
                </tr>
                <!-- <tr>
                  <td valign="top">&nbsp;</td>
                  <td valign="top">&nbsp;</td>
                  <td valign="top">&nbsp;</td>
                  <?php
                  // for($i=0;$i<count($arrRekanan);$i++)
                  // {
                  ?>
                      <td valign="top">&nbsp;</td>
                  <?php
                  // }
                  ?>
                </tr> -->
                <?php
                /**
                 * 1:Tender Umum
                 * 2:Pengadaan Langsung
                 * 3:Tender Terbatas
                 * 4:Seleksi Langsung
                 * 5:Penunjukan Langsung
                 * 6:e-Purchasing
                 * 7:Tender Cepat
                 * 8:Kompetisi
                 * 9:Pembelian Langsung Offline
                 * 10:Tender Prakualifikasi
                **/
                if ($reqMetodePengadaan != 7)
                { // Bukan Tender Cepat
                ?>

                <?php
                if ($reqMetodeEvaluasiId == '2') { // sistem nilai ?>
                  <tr>
                    <td></td>
                    <td colspan="2"><b>TOTAL NILAI TEKNIS DAN HARGA</b></td>
                    <?php
                    for($i=0;$i<count($arrRekanan);$i++)
                    { ?>
                        <td align="center"><strong><?= $skor_teknis_angka[$arrPaketRekananId[$i]] + $skor_harga_angka[$arrPaketRekananId[$i]] ?></strong></td>
                    <?php
                    } ?>
                  </tr>
                <?php
                }  ?>

                  <tr>
                    <td valign="top"><strong>IV</strong></td>
                    <td colspan="2" valign="top"> <strong>KESIMPULAN</strong></td>
                    <?php
                    for($i=0;$i<count($arrRekanan);$i++)
                    {
                        if((int)$reqOwnerEstimate == 0)
                          $nilai = 0;
                        else
                            $nilai = round(((int)$arrPaketRekananNilai[$i] / (int)$reqOwnerEstimate) * 100,2);

                        if($arrEvaluasiAdmin[$i] == 0 || $arrEvaluasiTeknis[$i] == 0 || $arrEvaluasiHarga[$i] == 0 )
                        { // tidak lulus Admin, Teknis & Harga
                            $hasil = "GUGUR";
                            $notifNegosiasi = '-';
                        } else
                        { // Lulus
                            if ($arrEvaluasiPresentase[$i] == 0 || $arrEvaluasiPresentase[$i] > 100)
                            {
                              $hasil = "LULUS <br> <small style='color:red'><i>Diatas Harga Perkiraan Sendiri</i></small>";
                              $notifNegosiasi = 'Ya';
                            } else { // Untuk Non Tender
                              $hasil = "LULUS";
                              $notifNegosiasi = 'Ya';
                            }
                        }
                    ?>
                        <td align="center"><strong><?=$hasil?></strong></td>
                        <input type="hidden" name="reqPaketRekananUrutId[]" value="<?=$arrPaketRekananId[$i]?>">
                        <input type="hidden" name="reqUrutan[]" value="<?=getUrutan($arrPaketRekananId[$i], $arrUrutan)?>">
                        <input type="hidden" name="reqEvaluasiPenilaianKeterangan[]" value="<?=$hasil?>">
                    <?php
                    }
                    ?>
                  </tr>
                <?php
                } else // Tender Cepat
                { ?>
                  <tr>
                    <td valign="top"><strong>II</strong></td>
                    <td colspan="2" valign="top"> <strong>KESIMPULAN</strong></td>
                    <?php
                    for($i=0;$i<count($arrRekanan);$i++)
                    {
                        if((int)$reqOwnerEstimate == 0)
                          $nilai = 0;
                        else
                          $nilai = round(((int)$arrPaketRekananNilai[$i] / (int)$reqOwnerEstimate) * 100,2);

                        if($arrEvaluasiHarga[$i] == 0 || $arrEvaluasiPresentase[$i] == 0 || $arrEvaluasiPresentase[$i] > 110)
                        {
                          $hasil = "GUGUR";
                          $notifNegosiasi = '-';
                        } else
                        {
                          if ($arrEvaluasiPresentase[$i] == 0 || $arrEvaluasiPresentase[$i] > 100) {
                            $hasil = "LULUS <br> <small style='color:red'><i>Diatas Harga Perkiraan Sendiri</i></small>";
                            $notifNegosiasi = 'Ya';
                          } else {
                            $hasil = "LULUS";
                            $notifNegosiasi = 'Ya';
                          }
                          // $hasil = "LULUS";
                          // $notifNegosiasi = 'Ya';
                        }
                    ?>
                        <td align="center"><strong><?=$hasil?></strong></td>
                        <input type="hidden" name="reqPaketRekananUrutId[]" value="<?=$arrPaketRekananId[$i]?>">
                        <input type="hidden" name="reqUrutan[]" value="<?=getUrutan($arrPaketRekananId[$i], $arrUrutan)?>">
                        <input type="hidden" name="reqEvaluasiPenilaianKeterangan[]" value="<?=$hasil?>">
                    <?php
                    }
                    ?>
                  </tr>
                <?php
                } ?>
                <?php
                if ($reqBidding == 1)
                {
                ?>
                <tr>
                  <td valign="top"><strong>III</strong></td>
                  <td colspan="2" valign="top"> <strong>UNDANGAN PEMBUKTIAN DAN AUCTION</strong></td>
                  <?php
                  for($i=0;$i<count($arrRekanan);$i++)
                  {
                    if ($reqMetodePengadaan != 7)
                    { // Bukan Tender Cepat
                      // if($arrEvaluasiAdmin[$i] == 0 || $arrEvaluasiTeknis[$i] == 0 || $arrEvaluasiHarga[$i] == 0 || $arrEvaluasiPresentase[$i] == 0 || $arrEvaluasiPresentase[$i] > 100) {
                      if($arrEvaluasiAdmin[$i] == 0 || $arrEvaluasiTeknis[$i] == 0 || $arrEvaluasiHarga[$i] == 0)
                      { // tidak lulus Admin, Teknis & Harga
                        $notifNegosiasi = '-';
                      } else
                      { // Lulus
                        $notifNegosiasi = 'Ya';
                      }
                    } else
                    { // Tender Cepat
                      if($arrEvaluasiHarga[$i] == 0)
                      { // tidak lulus evaluasi harga
                        $notifNegosiasi = '-';
                      } else
                      {
                        $notifNegosiasi = 'Ya';
                      }
                    }
                  ?>
                      <td align="center">
                        <!-- <strong><?php //$paketInfo->mata_uang?> <?php // numberToIna($arrPaketRekananNilaiAuction[$i])?></strong> -->
                        <?= $notifNegosiasi; ?>
                      </td>
                  <?php
                  }
                  ?>
                </tr>
                <?php
                 } else
                 {
                  /**
                   * 1:Tender Umum
                   * 2:Pengadaan Langsung
                   * 3:Tender Terbatas
                   * 4:Seleksi Langsung
                   * 5:Penunjukan Langsung
                   * 6:e-Purchasing
                   * 7:Tender Cepat
                   * 8:Kompetisi
                   * 9:Pembelian Langsung Offline
                   * 10:Tender Prakualifikasi
                  **/
                  // if ($reqMetodePengadaan != 7)
                  // { // Bukan Tender Cepat
                ?>
                <tr>
                  <td valign="top"><strong>V</strong></td>
                  <td colspan="2" valign="top"> <strong>
                  <?php
                  if ($reqMultiPemenang == '1') {
                    // echo 'UNDANGAN NEGOSIASI';
                    echo 'UNDANGAN PEMBUKTIAN DAN NEGOSIASI';
                  } else {
                    echo 'UNDANGAN PEMBUKTIAN DAN NEGOSIASI';
                  } ?>

                  </strong></td>
                  <?php
                  for($i=0;$i<count($arrRekanan);$i++)
                  {
                  ?>
                      <td align="center">
                        <?php
                        if($arrEvaluasiHarga[$i] == 0 || $arrEvaluasiPresentase[$i] == 0 || $arrEvaluasiPresentase[$i] > 110)
                        {
                            if($reqJenisPegadaaan == "PEMBELIAN")
                            {
                                ?>
                                <input type="radio" name="reqUndangan" value="<?=$arrRekananId[$i]?>" <?php if($arrRekananId[$i] == $reqRekananIdPemenang) { ?> checked <?php } ?>> Pilih 1
                                <?php
                            }
                        }
                        else
                        {
                        ?>
                        <input type="radio" name="reqUndangan" value="<?=$arrRekananId[$i]?>" <?php if($arrRekananId[$i] == $reqRekananIdPemenang) { ?> checked <?php } ?>> Pilih
                        <?php
                        }
                        ?>
                      </td>
                  <?php
                  }
                  ?>
                </tr>
                <?php
                  // }
                 }
                ?>
                <!-- <tr>
                  <td valign="top">&nbsp;</td>
                  <td colspan="<?=3+count($arrRekanan)?>" valign="top"> <?=$matrix_evaluasi->getField("KETERANGAN_HARGA")?> </td>
                </tr> -->
                <?php
                /**
                   * 1:Tender Umum
                   * 2:Pengadaan Langsung
                   * 3:Tender Terbatas
                   * 4:Seleksi Langsung
                   * 5:Penunjukan Langsung
                   * 6:e-Purchasing
                   * 7:Tender Cepat
                   * 8:Kompetisi
                   * 9:Pembelian Langsung Offline
                   * 10:Tender Prakualifikasi
                  **/
                if ($reqMetodePengadaan != 7)
                { ?>
                <!-- <tr>
                  <td valign="top">&nbsp;</td>
                  <td colspan="<?=3+count($arrRekanan)?>" valign="top"> <?=$matrix_evaluasi->getField("KETERANGAN_REKAP")?> </td>
                </tr>  -->
                <?php
                } ?>
                <tr colspan="5" style="display:none">
                    <td >
                        <textarea name="reqPaketRekananUrutArray"><?php print_r(serialize($arrUrutan)); ?></textarea>
                    </td>
                </tr>
            </table>

            <?php
            // $paket_rekanan_cek_lulus = new PaketRekanan();
            // if ($reqBidding == 1) { // e-Reverse Auction
            //   $paket_rekanan_cek_lulus->selectUrutPenawaran(array("A.PAKET_ID" => $reqId, "A.LULUS_PENDAFTARAN" => 1, "A.KIRIM_PENAWARAN" => 1, "A.LULUS_PENAWARAN" => 1), -1, -1, "", "", " ORDER BY NILAI_PENAWARAN ASC, KIRIM_PENAWARAN_TANGGAL ASC ");
            //   if ($paket_rekanan_cek_lulus->countRow() == '2') { true;
            //   } else {
            //     echo '<div class="alert alert-danger mt-1">Jumlah Calon Pemanang '.$paket_rekanan_cek_lulus->countRow().' penyedia, silahkan ubah Sistem Negosiasi menjadi Chatting Nego <a href="'.base_url('main/index/paket_lelang_tambah/?reqId='.$reqId).'" class="'.CLASS_BTN_PRIMARY.' btn-sm"><span class="fa fa-pencil"> Ubah Sistem Negosiasi</span></a></div>';
            //   }
            // } else { // Negosiasi
            //   $paket_rekanan_cek_lulus->selectByParams(array("PAKET_ID" => $reqId, "A.REKANAN_ID" => $reqRekananIdPemenang), -1, -1, " AND A.TANGGAL_DAFTAR IS NOT NULL AND A.LULUS_PENDAFTARAN = 1 AND A.LULUS_KUALIFIKASI = 1 ");
            //   if ($paket_rekanan_cek_lulus->countRow() == '2') {
            //     echo '<div class="alert alert-danger">Jumlah Calon Pemanang '.$paket_rekanan->countRow().' penyedia, silahkan ubah Sistem Negosiasi menjadi Chatting Nego</div>';
            //   } else { }
            // }
           ?>

              <div class="form-actions">
                <input type="hidden" name="reqId" value="<?=$reqId?>" />
                <input type="hidden" name="submitSimpan" value="Simpan" />
                <a href="main/index/paket_detil/?eid=<?=$reqId?>&key=<?=$reqUUID?>" class="<?= CLASS_BTN_DANGER ?> mr-1"> <?= BTN_KEMBALI ?> </a>
                <?php
                if (((int)$this->USER_TYPE_ID == 3 || (int)$this->USER_TYPE_ID == 11) && $paketInfo->user_login_id == $this->USER_LOGIN_ID)
                { // khusus untuk PIC ?>
                  <button type="submit" name="reqSimpan" id="reqSimpan" class="<?= CLASS_BTN_PRIMARY ?> pull-right" style="<?php if ($reqBidding == 1) { echo'display:none'; } ?>"><?= BTN_SIMPAN ?></button>
                <?php
                } else { ?>
                  <button type="submit" name="reqSimpan" id="reqSimpan" class="<?= CLASS_BTN_PRIMARY ?> pull-right" style="display:none"><?= BTN_SIMPAN ?></button>
                <?php 
                } ?>
                <a class="<?= CLASS_BTN_INFO ?>" href="main/loadUrl/report/evaluasi_penawaran_rekapitulasi_excel/?reqId=<?=$reqId?>" target="_blank" ><i class="fa fa-print"></i> Cetak Rekapitulasi</a>
                <?php
                /**
                 * 1:Tender Umum
                 * 2:Pengadaan Langsung
                 * 3:Tender Terbatas
                 * 4:Seleksi Langsung
                 * 5:Penunjukan Langsung
                 * 6:e-Purchasing
                 * 7:Tender Cepat
                 * 8:Kompetisi
                 * 9:Pembelian Langsung Offline
                 * 10:Tender Prakualifikasi
                **/
                if ($reqMetodePengadaan == '1' || $reqMetodePengadaan == '2' || $reqMetodePengadaan == '3' || $reqMetodePengadaan == '5' || $reqMetodePengadaan == '7' || $reqMetodePengadaan == '8' || $reqMetodePengadaan == '10') {
                  if($paketInfo->publish_ba_sampul1 == "1")
                  {}
                  else
                  {
                    if ((int)$this->USER_TYPE_ID == 3 && $paketInfo->user_login_id == $this->USER_LOGIN_ID) {
                 ?>
                  <!-- <a onClick="publishEvaluasi();" id="btnPublish" class="<?php // echo CLASS_BTN_SUCCESS ?>"><?php // echo BTN_PUBLISH ?> Hasil Evaluasi</a> -->
                <?php
                    }
                  }
                } ?>
              </div>
            </div>
          </form>
        </div>

      </div>
    </div>
  </div>
</div>

<script type="text/javascript">
<?php
if ((int)$this->USER_TYPE_ID == 3 || (int)$this->USER_TYPE_ID == 11 && $paketInfo->user_login_id == $this->USER_LOGIN_ID)
{ ?>
$(document).ready(function() {
  <?php
  if(count($arrRekanan) > 0) { ?>
    window.onload=function(){
      document.getElementById("reqSimpan").click();
    };
  <?php
  } ?>
});
$('#loading').hide();
<?php
} ?>

function publishEvaluasi()
{
  $.messager.confirm("Konfirmasi","Publish Hasil Evaluasi?",function(r){
    if (r){
      $.get( "paket_json/set_publish_evaluasi/?reqId=<?=$reqId?>", function( data ) {
          if(data == "1")
          {
            $("#btnPublish").css("display", "none");
            alertSuccess2('Publish Hasil Evaluasi berhasil.');
          }
          else
            $.messager.alert('Info', data, 'info');
      });
    }
  });
}
</script>
