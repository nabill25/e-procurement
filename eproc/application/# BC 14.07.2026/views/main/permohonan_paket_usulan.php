<?php
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 * 
 */
$this->libsession->cekSession();
$this->DEPARTMENT = isset($this->kauth->getInstance()->getIdentity()->DEPARTMENT) ? $this->kauth->getInstance()->getIdentity()->DEPARTMENT : '';

?>
<link rel="stylesheet" type="text/css" href="lib/DataTables-1.10.7/media/css/jquery.dataTables.css">
<link rel="stylesheet" type="text/css" href="lib/DataTables-1.10.7/extensions/Responsive/css/dataTables.responsive.css">
<link rel="stylesheet" type="text/css" href="lib/DataTables-1.10.7/examples/resources/syntax/shCore.css">
<link rel="stylesheet" type="text/css" href="lib/DataTables-1.10.7/examples/resources/demo.css">
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
					   null, null, null, null, null,
					   null, null, null, null, null,
					   // null,
					   null
				  ],
		"bSort":true,
		"bProcessing": true,
		"bServerSide": true,
		"sAjaxSource": "permohonan_paket_usulan_json/permohonan_usulan_monitoring_json",
		"lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
		columnDefs: [{ "targets": 4,"orderable": false }, { className: 'never', targets: [0,1,2,3,7] },{ className: 'text-center', targets: [0,1,2,3,6,7,8,9] }]
		});//.rowGrouping({iGroupingColumnIndex: 0});
		oTable.fnSort( [ [0,'asc'] ] );
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
			  $('div.flexmenumobile').hide()
			  $('div.flexoverlay').css('display', 'none')
		  });

		  $('#btnPosting').on('click', function (){
				onTeruskan(); 
			});

			$('#btnRJ').on('click', function () {
				// let Idnya = $('.check:checked').val();
			 //  if($('.check:checked').length == 0)
			 //  {
		  // 		alertError3("Checklist data dahulu");
			 // 		return false;
	  	//   }
			 //  if($('.check:checked').length == 1)
			 //  {
		  // 		openAddLg("main/loadUrl/main/rekam_jejak_view?id="+Idnya+"&konversi=ya");
			 //  } else {
			 //  	alertError3("Checklist satu data!");
			 // 		return false;
			 //  } 
			 if(anSelectedData == "")
			  {
		  		alertError3("Pilih data dahulu");
			 		return false;
	  	  }
		  		openAddLg("main/loadUrl/main/rekam_jejak_view?id="+anSelectedId+"&konversi=ya");
		  });

			$('#btnEdit').on('click', function ()
			{
				if(anSelectedData == "")
				{
		  		alertError3("Pilih data dahulu");
				 	return false;
		  	}
				if(anSelectedApproval != "2" && anSelectedApproval != "0")
				{
					alertError3("Usulan sudah di teruskan");
					return;
				}
			 	document.location.href = 'main/index/permohonan_paket_usulan_add/?usulanId='+anSelectedId;
	  	});

		  $('#btnDelete').on('click', function () {
				onDelete();
		  });
 
		   $('#btnCetak').on('click', function () {
				newWindow = window.open("main/loadUrl/report/permohonan_paket_usulan_cetak", 'Cetak');
				newWindow.focus();
		  });

		$('#reqStatus').combobox({
			onSelect: function(param){
				oTable.fnReloadAjax("permohonan_paket_usulan_json/permohonan_usulan_monitoring_json/?reqStatus="+param.value);
			}
		});
} );

function reloadMonitoring()
{
	oTable.fnReloadAjax("permohonan_paket_usulan_json/permohonan_usulan_monitoring_json/?reqStatus="+param.value);
}

$(document).ready(function() {
	$('#checkAll').click(function () {
	  $('.check').prop('checked', $(this).prop('checked'));
	});
});

function onTeruskan()
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
  if(confirm('Apakah anda ingin meneruskan usulan?'))
  {
    $.ajax({
        url : '<?= base_url() ?>permohonan_paket_usulan_json/posting_usulan_post',
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
					oTable.fnReloadAjax("permohonan_paket_usulan_json/permohonan_usulan_monitoring_json");
        },
        error: function (jqXHR, textStatus, errorThrown)
        {
					alertError3("Data gagal diproses, silahkan dicoba kembali");
        }
    });
  }
}

