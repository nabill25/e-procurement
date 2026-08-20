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
$this->load->model("EvaluasiSyaratDaftar");
$this->load->model("PaketEvaluasiSyaratDaftar");

$paket_rekanan = new PaketRekanan();
$paket = new Paket();
$evaluasi_syarat_daftar = new EvaluasiSyaratDaftar();
$paket_evaluasi_syarat_daftar = new PaketEvaluasiSyaratDaftar();

$reqBulan = date("m");
$reqTahun = date("Y");

/* VARIABLES */
$reqMode = httpFilterRequest("reqMode");
$reqId = httpFilterRequest("reqId");

$paket_rekanan->selectByParams(array("PAKET_ID" => $reqId));
$paketInfo->getPaket($reqId);
$reqMetodeLelangId = $paketInfo->metode_lelang_id;
?>

<script type="text/javascript">
$(document).ready(function() {
	
	$(function(){
		$('#ff').form({
			url:'paket_evaluasi_syarat_daftar_json/add', 
			onSubmit:function(){
				return $(this).form('validate');
			},
			success:function(data){
				$.messager.alert('Info', data, 'info');	
				// alert(data);
				document.location.href = "main/index/paket_lelang_tambah_daftar_panitia/?reqId=<?=$reqId?>";
			}
		});
		
	});
	
});

function ambilKeteranganBulan(id)
{
	var bulan_tahun = $('#reqEvaluasiBulan'+id).val();
	
	if(bulan_tahun == '')
	{
		$("#reqEvaluasiText"+id).text('');
		$("#reqEvaluasiKeterangan"+id).val('');		
	}
	else
	{

		$.getJSON('fungsi_json/get_bulan_rekening_koran/?reqId='+bulan_tahun, function (data) 
		{
			$("#reqEvaluasiText"+id).text(data.REKENING_KORAN);
			$("#reqEvaluasiKeterangan"+id).val(data.REKENING_KORAN);			
			$("#reqEvaluasiValue"+id).val(data.REKENING_KORAN_SET_VALID);
		});
		
	}
		
}

function addRowPaketSyarat(tableID) {
	var table = document.getElementById(tableID);

	var rowCount = $('#'+tableID+' tr').length;
	var row = table.insertRow(rowCount);


	var cell2 = row.insertCell(0);
	cell2.innerHTML = rowCount + '<input type="hidden" name="reqCheck['+ (rowCount) +']" id="reqCheck'+ (rowCount) +'" value="1">';

	var cell3 = row.insertCell(1);
	cell3.innerHTML = '<input name="reqEvaluasi['+(rowCount)+']" type="text" id="reqEvaluasi" value=""  class="form-control span5 easyui-validatebox"   />';


	var cell4 = row.insertCell(2);
	cell4.innerHTML = '<input name="reqEvaluasiKeterangan['+(rowCount)+']" type="text" id="reqEvaluasiKeterangan" value="" class="form-control span5 easyui-validatebox"  />';

	var cell4 = row.insertCell(3);
	cell4.innerHTML = '<a title="#" onclick="addRowPaketSyarat(\'dataTableAdmin\')" class="btn-aksi"><i class="fa fa-plus" aria-hidden="true"></i></a>';
	
	var rowLast = table.rows[rowCount - 1];
	var cell5 = rowLast.deleteCell(3);
	var cell6 = rowLast.insertCell(3);
	cell6.innerHTML = '<a title="#" onclick="$(this).parent().parent().remove();" class="btn-aksi"><i class="fa fa-trash" aria-hidden="true"></i></a>';
}

</script>

