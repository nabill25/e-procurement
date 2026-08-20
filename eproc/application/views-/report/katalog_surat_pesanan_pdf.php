<?php
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 * 
 */
$this->libsession->cekSession();   

ini_set('max_execution_time', 300); //300 seconds = 5 minutes
ini_set('memory_limit','2048M');
include_once("lib/MPDF60/mpdf.php");
$this->load->library("kauth");  $userLogin = new kauth(); 
$this->load->library("paketinfo"); $paketInfo = new paketinfo();
$this->load->model("Paket"); $paket = new Paket();
$this->load->model("Katalogrekanan"); $katalogrekananRow = new Katalogrekanan();

$reqId = $this->input->get("reqId"); 


/* LOGIN CHECK */
if($reqId == "")
    exit;

  header("Content-Type: application/vnd.ms-word");
  header("Expires: 0");
  header("Cache-Control:  must-revalidate, post-check=0, pre-check=0");
  header("Content-disposition: attachment; filename=\"surat_pesanan_".$reqId.date('Ymd').date('His').".doc\"");

  $output = file_get_contents(SYSTEM_URL_EMAIL."/main/loadUrl/report/katalog_surat_pesanan?reqId=".$reqId);

  echo $output;
  exit;
?>
