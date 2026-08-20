<?php
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 *
 */

$this->libsession->cekSession('blockpenyedia');
// cek allowed url
if ($this->libsession->cekUrl($this->uri->segment(3, ""))) {} else { redirect(base_url()); }

/* INCLUDE FILE */
$this->load->library("kauth");  $userLogin = new kauth();
include_once("functions/string.func.php");
include_once("functions/date.func.php");
include_once("functions/default.func.php");
$this->load->model("Rekanan");
$this->load->model("RekananPengalaman");
$this->load->model("RekananPengalamanBidang");
$this->load->model("BidangUsaha");

ini_set('upload_max_filesize', '10M');
ini_set('post_max_size', '10M');
ini_set('max_input_time', 3000);
ini_set('max_execution_time', 3000);

/* create objects */
$rekanan = new Rekanan();
$rekanan_pengalaman	= new RekananPengalaman(); // tipe 0


$reqPengalamanId= httpFilterRequest('reqPengalamanId') ?: '0';
$reqId= httpFilterPost('reqId');
$reqBidangUsahaId = $_POST["reqBidangUsahaId"];
$reqNama= httpFilterPost('reqNama');
$reqLokasi= httpFilterPost('reqLokasi');
$reqTgsNama= httpFilterPost('reqTgsNama');
$reqTgsAlamat= httpFilterPost('reqTgsAlamat');
$reqKontrakNo= httpFilterPost('reqKontrakNo');
$reqTanggal= httpFilterPost('reqTanggal');
$reqKontrakNilai= httpFilterPost('reqKontrakNilai');
$reqJOpersen= httpFilterPost('reqJOpersen');
$reqJOket= httpFilterPost('reqJOket');
$reqStatus= httpFilterPost('reqStatus');
$reqSelesaiBA= httpFilterPost('reqSelesaiBA');
$reqProgress= httpFilterPost('reqProgress');
$reqProgressTanggal= httpFilterPost('reqProgressTanggal');
$reqLinkFile= $_FILES['reqLinkFile'];
$reqLinkFileTemp = httpFilterPost("reqLinkFileTemp");
$reqLinkFileTempTipe = httpFilterPost("reqLinkFileTempTipe");
$reqLinkFileTempUkuran = httpFilterPost("reqLinkFileTempUkuran");
$reqLinkFileBA= $_FILES['reqLinkFileBA'];
$reqLinkFileBATemp = httpFilterPost("reqLinkFileBATemp");
$reqLinkFileBATempTipe = httpFilterPost("reqLinkFileBATempTipe");
$reqLinkFileBATempUkuran = httpFilterPost("reqLinkFileBATempUkuran");

$reqId = $this->ID;

if($reqPengalamanId == "0")
{
	$reqStatus = '1';
  $reqProgress = 0;
}
else
{
	$rekanan_pengalaman->selectByParams(array("REKANAN_PENGALAMAN_ID" => $reqPengalamanId, "REKANAN_ID" => $this->ID));
	$rekanan_pengalaman->firstRow();
	//echo $rekanan_pengalaman->query;exit;

	$reqNama= $rekanan_pengalaman->getField("NAMA");
	$reqLokasi= $rekanan_pengalaman->getField("LOKASI");
	$reqTgsNama= $rekanan_pengalaman->getField("PEMBERI_TUGAS");
	$reqTgsAlamat= $rekanan_pengalaman->getField("PEMBERI_TUGAS_ALAMAT");
	$reqKontrakNo= $rekanan_pengalaman->getField("KONTRAK_NOMOR");
	$reqTanggal= dateToPageCheck($rekanan_pengalaman->getField("KONTRAK_TANGGAL"));
	$reqKontrakNilai= $rekanan_pengalaman->getField("KONTRAK_NILAI");
	$reqJOpersen= $rekanan_pengalaman->getField("KONTRAK_JO");
	$reqJOket= $rekanan_pengalaman->getField("KONTRAK_KETERANGAN");
	$reqStatus= $rekanan_pengalaman->getField("KONTRAK_STATUS");
	$reqProgress= $rekanan_pengalaman->getField("PROGRESS");
	$reqProgressTanggal= dateToPageCheck($rekanan_pengalaman->getField("PROGRESS_TANGGAL"));
	$reqSelesaiBA= dateToPageCheck($rekanan_pengalaman->getField("BA_TANGGAL"));
	$reqLinkFileTemp= $rekanan_pengalaman->getField("PATH_FILE");
	$reqLinkFileTempTipe= $rekanan_pengalaman->getField("TIPE");
	$reqLinkFileTempUkuran= $rekanan_pengalaman->getField("UKURAN");
	$reqLinkFileTempBA= $rekanan_pengalaman->getField("PATH_FILE_BA");
	$reqLinkFileTempTipeBA= $rekanan_pengalaman->getField("TIPE_BA");
	$reqLinkFileTempUkuranBA= $rekanan_pengalaman->getField("UKURAN_BA");
	$reqLinkFileTempNama= $rekanan_pengalaman->getField("NAMA_FILE");
  $reqLinkFileTempBANama= $rekanan_pengalaman->getField("NAMA_FILE_BA");
	$reqLinkFileTempBA= $rekanan_pengalaman->getField("PATH_FILE_BA");
	if($reqStatus == 1)	{$tmpNoneP = 'none';$tmpNoneS = '';}
	else					{$tmpNoneP = '';$tmpNoneS = 'none';}
}

