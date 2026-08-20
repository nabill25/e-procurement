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
					   null,null,null,null, null,
					   null,null
				  ],
		"bSort":true,
		"bProcessing": true,
		"bServerSide": true,
		"sAjaxSource": "rup_json/json/?reqStatus=",
		"lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
		columnDefs: [{ className: 'never', targets: [0] },{ className: 'text-center', targets: [1,2,5,6,7,8] }]
		});//.rowGrouping({iGroupingColumnIndex: 0});
		oTable.fnSort( [ [0,'desc'] ] );
		new $.fn.dataTable.Responsive( oTable );
		  var anSelectedData = '';
		  var anSelectedId = '';

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
				  anSelectedKodeRUP = element[2];
				  anSelectedKodePR = element[3];
				  anSelectedNilaiPaguRUP = element[5];
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
			  openAdd("main/loadUrl/main/rencana_umum_pengadaan_lihat/?reqId="+anSelectedId);

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

			$('#btnAdd').on('click', function ()
			{
				if(anSelectedData == "")
				{
		  		alertError3("Pilih data dahulu");
				 	return false;
		  	}

				if (anSelectedKodeRUP == "") {
		  		alertError3("Kode RUP masih kosong, data tidak dapat diproses");
				 	return false;
				}

				// if (anSelectedKodePR == "") {
		  // 		alertError3("Kode PR masih kosong, data tidak dapat diproses");
				//  	return false;
				// }
			 	// document.location.href = 'main/index/permohonan_paket_usulan_pengguna/?sirupId='+anSelectedId;
			 	document.location.href = 'api_ui/updatepr/'+anSelectedId;
	  	});


			$('#btnProses').on('click', function ()
			{
				if(anSelectedData == "")
				{
		  		alertError3("Pilih data dahulu");
				 	return false;
		  	} 
			 	document.location.href = 'main/index/permohonan_paket_usulan_add/?sirupId='+anSelectedId;
	  	});

		  $('#btnCetakPdf').on('click', function () {
			  newWindow = window.open("main/loadUrl/report/pelanggan_cetak_pdf/", 'Cetak');
			  newWindow.focus();
		  });

		   $('#btnCetak').on('click', function () {
				newWindow = window.open("main/loadUrl/report/permohonan_paket_unit_cetak/?reqMode=ppkom", 'Cetak');
				newWindow.focus();
		  });

	   	$('#reqKodeSA, #reqKodeDPSJ').combobox({
				onSelect: function(param)
				{
					var kodeDPSJ = $('#reqKodeDPSJ').combobox('getValue');
					oTable.fnReloadAjax("rup_json/json/?reqFilterSA="+param.id+"&reqFilterDPSJ="+kodeDPSJ);
				}
			});

} );

function reloadMonitoring()
{
	oTable.fnReloadAjax("permohonan_paket_json/permohonan_lelang_monitoring_json/?reqStatus=");
}
</script>

<div class="row">
  <div class="col-md-12 col-sm-12">
    <div class="card">
      <div class="card-header card-head-inverse bg-primary">
        <h4 class="card-title text-white">Rencana Umum Pengadaan </h4>
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
		    	<div class="form-group col-md-6 mb-2" id="sticker2">
            <a id="btnLihat" title="Lihat" class="<?= CLASS_BTN_SUCCESS ?>"> <i class="fa fa-eye"></i> Lihat RUP</a>
            <!-- <a id="btnCetak" title="Cetak" class="<?= CLASS_BTN_INFO ?>"> <i class="fa fa-print"></i> Cetak</a> -->
						<?php
						if ($this->USER_TYPE_ID == 9) { // Pengguna  ?>
							<a id="btnAdd" class="<?= CLASS_BTN_PRIMARY ?>"> <i class="fa fa-send"></i> Kirim Usulan PBJ</a>
						<?php
						} ?> 

						<?php
						if ($this->USER_TYPE_ID == 27) { // Perencanaan  ?>
							<a id="btnAdd" class="<?= CLASS_BTN_PRIMARY ?>"> <i class="fa fa-send"></i> Proses Usulan PBJ</a>
						<?php
						} ?> 
			    </div>
			    <div class="form-group col-md-6 text-right">
			    	<?php 
			    	if ($this->USER_TYPE_ID != 9) {
			    	 ?>
			    	<input type="text" name="reqKodeSA" id="reqKodeSA" class="easyui-combobox span3" id="reqIjinUsaha" data-options="valueField:'id',textField:'text',url:'rup_json/combosa'"  style="width: 250%" value="- Pilih Kode SA -" />
			    	<input type="text" name="reqKodeDPSJ" id="reqKodeDPSJ" class="easyui-combobox span3" id="reqIjinUsaha" data-options="valueField:'id',textField:'text',url:'rup_json/combodpsj'"  style="width: 250%" value="- Pilih Kode DPSJ -"/>
			    	<?php 
			    	} ?>
			    </div>
	  		</div>
            <!-- <div class="table-responsive"> -->
              <!-- <table id="example" class="table mb-0"> -->
    		  <table id="example" class="border-double table mb-0 table-responsive table-bordered" style="width: 100%">
	          	<thead>
	               <tr>
	               	<th>Id</th>
	                <th>Tahun</th>
	                <th>Kode RUP</th>
	                <th>Kode PR</th>
	                <th>Nama Paket</th>
                  <th>Nilai Pagu RUP</th>
                  <th>Nilai RAB</th>
	                <th>Waktu Awal</th>
	                <th>Waktu Akhir</th>
	                <th>Status Proses</th>
									<th>Pembuat</th>
                  <th>SA</th>
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
