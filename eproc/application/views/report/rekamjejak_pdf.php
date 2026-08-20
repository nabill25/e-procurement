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
$this->load->model("Metode");
$this->load->library("AES");

// Unit Kerja
$this->load->library("libbreadcrumb");
$unitkerjaid =  $this->kauth->getInstance()->getIdentity()->UNIT_KERJA_ID;
$unitkerja = $this->libbreadcrumb->unitkerja($unitkerjaid);
// End Unit Kerja

$id = $this->input->get("id");
$paketid = $this->input->get("paketid");

$paketInfo->getPaket($paketid);
$reqNama = $paketInfo->nama;

/* LOGIN CHECK */
if($this->USER_LOGIN_ID == "" || $this->USER_TYPE_ID != '3' && $this->USER_TYPE_ID != '10') { //3:Panitia, 10:Audit
	echo "IP anda tercatat dalam aplikasi eproc ";
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

$mpdf->list_indent_first_level = 0;	// 1 or 0 - whether to indent the first level of a list

$stylesheet = file_get_contents('css/cetak.css');
// echo $stylesheet; die();
$mpdf->WriteHTML($stylesheet,1);	// The parameter 1 tells that this is css/style only and no body/html/text

$arrContextOptions=array(
    "ssl"=>array(
        "verify_peer"=>false,
        "verify_peer_name"=>false,
    ),
);

$html .= file_get_contents(base_url()."main/loadUrl/report/rekamjejak?id=".$id."&paketid=".$paketid."&unitkerjaid=".$unitkerjaid, false, stream_context_create($arrContextOptions));

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
$mpdf->Output('Rekam_jejak_'.$reqNama.'.pdf','I');
exit;
//==============================================================
//==============================================================
//==============================================================
?>
