<?php
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 * 
 */

if($this->USER_TYPE_ID == "")
  redirect("main");
  
$this->load->model("PaketRekanan");
$this->load->model("RekananEvaluasiPengalaman");
$this->load->model("RekananPengalaman");
$this->load->model("PaketEvaluasiPengalaman");
$this->load->model("PaketKriteriaEvaluasi");
$this->load->model("PaketRekananKualifikasi");

include_once("functions/string.func.php");
include_once("functions/date.func.php");
include_once("functions/default.func.php");
include_once("functions/recordcoloring.func.php");

$paket_rekanan = new PaketRekanan();
$paket_evaluasi_pengalaman = new PaketEvaluasiPengalaman();
$paket_kriteria_evaluasi = new PaketKriteriaEvaluasi();

$reqId = $this->input->get("reqId");
$submitSimpan = $this->input->post("submitSimpan");
$reqPaketRekanan = $_POST["reqPaketRekanan"];
$reqNilaiFinal = $_POST["reqNilaiFinal"];
$reqRataFinal = $_POST["reqRataFinal"];
$reqProsentaseFinal = $_POST["reqProsentaseFinal"];
$reqKesesuaian = $_POST["reqKesesuaian"];
$reqKesesuaianNilai = $_POST["reqKesesuaianNilai"];
$reqKesesuaianTotal = $_POST["reqKesesuaianTotal"];
$reqRekananEvalPengalamanId = $_POST["reqRekananEvalPengalamanId"];

if($submitSimpan == "SimpanPengalaman")
{
	for($i=0;$i<count($reqRekananEvalPengalamanId);$i++)
	{
		$rekanan_evaluasi_kd_insert = new RekananEvaluasiPengalaman();
		$rekanan_evaluasi_kd_insert->setField("BP_KESESUAIAN", $reqKesesuaian[$i]);
		$rekanan_evaluasi_kd_insert->setField("BP_KESESUAIAN_NILAI", $reqKesesuaianNilai[$i]);		
		$rekanan_evaluasi_kd_insert->setField("BP_KESESUAIAN_TOTAL", $reqKesesuaianTotal[$i]);				
		$rekanan_evaluasi_kd_insert->setField("REKANAN_EVAL_PENGALAMAN_ID", $reqRekananEvalPengalamanId[$i]);			
		$rekanan_evaluasi_kd_insert->updatePenilaianPengalaman();					
		unset($rekanan_evaluasi_kd_insert);			
	}
	
	for($i=0;$i<count($reqNilaiFinal);$i++)
	{
		$rekanan_evaluasi_kd_insert = new RekananEvaluasiPengalaman();
		$rekanan_evaluasi_kd_insert->setField("FIELD", "NILAI");
		$rekanan_evaluasi_kd_insert->setField("FIELD_VALUE", $reqNilaiFinal[$i]);		
		$rekanan_evaluasi_kd_insert->setField("PAKET_REKANAN_ID", $reqPaketRekanan[$i]);			
		$rekanan_evaluasi_kd_insert->updateByField();					
		unset($rekanan_evaluasi_kd_insert);
		
		$rekanan_evaluasi_kd_insert = new RekananEvaluasiPengalaman();
		$rekanan_evaluasi_kd_insert->setField("FIELD", "NILAI_RATA");
		$rekanan_evaluasi_kd_insert->setField("FIELD_VALUE", round($reqRataFinal[$i]));		
		$rekanan_evaluasi_kd_insert->setField("PAKET_REKANAN_ID", $reqPaketRekanan[$i]);			
		$rekanan_evaluasi_kd_insert->updateByField();					
		unset($rekanan_evaluasi_kd_insert);
		
		
		$rekanan_evaluasi_kd_insert = new RekananEvaluasiPengalaman();
		$rekanan_evaluasi_kd_insert->setField("FIELD", "NILAI_PROSENTASE");
		$rekanan_evaluasi_kd_insert->setField("FIELD_VALUE", round($reqProsentaseFinal[$i]));		
		$rekanan_evaluasi_kd_insert->setField("PAKET_REKANAN_ID", $reqPaketRekanan[$i]);			
		$rekanan_evaluasi_kd_insert->updateByField();		
		unset($rekanan_evaluasi_kd_insert);
		
	}
}

