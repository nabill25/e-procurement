<?php
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 *
 */
$this->load->library("kauth");  $userLogin = new kauth();
$this->load->model("Paket");
$this->load->model("PaketTahap");
$this->load->model("PaketDokumen");
$this->load->model("Paketpemenang");
$this->load->model("RekananEvaluasiAdmin");
$this->load->model("RekananEvaluasiAdminTawar");
$this->load->model("RekananEvaluasiTeknisTawar");
$this->load->model("RekananEvaluasiHargaTawar");
$this->load->model("PaketRekanan");
$this->load->model("PaketPanitia");
$this->load->model("PaketRekananDaftar");
$this->load->model("PaketPihakLain");
$this->load->model("PermohonanPaket");

include_once("functions/string.func.php");
include_once("functions/date.func.php");
include_once("functions/default.func.php");

/* create objects */
$paket = new Paket();
$paket_keterangan = new Paket();
$paket_tahap_jadwal = new PaketTahap();
$paket_tahap = new PaketTahap();
$paket_tahap_metode = new PaketTahap();
$paket_rekanan = new PaketRekanan();
$paket_rekanan_lulus_penawaran = new PaketRekanan();
$paket_pihak_lain = new PaketPihakLain();
$rekanan_evaluasi_admin = new RekananEvaluasiAdmin();
$rekanan_evaluasi_admin_tawar = new RekananEvaluasiAdminTawar();
$rekanan_evaluasi_teknis_tawar = new RekananEvaluasiTeknisTawar();
$rekanan_evaluasi_harga_tawar = new RekananEvaluasiHargaTawar();
$paket_dokumen = new PaketDokumen();
$paket_panitia = new PaketPanitia();
$permohonan_paket = new PermohonanPaket();

/* VARIABLES */
$reqId = httpFilterRequest("eid");
$reqKey = httpFilterRequest("key");

$reqMode = '';
if($reqMode == "reset")
{
	$paket->setField("FIELD", "ALASAN");
	$paket->setField("FIELD_VALUE", "''");
	$paket->setField("PAKET_ID", $reqId);
	$paket->updateByField();
}

$paket->selectByParamsMonitoring2(array(), -1, -1, " AND PAKET_ID = '".$reqId."' ");
$paket->firstRow();
if ($paket->getField("PUBLISH_PAKET") == 0 && ($this->USER_TYPE_ID == '6' || $this->USER_TYPE_ID == '')) { // khusus PENYEDIA di cek
  // echo "Maaf, paket tidak tersedia";
	// exit();
	redirect(base_url());
}
// 29.08.2025 cek key UUID
if ($paket->getField("PAKET_UUID") == '' || $reqKey == '' || $paket->getField("PAKET_UUID") != $reqKey) {
	redirect(base_url());
}

//echo $paket->query;exit;
$pra_kualifikasi_cek = $paket->getField("PAKET_METODE_KUALIFIKASI_ID"); // 1 File atau 2 File
$metode_evaluasi_cek = $paket->getField("PAKET_METODE_EVALUASI_ID"); // 2-Sistem Nilai, 7-Sistem Harga Terendah
$paket_jenis_cek = $paket->getField("PAKET_JENIS_ID"); // 1-PK, 2-JASKON, 3-B, 4-JL

$paket_user_id = $paket->getField("USER_LOGIN_ID");
$alasan = $paket->getField("ALASAN");
$alasan_ulang = $paket->getField("ALASAN_ULANG");
$multi_pemenang = $paket->getField("MULTI_PEMENANG");
$multi_bidang_usaha = $paket->getField("MULTI_BIDANG_USAHA");
$ppk = $paket->getField("PPK");
// 1:Tender, 2:Pengadaan langsung, 3:Tender Terbatas, 4:Seleksi Langsung, 5:Penunjukan Langsung, 6:e-Purchasing, 7:Tender Cepat, 8:Kompetisi, 9:Pembelian Langsung Offline, 10:Tender Kualifikasi, 11:Penunjukan Langsung Khusus
$paket_metode_lelang_id = $paket->getField("PAKET_METODE_LELANG_ID");
if (($paket_metode_lelang_id != '1' && $paket_metode_lelang_id != '3' && $paket_metode_lelang_id != '4' && $paket_metode_lelang_id != '7' && $paket_metode_lelang_id != '10' ) && $this->USER_TYPE_ID == "") { // selain Tender harus login
	redirect(base_url());
} else {
	if ($paket_metode_lelang_id != '1')
	{
		// 1:administrator, 3:Panitia, 6:Penyedia, 9:Perencana, 10:Audit, 11:Pembeli
		switch ($paket_metode_lelang_id) {
			case '2': // Pengadaan Langsung
			case '5': // Penunjukan Langsung
			case '8': // Kompetisi
				if ($this->USER_TYPE_ID != '1' && $this->USER_TYPE_ID != '3' && $this->USER_TYPE_ID != '6' && $this->USER_TYPE_ID != '9' && $this->USER_TYPE_ID != '10' && $this->USER_TYPE_ID != '11' && $this->USER_TYPE_ID != '27' && $this->USER_TYPE_ID != '28' && $this->USER_TYPE_ID != '20')
				{
					redirect(base_url());
				}
				break;

				case '6': // Purchasing
				case '9': // Pembelian Langsung Offline
					if ($this->USER_TYPE_ID != '1' && $this->USER_TYPE_ID != '3' && $this->USER_TYPE_ID != '9' && $this->USER_TYPE_ID != '6' && $this->USER_TYPE_ID != '10' && $this->USER_TYPE_ID != '11' && $this->USER_TYPE_ID != '27' && $this->USER_TYPE_ID != '28' && $this->USER_TYPE_ID != '20')
					{
						$this->load->model("UserLogin");
						$user_login_jabatan = new UserLogin();
						$user_login_jabatan->selectByParams(array("USER_LOGIN_ID" => $this->USER_LOGIN_ID));
						$user_login_jabatan->firstRow();
						if ($user_login_jabatan->getField('PENUNJUK_PIC') != '1') {
							redirect(base_url());
						}
					}
					break;

			default:
			// redirect(base_url());
				break;
		}
	}
}

// echo $paket_metode_lelang_id; die;
$paket_metode_nama = $paket->getField("METODE_LELANG");
$rekanan_id_pemenang_negosiasi = $paket->getField("REKANAN_ID_PEMENANG");
$publish_ba_penawaran = $paket->getField("PUBLISH_BA_PENAWARAN");
$publish_ba_kualifikasi = $paket->getField("PUBLISH_BA_KUALIFIKASI");
$sistem_sampul = $paket->getField("SISTEM_SAMPUL");
$publish_ba_evaluasi_sampul1 = $paket->getField("PUBLISH_BA_EVALSAMPUL1");
$publish_ba_evaluasi_sampul2 = $paket->getField("PUBLISH_BA_EVALSAMPUL2");
$publish_ba_penawaran_sampul2 = $paket->getField("PUBLISH_BA_PENAWARAN2");
$publish_eval_kualifikasi = $paket->getField("PUBLISH_EVALKUALIFIKASI");
$bidding = $paket->getField("BIDDING");
$reqPermohonanId = $paket->getField("PERMOHONAN_PAKET_ID");
$reqMetodePengadaan = $paket->getField("PAKET_METODE_LELANG_ID");
// echo $reqPermohonanId;
if ($reqPermohonanId) {
  $permohonan_paket = new PermohonanPaket();
  // $permohonan_paket->selectByParams(array("A.PERMOHONAN_PAKET_ID" => $reqPermohonanId, "A.UNIT_KERJA_ID" => $this->UNIT_KERJA_ID));
  $permohonan_paket->selectByParams(array("A.PERMOHONAN_PAKET_ID" => $reqPermohonanId));
  $permohonan_paket->firstRow();
  $reqPL = $permohonan_paket->getField("PENGADAANLANGSUNG");
}

// cek pemenang
$getpaket_pemenang = new Paketpemenang();
$getpaket_pemenang->selectByParams(array("PAKET_ID" => $reqId), -1, -1);
$getpaket_pemenang->firstRow();
$cekPublishPemenang = $getpaket_pemenang->getField("PUBLISH");
if ($bidding == '1') { // Auction
	$rekanan_id_pemenang = ($getpaket_pemenang->getField("PAKET_PEMENANG_ID")) ? $getpaket_pemenang->getField("REKANAN_ID") : $getpaket_pemenang->getField("REKANAN_ID");
} else { // Negosiasi
	$rekanan_id_pemenang = ($getpaket_pemenang->getField("PAKET_PEMENANG_ID")) ? $getpaket_pemenang->getField("REKANAN_ID") : $rekanan_id_pemenang_negosiasi;
}

$paket_tahap_jadwal->selectByParamsJadwal(array("TAMPILKAN" => "1"), -1, -1, " AND PAKET_ID = '".$reqId."' ");
//echo $paket_tahap_jadwal->query;exit;

$jenis_tahap = $paket_tahap_metode->getJenisTahapById($reqId);
// echo $jenis_tahap; die();

//cuman keterangan, yang dibawah ini adalah array urutan tahap, keynya = metode paket, valuenya= urutan tahap
$arrDokumenLelang               = DOKUMEN_LELANG;
$arrEvaluasiKualifikasi         = EVALUASI_KUALIFIKASI;
$arrEvaluasiKualifikasi1        = EVALUASI_KUALIFIKASI1;
$arrAanwijzing                  = AANWIJZING;
$arrDokumenPenawaran 	 	 		    = DOKUMEN_PENAWARAN; // ikn 2019.08 tender cepat
$arrDokumenPenawaran1 	 	 	 	  = DOKUMEN_PENAWARAN1; // ikn 2019.08 tender cepat
$arrUploadPasswordPenawaran	 		= UPLOAD_PASSWORD_PENAWARAN;
$arrPembukaanAuction	 			    = PEMBUKAAN_AUCTION;
$arrEvaluasiPenawaran	 			    = EVALUASI_PANAWARAN;
$arrNegosiasi			 		  	      = NEGOSIASI;
$arrPengumumanPemenang	 	 		  = PENGUMUMAN_PEMENANG;
$arrUploadPasswordPenawaranSampul2  = UPLOAD_PASSWORD_PENAWARAN_SAMPUL2;
$arrPembukaanAuctionSampul2	 	 	= PEMBUKAAN_AUCTION_SAMPUL2;
$arrEvaluasiPenawaranSampul2    = EVALUASI_PANAWARAN_SAMPUL2;
$arrSanggah                     = MASA_SANGGAH;
$arrPeringkat	 	                = PERINGKAT;

function ind_long_time($timestamp)
{
	if( ! empty($timestamp))
	{
		$timestamp = strtotime($timestamp);

		return date('H', $timestamp).':'.date('i', $timestamp).' WIB';
	}
	else
		return FALSE;
}

$reqPaketRekananId = '';
if ($this->REKANAN_ID) {
	$paket_rekanan->selectByParamsPaketLelangV2(array("A.PAKET_ID" => $reqId, "A.REKANAN_ID" => $this->REKANAN_ID), -1, -1, " AND TANGGAL_DAFTAR IS NOT NULL ");
	$paket_rekanan->firstRow();
	$reqPaketRekananId = $paket_rekanan->getField("PAKET_REKANAN_ID");
}

if ($paket_metode_lelang_id != '1' && $paket_metode_lelang_id != '3' && $paket_metode_lelang_id != '4' && $paket_metode_lelang_id != '7'  && $paket_metode_lelang_id != '10' && $reqPaketRekananId == '' && $this->USER_TYPE_ID == '6') { // Selain Tender hanya Penyedia yang sudah terundang yang bisa melihat
	redirect(base_url());
}
$reqTanggalDaftar = $paket_rekanan->getField("TANGGAL_DAFTAR");
$reqKodeRekanan = $paket_rekanan->getField("KODE_REKANAN");
$reqAanwijzing = $paket_rekanan->getField("AANWIJZING");
$reqLulusKualifikasi = $paket_rekanan->getField("LULUS_KUALIFIKASI");
$reqLulusKualifikasiKeterangan = $paket_rekanan->getField("LULUS_KUALIFIKASI_KETERANGAN");
$reqLulusPendaftaran = $paket_rekanan->getField("LULUS_PENDAFTARAN");
$reqLulusPendaftaranKeterangan = $paket_rekanan->getField("LULUS_PENDAFTARAN_KETERANGAN");
$reqKirimPenawaran = $paket_rekanan->getField("KIRIM_PENAWARAN");
$reqKirimPenawaranPassword = $paket_rekanan->getField("KIRIM_PENAWARAN_PASSWORD");
$reqKirimPenawaranPassword2 = $paket_rekanan->getField("KIRIM_PENAWARAN_PASSWORD2");

$reqKirimPenawaranKelengkapan = $paket_rekanan->getField("KIRIM_PENAWARAN_LENGKAP");
$reqKirimPenawaranKelengkapanAlasan = $paket_rekanan->getField("KIRIM_PENAWARAN_ALASAN");
$reqKirimPenawaranKelengkapanSampul2 = $paket_rekanan->getField("KIRIM_PENAWARAN_LENGKAP2");
$reqKirimPenawaranKelengkapanAlasanSampul2 = $paket_rekanan->getField("KIRIM_PENAWARAN_ALASAN2");
$reqLulusPenawaranSampul1 = $paket_rekanan->getField("LULUS_PENAWARAN_SAMPUL1");

$status_aanwitzing = $paket_tahap->getCountByParams(array("URUT" => $arrAanwijzing[$jenis_tahap], "PAKET_ID" => $reqId, "TAMPILKAN" => 1));
$status_dok_kualifikasi1 = $paket_tahap->getCountByParams(array("URUT" => $arrEvaluasiKualifikasi[$jenis_tahap], "PAKET_ID" => $reqId, "TAMPILKAN" => 1));
$status_dok_kualifikasi2 = $paket_tahap->getCountByParams(array("URUT" => $arrEvaluasiKualifikasi1[$jenis_tahap], "PAKET_ID" => $reqId, "TAMPILKAN" => 1));
$status_dok_penawaran1 = $paket_tahap->getCountByParams(array("URUT" => $arrDokumenPenawaran[$jenis_tahap], "PAKET_ID" => $reqId, "TAMPILKAN" => 1, "HADIR" => 0));
$status_dok_penawaran2 = $paket_tahap->getCountByParams(array("URUT" => $arrDokumenPenawaran1[$jenis_tahap], "PAKET_ID" => $reqId, "TAMPILKAN" => 1, "HADIR" => 0));
$status_pengumuman = $paket_tahap->getCountByParams(array("URUT" => $arrPengumumanPemenang[$jenis_tahap], "PAKET_ID" => $reqId, "TAMPILKAN" => 1));

$aktif_pengumuman_pra = $paket_tahap->getCountByParamsAktif(array("URUT" => 7, "PAKET_ID" => $reqId, "TAMPILKAN" => 1));
$aktif_pengumuman_pra2 = $paket_tahap->getCountByParamsBerlalu(array("URUT" => 7, "PAKET_ID" => $reqId, "TAMPILKAN" => 1));

$status_dokumen_lelang = $paket_tahap->getCountByParamsAktif(array("URUT" => $arrDokumenLelang[$jenis_tahap], "PAKET_ID" => $reqId, "TAMPILKAN" => 1));
$status_dokumen_lelang2 = $paket_tahap->getCountByParamsBerlalu(array("URUT" => $arrDokumenLelang[$jenis_tahap], "PAKET_ID" => $reqId, "TAMPILKAN" => 1));

