<?php 
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 * 
 */
$this->libsession->cekSession(); 

$status = $this->input->get("status") ?: '00';

$this->load->model("Dashboardvms");
$countData = new Dashboardvms();
$countDataKirimBerkas = new Dashboardvms();
$countData->selectByParamsForVerifikasi();
$countData->firstRow();

$countDataKirimBerkas->selectByParamsKirimBerkasRevisi();
$countDataKirimBerkas->firstRow();
// ---------------------------------- TOTAL PENYEDIA ------------------------------

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
		"aoColumns": [
						 {"bVisible": false}, null, {"bVisible": false}, null,null,
						 null,null,null,null,{"bVisible": false},
				  ],
		"bSort":true,
		"bProcessing": true,
		"bServerSide": true,		
		"sAjaxSource": "rekanan_json/daftar_rekanan_json/?reqStatus=<?= $status ?>",
		"lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],	
		columnDefs: [{ className: 'never', targets: [0,7,9] },{ className: 'text-center', targets: [ 6,7 ] }]
		});
		oTable.fnSort( [ [1,'desc'] ] );

		new $.fn.dataTable.Responsive( oTable );
		
		  var anSelectedData = '';
		  var anSelectedId = '';
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
				  anNoReg = element[1];
				  anSelectedNama = element[2];
				  anSelectedStatusValidasi = element[7];
				  anSelectedStatusAktif = element[8];
				  anSelectedLogid = element[9];
		  });
		  

		  // New
		  $('#reqMelengkapiBerkas').on('click', function () {
				oTable.fnReloadAjax("rekanan_json/daftar_rekanan_json/?reqStatus=00");
				$('#statusTampil').val('00');
				$('#showLabel').html('Melengkapi Berkas');
		  });   
		  $('#reqKirimBerkas').on('click', function () {
				oTable.fnReloadAjax("rekanan_json/daftar_rekanan_json/?reqStatus=20");
				$('#statusTampil').val('20');
				$('#showLabel').html('Kirim Berkas');
		  });    
		  $('#reqProsesApproval').on('click', function () {
				oTable.fnReloadAjax("rekanan_json/daftar_rekanan_json/?reqStatus=24");
				$('#statusTampil').val('24');
				$('#showLabel').html('Proses Approval');
		  });    
		  $('#reqBerkasDitolak').on('click', function () {
				oTable.fnReloadAjax("rekanan_json/daftar_rekanan_json/?reqStatus=210");
				$('#statusTampil').val('210');
				$('#showLabel').html('Pengajuan ditolak');
		  });    
		  $('#reqTerverifikasi').on('click', function () {
				oTable.fnReloadAjax("rekanan_json/daftar_rekanan_json/?reqStatus=11");
				$('#statusTampil').val('11');
				$('#showLabel').html('Terverifikasi');
		  });    
		  $('#reqRevisi').on('click', function () {
				oTable.fnReloadAjax("rekanan_json/daftar_rekanan_json/?reqStatus=01");
				$('#statusTampil').val('01');
				$('#showLabel').html('Revisi');
		  });   
		  // End New
  
	
		  $('#btnCetak').on('click', function () {
		  	var reqStatus = $('#statusTampil').val(); 
				newWindow = window.open("main/loadUrl/report/vms_daftar_penyedia/?reqStatus="+reqStatus, 'Cetak');
				newWindow.focus();
		  });
		  
		   $('#btnLihatDetil').on('click', function () {
			  if(anSelectedData == "") {
			  	  alertError3('Pilih data dahulu');
				  return false;				
			  }
			  openAdd("main/loadUrl/main/data_rekanan/?reqId="+anSelectedId+"&reqValidasi=1");
				
			  // tutup flex dropdown => untuk versi mobile
			  $('div.flexmenumobile').hide()
			  $('div.flexoverlay').css('display', 'none')
		  });

		   $('#reqStatus').combobox({
				onSelect: function(param){
					oTable.fnReloadAjax("rekanan_json/daftar_rekanan_json/?reqStatus="+param.value);
				}
			});   

		$('#btnUbahStatus').on('click', function () 
	  {
		if(anSelectedData == "")
		{
  		alertError3("Pilih data dahulu");
		 	return false;				
  	}

  	if(anSelectedStatusValidasi == "0" || anSelectedStatusValidasi == "4" || anSelectedStatusValidasi == "10" )
		{
  		alertError3("Status tidak bisa diubah, karena belum terverifikasi");
		 	return false;				
  	}

		$.messager.confirm('Konfirmasi',"Apakah anda ingin mengubah status?",function(r){
			if (r){
				var reqStatus = $('#statusTampil').val();
				$.getJSON("users_base_json/ubah_status2/?reqId="+anSelectedId,
				  function(data){
					  $.messager.alert('Info', data.PESAN, 'info');
					  oTable.fnReloadAjax("rekanan_json/daftar_rekanan_json/?reqStatus="+reqStatus);
					  $.getJSON("rekanan_json/getstatus",
						  function(data2){ 
						  	$('#showLabelMelengkapi').html(data2.melengkapi);
						  	$('#showLabelKirim').html(data2.kirim);
						  	$('#showLabelProses').html(data2.proses);
						  	$('#showLabelTolak').html(data2.tolak);
						  	$('#showLabelTerverifikasi').html(data2.terverifikasi);
						  	$('#showLabelRevisi').html(data2.revisi);
						});		
				});				
			}
		});
	});	  

	$('#btnUbahStatusAktif').on('click', function () 
	  {
		if(anSelectedData == "")
		{
  		alertError3("Pilih data dahulu");
		 	return false;				
  	}
		$.messager.confirm('Konfirmasi',"Apakah anda ingin mengubah status aktif "+anSelectedNama+" ?",function(r){
			if (r){
				var reqStatus = $('#statusTampil').val();
				$.getJSON("users_base_json/ubah_status_aktif/?reqId="+anSelectedLogid,
				  function(data){
					  $.messager.alert('Info', data.PESAN, 'info');
					  oTable.fnReloadAjax("rekanan_json/daftar_rekanan_json/?reqStatus="+reqStatus);
				});				
			}
		});
	});	   
} );


