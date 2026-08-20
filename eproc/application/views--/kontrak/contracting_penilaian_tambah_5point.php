<?php
/**
 * @package 	eProcurement Application
 * @author 		eproc2025
 * @since 		25. Version 3.1
 * 
 */

$this->libsession->cekSession();   

$this->load->library("kauth");  $userLogin = new kauth();
$this->load->library("paketinfo"); $paketInfo = new paketinfo();
$this->load->model("Rekanan");
$this->load->model("Paket");
$this->load->model("PaketPenilaian");
$this->load->model("Paketpemenang");
$this->load->model("Contracting");
$this->load->library("FileHandler");
include_once("functions/string.func.php");
include_once("functions/date.func.php");
include_once("functions/default.func.php");

$rekanan = new Rekanan();
$paketpenilaian = new PaketPenilaian();
$paketpenilaianChild = new PaketPenilaian();
$paketpenilaianChildCount = new PaketPenilaian();
$cekPenilaian = new PaketPenilaian();
$getpaket_pemenang = new Paketpemenang();

$reqMode = $this->input->get("reqMode");
$reqId = $this->input->get("reqId"); // contractingrekananid
$getTahun = $this->session->userdata('setTahunKontrak'); // tahun session
$reqProses = $this->session->userdata('setProsesKontrak'); 

$contracting = new Contracting();

$contracting->selectByParams(array("A.CONTRACTINGREKANANID" => $reqId)); 
$contracting->firstRow(); 

$reqJenisKontrak = $contracting->getField('JENIS_KONTRAK') ?: '-'; 
$reqRekananId = $contracting->getField('PEMENANG') ?: '-';     
$reqPaketId = $contracting->getField('PAKET_ID') ?: '-';    

$paketInfo->getPaket($reqId);
$reqNama = $paketInfo->nama; 
$reqMetodeLelang = $paketInfo->metode_lelang_nama;

$rekanan->selectByParams(array("REKANAN_ID" => $reqRekananId), -1, -1, '');
$rekanan->firstRow();
$reqRekananName = $rekanan->getField("NAMA");  

if ($reqRekananName == "") {
 redirect(base_url().'kontrak/index/contracting_penilaian?reqId='.$reqId.'&reqProses='.$reqProses);
}

$paketpenilaian->selectParent(array(), -1, -1, '');
?>
<script type="text/javascript">
$(document).ready(function() {

	$(function(){
		$('#ff').form({
			url:'paket_penilaian_json/penilaian',
			onSubmit:function(){
				return $(this).form('validate');
			},
			success:function(data){
				//alert(data);return false;
				alertSuccess2(data); 
     //    setTimeout(function() {
					// document.location.href = 'kontrak/index/contracting_penilaian_tambah/?reqId=<?=$reqId?>&pemenang=<?=$pemenang?>';
     //    }, 2000);
			}
		});

	});

});
</script>

