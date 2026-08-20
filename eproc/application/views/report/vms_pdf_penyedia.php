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

$reqId = $this->REKANAN_ID; // rekanan id
$kode = $this->REKANAN_KODE; // kode rekanan
$rekanantipeid = $this->input->get("rekanantipeid"); // kode rekanan

$PNG_TEMP_DIR = 'uploads/';
$PNG_TEMP_DIR_BARCODE = 'uploads/vms/barcode/';

$filename = $PNG_TEMP_DIR_BARCODE.$reqId.'_'.$kode.'.png';

if (file_exists($filename)) { } else {
   echo "SKT Belum bisa dicetak karena Admin belum generate SKT, silahkan hubungi Admin..!!!"; exit;
}

$rekanan->selectByParams(array('A.REKANAN_ID' => $reqId), -1, -1);
$rekanan->firstRow();
$reqRekananTipeId = $rekanan->getField("REKANAN_TIPE_ID");
$user_validasi = $rekanan->getField("USER_VALIDASI");

/* LOGIN CHECK */
if($this->USER_LOGIN_ID == "" || $this->USER_TYPE_ID != "6") // selain user Penyedia no access
  exit; 

/*$mpdf = new mPDF('c','LEGAL',0,'',2,2,2,2,2,2,'L');*/
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

$mpdf->list_indent_first_level = 0; // 1 or 0 - whether to indent the first level of a list

// LOAD a stylesheet
$stylesheet = file_get_contents('css/cetak.css');
$mpdf->WriteHTML($stylesheet,1);  // The parameter 1 tells that this is css/style only and no body/html/text

if ($rekanantipeid == '7') { // Perorangan
    $link_report = "vms_perorangan_penyedia";
} else {
    $link_report = "vms_penyedia";
}

$arrContextOptions=array(
    "ssl"=>array(
        "verify_peer"=>false,
        "verify_peer_name"=>false,
    ),
);

$html .= file_get_contents(SYSTEM_URL_EMAIL."/main/loadUrl/report/".$link_report."?reqId=".$reqId."&kode=".$kode, false, stream_context_create($arrContextOptions));

$mpdf->WriteHTML($html,2);
ob_end_clean();
$mpdf->Output('sertifikat_vms.pdf','I');
exit;
//==============================================================
//==============================================================
//==============================================================
?>
