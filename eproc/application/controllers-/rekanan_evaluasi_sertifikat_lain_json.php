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

class rekanan_evaluasi_sertifikat_lain_json extends CI_Controller {

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
	
	function set_evaluasi_kualifikasi_sertifikat() 
	{
		$this->load->library("kauth");  $userLogin = new kauth(); 
		$this->load->model("RekananEvaluasiSertifikatLain");
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
		
		if($submitSimpan == "SimpanSertifikat")
		{
			$reqRekananEvaluasiSertifikatId = $_POST["reqRekananEvaluasiSertifikatId"];
			/* SIMPAN KESESUAIAN */
			for($i=0; $i<count($reqRekananEvaluasiSertifikatId);$i++)
			{
				$rekanan_evaluasi_sertifikat_insert = new RekananEvaluasiSertifikatLain();		
				$rekanan_evaluasi_sertifikat_insert->setField("KESESUAIAN", $reqKesesuaian[$i]);
				$rekanan_evaluasi_sertifikat_insert->setField("KESESUAIAN_NILAI", $reqKesesuaianNilai[$i]);				
				$rekanan_evaluasi_sertifikat_insert->setField("REKANAN_EVAL_SERTIFIKAT_ID", $reqRekananEvaluasiSertifikatId[$i]);			
				$rekanan_evaluasi_sertifikat_insert->updatePenilaian();					
				unset($rekanan_evaluasi_sertifikat_insert);			
			}
			
			$reqKebutuhanPemenuhanNilai = $_POST["reqKebutuhanPemenuhanNilai"];
			$reqPaketEvaluasiSertifikatId = $_POST["reqPaketEvaluasiSertifikatId"];
			$reqPaketRekananSertifikatId  = $_POST["reqPaketRekananSertifikatId"];
			
			for($i=0; $i<count($reqPaketEvaluasiSertifikatId);$i++)
			{
				$rekanan_evaluasi_sertifikat_insert = new RekananEvaluasiSertifikatLain();		
				$rekanan_evaluasi_sertifikat_insert->setField("KESESUAIAN_TOTAL", $reqKebutuhanPemenuhanNilai[$i]);
				$rekanan_evaluasi_sertifikat_insert->setField("PAKET_EVAL_SERTIFIKAT_LAIN_ID", $reqPaketEvaluasiSertifikatId[$i]);				
				$rekanan_evaluasi_sertifikat_insert->setField("PAKET_REKANAN_ID", $reqPaketRekananSertifikatId[$i]);			
				$rekanan_evaluasi_sertifikat_insert->updatePenilaianTotal();					
				unset($rekanan_evaluasi_sertifikat_insert);			
			}
			
			for($i=0; $i<count($reqPaketRekanan);$i++)
			{
				$rekanan_evaluasi_sertifikat_insert = new RekananEvaluasiSertifikatLain();		
				$rekanan_evaluasi_sertifikat_insert->setField("FIELD", "NILAI");
				$rekanan_evaluasi_sertifikat_insert->setField("FIELD_VALUE", $reqNilaiFinal[$i]);
				$rekanan_evaluasi_sertifikat_insert->setField("PAKET_REKANAN_ID", $reqPaketRekanan[$i]);
				$rekanan_evaluasi_sertifikat_insert->updateByField();
				unset($rekanan_evaluasi_sertifikat_insert);
		
				$paket_rekanan_kualifikasi = new PaketRekananKualifikasi();
				$paket_rekanan_kualifikasi->setField("PAKET_REKANAN_ID", $reqPaketRekanan[$i]);
				$paket_rekanan_kualifikasi->setField("KODE", "SERTIFIKAT");
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
