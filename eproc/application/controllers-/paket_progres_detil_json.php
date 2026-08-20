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

class paket_progres_detil_json extends CI_Controller {

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
	
	function download() 
	{
		$this->load->library("kauth");  $userLogin = new kauth(); 
		/* variable */
		$reqMode = httpFilterGet("reqMode");
		$reqRowId= httpFilterGet("reqRowId");
		
		if($reqMode == "paket_work_order")
		{
			$this->load->model("PaketProgresDetil");
			
			$lampiran = new PaketProgresDetil();
			$lampiran->selectByParamsBlob(array("PAKET_PROGRES_DETIL_ID" => $reqRowId), -1, -1);
			$FILE_DIR= "uploads/progres_paket/";
		}
		
		$lampiran->firstRow();
		//echo $lampiran->query;
		if($lampiran->getField('TIPE') == 'image/jpeg')
		{
			$tempFormat=str_replace('jpeg','jpg',$lampiran->getField('TIPE'));
		}
		else
		{
			$tempFormat=$lampiran->getField('TIPE');
		}
		
		$tipe = $tempFormat;
		$isi = $FILE_DIR.$lampiran->getField("PATH_FILE");
		$nama = $reqMode;//str_replace(" ","",$lampiran->getField("NOMOR"));
		
		header("Content-type: $tipe");
		header('Content-Disposition: filename="'.$reqMode.'"');
		header('Content-Disposition: attachment; filename='.$reqMode.'.'.getExe($tipe));		
		
		$isi= file_get_contents($isi);
		echo $isi;
		
		/*
		// untuk blob
		header("Content-type: $tipe");
		header('Content-Disposition: attachment; filename='.$nama.'.'.getExe($tipe));		
		echo $isi;
		*/
	}
	
