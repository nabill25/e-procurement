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
$this->load->model("PaketRekanan");
$this->load->model("Aanwijzing");
$paket_rekanan = new PaketRekanan();

$reqId = $this->input->get("reqId");


$paketInfo->getPaket($reqId);
/* LOGIN CHECK */
if($this->USER_TYPE_ID == '')
	exit;

/* LOGIN CHECK */
$validasi_user = false;
if($reqToken == "")
{
	if($this->USER_TYPE_ID == 6)
	{
		$reqToken = $this->ID;
		$validasi_user = true;
	}
}
else
	$validasi_user = true;

/* KHUSUS DARI REKANAN DAN EMAIL */
if($validasi_user == true)
{ 
	$paket_rekanan->selectByParamsPaketLelang(array("PAKET_ID" => $reqId, "REKANAN_ID" => $reqToken), -1, -1, " AND TANGGAL_DAFTAR IS NOT NULL AND A.LULUS_PENDAFTARAN = 1 AND A.LULUS_KUALIFIKASI = 1");
	$paket_rekanan->firstRow();
	if($paket_rekanan->getField("PAKET_REKANAN_ID") == "")
	  exit;
}
 

  header("Content-Type: application/vnd.ms-word");
  header("Expires: 0");
  header("Cache-Control:  must-revalidate, post-check=0, pre-check=0");
  header("Content-disposition: attachment; filename=\"aanwijzing_".$reqId.date('Ymd').date('His').".doc\"");

  $arrContextOptions=array(
    "ssl"=>array(
        "verify_peer"=>false,
        "verify_peer_name"=>false,
    ),
  );

  $output = file_get_contents(SYSTEM_URL_EMAIL."/main/loadUrl/report/aanwijzing_cetak2/?reqId=".$reqId, false, stream_context_create($arrContextOptions));

ob_end_clean();
  echo $output;
  exit;

 
?>
