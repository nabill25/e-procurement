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
    "sAjaxSource": "katalog_laporan_json/json",  
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
        // location.href = "main/index/katalog_rekanan_add/?reqId="+anSelectedId;
        openAdd("main/loadUrl/main/katalog_laporan_view/?reqId="+anSelectedId);

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
        deleteData("katalog_laporan_json/delete/", anSelectedId);
      });  

      $('#treeSatker').treegrid({
        onClickRow: function(param){
          $("#tombol span").remove();
          $("#tombol").html('<div><a class="btn btn-primary text-white" onClick="openAdd(\'main/loadUrl/main/master_bidang_usaha_add/?reqParentId='+param.id+'\')" title="Tambah"><span class="fa fa-plus"></span> Tambah</a>&nbsp;&nbsp;<a class="btn btn-info text-white" onClick="openAdd(\'main/loadUrl/main/master_bidang_usaha_add/?reqId='+param.id+'\')" title="Edit"><span class="fa fa-pencil"></span> Edit</a>&nbsp;&nbsp;<a class="btn btn-danger text-white"  onClick="deleteData(\'bidang_usaha_json/delete\','+"'"+param.id+"'"+')" title="Hapus"><span class="fa fa-trash"></span> Hapus</a></div><hr>');      
        }
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
    
    <?php 
    if($this->USER_TYPE_ID != "18" && $this->USER_TYPE_ID != "19") 
    {  ?>
    <div class="col-md-2 col-sm-12">
      <div class="card border-bottom-primary" style="zoom: 1;">  
        <div class="card-body"> 
          <div class="card-text">
           <?php 
            if($this->USER_TYPE_ID == "6") {  ?>
              <a href="<?= base_url() ?>main/index/katalog_rekanan" class="btn btn-success btn-lg mb-1 text-left" style="width: 100%; opacity: .3"><i class="fa fa-cogs fa-lg pull-right"></i> Katalog</a>
              <a href="<?= base_url() ?>main/index/katalog_rekanan_add" class="btn btn-success btn-lg mb-1 text-left" style="width: 100%;"><i class="fa fa-plus fa-lg pull-right"></i> Tambah Katalog</a>
              <a href="" class="btn btn-success btn-lg mb-1 text-left" style="width: 100%"><i class="fa fa-shopping-basket fa-lg pull-right"></i> Penawaran</a>
            <?php 
            } ?> 

            <?php 
            if($this->USER_TYPE_ID == "1" || $this->USER_TYPE_ID == "2"){  // 1:admin, 2:validator?>
              <a href="<?= base_url() ?>main/index/katalog_rekanan" class="btn btn-success btn-lg mb-1 text-left" style="width: 100%;"><i class="fa fa-cogs fa-lg pull-right"></i> Katalog</a>
              <a href="<?= base_url() ?>main/index/katalog_validasi" class="btn btn-success btn-lg mb-1 text-left" style="width: 100%;"><i class="fa fa-edit fa-lg pull-right"></i> Validasi</a>
              <a href="<?= base_url() ?>main/index/katalog_laporan" class="btn btn-success btn-lg mb-1 text-left" style="width: 100%; opacity: .3"><i class="fa fa-flag fa-lg pull-right"></i> Laporan</a>
            <?php 
            } ?> 
          </div>
        </div>
      </div>
    </div> 
    <?php 
    }  
     
    if($this->USER_TYPE_ID != "18" && $this->USER_TYPE_ID != "19") 
    { 
    ?> 
    <div class="col-md-10 col-sm-12">
    <?php 
    } else {
    ?>
    <div class="col-md-12 col-sm-12">
    <?php  
    }
    ?>
      <div class="card border-bottom-primary" style="zoom: 1;"> 
        <div class="card-header">
          <h4 class="card-title">Katalog <small> Laporan</small></h4>
          <div class="heading-elements" id="tombol"> 
            <span></span>
          </div>
        </div>
        <div class="card-body area-datatable"> 
          <div class="form-body">
            <div class="row" id="sticker">
              <div class="form-group col-md-12 mb-2"> 
                  <a id="btnEdit" class="<?= CLASS_BTN_INFO ?> mr-1" title="Ubah"><?= BTN_LIHAT ?></a>
                  <?php 
                  if($this->USER_TYPE_ID != "18" && $this->USER_TYPE_ID != "19") 
                  { ?>
                  <a id="btnDelete" class="<?= CLASS_BTN_DANGER ?>" title="Hapus"><?= BTN_HAPUS ?></a>
                  <?php 
                  } ?>
              </div> 
            </div>
            <div>
              <!-- <table id="example" class="border-double table mb-0" class="table-responsive"> -->
              <table id="example" class="border-double table mb-0 table-bordered" style="width: 100%">
                  <thead>
                        <tr>
                        <th width="1px">Id</th>
                        <th>Nama</th>
                        <th>Alasan</th>
                        <th>Jenis Laporan</th>    
                        <th>Katalog</th>    
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