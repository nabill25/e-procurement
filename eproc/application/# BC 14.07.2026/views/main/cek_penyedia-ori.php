<?php
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 * 
 */

$this->libsession->cekSession('free');   

$this->load->model("Rekanan");
$this->load->library("crfs_protect"); $csrf = new crfs_protect('_crfs_rlt');
include_once("functions/string.func.php");
include_once("functions/date.func.php");
include_once("functions/default.func.php");

$rekanan = new Rekanan();

$reqId = httpFilterRequest("reqId");
$a = explode('||', $reqId);

$reqRekananId     = $a[0];
$reqRekananKode   = $a[1];
$reqUserLoginId   = $a[2];
$reqValidator     = str_replace("_", " ", $a[3]);


$rekanan->selectByParams2(array("A.KODE"=>$reqRekananKode, "A.REKANAN_ID"=>$reqRekananId),-1,-1);
$rekanan->firstRow();
// if ($rekanan->countRow() == 0) {
//   exit();
// }
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
$tempStatusValidasi= $rekanan->getField("STATUS_VALIDASI");
$tempUserStatus= $rekanan->getField("USER_STATUS");
$tempUserValidasi= $rekanan->getField("USER_VALIDASI");

$tempRekananNama = $rekanan->getField("REKANAN_NAMA");
$tempTglValidasi2 = $rekanan->getField("TANGGAL_VALIDASI2");
$a = explode('.', $tempTglValidasi2);
$tempTglValidasi = $a[0];

$tempTglDaftar = $rekanan->getField("TANGGAL_DAFTAR");

$tempKTP= $rekanan->getField("KTP");
$tempCV= $rekanan->getField("CV_FILE");

?>

