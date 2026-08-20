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

$this->load->model("RekananRekeningKoran");
$this->load->model("MataUang");
$this->load->model("Bank");

/* create objects */
$rekanan = new Rekanan();
$rekanan_koran = new RekananRekeningKoran();
$mata_uang = new MataUang();

$reqRekeningKoranId= $this->input->get("reqRekeningKoranId") ?: '0';
$reqTahunPajak= $this->input->get("reqTahunPajak") ?: '0';

$reqId = $this->ID;

if ($reqRekeningKoranId != '0') {
	$rekanan_koran->selectByParams(array("REKANAN_REKENING_KORAN_ID"=>$reqRekeningKoranId, "REKANAN_ID" => $this->ID),-1,-1);
	$rekanan_koran->firstRow();
	//echo $rekanan_koran->query;exit;
	$reqNoRekening = $rekanan_koran->getField('NOMOR');
	$reqNamaBank = $rekanan_koran->getField('NAMA_BANK');
	$reqBulan = $rekanan_koran->getField('BULAN');
	$reqAuditor = $rekanan_koran->getField('TAHUN');
	$reqMataUang = $rekanan_koran->getField('MATA_UANG_ID');
	$reqNilai = $rekanan_koran->getField('NILAI');
	$reqRekeningKoranId = $rekanan_koran->getField('REKANAN_REKENING_KORAN_ID');
	$reqLinkFileTemp= $rekanan_koran->getField("PATH_FILE");
	$reqLinkFileTempTipe= $rekanan_koran->getField("TIPE");
	$reqLinkFileTempUkuran= $rekanan_koran->getField("UKURAN");
	$reqBankId = $rekanan_koran->getField("BANK_ID");
	$reqLinkFileTempNama= $rekanan_koran->getField("NAMA_FILE");
} else {
  $rekanan->selectByParams(array("A.REKANAN_ID"=>$this->ID),-1,-1);
  $rekanan->firstRow();
  $reqNoRekening = $rekanan->getField('BANK_REKENING');
  $reqBankId = $rekanan->getField('BANK_ID');
}

if($reqRekeningKoranId == '0')
	$reqMode='insert';
else
	$reqMode='update';

?>
<script type="text/javascript">
$(document).ready(function() {

	$(function(){
		$('#ff').form({
			url:'rekanan_rekening_koran_json/data_keuangan_rekening_koran_ubah',
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
				      document.location.href = 'main/index/data_keuangan_rekening_koran/?reqTahunPajak=<?=$reqTahunPajak?>';
            }, 2000);
          }
			}
		});

	});

  $('#reqBankId').combobox({
    filter: function(q, row){
      var opts = $(this).combobox('options');
      return row[opts.textField].toLowerCase().indexOf(q.toLowerCase()) >= 0;
    }
    });

});
</script>

<div class="row">
  <div class="col-md-12 col-sm-12">
    <div class="card">
      <div class="card-header card-head-inverse bg-primary">
        <h4 class="card-title text-white">Rekening Koran</h4>
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
              <label>Nomor Rekening</label>
              <input name="reqNoRekening" type="text" title="Nomor rekening harus diisi" class="form-control easyui-validatebox span4" id="reqNoRekening" value="<?=$reqNoRekening?>" required />
            </div>
          </div>
          <div class="row">
            <div class="form-group col-md-3 mb-2">
              <label style="width: 100%">Bank</label>
              <input type="text" id="reqBankId" name="reqBankId" class="easyui-combobox span4" data-options="valueField:'id',textField:'text',url:'bank_json/combo'" style="width: 310% !important" value="<?=$reqBankId?>" required />
            </div>
            <div class="form-group col-md-3 mb-2">
              <label style="width: 100%">Bulan</label>
              <select style="width: 300% !important" name="reqBulan" id="reqBulan" class="easyui-combobox span2"  required >
              	<option ></option>
                    <option value="1" <?php if($reqBulan == 1) echo "selected";?>>Januari</option>
                    <option value="2" <?php if($reqBulan == 2) echo "selected";?>>Februari</option>
                    <option value="3" <?php if($reqBulan == 3) echo "selected";?>>Maret</option>
                    <option value="4" <?php if($reqBulan == 4) echo "selected";?>>April</option>
                    <option value="5" <?php if($reqBulan == 5) echo "selected";?>>Mei</option>
                    <option value="6" <?php if($reqBulan == 6) echo "selected";?>>Juni</option>
                    <option value="7" <?php if($reqBulan == 7) echo "selected";?>>Juli</option>
                    <option value="8" <?php if($reqBulan == 8) echo "selected";?>>Agustus</option>
                    <option value="9" <?php if($reqBulan == 9) echo "selected";?>>September</option>
                    <option value="10" <?php if($reqBulan == 10) echo "selected";?>>Oktober</option>
                    <option value="11" <?php if($reqBulan == 11) echo "selected";?>>November</option>
                    <option value="12" <?php if($reqBulan == 12) echo "selected";?>>Desember</option>
              </select>
            </div>
            <div class="form-group col-md-2 mb-2">
              <label>Tahun</label>
              <input class="form-control easyui-validatebox span3" required name="reqAuditor" type="text"  id="reqAuditor" value="<?=$reqAuditor?>" size="1" maxlength="4" onkeypress="return isNumberKey(event)" />
            </div>
            <div class="form-group col-md-4 mb-2">
              <label style="width: 100%">Mata Uang</label>
              <input type="text" name="reqMataUang" required class="form-control easyui-combobox span3" data-options="valueField:'id',textField:'text',url:'mata_uang_json/combo'" value="<?=$reqMataUang?>" style="width: 400% !important" />
            </div>
            <!-- <div class="form-group col-md-4 mb-2">
              <label>Nilai&nbsp;</label>
              <input name="reqNilai" class="form-control easyui-validatebox span3" required type="text" id="reqNilai" value="<?=numberToIna($reqNilai)?>" OnFocus="FormatAngka('reqNilai')" OnKeyUp="FormatUang('reqNilai')" OnBlur="FormatUang('reqNilai')"/>
            </div>  -->
          </div>
          <div class="row">
            <div class="form-group col-md-12 mb-2">
              <label>File Rekening Koran <?= UPLOAD_PDF_2MB ?></label><br>
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
          <div class="form-actions">
            <input type="hidden" name="reqMode" value="<?=$reqMode?>" />
            <input type="hidden" name="reqRekeningKoranId" value="<?=$reqRekeningKoranId?>" />
            <a href="main/index/data_keuangan_rekening_koran" class="<?= CLASS_BTN_DANGER ?> mr-1"> <?= BTN_KEMBALI ?></a>
            <button type="submit" class="<?= CLASS_BTN_PRIMARY ?>"><?= BTN_SIMPAN ?></button>
          </div>
        </div>
      </div>
      </form>
    </div>
  </div>
</div>