<div class="row"> 
  <div class="col-md-12 col-sm-12">
    <div class="card">
      <div class="card-header card-head-inverse bg-primary">
        <h4 class="card-title text-white">Persyaratan Pendaftaran <?= $paketInfo->metode_lelang_nama ?><!--(Optional)--></h4>
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
                <tbody id="dataTableAdmin">
                  <tr>
                    <th style="text-align: center">No</th>
                    <th align="center">Uraian</th>
                    <th colspan="2" align="center">Keterangan</th>
                  </tr>
                   <?php
                  $i = 1;
                  $style="gelap";
                  $evaluasi_syarat_daftar->selectByParamsEvaluasiPaket($reqId);
                  while($evaluasi_syarat_daftar->nextRow())
                  {
                  ?>
                  <tr class="<?=$style?>">
                    <td style="width: 5%; text-align: center"><?=$i?></td>
                    <td style="width: 45%">
                      <label for="reqEvaluasi"><?=$evaluasi_syarat_daftar->getField("NAMA")?></label>
                      <input name="reqEvaluasi[<?=$i?>]" type="hidden" id="reqEvaluasi" value="<?=$evaluasi_syarat_daftar->getField("NAMA")?>" readonly />
                      <input name="reqEvaluasiNumber[<?=$i?>]" type="hidden" id="reqEvaluasiNumber" value="<?=$evaluasi_syarat_daftar->getField("EVALUASI_NUMBER")?>" />
                    </td>
                    <td style="width: 45%">
                      <?php
                      if($evaluasi_syarat_daftar->getField("TIPE") == "BULAN")
                      {
                        $year = $year0 = $year1 = $year2 = $reqTahun;
                        
                        $month = (int)$reqBulan;
                        if($month <= 0) 	{	$year = date("Y") - 1;	$month = 12 + $month;	$monthname = getNameMonth($month);	}
                        else					$monthname = getNameMonth($month);
                        
                        $month0 = $reqBulan - 1;
                        if($month0 <= 0) 	{	$year0 = date("Y") - 1;	$month0 = 12 + $month0;	$monthname0 = getNameMonth($month0);	}
                        else					$monthname0 = getNameMonth($month0);
                        
                        $month1 = $reqBulan - 2;
                        if($month1 <= 0) 	{	$year1 = date("Y") - 1;	$month1 = 12 + $month1;	$monthname1 = getNameMonth($month1);}	
                        else					$monthname1 = getNameMonth($month1);
                        
                        $month2 = $reqBulan - 3;
                        if($month2 <= 0) 	{	$year2 = date("Y") - 1;	$month2 = 12 + $month2;	$monthname2 = getNameMonth($month2);}
                        else					$monthname2 = getNameMonth($month2);
                      ?>
                      <select name="reqEvaluasiBulan" id="reqEvaluasiBulan<?=$i?>" onChange="ambilKeteranganBulan('<?=$i?>');" class="form-control span2">
                          <option></option>
                          <option value="<?=$month2."-".$year2?>"><?=$monthname2." ".$year2?></option>
                          <option value="<?=$month1."-".$year1?>"><?=$monthname1." ".$year1?></option>
                          <option value="<?=$month0."-".$year0?>"><?=$monthname0." ".$year0?></option>
                          <option value="<?=$month."-".$year?>"><?=$monthname." ".$year?></option>
                      </select>
                      <label id="reqEvaluasiText<?=$i?>"><?=$evaluasi_syarat_daftar->getField("KETERANGAN")?></label>
                      <?php
                      $text = "";
                      if($evaluasi_syarat_daftar->getField("EVALUASI_NUMBER") == 8)
                          $text = $paketInfo->syarat_rekening_koran_bulan;
                      elseif($evaluasi_syarat_daftar->getField("EVALUASI_NUMBER") == 11)
                          $text = $paketInfo->syarat_keuangan_bulan_ppn;
                      elseif($evaluasi_syarat_daftar->getField("EVALUASI_NUMBER") == 12)
                          $text = $paketInfo->syarat_keuangan_bulan_pph;
                      ?>
                      <input name="reqEvaluasiValue[<?=$i?>]" type="hidden" id="reqEvaluasiValue<?=$i?>" value="<?=$text?>" />                  
                      <input name="reqEvaluasiKeterangan[<?=$i?>]" type="hidden" id="reqEvaluasiKeterangan<?=$i?>" value="<?=$evaluasi_syarat_daftar->getField("KETERANGAN")?>" />                  
                      <?php
                      }
                      elseif($evaluasi_syarat_daftar->getField("TIPE") == "TAHUN")
                      {
                          $tahun_terpilih = str_replace("SPT Tahun ", "", $evaluasi_syarat_daftar->getField("KETERANGAN"));
                       ?>
                        <select name="reqEvaluasiValue[]" id="reqEvaluasiValue<?=$i?>" onChange="document.getElementById('reqEvaluasiKeterangan<?=$i?>').value = 'SPT Tahun ' + document.getElementById('reqEvaluasiValue<?=$i?>').value" class="form-control span2">
                            <option></option>
                            <?php
                            for($tahun=(date("Y")-3);$tahun<=date("Y");$tahun++)
                            {
                            ?>
                                <option value="<?=$tahun?>" <?php if($tahun_terpilih == $tahun) { ?> selected <?php } ?>><?=$tahun?></option>
                            <?php
                            }
                            ?>
                        </select>
                        <input name="reqEvaluasiKeterangan[<?=$i?>]" type="hidden" id="reqEvaluasiKeterangan<?=$i?>" value="<?=$evaluasi_syarat_daftar->getField("KETERANGAN")?>" />
                       <?php
                      }
                      elseif($evaluasi_syarat_daftar->getField("TIPE") == "TAHUN_FLEX")
                      {
                          $tahun_terpilih = trim(str_replace("Tahun ", "", $evaluasi_syarat_daftar->getField("KETERANGAN")));
                          $check1 = $check2 = $check3 = "";
                          if($tahun_terpilih == (date("Y")-1))
                            $check1 = "selected";
                          elseif($tahun_terpilih == (date("Y")-2)."/".(date("Y")-1))
                            $check2 = "selected";					  
                          else
                            $check3 = "selected";					  
                       ?>
                        <select name="reqEvaluasiValue[]" id="reqEvaluasiValue<?=$i?>" onChange="document.getElementById('reqEvaluasiKeterangan<?=$i?>').value = 'Tahun ' + document.getElementById('reqEvaluasiValue<?=$i?>').value" class="form-control span2">
                            <option></option>
                            <option value="<?=date("Y")-1?>" <?=$check1?>><?=date("Y")-1?></option>
                            <option value="<?=date("Y")-2?>/<?=date("Y")-1?>" <?=$check2?>><?=date("Y")-2?>/<?=date("Y")-1?></option>
                            <option value="<?=date("Y")-2?>" <?=$check3?>><?=date("Y")-2?></option>
                        </select>
                        <input name="reqEvaluasiKeterangan[<?=$i?>]" type="hidden" id="reqEvaluasiKeterangan<?=$i?>" value="<?=$evaluasi_syarat_daftar->getField("KETERANGAN")?>" />
                       <?php
                      }				  
                      else
                      {
                      ?>
                        <input class="form-control span5 easyui-validatebox" name="reqEvaluasiKeterangan[<?=$i?>]" type="text" id="reqEvaluasiKeterangan<?=$i?>" value="<?=$evaluasi_syarat_daftar->getField("KETERANGAN")?>" />                  
                      <?php
                      }
                      ?>
                    </td>
                    <td style="width: 5%; text-align: center;">
                      <input type="checkbox" style="cursor: pointer;" name="reqCheck[<?=$i?>]" id="reqCheck<?=$i?>" value="1" <?php if($evaluasi_syarat_daftar->getField("EVALUASI_NUMBER_PAKET") == "") {} else { ?> checked <?php } ?>>
                      <input type="hidden" name="reqFieldName[<?=$i?>]" value="<?=$evaluasi_syarat_daftar->getField("PAKET_FIELD_NAME")?>">
                      <input type="hidden" name="reqFieldInfo[<?=$i?>]" value="<?=$evaluasi_syarat_daftar->getField("PAKET_FIELD_INFO")?>">
                    </td>
                  </tr>
                  <?php
                    $i++;
                    if($style == "gelap")
                        $style = "terang";
                    else
                        $style = "gelap";
                  }
                  ?>
                            
                </tbody>
              </table>   
            </div>

            <div class="form-actions">
            	<input type="hidden" name="reqId" value="<?=$reqId?>">
            	<input type="hidden" name="submitSimpan" value="SimpanSyarat" />
                <?php
                if($reqMetodeLelangId == 2 || $reqMetodeLelangId == 4 || $reqMetodeLelangId == 5)
									$link = "paket_lelang_tambah_rekanan";
								else
									$link = "paket_lelang_tambah_jadwal";
								?>                                        
              <a href="main/index/<?=$link?>/?reqId=<?=$reqId?>" class="btn btn-danger mr-1 text-white"> <i class="fa fa-arrow-left"></i> Kembali </a> 
              <button type="submit" class="btn btn-primary pull-right">Lanjut <i class="fa fa-arrow-right"></i></button>
            </div> 

        </div>
      </div>
      </form>
      
    </div>
  </div> 
</div>   