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

class paket_negosiasi_validasi_json extends CI_Controller {

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
	}	
	
	function negosiasi() 
	{
		
		$this->load->library("kauth");  $userLogin = new kauth(); 
		$this->load->model("PaketNegosiasiValidasi");
		
		$paket_negosiasi_validasi = new PaketNegosiasiValidasi();
		
		$reqId = $this->input->get("reqId");
		$reqKode = $this->input->get("reqKode");
		$reqJenis = $this->input->get("reqJenis");
		
		$paket_negosiasi_validasi->setField("PAKET_ID", $reqId);
		$paket_negosiasi_validasi->setField("USER_LOGIN_ID", $this->USER_LOGIN_ID);
		$paket_negosiasi_validasi->setField("KODE", $reqKode);
		$paket_negosiasi_validasi->setField("JENIS", $reqJenis);
		if($paket_negosiasi_validasi->insert())
			$pesan = "Validasi berhasil.";	
		else
			$pesan = "Validasi gagal.";	
		
		$arrFinal = array("PESAN" => $pesan);
		
		echo json_encode($arrFinal);
	}

	
	
}
?>
