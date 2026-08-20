<?php
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 *
 */
$this->load->library("kauth");  $userLogin = new kauth();
$this->load->model(array("Rekanan","Bank"));
include_once("functions/string.func.php");
include_once("functions/date.func.php");
include_once("functions/default.func.php");
/* create objects */
$rekanan = new Rekanan();

$reqId = $this->input->get("reqId");

$rekanan->selectByParams(array("A.REKANAN_ID"=>$reqId),-1,-1);
$rekanan->firstRow();
$reqStatusValidasi = $rekanan->getField("STATUS_VALIDASI");
$reqUserStatus = $rekanan->getField("USER_STATUS");

$tempKualifikasi = $rekanan->getField("REKANAN_KUALIFIKASI");
$tempMail = $rekanan->getField("EMAIL");
$tempFax = $rekanan->getField("FAX_KODE")."-".$rekanan->getField("FAX");
$tempTelepon = $rekanan->getField("TELEPON_KODE")."-".$rekanan->getField("TELEPON");
// $tempKota = $rekanan->getField("KOTA");
$tempAlamat = $rekanan->getField("ALAMAT");
$tempKodepos = $rekanan->getField("KODEPOS");
$tempStatus = $rekanan->getField("STATUS_CP");
$tempNPWP = $rekanan->getField("NPWP");
$tempNama= $rekanan->getField("NAMA");
$tempNPWPFILE = $rekanan->getField("NPWP_FILE");
$tempKontakPerson= $rekanan->getField("KONTAK_PERSON");
$tempKontakPersonHp= $rekanan->getField("KONTAK_PERSON_HP");
$tempWebsite= $rekanan->getField("WEBSITE");
$reqPKP = $rekanan->getField("PKP");
$reqMasaBerlakuPKP = $rekanan->getField("PKP_TANGGAL");
$reqLinkFileTemp = $rekanan->getField("PKP_TANGGAL");
$reqMasaBerlakuPKP = $rekanan->getField("PKP_TANGGAL");
$reqLinkFileTemp = $rekanan->getField("NAMA_FILE_PKP");
$reqStatusPKP = $rekanan->getField("STATUS_PKP");
$reqSKTPKP = $rekanan->getField("SKT_PKP_NOMOR");
$reqSKTPKPFileTemp = $rekanan->getField("SKT_PKP_FILE");
$reqNamaFileSKTPKP = $rekanan->getField("NAMA_SKT_PKP_FILE");
$reqNONPKPFileTemp = $rekanan->getField("NON_PKP_FILE");
$reqNamaFileNONPKP = $rekanan->getField("NAMA_NON_PKP_FILE");
$tempCPFILE = $rekanan->getField("COMPANY_PROFILE_FILE");

?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <base href="<?=base_url();?>" />
    <link rel="icon" href="../../favicon.ico">
    <title><?= SYSTEM_NAME.' | '.SYSTEM_NAME_PT ?></title>

    <link rel="stylesheet" type="text/css" href="<?=base_url() ?>assets/new/css/vendors.css">
    <link rel="stylesheet" type="text/css" href="<?=base_url() ?>assets/new/vendors/css/ui/prism.min.css">
    <link rel="stylesheet" type="text/css" href="<?=base_url() ?>assets/new/css/app.css">
    <link rel="stylesheet" type="text/css" href="<?=base_url() ?>assets/new/css/core/menu/menu-types/horizontal-menu.css">
    <link rel="stylesheet" type="text/css" href="<?=base_url() ?>assets/new/css/core/colors/palette-gradient.css">
    <link rel="stylesheet" type="text/css" href="<?=base_url() ?>assets/new/css/core/colors/palette-callout.css">
    <link rel="stylesheet" type="text/css" href="<?=base_url() ?>assets/new/css/style.css">

  </head>
