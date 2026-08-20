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

class rekanan_evaluasi_peralatan_json extends CI_Controller {

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
	
	function set_evaluasi_kualifikasi_peralatan() 
	{
		$this->load->library("kauth");  $userLogin = new kauth(); 
		$this->load->model("RekananEvaluasiPeralatan");
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
		
		if($submitSimpan == "SimpanPeralatan")
		{
		
			$reqRekananEvaluasiPeralatanId = $_POST["reqRekananEvaluasiPeralatanId"];
			/* SIMPAN KESESUAIAN */
			for($i=0; $i<count($reqRekananEvaluasiPeralatanId);$i++)
			{
				$rekanan_evaluasi_peralatan_insert = new RekananEvaluasiPeralatan();		
				$rekanan_evaluasi_peralatan_insert->setField("KESESUAIAN", $reqKesesuaian[$i]);
				$rekanan_evaluasi_peralatan_insert->setField("KESESUAIAN_NILAI", $reqKesesuaianNilai[$i]);				
				$rekanan_evaluasi_peralatan_insert->setField("REKANAN_EVAL_PERALATAN_ID", $reqRekananEvaluasiPeralatanId[$i]);			
				$rekanan_evaluasi_peralatan_insert->updatePenilaianPeralatan();					
				unset($rekanan_evaluasi_peralatan_insert);			
			}
			
			$reqKebutuhanPemenuhanNilai = $_POST["reqKebutuhanPemenuhanNilai"];
			$reqPaketEvaluasiPeralatanId = $_POST["reqPaketEvaluasiPeralatanId"];
			$reqPaketRekananPeralatanId  = $_POST["reqPaketRekananPeralatanId"];
			
			for($i=0; $i<count($reqPaketEvaluasiPeralatanId);$i++)
			{
				$rekanan_evaluasi_peralatan_insert = new RekananEvaluasiPeralatan();		
				$rekanan_evaluasi_peralatan_insert->setField("KESESUAIAN_TOTAL", $reqKebutuhanPemenuhanNilai[$i]);
				$rekanan_evaluasi_peralatan_insert->setField("PAKET_EVAL_PERALATAN_DETIL_ID", $reqPaketEvaluasiPeralatanId[$i]);				
				$rekanan_evaluasi_peralatan_insert->setField("PAKET_REKANAN_ID", $reqPaketRekananPeralatanId[$i]);			
				$rekanan_evaluasi_peralatan_insert->updatePenilaianPeralatanTotal();					
				unset($rekanan_evaluasi_peralatan_insert);			
			}
			
			for($i=0; $i<count($reqPaketRekanan);$i++)
			{
				$rekanan_evaluasi_peralatan_insert = new RekananEvaluasiPeralatan();		
				$rekanan_evaluasi_peralatan_insert->setField("FIELD", "NILAI");
				$rekanan_evaluasi_peralatan_insert->setField("FIELD_VALUE", $reqNilaiFinal[$i]);
				$rekanan_evaluasi_peralatan_insert->setField("PAKET_REKANAN_ID", $reqPaketRekanan[$i]);
				$rekanan_evaluasi_peralatan_insert->updateByField();
				unset($rekanan_evaluasi_peralatan_insert);
		
				$paket_rekanan_kualifikasi = new PaketRekananKualifikasi();
				$paket_rekanan_kualifikasi->setField("PAKET_REKANAN_ID", $reqPaketRekanan[$i]);
				$paket_rekanan_kualifikasi->setField("KODE", "PERALATAN");
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
