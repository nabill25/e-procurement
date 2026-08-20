<?php
/* INCLUDE FILE */
include_once("functions/string.func.php");
include_once("functions/default.func.php");
include_once("functions/date.func.php");
include_once("functions/pdf.func.php");
include_once("functions/encrypt2.func.php");

defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * @package 	eProcurement Application
 * @author 		eproc2025
 * @since 		25. Version 3.1
 * 
 */

class master_dokumen_template_upload extends CI_Controller {

	function __construct() {
		parent::__construct(); $this->db->query("SET TIME ZONE 'Asia/Jakarta'"); 
		
		if (!$this->kauth->getInstance()->hasIdentity())
		{
			//redirect('login');
		}		
		
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
	 
	function upload_validasi() 
	{
		// echo "string"; die();
		ini_set('memory_limit', '-1'); 		
		
		$this->load->model("Masterdokumentemplate");
		$this->load->library("FileHandler");
		$this->load->library("paketinfo"); $paketInfo = new paketinfo(); 
		
		$master_dokumen_template = new Masterdokumentemplate();
		$file = new FileHandler();
		
		$reqNamaDokumen= $this->input->post('reqNamaDokumen');
		$reqLinkFile= $_FILES['Filedata'];
		
		$FILE_DIR = "uploads/template/"; 

		$renameFile = md5(date("dmYHis").$reqLinkFile['name'].$reqPengirim).".".getExtension($reqLinkFile['name']);
		if($file->uploadToDir('Filedata', $FILE_DIR, "template_".$renameFile))
		{ 
						
			$master_dokumen_template->setField("NAMA", $reqNamaDokumen);
			$master_dokumen_template->setField("UKURAN", $file->uploadedSize);
			$master_dokumen_template->setField("TIPE", $file->uploadedExtension);
			$master_dokumen_template->setField("PATH_FILE", "template_".$renameFile);
			$master_dokumen_template->setField('CREATED_BY', $this->USER_LOGIN_ID);
			$master_dokumen_template->insert();
			echo "berhasil diupload."; 
		}
	}	 

	function delete_dokumen()
	{
		
		$this->load->model("Masterdokumentemplate");
		$master_dokumen = new Masterdokumentemplate();

		$reqId = $this->input->get("reqId");
		
		$master_dokumen->setField("ID", $reqId);	
		$master_dokumen->setField("CREATED_BY", $this->USER_LOGIN_ID);	
		$master_dokumen->deteleDokumen();
	} 
			
}
?>