<style type="text/css">.badge[class*='badge-'] span { bottom: 0px !important; }</style>
<body class="body-popup">

    <div class="card mb-1">
      <div class="card-content">
        <div class="p-1">
          <div class="alert bg-primary alert-icon-left alert-arrow-left alert-dismissible mb-2" role="alert">
            <span class="alert-icon"><i class="fa fa-th"></i></span>  <strong>Profil Perusahaan</strong>
          </div>
          <div class="table-responsive">
            <!-- <form id="ff" class="form-horizontal" role="form"> -->

                <table class="table table-striped table-hover">
                  <tbody>
                      <?php
                      // if ($rekanan->getField("STATUS_VALIDASI") == '3') {
                      //    echo '<div class="alert alert-danger" style="width: 100%; margin: 0 auto;">
                      //          <b>Catatan Rekomendasi</b> : '.$rekanan->getField("NOTE_1").' <br>
                      //          </div>';
                      //  } else if ($rekanan->getField("STATUS_VALIDASI") == '4') {
                      //    echo '<div class="alert alert-danger" style="width: 100%; margin: 0 auto;">
                      //          <b>Catatan Rekomendasi</b> : '.$rekanan->getField("NOTE_1").' <br>
                      //          <b>Catatan Approval Penyelia</b> : '.$rekanan->getField("NOTE_2").'
                      //          </div>';
                      //  } else if ($rekanan->getField("STATUS_VALIDASI") == '10') {
                      //    echo '<div class="alert alert-danger" style="width: 100%; margin: 0 auto;">
                      //          <b>Catatan Rekomendasi</b> : '.$rekanan->getField("NOTE_1").' <br>
                      //          <b>Catatan Approval Penyelia</b> : '.$rekanan->getField("NOTE_2").' <br>
                      //          <b>Catatan Approval Sub Div</b>: '.$rekanan->getField("NOTE_3").'
                      //          </div>';
                      //  }
                       ?>
                      <tr>
                          <td width="20%">Nama Perusahaan:</td>
                          <td>
                           <?=$tempNama?>
                          </td>
                      </tr>
                      <tr>
                          <td width="20%">NPWP:</td>
                          <td>
                            <?=$tempNPWP?>
                            <?php
                            if ($tempNPWPFILE) { ?>
                            <a href="<?= base_url('uploads/rekanan').'/'.$tempNPWPFILE ?>" class="badge badge-primary" target="_blank"><span class="fa fa-download"></span> Download file NPWP</a>
                            <?php
                            } else { echo '<span class="badge badge-danger">Belum upload</span>';} ?>
                          </td>
                      </tr>

                      <!-- <tr>
                          <td width="20%">Status Kantor Perusahaan:</td>
                          <td>
                            <?php // echo $tempStatus?>
                          </td>
                      </tr> -->
                      <tr>
                          <td width="20%">Alamat:</td>
                          <td>
                            <?=$tempAlamat?>
                          </td>
                      </tr> 
                      <tr>
                          <td style="width: 20%">Provinsi:</td>
                          <td>
                              <?=$rekanan->getField("NAMAPROPINSI")?>
                          </td>
                      </tr>
                      <tr>
                          <td style="width: 20%">Kabupaten/Kota:</td>
                          <td>
                              <?=$rekanan->getField("NAMAKABKOTA")?>
                          </td>
                      </tr>
                      <tr>
                          <td style="width: 20%">Kecamatan:</td>
                          <td>
                              <?=$rekanan->getField("NAMAKECAMATAN")?>
                          </td>
                      </tr>
                      <tr>
                          <td style="width: 20%">Kelurahan:</td>
                          <td>
                              <?=$rekanan->getField("KELURAHAN")?>
                          </td>
                      </tr>
                      <tr>
                          <td style="width: 20%">Kodepos :</td>
                          <td><?=$tempKodepos?></td>
                      </tr>
                      <tr>
                          <td width="20%">No. telepon:</td>
                          <td>
                            <?=$tempTelepon?>
                          </td>
                      </tr>
                      <tr>
                          <td width="20%">No. fax:</td>
                          <td>
                            <?=$tempFax?>
                          </td>
                      </tr>
                      <tr>
                          <td width="20%">Kontak Person:</td>
                          <td>
                            <?=$tempKontakPerson?>
                          </td>
                      </tr>
                      <tr>
                          <td width="20%">HP:</td>
                          <td>
                            <?=$tempKontakPersonHp?>
                          </td>
                      </tr>
                      <tr>
                          <td width="20%">E-mail:</td>
                          <td>
                            <?=$tempMail?>
                          </td>
                      </tr>
                      <tr>
                          <td style="width: 20%">Website :</td>
                          <td><?=$tempWebsite?></td>
                      </tr>
                      <tr>
                          <td width="20%">Kualifikasi Usaha:</td>
                          <td>
                            <?=$tempKualifikasi?>
                          </td>
                      </tr>
                      <tr>
                          <td style="width: 20%">Company Profile</td>
                          <td>:
                              <?php
                              if ($tempCPFILE != '' && file_exists('uploads/rekanan/'.$tempCPFILE)) {
                              // if ($tempCPFILE) {
                                  ?>
                              <a target="_blank" href="<?= base_url('uploads/rekanan/').$tempCPFILE ?>" class="badge badge-primary"><span class="fa fa-download"></span> Download Company Profile</a>
                              <?php
                              } else { echo '<span class="badge badge-danger">Belum upload</span>';} ?>
                          </td>
                      </tr>
                  </tbody>
                </table>

                <div class="alert bg-primary alert-icon-left alert-arrow-left alert-dismissible mb-2" role="alert">
                  <span class="alert-icon"><i class="fa fa-th"></i></span>  <strong>Informasi Pembayaran</strong>
                </div>
                <table class="table table-striped table-hover">
                  <tbody>
                      <tr>
                          <td width="20%">Bank:</td>
                          <td>
                            <?=$rekanan->getField("BANK")?>
                          </td>
                      </tr>
                      <tr>
                          <td width="20%">No. Rekening:</td>
                          <td>
                            <?=$rekanan->getField("BANK_REKENING")?>
                          </td>
                      </tr>
                      <tr>
                          <td width="20%">Atas Nama:</td>
                          <td>
                            <?=$rekanan->getField("BANK_PEMILIK")?>
                          </td>
                      </tr>
                      <tr>
                          <td width="20%">Cabang:</td>
                          <td>
                            <?=$rekanan->getField("BANK_CABANG")?>
                          </td>
                      </tr>
                      <!-- <tr>
                          <td width="20%">Cara Pembayaran:</td>
                          <td>
                            <?=$rekanan->getField("PAYMENT_METHOD")?>
                          </td>
                      </tr>
                      <tr>
                          <td width="20%">Mata Uang:</td>
                          <td>
                            <?=$rekanan->getField("MATA_UANG_KODE")?>
                          </td>
                      </tr> -->
                  </tbody>
                </table>
                <?php
                  /* create objects */
                  $bank = new Bank();
                  $bank->selectByParamsRekanan(array("REKANAN_ID"=>$reqId),-1,-1);
                  if ($bank->countRow() > 0) {
                  ?>
                  <table class="table table-bordered table mb-0">
                    <thead>
                      <tr>
                        <th>Bank</th>
                        <th>No. Rekening</th>
                        <th>Atas Nama</th>
                        <th>Cabang</th>
                      </tr>
                    </thead>
                    <tbody id="tbodyDeliverable">
                  <?php
                    while($bank->nextRow()) {
                    ?>
                    <tr>
                       <td><?= $bank->getField('BANK_NAMA') ?></td>
                       <td><?= $bank->getField('BANK_REKENING') ?></td>
                       <td><?= $bank->getField('BANK_PEMILIK') ?></td>
                       <td><?= $bank->getField('BANK_CABANG') ?></td>
                    </tr>
                  <?php
                      } ?>
                    </tbody>
                  </table>
                  <?php
                  } ?>

                <!-- <div class="alert bg-primary alert-icon-left alert-arrow-left alert-dismissible mb-2" role="alert">
                  <span class="alert-icon"><i class="fa fa-th"></i></span>  <strong>Incoterm</strong>
                </div>
                <table class="table table-striped table-hover">
                  <tbody>
                      <tr>
                          <td width="20%">Incoterm I:</td>
                          <td>
                            <?=$rekanan->getField("INCOTERM1")?>
                          </td>
                      </tr>
                      <tr>
                          <td width="20%">Incoterm II:</td>
                          <td>
                            <?=$rekanan->getField("INCOTERM2")?>
                          </td>
                      </tr>
                  </tbody>
                </table> -->
            <!-- </form> -->

          </div>
          <?php 
          if ($this->USER_TYPE_ID == '2' && ($reqStatusValidasi != '1' || $reqUserStatus == '0')) {?>
          <form id="ff2" class="easyui-form " method="post" novalidate enctype="multipart/form-data">
            <div class="form-actions card-content collapse show border-info border-darken-2 mt-2">
              <div class="card-body">
                <?php
                $checked = '';
                $cekData = new Rekanan();
                $cekData->selectByParamsRekananChecklist(array("REKANANID"=>$reqId),-1,-1);
                $cekData->firstRow();
                if ($cekData->getField("npwp") == '1') {
                  $checked = 'checked';
                }
                echo '<input class="mb-1" type="checkbox" name="checknpwp" id="checkjenis" onclick="return updateChecklist(\''.$reqId.'\',\'npwp\')" style="cursor:pointer" '.$checked.'> Ya, Lengkap ';
                ?>
                <input type="" class="form-control easyui-validatebox span2" required maxlength="255" style="height: 30px !important;" name="" placeholder="catatan" id="catatanjenis" value="<?=$cekData->getField("npwp_note")?>" onChange="return updateChecklist('<?= $reqId ?>','npwp')">
                <small><sup>*</sup>&nbsp;Tekan enter setalah mengisi catatan</small>
              </div>
            </div>
          </form>
          <?php 
          } ?>
        </div>
      </div>
    </div>


    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="lib/bootstrap/libs/jquery/1.12.4/jquery.min.js"></script>
    <script>window.jQuery || document.write('<script src="lib/bootstrap/assets/js/vendor/jquery.min.js"><\/script>')</script>
    <script src="lib/bootstrap/dist/js/bootstrap.min.js"></script>
    <link rel="stylesheet" type="text/css" href="<?=base_url()?>lib/eproc/themes/default/easyui.css">
    <script type="text/javascript" src="<?=base_url()?>lib/eproc/jquery.easyui.min.js"></script>
    <script type="text/javascript" src="<?=base_url()?>lib/eproc/kalender-easyui.js"></script>
    <script type="text/javascript" src="<?=base_url()?>lib/eproc/allfunc.js"></script>

    <script type="text/javascript">
    function updateChecklist(rekananid,jenis) {
      var n = $("#checkjenis:checked").length;
      if (n == 1) {
          $('#catatanjenis').validatebox({ required:false  });
          $('#catatanjenis').val('');
      } else {
          $('#catatanjenis').validatebox({ required:true  });
      }
      var c = $("#catatanjenis").val();
      // alert(n+'-'+c+'-'+rekananid+'-'+jenis); return false;
        $.getJSON("rekanan_json/updateChecklist2/?rekananid="+rekananid+"&jenis="+jenis+"&status="+n+"&catatan="+c,
          function(data){
            if (data.RESPONSE === 'Gagal') {
              $.messager.alert('Info', data.PESAN, 'info');
              if (n === 0) { // kalau gagal balik ke awal
                $("#checkjenis").prop("checked", true);
              } else {
                $("#checkjenis").prop("checked", false);
              }
            }
        });
    }
    </script>

  </body>
</html>
