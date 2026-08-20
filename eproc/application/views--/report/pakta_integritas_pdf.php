<?php
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 * 
 */
if($this->USER_TYPE_ID == "")
    redirect("app");
    
ini_set('max_execution_time', 300); //300 seconds = 5 minutes
include_once("functions/string.func.php");
include_once("functions/default.func.php");
include_once("functions/date.func.php");


include_once("lib/MPDF60/mpdf.php");
ob_start();
$reqId = $this->input->get("reqId");
$reqRekananId = $this->input->get("reqRekananId");

$mpdf = new mPDF('c','A4');
$mpdf->AddPage('P', // L - landscape, P - portrait
            '', '', '', '',
            15, // margin_left
            15, // margin right
            16, // margin top
            16, // margin bottom
            9, // margin header
            9);  

$mpdf->mirroMargins = true;

$mpdf->SetDisplayMode('fullpage');

$mpdf->list_indent_first_level = 0;	// 1 or 0 - whether to indent the first level of a list

$stylesheet = file_get_contents('css/cetak.css');
$mpdf->WriteHTML($stylesheet,1);	// The parameter 1 tells that this is css/style only and no body/html/text

$arrContextOptions=array(
    "ssl"=>array(
        "verify_peer"=>false,
        "verify_peer_name"=>false,
    ),
); 

$html = file_get_contents(SYSTEM_URL_EMAIL."/cetak/loadUrl/report/pakta_integritas_excel/?reqId=".$reqId."&reqRekananId=".$reqRekananId, false, stream_context_create($arrContextOptions));


$mpdf->WriteHTML($html,2);
ob_end_clean();
$mpdf->Output('pakta_integritas.pdf','I');
exit;
//==============================================================
//==============================================================
//==============================================================
?>
