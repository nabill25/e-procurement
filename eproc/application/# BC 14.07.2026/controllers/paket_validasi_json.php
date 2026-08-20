<?php
/* INCLUDE FILE */
include_once("functions/string.func.php");
include_once("functions/default.func.php");
include_once("functions/date.func.php");

defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * @package 	eProcurement Application
 * @author 		eproc2025
 * @since 		25. Version 3.1
 * 
 */

class paket_validasi_json extends CI_Controller {

	function __construct() {
		parent::__construct(); $this->db->query("SET TIME ZONE 'Asia/Jakarta'"); 
		
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
		$this->load->model("PaketAanwijzingValidasi");
		
		$paket_aanwijzing_validasi = new PaketAanwijzingValidasi();
		
		$reqId = $this->input->get("reqId");
		$reqKode = $this->input->get("reqKode");
		$reqJenis = $this->input->get("reqJenis");
		
		$paket_aanwijzing_validasi->setField("PAKET_ID", $reqId);
		$paket_aanwijzing_validasi->setField("USER_LOGIN_ID", $this->USER_LOGIN_ID);
		$paket_aanwijzing_validasi->setField("KODE", $reqKode);
		$paket_aanwijzing_validasi->setField("JENIS", $reqJenis);
		if($paket_aanwijzing_validasi->insert())
			$pesan = "Validasi berhasil.";	
		else
			$pesan = "Validasi gagal.";	
		
		echo $pesan;
	}

	function pembukaan() 
	{
		$this->load->model("PaketPembukaanValidasi");
		
		$paket_pembukaan_validasi = new PaketPembukaanValidasi();
		
		$reqId = $this->input->get("reqId");
		$reqKode = $this->input->get("reqKode");
		$reqJenis = $this->input->get("reqJenis");
		
		$paket_pembukaan_validasi->setField("PAKET_ID", $reqId);
		$paket_pembukaan_validasi->setField("USER_LOGIN_ID", $this->USER_LOGIN_ID);
		$paket_pembukaan_validasi->setField("KODE", $reqKode);
		$paket_pembukaan_validasi->setField("JENIS", $reqJenis);
		if($paket_pembukaan_validasi->insert()) {
			// Insert Rekam Jejak
		    $this->load->library("librekamjejak"); 
		    $this->librekamjejak->insertRJ('17','',$reqId,'null','17'); // param 1: Posisi/'null', param 2: Keterangan/'', param 3: Paket_id/'null', param 4: permohonan_id/'null'
		    // End Insert Rekam Jejak
    
			$pesan = "Validasi berhasil.";	
		}
		else {
			$pesan = "Validasi gagal.";	
		}
		
		echo $pesan;
	}

	function pembukaankedua() 
	{
		$this->load->model("PaketPembukaanKeduaValidasi");
		
		$paket_pembukaan_validasi = new PaketPembukaanKeduaValidasi();
		
		$reqId = $this->input->get("reqId");
		$reqKode = $this->input->get("reqKode");
		$reqJenis = $this->input->get("reqJenis");
		
		$paket_pembukaan_validasi->setField("PAKET_ID", $reqId);
		$paket_pembukaan_validasi->setField("USER_LOGIN_ID", $this->USER_LOGIN_ID);
		$paket_pembukaan_validasi->setField("KODE", $reqKode);
		$paket_pembukaan_validasi->setField("JENIS", $reqJenis);
		if($paket_pembukaan_validasi->insert())
			$pesan = "Validasi berhasil.";	
		else
			$pesan = "Validasi gagal.";	
		
		echo $pesan;
	}
	
	function evaluasi() 
	{
		$this->load->model("PaketEvaluasiValidasi");
		
		$paket_evaluasi_validasi = new PaketEvaluasiValidasi();
		
		$reqId = $this->input->get("reqId");
		$reqKode = $this->input->get("reqKode");
		$reqJenis = $this->input->get("reqJenis");
		
		$paket_evaluasi_validasi->setField("PAKET_ID", $reqId);
		$paket_evaluasi_validasi->setField("USER_LOGIN_ID", $this->USER_LOGIN_ID);
		$paket_evaluasi_validasi->setField("KODE", $reqKode);
		$paket_evaluasi_validasi->setField("JENIS", $reqJenis);
		
		if($paket_evaluasi_validasi->insert())
			$pesan = "Validasi berhasil.";	
		else
			$pesan = "Validasi gagal.";	
		
		echo $pesan;
	}
	
	function negosiasi() 
	{
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
		
		echo $pesan;
	}
			
}
?>