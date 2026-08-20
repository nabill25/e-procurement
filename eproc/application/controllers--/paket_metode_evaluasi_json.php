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

class paket_metode_evaluasi_json extends CI_Controller {

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
		
		$reqJenisPekerjaan = $this->input->get("reqJenisPekerjaan");
		
		$paket_metode_evaluasi = new Metode();
		
		$paket_metode_evaluasi->selectByParamsMetodeEvaluasi(array('PAKET_JENIS_ID'=>$reqJenisPekerjaan));
		
		$i = 0;
		while($paket_metode_evaluasi->nextRow())
		{
			$arr_json[$i]['id']		= $paket_metode_evaluasi->getField("PAKET_METODE_EVALUASI_ID");
			$arr_json[$i]['text']	= $paket_metode_evaluasi->getField("PAKET_METODE_EVALUASI");
			$i++;
		}
		
		echo json_encode($arr_json);
	}

	function combo2() 
	{
		$this->load->model('Metode');
		
		$reqJenisPekerjaan = $this->input->get("reqJenisPekerjaan");
		
		$paket_metode_evaluasi = new Metode();
		
		$paket_metode_evaluasi->selectByParamsMetodeEvaluasi2(array(),null,null,'AND B.PAKET_METODE_LELANG_ID LIKE \'%'.$reqJenisPekerjaan.'%\'');
		
		$i = 0;
		while($paket_metode_evaluasi->nextRow())
		{
			$arr_json[$i]['id']		= $paket_metode_evaluasi->getField("PAKET_METODE_EVALUASI_ID");
			$arr_json[$i]['text']	= $paket_metode_evaluasi->getField("PAKET_METODE_EVALUASI");
			$i++;
		}
		
		echo json_encode($arr_json);
	}
	
}
?>
