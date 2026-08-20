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
$this->load->model("PaketRekanan");
$this->load->library("paketinfo"); $paketInfo = new paketinfo();
include_once("functions/string.func.php");
include_once("functions/date.func.php");
include_once("functions/default.func.php");
$this->load->library("KMail");

$paket_rekanan = new PaketRekanan();
$paket_rekanan_total = new PaketRekanan();

$reqMode = $this->input->post("reqMode");
$reqId = $this->input->get("reqId");
$submitSimpan = $this->input->post("submitSimpan");

$reqLulusPendaftaran = isset($_POST["reqLulusPendaftaran"])?$_POST["reqLulusPendaftaran"]:'';
$reqLulusKeterangan = isset($_POST["reqLulusKeterangan"])?$_POST["reqLulusKeterangan"]:'';
$reqPaketRekananId = isset($_POST["reqPaketRekananId"])?$_POST["reqPaketRekananId"]:'';
$reqPaketRekananIdUser = $this->input->post("reqPaketRekananIdUser");
$reqLulusPendaftaranUser = $this->input->post("reqLulusPendaftaranUser");


$paket_rekanan->selectByParams(array("PAKET_ID" => $reqId));
$totalPaket = $paket_rekanan_total->getCountByParams(array("PAKET_ID" => $reqId));
$paketInfo->getPaket($reqId);

//echo $paket_rekanan->query;exit;
?> 
<script type="text/javascript">
$(document).ready(function() {
	$(function(){
		$('#ff').form({
			url:'paket_rekanan_json/verifikasi_peserta_lelang',
			onSubmit:function(){
				return $(this).form('validate');
			},
			success:function(data){
				$.messager.alert('Informasi',data, 'info');
				document.location.reload();
			}
		});
		
	});
	
});

function kirimEmailSemua()
{
	$.messager.defaults.ok = 'Ya';
	$.messager.defaults.cancel = 'Tidak';
	$.messager.confirm('Konfirmasi', 'Kirim email ke semua rekanan?',function(r){
	  if (r){		
			  var win = $.messager.progress({
				  title:'<?= SYSTEM_NAME.' '.SYSTEM_NAME_PT ?>',
				  msg:'Kirim email...'
			  });

			  $.get( "paket_rekanan_json/email_seluruh_peserta_lelang/?reqId=<?=$reqId?>", function( data ) {
				  $.messager.progress('close');
				  $.messager.alert('Informasi',data, 'info');
			  });
	  }
   });
}

function kirimEmail(reqId)
{
	$.messager.defaults.ok = 'Ya';
	$.messager.defaults.cancel = 'Tidak';
	$.messager.confirm('Konfirmasi', 'Kirim email ke rekanan terpilih?',function(r){
	  if (r){		
			  var win = $.messager.progress({
				  title:'<?= SYSTEM_NAME.' '.SYSTEM_NAME_PT ?>',
				  msg:'Kirim email...'
			  });

			  $.get( "paket_rekanan_json/email_peserta_lelang/?reqId=<?=$reqId?>&reqPaketId="+reqId, function( data ) {
				  $.messager.progress('close');
				  $.messager.alert('Informasi',data, 'info');
			  });
	  }
   });	
}

</script>

