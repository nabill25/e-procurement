<?php
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 * 
 */

/* INCLUDE FILE */
$this->load->model("RekananEvaluasiSyaratDaftar");
include_once("functions/default.func.php");
include_once("functions/date.func.php");
include_once("functions/string.func.php");

/* create objects */
$rekanan_evaluasi_syarat_daftar = new RekananEvaluasiSyaratDaftar();

/* VARIABLE */
$reqId = $this->input->get("reqId");
$reqCaption = $this->input->get("reqCaption");

$FILE_DIR = "uploads/syarat_pendaftaran/";

$rekanan_evaluasi_syarat_daftar->selectByParams(array("PAKET_EVAL_SYARAT_DAFTAR_ID" => $reqId, "REKANAN_ID" => $this->ID));
$rekanan_evaluasi_syarat_daftar->firstRow(); 

$tempPathFile = $rekanan_evaluasi_syarat_daftar->getField("PATH_FILE");
	
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

    <!-- EASYUI -->
    <link rel="stylesheet" type="text/css" href="lib/eproc/themes/default/easyui.css">
	<script type="text/javascript" src="js/jquery-1.6.1.min.js"></script>
    <script type="text/javascript" src="lib/eproc/jquery.easyui.min.js"></script>
    <script type="text/javascript" src="lib/eproc/kalender-easyui.js"></script>
    <script type="text/javascript" src="lib/eproc/allfunc.js"></script>

	<script type="text/javascript">
    $(document).ready(function() {
        
        $(function(){
            $('#ff').form({
                url:'rekanan_evaluasi_syarat_daftar_json/add',
                onSubmit:function(){
                    return $(this).form('validate');
                },
                success:function(data){
					top.setElementValue('reqSyaratLain<?=$reqId?>','Data Lengkap');
					top.closePopup();
                }
            });
            
        });
        
    });
    </script>

</head>


<body class="body-popup">
	<div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="area-main popup">
                    <div class="judul-halaman">UPLOAD DATA LAINNYA </div>
                    <div class="inner">
                        <div class="area-konten">
                            <div class="area-konten-inner">
                            
                                <form id="ff" class="easyui-form form-horizontal" method="post" novalidate enctype="multipart/form-data">
                                    
                                    <div class="judul-grup">Data <?=$reqCaption?></div>
                                    
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="inputEmail" class="col-md-2 control-label">File *</label>
                                                <div class="col-md-8">
                                                <input type="file" name="reqLinkFile" id="reqLinkFilePDF" size="30"  class="easyui-validatebox"  validType="fileType['pdf']" <?php if($tempPathFile == "") { ?> required <?php } ?> /><br />temp : <?=$tempPathFile?>
									            <input type="hidden" name="reqLinkFileTemp" value="<?=$tempPathFile?>" />
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="inputEmail" class="col-md-2 control-label">&nbsp;</label>
                                                <div class="col-md-4">
                                                
									           	 	<input type="hidden" name="reqId" value="<?=$reqId?>" />
									           	 	<input type="hidden" name="submitSimpan" value="Simpan" />
                                                    <button type="submit" class="btn btn-primary">Simpan</button>
                                                    <button type="button" class="btn btn-primary" onClick="top.closePopup()">Batal</button>
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
 	</div>
</body>