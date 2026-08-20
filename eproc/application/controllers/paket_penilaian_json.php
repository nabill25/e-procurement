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

class paket_penilaian_json extends CI_Controller {

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
		$this->REKANAN = $this->kauth->getInstance()->getIdentity()->NAMA;
		$this->REKANAN_KODE = $this->kauth->getInstance()->getIdentity()->KODE;
		$this->REKANAN_EMAIL = $this->kauth->getInstance()->getIdentity()->REKANAN_EMAIL;
		$this->REKANAN_PKP = $this->kauth->getInstance()->getIdentity()->PKP;
		$this->REKANAN_NPWP = $this->kauth->getInstance()->getIdentity()->NPWP;
		$this->REKANAN_STATUS_PERUSAHAAN = $this->kauth->getInstance()->getIdentity()->STATUS_PERUSAHAAN;
		$this->REKANAN_STATUS_VALIDASI = $this->kauth->getInstance()->getIdentity()->STATUS_VALIDASI;
	}	  
	
	function penilaian()
	{ 
		$this->load->model("PaketPenilaian"); 
		// echo "<pre>"; print_r($this->input->post()); die();
		$reqTemplate = $this->input->post("reqTemplate");
		$submitSimpan = $this->input->post("submitSimpan");
		$regRekananId = $this->input->post("regRekananId");
		$regId = $this->input->post("regId"); // contractingrekananid
		$reqPaketId = $this->input->post("reqPaketId");
		$regParentId = $this->input->post("regParentId");
		$getTotal = $this->input->post("getTotal"); 
		// getNAMA + value regRekananId
		// getChildId + value regRekananId
		// getNilai + increment + value regRekananId 
		
		if($submitSimpan == "Simpan")
		{
			foreach ($regParentId as $key => $value) {

				$PaketPenilaianHapus = new PaketPenilaian();
				$PaketPenilaianHapus->setField("REKANAN_ID", $regRekananId);
				$PaketPenilaianHapus->setField("PPT_PARENT_ID", $value);	
				$PaketPenilaianHapus->setField("CONTRACTINGREKANANID", $regId);	
				$PaketPenilaianHapus->setField("CREATED_BY", $this->USER_LOGIN_ID);	
				if ($this->USER_TYPE_ID == '20' || $this->USER_TYPE_ID == '28') { 
					$PaketPenilaianHapus->deleteAll();	
				} else {
					$PaketPenilaianHapus->delete();	
				}
				// echo $getTotal[$key]; die();
				// Delete yg ada
				// $PaketPenilaian->delete();	
					for($i=0;$i<$getTotal[$key];$i++)
					{
						$namaNilai = 'getNilai'.$i.$value;
						$namaPPTID = 'getChildId'.$value;
						$namaPenilaian = 'getNAMA'.$value;
						$namaPresentasi = 'getPresentasi'.$value;
						$namaNote = 'reqNote'.$value;
						
						if ($this->input->post($namaNilai)[0] != '') { 
							$PaketPenilaian = new PaketPenilaian();
							$PaketPenilaian->setField("PAKET_ID", $reqPaketId);
							$PaketPenilaian->setField("REKANAN_ID", $regRekananId);
							$PaketPenilaian->setField("CREATED_BY", $this->USER_LOGIN_ID); 
							$PaketPenilaian->setField("PPT_PARENT_ID", $value);	 
							$PaketPenilaian->setField("NILAI", $this->input->post($namaNilai)[0]);
							$PaketPenilaian->setField("NOTE", $this->input->post($namaNote)[0]);  
							$PaketPenilaian->setField("PPT_ID", $this->input->post($namaPPTID)[$i]);
							$PaketPenilaian->setField("NAMA", $this->input->post($namaPenilaian)[$i]);  
							$PaketPenilaian->setField("PRESENTASI", $this->input->post($namaPresentasi)[$i]);  
							$PaketPenilaian->setField("CONTRACTINGREKANANID", $regId);
							// $PaketPenilaian->setField("TEMPLATE", $reqTemplate);
							$PaketPenilaian->insert();	
							unset($PaketPenilaian);	 
						}	
					}
			}

			$this->load->library("librekamjejak");
			$this->librekamjejak->insertRJ('266','','null','null','266',$regId); 

			echo 'Data berhasil di simpan.';
		}
		
	}
	
}
?>
