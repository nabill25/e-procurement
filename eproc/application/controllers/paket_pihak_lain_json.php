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

class paket_pihak_lain_json extends CI_Controller {

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
		$this->load->model("UsersBase");
		$this->load->model("PaketPihakLain");
		$paket_pihak_lain = new PaketPihakLain();
		
		/* VARIABLES */
		$reqMode = $this->input->post("reqMode");
		$reqId = $this->input->post("reqId");
		$submitSimpan = $this->input->post("submitSimpan");
		$reqPihakLain = $_POST["reqPihakLain"];
		$reqLoginId = $_POST["reqLoginId"];
		
		if($submitSimpan == "Simpan")
		{
			for($i=0; $i<count($reqLoginId);$i++)
			{
				if($reqPihakLain[$i] != '')
				{
							//create "not where" query for deletion
							$whereString .= $reqPihakLain[$i].",";
						}
				else
				{
							//check if exist yet
							$exist = new PaketPihakLain();
							$exist->selectByParams(array('PAKET_ID' => $reqId, 'A.USER_LOGIN_ID'=>$reqLoginId[$i]));
							if($exist->rowCount < 1)
							{
								//do insert
								$doInsert = new PaketPihakLain();
								$doInsert->setField('USER_LOGIN_ID', $reqLoginId[$i]);
								$doInsert->setField('STATUS', 1);
								$doInsert->setField('PAKET_ID', $reqId);
								$doInsert->insert();
								 
								//INSERT THE NEW ID TO NOT WHER STRING
								$whereString .= $doInsert->getField('PAKET_PIHAK_LAIN_ID').',';
							}
				}
						unset($exist);
						unset($doInsert);
			}
				//delete the deleted
				$doDelete = new PaketPihakLain();
				$doDelete->deleteNotIn($reqId, trim($whereString,','));
				
				echo 'Data berhasil di simpan';
		}
	}
	
	function delete_daftar_panitia() 
	{
		$this->load->model("PaketPanitia");
		$set= new PaketPanitia();
		$set->setField('PAKET_PANITIA_ID', $reqId);
		//echo $reqId
		if($set->deletePanitia())
		{	
			echo "Data berhasil dihapus";
		}
		else
			echo "Data gagal dihapus";
		//echo $set->query;
		//echo "asd";
	}
	
	
}
?>
