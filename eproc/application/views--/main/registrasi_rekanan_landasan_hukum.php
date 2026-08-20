<?php
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 * 
 */
$this->libsession->cekSession();   

// if($this->USER_TYPE_ID == "")
//     redirect("app");

/* INCLUDE FILE */
$this->load->library("crfs_protect"); $csrf = new crfs_protect('_crfs_rr');
$this->load->library("kauth");  $userLogin = new kauth(); 
$this->load->model("Rekanan");
$this->load->model("RekananAkta");
$this->load->model("RekananSertifikat");

include_once("functions/string.func.php");
include_once("functions/date.func.php");
include_once("functions/default.func.php");

$reqRekananId = $this->ID;

//echo "rekanan_id => ".$reqRekananId;

if($reqRekananId == "")
  exit;

/* create objects */
$rekanan_akta = new RekananAkta();
$rekanan_akta_perubahan = new RekananAkta();
$rekanan = new Rekanan();
$rekanan_sertifikat = new RekananSertifikat();
$rekanan_sertifikat_domisili = new RekananSertifikat();
$rekanan_sertifikat_tanda_daftar = new RekananSertifikat();

$reqId      = httpFilterPost("reqId");
$reqAktaType = httpFilterRequest("reqAktaType");
$reqNamaNotaris     = httpFilterPost("reqNamaNotaris");
$reqTanggal     = httpFilterPost("reqTanggal");
$reqNomorAkta     = httpFilterPost("reqNomorAkta");
//$reqRekananAktaId= httpFilterPost('reqRekananAktaId');
$reqSubmit= httpFilterPost('reqSubmit');
$reqLinkFile= $_FILES['reqLinkFile'];
$reqLinkFileTemp = httpFilterPost("reqLinkFileTemp");
$reqLinkFileTempTipe = httpFilterPost("reqLinkFileTempTipe");
$reqLinkFileTempUkuran = httpFilterPost("reqLinkFileTempUkuran");
$reqId = $this->ID;

$FILE_DIR = "uploads/landasan_hukum/";

$rekanan->selectByParams(array("REKANAN_ID"=>$reqRekananId),-1,-1);
$rekanan->firstRow();

$rekanan_akta->selectByParams(array("REKANAN_ID"=>$reqRekananId, "AKTA_TYPE_ID"=>1),-1,-1);
$rekanan_akta->firstRow();
$reqRekananAktaId = $rekanan_akta->getField("REKANAN_AKTA_ID");
$reqNomor = $rekanan_akta->getField("NOMOR");
$reqTanggal = dateToPageCheck($rekanan_akta->getField("TANGGAL"));
$reqNotaris = $rekanan_akta->getField("NOTARIS");
$reqLinkFileTemp= $rekanan_akta->getField("PATH_FILE");
$reqLinkFileTempTipe= $rekanan_akta->getField("TIPE");
$reqLinkFileTempUkuran= $rekanan_akta->getField("UKURAN");
$reqLinkFileTempNama= $rekanan_akta->getField("NAMA_FILE");

$rekanan_akta_perubahan->selectByParams(array("REKANAN_ID"=>$reqRekananId, "AKTA_TYPE_ID"=>2),-1,-1);
$rekanan_akta_perubahan->firstRow();
$reqRekananAktaIdPerubahan = $rekanan_akta_perubahan->getField("REKANAN_AKTA_ID");
$reqNomorAktaPerubahan = $rekanan_akta_perubahan->getField("NOMOR");
$reqTanggalPerubahan = dateToPageCheck($rekanan_akta_perubahan->getField("TANGGAL"));
$reqNamaNotarisPerubahan = $rekanan_akta_perubahan->getField("NOTARIS");
$reqLinkFilePerubahanTemp= $rekanan_akta_perubahan->getField("PATH_FILE");
$reqLinkFilePerubahanTempTipe= $rekanan_akta_perubahan->getField("TIPE");
$reqLinkFilePerubahanTempUkuran= $rekanan_akta_perubahan->getField("UKURAN");
$reqLinkFilePerubahanTempNama= $rekanan_akta_perubahan->getField("NAMA_FILE");

