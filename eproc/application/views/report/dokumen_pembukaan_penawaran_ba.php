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
include_once("lib/phpqrcode/qrlib.php");

// Unit Kerja
$this->load->library("libbreadcrumb"); 
$unitkerjaid = $this->input->get("unitkerjaid");
// End Unit Kerja

$PNG_TEMP_DIR = 'uploads/';

$reqId = httpFilterGet("reqId");
/* create objects */
$paket_rekanan = new PaketRekanan();
$paket_evaluasi_admin_tawar = new PaketEvaluasiAdminTawar();
$paket_evaluasi_teknis_tawar = new PaketEvaluasiTeknisTawar();
$paket_evaluasi_harga_tawar = new PaketEvaluasiHargaTawar();
$paket_pembukaan_validasi = new PaketPembukaanValidasi();

$paketInfo->getPaket($reqId);
$reqNama = $paketInfo->nama;
$reqMetodeLelang = $paketInfo->metode_lelang_id;
$reqNilaiOwnerEstimate = $paketInfo->nilai_owner_estimate;
$reqMetodeLelangId    = $paketInfo->metode_lelang_id;


if($paketInfo->publish_ba_penawaran == "") {
  echo "Klik Publish Dahulu Pembukaan Penawaran Pengadaan";
	exit;
}

$paket_rekanan = new PaketRekanan();
	

$time = strtotime($paketInfo->publish_ba_penawaran_tanggal);
$aanwijzing_hari = date('w', $time);
$aanwijzing_tanggal = (int)date('d', $time);
$aanwijzing_bulan = (int)date('m', $time);
$aanwijzing_tahun = (int)date('Y', $time);
$aanwijzing_dmy = date('d-m-Y', $time);
$aanwijzing_ymd = date('Y-m-d', $time);

$nomor = $paketInfo->pr_group_number."/BA.PEMBUKAAN/".getYear($paketInfo->tanggal);

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
<div class="logo"><img src="images/<?= $this->libbreadcrumb->cetakcopyrightlogo($unitkerjaid) ?>" height="75" /></div>
<div class="judul">
	HASIL PEMBUKAAN PENAWARAN
	<?php
    if($paketInfo->bahasa == "EN")
		echo "<br>MINUTES OF BIDS OPENING";
	?>    
</div><br>

<!-- <div class="nomor">Nomor :  <?=$nomor?></div><br> -->

<div class="pekerjaan">
    PEKERJAAN <?php if($paketInfo->bahasa == "EN") echo "/ <em>WORKS</em>"; ?><br />
    <?=strtoupper($paketInfo->nama)?>
</div><br>

<div class="isi">
    Pada hari ini, <?=strtoupper(getHari($aanwijzing_hari));?> tanggal <?=strtoupper(getTerbilang($aanwijzing_tanggal));?> bulan <?=strtoupper(getNameMonth($aanwijzing_bulan));?> tahun <?=strtoupper(getTerbilang($aanwijzing_tahun));?> (<?=$aanwijzing_dmy?>), telah dilaksanakan pembukaan penawaran dengan hasil sebagai berikut.
</div>
<?php
if($paketInfo->bahasa == "EN")
{
?>
<div class="isi" style="margin-top:20px; font-style:italic">
   On this date, <?=(getHariEn($aanwijzing_hari));?>, <?=getDay($aanwijzing_ymd);?> <?=getNameMonthEn((int)getMonth($aanwijzing_ymd));?> <?=getYear($aanwijzing_ymd);?>, it has been held the Bid Opening for the work referred to above as follow.
</div>
<?php
}
?>

