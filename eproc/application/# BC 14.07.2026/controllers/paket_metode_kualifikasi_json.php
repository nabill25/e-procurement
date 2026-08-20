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

class paket_metode_kualifikasi_json extends CI_Controller {

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
		$this->load->model('Metode');

		$reqMetodePengadaan = $this->input->get("reqMetodePengadaan");

		$paket_metode_kualifikasi = new Metode();

		$paket_metode_kualifikasi->selectByParamsMetodeKualifikasi(array('PAKET_METODE_LELANG_ID'=>$reqMetodePengadaan));

		$i = 0;
		while($paket_metode_kualifikasi->nextRow())
		{
			$arr_json[$i]['id']		= $paket_metode_kualifikasi->getField("PAKET_METODE_KUALIFIKASI_ID");
			$arr_json[$i]['text']	= $paket_metode_kualifikasi->getField("PAKET_METODE_KUALIFIKASI");
			$i++;
		}

		echo json_encode($arr_json);
	}

}
?>
