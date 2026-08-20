<?php
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 * 
 */

$this->load->model(array("DashPermohonanPaket","Queryfree","PaketRekanan","PaketPenawaran","RekananPaketPenawaran"));

include_once("functions/string.func.php");
include_once("functions/date.func.php");
include_once("functions/default.func.php");
/* create objects */
$dataNya = new DashPermohonanPaket();

$tahun = $this->input->get("tahun");
$unitkerjaid = $this->input->get("uki");
$user_login_id = $this->input->get("uid");
$jenis = $this->input->get("jenis");


if ($tahun != 'all'){
  switch ($jenis) {
    case 'realisasi':
      $dataNya->selectByParams(array("A.USER_LOGIN_ID" => $user_login_id, "A.UNIT_KERJA_ID" => $unitkerjaid, "A.TAHUN_ANGGARAN" => $tahun),-1,-1," AND A.APPROVAL='1' AND TOTAL_REALISASI = '1' AND HARGA_NEGOSIASI > 0");
      break;
    default: 
      break;
  }
} else {
  switch ($jenis) {
    case 'realisasi': 
      $dataNya->selectByParams(array("A.USER_LOGIN_ID" => $user_login_id, "A.UNIT_KERJA_ID" => $unitkerjaid),-1,-1," AND A.APPROVAL='1' AND TOTAL_REALISASI = '1' AND HARGA_NEGOSIASI > 0");
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
    <!-- BEGIN VENDOR CSS-->
    <link rel="stylesheet" type="text/css" href="<?=base_url() ?>assets/new/css/vendors.css">
    <link rel="stylesheet" type="text/css" href="<?=base_url() ?>assets/new/vendors/css/ui/prism.min.css">
    <link rel="stylesheet" type="text/css" href="<?=base_url() ?>assets/new/vendors/css/ui/prism.min.css">
    <link rel="stylesheet" type="text/css" href="<?=base_url() ?>assets/new/vendors/css/extensions/toastr.css">
    <!-- END VENDOR CSS-->
    <!-- BEGIN ROBUST CSS-->
    <link rel="stylesheet" type="text/css" href="<?=base_url() ?>assets/new/css/plugins/animate/animate.css">
    <link rel="stylesheet" type="text/css" href="<?=base_url() ?>assets/new/css/app.css">
    <!-- END ROBUST CSS-->
    <!-- BEGIN Page Level CSS-->
    <link rel="stylesheet" type="text/css" href="<?=base_url() ?>assets/new/css/core/menu/menu-types/horizontal-menu.css">
    <link rel="stylesheet" type="text/css" href="<?=base_url() ?>assets/new/css/core/colors/palette-gradient.css">
    <link rel="stylesheet" type="text/css" href="<?=base_url() ?>assets/new/css/core/colors/palette-callout.css">
    <!-- END Page Level CSS-->
    <!-- BEGIN Custom CSS-->
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
        $('#prosesDash').DataTable({
          "iDisplayLength": 10,
          // "aaSorting": [[0, 'desc']],
          "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
        });
      });
    </script>
  <style>
  #prosesDash_length { display: none;}
  </style>
  </head>

<body style="background: #fff">

<?php 
switch ($jenis) {
  case 'realisasi': 
    $lableHeader = 'Realisasi Pengadaan';
    $lableHeaderTable = 'Harga Final/Akhir';
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
          <table id="prosesDash" class="border-double table mb-0 table-bordered" style="width: 100%">
            <thead>
              <tr>
                <th style="width:10px">No</th>
                <th>Nama Paket</th>
                <th style="text-align: center; width: 5%;">Kontrak</th>
                <th>Harga Perkiraan</th>
                <th>Harga Penawaran</th>
                <th style="width:15%"><?= $lableHeaderTable; ?></th>
              </tr>
            </thead>
            <tbody>
              <?php 
              $no=1;
              while($dataNya->nextRow()) 
              {  
                $total += $dataNya->getField('PERKIRAAN_BIAYA_HARGA');
                $totalFinal += $dataNya->getField('HARGA_NEGOSIASI');
                $totalPaketRencana++;
                ?>
                <tr>
                  <td align="center"><?= $no ?></td>
                  <td> <?= $dataNya->getField('NAMA') ?></td> 
                  <td style="text-align: center;">
                    <?php 
                    if ($dataNya->getField('STATUS_KONTRAK') != '') {
                       echo '<span class="fa fa-check-square-o"></span>';
                    } else {
                       echo '<span class="fa fa-close"></span>';
                    } ?>
                  </td>
                  <?php 
                  $paket_rekanan = new PaketRekanan();
                  $paket_rekanan->selectByParams(array("A.PAKET_ID" => $dataNya->getField('PAKET_ID'), "A.REKANAN_ID" => $dataNya->getField('PEMENANG')));
                  $paket_rekanan->firstRow();
                  $paketRekananId = $paket_rekanan->getField('PAKET_REKANAN_ID');

                  $paket_penawaran = new PaketPenawaran();
                  $paket_penawaran->selectByParams(array("A.PAKET_ID" => $dataNya->getField('PAKET_ID')));
                  $paket_penawaran->firstRow();
                  $paketPenawaranId = $paket_penawaran->getField('PAKET_PENAWARAN_ID');

                  $rekanan_paket_penawaran = new RekananPaketPenawaran();
                  $rekanan_paket_penawaran->selectByParams(array("PAKET_PENAWARAN_ID" => $paketPenawaranId, "PAKET_REKANAN_ID" => $paketRekananId));
                  $rekanan_paket_penawaran->firstRow(); 
                   ?>
                  <td> <?= number_format((float)$dataNya->getField('PERKIRAAN_BIAYA_HARGA'), 2, ',', '.') ?></td> 
                  <td> <?= number_format((float)$rekanan_paket_penawaran->getField('UNIT_PRICE'), 2, ',', '.') ?></td> 
                  <td> <?= number_format((float)$dataNya->getField('HARGA_NEGOSIASI'), 2, ',', '.') ?></td> 
                </tr>
              <?php 
              $no++;
              } 
              $totalSisa= $totalPaketRencana-$totalPaketRealisasi;
              ?>
           <!--  <tfoot>
                <tr>
                  <td></td>
                  <td>
                    <b>TOTAL <small style="font-weight: bold">Rp.</small></b>
                  </td> 
                  <td></td>
                  <td><?= number_format((float)$total, 2, ',', '.') ?></td>
                  <td><?= number_format((float)$totalFinal, 2, ',', '.') ?></td>
                </tr>
              </tfoot> -->
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>  
</body>
</html>
