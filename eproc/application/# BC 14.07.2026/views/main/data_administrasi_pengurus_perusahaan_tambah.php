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
$this->load->model("RekananPengurus");

/* create objects */
$rekanan_pengurus = new RekananPengurus();
$rekanan = new Rekanan();
//$reqId = $this->ID;

/* VARIABLE */
$reqId = $this->ID;

$reqPengurusID =httpFilterRequest("reqPengurusID") ?: '0';
$reqSubmit   = httpFilterPost("reqSubmit");

$reqNama     = httpFilterPost("reqNama");
$reqNomorKTP = httpFilterPost("reqNomorKTP");
$reqJabatan  = httpFilterPost("reqJabatan");
$reqTipe     = httpFilterRequest("reqTipe");

if($reqTipe == 1)
    $reqNamaTipe = "Komisaris";
else
    $reqNamaTipe = "Direksi";

$reqStatus = 1;

$rekanan_pengurus->selectByParams(array("REKANAN_PENGURUS_ID"=>$reqPengurusID, "REKANAN_ID" => $this->ID),-1,-1);
$rekanan_pengurus->firstRow();
//$reqPengurusID = $rekanan_pengurus->getField("NAMA");
$reqNama = $rekanan_pengurus->getField("NAMA");
$reqKTP = $rekanan_pengurus->getField("KTP");
$reqJabatan = $rekanan_pengurus->getField("JABATAN");

$reqLinkFileTemp= $rekanan_pengurus->getField("PATH_FILE");
$reqLinkFileTempTipe= $rekanan_pengurus->getField("TIPE_FILE");
$reqLinkFileTempUkuran= $rekanan_pengurus->getField("UKURAN");
$reqLinkFileTempNama= $rekanan_pengurus->getField("NAMA_FILE");
$reqKewarganegaraan= $rekanan_pengurus->getField("KEWARGANEGARAAN");
$reqJenisKelamin= $rekanan_pengurus->getField("JENIS_KELAMIN");
$reqAlamatKTP= $rekanan_pengurus->getField("ALAMAT_KTP");
$reqDomisili= $rekanan_pengurus->getField("DOMISILI");
$reqNPWP= $rekanan_pengurus->getField("NPWP");
$reqNegara= $rekanan_pengurus->getField("NEGARA");
$reqNoHPDirektur= $rekanan_pengurus->getField("NOMOR_HP_DIREKTUR");
$reqLinkFile2TempNama= $rekanan_pengurus->getField("PATH_FILE2");

$displaynegara = '';
if ($reqKewarganegaraan == 'Indonesia' || $reqKewarganegaraan == '') {
  $displaynegara = 'display:none';
}

// $rekanan->selectByParams(array("REKANAN_ID"=>$this->ID),-1,-1);
// $rekanan->firstRow();

if($reqPengurusID=='0')
    $reqMode='insert';
else
    $reqMode='update';

?>
<script type="text/javascript">
$(document).ready(function() {

    $(function(){
        $('#ff').form({
            url:'rekanan_pengurus_json/data_administrasi_pengurus_perusahaan_ubah',
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
              if (data == 'Data Gagal Tersimpan') {
				        alertError3(data);
    					} else {
    						alertSuccess2('Data berhasil disimpan');
    						setTimeout(function() {
                  document.location.href = 'main/index/data_administrasi_pengurus_perusahaan';
              	}, 2000);
    					}
            }
        });

    });

});

function onChangeKewarganegaraan(value) {
    if (value === 'Asing') {
        $('#displayNegara').show();
    } else {
        $('#displayNegara').hide();
    }
}

</script>

