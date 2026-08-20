<?php
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 * 
 */

if($this->USER_TYPE_ID == "")
  redirect("main");


$this->load->library("paketinfo"); $paketInfo = new paketinfo();
include_once("functions/string.func.php");
include_once("functions/default.func.php");
include_once("functions/date.func.php");
$this->load->model("PaketRekanan");
$this->load->model("Paket");
$this->load->model("PaketNegoisasi");
$this->load->model("RekananPaketPenawaran");
$this->load->model("Rekanan");
$this->load->model("PaketTahap");

$reqId = $this->input->get("reqId");

$paketInfo->getPaket($reqId);
$reqNamaPaket = $paketInfo->nama;
$reqRekananIdPemenang = $paketInfo->rekanan_id_pemenang;
$reqPublishBANegosiasi = $paketInfo->publish_ba_negosiasi;
$reqJenisPekerjaan = $paketInfo->jenis;
$reqJenisPekerjaanId = $paketInfo->jenis_id;
$reqMetodeEvaluasi = $paketInfo->metode_evaluasi;
$reqUUID = $paketInfo->uuid;

$paket_tahap = new PaketTahap();
$paket_tahap_metode = new PaketTahap();
$jenis_tahap = $paket_tahap_metode->getJenisTahapById($reqId);
$arrNegosiasi                    = NEGOSIASI;

$aktif_negosiasi = $paket_tahap->getCountByParamsAktif(array("URUT" => $arrNegosiasi[$jenis_tahap], "PAKET_ID" => $reqId, "TAMPILKAN" => 1));
$aktif_negosiasi2 = $paket_tahap->getCountByParamsBerlalu(array("URUT" => $arrNegosiasi[$jenis_tahap], "PAKET_ID" => $reqId, "TAMPILKAN" => 1));

if($aktif_negosiasi > 0 || $aktif_negosiasi2 < 1)
{
  /* CHECK APAKAH REKANAN PEMENANG */ 
	// if($this->ID == $reqRekananIdPemenang)
		$aktif_negosiasi =1;	
	// else
    // $aktif_negosiasi =0;   
} else {
		$aktif_negosiasi =0;		
}

// echo $aktif_negosiasi;

if($reqRekananIdPemenang != $this->ID) // hanya untuk pemenang dan terundang untuk negosiasi
  redirect(base_url('main'));

// if($aktif_negosiasi == 0) 
//   redirect(base_url('main'));

$paket_rekanan = new PaketRekanan();
$paket_nilai = new Paket();
$paket_nilai_estimate = new Paket();
$rekanan_paket_penawaran = new RekananPaketPenawaran();

$reqId = $this->input->get("reqId");
$reqNilaiEstimate = httpFilterPost("reqNilaiEstimate");
$reqDataPenawaranHarga = $_POST["reqDataPenawaranHarga"];
$reqRekananIdArray =unserialize(stripslashes($_POST['reqRekananIdArray']));
$submitSimpan = httpFilterPost("submitSimpan");
$paket_rekanan->selectByParams(array("PAKET_ID" => $reqId, "A.REKANAN_ID" => $reqRekananIdPemenang), -1, -1, " AND A.TANGGAL_DAFTAR IS NOT NULL AND A.LULUS_PENDAFTARAN = 1 AND A.LULUS_KUALIFIKASI = 1 ");
$paket_rekanan->firstRow();

$i = $indexRekananPememenang =  0;
$arrRekananId[$i] = $paket_rekanan->getField("REKANAN_ID");
$arrRekanan[$i] = $paket_rekanan->getField("REKANAN");
$arrPaketRekananId[$i] = $paket_rekanan->getField("PAKET_REKANAN_ID");
$arrPaketRekananNilai[$i] = $paket_rekanan->getField("NILAI_PENAWARAN");
$arrDiemailNegosiasi[$i] = $paket_rekanan->getField("DI_EMAIL_NEGOSIASI");

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

?>
<script type="text/javascript" src="lib/eproc/allfunc.js"></script>
<script type="text/javascript">
$(function(){
	$('#ff').form({
		url:'json/paket_negosiasi/add',
		onSubmit:function(){
			return $(this).form('validate');
		},
		success:function(data){
			alert(data);
			document.location.href = "main/?pg=negosiasi_undangan&reqId=<?=$reqId?>";
		}
	});

});
</script>

