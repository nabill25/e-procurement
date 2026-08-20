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

class rekanan_evaluasi_pengalaman_json extends CI_Controller {

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
	
	function set_evaluasi_kualifikasi_kemampuan_dasar() 
	{
		$this->load->library("kauth");  $userLogin = new kauth(); 
		$this->load->model("RekananEvaluasiPengalaman");
		$pengalaman = new RekananEvaluasiPengalaman();
		
		$reqId = httpFilterGet("reqId");
		
		/* LOGIN CHECK */
		if ($userLogin->checkUserLogin()) 
		{ 
			$userLogin->retrieveUserInfo();
		}
		
			$pengalaman->setField("FIELD", "MEMENUHI_SYARAT");
			$pengalaman->setField("FIELD_VALUE", "(SELECT DECODE(COALESCE(MEMENUHI_SYARAT, 0), 0, 1, 0) FROM REKANAN_EVAL_PENGALAMAN X WHERE X.REKANAN_EVAL_PENGALAMAN_ID = A.REKANAN_EVAL_PENGALAMAN_ID)");
			$pengalaman->setField("PAKET_REKANAN_ID", $reqId);
			$pengalaman->updateByField();
		$met = array();
		$i=0;
		
		$met[0]['STATUS'] = 1;
		echo json_encode($met);
	}

	function set_evaluasi_kualifikasi_pengalaman() 
	{
		$this->load->library("kauth");  $userLogin = new kauth(); 
		$this->load->model("RekananEvaluasiPengalaman");
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
		$reqRekananEvalPengalamanId = $_POST["reqRekananEvalPengalamanId"];
		
		if($submitSimpan == "SimpanPengalaman")
		{
		
			for($i=0;$i<count($reqRekananEvalPengalamanId);$i++)
			{
				$rekanan_evaluasi_kd_insert = new RekananEvaluasiPengalaman();
				$rekanan_evaluasi_kd_insert->setField("BP_KESESUAIAN", $reqKesesuaian[$i]);
				$rekanan_evaluasi_kd_insert->setField("BP_KESESUAIAN_NILAI", $reqKesesuaianNilai[$i]);		
				$rekanan_evaluasi_kd_insert->setField("BP_KESESUAIAN_TOTAL", $reqKesesuaianTotal[$i]);				
				$rekanan_evaluasi_kd_insert->setField("REKANAN_EVAL_PENGALAMAN_ID", $reqRekananEvalPengalamanId[$i]);			
				$rekanan_evaluasi_kd_insert->updatePenilaianPengalaman();					
				unset($rekanan_evaluasi_kd_insert);			
			}
				
			for($i=0; $i<count($reqPaketRekanan);$i++)
			{
				$rekanan_evaluasi_pengalaman = new RekananEvaluasiPengalaman();
				$rekanan_evaluasi_pengalaman->setField("FIELD", "NILAI");
				$rekanan_evaluasi_pengalaman->setField("FIELD_VALUE", $reqNilaiFinal[$i]);
				$rekanan_evaluasi_pengalaman->setField("PAKET_REKANAN_ID", $reqPaketRekanan[$i]);
				$rekanan_evaluasi_pengalaman->updateByField();
				unset($rekanan_evaluasi_pengalaman);
				
				$rekanan_evaluasi_kd_insert = new RekananEvaluasiPengalaman();
				$rekanan_evaluasi_kd_insert->setField("FIELD", "NILAI_RATA");
				$rekanan_evaluasi_kd_insert->setField("FIELD_VALUE", $reqRataFinal[$i]);		
				$rekanan_evaluasi_kd_insert->setField("PAKET_REKANAN_ID", $reqPaketRekanan[$i]);			
				$rekanan_evaluasi_kd_insert->updateByField();					
				unset($rekanan_evaluasi_kd_insert);
				
				
				$rekanan_evaluasi_kd_insert = new RekananEvaluasiPengalaman();
				$rekanan_evaluasi_kd_insert->setField("FIELD", "NILAI_PROSENTASE");
				$rekanan_evaluasi_kd_insert->setField("FIELD_VALUE", $reqProsentaseFinal[$i]);		
				$rekanan_evaluasi_kd_insert->setField("PAKET_REKANAN_ID", $reqPaketRekanan[$i]);			
				$rekanan_evaluasi_kd_insert->updateByField();		
				unset($rekanan_evaluasi_kd_insert);
			}
		
			for($i=0; $i<count($reqPaketRekanan);$i++)
			{
				$rekanan_evaluasi_pengalaman = new RekananEvaluasiPengalaman();
				$rekanan_evaluasi_pengalaman->setField("FIELD", "NILAI");
				$rekanan_evaluasi_pengalaman->setField("FIELD_VALUE", $reqNilaiFinal[$i]);
				$rekanan_evaluasi_pengalaman->setField("PAKET_REKANAN_ID", $reqPaketRekanan[$i]);
				$rekanan_evaluasi_pengalaman->updateByField();
				unset($rekanan_evaluasi_pengalaman);
				
				$rekanan_evaluasi_kd_insert = new RekananEvaluasiPengalaman();
				$rekanan_evaluasi_kd_insert->setField("FIELD", "NILAI_RATA");
				$rekanan_evaluasi_kd_insert->setField("FIELD_VALUE", $reqRataFinal[$i]);		
				$rekanan_evaluasi_kd_insert->setField("PAKET_REKANAN_ID", $reqPaketRekanan[$i]);			
				$rekanan_evaluasi_kd_insert->updateByField();					
				unset($rekanan_evaluasi_kd_insert);
				
				
				$rekanan_evaluasi_kd_insert = new RekananEvaluasiPengalaman();
				$rekanan_evaluasi_kd_insert->setField("FIELD", "NILAI_PROSENTASE");
				$rekanan_evaluasi_kd_insert->setField("FIELD_VALUE", $reqProsentaseFinal[$i]);		
				$rekanan_evaluasi_kd_insert->setField("PAKET_REKANAN_ID", $reqPaketRekanan[$i]);			
				$rekanan_evaluasi_kd_insert->updateByField();		
				unset($rekanan_evaluasi_kd_insert);
		
				$paket_rekanan_kualifikasi = new PaketRekananKualifikasi();
				$paket_rekanan_kualifikasi->setField("PAKET_REKANAN_ID", $reqPaketRekanan[$i]);
				$paket_rekanan_kualifikasi->setField("KODE", "PENGALAMAN");
				$paket_rekanan_kualifikasi->setField("CATATAN", $reqCatatan[$i]);
				$paket_rekanan_kualifikasi->setField("CREATED_BY", $this->USER_LOGIN_ID);
				$paket_rekanan_kualifikasi->delete();
				$paket_rekanan_kualifikasi->insert();
				unset($paket_rekanan_kualifikasi);	
						
			}
			echo "Data berhasil disimpan.";	
		}
	}

