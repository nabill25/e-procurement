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

class sap_pr_service_json extends CI_Controller {

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
	
	function hapus_service() 
	{
		$reqId = $this->input->get("reqId");
		
		$this->load->model("SAP/SapPrService");
		$sap_pr_service = new SapPrService();
		
		/* CHECK ID APA ADA KEMBAR */
		$jumlahData = $sap_pr_service->getCountByParams(array("SAP_PR_SERVICE_ID" => $reqId));
		
		if($jumlahData > 1)
		{
			echo "Terdapat lebih dari 1 data dengan ID sama. Hubungi Adminstrator...";	
			return;
		}
		
		$sap_pr_service->setField("SAP_PR_SERVICE_ID", $reqId);
		if($sap_pr_service->delete())
		{
			echo "Data berhasil dihapus.";	
		}
		else
			echo "Data gagal dihapus.";	
			
	}
	
	
}
?>
