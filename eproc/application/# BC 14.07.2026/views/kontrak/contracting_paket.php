<?php
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
    "aoColumns": [
             {"bVisible": false},null,null,null,null,
             null,null,null,null,null
          ],
    "bSort":true,
    "bProcessing": true,
    "bServerSide": true,
    "sAjaxSource": "contracting_json/contracting_paket?tahun=<?= $getTahun ?>",
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
          anSelectedPemenang = element[9]; // Pemenang
          anSelectedNilaiRep = anSelectedNilai.replace(/\./g,'');
      });

      $('#btnView').on('click', function () {
        if(anSelectedData == "") { alertError3("Pilih data dahulu"); return false; }
        if (anSelectedPemenang == '<span class="badge badge-danger">Belum Ditetapkan</span>') { alertError3("Pemenang Belum Ditetapkan"); return false; }

        if (anSelectedNilaiRep > 1000000000)
        {
          location.href = "kontrak/index/contracting_surat_perjanjian?reqId="+anSelectedId+"&tahun=<?= $getTahun ?>"; // PKS
        } else {
          location.href = "kontrak/index/contracting_surat_perintah?reqId="+anSelectedId+"&tahun=<?= $getTahun ?>"; // SPK
        }

        $('div.flexmenumobile').hide()
        $('div.flexoverlay').css('display', 'none')
      });

      $('#btnView1').on('click', function () {
        if(anSelectedData == "") { alertError3("Pilih data dahulu"); return false; }

        if (anSelectedPemenang == '<span class="badge badge-danger">Belum Ditetapkan</span>') { alertError3("Pemenang Belum Ditetapkan"); return false; }

        // if (anSelectedNilaiRep >= 500000000)
        // {
        //   alertError3("Nilai diatas 500jt, harap menggunakan Surat Perjanjian");
        // } else {
          location.href = "kontrak/index/contracting_surat_perjanjian?reqId="+anSelectedId+"&jnskontrak=0&tahun=<?= $getTahun ?>"; // SPK
        // }
          // location.href = "kontrak/index/contracting_surat_perintah?reqId="+anSelectedId+"&tahun=<?= $getTahun ?>"; // SPK

        $('div.flexmenumobile').hide()
        $('div.flexoverlay').css('display', 'none')
      });

      $('#btnView11').on('click', function () {
        if(anSelectedData == "") { alertError3("Pilih data dahulu"); return false; }

        if (anSelectedPemenang == '<span class="badge badge-danger">Belum Ditetapkan</span>') { alertError3("Pemenang Belum Ditetapkan"); return false; }
          alertError3("Flow belum ditentukan");
          // location.href = "kontrak/index/contracting_surat_perjanjian?reqId="+anSelectedId+"&jnskontrak=0&tahun=<?= $getTahun ?>"; // SPK

        $('div.flexmenumobile').hide()
        $('div.flexoverlay').css('display', 'none')
      });

      $('#btnView2').on('click', function () {
        if(anSelectedData == "") { alertError3("Pilih data dahulu"); return false; }
        if (anSelectedPemenang == '<span class="badge badge-danger">Belum Ditetapkan</span>') { alertError3("Pemenang Belum Ditetapkan"); return false; }

        location.href = "kontrak/index/contracting_surat_perjanjian?reqId="+anSelectedId+"&jnskontrak=1&tahun=<?= $getTahun ?>"; // SPK
          // location.href = "kontrak/index/contracting_surat_perintah?reqId="+anSelectedId+"&tahun=<?= $getTahun ?>"; // SPK

        $('div.flexmenumobile').hide()
        $('div.flexoverlay').css('display', 'none')
      });

      $('#btnView3').on('click', function () {
        if(anSelectedData == "") { alertError3("Pilih data dahulu"); return false; }
        if (anSelectedPemenang == '<span class="badge badge-danger">Belum Ditetapkan</span>') { alertError3("Pemenang Belum Ditetapkan"); return false; }

        location.href = "kontrak/index/contracting_surat_perjanjian?reqId="+anSelectedId+"&jnskontrak=3&tahun=<?= $getTahun ?>"; // SPK
          // location.href = "kontrak/index/contracting_surat_perintah?reqId="+anSelectedId+"&tahun=<?= $getTahun ?>"; // SPK

        $('div.flexmenumobile').hide()
        $('div.flexoverlay').css('display', 'none')
      });

      $('#btnViewLaporan').on('click', function () {
        if(anSelectedData == "") { alertError3("Pilih data dahulu"); return false; }
        if (anSelectedPemenang == '<span class="badge badge-danger">Belum Ditetapkan</span>') { alertError3("Pemenang Belum Ditetapkan"); return false; }

          openAddLg('main/loadUrl/main/paket_laporan_view?reqId='+anSelectedId)

        $('div.flexmenumobile').hide()
        $('div.flexoverlay').css('display', 'none')
      });


      $('#btnTeruskan').on('click', function (){ 
        if(anSelectedData == "") { alertError3("Pilih data dahulu"); return false; }
        if (anSelectedPemenang == '<span class="badge badge-danger">Belum Ditetapkan</span>') { alertError3("Pemenang Belum Ditetapkan"); return false; }

        $.messager.confirm('Konfirmasi',"Yakin ingin meneruskan paket ini untuk diproses kontrak?",function(r){
          if (r){ 
            $.post("contracting_json/approve_ppk", 
              { reqId: anSelectedId }, 
              function(data){
                  $.messager.alert('Info', data, 'info');
                  oTable.fnReloadAjax("contracting_json/contracting_paket?tahun=<?= $getTahun ?>");
              }
            );
          }
        });
      });

      // $('#btnView2').on('click', function () {
      //   if(anSelectedData == "")
      //   {
      //     alertError3("Pilih data dahulu");
      //     return false;
      //   }

      //   if (anSelectedPemenang == '<span class="badge badge-danger">Belum Ditetapkan</span>') {
      //     alertError3("Pemenang Belum Ditetapkan");
      //     return false;
      //   }

      //     location.href = "kontrak/index/contracting_surat_perjanjian?reqId="+anSelectedId+"&tahun=<?= $getTahun ?>"; // PKS

      //   $('div.flexmenumobile').hide()
      //   $('div.flexoverlay').css('display', 'none')
      // });
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
                  <!-- <a href="kontrak/index/contracting_dashboard?tahun=<?= $getTahun ?>" class="btn round btn-min-width box-shadow-1 btn-danger mr-1 text-white"> <i class="fa fa-arrow-left"></i> Kembali </a> -->
                  <?php
                  if ($this->USER_TYPE_ID == '28') {?>
                    <a id="btnTeruskan" class="<?= CLASS_BTN_INFO ?> mr-1" title="Teruskan"><i class="fa fa-send"></i> Teruskan untuk diproses </a>
                    <a id="btnViewLaporan" class="<?= CLASS_BTN_SUCCESS ?> mr-1" title="View"><i class="fa fa-file"></i> Lihat Laporan </a>
                  <?php 
                    } ?>
                  <?php
                  // if ($this->LEGAL != '1') {?>
                  <!-- <a id="btnView1" class="<?= CLASS_BTN_INFO ?> mr-1" title="Ubah"><span class="fa fa-plus"></span> Buat SPPBJ </a> -->
                  <!-- <a id="btnView11" class="<?= CLASS_BTN_DANGER ?> mr-1" title="Ubah"><span class="fa fa-minus-circle"></span> Non SPPBJ </a> -->
                  <!-- <a id="btnView1" class="<?= CLASS_BTN_INFO ?> mr-1" title="Ubah"><span class="fa fa-pencil"></span> SPK </a>
                  <a id="btnView2" class="<?= CLASS_BTN_SUCCESS ?> mr-1" title="Ubah"><span class="fa fa-book"></span> Surat Perjanjian </a>
                  <a id="btnView3" class="<?= CLASS_BTN_SECONDARY ?>" title="Ubah"><span class="fa fa-file-text"></span> Kontrak Payung </a> -->
                  <?php
                  // } ?>
              </div>
            </div>
            <div>
              <table id="example" class="display table-bordered" cellspacing="0" width="100%" style="border-bottom: none !important">
                  <thead> 
                        <tr>
                        <th width="1px">Id</th> <!-- PaketID -->
                        <th width="1px">Nilai</th>
                        <th style="width: 45%">Paket Pengadaan</th>
                        <th style="width: 15%">Harga Perkiraan Sediri</th>
                        <th style="width: 15%">Harga Pemilihan</th>
                        <th style="width: 10%">Metode Pengadaan</th>
                        <th style="width: 20%">Jenis Kontrak</th>
                        <th style="width: 10%">User</th>
                        <th style="width: 10%">Pemenang</th>
                        <th style="width: 10%">APPK</th> <!-- ApprovePPK -->
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
