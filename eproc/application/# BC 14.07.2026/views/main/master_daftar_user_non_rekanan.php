<?php 
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 * 
 */
 $this->libsession->cekSession(); ?>

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
		"aoColumns": [
						 {"bVisible": false},null,null,null,null,
						 null,null,null
				  ],
		"bSort":true,
		"bProcessing": true,
		"bServerSide": true,		
		"sAjaxSource": "users_base_json/master_daftar_rekanan_non_json",	
		"lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],	
		columnDefs: [{ className: 'never', targets: [ 0 ,6] },{ className: 'text-center', targets: [ 7 ] }]
		});
		oTable.fnSort( [ [0,'desc'] ] );
		new $.fn.dataTable.Responsive( oTable );
		  var anSelectedData = '';
		  var anSelectedId = '';
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
				  anSelectedNama = element[2];
		  });
		  
		  $('#btnAdd').on('click', function () {
			  openAdd("main/loadUrl/main/master_daftar_user_non_rekanan_add");
			  $('div.flexmenumobile').hide()
			  $('div.flexoverlay').css('display', 'none');
		  });

		  
		  $('#btnAddAkses').on('click', function () 
		  {
				if(anSelectedData == "") { alertError3("Pilih data dahulu"); return false; }		
		   	openAdd("main/loadUrl/main/master_daftar_user_non_rekanan_add_akses/?reqId="+anSelectedId);
			  $('div.flexmenumobile').hide()
			  $('div.flexoverlay').css('display', 'none');
		  });

		  $('#btnReset').on('click', function () 
		  {
				if(anSelectedData == "") { alertError3("Pilih data dahulu"); return false; }		
			  $.messager.prompt('Reset Password', 'Masukkan password baru :', function(r){
					if (r){
						$.get( "users_base_json/reset_password_daftar_user_non_rekanan/?reqPassword="+r+"&reqId="+ anSelectedId, function( data ) {
            	// alertSuccess2(data);
							$.messager.alert('Info', data, 'info');
						});
					}
				});
		  });
		  
		  $('#btnEdit').on('click', function () {
			  if(anSelectedData == "") { alertError3("Pilih data dahulu"); return false; }			
			  openAdd("main/loadUrl/main/master_daftar_user_non_rekanan_add/?reqId="+anSelectedId);
			  $('div.flexmenumobile').hide()
			  $('div.flexoverlay').css('display', 'none')
		  });
	 
		  $('#btnCetakPdf').on('click', function () {
			  newWindow = window.open("main/loadUrl/report/pelanggan_cetak_pdf/", 'Cetak');
			  newWindow.focus();
		  });
		  
		  $('#btnCetakExcel').on('click', function () {
			  newWindow = window.open("main/loadUrl/report/pelanggan_cetak_excel/", 'Cetak');
			  newWindow.focus();
		  });      
} );


function reloadMonitoring()
{
	oTable.fnReloadAjax("users_base_json/master_daftar_rekanan_non_json");	
}

</script>

<div class="row"> 
  <div class="col-md-12 col-sm-12">
    <div class="card">
      <div class="card-header card-head-inverse bg-primary">
        <h4 class="card-title text-white">User eProc</h4>
        <a class="heading-elements-toggle"><i class="fa fa-ellipsis-v font-medium-3"></i></a> 
        <div class="heading-elements">
            <ul class="list-inline mb-0">
              <li><a data-action="expand"><i class="ft-maximize"></i></a></li>
              <!-- <li><a data-action="close"><i class="ft-x"></i></a></li> -->
            </ul>
        </div>
      </div>
      <div class="card-content collapse show border-info border-darken-2">
        <div class="card-body area-datatable">
          <div class="row" id="sticker">
				    <div class="form-group col-md-12 mb-2">
		          <a class="<?= CLASS_BTN_PRIMARY ?>" id="btnAdd" title="Tambah"><span class="fa fa-user-plus"></span> Tambah User</a>
		          <a class="<?= CLASS_BTN_SUCCESS ?>" id="btnAddAkses" title="Tambah"><span class="fa fa-plus"></span> Akses User</a>
		          <a id="btnReset" class="<?= CLASS_BTN_DANGER ?>" title="Reset Password"><span class="fa fa-pencil"></span> Reset Password</a>
		          <a id="btnEdit" class="<?= CLASS_BTN_INFO ?>" title="Ubah"><span class="fa fa-pencil"></span> Edit</a>
				    </div> 
				  </div>
    			<table id="example" class="border-double table mb-0 table-bordered" style="width: 100%">
            <thead>
              <tr>
	              <th width="1px">Id</th>
	              <th width="15%">Tipe User</th>
	              <th width="15%">Nama</th>
	              <th width="10%">Username</th>
	              <th width="10%">Jabatan</th>    
	              <th width="10%">NUP/NIP</th>    
	              <th width="0%">Divisi</th>    
	              <th width="5%">Status Aktif</th>    
              </tr>       
            </thead>
        	</table>
        </div>
      </div>
    </div>
  </div> 
</div>   