$paket_rekanan->selectByParams(array("PAKET_ID" => $reqId), -1, -1, " AND A.TANGGAL_DAFTAR IS NOT NULL AND A.LULUS_PENDAFTARAN = 1 ");
while($paket_rekanan->nextRow())
{
  $arrRekanan[] = $paket_rekanan->getField("REKANAN");
  $arrPaketRekananId[] = $paket_rekanan->getField("PAKET_REKANAN_ID");
  $arrPaketRekananNilai[] = $paket_rekanan->getField("NILAI_PENAWARAN");
  $arrRekananId[] = $paket_rekanan->getField("REKANAN_ID");

  $rekanan_evaluasi_peng_insert = new RekananEvaluasiPengalaman();
  $rekanan_evaluasi_peng_insert->setField("PAKET_REKANAN_ID", $paket_rekanan->getField("PAKET_REKANAN_ID"));   
  $rekanan_evaluasi_peng_insert->deletePaketRekanan();
  
  // insert to rekanan_eval_pangalaman
  $rekanan_peng_get = new RekananPengalaman();
  $rekanan_peng_get->selectByParams(array("REKANAN_ID" => $paket_rekanan->getField("REKANAN_ID")), -1, -1, "");
  while($rekanan_peng_get->nextRow())
  {
    $rekanan_evaluasi_peng_insert->setField("REKANAN_PENGALAMAN_ID", $rekanan_peng_get->getField("REKANAN_PENGALAMAN_ID"));   
    $rekanan_evaluasi_peng_insert->insert();
  }
}

$paket_evaluasi_pengalaman->selectByParams(array("PAKET_ID" => $reqId));
$paket_evaluasi_pengalaman->firstRow();

$paket_kriteria_evaluasi->selectByParams(array("PAKET_ID" => $reqId));
$paket_kriteria_evaluasi->firstRow();
$check_bidang_kerja     = $paket_kriteria_evaluasi->getField("BIDANG_KERJA");
$check_nilai_kontrak    = $paket_kriteria_evaluasi->getField("NILAI_KONTRAK");
$check_status_penyedia  = $paket_kriteria_evaluasi->getField("STATUS_PENYEDIA");
$disp_bidang_kerja = " style='display:none'";
$disp_nilai_kontrak = " style='display:none'";
$disp_status_penyedia = " style='display:none'";

?>
<script type="text/javascript">
$(document).ready(function() {
	
	$(function(){
		$('#ff').form({
			url:'rekanan_evaluasi_pengalaman_json/set_evaluasi_kualifikasi_pengalaman2',
			onSubmit:function(){
				return $(this).form('validate');
			},
			success:function(data){
				$.messager.alert('Info', data, 'info');	
        document.location.href = "main/index/evaluasi_kualifikasi_pengalaman/?reqId=<?=$reqId?>"
			}
		});
		
	});
	
});
</script>


<style type="text/css">
  table th {
    text-align: center !important; 
    vertical-align: middle !important;
  }
</style>

