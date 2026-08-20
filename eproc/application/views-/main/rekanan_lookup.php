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
<link rel="stylesheet" href="css/core-bootstrap.css" type="text/css">
<link href="lib/bootstrap/bootstrap.css" rel="stylesheet">
<link rel="stylesheet" href="lib/font-awesome/4.5.0/css/font-awesome.css">
<script src="js/jquery-1.11.1.js" type="text/javascript" charset="utf-8"></script>
<link rel="stylesheet" type="text/css" href="lib/eproc/themes/default/easyui.css">
<script type="text/javascript" src="lib/eproc/jquery.easyui.min.js"></script>
<script type="text/javascript" src="lib/eproc/kalender-easyui.js"></script>
<script type="text/javascript" src="lib/eproc/allfunc.js"></script>
<link rel="stylesheet" href="lib/font-awesome-4.7.0/css/font-awesome.css" type="text/css">

<style>
.col-md-12{ padding-left:0px; padding-right:0px; }
html, body{ height:100%; }
.tree-folder-open, .tree-file  { display: none; }
.tableContainer-treegrid { padding:0 10px; }
/*.datagrid .panel-body, .datagrid-view { overflow: scroll !important; }*/
.datagrid .panel-body, .datagrid-view { overflow: auto !important; }
</style>

</head>

<body class="body-popup">

    <div class="container-fluid container-treegrid">
    	<div class="row row-treegrid">
        	<div class="col-md-12 col-treegrid">
            	<div class="area-konten-atas">
                	<div class="judul-halaman">
                    		Pilih Penyedia..
                    	<div class="info">
							<i class="fa fa-warning" aria-hidden="true"></i> Double-click untuk memilih penyedia.
                        </div>
                    </div>
                </div>
             	<div id="idpencarian" class="area-pencarian">
                    <input type="checkbox" id="reqCheckbox" value="1" > Tampilkan Semua Bidang Usaha
	                <input type="checkbox" id="reqCheckboxKualifikasi" value="1" > Tampilkan Semua Kualifikasi
	                Pencarian : <input type="text" name="reqPencarian" id="reqPencarian">
                </div>
				<div id="tableContainer" class="tableContainer tableContainer-treegrid">
                	<table id="treeSatker" class="easyui-treegrid"
                    data-options="
                        url: 'rekanan_json/get_data_rekanan/?reqId=<?=$reqId?>',
                        method: 'get',
                        idField: 'id',
                        treeField: 'text'
                    ">
                <thead>
                    <tr>
                        <th data-options="field:'NAMA'" style="width:70% !important" >Nama</th>
                        <!-- <th data-options="field:'ALAMAT'" >Alamat</th> -->
                        <th data-options="field:'EMAIL'" >Email</th>
                        <th data-options="field:'SIUP'" >NIB</th>
                        <th data-options="field:'TOTAL_UNDANG'" >Terundang</th>
                        <th data-options="field:'TOTAL_PEMENANG'" >Menang</th>
                        <!-- <th data-options="field:'IUJK'" >IUJK</th> -->
                        <!-- <th data-options="field:'SBUJK'" >SBUJK</th> -->
                    </tr>
                </thead>
            </table>
                </div>

            </div>
        </div>

    </div>
<!--
    <div class="container-fluid container-treegrid">

        <div class="row row-treegrid">
        	<div class="col-md-12 col-treegrid">
            	<div class="area-konten-atas">
                	<div class="judul-halaman">Rekanan</div>
                </div>
                <div id="tableContainer" class="tableContainer tableContainer-treegrid">
                </div>

            </div>
        </div>
    </div> -->

	<script>

    	$(document).ready( function () {
			$('input[name=reqPencarian]').change(function() {
				var value = this.value;
				var reqCheckbox = 0;
				var reqCheckboxKualifikasi = 0;

				if($("#reqCheckbox").is(":checked"))
					reqCheckbox = 1;

				if($("#reqCheckboxKualifikasi").is(":checked"))
					reqCheckboxKualifikasi = 1;

				var urlApp = 'rekanan_json/get_data_rekanan/?reqId=<?=$reqId?>&reqCheckbox='+ reqCheckbox+'&reqCheckboxKualifikasi='+reqCheckboxKualifikasi+'&reqPencarian='+value;


				$('#treeSatker').treegrid(
				{
					url: urlApp
				});

			});

		$("#reqCheckbox").change(function() {
			var reqCheckbox = 0;
			var reqCheckboxKualifikasi = 0;

			if(this.checked)
				reqCheckbox = 1;


			if($("#reqCheckboxKualifikasi").is(":checked"))
				reqCheckboxKualifikasi = 1;

			var urlApp = 'rekanan_json/get_data_rekanan/?reqId=<?=$reqId?>&reqCheckbox='+ reqCheckbox+'&reqCheckboxKualifikasi='+reqCheckboxKualifikasi+'&reqPencarian='+$("#reqPencarian").val();
			$('#treeSatker').treegrid(
			{
				url: urlApp
			});

		});

			$("#reqCheckboxKualifikasi").change(function() {
				var reqCheckbox = 0;
				var reqCheckboxKualifikasi = 0;

				if(this.checked)
					reqCheckboxKualifikasi = 1;


				if($("#reqCheckbox").is(":checked"))
					reqCheckbox = 1;

				var urlApp = 'rekanan_json/get_data_rekanan/?reqId=<?=$reqId?>&reqCheckbox='+ reqCheckbox+'&reqCheckboxKualifikasi='+reqCheckboxKualifikasi+'&reqPencarian='+$("#reqPencarian").val();
				$('#treeSatker').treegrid(
				{
					url: urlApp
				});

			});

        	$('#treeSatker').treegrid({
	              onDblClickRow: function(param){
					  <?php
					  	$this->load->model("PaketRekanan");
						$paket_rekanan = new PaketRekanan();
						$hitung_rekanan = $paket_rekanan->getCountByParamsHitungRekanan(array("A.PAKET_ID" => $reqId));
					  	//echo $paket_rekanan->query;exit;
					  ?>
					 	top.tambahRekanan(param.REKANAN_ID, param.NAMA, param.ALAMAT, param.EMAIL);
			  			$('#treeSatker').treegrid('deleteRow', param.id);

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
		var divTinggi = $(".area-konten-atas").height();
		$('#tableContainer').css({ 'height': 'calc(100% - ' + divTinggi+ 'px)' });
	</script>

</body>
</html>
