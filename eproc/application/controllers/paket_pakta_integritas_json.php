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

class paket_pakta_integritas_json extends CI_Controller {

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
	
	function pakta_integritas_validasi_json() 
	{
		$this->load->library("kauth");  $userLogin = new kauth(); 
		$this->load->model("PaketPaktaIntegritas");
		$paket_pakta_integritas = new PaketPaktaIntegritas();
		
		$reqId = $this->input->get("reqId"); //var_dump($reqId);die();
		$reqKode = $this->input->get("reqKode"); //var_dump($reqKode);die();
		$reqJenis = $this->input->get("reqJenis"); //var_dump($reqJenis);die();
		
		$paket_pakta_integritas->setField("PAKET_ID", $reqId);
		$paket_pakta_integritas->setField("USER_LOGIN_ID", $this->USER_LOGIN_ID); //var_dump($this->ID);die();
		$paket_pakta_integritas->setField("KODE", $reqKode);
		$paket_pakta_integritas->setField("JENIS", $reqJenis);
		$paket_pakta_integritas->setField('CREATED_BY', $this->USER_LOGIN_ID);
		if($paket_pakta_integritas->insert())
			$pesan = "Validasi berhasil.";	
		else
			$pesan = "Validasi gagal.";	
		
		$arrFinal = array("PESAN" => $pesan);
		
		echo json_encode($arrFinal);
	}
	
	function add() 
	{
		$this->load->model("PaketPaktaIntegritas");
		$paket_pakta_integritas = new PaketPaktaIntegritas();
		
		$submitSimpan	= $this->input->post("submitSimpan");
		$reqId = $this->input->post('reqId');
		
		if($submitSimpan == "Simpan")
		{		
			
			$paket_pakta_integritas_delete = new PaketPaktaIntegritas();	
			$paket_pakta_integritas_delete->setField("PAKET_ID", $reqId);
			$paket_pakta_integritas_delete->setField("USER_LOGIN_ID", $this->USER_LOGIN_ID);
			$paket_pakta_integritas_delete->deletePaktaRekanan();
			
			$paket_pakta_integritas->setField("PAKET_ID", $reqId);
			$paket_pakta_integritas->setField("USER_LOGIN_ID", $this->USER_LOGIN_ID);
			$paket_pakta_integritas->setField("KODE", $this->REKANAN_KODE);
			$paket_pakta_integritas->setField("JENIS", "REKANAN");
			if($paket_pakta_integritas->insert())
				echo "1";
			else
				echo "0";
		}
		
	}
		
}
?>
