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

class permohonan_paket_approval_json extends CI_Controller {

	function __construct() {
		parent::__construct(); $this->db->query("SET TIME ZONE 'Asia/Jakarta'");

		//kauth
		if (!$this->kauth->getInstance()->hasIdentity())
		{
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

	function approval()
	{
		$approval = $this->input->post("approval"); // Status Approval
		$permohonanId = $this->input->post("permohonanId"); // Master Checklist ID

		$this->load->model("Permohonanpaketapproval");
		$cekData = new Permohonanpaketapproval();
		$insertChecklist = new Permohonanpaketapproval();

		$cekData->selectByParams(array("PERMOHONAN_PAKET_ID"=>$permohonanId, "APPROVED_BY" => $this->USER_LOGIN_ID),-1,-1);

		$insertChecklist->setField('PERMOHONAN_PAKET_ID', $permohonanId);
		$insertChecklist->setField('APPROVED', $approval);
		$insertChecklist->setField('CREATED_BY', $this->USER_LOGIN_ID);

		if ($cekData->countRow() > 0) {
		} else {
			$insertChecklist->insert();
		}

		$aksi = $insertChecklist->update();
		if ($aksi) {
			$arrJson['PESAN'] = 'Data berhasil disimpan';
			$arrJson['RESPONSE'] = 'Sukses';
		} else {
			$arrJson['PESAN'] = 'Data gagal disimpan';
			$arrJson['RESPONSE'] = 'Gagal';
		}

		echo json_encode($arrJson);
	}

	function addRevisi() 
	{
		$this->load->model(array('Permohonanpaketapprovalrevisi','Permohonanpaket'));
		$this->load->library("FileHandler"); 
		$permohonanpaketapprovalrevisi	= new Permohonanpaketapprovalrevisi();
		$permohonanpaket	= new Permohonanpaket();
		$file = new FileHandler();
		
		$reqId		= $this->input->post('reqId'); // PermohonanID
		$reqMode	= $this->input->post('reqMode');

		$permohonanpaket->selectByParams(array("A.PERMOHONAN_PAKET_ID"=>$reqId),-1,-1);
		$permohonanpaket->firstRow();
		$permohonananalisaid = $permohonanpaket->getField("PERMOHONAN_PAKET_ANALISA_ID");
		
		$reqCatatan			= $this->input->post('reqCatatan');
		$reqTanggal			= date('Y-m-d H:i:s');
		
		$reqLinkFile			= $_FILES['reqLinkFile'];
		$reqLinkFileTemp = $this->input->post("reqLinkFileTemp");
		
		$FILE_DIR = "uploads/permohonan/";
		
		if($reqMode == "insert")
		{ 
			$permohonanpaketapprovalrevisi->setField("CATATAN", $reqCatatan); 
			$permohonanpaketapprovalrevisi->setField("PERMOHONAN_PAKET_ID", $reqId); 
			$permohonanpaketapprovalrevisi->setField("PERMOHONAN_PAKET_ANALISA_ID", $permohonananalisaid); 
			$permohonanpaketapprovalrevisi->setField("CREATED_BY", $this->USER_LOGIN_ID); 
			
			$renameFile = md5(date("dmYHis").$reqLinkFile['name'].$this->ID).".".getExtension($reqLinkFile['name']);
			if($file->uploadToDir('reqLinkFile', $FILE_DIR, $renameFile))
			{
				$insertLinkFile =  $renameFile;
			}
			else
			{
				$insertLinkFile =  $reqLinkFileTemp;
			}
			$permohonanpaketapprovalrevisi->setField("FILE", $insertLinkFile);
			$permohonanpaketapprovalrevisi->insert();

			// Insert Rekam Jejak
		    $this->load->library("librekamjejak");
		    $this->librekamjejak->insertRJ('711','','null',$reqId,'711'); // param 1: Posisi/'null', param 2: Keterangan/'', param 3: Paket_id/'null', param 4: permohonan_id/'null'
		    // End Insert Rekam Jejak
			
		}
		else
		{
			 
		}
		
		echo "Data berhasil disimpan.";
	}
	
	function delete() 
	{
		$this->load->model('Permohonanpaketapprovalrevisi');
		
		$banner	= new Permohonanpaketapprovalrevisi();
		
		$reqId		= $this->input->get('reqId');
		
		$reqNama		= $this->input->post('reqNama');
		
		$banner	= new Banner();
		$banner->setField("BANNER_ID", $reqId);
		$banner->delete();
		
		echo "Data berhasil disimpan.";
	}


}
?>
