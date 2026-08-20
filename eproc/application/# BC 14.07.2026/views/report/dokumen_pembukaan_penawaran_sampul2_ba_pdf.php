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
$this->load->library("kauth");  $userLogin = new kauth(); 
$this->load->library("paketinfo"); $paketInfo = new paketinfo();
$this->load->model("PaketRekanan");
$this->load->model("PaketDokumen");
$paket_rekanan = new PaketRekanan();

// Unit Kerja
$this->load->library("libbreadcrumb");
// End Unit Kerja

$reqId = $this->input->get("reqId");

$paketInfo->getPaket($reqId);

if($paketInfo->publish_ba_penawaran == "") {
  echo "Klik Publish Dahulu Pembukaan Penawaran Pengadaan";
	exit;
}

$reqSistemSampul = $paketInfo->sistem_sampul;
/* LOGIN CHECK */
if($this->USER_LOGIN_ID == "")
	exit;
	
if ($this->USER_TYPE_ID == "6") 
{
		$paket_rekanan->selectByParamsPaketLelang(array("PAKET_ID" => $reqId, "REKANAN_ID" => $this->ID), -1, -1, " AND TANGGAL_DAFTAR IS NOT NULL AND A.LULUS_PENDAFTARAN = 1 AND A.LULUS_KUALIFIKASI = 1 AND A.LULUS_PENAWARAN_SAMPUL1 = 1 ");
		$paket_rekanan->firstRow();
		if($paket_rekanan->getField("PAKET_REKANAN_ID") == "")
			exit;

		/* CEK APAKAH SUDAH MEMASUKKAN DOKUMEN PENAWARAN */
		$paket_dokumen_validasi = new PaketDokumen();
		$sudahUpload = $paket_dokumen_validasi->getCountByParams(array("PAKET_ID" => $reqId, "REKANAN_USER_ID" => $this->ID), " AND JENIS_DOKUMEN LIKE 'PENAWARAN%' ");
		if($sudahUpload <= 0)
			exit;
			
		if($paketInfo->publish_ba_penawaran_sampul2 == "")
			exit;
		
		/* UPDATE HADIR PEMBUKAAN */
		$reqPaketRekananId = $paket_rekanan->getField("PAKET_REKANAN_ID");
		$paket_rekanan->setField("FIELD", "HADIR_PEMBUKAAN_PENAWARAN2");
		$paket_rekanan->setField("FIELD_VALUE", 1);
		$paket_rekanan->setField("PAKET_REKANAN_ID", $reqPaketRekananId);
		$paket_rekanan->update();
			
		unset($paket_rekanan);

	$unitkerjaid = '1';
} else { // Panitia
	$unitkerjaid =  $this->kauth->getInstance()->getIdentity()->UNIT_KERJA_ID;
	$unitkerja = $this->libbreadcrumb->unitkerja($unitkerjaid);

}


/*$mpdf = new mPDF('c','LEGAL',0,'',2,2,2,2,2,2,'L');*/
$mpdf = new mPDF('c','A4');
$mpdf->AddPage('P', // L - landscape, P - portrait
            '', '', '', '',
            8, // margin_left
            8, // margin right
            10, // margin top
            18, // margin bottom
            2, // margin header
            2);  
//$mpdf=new mPDF('c','A4'); 
//$mpdf=new mPDF('utf-8', array(297,420));

$mpdf->SetDisplayMode('fullpage');

$mpdf->list_indent_first_level = 0;	// 1 or 0 - whether to indent the first level of a list

// LOAD a stylesheet
//$stylesheet = file_get_contents('css/rekap-gaji.css');
$stylesheet = file_get_contents('css/cetak.css');
$mpdf->WriteHTML($stylesheet,1);	// The parameter 1 tells that this is css/style only and no body/html/text


$arrContextOptions=array(
    "ssl"=>array(
        "verify_peer"=>false,
        "verify_peer_name"=>false,
    ),
);

$html .= file_get_contents(base_url()."main/loadUrl/report/dokumen_pembukaan_penawaran_sampul2_ba?reqId=".$reqId."&rekananid=".$this->REKANAN_ID."&unitkerjaid=".$unitkerjaid, false, stream_context_create($arrContextOptions));						

/* SET FOOTER */
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
<td width="33%"><span style="font-weight: bold; font-style: italic;">'.SYSTEM_NAME.' '.SYSTEM_NAME_PT.'</span></td>
<td width="33%" align="center" style="font-weight: bold; font-style: italic;">{PAGENO}/{nbpg}</td>
</tr></table>
');  

$mpdf->WriteHTML($html,2);

$mpdf->Output('dokumen_pembukaan_penawaran.pdf','I');
exit;
//==============================================================
//==============================================================
//==============================================================
?>