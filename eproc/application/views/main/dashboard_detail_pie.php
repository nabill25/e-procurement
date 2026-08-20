<?php
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 * 
 */

$this->load->model("Paket");
$this->load->model("Paketpanitiadash");
$this->load->model("Paketjenis");
include_once("functions/string.func.php");
include_once("functions/date.func.php");
include_once("functions/default.func.php");
/* create objects */
$paketjenis = new Paketjenis();
/* VARIABLES */
$jenis = $this->input->get("jenis");
$jumlah = $this->input->get("jumlah");
$tahun = $this->input->get("tahun");
$unitkerja = $this->input->get("uki");
$user_login_id = $this->input->get("uid");
$type = $this->input->get("type");


// 1 e-Tender
// 7 e-Tender Cepat
// 2 Pengadaan Langsung
// 5 Penunjukan Langsung

switch ($jenis) {
  case 'Barang':
    $jenisConvert = 3;
    break;
  case 'Pekerjaan Konstruksi':
    $jenisConvert = 1;
    break;
  case 'Jasa Lainnya':
    $jenisConvert = 4;
    break;
  case 'Jasa Konsultansi':
    $jenisConvert = 2;
    break;
  case 'Katalog':
    $jenisConvert = 5;
    break;

  default:
    $jenisConvert = '';
    break;
}

if ($type == 'all') { // Untuk All Akses
  $paket = new Paket();
  $paket->getDashboardPieDetail($unitkerja,$jenisConvert,$tahun);
} else {
  $paket = new Paketpanitiadash();
  $paket->getDashboardPieDetail($unitkerja,$jenisConvert,$tahun,$user_login_id);
}

while($paket->nextRow())
{
  $data[] = array(
                'PAKET_ID' => $paket->getField("PAKET_ID"),
                'NAMA_PAKET' => $paket->getField("NAMA"),
                'PAKET_JENIS_ID' => $paket->getField("PAKET_JENIS_ID"),
                'PAKET_JENIS_ID_NAMA' => $paket->getField("PAKET_JENIS_ID_NAMA"),
                'NILAI' => $paket->getField("NILAI"),
              );
}
if (count($data) > 0) {
  $data = $data;
} else {
  $data = array();
}

// echo "<pre>"; print_r($result);

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

    <link rel="stylesheet" type="text/css" href="<?=base_url() ?>assets/new/vendors/css/ui/jquery-ui.min.css">
    <link rel="stylesheet" type="text/css" href="<?=base_url() ?>assets/new/css/plugins/ui/jqueryui.css">
    <script src="<?=base_url()?>assets/new/js/core/libraries/jquery_ui/jquery-ui.min.js"></script>
    <script src="<?=base_url()?>assets/new/js/scripts/ui/jquery-ui/navigations.js"></script>
    <style type="text/css">
      ul.menu-icons li {list-style-type:none;}
      ul { padding-left: 2px; }
    </style>
  </head>

<body style="background: #fff">

 <div class="card mb-1 border-darken-1">
  <div class="card-content">
    <div class="p-1">
      <div class="alert bg-primary alert-icon-left alert-arrow-left alert-dismissible mb-2" role="alert">
        <span class="alert-icon"><i class="fa fa-th"></i></span>  <strong> <?=$jenis?>  </strong>  <small>- <?= $tahun ?></small>
      </div>
      <div class="p-1">
         <?php
            $no=1;
            $total = 0;
            echo '<table class="table table-striped">
                            <thead>
                              <tr>
                                <th style="text-align: center;width: 5%">No</th>
                                <th>Nama Paket</th>
                                <th>HPS <small style="font-weight: bold">Rp.</small></th>
                              </tr>
                            </thead>
                            <tbody> ';
            $total = 0;
            foreach ($data as $key => $value) {
                $total += $value['NILAI'];
                echo           '<tr>';
                echo             '<td align="center">'.$no.'</td>';
                                    // <a href="'.base_url('main/index/paket_detil/?reqId='.$value['PAKET_ID'].'').'" target="_blank"> '.$value['NAMA_PAKET'].'</a>
                echo             '<td>
                                    '.$value['NAMA_PAKET'].'
                                  </td>';
                echo             '<td>'.number_format($value['NILAI'],'0',',','.').'</td>';
                echo           '</tr>';
            $no++;
            }
              echo '
                    <tfoot>
                    <tr>
                      <td colspan="2" align="center"><b>TOTAL HPS <small style="font-weight: bold">Rp.</small></b></td> <td>'.number_format($total,'0',',','.').'</td>
                    </tr>
                  </tfoot>
                   ';
              echo        '   </tbody>
                            </table>
                      ';
            ?>
      </div>
    </div>
  </div>
</div>

<script src="lib/bootstrap/libs/jquery/1.12.4/jquery.min.js"></script>

    <script src="<?=base_url()?>assets/new/vendors/js/vendors.min.js"></script>
    <script src="<?=base_url()?>assets/new/vendors/js/ui/jquery.sticky.js"></script>
    <script src="<?=base_url()?>assets/new/vendors/js/ui/prism.min.js"></script>
    <script src="<?=base_url()?>assets/new/js/core/app-menu.js"></script>
    <script src="<?=base_url()?>assets/new/js/core/app.js"></script>
    <script src="<?=base_url()?>assets/new/js/scripts/ui/breadcrumbs-with-stats.js"></script>
    <script src="<?=base_url()?>assets/new/js/scripts/tooltip/tooltip.js"></script>

  </body>
</html>
