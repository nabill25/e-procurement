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
$reqTemplate = $this->input->get("reqTemplate"); // template
$reqRekananId = $this->input->get("reqRekananId"); // Pemenang
$getTahun = $this->session->userdata('setTahunKontrak'); // tahun session
$reqProses = $this->session->userdata('setProsesKontrak');

// if ($reqTemplate == '') {
// 	echo "Silahkan pilih template penilaian dahulu..!!!";
// 	exit;
// }

$contracting = new Contracting();

$contracting->selectByParams(array("A.CONTRACTINGREKANANID" => $reqId));
$contracting->firstRow();

$reqJenisKontrak = $contracting->getField('JENIS_KONTRAK') ?: '-';
// $reqRekananId = $contracting->getField('PEMENANG') ?: '-'; 
$reqPaketId = $contracting->getField('PAKET_ID') ?: '-';

// echo $reqRekananId; die();

$paketInfo->getPaket($reqPaketId);
$reqNama = $paketInfo->nama;
$reqMetodeLelang = $paketInfo->metode_lelang_nama;

$rekanan->selectByParams(array("A.REKANAN_ID" => $reqRekananId), -1, -1, '');
$rekanan->firstRow();
$reqRekananName = $rekanan->getField("NAMA");

if ($reqRekananName == "") {
 redirect(base_url().'kontrak/index/contracting_penilaian?reqId='.$reqId.'&reqProses='.$reqProses);
}

// $paketpenilaian->selectParent(array("TEMPLATE" => $reqTemplate), -1, -1, '');
if ($this->USER_TYPE_ID == '9') { // Pengguna
	$paketpenilaian->selectParent(array("USER_TYPE_ID" => '9'), -1, -1, '');
} else if ($this->USER_TYPE_ID == '12' || $this->USER_TYPE_ID == '20' || $this->USER_TYPE_ID == '28') { // Perencana
	$paketpenilaian->selectParent(array("USER_TYPE_ID" => '122'), -1, -1, '');
} 
?>
<script type="text/javascript">
$(document).ready(function() {

	$(function(){
		$('#ff').form({
			url:'paket_penilaian_json/penilaian',
			onSubmit:function(){
				// return $(this).form('validate');
				var v=$(this).form('validate');
        if(v) showLoad();  // show the message box
        return v;
			},
			success:function(data){
				//alert(data);return false;
				alertSuccess2(data);
        setTimeout(function() {
        	<?php 
        	if ($this->USER_TYPE_ID == '9') {
        	?>
					document.location.href = 'kontrak/index/contracting_pengguna';
					<?php 
					} else { ?>
					document.location.href = 'kontrak/index/contracting_penilaian/?reqId=<?=$reqId?>';
					<?php 
					} ?>
        }, 2000);
			}
		});

	});

	$('.radioDeal').on('input', function(){
    var name = $(this).attr("name");
    var id = $(this).data("id");
	  var checkedvalue =  $('input[name="'+ name +'"]:checked').val();
    // alert(id +' = '+name+' = '+checkedvalue);
    if (checkedvalue == 2 ) {
    	$("#reqNote"+id).attr('required', '');
    	$("#setNotif"+id).html('Komentar harus di isi');
    } else {
    	$("#reqNote"+id).removeAttr('required', '');
    	$("#setNotif"+id).html('');
    }

  });

});
</script>

