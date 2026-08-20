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

class permohonan_paket_checklist_json extends CI_Controller {

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

	function updateChecklist()
	{
		$reqPerId = $this->input->get("reqPerId"); // Permohonan ID
		$masterchecklistId = $this->input->get("masterchecklistId"); // Master Checklist ID
		$status = $this->input->get("status"); // checked?

		$this->load->model("Permohonanpaketchecklist");
		$cekData = new Permohonanpaketchecklist();
		$insertChecklist = new Permohonanpaketchecklist();

		$cekData->selectByParams(array("PERMOHONAN_PAKET_ID"=>$reqPerId, "MASTER_CHECKLIST_ID" => $masterchecklistId),-1,-1);

		$insertChecklist->setField('PERMOHONAN_PAKET_ID', $reqPerId);
		$insertChecklist->setField('MASTER_CHECKLIST_ID', $masterchecklistId);
		$insertChecklist->setField('APPROVED', $status);
		$insertChecklist->setField('CREATED_BY', $this->USER_LOGIN_ID);

		if ($cekData->countRow() > 0) {
		} else {
			$insertChecklist->insert();
		}

		$update = $insertChecklist->update();
		if ($update) {
			$arrJson['PESAN'] = 'Data berhasil disimpan';
			$arrJson['RESPONSE'] = 'Sukses';
		} else {
			$arrJson['PESAN'] = 'Data gagal disimpan';
			$arrJson['RESPONSE'] = 'Gagal';
		}

		echo json_encode($arrJson);
	}

	function updateFileCheck()
	{
		$reqId = $this->input->get("reqId"); // Paket File Analisa ID
		$jenis = $this->input->get("jenis"); // Jenis ID
		$status = $this->input->get("status"); // checked?

		$this->load->model("PermohonanPaketAnalisaFile");
		$cekData = new PermohonanPaketAnalisaFile();

		$cekData->setField('PERMOHONAN_PAKET_ANALISA_FILE_ID', $reqId);
		$cekData->setField('FILE_TTE', $status);
		$cekData->setField('FILE_SHARE', $status);
		$cekData->setField('JENIS', $jenis);
		$cekData->setField('UPDATED_BY', $this->USER_LOGIN_ID);
		
		$update = $cekData->updateFileCheck();
		if ($update) {
			$arrJson['PESAN'] = 'Data berhasil disimpan';
			$arrJson['RESPONSE'] = 'Sukses';
		} else {
			$arrJson['PESAN'] = 'Data gagal disimpan';
			$arrJson['RESPONSE'] = 'Gagal';
		}

		echo json_encode($arrJson);
	}


}
?>
