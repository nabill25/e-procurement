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
             null,null,null,null,null,null
          ],
    "bSort":true,
    "bProcessing": true,
    "bServerSide": true,
    "sAjaxSource": "contracting_json/contracting_paket_sppbj?tahun=<?= $getTahun ?>",
    "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
    columnDefs: [{ className: 'never', targets: [ 0,1,2,3,6,8,9] }]
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
          anSelectedPIC = element[1]; // PIC Kontrak
          anSelectedPemenang = element[2]; // Pemenang
          anSelectedNilai = element[3]; // Nilai
          anSelectedNilaiRep = anSelectedNilai.replace(/\./g,'');
      }); 

      $('#btnView1').on('click', function () 
      {
        if(anSelectedData == "") { alertError3("Pilih data dahulu"); return false; }
        if (anSelectedPemenang == '<span class="badge badge-danger">Belum Ditetapkan</span>') { alertError3("Pemenang Belum Ditetapkan"); return false; }

        if (anSelectedPIC != <?= $this->USER_LOGIN_ID ?>) {
          alertError3("Paket tidak dapat diproses karena anda bukan PIC"); return false;
        }

        if (anSelectedNilaiRep > 300000000)
        {
          location.href = "kontrak/index/contracting_surat_perjanjian?reqId="+anSelectedId+"&jnskontrak=1&tahun=<?= $getTahun ?>"; // SPK
        } else {
          // alertError3("Nilai diatas dibawah 300.000.000, tanpa SPPBJ");
          $.messager.confirm('Konfirmasi',"Nilai dibawah 300.000.000, lanjutkan tanpa SPPBJ?",function(r){
            if (r){ 
              $.get("contracting_json/addSppbjNon/"+anSelectedId+"/<?= $getTahun ?>",
                function(data){ 
                  if (data == 'Data berhasil disimpan') {
                    location.href = "kontrak/index/contracting_persiapan";
                  } else {
                    location.reload();
                  }
              });
            }
          });
        }

        $('div.flexmenumobile').hide()
        $('div.flexoverlay').css('display', 'none')
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
          <h4 class="card-title">SPPBJ</h4>
          <div class="heading-elements" id="tombol">
            <span></span>
          </div>
        </div>
        <div class="card-body area-datatable">
          <div class="form-body">
            <div class="row">
              <div class="form-group col-md-12 mb-2"> 
                  <?php
                  if ($this->LEVEL_KONTRAK == '1') {?>
                  <a id="btnView1" class="<?= CLASS_BTN_INFO ?> mr-1" title="Ubah"><span class="fa fa-plus"></span> Buat SPPBJ </a>
                  <?php 
                  } ?> 
                  <?php
                  // } ?>
              </div>
            </div>
            <div>
              <table id="example" class="display table-bordered" cellspacing="0" width="100%" style="border-bottom: none !important">
                  <thead>
                    <tr>
                      <th width="1px">Id</th> <!-- PaketID -->
                      <th width="1px">PIC</th> <!-- PIC -->
                      <th style="width: 10%">Pemenang</th>
                      <th style="width: 10%">Nilai</th>
                      <th style="width: 45%">Paket Pengadaan</th>
                      <th style="width: 15%">Harga Perkiraan</th>
                      <th style="width: 10%">Metode Pengadaan</th>
                      <th style="width: 10%">Jenis Kontrak</th>
                      <th style="width: 10%">User</th>
                      <th style="width: 10%">APPK</th> <!-- ApprovePPK -->
                      <th style="width: 15%">PIC</th> <!-- PICKontrak -->
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
