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

class inbox_rfi_json extends CI_Controller {

	function __construct() {
		parent::__construct(); $this->db->query("SET TIME ZONE 'Asia/Jakarta'");

		$this->USER_LOGIN_ID =  isset($this->kauth->getInstance()->getIdentity()->USER_LOGIN_ID) ? $this->kauth->getInstance()->getIdentity()->USER_LOGIN_ID : '';
		$this->REKANAN_ID =  isset($this->kauth->getInstance()->getIdentity()->REKANAN_ID) ? $this->kauth->getInstance()->getIdentity()->REKANAN_ID : '';
		$this->HTTP_USER_AGENT = $_SERVER['HTTP_USER_AGENT'];
		$this->REMOTE_ADDR = $_SERVER['REMOTE_ADDR'];

	}

	function add()
	{
		$this->load->library("kauth");  $userLogin = new kauth();
		$this->load->model("Inbox");
		$this->load->library("crfs_protect"); $csrf = new crfs_protect();
		$this->load->library("FileHandler");
		$file = new FileHandler();
		$inboxInput = new Inbox();
		$reqSubject 		= $this->input->post("reqSubject");
		$reqTo 				= $this->input->post("reqTo");
		$reqFrom 			= $this->input->post("reqFrom");
		$reqUraian 			= str_replace("'","''",$_POST["reqUraianKegiatan"]);
		$reqInboxcategory 	= $this->input->post("reqInboxcategory");
		$reqLinkFile		= $_FILES['reqLinkFile'];
		$today 				= date('Y-m-d H:i:s');

		foreach ($reqTo as $key => $value) {
			if ((count($reqTo)-1) > $key) {
				$reqToValue .= $value.',';
			} else {
				$reqToValue .= $value;
			}
		}
		// echo $reqToValue; die();
		$inboxInput->setField("INBOXCATEGORYID", $reqInboxcategory);
		$inboxInput->setField("INBOX_SUBJECT", $reqSubject);
		$inboxInput->setField("INBOX_CONTENT", $reqUraian);
		$inboxInput->setField("INBOX_TO", $reqToValue);
		$inboxInput->setField("INBOX_FROM", $reqFrom);
		$inboxInput->setField("STATUS", 0);
		$inboxInput->setField("PARENT", 0);
		$inboxInput->setField("BROWSER", $this->HTTP_USER_AGENT);
		$inboxInput->setField("IP", $this->REMOTE_ADDR);
		$inboxInput->setField("CREATED_BY", $this->USER_LOGIN_ID);
		$inboxInput->setField("CREATED_DATE", $today);

		$FILE_DIR = "uploads/inbox/";
		$renameFile = $reqInboxcategory.'-'.md5(date("dmYHis").$reqLinkFile['name']).".".getExtension($reqLinkFile['name']);
		if($file->uploadToDir('reqLinkFile', $FILE_DIR, $renameFile))
		{
			$insertLinkFile =  $renameFile;
			$insertLinkFileNama = $reqLinkFile['name'];
			$insertLinkFileType = $reqLinkFile['type'];
			$insertLinkFileSize = $reqLinkFile['size'];
		}
		else
		{
			$insertLinkFile =  '-';
			$insertLinkFileNama = '-';
			$insertLinkFileType = '-';
			$insertLinkFileSize = '-';
		}
		/* END UPLOAD FILE */
		$inboxInput->setField("INBOX_FILE", $insertLinkFile);
		$inboxInput->setField("INBOX_FILE_NAMA", $insertLinkFileNama);
		$inboxInput->setField("INBOX_FILE_TYPE", $insertLinkFileType);
		$inboxInput->setField("INBOX_FILE_SIZE", $insertLinkFileSize);

		if ($inboxInput->insert()) {
			echo "Data Berhasil dikirim";
		} else {
			echo "Data Gagal dikirim";
		}

	}

