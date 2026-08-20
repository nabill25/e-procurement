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

include_once("functions/string.func.php");
include_once("functions/date.func.php");
include_once("functions/default.func.php");

/* create objects */
$rekanan = new Rekanan();

$reqId = $this->ID;

$rekanan->selectByParams(array("A.REKANAN_ID"=>$this->ID),-1,-1);
$rekanan->firstRow();

$reqPKP= httpFilterPost("reqPKP");
$reqMasaBerlakuPKP= httpFilterPost("reqMasaBerlakuPKP");
$reqJabatan= httpFilterPost("reqJabatan");
$reqSubmit= httpFilterPost('reqSubmit');

$reqPKP = $rekanan->getField("PKP");
$reqMasaBerlakuPKP = dateToPageCheck($rekanan->getField("PKP_TANGGAL"));
$reqJabatan = $rekanan->getField("NPWP");
$reqPKP = $rekanan->getField("PKP");
$reqPKPFileTemp = $rekanan->getField("PKP_FILE");
$reqNamaFilePKP = $rekanan->getField("NAMA_FILE_PKP");
$reqStatusPKP = $rekanan->getField("STATUS_PKP");
$reqSKTPKP = $rekanan->getField("SKT_PKP_NOMOR");
$reqSKTPKPFileTemp = $rekanan->getField("SKT_PKP_FILE");
$reqNamaFileSKTPKP = $rekanan->getField("NAMA_SKT_PKP_FILE");
$reqNonPKPFileTemp = $rekanan->getField("NON_PKP_FILE");
$reqNamaFileNonPKP = $rekanan->getField("NAMA_NON_PKP_FILE");
?>
<script type="text/javascript">
$(document).ready(function() {

	$(function(){
		$('#ff').form({
			url:'rekanan_json/data_administrasi_keuangan_pkp_ubah',
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
				    document.location.href = 'main/index/data_perpajakan_pkp';
          }, 2000);
        }
			}
		});

	});

  $('#reqMasaBerlakuPKP').datebox({
    editable: false
  });

});

$(document).ready(function() {

  <?php
  if ($reqStatusPKP == '0') { ?>
    $('#IdFilePKP').hide();
    $('#IdFileNonPKP').show();
  <?php
  } ?>

  <?php
  if ($reqStatusPKP == '1') { ?>
    $('#IdFilePKP').show();
    $('#IdFileNonPKP').hide();
  <?php
  } ?>

  $('input:radio[name=reqStatus]').change(function() {
    if (this.value == '0') { // pusat
      $('#kantor-pusat').show();
    }
    else if (this.value == '1') { // cabang
      $('#kantor-pusat').hide();
    }
  });

  $('input:radio[name=reqStatusPKP]').change(function() {
    if (this.value == '1') { // PKP
      $('#IdFilePKP').show();
      $('#IdFileNonPKP').hide();
    }
    else if (this.value == '0') { // Non PKP
      $('#IdFilePKP').hide();
      $('#IdFileNonPKP').show();
    }
  });

  $('body').bind('cut copy paste', function (e) {
    e.preventDefault();
    alertError3('Lakukan pengisian dengan cara di ketik...!');
 });

});
</script>

