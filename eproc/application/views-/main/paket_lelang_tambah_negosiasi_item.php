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
$this->load->model(array("PaketRekanan","Paket","PaketNegoisasi","RekananPaketPenawaran","Rekanan","PaketNegosiasiValidasi","Paketnegosiasiitem","PaketNegoisasi"));

$paket = new Paket();
$paket->selectByParamsMonitoring2(array(), -1, -1, " AND PAKET_ID = '".$reqId."' ");
$paket->firstRow();
$reqPermohonanId = $paket->getField("PERMOHONAN_PAKET_ID");

$negosiasiitem = new Paketnegosiasiitem();
$negosiasiitem->selectByParams(array(), $dsplyRange, $dsplyStart, "AND PAKET_ID = ".$reqId."", $sOrder);

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
  $('#example').DataTable({
    "iDisplayLength": 10,
    "paging": false,
    // "aaSorting": [[0, 'desc']],
    "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
  });

  $('#btnAdd').on('click', function () {
    openAddFrame("main/loadUrl/main/paket_lelang_tambah_negosiasi_item_files?reqId=<?= $reqId ?>");
  });
});

$(function(){
  let timer;

  $('#ff').form({
    url:'paket_lelang_tambah_negosiasi_item_json/updateAllItem',
    onSubmit:function(){
      return $(this).form('validate');
    },
    success:function(data){
      alertSuccess2(data);

      // $.messager.alert('Info', data, 'info');
      // setTimeout(function () { 
      //   location.reload();
      //  }, 2000);
    }
  });

  $('#ff').on('input change', 'input, select, textarea', function(){
    clearTimeout(timer);

    timer = setTimeout(function(){
      $('#ff').form('submit');
    }, 800); // delay 800ms
  });
});

function reloadMonitoring()
{
  oTable.fnReloadAjax("paket_lelang_tambah_negosiasi_item_json/json?reqId=<?= $reqId ?>");
}

function reloadMonitoringReload()
{
  location.reload();
}

function edit(id) {
  openAddFrame("main/loadUrl/main/paket_lelang_tambah_negosiasi_item_form/?reqId="+id);
}

function deleted(id) {
  deleteData("paket_lelang_tambah_negosiasi_item_json/deleteItem/", id);
}


function teruskan(id) {
  $.messager.confirm('Konfirmasi',"Kirim Semua Item Negosiasi ini ke penyedia?",function(r){
  if (r) { 
    $.post(
      "paket_lelang_tambah_negosiasi_item_json/teruskan",
      {
        reqId: "<?= $reqId ?>",
        reqStatus: "2"
      },
      function (data) {
        var result = data.split("||");

        if (result[0] == '0') {
          alertError3(result[1]);
        } else {
          alertSuccess2(result[1]);
          setTimeout(function() {
            location.reload();
          }, 2000);
        }
      }
    ); 
   }
  });
}

function terima(id) {
  $.messager.confirm('Konfirmasi',"Terima Item Negosiasi ini?",function(r){
  if (r) { 
    $.post(
      "paket_lelang_tambah_negosiasi_item_json/teruskan",
      {
        reqId: "<?= $reqId ?>",
        reqStatus: "1"
      },
      function (data) {
        var result = data.split("||");

        if (result[0] == '0') {
          alertError3(result[1]);
        } else {
          alertSuccess2(result[1]);
          setTimeout(function() {
            location.reload();
          }, 2000);
        }
      }
    ); 
   }
  });
}

