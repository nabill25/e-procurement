<?php
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 * 
 */

$this->load->model("Paket");
$this->load->model("Paketpanitiadash");
$this->load->model("PaketMetodeLelang");
include_once("functions/string.func.php");
include_once("functions/date.func.php");
include_once("functions/default.func.php");
/* create objects */
$paketmetodelelang = new PaketMetodeLelang();
/* VARIABLES */

$metode = $this->input->get("metode");
$bulan = $this->input->get("bulan") ?: '';
$tahun = $this->input->get("tahun");
$unitkerjaid = $this->input->get("uki");
$user_login_id = $this->input->get("uid");
$jenis = $this->input->get("jenis");

switch ($metode) {
  case 'Tender': $metodeConvert = 1;
    break;
  case 'Tender Terbatas': $metodeConvert = 3;
    break;
  case 'Tender Cepat': $metodeConvert = 7;
    break;
  case 'Pengadaan Langsung': $metodeConvert = 2;
    break;
  case 'Penunjukan Langsung': $metodeConvert = 5;
    break;
  case 'Pembelian Langsung': $metodeConvert = 6;
    break;
  case 'Kompetisi': $metodeConvert = 8;
    break;
  case 'Tender Kualifikasi': $metodeConvert = 10;
    break;
  case 'Pembelian Offline': $metodeConvert = 9;
    break;

  default: $metodeConvert = '';
    break;
}

if ($jenis == 'all') { // Untuk All Akses
  $paket = new Paket();
  if ($bulan != '') {
    $paket->getDashboardDetailPaket($metodeConvert,$tahun,$bulan);
  } else {
    $paket->getDashboardDetailPaket($metode,$tahun,$bulan);
  }
  $paketmetodelelang->selectByParams(array('PAKET_METODE_LELANG_ID' => $metode),-1,-1);
  $paketmetodelelang->firstRow();
  $metodeName = $paketmetodelelang->getField("NAMA");
} else { // Untuk Panitia
  $paket = new Paketpanitiadash();
  if ($bulan != '') {
    $paket->getDashboardDetailPaket($metodeConvert,$tahun,$unitkerjaid,$user_login_id,$bulan);
  } else {
    $paket->getDashboardDetailPaket($metode,$tahun,$unitkerjaid,$user_login_id,$bulan);
  }
  $paketmetodelelang->selectByParams(array('PAKET_METODE_LELANG_ID' => $metode),-1,-1);
  $paketmetodelelang->firstRow();
  $metodeName = $paketmetodelelang->getField("NAMA");
}
// echo $paket->query;
while($paket->nextRow())
{
  $data[] = array(
                'PAKET_ID' => $paket->getField("PAKET_ID"),
                'NAMA_PAKET' => $paket->getField("NAMA"),
                'PAKET_JENIS_ID' => $paket->getField("PAKET_JENIS_ID"),
                'PAKET_JENIS_ID_NAMA' => $paket->getField("PAKET_JENIS_ID_NAMA"),
                'NILAI' => $paket->getField("NILAI"),
              );
  // $reqPaketId[] = $paket->getField("PAKET_ID");
  // $reqNamaPaket[] = $paket->getField("NAMA");
  // $reqNilai[] = number_format($paket->getField("NILAI"),'0',',','.');
}

//  array grouping by paket_jenis_id
$result = array();
if (is_array($data)) {
  foreach ($data as $key => $value) {
    $result[$value['PAKET_JENIS_ID_NAMA']][] = $value;
  }
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
        <span class="alert-icon"><i class="fa fa-th"></i></span>  <strong> <?=$metodeName?>  </strong>  <small><?=$tahun?></small>
      </div>
      <div class="p-1">
        <!-- <table class="table table-striped">
          <thead>
            <tr>
              <th style="text-align: center;width: 5%">No</th>
              <th>Nama Paket</th>
              <th>HPS <small style="font-weight: bold">Rp.</small></th>
            </tr>
          </thead>
          <tbody> -->
            <?php
            $no=1;
            $total = 0;
            foreach ($result as $key => $value) {
              // $total += $value['NILAI'];
              if ($no==1) {
                $show = '';
                $expan = '';
              } else {
                $show = '';
                $expan = '';
              }

              switch ($key) {
                case 'Pekerjaan Konstruksi':
                    $pktll = 'danger';
                  break;
                case 'Jasa Konsultansi':
                    $pktll = 'warning';
                  break;
                case 'Barang':
                    $pktll = 'info';
                  break;
                case 'Jasa Lainnya':
                    $pktll = 'success';
                  break;
                default:
                    $pktll = 'success';
                  break;
              }

              echo '<div class="card collapse-icon accordion-icon-rotate">
                      <div id="headingCollapse31" class="card-header bg-'.$pktll.'">
                        <a data-toggle="collapse" href="#collapse'.$no.'" aria-expanded="true" aria-controls="collapse'.$no.'" class="card-title lead white">'.$key.' <span class="badge badge-danger" style="margin-right:3%">'.count($value).' Paket<span></a>
                      </div>
                      <div id="collapse'.$no.'" role="tabpanel" aria-labelledby="headingCollapse'.$key.'" class="card-collapse collapse" style="">
                        <div class="card-content">
                          <div class="card-body">
                            <table class="table table-striped">
                              <thead>
                                <tr>
                                  <th style="text-align: center;width: 5%">No</th>
                                  <th>Nama Paket</th>
                                  <th>Harga Perkiraan <small style="font-weight: bold">Rp.</small></th>
                                </tr>
                              </thead>
                              <tbody> ';
                              $nonya = 1;
                $total = 0;
              foreach ($value as $isi) {
                $total += $isi['NILAI'];
                echo           '<tr>';
                echo             '<td align="center">'.$nonya.'</td>';
                // echo             '<td>
                //                     <a href="'.base_url('main/index/paket_detil/?reqId='.$isi['PAKET_ID'].'').'" target="_blank"> '.$isi['NAMA_PAKET'].'</a>
                //                   </td>';
                echo             '<td>
                                    '.$isi['NAMA_PAKET'].'
                                  </td>';
                echo             '<td>'.number_format($isi['NILAI'],'0',',','.').'</td>';
                echo           '</tr>';
              $nonya++;
              }
              echo '
                    <tfoot>
                    <tr>
                      <td colspan="2" align="center"><b>TOTAL HARGA PERKIRAAN <small style="font-weight: bold">Rp.</small></b></td> <td>'.number_format($total,'0',',','.').'</td>
                    </tr>
                  </tfoot>
                   ';
              echo        '   </tbody>
                            </table>
                          </div>
                        </div>
                      </div>
                    </div>
                      ';
             ?>
            <!-- <tr>
              <td style="text-align: center"><?= $no; ?></td>
              <td>
                <a href="<?= base_url('main/index/paket_detil/?reqId='.$value['PAKET_ID']) ?>" target="_blank">
                  <?= $key; ?>
                </a>
              </td>
              <td>
                <?= $value['NILAI']; ?>
              </td>
            </tr> -->

           <?php
            $no++;}
            ?>
         <!--  </tbody>
          <tfoot>
            <tr>
              <td colspan="2" style="text-align: center"><b>TOTAL</b></td>
              <td style="font-weight: bold"><?= number_format($total,'0',',','.') ?></td>
            </tr>
          </tfoot>
        </table> -->
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
