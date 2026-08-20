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
             null,
             null,
             null
          ],
    "bSort":true,
    "bProcessing": true,
    "bServerSide": true,    
    "sAjaxSource": "contracting_json/json",  
    "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],  
    columnDefs: [{ className: 'never', targets: [ 0 ] }]
    });
    
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
      
      $('#btnEdit').on('click', function () {
        if(anSelectedData == "")
        {
          alertError3("Pilih data dahulu");
        return false;       
          }     
        location.href = "main/index/katalog_rekanan_add/?reqId="+anSelectedId;
        
        // tutup flex dropdown => untuk versi mobile
        $('div.flexmenumobile').hide()
        $('div.flexoverlay').css('display', 'none')
      });
  
      $('#btnDelete').on('click', function () {
        if(anSelectedData == "")
        {
            alertError3("Pilih data dahulu");
          return false;       
          }   
        deleteData("katalog_json/delete/", anSelectedId);
      });  
} );


function reloadMonitoring()
{
  oTable.fnReloadAjax("users_base_json/master_daftar_rekanan_non_json");  
}

</script>

<style type="text/css">
  .card-text a {
    font-size: 11px;
  }
</style>

<section id="backColor">
  <div class="row">   

    <div class="col-md-12 col-sm-12">
      <div class="card border-bottom-primary" style="zoom: 1;"> 
        <div class="card-header">
          <h4 class="card-title">Paket <small> Pengadaan</small></h4>
          <div class="heading-elements" id="tombol"> 
            <span></span>
          </div>
        </div>
        <div class="card-body area-datatable"> 
          <div class="form-body">
            <ul class="nav nav-tabs process-model more-icon-preocess" role="tablist">
              <li role="presentation" style="width: 20% !important;" class="active">
                <a href="main/index/evaluasi_penawaran_administrasi_sampul1">
                  <i class="fa fa-cogs" aria-hidden="true"></i> 
                  <p> <span class="badge badge-primary" style="font-size: 1.3em">16</span><br> Persiapan Kontrak</p>
                </a>
              </li> 
              <li role="presentation" style="width: 20% !important;">
                <a href="main/index/evaluasi_penawaran_administrasi_sampul1">
                  <i class="fa fa-flag" aria-hidden="true"></i> 
                  <p> <span class="badge badge-success" style="font-size: 1.3em">8</span><br> Pelaksanaan Awal Kontrak</p>
                </a>
              </li> 
              <li role="presentation" style="width: 20% !important;">
                <a href="main/index/evaluasi_penawaran_administrasi_sampul1">
                  <i class="fa fa-handshake-o" aria-hidden="true"></i> 
                  <p> <span class="badge badge-info" style="font-size: 1.3em">5</span><br> Pengelolaan Kontrak</p>
                </a>
              </li> 
              <li role="presentation" style="width: 20% !important;">
                <a href="main/index/evaluasi_penawaran_administrasi_sampul1">
                  <i class="fa fa-window-close" aria-hidden="true"></i> 
                  <p> <span class="badge badge-danger" style="font-size: 1.3em">0</span><br> Permasalahan Kontrak</p>
                </a>
              </li> 
              <li role="presentation" style="width: 20% !important;">
                <a href="main/index/evaluasi_penawaran_administrasi_sampul1">
                  <i class="fa fa-check" aria-hidden="true"></i> 
                  <p> <span class="badge badge-dark" style="font-size: 1.3em">52</span><br> Serah Terima</p>
                </a>
              </li> 
            </ul> 
            <hr>
            <div class="row">
              <div class="form-group col-md-12 mb-2"> 
                  <a href="main/index/contracting_dashboard" class="btn btn-danger mr-1 text-white"> <i class="fa fa-arrow-left"></i> Kembali </a> 
                  <a id="btnEdit" class="btn btn-info text-white" title="Ubah"><span class="fa fa-eye"></span> Lihat</a>
              </div> 
            </div>
            <div>
              <table id="example" class="border-double table mb-0" class="table-responsive">
                  <thead>
                        <tr>
                        <th width="1px">Id</th>
                        <th style="width: 45%">Nama</th>
                        <th style="width: 25%">Nilai</th>
                        <th style="width: 15%">Metode Pengadaan</th>    
                        <th style="width: 5%">Jenis Kontrak</th>    
                        </tr>       
                  </thead>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div> 
</section>  