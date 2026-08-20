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
$this->load->model("PaketEvaluasiAdminTawar");
$this->load->model("PaketEvaluasiTeknisTawar");
$this->load->model("PaketEvaluasiHargaTawar");
$this->load->model("PaketRekanan");
$this->load->model("PaketDokumen");
$this->load->model("RekananEvaluasiAdminTawar");
$this->load->model("RekananEvaluasiTeknisTawar");
$this->load->model("RekananEvaluasiHargaTawar");
$this->load->model("Paket");
$this->load->model("PaketTahap");
$this->load->model("PaketPembukaanKeduaValidasi");

$paket_evaluasi_admin = new PaketEvaluasiAdminTawar();
$paket_evaluasi_teknis = new PaketEvaluasiTeknisTawar();
$paket_evaluasi_harga = new PaketEvaluasiHargaTawar();
$paket_rekanan = new PaketRekanan();
$paket_nilai = new Paket();
$paket_nilai_estimate = new Paket();
$paket_tahap_metode = new PaketTahap();
$paket_tahap = new PaketTahap();
$paket_pembukaan_validasi = new PaketPembukaanKeduaValidasi();

$reqId = $this->input->get("reqId");

$paketInfo->getPaket($reqId);
$reqNamaPaket = $paketInfo->nama;
$reqSistemSampul = $paketInfo->sistem_sampul;

$paket_evaluasi_admin->selectByParams(array("PAKET_ID" => $reqId));
$paket_evaluasi_teknis->selectByParams(array("PAKET_ID" => $reqId));
$paket_evaluasi_harga->selectByParams(array("PAKET_ID" => $reqId));
$paket_rekanan->selectByParams(array("PAKET_ID" => $reqId), -1, -1, " AND A.TANGGAL_DAFTAR IS NOT NULL AND A.LULUS_PENDAFTARAN = 1 AND A.LULUS_KUALIFIKASI = 1 AND A.KIRIM_PENAWARAN = 1 ");
while($paket_rekanan->nextRow())
{
	$arrRekanan[] = $paket_rekanan->getField("REKANAN");
	$arrRekananId[] = $paket_rekanan->getField("REKANAN_ID");
	$arrPaketRekananId[] = $paket_rekanan->getField("PAKET_REKANAN_ID");
	$arrPaketRekananNilai[] = $paket_rekanan->getField("NILAI_PENAWARAN");	
	$arrRekananHadirPembukaan[] = $paket_rekanan->getField("HADIR_PEMBUKAAN_PENAWARAN");
	$arrPasswordDokumen[] = $paket_rekanan->getField("KIRIM_PENAWARAN_PASSWORD2");
}

$paket_nilai->selectByParams(array("PAKET_ID" => $reqId));
$paket_nilai->firstRow();
$reqNilaiEstimate = $paket_nilai->getField("NILAI_OWNER_ESTIMATE");

$jenis_tahap = $paket_tahap_metode->getJenisTahapById($reqId);
//$arrPembukaanAuction	 	 = array(0, 13, 8,  13, 8,  12, 8,  13, 13);
$arrPembukaanAuctionSampul2	 = array(0, 0,  0,  0, 	0,  0, 	0,  0, 	0, 	0, 0, 16, 11, 16, 11);
//$arrPembukaanAuctionSampul2	 	 = array(0, 0,  0,  0, 	0,  0, 	0,  0, 	0, 	0, 0, 14, 9,  14, 9);


// if($paket_tahap->getCountByParams(array("URUT" => $arrPembukaanAuctionSampul2[$jenis_tahap], "PAKET_ID" => $reqId), " AND SYSDATE >= TANGGAL_AWAL ") > 0)
if($paket_tahap->getCountByParams(array("URUT" => $arrPembukaanAuctionSampul2[$jenis_tahap], "PAKET_ID" => $reqId), " AND NOW() >= TANGGAL_AWAL ") > 0)
	$info = "1";
