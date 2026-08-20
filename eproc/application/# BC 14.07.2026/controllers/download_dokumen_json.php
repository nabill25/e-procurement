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
include_once("functions/default.func.php");

class download_dokumen_json extends CI_Controller {

	function __construct() {
		parent::__construct(); $this->db->query("SET TIME ZONE 'Asia/Jakarta'");

		//kauth
		if (!$this->kauth->getInstance()->hasIdentity()) { }

		/* GLOBAL VARIABLE */
		$this->ID = isset($this->kauth->getInstance()->getIdentity()->REKANAN_ID) ? $this->kauth->getInstance()->getIdentity()->REKANAN_ID : '';

	    $this->USER_LOGIN_ID =  isset($this->kauth->getInstance()->getIdentity()->USER_LOGIN_ID) ? $this->kauth->getInstance()->getIdentity()->USER_LOGIN_ID : '';
	    $this->USER_LOGIN =  isset($this->kauth->getInstance()->getIdentity()->USER_LOGIN) ? $this->kauth->getInstance()->getIdentity()->USER_LOGIN : '';
	    $this->USER_NAMA =  isset($this->kauth->getInstance()->getIdentity()->USER_NAMA) ? $this->kauth->getInstance()->getIdentity()->USER_NAMA : '';
	    $this->USER_TYPE_ID =  isset($this->kauth->getInstance()->getIdentity()->USER_TYPE_ID) ? $this->kauth->getInstance()->getIdentity()->USER_TYPE_ID : '';
	    $this->REKANAN_ID =  isset($this->kauth->getInstance()->getIdentity()->REKANAN_ID) ? $this->kauth->getInstance()->getIdentity()->REKANAN_ID : '';
	    $this->UNIT_KERJA_ID =  isset($this->kauth->getInstance()->getIdentity()->UNIT_KERJA_ID) ? $this->kauth->getInstance()->getIdentity()->UNIT_KERJA_ID : '';
	    $this->NIP =  isset($this->kauth->getInstance()->getIdentity()->NIP) ? $this->kauth->getInstance()->getIdentity()->NIP : '';
	    $this->LOGIN_TIME = isset($this->kauth->getInstance()->getIdentity()->LOGIN_TIME) ? $this->kauth->getInstance()->getIdentity()->LOGIN_TIME : '';
	    $this->LOGIN_DATE = isset($this->kauth->getInstance()->getIdentity()->LOGIN_DATE) ? $this->kauth->getInstance()->getIdentity()->LOGIN_DATE : '';
	    $this->REKANAN = isset($this->kauth->getInstance()->getIdentity()->NAMA) ? $this->kauth->getInstance()->getIdentity()->NAMA : '';
	    $this->REKANAN_KODE = isset($this->kauth->getInstance()->getIdentity()->KODE) ? $this->kauth->getInstance()->getIdentity()->KODE : '';
	    $this->REKANAN_PKP = isset($this->kauth->getInstance()->getIdentity()->PKP) ? $this->kauth->getInstance()->getIdentity()->PKP : '';
	    $this->REKANAN_NPWP = isset($this->kauth->getInstance()->getIdentity()->NPWP) ? $this->kauth->getInstance()->getIdentity()->NPWP : '';
	    $this->REKANAN_STATUS_PERUSAHAAN = isset($this->kauth->getInstance()->getIdentity()->STATUS_PERUSAHAAN) ? $this->kauth->getInstance()->getIdentity()->STATUS_PERUSAHAAN : '';
	    $this->REKANAN_STATUS_VALIDASI = isset($this->kauth->getInstance()->getIdentity()->STATUS_VALIDASI) ? $this->kauth->getInstance()->getIdentity()->STATUS_VALIDASI : '';
	}
 
	function getDok($reqId,$paketDokumenId,$dok)
	{
		// echo $reqId.'--'.$paketDokumenId.'--'.$dok; die;	
		$path = 'uploads/lelang/';

		$this->load->model("PaketRekanan");
		$this->load->model("PaketDokumenDownload");
		$paket_rekanan_check = new PaketRekanan();
		$check = $paket_rekanan_check->getCountByParams(array("PAKET_ID" => $reqId, "REKANAN_ID" => $this->REKANAN_ID));
		if($check == 0)
		{
			$this->load->library("libsession"); $libsession = new libsession();
            $ins = $libsession->insertLogs();
			if ($ins) {
			  redirect(base_url().'main/index/403');
			}
		} 

		$paket_dokumen_download = new PaketDokumenDownload();
		$paket_dokumen_download->setField("PAKET_ID", $reqId);
		$paket_dokumen_download->setField("REKANAN_ID", $this->ID);
		$paket_dokumen_download->setField("PAKET_DOKUMEN_ID", $paketDokumenId);
		$paket_dokumen_download->setField("CREATED_BY", $this->USER_LOGIN_ID);
		$paket_dokumen_download->insert();
	    $idnya = $paket_dokumen_download->id;

		if($idnya) {
			  redirect(base_url($path.$dok));
		    // $this->load->helper('download');
		    // $data = file_get_contents($path.$dok);
		    // force_download($dok, $data);
			// echo "string"; die;
		} else {
		  redirect(base_url().'main/index/403');
		}
 		
	}

	function getDokContract($fileId,$contractId,$dok)
	{
		// echo $fileId.'--'.$dok.'--'.$this->USER_TYPE_ID; die;	 

		$this->load->model("Contractingfile");
		$this->load->model("PaketDokumenKontrakDownload");
		$cek_dok = new Contractingfile();
		$get_dok = new Contractingfile();

		if ($this->USER_TYPE_ID == '6') { // Penyedia di catat
			
			$cek_dok->selectByParams(array("CONTRACTINGFILEID" => $fileId, "CONTRACTINGREKANANID" => $contractId, "FILE_NAMA_ENCRYPT" => $dok, "FILE_PUBLISH_PENYEDIA" => "1"));
			if($cek_dok->countRow() == 0)
			{
				$this->load->library("libsession"); $libsession = new libsession();
	            $ins = $libsession->insertLogs();
				if ($ins) {
				  redirect(base_url().'main/index/403');
				}
			} 

			$get_dok->selectByParams(array("CONTRACTINGFILEID" => $fileId, "CONTRACTINGREKANANID" => $contractId, "FILE_NAMA_ENCRYPT" => $dok, "FILE_PUBLISH_PENYEDIA" => "1"));
			$get_dok->firstRow();

			$paket_dokumen_kontrak_download = new PaketDokumenKontrakDownload();
			$paket_dokumen_kontrak_download->setField("CONTRACTINGREKANANID", $contractId);
			$paket_dokumen_kontrak_download->setField("REKANAN_ID", $this->ID);
			$paket_dokumen_kontrak_download->setField("CONTRACTINGFILEID", $fileId);
			$paket_dokumen_kontrak_download->setField("CREATED_BY", $this->USER_LOGIN_ID);
			$paket_dokumen_kontrak_download->insert();
		    $idnya = $paket_dokumen_kontrak_download->id;

			if($idnya) {
			  redirect(base_url($get_dok->getField("FILE_PATH").$dok));
			} else {
			  redirect(base_url().'main/index/403');
			}
		}
 		
	}
 

 }
?>
