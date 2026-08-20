<?php
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 * 
 */

$this->load->library("kauth");  $userLogin = new kauth(); 
$this->load->model("PaketEvaluasiSyaratDaftar");
$this->load->model("Rekanan");
include_once("functions/string.func.php");
include_once("functions/date.func.php");
include_once("functions/default.func.php");

$paket_evaluasi_syarat_daftar = new PaketEvaluasiSyaratDaftar();

$reqPaketId = $this->input->get("reqPaketId");
$reqId = $this->input->get("reqId");

$paket_evaluasi_syarat_daftar->selectByParamsPersyaratan($reqId, array("PAKET_ID" => $reqPaketId), -1, -1, " AND EVALUASI_NUMBER IS NULL ");
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
                        <div class="judul-halaman">Persyaratan Pendaftaran</div>
                        <div class="inner">
                            <div class="area-konten">
                                <div class="area-konten-inner">
                                    <form id="ff" class="form-horizontal" role="form">
                                    	
                                        
                                        <div class="judul-grup">Persyaratan Pendaftaran </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <table width="100%" border="0" cellpadding="2" cellspacing="1" id="tbl_bidang">
                                                      <tbody>
                                                      	<tr class="judul-kolom">
                                                          <th align="center">No.</th>
                                                          <th align="center">Persyaratan</th>
                                                          <th align="center">Informasi Tambahan</th>
                                                          <th align="center">File</th>
                                                        </tr>
                                                        <?php
														$i=0;
														while($paket_evaluasi_syarat_daftar->nextRow())
														{
														?>   
                                                      		<tr class="<?=$css?>">              
                                                                <td><?=$i+1?></td>
                                                                <td><?=$paket_evaluasi_syarat_daftar->getField("NAMA")?></td>
                                                                <td><?=$paket_evaluasi_syarat_daftar->getField("KETERANGAN")?></td>
                                                                <td><a href="uploads/syarat_pendaftaran/<?=$paket_evaluasi_syarat_daftar->getField("PATH_FILE")?>" class="taut" target="_blank"><?=$paket_evaluasi_syarat_daftar->getField("PATH_FILE")?></a></td>
                                                            </tr>
                                                        <?php
                                                            $i++;
                                                        }
                                                        ?>
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
