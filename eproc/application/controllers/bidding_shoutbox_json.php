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

class bidding_shoutbox_json extends CI_Controller {

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
		
		$this->USER_LOGIN_ID =  $this->kauth->getInstance()->getIdentity()->USER_LOGIN_ID;
		$this->USER_LOGIN =  $this->kauth->getInstance()->getIdentity()->USER_LOGIN;
		$this->USER_NAMA =  $this->kauth->getInstance()->getIdentity()->USER_NAMA;
		$this->USER_TYPE_ID =  $this->kauth->getInstance()->getIdentity()->USER_TYPE_ID;
		$this->REKANAN_ID =  $this->kauth->getInstance()->getIdentity()->REKANAN_ID;
		$this->UNIT_KERJA_ID =  $this->kauth->getInstance()->getIdentity()->UNIT_KERJA_ID;
		$this->NIP =  $this->kauth->getInstance()->getIdentity()->NIP;
		$this->LOGIN_TIME = $this->kauth->getInstance()->getIdentity()->LOGIN_TIME;
		$this->LOGIN_DATE = $this->kauth->getInstance()->getIdentity()->LOGIN_DATE;
		$this->REKANAN = $this->kauth->getInstance()->getIdentity()->REKANAN;
		$this->REKANAN_KODE = $this->kauth->getInstance()->getIdentity()->REKANAN_KODE;
		$this->REKANAN_PKP = $this->kauth->getInstance()->getIdentity()->REKANAN_PKP;
		$this->REKANAN_NPWP = $this->kauth->getInstance()->getIdentity()->REKANAN_NPWP;
		$this->REKANAN_STATUS_PERUSAHAAN = $this->kauth->getInstance()->getIdentity()->STATUS_PERUSAHAAN;
		$this->REKANAN_STATUS_VALIDASI = $this->kauth->getInstance()->getIdentity()->STATUS_VALIDASI;   
	}	
	
	function bidding_shoutbox() 
	{
		$this->load->library("paketinfo"); $paketInfo = new paketinfo();
		$this->load->model("BiddingShoutbox");
		
		$reqId = $_REQUEST["reqId"];
		$reqHalaman = $this->input->post("reqHalaman");
		$reqKode = $this->input->post("reqKode");
		//echo $reqId;exit;
		/* validasi */
		
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
				array_walk($_POST, 'replace');
				$_POST['nickname'] = htmlentities($_POST['nickname']);
				$_POST['message'] = htmlentities($_POST['message']);
				$reqId = $this->input->post("reqId");
				$time = time();
				$arr[] = $time.'|'.$_POST['nickname'].'|'.$_POST['message'].'|'.$_SERVER['REMOTE_ADDR']."\n";
				//echo $reqId;exit;
				$bidding_shoutbox = new BiddingShoutbox();
				$bidding_shoutbox->setField("JAM", $time);
				$bidding_shoutbox->setField("NAMA", $_POST['nickname']);
				// $bidding_shoutbox->setField("PESAN", formatTextToDb(strtoupper($_POST['message'])));
				$bidding_shoutbox->setField("PESAN", formatTextToDb($_POST['message']));
				$bidding_shoutbox->setField("IP_ADDRESS", $_SERVER['REMOTE_ADDR']);
				$bidding_shoutbox->setField("PAKET_ID", $reqId);
				$bidding_shoutbox->setField("REKANAN_ID", ValToNullDB($this->ID));
				$bidding_shoutbox->setField("HALAMAN", $reqHalaman);
				$bidding_shoutbox->setField("KODE", $reqKode);
				$bidding_shoutbox->insert();
		  
				$data['response'] = 'Good work';
				$data['nickname'] = $_POST['nickname'];
				$data['message'] = $_POST['message'];
				$data['time'] = $time;
			break;
			
			case 'view':
			  $data = array();
			  if(!$_GET['time'])
				$_GET['time'] = 0;
			  $bidding_shoutbox = new BiddingShoutbox();
			  $bidding_shoutbox->selectByParams(array("PAKET_ID" => $reqId));
			  
			  while($bidding_shoutbox->nextRow())
			  {
				$row = $bidding_shoutbox->getField("JAM")."|".$bidding_shoutbox->getField("WAKTU")."|".$bidding_shoutbox->getField("NAMA")."|".$bidding_shoutbox->getField("PESAN")."|".$bidding_shoutbox->getField("HALAMAN");  
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