else
{
	$paket_tahap->selectByParams(array("URUT" => $arrPembukaanAuctionSampul2[$jenis_tahap], "PAKET_ID" => $reqId));
	$paket_tahap->firstRow();
	$info = "0";//"Password akan terbuka pada : ".$paket_tahap->getField("JAM_BUKA");
}

$paket_pembukaan_validasi->selectByParamsValidasi(array("NIP" => $this->NIP, "A.PAKET_ID" => $reqId));
$paket_pembukaan_validasi->firstRow();
if($reqSistemSampul == "1")
	exit;
?>

<script type="text/javascript">	
$(function(){
    $('#ff').form({
        url:'paket_rekanan_json/nilai_penawaran',
        onSubmit:function(){
            return $(this).form('validate');
        },
        success:function(data){
            $.messager.alert('Info', data, 'info');	
        }
    });


	$('#ffUpload').form({
		url:'dokumen_pengadaan_upload_rekanan/upload_surat_penawaran',
		onSubmit:function(){
			if($(this).form('validate'))
			{
			var win = $.messager.progress({
										title:'Upload Data',
										msg:'Mengupload data...'
									});
			}
			else
				$('input:file').MultiFile('reset');
				
			return $(this).form('validate');
		},
		success:function(data){
			alert(data);
			$.messager.progress('close');
			document.location.reload();
		}
	});   
	 
});

	
function publishPembukaanSampul2()
{
	$.messager.confirm("Konfirmasi","Publish berita acara pembukaan penawaran sampul 2 ?",function(r){
		if (r){
			$.get( "paket_json/set_publish_pembukaan_sampul2/?reqId=<?=$reqId?>", function( data ) {
				  if(data == "1")
				  {
					  $("#btnPublish").css("display", "none");
					  $.messager.alert('Info', "Publish pembukaan penawaran sampul 2 berhasil.", 'info');	
				  }
				  else
					  $.messager.alert('Info', data, 'info');	
			});	
		}
	});		
}

function submitValidasi(kode, jenis)
{
	$.messager.confirm("Konfirmasi","Validasi berita acara pembukaan penawaran ?",function(r){
		if (r){
			$.get('paket_validasi_json/pembukaankedua/?reqId=<?=$reqId?>&reqKode='+kode+'&reqJenis='+jenis, function( data ) {
				$.messager.alert('Info', data, 'info');	
				$("#tombolValidasi").css("display", "none");	
			});	
		}
	});		
		
}	
</script>
<style type="text/css">
  th {
    vertical-align: middle;
  }
</style>

