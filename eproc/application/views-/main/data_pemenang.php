<?php
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 * 
 */

$this->load->model("PaketPanitia");

$reqId = $this->input->get("reqId");

$paket_panitia = new PaketPanitia(); 
$paket_panitia->selectByParams(array("A.PAKET_ID" => $reqId));
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

<!-- <body class="body-popup"> -->
<body>

    <div class="card mb-1 border-darken-1">
      <div class="card-content">
        <div class="p-1">  
          <div class="row">  
            <div class="col-md-12 col-sm-12">
              <div class="card">
                <div class="card-content collapse show border-info border-darken-2">
                  <div class="card-body" style="padding-right: 2px">
                   <table class="table table-double mb-0"> 
                    <thead>
                      <tr>
                        <th>NPP</th>
                        <th>Nama</th>
                        <th>Jabatan</th> 
                        <th style="text-align: center">Status</th>
                      </tr>                                          
                    </thead>
                    <tbody id="tbodyPanitia">
                      <?php
                        $i=1; 
                        $totalPanitia=1;
                        $totalPanitiaSudahValidasi=1;
                        while($paket_panitia->nextRow())
                        {
                          $input = $paket_panitia->getField("NAMA").";".$paket_panitia->getField("NIP").";".$paket_panitia->getField("JABATAN").";".$paket_panitia->getField("KETUA");
                        ?> 
                        <tr>
                          <td><?=$paket_panitia->getField("NIP")?></td>
                          <td><?=$paket_panitia->getField("NAMA")?></td>
                          <td><?=$paket_panitia->getField("JABATAN")?></td> 
                          <td style="text-align: center">
                            <?php 
                            if ($paket_panitia->getField("VALIDASI_PEMENANG") != '1') { 
                              if ($this->NIP == $paket_panitia->getField("NIP")) {
                                echo '<a title="#" onclick="submitValidasi(\''.$paket_panitia->getField("PAKET_PANITIA_ID").'\')" class="btn btn-danger btn-sm" style="color:#fff">Validasi</a>';
                              } else {
                                if ($paket_panitia->getField("VALIDASI_PEMENANG") == '') {
                                  echo '-';
                                } else if ($paket_panitia->getField("VALIDASI_PEMENANG") == '2') {
                                  echo '<i class="fa fa-close btn btn-danger" style="padding:3px 8px !important"> di tolak</i>';
                                  echo '<br><small>Catatan: '.$paket_panitia->getField("VALIDASI_PEMENANG_CATATAN").'</small>';
                                } else if ($paket_panitia->getField("VALIDASI_PEMENANG") == '1') {
                                  echo '<i class="fa fa-check-square-o btn btn-primary" style="padding:3px 8px !important"> di terima </i>';
                                } 
                                // echo '<i class="fa fa-close btn btn-danger" style="padding:3px 8px !important"></i>';
                                // echo "-";
                                // echo '<img src="images/uncentang.png">';
                              }
                            } else { 
                              $totalPanitiaSudahValidasi++;
                              echo '<img src="images/centang.png">';
                            } ?>
                          </td>
                        </tr>
                      <?php
                        $i++;  
                        $totalPanitia++;
                      }
                      $totalPanitiaSetuju = $totalPanitia-1;
                      // echo $totalPanitia;
                      // echo $totalPanitiaSetuju;
                      // echo $totalPanitiaSudahValidasi;
                      ?>
                    </tbody>
                  </table> 
                  </div>
                </div>
              </div>
            </div>
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
