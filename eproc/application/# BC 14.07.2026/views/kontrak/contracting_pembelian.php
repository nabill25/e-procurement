<?php
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 * 
 */

$this->libsession->cekSession();

$getTahun = isset($_GET['tahun']) ? $_GET['tahun'] : 'all';
$this->LEGAL =  $this->kauth->getInstance()->getIdentity()->LEGAL;
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
             null
          ],
    "bSort":true,
    "bProcessing": true,
    "bServerSide": true,
    "sAjaxSource": "contracting_json/contracting_pembeli?reqStatus=0&tahun=<?= $getTahun ?>",
    "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
    columnDefs: [{ className: 'never', targets: [ 0 ] },{ className: 'text-center', targets: [ 3 ] }]
    });
    oTable.fnSort( [ [0,'desc'] ] );
    new $.fn.dataTable.Responsive( oTable );

      var anSelectedData = '';
      var anSelectedId = '';

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
          anSelectedNilaiRep = anSelectedNilai.replace(/\./g,'');
      });

      $('#btnEdit').on('click', function () {
        if(anSelectedData == "")
        {
          alertError3("Pilih data dahulu");
        return false;
          }
        location.href = "main/index/paket_detil_kontrak/?reqId="+anSelectedId;

        // tutup flex dropdown => untuk versi mobile
        $('div.flexmenumobile').hide()
        $('div.flexoverlay').css('display', 'none')
      });

      $('#reqStatus').combobox({
      onSelect: function(param){
        oTable.fnReloadAjax("contracting_json/contracting_pembeli?reqStatus="+param.value);
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

    <div class="col-md-12 col-sm-12">
      <div class="card border-bottom-primary" style="zoom: 1;">
        <div class="card-header">
          <h4 class="card-title">Kontrak Pembelian</h4>
          <div class="heading-elements" id="tombol">
            <span></span>
          </div>
        </div>
        <div class="card-body area-datatable">
          <div class="form-body">
            <div class="row">
              <div class="form-group col-md-6 mb-2" id="sticker">
                  <?php
                  if ($this->LEGAL != '1') { ?>
                  <a id="btnEdit" class="<?= CLASS_BTN_INFO ?> mr-1" title="Ubah"><span class="fa fa-eye"></span> Lihat </a>
                  <?php
                  } ?>
              </div>
              <div class="form-group col-md-6 mb-2 text-right" id="sticker">
                <label>Metode</label>
                <select name="reqStatus" id="reqStatus" class="easyui-combobox" style="width: 250%">
                  <option value="0">Semua</option>
                  <option value="6">e-Purchasing</option>
                  <option value="12">e-Purchasing Pemerintah</option>
                  <option value="9">Pembelian Langsung Offline</option>
                </select>
              </div>
            </div>
            <div>
              <table id="example" class="display table-bordered" cellspacing="0" width="100%" style="border-bottom: none !important">
                  <thead>
                    <tr>
                      <th width="1px">Id</th>
                      <th style="width: 45%">Paket Pengadaan</th>
                      <th style="width: 15%">Harga Perkiraan</th>
                      <th style="width: 10%">Status</th>
                      <th style="width: 15%">Metode</th>
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
