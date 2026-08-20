<?php
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 * 
 */

if($this->USER_TYPE_ID == "")
  redirect("main");

include_once("functions/string.func.php");
include_once("functions/date.func.php");
include_once("functions/default.func.php");
$this->load->model("PaketRekanan");
$this->load->model("RekananEvaluasiSertifikatLain");
$this->load->model("PaketEvaluasiSertifikatLain");
$this->load->model("PaketRekananKualifikasi");

$paket_rekanan = new PaketRekanan();
$paket_evaluasi_sertifikat = new PaketEvaluasiSertifikatLain();

$reqId = $this->input->get("reqId");

$paket_rekanan->selectByParams(array("PAKET_ID" => $reqId), -1, -1, " AND A.TANGGAL_DAFTAR IS NOT NULL AND A.LULUS_PENDAFTARAN = 1 ");
while($paket_rekanan->nextRow())
{
	$arrRekanan[] = $paket_rekanan->getField("REKANAN");
	$arrPaketRekananId[] = $paket_rekanan->getField("PAKET_REKANAN_ID");
}

$paket_evaluasi_sertifikat->selectByParams(array("PAKET_ID" => $reqId));
while($paket_evaluasi_sertifikat->nextRow())
{
	$arrEvalSertifikatId[] = $paket_evaluasi_sertifikat->getField("PAKET_EVAL_SERTIFIKAT_LAIN_ID");
	$arrNama[] = $paket_evaluasi_sertifikat->getField("NAMA");
	$arrKeterangan[] = $paket_evaluasi_sertifikat->getField("KETERANGAN");	
	$arrNilaiPerEvaluasi[] = $paket_evaluasi_sertifikat->getField("NILAI");	
	$arrNilai[] = $paket_evaluasi_sertifikat->getField("NILAI_MINIMUM");		
	
}

?>

