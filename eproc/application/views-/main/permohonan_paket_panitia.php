<?php
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 *
 */
$this->libsession->cekSession();
 ?>
<link rel="stylesheet" type="text/css" href="<?=base_url()?>lib/DataTables-1.10.7/media/css/jquery.dataTables.css">
<link rel="stylesheet" type="text/css" href="<?=base_url()?>lib/DataTables-1.10.7/extensions/Responsive/css/dataTables.responsive.css">
<link rel="stylesheet" type="text/css" href="<?=base_url()?>lib/DataTables-1.10.7/examples/resources/syntax/shCore.css">
<link rel="stylesheet" type="text/css" href="<?=base_url()?>lib/DataTables-1.10.7/examples/resources/demo.css">
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
						   {"bVisible": false},{"bVisible": false},{"bVisible": false},null,null,
						   null,null,null,null,null,
						   null,null
				  ],
		"bSort":true,
		"bProcessing": true,
		"bServerSide": true,
		"sAjaxSource": "permohonan_paket_json/permohonan_lelang_panitia_monitoring_json?reqStatus=1",
		"lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
		columnDefs: [{ className: 'never', targets: [ 0,1,2,3,5,6 ]}, 
		// columnDefs: [{ className: 'never', targets: [ ]}, 
		{ className: 'text-center', targets: [8,9,10] }]
		});//.rowGrouping({iGroupingColumnIndex: 0});
    oTable.fnSort( [ [0,'desc'] ] );
		new $.fn.dataTable.Responsive( oTable );
		  var anSelectedData = '';
		  var anSelectedId = '';
		  var anSelectedStatus = '';
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
				  anSelectedStatus = element[1];
				  anSelectedStatusKJ = element[2];
		  });

		   $('#btnLihat').on('click', function () {
			  if(anSelectedData == "") { alertError3("Pilih data dahulu"); return false; }
			  openAdd("main/loadUrl/main/permohonan_lelang_panitia_add/?reqId="+anSelectedId);
			  $('div.flexmenumobile').hide()
			  $('div.flexoverlay').css('display', 'none')
		  });

			$('#btnKembali').on('click', function () {
			  if(anSelectedData == "") { alertError3("Pilih data dahulu"); return false; }
	  	  if(anSelectedStatus == "1") { alertError3("Paket telah dibuat."); return false; }

			  openAdd("main/loadUrl/main/permohonan_paket_kembali_add/?reqId="+anSelectedId);
			  $('div.flexmenumobile').hide()
			  $('div.flexoverlay').css('display', 'none')
		  });


		   $('#btnBuatPaket').on('click', function () {
			  if(anSelectedData == "") { alertError3("Pilih data dahulu"); return false; }
			  if(anSelectedStatus == "1") { alertError3("Paket telah dibuat."); return false; }
			  <?php 
			  if ($this->USER_TYPE_ID == 3) { ?>
			  if(anSelectedStatusKJ != "1") { alertError3("Belum dilakukan Kaji Ulang."); return false; }
			  <?php 
			  } ?>

			  document.location.href = 'main/index/paket_lelang_tambah/?reqPermohonanId='+anSelectedId;
			  $('div.flexmenumobile').hide()
			  $('div.flexoverlay').css('display', 'none')
		  });

		  $('#btnDelete').on('click', function () {
				if(anSelectedData == "") { alertError3("Pilih data dahulu"); return false; }
				deleteData("bank_json/delete/", anSelectedId);
		  });

		  $('#btnCetakPdf').on('click', function () {
			  newWindow = window.open("main/loadUrl/report/pelanggan_cetak_pdf/", 'Cetak');
			  newWindow.focus();
		  });

		  $('#btnCetakExcel').on('click', function () {
			  newWindow = window.open("main/loadUrl/report/pelanggan_cetak_excel/", 'Cetak');
			  newWindow.focus();
		  });

		   $('#btnCetak').on('click', function () {

				newWindow = window.open("main/loadUrl/report/permohonan_paket_cetak/?reqMode=panitia&reqStatus="+$("#reqStatus").combobox('getValue'), 'Cetak');
				newWindow.focus();
		  });

		  $('#btnRJ').on('click', function () {
			if(anSelectedData == "") { alertError3("Pilih data dahulu"); return false; }
		  	openAddLg("main/loadUrl/main/rekam_jejak_view?id="+anSelectedId);
		  });

		  $('#btnKU').on('click', function () {
			if(anSelectedData == "") { alertError3("Pilih data dahulu"); return false; }
		  	openAddLg("main/loadUrl/main/rekam_jejak_kaji_ulang_view?id="+anSelectedId);
		  });

		  $('#btnReset').on('click', function () {
				if(anSelectedData == "") { alertError3("Pilih data dahulu"); return false; }
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

		$('#reqStatus').combobox({
			onSelect: function(param){
				oTable.fnReloadAjax("permohonan_paket_json/permohonan_lelang_panitia_monitoring_json/?reqStatus="+param.value);
			}
		});
} );