<div class="row"> 
  <div class="col-md-12 col-sm-12">
    <div class="card">
      <div class="card-header card-head-inverse bg-primary">
        <h4 class="card-title text-white">Pembukaan Penawaran Sampul 2</h4>
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
          <div class="table-responsive">

            <div class="alert alert-info">PENAWARAN HARGA</div>
            <table class="table table-bordered table-hover">
              <tr>
                <th width="5%" style="vertical-align: middle;">No.</th>
                <th width="25%" style="vertical-align: middle;">Mitra Usaha</th>
                <th width="10%" style="vertical-align: middle;">Upload Surat Penawaran </th>
                <th style="vertical-align: middle;">Uraian</th>
                <th width="20%" style="vertical-align: middle;">Besar Penawaran</th>
                <th width="5%" style="vertical-align: middle;">Cetak BA <br> Pembukaan</th>
              </tr> 

              <?
              $no=1;
              for($i=0;$i<count($arrRekanan);$i++)
              {
              ?>
              <tr>
                <td><?= $no; ?></td>
                <td><?=$arrRekanan[$i]?> <br>
                <span class="fa fa-unlock-alt"></span> : <small><?=$arrPasswordDokumen[$i]?></small>
                </td>

                <!-- UPLOAD SURAT PENAWARAN -->
                <form id="ffUpload" method="post" novalidate enctype="multipart/form-data">
                  <td>
                    <input name="reqLinkFile<?=$arrRekananId[$i]?>" type="file" class="maxsize-20240" accept="pdf" id="reqLinkFile<?=$arrRekananId[$i]?>" />
                      <small>(Save as hanya surat penawaran, hapus password file pdf, dan upload ulang)</small><br>
                    <script>
                    // wait for document to load
                    $(function(){
                        // invoke plugin
                        $('#reqLinkFile<?=$arrRekananId[$i]?>').MultiFile({
                            onFileChange: function(){
                                $("#reqRekananId").val("<?=$arrRekananId[$i]?>");
                                $("#reqNamaDokumen").val("SURAT PENAWARAN <?=$arrRekanan[$i]?>");
                                $("#reqJenisDokumen").val("PEMBUKAAN_PENAWARAN");
                                $("#reqSubmit").click();
                            }
                        });
                    
                    });
                    </script>      
                    <?
                    $paket_dokumen = new PaketDokumen();
                    $paket_dokumen->selectByParams(array("PAKET_ID" => $reqId, "REKANAN_USER_ID" => $arrRekananId[$i], "JENIS_DOKUMEN" => 'PEMBUKAAN_PENAWARAN'));
                    $paket_dokumen->firstRow();
                    $file_penawaran_rekanan = $paket_dokumen->getField("PATH_FILE");
                    if($file_penawaran_rekanan == "")
                    {}
                    else
                    {
                    ?>
                      <a href="uploads/penawaran/<?=$paket_dokumen->getField("PATH_FILE")?>" class="label label-info" target="_blank">Dokumen penawaran <i class="fa fa-download"></i></a>
                    <?
                    }
                    ?>
                  </td>
                  <input type="hidden" name="reqRekananId" id="reqRekananId" value="" />
                  <input type="hidden" name="reqNamaDokumen" id="reqNamaDokumen" value="" />
                  <input type="hidden" name="reqJenisDokumen" id="reqJenisDokumen" value="" />
                  <input type="hidden" name="reqId" value="<?=$reqId?>" />
                  <input type="hidden" name="submitSimpan" value="Simpan" />
                  <input type="submit" name="reqSubmit" id="reqSubmit" value="" style="display:none">  
                </form>
                <!-- END UPLOAD SURAT PENAWARAN -->

                <!-- URAIAN -->
                <?
                while($paket_evaluasi_harga->nextRow())
                {
                  $a = $paket_evaluasi_harga->getField("NAMA");
                 //set up 16-10-2012
                  $paket_dokumen = new PaketDokumen();
                  $paket_dokumen->selectByParams(array("PAKET_ID" => $reqId, "REKANAN_USER_ID" => $arrRekananId[$i], "TRIM(NAMA)" => trim($paket_evaluasi_harga->getField("NAMA"))));
                  $paket_dokumen->firstRow();  
                  $b = $paket_dokumen->getField("PATH_FILE");
                }?>
                <td>
                  <?
                  if($info == "0")
                    echo $a ." : <br> -";
                  else
                  {
                    echo $a ." : <br>";
                  ?>
                    <a href="uploads/penawaran/<?=$b?>" target="_blank"><img src="images/icon-download.png" alt="" width="16" height="16" border="0" /></a>
                  <?
                  }
                  ?>                                                    
                </td>
                <!-- END URAIAN -->

            <!-- BESAR PENAWARAN -->
            <form id="ff" method="post" novalidate enctype="multipart/form-data">
                <?
                  $adaDiatasOE = 0;
                ?>
                 <td align="center" >
                    <input type="hidden" name="reqPaketRekananId[]" value="<?=$arrPaketRekananId[$i]?>">
                    <input name="reqDataPenawaranHarga[]" class="form-control" type="text" id="reqDataPenawaranHarga<?=$i?>"  value="<?=numberToIna($arrPaketRekananNilai[$i])?>"  OnFocus="FormatAngka('reqDataPenawaranHarga<?=$i?>')" OnKeyUp="FormatUang('reqDataPenawaranHarga<?=$i?>')" OnBlur="FormatUang('reqDataPenawaranHarga<?=$i?>')" class="form-rounded"/>
                  </td>
                  <?
                  if($arrPaketRekananNilai[$j] > $reqNilaiEstimate)
                     $adaDiatasOE++;   
                  ?>

                <?
                if($paketInfo->publish_ba_penawaran_sampul2 == "1" || $paketInfo->publish_ba_penawaran_sampul2 == "2")
                {
                ?> 
                <td align="center"> 
                  <?
                  if($arrRekananHadirPembukaan[$i] == "1")
                    $imgHadir = "images/centang.png";
                  else
                    $imgHadir = "images/delete-icon.png";
                  ?>  
                  <img src="<?=$imgHadir?>">        
                </td>
                <? 
                } else {
                  echo "<td>-</td>";
                }
                ?>    

              </tr>
              <?
              $no++;
              } 
              unset($paket_dokumen);
              ?>
              <tr colspan="5" style="display:none">
                <td >
                  <textarea name="reqRekananIdArray"><?php print_r(serialize($arrPaketRekananId)); ?></textarea>           
                </td>
              </tr> 
              <tfoot>
                <tr>
                  <td colspan="4"><b>Owner Estimate</b></td>
                  <td>
                    <input type="text" name="reqNilaiEstimate" class="form-control" id="reqNilaiOE" value="<?=numberToIna($reqNilaiEstimate)?>" readonly class="form-rounded" />
                  </td>
                  <td></td>
                </tr>
              </tfoot>

            </table>
            <hr>
            <div class="form-actions">
              <input type="hidden" name="reqId" value="<?=$reqId?>" />
              <input type="hidden" name="submitSimpan" value="Simpan" />
              <a href="main/index/paket_detil/?reqId=<?=$reqId?>" class="btn btn-danger text-white"> <i class="fa fa-arrow-left"></i> Kembali </a> 
              <button type="submit" name="varSimpan" id="varSimpan" class="btn btn-primary"><i class="fa fa-check-square-o"></i> Simpan</button>
              <a href="main/loadUrl/report/dokumen_pembukaan_penawaran_sampul2_ba_pdf/?reqId=<?=$reqId?>" target="_blank" class="btn btn-info"><i class="fa fa-print"></i> Cetak</a>  
              <?php /*?><input type="button" onclick="windowOpenerPopup(350,450,'Cetak','main/loadUrl/main/dokumen_pembukaan_penawaran_sampul2_ba_pdf?reqId=<?=$reqId?>');" name="varCetak" id="varCetak" value="Cetak" class="btn-cetak"/><?php */?>
               <?
              if($paketInfo->publish_ba_penawaran_sampul2 == "1")
              {}
              else
              {
                if($paket_pembukaan_validasi->getField("JENIS") == "PANITIA")
                {
                  if($paket_pembukaan_validasi->getField("KODE") == "")
                  {
                ?>
                    <a title="#" id="tombolValidasi" onclick="submitValidasi('<?=$paket_pembukaan_validasi->getField("NIP")?>', '<?=$paket_pembukaan_validasi->getField("JENIS")?>')" class="btn btn-primary"><i class="fa fa-check-square-o"></i> Validasi</a>
                <?
                    }
                }
                if($paket_pembukaan_validasi->getField("JENIS") == "PEMBUAT")
                {
                    
                  ?>                                      
                    <a onClick="publishPembukaanSampul2();" id="btnPublish" class="btn btn-success text-white"><i class="fa fa-send"></i> Publish</a> 
                  <?
                }
              }
              ?>     
            </div>
          </form>
          <!-- END BESAR PENAWARAN -->
 

        </div>
      </div>
    </div>
  </div> 
</div>   
