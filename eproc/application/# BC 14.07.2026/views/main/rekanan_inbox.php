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

<!-- BOOTSTRAP -->
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
<!-- <link href="lib/bootstrap/bootstrap.css" rel="stylesheet"> -->
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
<script type="text/javascript">

function reloadMonitoring()
{
	$('#treeSatker').treegrid('reload');	
}

</script>
<!-- FONT AWESOME -->
<link rel="stylesheet" href="lib/font-awesome-4.7.0/css/font-awesome.css" type="text/css">

</head>

<body>
<div class="card mb-1 border-darken-1">
  <div class="card-content">
    <div class="p-1">
      <div class="alert bg-primary alert-icon-left alert-arrow-left alert-dismissible mb-2" role="alert">
        <span class="alert-icon"><i class="fa fa-th"></i></span>  <strong>Rekanan</strong> <i class="fa fa-warning" aria-hidden="true"></i> Double-click untuk memilih rekanan.  
      </div> 
      <div class="p-1">                  
        <div class="row row-treegrid">
        	<div class="col-md-12 col-treegrid">
            	<div class="area-konten-atas">
                	<div class="judul-halaman">
                    </div>
                </div>
                <div id="idpencarian" class="area-pencarian">
                    <label>Ketik nama rekanan : <input class="" placeholder="" name="reqPencarian" aria-controls="example" type="search"></label>
                </div>
				<div id="tableContainer" class="tableContainer tableContainer-treegrid">
                	<table id="treeSatker" class="easyui-treegrid table"
                            data-options="url: 'rekanan_json/get_data_daftar_rekanan',  method: 'get',idField: 'id',treeField: 'text'">
                        <thead>
                            <tr>
                                <th data-options="field:'KODE'" >KODE</th>
                                <th data-options="field:'NAMA'" >NAMA</th>
                                <th data-options="field:'NPWP'" >NPWP</th>
                                <th data-options="field:'ALAMAT'" >ALAMAT</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>        
         
      </div>
    </div>
  </div>
</div> 

<link rel="stylesheet" type="text/css" href="<?=base_url()?>lib/eproc/themes/default/easyui.css">
<script type="text/javascript" src="<?=base_url()?>lib/eproc/jquery.easyui.min.js"></script>
<script type="text/javascript" src="<?=base_url()?>lib/eproc/kalender-easyui.js"></script>
<script type="text/javascript" src="<?=base_url()?>lib/eproc/allfunc.js"></script>
    
	<script>

    $(document).ready( function () 
	{
		$('input[name=reqPencarian]').keypress(function( event ) {
			if ( event.which == 13 ) {
				var value = this.value;
				
				$("html, body").animate({ scrollTop: 0 });
		
				var urlApp = 'rekanan_json/get_data_daftar_rekanan/?reqSearch='+ value;
				$('#treeSatker').treegrid(
				{
					url: urlApp
				});	
			}
		});
	
        $('#treeSatker').treegrid(
		{
			  onDblClickRow: function(param)
			  {
				 parent.tambahRekananBlacklist(param.id, param.NAMA, param.ALAMAT, param.KOTA, param.NPWP);
				 parent.closePopup();
			  }
        });
    });
	    
		$("#dnd-example tr").click(function(){
		   $(this).addClass('selected').siblings().removeClass('selected');
		   var id = $(this).find('td:first').attr('id');
		   var title = $(this).find('td:first').attr('title');
		});
		
	/** FIXED AREA-MENU-AKSI WHEN SCROLLING UP **/
	$(document).ready(function() {
		var s = $("#sticker");
		var f = $("#idpencarian");
		var pos = s.position();
		var posfilter = f.position();  
		
		$(window).scroll(function() {
			var windowpos = $(window).scrollTop();
	
			//if (windowpos >= pos.top) {
			if (windowpos >= 107) {
				s.addClass("stick");
				f.addClass("stickfilter");
			} else {
				s.removeClass("stick"); 
				f.removeClass("stickfilter"); 
			}
		});	
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
