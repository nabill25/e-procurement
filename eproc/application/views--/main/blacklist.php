<?php
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 *
 */
?>
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
		columnDefs: [{ className: 'never', targets: [ 0, 2, 5 ] }]
		});//.rowGrouping({iGroupingColumnIndex: 0});

		new $.fn.dataTable.Responsive( oTable );

		/* Click event handler */

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
		  });

		  $('#btnAdd').on('click', function () {
			  openAdd("main/loadUrl/main/master_blacklist_add");

			  // tutup flex dropdown => untuk versi mobile
			  $('div.flexmenumobile').hide()
			  $('div.flexoverlay').css('display', 'none')

		  });

		  $('#btnEdit').on('click', function () {
			  if(anSelectedData == "")
				  return false;
			  openAdd("main/loadUrl/main/anak_perusahaan_add/?reqId="+anSelectedId);

			  // tutup flex dropdown => untuk versi mobile
			  $('div.flexmenumobile').hide()
			  $('div.flexoverlay').css('display', 'none')
		  });

		  $('#btnDelete').on('click', function () {
				if(anSelectedData == "")
					  return false;
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

<style type="text/css">
	.ui-widget-header {border: 1px solid transparent !important }
</style>

<section id="grid-options" class="row">

  <div class="col-md-12">
    <div class="card">
      <div class="card-header">
        <h4 class="card-title">Daftar <small class="text-muted">Hitam</small></h4>
        <a class="heading-elements-toggle"><i class="fa fa-ellipsis-v font-medium-3"></i></a>
        <div class="heading-elements">
          <ul class="list-inline mb-0">
              <li><a data-action="expand"><i class="ft-maximize"></i></a></li>
          </ul>
        </div>
      </div>
      <div class="card-content">
        <div class="card-body">
          <div class="card-text">
		     <div class="card-body area-datatable">
		        <table id="example" class="display table-bordered" cellspacing="0" width="100%" style="border-bottom: none !important">
			        <thead>
		                <tr>
		                <th width="">Id</th>
		                <th width="15%">Nama</th>
		                <th width="20%">Alamat</th>
		                <th width="10%">NPWP</th>
		                <th width="10%">Tanggal</th>
		                <th width="15%">Alasan</th>
		                <th width="20%">SK</th>
		                </tr>
		            </thead>
		        </table>
		     </div>
          </div>
        </div>
      </div>
    </div>
  </div>

</section>

<script>
/** FIXED AREA-MENU-AKSI WHEN SCROLLING UP **/
$(document).ready(function() {
    var s = $("#sticker");
	var f = $("#example_filter");
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
