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

class paket_sanggah_json extends CI_Controller {

	function __construct() {
		parent::__construct(); $this->db->query("SET TIME ZONE 'Asia/Jakarta'");

		//kauth
		if (!$this->kauth->getInstance()->hasIdentity())
		{
			// trow to unauthenticated page!
			//redirect('Login');
		}

		/* GLOBAL VARIABLE */
		$this->ID = $this->kauth->getInstance()->getIdentity()->REKANAN_ID;
	}

	function dokumen_sanggah_rekanan()
	{
		$this->load->library("kauth");  $userLogin = new kauth();
		$this->load->library("paketinfo"); $paketInfo = new paketinfo();
		$this->load->model("PaketDokumen");
		$this->load->library("FileHandler");

		$paket_dokumen = new PaketDokumen();
		$file = new FileHandler();
		// echo "<pre>"; print_r($this->input->post()); die();
		$reqMode = $this->input->post("reqMode");
		$reqId = $this->input->post("reqId");
		$reqCetak= $this->input->post('reqCetak');
		$reqNamaDokumen= $this->input->post('reqNamaDokumen');
		$reqKeterangan= $this->input->post('reqKeterangan');
		$reqLinkFile= $_FILES['reqLinkFile'];
		$reqBayar= $this->input->post('reqBayar');
		$reqDokumenId = $this->input->post('reqDokumenId');
		$submitSimpan= $this->input->post('submitSimpan');

		$FILE_DIR = "uploads/lelang/";

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
		$paket_dokumen->setField("PAKET_ID", $reqId);
		$paket_dokumen->setField("NAMA", 'Dokumen sanggah');
		$paket_dokumen->setField("UKURAN", $insertLinkFilesSize);
		$paket_dokumen->setField("TIPE", $insertLinkFilesExe);
		$paket_dokumen->setField("PATH_FILE", $insertLinkFile);
		$paket_dokumen->setField("JENIS_DOKUMEN", "SANGGAH");
		$paket_dokumen->setField("KETERANGAN", $reqKeterangan);
		$paket_dokumen->setField("REKANAN_USER_ID", $this->ID);
		$paket_dokumen->setField("STATUS", (int)$reqBayar);
		$paket_dokumen->setField("PARENT_ID", 0);
		$paket_dokumen->insert();

		echo "Data berhasil disimpan";
	}

	function dokumen_sanggah_tanggapan()
	{
		$this->load->library("kauth");  $userLogin = new kauth();
		$this->load->library("paketinfo"); $paketInfo = new paketinfo();
		$this->load->model("PaketDokumen");
		$this->load->library("FileHandler");

		$paket_dokumen = new PaketDokumen();
		$file = new FileHandler();
		// echo "<pre>"; print_r($this->input->post()); die();
		$reqMode = $this->input->post("reqMode");
		$reqId = $this->input->post("reqId");
		$reqCetak= $this->input->post('reqCetak');
		$reqNamaDokumen= $this->input->post('reqNamaDokumen');
		$reqKeterangan= $this->input->post('reqKeterangan');
		$reqLinkFile= $_FILES['reqLinkFile'];
		$reqBayar= $this->input->post('reqBayar');
		$reqDokumenId = $this->input->post('reqDokumenId');
		$reqPaketDokumenId= $this->input->post('reqPaketDokumenId');
		$reqRekananUserId= $this->input->post('reqRekananUserId');
		$submitSimpan= $this->input->post('submitSimpan');

		$FILE_DIR = "uploads/lelang/";

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

		$paket_dokumen->setField("PAKET_ID", $reqId);
		$paket_dokumen->setField("NAMA", 'Dokumen sanggah');
		$paket_dokumen->setField("UKURAN", $insertLinkFilesSize);
		$paket_dokumen->setField("TIPE", $insertLinkFilesExe);
		$paket_dokumen->setField("PATH_FILE", $insertLinkFile);
		$paket_dokumen->setField("JENIS_DOKUMEN", "SANGGAH");
		$paket_dokumen->setField("KETERANGAN", $reqKeterangan);
		$paket_dokumen->setField("REKANAN_USER_ID", $reqRekananUserId);
		$paket_dokumen->setField("STATUS", (int)$reqBayar);
		$paket_dokumen->setField("PARENT_ID", (int)$reqPaketDokumenId);

		if ($insertLinkFilesSize) {
			$paket_dokumen->insert();
		} else {
			$paket_dokumen->insertNoFile();
		}

		echo "Data berhasil disimpan";
	}

	function delete()
	{
		$this->load->library("paketinfo"); $paketInfo = new paketinfo();
		$this->load->model("PaketDokumen");

		$paket_dokumen = new PaketDokumen();

		$reqId = $this->input->get("reqId");

		$paket_dokumen->setField("PAKET_DOKUMEN_ID", $reqId);
		if($paket_dokumen->delete())
		{

		}

	}

}
?>
