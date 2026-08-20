<?php
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 *
 */
  $this->libsession->cekSession();   ?>
<link rel="stylesheet" type="text/css" href="<?=base_url() ?>lib/DataTables-1.10.7/media/css/jquery.dataTables.css">
<link rel="stylesheet" type="text/css" href="<?=base_url() ?>lib/DataTables-1.10.7/extensions/Responsive/css/dataTables.responsive.css">
<link rel="stylesheet" type="text/css" href="<?=base_url() ?>lib/DataTables-1.10.7/examples/resources/syntax/shCore.css">
<link rel="stylesheet" type="text/css" href="<?=base_url() ?>lib/DataTables-1.10.7/examples/resources/demo.css">
<script type="text/javascript" language="javascript" src="<?=base_url() ?>lib/DataTables-1.10.7/media/js/jquery.dataTables.js"></script>
<script type="text/javascript" language="javascript" src="<?=base_url() ?>lib/DataTables-1.10.7/extensions/Responsive/js/dataTables.responsive.js"></script>
<script type="text/javascript" language="javascript" src="<?=base_url() ?>lib/DataTables-1.10.7/examples/resources/syntax/shCore.js"></script>
<script type="text/javascript" language="javascript" src="<?=base_url() ?>lib/DataTables-1.10.7/examples/resources/demo.js"></script>
<script type="text/javascript" language="javascript" class="init">
var oTable;
$(document).ready(function() {

	oTable = $('#example').dataTable({ bJQueryUI: true,"iDisplayLength": 10,
		/* UNTUK MENGHIDE KOLOM ID */
		"aoColumns": [
						 {"bVisible": false},
						 null,
						 null,
						 null,
						 null,
						 null,
						 null
				  ],
		"bSort":true,
		"bProcessing": true,
		"bServerSide": true,
		"sAjaxSource": "blacklist_json/json",
		"lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
		columnDefs: [{ className: 'never', targets: [ 0, 5 ] }]
		});//.rowGrouping({iGroupingColumnIndex: 0});
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
		  });

		  $('#btnAdd').on('click', function () {
			  openAdd("main/loadUrl/main/master_blacklist_add");

			  // tutup flex dropdown => untuk versi mobile
			  $('div.flexmenumobile').hide()
			  $('div.flexoverlay').css('display', 'none')

		  });

		  $('#btnLihatDetil').on('click', function () {
			  if(anSelectedData == "") {
			  	  alertError3('Pilih data dahulu');
				  return false;
			  }
			  openAdd("main/loadUrl/main/blacklist_view/?reqId="+anSelectedId);
			  $('div.flexmenumobile').hide()
			  $('div.flexoverlay').css('display', 'none')
		  });

			$('#btnUpload').on('click', function () {
			  if(anSelectedData == "") {
			  	  alertError3('Pilih data dahulu');
				  return false;
			  }
			  openAdd("main/loadUrl/main/blacklist_file_add/?reqId="+anSelectedId);
			  $('div.flexmenumobile').hide()
			  $('div.flexoverlay').css('display', 'none')
		  });

		  $('#btnEdit').on('click', function () {
			  if(anSelectedData == "")
				  return false;
			  openAdd("main/loadUrl/app/anak_perusahaan_add/?reqId="+anSelectedId);
			  $('div.flexmenumobile').hide();
			  $('div.flexoverlay').css('display', 'none')
		  });

		  $('#btnDelete').on('click', function () {
				if(anSelectedData == "")
				{
			  		alertError3("Pilih data dahulu");
				 	return false;
			  	}
				deleteData("blacklist_json/delete/", anSelectedId);
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
				  return false;
			$.messager.confirm('Konfirmasi',"Reset password data terpilih?",function(r){
				if (r){
					$.getJSON("perusahaan_json/reset_password_anak_perusahaan/?reqId="+anSelectedId,
					  function(data){
							  $.messager.alert('Info', data.PESAN, 'info');
							  oTable.fnReloadAjax("perusahaan_json/json_anak_perusahaan");
					});
				}
			});

		});
} );

function reloadMonitoring()
{
	oTable.fnReloadAjax("blacklist_json/json");
}

</script>

<div class="row">
  <div class="col-md-12 col-sm-12">
    <div class="card">
      <div class="card-header card-head-inverse bg-primary">
        <h4 class="card-title text-white">Black List</h4>
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
							<a id="btnLihatDetil" class="<?= CLASS_BTN_INFO ?> mr-1" title="Lihat" style="margin-bottom: 15px"><span class="fa fa-eye"></span> Lihat</a>
		          <!-- <a id="btnUpload" class="<?= CLASS_BTN_SUCCESS ?>" title="Upload" style="margin-bottom: 15px"><span class="fa fa-upload"></span> Upload Dokumen</a> -->
				     	<a id="btnAdd" class="<?= CLASS_BTN_PRIMARY ?> mr-1" title="Tambah" style="margin-bottom: 15px"><span class="fa fa-plus"></span> Tambah</a>
					   	<a id="btnDelete" class="<?= CLASS_BTN_DANGER ?>" title="Hapus" style="margin-bottom: 15px"><span class="fa fa-trash"></span> Hapus</a>
				    </div>
				  </div>
    		  <table id="example" class="border-double table mb-0 table-bordered" style="width: 100%">
		        <thead>
              <tr>
                <th width="1px">Id</th>
                <th width="15%">Nama</th>
                <th width="20%">Alamat</th>
                <th width="10%">NPWP</th>
                <th width="15%">Tanggal</th>
                <th width="15%">Alasan</th>
                <th width="10%">SK</th>
              </tr>
            </thead>
          </table>
	  		</div>
      </div>
    </div>
  </div>
</div>
