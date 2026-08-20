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
$this->load->model("RekananEvaluasiTeknisTawar");
$this->load->model("RekananEvaluasiAdminTawar");
$this->load->model("RekananEvaluasiHargaTawar");
$this->load->model("MatrixEvaluasi");
$this->load->model("PaketEvaluasiValidasi");

$paket_rekanan = new PaketRekanan();
$paket_rekanan_nilai = new PaketRekanan();
$matrix_evaluasi = new MatrixEvaluasi();
$paket_evaluasi_validasi = new PaketEvaluasiValidasi();

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

// $paket_rekanan->selectByParams(array("PAKET_ID" => $reqId), -1, -1, " AND A.TANGGAL_DAFTAR IS NOT NULL AND A.LULUS_PENDAFTARAN = 1 AND A.LULUS_KUALIFIKASI = 1 AND A.KIRIM_PENAWARAN = 1 AND A.KIRIM_PENAWARAN_LENGKAP = '1' ");
//  ikn 20191125
$paket_rekanan->selectByParams2(array("PAKET_ID" => $reqId), -1, -1, " AND A.TANGGAL_DAFTAR IS NOT NULL AND A.LULUS_PENDAFTARAN = 1 AND A.LULUS_KUALIFIKASI = 1 AND A.KIRIM_PENAWARAN = 1  ",'', '  ORDER BY A.LULUS_PENAWARAN_URUT ASC');
// echo "<pre>"; print_r($paket_rekanan); die();
while($paket_rekanan->nextRow())
{
	$arrRekananId[] = $paket_rekanan->getField("REKANAN_ID");
	$arrRekanan[] = $paket_rekanan->getField("REKANAN");
	$arrPaketRekananId[] = $paket_rekanan->getField("PAKET_REKANAN_ID");
	$arrPaketRekananNilai[] = $paket_rekanan->getField("NILAI_PENAWARAN");
	$arrPaketRekananLulus[] = $paket_rekanan->getField("LULUS_PENAWARAN");
}

if (is_array($arrRekanan)) {
	$arrRekananId = $arrRekananId;
	$arrRekanan = $arrRekanan;
	$arrPaketRekananId = $arrPaketRekananId;
	$arrPaketRekananNilai = $arrPaketRekananNilai;
	$arrPaketRekananLulus = $arrPaketRekananLulus;
} else {
	$arrRekananId = array();
	$arrRekanan = array();
	$arrPaketRekananId = array();
	$arrPaketRekananNilai = array();
	$arrPaketRekananLulus = array();
}


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


