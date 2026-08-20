<?php
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 * 
 */
$this->load->library("paketinfo"); $paketInfo = new paketinfo();
include_once("functions/string.func.php");
include_once("functions/default.func.php");
include_once("functions/date.func.php");
$this->load->model("PaketReschedule");
$this->load->model("Metode");
$this->load->model("Paket");

$metode = new Metode();
$paket_reschedule = new PaketReschedule();

$reqId = $this->input->get("reqId");

$paketInfo->getPaket($reqId);
$reqMetodeLelangId = $paketInfo->metode_lelang_id;
$reqSistemHarga = isset($paketInfo->sistem_harga) ? $paketInfo->sistem_harga : '';

$reqExistData = $metode->getCountByParams(array("PAKET_ID" => coalesce($reqId, 0)));
$metode->selectByParamsReschedule(array(), -1, -1, coalesce($reqId, 0));

if($reqMetodeLelangId == 1 || $reqMetodeLelangId == 3)
{
	$tempPublishTanggalJam = $paketInfo->publish_paket_tanggal;
	$arrPublishTanggalJam = explode(" ", $tempPublishTanggalJam);
	$arrPublishJamMenit = explode(":", $arrPublishTanggalJam[1]);
	$tempPublishTanggal = $arrPublishTanggalJam[0];
	$tempPublishJam = $arrPublishJamMenit[0];
	$tempPublishMenit = $arrPublishJamMenit[1];
}

$rescheduleKe = $paket_reschedule->getRescheduleKe(array("A.PAKET_ID" => $reqId));

?> 

<script type="text/javascript">
$(document).ready(function() {
	
	$(function(){
		$('#ff').form({
			url:'paket_tahap_json/reschedule',
			onSubmit:function(){
				return $(this).form('validate');
			},
			success:function(data){
				$.messager.alert('Info', data, 'info');	
				document.location.href = 'main/index/paket_lelang_tambah_reschedule_jadwal/?reqId=<?=$reqId?>';
				
			}
		});
		
		$("input[id^='reqTanggalSelesai']").datebox({
			onSelect: function(date){
				var idElement = $(this).attr("id").replace("reqTanggalSelesai", "");
				checkTanggal(idElement);
			}
		});		
		
	});
	
});

function checkTanggal(idElement)
{
	var awal = $('#reqTanggalMulai'+idElement).datebox('getValue');	
	var selesai = $('#reqTanggalSelesai'+idElement).datebox('getValue');	
	var triggerAkhir = $('#reqTriggerTanggalAkhir'+idElement).val();
	if(awal == "")
	{
		$.messager.alert('Info', "Tentukan tanggal awal.", 'info');	
		$('#reqTanggalSelesai'+idElement).datebox('setValue', '');	
		return;
	}
	
	var dt1   = parseInt(awal.substring(0,2),10); 
	var mon1  = parseInt(awal.substring(3,5),10);
	var yr1   = parseInt(awal.substring(6,10),10); 
	var dt2   = parseInt(selesai.substring(0,2),10); 
	var mon2  = parseInt(selesai.substring(3,5),10); 
	var yr2   = parseInt(selesai.substring(6,10),10); 
	var date1 = new Date(yr1, mon1, dt1); 
	var date2 = new Date(yr2, mon2, dt2); 
	
	
	if(date2 < date1)
	{
		$.messager.alert('Info', "Tanggal akhir lebih kecil.", 'info');	
		$('#reqTanggalSelesai'+idElement).datebox('setValue', '');	
	}
	
	if(triggerAkhir == "1")
	{
		$('#reqTanggalMulai'+(Number(idElement)+1)).datebox('setValue', selesai);		
	}

}

function checkNotifikasi(id, notifikasi)
{
	if(notifikasi == "PENAWARAN")
	{
		if($("#reqHadir"+id).is(":checked"))
	 		$("#lblNotifikasi"+notifikasi).text("Pemasukan dokumen penawaran melalui offline");
		else
	 		$("#lblNotifikasi"+notifikasi).text("Pemasukan dokumen penawaran melalui online");		
	}
}

</script>

<style type="text/css">
.table th {
    padding: 10px !important;
    background-color: #b7b7b7;
    color: #000;
}    
</style>

