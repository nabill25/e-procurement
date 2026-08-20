<?php
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 * 
 */

/* INCLUDE FILE */
$this->load->library("kauth");  $userLogin = new kauth();
$this->load->model(array("PaketPenilaian","Rekanan"));
include_once("functions/string.func.php");
include_once("functions/date.func.php");
include_once("functions/default.func.php");
include_once("functions/penilaian.func.php");

$reqId = httpFilterGet("reqId"); // rekanan_id
$reqPaketId = httpFilterGet("reqPaketId");

$getpaketpenilaian = new PaketPenilaian();
$paketpenilaianrekap = new PaketPenilaian();
$rekanan_get_nama = new Rekanan();

$getpaketpenilaian->getHasilByPaketPengadaan($reqId, -1, -1);

$rekanan_get_nama->selectByParams(array("A.REKANAN_ID"=>$reqId),-1,-1);
$rekanan_get_nama->firstRow();
$tempNama_getNama= $rekanan_get_nama->getField("NAMA");
$reqStatusValidasi = $rekanan_get_nama->getField("STATUS_VALIDASI");
$reqUserStatus = $rekanan_get_nama->getField("USER_STATUS");

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
    <script language="JavaScript" src="jslib/displayElement.js"></script>
  </head>

<body>

  <div class="card mb-1">
    <div class="card-content">
      <div class="p-1">
        <div class="alert bg-primary alert-icon-left alert-arrow-left alert-dismissible mb-2" role="alert">
          <span class="alert-icon"><i class="fa fa-th"></i></span>  <strong>PENILAIAN</strong>
        </div>
        <p class="alert alert-primary" style="background-color: transparent !important;"><b><?= $tempNama_getNama ?></b></p>
        <div class="table-responsive">
          <form action="" name="frmDaftarAlamat" method="post" enctype="multipart/form-data">
            <table width="100%" class="table table-bordered" border="0" cellpadding="2" cellspacing="1" id="tbl_bidang">
              <tbody>
                <tr class="judul-kolom">
                  <!-- <th style="width: 30%">Nama Pekerjaan </th> -->
                  <!-- <th style="width: 65%">Penilaian</th> -->
                </tr>
                <?php
                  // if($allRecord_K > 0){
                  $i = 1;
                  if ($getpaketpenilaian->countRow() > 0) {
                    while($getpaketpenilaian->nextRow())
                    {
                      $paketpenilaianrekap->getHasil($getpaketpenilaian->getField("CONTRACTINGREKANANID"),$reqId, -1, -1);
                    ?>
                      <tr style="background-color: #e3ebf3;">
                        <!-- <td width="5%"><?=$i?></td> -->
                        <td><h3><?=$getpaketpenilaian->getField("NAMA")?></h3></td>
                      </tr>
                      <tr>
                        <!-- <td></td> -->
                        <td>
                          <h4>Penilaian</h4>
                           <table class="table">
                              <tr class="tr">
                                <td class="td" align="center" valign="middle" width="7%">No.</td>
                                <td class="td" align="left" valign="middle" width="73%">Deskripsi Penilaian</td>
                                <td class="td" align="center" valign="middle" width="10%">Nilai</td>
                              </tr>
                              <?php
                              $noHasil=1;
                              $totalNilaiTampung = 0;
                              while ($paketpenilaianrekap->nextRow()) {
                                $totalNilaiTampung += $paketpenilaianrekap->getField("TOTAL_SKOR");
                               ?>
                              <tr>
                                <td class="td" align="center" valign="middle"><?=$noHasil?></td>
                                <td class="td" align="left" valign="middle"><?=$paketpenilaianrekap->getField("NAMA")?></td>
                                <td class="td" align="center" valign="middle"><?= round($paketpenilaianrekap->getField("TOTAL_SKOR"),2) ?></td>
                              </tr>
                              <?php $noHasil++;
                                } ?>
                              <tr class="tr-bc">
                                <td class="td" colspan="2" align="center" valign="middle">TOTAL</td>
                                <td class="td" align="center" valign="middle"><?= $totalNilaiTampung; ?></td>
                              </tr>
                              <tr class="tr-bc">
                                <td class="td" colspan="2" align="center" valign="middle">Kesimpulan Penilaian Akhir</td>
                                <td class="td" align="center" valign="middle">
                                  <?= setGrade($totalNilaiTampung); ?>
                                </td>
                              </tr>
                            </table>
                        </td>
                      </tr>
                    <?php
                      $i++;
                    }
                  } else
                  {
                  ?>
                    <tr>
                        <td colspan="4">.: Data belum ada :.</td>
                    </tr>
                  <?php
                    }
                  ?>
              </tbody>
            </table>

          </form>
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

  </body>
</html>