$paket_evaluasi_validasi->selectByParamsValidasi(array("NIP" => $this->NIP, "A.PAKET_ID" => $reqId));
$paket_evaluasi_validasi->firstRow();

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
        <h4 class="card-title text-white">Hasil Evaluasi Penawaran File 1</h4>
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
                  <td width="30%"> Pekerjaan</td>
                  <td> <?=$reqNamaPaket?> </td>
                </tr>
              <tr>
                <td width="20%"> Jenis Pekerjaan </td>
                <td colspan="2"> <?=$reqJenisPekerjaan?> </td>
              </tr>
              <tr>
                  <td width="20%"> Metode Evaluasi</td>
                  <td> <?=$reqMetodeEvaluasi?> </td>
                </tr>
              <tr>
              </table>
              <table class="table table-bordered table-hover table-responsive">
                <tr>
                  <th width="5%">No.</th>
                  <th>Nama Peserta</th>
                  <th width="15%" style="text-align: center">Evaluasi Administrasi</th>
                  <th width="15%" style="text-align: center">Evaluasi Teknis</th>
                  <?php
                  if ($reqMetodeEvaluasiId == '2') { ?>
                  <th width="15%" style="text-align: center">Nilai Teknis</th>
                  <?php
                  } ?>
                  <th width="15%" style="text-align: center">Hasil Evaluasi</th>
                </tr>
                <?php
                $no=1;
                for($i=0;$i<count($arrRekanan);$i++)
                {
                  $rekanan_evaluasi_admin = new RekananEvaluasiAdminTawar();
                  $rekanan_evaluasi_admin->selectMemenuhiSyarat($reqId, $arrPaketRekananId[$i]);
                  $rekanan_evaluasi_admin->firstRow();
                  // if($rekanan_evaluasi_admin->getField("JUMLAH_EVALUASI") <= $rekanan_evaluasi_admin->getField("JUMLAH_DILENGKAPI"))
                  //  ikn 20191125
                  // if($rekanan_evaluasi_admin->getField("JUMLAH_DILENGKAPI") >= 1)
                  // {
                  //   $status_admin = '<img src="images/centang.png">';
                  //   $arrEvaluasiAdmin[$i] = 1;
                  // }
                  // else
                  // {
                  //   $status_admin = '<img src="images/uncentang.png">';
                  //   $arrEvaluasiAdmin[$i] = 0;
                  // }

									//  ikn 20220310
									if($rekanan_evaluasi_admin->getField("JUMLAH_DILENGKAPI") >= 1)
									{
										$status_admin = '<img class="text-center" src="images/centang-cetak.png">';
										$keterangan_admin = $rekanan_evaluasi_admin->getField("KETERANGAN_LULUS");
										$arrEvaluasiAdmin[$i] = 1;
									}
									else
									{
										$status_admin = '<img class="text-center" src="images/uncentang-cetak.png">';
										$keterangan_admin = $rekanan_evaluasi_admin->getField("KETERANGAN_GAGAL");
										$arrEvaluasiAdmin[$i] = 0;
									}

                  $rekanan_evaluasi_teknis = new RekananEvaluasiTeknisTawar();
                  $rekanan_evaluasi_teknis->selectMemenuhiSyarat($reqId, $arrPaketRekananId[$i]);
                  $rekanan_evaluasi_teknis->firstRow();
                  // if($rekanan_evaluasi_teknis->getField("JUMLAH_EVALUASI") <= $rekanan_evaluasi_teknis->getField("JUMLAH_DILENGKAPI"))
                  //  ikn 20191125
                  // if($rekanan_evaluasi_teknis->getField("JUMLAH_DILENGKAPI") >= 1)
                  // {
                  //   $status_teknis = '<img src="images/centang.png">';
                  //   $arrEvaluasiTeknis[$i] = 1;
                  // }
                  // else
                  // {
                  //   $status_teknis = '<img src="images/uncentang.png">';
                  //   $arrEvaluasiTeknis[$i] = 0;
                  // }

									//  ikn 20220310
									if($rekanan_evaluasi_admin->getField("JUMLAH_DILENGKAPI") >= 1 && $rekanan_evaluasi_teknis->getField("JUMLAH_DILENGKAPI") >= 1)
									{
										$status_teknis = '<img class="text-center" src="images/centang-cetak.png">';
										if ($reqMetodeEvaluasiId == '2') {
											$keterangan_teknis = 'Nilai Teknis <b>'.$rekanan_evaluasi_teknis->getField("NILAI_TEKNIS").'</b>';
										} else {
											$keterangan_teknis = $rekanan_evaluasi_teknis->getField("KETERANGAN_LULUS");
										}
										$arrEvaluasiTeknis[$i] = 1;
									}
									else
									{
										$status_teknis = '<img class="text-center" src="images/uncentang-cetak.png">';
										$keterangan_teknis = $rekanan_evaluasi_teknis->getField("KETERANGAN_GAGAL");
										$arrEvaluasiTeknis[$i] = 0;
									}

                  if((int)$reqOwnerEstimate == 0)
                    $nilai = 0;
                  else
                    $nilai = round(((int)$arrPaketRekananNilai[$i] / (int)$reqOwnerEstimate) * 100,2);
                  if($arrEvaluasiAdmin[$i] == 0 || $arrEvaluasiTeknis[$i] == 0)
                  {
                    $evaluasi = 0;
                    $hasil = "Tidak Memenuhi Syarat";
                  }
                  else
                  {
                    $evaluasi = 1;
                    $hasil = "Memenuhi Syarat";
                  }

                  // bold akun login
                  if ($arrRekananId[$i] == $this->ID) {
                    $bold = 'style="font-weight:bold"';
                  } else {
                    $bold = '';
                  }
                ?>
                  <tr>
                    <td><?= $no ?>.</td>
                    <td <?= $bold ?>><?=$arrRekanan[$i]?></td>
										<td class="text-center">
											<strong><?=$status_admin.'<br><small>'.$keterangan_admin.'</small>'?></strong>
										</td>
                    <td class="text-center">
                      <strong><?=$status_teknis.'<br><small>'.$keterangan_teknis.'</small>'?></strong>
                    </td>

                    <?php
                    if ($reqMetodeEvaluasiId == '2') { ?>
                    <td <?= $bold ?> class="text-center"><?=$keterangan_teknis?></td>
                    <?php
                    } ?>
                    <td <?= $bold ?> class="text-center"> <?=$hasil?></td>
                  </tr>

                <?php
                $no++;
                }
                unset($rekanan_evaluasi_admin);
                unset($rekanan_evaluasi_teknis);
                ?>
              </table>

              <div class="form-actions">
                <a href="main/index/paket_detil/?eid=<?=$reqId?>&key=<?=$reqUUID?>" class="<?= CLASS_BTN_DANGER ?>"> <?= BTN_KEMBALI ?> </a>
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
