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
$this->load->model("PaketEvaluasiTeknisTawar");
$this->load->model("PaketEvaluasiHargaTawar");
$this->load->model("PaketRekanan");
$this->load->model("PaketDokumen");
$this->load->model("RekananEvaluasiAdminTawar");
$this->load->model("RekananEvaluasiTeknisTawar");
$this->load->model("RekananEvaluasiHargaTawar");
$this->load->model("Paket");
$this->load->model("PaketTahap");
$this->load->model("PaketPembukaanValidasi");

$paket_evaluasi_admin = new PaketEvaluasiAdminTawar();
$paket_evaluasi_teknis = new PaketEvaluasiTeknisTawar();
$paket_evaluasi_harga = new PaketEvaluasiHargaTawar();
$paket_rekanan = new PaketRekanan();
$paket_nilai = new Paket();
$paket_nilai_estimate = new Paket();
$paket_tahap_metode = new PaketTahap();
$paket_tahap = new PaketTahap();
$paket_pembukaan_validasi = new PaketPembukaanValidasi();


$paketInfo->getPaket($reqId);
$reqNamaPaket = $paketInfo->nama;
$reqSistemSampul = $paketInfo->sistem_sampul;
$reqMetodeEvaluasiId = $paketInfo->metode_lelang_id;
$reqUUID = $paketInfo->uuid;

$paket_evaluasi_admin->selectByParams(array("PAKET_ID" => $reqId));
$paket_evaluasi_teknis->selectByParams(array("PAKET_ID" => $reqId));
$paket_evaluasi_harga->selectByParams(array("PAKET_ID" => $reqId));
$paket_rekanan->selectByParams(array("PAKET_ID" => $reqId), -1, -1, " AND A.TANGGAL_DAFTAR IS NOT NULL AND A.LULUS_PENDAFTARAN = 1 AND A.LULUS_KUALIFIKASI = 1 AND A.KIRIM_PENAWARAN = 1 ");
while($paket_rekanan->nextRow())
{
	$arrRekanan[] = $paket_rekanan->getField("REKANAN");
	$arrRekananId[] = $paket_rekanan->getField("REKANAN_ID");
	$arrPaketRekananId[] = $paket_rekanan->getField("PAKET_REKANAN_ID");
  // $arrPaketRekananNilai[] = $paket_rekanan->getField("NILAI_PENAWARAN");
	$arrPaketRekananNilai[] = $paket_rekanan->getField("JUMLAH");
	$arrPaketRekananNilaiSebelumnya[] = $paket_rekanan->getField("NILAI_PENAWARAN_SEBELUMNYA");
	$arrRekananHadirPembukaan[] = $paket_rekanan->getField("HADIR_PEMBUKAAN_PENAWARAN");
	$arrPasswordDokumen[] = $paket_rekanan->getField("KIRIM_PENAWARAN_PASSWORD");
}

if (is_array($arrRekanan)) {
	$arrRekanan = $arrRekanan;
	$arrRekananId = $arrRekananId;
	$arrPaketRekananId = $arrPaketRekananId;
	$arrPaketRekananNilai = $arrPaketRekananNilai;
	$arrPaketRekananNilaiSebelumnya = $arrPaketRekananNilaiSebelumnya;
	$arrRekananHadirPembukaan = $arrRekananHadirPembukaan;
	$arrPasswordDokumen = $arrPasswordDokumen;
} else {
	$arrRekanan = array();
	$arrRekananId = array();
	$arrPaketRekananId = array();
	$arrPaketRekananNilai = array();
	$arrPaketRekananNilaiSebelumnya = array();
	$arrRekananHadirPembukaan = array();
	$arrPasswordDokumen = array();
}

$paket_nilai->selectByParams(array("PAKET_ID" => $reqId));
$paket_nilai->firstRow();
$reqNilaiEstimate = $paket_nilai->getField("NILAI_OWNER_ESTIMATE");

$jenis_tahap = $paket_tahap_metode->getJenisTahapById($reqId);
$arrPembukaanAuction            = PEMBUKAAN_AUCTION;


$aktif_pembukaan = $paket_tahap->getCountByParamsAktif(array("URUT" => $arrPembukaanAuction[$jenis_tahap], "PAKET_ID" => $reqId, "TAMPILKAN" => 1));
$aktif_pembukaan2 = $paket_tahap->getCountByParamsBerlalu(array("URUT" => $arrPembukaanAuction[$jenis_tahap], "PAKET_ID" => $reqId, "TAMPILKAN" => 1));

