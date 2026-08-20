<?php
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 * 
 */
/* INCLUDE FILE */
$this->load->library("kauth");  $userLogin = new kauth(); 
$this->load->library("paketinfo"); $paketInfo = new paketinfo();
$this->load->model("PaketRekanan");
$this->load->model("PaketTahap");
$this->load->model("Aanwijzing");
$this->load->model("PhpShoutbox");
include_once("functions/date.func.php");
include_once("functions/default.func.php");
include_once("functions/string.func.php");
$this->load->model("PaketEvaluasiAdminTawar");
$this->load->model("PaketEvaluasiTeknisTawar");
$this->load->model("PaketEvaluasiHargaTawar");
$this->load->model("PaketPembukaanValidasi");
$this->load->model("Metode");
include_once("lib/phpqrcode/qrlib.php");
$PNG_TEMP_DIR = 'uploads/';
$reqId = httpFilterGet("reqId");
$reqRekananId = httpFilterGet("rekananid");
/* create objects */
$paket_rekanan = new PaketRekanan();
$paket_evaluasi_admin_tawar = new PaketEvaluasiAdminTawar();
$paket_evaluasi_teknis_tawar = new PaketEvaluasiTeknisTawar();
$paket_evaluasi_harga_tawar = new PaketEvaluasiHargaTawar();
$paket_pembukaan_validasi = new PaketPembukaanValidasi();

$paketInfo->getPaket($reqId);
$reqNama = $paketInfo->nama;
$reqSistemSampul = $paketInfo->sistem_sampul;
$reqMetodeLelang = $paketInfo->metode_lelang_id;

$paket_rekanan = new PaketRekanan();

$metode = new Metode();
$metode->selectByParams(array("UPPER(A.NAMA)" => "UPLOAD DOKUMEN PENAWARAN"), -1, -1, $reqId);
$metode->firstRow();	

$time = strtotime($metode->getField("TANGGAL_AKHIR"));
$aanwijzing_hari = date('w', $time);
$aanwijzing_tanggal = (int)date('d', $time);
$aanwijzing_bulan = (int)date('m', $time);
$aanwijzing_tahun = (int)date('Y', $time);
$aanwijzing_dmy = date('d-m-Y', $time);
$aanwijzing_ymd = date('Y-m-d', $time);

$nomor = $paketInfo->pr_group_number."/BA.MASUK/".getYear($paketInfo->tanggal);
$paket_pembukaan_validasi->selectByParamsValidasi(array("A.PAKET_ID" => $reqId)); 

?>


<html moznomarginboxes mozdisallowselectionprint>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<base href="<?=base_url()?>" />

<!-- QRCODE -->
<script type="text/javascript" src="lib/jquery-qrcode-0.11.0/demo/jquery-1.10.2.js"></script>
<script type="text/javascript" src="lib/jquery-qrcode-0.11.0/jquery.qrcode-0.11.0.js"></script>
<script type="text/javascript" src="lib/jquery-qrcode-0.11.0/demo/ff-range.js"></script>
<script type="text/javascript" src="lib/jquery-qrcode-0.11.0/demo/scripts.js"></script>
<!--<link href='http://fonts.googleapis.com/css?family=Ubuntu:400,700' rel='stylesheet'>-->
<!--<link href="http://fonts.googleapis.com/css?family=Noto+Sans" rel="stylesheet" type="text/css">-->


<link rel="stylesheet" href="css/dokumen-pembukaan-penawaran.css" type="text/css">
</head>

<body>

<div class="tombol-print">
<input type="button" value="Print" onClick="print();">
</div>

<br>
<div class="logo"><img src="images/<?= SYSTEM_LOGO_CETAK ?>" height="75" /></div>
<div class="judul">
	HASIL PEMASUKAN PENAWARAN
	<?php 
    if($paketInfo->bahasa == "EN")
		echo "<br>MINUTES OF SUBMISSION OF BIDS";
	?>
</div><br>

<!-- <div class="nomor">Nomor :  <?=$nomor?></div><br> -->

<div class="pekerjaan">
    PEKERJAAN<br />
    <?=strtoupper($paketInfo->nama)?>
</div><br>

<div class="isi">
    Pada hari ini, <?=strtoupper(getHari($aanwijzing_hari));?> tanggal <?=strtoupper(getTerbilang($aanwijzing_tanggal));?> bulan <?=strtoupper(getNameMonth($aanwijzing_bulan));?> tahun <?=strtoupper(getTerbilang($aanwijzing_tahun));?> (<?=$aanwijzing_dmy?>), telah dilaksanakan pemasukan penawaran dengan uraian sebagai berikut.
</div>
<?php 
if($paketInfo->bahasa == "EN")
{
?>
<div class="isi" style="margin-top:20px; font-style:italic">
   On this date, <?=(getHariEn($aanwijzing_hari));?>, <?=getDay($aanwijzing_ymd);?> <?=getNameMonthEn((int)getMonth($aanwijzing_ymd));?> <?=getYear($aanwijzing_ymd);?>, it has been held the Submission of Bids with the descriptions as follow:
</div>
<?php 

} 
$no_urut = 1;

