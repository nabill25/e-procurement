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

$paket->selectByParamsMonitoring(array("A.PAKET_ID" => coalesce($reqId, 0)));
$paket->firstRow();

if($this->USER_TYPE_ID == 11) // PEJABAT PENGADAAN
{ 
if ($paket->getField("USER_LOGIN_ID") != $this->USER_LOGIN_ID)
  exit;
} else if($this->USER_TYPE_ID == 6) // PENYEDIA
{ 
$katalogrekananRow->selectByParams(array('A.PAKET_ID' => $reqId));
$katalogrekananRow->firstRow();
if ($katalogrekananRow->getField('REKANAN_ID') != $this->ID)
  exit;
} else {
  exit;
}


$paketInfo->getPaket($reqId); 

$mpdf = new mPDF('c','A4');
$mpdf->AddPage('P', // L - landscape, P - portrait
            '', '', '', '',
            8, // margin_left
            8, // margin right
            10, // margin top
            28, // margin bottom
            2, // margin header
            2); 
//$mpdf=new mPDF('c','A4'); 
//$mpdf=new mPDF('utf-8', array(297,420));

$mpdf->SetDisplayMode('fullpage');

$mpdf->list_indent_first_level = 0;	// 1 or 0 - whether to indent the first level of a list

// LOAD a stylesheet
$stylesheet = file_get_contents('css/cetak.css');
$mpdf->WriteHTML($stylesheet,1);	// The parameter 1 tells that this is css/style only and no body/html/text

$link_report = "katalog_cetak";

$arrContextOptions=array(
    "ssl"=>array(
        "verify_peer"=>false,
        "verify_peer_name"=>false,
    ),
);

$html .= file_get_contents(base_url()."main/loadUrl/report/".$link_report."?reqId=".$reqId, false, stream_context_create($arrContextOptions));//".$link_report."?reqId=".$reqId);						
                        					
$mpdf->SetHTMLFooter('
<table width="100%" style="vertical-align: bottom; font-family: serif; font-size: 8pt; color: #000000; font-weight: bold; font-style: italic;"><tr>
<td width="33%"><span style="font-weight: bold; font-style: italic;">'.SYSTEM_NAME.' '.SYSTEM_NAME_PT.'</span></td>
<td width="33%" align="center" style="font-weight: bold; font-style: italic;">{PAGENO}/{nbpg}</td>
<td width="33%" style="text-align: right; "><!-- <img src="'.$PNG_TEMP_DIR.basename($filename).'" /> --></td>
</tr></table>
');  // Note that the second parameter is optional : default = 'O' for ODD
/* SET FOOTER */

$mpdf->WriteHTML($html,2);

$mpdf->Output('PEMBELIAN_LANGSUNG_'.$paketInfo->nama.'_'.$paketInfo->pr_group_number.'.pdf','I');
exit;
//==============================================================
//==============================================================
//==============================================================
?>