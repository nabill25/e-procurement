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

class mata_uang_json extends CI_Controller {

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
		$this->load->model('MataUang');
		$mata_uang = new MataUang();
		
		$mata_uang->selectByParams();
		
		$i = 0;
		while($mata_uang->nextRow())
		{
			$arr_json[$i]['id']		= $mata_uang->getField("MATA_UANG_ID");
			$arr_json[$i]['text']	= $mata_uang->getField("NAMA_KODE");
			$i++;
		}
		
		echo json_encode($arr_json);
	}
	
	function comboMataUang() 
	{
		$this->load->model('MataUang');
		$mata_uang = new MataUang();
		
		$mata_uang->selectByParams();
		
		$i = 0;
		while($mata_uang->nextRow())
		{
			$arr_json[$i]['id']		= $mata_uang->getField("KODE");
			$arr_json[$i]['text']	= $mata_uang->getField("KODE");
			$i++;
		}
		
		echo json_encode($arr_json);
	}
	
}
?>
