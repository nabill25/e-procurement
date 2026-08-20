<?php
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
					   null,
					   null,
					   null,
					   null,
					   null
				  ],
		"bSort":true,
		"bProcessing": true,
		"bServerSide": true,
		"sAjaxSource": "permohonan_paket_usulan_json/permohonan_usulan_monitoring_admin_json",
		"lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
		columnDefs: [{ "targets": 4,"orderable": false }, { className: 'never', targets: [0,1,2,3,7,11,12,13,14] }]
		// columnDefs: [{ "targets": 3,"orderable": false }, { className: 'never', targets: [1,2,11,12] }]
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
		  var anSelectedPermohonanId = '';

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
				  anSelectedPermohonanId = element[3];
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
								  oTable.fnReloadAjax("permohonan_paket_json/permohonan_usulan_monitoring_admin_json/?reqStatus=0");
						});
					}
				});
			});

			$('#btnCetak').on('click', function () {
				newWindow = window.open("main/loadUrl/report/permohonan_paket_unit_cetak/?reqMode=adminrupdraft", 'Cetak');
				newWindow.focus();
		  });

		  $('#btnRJ').on('click', function () {
			if(anSelectedData == "")
			{
		  		alertError3("Pilih data dahulu");
			 	return false;
		  	}
		  	// alert(anSelectedData);
		  	openAddLg("main/loadUrl/main/rekam_jejak_view?id="+anSelectedPermohonanId);
		  });

		   $('#btnLihat').on('click', function () {
			  if(anSelectedData == "")
			  {
		  		alertError3("Pilih data dahulu");
			 	return false;
		  	  }
	  		$('.check').prop('checked', false);

			  openAdd("main/loadUrl/main/permohonan_paket_usulan_periksa/?usulanId="+anSelectedId);

			  // tutup flex dropdown => untuk versi mobile
			  $('div.flexmenumobile').hide()
			  $('div.flexoverlay').css('display', 'none')
		  });

		   $('#btnLihat2').on('click', function () {
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

		   $('#btnToBeApprove').on('click', function () {
			  ondelete();
			  // openAdd("main/loadUrl/main/permohonan_paket_usulan_periksa/?usulanId="+anSelectedId);

			  // tutup flex dropdown => untuk versi mobile
			  $('div.flexmenumobile').hide()
			  $('div.flexoverlay').css('display', 'none')
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

$(document).ready(function() {
	$('#checkAll').click(function () {
	  $('.check').prop('checked', $(this).prop('checked'));
	});
});

function ondelete()
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
		alertError3("Pilih data dahulu");
    return false;
  }

  // alert(chkId);
  if(confirm('Usulan Kebutuhan To Be Approved ?'))
  {
    $.ajax({
        url : '<?= base_url() ?>permohonan_paket_usulan_json/ontobeapproved',
        type: 'POST',
        data: {'chkId':chkId},
        dataType: 'JSON',
        success: function(data)
        {
        	if (data.respon == 'GAGAL') {
						alertError3("Data gagal diproses, silahkan dicoba kembali");
        	} else {
						alertSuccess2("Data berhasil diproses");
        	}
					oTable.fnReloadAjax("permohonan_paket_usulan_json/permohonan_usulan_monitoring_admin_json");
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
        <h4 class="card-title text-white">Usulan Kebutuhan </h4>
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
        	<div class="col-md-12">
                <ul class="nav nav-tabs process-model more-icon-preocess" role="tablist">
                  <li role="presentation" style="width:50% !important" class="active"><a href="main/index/permohonan_paket_usulan_admin"><i class="fa fa-book" aria-hidden="true"></i>
                    <p>Draft</p>
                    </a></li>
                  <li role="presentation" style="width:50% !important"><a href="main/index/permohonan_paket_usulan_admin_to_be_approved" ><i class="fa fa-pencil" aria-hidden="true"></i>
                    <p>To Be approved</p>
                    </a></li>
                </ul>
            </div>
        	<div class="row" id="sticker">
		    	<div class="form-group col-md-12 mb-2t">
					<label style="width: 100%"></label>
            <a id="btnToBeApprove" title="To Be Approve" class="<?= CLASS_BTN_PRIMARY ?>"> <i class="fa fa-edit"></i> To Be Approve</a>
            <a id="btnLihat" title="Periksa" class="<?= CLASS_BTN_DANGER ?>"> <i class="fa fa-repeat"></i> Kembalikan Usulan</a>
            <a id="btnLihat2" title="Lihat" class="<?= CLASS_BTN_SUCCESS ?>"> <i class="fa fa-eye"></i> Lihat</a>
            <!-- <a id="btnPublish" title="Delete" class="<?= CLASS_BTN_DANGER ?>"> <i class="fa fa-check-square"></i> Publish</a> -->
            <a id="btnCetak" title="Cetak" class="<?= CLASS_BTN_INFO ?>"> <i class="fa fa-print"></i> Cetak</a>
      			<a id="btnRJ" title="Rekam Jejak" class="btn round btn-min-width box-shadow-1 btn-dark text-white"><i class="fa fa-paw"></i> Rekam Jejak</a>
			    </div>
	  		</div>
    		  <table id="example" class="border-double table mb-0 table-bordered" style="width: 100%">
	          	<thead>
	               <tr>
	               	<th width="1px">Id</th>
	                <th width="">Posting</th>
	                <th width="">Approval</th>
	                 <th width="">Permohonan ID</th>
	                <th width="5%" style="text-align: center"><input type="checkbox" id="checkAll" style="cursor:pointer"> Tahun <br>Anggaran</th>
	                <th width="35%">Nama Paket</th>
	                <th width="5%" style="text-align:center">Produk <br>Dalam Negeri</th>
	                <th width="5%">Jenis <br>Belanja</th>
	                <th width="5%" style="text-align: center">Harga<br>Perkiraan</th>
	                <th width="5%">Mulai Rencana <br>Pengadaan</th>
	                <th width="5%" style="text-align: center">Waktu <br> Penggunaan</th>
	                 <th width="">Jenis B/J</th>
	                 <th width="">Cara Pengadaan</th>
	                 <th width="">Pembuat</th>
	                 <th width="5%">Kode RUP</th>
	                 <!-- <th width="">User</th> -->
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
