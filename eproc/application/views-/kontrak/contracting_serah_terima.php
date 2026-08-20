<?php 
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 * 
 */

$this->libsession->cekSession();    
// ECHO $this->LEVEL_KONTRAK.'--'.$this->USER_TYPE_ID; DIE;
if ($this->LEVEL_KONTRAK != '1' && $this->LEVEL_KONTRAK != '3' && $this->USER_TYPE_ID != '20' && $this->USER_TYPE_ID != '28') { // Khusus Penyelesai
  redirect(base_url().'main/index/403');
  
}
$getTahun = isset($_GET['tahun']) ? $_GET['tahun'] : 'all'; 
?>
<link rel="stylesheet" type="text/css" href="lib/DataTables-1.10.7/media/css/jquery.dataTables.css">
<link rel="stylesheet" type="text/css" href="lib/DataTables-1.10.7/extensions/Responsive/css/dataTables.responsive.css">
<link rel="stylesheet" type="text/css" href="lib/DataTables-1.10.7/examples/resources/syntax/shCore.css">
<link rel="stylesheet" type="text/css" href="lib/DataTables-1.10.7/examples/resources/demo.css">
<script type="text/javascript" language="javascript" src="lib/DataTables-1.10.7/media/js/jquery.dataTables.js"></script>
<script type="text/javascript" language="javascript" src="lib/DataTables-1.10.7/extensions/Responsive/js/dataTables.responsive.js"></script>
<script type="text/javascript" language="javascript" src="lib/DataTables-1.10.7/examples/resources/syntax/shCore.js"></script>
<!-- <script type="text/javascript" language="javascript" src="lib/DataTables-1.10.7/examples/resources/demo.js"></script> -->
<script type="text/javascript" language="javascript" class="init">
var oTable; 
$(document).ready(function() {
           
  oTable = $('#example').dataTable({ bJQueryUI: true,"iDisplayLength": 10,
  /* UNTUK MENGHIDE KOLOM ID */
  "aoColumns": [
           {"bVisible": false},null,null,null,null,null,
           null,null,null,null,null, null,null,null
        ],
  "bSort":true,
  "bProcessing": true,
  "bServerSide": true,    
  "sAjaxSource": "contracting_rekanan_json/jsonPenyelesaian?reqProses=3",  
  "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],  
  columnDefs: [{ className: 'never', targets: [ 0,1,3,4,5,8,9] }]
  });
  
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
      anSelectedId = element[0]; // paketId
      anSelectedKontrakId = element[1]; // kontrakId
      anSelectedPICPenyelesaian = element[11]; // PIC Penyelesaian
  }); 

  $('#btnView').on('click', function () {
    if(anSelectedId == "")
    {
      alertError3("Pilih data dahulu");
      return false;
    }

    if (anSelectedPICPenyelesaian == '-') {
      alertError3("PIC belum ditunjuk");
      return false;
    }

    location.href = "kontrak/index/contracting_detail?reqId="+anSelectedKontrakId;

    $('div.flexmenumobile').hide()
    $('div.flexoverlay').css('display', 'none')
  });

  $('#btnTunjukPIC').on('click', function () {
    if(anSelectedId == "") { alertError3("Pilih data dahulu"); return false; }

    openAdd("kontrak/loadUrl/kontrak/contracting_penunjukan_pic_penyelesaian/?reqId="+anSelectedId+"&reqContractId="+anSelectedKontrakId);
    // tutup flex dropdown => untuk versi mobile
    $('div.flexmenumobile').hide()
    $('div.flexoverlay').css('display', 'none')
  });
}); 

function reloadMonitoring()
{
  oTable.fnReloadAjax("contracting_rekanan_json/jsonPenyelesaian?reqProses=3");
}

</script>

<style type="text/css">
  .card-text a { font-size: 11px; }
  a:hover { text-decoration: none; }
</style>

<section id="backColor">
  <div class="row">   

    <div class="col-md-12 col-sm-12">
      <div class="card border-bottom-primary" style="zoom: 1;"> 
        <div class="card-header">
          <h4 class="card-title">PENYELESAIAN <small> KONTRAK</small></h4>
          <div class="heading-elements" id="tombol"> 
            <!-- <a href="kontrak/index/contracting_dashboard?tahun=<?= $getTahun ?>" class="<?= CLASS_BTN_DANGER ?>"> <i class="fa fa-arrow-left"></i> Kembali </a>  -->
          </div>
        </div>
        <div class="card-body area-datatable"> 
          <div class="form-body"> 
            <div class="row">
              <div class="form-group col-md-12 mb-2">
                <a id="btnView" class="<?= CLASS_BTN_SUCCESS ?> text-white" title="Lihat Kontrak"><span class="fa fa-eye"></span> Lihat Kontrak </a>
                <?php 
                if ($this->PENUNJUK_PIC == '1' && $this->LEVEL_KONTRAK == '3') {  ?>
                <a id="btnTunjukPIC" title="Tunjuk PIC" class="btn round btn-min-width box-shadow-1 btn-danger text-white"><i class="fa fa-flag-o"></i> Tunjuk PIC</a>
                <?php 
                } ?>
              </div>
            </div>
            <div>
              <table id="example" class="display table-bordered" cellspacing="0" width="100%" style="border-bottom: none !important">
                <thead>
                  <tr>
                    <th width="1px">Id</th> <!-- PaketID -->
                    <th width="1px">Id</th> <!-- contracRekID -->
                    <th style="width: 45%">Paket Pengadaan</th>
                    <!-- <th style="width: 15%">Nilai</th> -->
                    <th style="width: 10%">Metode Pengadaan</th>    
                    <th style="width: 10%">Metode ID</th>    
                    <th style="width: 10%">User</th>    
                    <th style="width: 10%">Penyedia</th>   
                    <th style="width: 10%">Nilai Kontrak</th>      
                    <th style="width: 10%">Tanggal BAST</th>    
                    <th style="width: 10%">Termin</th>   
                    <th style="width: 10%">Jenis Kontrak</th>
                    <th style="width: 10%">PIC Penyelesai</th>
                    <th style="width: 10%">Tahap</th>
                    <th style="width: 10%">Status</th>
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