$reqMode = "update";
?>
<script type="text/javascript">
$(document).ready(function() {
  
  $(function(){
    $('#ff').form({
      url:'rekanan_akta_json/registrasi',
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
        //alert(data);return false;
        // $.messager.alert('Info', data, 'info');
        alertSuccess2(data);     
        hideLoad();
        document.location.href = 'main/index/registrasi_rekanan_ijin_usaha';
      }
    });
    
  });
  
});
</script>

<link rel="stylesheet" type="text/css" href="<?=base_url() ?>assets/new/vendors/css/ui/jquery-ui.min.css">
<link rel="stylesheet" type="text/css" href="<?=base_url() ?>assets/new/css/plugins/ui/jqueryui.css">
<!-- <script src="<?=base_url()?>assets/new/js/core/libraries/jquery_ui/jquery-ui.min.js"></script> -->
<!-- <script src="<?=base_url()?>assets/new/js/scripts/ui/jquery-ui/navigations.js"></script> -->
<style type="text/css">
  small { font-weight: bold; font-size: 11.5px }
  .badge-danger { padding: 5 25px  }
</style>

<div class="row">  
  <div class="col-md-12 col-sm-12">
    <div class="card">
      <div class="card-content collapse show border-info border-darken-2">
        <div class="card-body">
          <div class="alert bg-primary alert-icon-left alert-arrow-left alert-dismissible mb-2" role="alert">
            <span class="alert-icon"><i class="fa fa-th"></i></span>  
            <strong>Landasan Hukum</strong>  
          </div> 
          <form id="ff" class="easyui-form form-horizontal" method="post" novalidate enctype="multipart/form-data"> 
            <div class="alert alert-info"><?=translate("Akta Pendirian", "Deed")?> <span class="badge badge-pill badge-danger">wajib</span></div>

            <div class="row">
              <div class="form-group col-md-12 mb-2">
                <label><?=translate("Nomor Akta", "Reference Number")?></label>
                <input type="text" name="reqNomorAkta" id="reqNomorAkta" title="Nomor Akta harus diisi" class="form-control easyui-validatebox span4" value="<?=$reqNomor?>" required />
              </div> 
            </div> 

            <div class="row">
              <div class="form-group col-md-10 mb-2">
                <label><?=translate("Nama Notaris", "Notary Name")?></label>
                <input type="text" name="reqNamaNotaris" id="reqNamaNotaris" title="Nama Notaris harus diisi" class="form-control easyui-validatebox span4" value="<?=$reqNotaris?>" required />
              </div> 

              <div class="form-group col-md-2 mb-2">
                <label style="width: 100%"><?=translate("Tanggal", "Issue Date")?></label>
                <input type="text" style="width:150px" name="reqTanggal" id="reqTanggal" title="Tanggal harus diisi" class="form-control easyui-datebox span2" value="<?=$reqTanggal?>" required />
              </div> 
            </div>  

            <div class="row">
              <div class="form-group col-md-12 mb-2">
                <label style="width: 100%">File (<small> Format file .pdf & Maksimal ukuran file 2MB </small>)</label>
                 <input type="file" name="reqLinkFile" id="reqLinkFilePDF" size="30" <?php if($reqLinkFileTemp == "") { ?> required <?php } ?> class="easyui-validatebox span4"  validType="fileType['pdf']" />
                 <input type="hidden" name="reqLinkFileTemp" value="<?=$reqLinkFileTemp?>">
                 <input type="hidden" name="reqLinkFileTempTipe" value="<?=$reqLinkFileTempTipe?>">
                 <input type="hidden" name="reqLinkFileTempUkuran" value="<?=$reqLinkFileTempUkuran?>">
                 <input type="hidden" name="reqLinkFileTempNama" value="<?=$reqLinkFileTempNama?>">
                 <?php
                 if($reqLinkFileTempNama == "")
                 {}
                 else
                 {
                  if ($reqLinkFileTempNama)
                    echo 'File :'.$reqLinkFileTempNama;
                 }
                 ?> 
                <br><small class="form-text text-muted"> (Akta yang diupload hanya cover, komparisi, susunan pemegang saham, susunan pengurus perusahaan).</small>
              </div> 
            </div>  
             
            <hr>
            <div class="alert alert-info"><?=translate("Akta Perubahan Terakhir", "Deed of The Last Amendment")?> <span class="badge badge-pill badge-danger">jika ada</span></div>
            
            <div class="row">
              <div class="form-group col-md-12 mb-2">
                <label><?=translate("Nomor Akta", "Reference Number")?></label>
                <input type="text" name="reqNomorAktaPerubahan" id="reqNomorAktaPerubahan" title="Nomor Akta " class="form-control easyui-validatebox span4" value="<?=$reqNomorAktaPerubahan?>"  />
              </div> 
            </div>     
            
            <div class="row">
              <div class="form-group col-md-10 mb-2">
                <label><?=translate("Nama Notaris", "Notary Name")?></label>
                <input type="text" name="reqNamaNotarisPerubahan" id="reqNamaNotarisPerubahan" title="Nama Notaris harus diisi" class="form-control easyui-validatebox span4" value="<?=$reqNamaNotarisPerubahan?>"  />
              </div> 
              <div class="form-group col-md-2 mb-2">
                <label style="width: 100%"><?=translate("Tanggal", "Issue Date")?></label>
                <input type="text" style="width: 150px" name="reqTanggalPerubahan" id="reqTanggalPerubahan" title="Tanggal harus diisi" class="form-control easyui-datebox span2" value="<?=$reqTanggalPerubahan?>"  />
              </div> 
            </div>  
            
            <div class="row">
              <div class="form-group col-md-12 mb-2">
                <label style="width: 100%">File (<small> Format file .pdf & Maksimal ukuran file 2MB </small>)</label>
                <input type="file" name="reqLinkFilePerubahan" id="reqLinkFilePerubahanPDF" size="30" <?php if($reqLinkFilePerubahanTemp == "") { ?>  <?php } ?> class="easyui-validatebox span4"  validType="fileType['pdf']" />
                <input type="hidden" name="reqLinkFilePerubahanTemp" value="<?=$reqLinkFilePerubahanTemp?>">
                <input type="hidden" name="reqLinkFilePerubahanTempTipe" value="<?=$reqLinkFilePerubahanTempTipe?>">
                <input type="hidden" name="reqLinkFilePerubahanTempUkuran" value="<?=$reqLinkFilePerubahanTempUkuran?>">
                <input type="hidden" name="reqLinkFilePerubahanTempNama" value="<?=$reqLinkFilePerubahanTempNama?>">
                <?php
                if($reqLinkFileTempNama == "")
                {}
                else
                { 
                  if ($reqLinkFilePerubahanTempNama) {
                    echo 'File :'.$reqLinkFilePerubahanTempNama;
                  }
                }
                ?>         
                <br><small class="form-text text-muted"> (Akta yang diupload hanya cover, laporan kemenkumham, susunan pemegang saham, susunan pengurus perusahaan).</small>
              </div> 
            </div>  

            <?php
              $rekanan_sertifikat->selectByParams(array("REKANAN_ID"=>$reqRekananId, "SERTIFIKAT_TIPE"=>"PENGESAHAN_BADAN_USAHA"),-1,-1);
              $rekanan_sertifikat->firstRow();
              $reqPengesahanSertifikatId = $rekanan_sertifikat->getField("REKANAN_SERTIFIKAT_ID");
              $reqNomorPengesahan = $rekanan_sertifikat->getField("NOMOR");
              $reqTanggalPengesahan = dateToPageCheck($rekanan_sertifikat->getField("TANGGAL"));
              $reqTanggalBerlakuPengesahan = dateToPageCheck($rekanan_sertifikat->getField("BERLAKU"));
              $reqLinkFilePengesahanTempNama = $rekanan_sertifikat->getField("NAMA_FILE");
              $reqLinkFilePengesahanTemp= $rekanan_sertifikat->getField("PATH_FILE");
              $reqLinkFilePengesahanTempTipe= $rekanan_sertifikat->getField("TIPE");
              $reqLinkFilePengesahanTempUkuran= $rekanan_sertifikat->getField("UKURAN");
            ?>

            <hr>
            <div class="alert alert-info"><?=translate("Pengesahan Badan Usaha", "Business Entity Endorsement,")?> <span class="badge badge-pill badge-danger">jika ada</span></div>
            
            <div class="row">
              <div class="form-group col-md-12 mb-2">
                <label><?=translate("Nomor", "Reference Number")?></label>
                <input type="text" name="reqNomorPengesahan" id="reqNomorPengesahan" title="Nomor Akta harus diisi" class="form-control easyui-validatebox span4" value="<?=$reqNomorPengesahan?>" />
              </div> 
            </div>  
            
            <div class="row">
              <div class="form-group col-md-2 mb-2">
                <label style="width: 100%"><?=translate("Tanggal", "Issue Date")?></label>
                <input type="text" style="width: 150px" name="reqTanggalPengesahan" id="reqTanggalPengesahan" title="Tanggal harus diisi" class="form-control easyui-datebox span2" value="<?=$reqTanggalPengesahan?>" />
              </div> 
              <div class="form-group col-md-2 mb-2">
                <label style="width: 100%"><?=translate("Tanggal Berlaku", "Issue Date")?></label>
                <input type="text" style="width: 150px" name="reqTanggalBerlakuPengesahan" id="reqTanggalBerlakuPengesahan" title="Tanggal harus diisi" class="form-control easyui-datebox span2" value="<?=$reqTanggalBerlakuPengesahan?>" />
              </div> 
              <div class="form-group col-md-8 mb-2">
                <label style="width: 100%">File (<small> Format file .pdf & Maksimal ukuran file 2MB </small>)</label>
                 <input type="file" name="reqLinkFilePengesahan" id="reqLinkFilePengesahanPDF" size="30" <?php if($reqLinkFilePengesahanTemp == "") { ?> <?php } ?> class="easyui-validatebox span4"  validType="fileType['pdf']" /> 
                 <input type="hidden" name="reqLinkFilePengesahanTemp" value="<?=$reqLinkFilePengesahanTemp?>">
                 <input type="hidden" name="reqLinkFilePengesahanTempTipe" value="<?=$reqLinkFilePengesahanTempTipe?>">
                 <input type="hidden" name="reqLinkFilePengesahanTempUkuran" value="<?=$reqLinkFilePengesahanTempUkuran?>">
                 <input type="hidden" name="reqLinkFilePengesahanTempNama" value="<?=$reqLinkFilePengesahanTempNama?>">
                 <?php
                 if($reqLinkFileTempNama == "")
                 {}
                 else
                 {
                  if ($reqLinkFilePengesahanTempNama) { 
                    echo 'File :'.$reqLinkFilePengesahanTempNama;
                  }
                 }
                 ?>           
              </div> 
            </div>  

            <?php
              $rekanan_sertifikat_domisili->selectByParams(array("REKANAN_ID"=>$reqRekananId, "SERTIFIKAT_TIPE"=>"SURAT_DOMISILI"),-1,-1);
              $rekanan_sertifikat_domisili->firstRow();
              $reqDomisiliId = $rekanan_sertifikat_domisili->getField("REKANAN_SERTIFIKAT_ID");
              $reqNomorDomisili = $rekanan_sertifikat_domisili->getField("NOMOR");
              $reqTanggalDomisili = dateToPageCheck($rekanan_sertifikat_domisili->getField("TANGGAL"));
              $reqTanggalBerlakuDomisili = dateToPageCheck($rekanan_sertifikat_domisili->getField("BERLAKU"));
              $reqLinkFileDomisiliTempNama = $rekanan_sertifikat_domisili->getField("NAMA_FILE");
              $reqLinkFileDomisiliTemp= $rekanan_sertifikat_domisili->getField("PATH_FILE");
              $reqLinkFileDomisiliTempTipe= $rekanan_sertifikat_domisili->getField("TIPE");
              $reqLinkFileDomisiliTempUkuran= $rekanan_sertifikat_domisili->getField("UKURAN");
            
            ?>

            <hr>
            <div class="alert alert-info"><?=translate("Surat Domisili", "Domicile")?> <span class="badge badge-pill badge-danger">jika ada</span></div>
            
            <div class="row">
              <div class="form-group col-md-12 mb-2">
                <label><?=translate("Nomor", "Reference Number")?></label>
                <input type="text" name="reqNomorDomisili" id="reqNomorDomisili" title="Nomor Akta harus diisi" class="form-control easyui-validatebox span4" value="<?=$reqNomorDomisili?>"  />
              </div> 
            </div>   
            
            <div class="row">
              <div class="form-group col-md-2 mb-2">
                <label style="width: 100%"><?=translate("Tanggal", "Issue Date")?></label>
                <input type="text" style="width: 150px" name="reqTanggalDomisili" id="reqTanggalDomisili" title="Tanggal harus diisi" class="form-control easyui-datebox span2" value="<?=$reqTanggalDomisili?>"  />
              </div> 
              <div class="form-group col-md-2 mb-2">
                <label style="width: 100%"><?=translate("Tanggal Berlaku", "Issue Date")?></label>
                <input type="text"  style="width: 150px" name="reqTanggalBerlakuDomisili" id="reqTanggalBerlakuDomisili" title="Tanggal harus diisi" class="form-control easyui-datebox span2" value="<?=$reqTanggalBerlakuDomisili?>"  />
              </div> 
              <div class="form-group col-md-8 mb-2">
                <label style="width: 100%">File (<small> Format file .pdf & Maksimal ukuran file 2MB </small>)</label>
                 <input type="file" name="reqLinkFileDomisili" id="reqLinkFileDomisiliPDF" size="30" <? if($reqLinkFileDomisiliTemp == "") { ?>  <? } ?> class="easyui-validatebox span4"  validType="fileType['pdf']" />
                 <input type="hidden" name="reqLinkFileDomisiliTemp" value="<?=$reqLinkFileDomisiliTemp?>">
                 <input type="hidden" name="reqLinkFileDomisiliTempTipe" value="<?=$reqLinkFileDomisiliTempTipe?>">
                 <input type="hidden" name="reqLinkFileDomisiliTempUkuran" value="<?=$reqLinkFileDomisiliTempUkuran?>">
                 <input type="hidden" name="reqLinkFileDomisiliTempNama" value="<?=$reqLinkFileDomisiliTempNama?>">
                 <?php
                 if($reqLinkFileTempNama == "")
                 {}
                 else
                 {
                  if ($reqLinkFileDomisiliTempNama) {
                    echo 'File :'.$reqLinkFileDomisiliTempNama;
                  }
                 }
                 ?>                                         
              </div> 
            </div>  

            <?php
              $rekanan_sertifikat_tanda_daftar->selectByParams(array("REKANAN_ID"=>$reqRekananId, "SERTIFIKAT_TIPE"=>"TANDA_DAFTAR_PERUSAHAAN"),-1,-1);
              $rekanan_sertifikat_tanda_daftar->firstRow();
              $reqTandaDaftarId = $rekanan_sertifikat_tanda_daftar->getField("REKANAN_SERTIFIKAT_ID");
              $reqNomorTandaDaftar = $rekanan_sertifikat_tanda_daftar->getField("NOMOR");
              $reqTanggalTandaDaftar = dateToPageCheck($rekanan_sertifikat_tanda_daftar->getField("TANGGAL"));
              $reqTanggalBerlakuTandaDaftar = dateToPageCheck($rekanan_sertifikat_tanda_daftar->getField("BERLAKU"));
              $reqLinkFileTandaDaftarTempNama = $rekanan_sertifikat_tanda_daftar->getField("NAMA_FILE");
              $reqLinkFileTandaDaftarTemp= $rekanan_sertifikat_tanda_daftar->getField("PATH_FILE");
              $reqLinkFileTandaDaftarTempTipe= $rekanan_sertifikat_tanda_daftar->getField("TIPE");
              $reqLinkFileTandaDaftarTempUkuran= $rekanan_sertifikat_tanda_daftar->getField("UKURAN");
            ?>

            <hr>
            <div class="alert alert-info"><?=translate("Tanda Daftar Perusahaan", "Certificate of Company Registration")?> <span class="badge badge-pill badge-danger">jika ada</span></div>
            
            <div class="row">
              <div class="form-group col-md-12 mb-2">
                <label><?=translate("Nomor", "Reference Number")?></label>
                <input type="text" name="reqNomorTandaDaftar" id="reqNomorTandaDaftar" title="Nomor Akta harus diisi" class="form-control easyui-validatebox span4" value="<?=$reqNomorTandaDaftar?>"  />
              </div> 
            </div>    
            
            <div class="row">
              <div class="form-group col-md-2 mb-2">
                <label style="width: 100%"><?=translate("Tanggal", "Issue Date")?></label>
                <input type="text"  style="width: 150px" name="reqTanggalTandaDaftar" id="reqTanggalTandaDaftar" title="Tanggal harus diisi" class="form-control easyui-datebox span2" value="<?=$reqTanggalTandaDaftar?>"  />
              </div> 
              <div class="form-group col-md-2 mb-2">
                <label style="width: 100%"><?=translate("Tanggal Berlaku", "Issue Date")?></label>
                <input type="text"  style="width: 150px" name="reqTanggalBerlakuTandaDaftar" id="reqTanggalBerlakuTandaDaftar" title="Tanggal harus diisi" class="form-control easyui-datebox span2" value="<?=$reqTanggalBerlakuTandaDaftar?>"  />
              </div> 
              <div class="form-group col-md-8 mb-2">
                <label style="width: 100%">File (<small> Format file .pdf & Maksimal ukuran file 2MB </small>)</label>
                 <input type="file" name="reqLinkFileTandaDaftar" id="reqLinkFileTandaDaftarPDF" size="30" <?php if($reqLinkFileTandaDaftarTemp == "") { ?>  <?php } ?> class="easyui-validatebox span4"  validType="fileType['pdf']" />
                 <input type="hidden" name="reqLinkFileTandaDaftarTemp" value="<?=$reqLinkFileTandaDaftarTemp?>">
                 <input type="hidden" name="reqLinkFileTandaDaftarTempTipe" value="<?=$reqLinkFileTandaDaftarTempTipe?>">
                 <input type="hidden" name="reqLinkFileTandaDaftarTempUkuran" value="<?=$reqLinkFileTandaDaftarTempUkuran?>">
                 <input type="hidden" name="reqLinkFileTandaDaftarTempNama" value="<?=$reqLinkFileTandaDaftarTempNama?>">
                 <?php
                 if($reqLinkFileTempNama == "")
                 {}
                 else
                 {
                  if ($reqLinkFileTandaDaftarTempNama) {
                   echo 'File :'.$reqLinkFileTandaDaftarTempNama;
                  }
                 }
                 ?>               
              </div> 
            </div>   
           
            <div class="form-actions" >
              <input type="hidden" name="reqAktaType" value="<?=$reqAktaType?>" />
              <input type="hidden" name="reqId" value="<?=$reqId?>" />
              <input type="hidden" name="reqRekananId" value="<?=$reqRekananId?>" />
              <input type="hidden" name="reqRekananAktaId" value="<?=$reqRekananAktaId?>"/>
              <input type="hidden" name="reqRekananAktaIdPerubahan" value="<?=$reqRekananAktaIdPerubahan?>" />
              <input type="hidden" name="reqPengesahanSertifikatId" value="<?=$reqPengesahanSertifikatId?>" />
              <input type="hidden" name="reqDomisiliId" value="<?=$reqDomisiliId?>" />
              <input type="hidden" name="reqTandaDaftarId" value="<?=$reqTandaDaftarId?>" />
              <input type="hidden" name="reqMode" value="<?=$reqMode?>" />
              <input type="hidden" name="reqId" value="<?=$reqId?>">
              <a href="main/index/registrasi_rekanan_identitas_perusahaan" class="btn btn-danger mr-1 text-white"> <i class="fa fa-arrow-left"></i> Kembali </a> 
                <button type="submit" class="btn btn-primary pull-right"><?=translate("Lanjut", "Next")?> <i class="fa fa-arrow-right"></i></button>
            </div>   
          </form>        
        </div>
      </div>
    </div>
  </div>
</div>  