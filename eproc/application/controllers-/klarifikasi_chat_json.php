<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * @package 	eProcurement Application
 * @author 		eproc2025
 * @since 		25. Version 3.1
 * 
 */

include_once("functions/string.func.php");
include_once("functions/date.func.php");

class klarifikasi_chat_json extends CI_Controller {

	function __construct() {
		parent::__construct(); $this->db->query("SET TIME ZONE 'Asia/Jakarta'");

		//kauth
		if (!$this->kauth->getInstance()->hasIdentity())
		{
			// trow to unauthenticated page!
			redirect('Login');
		}

		/* GLOBAL VARIABLE */
		$this->USER_LOGIN_ID = $this->kauth->getInstance()->getIdentity()->USER_LOGIN_ID;
		$this->ID = $this->kauth->getInstance()->getIdentity()->REKANAN_ID;
		$this->REKANAN_KODE = $this->kauth->getInstance()->getIdentity()->REKANAN_KODE;
	}

	function dokumen_klarifikasi_rekanan()
	{
		$this->load->library("kauth");  $userLogin = new kauth();
		$this->load->library("paketinfo"); $paketInfo = new paketinfo();
		$this->load->model("PaketKlarifikasi");
		$this->load->library("FileHandler");

		$paket_klarifikasi = new PaketKlarifikasi();
		$file = new FileHandler();
		// echo "<pre>"; print_r($this->input->post()); die();
		$reqMode = $this->input->post("reqMode");
		$reqId = $this->input->post("reqId");
		$reqCetak= $this->input->post('reqCetak');
		$reqNamaDokumen= $this->input->post('reqNamaDokumen');
		$reqKeterangan= $this->input->post('reqKeterangan');
		$reqLinkFile= $_FILES['reqLinkFile'];
		$reqBayar= 1;
		$reqDokumenId = $this->input->post('reqDokumenId');
		$submitSimpan= $this->input->post('submitSimpan');

		$FILE_DIR = "uploads/klarifikasi/";

		/* UPLOAD FILE */
		$renameFile = md5(date("dmYHis").$reqLinkFile['name'].$this->ID).".".getExtension($reqLinkFile['name']);
		if($file->uploadToDir('reqLinkFile', $FILE_DIR, $renameFile))
		{
			$insertLinkFilesSize = $file->uploadedSize;
			$insertLinkFilesExe =  $file->uploadedExtension;
			$insertLinkFile =  $renameFile;
		}
		else
		{
			$insertLinkFilesSize = $reqLinkFileTempUkuran;
			$insertLinkFilesExe =  $reqLinkFileTempTipe;
			$insertLinkFile =  $reqLinkFileTemp;
		}
		/* END UPLOAD FILE */ 
		$paket_klarifikasi->setField("PAKET_ID", $reqId);
		$paket_klarifikasi->setField("KETERANGAN", $reqKeterangan);
		$paket_klarifikasi->setField("REKANAN_USER_ID", $this->ID);
		$paket_klarifikasi->setField("REKANAN_KODE", $this->REKANAN_KODE);
		$paket_klarifikasi->setField("STATUS", (int)$reqBayar);
		$paket_klarifikasi->setField("PARENT_ID", 0);
		$paket_klarifikasi->setField('CREATED_BY', $this->USER_LOGIN_ID);
		$paket_klarifikasi->insertRekanan();

		echo "Data berhasil disimpan";
	}

	function dokumen_aanwijzing_tanggapan()
	{
		$this->load->library("kauth");  $userLogin = new kauth();
		$this->load->library("paketinfo"); $paketInfo = new paketinfo();
		$this->load->model("PaketKlarifikasi");
		$this->load->library("FileHandler");

		$paket_klarifikasi = new PaketKlarifikasi();
		$file = new FileHandler();
		// echo "<pre>"; print_r($this->input->post()); die();
		$reqMode = $this->input->post("reqMode");
		$reqId = $this->input->post("reqId");
		$reqCetak= $this->input->post('reqCetak');
		$reqNamaDokumen= $this->input->post('reqNamaDokumen');
		$reqKeterangan= $this->input->post('reqKeterangan');
		$reqLinkFile= $_FILES['reqLinkFile'];
		$reqBayar= 1;
		$reqDokumenId = $this->input->post('reqPaketDokumenId'); 
		$reqRekananUserId= $this->input->post('reqRekananUserId');
		$submitSimpan= $this->input->post('submitSimpan');

		$FILE_DIR = "uploads/klarifikasi/";

		/* UPLOAD FILE */
		$renameFile = md5(date("dmYHis").$reqLinkFile['name'].$this->ID).".".getExtension($reqLinkFile['name']);
		if($file->uploadToDir('reqLinkFile', $FILE_DIR, $renameFile))
		{
			$insertLinkFilesSize = $file->uploadedSize;
			$insertLinkFilesExe =  $file->uploadedExtension;
			$insertLinkFile =  $renameFile;
		}
		else
		{
			$insertLinkFilesSize = $reqLinkFileTempUkuran;
			$insertLinkFilesExe =  $reqLinkFileTempTipe;
			$insertLinkFile =  $reqLinkFileTemp;
		}
		/* END UPLOAD FILE */

		$paket_klarifikasi->setField("PAKET_ID", $reqId);
		$paket_klarifikasi->setField("NAMA", 'Dokumen aanwijzing');
		$paket_klarifikasi->setField("UKURAN", $insertLinkFilesSize);
		$paket_klarifikasi->setField("TIPE", $insertLinkFilesExe);
		$paket_klarifikasi->setField("PATH_FILE", $insertLinkFile); 
		$paket_klarifikasi->setField("KETERANGAN", $reqKeterangan);
		$paket_klarifikasi->setField("REKANAN_USER_ID", $reqRekananUserId);
		$paket_klarifikasi->setField("STATUS", (int)$reqBayar);
		$paket_klarifikasi->setField("PARENT_ID", (int)$reqDokumenId);
		$paket_klarifikasi->setField("USER_LOGIN_ID", $this->USER_LOGIN_ID);
		$paket_klarifikasi->setField('CREATED_BY', $this->USER_LOGIN_ID);

		if ($insertLinkFilesSize) {
			$paket_klarifikasi->insert();
		} else {
			$paket_klarifikasi->insertNoFile();
			// $paket_klarifikasi->insert();
		}

		echo "Data berhasil disimpan";
	}