function summary(id,volume,durasi) {
  var getHargaNego = $('#reqIdNilainegosiasi'+id).val();
  var getNilaiPenawaran = $('#reqIdNilaipenawaran'+id).val();
  var getNilaiSatuan = $('#reqIdNilaisatuan'+id).val();
  var hasilHargaNego = getHargaNego.replace(/\./g, '');
  var hasilNilaiPenawaran = getNilaiPenawaran.replace(/\./g, '');

  if (parseInt(hasilHargaNego) > parseInt(hasilNilaiPenawaran)) {
    alertError3('Harga Nego tidak boleh lebih besar dari Harga Penawaran');
    $('#reqIdNilainegosiasi'+id).val(0);
    $('#reqIdJumlahnegosiasi'+id).val(0);
    hitungTotal();
    return false;
  }


  var totalNego = volume * durasi * hasilHargaNego;
  var hasil2 = parseInt(totalNego).toLocaleString('id-ID');
  $('#reqIdJumlahnegosiasi'+id).val(hasil2);
  
  var persen = parseFloat((parseInt(hasilHargaNego) / parseInt(getNilaiSatuan) * 100).toFixed(2));

  $('#IdPersentaseNego'+id).html(persen).toLocaleString('id-ID');
  
  // hitungTotal();

  // Hitung Total Harga Nego
  if ($('#reqNullJumlahharganego').is(':checked')) { // = 0
      // checked
  } else {
    var PPN = parseInt($('#reqIdPPN').val());
    var totalHargaNego = 0;
    $('input[name="reqJumlahnegosiasi[]"]').each(function() {
      var nilaiHargaNego = $(this).val();
      nilaiHargaNego = nilaiHargaNego.replace(/[.,]/g, '');
      totalHargaNego += parseInt(nilaiHargaNego) || 0;
    }); 
    $('#reqIdJumlahharganego, #reqIdHideJumlahharganego').val(Math.ceil(((PPN/100) * totalHargaNego)).toLocaleString('id-ID'));
  }
  hitungTotal(); 

}

function hitungTotal() {
  var total = 0;
  var ppnHargaNego = $('#reqIdJumlahharganego').val();
      ppnHargaNego = ppnHargaNego.replace(/[.,]/g, '');

  $('input[name="reqJumlahnegosiasi[]"]').each(function() {
    var nilai = $(this).val();
    nilai = nilai.replace(/[.,]/g, '');
    total += parseInt(nilai) || 0;
  });

  total = total + parseInt(ppnHargaNego);
  $('#totalHargaNego').html(Math.ceil(total).toLocaleString('id-ID'));
  $('#reqTotalHargaNego').val(Math.ceil(total).toLocaleString('id-ID'));
}

function hitungTotalHargaPenawaran() {
  var total = 0;
  var ppnHargaPenawaran = $('#reqIdJumlahhargapenawaran').val();
      ppnHargaPenawaran = ppnHargaPenawaran.replace(/[.,]/g, '');

  $('input[name="reqJumlahpenawaran[]"]').each(function() {
    var nilai = $(this).val();
    nilai = nilai.replace(/[.,]/g, '');
    total += parseInt(nilai) || 0;
  });

  total = total + parseInt(ppnHargaPenawaran);
  $('#totalHargaPenawaran').html(Math.ceil(total).toLocaleString('id-ID'));
  $('#reqTotalHargaPenawaran').val(Math.ceil(total).toLocaleString('id-ID'));
}

function hitungTotalHargaSatuan() {
  var total = 0;
  var ppnHargaSatuan = $('#reqIdJumlahhargasatuan').val();
      ppnHargaSatuan = ppnHargaSatuan.replace(/[.,]/g, '');

  $('input[name="reqJumlahsatuan[]"]').each(function() {
    var nilai = $(this).val();
    nilai = nilai.replace(/[.,]/g, '');
    total += parseInt(nilai) || 0;
  });

  total = total + parseInt(ppnHargaSatuan);
  $('#totalHargaSatuan').html(Math.ceil(total).toLocaleString('id-ID'));
  $('#reqTotalHargaSatuan').val(Math.ceil(total).toLocaleString('id-ID'));
}

$(document).ready(function() {
  $('#reqNullJumlahhargasatuan').change(function() {
    if ($(this).is(':checked')) {
      $('#reqIdJumlahhargasatuan, #reqIdHideJumlahhargasatuan').val(0);
    } else {
      var total = parseInt($('#reqTotalHargaSatuan').val().replace(/[.,]/g, ''));
      var PPN = parseInt($('#reqIdPPN').val());

      if (PPN > 0) {
        // alert('ada PPN '+parseInt(total));
        $('#reqIdJumlahhargasatuan, #reqIdHideJumlahhargasatuan').val(Math.ceil(((PPN/100) * total)).toLocaleString('id-ID'));
      } else {
        $('#reqIdJumlahhargasatuan, #reqIdHideJumlahhargasatuan').val(parsetInt(Math.ceil(total)).toLocaleString('id-ID'));
      }
    }
    hitungTotalHargaSatuan();
    // hitungTotalPPN();
  });
}); 

