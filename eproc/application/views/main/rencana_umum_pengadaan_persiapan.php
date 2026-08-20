<?php
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 *
 */
$this->libsession->cekSession();
$reqStatus = isset($_GET['reqStatus']) ? $_GET['reqStatus'] : '';

if ($reqStatus) { }
else {
	switch ($this->USER_TYPE_ID) {
		case '9': // Pengguna
			$reqStatus = '22';
			break;
		case '27': // Perencana
			if ($this->LEVEL_PERENCANA != '3') { // Staff dan Kasi
				$reqStatus = '33';
			}
			if ($this->LEVEL_PERENCANA == '3') { // Kasubdit
				$reqStatus = '5';
			}
			break;
		case '28': // PPK
			$reqStatus = '6';
			break;
		
		default:
			$reqStatus = '';
			break;
	}		
} 

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
		"aoColumns": [
					   null,null,null,null,null,
					   null,null,null,null, null,
						 null, null, null, null, null, null
				  ],
		"bSort":true,
		"bProcessing": true,
		"bServerSide": true,
		"sAjaxSource": "rup_json/jsonPersiapan/?reqStatus=<?= $reqStatus ?>",
		"lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
		columnDefs: [{ className: 'never', targets: [0,1,2,12] },{ className: 'text-center', targets: [2,3,4,7,8,9,12,13,14] }]
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
				  anSelectedId = element[0]; // permohonan_paket_analisa_id
					anSelectedSiRUP = element[1]; // kode_sirup
					anSelectedPermohonanId = element[2]; // permohonan_paket_id
				  anSelectedStatus = element[12]; // status analis
		  });

		  $('#btnRJ').on('click', function () {
			if(anSelectedData == "")
			{
		  		alertError3("Pilih data dahulu");
			 	return false;
		  	}
		  	openAddLg("main/loadUrl/main/rekam_jejak_view?id="+anSelectedPermohonanId);
		  });

		  $('#btnPR').on('click', function () {
			  openAdd("main/loadUrl/main/permohonan_paket_fungsional_update_pr");

			  // tutup flex dropdown => untuk versi mobile
			  $('div.flexmenumobile').hide()
			  $('div.flexoverlay').css('display', 'none');
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
			  openAdd("main/loadUrl/main/rencana_umum_pengadaan_persiapan_lihat/?reqId="+anSelectedId+"&sirupId="+anSelectedSiRUP);
			  $('div.flexmenumobile').hide()
			  $('div.flexoverlay').css('display', 'none')
		  });
 
		  $('#btnTeruskan').on('click', function ()
			{
				if(anSelectedData == "") { alertError3("Pilih data dahulu"); return false; }
				if(anSelectedStatus == "6") { alertError3("Tahap Persetujuan PPK."); return; }
				if(anSelectedStatus == "5") 
				{ 
			 		document.location.href = "main/index/permohonan_paket_pic_add/?reqId="+anSelectedId;
				} else {
					alertError3("Data atau Dokumen belum lengkap."); return;
				}
	  	});

		  $('#btnApprove').on('click', function (){	
	  		
			if(anSelectedData == "") { alertError3("Pilih data dahulu"); return false; }
				if(anSelectedStatus == "1") { alertError3("Data sudah setujui."); return; }

				$.messager.confirm('Konfirmasi',"Cek dahulu permohonan ini!",function(r){
					if (r){
		  			openAdd("main/loadUrl/main/permohonan_paket_pic_approve/?reqId="+anSelectedId+"&sirupId="+anSelectedSiRUP+"&permohonanId="+anSelectedPermohonanId);
					}
				});
			});
			
			// $('#btnApprove').on('click', function (){		  		
			// 	if(anSelectedData == "") { alertError3("Pilih data dahulu"); return false; }				
			// 	if(anSelectedStatus == "1") { alertError3("Data sudah setujui."); return; }				
				
			// 	$.messager.confirm('Konfirmasi', "Cek dahulu permohonan ini!", function(r){					
			// 		if (r){		  			
			// 			// 1. Panggil API pengecekan e-sign terlebih dahulu
			// 			$.ajax({
			// 				url: 'https://eproc.ui.ac.id/api_ui/updatefileesign',
			// 				type: 'GET', // Sesuaikan jadi 'POST' jika API membutuhkan method POST
			// 				dataType: 'json',
			// 				success: function(response) {
			// 					// 2. Cek apakah output json memiliki status == "oke"
			// 					if (response && response.status === "oke") {
			// 						// 3. Jika oke, baru lanjut ke openAdd
			// 						openAdd("main/loadUrl/main/permohonan_paket_pic_approve/?reqId="+anSelectedId+"&sirupId="+anSelectedSiRUP+"&permohonanId="+anSelectedPermohonanId);
			// 					} else {
			// 						// Handle jika status bukan "oke" (opsional)
			// 						alertError3("Gagal verifikasi e-sign: " + (response.message || "Status tidak valid."));
			// 					}
			// 				},
			// 				error: function(xhr, status, error) {
			// 					// Handle jika API error / tidak bisa diakses
			// 					alertError3("Gagal menghubungi server e-sign: " + error);
			// 				}
			// 			});
			// 		}				
			// 	});			
			// });

			$('#btnKirimUlang').on('click', function (){
	  		if(anSelectedData == "") { alertError3("Pilih data dahulu"); return false; }
				if(anSelectedStatus == "3") { alertError3("Tahap Pengecekan Perencanaan."); return; }
				if(anSelectedStatus == "4") { alertError3("Revisi Pengecekan Perencanaan."); return; }
				if(anSelectedStatus == "5") { alertError3("Tahap Pengecekan Kasubdit Perencanaan."); return; }
				if(anSelectedStatus == "6") { alertError3("Tahap Pengecekan PPK."); return; }
				if(anSelectedStatus == "1") { alertError3("Data sudah setujui."); return; }

				$.messager.confirm('Konfirmasi',"Kirim ulang permohonan ini?",function(r){
					if (r){
						$.get("permohonan_paket_json/resend_permohonan/?reqId="+anSelectedId+"&sirupId="+anSelectedSiRUP+"&permohonanId="+anSelectedPermohonanId,
						  function(data){
								  $.messager.alert('Info', data, 'info');
								  oTable.fnReloadAjax("rup_json/jsonPersiapan?reqStatus=3");
						});
					}
				});
			});

			$('#btnTolak').on('click', function () {
			  if(anSelectedData == "") { alertError3("Pilih data dahulu"); return false; }
	  	  if(anSelectedStatus == "3") { alertError3("Tahap Pengecekan Perencanaan."); return; }
				if(anSelectedStatus == "4") { alertError3("Revisi Pengecekan Perencanaan."); return; }
				if(anSelectedStatus == "6") { alertError3("Tahap Pengecekan PPK."); return; }
				if(anSelectedStatus == "1") { alertError3("Data sudah setujui."); return; }

			  openAdd("main/loadUrl/main/permohonan_paket_usulan_kembalikan/?reqId="+anSelectedId+'&reqPerId='+anSelectedPermohonanId);
			  $('div.flexmenumobile').hide()
			  $('div.flexoverlay').css('display', 'none')
		  });

			$('#btnReviu').on('click', function ()
			{
				if(anSelectedData == "") { alertError3("Pilih data dahulu"); return false; } 
				if(anSelectedStatus == "1") { alertError3("Data sudah setujui."); return; }
				if(anSelectedStatus == "6") { alertError3("Tahap Pengecekan PPK."); return; }
			 	document.location.href = 'main/index/permohonan_paket_usulan_add/?reqId='+anSelectedId+'&reqPerId='+anSelectedPermohonanId;
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
					oTable.fnReloadAjax("rup_json/jsonPersiapan/?reqStatus="+param.value);
				}
			});   

} );