<div class="row"> 
  <div class="col-md-12 col-sm-12">
    <div class="card">
      <div class="card-header card-head-inverse bg-primary">
        <h4 class="card-title text-white">Negosiasi</h4>
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
                  <li role="presentation" style="width: 50% !important"><a href="main/index/klarifikasi_chat_rekanan/?reqId=<?=$reqId?>#area-chat"><i class="fa fa-check-circle" aria-hidden="true"></i>
                    <p>Pembuktian</p>
                    </a>
                  </li> 
                  <li class="active" role="presentation" style="width: 50% !important"><a href="main/index/negosiasi_rekanan/?reqId=<?=$reqId?>"><i class="fa fa-flag" aria-hidden="true"></i>
                    <p>Negosiasi</p>
                    </a>
                  </li> 
                </ul>
              </div> 
            </div> 
          </div>
          <?php 
          if ($aktif_negosiasi == '0') {
             echo '<div class="alert alert-danger" style="color:#fff">
                      <span style="color: #fff">
                        Negosiasi belum dimulai atau sudah selesai.
                      </span>
                    </div>';
           } ?>
          <div class="table-responsive">
            <table class="table table-bordered table-hover" style="width: 100%">
              <tbody>
                <tr>
                  <td width="20%"> Pekerjaan :</td>
                  <td> <?=$reqNamaPaket?> </td>
                </tr> 
                <tr>
                  <td width="20%"> Jenis Pekerjaan :</td>
                  <td> <?=$reqJenisPekerjaan?> </td>
                </tr> 
                <tr>
                  <td> Metode Evaluasi :</td> 
                  <td> <?=$reqMetodeEvaluasi?>  </td> 
                </tr> 
              </tbody>
            </table>
            <form id="ff" method="post" enctype="multipart/form-data" novalidate>
              <table class="table table-bordered">
                  <tr class="judul-kolom"  style="background-color: #967adc; color: #fff; padding: 25px 0">
                    <!-- <td rowspan="2" align="center">No.</td> -->
                    <!-- <td rowspan="2" align="center">Uraian</td> -->
                    <!-- <td rowspan="2" align="center">Quantity</td> -->
                    <!-- <td rowspan="2" align="center">Satuan</td> -->
                    <td colspan="1" align="center"><?=$arrRekanan[$indexRekananPememenang]?></td>
                    <td colspan="1" align="center">Negosiasi</td>
                    <td colspan="3" align="center" style="width: 15%">Persetujuan</td>
                  </tr>       
                  <tr class="judul-kolom"  style="background-color: #967adc; color: #fff; padding: 25px 0">
                      <td align="center">Penawaran Terkoreksi</td>
                      <!-- <td align="center">Total</td> -->
                      <!-- <td align="center" <?=$display_pembelian?>>Delivery</td> -->
                      <td align="center">Total</td>
                      <!-- <td align="center">Total</td> -->
                      <?php 
                      if ($aktif_negosiasi == '1') { ?>
                      <td align="center">Chat</td>
                      <td align="center">Nego Item</td>
                      <?php 
                      } ?>
                      <td align="center">Penyedia</td>
                  </tr>
                  <?php
                  $no = 0;
                  $style="gelap";
                  $totalNegosiasi = 0;
                  while($rekanan_paket_penawaran->nextRow())
                  {
                    // echo $rekanan_paket_penawaran->getField("PAKET_PENAWARAN_ID");
                    $displayElement = "";
                    $colspan = "";
                    if((int)$rekanan_paket_penawaran->getField("QUANTITY") == 0)
                    {
                      $displayElement = " style='display:none' ";
                      $no = -1;
                      $colspan = "10";
                    }
                  ?>                     
                  <?php 
                  if ($aktif_negosiasi == '1') {?> 
                  <script type="text/javascript">
                    //$(document).ready(function() {
                      //  window.openPopupChat('main/loadUrl/main/negosiasi_chat_rekanan/?reqId=<?=$reqId?>&reqPaketPenawaranId=<?=$rekanan_paket_penawaran->getField("PAKET_PENAWARAN_ID")?>');
                        //setTimeout(function() {
                          //  $('.close-modal').click();
                       // }, 1000);
                    // });
                    $(document).ready(function() {
                      showLoad();
                        window.openPopupChat('main/loadUrl/main/negosiasi_chat_rekanan/?reqId=<?=$reqId?>&reqPaketPenawaranId=<?=$rekanan_paket_penawaran->getField("PAKET_PENAWARAN_ID")?>');
                        setTimeout(function() { 
                            document.querySelector('.close').click();
                            document.querySelector('.x').click();
                            hideLoad();
                        }, 800);
                    });
                  </script>
                  <?php 
                  } ?>
                      <tr class="<?=$style?>">
                        <input type="hidden" name="reqQuantity[]" id="reqQuantity<?=$rekanan_paket_penawaran->getField("PAKET_PENAWARAN_ID")?>" value="<?=$rekanan_paket_penawaran->getField("QUANTITY")?>">
                        <input type="hidden" name="reqPaketPenawaranId[]" id="reqPaketPenawaranId<?=$rekanan_paket_penawaran->getField("PAKET_PENAWARAN_ID")?>" value="<?=$rekanan_paket_penawaran->getField("PAKET_PENAWARAN_ID")?>">
                              
                          <td align="right" <?=$displayElement?>><?=numberToIna($rekanan_paket_penawaran->getField("UPK_".$arrPaketRekananId[$indexRekananPememenang]))?></td>
                          <!-- <td align="right" <?=$displayElement?>><?=numberToIna($rekanan_paket_penawaran->getField("SUM_".$arrPaketRekananId[$indexRekananPememenang]))?></td> -->
                          <td align="center" <?=$display_pembelian?>><?=dateToPageCheck($rekanan_paket_penawaran->getField("DD_".$arrPaketRekananId[$indexRekananPememenang]))?></td>
                          <?php
                          $arrSummary["SUMMARY"][$no] = $rekanan_paket_penawaran->getField("SUMMARY");
                          $arrSummary["SUM_".$arrPaketRekananId[$indexRekananPememenang]][$no] = $rekanan_paket_penawaran->getField("SUM_".$arrPaketRekananId[$indexRekananPememenang]);
                      
                          $paket_negosiasi = new PaketNegoisasi();                            
                          $paket_negosiasi->selectByParams(array("A.PAKET_PENAWARAN_ID" => $rekanan_paket_penawaran->getField("PAKET_PENAWARAN_ID")));  
                          $paket_negosiasi->firstRow();   
                          $penawaranNegosiasi =  $paket_negosiasi->getField("UNIT_PRICE");                          
                          $jumlahNegosiasi =  $paket_negosiasi->getField("TOTAL");
                              
                          ?>            
                          <td align="right" <?=$displayElement?>><input type="text" name="reqJumlahNegosiasi[]" id="reqJumlahNegosiasi<?=$rekanan_paket_penawaran->getField("PAKET_PENAWARAN_ID")?>" class="form-control" value="<?=numberToIna($jumlahNegosiasi)?>" style="width:50%; text-align:right;background-color:#EDEDED" readonly></td>   
                          <?php 
                          if ($aktif_negosiasi == '1') {?>        
                          <td align="center" <?=$displayElement?>>
                              <a onClick="window.openPopupChat('main/loadUrl/main/negosiasi_chat_rekanan/?reqId=<?=$reqId?>&reqPaketPenawaranId=<?=$rekanan_paket_penawaran->getField("PAKET_PENAWARAN_ID")?>')">
                                <span class="fa fa-comments-o fa-2x"></span>
                          </td>
                          <td align="center">
                            <a href="main/index/negosiasi_item_rekanan?reqId=<?= $reqId ?>">
                              <span class="fa fa-calculator fa-2x"></span>
                            </a>
                          </td>
                          <?php 
                          } ?>
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
                            unset($paket_negosiasi);
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
                      <tr>
                        <td colspan="100" align="right">
                        <input type="hidden" name="reqId" value="<?=$reqId?>" />
                        <input type="hidden" name="submitSimpan" value="Simpan" />
                        <input type="submit" name="btnSimpan" id="btnSimpan" value="" style="display:none"/> 
                      </tr>
              </table>
            </form>
            <a href="main/index/paket_detil/?eid=<?=$reqId?>&key=<?=$reqUUID?>" class="<?= CLASS_BTN_DANGER ?>"><i class="fa fa-arrow-left"></i> Kembali</a>
            <?php
            if($reqPublishBANegosiasi == "1")
            {
            ?>
                <a href="main/loadUrl/report/negosiasi_cetak_pdf?reqId=<?=$reqId?>" target="_blank" class="<?= CLASS_BTN_INFO ?>"><i class="fa fa-print"></i> Cetak Hasil Negosiasi</a>
            <?php
            }
            ?>
          </div>
        </div>
      </div>
    </div>
  </div> 
</div> 
 

<script type="text/javascript">
function summary()
{
	var reqTotal = 0;
	 
	$("table input[id^=reqUnitPriceNegosiasi]").each(function() {
		var txtQuantity = $(this).attr("id").replace("reqUnitPriceNegosiasi", "reqQuantity");
		var txtJumlah = $(this).attr("id").replace("reqUnitPriceNegosiasi", "reqJumlahNegosiasi");
		
		var jumlah = (Number(FormatAngkaNumber($(this).val())) * Number($("#"+txtQuantity).val())).toFixed(2);
	
		reqTotal = Number(reqTotal) + Number(jumlah);
	
		$("#"+txtJumlah).val(FormatCurrencyBaru(jumlah));
	
	});		
	
	reqTotal = reqTotal.toFixed(2);
	$("#reqTotal").val(FormatCurrencyBaru(reqTotal));
}

</script>
