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


class Cronjobs_getfile_tte extends CI_Controller {

	function __construct() {
		parent::__construct(); $this->db->query("SET TIME ZONE 'Asia/Jakarta'");
		$this->HTTP_USER_AGENT = $_SERVER['HTTP_USER_AGENT'];
		$this->REMOTE_ADDR = $_SERVER['REMOTE_ADDR'];
		$this->REMOTE_HOST = $_SERVER['REMOTE_HOST'];
		$this->REMOTE_PORT = $_SERVER['REMOTE_PORT'];
		$this->HTTP_ACCEPT = $_SERVER['HTTP_ACCEPT'];
		$this->REQUEST_URI = $_SERVER['REQUEST_URI'];
		$this->HTTP_HOST = $_SERVER['HTTP_HOST'];
		$this->SERVER_SOFTWARE = $_SERVER['SERVER_SOFTWARE'];
		$this->REQUEST_METHOD = $_SERVER['REQUEST_METHOD'];
		$this->HTTP_TRY = $_SERVER['HTTP_TRY'];
		$this->title_api = 'eProcurement UI';

		$this->pathFileRUP = 'uploads/api_rup/';
		$this->pathLogs = 'logs/api_rup/';


		$this->load->library('excel'); // Load library PHPExcel
	}

	// http://10.39.28.110/Cronjobs_getfile_tte/getFileTTE
	public function getFileTTE()
	{
		$this->load->model("PermohonanPaketAnalisaFile");
		$this->load->library("libapiui");

		$permohonan_paket_file = new PermohonanPaketAnalisaFile();
		$permohonan_paket_file->selectFileTTE();

		$libapiui = new libapiui();
		if ($permohonan_paket_file->countRow() > 0) { 
			while($permohonan_paket_file->nextRow())
            {
              	$cekEsign = $libapiui->postEsignCekStatus($permohonan_paket_file->getField("ESIGN_ID"),$fileName);
              	echo $fileName.'-'.$cekEsign->data->status.'<br>';
                if ($cekEsign->data->status == 'Selesai') { // Update ke DB
                  $permohonan_paket_fileU = new PermohonanPaketAnalisaFile();
                  $permohonan_paket_fileU->setField('PERMOHONAN_PAKET_ANALISA_FILE_ID', $permohonan_paket_file->getField("PERMOHONAN_PAKET_ANALISA_FILE_ID"));
                  $permohonan_paket_fileU->setField('ESIGN_PATH_FILE', $fileName);
                  $permohonan_paket_fileU->setField('ESIGN_STATUS', $cekEsign->data->status);
                  $permohonan_paket_fileU->setField('UPDATED_BY', $this->USER_LOGIN_ID);
                  $permohonan_paket_fileU->updateEsign400Close();
                  // code...
                }
            }

		}
	}
 	 

	public function testCron()
	{
		$filepath = 'logs/notif/logs_notif_mail_dok_expired_test.txt';
		$handle = fopen($filepath, "a+");

		$text   = "TIME:".date('h:i:s')." ### DATE:".date('Y-m-d')." ### IP:". $this->getIP()."
		-----------------------------------------------------------------------------------------
		";
		$arr = array(' ', '<br>');
		$logtext = str_replace($arr, "", $text);
		fwrite($handle, $logtext . "\r\n");
		fclose($handle);
	}

	public function getIP()
  {
      // $ip = $_SERVER['REMOTE_ADDR']?:($_SERVER['HTTP_X_FORWARDED_FOR']?:$_SERVER['HTTP_CLIENT_IP']);
    $ipaddress = '';
    if (isset($_SERVER['HTTP_CLIENT_IP']))
        $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
    else if(isset($_SERVER['HTTP_X_FORWARDED_FOR']))
        $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
    else if(isset($_SERVER['HTTP_X_FORWARDED']))
        $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
    else if(isset($_SERVER['HTTP_FORWARDED_FOR']))
        $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
    else if(isset($_SERVER['HTTP_FORWARDED']))
        $ipaddress = $_SERVER['HTTP_FORWARDED'];
    else if(isset($_SERVER['REMOTE_ADDR']))
        $ipaddress = $_SERVER['REMOTE_ADDR'];
    else
        $ipaddress = 'UNKNOWN';
      return $ipaddress;
  }

}
