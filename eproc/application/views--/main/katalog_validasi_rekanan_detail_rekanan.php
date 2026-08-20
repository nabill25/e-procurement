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
include_once("functions/string.func.php");
include_once("functions/date.func.php");
include_once("functions/default.func.php");

/* create objects */
$rekanan = new Rekanan();

/* VARIABLE */
$id  = $this->input->get("reqId"); 

$arrStatement = array('A.REKANAN_ID' => $id);
$rekanan->selectByParams($arrStatement, -1, -1);
$rekanan->firstRow(); 
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

    <link rel="stylesheet" type="text/css" href="<?=base_url() ?>assets/new/vendors/css/extensions/zoom.css">
    <script src="<?=base_url()?>assets/new/vendors/js/extensions/zoom.min.js"></script>
    
    <script type="text/javascript" src="js/jquery-1.6.1.min.js"></script>
    <!-- PAGINATION -->
    <link rel="stylesheet" type="text/css" href="lib/drupal/index.css" /> 
    <style type="text/css">
      .preview-thumbnail.nav-tabs {
        border: none;
        margin-top: 15px; }
      .preview-thumbnail.nav-tabs li {
        width: 20%;
        margin-right: 1%; }
      .preview-thumbnail.nav-tabs li img {
        max-width: 100%;
        display: block; }
      .preview-thumbnail.nav-tabs li a {
        padding: 0;
        margin: 0; }
      .preview-thumbnail.nav-tabs li:last-of-type {
        margin-right: 0; }
    </style>
  </head>
     <div class="card mb-1 border-darken-1">
      <div class="card-content">
        <div class="p-1">
          <div class="alert bg-primary alert-icon-left alert-arrow-left alert-dismissible mb-2" role="alert">
            <span class="alert-icon"><i class="fa fa-th"></i></span>  <strong>Perusahaan/Penyedia detail profil</strong>
          </div> 
          <div class="p-1" >   
                <table class="table table-bordered">
                  <tbody>
                    <tr> <td width="20%">Nama Perusahaan</td><td class="tdContent"><?= $rekanan->getField('REKANAN_NAMA') ?></td></tr> 
                    <tr> <td width="20%">NPWP</td><td class="tdContent"><?= $rekanan->getField('NPWP') ?></td></tr> 
                    <tr> <td width="20%">Alamat</td><td class="tdContent"><?= $rekanan->getField('ALAMAT') ?></td></tr> 
                    <tr> <td width="20%">Kota</td><td class="tdContent"><?= $rekanan->getField('KOTA') ?></td></tr> 
                    <tr> <td width="20%">Provinsi</td><td class="tdContent"><?= $rekanan->getField('REGION') ?></td></tr> 
                    <tr> <td width="20%">Kode Pos</td><td class="tdContent"><?= $rekanan->getField('KODEPOS') ?></td></tr> 
                    <tr> <td width="20%">No. Telepon</td><td class="tdContent"><?= $rekanan->getField('TELEPON') ?></td></tr> 
                    <tr> <td width="20%">No. Fax</td><td class="tdContent"><?= $rekanan->getField('FAX') ?></td></tr> 
                    <tr> <td width="20%">Email</td><td class="tdContent"><?= $rekanan->getField('EMAIL') ?></td></tr> 
                    <tr> <td width="20%">Website</td><td class="tdContent"><?= $rekanan->getField('WEBSITE') ?></td></tr> 
                    <tr> <td width="20%">Kualifikasi Usaha</td><td class="tdContent"><?= $rekanan->getField('KUALIFIKASI') ?></td></tr> 
                  </tbody>
                </table> 
            </div>
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
