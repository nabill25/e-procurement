<?php
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 * 
 */
 ?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<base href="<?=base_url();?>" />

<!-- File Load Popup Jquery Easy -->
<link rel="stylesheet" href="css/core.css" type="text/css">
<link rel="stylesheet" href="css/core-bootstrap.css" type="text/css">

<!-- BOOTSTRAP -->
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
<link href="lib/bootstrap/bootstrap.css" rel="stylesheet">
<link rel="stylesheet" href="lib/font-awesome/4.5.0/css/font-awesome.css">

<script src="js/jquery-1.11.1.js" type="text/javascript" charset="utf-8"></script> 

    <style>
  .col-md-12{
    padding-left:0px;
    padding-right:0px;
  }
  </style>

<!-- EASYUI -->
<link rel="stylesheet" type="text/css" href="lib/eproc/themes/default/easyui.css">
<script type="text/javascript" src="lib/eproc/jquery.easyui.min.js"></script>
<script type="text/javascript" src="lib/eproc/kalender-easyui.js"></script>
<script type="text/javascript" src="lib/eproc/allfunc.js"></script>

<!-- FONT AWESOME -->
<link rel="stylesheet" href="lib/font-awesome-4.7.0/css/font-awesome.css" type="text/css">

<style>
html, body{
  height:100%;
}
</style>

</head>

<body class="body-popup">
    <div class="container-fluid container-treegrid">
        
        <div class="row-treegrid">
            <div class="col-md-12 col-treegrid">
                <div class="area-konten-atas">
                    <div class="judul-halaman">
                            Bidang Usaha
                        <div class="info">
                            <i class="fa fa-warning" aria-hidden="true"></i> Double-click untuk memilih bidang usaha.  
                        </div>
                    </div>
                </div>
                <div id="idpencarian" class="area-pencarian">
                        <label>Pencarian : <input class="" placeholder="" name="reqPencarian" aria-controls="example" type="search"></label>
                </div>
                <div id="tableContainer" class="tableContainer tableContainer-treegrid">
                    <table id="treeSatker" class="easyui-treegrid"
                            data-options="
                                url: 'bidang_usaha_json/bidang_usaha_combo_json',
                                method: 'get',
                                idField: 'id',
                                treeField: 'text'
                            ">
                        <thead>
                            <tr>
                                <th data-options="field:'text'" width="100%">Nama</th>
                            </tr>
                        </thead>
                    </table>
                </div>
                
            </div>
        </div>        
    </div>

	<script>

    $(document).ready( function () {
        $('#treeSatker').treegrid({
              onDblClickRow: function(param){
				 parent.tambahBidangUsaha(param.id, param.text);
              }
        });
    });
	    
		$("#dnd-example tr").click(function(){
		   $(this).addClass('selected').siblings().removeClass('selected');
		   var id = $(this).find('td:first').attr('id');
		   var title = $(this).find('td:first').attr('title');

			
		});
    
	</script>
    
    <script>
		// Mendapatkan tinggi .area-konten-atas
		var divTinggi = $(".area-konten-atas").height();
		//alert(divTinggi);
		
		// Menentukan tinggi tableContainer
		$('#tableContainer').css({ 'height': 'calc(100% - ' + divTinggi+ 'px)' });
	</script>

</body>
</html>
