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

class negara_json extends CI_Controller {

	function __construct() {
		parent::__construct(); $this->db->query("SET TIME ZONE 'Asia/Jakarta'");

		if (!$this->kauth->getInstance()->hasIdentity())
		{
		}

		$this->ID = $this->kauth->getInstance()->getIdentity()->REKANAN_ID;
	}

	function combo()
	{
		$this->load->model('Negara');
		$negara = new Negara();

		$negara->selectByParams();

		$i = 0;
		while($negara->nextRow())
		{
			$arr_json[$i]['id']		= $negara->getField("ID");
			$arr_json[$i]['text']	= $negara->getField("NAMA");
			$i++;
		}

		echo json_encode($arr_json);
	}

}
?>