if($reqPengalamanId=='0')
	$reqMode='insert';
else
	$reqMode='update';

?>
<script type="text/javascript">
$(document).ready(function() {

	$(function(){
		$('#ff').form({
			url:'rekanan_pengalaman_json/data_teknis_pengalaman_selesai_ubah',
			onSubmit:function(){
				var v=$(this).form('validate');
        if(v) {
            showLoad();
            return v;
        } else {
            hideLoad();
            return false;
        }
			},
      success:function(data){
        if (data == 'Data Gagal Tersimpan') {
          alertError3(data);
        } else {
          alertSuccess2('Data berhasil disimpan');
          setTimeout(function() {
            document.location.href = 'main/index/data_teknis_pengalaman';
          }, 2000);
        } 
			}
		});

	});

});

function createRowDokumen()
{
	$(function () {
		$.get("main/loadUrl/main/data_teknis_bidang_pekerjaan_template", function (data) {
			$("#dataTableTAPengalaman").append(data);
		});
	});
}

$(document).ready(function() {

  <?php
  if($reqPengalamanId=='0') {
  $reqMode='insert';
  ?>
    $('input:radio[name=reqStatus]').change(function() {
      if (this.value == '1') {
				$('#reqSelesaiBA').datebox({ required:true  });
        $('#reqLinkFilePDFSelesai').validatebox({ required:true  });
        $('#reqProgressWrap').hide();
        $('#txtTglBA').html('Tanggal Selesai');
        $('#txtFileBA').html('BA Selesai');
        $('#headerBA').html('Selesai');
      }
      else if (this.value == '2') {
        $('#reqSelesaiBA').datebox({ required:false  });
				$('#reqLinkFilePDFSelesai').validatebox({ required:false  });
        $('#reqProgressWrap').show();
        $('#txtTglBA').html('Tanggal Progress');
        $('#txtFileBA').html('BA Progress');
        $('#headerBA').html('Progres');
      }
    });

  <?php
  }
  else
  {
  $reqMode='update';
  ?>

    $('input:radio[name=reqStatus]').change(function() {
      if (this.value == '1') {
        $('#reqSelesaiBA').datebox({ required:true  });
        <?php
        if ($reqLinkFileTempBA == '')
        {?>
        $('#reqLinkFilePDFSelesai').validatebox({ required:true  });
        <?php
        } ?>
        $('#reqProgressWrap').hide();
        $('#txtTglBA').html('Tanggal Selesai');
        $('#txtFileBA').html('BA Selesai');
        $('#headerBA').html('Selesai');
      }
      else if (this.value == '2') {
        $('#reqSelesaiBA').datebox({ required:false  });
        $('#reqLinkFilePDFSelesai').validatebox({ required:false  });
        $('#reqProgressWrap').show();
        $('#txtTglBA').html('Tanggal Progress');
        $('#txtFileBA').html('BA Progress');
        $('#headerBA').html('Progres');
      }
    });

    <?php
    if($reqStatus == 1) { ?>
			$('#reqProgressWrap').hide();
        $('#txtTglBA').html('Tanggal Selesai');
        $('#txtFileBA').html('BA Selesai');
    <?php
    }
    ?>

    <?php
    if($reqStatus == 2) { ?>
      $('#reqSelesaiBA').datebox({ required:false  });
      $('#reqLinkFilePDFSelesai').validatebox({ required:false  });
			$('#reqProgressWrap').show();
      $('#txtTglBA').html('Tanggal Progress');
      $('#txtFileBA').html('BA Progress');
    <?php
    }
    ?>
  <?php
  }
  ?>

    $('#reqTanggal, #reqSelesaiBA').datebox({
      editable: false
    });

  });

