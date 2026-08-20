<?php
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 * 
 */

include_once("functions/string.func.php");
include_once("functions/date.func.php");

$this->load->model("Blacklist");

$reqId = $this->input->get("reqId");

$blacklist = new Blacklist();

$blacklist->selectByParamsAll(array("BLACKLIST_ID"=> $reqId),-1,-1);
$blacklist->firstRow();
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
    <!-- END VENDOR CSS-->
    <!-- BEGIN ROBUST CSS-->
    <link rel="stylesheet" type="text/css" href="<?=base_url() ?>assets/new/css/app.css">
    <!-- <link rel="stylesheet" type="text/css" href="<?=base_url() ?>css/core.css"> -->
    <!-- END ROBUST CSS-->
    <!-- BEGIN Page Level CSS-->
    <link rel="stylesheet" type="text/css" href="<?=base_url() ?>assets/new/css/core/menu/menu-types/horizontal-menu.css">
    <link rel="stylesheet" type="text/css" href="<?=base_url() ?>assets/new/css/core/colors/palette-gradient.css">
    <link rel="stylesheet" type="text/css" href="<?=base_url() ?>assets/new/css/core/colors/palette-callout.css">
    <!-- END Page Level CSS-->
    <!-- BEGIN Custom CSS-->
    <link rel="stylesheet" type="text/css" href="<?=base_url() ?>assets/new/css/style.css">

    <script type="text/javascript" src="js/jquery-1.6.1.min.js"></script>

    <!-- PAGINATION -->
    <link rel="stylesheet" type="text/css" href="lib/drupal/index.css" />
    <script src="lib/emodal/eModal.js"></script>
<body class="body-popup">

<div class="card mb-1 border-darken-1">
  <div class="card-content">
    <div class="p-1">
      <div class="alert bg-primary alert-icon-left alert-arrow-left alert-dismissible mb-2" role="alert">
        <span class="alert-icon"><i class="fa fa-th"></i></span>  <strong>Blacklist Ditail</strong>
      </div>
      <div class="p-1">
        <form id="ff" class="form-horizontal" role="form" method="post" novalidate style="padding:0 50px">
          <table class="table table-bordered table-hover">
            <tbody>
              <tr>
                <td width="25%">Nama Perusahaan</td>
                <td width="75%"><?=$blacklist->getField("NAMA")?></td>
              </tr>
              <tr>
                <td width="25%">NPWP</td>
                <td width="75%"><?=$blacklist->getField("NPWP")?></td>
              </tr>
              <tr>
                <td width="25%">Alamat</td>
                <td width="75%"><?=$blacklist->getField("ALAMAT")?></td>
              </tr>
              <tr>
                <td width="25%">Kota</td>
                <td width="75%"><?=$blacklist->getField("KOTA")?></td>
              </tr>
              <tr>
                <td width="25%">No.SK</td>
                <td width="75%"><?=$blacklist->getField("NO_SK")?></td>
              </tr>
              <tr>
                <td width="25%">Tanggal</td>
                <td width="75%">
                  <?= getFormattedDate($blacklist->getField("TANGGAL_MULAI")).' s/d '.getFormattedDate($blacklist->getField("TANGGAL_SELESAI")); ?>
                  <?php
                  $tgl1 = new DateTime($blacklist->getField("TANGGAL_SELESAI"));
                  $tgl2 = new DateTime(date("Y-m-d"));
                  $d = $tgl1->diff($tgl2)->days;
                  if ($blacklist->getField("TANGGAL_SELESAI") >= date('Y-m-d')) {
                    echo '<span class="badge badge-primary">Blacklist sisa '.$d.' hari lagi</span>';
                  } else if ($d == 0) {
                    echo '<span class="badge badge-warning"> Blacklist selesai </span>';
                  }  ?>
                </td>
              </tr>
              <tr>
                <td width="25%">Alasan</td>
                <td width="75%"><?=$blacklist->getField("ALASAN")?></td>
              </tr>
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