	function progres_pelaksanaan_monitoring_add() 
	{
		$this->load->model("PaketProgresDetil");
		$this->load->library("kauth");  $userLogin = new kauth(); 
		include_once("WEB-INF/classes/utils/FileHandler.php");
		
		$paket_work_order = new PaketProgresDetil();
		$file = new FileHandler();
		
		$FILE_DIR = "../uploads/progres_paket/";
		
		$reqId = httpFilterPost("reqId");
		$reqUserLoginId= httpFilterPost("reqUserLoginId");
		$reqMode = httpFilterPost("reqMode");
		$reqRowId= $_POST["reqRowId"];
		
		$reqLinkFileTemp= $_POST["reqLinkFileTemp"];
		$reqTanggal= $_POST["reqTanggal"];
		$reqNama= $_POST["reqNama"];
		$reqKeterangan= $_POST["reqKeterangan"];
		$reqKendala= $_POST["reqKendala"];
		$reqLinkFile= $_FILES['reqLinkFile'];
		$reqProsentase = $_POST["reqProsentase"];
		
		$reqArrayIndex= $_POST["reqArrayIndex"];
		
		$set_loop= $reqArrayIndex;
		
		if($reqMode == "insert")
		{
			for($i=0;$i<=$set_loop;$i++)
			{
				if($reqTanggal[$i] == "")
				{}
				else
				{
					$index = $i;
					$paket_work_order = new PaketProgresDetil();
					
					$paket_work_order->setField("TANGGAL", dateToDBCheck($reqTanggal[$index]));
					$paket_work_order->setField("KENDALA", $reqKendala[$index]);			
					$paket_work_order->setField("PROSENTASE", $reqProsentase[$index]);
					$paket_work_order->setField("NAMA", $reqNama[$index]);
					$paket_work_order->setField("USER_LOGIN_ID", ValToNullDB($reqUserLoginId));
					$paket_work_order->setField("KETERANGAN", $reqKeterangan[$index]);
					$paket_work_order->setField("PAKET_PROGRES_ID", ValToNullDB($reqId));
					$paket_work_order->setField("PAKET_PROGRES_DETIL_ID", $reqRowId[$i]);
					$paket_work_order->setField("LAST_CREATE_USER", $userLogin->idUser);
					$paket_work_order->setField("LAST_CREATE_DATE", "CURRENT_DATE");
					
					if($reqRowId[$i] == "")
					{
						if($paket_work_order->insert())
						{
							$id= $paket_work_order->id;
							
							if($_FILES['reqLinkFile']['tmp_name'][$i] == "")
							{
								$insertLinkFile = $reqLinkFileTemp[$i];
							}
							else
							{
								$set= new PaketProgresDetil();
								$renameFile= $id.formatTextToDb($file->getFileNameArray('reqLinkFile',$i));
								
								$varSource=$FILE_DIR.$reqLinkFileTemp[$i];
								if($file->uploadToDirArray('reqLinkFile', $FILE_DIR, $renameFile, $i))
								{
									if($reqLinkFileTemp[$i] == ''){}
									else
									{
										if($file->delete($varSource)){}
									}
									$insertLinkFile= $file->uploadedFileName;
								}
								
								$set->setField("PATH_FILE", $insertLinkFile);
								$set->setField("UKURAN", ValToNullDB($_FILES['reqLinkFile']['size'][$i]));
								$set->setField("TIPE", $_FILES['reqLinkFile']['type'][$i]);
								$set->setField("PAKET_PROGRES_DETIL_ID", $id);
								if($set->updateFormat()){}
								//echo $set->query;
							}
				
						}
						//echo $paket_work_order->query;
					}
					else
					{
						if($paket_work_order->update())
						{
							$id = $reqRowId[$i];
							
							if($_FILES['reqLinkFile']['tmp_name'][$i] == "")
							{
								$insertLinkFile = $reqLinkFileTemp[$i];
							}
							else
							{
								$set= new PaketProgresDetil();
								$renameFile= $id.formatTextToDb($file->getFileNameArray('reqLinkFile',$i));
								
								$varSource=$FILE_DIR.$reqLinkFileTemp[$i];
								//echo $varSource;
								if($file->uploadToDirArray('reqLinkFile', $FILE_DIR, $renameFile, $i))
								{
									if($reqLinkFileTemp[$i] == ''){}
									else
									{
										if($file->delete($varSource)){}
									}
									$insertLinkFile= $file->uploadedFileName;
								}
								
								$set->setField("PATH_FILE", $insertLinkFile);
								$set->setField("UKURAN", ValToNullDB($_FILES['reqLinkFile']['size'][$i]));
								$set->setField("TIPE", $_FILES['reqLinkFile']['type'][$i]);
								$set->setField("PAKET_PROGRES_DETIL_ID", $id);
								if($set->updateFormat()){}
							}
						}
						//echo $paket_work_order->query;
					}
					//echo $paket_work_order->query;
					unset($paket_work_order);
				}
			}
			echo "Data berhasil disimpan.";
		}
	}
	
	function delete_paket_work_order()
	{
		$FILE_DIR = "uploads/progres_paket/";
			
			$this->load->model("PaketProgresDetil");
			$lampiran = new PaketProgresDetil();
			$lampiran->selectByParamsBlob(array("PAKET_PROGRES_DETIL_ID" => $reqId), -1, -1);
			$lampiran->firstRow();
			$tempLinkFileTemp= $lampiran->getField("PATH_FILE");
			
			if($tempLinkFileTemp == ""){}
			else
			{
				$varSource=$FILE_DIR.$tempLinkFileTemp;
				if($file->delete($varSource)){}
			}
			
			$set= new PaketProgresDetil();
			$set->setField('PAKET_PROGRES_DETIL_ID', $reqId);
			//echo $reqId
			if($set->delete())
			{	
				$alertMsg .= "Data berhasil dihapus";
			}
			else
				$alertMsg .= "Error ".$set->getErrorMsg();
			//echo $set->query;
			//echo "asd";
	}
	
}
?>
