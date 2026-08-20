<?php
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 * 
 */

function showMessageDlg($messageStr,$continueLoad=true,$redirectUrl="",$pPage=""){
	if(!$continueLoad){
		echo "<script language='javascript'>";
      	echo "  alert('".$messageStr."');";
      	if(trim($redirectUrl)=="")
        	echo "  history.go(-1);";
      	else
        	echo "  ". $pPage."location.href = '".$redirectUrl."';";
      	echo "</script>";
      exit;
	}else{
      	echo "<script language='javascript'>";
      	echo "  alert('".$messageStr."');";
      	echo "</script>";
    }
}
?>