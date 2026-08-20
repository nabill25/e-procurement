<?php
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 * 
 */
$this->load->library("paketinfo"); $paketInfo = new paketinfo();
$this->load->model("PaketKriteriaEvaluasi");
$this->load->model("PaketEvaluasiAdmin");
$this->load->model("PaketEvaluasiPersonil");
$this->load->model("PaketEvaluasiSertifikatLain");
$this->load->model("PaketEvaluasiPengalaman");
$this->load->model("PaketEvaluasiPeralatan");
$this->load->model("PaketRekanan");
$this->load->model("RekananEvaluasiAdmin");
$this->load->model("RekananEvaluasiKeuangan");
$this->load->model("RekananEvaluasiPengalaman");
$this->load->model("RekananEvaluasiPersonil");
$this->load->model("RekananEvaluasiPeralatan");
$this->load->model("RekananEvaluasiSertifikatLain");
$this->load->model("PaketRekananKualifikasi");
include_once("functions/string.func.php");
include_once("functions/date.func.php");
include_once("lib/phpqrcode/qrlib.php");

header("Content-type: application/vnd-ms-excel");
header("Content-Disposition: attachment; filename=evaluasi_kualifikasi_rekapitulasi.xls");

$paket_kriteria_evaluasi = new PaketKriteriaEvaluasi();
$paket_rekanan = new PaketRekanan();

set_time_limit(300);
ini_set("memory_limit","500M");
ini_set('max_execution_time', 520);

$reqId = $this->input->get("reqId");

$paketInfo->getPaket($reqId);
$paket_rekanan->selectByParams(array("PAKET_ID" => $reqId), -1, -1, " AND A.TANGGAL_DAFTAR IS NOT NULL AND A.LULUS_PENDAFTARAN = 1 ");
while($paket_rekanan->nextRow())
{
	$arrRekanan[] = $paket_rekanan->getField("REKANAN");
	$arrPaketRekananId[] = $paket_rekanan->getField("PAKET_REKANAN_ID");
	$arrPaketRekananNilai[] = $paket_rekanan->getField("NILAI_PENAWARAN");
	$arrPaketRekananLulusAdmin[] = $paket_rekanan->getField("LULUS_ADMINISTRASI");
	$arrPaketRekananLulusKualifikasi[] = $paket_rekanan->getField("LULUS_KUALIFIKASI");
	$arrPaketRekananKeteranganLulus[] = $paket_rekanan->getField("LULUS_KUALIFIKASI_KETERANGAN");
}

$paket_kriteria_evaluasi->selectByParams(array("PAKET_ID" => $reqId));
$paket_kriteria_evaluasi->firstRow();

