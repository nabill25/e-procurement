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
$nip =  $this->kauth->getInstance()->getIdentity()->NIP;
$unitkerja = $this->libbreadcrumb->unitkerja($unitkerjaid);
// End Unit Kerja
$reqId = $this->input->get("reqId"); 

$paketInfo->getPaket($reqId); 


/* LOGIN CHECK */
if($this->USER_LOGIN_ID == "")
    exit;

$filename = 'PAKET_'.$paketInfo->nama.'_'.$paketInfo->pr_group_number.'.pdf';
$path = FCPATH.'uploads/pelaporan/'.$filename;

if (file_exists($path)) {
    // Download ke browser
    $this->load->helper('download');
    $data = file_get_contents($path);
    force_download($filename, $data);
} else 
{
    
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

    $link_report = "paket_cetak";

    $arrContextOptions=array(
        "ssl"=>array(
            "verify_peer"=>false,
            "verify_peer_name"=>false,
        ),
    );

    $html .= file_get_contents(SYSTEM_URL_EMAIL."/main/loadUrl/report/".$link_report."?reqId=".$reqId."&unitkerjaid=".$unitkerjaid."&userloginid=".$this->USER_LOGIN_ID."&nip=".$this->NIP, false, stream_context_create($arrContextOptions));//".$link_report."?reqId=".$reqId);  					
    $mpdf->SetHTMLFooter('
    <table width="100%" style="vertical-align: bottom; font-family: serif; font-size: 8pt; color: #000000; font-weight: bold; font-style: italic;"><tr>
    <td width="33%"><span style="font-weight: bold; font-style: italic;">'.SYSTEM_NAME.' '.$unitkerja.'</span></td>
    <td width="33%" align="center" style="font-weight: bold; font-style: italic;">{PAGENO}/{nbpg}</td>
    <td width="33%" style="text-align: right; "> <!-- <img src="'.$PNG_TEMP_DIR.basename($filename).'" /> --></td>
    </tr></table>
    ');  // Note that the second parameter is optional : default = 'O' for ODD
    /* SET FOOTER */

    $mpdf->WriteHTML($html,2);
    ob_end_clean();
    // $mpdf->Output('PAKET_'.$paketInfo->nama.'_'.$paketInfo->pr_group_number.'.pdf','I');

    $mpdf->Output($path, 'F');
    // Download ke browser
    $mpdf->Output($filename, 'D');
    exit;
}
//==============================================================
//==============================================================
//==============================================================
?>
