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

class phpshoutbox_json extends CI_Controller {

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
		$this->NIP = $this->kauth->getInstance()->getIdentity()->NIP;  
	}	
	
	function aanwijzing_konfirmasi_json() 
	{
		
		$this->load->library("kauth");  $userLogin = new kauth(); 
		$this->load->model("PhpShoutbox");
		
		$php_shoutbox = new PhpShoutbox();
		
		$reqId = $this->input->get("reqId");
		$reqKode = $this->input->get("reqKode");
		
		$php_shoutbox->selectByParamsKonfirmasi(array("A.PAKET_ID" => $reqId));
		
		$i=0;
		while($php_shoutbox->nextRow())
		{
				
			$met[$i]['KODE'] = $php_shoutbox->getField("KODE");
			$met[$i]['KODE_HALAMAN'] = $php_shoutbox->getField("KODE_HALAMAN");
			$met[$i]['HALAMAN'] = $php_shoutbox->getField("HALAMAN");
			$met[$i]['INFORMASI'] = $php_shoutbox->getField("INFORMASI");
			$i++;
		}
		echo json_encode($met);	
	}

	function aanwijzing_konfirmasi_rekanan_json() 
	{
		$this->load->library("kauth");  $userLogin = new kauth(); 
		$this->load->model("PhpShoutbox");
		$this->load->model("PaketRekanan");
		
		/* create objects */
		$php_shoutbox = new PhpShoutbox();
		$paket_rekanan = new PaketRekanan();

		$reqId = httpFilterGet("reqId");
		$reqKode = httpFilterGet("reqKode");

		$paket_rekanan->selectByParams(array("A.PAKET_ID" => $reqId, "A.REKANAN_ID" => $userLogin->userRekanan));
		$paket_rekanan->firstRow();
		$nickname = $paket_rekanan->getField("KODE_REKANAN");		
		
		/* LOGIN CHECK */
		if ($userLogin->checkUserLogin()) 
		{ 
			$userLogin->retrieveUserInfo();
		}

		$php_shoutbox->selectByParamsKonfirmasiRekanan(array("A.PAKET_ID" => $reqId), -1, -1, $nickname);
		$i=0;
		while($php_shoutbox->nextRow())
		{
				
			$met[$i]['KODE'] = $php_shoutbox->getField("KODE");
			$met[$i]['KODE_HALAMAN'] = $php_shoutbox->getField("KODE_HALAMAN");
			$met[$i]['HALAMAN'] = $php_shoutbox->getField("HALAMAN");
			$met[$i]['INFORMASI'] = $php_shoutbox->getField("INFORMASI");
			$i++;
		}
		echo json_encode($met);	
	}	
	
	function daddy_shoutbox() 
	{
		$this->load->library("kauth");  $userLogin = new kauth(); 
		$this->load->model("PhpShoutbox");
		$this->load->model("Aanwijzing");
		$this->load->model("PaketTahap");
			
		$paket_tahap_metode = new PaketTahap();
		$paket_tahap = new PaketTahap();
		$aanwijzing = new Aanwijzing();
		
		$reqId = httpFilterRequest("reqId");
		$reqHalaman = httpFilterRequest("reqHalaman");
		$reqKode = httpFilterRequest("reqKode");
		$reqToken = httpFilterRequest("reqToken");
		$arrTahapan = array(0, 10, 5, 10, 5, 9, 5, 10, 10);
		
		$jenis_tahap = $paket_tahap_metode->getJenisTahapById($reqId);
		$paket_tahap->selectByParams(array("URUT" => $arrTahapan[$jenis_tahap], "PAKET_ID" => $reqId));
		$paket_tahap->firstRow();
		
		if($paket_tahap->getField("JAM_AKHIR") == "")
			$jam_akhir = "23";
		else
			$jam_akhir = $paket_tahap->getField("JAM_AKHIR");
		
		$limit = strtotime($paket_tahap->getField("TANGGAL_AKHIR")." ".$jam_akhir.":00");
		$time = strtotime(date("Y-m-d H:i:s"));
		
		if($limit >= $time)
			$validasi = 1;
		else
			$validasi = 0;
		
		if($userLogin->userLevel == 3 || $userLogin->userLevel == 9)
			$validasi = 1;
		
		$aanwijzing->selectByParams(array("PAKET_ID" => $reqId));
		$aanwijzing->firstRow();
		$publish = $aanwijzing->getField("PUBLISH");
		
			
		/* LOGIN CHECK */
		if ($userLogin->checkUserLogin()) 
		{ 
			$userLogin->retrieveUserInfo();
		}
		  function replace(&$item, $key) {
			$item = str_replace('|', '-', $item);
		  }
		  
		  if (!function_exists('file_put_contents')) {
				function file_put_contents($fileName, $data) {
					if (is_array($data)) {
						$data = join('', $data);
					}
					$res = @fopen($fileName, 'w+b');
					if ($res) {
						$write = @fwrite($res, $data);
						if($write === false) {
							return false;
						} else {
							return $write;
						}
					}
				}
			}
		  
		  //file_put_contents('debug.txt', print_r($_GET, true));
		  switch($_GET['action']) {
			case 'add':
			  if($validasi == 1)
			  {
				  if($publish == 0)
				  {
					  array_walk($_POST, 'replace');
					  $_POST['nickname'] = htmlentities($_POST['nickname']);
					  $_POST['message'] = htmlentities($_POST['message']);
					  $time = time();
					  $arr[] = $time.'|'.$_POST['nickname'].'|'.$_POST['message'].'|'.$_SERVER['REMOTE_ADDR']."\n";
					  
					  $php_shoutbox = new PhpShoutbox();
					  $php_shoutbox->setField("JAM", $time);
					  $php_shoutbox->setField("NAMA", $_POST['nickname']);
					  $php_shoutbox->setField("PESAN", formatTextToDb($_POST['message']));
					  $php_shoutbox->setField("IP_ADDRESS", $_SERVER['REMOTE_ADDR']);
					  $php_shoutbox->setField("PAKET_ID", $reqId);
					  $php_shoutbox->setField("HALAMAN", $reqHalaman);
					  $php_shoutbox->setField("KODE", $reqKode);
					  $php_shoutbox->insert();
				
					  $data['response'] = 'Good work';
					  $data['nickname'] = $_POST['nickname'];
					  $data['message'] = $_POST['message'];
					  $data['time'] = $time;
				  }
				  else
					$data['response'] = 'closed';  
			  }
			  else
			  {
				  $data['response'] = 'failed';  
				  
			  }
			break;
			
			case 'add_global':
			  if($validasi == 1)
			  {
				  if($publish == 0)
				  {
					  array_walk($_POST, 'replace');
					  $_POST['nickname'] = htmlentities($_POST['nickname']);
					  $_POST['message'] = htmlentities($_POST['message']);
					  $time = time();
					  $arr[] = $time.'|'.$_POST['nickname'].'|'.$_POST['message'].'|'.$_SERVER['REMOTE_ADDR']."\n";
					  
					  $php_shoutbox = new PhpShoutbox();
					  $php_shoutbox->setField("JAM", $time);
					  $php_shoutbox->setField("NAMA", $_POST['nickname']);
					  $php_shoutbox->setField("PESAN", formatTextToDb($_POST['message']));
					  $php_shoutbox->setField("IP_ADDRESS", $_SERVER['REMOTE_ADDR']);
					  $php_shoutbox->setField("PAKET_ID", $reqId);
					  $php_shoutbox->setField("HALAMAN", $reqHalaman);
					  $php_shoutbox->setField("KODE", $reqKode);
					  $php_shoutbox->insert();
				
					  $data['response'] = 'Good work';
					  $data['nickname'] = $_POST['nickname'];
					  $data['message'] = $_POST['message'];
					  $data['time'] = $time;
				  }
				  else
					$data['response'] = 'closed';  
			  }
			  else
			  {
				  $data['response'] = 'failed';  
			  }
			break;
			
			case 'confirm':
			  array_walk($_POST, 'replace');
			  $time = time();
			  
			  $php_shoutbox = new PhpShoutbox();
			  $php_shoutbox->setField("JAM", $time);
			  $php_shoutbox->setField("NAMA", $reqToken);
			  $php_shoutbox->setField("PESAN", "CONFIRMED");
			  $php_shoutbox->setField("IP_ADDRESS", $_SERVER['REMOTE_ADDR']);
			  $php_shoutbox->setField("PAKET_ID", $reqId);
			  $php_shoutbox->setField("HALAMAN", $reqHalaman);
			  $php_shoutbox->setField("KODE", $reqKode);
			  if($php_shoutbox->insert())
				$data['response'] = 'success';
			  else
				$data['response'] = 'failed';
			  
			break;
			 
			case 'view':
			  $data = array();
			  if(!$_GET['time'])
				$_GET['time'] = 0;
			  $php_shoutbox = new PhpShoutbox();
			  $php_shoutbox->selectByParams(array("PAKET_ID" => $reqId, "KODE" => $reqKode));
			  while($php_shoutbox->nextRow())
			  {
				$row = $php_shoutbox->getField("JAM")."|".$php_shoutbox->getField("WAKTU")."|".$php_shoutbox->getField("NAMA")."|".str_replace("\n", "<br>", $php_shoutbox->getField("PESAN"))."|".$php_shoutbox->getField("HALAMAN");  
				list($aTemp['time'], $aTemp['waktu'], $aTemp['nickname'], $aTemp['message'], $aTemp['halaman']) = explode('|', $row); 
				if($aTemp['message'] AND $aTemp['time'] > $_GET['time'])
				  $data[] = $aTemp;
			  }
			break;
		  }
		  
		  require_once('WEB-INF/base-main/JSON.php');
		  $json = new Services_JSON();
		  $out = $json->encode($data);
		  print $out;
	}
}
?>
