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

class jenis_pekerjaan_json extends CI_Controller {

	function __construct() {
		parent::__construct(); $this->db->query("SET TIME ZONE 'Asia/Jakarta'"); 
		
		//kauth
		if (!$this->kauth->getInstance()->hasIdentity())
		{ 
		}       
		
		/* GLOBAL VARIABLE */
		$this->ID = isset($this->kauth->getInstance()->getIdentity()->REKANAN_ID)?$this->kauth->getInstance()->getIdentity()->REKANAN_ID:'';   
	}	
	
	function combo() 
	{
		$this->load->model('Contracting');
		$contracting = new Contracting();
		
		$contracting->selectJenisPekerjaan();
		
		$i = 0;
		while($contracting->nextRow())
		{
			$arr_json[$i]['id']		= $contracting->getField("CONTRACTINGJENISPEKERJAANID");
			$arr_json[$i]['text']	= $contracting->getField("JP_NAME");
			$i++;
		}
		
		echo json_encode($arr_json);
	}
	
}
?>