$(document).ready(function() {
  $('#reqNullJumlahhargapenawaran').change(function() {
    if ($(this).is(':checked')) {
      $('#reqIdJumlahhargapenawaran, #reqIdHideJumlahhargapenawaran').val(0);
    } else {
      var total = $('#reqIdHideJumlahhargapenawaran').val();
      $('#reqIdJumlahhargapenawaran').val(total);

      var total = parseInt($('#reqTotalHargaPenawaran').val().replace(/[.,]/g, ''));
      var PPN = parseInt($('#reqIdPPN').val());

      if (PPN > 0) {
        // alert('ada PPN '+parseInt(total));
        $('#reqIdJumlahhargapenawaran, #reqIdHideJumlahhargapenawaran').val(Math.ceil(((PPN/100) * total)).toLocaleString('id-ID'));
      } else {
        alert('tidak ada PPN');
        $('#reqIdJumlahhargapenawaran, #reqIdHideJumlahhargapenawaran').val(parsetInt(Math.ceil(total)).toLocaleString('id-ID'));
      }

    }
    hitungTotalHargaPenawaran(); 
    // hitungTotalPPN();
  });
}); 

$(document).ready(function() {
  $('#reqNullJumlahharganego').change(function() {
    if ($(this).is(':checked')) {
      $('#reqIdJumlahharganego, #reqIdHideJumlahharganego').val(0);
    } else { 
      var total = parseInt($('#reqTotalHargaNego').val().replace(/[.,]/g, ''));
      var PPN = parseInt($('#reqIdPPN').val());

      if (PPN > 0) {
        // alert('ada PPN '+parseInt(total));
        $('#reqIdJumlahharganego, #reqIdHideJumlahharganego').val(Math.ceil(((PPN/100) * total)).toLocaleString('id-ID'));
      } else {
        alert('tidak ada PPN');
        $('#reqIdJumlahharganego, #reqIdHideJumlahharganego').val(parsetInt(Math.ceil(total)).toLocaleString('id-ID'));
      }
    }
    hitungTotal(); 
    // hitungTotalPPN();
  });
}); 

function hitungTotalPPN() {

  var PPN = parseInt($('#reqIdPPN').val());
  var reqTotalHargaSatuan = $('#reqTotalHargaSatuan').val().replace(/[.,]/g, '');
  var reqTotalHargaPenawaran = $('#reqTotalHargaPenawaran').val().replace(/[.,]/g, '');
  var reqTotalHargaNego = $('#reqTotalHargaNego').val().replace(/[.,]/g, '');
  
  // Hitung PPN dan Total Harga Satuan
  if ($('#reqNullJumlahhargasatuan').is(':checked')) { // = 0
      // checked
  } else {
    var totalHargaSatuan = 0;
    $('input[name="reqJumlahsatuan[]"]').each(function() {
      var nilaiHargaSatuan = $(this).val();
      nilaiHargaSatuan = nilaiHargaSatuan.replace(/[.,]/g, '');
      totalHargaSatuan += parseInt(nilaiHargaSatuan) || 0;
    });
    $('#reqIdJumlahhargasatuan, #reqIdHideJumlahhargasatuan').val(Math.ceil(((PPN/100) * totalHargaSatuan)).toLocaleString('id-ID'));
    hitungTotalHargaSatuan();
  }

  // Hitung PPN dan Total Harga Penawaran
  if ($('#reqNullJumlahhargapenawaran').is(':checked')) { // = 0
      // checked
  } else {
    var totalHargaPenawaran = 0;
    $('input[name="reqJumlahpenawaran[]"]').each(function() {
      var nilaiHargaPenawaran = $(this).val();
      nilaiHargaPenawaran = nilaiHargaPenawaran.replace(/[.,]/g, '');
      totalHargaPenawaran += parseInt(nilaiHargaPenawaran) || 0;
    }); 
    $('#reqIdJumlahhargapenawaran, #reqIdHideJumlahhargapenawaran').val(Math.ceil(((PPN/100) * totalHargaPenawaran)).toLocaleString('id-ID'));
    hitungTotalHargaPenawaran();
  }

  // Hitung PPN dan Total Harga Nego
  if ($('#reqNullJumlahharganego').is(':checked')) { // = 0
      // checked
  } else {
    var totalHargaNego = 0;
    $('input[name="reqJumlahnegosiasi[]"]').each(function() {
      var nilaiHargaNego = $(this).val();
      nilaiHargaNego = nilaiHargaNego.replace(/[.,]/g, '');
      totalHargaNego += parseInt(nilaiHargaNego) || 0;
    }); 
    $('#reqIdJumlahharganego, #reqIdHideJumlahharganego').val(Math.ceil(((PPN/100) * totalHargaNego)).toLocaleString('id-ID'));
    hitungTotal(); 
  }

}

