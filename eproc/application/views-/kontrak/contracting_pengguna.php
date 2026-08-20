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
<!-- <script type="text/javascript" language="javascript" src="lib/DataTables-1.10.7/examples/resources/demo.js"></script> -->
<script type="text/javascript" language="javascript" class="init">
var oTable;
$(document).ready(function() {
  oTable = $('#example').dataTable({ bJQueryUI: true,"iDisplayLength": 10,
    "aoColumns": [
             {"bVisible": false},null,null,null,null,null,null
          ],
    "bSort":true,
    "bProcessing": true,
    "bServerSide": true,
    "sAjaxSource": "contracting_json/penilaianPengguna",
    "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
    columnDefs: [{ className: 'never', targets: [ 0,1 ] },{ className: 'text-center', targets: [ 6 ] }]
    // columnDefs: [{ className: 'never', targets: [] }]
    });
    oTable.fnSort( [ [0,'desc'] ] );

    new $.fn.dataTable.Responsive( oTable );
      /* RIGHT CLICK EVENT */
      var anSelectedData = '';
      var anSelectedId = '';
      var anSelectedDownload = '';

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
          anSelectedIdRek = element[1];
          anSelectedApprovalPICUnit = element[6];
      });

      $('#btnView').on('click', function () {
        if(anSelectedData == "")
        {
          alertError3("Pilih data dahulu");
          return false;
        }
        if(anSelectedApprovalPICUnit == '<span class="badge badge-primary"><a class="fa fa-check"></a></span>') { alertError3("Penilaian sudah disetujui"); return false; }
        
        location.href = "kontrak/index/contracting_penilaian_tambah/?reqId="+anSelectedId+"&reqRekananId="+anSelectedIdRek;
        $('div.flexmenumobile').hide()
        $('div.flexoverlay').css('display', 'none')
      });

      $('#btnCetak').on('click', function () {
        if(anSelectedData == "")
        {
          alertError3("Pilih data dahulu");
          return false;
        }
        // location.href = "main/loadUrl/report/paket_penilaian_pdf/?reqId="+anSelectedId+"&pemenang="+anSelectedIdRek; 
        window.open(
            "main/loadUrl/report/paket_penilaian_pdf/?reqId=" + anSelectedId + "&pemenang=" + anSelectedIdRek,
            "_blank"
        );
        $('div.flexmenumobile').hide()
        $('div.flexoverlay').css('display', 'none')
      });

      $('#btnApprove').on('click', function (){ 
        if(anSelectedData == "") { alertError3("Pilih data dahulu"); return false; }
        if(anSelectedApprovalPICUnit == '<span class="badge badge-primary"><a class="fa fa-check"></a></span>') { alertError3("Penilaian sudah disetujui"); return false; }

        $.messager.confirm('Konfirmasi',"Setujui penilaian ini?",function(r){
          if (r){ 
            $.post("contracting_json/approvalPICUnit", 
              { reqId: anSelectedId, status: '1' }, 
              function(data){
                  $.messager.alert('Info', data, 'info');
                  oTable.fnReloadAjax("contracting_json/penilaianPengguna");
              }
            );
          }
        });
      });
} );


function reloadMonitoring()
{
  oTable.fnReloadAjax("contracting_json/penilaianPengguna");
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
          <h4 class="card-title">Penilaian</small></h4>
          <div class="heading-elements" id="tombol">
            <span></span>
          </div>
        </div>
        <div class="card-body area-datatable">
          <div class="form-body">
            <div class="row">
              <div class="form-group col-md-12 mb-2">
                <?php 
                if ($this->LEVEL_PENGGUNA == '1') { ?>
                  <a id="btnView" class="<?= CLASS_BTN_SUCCESS ?> text-white" title="Lihat Kontrak"><span class="fa fa-pencil"></span> Ubah Penilaian</a>
                  <a id="btnApprove" class="<?= CLASS_BTN_INFO ?> text-white" title="Lihat Kontrak"><span class="fa fa-gavel"></span> Setujui</a>
                <?php 
                } else { ?>
                  <a id="btnView" class="<?= CLASS_BTN_SUCCESS ?> text-white" title="Lihat Kontrak"><span class="fa fa-eye"></span> Lakukan Penilaian</a>
                <?php 
                } ?>
                <a id="btnCetak" class="<?= CLASS_BTN_PRIMARY ?> text-white" title="Cetak"><span class="fa fa-print"></span> Cetak </a>
              </div>
            </div>
            <div>
              <table id="example" class="display table-bordered" cellspacing="0" width="100%" style="border-bottom: none !important">
                <thead>
                  <tr>
                    <th width="1px">ContractId</th>
                    <th>RekananId</th>
                    <th style="width: 40%">Paket Pengadaan</th>
                    <th style="width: 10%">Nilai Kontrak</th>
                    <th style="width: 20%">Jenis Kontrak</th>
                    <th style="width: 15%">Penyedia</th>    
                    <th style="width: 5%">Approval</th>    
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
