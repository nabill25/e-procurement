<?php
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 * 
 */
$this->libsession->cekSession();

$this->load->model("UserLogin");
$user_login_jabatan = new UserLogin();
$user_login_jabatan->selectByParams(array("USER_LOGIN_ID"=> $this->USER_LOGIN_ID));
$user_login_jabatan->firstRow(); 
if ($user_login_jabatan->getField('PENUNJUK_PIC') == '1' || $this->USER_TYPE_ID == '7'  || $this->USER_TYPE_ID == '11') { // Bukan Ketua dan bukan Penunjuk PIC atau MANAGER PENGADAAN atau PELAKSANA PEMBELI
} else {	redirect(base_url().'main/index/403'); }

if ($this->USER_TYPE_ID == '7') { // Manajer Peng.
 	$lablePIC = 'Ketua';
} else {
 	$lablePIC = 'PIC';
} 
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
						   {"bVisible": false}, {"bVisible": false}, {"bVisible": false}, null, null,
						   null,null,null,null,null,
						   null,null,null,null,null
				  ],
		"bSort":true,
		"bProcessing": true,
		"bServerSide": true,
		"sAjaxSource": "permohonan_paket_json/permohonan_lelang_bypass_json/?reqStatusPIC=1",
		"lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
		columnDefs: [{ className: 'never', targets: [ 0,1,2,3,4,6,7,8,11,14] }]
		// columnDefs: [{ className: 'never', targets: [] }]
		});//.rowGrouping({iGroupingColumnIndex: 0});
		oTable.fnSort( [ [0,'desc'] ] );

		new $.fn.dataTable.Responsive( oTable );
		  var anSelectedData = '';
		  var anSelectedId = '';
		  var anSelectedStatus = '';

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
				  anSelectedId = element[0]; // perId
				  anSelectedStatus = element[1];
				  anSelectedAnalisaId = element[3];
				  anSelectedSirupId = element[4];
		  }); 

		   $('#btnLihat').on('click', function () {
			  if(anSelectedData == "") { alertError3("Pilih data dahulu"); return false; }
			  openAdd("main/loadUrl/main/permohonan_lelang_panitia_add/?reqId="+anSelectedId);
			  $('div.flexmenumobile').hide()
			  $('div.flexoverlay').css('display', 'none')
		  });

		   $('#btnBuatPaket').on('click', function () {
			  if(anSelectedData == "") { alertError3("Pilih data dahulu"); return false; }
			  if(anSelectedStatus == "1") { alertError3("Paket telah dibuat"); return false; }

			  document.location.href = 'kontrak/index/paket_lelang_tambah_bypass/?reqPermohonanId='+anSelectedId;
			  $('div.flexmenumobile').hide()
			  $('div.flexoverlay').css('display', 'none')
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

		$('#btnRJ').on('click', function () {
			if(anSelectedData == "") { alertError3("Pilih data dahulu"); return false; }
		  	openAddLg("main/loadUrl/main/rekam_jejak_view?id="+anSelectedId);
		});  
} );

function reloadMonitoring()
{
	oTable.fnReloadAjax("permohonan_paket_json/permohonan_lelang_tunjuk_pic_json/?reqStatusPIC="+$("#reqStatusPIC").combobox('getValue'));
}

function getNotif() {
      $.getJSON("main/getNotif", function(json) {
        $('#notif_count').html(json.count);
        $('#notif_message').html(json.data);
      });
    }

</script>

<div class="row">
  <div class="col-md-12 col-sm-12">
    <div class="card">
      <div class="card-header card-head-inverse bg-primary">
        <h4 class="card-title text-white">Contracting Bypass</h4>
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
          <a id="btnLihat" title="Lihat" class="btn round btn-min-width box-shadow-1 btn-primary text-white"><i class="fa fa-eye"></i> Lihat</a>
          <a id="btnBuatPaket" title="Buat Kontrak" class="btn round btn-min-width box-shadow-1 btn-success text-white"><i class="fa fa-plus"></i> Buat Kontrak</a>
		    </div> 
		  </div>

    			<table id="example" class="border-double table mb-0 table-bordered" style="width: 100%">
	          <thead>
             <tr>
              <th width="1px">permohonanId</th>
              <th width="90px">status</th>
              <th width="1px">permohonanIdEncrypt</th>
              <th width="7%">analisaId</th>
              <th width="7%">sirupId</th>
              <th width="90px">Status</th>
              <th width="90px">Perencana</th>
              <th width="90px">No. Disposisi</th>
              <th width="90px">Tanggal Disposisi</th>
              <th width="120px">Nama Paket</th>
              <th width="90px">Harga Perkiraan Sendiri</th>
              <th width="90px"><?= $lablePIC ?></th>
              <th width="7%">Kode RUP</th>
              <th width="7%">Kode PR</th>
              <th width="7%">Alasan Tolak</th>
              </tr>
	          </thead>
	        </table>

        </div>
      </div>
    </div>
  </div>
</div>


<script>
$(document).ready(function() {
    var s = $("#sticker");
	var f = $("#example_filter");
    var pos = s.position();
	var posfilter = f.position();
    $(window).scroll(function() {
        var windowpos = $(window).scrollTop();
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
