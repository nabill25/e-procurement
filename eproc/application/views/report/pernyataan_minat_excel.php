<?php
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 * 
 */
$this->load->library("kauth");  $userLogin = new kauth(); 
$this->load->library("paketinfo"); $paketInfo = new paketinfo();
include_once("functions/string.func.php");
include_once("functions/date.func.php");
include_once("lib/phpqrcode/qrlib.php");

$PNG_TEMP_DIR = 'uploads/';
/*header("Content-type: application/vnd-ms-excel");
header("Content-Disposition: attachment; filename=Neraca_Saldo_Eksport.xls");*/
$this->load->model("Rekanan");
$this->load->model("PaketPernyataanMinat");

$rekanan = new Rekanan();
$paket_pernyataan_minat = new PaketPernyataanMinat();

$reqId = $this->input->get("reqId");
$reqRekananId = $this->input->get("reqRekananId");
$reqPaketRekananId = $this->input->get("reqPaketRekananId");

// $paket_pernyataan_minat->selectByParams(array("MD5(PAKET_REKANAN_ID)" => $reqPaketRekananId));
$paket_pernyataan_minat->selectByParams(array("MD5(PAKET_REKANAN_ID::text)" => $reqPaketRekananId));
$paket_pernyataan_minat->firstRow();

$rekanan->selectByParamsSimple(array("MD5(A.REKANAN_ID)" => $reqRekananId));
$rekanan->firstRow();

$paketInfo->getPaket($reqId);
?>
<!doctype html>
<html>
	<head>
		<meta charset="utf-8">
		<base href="<?=base_url()?>" />
        
        
	</head>
	<body style="font-family:'Arial Narrow';">
    	<div class="kop-laporan">
            <div class="info">
                <div class="judul-laporan" align="center" style="text-transform:uppercase; font-weight:bold;"><font size="+1">Surat Pernyataan Minat</font></div><br>
                 <div class="judul-laporan" align="center"><font size="+1">Untuk Mengikuti Pekerjaan <?=$paketInfo->nama?></font></div>
                <br>
            </div>
        </div>
        <div class="data-laporan">
        <table border="1" class="tabel-pernyataan">
            <tbody>
              <tr>
                <td  align="left" width="35%">Nama</td>
                <td align="left" width="1%">:</td>
                <td align="left"><?=strtoupper($paket_pernyataan_minat->getField("NAMA"))?></td>
              </tr>
               <tr>
                <td align="left">Jabatan</td>
                <td align="left">:</td>
                <td  align="left"><?=strtoupper($paket_pernyataan_minat->getField("JABATAN"))?></td>
              </tr>
              <tr>
                <td align="left">Bertindak untuk dan atas nama</td>
                <td align="left">:</td>
                <td  align="left"><?=strtoupper($paket_pernyataan_minat->getField("NAMA"))?></td>
              </tr> 
              <tr>
                <td align="left">Alamat</td>
                <td align="left">:</td>
                <td align="left"><?=strtoupper($paket_pernyataan_minat->getField("ALAMAT"))?></td>
              </tr>
              <tr>
                <td align="left">Telepon/Fax</td>
                <td  align="left">:</td>
                <td  align="left"><?=strtoupper($paket_pernyataan_minat->getField("TELEPON"))?></td>
              </tr>
              <tr>
                <td align="left">E-mail</td>
                <td  align="left">:</td>
                <td  align="left"><?=strtoupper($paket_pernyataan_minat->getField("EMAIL"))?></td>
              </tr>
        </tbody>        
        </table>
        <div class="isi" align="justify" style="text-align: justify;">
            <p>Menyatakan dengan sebenarnya bahwa setelah mengetahui pengadaan yang akan dilaksanakan oleh : 
			 	<?=$paketInfo->unit_kerja?>, 
                maka dengan ini saya menyatakan berminat untuk mengikuti proses pelelangan pekerjaan dengan 
             	<span style="text-decoration:underline">Prakualifikasi</span> 
                untuk pekerjaan <?=$paketInfo->nama?> sampai dengan selesai.</p>
                 
            <p>Demikian pernyataan ini kami buat dengan penuh kesadaran dan rasa tanggung jawab.</p>
        </div>
        
        <div class="area-ttd-bawah">
            <div class="tanggal-tempat">
                 <div class="tanggal"><?=getFormattedDate($paket_pernyataan_minat->getField("TANGGAL"))?></div>
                 <div class="tempat"><?=strtoupper($rekanan->getField("NAMA"))?></div>
            </div>
            <div class="ttd" style="height: 100px">
           <?php
                // $encrypt_text = $paket_pernyataan_minat->getField("KODE_QR");//"adada";//$arrKodeQr[$i];
                // $filename = $PNG_TEMP_DIR.$encrypt_text.'.png';
                // $errorCorrectionLevel = 'L';   
                // $matrixPointSize = 3;
                // QRcode::png($encrypt_text, $filename, $errorCorrectionLevel, $matrixPointSize, 2);    
                //display generated file
            ?>
            <?php
                 // echo '<img src="'.$PNG_TEMP_DIR.basename($filename).'" />'; 
            ?>
            </div>
            <div class="nama">
                 <?=strtoupper($paket_pernyataan_minat->getField("NAMA"))?>
            </div>
            <div class="jabatan">
                 <?=strtoupper($paket_pernyataan_minat->getField("JABATAN"))?>
            </div>
        </div>
        
        </div>
	</body>
</html>