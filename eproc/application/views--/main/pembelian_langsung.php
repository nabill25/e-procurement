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

  oTable = $('#example').dataTable({ bJQueryUI: true,"iDisplayLength": 10,
    /* UNTUK MENGHIDE KOLOM ID */
    "aoColumns": [
             {"bVisible": false},
             null,
             null,
             null,
             null,
             null
          ],
    "bSort":true,
    "bProcessing": true,
    "bServerSide": true,
    "sAjaxSource": "katalog_json/json_pejabat",
    "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
		columnDefs: [{ className: 'never', targets: [ 0,5 ] }]
		});//.rowGrouping({iGroupingColumnIndex: 0});

    new $.fn.dataTable.Responsive( oTable );

      var anSelectedData = '';
      var anSelectedId = '';
      var anSelectedDownload = '';
      var anSelectedPosition = '';
      var anSelectedKodePR = '';
      var anSelectedKey = '';

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
          anSelectedKey = element[6];
      });

      $('#btnEdit').on('click', function () {
        if(anSelectedData == "")
        {
          alertError3("Pilih data dahulu");
          return false;
        }
        location.href = "main/index/paket_detil/?eid="+anSelectedId+'&key='+anSelectedKey;
        $('div.flexmenumobile').hide()
        $('div.flexoverlay').css('display', 'none')
      });

} );

function reloadMonitoring()
{
  oTable.fnReloadAjax("users_base_json/master_daftar_rekanan_non_json");
}
</script>

<section id="backColor">
  <div class="row">
    <div class="col-md-12 col-sm-12">
      <div class="card border-bottom-primary" style="zoom: 1;">
        <div class="card-header">
          <h4 class="card-title">Pembelian <small> Katalog</small></h4>
          <div class="heading-elements" id="tombol">
            <span></span>
          </div>
        </div>
        <div class="card-body area-datatable">
          <div class="form-body">
            <div class="row">
              <div class="form-group col-md-12 mb-2">
                <a id="btnEdit" class="<?= CLASS_BTN_INFO ?>" title="Ubah"><span class="fa fa-eye"></span> Lihat</a>
              </div>
            </div>
              <table id="example" class="border-double table mb-0 table-bordered" style="width: 100%">
                <thead>
                  <tr>
                    <th width="1px">Id</th>
                    <th style="width: 35%">Pembelian Katalog</th>
                    <th style="width: 25%">Harga Perkiraan</th>
                    <th style="width: 15%">Status</th>
                    <th style="width: 10%">Kode PR</th>
                    <th style="width: 10%">UUID</th>
                  </tr>
                </thead>
              </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>
