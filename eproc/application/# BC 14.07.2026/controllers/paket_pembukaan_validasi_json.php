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

class paket_pembukaan_validasi_json extends CI_Controller {

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
	
	function pembukaan() 
	{
		$this->load->library("kauth");  $userLogin = new kauth(); 
		$this->load->model("PaketPembukaanValidasi");
		
		$paket_pembukaan_validasi = new PaketPembukaanValidasi();
		
		$reqId = httpFilterGet("reqId");
		$reqKode = httpFilterGet("reqKode");
		$reqJenis = httpFilterGet("reqJenis");
		/* LOGIN CHECK */
		if ($userLogin->checkUserLogin()) 
		{ 
			$userLogin->retrieveUserInfo();  
			if($userLogin->userLevel == 6)
			{
				echo '<script language="javascript">';
				echo 'alert("Anda tidak berhak mengakses halaman ini. IP address anda telah kami catat sebagai rekanan yang mencoba membuka halaman administrator.");';
				echo 'top.location.href = "index.php";';
				echo '</script>';
				exit;		
			}
		}
		
		$paket_pembukaan_validasi->setField("PAKET_ID", $reqId);
		$paket_pembukaan_validasi->setField("USER_LOGIN_ID", $userLogin->UID);
		$paket_pembukaan_validasi->setField("KODE", $reqKode);
		$paket_pembukaan_validasi->setField("JENIS", $reqJenis);
		if($paket_pembukaan_validasi->insert())
			$pesan = "Validasi berhasil.";	
		else
			$pesan = "Validasi gagal.";	
		
		$arrFinal = array("PESAN" => $pesan);
		
		echo json_encode($arrFinal);
	}

	
	
}
?>
