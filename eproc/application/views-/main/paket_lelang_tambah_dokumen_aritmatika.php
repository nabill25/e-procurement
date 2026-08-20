<?php
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 * 
 */
$this->load->library("paketinfo"); $paketInfo = new paketinfo();
$this->load->model("Paket");
$this->load->model("PaketDokumen");
$this->load->model("PaketPenawaran");
include_once("functions/string.func.php");
include_once("functions/date.func.php");
include_once("functions/default.func.php");

$paket_dokumen = new PaketDokumen();
$paket_dokumen_peserta = new PaketDokumen();
$paket_nilai = new Paket();
$paket_penawaran = new PaketPenawaran();

$reqMode = httpFilterRequest("reqMode");
$reqId = httpFilterRequest("reqId");

$FILE_DIR = "uploads/aritmatika/";

$paketInfo->getPaket($reqId);
$reqNamaPaket = $paketInfo->nama;

$paket_dokumen->selectByParams(array("PAKET_ID" => $reqId, "JENIS_DOKUMEN" => "ARITMATIKA"), -1, -1, " AND REKANAN_USER_ID IS NULL ");
$paket_dokumen->firstRow();
//echo $paket_dokumen->query;
$reqDokumenId = $paket_dokumen->getField("PAKET_DOKUMEN_ID");
$reqNama = $paket_dokumen->getField("NAMA");
$reqKeterangan = $paket_dokumen->getField("KETERANGAN");
$reqLinkFileTemp  = $paket_dokumen->getField("PATH_FILE");
$reqLinkFileTempTipe  = $paket_dokumen->getField("TIPE");
$reqLinkFileTempUkuran = $paket_dokumen->getField("UKURAN");

$reqSummaryOE = $paket_penawaran->getSummaryOE(array("PAKET_ID" => $reqId));

$paket_nilai->selectByParams(array("PAKET_ID" => $reqId));
$paket_nilai->firstRow();
$reqNilaiEstimate = $paket_nilai->getField("NILAI_OWNER_ESTIMATE");

if($reqNilaiEstimate == "")
	$reqNilaiEstimate = $reqSummaryOE;
?>
<script type="text/javascript">
$(document).ready(function() {
	
	$(function(){
		$('#ff').form({
			url:'paket_dokumen_json/paket_lelang_dokumen_aritmatika',
			onSubmit:function(){
				return $(this).form('validate');
			},
			success:function(data){
				//alert(data);return false;
              $.messager.alertReload('Informasi',data, 'info');
			  document.location.href = 'main/index/paket_lelang_tambah_dokumen_aritmatika/?reqId=<?=$reqId?>';
			}
		});
		
	});
	
});
</script>

<div class="span12">
 <div class="card">
  <h3 class="card-heading simple">Upload HPS</h3>
   <div class="card-body">
    <div class="control-group">
     <div class="controls">
        
      <form id="ff" class="easyui-form form-horizontal" method="post" data-options="novalidate:true" enctype="multipart/form-data">
                            
        <div class="control-group">
          <label class="control-label">Dokumen HPS:</label>
          <div class="controls">
              <?
              if($reqDokumenId == "")
              {
              ?>
                -
              <?
              }
              else
              {
              ?>
                <a href="<?=$FILE_DIR.$paket_dokumen->getField("PATH_FILE")?>" target="_blank"><?=$paket_dokumen->getField("NAMA")?>.<?=$paket_dokumen->getField("TIPE")?></a> <a title="#"><img src="images/button_cancel.png" width="16" height="16" border="0" /></a>
              <?
              }
              ?>
          </div>
        </div>  
        
        <div class="control-group">
          <label class="control-label">Nilai HPS:</label>
          <div class="controls">
            <? if($reqNilaiEstimate == "") { echo "-"; } else { echo currencyToPage($reqNilaiEstimate);?> <!--Rp 299.703.580,00--> <? } ?>
          </div>
        </div>   
        
        <div class="alert alert-info">Upload Dokumen HPS</div>
        <div class="control-group">
          <label class="control-label">Nama Dokumen:</label>
          <div class="controls">
            <input type="text" name="reqNamaDokumen" id="reqNamaDokumen" value="<?=$reqNama?>" class="form-control span9"/>
          </div>
        </div>   
        
        <div class="control-group">
          <label class="control-label">Nilai HPS:</label>
          <div class="controls">
            <input type="text" id="reqOE" name="reqOE" value="<?=numberToIna($reqNilaiEstimate)?>" OnFocus="FormatAngka('reqOE')" OnKeyUp="FormatUang('reqOE')" OnBlur="FormatUang('reqOE')" style="text-align:right" class="form-control span2" />
          </div>
        </div>  

        <div class="control-group">
          <label class="control-label">File:</label>
          <div class="controls">
            <input type="file" name="reqLinkFile" id="reqLinkFile" />
            <input type="hidden" name="reqLinkFileTemp" value="<?=$reqLinkFileTemp?>">
            <input type="hidden" name="reqLinkFileTempTipe" value="<?=$reqLinkFileTempTipe?>">
            <input type="hidden" name="reqLinkFileTempUkuran" value="<?=$reqLinkFileTempUkuran?>">
          </div>
        </div>  

        <div class="control-group">
          <label class="control-label"></label>
          <div class="controls">
            <input type="hidden" name="reqId" value="<?=$reqId?>" />
            <input type="hidden" name="reqDokumenId" value="<?=$reqDokumenId?>" />
            <input type="hidden" name="submitSimpan" value="Simpan" />
              <a href="main/index/paket_detil/?reqId=<?=$reqId?>" class="btn btn-danger">Kembali</a>
            <button type="submit" class="btn btn-primary">Simpan</button>
          </div>
        </div>  

      </form>
     </div>
    </div>
   </div>
   <div class="card-actions">
   </div>
 </div> 
</div>