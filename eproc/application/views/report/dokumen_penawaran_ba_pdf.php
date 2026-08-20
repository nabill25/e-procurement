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
$this->load->model("PaketTahap");
$this->load->model("PaketDokumen");

// Unit Kerja
$this->load->library("libbreadcrumb");
$unitkerjaid =  $this->kauth->getInstance()->getIdentity()->UNIT_KERJA_ID;
$unitkerja = $this->libbreadcrumb->unitkerja($unitkerjaid);
// End Unit Kerja

$paket_rekanan = new PaketRekanan();
$paket_tahap_metode = new PaketTahap();
$paket_tahap = new PaketTahap();
$paket_dokumen = new PaketDokumen();

$reqId = $this->input->get("reqId");

$paketInfo->getPaket($reqId);

/* LOGIN CHECK */
if($this->USER_LOGIN_ID == "" || $this->USER_TYPE_ID != '3' && $this->USER_TYPE_ID != '10') {
	echo "IP anda tercatat dalam aplikasi eproc";
	exit;
}

$jenis_tahap = $paket_tahap_metode->getJenisTahapById($reqId);

$arrUploadPasswordPenawaran	 = array(0, 12, 7,  12, 7,  11, 7,  12, 12, 0, 0, 12, 7,  12, 7);
 
if($this->USER_TYPE_ID == "3" || $this->USER_TYPE_ID == "10")
{}
else
{
	$paket_rekanan->selectByParamsPaketLelang(array("PAKET_ID" => $reqId, "REKANAN_ID" => $this->ID), -1, -1, " AND TANGGAL_DAFTAR IS NOT NULL AND A.LULUS_PENDAFTARAN = 1 AND A.LULUS_KUALIFIKASI = 1");
	$paket_rekanan->firstRow();
	if($paket_rekanan->getField("PAKET_REKANAN_ID") == "")
		exit;
	
	/* CEK APAKAH SUDAH MEMASUKKAN DOKUMEN PENAWARAN */
	$paket_dokumen = new PaketDokumen();
	$sudahUpload = $paket_dokumen->getCountByParams(array("PAKET_ID" => $reqId, "REKANAN_USER_ID" => $this->ID), " AND JENIS_DOKUMEN LIKE 'PENAWARAN%' ");
	if($sudahUpload <= 0)
		exit;
}

// $mpdf = new mPDF('c','LEGAL',0,'',2,2,2,2,2,2,'L');
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

$stylesheet = file_get_contents('css/cetak.css');
// echo $stylesheet; die();
$mpdf->WriteHTML($stylesheet,1);	// The parameter 1 tells that this is css/style only and no body/html/text

$arrContextOptions=array(
    "ssl"=>array(
        "verify_peer"=>false,
        "verify_peer_name"=>false,
    ),
);

$html .= file_get_contents(SYSTEM_URL_EMAIL."/main/loadUrl/report/dokumen_penawaran_ba?reqId=".$reqId."&unitkerjaid=".$unitkerjaid, false, stream_context_create($arrContextOptions));						
// $html .= file_get_contents(SYSTEM_URL_EMAIL."/main/loadUrl/report/dokumen_penawaran_ba?reqId=".$reqId."&unitkerjaid=".$unitkerjaid);

$this->load->model("PaketPembukaanValidasi");
$paket_pembukaan_validasi = new PaketPembukaanValidasi();
$paket_pembukaan_validasi->selectByParamsValidasi(array("A.PAKET_ID" => $reqId));
$i=1;
$validatorNumber = "";
$validatorNIP = "";
while($paket_pembukaan_validasi->nextRow())
{
	$validatorNIP .= $paket_pembukaan_validasi->getField("NIP");
	if($paket_pembukaan_validasi->getField("KODE") == "")
	{}
	else
		$validatorNumber .= $i;
		
	$i++;
}
 

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
$mpdf->Output('dokumen_penawaran_peserta.pdf','I');
exit;
//==============================================================
//==============================================================
//==============================================================
?>