function reloadMonitoring()
{
	oTable.fnReloadAjax("rup_json/jsonPersiapan/?reqStatus=");
}

function reloadMonitoring5()
{
	oTable.fnReloadAjax("rup_json/jsonPersiapan/?reqStatus=5");
}

function reloadMonitoring6()
{
	oTable.fnReloadAjax("rup_json/jsonPersiapan/?reqStatus=6");
}
</script>

<div class="row">
  <div class="col-md-12 col-sm-12">
    <div class="card">
      <div class="card-header card-head-inverse bg-primary">
        <h4 class="card-title text-white">Persiapan </h4>
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
            <a id="btnLihat" title="Lihat" class="<?= CLASS_BTN_SUCCESS ?>"> <i class="fa fa-eye"></i> Lihat Detail Data </a>
            <?php 
						if ($this->USER_TYPE_ID != 10 && $this->USER_TYPE_ID != 25) { // Audit; Direksi  ?>
            <a id="btnCetak" title="Cetak" class="<?= CLASS_BTN_INFO ?>"> <i class="fa fa-print"></i> Cetak</a>
            <?php 
            } ?>
            <a id="btnRJ" title="Rekam Jejak" class="<?= CLASS_BTN_DARK ?>"><i class="fa fa-paw"></i> Rekam Jejak</a>
						<?php 
						if ($this->USER_TYPE_ID == 7) { // Manager Pengadaan  ?>
							<!-- <a id="btnTeruskan" class="<?= CLASS_BTN_PRIMARY ?>"> <i class="fa fa-check-square"></i> Teruskan</a> -->
						<?php
						} if ($this->USER_TYPE_ID == 9) { // Pengguna  ?>
							<a id="btnKirimUlang" class="<?= CLASS_BTN_PRIMARY ?>"> <i class="fa fa-check-square"></i> Kirim Ulang</a>
						<?php
						} ?>

						<?php
						if ($this->USER_TYPE_ID == 27 && $this->LEVEL_PERENCANA != '3') { // Perencana  ?>
							<a id="btnReviu" class="<?= CLASS_BTN_PRIMARY ?>"> <i class="fa fa-check-square"></i> Reviu Usulan</a>
						<?php
						} ?>

						<?php
						if ($this->USER_TYPE_ID == 27 && $this->LEVEL_PERENCANA == 3) { // Perencana Kasubdit  ?>
							<a id="btnTolak" class="<?= CLASS_BTN_DANGER ?>"> <i class="fa fa-arrow-left"></i> Kembalikan</a>
							<a id="btnTeruskan" class="<?= CLASS_BTN_WARNING ?>"> <i class="fa fa-check-square"></i> Pilih Metode Pengadaan</a>
						<?php
						} ?>

						<?php
						if ($this->USER_TYPE_ID == 28) { // PPK  ?>
							<a id="btnApprove" class="<?= CLASS_BTN_DANGER ?>"> <i class="fa fa-check-square"></i> Approve</a>
						<?php
						} ?>

			    </div>
		    	<div class="form-group col-md-4 mb-2 text-right">
				    <select name="reqStatus" id="reqStatus" class="form-control easyui-combobox span3" style="width: 300%">
	            <option value="">Semua</option>
	            <option value="1" <?php if ($reqStatus == '1') { echo 'selected=""';} ?>>Disetujui PPK</option>
	            <option value="2" <?php if ($reqStatus == '2') { echo 'selected=""';} ?>>Revisi oleh Unit Kerja</option>
	            <option value="3" <?php if ($reqStatus == '3' || $reqStatus == '33' || $reqStatus == '22') { echo 'selected=""';} ?>>Pengecekan Perencanaan</option>
	            <option value="4" <?php if ($reqStatus == '4') { echo 'selected=""';} ?>>Revisi Pengecekan Perencanaan</option>
	            <option value="5" <?php if ($reqStatus == '5') { echo 'selected=""';} ?>>Pengecekan Kasubdit</option>
	            <option value="6" <?php if ($reqStatus == '6') { echo 'selected=""';} ?>>Persetujuan PPK</option>
				    </select>
			    </div>
	  		</div>
    		  <table id="example" class="table-responsive border-double table mb-0 table-bordered" style="width: 100%">
          	<thead>
               <tr>
								<th>IdAnalisa</th>
               	<th>SirupId</th>
								<th>IdPermohonan</th>
                <th>Tahun</th>
                <th>Kode RUP</th>
                <th>Kode PR</th>
                <th style="width:200px">Nama Paket</th>
                <th>Nilai Pagu RUP</th>
                <th>Nilai RAB</th>
                <th>Nilai HPS</th>
                <th>Waktu Awal</th>
                <th>Waktu Akhir</th>
                <th>StatusId</th>
                <th>Status</th>
                <th>Strategi Pengadaan</th>
                <th>Diterima</th>
              </tr>
            </thead>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
/** FIXED AREA-MENU-AKSI WHEN SCROLLING UP **/
$(document).ready(function() {
  var s = $("#sticker2");
	var f = $("#example_filter");
  var pos = s.position();
	var posfilter = f.position();

    $(window).scroll(function() {
      var windowpos = $(window).scrollTop();
        //if (windowpos >= pos.top) {
			if (windowpos >= 162) {
        s.addClass("stick");
				f.addClass("stickfilter");
      } else {
        s.removeClass("stick");
				f.removeClass("stickfilter");
      }
    });
});
</script>