	function addcomplain()
	{
		$this->load->library("kauth");  $userLogin = new kauth();
		$this->load->model("Inbox");
		$this->load->library("crfs_protect"); $csrf = new crfs_protect();
		$this->load->library("FileHandler");
		$file = new FileHandler();
		$inboxInput = new Inbox();
		$reqId 				= $this->input->post("reqId");
		$reqSubject 		= $this->input->post("reqSubject");
		$reqTo 				= $this->input->post("reqTo");
		$reqFrom 			= $this->input->post("reqFrom");
		$reqUraian 			= str_replace("'","''",$_POST["reqUraianKegiatan"]);
		$reqInboxcategory 	= $this->input->post("reqInboxcategory");
		$reqLinkFile		= $_FILES['reqLinkFile'];
		$today 				= date('Y-m-d H:i:s');

		$inboxInput->setField("INBOXCATEGORYID", $reqInboxcategory);
		$inboxInput->setField("INBOX_SUBJECT", $reqSubject);
		$inboxInput->setField("INBOX_CONTENT", $reqUraian);
		$inboxInput->setField("INBOX_TO", $reqTo);
		$inboxInput->setField("INBOX_FROM", $reqFrom);
		$inboxInput->setField("STATUS", 0);
		$inboxInput->setField("PARENT", $reqId);
		$inboxInput->setField("BROWSER", $this->HTTP_USER_AGENT);
		$inboxInput->setField("IP", $this->REMOTE_ADDR);
		$inboxInput->setField("CREATED_BY", $this->REKANAN_ID);
		$inboxInput->setField("CREATED_DATE", $today);

		$FILE_DIR = "uploads/inbox/";
		$renameFile = $reqInboxcategory.'-'.md5(date("dmYHis").$reqLinkFile['name']).".".getExtension($reqLinkFile['name']);
		if($file->uploadToDir('reqLinkFile', $FILE_DIR, $renameFile))
		{
			$insertLinkFile =  $renameFile;
			$insertLinkFileNama = $reqLinkFile['name'];
			$insertLinkFileType = $reqLinkFile['type'];
			$insertLinkFileSize = $reqLinkFile['size'];
		}
		else
		{
			$insertLinkFile =  '-';
			$insertLinkFileNama = '-';
			$insertLinkFileType = '-';
			$insertLinkFileSize = '-';
		}
		/* END UPLOAD FILE */
		$inboxInput->setField("INBOX_FILE", $insertLinkFile);
		$inboxInput->setField("INBOX_FILE_NAMA", $insertLinkFileNama);
		$inboxInput->setField("INBOX_FILE_TYPE", $insertLinkFileType);
		$inboxInput->setField("INBOX_FILE_SIZE", $insertLinkFileSize);

		if ($inboxInput->insert()) {
			echo "Data Berhasil dikirim";
		} else {
			echo "Data Gagal dikirim";
		}

	}

	function replay()
	{
		$this->load->library("kauth");  $userLogin = new kauth();
		$this->load->model("Inbox");
		$this->load->library("crfs_protect"); $csrf = new crfs_protect();
		$this->load->library("FileHandler");
		$file = new FileHandler();
		$inboxInput = new Inbox();
		$reqId 				= $this->input->post("reqId");
		$reqSubject 		= $this->input->post("reqSubject");
		$reqTo 				= $this->input->post("reqTo");
		$reqFrom 			= $this->input->post("reqFrom");
		$reqUraian 			= str_replace("'","''",$_POST["reqUraianKegiatan"]);
		$reqInboxcategory 	= $this->input->post("reqInboxcategory");
		$reqLinkFile		= $_FILES['reqLinkFile'];
		$today 				= date('Y-m-d H:i:s');
		// echo "<pre>"; print_r($this->input->post()); die();
		// foreach ($reqTo as $key => $value) {
		// 	if ((count($reqTo)-1) > $key) {
		// 		$reqToValue .= $value.',';
		// 	} else {
		// 		$reqToValue .= $value;
		// 	}
		// }
		// echo $reqToValue; die();
		$inboxInput->setField("INBOXCATEGORYID", $reqInboxcategory);
		$inboxInput->setField("INBOX_SUBJECT", $reqSubject);
		$inboxInput->setField("INBOX_CONTENT", $reqUraian);
		$inboxInput->setField("INBOX_TO", $reqTo);
		$inboxInput->setField("INBOX_FROM", $reqFrom);
		$inboxInput->setField("STATUS", 0);
		$inboxInput->setField("PARENT", $reqId);
		$inboxInput->setField("BROWSER", $this->HTTP_USER_AGENT);
		$inboxInput->setField("IP", $this->REMOTE_ADDR);
		$inboxInput->setField("CREATED_BY", $this->REKANAN_ID);
		$inboxInput->setField("CREATED_DATE", $today);

		$FILE_DIR = "uploads/inbox/";
		$renameFile = $reqInboxcategory.'-'.md5(date("dmYHis").$reqLinkFile['name']).".".getExtension($reqLinkFile['name']);
		if($file->uploadToDir('reqLinkFile', $FILE_DIR, $renameFile))
		{
			$insertLinkFile =  $renameFile;
			$insertLinkFileNama = $reqLinkFile['name'];
			$insertLinkFileType = $reqLinkFile['type'];
			$insertLinkFileSize = $reqLinkFile['size'];
		}
		else
		{
			$insertLinkFile =  '-';
			$insertLinkFileNama = '-';
			$insertLinkFileType = '-';
			$insertLinkFileSize = '-';
		}
		/* END UPLOAD FILE */
		$inboxInput->setField("INBOX_FILE", $insertLinkFile);
		$inboxInput->setField("INBOX_FILE_NAMA", $insertLinkFileNama);
		$inboxInput->setField("INBOX_FILE_TYPE", $insertLinkFileType);
		$inboxInput->setField("INBOX_FILE_SIZE", $insertLinkFileSize);

		if ($inboxInput->insert()) {
			echo "Data Berhasil dikirim";
		} else {
			echo "Data Gagal dikirim";
		}

	}

