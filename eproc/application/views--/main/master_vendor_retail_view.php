<?php
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 *
 */

 $this->load->model(array("Vendorretail","Rekanantipe","Region"));
 include_once("functions/string.func.php");
 include_once("functions/date.func.php");
 include_once("functions/default.func.php");

$vendor_retail = new Vendorretail();

$reqId	= $this->input->get("reqId") ?: '0';

$vendor_retail->selectByParams(array("REKANAN_RETAIL_ID" => $reqId));
$vendor_retail->firstRow();
$reqRekananTipe = $vendor_retail->getField("REKANAN_TIPE_ID");
$reqNama = $vendor_retail->getField("NAMA");
$reqNPWP = $vendor_retail->getField("NPWP");
$reqTeleponKode = $vendor_retail->getField("TELEPON_KODE");
$reqTelepon = $vendor_retail->getField("TELEPON");
$reqWhatsapp = $vendor_retail->getField("WHATSAPP");
$reqTanggalDaftar = $vendor_retail->getField("TANGGAL_DAFTAR");
$reqRegionId = $vendor_retail->getField("REGION_ID");
$reqKota = $vendor_retail->getField("KOTA");
$reqKontakPerson = $vendor_retail->getField("KONTAK_PERSON");
$reqKontakPersonHP = $vendor_retail->getField("KONTAK_PERSON_HP");
$reqAlamat = $vendor_retail->getField("ALAMAT");
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <base href="<?=base_url();?>" />
    <link rel="icon" href="../../favicon.ico">
    <title><?= SYSTEM_NAME_PT ?></title>
    <link rel="stylesheet" type="text/css" href="<?=base_url() ?>assets/new/css/vendors.css">
    <link rel="stylesheet" type="text/css" href="<?=base_url() ?>assets/new/vendors/css/ui/prism.min.css">
    <link rel="stylesheet" type="text/css" href="<?=base_url() ?>assets/new/css/app.css">
    <link rel="stylesheet" type="text/css" href="<?=base_url() ?>assets/new/css/core/menu/menu-types/horizontal-menu.css">
    <link rel="stylesheet" type="text/css" href="<?=base_url() ?>assets/new/css/core/colors/palette-gradient.css">
    <link rel="stylesheet" type="text/css" href="<?=base_url() ?>assets/new/css/core/colors/palette-callout.css">
    <link rel="stylesheet" type="text/css" href="<?=base_url() ?>assets/new/css/style.css">
    <script type="text/javascript" src="js/jquery-1.6.1.min.js"></script>
    <link rel="stylesheet" type="text/css" href="lib/drupal/index.css" />
    <script src="lib/emodal/eModal.js"></script>
    <script>
    </script>

  </head>

<body class="body-popup">

  <div class="card mb-1 border-darken-1">
    <div class="card-content">
      <div class="p-1">
        <div class="alert bg-primary alert-icon-left alert-arrow-left alert-dismissible mb-2" role="alert">
          <span class="alert-icon"><i class="fa fa-th"></i></span>  <strong>Vendor Retail</strong>
        </div>
        <div class="p-1">
          <table class="table table-bordered table-hover">
          <tbody>
            <tr>
              <td width="30%">Bentuk Usaha</td>
              <td>
                <?php
                $rekanantipe = new Rekanantipe();
                $rekanantipe->selectByParams(array("REKANAN_TIPE_ID" => $reqRekananTipe));
                $rekanantipe->firstRow();

              echo $rekanantipe->getField("NAMA") ?>
              </td>
            </tr>
            <tr>
              <td>Nama Perusahaan</td><td><?= $reqNama ?></td>
            </tr>
            <tr>
              <td>NPWP</td><td><?= $reqNPWP ?></td>
            </tr>
            <tr>
              <td>Telepon</td><td><?=$reqTelepon?> <?= $reqTeleponKode ?></td>
            </tr>
            <tr>
              <td>Whatsapp</td><td><?= $reqWhatsapp ?></td>
            </tr>
            <tr>
              <td>Tanggal Daftar</td><td><?= dateToPageCheck($reqTanggalDaftar) ?></td>
            </tr>
            <tr>
              <td>Provinsi</td>
              <td>
                <?php
                $region = new Region();
                $region->selectByParams(array("REGION_ID" => $reqRegionId));
                $region->firstRow();
                echo $region->getField("NAMA") ?>
              </td>
            </tr>
            <tr>
              <td>Kota</td><td><?= $reqKota ?></td>
            </tr>
            <tr>
              <td>Penanggung Jawab</td><td><?= $reqKontakPerson ?></td>
            </tr>
            <tr>
              <td>Nomor Kontak Penanggung Jawab</td><td><?= $reqKontakPersonHP ?></td>
            </tr>
            <tr>
              <td>Alamat</td><td><?= $reqAlamat ?></td>
            </tr>
          </tbody>

          </table>
        </div>
      </div>
    </div>
    </div>
  </body>
</html>