function onDelete()
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
 
  if(confirm('Apakah anda ingin manghapus '+$('.check:checked').length+' usulan terpilih?'))
  {
    $.ajax({
        url : '<?= base_url() ?>permohonan_paket_usulan_json/delete_usulan_post',
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
					oTable.fnReloadAjax("permohonan_paket_usulan_json/permohonan_usulan_monitoring_json");
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
        <h4 class="card-title text-white">Usulan Kebutuhan 
        </h4>

        <a class="heading-elements-toggle"><i class="fa fa-ellipsis-v font-medium-3"></i></a>
        <div class="heading-elements">
            <ul class="list-inline mb-0">
              <li>
              	<?php 
              	if ($this->DEPARTMENT) { ?>
        				<a href="<?= base_url('/main/index/permohonan_paket_usulan_divisi') ?>" title="Lihat" class="btn btn-primary"> Lihat Usulan Kebutuhan <?= $this->DEPARTMENT ?></a>
        				<?php 
        				} ?>
              	<a data-action="expand"><i class="ft-maximize"></i></a></li>
              <!-- <li><a data-action="close"><i class="ft-x"></i></a></li> -->
            </ul>
        </div>
      </div>
      <div class="card-content collapse show border-info border-darken-2">
        <div class="card-body area-datatable">
        	<div class="row" id="sticker">
		    	<!-- <div class="form-group col-md-6 mb-2">
					<label style="width: 100%">Status</label>
				    <select name="reqStatus" id="reqStatus" class="form-control easyui-combobox span3" style="width: 300%">
				    		<option value="0">Belum Diteruskan atau Dikembalikan</option>
				            <option value="1">Approve</option>
				            <option value="2">Sudah Diproses</option>
				            <option value="">Semua</option>
				    </select>
			    </div> -->
		    	<div class="form-group col-md-12 mb-2t">
					<label style="width: 100%"></label>
	            <a id="btnLihat" title="Lihat" class="<?= CLASS_BTN_SUCCESS ?>"> <i class="fa fa-eye"></i> Lihat</a>
	            <a href="main/index/permohonan_paket_usulan_add" class="<?= CLASS_BTN_PRIMARY ?>"> <i class="fa fa-plus"></i> Tambah Manual</a> 
	            <a onclick="openAdd('main/loadUrlKontrak/main/permohonan_paket_usulan_add_importexcel')" class="badge badge-success pull-right mr-1 text-white"> <i class="fa fa-upload"></i> Import Usulan </a>
	            <a id="btnEdit" title="Ubah" class="<?= CLASS_BTN_INFO ?>"> <i class="fa fa-repeat"></i> Ubah</a>
	            <a id="btnDelete" title="Delete" class="<?= CLASS_BTN_DANGER ?>"> <i class="fa fa-trash"></i> Delete</a>
	            <a id="btnPosting" title="Teruskan" class="<?= CLASS_BTN_PRIMARY ?>"> <i class="fa fa-arrow-right"></i> Teruskan</a>
	            <a id="btnCetak" title="Cetak" class="<?= CLASS_BTN_INFO ?>"> <i class="fa fa-print"></i> Cetak</a>
        			<a id="btnRJ" title="Rekam Jejak" class="<?= CLASS_BTN_DARK ?>"><i class="fa fa-paw"></i> Rekam Jejak</a>
			    </div>
	  		</div>
    		  <table id="example" class="border-double table mb-0 table-bordered" style="width: 100%">
          	<thead>
              <tr>
               	<th width="1px">Id</th>
                <th width="">Posting</th>
                <th width="">Approval</th>
                <th width="">Permohonan</th>
                <th width="5%" style="text-align: center"><input type="checkbox" id="checkAll" style="cursor:pointer"> Tahun <br>Anggaran</th>
                <th width="75%">Nama Paket</th>
                <th width="5%" style="text-align: center">Produk <br>Dalam Negeri</th>
                <th width="5%">Jenis <br> Belanja</th>
                <th width="5%" style="text-align: center">Harga <br> Perkiraan</th>
                <th width="">Mulai Rencana <br>Pengadaan</th>
                <th width="5%" style="text-align: center">Waktu <br> Penggunaan</th>
                 <!-- <th width="">Jenis B/J</th> -->
              </tr>
            </thead>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>
