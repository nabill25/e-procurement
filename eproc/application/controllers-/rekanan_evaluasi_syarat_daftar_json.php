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

class rekanan_evaluasi_syarat_daftar_json extends CI_Controller {

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
	
	function add() 
	{
		/* INCLUDE FILE */
		include_once("functions/default.func.php");
		include_once("functions/date.func.php");
		include_once("functions/string.func.php");
		$this->load->model("RekananEvaluasiSyaratDaftar");
		$this->load->library("FileHandler"); 
		
		/* create objects */
		$rekanan_evaluasi_syarat_daftar = new RekananEvaluasiSyaratDaftar();
		$file = new FileHandler();
		
		/* VARIABLE */
		$submitSimpan	= $this->input->post("submitSimpan");
		$reqId = $this->input->post('reqId');
		$reqLinkFile= $_FILES['reqLinkFile'];
		$reqLinkFileTemp = $this->input->post('reqLinkFileTemp');
		
		$FILE_DIR = "uploads/syarat_pendaftaran/";
		
		if($submitSimpan == "Simpan")
		{

			$rekanan_evaluasi_syarat_daftar_delete = new RekananEvaluasiSyaratDaftar();
			$rekanan_evaluasi_syarat_daftar_delete->setField("PAKET_EVAL_SYARAT_DAFTAR_ID", $reqId);
			$rekanan_evaluasi_syarat_daftar_delete->setField("REKANAN_ID", $this->ID);
			$rekanan_evaluasi_syarat_daftar_delete->delete();
			
			$rekanan_evaluasi_syarat_daftar->setField("PAKET_EVAL_SYARAT_DAFTAR_ID", $reqId);
			$rekanan_evaluasi_syarat_daftar->setField("REKANAN_ID", $this->ID);
			
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
			
			$rekanan_evaluasi_syarat_daftar->setField("PATH_FILE", $insertLinkFile);
			if($rekanan_evaluasi_syarat_daftar->insert())
			{		
				echo "Data berhasil disimpan.";
			}
		}
	}
	
	
}
?>
