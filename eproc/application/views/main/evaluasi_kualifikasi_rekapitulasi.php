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
$this->load->model("PaketKriteriaEvaluasi");
$this->load->model("PaketEvaluasiAdmin");
$this->load->model("PaketEvaluasiPersonil");
$this->load->model("PaketEvaluasiSertifikatLain");
$this->load->model("PaketEvaluasiPengalaman");
$this->load->model("PaketEvaluasiPeralatan");
$this->load->model("PaketRekanan");
$this->load->model("RekananEvaluasiAdmin");
$this->load->model("RekananEvaluasiKeuangan");
$this->load->model("RekananEvaluasiPengalaman");
$this->load->model("RekananEvaluasiPersonil");
$this->load->model("RekananEvaluasiPeralatan");
$this->load->model("RekananEvaluasiSertifikatLain");
$this->load->model("PaketRekananKualifikasi");

include_once("functions/string.func.php");
include_once("functions/date.func.php");
include_once("functions/default.func.php");
include_once("functions/recordcoloring.func.php");


$paket_kriteria_evaluasi = new PaketKriteriaEvaluasi();
$paket_rekanan = new PaketRekanan();
$paket_rekanan_height = new PaketRekanan();

$reqId = $this->input->get("reqId");
$paketInfo->getPaket($reqId);
$paket_rekanan->selectByParams(array("PAKET_ID" => $reqId), -1, -1, " AND A.TANGGAL_DAFTAR IS NOT NULL AND A.LULUS_PENDAFTARAN = 1 ");
while($paket_rekanan->nextRow())
{
	$arrRekanan[] = $paket_rekanan->getField("REKANAN");
	$arrPaketRekananId[] = $paket_rekanan->getField("PAKET_REKANAN_ID");
	$arrPaketRekananNilai[] = $paket_rekanan->getField("NILAI_PENAWARAN");
  $arrPaketRekananLulusAdmin[] = $paket_rekanan->getField("LULUS_ADMINISTRASI");
	$arrPaketRekananLulusKualifikasi[] = $paket_rekanan->getField("LULUS_KUALIFIKASI");
	$arrPaketRekananKeteranganLulus[] = $paket_rekanan->getField("LULUS_KUALIFIKASI_KETERANGAN");
}

$paket_kriteria_evaluasi->selectByParams(array("PAKET_ID" => $reqId));
$paket_kriteria_evaluasi->firstRow();

?>
    <script type="text/javascript">	
	$(function(){
		$('#ff').form({
			url:'paket_rekanan_json/set_evaluasi_kualifikasi_rekapitulasi',
			onSubmit:function(){
				return $(this).form('validate');
			},
			success:function(data){
				$.messager.alert('Info', data, 'info');	
			}
		});
		
	});
	
	function publishKualifikasi()
	{
		$.messager.confirm('Konfirmasi',"Publish hasil evaluasi kualifikasi ?",function(r){
			if (r){
				$.get( "paket_json/publish_evaluasi_kualifikasi/?reqId=<?=$reqId?>", function( data ) {
				  $.messager.alert('Informasi',data, 'info');
				  $("#btnPublish").css("display", "none");
				});
			}
		});			
		
	}
</script>  

