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

class paket_progres_komen_json extends CI_Controller {

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
	
	function qry() 
	{
		$this->load->model("PaketProgresKomen");
		$this->load->library("kauth");  $userLogin = new kauth(); 
		
		$set= new PaketProgresKomen();
		
		
		$str = "";
		
		if($set->execSQL($str))
		{
			echo "Data berhasil dieksekusi.";
		}
	}
	
	function timeline_paket_komen() 
	{
		$this->load->library("kauth");  $userLogin = new kauth(); 
		$this->load->model("PaketProgresKomen");
		
		$reqId= httpFilterPost("reqId");
		$reqMode = httpFilterPost("reqMode");
		$reqKeterangan= httpFilterPost("reqKeterangan");
							
		if($reqMode == "insert")
		{
			$set= new PaketProgresKomen();
			$set->setField("USER_LOGIN_ID", $userLogin->UID);
			$set->setField("KETERANGAN", $reqKeterangan);
			$set->setField("PAKET_PROGRES_ID", $reqId);
			$set->setField("LAST_CREATE_USER", $userLogin->idUser);
			$set->setField("LAST_CREATE_DATE", "CURRENT_DATE");
			if($set->insert())
			{
				echo "Data berhasil disimpan.";
			}
		}
	}
	
	
}
?>
