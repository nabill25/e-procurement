<?php
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 * 
 */

$this->load->library("kauth");  $userLogin = new kauth(); 
$this->load->model("Metodetahap");
include_once("functions/string.func.php");
include_once("functions/date.func.php");
include_once("functions/default.func.php");

$metode_tahap = new Metodetahap();

$reqId = $this->input->get("reqId");

$metode_tahap->selectByParams(array("JENIS_TAHAP"=>$reqId),-1,-1);
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
    <script language="JavaScript" src="jslib/displayElement.js"></script>
  </head>

<body>
    <div class="card mb-1">
      <div class="card-content">
        <div class="p-1">
          <div class="alert bg-primary alert-icon-left alert-arrow-left alert-dismissible mb-2" role="alert">
            <span class="alert-icon"><i class="fa fa-th"></i></span>  <strong>DETAIL JADWAL</strong>  
          </div> 
          <div class="table-responsive">
            <table class="table table-bordered table-responsive" style="width:100%">
              <tbody>
              	<tr class="judul-kolom">
                  <th align="center">No.</th>
                  <th align="center" width="90%">Jadwal</th>
                  <th align="center">Cek Tgl Merah</th>
                </tr>
              <?php		
              $i = 0;
  							while($metode_tahap->nextRow()){
  						?>   
              <tr>              
                <td class="text-center"><?=$i+1?></td>
                <td><?=$metode_tahap->getField("NAMA")?></td>
                <td class="text-center">
                  <?php 
                  if ($metode_tahap->getField("CEK_TANGGAL_MERAH") == '1') {
                    echo '<span class="badge badge-primary">Ya</span>';
                  } else {
                    // echo '<span class="badge badge-danger">Tidak</span>';
                    echo '-';
                  }
                  ?>
                    
                </td>
              </tr>
              <?php 
  						$i++;
              } ?>
            </tbody>
          </table> 
        </div>
      </div>
    </div>
  </div>
  </body>
</html>
