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

class rekanan_evaluasi_keuangan_json extends CI_Controller {

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

	
	function set_evaluasi_kualifikasi_rekening_koran() 
	{
		$this->load->model("RekananEvaluasiKeuangan");
		$rekanan_koran = new RekananEvaluasiKeuangan();
		
		$reqId = $this->input->get("reqId");
		
		$rekanan_koran->setField("FIELD", "LULUS_REKENING_KORAN");
		$rekanan_koran->setField("FIELD_VALUE", "(SELECT CASE WHEN COALESCE(LULUS_REKENING_KORAN, 0) = 0 
															  THEN 1 
															  ELSE 0 
															  END LULUS_REKENING_KORAN 
													FROM REKANAN_EVAL_KEUANGAN X 
													WHERE X.REKANAN_EVAL_KEUANGAN_ID = A.REKANAN_EVAL_KEUANGAN_ID)");
		$rekanan_koran->setField("PAKET_REKANAN_ID", $reqId);
		if($rekanan_koran->updateByField())
			echo "Data berhasil disimpan.";
	}
	
	function set_evaluasi_kualifikasi_skk()
	{

		$this->load->model("PaketRekanan");
		$this->load->model("RekananEvaluasiKeuangan");
		$this->load->model("PaketRekananKualifikasi");
		
		$paket_rekanan = new PaketRekanan();
		
		$reqId = $this->input->post("reqId");
		$submitSimpan = $this->input->post("submitSimpan");
		$reqPaketRekanan = $_POST["reqPaketRekanan"];
		$reqNilaiFinal = $_POST["reqNilaiFinal"];
		$reqCatatan = $_POST["reqCatatan"];
		
		if($submitSimpan == "SimpanSKK")
		{
			for($i=0;$i<count($reqNilaiFinal);$i++)
			{
				$rekanan_evaluasi_skk_insert = new RekananEvaluasiKeuangan();
				$rekanan_evaluasi_skk_insert->setField("FIELD", "LULUS_SKK_NILAI");
				$rekanan_evaluasi_skk_insert->setField("FIELD_VALUE", $reqNilaiFinal[$i]);		
				$rekanan_evaluasi_skk_insert->setField("PAKET_REKANAN_ID", $reqPaketRekanan[$i]);			
				$rekanan_evaluasi_skk_insert->updateByField();					
				unset($rekanan_evaluasi_skk_insert);
		
				$paket_rekanan_kualifikasi = new PaketRekananKualifikasi();
				$paket_rekanan_kualifikasi->setField("PAKET_REKANAN_ID", $reqPaketRekanan[$i]);
				$paket_rekanan_kualifikasi->setField("KODE", "SKK");
				$paket_rekanan_kualifikasi->setField("CATATAN", $reqCatatan[$i]);
				$paket_rekanan_kualifikasi->setField("CREATED_BY", $userLogin->UID);
				$paket_rekanan_kualifikasi->delete();
				$paket_rekanan_kualifikasi->insert();
				unset($paket_rekanan_kualifikasi);
				
			}
			echo "Data berhasil disimpan.";
		}
		
	}

	// ikn 20191001
	function set_evaluasi_kualifikasi_skk2()
	{

		$this->load->model("PaketRekanan");
		$this->load->model("PaketRekananKualifikasi");
		
		$paket_rekanan = new PaketRekanan();
		// echo "<pre>"; print_r($this->input->post()); die();
		$reqId = $this->input->post("reqId");
		$submitSimpan = $this->input->post("submitSimpan");
		$reqPaketRekanan = $_POST["reqPaketRekanan"];
		$reqCatatan = $_POST["reqCatatan"];
		$reqNilaiSKK = $_POST["reqNilaiSKK"];
		$reqNilaiRekeningKoran = $_POST["reqNilaiRekeningKoran"];
		$reqStatus = $_POST["reqStatus"] ? $_POST["reqStatus"] : 0;
		
		if($submitSimpan == "SimpanSKK")
		{
			for($i=0;$i<count($reqPaketRekanan);$i++)
			{ 
				$status = $_POST["reqStatus$reqPaketRekanan[$i]"];
				$paket_rekanan_kualifikasi = new PaketRekananKualifikasi();
				$paket_rekanan_kualifikasi->setField("PAKET_REKANAN_ID", $reqPaketRekanan[$i]);
				$paket_rekanan_kualifikasi->setField("KODE", "SKK");
				$paket_rekanan_kualifikasi->setField("CATATAN", $reqCatatan[$i]);
				$paket_rekanan_kualifikasi->setField("NILAI", $reqNilaiSKK[$i]);
				$paket_rekanan_kualifikasi->setField("STATUS", $status);
				$paket_rekanan_kualifikasi->setField("CREATED_BY", $this->USER_LOGIN_ID);
				$paket_rekanan_kualifikasi->delete();
				$paket_rekanan_kualifikasi->insert2();
				unset($paket_rekanan_kualifikasi);
				
			}

			for($i=0;$i<count($reqPaketRekanan);$i++)
			{ 
				$status2 = $_POST["reqStatus$reqPaketRekanan[$i]"];
				$paket_rekanan_kualifikasi2 = new PaketRekananKualifikasi();
				$paket_rekanan_kualifikasi2->setField("PAKET_REKANAN_ID", $reqPaketRekanan[$i]);
				$paket_rekanan_kualifikasi2->setField("KODE", "REKENING_KORAN");
				$paket_rekanan_kualifikasi2->setField("CATATAN", $reqCatatan[$i]);
				$paket_rekanan_kualifikasi2->setField("NILAI", $reqNilaiRekeningKoran[$i]);
				$paket_rekanan_kualifikasi2->setField("STATUS", $status2);
				$paket_rekanan_kualifikasi2->setField("CREATED_BY", $this->USER_LOGIN_ID);
				$paket_rekanan_kualifikasi2->delete();
				$paket_rekanan_kualifikasi2->insert2();
				unset($paket_rekanan_kualifikasi2);

			}
 
		
				

			echo "Data berhasil disimpan.";
		}
		
	}
	
	
}
?>