	// belum di pakai
	function dokumen_aanwijzing_ba()
	{
		$this->load->library("kauth");  $userLogin = new kauth();
		$this->load->library("paketinfo"); $paketInfo = new paketinfo();
		$this->load->model("PaketKlarifikasi");
		$this->load->library("FileHandler");

		$paket_klarifikasi = new PaketKlarifikasi();
		$file = new FileHandler();
		// echo "<pre>"; print_r($this->input->post()); die();
		$reqMode = $this->input->post("reqMode");
		$reqId = $this->input->post("reqId");
		$reqCetak= $this->input->post('reqCetak');
		$reqNamaDokumen= $this->input->post('reqNamaDokumen');
		$reqKeterangan= $this->input->post('reqKeterangan');
		$reqLinkFile= $_FILES['reqLinkFile'];
		$reqBayar= 11;
		$reqDokumenId = $this->input->post('reqPaketDokumenId'); 
		$reqRekananUserId= $this->input->post('reqRekananUserId');
		$submitSimpan= $this->input->post('submitSimpan');

		$FILE_DIR = "uploads/klarifikasi/";

		/* UPLOAD FILE */
		$renameFile = md5(date("dmYHis").$reqLinkFile['name'].$this->ID).".".getExtension($reqLinkFile['name']);
		if($file->uploadToDir('reqLinkFile', $FILE_DIR, $renameFile))
		{
			$insertLinkFilesSize = $file->uploadedSize;
			$insertLinkFilesExe =  $file->uploadedExtension;
			$insertLinkFile =  $renameFile;
		}
		else
		{
			$insertLinkFilesSize = $reqLinkFileTempUkuran;
			$insertLinkFilesExe =  $reqLinkFileTempTipe;
			$insertLinkFile =  $reqLinkFileTemp;
		}
		/* END UPLOAD FILE */

		$paket_klarifikasi->setField("PAKET_ID", $reqId);
		$paket_klarifikasi->setField("NAMA", 'Dokumen aanwijzing');
		$paket_klarifikasi->setField("UKURAN", $insertLinkFilesSize);
		$paket_klarifikasi->setField("TIPE", $insertLinkFilesExe);
		$paket_klarifikasi->setField("PATH_FILE", $insertLinkFile); 
		$paket_klarifikasi->setField("KETERANGAN", $reqKeterangan);
		$paket_klarifikasi->setField("REKANAN_USER_ID", $reqRekananUserId);
		$paket_klarifikasi->setField("STATUS", (int)$reqBayar);
		$paket_klarifikasi->setField("PARENT_ID", (int)$reqDokumenId);
		$paket_klarifikasi->setField("USER_LOGIN_ID", $this->USER_LOGIN_ID);

		if ($insertLinkFilesSize) {
			$paket_klarifikasi->insert();
		} else {
			$paket_klarifikasi->insertNoFile();
			// $paket_klarifikasi->insert();
		}

		echo "Data berhasil disimpan";
	}

	function updateChecklistPenawaran()
	{

		$id = $this->input->get("id");
		$status = $this->input->get("status");
		$catatan = $this->input->get("catatan");

		$this->load->model("PaketDokumen");
		$updateVerif = new PaketDokumen();

		$updateVerif->setField('VERIFIKASI', $status);
		$updateVerif->setField('CATATAN', $catatan);
		$updateVerif->setField('PAKET_DOKUMEN_ID', $id);
		$updateVerif->setField('CREATED_BY', $this->USER_LOGIN_ID); 

		$update = $updateVerif->updateVerifikasi();
		if ($update) {
			$arrJson['PESAN'] = 'Data berhasil disimpan';
			$arrJson['RESPONSE'] = 'Sukses';
		} else {
			$arrJson['PESAN'] = 'Data gagal disimpan';
			$arrJson['RESPONSE'] = 'Gagal';
		}

		echo json_encode($arrJson);
	}

	function delete()
	{
		$this->load->library("paketinfo"); $paketInfo = new paketinfo();
		$this->load->model("PaketKlarifikasi");

		$paket_klarifikasi = new PaketKlarifikasi();

		$reqId = $this->input->get("reqId");

		$paket_klarifikasi->setField("PAKET_KLARIFIKASI_ID", $reqId);
		if($paket_klarifikasi->delete())
		{

		}

	}

}
?>
