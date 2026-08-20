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

class paket_jenis_json extends CI_Controller {

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
		$this->load->model('PaketJenis');
		$paket_jenis = new PaketJenis();

		$paket_jenis->selectByParams(array("AKTIF" => '1'));

		$i = 0;
		while($paket_jenis->nextRow())
		{
			$arr_json[$i]['id']		= $paket_jenis->getField("PAKET_JENIS_ID");
			$arr_json[$i]['text']	= $paket_jenis->getField("NAMA");
			$i++;
		}

		echo json_encode($arr_json);
	}

}
?>
