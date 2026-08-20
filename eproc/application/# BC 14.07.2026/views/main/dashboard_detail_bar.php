<?php
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 * 
 */

$this->load->model("DashpaketManager");
$this->load->model("PaketMetodeLelang");
$this->load->model("UsersBase");
include_once("functions/string.func.php");
include_once("functions/date.func.php");
include_once("functions/default.func.php");
/* create objects */
$paket = new DashpaketManager();
$paketmetodelelang = new PaketMetodeLelang();

/* VARIABLES */
$direktorat = $this->input->get("direktorat");
$jenis = $this->input->get("jenis");
$total = $this->input->get("total");
$tahun = $this->input->get("tahun");
$vp_pengadaan = $this->USER_LOGIN_ID;
$usertypeid = $this->USER_TYPE_ID;
// $pengguna_str = explode(" (", $pengguna); 

if ($usertypeid == '7') { // Manager Pengadaan
  $paket->getDashboardBar2Detail($tahun,$direktorat,$vp_pengadaan);  
} else { // all
  $paket->getDashboardBar2DetailAll($tahun,$direktorat);  
}
// echo $paket->query; die;
while($paket->nextRow())
{
  $data[] = array(
                'HARGA_NEGOSIASI' => $paket->getField("HARGA_NEGOSIASI"),
                'USER_LOGIN_ID' => $paket->getField("USER_LOGIN_ID"),
                'NAMA_PAKET' => $paket->getField("NAMA"),
                'NILAI' => $paket->getField("NILAI"),
                'TOTAL_REALISASI' => $paket->getField("TOTAL_REALISASI"),
                'PAKET_ID' => $paket->getField("PAKET_ID"),
                'DEPARTMENT' => $paket->getField("DEPARTMENT"),
                'NILAI_KONTRAK' => $paket->getField("NILAI_KONTRAK"),
                'UUID' => $paket->getField("PAKET_UUID"),
              );
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
        <span class="alert-icon"><i class="fa fa-th"></i></span>  <strong> <?=$jenis?>  </strong> - <small><?= $pengguna ?> - <?= $tahun ?></small>  
      </div> 
      <div class="p-1"> 
         <?php  
            $no=1;
            $total = 0; 
            echo '<table id="example" class="border-double table mb-0 table-bordered">
                            <thead>
                              <tr>
                                <th style="text-align: center;width: 5%">No</th>
                                <th width="50%">Nama Paket</th>
                                <th width="20%">Divisi</th>
                                <th>Harga Perkiraan <small style="font-weight: bold">Rp.</small></th>
                                <th>Harga Final/Akhir <small style="font-weight: bold">Rp.</small></th>
                                <th>Efesiensi <small style="font-weight: bold">Rp.</small></th>
                                <th width="2%" align="center">Realisasi</th>
                              </tr>
                            </thead>
                            <tbody> ';
            $total = 0;
            $totalFinal = 0;
            $totalEfesiensi = 0;
            $totalEfesiensiAll = 0;
            $totalPaketRencana = 0;
            $totalPaketRealisasi = 0;
            $totalSisa = 0;
            foreach ($data as $key => $value) {  
                $total += $value['NILAI'];
                $totalFinal += $value['HARGA_NEGOSIASI'];
                if ($value['HARGA_NEGOSIASI'] > 0) {
                  $totalEfesiensi = $value['NILAI'] - $value['HARGA_NEGOSIASI'];
                } else {
                  $totalEfesiensi = 0;
                }
                $totalPaketRencana++;
                echo           '<tr>';
                echo             '<td align="center">'.$no.'</td>';
                echo             '<td>';
                if ($value['PAKET_ID'] > 0) {
                  echo                '<a href="'.base_url('main/index/paket_detil/?eid='.$value['PAKET_ID'].'&key='.$value['UUID']).'" target="_blank"> '.$value['NAMA_PAKET'].'</a>';
                } else {
                  echo                $value['NAMA_PAKET'];
                }
                echo             '</td>';
                echo             '<td>'.$value['DEPARTMENT'].'</td>';
                echo             '<td>'.number_format($value['NILAI'],'0',',','.').'</td>';
                echo             '<td>'.number_format($value['HARGA_NEGOSIASI'],'0',',','.').'</td>';
                echo             '<td>'.number_format($totalEfesiensi,'0',',','.').'</td>';
                // if ($value['TOTAL_REALISASI'] == '1' && $value['HARGA_NEGOSIASI'] != '') {
                // if ($value['TOTAL_REALISASI'] == '1' && ($value['HARGA_NEGOSIASI'] != '' || $value['NILAI_KONTRAK'] != '')) {
                if ($value['TOTAL_REALISASI'] == '1') {
                  $totalPaketRealisasi++;
                  echo             '<td align="center"><span style="display:none">1</span><img src="images/centang.png"></td>';
                } else {
                  echo             '<td align="center"><span style="display:none">0</span><img src="images/uncentang.png"></td>';
                }
                echo           '</tr>';
              $totalEfesiensiAll += abs($totalEfesiensi);
              $no++;
            } 
              $totalSisa= $totalPaketRencana-$totalPaketRealisasi;
              echo '
                    <tfoot>
                      <tr>
                        <td colspan="3"><b>TOTAL</b></td> 
                        <td>'.number_format($total,'0',',','.').'</td> 
                        <td>'.number_format($totalFinal,'0',',','.').'</td> 
                        <td colspan="2">'.number_format($totalEfesiensiAll,'0',',','.').'</td> 
                      </tr>
                      <tr>
                        <td colspan="3"><b>TOTAL Paket Rencana</b></td> 
                        <td colspan="4">'.$totalPaketRencana.'</td>
                      </tr>
                      <tr>
                        <td colspan="3"><b>TOTAL Paket Realisasi</b></td> 
                        <td colspan="4">'.$totalPaketRealisasi.'</td>
                      </tr>
                      <tr>
                        <td colspan="3"><b>TOTAL Sisa Paket Rencana</b></td> 
                        <td colspan="4">'.$totalSisa.'</td>
                      </tr>
                    </tfoot>';
              // echo '
              //       <tfoot>
              //         <tr>
              //           <td colspan="3"><b>TOTAL Harga Perkiraan <small style="font-weight: bold">Rp.</small></b></td> 
              //           <td colspan="4">'.number_format($total,'0',',','.').'</td>
              //         </tr>
              //         <tr>
              //           <td colspan="3"><b>TOTAL Harga Final/Akhir <small style="font-weight: bold">Rp.</small></b></td> 
              //           <td colspan="4">'.number_format($totalFinal,'0',',','.').'</td>
              //         </tr>
              //         <tr>
              //           <td colspan="3"><b>TOTAL Efesiensi <small style="font-weight: bold">Rp.</small></b></td> 
              //           <td colspan="4">'.number_format($totalEfesiensi,'0',',','.').'</td>
              //         </tr>
              //         <tr>
              //           <td colspan="3"><b>TOTAL Paket Rencana</b></td> 
              //           <td colspan="4">'.$totalPaketRencana.'</td>
              //         </tr>
              //         <tr>
              //           <td colspan="3"><b>TOTAL Paket Realisasi</b></td> 
              //           <td colspan="4">'.$totalPaketRealisasi.'</td>
              //         </tr>
              //         <tr>
              //           <td colspan="3"><b>TOTAL Sisa Paket Rencana</b></td> 
              //           <td colspan="4">'.$totalSisa.'</td>
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
 
  </body>
</html>
