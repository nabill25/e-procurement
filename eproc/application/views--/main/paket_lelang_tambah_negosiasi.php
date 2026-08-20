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
include_once("functions/string.func.php");
include_once("functions/default.func.php");
include_once("functions/date.func.php");
$this->load->model("PaketRekanan");
$this->load->model("Paket");
$this->load->model("PaketNegoisasi");
$this->load->model("RekananPaketPenawaran");
$this->load->model("Rekanan");
$this->load->model(array("PaketNegosiasiValidasi","PaketDokumen"));

$paket_rekanan = new PaketRekanan();
$paket_nilai = new Paket();
$paket_nilai_estimate = new Paket();
$rekanan_paket_penawaran = new RekananPaketPenawaran();
$paket_negosiasi_validasi = new PaketNegosiasiValidasi();

$reqNilaiEstimate = httpFilterPost("reqNilaiEstimate");
$reqDataPenawaranHarga = $_POST["reqDataPenawaranHarga"];
$reqRekananIdArray =unserialize(stripslashes($_POST['reqRekananIdArray']));
$submitSimpan = httpFilterPost("submitSimpan");


$paketInfo->getPaket($reqId);
$reqNamaPaket = $paketInfo->nama;
$reqJenisPekerjaan = $paketInfo->jenis;
$reqJenisPekerjaanId = $paketInfo->jenis_id;
$reqMetodeEvaluasi = $paketInfo->metode_evaluasi;
$reqRekananIdPemenang = $paketInfo->rekanan_id_pemenang;
$reqPublishBANegosiasi = $paketInfo->publish_ba_negosiasi;

$paket_rekanan->selectByParams(array("PAKET_ID" => $reqId), -1, -1, " AND A.TANGGAL_DAFTAR IS NOT NULL AND A.LULUS_PENDAFTARAN = 1 AND A.LULUS_KUALIFIKASI = 1 ");

$i = 0;
while($paket_rekanan->nextRow())
{
  $arrRekananId[$i] = $paket_rekanan->getField("REKANAN_ID");
  $arrRekanan[$i] = $paket_rekanan->getField("REKANAN");
  $arrPaketRekananId[$i] = $paket_rekanan->getField("PAKET_REKANAN_ID");
  $arrPaketRekananNilai[$i] = $paket_rekanan->getField("NILAI_PENAWARAN");
  $arrDiemailNegosiasi[$i] = $paket_rekanan->getField("DI_EMAIL_NEGOSIASI");

  if($reqRekananIdPemenang == $paket_rekanan->getField("REKANAN_ID"))
    $indexRekananPememenang = $i;

  $i++;
}

if (is_array($arrRekanan)) {
  $arrRekananId = $arrRekananId;
  $arrRekanan = $arrRekanan;
  $arrPaketRekananId = $arrPaketRekananId;
  $arrPaketRekananNilai = $arrPaketRekananNilai;
  $arrDiemailNegosiasi = $arrDiemailNegosiasi;
} else {
  $arrRekananId = array();
  $arrRekanan = array();
  $arrPaketRekananId = array();
  $arrPaketRekananNilai = array();
  $arrDiemailNegosiasi = array();
}

// echo "<pre>"; print_r($arrPaketRekananId); die;

$paket_nilai->selectByParams(array("PAKET_ID" => $reqId));
$paket_nilai->firstRow();
$reqNilaiEstimate = $paket_nilai->getField("NILAI_OWNER_ESTIMATE");
$reqJenisPengadaan = $paket_nilai->getField("JENIS_PENGADAAN");

$colspanRekanan = 3;
if($reqJenisPengadaan == "LELANG")
{
  $display_pembelian = " style='display:none' ";
  $colspanRekanan = 2;
}

$submitNegosiasi = true;

$rekanan_paket_penawaran->selectByParamsEvaluasiRekanan($arrPaketRekananId, array("A.PAKET_ID" => $reqId));
//echo $rekanan_paket_penawaran->query;exit;

