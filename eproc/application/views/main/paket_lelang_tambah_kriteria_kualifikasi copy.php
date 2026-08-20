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
$this->load->model("Paket");
$this->load->model("PaketEvaluasiAdmin");
$this->load->model("PaketEvaluasiKeuangan");
$this->load->model("PaketKriteriaEvaluasi");
$this->load->model("PaketEvaluasiKemampuanDasar");
$this->load->model("PaketEvaluasiPengalaman");
$this->load->model("PaketEvaluasiPersonil");
$this->load->model("PaketEvaluasiSertifikatLain");
$this->load->model("PaketEvaluasiPeralatan");
$this->load->model("PaketEvaluasiPeralatanDetil");

$paket = new Paket();
$paket_evaluasi_admin = new PaketEvaluasiAdmin();
$paket_evaluasi_keuangan = new PaketEvaluasiKeuangan();
$paket_kriteria_evaluasi = new PaketKriteriaEvaluasi();
$paket_evaluasi_kemampuan_dasar = new PaketEvaluasiKemampuanDasar();
$paket_evaluasi_pengalaman = new PaketEvaluasiPengalaman();
$paket_evaluasi_personil = new PaketEvaluasiPersonil();
$paket_evaluasi_peralatan = new PaketEvaluasiPeralatan();
$paket_evaluasi_peralatan_detil = new PaketEvaluasiPeralatanDetil();
$paket_evaluasi_sertifikat_lain = new PaketEvaluasiSertifikatLain();

$reqId = $this->input->get("reqId");

$paketInfo->getPaket($reqId);
$reqNama = $paketInfo->nama;
$reqKualifikasi = $paketInfo->kualifikasi;
$reqKualifikasiId = $paketInfo->kualifikasi_id;
$reqNilai = $paketInfo->nilai;
$reqPaketJenis = $paketInfo->jenis_id;
$reqTahun = getYear($paketInfo->tanggal_pemasukan);
$reqBulan = (int)getMonth($paketInfo->tanggal_pemasukan);

$paket_evaluasi_keuangan->selectByParams(array("PAKET_ID" => $reqId));
$paket_evaluasi_keuangan->firstRow();
$paket_evaluasi_pengalaman->selectByParams(array("PAKET_ID" => $reqId));
$paket_evaluasi_pengalaman->firstRow();

//VARIABLE
$dimPengaliSKK = 0.2;
$dimPengaliPengalaman = 0.5;
$dimFP = 6;
$dimFPB = 8;
$dimFL =  0.3;
$dimFLB = 0.8;
    
//PERHITUNGAN SEGMENTASI SKK
if($paket_evaluasi_keuangan->getField("SKK2RPMIN") != '' 
    and $paket_evaluasi_keuangan->getField("SKK2RPMIN")==$paket_evaluasi_keuangan->getField("SKK3RP")
    and $paket_evaluasi_keuangan->getField("SKK1RP")== $reqNilai)
    $nilaiSkkHitung =$paket_evaluasi_keuangan->getField("SKK2RPMIN");
else
    $nilaiSkkHitung = $reqNilai * $dimPengaliSKK;

//PERHITUNGAN SEGMENTASI PENGALAMAN
if($paket_evaluasi_pengalaman->getField("NK2_RPMIN") != '' 
   and $paket_evaluasi_pengalaman->getField("NK2_RPMIN") == $paket_evaluasi_pengalaman->getField("NK3_RP")
   and $paket_evaluasi_pengalaman->getField("NK1_RP")==$reqNilai)
    $nilaiNKHitung =$paket_evaluasi_pengalaman->getField("NK2_RPMIN");
else
    $nilaiNKHitung = $reqNilai * $dimPengaliPengalaman;
	
	
$paket_evaluasi_admin->selectByParams(array("B.PAKET_ID" => $reqId));
$paket_kriteria_evaluasi->selectByParams(array("PAKET_ID" => $reqId));
$paket_kriteria_evaluasi->firstRow();
$paket_evaluasi_kemampuan_dasar->selectByParams(array("PAKET_ID" => $reqId));
$paket_evaluasi_kemampuan_dasar->firstRow();


$paket_evaluasi_personil->selectByParams(array("PAKET_ID" => $reqId));
$i = 0;
while($paket_evaluasi_personil->nextRow())
{
	if($i == 0){
		$reqPersonilNilaiMinimum = $paket_evaluasi_personil->getField("NILAI_MINIMUM");
		
		/*update tanggal 12-10-2012*/
		$reqPersonilMinimum= $paket_evaluasi_personil->getField("NILAI_MINIMAL");
	}
	
	$tombol_simpan_personil = false;
	if($paket_evaluasi_personil->getField("JABATAN") == "")
	{}
	else
	{
		$arrPersonilId[] = $paket_evaluasi_personil->getField("PAKET_EVAL_PERSONIL_ID");
		$arrPersonilJabatan[] = $paket_evaluasi_personil->getField("JABATAN");
		$arrPersonilPendidikan[] = $paket_evaluasi_personil->getField("PENDIDIKAN");
		$arrPersonilPengalaman[] = $paket_evaluasi_personil->getField("PENGALAMAN");
		$arrPersonilJumlah[] = $paket_evaluasi_personil->getField("JUMLAH");
		$arrPersonilNilai[] = $paket_evaluasi_personil->getField("NILAI");
		$arrPersonilSKA[] = $paket_evaluasi_personil->getField("SKA");
		$arrPersonilCV[] = $paket_evaluasi_personil->getField("CV");	
		$arrPersonilEntri[] = $paket_evaluasi_personil->getField("JUMLAH_ENTRI");			
		$i++;
		$jumlah_entri_personil += $paket_evaluasi_personil->getField("JUMLAH_ENTRI");
	}
}
if($jumlah_entri_personil > 0)
	$tombol_simpan_personil = true;
	
$paket_evaluasi_peralatan->selectByParams(array("PAKET_ID" => $reqId));
$paket_evaluasi_peralatan->firstRow();
$paket_evaluasi_peralatan_detil->selectByParams(array("PAKET_ID" => $reqId));

$paket_evaluasi_sertifikat_lain->selectByParams(array("PAKET_ID" => $reqId));
$i = 0;
while($paket_evaluasi_sertifikat_lain->nextRow())
{
	if($i == 0){
		$reqSertifikatNilai = $paket_evaluasi_sertifikat_lain->getField("NILAI_MINIMUM");
		
		/*update tanggal 12-10-2012*/
		$reqSertifikatlainNilaiMinimum = $paket_evaluasi_sertifikat_lain->getField("NILAI_MINIMAL");
	}
	if($paket_evaluasi_sertifikat_lain->getField("NAMA") == "")
	{}
	else
	{	
		$arrSertifikatId[] 	   	   = $paket_evaluasi_sertifikat_lain->getField("PAKET_EVAL_SERTIFIKAT_LAIN_ID");
		$arrSertifikatNama[] 	   = $paket_evaluasi_sertifikat_lain->getField("NAMA");
		$arrSertifikatKeterangan[] = $paket_evaluasi_sertifikat_lain->getField("KETERANGAN");
		$arrSertifikatDetilNilai[] = $paket_evaluasi_sertifikat_lain->getField("NILAI");
		$i++;
	}
}

/* call again for refresh data after insert */
$paketInfo->getPaket($reqId);
$reqPassingGrade = $paketInfo->passing_grade;

?>

<script>
function createRowDataAdministrasi()
{
	$(function () {
		$.get("main/loadUrl/main/kriteria_kualifikasi_data_administrasi_template", function (data) {
			$("#tbodyDataAdministrasi").append(data);
		});
	});	
	var rowCount = $('#tbodyDataAdministrasi tr').length;
	$('#tbodyDataAdministrasi td:nth-child(2)').eq(rowCount-1).html('<a title="#" onclick="$(this).parent().parent().remove();" class="btn-aksi"><i class="btn btn-danger fa fa-trash" aria-hidden="true"></i></a>');
}

