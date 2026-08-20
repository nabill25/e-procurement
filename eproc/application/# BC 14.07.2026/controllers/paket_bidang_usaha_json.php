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

class paket_bidang_usaha_json extends CI_Controller {

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
	
	function get_data_tambah_rekanan() 
	{
		$this->load->library("kauth");  $userLogin = new kauth(); 
		$this->load->model("PaketBidangUsaha");
		$bidang_usaha = new PaketBidangUsaha();
		
		$reqId = httpFilterGet("reqId");
		$reqPaketId = httpFilterGet("reqPaketId");
		
		/* LOGIN CHECK */
		if ($userLogin->checkUserLogin()) 
		{ 
			$userLogin->retrieveUserInfo();
		}
		//$reqId = 354;
		//$reqPaketId = 120;
		
		$bidang_usaha->selectByParamsRekanan(array(), -1, -1, $reqPaketId, " AND NOT A.REKANAN_ID IN (".$reqId.")"); 
		//echo $bidang_usaha->query;
		//$bidang_usaha->selectByParams(array("REKANAN_ID" => $userLogin->userRekanan), -1, -1); 
		$met = array();
		$i=0;
		
		while($bidang_usaha->nextRow()){
			$met[$i]['NAMA'] = $bidang_usaha->getField('NAMA');
			$alamat = str_replace("\r\n",'',$bidang_usaha->getField("ALAMAT"));
			$met[$i]['ALAMAT'] = $alamat;
			$met[$i]['EMAIL'] = $bidang_usaha->getField('EMAIL');
			$met[$i]['REKANAN_ID'] = $bidang_usaha->getField('REKANAN_ID');
			$i++;
		}
		echo json_encode($met);
	}
	
	
}
?>
