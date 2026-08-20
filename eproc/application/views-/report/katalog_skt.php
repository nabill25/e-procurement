<?php
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 * 
 */
/* INCLUDE FILE */
$this->load->library("kauth");  $userLogin = new kauth();  
$this->load->model("Rekanan");
include_once("functions/date.func.php");
include_once("functions/default.func.php");
include_once("functions/string.func.php"); 
include_once("lib/phpqrcode/qrlib.php");

$reqId = httpFilterGet("reqId");
$reqKode = httpFilterGet("reqKode"); 
/* create objects */   

$this->load->model("Katalog");
$this->load->model("Rekanan");

$rekanan = new Rekanan();
$rekanan->selectByParams(array("A.KODE"=>$reqKode, "A.REKANAN_ID"=>$reqId),-1,-1);
$rekanan->firstRow();
$reqId = $rekanan->getField("REKANAN_ID");
$tempKualifikasi = $rekanan->getField("REKANAN_KUALIFIKASI");
$reqRekananTipeId = $rekanan->getField("REKANAN_TIPE_ID");
$tempMail = $rekanan->getField("EMAIL");
$tempWebsite = $rekanan->getField("WEBSITE");
$tempKontakPerson = $rekanan->getField("KONTAK_PERSON");
$tempKontakPersonHp = $rekanan->getField("KONTAK_PERSON_HP");
$tempFax = $rekanan->getField("FAX_KODE").$rekanan->getField("FAX");
$tempTelepon = $rekanan->getField("TELEPON_KODE").$rekanan->getField("TELEPON");
$tempKota = $rekanan->getField("KOTA");
$tempAlamat = $rekanan->getField("ALAMAT");
$tempPKPTanggal = getFormattedDate($rekanan->getField("PKP_TANGGAL"));
$tempLinkFileTempPKP= $rekanan->getField("PKP_FILE");
$tempLinkFileTempNPWP= $rekanan->getField("NPWP_FILE");
$tempStatus = $rekanan->getField("STATUS_CP");
$tempNPWP = $rekanan->getField("NPWP");
$tempPKP = $rekanan->getField("PKP");
$tempNama= $rekanan->getField("NAMA");
$tempRekananNama = $rekanan->getField("REKANAN_NAMA");

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
</head>

