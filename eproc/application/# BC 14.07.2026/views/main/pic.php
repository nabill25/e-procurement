<?php
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 * 
 */
 ?>

 <link rel="stylesheet" type="text/css" href="lib/DataTables-1.10.7/media/css/jquery.dataTables.css">
<link rel="stylesheet" type="text/css" href="lib/DataTables-1.10.7/extensions/Responsive/css/dataTables.responsive.css">
<link rel="stylesheet" type="text/css" href="lib/DataTables-1.10.7/examples/resources/syntax/shCore.css">
<link rel="stylesheet" type="text/css" href="lib/DataTables-1.10.7/examples/resources/demo.css">
<style type="text/css" class="init">

div.container { max-width: 100%;}

</style>
<!--<script type="text/javascript" language="javascript" src="lib/DataTables-1.10.7/media/js/jquery.js"></script>-->
<script type="text/javascript" language="javascript" src="lib/DataTables-1.10.7/media/js/jquery.dataTables.js"></script>
<script type="text/javascript" language="javascript" src="lib/DataTables-1.10.7/extensions/Responsive/js/dataTables.responsive.js"></script>
<script type="text/javascript" language="javascript" src="lib/DataTables-1.10.7/examples/resources/syntax/shCore.js"></script>
<script type="text/javascript" language="javascript" src="lib/DataTables-1.10.7/examples/resources/demo.js"></script>

<script type="text/javascript" language="javascript" class="init">
var oTable; 
$(document).ready(function() {
	           
	oTable = $('#example').dataTable({ bJQueryUI: true,"iDisplayLength": 50,
		/* UNTUK MENGHIDE KOLOM ID */
		"aoColumns": [
					     {"bVisible": false},
						 null,
						 null
				  ],
		"bSort":true,
		"bProcessing": true,
		"bServerSide": true,		
		"sAjaxSource": "unit_kerja_json/unit_kerja_pic_json",	
		"lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],	
		columnDefs: [{ className: 'never', targets: [ 0 ] }]
		});//.rowGrouping({iGroupingColumnIndex: 0});
		
		new $.fn.dataTable.Responsive( oTable );
		
		/* Click event handler */
	
		  /* RIGHT CLICK EVENT */
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
		  });
		  
		  $('#btnAdd').on('click', function () {
			  openAdd("main/loadUrl/main/master_daftar_user_non_rekanan_add");
			  
			  // tutup flex dropdown => untuk versi mobile
			  $('div.flexmenumobile').hide()
			  $('div.flexoverlay').css('display', 'none')
	
		  });
		  
		  $('#btnEdit').on('click', function () {
			  if(anSelectedData == "")
				  return false;				
			  openAdd("main/loadUrl/main/pic_add/?reqId="+anSelectedId);
				
			  // tutup flex dropdown => untuk versi mobile
			  $('div.flexmenumobile').hide()
			  $('div.flexoverlay').css('display', 'none')
		  });
	
		  $('#btnDelete').on('click', function () {
				if(anSelectedData == "")
					  return false;	
				deleteData("perusahaan_json/delete/", anSelectedId);
		  });
		  
		  $('#btnCetakPdf').on('click', function () {
			  newWindow = window.open("main/loadUrl/report/pelanggan_cetak_pdf/", 'Cetak');
			  newWindow.focus();
		  });
		  
		  $('#btnCetakExcel').on('click', function () {
			  newWindow = window.open("main/loadUrl/report/pelanggan_cetak_excel/", 'Cetak');
			  newWindow.focus();
		  });
		  
		  $('#btnReset').on('click', function () {
			if(anSelectedData == "")
				  return false;	
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
} );


function reloadMonitoring()
{
	oTable.fnReloadAjax("unit_kerja_json/unit_kerja_pic_json");	
}

</script>


<div class="row">
    <div class="col-md-12">
    	<div class="area-main">
        	<div class="judul-halaman">PIC</div>
            <div class="inner area-datatable">
            	<div class="area-konten">
                    <div class="area-konten-inner">
                    	<div id="sticker">
                            <div class="area-menu-aksi">
                                <a id="btnEdit" title="Ubah"> Edit</a>
                            </div>
                        </div>
                        
                        <section>
                          <table id="example" class="display nowrap" cellspacing="0" width="100%">
                              <thead>
                                    <tr>
                                        <th width="1px">Id</th>
                                        <th width="1px">Nama Unit</th>
                                        <th width="70px">Jumlah</th>
                                    </tr>       
                                </thead>
                            </table>
                        </section>
                    </div>
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
