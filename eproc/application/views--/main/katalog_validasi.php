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
             null
          ],
    "bSort":true,
    "bProcessing": true,
    "bServerSide": true,
    "sAjaxSource": "katalog_validasi_json/json",
    "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
    columnDefs: [{ className: 'never', targets: [ 0 ] },{ className: 'text-center', targets: [ 2 ] }]
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
          anSelectedId = element[0];
      });

      $('#btnEdit').on('click', function () {
        if(anSelectedData == "")
        {
          alertError3("Pilih data dahulu");
          return false;
        }
        location.href = "main/index/katalog_validasi_rekanan/?reqId="+anSelectedId;

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

} );

</script>

<style type="text/css">
  .card-text a {
    font-size: 11px;
  }
</style>

<section id="backColor">
  <div class="row">

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
              <a href="<?= base_url() ?>main/index/katalog_validasi" class="btn btn-success btn-lg mb-1 text-left" style="width: 100%; opacity: .3"><i class="fa fa-edit fa-lg pull-right"></i> Verifikasi</a>
              <a href="<?= base_url() ?>main/index/katalog_laporan" class="btn btn-success btn-lg mb-1 text-left" style="width: 100%;"><i class="fa fa-flag fa-lg pull-right"></i> Laporan</a>
            <?php
            } ?>
          </div>
        </div>
      </div>
    </div>

    <div class="col-md-10 col-sm-12">
      <div class="card border-bottom-primary" style="zoom: 1;">
        <div class="card-header">
          <h4 class="card-title">Katalog <small> Verifikasi</small></h4>
          <div class="heading-elements" id="tombol">
            <span></span>
          </div>
        </div>
        <div class="card-body area-datatable">
          <div class="form-body">
            <div class="row" id="sticker">
              <div class="form-group col-md-12 mb-2">
                  <a id="btnEdit" class="<?= CLASS_BTN_INFO ?>" title="Ubah"><span class="fa fa-eye"></span> Lihat</a>
              </div>
            </div>
            <div>
              <!-- <table id="example" class="border-double table mb-0" class="table-responsive"> -->
              <table id="example" class="border-double table mb-0 table-bordered" style="width: 100%">
                  <thead>
                        <tr>
                        <th width="1px">Id</th>
                        <th>Nama Penyedia</th>
                        <th width="5%">Total Katalog</th>
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
