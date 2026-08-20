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
$this->load->model("RekananSaham");

/* create objects */
$rekanan = new Rekanan();
$rekanan_saham 	= new RekananSaham();

$reqSahamId= httpFilterRequest('reqSahamId') ?: '0';
$reqPemegangSaham= httpFilterPost('reqPemegangSaham');
$reqNomorKTP= httpFilterPost('reqNomorKTP');
$reqAlamat= httpFilterPost('reqAlamat');
$reqPersentase= httpFilterPost('reqPersentase');
$reqId= httpFilterPost('reqId');
$reqSubmit= httpFilterPost('reqSubmit');

$reqId = $this->ID;

// $rekanan->selectByParams(array("REKANAN_ID"=>$this->ID),-1,-1);
// $rekanan->firstRow();

$rekanan_saham->selectByParams(array("REKANAN_SAHAM_ID"=>$reqSahamId, "REKANAN_ID" => $this->ID),-1,-1);
$rekanan_saham->firstRow();
$reqreqPemegangSaham= $rekanan_saham->getField("NAMA");
$reqreqNomorKTP= $rekanan_saham->getField("KTP");
$reqreqAlamat= $rekanan_saham->getField("ALAMAT");
$reqreqPersentase= $rekanan_saham->getField("JUMLAH_SAHAM");
$reqLinkFileTemp= $rekanan_saham->getField("PATH_FILE");
$reqLinkFileTempTipe= $rekanan_saham->getField("TIPE_FILE");
$reqLinkFileTempUkuran= $rekanan_saham->getField("UKURAN");
$reqLinkFileTempNama= $rekanan_saham->getField("NAMA_FILE");
$reqKepemilikan= $rekanan_saham->getField("KEPEMILIKAN") ?: '';
$reqNomorNPWP= $rekanan_saham->getField("NPWP") ?: '';
$reqKewarganegaraan= $rekanan_saham->getField("KEWARGANEGARAAN");
$reqJenisKelamin= $rekanan_saham->getField("JENIS_KELAMIN");
$reqNegara= $rekanan_saham->getField("NEGARA");
$reqNominalSaham= $rekanan_saham->getField("NOMINAL_SAHAM");

$displaynegara = '';
if ($reqKewarganegaraan == 'Indonesia' || $reqKewarganegaraan == '') {
  $displaynegara = 'display:none';
}

$reqSahamId = $reqSahamId;
if($reqSahamId=='0')
 	$reqMode='insert';
else
	$reqMode='update';
?>
<script type="text/javascript">


$(document).ready(function() {

<?php
if ($reqKepemilikan == 'Instansi') { ?>
  $('#reqNomorKTPAll').hide();
  $('#reqNomorNPWPAll').hide();
<?php
}?>
	$(function(){
		$('#ff').form({
			url:'rekanan_saham_json/data_administrasi_keuangan_saham_ubah',
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
            document.location.href = 'main/index/data_administrasi_kepemilikan_saham';
          }, 2000);
        }
			}
		});

	});

});

<?php
if($reqKepemilikan == '' || $reqKepemilikan == 'Perorangan') {
 ?>
$(document).ready(function() {
  $('#reqNomorKTP').validatebox({ required:true  });
  $('#reqNomorNPWP').validatebox({ required:true  });
});
<?php
} else {
?>
$(document).ready(function() {
  $('#reqNomorKTP').validatebox({ required:false  });
  $('#reqNomorNPWP').validatebox({ required:false  });
});
<?php
} ?>

