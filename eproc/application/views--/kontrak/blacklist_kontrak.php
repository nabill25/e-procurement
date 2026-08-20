<?php
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 *
 */
$this->libsession->cekSession();

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
						 {"bVisible": false},null,null,null,null,null,null,null,null
				  ],
		"bSort":true,
		"bProcessing": true,
		"bServerSide": true,
		"sAjaxSource": "contracting_json/jsonBlacklistkontrak",
		"lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
		columnDefs: [{ className: 'never', targets: [0,1,2] }]
		// columnDefs: [{ className: 'never', targets: [] }]
		});//.rowGrouping({iGroupingColumnIndex: 0});

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
		  }); 
} );

function reloadMonitoring()
{
	oTable.fnReloadAjax("contracting_json/jsonBlacklistkontrak");
}
</script>

<style type="text/css">
	.ui-widget-header {border: 1px solid transparent !important }
</style>

<section id="grid-options" class="row">

  <div class="col-md-12">
    <div class="card">
      <div class="card-header">
        <h4 class="card-title">Daftar <small class="text-muted">Hitam Kontrak</small></h4>
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
		                <th>Id</th>
		                <th>RekId</th>
		                <th>ContractingRekId</th>
		                <th width="10%">No SK</th>
		                <th width="20%">Penyedia</th>
		                <th width="20%">Judul</th>
		                <th width="10%">Tanggal Masa Berlaku</th>
		                <th width="35%">Keterangan</th>
		                <th width="5%">File</th>
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