<body> 

  <div class="logo"><img src="images/<?= basename(SYSTEM_LOGO_CETAK) ?>" height="75" /></div>
  <div class="judul">
    SURAT KETERANGAN TERDAFTAR (SKT) KATALOG<br>
  </div>
  <div class="nomor">PENYEDIA <?= SYSTEM_NAME_PT ?></div><br><br>

    <div class="area-dokumen">
  <p> Berdasarkan hasil proses Verifikasi, dengan ini dinyatakan sebagai berikut: </p>
      <table>
        <tr>
            <td style="width: 25%">Nama Perusahaan</td>
            <td>: <?=$tempNama ? $tempNama : '-'?></td>
        </tr>
        <tr>
            <td>Status Kantor</td>
            <td>: <?=$tempStatus ? $tempStatus : '-'?></td>
        </tr>
        <tr>
            <td valign="top">Alamat</td>
            <td>: <?=$tempAlamat ? $tempAlamat : '-'?>
                  <br> &nbsp;&nbsp;<?=$tempKota ? $tempKota : '-'?>
                  <br> &nbsp;&nbsp;<?=$rekanan->getField("REGION") ? $rekanan->getField("REGION") : '-'?>
                  <br> &nbsp;&nbsp;<?=$rekanan->getField("KODEPOS") ? $rekanan->getField("KODEPOS") : '-'?>
            </td>
        </tr>
        <!-- <tr>
            <td>Kota</td>
            <td>: <?=$tempKota ? $tempKota : '-'?></td>
        </tr>
        <tr>
            <td>Provinsi</td>
            <td>: <?=$rekanan->getField("REGION") ? $rekanan->getField("REGION") : '-'?></td>
        </tr> 
        <tr>
            <td>Kodepos</td>
            <td>: <?=$rekanan->getField("KODEPOS") ? $rekanan->getField("KODEPOS") : '-'?></td>
        </tr>-->
        <tr>
            <td>No. telepon</td>
            <td>: 
              <?=$tempTelepon ? $tempTelepon : '-'?> / Fax: <?=$tempFax ? $tempFax : '-'?>
            </td>
        </tr>
        <!-- <tr>
            <td>No. Fax</td>
            <td>: <?=$tempFax ? $tempFax : '-'?></td>
        </tr> -->
        <tr>
            <td>Kontak Person</td>
            <td>: 
              <?=$tempKontakPerson ? $tempKontakPerson : '-'?>  / HP: <?=$tempKontakPersonHp ? $tempKontakPersonHp : '-'?>
            </td>
        </tr>
        <!-- <tr>
            <td>HP</td>
            <td>: <?=$tempKontakPersonHp?>  </td>
        </tr> -->
        <tr>
            <td>E-mail</td>
            <td>: <?=$tempMail ? $tempMail : '-'?></td>
        </tr>
        <tr>
            <td>Website</td>
            <td>: <?=$tempWebsite ? $tempWebsite : '-'?> </td>
        </tr> 
      </table> 
    </div>

     <p style="text-align: justify;margin-top: 15px">
      Dengan ketentuan bahwa data vendor tersebut adalah benar Perusahaan saudara dalam <?= SYSTEM_NAME.' '.SYSTEM_NAME_PT ?>, setelah proses verifikasi data Perusahaan saudara dapat mengikuti kegiatan pengadaan barang/jasa pada <?= SYSTEM_NAME.' '.SYSTEM_NAME_PT ?> selama tidak ada salah satu dari dokumen diatas dan pendukung yang habis masa berlakunya dan/atau perusahaan saudara tidak masuk dalam daftar hitam serta perusahaan saudara memiliki penilaian kinerja terhadap kegiatan pengadaan barang/jasa tidak masuk dalam kategori penilaian buruk.<br>

      Segala perubahan data setelah disahkan perusahaan saudara sebagai mitra kami akan mempengaruhi proses kualifikasi kegiatan pengadaan barang/jasa, pelaksana pengadaan barang/jasa berhak menolak bilamana terdapat data perusahaan saudara yang tidak sesuai.<br>

      Surat Keterangan Terdaftar ini tidak mempunyai masa berlaku dan menjadi tidak berlaku bila ada dokumen yang sudah kadaluarsa dan tidak diperbarui oleh Penyedia Barang/Jasa.<br>
      Demikian, atas perhatiannya diucapkan terima kasih.<br>
      <div class="logo" style="font-size: 11px; text-align: center"> 
        Dicetak dari <?= SYSTEM_NAME.' '.SYSTEM_NAME_PT ?> tanggal <?= getFormattedDate(date('Y-m-d')) ?>.<br>
      </div>
      <div class="logo"> 
        Verifikator.<br>
        <?= SYSTEM_NAME_PT ?>
      </div>
    </p>

    <div class="nomor-oe">
      <div class="data" style="font-size:10px; font-style:italic; text-align: center;">
         <?= SYSTEM_SAH ?>
         <?php if($paketInfo->bahasa == "EN") echo "<br><br><em>".SYSTEM_SAH_EN."</em>"; ?>
      </div>
    </div>

    <div style="page-break-before:always">&nbsp;</div> 

    <!-- <div class="logo"><img src="images/<?= basename(SYSTEM_LOGO_CETAK) ?>" height="75" /></div> -->
    <div class="judul"> DAFTAR KATALOG<br><br><br>
    </div>

    <div class="area-dokumen" style="border: 1px solid #b7b7b7; padding: 10px 10px 0 10px">  
      <table class="table">    
        <tr class="tr-bc">
          <th class="td">No</th>
          <th class="td">Katalog</th>
          <th class="td">Verifikasi</th>
        </tr>
      <?php
        $katalog_publish = new Katalog();
        $katalog_publish->selectByParamsViewKatalog2(array(), -1, -1, " AND A.REKANAN_ID = '".$reqId."' AND A.PUBLISH = '1'");

        $katalog = new Katalog();
        $katalog->selectByParamsViewKatalog(array(), -1, -1, " AND A.REKANAN_ID = '".$reqId."' ORDER BY A.STATUS DESC ");
        $no=1;
         ?> 
        <?php
          while($katalog->nextRow())
          {
            if ($katalog->getField("PUBLISH") == '1') {
              $checkPublish = '<img src="images/centang-cetak.png" height="15" />';
            } else {
              $checkPublish = '<img src="images/uncentang-cetak.png" height="15" />';
            }
        ?>
            <tr>
              <td class="td" style="width: 2%; text-align: center;"><?=$no;?></td>
              <td class="td" style="width: 83%">
                <?php 
                if ($katalog->getField("NOPRODUKPENYEDIA")) { ?>
                  <small style="font-size: .7rem"><?= $katalog->getField("NOPRODUKPENYEDIA") ?></small><br>
                <?php 
                } ?>
                <?= $katalog->getField("NAMAPRODUK") ?> 
              </td>
              <td class="td" style="width: 6%">
                <?= $checkPublish ?>
              </td>
            </tr>
              <?php  
          $no++;
          } ?>
 
        </table> 

        <div class="logo" style="font-size: 11px; text-align: center"> 
          Dicetak dari <?= SYSTEM_NAME.' '.SYSTEM_NAME_PT ?> tanggal <?= getFormattedDate(date('Y-m-d')) ?>.<br>
          </div>
          <div class="logo"> 
            Verifikator.<br>
            <?= SYSTEM_NAME_PT ?>
          </div>
        </p>

        <div class="nomor-oe">
          <div class="data" style="font-size:10px; font-style:italic; text-align: center;">
             <?= SYSTEM_SAH ?>
             <?php if($paketInfo->bahasa == "EN") echo "<br><br><em>".SYSTEM_SAH_EN."</em>"; ?>
          </div>
        </div>
    </div>   

</body>
</html>
