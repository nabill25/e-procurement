<?php
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 *
 */

$this->load->model(array("Dashboardvms","Masterpengaturan"));
include_once("functions/string.func.php");
include_once("functions/date.func.php");
include_once("functions/default.func.php");
/* create objects */
$dashboardvms = new Dashboardvms();
$dokexpired = new Masterpengaturan();
/* VARIABLES */
$reqJenis = $this->input->get("reqJenis");

switch ($reqJenis) {
  case 'total':
    $dashboardvms->selectDash(array(),-1,-1," AND A.user_type_id = 6 AND A.rekanan_id IS NOT NULL AND B.status_validasi in(0, 1, 2, 3, 4, 10) ORDER BY A.rekanan_id ASC");
    $title = 'Total Penyedia Terdaftar';
    break;

  case 'verifikasi':
    $dashboardvms->selectDash(array(),-1,-1," AND A.user_type_id = 6 AND A.rekanan_id IS NOT NULL AND B.status_validasi in(1) ORDER BY B.status_validasi ASC");
    $title = 'Total Penyedia Terverifikasi';
    break;

  case 'nonverifikasi':
    $dashboardvms->selectDash(array(),-1,-1," AND A.user_type_id = 6 AND A.rekanan_id IS NOT NULL AND B.status_validasi in(0, 2, 3, 4, 10) ORDER BY B.status_validasi ASC");
    $title = 'Total Penyedia Belum Terverifikasi';
    break;

  case 'sudahkirimberkas':
    $dashboardvms->selectDash(array(),-1,-1," AND A.user_type_id = 6 AND A.rekanan_id IS NOT NULL AND A.USER_STATUS = '2' ORDER BY B.status_validasi ASC");
    $title = 'Total Penyedia Sudah Kirim Berkas';
    break;

  case 'belumkirimberkas':
    $dashboardvms->selectDash(array(),-1,-1," AND A.user_type_id = 6 AND A.rekanan_id IS NOT NULL AND A.USER_STATUS = '0' ORDER BY B.status_validasi ASC");
    $title = 'Total Penyedia Belum Kirim Berkas';
    break;

  case 'blacklist';
    $dashboardvms->selectBlacklist();
    $title = 'Penyedia Masuk Daftar Blacklist';
  break;

  case 'blacklistHistory';
    $dashboardvms->selectBlacklistHistory();
    $title = 'History Daftar Hitam';
  break;

  case 'expired';
    $dokexpired->selectByParamsDokExpired();
    $title = 'Dokumen Expired Penyedia';
  break;

  default:
    // code...
    break;
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
          <?php
          if ($reqJenis == 'expired') { ?>
            <thead>
              <tr>
                <th width="5px" style="text-align: center;">No</th>
                <th width="20%">Nama</th>
                <th width="20%">Jenis Dokumen</th>
                <th width="20%">Tanggal Berakhir</th>
              </tr>
            </thead>
            <?php
              $no     =1;
              $total  = 0;
              $html   = '';
              while ($dokexpired->nextRow()) {
                $html .= '<tr>';
                $html .= '
                  <td style="text-align: center">'.$no.'</td>
                  <td>'.$dokexpired->getField('NAMA').'</td>
                  <td>'.$dokexpired->getField('JENIS').'</td>
                  <td>'.getFormattedDateJson(str_replace(" 00:00:00", "", $dokexpired->getField('TANGGAL_BERAKHIR'))).'</td>';
                $html .= '</tr>';
                $no++;
              }
            ?>

          <?php
          } else { ?>
            <thead>
              <tr>
                <th width="15px" style="text-align: center;">No</th>
                <th width="20%">No Registrasi</th>
                <th>Nama</th>
                <th>Kota</th>
                <th>Email</th>
                <!-- <th>Status</th> -->
              </tr>
            </thead>
            <tbody>
              <?php
              $no     =1;
              $total  = 0;
              $html   = '';
              while ($dashboardvms->nextRow()) {
                $html .= '<tr>';
                $html .= '
                  <td style="text-align: center">'.$no.'</td>
                  <td>'.$dashboardvms->getField('KODE').'</td>
                  <td>'.$dashboardvms->getField('NAMA').'</td>
                  <td>'.$dashboardvms->getField('KOTA').'</td>
                  <td>'.$dashboardvms->getField('EMAIL').'</td>
                         ';
                // 0=Belum 1=Validasi 2=Hapus 3=Kirim ke Rekomendator, 4=Kirim ke Validator, 10=Tolak
                switch ($dashboardvms->getField('STATUS_VALIDASI')) {
                  case '0':
                    $status = '<span class="badge badge-danger">Melengkapi Berkas</span>'; break;
                  case '1':
                    $status = '<span class="badge badge-primary">Terverifikasi</span>'; break;
                  case '2':
                    $status = '<span class="badge badge-danger">-</span>'; break;
                  case '3':
                    $status = '<span class="badge badge-info">Approval Penyelia</span>'; break;
                  case '4':
                    $status = '<span class="badge badge-success">Approval Sub Div</span>'; break;
                  case '10':
                    $status = '<span class="badge badge-danger">Ditolak (Revisi)</span>'; break;

                  default:
                    $status = '-'; break;
                }
                // $html .= '
                //   <td>'.$status.'</td>
                //          ';
                $html .= '
                         ';
                $html .= '</tr>';
                $no++;
              }
              ?>
          <?php
          }
          echo $html;
          ?>
      </div>
    </div>
  </div>
</div>

  </body>
</html>
