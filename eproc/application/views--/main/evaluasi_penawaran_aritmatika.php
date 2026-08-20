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
$this->load->model("PaketDokumen");
$this->load->model("PaketRekanan");
$this->load->model("RekananPaketPenawaran");
$this->load->library("FileHandler");
$this->load->model("PaketPenawaran");
//include_once("WEB-INF/classes/utils/FileHandler.php");


$FILE_DIR = "uploads/aritmatika/";

$paket_rekanan = new PaketRekanan();
$paket_dokumen = new PaketDokumen();
$rekanan_paket_penawaran = new RekananPaketPenawaran();
$rekanan_paket_penawaran2 = new RekananPaketPenawaran();
$file = new FileHandler();

$reqId = httpFilterRequest("reqId");
 

$submitSimpan = httpFilterPost("submitSimpan");
$reqLinkFile= $_FILES['reqLinkFile'];
$reqRekananId = $_POST['reqRekananId']; 
$reqPaketRekananId = $_POST['reqPaketRekananId']; 
$reqPenawaranKoreksi = $_POST['reqPenawaranKoreksi']; 
$reqUraian = $_POST["reqUraian"];

$paketInfo->getPaket($reqId);
$reqNamaPaket = $paketInfo->nama;
$reqJenisPekerjaan = $paketInfo->jenis;
$reqMetodeEvaluasi = $paketInfo->metode_evaluasi;
$reqMetodePengadaan = $paketInfo->metode_lelang_id;
// $reqMetodeLelang = $paketInfo->metode_lelang_id;

$paket_penawaran = new PaketPenawaran();
$adaBiayaPengiriman = $paket_penawaran->getCountByParams(array("PAKET_ID" => $reqId), " AND BIAYA_KIRIM > 0");


$paket_rekanan = new PaketRekanan();
$paket_rekanan->selectByParams(array("PAKET_ID" => $reqId), -1, -1, " AND A.TANGGAL_DAFTAR IS NOT NULL AND A.LULUS_PENDAFTARAN = 1 AND A.LULUS_KUALIFIKASI = 1 AND A.KIRIM_PENAWARAN = 1 ");

while($paket_rekanan->nextRow())
{
	$arrRekanan[] = $paket_rekanan->getField("REKANAN");
	$arrRekananId[] = $paket_rekanan->getField("REKANAN_ID");
	$arrPaketRekananId[] = $paket_rekanan->getField("PAKET_REKANAN_ID");
	$arrNilaiPenawaran[] = $paket_rekanan->getField("NILAI_PENAWARAN");
}

$rekanan_paket_penawaran->selectByParamsEvaluasiRekanan($arrPaketRekananId, array("A.PAKET_ID" => $reqId));
$rekanan_paket_penawaran2->selectByParamsEvaluasiRekanan($arrPaketRekananId, array("A.PAKET_ID" => $reqId));
   	
?>
<script type="text/javascript">
$(document).ready(function() {
	$(function(){
		$('#ff').form({
			url:'paket_rekanan_json/evaluasi_penawaran_aritmatika',
			onSubmit:function(){
				return $(this).form('validate');
			},
			success:function(data){
				$.messager.alert('Info', data, 'info');	
				//document.location.href = 'main/index/evaluasi_penawaran_aritmatika/?reqId=<?=$reqId?>';
			}
		});
	});
});

function summary(idElement)
{
  var reqTotal = 0;
  
  $("table input[id^=reqUnitPriceKoreksi"+idElement+"]").each(function() {
    
    
    var id = $(this).attr("id");
    data = id.replace("reqUnitPriceKoreksi", "");
    arrData = data.split("-");
    var paketRekananId = arrData[0];
    var paketPenawaranId = arrData[1];
    
    var txtQuantity = $("#reqQuantity"+paketPenawaranId).val();
    if(Number(txtQuantity) > 0)
    {
      var txtJumlah = id.replace("reqUnitPriceKoreksi", "reqJumlahKoreksi");      
      var jumlah = (Number(FormatAngkaNumber($(this).val())) * Number(txtQuantity));    
      reqTotal = reqTotal + jumlah;   
      $("#"+txtJumlah).val(FormatCurrency(jumlah));
    }
  
  });   
  
  $("#reqPenawaranKoreksi"+idElement).val(FormatCurrency(reqTotal));
  
}
</script>

