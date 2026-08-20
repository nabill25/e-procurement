<?php
/* INCLUDE FILE */
include_once("functions/string.func.php");
include_once("functions/default.func.php");

defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * @package 	eProcurement Application
 * @author 		eproc2025
 * @since 		25. Version 3.1
 * 
 */
class aanwijzing_konfirmasi_rekanan_json extends CI_Controller {

	function __construct() {
		parent::__construct(); $this->db->query("SET TIME ZONE 'Asia/Jakarta'"); 

		//kauth
		if (!$this->kauth->getInstance()->hasIdentity())
		{
			//redirect('main');
		}		
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
	function json() 
	{
		
		$this->load->library("kauth");  $userLogin = new kauth(); 
		$this->load->model("PhpShoutbox");
		$this->load->model("PaketRekanan");
		
		/* create objects */
		$php_shoutbox = new PhpShoutbox();
		$paket_rekanan = new PaketRekanan();

		$reqId = httpFilterGet("reqId");
		$reqKode = httpFilterGet("reqKode");

		$paket_rekanan->selectByParams(array("A.PAKET_ID" => $reqId, "A.REKANAN_ID" => $this->ID));
		$paket_rekanan->firstRow();
		$nickname = $paket_rekanan->getField("KODE_REKANAN");		
		
		$php_shoutbox->selectByParamsKonfirmasiRekanan(array("A.PAKET_ID" => $reqId), -1, -1, $nickname);
		$i=0;
		while($php_shoutbox->nextRow())
		{
				
			$met[$i]['KODE'] = $php_shoutbox->getField("KODE");
			$met[$i]['KODE_HALAMAN'] = $php_shoutbox->getField("KODE_HALAMAN");
			$met[$i]['HALAMAN'] = $php_shoutbox->getField("HALAMAN");
			$met[$i]['INFORMASI'] = $php_shoutbox->getField("INFORMASI");
			$i++;
		}
		echo json_encode($met);	
	}
}
?>