<section id="backColor">
  <div class="row"> 
    <div class="col-md-12 col-sm-12">
      <div class="card card border-bottom-primary box-shadow-0 animated zoomIn" style="zoom: 1;"> 
        <div class="card-header">
          <h4 class="card-title"><i class="ft-user"></i> Cek Surat Keterangan Terdaftar (SKT)</h4>
          <div class="heading-elements">
            <ul class="list-inline mb-0">
              <li><a data-action="expand"><i class="ft-maximize"></i></a></li>
              <!-- <li><a data-action="close"><i class="ft-x"></i></a></li> -->
            </ul>
          </div>
        </div>
        <div class="card-body"> 
          <div class="form-body"> 
            <?php
            if ($tempUserStatus == '1') { ?>
              <img src="images/centang.png" height="75" style="position: absolute; right: 6%; top: 5%" />
            <?php 
            } else { ?>
              <img src="images/uncentang.png" height="75" style="position: absolute; right: 6%; top: 5%" />
            <?php 
            } ?>

              <?php 
              if ($reqRekananTipeId == '7') { ?>
                <table class="table table-bordered">
                  <tr>
                      <td style="width: 30%">Nama</td>
                      <td> <?=$tempNama ? $tempNama : '-'?></td>
                  </tr> 
                  <tr>
                      <td>Alamat</td>
                      <td> <?=$tempAlamat ? $tempAlamat : '-'?></td>
                  </tr>
                  <tr>
                      <td>Kota</td>
                      <td> <?=$tempKota ? $tempKota : '-'?></td>
                  </tr>
                  <tr>
                      <td>Provinsi</td>
                      <td> <?=$rekanan->getField("REGION") ? $rekanan->getField("REGION") : '-'?></td>
                  </tr>
                  <tr>
                      <td>Kodepos</td>
                      <td> <?=$rekanan->getField("KODEPOS") ? $rekanan->getField("KODEPOS") : '-'?></td>
                  </tr>
                  <tr>
                      <td>No. telepon</td>
                      <td> <?=$tempTelepon ? $tempTelepon : '-'?></td>
                  </tr>
                  <tr>
                      <td>No. Fax</td>
                      <td> <?=$tempFax ? $tempFax : '-'?></td>
                  </tr>
                  <tr>
                      <td>Kontak Person</td>
                      <td> <?=$tempKontakPerson ? $tempKontakPerson : '-'?> </td>
                  </tr>
                  <tr>
                      <td>HP</td>
                      <td> <?=$tempKontakPersonHp?>  </td>
                  </tr>
                  <tr>
                      <td>E-mail</td>
                      <td> <?=$tempMail ? $tempMail : '-'?></td>
                  </tr>
                  <tr>
                      <td>Website</td>
                      <td> <?=$tempWebsite ? $tempWebsite : '-'?> </td>
                  </tr>
                  <tr>
                      <td>Kualifikasi</td>
                      <td> <?=$tempKualifikasi ? $tempKualifikasi : '-'?></td>
                  </tr>
                  <tr>
                      <td>NPWP</td>
                      <td> <?=$tempNPWP ? $tempNPWP : '-'?></td>
                  </tr>
                  <tr>
                      <td>KTP</td>
                      <td> <?=$tempKTP ? $tempKTP : '-'?></td>
                  </tr> 
                  <tr>
                      <td>CV ( Daftar Riwayat Hidup )</td>
                      <td> 
                          <?php
                          if ($tempCV) {
                              echo "Ada";
                          } else {
                              echo "Tidak Ada";
                          }
                          ?>
                      </td>
                  </tr> 
                </table> 

                <table class="table table-bordered">    
                <?php
                  $jumlahUncentang = 0;
                  $rekanan = new Rekanan();
                  $rekanan_keuangan = new Rekanan();
                  $rekanan_perpajakan = new Rekanan();
                  $rekanan_teknis = new Rekanan();
                    $rekanan->selectByParamsKonfirmasiPerorangan($reqId);
                    $rekanan_perpajakan->selectByParamsKonfirmasiPeroranganDataPerpajakan($reqId);
                    $rekanan_teknis->selectByParamsKonfirmasiPeroranganDataTeknis($reqId);
                  $no=1;
                  ?>
                  <tr style="background-color: #967adc; color: #fff">
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

                  <tr style="background-color: #967adc; color: #fff">
                    <td class="td" colspan="3" >Data Perpajakan</td>
                  </tr> 
                  <?php
                  $no2=1;
                    while($rekanan_perpajakan->nextRow())
                    {
                  ?>
                      <tr>
                        <td class="td" style="width: 2%; text-align: center;"><?=$no2;?></td>
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
                    $no2++;
                    } ?>
                   

                  <tr style="background-color: #967adc; color: #fff">
                    <td class="td" colspan="3" >Data Teknis</td>
                  </tr> 
                  <?php 
                  $no3=1;
                    while($rekanan_teknis->nextRow())
                    {
                  ?>
                      <tr>
                        <td class="td" style="width: 2%; text-align: center;"><?=$no3;?></td>
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
                    $no3++;
                    } ?> 
                </table>

                <?php 
                if ($jumlahUncentang > 0) {
                   echo '<div>
                          <b> Kurang '.$jumlahUncentang.' data belum dilengkapi (tanda * data wajib diisi)  </b> 
                        </div><br>';
                } ?>
              <?php
              } else { ?>

              <table class="table table-bordered">
                <tr>
                    <td style="width: 25%">Nama Perusahaan</td>
                    <td> <?=$tempNama ? $tempNama : '-'?></td>
                </tr>
                <tr>
                    <td>Status Kantor</td>
                    <td> <?=$tempStatus ? $tempStatus : '-'?></td>
                </tr>
                <tr>
                    <td valign="top">Alamat</td>
                    <td> <?=$tempAlamat ? $tempAlamat : '-'?>
                          <br> &nbsp;&nbsp;<?=$tempKota ? $tempKota : '-'?>
                          <br> &nbsp;&nbsp;<?=$rekanan->getField("REGION") ? $rekanan->getField("REGION") : '-'?>
                          <br> &nbsp;&nbsp;<?=$rekanan->getField("KODEPOS") ? $rekanan->getField("KODEPOS") : '-'?>
                    </td>
                </tr> 
                <tr>
                    <td>No. telepon</td>
                    <td> 
                      <?=$tempTelepon ? $tempTelepon : '-'?> / Fax: <?=$tempFax ? $tempFax : '-'?>
                    </td>
                </tr> 
                <tr>
                    <td>Kontak Person</td>
                    <td> 
                      <?=$tempKontakPerson ? $tempKontakPerson : '-'?>  / HP: <?=$tempKontakPersonHp ? $tempKontakPersonHp : '-'?>
                    </td>
                </tr> 
                <tr>
                    <td>E-mail</td>
                    <td> <?=$tempMail ? $tempMail : '-'?></td>
                </tr>
                <tr>
                    <td>Website</td>
                    <td> <?=$tempWebsite ? $tempWebsite : '-'?> </td>
                </tr>
                <tr>
                    <td>Kualifikasi Usaha</td>
                    <td> <?=$tempKualifikasi ? $tempKualifikasi : '-'?></td>
                </tr>
                <tr>
                    <td>NPWP</td>
                    <td> <?=$tempNPWP ? $tempNPWP : '-'?></td>
                </tr>
                <tr>
                    <td>PKP</td>
                    <td> <?=$tempPKP ? $tempPKP : '-'?></td>
                </tr>
                <tr>
                    <td>Tanggal PKP</td>
                    <td> <?=$tempPKPTanggal ? $tempPKPTanggal : '-'?></td>
                </tr>
                <tr>
                    <td>Tanggal Daftar</td>
                    <td> <?=$tempTglDaftar ? $tempTglDaftar : '-'?></td>
                </tr>
                <tr>
                    <td>Tanggal Verifikasi</td>
                    <td> <?=$tempTglValidasi ? $tempTglValidasi : '-'?></td>
                </tr>
                <tr>
                    <td>Validator</td>
                    <td> <?=$reqValidator ? $reqValidator : '-'?></td>
                </tr>
              </table> 

              <table class="table table-bordered">    
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
                    <tr style="background-color: #967adc; color: #fff">
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
 
                    <tr style="background-color: #967adc; color: #fff">
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
 
                    <tr style="background-color: #967adc; color: #fff">
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
 
                   <tr style="background-color: #967adc; color: #fff">
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
              <?php
              } ?>

          </div>
        </div>
      </div>
    </div>  

  </div>  
</section> 
 