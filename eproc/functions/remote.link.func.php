<?php
/**
 * @package 	eProcurement Application
 * @author 		eproc2025
 * @since 		25. Version 3.1
 * 
 */

	$url = $_SERVER['REMOTE_ADDR'].$_SERVER['REQUEST_URI'];
	$arrUrl = explode("main/", $url);
	
	$_SESSION["ssUsr_UrlQ"] = $arrUrl[0];
	//echo $_SESSION["ssUsr_UrlQ"].'--';
?>