<?php
$no_urut = 1;
$paket_rekanan->selectByParams(array("PAKET_ID" => $reqId), -1, -1, " AND A.TANGGAL_DAFTAR IS NOT NULL AND A.LULUS_PENDAFTARAN = 1 AND A.LULUS_KUALIFIKASI = 1 AND A.KIRIM_PENAWARAN = 1 ");
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
      <tr class="tr">
          <td colspan="5" class="tdnoborder"><?=$no_urut?>. <?=$paket_rekanan->getField("FULL_NAMA_REKANAN")?></td>
      </tr>
      <tr class="tr-bc">
        <td colspan="5" class="td padding10">
        <?php
        // $nilaiPenawaran = $paket_rekanan->getField("NILAI_PENAWARAN");
        $nilaiPenawaran = $paket_rekanan->getField("JUMLAH");
        
        if($nilaiPenawaran == "")
        {
          if($paketInfo->bahasa == "EN")              
            echo "<font color=\"red\">DOKUMEN TIDAK DAPAT DIBUKA / TIDAK LENGKAP (DOCUMENTS CAN NOT OPEN / INCOMPLETE)</font>";
          else
            echo "<font color=\"red\">DOKUMEN TIDAK DAPAT DIBUKA / TIDAK LENGKAP</font>";
        }
        else
        {
        ?>
          Nilai Penawaran <?php if($paketInfo->bahasa == "EN") echo "/ <em>Bid Price</em>"; ?>: <?=$paketInfo->mata_uang?> <?=numberToIna($nilaiPenawaran)?> (<?=terbilang($nilaiPenawaran)?>)
          <?php
        }
        ?>
       </td>
      </tr>
      <tr class="tr-bc">
        <td class="tdno">No. <?php if($paketInfo->bahasa == "EN") echo "/ <em>Number</em>"; ?></td>
        <td class="td" style="width:40%">Nama Dokumen <?php if($paketInfo->bahasa == "EN") echo "/ <em>Documents Name</em>"; ?></td>
        <td class="td">Nama File <?php if($paketInfo->bahasa == "EN") echo "/ <em>File Name</em>"; ?></td>
        <td class="td">Ukuran <?php if($paketInfo->bahasa == "EN") echo "/ <em>File Size</em>"; ?></td>
        <td class="td">Tgl Upload <?php if($paketInfo->bahasa == "EN") echo "/ <em>Upload Date</em>"; ?></td>
      </tr>
    <?php 
    if ($reqMetodeLelang == '7') {
    } else 
    { ?>
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
          <td class="td font10"> <?=($paket_evaluasi_admin_tawar->getField("TANGGAL_UPLOAD"))?> </td>            
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
          <td class="td font10"> <?=($paket_evaluasi_teknis_tawar->getField("TANGGAL_UPLOAD"))?> </td>
        </tr>
        <?php
          $i++;
          $id++;
          $jumlahDokumenTeknis++;
        }
        ?>
    <?php 
    } ?>
        <tr class="tr">
          <!-- <td class="padding5">III</td> -->
          <td colspan="5" class="padding5">Dokumen Harga <?php if($paketInfo->bahasa == "EN") echo "/ <em>Financial Documents</em>"; ?></td>
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
          <td class="td font10"> <?=($paket_evaluasi_harga_tawar->getField("TANGGAL_UPLOAD"))?> </td>
        </tr>
        <?php
          $i++;
          $id++;
          $jumlahDokumenHarga++;
        }
        ?>                          
                      
      </table>
    </div>
<?php
    unset($paket_evaluasi_admin_tawar);
    unset($paket_evaluasi_teknis_tawar);
    unset($paket_evaluasi_harga_tawar);
    $no_urut++;
}
?>

<div class="area-dokumen">
  <?php
    if($paketInfo->mata_uang == "USD")
    $pecahan = "dolar";
  else
    $pecahan = "rupiah";
  
  ?>
  <div class="tr-bc td">
      
      Harga Perkiraan <?php if($paketInfo->bahasa == "EN") echo "/ <em>The amount of OE</em>"; ?> : <?=$paketInfo->mata_uang?> <?=numberToIna($reqNilaiOwnerEstimate)?> (<?=terbilang($reqNilaiOwnerEstimate)?> <?=$pecahan?>)
        
    </div>
</div>

<div class="info-oe" >
  
  <?php
    if($paketInfo->mata_uang == "USD")
    $pecahan = "dolar";
  else
    $pecahan = "rupiah";
  
  if($paketInfo->publish_ba_penawaran == "2")
  {
  ?>
  <div class="data">
      
      
        <strong>Seluruh penawaran dari rekanan di atas Owner Estimate, selanjutnya dilakukan proses pelelangan ulang (Pemilihan Terbatas).</strong>
        
        <?php
        if($paketInfo->bahasa == "EN")
    {
    ?>
        <br><br>
        <strong><em>In case of the amount of all bids is higher than Owner Estimate, then will be conducted re-tender (Selected Vendor)</em></strong>
        <?php
    }
    ?>
    </div>
    <?php
  }
  ?>
</div>

<p></p>
 <br>
<div class="area-dokumen mt-20">
<?php 
  if ($reqMetodeLelangId == '2') {
   } else {
    echo "PELAKSANA PENGADAAN BARANG DAN JASA :";
   } ?>
<?php if($paketInfo->bahasa == "EN") echo "/ <em>Procurement Committee</em>"; ?>
    <table class="table">
    <tr class="tr-bc">
        <td style="width:20px" class="tdno">No</td>
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
                   /*$encrypt_text = "QR CODE VALID UNTUK DOKUMEN NOMOR ".$nomor." ( ".$paket_pembukaan_validasi->getField("NAMA").")";
                    $filename = $PNG_TEMP_DIR.$reqId.$paket_pembukaan_validasi->getField("NIP").'.png';
                   
                    //$encrypt_text = $paket_pembukaan_validasi->getField("NIP");
                    //$filename = $PNG_TEMP_DIR.$encrypt_text.'.png';
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
       <?= $this->libbreadcrumb->cetakcopyright($unitkerjaid) ?>
       <?php if($paketInfo->bahasa == "EN") echo "<br><br><em>".SYSTEM_SAH_EN."</em>"; ?>
  </div>
</div>


<!--<table>
   <thead><tr><td>&nbsp;</td></tr></thead>
   <tbody>
     <tr><td>
     	
        
        
     </td>
     </tr>
     <tr>
     <td align="center">
     	
      </td>
     </tr>
   </tbody>
   <tfoot>
   <tr>
   <td>
   		
    </td>
    </tr></tfoot>
</table>-->

</body>
</html>