<style type="text/css">
  table th {
    text-align: center !important; 
    vertical-align: middle !important;
    font-size: 12px;
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
              <a href="main/index/evaluasi_kualifikasi_administrasi/?reqId=<?=$reqId?>" class="btn btn-primary "> <span class="fa fa-pencil-square-o"></span> Evaluasi Administrasi</a>
              <a href="main/index/evaluasi_kualifikasi_skk/?reqId=<?=$reqId?>" class="btn btn-primary"><span class="fa fa-money"></span> Data Keuangan</a>
              <a href="main/index/evaluasi_kualifikasi_pengalaman/?reqId=<?=$reqId?>" class="btn btn-primary"><span class="fa fa-cogs"></span> Data Teknis</a>
              <a href="main/index/evaluasi_kualifikasi_rekapitulasi/?reqId=<?=$reqId?>" class="btn btn-primary disabled"><span class="fa fa-list-alt"></span> Rekapitulasi</a>
            </div> 
          </div>
          <div class="table-responsive">  
            <form id="ff" class="easyui-form form-horizontal" method="post" data-options="novalidate:true" enctype="multipart/form-data">
              <table class="table table-bordered table-hover"> 
                <thead>
                  <tr>
                    <th rowspan="2" style="width: 20px">No</th>
                    <th rowspan="2">Nama Perusahaan</th>
                    <th colspan="7">Nilai</th>
                    <th rowspan="2"> Nilai </th>
                    <th rowspan="2">Keterangan </th>
                  </tr>
                  <tr>
                    <th style="width: 30px;">Administrasi</th>
                    <th style="width: 30px;">SKK</th>
                    <th style="width: 30px;">Rekening <br>Koran</th>
                    <th style="width: 30px;">Pengalaman</th>
                    <th style="width: 30px;">Personil</th>
                    <th style="width: 30px;">Peralatan</th>
                    <th style="width: 30px;">Sertifikat</th>
                  </tr>
                </thead> 
                <?php
                  for($i=0;$i<count($arrRekanan);$i++)
                  { 
                    $paket_rekanan_kualifikasi1 = new PaketRekananKualifikasi();
                    $paket_rekanan_kualifikasi1->selectByParams(array("A.PAKET_REKANAN_ID" => $arrPaketRekananId[$i], "A.KODE" => "EVALUASI_ADMIN"));
                    $paket_rekanan_kualifikasi1->firstRow();
                    $reqNilaiAdm    = $paket_rekanan_kualifikasi1->getField("NILAI");
                    $reqCatatanAdm  = $paket_rekanan_kualifikasi1->getField("CATATAN");
                    $reqStatusAdm   = $paket_rekanan_kualifikasi1->getField("STATUS");

                    $paket_rekanan_kualifikasi2 = new PaketRekananKualifikasi();
                    $paket_rekanan_kualifikasi2->selectByParams(array("A.PAKET_REKANAN_ID" => $arrPaketRekananId[$i], "A.KODE" => "SKK"));
                    $paket_rekanan_kualifikasi2->firstRow();
                    $reqNilaiSKK = $paket_rekanan_kualifikasi2->getField("NILAI");
                    $reqCatatanKeu = $paket_rekanan_kualifikasi2->getField("CATATAN");
                    $reqStatusKeu = $paket_rekanan_kualifikasi2->getField("STATUS");
                    
                    $paket_rekanan_kualifikasi3 = new PaketRekananKualifikasi();
                    $paket_rekanan_kualifikasi3->selectByParams(array("A.PAKET_REKANAN_ID" => $arrPaketRekananId[$i], "A.KODE" => "REKENING_KORAN"));
                    $paket_rekanan_kualifikasi3->firstRow();
                    $reqNilaiRekeningKoran = $paket_rekanan_kualifikasi3->getField("NILAI");

                    $paket_rekanan_kualifikasi4 = new PaketRekananKualifikasi();
                    $paket_rekanan_kualifikasi4->selectByParams(array("A.PAKET_REKANAN_ID" => $arrPaketRekananId[$i], "A.KODE" => "PENGALAMAN"));
                    $paket_rekanan_kualifikasi4->firstRow();
                    $reqNilaiPengalaman = $paket_rekanan_kualifikasi4->getField("NILAI");
                    $reqCatatanTek = $paket_rekanan_kualifikasi4->getField("CATATAN");
                    $reqStatusTek = $paket_rekanan_kualifikasi4->getField("STATUS");

                    $paket_rekanan_kualifikasi5 = new PaketRekananKualifikasi();
                    $paket_rekanan_kualifikasi5->selectByParams(array("A.PAKET_REKANAN_ID" => $arrPaketRekananId[$i], "A.KODE" => "PERSONIL"));
                    $paket_rekanan_kualifikasi5->firstRow();
                    $reqNilaiPersonil = $paket_rekanan_kualifikasi5->getField("NILAI");

                    $paket_rekanan_kualifikasi6 = new PaketRekananKualifikasi();
                    $paket_rekanan_kualifikasi6->selectByParams(array("A.PAKET_REKANAN_ID" => $arrPaketRekananId[$i], "A.KODE" => "PERALATAN"));
                    $paket_rekanan_kualifikasi6->firstRow();
                    $reqNilaiPeralatan = $paket_rekanan_kualifikasi6->getField("NILAI");

                    $paket_rekanan_kualifikasi7 = new PaketRekananKualifikasi();
                    $paket_rekanan_kualifikasi7->selectByParams(array("A.PAKET_REKANAN_ID" => $arrPaketRekananId[$i], "A.KODE" => "SERTIFIKAT"));
                    $paket_rekanan_kualifikasi7->firstRow();
                    $reqNilaiSertifikat = $paket_rekanan_kualifikasi7->getField("NILAI");

                    if ($reqStatusAdm == '1') { 
                      $reqNilaiAdm = $reqNilaiAdm; 
                    } else {
                      $reqNilaiAdm = 0;
                    } 
                    if ($reqStatusKeu == '1') { 
                      $reqNilaiSKK = $reqNilaiSKK; 
                    } else {
                      $reqNilaiSKK = 0;
                    }  
                    if ($reqStatusKeu == '1') { 
                      $reqNilaiRekeningKoran = $reqNilaiRekeningKoran; 
                    } else {
                      $reqNilaiRekeningKoran = 0;
                    }  
                    if ($reqStatusTek == '1') { 
                      $reqNilaiPengalaman = $reqNilaiPengalaman; 
                    } else {
                      $reqNilaiPengalaman = 0;
                    }  
                    if ($reqStatusTek == '1') { 
                      $reqNilaiPersonil = $reqNilaiPersonil; 
                    } else {
                      $reqNilaiPersonil = 0;
                    }  
                    if ($reqStatusTek == '1') { 
                      $reqNilaiPeralatan = $reqNilaiPeralatan; 
                    } else {
                      $reqNilaiPeralatan = 0;
                    }  
                    if ($reqStatusTek == '1') { 
                      $reqNilaiSertifikat = $reqNilaiSertifikat; 
                    } else {
                      $reqNilaiSertifikat = 0;
                    }  

                    $totalNilai = round(($reqNilaiAdm + $reqNilaiSKK + $reqNilaiRekeningKoran + $reqNilaiPengalaman + $reqNilaiPersonil + $reqNilaiPeralatan + $reqNilaiSertifikat) /7);
                  ?>
                <tr class="terang">
                  <td valign="top" style="text-align: center"><?=$i+1?></td>
                  <td valign="top">
                    <a onclick="openAdd('main/loadUrl/main/data_rekanan/?reqId=<?=$arrRekananId[$i]?>')" style="text-decoration:none"><?=$arrRekanan[$i]?> <span class="fa fa-eye"></span></a>
                    <input type="hidden" name="reqPaketRekanan[]" value="<?=$arrPaketRekananId[$i]?>"/>
                  </td>
                  <td valign="top" style="text-align: center">
                    <?php 
                    if ($reqStatusAdm == '1') { 
                      echo $reqNilaiAdm;
                    } else {
                      echo '<i class="fa fa-times" aria-hidden="true"></i><br><small>'.$reqCatatanAdm.'</small>';
                    } ?>
                  </td>
                  <td valign="top" style="text-align: center">
                    <?php 
                    if ($reqStatusKeu == '1') { 
                      echo $reqNilaiSKK;
                    } else {
                      echo '<i class="fa fa-times" aria-hidden="true"></i><br><small>'.$reqCatatanKeu.'</small>';
                    } ?>
                  </td>
                  <td valign="top" style="text-align: center">
                    <?php 
                    if ($reqStatusKeu == '1') { 
                      echo $reqNilaiRekeningKoran;
                    } else {
                      echo '<i class="fa fa-times" aria-hidden="true"></i><br><small>'.$reqCatatanKeu.'</small>';
                    } ?>
                  </td>
                  <td valign="top" style="text-align: center">
                    <?php 
                    if ($reqStatusTek == '1') { 
                      echo $reqNilaiPengalaman;
                    } else {
                      echo '<i class="fa fa-times" aria-hidden="true"></i><br><small>'.$reqCatatanTek.'</small>';
                    } ?>
                  </td>
                  <td valign="top" style="text-align: center">
                    <?php 
                    if ($reqStatusTek == '1') { 
                      echo $reqNilaiPersonil;
                    } else {
                      echo '<i class="fa fa-times" aria-hidden="true"></i><br><small>'.$reqCatatanTek.'</small>';
                    } ?>
                  </td>
                  <td valign="top" style="text-align: center">
                    <?php 
                    if ($reqStatusTek == '1') { 
                      echo $reqNilaiPeralatan;
                    } else {
                      echo '<i class="fa fa-times" aria-hidden="true"></i><br><small>'.$reqCatatanTek.'</small>';
                    } ?>
                  </td> 
                  <td valign="top" style="text-align: center">
                    <?php 
                    if ($reqStatusTek == '1') { 
                      echo $reqNilaiSertifikat;
                    } else {
                      echo '<i class="fa fa-times" aria-hidden="true"></i><br><small>'.$reqCatatanTek.'</small>';
                    } ?>
                  </td>
                  <td style="text-align: left">
                    <?php 
                    echo $totalNilai ?>
                  </td> 
                  <td valign="top"> 
                    <?php 
                    if ($arrPaketRekananLulusKualifikasi[$i] == 1) { ?>
                    <input type="radio" style="cursor:pointer" name="reqStatus<?=$arrPaketRekananId[$i]?>"  id="reqStatus" value="1" checked="checked"/> Lulus<br>
                    <input type="radio" style="cursor:pointer" name="reqStatus<?=$arrPaketRekananId[$i]?>"  id="reqStatus" value="0"/> Tidak<br>
                    <?php 
                    } else { ?>
                    <input type="radio" style="cursor:pointer" name="reqStatus<?=$arrPaketRekananId[$i]?>"  id="reqStatus" value="1"/> Lulus<br>
                    <input type="radio" style="cursor:pointer" name="reqStatus<?=$arrPaketRekananId[$i]?>"  id="reqStatus" value="0" checked="checked"/> Tidak
                    <?php 
                    } ?>
                    <div class="area-catatan-panitia" style="margin:10px">
                        <div class="isi">
                        <textarea rows="3" name="reqCatatan[]" class="form-control"><?=$arrPaketRekananKeteranganLulus[$i]?></textarea>
                        </div>
                    </div>            
                  </td>
                  <input type="hidden" name="reqPaketRekananId[]" id="reqPaketRekananId" value="<?=$arrPaketRekananId[$i]?>" />
                  <!-- <input type="hidden" name="reqLulus[]" id="reqLulus" value="<?=$lulus?>" /> -->
                  <!-- <input type="text" name="reqKeterangan[]" id="reqKeterangan" value="<?php // $arrPaketRekananKeteranganLulus[$i]?>" /> -->
                </tr>
                  <?php
                    unset($paket_rekanan_kualifikasi);
                  }
                  ?>
              </table> 
                  
              <div>
                <a href="main/index/paket_detil/?reqId=<?=$reqId?>" class="btn btn-danger"><i class="fa fa-arrow-left"></i> Kembali</a>
                <input type="hidden" name="submitSimpan" value="Simpan" />
                <button type="submit" class="btn btn-primary"><i class="fa fa-check-square-o"></i> Simpan</button>
                <a class="btn btn-info" href="main/loadUrl/report/evaluasi_kualifikasi_rekapitulasi_excel/?reqId=<?=$reqId?>" target="_blank" ><i class="fa fa-print"></i> Cetak</a>
               <?php
                if($paketInfo->publish_ba_kualifikasi == "1")
                {}
                else
                {
                ?>                    
                  <a onClick="publishKualifikasi();" id="btnPublish" class="btn btn-success text-white"><i class="fa fa-send"></i> Publish</a>                       
                <?php
                }
                ?>                                                       
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div> 
</div>    