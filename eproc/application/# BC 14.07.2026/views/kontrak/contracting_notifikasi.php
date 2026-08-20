<?php 
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 * 
 */
$this->libsession->cekSession();    
$reqId = httpFilterRequest("reqId");


?>
<link rel="stylesheet" type="text/css" href="lib/DataTables-1.10.7/media/css/jquery.dataTables.css">
<link rel="stylesheet" type="text/css" href="lib/DataTables-1.10.7/extensions/Responsive/css/dataTables.responsive.css">
<link rel="stylesheet" type="text/css" href="lib/DataTables-1.10.7/examples/resources/syntax/shCore.css">
<link rel="stylesheet" type="text/css" href="lib/DataTables-1.10.7/examples/resources/demo.css">
<script type="text/javascript" language="javascript" src="lib/DataTables-1.10.7/media/js/jquery.dataTables.js"></script>
<script type="text/javascript" language="javascript" src="lib/DataTables-1.10.7/extensions/Responsive/js/dataTables.responsive.js"></script>
<script type="text/javascript" language="javascript" src="lib/DataTables-1.10.7/examples/resources/syntax/shCore.js"></script>
<script type="text/javascript" language="javascript" src="lib/DataTables-1.10.7/examples/resources/demo.js"></script>

<script type="text/javascript" language="javascript" class="init">
	var oTable; 
$(document).ready(function() {

	oTable = $('#example').dataTable({ bJQueryUI: true,"iDisplayLength": 10,
		/* UNTUK MENGHIDE KOLOM ID */
		"aoColumns": [
						 {"bVisible": false},null,null,null,null,null
				  ],
		"bSort":true,
		"bProcessing": true,
		"bServerSide": true,		
		"sAjaxSource": "contracting_notifikasi_json/json?reqId=<?= $reqId ?>",	
		"lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],	
		columnDefs: [{ className: 'never', targets: [0,1,4] }]
		});//.rowGrouping({iGroupingColumnIndex: 0});

	oTable.fnSort( [ [1,'desc'] ] );

		
		new $.fn.dataTable.Responsive( oTable );
		  var anSelectedData = '';
		  var anSelectedId = '';
		  var anSelectedIdDelete = '';
		  var anSelectedDownload = '';
		  var anSelectedPosition = '';	
					  
		  function fnGetSelected( oTableLocal )
		  {
			  var aReturn = new Array();
			  var aTrs = oTableLocal.fnGetNodes();
			  for ( var i=0 ; i<aTrs.length ; i++ )
			  {
				  if ( $(aTrs[i]).hasClass('row_selected') )
				  {
					  aReturn.push( aTrs[i] );
					  anSelectedPosition = i;
				  }
			  }
			  return aReturn;
		  }
	  
		  $("#example tbody").click(function(event) {
				  $(oTable.fnSettings().aoData).each(function (){
					  $(this.nTr).removeClass('row_selected');
				  });
				  $(event.target.parentNode).addClass('row_selected');
				  //
				  var anSelected = fnGetSelected(oTable);													
				  anSelectedData = String(oTable.fnGetData(anSelected[0]));
				  var element = anSelectedData.split(','); 
				  anSelectedId = element[0];
				  anSelectedIdDelete = element[1];
		  });
		  
		   $('#btnAdd').on('click', function () {
			  openAddFrame("main/loadUrlKontrak/kontrak/contracting_notifikasi_add/?reqPaketId=<?= $reqId ?>");
			  $('div.flexmenumobile').hide()
			  $('div.flexoverlay').css('display', 'none')
	
		  });
		  
		  $('#btnEdit').on('click', function () {
			  if(anSelectedData == "")
			  {
		  		alertError3("Pilih data dahulu");
			 	return false;				
		  	  }					
			  openAddFrame("main/loadUrlKontrak/kontrak/contracting_notifikasi_add/?reqId="+anSelectedId+"&reqPaketId=<?= $reqId ?>");
			  $('div.flexmenumobile').hide()
			  $('div.flexoverlay').css('display', 'none')
		  });
	
		  $('#btnDelete').on('click', function () {
				if(anSelectedData == "")
				{
			  		alertError3("Pilih data dahulu");
				 	return false;				
		  	  	}	
				deleteData("contracting_notifikasi_json/delete/", anSelectedId);
		  });     
} );

function reloadMonitoring()
{
	oTable.fnReloadAjax("contracting_notifikasi_json/json?reqId=<?= $reqId ?>");	
}

</script>

<div class="row"> 
	<div class="col-md-3 col-sm-3">
    <div class="jqueryui-ele-container">
      <?= $this->libkontrak->getMenu($reqId); ?>
    </div>
  </div>
  <div class="col-md-9 col-sm-9">
    <div class="card">
      <div class="card-content collapse show border-info border-darken-2">
        <div class="card-body">
          <h4 class="mb-2">Pengaturan Notifikasi Kontrak</h4>

          <div class="row mb-1"> 
          	<div class="card-body area-datatable">
		        	<div class="row" id="sticker">
				    	<div class="form-group col-md-12 mb-2">
		            <a id="btnAdd" title="Tambah" class="btn round btn-min-width box-shadow-1 btn-primary text-white"><span class="fa fa-plus"></span> Tambah</a>
		            <a id="btnEdit" title="Ubah" class="btn round btn-min-width box-shadow-1 btn-info text-white"><span class="fa fa-pencil"></span> Edit</a>
		            <a id="btnDelete" title="Hapus" class="btn round btn-min-width box-shadow-1 btn-danger text-white"><span class="fa fa-trash"></span> Hapus</a>
				    	</div> 
				    </div>
			    	 	<table id="example" class="border-double table mb-0 table-bordered" style="width: 100%">
			          <thead>
			            <tr>
				            <th>Id</th>
				            <th>paketId</th>
				            <th width="70%">Judul</th>
				            <th>Tanggal</th>
				            <th>Tanggal Akhir</th>
				            <th>Pembuat</th>
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
