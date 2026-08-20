<?php
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 * 
 */

ini_set('max_execution_time', 300); //300 seconds = 5 minutes
ini_set('memory_limit','2048M');
include_once("lib/MPDF60/mpdf.php");
ob_start();
$this->load->library("kauth");  $userLogin = new kauth(); 
$this->load->library("paketinfo"); $paketInfo = new paketinfo();
$this->load->model("Paket"); $paket = new Paket();

$this->load->library("libbreadcrumb");
$unitkerjaid =  $this->kauth->getInstance()->getIdentity()->UNIT_KERJA_ID;
$unitkerja = $this->libbreadcrumb->unitkerja($unitkerjaid);

$reqId = $this->input->get("reqPaketId"); 


/* LOGIN CHECK */
if($reqId == "")
    exit;

  header("Content-Type: application/vnd.ms-word");
  header("Expires: 0");
  header("Cache-Control:  must-revalidate, post-check=0, pre-check=0");
  header("Content-disposition: attachment; filename=\"laporan_auction_".$reqId.date('Ymd').date('His').".doc\"");

  $arrContextOptions=array(
    "ssl"=>array(
        "verify_peer"=>false,
        "verify_peer_name"=>false,
    ),
);

  $output = file_get_contents(SYSTEM_URL_EMAIL."/main/loadUrl/report/paket_cetak_auction?reqId=".$reqId."&unitkerjaid=".$unitkerjaid, false, stream_context_create($arrContextOptions));
  echo $output; die;
  ob_end_clean();
  echo $output;
  exit;
?>