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

class ijin_usaha_json extends CI_Controller {

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
	
	function combo() 
	{
		$this->load->model('IjinUsaha');
		$ijin_usaha = new IjinUsaha();
		
		$ijin_usaha->selectByParams(array(),-1,-1, " AND IJIN_USAHA_ID NOT IN(99) AND IJIN_USAHA_ID NOT IN (SELECT IJIN_USAHA_ID FROM REKANAN_IJIN_USAHA WHERE REKANAN_ID = '".$this->ID."') AND AKTIF='1'");
		
		$i = 0;
		while($ijin_usaha->nextRow())
		{
			$arr_json[$i]['id']		= $ijin_usaha->getField("IJIN_USAHA_ID");
			$arr_json[$i]['text']	= $ijin_usaha->getField("NAMA");
			$i++;
		}
		
		echo json_encode($arr_json);
	}

	function combosiup() 
	{
		$this->load->model('IjinUsaha');
		$ijin_usaha = new IjinUsaha();
		
		$ijin_usaha->selectByParams(array(),-1,-1, " AND IJIN_USAHA_ID = '1'");
		
		$i = 0;
		while($ijin_usaha->nextRow())
		{
			$arr_json[$i]['id']		= $ijin_usaha->getField("IJIN_USAHA_ID");
			$arr_json[$i]['text']	= $ijin_usaha->getField("NAMA");
			$i++;
		}
		
		echo json_encode($arr_json);
	}
	
}
?>
