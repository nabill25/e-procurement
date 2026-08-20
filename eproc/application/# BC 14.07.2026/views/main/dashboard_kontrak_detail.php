<?php
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 * 
 */

$this->load->model(array("Dashcontractingui","Queryfree"));

include_once("functions/string.func.php");
include_once("functions/date.func.php");
include_once("functions/default.func.php");
/* create objects */
$dataNya = new Dashcontractingui();

$bulan = $this->input->get("bulan") ?: '';
$tahun = $this->input->get("tahun");
$unitkerjaid = $this->input->get("uki");
$user_login_id = $this->input->get("uid");
$jenis = $this->input->get("jenis");
$rekananid = $this->input->get("rekananid");



if ($tahun != 'all'){
  switch ($jenis) {
    case 'persiapan': 
      $dataNya->selectByParamsViewContracting(array("A.CONTRACTINGPROSESID|| IN " => "('1','2')"),-1,-1," AND TAHUN = '".$tahun."'");
      break;
    case 'pengendalian': 
    case 'penyelesaian': 
      $dataNya->selectByParamsViewContracting(array("A.CONTRACTINGPROSESID|| IN " => "('3','4','5')"),-1,-1," AND TAHUN = '".$tahun."'");
      break;
    case 'selesai': 
      $dataNya->selectByParamsViewContracting(array("A.CONTRACTINGPROSESID|| IN " => "('6')"),-1,-1," AND TAHUN = '".$tahun."'");
      break; 
    default: 
      break;
  }
} else {
  switch ($jenis) {
    case 'persiapan': 
      $dataNya->selectByParamsViewContracting(array("A.CONTRACTINGPROSESID|| IN " => "('1','2')"));
      break;
    case 'pengendalian': 
    case 'penyelesaian': 
      $dataNya->selectByParamsViewContracting(array("A.CONTRACTINGPROSESID|| IN " => "('3','4','5')"));
      break;
    case 'selesai': 
      $dataNya->selectByParamsViewContracting(array("A.CONTRACTINGPROSESID|| IN " => "('6')"));
      break; 
    default: 
      break;
  }
}

// echo $dataNya->query;
 
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
    <script src="<?=base_url()?>assets/new/vendors/js/jquery.min.3.6.0.js"></script>
    <link rel="stylesheet" type="text/css" href="<?=base_url() ?>assets/new/css/vendors.css">
    <link rel="stylesheet" type="text/css" href="<?=base_url() ?>assets/new/vendors/css/ui/prism.min.css">
    <link rel="stylesheet" type="text/css" href="<?=base_url() ?>assets/new/vendors/css/ui/prism.min.css">
    <link rel="stylesheet" type="text/css" href="<?=base_url() ?>assets/new/vendors/css/extensions/toastr.css">
    <link rel="stylesheet" type="text/css" href="<?=base_url() ?>assets/new/css/plugins/animate/animate.css">
    <link rel="stylesheet" type="text/css" href="<?=base_url() ?>assets/new/css/app.css">
    <link rel="stylesheet" type="text/css" href="<?=base_url() ?>assets/new/css/core/menu/menu-types/horizontal-menu.css">
    <link rel="stylesheet" type="text/css" href="<?=base_url() ?>assets/new/css/core/colors/palette-gradient.css">
    <link rel="stylesheet" type="text/css" href="<?=base_url() ?>assets/new/css/core/colors/palette-callout.css">
    <link rel="stylesheet" type="text/css" href="<?=base_url() ?>assets/new/css/style.css">
    <link rel="stylesheet" type="text/css" href="lib/DataTables-1.10.7/media/css/jquery.dataTables.css">
    <link rel="stylesheet" type="text/css" href="lib/DataTables-1.10.7/extensions/Responsive/css/dataTables.responsive.css">
    <link rel="stylesheet" type="text/css" href="lib/DataTables-1.10.7/examples/resources/syntax/shCore.css">
    <link rel="stylesheet" type="text/css" href="lib/DataTables-1.10.7/examples/resources/demo.css">
    <script type="text/javascript" language="javascript" src="lib/DataTables-1.10.7/media/js/jquery.dataTables.js"></script>
    <script type="text/javascript" language="javascript" src="lib/DataTables-1.10.7/extensions/Responsive/js/dataTables.responsive.js"></script>
    <script type="text/javascript" language="javascript" src="lib/DataTables-1.10.7/examples/resources/syntax/shCore.js"></script>
    <script type="text/javascript" language="javascript" src="lib/DataTables-1.10.7/examples/resources/demo.js"></script>
    <script type="text/javascript" language="javascript" class="init">
      $(document).ready(function() {

        var groupingTable = $('.row-grouping').DataTable({
              "columnDefs": [{
                  "visible": false,
                  "targets": 1
              }],
              "order": [
                  [1, 'asc']
              ],
              "displayLength": 25,
              "drawCallback": function(settings) {
                  var api = this.api();
                  var rows = api.rows({
                      page: 'current'
                  }).nodes();
                  var last = null;

                  api.column(1, {
                      page: 'current'
                  }).data().each(function(group, i) {
                      if (last !== group) {
                          $(rows).eq(i).before(
                              '<tr class="group"><td colspan="6">' + group + '</td></tr>'
                          );

                          last = group;
                      }
                  });
              }
          });

          $('.row-grouping tbody').on('click', 'tr.group', function() {
              var currentOrder = table.order()[0];
              if (currentOrder[0] === 2 && currentOrder[1] === 'asc') {
                  table.order([0, 'desc']).draw();
              }
              else {
                  table.order([0, 'asc']).draw();
              }
          });
      });
    </script>
  <style>
  #prosesDash_length, .dataTables_length { display: none;}
  </style>
  </head>