</script>

<style type="text/css">
.table th { padding: 4px !important; text-align: center; vertical-align: middle; }
#example_length { display: none; }
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
              <div class="row" id="sticker">
                <div class="form-group col-md-12">
                  <a id="btnAdd" title="Tambah" class="<?= CLASS_BTN_PRIMARY ?>"><span class="fa fa-download"></span> Import Item </a> 
                </div>
              </div>

              <form id="ff" class="easyui-form form-horizontal" method="post" novalidate enctype="multipart/form-data"> 
                <table id="example" class="border-double table mb-0 table-bordered"> 
                  <thead>
                    <tr>
                      <th style="width: 20%">Uraian</th>
                      <th>Volume</th>
                      <th>Durasi</th>
                      <th>Harga Satuan</th>
                      <th>Jumlah <br>Harga Satuan</th>
                      <th>Harga <br>Penawaran</th>
                      <th>Jumlah <br>Harga Penawaran</th>
                      <th>% HPS</th>
                      <th>Harga Nego</th>
                      <th>Jumlah <br>Harga Nego</th>
                      <th>% HPS</th>
                      <th>#</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                    $no     =1;
                    $total  = 0;
                    $jumlahHarga = 0;
                    $jumlahHargaPenawaran = 0;
                    $jumlahHargaNego = 0;
                    $html   = '';
                    while ($negosiasiitem->nextRow()) {
                      $jumlahHarga += $negosiasiitem->getField('JUMLAH_HARGA');
                      $jumlahHargaPenawaran += $negosiasiitem->getField('JUMLAH_PENAWARAN');
                      $jumlahHargaNego += $negosiasiitem->getField('JUMLAH_NEGOSIASI');
                      $statusNego = $negosiasiitem->getField('STATUS_NEGO');
                      if ($statusNego == '1' || $statusNego == '2' || $statusNego == '4' || $statusNego == '5') { $open = 'readonly'; $disabled = 'disabled'; } else { $open = ''; $disabled = ''; }
                      $html .= '<tr>';
                      $html .= '
                        <td>
                          '.$negosiasiitem->getField('URAIAN').' 
                          <input class="form-control" name="paketnegosiasiitemid[]" type="hidden" value="'.$negosiasiitem->getField('PAKET_NEGOSIASI_ITEM_ID').'">
                        </td> 
                        <td class="text-center">'.$negosiasiitem->getField('VOLUME').'<br>'.$negosiasiitem->getField('SATUAN_VOLUME').'</td> 
                        <td class="text-center">'.$negosiasiitem->getField('DURASI').'<br>'.$negosiasiitem->getField('SATUAN_DURASI').'</td> 
                        <td class="text-center">
                          '.currencyToPage($negosiasiitem->getField('HARGA_SATUAN')).'
                          <input class="form-control" name="reqNilaisatuan[]" type="hidden" value="'.($negosiasiitem->getField('HARGA_SATUAN')).'" id="reqIdNilaisatuan'.$negosiasiitem->getField('PAKET_NEGOSIASI_ITEM_ID').'">
                        </td> 
                        <td class="text-center">
                          '.currencyToPage($negosiasiitem->getField('JUMLAH_HARGA')).'
                          <input class="form-control" name="reqJumlahsatuan[]" type="hidden" value="'.($negosiasiitem->getField('JUMLAH_HARGA')).'" id="reqIdJumlahharga'.$negosiasiitem->getField('PAKET_NEGOSIASI_ITEM_ID').'">
                        </td> 
                        <td class="text-center">
                          '.currencyToPage($negosiasiitem->getField('NILAI_PENAWARAN')).'
                          <input class="form-control" name="reqNilaipenawaran[]" type="hidden" value="'.$negosiasiitem->getField('NILAI_PENAWARAN').'" id="reqIdNilaipenawaran'.$negosiasiitem->getField('PAKET_NEGOSIASI_ITEM_ID').'">
                        </td>
                        <td class="text-center">
                          '.currencyToPage($negosiasiitem->getField('JUMLAH_PENAWARAN')).'
                          <input class="form-control" name="reqJumlahpenawaran[]" type="hidden" value="'.$negosiasiitem->getField('JUMLAH_PENAWARAN').'" id="reqIdJumlahpenawaran'.$negosiasiitem->getField('PAKET_NEGOSIASI_ITEM_ID').'">
                        </td> 
                        <td>'.$negosiasiitem->getField('PERSENTASE_PENAWARAN').'</td>';

                      $html .= '
                        <td> 
                        <input title="Harga Satuan harus diisi" OnFocus="FormatAngka(\'reqIdNilainegosiasi'.$negosiasiitem->getField('PAKET_NEGOSIASI_ITEM_ID').'\');" OnKeyUp="FormatUang(\'reqIdNilainegosiasi'.$negosiasiitem->getField('PAKET_NEGOSIASI_ITEM_ID').'\'); summary('.$negosiasiitem->getField('PAKET_NEGOSIASI_ITEM_ID').','.$negosiasiitem->getField('VOLUME').','.$negosiasiitem->getField('DURASI').')" OnBlur="FormatUang(\'reqIdNilainegosiasi'.$negosiasiitem->getField('PAKET_NEGOSIASI_ITEM_ID').'\');" class="form-control easyui-validatebox span3"  name="reqNilainegosiasi[]" type="text" id="reqIdNilainegosiasi'.$negosiasiitem->getField('PAKET_NEGOSIASI_ITEM_ID').'" value="'.currencyToPage($negosiasiitem->getField("NILAI_NEGOSIASI")).'" required  onchange="summary(\''.$negosiasiitem->getField('PAKET_NEGOSIASI_ITEM_ID').'\','.$negosiasiitem->getField('VOLUME').','.$negosiasiitem->getField('DURASI').')" '.$open.'/>

                        </td>';

                      $html .= '
                        <td><input class="form-control" name="reqJumlahnegosiasi[]" type="text" value="'.currencyToPage($negosiasiitem->getField('JUMLAH_NEGOSIASI')).'" id="reqIdJumlahnegosiasi'.$negosiasiitem->getField('PAKET_NEGOSIASI_ITEM_ID').'" readonly></td>';

                      $html .= '
                        <td class="text-center" id="IdPersentaseNego'.$negosiasiitem->getField('PAKET_NEGOSIASI_ITEM_ID').'">
                          '.$negosiasiitem->getField('PERSENTASE_NEGOSIASI').'
                        </td>';

                      if ($statusNego == '1' || $statusNego == '2' || $statusNego == '4' || $statusNego == '5') {
                        $html .= '<td>-</td>';
                      } else {
                          // <a onClick="return edit(\''.$negosiasiitem->getField('PAKET_NEGOSIASI_ITEM_ID').'\')"><span class="fa fa-pencil mr-1"></span></a>
                      $html .= '
                        <td>
                          <a onClick="return deleted(\''.$negosiasiitem->getField('PAKET_NEGOSIASI_ITEM_ID').'\')"><span class="fa fa-trash mr-1"></span></a>
                          </td>';
                      }

                      $html .= '</tr>';
                      $no++;
                    }
                  echo $html;
                  ?>
                  </tbody>

                  <?php
                  if ($statusNego != '')
                  { ?>
                  <tfoot>
                    <?php 
                      $paketNegosiasi = new PaketNegoisasi();
                      $paketNegosiasi->selectByParams(array(), -1, -1, " AND PAKET_ID = '".$reqId."' ");
                      $paketNegosiasi->firstRow();
                     ?>
                    <tr>
                      <td class="text-center">
                        <b>PPN</b>
                        <div class="form-check form-check-inline">
                          <input class="form-control" id="reqIdPPN" name="reqPPN" type="text" value="<?= currencyToPage($paketNegosiasi->getField('PPN')) ?: 0 ?>" onChange="return hitungTotalPPN()" OnFocus="FormatAngka('reqIdPPN');" OnKeyUp="FormatUang('reqIdPPN'); hitungTotalPPN()" OnBlur="FormatUang('reqIdPPN');" maxlength="2" style="width: 50px;" <?= $open ?>>
                          %
                        </div>
                      </td>
                      <td colspan="2"></td>
                      <td><input class="form-control" name="reqId" type="hidden" value="<?= $reqId ?>"></td>
                      <td class="text-center">
                        <div class="form-check form-check-inline">
                          <input class="form-check-input" type="checkbox" id="reqNullJumlahhargasatuan" <?php if ($paketNegosiasi->getField('PPN_JUMLAH_HARGA_SATUAN') == 0) { echo 'checked'; } ?> <?= $disabled ?>>
                          <label class="form-check-label" for="nullCheck"><small>Tanpa&nbsp;PPN</small></label>
                        </div>
                        <input class="form-control" id="reqIdJumlahhargasatuan" name="reqJumlahhargasatuan" type="text" value="<?= currencyToPage($paketNegosiasi->getField('PPN_JUMLAH_HARGA_SATUAN')) ?: 0 ?>" onChange="return hitungTotalHargaSatuan()" OnFocus="FormatAngka('reqIdJumlahhargasatuan');" OnKeyUp="FormatUang('reqIdJumlahhargasatuan'); hitungTotalHargaSatuan()" OnBlur="FormatUang('reqIdJumlahhargasatuan');" readonly>
                        <input class="form-control" id="reqIdHideJumlahhargasatuan" name="reqIdHideJumlahhargasatuan" type="hidden" value="<?= currencyToPage($paketNegosiasi->getField('PPN_JUMLAH_HARGA_SATUAN')) ?: 0 ?>" readonly>
                      </td>
                      <td></td>
                      <td class="text-center">
                        <div class="form-check form-check-inline">
                          <input class="form-check-input" type="checkbox" id="reqNullJumlahhargapenawaran" <?php if ($paketNegosiasi->getField('PPN_JUMLAH_HARGA_PENAWARAN') == 0) { echo 'checked'; } ?> <?= $disabled ?>>
                          <label class="form-check-label" for="nullCheck"><small>Tanpa&nbsp;PPN</small></label>
                        </div>
                        <input class="form-control" id="reqIdJumlahhargapenawaran" name="reqJumlahhargapenawaran" type="text" value="<?= currencyToPage($paketNegosiasi->getField('PPN_JUMLAH_HARGA_PENAWARAN')) ?: 0 ?>" onChange="return hitungTotalHargaPenawaran()" OnFocus="FormatAngka('reqIdJumlahhargapenawaran');" OnKeyUp="FormatUang('reqIdJumlahhargapenawaran'); hitungTotalHargaPenawaran()" OnBlur="FormatUang('reqIdJumlahhargapenawaran');" readonly>
                        <input class="form-control" id="reqIdHideJumlahhargapenawaran" name="reqIdHideJumlahhargapenawaran" type="hidden" value="<?= currencyToPage($paketNegosiasi->getField('PPN_JUMLAH_HARGA_PENAWARAN')) ?: 0 ?>" readonly>
                      </td>
                      <td></td>
                      <td></td>

                      <td class="text-center">
                        <div class="form-check form-check-inline">
                          <input class="form-check-input" type="checkbox" id="reqNullJumlahharganego" <?php if ($paketNegosiasi->getField('PPN_JUMLAH_HARGA_NEGO') == 0) { echo 'checked'; } ?> <?= $disabled ?>>
                          <label class="form-check-label" for="nullCheck"><small>Tanpa&nbsp;PPN</small></label>
                        </div>
                        <input class="form-control" id="reqIdJumlahharganego" name="reqJumlahharganego" type="text" value="<?= currencyToPage($paketNegosiasi->getField('PPN_JUMLAH_HARGA_NEGO')) ?: 0 ?>" onChange="return hitungTotal()" OnFocus="FormatAngka('reqIdJumlahharganego');" OnKeyUp="FormatUang('reqIdJumlahharganego'); hitungTotal()" OnBlur="FormatUang('reqIdJumlahharganego');" readonly>
                        <input class="form-control" id="reqIdHideJumlahharganego" name="reqIdHideJumlahharganego" type="hidden" value="<?= currencyToPage($paketNegosiasi->getField('PPN_JUMLAH_HARGA_NEGO')) ?: 0 ?>" readonly>
                      </td>
                      <td></td>
                      <td></td>
                    </tr>
                    <tr>
                      <td class="text-center"><b>TOTAL</b></td>
                      <td colspan="2"></td>
                      <td></td>
                      <td class="text-center">
                        <span id="totalHargaSatuan"><?= currencyToPage($jumlahHarga + $paketNegosiasi->getField('PPN_JUMLAH_HARGA_SATUAN')); ?></span>
                        <input class="form-control" id="reqTotalHargaSatuan" name="reqTotalHargaSatuan" type="hidden" value="<?= currencyToPage($jumlahHarga + $paketNegosiasi->getField('PPN_JUMLAH_HARGA_SATUAN')); ?>">
                      </td>
                      <td></td>
                      <td class="text-center">
                        <span id="totalHargaPenawaran"><?= currencyToPage($jumlahHargaPenawaran + $paketNegosiasi->getField('PPN_JUMLAH_HARGA_PENAWARAN')); ?></span>
                        <input class="form-control" id="reqTotalHargaPenawaran" name="reqTotalHargaPenawaran" type="hidden" value="<?= currencyToPage($jumlahHargaPenawaran + $paketNegosiasi->getField('PPN_JUMLAH_HARGA_PENAWARAN')); ?>">
                      </td>
                      <td></td>
                      <td></td>
                      <td class="text-center">
                        <span id="totalHargaNego"><?= currencyToPage($jumlahHargaNego + $paketNegosiasi->getField('PPN_JUMLAH_HARGA_NEGO')); ?></span>
                        <input class="form-control" id="reqTotalHargaNego" name="reqTotalHargaNego" type="hidden" value="<?= currencyToPage($jumlahHargaNego + $paketNegosiasi->getField('PPN_JUMLAH_HARGA_NEGO')); ?>">
                      </td>
                      <td></td>
                      <td></td>
                    </tr>    
                  </tfoot>
                  <?php 
                  } ?>
                </table>

                <?php 
                switch ($statusNego) {
                  case '0':
                  echo '<div class="alert alert-info text-center mt-1"><b>. : : Proses Input : : .</b></div>';
                  echo '<a onClick="return teruskan('.$reqId.')" title="Kirim" class="'.CLASS_BTN_WARNING.'"><span class="fa fa-send"></span> Kirim ke Penyedia</a>';
                  // echo '<button type="submit" class="'.CLASS_BTN_PRIMARY.' pull-right" style="padding:10px 30px"> <i class="fa fa-check-square-o"></i> Simpan</button>';
                    break;
                  case '2':
                  echo '<div class="alert alert-success text-center mt-1"><b>. : : Dikirim ke penyedia : : .</b></div>';
                    break;
                  case '3':
                  echo '<div class="alert alert-danger text-center mt-1"><b>. : : Negosiasi Item Dikembalikan oleh penyedia : : .</b></div>';
                  echo '<a onClick="return teruskan('.$reqId.')" title="Kirim" class="'.CLASS_BTN_WARNING.'"><span class="fa fa-send"></span> Kirim ke Penyedia</a>';
                  // echo '<button type="submit" class="'.CLASS_BTN_PRIMARY.' pull-right" style="padding:10px 30px"> <i class="fa fa-check-square-o"></i> Simpan</button>';
                    break;
                  case '4': // Penyedia Menerima
                  echo '<div class="alert alert-info text-center mt-1"><b>. : : Negosiasi Item Diterima oleh penyedia : : .</b></div>';
                  echo '<a onClick="return teruskan('.$reqId.')" title="Kirim" class="'.CLASS_BTN_DANGER.'"><span class="fa fa-send"></span> Kirim Kembali ke Penyedia</a>';
                  echo '<a onClick="return terima('.$reqId.')" title="Terima" class="'.CLASS_BTN_PRIMARY.' ml-1"><span class="fa fa-check-square-o"></span> Terima Negosiasi</a>';
                    break;

                  case '5':
                  echo '<div class="alert alert-danger text-center mt-1"><b>. : : Negosiasi Item Ditolak, penyedia sedang merubah harga nego : : .</b></div>';
                    break;

                  case '1':
                  echo '<div class="alert alert-info text-center mt-1"><b>. : : Negosiasi Item Telah Disetujui : : .</b></div>';
                  echo '<a href="'.base_url().'main/index/paket_lelang_tambah_penentuan_pemenang/?reqId='.$reqId.'" class="'.CLASS_BTN_PRIMARY.'"><span class="fa fa-gavel"></span> Lanjut ke penetapan pemenang</a>';
                  echo '<a href="'.base_url().'main/loadUrl/report/negosiasi_item_excel/?reqId='.$reqId.'" class="'.CLASS_BTN_INFO.' ml-1"><span class="fa fa-file-excel-o"></span> Download Rekap Nego</a>';
                    break;
                  
                  default:
                    // code...
                    break;
                } 
                ?>
                
                

              </form>
               
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
 
