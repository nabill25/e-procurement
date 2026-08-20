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

class aes_json extends CI_Controller {

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
	
	function decode_qr() 
	{
		error_reporting(0);
		$reqId = $this->input->get("reqId");
		
		include_once("WEB-INF/classes/utils/AES.php");
		$aes = new AES();
		
		$decrypt = $aes->decrypt($reqId);
		if($decrypt == "")
			echo "QR tidak dikenali. Pastikan QR Code adalah QR Dokumen.";
		else
			echo $decrypt;
	}
	
	
}
?>
