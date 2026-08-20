<?php
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 * 
 */
$this->load->model("RekananPeralatan");
$this->load->model("Rekanan");
include_once("functions/string.func.php");
include_once("functions/date.func.php");
include_once("functions/default.func.php");

$rekanan_peralatan = new RekananPeralatan();
$rekanan_get_nama = new Rekanan();

$reqId = $this->input->get("reqId");
$reqCari = httpFilterPost("reqCari");
$reqInputCari = httpFilterPost("reqInputCari");

$FILE_DIR = "uploads/peralatan/";

//$reqId = 449;
$rekanan_peralatan->selectByParams(array('REKANAN_PERALATAN_ID'=>$reqId), -1, -1, $statement);
$rekanan_peralatan->firstRow();

$rekanan_get_nama->selectByParams(array("REKANAN_ID"=>$rekanan_peralatan->getField("REKANAN_ID")),-1,-1);
$rekanan_get_nama->firstRow();
$tempNama_getNama= $rekanan_get_nama->getField("NAMA");
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

    <!-- Bootstrap core CSS -->
    <link href="lib/bootstrap/dist/css/bootstrap.css" rel="stylesheet">

    
    <link rel="stylesheet" href="css/gaya.css" type="text/css">
    <link rel="stylesheet" href="css/gaya-bootstrap.css" type="text/css">
    
    <!-- FONT AWESOME -->
    <link rel="stylesheet" href="lib/font-awesome-4.7.0/css/font-awesome.css" type="text/css">
    
    <!-- PAGINATION -->
    <link rel="stylesheet" type="text/css" href="lib/drupal/index.css" />  	
    <script language="JavaScript" src="jslib/displayElement.js"></script>
    
  </head>

<body class="body-popup">
        <div class="container-fluid">
        	
           <div class="row">
                <div class="col-md-12">
                    <div class="area-main popup">
                        <div class="judul-halaman">PERALATAN</div>
                        <div class="inner">
                            <div class="area-konten">
                                <div class="area-konten-inner">
                                    <form id="ff" class="form-horizontal" role="form">
                                        <div class="judul-grup">PERALATAN</div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <table width="100%" border="0" cellpadding="2" cellspacing="1">
                                                          <tbody>
                                                            <tr>
                                                              <td style="padding-left:10px; padding-right:10px;">
                                                              <div id="data-paket">
                                                                <table width="100%" border="0" align="center" cellpadding="2" cellspacing="1">          
                                                                <tbody>
                                                                <tr>
                                                                    <td>Jenis</td>
                                                                    <td>:</td>
                                                                    <td><?=$rekanan_peralatan->getField("JENIS")?></td>
                                                                </tr> 
                                                                <tr>
                                                                    <td>Jumlah</td>
                                                                    <td>:</td>
                                                                    <td><?=$rekanan_peralatan->getField("JUMLAH")?></td>
                                                                </tr> 
                                                                <tr>
                                                                    <td>Kapasitas</td>
                                                                    <td>:</td>
                                                                    <td><?=$rekanan_peralatan->getField("KAPASITAS")?></td>
                                                                </tr> 
                                                                <tr>
                                                                    <td>Merk</td>
                                                                    <td>:</td>
                                                                    <td><?=$rekanan_peralatan->getField("MERK")?></td>
                                                                </tr>
                                                                <tr>
                                                                    <td>Th. Pembuatan</td>
                                                                    <td>:</td>
                                                                    <td><?=$rekanan_peralatan->getField("TAHUN")?></td>
                                                                </tr>
                                                                <tr>  
                                                                    <td>Kondisi</td>
                                                                    <td>:</td>
                                                                    <td><?=$rekanan_peralatan->getField("KONDISI")?></td>
                                                                </tr>
                                                                <tr>  
                                                                    <td>Lokasi</td>
                                                                    <td>:</td>
                                                                    <td><?=$rekanan_peralatan->getField("LOKASI")?></td>
                                                                </tr>
                                                                <tr>
                                                                    <td>Kepemilikan</td>
                                                                    <td>:</td>
                                                                    <td><?=$rekanan_peralatan->getField("BUKTI_KEPEMILIKAN")?></td>
                                                                </tr>
                                                                <tr>  
                                                                    <td>File</td>
                                                                    <td>:</td>
                                                                    <td><a href="<?=$FILE_DIR.$rekanan_peralatan->getField("PATH_FILE")?>" class="taut" target="_blank"><?=$rekanan_peralatan->getField("PATH_FILE")?></a></td>
                                                                </tr>
                                                                </tbody>
                                                                </table>
                                                              </div>
                                                              </td>
                                                            </tr>
                                                          </tbody>
                                                        </table>
                                                    <div class="col-md-8">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div> <!-- /container -->
    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="lib/bootstrap/libs/jquery/1.12.4/jquery.min.js"></script>
    <script>window.jQuery || document.write('<script src="lib/bootstrap/assets/js/vendor/jquery.min.js"><\/script>')</script>
    <script src="lib/bootstrap/dist/js/bootstrap.min.js"></script>
    
	
    
    <!-- EASYUI -->
    <link rel="stylesheet" type="text/css" href="lib/eproc/themes/default/easyui.css">
	<script type="text/javascript" src="js/jquery-1.6.1.min.js"></script>
    <script type="text/javascript" src="lib/eproc/jquery.easyui.min.js"></script>
    <script type="text/javascript" src="lib/eproc/kalender-easyui.js"></script>
    <script type="text/javascript" src="lib/eproc/allfunc.js"></script>
	
    
  </body>
</html>