$paket_negosiasi_validasi->selectByParamsValidasi(array("NIP" => $this->NIP, "A.PAKET_ID" => $reqId));
$paket_negosiasi_validasi->firstRow();
//echo $paket_negosiasi_validasi->query;exit;

if($paket_negosiasi_validasi->getField("JENIS") == "")
  exit;
?>
<script type="text/javascript" src="lib/eproc/allfunc.js"></script>
<script type="text/javascript">
$(function(){
  $('#ff').form({
    url:'paket_negoisasi_json/negosiasi_lelang',
    onSubmit:function(){
      return $(this).form('validate');
    },
    success:function(data){
      arrData = data.split("-");
      if(arrData[0] == "0")
        $.messager.alert('Info', arrData[1], 'info');
      else
        document.location.href = 'main/index/paket_lelang_tambah_negosiasi_undangan/?reqId=<?=$reqId?>';
    }
  });
});

function summary()
{
  var reqTotal = 0;

  $("table input[id^=reqUnitPriceNegosiasi]").each(function() {
    var txtQuantity = $(this).attr("id").replace("reqUnitPriceNegosiasi", "reqQuantity");
    var txtJumlah = $(this).attr("id").replace("reqUnitPriceNegosiasi", "reqJumlahNegosiasi");

    var jumlah = (Number(FormatAngkaNumber($(this).val())) * Number($("#"+txtQuantity).val())).toFixed(2);

    reqTotal = Number(reqTotal) + Number(jumlah);

    $("#"+txtJumlah).val(FormatCurrency(jumlah));

  });

  $("#reqTotal").val(FormatCurrency(reqTotal));
}

function summaryPenawaran()
{
  var reqTotal = 0;
  var reqTotalFix = Number(FormatAngkaNumber($("#reqTotalPenawaran").val()));

  $("table input[id^=reqUnitPricePenawaran]").each(function() {
    var txtQuantity = $(this).attr("id").replace("reqUnitPricePenawaran", "reqQuantity");
    var txtJumlah = $(this).attr("id").replace("reqUnitPricePenawaran", "reqJumlahPenawaran");

    var jumlah = (Number(FormatAngkaNumber($(this).val())) * Number($("#"+txtQuantity).val())).toFixed(2);

    reqTotal = Number(reqTotal) + Number(jumlah);

    $("#"+txtJumlah).val(FormatCurrency(jumlah));

  });

  if(reqTotalFix == reqTotal)
    $("#reqTotalPenawaran").css("background-color", "#EDEDED");
  else
    $("#reqTotalPenawaran").css("background-color", "red");
}

</script>

<style type="text/css">
.table th { padding: 4px !important; text-align: center; vertical-align: middle; }
</style>

