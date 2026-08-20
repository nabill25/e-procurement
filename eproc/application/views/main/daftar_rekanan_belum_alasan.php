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
$this->load->model("Rekanan");

$reqSubject = httpFilterPost("reqSubject");
$reqIsi = httpFilterPost("reqIsi");
$reqId = $this->input->get("reqId");
$reqSubmit = httpFilterPost("reqSubmit");


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
    <script type="text/javascript" src="js/jquery-1.6.1.min.js"></script>
    <!-- PAGINATION -->
    <link rel="stylesheet" type="text/css" href="lib/drupal/index.css" />  	
    <script type="text/javascript">
	$(function(){
		$('#ff').form({
			url:'rekanan_json/daftar_rekanan_belum_alasan',
			onSubmit:function(){
				return $(this).form('validate');
			},
			success:function(data){
				//alert(data);return false;
				$.messager.alert('Info', data, 'info');	
				top.reloadMonitoring();
			}
		});
		
	});
  </script>
  </head>

<body class="body-popup">
        <div class="container-fluid">
        	
            <div class="row">
                <div class="col-md-12">
                    <div class="area-main popup">
                        <div class="judul-halaman">Hapus Rekanan</div>
                        <div class="inner">
                            <div class="area-konten">
                                <div class="area-konten-inner">
                                    <form id="ff" class="form-horizontal" role="form" method="post" novalidate >
                                        <div class="judul-grup">Hapus Rekanan</div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label for="inputEmail" class="col-md-3 control-label harus-diisi">Alasan</label>
                                                    <div class="col-md-2">
                                                        <textarea name="reqIsi" id="reqIsi" cols="50" rows="5" title="Alasan harus diisi" class="required"><?=$reqIsi?></textarea>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="area-tombol-bawah text-center">
                                                    <input type="hidden" name="reqId" id="reqId" value="<?=$reqId?>"/>
                                                    <input type="hidden" name="reqSubmit" id="reqSubmit" value="Submit"/>
                                                    <button type="submit" class="btn-simpan">Simpan</button>      
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
