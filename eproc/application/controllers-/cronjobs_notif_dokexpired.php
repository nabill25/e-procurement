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


class Cronjobs_notif_dokexpired extends CI_Controller {

	function __construct() {
		// parent::__construct(); $this->db->query("SET TIME ZONE 'Asia/Jakarta'");
		// $this->db->query("alter session set nls_date_format='YYYY-MM-DD'");
		// $this->db->query("alter session set nls_numeric_characters='.,'");
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
		$this->title_api = 'eprocui';
	}
 	
 	// URL Cron Jobs: http://url-buyer/cronjobs_notif_dokexpired/sendMail
 	// Sintak Crontab--Jalan 4xsehari atau 6jam sekali: 
 	// * */6 * * * curl -s "http://10.4.2.161/cronjobs_notif_dokexpired/testCron" > /dev/null 2>&1
 	public function sendMail()
 	{
		$this->load->model("Masterpengaturan");
 		$master_pengaturan = new Masterpengaturan();
	  $master_pengaturan->selectByParams(array('ID' => 1));
	  $master_pengaturan->firstRow();

	  $reqAktif = $master_pengaturan->getField("AKTIF"); 

	  if ($reqAktif == 'y') // Eksekusi Send Mail
	  {
 			$dok_expired = new Masterpengaturan();
	  	$dok_expired->selectByParamsDokExpiredEmail(array(),6,-1);
    	$statement  = '';
			while($dok_expired->nextRow()) {

				$reqRekananId = $dok_expired->getField("REKANAN_ID");
				$reqEmail 		= $dok_expired->getField("EMAIL");
				$reqNama 			= $dok_expired->getField("NAMA");
				$reqKirimKe 	= $dok_expired->getField("KIRIM_KE") + 1;

				// 1. Send Email 
				$reqSendEmail = $this->mailText($reqRekananId,$reqEmail,$reqNama);

				// 2. -------------------- Insert LOGS to DATABASE

				$cek_data_rekanan = new Masterpengaturan();
			  $cek_data_rekanan->selectByParamsCekLog(array('REKANAN_ID' => $dok_expired->getField("REKANAN_ID")));
			  $cek_data_rekanan->firstRow();

	  		$cekId = $cek_data_rekanan->getField("ID"); 
	  		echo $cekId;
	  		if (!$cekId) { // Insert
	  			$action = "INSERT";
					$logs_insert = new Masterpengaturan();
					$logs_insert->setField("REKANAN_ID", $dok_expired->getField("REKANAN_ID"));
					$logs_insert->setField("STAT", "1");
					$logs_insert->setField("KIRIM_KE", $reqKirimKe);
					$logs_insert->setField('CREATED_BY', $this->USER_LOGIN_ID);
					$logs_insert->insertLogs();
					echo "Insert <br>";
	  		} else { // Update
	  			$action = "UPDATE";
	  			$logs_update = new Masterpengaturan();
					$logs_update->setField("REKANAN_ID", $dok_expired->getField("REKANAN_ID"));
					$logs_update->setField("KIRIM_KE", $reqKirimKe);
					$logs_update->setField('CREATED_BY', $this->USER_LOGIN_ID);
					$logs_update->insertLogs();
					$logs_update->updateLogs();
					echo "Update <br>";
	  		}

	  		// 3. -------------------- Insert LOGS to File
      	$filepath = 'logs/notif/logs_notif_mail_dok_expired.txt'; 
      	$handle = fopen($filepath, "a+");

      	$statement .= $dok_expired->getField("NAMA");
				$text   = "ACTION: ".$action." ### SENDMAIL: ".$reqSendEmail." ### TIME:".date('h:i:s')." ### DATE:".date('Y-m-d')." ### IP:". $this->getIP()." ### EXECUTION-TIME:". $times[$key]." ### REKANAN_ID:". $dok_expired->getField("REKANAN_ID")." ### STATEMENT:". $statement."
        -----------------------------------------------------------------------------------------
        ";
      	$arr = array(' ', '<br>');
        $logtext = str_replace($arr, "", $text);
        fwrite($handle, $logtext . "\r\n");              
	      fclose($handle); 
				// -------------------- END Insert LOGS to File

	      echo $text.'<br>';
			}
	  }

 	}

 	function mailText($reqRekananId,$reqEmail,$reqNama)
	{
		// echo $reqRekananId.'____'.$reqEmail.'____'.$reqNama; die;
		$this->load->library("KMail");

		$cbg = str_replace(dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR,'',dirname(__FILE__));
		$mail = new KMail($cbg);
		$mail->Subject  =  'Pemberitahuan Dokumen Expired dari '.SYSTEM_NAME.' '.SYSTEM_NAME_PT;
		$mail->AddAddress($reqEmail, $reqNama);
		$body = file_get_contents(SYSTEM_URL_EMAIL."/main/loadUrl/email/dokumen_expired/".$reqRekananId);
		// echo $body; die;
		$mail->MsgHTML($body);

		if(!$mail->Send())
		{
			return "Mailer Error: " . $mail->ErrorInfo;
		}
		else
		{
			return 'Message has been sent.';
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
