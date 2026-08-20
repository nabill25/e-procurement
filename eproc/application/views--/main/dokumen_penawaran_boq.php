<?php
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 * 
 */

$this->libsession->cekSession($this->input->get("reqId"));

$this->load->library("kauth");  $userLogin = new kauth();
$this->load->library("paketinfo"); $paketInfo = new paketinfo();
$this->load->model("PaketDokumen");
$this->load->model("PaketRekanan");
$this->load->model("RekananPaketPenawaran");
$this->load->model("PaketPenawaran");
$this->load->model("PaketTahap");
include_once("functions/string.func.php");
include_once("functions/date.func.php");
include_once("functions/default.func.php");
include_once("functions/recordcoloring.func.php");
//include_once("lib/php-excel-reader-2.21/excel_reader2.php");

$reqMode = httpFilterRequest("reqMode");
$reqId = httpFilterRequest("reqId");
$reqCetak= httpFilterPost('reqCetak');
$reqNamaDokumen= httpFilterPost('reqNamaDokumen');
$reqDokumenId = httpFilterPost('reqDokumenId');
$reqKeterangan= httpFilterPost('reqKeterangan');
$reqBayar= httpFilterPost('reqBayar');
$reqDokumenId = httpFilterGet('reqDokumenId');
$submitSimpan= httpFilterPost('submitSimpan');
$submitBOQ = httpFilterPost('submitBOQ');

$reqLinkFile= $_FILES['reqLinkFile'];
$reqBoqKolom = $_POST["reqBoqKolom"];
$reqLinkFileTemp = $_POST["reqLinkFileTemp"];
$reqUnitPrice = $_POST["reqUnitPrice"];
$reqDeliveryDate = $_POST["reqDeliveryDate"];
$reqQuantity = $_POST["reqQuantity"];
$reqJumlah = $_POST["reqJumlah"];
$reqPaketPenawaranId = $_POST["reqPaketPenawaranId"];

$rekanan_paket_penawaran = new RekananPaketPenawaran();
$paket_dokumen = new PaketDokumen();
$paket_dokumen_rekanan = new PaketDokumen();
$paket_rekanan = new PaketRekanan();
$paket_penawaran = new PaketPenawaran();
$paket_tahap_metode = new PaketTahap();
$paket_tahap = new PaketTahap();

//$file = new FileHandler();

$reqPaketRekananId = $paket_rekanan->getPaketRekananId($reqId, $this->ID);

$FILE_DIR_ARITMATIKA = "uploads/aritmatika/";
$FILE_DIR = "uploads/penawaran/";


$paketInfo->getPaket($reqId);
$reqNama = $paketInfo->nama;
$reqUUID = $paketInfo->uuid;
$reqMetodeLelangID = $paketInfo->metode_lelang_id;

$adaBiayaPengiriman = $paket_penawaran->getCountByParams(array("PAKET_ID" => $reqId), " AND BIAYA_KIRIM > 0");
//echo $adaBiayaPengiriman;exit;

$rekanan_paket_penawaran = new RekananPaketPenawaran();
$rekanan_paket_penawaran->selectByParamsRekanan($reqPaketRekananId, array("A.PAKET_ID" => $reqId, "ITEM_CHILD" => "0"));
//echo $rekanan_paket_penawaran->query;exit;

/* VALIDASI */
$paket_rekanan->selectByParamsPaketLelang(array("PAKET_ID" => $reqId, "REKANAN_ID" => $this->ID), -1, -1, " AND TANGGAL_DAFTAR IS NOT NULL ");
$paket_rekanan->firstRow();
if($paket_rekanan->getField("PAKET_REKANAN_ID") == "")
  exit;

$kirimPenawaran = $paket_rekanan->getField("KIRIM_PENAWARAN"); // 0: belum kirim, 1: sudah kirim

/* VALIDASI WAKTU HABIS */
$jenis_tahap = $paket_tahap_metode->getJenisTahapById($reqId);

