<?php
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 * 
 */
if($this->USER_TYPE_ID == "")
    redirect("main");

$this->load->library("kauth");  $userLogin = new kauth(); 
$this->load->library("paketinfo"); $paketInfo = new paketinfo();
include_once("functions/string.func.php");
include_once("functions/default.func.php");
include_once("functions/date.func.php");
$this->load->model("PaketRekanan");
$this->load->model("Paket");
$this->load->model("PaketNegoisasi");
$this->load->model("RekananPaketPenawaran");
$this->load->model("Rekanan");


$paket_rekanan = new PaketRekanan();
$paket_nilai = new Paket();
$paket_nilai_estimate = new Paket();
$rekanan_paket_penawaran = new RekananPaketPenawaran();

$reqId = httpFilterRequest("reqId");
$reqNilaiEstimate = httpFilterPost("reqNilaiEstimate");
$reqDataPenawaranHarga = $_POST["reqDataPenawaranHarga"];
$reqRekananIdArray =unserialize(stripslashes($_POST['reqRekananIdArray']));
$submitSimpan = httpFilterPost("submitSimpan");


$paketInfo->getPaket($reqId);
$reqNamaPaket = $paketInfo->nama;
$reqNamaPaket = $paketInfo->nama;
$reqJenisPekerjaan = $paketInfo->jenis;
$reqJenisPekerjaanId = $paketInfo->jenis_id;
$reqMetodeEvaluasi = $paketInfo->metode_evaluasi;
$reqMetodeEvaluasiId = $paketInfo->metode_evaluasi_id;
$reqOwnerEstimate  = $paketInfo->nilai_owner_estimate;
$reqRekananIdPemenang = $paketInfo->rekanan_id_pemenang;
$reqMetodePengadaan = $paketInfo->metode_lelang_id;
$reqJenisPegadaaan = $paketInfo->jenis_pengadaan;
$paket_rekanan->selectByParams(array("A.PAKET_ID" => $reqId, "A.REKANAN_ID" => coalesce($reqRekananIdPemenang, 0)), -1, -1, " AND A.TANGGAL_DAFTAR IS NOT NULL AND A.LULUS_PENDAFTARAN = 1 AND A.LULUS_KUALIFIKASI = 1 ");

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

$paket_nilai->selectByParams(array("PAKET_ID" => $reqId));
$paket_nilai->firstRow();
$reqNilaiEstimate = $paket_nilai->getField("NILAI_OWNER_ESTIMATE");
$reqJenisPengadaan = $paket_nilai->getField("JENIS_PENGADAAN");
$reqNilaiNegosiasi = $paket_nilai->getField("NILAI_NEGOSIASI");



$submitNegosiasi = true;
if($arrDiemailNegosiasi[$indexRekananPememenang] > 0)
{
	echo '<script language="javascript">';
	echo 'alert("Rekanan telah diemail, anda tidak dapat mengubah nilai negosiasi.");';
	echo '</script>';
	$submitNegosiasi = false;
}

$rekanan_paket_penawaran->selectByParamsEvaluasiRekanan($arrPaketRekananId, array("A.PAKET_ID" => $reqId));

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

