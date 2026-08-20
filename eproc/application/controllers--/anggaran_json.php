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

class anggaran_json extends CI_Controller {

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
	
	function anggaran_combo_json() 
	{
		$this->load->library("kauth");  $userLogin = new kauth(); 
		$this->load->model("Anggaran");
		$anggaran = new Anggaran();
		
		/* LOGIN CHECK */
		if ($userLogin->checkUserLogin()) 
		{ 
			$userLogin->retrieveUserInfo();
		}
		
		
		$anggaran->selectByParams(array());
		
		echo $anggaran->errorMsg;
		
		$arr_json = array();
		$i = 0;
		while($anggaran->nextRow())
		{
			$arr_json[$i]['id'] = $anggaran->getField("NO_PPA");
			$arr_json[$i]['text'] = utf8_encode($anggaran->getField("KET_TAMBAH"));
			$arr_json[$i]['dasar'] = $anggaran->getField("DASAR_PPA");
			
			$i++;
		}
		echo json_encode($arr_json, JSON_UNESCAPED_UNICODE);
	}
	
	function anggaran_get_json() 
	{
		$this->load->library("kauth");  $userLogin = new kauth(); 
		$this->load->model("Anggaran");
		$anggaran = new Anggaran();
		
		$reqId = httpFilterGet("reqId");
		
		$anggaran->selectByParams(array("NO_PPA" => $reqId));
		$anggaran->firstRow();
		$orders[] = array(
						'NAMA' => $anggaran->getField("KET_TAMBAH"),
						'NO_PPA' => $anggaran->getField("NO_PPA")
					);
		
		
		echo json_encode($orders);
	}
	
}
?>