$arrDokumenPenawaran            = DOKUMEN_PENAWARAN; // ikn

$aktif_dok_penawaran1 = $paket_tahap->getCountByParamsAktif(array("URUT" => $arrDokumenPenawaran[$jenis_tahap], "PAKET_ID" => $reqId, "TAMPILKAN" => 1, "HADIR" => 0));
// echo $aktif_dok_penawaran1; die();
if($aktif_dok_penawaran1 == 0) // waktu nya habis
  $kirimPenawaran = "1"; // dianggap sudah melakukan penawaran

if($paketInfo->sistem_harga == '1HARGA')
{
  $displayLokasi = 'style="display:none"';
}

?>
<script src="lib/uploadify/jquery.uploadify.min.js" type="text/javascript"></script>
<script type="text/javascript">
$(document).ready(function() {
  $(function(){
    $('#ff').form({
      url:'rekanan_paket_penawaran_json/dokumen_pengadaan_penawaran_rekanan',
      onSubmit:function(){
        // return $(this).form('validate');
        var v=$(this).form('validate');
        if(v) {
          showLoad();
          return v;
        } else {
          hideLoad();
          return false;
        }
      },
      success:function(data){
        // console.log(data); return false;
        // alert(data);return false;
        //$.messager.alert('Info', data, 'info');
        hideLoad();
        if(data == '0') {
          $.messager.alert('Info', 'Isi terlebih dahulu nilai penawaran anda.', 'info');
        }
        else if(data == '3') {
          $.messager.alert('Info', 'Waktu penawaran telah berakhir.', 'info');
        }
        else if(data == '100') {
          alertError2('Nilai Penawaran melebihi Harga Perkiraan, silahkan isi dibawah Harga Perkiraan.');
          // $.messager.alert('Info', 'Nilai Penawaran melebihi Harga Perkiraan, silahkan isi dibawah Harga Perkiraan.', 'info');
        }
        else {
          alertSuccess2('Berhasil simpan');
        }
        setTimeout(function(){
           window.location.reload(1);
        }, 1000); 
      }
    });

  });

});

function summary()
{
  var reqTotal = 0;

  $("table input[id^=reqUnitPrice]").each(function() {
    var txtQuantity = $(this).attr("id").replace("reqUnitPrice", "reqQuantity");
    var txtJumlah = $(this).attr("id").replace("reqUnitPrice", "reqJumlah");

    var jumlah = (Number(FormatAngkaNumber($(this).val())) * Number($("#"+txtQuantity).val()));

    reqTotal = reqTotal + jumlah;

    $("#"+txtJumlah).val(FormatCurrency(jumlah));

  });

  var reqPPN = Number(reqTotal) * Number(0.10);
  reqPPN = Math.round(reqPPN);
  var reqTotalPPN = reqTotal + reqPPN;

  $("#reqTotal").val(FormatCurrency(reqTotal));
  $("#reqPPN").val(FormatCurrency(reqPPN));
  $("#reqTotalPPN").val(FormatCurrency(reqTotalPPN));

}