$aktif_aanwitzing = $paket_tahap->getCountByParamsAktif(array("URUT" => $arrAanwijzing[$jenis_tahap], "PAKET_ID" => $reqId, "TAMPILKAN" => 1));
$aktif_aanwitzing2 = $paket_tahap->getCountByParamsBerlalu(array("URUT" => $arrAanwijzing[$jenis_tahap], "PAKET_ID" => $reqId, "TAMPILKAN" => 1));

$aktif_dok_kualifikasi1 = $paket_tahap->getCountByParamsAktif(array("URUT" => $arrEvaluasiKualifikasi[$jenis_tahap], "PAKET_ID" => $reqId, "TAMPILKAN" => 1));
$aktif_dok_kualifikasi2 = $paket_tahap->getCountByParamsAktif(array("URUT" => $arrEvaluasiKualifikasi1[$jenis_tahap], "PAKET_ID" => $reqId, "TAMPILKAN" => 1));
$aktif_dok_kualifikasi3 = $paket_tahap->getCountByParamsBerlalu(array("URUT" => $arrEvaluasiKualifikasi1[$jenis_tahap], "PAKET_ID" => $reqId, "TAMPILKAN" => 1));
// $aktif_upload_password = 1;//$paket_tahap->getCountByParamsAktif(array("URUT" => $arrUploadPasswordPenawaran[$jenis_tahap], "PAKET_ID" => $reqId, "TAMPILKAN" => 1));
// $aktif_upload_password2 = 1;//$paket_tahap->getCountByParamsBerlalu(array("URUT" => $arrUploadPasswordPenawaran[$jenis_tahap], "PAKET_ID" => $reqId, "TAMPILKAN" => 1));
$aktif_upload_password = $paket_tahap->getCountByParamsAktif(array("URUT" => $arrUploadPasswordPenawaran[$jenis_tahap], "PAKET_ID" => $reqId, "TAMPILKAN" => 1));
$aktif_upload_password2 = $paket_tahap->getCountByParamsBerlalu(array("URUT" => $arrUploadPasswordPenawaran[$jenis_tahap], "PAKET_ID" => $reqId, "TAMPILKAN" => 1));

// $aktif_upload_password_sampul2 = 1; //$paket_tahap->getCountByParamsAktif(array("URUT" => $arrUploadPasswordPenawaranSampul2[$jenis_tahap], "PAKET_ID" => $reqId, "TAMPILKAN" => 1));
// $aktif_upload_password_sampul2_2 = 1; //$paket_tahap->getCountByParamsBerlalu(array("URUT" => $arrUploadPasswordPenawaranSampul2[$jenis_tahap], "PAKET_ID" => $reqId, "TAMPILKAN" => 1));
$aktif_upload_password_sampul2 = $paket_tahap->getCountByParamsAktif(array("URUT" => $arrUploadPasswordPenawaranSampul2[$jenis_tahap], "PAKET_ID" => $reqId, "TAMPILKAN" => 1));
$aktif_upload_password_sampul2_2 = $paket_tahap->getCountByParamsBerlalu(array("URUT" => $arrUploadPasswordPenawaranSampul2[$jenis_tahap], "PAKET_ID" => $reqId, "TAMPILKAN" => 1));

/* JIKA PASCA TIDAK PERLU DATA KUALIFIKASI */
if($pra_kualifikasi_cek == 2)
{
	$status_dok_kualifikasi1 = 0;
	$status_dok_kualifikasi2 = 0;
	$aktif_dok_kualifikasi1 = 0;
	$aktif_dok_kualifikasi2 = 0;
}

//echo '---'.$jenis_tahap.'--'.$aktif_dok_kualifikasi2.'---'.$pra_kualifikasi_cek;

/* APABILA KUALIFIKASI GAGAL, MAKA TIDAK BERHAK MELANJUTKAN KE DOKUMEN PENAWARAN */
$aktif_dok_penawaran1 = $paket_tahap->getCountByParamsAktif(array("URUT" => $arrDokumenPenawaran[$jenis_tahap], "PAKET_ID" => $reqId, "TAMPILKAN" => 1, "HADIR" => 0));
$aktif_dok_penawaran2 = $paket_tahap->getCountByParamsBerlalu(array("URUT" => $arrDokumenPenawaran[$jenis_tahap], "PAKET_ID" => $reqId, "TAMPILKAN" => 1));
if($aktif_dok_kualifikasi1 > 0 || $aktif_dok_kualifikasi2 > 0)
{
	//echo "mazuk".$reqLulusKualifikasi;
	if($reqLulusKualifikasi == 0)
	{
		$aktif_dok_penawaran1 = 0;
		$aktif_dok_penawaran2 = 0;
	}
}

$aktif_pengumuman = $paket_tahap->getCountByParamsAktif(array("URUT" => $arrPengumumanPemenang[$jenis_tahap], "PAKET_ID" => $reqId, "TAMPILKAN" => 1));
$aktif_pengumuman2 = $paket_tahap->getCountByParamsBerlalu(array("URUT" => $arrPengumumanPemenang[$jenis_tahap], "PAKET_ID" => $reqId, "TAMPILKAN" => 1));
$aktif_negosiasi = $paket_tahap->getCountByParamsAktif(array("URUT" => $arrNegosiasi[$jenis_tahap], "PAKET_ID" => $reqId, "TAMPILKAN" => 1));
$aktif_negosiasi2 = $paket_tahap->getCountByParamsBerlalu(array("URUT" => $arrNegosiasi[$jenis_tahap], "PAKET_ID" => $reqId, "TAMPILKAN" => 1));
// echo $aktif_negosiasi2; die();
if($aktif_negosiasi > 0 || $aktif_negosiasi2 > 0)
{
  /* CHECK APAKAH REKANAN PEMENANG */
  // echo $this->REKANAN_ID .'=='. $rekanan_id_pemenang; die();
	if($this->REKANAN_ID == $rekanan_id_pemenang)
		$aktif_negosiasi_menu =1;
	else
		$aktif_negosiasi_menu =0;
}

if ($this->REKANAN_ID) {
	// cek lulus penawaran atau tidak
	$paket_rekanan_lulus_penawaran->selectByParams(array("A.PAKET_ID" => $reqId, "A.LULUS_PENAWARAN" => 1, "A.REKANAN_ID" => $this->REKANAN_ID), -1, -1, "", " ORDER BY NILAI_PENAWARAN ASC, KIRIM_PENAWARAN_TANGGAL ASC ");
	$paket_rekanan_lulus_penawaran->firstRow();
	$rekanan_lulus_penawaran = $paket_rekanan_lulus_penawaran->getField("LULUS_PENAWARAN");
}

if ($this->USER_LOGIN_ID) {
	//pihak lain tambahan
	$paket_pihak_lain->selectByParamsPaketLelang(array("PAKET_ID" => $reqId, "USER_LOGIN_ID" => $this->USER_LOGIN_ID), -1, -1);
	$paket_pihak_lain->firstRow();
	$idPihakLain = $paket_pihak_lain->getField('USER_LOGIN_ID');
	if ($this->USER_TYPE_ID == 3) {
		$idPanitia = $paket_panitia->getCountByParams(array("PAKET_ID" => $reqId, "NIP" => $this->NIP));
	} else {
		$idPanitia = 0;
	}
}


$sampul1 = "";
if($sistem_sampul == "2")
	$sampul1 = translate(" File 1", " Cover 1");

if($pra_kualifikasi_cek == 1) // Prakualifikasi
  $labelDokumenPengadaan = 'Dokumen Pengadaan/Kualifikasi';
else
  $labelDokumenPengadaan = 'Dokumen Pengadaan';

$wajib = '<span class="badge badge-primary"><small style="font-size: 10px">wajib dilengkapi</small>';
$optional = '<span class="badge badge-success"><small style="font-size: 10px">Optional</small>';
$sudahwajib = '<span class="badge badge-success"><small style="font-size: 10px"><i class="fa fa-check"></i> lengkap</small>';
// echo $arrSanggah[$jenis_tahap];
$aktif_masa_sanggah = $paket_tahap->getCountByParamsAktif(array("URUT" => $arrSanggah[$jenis_tahap], "PAKET_ID" => $reqId, "TAMPILKAN" => 1));
$aktif_masa_sanggah2 = $paket_tahap->getCountByParamsBerlalu(array("URUT" => $arrSanggah[$jenis_tahap], "PAKET_ID" => $reqId, "TAMPILKAN" => 1));
// echo $aktif_masa_sanggah2; die();
if($aktif_masa_sanggah > 0 || $aktif_masa_sanggah2 > 0 )
{
  $cekAktif_sanggah = 1;
} else {
  $cekAktif_sanggah = 0;
}

// cek aktif pemberitahuan peringkat
$aktif_peringkat = $paket_tahap->getCountByParamsAktif(array("URUT" => $arrPeringkat[$jenis_tahap], "PAKET_ID" => $reqId, "TAMPILKAN" => 1));
$aktif_peringkat2 = $paket_tahap->getCountByParamsBerlalu(array("URUT" => $arrPeringkat[$jenis_tahap], "PAKET_ID" => $reqId, "TAMPILKAN" => 1));
if($aktif_peringkat > 0 || $aktif_peringkat2 > 0 )
{
  $cekAktif_peringkat = 1;
} else {
  $cekAktif_peringkat = 0;
}
?>

