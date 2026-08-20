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
$this->load->model("RekananEvaluasiPersonil");
$this->load->model("PaketEvaluasiPersonil");
$this->load->model("PaketRekananKualifikasi");
include_once("functions/string.func.php");
include_once("functions/date.func.php");
include_once("functions/default.func.php");

$paket_rekanan = new PaketRekanan();
$paket_evaluasi_personil = new PaketEvaluasiPersonil();

$reqId = $this->input->get("reqId");

$paket_rekanan->selectByParams(array("PAKET_ID" => $reqId), -1, -1, " AND A.TANGGAL_DAFTAR IS NOT NULL AND A.LULUS_PENDAFTARAN = 1 ");
while($paket_rekanan->nextRow())
{
	$arrRekanan[] = $paket_rekanan->getField("REKANAN");
	$arrRekananId[] = $paket_rekanan->getField("REKANAN_ID");
	$arrPaketRekananId[] = $paket_rekanan->getField("PAKET_REKANAN_ID");
	$arrPaketRekananNilai[] = $paket_rekanan->getField("NILAI_PENAWARAN");
}
//echo $paket_rekanan->query;
$paket_evaluasi_personil->selectByParams(array("PAKET_ID" => $reqId));
while($paket_evaluasi_personil->nextRow())
{
	$arrEvalPersonilId[] = $paket_evaluasi_personil->getField("PAKET_EVAL_PERSONIL_ID");
	$arrJabatan[] = $paket_evaluasi_personil->getField("JABATAN");
	$arrPendidikan[] = $paket_evaluasi_personil->getField("PENDIDIKAN_NAMA");	
	$arrPengalaman[] = $paket_evaluasi_personil->getField("PENGALAMAN");	
	$arrJumlah[] = $paket_evaluasi_personil->getField("JUMLAH");	
	$arrNilaiPerEvaluasi[] = $paket_evaluasi_personil->getField("NILAI");	
	$arrNilai[] = $paket_evaluasi_personil->getField("NILAI_MINIMUM");		
}

//echo $paket_evaluasi_personil->query;