	// ikn 20191001
	function set_evaluasi_kualifikasi_pengalaman2()
	{

		$this->load->model("PaketRekanan");
		$this->load->model("PaketRekananKualifikasi");
		
		$paket_rekanan = new PaketRekanan();
		// echo "<pre>"; print_r($this->input->post()); die();
		$reqId = $this->input->post("reqId");
		$submitSimpan = $this->input->post("submitSimpan");
		$reqPaketRekanan = $_POST["reqPaketRekanan"];
		$reqCatatan = $_POST["reqCatatan"];
		$reqNilaiPengalaman = $_POST["reqNilaiPengalaman"];
		$reqNilaiPersonil = $_POST["reqNilaiPersonil"];
		$reqNilaiPeralatan = $_POST["reqNilaiPeralatan"];
		$reqNilaiSertifikat = $_POST["reqNilaiSertifikat"];
		$reqStatus = $_POST["reqStatus"] ? $_POST["reqStatus"] : 0;
		
		if($submitSimpan == "SimpanPengalaman")
		{
			for($i=0;$i<count($reqPaketRekanan);$i++)
			{ 
				$status = $_POST["reqStatus$reqPaketRekanan[$i]"];
				$paket_rekanan_kualifikasi = new PaketRekananKualifikasi();
				$paket_rekanan_kualifikasi->setField("PAKET_REKANAN_ID", $reqPaketRekanan[$i]);
				$paket_rekanan_kualifikasi->setField("KODE", "PENGALAMAN");
				$paket_rekanan_kualifikasi->setField("CATATAN", $reqCatatan[$i]);
				$paket_rekanan_kualifikasi->setField("NILAI", $reqNilaiPengalaman[$i]);
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
				$paket_rekanan_kualifikasi2->setField("KODE", "PERSONIL");
				$paket_rekanan_kualifikasi2->setField("CATATAN", $reqCatatan[$i]);
				$paket_rekanan_kualifikasi2->setField("NILAI", $reqNilaiPersonil[$i]);
				$paket_rekanan_kualifikasi2->setField("STATUS", $status2);
				$paket_rekanan_kualifikasi2->setField("CREATED_BY", $this->USER_LOGIN_ID);
				$paket_rekanan_kualifikasi2->delete();
				$paket_rekanan_kualifikasi2->insert2();
				unset($paket_rekanan_kualifikasi2);

			}

			for($i=0;$i<count($reqPaketRekanan);$i++)
			{ 
				$status3 = $_POST["reqStatus$reqPaketRekanan[$i]"];
				$paket_rekanan_kualifikasi3 = new PaketRekananKualifikasi();
				$paket_rekanan_kualifikasi3->setField("PAKET_REKANAN_ID", $reqPaketRekanan[$i]);
				$paket_rekanan_kualifikasi3->setField("KODE", "PERALATAN");
				$paket_rekanan_kualifikasi3->setField("CATATAN", $reqCatatan[$i]);
				$paket_rekanan_kualifikasi3->setField("NILAI", $reqNilaiPeralatan[$i]);
				$paket_rekanan_kualifikasi3->setField("STATUS", $status3);
				$paket_rekanan_kualifikasi3->setField("CREATED_BY", $this->USER_LOGIN_ID);
				$paket_rekanan_kualifikasi3->delete();
				$paket_rekanan_kualifikasi3->insert2();
				unset($paket_rekanan_kualifikasi3);

			}

			for($i=0;$i<count($reqPaketRekanan);$i++)
			{ 
				$status4 = $_POST["reqStatus$reqPaketRekanan[$i]"];
				$paket_rekanan_kualifikasi4 = new PaketRekananKualifikasi();
				$paket_rekanan_kualifikasi4->setField("PAKET_REKANAN_ID", $reqPaketRekanan[$i]);
				$paket_rekanan_kualifikasi4->setField("KODE", "SERTIFIKAT");
				$paket_rekanan_kualifikasi4->setField("CATATAN", $reqCatatan[$i]);
				$paket_rekanan_kualifikasi4->setField("NILAI", $reqNilaiSertifikat[$i]);
				$paket_rekanan_kualifikasi4->setField("STATUS", $status4);
				$paket_rekanan_kualifikasi4->setField("CREATED_BY", $this->USER_LOGIN_ID);
				$paket_rekanan_kualifikasi4->delete();
				$paket_rekanan_kualifikasi4->insert2();
				unset($paket_rekanan_kualifikasi4);

			}
 
			echo "Data berhasil disimpan.";
		}
		
	}
	
	
		
	
}
?>
