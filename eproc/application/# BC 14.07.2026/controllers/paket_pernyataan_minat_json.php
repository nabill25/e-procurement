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

class paket_pernyataan_minat_json extends CI_Controller {

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
		$this->USER_LOGIN_ID =  $this->kauth->getInstance()->getIdentity()->USER_LOGIN_ID;
		$this->USER_LOGIN =  $this->kauth->getInstance()->getIdentity()->USER_LOGIN;
		$this->USER_NAMA =  $this->kauth->getInstance()->getIdentity()->USER_NAMA;
		$this->USER_TYPE_ID =  $this->kauth->getInstance()->getIdentity()->USER_TYPE_ID;
		$this->REKANAN_ID =  $this->kauth->getInstance()->getIdentity()->REKANAN_ID;
		$this->UNIT_KERJA_ID =  $this->kauth->getInstance()->getIdentity()->UNIT_KERJA_ID;
		$this->NIP =  $this->kauth->getInstance()->getIdentity()->NIP;
		$this->LOGIN_TIME = $this->kauth->getInstance()->getIdentity()->LOGIN_TIME;
		$this->LOGIN_DATE = $this->kauth->getInstance()->getIdentity()->LOGIN_DATE;
		$this->REKANAN = $this->kauth->getInstance()->getIdentity()->REKANAN;
		$this->REKANAN_KODE = $this->kauth->getInstance()->getIdentity()->REKANAN_KODE;
		$this->REKANAN_PKP = $this->kauth->getInstance()->getIdentity()->REKANAN_PKP;
		$this->REKANAN_NPWP = $this->kauth->getInstance()->getIdentity()->REKANAN_NPWP;
		$this->REKANAN_STATUS_PERUSAHAAN = $this->kauth->getInstance()->getIdentity()->STATUS_PERUSAHAAN;
		$this->REKANAN_STATUS_VALIDASI = $this->kauth->getInstance()->getIdentity()->STATUS_VALIDASI;
	}	
	
	function add() 
	{
		$this->load->model("PaketPernyataanMinat");
		$this->load->library("FileHandler");  
			
		/* create objects */
		$paket_pernyataan_minat = new PaketPernyataanMinat();
		$file = new FileHandler();
		
		/* VARIABLE */
		$submitSimpan	= $this->input->post("submitSimpan");
		$reqId = $this->input->post('reqId');
		$reqNama = $this->input->post("reqNama");
		$reqJabatan = $this->input->post("reqJabatan");
		$reqAlamat = $this->input->post("reqAlamat");
		$reqTelepon = $this->input->post("reqTelepon");
		$reqEmail = $this->input->post("reqEmail");
		$reqPenerimaKuasa  = $this->input->post("reqPenerimaKuasa");
		$reqPenerimaKuasaJabatan = $this->input->post("reqPenerimaKuasaJabatan");
		$reqPenerimaKuasaNoKTP = $this->input->post("reqPenerimaKuasaNoKTP");
		$reqPaketRekananId = $this->input->post("reqPaketRekananId");
		$reqPenerimaKuasaUpload = $_FILES["reqPenerimaKuasaUpload"];
		
		$FILE_DIR = "uploads/kualifikasi/";
		
		if($submitSimpan == "Simpan")
		{
			$paket_pernyataan_minat->setField("PAKET_REKANAN_ID", $reqPaketRekananId);
			$paket_pernyataan_minat->setField("NAMA", $reqNama);
			$paket_pernyataan_minat->setField("JABATAN", $reqJabatan);
			$paket_pernyataan_minat->setField("ALAMAT", $reqAlamat);
			$paket_pernyataan_minat->setField("TELEPON", $reqTelepon);
			$paket_pernyataan_minat->setField("EMAIL", $reqEmail);
			$paket_pernyataan_minat->setField("PENERIMA_KUASA", $reqPenerimaKuasa);
			$paket_pernyataan_minat->setField("PENERIMA_KUASA_JABATAN", $reqPenerimaKuasaJabatan);
			$paket_pernyataan_minat->setField("PENERIMA_KUASA_KTP", $reqPenerimaKuasaNoKTP);
	
			$renameFile = md5(date("dmYHis").$reqPenerimaKuasaUpload['name'].$this->ID).".".getExtension($reqPenerimaKuasaUpload['name']);
			if($file->uploadToDir('reqPenerimaKuasaUpload', $FILE_DIR, $renameFile))
			{
				$insertLinkFilesSize = $file->uploadedSize;
				$insertLinkFilesExe =  $file->uploadedExtension;
				$insertLinkFile =  $renameFile;
				$insertLinkFileNama = $reqPenerimaKuasaUpload['name'];
			}
			else
			{
				$insertLinkFilesSize = $reqLinkFileTempUkuran;
				$insertLinkFilesExe =  $reqLinkFileTempTipe;
				$insertLinkFile =  $reqLinkFileTemp;
				$insertLinkFileNama = $reqLinkFileTempNama;
			}
						
			$paket_pernyataan_minat->setField("PENERIMA_KUASA_FILE", $insertLinkFile);
			
			if($paket_pernyataan_minat->insert())
				echo "1";
			else
				echo "0";
		}

	}
		
}
?>
