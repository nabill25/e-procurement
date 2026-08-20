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
$this->load->model("Katalogfoto");
$this->load->model("Kataloglampiran");
$this->load->model("Katalogkategorirekanan");
include_once("functions/string.func.php");
include_once("functions/date.func.php");
include_once("functions/default.func.php");

/* create objects */
$katalog = new Katalog();
$katalog_foto = new Katalogfoto();
$katalog_lapiran = new Kataloglampiran();
$katalog_kategori_rekanan = new Katalogkategorirekanan;

/* VARIABLE */
$id  = $this->input->get("reqId");


$arrStatement = array('A.KATALOGID' => $id);
$katalog->selectByParamsViewKatalog($arrStatement, -1, -1);
$katalog->firstRow();
$katalogid = $katalog->getField("KATALOGID");

$Katalogfoto = new Katalogfoto();
$Katalogfoto->selectByParams(array('KATALOGID' => $katalogid), -1, -1);
$katalog_kategori_rekanan->selectByParams(array('KATALOGID' => $katalogid), -1, -1);

if($Katalogfoto->countRow() > 0)
{
  while($Katalogfoto->nextRow())
  {
    $dataKatalogFoto[] =  'images/katalog/'.$Katalogfoto->getField("path_file");
  }
} else {
    $dataKatalogFoto =  array();
}
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
            <span class="alert-icon"><i class="fa fa-th"></i></span>  <strong>Katalog Detail</strong>
          </div>
          <div class="p-1" >
            <ul class="nav nav-tabs nav-underline">
              <li class="nav-item">
                <a class="nav-link" id="baseIcon-tabspec" data-toggle="tab" aria-controls="tabspec" href="#tabspec" aria-expanded="true"><i class="fa fa-cogs"></i> Spesifikasi</a>
              </li>
              <li class="nav-item">
                <a class="nav-link" id="baseIcon-tabsdesc" data-toggle="tab" aria-controls="tabsdesc" href="#tabsdesc" aria-expanded="true"><i class="fa fa-list"></i> Deskripsi Produk</a>
              </li>
              <li class="nav-item">
                <a class="nav-link" id="baseIcon-lamp" data-toggle="tab" aria-controls="lamp" href="#tabIcon42" aria-expanded="false"><i class="fa fa-file"></i> Lampiran</a>
              </li>
              <li class="nav-item">
                <a class="nav-link" id="baseIcon-lap" data-toggle="tab" aria-controls="lap" href="#lap" aria-expanded="false"><i class="fa fa-flag"></i> Gambar/Foto</a>
              </li>
            </ul>

            <div class="tab-content px-1 pt-1">
              <div role="tabpanel" class="tab-pane active mt-1" id="tabspec" aria-expanded="true" aria-labelledby="baseIcon-tabspec">
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
                          <td class="tdHead2">Panjang</td><td class="tdContent2"><?= $katalog->getField('PANJANG') ?> cm</td>
                        </tr>
                        <tr>
                          <td class="tdHead2">Lebar</td><td class="tdContent2"><?= $katalog->getField('LEBAR') ?> cm</td>
                          <td class="tdHead2">Tinggi</td><td class="tdContent2"><?= $katalog->getField('TINGGI') ?> cm</td>
                        </tr>
                      </table>
                     </td>
                  </tr>
                  <tr> <td class="tdHead">Tahun Pembuatan Produk</td><td class="tdContent"><?= $katalog->getField('UNITPENGUKURAN') ?></td></tr>
                  <tr> <td class="tdHead">TKDN</td><td class="tdContent"><?= $katalog->getField('TKDNPRODUK') ?></td></tr>
                  <tr> <td class="tdHead">Kemasan</td><td class="tdContent"><?= $katalog->getField('KEMASAN') ?></td></tr>
                  <tr> <td class="tdHead">Berlaku Sampai</td><td class="tdContent">
                    <?php $tglEx = explode(' ',$katalog->getField('BERLAKUSAMPAI')); echo getFormattedDate($tglEx[0]); ?></td>
                  </tr>
                  <tr> <td class="tdHead">Jenis Produk</td><td class="tdContent"><?php echo $katalog->getField('JENISPRODUK'); // if ($katalog->getField('JENISPRODUK') == '1') { echo "Lokal";} else { echo "Import";} ?></td></tr>
                  <tr> <td class="tdHead">Garansi</td><td class="tdContent"><?= $katalog->getField('LAMAGARANSI').' '.$katalog->getField('LAMAGARANSI2') ?></td></tr>
                  <tr> <td class="tdHead">Jumlah Stok</td>
                    <td class="tdContent">
                      <?php
                        echo $katalog->getField('JUMLAHSTOCK');
                      ?>
                    </td>
                  </tr>
                  <?php
                  if ($katalog->getField('JUMLAHSTOCK') == 'Tersedia') { ?>
                  <tr> <td class="tdHead">Waktu Pengiriman (hari)</td>
                    <td class="tdContent">
                      <?php
                        echo $katalog->getField('JUMLAHSTOCK_READY');
                      ?>
                    </td>
                  </tr>
                  <?php
                  } ?>
                  <!-- <tr> <td class="tdHead">Kemasan</td><td class="tdContent"><?php // $katalog->getField('KEMASAN') ?></td></tr> -->

                </tbody>
              </table>
              </div>
              <div role="tabpanel" class="tab-pane mt-1" id="tabsdesc" aria-expanded="true" aria-labelledby="baseIcon-tabsdesc">
                  <div class="media">
                      <div class="media-body pl-1 font14">
                          <?= $katalog->getField('KETERANGANTAMBAHAN') ?>
                      </div>
                  </div>
              </div>
              <div class="tab-pane mt-1" id="tabIcon42" aria-labelledby="baseIcon-lamp">
                <?php
                $this->load->model("Kataloglampiran");
                $Kataloglampiran = new Kataloglampiran();
                $Kataloglampiran_count = new Kataloglampiran();

                $Kataloglampiran->selectByParams(array('KATALOGID' => $katalogid));
                $contLampiran = $Kataloglampiran_count->getCountByParams(array('KATALOGID' => $katalogid));
                if ($Kataloglampiran->countRow() == 0) {
                  echo '<span style="color:red">Tidak ada Lampiran</span>';
                } else
                {
                  while($Kataloglampiran->nextRow())
                  {
                    if (file_exists('images/katalog/'.$Kataloglampiran->getField("path_file"))) {
                              $filenya = $Kataloglampiran->getField("path_file");
                            } else {
                              $filenya = '';
                            }
                  ?>
                    <a href="<?= 'images/katalog/'.$filenya ?>" target="_blank" class="btn btn-sm btn-social btn-block mb-1 btn-outline-yahoo"><i class="fa fa-download"></i> <?= $Kataloglampiran->getField('FILE') ?></a>
                <?php
                  }
                } ?>
              </div>
              <div class="tab-pane mt-1" id="lap" aria-labelledby="baseIcon-lap">
                 <div class="col-md-12">
                  <ul class="preview-thumbnail nav nav-tabs">
                    <?php
                    foreach ($dataKatalogFoto as $key3 => $value3) {
                      if (file_exists($value3)) {
                        echo '<li><img src="'.$value3.'" alt="eproc19.com"/></li>';
                      } else {
                      }
                    } ?>
                  </ul>
                </div>
              </div>
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