function summaryBiayaPengiriman()
{
  var reqTotalPengiriman = 0;

  $("table input[id^=reqBiayaKirim]").each(function() {
    //alert($(this).val());
    reqTotalPengiriman = reqTotalPengiriman + Number(FormatAngkaNumber($(this).val()));
  });

  $("#reqTotalPengiriman").val(FormatCurrency(reqTotalPengiriman));
}
</script>
<link rel="stylesheet" type="text/css" href="lib/uploadify/uploadify.css">

 <div class="row">
  <div class="col-md-12 col-sm-12">
    <div class="card">
      <div class="card-header card-head-inverse bg-primary">
        <h4 class="card-title text-white"><?=translate("Nilai Penawaran", "Value Documents")?></h4>
        <a class="heading-elements-toggle"><i class="fa fa-ellipsis-v font-medium-3"></i></a>
        <div class="heading-elements">
            <ul class="list-inline mb-0">
              <li><a data-action="expand"><i class="ft-maximize"></i></a></li>
              <!-- <li><a data-action="close"><i class="ft-x"></i></a></li> -->
            </ul>
        </div>
      </div>
      <div class="card-content collapse show border-info border-darken-2">
        <div class="card-body area-datatable">
          <div class="row">
            <div class="form-group col-md-12 mb-2">
              <ul class="nav nav-tabs nav-iconfall">
                <li class="nav-item">
                  <a class="nav-link active show">
                    <i class="fa fa-money"></i> <h4> 1. Nilai Penawaran</h4>
                  </a>
                </li>
                <li class="nav-item" style="cursor:not-allowed">
                  <a class="nav-link" style="cursor:not-allowed">
                    <button class="btn" style="border-radius: 20px; padding: 2% 7%; opacity: .4; cursor: not-allowed;"> 2. Dokumen Penawaran</button>
                  </a>
                </li>
                <?php
                if($kirimPenawaran == "1")
                {
                ?>
                <?php
                }
                else {
                ?>
                <li class="nav-item" style="cursor:not-allowed">
                  <a class="nav-link" style="cursor:not-allowed">
                    <button class="btn" style="border-radius: 20px; padding: 2% 7%; opacity: .4; cursor:not-allowed;"> 3. Masukkan Kode Verifikasi </button>
                  </a>
                </li>
                <?php
                }
                ?>
              </ul> 
            </div>
          </div>

          <?php
            if($aktif_dok_penawaran1 == 0)
            {
              if($paket_rekanan->getField("KIRIM_PENAWARAN") == "1")
              {}
              else
              {
            ?>
              <div class="alert alert-danger"> 
                <span>Waktu penawaran telah berakhir</span>
              </div>
            <?php
              }
            }
            ?>

          <div class="table-responsive">
            <!-- <form id="ff" method="post" novalidate enctype="multipart/form-data">  -->

              <table class="table table-bordered">
                <tr>
                  <?php
                  // if($paketInfo->sistem_ppn == "PISAH")
                  //   $sistem_ppn = ".";
                  // elseif($paketInfo->sistem_ppn == "GABUNG")
                    $sistem_ppn = ", Pastikan penawaran yang anda masukkan <strong>termasuk PPN yang berlaku</strong>";
                  // else
                    // $sistem_ppn = ", Pastikan penawaran yang anda masukkan <strong>tidak termasuk PPN 10%</strong>";
                    if ($kirimPenawaran == '1') { }
                    else {
                  ?>
                  <td colspan="5" style="background-color: #967adc; color: #fff"> Masukkan Total (termasuk PPN) pada item penawaran di bawah ini  dan Lampirkan Rincian BoQ Penawaran anda<?= $sistem_ppn?></td>
                  <?php
                    } ?>
                </tr>
                <tr>
                  <td colspan="5">
                    <form id="ff" method="post" enctype="multipart/form-data" novalidate>
                    <table class="table table-striped">
                      <tr class="judul-kolom">
                        <!-- <th align="center">Lot</th> -->
                        <th align="center">Item</th>
                        <th align="center" style="width: 5%">Satuan</th>
                        <!-- <th align="center" style="width: 5%">Quantity</th> -->
                        <th align="center" style="width: 30%">Total (termasuk PPN)</th>
                        <!-- <th align="center" style="width: 20%">Total</th> -->
                         <?php
                         if($adaBiayaPengiriman > 0)
                         {
                         ?>
                          <th align="center">Biaya Kirim</th>
                         <?php
                         }
                         ?>
                      </tr>
                      <?php
                      $style="gelap";
                      $i = 0;
                      $grand_total = 0;
                      $biaya_kirim = 0;
                      $bolehLanjut = "1";
                      while($rekanan_paket_penawaran->nextRow())
                      {
                      $style = "";
                      $required = "required";
                      $quantity = $rekanan_paket_penawaran->getField("QUANTITY");
                      if($quantity == 0)
                      {
                        $style = 'style="display:none"';
                        $required = "";
                      }
                      ?>
                      <tr class="<?=$style?>">
                        <!-- <td valign="top"><?=$rekanan_paket_penawaran->getField("ITEM_NUMBER")?></td> -->
                        <td valign="top"><?=$rekanan_paket_penawaran->getField("ITEM")?></td>
                        <td valign="top" <?=$style?> style="text-align: center"><?=$rekanan_paket_penawaran->getField("SATUAN")?></td>
                        <!-- <td valign="top" <?php // echo $style?> style="text-align: center">
                         <?php // echo $rekanan_paket_penawaran->getField("QUANTITY")?></td> -->
                        <td valign="top" <?=$style?>>
                          <?php
                          // if($kirimPenawaran == 1)
                          if($aktif_dok_penawaran1 == 0) // habis waktu penawaran
                          {
                          ?>
                            <input type="text" name="reqUnitPrice[]" class="form-control span2" id="reqUnitPrice<?=$i?>" value="<?=numberToIna($rekanan_paket_penawaran->getField("JUMLAH"))?>"  readonly style="text-align:right;background-color:#EDEDED" />
                         <?php
                          } else

                          {
                            if ($kirimPenawaran == '1') { $readOnly = "readonly";
                            } else { $readOnly = ""; }
                           ?>

                            <input  class="form-control easyui-validatebox span2" required=""
                            type="text" style="width: 100%"
                            name="reqUnitPrice[]"
                            id="reqUnitPrice<?=$i?>"
                            value="<?=numberToIna($rekanan_paket_penawaran->getField("JUMLAH"))?>"
                            OnFocus="FormatAngka('reqUnitPrice<?=$i?>')" OnKeyUp="FormatUang('reqUnitPrice<?=$i?>'); summary();" OnBlur="FormatUang('reqUnitPrice<?=$i?>')" <?= $readOnly; ?> >
                             <?php
                             // if($rekanan_paket_penawaran->getField("BOQ_FILE") == "")
                             // {}
                             // else
                             // {
                              if ($kirimPenawaran == '1') { }
                              else {
                             ?>
                           <br>  
                           <?php 
                           // if ($reqMetodeLelangID == '2') { // Khusus Pengadaan Langusung Upload BoQ
                            ?>
                            <b>Upload File .xls atau xlsx</b> 
                            <input type="file" name="reqLinkFile[]" id="reqLinkFile<?=$i?>" class="easyui-validatebox" validType="fileType['xls','xlsx']" <?= $readOnly; ?>/>
                           <?php
                              // }
                            }
                           // }
                           ?>
                           <?php
                          }
                          ?>

                          <?php
                           if ($rekanan_paket_penawaran->getField("JUMLAH") == null || $rekanan_paket_penawaran->getField("JUMLAH") == '' || $rekanan_paket_penawaran->getField("JUMLAH") == 0) {
                              echo '<span class="badge badge-danger mt-1">Nilai Penawaran Masih Kosong</span>';
                            } ?> 
                            <?php
                           // if ($reqMetodeLelangID == '2') { // Khusus Pengadaan Langusung Upload BoQ

                             if($rekanan_paket_penawaran->getField("BOQ_REKANAN") == "")
                               { ?><br>
                                <span class="badge badge-danger">Penawaran belum diupload</span>
                                <br>
                               <?php
                               }
                               else
                               {
                               ?>
                               <br>
                               <b> Rincian Penawaran Anda : <a href="uploads/penawaran/<?=$rekanan_paket_penawaran->getField("BOQ_REKANAN")?>" target="_blank" class="badge badge-pill badge-primary"><span class="fa fa-download"></span> download</a> </b>
                               <br>
                               <?php
                               }
                             // }
                           ?>

                        </td>
                        <!-- <td valign="top" <?=$style?>> -->
                          <input type="hidden" name="reqJumlah[]" class="span2" id="reqJumlah<?=$i?>" value="<?=numberToIna($rekanan_paket_penawaran->getField("JUMLAH"))?>"  readonly style="text-align:right;background-color:#EDEDED" />
                          <input type="hidden" name="reqQuantity[]" id="reqQuantity<?=$i?>" value="<?=$rekanan_paket_penawaran->getField("QUANTITY")?>">
                          <input type="hidden" name="reqPaketPenawaranId[]" value="<?=$rekanan_paket_penawaran->getField("PAKET_PENAWARAN_ID")?>">
                          <input type="hidden" name="reqBoqKolom[]" value="<?=$rekanan_paket_penawaran->getField("BOQ_KOLOM")?>" />
                          <input type="hidden" name="reqLinkFileTemp[]" value="<?=$rekanan_paket_penawaran->getField("BOQ_REKANAN")?>" />
                        <!-- </td> -->
                        <?php
                         if($adaBiayaPengiriman > 0)
                         {
                         ?>
                        <td valign="top" <?=$style?>>
                           <input  class="easyui-validatebox"
                                  type="text"
                                  name="reqBiayaKirim[]"
                                  id="reqBiayaKirim<?=$i?>"
                                  value="<?=numberToIna($rekanan_paket_penawaran->getField("BIAYA_KIRIM"))?>"
                                  OnFocus="FormatAngka('reqBiayaKirim<?=$i?>')"
                                  OnKeyUp="FormatUang('reqBiayaKirim<?=$i?>'); summaryBiayaPengiriman();"
                                  OnBlur="FormatUang('reqBiayaKirim<?=$i?>')"
                              <?php if($rekanan_paket_penawaran->getField("BIAYA_KIRIM") > 0)  { ?> <?=$required?> style="text-align:right" <?php } else { ?> readonly style="text-align:right;background-color:#EDEDED" <?php } ?> />
                          </td>
                         <?php
                         }
                         ?>
                      </tr>

                      <?php

                      $grand_total += $rekanan_paket_penawaran->getField("JUMLAH");
                      $biaya_kirim += $rekanan_paket_penawaran->getField("BIAYA_KIRIM_REKANAN");

                      if($rekanan_paket_penawaran->getField("JUMLAH") == "0" || $rekanan_paket_penawaran->getField("JUMLAH") == "")
                      {
                        if($required == "required")
                          $bolehLanjut = "0";
                      }

                      $i++;
                      $rekanan_paket_penawaran_child = new RekananPaketPenawaran();
                      $rekanan_paket_penawaran_child->selectByParamsRekanan($reqPaketRekananId, array("A.PAKET_ID" => $reqId, "ITEM_CHILD" => $rekanan_paket_penawaran->getField("ITEM_PARENT")));

                      while($rekanan_paket_penawaran_child->nextRow())
                      {
                      ?>
                      <tr>
                        <td>
                        </td>
                        <td>
                          <?=$rekanan_paket_penawaran_child->getField("ITEM")?>
                        </td>
                        <td>
                         <?=$rekanan_paket_penawaran_child->getField("SATUAN")?>
                        </td>
                        <td>
                         <?=$rekanan_paket_penawaran_child->getField("QUANTITY")?>
                        </td>
                        <td>
                        <?php
                        if($kirimPenawaran == 1)
                        {
                        ?>
                        <input type="text" name="reqUnitPrice[]" class="span2" id="reqUnitPrice<?=$i?>" value="<?=numberToIna($rekanan_paket_penawaran_child->getField("JUMLAH"))?>"  readonly style="text-align:right;background-color:#EDEDED" />
                         <?php
                        }else
                        {
                         ?>
                          <input class="easyui-validatebox span2" type="text" name="reqUnitPrice[]" id="reqUnitPrice<?=$i?>"
                             value="<?=numberToIna($rekanan_paket_penawaran_child->getField("JUMLAH"))?>"
                             OnFocus="FormatAngka('reqUnitPrice<?=$i?>')" OnKeyUp="FormatUang('reqUnitPrice<?=$i?>'); summary();"
                             OnBlur="FormatUang('reqUnitPrice<?=$i?>')"
                            <?php if($rekanan_paket_penawaran->getField("BOQ") == "")  { ?>
                                 required style="text-align:right"
                            <?php } else { ?>
                                 readonly style="text-align:right;background-color:#EDEDED" <?php } ?> />
                            <?php } ?>
                        </td>
                        <td>
                          <input type="text" name="reqJumlah[]" class="span2" id="reqJumlah<?=$i?>" value="<?=numberToIna($rekanan_paket_penawaran_child->getField("JUMLAH"))?>"  readonly style="text-align:right;background-color:#EDEDED" />

                          <input type="hidden" name="reqQuantity[]" id="reqQuantity<?=$i?>" value="<?=$rekanan_paket_penawaran_child->getField("QUANTITY")?>">
                          <input type="hidden" name="reqPaketPenawaranId[]" value="<?=$rekanan_paket_penawaran_child->getField("PAKET_PENAWARAN_ID")?>">
                          <input type="hidden" name="reqBoqKolom[]" value="<?=$rekanan_paket_penawaran_child->getField("BOQ_KOLOM")?>" />
                          <input type="hidden" name="reqLinkFileTemp[]" value="<?=$rekanan_paket_penawaran_child->getField("BOQ_REKANAN")?>" />

                        </td>
                        <?php
                         if($adaBiayaPengiriman > 0)
                         {
                         ?>
                        <td valign="top">
                         <input  class="easyui-validatebox"
                            type="text"
                            name="reqBiayaKirim[]"
                            id="reqBiayaKirim<?=$i?>"
                            value="<?=numberToIna($rekanan_paket_penawaran_child->getField("BIAYA_KIRIM_REKANAN"))?>"
                            OnFocus="FormatAngka('reqBiayaKirim<?=$i?>')"
                            OnKeyUp="FormatUang('reqBiayaKirim<?=$i?>'); summaryBiayaPengiriman();"
                            OnBlur="FormatUang('reqBiayaKirim<?=$i?>')"
                            <?php if($rekanan_paket_penawaran_child->getField("BIAYA_KIRIM") > 0)  { ?> <?=$required?> style="text-align:right" <?php } else { ?> readonly style="text-align:right;background-color:#EDEDED" <?php } ?> />
                        </td>
                         <?php
                         }
                         ?>
                      </tr>
                      <?php
                      $i++;
                      $grand_total += $rekanan_paket_penawaran_child->getField("JUMLAH");
                      $biaya_kirim += $rekanan_paket_penawaran_child->getField("BIAYA_KIRIM_REKANAN");

                      if($rekanan_paket_penawaran_child->getField("JUMLAH") == "0" || $rekanan_paket_penawaran_child->getField("JUMLAH") == "")
                        $bolehLanjut = "0";

                        }
                      }
                      if($grand_total > 0)
                        $tokenUnitPrice = rand();
                      else
                        $tokenUnitPrice = 0;
                      ?>
                      <!-- <tr class="<?=$style?>">
                        <td colspan="4" style="text-align:right">Grand Total</td>
                         <td>
                        <input type="text" name="reqTotal" id="reqTotal" class="span2" readonly style="text-align:right;background-color:#EDEDED" value="<?=numberToIna($grand_total)?>" />
                        </td> -->
                       <?php
                        if($adaBiayaPengiriman > 0)
                        {
                        ?>
                        <!-- <td> -->
                           <input type="hidden" name="reqTotalPengiriman" id="reqTotalPengiriman"  readonly style="text-align:right;background-color:#EDEDED" value="<?=numberToIna($biaya_kirim)?>" />
                        <!-- </td> -->
                        <?php
                        }
                        ?>
                      <!-- </tr> -->
                      <?php
                      if($paketInfo->sistem_ppn == "PISAH")
                      {
                        $ppn = round($grand_total * 0.10);
                        $totalHargaPPN = $grand_total + $ppn;
                      ?>
                      <tr class="<?=$style?>">
                        <td colspan="<?=$colspan?>" style="text-align:right">PPN</td><td>
                          <input type="text" name="reqPPN" id="reqPPN"
                           readonly style="ext-align:right;background-color:#EDEDED;" value="<?=numberToIna($ppn)?>" />
                        </td>
                      </tr>
                      <tr class="<?=$style?>">
                        <td colspan="<?=$colspan?>" style="text-align:right">Grand Total + PPN</td><td>
                          <input type="text" name="reqTotalPPN" id="reqTotalPPN"
                           readonly style="ext-align:right;background-color:#EDEDED;" value="<?=numberToIna($totalHargaPPN)?>" />
                        </td>
                      </tr>
                      <?php
                      }
                      ?>
                      <tr class="<?=$style?>">
                        <td colspan="8">
                          <input type="hidden" name="reqId" value="<?=$reqId?>" />
                          <input type="hidden" name="reqPaketRekananId" value="<?=$reqPaketRekananId?>" />
                          <input type="hidden" name="submitSimpan" value="Simpan" />

                        </td>
                      </tr>
                    </table>
                  </td>
                </tr>
                <!-- <tr>
                  <td colspan="5">&nbsp;</td>
                </tr> -->
                <tr>
                  <td colspan="3">
                    <a href="main/index/paket_detil/?eid=<?=$reqId?>&key=<?=$reqUUID?>" class="<?= CLASS_BTN_DANGER ?>"><i class="fa fa-arrow-left"></i> Kembali</a>
                  </td>

                  <td colspan="2" align="right">
                   <?php
                    if($aktif_dok_penawaran1 == 0)
                    {}
                    else
                    {
                      if ($kirimPenawaran == '1') { }
                      else {
                    ?>
                      <?php /*?><input type="submit"  value="Simpan" class="btn-simpan" /><?php */?>
                      <!-- <div style="display: none"> -->
                      <?php
                      if($rekanan_paket_penawaran->getField("BOQ_REKANAN") == "")
                      { ?>
                        <button type="submit" name="reqSubmit" id="reqSubmit" class="<?= CLASS_BTN_SUCCESS ?>" > <i class="fa fa-check-square-o"></i> Simpan Penawaran </button>
                      <?php
                    } else { ?>
                      <button type="submit" name="reqSubmit" id="reqSubmit" class="<?= CLASS_BTN_SUCCESS ?>" > <i class="fa fa-check-square-o"></i> Ubah Penawaran </button>
                      <?php
                    } ?> 
                    <?php
                      }
                    }
                    ?>

                    <?php
                     // if ($rekanan_paket_penawaran->getField("JUMLAH") == null || $rekanan_paket_penawaran->getField("JUMLAH") == '' || $rekanan_paket_penawaran->getField("JUMLAH") == 0 || $rekanan_paket_penawaran->getField("BOQ_REKANAN") == "") {
                     if ($rekanan_paket_penawaran->getField("JUMLAH") == null || $rekanan_paket_penawaran->getField("JUMLAH") == '' || $rekanan_paket_penawaran->getField("JUMLAH") == 0) {
                     } else {
                      ?>
                      <a href="main/index/dokumen_penawaran_rekanan/?reqId=<?=$reqId?>" class="<?= CLASS_BTN_PRIMARY ?>"> Lanjut <i class="fa fa-arrow-right"></i></a>
                    <?php
                    } ?>
                  <?php /*?><a onClick="prosesSelanjutnya()" class="btn-lanjut">Lanjut</a><?php */?>
                  </td>
                </tr>
              </table>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>


<script type="text/javascript">
  function prosesSelanjutnya()
  {
    <?php
      if($bolehLanjut != "0")
      {
    ?>
      document.location.href = 'main/index/dokumen_penawaran_rekanan/?reqId=<?=$reqId?>';
    <?php
      }
      else
      {
        echo "$.messager.alert('Info', 'Isi terlebih dahulu nilai penawaran anda.', 'info');";
      }
    ?>
  }

</script>
