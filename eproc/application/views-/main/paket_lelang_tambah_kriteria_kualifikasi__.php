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
			url:'paket_json/kriteria_kualifikasi_new',
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
              <div class="alert bg-primary">
                <strong>I. EVALUASI DMINISTRASI</strong>  
              </div> 
              <table class="table table-bordered mb-0"> 
                <thead>
                  <tr class="judul-kolom">
                    <th>Kriteria</th>
                    <th style="text-align: center">
                      Pilih<br>
                      <!-- <input type="checkbox" name="reqPilihAll" id="reqPilihAll" onChange="cek_semua(document.frmInformasiAdd.reqPilih)"/> -->
                    <!-- <label for="reqPilihAll"></label> -->
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
                
                <div class="alert bg-primary">
                    <strong>II. EVALUASI KEUANGAN</strong>  
                </div> 
                  <ul>
                    <li>SKK</li>
                    <li>Rekening Koran</li>
                  </ul>
              </div>
            </div>
          </div>

          <div class="card mb-1 border-blue border-darken-1">
            <div class="card-content">
              <div class="p-1"> 
                <div class="alert bg-primary">
                    <strong>III. EVALUASI TEKNIS</strong>  
                </div>  
                 <ul>
                    <li>Pengalaman Perusahaan</li>
                    <li>Personil</li>
                    <li>Peralatan</li>
                    <li>Sertifikat</li>
                  </ul>
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