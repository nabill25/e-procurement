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

class region_json extends CI_Controller {

	function __construct() {
		parent::__construct(); $this->db->query("SET TIME ZONE 'Asia/Jakarta'"); 
		if (!$this->kauth->getInstance()->hasIdentity()) { }       
		$this->ID = isset($this->kauth->getInstance()->getIdentity()->REKANAN_ID)?$this->kauth->getInstance()->getIdentity()->REKANAN_ID:'';   
	}	
	
	function combo() 
	{
		$this->load->model('Indowilayah2023');
		$provinsi = new Indowilayah2023();
		
		$provinsi->selectProvinsi();
		
		$i = 0;
		while($provinsi->nextRow())
		{
			$arr_json[$i]['id']		= $provinsi->getField("NAMAPROPINSI");
			$arr_json[$i]['text']	= $provinsi->getField("NAMAPROPINSI");
			$i++;
		}
		
		echo json_encode($arr_json);
	}

	function combokabkot() 
	{
		$reqProvinsi = $this->input->get("reqProvinsi");

		$this->load->model('Indowilayah2023');
		$kabkot = new Indowilayah2023();
		$kabkot->selectKabKot($reqProvinsi);
		
		$i = 0;
		while($kabkot->nextRow())
		{
			$arr_json[$i]['id']		= $kabkot->getField("NAMAKABKOTA");
			$arr_json[$i]['text']	= $kabkot->getField("NAMAKABKOTA");
			$i++;
		}
		
		echo json_encode($arr_json);
	}

	function combokecamatan() 
	{
		$reqKabkot = $this->input->get("reqKabkot");
		$reqProvinsi = $this->input->get("reqProvinsi");

		$this->load->model('Indowilayah2023');
		$kecamatan = new Indowilayah2023();
		$kecamatan->selectKecamatan($reqProvinsi,$reqKabkot);
		
		$i = 0;
		while($kecamatan->nextRow())
		{
			$arr_json[$i]['id']		= $kecamatan->getField("NAMAKECAMATAN");
			$arr_json[$i]['text']	= $kecamatan->getField("NAMAKECAMATAN");
			$i++;
		}
		
		echo json_encode($arr_json);
	}

	function combokelurahan() 
	{
		$reqProvinsi = $this->input->get("reqProvinsi");
		$reqKabkot = $this->input->get("reqKabkot");
		$reqKecamatan = $this->input->get("reqKecamatan");

		$this->load->model('Indowilayah2023');
		$kelurahan = new Indowilayah2023();
		$kelurahan->selectKelurahan($reqProvinsi,$reqKabkot,$reqKecamatan);
		
		$i = 0;
		while($kelurahan->nextRow())
		{
			$arr_json[$i]['id']		= $kelurahan->getField("KELURAHAN");
			$arr_json[$i]['text']	= $kelurahan->getField("KELURAHAN");
			$i++;
		}
		
		echo json_encode($arr_json);
	}

	
	
}
?>
