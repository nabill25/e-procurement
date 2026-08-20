<?php
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 * 
 */
 ?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="favicon.ico">

    <title><?= SYSTEM_NAME.' | '.SYSTEM_NAME_PT ?></title>
    <base href="<?=base_url()?>">
    
    <!-- DATATABLE -->
    <script type="text/javascript" language="javascript" src="lib/DataTables-1.10.7/media/js/jquery.js"></script>

    <!-- Bootstrap core CSS -->
    <link href="lib/bootstrap/dist/css/bootstrap.css" rel="stylesheet">
    
  </head>

<body style="border:0px solid red;">

    Bidang usaha <a title="Tambah Bidang Usaha" class="btn-lookup" id="btnAdd" onClick="openAdd('main/loadUrl/main/bidang_usaha');">Tambah</a>
    
    
    <script src="lib/bootstrap/dist/js/bootstrap.min.js"></script>
    
    <script src="lib/emodal/eModal.js"></script>
    <script>
	function openAdd(pageUrl) {
		eModal.iframe(pageUrl, 'Eprocurement | PT. Angkasa Pura Suport')
	}
	function closePopup() {
		eModal.close();
	}
    </script>
    
  </body>
</html>
