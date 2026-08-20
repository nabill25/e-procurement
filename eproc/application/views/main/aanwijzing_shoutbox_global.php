<?php
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 * 
 */

$this->load->library("kauth");  $userLogin = new kauth(); 
$this->load->model("PhpShoutbox");
include_once("functions/default.func.php");

/* LOGIN CHECK */
if($_POST)
{
	//connect to mysql db
	
	//check if its an ajax request, exit if not
    if(!isset($_SERVER['HTTP_X_REQUESTED_WITH']) AND strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) != 'xmlhttprequest') {
        die();
    } 
	
	if(isset($_POST["message"]) &&  strlen($_POST["message"])>0)
	{
		//sanitize user name and message received from chat box
		//You can replace username with registerd username, if only registered users are allowed.
		$username = filter_var(trim($_POST["username"]),FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH);
		$message = filter_var(trim($_POST["message"]),FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH);
		$user_ip = $_SERVER['REMOTE_ADDR'];
		
		//insert new message in db
		$php_shoutbox = new PhpShoutbox();
		$php_shoutbox->setField("JAM", $time);
		$php_shoutbox->setField("NAMA", $username);
		$php_shoutbox->setField("PESAN", $message);
		$php_shoutbox->setField("IP_ADDRESS", $user_ip);
		$php_shoutbox->setField("PAKET_ID", $_POST["paketid"]);
		$php_shoutbox->setField("HALAMAN", "0");
		$php_shoutbox->setField("KODE", "0");
		if($php_shoutbox->insert())
		{
			$msg_time = date('h:i A M d',time()); // current time
			echo '<div class="shout_msg"><time>'.$msg_time.'</time><span class="username">'.$username.'</span><span class="message">'.$message.'</span></div>';
		}
		
	}
	elseif($_POST["fetch"]==1)
	{
	  $php_shoutbox = new PhpShoutbox();
	  $php_shoutbox->selectByParams(array("PAKET_ID" => $_POST["paketid"], "KODE" => "0"), -1, -1, "", " ORDER BY WAKTU ASC ");
	  while($php_shoutbox->nextRow())
	  {
			$msg_time = $php_shoutbox->getField("WAKTU"); //message posted time
			echo '<div class="shout_msg"><time>'.$msg_time.'</time><span class="username">'.$php_shoutbox->getField("NAMA").'</span> <span class="message">'.$php_shoutbox->getField("PESAN").'</span></div>';
	  }
	}
	else
	{
		header('HTTP/1.1 500 Error?');
    	exit();
	}
}