<body style="background: #fff">

<?php 
switch ($jenis) {
  case 'persiapan': 
    $lableHeader = 'Persiapan';
    $lableHeaderTable = 'Harga Perkiraan';
    break;
  case 'pengedalian': 
    $lableHeader = 'Pengendalian';
    $lableHeaderTable = 'Harga Perkiraan';
    break;
  case 'penyelesaian':  
    $lableHeader = 'Penyelesaian';
    $lableHeaderTable = 'Harga Perkiraan';
    break;
  case 'selesai': 
    $lableHeader = 'Selesai';
    $lableHeaderTable = 'Nilai Kontrak';
    break; 
  default: 
    break;
} ?>
   <div class="card mb-1 border-darken-1">
    <div class="card-content">
      <div class="p-1">
        <div class="alert bg-primary alert-icon-left alert-arrow-left alert-dismissible mb-2" role="alert">
          <span class="alert-icon"><i class="fa fa-th"></i></span>  <strong> <?=$lableHeader?>  </strong>  <small><?= $tahun?></small>
        </div>
        <div class="p-1"> 
          <?php 
          if ($jenis == 'persiapan' || $jenis == 'selesai') 
          { ?>
            <table class="table table-striped table-bordered row-grouping">
              <thead>
                <tr>
                  <th>Paket Pengadaan</th>
                  <th>Status</th> 
                </tr>
              </thead>
              <tbody> 
                <?php  
                if ($dataNya->countRow() > 0) { 
                  while($dataNya->nextRow())
                  {  
                ?>
                <tr>
                  <?php 
                  if ($jenis == 'persiapan') 
                  { ?>
                    <td><a href="kontrak/index/contracting_persiapan" target="_blank"><?= $dataNya->getField("NAMA") ?></a></td>
                  <?php 
                  } else { ?>
                    <td><a href="kontrak/index/contracting_detail?reqId=<?=  $dataNya->getField("CONTRACTINGREKANANID") ?>" target="_blank"><?= $dataNya->getField("NAMA") ?></a></td>
                  <?php 
                  } ?>
                  <td><?= $dataNya->getField("CONTRACTING_STATUS_KONTRAK") ?>
                  </td> 
                </tr>
                <?php 
                  } 
                }?>
              </tbody>
            </table>
          <?php 
          } ?>

          <?php 
          if ($jenis == 'pengendalian' || $jenis == 'penyelesaian') 
          { ?>
            <table class="table table-striped table-bordered row-grouping">
              <thead>
                <tr>
                  <th style="width: 50%;">Paket Pengadaan</th>
                  <th>Status</th>
                  <th class="text-center">Realisasi</th>
                  <th class="text-center">Tagihan</th>
                  <th class="text-center">Perubahan</th>
                  <th class="text-center">Sanksi/Denda</th>
                  <th class="text-center">Pemutusan</th>
                </tr>
              </thead>
              <tbody> 
                <?php  
                if ($dataNya->countRow() > 0) { 
                  while($dataNya->nextRow())
                  { 
                    // REALISASI PEKERJAAN
                    $realisasi = '';
                    $countKontrakRealisasi = new Queryfree();
                    $countKontrakRealisasi->selectByParams("SELECT DELIVERABLEID FROM CONTRACTING_DELIVERABLE 
                                        WHERE CONTRACTINGREKANANID = '".$dataNya->getField("CONTRACTINGREKANANID")."'");

                    $countKontrakRealisasiSelesai = new Queryfree();
                    $countKontrakRealisasiSelesai->selectByParams("SELECT DELIVERABLEID FROM CONTRACTING_DELIVERABLE 
                                        WHERE CONTRACTINGREKANANID = '".$dataNya->getField("CONTRACTINGREKANANID")."' AND STATUS = 'Selesai'");
                    if ($countKontrakRealisasi->countRow()) {
                      $realisasi = $countKontrakRealisasiSelesai->countRow().' / '.$countKontrakRealisasi->countRow();
                    }

                    // TERMIN
                    $tagihan = '';
                    $countKontrakPayment = new Queryfree();
                    $countKontrakPayment->selectByParams("SELECT PAYMENTID FROM CONTRACTING_PAYMENT 
                                        WHERE CONTRACTINGREKANANID = '".$dataNya->getField("CONTRACTINGREKANANID")."'");

                    $countKontrakPaymentSelesai = new Queryfree();
                    $countKontrakPaymentSelesai->selectByParams("SELECT PAYMENTID FROM CONTRACTING_PAYMENT 
                                        WHERE CONTRACTINGREKANANID = '".$dataNya->getField("CONTRACTINGREKANANID")."' AND PAY_STATUS = 'Selesai'");
                    if ($countKontrakPayment->countRow()) {
                      $tagihan = $countKontrakPaymentSelesai->countRow().' / '.$countKontrakPayment->countRow();
                    }

                    // PERUBAHAN
                    $perubahan = '';
                    $countKontrakAddendum = new Queryfree();
                    $countKontrakAddendum->selectByParams("SELECT CONTRACTING_ADDENDUM_ID FROM CONTRACTING_ADDENDUM 
                                        WHERE CONTRACTINGREKANANID = '".$dataNya->getField("CONTRACTINGREKANANID")."'");
                    if ($countKontrakAddendum->countRow()) {
                      $perubahan = $countKontrakAddendum->countRow().'x';
                    }

                    // SANKSI DAN DENDA
                    $sanksi = '';
                    $countKontrakSanksi = new Queryfree();
                    $countKontrakSanksi->selectByParams("SELECT SANKSIID FROM CONTRACTING_SANKSI 
                                        WHERE CONTRACTINGREKANANID = '".$dataNya->getField("CONTRACTINGREKANANID")."'");
                    if ($countKontrakSanksi->countRow()) {
                      $sanksi = $countKontrakSanksi->countRow().'x';
                    }

                    // PEMUTUSAN
                    $pemutusan = '';
                    $countKontrakPemutusan = new Queryfree();
                    $countKontrakPemutusan->selectByParams("SELECT CR_PEMUTUSAN FROM VIEW_CONTRACTING_PAKET 
                                        WHERE CONTRACTINGREKANANID = '".$dataNya->getField("CONTRACTINGREKANANID")."'");
                    $countKontrakPemutusan->firstRow();
                    if ($countKontrakPemutusan->getField('CR_PEMUTUSAN') == '1') {
                      $pemutusan = '<span class="badge badge-primary" style="margin-right:5px">Ya</span>';
                    } else {
                      $pemutusan = '<span class="badge badge-danger" style="margin-right:5px">Tidak</span>';
                    }
                ?>
                <tr>
                  <td><a href="kontrak/index/contracting_detail?reqId=<?= $dataNya->getField("CONTRACTINGREKANANID") ?>" target="_blank"><?= $dataNya->getField("NAMA") ?></a></td>
                  <td> <?= $dataNya->getField("CONTRACTING_STATUS_KONTRAK") ?>
                  </td>
                  <td class="text-center">
                    <?= $realisasi ?>
                  </td>
                  <td class="text-center">
                    <?= $tagihan ?>
                  </td>
                  <td class="text-center">
                    <?= $perubahan ?>
                  </td>
                  <td class="text-center">
                    <?= $sanksi ?>
                  </td>
                  <td class="text-center">
                    <?= $pemutusan ?>
                  </td>
                </tr>
                <?php 
                  } 
                }?>
              </tbody>
            </table>
          <?php 
          } ?>


        </div>
      </div>
    </div>
  </div>  
</body>
</html>
