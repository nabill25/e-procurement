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

$PNG_TEMP_DIR = 'uploads/';
$PNG_TEMP_DIR_BARCODE = 'uploads/vms/barcode/';

$reqId = httpFilterGet("reqId");
$reqKode = httpFilterGet("kode");
$filename = $PNG_TEMP_DIR_BARCODE.$reqId.'_'.$reqKode.'.png';
/* create objects */   

$this->load->model("Rekanan");
$this->load->model("RekananIjinUsaha");
$this->load->model("RekananAkta");
$this->load->model("RekananPengurus");
$this->load->model("RekananBidangUsaha");
$this->load->model("Users");
$this->load->library("KMail");
$this->load->model("RekananSaham");
$this->load->model("RekananSertifikat");
$this->load->model("RekananRekeningKoran");

$rekanan = new Rekanan();
$rekanan_ijin_usaha = new RekananIjinUsaha();
$rekanan_akta = new RekananAkta();
$rekanan_pengurus_komisaris = new RekananPengurus();
$rekanan_pengurus_direksi = new RekananPengurus();
$rekanan_bidang_usaha = new RekananBidangUsaha();
$rekanan_bidang_usaha_sbu = new RekananBidangUsaha();
$FILE_DIR = "uploads/rekanan/";
$FILE_DIR_IJIN_USAHA = "uploads/ijin_usaha/";
$FILE_DIR_LANDASAN_HUKUM = "uploads/landasan_hukum/";
$FILE_DIR_KOMISARIS = "uploads/pemimpin_perusahaan/";
$FILE_DIR_DIREKSI = "uploads/pemimpin_perusahaan/";
$user_login = new Users();
$rekanan_saham  = new RekananSaham();
$rekanan_sertifikat   = new RekananSertifikat();
$rekanan_sertifikat_domisili  = new RekananSertifikat();
$rekanan_sertifikat_tanda_daftar  = new RekananSertifikat();

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
    SURAT KETERANGAN TERDAFTAR (SKT)<br>
  </div>
  <div class="nomor">PENYEDIA <?= SYSTEM_NAME_PT ?></div><br><br>

    <div class="area-dokumen">
  <p> Berdasarkan hasil proses Verifikasi, dengan ini dinyatakan sebagai berikut: </p>
      <table>
        <tr>
            <td style="width: 35%">Nama Perusahaan</td>
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
        <tr>
            <td>Kualifikasi Usaha</td>
            <td>: <?=$tempKualifikasi ? $tempKualifikasi : '-'?></td>
        </tr>
        <tr>
            <td>NPWP</td>
            <td>: <?=$tempNPWP ? $tempNPWP : '-'?></td>
        </tr>
        <tr>
            <td>PKP</td>
            <td>: <?=$tempPKP ? $tempPKP : '-'?></td>
        </tr>
        <tr>
            <td>Tanggal PKP</td>
            <td>: <?=$tempPKPTanggal ? $tempPKPTanggal : '-'?></td>
        </tr>
      </table> 
    </div>

     <p style="text-align: justify;margin-top: 15px">
      Dengan ketentuan bahwa data vendor tersebut adalah benar Perusahaan saudara dalam <?= SYSTEM_NAME.' '.SYSTEM_NAME_PT ?>, setelah proses verifikasi data Perusahaan saudara dapat mengikuti kegiatan pengadaan barang/jasa pada <?= SYSTEM_NAME.' '.SYSTEM_NAME_PT ?> selama tidak ada salah satu dari dokumen diatas dan pendukung yang habis masa berlakunya dan/atau perusahaan saudara tidak masuk dalam daftar hitam serta perusahaan saudara memiliki penilaian kinerja terhadap kegiatan pengadaan barang/jasa tidak masuk dalam kategori penilaian buruk.<br><br>

      Segala perubahan data setelah disahkan perusahaan saudara sebagai mitra kami akan mempengaruhi proses kualifikasi kegiatan pengadaan barang/jasa, pelaksana pengadaan barang/jasa berhak menolak bilamana terdapat data perusahaan saudara yang tidak sesuai.<br><br>

      Surat Keterangan Terdaftar ini tidak mempunyai masa berlaku dan menjadi tidak berlaku bila ada dokumen yang sudah kadaluarsa dan tidak diperbarui oleh Penyedia Barang/Jasa.<br>
      Demikian, atas perhatiannya diucapkan terima kasih.<br>
      <div class="logo" style="font-size: 11px; text-align: center"> 
        Dicetak dari <?= SYSTEM_NAME.' '.SYSTEM_NAME_PT ?> tanggal <?= getFormattedDate(date('Y-m-d')) ?>.<br>
      <?php echo '<img src="'.$PNG_TEMP_DIR_BARCODE.basename($filename).'" style="text-align:center" />'; ?>
      </div>
      <div class="logo"> 
        <!-- Verifikator.<br> -->
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
    <div class="judul">
      CHECKLIST KELENGKAPAN<br>
    </div>
    <div class="nomor">DATA PENYEDIA <?= SYSTEM_NAME_PT ?></div><br>

    <div class="area-dokumen" style="border: 1px solid #b7b7b7; padding: 10px 10px 0 10px">  
      <table class="table">    
                  <?php
                    $jumlahUncentang = 0;
                    $rekanan = new Rekanan();
                    $rekanan_keuangan = new Rekanan();
                    $rekanan_perpajakan = new Rekanan();
                    $rekanan_teknis = new Rekanan(); 
                      $rekanan->selectByParamsKonfirmasiDataAdmin($reqId);
                      $rekanan_keuangan->selectByParamsKonfirmasiDataKeuangan($reqId);
                      $rekanan_perpajakan->selectByParamsKonfirmasiDataPerpajakan($reqId);
                      $rekanan_teknis->selectByParamsKonfirmasiDataTeknis($reqId);
                    $no=1;
                     ?>
                    <tr class="tr-bc">
                      <td class="td" colspan="3" >Data Administrasi</td>
                    </tr> 
                    <?php
                      while($rekanan->nextRow())
                      {
                    ?>
                        <tr>
                          <td class="td" style="width: 2%; text-align: center;"><?=$no;?></td>
                          <td class="td" style="width: 83%">
                            <?=$rekanan->getField("NAMA") ?>
                            <?php 
                            if ($rekanan->getField("WAJIB") == '*') {
                               echo '<span class="color:red">'.$rekanan->getField("WAJIB").'</span>';
                            } else {
                              echo "";
                            } ?> 
                          </td>
                          <td class="td" style="width: 6%">
                            <img src="images/<?= basename($rekanan->getField("SIMBOL").'-cetak.png') ?>" height="15" />
                          </td>
                        </tr>
                          <?php 
                       if($rekanan->getField("SIMBOL") == "uncentang" && $rekanan->getField("WAJIB") == '*')
                        $jumlahUncentang++;
                      $no++;
                      } ?>
 
                    <tr class="tr-bc">
                      <td class="td" colspan="3" >Data Keuangan</td>
                    </tr> 
                    <?php
                    $no2=1;
                      while($rekanan_keuangan->nextRow())
                      {
                    ?>
                        <tr>
                          <td class="td" style="width: 2%; text-align: center;"><?=$no2;?></td>
                          <td class="td" style="width: 83%">
                            <?=$rekanan_keuangan->getField("NAMA") ?>
                           <?php 
                            if ($rekanan_keuangan->getField("WAJIB") == '*') {
                               echo '<span class="color:red">'.$rekanan_keuangan->getField("WAJIB").'</span>';
                            } else {
                              echo "";
                            } ?> 
                          </td>
                          <td class="td" style="width: 6%">
                            <img src="images/<?= basename($rekanan_keuangan->getField("SIMBOL").'-cetak.png') ?>" height="15" />
                          </td>
                        </tr>
                          <?php 
                       if($rekanan_keuangan->getField("SIMBOL") == "uncentang" && $rekanan_keuangan->getField("WAJIB") == '*')
                        $jumlahUncentang++;
                      $no2++;
                      } ?>
 
                    <tr class="tr-bc">
                      <td class="td" colspan="3" >Data Perpajakan</td>
                    </tr> 
                    <?php
                    $no3=1;
                      while($rekanan_perpajakan->nextRow())
                      {
                    ?>
                        <tr>
                          <td class="td" style="width: 2%; text-align: center;"><?=$no3;?></td>
                          <td class="td" style="width: 83%">
                            <?=$rekanan_perpajakan->getField("NAMA") ?>
                             <?php 
                            if ($rekanan_perpajakan->getField("WAJIB") == '*') {
                               echo '<span class="color:red">'.$rekanan_perpajakan->getField("WAJIB").'</span>';
                            } else {
                              echo "";
                            } ?> 
                          </td>
                          <td class="td" style="width: 6%">
                            <img src="images/<?= basename($rekanan_perpajakan->getField("SIMBOL").'-cetak.png') ?>" height="15" />
                          </td>
                        </tr>
                          <?php 
                       if($rekanan_perpajakan->getField("SIMBOL") == "uncentang" && $rekanan_perpajakan->getField("WAJIB") == '*')
                        $jumlahUncentang++;
                      $no3++;
                      } ?>
 
                   <tr class="tr-bc">
                      <td class="td" colspan="3" >Data Teknis</td>
                    </tr> 
                    <?php 
                    $no4=1;
                      while($rekanan_teknis->nextRow())
                      {
                    ?>
                        <tr>
                          <td class="td" style="width: 2%; text-align: center;"><?=$no4;?></td>
                          <td class="td" style="width: 83%">
                            <?=$rekanan_teknis->getField("NAMA") ?>
                             <?php 
                            if ($rekanan_teknis ->getField("WAJIB") == '*') {
                               echo '<span class="color:red">'.$rekanan_teknis  ->getField("WAJIB").'</span>';
                            } else {
                              echo "";
                            } ?> 
                          </td>
                          <td class="td" style="width: 6%">
                            <img src="images/<?= basename($rekanan_teknis->getField("SIMBOL").'-cetak.png') ?>" height="15" />
                          </td>
                        </tr>
                          <?php 
                       if($rekanan_teknis->getField("SIMBOL") == "uncentang" && $rekanan_teknis->getField("WAJIB") == '*')
                        $jumlahUncentang++;
                      $no4++;
                      } ?>
                
     
        </table>
              <?php 
              if ($jumlahUncentang > 0) {
                 echo '<div>
                        <b> Kurang '.$jumlahUncentang.' data belum dilengkapi (tanda * data wajib diisi)  </b> 
                      </div><br>';
              } ?>
    </div>   

</body>
</html>
