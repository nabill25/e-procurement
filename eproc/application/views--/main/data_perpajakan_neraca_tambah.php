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
$this->load->model("Rekanan");
$this->load->model("RekananNeraca");

include_once("functions/string.func.php");
include_once("functions/date.func.php");
include_once("functions/default.func.php");

ini_set("memory_limit","500M");
ini_set('max_execution_time', 520);

/* create objects */
$rekanan = new Rekanan();
$rekanan_neraca	= new RekananNeraca();

$reqId = $this->ID;

//$reqTahun= $this->input->post("reqTahun");
$reqKekayaanBersih= $this->input->post("reqKekayaanBersih");
$reqAuditor= $this->input->post("reqAuditor");
$reqNomor= $this->input->post("reqNomor");
$reqTanggal= $this->input->post("reqTanggal");
$reqKesimpulan= $this->input->post("reqKesimpulan");
$reqSubmit= $this->input->post("reqSubmit");
$reqLinkFile= $_FILES['reqLinkFile'];
$reqLinkFileTemp = $this->input->post("reqLinkFileTemp");
$reqLinkFileTempTipe = $this->input->post("reqLinkFileTempTipe");
$reqLinkFileTempUkuran = $this->input->post("reqLinkFileTempUkuran");
$reqLinkFile2= $_FILES['reqLinkFile2'];
$reqLinkFileTemp2 = $this->input->post("reqLinkFileTemp2");
$reqLinkFileTempTipe2 = $this->input->post("reqLinkFileTempTipe2");
$reqLinkFileTempUkuran2 = $this->input->post("reqLinkFileTempUkuran2");
$reqModalNeraca = dotToNo($reqKekayaanBersih);
$reqAuditNamaNeraca = $reqAuditor;
$reqAuditNomorNeraca = $reqNomor;
$reqAuditTanggalNeraca = dateToPageCheck($reqTanggal);
$reqAuditKeteranganNeraca = $reqKesimpulan;

$reqTahunNeraca = $this->input->get("reqTahunNeraca") ?: '0';

$rekanan->selectByParams(array("A.REKANAN_ID"=>$this->ID),-1,-1);
$rekanan->firstRow();

if($tidak_link_data != 'ya'){
$rekanan_neraca->selectByParams(array("REKANAN_ID"=>$this->ID, "TAHUN"=>$reqTahunNeraca), -1, -1);
$rekanan_neraca->firstRow();
$reqModalNeraca = $rekanan_neraca->getField("MODAL");
$reqAuditNamaNeraca = $rekanan_neraca->getField("AUDIT_NAMA");
$reqAuditNomorNeraca = $rekanan_neraca->getField("AUDIT_NOMOR");
$reqAuditTanggalNeraca = dateToPageCheck($rekanan_neraca->getField("AUDIT_TANGGAL"));
$reqAuditKeteranganNeraca = $rekanan_neraca->getField("AUDIT_KESIMPULAN");
$reqLinkFileTemp= $rekanan_neraca->getField("PATH_FILE");
$reqLinkFileTempTipe= $rekanan_neraca->getField("TIPE");
$reqLinkFileTempUkuran= $rekanan_neraca->getField("UKURAN");
$reqLinkFileTempNama = $rekanan_neraca->getField("NAMA_FILE");
$reqLinkFileTemp2 = $rekanan_neraca->getField("PATH_FILE2");
$reqLinkFileTempTipe2 = $rekanan_neraca->getField("TIPE2");
$reqLinkFileTempUkuran2 = $rekanan_neraca->getField("UKURAN2");
$reqLinkFileTempNama2 = $rekanan_neraca->getField("NAMA_FILE2");
}
?>
<script type="text/javascript">
$(document).ready(function() {

	$(function(){
		$('#ff').form({
			url:'rekanan_neraca_json/data_administrasi_keuangan_neraca_tambah',
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
          alertSuccess2(data);
          setTimeout(function() {
            document.location.href = 'main/index/data_perpajakan_neraca';
          }, 2000);
        }
			}
		});

	});

  $('#reqTanggal').datebox({
    editable: false
  });

});
</script>

