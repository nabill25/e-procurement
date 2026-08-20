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

class chat_json extends CI_Controller {

	function __construct() {
		parent::__construct(); $this->db->query("SET TIME ZONE 'Asia/Jakarta'");

		//kauth
		if (!$this->kauth->getInstance()->hasIdentity())
		{
			// trow to unauthenticated page!
			//redirect('Login');
		}

		/* GLOBAL VARIABLE */
		$this->ID = isset($this->kauth->getInstance()->getIdentity()->REKANAN_ID) ? $this->kauth->getInstance()->getIdentity()->REKANAN_ID : '';

	    $this->USER_LOGIN_ID =  isset($this->kauth->getInstance()->getIdentity()->USER_LOGIN_ID) ? $this->kauth->getInstance()->getIdentity()->USER_LOGIN_ID : '';
	    $this->USER_LOGIN =  isset($this->kauth->getInstance()->getIdentity()->USER_LOGIN) ? $this->kauth->getInstance()->getIdentity()->USER_LOGIN : '';
	    $this->USER_NAMA =  isset($this->kauth->getInstance()->getIdentity()->USER_NAMA) ? $this->kauth->getInstance()->getIdentity()->USER_NAMA : '';
	    $this->USER_TYPE_ID =  isset($this->kauth->getInstance()->getIdentity()->USER_TYPE_ID) ? $this->kauth->getInstance()->getIdentity()->USER_TYPE_ID : '';
	    $this->REKANAN_ID =  isset($this->kauth->getInstance()->getIdentity()->REKANAN_ID) ? $this->kauth->getInstance()->getIdentity()->REKANAN_ID : '';
	    $this->UNIT_KERJA_ID =  isset($this->kauth->getInstance()->getIdentity()->UNIT_KERJA_ID) ? $this->kauth->getInstance()->getIdentity()->UNIT_KERJA_ID : '';
	    $this->NIP =  isset($this->kauth->getInstance()->getIdentity()->NIP) ? $this->kauth->getInstance()->getIdentity()->NIP : '';
	    $this->LOGIN_TIME = isset($this->kauth->getInstance()->getIdentity()->LOGIN_TIME) ? $this->kauth->getInstance()->getIdentity()->LOGIN_TIME : '';
	    $this->LOGIN_DATE = isset($this->kauth->getInstance()->getIdentity()->LOGIN_DATE) ? $this->kauth->getInstance()->getIdentity()->LOGIN_DATE : '';
	    $this->REKANAN = isset($this->kauth->getInstance()->getIdentity()->NAMA) ? $this->kauth->getInstance()->getIdentity()->NAMA : '';
	    $this->REKANAN_KODE = isset($this->kauth->getInstance()->getIdentity()->KODE) ? $this->kauth->getInstance()->getIdentity()->KODE : '';
	    $this->REKANAN_PKP = isset($this->kauth->getInstance()->getIdentity()->PKP) ? $this->kauth->getInstance()->getIdentity()->PKP : '';
	    $this->REKANAN_NPWP = isset($this->kauth->getInstance()->getIdentity()->NPWP) ? $this->kauth->getInstance()->getIdentity()->NPWP : '';
	    $this->REKANAN_STATUS_PERUSAHAAN = isset($this->kauth->getInstance()->getIdentity()->STATUS_PERUSAHAAN) ? $this->kauth->getInstance()->getIdentity()->STATUS_PERUSAHAAN : '';
	    $this->REKANAN_STATUS_VALIDASI = isset($this->kauth->getInstance()->getIdentity()->STATUS_VALIDASI) ? $this->kauth->getInstance()->getIdentity()->STATUS_VALIDASI : '';
	}

	function negoshoutbox()
	{
		$this->load->model("ChatShoutbox");
		$chat_shoutbox = new ChatShoutbox();

		$time = time();
		$reqUserId   = $this->USER_LOGIN_ID;
		$pesan	= $_POST['reqPesanNego'];
		$reqId	= $_POST['reqId'];
		$reqJenisChat	= $_POST['reqJenisChat'];
		$reqRekananId	= $_POST['reqRekananId'];

		$chat_shoutbox->setField("JAM", $time);
		$chat_shoutbox->setField("NAMA", $this->USER_NAMA);
		$chat_shoutbox->setField("PESAN", formatTextToDb($pesan));
		$chat_shoutbox->setField("IP_ADDRESS", $_SERVER['REMOTE_ADDR']);
		$chat_shoutbox->setField("PAKET_ID", $reqId);
		$chat_shoutbox->setField("JENIS_CHAT", $reqJenisChat);
		$chat_shoutbox->setField("REKANAN_ID", $reqRekananId );
		$chat_shoutbox->setField("USER_LOGIN_ID", $reqUserId );
		$chat_shoutbox->insert2();
	}

