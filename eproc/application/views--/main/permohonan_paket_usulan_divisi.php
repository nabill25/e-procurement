<?php
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 * 
 */
$this->libsession->cekSession();
$this->DEPARTMENT = isset($this->kauth->getInstance()->getIdentity()->DEPARTMENT) ? $this->kauth->getInstance()->getIdentity()->DEPARTMENT : '';

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
					   null,
					   null,
					   null,
					   null,
					   null,
					   null,
					   null,
					   null,
					   null,
					   null,
					   null,
					   // null,
					   null
				  ],
		"bSort":true,
		"bProcessing": true,
		"bServerSide": true,
		"sAjaxSource": "permohonan_paket_usulan_json/permohonan_usulan_monitoring_divisi_json",
		"lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
		columnDefs: [{ "targets": 4,"orderable": false }, { className: 'never', targets: [0,1,2,3,7] }]
		});//.rowGrouping({iGroupingColumnIndex: 0});
		oTable.fnSort( [ [0,'asc'] ] );
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
				  anSelectedPermohoanan = element[3];
		  });

		  $('#btnLihat').on('click', function () {
			  if(anSelectedData == "")
			  {
		  		alertError3("Pilih data dahulu");
			 	return false;
		  	  }
			  openAdd("main/loadUrl/main/permohonan_paket_usulan_lihat/?usulanId="+anSelectedId);

			  // tutup flex dropdown => untuk versi mobile
			  $('div.flexmenumobile').hide()
			  $('div.flexoverlay').css('display', 'none')
		  });
 

		$('#reqStatus').combobox({
			onSelect: function(param){
				oTable.fnReloadAjax("permohonan_paket_usulan_json/permohonan_usulan_monitoring_json/?reqStatus="+param.value);
			}
		});
} );
 

</script>

<div class="row">
  <div class="col-md-12 col-sm-12">
    <div class="card">
      <div class="card-header card-head-inverse bg-primary">
        <h4 class="card-title text-white">Usulan Kebutuhan <?= $this->DEPARTMENT ?>
        </h4>

        <a class="heading-elements-toggle"><i class="fa fa-ellipsis-v font-medium-3"></i></a>
        <div class="heading-elements">
            <ul class="list-inline mb-0">
              <li>
        				<a href="<?= base_url('/main/index/permohonan_paket_usulan') ?>" title="Lihat" class="btn btn-primary"> Lihat Usulan Kebutuhan</a>
              	<a data-action="expand"><i class="ft-maximize"></i></a></li>
              <!-- <li><a data-action="close"><i class="ft-x"></i></a></li> -->
            </ul>
        </div>
      </div>
      <div class="card-content collapse show border-info border-darken-2">
        <div class="card-body area-datatable">
        	<div class="row" id="sticker">
		    	<div class="form-group col-md-12 mb-2t">
					<label style="width: 100%"></label>
		            <a id="btnLihat" title="Lihat" class="<?= CLASS_BTN_SUCCESS ?>"> <i class="fa fa-eye"></i> Lihat</a>
			    </div>
	  		</div>
            <!-- <div class="table-responsive"> -->
              <!-- <table id="example" class="table mb-0"> -->
    		  <table id="example" class="border-double table mb-0 table-bordered" style="width: 100%">
	          	<thead>
	               <tr>
	               	<th width="1px">Id</th>
	                <th width="">Posting</th>
	                <th width="">Approval</th>
	                <th width="">Permohonan</th>
	                <th width="5%" style="text-align: center">Tahun <br>Anggaran</th>
	                <th width="75%">Nama Paket</th>
	                <th width="5%" style="text-align: center">Produk <br>Dalam Negeri</th>
	                <th width="5%">Jenis <br> Belanja</th>
	                <th width="5%" style="text-align: center">Harga <br> Perkiraan</th>
	                <th width="">Mulai Rencana <br>Pengadaan</th>
	                <th width="5%" style="text-align: center">Waktu <br> Penggunaan</th>
	                <th width="75%">Pembuat</th>
	                 <!-- <th width="">Jenis B/J</th> -->
	                </tr>
	            </thead>
              </table>
            <!-- </div>  -->
        </div>
      </div>
    </div>
  </div>
</div>
