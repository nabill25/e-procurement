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

$reqId = $this->input->get("reqId");
$pemenang = $this->input->get("pemenang");
$template = str_replace(" ","|-|",$this->input->get("reqTemplate"));
$paketInfo->getPaket($reqId);

$reqSistemSampul = $paketInfo->sistem_sampul;

if($this->USER_LOGIN_ID == "")
	exit;

$mpdf = new mPDF('c','A4');
$mpdf->AddPage('P', // L - landscape, P - portrait
            '', '', '', '',
            8, // margin_left
            8, // margin right
            10, // margin top
            28, // margin bottom
            2, // margin header
            2);

$mpdf->SetDisplayMode('fullpage');

$mpdf->list_indent_first_level = 0;	// 1 or 0 - whether to indent the first level of a list

$stylesheet = file_get_contents('css/cetak.css');
$mpdf->WriteHTML($stylesheet,1);	// The parameter 1 tells that this is css/style only and no body/html/text

$link_report = "paket_penilaian_multi";

$arrContextOptions=array(
    "ssl"=>array(
        "verify_peer"=>false,
        "verify_peer_name"=>false,
    ),
);

$html .= file_get_contents(SYSTEM_URL_EMAIL."/main/loadUrl/report/".$link_report."?reqId=".$reqId."&pemenang=".$pemenang."&template=".$template, false, stream_context_create($arrContextOptions));

$mpdf->WriteHTML($html,2);
ob_end_clean();
$mpdf->Output('paket_panilaian_rekanan.pdf','I');
exit;
//==============================================================
//==============================================================
//==============================================================
?>
