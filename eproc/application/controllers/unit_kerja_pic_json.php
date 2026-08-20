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

class unit_kerja_pic_json extends CI_Controller {

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
	
	function add() 
	{
		$this->load->model('UnitKerjaPic');
		
		$reqDelete = $this->input->post("reqDelete");
		$reqId = $this->input->post("reqId");
		$reqMode = $this->input->post("reqMode");
		
		$reqUnitKerjaPicId = $this->input->post("reqUnitKerjaPicId");
		$reqNip = $this->input->post("reqNip");
		$reqNama = $this->input->post("reqNama");
		
		if($reqDelete == '0')
		{}
		else
		{
			$arrDeleteOut = explode(",", $reqDelete);
			for($d=0;$d<count($arrDeleteOut);$d++)
			{
				$unit_kerja_pic_delete = new UnitKerjaPic();
				$unit_kerja_pic_delete->setField("UNIT_KERJA_PIC_ID", $arrDeleteOut[$d]);
				$unit_kerja_pic_delete->delete();
			}
		}
		
		for($i=0;$i<count($reqNip);$i++)
		{
			if($reqNip[$i] == "")
			{}
			else
			{
				$unit_kerja_pic = new UnitKerjaPic();
				
				$unit_kerja_pic->setField("UNIT_KERJA_PIC_ID", $reqUnitKerjaPicId[$i]);
				$unit_kerja_pic->setField("UNIT_KERJA_ID", $reqId);
				$unit_kerja_pic->setField("NIP", $reqNip[$i]);
				$unit_kerja_pic->setField("NAMA", $reqNama[$i]);
				
				if($reqUnitKerjaPicId[$i] == "")
				{
					$unit_kerja_pic->insert();
				}
				else
				{
					$unit_kerja_pic->update();
				}
				unset($unit_kerja_pic);
			}
		}
		
		echo "Data berhasil disimpan.";
	}
	
	function delete() 
	{
		$this->load->model('UnitKerjaPic');
		$unit_kerja_pic = new UnitKerjaPic();
		
		$reqId =  $this->input->get('reqId');
		
		$unit_kerja_pic->setField("UNIT_KERJA_PIC_ID", $reqId);
		
		if($unit_kerja_pic->delete())
			$arrJson["PESAN"] = "Data berhasil dihapus.";
		else
			$arrJson["PESAN"] = "Data gagal dihapus.";		
		
		echo json_encode($arrJson);
		
	}
	
	
}
?>
