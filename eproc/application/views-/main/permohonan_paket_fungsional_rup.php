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
					   null
				  ],
		"bSort":true,
		"bProcessing": true,
		"bServerSide": true,
		"sAjaxSource": "permohonan_paket_json/permohonan_lelang_monitoring_rup_json/?reqStatus=0",
		"lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
		columnDefs: [{ "targets": 4,"orderable": false }, { className: 'never', targets: [0,1,2,3,5,6,9] }]
		});//.rowGrouping({iGroupingColumnIndex: 0});
		oTable.fnSort( [ [0,'desc'] ] );
		
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

		  $('#btnPublish').on('click', function (){
		  		if(anSelectedData == "")
				{
			  		alertError3("Pilih data dahulu");
				 	return false;
			  	}

				$.messager.confirm('Konfirmasi',"Publish Rencana Pengadaan?",function(r){
					if (r){
						$.get("permohonan_paket_json/publish_usulan/?reqId="+anSelectedId,
						  function(data){
							  		//alert(data);return false;
								  $.messager.alert('Info', data, 'info');
								  oTable.fnReloadAjax("permohonan_paket_json/permohonan_lelang_monitoring_rup_json/?reqStatus=0");
						});
					}
				});
			});

			$('#btnToBePublish').on('click', function () {
			  onpublish();
			  $('div.flexmenumobile').hide()
			  $('div.flexoverlay').css('display', 'none')
		  });  

		  $('#btnToBeUnPublish').on('click', function () {
			  onunpublish();
			  $('div.flexmenumobile').hide()
			  $('div.flexoverlay').css('display', 'none')
		  });  

		  $('#btnCetakPdf').on('click', function () {
			  newWindow = window.open("main/loadUrl/report/pelanggan_cetak_pdf/", 'Cetak');
			  newWindow.focus();
		  }); 

		   $('#btnCetak').on('click', function () {
				newWindow = window.open("main/loadUrl/report/permohonan_paket_unit_cetak/?reqMode=adminrup", 'Cetak');
				newWindow.focus();
		  });

		$('#reqStatus').combobox({
			onSelect: function(param){
				oTable.fnReloadAjax("permohonan_paket_json/permohonan_lelang_monitoring_rup_json/?reqStatus="+param.value);
			}
		});
} );

function reloadMonitoring()
{
	oTable.fnReloadAjax("permohonan_paket_json/permohonan_lelang_monitoring_rup_json/?reqStatus="+param.value);
}

function make(a) {
	openAdd("main/loadUrl/main/permohonan_paket_usulan_lihat/?usulanId="+a);
}

$(document).ready(function() {
	$('#checkAll').click(function () {
	  $('.check').prop('checked', $(this).prop('checked'));
	}); 
}); 

function onpublish()
{ 
  if ($('.check:checked').length) {
    var chkId = '';
    $('.check:checked').each(function () {
      chkId += $(this).val() + ',';
    });
    chkId = chkId.slice(0, -1);
    // alert(chkId);
  }
  else {
		alertError3("Checklist data dahulu");
    return false;
  }

  // alert(chkId); return false;
  if(confirm('Publish Rencana Pengadaan ?'))
  {
    $.ajax({
        url : '<?= base_url() ?>permohonan_paket_json/publish_usulan_multi',
        type: 'POST',
        data: {'chkId':chkId},
        dataType: 'JSON',
        success: function(data)
        {
        	if (data.respon == 'GAGAL') {
						alertError3(data.message);
        	} else {
						alertSuccess2(data.message);
        	}
					oTable.fnReloadAjax("permohonan_paket_json/permohonan_lelang_monitoring_rup_json/?reqStatus=0");
        },
        error: function (jqXHR, textStatus, errorThrown)
        {
					alertError3("Data gagal diproses, silahkan dicoba kembali");
        }
    });
  }
}

function onunpublish()
{ 
  if ($('.check:checked').length) {
    var chkId = '';
    $('.check:checked').each(function () {
      chkId += $(this).val() + ',';
    });
    chkId = chkId.slice(0, -1);
    // alert(chkId);
  }
  else {
		alertError3("Checklist data dahulu");
    return false;
  }

  // alert(chkId); return false;
  if(confirm('Unpublish Rencana Pengadaan ?'))
  {
    $.ajax({
        url : '<?= base_url() ?>permohonan_paket_json/unpublish_usulan_multi',
        type: 'POST',
        data: {'chkId':chkId},
        dataType: 'JSON',
        success: function(data)
        {
        	if (data.respon == 'GAGAL') {
						alertError3(data.message);
        	} else {
						alertSuccess2(data.message);
        	}
					oTable.fnReloadAjax("permohonan_paket_json/permohonan_lelang_monitoring_rup_json/?reqStatus=0");
        },
        error: function (jqXHR, textStatus, errorThrown)
        {
					alertError3("Data gagal diproses, silahkan dicoba kembali");
        }
    });
  }
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
		    	<div class="form-group col-md-8 mb-2" id="sticker">
						<label style="width: 100%"></label>
            <!-- <a id="btnToBePublish" title="Publish" class="<?= CLASS_BTN_PRIMARY ?>"> <i class="fa fa-edit"></i> Publish</a> -->
            <!-- <a id="btnToBeUnPublish" title="Unpublish" class="<?= CLASS_BTN_DANGER ?>"> <i class="fa fa-close"></i> Unpublish</a> -->
            <a id="btnLihat" title="Lihat" class="<?= CLASS_BTN_SUCCESS ?>"> <i class="fa fa-eye"></i> Lihat</a>
            <!-- <a id="btnPublish" title="Delete" class="<?= CLASS_BTN_DANGER ?>"> <i class="fa fa-check-square"></i> Publish</a> -->
            <a id="btnCetak" title="Cetak" class="<?= CLASS_BTN_INFO ?>"> <i class="fa fa-print"></i> Cetak</a>
			    </div>
		    	<!-- <div class="form-group col-md-4 mb-2 text-right">
						<label style="width: 100%"></label>
				    <select name="reqStatus" id="reqStatus" class="form-control easyui-combobox span3" style="width: 300%">
			    		<option value="0">Belum Diteruskan atau Dikembalikan</option>
	            <option value="1">Belum Diproses</option>
	            <option value="2">Sudah Diproses</option>
	            <option value="">Semua</option>
				    </select>
			    </div> -->
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
	                <th width="10%"><input type="checkbox" id="checkAll" style="cursor:pointer"> Tahun <br> Anggaran</th>
	                <th width="5%">No. Nota</th>
	                <th width="5%">Tanggal Nota</th>
	                <th width="50%">Nama Paket</th>
	                <th width="13%">Harga Perkiraan</th>
	                 <th width="15%">PIC</th>
	                <th width="5%">Mulai Rencana <br> Pengadaan</th>  
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