if($aktif_pembukaan  > 0 || $aktif_pembukaan2  > 0) {
	$info = "1";
}
else
{
	$info = "0";//"Password akan terbuka pada : ".$paket_tahap->getField("JAM_BUKA");
}

// echo $info; die();
$paket_pembukaan_validasi->selectByParamsValidasi(array("NIP" => $this->NIP, "A.PAKET_ID" => $reqId));
$paket_pembukaan_validasi->firstRow();

//if($paket_pembukaan_validasi->getField("JENIS") == "")
	//exit;

if($reqSistemSampul == "2")
	exit;
?>

<script type="text/javascript">
$(function(){
    $('#ff').form({
        url:'paket_rekanan_json/nilai_penawaran',
        onSubmit:function(){
            return $(this).form('validate');
        },
        success:function(data){
            $.messager.alert('Info', data, 'info');
        }
    });


	$('#ffUpload').form({
		url:'dokumen_pengadaan_upload_rekanan/upload_surat_penawaran',
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
			alert(data);
			$.messager.progress('close');
			document.location.reload();
		}
	});

});

function publishPembukaan()
{
	$.messager.confirm("Konfirmasi","Publish hasil pembukaan penawaran ?",function(r){
		if (r){
			$.get( "paket_json/set_publish_pembukaan/?reqId=<?=$reqId?>", function( data ) {
				  if(data == "1")
				  {
					  $("#btnPublish").css("display", "none");
					  $.messager.alert('Info', "Publish pembukaan penawaran berhasil.", 'info');
				  }
				  else
					  $.messager.alert('Info', data, 'info');
			});
		}
	});
}

/*function publishPembukaanPenawaranUlang()
{
	$.messager.confirm("Konfirmasi","Publish hasil, batalkan paket dan buat paket baru ?",function(r){
		if (r){
			$.getJSON('json/setPublishPembukaan/ulang/?reqId=<?=$reqId?>', function (data)
			{
			  if(data.STATUS == "1")
			  {
				  $("#btnPublish").css("display", "none");
				  alert("Publish pembukaan penawaran berhasil, paket baru telah dibuat.");
				  document.location.reload();
			  }
			  else
				  alert(data.STATUS);
			});
		}
	});

}*/

function submitValidasi(kode, jenis)
{
	$.messager.confirm("Konfirmasi","Validasi hasil pembukaan penawaran ?",function(r){
		if (r){
			$.get('paket_validasi_json/pembukaan/?reqId=<?=$reqId?>&reqKode='+kode+'&reqJenis='+jenis, function( data ) {
				$.messager.alert('Info', data, 'info');
				$("#tombolValidasi").css("display", "none");
			});
		}
	});

}

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

