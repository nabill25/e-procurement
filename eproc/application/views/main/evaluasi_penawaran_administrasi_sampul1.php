<?php
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 * 
 */

$reqId = $this->input->get("reqId");
$this->libsession->cekSession($reqId);

$this->load->library("paketinfo"); $paketInfo = new paketinfo();
include_once("functions/string.func.php");
include_once("functions/default.func.php");
include_once("functions/date.func.php");
$this->load->model("PaketEvaluasiAdminTawar");
$this->load->model("PaketRekanan");
$this->load->model("PaketDokumen");
$this->load->model("RekananEvaluasiAdminTawar");
$this->load->model(array("PaketTahap","Paketpemenang"));


$paket_tahap_metode = new PaketTahap();
$paket_tahap = new PaketTahap();

$paket_evaluasi_admin = new PaketEvaluasiAdminTawar();
$paket_evaluasi_admin2 = new PaketEvaluasiAdminTawar();
$paket_rekanan = new PaketRekanan();

$paketInfo->getPaket($reqId);
$reqNamaPaket = $paketInfo->nama;
$reqJenisPekerjaan = $paketInfo->jenis;
$reqMetodeEvaluasi = $paketInfo->metode_evaluasi;
$reqMetodeLelang = $paketInfo->metode_lelang_id;
$reqUUID = $paketInfo->uuid;

$paket_evaluasi_admin->selectByParams(array("PAKET_ID" => $reqId));
$paket_evaluasi_admin2->selectByParams(array("PAKET_ID" => $reqId));
$paket_rekanan->selectByParams2(array("PAKET_ID" => $reqId), -1, -1, " AND A.TANGGAL_DAFTAR IS NOT NULL AND A.LULUS_PENDAFTARAN = 1 AND A.LULUS_KUALIFIKASI = 1 AND A.KIRIM_PENAWARAN = 1 ");
while($paket_rekanan->nextRow())
{
	$arrRekananId[] = $paket_rekanan->getField("REKANAN_ID");
	$arrRekanan[] = $paket_rekanan->getField("REKANAN");
	$arrPaketRekananId[] = $paket_rekanan->getField("PAKET_REKANAN_ID");
	$arrPaketRekananNilai[] = $paket_rekanan->getField("NILAI_PENAWARAN");
	$arrPasswordDokumen[] = $paket_rekanan->getField("KIRIM_PENAWARAN_PASSWORD");
}

if (is_array($arrRekanan)) {
	$arrRekananId = $arrRekananId;
	$arrRekanan = $arrRekanan;
	$arrPaketRekananId = $arrPaketRekananId;
	$arrPaketRekananNilai = $arrPaketRekananNilai;
	$arrPasswordDokumen = $arrPasswordDokumen;
} else {
	$arrRekananId = array();
	$arrRekanan = array();
	$arrPaketRekananId = array();
	$arrPaketRekananNilai = array();
	$arrPasswordDokumen = array();
}

$jenis_tahap = $paket_tahap_metode->getJenisTahapById($reqId);
$arrPembukaanAuction            = PEMBUKAAN_AUCTION;

if($paket_tahap->getCountByParams(array("URUT" => $arrPembukaanAuction[$jenis_tahap], "PAKET_ID" => $reqId), " AND NOW() >= TANGGAL_AWAL ") > 0)
	$allowPassword = 1;
else
{
	$allowPassword = 0;
}
// echo $paket_tahap->query(); die();


$arrEvaluasiPenawaranFile1 = EVALUASI_PANAWARAN;

$aktif_evaluasi_penawaran1 = $paket_tahap->getCountByParamsAktif(array("URUT" => $arrEvaluasiPenawaranFile1[$jenis_tahap], "PAKET_ID" => $reqId, "TAMPILKAN" => 1));
$aktif_evaluasi_penawaran12 = $paket_tahap->getCountByParamsBerlalu(array("URUT" => $arrEvaluasiPenawaranFile1[$jenis_tahap], "PAKET_ID" => $reqId, "TAMPILKAN" => 1));
 
if($aktif_evaluasi_penawaran1 > 0 || $aktif_evaluasi_penawaran12 > 0 )
{
  $cekAktif = 1;
} else {
  $cekAktif = 0;
} 

