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
$this->load->model(array("PaketRekanan","Paket","PaketNegoisasi","RekananPaketPenawaran","Rekanan","PaketNegosiasiValidasi"));

$paket = new Paket();
$paket->selectByParams(array("PAKET_ID" => $reqId));
$paket->firstRow();
$reqKodePR = $paket->getField("KODE_PR");
$reqKodeRUP = $paket->getField("KODE_RUP");
$reqKodeSA = $paket->getField("KODE_SA");

$prLines = $libapiui->getPRLines($reqKodeSA,$reqKodePR);
// echo "<pre>"; print_r($prLines);
// [ORG_ID] => 101
// [ORG_NAME] => Pusat Administrasi Universitas
// [REQUISITION_HEADER_ID] => 346859
// [PR_NO] => 4427
// [PR_DESC] => Jasa Pembasmian Kelelawar Gedung PAU dan Pengendalian Hama Terpadu Gedung di Bawah Pengelolaan PAU Tahun 2025
// [CURRENCY_CODE] => IDR
// [RATE_TYPE] => 
// [RATE_DATE] => 
// [RATE] => 
// [PR_CREATION_DATE] => 2025-01-03
// [AUTHORIZATION_STATUS] => APPROVED
// [PREPARER_NAME] => Gerryaldo Aulia Yudhantara,
// [LINE_NUM] => 1
// [LINE_DESCRIPTION] => Jasa Pest Control/Rodent Control/Hama Lainnya
// [TOTAL_WITHOUT_TAX] => 10250000
// [RECOVERABLE_TAX] => 0
// [NONRECOVERABLE_TAX] => 1127500
// [TOTAL_WITH_TAX] => 11377500
// [NOTE_TO_BUYER] => Gd. PMB
// [KODE_MASTER_ITEM] => 3160004
// [NAMA_MASTER_ITEM] => Jasa Pest Control/Rodent Control/Hama Lainnya
// [SA] => 15101
// [SA_DESC] => PAU
// [DPSJ] => 51140500
// [DPSJ_DESC] => Direktorat Operasi, Pemeliharaan Fasilitas, dan Manajemen Aset
// [DANA] => 51
// [DANA_DESC] => Dana Masyarakat (BP) - Tidak Terikat
// [AKUN] => 723219
// [AKUN_DESC] => Beban Pemeliharaan Lingkungan
?>
<script type="text/javascript" src="lib/eproc/allfunc.js"></script>
<script type="text/javascript">
<script type="text/javascript" language="javascript" class="init">
var oTable;
$(document).ready(function() {

  oTable = $('#example').dataTable({ bJQueryUI: true,"iDisplayLength": 10,
    /* UNTUK MENGHIDE KOLOM ID */
    "aoColumns": [
             {"bVisible": false},
             null,
             null,
             null,
             null,
          ],
    "bSort":true,
    "bProcessing": true,
    "bServerSide": true,
    "sAjaxSource": "permohonan_paket_usulan_json/files?reqId=<?= $reqId ?>",
    "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
    columnDefs: [{ className: 'never', targets: [0] },{ className: 'text-center', targets: [3,4] }]
    });//.rowGrouping({iGroupingColumnIndex: 0});
    oTable.fnSort( [ [0,'desc'] ] );

    new $.fn.dataTable.Responsive( oTable );
      var anSelectedData = '';
      var anSelectedId = '';
      var anSelectedIdDelete = '';
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
          anSelectedIdDelete = element[1];
      });

       $('#btnAdd').on('click', function () {
        openAddFrame("main/loadUrl/main/permohonan_paket_analisa_files?reqId=<?= $reqId ?>");
        $('div.flexmenumobile').hide()
        $('div.flexoverlay').css('display', 'none')

      });

      $('#btnEdit').on('click', function () {
        if(anSelectedData == "")
        {
          alertError3("Pilih data dahulu");
        return false;
          }
        openAddFrame("main/loadUrl/main/permohonan_paket_analisa_files/?reqFileId="+anSelectedId);
        $('div.flexmenumobile').hide()
        $('div.flexoverlay').css('display', 'none')
      });

      $('#btnDelete').on('click', function () {
        if(anSelectedData == "")
          {
            alertError3("Pilih data dahulu");
          return false;
            }
        deleteData("permohonan_paket_usulan_json/deleteFileAnalisa/", anSelectedId);
      }); 
} );

function reloadMonitoring()
{
  oTable.fnReloadAjax("permohonan_paket_usulan_json/files?reqId=<?= $reqId ?>");
}

function reloadMonitoringReload()
{
  location.reload();
}


</script>
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

            <div class="card"> 
                <div class="card-body area-datatable">
                  <div class="row" id="sticker">
                    <div class="form-group col-md-12 mb-2">
                      <a id="btnAdd" title="Tambah" class="<?= CLASS_BTN_PRIMARY ?>"><span class="fa fa-plus"></span> Import </a>
                      <a id="btnEdit" title="Ubah" class="<?= CLASS_BTN_INFO ?>"><span class="fa fa-pencil"></span> Edit </a>
                      <a id="btnDelete" title="Hapus" class="<?= CLASS_BTN_DANGER ?>"><span class="fa fa-trash"></span> Hard hapus</a>
                    </div>
                  </div>
                  <table id="example" class="border-double table mb-0 table-bordered" style="width: 100%">
                    <thead>
                      <tr>
                        <th>Id</th>
                        <th>Nama Dokumen</th>
                        <th width="10px">File</th>
                        <th width="10px">E-Sign</th>
                        <th width="10px">Share</th>
                      </tr>
                    </thead>
                  </table>
                </div>
            </div>

            <table class="table table-bordered">
              <thead>
                <tr>
                  <th>NO</th>
                  <th>LINE_DESCRIPTION</th>
                  <th>KODE_MASTER_ITEM</th>
                  <th>NAMA_MASTER_ITEM</th>
                  <th>TOTAL_WITH_TAX</th>
                </tr>
              </thead>
            <?php 
            $no=1;
            foreach ($prLines as $key => $value) {
              echo '<tr>';
              echo '<td>'.$no.'</td>';
              echo '<td>'.$value->LINE_DESCRIPTION.'</td>';
              echo '<td>'.$value->KODE_MASTER_ITEM.'</td>';
              echo '<td>'.$value->NAMA_MASTER_ITEM.' ('.$value->NOTE_TO_BUYER.')</td>';
              echo '<td>'.number_format($value->TOTAL_WITH_TAX).'</td>';
              echo '</tr>';
              $no++;
             } ?>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>



<script type="text/javascript">
 
</script>
