<?php
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 * 
 */
 ini_set('max_execution_time', 300); //300 seconds = 5 minutes
include_once("functions/string.func.php");
include_once("functions/default.func.php");
include_once("functions/date.func.php");

include_once("lib/MPDF60/mpdf.php");

$reqId = $this->input->get("reqId");

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

$stylesheet = file_get_contents('css/laporan-pdf.css');
$mpdf->WriteHTML($stylesheet,1);	// The parameter 1 tells that this is css/style only and no body/html/text

$arrContextOptions=array(
    "ssl"=>array(
        "verify_peer"=>false,
        "verify_peer_name"=>false,
    ),
);

$html = file_get_contents(base_url()."cetak/loadUrl/report/evaluasi_kualifikasi_skk_excel/?reqId=".$reqId, false, stream_context_create($arrContextOptions));


$mpdf->WriteHTML($html,2);

$mpdf->Output('pakta_integritas.pdf','I');
exit;
//==============================================================
//==============================================================
//==============================================================
?>