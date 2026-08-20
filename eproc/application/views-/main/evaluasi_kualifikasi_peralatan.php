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
$this->load->model("RekananEvaluasiPeralatan");
$this->load->model("PaketEvaluasiPeralatanDetil");
$this->load->model("PaketEvaluasiPeralatan");
$this->load->model("PaketRekananKualifikasi");
include_once("functions/string.func.php");
include_once("functions/date.func.php");
include_once("functions/default.func.php");
include_once("functions/recordcoloring.func.php");

$paket_rekanan = new PaketRekanan();
$paket_evaluasi_peralatan = new PaketEvaluasiPeralatan();
$paket_evaluasi_peralatan_detil = new PaketEvaluasiPeralatanDetil();

$reqId = $this->input->get("reqId");

$paket_rekanan->selectByParams(array("PAKET_ID" => $reqId), -1, -1, " AND A.TANGGAL_DAFTAR IS NOT NULL AND A.LULUS_PENDAFTARAN = 1 ");
while($paket_rekanan->nextRow())
{
	$arrRekanan[] = $paket_rekanan->getField("REKANAN");
	$arrRekananId[] = $paket_rekanan->getField("REKANAN_ID");
	$arrPaketRekananId[] = $paket_rekanan->getField("PAKET_REKANAN_ID");
}

$paket_evaluasi_peralatan->selectByParams(array("PAKET_ID" => $reqId));
$paket_evaluasi_peralatan->firstRow();
$msb = $paket_evaluasi_peralatan->getField("MSB"); 
$spjb = $paket_evaluasi_peralatan->getField("SPJB"); 
$spdb = $paket_evaluasi_peralatan->getField("SPDB"); 
$nilai = $paket_evaluasi_peralatan->getField("NILAI_MINIMUM"); 

$paket_evaluasi_peralatan_detil->selectByParams(array("PAKET_ID" => $reqId));
while($paket_evaluasi_peralatan_detil->nextRow())
{
	$arrEvalPeralatanId[] = $paket_evaluasi_peralatan_detil->getField("PAKET_EVAL_PERALATAN_DETIL_ID");
	$arrNama[] = $paket_evaluasi_peralatan_detil->getField("NAMA");
	$arrKeterangan[] = $paket_evaluasi_peralatan_detil->getField("KETERANGAN");	
	$arrNilai[] = $paket_evaluasi_peralatan_detil->getField("NILAI");		
}

