<?php
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 *
 */

$this->libsession->cekSession('blockpenyedia');

if ($this->REKANAN_TIPE_ID != '7')
    redirect(base_url('main'));

//ob_start();
/* INCLUDE FILE */
$this->load->library("crfs_protect"); $csrf = new crfs_protect();
$this->load->library("kauth");  $userLogin = new kauth();
$this->load->model("Rekanan");
$this->load->model("Region");
$this->load->model("Bank");
$this->load->model("Incoterm");
$this->load->model("PaymentMethod");
$this->load->model("MataUang");

include_once("functions/string.func.php");
include_once("functions/date.func.php");
include_once("functions/default.func.php");

/* create objects */
$rekanan = new Rekanan();
$region = new Region();
$bank = new Bank();
$incoterm = new Incoterm();
$payment_method = new PaymentMethod();
$mata_uang = new MataUang();

$rekanan->selectByParams(array("A.REKANAN_ID"=>$this->ID),-1,-1);
$rekanan->firstRow();

$reqKualifikasi = $rekanan->getField("REKANAN_KUALIFIKASI_ID");
if ($reqKualifikasi == '2' || $reqKualifikasi == '3') { // 1:Kecil - 2:Non-Kecil - 3:Kecil / Non-Kecil
  $reqKualifikasi = '2';
} else {
  $reqKualifikasi = '1'; // default kecil
}
$reqMail = $rekanan->getField("EMAIL");
$reqFaxKode = $rekanan->getField("FAX_KODE");
$reqFaxNo = $rekanan->getField("FAX");
$reqTeleponKode = $rekanan->getField("TELEPON_KODE");
$reqTeleponNo = $rekanan->getField("TELEPON");
$reqKota = $rekanan->getField("KOTA");
$reqAlamat = $rekanan->getField("ALAMAT");
$reqStatus = $rekanan->getField("STATUS_PERUSAHAAN");
$reqNPWP = $rekanan->getField("NPWP");
$reqNPWPFileTemp = $rekanan->getField("NPWP_FILE");
$reqNamaFileNPWP = $rekanan->getField("NAMA_FILE_NPWP");
$reqKTP = $rekanan->getField("KTP");
$reqKTPFileTemp = $rekanan->getField("KTP_FILE");
$reqNamaFileKTP = $rekanan->getField("NAMA_FILE_KTP");
$reqPKP = $rekanan->getField("PKP");
$reqPKPFileTemp = $rekanan->getField("PKP_FILE");
$reqMasaBerlakuPKP = dateToPageCheck($rekanan->getField("PKP_TANGGAL"));
$reqNamaFilePKP = $rekanan->getField("NAMA_FILE_PKP");
$reqRekananTipe= $rekanan->getField("REKANAN_TIPE_ID");
$reqNama= $rekanan->getField("REKANAN_NAMA");
$reqMailPusat = $rekanan->getField("EMAIL_PUSAT");
$reqFaxKodePusat = $rekanan->getField("FAX_KODE_PUSAT");
$reqFaxNoPusat = $rekanan->getField("FAX_PUSAT");
$reqTeleponKodePusat = $rekanan->getField("TELEPON_KODE_PUSAT");
$reqTeleponNoPusat = $rekanan->getField("TELEPON_PUSAT");
$reqAlamatPusat = $rekanan->getField("ALAMAT_PUSAT");
$reqKodepos = $rekanan->getField("KODEPOS");

$reqRegionId = $rekanan->getField("REGION_ID");
$reqBankId = $rekanan->getField("BANK_ID");
$reqRekening = $rekanan->getField("BANK_REKENING");
$reqRekeningNama = $rekanan->getField("BANK_PEMILIK");
$reqIncoterm1 = $rekanan->getField("INCOTERM_ID");
$reqIncoterm2 = $rekanan->getField("INCOTERM2");
$reqPaymentMethodId = $rekanan->getField("PAYMENT_METHOD_ID");
$reqMataUang = $rekanan->getField("MATA_UANG_KODE");
$reqKontakPerson = $rekanan->getField("KONTAK_PERSON");
$reqKontakPersonHp = $rekanan->getField("KONTAK_PERSON_HP");
$reqWebsite = $rekanan->getField("WEBSITE");
$reqBankCabang = $rekanan->getField("BANK_CABANG");
$tempCPFILE = $rekanan->getField("COMPANY_PROFILE_FILE");
$reqRegionId = $rekanan->getField("NAMAPROPINSI");
$reqKota = $rekanan->getField("NAMAKABKOTA");
$reqKecamatan = $rekanan->getField("NAMAKECAMATAN");
$reqKelurahan = $rekanan->getField("KELURAHAN");