<div class="row">
  <div class="col-md-12 col-sm-12">
    <div class="card">
      <div class="card-header card-head-inverse bg-primary">
        <h4 class="card-title text-white">Negosiasi <?= $paketInfo->metode_lelang_nama ?></h4>
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
          <div class="row">
            <div class="form-group col-md-12 mb-2">
              <div class="col-md-12">
                <ul class="nav nav-tabs process-model more-icon-preocess" role="tablist">
                  <li role="presentation" style="width: 33% !important"><a href="main/index/paket_lelang_tambah_negosiasi_undangan/?reqId=<?=$reqId?>"><i class="fa fa-cogs" aria-hidden="true"></i>
                    <p>Setting Notifikasi Pembuktian</p>
                    </a>
                  </li>
                  <li role="presentation" style="width: 33% !important"><a href="main/index/klarifikasi_chat/?reqId=<?=$reqId?>"><i class="fa fa-check-circle" aria-hidden="true"></i>
                    <p>Pembuktian Dok. Penawaran</p>
                    </a>
                  </li>
                  <li role="presentation" class="active" style="width: 33% !important"><a href="main/index/paket_lelang_tambah_negosiasi/?reqId=<?=$reqId?>"><i class="fa fa-flag" aria-hidden="true"></i>
                    <p>Negosiasi</p>
                    </a>
                  </li>
                </ul>
              </div>
            </div>
          </div>

          <div class="table-responsive">
            <!-- <div class="alert alert-warning">Negosiasi dilakukan untuk mendapatkan satu harga dan teknis terbaik yang sama untuk seluruh pemenang</div> -->
            <table class="table table-bordered table-hover" style="width: 100%">
              <tbody>
                <tr>
                  <td width="20%"> Pekerjaan</td>
                  <td> <?=$reqNamaPaket?> </td>
                </tr>
                <tr>
                  <td width="20%"> Jenis Pekerjaan</td>
                  <td> <?=$reqJenisPekerjaan?> </td>
                </tr>
                <tr>
                  <td> Metode Evaluasi</td>
                  <td> <?=$reqMetodeEvaluasi?>  </td>
                </tr>
                <tr>
                  <td>Upload Berita Acara</td>
                  <td>
                    <form id="ffUpload" method="post" novalidate enctype="multipart/form-data">
                    <input name="reqLinkFile" type="file" multiple class="maxsize-20240" accept="pdf|zip" id="reqLinkFile" value=""/>
                    <br><?= UPLOAD_PDF_ZIP_10MB ?> 
                    <input type="hidden" name="reqId" value="<?=$reqId?>" />
                    <input type="hidden" name="reqNamaDokumen" id="reqNamaDokumen" value="Berita Acara Negosiasi" />
                    <input type="hidden" name="reqJenisDokumen" id="reqJenisDokumen" value="BERITA_ACARA_NEGOSIASI" />
                    <!-- <input type="submit" name="reqSubmit" id="reqSubmit" value="" style="display:none"> -->
                    <br><br>
                    <button type="submit" class="<?= CLASS_BTN_PRIMARY ?> btn-sm" id="btnUpload"><i class="fa fa-upload"></i> Upload Dokumen</button>

                    </form>
                  </td>
                </tr>
                <tr>
                <?php
                  $paket_dokumen = new PaketDokumen();
                  $paket_dokumen->selectByParams(array("PAKET_ID" => $reqId, "JENIS_DOKUMEN" => "BERITA_ACARA_NEGOSIASI"));
                  $paket_dokumen->firstRow();
                  $dokumen = $paket_dokumen->getField("PATH_FILE");
                  if($dokumen == "")
                  {}
                  else
                  {
                  ?>
                  <td>Download Berita Acara Negosiasi</td>
                  <td>
                  <a href="uploads/penawaran/<?=$dokumen?>" target="_blank" class="btn-sm btn-success round">
                    <?= ICON_DOWNLOAD ?> Download
                  </a>
                  </td>
                  <?php
                  }
                  ?>
                </tr>
              </tbody>
            </table>

            <form id="ff" method="post" enctype="multipart/form-data" novalidate>
              <table class="table table-bordered table-hover" style="width: 100%">
                <tr style="background-color: #967adc; color: #fff; padding: 25px 0">
                  <!-- <th rowspan="2" align="center">No.</th> -->
                  <!-- <th rowspan="2" align="center">Uraian</th> -->
                  <!-- <th rowspan="2" align="center">Quantity</th> -->
                  <!-- <th rowspan="2" align="center">Satuan</th> -->
                  <th colspan="1" align="center">Harga Perkiraan Sendiri</th>
                  <th colspan="2" align="center"><?=$arrRekanan[$indexRekananPememenang]?></th>
                  <th colspan="1" align="center">Negosiasi</th>
                  <th colspan="3" align="center">Persetujuan</th>
                </tr>
                <tr style="background-color: #967adc; color: #fff; padding: 25px 0">
                    <!-- <th align="center">Unit Price</th> -->
                    <th align="center">Total</th>
                    <!-- <th align="center">Delivery</th> -->
                    <!-- <th align="center">Unit Price</th> -->
                    <th align="center">Penawaran</th>
                    <th align="center">Penawaran Terkoreksi</th>
                    <!-- <th align="center" <?=$display_pembelian?>>Delivery</th> -->
                    <!-- <th align="center">Unit Price</th> -->
                    <th align="center">Total</th>
                    <th align="center">Chat</th>
                    <th align="center">Nego Item</th>
                    <th align="center">
                    <?php
                      if ($reqMetodeLelang == 1 || $reqMetodeLelang == 7) { // tender & tender cepat
                         echo "Peserta";
                      } else {
                        echo "Penyedia";
                      }?>
                    </th>
                </tr>
                <?php
                $no = 0;
                $style="gelap";
                $totalNegosiasi = 0;
                while($rekanan_paket_penawaran->nextRow())
                {
                  $displayElement = "";
                  $colspan = "";
                  if((int)$rekanan_paket_penawaran->getField("QUANTITY") == 0)
                  {
                    $displayElement = " style='display:none' ";
                    $no = -1;
                    $colspan = "13";
                  }

                    $paketPenawaranId = $rekanan_paket_penawaran->getField("PAKET_PENAWARAN_ID");
                ?>

                <script type="text/javascript">
                    $(document).ready(function() { 
                      
                      showLoad();
                        openPopupChat('main/loadUrl/main/negosiasi_chat/?reqId=<?=$reqId?>&reqPaketPenawaranId=<?=$rekanan_paket_penawaran->getField("PAKET_PENAWARAN_ID")?>');
                        setTimeout(function() { 
                            document.querySelector('.close').click();
                            document.querySelector('.x').click();
                            hideLoad();
                        }, 800);
                    });
                  </script>
                    <tr class="<?=$style?>">
                        <input type="hidden" name="reqQuantity[]" id="reqQuantity<?=$paketPenawaranId?>" value="<?=$rekanan_paket_penawaran->getField("QUANTITY")?>">
                        <input type="hidden" name="reqPaketPenawaranId[]" id="reqPaketPenawaranId<?=$paketPenawaranId?>" value="<?=$rekanan_paket_penawaran->getField("PAKET_PENAWARAN_ID")?>">

                        <td align="right" <?=$displayElement?>>
                          <?=numberToIna($rekanan_paket_penawaran->getField("SUMMARY"))?>
                        </td>
                        <td align="right" <?=$displayElement?>>
                          <?=numberToIna($rekanan_paket_penawaran->getField("UP_".$arrPaketRekananId[$indexRekananPememenang]))?>
                        </td>
 
                        <td align="right" <?=$displayElement?>><?=numberToIna($rekanan_paket_penawaran->getField("SUMKOREKSI_".$arrPaketRekananId[$indexRekananPememenang]))?></td>

                        <td align="center" <?=$display_pembelian?>><?=dateToPageCheck($rekanan_paket_penawaran->getField("DD_".$arrPaketRekananId[$indexRekananPememenang]))?></td>
                        <?php
                        $arrSummary["SUMMARY"][$no] = $rekanan_paket_penawaran->getField("SUMMARY");
                        $arrSummary["SUM_".$arrPaketRekananId[$indexRekananPememenang]][$no] = $rekanan_paket_penawaran->getField("SUM_".$arrPaketRekananId[$indexRekananPememenang]);

                        $paket_negosiasi = new PaketNegoisasi();
                        $paket_negosiasi->selectByParams(array("A.PAKET_PENAWARAN_ID" => $rekanan_paket_penawaran->getField("PAKET_PENAWARAN_ID")));
                        $paket_negosiasi->firstRow();
                        $penawaranNegosiasi =  $paket_negosiasi->getField("UNIT_PRICE");
                        $jumlahNegosiasi =  $paket_negosiasi->getField("TOTAL");
                        $setujui =  $paket_negosiasi->getField("SETUJUI");

                        ?>
                        <!-- <td align="right" <?=$displayElement?>> -->
                          <input type="hidden" name="reqUnitPriceNegosiasi[]" id="reqUnitPriceNegosiasi<?=$paketPenawaranId?>" value="<?=numberToIna($penawaranNegosiasi)?>"  OnFocus="FormatAngka('reqUnitPriceNegosiasi<?=$paketPenawaranId?>')" OnKeyUp="FormatUang('reqUnitPriceNegosiasi<?=$paketPenawaranId?>'); summary(); save('<?=$paketPenawaranId?>', 'reqUnitPriceNegosiasi<?=$paketPenawaranId?>', event); " OnBlur="FormatUang('reqUnitPriceNegosiasi<?=$paketPenawaranId?>')" style="text-align:right" <?php if($setujui == "") { ?>  <?php } else { ?> style=" width:100px; text-align:right" disabled <?php } ?>>
                        <!-- </td>    -->
                        <td align="center" <?=$displayElement?>>
                          <input type="text" name="reqJumlahNegosiasi[]" id="reqJumlahNegosiasi<?=$paketPenawaranId?>" class="form-control" value="<?=numberToIna($jumlahNegosiasi)?>" style="width:50%; text-align:right;background-color:#EDEDED" readonly></td>
                        <td align="center" <?=$displayElement?>>
                          <?php 
                          if ($jumlahNegosiasi) { ?>
                            <a onClick="openPopupChat('main/loadUrl/main/negosiasi_chat/?reqId=<?=$reqId?>&reqPaketPenawaranId=<?=$rekanan_paket_penawaran->getField("PAKET_PENAWARAN_ID")?>')">
                              <span class="fa fa-comments-o fa-2x"></span>
                            </a>
                          <?php 
                          } else { echo '<img src="images/uncentang.png">';} ?>
                        </td>
                        <td align="center">
                          <?php 
                          if ($jumlahNegosiasi) { ?>
                            <a href="main/index/paket_lelang_tambah_negosiasi_item?reqId=<?= $reqId ?>">
                              <span class="fa fa-calculator fa-2x"></span>
                            </a>
                          <?php 
                          } else { echo '<img src="images/uncentang.png">';} ?>
                        </td>
                        <td align="center" <?=$displayElement?>>
                        <label id="setujui<?=$rekanan_paket_penawaran->getField("PAKET_PENAWARAN_ID")?>" <?php if($paket_negosiasi->getField("SETUJUI") == "1") {} else { ?> style="display:none" <?php } ?>>
                           <img src="images/centang.png" style="width: 20px;">
                        </label>
                        </td>
                    </tr>
                <?php
                    $totalTerkecil += $jumlahTerkecil;
                    $totalNegosiasi += $jumlahNegosiasi;
                    unset($arrPenawaran);
                    $no++;
                    if($style == "gelap")
                        $style = "terang";
                    else
                        $style = "gelap";
                }
                ?>
                <!-- <tr>
                    <td></td>
                    <td colspan="3"></td>
                    <td align="right"><?=numberToIna(array_sum($arrSummary["SUMMARY"]))?></td>
                    <td></td>
                    <td></td>
                    <td align="right"><?=numberToIna(array_sum($arrSummary["SUM_".$arrPaketRekananId[$indexRekananPememenang]]))?></td>
                    <td></td>
                    <td <?=$display_pembelian?>></td>
                    <td align="center">
                      <input type="text" name="reqTotal" id="reqTotal" value="<?=numberToIna($totalNegosiasi)?>"  readonly style="width:100px; text-align:right;background-color:#EDEDED" />
                    </td>
                    <td></td>
                    <td></td>
                </tr>    -->
                <!-- <tr>
                  <td colspan="12">&nbsp;&nbsp;</td>
                </tr> -->
                <tr colspan="12" style="display:none">
                    <td >
                        <textarea name="reqRekananIdArray"><?php print_r(serialize($arrPaketRekananId)); ?></textarea>
                    </td>
                </tr>
              </table>
              <?php 
              $paket = new Paket();
              $paket->selectByParams(array("A.PAKET_ID" => coalesce($reqId, 0)));
              $paket->firstRow(); 
              $reqBiddingMulai = $paket->getField("NEGOSIASI_MULAI");
              // Parsing Tanggal Mulai
              $exBiddingMulai = explode(' ',$reqBiddingMulai);
              $exBiddingMulaiDate = explode('-',$exBiddingMulai[0]);

              $errorNego = '';
              if ($jumlahNegosiasi) { } else {
                if ($exBiddingMulai[0]) {
                  $errorNego = '<span class="badge badge-danger ml-1">Notifikasi belum dikirim, <a href="main/index/paket_lelang_tambah_negosiasi_undangan/?reqId='.$reqId.'">klik disini </a> untuk kirim notifikasi</span>';
                }
              }

              echo '<div class="alert alert-info" style="color:#fff">
                      <span style="color: #fff">
                        <u>Negosiasisi Akan dimulai pada: '.getFormattedDate($exBiddingMulai[0]).' '.addWIB($exBiddingMulai[1]).' </u> '.$errorNego.'
                      </span>
                    </div>'; ?>

              <div class="form-actions">
                <a href="main/index/paket_lelang_tambah_negosiasi_undangan/?reqId=<?=$reqId?>" class="<?= CLASS_BTN_DANGER ?> mr-1"> <?= BTN_KEMBALI ?> </a>
                <a href="main/loadUrl/report/negosiasi_cetak_pdf?reqId=<?=$reqId?>" target="_blank" class="<?= CLASS_BTN_INFO ?> mr-1"><?= BTN_PRINT ?> Hasil Negosiasi</a>
                <?php
                if($reqPublishBANegosiasi != "1")
                {}
                else
                {
                if($paket_negosiasi_validasi->getField("JENIS") == "PANITIA")
                {
                    if($paket_negosiasi_validasi->getField("KODE") == "")
                    {
                ?>
                    <a title="#" id="tombolValidasi" onclick="submitValidasi('<?=$paket_negosiasi_validasi->getField("NIP")?>', '<?=$paket_negosiasi_validasi->getField("JENIS")?>')" class="<?= CLASS_BTN_PRIMARY ?> mr-1"><?= BTN_VALIDASI ?></a>
                <?php
                    }
                }
                if($paket_negosiasi_validasi->getField("JENIS") == "PEMBUAT")
                {
                ?>
              <a onClick="publishNegosiasi();" id="btnPublish" class="<?= CLASS_BTN_PRIMARY ?>"><?= BTN_PUBLISH ?></a>
                <?php
                }
                }
                ?>

              </div>
                  <?php /*?><div class="row">
                        <div class="col-md-12">
                            <div class="area-tombol-bawah">
                                <input type="hidden" name="reqPaketRekananId" value="<?=$arrPaketRekananId[0]?>" />
                                  <input type="hidden" name="reqId" value="<?=$reqId?>" />
                                  <input type="hidden" name="submitSimpan" value="Simpan" />
                                  <input type="submit" name="btnSimpan" id="btnSimpan" value="" style="display:none"/>
                                  <a href="main/index/paket_detil/?reqId=<?=$reqId?>" class="btn-batal">Kembali</a>
                                  <a <?php if($submitNegosiasi == true) { ?>
                                      onClick="$('#btnSimpan').click();"
                     <?php } else { ?>
                                      href="main/index/paket_lelang_tambah_negosiasi_undangan/?reqId=<?=$reqId?>&reqMode=lelang"
                     <?php } ?>
                                      class="btn-lanjut pull-right">Lanjut</a>
                            </div>
                        </div>
                    </div><?php */?>
              </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>



