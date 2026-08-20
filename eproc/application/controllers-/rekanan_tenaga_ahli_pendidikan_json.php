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

class rekanan_tenaga_ahli_pendidikan_json extends CI_Controller {

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

	function delete()
	{
		$this->load->model("RekananTenagaAhliPendidikan");
		
		$reqId = $this->input->get("reqId");
		
		$rekanan_tenaga_ahli = new RekananTenagaAhliPendidikan();
		
		$rekanan_tenaga_ahli->setField("REKANAN_TENAGA_AHLI_PEND_ID", $reqId);
		$rekanan_tenaga_ahli->setField('REKANAN_ID', $this->ID);
		if($rekanan_tenaga_ahli->delete())
		{
			echo 'Data berhasil dihapus.';	
		} 
		else 
		{
			echo 'Data gagal dihapus.';	
		}	
			
	}
	
}
?>