<div class="row">
  <div class="col-md-12 col-sm-12">
    <div class="card">
      <div class="card-header card-head-inverse bg-primary">
        <h4 class="card-title text-white">FORMULIR PENILAIAN KINERJA PENYEDIA BARANG/JASA
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
            	<?php 
            	if($this->libkontrak->cekTagihanSelesai($reqId)) 
            	{?>
				        <form id="ff" class="easyui-form form-horizontal" method="post" data-options="novalidate:true" enctype="multipart/form-data">

									<table class="table table-bordered table-hover">
										<tr>
											<td width="200px"> Nama Penyedia: </td>
											<td colspan="5"><?= $reqRekananName ?> </td>
										</tr>
										<tr>
											<td width="200px"> Nama Paket: </td>
											<td colspan="5"><?= $paketInfo->nama ?> </td>
										</tr>
										<tr>
											<td colspan="6">
												<h4>KETERANGAN SKOR AKHIR</h4> 
													<table class="table table-striped" width="70%" style="width: 40%;">
														<thead>
														  <tr>
														    <th width="15%">Skor Tertimbang (0–5)</th>
														    <th width="4%">Kualifikasi Penilaian</th>
														  </tr>
														</thead>
														<tbody>
														  <tr>
														    <td>4.51 – 5.00</td>
														    <td>Sangat Baik</td>
														  </tr>
														  <tr>
														    <td>3.51 – 4.50</td>
														    <td>Baik</td>
														  </tr> 
														  <tr>
														    <td>2.51 – 3.50</td>
														    <td>Cukup</td>
														  </tr> 
														  <tr>
														    <td>1.51 – 2.50</td>
														    <td>Buruk</td>
														  </tr> 
														  <tr>
														    <td>0.00 – 1.50</td>
														    <td>Sangat Buruk</td>
														  </tr> 
														</tbody>
													</table>
											</td>
										</tr>
										<!-- <input type="hidden" name="regRekananId" value="<?php //$reqRekananId?>"> -->
										<input type="hidden" name="reqTemplate" value="<?=$reqTemplate?>">
										<input type="hidden" name="regRekananId" value="<?=$reqRekananId?>">
										<input type="hidden" name="reqPaketId" value="<?=$reqPaketId?>">
										<input type="hidden" name="regId" value="<?=$reqId?>">
										<?php
										$noteno = 1;
										while($paketpenilaian->nextRow())
										{
											if ($this->LEVEL_KONTRAK == '2') { // Pengendali
												$paketpenilaianChild->selectChild(array("USER_TYPE_ID" => '122', "PPT_PARENT_ID" => $paketpenilaian->getField("PPT_ID")), -1, -1, '');
												$total = $paketpenilaianChildCount->getCountByParams(array("PPT_PARENT_ID" => $paketpenilaian->getField("PPT_ID")));
											} else if ($this->LEVEL_KONTRAK == '3') { // Penyelesaian
												$paketpenilaianChild->selectChild(array("USER_TYPE_ID" => '123', "PPT_PARENT_ID" => $paketpenilaian->getField("PPT_ID")), -1, -1, '');
												$total = $paketpenilaianChildCount->getCountByParams(array("PPT_PARENT_ID" => $paketpenilaian->getField("PPT_ID")));
											} else if ($this->USER_TYPE_ID == '9') { // Pengguna
												$paketpenilaianChild->selectChild(array("USER_TYPE_ID" => $this->USER_TYPE_ID, "PPT_PARENT_ID" => $paketpenilaian->getField("PPT_ID")), -1, -1, '');
												$total = $paketpenilaianChildCount->getCountByParams(array("PPT_PARENT_ID" => $paketpenilaian->getField("PPT_ID")));
											} else if ($this->USER_TYPE_ID == '20' || $this->USER_TYPE_ID == '28') { // Pengguna
												$paketpenilaianChild->selectChild(array("PPT_PARENT_ID" => $paketpenilaian->getField("PPT_ID")), -1, -1, ' AND USER_TYPE_ID = \'122\' OR USER_TYPE_ID = \'123\' ');
												$total = $paketpenilaianChildCount->getCountByParams(array("PPT_PARENT_ID" => $paketpenilaian->getField("PPT_ID")));
											}
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
													<th align="left" valign="middle" width="25%">Keterangan</th>
													<th align="center" valign="middle" style="text-align: center" width="13%">Sangat Buruk</th>
													<th align="center" valign="middle" style="text-align: center" width="13%">Buruk</th>
													<th align="center" valign="middle" style="text-align: center" width="13%">Cukup</th>
													<th align="center" valign="middle" style="text-align: center" width="13%">Baik</th>
													<th align="center" valign="middle" style="text-align: center" width="13%">Sangat Baik</th>
												</tr>
												<?php
												$no 		= 1;
												$noChild 	= 0;
												while($paketpenilaianChild->nextRow())
												{
												 $cekPenilaian->selectPenilaian(array("CONTRACTINGREKANANID" => $reqId,"A.REKANAN_ID" => $reqRekananId, "PPT_ID" => $paketpenilaianChild->getField("PPT_ID"), "PPT_PARENT_ID" => $paketpenilaianChild->getField("PPT_PARENT_ID")), -1, -1, '');
												 $cekPenilaian->firstRow();
												 $nilai = $cekPenilaian->getField("NILAI");
												 $note 	= $cekPenilaian->getField("NOTE");
												?>
												<tr class="gelap">
													<td valign="top"><strong><?=$no?></strong></td>
													<input type="hidden" name="getNAMA<?=$paketpenilaianChild->getField("PPT_PARENT_ID")?>[]" value="<?=$paketpenilaianChild->getField("NAMA")?>">
													<input type="hidden" name="getPresentasi<?=$paketpenilaianChild->getField("PPT_PARENT_ID")?>[]" value="<?=$paketpenilaianChild->getField("PRESENTASI")?>">
													<input type="hidden" name="getChildId<?=$paketpenilaianChild->getField("PPT_PARENT_ID")?>[]" value="<?=$paketpenilaianChild->getField("PPT_ID")?>">
													<td valign="top"><b><?=$paketpenilaianChild->getField("NAMA")?></b><br><?=$paketpenilaianChild->getField("NOTE")?></td>
													<?php
													switch ($nilai) {
														case '1': // gak ada nilai 1
															echo
															' <td align="center" valign="top">
																	<input type="radio" class="radioDeal" data-id="'.$noteno.'" value="1" checked="" name="getNilai'.$noChild.$paketpenilaianChild->getField("PPT_PARENT_ID").'[]" style="cursor:pointer"><br>
																	<small>'.$paketpenilaianChild->getField("NOTE_1").'</small>
																</td>
																<td align="center" valign="top">
																	<input type="radio" class="radioDeal" data-id="'.$noteno.'" value="2" name="getNilai'.$noChild.$paketpenilaianChild->getField("PPT_PARENT_ID").'[]" style="cursor:pointer"><br>
																	<small>'.$paketpenilaianChild->getField("NOTE_2").'</small>
																</td>
																<td align="center" valign="top">
																	<input type="radio" class="radioDeal" data-id="'.$noteno.'" value="3" name="getNilai'.$noChild.$paketpenilaianChild->getField("PPT_PARENT_ID").'[]" style="cursor:pointer"><br>
																	<small>'.$paketpenilaianChild->getField("NOTE_3").'</small>
																</td>
																<td align="center" valign="top">
																	<input type="radio" class="radioDeal" data-id="'.$noteno.'" value="4" name="getNilai'.$noChild.$paketpenilaianChild->getField("PPT_PARENT_ID").'[]" style="cursor:pointer"><br>
																	<small>'.$paketpenilaianChild->getField("NOTE_4").'</small>
																</td>
																<td align="center" valign="top">
																	<input type="radio" class="radioDeal" data-id="'.$noteno.'" value="5" name="getNilai'.$noChild.$paketpenilaianChild->getField("PPT_PARENT_ID").'[]" style="cursor:pointer"><br>
																	<small>'.$paketpenilaianChild->getField("NOTE_5").'</small>
																</td>';
															break;

														case '2':
															echo
															' <td align="center" valign="top">
																	<input type="radio" class="radioDeal" data-id="'.$noteno.'" value="1" name="getNilai'.$noChild.$paketpenilaianChild->getField("PPT_PARENT_ID").'[]" style="cursor:pointer"><br>
																	<small>'.$paketpenilaianChild->getField("NOTE_1").'</small>
																</td>
																<td align="center" valign="top">
																	<input type="radio" class="radioDeal" data-id="'.$noteno.'" value="2" checked="" name="getNilai'.$noChild.$paketpenilaianChild->getField("PPT_PARENT_ID").'[]" style="cursor:pointer"><br>
																	<small>'.$paketpenilaianChild->getField("NOTE_2").'</small>
																</td>
																<td align="center" valign="top">
																	<input type="radio" class="radioDeal" data-id="'.$noteno.'" value="3" name="getNilai'.$noChild.$paketpenilaianChild->getField("PPT_PARENT_ID").'[]" style="cursor:pointer"><br>
																	<small>'.$paketpenilaianChild->getField("NOTE_3").'</small>
																</td>
																<td align="center" valign="top">
																	<input type="radio" class="radioDeal" data-id="'.$noteno.'" value="4" name="getNilai'.$noChild.$paketpenilaianChild->getField("PPT_PARENT_ID").'[]" style="cursor:pointer"><br>
																	<small>'.$paketpenilaianChild->getField("NOTE_4").'</small>
																</td>
																<td align="center" valign="top">
																	<input type="radio" class="radioDeal" data-id="'.$noteno.'" value="5" name="getNilai'.$noChild.$paketpenilaianChild->getField("PPT_PARENT_ID").'[]" style="cursor:pointer"><br>
																	<small>'.$paketpenilaianChild->getField("NOTE_5").'</small>
																</td>';
															break;

														case '3':
															echo
															' <td align="center" valign="top">
																	<input type="radio" class="radioDeal" data-id="'.$noteno.'" value="1" name="getNilai'.$noChild.$paketpenilaianChild->getField("PPT_PARENT_ID").'[]" style="cursor:pointer"><br>
																	<small>'.$paketpenilaianChild->getField("NOTE_1").'</small>
																</td>
																<td align="center" valign="top">
																	<input type="radio" class="radioDeal" data-id="'.$noteno.'" value="2" name="getNilai'.$noChild.$paketpenilaianChild->getField("PPT_PARENT_ID").'[]" style="cursor:pointer"><br>
																	<small>'.$paketpenilaianChild->getField("NOTE_2").'</small>
																</td>
																<td align="center" valign="top">
																	<input type="radio" class="radioDeal" data-id="'.$noteno.'" value="3" checked="" name="getNilai'.$noChild.$paketpenilaianChild->getField("PPT_PARENT_ID").'[]" style="cursor:pointer"><br>
																	<small>'.$paketpenilaianChild->getField("NOTE_3").'</small>
																</td>
																<td align="center" valign="top">
																	<input type="radio" class="radioDeal" data-id="'.$noteno.'" value="4" name="getNilai'.$noChild.$paketpenilaianChild->getField("PPT_PARENT_ID").'[]" style="cursor:pointer"><br>
																	<small>'.$paketpenilaianChild->getField("NOTE_4").'</small>
																</td>
																<td align="center" valign="top">
																	<input type="radio" class="radioDeal" data-id="'.$noteno.'" value="5" name="getNilai'.$noChild.$paketpenilaianChild->getField("PPT_PARENT_ID").'[]" style="cursor:pointer"><br>
																	<small>'.$paketpenilaianChild->getField("NOTE_5").'</small>
																</td>';
															break;

														case '4':
															echo
															' <td align="center" valign="top">
																	<input type="radio" class="radioDeal" data-id="'.$noteno.'" value="1" name="getNilai'.$noChild.$paketpenilaianChild->getField("PPT_PARENT_ID").'[]" style="cursor:pointer"><br>
																	<small>'.$paketpenilaianChild->getField("NOTE_1").'</small>
																</td>
																<td align="center" valign="top">
																	<input type="radio" class="radioDeal" data-id="'.$noteno.'" value="2" name="getNilai'.$noChild.$paketpenilaianChild->getField("PPT_PARENT_ID").'[]" style="cursor:pointer"><br>
																	<small>'.$paketpenilaianChild->getField("NOTE_2").'</small>
																</td>
																<td align="center" valign="top">
																	<input type="radio" class="radioDeal" data-id="'.$noteno.'" value="3" name="getNilai'.$noChild.$paketpenilaianChild->getField("PPT_PARENT_ID").'[]" style="cursor:pointer"><br>
																	<small>'.$paketpenilaianChild->getField("NOTE_3").'</small>
																</td>
																<td align="center" valign="top">
																	<input type="radio" class="radioDeal" data-id="'.$noteno.'" value="4" checked="" name="getNilai'.$noChild.$paketpenilaianChild->getField("PPT_PARENT_ID").'[]" style="cursor:pointer"><br>
																	<small>'.$paketpenilaianChild->getField("NOTE_4").'</small>
																</td>
																<td align="center" valign="top">
																	<input type="radio" class="radioDeal" data-id="'.$noteno.'" value="5" name="getNilai'.$noChild.$paketpenilaianChild->getField("PPT_PARENT_ID").'[]" style="cursor:pointer"><br>
																	<small>'.$paketpenilaianChild->getField("NOTE_5").'</small>
																</td>';
															break;

														case '5':
															echo
															' <td align="center" valign="top">
																	<input type="radio" class="radioDeal" data-id="'.$noteno.'" value="1" name="getNilai'.$noChild.$paketpenilaianChild->getField("PPT_PARENT_ID").'[]" style="cursor:pointer"><br>
																	<small>'.$paketpenilaianChild->getField("NOTE_1").'</small>
																</td>
																<td align="center" valign="top">
																	<input type="radio" class="radioDeal" data-id="'.$noteno.'" value="2" name="getNilai'.$noChild.$paketpenilaianChild->getField("PPT_PARENT_ID").'[]" style="cursor:pointer"><br>
																	<small>'.$paketpenilaianChild->getField("NOTE_2").'</small>
																</td>
																<td align="center" valign="top">
																	<input type="radio" class="radioDeal" data-id="'.$noteno.'" value="3" name="getNilai'.$noChild.$paketpenilaianChild->getField("PPT_PARENT_ID").'[]" style="cursor:pointer"><br>
																	<small>'.$paketpenilaianChild->getField("NOTE_3").'</small>
																</td>
																<td align="center" valign="top">
																	<input type="radio" class="radioDeal" data-id="'.$noteno.'" value="4" name="getNilai'.$noChild.$paketpenilaianChild->getField("PPT_PARENT_ID").'[]" style="cursor:pointer"><br>
																	<small>'.$paketpenilaianChild->getField("NOTE_4").'</small>
																</td>
																<td align="center" valign="top">
																	<input type="radio" class="radioDeal" data-id="'.$noteno.'" value="5" checked="" name="getNilai'.$noChild.$paketpenilaianChild->getField("PPT_PARENT_ID").'[]" style="cursor:pointer"><br>
																	<small>'.$paketpenilaianChild->getField("NOTE_5").'</small>
																</td>';
															break;

														default:
															echo
															' <td align="center" valign="top">
																	<input type="radio" class="radioDeal" data-id="'.$noteno.'" value="1" name="getNilai'.$noChild.$paketpenilaianChild->getField("PPT_PARENT_ID").'[]" style="cursor:pointer"><br>
																	<small>'.$paketpenilaianChild->getField("NOTE_1").'</small>
																</td>
																<td align="center" valign="top">
																	<input type="radio" class="radioDeal" data-id="'.$noteno.'" value="2" name="getNilai'.$noChild.$paketpenilaianChild->getField("PPT_PARENT_ID").'[]" style="cursor:pointer"><br>
																	<small>'.$paketpenilaianChild->getField("NOTE_2").'</small>
																</td>
																<td align="center" valign="top">
																	<input type="radio" class="radioDeal" data-id="'.$noteno.'" value="3" checked="" name="getNilai'.$noChild.$paketpenilaianChild->getField("PPT_PARENT_ID").'[]" style="cursor:pointer"><br>
																	<small>'.$paketpenilaianChild->getField("NOTE_3").'</small>
																</td>
																<td align="center" valign="top">
																	<input type="radio" class="radioDeal" data-id="'.$noteno.'" value="4" name="getNilai'.$noChild.$paketpenilaianChild->getField("PPT_PARENT_ID").'[]" style="cursor:pointer"><br>
																	<small>'.$paketpenilaianChild->getField("NOTE_4").'</small>
																</td>
																<td align="center" valign="top">
																	<input type="radio" class="radioDeal" data-id="'.$noteno.'" value="5" name="getNilai'.$noChild.$paketpenilaianChild->getField("PPT_PARENT_ID").'[]" style="cursor:pointer"><br>
																	<small>'.$paketpenilaianChild->getField("NOTE_5").'</small>
																</td>';
															break;
													}
													?>

													<script type="text/javascript">
														// $('input[type=radio][name=<?php // 'getNilai'.$noChild.$paketpenilaianChild->getField("PPT_PARENT_ID") ?>]').change(function() {
													 //    if (this.value == 'allot') {
												  //       alert("Allot Thai Gayo Bhai");
													 //    }
													 //    else if (this.value == 'transfer') {
												  //       alert("Transfer Thai Gayo");
													 //    }
														// });
													</script>

												</tr>
											<?php $no++; $noChild++;
												} ?>
											</table> <br>

											<!-- <div class="control-group">
								              <label class="control-label"><b>Komentar <i>(Comments)</i></b> <i id="setNotif<?= $noteno; ?>" style="color: red; font-weight: bold;"> </i> :</label>
								              <div class="controls">
							                    <textarea class="form-control" name="reqNote<?=$paketpenilaianChild->getField("PPT_PARENT_ID")?>[]" id="reqNote<?= $noteno; ?>" cols="45" rows="5" class="easyui-validatebox span9"><?=$note?></textarea>
								              </div>
								            </div> <hr><br> -->
										<?php
										$noteno++;
										} ?>
									</table>

				          <div>
				            <input type="hidden" name="reqId" value="<?=$reqId?>" />
				            <?php 
											if ($this->USER_TYPE_ID == '9') { // Pengguna
				             ?>
				            	<a href="kontrak/index/contracting_pengguna" class="<?= CLASS_BTN_DANGER ?> mr-1"><?= BTN_KEMBALI ?></a>
				            <?php 
				            } else { ?>
				            	<a href="kontrak/index/contracting_penilaian?reqId=<?=$reqId?>&reqProses=<?= $reqProses ?>" class="<?= CLASS_BTN_DANGER ?> mr-1"><?= BTN_KEMBALI ?></a>
				            <?php 
				            } ?>
				            <input type="hidden" name="submitSimpan" value="Simpan" />
				            <input type="submit" name="reqSubmit" id="reqSubmit" value="Simpan" style="cursor:pointer" class="<?= CLASS_BTN_PRIMARY ?>" />
									</div>
								</form>
							<?php 
							} else {
							echo '<div class="col-md-12 alert alert-danger">Belum bisa melakukan penilaian karena status tagihan belum selesai</div>';
							}  ?>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
