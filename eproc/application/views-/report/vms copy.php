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

$reqId = httpFilterGet("reqId");
$reqKode = httpFilterGet("kode");
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

     <p style="text-align: justify;margin-top: 20px">
      Dengan ketentuan bahwa data vendor tersebut adalah benar Perusahaan saudara dalam <?= SYSTEM_NAME.' '.SYSTEM_NAME_PT ?>, setelah proses verifikasi data Perusahaan saudara dapat mengikuti kegiatan pengadaan barang/jasa pada <?= SYSTEM_NAME.' '.SYSTEM_NAME_PT ?> selama tidak ada salah satu dari dokumen diatas dan pendukung yang habis masa berlakunya dan/atau perusahaan saudara tidak masuk dalam daftar hitam serta perusahaan saudara memiliki penilaian kinerja terhadap kegiatan pengadaan barang/jasa tidak masuk dalam kategori penilaian buruk.<br><br>

      Segala perubahan data setelah disahkan perusahaan saudara sebagai mitra kami akan mempengaruhi proses kualifikasi kegiatan pengadaan barang/jasa, pelaksana pengadaan barang/jasa berhak menolak bilamana terdapat data perusahaan saudara yang tidak sesuai.<br><br>

      Surat Keterangan Terdaftar ini tidak mempunyai masa berlaku dan menjadi tidak berlaku bila ada dokumen yang sudah kadaluarsa dan tidak diperbarui oleh Penyedia Barang/Jasa.<br>
      Demikian, atas perhatiannya diucapkan terima kasih.<br><br>
      <div class="logo" style="font-size: 11px"> 
        Dicetak dari <?= SYSTEM_NAME.' '.SYSTEM_NAME_PT ?> tanggal <?= getFormattedDate(date('Y-m-d')) ?>.<br><br><br><br><br>
      </div>
      <div class="logo"> 
        Verifikator<br>
        <?= SYSTEM_NAME_PT ?>
      </div>
    </p>

    <div class="nomor-oe">
      <div class="data" style="font-size:10px; font-style:italic; text-align: center;">
         <?= SYSTEM_SAH ?>
         <? if($paketInfo->bahasa == "EN") echo "<br><br><em>".SYSTEM_SAH_EN."</em>"; ?>
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
                          <?
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
                          <?
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
                          <?
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
                          <?
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

    <div style="page-break-before:always">&nbsp;</div> 

    <!-- <div class="logo"><img src="images/<?= basename(SYSTEM_LOGO_CETAK) ?>" height="75" /></div> -->
      <div class="judul">
        LAMPIRAN<br>
      </div>
      <div class="nomor">DATA PENYEDIA <?= SYSTEM_NAME_PT ?></div><br>

    <?
      $rekanan_ijin_usaha->selectByParams(array("REKANAN_ID" => $reqId));
      $rekanan_ijin_usaha->firstRow();
      $tempNomor = $rekanan_ijin_usaha->getField("NO_IJIN");
      $tempTanggalIjin = getFormattedDate($rekanan_ijin_usaha->getField("TANGGAL"));
      $tempTanggalBerakhir = getFormattedDate($rekanan_ijin_usaha->getField("TANGGAL_BERAKHIR"));
      $tempLinkFileTempIjinSiup = $rekanan_ijin_usaha->getField("PATH_FILE");
      $tempBidang = $rekanan_ijin_usaha->getField("IJIN_USAHA");
      $tempInstansi = $rekanan_ijin_usaha->getField("INSTANSI");
    ?>
    <?php 
    if ($tempNomor) { ?>
    <br>
    <div class="area-dokumen" style="border: 1px solid #b7b7b7; padding: 10px 10px 0 10px"> 
        <b style="font-weight: bold; text-decoration: underline;"><u>Ijin Usaha / OSS (SIUP)</u></b>
        <table class="table" style="margin-bottom: 0px">   
          <tr> 
            <td style="width: 30%">Nomor ijin</td>
            <td>: <?=$tempNomor?> </td> 
          </tr>
          <tr>  
            <td>Tanggal ijin</td>
            <td>: <?=$tempTanggalIjin?></td>
          </tr>
          <tr>  
            <td>Tanggal berakhir</td>
            <td>: <?=$tempTanggalBerakhir?></td>
          </tr>
          <tr>  
            <td>Instansi pemberi ijin</td>
            <td>: <?=$tempInstansi?></td>
          </tr>
        </table> 
        <b style="font-weight: bold; text-decoration: underline;"><u>Bidang Usaha</u></b>
        <table class="table">   
          <tr class="tr-bc">
            <td class="td" style="width: 7%">No</td>
            <td class="td">Bidang Usaha</td>
          </tr>
          <?
          $rekanan_bidang_usaha->selectByParamsMonitoring(array("REKANAN_ID" =>$reqId),-1,-1, " AND IJIN_USAHA_ID NOT IN(99)");
          $no = 1;
          while($rekanan_bidang_usaha->nextRow())
          {
          ?>
            <tr>
              <td class="td"><?=$no?></td>
              <td class="td"><?=$rekanan_bidang_usaha->getField("NAMA")?></td>
            </tr>
          <?
            $no++;
          } ?>
        </table> 
    </div> 

    <?php 
    } ?>    

    <?
      $rekanan_akta->selectByParams(array("REKANAN_ID"=>$reqId, "AKTA_TYPE_ID" => 1),-1,-1);
      $rekanan_akta->firstRow();
      $tempAktaTypeId = $rekanan_akta->getField("AKTA_TYPE_ID");
      $tempNomorLandasan = $rekanan_akta->getField("NOMOR");
      $tempTanggalLandasan = getFormattedDateJson($rekanan_akta->getField("TANGGAL"));
      $tempNotarisLandasan = $rekanan_akta->getField("NOTARIS");
      $tempLinkFileTempAktaPendirian = $rekanan_akta->getField("PATH_FILE");
    ?>

    <div class="area-dokumen" style="border: 1px solid #b7b7b7; padding: 10px 10px 10px 10px; margin-top: 10px"> 
        <b style="font-weight: bold; text-decoration: underline;"><u>Akta Pendirian</u></b>
        <table class="table" style="margin-bottom: 0px">   
          <tbody>
            <tr> 
              <td style="width: 30%">Nomor Akta </td>
              <td>: <?=$tempNomorLandasan?></td>
            </tr>
            <tr> 
              <td>Tanggal</td>
              <td>: <?=$tempTanggalLandasan?></td>
            </tr>
            <tr> 
              <td>Nama Notaris</td>
              <td>: <?=$tempNotarisLandasan?></td>
            </tr> 
          </tbody>
        </table>
      </div> 
    </div>    
    <?
      $rekanan_akta_perubahan = new RekananAkta();
      $reqHitung = $rekanan_akta_perubahan->getCountByParams(array("REKANAN_ID"=>$reqId, "AKTA_TYPE_ID" => "2"));

      $rekanan_akta_perubahan_terakhir = new RekananAkta();
      $rekanan_akta_perubahan_terakhir->selectByParams(array("REKANAN_ID"=>$reqId, "AKTA_TYPE_ID" => "2"),-1,-1);
      $rekanan_akta_perubahan_terakhir->firstRow();
    if($reqHitung >0)
    {
    ?>
    <div class="area-dokumen" style="border: 1px solid #b7b7b7; padding: 10px 10px 10px 10px; margin-top: 10px"> 
        <b style="font-weight: bold; text-decoration: underline;"><u>Akta Perubahan Terakhir</u></b>
        <table class="table" style="margin-bottom: 0px">   
          <tbody>
            <tr> 
              <td style="width: 30%">Nomor Akta</td>
              <td>: <?=$rekanan_akta_perubahan_terakhir->getField("NOMOR");?></td>
            </tr>
            <tr>  
              <td>TanggaL</td>
              <td>: <?=getFormattedDate($rekanan_akta_perubahan_terakhir->getField("TANGGAL"));?></td>
            </tr>
            <tr>  
              <td>Nama Notaris</td>
              <td>: <?=$rekanan_akta_perubahan_terakhir->getField("NOTARIS");?> </td>
            </tr>
          </tbody>
        </table>
      </div> 
    </div>   
    <?
    }
    else
    {}
    ?>

    <?
      $rekanan_sertifikat->selectByParams(array("REKANAN_ID"=>$reqId, "SERTIFIKAT_TIPE"=>"PENGESAHAN_BADAN_USAHA"),-1,-1);
      $rekanan_sertifikat->firstRow();
      $reqPengesahanSertifikatId = $rekanan_sertifikat->getField("REKANAN_SERTIFIKAT_ID");
      $reqNomorPengesahan = $rekanan_sertifikat->getField("NOMOR");
      $reqTanggalPengesahan = ($rekanan_sertifikat->getField("TANGGAL"));
      $reqTanggalBerlakuPengesahan = ($rekanan_sertifikat->getField("BERLAKU"));
      $reqLinkFilePengesahanTempNama = $rekanan_sertifikat->getField("NAMA_FILE");
      $reqLinkFilePengesahanTemp= $rekanan_sertifikat->getField("PATH_FILE");
      $reqLinkFilePengesahanTempTipe= $rekanan_sertifikat->getField("TIPE");
      $reqLinkFilePengesahanTempUkuran= $rekanan_sertifikat->getField("UKURAN");
    ?>
    <?php 
    if ($reqNomorPengesahan) { ?>
    <div class="area-dokumen" style="border: 1px solid #b7b7b7; padding: 10px 10px 10px 10px; margin-top: 10px"> 
      <b style="font-weight: bold; text-decoration: underline;"><u>Pengesaahn Badan Hukum</u></b>
      <table class="table" style="margin-bottom: 0px">     
        <tr> 
            <td style="width: 30%">Nomor Sertifikat :
            </td>
            <td>
                <?=$reqNomorPengesahan;?>
            </td>
        </tr>
        <tr> 
            <td>Tanggal :</td>
            <td>
               <?=getFormattedDate($reqTanggalPengesahan);?>
            </td>
        </tr>
        <tr> 
            <td>Berlaku :</td>
            <td>
                <?=getFormattedDate($reqTanggalBerlakuPengesahan);?>
            </td>
        </tr>
      </table>  
    </div> 
    <?php 
    } ?> 

    <?
      $rekanan_sertifikat_domisili->selectByParams(array("REKANAN_ID"=>$reqId, "SERTIFIKAT_TIPE"=>"SURAT_DOMISILI"),-1,-1);
      $rekanan_sertifikat_domisili->firstRow();
      $reqDomisiliId = $rekanan_sertifikat_domisili->getField("REKANAN_SERTIFIKAT_ID");
      $reqNomorDomisili = $rekanan_sertifikat_domisili->getField("NOMOR");
      $reqTanggalDomisili = ($rekanan_sertifikat_domisili->getField("TANGGAL"));
      $reqTanggalBerlakuDomisili = ($rekanan_sertifikat_domisili->getField("BERLAKU"));
      $reqLinkFileDomisiliTempNama = $rekanan_sertifikat_domisili->getField("NAMA_FILE");
      $reqLinkFileDomisiliTemp= $rekanan_sertifikat_domisili->getField("PATH_FILE");
      $reqLinkFileDomisiliTempTipe= $rekanan_sertifikat_domisili->getField("TIPE");
      $reqLinkFileDomisiliTempUkuran= $rekanan_sertifikat_domisili->getField("UKURAN");
    ?>

    <?php 
    if ($reqNomorDomisili) { ?>
    <div class="area-dokumen" style="border: 1px solid #b7b7b7; padding: 10px 10px 10px 10px; margin-top: 10px"> 
      <b style="font-weight: bold; text-decoration: underline;"><u>Surat Domisili</u></b>
      <table class="table" style="margin-bottom: 0px">     
          <tr> 
            <td style="width: 20%">Nomor Sertifikat </td>
            <td>: <?=$reqNomorDomisili;?></td>
          </tr>
          <tr> 
            <td>Tanggal</td>
            <td>: <?=getFormattedDate($reqTanggalDomisili);?> </td>
          </tr>
          <tr> 
            <td>Berlaku</td>
            <td>: <?=getFormattedDate($reqTanggalBerlakuDomisili);?> </td>
          </tr>
      </table>  
    </div> 
    <?php 
    } ?>  

    <?
      $rekanan_sertifikat_tanda_daftar->selectByParams(array("REKANAN_ID"=>$reqId, "SERTIFIKAT_TIPE"=>"TANDA_DAFTAR_PERUSAHAAN"),-1,-1);
      $rekanan_sertifikat_tanda_daftar->firstRow();
      $reqTandaDaftarId = $rekanan_sertifikat_tanda_daftar->getField("REKANAN_SERTIFIKAT_ID");
      $reqNomorTandaDaftar = $rekanan_sertifikat_tanda_daftar->getField("NOMOR");
      $reqTanggalTandaDaftar = ($rekanan_sertifikat_tanda_daftar->getField("TANGGAL"));
      $reqTanggalBerlakuTandaDaftar = ($rekanan_sertifikat_tanda_daftar->getField("BERLAKU"));
      $reqLinkFileTandaDaftarTempNama = $rekanan_sertifikat_tanda_daftar->getField("NAMA_FILE");
      $reqLinkFileTandaDaftarTemp= $rekanan_sertifikat_tanda_daftar->getField("PATH_FILE");
      $reqLinkFileTandaDaftarTempTipe= $rekanan_sertifikat_tanda_daftar->getField("TIPE");
      $reqLinkFileTandaDaftarTempUkuran= $rekanan_sertifikat_tanda_daftar->getField("UKURAN");
    ?>

    <?php 
    if ($reqNomorTandaDaftar) { ?>
    <div class="area-dokumen" style="border: 1px solid #b7b7b7; padding: 10px 10px 10px 10px; margin-top: 10px"> 
      <b style="font-weight: bold; text-decoration: underline;"><u>Tanda Daftar Perusahaan</u></b>
      <table class="table" style="margin-bottom: 0px">     
          <tr> 
            <td style="width: 30%">Nomor Sertifikat </td>
            <td>: <?=$reqNomorTandaDaftar;?> </td>
          </tr>
          <tr> 
            <td>Tanggal</td>
            <td>: <?=getFormattedDate($reqTanggalTandaDaftar);?> </td>
          </tr>
          <tr> 
            <td>Berlaku</td>
            <td>: <?=getFormattedDate($reqTanggalBerlakuTandaDaftar);?></td>
          </tr>
        </table>  
    </div> 
    <?php 
    } ?>   
 

    <?
      $allRecord_komisaris = $rekanan_pengurus_komisaris->getCountByParams(array("REKANAN_ID"=>$reqId,"TIPE"=>1));
      $rekanan_pengurus_komisaris ->selectByParams(array("REKANAN_ID"=>$reqId,"TIPE"=>1),-1,-1);
      $rekanan_cek = new Rekanan();
      $rekanan_cek->selectByParams(array("REKANAN_ID"=>$reqId),-1,-1);
      $rekanan_cek->firstRow();
      $tempRekananTipeID = $rekanan_cek->getField("REKANAN_TIPE_ID"); // 1 PT, 2 CV, 3 Firma, 4 Koperasi, 5 UD, 6 Lainnya
    ?>

    <?php 
    if ($allRecord_komisaris > 0) { ?>
    <br>
      <b style="font-weight: bold; text-decoration: underline;"><u>KOMISARIS</u></b>
      <table class="table" style="margin: 0px 0 10px 0">     
        <tr class="tr-bc">
          <td class="td" align="center" style="width: 7%">No.</td>
          <td class="td" style="width: 43%">Nama</td>
          <td class="td" style="width: 40">No. KTP</td>
          <td class="td">Jabatan</td> 
        </tr>  
        <?
          if($allRecord_komisaris > 0){
            $no_komisaris = 1;
            while($rekanan_pengurus_komisaris->nextRow()){
        ?>
        <tr>
          <td class="td" align="center"><?=$no_komisaris?></td>
          <td class="td"><?=$rekanan_pengurus_komisaris->getField("NAMA")?></td>
          <td class="td"><?=$rekanan_pengurus_komisaris->getField("KTP")?></td>
          <td class="td"><?=$rekanan_pengurus_komisaris->getField("JABATAN")?></td> 
        </tr>
        <? $no_komisaris++;}}else{
        ?>
        <tr>
          <td colspan="5" align="center"><span class="merah">.: data belum ada :.</span></td>
        </tr>
        <? }?>
      </table>  
    <?php 
    } ?>   

    <?
      $allRecord_direksi = $rekanan_pengurus_direksi->getCountByParams(array("REKANAN_ID"=>$reqId,"TIPE"=>2));
      $rekanan_pengurus_direksi->selectByParams(array("REKANAN_ID"=>$reqId,"TIPE"=>2),-1,-1);
    ?>
    <?php 
    if ($allRecord_direksi > 0) { ?>
      <b style="font-weight: bold; text-decoration: underline;"><u>DIREKSI</u></b>
      <table class="table" style="margin: 0px 0 10px 0">     
        <tr class="tr-bc">   
            <td class="td" align="center" style="width: 7%">No.</td>
            <td class="td" style="width: 43%">Nama</td>
            <td class="td" style="width: 40">No. KTP</td>
            <td class="td">Jabatan</td> 
          </tr>  
          <?
            if($allRecord_direksi > 0){
              $no_direksi = 1;
              while($rekanan_pengurus_direksi->nextRow()){
          ?>
          <tr>
            <td class="td" align="center"><?=$no_direksi?></td>
            <td class="td"><?=$rekanan_pengurus_direksi->getField("NAMA")?></td>
            <td class="td"><?=$rekanan_pengurus_direksi->getField("KTP")?></td>
            <td class="td"><?=$rekanan_pengurus_direksi->getField("JABATAN")?></td> 
          </tr>
          <? $no_direksi++;}}else{
          ?>
          <tr>
            <td colspan="5" align="center"><span class="merah">.: data belum ada :.</span></td>
          </tr>
          <? }?>
      </table> 
    <?php 
    } ?>    

    <?php 
    $rekanan_rekening_koran = new RekananRekeningKoran();
    $rekanan_rekening_koran->selectByParams(array("REKANAN_ID" => $reqId),-1,-1);
    ?>
      <b style="font-weight: bold; text-decoration: underline;"><u>REKENING KORAN</u></b>
      <table class="table" style="margin: 0px 0 10px 0">    
        <tr class="tr-bc">
          <td class="td" align="center" style="width: 20%">No. Rek</td>
          <td class="td" style="width: 35%">Bank</td> 
          <td class="td" style="width: 30%">Nilai</td> 
          <td class="td" style="width: 15%">Tahun</td> 
        </tr>  
        <?
          while($rekanan_rekening_koran->nextRow()){
        ?>
        <tr>
          <td class="td"><?=$rekanan_rekening_koran->getField("NOMOR")?></td>
          <td class="td"><?=$rekanan_rekening_koran->getField("NAMA")?></td>
          <td class="td"><?=$rekanan_rekening_koran->getField("MATAUANG").' '.numberToIna($rekanan_rekening_koran->getField("NILAI"))?></td> 
          <td style="border:1px solid #000; padding: 3px 20px; text-align: center"><?=$rekanan_rekening_koran->getField("TAHUN")?></td> 
        </tr>
        <? $no_direksi++;} 
        ?> 
      </table> 
    <?php 
      $rekanan_saham->selectByParams(array("REKANAN_ID" => $reqId),-1,-1);
    ?>
      <b style="font-weight: bold; text-decoration: underline;"><u>KEPEMILIKAN SAHAM</u></b>
      <table class="table" style="margin: 0px 0 10px 0">    
        <tr class="tr-bc">
          <td class="td" align="center" style="width: 20%">Pemegang Saham</td>
          <td class="td" style="width: 35%">No. KTP/NPWP</td> 
          <td class="td" style="width: 30%">Alamat</td> 
          <td class="td" style="width: 15%">Prosentase(%)</td> 
        </tr>  
        <?
          while($rekanan_saham->nextRow()){
        ?>
        <tr>
          <td class="td"><?=$rekanan_saham->getField("NAMA")?></td>
          <td class="td"><?=$rekanan_saham->getField("KTP")?></td>
          <td class="td"><?=$rekanan_saham->getField("ALAMAT")?></td> 
          <td class="td" align="center"><?=$rekanan_saham->getField("JUMLAH_SAHAM")?></td> 
        </tr>
        <? $no_direksi++;} 
        ?> 
      </table> 

    <?
      $rekanan_ijin_usaha->selectByParams(array("REKANAN_ID" => $reqId, "IJIN_USAHA_ID" => 99 ));
      $rekanan_ijin_usaha->firstRow();
      $reqIjinUsahaId = $rekanan_ijin_usaha->getField("REKANAN_IJIN_USAHA_ID");
      $reqNomor = $rekanan_ijin_usaha->getField("NO_IJIN");
      $reqTanggalIjin = $rekanan_ijin_usaha->getField("TANGGAL");
      $reqTanggalBerakhir = $rekanan_ijin_usaha->getField("TANGGAL_BERAKHIR");
      $reqInstansi = $rekanan_ijin_usaha->getField("INSTANSI");
      $reqBidang = $rekanan_ijin_usaha->getField("IJIN_USAHA");
      $reqLinkFileTemp= $rekanan_ijin_usaha->getField("PATH_FILE");
      $reqLinkFileTempTipe= $rekanan_ijin_usaha->getField("TIPE");
      $reqLinkFileTempUkuran= $rekanan_ijin_usaha->getField("UKURAN");
      $reqLinkFileTempNama= $rekanan_ijin_usaha->getField("NAMA_FILE");
    ?>
    <?php 
    if ($reqNomor) { ?>
    <br>
    <div class="area-dokumen" style="border: 1px solid #b7b7b7; padding: 10px 10px 0 10px"> 
      <b style="font-weight: bold; text-decoration: underline;"><u>Sertifikat Badan Usaha</u></b>
      <table class="table" style="margin-bottom: 0px">    
        <tr>
          <td style="width: 30%"> Nomor Sertifikat</td>
          <td>: <?=$reqNomor?> </td>
        </tr>
        <tr> 
          <td>Tanggal Sertifikat</td>
          <td>: <?=getFormattedDate($reqTanggalIjin)?></td>
        </tr>
        <tr> 
          <td>Tanggal Berakhir</td>
          <td>: <?=getFormattedDate($reqTanggalBerakhir)?></td>
        </tr>
        <tr> 
          <td> Nama Penanda Tangan</td>
          <td>: <?=$reqInstansi?></td>
        </tr>
      </table> 
      <b style="font-weight: bold; text-decoration: underline;"><u>Bidang Usaha</u></b>
      <table class="table">   
        <tr class="tr-bc">
          <td class="td" style="width: 7%" align="center">No</td>
          <td class="td">Bidang Usaha</td>
        </tr>
        <?
        $nosbu=1;
        $rekanan_bidang_usaha_sbu->selectByParamsMonitoring(array("REKANAN_ID" =>$reqId, "IJIN_USAHA_ID "=> "99"));
        while($rekanan_bidang_usaha_sbu->nextRow())
        {
        ?>
          <tr>
            <td class="td" align="center" style="border:1px solid #000; padding: 3px 20px; text-align: center"><?=$nosbu?></td>
            <td class="td"><?=$rekanan_bidang_usaha_sbu->getField("NAMA")?></td>
          </tr>
        <?
          $nosbu++;
        } ?>
      </table> 
    </div> 
    <?php 
    } ?>    

 

</body>
</html>