<script type="text/javascript">
$(document).ready(function() {
	
	$(function(){
		$('#ff').form({
			url:'rekanan_evaluasi_sertifikat_lain_json/set_evaluasi_kualifikasi_sertifikat',
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
              <a class="btn btn-success btn-sm " href="main/index/evaluasi_kualifikasi_personil/?reqId=<?=$reqId?>">Penilaian Personil</a>
              <a class="btn btn-success btn-sm " href="main/index/evaluasi_kualifikasi_peralatan/?reqId=<?=$reqId?>">Penilaian Peralatan</a>
              <a class="btn btn-success btn-sm disabled" href="main/index/evaluasi_kualifikasi_sertifikat/?reqId=<?=$reqId?>">Penilaian Sertifikat</a>
            </div> 
            
            <form id="ff" class="easyui-form form-horizontal" method="post" data-options="novalidate:true" enctype="multipart/form-data">
            
            <table class="table table-bordered">
              <tr class="">
                <!-- <td colspan="3"> -->
                <!-- </td> -->
                <input type="hidden" id="reqJumlahKategori" value="<?=count($arrEvalSertifikatId)?>" style="width:30px" />
                <input type="hidden" id="reqNilai" value="<?=$arrNilai[0]?>" style="width:30px" />
              </tr>
                <tr class="judul-kolom">
                <th>No</th>
                <th style="width:50%">Nama Perusahaan</th>
                <th>Perhitungan</th>
                <th>Nilai </th>
                <th></th>
                </tr>
                <?php
                for($i=0;$i<count($arrRekanan);$i++)
                {
                    $nilai_final = 0;
                    $arrNilaiFinalPerEvaluasi = array();    
                ?>
                  <tr class="terang">
                        <td valign="top"><?=$i+1?></td>     
                        <td valign="top">
                        <a onclick="openAdd('main/loadUrl/main/data_rekanan/?reqId=<?=$arrRekananId[$i]?>')" style="text-decoration:none"><?=$arrRekanan[$i]?></a> <br />
                        <table class="table table-striped">
                        <?php
                        $prosentase_nilai = 0;
                        for($j=0; $j<count($arrEvalSertifikatId);$j++)
                        {
                        ?>
                            <tr>
                            <td colspan="3"><?=$j+1?>. <?=$arrNama[$j]?>, <?=$arrKeterangan[$j]?> - <strong>Prosentase : <?=$arrNilaiPerEvaluasi[$j]?>%</strong> 
                            <input type="hidden" id="reqProsentase<?=$j?>-<?=$i?>" value="<?=$arrNilaiPerEvaluasi[$j]?>" />                     
                            </td>
                            <td>
                                <?php
                                $rekanan_evaluasi_sertifikat = new RekananEvaluasiSertifikatLain();
                                $rekanan_evaluasi_sertifikat->selectByParams(array("PAKET_EVAL_SERTIFIKAT_LAIN_ID" => $arrEvalSertifikatId[$j], "PAKET_REKANAN_ID" => $arrPaketRekananId[$i]));
                                $rekanan_evaluasi_sertifikat->firstRow();
                                $prosentase_nilai += $rekanan_evaluasi_sertifikat->getField("KESESUAIAN_TOTAL");
                                ?>
                                <input type="text" name="reqKebutuhanPemenuhanNilai[]" id="reqKebutuhanPemenuhanNilai<?=$j?>-<?=$i?>" readonly value="<?=$rekanan_evaluasi_sertifikat->getField("KESESUAIAN_TOTAL")?>" style="width:40px;" />                
                                <input type="hidden" name="reqPaketEvaluasiSertifikatId[]" id="reqPaketEvaluasiSertifikatId<?=$j?>-<?=$i?>" value="<?=$arrEvalSertifikatId[$j]?>" style="width:50px;" />
                                <input type="hidden" name="reqPaketRekananSertifikatId[]" id="reqPaketRekananSertifikatId<?=$j?>-<?=$i?>" value="<?=$arrPaketRekananId[$i]?>" style="width:50px;" />
                                <?php
                                unset($rekanan_evaluasi_sertifikat);
                                ?>
                            </td>                
                            </tr>
                        <?php
                            $k=0;
                            $total_sertifikat = 0;
                            $rekanan_evaluasi_sertifikat = new RekananEvaluasiSertifikatLain();
                            $rekanan_evaluasi_sertifikat->selectByParams(array("PAKET_EVAL_SERTIFIKAT_LAIN_ID" => $arrEvalSertifikatId[$j], "PAKET_REKANAN_ID" => $arrPaketRekananId[$i]));
                            while($rekanan_evaluasi_sertifikat->nextRow())
                            {
                        ?>  
                            <tr>
                                <td>&raquo; <a onclick="openAdd('main/loadUrl/main/paket_evaluasi_lihat_sertifikat/?reqId=<?=$rekanan_evaluasi_sertifikat->getField("REKANAN_SERTIFIKAT_ID")?>')" style="text-decoration:none"><?=$rekanan_evaluasi_sertifikat->getField("SERTIFIKAT")?></a></td>
                                <td style="width:3%">&nbsp;</td>
                                <td>
                                    <select name="reqKesesuaian[]" id="reqKesesuaian<?=$k?>-<?=$j?>-<?=$i?>" onchange="hitungSertifikat('<?=$k?>', '<?=$j?>', '<?=$i?>')">
                                    <option value=""></option>
                                    <option value="S" <?php if($rekanan_evaluasi_sertifikat->getField("KESESUAIAN") == "S") {?> selected="selected" <?php } ?>>S</option>
                                    <option value="R" <?php if($rekanan_evaluasi_sertifikat->getField("KESESUAIAN") == "R") {?> selected="selected" <?php } ?>>R</option>
                                    <option value="TS" <?php if($rekanan_evaluasi_sertifikat->getField("KESESUAIAN") == "TS") {?> selected="selected" <?php } ?>>TS</option>
                                    </select>     
                                    <input type="text" name="reqKesesuaianNilai[]" id="reqKesesuaianNilai<?=$k?>-<?=$j?>-<?=$i?>" value="<?=$rekanan_evaluasi_sertifikat->getField("KESESUAIAN_NILAI")?>" style="width:40px" onkeyup="hitungSertifikat('<?=$k?>', '<?=$j?>', '<?=$i?>');" />                        
                                    <input type="hidden" name="reqRekananEvaluasiSertifikatId[]" value="<?=$rekanan_evaluasi_sertifikat->getField("REKANAN_EVAL_SERTIFIKAT_ID")?>" />                    
                                </td>
                            </tr>
                        <?php    
                                $total_sertifikat += 1;
                                $k++;
                                $nilai_final = $rekanan_evaluasi_sertifikat->getField("NILAI");
                            }
                        ?>
                            <tr>
                                <td colspan="3"><input type="hidden" id="reqPemenuhan<?=$j?>-<?=$i?>" value="<?=$total_sertifikat?>" /></td>
                            </tr>
                        <?php
                            unset($rekanan_evaluasi_sertifikat);
                        }
                        
                        ?>
                        </table>
                        </td>
                        <td valign="top"><?=$arrNilai[0]?> * <label style="font-size:13px;" id="reqNilaiProsentase<?=$i?>"><?=$prosentase_nilai?></label>%</td>     
                        <td valign="top" align="center">
                            <input type="hidden" name="reqPaketRekanan[]" value="<?=$arrPaketRekananId[$i]?>" />
                            <input type="text" name="reqNilaiFinal[]" id="reqNilaiFinal<?=$i?>" style="width:40px" value="<?=$nilai_final?>" <?=$readonly?> />
            
            
                                    <div class="area-catatan-panitia" style="margin:10px">
                                        <div class="judul">
                                        Catatan Untuk Peserta (apabila tidak memenuhi syarat) :
                                        </div>
                                        <div class="isi">
                                        <?php
                                          $paket_rekanan_kualifikasi = new PaketRekananKualifikasi();
                                          $paket_rekanan_kualifikasi->selectByParams(array("A.PAKET_REKANAN_ID" => $arrPaketRekananId[$i], "A.KODE" => "SERTIFIKAT"));
                                          $paket_rekanan_kualifikasi->firstRow();
                                          $reqCatatan = $paket_rekanan_kualifikasi->getField("CATATAN");
                                          unset($paket_rekanan_kualifikasi);
                                        ?>
                                        <textarea style="width:200px; height:100px;" name="reqCatatan[]"><?=$reqCatatan?></textarea>
                                        </div>
                                    </div>                    
                        </td>
                        <td valign="top"></td>        
                  </tr>
                <?php
                    unset($arrNilaiFinalPerEvaluasi);
                }
                ?>    
            </table>
             
            <div>
                <a href="main/index/paket_detil/?reqId=<?=$reqId?>" class="btn btn-danger"><i class="fa fa-arrow-left"></i> Kembali</a>
                <input type="hidden" name="submitSimpan" value="SimpanSertifikat" />
                <button type="submit" class="btn btn-primary"><i class="fa fa-check-square-o"></i> Simpan</button>
                <a class="btn btn-info" href="main/loadUrl/report/evaluasi_kualifikasi_sertifikat_excel/?reqId=<?=$reqId?>" target="_blank" ><i class="fa fa-print"></i> Cetak</a>
            </div>
                  
           </form>

          </div>
        </div>
      </div>
    </div>
  </div> 
</div>   