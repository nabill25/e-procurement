<?php
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 * 
 */
$this->libsession->cekSession();

// if($this->USER_TYPE_ID == "")
//     redirect("app");
?>
<link rel="stylesheet" type="text/css" href="lib/DataTables-1.10.7/media/css/jquery.dataTables.css">
<link rel="stylesheet" type="text/css" href="lib/DataTables-1.10.7/extensions/Responsive/css/dataTables.responsive.css">
<link rel="stylesheet" type="text/css" href="lib/DataTables-1.10.7/examples/resources/syntax/shCore.css">
<link rel="stylesheet" type="text/css" href="lib/DataTables-1.10.7/examples/resources/demo.css">
<!--<script type="text/javascript" language="javascript" src="lib/DataTables-1.10.7/media/js/jquery.js"></script>-->
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
					   {"bVisible": false},null,null,null,null,null,null,null,null, 
				  ],
		"bSort":true,
		"bProcessing": true,
		"bServerSide": true,
		"sAjaxSource": "permohonan_paket_usulan_json/permohonan_usulan_monitoring_admin_rup_json",
		"lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
		columnDefs: [{ className: 'never', targets: [0,1,2] }]
		});//.rowGrouping({iGroupingColumnIndex: 0});

		new $.fn.dataTable.Responsive( oTable );

		/* Click event handler */

		  /* RIGHT CLICK EVENT */
		  var anSelectedData = '';
		  var anSelectedId = '';
		  var anSelectedIdDelete = '';
		  var anSelectedDownload = '';
		  var anSelectedPosition = '';
		  var anSelectedPosting = '';

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
				  anSelectedPosting = element[1];
				  anSelectedApproval = element[2];
		  }); 

		   $('#btnCetak').on('click', function () {
				newWindow = window.open("main/loadUrl/report/permohonan_paket_usulan_admin_cetak_rup", 'Cetak');
				newWindow.focus();
		  });

		$('#reqStatus').combobox({
			onSelect: function(param){
				oTable.fnReloadAjax("permohonan_paket_usulan_json/permohonan_usulan_monitoring_admin_json/?reqStatus="+param.value);
			}
		});
} );

function reloadMonitoring()
{
	oTable.fnReloadAjax("permohonan_paket_usulan_json/permohonan_usulan_monitoring_admin_json");
}

</script>

<div class="row">
  <div class="col-md-12 col-sm-12">
    <div class="card">
      <div class="card-header card-head-inverse bg-primary">
        <h4 class="card-title text-white">Rencana Pengadaan</h4>
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
        	<div class="row"> 
		    	<div class="form-group col-md-6 mb-2t">
					<label style="width: 100%"></label>
		            <a id="btnCetak" title="Cetak" class="btn btn-info text-white"> <i class="fa fa-print"></i> Cetak</a>
			    </div>
	  		</div>
    		  <table id="example" class="border-double table mb-0 table-bordered" style="width: 100%">
	          	<thead>
	               <tr>
	               	 <th width="1px">Id</th>
	                <th width="">Posting</th>
	                <th width="">Approval</th>
	                <th width="" style="text-align: center">Tahun <br>Anggaran</th>
	                <th width="50%">Nama Kebutuhan</th>
	                <th width="5%" style="text-align: center">Perkiraan Biaya</th>
	                <th width="5%" style="text-align: center">Waktu <br> Penggunaan</th>
	                <th width="">Rencana <br>Pengadaan</th>
	                 <th width="">Cara Pengadaan</th>
	                 <!-- <th width="">User</th> -->
	                </tr>
	            </thead>
              </table>
        </div>
      </div>
    </div>
  </div>
</div>
