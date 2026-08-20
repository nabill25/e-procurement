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

class user_type_json extends CI_Controller {

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
		$this->load->model('UserType');
		$user_type = new UserType();
		
		$user_type->selectByParams();
		
		$i = 0;
		while($user_type->nextRow())
		{
			$arr_json[$i]['id']		= $user_type->getField("USER_TYPE_ID");
			$arr_json[$i]['text']	= $user_type->getField("NAMA");
			$i++;
		}
		
		echo json_encode($arr_json);
	}
	
}
?>