$getpaket_pemenang = new Paketpemenang();
$getpaket_pemenang->selectByParams(array("PAKET_ID" => $reqId), -1, -1);
$getpaket_pemenang->firstRow();
if ($getpaket_pemenang->getField("PUBLISH") == '1') {
  $formReadOnly = 'readonly';
  $formDisable = 'disabled';
} else {
  $formReadOnly = '';
  $formDisable = '';
}

?>
<script type="text/javascript">
$(function(){
	$('#ffUpload').form({
		url:'dokumen_pengadaan_upload_rekanan/upload_evaluasi',
		onSubmit:function(){
			if($(this).form('validate'))
			{
			var win = $.messager.progress({
										title:'Upload Data',
										msg:'Mengupload data...'
									});
			}
			else
				$('input:file').MultiFile('reset');
			return $(this).form('validate');
		},
		success:function(data){
			if (data === 'Dokumen berhasil diupload.') { alertSuccess2(data); 
      } else {
        alertError2(data);
      }
      $.messager.progress('close');
      setTimeout(function() {
        document.location.reload();
      }, 2000);
		}
	});

	$('#ff').form({
		url:'rekanan_evaluasi_admin_tawar_json/evaluasi_penawaran',
		onSubmit:function(){
			return $(this).form('validate');
		},
		success:function(data){
			// $.messager.alert('Info', data, 'info');
      alertSuccess2(data);
			$('#reqTextSimpan').html('<i class="fa fa-check-square-o"></i> Update');
		}
	});
});
function myFunction(a) {
    var id = "myPass"+a;
    var copyText = document.getElementById(id);
    copyText.select();
    copyText.setSelectionRange(0, 99999)
    document.execCommand("copy");
    // alert("Copied the text: " + copyText.value);
    alertSuccess2("Password disalin "+copyText.value);
  }
</script>

<style type="text/css">
  table th {
    background-color: #967adc;
    color: #fff;
  }
</style>

