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

class rekanan_evaluasi_personil_json extends CI_Controller {

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
	
	function set_evaluasi_kualifikasi_personil() 
	{
		$this->load->library("kauth");  $userLogin = new kauth(); 
		$this->load->model("RekananEvaluasiPersonil");
		$this->load->model("PaketRekananKualifikasi");


		$reqId = $this->input->post("reqId");
		$submitSimpan = $this->input->post("submitSimpan");
		$reqKesesuaian = $_POST["reqKesesuaian"];
		$reqKesesuaianNilai = $_POST["reqKesesuaianNilai"];
		$reqPaketRekanan = $_POST["reqPaketRekanan"];
		$reqNilaiFinal = $_POST["reqNilaiFinal"];
		$reqCatatan = $_POST["reqCatatan"];
		
		$reqRataFinal = $_POST["reqRataFinal"];
		$reqProsentaseFinal = $_POST["reqProsentaseFinal"];
		$reqKesesuaianTotal = $_POST["reqKesesuaianTotal"];
		
		if($submitSimpan == "SimpanPersonil")
		{
			$reqRekananEvaluasiPersonilId = $_POST["reqRekananEvaluasiPersonilId"];
			/* SIMPAN KESESUAIAN */
			for($i=0; $i<count($reqRekananEvaluasiPersonilId);$i++)
			{
				$rekanan_evaluasi_personil_insert = new RekananEvaluasiPersonil();		
				$rekanan_evaluasi_personil_insert->setField("KESESUAIAN", $reqKesesuaian[$i]);
				$rekanan_evaluasi_personil_insert->setField("KESESUAIAN_NILAI", $reqKesesuaianNilai[$i]);				
				$rekanan_evaluasi_personil_insert->setField("REKANAN_EVAL_PERSONIL_ID", $reqRekananEvaluasiPersonilId[$i]);			
				$rekanan_evaluasi_personil_insert->updatePenilaianPersonil();					
				unset($rekanan_evaluasi_personil_insert);			
			}
			
			$reqKebutuhanPemenuhanNilai = $_POST["reqKebutuhanPemenuhanNilai"];
			$reqPaketEvaluasiPersonilId = $_POST["reqPaketEvaluasiPersonilId"];
			$reqPaketRekananPersonilId  = $_POST["reqPaketRekananPersonilId"];
			
			for($i=0; $i<count($reqPaketEvaluasiPersonilId);$i++)
			{
				$rekanan_evaluasi_personil_insert = new RekananEvaluasiPersonil();		
				$rekanan_evaluasi_personil_insert->setField("KESESUAIAN_TOTAL", $reqKebutuhanPemenuhanNilai[$i]);
				$rekanan_evaluasi_personil_insert->setField("PAKET_EVAL_PERSONIL_ID", $reqPaketEvaluasiPersonilId[$i]);				
				$rekanan_evaluasi_personil_insert->setField("PAKET_REKANAN_ID", $reqPaketRekananPersonilId[$i]);			
				$rekanan_evaluasi_personil_insert->updatePenilaianPersonilTotal();					
				unset($rekanan_evaluasi_personil_insert);			
			}
			
			for($i=0; $i<count($reqPaketRekanan);$i++)
			{
				$rekanan_evaluasi_personil_insert = new RekananEvaluasiPersonil();		
				$rekanan_evaluasi_personil_insert->setField("FIELD", "NILAI");
				$rekanan_evaluasi_personil_insert->setField("FIELD_VALUE", $reqNilaiFinal[$i]);
				$rekanan_evaluasi_personil_insert->setField("PAKET_REKANAN_ID", $reqPaketRekanan[$i]);
				$rekanan_evaluasi_personil_insert->updateByField();
				unset($rekanan_evaluasi_personil_insert);
		
				$paket_rekanan_kualifikasi = new PaketRekananKualifikasi();
				$paket_rekanan_kualifikasi->setField("PAKET_REKANAN_ID", $reqPaketRekanan[$i]);
				$paket_rekanan_kualifikasi->setField("KODE", "PERSONIL");
				$paket_rekanan_kualifikasi->setField("CATATAN", $reqCatatan[$i]);
				$paket_rekanan_kualifikasi->setField("CREATED_BY", $userLogin->UID);
				$paket_rekanan_kualifikasi->delete();
				$paket_rekanan_kualifikasi->insert();
				unset($paket_rekanan_kualifikasi);			
			}
			echo "Data berhasil disimpan.";	
		}
		
		
	}
	
		
	
}
?>
