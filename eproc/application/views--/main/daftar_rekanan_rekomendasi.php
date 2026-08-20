<?php
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 *
 */

$reqId = $this->input->get("reqId");

$this->load->library("kauth");  $userLogin = new kauth();
include_once("functions/string.func.php");
include_once("functions/date.func.php");
include_once("functions/default.func.php");
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

$rekanan = new Rekanan();
$user_login = new Users();

$rekanan->selectByParams(array("A.REKANAN_ID"=>$reqId),-1,-1);
$rekanan->firstRow();
$reqKode = $rekanan->getField("KODE");
$reqId = $rekanan->getField("REKANAN_ID");
$reqRekananTipeId = $rekanan->getField("REKANAN_TIPE_ID");
$tempKualifikasi = $rekanan->getField("REKANAN_KUALIFIKASI");
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
$tempLinkFileTempKTP= $rekanan->getField("KTP_FILE");
$tempLinkFileTempNPWP= $rekanan->getField("NPWP_FILE");
$tempStatus = $rekanan->getField("STATUS_CP");
$tempNPWP = $rekanan->getField("NPWP");
$tempNama= $rekanan->getField("NAMA");
$tempCV= $rekanan->getField("CV_FILE");
$tempRekananNama = $rekanan->getField("REKANAN_NAMA");
$tempStatusValidasi = $rekanan->getField("STATUS_VALIDASI");
$reqStatusValidasi = $rekanan->getField("STATUS_VALIDASI");
$reqUserStatus = $rekanan->getField("USER_STATUS");
$reqUserValidasi = $rekanan->getField("USER_VALIDASI");
$explodeRekananValidasi = explode("||", $reqUserValidasi);
$reqTanggalDaftar = $rekanan->getField("TANGGAL_DAFTAR");
$reqTanggalValidasi = $rekanan->getField("TANGGAL_VALIDASI");


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
    <link href="lib/bootstrap/dist/css/bootstrap.css" rel="stylesheet">
    <link rel="stylesheet" href="css/core.css" type="text/css">
    <link href='http://fonts.googleapis.com/css?family=Roboto:400,300,700' rel='stylesheet' type='text/css'>
    <link rel="stylesheet" type="text/css" href="<?=base_url() ?>assets/new/css/vendors.css">
    <link rel="stylesheet" type="text/css" href="<?=base_url() ?>assets/new/vendors/css/ui/prism.min.css">
    <link rel="stylesheet" type="text/css" href="<?=base_url() ?>assets/new/css/app.css">
    <link rel="stylesheet" type="text/css" href="<?=base_url() ?>assets/new/css/core/menu/menu-types/horizontal-menu.css">
    <link rel="stylesheet" type="text/css" href="<?=base_url() ?>assets/new/css/core/colors/palette-gradient.css">
    <link rel="stylesheet" type="text/css" href="<?=base_url() ?>assets/new/css/core/colors/palette-callout.css">
    <link rel="stylesheet" type="text/css" href="<?=base_url() ?>assets/new/css/style.css">
    <link rel="stylesheet" href="lib/font-awesome-4.7.0/css/font-awesome.css" type="text/css">
    <script type="text/javascript" src="js/jquery-1.6.1.min.js"></script>
  </head>

  <script type="text/javascript">
  $(function(){
    $('#ff').form({
      url:'rekanan_json/validasi_rekanan_teruskan',
      onSubmit:function(){
        return $(this).form('validate');
      },
      success:function(data){
        window.top.location.reload();
      }
    });
  });

  function revisiPendaftaran()
  {
    <?php //$this->input->get("reqId") ?>
    if(confirm("Apa benar data penyedia ini ingin dikembalikan?"))
    {
      $.getJSON('rekanan_json/revisi_rekanan?reqId='+<?=$reqId?>,
      function(data){
         $.messager.alert('Info', "Dikembalikan ke Rekanan.", 'info');
         $("#btnSertifikat").hide();
         $("#btnRevisi").show();
         $("#btnValidasi").show();
      });
    }
  }