function reloadMonitoring()
{
	oTable.fnReloadAjax("rekanan_json/daftar_rekanan_json");	
}

</script> 
<input type="hidden" id="statusTampil" value="00">
<div class="row">  
  <div class="col-xl-2 col-lg-2 col-12">
    <div class="card bg-gradient-directional-danger" id="reqMelengkapiBerkas" style="cursor: pointer;">
      <div class="card-content">
        <div class="card-body">
          <div class="media d-flex"> 
            <div class="media-body text-white text-center">
              <h3 class="text-white" id="showLabelMelengkapi"><?= number_format($countData->getfield('melengkapi_berkas'),0, ",",".")?></h3>
              <span>Melangkapi Berkas</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-xl-2 col-lg-2 col-12">
    <div class="card bg-gradient-directional-info" id="reqKirimBerkas" style="cursor: pointer;">
      <div class="card-content">
        <div class="card-body" style="padding: 1.5rem 0;">
          <div class="media d-flex"> 
            <div class="media-body text-white text-center">
            	<ul class="list-inline text-center clearfix mb-0">
                <li class="border-right-grey border-right-lighten-2 pr-1">
                   <h3 class="block text-white"><?= number_format($countData->getfield('kirim_berkas') - $countDataKirimBerkas->getfield('total'),0, ",",".")?></h3>
                	<span class="text-white"><small>Kirim Berkas</small></span>
                </li>
                <li class="pl-1">
                  <h3 class="block text-white"><?= number_format($countDataKirimBerkas->getfield('total'),0, ",",".")?></h3>
                	<span class="text-white"><small>Dikembalikan</small></span>
                </li>
              </ul>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-xl-2 col-lg-2 col-12">
    <div class="card bg-gradient-directional-primary" id="reqProsesApproval" style="cursor: pointer;">
      <div class="card-content">
        <div class="card-body">
          <div class="media d-flex"> 
            <div class="media-body text-white text-center">
              <h3 class="text-white" id="showLabelProses"><?= number_format($countData->getfield('proses_approval'),0, ",",".")?></h3>
              <span>Proses Persetujuan</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-xl-2 col-lg-2 col-12">
    <div class="card bg-gradient-directional-warning" id="reqBerkasDitolak" style="cursor: pointer;">
      <div class="card-content">
        <div class="card-body">
          <div class="media d-flex"> 
            <div class="media-body text-white text-center">
              <h3 class="text-white" id="showLabelTolak"><?= number_format($countData->getfield('berkas_ditolak'),0, ",",".")?></h3>
              <span>Tolak</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-xl-2 col-lg-2 col-12">
    <div class="card bg-gradient-directional-success" id="reqTerverifikasi" style="cursor: pointer;">
      <div class="card-content">
        <div class="card-body">
          <div class="media d-flex"> 
            <div class="media-body text-white text-center">
              <h3 class="text-white" id="showLabelTerverifikasi"><?= number_format($countData->getfield('terverfikasi'),0, ",",".")?></h3>
              <span>Terverifikasi</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-xl-2 col-lg-2 col-12">
    <div class="card bg-gradient-directional-success" id="reqRevisi" style="background-image: linear-gradient(45deg, #fc2565, #ed9800); cursor: pointer;">
      <div class="card-content">
        <div class="card-body">
          <div class="media d-flex"> 
            <div class="media-body text-white text-center">
              <h3 class="text-white" id="showLabelRevisi"><?= number_format($countData->getfield('revisi'),0, ",",".")?></h3>
              <span>Revisi</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-md-12 col-sm-12">

    <div class="card"> 
      <div class="card-content collapse show border-info border-darken-2">
        <div class="card-body area-datatable">
 
        	<div class="row" id="sticker">
		     	<div class="form-group col-md-8 mb-2">
		      <!-- <a href="main/index/daftar_rekanan_belum_valid" class="btn btn-info text-white"> <span class="fa fa-ban"></span> Belum Verifikasi </a> -->
		      <!-- <a href="main/index/daftar_rekanan_valid" class="btn btn-primary text-white"> <span class="fa fa-check-square-o"></span> Verifikasi </a> -->
		      <!-- <a href="main/index/daftar_rekanan_hapus" class="btn btn-danger text-white"> <span class="fa fa-trash"></span> Hapus </a> -->
		      <a id="btnLihatDetil" title="Lihat Detil" class="<?= CLASS_BTN_SUCCESS ?>"><span class="fa fa-eye"></span> Lihat Detil / Verifikasi</a>
          <a id="btnCetak" title="Cetak" class="<?= CLASS_BTN_INFO ?>"> <i class="fa fa-print"></i> Cetak</a>
          <?php 
		      	if ($this->USER_TYPE_ID == '2') {?>
	          <a id="btnUbahStatus" class="<?= CLASS_BTN_PRIMARY ?>" title="Ubah Status User"><span class="fa fa-pencil"></span> Ubah Status Validasi</a>
	          <a id="btnUbahStatusAktif" class="<?= CLASS_BTN_DANGER ?>" title="Ubah Status User"><span class="fa fa-pencil"></span> Ubah Status Aktif</a>
			      <!-- <a href="main/index/daftar_rekanan_hapus" class="btn btn-danger text-white"> <span class="fa fa-trash"></span> Hapus </a> -->
			      <?php 
			      } ?>
		     </div> 
		     <div class="form-group col-md-4 mb-2 text-right">
						<label style="width: 100%"><h3 id="showLabel">Melengkapi Berkas</h3></label>
			    </div>
		    </div>

            <div class="table-responsive">
              <table id="example" class="border-double table mb-0 table-bordered">
					      <thead>
			            <tr>
		                <th>rekId</th>
		                <th width="6%">No Registrasi</th>
		                <th width="32%">Nama</th>
		                <th>Kota</th>
		                <th>Tanggal</th>
		                <th width="15%">Validator</th>
		                <th width="5%" style="text-align: center">Status <br> Validasi</th>
		                <th width="5%" style="text-align: center">Status <br> Validasi</th>
		                <th width="5%" style="text-align: center">Status <br> Aktif</th>
		                <th>logId</th>
			            </tr>       
				       	</thead>
		          </table>   
            </div>

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

        //if (windowpos >= pos.top) {
		if (windowpos >= 257) {
			//alert(windowpos);
            s.addClass("stick");
			f.addClass("stickfilter");
        } else {
            s.removeClass("stick"); 
			f.removeClass("stickfilter"); 
        }
    });
	
});
</script>
