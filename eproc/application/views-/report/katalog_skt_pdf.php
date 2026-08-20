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
$reqId = $this->input->get("reqId"); // rekanan id
$kode = $this->input->get("reqKode"); // kode rekanan 

/* LOGIN CHECK */
if($this->USER_TYPE_ID != "2") // selain user admin vms
{
  exit; 
} 

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

$mpdf->list_indent_first_level = 0; // 1 or 0 - whether to indent the first level of a list

// LOAD a stylesheet
$stylesheet = file_get_contents('css/cetak.css');
$mpdf->WriteHTML($stylesheet,1);  // The parameter 1 tells that this is css/style only and no body/html/text

$arrContextOptions=array(
    "ssl"=>array(
        "verify_peer"=>false,
        "verify_peer_name"=>false,
    ),
);

$html .= file_get_contents(SYSTEM_URL_EMAIL."/main/loadUrl/report/katalog_skt?reqId=".$reqId."&reqKode=".$kode);

$mpdf->WriteHTML($html,2);
ob_end_clean();
$mpdf->Output('sertifikat_katalog.pdf','I'); // I:Print D:Download
exit;
//==============================================================
//==============================================================
//==============================================================
?>
