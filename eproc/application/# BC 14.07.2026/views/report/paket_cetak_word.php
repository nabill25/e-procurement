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
ob_start();
$this->load->library("kauth");  $userLogin = new kauth();
$this->load->library("paketinfo"); $paketInfo = new paketinfo();
$this->load->model("PaketRekanan");
$this->load->model("PaketDokumen");
$paket_rekanan = new PaketRekanan();
// Unit Kerja
$this->load->library("libbreadcrumb");
$unitkerjaid =  $this->kauth->getInstance()->getIdentity()->UNIT_KERJA_ID;
$unitkerja = $this->libbreadcrumb->unitkerja($unitkerjaid);
// End Unit Kerja
$reqId = $this->input->get("reqId");

$paketInfo->getPaket($reqId);


header("Content-Type: application/vnd.ms-word");
  header("Expires: 0");
  header("Cache-Control:  must-revalidate, post-check=0, pre-check=0");
  header("Content-disposition: attachment; filename=\"PAKET_".$paketInfo->nama."_".$paketInfo->pr_group_number."_".$reqId.date('Ymd').date('His').".doc\"");

$link_report = "paket_cetak2";

$arrContextOptions=array(
    "ssl"=>array(
        "verify_peer"=>false,
        "verify_peer_name"=>false,
    ),
  );

  $output = file_get_contents(SYSTEM_URL_EMAIL."/main/loadUrl/report/".$link_report."?reqId=".$reqId."&unitkerjaid=".$unitkerjaid, false, stream_context_create($arrContextOptions));


  echo $output;
  exit;
?>
