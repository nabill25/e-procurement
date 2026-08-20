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

class panel_bidang_usaha_json extends CI_Controller {

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
	
	function panel_bidang_usaha_combo_json() 
	{
		$this->load->library("kauth");  $userLogin = new kauth(); 
		$this->load->model("PanelBidangUsaha");
		$panel_bidang_usaha= new PanelBidangUsaha();
		
		/* LOGIN CHECK */
		if ($userLogin->checkUserLogin()) 
		{ 
			$userLogin->retrieveUserInfo();
		}
		
		$reqId= httpFilterGet("reqId");
		
		$arr_json = array();
		$i = 0;
		
		$panel_bidang_usaha->selectByParams(array("A.PANEL_ID" => $reqId));
		while($panel_bidang_usaha->nextRow()){
			$arr_json[$i]['id'] = $panel_bidang_usaha->getField("PANEL_BIDANG_USAHA_ID");
			$arr_json[$i]['text'] = $panel_bidang_usaha->getField("NAMA");
			$i++;
		}
		
		echo json_encode($arr_json);
	}
	
	function panel_rekanan_peringkat_versi_json() 
	{

		$this->load->library("kauth");  $userLogin = new kauth(); 
		$this->load->model("PanelBidangUsaha");
		$panel_bidang_usaha = new PanelBidangUsaha();
		
		$reqId = httpFilterGet("reqId");
		$reqBidangUsaha = httpFilterGet("reqBidangUsaha");
		
		$panel_bidang_usaha->selectByParams(array("PANEL_ID" => $reqId, "MD5(BIDANG_USAHA_ID)" => $reqBidangUsaha));
		$panel_bidang_usaha->firstRow();
		
		$arrFinal = array("STATUS" => $panel_bidang_usaha->getField("PERUBAHAN_DATA"));
		echo json_encode($arrFinal);
	}
	
	
}
?>