<div class="row"> 
  <div class="col-md-12 col-sm-12">
    <div class="card">
      <div class="card-header card-head-inverse bg-primary">
        <h4 class="card-title text-white">Reschedule Jadwal</h4>
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
                <tr valign="top">
                  <th rowspan="3" valign="middle" style="text-align: center;width:10px;"> No </th>
                  <th rowspan="3" valign="middle" style="text-align: center; width: 250px"> Tahapan Lelang </th>
                  <th colspan="2" valign="top" style="text-align: center"> Waktu Pelaksanaan </th>
                  <th colspan="4" valign="top" style="text-align: center"> Reschedule </th>
                  </tr>
                <tr valign="top" class="judul-kolom">
                  <th colspan="1" style="text-align: center"> Mulai </th>
                  <th colspan="1" style="text-align: center"> Selesai </th>
                  <th colspan="2" style="text-align: center"> Mulai </th>
                  <th colspan="2" style="text-align: center"> Selesai </th>
                  </tr>
                <tr valign="top" class="judul-kolom">
                  <th style="text-align: center"> Tanggal </th>
                  <th style="text-align: center"> Tanggal </th>
                  <th style="text-align: center"> Tanggal </th>
                  <th style="text-align: center; width: 158px"> Jam </th>
                  <th style="text-align: center"> Tanggal </th>
                  <th style="text-align: center; width: 158px"> Jam </th>
                </tr>

               <?
                $i=1; $no=1; $stat = ''; $stat_m = '';
                while($metode->nextRow())
                {
                  if($stat == '') $comma = '';  else  $comma = ', ';                
                  $stat .= $comma."#reqJamSelesai$i, #reqJamMulai$i";
                  
                  if($stat_m == '') $comma = '';  else  $comma = ', ';                
                  $stat_m .= $comma."#reqMenitSelesai$i, #reqMenitMulai$i";
                  
                  $disabledTanggalAwal = $metode->getField("TANGGAL_AWAL_DISABLED");
                  $triggerTanggalAkhir = $metode->getField("TANGGAL_AKHIR_TRIGGER");
                ?>
                  <tr valign="top" class="gelap">
                    <td><?=$no?>.</td>
                    <td style="width:calc(50% - 50px)">
                      <?=$metode->getField("NAMA")?>
                      <?
                      $notif = "";
                      $notifikasi = $metode->getField("NOTIFIKASI");
                      if($notifikasi == "PENAWARAN")
                      {
                          if($metode->getField("HADIR_CENTANG") == 1)
                              $notif = "Pemasukan dokumen penawaran melalui offline";
                          else  
                              $notif = "Pemasukan dokumen penawaran melalui online";
                      }
                      if($notifikasi == "")
                      {}
                      else
                      {
                      ?>        
                      <br>                                 
                      <label id="lblNotifikasi<?=$notifikasi?>" style="font-size:10px; font-weight:bold"><?=$notif?></label>
                      <?
                      }
                      ?>
                    </td>
                    <td>
                      <?php 
                      $tglawal = explode(',', getFormattedDateTime($metode->getField("TANGGAL_AWAL")));
                      echo isset($tglawal[0]) ? $tglawal[0] : '.'.'<br>'.isset($tglawal[1]) ? $tglawal[1] : '';
                      ?>
                    </td>
                    <td>
                      <?php 
                      $tglakhir = explode(',', getFormattedDateTime($metode->getField("TANGGAL_AKHIR")));
                      echo isset($tglakhir[0]) ? $tglakhir[0] : '.'.'<br>'.isset($tglakhir[1]) ? $tglakhir[1] : '';
                      ?>
                    </td>
                    <label for="reqTampil"></label></td>
                    <td>                                    
                      <input type="text" class="easyui-datebox span2" name="reqTanggalMulai[<?=$i?>]" id="reqTanggalMulai<?=$i?>" value="<?=datetimeToPage($metode->getField("TANGGAL_AWAL_BARU"), "date")?>" <? if($disabledTanggalAwal == "1") { ?> readonly style="background:#F1F1F1; width: 135% !important" <? } else { ?> style="background:#F1F1F1; width: 135% !important" <? } ?> />                                                    
                    </td>
                    <td>
                      <!-- <label for="reqJamMulai"></label> -->
                      <?
                      $arrJamAwal = explode(":", $metode->getField("JAM_AWAL_BARU"));
                      ?>
                      <input name="reqJamMulai[<?=$i?>]" type="text" id="reqJamMulai<?=$i?>" value="<?=isset($arrJamAwal[0]) ? $arrJamAwal[0] : ''?>" size="2" maxlength="2" <? if($disabledTanggalAwal == "1") { ?> readonly style="background:#F1F1F1; width: 45px; display: inline;" <? } ?> class="form-control" style="width: 45px; display: inline;"/> 
                      : 
                      <!-- <label for="reqMenitMulai"></label> -->
                      <input name="reqMenitMulai[<?=$i?>]" type="text" id="reqMenitMulai<?=$i?>" value="<?=isset($arrJamAwal[1]) ? $arrJamAwal[1] : ''?>" size="2" maxlength="2" <? if($disabledTanggalAwal == "1") { ?> readonly style="background:#F1F1F1; width: 45px; display: inline;" <? } ?> class="form-control" style="width: 45px; display: inline;"/>
                    </td>
                    <td>
                      <input type="text" class="easyui-datebox span2" name="reqTanggalSelesai[<?=$i?>]" id="reqTanggalSelesai<?=$i?>" value="<?=datetimeToPage($metode->getField("TANGGAL_AKHIR_BARU"), "date")?>" style="width: 135% !important"/>
                    </td>
                      <?
                      $arrJamAkhir = explode(":", $metode->getField("JAM_AKHIR_BARU"));
                      ?>                                      
                    <td>
                      <!-- <label for="reqJamMulai"></label> -->
                      <input name="reqJamSelesai[<?=$i?>]" type="text" value="<?=$arrJamAkhir[0]?>" id="reqJamSelesai<?=$i?>" size="2" maxlength="2" <? if($triggerTanggalAkhir == "1") { ?> onKeyUp="$('#reqJamMulai<?=$i+1?>').val(this.value);" <? } ?> class="form-control" style="width: 45px; display: inline;"/>
                      :
                      <!-- <label for="reqMenitMulai"></label> -->
                      <input name="reqMenitSelesai[<?=$i?>]" type="text" value="<?=isset($arrJamAkhir[1]) ? $arrJamAkhir[1] : ''?>" id="reqMenitSelesai<?=$i?>" size="2" maxlength="2" <? if($triggerTanggalAkhir == "1") { ?> onKeyUp="$('#reqMenitMulai<?=$i+1?>').val(this.value);" <? } ?> class="form-control" style="width: 45px; display: inline;"/>
                      <input type="hidden" name="reqTahapanLelang[<?=$i?>]" value="<?=$metode->getField("NAMA")?>" />
                      <input type="hidden" name="reqTriggerTanggalAkhir[<?=$i?>]" id="reqTriggerTanggalAkhir<?=$i?>" value="<?=$triggerTanggalAkhir?>" />
                      <input type="hidden" name="reqAktivitas[<?=$i?>]" value="<?=$metode->getField("AKTIVITAS")?>" />
                      <input type="hidden" name="reqPaketTahapId[<?=$i?>]" value="<?=$metode->getField("PAKET_TAHAP_ID")?>" />
                    </td>
                  </tr>
                <?
                    $i++;
                    $no++;
                }
                ?>
              </tbody>
            </table>   
          </div>
          
          <?
          if($rescheduleKe == "")
          {}
          else
          {
          ?>
            <div class="judul-grup mt-2 alert alert-info">Jadwal Reschedule</div>
            <div class="row">
              <div class="col-md-12">
                <div class="form-group">
                  <table class="table table-bordered" id="tbl_bidang">
                    <tbody>
                      <tr valign="top" class="judul-kolom">
                        <td rowspan="2" valign="middle">No</td>
                        <td rowspan="2" valign="middle">Tahapan Lelang</td>
                        <td colspan="2" valign="top"> Waktu Pelaksanaan </td>
                        <td colspan="2" valign="top"> Reschedule </td>
                        </tr>
                      <tr valign="top" class="judul-kolom">
                        <td colspan="1"> Mulai </td>
                        <td colspan="1">Selesai</td>
                        <td colspan="1"> Mulai </td>
                        <td colspan="1">Selesai</td>
                        </tr>

                     <?
                      $i=1; $no=1; $stat = ''; $stat_m = '';
                      $paket_reschedule->selectByParams(array("PAKET_ID" => $reqId, "RESCHEDULE_KE" => $rescheduleKe));
                      while($paket_reschedule->nextRow())
                      {
                      ?>
                        <tr valign="top" class="gelap">
                          <td><?=$no?>.</td>
                          <td style="width:calc(50% - 50px)">
                            <?=$paket_reschedule->getField("NAMA")?>
                          </td>
                          <td><?=getFormattedDateTime($paket_reschedule->getField("TANGGAL_AWAL"))?></td>
                          <td><?=getFormattedDateTime($paket_reschedule->getField("TANGGAL_AKHIR"))?></td>
                          <td><?=getFormattedDateTime($paket_reschedule->getField("TANGGAL_AWAL_BARU"))?></td>
                          <td><?=getFormattedDateTime($paket_reschedule->getField("TANGGAL_AKHIR_BARU"))?></td>
                        </tr>
                      <?
                        $i++;
                        $no++;
                      }
                      ?>
                    </tbody>
                  </table> 
                </div>
              </div>
            </div>
          <?
            }
          ?>

          <div class="form-actions">
          	<input type="hidden" name="reqId" value="<?=$reqId?>">
          	<input type="hidden" name="submitSimpan" value="Reschedule" />
            <a href="main/index/paket_detil/?reqId=<?=$reqId?>" class="btn btn-danger mr-1 text-white"> <i class="fa fa-arrow-left"></i> Kembali </a> 
            <button type="submit" class="btn btn-primary text-white"><i class="fa fa-check-square-o"></i> Reschedule</button>
            <a onClick="kirimEmail()" class="btn btn-primary text-white pull-right"><span class="fa fa-send"></span> Kirim</a>
          </div> 

        </div>
      </div>
      </form>
    </div>
  </div> 
</div>  

<script>
function kirimEmail()
{
	$.messager.confirm("Konfirmasi","Kirim email reschedule jadwal ke rekanan?",function(r){
		if (r){
			var win = $.messager.progress({
										title:'Kirim Email',
										msg:'Mengirim email reschedule jadwal...'
									});					
			$.get( "paket_tahap_json/kirim_reschedule/?reqId=<?=$reqId?>", function( data ) {
				  if(data == "1")
				  {
					  $.messager.progress('close');
					  $.messager.alert('Info', "Kirim email berhasil.", 'info');	
				  }
				  else
				  	//alert(data);return false;
    					$.messager.progress('close');
					  $.messager.alert('Info', data, 'info');	
			});
		}
	});		
}

</script>
