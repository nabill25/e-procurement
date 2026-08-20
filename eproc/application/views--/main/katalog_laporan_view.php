<?php
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 * 
 */

/* INCLUDE FILE */
$this->load->library("kauth");  $userLogin = new kauth(); 
$this->load->model("Katalog");
$this->load->model("Kataloglaporan");
include_once("functions/string.func.php");
include_once("functions/date.func.php");
include_once("functions/default.func.php");

/* create objects */
$katalog = new Katalog();
$katalog_laporan = new Kataloglaporan();

/* VARIABLE */
$reqId	= $this->input->get("reqId");

$katalog_laporan->selectByParams(array("A.LAPORANID"=>$reqId),-1,-1);
$katalog_laporan->firstRow();

$arrStatement = array('A.KATALOGID' => $katalog_laporan->getField("KATALOGID"));
$katalog->selectByParams($arrStatement, -1, -1);
$katalog->firstRow();
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
    
    <!-- FONT AWESOME -->
    <link rel="stylesheet" href="lib/font-awesome-4.7.0/css/font-awesome.css" type="text/css">
    
    <script type="text/javascript" src="js/jquery-1.6.1.min.js"></script>
    <!-- PAGINATION -->
    <link rel="stylesheet" type="text/css" href="lib/drupal/index.css" /> 
  </head>
     <div class="card mb-1 border-darken-1">
      <div class="card-content">
        <div class="p-1">
          <div class="alert bg-primary alert-icon-left alert-arrow-left alert-dismissible mb-2" role="alert">
            <span class="alert-icon"><i class="fa fa-th"></i></span>  <strong>Katalog Laporan</strong>
          </div> 
          <div class="p-1" >
            <table class="table table-bordered">
              <tr>
                <td width="20%">Nama</td><td width="80%"><?= $katalog_laporan->getField("NAMA") ?></td>
              </tr>
              <tr>
                <td width="20%">Email</td><td width="80%"><?= $katalog_laporan->getField("EMAIL") ?></td>
              </tr>
              <tr>
                <td width="20%">Telepon</td><td width="80%"><?= $katalog_laporan->getField("TELEPON") ?></td>
              </tr>
              <tr>
                <td width="20%">Jenis Laporan</td><td width="80%"><?= $katalog_laporan->getField("JENISLAPORAN") ?></td>
              </tr>
              <tr>
                <td width="20%">Alasan</td><td width="80%"><?= $katalog_laporan->getField("ALASAN") ?></td>
              </tr>
              <tr>
                <td width="20%">Tanggal Laporan</td><td width="80%"><?= $katalog_laporan->getField("CREATED_DATE") ?></td>
              </tr>
              <tr>
                <td width="20%">Browser yang digunakan</td><td width="80%"><small><?= $katalog_laporan->getField("BROWSER") ?></small></td>
              </tr>
              </tr>
            </table>
            <hr>
            <h4 class="alert alert-info">Detail Katalog</h4>
            <table class="table table-bordered">
                <tbody>
                  <tr> <td class="tdHead">Nomor Produk</td><td class="tdContent"><?= $katalog->getField('NOPRODUK') ?></td></tr>
                  <tr> <td class="tdHead">Nama Produk</td><td class="tdContent"><?= $katalog->getField('NAMAPRODUK') ?></td></tr>
                  <tr> <td class="tdHead">Merek</td><td class="tdContent"><?= $katalog->getField('MEREK') ?></td></tr>
                  <tr> <td class="tdHead">Model/Type</td><td class="tdContent"><?= $katalog->getField('MODELTYPE') ?></td></tr>
                  <tr> <td class="tdHead">Dimensi</td>
                     <td class="tdContent">
                      <table class="table table-bordered">
                        <tr>
                          <td class="tdHead2">Diameter</td><td class="tdContent2"><?= $katalog->getField('DIAMETER') ?> cm</td>
                          <td class="tdHead2">Panjang</td><td class="tdContent2"><?= $katalog->getField('PANJANG') ?></td>
                        </tr>
                        <tr>
                          <td class="tdHead2">Lebar</td><td class="tdContent2"><?= $katalog->getField('LEBAR') ?> cm</td>
                          <td class="tdHead2">Tinggi</td><td class="tdContent2"><?= $katalog->getField('TINGGI') ?></td>
                        </tr>
                      </table>
                     </td>
                  </tr>
                  <tr> <td class="tdHead">Kemasan</td><td class="tdContent"><?= $katalog->getField('KEMASAN') ?></td></tr>
                  <tr> <td class="tdHead">Garansi</td><td class="tdContent"><?= $katalog->getField('LAMAGARANSI').' '.$katalog->getField('LAMAGARANSI2') ?></td></tr>
                  <!-- <tr> <td class="tdHead">No. Produk Penyedia</td><td class="tdContent"><?= $katalog->getField('NOPRODUKPENYEDIA') ?></td></tr> -->
                  <tr> <td class="tdHead">Tahun Pembuatan Produk</td><td class="tdContent"><?= $katalog->getField('UNITPENGUKURAN') ?></td></tr>
                  <tr> <td class="tdHead">Jenis Produk</td><td class="tdContent"><?php if ($katalog->getField('JENISPRODUK') == '1') { echo "Lokal";} else { echo "Import";} ?></td></tr>
                  <tr> <td class="tdHead">TKDN(%)</td><td class="tdContent"><?= $katalog->getField('TKDNPRODUK') ?></td></tr>
                  <tr> <td class="tdHead">Berlaku Sampai</td><td class="tdContent"><?= $katalog->getField('BERLAKUSAMPAI') ?></td></tr>
                  <!-- <tr> <td class="tdHead">Type</td><td class="tdContent"><?= $katalog->getField('MODELTYPE') ?></td></tr> -->
                  <!-- <tr> <td class="tdHead">No. Test Report</td><td class="tdContent"><?= $katalog->getField('NOMORTEST') ?></td></tr> -->
                </tbody>
          </div>
        </div>
      </div>
    </div>
    
    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="lib/bootstrap/libs/jquery/1.12.4/jquery.min.js"></script>
    <script>window.jQuery || document.write('<script src="lib/bootstrap/assets/js/vendor/jquery.min.js"><\/script>')</script>
    <script src="lib/bootstrap/dist/js/bootstrap.min.js"></script>
    
    <!-- EASYUI -->
    <link rel="stylesheet" type="text/css" href="<?=base_url()?>lib/eproc/themes/default/easyui.css">
    <script type="text/javascript" src="<?=base_url()?>lib/eproc/jquery.easyui.min.js"></script>
    <script type="text/javascript" src="<?=base_url()?>lib/eproc/kalender-easyui.js"></script>
    <script type="text/javascript" src="<?=base_url()?>lib/eproc/allfunc.js"></script>
    
  </body>
</html>
