<?php
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 * 
 */
$reqId = httpFilterRequest("reqId");
$this->libsession->cekSession($reqId);

$this->load->library("paketinfo"); $paketInfo = new paketinfo();
$this->load->library("libapiui"); $libapiui = new libapiui();
include_once("functions/string.func.php");
include_once("functions/default.func.php");
include_once("functions/date.func.php");
$this->load->model(array("PaketRekanan","Paket","PaketNegoisasi","RekananPaketPenawaran","Rekanan","PaketNegosiasiValidasi","PaketTahap"));
$paket_tahap = new PaketTahap();
$paket_tahap_metode = new PaketTahap();
$jenis_tahap = $paket_tahap_metode->getJenisTahapById($reqId);
$arrNegosiasi   = NEGOSIASI;
$aktif_negosiasi = $paket_tahap->getCountByParamsAktif(array("URUT" => $arrNegosiasi[$jenis_tahap], "PAKET_ID" => $reqId, "TAMPILKAN" => 1));
$aktif_negosiasi2 = $paket_tahap->getCountByParamsBerlalu(array("URUT" => $arrNegosiasi[$jenis_tahap], "PAKET_ID" => $reqId, "TAMPILKAN" => 1));

if($aktif_negosiasi > 0 || $aktif_negosiasi2 < 1)
{
    $aktif_negosiasi =1;  
} else {
    $aktif_negosiasi =0;    
}
?>
<script type="text/javascript" src="lib/eproc/allfunc.js"></script>
<link rel="stylesheet" type="text/css" href="lib/DataTables-1.10.7/media/css/jquery.dataTables.css">
<link rel="stylesheet" type="text/css" href="lib/DataTables-1.10.7/extensions/Responsive/css/dataTables.responsive.css">
<link rel="stylesheet" type="text/css" href="lib/DataTables-1.10.7/examples/resources/syntax/shCore.css">
<link rel="stylesheet" type="text/css" href="lib/DataTables-1.10.7/examples/resources/demo.css">
<script type="text/javascript" language="javascript" src="lib/DataTables-1.10.7/media/js/jquery.dataTables.js"></script>
<script type="text/javascript" language="javascript" src="lib/DataTables-1.10.7/extensions/Responsive/js/dataTables.responsive.js"></script>
<script type="text/javascript" language="javascript" src="lib/DataTables-1.10.7/examples/resources/syntax/shCore.js"></script>
<script type="text/javascript" language="javascript" src="lib/DataTables-1.10.7/examples/resources/demo.js"></script>
<link rel="stylesheet" type="text/css" href="<?=base_url() ?>assets/new/vendors/css/extensions/zoom.css">
<script src="<?=base_url()?>assets/new/vendors/js/extensions/zoom.min.js"></script>

<script type="text/javascript" language="javascript" class="init">
var oTable;
$(document).ready(function() {

  oTable = $('#example').dataTable({ bJQueryUI: true,"iDisplayLength": 10,
    /* UNTUK MENGHIDE KOLOM ID */
    "aoColumns": [
             {"bVisible": false},null,null,null,
             null,null,null,null,null,null,null,
             null,null,null,null
          ],
    "bSort":true,
    "bProcessing": true,
    "bServerSide": true,
    "sAjaxSource": "negosiasi_item_rekanan_json/json?reqId=<?= $reqId ?>",
    "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
    columnDefs: [{ className: 'never', targets: [0,1,4,6,14] },{ className: 'text-center', targets: [3,4,5,6,7,8,9] }]
    });//.rowGrouping({iGroupingColumnIndex: 0});
    oTable.fnSort( [ [0,'desc'] ] );

    new $.fn.dataTable.Responsive( oTable );
      var anSelectedData = '';
      var anSelectedId = '';
      var anSelectedStatus = '';
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
          anSelectedStatusID = element[15];
      }); 

      $('#btnEdit').on('click', function () {
        if(anSelectedData == "")
        {
          alertError3("Pilih data dahulu");
          return false;
        }
        if (anSelectedStatusID == "1") {
          alertError3("Status Approved tidak bisa diubah");
          return false;
        }
        openAddFrame("main/loadUrl/main/negosiasi_item_form/?reqId="+anSelectedId+"&reqPaketId=<?= $reqId ?>");
        $('div.flexmenumobile').hide()
        $('div.flexoverlay').css('display', 'none')
      });  
} );

function reloadMonitoring()
{
  oTable.fnReloadAjax("negosiasi_item_rekanan_json/json?reqId=<?= $reqId ?>");
}

function reloadMonitoringReload()
{
  location.reload();
}


</script>

<style type="text/css">
.table th { padding: 4px !important; text-align: center; vertical-align: middle; }
</style>

<div class="row">
  <div class="col-md-12 col-sm-12">
    <div class="card">
      <div class="card-header card-head-inverse bg-primary">
        <h4 class="card-title text-white">Negosiasi Item</h4>
        <a class="heading-elements-toggle"><i class="fa fa-ellipsis-v font-medium-3"></i></a>
        <div class="heading-elements">
            <ul class="list-inline mb-0">
              <li><a data-action="expand"><i class="ft-maximize"></i></a></li>
              <!-- <li><a data-action="close"><i class="ft-x"></i></a></li> -->
            </ul>
        </div>
      </div>
      <div class="card-content collapse show border-info border-darken-2">
        <div class="card-body">

          <div class="table-responsive">

            <div class="card-body area-datatable">
            <?php 
            if ($aktif_negosiasi == '0') {
               echo '<div class="alert alert-danger" style="color:#fff">
                        <span style="color: #fff">
                          Negosiasi belum dimulai atau sudah selesai.
                        </span>
                      </div>';
             } ?>

              <div class="row" id="sticker">
                <div class="form-group col-md-12">
                <?php 
                 if ($aktif_negosiasi == '1') {?>
                      <a id="btnEdit" title="Ubah" class="<?= CLASS_BTN_INFO ?>"><span class="fa fa-pencil"></span> Edit Harga Negosiasi </a>
                  <?php 
                  } ?>
                  <a onclick="return reloadMonitoring()" class="<?= CLASS_BTN_DARK ?> pull-right"><span class="fa fa-refresh"></span> Refresh</a>
                </div>
              </div>
              <table id="example" class="border-double table mb-0 table-bordered" style="width: 100%">
                <thead>
                  <tr>
                    <th>PaketNegosiasiItenId</th>
                    <th>PaketId</th>
                    <th style="width: 25%">Uraian</th>
                    <th>Volume</th>
                    <th>Satuan</th>
                    <th>Durasi</th>
                    <th>Satuan</th>
                    <th>Harga Satuan</th>
                    <th>Jumlah <br>Harga Satuan</th>
                    <th>Harga Penawaran</th>
                    <th>Jumlah <br>Harga Penawaran</th>
                    <th>Harga Nego</th>
                    <th>Jumlah <br>Harga Nego</th>
                    <th>Status</th>
                    <th>StatusId</th>
                  </tr>
                </thead>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>



<script type="text/javascript">
 
</script>