$paket_rekanan->selectByParams(array("PAKET_ID" => $reqId), -1, -1, " AND A.TANGGAL_DAFTAR IS NOT NULL AND A.LULUS_PENDAFTARAN = 1 AND A.LULUS_KUALIFIKASI = 1 AND A.KIRIM_PENAWARAN = 1 AND A.REKANAN_ID = '".$reqRekananId."'  ");

// $paket_rekanan->selectByParams(array("PAKET_ID" => $reqId), -1, -1, " AND A.TANGGAL_DAFTAR IS NOT NULL AND A.LULUS_PENDAFTARAN = 1 AND A.LULUS_KUALIFIKASI = 1 AND A.REKANAN_ID = '".$this->REKANAN_ID."' ");
while($paket_rekanan->nextRow())
{
    $userRekanan = $paket_rekanan->getField("REKANAN_ID");

    $paket_evaluasi_admin_tawar = new PaketEvaluasiAdminTawar();
    $paket_evaluasi_teknis_tawar = new PaketEvaluasiTeknisTawar();
    $paket_evaluasi_harga_tawar = new PaketEvaluasiHargaTawar();
	
	
	$paket_evaluasi_admin_tawar->selectByParamsRekananDokumen($userRekanan, array("A.PAKET_ID" => $reqId));
	$paket_evaluasi_teknis_tawar->selectByParamsRekananDokumen($userRekanan, array("A.PAKET_ID" => $reqId));
	$paket_evaluasi_harga_tawar->selectByParamsRekananDokumen($userRekanan, array("A.PAKET_ID" => $reqId));
	

?>        
    <div class="area-dokumen">

      <table class="table">
      <tr>
          <td colspan="5" class="td"><?=$no_urut?>. <?=$paket_rekanan->getField("FULL_NAMA_REKANAN")?></td>
      </tr>
      <?php 
      if($reqSistemSampul == "2")
			{
			?>
      <tr class="tr-bc">
        <td align="center" colspan="5" class="td">SAMPUL 1 <?php if($paketInfo->bahasa == "EN") echo "/ <em>COVER 1</em>"; ?></td>
      </tr>                
      <?php 
			}
			?>
      <tr class="tr-bc">
        <td class="tdno">No&nbsp; <?php if($paketInfo->bahasa == "EN") echo "/ <em>Number</em>"; ?></td>
        <td class="td" style="width:40%">Nama Dokumen <?php if($paketInfo->bahasa == "EN") echo "/ <em>Documents Name</em>"; ?></td>
        <td class="td">Nama File <?php if($paketInfo->bahasa == "EN") echo "/ <em>File Name</em>"; ?></td>
        <td class="td">Ukuran <?php if($paketInfo->bahasa == "EN") echo "/ <em>File Size</em>"; ?></td>
        <td class="td">Tgl Upload <?php if($paketInfo->bahasa == "EN") echo "/ <em>Upload Date</em>"; ?></td>
      </tr>
    <?php 
    if ($reqMetodeLelang == '7') {
    } else { ?>
      <tr class="tr">
          <!-- <td class="padding5">I</td> -->
          <td colspan="5" class="padding5">Dokumen Administrasi <?php if($paketInfo->bahasa == "EN") echo "/ <em>Administrative Documents</em>"; ?></td>
      </tr>
        <?php 
        $id = 1;
        $i=1;
        $jumlahDokumenAdmin = 0;
        $jumlahUploadAdmin = 0;
        while($paket_evaluasi_admin_tawar->nextRow())
        {
        ?>                
        <tr class="terang">
          <td class="tdno"><?=$i?>.</td>
          <td class="td"> <?=$paket_evaluasi_admin_tawar->getField("NAMA")?> </td>
          <td class="td font10"> <?=$paket_evaluasi_admin_tawar->getField("KETERANGAN")?> </td>
          <td align="right" class="td font10"> <?=round($paket_evaluasi_admin_tawar->getField("UKURAN") / 1024, 2)?> Kb </td>
          <td class="td font10"> <?=coalesce($paket_evaluasi_admin_tawar->getField("TANGGAL_UPLOAD"), "N/A")?> </td>            
        </tr>
        <?php 
          $i++;
          $id++;
          $jumlahDokumenAdmin++;
        }
        ?>
        <tr class="tr">
          <!-- <td class="padding5">II</td> -->
          <td colspan="5" class="padding5">Dokumen Teknis <?php if($paketInfo->bahasa == "EN") echo "/ <em>Technical Documents</em>"; ?></td>
        </tr>
        <?php 
        $i=1;
        $jumlahDokumenTeknis = 0;
        $jumlahUploadTeknis = 0;
        while($paket_evaluasi_teknis_tawar->nextRow())
        {
        ?>                
        <tr class="terang">
          <td class="tdno"><?=$i?>.</td>
          <td class="td"> <?=$paket_evaluasi_teknis_tawar->getField("NAMA")?> </td>
          <td class="td font10"> <?=$paket_evaluasi_teknis_tawar->getField("KETERANGAN")?> </td>
          <td align="right" class="td font10"> <?=round($paket_evaluasi_teknis_tawar->getField("UKURAN") / 1024, 2)?> Kb </td>
          <td class="td font10"> <?=coalesce($paket_evaluasi_teknis_tawar->getField("TANGGAL_UPLOAD"), "N/A")?> </td>
        </tr>
        <?php 
          $i++;
          $id++;
          $jumlahDokumenTeknis++;
        }
        ?>
    <?php 
    }  
        if($reqSistemSampul == "2")
				{
				?>
        <tr class="tr">
          <td align="center" colspan="5" class="padding5">SAMPUL 2 <?php if($paketInfo->bahasa == "EN") echo "/ <em>COVER 2</em>"; ?></td>
        </tr>   
        <tr class="tr-bc">
          <td align="center" class="tdno">No&nbsp; <?php if($paketInfo->bahasa == "EN") echo "/ <em>Number</em>"; ?></td>
          <td align="center" class="td" style="width:40%">Nama Dokumen <?php if($paketInfo->bahasa == "EN") echo "/ <em>Documents Name</em>"; ?></td>
          <td align="center" class="td font10">Nama File <?php if($paketInfo->bahasa == "EN") echo "/ <em>File Name</em>"; ?></td>
          <td align="center" class="td font10">Ukuran <?php if($paketInfo->bahasa == "EN") echo "/ <em>File Size</em>"; ?></td>
          <td align="center" class="td font10">Tgl Upload <?php if($paketInfo->bahasa == "EN") echo "/ <em>Upload Date</em>"; ?></td>
        </tr>             
        <?php 
				$rowawi = "I";
				}
				else
					$romawi = "III";
				?>
        <tr class="tr">
          <!-- <td class="padding5"><?=$romawi?></td> -->
          <td colspan="5" class="padding5">Dokumen Harga <?php if($paketInfo->bahasa == "EN") echo "/ <em>Pricing Document</em>"; ?></td>
        </tr>
        <?php 
        $i=1;
        $jumlahDokumenHarga = 0;
        $jumlahUploadHarga = 0;
        while($paket_evaluasi_harga_tawar->nextRow())
        {
        ?>                
        <tr class="terang">
          <td class="tdno"><?=$i?>.</td>
          <td class="td"> <?=$paket_evaluasi_harga_tawar->getField("NAMA")?> </td>
          <td class="td font10"> <?=$paket_evaluasi_harga_tawar->getField("KETERANGAN")?> </td>
          <td align="right" class="td font10"> <?=round($paket_evaluasi_harga_tawar->getField("UKURAN") / 1024, 2)?> Kb </td>
          <td class="td font10"> <?=coalesce($paket_evaluasi_harga_tawar->getField("TANGGAL_UPLOAD"), "N/A")?> </td>
        </tr>
        <?php 
          $i++;
          $id++;
          $jumlahDokumenHarga++;
        }
        ?>                          
                
      </table>
    </div>
    <br>
<?php 
    unset($paket_evaluasi_admin_tawar);
    unset($paket_evaluasi_teknis_tawar);
    unset($paket_evaluasi_harga_tawar);
    $no_urut++;
}
?>
<!-- <p></p> -->
 
