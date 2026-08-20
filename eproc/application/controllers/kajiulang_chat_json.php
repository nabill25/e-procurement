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

class kajiulang_chat_json extends CI_Controller {

	function __construct() {
		parent::__construct(); $this->db->query("SET TIME ZONE 'Asia/Jakarta'");

		if (!$this->kauth->getInstance()->hasIdentity())
		{
			redirect('Login');
		}

		$this->USER_LOGIN_ID = $this->kauth->getInstance()->getIdentity()->USER_LOGIN_ID;
		$this->ID = $this->kauth->getInstance()->getIdentity()->REKANAN_ID;
		$this->REKANAN_KODE = $this->kauth->getInstance()->getIdentity()->REKANAN_KODE;
	}

	function dokumen_aanwijzing_tanggapan()
	{
		$this->load->library("kauth");  $userLogin = new kauth();
		$this->load->library("paketinfo"); $paketInfo = new paketinfo();
		$this->load->model("Paketkajiulang");
		$this->load->library("FileHandler");

		$paket_kajiulang = new Paketkajiulang();
		$file = new FileHandler();

		$reqPermohonanId = $this->input->post("reqPermohonanId");
		$reqKeterangan= $this->input->post('reqKeterangan');
		$reqLinkFile= $_FILES['reqLinkFile'];

		$FILE_DIR = "uploads/kajiulang/";

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

		$paket_kajiulang->setField("PERMOHONAN_PAKET_ID", $reqPermohonanId);
		$paket_kajiulang->setField("NAMA", 'Dokumen kajiulang');
		$paket_kajiulang->setField("UKURAN", $insertLinkFilesSize);
		$paket_kajiulang->setField("TIPE", $insertLinkFilesExe);
		$paket_kajiulang->setField("PATH_FILE", $insertLinkFile); 
		$paket_kajiulang->setField("KETERANGAN", $reqKeterangan);
		$paket_kajiulang->setField("PARENT_ID", 1);
		$paket_kajiulang->setField("USER_LOGIN_ID", $this->USER_LOGIN_ID);
		$paket_kajiulang->setField('CREATED_BY', $this->USER_LOGIN_ID);

		if ($insertLinkFilesSize) {
			$paket_kajiulang->insert();
		} else {
			$paket_kajiulang->insertNoFile();
			// $paket_kajiulang->insert();
		}

		echo "Data berhasil disimpan";
	}

	function delete()
	{
		$this->load->library("paketinfo"); $paketInfo = new paketinfo();
		$this->load->model("Paketkajiulang");

		$paket_kajiulang = new Paketkajiulang();

		$reqId = $this->input->get("reqId");

		$paket_kajiulang->setField("PAKET_KAJI_ULANG_ID", $reqId);
		if($paket_kajiulang->delete())
		{

		}

	}

	function approve()
	{
		$reqId =  $this->input->post('reqId'); // permohonan_id
		$this->load->model("Permohonanpaket");

		$permohonanpaket = new Permohonanpaket();
		$permohonanpaket->setField("PERMOHONAN_PAKET_ID", $reqId);
		$permohonanpaket->setField("KAJI_ULANG", "1");
		$permohonanpaket->setField("CREATED_BY", $this->USER_LOGIN_ID);

		if($permohonanpaket->approveKajiulang()) {
			 // Insert Rekam Jejak
		    $this->load->library("librekamjejak");
		    $this->librekamjejak->insertRJ('512','','null',$reqId,'512'); 
		    // param 1: Posisi/'null', param 2: Keterangan/'', param 3: Paket_id/'null', param 4: permohonan_id/'null'
		    // End Insert Rekam Jejak
			echo "Data Berhasil Disimpan";
		}else {
			echo "Data Gagal Disimpan";
		}

	}

}
?>