<script type="text/javascript">
function save(paketPenawaranId, inputId, event)
{
  if(event.keyCode == "13")
  {
    if(confirm("Ubah data negosiasi ?"))
    {
      var nilai = $("#"+inputId).val();

      $.getJSON("paket_negoisasi_json/negosiasi/?reqId="+paketPenawaranId+"&reqNilai="+nilai, function(json) {
        alert(json);
      });

    }
  }

}


function summary()
{
  var reqTotal = 0;

  $("table input[id^=reqUnitPriceNegosiasi]").each(function() {
    var txtQuantity = $(this).attr("id").replace("reqUnitPriceNegosiasi", "reqQuantity");
    var txtJumlah = $(this).attr("id").replace("reqUnitPriceNegosiasi", "reqJumlahNegosiasi");

    var jumlah = (Number(FormatAngkaNumber($(this).val())) * Number($("#"+txtQuantity).val())).toFixed(2);

    reqTotal = Number(reqTotal) + Number(jumlah);

    // $("#"+txtJumlah).val(FormatCurrencyBaru(jumlah));
    $("#"+txtJumlah).val($(this).val());

  });

  reqTotal = reqTotal.toFixed(2);
  $("#reqTotal").val(FormatCurrencyBaru(reqTotal));
}

</script>


<script>

  $(document).on('click', '#btnUpload', function () {

    let fileInput = $('#reqLinkFile')[0];
    let file = fileInput.files[0];

    if (!file) {
      alert('Silakan pilih file terlebih dahulu');
      return;
    }

    let fileName = file.name.toLowerCase();

    if (!fileName.endsWith('.pdf') && !fileName.endsWith('.zip')) {
      alert('File harus PDF atau ZIP');
      return;
    }

    // validasi tipe
    let allowed = ['application/pdf', 'application/zip', 'application/x-zip-compressed'];
    if (!allowed.includes(file.type)) {
      alert('File harus PDF atau ZIP');
      return;
    }

    // validasi ukuran (10MB)
    if (file.size > 10 * 1024 * 1024) {
      alert('Ukuran file maksimal 10MB');
      return;
    }

    let formData = new FormData();
    formData.append('reqLinkFile', file);
    formData.append('reqId', '<?=$reqId?>');
    formData.append('reqNamaDokumen', 'Berita Acara Negosiasi');
    formData.append('reqJenisDokumen', 'BERITA_ACARA_NEGOSIASI');

    // loading
    $.messager.progress({
      title: 'Proses Upload',
      msg: 'Mengupload dokumen...'
    });

    $.ajax({
      url: 'dokumen_pengadaan_upload_rekanan/upload_negosiasi',
      type: 'POST',
      data: formData,
      processData: false,
      contentType: false,
      success: function (res) {

        $.messager.progress('close');

        if (res === 'Dokumen berhasil diupload.') {
          alertSuccess2(res);
        } else {
          alertError2(res);
        }

        setTimeout(function () {
          location.reload();
        }, 1500);
      },
      error: function () {
        $.messager.progress('close');
        alert('Upload gagal (server error)');
      }
    });

  });

  // $(document).ready(function() {
    // $(function(){
    //   $('#ffUpload').form({
    //     url:'dokumen_pengadaan_upload_rekanan/upload_negosiasi',
    //     onSubmit:function(){
    //       if($(this).form('validate'))
    //       {
    //       var win = $.messager.progress({
    //                     title:'Proses Upload',
    //                     msg:'Mengupload dokumen...'
    //                   });
    //       }
    //       else
    //         $('input:file').MultiFile('reset');
    //       return $(this).form('validate');
    //     },
    //     success:function(data){
    //       // alert(data);
    //       if (data === 'Dokumen berhasil diupload.') { alertSuccess2(data);
    //       } else {
    //         alertError2(data);
    //       }
    //       $.messager.progress('close');
    //       setTimeout(function() {
    //         document.location.reload();
    //       }, 2000);
    //     }
    //   });
    // });
  // });

  function publishNegosiasi()
  {
    if(confirm("Publish hasil negosiasi ?"))
    {

      $.getJSON('paket_json/setPublishNegosiasi/?reqId=<?=$reqId?>', function (data)
      {
        if(data.STATUS == "1")
        {
          $("#btnPublish").css("display", "none");
          alert("Publish hasil negosiasi berhasil.");
        }
        else
          alert(data.STATUS);
      });

    }

  }

  function submitValidasi(kode, jenis)
  {
    if(confirm("Validasi hasil negosiasi ?"))
    {
      $.getJSON('paket_negosiasi_validasi_json/negosiasi?reqId=<?=$reqId?>&reqKode='+kode+'&reqJenis='+jenis,
      function(data){
        alert(data.PESAN);
        $("#tombolValidasi").css("display", "none");
      });
    }
  }
</script>
