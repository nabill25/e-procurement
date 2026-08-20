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

class panel_json extends CI_Controller {

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
	
	function set_publish_panel() 
	{
		$this->load->library("kauth");  $userLogin = new kauth(); 
		$this->load->model("Panel");
		$panel = new Panel();
		
		$reqId = httpFilterGet("reqId");
		
		/* LOGIN CHECK */
		if ($userLogin->checkUserLogin()) 
		{ 
			$userLogin->retrieveUserInfo();
			if($userLogin->userLevel == 6)
			{
				echo '<script language="javascript">';
				echo 'alert("Anda tidak berhak mengakses halaman ini. IP address anda telah kami catat sebagai rekanan yang mencoba membuka halaman administrator.");';
				echo 'top.location.href = "index.php";';
				echo '</script>';
				exit;		
			}
		}
		
			$panel->setField("FIELD", "PUBLISH_PANEL");
			$panel->setField("FIELD_VALUE", "(SELECT DECODE(COALESCE(PUBLISH_PANEL, 0), 0, 1, 0) FROM PANEL X WHERE X.PANEL_ID = A.PANEL_ID)");
			$panel->setField("PANEL_ID", $reqId);
			$panel->updateByField();
		$met = array();
		$i=0;
		
		$met[0]['STATUS'] = 1;
		echo json_encode($met);
	}
	
	
}
?>
