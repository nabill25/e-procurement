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

class rekanan_evaluasi_teknis_tawar_json extends CI_Controller {

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
	
	function set_evaluasi_teknis() 
	{
		$this->load->library("kauth");  $userLogin = new kauth(); 
		$this->load->model("RekananEvaluasiTeknisTawar");
		$rekanan_evaluasi_teknis = new RekananEvaluasiTeknisTawar();
		
		$reqId = httpFilterGet("reqId");
		$arrId = explode(";", $reqId);
		
		$reqPaketRekananId = $arrId[0];
		$reqEvaluasiTeknisId = $arrId[1];
		
		/* LOGIN CHECK */
		if ($userLogin->checkUserLogin()) 
		{ 
			$userLogin->retrieveUserInfo();
		}
		
		//, , 
		$check = $rekanan_evaluasi_teknis->getCountByParams(array("PAKET_REKANAN_ID" => $reqPaketRekananId, "PAKET_EVAL_TEKNIS_TAWAR_ID" => $reqEvaluasiTeknisId));
		
		if($check == 0)
		{	
			$rekanan_evaluasi_teknis->setField("PAKET_REKANAN_ID", $reqPaketRekananId);
			$rekanan_evaluasi_teknis->setField("PAKET_EVAL_TEKNIS_TAWAR_ID", $reqEvaluasiTeknisId);
			$rekanan_evaluasi_teknis->insertStatus();
		}
		else
		{
			$rekanan_evaluasi_teknis->setField("STATUS", "(SELECT DECODE(COALESCE(STATUS, 0), 0, 1, 0) FROM PAKET_EVAL_TEKNIS_TAWAR X WHERE X.PAKET_EVAL_TEKNIS_TAWAR_ID = A.PAKET_EVAL_TEKNIS_TAWAR_ID)");
			$rekanan_evaluasi_teknis->setField("PAKET_REKANAN_ID", $reqPaketRekananId);
			$rekanan_evaluasi_teknis->setField("PAKET_EVAL_TEKNIS_TAWAR_ID", $reqEvaluasiTeknisId);
			$rekanan_evaluasi_teknis->updateStatus();
		}
		$met = array();
		$i=0;
		
		$met[0]['STATUS'] = 1;
		echo json_encode($met);
	}
	
	function evaluasi_penawaran()
	{

		$this->load->library("paketinfo"); $paketInfo = new paketinfo();
		$this->load->model("PaketEvaluasiTeknisTawar");
		$this->load->model("PaketRekanan");
		$this->load->model("PaketDokumen");
		$this->load->model("RekananEvaluasiTeknisTawar");
		$this->load->model("PaketTahap");
		
		$paket_tahap_metode = new PaketTahap();
		$paket_tahap = new PaketTahap();
		
		$paket_evaluasi_teknis = new PaketEvaluasiTeknisTawar();
		$paket_rekanan = new PaketRekanan();
		
		$reqId = $this->input->post("reqId");
		$submitSimpan = $this->input->post("submitSimpan");
		$reqPaketRekananId = $_POST["reqPaketRekananId"];
		$reqPaketEvaluasiId = $_POST["reqPaketEvaluasiId"];
		$reqEvaluasiTeknisSyarat = $_POST["reqEvaluasiTeknisSyarat"];
		$reqUraian = $_POST["reqUraian"];
		$reqKeterangan = $_POST["reqKeterangan"];
		$reqSkorTeknis = $_POST["reqSkorTeknis"] ? $_POST["reqSkorTeknis"] : '0';
		$reqNilaiTeknis = $_POST["reqNilaiTeknis"] ? $_POST["reqNilaiTeknis"] : '0';
		
		if($submitSimpan == "Simpan")
		{
			if (isset($reqPaketRekananId) > 0) {
				for($i=0;$i<count($reqPaketRekananId);$i++)
				{
					$skor_teknis = $reqSkorTeknis[$i] ? $reqSkorTeknis[$i] : '0';
					$nilai_teknis = $reqNilaiTeknis[$i] ? $reqNilaiTeknis[$i] : '0';

					$rekanan_evaluasi_teknis = new RekananEvaluasiTeknisTawar();
					$check = $rekanan_evaluasi_teknis->getCountByParams(array("PAKET_REKANAN_ID" => $reqPaketRekananId[$i], "PAKET_EVAL_TEKNIS_TAWAR_ID" => $reqPaketEvaluasiId[$i]));

					$rekanan_evaluasi_teknis->setField('CREATED_BY', $this->USER_LOGIN_ID);

					if($check == 0)
					{	
						if ($reqEvaluasiTeknisSyarat[$i] != '') {
							$rekanan_evaluasi_teknis->setField("PAKET_REKANAN_ID", $reqPaketRekananId[$i]);
							$rekanan_evaluasi_teknis->setField("PAKET_EVAL_TEKNIS_TAWAR_ID", $reqPaketEvaluasiId[$i]);
							$rekanan_evaluasi_teknis->setField("MEMENUHI_SYARAT", $reqEvaluasiTeknisSyarat[$i]);
							$rekanan_evaluasi_teknis->setField("URAIAN", $reqUraian[$i]);
							$rekanan_evaluasi_teknis->setField("KETERANGAN", $reqKeterangan[$i]);
							$rekanan_evaluasi_teknis->setField("SKOR_TEKNIS", $skor_teknis);
							$rekanan_evaluasi_teknis->setField("NILAI_TEKNIS", $nilai_teknis);
							$rekanan_evaluasi_teknis->insertSyarat();		
						}
					}
					else
					{
						$rekanan_evaluasi_teknis->setField("MEMENUHI_SYARAT", $reqEvaluasiTeknisSyarat[$i]);
						$rekanan_evaluasi_teknis->setField("PAKET_REKANAN_ID", $reqPaketRekananId[$i]);
						$rekanan_evaluasi_teknis->setField("PAKET_EVAL_TEKNIS_TAWAR_ID", $reqPaketEvaluasiId[$i]);
						$rekanan_evaluasi_teknis->setField("URAIAN", $reqUraian[$i]);
						$rekanan_evaluasi_teknis->setField("KETERANGAN", $reqKeterangan[$i]);
						$rekanan_evaluasi_teknis->setField("SKOR_TEKNIS", $skor_teknis);
						$rekanan_evaluasi_teknis->setField("NILAI_TEKNIS", $nilai_teknis);
						$rekanan_evaluasi_teknis->updateSyarat();
					}
					unset($rekanan_evaluasi_teknis);	
				}
			} else {}
			
			// Insert Rekam Jejak
		    $this->load->library("librekamjejak"); 
		    $this->librekamjejak->insertRJ('19','',$reqId,'null','19'); // param 1: Posisi/'null', param 2: Keterangan/'', param 3: Paket_id/'null', param 4: permohonan_id/'null'
		    // End Insert Rekam Jejak

			echo 'Data berhasil di simpan';
		}

	}	
}
?>
