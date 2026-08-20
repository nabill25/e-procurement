<?php
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 * 
 */

$this->load->model("Dashboardvms");
include_once("functions/string.func.php");
include_once("functions/date.func.php");
include_once("functions/default.func.php");
/* create objects */
$dashboardvms = new Dashboardvms();
/* VARIABLES */
$reqJenis = $this->input->get("reqJenis");

if ($reqJenis == 'all') {
  $dashboardvms->selectByParams("view_katalog",array(),-1,-1);
  $title = 'Total Katalog ';
} else {
  $dashboardvms->selectByParams("view_katalog",array("publis_status" => $reqJenis),-1,-1);  
  if ($reqJenis == '1') {
    $title = 'Total Katalog Terverifikasi';
  } else {
    $title = 'Total Katalog Belum Terverifikasi';
  }
}
$statement = '';
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
        $('#example').DataTable({
          "iDisplayLength": 10,
          // "aaSorting": [[0, 'desc']],
          "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
        });
      });
    </script>
  <style>
  #example_length { display: none;}
  </style>
  </head>

<body style="background: #fff">

 <div class="card mb-1 border-darken-1">
  <div class="card-content">
    <div class="p-1">
      <div class="alert bg-primary alert-icon-left alert-arrow-left alert-dismissible mb-2" role="alert">
        <span class="alert-icon"><i class="fa fa-th"></i></span>  <strong> <?=$title?>  </strong>
      </div>
      <div class="p-1">
        <table id="example" class="border-double table mb-0 table-bordered">
          <thead>
            <tr>
              <th style="text-align: center;width: 5%">No</th>
              <th>Nama Produk</th>
              <th>Harga</th>
              <th>Jumlah Stock</th>
              <th>Penyedia</th>
            </tr>
          </thead>
          <tbody>
            <?php
            $no     =1;
            $total  = 0;
            $html   = '';
            if ($dashboardvms->countRow() > 0) {
              while ($dashboardvms->nextRow()) {
                $html .= '<tr>';
                $html .= '
                  <td width="5%">'.$no.'</td>
                  <td width="35%">'.$dashboardvms->getField('NAMAPRODUK').'</td>
                  <td width="25%">'.currencyToPage($dashboardvms->getField('HARGA')).'</td>
                  <td width="15%">'.$dashboardvms->getField('JUMLAHSTOCK').'</td>
                  <td width="20%">'.$dashboardvms->getField('USER_NAMA').'</td>
                         ';
                $html .= '
                         ';
                $html .= '</tr>';
                $no++;
              }
            } else {
              $html .= '<tr class="text-center"><td colspan="4">. : : Tidak ada data : : .</td></tr>';
            }
            echo $html;
            ?>
      </div>
    </div>
  </div>
</div> 
  </body>
</html>
