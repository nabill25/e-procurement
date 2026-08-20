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
             null,
             null,
             null,
             null
          ],
    "bSort":true,
    "bProcessing": true,
    "bServerSide": true,
    "sAjaxSource": "katalog_json/json",
    "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
    columnDefs: [{ className: 'never', targets: [ 0, 1 ] },{ className: 'text-center', targets: [ 5,6,7,8 ] }]
    });
    oTable.fnSort( [ [0,'desc'] ] );

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
          anSelectedPublish = element[1];
      });

      $('#btnEdit').on('click', function () {
        if(anSelectedData == "")
        {
          alertError3("Pilih data dahulu");
          return false;
        }

        if(anSelectedPublish == "1")
        {
          alertError3("Katalog sudah di publish tidak dapat di edit");
          return false;
        }

        location.href = "main/index/katalog_rekanan_add/?reqId="+anSelectedId;
        // tutup flex dropdown => untuk versi mobile
        $('div.flexmenumobile').hide()
        $('div.flexoverlay').css('display', 'none')
      });

      $('#btnTambahFoto').on('click', function () {
        if(anSelectedData == "")
        {
          alertError3("Pilih data dahulu");
          return false;
        }
        location.href = "main/index/katalog_foto/?reqId="+anSelectedId;
        // tutup flex dropdown => untuk versi mobile
        $('div.flexmenumobile').hide()
        $('div.flexoverlay').css('display', 'none')
      });

      $('#btnTambahLampiran').on('click', function () {
        if(anSelectedData == "")
        {
          alertError3("Pilih data dahulu");
          return false;
        }
        location.href = "main/index/katalog_lampiran/?reqId="+anSelectedId;
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
  .ui-widget-header {border: 1px solid transparent !important }
</style>

<section id="backColor">
  <div class="row">

    <div class="col-md-2 col-sm-12">
      <div class="card border-bottom-primary" style="zoom: 1;">
        <div class="card-body">
          <div class="card-text">
           <?php
            if($this->USER_TYPE_ID == "6") {
            // get Notification Penawaran
              $this->load->model("Katalog");
              $katalog = new Katalog();
              $statement = " AND A.REKANAN_ID = ".$this->ID." AND (A.STATUS='1' OR A.STATUS='3' OR A.STATUS='4' OR A.STATUS='5' )";
              $totalPenawaran = $katalog->getCountByParamsPenawaran(array(), $statement);
              ?>
              <a href="<?= base_url() ?>main/index/katalog_rekanan" class="btn btn-success btn-lg mb-1 text-left" style="width: 100%; opacity: .3"><i class="fa fa-cogs fa-lg pull-right"></i> Katalog</a>
              <a href="<?= base_url() ?>main/index/katalog_rekanan_add" class="btn btn-success btn-lg mb-1 text-left" style="width: 100%;"><i class="fa fa-plus fa-lg pull-right"></i> Tambah Katalog</a>
              <a href="<?= base_url() ?>main/index/katalog_penawaran" class="btn btn-success btn-lg mb-1 text-left" style="width: 100%"><i class="fa fa-shopping-basket fa-lg pull-right"></i> Penawaran <?= '<span class="badge badge-danger" style="opacity: 1">'.$totalPenawaran.'</span>'; ?></a>
              <a href="<?= base_url() ?>main/index/katalog_pernyataan" class="btn btn-success btn-lg mb-1 text-left" style="width: 100%"><i class="fa fa-upload fa-lg pull-right"></i> Upload <br>Kontrak Katalog & <br>Surat Pernyataan<br> Kewajaran Harga</a>
            <?php
            } ?>

            <?php
            if($this->USER_TYPE_ID == "1" || $this->USER_TYPE_ID == "2"){  // 1:admin, 2:validator?>
              <a href="<?= base_url() ?>main/index/katalog_rekanan" class="btn btn-success btn-lg mb-1 text-left" style="width: 100%; opacity: .3"><i class="fa fa-cogs fa-lg pull-right"></i> Katalog</a>
              <a href="<?= base_url() ?>main/index/katalog_validasi" class="btn btn-success btn-lg mb-1 text-left" style="width: 100%;"><i class="fa fa-edit fa-lg pull-right"></i> Verifikasi</a>
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
          <h4 class="card-title">Katalog <small> List</small></h4>
          <div class="heading-elements" id="tombol">
            <span></span>
          </div>
        </div>
        <div class="card-body area-datatable">
          <div class="form-body">
            <div class="row" id="sticker">
              <div class="form-group col-md-12 mb-2">
                <?php
                if($this->USER_TYPE_ID == "6"){ // 6:Penyedia
                ?>
                  <a id="btnEdit" class="<?= CLASS_BTN_INFO ?>" title="Ubah"><span class="fa fa-pencil"></span> Edit</a>
                  <a id="btnTambahFoto" class="<?= CLASS_BTN_SUCCESS ?>" title="Foto"><span class="fa fa-picture-o"></span> Tambah Foto</a>
                  <a id="btnTambahLampiran" class="<?= CLASS_BTN_PRIMARY ?>" title="Lampiran"><span class="fa fa-file-pdf-o"></span> Tambah Lampiran</a>
                  <a id="btnDelete" class="<?= CLASS_BTN_DANGER ?>" title="Hapus"><span class="fa fa-trash"></span> Hapus</a>
                <?php
                } ?>

                <?php
                if($this->USER_TYPE_ID == "2"){  // 2:validator
                ?>
                  <!-- <a id="btnEdit" class="btn btn-info text-white" title="Ubah"><span class="fa fa-eye"></span> Lihat</a> -->
                <?php
                } ?>

              </div>
            </div>
            <div> 
              
              <table id="example" class="border-double table mb-0 table-bordered" style="width: 100%">
                <thead>
                  <tr>
                    <th width="1px">Id</th>
                    <th>Pub</th>
                    <th style="width: 45%">Nama</th>
                    <th style="width: 25%">Merek</th>
                    <!-- <th>Unit Pengukuran/Satuan</th>     -->
                    <th style="width: 15%">Harga</th>
                    <th style="width: 5%">Status<br>Aktif</th>
                    <th style="width: 5%">Publish</th>
                    <th style="width: 2%">Foto</th>
                    <th style="width: 3%">Lampiran</th>
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

<script type="text/javascript">
$(document).ready( function ()
{
  $('#treeSatker').treegrid({
    onClickRow: function(param){
    $("#tombol span").remove();
    $("#tombol").html('<div><a class="btn btn-primary text-white" onClick="openAdd(\'main/loadUrl/main/master_bidang_usaha_add/?reqParentId='+param.id+'\')" title="Tambah"><span class="fa fa-plus"></span> Tambah</a>&nbsp;&nbsp;<a class="btn btn-info text-white" onClick="openAdd(\'main/loadUrl/main/master_bidang_usaha_add/?reqId='+param.id+'\')" title="Edit"><span class="fa fa-pencil"></span> Edit</a>&nbsp;&nbsp;<a class="btn btn-danger text-white"  onClick="deleteData(\'bidang_usaha_json/delete\','+"'"+param.id+"'"+')" title="Hapus"><span class="fa fa-trash"></span> Hapus</a></div><hr>');
    }
  });
});
</script>
