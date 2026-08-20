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
$this->load->model("Rekanan");
$this->load->model("Paket"); 
include_once("functions/date.func.php");
include_once("functions/default.func.php");
include_once("functions/string.func.php"); 
include_once("lib/phpqrcode/qrlib.php");

$PNG_TEMP_DIR = 'uploads/';

$reqId = httpFilterGet("reqId");
// Unit Kerja
$this->load->library("libbreadcrumb"); 
$unitkerjaid = $this->input->get("unitkerjaid");
// End Unit Kerja

/* create objects */
$paket                      = new Paket();
$paket_rekanan              = new PaketRekanan(); 
$paket_rekanan2              = new PaketRekanan(); 
$paket_rekanan3             = new PaketRekanan(); 
$paket_rekanan4             = new PaketRekanan(); 
$paket_rekanan_pemenang     = new PaketRekanan(); 
$rekanan                    = new Rekanan();  

$paketInfo->getPaket($reqId); 

$paket->selectByParamsMonitoring(array(), -1, -1, " AND PAKET_ID = '".$reqId."' ");
$paket->firstRow(); 

$field = array('REKANAN', 'TANGGAL_UNDANG','TANGGAL_DAFTAR','LULUS_PENDAFTARAN', 'LULUS_KUALIFIKASI', 'LULUS_PENAWARAN', 'LULUS_PENAWARAN_URUT', 'STATUS_BAYAR');
$allrecord = $paket_rekanan->getCountByParams(array("PAKET_ID" => $reqId));
$paket_rekanan->selectByParamsPaket2(array("PAKET_ID" => $reqId, "NILAI_PENAWARAN|| > " => "0"),0,0," ORDER BY NILAI_PENAWARAN ASC"); 
$paket_rekanan3->selectByParamsPaket2(array("PAKET_ID" => $reqId, "NILAI_PENAWARAN|| > " => "0"),0,0," ORDER BY NILAI_PENAWARAN ASC"); 
$paket_rekanan4->selectByParamsPaket2(array("PAKET_ID" => $reqId, "NILAI_PENAWARAN|| > " => "0"),0,0," ORDER BY NILAI_PENAWARAN ASC"); 
$paket_rekanan2->selectByParamsPaket2(array("PAKET_ID" => $reqId),0,0," ORDER BY REKANAN_NAMA DESC"); 
$paket_rekanan_pemenang->selectByParamsPaketPemenang(array("PAKET_ID" => $reqId, "NILAI_PENAWARAN|| > " => "0")); 
?>

<html moznomarginboxes mozdisallowselectionprint>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<base href="<?=base_url()?>" />
<link rel="stylesheet" href="css/dokumen-pembukaan-penawaran.css" type="text/css">
</head>

<body>

<div class="tombol-print">
<input type="button" value="Print" onClick="print();">
</div>

<br>
<div class="logo"><img src="images/<?= $this->libbreadcrumb->cetakcopyrightlogo($unitkerjaid) ?>" width="100" height="75" /></div>
<div class="judul">
  LAPORAN E-REVERSE AUCTION 