?>
<script type="text/javascript">
$(document).ready(function() {
	
	$(function(){
		$('#ff').form({
			url:'rekanan_evaluasi_personil_json/set_evaluasi_kualifikasi_personil',
			onSubmit:function(){
				return $(this).form('validate');
			},
			success:function(data){
				$.messager.alert('Info', data, 'info');	
			}
		});
		
	});
	
});
</script>

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
          <hr>
          <div class="table-responsive">  
            
            <div class="tab-navigasi-sub" style="margin-bottom: 2%">
              <a class="btn btn-success btn-sm " href="main/index/evaluasi_kualifikasi_pengalaman/?reqId=<?=$reqId?>">Penilaian Pengalaman Perusahaan</a>
              <a class="btn btn-success btn-sm disabled" href="main/index/evaluasi_kualifikasi_personil/?reqId=<?=$reqId?>">Penilaian Personil</a>
              <a class="btn btn-success btn-sm" href="main/index/evaluasi_kualifikasi_peralatan/?reqId=<?=$reqId?>">Penilaian Peralatan</a>
              <a class="btn btn-success btn-sm" href="main/index/evaluasi_kualifikasi_sertifikat/?reqId=<?=$reqId?>">Penilaian Sertifikat</a>
            </div>
              
            <form id="ff" class="easyui-form form-horizontal" method="post" data-options="novalidate:true" enctype="multipart/form-data">
                <table class="table table-bordered tabl-hover">
                  <tr class="">
                        <input type="hidden" id="reqNilai" value="<?=$arrNilai[0]?>" style="width:30px" />
                        <input type="hidden" id="reqJumlahKategori" value="<?=count($arrEvalPersonilId)?>" style="width:30px" />
                  </tr>
                    <tr class="judul-kolom">
                    <th>No</th>
                    <th style="width:80%">Nama Perusahaan</th>
                    <th>Perhitungan</th>    
                    <th>Nilai </th>
                    </tr>
                    <?php
                    for($i=0;$i<count($arrRekanan);$i++)
                    {
                        $arrNilaiFinalPerEvaluasi = array();    
                    ?>
                      <tr class="terang">
                            <td valign="top"><?=$i+1?></td>     
                            <td valign="top">
                            <a onclick="openAdd('main/loadUrl/main/data_rekanan/?reqId=<?=$arrRekananId[$i]?>')" style="text-decoration:none"><?=$arrRekanan[$i]?> <span class="fa fa-eye"></span></a> <br />
                            <table>
                            <?php
                            $prosentase_nilai = 0;
                            for($j=0; $j<count($arrEvalPersonilId);$j++)
                            {                           
                                $nilai_final = 0;
                            ?>
                                <tr>
                                <td colspan="2">
                                    <?=$j+1?>. <?=$arrJabatan[$j]?>, <?=$arrPendidikan[$j]?> / <?=$arrPengalaman[$j]?> th (Jumlah : <?=$arrJumlah[$j]?> orang) - <strong>Prosentase : <?=$arrNilaiPerEvaluasi[$j]?> %</strong>
                                    <input type="hidden" id="reqProsentase<?=$j?>-<?=$i?>" value="<?=$arrNilaiPerEvaluasi[$j]?>" />                  
                                    <input type="hidden" id="reqKebutuhan<?=$j?>-<?=$i?>" value="<?=$arrJumlah[$j]?>" />                  
                                </td>
                                <td>
                                    <?php
                                    $rekanan_evaluasi_personil = new RekananEvaluasiPersonil();
                                    $rekanan_evaluasi_personil->selectByParams(array("PAKET_EVAL_PERSONIL_ID" => $arrEvalPersonilId[$j], "PAKET_REKANAN_ID" => $arrPaketRekananId[$i]));
                                    $rekanan_evaluasi_personil->firstRow();
                                    $prosentase_nilai += $rekanan_evaluasi_personil->getField("KESESUAIAN_TOTAL");
                                    ?>
                                    <input type="text" name="reqKebutuhanPemenuhanNilai[]" id="reqKebutuhanPemenuhanNilai<?=$j?>-<?=$i?>" readonly value="<?=$rekanan_evaluasi_personil->getField("KESESUAIAN_TOTAL")?>" style="width:40px;" />
                                    <input type="hidden" name="reqPaketEvaluasiPersonilId[]" id="reqPaketEvaluasiPersonilId<?=$j?>-<?=$i?>" value="<?=$arrEvalPersonilId[$j]?>" style="width:50px;" />
                                    <input type="hidden" name="reqPaketRekananPersonilId[]" id="reqPaketRekananPersonilId<?=$j?>-<?=$i?>" value="<?=$arrPaketRekananId[$i]?>" style="width:50px;" />
                                    <?php
                                    unset($rekanan_evaluasi_personil);
                                    ?>
                                 </td>                
                                </tr>
                            <?php
                                $k = 0;
                                $total_pegawai = 0;
                                $rekanan_evaluasi_personil = new RekananEvaluasiPersonil();
                                $rekanan_evaluasi_personil->selectByParams(array("PAKET_EVAL_PERSONIL_ID" => $arrEvalPersonilId[$j], "PAKET_REKANAN_ID" => $arrPaketRekananId[$i]));
                                while($rekanan_evaluasi_personil->nextRow())
                                {
                                    //echo $rekanan_evaluasi_personil->query;
                                ?>  
                                    <tr>
                                        <td>
                                            &raquo; 
                                            <a onclick="openAdd('main/loadUrl/main/paket_evaluasi_lihat_personil/?reqId=<?=$rekanan_evaluasi_personil->getField("REKANAN_TENAGA_AHLI_ID")?>')" style="text-decoration:none"><?=$rekanan_evaluasi_personil->getField("TENAGA_AHLI")?></a>
                                        </td>
                                        <td>
                                            <select name="reqKesesuaian[]" id="reqKesesuaian<?=$k?>-<?=$j?>-<?=$i?>" onchange="hitungPersonil('<?=$k?>', '<?=$j?>', '<?=$i?>')">
                                            <option value=""></option>
                                            <option value="S" <?php if($rekanan_evaluasi_personil->getField("KESESUAIAN") == "S") {?> selected="selected" <?php } ?>>S</option>
                                            <option value="R" <?php if($rekanan_evaluasi_personil->getField("KESESUAIAN") == "R") {?> selected="selected" <?php } ?>>R</option>
                                            <option value="TS" <?php if($rekanan_evaluasi_personil->getField("KESESUAIAN") == "TS") {?> selected="selected" <?php } ?>>TS</option>
                                            </select>     
                                            <input type="text" name="reqKesesuaianNilai[]" id="reqKesesuaianNilai<?=$k?>-<?=$j?>-<?=$i?>" value="<?=$rekanan_evaluasi_personil->getField("KESESUAIAN_NILAI")?>" style="width:40px" onkeyup="hitungPersonil('<?=$k?>', '<?=$j?>', '<?=$i?>');" />                        
                                            <input type="hidden" name="reqRekananEvaluasiPersonilId[]" value="<?=$rekanan_evaluasi_personil->getField("REKANAN_EVAL_PERSONIL_ID")?>" />
                                        </td>
                                        <td></td>
                                    </tr>
                                <?php      
                                    $nilai_final = $rekanan_evaluasi_personil->getField("NILAI");
                                    $total_pegawai += 1;
                                    $k++;
                                }
                                unset($rekanan_evaluasi_personil);
                                ?>
                                <tr>
                                    <td colspan="3"><input type="hidden" id="reqPemenuhan<?=$j?>-<?=$i?>" value="<?=$total_pegawai?>" /></td>
                                </tr>
                                <?php  
                            }
                            ?>
                            </table>                
                            </td>
                            <td valign="top"><?=$arrNilai[0]?> * <label style="font-size:13px;" id="reqNilaiProsentase<?=$i?>"><?=$prosentase_nilai?></label>%</td>     
                            <td valign="top" align="center">
                                <input type="hidden" name="reqPaketRekanan[]" value="<?=$arrPaketRekananId[$i]?>" />                
                                <input type="text" name="reqNilaiFinal[]" id="reqNilaiFinal<?=$i?>" value="<?=$nilai_final?>" style="width:40px" <?=$readonly?> />
                                        <div class="area-catatan-panitia" style="margin:10px">
                                            <div class="judul">
                                            Catatan Untuk Peserta (apabila tidak memenuhi syarat) :
                                            </div>
                                            <div class="isi">
                                            <?php
                                              $paket_rekanan_kualifikasi = new PaketRekananKualifikasi();
                                              $paket_rekanan_kualifikasi->selectByParams(array("A.PAKET_REKANAN_ID" => $arrPaketRekananId[$i], "A.KODE" => "PERSONIL"));
                                              $paket_rekanan_kualifikasi->firstRow();
                                              $reqCatatan = $paket_rekanan_kualifikasi->getField("CATATAN");
                                              unset($paket_rekanan_kualifikasi);
                                            ?>
                                            <textarea class="form-control" style="width: 150px; height:100px;" name="reqCatatan[]"><?=$reqCatatan?></textarea>
                                            </div>
                                        </div>                  
                            </td>
                            <!-- <td valign="top"></td>         -->
                      </tr>
                    <?php
                        unset($arrNilaiFinalPerEvaluasi);
                    }
                    ?>    
                </table>

                <div>
                    <a href="main/index/paket_detil/?reqId=<?=$reqId?>" class="btn btn-danger"><i class="fa fa-arrow-left"></i> Kembali</a>
                    <input type="hidden" name="submitSimpan" value="SimpanPersonil" />
                    <button type="submit" class="btn btn-primary"><i class="fa fa-check-square-o"></i> Simpan</button>
                    <a class="btn btn-info" href="main/loadUrl/report/evaluasi_kualifikasi_personil_excel/?reqId=<?=$reqId?>" target="_blank" ><span class="fa fa-print"></span> Cetak</a>
                </div>
            </form>

          </div>
        </div>
      </div>
    </div>
  </div> 
</div>   