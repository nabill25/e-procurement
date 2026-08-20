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

class paket_rekanan_kualifikasi_json extends CI_Controller {

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
		$this->load->model("PaketRekananKualifikasi");
		
		$reqPaketRekananId = $this->input->post("reqPaketRekananId");
		$reqCatatan = $this->input->post("reqCatatan");
		$reqKode = $this->input->post("reqKode");
		$reqNilai = $this->input->post("reqNilai");
		$reqStatus = $this->input->post("reqStatus");

		// Update status di paket_rekanan
		$this->load->model("PaketRekanan");
		$paket_rekanan = new PaketRekanan();
		$paket_rekanan->setField("FIELD", "LULUS_ADMINISTRASI");
		$paket_rekanan->setField("FIELD_VALUE", "(SELECT CASE WHEN COALESCE(LULUS_ADMINISTRASI, 0) = 0 THEN 1 ELSE 0 END LULUS_ADMINISTRASI FROM PAKET_REKANAN X WHERE X.PAKET_REKANAN_ID = A.PAKET_REKANAN_ID)");
		$paket_rekanan->setField("PAKET_REKANAN_ID", $reqPaketRekananId);
		$paket_rekanan->update();

		$get_paket_rekanan = new PaketRekanan();
		$get_paket_rekanan->selectByParams(array("A.PAKET_REKANAN_ID" => $reqPaketRekananId));
		$get_paket_rekanan->firstRow();
	
		$paket_rekanan_kualifikasi = new PaketRekananKualifikasi();
		$paket_rekanan_kualifikasi->setField("PAKET_REKANAN_ID", $reqPaketRekananId);
		$paket_rekanan_kualifikasi->setField("KODE", $reqKode);
		$paket_rekanan_kualifikasi->setField("CATATAN", $reqCatatan);
		$paket_rekanan_kualifikasi->setField("NILAI", $reqNilai);
		$paket_rekanan_kualifikasi->setField("STATUS", $get_paket_rekanan->getField("LULUS_ADMINISTRASI"));
		$paket_rekanan_kualifikasi->setField("CREATED_BY", $this->USER_LOGIN_ID);
		$paket_rekanan_kualifikasi->delete();
		$paket_rekanan_kualifikasi->insert2();
		
		echo "Catatan berhasil disimpan.";
	}
}
?>