$(document).ready(function() {
    $('input:radio[name=reqKepemilikan]').change(function() {
      if (this.value == 'Instansi') {
        $('#reqNomorKTP').validatebox({ required:false  });
        $('#reqNomorKTPAll').hide();
        $('#reqNomorNPWP').validatebox({ required:false  });
        $('#reqNomorNPWPAll').hide();
        $('#reqPerorangan').hide();
      }
      else if (this.value == 'Perorangan') {
        $('#reqNomorKTP').validatebox({ required:true  });
        $('#reqNomorKTPAll').show();
        $('#reqNomorNPWP').validatebox({ required:true  });
        $('#reqNomorNPWPAll').show();
        $('#reqPerorangan').show();

      }
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
        <h4 class="card-title text-white">Kepemilikan Saham </h4>
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
              <label style="width: 100%">Kepemilikan</label>
              <input type="radio" <?php if($reqKepemilikan == '' || $reqKepemilikan == 'Perorangan') echo 'checked';?>  name="reqKepemilikan" value="Perorangan" required/> Perorangan &nbsp;&nbsp;&nbsp;
              <input type="radio" <?php if($reqKepemilikan == 'Instansi') echo 'checked';?> name="reqKepemilikan" value="Instansi" required /> Instansi
            </div>
          </div>
          <div class="row">
            <div class="form-group col-md-12 mb-2">
              <label>Pemegang Saham</label>
              <input type="text" name="reqPemegangSaham" title="Nama pemegang saham harus diisi" class="form-control easyui-validatebox span4" id="reqPemegangSaham" value="<?=$reqreqPemegangSaham?>" required />
            </div>
          </div>
          <div class="row" id="reqNomorKTPAll">
            <div class="form-group col-md-6 mb-2">
              <label>No. KTP</label>
              <input type="text" name="reqNomorKTP" id="reqNomorKTP" value="<?=$reqreqNomorKTP?>" class="form-control easyui-validatebox span4" required maxlength="20" />
            </div>
            <div class="form-group col-md-6 mb-2">
              <label>No. NPWP</label>
              <input type="text" name="reqNomorNPWP" id="reqNomorNPWP" value="<?=$reqNomorNPWP?>" class="form-control easyui-validatebox span4" maxlength="50" required required maxlength="19" />
            </div>
          </div>
          <div class="row" id="reqPerorangan">
            <div class="form-group col-md-2 mb-2">
              <label style="width: 100%">Jenis Kelamin</label>
              <select name="reqJenisKelamin" class="easyui-combobox span2" style="width: 200%">
                <option value="L" <?php if($reqJenisKelamin == "L") { ?> selected <?php } ?>>Laki-Laki</option>
                <option value="P" <?php if($reqJenisKelamin == "P") { ?> selected <?php } ?>>Perempuan</option>
              </select>
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
            <div class="form-group col-md-6 mb-2">
              <label>Persentase % </label>
              <input type="text" name="reqPersentase" title="Presentase harus diisi" class="form-control easyui-validatebox span4" id="reqPersentase" value="<?=$reqreqPersentase?>" size="6" maxlength="6" onkeypress="return isNumberKey(event)" required />
              <sup><i>gunakan tanda titik untuk decimal </i> (contoh: 85.50)</sup>
            </div>
            <div class="form-group col-md-6 mb-2">
              <label>Nominal Saham </label>
              <input type="text" name="reqNominalSaham" title="Nominal Saham harus diisi" class="form-control easyui-validatebox span4" id="reqNominalSaham" value="<?=$reqNominalSaham?>" size="6" maxlength="20" OnFocus="FormatAngka('reqNominalSaham')" OnKeyUp="FormatUang('reqNominalSaham')" OnBlur="FormatUang('reqNominalSaham')" required />
            </div>
          </div>

          <!-- <div class="row">
            <div class="form-group col-md-12 mb-2">
              <label>File KTP/NPWP/Kepemilikan Saham <?php // echo UPLOAD_PDF_2MB ?></label><br>
              <input type="hidden" name="reqLinkFileTemp" value="<?php // echo$reqLinkFileTemp?>" />
              <input type="hidden" name="reqLinkFileTempTipe" value="<?php // echo$reqLinkFileTempTipe?>" />
              <input type="hidden" name="reqLinkFileTempUkuran" value="<?php // echo$reqLinkFileTempUkuran?>" />
              <input type="file" name="reqLinkFile" id="reqLinkFilePDF" size="30" <?php // if($reqLinkFileTemp == "") { ?> required <?php // } ?> class="easyui-validatebox"  validType="fileType['pdf']" />
              <input type="hidden" name="reqLinkFileTempNama" value="<?php // echo$reqLinkFileTempNama?>">
              <?php
              // if ($reqLinkFileTempNama) {
              //    echo "File :".$reqLinkFileTempNama;
              //  } 
               ?>
            </div>
          </div> -->
          <div class="row">
            <div class="form-group col-md-12 mb-2">
              <label>Alamat</label>
              <textarea name="reqAlamat" id="reqAlamat" title="Alamat harus diisi" class="form-control easyui-validatebox span4" cols="50" rows="3" required><?=$reqreqAlamat?></textarea>
            </div>
          </div>
          <div class="form-actions">
            <input type="hidden" name="reqSahamId" value="<?=$reqSahamId?>" />
            <input type="hidden" name="reqMode" value="<?=$reqMode?>" />
            <a href="main/index/data_administrasi_kepemilikan_saham" class="<?= CLASS_BTN_DANGER ?> mr-1"><?= BTN_KEMBALI ?></a>
            <button type="submit" class="<?= CLASS_BTN_PRIMARY ?>"><?= BTN_SIMPAN ?></button>
          </div>
        </div>
      </div>
      </form>

    </div>
  </div>
</div>