<div class="row"> 
  <div class="col-md-12 col-sm-12">
    <div class="card">
      <div class="card-header card-head-inverse bg-primary">
        <h4 class="card-title text-white">Evaluasi Kualifikasi</h4>
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
              <a href="main/index/evaluasi_kualifikasi_administrasi/?reqId=<?=$reqId?>" class="btn btn-primary"> <span class="fa fa-pencil-square-o"></span> Evaluasi Administrasi</a>
              <a href="main/index/evaluasi_kualifikasi_skk/?reqId=<?=$reqId?>" class="btn btn-primary"><span class="fa fa-money"></span> Data Keuangan</a>
              <a href="main/index/evaluasi_kualifikasi_pengalaman/?reqId=<?=$reqId?>" class="btn btn-primary disabled"><span class="fa fa-cogs"></span> Data Teknis</a>
              <a href="main/index/evaluasi_kualifikasi_rekapitulasi/?reqId=<?=$reqId?>" class="btn btn-primary"><span class="fa fa-list-alt"></span> Rekapitulasi</a>
            </div> 
          </div>
          <div class="table-responsive">   
              
            <form id="ff" class="easyui-form form-horizontal" method="post" data-options="novalidate:true" enctype="multipart/form-data">
              <table class="table table-bordered table-hover"> 
                <thead>
                  <tr>
                    <th rowspan="2" style="width: 20px">No</th>
                    <th rowspan="2">Nama Perusahaan</th>
                    <th colspan="4">Nilai</th>
                    <th rowspan="2" style="width: 50px">Memenuhi <br>Syarat</th>
                    <th rowspan="2">Catatan <br>
                      <!-- <small>Di isi apabila tidak memenuhi syarat</small> -->
                    </th>
                  </tr>
                  <tr>
                    <th style="width: 30px">Pengalaman</th>
                    <th style="width: 30px">Personil</th>
                    <th style="width: 30px">Peralatan</th>
                    <th style="width: 30px">Sertifikat</th>
                  </tr>
                </thead> 
                <?php
                $no = 1;
                for($i=0;$i<count($arrRekanan);$i++)
                { 

                  $paket_rekanan_kualifikasi = new PaketRekananKualifikasi();
                  $paket_rekanan_kualifikasi->selectByParams(array("A.PAKET_REKANAN_ID" => $arrPaketRekananId[$i], "A.KODE" => "PENGALAMAN"));
                  $paket_rekanan_kualifikasi->firstRow();
                  $reqNilaiPengalaman = $paket_rekanan_kualifikasi->getField("NILAI");
                  $reqCatatan = $paket_rekanan_kualifikasi->getField("CATATAN");
                  $reqStatus = $paket_rekanan_kualifikasi->getField("STATUS");
                  
                  $paket_rekanan_kualifikasi2 = new PaketRekananKualifikasi();
                  $paket_rekanan_kualifikasi2->selectByParams(array("A.PAKET_REKANAN_ID" => $arrPaketRekananId[$i], "A.KODE" => "PERSONIL"));
                  $paket_rekanan_kualifikasi2->firstRow();
                  $reqNilaiPersonil = $paket_rekanan_kualifikasi2->getField("NILAI");

                  $paket_rekanan_kualifikasi3 = new PaketRekananKualifikasi();
                  $paket_rekanan_kualifikasi3->selectByParams(array("A.PAKET_REKANAN_ID" => $arrPaketRekananId[$i], "A.KODE" => "PERALATAN"));
                  $paket_rekanan_kualifikasi3->firstRow();
                  $reqNilaiPeralatan = $paket_rekanan_kualifikasi3->getField("NILAI");

                  $paket_rekanan_kualifikasi4 = new PaketRekananKualifikasi();
                  $paket_rekanan_kualifikasi4->selectByParams(array("A.PAKET_REKANAN_ID" => $arrPaketRekananId[$i], "A.KODE" => "SERTIFIKAT"));
                  $paket_rekanan_kualifikasi4->firstRow();
                  $reqNilaiSertifikat = $paket_rekanan_kualifikasi4->getField("NILAI");
                ?>
                  <tr class="terang">
                    <td valign="top" style="text-align: center"><?=$i+1?></td>
                    <td valign="top">
                      <a onclick="openAdd('main/loadUrl/main/data_rekanan/?reqId=<?=$arrRekananId[$i]?>')" style="text-decoration:none"><?=$arrRekanan[$i]?> <span class="fa fa-eye"></span></a>
                      <input type="hidden" name="reqPaketRekanan[]" value="<?=$arrPaketRekananId[$i]?>"/>
                    </td>
                    <td valign="top" style="text-align: center">
                      <input type="text" name="reqNilaiPengalaman[]" id="reqNilaiPengalaman<?=$i?>" value="<?=$reqNilaiPengalaman ?>" style="width:65px" maxlength="3" class="form-control" />
                    </td>
                    <td valign="top" style="text-align: center">
                      <input type="text" name="reqNilaiPersonil[]" id="reqNilaiPersonil<?=$i?>" value="<?=$reqNilaiPersonil ?>" style="width:65px" maxlength="3" class="form-control" />
                    </td>
                    <td valign="top" style="text-align: center">
                      <input type="text" name="reqNilaiPeralatan[]" id="reqNilaiPeralatan<?=$i?>" value="<?=$reqNilaiPeralatan ?>" style="width:65px" maxlength="3" class="form-control" />
                    </td>
                    <td valign="top" style="text-align: center">
                      <input type="text" name="reqNilaiSertifikat[]" id="reqNilaiSertifikat<?=$i?>" value="<?=$reqNilaiSertifikat ?>" style="width:65px" maxlength="3" class="form-control" />
                    </td>
                    <td style="text-align: left">
                      <?php 
                      if ($reqStatus == 1) { ?>
                      <input type="radio" style="cursor:pointer" name="reqStatus<?=$arrPaketRekananId[$i]?>"  id="reqStatus" value="1" checked="checked"/> Ya<br>
                      <input type="radio" style="cursor:pointer" name="reqStatus<?=$arrPaketRekananId[$i]?>"  id="reqStatus" value="0"/> Tidak<br>
                      <?php 
                      } else { ?>
                      <input type="radio" style="cursor:pointer" name="reqStatus<?=$arrPaketRekananId[$i]?>"  id="reqStatus" value="1"/> Ya<br>
                      <input type="radio" style="cursor:pointer" name="reqStatus<?=$arrPaketRekananId[$i]?>"  id="reqStatus" value="0" checked="checked"/> Tidak
                      <?php 
                      } ?>
                    </td>
                    <td valign="top"> 
                      <div class="area-catatan-panitia" style="margin:10px">
                          <div class="isi">
                          <textarea rows="3" name="reqCatatan[]" class="form-control"><?=$reqCatatan?></textarea>
                          </div>
                      </div>            
                    </td>
                  </tr>
                <?php
                }
                ?>  
              </table>
                
              <div class="form-group">
                <input type="hidden" name="submitSimpan" value="SimpanPengalaman" />
                <input type="hidden" name="reqId" value="<?=$reqId?>" />
                <a href="main/index/paket_detil/?reqId=<?=$reqId?>" class="btn btn-danger"><i class="fa fa-arrow-left"></i> Kembali</a>
                <button type="submit" class="btn btn-primary"><i class="fa fa-check-square-o"></i> Simpan</button>
                <!-- <a class="btn btn-info" href="main/loadUrl/report/evaluasi_kualifikasi_pengalaman_excel/?reqId=<?=$reqId?>" target="_blank" ><span class="fa fa-print"></span> Cetak</a> -->
              </div>
            </form> 

          </div>
        </div>
      </div>
    </div>
  </div> 
</div>     