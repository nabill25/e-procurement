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

class katalog_foto_json extends CI_Controller {

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

		$this->load->model("Katalogfoto");
		$this->load->library("FileHandler");
		
		$katalogfoto = new Katalogfoto();
		$file = new FileHandler();
		
		$reqId = $this->input->post("reqId");
		$reqLinkFile= $_FILES['reqLinkFile'];
		$FILE_DIR = "images/katalog/";
		
		$renameFile = md5(date("dmYHis").$reqLinkFile['name'].$this->ID).".".getExtension($reqLinkFile['name']);
		if($file->uploadToDir('reqLinkFile'.$reqDokumenKe, $FILE_DIR, $renameFile))
		{
			$katalogfoto->setField("CREATED_BY", $this->ID);
			$katalogfoto->setField("KATALOGID", $reqId);
			$katalogfoto->setField("FILE", $reqLinkFile['name']);
			$katalogfoto->setField("UKURAN", $file->uploadedSize);
			$katalogfoto->setField("TIPE", $file->uploadedExtension);
			$katalogfoto->setField("PATH_FILE", $file->uploadedFileName);
			$katalogfoto->insert();
			// echo "Gambar/Foto berhasil diupload.";
		}
		else
			echo "Gambar/Foto gagal diupload.";
	}   	 

	function delete_foto()
	{
		
		$this->load->model("Katalogfoto");
		$katalogfoto = new Katalogfoto();
		$katalogfoto2 = new Katalogfoto();

		$reqId = $this->input->get("reqId");
		$file = $this->input->get("file");

		$katalogfoto2->selectByParams(array(), -1, -1, " AND FOTOID = '".$reqId."' AND CREATED_BY = '".$this->ID."' ");
		$katalogfoto2->firstRow(); 

		if ($katalogfoto2->getField("FOTOID")) {
			$katalogfoto->setField("FOTOID", $reqId);	
			$katalogfoto->setField("PATH_FILE", $file);	
			$katalogfoto->setField("REKANAN_USER_ID", $this->ID);	
			$katalogfoto->delete();
			echo "Gambar/Foto berhasil dihapus.";
		} else {
			echo "Gambar/Foto gagal dihapus.";
		}
		
	}
			
}
?>