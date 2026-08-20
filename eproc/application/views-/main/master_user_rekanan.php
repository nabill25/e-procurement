<?php 
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 * 
 */
 $this->libsession->cekSession(); ?>

<link rel="stylesheet" type="text/css" href="<?= base_url() ?>lib/DataTables-1.10.7/media/css/jquery.dataTables.css">
<link rel="stylesheet" type="text/css" href="<?= base_url() ?>lib/DataTables-1.10.7/extensions/Responsive/css/dataTables.responsive.css">
<link rel="stylesheet" type="text/css" href="<?= base_url() ?>lib/DataTables-1.10.7/examples/resources/syntax/shCore.css">
<link rel="stylesheet" type="text/css" href="<?= base_url() ?>lib/DataTables-1.10.7/examples/resources/demo.css">
<script type="text/javascript" language="javascript" src="<?= base_url() ?>lib/DataTables-1.10.7/media/js/jquery.dataTables.js"></script>
<script type="text/javascript" language="javascript" src="<?= base_url() ?>lib/DataTables-1.10.7/extensions/Responsive/js/dataTables.responsive.js"></script>
<script type="text/javascript" language="javascript" src="<?= base_url() ?>lib/DataTables-1.10.7/examples/resources/syntax/shCore.js"></script>
<script type="text/javascript" language="javascript" src="<?= base_url() ?>lib/DataTables-1.10.7/examples/resources/demo.js"></script>

<script type="text/javascript" language="javascript" class="init">
$(document).ready(function() {
	var oTable;
	oTable = $('#example').dataTable({ bJQueryUI: true,"iDisplayLength": 10,
		/* UNTUK MENGHIDE KOLOM ID */
		"aoColumns": [
									 {"bVisible": false},
									 null,
									 null,
									 null,
									 null
				  ],
		"bSort":true,
		"bProcessing": true,
		"bServerSide": true,
		"sAjaxSource": "users_base_json/master_daftar_rekanan_json",
		"lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
		columnDefs: [{ className: 'never', targets: [ 0 ] },{ className: 'text-center', targets: [ 3,4 ] }]
		});
		oTable.fnSort( [ [0,'desc'] ] );

		new $.fn.dataTable.Responsive( oTable );
		  /* RIGHT CLICK EVENT */
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
				  anSelectedNama = element[1];
		  });

		  $('#btnAdd').on('click', function () {
			  openAdd("main/loadUrl/main/master_daftar_user_non_rekanan_add");
			  $('div.flexmenumobile').hide()
			  $('div.flexoverlay').css('display', 'none')

		  });

		  $('#btnEdit').on('click', function () {
			  if(anSelectedData == "") {
			  	alert("Pilih data dahulu");
				 return false;
			  }
			  openAdd("main/loadUrl/app/anak_perusahaan_add/?reqId="+anSelectedId);
			  $('div.flexmenumobile').hide()
			  $('div.flexoverlay').css('display', 'none')
		  });

		  $('#btnDelete').on('click', function () {
				if(anSelectedData == "")
					  return false;
				deleteData("perusahaan_json/delete/", anSelectedId);
		  });

		  $('#btnCetakPdf').on('click', function () {
			  newWindow = window.open("main/loadUrl/report/pelanggan_cetak_pdf/", 'Cetak');
			  newWindow.focus();
		  });

		  $('#btnCetakExcel').on('click', function () {
			  newWindow = window.open("main/loadUrl/report/pelanggan_cetak_excel/", 'Cetak');
			  newWindow.focus();
		  });

		  $('#btnReset').on('click', function () {
			if(anSelectedData == "")
			{
		  		alertError3("Pilih data dahulu");
			 	return false;
		  	}
				   $.messager.prompt('Reset Password', 'Masukkan password baru :', function(r){
					if (r){
						$.get( "users_base_json/reset_password_daftar_user_non_rekanan/?reqPassword="+r+"&reqId="+ anSelectedId, function( data ) {
						   $.messager.alert('Info', data, 'info');
						});
					}
				});
		});

		$('#btnUbahStatus').on('click', function ()
		 {
			if(anSelectedData == "")
			{
		  		alertError3("Pilih data dahulu");
			 	return false;
		  	}
			$.messager.confirm('Konfirmasi',"Apakah anda ingin mengubah status validasi "+anSelectedNama+" ?",function(r){
				if (r){
					$.getJSON("users_base_json/ubah_status/?reqId="+anSelectedId,
					  function(data){

							  $.messager.alert('Info', data.PESAN, 'info');
							  oTable.fnReloadAjax("users_base_json/master_daftar_rekanan_json");
					});
				}
			});
		});

		$('#btnUbahStatusAktif').on('click', function ()
		 {
			if(anSelectedData == "")
			{
		  		alertError3("Pilih data dahulu");
			 	return false;
		  	}
			$.messager.confirm('Konfirmasi',"Apakah anda ingin mengubah status aktif "+anSelectedNama+" ?",function(r){
				if (r){
					$.getJSON("users_base_json/ubah_status_aktif/?reqId="+anSelectedId,
					  function(data){

							  $.messager.alert('Info', data.PESAN, 'info');
							  oTable.fnReloadAjax("users_base_json/master_daftar_rekanan_json");
					});
				}
			});
		});
} );

function reloadMonitoring()
{
	oTable.fnReloadAjax("users_base_json/master_daftar_rekanan_json");
}

</script>

<div class="row">
  <div class="col-md-12 col-sm-12">
    <div class="card">
      <div class="card-header card-head-inverse bg-primary">
        <h4 class="card-title text-white">User Penyedia</h4>
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
                	<a id="btnReset" class="<?= CLASS_BTN_DANGER ?>" title="Reset Password"><span class="fa fa-pencil"></span> Reset Password</a>
	                <a id="btnUbahStatus" class="btn round btn-min-width box-shadow-1 btn-primary text-white" title="Ubah Status Validasi"><span class="fa fa-pencil"></span> Ubah Status Validasi</a>
	                <a id="btnUbahStatusAktif" class="btn round btn-min-width box-shadow-1 btn-warning text-white" title="Ubah Status Aktif"><span class="fa fa-pencil"></span> Ubah Status Aktif</a>
			    </div>
			</div>
    			<table id="example" class="border-double table mb-0 table-bordered" style="width: 100%">

            <thead>
                  <tr>
                      <th width="1px">Id</th>
                      <th width="100px">Nama</th>
                      <th width="80px">Username</th>
                      <th style="width: 5%;">Status Validasi</th>
                      <th style="width: 5%;">Status Aktif</th>
                  </tr>
              </thead>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>