?>
<!doctype html>
<html>
	<head>
		<meta charset="utf-8">
		<base href="<?=base_url()?>" />
	</head>
	<body>
    <div class="kop-laporan">
            <div class="info">
                
            </div>
        </div>
        <div class="isi">
        </div>
        <br>
        <div class="data-laporan">
        <table class="table table-bordered table-hover"> 
            <thead>
              <tr>
                <th rowspan="2">No</th>
                <th rowspan="2">Nama Perusahaan</th>
                <th colspan="7">Nilai</th>
                <th rowspan="2"> Nilai </th>
                <th rowspan="2">Lulus </th>
                <th rowspan="2">Keterangan </th>
              </tr>
              <tr>
                <th>Administrasi</th>
                <th>SKK</th>
                <th>Rekening Koran</th>
                <th>Pengalaman</th>
                <th>Personil</th>
                <th>Peralatan</th>
                <th>Sertifikat</th>
              </tr>
            </thead> 
            <?php
              for($i=0;$i<count($arrRekanan);$i++)
              { 
                $paket_rekanan_kualifikasi1 = new PaketRekananKualifikasi();
                $paket_rekanan_kualifikasi1->selectByParams(array("A.PAKET_REKANAN_ID" => $arrPaketRekananId[$i], "A.KODE" => "EVALUASI_ADMIN"));
                $paket_rekanan_kualifikasi1->firstRow();
                $reqNilaiAdm    = $paket_rekanan_kualifikasi1->getField("NILAI");
                $reqCatatanAdm  = $paket_rekanan_kualifikasi1->getField("CATATAN");
                $reqStatusAdm   = $paket_rekanan_kualifikasi1->getField("STATUS");

                $paket_rekanan_kualifikasi2 = new PaketRekananKualifikasi();
                $paket_rekanan_kualifikasi2->selectByParams(array("A.PAKET_REKANAN_ID" => $arrPaketRekananId[$i], "A.KODE" => "SKK"));
                $paket_rekanan_kualifikasi2->firstRow();
                $reqNilaiSKK = $paket_rekanan_kualifikasi2->getField("NILAI");
                $reqCatatanKeu = $paket_rekanan_kualifikasi2->getField("CATATAN");
                $reqStatusKeu = $paket_rekanan_kualifikasi2->getField("STATUS");
                
                $paket_rekanan_kualifikasi3 = new PaketRekananKualifikasi();
                $paket_rekanan_kualifikasi3->selectByParams(array("A.PAKET_REKANAN_ID" => $arrPaketRekananId[$i], "A.KODE" => "REKENING_KORAN"));
                $paket_rekanan_kualifikasi3->firstRow();
                $reqNilaiRekeningKoran = $paket_rekanan_kualifikasi3->getField("NILAI");

                $paket_rekanan_kualifikasi4 = new PaketRekananKualifikasi();
                $paket_rekanan_kualifikasi4->selectByParams(array("A.PAKET_REKANAN_ID" => $arrPaketRekananId[$i], "A.KODE" => "PENGALAMAN"));
                $paket_rekanan_kualifikasi4->firstRow();
                $reqNilaiPengalaman = $paket_rekanan_kualifikasi4->getField("NILAI");
                $reqCatatanTek = $paket_rekanan_kualifikasi4->getField("CATATAN");
                $reqStatusTek = $paket_rekanan_kualifikasi4->getField("STATUS");

                $paket_rekanan_kualifikasi5 = new PaketRekananKualifikasi();
                $paket_rekanan_kualifikasi5->selectByParams(array("A.PAKET_REKANAN_ID" => $arrPaketRekananId[$i], "A.KODE" => "PERSONIL"));
                $paket_rekanan_kualifikasi5->firstRow();
                $reqNilaiPersonil = $paket_rekanan_kualifikasi5->getField("NILAI");

                $paket_rekanan_kualifikasi6 = new PaketRekananKualifikasi();
                $paket_rekanan_kualifikasi6->selectByParams(array("A.PAKET_REKANAN_ID" => $arrPaketRekananId[$i], "A.KODE" => "PERALATAN"));
                $paket_rekanan_kualifikasi6->firstRow();
                $reqNilaiPeralatan = $paket_rekanan_kualifikasi6->getField("NILAI");

                $paket_rekanan_kualifikasi7 = new PaketRekananKualifikasi();
                $paket_rekanan_kualifikasi7->selectByParams(array("A.PAKET_REKANAN_ID" => $arrPaketRekananId[$i], "A.KODE" => "SERTIFIKAT"));
                $paket_rekanan_kualifikasi7->firstRow();
                $reqNilaiSertifikat = $paket_rekanan_kualifikasi7->getField("NILAI");

                if ($reqStatusAdm == '1') { 
                  $reqNilaiAdm = $reqNilaiAdm; 
                } else {
                  $reqNilaiAdm = 0;
                } 
                if ($reqStatusKeu == '1') { 
                  $reqNilaiSKK = $reqNilaiSKK; 
                } else {
                  $reqNilaiSKK = 0;
                }  
                if ($reqStatusKeu == '1') { 
                  $reqNilaiRekeningKoran = $reqNilaiRekeningKoran; 
                } else {
                  $reqNilaiRekeningKoran = 0;
                }  
                if ($reqStatusTek == '1') { 
                  $reqNilaiPengalaman = $reqNilaiPengalaman; 
                } else {
                  $reqNilaiPengalaman = 0;
                }  
                if ($reqStatusTek == '1') { 
                  $reqNilaiPersonil = $reqNilaiPersonil; 
                } else {
                  $reqNilaiPersonil = 0;
                }  
                if ($reqStatusTek == '1') { 
                  $reqNilaiPeralatan = $reqNilaiPeralatan; 
                } else {
                  $reqNilaiPeralatan = 0;
                }  
                if ($reqStatusTek == '1') { 
                  $reqNilaiSertifikat = $reqNilaiSertifikat; 
                } else {
                  $reqNilaiSertifikat = 0;
                }  

                $totalNilai = round(($reqNilaiAdm + $reqNilaiSKK + $reqNilaiRekeningKoran + $reqNilaiPengalaman + $reqNilaiPersonil + $reqNilaiPeralatan + $reqNilaiSertifikat) /7);
              ?>
            <tr class="terang">
              <td valign="top" style="text-align: center"><?=$i+1?></td>
              <td valign="top">
                <?=$arrRekanan[$i]?> 
              </td>
              <td valign="top" style="text-align: center">
                <?php 
                if ($reqStatusAdm == '1') { 
                  echo $reqNilaiAdm;
                } else {
                  echo '<i class="fa fa-times" aria-hidden="true"></i><br><small>'.$reqCatatanAdm.'</small>';
                } ?>
              </td>
              <td valign="top" style="text-align: center">
                <?php 
                if ($reqStatusKeu == '1') { 
                  echo $reqNilaiSKK;
                } else {
                  echo '<i class="fa fa-times" aria-hidden="true"></i><br><small>'.$reqCatatanKeu.'</small>';
                } ?>
              </td>
              <td valign="top" style="text-align: center">
                <?php 
                if ($reqStatusKeu == '1') { 
                  echo $reqNilaiRekeningKoran;
                } else {
                  echo '<i class="fa fa-times" aria-hidden="true"></i><br><small>'.$reqCatatanKeu.'</small>';
                } ?>
              </td>
              <td valign="top" style="text-align: center">
                <?php 
                if ($reqStatusTek == '1') { 
                  echo $reqNilaiPengalaman;
                } else {
                  echo '<i class="fa fa-times" aria-hidden="true"></i><br><small>'.$reqCatatanTek.'</small>';
                } ?>
              </td>
              <td valign="top" style="text-align: center">
                <?php 
                if ($reqStatusTek == '1') { 
                  echo $reqNilaiPersonil;
                } else {
                  echo '<i class="fa fa-times" aria-hidden="true"></i><br><small>'.$reqCatatanTek.'</small>';
                } ?>
              </td>
              <td valign="top" style="text-align: center">
                <?php 
                if ($reqStatusTek == '1') { 
                  echo $reqNilaiPeralatan;
                } else {
                  echo '<i class="fa fa-times" aria-hidden="true"></i><br><small>'.$reqCatatanTek.'</small>';
                } ?>
              </td> 
              <td valign="top" style="text-align: center">
                <?php 
                if ($reqStatusTek == '1') { 
                  echo $reqNilaiSertifikat;
                } else {
                  echo '<i class="fa fa-times" aria-hidden="true"></i><br><small>'.$reqCatatanTek.'</small>';
                } ?>
              </td>
              <td style="text-align: left">
                <?php 
                echo $totalNilai ?>
              </td> 
              <td valign="top"> 
                <?php 
                if ($arrPaketRekananLulusKualifikasi[$i] == 1) { ?>
                Lulus
                <?php 
                } else { ?> 
                Tidak
                <?php 
                } ?>
              </td>
              <td>
                 <?=$arrPaketRekananKeteranganLulus[$i]?> 
             </td>
            </tr>
              <?php
                unset($paket_rekanan_kualifikasi);
              }
              ?>
          </table> 
        </div>
	</body>
</html>