<div class="row"> 
  <div class="col-md-12 col-sm-12">
    <div class="card">
      <div class="card-header card-head-inverse bg-primary">
        <h4 class="card-title text-white">Setup Negosiasi</h4>
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
              <a href="main/index/paket_lelang_tambah_negosiasi_setup/?reqId=<?=$reqId?>" class="btn btn-primary disabled"> <span class="fa fa-cogs"></span> Setup Negosiasi</a>
              <a href="main/index/paket_lelang_tambah_negosiasi_undangan/?reqId=<?=$reqId?>" class="btn btn-primary"><span class="fa fa-send"></span> Kirim Undangan Negosiasi</a>
            </div> 
          </div>
          <table class="table table-bordered table-hover">
                <tr>
                  <td width="30%"> Pekerjaan</td>
                  <td> <?=$reqNamaPaket?> </td>
                </tr> 
                <tr>
                  <td width="20%"> Jenis Pekerjaan</td>
                  <td> <?=$reqJenisPekerjaan?> </td>
                </tr> 
                <tr>
                  <td width="20%"> Metode Evaluasi</td>
                  <td> <?=$reqMetodeEvaluasi?> </td>
                </tr> 
              </table>
          <form id="ff" method="post" enctype="multipart/form-data" novalidate>
            <div class="table-responsive">
              <table class="table table-bordered table-hover">
                  <tr style="background-color: #967adc; color: #fff; padding: 25px 0">
                    <th colspan="1" style="text-align: center">HPS</th>
                    <th colspan="1" style="text-align: center"><?=$arrRekanan[$indexRekananPememenang]?></th>
                    <th colspan="1" style="text-align: center">Negosiasi</th>
                  </tr>       
                  <tr style="background-color: #967adc; color: #fff; padding: 25px 0">
                      <!-- <th style="text-align: center">Unit Price</th> -->
                      <th style="text-align: center">Total</th>
                      <!-- <th style="text-align: center">Delivery</th> -->
                      <!-- <th style="text-align: center">Unit Price</th> -->
                      <th style="text-align: center">Penawaran Terkoreksi</th>
                      <!-- <th style="text-align: center">Unit Price</th> -->
                      <th style="text-align: center">Total</th>
                  </tr>
                  <?
                  $no = 0;
                  $style="gelap";
                  $totalNegosiasi = 0;
                  while($rekanan_paket_penawaran->nextRow())
                  {
                      $displayElement = "";
                      if((int)$rekanan_paket_penawaran->getField("QUANTITY") == 0)
                          $displayElement = " style='display:none' ";
                      
                  ?>                     
                      <tr class="<?=$style?>">
                              <input type="hidden" name="reqQuantity[]" id="reqQuantity<?=$no?>" value="<?=$rekanan_paket_penawaran->getField("QUANTITY")?>">
                              <input type="hidden" name="reqPaketPenawaranId[]" id="reqPaketPenawaranId<?=$no?>" value="<?=$rekanan_paket_penawaran->getField("PAKET_PENAWARAN_ID")?>">
                              
                          <!-- <td align="right" <?=$displayElement?>><?=numberToIna($rekanan_paket_penawaran->getField("OE"))?></td>     -->
                          <td align="right" <?=$displayElement?>>
                            <?=numberToIna($rekanan_paket_penawaran->getField("SUMMARY"))?>   
                          </td>
                          <!-- <td align="center" <?=$displayElement?>><?=dateToPageCheck($rekanan_paket_penawaran->getField("DELIVERY_DATE"))?></td> -->
                      
                          <!-- <td <?=$displayElement?>> -->
                               <?php //numberToIna($rekanan_paket_penawaran->getField("UPK_".$arrPaketRekananId[$indexRekananPememenang]))?>
                          <!-- </td> -->
                          <td align="right" <?=$displayElement?>>
                              <?=numberToIna($rekanan_paket_penawaran->getField("SUMKOREKSI_".$arrPaketRekananId[$indexRekananPememenang]))?>
                            <?php 
                            $totalSum += $rekanan_paket_penawaran->getField("SUMKOREKSI_".$arrPaketRekananId[$indexRekananPememenang]);
                           ?>  
                          </td> 
                          <?
                          $arrSummary["SUMMARY"][$no] = $rekanan_paket_penawaran->getField("SUMMARY");
                          $arrSummary["SUM_".$arrPaketRekananId[$indexRekananPememenang]][$no] = $rekanan_paket_penawaran->getField("SUMKOREKSI_".$arrPaketRekananId[$indexRekananPememenang]);
                          
                          /* AMBIL NILAI TERKECIL */
                          $arrPenawaran[]=0;
                          for($i=0;$i<count($arrRekanan);$i++)
                          {
                              $arrPenawaran[$i] = coalesce($rekanan_paket_penawaran->getField("UPK_".$arrPaketRekananId[$i]), $rekanan_paket_penawaran->getField("OE"));      
                              
                          }
                          
                              $paket_negosiasi = new PaketNegoisasi();
                              $penawaranTerkecil = min($arrPenawaran);                                

                              $jumlahTerkecil =  round($penawaranTerkecil * toNumber($rekanan_paket_penawaran->getField("QUANTITY")), 2);
                                                      
                              $penawaranNegosiasi = $paket_negosiasi->getUnitPrice(array("A.PAKET_PENAWARAN_ID" => $rekanan_paket_penawaran->getField("PAKET_PENAWARAN_ID")));                                    
                              if($penawaranNegosiasi == "")
                              {
                                  $penawaranNegosiasi = $penawaranTerkecil;                   
                              }
                              $jumlahNegosiasi =  round($penawaranNegosiasi * toNumber($rekanan_paket_penawaran->getField("QUANTITY")), 2);
                              
                          ?>           
                          <!-- <td align="right" <?=$displayElement?>> -->
                              <input type="hidden" name="reqUnitPriceNegosiasi[]" id="reqUnitPriceNegosiasi<?=$no?>" 
                              value="<?=numberToIna($penawaranNegosiasi)?>"  
                              OnFocus="FormatAngka('reqUnitPriceNegosiasi<?=$no?>')" 
                              OnKeyUp="FormatUang('reqUnitPriceNegosiasi<?=$no?>'); summary();" 
                              OnBlur="FormatUang('reqUnitPriceNegosiasi<?=$no?>')"  class="form-control"
                                      <?  if($submitNegosiasi == true) { ?> 
                                          style="text-align:right; width: 50%" 
                                      <? 
                                      }
                                       else 
                                      { ?>  
                                          style="text-align:right;background-color:#EDEDED; width: 50%" 
                              readonly <? } ?> class="span1" >
                              <!-- </td>    -->
                          <td align="right" <?=$displayElement?>>
                              <input type="text" name="reqJumlahNegosiasi[]" class="form-control" id="reqJumlahNegosiasi<?=$no?>" value="<?=numberToIna($jumlahNegosiasi)?>" style="text-align:right;background-color:#EDEDED; width: 50%" readonly class="span1">
                          </td>           
                      </tr>                    
                  <?
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
                  <tr colspan="100" style="display:none">
                      <td>
                          <textarea name="reqRekananIdArray"><?php print_r(serialize($arrPaketRekananId)); ?></textarea>           
                      </td>
                  </tr>
              </table>
            </div>
            <div class="form-actions">
              <input type="hidden" name="reqPaketRekananId" value="<?=$arrPaketRekananId[0]?>" />
              <input type="hidden" name="reqId" value="<?=$reqId?>" />
              <input type="hidden" name="submitSimpan" value="Simpan" />
              <input type="submit" name="btnSimpan" id="btnSimpan" value="" style="display:none"/> 
              <a href="main/index/paket_detil/?reqId=<?=$reqId?>" class="btn btn-danger text-white"><i class="fa fa-arrow-left"></i> Kembali</a>
              <a <? if($submitNegosiasi == true) { ?> 
                    onClick="$('#btnSimpan').click();" 
                 <? } else { ?> 
                    href="main/index/paket_lelang_tambah_negosiasi_undangan/?reqId=<?=$reqId?>&reqMode=lelang" 
                 <? } ?> 
                    class="btn btn-primary text-white pull-right">Lanjut <i class="fa fa-arrow-right"></i></a>
            </div> 
          </form>
        </div>
      </div>
    </div>
  </div> 
</div>    