</script>

<div class="row">
  <div class="col-md-12 col-sm-12">
    <div class="card">
      <div class="card-header card-head-inverse bg-primary">
        <h4 class="card-title text-white">Pengalaman Pekerjaan</h4>
        <a class="heading-elements-toggle"><i class="fa fa-ellipsis-v font-medium-3"></i></a>
        <div class="heading-elements">
            <ul class="list-inline mb-0">
              <li><a data-action="expand"><i class="ft-maximize"></i></a></li>
              <!-- <li><a data-action="close"><i class="ft-x"></i></a></li> -->
            </ul>
        </div>
      </div>

      <form id="ff" class="easyui-form form-horizontal" method="post" novalidate enctype="multipart/form-data">
      <div class="card-content collapse show border-info border-darken-2">
        <div class="card-body">

          <div class="card mb-1 border-blue border-darken-1">
            <div class="card-content">
              <div class="p-1">
                <div class="alert bg-primary alert-icon-left alert-arrow-left alert-dismissible mb-2" role="alert">
                  <span class="alert-icon"><i class="fa fa-th"></i></span>  <strong>Data Pengalaman
                  <?php
                  if ($this->REKANAN_TIPE_ID == '7') { // Perorangan
                    echo "";
                  } else {
                    echo "perusahaan";
                  }
                  ?>
                  </strong>
                </div>
                <div class="row">
                  <div class="form-group col-md-12 mb-2">
                    <label>Nama Paket Pekerjaan</label>
        		        <input name="reqNama" id="txtNama" size="50" value="<?=$reqNama?>" title="Nama paket pekerjaan harus diisi" required class="form-control easyui-validatebox span4"  maxlength="100" type="text" />
                  </div>
                </div>
                <div class="row">
                  <div class="form-group col-md-12 mb-2">
                    <label>Lokasi</label>
                    <input name="reqLokasi" id="txtLokasi" size="50" title="Lokasi harus diisi" required class="form-control easyui-validatebox span4" maxlength="100" type="text" value="<?=$reqLokasi?>" />
                  </div>
                </div>
                <hr>
                <div class="card mb-1 border-blue border-darken-1">
                  <div class="card-content">
                    <div class="p-1">
                      <div class="alert bg-primary alert-icon-left alert-arrow-left alert-dismissible mb-2" role="alert">
                        <span class="alert-icon"><i class="fa fa-th"></i></span>  <strong>Bidang
                          <?php
                            if ($this->REKANAN_TIPE_ID == '7') { // Perorangan
                              echo "Pekerjaan";
                            } else {
                              echo "Usaha";
                            }
                            ?> </strong>
                        <div class="badge badge-pill badge-warning">
                          <a id="btnAdd" onClick="openAdd('main/loadUrl/main/bidang_usaha_own');">
                            <span class="fa fa-plus text-white icon-line-height" data-toggle="tooltip" data-placement="top" title="Tambah Bidang Usaha"> Tambah</span>
                          </a>
                        </div>
                      </div>
                      <div class="table-responsive">
                        <table class="table table-bordered table mb-0">
                          <thead>
                            <tr class="judul-kolom">
                              <th>
                                Bidang
                                <?php
                                if ($this->REKANAN_TIPE_ID == '7') { // Perorangan
                                  echo "Pekerjaan";
                                } else {
                                  echo "Usaha";
                                }
                                ?>
                              </th>
                              <th width="5%">Aksi</th>
                            </tr>
                          </thead>
                          <tbody id="tbodyBidangUsaha">
                            <?php
                                $paketBidangUsaha = new RekananPengalamanBidang();
                                $paketBidangUsaha->selectByParams(array("REKANAN_PENGALAMAN_ID" => $reqPengalamanId));
                              if ($paketBidangUsaha->countRow() > 0) {

                                while($paketBidangUsaha->nextRow())
                                {
                                ?>
                                <tr>
                                  <!-- <td><?=$paketBidangUsaha->getField("BIDANG_USAHA_ID")?></td> -->
                                  <td><?=$paketBidangUsaha->getField("NAMA")?></td>
                                  <td><input type="hidden" name="reqBidangUsahaId[]" value="<?=$paketBidangUsaha->getField("BIDANG_USAHA_ID")?>" /><a title="#" onclick="$(this).parent().parent().remove();" class="btn-aksi"><i class="fa fa-trash" aria-hidden="true"></i></a></td>
                                </tr>
                                <?php
                                }
                              }
                            ?>
                          </tbody>
                        </table>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="card mb-1 border-blue border-darken-1">
            <div class="card-content">
              <div class="p-1">
                <div class="alert bg-primary alert-icon-left alert-arrow-left alert-dismissible mb-2" role="alert">
                  <span class="alert-icon"><i class="fa fa-th"></i></span>  <strong>Pemberi tugas / Pengguna Jasa</strong>
                </div>

                <div class="row">
                  <div class="form-group col-md-12 mb-2">
                    <label>Nama</label>
                    <input name="reqTgsNama" id="txtTgsNama" value="<?=$reqTgsNama?>" required title="Nama harus diisi" class="form-control easyui-validatebox span4" size="50" maxlength="100" type="text" />
                  </div>
                </div>
                <div class="row">
                  <div class="form-group col-md-12 mb-2">
                    <label>Alamat</label>
                    <input name="reqTgsAlamat" title="Alamat harus diisi" required value="<?=$reqTgsAlamat?>" class="form-control easyui-validatebox span4" id="txtTgsAlamat" size="50" maxlength="100" type="text" />
                  </div>
                </div>

              </div>
            </div>
          </div>

          <div class="card mb-1 border-blue border-darken-1">
            <div class="card-content">
              <div class="p-1">
                <div class="alert bg-primary alert-icon-left alert-arrow-left alert-dismissible mb-2" role="alert">
                  <span class="alert-icon"><i class="fa fa-th"></i></span>  <strong>Kontrak</strong>
                </div>

                <div class="row">
                  <div class="form-group col-md-12 mb-2">
                    <label>Nomor</label>
                    <input name="reqKontrakNo" id="txtKontrakNo" required title="No kontrak harus diisi" value="<?=$reqKontrakNo?>" class="form-control easyui-validatebox span4" size="50" maxlength="100" type="text" />
                  </div>
                </div>
                <div class="row">
                  <div class="form-group col-md-12 mb-2">
                    <label style="width: 100%">Tanggal</label>
                    <input type="text" required name="reqTanggal" id="reqTanggal" title="Tanggal kontrak harus diisi" class="form-control easyui-datebox span2" value="<?=$reqTanggal?>" style="width: 200% !important" />
                  </div>
                </div>
                <div class="row">
                  <div class="form-group col-md-12 mb-2">
                    <label>Nilai</label>
                    <input name="reqKontrakNilai" id="txtKontrakNilai" required title="Nilai kontrak harus diisi" class="form-control easyui-validatebox span4 " value="<?=$reqKontrakNilai?>" size="50" maxlength="100" type="text" OnFocus="FormatAngka('txtKontrakNilai')" OnKeyUp="FormatUang('txtKontrakNilai')" OnBlur="FormatUang('txtKontrakNilai')"/>
                  </div>
                </div>
                <?php
                if ($this->REKANAN_TIPE_ID == '7') { // Perorangan
                } else {
                ?>
               <!--  <div class="row">
                  <div class="form-group col-md-12 mb-2">
                    <label style="width: 100%">JO</label>
                    <input id="checkjo" onclick="countCheckedBayar('checkjo','reqJOpersen')" <?php if($reqJOpersen > 0) echo "checked"?> type="checkbox" />
                    <input name="reqJOpersen" onkeypress="return isNumberKey(event)" id="reqJOpersen" required disabled="disabled" value="<?=$reqJOpersen?>" size="3" maxlength="3" type="text" /> % <br>
                    <i><b>isi dengan 100 jika perusahan Anda bertindak sebagai kontraktor utama</b></i>
                  </div>
                </div>  -->
                <?php
                } ?>
                <div class="row">
                  <div class="form-group col-md-12 mb-2">
                    <label>Keterangan</label>
                    <textarea name="reqJOket" id="txtJOket" required class="form-control easyui-validatebox span4" cols="46" rows="2"><?=$reqJOket?></textarea>
                  </div>
                </div>
                <div class="row">
                  <div class="form-group col-md-12 mb-2">
                    <label style="width: 100%">Status</label>
                    <input value="1" <?php if($reqStatus == 1) echo 'checked'?> required name="reqStatus" id="rdstatus1" onclick="changePengalaman(this.value)" type="radio" /> Selesai &nbsp;
                    <input value="2" <?php if($reqStatus == 2) echo 'checked'?> required name="reqStatus" id="rdstatus2" onclick="changePengalaman(this.value)" type="radio" /> Dalam Progres
                  </div>
                </div>

                <div class="row">
                  <div class="form-group col-md-12 mb-2">
                    <label>File Kontrak <?= UPLOAD_PDF_2MB ?></label><br>
                    <input type="hidden" name="reqLinkFileTemp" value="<?=$reqLinkFileTemp?>" />
                    <input type="hidden" name="reqLinkFileTempTipe" value="<?=$reqLinkFileTempTipe?>" />
                    <input type="hidden" name="reqLinkFileTempUkuran" value="<?=$reqLinkFileTempUkuran?>" />
                    <input type="file" name="reqLinkFile" id="reqLinkFilePDF" size="30" <?php if($reqLinkFileTemp == "") { ?> required <?php } ?> class="easyui-validatebox"  validType="fileType['pdf']"  />
                    <input type="hidden" required name="reqLinkFileTempNama" value="<?=$reqLinkFileTempNama?>">
                    <?php
                    if ($reqLinkFileTempNama) {
                       echo '<br><a href="'.base_url('uploads/pengalaman/').$reqLinkFileTemp.'" target="_blank" class="badge badge-primary">Download file</a>';
                     } ?>
                  </div>
                </div>

              </div>
            </div>
          </div>

          <div class="card mb-1 border-blue border-darken-1">
            <div class="card-content">
              <div class="p-1">
                <div class="alert bg-primary alert-icon-left alert-arrow-left alert-dismissible mb-2" role="alert">
                  <span class="alert-icon"><i class="fa fa-th"></i></span>  <strong>Tanggal <span id="headerBA">Selesai</span></strong>
                </div>

                <div class="row">
                  <div class="form-group col-md-2 mb-2">
                    <label style="width: 100%" id="txtTglBA">Selesai BAST</label>
        		        <input type="text" name="reqSelesaiBA" required id="reqSelesaiBA" title="Selesai BAST harus diisi" class="form-control easyui-datebox span2" value="<?=$reqSelesaiBA?>" style="width:200% !important" />
                  </div>
                  <div class="form-group col-md-2 mb-2" id="reqProgressWrap" style="display: none">
                    <label style="width: 100%">Progress %</label>
                    <input name="reqProgress" id="reqProgress" value="<?=$reqProgress?>" onkeypress="return isNumberKey(event)" required title="Progress harus diisi" class="form-control easyui-validatebox span4" size="50" maxlength="3" type="text" />
                  </div>
                </div>
                <div class="row">
                  <div class="form-group col-md-12 mb-2">
                    <label id="txtFileBA">File BAST <?= UPLOAD_PDF_2MB ?> </label><br>
                    <input type="hidden" name="MAX_FILE_SIZE" value="100000000" />
                    <input type="hidden" name="reqLinkFileBATemp" value="<?=$reqLinkFileTempBA?>" />
                    <input type="hidden" name="reqLinkFileBATempTipe" value="<?=$reqLinkFileTempTipeBA?>" />
                    <input type="hidden" name="reqLinkFileBATempUkuran" value="<?=$reqLinkFileTempUkuranBA?>" />
                    <input type="file" name="reqLinkFileBA" id="reqLinkFilePDFSelesai" size="30" <?php if($reqLinkFileTempBA == "") { ?> required <?php } ?> class="easyui-validatebox"  validType="fileType['pdf']" />
                    <input type="hidden" name="reqLinkFileTempBANama" value="<?=$reqLinkFileTempBANama?>">
                     <?php
                      if ($reqLinkFileTempBANama) {
                         echo '<br><a href="'.base_url('uploads/pengalaman/').$reqLinkFileTempBA.'" target="_blank" class="badge badge-primary">Download file</a>';
                       } ?>
                  </div>
                </div>

              </div>
            </div>
          </div>

          <div class="form-actions">
            <input type="hidden" name="reqMode" value="<?=$reqMode?>" />
            <input type="hidden" name="reqPengalamanId" value="<?=$reqPengalamanId?>" />
            <a href="main/index/data_teknis_pengalaman" class="<?= CLASS_BTN_DANGER ?> mr-1"><?= BTN_KEMBALI ?></a>
            <button type="submit" class="<?= CLASS_BTN_PRIMARY ?>"><?= BTN_SIMPAN ?></button>
          </div>
        </div>
      </div>
      </form>

    </div>
  </div>
</div>