function createRowSertifikatKualifikasi()
{
	$(function () {
		$.get("main/loadUrl/main/kriteria_kualifikasi_sertifikat_template", function (data) {
			$("#tbodySertifikat").append(data);
		});
	});	

	var rowCount = $('#tbodySertifikat tr').length;
	$('#tbodySertifikat td:nth-child(4)').eq(rowCount-1).html('<a title="#" onclick="$(this).parent().parent().remove();" class="btn-aksi"><i class="btn btn-danger fa fa-trash" aria-hidden="true"></i></a>');

}


function createRowPeralatanKualifikasi()
{
	$(function () {
		$.get("main/loadUrl/main/kriteria_kualifikasi_peralatan_template", function (data) {
			$("#tbodyPeralatan").append(data);
		});
	});	

	var rowCount = $('#tbodyPeralatan tr').length;
	$('#tbodyPeralatan td:nth-child(4)').eq(rowCount-1).html('<a title="#" onclick="$(this).parent().parent().remove();" class="btn-aksi"><i class="btn btn-danger fa fa-trash" aria-hidden="true"></i></a>');

}


function createRowPersonilKualifikasi()
{
	$(function () {
		$.get("main/loadUrl/main/kriteria_kualifikasi_personil_template", function (data) {
			$("#tbodyPersonil").append(data);
		});
	});	

	var rowCount = $('#tbodyPersonil tr').length;
	$('#tbodyPersonil td:nth-child(8)').eq(rowCount-1).html('<a title="#" onclick="$(this).parent().parent().remove();" class="btn-aksi"><i class="btn btn-danger fa fa-trash" aria-hidden="true"></i></a>');

}

$(function(){
	$('#reqBulanKualifikasi').bind('change', function(ev) {
		var bulan_tahun = $('#reqBulanKualifikasi').val();
		$.getJSON('fungsi_json/get_bulan_rekening_koran/?reqId='+bulan_tahun, function (data) 
		{
			$("#reqSaldoBulan").val(data.REKENING_KORAN);	
		});
	});
	
});

$(document).ready(function() {
	
	$(function(){
		$('#ff').form({
			url:'paket_json/kriteria_kualifikasi',
			onSubmit:function(){
				return $(this).form('validate');
			},
			success:function(data){
				$.messager.alert('Informasi',data, 'info');
			}
		});
		
	});
	
});

</script>