	function negoshoutboxWithFile()
	{
		$this->load->model("ChatShoutbox");
		$chat_shoutbox = new ChatShoutbox();
		$this->load->library("FileHandler"); 
		$file = new FileHandler();

		$time = time();
		$reqUserId   = $this->USER_LOGIN_ID;
		$pesan	= $_POST['reqPesanNego'];
		$reqId	= $_POST['reqId'];
		$reqJenisChat	= $_POST['reqJenisChat'];
		$reqRekananId	= $_POST['reqRekananId'];
		$reqLinkFile 	= $_FILES['reqLinkFile'];

		$FILE_DIR = "uploads/inbox/";

		$renameFile = 'chat_auction_'.md5(date("dmYHis").$reqLinkFile['name'].$this->ID).".".getExtension($reqLinkFile['name']);
		if($file->uploadToDir('reqLinkFile', $FILE_DIR, $renameFile))
		{
			$insertLinkFile =  $renameFile;
		}
		else
		{
			$insertLinkFile =  "";
		}

		$chat_shoutbox->setField("FILE", $insertLinkFile);
		$chat_shoutbox->setField("JAM", $time);
		$chat_shoutbox->setField("NAMA", $this->USER_NAMA);
		$chat_shoutbox->setField("PESAN", formatTextToDb($pesan));
		$chat_shoutbox->setField("IP_ADDRESS", $_SERVER['REMOTE_ADDR']);
		$chat_shoutbox->setField("PAKET_ID", $reqId);
		$chat_shoutbox->setField("JENIS_CHAT", $reqJenisChat);
		$chat_shoutbox->setField("REKANAN_ID", $reqRekananId );
		$chat_shoutbox->setField("USER_LOGIN_ID", $reqUserId );
		$chat_shoutbox->insert3();
	}

	function chatNegoBox()
	{
		$this->load->model("ChatShoutbox");
		$chat_shoutbox = new ChatShoutbox();
		$FILE_DIR = "uploads/inbox/";
		$reqId			= $_GET['reqId'];
		$reqJenisChat	= $_GET['reqJenis'];
		$reqRekananId	= $_GET['reqRekananId'];

		if ($reqRekananId) {
			$chat_shoutbox->selectByParams(array(), -1, -1, " AND A.PAKET_ID = '".$reqId."' AND A.JENIS_CHAT = '".$reqJenisChat."' AND A.REKANAN_ID = ".$reqRekananId."");
			// echo $chat_shoutbox->countRow(); die();
			if($chat_shoutbox->countRow() == 0) {
				$html .= 'Tidak ada pesan';
			} else
			{
				while($chat_shoutbox->nextRow())
				{
					$nama = $chat_shoutbox->getField('NAMA');
					$pesan = $chat_shoutbox->getField('PESAN');
					$waktu = $chat_shoutbox->getField('WAKTU');
					$file = $chat_shoutbox->getField('FILE');
					$html 		.= '<div class="direct-chat-info clearfix" style="margin-top:2px; margin-bottom:-6px">
								    	<span class="direct-chat-name pull-left"><small>'.$nama.'</small></span>
								    </div>';
					if (file_exists($FILE_DIR.$file) && !$file) { $filenya=''; } else {
					$filenya = '<br><a href="'.$FILE_DIR.$file.'" target="_blank">
									<small class="badge badge-warning"><span class="fa fa-download"></span> download file</small>
									</a>';
					}
					$html 		.= '<div class="direct-chat-text">
								        '.$pesan.'
									'.$filenya.'
								    </div>';
					$html 		.= '
								    <div class="direct-chat-info clearfix">
								        <span class="direct-chat-timestamp pull-right" style="font-size:9px">'.$waktu.'</span>
								    </div>';

				}
			}
		}

		require_once('lib/JSON.php');
		$json = new Services_JSON();
		$out = $json->encode($html);
		// return $out;
		print $out;
	}

	function getstatus()
	{
		$this->load->model("Katalogrekanan");
		$katalogrekanan = new Katalogrekanan();
		$reqId	= $_GET['reqId'];

		$katalogrekanan->selectByParams(array(), -1, -1, " AND A.PAKET_ID = '".$reqId."'");
		$katalogrekanan->firstRow();

		$html = $katalogrekanan->getField('STATUS');

		require_once('lib/JSON.php');
		$json = new Services_JSON();
		$out = $json->encode($html);
		// return $out;
		print $out;
	}

	function getNotif($reqId,$reqJenis,$reqRekananId)
	{
		$this->load->model("ChatShoutbox");
		$getNotif = new ChatShoutbox();

		$getNotif->selectByParams(array("IS_READ" => "0", "PAKET_ID" => $reqId, "JENIS_CHAT" => $reqJenis,"REKANAN_ID" => $reqRekananId), -1, -1, " AND USER_LOGIN_ID != ".$this->USER_LOGIN_ID." ");

		$arrJson["countchat"] = $getNotif->countRow();
		$arrJson["id"] = $reqRekananId;

		echo json_encode($arrJson);
	}

	function updateRead()
	{
		$this->load->model("ChatShoutbox");
		$chat_shoutbox = new ChatShoutbox();
		$reqId			= $_GET['reqId'];
		$reqJenisChat	= $_GET['reqJenis'];
		$reqRekananId	= $_GET['reqRekananId'];

		if ($reqRekananId) {
			$chat_shoutbox->setField("PAKET_ID", $reqId);
			$chat_shoutbox->setField("JENIS_CHAT", $reqJenisChat);
			$chat_shoutbox->setField("REKANAN_ID", $reqRekananId);
			if ($chat_shoutbox->updateRead())
				echo "update success";
		 	else
				echo "update failed";
		}
	}

 }
?>
