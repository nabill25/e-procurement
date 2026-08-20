<?php
/**
 * @package 	eProcurement Application
 * @author 		eproc2025
 * @since 		25. Version 3.1
 * 
 */
include_once("functions/string.func.php");
include_once("functions/date.func.php");
include_once("functions/default.func.php"); 
include_once("functions/blob.func.php"); 

class Blobfile extends CI_Controller {

	function __construct() {
		parent::__construct(); $this->db->query("SET TIME ZONE 'Asia/Jakarta'");
		$this->db->query("alter session set nls_date_format='YYYY-MM-DD'");
		$this->db->query("alter session set nls_numeric_characters='.,'");
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
		$this->title_api = 'eprocurement PAM JAYA';
	}

	function downloadBlobFile($fileTxt,$filename,$nomorPR)
  { 
  	$filepath = 'uploads/base64/'.$nomorPR.'/'.$fileTxt.'.txt';

  	if (file_exists($filepath)) {
	  	$fh = fopen($filepath,'r');
	  	while(!feof($fh)){ $base64Text = fgets($fh)."<br>";}

	  	$base64_encoded_file_data = $base64Text;
	 
	    $filenameReplace = str_replace('%20',' ',$filename);

	    downloadBase64($filenameReplace, $filepath, $base64_encoded_file_data);
  	} else {
  		echo "File sudah di download, silahkan tutup halaman ini dan buka kembali agar bisa download ulang filenya !";
  	}
  }

}
