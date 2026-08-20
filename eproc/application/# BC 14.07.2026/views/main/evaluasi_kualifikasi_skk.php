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
$this->load->model("RekananEvaluasiKeuangan");
$this->load->model("PaketRekananKualifikasi");
include_once("functions/string.func.php");
include_once("functions/date.func.php");
include_once("functions/default.func.php");

$paket_rekanan = new PaketRekanan();

$reqId = $this->input->get("reqId");

$paket_rekanan->selectByParams(array("PAKET_ID" => $reqId), -1, -1, " AND A.TANGGAL_DAFTAR IS NOT NULL AND A.LULUS_PENDAFTARAN = 1 ");
//echo $paket_rekanan->query;
while($paket_rekanan->nextRow())
{
  $arrRekanan[] = $paket_rekanan->getField("REKANAN");
	$arrRekananId[] = $paket_rekanan->getField("REKANAN_ID");
	$arrPaketRekananId[] = $paket_rekanan->getField("PAKET_REKANAN_ID");
	$arrPaketRekananNilai[] = $paket_rekanan->getField("NILAI_PENAWARAN");
}

?>
    <script type="text/javascript">	
	$(function(){
		$('#ff').form({
			url:'rekanan_evaluasi_keuangan_json/set_evaluasi_kualifikasi_skk2',
			onSubmit:function(){
				return $(this).form('validate');
			},
			success:function(data){
					//alert(data);return false;
				$.messager.alert('Info', data, 'info');	
				document.location.href = "main/index/evaluasi_kualifikasi_skk/?reqId=<?=$reqId?>"
			}
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
              <a href="main/index/evaluasi_kualifikasi_skk/?reqId=<?=$reqId?>" class="btn btn-primary disabled"><span class="fa fa-money"></span> Data Keuangan</a>
              <a href="main/index/evaluasi_kualifikasi_pengalaman/?reqId=<?=$reqId?>" class="btn btn-primary"><span class="fa fa-cogs"></span> Data Teknis</a>
              <a href="main/index/evaluasi_kualifikasi_rekapitulasi/?reqId=<?=$reqId?>" class="btn btn-primary"><span class="fa fa-list-alt"></span> Rekapitulasi</a>
            </div> 
          </div>
          <div class="table-responsive">  
            <!-- <hr>
          	<div class="tab-navigasi-sub" style="margin-bottom: 2%">
            	<a class="btn btn-success btn-sm disabled" href="main/index/evaluasi_kualifikasi_skk/?reqId=<?=$reqId?>">SKK</a>
            	<a class="btn btn-success btn-sm" href="main/index/evaluasi_kualifikasi_koran/?reqId=<?=$reqId?>">Rekening Koran</a>
            </div> -->
              
          	<form id="ff" class="easyui-form form-horizontal" method="post" data-options="novalidate:true" enctype="multipart/form-data">
                
              <table class="table table-bordered table-hover">
                <thead>
                  <tr>
                    <th rowspan="2" style="width: 20px">No</th>
                    <th rowspan="2">Nama Perusahaan</th>
                    <th colspan="2">Nilai</th>
                    <th rowspan="2" style="width: 50px">Memenuhi <br>Syarat</th>
                    <th rowspan="2">Catatan <br>
                      <!-- <small>Di isi apabila tidak memenuhi syarat</small> -->
                    </th>
                  </tr>
                  <tr>
                    <th style="width: 30px">SKK</th>
                    <th style="width: 30px">Rekening Koran</th>
                  </tr>
                </thead>
                  <?php
                  for($i=0;$i<count($arrRekanan);$i++)
                  { 
                      $paket_rekanan_kualifikasi = new PaketRekananKualifikasi();
                      $paket_rekanan_kualifikasi->selectByParams(array("A.PAKET_REKANAN_ID" => $arrPaketRekananId[$i], "A.KODE" => "SKK"));
                      $paket_rekanan_kualifikasi->firstRow();
                      $reqNilaiSKK = $paket_rekanan_kualifikasi->getField("NILAI");
                      $reqCatatan = $paket_rekanan_kualifikasi->getField("CATATAN");
                      $reqStatus = $paket_rekanan_kualifikasi->getField("STATUS");
                      
                      $paket_rekanan_kualifikasi2 = new PaketRekananKualifikasi();
                      $paket_rekanan_kualifikasi2->selectByParams(array("A.PAKET_REKANAN_ID" => $arrPaketRekananId[$i], "A.KODE" => "REKENING_KORAN"));
                      $paket_rekanan_kualifikasi2->firstRow();
                      $reqNilaiRekeningKoran = $paket_rekanan_kualifikasi2->getField("NILAI");
                  ?>
                <tr class="terang">
                  <td valign="top" style="text-align: center"><?=$i+1?></td>
                  <td valign="top">
                    <a onclick="openAdd('main/loadUrl/main/data_rekanan/?reqId=<?=$arrRekananId[$i]?>')" style="text-decoration:none"><?=$arrRekanan[$i]?> <span class="fa fa-eye"></span></a>
                    <input type="hidden" name="reqPaketRekanan[]" value="<?=$arrPaketRekananId[$i]?>"/>
                  </td>
                  <td valign="top" style="text-align: center">
                    <input type="text" name="reqNilaiSKK[]" id="reqNilaiSKK<?=$i?>" value="<?=$reqNilaiSKK ?>" style="width:65px" maxlength="3" class="form-control" />
                  </td>
                  <td valign="top" style="text-align: center">
  				          <input type="text" name="reqNilaiRekeningKoran[]" id="reqNilaiRekeningKoran<?=$i?>" value="<?=$reqNilaiRekeningKoran ?>" style="width:65px" maxlength="3" class="form-control" />
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
                    unset($paket_rekanan_kualifikasi);
                  }
                  ?>
              </table>
              
              <div>
                <input type="hidden" name="submitSimpan" value="SimpanSKK" />
                <input type="hidden" name="reqId" value="<?=$reqId?>" />
                <a href="main/index/paket_detil/?reqId=<?=$reqId?>" class="btn btn-danger"><i class="fa fa-arrow-left"></i> Kembali</a>
                <button type="submit" class="btn btn-primary"><i class="fa fa-check-square-o"></i> Simpan</button>
                <!-- <a class="btn btn-info" href="main/loadUrl/report/evaluasi_kualifikasi_skk_excel/?reqId=<?=$reqId?>" target="_blank" ><span class="fa fa-print"></span> Cetak</a> -->
              </div>
            </form> 

          </div>
        </div>
      </div>
    </div>
  </div> 
</div>     