function reloadMonitoring()
{
	oTable.fnReloadAjax("permohonan_paket_json/permohonan_lelang_panitia_monitoring_json?reqStatus=1");
}

</script>

<div class="row">
  <div class="col-md-12 col-sm-12">
    <div class="card">
      <div class="card-header card-head-inverse bg-primary">
        <h4 class="card-title text-white">Permohonan Paket </h4>
        <a class="heading-elements-toggle"><i class="fa fa-ellipsis-v font-medium-3"></i></a>
        <div class="heading-elements">
            <ul class="list-inline mb-0">
              <li><a data-action="expand"><i class="ft-maximize"></i></a></li>
              <!-- <li><a data-action="close"><i class="ft-x"></i></a></li> -->
            </ul>
        </div>
      </div>
      <div class="card-content collapse show border-info border-darken-2">
        <div class="card-body">
          <div class="row">
				    <div class="form-group col-md-8 mb-2" id="sticker">
			      	<label>&nbsp;</label>
			        <a id="btnLihat" title="Tambah" class="<?= CLASS_BTN_SUCCESS ?>"><i class="fa fa-eye"></i> Lihat Detail Data</a>
			        <a id="btnCetak" class="<?= CLASS_BTN_INFO ?>"><i class="fa fa-print"></i> Cetak</a>
		          <a id="btnRJ" title="Rekam Jejak" class="<?= CLASS_BTN_DARK ?>"><i class="fa fa-paw"></i> Rekam Jejak</a>
		          <?php
							if ($this->USER_TYPE_ID == 3) { // POKJA ANGGOTA  ?>
		          <a id="btnKU" title="Kaji Ulang" class="<?= CLASS_BTN_WARNING ?>"><i class="fa fa-paw"></i> Rekam Kaji Ulang</a>
							<?php 
							} ?>
			        <?php
							if ($this->USER_TYPE_ID == 3 || $this->USER_TYPE_ID == 11) { // POKJA ANGGOTA  ?>
			        	<a id="btnBuatPaket" class="<?= CLASS_BTN_PRIMARY ?>"><i class="fa fa-plus"></i> Proses Paket</a>
							<?php
							} ?>
							<?php
							if ($this->USER_TYPE_ID == 3 && $this->USER_JABATAN_PANITIA == 1) { // POKJA KETUA  ?>
			        	<!-- <a id="btnBuatPaket" class="<?= CLASS_BTN_PRIMARY ?>"><i class="fa fa-plus"></i> Tunjuk Pokja</a> -->
							<?php
							} 

							if ($this->USER_TYPE_ID != 3) {?>
			        	<a id="btnKembali" class="<?= CLASS_BTN_DANGER ?>"><i class="fa fa-arrow-left"></i> Kembalikan</a>
			        <?php 
			        } ?>
			        
				    </div>
				    <div class="form-group col-md-4 mb-2 text-right" id="sticker">
				      <label>Status</label>
			        <select name="reqStatus" id="reqStatus" class="easyui-combobox" style="width: 150%">
		            <option value="1">Belum Diproses</option>
		            <option value="2">Sudah Diproses</option>
		            <option value="0">Semua</option>
			        </select>
				    </div>
		  		</div>
		  		<div class="area-datatable">
    				<table id="example" class="border-double table mb-0 table-bordered" style="width: 100%">
      	    	<thead>
	               <tr>
                  <th width="1px">IdPermohonan</th>
                  <th width="8%">status</th>
                  <th width="8%">KJ</th>
                  <th width="1px">Id</th>
                  <th width="90px">Status</th>
                  <th width="90px">No. Disposisi</th>
                  <th width="90px">Tanggal Disposisi</th>
                  <th width="45%">Nama Paket</th>
                  <th width="90px">Harga Perkiraan Sendiri</th>
                  <th width="90px">PIC</th>
                  <th width="8%">Kode RUP</th>
                  <th width="8%">Kode PR</th>
	                </tr>
	            </thead>
						</table>
	      	</div>
        </div>
      </div>
    </div>
  </div>
</div>
