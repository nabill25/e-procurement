<?php
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 * 
 */
$this->load->library("kauth");  $userLogin = new kauth(); 
$this->load->library("paketinfo"); $paketInfo = new paketinfo(); 
$this->load->model("Metode"); 
include_once("functions/string.func.php");
include_once("functions/default.func.php");
include_once("functions/date.func.php");
$this->load->library("AES"); 

// Unit Kerja
$this->load->library("libbreadcrumb"); 
$unitkerjaid = $this->input->get("unitkerjaid");
// End Unit Kerja

$aes = new AES();
include_once("lib/phpqrcode/qrlib.php");
$PNG_TEMP_DIR = 'uploads/';
$PNG_TEMP_DIR_LOGO = '';
$filenamelogo = $PNG_TEMP_DIR_LOGO.'logo.png';

$metode = new Metode();

$reqId = $this->input->get("reqId");

$paketInfo->getPaket($reqId);
$reqMetodeLelangId = $paketInfo->metode_lelang_id;

$reqExistData = $metode->getCountByParams(array("PAKET_ID" => $reqId));
$metode->selectByParams(array(), -1, -1, $reqId);


if($reqMetodeLelangId == 1 || $reqMetodeLelangId == 3)
{
    $tempPublishTanggalJam = $paketInfo->publish_paket_tanggal;
    $arrPublishTanggalJam = explode(" ", $tempPublishTanggalJam);
    $arrPublishJamMenit = explode(":", $arrPublishTanggalJam[1]);
    $tempPublishTanggal = $arrPublishTanggalJam[0];
    $tempPublishJam = $arrPublishJamMenit[0];
    $tempPublishMenit = $arrPublishJamMenit[1];
}

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

	
</head>

<body>
  <br>
  <div class="logo"><img src="images/<?= $this->libbreadcrumb->cetakcopyrightlogo($unitkerjaid) ?>" height="75" /></div>
  <div class="judul">
  	JADWAL PEKERJAAN<br>
  
  <div class="pekerjaan">
   <?php  if($paketInfo->bahasa == "EN") echo "/ <em>WORKS</em>"; ?><br />
  <?=strtoupper($paketInfo->nama)?>
  </div><br> 

  <div class="area-dokumen">
    <table class="table font13"> 
      <tbody>
          <tr class="tr">
            <th rowspan="3" valign="middle" class="tdno">No</th>
            <th rowspan="3" valign="middle" class="td">Tahapan <br><?= $paketInfo->metode_lelang_nama ?></th>
            <th colspan="4" valign="top" style="text-align: center" class="td"> Waktu Pelaksanaan </th>
          </tr>
          <tr class="tr">
            <th colspan="2" style=" text-align: center" class="td"> Mulai </th>
            <th colspan="2" style=" text-align: center" class="td">Selesai</th>
            </tr>
          <tr class="tr"> 
            <th style=" text-align: center" class="td"> Tanggal </th>
            <th style=" text-align: center" class="td"> Jam </th>
            <th style=" text-align: center" class="td"> Tanggal </th>
            <th style=" text-align: center" class="td"> Jam </th>
          </tr>

          <?php 
          $i=1; $no=1; $stat = ''; $stat_m = '';
          while($metode->nextRow())
          {
              if($stat == '') $comma = '';    else    $comma = ', ';                              
              $stat .= $comma."#reqJamSelesai$i, #reqJamMulai$i";
              
              if($stat_m == '') $comma = '';  else    $comma = ', ';                              
              $stat_m .= $comma."#reqMenitSelesai$i, #reqMenitMulai$i";
              
              $disabledTanggalAwal = $metode->getField("TANGGAL_AWAL_DISABLED");
              $triggerTanggalAkhir = $metode->getField("TANGGAL_AKHIR_TRIGGER");
          ?>
              <tr valign="top">
                <td class="tdno" style="width: 6% !important"><?=$no?>.</td>
                  <td class="td">
                      <?php 
                      if($paketInfo->bidding == "1")
                          $namaJadwal = str_replace("Negosiasi", "e-Reverse Auction", $metode->getField("NAMA"));
                      else
                          $namaJadwal = $metode->getField("NAMA");
                      ?>

                      <?=$namaJadwal?>
                      <?php 
                      $notif = "";
                      $notifikasi = $metode->getField("NOTIFIKASI");
                      if($notifikasi == "PENAWARAN")
                      {
                          if($metode->getField("HADIR_CENTANG") == 1)
                              $notif = "Pemasukan dokumen penawaran melalui offline";
                          else    
                              $notif = "Pemasukan dokumen penawaran melalui online";
                      }
                      if($notifikasi == "")
                      {}
                      else
                      {
                      ?>        
                      <br>                                 
                      <span class="font11">(<?=$notif?>)</span>
                      <?php 
                      }
                      ?>
                  </td>  
                  <td class="td">                                    
                      <?=datetimeToPage($metode->getField("TANGGAL_AWAL"), "date")?> 
                  </td>
                  <td class="td ">
                      <?php 
                      $arrJamAwal = explode(":", $metode->getField("JAM_AWAL"));
                      ?>
                      <?=$arrJamAwal[0]?> 
                      : 
                      <?=$arrJamAwal[1]?> 
                  </td>
                  <td class="td">
                      <?=datetimeToPage($metode->getField("TANGGAL_AKHIR"), "date")?> 
                  </td>
                      <?php 
                      $arrJamAkhir = explode(":", $metode->getField("JAM_AKHIR"));
                      ?>                                      
                  <td class="td">
                      <?=$arrJamAkhir[0]?> 
                      
                      <?= $arrJamAkhir[1] ? ' : '.$arrJamAkhir[1] : '' ?> 
                      <?php 
                      if($paketInfo->bidding == "1")
                          $namaJadwal = str_replace("Negosiasi", "e-Auction", $metode->getField("NAMA"));
                      else
                          $namaJadwal = $metode->getField("NAMA");
                      ?>
                  </td>
              </tr> 
          <?php 
              $i++;
              $no++;
          }
          ?>
      </tbody>
    </table>   
  </div>

<p></p>
 
<div class="nomor-oe">
  <div class="data" style="font-size:10px; font-style:italic">
       <?= $this->libbreadcrumb->cetakcopyright($unitkerjaid) ?>
       <?php if($paketInfo->bahasa == "EN") echo "<br><br><em>".SYSTEM_SAH_EN."</em>"; ?>
  </div>
</div>
</body>
</html>
