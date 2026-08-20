<?php
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 * 
 */

/* INCLUDE FILE */
$this->load->library("kauth");  $userLogin = new kauth(); 
$this->load->model("RekananAkta");
$this->load->model("Rekanan");
include_once("functions/string.func.php");
include_once("functions/date.func.php");
include_once("functions/default.func.php");

/* create objects */
$rekanan = new Rekanan();
$rekanan_akta = new RekananAkta();
$rekanan_akta_perubahan = new RekananAkta();
$rekanan_akta_perubahan_history = new RekananAkta();
$rekanan_get_nama = new Rekanan();

$FILE_DIR = "uploads/landasan_hukum/";

/* VARIABLE */
$reqId = $this->input->get("reqId");

$reqPaketId = httpFilterRequest("reqPaketId");
$reqMode = httpFilterRequest("reqMode");
$reqKoreksi = httpFilterRequest("reqKoreksi");
$reqSubmit = httpFilterRequest("reqSubmit");

/* VALIDATION */
if($reqId=='')
	$reqId = $this->ID;
else
	$reqId = $reqId;    
// trigger the validation

/* ACTION BY REQMODE */
$rekanan->selectByParams(array("REKANAN_ID"=>$reqId));
$rekanan->firstRow();


$rekanan_akta->selectByParams(array("REKANAN_ID"=>$reqId, "AKTA_TYPE_ID" => 1),-1,-1, ' ORDER BY REKANAN_AKTA_ID DESC LIMIT 1');
$rekanan_akta->firstRow();
$tempNomor = $rekanan_akta->getField("NOMOR");
$tempNomorKemenkumham = $rekanan_akta->getField("NOMOR_KEMENKUMHAM");
$tempTanggal = getFormattedDateJson($rekanan_akta->getField("TANGGAL"));
$tempNotaris = $rekanan_akta->getField("NOTARIS");
$tempLinkFileTemp= $rekanan_akta->getField("PATH_FILE");

$rekanan_akta_perubahan->selectByParams(array("REKANAN_ID"=>$reqId, "AKTA_TYPE_ID" => 2),-1,-1, ' ORDER BY REKANAN_AKTA_ID DESC LIMIT 1');
$rekanan_akta_perubahan->firstRow();
$tempNomor1 = $rekanan_akta_perubahan->getField("NOMOR");
$tempNomor1Kemenkumham = $rekanan_akta_perubahan->getField("NOMOR_KEMENKUMHAM");
$tempTanggal1 = getFormattedDateJson($rekanan_akta_perubahan->getField("TANGGAL"));
$tempNotaris1 = $rekanan_akta_perubahan->getField("NOTARIS");
$tempLinkFileTemp1 = $rekanan_akta_perubahan->getField("PATH_FILE");

$rekanan_get_nama->selectByParams(array("A.REKANAN_ID"=>$reqId),-1,-1);
$rekanan_get_nama->firstRow();
$tempNama_getNama= $rekanan_get_nama->getField("NAMA");

// $rekanan_akta_perubahan_history->selectByParams(array("REKANAN_ID"=>$reqId, "AKTA_TYPE_ID" => 2),-1,-1, ' AND REKANAN_AKTA_ID NOT IN ('.$rekanan_akta_perubahan->getField("REKANAN_AKTA_ID").') ORDER BY REKANAN_AKTA_ID ASC');
$rekanan_akta_perubahan_history->selectByParams(array("REKANAN_ID"=>$reqId, "AKTA_TYPE_ID" => 2),-1,-1, ' ORDER BY TANGGAL DESC');
// echo $rekanan_akta_perubahan_history->query;
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
    
  </head>

