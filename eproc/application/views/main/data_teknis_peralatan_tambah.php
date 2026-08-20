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
$this->load->model("RekananPeralatan");
$this->load->model("Rekanan");
include_once("functions/string.func.php");
include_once("functions/date.func.php");
include_once("functions/default.func.php");

/* create objects */
$rekanan = new Rekanan();
$rekanan_peralatan = new RekananPeralatan();

$reqTipe= httpFilterPost('reqTipe');
$reqJml= httpFilterPost('reqJml');
$reqKapasitas= httpFilterPost('reqKapasitas');
$reqKapasitasSat= httpFilterPost('reqKapasitasSat');
$reqMerk= httpFilterPost('reqMerk');
$reqTahun= httpFilterPost('reqTahun');
$reqKondisi= httpFilterPost('reqKondisi');
$reqLokasi= httpFilterPost('reqLokasi');
$reqKepemilikan= httpFilterPost('reqKepemilikan');
$reqId= httpFilterPost('reqId');
$reqSubmit= httpFilterPost('reqSubmit');
$reqPeralatanId= httpFilterRequest('reqPeralatanId') ?: '0';
$reqLinkFile= $_FILES['reqLinkFile'];


$rekanan_peralatan->selectByParams(array("REKANAN_PERALATAN_ID"=>$reqPeralatanId, "REKANAN_ID" => $this->ID),-1,-1);
$rekanan_peralatan->firstRow();

$reqTipe= $rekanan_peralatan->getField("JENIS");
$reqJml= $rekanan_peralatan->getField("JUMLAH");
$reqKapasitas= $rekanan_peralatan->getField("KAPASITAS");
$reqKapasitasSat= $rekanan_peralatan->getField("KAPASITAS_SATUAN");
$reqMerk= $rekanan_peralatan->getField("MERK");
$reqTahun= $rekanan_peralatan->getField("TAHUN");
$reqKondisi= $rekanan_peralatan->getField("KONDISI");
$reqLokasi= $rekanan_peralatan->getField("LOKASI");
$reqKepemilikan= $rekanan_peralatan->getField("BUKTI_KEPEMILIKAN");
$reqLinkFileTemp= $rekanan_peralatan->getField("PATH_FILE");
$reqLinkFileTempTipe= $rekanan_peralatan->getField("TIPE");
$reqLinkFileTempUkuran= $rekanan_peralatan->getField("UKURAN");
$reqLinkFileTempNama= $rekanan_peralatan->getField("NAMA_FILE");

$reqId = $this->ID;

$rekanan->selectByParams(array("A.REKANAN_ID"=>$this->ID),-1,-1);
$rekanan->firstRow();
if($reqPeralatanId=='0')
	$reqMode='insert';
else
	$reqMode='update';

?>
<script type="text/javascript">
$(document).ready(function() {

	$(function(){
		$('#ff').form({
			url:'rekanan_peralatan_json/data_teknis_peralatan_ubah',
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
				    document.location.href = 'main/index/data_teknis_peralatan';
          }, 2000);
        }
			}
		});

	});

});
</script>

