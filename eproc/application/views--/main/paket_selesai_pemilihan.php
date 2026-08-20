<?php
$this->libsession->cekSession();
if ($this->USER_TYPE_ID == 27 && $this->LEVEL_PERENCANA != '2') { // Type khusu perencanan dan hanya untuk kasi
 redirect(base_url().'main/index/403');
}
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
    "aoColumns": [
             {"bVisible": false},null,null,null,null,
             null,null,null,null,null,null
          ],
    "bSort":true,
    "bProcessing": true,
    "bServerSide": true,
    "sAjaxSource": "contracting_json/contracting_paket",
    "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
    columnDefs: [{ className: 'never', targets: [ 0,1,5,6,7,9,10 ] }]
    // columnDefs: [{ className: 'never', targets: [] }]
    });

    new $.fn.dataTable.Responsive( oTable );
      var anSelectedData = '';
      var anSelectedId = '';
      var anSelectedDownload = '';
      var anSelectedPosition = '';
      var anSelectedPemenang = '';

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
          anSelectedNilai = element[1]; // Nilai
          anSelectedMetode = element[6]; // metode pengadaan
          anSelectedPemenang = element[10]; // Pemenang
          anSelectedNilaiRep = anSelectedNilai.replace(/\./g,'');
      }); 

      $('#btnTeruskan').on('click', function (){ 
        if(anSelectedData == "") { alertError3("Pilih data dahulu"); return false; }
        if (anSelectedPemenang == '<span class="badge badge-danger">Belum Ditetapkan</span>') { alertError3("Pemenang Belum Ditetapkan"); return false; }

        $.messager.confirm('Konfirmasi',"Yakin ingin meneruskan paket ini untuk diproses kontrak?",function(r){
          if (r){ 
            $.post("contracting_json/approve_manager", 
              { reqId: anSelectedId }, 
              function(data){
                  $.messager.alert('Info', data, 'info');
                  oTable.fnReloadAjax("contracting_json/contracting_paket");
              }
            );
          }
        });
      }); 

      $('#btnCetakLaporan').on('click', function () {
        if(anSelectedData == "") { alertError3("Pilih data dahulu"); return false; }
        newWindow = window.open("main/loadUrl/report/paket_cetak_pdf/?reqId="+anSelectedId, 'Cetak');
        newWindow.focus();
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
          <h4 class="card-title">Selesai Penilihan</h4>
          <div class="heading-elements" id="tombol">
            <span></span>
          </div>
        </div>
        <div class="card-body area-datatable">
          <div class="form-body">
            <div class="row">
              <div class="form-group col-md-12 mb-2">
                <a id="btnTeruskan" class="<?= CLASS_BTN_INFO ?> mr-1" title="Ubah"><i class="fa fa-send"></i> Teruskan ke PPK </a>
                <a id="btnCetakLaporan" class="<?= CLASS_BTN_INFO ?> mr-1" title="Ubah"><i class="fa fa-print"></i> Cetak Laporan</a>
              </div>
            </div>
            <div>
              <table id="example" class="display table-bordered" cellspacing="0" width="100%" style="border-bottom: none !important">
                  <thead>
                        <tr>
                        <th width="1px">Id</th> <!-- PaketID -->
                        <th width="1px">Nilai</th>
                        <th style="width: 45%">Paket Pengadaan</th>
                        <th style="width: 15%">Harga Perkiraan Sendiri</th>
                        <th style="width: 10%">Harga Final</th> <!-- Harga Final Nego / Auction -->
                        <th style="width: 10%">Metode Pengadaan</th>
                        <th style="width: 20%">Jenis Kontrak</th>
                        <th style="width: 10%">User</th>
                        <th style="width: 10%">Pemenang</th>
                        <th style="width: 10%">PPK</th> <!-- ApprovePPK -->
                        <th style="width: 10%">PIC</th> <!-- PICKontrak -->
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
