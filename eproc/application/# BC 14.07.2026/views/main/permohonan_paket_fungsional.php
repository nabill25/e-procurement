<?php
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 * 
 */
$this->libsession->cekSession();
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
					   null,null,null,null,null,
					   null,null,null,null,null,
					   null
				  ],
		"bSort":true,
		"bProcessing": true,
		"bServerSide": true,
		"sAjaxSource": "permohonan_paket_json/permohonan_lelang_monitoring_json/?reqStatus=",
		"lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
		columnDefs: [{ className: 'never', targets: [0,1,2,3] },{ className: 'text-center', targets: [5,10] }]
		});//.rowGrouping({iGroupingColumnIndex: 0});
		oTable.fnSort( [ [0,'desc'] ] );
		new $.fn.dataTable.Responsive( oTable );
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
		  });

		  $('#btnRJ').on('click', function () {
			if(anSelectedData == "")
			{
		  		alertError3("Pilih data dahulu");
			 	return false;
		  	}
		  	openAddLg("main/loadUrl/main/rekam_jejak_view?id="+anSelectedId);
		  });

		  $('#btnPR').on('click', function () { 
			  openAdd("main/loadUrl/main/permohonan_paket_fungsional_update_pr");

			  // tutup flex dropdown => untuk versi mobile
			  $('div.flexmenumobile').hide()
			  $('div.flexoverlay').css('display', 'none')
		  });

		  $('#btnPRUpdate').on('click', function () {
			  if(anSelectedData == "")
			  {
		  		alertError3("Pilih data dahulu");
			 		return false;
	  	  } 

	  		openAdd("main/loadUrl/main/permohonan_paket_fungsional_update_pr_one/?reqId="+anSelectedId);

			  // tutup flex dropdown => untuk versi mobile
			  $('div.flexmenumobile').hide()
			  $('div.flexoverlay').css('display', 'none')
		  });

		   $('#btnLihat').on('click', function () {
			  if(anSelectedData == "")
			  {
		  		alertError3("Pilih data dahulu");
			 	return false;
		  	  }
			  openAdd("main/loadUrl/main/permohonan_lelang_panitia_add/?reqId="+anSelectedId);

			  // tutup flex dropdown => untuk versi mobile
			  $('div.flexmenumobile').hide()
			  $('div.flexoverlay').css('display', 'none')
		  });

		  $('#btnPosting').on('click', function (){
		  		if(anSelectedData == "")
				{
			  		alertError3("Pilih data dahulu");
				 	return false;
			  	}
				if(anSelectedPosting == "1")
				{
			  	alertError3("Data sudah diteruskan.");
					return;
				}

				$.messager.confirm('Konfirmasi',"Apakah anda ingin meneruskan permohonan paket?",function(r){
					if (r){
						$.get("permohonan_paket_json/posting_permohonan/?reqId="+anSelectedId,
						  function(data){
							  		//alert(data);return false;
								  $.messager.alert('Info', data, 'info');
								  oTable.fnReloadAjax("permohonan_paket_json/permohonan_lelang_monitoring_json/?reqStatus=0");
						});
					}
				});
			});

			$('#btnEdit').on('click', function ()
			{
				if(anSelectedData == "")
				{
			  		alertError3("Pilih data dahulu");
				 	return false;
			  	}
				// if(anSelectedPosting == 1)
				// {
				// 	alertError3("Paket sudah di teruskan");
				// 	return;
				// }
			 	document.location.href = 'main/index/permohonan_paket_fungsional_add/?reqId='+anSelectedId;
		  	});

		  $('#btnCetakPdf').on('click', function () {
			  newWindow = window.open("main/loadUrl/report/pelanggan_cetak_pdf/", 'Cetak');
			  newWindow.focus();
		  });

		   $('#btnCetak').on('click', function () {
				newWindow = window.open("main/loadUrl/report/permohonan_paket_unit_cetak/?reqMode=ppkom", 'Cetak');
				newWindow.focus();
		  });

		$('#reqStatus').combobox({
			onSelect: function(param){
				oTable.fnReloadAjax("permohonan_paket_json/permohonan_lelang_monitoring_json/?reqStatus="+param.value);
			}
		});
} );

function reloadMonitoring()
{
	oTable.fnReloadAjax("permohonan_paket_json/permohonan_lelang_monitoring_json/?reqStatus=");
} 


function make(a) {
	openAdd("main/loadUrl/main/permohonan_paket_usulan_lihat/?usulanId="+a);
}
</script>

<div class="row">
  <div class="col-md-12 col-sm-12">
    <div class="card">
      <div class="card-header card-head-inverse bg-primary">
        <h4 class="card-title text-white">Rencana Pengadaan </h4>
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
		    	<div class="form-group col-md-8 mb-2" id="sticker2">
						<label style="width: 100%"></label>
            <a id="btnLihat" title="Lihat" class="<?= CLASS_BTN_SUCCESS ?>"> <i class="fa fa-eye"></i> Lihat Rencana Pengadaan</a>
            <?php 
      			if($this->USER_TYPE_ID == 9) // PENGGUNA
          	{ ?>
            <a id="btnEdit" title="Upload BoQ" class="<?= CLASS_BTN_INFO ?>"> <i class="fa fa-file-excel-o"></i> Upload BoQ</a>
            <?php 
            } ?>
            <!-- <a id="btnPosting" title="Teruskan" class="btn round btn-min-width box-shadow-1 btn-primary text-white"> <i class="fa fa-arrow-right"></i> Teruskan</a> -->
            <a id="btnCetak" title="Cetak" class="<?= CLASS_BTN_INFO ?>"> <i class="fa fa-print"></i> Cetak</a>
      			<a id="btnRJ" title="Rekam Jejak" class="<?= CLASS_BTN_DARK ?>"><i class="fa fa-paw"></i> Rekam Jejak</a>
      			<?php 
      			if($this->USER_TYPE_ID == 7) // KEPALA PENGADAAN
          	{ ?>
      			<a id="btnPR" title="Cek PR" class="<?= CLASS_BTN_WARNING ?>"><i class="fa fa-cogs"></i> Cek PR</a>
      			<?php 
      			} ?>
      			<a id="btnPRUpdate" title="Update PR" class="<?= CLASS_BTN_DANGER ?>"><i class="fa fa-pencil"></i> Update PR</a>
			    </div>
		    	<div class="form-group col-md-4 mb-2 text-right"> 
			    </div>
	  		</div>
            <!-- <div class="table-responsive"> -->
              <!-- <table id="example" class="table mb-0"> -->
    		  <table id="example" class="border-double table mb-0 table-bordered" style="width: 100%">
	          	<thead>
	               <tr>
	               	<th width="">Id</th>
	                <th width="">Posting</th>
	                <th width="">Status</th>
	                <th width="">Publish</th>
	                 <th width="10%">Kode RUP</th>
	                <th width="">Tahun <br>Anggaran</th>
	                <!-- <th width="5%">No. Nota</th> -->
	                <!-- <th width="5%">Tanggal Nota</th> -->
	                <th width="50%">Nama Paket</th>
	                <th width="13%">Harga Perkiraan</th>
	                 <th width="15%">PIC</th>
	                 <th width="15%">No. PR</th>
	                <th width="5%">BoQ</th>  
	                </tr>
	            </thead>
              </table>
            <!-- </div>  -->
        </div>
      </div>
    </div>
  </div>
</div>

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