<div class="row">
  <div class="col-md-12 col-sm-12">
    <div class="card">
      <div class="card-header card-head-inverse bg-primary">
        <h4 class="card-title text-white">Peralatan</h4>
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
              <label>Jenis</label>
        		  <input name="reqTipe" type="text" id="txtTipe" title="Jenis harus diisi" class="form-control easyui-validatebox span4" value="<?=$reqTipe?>" size="50" maxlength="100" required />
            </div>
          </div>
          <div class="row">
            <div class="form-group col-md-3 mb-2">
              <label>Jumlah</label>
              <input name="reqJml" type="text" id="txtJml" title="Jumlah harus diisi" class="form-control easyui-validatebox span4" value="<?=$reqJml?>" size="5" maxlength="10" required onkeypress="return isNumberKey(event)"/>
            </div>
            <div class="form-group col-md-3 mb-2">
              <label>Kapasitas</label>
              <input name="reqKapasitas" type="text" id="txtKapasitas" value="<?=$reqKapasitas?>" size="50" maxlength="100" class="form-control easyui-validatebox span4" maxlength="10" onkeypress="return isNumberKey(event)"/>
            </div>
            <div class="form-group col-md-3 mb-2">
              <label>Satuan</label>
              <input name="reqKapasitasSat" type="text" id="txtKapasitasSat" value="<?=$reqKapasitasSat?>" size="50" maxlength="100" class="form-control easyui-validatebox span4" required />
            </div>
            <div class="form-group col-md-3 mb-2">
              <label>Merk</label>
              <input name="reqMerk" type="text" id="txtMerk" value="<?=$reqMerk?>" size="50" maxlength="100" class="form-control easyui-validatebox span4" required />
            </div>
          </div>
          <div class="row">
            <div class="form-group col-md-4 mb-2">
              <label>Th. Pembuatan</label>
              <input name="reqTahun" type="text" id="txtTahun" class="form-control easyui-validatebox span4" required value="<?=$reqTahun?>" size="5" maxlength="4" onkeypress="return isNumberKey(event)"/>
            </div>
            <div class="form-group col-md-4 mb-2">
              <label>Kondisi</label>
              <select name="reqKondisi" id="reqKondisi" class="form-control easyui-validatebox span4" >
             	  <option value="SANGAT TIDAK BAIK" <?php if($reqKondisi == "SANGAT TIDAK BAIK") echo 'selected';?> >SANGAT TIDAK BAIK</option>
                <option value="TIDAK BAIK" <?php if($reqKondisi == "TIDAK BAIK") echo 'selected';?>>TIDAK BAIK</option>
                <option value="CUKUP BAIK" <?php if($reqKondisi == "CUKUP BAIK") echo 'selected';?>>CUKUP BAIK</option>
                <option value="BAIK" <?php if($reqKondisi == "BAIK") echo 'selected';?>>BAIK</option>
                <option value="BAIK SEKALI" <?php if($reqKondisi == "BAIK SEKALI") echo 'selected';?>>BAIK SEKALI</option>
              </select>
            </div>
            <div class="form-group col-md-4 mb-2">
              <label>Kepemilikan</label>
              <select name="reqKepemilikan" id="reqKepemilikan" class="form-control easyui-validatebox span4">
                <option value="Milik Sendiri" <?php if($reqKepemilikan == "Milik Sendiri") echo "selected";?>>Milik Sendiri</option>
      				  <option value="Sewa Jangka Panjang" <?php if($reqKepemilikan == "Sewa Jangka Panjang") echo "selected";?>>Sewa Jangka Panjang</option>
      				  <option value="Sewa Jangka Pendek" <?php if($reqKepemilikan == "Sewa Jangka Pendek") echo "selected";?>>Sewa Jangka Pendek</option>
              </select>
            </div>
          </div>
          <div class="row">
            <div class="form-group col-md-12 mb-2">
              <label>Lokasi</label>
              <textarea name="reqLokasi" id="txtLokasi" class="form-control easyui-validatebox span4" required cols="46" rows="2"><?=$reqLokasi?></textarea>
            </div>
          </div>
          <div class="row">
            <div class="form-group col-md-12 mb-2">
              <label>File Peralatan <?= UPLOAD_PDF_2MB ?></label><br>
              <input type="hidden" name="reqLinkFileTemp" value="<?=$reqLinkFileTemp?>" />
              <input type="hidden" name="reqLinkFileTempTipe" value="<?=$reqLinkFileTempTipe?>" />
              <input type="hidden" name="reqLinkFileTempUkuran" value="<?=$reqLinkFileTempUkuran?>" />
              <input type="file" name="reqLinkFile" id="reqLinkFilePDF" size="30" <?php if($reqLinkFileTemp == "") { ?> required <?php } ?> class="easyui-validatebox span4"  validType="fileType['pdf']"/>
               <input type="hidden" name="reqLinkFileTempNama" value="<?=$reqLinkFileTempNama?>" />
              <?php
              if ($reqLinkFileTempNama) {
                 echo "<br><small>File :".$reqLinkFileTempNama.'</small> <a href="'.base_url('uploads/peralatan/').$reqLinkFileTemp.'" class="badge badge-primary">Download file</a>';
               } ?>
            </div>
          </div>
          <div class="form-actions">
            <input type="hidden" name="reqPeralatanId" value="<?=$reqPeralatanId?>" />
            <input type="hidden" name="reqMode" value="<?=$reqMode?>" />
            <a href="main/index/data_teknis_peralatan" class="<?= CLASS_BTN_DANGER ?> mr-1"><?= BTN_KEMBALI ?></a>
            <button type="submit" class="<?= CLASS_BTN_PRIMARY ?>"><?= BTN_SIMPAN ?></button>
          </div>


        </div>
      </div>
      </form>
    </div>
  </div>
</div>