if($reqIncoterm1 == "")
  $reqIncoterm1 = "CIF";

$region->selectByParams();
$bank->selectByParams();
$incoterm->selectByParams();
$payment_method->selectByParams();
$mata_uang->selectByParams();

?>
<script type="text/javascript">
$(document).ready(function() {

  $(function(){
    $('#ff').form({
      url:'rekanan_json/data_administrasi_umum_ubah_profile_perorangan',
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
        $.messager.alert('Info', data, 'info');
        // document.location.href = 'main/index/registrasi_rekanan_identitas_perorangan';
        // document.location.href = 'main/index/data_administrasi_umum_perorangan';
        hideLoad();
      }
    });

  $('#reqBankId').combobox({
    filter: function(q, row){
      var opts = $(this).combobox('options');
      return row[opts.textField].toLowerCase().indexOf(q.toLowerCase()) >= 0;
    }
    });

  });



});
</script>

<div class="row">
    <div class="col-md-12 col-sm-12">
        <div class="card">
            <div class="card-header card-head-inverse bg-primary">
                <h4 class="card-title text-white">Ubah Profil Perorangan
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
              <div class="card-body">
                <form id="ff" class="easyui-form form-horizontal" method="post" novalidate enctype="multipart/form-data">
                  <div class="row">
                    <div class="form-group col-md-2 mb-2">
                      <label style="width: 100%">Bentuk Usaha</label>
                      <input type="text" name="reqRekananTipe" class="form-control easyui-combobox span1" data-options="valueField:'id',textField:'text',url:'rekanan_tipe_json/combo'" style="width: 200% !important" value="<?=$reqRekananTipe?>"  />
                    </div>
                    <div class="form-group col-md-10 mb-2">
                      <label for="projectinput6">Nama Perorangan</label>
                      <input type="text" class="form-control easyui-validatebox span8" name="reqNama" value="<?=$reqNama?>" title="Nama perusahaan harus diisi"  required >
                    </div>
                  </div>

                  <div class="row">
                    <div class="form-group col-md-12 mb-2">
                      <label>Alamat:</label>
                      <textarea name="reqAlamat" cols="50" rows="5" title="Alamat harus diisi" class="form-control easyui-validatebox span4" required ><?=$reqAlamat;?></textarea>
                    </div>
                  </div>

                  <div class="row">
                    <div class="form-group col-md-3 mb-2">
                      <label style="width: 100%">Provinsi</label>
                      <input type="text" name="reqRegionId" id="reqRegionId" class="easyui-combobox span4" required style="width: 300% !important" data-options="valueField:'id',textField:'text',url:'region_json/combo',
                                        onSelect: function(rec){
                                            $('#reqKota').combobox('clear');
                                            $('#reqKecamatan').combobox('clear');
                                            $('#reqKelurahan').combobox('clear');
                                            $('#reqKota').combobox('reload', 'region_json/combokabkot/?reqProvinsi='+rec.id);
                                        }" value="<?=isset($reqRegionId) ? $reqRegionId : ''?>"  />
                    </div>

                    <div class="form-group col-md-3 mb-2">
                      <label style="width: 100%">Kabupaten / Kota</label>
                      <input type="text" name="reqKota" id="reqKota" style="width: 300% !important" title="Kota harus diisi" class="easyui-combobox form-control easyui-validatebox span4" data-options="valueField:'id',textField:'text',url:'region_json/combokabkot/?reqProvinsi=<?=isset($reqRegionId) ? $reqRegionId : ''?>', onSelect: function(rec){ 
                                            $('#reqKecamatan').combobox('reload', 'region_json/combokecamatan/?reqKabkot='+rec.id+'&reqProvinsi='+$('#reqRegionId').combobox('getValue'));
                                        }" value="<?=isset($reqKota) ? $reqKota : ''?>" />
                    </div>

                    <div class="form-group col-md-3 mb-2">
                      <label style="width: 100%">Kecamatan</label>
                      <input type="text" name="reqKecamatan" id="reqKecamatan" style="width: 300% !important" title="Kota harus diisi" class="easyui-combobox form-control easyui-validatebox span4"  data-options="valueField:'id',textField:'text',url:'region_json/combokecamatan/?reqKabkot=<?=isset($reqKota) ? $reqKota : ''?>&reqProvinsi=<?=isset($reqRegionId) ? $reqRegionId : ''?>', onSelect: function(rec){ 
                                var kotaText = $('#reqKota').combobox('getText');
                                $('#reqKelurahan').combobox('reload', 'region_json/combokelurahan/?reqKecamatan='+rec.id+'&reqProvinsi='+$('#reqRegionId').combobox('getValue')+'&reqKabkot='+$('#reqKota').combobox('getText'));
                            }" value="<?=isset($reqKecamatan) ? $reqKecamatan : ''?>"/>
                    </div>


                    <div class="form-group col-md-3 mb-2">
                      <label style="width: 100%">Keluarahan <?=isset($reqKelurahan) ? $reqKelurahan : ''?></label>
                      <input type="text" name="reqKelurahan" id="reqKelurahan" style="width: 300% !important" title="Kota harus diisi" class="easyui-combobox form-control easyui-validatebox span4"  data-options="valueField:'id',textField:'text',url:'region_json/combokelurahan/?reqKecamatan=<?=isset($reqKecamatan) ? $reqKecamatan : ''?>&reqProvinsi=<?=isset($reqRegionId) ? $reqRegionId : ''?>&reqKabkot=<?=isset($reqKota) ? $reqKota : ''?>', onSelect: function(rec){ 
                            }" value="<?=isset($reqKelurahan) ? $reqKelurahan : ''?>"/>
                    </div>
                  </div> 

                  <div class="row">
                    <div class="form-group col-md-3 mb-2">
                      <label>Kode Pos</label>
                      <input type="text" name="reqKodepos" value="<?=$reqKodepos?>" title="Kodepos harus diisi" class="form-control easyui-validatebox span2" maxlength="6" required  />
                    </div>
                    <div class="form-group col-md-5 mb-2">
                      <label style="width: 100%">No. NPWP</label>
                      <input type="text" class="form-control easyui-validatebox span4" value="<?=$reqNPWP?>" name="reqNPWP" id="reqNPWP" onkeydown="return format_npwp(event);" maxlength="20" required  >
                    </div>
                    <div class="form-group col-md-3 mb-2">
                      <label>NPWP</label> <br>
                      <input type="file" name="reqNPWPFile" id="reqLinkFilePDFNPWP" size="30"  <?php if ($reqNPWPFileTemp) { } else { echo 'required'; }?>  class="easyui-validatebox"  validType="fileType['pdf']" readonly/> <br>
                      <?php if (file_exists('uploads/rekanan/'.$reqNPWPFileTemp) && $reqNPWPFileTemp) { ?>
                      <a target="_blank" href="<?= base_url('uploads/rekanan/').$reqNPWPFileTemp ?>" class="badge badge-primary">Download file NPWP</a>
                      <?php } ?>
                      <small> <br>Format file .pdf & Maksimal ukuran file 2MB </small>
                      <input type="hidden" name="reqNPWPFileTemp" value="<?=isset($reqNPWPFileTemp)?$reqNPWPFileTemp:''?>">
                      <input type="hidden" name="reqNamaFileNPWP" value="<?=isset($reqNamaFileNPWP)?$reqNamaFileNPWP:''?>">
                    </div>
                  </div>

                  <div class="row">
                    <div class="form-group col-md-7 mb-2">
                      <label>No. KTP</label>
                      <input type="text" class="form-control easyui-validatebox span4" value="<?=$reqKTP?>" name="reqKTP" id="reqKTP" maxlength="20" required >
                    </div>
                    <div class="form-group col-md-5 mb-2">
                      <label>KTP</label> <br>
                      <input type="file" name="reqKTPFile" id="reqLinkFilePDF" size="30"  <?php if ($reqKTPFileTemp) { } else { echo 'required'; }?>  class="easyui-validatebox"  validType="fileType['pdf']" /> <br>
                      <?php if (file_exists('uploads/rekanan/'.$reqKTPFileTemp) && $reqKTPFileTemp) { ?>
                      <a target="_blank" href="<?= base_url('uploads/rekanan/').$reqKTPFileTemp ?>" class="badge badge-primary">Download file KTP</a>
                      <?php } ?>
                      <small> <br>Format file .pdf & Maksimal ukuran file 2MB </small>
                      <input type="hidden" name="reqKTPFileTemp" value="<?=isset($reqKTPFileTemp)?$reqKTPFileTemp:''?>">
                      <input type="hidden" name="reqNamaFileKTP" value="<?=isset($reqNamaFileKTP)?$reqNamaFileKTP:''?>">
                    </div>
                  </div>


                  <div class="row">
                    <div class="form-group col-md-1 mb-2">
                      <label style="width: 100%">No. Telepon</label>
                      <input type="text" name="reqTeleponKode" id="reqTeleponKode" class="form-control easyui-validatebox span1" value="<?=$reqTeleponKode?>">
                    </div>
                    <div class="form-group col-md-5 mb-2">
                      <label><small>(kode area tidak perlu diisi jika nomor telepon yang dicantumkan adalah nomor ponsel) </small></label>
                      <input type="text" name="reqTeleponNo" id="reqTeleponNo" class="form-control easyui-validatebox span3" value="<?=$reqTeleponNo?>" required>
                    </div>
                    <div class="form-group col-md-1 mb-2">
                      <label style="width: 100%">Fax</label>
                      <input type="text" name="reqFaxKode" id="reqFaxKode" class="form-control easyui-validatebox span1" value="<?=$reqFaxKode?>">
                    </div>
                    <div class="form-group col-md-5 mb-2">
                      <label>&nbsp;</label>
                      <input type="text" name="reqFaxNo" id="reqFaxNo" class="form-control easyui-validatebox span3" value="<?=$reqFaxNo?>">
                    </div>
                  </div>

                  <div class="row">
                    <div class="form-group col-md-6 mb-2">
                      <label style="width: 100%">Kontak Person</label>
                      <input type="text" name="reqKontakPerson" id="reqKontakPerson" class="form-control easyui-validatebox span3" value="<?=$reqKontakPerson?>" placeholder="Kontak Person">
                    </div>
                    <div class="form-group col-md-6 mb-2">
                      <label>No. HP</label>
                      <input type="text" name="reqKontakPersonHp" id="reqKontakPersonHp" class="form-control easyui-validatebox span3" value="<?=$reqKontakPersonHp?>" required>
                    </div>
                  </div>

                  <div class="row">
                    <div class="form-group col-md-6 mb-2">
                      <label style="width: 100%">eMail</label>
                       <input type="text" name="reqMail" value="<?=$reqMail?>"  title="Email harus diisi" class="form-control easyui-validatebox span4" data-options="required:true,validType:['email']" readonly />
                    </div>
                    <div class="form-group col-md-6 mb-2">
                      <label>Website</label>
                      <input type="text" name="reqWebsite" value="<?=$reqWebsite?>"  title="Email harus diisi" class="form-control easyui-validatebox span4" />
                    </div>
                  </div>

                  <div class="row">
                    <div class="form-group col-md-12 mb-2">
                      <label style="width: 100%">Kualifikasi:</label>
                      <input type="radio" <?php if($reqKualifikasi == '1') echo 'checked';?>  name="reqKualifikasi" value="1" /> Kecil &nbsp;&nbsp;&nbsp;
                      <input type="radio" <?php if($reqKualifikasi == '2') echo 'checked';?> name="reqKualifikasi" value="2" /> Non Kecil
                    </div>
                  </div>

                   <div class="card mb-1 border-blue border-darken-1">
                    <div class="card-content">
                      <div class="p-1">
                        <div class="alert bg-primary alert-icon-left alert-arrow-left alert-dismissible mb-2" role="alert">
                          <span class="alert-icon"><i class="fa fa-th"></i></span>  <strong>Informasi Pembayaran (Nomor Rekning)</strong>
                        </div>

                        <div class="row"> 
                          <div class="form-group col-md-12 mb-2">
                            <label>No Rekening</label>
                            <input type="text" name="reqNoRekening" value="<?=$reqRekening?>" title="No rekening harus diisi" class="form-control easyui-validatebox span4" required  />
                          </div>
                        </div>
                        <div class="row"> 
                          <div class="form-group col-md-4 mb-2">
                            <label style="width: 100%">Bank</label>
                            <input required type="text" name="reqBankId" id="reqBankId" class="easyui-combobox span4" data-options="valueField:'id',textField:'text',url:'bank_json/combo'"  value="<?=$reqBankId?>" style="width: 400% !important" />
                          </div>
                          <div class="form-group col-md-4 mb-2">
                            <label style="width: 100%">Atas Nama</label>
                            <input type="text" name="reqAtasNama" value="<?=$reqRekeningNama?>" title="Pemilik rekening harus diisi" class="form-control easyui-validatebox span4" required  />
                          </div> 
                          <div class="form-group col-md-4 mb-2">
                            <label style="width: 100%">Cabang</label>
                            <input type="text" name="reqBankCabang" value="<?=$reqBankCabang?>" title="Cabang harus diisi" class="form-control easyui-validatebox span4" required  />
                          </div>
                        </div>

                      </div>
                    </div>
                  </div>


                  <div class="form-actions">
                    <a href="main/index/data_administrasi_umum_perorangan" class="<?= CLASS_BTN_DANGER ?> mr-1"> <i class="fa fa-arrow-left"></i> Kembali </a>
                    <button type="submit" class="<?= CLASS_BTN_PRIMARY ?>"><i class="fa fa-check-square-o"></i> Simpan</button>

                  </div>
                  <?=$csrf->echoInputField();?>

                </form>
              </div>
            </div>
        </div>
    </div>
</div>
