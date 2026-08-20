<?php
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 * 
 */

$this->load->model("Katalog");
include_once("functions/string.func.php");
include_once("functions/date.func.php");
include_once("functions/default.func.php");
/* create objects */
$katalog = new Katalog();
$id = $this->input->get("reqId");
  
$katalog->selectByParamsRiwayatHarga(array('A.KATALOGID' => $id),-1,-1); 

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
        <span class="alert-icon"><i class="fa fa-th"></i></span>  <strong> History Perubahan Harga  </strong>  
      </div> 
      <div class="p-1"> 
            <?php  
            $no=1; 

              echo ' <table class="table table-striped">
                      <thead>
                        <tr>
                          <th style="text-align: center;width: 5%">No</th>
                          <th>Harga Lama <small style="font-weight: bold">Rp.</small></th>
                          <th>Harga Baru <small style="font-weight: bold">Rp.</small></th>
                          <th style="text-align:center">Statistik Perubahan</th>
                          <th>Tanggal Perubahan</th>
                        </tr>
                      </thead>
                      <tbody> ';
              while($katalog->nextRow())
              { 
                $hargalama = $katalog->getField('hargalama');
                $hargabaru = $katalog->getField('hargabaru');
                if ($hargabaru > $hargalama) {
                  $labelPerubahan = '<span class="fa fa-chevron-up" style="color:red"></span>';
                  $persentasePerubahan = round((($hargabaru - $hargalama) / $hargalama)*100,2) .' %';
                } else {
                  $labelPerubahan = '<span class="fa fa-chevron-down" style="color:blue"></span>';
                  $persentasePerubahan = round((($hargabaru - $hargalama) / $hargalama)*100,2) .' %';
                  // $persentasePerubahan = '';
                }
                // $persentasePerubahan = round((($hargabaru - $hargalama) / $hargalama)*100,3);
                $date = explode(" ", $katalog->getField('created_date'));
                echo           '<tr>';
                echo             '<td align="center">'.$no.'</td>';
                echo             '<td>'.number_format($hargalama,2,',','.').'</td>';
                echo             '<td>'.number_format($hargabaru,2,',','.').'</td>';
                echo             '<td align="center">'.$labelPerubahan.' '.$persentasePerubahan.'</td>';
                echo             '<td>'.getFormattedDate($date[0]).' '.$date[1].'</td>';
                echo           '</tr>';
              $no++;
              } 
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
