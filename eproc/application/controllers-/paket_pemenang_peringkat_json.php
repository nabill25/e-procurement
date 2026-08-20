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


class paket_pemenang_peringkat_json extends CI_Controller {

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
		/* INCLUDE FILE */
		$this->load->model("Paketpemenangperingkat");
		$paket_pemenang = new Paketpemenangperingkat();
		
		/* VARIABLE */
		$paket_pemenang_peringkat_id = $this->input->get("reqId");
		
		$paket_pemenang->setField("PAKET_PEMENANG_PERINGKAT_ID", $paket_pemenang_peringkat_id);
		
		if($paket_pemenang->delete())
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