<style type="text/css">
  table th {
    background-color: #967adc;
    color: #fff;
  }
</style>

<div class="row"> 
  <div class="col-md-12 col-sm-12">
    <div class="card">
      <div class="card-header card-head-inverse bg-primary">
        <h4 class="card-title text-white">Evaluasi Aritmatika</h4>
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
          <div class="form-group col-md-12 mb-2">
            <?php 
            // 1-e-Tender ,2-Pengadaan Langsung ,5-Penunjukan Langsung Cepat,7-e-Tender Cepat
            if ($reqMetodePengadaan != 7) { ?>
            <a href="main/index/evaluasi_penawaran_administrasi/?reqId=<?=$reqId?>" class="btn btn-primary"><i class="fa fa-check"></i> Evaluasi Administrasi</a>
            <?php 
            } ?> 
            <a href="main/index/evaluasi_penawaran_aritmatika/?reqId=<?=$reqId?>" class="btn btn-primary disabled"><span class="fa fa-wpforms"></span> Evaluasi Aritmatika</a> 
            <?php 
            // 1-e-Tender ,2-Pengadaan Langsung ,5-Penunjukan Langsung Cepat,7-e-Tender Cepat
            if ($reqMetodePengadaan != 7) { ?>
            <a href="main/index/evaluasi_penawaran_teknis/?reqId=<?=$reqId?>" class="btn btn-primary"><span class="fa fa-pencil"></span> Evaluasi Teknis</a>
            <?php 
            } ?>
            <a href="main/index/evaluasi_penawaran_harga/?reqId=<?=$reqId?>" class="btn btn-primary"><span class="fa fa-money"></span> Evaluasi Harga</a>
            <a href="main/index/evaluasi_penawaran_rekapitulasi/?reqId=<?=$reqId?>" class="btn btn-primary"><span class="fa fa-list-alt"></span> Rekapitulasi </a>
          </div> 

          <form id="ff" class="easyui-form form-horizontal" method="post" novalidate enctype="multipart/form-data">
            <div class="table-responsive">  
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
                <!-- <tr>
                  <?php
                  $paket_dokumen = new PaketDokumen();
                  $paket_dokumen->selectByParams(array("PAKET_ID" => $reqId, "JENIS_DOKUMEN" => "EVALUASI_PENAWARAN_ADMINISTRASI"));
                  $paket_dokumen->firstRow();            
                  $dokumen = $paket_dokumen->getField("PATH_FILE");
                  if($dokumen == "")
                  {}
                  else
                  {
                  ?>
                  <td>Download Berita Acara Evaluasi Administrasi</td>
                  <td>
                  <a href="uploads/penawaran/<?=$dokumen?>" target="_blank"><img src="images/icon-download.png"></a>       
                  </td>
                  <?php
                  }
                  ?>
                </tr>
                <tr>
                  <?php
                  $paket_dokumen_teknis = new PaketDokumen();
                  $paket_dokumen_teknis->selectByParams(array("PAKET_ID" => $reqId, "JENIS_DOKUMEN" => "EVALUASI_PENAWARAN_TEKNIS"));
                  $paket_dokumen_teknis->firstRow();            
                  $dokumen_teknis = $paket_dokumen_teknis->getField("PATH_FILE");
                  if($dokumen_teknis == "")
                  {}
                  else
                  {
                  ?>
                  <td>Download Berita Acara Evaluasi Teknis</td>
                  <td>
                  <a href="uploads/penawaran/<?=$dokumen_teknis?>" target="_blank"><img src="images/icon-download.png"></a>       
                  </td>
                  <?php
                  }
                  ?>
                </tr>
                <tr>
                  <?php
                  $paket_dokumen_harga = new PaketDokumen();
                  $paket_dokumen_harga->selectByParams(array("PAKET_ID" => $reqId, "JENIS_DOKUMEN" => "EVALUASI_PENAWARAN_HARGA"));
                  $paket_dokumen_harga->firstRow();            
                  $dokumen_harga = $paket_dokumen_harga->getField("PATH_FILE");
                  if($dokumen_harga == "")
                  {}
                  else
                  {
                  ?>
                  <td>Download Berita Acara Evaluasi Harga</td>
                  <td>
                  <a href="uploads/penawaran/<?=$dokumen_harga?>" target="_blank"><img src="images/icon-download.png"></a>       
                  </td>
                  <?php
                  }
                  ?>
                </tr> -->
              </table>  

              <table class="table table-bordered table-hover"> 
                <tr>
                  <th width="2%">
                    <?php
                      if($paketInfo->sistem_harga == "LOT")
                        echo "Lot";
                      else
                        echo "No.";
                  ?>
                  </th>
                  <th width="38%">Mitra Usaha</th>
                  <th width="15%">Penawaran</th>
                  <!-- <th width="15%">Total</th> -->
                  <th width="15%">Penawaran Terkoreksi</th>
                  <!-- <th width="15%">Jumlah Koreksi</th> -->
                </tr>
                <?php
                $no=1;
                while($rekanan_paket_penawaran->nextRow())
                { 
                  $style = "";
                  if($rekanan_paket_penawaran->getField("QUANTITY") == 0)
                    $style = ' style="display:none" ';
                for($i=0;$i<count($arrRekanan);$i++)
                {
                ?>
                <tr>
                  <td width="2%"><?= $no ?></td>
                  <td><?=$arrRekanan[$i]?></td> 
                  <?php
                  $arrSummary["SUMMARY"][$no] = $rekanan_paket_penawaran->getField("SUMMARY");
                  $idElement = $arrPaketRekananId[$i]."-".$rekanan_paket_penawaran->getField("PAKET_PENAWARAN_ID");
                  ?>
                    <td align="right" <?=$style?>>
                      <?=numberToIna($rekanan_paket_penawaran->getField("UP_".$arrPaketRekananId[$i]))?>
                      <?php
                      if($rekanan_paket_penawaran->getField("BOQ_".$arrPaketRekananId[$i]) == "")
                        {}
                        else
                        {
                        ?>
                      <br>
                     <a href="uploads/penawaran/<?=$rekanan_paket_penawaran->getField("BOQ_".$arrPaketRekananId[$i])?>" class="badge badge-primary">download rincian BoQ</a>
                      <?php
                      }
                      ?>
                    </td>
                    <!-- <td align="right" <?=$style?>><?=numberToIna($rekanan_paket_penawaran->getField("SUM_".$arrPaketRekananId[$i]))?></td> -->
                    <td align="center" <?=$style?>>
                      <input class="form-control" id="reqUnitPriceKoreksi<?=$idElement?>" name="reqUnitPriceKoreksi<?=$idElement?>" value="<?=numberToIna($rekanan_paket_penawaran->getField("UPK_".$arrPaketRekananId[$i]))?>" class="easyui-validatebox"style="text-align:right; width:100% !important;" OnFocus="FormatAngka('reqUnitPriceKoreksi<?=$idElement?>')" OnKeyUp="FormatUang('reqUnitPriceKoreksi<?=$idElement?>');  summary('<?=$arrPaketRekananId[$i]?>');" OnBlur="FormatUang('reqUnitPriceKoreksi<?=$idElement?>')" style="width: 100%">
                    </td> 
                    <!-- <td align="center" <?=$style?>> -->
                      <input class="form-control" id="reqJumlahKoreksi<?=$idElement?>" name="reqJumlahKoreksi<?=$idElement?>" value="<?=numberToIna($rekanan_paket_penawaran->getField("SUMKOREKSI_".$arrPaketRekananId[$i]))?>" class="easyui-validatebox" style="text-align:right; width:100% !important; background:#F8F8F8" type="hidden">
                    <!-- </td>       -->
                                                                                      
                  <?php
                      $arrSummary["SUM_".$arrPaketRekananId[$i]][$no] = $rekanan_paket_penawaran->getField("SUM_".$arrPaketRekananId[$i]);
                  ?>
                </tr>
                <?php
                  $no++;
                  } 
                }
                while($rekanan_paket_penawaran2->nextRow())
                {
                ?>
                <input type="hidden" name="reqPaketPenawaranId[]" value="<?php echo $rekanan_paket_penawaran2->getField("PAKET_PENAWARAN_ID")?>">
                <input type="hidden" name="reqQuantity[]" id="reqQuantity<?php echo $rekanan_paket_penawaran2->getField("PAKET_PENAWARAN_ID")?>" value="<?php echo $rekanan_paket_penawaran2->getField("QUANTITY")?>">
                <tfoot>
                  <tr>
                    <td>#</td>
                    <td>HPS</td>
                    <!-- <td align="right"><?php echo numberToIna($rekanan_paket_penawaran2->getField("OE"))?></td> -->
                    <td align="right" colspan="2" style="text-align: center;">
                      <b><?php echo numberToIna($rekanan_paket_penawaran2->getField("SUMMARY"))?></b>
                    </td>
                    <!-- <td colspan="1"></td> -->
                  </tr>
                </tfoot> 
                <?php } ?>

                <?php
                  for($i=0;$i<count($arrRekanan);$i++)
                  {          
                    $reqNilaiPenawaran = $arrNilaiPenawaran[$i];               
                  ?>
                      <input type="hidden" name="reqPenawaranSebelumnya[]" value="<?=array_sum($arrSummary["SUM_".$arrPaketRekananId[$i]])?>" class="span2">
                      <input type="hidden" name="reqPaketRekananId[]" value="<?=$arrPaketRekananId[$i]?>">
                  <?php                                                               
                  }
                  ?>
              </table> 

              <div class="form-actions">
                <input type="hidden" name="reqId" value="<?=$reqId?>" />
                <input type="hidden" name="submitSimpan" value="Simpan" />
                <a href="main/index/paket_detil/?reqId=<?=$reqId?>" class="btn btn-danger mr-1 text-white"> <i class="fa fa-arrow-left"></i> Kembali </a>
                <a class="btn btn-info mr-1" href="main/loadUrl/report/evaluasi_penawaran_aritmatika_excel/?reqId=<?=$reqId?>" target="_blank" ><i class="fa fa-print"></i> Cetak</a>
                <button type="submit" name="reqSimpan" id="reqSimpan" class="btn btn-primary"><i class="fa fa-check-square-o"></i> Simpan</button>
              </div> 
            </div>
          </form>
        </div>
      
      </div>
    </div>
  </div> 
</div>   
  
<!-- <div class="row">
    <div class="col-md-12">
    	<div class="area-main">
        	<div class="judul-halaman">Evaluasi Penawaran Paket Lelang</div>
            <div class="inner">
            	<div class="area-sidelook"></div>
                <div class="area-konten">
                    <div class="area-konten-inner">
                    	<div class="area-tab-external">
                        	<ul class="tab-navigasi">
                            	<li><a href="main/index/evaluasi_penawaran_administrasi/?reqId=<?=$reqId?>">Evaluasi Administrasi</a></li>
                                <li><a href="main/index/evaluasi_penawaran_teknis/?reqId=<?=$reqId?>">Evaluasi Teknis</a></li>
                                <li><a href="main/index/evaluasi_penawaran_harga/?reqId=<?=$reqId?>">Evaluasi Harga</a></li>
                                <li><a class="current">Perbandingan Aritmatika</a></li>
                                <li><a href="main/index/evaluasi_penawaran_rekapitulasi/?reqId=<?=$reqId?>">Rekapitulasi</a></li>
                            </ul>
                            <div class="konten">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                              
                                      </div>
                                    </div>
                                </div> 
                            </div>
						          </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div> -->