	function replaycomplain()
	{
		$this->load->library("kauth");  $userLogin = new kauth();
		$this->load->model("Inbox");
		$this->load->library("crfs_protect"); $csrf = new crfs_protect();
		$this->load->library("FileHandler");
		$file = new FileHandler();
		$inboxInput = new Inbox();
		$reqId 				= $this->input->post("reqId");
		$reqSubject 		= $this->input->post("reqSubject");
		$reqTo 				= $this->input->post("reqTo");
		$reqFrom 			= $this->input->post("reqFrom");
		$reqUraian 			= str_replace("'","''",$_POST["reqUraianKegiatan"]);
		$reqInboxcategory 	= $this->input->post("reqInboxcategory");
		$reqLinkFile		= $_FILES['reqLinkFile'];
		$today 				= date('Y-m-d H:i:s');
		// echo "<pre>"; print_r($this->input->post()); die();
		// foreach ($reqTo as $key => $value) {
		// 	if ((count($reqTo)-1) > $key) {
		// 		$reqToValue .= $value.',';
		// 	} else {
		// 		$reqToValue .= $value;
		// 	}
		// }
		// echo $reqToValue; die();
		$inboxInput->setField("INBOXCATEGORYID", $reqInboxcategory);
		$inboxInput->setField("INBOX_SUBJECT", $reqSubject);
		$inboxInput->setField("INBOX_CONTENT", $reqUraian);
		$inboxInput->setField("INBOX_TO", $reqTo);
		$inboxInput->setField("INBOX_FROM", $reqFrom);
		$inboxInput->setField("STATUS", 0);
		$inboxInput->setField("PARENT", $reqId);
		$inboxInput->setField("BROWSER", $this->HTTP_USER_AGENT);
		$inboxInput->setField("IP", $this->REMOTE_ADDR);
		$inboxInput->setField("CREATED_BY", $this->USER_LOGIN_ID);
		$inboxInput->setField("CREATED_DATE", $today);

		$FILE_DIR = "uploads/inbox/";
		$renameFile = $reqInboxcategory.'-'.md5(date("dmYHis").$reqLinkFile['name']).".".getExtension($reqLinkFile['name']);
		if($file->uploadToDir('reqLinkFile', $FILE_DIR, $renameFile))
		{
			$insertLinkFile =  $renameFile;
			$insertLinkFileNama = $reqLinkFile['name'];
			$insertLinkFileType = $reqLinkFile['type'];
			$insertLinkFileSize = $reqLinkFile['size'];
		}
		else
		{
			$insertLinkFile =  '-';
			$insertLinkFileNama = '-';
			$insertLinkFileType = '-';
			$insertLinkFileSize = '-';
		}
		/* END UPLOAD FILE */
		$inboxInput->setField("INBOX_FILE", $insertLinkFile);
		$inboxInput->setField("INBOX_FILE_NAMA", $insertLinkFileNama);
		$inboxInput->setField("INBOX_FILE_TYPE", $insertLinkFileType);
		$inboxInput->setField("INBOX_FILE_SIZE", $insertLinkFileSize);

		if ($inboxInput->insert()) {
			echo "Data Berhasil dikirim";
		} else {
			echo "Data Gagal dikirim";
		}

	}

}
?>
