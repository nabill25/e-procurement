<?php
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 * 
 */

$reqId = $this->input->get("reqId");
$this->libsession->cekSessionKualifikasi($reqId);

$this->load->library("paketinfo"); $paketInfo = new paketinfo();
include_once("functions/string.func.php");
include_once("functions/default.func.php");
include_once("functions/date.func.php");
$this->load->model("Paket");
$this->load->model("PaketTahap");
$this->load->model("PaketRekanan");
$this->load->model("RekananPaketPenawaran");
$this->load->model("PaketEvaluasiKualifikasi");
$this->load->model("MatrixEvaluasi");
$this->load->model("PaketEvaluasiValidasi");
$this->load->model("RekananEvaluasiKualifikasiTawar");

$paket_tahap = new PaketTahap();
$paket_tahap_metode = new PaketTahap();
$paket_rekanan = new PaketRekanan();
$paket_rekanan_nilai = new PaketRekanan();
$matrix_evaluasi = new MatrixEvaluasi();
$paket_evaluasi_kualifikasi = new PaketEvaluasiKualifikasi();
$paketInfo->getPaket($reqId);
$reqNamaPaket = $paketInfo->nama;
$reqJenisPekerjaan = $paketInfo->jenis;
$reqJenisPekerjaanId = $paketInfo->jenis_id;
$reqMetodeEvaluasi = $paketInfo->metode_evaluasi;
$reqMetodeEvaluasiId = $paketInfo->metode_evaluasi_id;
$reqOwnerEstimate  = $paketInfo->nilai_owner_estimate;
$reqRekananIdPemenang = $paketInfo->rekanan_id_pemenang;
$reqMetodeLelang = $paketInfo->metode_lelang_id;
$reqUUID = $paketInfo->uuid;

$i = 0;

$paket_rekanan_nilai->selectNilaiPenawaran(array("PAKET_ID" => $reqId));
while($paket_rekanan_nilai->nextRow())
{
	$arrUrutan[] = $paket_rekanan_nilai->getField("PAKET_REKANAN_ID");
}

function getUrutan($reqPaketRekananId, $arrUrutan)
{
	$key = array_search($reqPaketRekananId, $arrUrutan);
	return $key + 1;
}

$matrix_evaluasi->selectByParams(array("A.PAKET_JENIS_ID" => $reqJenisPekerjaanId, "A.PAKET_METODE_EVALUASI_ID" => $reqMetodeEvaluasiId));
$matrix_evaluasi->firstRow();

$paket_evaluasi_kualifikasi->selectByParams(array("PAKET_ID" => $reqId));
$paket_rekanan->selectByParams2(array("PAKET_ID" => $reqId), -1, -1, " AND A.TANGGAL_DAFTAR IS NOT NULL ");
while($paket_rekanan->nextRow())
{
  $arrRekananId[] = $paket_rekanan->getField("REKANAN_ID");
  $arrRekanan[] = $paket_rekanan->getField("REKANAN");
  $arrPaketRekananId[] = $paket_rekanan->getField("PAKET_REKANAN_ID");
  $arrPaketRekananNilai[] = $paket_rekanan->getField("NILAI_PENAWARAN");
  $arrPasswordDokumen[] = $paket_rekanan->getField("KIRIM_PENAWARAN_PASSWORD");
}
// echo "<pre>"; print_r($arrRekananId); die();
if (is_array($arrRekananId)) {
  $arrRekananId = $arrRekananId;
  $arrRekanan = $arrRekanan;
  $arrPaketRekananId = $arrPaketRekananId;
  $arrPaketRekananNilai = $arrPaketRekananNilai;
  $arrPasswordDokumen = $arrPasswordDokumen;
} else {
  $arrRekananId = array();
  $arrRekanan = array();
  $arrPaketRekananId = array();
  $arrPaketRekananNilai = array();
  $arrPasswordDokumen = array();
}

$jenis_tahap = $paket_tahap_metode->getJenisTahapById($reqId);
$arrPembukaanAuction            = PENGUMUMAN_KUALIFIKASI;

if($paket_tahap->getCountByParams(array("URUT" => $arrPembukaanAuction[$jenis_tahap], "PAKET_ID" => $reqId), " AND NOW() >= TANGGAL_AWAL ") > 0) {
  $allowPassword = 1;
}
else
{
  $allowPassword = 0;
}

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
        <h4 class="card-title text-white">Hasil Evaluasi Kualifikasi</h4>
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

  		    <form id="ff" class="easyui-form form-horizontal" method="post" data-options="novalidate:true" enctype="multipart/form-data">
            <div>
              <table class="table table-bordered table-hover">
                <tr>
                  <td width="15%"> Pekerjaan</td>
                  <td> <?=$reqNamaPaket?> </td>
                </tr>
                <tr>
                  <td width="15%"> Jenis Pekerjaan </td>
                  <td colspan="2"> <?=$reqJenisPekerjaan?> </td>
                </tr>
                <tr>
                  <td width="15%"> Metode Evaluasi</td>
                  <td> <?=$reqMetodeEvaluasi?> </td>
                </tr>
              <tr>
              </table>
              <table class="table table-bordered table-hover table-responsive">
                <tr>
                  <th width="5%">No.</th>
                  <th >Nama Peserta</th>
                  <th width="21%" style="text-align: center">Evaluasi Kualifikasi</th> 
                </tr> 
                <?php 
                $no=1;
                for($i=0;$i<count($arrRekanan);$i++)
                { 
                  $rekanan_evaluasi_kualifikasi = new RekananEvaluasiKualifikasiTawar();
                  $rekanan_evaluasi_kualifikasi->selectByParams(array("PAKET_REKANAN_ID" => $arrPaketRekananId[$i]));
                  $rekanan_evaluasi_kualifikasi->firstRow();
                  $status = $rekanan_evaluasi_kualifikasi->getField("MEMENUHI_SYARAT");
                  $uraian = $rekanan_evaluasi_kualifikasi->getField("URAIAN");
                  $keterangan = $rekanan_evaluasi_kualifikasi->getField("KETERANGAN");

                  if($status == '1')
                  {
                    $status_kualifikasi = '<img class="text-center" src="images/centang-cetak.png">';
                    $keterangan_kualifikasi = $keterangan;
                  }
                  else
                  {
                    $status_kualifikasi = '<img class="text-center" src="images/uncentang-cetak.png">';
                    $keterangan_kualifikasi = $uraian;
                  }
                ?>
                 <tr>
                      <td widtd="10px"><?= $no ?></td>
                      <td> <?= $arrRekanan[$i]; ?> </td> 
                      <td class="text-center">
                        <strong><?=$status_kualifikasi.'<br><small>'.$keterangan_kualifikasi.'</small>';?></strong>
                      </td>
                    </tr>
                <?php 
                $no++;} ?>
              </table>

              <div class="form-actions">
                <a href="main/index/paket_detil/?eid=<?=$reqId?>&key=<?=$reqUUID?>" class="<?= CLASS_BTN_DANGER ?>"> <i class="fa fa-arrow-left"></i> Kembali </a>
              </div>
            </div>
          </form>

        </div>
      </div>
    </div>
  </div>
</div>



   </div>
  </div>
</div>