?>
<script type="text/javascript">
$(document).ready(function() {
	
	$(function(){
		$('#ff').form({
			url:'rekanan_evaluasi_peralatan_json/set_evaluasi_kualifikasi_peralatan',
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
              <a class="btn btn-success btn-sm disabled" href="main/index/evaluasi_kualifikasi_peralatan/?reqId=<?=$reqId?>">Penilaian Peralatan</a>
              <a class="btn btn-success btn-sm" href="main/index/evaluasi_kualifikasi_sertifikat/?reqId=<?=$reqId?>">Penilaian Sertifikat</a>
            </div> 

            <form id="ff" class="easyui-form form-horizontal" method="post" data-options="novalidate:true" enctype="multipart/form-data">
               
              <table class="table table-bordered table-hover">
                <tr class="terang">
                  <th colspan="3">Ketentuan :</th>
                </tr>
                <tr class="terang">
                  <td style="width: 20px">a.</td>
                  <td>  Milik sendiri/sewa beli ada bukti (MSB) &nbsp;
                      <input type="hidden" id="reqMSB" value="<?=$msb?>" />
                  </td>
                  <td><?=$msb?> %</td>
                </tr>
                <tr class="terang">
                  <td>b.</td>
                  <td>  Sewa jangka panjang ada bukti (SPJB) &nbsp;
                      <input type="hidden" id="reqSPJB" value="<?=$spjb?>" />
                  </td> 
                  <td><?=$spjb?> %</td>
                </tr>
                <tr class="terang">
                  <td>c.</td>
                  <td> Sewa jangka pendek ada bukti (SPDB) &nbsp;
                      <input type="hidden" id="reqSPDB" value="<?=$spdb?>" />
                  </td>
                  <td><?=$spdb?> %</td>
                </tr>
              </table>  

              <table class="table table-bordered table-hover">
                <tr class="">
                  <!-- <td colspan="3"> -->
                  
                  <?php /*?><a onclick="windowOpenerPopup(350,450,'Cetak Close','main/loadUrl/main/cetak_evaluasi_kualifikasi_peralatan/?reqId=<?=$reqId?>');" class="btn-cetak">Cetak</a><?php */?>
                  <input type="hidden" id="reqJumlahKategori" value="<?=count($arrEvalPeralatanId)?>" style="width:30px" />
                  <input type="hidden" id="reqNilai" value="<?=$nilai?>" style="width:30px" />
                  <!-- </td> -->
                </tr>
                  <tr class="judul-kolom">
                  <th>No</th>
                  <th style="width:50%">Nama Perusahaan</th>
                  <th>Perhitungan</th>
                  <th>Nilai </th>
                  </tr>
                  <?php
                  for($i=0;$i<count($arrRekanan);$i++)
                  {
                      $arrNilaiFinalPerEvaluasi = array();
                      $nilai_final = 0;
                  ?>
                    <tr class="terang">
                          <td valign="top"><?=$i+1?></td>     
                          <td valign="top">
                          <a onclick="openAdd('main/loadUrl/main/data_rekanan/?reqId=<?=$arrRekananId[$i]?>')" style="text-decoration:none"><?=$arrRekanan[$i]?></a> <br />
                          <table>
                          <?php
                          $prosentase_nilai = 0;
                          for($j=0; $j<count($arrEvalPeralatanId);$j++)
                          {
                          ?>
                              <tr>
                              <td colspan="3" style="width:90%">
                                  <?=$j+1?>. <?=$arrNama[$j]?>, <?=$arrKeterangan[$j]?> - <strong>Prosentase : <?=$arrNilai[$j]?>%</strong>
                                  <input type="hidden" id="reqProsentase<?=$j?>-<?=$i?>" value="<?=$arrNilai[$j]?>" />     
                              </td> 
                              <td>
                                  <?php
                                  $rekanan_evaluasi_peralatan = new RekananEvaluasiPeralatan();
                                  $rekanan_evaluasi_peralatan->selectByParams(array("PAKET_EVAL_PERALATAN_DETIL_ID" => $arrEvalPeralatanId[$j], "PAKET_REKANAN_ID" => $arrPaketRekananId[$i]));
                                  $rekanan_evaluasi_peralatan->firstRow();
                                  $prosentase_nilai += $rekanan_evaluasi_peralatan->getField("KESESUAIAN_TOTAL");
                                  ?>
                                  <input type="text" name="reqKebutuhanPemenuhanNilai[]" id="reqKebutuhanPemenuhanNilai<?=$j?>-<?=$i?>" readonly value="<?=$rekanan_evaluasi_peralatan->getField("KESESUAIAN_TOTAL")?>" style="width:40px;" />                
                                  <input type="hidden" name="reqPaketEvaluasiPeralatanId[]" id="reqPaketEvaluasiPeralatanId<?=$j?>-<?=$i?>" value="<?=$arrEvalPeralatanId[$j]?>" style="width:50px;" />
                                  <input type="hidden" name="reqPaketRekananPeralatanId[]" id="reqPaketRekananPeralatanId<?=$j?>-<?=$i?>" value="<?=$arrPaketRekananId[$i]?>" style="width:50px;" />
                                  <?php
                                  unset($rekanan_evaluasi_peralatan);
                                  ?>
                              </td>         
                              </tr>
                          <?php
                              $k = 0;
                              $total_peralatan = 0;
                              $rekanan_evaluasi_peralatan = new RekananEvaluasiPeralatan();
                              $rekanan_evaluasi_peralatan->selectByParams(array("PAKET_EVAL_PERALATAN_DETIL_ID" => $arrEvalPeralatanId[$j], "PAKET_REKANAN_ID" => $arrPaketRekananId[$i]));
                              while($rekanan_evaluasi_peralatan->nextRow())
                              {
                                  switch($rekanan_evaluasi_peralatan->getField("BUKTI_KEPEMILIKAN"))
                                  {
                                      case "Milik Sendiri":
                                          $prosentase_kepemilikan = $msb;
                                          break;
                                      case "Sewa Jangka Panjang":
                                          $prosentase_kepemilikan = $spjb;
                                          break;
                                      case "Sewa Jangka Pendek":
                                          $prosentase_kepemilikan = $spdb;
                                          break;
                                  }         
                          ?>  
                              <tr>
                                  <td style="width:63%">
                                      &raquo; <a onclick="openAdd('main/loadUrl/main/paket_evaluasi_lihat_peralatan/?reqId=<?=$rekanan_evaluasi_peralatan->getField("REKANAN_PERALATAN_ID")?>')" style="text-decoration:none"><?=$rekanan_evaluasi_peralatan->getField("PERALATAN")?></a>
                                       - <span style="color:#03C"><?=$rekanan_evaluasi_peralatan->getField("BUKTI_KEPEMILIKAN")?></span> 
                                  </td>
                                  <td style="width:3%">&nbsp;</td>
                                  <td>
                                      <select name="reqKesesuaian[]" id="reqKesesuaian<?=$k?>-<?=$j?>-<?=$i?>" onchange="hitungPeralatan('<?=$k?>', '<?=$j?>', '<?=$i?>')">
                                      <option value=""></option>
                                      <option value="S" <?php if($rekanan_evaluasi_peralatan->getField("KESESUAIAN") == "S") {?> selected="selected" <?php } ?>>S</option>
                                      <option value="R" <?php if($rekanan_evaluasi_peralatan->getField("KESESUAIAN") == "R") {?> selected="selected" <?php } ?>>R</option>
                                      <option value="TS" <?php if($rekanan_evaluasi_peralatan->getField("KESESUAIAN") == "TS") {?> selected="selected" <?php } ?>>TS</option>
                                      </select>     
                                      <input type="text" name="reqKesesuaianNilai[]" id="reqKesesuaianNilai<?=$k?>-<?=$j?>-<?=$i?>" value="<?=$rekanan_evaluasi_peralatan->getField("KESESUAIAN_NILAI")?>" style="width:40px" onkeyup="hitungPeralatan('<?=$k?>', '<?=$j?>', '<?=$i?>');" />                        
                                      <input type="hidden" name="reqKepemilikan[]" id="reqKepemilikan<?=$k?>-<?=$j?>-<?=$i?>" value="<?=$prosentase_kepemilikan?>" style="width:40px" />                                    
                                      <input type="hidden" name="reqRekananEvaluasiPeralatanId[]" value="<?=$rekanan_evaluasi_peralatan->getField("REKANAN_EVAL_PERALATAN_ID")?>" />                    
                                  </td>
                              </tr>
                          <?php    
                                  $nilai_final = $rekanan_evaluasi_peralatan->getField("NILAI");
                                  $total_peralatan += 1;
                                  $k++;
                              }
                              unset($rekanan_evaluasi_peralatan);
                              ?>
                              <tr>
                                  <td colspan="3"><input type="hidden" id="reqPemenuhan<?=$j?>-<?=$i?>" value="<?=$total_peralatan?>" /></td>
                              </tr>
                          <?php
                          }
                          ?>            
                          </table>
                          </td>
                          <td><?=$nilai?> * <label style="font-size:13px;" id="reqNilaiProsentase<?=$i?>"><?=$prosentase_nilai?></label>%</td>      
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
                                            $paket_rekanan_kualifikasi->selectByParams(array("A.PAKET_REKANAN_ID" => $arrPaketRekananId[$i], "A.KODE" => "PERALATAN"));
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
                      unset($arrNilaiProsentase);
                  }
                  ?>    
              </table>
                  
              <div>
                <a href="main/index/paket_detil/?reqId=<?=$reqId?>" class="btn btn-danger"><i class="fa fa-arrow-left"></i> Kembali</a>
                <input type="hidden" name="submitSimpan" value="SimpanPeralatan" />
                <button type="submit" class="btn btn-primary"><i class="fa fa-check-square-o"></i> Simpan</button>
                <a class="btn btn-info" href="main/loadUrl/report/evaluasi_kualifikasi_peralatan_excel/?reqId=<?=$reqId?>" target="_blank" ><i class="fa fa-print"></i> Cetak</a>
              </div>
            </form>    

          </div>
        </div>
      </div>
    </div>
  </div> 
</div>   