<div class="row">
  <div class="col-md-12 col-sm-12">
    <div class="card">
      <div class="card-header card-head-inverse bg-primary">
        <h4 class="card-title text-white">Data <?=$reqNamaTipe?> Perusahaan </h4>
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
              <div class="form-group col-md-12 mb-2">
                <label>Nama</label>
                <input type="text" name="reqNama" id="reqNama" title="Nama Pengurus harus diisi" class="form-control easyui-validatebox span4" value="<?=$reqNama?>" required />
              </div>
            </div>
            <div class="row">
              <div class="form-group col-md-6 mb-2">
                <label>KTP / Passport / KITAS</label>
                <input type="text" name="reqNomorKTP" id="reqNomorKTP" title="Nomor KTP harus diisi" class="form-control easyui-validatebox span4" value="<?=$reqKTP?>" required onkeypress="return isNumberKey(event)" maxlength="20" />
              </div>
              <div class="form-group col-md-6 mb-2">
                <label>No. NPWP</label>
                <input type="text" name="reqNPWP" id="reqNPWP" title="Nomor NPWP harus diisi" class="form-control easyui-validatebox span4" value="<?=$reqNPWP?>" required maxlength="19" />
              </div>
            </div>
            <div class="row">
              <div class="form-group col-md-2 mb-2">
                <label style="width: 100%">Jenis Kelamin</label>
                <select name="reqJenisKelamin" class="easyui-combobox span2" style="width: 200%">
                  <option value="L" <?php if($reqJenisKelamin == "L") { ?> selected <?php } ?>>Laki-Laki</option>
                  <option value="P" <?php if($reqJenisKelamin == "P") { ?> selected <?php } ?>>Perempuan</option>
                </select>
              </div>
              <div class="form-group col-md-5 mb-2">
                <label>Jabatan</label>
                <input type="text" name="reqJabatan" id="reqJabatan" title="Jabatan harus diisi" class="form-control easyui-validatebox span4" value="<?=$reqJabatan?>" required  maxlength="100"/>
              </div>
              <div class="form-group col-md-2 mb-2">
                <label style="width: 100%">Kewarganegaraan</label>
                <select name="reqKewarganegaraan" class="easyui-combobox span2" style="width: 200%" data-options="
                      editable:false,
                      onChange: function(newValue, oldValue){
                          onChangeKewarganegaraan(newValue);
                      }">
                  <option value="Indonesia" <?php if($reqKewarganegaraan == "Indonesia") { ?> selected <?php } ?>>Indonesia</option>
                  <option value="Asing" <?php if($reqKewarganegaraan == "Asing") { ?> selected <?php } ?>>Asing</option>
                </select>
              </div>
              <div class="form-group col-md-3 mb-2" id="displayNegara" style="<?= $displaynegara ?>">
                <label style="width: 100%">Negara</label>
                <input type="text" name="reqNegara" class="easyui-combobox span4" id="reqNegara" data-options="valueField:'id',textField:'text',url:'negara_json/combo',
                          onSelect : function(rec){ }
                          "  value="<?=$reqNegara?>" style="width: 300%"/>
              </div>
            </div>
            <div class="row">
              <div class="form-group col-md-12 mb-2">
                <label>Alamat KTP / Passport / KITAS</label>
                <input type="text" name="reqAlamatKTP" id="reqAlamatKTP" title="Jabatan harus diisi" class="form-control easyui-validatebox span4" value="<?=$reqAlamatKTP?>" maxlength="255" />
              </div>
            </div>
            <div class="row">
              <div class="form-group col-md-12 mb-2">
                <label>Alamat Domisili</label>
                <input type="text" name="reqDomisili" id="reqDomisili" title="Jabatan harus diisi" class="form-control easyui-validatebox span4" value="<?=$reqDomisili?>" maxlength="255" />
              </div>
            </div>
            <?php
            if($reqTipe == 2) { ?>

              <div class="row">
                <div class="form-group col-md-12 mb-2">
                  <label>No Handphone Direktur</label>
                  <input type="text" name="reqNoHPDirektur" id="reqNoHPDirektur" title="Nomor Handphone Direktur" class="form-control easyui-validatebox span4" value="<?=$reqNoHPDirektur?>" required onkeypress="return isNumberKey(event)" />
                </div>
              </div>

            <?php
          } else {
            echo '<input type="hidden" name="reqNoHPDirektur" class="form-control easyui-validatebox span4" value="0"/>';
          } ?>

            <div class="row">
              <div class="form-group col-md-12 mb-2">
                <label>File KTP atau Identitas <?= UPLOAD_PDF_2MB ?></label><br>
                <input type="hidden" name="reqLinkFileTemp" value="<?=$reqLinkFileTemp?>" />
                <input type="hidden" name="reqLinkFileTempTipe" value="<?=$reqLinkFileTempTipe?>" />
                <input type="hidden" name="reqLinkFileTempUkuran" value="<?=$reqLinkFileTempUkuran?>" />
                <input type="file" name="reqLinkFile" id="reqLinkFilePDF" size="30" <?php if($reqLinkFileTemp == "") { ?> required <?php } ?> class="easyui-validatebox"  validType="fileType['pdf']" />
                <input type="hidden" name="reqLinkFileTempNama" value="<?=$reqLinkFileTempNama?>">
                <?php
                if ($reqLinkFileTempNama) {
                   echo "File :".$reqLinkFileTempNama;
                 } ?>
              </div>
            </div>
             <div class="row">
              <div class="form-group col-md-12 mb-2">
                <label>File NPWP <?= UPLOAD_PDF_2MB ?></label><br>
                <input type="hidden" name="reqLinkFile2TempNama" value="<?=$reqLinkFile2TempNama?>" />
                <input type="file" name="reqLinkFile2" id="reqLinkFile2PDF" size="30" <?php if($reqLinkFile2TempNama == "") { ?> required <?php } ?> class="easyui-validatebox"  validType="fileType['pdf']" />
                <input type="hidden" name="reqLinkFile2TempNama" value="<?=$reqLinkFile2TempNama?>">
                <?php
                if ($reqLinkFile2TempNama) {
                   echo "File :".$reqLinkFile2TempNama;
                 } ?>
              </div>
            </div>
            <div class="form-actions">
              <input type="hidden" name="reqTipe" value="<?=$reqTipe?>" />
              <input type="hidden" name="reqPengurusID" value="<?=$reqPengurusID?>" />
              <input type="hidden" name="reqMode" value="<?=$reqMode?>" />
              <a href="main/index/data_administrasi_pengurus_perusahaan" class="<?= CLASS_BTN_DANGER ?> mr-1"> <?= BTN_KEMBALI ?> </a>
              <button type="submit" class="<?= CLASS_BTN_PRIMARY ?>"><?= BTN_SIMPAN ?></button>
            </div>
        </div>
      </div>
      </form>
    </div>
  </div>
</div>