<style type="text/css">
  small { font-weight: bold; font-size: 11.5px }
  a.list-group-item { color: #000 !important; }
  .list-group-item { padding: 0.5rem 1.25rem !important; border: transparent !important; }
  .list-group a.list-group-item:hover  { color: #000 !important;  transition: 0.3s; background-color: #F7CA18 !important;}
</style>

<script type="text/javascript">
$(document).ready(function() {

	$('#btnKirim').on('click', function () {
		$.messager.defaults.ok = 'Ya';
		$.messager.defaults.cancel = 'Tidak';
		$.messager.confirm('Konfirmasi',"Kirim Undangan ke Penyedia?",function(r){
		  if (r){
			  var win = $.messager.progress({
									  title:'<?= SYSTEM_NAME.' | '.SYSTEM_NAME_PT ?>',
									  msg:'Proses kirim undangan via email...'
								  });
			  $.get("paket_rekanan_json/undang_pemilihan_email/?reqId=<?=$reqId?>", function( data ) {
				  $.messager.progress('close');
				  $.messager.alert('Informasi',data, 'info');
			  });
		  }
	  });
	});
});
</script>

<div class="row">
  <?php
  if ((int)$this->USER_TYPE_ID != '')
  { // Untuk user login ?>
  <div class="col-md-3 col-sm-3">

		<div class="list-group">
			<a class="text-white list-group-item disabled" style="color:#fff !important; background-color: #000 !important;"> Info Detail <?= $paket_metode_nama ?> </a>
        <?php
        if($this->USER_LOGIN_ID == $paket_user_id || $idPanitia > 0 || $this->USER_TYPE_ID == 7) // KEPALA PENGADAAN
        {
          // if($this->USER_LOGIN_ID == $paket_user_id || $this->USER_TYPE_ID == 7) // KEPALA PENGADAAN
          if($this->USER_LOGIN_ID == $paket_user_id || $idPanitia > 0)
          {
            // untuk label wajib dilengkapi dan lengkap
            $this->load->model("Metode");
            $this->load->model("Paket");
            $this->load->model("PaketDokumen");
            $this->load->model("PaketPanitia");
            $this->load->model("PaketEvaluasiAdminTawar");
            $this->load->model("PaketEvaluasiTeknisTawar");
            $this->load->model("PaketEvaluasiHargaTawar");
						$this->load->model("PaketEvaluasiKualifikasi");
            $this->load->model("Paketpemenang");
            $this->load->model("PaketBidangUsaha");

            $metode_c = new Metode();
            $paket_c = new Paket();
            $paket_dokumen_c = new PaketDokumen();
    				$paket_dokumen_k = new PaketDokumen();
            $paket_panitia_c = new PaketPanitia();
            $paket_evaluasi_admin_count_c = new PaketEvaluasiAdminTawar();
            $paket_evaluasi_teknis_count_c = new PaketEvaluasiTeknisTawar();
            $paket_evaluasi_harga_count_c = new PaketEvaluasiHargaTawar();
						$paket_evaluasi_kualifikasi_count = new PaketEvaluasiKualifikasi();
            $getpaket_pemenang_c = new Paketpemenang();
            $paket_bidang_usaha_c = new PaketBidangUsaha();

            $countJadwal = $metode_c->getCountByParams(array("PAKET_ID" => $reqId));
            $countDokumen = $paket_dokumen_c->getCountByParams(array("PAKET_ID" => $reqId, "JENIS_DOKUMEN" => "LELANG"));
    				$countDokumenKualifikasi = $paket_dokumen_k->getCountByParams(array("PAKET_ID" => $reqId, "JENIS_DOKUMEN" => "KUALIFIKASI"));
            $countPanitia = $paket_panitia_c->getCountByParams(array("PAKET_ID" => $reqId));
            $countPaket = $paket_c->getCountByParams(array("A.PAKET_ID" => coalesce($reqId, 0)));
            $countPaketEvaluasiAdminCOunt = $paket_evaluasi_admin_count_c->getCountByParams(array("PAKET_ID" => $reqId));
            $countPaketEvaluasiTeknisCOunt = $paket_evaluasi_teknis_count_c->getCountByParams(array("PAKET_ID" => $reqId));
            $countPaketEvaluasiHargaCOunt = $paket_evaluasi_harga_count_c->getCountByParams(array("PAKET_ID" => $reqId));
						$countPaketEvaluasiKualifikasi = $paket_evaluasi_kualifikasi_count->getCountByParams(array("PAKET_ID" => $reqId));
            $countPemenang = $getpaket_pemenang_c->getCountByParams(array("A.PAKET_ID" => $reqId));
            $countBidangUsaha = $paket_bidang_usaha_c->getCountByParams(array("PAKET_ID" => coalesce($reqId, 0)));

            $syarat = 0;
            if ($paket_metode_lelang_id == '7')  {
              if ($countPaketEvaluasiHargaCOunt > 0) {
                $syarat = 1;
              }
            } else {
              if ($countPaketEvaluasiAdminCOunt > 0 && $countPaketEvaluasiTeknisCOunt > 0 && $countPaketEvaluasiHargaCOunt > 0) {
                $syarat = 1;
              }
            }

            $editpaket = 0;
            // 1-e-Tender, 2-Pengadaan Langsung, 3-Tender Terbatas, 4:Seleksi, 5-Penunjukan Langsung, 6:e-Purchasing ,7-e-Tender Cepat, 8:Kompetisi/Beauty Contest, 9:Pembelian Langsung Offline, 10:Tender Kualifikasi
            if ($paket_metode_lelang_id == '1' || $paket_metode_lelang_id == '7' || $paket_metode_lelang_id == '10' || $paket_metode_lelang_id == '4')  {
              if ($countPaket > 0 && $countBidangUsaha > 0) {
                $editpaket = 1;
              }
            } else if ($paket_metode_lelang_id == '6' || $paket_metode_lelang_id == '9' || $paket_metode_lelang_id == '12')  {
              if ($countPaket > 0) {
                $editpaket = 1;
              }
            } else {
              if ($countPaket > 0 && $countBidangUsaha > 0) {
                $editpaket = 1;
              }
            }

            $countUndangan = 0;
            if ($paket_metode_lelang_id == '2' || $paket_metode_lelang_id == '3' || $paket_metode_lelang_id == '5' || $paket_metode_lelang_id == '8')  {
	            	$paket_rekanan_count = new PaketRekanan();
	              $prcount = $paket_rekanan_count->getCountByParams(array("PAKET_ID" => $reqId));
              if ($countPaket > 0 && $countBidangUsaha > 0 && $prcount > 0) {
                $countUndangan = 1;
              }
            }


            // echo $paket_metode_lelang_id.'-'.$editpaket.'---';
            if ($paket_metode_lelang_id == '10') {
            	echo '<a style="background-color:#f4f5f9; font-weight:500" class="list-group-item">K U A L I F I K A S I</a>';

            // end untuk label wajib dilengkapi dan lengkap
            ?>
            <?php
            }
             ?>
            <a href="main/index/paket_lelang_tambah/?reqId=<?=$reqId?>" class="list-group-item"> <span class="ft-arrow-right"></span> Edit Paket Pengadaan
              <?php if ($editpaket == 0) { echo $wajib; } else { echo $sudahwajib; }  ?></span>
            </a>
            <?php
            // if ($paket_metode_lelang_id == '1' || $paket_metode_lelang_id == '7') {
            if ($paket_metode_lelang_id != '6' && $paket_metode_lelang_id != '9' && $paket_metode_lelang_id != '12' && $paket_metode_lelang_id != '2') { // selain e-Purchasing & Pembelian offline & e-Purchasing Pemerintah
            ?>
            <a href="main/index/paket_lelang_tambah_daftar_panitia/?reqId=<?=$reqId?>" class="list-group-item"> <span class="ft-arrow-right"></span> Tim Pengadaan
              <?php if ($countPanitia == 0) {
              	if ($paket_metode_lelang_id == '1' || $paket_metode_lelang_id == '3' || $paket_metode_lelang_id == '4' || $paket_metode_lelang_id == '8' || $paket_metode_lelang_id == '10') {
              		echo $wajib;
              	} else {
              		echo $optional;
              	}
              } else { echo $sudahwajib; }  ?>
            </a>
            <?php
            }

            // ---------------  PEMBELIAN LANGSUNG ----------------
            if ($paket_metode_lelang_id == '6')
            { // Purchasing/Pembelian Langsung
              $this->load->model("Katalogrekanan");
              $katalogrekananRow = new Katalogrekanan();
              $katalogrekananRow->selectByParams(array('A.PAKET_ID' => $reqId));
              $katalogrekananRow->firstRow();
            ?>
            <a href="main/index/katalog_cart/?reqId=<?=$reqId?>" class="list-group-item"> <span class="ft-arrow-right"></span> Daftar Produk </a>
            <a href="main/index/katalog_negosiasi/?reqId=<?=$reqId?>" class="list-group-item"> <span class="ft-arrow-right"></span> Negosiasi </a>
            <?php
            if ($katalogrekananRow->getField('STATUS') >= 2 ) {
            $paket_ppk = new Paket();
            $paket_ppk->selectByParams(array("A.PAKET_ID" => coalesce($reqId, 0)));
            $paket_ppk->firstRow();
            $ppknya = $paket_ppk->getField("PPK");
            ?>
						<!-- <a onclick="openAddFrame('main/loadUrl/main/tunjuk_pengelola_kontrak_pembelian?paketid=<?php // echo $reqId ?>')" class="list-group-item"> <span class="ft-arrow-right"></span> Pilih PJK
              <?php // if ($ppknya == '') { echo $wajib; } else { echo $sudahwajib; }  ?>
						</a> -->

            <a href="main/index/katalog_surat_pesanan/?reqId=<?=$reqId?>" class="list-group-item"> <span class="ft-arrow-right"></span> Surat Pesanan </a>

            <?php
            } ?>
            <?php
            if ($katalogrekananRow->getField('STATUS') >= 4 ) { ?>
            <a href="main/index/katalog_tracking_pesanan/?reqId=<?=$reqId?>" class="list-group-item"> <span class="ft-arrow-right"></span> Tracking Pesanan </a>
            <?php
            } ?>
            <?php
            if ($katalogrekananRow->getField('STATUS') != 6 ) {
	     				if(trim($alasan_ulang) == "" && trim($alasan) == "")
              {
	    			?>
            <a onClick="openAdd('main/loadUrl/main/paket_lelang_batal/?reqId=<?=$reqId?>');" class="list-group-item"> <span class="ft-arrow-right"></span>  Batalkan Paket </a>
            <?php
	      			}
             }
            }

            // ---------------  PEMBELIAN OFFLINE ----------------
            if ($paket_metode_lelang_id == '9' || $paket_metode_lelang_id == '12')
            { // Pembelian Offline
            ?>
            <a onclick="openAddFrame('main/loadUrl/main/tunjuk_penyedia?paketid=<?= $reqId ?>')" class="list-group-item"> <span class="ft-arrow-right"></span> Tunjuk Penyedia
        		</a>

        		<?php
            $paket_ppk = new Paket();
            $paket_ppk->selectByParams(array("A.PAKET_ID" => coalesce($reqId, 0)));
            $paket_ppk->firstRow();
            $ppknya = $paket_ppk->getField("PPK");
            ?>
						<!-- <a onclick="openAddFrame('main/loadUrl/main/tunjuk_pengelola_kontrak_pembelian?paketid=<?php // echo $reqId ?>')" class="list-group-item"> <span class="ft-arrow-right"></span> Pilih PJK
              <?php // if ($ppknya == '') { echo $wajib; } else { echo $sudahwajib; }  ?>
						</a> -->

            <a href="main/index/purchasing_file/?reqId=<?=$reqId?>" class="list-group-item"> <span class="ft-arrow-right"></span> Upload Dokumen </a>
            <?php
            if(trim($alasan_ulang) == "" && trim($alasan) == "")
            { ?>
	    				<a onClick="openAdd('main/loadUrl/main/paket_lelang_batal/?reqId=<?=$reqId?>');" class="list-group-item"> <span class="ft-arrow-right"></span>  Batalkan Paket </a>
            <?php
	     			}
            } ?>

            <?php
            if ($paket_metode_lelang_id != '6' && $paket_metode_lelang_id != '9' && $paket_metode_lelang_id != '12')
            { // bukan Purchasing/Pembelian Langsung & bukan pembelian offline & e-Purchasing Pemerintah ?>
              <a href="main/index/paket_lelang_tambah_jadwal/?reqId=<?=$reqId?>&back=1" class="list-group-item"> <span class="ft-arrow-right"></span> Jadwal <?php if ($countJadwal == 0) { echo $wajib; } else { echo $sudahwajib; }  ?> </a>
            <?php
            if ($paket_metode_lelang_id == '10') {

            	$arrDokumenKualifikasi          = DOKUMEN_KUALIFIKASI;
							$arrAanwijzingKualifikasi       = AANWIJZING_KUALIFIKASI;
							$arrDokumenKualifikasiPra 	 	 	= UPLOAD_DOKUMEN_KUALIFIKASI;
							$arrEvaluasiKualifikasiPra	 		= EVALUASI_KUALIFIKASI_PRA;
							$arrPembuktianKualifikasi	 			= PEMBUKTIAN_KUALIFIKASI;
							$arrPengumumanKualifikasi	 			= PENGUMUMAN_KUALIFIKASI;
							$arrSanggahKualifikasi			 		= SANGGAH_KUALIFIKASI;
            ?>
             <a href="main/index/paket_lelang_tambah_daftar_peserta/?reqId=<?=$reqId?>" class="list-group-item"> <span class="ft-arrow-right"></span> Daftar Peserta </a>
             <a href="main/index/paket_lelang_tambah_dokumen_kualifikasi/?reqId=<?=$reqId?>&back=1" class="list-group-item"> <span class="ft-arrow-right"></span> Dokumen Kualifikasi <?php if ($countDokumenKualifikasi == 0) { echo $wajib; } else { echo $sudahwajib; }  ?></a>
             <a href="main/index/paket_lelang_tambah_kriteria_kualifikasi/?reqId=<?=$reqId?>&back=1" class="list-group-item"> <span class="ft-arrow-right"></span> Syarat Dokumen Kualifikasi <?php if ($countPaketEvaluasiKualifikasi == 0) { echo $wajib; } else { echo $sudahwajib; }  ?> </a>
             <a href="main/index/aanwijzing_kualifikasi_chat/?reqId=<?=$reqId?>&back=1" class="list-group-item"> <span class="ft-arrow-right"></span> Aanwijzing Kualifikasi </a>
             <a href="main/index/evaluasi_dokumen_kualifikasi/?reqId=<?=$reqId?>&back=1" class="list-group-item"> <span class="ft-arrow-right"></span> Evaluasi Dok. & Pembuktian Kualifikasi </a>
             <!-- <a href="main/index/klarifikasi_kualifikasi_chat/?reqId=<?=$reqId?>&back=1" class="list-group-item"> <span class="ft-arrow-right"></span> Pembuktian Kualifikasi </a> -->
             <a href="main/index/paket_lelang_tambah_daftar_peserta_hasil/?reqId=<?=$reqId?>&back=1" class="list-group-item"> <span class="ft-arrow-right"></span> Pengumuman Hasil Kualifikasi </a>
             <a href="main/index/paket_lelang_masa_sanggah_kualifikasi/?reqId=<?=$reqId?>&back=1" class="list-group-item"> <span class="ft-arrow-right"></span> Sanggah Kualifikasi</a>
            <?php
            }

            }
            if ($paket_metode_lelang_id == '10') {
            	echo '<a style="background-color:#f4f5f9; font-weight:500" class="list-group-item">P E M I L I H A N</a>';
            }
             ?>
            <!-- <li>
              <div>
                <a href="main/index/paket_lelang_tambah_reschedule_jadwal/?reqId=<?=$reqId?>"> <span class="ft-arrow-right"></span> Reschedule Jadwal </a>
              </div>
            </li>   -->

            <?php
            if ($paket_metode_lelang_id != '6' && $paket_metode_lelang_id != '9' && $paket_metode_lelang_id != '12')
            { // bukan Purchasing/Pembelian Langsung & bukan pembelian offline & e-Purchasing Pemerintah ?>
              <a href="main/index/paket_lelang_tambah_dokumen_lelang/?reqId=<?=$reqId?>" class="list-group-item"><span class="ft-arrow-right"></span> <?= $labelDokumenPengadaan ?> <?php if ($countDokumen == 0) { echo $wajib; } else { echo $sudahwajib; }  ?></a>
            <?php
            } ?>

            <?php
            if ($paket_metode_lelang_id == '2' || $paket_metode_lelang_id == '3' || $paket_metode_lelang_id == '5' || $paket_metode_lelang_id == '8')
            { // PL ?>
              <a href="main/index/paket_lelang_tambah_rekanan/?reqId=<?=$reqId?>" class="list-group-item"><span class="ft-arrow-right"></span> Undangan <?php if ($countUndangan == 0) { echo $wajib; } else { echo $sudahwajib; }  ?></a>
            <?php
            } ?>

            <?php
            if($pra_kualifikasi_cek == 1)
            {
            ?>
              <a href="main/index/paket_lelang_tambah_kriteria_kualifikasi/?reqId=<?=$reqId?>" class="list-group-item"> <span class="ft-arrow-right"></span> Kriteria Kualifikasi </a>
            <?php
            }
            ?>
            <?php
            if ($paket_metode_lelang_id != '6' && $paket_metode_lelang_id != '9' && $paket_metode_lelang_id != '12')
            { // bukan Purchasing/Pembelian Langsung  & bukan pembelian offline & e-Purchasing Pemerintah ?>
              <a href="main/index/paket_lelang_tambah_kriteria_penawaran/?reqId=<?=$reqId?>" class="list-group-item">
                <span class="ft-arrow-right"></span> Syarat Dokumen Penawaran <?php if ($syarat == 0) { echo $wajib; } else { echo $sudahwajib; }  ?>
              </a>
            <?php
            } ?>

            <!-- <li>
              <div>
              <a href="main/index/paket_lelang_tambah_dokumen_aritmatika/?reqId=<?=$reqId?>"> <span class="ft-arrow-right"></span>
              Nilai HPS </a>
            </div>
            </li>    -->

          <?php
          }

          if($this->USER_LOGIN_ID == $paket_user_id || $idPanitia > 0 || $this->USER_TYPE_ID == 7) // KEPALA PENGADAAN
          {
            if (($idPanitia > 0 || $this->USER_TYPE_ID == 7) && ($paket_metode_lelang_id == '1' || $paket_metode_lelang_id == '2' || $paket_metode_lelang_id == '3' || $paket_metode_lelang_id == '5' || $paket_metode_lelang_id == '7' || $paket_metode_lelang_id == '8')) {  // KHUSUS PANITIA & KEPALA PENGADAAN
             ?>
              <!-- <a href="main/index/paket_lelang_tambah_pakta_integritas/?reqId=<?=$reqId?>" class="list-group-item"> <span class="ft-arrow-right"></span> Pakta Integritas </a> -->
            <?php
            }
            ?>

            <?php
            if (($paket_metode_lelang_id == '1' || $paket_metode_lelang_id == '3' || $paket_metode_lelang_id == '4' || $paket_metode_lelang_id == '7') && ($this->USER_LOGIN_ID == $paket_user_id || $idPanitia > 0)) { ?>
              <a href="main/index/paket_lelang_tambah_daftar_peserta/?reqId=<?=$reqId?>" class="list-group-item"> <span class="ft-arrow-right"></span> Daftar Peserta </a>
            <?php
            } ?>

            <?php
            if (($paket_metode_lelang_id == '10') && ($this->USER_LOGIN_ID == $paket_user_id || $idPanitia > 0)) { ?>
              <a href="main/index/paket_lelang_tambah_daftar_peserta_kualifikasi/?reqId=<?=$reqId?>" class="list-group-item"> <span class="ft-arrow-right"></span> Daftar Peserta Pemilihan </a>
            <?php
            } ?>

            <!--
            <li>
              <div>
              <a href="main/index/paket_lelang_tambah_daftar_pihak_lain/?reqId=<?=$reqId?>"> <span class="ft-arrow-right"></span>
              Unit Fungsional &amp; Konsultan </a>
              <div>Daftar Unit Fungsional &amp; Konsultan.</div>

            </div>
              </li>
            -->

            <?php
            if ($paket_metode_lelang_id != '7') { ?>
            <!-- <li>
              <div>
                <a href="main/index/paket_lelang_tambah_aanwijzing_pra/?reqId=<?=$reqId?>"> <span class="ft-arrow-right"></span> Materi Aanwijzing </a>
              </div>
            </li>     -->
            <?php
            } ?>

            <?php
            if($paket_tahap->getCountByParams(array("URUT" => $arrEvaluasiKualifikasi[$jenis_tahap], "PAKET_ID" => $reqId, "TAMPILKAN" => 1)) > 0 || $paket_tahap->getCountByParams(array("URUT" => $arrEvaluasiKualifikasi1[$jenis_tahap], "PAKET_ID" => $reqId, "TAMPILKAN" => 1)) > 0)
            {
              if($pra_kualifikasi_cek == 1)
              {
            ?>
                <a href="main/index/evaluasi_kualifikasi_administrasi/?reqId=<?=$reqId?>" class="list-group-item"> <span class="ft-arrow-right"></span>  Evaluasi Kualifikasi </a>
            <?php
              }
            }
            if($pra_kualifikasi_cek == 1) // Prakualifikasi
            {
             if($paket_tahap->getCountByParams(array("URUT" => 7, "PAKET_ID" => $reqId, "TAMPILKAN" => 1)) > 0 || $paket_tahap->getCountByParams(array("URUT" => $arrEvaluasiKualifikasi1[$jenis_tahap], "PAKET_ID" => $reqId, "TAMPILKAN" => 1)) > 0)
             {
              ?>
                <a href="main/index/paket_lelang_tambah_pengumuman_prakualifikasi/?reqId=<?=$reqId?>" class="list-group-item"> <span class="ft-arrow-right"></span>  Pengumuman Kualifikasi </a>
            <?php
             }
            }
            // if($paket_tahap->getCountByParams(array("URUT" => $arrAanwijzing[$jenis_tahap], "PAKET_ID" => $reqId, "TAMPILKAN" => 1)) > 0)
            // {
              // if (($paket_metode_lelang_id != '5' && $paket_metode_lelang_id != '6' && $paket_metode_lelang_id != '9') && ($this->USER_LOGIN_ID == $paket_user_id || $idPanitia > 0)) { // 19-08-2023
              if (($paket_metode_lelang_id != '6' && $paket_metode_lelang_id != '7' && $paket_metode_lelang_id != '9' && $paket_metode_lelang_id != '12') && ($this->USER_LOGIN_ID == $paket_user_id || $idPanitia > 0)) {
            ?>
              <!-- <li>
                <div>
                  <a href="main/index/aanwijzing/?reqId=<?=$reqId?>"> <span class="ft-arrow-right"></span>  Aanwijzing </a>
                </div>
              </li>    -->
              <a href="main/index/aanwijzing_chat/?reqId=<?=$reqId?>" class="list-group-item"> <span class="ft-arrow-right"></span>  Aanwijzing <?php // $paket_metode_lelang_id; ?> </a>
            <?php
              }
            // }

            if($paket_tahap->getCountByParams(array("URUT" => $arrDokumenPenawaran[$jenis_tahap], "PAKET_ID" => $reqId, "TAMPILKAN" => 1)) > 0 || $paket_tahap->getCountByParams(array("URUT" => $arrDokumenPenawaran1[$jenis_tahap], "PAKET_ID" => $reqId, "TAMPILKAN" => 1)) > 0)
            {
              if($paket_metode_lelang_id == "9")
              {
              ?>
                <a href="main/index/paket_lelang_tambah_auction/?reqId=<?=$reqId?>" class="list-group-item"> <span class="ft-arrow-right"></span> e-Reverse Auction</a>
            <?php
              } else
              {
                if (($this->USER_LOGIN_ID == $paket_user_id || $idPanitia > 0)) {
            ?>
                  <a href="main/index/paket_lelang_tambah_dokumen_penawaran/?reqId=<?=$reqId?>" class="list-group-item"> <span class="ft-arrow-right"></span>  Dokumen Penawaran </a>
            <?php
                }
              }
            }
            $paket_tahap_auction = new PaketTahap();
            $paket_tahap_auction->selectByParams(array("URUT" => $arrPembukaanAuction[$jenis_tahap], "PAKET_ID" => $reqId));
            $paket_tahap_auction->firstRow();

            if($paket_tahap_auction->getField("HADIR") == "1")
              $link_auction = "paket_lelang_tambah_pembukaan_auction_manual";
            else
            {
              if($sistem_sampul == "2")
                $link_auction = "paket_lelang_tambah_pembukaan_auction_sampul1";
              else
                $link_auction = "paket_lelang_tambah_pembukaan_auction";
            }

              if($paket_metode_lelang_id == "9")
              {}
              else
              {
                if (($this->USER_LOGIN_ID == $paket_user_id || $idPanitia > 0))
                {
                  if ($paket_metode_lelang_id != '6' && $paket_metode_lelang_id != '9' && $paket_metode_lelang_id != '12')
                  { // bukan Purchasing/Pembelian Langsung  & bukan pembelian offline
            ?>
                    <a href="main/index/<?=$link_auction?>/?reqId=<?=$reqId?>" class="list-group-item"> <span class="ft-arrow-right"></span>  Pembukaan Penawaran <?=$sampul1?> </a>
            <?php
                  }
                }
              }

              if($sistem_sampul == "2")
              {
                if (($this->USER_LOGIN_ID == $paket_user_id || $idPanitia > 0))
                {
            ?>
                  <a href="main/index/evaluasi_penawaran_administrasi_sampul1/?reqId=<?=$reqId?>" class="list-group-item"> <span class="ft-arrow-right"></span>  Evaluasi Penawaran File 1 </a>
                  <a href="main/index/paket_lelang_tambah_pembukaan_auction_sampul2/?reqId=<?=$reqId?>" class="list-group-item"> <span class="ft-arrow-right"></span>  Pembukaan Penawaran File 2 </a>
                  <a href="main/index/evaluasi_penawaran_harga_sampul2/?reqId=<?=$reqId?>" class="list-group-item"> <span class="ft-arrow-right"></span> Evaluasi Penawaran File 2 </a>
            <?php
                }
              } else
              {
                if (($this->USER_TYPE_ID == 3 || $this->USER_TYPE_ID == 11) && ($this->USER_LOGIN_ID == $paket_user_id || $idPanitia > 0)) {  // PANITIA, USER PEMBUAT PAKET DAN ANGGOTA PANITIA
                  // 1:Tender, 2:Pengadaan langsung, 3:Tender Terbatas, 4:Seleksi Langsung, 5:Penunjukan Langsung, 6:e-Purchasing, 7:Tender Cepat, 8:Kompetisi
                  if ($paket_metode_lelang_id == 7) {
                   ?>
                    <a href="main/index/evaluasi_penawaran_harga/?reqId=<?=$reqId?>" class="list-group-item"> <span class="ft-arrow-right"></span>  Evaluasi Penawaran </a>
                  <?php
                  } elseif (($paket_metode_lelang_id != '6' && $paket_metode_lelang_id != '7' && $paket_metode_lelang_id != '9' && $paket_metode_lelang_id != '12')) { ?>
                    <a href="main/index/evaluasi_penawaran_administrasi/?reqId=<?=$reqId?>" class="list-group-item"> <span class="ft-arrow-right"></span>  Evaluasi Penawaran </a>
                  <?php
                  }
                }
              }
              ?>

              <?php
              if ($paket_metode_lelang_id == '1' || $paket_metode_lelang_id == '3' || $paket_metode_lelang_id == '7' || $paket_metode_lelang_id == '10') {
              ?>
               <!-- <a href="main/index/paket_lelang_tambah_penentuan_peringkat/?reqId=<?php // $reqId?>" class="list-group-item"> <span class="ft-arrow-right"></span>  Penentuan Peringkat </a> -->
              <?php
              }
            ?>

            <?php
              if($bidding == "1" && ($this->USER_TYPE_ID == 3 || $this->USER_TYPE_ID == 11) && ($this->USER_LOGIN_ID == $paket_user_id || $idPanitia > 0)) // BIDDING, PANITIA, USER PEMBUAT PAKET DAN ANGGOTA PANITIA
              {
            ?>
                <!-- <a href="main/index/klarifikasi_chat/?reqId=<?=$reqId?>" class="list-group-item"> <span class="ft-arrow-right"></span>  Klarifikasi Teknis & e-Reverse Auction </a> -->
                <a href="main/index/paket_lelang_tambah_negosiasi_undangan_reverse/?reqId=<?=$reqId?>" class="list-group-item"> <span class="ft-arrow-right"></span>  Pembuktian & e-Reverse Auction </a>
            <?php
              } else
              {
                if (($this->USER_TYPE_ID == 3 || $this->USER_TYPE_ID == 11) && ($this->USER_LOGIN_ID == $paket_user_id || $idPanitia > 0)) { // PANITIA, USER PEMBUAT PAKET DAN ANGGOTA PANITIA

                	if (($paket_metode_lelang_id != '6' && $paket_metode_lelang_id != '9' && $paket_metode_lelang_id != '12')) 
                	{ 
            ?>
                <!-- <li>
                  <div>
                    <a href="main/index/paket_lelang_tambah_negosiasi_setup/?reqId=<?=$reqId?>"> <span class="ft-arrow-right"></span>  Setup Negosiasi </a>
                  </div>
                </li>    -->
		                <a href="main/index/paket_lelang_tambah_negosiasi_undangan/?reqId=<?=$reqId?>" class="list-group-item"> <span class="ft-arrow-right"></span>
		                <?php
		                if ($bidding == "1") {
		                 	echo "Pembuktian & e-Reverse Auction ";
		                } else {
		                 	echo "Pembuktian & Negosiasi";
		                } ?>
		              	</a>
            <?php
            			}
                }
              }
                if (($this->USER_TYPE_ID == 3 || $this->USER_TYPE_ID == 11) && ($this->USER_LOGIN_ID == $paket_user_id || $idPanitia > 0)) { // PANITIA, USER PEMBUAT PAKET DAN ANGGOTA PANITIA

                	if (($paket_metode_lelang_id != '6' && $paket_metode_lelang_id != '9' && $paket_metode_lelang_id != '12')) 
                	{ 
            ?>
                <a href="main/index/paket_lelang_tambah_penentuan_pemenang/?reqId=<?=$reqId?>" class="list-group-item"> <span class="ft-arrow-right"></span>
                    <?php
                    // 1:Tender, 2:Pengadaan langsung, 3:Tender Terbatas, 4:Seleksi Langsung, 5:Penunjukan Langsung, 6:e-Purchasing, 7:Tender Cepat, 8:Kompetisi
                    switch ($paket_metode_lelang_id) {
                      case '1':
                      case '2':
                      case '3':
                      case '4':
                      case '7':
                      case '8':
                      case '10':
                        // if ($countPemenang == 0) { echo "Penetapan Pemenang ".$wajib; } else { echo "Penetapan Pemenang ".$sudahwajib; }
                        if ($countPemenang == 0) { echo "Penetapan Pemenang "; } else { echo "Penetapan Pemenang "; }
                        break;
                      case '5':
                      case '11':
                      // if ($countPemenang == 0) { echo "Penetapan Penyedia ".$wajib; } else { echo "Penetapan Penyedia ".$sudahwajib; }
                      if ($countPemenang == 0) { echo "Penetapan Penyedia "; } else { echo "Penetapan Penyedia "; }
                        break;

                      // default:
                      //   if ($countPemenang == 0) { echo "Penetapan Pemenang ".$wajib; } else { echo "Penetapan Pemenang ".$sudahwajib; }
                      //   break;
                    }
                  }
                    ?>
                </a>
              <?php
              } ?>

              <?php
              if ($paket_metode_lelang_id == '1' || $paket_metode_lelang_id == '3'|| $paket_metode_lelang_id == '4' || $paket_metode_lelang_id == '8' || $paket_metode_lelang_id == '10') {
              	if ($this->USER_TYPE_ID == 3 && ($this->USER_LOGIN_ID == $paket_user_id || $idPanitia > 0)) {
              ?>
               <a href="main/index/paket_lelang_masa_sanggah/?reqId=<?=$reqId?>" class="list-group-item"> <span class="ft-arrow-right"></span>  Masa Sanggah </a>
              <?php
            		}
              }
            ?>

             <!--  <li>
                <div>
                  <a href="main/index/paket_lelang_tambah_pengumuman_pemenang/?reqId=<?=$reqId?>"> <span class="ft-arrow-right"></span>    -->
                  <?php
                    // 1:Tender, 2:Pengadaan langsung, 3:Tender Terbatas, 4:Seleksi Langsung, 5:Penunjukan Langsung, 6:e-Purchasing, 7:Tender Cepat, 8:Kompetisi
                    // switch ($paket_metode_lelang_id) {
                    //   case '2':
                    //   case '5':
                    //     echo "Pengumuman";
                    //     break;

                    //   default:
                    //     echo "Pengumuman Pemenang";
                    //     break;
                    // }
                    ?>
                 <!--  </a>
                </div>
              </li>    -->


            <?php
            if ($rekanan_id_pemenang!='')
            {
            ?>
              <!-- <li>
                <div>
                <a href="main/index/paket_penilaian/?reqId=<?=$reqId?>"> <span class="ft-arrow-right"></span>
                Penilaian </a> -->
                <!-- <div>Penilaian Pemenang Lelang</div> -->
             <!--  </div>
              </li>   -->
              <!-- <li>
                <div>
                  <a href="main/index/paket_lelang_tambah_sppjb/?reqId=<?=$reqId?>"> <span class="ft-arrow-right"></span> SPPBJ </a>
                </div>
              </li>   -->
            <?php
            }
            }
          }
          ?>

          <!--  FOR PENGGUNA-->
          <?php
          // PENGGUNA yang MENGUSULKAN
          if(($this->USER_TYPE_ID == 8 or $this->USER_TYPE_ID == 9) and ($idPihakLain !='' or $paket->getField("USER_LOGIN_ID_FUNGSIONAL") == $this->USER_LOGIN_ID))
          {

          if ($paket_metode_lelang_id != '6' && $paket_metode_lelang_id != '9')
          { // bukan Purchasing/Pembelian Langsung  dan bukan pembelian offline
          ?>
              <!-- <a href="main/index/dokumen_lelang_fungsional/?reqId=<?=$reqId?>"> Dokumen Lelang <br> -->
            <a href="main/index/dokumen_lelang_pengguna/?reqId=<?=$reqId?>" class="list-group-item"><span class="ft-arrow-right"></span> <?= $labelDokumenPengadaan ?></a>
            <?php
          }
          if ($paket_metode_lelang_id != '6' && $paket_metode_lelang_id != '9')  // 7:Tender Cepat, 6: Pembelian Langsung Katalog, 9:Pembelian Offline
          { ?>
            <!-- <li>
              <div>
                <a href="main/index/aanwijzing/?reqId=<?=$reqId?>"><span class="ft-arrow-right"></span> Aanwijzing</a>
              </div>
            </li>    -->
            <!-- Aanwijzing khusus pengguna penusul -->
            <a href="main/index/aanwijzing_chat/?reqId=<?=$reqId?>" class="list-group-item"><span class="ft-arrow-right"></span> Aanwijzing</a>
          <?php
          } ?>

            <?php
            if($idPihakLain == "")
            {}
            else
            {
              if ($paket_metode_lelang_id == '1' || $paket_metode_lelang_id == '2' || $paket_metode_lelang_id == '3' || $paket_metode_lelang_id == '5' || $paket_metode_lelang_id == '7' || $paket_metode_lelang_id == '8') {
            ?>
              <!-- <h3><a href="main/index/pakta_integritas/?reqId=<?=$reqId?>"><span>Pakta Integritas</span></a></h3> -->
              <!-- <a href="main/index/paket_lelang_tambah_pakta_integritas/?reqId=<?=$reqId?>" class="list-group-item"> <span class="ft-arrow-right"></span> Pakta Integritas</a> -->
              <a href="main/index/paket_laporan_pengguna/?reqId=<?=$reqId?>" class="list-group-item"> <span class="ft-arrow-right"></span> Laporan Paket </a>
            <?php
              }
            }
          }
          ?>


          <!--  FOR AUDIT-->
          <?php
          if($this->USER_TYPE_ID == 10 && $paket_metode_lelang_id != '6' && $paket_metode_lelang_id != '9')
          { // bukan Purchasing/Pembelian Langsung ) // AUDIT // bukan Pembelian Offline
          ?>
            <a href="main/index/dokumen_lelang_pengguna/?reqId=<?=$reqId?>" class="list-group-item"> <span class="ft-arrow-right"></span> <?=$labelDokumenPengadaan?> </a>
            <a href="main/loadUrl/report/jadwal_pdf/?reqId=<?=$reqId?>" target="_blank" class="list-group-item"> <span class="ft-arrow-right"></span> Cetak Jadwal </a>
            <?php
            if ($paket_metode_lelang_id != '7') { ?>
              <a href="main/loadUrl/report/aanwijzing_cetak_pdf/?reqId=<?=$reqId?>" target="_blank" class="list-group-item"> <span class="ft-arrow-right"></span> Hasil Aanwijzing </a>
            <?php
            } ?>
            <!-- <li>
              <div>
                <a href="main/loadUrl/report/dokumen_penawaran_ba_pdf/?reqId=<?=$reqId?>" target="_blank"> <span class="ft-arrow-right"></span>  Hasil Pemasukan Penawaran </a>
              </div>
            </li>    -->
            <?php
            if($sistem_sampul == "2")
              {
              ?>
                <a href="main/loadUrl/report/dokumen_pembukaan_penawaran_ba_pdf/?reqId=<?=$reqId?>" target="_blank" class="list-group-item"> <span class="ft-arrow-right"></span>  Hasil Pembukaan Penawaran File 1 </a>
              <?php
              }
              else
              {
              ?>
                <a href="main/loadUrl/report/dokumen_pembukaan_penawaran_ba_pdf/?reqId=<?=$reqId?>" target="_blank" class="list-group-item"> <span class="ft-arrow-right"></span>  <?=translate("Pembukaan Penawaran", "Bids Opening")?> </a>
            <?php
              }

            if($publish_ba_penawaran_sampul2 == "1" || $publish_ba_penawaran_sampul2 == "2")
            {
            ?>
              <a href="main/loadUrl/report/dokumen_pembukaan_penawaran_sampul2_ba_pdf/?reqId=<?=$reqId?>" target="_blank" class="list-group-item"> <span class="ft-arrow-right"></span>  Hasil Pembukaan Penawaran File 2 </a>
            <?php
            }
						?>

						<?php
						    if($sistem_sampul == "1")
						    { ?>
						      <a href="main/index/hasil_eval_rekanan/?reqId=<?=$reqId?>" class="list-group-item"> <span class="ft-arrow-right"></span> Hasil Evaluasi </a>
						<?php
						    } ?>

						<?php
						    if($sistem_sampul == "2")
						    { ?>
						      <a href="main/index/hasil_eval_rekanan/?reqId=<?=$reqId?>" class="list-group-item"> <span class="ft-arrow-right"></span> Hasil Evaluasi </a>
						      <!-- <a href="main/index/hasil_eval_rekanan_file_1/?reqId=<?php // echo $reqId?>" class="list-group-item"> <span class="ft-arrow-right"></span> Hasil Evaluasi File 1 </a>
									<a href="main/index/hasil_eval_rekanan_file_2/?reqId=<?php // echo $reqId?>" class="list-group-item"> <span class="ft-arrow-right"></span> Hasil Evaluasi File 2 </a> -->
						<?php
						    } ?>
									<!-- <a href="main/index/paket_lelang_penentuan_peringkat_rekanan/?reqId=<?=$reqId?>" class="list-group-item"> <span class="ft-arrow-right"></span>  Pemberitahuan Peringkat </a> -->
									<a href="main/index/paket_dokumen_pemilihan_legal/?reqId=<?=$reqId?>" class="list-group-item"> <span class="ft-arrow-right"></span>  Dokumen Hasil Pemilihan </a>
						<?php
            if($bidding == "1")
              {}
            else
              {
            ?>
                  <a href="main/loadUrl/report/negosiasi_cetak_pdf/?reqId=<?=$reqId?>" target="_blank" class="list-group-item"> <span class="ft-arrow-right"></span>  Hasil Negosiasi </a>
            <?php
              } ?>
                <!-- <a href="main/loadUrl/report/sppjb_pdf/?reqId=<?=$reqId?>" target="_blank"> <span class="ft-arrow-right"></span>  Pemenang Lelang </a> -->
                <a href="main/index/pengumuman_pemenang_rekanan/?reqId=<?=$reqId?>" class="list-group-item"> <span class="ft-arrow-right"></span>
                  <?php
                  switch ($paket_metode_lelang_id) {
                    case '2':
                    case '5':
                      echo "Pengumuman";
                      break;

                    default:
                      echo "Pengumuman Pemenang";
                      break;
                  } ?>
                </a>

						<!-- <li>
							<div>
								<a onclick="openAddLg('main/loadUrl/main/rekam_jejak_view?id=<?= $reqPermohonanId ?>&paketid=<?= $reqId ?>')"> <span class="ft-arrow-right"></span>  Rekam Jejak..</a>
							</div>
						</li> -->

							<a href="kontrak/index/contracting_audit_dokumen/?reqId=<?=$reqId?>&reqProses=6" class="list-group-item"> <span class="ft-arrow-right"></span>  Dokumen Kontrak</a>

            <?php
            $FILE_DIR = "uploads/pemenang/";
            $this->load->model("PaketDokumen");
            $paket_dokumen = new PaketDokumen();
            $paket_dokumen->selectByParams(array("PAKET_ID" => $reqId, "JENIS_DOKUMEN" => "PENGUMUMAN_PEMENANG"));
            $paket_dokumen->firstRow();

            if($paket_dokumen->getField("PATH_FILE") =='')
            {}
            else
            {
            ?>
           <!--  <li>
              <div>
                <a href="<?=$FILE_DIR.str_replace("'", "''", $paket_dokumen->getField("PATH_FILE"))?>" target="_blank"> <span class="ft-arrow-right"></span>  -->
                <?php
                // 1:Tender, 2:Pengadaan langsung, 3:Tender Terbatas, 4:Seleksi Langsung, 5:Penunjukan Langsung, 6:e-Purchasing, 7:Tender Cepat, 8:Kompetisi
                // switch ($paket_metode_lelang_id) {
                //   case '2':
                //   case '5':
                //     echo "Pengumuman";
                //     break;

                //   default:
                //     echo "Pengumuman Pemenang";
                //     break;
                // }
                ?>
                <!-- </a>
              </div>
            </li>    -->
            <?php
            }
          }

          // For Penyedia
          if($reqPaketRekananId != "") // KHUSUS PENYEDIA YANG SUDAH MENDAFTAR
          {
            if($alasan == "" || $publish_ba_penawaran == "2") // PAKET TIDAK DIBATALKAN || BA PENAWARAN SUDAH PUBLISH
            {
            	if ($paket_metode_lelang_id == '10') {

            		// cek tanggal aktif kualifikasi
								$arrKualifikasiDokumen               = DOKUMEN_KUALIFIKASI;
								$arrKualifikasiAanwijzing            = AANWIJZING_KUALIFIKASI;
								$arrKualifikasiUploadDokumen         = UPLOAD_DOKUMEN_KUALIFIKASI;
								$arrKualifikasiEvaluasiKualifikasi   = EVALUASI_KUALIFIKASI_PRA;
								$arrKualifikasiPembuktian            = PEMBUKTIAN_KUALIFIKASI;
								$arrKualifikasiPengumuman            = PENGUMUMAN_KUALIFIKASI;
								$arrKualifikasiSanggah               = SANGGAH_KUALIFIKASI;
            		// end cek tanggal aktif kualifikasi

								$status_dokumen_kualifikasi = $paket_tahap->getCountByParamsAktif(array("URUT" => $arrKualifikasiDokumen[$jenis_tahap], "PAKET_ID" => $reqId, "TAMPILKAN" => 1));
								$status_dokumen_kualifikasi2 = $paket_tahap->getCountByParamsBerlalu(array("URUT" => $arrKualifikasiDokumen[$jenis_tahap], "PAKET_ID" => $reqId, "TAMPILKAN" => 1));
								$status_aanwijzing_kualifikasi = $paket_tahap->getCountByParamsAktif(array("URUT" => $arrKualifikasiAanwijzing[$jenis_tahap], "PAKET_ID" => $reqId, "TAMPILKAN" => 1));
								$status_aanwijzing_kualifikasi2 = $paket_tahap->getCountByParamsBerlalu(array("URUT" => $arrKualifikasiAanwijzing[$jenis_tahap], "PAKET_ID" => $reqId, "TAMPILKAN" => 1));
								$status_upload_dokumen_kualifikasi = $paket_tahap->getCountByParamsAktif(array("URUT" => $arrKualifikasiUploadDokumen[$jenis_tahap], "PAKET_ID" => $reqId, "TAMPILKAN" => 1));
								$status_upload_dokumen_kualifikasi2 = $paket_tahap->getCountByParamsBerlalu(array("URUT" => $arrKualifikasiUploadDokumen[$jenis_tahap], "PAKET_ID" => $reqId, "TAMPILKAN" => 1));
								$status_pembuktian_kualifikasi = $paket_tahap->getCountByParamsAktif(array("URUT" => $arrKualifikasiPembuktian[$jenis_tahap], "PAKET_ID" => $reqId, "TAMPILKAN" => 1));
								$status_pembuktian_kualifikasi2 = $paket_tahap->getCountByParamsBerlalu(array("URUT" => $arrKualifikasiPembuktian[$jenis_tahap], "PAKET_ID" => $reqId, "TAMPILKAN" => 1));
								$status_pengumuman_kualifikasi = $paket_tahap->getCountByParamsAktif(array("URUT" => $arrKualifikasiPengumuman[$jenis_tahap], "PAKET_ID" => $reqId, "TAMPILKAN" => 1));
								$status_pengumuman_kualifikasi2 = $paket_tahap->getCountByParamsBerlalu(array("URUT" => $arrKualifikasiPengumuman[$jenis_tahap], "PAKET_ID" => $reqId, "TAMPILKAN" => 1));
								$status_sanggah_kualifikasi = $paket_tahap->getCountByParamsAktif(array("URUT" => $arrKualifikasiSanggah[$jenis_tahap], "PAKET_ID" => $reqId, "TAMPILKAN" => 1));
								$status_sanggah_kualifikasi2 = $paket_tahap->getCountByParamsBerlalu(array("URUT" => $arrKualifikasiSanggah[$jenis_tahap], "PAKET_ID" => $reqId, "TAMPILKAN" => 1));


          			echo '<a style="background-color:#f4f5f9; font-weight:500" class="list-group-item">K U A L I F I K A S I</a>';
          			?>
          			<?php
          			if ($status_dokumen_kualifikasi > 0 || $status_dokumen_kualifikasi2 > 0) {
          			?>
          			<a href="main/index/paket_lelang_dokumen_kualifikasi_rekanan/?reqId=<?=$reqId?>" class="list-group-item"> <span class="ft-arrow-right"></span> Dokumen Kualifikasi </a>
          			<?php
          			}

          			if ($status_aanwijzing_kualifikasi > 0 || $status_aanwijzing_kualifikasi2 > 0) {
          			?>
			          <a href="main/index/aanwijzing_kualifikasi_chat_rekanan/?reqId=<?=$reqId?>" class="list-group-item"> <span class="ft-arrow-right"></span> Aanwijzing Kualifikasi </a>
			          <?php
			          }

          			if ($status_upload_dokumen_kualifikasi > 0 || $status_upload_dokumen_kualifikasi2 > 0) {
			          ?>
			          <a href="main/index/dokumen_penawaran_kualifikasi_rekanan/?reqId=<?=$reqId?>" class="list-group-item"> <span class="ft-arrow-right"></span> Upload Dokumen Kualifikasi </a>
			          <?php
			          }

          			if ($status_pembuktian_kualifikasi > 0 || $status_pembuktian_kualifikasi2 > 0) {
			          ?>
			          <!-- Jika Lolos di evaluasi kualifikasi -->
			          <a href="main/index/rekanan_chat_eval_kualifikasi/?reqId=<?=$reqId?>" class="list-group-item"> <span class="ft-arrow-right"></span> Pembuktian Kualifikasi </a>
			          <?php
			          }

          			if ($status_pengumuman_kualifikasi > 0 || $status_pengumuman_kualifikasi2 > 0) {
                	if($publish_eval_kualifikasi == "1") {
                		?>
                  <a href="main/index/hasil_eval_rekanan_kualifikasi/?reqId=<?=$reqId?>" class="list-group-item"> <span class="ft-arrow-right"></span> Pengumuman Hasil Kualifikasi </a>
              	<?php
                	}
                }

          			if ($status_sanggah_kualifikasi > 0 || $status_sanggah_kualifikasi2 > 0) {
                ?>
			          <a href="main/index/paket_lelang_masa_sanggah_kualifikasi_rekanan/?reqId=<?=$reqId?>" class="list-group-item"> <span class="ft-arrow-right"></span> Sanggah Kualifikasi</a>
			          <?php
			          }

							}

              if($reqLulusPendaftaran == 1) // LULUS PENDAFTARAN
              {
                if($reqLulusPendaftaran == 1 && $status_dokumen_lelang > 0 || $status_dokumen_lelang2 > 0) // LULUS PENDAFTARAN && JADWAL DOWNLOAD DOKUMEN LELANG SUDAH AKTIF
                {
                	if ($paket_metode_lelang_id == '10') {
			            	echo '<a style="background-color:#f4f5f9; font-weight:500" class="list-group-item">P E M I L I H A N</a>';
			            }
                	?>

                  <a href="main/index/dokumen_lelang_rekanan/?reqId=<?=$reqId?>" class="list-group-item"> <span class="ft-arrow-right"></span>  <?= $labelDokumenPengadaan ?></a>

                  <?php
                  if($pra_kualifikasi_cek == 1) // KHUSUS PRA-KUALIFIKASI
                  {
                    if($aktif_dok_kualifikasi1 > 0  || $aktif_dok_kualifikasi2 > 0 || $aktif_dok_kualifikasi3 > 0)
                    {
                    ?>
                      <a href="main/index/data_kualifikasi/?reqId=<?=$reqId?>" class="list-group-item"> <span class="ft-arrow-right"></span> <?=translate("Data Kualifikasi", "Submit Qualification")?></a>
                     <?php
                    }
                  }
                  if($pra_kualifikasi_cek == 1 && $aktif_pengumuman_pra > 0  || $aktif_pengumuman_pra2 > 0)
                  {
                    $paket_dokumen_pra = new PaketDokumen();
                    $paket_dokumen_pra->selectByParams(array("PAKET_ID" => $reqId, "JENIS_DOKUMEN" => "PENGUMUMAN_PRA"));

                    $paket_dokumen_pra->firstRow();
                    $reqPaketDokumenId = $paket_dokumen_pra->getField("PAKET_DOKUMEN_ID");
                    if ($paket_dokumen_pra->getField("PATH_FILE")=='')
                    {
                    }
                    else
                    {
                    ?>
                      <a href="main/index/pengumuman_prakualifikasi_rekanan/?reqId=<?=$reqId?>" class="list-group-item"> <span class="ft-arrow-right"></span> <?=translate("Pengumuman Kualifikasi", "Announcement Qualification")?></a>
                    <?php
                      }
                    ?>
                   <?php
                  }
                  $aanwijzing_tampil = true;
                  if($pra_kualifikasi_cek == 1)
                  {
                    if($reqLulusKualifikasi == "0")
                    {
                      $aanwijzing_tampil = false;
                    }
                  }
                  if($aanwijzing_tampil == true && ($aktif_aanwitzing > 0 || $aktif_aanwitzing2 > 0 ))
                  {
                    // if ($paket_metode_lelang_id != '5')
                    // {
                  ?>
                    <!-- <li>
                      <div>
                        <a href="main/index/aanwijzing/?reqId=<?=$reqId?>"> <span class="ft-arrow-right"></span> Aanwijzing </a>
                      </div>
                    </li>   -->
                      <a href="main/index/aanwijzing_chat_rekanan/?reqId=<?=$reqId?>" class="list-group-item"> <span class="ft-arrow-right"></span> Aanwijzing </a>
                   <?php
                    // }
                  }
                  // echo $aktif_dok_penawaran2.'...';
                  if($aktif_dok_penawaran1 > 0 || $aktif_dok_penawaran2 > 0)
                  {
                    if($paket_metode_lelang_id == "9")
                    {
                    ?>
                      <a href="main/index/auction_rekanan/?reqId=<?=$reqId?>" class="list-group-item"> <span class="ft-arrow-right"></span>  e-Reverse Auction </a>
                    <?php
                    }
                    else
                    {
                    ?>
                      <a href="main/index/dokumen_penawaran_boq/?reqId=<?=$reqId?>" class="list-group-item"> <span class="ft-arrow-right"></span>  <?=translate("Upload Dokumen Penawaran", "Submit Proposal")?></a>
                    <?php
                    }
                  }

                  if($reqKirimPenawaran == "1")
                  {
                    if($aktif_upload_password > 0 || $aktif_upload_password2 > 0)
                    {
                      if($sampul1 == "")
                        $uploadPassTitle = " Penawaran";
                      else
                        $uploadPassTitle = $sampul1;
                    ?>
                      <a href="main/index/dokumen_penawaran_password/?reqId=<?=$reqId?>" class="list-group-item"> <span class="ft-arrow-right"></span> <?=translate("Upload Enkripsi ".$uploadPassTitle, "Upload Certificate File")?></a>
                    <?php
                    }
                  }

                  if($aktif_upload_password > 0 || $aktif_upload_password2 > 0)
                  {
                    if($aktif_dok_penawaran2 > 0) {
                      /* CEK APAKAH SUDAH MEMASUKKAN DOKUMEN PENAWARAN */
                      $sudahUpload = $paket_dokumen->getCountByParams(array("PAKET_ID" => $reqId, "REKANAN_USER_ID" => $this->ID), " AND JENIS_DOKUMEN LIKE 'PENAWARAN%' ");
                      if($sudahUpload > 0)
                      {
                        if (($paket_metode_lelang_id == '1' || $paket_metode_lelang_id == '7'))
                        {
                    ?>
                        <!-- <li>
                          <div>
                            <a href="main/loadUrl/report/dokumen_penawaran_ba_pdf_rekanan?reqId=<?=$reqId?>"> <span class="ft-arrow-right"></span>  <?=translate("Hasil Pemasukan Penawaran", "Minutes of Submission of Bids")?> </a>
                          </div>
                        </li>   -->
                    <?php
                        }
                      }
                    }
                  }
                  if($publish_ba_penawaran == "1" || $publish_ba_penawaran == "2")
                  {
                    /* CEK APAKAH SUDAH MEMASUKKAN DOKUMEN PENAWARAN */
                    $sudahUpload = $paket_dokumen->getCountByParams(array("PAKET_ID" => $reqId, "REKANAN_USER_ID" => $this->ID), " AND JENIS_DOKUMEN LIKE 'PENAWARAN%' ");
                    if($sudahUpload > 0)
                    {
                      if($sistem_sampul == "2")
                      {
                      ?>
                        <a href="main/index/pembukaan_auction_rekanan_sampul1/?reqId=<?=$reqId?>" class="list-group-item"> <span class="ft-arrow-right"></span>  <?=translate("Pembukaan Penawaran File 1", "Bids Opening Cover 1")?> </a>
                      <?php
                      }
                      else
                      {
                  ?>
                        <a href="main/index/pembukaan_auction_rekanan/?reqId=<?=$reqId?>" class="list-group-item"> <span class="ft-arrow-right"></span>  <?=translate("Pembukaan Penawaran", "Bids Opening")?> </a>
                  <?php
                      }
                    }
                  } ?>

                  <?php

                  // Cek undangan Klarifikasi
                  $this->load->model(array("Paketundanganklarifikasi"));
                  $paketundangan = new Paketundanganklarifikasi();
                  $paketundangan->selectByParams(array("PAKET_ID" => $reqId, "REKANAN_ID" => $this->ID));
                  if($paketundangan->countRow() > 0) { ?>
                    <a onClick="openAddFrame('main/loadUrl/main/undangan_klarifikasi_view?reqId=<?= $reqId ?>&rekanan=<?= $this->ID ?>')" class="list-group-item"> <span class="ft-arrow-right"></span> Undangan Klarifikasi</a>
                  <?php 
                  }

                  if($reqKirimPenawaran == "1" && $paket_metode_lelang_id != '6' && $paket_metode_lelang_id != '9')
                  {
                    $rekanan_evaluasi_admin_tawar->selectByParams(array("PAKET_REKANAN_ID" => $reqPaketRekananId));
                    $rekanan_evaluasi_admin_tawar->firstRow();

                    $rekanan_evaluasi_teknis_tawar->selectByParams(array("PAKET_REKANAN_ID" => $reqPaketRekananId));
                    $rekanan_evaluasi_teknis_tawar->firstRow();

                    $rekanan_evaluasi_harga_tawar->selectByParams(array("PAKET_REKANAN_ID" => $reqPaketRekananId));
                    $rekanan_evaluasi_harga_tawar->firstRow();

                    if ($rekanan_evaluasi_admin_tawar->getField('memenuhi_syarat') == '1'
                        // && $rekanan_evaluasi_teknis_tawar->getField('memenuhi_syarat') == '1'
                       )
                    {
                  ?>
                      <!-- <a href="main/index/rekanan_chat_eval_teknis/?reqId=<?=$reqId?>" class="list-group-item"> <span class="ft-arrow-right"></span>  Evaluasi Dokumen </a> -->
                  <?php
                    }

                    if ($reqMetodePengadaan == '1' || $reqMetodePengadaan == '2' || $reqMetodePengadaan == '3' || $reqMetodePengadaan == '7' || $reqMetodePengadaan == '8'|| $reqMetodePengadaan == '10')
                    {
                  ?>

                  <?php
                      // if($sistem_sampul == "1" && $rekanan_evaluasi_admin_tawar->getField('memenuhi_syarat') == '1' && $rekanan_evaluasi_teknis_tawar->getField('memenuhi_syarat') == '1' && $rekanan_evaluasi_harga_tawar->getField('memenuhi_syarat') == '1')
                      // if($sistem_sampul == "1" && $reqKirimPenawaran == "1" && $publish_ba_evaluasi_sampul1  == "1")
                      if(($sistem_sampul == "1" || $sistem_sampul == "2") && $reqKirimPenawaran == "1" && $cekPublishPemenang  == "1") // Evaluasi muncul setelah publish pemenang
                      {


                      	?>
                        <!-- <a href="main/index/hasil_eval_rekanan/?reqId=<?=$reqId?>" class="list-group-item"> <span class="ft-arrow-right"></span> Hasil Evaluasi</a> -->
                  <?php
                      } ?>

                  <?php
                      // if($publish_ba_evaluasi_sampul1  == "1" && $sistem_sampul == "2" && $rekanan_evaluasi_admin_tawar->getField('memenuhi_syarat') == '1'
                      // if($sistem_sampul == "2" && $reqKirimPenawaran == "1" && $publish_ba_evaluasi_sampul1  == "1")
                      if($sistem_sampul == "2" && $reqKirimPenawaran == "1" && $cekPublishPemenang  == "1") // Evaluasi muncul setelah publish pemenang
                      {
                      	// Hasil evaluasi 2 file gabung jadi 1
                      	?>
                        <!-- <a href="main/index/hasil_eval_rekanan_file_1/?reqId=<?php // $reqId?>" class="list-group-item"> <span class="ft-arrow-right"></span> Hasil Evaluasi File 1 </a> -->
                  <?php
                      } ?>

                  <?php
                    }
                  } ?>

                  <?php
                  if($reqKirimPenawaran == "1")
                  {
                    if($aktif_upload_password_sampul2 > 0 || $aktif_upload_password_sampul2_2 > 0)
                    {
                    ?>
                      <a href="main/index/dokumen_penawaran_password_sampul2/?reqId=<?=$reqId?>" class="list-group-item"> <span class="ft-arrow-right"></span>  Upload Enkripsi File 2 </a>
                    <?php
                    }
                  }

                  if($reqLulusPenawaranSampul1 == "1")
                  {
                    if($publish_ba_penawaran_sampul2 == "1" || $publish_ba_penawaran_sampul2 == "2")
                    {
                    ?>
                      <a href="main/index/pembukaan_auction_rekanan_sampul2/?reqId=<?=$reqId?>" class="list-group-item"> <span class="ft-arrow-right"></span>  Pembukaan Penawaran File 2 </a>
                    <?php
                    }
                  }
                  ?>

                  <?php
                  // if($publish_ba_evaluasi_sampul2  == "1" && $sistem_sampul == "2" && $rekanan_evaluasi_harga_tawar->getField('memenuhi_syarat') == '1')
                  // if($sistem_sampul == "2" && $reqKirimPenawaran == "1" && $publish_ba_evaluasi_sampul2  == "1")
                  if($sistem_sampul == "2" && $reqKirimPenawaran == "1" && $cekPublishPemenang  == "1") // Evaluasi muncul setelah publish pemenang
                  { ?>
                    <!-- <a href="main/index/hasil_eval_rekanan_file_2/?reqId=<?php //$reqId?>" class="list-group-item"> <span class="ft-arrow-right"></span> Hasil Evaluasi File 2 </a> -->
                  <?php
                  }

                  if (($paket_metode_lelang_id == '1' || $paket_metode_lelang_id == '3' || $paket_metode_lelang_id == '7' || $paket_metode_lelang_id == '10')  && $cekAktif_peringkat == '1' )
                  {
                  ?>
                    <!-- <a href="main/index/paket_lelang_penentuan_peringkat_rekanan/?reqId=<?php //$reqId?>" class="list-group-item"> <span class="ft-arrow-right"></span>  Pemberitahuan Peringkat </a> -->
                  <?php
                  }

                  if($bidding == "1")
                  {
                    // echo $sistem_sampul.'----';
                    if ($sistem_sampul == '2') {
                      if($aktif_negosiasi > 0 || $aktif_negosiasi2 > 0)
                      {
                        if ($rekanan_lulus_penawaran == '1' && $reqLulusPenawaranSampul1 == "1") // $reqLulusPenawaranSampul1 == "1"
                        {
                  ?>
                          <a href="main/index/klarifikasi_chat_rekanan/?reqId=<?=$reqId?>" class="list-group-item"> <span class="ft-arrow-right"></span> Pembuktian & e-Reverse Auction</a>
                    <?php
                        }
                      }
                    } else { // 1 file
                      if($aktif_negosiasi > 0 || $aktif_negosiasi2 > 0)
                      {
                        if ($rekanan_lulus_penawaran == '1')
                        {
                  ?>
                          <a href="main/index/klarifikasi_chat_rekanan/?reqId=<?=$reqId?>" class="list-group-item"> <span class="ft-arrow-right"></span> Pembuktian & e-Reverse Auction</a>
                    <?php
                        }
                      }
                    }
                  }
                  else
                  {
                    if($aktif_negosiasi_menu > 0)
                    {
                    ?>
                      <a href="main/index/klarifikasi_chat_rekanan/?reqId=<?=$reqId?>" class="list-group-item"> <span class="ft-arrow-right"></span> Pembuktian & Negosiasi</a>
                    <?php
                    }
                  }

                  if ($reqMetodePengadaan == '1' || $reqMetodePengadaan == '2' || $reqMetodePengadaan == '3' || $reqMetodePengadaan == '5' || $reqMetodePengadaan == '7' || $reqMetodePengadaan == '8'|| $reqMetodePengadaan == '10')
                    {
                  ?>

                  <?php
                      if(($sistem_sampul == "1" || $sistem_sampul == "2") && $reqKirimPenawaran == "1") // Evaluasi muncul setelah publish pemenang
                      {
                      // if(($sistem_sampul == "1" || $sistem_sampul == "2") && $reqKirimPenawaran == "1")
                      	if ($publish_ba_evaluasi_sampul1  == "1" && $publish_ba_evaluasi_sampul2  != "1")
                      	{
                      	?>
	                      	<a href="main/index/hasil_eval_rekanan_file_1/?reqId=<?php echo $reqId?>" class="list-group-item"> <span class="ft-arrow-right"></span> Hasil Evaluasi File 1 </a>
                  <?php
                				}

                				if ($publish_ba_evaluasi_sampul2  == "1")
                      	{ ?>
                        	<a href="main/index/hasil_eval_rekanan/?reqId=<?=$reqId?>" class="list-group-item"> <span class="ft-arrow-right"></span> Hasil Evaluasi</a>

                  <?php
                      	}
                      }
                    }

                  if($aktif_pengumuman > 0  || $aktif_pengumuman2 > 0)
                  {
                    $paket_dokumen = new PaketDokumen();
                    $paket_dokumen->selectByParams(array("PAKET_ID" => $reqId, "JENIS_DOKUMEN" => "PENGUMUMAN_PEMENANG"));

                    $paket_dokumen->firstRow();
                    $reqPaketDokumenId = $paket_dokumen->getField("PAKET_DOKUMEN_ID");

                    if($reqPaketDokumenId == '')
                    {
                      $reqMode=  'insert';
                    }
                    else
                      $reqMode = 'update';

                    if ($paket_dokumen->getField("PATH_FILE")=='')
                    {
                    } else
                    {
                    ?>
                      <!-- <li>
                        <div>
                          <a href="main/index/pengumuman_pemenang_rekanan/?reqId=<?=$reqId?>"> <span class="ft-arrow-right"></span>  -->
                          <?php
                            // 1:Tender, 2:Pengadaan langsung, 3:Tender Terbatas, 4:Seleksi Langsung, 5:Penunjukan Langsung, 6:e-Purchasing, 7:Tender Cepat, 8:Kompetisi
                            // switch ($paket_metode_lelang_id) {
                            //   case '2':
                            //   case '5':
                            //     echo "Pengumuman";
                            //     break;

                            //   default:
                            //     echo "Pengumuman Pemenang";
                            //     break;
                            // }
                            ?>
                         <!--  </a>
                        </div>
                      </li>    -->
                  <?php
                    }
                  ?>
                    <a href="main/index/pengumuman_pemenang_rekanan/?reqId=<?=$reqId?>" class="list-group-item"> <span class="ft-arrow-right"></span>
                      <?php
                        // 1:Tender, 2:Pengadaan langsung, 3:Tender Terbatas, 4:Seleksi Langsung, 5:Penunjukan Langsung, 6:e-Purchasing, 7:Tender Cepat, 8:Kompetisi
                        switch ($paket_metode_lelang_id) {
                          case '2':
                          case '5':
                            echo "Pengumuman";
                            break;

                          default:
                            echo "Pengumuman Pemenang";
                            break;
                        }
                        ?>
                    </a>
                  <?php
                  }
                  if (($paket_metode_lelang_id == '1' || $paket_metode_lelang_id == '3'|| $paket_metode_lelang_id == '8' || $paket_metode_lelang_id == '10') && $cekAktif_sanggah == '1')
                  {
                  ?>
                    <a href="main/index/paket_lelang_masa_sanggah_rekanan/?reqId=<?=$reqId?>" class="list-group-item"> <span class="ft-arrow-right"></span>  Masa Sanggah </a>
                  <?php
                  }
                }
              } // if($reqLulusPendaftaran == 1)
            }
          }

          if(((int)$this->USER_TYPE_ID == 3 || (int)$this->USER_TYPE_ID == 11) && $this->USER_LOGIN_ID == $paket_user_id)
          {
						if ($cekPublishPemenang !='1')
						{
            	if(trim($alasan_ulang) == "" && trim($alasan) == "")
            	{
          ?>
								<a onClick="openAdd('main/loadUrl/main/paket_lelang_batal/?reqId=<?=$reqId?>');" class="list-group-item"> <span class="ft-arrow-right"></span>  Batalkan Paket </a>
            <?php
            	}
              if(trim($alasan_ulang) == "" && trim($alasan) == "")
							{
								// 1:Tender, 2:Pengadaan langsung, 3:Tender Terbatas, 4:Seleksi Langsung, 5:Penunjukan Langsung, 6:e-Purchasing, 7:Tender Cepat, 8:Kompetisi
								// if ($paket_metode_lelang_id != 5) { // selain Penunjukan Langsung
								?>
									<a onClick="openAdd('main/loadUrl/main/paket_lelang_ulang/?reqId=<?=$reqId?>');" class="list-group-item"> <span class="ft-arrow-right"></span>  Gagal Paket </a>
						<?php
								// }
							}
        		}
					if (trim($alasan_ulang) != "" || trim($alasan) != "" || $rekanan_id_pemenang!='')
          {
            $paket_ppk = new Paket();
            $paket_ppk->selectByParams(array("A.PAKET_ID" => coalesce($reqId, 0)));
            $paket_ppk->firstRow();
            $ppknya = $paket_ppk->getField("PPK");
						?>
						<!-- <a href="main/loadUrl/report/paket_cetak_pdf/?reqId=<?=$reqId?>" target="_blank"> <span class="ft-arrow-right"></span> Laporan Paket </a> -->
						<a href="main/index/paket_laporan/?reqId=<?=$reqId?>" class="list-group-item"> <span class="ft-arrow-right"></span> Laporan Paket </a>
					<?php
					}

					if ($rekanan_id_pemenang!='')
					{
					?>
						<!-- <a onclick="openAddFrame('main/loadUrl/main/tunjuk_pengelola_kontrak?paketid=<?php // echo $reqId ?>')" class="list-group-item"> <span class="ft-arrow-right"></span> Pilih PJK
              <?php // if ($ppknya == '') { echo $wajib; } else { echo $sudahwajib; }  ?>
						</a> -->
					<?php
					}
          ?>
        <?php

        }

				if((int)$this->USER_TYPE_ID != 6)
        { // selain Penyedia
        ?>
					<a onclick="openAddLg('main/loadUrl/main/rekam_jejak_view?id=<?= $reqPermohonanId ?>&paketid=<?= $reqId ?>')" class="list-group-item"> <span class="ft-arrow-right"></span>  Rekam Jejak </a>
				<?php
				} ?>

        <?php
        if ($this->USER_TYPE_ID == "") {
          if($aktif_pengumuman > 0  || $aktif_pengumuman2 > 0)
          { ?>
            <a href="main/index/pengumuman_pemenang_rekanan/?reqId=<?=$reqId?>" class="list-group-item"> <span class="ft-arrow-right"></span>
            <?php
              // 1:Tender, 2:Pengadaan langsung, 3:Tender Terbatas, 4:Seleksi Langsung, 5:Penunjukan Langsung, 6:e-Purchasing, 7:Tender Cepat, 8:Kompetisi
              switch ($paket_metode_lelang_id) {
                case '2':
                case '5':
                  echo "Pengumuman";
                  break;

                default:
                  echo "Pengumuman Pemenang";
                  break;
              }
              ?>
            </a>
        <?php
          }
        } ?>

      </ul>
    </div>
  </div>
  <?php
  } // if ((int)$this->USER_TYPE_ID != '') { ?>

  <?php
  if ((int)$this->USER_TYPE_ID != '') { // Untuk user login ?>
  <div class="col-md-9 col-sm-9">
  <?php
  } else { ?>
  <div class="col-md-12 col-sm-12">
  <?php
  } ?>
    <div class="card">
      <div class="card-content collapse show border-info border-darken-2">
        <div class="card-body">
          <!-- <h5>Paket Tender Detil</h5> -->
          <!-- <form id="ff" class="easyui-form form-horizontal" method="post" data-options="novalidate:true" enctype="multipart/form-data"> -->
              <?php
              if($reqPaketRekananId == "")
              {}
              else
              { // LOGIN REKANAN
                $paket_keterangan->selectByPaketRekananKeterangan($reqId, $reqPaketRekananId, $this->REKANAN_ID, $arrEvaluasiKualifikasi[$jenis_tahap], $arrDokumenPenawaran[$jenis_tahap]);
              ?>
                  <?php
                  if($alasan == "")
                  {
                    $alertPenyediaPrimary = '';
                    $alertPenyediaDanger  = '';
                  ?>
                    <?php
                    // if (($paket_metode_lelang_id == '1' || $paket_metode_lelang_id == '3' || $paket_metode_lelang_id == '7'))
                    if (($paket_metode_lelang_id == '1' || $paket_metode_lelang_id == '4' || $paket_metode_lelang_id == '7' || $paket_metode_lelang_id == '10')) // 1:Tender Umum, 7:Tender Cepat
                    {
                      $alertPenyediaPrimary .= "Anda telah mendaftar pada <b>".getFormattedDate($reqTanggalDaftar)."</b> dengan <b><i> no. registrasi : ".$reqKodeRekanan.'</i></b><br>';

                    } else
                    {
                      $alertPenyediaPrimary .= "Anda telah diundang paket ini <br>";
                    }
                    ?>

                      <?php
                      if($reqLulusPendaftaran == 0)
                      {
                        $paket_rekanan_daftar = new PaketRekananDaftar();
                        $paket_rekanan_daftar->selectByParamsCatatan2(array("PAKET_REKANAN_ID" => $reqPaketRekananId));
                        $paket_rekanan_daftar->firstRow();
                      ?>
                      <!-- <li class="gagal"> -->
                        <?php /*?>Verifikasi data pendaftaran anda gagal dengan alasan : <?=$reqLulusPendaftaranKeterangan?>. Anda tidak dapat melanjutkan proses lelang.<?php */?>
                        <!-- <div class="alert alert-danger"> -->
                          <!-- <button type="button" class="close" data-dismiss="alert">&times;</button>  -->
                          <?php $alertPenyediaDanger .= "<b>Anda tidak dapat melanjutkan proses selanjutnya.</b>".$paket_rekanan_daftar->getField("LULUS_PENDAFTARAN_KETERANGAN");
                          ?>
                        <!-- </div>     -->
                      <?php
                      } elseif($reqLulusPendaftaran == 2)
                      {
                      ?>
                      <div class="alert alert-warning">
                        <button type="button" class="close" data-dismiss="alert">&times;</button>
                        <?=translate("Data Pendaftaran Kualifikasi sedang di proses, ikuti tahapan dengan benar.", "we are verify your data")?>
                      </div>
                      <?php
                      } else
                      {
                        if($status_dok_kualifikasi1 > 0 || $status_dok_kualifikasi2 > 0)
                        {
                          if($rekanan_evaluasi_admin->getCountByParams(array("PAKET_REKANAN_ID" => $reqPaketRekananId)) > 0)
                          {
                            $alertPenyediaPrimary .= translate("<img src='images/centang.png'> memasukkan data kualifikasi", "You have completed the prequalification data").'<br>';

                          } else
                          {
                            $alertPenyediaPrimary .= translate("<img src='images/uncentang.png'> Memasukkan data kualifikasi", "You have not completed the Qualification data").'<br>';
                          }
                        }

                        if($publish_ba_kualifikasi == "1")
                        {
                          if($reqLulusKualifikasi == 0)
                          {}
                          else
                          {
                              $alertPenyediaPrimary .= translate("<img src='images/centang.png'> Memenuhi Syarat pada tahap kualifikasi", "You have passed the prequalification stage").'<br>';
                          }
                        }

                        // 1:Tender, 2:Pengadaan langsung, 3:Tender Terbatas, 4:Seleksi Langsung, 5:Penunjukan Langsung, 6:e-Purchasing, 7:Tender Cepat, 8:Kompetisi
                        if ($paket_metode_lelang_id != '7') {
                          if($status_aanwitzing > 0)
                          {
                            if($reqAanwijzing == "")
                            {
                          ?>
                            <!-- <div class="alert alert-danger">
                              <button type="button" class="close" data-dismiss="alert">&times;</button>
                                <?=translate("<img src='images/uncentang.png'> Mengikuti Aanwijzing", "You have not completed the Aanwijzing Chat")?>.
                            </div>   -->
                          <?php
                            } else
                            {
                          ?>
                            <!-- <div class="alert alert-primary">
                              <button type="button" class="close" data-dismiss="alert">&times;</button>
                              <?=translate("<img src='images/centang.png'> Mengikuti Aanwijzing", "You have completed the Aanwijzing Chat")?>.
                            </div>  -->
                          <?php
                            }
                          }
                        }

                        if($status_dok_penawaran1 > 0 || $status_dok_penawaran2 > 0)
                        {
                          if($reqKirimPenawaran  == "1")
                          {
                            $alertPenyediaPrimary .= translate("<img src='images/centang.png'> Upload dokumen penawaran", "You have completed the Bidding Documents").'<br>';

                              if($reqKirimPenawaranPassword == "")
                              {
                                $alertPenyediaDanger .= translate("<img src='images/uncentang.png'> Upload Enkripsi Penawaran ".$sampul1, "You have not uploaded bidding document ".$sampul1." password").'<br>';
                              }
                              else
                              {
                                $alertPenyediaPrimary .= translate("<img src='images/centang.png'> Upload Enkripsi Penawaran ".$sampul1, "You have uploaded the bidding document ".$sampul1." password").'<br>';
                              }

                          } else
                          {
                            if($aktif_dok_penawaran1 > 0 || $aktif_dok_penawaran2 > 0)
                            {
                              $alertPenyediaDanger .= translate("<img src='images/uncentang.png'> Upload dokumen penawaran", "You have not completed the bidding documents").'<br>';
                            }
                          }
                        }
                        // if($publish_ba_evaluasi_sampul1  == "1" && $sistem_sampul == "2")
                        if($sistem_sampul == "2")
                        {
                          if($reqLulusPenawaranSampul1 == "1")
                          {
                            $alertPenyediaPrimary .= translate("<img src='images/centang.png'> Memenuhi Syarat evaluasi penawaran File 1", "You have passed the bid evaluation of Cover 1").'<br>';

                          } else if($reqLulusPenawaranSampul1 == "0")
                          {
                            $alertPenyediaDanger .= translate("<img src='images/uncentang.png'> Tidak Memenuhi Syarat pada tahap evaluasi penawaran File 1", "You have failed the bid evaluation of Cover 1").'<br>';
                          }

                          if($reqKirimPenawaran == "1")
                  				{
	                          if($reqKirimPenawaranPassword2 == "")
	                          {
	                            $alertPenyediaDanger .= translate("<img src='images/uncentang.png'> Upload Enkripsi Penawaran File 2", "You have not uploaded bidding document cover 2 password").'<br>';
	                          } else
	                          {
	                            $alertPenyediaPrimary .= translate("<img src='images/centang.png'> Upload Enkripsi Penawaran File 2", "You have uploaded the bidding document cover 2 password").'<br>';
	                          }
	                        }
                        }
                      }
                    ?>


                      <?php
                      if($publish_ba_kualifikasi == "1")
                      {
                        if($reqLulusKualifikasi == 0)
                        {
                          $alertPenyediaDanger .= translate("<img src='images/uncentang.png'> Tidak Memenuhi Syarat pada tahap kualifikasi", "You failed the prequalification stage")?>, <?=translate("Alasan", "Reason").$reqLulusKualifikasiKeterangan.'<br>';
                        }
                      }

                      while($paket_keterangan->nextRow())
                      {
                        if($paket_keterangan->getField("KETERANGAN") == "")
                        {}
                        else
                        {
                      ?>
                        <div class="alert alert-danger">
                          <button type="button" class="close" data-dismiss="alert">&times;</button>
                            <?=$paket_keterangan->getField("KETERANGAN")?>.
                        </div>
                      <?php
                          }
                      }
                      ?>
                    <!-- </ul> -->
                     <?php
                  }
                  else
                  {
                    $alertPenyediaDanger .= translate("Paket dibatalkan dengan alasan", "Tender canceled, with reason").' : '.$alasan.'<br>';
                  }
                  ?>
                <!-- </div> -->
              <?php
              // Manampilkan alert
              if ($alertPenyediaDanger != '') {
                echo '<div class="alert alert-danger">
                        <button type="button" class="close" data-dismiss="alert">&times;</button>'.
                          $alertPenyediaDanger
                        .'</div>';
              }

              if ($alertPenyediaPrimary != '') {
                echo '<div class="alert alert-primary">
                        <button type="button" class="close" data-dismiss="alert">&times;</button>'.
                          $alertPenyediaPrimary
                        .'</div>';
              }

              }
              ?>

              <!-- <div class="alert alert-icon-left alert-arrow-left alert-info mb-2" role="alert"> -->
                  <!-- <span class="alert-icon"><i class="fa fa-info"></i></span> -->
                  <h4 style="color: #000; font-weight: bold"><?=$paket->getField("NAMA")?></h4>
              <hr>
              <!-- </div> -->
              <table class="table table-bordered table-hover">
                <tbody>
                	<?php
                	if ($paket_metode_lelang_id == '2' || $paket_metode_lelang_id == '3' || $paket_metode_lelang_id == '5' || $paket_metode_lelang_id == '8')
                  {
                  	if ($this->USER_LOGIN_ID == $paket_user_id || $idPanitia > 0) {
	                		if ($paket->getField("PUBLISH_PAKET")  != '1')
	                		{
	                			$this->load->library("libvalidasi"); $libvalidasi = new libvalidasi();
		                    $countCekValidasi = $libvalidasi->cekValidasiPublishPaket($reqId);
		                    if ($countCekValidasi['count'] > 0) { }
		                    else
		                    {
	                			?>
			                	<tr>
			                		<td colspan="4">
			                  		<button type="button" id="btnKirim" class="<?= CLASS_BTN_SUCCESS ?>"> <?= BTN_KIRIM ?> Undangan</button>
			                		</td>
			                	</tr>
	                	<?php
	                			}
	                		}
	                	}
                	}
                	?>

                  <!-- <tr>
                    <td width="25%" colspan="2">Tgl Pembuatan Paket</td>
                    <td width="25%" colspan="2"><?php //getFormattedDate($paket->getField("TANGGAL_TAHAP"))?></td>
                  </tr> -->
                  <tr>
                    <td width="25%" colspan="2">
                      <small><i class="fa fa-calendar"></i> Tahun Anggaran</small> <br>
                      <?=getYear($paket->getField("TAHUN_ANGGARAN"))?>
                    </td>
                    <td width="25%" colspan="2">
                      <small><i class="fa fa-map-marker"></i> Lokasi Pekerjaan</small> <br>
                      <?=$paket->getField("LOKASI")?>
                    </td>
                  </tr>
                  <tr>
                    <td width="25%" colspan="2">
                      <small><i class="fa fa-inbox"></i> Jenis Pengadaan</small> <br>
                      <?= str_replace("Katalog", "Purchasing", $paket->getField("PAKET_JENIS")) ?>
                    </td>
                    <td width="25%" colspan="2">
                      <small><i class="fa fa-tag"></i> Metode Pengadaan</small> <br>
                      <?=$paket->getField("METODE_LELANG")?>
                      <?php
                      if($paket->getField("PAKET_METODE_LELANG_ID") == '1') {
                      	if ($paket->getField("MULTI_PEMENANG") == '1') {
                      		echo '&nbsp;<span style="font-size:11px">( Pemanang lebih dari satu )</span>';
                      	}
                      }  ?>
                    </td>
                  </tr>
                  <?php
                  if ($paket_metode_lelang_id != '6' && $paket_metode_lelang_id != '9')
                  { // bukan Purchasing/Pembelian Langsung & bukan pembelian offline ?>
                  <tr>
                    <!-- <td width="25%" colspan="2">
                      <small><i class="fa fa-clipboard"></i> Metode Kualifikasi</small> <br>
                      <?=$paket->getField("METODE_KUALIFIKASI")?>
                    </td> -->
                    <td width="25%" colspan="2">
                      <small><i class="fa fa-folder-open"></i> Metode Penyampaian Penawaran</small> <br>
                      <?=$paket->getField("SISTEM_SAMPUL")?> File
                    </td>
                    <td width="25%" colspan="2">
                      <small><i class="fa fa-exchange"></i> Metode Evaluasi</small> <br>
                      <?=$paket->getField("METODE_EVALUASI")?>
                    </td>
                  </tr>
                  <?php
                  } ?>

                  <?php
                  if ($paket_metode_lelang_id != '6' && $paket_metode_lelang_id != '9')
                  { // bukan Purchasing/Pembelian Langsung & bukan pembelian offline ?>
                  <tr>
                    <td width="25%" colspan="4">
                      <small><i class="fa fa-file-text"></i> Kualifikasi Usaha</small> <br>
                      <?=$paket->getField("REKANAN_KUALIFIKASI")?>
                    </td>
                    <!-- <td width="25%" colspan="2"> -->
                      <!-- <small><i class="fa fa-clock-o"></i> Sistem Negosiasi</small> <br> -->
                      <?php
                      // if ($paket->getField("BIDDING") == 1) {
                      //   echo 'e-Reverse Auction '.$paket->getField("BIDDING_MENIT").' menit';
                      // } else {
                      //   echo "Chatting Nego";
                      // }
                      ?>
                    <!-- </td> -->
                  </tr>
                  <?php
                  } ?>
                  <?php
                  // if($this->USER_TYPE_ID == 3 || $this->USER_TYPE_ID == 7 ) // PANITIA & EKSEKUTIF
                  // {
                  if ($paket_metode_lelang_id == '1' || $paket_metode_lelang_id == '2' || $paket_metode_lelang_id == '3' || $paket_metode_lelang_id == '4' || $paket_metode_lelang_id == '10') // ditampilkan hanya untuk Tender
                  {
                  ?>
                  <tr>
                    <td width="25%" colspan="4">
                      <small><i class="fa fa-money"></i> Harga Perkiraan Sendiri</small> <br>
                      <?=$paket->getField("NILAI_MATA_UANG")?> <?=currencyToPage($paket->getField("NILAI_OWNER_ESTIMATE"))?>
                    </td>
                    </td>
                  </tr>
                  <?php
                  } else {
                    if ($this->USER_TYPE_ID != '' && $this->USER_TYPE_ID != '6') { // bukan untuk penyedia
                  ?>
                  <tr>
                    <td width="25%" colspan="4">
                      <small><i class="fa fa-money"></i> Harga Perkiraan Sendiri</small> <br>
                      <?=$paket->getField("NILAI_MATA_UANG")?> <?=currencyToPage($paket->getField("NILAI_OWNER_ESTIMATE"))?>
                    </td>
                    </td>
                  </tr>
                  <?php
                    }
                  }
                  // }
                  ?>
                  <?php
                  if ($paket_metode_lelang_id != '6' && $paket_metode_lelang_id != '9')
                  { // bukan Purchasing/Pembelian Langsung & bukan pembelian offline 
                  	if($multi_bidang_usaha == '1') {
                  		$keterangan_multi = 'Keseluruhan terpenuhi';
                  	} else {
                  		$keterangan_multi = 'Salah satu terpenuhi';
                  	}
                  	?>
                  <tr>
                    <td width="25%" colspan="4">
                      <small><i class="fa fa-suitcase"></i> Bidang / Sub Bidang<sup>* *</sup></small><br>
                      <?php if(trim($paket->getField("BIDANG_USAHA")) == "()")
                          echo "-";
                         else
                          echo str_replace("---"," <br/> ", $paket->getField("BIDANG_USAHA"));
                          // echo $paket->getField("BIDANG_USAHA"); ?> <br>
                      <?= '<span class="badge badge-info">* * '.$keterangan_multi.'</span>' ?>
                    </td>
                  </tr>
                  <tr>
                    <td width="25%" colspan="4">
                      <small><i class="fa fa-th-list"></i> Persyaratan Peserta</small><br>
                      <?=$paket->getField("URAIAN")?>
                    </td>
                  </tr>
                  <?php
                  } else { ?>
                    <tr>
                      <td width="25%" colspan="4">
                        <small><i class="fa fa-th-list"></i> Keterangan</small><br>
                        <?=$paket->getField("URAIAN")?>
                      </td>
                    </tr>
                  <?php
                }
                    // echo $reqPermohonanId.'-'.$reqPL.'-'.$reqMetodePengadaan;
                    if (($reqPL == '0' && $reqMetodePengadaan == '2') || $reqMetodePengadaan != '2') { // Pengadaan langsung <= 300jt
                   ?>
                  <!-- <tr>
                    <td width="25%" colspan="4">
                      <div class="alert alert-info">PANITIA</div>
                      <table class="table table-hover">
                        <tr>
                          <td width="15%"><small><i class="fa fa-building-o"></i> Unit Kerja </small></td>
                          <td width="85%">: <?=$paket->getField("UNIT_KERJA")?></td>
                        </tr>
                        <tr>
                          <td><small><i class="fa fa-envelope-o"></i> Email </small></td>
                          <td>: <?=$paket->getField("EMAIL")?></td>
                        </tr>
                        <tr>
                          <td><small><i class="fa fa-phone"></i> Telepon </small></td>
                          <td>: <?=$paket->getField("TELEPON")?></td>
                        </tr>
                        <tr>
                          <td><small><i class="fa fa-map-marker"></i> Alamat </small></td>
                          <td>: <?=$paket->getField("ALAMAT")?></td>
                        </tr>
                      </table>
                    </td>
                  </tr>  -->
                  <?php
                  } ?>
                </tbody>
              </table>
              <?php
              if ($paket_metode_lelang_id != '6' && $paket_metode_lelang_id != '9')
              { // bukan Purchasing/Pembelian Langsung & bukan Pembelian Offline ?>

              <!---------------------- TAHAPAN LELANG ---------------------->
              <div class="card mb-1 border-blue border-darken-1">
                <div class="card-content">
                  <div class="p-1">
                    <div class="alert bg-primary alert-icon-left alert-arrow-left alert-dismissible mb-2" role="alert">
                      <span class="alert-icon"><i class="fa fa-th"></i></span>  <strong>Jadwal <?= $paket_metode_nama ?></strong>
                    </div>
                    <div class="table-responsive">
                      <table class="table table-bordered table mb-0" style="margin-bottom:20px">
                        <tbody>
                         <?php
													$this->load->library("libjadwal");

                        	$libjadwal = new libjadwal();
  												$getJadwalPaket = $libjadwal->getJadwal($reqId);
  												echo $getJadwalPaket;
                          ?>
                        </tbody>
                      </table>

                      <!-- Informasi Reschedule -->
				              <?php
											$this->load->library("paketinfo"); $paketInfo = new paketinfo();
				              $paketInfo->getPaket($reqId);
											$reqMetodeLelangId = $paketInfo->metode_lelang_id;
											$reqUUID = $paketInfo->uuid;
											$reqPublishPaket = $paketInfo->publish_paket;
											$reqRescheduleKe = $paketInfo->reschedule_ke ? $paketInfo->reschedule_ke : 0;
											$reqReschedule1 = $paketInfo->reschedule_1;
											$reqReschedule2 = $paketInfo->reschedule_2;
											$reqReschedule3 = $paketInfo->reschedule_3;
											$reqReschedule4 = $paketInfo->reschedule_4;
											$reqReschedule5 = $paketInfo->reschedule_5;
											$reqReschedule6 = $paketInfo->reschedule_6;
											$reqReschedule7 = $paketInfo->reschedule_7;
											$reqReschedule8 = $paketInfo->reschedule_8;
											$reqReschedule9 = $paketInfo->reschedule_9;
											$reqReschedule10 = $paketInfo->reschedule_10;
					                $html = '';
					            // if ($reqRescheduleKe > 0) {
					            //     $html .= '<div class="alert alert-success" style="color:#fff">';
					            //     $html .= '<p style="color:#000">
					            //               <b>Reschedule Jadwal telah dilakukan dengan alasan sebagai berikut: </b> <br>';
					            //     $html .= $reqReschedule1 ? 'Reschedule 1 : <u>'.$reqReschedule1.'</u><br>' : '';
					            //     // $html .= '<a onClick="openAdd(\'main/loadUrl/main/reschedule?reqId='.$reqId.'&reqKe='.$reqRescheduleKe.'&back='.$setKembali.'\');"><b><i><u> Lihat detail reschedule 1</u></i></b> </a><br>';
					            //     $html .= $reqReschedule2 ? 'Reschedule 2 : <u>'.$reqReschedule2.'</u><br>' : '';
					            //     $html .= $reqReschedule3 ? 'Reschedule 3 : <u>'.$reqReschedule3.'</u><br>' : '';
					            //     $html .= $reqReschedule4 ? 'Reschedule 4 : <u>'.$reqReschedule4.'</u><br>' : '';
					            //     $html .= $reqReschedule5 ? 'Reschedule 5 : <u>'.$reqReschedule5.'</u><br>' : '';
					            //     $html .= $reqReschedule6 ? 'Reschedule 6 : <u>'.$reqReschedule6.'</u><br>' : '';
					            //     $html .= $reqReschedule7 ? 'Reschedule 7 : <u>'.$reqReschedule7.'</u><br>' : '';
					            //     $html .= $reqReschedule8 ? 'Reschedule 8 : <u>'.$reqReschedule8.'</u><br>' : '';
					            //     $html .= $reqReschedule9 ? 'Reschedule 9 : <u>'.$reqReschedule9.'</u><br>' : '';
					            //     $html .= $reqReschedule10 ? 'Reschedule 10 : <u>'.$reqReschedule10.'</u>' : '';
					            //     $html .= '</p>';
					            //     $html .=  '</div>';
					            // }

					            //     echo $html;
					             ?>
				              <!-- End Informasi Reschedule -->

                    </div>
                  </div>
                </div>
              </div>
              <?php
              } ?>

          <!-- </form> -->
          <div class="form-actions">
            <?php
            if ($this->USER_TYPE_ID != '6') {
              // 1-e-Tender, 3-Tender Terbatas ,7-e-Tender Cepat, 2-Pengadaan Langsung ,5-Penunjukan Langsung Cepat, 6-Pembelian langsung
              switch ($paket_metode_lelang_id) {
                case '1':
                case '3':
                case '7':
                case '10':
                  echo '<a href="main/index/tender" class="'.CLASS_BTN_DANGER.' mr-1"> '.BTN_KEMBALI.' </a>';
                  break;
                case '2':
                case '5':
                case '11':
                  echo '<a href="main/index/tendernon" class="'.CLASS_BTN_DANGER.' mr-1"> '.BTN_KEMBALI.' </a>';
                  break;
                case '6':
                  if ($this->USER_TYPE_ID == '11') {
                    echo '<a href="main/index/pembelian_langsung" class="'.CLASS_BTN_DANGER.' mr-1"> '.BTN_KEMBALI.' </a>';
                  }
                  break;

                default:
                  break;
              }
            }
             ?>
             <?php
              // if ($this->USER_TYPE_ID != "") {
                if($aktif_pengumuman > 0  || $aktif_pengumuman2 > 0)
                {
                  // $this->load->model("PaketRekanan");
                  // $paket_rekanan_check = new PaketRekanan();
                  // $check = $paket_rekanan_check->getCountByParams(array("PAKET_ID" => $reqId, "REKANAN_ID" => $this->REKANAN_ID)); // Hanya pemenang yang bisa lihat
                  // if($check > 0 && $alasan == "")
                  // {
										?>
                  <a href="main/index/pengumuman_pemenang_rekanan/?reqId=<?=$reqId?>" class="<?= CLASS_BTN_PRIMARY ?> mr-1">
                  <?php
                    // 1:Tender, 2:Pengadaan langsung, 3:Tender Terbatas, 4:Seleksi Langsung, 5:Penunjukan Langsung, 6:e-Purchasing, 7:Tender Cepat, 8:Kompetisi
                    switch ($paket_metode_lelang_id) {
                      case '2':
                      case '5':
                        echo "Pengumuman";
                        break;

                      default:
                        echo "Pengumuman Pemenang";
                        break;
                    }
                    ?>
                  </a>
              <?php
                  // }
                }
              //} ?>

              <?php
            if ($reqRescheduleKe >= 1) { ?>
            <a <?php echo 'onClick="openAdd(\'main/loadUrl/main/reschedule_rekamjejak?reqId='.$reqId.'\');"'; ?> class="<?= CLASS_BTN_DARK ?> mr-1 text-white" target="_blank"> <i class="fa fa-paw"></i> Rekam Jejak Reschedule Jadwal </a>
            <?php
            } ?>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<?php
// if($reqKirimPenawaran  == "1") { }
// else {
if($reqPaketRekananId != "") // KHUSUS PENYEDIA YANG SUDAH MENDAFTAR
{
	if ($sistem_sampul == '1') {
		if($reqKirimPenawaranPassword == "")
    {
			echo '<div class="alert alert-info" style="position:fixed; z-index:9999; bottom:1%; width: 98%; margin: 0 auto; left: 1%; text-align:center; font-weight:bold">. : : Untuk mengantisipasi terjadinya web traffic, kendala jaringan dan  kegagalan upload dokumen penawaran pada Tender, <br> kami himbau kepada para Penyedia (Vendor) Rekanan '.SYSTEM_NAME_PT.'  untuk melakukan upload dokumen pada H-2 atau H-1 <br> dari batas waktu tanggal berakhirnya pemasukan dokumen penawaran. : : .</div>';
    }
	}

	if ($sistem_sampul == '2') {
		if($reqKirimPenawaranPassword == "" || $reqKirimPenawaranPassword2 == "")
    {
			echo '<div class="alert alert-info" style="position:fixed; z-index:9999; bottom:1%; width: 98%; margin: 0 auto; left: 1%; text-align:center; font-weight:bold">. : : Untuk mengantisipasi terjadinya web traffic, kendala jaringan dan  kegagalan upload dokumen penawaran pada Tender, <br> kami himbau kepada para Penyedia (Vendor) Rekanan '.SYSTEM_NAME_PT.'  untuk melakukan upload dokumen pada H-2 atau H-1 <br> dari batas waktu tanggal berakhirnya pemasukan dokumen penawaran. : : .</div>';
    }
	}
}
// } ?>
