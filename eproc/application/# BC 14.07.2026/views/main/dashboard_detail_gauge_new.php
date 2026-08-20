<?php
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 * 
 */

$this->load->model("Paket");
$this->load->model("Dashpaket");
$this->load->model("PaketMetodeLelang");
include_once("functions/string.func.php");
include_once("functions/date.func.php");
include_once("functions/default.func.php");
/* create objects */
$paket = new Paket();
$paketmetodelelang = new PaketMetodeLelang();
/* VARIABLES */

$tahun = $this->input->get("tahun");
$unitkerja = $this->input->get("uki");
$user_login_id = $this->input->get("uid");
$type = $this->input->get("type");
 
$paket = new Dashpaket();
$paket->getDashboardGaugeNewDetail($unitkerja,$type,$tahun);
// echo $paket->query;

while($paket->nextRow())
{
  $data[] = array(
                'NAMA_PAKET' => $paket->getField("NAMA"),
                'NILAI' => $paket->getField("NILAI"),
                'PROSES' => $paket->getField("PROSES"),
                'PAKET_ID' => $paket->getField("PAKET_ID"),
              );
}

if (!$data) {
    $data = array();
} else {
  if (count($data) > 0) {
    $data = $data;
  } else {
    $data = array();
  }
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

 <div class="card mb-1 border-darken-1">
  <div class="card-content">
    <div class="p-1">
      <div class="alert bg-primary alert-icon-left alert-arrow-left alert-dismissible mb-2" role="alert">
        <span class="alert-icon"><i class="fa fa-th"></i></span>  <strong> Beban Kerja Paket </strong> <small>- <?= $tahun ?></small>
      </div>
      <div class="p-1">
        <?php
          $no=1;
          $total = 0;
          echo '          <table id="prosesDash" class="border-double table mb-0 table-bordered" style="width: 100%">

                          <thead>
                            <tr>
                              <th style="text-align: center;width: 5%">No</th>
                              <th>Nama Paket</th>
                              <th>Harga Perkiraan <small style="font-weight: bold">Rp.</small></th>
                              <th width="10px" align="center">Sedang Proses ?</th>
                            </tr>
                          </thead>
                          <tbody> ';
          $total = 0;
          foreach ($data as $key => $value) {
              $total += $value['NILAI'];
              echo           '<tr>';
              echo             '<td align="center">'.$no.'</td>';
              echo             '<td> '.$value['NAMA_PAKET'].'</td>';
              // echo             '<td>
              //                     <a href="'.base_url('main/index/paket_detil/?reqId='.$value['PAKET_ID'].'').'" target="_blank"> '.$value['NAMA_PAKET'].'</a>
              //                   </td>';
              echo             '<td>'.number_format($value['NILAI'],'0',',','.').'</td>';
              if ($value['PROSES'] == '1') {
                echo             '<td align="center"><span class="badge badge-primary">Ya</span></td>';
              } else {
                echo             '<td align="center"><span class="badge badge-danger">Tidak</span></td>';
              }
              echo           '</tr>';
          $no++;
          }
            // echo '
            //       <tfoot>
            //         <tr>
            //           <td colspan="2" align="center"><b>TOTAL Harga Perkiraan <small style="font-weight: bold">Rp.</small></b></td> <td>'.number_format($total,'0',',','.').'</td>
            //         </tr>
            //       </tfoot>
            //      ';
            echo        '   </tbody>
                          </table>
                    ';
        ?>
      </div>
    </div>
  </div>
</div>
 