<div class="row">
  <div class="col-md-12 col-sm-12">
    <div class="card">
      <div class="card-header card-head-inverse bg-primary">
        <h4 class="card-title text-white">PKP / Non PKP</h4>
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
              <label style="width: 100%">Status PKP</label>
              <input type="radio" <?php if($reqStatusPKP == '1' || $reqStatusPKP == '') echo 'checked';?> name="reqStatusPKP" value="1" /> PKP &nbsp;&nbsp;&nbsp;
              <input type="radio" <?php if($reqStatusPKP == '0') echo 'checked';?>  name="reqStatusPKP" value="0" /> Non PKP &nbsp;&nbsp;&nbsp;
            </div>
          </div>

          <div class="row" id="IdFileNonPKP" style="display:none">
            <div class="form-group col-md-4 mb-2">
              <label>File Non PKP</label> <br>
              <input type="file" name="reqNonPKPFile" id="reqLinkFilePDFNonPKP" size="30"  <?php //if ($reqNonPKPFileTemp) { } else { echo 'required'; }?>  class="easyui-validatebox"  validType="fileType['pdf']" /> <br>
              <?php if (file_exists('uploads/rekanan/'.$reqNonPKPFileTemp) && $reqNonPKPFileTemp != '' ) { ?>
              <a target="_blank" href="<?= base_url('uploads/rekanan/').$reqNonPKPFileTemp ?>" class="badge badge-primary">Download file Non PKP</a>
              <?php } ?>
              <small>(File .pdf & Mak. ukuran file 3MB)</small>
              <input type="hidden" name="reqNonPKPFileTemp" value="<?=isset($reqNonPKPFileTemp)?$reqNonPKPFileTemp:''?>">
              <input type="hidden" name="reqNamaFileNonPKP" value="<?=isset($reqNamaFileNonPKP)?$reqNamaFileNonPKP:''?>">
            </div>
          </div>

          <div class="row" id="IdFilePKP">
            <div class="form-group col-md-7 mb-2">
              <label style="width: 100%">Nomor SPPKP</label>
              <input type="text" class="form-control easyui-validatebox span4" value="<?=$reqPKP?>" name="reqPKP" id="reqPKP" onkeydown="return format_PKP(event);" maxlength="50" >
            </div>
            <div class="form-group col-md-2 mb-2">
              <label style="width: 100%"> Tanggal </label>
              <input type="text" style="width:170px" name="reqMasaBerlakuPKP" title="Tanggal harus diisi" id="reqMasaBerlakuPKP" class="form-control easyui-datebox" value="<?=isset($reqMasaBerlakuPKP)?$reqMasaBerlakuPKP:''?>"  />
            </div>
            <div class="form-group col-md-3 mb-2">
              <label>File SPPKP</label> <br>
              <input type="file" name="reqPKPFile" id="reqLinkFilePDFPKP" size="30"  <?php //if ($reqPKPFileTemp) { } else { echo 'required'; }?>  class="easyui-validatebox"  validType="fileType['pdf']" /> <br>
              <?php if (file_exists('uploads/rekanan/'.$reqPKPFileTemp) && $reqPKPFileTemp != '' ) { ?>
              <a target="_blank" href="<?= base_url('uploads/rekanan/').$reqPKPFileTemp ?>" class="badge badge-primary">Download file PKP</a>
              <?php } ?>
              <small>(File .pdf & Mak. ukuran file 3MB)</small>
              <input type="hidden" name="reqPKPFileTemp" value="<?=isset($reqPKPFileTemp)?$reqPKPFileTemp:''?>">
              <input type="hidden" name="reqNamaFilePKP" value="<?=isset($reqNamaFilePKP)?$reqNamaFilePKP:''?>">
            </div>
            <!-- 
            <div class="form-group col-md-7 mb-2">
              <label style="width: 100%">Nomor. SKT PKP</label>
              <input type="text" class="form-control easyui-validatebox span4" value="<?php // echo $reqSKTPKP?>" name="reqSKTPKP" id="reqSKTPKP" onkeydown="return format_PKP(event);" maxlength="50" >
            </div>
            <div class="form-group col-md-5 mb-2">
              <label>File SKT PKP</label> <br>
              <input type="file" name="reqSKTPKPFile" id="reqLinkFilePDFSKTPKP" size="30"class="easyui-validatebox"  validType="fileType['pdf']" /> <br>
              <?php // if (file_exists('uploads/rekanan/'.$reqSKTPKPFileTemp) && $reqSKTPKPFileTemp != '' ) { ?>
              <a target="_blank" href="<?php // echo base_url('uploads/rekanan/').$reqSKTPKPFileTemp ?>" class="badge badge-primary">Download file SKT PKP</a>
              <?php // } ?>
              <small>(File .pdf & Mak. ukuran file 3MB)</small>
              <input type="hidden" name="reqSKTPKPFileTemp" value="<?php // echo isset($reqSKTPKPFileTemp)?$reqSKTPKPFileTemp:''?>">
              <input type="hidden" name="reqNamaFileSKTPKP" value="<?php // echo isset($reqNamaFileSKTPKP)?$reqNamaFileSKTPKP:''?>">
            </div>
              -->
          </div>

          <!-- <div class="row">
            <div class="form-group col-md-12 mb-2">
              <label>No. PKP</label>
        		  <input type="text" name="reqPKP" id="reqPKP" title="No surat harus diisi" class="form-control easyui-validatebox span4" value="<?php // $reqPKP?>" maxlength="50" required />
            </div>
          </div>
          <div class="row">
            <div class="form-group col-md-12 mb-2">
              <label style="width: 100%">Tanggal</label>
              <input type="text" name="reqMasaBerlakuPKP" title="Tanggal PKP harus diisi" class="form-control easyui-datebox span2" id="reqMasaBerlakuPKP" value="<?php //$reqMasaBerlakuPKP?>" required  style="width: 200% !important"/>
            </div>
          </div>  -->

          <div class="form-actions">
            <a href="main/index/data_perpajakan_pkp" class="<?= CLASS_BTN_DANGER ?> mr-1"><?= BTN_KEMBALI ?></a>
            <button type="submit" class="<?= CLASS_BTN_PRIMARY ?>"><?= BTN_SIMPAN ?></button>
          </div>
        </div>
      </div>
      </form>
    </div>
  </div>
</div>