<div class="row"> 
  <div class="col-md-12 col-sm-12">
    <div class="card">
      <div class="card-header card-head-inverse bg-primary">
        <h4 class="card-title text-white">Kriteria Kualifikasi <?= $paketInfo->metode_lelang_nama ?></h4>
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
          
          <div class="card mb-1 border-blue border-darken-1">
            <div class="table-responsive">
              <table class="table table-bordered mb-0"> 
                <thead>
                  <tr class="judul-kolom">
                    <th>Kriteria</th>
                    <th>
                      Pilih<br>
                      <input type="checkbox" name="reqPilihAll" id="reqPilihAll" onChange="cek_semua(document.frmInformasiAdd.reqPilih)"/>
                    <label for="reqPilihAll"></label>
                    </th>
                  </tr>
                </thead>
                <tbody id="tbodyDataAdministrasi">
                  <?				
                  $i = 1;
                  while($paket_evaluasi_admin->nextRow())
                  {
                  ?>                
                  <tr>
                    <td><input type="hidden" name="reqEvaluasiAdministrasi[<?=$i?>]" value="<?=$paket_evaluasi_admin->getField("NAMA")?>" /><?=$paket_evaluasi_admin->getField("NAMA")?> </td>
                    <td align="center"><input type="checkbox" name="reqCheck[<?=$i?>]" id="reqPilih" value="1" <? if($paket_evaluasi_admin->getField("STATUS") == 1) { ?> checked="checked"<? } ?> />
                    <label for="reqPilih"></label></td>
                  </tr>
                  <?
                      $i++;
                  }
                  ?>

                  <?
                  $paket_evaluasi_admin_kualifikasi = new PaketEvaluasiAdmin();
                  $paket_evaluasi_admin_kualifikasi->selectByParamsProses(array("PAKET_ID" => $reqId), -1,-1, " AND NOT EXISTS(SELECT 1 FROM EVAL_ADMIN X WHERE X.EVALUASI_NUMBER = A.EVALUASI_NUMBER) ");
                  while($paket_evaluasi_admin_kualifikasi->nextRow())
                  {
                  ?>
                  <tr>
                    <td>
                      <div class="row">
                        <div class="form-group col-md-12 mb-2">
                          <label for="reqEvaluasiAdministrasi"></label>
                          <input name="reqEvaluasiAdministrasi[<?=$i?>]" type="text" id="reqEvaluasiAdministrasi" value="<?=$paket_evaluasi_admin_kualifikasi->getField("NAMA")?>" size="100" class="form-control" />
                          <input type="hidden" name="reqCheck[<?=$i?>]" id="reqCheck<?=$i?>" value="1">
                        </div> 
                      </div> 
                    </td>
                    <td align="center"><a title="#" onclick="$(this).parent().parent().remove();" class="btn-aksi"><i class="btn btn-danger fa fa-trash" aria-hidden="true"></i></a></td>
                  </tr>
                  <?
                    $i++;
                  }
                  ?>
                  <tr>
                    <td>
                      <div class="row">
                        <div class="form-group col-md-12 mb-2">
                          <label for="reqEvaluasiAdministrasi"></label>
                          <input name="reqEvaluasiAdministrasi[<?=$i?>]" type="text" id="reqEvaluasiAdministrasi" value="" size="100" class="form-control span10" />
                          <input type="hidden" name="reqCheck[<?=$i?>]" id="reqPilih<?=$i?>" value="1">
                        </div> 
                      </div>  
                    </td>
                    <td align="center"><a title="#" onclick="createRowDataAdministrasi()" class="btn-aksi"><i class="btn btn-primary fa fa-plus" aria-hidden="true"></i></a></td>
                  </tr>
      		      </tbody>
              </table>   
            </div>
          </div>

          <div class="card mb-1 border-blue border-darken-1">
            <div class="card-content">
              <div class="p-1">
                <div class="alert bg-primary alert-icon-left alert-arrow-left alert-dismissible mb-2" role="alert">
                  <span class="alert-icon"><i class="fa fa-th"></i></span>  <strong>EVALUASI DATA ADMINISTRASI</strong>  
                </div> 
                 
                <div class="alert alert-info">II. EVALUASI KEUANGAN</div> 
                <div class="table-responsive">
                  <table class="table table-bordered table-hover">
                    <tr class="judul-kolom">
                      <th rowspan="2" align="center">No.</th>
                      <th rowspan="2" align="center" style="width:734px">Kriteria</th>
                      <th rowspan="2" align="center">Bobot</th>
                      <th rowspan="2" align="center">Nilai</th>
                      <th>Pilih</th>
                    </tr>
                    <tr class="judul-kolom">
                      <th><input type="checkbox" name="reqPilihAll2" id="reqPilihAll2" onChange="cek_semua2(document.frmInformasiAdd.reqPilih2)"/>
                      <label for="reqPilihAll2"></label></th>
                    </tr>
                    <tr class="gelap">
                      <td>1.</td>
                      <td><strong>Saldo Rekening 3 bulan terakhir</strong></td>
                      <td>&nbsp;</td>
                      <td>&nbsp;</td>
                      <td><input type="checkbox" name="reqSaldoPilih" value="1" id="reqPilih2" <? if($paket_kriteria_evaluasi->getField("SALDO")) { ?> checked="checked" <? } ?>  />
                      <label for="reqSaldoPilih"></label></td>
                    </tr>
                    <tr class="terang">
                      <td>&nbsp;</td>
                      <?
                        $year = $year1 = $year2 = $reqTahun;
                        
                        $month = $reqBulan - 1;
                        if($month <= 0) 
                        {
                            $year = date("Y") - 1;
                            $month = 12 + $month;
                            $monthname = getNameMonth($month);
                        }
                        else
                            $monthname = getNameMonth($month);
                        $month1 = $reqBulan - 2;
                        if($month1 <= 0) 
                        {
                            $year1 = date("Y") - 1;
                            $month1 = 12 + $month1;
                            $monthname1 = getNameMonth($month1);
                        }	
                        else
                            $monthname1 = getNameMonth($month1);
                        $month2 = $reqBulan - 3;
                        if($month2 <= 0) 
                        {
                            $year2 = date("Y") - 1;
                            $month2 = 12 + $month2;
                            $monthname2 = getNameMonth($month2);
                        }									
                        else
                            $monthname2 = getNameMonth($month2);
                      ?>
                      <td> - Bulan 
                      <input type="text" id="reqSaldoBulan" name="reqSaldoBulan" readonly style='font-family:"Century Gothic", sans-serif; background-color:#f2faff; font-size:12px; width:300px; border:none' value="<? if($paket_evaluasi_keuangan->getField("REKENING_BULAN") == "") { ?><?=$monthname2." ".$year2?>, <?=$monthname1." ".$year1?>, <?=$monthname." ".$year?> <? } else { echo $paket_evaluasi_keuangan->getField("REKENING_BULAN"); } ?>" /> (Bulan Terakhir 
                      <?
                      if($paket_evaluasi_keuangan->getField("REKENING_BULAN") == "")
                      {
                      ?>
                      <select name="reqBulanKualifikasi" id="reqBulanKualifikasi">
                        <option value="<?=$month2."-".$year2?>"><?=$monthname2." ".$year2?></option>
                        <option value="<?=$month1."-".$year1?>"><?=$monthname1." ".$year1?></option>
                        <option value="<?=$month."-".$year?>" selected="selected"><?=$monthname." ".$year?></option>
                      </select>                                    
                      <?
                      }
                      else
                      {
                       $arrBulanTahun = explode(",", $paket_evaluasi_keuangan->getField("REKENING_BULAN"));
                      ?>
                      <select name="reqBulanKualifikasi" id="reqBulanKualifikasi">
                        <option value="<?=$month2."-".$year2?>" <? if(trim($arrBulanTahun[2]) == $monthname2." ".$year2) { ?> selected="selected" <? } ?>><?=$monthname2." ".$year2?></option>
                        <option value="<?=$month1."-".$year1?>" <? if(trim($arrBulanTahun[2]) == $monthname1." ".$year1) { ?> selected="selected" <? } ?>><?=$monthname1." ".$year1?></option>
                        <option value="<?=$month."-".$year?>"   <? if(trim($arrBulanTahun[2]) == $monthname." ".$year) { ?> selected="selected" <? } ?>><?=$monthname." ".$year?></option>
                      </select>                                                      
                      <?  
                      }
                      ?>
                      )</td>
                      <td>&nbsp;</td>
                      <td>&nbsp;</td>
                      <td>&nbsp;</td>
                    </tr>
                    <tr class="gelap">
                      <td>&nbsp;</td>
                      <td> - Saldo Minimal Tiap Bulan : 
                        <label for="reqSaldoMinimal"></label>
                        <input type="text" name="reqSaldoMinimal" id="reqSaldoMinimal" value="<?=numberToIna($paket_evaluasi_keuangan->getField("SALDO_REK_MIN"))?>"  OnFocus="FormatAngka('reqSaldoMinimal')" OnKeyUp="FormatUang('reqSaldoMinimal')" OnBlur="FormatUang('reqSaldoMinimal')" class="form-rounded" />
                        </td>
                      <td>&nbsp;</td>
                      <td>&nbsp;</td>
                      <td>&nbsp;</td>
                    </tr>
                    <tr class="terang">
                      <td>&nbsp;</td>
                      <td> - Saldo Rekening Koran &lt; Rp. <span id="saldo_gt_copy"><?=currencyToPage($paket_evaluasi_keuangan->getField("SALDO_REK_MIN"), "")?></span> dinyatakan gugur </td>
                      <td>&nbsp;</td>
                      <td>&nbsp;</td>
                      <td>&nbsp;</td>
                    </tr>
                    <tr class="gelap">
                      <td>2.</td>
                      <td><strong>SKK</strong></td>
                      <td>&nbsp;</td>
                      <td>&nbsp;</td>
                      <td><input type="checkbox" name="reqSKKPilih" value="1" id="reqPilih2"  <? if($paket_kriteria_evaluasi->getField("SKK")) { ?> checked="checked" <? } ?> />
                      <label for="reqSKKPilih"></label></td>
                    </tr>
                    <tr class="terang">
                      <td>&nbsp;</td>
                      <td><strong>FP (Faktor Perputaran modal) *</strong></td>
                      <td>
                        <?
                        if($reqKualifikasiId == 1)
                            echo coalesce($paket_evaluasi_keuangan->getField("FP"), $dimFP);
                        else
                            echo coalesce($paket_evaluasi_keuangan->getField("FPB"), $dimFPB);					
                        ?>
                      </td>
                      <td>&nbsp;</td>
                      <td>&nbsp;</td>
                    </tr>
                    <tr class="gelap">
                      <td>&nbsp;</td>
                      <td><strong>FL (Faktor Likuiditas) *</strong></td>
                      <td>
                        <?
                        if($reqKualifikasiId == 1)
                            echo coalesce($paket_evaluasi_keuangan->getField("FL"), $dimFL);
                        else
                            echo coalesce($paket_evaluasi_keuangan->getField("FLB"), $dimFLB);					
                        ?>
                      </td>
                      <td>&nbsp;</td>
                      <td>&nbsp;</td>
                    </tr>
                    <tr class="terang">
                      <td>&nbsp;</td>
                      <td> <strong>Nilai maksimum lulus evaluasi keuangan </strong></td>
                      <td>&nbsp;</td>
                      <td><label for="reqSKKNilaiMaksimum"></label>
                      <input name="reqSKKNilaiMaksimum" type="text" id="reqSKKNilaiMaksimum" value="<?=$paket_evaluasi_keuangan->getField("SKK1NILAI")?>" style="width:40px; background:#09F" maxlength="5" class="form-rounded" /></td>
                      <td>&nbsp;</td>
                    </tr>
                    <tr class="gelap">
                      <td>&nbsp;</td>
                      <td> Range dan bobot SKK </td>
                      <td>&nbsp;</td>
                      <td>&nbsp;</td>
                      <td>&nbsp;</td>
                    </tr>
                    <tr class="terang">
                      <td>&nbsp;</td>
                      <td> - SKK &ge; Rp. <strong>
                      <?= currencyToPage($reqNilai,"")?>
                        </strong>&nbsp;&nbsp;&nbsp;( Prosentase : <input name="reqSKKTinggi" type="text" value="100" readonly id="reqSKKTinggi" style="width:40px" maxlength="5" class="form-rounded" /> % )</td>
                      <td>&nbsp;</td>
                      <td>&nbsp;</td>
                      <td>&nbsp;</td>
                    </tr>
                    <tr class="gelap">
                      <td>&nbsp;</td>
                      <td> - SKK antara Rp. <strong>
                      <?= currencyToPage($nilaiSkkHitung, "")?>
                            </strong> s.d. Rp. <strong>
                      <?= currencyToPage($reqNilai,"")?>
                            </strong>&nbsp;&nbsp;&nbsp;( Prosentase : <input name="reqSKKSedang" type="text" value="50" readonly id="reqSKKSedang" style="width:40px" maxlength="5" class="form-rounded" /> % )</td>
                      <td></td>
                      <td>&nbsp;</td>
                      <td>&nbsp;</td>
                    </tr>
                    <tr class="terang">
                      <td>&nbsp;</td>
                      <td> - SKK &lt; Rp. <strong>
                      <?= currencyToPage($nilaiSkkHitung, "")?>
                            </strong> &nbsp;&nbsp;&nbsp;( Prosentase : <input name="reqSKKRendah" type="text" value="0" readonly id="reqSKKRendah" style="width:40px" maxlength="5" class="form-rounded" /> % )</td>
                      <td></td>
                      <td>&nbsp;</td>
                      <td>&nbsp;</td>
                    </tr>
                    <tr class="gelap">
                      <td>&nbsp;</td>
                      <td><strong>Nilai minimum lulus evaluasi keuangan</strong></td>
                      <td>&nbsp;</td>
                      <td><input name="reqSKKNilaiMinimum" type="text" id="reqSKKNilaiMinimum" value="<?=$paket_evaluasi_keuangan->getField("NILAI_LULUS")?>" style="width:40px" class="form-rounded" /></td>
                      <td>&nbsp;</td>
                    </tr>
                  </table> 
                </div>
              
                <div class="alert alert-info">EVALUASI DATA HARGA</div> 
                <div class="table-responsive">
                  <table class="table table-bordered table-hover">
                    <tr class="judul-kolom">
                      <th rowspan="2" align="center">No.</th>
                      <th colspan="2" rowspan="2" align="center">Kriteria</th>
                      <th rowspan="2" align="center">Bobot</th>
                      <th rowspan="2" align="center">Nilai</th>
                      <th>Pilih</th>
                    </tr>
                    <tr class="judul-kolom">
                      <th><input type="checkbox" name="reqPilihAll3" id="reqPilihAll3" onChange="cek_semua3(document.frmInformasiAdd.reqPilih3)"/>
                      <label for="reqPilihAll3"></label>
                      </th>
                    </tr>
                    <?
                    switch($reqPaketJenis)
                    {
                        case 1:
                            $kali = 2;
                            break;
                        case 2:
                            $kali = 3;
                            break;
                        case 3:	
                            $kali = 5;
                            break;
                    }
                    $kontrak_min = $paket_evaluasi_kemampuan_dasar->getField("NILAI_KONTRAK_MIN") * $kali;
                    
                    ?>

                    <tr class="terang" style="display:none">
                      <td>A</td>
                      <td colspan="2"> KEMAMPUAN DASAR (KD = <?=$kali?> NPt) </td>
                      <td>&nbsp;</td>
                      <td>&nbsp;</td>
                      <td><input name="reqKDPilih" value="1" type="checkbox" id="reqPilih3"  <? if($paket_kriteria_evaluasi->getField("KEMAMPUAN_DASAR")) { ?> checked="checked" <? } ?> /></td>
                    </tr>
                    <tr class="terang" style="display:none">
                      <td>&nbsp;</td>
                      <td colspan="2"> Pekerjaan  
                        <label for="reqKDPekerjaan"></label>
                      <input type="text" name="reqKDPekerjaan" id="reqKDPekerjaan" value="<?=$paket_evaluasi_kemampuan_dasar->getField("PEKERJAAN")?>" /></td>
                      <td>&nbsp;</td>
                      <td>&nbsp;</td>
                      <td>&nbsp;</td>
                    </tr>
                    <tr class="terang" style="display:none">
                      <td>&nbsp;</td>                  
                      <td colspan="2"> Nilai kontrak minimum Rp.  
                      <input name="reqKDNilaiMinimum" id="reqKDNilaiMinimum" type="text" value="<?=numberToIna($paket_evaluasi_kemampuan_dasar->getField("NILAI_KONTRAK_MIN"))?>"  OnFocus="FormatAngka('reqKDNilaiMinimum')" OnKeyUp="FormatUang('reqKDNilaiMinimum')" OnBlur="FormatUang('reqKDNilaiMinimum')" style="width:140px" />
                      <?php /*?><input name="reqKDNilaiMinimum" type="text" value="<?=$paket_evaluasi_kemampuan_dasar->getField("NILAI_KONTRAK_MIN")?>" size="20" maxlength="3" /><?php */?>
                      </td>
                      <td>&nbsp;</td>
                      <td>&nbsp;</td>
                      <td>&nbsp;</td>
                    </tr>
                    <tr class="terang" style="display:none">
                      <td>&nbsp;</td>
                      <td colspan="2"> Pengalaman dalam waktu 
                        <input name="reqKDPengalaman" type="text" value="<?=$paket_evaluasi_kemampuan_dasar->getField("PENGALAMAN_TAHUN")?>" style="width:40px" maxlength="3" />
                       tahun terakhir </td>
                      <td>&nbsp;</td>
                      <td>&nbsp;</td>
                      <td>&nbsp;</td>
                    </tr>
                    <tr class="terang" style="display:none">
                      <td>&nbsp;</td>
                      <td colspan="2"> KD &lt; <span id="kd_lt_copy"><?=currencyToPage($kontrak_min)?></span> dinyatakan gugur </td>
                      <td>&nbsp;</td>
                      <td>&nbsp;</td>
                      <td>&nbsp;</td>
                    </tr>
                    <tr class="terang" style="display:none">
                      <td>&nbsp;</td>
                      <td colspan="2"> KD &gt; <span id="kd_gt_copy"><?=currencyToPage($kontrak_min)?></span> dinyatakan memenuhi syarat </td>
                      <td>&nbsp;</td>
                      <td>&nbsp;</td>
                      <td>&nbsp;</td>
                    </tr>
                    <tr class="terang" style="display:none">
                      <td>&nbsp;</td>
                      <td>&nbsp;</td>
                      <td>&nbsp;</td>
                      <td>&nbsp;</td>
                      <td>&nbsp;</td>
                      <td>&nbsp;</td>
                    </tr>
                    <tr class="terang">
                      <td>A</td>
                      <td colspan="2"> PENILAIAN PENGALAMAN PERUSAHAAN </td>
                      <td>&nbsp;</td>
                      <td>&nbsp;</td>
                      <td>&nbsp;</td>
                    </tr>
                    <tr class="terang">
                      <td>&nbsp;</td>
                      <td colspan="2"> <strong>Nilai maksimum pengalaman perusahaan</strong> </td>
                      <td>&nbsp;</td>
                      <td><input name="reqSTNilaiMaksimum" type="text" id="reqSTNilaiMaksimum" value="<?=$paket_evaluasi_pengalaman->getField("NILAI_MAKSIMUM")?>" style="width:40px; background:#09F" maxlength="5" class="form-rounded" /></td>
                      <td>&nbsp;</td>
                    </tr>                                

                    <tr class="terang">
                      <td>&nbsp;</td>
                      <td>&raquo;</td>
                      <td> Jumlah Pengalaman </td>
                      <td></td>
                      <td></td>
                      <td></td>
                    </tr>
                    <tr class="terang">
                      <td>&nbsp;</td>
                      <td>&nbsp;</td>
                      <td> a. Jumlah pengalaman <input name="reqJumlahPengalamanA" type="text" style="width:40px;" value="<? if($paket_evaluasi_pengalaman->getField("JUMLAH_PENGALAMAN_A") == "") echo "3"; else echo $paket_evaluasi_pengalaman->getField("JUMLAH_PENGALAMAN_A");?>" class="form-rounded"> &nbsp;&nbsp;&nbsp;
                          ( Prosentase : <input name="reqProsentasePengalamanA" type="text" id="reqProsentasePengalamanA" value="<? if($paket_evaluasi_pengalaman->getField("PROSENTASE_PENGALAMAN_A") == "") echo "100"; else echo $paket_evaluasi_pengalaman->getField("PROSENTASE_PENGALAMAN_A");?>" style="width:40px" maxlength="5" class="form-rounded" /> % )</td>
                      <td></td>
                      <td>&nbsp;</td>
                      <td>&nbsp;</td>
                    </tr>
                    <tr class="terang">
                      <td>&nbsp;</td>
                      <td>&nbsp;</td>
                      <td> b. Jumlah pengalaman <input name="reqJumlahPengalamanB" type="text" style="width:40px;" value="<? if($paket_evaluasi_pengalaman->getField("JUMLAH_PENGALAMAN_B") == "") echo "2"; else echo $paket_evaluasi_pengalaman->getField("JUMLAH_PENGALAMAN_B");?>" class="form-rounded"> &nbsp;&nbsp;&nbsp;
                          ( Prosentase : <input name="reqProsentasePengalamanB" type="text" id="reqProsentasePengalamanB" value="<? if($paket_evaluasi_pengalaman->getField("PROSENTASE_PENGALAMAN_B") == "") echo "50"; else echo $paket_evaluasi_pengalaman->getField("PROSENTASE_PENGALAMAN_B");?>" style="width:40px" maxlength="5" class="form-rounded" /> % )</td>
                      <td></td>
                      <td>&nbsp;</td>
                      <td>&nbsp;</td>
                    </tr>
                    <tr class="terang">
                      <td>&nbsp;</td>
                      <td>&nbsp;</td>
                      <td> c. Jumlah pengalaman <input name="reqJumlahPengalamanC" type="text" style="width:40px;" value="<? if($paket_evaluasi_pengalaman->getField("JUMLAH_PENGALAMAN_C") == "") echo "1"; else echo $paket_evaluasi_pengalaman->getField("JUMLAH_PENGALAMAN_C");?>" class="form-rounded"> &nbsp;&nbsp;&nbsp;
                          ( Prosentase : <input name="reqProsentasePengalamanC" type="text" id="reqProsentasePengalamanC" value="<? if($paket_evaluasi_pengalaman->getField("PROSENTASE_PENGALAMAN_C") == "") echo "25"; else echo $paket_evaluasi_pengalaman->getField("PROSENTASE_PENGALAMAN_C");?>" style="width:40px" maxlength="5" class="form-rounded" /> % )</td>
                      <td></td>
                      <td>&nbsp;</td>
                      <td>&nbsp;</td>
                    </tr>
                    <tr class="terang">
                      <td>&nbsp;</td>
                      <td>&nbsp;</td>
                      <td> d. Jumlah pengalaman <input name="reqJumlahPengalamanD" type="text" style="width:40px;" value="<? if($paket_evaluasi_pengalaman->getField("JUMLAH_PENGALAMAN_D") == "") echo "0"; else echo $paket_evaluasi_pengalaman->getField("JUMLAH_PENGALAMAN_D");?>" class="form-rounded"> &nbsp;&nbsp;&nbsp;
                          ( Prosentase : <input name="reqProsentasePengalamanD" type="text" id="reqProsentasePengalamanD" value="<? if($paket_evaluasi_pengalaman->getField("PROSENTASE_PENGALAMAN_D") == "") echo "0"; else echo $paket_evaluasi_pengalaman->getField("PROSENTASE_PENGALAMAN_D");?>" style="width:40px" maxlength="5" class="form-rounded" /> % )</td>
                      <td></td>
                      <td>&nbsp;</td>
                      <td>&nbsp;</td>
                    </tr>                           

                    <tr class="terang">
                      <td>&nbsp;</td>
                      <td>&raquo;</td>
                      <td> Bidang Pekerjaan </td>
                      <td>
                        <input name="reqBPProsentase" type="text" id="reqBPProsentase" value="<?=$paket_evaluasi_pengalaman->getField("BP_NILAI_PROSENTASE")?>" style="width:40px" maxlength="5" class="form-rounded" /> %
                        <input name="reqBPNilai" type="hidden" id="reqBPNilai" value="<?=$paket_evaluasi_pengalaman->getField("BP_NILAI")?>" style="width:40px" />
                      </td>
                      <td></td>
                      <td><input name="reqBPPilih" value="1" type="checkbox" id="reqPilih3" <? if($paket_kriteria_evaluasi->getField("BIDANG_KERJA")) { ?> checked="checked" <? } ?> /></td>
                    </tr>
                    <tr class="terang">
                      <td>&nbsp;</td>
                      <td>&nbsp;</td>
                      <td> a. Pekerjaan dengan bidang dan subbidang sama&nbsp;&nbsp;&nbsp;( Prosentase : <input name="reqBPSama" type="text" id="reqBPSama" value="<? if($paket_evaluasi_pengalaman->getField("BP_SUB_SAMA_PERSEN") == "") echo "100"; else echo $paket_evaluasi_pengalaman->getField("BP_SUB_SAMA_PERSEN");?>" style="width:40px" maxlength="5" class="form-rounded" /> % )</td>
                      <td></td>
                      <td>&nbsp;</td>
                      <td>&nbsp;</td>
                    </tr>
                    <tr class="terang">
                      <td>&nbsp;</td>
                      <td>&nbsp;</td>
                      <td> b. Pekerjaan dengan bidang sama dan subbidang berbeda&nbsp;&nbsp;&nbsp;( Prosentase : <input name="reqBPBeda" type="text" id="reqBPBeda" value="<? if($paket_evaluasi_pengalaman->getField("BP_SUB_BEDA_PERSEN") == "") echo "50"; else echo $paket_evaluasi_pengalaman->getField("BP_SUB_BEDA_PERSEN");?>" style="width:40px" maxlength="5" class="form-rounded" /> % )</td>
                      <td></td>
                      <td>&nbsp;</td>
                      <td>&nbsp;</td>
                    </tr>
                    <tr class="terang">
                      <td>&nbsp;</td>
                      <td>&raquo;</td>
                      <td> Besar Nilai Kontrak </td>
                      <td>
                        <input name="reqNKProsentase" type="text" id="reqNKProsentase" value="<?=$paket_evaluasi_pengalaman->getField("NK_NILAI_PROSENTASE")?>" style="width:40px" maxlength="5" class="form-rounded" /> %
                        <input name="reqNKNilai" type="hidden" id="reqNKNilai" value="<?=$paket_evaluasi_pengalaman->getField("NK_NILAI")?>" style="width:40px"  />
                      </td>
                      <td>&nbsp;</td>
                      <td><input name="reqNKPilih" value="1" type="checkbox" id="reqPilih3" <? if($paket_kriteria_evaluasi->getField("NILAI_KONTRAK")) { ?> checked="checked" <? } ?> /></td>
                    </tr>
                    <tr class="terang">
                      <td>&nbsp;</td>
                      <td>&nbsp;</td>
                      <td> - Nilai kontrak &ge; Rp. <strong><?= currencyToPage($reqNilai, "")?></strong>&nbsp;&nbsp;&nbsp;( Prosentase : <input name="reqNKBesarPersen" type="text" id="reqNKBesarPersen" value="<? if($paket_evaluasi_pengalaman->getField("NK1_PERSEN") == "") echo "100"; else echo $paket_evaluasi_pengalaman->getField("NK1_PERSEN");?>" style="width:40px" maxlength="5" class="form-rounded" /> % )</td>
                      <td></td>
                      <td>&nbsp;</td>
                      <td>&nbsp;</td>
                    </tr>
                    <tr class="terang">
                      <td>&nbsp;</td>
                      <td>&nbsp;</td>
                      <td> - Rp. <strong><? echo currencyToPage($nilaiNKHitung, "");?></strong> &le; nilai kontrak &lt; Rp. <strong><? echo currencyToPage($reqNilai, "");?></strong>&nbsp;&nbsp;&nbsp;( Prosentase : <input name="reqNKSedangPersen" type="text" id="reqNKSedangPersen" value="<? if($paket_evaluasi_pengalaman->getField("NK2_PERSEN") == "") echo "50"; else echo $paket_evaluasi_pengalaman->getField("NK2_PERSEN");?>" style="width:40px" maxlength="5" class="form-rounded" /> % )</td>
                      <td><table id="tbl_teknik4" cellspacing="1">
                        <tbody>
                          <tr>
                            <td></td>
                          </tr>
                        </tbody>
                      </table>
                      <br /></td>
                      <td>&nbsp;</td>
                      <td>&nbsp;</td>
                    </tr>
                    <tr class="terang">
                      <td>&nbsp;</td>
                      <td>&nbsp;</td>
                      <td> - Nilai kontrak &lt; Rp. <strong><?echo currencyToPage($nilaiNKHitung, "");?></strong>&nbsp;&nbsp;&nbsp;( Prosentase : <input name="reqNKKecilPersen" type="text" id="reqNKKecilPersen" value="<? if($paket_evaluasi_pengalaman->getField("NK3_PERSEN") == "") echo "0"; else echo $paket_evaluasi_pengalaman->getField("NK3_PERSEN");?>" style="width:40px" maxlength="5" class="form-rounded" /> % )</td>
                      <td><table id="tbl_teknik5" cellspacing="1">
                        <tbody>
                          <tr>
                            <td></td>
                          </tr>
                        </tbody>
                      </table>
                      <br /></td>
                      <td>&nbsp;</td>
                      <td>&nbsp;</td>
                    </tr>
                    <tr class="terang">
                      <td>&nbsp;</td>
                      <td>&raquo;</td>
                      <td> Status Penyedia Jasa </td>
                      <td>
                        <input name="reqSTJasaProsentase" type="text" id="reqSTJasaProsentase" value="<?=$paket_evaluasi_pengalaman->getField("STBU_NILAI_PROSENTASE")?>" style="width:40px" maxlength="5" class="form-rounded" /> %
                        <input name="reqSTJasaNilai" type="hidden" id="reqSTJasaNilai" value="<?=$paket_evaluasi_pengalaman->getField("STBU_NILAI")?>" style="width:40px"  />
                      </td>
                      <td>&nbsp;</td>
                      <td><input name="reqSTPilih" value="1" type="checkbox" id="reqPilih3" <? if($paket_kriteria_evaluasi->getField("STATUS_PENYEDIA")) { ?> checked="checked" <? } ?> /></td>
                    </tr>
                    <tr class="terang">
                      <td>&nbsp;</td>
                      <td>&nbsp;</td>
                      <td> - Sebagai kontraktor utama&nbsp;&nbsp;&nbsp;( Prosentase : <input name="reqSTKontraktorNilai" type="text" id="reqSTKontraktorNilai" value="<? if($paket_evaluasi_pengalaman->getField("STBU_UTAMA_PERSEN") == "") echo "100"; else echo $paket_evaluasi_pengalaman->getField("STBU_UTAMA_PERSEN");?>" style="width:40px" maxlength="5" class="form-rounded" /> % )</td>
                      <td></td>
                      <td>&nbsp;</td>
                      <td>&nbsp;</td>
                    </tr>
                    <tr class="terang">
                      <td>&nbsp;</td>
                      <td>&nbsp;</td>
                      <td> - Sebagai subkontraktor&nbsp;&nbsp;&nbsp;( Prosentase : <input name="reqSTSubKontraktorNilai" type="text" id="reqSTSubKontraktorNilai" value="<? if($paket_evaluasi_pengalaman->getField("STBU_SUB_PERSEN") == "") echo "30"; else echo $paket_evaluasi_pengalaman->getField("STBU_SUB_PERSEN");?>" style="width:40px" maxlength="5" class="form-rounded" /> % )</td>
                      <td></td>
                      <td>&nbsp;</td>
                      <td>&nbsp;</td>
                    </tr>
                    <tr class="terang">
                      <td>&nbsp;</td>
                      <td colspan="2"> <strong>Nilai minimum lulus penilaian pengalaman perusahaan</strong></td>
                      <td>&nbsp;</td>
                      <td><input name="reqSTNilaiMinimum" type="text" id="reqSTNilaiMinimum" value="<?=$paket_evaluasi_pengalaman->getField("NILAI_MINIMAL")?>" style="width:40px" maxlength="5" class="form-rounded" /></td>
                      <td>&nbsp;</td>
                    </tr>
                    <tr class="terang">
                      <td>&nbsp;</td>
                      <td>&nbsp;</td>
                      <td>&nbsp;</td>
                      <td>&nbsp;</td>
                      <td>&nbsp;</td>
                      <td>&nbsp;</td>
                    </tr>
                    <tr class="terang">
                      <td>&nbsp;</td>
                      <td>&nbsp;</td>
                      <td>&nbsp;</td>
                      <td>&nbsp;</td>
                      <td>&nbsp;</td>
                      <td>&nbsp;</td>
                    </tr>
                    <tr class="terang">
                      <td>B</td>
                      <td colspan="2"> PENILAIAN PERSONIL </td>
                      <td>&nbsp;</td>
                      <td>&nbsp;</td>
                      <td><input name="reqPersonilPilih" value="1" type="checkbox" id="reqPilih3" <? if($paket_kriteria_evaluasi->getField("PERSONIL")) { ?> checked="checked" <? } ?> /></td>
                    </tr>
                    <tr class="terang">
                      <td>&nbsp;</td>
                      <td colspan="2"> <strong>Nilai maksimum lulus penilaian personil</strong> </td>
                      <td>&nbsp;</td>
                      <td><input name="reqPersonilNilaiMinimum" type="text" id="reqPersonilNilaiMinimum" value="<?=$reqPersonilNilaiMinimum?>" style="width:40px; background:#09F" maxlength="5" class="form-rounded" /></td>
                      <td>&nbsp;</td>
                    </tr>
                    <tr class="terang">
                      <td>&nbsp;</td>
                      <td colspan="2">
                      <table  id="dataTablePersonil">
                      <thead>
                        <tr class="judul-kolom">
                          <th align="center">Uraian</th>
                          <th align="center">Ijasah</th>
                          <th align="center">Pengalaman</th>
                          <th align="center">Jumlah</th>
                          <th colspan="2" align="center">Kelengkapan Data</th>
                          <th colspan="2"  align="center">Nilai (%)</th>
                        </tr>
                     </thead>
                     <tbody id="tbodyPersonil">
                            <?
                            $no = 1;
                            $personal_id_in = "";
                            for($i=0;$i<count($arrPersonilPengalaman);$i++)
                            {
                            ?>
                            <tr>
                                <td><input type="text" name="reqPersonilKualifikasi[]" style="width:200px" value="<?=$arrPersonilJabatan[$i]?>" class="form-rounded" /></td>
                                <td><select name='reqPendidikan[]' id='reqPendidikan'>
                                    <option value=''></option>
                                    <option value='1' <? if($arrPersonilPendidikan[$i] == 1) { ?> selected="selected" <? } ?>>S1</option>
                                    <option value='2' <? if($arrPersonilPendidikan[$i] == 2) { ?> selected="selected" <? } ?>>S2</option>
                                    <option value='3' <? if($arrPersonilPendidikan[$i] == 3) { ?> selected="selected" <? } ?>>S3</option>
                                    <option value='4' <? if($arrPersonilPendidikan[$i] == 4) { ?> selected="selected" <? } ?>>D3</option>
                                    <option value='5' <? if($arrPersonilPendidikan[$i] == 5) { ?> selected="selected" <? } ?>>D4</option>
                                    <option value='6' <? if($arrPersonilPendidikan[$i] == 6) { ?> selected="selected" <? } ?>>SLTA</option>
                                    </select>
                                </td>
                                <td><input name="reqPersonilPengalaman[]" type="text" style="width:40px" value="<?=$arrPersonilPengalaman[$i]?>" /> th</td>
                                <td><input name="reqPersonilJumlah[]" type="text" style="width:40px" value="<?=$arrPersonilJumlah[$i]?>" /></td>
                                <td><input type="hidden" name="reqPersonilSKA[]" id="reqPersonilSKA<?=$no?>" value="<?=$arrPersonilSKA[$i]?>" /><input name="reqPersonilSKACheckbox[]" value="1" type="checkbox" onchange="setEvaluasiPenawaran(this, 'reqPersonilSKA<?=$no?>')" <? if($arrPersonilSKA[$i] == 1) { ?> checked="checked" <? } ?> /> SKA</td>
                                <td><input type="hidden" name="reqPersonilCV[]" id="reqPersonilCV<?=$no?>" value="<?=$arrPersonilCV[$i]?>" /><input name="reqPersonilCV[]" value="1" type="checkbox" onchange="setEvaluasiPenawaran(this, 'reqPersonilCV<?=$no?>')" <? if($arrPersonilCV[$i] == 1) { ?> checked="checked" <? } ?> /> CV</td>
                                <td><input name="reqPersonilNilai[]" type="text" style="width:40px" value="<?=$arrPersonilNilai[$i]?>" class="form-rounded" /></td>
                                <td align="center">
                                    <a onclick="$(this).parent().parent().remove();" class="btn-aksi"><i class="btn btn-danger fa fa-trash" aria-hidden="true"></i></a>
                                    <input type="hidden" name="reqPersonilId[]" value="<?= $arrPersonilId[$i]?>" />
                                </td>                        
                            </tr>
                            <?
                                $no++;
                            }

                            ?>
                        <tr>
                            <td><input type="text" name="reqPersonilKualifikasi[]" style="width:200px" class="form-rounded" /></td>
                            <td><select name='reqPendidikan[]' id='reqPendidikan'><option value=''></option><option value='1'>S1</option><option value='2'>S2</option><option value='3'>S3</option><option value='4'>D3</option><option value='5'>D4</option><option value='6'>SLTA</option></select></td>
                            <td><input name="reqPersonilPengalaman[]" type="text" style="width:40px" class="form-rounded" /> th</td>
                            <td><input name="reqPersonilJumlah[]" type="text" style="width:40px" class="form-rounded" /></td>
                            
                            <td><input type="hidden" name="reqPersonilSKA[]" id="reqPersonilSKA1" /><input name="reqPersonilSKACheckbox[]" value="1" type="checkbox" onchange="setEvaluasiPenawaran(this, 'reqPersonilSKA1')" /> SKA</td>
                            <td><input type="hidden" name="reqPersonilCV[]" id="reqPersonilCV1" /><input name="reqPersonilCV[]" value="1" type="checkbox" onchange="setEvaluasiPenawaran(this, 'reqPersonilCV1')" /> CV</td>
                            <td><input name="reqPersonilNilai[]" type="text" style="width:40px" class="form-rounded" /></td>
                            <td align="center"><a onclick="createRowPersonilKualifikasi()" class="btn-aksi"><i class="fa fa-plus" aria-hidden="true"></i></a></td>
                        </tr>
                      </tbody>
                      </table>
                      </td>
                      <td>&nbsp;</td>
                      <td>&nbsp;</td>
                      <td>&nbsp;</td>
                    </tr>
                    <tr class="terang">
                        <td>&nbsp;</td>
                      <td colspan="5">
                      <?
                      if($tombol_simpan_personil == true)
                        echo 'Apabila tombol <i class="fa fa-trash" aria-hidden="true"></i> hilang, anda tidak diperbolehkan untuk menghapus data karena terdapat peserta <br>yang telah melakukan entri data sebelumnya.';
                      ?>
                      </td>
                    </tr>
                    <tr class="terang">
                      <td>&nbsp;</td>
                      <td colspan="2"> <strong>Nilai minimum lulus penilaian personil</strong></td>
                      <td>&nbsp;</td>
                      <td><input name="reqPersonilMinimum" type="text" id="reqPersonilMinimum" value="<?=$reqPersonilMinimum?>" style="width:40px" maxlength="5" class="form-rounded" /></td>
                      <td>&nbsp;</td>
                    </tr>
                    <tr class="terang">
                      <td>&nbsp;</td>
                      <td>&nbsp;</td>
                      <td>&nbsp;</td>
                      <td>&nbsp;</td>
                      <td>&nbsp;</td>
                      <td>&nbsp;</td>
                    </tr>
                    <tr class="terang">
                      <td>C</td>
                      <td colspan="2"> PENILAIAN PERALATAN </td>
                      <td>&nbsp;</td>
                      <td>&nbsp;</td>
                      <td><input name="reqPeralatanPilih" value="1" type="checkbox" id="reqPilih3" <? if($paket_kriteria_evaluasi->getField("PERALATAN")) { ?> checked="checked" <? } ?> /></td>
                    </tr>
                    <tr class="terang">
                      <td>&nbsp;</td>
                      <td colspan="2"> <strong>Nilai maksimum lulus penilaian peralatan</strong> </td>
                      <td>&nbsp;</td>
                      <td><input name="reqPeralatanNilai" type="text" id="reqPeralatanNilai" value="<?=$paket_evaluasi_peralatan->getField("NILAI_MINIMUM")?>" style="width:40px; background:#09F" maxlength="5" class="form-rounded" /></td>
                      <td>&nbsp;</td>
                    </tr>                
                    <tr class="terang">
                      <td>&nbsp;</td>
                      <td>-</td>
                      <td> Milik sendiri/sewa beli ada bukti (MSB)&nbsp;&nbsp;&nbsp;( Prosentase : <input name="reqPeralatanMSB" type="text" id="reqMSB" value="100" style="width:40px" maxlength="5" readonly class="form-rounded" /> % )</td>
                      <td><table id="tbl_teknik6" cellspacing="1">
                        <tbody>
                          <tr>
                            <td></td>
                          </tr>
                        </tbody>
                      </table>
                      <br /></td>
                      <td>&nbsp;</td>
                      <td>&nbsp;</td>
                    </tr>
                    <tr class="terang">
                      <td>&nbsp;</td>
                      <td>-</td>
                      <td> Sewa jangka panjang ada bukti (SPJB)&nbsp;&nbsp;&nbsp;( Prosentase : <input name="reqPeralatanSPJB" type="text" id="reqSPJB" value="90" style="width:40px" maxlength="5" readonly class="form-rounded" /> % )</td>
                      <td><table id="tbl_teknik7" cellspacing="1">
                        <tbody>
                          <tr>
                            <td></td>
                          </tr>
                        </tbody>
                      </table>
                      <br /></td>
                      <td>&nbsp;</td>
                      <td>&nbsp;</td>
                    </tr>
                    <tr class="terang">
                      <td>&nbsp;</td>
                      <td>-</td>
                      <td> Sewa jangka pendek ada bukti (SPDB)&nbsp;&nbsp;&nbsp;( Prosentase : <input name="reqPeralatanSPDB" type="text" id="reqSPDB" value="50" style="width:40px" maxlength="5" readonly class="form-rounded" /> % )</td>
                      <td><table id="tbl_teknik8" cellspacing="1">
                        <tbody>
                          <tr>
                            <td></td>
                          </tr>
                        </tbody>
                      </table>
                      <br /></td>
                      <td>&nbsp;</td>
                      <td>&nbsp;</td>
                    </tr>                
                    <tr class="terang">
                      <td>&nbsp;</td>
                      <td>&nbsp;</td>
                      <td>&nbsp;</td>
                      <td>&nbsp;</td>
                      <td>&nbsp;</td>
                      <td>&nbsp;</td>
                    </tr>                
                    <tr class="terang">
                      <td>&nbsp;</td>
                      <td colspan="2"> Peralatan Minimum: </td>
                      <td>&nbsp;</td>
                      <td>&nbsp;</td>
                      <td>&nbsp;</td>
                    </tr>
                    <tr class="terang">
                      <td>&nbsp;</td>
                      <td colspan="2">
                      <table id="dataTablePeralatan">
                      <thead>
                        <tr class="judul-kolom">
                          <th align="center">Nama</th>
                          <th align="center">Uraian</th>
                          <th align="center" colspan="2">Nilai (%)</th>
                        </tr>
                     </thead>
                     <tbody id="tbodyPeralatan">
                        <?
                        $no = 1;
                        while($paket_evaluasi_peralatan_detil->nextRow())
                        {
                        ?>
                        <tr>
                            <td>
                            <input type="hidden" name="reqPeralatanId[]" value="<?=$paket_evaluasi_peralatan_detil->getField("PAKET_EVAL_PERALATAN_DETIL_ID")?>" />
                            <input type="text" name="reqPeralatanNama[]" style="width:200px" value="<?=$paket_evaluasi_peralatan_detil->getField("NAMA")?>" class="form-rounded" /></td>
                            <td><input name="reqPeralatanKeterangan[]" type="text" style="width:220px" value="<?=$paket_evaluasi_peralatan_detil->getField("KETERANGAN")?>" class="form-rounded" /></td>
                            <td><input name="reqPeralatanDetilNilai[]" type="text" style="width:40px" value="<?=$paket_evaluasi_peralatan_detil->getField("NILAI")?>" class="form-rounded" /></td>
                            <td align="center"><a onclick="$(this).parent().parent().remove();" class="btn-aksi"><i class="fa fa-trash" aria-hidden="true"></i></a></td>                        
                        </tr>
                        <?
                            $no++;
                        }
                        ?>
                        <tr>
                            <td><input type="hidden" name="reqPeralatanId[]" value="" />
                            <input type="text" name="reqPeralatanNama[]" style="width:200px" class="form-rounded" /></td>
                            <td><input name="reqPeralatanKeterangan[]" type="text" style="width:220px" class="form-rounded" /></td>
                            <td><input name="reqPeralatanDetilNilai[]" type="text" style="width:40px" class="form-rounded" /></td>
                            <td align="center"><a onclick="createRowPeralatanKualifikasi()" class="btn-aksi"><i class="fa fa-plus" aria-hidden="true"></i></a></td>
                        </tr>
                      </tbody>
                      </table>
                      </td>
                      <td>&nbsp;</td>
                      <td>&nbsp;</td>
                      <td>&nbsp;</td>
                    </tr>
                    <tr class="terang">
                      <td>&nbsp;</td>
                      <td colspan="2"> <strong>Nilai minimum lulus penilaian peralatan</strong></td>
                      <td>&nbsp;</td>
                      <td><input name="reqPeralatanNilaiMinimum" type="text" id="reqPeralatanNilaiMinimum" value="<?=$paket_evaluasi_peralatan->getField("NILAI_MINIMAL")?>" style="width:40px" maxlength="5" class="form-rounded" /></td>
                      <td>&nbsp;</td>
                    </tr>                
                    <tr class="terang">
                      <td>&nbsp;</td>
                      <td>&nbsp;</td>
                      <td>&nbsp;</td>
                      <td>&nbsp;</td>
                      <td>&nbsp;</td>
                      <td>&nbsp;</td>
                    </tr>
                    <tr class="terang">
                      <td>D</td>
                      <td colspan="2"> SERTIFIKAT LAIN </td>
                      <td>&nbsp;</td>
                      <td>&nbsp;</td>
                      <td><input name="reqSertifikatPilih" value="1" type="checkbox" id="reqPilih3" <? if($paket_kriteria_evaluasi->getField("SERTIFIKAT_LAIN")) { ?> checked="checked" <? } ?> /></td>
                    </tr>
                    <tr class="terang">
                      <td>&nbsp;</td>
                      <td colspan="2"> <strong>Nilai maksimum lulus penilaian sertifikat</strong> </td>
                      <td>&nbsp;</td>
                      <td><input name="reqSertifikatNilai" type="text" id="reqSertifikatNilai" value="<?=$paket_evaluasi_sertifikat_lain->getField("NILAI_MINIMUM")?>" style="width:40px; background:#09F" maxlength="5" class="form-rounded" /></td>
                      <td>&nbsp;</td>
                    </tr>
                    <tr class="terang">
                      <td>&nbsp;</td>
                      <td colspan="2">
                      <table id="dataTableSertifikat">
                      <thead>
                        <tr class="judul-kolom">
                          <th align="center">Nama</th>
                          <th align="center">Uraian</th>
                          <th align="center" colspan="2">Nilai (%)</th>
                        </tr>
                      </thead>
                      <tbody id="tbodySertifikat">
                        <?
                        $no = 1;
                        for($i=0;$i<count($arrSertifikatNama);$i++)
                        {
                        ?>
                        <tr>
                            <td><input type="hidden" name="reqSertifikatId[]" value="<?=$arrSertifikatId[$i]?>" />
                            <input type="text" name="reqSertifikatNama[]" style="width:200px" value="<?=$arrSertifikatNama[$i]?>" class="form-rounded" /></td>
                            <td><input name="reqSertifikatKeterangan[]" type="text" style="width:220px" value="<?=$arrSertifikatKeterangan[$i]?>" class="form-rounded" /></td>
                            <td><input name="reqSertifikatDetilNilai[]" type="text" style="width:40px" value="<?=$arrSertifikatDetilNilai[$i]?>" class="form-rounded" /></td>
                            <td align="center"><a onclick="$(this).parent().parent().remove();" class="btn-aksi"><i class="fa fa-trash" aria-hidden="true"></i></a></td>                        
                        </tr>
                        <?
                            $no++;
                        }
                        ?>
                        <tr>
                            <td><input type="hidden" name="reqSertifikatId[]" value="" /><input type="text" name="reqSertifikatNama[]" style="width:200px" class="form-rounded" /></td>
                            <td><input name="reqSertifikatKeterangan[]" type="text" style="width:220px" class="form-rounded" /></td>
                            <td><input name="reqSertifikatDetilNilai[]" type="text" style="width:40px" class="form-rounded" /></td>
                            <td align="center"><a onclick="createRowSertifikatKualifikasi()" class="btn-aksi"><i class="fa fa-plus" aria-hidden="true"></i></a></td>
                        </tr>
                      </tbody>
                      </table>
                      </td>
                      <td>&nbsp;</td>
                      <td>&nbsp;</td>
                      <td>&nbsp;</td>
                    </tr>
                    <tr class="terang">
                      <td>&nbsp;</td>
                      <td colspan="2"> <strong>Nilai minimum lulus penilaian sertifikat</strong></td>
                      <td>&nbsp;</td>
                      <td><input name="reqSertifikatlainNilaiMinimum" type="text" id="reqSertifikatlainNilaiMinimum" value="<?=$paket_evaluasi_sertifikat_lain->getField("NILAI_MINIMAL")?>" style="width:40px" maxlength="5" class="form-rounded" /></td>
                      <td>&nbsp;</td>
                    </tr>    
                    <tr class="terang">
                      <td colspan="6">&nbsp;</td>
                    </tr>                              
                    <tr class="terang">
                      <td colspan="3"><strong>Passing Grade</strong></td>
                      <td>&nbsp;</td>
                      <td><input name="reqPassingGrade" type="text" style="width:40px" onkeypress="return isNumberKey(event)"  maxlength="5" value="<?=$reqPassingGrade?>" class="form-rounded" /></td>
                      <td></td>
                    </tr>                              
                  </table> 
                </div>

                <div class="alert alert-info">REKAPITULASI</div>
                <div>
                	Keterangan : input box ( <input name="contoh" type="text" style="width:40px; background:#09F" maxlength="0" class="form-rounded" /> ) apabila dijumlah harus sama dengan <strong>100</strong>
                    <div class="col-md-8">
                    </div>
                </div>

                 
              </div>
            </div>
          </div>
          
          <div class="form-actions">
            <input type="hidden" name="reqId" value="<?=$reqId?>" />
            <input type="hidden" name="reqNama" value="<?=$reqNama?>" />
            <input type="hidden" name="submitSimpan" value="Simpan" />
            <a href="main/index/paket_detil/?reqId=<?=$reqId?>" class="btn btn-danger mr-1 text-white"> <i class="fa fa-arrow-left"></i> Kembali </a> 
            <button type="submit" class="btn btn-primary"><i class="fa fa-check-square-o"></i> Simpan</button>
          </div> 

        </div>
      </div>
      </form>

    </div>
  </div> 
</div>   