<div class="row"> 
  <div class="col-md-12 col-sm-12">
    <div class="card">
      <div class="card-header card-head-inverse bg-primary">
        <h4 class="card-title text-white">Penilaian <?= $reqMetodeLelang ?>  
        </h4>
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
          <div class="card mb-1">
            <div class="table-responsive">
		        <form id="ff" class="easyui-form form-horizontal" method="post" data-options="novalidate:true" enctype="multipart/form-data">
		            
					<table class="table table-bordered table-hover">
						<tr>
							<td colspan="6"> Nama Rekanan: <?= $reqRekananName ?> </td>
						</tr>
						<tr>
							<td colspan="6"> Nama Paket : <?=$paketInfo->nama?> </td>
						</tr>
						<tr>
							<td colspan="6">
								<h4>PENILAIAN :</h4>
								<p>
								Bagaimana tingkat kepuasan anda terhadap performance penyedia untuk area-area : <br> 
								Pilih pada satu pilihan setiap baris <br>
								<h4>ASPEK PENILAIAN KINERJA</h4>
								<table class="table table-striped" width="70%" style="width: 60%;">
									<thead>
									  <tr>
									    <th width="20%">Aspek</th>
									    <th width="70%">Indikator</th>
									    <th width="10%">Bobot</th>
									  </tr>
									</thead>
									<tbody>
									  <tr>
									    <td>Kualitas</td>
									    <td>Kesesuaian hasil pekerjaan</td>
									    <td>30%</td>
									  </tr>
									  <tr>
									    <td>Kuantitas</td>
									    <td>Ketepatan volume pekerjaan</td>
									    <td>20%</td>
									  </tr>
									  <tr>
									    <td>Waktu</td>
									    <td>Kemampuan penyelesaian pekerjaan</td>
									    <td>30%</td>
									  </tr>
									  <tr>
									    <td>Layanan</td>
									    <td>Kecepatan respon risiko pekerjaan</td>
									    <td>20%</td>
									  </tr>
									</tbody>
									</table>

									<h4>SKOR PENILAIAN KINERJA</h4>
									<table class="table table-striped" width="70%" style="width: 40%;">
										<thead>
										  <tr>
										    <th width="15%">Kriteria</th>
										    <th width="4%">Skor</th>
										  </tr>
										</thead>
										<tbody>
										  <tr>
										    <td>Sangat Baik</td>
										    <td>5</td>
										  </tr>
										  <tr>
										    <td>Baik</td>
										    <td>4</td>
										  </tr>
										  <tr>
										    <td>Cukup</td>
										    <td>3</td>
										  </tr>
										  <tr>
										    <td>Kuran</td>
										    <td>2</td>
										  </tr>
										</tbody>
										</table>
							</td>
						</tr>
						<!-- <input type="hidden" name="regRekananId" value="<?php //$reqRekananId?>"> -->
						<input type="hidden" name="regRekananId" value="<?=$reqRekananId?>">
						<input type="hidden" name="reqPaketId" value="<?=$reqPaketId?>">
						<input type="hidden" name="regId" value="<?=$reqId?>">
						<?php 
						while($paketpenilaian->nextRow())
						{
							$paketpenilaianChild->selectChild(array("PPT_PARENT_ID" => $paketpenilaian->getField("PPT_ID")), -1, -1, '');
							$total = $paketpenilaianChildCount->getCountByParams(array("PPT_PARENT_ID" => $paketpenilaian->getField("PPT_ID")));
						 ?>
							<table class="table table-bordered table-hover">
								<tr>
									<th colspan="7">
									<?= '<b>'.$paketpenilaian->getField("KODE").'. '.$paketpenilaian->getField("NAMA").'</b>'?>
									<input type="hidden" name="regParentId[]" value="<?=$paketpenilaian->getField("PPT_ID")?>">
									<input type="hidden" name="getTotal<?=$paketpenilaianChild->getField("PPT_PARENT_ID")?>[]" value="<?=$total?>">
									</th>
								</tr>
								<tr class="judul-kolom">
									<th align="center" valign="middle" width="2%">No.</th>
									<th align="left" valign="middle" width="50%">Keterangan</th>
									<th align="center" valign="middle" style="text-align: center">TR</th>
									<th align="center" valign="middle" style="text-align: center">K</th>
									<th align="center" valign="middle" style="text-align: center">C</th>
									<th align="center" valign="middle" style="text-align: center">B</th>
									<th align="center" valign="middle" style="text-align: center">BS</th>
								</tr>
								<?php 
								$no 		= 1;
								$noChild 	= 0; 
								while($paketpenilaianChild->nextRow())
								{
								 $cekPenilaian->selectPenilaian(array("CONTRACTINGREKANANID" => $reqId,"REKANAN_ID" => $reqRekananId, "PPT_ID" => $paketpenilaianChild->getField("PPT_ID"), "PPT_PARENT_ID" => $paketpenilaianChild->getField("PPT_PARENT_ID")), -1, -1, '');
								 $cekPenilaian->firstRow();
								 $nilai = $cekPenilaian->getField("NILAI");
								 $note 	= $cekPenilaian->getField("NOTE");
								?>
								<tr class="gelap">
									<td valign="top"><strong><?=$no?></strong></td>
									<input type="hidden" name="getNAMA<?=$paketpenilaianChild->getField("PPT_PARENT_ID")?>[]" value="<?=$paketpenilaianChild->getField("NAMA")?>">
									<input type="hidden" name="getChildId<?=$paketpenilaianChild->getField("PPT_PARENT_ID")?>[]" value="<?=$paketpenilaianChild->getField("PPT_ID")?>">
									<td valign="top"><b><?=$paketpenilaianChild->getField("NAMA")?></b><br><?=$paketpenilaianChild->getField("NOTE")?></td>
									<?php 
									switch ($nilai) {
										case '1':
											echo 
											'	<td align="center" valign="top"><input type="radio" value="1" name="getNilai'.$noChild.$paketpenilaianChild->getField("PPT_PARENT_ID").'[]" checked="" style="cursor:pointer"></td>
												<td align="center" valign="top"><input type="radio" value="2" name="getNilai'.$noChild.$paketpenilaianChild->getField("PPT_PARENT_ID").'[]" style="cursor:pointer"></td>
												<td align="center" valign="top"><input type="radio" value="3" name="getNilai'.$noChild.$paketpenilaianChild->getField("PPT_PARENT_ID").'[]" style="cursor:pointer"></td>
												<td align="center" valign="top"><input type="radio" value="4" name="getNilai'.$noChild.$paketpenilaianChild->getField("PPT_PARENT_ID").'[]" style="cursor:pointer"></td>
												<td align="center" valign="top"><input type="radio" value="5" name="getNilai'.$noChild.$paketpenilaianChild->getField("PPT_PARENT_ID").'[]" style="cursor:pointer"></td>';
											break;

										case '2':
											echo 
											'	<td align="center" valign="top"><input type="radio" value="1" name="getNilai'.$noChild.$paketpenilaianChild->getField("PPT_PARENT_ID").'[]" style="cursor:pointer"></td>
												<td align="center" valign="top"><input type="radio" value="2" name="getNilai'.$noChild.$paketpenilaianChild->getField("PPT_PARENT_ID").'[]" checked="" style="cursor:pointer"></td>
												<td align="center" valign="top"><input type="radio" value="3" name="getNilai'.$noChild.$paketpenilaianChild->getField("PPT_PARENT_ID").'[]" style="cursor:pointer"></td>
												<td align="center" valign="top"><input type="radio" value="4" name="getNilai'.$noChild.$paketpenilaianChild->getField("PPT_PARENT_ID").'[]" style="cursor:pointer"></td>
												<td align="center" valign="top"><input type="radio" value="5" name="getNilai'.$noChild.$paketpenilaianChild->getField("PPT_PARENT_ID").'[]" style="cursor:pointer"></td>';
											break;

										case '3':
											echo 
											'	<td align="center" valign="top"><input type="radio" value="1" name="getNilai'.$noChild.$paketpenilaianChild->getField("PPT_PARENT_ID").'[]" style="cursor:pointer"></td>
												<td align="center" valign="top"><input type="radio" value="2" name="getNilai'.$noChild.$paketpenilaianChild->getField("PPT_PARENT_ID").'[]" style="cursor:pointer"></td>
												<td align="center" valign="top"><input type="radio" value="3" name="getNilai'.$noChild.$paketpenilaianChild->getField("PPT_PARENT_ID").'[]" checked="" style="cursor:pointer"></td>
												<td align="center" valign="top"><input type="radio" value="4" name="getNilai'.$noChild.$paketpenilaianChild->getField("PPT_PARENT_ID").'[]" style="cursor:pointer"></td>
												<td align="center" valign="top"><input type="radio" value="5" name="getNilai'.$noChild.$paketpenilaianChild->getField("PPT_PARENT_ID").'[]" style="cursor:pointer"></td>';
											break;

										case '4':
											echo 
											'	<td align="center" valign="top"><input type="radio" value="1" name="getNilai'.$noChild.$paketpenilaianChild->getField("PPT_PARENT_ID").'[]" style="cursor:pointer"></td>
												<td align="center" valign="top"><input type="radio" value="2" name="getNilai'.$noChild.$paketpenilaianChild->getField("PPT_PARENT_ID").'[]" style="cursor:pointer"></td>
												<td align="center" valign="top"><input type="radio" value="3" name="getNilai'.$noChild.$paketpenilaianChild->getField("PPT_PARENT_ID").'[]" style="cursor:pointer"></td>
												<td align="center" valign="top"><input type="radio" value="4" name="getNilai'.$noChild.$paketpenilaianChild->getField("PPT_PARENT_ID").'[]" checked="" style="cursor:pointer"></td>
												<td align="center" valign="top"><input type="radio" value="5" name="getNilai'.$noChild.$paketpenilaianChild->getField("PPT_PARENT_ID").'[]" style="cursor:pointer"></td>';
											break;

										case '5':
											echo 
											'	<td align="center" valign="top"><input type="radio" value="1" name="getNilai'.$noChild.$paketpenilaianChild->getField("PPT_PARENT_ID").'[]" style="cursor:pointer"></td>
												<td align="center" valign="top"><input type="radio" value="2" name="getNilai'.$noChild.$paketpenilaianChild->getField("PPT_PARENT_ID").'[]" style="cursor:pointer"></td>
												<td align="center" valign="top"><input type="radio" value="3" name="getNilai'.$noChild.$paketpenilaianChild->getField("PPT_PARENT_ID").'[]" style="cursor:pointer"></td>
												<td align="center" valign="top"><input type="radio" value="4" name="getNilai'.$noChild.$paketpenilaianChild->getField("PPT_PARENT_ID").'[]" style="cursor:pointer"></td>
												<td align="center" valign="top"><input type="radio" value="5" name="getNilai'.$noChild.$paketpenilaianChild->getField("PPT_PARENT_ID").'[]" checked="" style="cursor:pointer"></td>';
											break;
										
										default:
											echo 
											'	<td align="center" valign="top"><input type="radio" value="1" name="getNilai'.$noChild.$paketpenilaianChild->getField("PPT_PARENT_ID").'[]" checked="" style="cursor:pointer"></td>
												<td align="center" valign="top"><input type="radio" value="2" name="getNilai'.$noChild.$paketpenilaianChild->getField("PPT_PARENT_ID").'[]" style="cursor:pointer"></td>
												<td align="center" valign="top"><input type="radio" value="3" name="getNilai'.$noChild.$paketpenilaianChild->getField("PPT_PARENT_ID").'[]" style="cursor:pointer"></td>
												<td align="center" valign="top"><input type="radio" value="4" name="getNilai'.$noChild.$paketpenilaianChild->getField("PPT_PARENT_ID").'[]" style="cursor:pointer"></td>
												<td align="center" valign="top"><input type="radio" value="5" name="getNilai'.$noChild.$paketpenilaianChild->getField("PPT_PARENT_ID").'[]" style="cursor:pointer"></td>';
											break;
									}
									?>
									
								</tr>
							<?php $no++; $noChild++; } ?>
							</table> <br>

							<div class="control-group">
				              <label class="control-label"><b>Komentar <i>(Comments)</i></b> :</label>
				              <div class="controls">
			                    <textarea class="form-control" name="reqNote<?=$paketpenilaianChild->getField("PPT_PARENT_ID")?>[]" id="reqNote" cols="45" rows="5" class="easyui-validatebox span9" required><?=$note?></textarea>
				              </div>
				            </div> <hr><br>
						<?php } ?>
					</table> 

		            <div> 
		                <input type="hidden" name="reqId" value="<?=$reqId?>" />
		                <a href="kontrak/index/contracting_penilaian?reqId=<?=$reqId?>&reqProses=<?= $reqProses ?>" class="btn btn-danger"><i class="fa fa-arrow-left"></i> Kembali</a>
		                <input type="hidden" name="submitSimpan" value="Simpan" />
		                <input type="submit" name="reqSubmit" id="reqSubmit" value="Simpan" style="cursor:pointer" class="btn btn-primary" />
					</div>
				</form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div> 
</div>  