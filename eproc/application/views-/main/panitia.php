<?php
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 * 
 */
$reqId = $this->input->get("reqId");
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
<base href="<?=base_url();?>" />
<link rel="stylesheet" href="css/core.css" type="text/css">
<script src="js/jquery-1.11.1.js" type="text/javascript" charset="utf-8"></script> 
<link rel="stylesheet" type="text/css" href="lib/eproc/themes/default/easyui.css">
<script type="text/javascript" src="lib/eproc/jquery.easyui.min.js"></script>
<script type="text/javascript" src="lib/eproc/kalender-easyui.js"></script>
<script type="text/javascript" src="lib/eproc/allfunc.js"></script>

<style>
	.col-md-12{ padding-left:0px; padding-right:0px; }
	html, body{ height:100%; overflow: scroll; } 
	.tree-folder-open, .tree-file  { display: none; }
.tableContainer-treegrid { padding:0 10px; }
</style>
    
</head>
 
<body class="body-popup">
 
 <div class="container-fluid container-treegrid">
    <div class="row row-treegrid">
    	<div class="col-md-12 col-treegrid">
        	<div class="area-konten-atas">
            	<div class="judul-halaman">Panitia
                	<div class="info">
						<i class="fa fa-warning" aria-hidden="true"></i> Double-click untuk memilih panitia.  
                    </div>
                </div>
                
            </div>
            <div id="idpencarian" class="area-pencarian" style="padding:10px">
            	<input class="" placeholder="  Pencarian" name="reqPencarian" aria-controls="example" type="search" style="width:100%; border-radius:20px; height: 30px;">
            </div>
			<div id="tableContainer" class="tableContainer tableContainer-treegrid">
            	<table id="treeSatker" class="easyui-treegrid" style="width:100%;height:100px"
		                data-options="
		                    url: 'panitia_json/get_data_daftar_panitia/?reqId=<?=$reqId?>',
		                    method: 'get',
		                    idField: 'id',
		                    treeField: 'text', 
		                ">
		            <thead>
		                <tr>
		                    <th data-options="field:'NIP'" >NUP</th>
		                    <th data-options="field:'NAMA'" >Nama</th>
		                    <th data-options="field:'JABATAN'" >Jabatan</th>
		                    <th data-options="field:'JABATAN_STR'" >#</th>
		                    <!-- <th data-options="field:'BEBAN_PAKET_PANITIA'" >Beban Paket</th> -->
		                    <!-- <th data-options="field:'BEBAN_PAKET_PANITIA_PROSES'" >Beban Paket Proses</th> -->
		                    <!-- <th data-options="field:'PAKTA'" >Pakta Integritas</th> -->
		                </tr>
		            </thead>
		        </table>
            </div>
            
        </div>
    </div>        
</div> 

	<script>

    $(document).ready( function () {

    	$('input[name=reqPencarian]').change(function() {
			var value = this.value;
			$("html, body").animate({ scrollTop: 0 });
	
			var urlApp = 'panitia_json/get_data_daftar_panitia/?reqId=<?=$reqId?>&reqSearch='+ value;
			$('#treeSatker').treegrid(
			{
				url: urlApp
			});	
		});

        $('#treeSatker').treegrid({
              onDblClickRow: function(param){
				 top.tambahPanitia(param.NIP, param.NAMA,  param.JABATAN);
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