<div class="row">
  <div class="col-md-12 col-sm-12">
    <div class="card">
      <div class="card-header card-head-inverse bg-primary">
        <h4 class="card-title text-white">Evaluasi Administrasi</h4>
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
        <?php
        if ($cekAktif == 0) { ?>
        <div class="alert alert-danger" style="color:#fff">
          <span style="color: #fff">
            Evaluasi Penawaran belum mulai.
          </span>
        </div>
        <?php
        }
        else 
        { ?>
          <div class="row">
            <div class="form-group col-md-12 mb-2">
              <div class="col-md-12">
                <ul class="nav nav-tabs process-model more-icon-preocess" role="tablist">
                  <li role="presentation" class="active" style="width: 33% !important;"><a href="main/index/evaluasi_penawaran_administrasi_sampul1/?reqId=<?=$reqId?>"><i class="fa fa-check" aria-hidden="true"></i>
                    <p>Evaluasi Administrasi</p>
                    </a></li>
                  <li role="presentation" style="width: 33% !important;"><a href="main/index/evaluasi_penawaran_teknis_sampul1/?reqId=<?=$reqId?>" ><i class="fa fa-pencil" aria-hidden="true"></i>
                    <p>Evaluasi Teknis</p>
                    </a></li>
                  <li role="presentation" style="width: 33% !important;"><a href="main/index/evaluasi_penawaran_rekapitulasi_sampul1/?reqId=<?=$reqId?>"><i class="fa fa-list-alt" aria-hidden="true"></i>
                    <p>Rekapitulasi</p>
                    </a></li>
                </ul>
              </div>
              <!-- <a href="main/index/evaluasi_penawaran_administrasi_sampul1/?reqId=<?=$reqId?>" class="btn btn-primary disabled"> <span class="fa fa-pencil-square-o"></span> Evaluasi Administrasi</a>
              <a href="main/index/evaluasi_penawaran_teknis_sampul1/?reqId=<?=$reqId?>" class="btn btn-primary"><span class="fa fa-pencil"></span> Evaluasi Teknis</a>
              <a href="main/index/evaluasi_penawaran_rekapitulasi_sampul1/?reqId=<?=$reqId?>" class="btn btn-primary"><span class="fa fa-list-alt"></span> Rekapitulasi</a> -->
            </div>
          </div>
          <div class="table-responsive">
            <table class="table table-bordered table-hover">
              <tr>
                <td width="30%"> Pekerjaan </td>
                <td> <?=$reqNamaPaket?> </td>
              </tr>
              <tr>
                <td width="30%"> Jenis Pekerjaan</td>
                <td> <?=$reqJenisPekerjaan?> </td>
              </tr>
                <tr>
                  <td width="30%"> Metode Evaluasi</td>
                  <td> <?=$reqMetodeEvaluasi?> </td>
              </tr>
              <?php
                $this->load->model("Masterdokumentemplate");
                $master_dokumen = new Masterdokumentemplate();
                $master_dokumen->selectByParams(array('B.NAMA' => 'Dokumen Template Evaluasi Admin'));
                if ($master_dokumen->countRow() > 0) {
                  $master_dokumen->firstRow();
                 ?>
                <tr>
                  <td>Template BA Evaluasi Administrasi</td>
                  <td>
                    <a href="uploads/template/<?=$master_dokumen->getField('PATH_FILE')?>" target="_blank" class="btn-sm btn-success round">
                      <?= ICON_DOWNLOAD ?> <small>Download Template</small></a>
                  </td>
                </tr>
                <?php
                } ?>
              <tr>
                <td width="30%">Upload BA Evaluasi Administrasi</td>
                <td>
                    <form id="ffUpload" method="post" novalidate enctype="multipart/form-data">
                    <input name="reqLinkFile" type="file" multiple class="maxsize-20240" accept="xls|xlsx|pdf" id="reqLinkFile" value=""/>
                    <br><?= UPLOAD_XLS_XLSX_PDF_2MB ?>
                    <script>
                    // wait for document to load
                    $( "#reqLinkFile" ).bind( "change", function() {
                        document.querySelector('#reqSubmit').click();
                      });
                    </script>
                    <input type="hidden" name="reqId" value="<?=$reqId?>" />
                    <input type="hidden" name="reqNamaDokumen" id="reqNamaDokumen" value="Evaluasi penawaran administrasi" />
                    <input type="hidden" name="reqJenisDokumen" id="reqJenisDokumen" value="EVALUASI_PENAWARAN_ADMINISTRASI" />
                    <input type="submit" name="reqSubmit" id="reqSubmit" value="" style="display:none">
                    </form>
                </td>
              </tr>
              <tr>
                <?php
                $paket_dokumen = new PaketDokumen();
                $paket_dokumen->selectByParams(array("PAKET_ID" => $reqId, "JENIS_DOKUMEN" => "EVALUASI_PENAWARAN_ADMINISTRASI"));
                $paket_dokumen->firstRow();
                $dokumen = $paket_dokumen->getField("PATH_FILE");
                if($dokumen == "")
                {}
                else
                {
                ?>
                <td>Download BA Evaluasi Administrasi</td>
                <td>
                  <a href="uploads/penawaran/<?=$dokumen?>" target="_blank" class="btn-sm btn-success round">
                    <?= ICON_DOWNLOAD ?> Download
                  </a>
                </td>
                <?php
                }
                ?>
              </tr>
            </table>
            <!-- <div class="alert alert-info">Evaluasi Data Administrasi</div> -->
            <form id="ff" method="post" novalidate enctype="multipart/form-data">
              <table class="table table-bordered table-hover">
                  <tr>
                    <th width="2%">No</th>
                    <th width="30%">
                      <?php
                      if ($reqMetodeLelang == 1 || $reqMetodeLelang == 7 || $reqMetodeLelang == 10) { // tender & tender cepat
                         echo "Nama Peserta";
                      } else {
                        echo "Nama Penyedia";
                      }?>
                    </th>
                    <?php
                    while($paket_evaluasi_admin->nextRow())
                    {
                      // $pecahspasi = str_word_count($paket_evaluasi_admin->getField("NAMA"));
                      // $gabungkata = '';
                      // $PecahStr = explode(" ", $paket_evaluasi_admin->getField("NAMA"));
                      // for ( $i = 0; $i < $pecahspasi; $i++ ) {
                      //   if (($i % 3) == 0) {
                      //     $gabungkata .= $PecahStr[$i].' <br>';
                      //   } else {
                      //     $gabungkata .= $PecahStr[$i].' ';
                      //   }
                      // }
                      // echo '<th width="10%">'.$paket_evaluasi_admin->getField("PAKET_EVAL_ADMIN_TAWAR_ID").'</th>';
                      // echo '<th width="10%">'.$gabungkata.'</th>';
                      $a[] = $paket_evaluasi_admin->getField("PAKET_EVAL_ADMIN_TAWAR_ID");
                      $b[] = $paket_evaluasi_admin->getField("NAMA");
                    }
                    ?>
                    <th width="35%" class="text-center">Hasil Evaluasi</th>
                    <!-- <th width="30%">Keterangan</th> -->
                  </tr>

                <?php
                if($allowPassword == 1)
                {
                  $no = 1;
                  $check = 0;
									$countStatusMemenuhiSyarat = 0;
                  for($i=0;$i<count($arrRekanan);$i++)
                  {
                  ?>
                    <tr>
                      <td widtd="10px"><?= $no ?></td>
                      <td>
                        <?php
                          echo '<b>'.$arrRekanan[$i].'<b><br>';
                        if($allowPassword == 1) {
                          $password =  $arrPasswordDokumen[$i];
                          echo '<a onClick="openAdd(\'main/loadUrl/main/evaluasi_penawaran_dokumen_popup/?reqId='.$reqId.'&rekanan='.$arrRekananId[$i].'&file='.$reqMetodePenyempaian.'&tahap=admin\');"> <small class="badge badge-danger" style="margin-top:1%"> <i class="fa fa-folder-open-o"></i>  lihat dokumen</small>';
                        }
                            // <a onClick="return myFunction(\''.$arrRekanan[$i].'\')">
                            // <div class="input-group" style="margin-top:1%">
                            //   <div class="input-group-prepend">
                            //     <i class="fa fa-copy"></i> &nbsp;&nbsp;
                            //   </div>
                            //   <input class="form-control" type="text" value="'.$password.'" id="myPass'.$arrRekanan[$i].'" style="border:none; height:10px; cursor:copy; font-size:11px" readonly>
                            // </div>
                            // </a>
                        ?>
                      </td>
                      <?php
                      for ($j=0; $j < count($a) ; $j++) {
                        $status = "";
                        $uraian = "";
                        $rekanan_evaluasi_admin_tawar = new RekananEvaluasiAdminTawar();
                        $rekanan_evaluasi_admin_tawar->selectByParams(array("PAKET_REKANAN_ID" => $arrPaketRekananId[$i], "PAKET_EVAL_ADMIN_TAWAR_ID" => $a[$j]));
                        $rekanan_evaluasi_admin_tawar->firstRow();
                        $status = $rekanan_evaluasi_admin_tawar->getField("MEMENUHI_SYARAT");
                        $uraian = $rekanan_evaluasi_admin_tawar->getField("URAIAN");
                        $keterangan = $rekanan_evaluasi_admin_tawar->getField("KETERANGAN");

												// Login untuk tombol Simpan dan Update
												if($rekanan_evaluasi_admin_tawar->countRow() > 0) {
													$countStatusMemenuhiSyarat++;
												}

                        $paket_dokumen = new PaketDokumen();
                        $paket_dokumen->selectByParams(array("PAKET_ID" => $reqId, "REKANAN_USER_ID" => $arrRekananId[$i], "JENIS_DOKUMEN" => "PENAWARAN_ADMIN", "TRIM(NAMA)" => trim($b[$j])));
                        $paket_dokumen->firstRow();
                        $dokumen = $paket_dokumen->getField("PATH_FILE");
                        // if($allowPassword == 1)
                        //   $password =  $arrPasswordDokumen[$i];
                      ?>
                      <!-- <td align="center"> -->
                        <?php
                        // if($dokumen == "")
                        // {
                        ?>
                          <!-- <img src="images/icon-hapus.png"> -->
                        <?php
                        // }
                        // else
                        // {
                        ?>
                        <!-- <a href="uploads/penawaran/<?=$dokumen?>" target="_blank"><img src="images/icon-download.png"></a> -->
                        <?php
                        // }
                        ?>
                      <!-- </td> -->
                      <?php
                      } ?>
                      <td style="font-size: 12px">
                        <input type="hidden" name="reqPaketRekananId[]" value="<?=$arrPaketRekananId[$i]?>" />
                        <input type="hidden" name="reqPaketEvaluasiId[]" value="<?=$paket_evaluasi_admin->getField("PAKET_EVAL_ADMIN_TAWAR_ID")?>" />
                        <input type="hidden" name="reqEvaluasiAdminSyarat[]" id="reqEvaluasiAdminSyarat<?=$check?>" value="<?=$status?>" />
                        <input type="radio" name="reqPenilaian<?=$check?>" value="1" onClick="$('#reqEvaluasiAdminSyarat<?=$check?>').val('1'); $('#reqUraian<?=$check?>').val('');  $('#reqUraian<?=$check?>').hide(); $('#reqKeterangan<?=$check?>').show()" <?php if($status == "1") { ?> checked <?php } ?> <?= $formDisable ?>> Memenuhi Syarat &nbsp;&nbsp;
                        <input type="radio" name="reqPenilaian<?=$check?>" value="0" onClick="$('#reqEvaluasiAdminSyarat<?=$check?>').val('0'); $('#reqUraian<?=$check?>').val('');  $('#reqUraian<?=$check?>').show(); $('#reqKeterangan<?=$check?>').hide()" <?php if($status == "0") { ?> checked <?php } ?> <?= $formDisable ?>> Tidak Memenuhi Syarat
                       <br><br>
                      <!-- </td>
                      <td> -->
                        <textarea name="reqUraian[]" class="form-control" id="reqUraian<?=$check?>" <?php if($status == "1" || $status == "") { ?> style="display:none;" <?php } ?> placeholder="alasan tidak memenuhi syarat.." <?= $formReadOnly ?>><?=$uraian?></textarea>
                        <textarea name="reqKeterangan[]" <?php if($status == "0") { ?> style="display:none;" <?php } ?> class="form-control" id="reqKeterangan<?=$check?>" placeholder="keterangan tambahan.." <?= $formReadOnly ?>><?=$keterangan?></textarea>
                        <!-- <textarea class="form-control" name="reqUraian[]" id="reqUraian<?=$check?>" <?php if($status == "1" || $status == "") { ?> style="display:none; margin-top: 4px" <?php } ?> placeholder="alasan tidak sesuai..."><?=$uraian?></textarea>
                        <textarea class="form-control" name="reqKeterangan[]" id="reqKeterangan<?=$check?>" placeholder="keterangan tambahan.."><?=$keterangan?></textarea> -->
                      </td>
                    </tr>
                  <?php
                  $no++;
                  $check++;
                  }
                  unset($paket_evaluasi_admin);
                  unset($paket_dokumen);
                }  // end of  if($allowPassword == 1)
                ?>

              </table>

              <div class="form-actions">
                <input type="hidden" name="reqId" value="<?=$reqId?>" />
                <input type="hidden" name="submitSimpan" value="Simpan" />
                <a href="main/index/paket_detil/?eid=<?=$reqId?>&key=<?=$reqUUID?>" class="<?= CLASS_BTN_DANGER ?> mr-1"> <?= BTN_KEMBALI ?> </a>
                <!-- <button type="submit" name="reqSimpan" id="reqSimpan" class="btn btn-primary"><?= BTN_SIMPAN ?></button> -->
                <?php 
                if ($getpaket_pemenang->getField("PUBLISH") == '1') { } else { ?>
								<button type="submit" name="reqSimpan" id="reqSimpan" class="<?= CLASS_BTN_PRIMARY ?>"> <?php if($countStatusMemenuhiSyarat > 0 ) { echo '<span id="reqTextSimpan"> <i class="fa fa-check-square-o"></i> Update </span>'; } else { echo '<span id="reqTextSimpan"><i class="fa fa-check-square-o" id="reqTextSimpan"></i> Simpan</span>'; } ?></button>
                <?php 
                } ?>

                <a href="main/index/evaluasi_penawaran_rekapitulasi_sampul2/?reqId=<?=$reqId?>" class="<?= CLASS_BTN_INFO ?> mr-1 pull-right"> <span class="fa fa-refresh"></span> Update Rekapitulasi  </a>
                
                <?php /*?><input type="button" onclick="windowOpenerPopup(350,450,'Cetak Close','main/loadUrl/main/cetak_penawaran_teknis/?reqId=<?=$reqId?>');" name="varCetak" id="varCetak" value="Cetak" class="btn-cetak"/><?php */?>
              </div>
            </form>
          </div>
        <?php 
        } ?>
        </div>
      </div>
    </div>
  </div>
</div>
