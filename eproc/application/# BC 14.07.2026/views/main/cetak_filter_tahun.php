<?php
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 * 
 */

include_once("functions/string.func.php");
include_once("functions/date.func.php");
include_once("functions/default.func.php");

/* VARIABLES */
$reqSubmit= $this->input->post("reqSubmit");

$reqTahunPajak = $this->input->get("reqTahunPajak"); 
$reqMetode = $this->input->get("reqMetode"); 

// if ($reqMetode == 'tender')
//     $paket_metode = '1';
// else
//     $paket_metode = '2,5,6,8'

if($reqTahunPajak=='')
	$reqTahunPajak = date("Y");
else
	$reqTahunPajak = $reqTahunPajak;

?>
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
        <span class="alert-icon"><i class="fa fa-th"></i></span>  <strong>Tahun Paket</strong>
      </div> 
      <div class="p-1">
        <form id="ff" method="post" class="form-horizontal" role="form">
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="inputEmail" class="col-md-3 control-label harus-diisi">Pilih Tahun</label>
                            <div class="col-md-6">
                                <select class="form-control" name="reqTahunPajak" id="reqTahunPajak" onChange="document.location.href='main/loadUrl/main/cetak_filter_tahun/?reqMetode=<?= $reqMetode ?>&reqTahunPajak='+this.value" >
                                    <?php
                                    for($i=date('Y')-2;$i<=date('Y')+1; $i++)
                                    {
                                    ?>
                                      <option value="<?=$i?>" <?php if($i == $reqTahunPajak) { ?> selected="selected" <?php } ?>><?=$i?></option>
                                    <?php
                                    }
                                    ?>
                              </select>   
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="inputEmail" class="col-md-2 control-label">&nbsp;</label>
                        <div class="col-md-4">
                            <a class="<?= CLASS_BTN_PRIMARY ?>" href="main/loadUrl/report/rekapitulasi_pekerjaan_excel/?reqTahun=<?=$reqTahunPajak?>&reqMetode=<?= $reqMetode ?>" target="_blank" ><?= BTN_PRINT ?></a>
                        </div>
                    </div>
                </div>
            </div>
            <!--<button onClick="top.closePopup()">hai</button>
            -->
        </form>
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
