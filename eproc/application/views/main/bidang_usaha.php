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
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
<base href="<?=base_url();?>" />

<link rel="stylesheet" href="css/core.css" type="text/css">
<link rel="stylesheet" href="css/core-bootstrap.css" type="text/css">
<link rel="stylesheet" href="lib/font-awesome-4.7.0/css/font-awesome.css" type="text/css">
<link href="lib/bootstrap/bootstrap.css" rel="stylesheet">
<link rel="stylesheet" href="lib/font-awesome/4.5.0/css/font-awesome.css">
<link rel="stylesheet" type="text/css" href="lib/eproc/themes/default/easyui.css">
<script src="js/jquery-1.11.1.js" type="text/javascript" charset="utf-8"></script> 
<script type="text/javascript" src="lib/eproc/jquery.easyui.min.js"></script>
<script type="text/javascript" src="lib/eproc/kalender-easyui.js"></script>
<script type="text/javascript" src="lib/eproc/allfunc.js"></script>

<style>
.col-md-12{ padding-left:0px; padding-right:0px; }
html, body{ height:100%; }
.tree-folder-open, .tree-file  { display: none; }
.tableContainer-treegrid { padding:0 10px; }
</style>
    
</head>

<body class="body-popup">
	
    <div class="container-fluid container-treegrid">
    	
        <div class="row row-treegrid">
        	<div class="col-md-12 col-treegrid">
            	<div class="area-konten-atas">
                	<div class="judul-halaman">
                    		K B L I
                    	<div class="info">
							<i class="fa fa-warning" aria-hidden="true"></i> Double-click untuk memilih bidang usaha.  
                        </div>
                    </div>
                </div>
             	<div id="idpencarian" class="area-pencarian" style="padding:10px">
                	<input class="" placeholder="  Pencarian K B L I" name="reqPencarian" id="reqPencarian" aria-controls="example" type="search" style="width:100%; border-radius:20px; height: 30px; padding-left: 20px;">
                </div>
				<div id="tableContainer" class="tableContainer tableContainer-treegrid">
                	<table id="treeSatker" class="easyui-treegrid" style="width:100%;height:100px"
                            data-options="
                                url: 'bidang_usaha_json/jsonaktif',
                                method: 'get',
                                idField: 'id',
                                treeField: 'text'
                            ">
                        <thead>
                            <tr>
                                <th data-options="field:'text'" width="100%"><span style="margin-left: 20px;">Nama</span></th>
                            </tr>
                        </thead>
                    </table>
                </div>
                
            </div>
        </div>        
    </div>
    
	<script>
	window.onload = function() {
		document.getElementById("reqPencarian").focus();
	}
    $(document).ready( function () {
        // $('input[name=reqPencarian]').keyup(function() {
		$('input[name=reqPencarian]').change(function() {
			var value = this.value;
			$("html, body").animate({ scrollTop: 0 });
	
			var urlApp = 'bidang_usaha_json/jsonaktif/?reqSearch='+ value;
			$('#treeSatker').treegrid(
			{
				url: urlApp
			});	
		});
		
        $('#treeSatker').treegrid({
              onDblClickRow: function(param){
				 top.tambahBidangUsaha(param.id, param.text);
              }
        });
    });
	    
	$("#dnd-example tr").click(function(){
	   $(this).addClass('selected').siblings().removeClass('selected');
	   var id = $(this).find('td:first').attr('id');
	   var title = $(this).find('td:first').attr('title');
	});

	var divTinggi = $(".area-konten-atas").height();
	$('#tableContainer').css({ 'height': 'calc(100% - ' + divTinggi+ 'px)' });
	</script>

</body>
</html>
