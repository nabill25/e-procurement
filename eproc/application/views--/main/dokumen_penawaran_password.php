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

$this->load->model("PaketTahap");
$this->load->model("PaketRekanan");
$this->load->library("paketinfo"); $paketInfo = new paketinfo();

$reqId = $this->input->get("reqId");


$paket_rekanan_check = new PaketRekanan();
$reqPaketRekananId = $paket_rekanan_check->getPaketRekananId($reqId, $this->ID);
if($reqPaketRekananId == "")
	exit;

$paket_rekanan = new PaketRekanan();
$paket_tahap = new PaketTahap();
$paket_tahap_metode = new PaketTahap();

$jenis_tahap = $paket_tahap_metode->getJenisTahapById($reqId);
// echo $jenis_tahap; die();
$arrUploadPasswordPenawaran     = UPLOAD_PASSWORD_PENAWARAN;

$aktif_upload_password = $paket_tahap->getCountByParamsAktif(array("URUT" => $arrUploadPasswordPenawaran[$jenis_tahap], "PAKET_ID" => $reqId, "TAMPILKAN" => 1));
// echo $aktif_upload_password; die();
$paketInfo->getPaket($reqId);
$reqNama = $paketInfo->nama;
$reqSistemSampul = $paketInfo->sistem_sampul;
$reqUUID = $paketInfo->uuid;

$paket_rekanan_validasi = new PaketRekanan();
$paket_rekanan_validasi->selectByParamsPaketLelang(array("PAKET_ID" => $reqId, "REKANAN_ID" => $this->ID), -1, -1, " AND TANGGAL_DAFTAR IS NOT NULL ");
$paket_rekanan_validasi->firstRow();

if($reqSistemSampul == "2")
	$reqFileValidasi = FILENAME_PENAWARAN."1_".$paketInfo->publish_ba_penawaran.$paketInfo->pr_group_number."-".$paket_rekanan_validasi->getField("KODE_REKANAN").".cert";
else
	$reqFileValidasi = FILENAME_PENAWARAN.$paketInfo->publish_ba_penawaran.$paketInfo->pr_group_number."-".$paket_rekanan_validasi->getField("KODE_REKANAN").".cert";

$paket_rekanan->selectByParamsPaketLelang(array("PAKET_ID" => $reqId, "REKANAN_ID" => $this->ID), -1, -1, " AND TANGGAL_DAFTAR IS NOT NULL ");
$paket_rekanan->firstRow();
if($paket_rekanan->getField("PAKET_REKANAN_ID") == "")
	exit;

if($paket_rekanan->getField("KIRIM_PENAWARAN") == "0")
	exit;

$reqPassword = $paket_rekanan->getField("KIRIM_PENAWARAN_PASSWORD");

if($reqPassword == "")
{}
else
	$reqFile = $reqFileValidasi;


?>
<script type="text/javascript">
$(document).ready(function() {

	$(function(){
		$('#ff').form({
			url:'dokumen_pengadaan_upload_rekanan/upload_password_penawaran',
			onSubmit:function(){
				// return $(this).form('validate');
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
        let a = data.split("--");
        if (a[0] == '1') {
          alertError2(a[1]);
        } else {
          alertSuccess2(a[1]);
        }
        setTimeout(function(){
				  document.location.href = 'main/index/dokumen_penawaran_password/?reqId=<?=$reqId?>';
        }, 1500);
				//$.messager.alert('Info', data, 'info');
        hideLoad();
			}
		});

	});

});
</script>

<div class="row">
  <div class="col-md-12 col-sm-12">
    <div class="card">
      <div class="card-header card-head-inverse bg-primary">
        <h4 class="card-title text-white">Upload Enkripsi Penawaran
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
            <div class="table-responsive">
              <form id="ff" class="easyui-form form-horizontal" method="post" novalidate enctype="multipart/form-data">
                <?php
                if($aktif_upload_password == 0)
                    echo '<div class="alert alert-danger">Waktu Upload File .cert Penawaran habis atau belum mulai</div>';
                else
                { }
                  ?>
                <div class="alert alert-info">Upload File .cert <?=$reqNama?></div>
                <table class="table table-bordered table-hover">
                  <tr>
                    <td colspan="2"> Silahkan upload file .cert dokumen penawaran yang sudah dikirim via email dengan nama file : <font color="#000"><b><?=$reqFileValidasi?></b></font></td>
                  </tr>
                  <?php
                  $notifUpload = '';
                  if($reqPassword == "") {
                      $title = "File .cert";
                  }
                  else
                  {
                      $title = "Upload ulang File .cert";
                      $notifUpload = '<div class="alert alert-info">File Enkripsi Penawaran Berhasil diupload</div>';
                  ?>
                  <tr class="gelap">
                    <td>File .cert</td>
                    <td><?=$reqFile?></td>
                  </tr>
                  <?php
                  }
                  ?>
                  <?php
                  if($aktif_upload_password == 1) { ?>
                  <tr class="gelap">
                    <td style="width: 20%"><?=$title?></td>
      							<td><input type="file" name="reqLinkFile" <?php if($aktif_upload_password == 0) { ?> disabled <?php } ?> class="easyui-validatebox" required /></td>
                    <!-- <td><input type="file" name="reqLinkFile" <?php if($aktif_upload_password != 0) { ?> disabled <?php } ?> class="easyui-validatebox" required /></td> -->
                  </tr>
                  <?php
                  } ?>
                </table>

                  <?= $notifUpload; ?>
                <div class="form-actions">
                  <a href="main/index/paket_detil/?eid=<?=$reqId?>&key=<?=$reqUUID?>" class="<?= CLASS_BTN_DANGER ?> mr-1"> <i class="fa fa-arrow-left"></i> Kembali </a>
                  <?php if($aktif_upload_password == 1) { ?>
                  <?php //if($aktif_upload_password == 0) { ?>
                      <input type="hidden" name="reqId" value="<?=$reqId?>" />
                      <input type="hidden" name="submitSimpan" value="Simpan" />
                      <button type="submit" class="<?= CLASS_BTN_PRIMARY ?>"><i class="fa fa-check-square-o"></i> Upload File .cert</button>
                  <?php } ?>
                </div>
              </form>
            </div>
        </div>
      </div>
    </div>
  </div>
</div>