<div class="row">
  <div class="col-md-12 col-sm-12">
    <div class="card">
      <div class="card-header card-head-inverse bg-primary">
        <h4 class="card-title text-white">Neraca </h4>
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
          <div class="row">
            <div class="form-group col-md-2 mb-2">
              <label>Tahun</label>
              <select name="reqTahunNeraca" id="reqTahunNeraca" onChange="document.location.href='main/index/data_perpajakan_neraca_tambah/?reqTahunNeraca='+this.value" class="form-control span2">
				       <?php
                for($i=date('Y')-2;$i<=date('Y')+1; $i++)
                {
                ?>
                  <option value="<?=$i?>" <?php if($i == $reqTahunNeraca) { ?> selected="selected" <?php } ?>><?=$i?></option>
                <?php
                }
                ?>
              </select>
            </div>
          </div>
          <div class="row">
            <div class="form-group col-md-12 mb-2">
              <label><?=translate("Modal (kekayaan bersih)", "Net Worth")?></label>
            	<input name="reqKekayaanBersih" type="text"  class="form-control easyui-validatebox span4" id="reqKekayaanBersih" value="<?=numberToIna($reqModalNeraca)?>" OnFocus="FormatAngka('reqKekayaanBersih')" OnKeyUp="FormatUang('reqKekayaanBersih')" OnBlur="FormatUang('reqKekayaanBersih')" required />
            </div>
          </div>
          <!-- <div class="alert alert-success">
            Audit (Wajib di isi jika ingin mengikuti pelelangan dengan nilai di atas 2M)
          </div> -->
          <div class="row">
            <div class="form-group col-md-12 mb-2">
              <label>K A P</label>
            	<input type="text" name="reqAuditor" id="reqAuditor" class="form-control span4" value="<?=$reqAuditNamaNeraca?>" />
            </div>
          </div>
          <div class="row">
            <div class="form-group col-md-8 mb-2">
              <label style="width: 100%">Nomor</label>
            	<input type="text" name="reqNomor" id="reqNomor" class="form-control span4"  value="<?=$reqAuditNomorNeraca?>" />
            </div>
            <div class="form-group col-md-2 mb-2">
              <label style="width: 100%">Tanggal</label>
            	<input type="text" style="width: 200% !important" name="reqTanggal" class="form-control easyui-datebox span2" id="reqTanggal" value="<?=$reqAuditTanggalNeraca?>" />
            </div>
          </div>
          <div class="row">
            <div class="form-group col-md-12 mb-2">
              <label>Kesimpulan</label>
            	<textarea name="reqKesimpulan" id="reqKesimpulan" cols="45" class="form-control span4" rows="5"><?=$reqAuditKeteranganNeraca?></textarea>
            </div>
          </div>
          <div class="row">
            <div class="form-group col-md-12 mb-2">
              <label>File Neraca / K A P <?= UPLOAD_PDF_2MB ?></label> <br>
            	<input type="hidden" name="reqLinkFileTemp" value="<?=$reqLinkFileTemp?>" />
              <input type="hidden" name="reqLinkFileTempTipe" value="<?=$reqLinkFileTempTipe?>" />
              <input type="hidden" name="reqLinkFileTempUkuran" value="<?=$reqLinkFileTempUkuran?>" />
              <input type="file" name="reqLinkFile" id="reqLinkFilePDF" size="30" <?php if($reqLinkFileTemp == "") { ?> required <?php } ?>  class="easyui-validatebox"  validType="fileType['pdf']" />
               <input type="hidden" name="reqLinkFileTempNama" value="<?=$reqLinkFileTempNama?>">
               <?php
               if ($reqLinkFileTempNama) {
                  echo "File :".$reqLinkFileTempNama;
                  # code...
                } ?>
            </div>
          </div>

					<div class="row">
            <div class="form-group col-md-12 mb-2">
              <label>File Laba Rugi <?= UPLOAD_PDF_2MB ?></label> <br>
            	<input type="hidden" name="reqLinkFileTemp2" value="<?=$reqLinkFileTemp2?>" />
              <input type="hidden" name="reqLinkFileTempTipe2" value="<?=$reqLinkFileTempTipe2?>" />
              <input type="hidden" name="reqLinkFileTempUkuran2" value="<?=$reqLinkFileTempUkuran2?>" />
              <input type="file" name="reqLinkFile2" id="reqLinkFilePDF" size="30" <?php if($reqLinkFileTemp2 == "") { ?> required <?php } ?>  class="easyui-validatebox"  validType="fileType['pdf']" />
               <input type="hidden" name="reqLinkFileTempNama2" value="<?=$reqLinkFileTempNama2?>">
               <?php
               if ($reqLinkFileTempNama2) {
                  echo "File :".$reqLinkFileTempNama2;
                  # code...
                } ?>
            </div>
          </div>
          <div class="form-actions">
            <input type="hidden" name="reqTahunPajak" value="<?=$reqTahunPajak?>" />
            <a href="main/index/data_perpajakan_neraca" class="<?= CLASS_BTN_DANGER ?> mr-1"><?= BTN_KEMBALI ?></a>
            <button type="submit" class="<?= CLASS_BTN_PRIMARY ?>"><?= BTN_SIMPAN ?></button>
          </div>
        </div>
      </div>
      </form>
    </div>
  </div>
</div>