</div><br>
 
 
  <div class="isi" style="margin-bottom:20px"> 
    <table border="0">  
      <tr>
        <td align="left" valign="top" width="30%"> Nama Pengadaan </td>
        <td align="center" valign="top" width="2%">:</td>
        <td align="left" valign="top" width="68%"> <?= $paketInfo->nama?> </td> 
      </tr> 
      <tr>
        <td align="left" valign="top" width="30%"> HPS </td>
        <td align="center" valign="top" width="2%">:</td>
        <td align="left" valign="top" width="68%"> Rp. <?= number_format($paketInfo->nilai,2,",",".");?> </td> 
      </tr> 
      <!-- <tr>
        <td align="left" valign="top" width="30%"> Penawaran Harga Maksimal </td>
        <td align="center" valign="top" width="2%">:</td>
        <td align="left" valign="top" width="68%"> Rp. <?php // number_format($paketInfo->penawaran_harga_maksimal,2,",",".");?> </td> 
      </tr>  -->
      <tr>
        <td align="left" valign="top" width="30%"> Lokasi Pekerjaan </td>
        <td align="center" valign="top" width="2%">:</td>
        <td align="left" valign="top" width="68%"> <?=$paketInfo->lokasi?> </td> 
      </tr>
      <tr>
        <td align="left" valign="top" width="30%"> Tanggal Rev. Auction  </td>
        <td align="center" valign="top" width="2%">:</td>
        <td align="left" valign="top" width="68%"> <?php $arrTgl = explode(" ", $paketInfo->tanggal); echo getFormattedDate($arrTgl[0]) ?> </td> 
      </tr>  
      <tr>
        <td align="left" valign="top" width="30%"> Batas Waktu  </td>
        <td align="center" valign="top" width="2%">:</td>
        <td align="left" valign="top" width="68%"> <?=$paketInfo->bidding_menit?> Menit </td> 
      </tr> 
      <tr>
        <td align="left" valign="top" width="30%"> Uraian </td>
        <td align="center" valign="top" width="2%">:</td>
        <td align="left" valign="top" width="68%"> <?=$paketInfo->uraian?> </td> 
      </tr>  
    </table>
  </div> 
 
  <div class="isi">
    <p>A. Penyedia (Peserta) yang diundang untuk mengikuti e-Reverse Auction sebagai berikut:</p>
    <ol style="margin-bottom:20px">
    <?php 
    $no=1;
      while($paket_rekanan2->nextRow())
      { 
        echo '<li style="margin-left:20px"> 
              '.$paket_rekanan2->getField('REKANAN_NAMA').'
              </li>';
    } ?>
    </ol>
  </div>
  
  <div class="isi">
    <p>B. Hasil e-Reverse Auction sesuai alokasi waktu: <?=$paketInfo->bidding_menit?> Menit</p>
  </div>
    <div class="area-dokumen" style="margin-bottom:30px">
      <table class="table"> 
        <tr  class="tr-bc">
          <td class="td" align="center" width="5%">No</td>
          <td class="td" align="center" width="30%">Nama Perusahaan</td>
          <td class="td" align="center" width="25%">Harga Penawaran <br> Awal (Rp)</td>
          <td class="td" align="center" width="25%">Harga Penawaran <br> setelah e-Reverse Auction (Rp)</td>
          <!-- <td align="center" width="15%">TTD</td> -->
        </tr>
        <?php 
        $no=1;
          while($paket_rekanan->nextRow())
          {
         ?>
        <tr class="judul-kolom">
          <td class="td" align="center" height="60px"><?=$no?></td>
          <td class="td" align="left">
            <?=$paket_rekanan->getField('REKANAN_NAMA');?> <br>
            <small style="font-size:9px">Kode: <i><?=$paket_rekanan->getField('KODE_REKANAN');?></i></small><br>
          </td> 
          <td class="td" align="center"><?= number_format($paket_rekanan->getField('JUMLAH_KOREKSI'),0,",",".");?></td>
          <td class="td" align="center"><?= number_format($paket_rekanan->getField('NILAI_PENAWARAN'),0,",",".");?></td>
          <!-- <td align="center"></td> -->
        </tr>
        <?php $no++; } ?> 
      </table> 
    </div> 
  </div>

  <div class="isi">
    <h3>Kesimpulan:</h3>
    <p>Berdasarkan hasil e-Reverse Auction maka kami berkesimpulan dan memutuskan untuk mengusulkan peserta yang memenuhi syarat untuk ditunjuk sebagai pemenang adalah:</p>
  </div>
  <div class="isi" style="margin-bottom:20px"> 
    <table border="0">  
      <?php 
      $no=1;
        while($paket_rekanan_pemenang->nextRow())
        {
       ?>
      <tr>
        <td align="left" valign="top" width="30%"> Nama Perusahaan </td>
        <td align="center" valign="top" width="2%">:</td>
        <td align="left" valign="top" width="68%"> <?=$paket_rekanan_pemenang->getField('REKANAN_NAMA');?></td> 
      </tr> 
      <tr>
        <td align="left" valign="top" width="30%"> Harga Penawaran Awal  </td>
        <td align="center" valign="top" width="2%">:</td>
        <td align="left" valign="top" width="68%"> Rp. <?= number_format($paket_rekanan_pemenang->getField('JUMLAH_KOREKSI'),2,",",".");?> </td> 
      </tr>   
      <tr>
        <td align="left" valign="top" width="30%"> Harga Penawaran setelah e-Reverse Auction  </td>
        <td align="center" valign="top" width="2%">:</td>
        <td align="left" valign="top" width="68%"> Rp. <?= number_format($paket_rekanan_pemenang->getField('NILAI_PENAWARAN'),2,",",".");?> </td> 
      </tr>   
      <?php } ?>
    </table>
  </div> 
  <div class="isi">
    <p>Demikian Berita Acara Hasil e-Reverse Auction ini dibuat dengan penuh rasa tanggung jawab untuk dipergunakan dalam penetapan pemenang sebagaimana mestinya.</p>
  </div>

  <div class="area-dokumen" style="margin-bottom:30px">
    <table class="table" > 
      <tr class="tr-bc">
        <td class="td" align="center" width="5%">No</td>
        <td class="td" align="center" width="50%">Nama</td>
        <td class="td" align="center" width="20%">Jabatan</td>
        <td class="td" align="center" width="25%">TTD</td>
      </tr>
      <?php 
      $jumlahRow = array('1','2','3','4','5','6','7','8','9');
      $no=1;
        foreach ($jumlahRow as $key => $value) {
       ?>
      <tr class="judul-kolom">
        <td class="td" align="center" height="60px"><?=$no?></td> 
        <td class="td" align="center"></td>
        <td class="td" align="center"></td>
        <td class="td" align="center"></td>
      </tr>
      <?php $no++; } ?> 
    </table> 
  </div> 

  <div class="area-dokumen" style="margin-bottom:30px">
    <table class="table" > 
      <tr class="tr-bc">
        <td class="td" align="center" width="5%">No</td>
        <td class="td" align="center" width="70%">Nama Perusahaan</td>
        <td class="td" align="center" width="25%">TTD</td>
      </tr>
      <?php 
      $no=1;
        while($paket_rekanan3->nextRow())
        {
       ?>
      <tr class="judul-kolom">
        <td class="td" align="center" height="60px"><?=$no?></td>
        <td class="td" align="left">
            <?=$paket_rekanan3->getField('REKANAN_NAMA');?>
        </td> 
        <td class="td" align="center"></td>
      </tr>
      <?php $no++; } ?> 
    </table> 
  </div> 

  <pagebreak>

  <div class="area-dokumen" style="margin:30px 0;">
    <?php 
    $no=1;
    while($paket_rekanan4->nextRow())
    {
      $this->load->model("ChatShoutbox");
      $getChatShoutbox = new ChatShoutbox();

      $getChatShoutbox->selectByParams(array("PAKET_ID" => $reqId, "JENIS_CHAT" => '2' ,"REKANAN_ID" => $paket_rekanan4->getField('REKANAN_ID')), -1, -1, "");
    ?>
    <h4>Chat Dengan <?=$paket_rekanan4->getField('REKANAN_NAMA');?></h4>
    <table class="table">
      <?php 
      while($getChatShoutbox->nextRow())
      { ?>
      <tr class="judul-kolom">
        <td class="td" width="35%" style="font-size: 11px"><?=$getChatShoutbox->getField('NAMA');?><br><small style="font-size: 9px">(<?=$getChatShoutbox->getField('WAKTU');?>)</small></td>
        <td class="td" style="font-size: 11px"><?=$getChatShoutbox->getField('PESAN');?></td>
      </tr>
      <?php 
      } ?>
    </table>
    <?php 
    } ?>
  </div>

  <pagebreak>

  <div class="area-dokumen" style="margin-bottom:30px; font-size: 11px">
    <h4>Logs Penawaran</h4>
    <?php 
    $filepath = 'logs/Auction-log_' .$reqId. '.txt'; 

    if(file_exists($filepath))
    {
      $fh = fopen($filepath,'r');
      while ($line = fgets($fh)) {
        $line = str_replace('-----------------------------------------------------------------------------------------','<br><br>',$line);
        echo($line);
      }
      fclose($fh); 
    } else {
      echo ".:: Tidak ada data ::.";
    }
    ?>
  </div> 


   

  <div class="nomor-oe">
    <div class="data" style="font-size:10px; font-style:italic; margin-top:20px">
       <?= SYSTEM_NAME_PT ?> menyatakan dokumen ini SAH dan dikeluarkan oleh sistem <?= SYSTEM_NAME ?>
       <?php  if($paketInfo->bahasa == "EN") echo "<br><br><em><?= SYSTEM_NAME_PT ?> states that this document is valid and published by e-procurement system.</em>"; ?>
    </div>
  </div> 

</body>
</html>