<body class="body-popup">  

  <div class="card mb-1">
    <div class="card-content">
      <div class="p-1">
        <div class="alert bg-primary alert-icon-left alert-arrow-left alert-dismissible mb-2" role="alert">
          <span class="alert-icon"><i class="fa fa-th"></i></span>  <strong>Akta Pendirian</strong>  
        </div> 
        <div class="table-responsive">
          <table class="table table-striped table-hover">
            <tbody>
                <tr>
                    <td width="30%">Nomor Akta:</td>
                    <td>
                      <?=$tempNomor?>
                    </td>
                </tr>
                <tr>
                    <td width="20%">Nomor SK KEMENKUMHAM:</td>
                    <td>
                      <?=$tempNomorKemenkumham?>
                    </td>
                </tr>
                <tr>
                    <td width="20%">Tanggal:</td>
                    <td>
                      <?=$tempTanggal?>
                    </td>
                </tr>
                <tr>
                    <td width="20%">Nama Notaris:</td>
                    <td>
                      <?=$tempNotaris?>
                    </td>
                </tr> 
                <tr>
                    <td width="20%">File Akta Pendirian:</td>
                    <td>
                      <?php
                      if($tempLinkFileTemp == '')
                      {}
                      else
                      {
                       $arrFile = explode(";", $tempLinkFileTemp);
                       for($iFile=0;$iFile<count($arrFile);$iFile++)
                       {
                        if (file_exists($FILE_DIR.$arrFile[$iFile])) {
                          // code...
                      ?>
                              <a href="<?=$FILE_DIR.$arrFile[$iFile]?>" class="taut" target="_blank"><span class="fa fa-download"></span> Download</a>
                      <?php
                        }
                       }
                          }
                      ?>   
                    </td>
                </tr>  
            </tbody> 
          </table>   
          
          <?php
          if($tempNomor1 == "")
          {}
          else
          {
          ?>
          <div class="alert bg-primary alert-icon-left alert-arrow-left alert-dismissible mb-2" role="alert">
            <span class="alert-icon"><i class="fa fa-th"></i></span>  <strong>Akta Perubahan Terakhir</strong>  
          </div> 
          <table class="table table-striped table-hover">
            <tbody>
                <tr>
                    <td width="30%">Nomor Akta :</td>
                    <td>
                      <?=$tempNomor1?>
                    </td>
                </tr>
                <tr>
                    <td width="20%">Nomor SK KEMENKUMHAM:</td>
                    <td>
                      <?=$tempNomor1Kemenkumham?>
                    </td>
                </tr>
                <tr>
                    <td width="20%">Tanggal:</td>
                    <td>
                      <?=$tempTanggal1?>
                    </td>
                </tr>
                <tr>
                    <td width="20%">Nama Notaris:</td>
                    <td>
                      <?=$tempNotaris1?>
                    </td>
                </tr> 
                <tr>
                    <td width="20%">File Akta Perubahan:</td>
                    <td>
                      <?php
                      if($tempLinkFileTemp1 == '')
                      {}
                      else
                      {
                          $arrFile = explode(";", $tempLinkFileTemp1);
                          for($iFile=0;$iFile<count($arrFile);$iFile++)
                          {
                            if (file_exists($FILE_DIR.$arrFile[$iFile])) {
                              // code...
                      ?>
                              <a href="<?=$FILE_DIR.$arrFile[$iFile]?>" class="taut" target="_blank"><span class="fa fa-download"></span> Download</a>
                      <?php
                            }
                          }
                      }
                      ?>  
                    </td>
                </tr>  
            </tbody> 
          </table>   
          <?php
          }

          if ($rekanan_akta_perubahan_history->countRow() > 0) { ?> 
            <!-- <h4>History Perubahan</h4> -->
            <table class="table table-bordered table-hover">
              <tr>
                <th>No. Akta</th>
                <th>No. SK KEMENKUMHAM</th>
                <th>Tanggal</th>
                <th>Nama Notaris</th>
                <th width="5%">File</th>
              </tr>

          <?php 
            while($rekanan_akta_perubahan_history->nextRow()){
            ?>
            <tr>
              <td><?=$rekanan_akta_perubahan_history->getField("NOMOR")?></td>
              <td><?=$rekanan_akta_perubahan_history->getField("NOMOR_KEMENKUMHAM")?></td>
              <td><?=getFormattedDateJson($rekanan_akta_perubahan_history->getField("TANGGAL"))?></td>
              <td><?=$rekanan_akta_perubahan_history->getField("NOTARIS")?></td>
              <td class="text-center">
                <?php 
                if ($rekanan_akta_perubahan_history->getField("PATH_FILE") != '' && file_exists($FILE_DIR.$rekanan_akta_perubahan_history->getField("PATH_FILE"))) {
                ?>
                        <a href="<?=$FILE_DIR.$rekanan_akta_perubahan_history->getField("PATH_FILE")?>" class="taut" target="_blank"><span class="fa fa-download"></span></a>
                <?php
                } ?>
                  
              </td>
            </tr>
          <?php
            }
           ?>
            </table>
          <?php
          } 
          ?>
          
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
