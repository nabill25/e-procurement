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
include_once("functions/default.func.php");


class aanwijzing_json extends CI_Controller {

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
	
	function aanwijzing_publish_json() 
	{
		
		$this->load->library("kauth");  $userLogin = new kauth(); 
		$this->load->model("Aanwijzing");
		$aanwijzing = new Aanwijzing();
		
		$reqId = httpFilterGet("reqId");
		
		$aanwijzing->selectByParams(array("PAKET_ID" => $reqId));
		$aanwijzing->firstRow();
		$i=0;
		$met[$i]['PUBLISH'] = $aanwijzing->getField("PUBLISH");
		echo json_encode($met);	
	}

	function set_publish_aanwijzing() 
	{
		$this->load->library("KMail");	
		$this->load->library("kauth");  $userLogin = new kauth(); 
		$this->load->library("paketinfo"); $paketInfo = new paketinfo();
		$this->load->model("Aanwijzing");
		$this->load->model("PaketRekanan");
		$aanwijzing = new Aanwijzing();
		$paket_rekanan = new PaketRekanan();
		
		
		$reqId = httpFilterGet("reqId");
		
		$paketInfo->getPaket($reqId);
		
		/* LOGIN CHECK */
		/*if ($userLogin->checkUserLogin()) 
		{ 
			$userLogin->retrieveUserInfo();
			if($userLogin->userLevel == 6)
			{
				echo '<script language="javascript">';
				echo 'alert("Anda tidak berhak mengakses halaman ini. IP address anda telah kami catat sebagai rekanan yang mencoba membuka halaman administrator.");';
				echo 'top.location.href = "index.php";';
				echo '</script>';
				exit;		
			}
		}*/
		
		$aanwijzing->setField("FIELD", "PUBLISH");
		$aanwijzing->setField("FIELD_VALUE", "1");
		$aanwijzing->setField("PAKET_ID", $reqId);
		$aanwijzing->updateByField();
		
		/* EMAIL KE PESERTA */
		$paket_rekanan->selectByParamsEmail(array("A.PAKET_ID" => $reqId, "AANWIJZING" => "1"));

		if($paketInfo->bahasa == "EN")
			$link_email = "aanwijzing_publish_en";
		else
			$link_email = "aanwijzing_publish";
							
		while($paket_rekanan->nextRow())
		{
			$mail = new KMail();
			$mail->AddAddress($paket_rekanan->getField("EMAIL") , $paket_rekanan->getField("NAMA"));
			$mail->Subject  =  "Pemberitahuan Publish Berita Acara Aanwijzing";
			$body = file_get_contents(base_url()."main/loadUrl/email/".$link_email."/".$reqId."/".$paket_rekanan->getField("REKANAN_ID"));
			$mail->MsgHTML($body);
			$mail->Send();

			unset($mail);
			unset($body);
		}
		
		$met = array();
		$i=0;
		
		$met[0]['STATUS'] = 1;
		echo "Publish aanwijzing dan kirim email berhasil.";
	}
	
	function aanwijzing_pra() 
	{
		$this->load->library("kauth");  $userLogin = new kauth(); 
		$this->load->model("Aanwijzing");
		$this->load->library("FileHandler");
		
		$aanwijzing = new Aanwijzing();
		$aanwijzing_pasal = new Aanwijzing();
		
		$reqId = $this->input->post("reqId");
		$reqNama = $this->input->post("reqNama");
		$submitSimpan = $this->input->post("submitSimpan");
		$reqPageCount = $_POST["reqPageCount"];
		$reqLinkFileTemp = $_POST["reqLinkFileTemp"];
		$reqKeterangan = $_POST["reqKeterangan"];
		$reqLinkFile = isset($_FILE["reqLinkFile"])?$_FILE["reqLinkFile"]:'';
		
		$FILE_DIR = "uploads/aanwijzing/";
			
		if($submitSimpan == "Pasal")
		{
			$validasi = 1;
			for($i=0;$i<count($reqLinkFileTemp);$i++)
			{
				$file = new FileHandler();
				$filename = formatTextToDb($file->getFileNameArray('reqLinkFile', $i));
				if($file->getFileExtension($filename) == "zip")
				{}
				else
				{
					if($file->getFileExtension($filename) == "")
					{}
					else
						$validasi = 0;	
				}
				unset($file);
			}
			
			if($validasi == 1)
			{
				$aanwijzing->setField("PAKET_ID", $reqId);	
				$aanwijzing->deleteParentChild();
				for($i=0;$i<count($reqLinkFileTemp);$i++)
				{
					
					$file = new FileHandler();
					$file_split = new FileHandler();
					$aanwijzing_insert = new Aanwijzing();
					$cek = formatTextToDb($file->getFileNameArray('reqLinkFile', $i));
					if($cek == "")
					{
						$insertLink = $reqLinkFileTemp[$i];			
						// $pageCount = $reqPageCount[$i];
						$pageCount = $this->input->post("subPageCount")[$i];
					}
					else
					{
						$renameFile = formatTextToDb($file->getFileNameArray('reqLinkFile', $i));
						$renameFile = str_replace(" ", "", $renameFile);
						if($file->uploadToDirArray('reqLinkFile', $FILE_DIR, $renameFile, $i))
						{
							$insertLink = $renameFile;	
							$pageCountOld = $file_split->zip_flatten($FILE_DIR.$renameFile, $FILE_DIR);
							$pageCount = $this->input->post("subPageCount")[$i];
						}
					}
					// echo $pageCount; die();
					
					$aanwijzing_insert->setField("AANWIJZING_ID", "0");
					$aanwijzing_insert->setField("PAKET_ID", $reqId);	
					$aanwijzing_insert->setField("AANWIJZING_PARENT_ID", "0");	
					$aanwijzing_insert->setField("KODE", ($i+1));	
					$aanwijzing_insert->setField("NAMA", $reqNama);	
					$aanwijzing_insert->setField("KETERANGAN", $reqKeterangan[$i]);	
					$aanwijzing_insert->setField("FILE_UPLOAD", $insertLink);	
					$aanwijzing_insert->setField("FILE_COUNT", $pageCount);	
					$aanwijzing_insert->insert();
			
					unset($file);	
					unset($file_split);
					unset($aanwijzing_insert);	
				}
				
				echo "Data berhasi di simpan";
			}
			else
			{
				echo "Hanya file zip yang diijinkan.";				
			}
				
		}
	}

	
	function delete()
	{
		/* INCLUDE FILE */
		$this->load->model("Aanwijzing");
		$aanwijzing = new Aanwijzing();
		
		/* VARIABLE */
		$reqId				= $this->input->get("reqId");
		
		$aanwijzing->setField("AANWIJZING_ID", $reqId);
		
		if($aanwijzing->delete())
		{
			echo 'Data berhasil dihapus.';	
		} 
		else 
		{
			echo 'Data gagal dihapus.';	
		}	
		
	}
	
		
}
?>