</script>

  <body>

    <div class="card mb-1">
    <div class="card-content">
      <div class="p-1">
        <div class="alert bg-primary alert-icon-left alert-arrow-left alert-dismissible mb-2" role="alert">
          <span class="alert-icon"><i class="fa fa-th"></i></span>  <strong>CHECKLIST KELENGKAPAN</strong>
        </div>
        <div class="table-responsive">
            <div class="row">
              <div class="col-md-12 col-sm-12">

              <p class="alert alert-primary" style="background-color: transparent !important;">
                <b><?= $tempNama ?></b>
                <?php 
                  echo '<br>Tanggal Daftar: '.getFormattedDateJson($reqTanggalDaftar); 
                if($tempStatusValidasi == 1)
                {
                  echo '<br>Tanggal Validasi: '.getFormattedDateJson($reqTanggalValidasi); 
                  echo '<br>Validator: '.$explodeRekananValidasi[1]; 
                }
                ?>
              </p>

                <div class="card">
                  <?php
                  if ($rekanan->getField("STATUS_VALIDASI") == '3') {
                     echo '<div class="alert alert-danger" style="width: 100%; margin: 0 auto;">
                           <h4>Catatan</h4>
                           <b>Admin VMS</b> : '.$rekanan->getField("NOTE_1").' <br>
                           </div>';
                   } else if ($rekanan->getField("STATUS_VALIDASI") == '4') {
                     echo '<div class="alert alert-danger" style="width: 100%; margin: 0 auto;">
                           <h4>Catatan</h4>
                           <b>Admin VMS</b> : '.$rekanan->getField("NOTE_1").' <br>
                           <!-- <b>Approval Penyelia</b> : '.$rekanan->getField("NOTE_2").' -->
                           </div>';
                   } else if ($rekanan->getField("STATUS_VALIDASI") == '10') {
                     echo '<div class="alert alert-danger" style="width: 100%; margin: 0 auto;">
                           <h4>Catatan</h4>
                           <b>Admin VMS</b> : '.$rekanan->getField("NOTE_1").' <br>
                           <!-- <b>Approval Penyelia</b> : '.$rekanan->getField("NOTE_2").' <br> -->
                           <b>Approval Sub Div</b>: '.$rekanan->getField("NOTE_3").'
                           </div>';
                   }
                   ?>
                  <div class="card-content collapse show border-info border-darken-2">
                    <div class="card-body">

                      <?php
                      if ($reqRekananTipeId == '7')
                      { // Konsultan Perorangan ?>
                       <?php
                        $jumlahUncentang = 0;
                        $rekanan = new Rekanan();
                        $rekanan_keuangan = new Rekanan();
                        $rekanan_perpajakan = new Rekanan();
                        $rekanan_teknis = new Rekanan();
                        $rekanan_pakta_integritas = new Rekanan();

                        if ($reqRekananTipeId == '7') { // Perorangan
                          $rekanan->selectByParamsKonfirmasiPerorangan($reqId);
                          $rekanan_perpajakan->selectByParamsKonfirmasiPeroranganDataPerpajakan($reqId);
                          $rekanan_teknis->selectByParamsKonfirmasiPeroranganDataTeknis($reqId);
                        } else {
                          $rekanan->selectByParamsKonfirmasiDataAdmin($reqId);
                          $rekanan_keuangan->selectByParamsKonfirmasiDataKeuangan($reqId);
                          $rekanan_perpajakan->selectByParamsKonfirmasiDataPerpajakan($reqId);
                          $rekanan_teknis->selectByParamsKonfirmasiDataTeknis($reqId);
                        }
                        $rekanan_pakta_integritas->selectByParamsKonfirmasiPaktaIntegritas($reqId);

                        $no=1;
                        if ($reqRekananTipeId == '7') { // Perorangan ?>
                        <h4>Data Administrasi</h4>
                        <table class="table table-striped">
                        <?php
                            while($rekanan->nextRow())
                            {
                              $checked = '';
                              $pesan = '';
                              $cekData = new Rekanan();
                              $cekData->selectByParamsRekananChecklist(array("REKANANID"=>$reqId),-1,-1);
                              $cekData->firstRow();
                              $field = strtoupper($rekanan->getField("FIELDNYA"));
                              if ($cekData->getField("$field") == '1') {
                                $checked = 'checked';
                              } else {
                                if ($cekData->getField("$field"."_NOTE")) {
                                  $pesan = '<span class="badge badge-danger"><small>'.$cekData->getField("$field"."_NOTE").'</small></span>';
                                }
                              }
                          ?>
                              <tr>
                                <td style="width: 2%"><?=$no;?></td>
                                <td style="width: 83%">
                                  <?=$rekanan->getField("NAMA") ?>
                                  <?php
                                  if ($rekanan->getField("WAJIB") == '*') {
                                     echo '<span class="color:red">'.$rekanan->getField("WAJIB").'</span>';
                                  } else {
                                    echo "";
                                  } 
                                  echo "<br>".$pesan; ?>
                                </td>
                                <td style="width: 15%" class="text-center">
                                  <img class="simbol" src="images/<?=$rekanan->getField("SIMBOL")?>.png">
                                </td>
                                <td style="width: 5%" class="text-center">
                                <?php
                                if ($this->USER_TYPE_ID == '2' && ($reqStatusValidasi != '1' || $reqUserStatus == '0'))
                                {
                                  // if ($rekanan->getField("SIMBOL") == 'centang' && $rekanan->getField("WAJIB") == '*') {
                                     echo '<input type="checkbox" name="check'.$rekanan->getField("FIELDNYA").'" id="check'.$rekanan->getField("FIELDNYA").'" onclick="return updateChecklist(\''.$reqId.'\',\''.$rekanan->getField("FIELDNYA").'\')" style="cursor:pointer" '.$checked.'>';
                                  // }
                                }?>
                              </td>
                              </tr>
                                <?php
                             if($rekanan->getField("SIMBOL") == "uncentang" && $rekanan->getField("WAJIB") == '*')
                              $jumlahUncentang++;
                            $no++;
                            } ?>
                        </table>
                        <?php
                        } else {  ?>
                        <h4>Data Administrasi</h4>
                        <table class="table table-striped">
                        <?php
                          while($rekanan->nextRow())
                          {
                        ?>
                            <tr>
                              <td style="width: 2%"><?=$no;?></td>
                              <td style="width: 83%">
                                <?=$rekanan->getField("NAMA") ?>
                                <?php
                                if ($rekanan->getField("WAJIB") == '*') {
                                   echo '<span class="color:red">'.$rekanan->getField("WAJIB").'</span>';
                                } else {
                                  echo "";
                                } ?>
                              </td>
                              <td style="width: 15%" class="text-center">
                                <img class="simbol" src="images/<?=$rekanan->getField("SIMBOL")?>.png">
                              </td>
                            </tr>
                              <?php
                           if($rekanan->getField("SIMBOL") == "uncentang" && $rekanan->getField("WAJIB") == '*')
                            $jumlahUncentang++;
                          $no++;
                          } ?>
                      </table>
                      <?php
                        }
                      ?>

                      <?php
                        $no=1;
                        if ($reqRekananTipeId == '7') { // Perorangan
                        } else {  ?>
                        <h4>Data Keuangan</h4>
                        <table class="table table-striped">
                        <?php
                          while($rekanan_keuangan->nextRow())
                          {
                        ?>
                            <tr>
                              <td style="width: 2%"><?=$no;?></td>
                              <td style="width: 83%">
                                <?=$rekanan_keuangan->getField("NAMA") ?>
                               <?php
                                if ($rekanan_keuangan->getField("WAJIB") == '*') {
                                   echo '<span class="color:red">'.$rekanan_keuangan->getField("WAJIB").'</span>';
                                } else {
                                  echo "";
                                } ?>
                              </td>
                              <td style="width: 15%" class="text-center">
                                <img class="simbol" src="images/<?=$rekanan_keuangan->getField("SIMBOL")?>.png">
                              </td>
                            </tr>
                              <?php
                           if($rekanan_keuangan->getField("SIMBOL") == "uncentang" && $rekanan_keuangan->getField("WAJIB") == '*')
                            $jumlahUncentang++;
                          $no++;
                          } ?>
                        </table>
                      <?php
                        }
                      ?>

                      <?php
                        $no=1;
                        if ($reqRekananTipeId == '7') { // Perorangan ?>
                        <h4>Data Perpajakan</h4>
                        <table class="table table-striped">
                          <?php
                            while($rekanan_perpajakan->nextRow())
                            {
                              $checked = '';
                              $pesan = '';
                              $cekData = new Rekanan();
                              $cekData->selectByParamsRekananChecklist(array("REKANANID"=>$reqId),-1,-1);
                              $cekData->firstRow();
                              $field = strtoupper($rekanan_perpajakan->getField("FIELDNYA"));
                              if ($cekData->getField("$field") == '1') {
                                $checked = 'checked';
                              } else {
                                if ($cekData->getField("$field"."_NOTE")) {
                                  $pesan = '<span class="badge badge-danger"><small>'.$cekData->getField("$field"."_NOTE").'</small></span>';
                                }
                              }
                          ?>
                              <tr>
                                <td style="width: 2%"><?=$no;?></td>
                                <td style="width: 83%">
                                  <?=$rekanan_perpajakan->getField("NAMA") ?>
                                   <?php
                                  if ($rekanan_perpajakan->getField("WAJIB") == '*') {
                                     echo '<span class="color:red">'.$rekanan_perpajakan->getField("WAJIB").'</span>';
                                  } else {
                                    echo "";
                                  } 
                                  echo "<br>".$pesan; ?>
                                </td>
                                <td style="width: 15%" class="text-center">
                                  <img class="simbol" src="images/<?=$rekanan_perpajakan->getField("SIMBOL")?>.png">
                                </td>
                                <td style="width: 5%" class="text-center">
                                  <?php
                                  if ($this->USER_TYPE_ID == '2' && ($reqStatusValidasi != '1' || $reqUserStatus == '0'))
                                  {
                                    // if ($rekanan_perpajakan->getField("SIMBOL") == 'centang' && $rekanan_perpajakan->getField("WAJIB") == '*') {
                                       echo '<input type="checkbox" name="check'.$rekanan_perpajakan->getField("FIELDNYA").'" id="check'.$rekanan_perpajakan->getField("FIELDNYA").'" onclick="return updateChecklist(\''.$reqId.'\',\''.$rekanan_perpajakan->getField("FIELDNYA").'\')" style="cursor:pointer" '.$checked.'>';
                                    // }
                                  }?>
                                </td>
                              </tr>
                                <?php
                             if($rekanan_perpajakan->getField("SIMBOL") == "uncentang" && $rekanan_perpajakan->getField("WAJIB") == '*')
                              $jumlahUncentang++;
                            $no++;
                            } ?>
                          </table>
                        <?php
                        } else { ?>
                        <h4>Data Perpajakan</h4>
                        <table class="table table-striped">
                        <?php
                          while($rekanan_perpajakan->nextRow())
                          {
                        ?>
                            <tr>
                              <td style="width: 2%"><?=$no;?></td>
                              <td style="width: 83%">
                                <?=$rekanan_perpajakan->getField("NAMA") ?>
                                 <?php
                                if ($rekanan_perpajakan->getField("WAJIB") == '*') {
                                   echo '<span class="color:red">'.$rekanan_perpajakan->getField("WAJIB").'</span>';
                                } else {
                                  echo "";
                                } ?>
                              </td>
                              <td style="width: 15%" class="text-center">
                                <img class="simbol" src="images/<?=$rekanan_perpajakan->getField("SIMBOL")?>.png">
                              </td>
                            </tr>
                              <?php
                           if($rekanan_perpajakan->getField("SIMBOL") == "uncentang" && $rekanan_perpajakan->getField("WAJIB") == '*')
                            $jumlahUncentang++;
                          $no++;
                          } ?>
                        </table>
                      <?php
                        }
                      ?>

                      <?php
                        $no=1;
                        if ($reqRekananTipeId == '7') { // Perorangan ?>
                        <h4>Data Teknis</h4>
                        <table class="table table-striped">
                          <?php
                            while($rekanan_teknis->nextRow())
                            {
                              $checked = '';
                              $pesan = '';
                              $cekData = new Rekanan();
                              $cekData->selectByParamsRekananChecklist(array("REKANANID"=>$reqId),-1,-1);
                              $cekData->firstRow();
                              $field = strtoupper($rekanan_teknis->getField("FIELDNYA"));
                              if ($cekData->getField("$field") == '1') {
                                $checked = 'checked';
                              }  else {
                                if ($cekData->getField("$field"."_NOTE")) {
                                  $pesan = '<span class="badge badge-danger"><small>'.$cekData->getField("$field"."_NOTE").'</small></span>';
                                }
                              }
                          ?>
                              <tr>
                                <td style="width: 2%"><?=$no;?></td>
                                <td style="width: 83%">
                                  <?=$rekanan_teknis->getField("NAMA") ?>
                                   <?php
                                  if ($rekanan_teknis ->getField("WAJIB") == '*') {
                                     echo '<span class="color:red">'.$rekanan_teknis->getField("WAJIB").'</span>';
                                  } else {
                                    echo "";
                                  } 
                                  echo "<br>".$pesan; ?>
                                </td>
                                <td style="width: 15%" class="text-center">
                                  <img class="simbol" src="images/<?=$rekanan_teknis->getField("SIMBOL")?>.png">
                                </td>
                                <td style="width: 5%" class="text-center">
                                <?php
                                if ($this->USER_TYPE_ID == '2' && ($reqStatusValidasi != '1' || $reqUserStatus == '0'))
                                {
                                  // if ($rekanan_teknis->getField("SIMBOL") == 'centang' && $rekanan_teknis->getField("WAJIB") == '*') {
                                     echo '<input type="checkbox" name="check'.$rekanan_teknis->getField("FIELDNYA").'" id="check'.$rekanan_teknis->getField("FIELDNYA").'" onclick="return updateChecklist(\''.$reqId.'\',\''.$rekanan_teknis->getField("FIELDNYA").'\')" style="cursor:pointer" '.$checked.'>';
                                  // }
                                }?>
                              </td>
                              </tr>
                                <?php
                             if($rekanan_teknis->getField("SIMBOL") == "uncentang" && $rekanan_teknis->getField("WAJIB") == '*')
                              $jumlahUncentang++;
                            $no++;
                            } ?>
                          </table>
                        <?php
                        } else { ?>
                       <h4>Data Teknis</h4>
                       <table class="table table-striped">
                        <?php
                          while($rekanan_teknis->nextRow())
                          {
                        ?>
                            <tr>
                              <td style="width: 2%"><?=$no;?></td>
                              <td style="width: 83%">
                                <?=$rekanan_teknis->getField("NAMA") ?>
                                 <?php
                                if ($rekanan_teknis ->getField("WAJIB") == '*') {
                                   echo '<span class="color:red">'.$rekanan_teknis  ->getField("WAJIB").'</span>';
                                } else {
                                  echo "";
                                } ?>
                              </td>
                              <td style="width: 15%" class="text-center">
                                <img class="simbol" src="images/<?=$rekanan_teknis->getField("SIMBOL")?>.png">
                              </td>
                            </tr>
                              <?php
                           if($rekanan_teknis->getField("SIMBOL") == "uncentang" && $rekanan_teknis->getField("WAJIB") == '*')
                            $jumlahUncentang++;
                          $no++;
                          } ?>
                        </table>
                      <?php
                        }
                      ?>

                      <!--
                      ************************************************
                      ************** BUKAN PERORANGAN ****************
                      ************************************************
                      -->

                      <?php
                      if ($jumlahUncentang > 0) {
                        echo '<div class="alert alert-danger">
                                <b> Kurang '.$jumlahUncentang.' data belum dilengkapi (tanda * data wajib diisi)  </b>
                              </div>';
                        }
                      } else { // bukan perorangan ?>

                      <?php
                          $jumlahUncentang = 0;
                          $rekanan = new Rekanan();
                          $rekanan_keuangan = new Rekanan();
                          $rekanan_perpajakan = new Rekanan();
                          $rekanan_teknis = new Rekanan();
                          $rekanan_pakta_integritas = new Rekanan();

                          if ($reqRekananTipeId == '7') { // Perorangan
                            $rekanan->selectByParamsKonfirmasiPerorangan($reqId);
                            $rekanan_perpajakan->selectByParamsKonfirmasiPeroranganDataPerpajakan($reqId);
                            $rekanan_teknis->selectByParamsKonfirmasiPeroranganDataTeknis($reqId);
                          } else {
                            $rekanan->selectByParamsKonfirmasiDataAdmin($reqId);
                            $rekanan_keuangan->selectByParamsKonfirmasiDataKeuangan($reqId);
                            $rekanan_perpajakan->selectByParamsKonfirmasiDataPerpajakan($reqId);
                            $rekanan_teknis->selectByParamsKonfirmasiDataTeknis($reqId);
                          }
                            $rekanan_pakta_integritas->selectByParamsKonfirmasiPaktaIntegritas($reqId);
                          $no=1;
                          if ($reqRekananTipeId == '7') { // Perorangan ?>
                          <h4>Data Administrasi</h4>
                          <table class="table table-striped">
                          <?php
                            while($rekanan->nextRow())
                            {
                          ?>
                              <tr>
                                <td style="width: 2%"><?=$no;?></td>
                                <td style="width: 83%">
                                  <?=$rekanan->getField("NAMA") ?>
                                  <?php
                                  if ($rekanan->getField("WAJIB") == '*') {
                                     echo '<span class="color:red">'.$rekanan->getField("WAJIB").'</span>';
                                  } else {
                                    echo "";
                                  } ?>
                                </td>
                                <td style="width: 15%" class="text-center">
                                  <img class="simbol" src="images/<?=$rekanan->getField("SIMBOL")?>.png">
                                </td>
                                <td style="width: 5%" class="text-center">
                                <?php
                                if ($this->USER_TYPE_ID == '2' && ($reqStatusValidasi != '1' || $reqUserStatus == '0'))
                                {
                                  $checked = '';
                                  $cekData = new Rekanan();
                                  $cekData->selectByParamsRekananChecklist(array("REKANANID"=>$reqId),-1,-1);
                                  $cekData->firstRow();
                                  $field = strtoupper($rekanan->getField("FIELDNYA"));
                                  if ($cekData->getField("$field") == '1') {
                                    $checked = 'checked';
                                  }
                                  // if ($rekanan->getField("SIMBOL") == 'centang' && $rekanan->getField("WAJIB") == '*') {
                                     echo '<input type="checkbox" name="check'.$rekanan->getField("FIELDNYA").'" id="check'.$rekanan->getField("FIELDNYA").'" onclick="return updateChecklist(\''.$reqId.'\',\''.$rekanan->getField("FIELDNYA").'\')" style="cursor:pointer" '.$checked.'>';
                                  // }
                                }?>
                              </td>
                              </tr>
                                <?php
                             if($rekanan->getField("SIMBOL") == "uncentang" && $rekanan->getField("WAJIB") == '*')
                              $jumlahUncentang++;
                            $no++;
                            } ?>
                        </table>
                          <?php
                          } else {  ?>
                          <h4>Data Administrasi</h4>
                          <table class="table table-striped">
                          <?php
                            $this->load->model("Rekanan");
                            while($rekanan->nextRow())
                            {
                              $checked = '';
                              $pesan = '';
                              $cekData = new Rekanan();
                              $cekData->selectByParamsRekananChecklist(array("REKANANID"=>$reqId),-1,-1);
                              $cekData->firstRow();
                              $field = strtoupper($rekanan->getField("FIELDNYA"));
                              if ($cekData->getField("$field") == '1') {
                                $checked = 'checked';
                              } else {
                                if ($cekData->getField("$field"."_NOTE")) {
                                  $pesan = '<span class="badge badge-danger"><small>'.$cekData->getField("$field"."_NOTE").'</small></span>';
                                }
                              }
                          ?>
                              <tr>
                                <td style="width: 2%"><?=$no;?></td>
                                <td style="width: 83%">
                                  <?=$rekanan->getField("NAMA") ?>
                                  <?php
                                  if ($rekanan->getField("WAJIB") == '*') {
                                     echo '<span class="color:red">'.$rekanan->getField("WAJIB").'</span>';
                                  } else {
                                    echo "";
                                  } 
                                  echo "<br>".$pesan;
                                  ?>
                                </td>
                                <td style="width: 5%" class="text-center">
                                  <img class="simbol" src="images/<?=$rekanan->getField("SIMBOL")?>.png">
                                </td>
                                <td style="width: 5%" class="text-center">
                                  <?php 
                                  // echo $this->USER_TYPE_ID.'--'.$reqStatusValidasi.'--'.$reqUserStatus;
                                  if ($this->USER_TYPE_ID == '2' && ($reqStatusValidasi != '1' || $reqUserStatus == '0'))
                                  {
                                    // if ($rekanan->getField("SIMBOL") == 'centang' && $rekanan->getField("WAJIB") == '*') {
                                       echo '<input type="checkbox" name="check'.$rekanan->getField("FIELDNYA").'" id="check'.$rekanan->getField("FIELDNYA").'" onclick="return updateChecklist(\''.$reqId.'\',\''.$rekanan->getField("FIELDNYA").'\')" style="cursor:pointer" '.$checked.'>';
                                    // }
                                  }?>
                                </td>
                              </tr>
                                <?php
                             if($rekanan->getField("SIMBOL") == "uncentang" && $rekanan->getField("WAJIB") == '*')
                              $jumlahUncentang++;
                            $no++;
                            } ?>
                        </table>
                      <?php
                          }
                        ?>

                        <?php
                          $no=1;
                          if ($reqRekananTipeId == '7') { // Perorangan
                          } else {  ?>
                          <h4>Data Keuangan</h4>
                          <table class="table table-striped">
                          <?php
                            while($rekanan_keuangan->nextRow())
                            {
                              $checked = '';
                              $pesan = '';
                              $cekData = new Rekanan();
                              $cekData->selectByParamsRekananChecklist(array("REKANANID"=>$reqId),-1,-1);
                              $cekData->firstRow();
                              $field = strtoupper($rekanan_keuangan->getField("FIELDNYA"));
                              if ($cekData->getField("$field") == '1') {
                                $checked = 'checked';
                              } else {
                                if ($cekData->getField("$field"."_NOTE")) {
                                  $pesan = '<span class="badge badge-danger"><small>'.$cekData->getField("$field"."_NOTE").'</small></span>';
                                }
                              }
                          ?>
                              <tr>
                                <td style="width: 2%"><?=$no;?></td>
                                <td style="width: 83%">
                                  <?=$rekanan_keuangan->getField("NAMA") ?>
                                 <?php
                                  if ($rekanan_keuangan->getField("WAJIB") == '*') {
                                     echo '<span class="color:red">'.$rekanan_keuangan->getField("WAJIB").'</span>';
                                  } else {
                                    echo "";
                                  }
                                  echo "<br>".$pesan; ?>
                                </td>
                                <td style="width: 5%" class="text-center">
                                  <img class="simbol" src="images/<?=$rekanan_keuangan->getField("SIMBOL")?>.png">
                                </td>
                                <td style="width: 5%" class="text-center">
                                <?php
                                if ($this->USER_TYPE_ID == '2' && ($reqStatusValidasi != '1' || $reqUserStatus == '0'))
                                {
                                  // if ($rekanan_keuangan->getField("SIMBOL") == 'centang' && $rekanan_keuangan->getField("WAJIB") == '*') {
                                     echo '<input type="checkbox" name="check'.$rekanan_keuangan->getField("FIELDNYA").'" id="check'.$rekanan_keuangan->getField("FIELDNYA").'" onclick="return updateChecklist(\''.$reqId.'\',\''.$rekanan_keuangan->getField("FIELDNYA").'\')" style="cursor:pointer" '.$checked.'>';
                                  // }
                                }?>
                              </td>
                              </tr>
                                <?php
                             if($rekanan_keuangan->getField("SIMBOL") == "uncentang" && $rekanan_keuangan->getField("WAJIB") == '*')
                              $jumlahUncentang++;
                            $no++;
                            } ?>
                          </table>
                        <?php
                          }
                        ?>

                        <?php
                          $no=1;
                          if ($reqRekananTipeId == '7') { // Perorangan ?>
                          <h4>Data Perpajakan</h4>
                          <table class="table table-striped">
                          <?php
                            while($rekanan_perpajakan->nextRow())
                            {
                          ?>
                              <tr>
                                <td style="width: 2%"><?=$no;?></td>
                                <td style="width: 83%">
                                  <?=$rekanan_perpajakan->getField("NAMA") ?>
                                   <?php
                                  if ($rekanan_perpajakan->getField("WAJIB") == '*') {
                                     echo '<span class="color:red">'.$rekanan_perpajakan->getField("WAJIB").'</span>';
                                  } else {
                                    echo "";
                                  } ?>
                                </td>
                                <td style="width: 5%" class="text-center">
                                  <img class="simbol" src="images/<?=$rekanan_perpajakan->getField("SIMBOL")?>.png">
                                </td>
                              </tr>
                              <td style="width: 5%" class="text-center">
                                <?php
                                if ($this->USER_TYPE_ID == '2' && ($reqStatusValidasi != '1' || $reqUserStatus == '0'))
                                {
                                  $checked = '';
                                  $cekData = new Rekanan();
                                  $cekData->selectByParamsRekananChecklist(array("REKANANID"=>$reqId),-1,-1);
                                  $cekData->firstRow();
                                  $field = strtoupper($rekanan_perpajakan->getField("FIELDNYA"));
                                  if ($cekData->getField("$field") == '1') {
                                    $checked = 'checked';
                                  }
                                  // if ($rekanan_perpajakan->getField("SIMBOL") == 'centang' && $rekanan_perpajakan->getField("WAJIB") == '*') {
                                     echo '<input type="checkbox" name="check'.$rekanan_perpajakan->getField("FIELDNYA").'" id="check'.$rekanan_perpajakan->getField("FIELDNYA").'" onclick="return updateChecklist(\''.$reqId.'\',\''.$rekanan_perpajakan->getField("FIELDNYA").'\')" style="cursor:pointer" '.$checked.'>';
                                  // }
                                }?>
                              </td>
                                <?php
                             if($rekanan_perpajakan->getField("SIMBOL") == "uncentang" && $rekanan_perpajakan->getField("WAJIB") == '*')
                              $jumlahUncentang++;
                            $no++;
                            } ?>
                          </table>
                          <?php
                          } else { ?>
                          <h4>Data Perpajakan</h4>
                          <table class="table table-striped">
                          <?php
                            while($rekanan_perpajakan->nextRow())
                            {
                              $checked = '';
                              $pesan = '';
                              $cekData = new Rekanan();
                              $cekData->selectByParamsRekananChecklist(array("REKANANID"=>$reqId),-1,-1);
                              $cekData->firstRow();
                              $field = strtoupper($rekanan_perpajakan->getField("FIELDNYA"));
                              if ($cekData->getField("$field") == '1') {
                                $checked = 'checked';
                              } else {
                                if ($cekData->getField("$field"."_NOTE")) {
                                  $pesan = '<span class="badge badge-danger"><small>'.$cekData->getField("$field"."_NOTE").'</small></span>';
                                }
                              }
                          ?>
                              <tr>
                                <td style="width: 2%"><?=$no;?></td>
                                <td style="width: 83%">
                                  <?=$rekanan_perpajakan->getField("NAMA") ?>
                                   <?php
                                  if ($rekanan_perpajakan->getField("WAJIB") == '*') {
                                     echo '<span class="color:red">'.$rekanan_perpajakan->getField("WAJIB").'</span>';
                                  } else {
                                    echo "";
                                  } 
                                  echo "<br>".$pesan; ?>
                                </td>
                                <td style="width: 5%" class="text-center">
                                  <img class="simbol" src="images/<?=$rekanan_perpajakan->getField("SIMBOL")?>.png">
                                </td>
                                <td style="width: 5%" class="text-center">
                                <?php
                                if ($this->USER_TYPE_ID == '2' && ($reqStatusValidasi != '1' || $reqUserStatus == '0'))
                                {
                                  // if ($rekanan_perpajakan->getField("SIMBOL") == 'centang' && $rekanan_perpajakan->getField("WAJIB") == '*') {
                                     echo '<input type="checkbox" name="check'.$rekanan_perpajakan->getField("FIELDNYA").'" id="check'.$rekanan_perpajakan->getField("FIELDNYA").'" onclick="return updateChecklist(\''.$reqId.'\',\''.$rekanan_perpajakan->getField("FIELDNYA").'\')" style="cursor:pointer" '.$checked.'>';
                                  // }
                                }?>
                              </td>
                              </tr>
                                <?php
                             if($rekanan_perpajakan->getField("SIMBOL") == "uncentang" && $rekanan_perpajakan->getField("WAJIB") == '*')
                              $jumlahUncentang++;
                            $no++;
                            } ?>
                          </table>
                        <?php
                          }
                        ?>

                        <?php
                          $no=1;
                          if ($reqRekananTipeId == '7') { // Perorangan ?>
                          <h4>Data Teknis</h4>
                         <table class="table table-striped">
                          <?php
                            while($rekanan_teknis->nextRow())
                            {
                          ?>
                              <tr>
                                <td style="width: 2%"><?=$no;?></td>
                                <td style="width: 83%">
                                  <?=$rekanan_teknis->getField("NAMA") ?>
                                   <?php
                                  if ($rekanan_teknis ->getField("WAJIB") == '*') {
                                     echo '<span class="color:red">'.$rekanan_teknis->getField("WAJIB").'</span>';
                                  } else {
                                    echo "";
                                  } ?>
                                </td>
                                <td style="width: 5%" class="text-center">
                                  <img class="simbol" src="images/<?=$rekanan_teknis->getField("SIMBOL")?>.png">
                                </td>
                                <td style="width: 5%" class="text-center">
                                <?php
                                if ($this->USER_TYPE_ID == '2' && ($reqStatusValidasi != '1' || $reqUserStatus == '0'))
                                {
                                  $checked = '';
                                  $cekData = new Rekanan();
                                  $cekData->selectByParamsRekananChecklist(array("REKANANID"=>$reqId),-1,-1);
                                  $cekData->firstRow();
                                  $field = strtoupper($rekanan_teknis->getField("FIELDNYA"));
                                  if ($cekData->getField("$field") == '1') {
                                    $checked = 'checked';
                                  }
                                  // if ($rekanan_teknis->getField("SIMBOL") == 'centang' && $rekanan_teknis->getField("WAJIB") == '*') {
                                     echo '<input type="checkbox" name="check'.$rekanan_teknis->getField("FIELDNYA").'" id="check'.$rekanan_teknis->getField("FIELDNYA").'" onclick="return updateChecklist(\''.$reqId.'\',\''.$rekanan_teknis->getField("FIELDNYA").'\')" style="cursor:pointer" '.$checked.'>';
                                  // }
                                }?>
                              </td>
                              </tr>
                                <?php
                             if($rekanan_teknis->getField("SIMBOL") == "uncentang" && $rekanan_teknis->getField("WAJIB") == '*')
                              $jumlahUncentang++;
                            $no++;
                            } ?>
                          </table>
                          <?php
                          } else { ?>
                         <h4>Data Teknis</h4>
                         <table class="table table-striped">
                          <?php
                            while($rekanan_teknis->nextRow())
                            {
                              $checked = '';
                              $pesan = '';
                              $cekData = new Rekanan();
                              $cekData->selectByParamsRekananChecklist(array("REKANANID"=>$reqId),-1,-1);
                              $cekData->firstRow();
                              $field = strtoupper($rekanan_teknis->getField("FIELDNYA"));
                              if ($cekData->getField("$field") == '1') {
                                $checked = 'checked';
                              } else {
                                if ($cekData->getField("$field"."_NOTE")) {
                                  $pesan = '<span class="badge badge-danger"><small>'.$cekData->getField("$field"."_NOTE").'</small></span>';
                                }
                              }
                          ?>
                              <tr>
                                <td style="width: 2%"><?=$no;?></td>
                                <td style="width: 83%">
                                  <?=$rekanan_teknis->getField("NAMA") ?>
                                   <?php
                                  if ($rekanan_teknis ->getField("WAJIB") == '*') {
                                     echo '<span class="color:red">'.$rekanan_teknis->getField("WAJIB").'</span>';
                                  } else {
                                    echo "";
                                  } 
                                  echo "<br>".$pesan; ?>
                                </td>
                                <td style="width: 5%" class="text-center">
                                  <img class="simbol" src="images/<?=$rekanan_teknis->getField("SIMBOL")?>.png">
                                </td>
                                <td style="width: 5%" class="text-center">
                                <?php
                                if ($this->USER_TYPE_ID == '2' && ($reqStatusValidasi != '1' || $reqUserStatus == '0'))
                                {
                                  // if ($rekanan_teknis->getField("SIMBOL") == 'centang' && $rekanan_teknis->getField("WAJIB") == '*') {
                                     echo '<input type="checkbox" name="check'.$rekanan_teknis->getField("FIELDNYA").'" id="check'.$rekanan_teknis->getField("FIELDNYA").'" onclick="return updateChecklist(\''.$reqId.'\',\''.$rekanan_teknis->getField("FIELDNYA").'\')" style="cursor:pointer" '.$checked.'>';
                                  // }
                                }?>
                              </td>
                              </tr>
                                <?php
                             if($rekanan_teknis->getField("SIMBOL") == "uncentang" && $rekanan_teknis->getField("WAJIB") == '*')
                              $jumlahUncentang++;
                            $no++;
                            } ?>
                          </table>
                        <?php
                          }
                        ?>


                        <?php
                        if ($jumlahUncentang > 0) {
                           echo '<div class="alert alert-danger">
                                  <b> Kurang '.$jumlahUncentang.' data belum dilengkapi (tanda * data wajib diisi)  </b>
                                </div>';
                        } ?>

                      <?php
                      }

                      if($this->USER_TYPE_ID == 2 && $jumlahUncentang == 0) // Admin VMS
                      {
                      ?>
                      <form id="ff" class="easyui-form form-horizontal" method="post" data-options="novalidate:true" enctype="multipart/form-data">
                        <div class="form-actions text-center">
                          <input type="hidden" name="reqNomorValidasi" value="<?=$reqKode?>">
                          <input type="hidden" name="reqEmail" value="<?=$tempMail?>">
                          <input type="hidden" name="reqId" value="<?=$reqId?>">
                          <input type="hidden" name="reqRekananNama" value="<?=$tempRekananNama?>">
                          <input type="hidden"  name="submitSimpan" value="Simpan" />
                          <?php
                          $user_login->selectByParams(array("REKANAN_ID" => $reqId));
                          $user_login->firstRow();
                          $user_status = $user_login->getField("USER_STATUS");
                          if($tempStatusValidasi == 1)
                          { ?>
                          <a id="btnSertifikat" target="_blank" href="main/loadUrl/report/vms_pdf/?reqId=<?=$reqId?>&kode=<?=$reqKode?>" onclick="if(confirm('Cetak Surat Keterangan Terdaftar (SKT) ?')) { return true; } else { return false; }" class="<?= CLASS_BTN_PRIMARY ?>"><i class="fa fa-print"></i> Cetak Surat Keterangan Terdaftar (SKT)</a>

                          <?php
                          $this->load->model("Dokumenrekanan");
                          $dokumen_rekanan = new Dokumenrekanan();
                          $dokumen_rekanan->selectByParams(array('REKANAN_ID' => $reqId));
                           ?>
                          <?php
                            if ($dokumen_rekanan->countRow() > 0) {
                              // $dokumen_rekanan->firstRow();
                              // echo '<a style="color:#fff" href="uploads/pakta_integritas/'.$dokumen_rekanan->getField('PATH_FILE').'" target="_blank" class="'.CLASS_BTN_SUCCESS.'"><span class="fa fa-book"></span> Pakta Integritas</a>';
                            } else { }
                          ?>

                          <?php
                          } else if($tempStatusValidasi == 3) { // Posisi di user REKOMENDASI VMS
                            echo '<a class="btn btn-success mr-1 text-white" ><i class="fa fa-info-circle"></i> MENUNGGU APPROVAL PENYELIA</a>';
                          } else if($tempStatusValidasi == 4) { // Posisi di user APPROVAL VMS
                            echo '<a class="btn btn-info mr-1 text-white" ><i class="fa fa-info-circle"></i> MENUNGGU APPROVAL VMS </a>';
                          } else if ($tempStatusValidasi == 10 || $tempStatusValidasi == 0) { // Tolak, Melengkapi Data
                            if($this->USER_TYPE_ID == '2')
                            {
                              echo '<input name="reqSetuju" type="checkbox" id="chk_agreement" accesskey="e" value="1" style="cursor: pointer;" /> Dengan ini saya menyatakan bahwa data-data penyedia sudah benar dan dapat diteruskan untuk dilakukan approval. <br><br>';
                              echo '<div id="reqSubmit" style="display:none">';
                              echo '<label id="note1">Catatan</label><input type="text" class="form-control easyui-validatebox" id="note11" name="reqNote1" placeholder="Ketik disini" style="margin-bottom:2%" required>';
                              echo '<button type="submit" class="'.CLASS_BTN_PRIMARY.'" id="btnValidasi"><i class="fa fa-check-square-o"></i> KIRIM KE APPROVAL VMS</button>';
                              echo '</div>';
                            }
                          }
                          ?>
                            <!-- <a href="main/index/validasi" class="btn btn-danger mr-1 text-white"> <i class="fa fa-arrow-left"></i> Kembali </a>  -->

                        </div>
                      </form>
                      <?php
                      } ?>
                    </div>
                  </div>
                </div>
              </div>
            </div>
        </div>
      </div>
    </div>
  </div>

    <script src="lib/bootstrap/libs/jquery/1.12.4/jquery.min.js"></script>
    <script>window.jQuery || document.write('<script src="lib/bootstrap/assets/js/vendor/jquery.min.js"><\/script>')</script>
    <script src="lib/bootstrap/dist/js/bootstrap.min.js"></script>
    <link rel="stylesheet" type="text/css" href="<?=base_url()?>lib/eproc/themes/default/easyui.css">
    <script type="text/javascript" src="<?=base_url()?>lib/eproc/jquery.easyui.min.js"></script>
    <script type="text/javascript" src="<?=base_url()?>lib/eproc/kalender-easyui.js"></script>
    <script type="text/javascript" src="<?=base_url()?>lib/eproc/allfunc.js"></script>

    <script type="text/javascript">
      $(document).ready(function() {
        $("#chk_agreement").click(countChecked);
      });

      function countChecked() {
        openBtn();
          // var n = $("#chk_agreement:checked").length;
      }

      function updateChecklist(rekananid,jenis) {
        var n = $("#check"+jenis+":checked").length;
          $.getJSON("rekanan_json/updateChecklist/?rekananid="+rekananid+"&jenis="+jenis+"&status="+n,
            function(data){
              if (data.RESPONSE === 'Gagal') {
                $.messager.alert('Info', data.PESAN, 'info');
                if (n === 0) { // kalau gagal balik ke awal
                  $("#check"+jenis).prop("checked", true);
                } else {
                  $("#check"+jenis).prop("checked", false);
                }
              }
              location.reload();
          });
          openBtn();
        // alert(rekananid+'-'+jenis);
      }

      function openBtn() {
        var numberNotChecked = $('input:checkbox:not(":checked")').length;
        if (numberNotChecked == 0) {
          $("#reqSubmit").show(0);
        }else{
          $("#reqSubmit").hide(0);
        }
      }
    </script>


  </body>
</html>
