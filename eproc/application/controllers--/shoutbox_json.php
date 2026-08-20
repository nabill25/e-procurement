<?php
include_once("functions/default.func.php");

defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * @package 	eProcurement Application
 * @author 		eproc2025
 * @since 		25. Version 3.1
 * 
 */

class shoutbox_json extends CI_Controller {

	function __construct() {
		parent::__construct(); $this->db->query("SET TIME ZONE 'Asia/Jakarta'"); 

		if (!$this->kauth->getInstance()->hasIdentity())
		{
			redirect('main');
		}		
		$this->db->query("alter session set nls_date_format='YYYY-MM-DD'"); 	
		$this->db->query("alter session set nls_numeric_characters='.,'");   
		
		$this->ID = isset($this->kauth->getInstance()->getIdentity()->REKANAN_ID)?$this->kauth->getInstance()->getIdentity()->REKANAN_ID:'';   
		
		$this->USER_LOGIN_ID =  isset($this->kauth->getInstance()->getIdentity()->USER_LOGIN_ID)?$this->kauth->getInstance()->getIdentity()->USER_LOGIN_ID:'';
		$this->USER_LOGIN =  isset($this->kauth->getInstance()->getIdentity()->USER_LOGIN)?$this->kauth->getInstance()->getIdentity()->USER_LOGIN:'';
		$this->USER_NAMA =  isset($this->kauth->getInstance()->getIdentity()->USER_NAMA)?$this->kauth->getInstance()->getIdentity()->USER_NAMA:'';
		$this->USER_TYPE_ID =  isset($this->kauth->getInstance()->getIdentity()->USER_TYPE_ID)?$this->kauth->getInstance()->getIdentity()->USER_TYPE_ID:'';
		$this->REKANAN_ID =  isset($this->kauth->getInstance()->getIdentity()->REKANAN_ID)?$this->kauth->getInstance()->getIdentity()->REKANAN_ID:'';
		$this->UNIT_KERJA_ID =  isset($this->kauth->getInstance()->getIdentity()->UNIT_KERJA_ID)?$this->kauth->getInstance()->getIdentity()->UNIT_KERJA_ID:'';
		$this->NIP =  isset($this->kauth->getInstance()->getIdentity()->NIP)?$this->kauth->getInstance()->getIdentity()->NIP:'';
		$this->LOGIN_TIME = isset($this->kauth->getInstance()->getIdentity()->LOGIN_TIME)?$this->kauth->getInstance()->getIdentity()->LOGIN_TIME:'';
		$this->LOGIN_DATE = isset($this->kauth->getInstance()->getIdentity()->LOGIN_DATE)?$this->kauth->getInstance()->getIdentity()->LOGIN_DATE:'';
		$this->REKANAN = isset($this->kauth->getInstance()->getIdentity()->REKANAN)?$this->kauth->getInstance()->getIdentity()->REKANAN:'';
		$this->REKANAN_KODE = isset($this->kauth->getInstance()->getIdentity()->REKANAN_KODE)?$this->kauth->getInstance()->getIdentity()->REKANAN_KODE:'';
		$this->REKANAN_PKP = isset($this->kauth->getInstance()->getIdentity()->REKANAN_PKP)?$this->kauth->getInstance()->getIdentity()->REKANAN_PKP:'';
		$this->REKANAN_NPWP = isset($this->kauth->getInstance()->getIdentity()->REKANAN_NPWP)?$this->kauth->getInstance()->getIdentity()->REKANAN_NPWP:'';
		$this->REKANAN_STATUS_PERUSAHAAN = isset($this->kauth->getInstance()->getIdentity()->STATUS_PERUSAHAAN)?$this->kauth->getInstance()->getIdentity()->STATUS_PERUSAHAAN:'';
		$this->REKANAN_STATUS_VALIDASI = isset($this->kauth->getInstance()->getIdentity()->STATUS_VALIDASI)?$this->kauth->getInstance()->getIdentity()->STATUS_VALIDASI:'';
				
	}

	function json() 
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
		$arrTahapan = array(0, 10, 5, 10, 5, 9, 5, 10, 10, 0, 0, 0, 0);
		
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
		
		if($this->USER_TYPE_ID == 3 || $this->USER_TYPE_ID == 9)
			$validasi = 1;
		
		$aanwijzing->selectByParams(array("PAKET_ID" => $reqId));
		$aanwijzing->firstRow();
		$publish = $aanwijzing->getField("PUBLISH");
		
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
		  		// echo $validasi; die();
					  // array_walk($_POST, 'replace');
					  // $_POST['nickname'] = htmlentities($_POST['nickname']);
					  // $_POST['message'] = htmlentities($_POST['message']);
					  // $time = time();
					  // $arr[] = $time.'|'.$_POST['nickname'].'|'.$_POST['message'].'|'.$_SERVER['REMOTE_ADDR']."\n";
					  // echo "<pre>"; print_r($arr[]); die();
				  	  // $nip = isset($this->NIP) ? $this->NIP : '0';
					  $php_shoutbox = new PhpShoutbox();
					  $php_shoutbox->setField("JAM", $time);
					  $php_shoutbox->setField("NAMA", $_POST['nickname']);
					  $php_shoutbox->setField("PESAN", formatTextToDb($_POST['message']));
					  $php_shoutbox->setField("IP_ADDRESS", $_SERVER['REMOTE_ADDR']);
					  $php_shoutbox->setField("PAKET_ID", $reqId);
					  $php_shoutbox->setField("HALAMAN", $reqHalaman);
					  $php_shoutbox->setField("KODE", $reqKode);
					  if($this->USER_TYPE_ID == 3 || $this->USER_TYPE_ID == 9) {
					  $php_shoutbox->setField("NIP", $this->NIP);
					  }

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
			  // print_r($data['response']); die();
			break;
			
			case 'add_global':
			  if($validasi == 1)
			  {
				  if($publish == 0)
				  {
					  // array_walk($_POST, 'replace');
					  // $_POST['nickname'] = htmlentities($_POST['nickname']);
					  // $_POST['message'] = htmlentities($_POST['message']);
					  // $time = time();
					  // $arr[] = $time.'|'.$_POST['nickname'].'|'.$_POST['message'].'|'.$_SERVER['REMOTE_ADDR']."\n";
					  
					  $php_shoutbox = new PhpShoutbox();
					  $php_shoutbox->setField("JAM", $time);
					  $php_shoutbox->setField("NAMA", $_POST['nickname']);
					  $php_shoutbox->setField("PESAN", formatTextToDb($_POST['message']));
					  $php_shoutbox->setField("IP_ADDRESS", $_SERVER['REMOTE_ADDR']);
					  $php_shoutbox->setField("PAKET_ID", $reqId);
					  $php_shoutbox->setField("HALAMAN", $reqHalaman);
					  $php_shoutbox->setField("KODE", $reqKode);
					  if($this->USER_TYPE_ID == 3 || $this->USER_TYPE_ID == 9) {
					  $php_shoutbox->setField("NIP", $this->NIP);
					  }
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
			  if($this->USER_TYPE_ID == 3 || $this->USER_TYPE_ID == 9) {
			  	$php_shoutbox->setField("NIP", $this->NIP);
			  }
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
		  
		  require_once('lib/JSON.php');
		  $json = new Services_JSON();
		  $out = $json->encode($data);
		  print $out;
	}
}
?>