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

class paket_aanwijzing_validasi_json extends CI_Controller {

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
	
	function aanwijzing_publish_validasi_json() 
	{
		$this->load->library("kauth");  $userLogin = new kauth(); 		
		$this->load->model("PaketAanwijzingValidasi");
		
		$paket_aanwijzing_validasi = new PaketAanwijzingValidasi();
		
		$reqId = $this->input->get("reqId");
		/* LOGIN CHECK */
		/*if ($userLogin->checkUserLogin()) 
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
		
		$paket_aanwijzing_validasi->selectByParamsValidasiPublish($reqId);
		$paket_aanwijzing_validasi->firstRow();
		$total_anggota = $paket_aanwijzing_validasi->getField("TOTAL_ANGGOTA");
		$total_ttd = $paket_aanwijzing_validasi->getField("TOTAL_TTD");
		$diijinkan = round($paket_aanwijzing_validasi->getField("TOTAL_ANGGOTA") / 2);
		
		if($total_ttd >= $diijinkan)
			$pesan = "1";
		else
			$pesan = "Saat ini tercatat ".$paket_aanwijzing_validasi->getField("TOTAL_TTD")."/".$paket_aanwijzing_validasi->getField("TOTAL_ANGGOTA")." yang telah validasi. Publish diijinkan minimal terdapat ".$diijinkan." validasi.";
									
		$arrFinal = array("PESAN" => $pesan);
		*/
		/* VALIDASI PEMBUKAAN PENAWARAN */
		// $jumlahValidasi = $paket_aanwijzing_validasi->getCountByParams(array("A.PAKET_ID" => $reqId), " AND EXISTS (SELECT 1 FROM USER_LOGIN X WHERE X.NIP = A.KODE AND X.USER_TYPE_ID = '7')");

		// ikn 20191126
		$jumlahValidasi = $paket_aanwijzing_validasi->getCountByParams(array("A.PAKET_ID" => $reqId), " AND EXISTS (SELECT 1 FROM USER_LOGIN X WHERE X.USER_LOGIN_ID = A.USER_LOGIN_ID AND X.USER_TYPE_ID = '9')");

		if($jumlahValidasi == 0)
			// $pesan = "Publish gagal, Kepala Pengadaan belum melakukan validasi berita acara aanwijzing.";	
			$pesan = "Publish gagal, Pengusul belum melakukan validasi berita acara aanwijzing.";	
		else
			$pesan = "1";
				
		$arrFinal = array("PESAN" => $pesan);
		echo json_encode($arrFinal);
	}
	
	function aanwijzing_validasi_json() 
	{
		$this->load->library("kauth");  $userLogin = new kauth(); 
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
		
		$arrFinal = array("PESAN" => $pesan);
		
		echo json_encode($arrFinal);
	}

	
	
}
?>