<div class="area-dokumen">
PELAKSANA PENGADAAN BARANG DAN JASA <?php if($paketInfo->bahasa == "EN") echo "/ <em>Procurement Committee</em>"; ?>
    <table class="table">
    <tr class="tr-bc">
        <td class="tdno">No</td>
        <td class="td">Nama <?php if($paketInfo->bahasa == "EN") echo "/ <em>Name</em>"; ?></td>
        <!-- <td style="width:20%">Approval QRCode</td> -->
    </tr>
    <?php 
	$i = 1;
    while($paket_pembukaan_validasi->nextRow())
	{
	?>
        <tr>
            <td class="tdno"><?=$i?></td>
            <td class="td">
                <?=$paket_pembukaan_validasi->getField("NAMA")?>
            </td>
            <!-- <td align="center"> -->
                <?php 
                    //$paket_pembukaan_validasi->getField("NIP");
                    /*$encrypt_text = "QR CODE VALID UNTUK DOKUMEN NOMOR ".$nomor." (".$paket_pembukaan_validasi->getField("NAMA").")";
                    $filename = $PNG_TEMP_DIR.$reqId.$paket_pembukaan_validasi->getField("KODE").'.png';
                    $errorCorrectionLevel = 'L';   
                    $matrixPointSize = 2;
                    QRcode::png($encrypt_text, $filename, $errorCorrectionLevel, $matrixPointSize, 2);    
                    //display generated file
                    echo '<img src="'.$PNG_TEMP_DIR.basename($filename).'" />'; */
                ?>        
            <!-- </td> -->
        </tr>
	<?php 
		$i++;
	}
	?>       
    </table>

</div>
<div class="nomor-oe">
  <div class="data" style="font-size:10px; font-style:italic">
       <?= SYSTEM_SAH ?>
       <?php if($paketInfo->bahasa == "EN") echo "<br><br><em>".SYSTEM_SAH_EN."</em>"; ?>
  </div>
</div> 

</body>
</html>
