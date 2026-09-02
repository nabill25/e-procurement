<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * @package 	eProcurement Application
 * @author 		eproc2025
 * @since 		25. Version 3.1
 *
 * Controller AJAX untuk fitur baru: Pencatatan Tindak Lanjut Kelengkapan
 * Dokumen Penyedia. Pola constructor/guard meniru controller *_json.php lain.
 *
 * Endpoint:
 *  - kirimCatatan()      sisi verifikator (Validasi Rekanan)
 *  - tandaiSelesai()     sisi verifikator (Validasi Rekanan)
 *  - konfirmasiLengkap() sisi penyedia (Konfirmasi Pendaftaran)
 *  - timeline()          render potongan HTML timeline (dipakai reload widget)
 */
class rekanan_tindak_lanjut_json extends CI_Controller {

	function __construct()
	{
		parent::__construct();
		$this->db->query("SET TIME ZONE 'Asia/Jakarta'");

		if (!$this->kauth->getInstance()->hasIdentity()) {
			redirect('Login');
		}

		$this->USER_LOGIN_ID = $this->kauth->getInstance()->getIdentity()->USER_LOGIN_ID;
		$this->USER_TYPE_ID  = $this->kauth->getInstance()->getIdentity()->USER_TYPE_ID;
		$this->REKANAN_ID    = $this->kauth->getInstance()->getIdentity()->REKANAN_ID;
	}

	/**
	 * Verifikator kirim catatan minta penyedia melengkapi berkas.
	 * Hanya untuk user internal VMS (checker/rekomendator/validator).
	 */
	function kirimCatatan()
	{
		if (!in_array($this->USER_TYPE_ID, array('2', '18', '19', '26', 2, 18, 19, 26), true)) {
			echo json_encode(array("status" => "gagal", "pesan" => "Anda tidak berhak melakukan aksi ini."));
			return;
		}

		$reqRekananId = $this->input->post("reqRekananId");
		$reqCatatan   = trim($this->input->post("reqCatatan"));

		if (!$reqRekananId || $reqCatatan === '') {
			echo json_encode(array("status" => "gagal", "pesan" => "Penyedia dan catatan wajib diisi."));
			return;
		}

		$this->load->library("libtindaklanjut");
		$lib = new libtindaklanjut();
		$res = $lib->verifikatorMintaLengkapi($reqRekananId, $reqCatatan);

		if (!$res['ok']) {
			echo json_encode(array("status" => "gagal", "pesan" => "Catatan gagal disimpan. Cek log aplikasi."));
			return;
		}
		echo json_encode(array(
			"status" => "sukses",
			"pesan"  => $res['email']
				? "Catatan tersimpan. Email pemberitahuan terkirim ke penyedia."
				: "Catatan tersimpan. Email ke penyedia GAGAL terkirim (lihat riwayat), status tetap tercatat.",
		));
	}

	/**
	 * Verifikator menandai dokumen penyedia sudah lengkap/oke (menutup siklus).
	 */
	function tandaiSelesai()
	{
		if (!in_array($this->USER_TYPE_ID, array('2', '18', '19', '26', 2, 18, 19, 26), true)) {
			echo json_encode(array("status" => "gagal", "pesan" => "Anda tidak berhak melakukan aksi ini."));
			return;
		}

		$reqRekananId = $this->input->post("reqRekananId");
		$reqCatatan   = trim($this->input->post("reqCatatan"));

		if (!$reqRekananId) {
			echo json_encode(array("status" => "gagal", "pesan" => "Penyedia wajib diisi."));
			return;
		}

		$this->load->library("libtindaklanjut");
		$lib = new libtindaklanjut();
		$res = $lib->verifikatorTandaiSelesai($reqRekananId, $reqCatatan);

		echo json_encode($res['ok']
			? array("status" => "sukses", "pesan" => "Dokumen ditandai terverifikasi.")
			: array("status" => "gagal", "pesan" => "Gagal menyimpan. Cek log aplikasi."));
	}

	/**
	 * Penyedia konfirmasi sudah melengkapi berkas sesuai catatan verifikator.
	 * Hanya boleh untuk REKANAN_ID milik penyedia yang sedang login.
	 */
	function konfirmasiLengkap()
	{
		$reqRekananId = $this->input->post("reqRekananId");
		$reqCatatan   = trim($this->input->post("reqCatatan"));

		if (!$reqRekananId) {
			echo json_encode(array("status" => "gagal", "pesan" => "Data tidak lengkap."));
			return;
		}

		// Cegah penyedia A konfirmasi atas nama penyedia B.
		if ((string) $reqRekananId !== (string) $this->REKANAN_ID) {
			echo json_encode(array("status" => "gagal", "pesan" => "Anda hanya bisa konfirmasi untuk perusahaan sendiri."));
			return;
		}

		$this->load->library("libtindaklanjut");
		$lib = new libtindaklanjut();
		$res = $lib->penyediaKonfirmasiLengkap($reqRekananId, $reqCatatan);

		if (!$res['ok']) {
			echo json_encode(array("status" => "gagal", "pesan" => "Konfirmasi gagal disimpan. Cek log aplikasi."));
			return;
		}
		echo json_encode(array(
			"status" => "sukses",
			"pesan"  => $res['email']
				? "Konfirmasi tersimpan. Verifikator sudah diberi tahu lewat email untuk memeriksa ulang."
				: "Konfirmasi tersimpan. Verifikator akan memeriksa ulang dokumen Anda.",
		));
	}

}
