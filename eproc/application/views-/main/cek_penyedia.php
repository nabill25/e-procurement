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
$userValidasiEx = explode('||', $tempUserValidasi);

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
            if ($tempStatusValidasi == '1') { ?>
              <span class="fa fa-check fa-4x" style="position: absolute; right: 6%; top: 5%; color: blue"> </span>
            <?php
            } else { ?>
              <span class="fa fa-close fa-4x" style="position: absolute; right: 6%; top: 5%; color: red"> </span>
              <!-- <img src="images/uncentang.png" height="75" style="position: absolute; right: 6%; top: 5%" /> -->
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
                    <td> <?=$tempUserValidasi ? $userValidasiEx[1] : '-'?></td>
                </tr>
              </table>

              <?php
              } ?>

          </div>
        </div>
      </div>
    </div>

  </div>
</section>
