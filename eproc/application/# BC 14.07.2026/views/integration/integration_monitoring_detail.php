<?php
$this->load->model(array("Integrate","Queryfree"));

include_once("functions/string.func.php");
include_once("functions/date.func.php");
include_once("functions/default.func.php");
/* create objects */
$jenis = $this->input->get("jenis");
$dataNya = new Integrate(); 

  switch ($jenis) {
    case 'vendor': 
      $dataNya->selectByParamsLogs(array("TYPE" => "SUPPLIER"),-1,-1," ORDER BY INTEGRATION_LOGS_ID DESC");
      break; 
    case 'po': 
      $dataNya->selectByParamsLogs(array("TYPE" => "PO"),-1,-1," ORDER BY INTEGRATION_LOGS_ID DESC");
      break; 
    default: 
      break;
  } 
// echo $dataNya->query; die;
 
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
          "aaSorting": [[0, 'desc']],
          "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
        });
      });

      function reloadTable() {
        $('#prosesDash').DataTable().destroy();

        $('#prosesDash').DataTable({
          "iDisplayLength": 10,
          "aaSorting": [[0, 'desc']],
          "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
        });
      }
    </script>
  <style>
  #prosesDash_length { display: none;}
  </style>
  </head>

<body style="background: #fff">

<?php 
switch ($jenis) {
  case 'vendor': 
    $lableHeader = 'Supplier integration';
    break; 
  case 'po': 
    $lableHeader = 'Purchase Order (PO) integration';
    break; 
  default: 
    break;
} ?>
   <div class="card mb-1 border-darken-1">
    <div class="card-content">
      <div class="p-1">
        <div class="alert bg-primary alert-icon-left alert-arrow-left alert-dismissible mb-2" role="alert">
          <span class="alert-icon"><i class="fa fa-th"></i></span>  <strong> <?=$lableHeader?>  </strong>
        </div>
          <div class="form-group col-md-12" style="margin-bottom:-50px">
              <a onclick="reloadTable()" title="Tambah" class="btn round btn-min-width box-shadow-1 btn-primary text-white">Reload</a>
          </div>
        <div class="p-1"> 

          <table id="prosesDash" class="border-double table mb-0 table-bordered" style="width: 100%">
            <thead>
              <tr>
                <!-- <th style="width:10px" rowspan="2">No</th> -->
                <th style="width: 10%;" rowspan="2">Tanggal</th>
                <th class="text-center" colspan="3">Lokal File</th> 
                <th class="text-center" colspan="3">Kirim SFTP</th> 
              </tr>
              <tr>
                <th>Nama File</th> 
                <th>Catatan</th> 
                <th class="text-center" style="width: 5%;">Download</th> 
                <th class="text-center" style="width: 3%;">Status</th>
                <th>Catatan</th> 
                <th>Tanggal</th> 
              </tr>
            </thead>
            <tbody>
              <?php 
              $no=1;
              while($dataNya->nextRow()) 
              {  
                switch ($jenis) {
                  case 'vendor': 
                    $downloadFile = '<a href="integration/vms/supplier/'.$dataNya->getField('FILE_NAME').'" class="btn btn-xs btn-primary" style="padding:2px 5px !important" target="_blank"><i class="fa fa-download" aria-hidden="true"></i></a>';
                    break; 
                  case 'po': 
                    $downloadFile = '<a href="integration/vms/po/'.$dataNya->getField('FILE_NAME').'" class="btn btn-xs btn-primary" style="padding:2px 5px !important" target="_blank"><i class="fa fa-download" aria-hidden="true"></i></a>';
                    break; 
                  default: 
                    break;
                }
                ?>
                <tr>
                  <!-- <td align="center"><?= $no ?></td> -->
                  <td> 
                    <?php 
                    $dateEx = explode(",",getFormattedDateTime($dataNya->getField('CREATED_DATE'))); 
                    echo $dateEx[0].'<br> <span class="fa fa-clock-o"></span> <small class="badge badge-success">'.$dateEx[1].'</small>';
                    ?> 
                  </td>   
                  <td> 
                    <?= $dataNya->getField('FILE_NAME') ?> 
                  </td>   
                  <td> 
                    <?= $dataNya->getField('NOTE') ?> 
                  </td>    
                  <td class="text-center"> 
                    <?= $downloadFile; ?>
                  </td>   
                  <td class="text-center"> 
                    <?php 
                    if ($dataNya->getField('SEND_STATUS') == '1') {
                      echo '<span class="fa fa-check-square-o"></span>';
                    } else {
                      echo '<span class="fa fa-exclamation-triangle" style="color:red"></span>';
                    }
                    ?> 
                  </td>     
                  <td> 
                    <?= $dataNya->getField('SEND_NOTE') ?> 
                  </td>     
                  <td> 
                    <?php 
                    if ($dataNya->getField('SEND_DATE')) {
                      $dateEx2 = explode(",",getFormattedDateTime($dataNya->getField('SEND_DATE'))); 
                      echo $dateEx2[0].'<br> <span class="fa fa-clock-o"></span> <small class="badge badge-success">'.$dateEx2[1].'</small>';
                    }
                    ?> 
                  </td>     
                </tr>
              <?php 
              $no++;
              } ?>
            </tbody>
          </table> 
          
        </div>
      </div>
    </div>
  </div>  
</body>
</html>
