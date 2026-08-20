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
$this->load->model("Rekanan");  $rekanan = new Rekanan();

$reqId = $this->input->get("reqId"); // rekanan id
$kode = $this->input->get("kode"); // kode rekanan
$rekanantipeid = $this->input->get("rekanantipeid"); // kode rekanan

$rekanan->selectByParams(array('A.REKANAN_ID' => $reqId), -1, -1);
$rekanan->firstRow();
$reqRekananTipeId = $rekanan->getField("REKANAN_TIPE_ID");
$user_validasi = $rekanan->getField("USER_VALIDASI");

if($this->USER_TYPE_ID != "2" && $this->USER_TYPE_ID != "18" && $this->USER_TYPE_ID != "19") // selain user admin vms, approval penyelia & approval sub div  no access
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

$stylesheet = file_get_contents('css/cetak.css');
$mpdf->WriteHTML($stylesheet,1);  // The parameter 1 tells that this is css/style only and no body/html/text

if ($rekanantipeid == '7') { // Perorangan
	$link_report = "vms_perorangan";
} else {
	$link_report = "vms";
}

$arrContextOptions=array(
    "ssl"=>array(
        "verify_peer"=>false,
        "verify_peer_name"=>false,
    ),
);
$PNG_TEMP_DIR = 'uploads/vms/barcode/';
$filename = $PNG_TEMP_DIR.$reqId.'_'.$kode.'.png';

$html .= file_get_contents(SYSTEM_URL_EMAIL."/main/loadUrl/report/".$link_report."?reqId=".$reqId."&kode=".$kode);

/* SET FOOTER */
$i=1;

include_once("lib/phpqrcode/qrlib.php");
$this->load->library("AES");
$aes = new AES();

// -- $encrypt_text = $aes->encrypt($reqId."|".$kode."|".$user_validasi);
$encrypt_text = str_replace(" ", "_", base_url()."main/index/cek_penyedia?reqId=".$reqId."||".$kode."||".$user_validasi);
$errorCorrectionLevel = 'L';
$matrixPointSize = 2;
QRcode::png($encrypt_text, $filename, $errorCorrectionLevel, $matrixPointSize, 2);
// display generated file

 $mpdf->SetHTMLFooter('
 <table width="100%" style="vertical-align: bottom; font-family: serif; font-size: 8pt; color: #000000; font-weight: bold; font-style: italic;">
   <tr>
     <td width="33%"><span style="font-weight: bold; font-style: italic;">'.SYSTEM_NAME.' '.SYSTEM_NAME_PT.'</span></td>
     <td width="33%" align="center" style="font-weight: bold; font-style: italic;">{PAGENO}/{nbpg}</td>
     <td width="33%" style="text-align: right; "><img src="'.$PNG_TEMP_DIR.basename($filename).'" /></td>
   </tr>
 </table>');
 // Note that the second parameter is optional : default = 'O' for ODD
/* SET FOOTER */


$mpdf->WriteHTML($html,2);
ob_end_clean();
$mpdf->Output('sertifikat_vms.pdf','I'); // I:Print D:Download
exit;
//==============================================================
//==============================================================
//==============================================================
?>
