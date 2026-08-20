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

class katalog_lampiran_json extends CI_Controller {

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
	
	function json() 
	{

		$this->load->model("Kataloglampiran");
		$this->load->library("FileHandler");
		
		$Kataloglampiran = new Kataloglampiran();
		$file = new FileHandler();
		
		$reqId = $this->input->post("reqId");
		$reqLinkFile= $_FILES['reqLinkFile'];
		$FILE_DIR = "images/katalog/";
		
		$renameFile = md5(date("dmYHis").$reqLinkFile['name'].$this->ID).".".getExtension($reqLinkFile['name']);
		if($file->uploadToDir('reqLinkFile'.$reqDokumenKe, $FILE_DIR, $renameFile))
		{
			$Kataloglampiran->setField("CREATED_BY", $this->ID);
			$Kataloglampiran->setField("KATALOGID", $reqId);
			$Kataloglampiran->setField("FILE", $reqLinkFile['name']);
			$Kataloglampiran->setField("UKURAN", $file->uploadedSize);
			$Kataloglampiran->setField("TIPE", $file->uploadedExtension);
			$Kataloglampiran->setField("PATH_FILE", $file->uploadedFileName);
			$Kataloglampiran->insert();
			// echo "Gambar/Foto berhasil diupload.";
		}
		else
			echo "Gambar/Foto gagal diupload.";
	}   	 

	function delete_foto()
	{
		
		$this->load->model("Kataloglampiran");
		$Kataloglampiran = new Kataloglampiran();
		$Kataloglampiran2 = new Kataloglampiran();

		$reqId = $this->input->get("reqId");
		$file = $this->input->get("file");

		$Kataloglampiran2->selectByParams(array(), -1, -1, " AND LAMPIRANID = '".$reqId."' AND CREATED_BY = '".$this->ID."' ");
		$Kataloglampiran2->firstRow(); 

		if ($Kataloglampiran2->getField("LAMPIRANID")) {
			$Kataloglampiran->setField("LAMPIRANID", $reqId);	
			$Kataloglampiran->setField("PATH_FILE", $file);	
			$Kataloglampiran->setField("REKANAN_USER_ID", $this->ID);	
			$Kataloglampiran->delete();
			echo "Gambar/Foto berhasil dihapus.";
		} else {
			echo "Gambar/Foto gagal dihapus.";
		}
		
	}
			
}
?>