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
$reqStar = $this->input->get("reqStar"); 
$statement = ''; 
$dashboardvms->selectByParams("view_penilaian_rekanan_by_user",array("star" => $reqStar),-1,-1);  
$title = 'Penyedia Rating '.$reqStar; 
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
    <link rel="stylesheet" type="text/css" href="<?=base_url() ?>assets/new/css/app.css">
    <link rel="stylesheet" type="text/css" href="<?=base_url() ?>assets/new/css/core/menu/menu-types/horizontal-menu.css">
    <link rel="stylesheet" type="text/css" href="<?=base_url() ?>assets/new/css/style.css"> 

    <style type="text/css">
      ul.menu-icons li {list-style-type:none;}
      ul { padding-left: 2px; }
    </style>
  </head>
<script type="text/javascript">
// $(document).ready(function() {
//     $('#example').DataTable({
//       "aaSorting": [[1, 'desc']],
//       "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],  
//     });
//   }); 
</script>

<body style="background: #fff">
 
 <div class="card mb-1 border-darken-1">
  <div class="card-content">
    <div class="p-1">
      <div class="alert bg-primary alert-icon-left alert-arrow-left alert-dismissible mb-2" role="alert">
        <span class="alert-icon"><i class="fa fa-th"></i></span>  <strong> <?=$title?>  </strong>  
      </div> 
      <div class="p-1">
        <table id="example" class="table table-striped">
          <thead>
            <tr>
              <th style="text-align: center;width: 5%">No</th>
              <th>Nama Penyedia</th>
              <th>Jumlah Penilaian</th>
              <th>Total Skor</th>
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
                  <td width="75%">'.$dashboardvms->getField('NAMA').'</td>
                  <td width="10%" class="text-center">'.$dashboardvms->getField('TOTAL_PENILAIAN').'</td>
                  <td width="10%" class="text-center">'.round($dashboardvms->getField('TOTAL_SKOR'),2).'</td>
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