<div class="row">
  <div class="col-md-12 col-sm-12">
    <div class="card">
      <div class="card-header card-head-inverse bg-primary">
        <h4 class="card-title text-white">Pembukaan Penawaran Pengadaan</h4>
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
          if ($info == 0) { ?>
          <div class="alert alert-danger" style="color:#fff">
            <span style="color: #fff">
              Pembukaan Penawaran Pengadaan belum mulai.
            </span>
          </div>
          <?php
          } else 
          { ?>
            <div class="table-responsive">
              <table class="table table-bordered">
                <tr class="judul-kolom">
                  <th width="2%">No.</th>
                  <th style="width: 28%">Uraian</th>
                  <?php
                  for($i=0;$i<count($arrRekanan);$i++)
                  {
                  ?>
                    <th class="alert" style="font-size: 15px; font-weight: bold; text-align: center">
                      <?=$arrRekanan[$i]?> <br>
                      <?php
                      if ($info == 0) { } else { ?>
                      <a href="<?= base_url('evaluasi_download_json/aanwijzing_publish_json?reqId='.$reqId.'&rekanan='.$arrRekananId[$i].'&file=all&tahap=all') ?>" class="btn btn-sm round btn-min-width box-shadow-1 btn-success text-white" target="_blank"><span class="fa fa-download"></span> Download semua dokumen</a>
                      <?php
                      } ?>
                    </th>
                  <?php
                  }
                  ?>
                  <tr class="gelap">
                    <th colspan="2">Password</th>
                    <?php
                      for($i=0;$i<count($arrRekanan);$i++)
                      {
                      ?>
                      <th align="center" style="text-align: center">
                        <?php
                        if ($arrPasswordDokumen[$i]) {
                          echo  '<a onClick="return myFunction(\''.$arrRekanan[$i].'\')">
                                <div class="text-center" style="margin-top:1%">
                                    <i class="fa fa-copy"></i> &nbsp;&nbsp; Copy Password
                                  <input type="text" value="'.$arrPasswordDokumen[$i].'" id="myPass'.$arrRekanan[$i].'" style="border:none; height:10px; width:5px !important; cursor:copy;" readonly>
                                </div>
                                </a>';
                        } else {
                          echo "Enkripsi Penawaran tidak di upload";
                        }
                                // <input class="form-control" type="text" value="'.$arrPasswordDokumen[$i].'" id="myPass'.$arrRekanan[$i].'" style="border:none; height:10px; cursor:copy;" readonly>
                        ?>
                      </th>
                      <?php
                      }
                   ?>
                  </tr>

                  <?php
                  if ($reqMetodeEvaluasiId != 7 ) { // selain tender cepat
                  ?>
                    <!-- DATA ADMINISTRASI  -->
                    <tr class="gelap">
                      <td colspan="<?=2+(count($arrRekanan))?>"><strong>DOKUMEN ADMINISTRASI</strong></td>
                    </tr>
                    <?php
                    $i = 1;
                    $check = 0;
                    $style="gelap";

                    //set up 31-10-2012
                    $total_administrasi=$data_administrasi='';

                    while($paket_evaluasi_admin->nextRow())
                    {
                    ?>
                      <tr class="terang">
                        <td style="width: 4%" align="center"><?=$i?>.</div></td>
                        <td style="width: 28%">
                          <?=$paket_evaluasi_admin->getField("NAMA")?> <?php if($paket_evaluasi_admin->getField("WAJIB") == '1'){ ?><font color="#FF0000">* </font><?php } ?>
                        </td>
                        <?php
                        for($j=0;$j<count($arrRekanan);$j++)
                        {
                          //set up 16-10-2012
                          $paket_dokumen = new PaketDokumen();
                          $paket_dokumen->selectByParams(array("PAKET_ID" => $reqId, "REKANAN_USER_ID" => $arrRekananId[$j], "TRIM(NAMA)" => trim($paket_evaluasi_admin->getField("NAMA"))));
                          $paket_dokumen->firstRow();
                          ?>
                          <td align="center">
                            <div class="data-rekanan">
                            <?php
                            if($paket_dokumen->getField("PATH_FILE") == "")
                            {
                              echo '<img src="images/uncentang.png">';
                            }
                            else
                            {
                              if($info == "0")
                                echo "-";
                              else
                              {
                              ?>
                                <a href="uploads/penawaran/<?=$paket_dokumen->getField("PATH_FILE")?>" target="_blank"> <?= ICON_DOWNLOAD ?>
                                </a>
                              <?php
                              }
                            }
                            unset($paket_dokumen);
                            ?>

                            </div>
                          </td>
                          <?php
                           $check++;
                        }
                        ?>
                      </tr>
                    <?php
                      $i++;
                    }
                    ?>

                    <!-- DATA TEKNIS -->
                    <tr class="gelap">
                      <td colspan="<?=2+(count($arrRekanan))?>"><strong> DOKUMEN TEKNIS</strong></td>
                    </tr>
                    <?php
                    $i = 1;
                    $check = 0;
                    $style="gelap";

                    //set up 31-10-2012
                    $total_teknis=$data_teknis;

                    while($paket_evaluasi_teknis->nextRow())
                    {
                    ?>
                      <tr class="terang">
                        <td align="center"><?=$i?>.</td>
                        <td>
                          <?=$paket_evaluasi_teknis->getField("NAMA")?> <?php if($paket_evaluasi_teknis->getField("WAJIB") == '1'){ ?> <font color="#FF0000">* </font><?php } ?>
                        </td>
                        <?php
                        for($j=0;$j<count($arrRekanan);$j++)
                        {
                          //set up 16-10-2012
                          $paket_dokumen = new PaketDokumen();
                          $paket_dokumen->selectByParams(array("PAKET_ID" => $reqId, "REKANAN_USER_ID" => $arrRekananId[$j], "TRIM(NAMA)" => trim($paket_evaluasi_teknis->getField("NAMA"))));
                          $paket_dokumen->firstRow();
                          ?>
                          <td align="center">
                            <?php
                            if($paket_dokumen->getField("PATH_FILE") == "")
                            {
                              echo '<img src="images/uncentang.png">';
                            }
                            else
                            {
                              if($info == "0")
                                echo "-";
                              else
                              {
                              ?>
                                <a href="uploads/penawaran/<?=$paket_dokumen->getField("PATH_FILE")?>" target="_blank">
                                  <?= ICON_DOWNLOAD  ?>
                                </a>
                              <?php
                              }
                            }
                            unset($paket_dokumen);

                            ?>
                          </td>
                          <?php
                           $check++;
                        }
                        ?>
                      </tr>
                    <?php
                      $i++;
                    }
                    ?>
                  <?php
                  } // end of if ($reqMetodeEvaluasiId != 7 ) { // selain tender cepat
                   ?>

                  <!-- DATA HARGA -->
                  <tr class="gelap">
                    <td colspan="<?=2+(count($arrRekanan))?>"><strong> DOKUMEN HARGA</strong></td>
                  </tr>
                  <?php
                  $i = 1;
                  $check = 0;
                  $style="gelap";

                  //set up 31-10-2012
                  $total_harga=$data_harga;

                  while($paket_evaluasi_harga->nextRow())
                  {
                  ?>
                    <tr class="terang">
                      <td align="center"><?=$i?>.</td>
                      <td>
                        <?=$paket_evaluasi_harga->getField("NAMA")?> <?php if($paket_evaluasi_harga->getField("WAJIB") == '1'){ ?> <font color="#FF0000">* </font><?php } ?>
                      </td>
                      <?php
                      for($j=0;$j<count($arrRekanan);$j++)
                      {
                        //set up 16-10-2012
                        $paket_dokumen = new PaketDokumen();
                        $paket_dokumen->selectByParams(array("PAKET_ID" => $reqId, "REKANAN_USER_ID" => $arrRekananId[$j], "TRIM(NAMA)" => trim($paket_evaluasi_harga->getField("NAMA"))));
                        $paket_dokumen->firstRow();
                        ?>
                        <td align="center">
                        <?php
                        if($paket_dokumen->getField("PATH_FILE") == "")
                        {
                          echo '<img src="images/uncentang.png">';
                        }
                        else
                        {
                          if($info == "0")
                              echo "-";
                          else
                          {
                          ?>
                            <a href="uploads/penawaran/<?=$paket_dokumen->getField("PATH_FILE")?>" target="_blank">
                              <?= ICON_DOWNLOAD ?>
                            </a>
                          <?php
                          }
                        }
                        unset($paket_dokumen);
                        ?>
                        </td>
                        <?php
                         $check++;
                         unset($paket_dokumen);
                      }
                      ?>
                    </tr>
                  <?php
                    $i++;
                  }
                  ?>
              </table>

              <!-- PENAWARAN HARGA  -->
              <form id="ffUpload" method="post" novalidate enctype="multipart/form-data">
                <table class="table table-bordered">
                  <tr>
                    <td class="alert-info"><strong> PENAWARAN HARGA</strong></td>
                  </tr>
                    <?php /*?><tr class="gelap">
                      <td align="center"><div class="nomor">a.</div></td>
                      <td>
                        <div class="uraian">
                        Upload Surat Penawaran
                        </div>
                      </td>
                      <?php
                      for($j=0;$j<count($arrRekanan);$j++)
                      {
                      ?>
                          <td>
                            <div class="data-rekanan">

                            <span style="float:right; width:100%; text-align:center;">
                            <input name="reqLinkFile<?=$arrRekananId[$j]?>" type="file" class="maxsize-20240" accept="pdf" id="reqLinkFile<?=$arrRekananId[$j]?>" />
                            </span>
                            <script>
                            // wait for document to load
                            $(function(){

                                // invoke plugin
                                $('#reqLinkFile<?=$arrRekananId[$j]?>').MultiFile({
                                    onFileChange: function(){
                                        $("#reqRekananId").val("<?=$arrRekananId[$j]?>");
                                        $("#reqNamaDokumen").val("SURAT PENAWARAN <?=$arrRekanan[$j]?>");
                                        $("#reqJenisDokumen").val("PEMBUKAAN_PENAWARAN");
                                        $("#reqSubmit").click();
                                    }
                                });

                            });

                            </script>

                            <?php
                            $paket_dokumen = new PaketDokumen();
                            $paket_dokumen->selectByParams(array("PAKET_ID" => $reqId, "REKANAN_USER_ID" => $arrRekananId[$j], "JENIS_DOKUMEN" => 'PEMBUKAAN_PENAWARAN'));
                            $paket_dokumen->firstRow();
                            $file_penawaran_rekanan = $paket_dokumen->getField("PATH_FILE");
                            if($file_penawaran_rekanan == "")
                            {}
                            else
                            {
                            ?>
                            <div align="center">
                            <a href="uploads/penawaran/<?=$paket_dokumen->getField("PATH_FILE")?>" target="_blank">Dokumen penawaran <img src="images/icon-download.png" alt="" width="16" height="16" border="0" /></a>
                            </div>
                            <?php
                            }
                            unset($paket_dokumen);

                            ?>
                            </div>
                            </td>
                            <?php
                      }
                      ?>
                    </tr>  <?php */?>
                </table>
                <input type="hidden" name="reqRekananId" id="reqRekananId" value="" />
                <input type="hidden" name="reqNamaDokumen" id="reqNamaDokumen" value="" />
                <input type="hidden" name="reqJenisDokumen" id="reqJenisDokumen" value="" />
                <input type="hidden" name="reqId" value="<?=$reqId?>" />
                <input type="hidden" name="submitSimpan" value="Simpan" />
                <input type="submit" name="reqSubmit" id="reqSubmit" value="" style="display:none">
              </form>

              <form id="ff" method="post" novalidate enctype="multipart/form-data">
                <table class="table table-bordered">
                  <tr class="gelap">
                    <td align="center" style="width: 2%">a.</td>
                    <td colspan="2">Harga Perkiraan Sendiri</td>
                    <td style="" colspan="<?=count($arrRekanan)?>" id="reqNilaiEstimate" align="center">
                      <input type="text" name="reqNilaiEstimate" id="reqNilaiOE" value="<?=numberToIna($reqNilaiEstimate)?>" readonly class="form-control easyui-validatebox span2" />
                    </td>
                  </tr>
                  <?php
                  if ($info == 1)
                  { ?>
                  <tr class="terang">
                    <td align="center" style="width: 2%">b.</td>
                    <td colspan="2" style="width: 28%"><div class="uraian"> Nilai Penawaran </div></td>
                    <?php
                    $adaDiatasOE = 0;
                    for($j=0;$j<count($arrRekanan);$j++)
                    {
                    ?>
                      <td align="center">
                        <input type="hidden" name="reqPaketRekananId[]" value="<?=$arrPaketRekananId[$j]?>">
                        <input name="reqDataPenawaranHarga[]" class="form-control easyui-validatebox span2" type="text" id="reqDataPenawaranHarga<?=$j?>" value="<?= number_format($arrPaketRekananNilai[$j],2, ",",".")?>"  OnFocus="FormatAngka('reqDataPenawaranHarga<?=$j?>')" OnKeyUp="FormatUang('reqDataPenawaranHarga<?=$j?>')" OnBlur="FormatUang('reqDataPenawaranHarga<?=$j?>')" readonly="readonly"/>
                      </td>
                      <?php
                      if($arrPaketRekananNilai[$j] > $reqNilaiEstimate)
                        $adaDiatasOE++;
                      }
                      ?>
                  </tr>
                  <?php
                  } ?>

                  <?php
                  if($paketInfo->publish_ba_penawaran == "1" || $paketInfo->publish_ba_penawaran == "2")
                  {
                  ?>
                  <tr class="gelap">
                    <td colspan="3"><strong>CETAK HASIL PEMBUKAAN</strong></td>
                    <?php
                    for($i=0;$i<count($arrRekanan);$i++)
                    {
                    ?>
                    <td align="center">
                      <?php
                        if($arrRekananHadirPembukaan[$i] == "1")
                          $imgHadir = "images/centang.png";
                        else
                          $imgHadir = "images/delete-icon.png";
                        ?>
                        <img src="<?=$imgHadir?>">
                    </td>
                    <?php
                    }
                  }
                  ?>
                    <tr>
                      <td colspan="100">&nbsp;</td>
                    </tr>
                    <!--<tr>
                      <td></td>
                      <td colspan="3" align="right">

                      </td>
                    </tr>-->
                </table>

                <div class="form-actions">
                  <input type="hidden" name="reqId" value="<?=$reqId?>" />
                  <input type="hidden" name="submitSimpan" value="Simpan" />
                  <a href="main/index/paket_detil/?eid=<?=$reqId?>&key=<?= $reqUUID ?>" class="<?= CLASS_BTN_DANGER ?> mr-1"> <?= BTN_KEMBALI ?> </a>
                  <?php
                  if ($info == 1) { ?>
                    <a href="main/loadUrl/report/dokumen_pembukaan_penawaran_ba_pdf/?reqId=<?php echo $reqId; ?>" target="_blank" class="<?php echo CLASS_BTN_INFO; ?>"><?php echo BTN_PRINT; ?> Hasil Pembukaan</a>
                  <?php
                  }
                  if($paketInfo->publish_ba_penawaran == "1")
                  {}
                  else
                  {
                      if ($info == 0) { } else { ?>
                       <a onClick="publishPembukaan();" id="btnPublish" class="<?= CLASS_BTN_INFO ?>"><?= BTN_PUBLISH ?></a>
                      <?php
                    }
                  }
                  ?>
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