<div class="row"> 
  <div class="col-md-12 col-sm-12">
    <div class="card">
      <div class="card-header card-head-inverse bg-primary">
        <h4 class="card-title text-white">Daftar Peserta <?= $paketInfo->metode_lelang_nama ?></h4>
        <a class="heading-elements-toggle"><i class="fa fa-ellipsis-v font-medium-3"></i></a> 
        <div class="heading-elements">
            <ul class="list-inline mb-0">
              <li><a data-action="expand"><i class="ft-maximize"></i></a></li>
              <!-- <li><a data-action="close"><i class="ft-x"></i></a></li> -->
            </ul>
        </div>
      </div>

      <form id="ff" class="easyui-form form-horizontal" method="post" data-options="novalidate:true" enctype="multipart/form-data">
      <div class="card-content collapse show border-info border-darken-2">
        <div class="card-body area-datatable">

            <div class="table-responsive">
              <table class="table table-bordered mb-0"> 
                <tbody>
                  <tr class="judul-kolom">
                    <th style="width: 3px">No.</th>
                    <th >Nama</th>
                    <!-- <th>Syarat</th> -->
                    <?php 
                    if ($paketInfo->metode_lelang_id != '1' && $paketInfo->metode_lelang_id != '7' ) { ?>
                    <th>Diundang</th>
                    <?php 
                    } ?>
                    <th>Tgl<br>Daftar</th>
                    <th>Lulus<br> Pendaftaran</th>
                    <!-- <th>Lulus<br> Pendaftaran</th> -->
                    <th>Dok.<br>Penawaran</th>
                    <th>Lulus<br> Penawaran</th>
                    <th>Sudah<br> Email</th>
                    <th>Email</th>
                  </tr>
                   <? 
                   if ($totalPaket == 0) {
                     echo '<tr><td colspan="8">.:: Belum ada peserta ::.</td></tr>';
                   } else 
                   {
    								  $i=1;
    								  while($paket_rekanan->nextRow())
    								  {
    									  $disable = "";
    							  ?>
                    	<tr>
                        <td><?=$i?>.</td>
                        <td> 
                          <a title="#" onClick="openAdd('main/loadUrl/main/data_rekanan/?reqId=<?=$paket_rekanan->getField("REKANAN_ID")?>');">
                            <i class="fa fa-eye btn btn-info btn-sm text-white" style="padding: 2px 4px !important"></i>
                            <?=$paket_rekanan->getField("REKANAN")?></a>
                        </td>
                        <!-- <td align="center">  -->
                          <!-- <input type="button" onclick="openAdd('main/loadUrl/main/daftar_rekanan_persyaratan/?reqId=<?=$paket_rekanan->getField("REKANAN_ID")?>&reqPaketId=<?=$reqId?>')" value="Lihat" class="btn btn-info"> -->
                          <!-- <a onclick="openAdd('main/loadUrl/main/data_rekanan/?reqId=<?=$paket_rekanan->getField("REKANAN_ID")?>&reqPaketId=<?=$reqId?>')" value="Lihat" class="btn btn-info btn-sm text-white">Lihat</a> -->
                        <!-- </td> -->
                        <?php 
                        if ($paketInfo->metode_lelang_id != '1' && $paketInfo->metode_lelang_id != '7' ) { ?>
                        <td> <?=getFormattedDate($paket_rekanan->getField("TANGGAL_UNDANG"))?> </td>
                        <?php 
                        } else { ?>
                        <td> <?=getFormattedDate($paket_rekanan->getField("TANGGAL_DAFTAR")).' <br> <small> '.$paket_rekanan->getField("JAM_DAFTAR").'</small>'?> 
                        </td>
                        <?php 
                        } ?>
                        <?
                        if($paket_rekanan->getField("TANGGAL_DAFTAR") == "")
                            $disable = 'disabled="disabled"';
                        ?>
                        <td>
                          <input type="hidden" name="reqPaketRekananId[]" value="<?=$paket_rekanan->getField("PAKET_REKANAN_ID")?>" />
                          <input type="hidden" name="reqLulusPendaftaran[]" id="reqLulusPendaftaran<?=$i?>" value="<? if($paket_rekanan->getField("LULUS_PENDAFTARAN") == 2) echo "NULL"; else { echo $paket_rekanan->getField("LULUS_PENDAFTARAN"); } ?>" />
                          <input type="radio" name="reqSetujuiPendaftaran<?=$i?>" onclick="document.getElementById('reqKeterangan<?=$i?>').style.display = 'none'; document.getElementById('reqLulusPendaftaran<?=$i?>').value='1'"  <? if($paket_rekanan->getField("LULUS_PENDAFTARAN") == 1) { ?> checked="checked" <? } ?> style="cursor: pointer;" <?=$disable?> /> Setuju
                          <input type="radio" name="reqSetujuiPendaftaran<?=$i?>" onclick="document.getElementById('reqKeterangan<?=$i?>').style.display = ''; document.getElementById('reqLulusPendaftaran<?=$i?>').value='0'" <? if($paket_rekanan->getField("LULUS_PENDAFTARAN") == 0) { ?> checked="checked" <? } ?> style="cursor: pointer;" <?=$disable?> /> Tolak
                          <br />
    				               <textarea <? if($paket_rekanan->getField("LULUS_PENDAFTARAN") == 0) {} else { ?> style="display:none" <? } ?> name="reqLulusKeterangan[]" id="reqKeterangan<?=$i?>" onclick="clearText('reqKeterangan<?=$i?>')" placeholder="Alasan.."><? if($paket_rekanan->getField("LULUS_PENDAFTARAN_KETERANGAN") == "") { echo ""; } else { echo $paket_rekanan->getField("LULUS_PENDAFTARAN_KETERANGAN"); }?></textarea>
                        </td>
                        <!-- <td align="center"> 
                          <?php // if($paket_rekanan->getField("LULUS_KUALIFIKASI") == 1) {?><i class="fa fa-check" aria-hidden="true"></i> <?php // } ?> 
                        </td> -->
                        <td align="center"> 
                          <? if($paket_rekanan->getField("LULUS_PENAWARAN") == 1) {?><i class="fa fa-check" aria-hidden="true"></i> <? } ?> 
                        </td>
                        <td> 
                          <? if($paket_rekanan->getField("LULUS_PENAWARAN_URUT") == "") {} else { 
                               if($paket_rekanan->getField("LULUS_PENAWARAN_KETERANGAN") == "")
                               {	
                             ?>
                                Terendah <?=$paket_rekanan->getField("LULUS_PENAWARAN_URUT")?> 
                             <?
                               }
                               else
                                echo $paket_rekanan->getField("LULUS_PENAWARAN_KETERANGAN");
                            } 
                          ?>
                        </td>
                        <td align="center">
                          <? if($paket_rekanan->getField("DI_EMAIL") == 2) { ?> 
                            <i class="fa fa-check" aria-hidden="true"></i> 
                          <? } else { ?> 
                            <i class="fa fa-times" aria-hidden="true"></i> 
                            <? } ?> 
                          <label for="reqSudahBayar"></label></td>
                        <td>
                          <?
                          if($paket_rekanan->getField("LULUS_PENDAFTARAN") == "2")
                          {}
                          else
                          {
                          ?>
                          <a onclick="kirimEmail('<?=$paket_rekanan->getField("PAKET_REKANAN_ID")?>')" style="cursor:pointer" class="btn-aksi"><i class="fa fa-paper-plane"></i></a>
                          <?
                          }
                          ?>
                        </td>
                      </tr>
                      <?
                        $i++;
                      }
                    }
                    ?>
                </tbody>
              </table>   
            </div>

            <div class="form-actions">
              <input type="hidden" name="submitSimpan" value="Simpan" />
              <input type="hidden" id="reqMode" name="reqMode" />
              <input type="hidden" name="reqId" value="<?=$reqId?>" />
              <a href="main/index/paket_detil/?reqId=<?=$reqId?>" class="btn btn-danger text-white"> <i class="fa fa-arrow-left"></i> Kembali </a> 
              <?php 
              if ($totalPaket > 0) { ?>
              <a class="btn btn-info text-white" href="main/loadUrl/report/daftar_peserta_lelang_excel/?reqId=<?=$reqId?>" target="_blank" ><i class="fa fa-print"></i> Cetak</a>
              <button type="button" id="btnKirim" class="btn btn-success" onClick="kirimEmailSemua()"><i class="fa fa-envelope"></i> Kirim Email Verifikasi Pendaftaran</button>
              <button type="submit" id="btnSimpan" class="btn btn-primary"><i class="fa fa-check-square-o"></i> Simpan</button>
              <?php 
              } ?>
            </div> 
            
        </div>
      </div>
      </form>
      
    </div>
  </div> 
</div>  