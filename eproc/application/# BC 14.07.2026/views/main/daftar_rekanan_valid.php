<?php 
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 * 
 */
 $this->libsession->cekSession(); ?>

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
$(document).ready(function() {
	var oTable;            
	oTable = $('#example').dataTable({ bJQueryUI: true,"iDisplayLength": 10,
		/* UNTUK MENGHIDE KOLOM ID */
		"aoColumns": [
						 {"bVisible": false},
						 null,
						 {"bVisible": false},
						 null,
						 null,
						 null,
						 null,
						 null,
						 null,
						 {"bVisible": false},
				  ],
		"bSort":true,
		"bProcessing": true,
		"bServerSide": true,		
		"sAjaxSource": "rekanan_json/daftar_rekanan_valid_json",	
		"lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],	
		columnDefs: [{ className: 'never', targets: [ 0, 2, 6, 9 ] },{ className: 'text-center', targets: [ 7, 8 ] }]
		});
		oTable.fnSort( [ [0,'desc'] ] );
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
				  anSelectedNama = element[3];
				  anSelectedIdU = element[9];
		  });
		  
		  $('#btnAdd').on('click', function () {
			  openAdd("main/loadUrl/main/master_daftar_user_non_rekanan_add");
			  $('div.flexmenumobile').hide()
			  $('div.flexoverlay').css('display', 'none')
	
		  });
		  
		  $('#btnEdit').on('click', function () {
			  if(anSelectedData == "")
				  return false;				
			  openAdd("main/loadUrl/main/master_daftar_user_non_rekanan_add/?reqId="+anSelectedId);
			  $('div.flexmenumobile').hide()
			  $('div.flexoverlay').css('display', 'none')
		  });
	
		  $('#btnDelete').on('click', function () {
				if(anSelectedData == "")
					  return false;	
				deleteData("perusahaan_json/delete/", anSelectedId);
		  });
		  
		  $('#btnCetak').on('click', function () {
				newWindow = window.open("main/loadUrl/report/vms_daftar_penyedia_terverifikasi", 'Cetak');
				newWindow.focus();
		  });
		  
		   $('#btnLihatDetil').on('click', function () {
			  if(anSelectedData == "") {
			  	  alertError3('Pilih data dahulu');
				  return false;				
			  }
			  openAdd("main/loadUrl/main/data_rekanan/?reqId="+anSelectedId);
				
			  // tutup flex dropdown => untuk versi mobile
			  $('div.flexmenumobile').hide()
			  $('div.flexoverlay').css('display', 'none')
		  });
		  
		  $('#btnReset').on('click', function () 
		  {
			if(anSelectedData == "")
				  return false;	
			  $.messager.prompt('Reset Password', 'Masukkan password baru :', function(r){
					if (r){
						$.get( "users_base_json/reset_password_daftar_user_non_rekanan/?reqPassword="+r+"&reqId="+ anSelectedId, function( data ) {
						   $.messager.alert('Info', data, 'info');
						});
					}
				});
		  });
		  
		  $('#btnLihatDetil').on('click', function () {
			  if(anSelectedData == "")
				  return false;				
			  openAdd("main/loadUrl/main/data_rekanan/?reqId="+anSelectedId);
				
			  // tutup flex dropdown => untuk versi mobile
			  $('div.flexmenumobile').hide()
			  $('div.flexoverlay').css('display', 'none')
		  });
				
		 //  $('#btnUbahStatus').on('click', function () 
			// {
			//   if(anSelectedData == "")
			// 		return false;	
			//   $.messager.confirm('Konfirmasi',"Apakah anda ingin mengubah status?",function(r){
			// 	  if (r){
			// 		  $.getJSON("users_base_json/ubah_status/?reqId="+anSelectedId,
			// 			function(data){
							
			// 					$.messager.alert('Info', data.PESAN, 'info');
			// 					oTable.fnReloadAjax("users_base_json/master_daftar_rekanan_non_json");
			// 		  });				
			// 	  }
			//   });
		 //  });	    

		  $('#btnUbahStatus').on('click', function () 
		  {
			if(anSelectedData == "")
			{
		  		alertError3("Pilih data dahulu");
			 	return false;				
		  	}
			$.messager.confirm('Konfirmasi',"Apakah anda ingin mengubah status?",function(r){
				if (r){
					$.getJSON("users_base_json/ubah_status2/?reqId="+anSelectedId,
					  function(data){
						  
							  $.messager.alert('Info', data.PESAN, 'info');
							  oTable.fnReloadAjax("rekanan_json/daftar_rekanan_valid_json");
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
					$.getJSON("users_base_json/ubah_status_aktif/?reqId="+anSelectedIdU,
					  function(data){
						  
							  $.messager.alert('Info', data.PESAN, 'info');
							  oTable.fnReloadAjax("rekanan_json/daftar_rekanan_valid_json");
					});				
				}
			});
		});	       
} );


function reloadMonitoring()
{
	oTable.fnReloadAjax("users_base_json/master_daftar_rekanan_non_json");	
}

</script>

<div class="row"> 
  <div class="col-md-12 col-sm-12">
    <div class="card">
      <div class="card-header card-head-inverse bg-primary">
        <h4 class="card-title text-white">Daftar <?= LABEL_PENYEDIA ?> Terverifikasi</h4>
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

        	<div class="row" id="sticker">
			     <div class="form-group col-md-12 mb-2">
			      <!-- <a href="main/index/daftar_rekanan_belum_valid" class="btn btn-info text-white"> <span class="fa fa-ban"></span> Belum Verifikasi </a> -->
			      <!-- <a href="main/index/daftar_rekanan_valid" class="btn btn-primary text-white"> <span class="fa fa-check-square-o"></span> Verifikasi </a> -->
			      <a id="btnLihatDetil" title="Lihat Detil" class="<?= CLASS_BTN_SUCCESS ?>"><span class="fa fa-eye"></span> Lihat Detil</a>
          	<a id="btnCetak" title="Cetak" class="<?= CLASS_BTN_INFO ?>"> <i class="fa fa-print"></i> Cetak</a>
			     	<?php 
		      	if ($this->USER_TYPE_ID == '2') {?>
	          <a id="btnUbahStatus" class="<?= CLASS_BTN_PRIMARY ?>" title="Ubah Status User"><span class="fa fa-pencil"></span> Ubah Status Validasi</a>
	          <a id="btnUbahStatusAktif" class="<?= CLASS_BTN_DANGER ?>" title="Ubah Status User"><span class="fa fa-pencil"></span> Ubah Status Aktif</a>
			      <!-- <a href="main/index/daftar_rekanan_hapus" class="btn btn-danger text-white"> <span class="fa fa-trash"></span> Hapus </a> -->
			      <?php 
			      } ?>
			     </div> 
			    </div>
          <table id="example" class="border-double table mb-0 table-bordered">
			      <thead>
	            <tr>
                <th>Id</th>
                <th width="6%">No Registrasi</th>
                <th>SAP Kode</th>
                <th width="32%">Nama</th>
                <th width="15%">Kota</th>
                <th width="15%">Tanggal</th>
                <!-- <th width="15%">Tanggal Validasi</th> -->
                <th width="15%">Validator</th>
                <th width="5%" style="text-align: center">Status <br> Validasi</th>
                <th width="5%" style="text-align: center">Status <br> Aktif</th>
                <th>Id</th>
	            </tr>       
		       	</thead>
          </table>   
            
        </div>
      </div>
    </div>
  </div> 
</div>    
