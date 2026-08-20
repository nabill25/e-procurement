<?php
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 * 
 */
/* INCLUDE FILE */
include_once("functions/string.func.php");
include_once("functions/date.func.php");
include_once("functions/default.func.php");

/* VARIABLE */
$reqId  = $this->input->get("reqId");
 
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
    
	 <script type="text/javascript" src="js/jquery-1.6.1.min.js"></script>
    
    <!-- PAGINATION -->
    <link rel="stylesheet" type="text/css" href="lib/drupal/index.css" /> 
    <script src="lib/emodal/eModal.js"></script>
  </head>

<body class="body-popup">

    <div class="card mb-1 border-darken-1">
      <div class="card-content">
        <div class="p-1">
          <div class="alert bg-primary alert-icon-left alert-arrow-left alert-dismissible mb-2" role="alert">
            <span class="alert-icon"><i class="fa fa-th"></i></span>  <strong>Rekam Jejak Reschedule Jadwal</strong>
          </div> 
          
          <div class="table-responsive">
            <table class="table mb-0 table-bordered">
              <tbody>
                <tr valign="top" class="judul-kolom">
                  <th rowspan="2" valign="middle" style="width:1%; text-align: center">No</th>
                  <th rowspan="2" valign="middle" style="width:48%; text-align: center">Tahapan <br><?= $paketInfo->metode_lelang_nama ?></th>
                  <th colspan="6" valign="top" style=" text-align: center"> Reschedule </th>
                </tr> 
                <tr valign="top" class="judul-kolom">
                  <th style=" text-align: center" width="400px"> 1 </th>
                  <th style=" text-align: center" width="400px"> 2 </th>
                  <th style=" text-align: center" width="400px"> 3 </th>
                  <th style=" text-align: center" width="400px"> 4 </th>
                  <th style=" text-align: center" width="400px"> 5 </th>
                  <th style=" text-align: center" width="400px"> 6 </th>
                </tr>
                <?php 
                $this->load->model("Paket");
                $paket_reschedule_rekamjejak = new Paket();
                $paket_reschedule_rekamjejak->selectByParamsReschedule(array('PAKET_ID'=>$reqId));
                $no=1;
                while($paket_reschedule_rekamjejak->nextRow())
                {  ?>
                  <tr>
                    <td width="5px"><?= $no ?></td>
                    <td><?= $paket_reschedule_rekamjejak->getField('NAMA');  ?></td>
                    <td style="font-size:10px"><?= str_replace("||","s/d <br>",$paket_reschedule_rekamjejak->getField('reschedule_1')); ?></td>
                    <td style="font-size:10px"><?= str_replace("||","s/d <br>",$paket_reschedule_rekamjejak->getField('reschedule_2')); ?></td>
                    <td style="font-size:10px"><?= str_replace("||","s/d <br>",$paket_reschedule_rekamjejak->getField('reschedule_3')); ?></td>
                    <td style="font-size:10px"><?= str_replace("||","s/d <br>",$paket_reschedule_rekamjejak->getField('reschedule_4')); ?></td>
                    <td style="font-size:10px"><?= str_replace("||","s/d <br>",$paket_reschedule_rekamjejak->getField('reschedule_5')); ?></td>
                    <td style="font-size:10px"><?= str_replace("||","s/d <br>",$paket_reschedule_rekamjejak->getField('reschedule_6')); ?></td>
                  </tr>

                <?php 
                $no++;
                } 
                ?>
              </tbody>
            </table>
          </div>

        </div>
      </div>
    </div> 
    
    <script src="lib/bootstrap/libs/jquery/1.12.4/jquery.min.js"></script>
    <script>window.jQuery || document.write('<script src="lib/bootstrap/assets/js/vendor/jquery.min.js"><\/script>')</script>
    <script src="lib/bootstrap/dist/js/bootstrap.min.js"></script>
    <link rel="stylesheet" type="text/css" href="<?=base_url()?>lib/eproc/themes/default/easyui.css">
    <script type="text/javascript" src="<?=base_url()?>lib/eproc/jquery.easyui.min.js"></script>
    <script type="text/javascript" src="<?=base_url()?>lib/eproc/kalender-easyui.js"></script>
    <script type="text/javascript" src="<?=base_url()?>lib/eproc/allfunc.js"></script>
  </body>
</html>
