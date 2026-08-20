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

class auth_user_json extends CI_Controller {

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
	
	function panitia_combo_json() 
	{
		$this->load->library("kauth");  $userLogin = new kauth(); 
		$this->load->model("AuthUser");
		$auth_user = new AuthUser();
		
		/* LOGIN CHECK */
		if ($userLogin->checkUserLogin()) 
		{ 
			$userLogin->retrieveUserInfo();
		}
		
		$reqKdCabang = httpFilterGet("reqKdCabang");
		
		if($reqKdCabang == 1 || $reqKdCabang == 2 || $reqKdCabang == 24)
			$stat = " AND (UPPER(NAMA_SEK) LIKE '%PBJ%' OR UPPER(NAMA_SEK) LIKE '%PENGADAAN%') ";
		else
			$stat = "";
		
		$auth_user->selectByParams(array(), -1, -1, $stat." AND (KD_PEL = '".generateZero($reqKdCabang, 2)."' OR KD_PEL = '".$reqKdCabang."') ");
		
		
		$arr_json = array();
		$i = 0;
		while($auth_user->nextRow())
		{
			$arr_json[$i]['id'] = $auth_user->getField("NIPP");
			$arr_json[$i]['text'] = trim($auth_user->getField("NAMA"));
			$arr_json[$i]['jabatan'] = $auth_user->getField("NAJAB");
			$arr_json[$i]['cabang'] = $auth_user->getField("NAMA");
			
			$i++;
		}
		echo json_encode($arr_json);
	}
	
	
}
?>
