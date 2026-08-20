<?php
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 * 
 */
$this->libsession->cekSession();   

// if($this->USER_TYPE_ID == "")
//     redirect("main");

if ($this->REKANAN_TIPE_ID != '7')
    redirect(base_url('main'));
    
/* INCLUDE FILE */
$this->load->library("crfs_protect"); $csrf = new crfs_protect('_crfs_rr');
$this->load->library("kauth");  $userLogin = new kauth(); 
$this->load->model("IjinUsaha");
$this->load->model("Rekanan");
include_once("functions/string.func.php");
include_once("functions/date.func.php");
include_once("functions/default.func.php");

/* VARIABLE */
$submitSimpan	= httpFilterPost("submitSimpan");
$reqBatal	= httpFilterPost("reqBatal");
$reqNomorIjin = httpFilterPost('reqNomorIjin');
$reqTanggalIjin = httpFilterPost('reqTanggalIjin');
$reqTanggalBerakhir = httpFilterPost('reqTanggalBerakhir');
$reqInstansiPemberiIjin = httpFilterPost('reqInstansiPemberiIjin');
$reqId = httpFilterRequest('reqId');
$reqBidangUsahaId = $_POST["reqBidangUsahaId"];
$reqLinkFile= $_FILES['reqLinkFile'];
$reqLinkFileTemp = httpFilterPost("reqLinkFileTemp");
$reqLinkFileTempTipe = httpFilterPost("reqLinkFileTempTipe");
$reqLinkFileTempUkuran = httpFilterPost("reqLinkFileTempUkuran");

//$reqRekananId = $this->ID;
/* create objects */
$rekanan = new Rekanan();

$rekanan->selectByParams(array("REKANAN_ID"=> $this->ID),-1,-1);
$rekanan->firstRow(); 
$reqNamaPerusahaan = $rekanan->getField("NAMA");
$reqAlamat = $rekanan->getField("ALAMAT");
$reqKota = $rekanan->getField("KOTA");
$reqProvinsi = $rekanan->getField("REGION");
$reqKodePos = $rekanan->getField("KODEPOS");
$reqStatus = $rekanan->getField("STATUS_CP");
$reqNPWP = $rekanan->getField("NPWP");
$reqLinkFileNPWPTemp = $rekanan->getField("NAMA_FILE_NPWP");
$reqPKP = $rekanan->getField("PKP");
$reqKTP = $rekanan->getField("KTP");
$reqKTPFile = $rekanan->getField("KTP_FILE");
$reqNamaFileKTP = $rekanan->getField("NAMA_FILE_KTP");
$reqMasaBerlakuPKP = $rekanan->getField("PKP_TANGGAL");
$reqLinkFileTemp = $rekanan->getField("PKP_TANGGAL");
$reqMasaBerlakuPKP = $rekanan->getField("PKP_TANGGAL"); 
$reqLinkFileTemp = $rekanan->getField("NAMA_FILE_PKP");
$reqNomorTelepon = $rekanan->getField("TELEPON_FULL");
$reqNomorFax = $rekanan->getField("FAX_FULL");
$reqEmail = $rekanan->getField("EMAIL");
$reqKualifikasi = $rekanan->getField("REKANAN_KUALIFIKASI");
$reqKontakPerson = $rekanan->getField("KONTAK_PERSON");
$reqKontakPersonHp = $rekanan->getField("KONTAK_PERSON_HP");
$reqWebsite = $rekanan->getField("WEBSITE");

?>
<script type="text/javascript">
$(document).ready(function() {
	
	$(function(){
		$('#ff').form({
			url:'rekanan_json/registrasi',
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
				arrData = data.split("-");
				
				if(arrData[0] == "0")
					$.messager.alert('Informasi',arrData[2],'info');				
				else
					document.location.href = 'login/action/?reqUser='+arrData[0]+'&reqPasswd='+arrData[1];
                
                hideLoad();
			}
		});
		
	});

	$(function(){
		$('input[name="reqStatus"]').on('change', function() {
			  var radioValue = $('input[name="reqStatus"]:checked').val();        
			  if(radioValue == "0")
			  {
				$( "input[name*='reqSuratKuasa']" ).prop("disabled", "disabled");  
				$( "input[name*='reqSuratKuasa']" ).val("");  
				$("#reqSuratKuasaTanggal").datebox({ disabled:true, required:false });
				$("#reqSuratKuasaNomor").validatebox({ required:false });
				$("#reqSuratKuasaNotaris").validatebox({ required:false });
			  } 
			  else
			  {
				$( "input[name*='reqSuratKuasa']" ).prop("disabled", "");  
				$("#reqSuratKuasaTanggal").datebox({ disabled:false, required:true });
				$("#reqSuratKuasaNomor").validatebox({ required:true });
				$("#reqSuratKuasaNotaris").validatebox({ required:true });
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
</style>

<div class="row">  
  <div class="col-md-12 col-sm-12">
    <div class="card">
      <div class="card-content collapse show border-info border-darken-2">
        <div class="card-body">
          <div class="alert bg-primary alert-icon-left alert-arrow-left alert-dismissible mb-2" role="alert">
            <span class="alert-icon"><i class="fa fa-th"></i></span>  
            <strong><?=translate("Pendaftaran Perorangan", "Registration")?></strong>  
            <div class="badge badge-glow badge-pill badge-warning">
                <a href="main/index/data_administrasi_umum_ubah_profile_perorangan">
                    <span class="fa fa-pencil text-white font-medium-2 icon-line-height" data-toggle="tooltip" data-placement="top" title="edit"></span>
                </a>
            </div>
          </div> 
        <form id="ff" class="easyui-form form-horizontal" method="post" novalidate enctype="multipart/form-data">
        	
            <table class="table table-bordered table-hover">
                <tbody>
                    <tr>
                        <td width="23%">Nama Perorangan:</td>
                        <td>
                            <?=$reqNamaPerusahaan?>
                        </td>
                    </tr>
                    <tr>
                        <td><?=translate("Alamat", "Address")?>:</td>
                        <td>
                    		<?=$reqAlamat?> 
                        </td>
                    </tr>
                    <tr>
                        <td><?=translate("Kota", "City")?>:</td>
                        <td>
                            <?=$reqKota?> 
                        </td>
                    </tr>
                    <tr>
                        <td><?=translate("Provinsi", "Province")?>:</td>
                        <td>
                            <?=$reqProvinsi?>
                        </td>
                    </tr>
                    <tr>
                        <td><?=translate("Kode Pos", "Postal Code")?>:</td>
                        <td>
                            <?=$reqKodePos?>
                        </td>
                    </tr> 
                    <tr>
                        <td><?=translate("NPWP", "Taxpayer Registration Number")?>:</td>
                        <td>
                            <?=$reqNPWP?> 
                        </td>
                    </tr>
                    <tr>
                        <td>File NPWP:</td>
                        <td>
                            <?php
                                $arrFile = explode(";", $rekanan->getField("NAMA_FILE_NPWP"));
                                for($iFile=0;$iFile<count($arrFile);$iFile++)
                                {
                            ?>
                                    <?=$arrFile[$iFile]?>
                                    <?php if ($arrFile[$iFile]) { ?>
                                    <a href="<?= base_url('uploads/rekanan/').$rekanan->getField("NPWP_FILE") ?>" class="badge badge-primary">Download file NPWP</a>
                                    <?php } ?>
                            <?php
                                }
                            ?> 
                        </td>
                    </tr>
                    <tr>
                        <td>KTP:</td>
                        <td>
                            <?=$reqKTP?> 
                        </td>
                    </tr>
                    <tr>
                        <td>File KTP:</td>
                        <td>
                            <?= $reqNamaFileKTP ?>
                            <?php if ($reqKTP) { ?>
                            <a href="<?= base_url('uploads/rekanan/').$reqKTPFile ?>" class="badge badge-primary">Download file KTP</a>
                            <?php } ?>
                        </td>
                    </tr> 
                    <tr>
                        <td><?=translate("PKP", "PKP")?>:</td>
                        <td>
                            <?=$reqPKP?>
                        </td>
                    </tr>
                    <tr>
                        <td><?=translate("Tanggal", "Date")?>:</td>
                        <td>
                            <?=getFormattedDateJson($reqMasaBerlakuPKP)?>
                        </td>
                    </tr>
                    <tr>
                        <td>File PKP:</td>
                        <td>
                        <?php
                            $arrFile = explode(";", $rekanan->getField("NAMA_FILE_PKP"));
                            for($iFile=0;$iFile<count($arrFile);$iFile++)
                            {
                        ?>
                                <?=$arrFile[$iFile]?>
                                <?php if ($arrFile[$iFile]) { ?>
                                <a href="<?= base_url('uploads/rekanan/').$rekanan->getField("PKP_FILE") ?>" class="badge badge-primary">Download file PKP</a>
                                <?php } else { echo "-";} ?>
                        <?php
                            }
                        ?>  
                        </td>
                    </tr>
                    <tr>
                        <td><?=translate("No. telepon", "Telephone")?>:</td>
                        <td>
                            <?=$reqNomorTelepon?>
                        </td>
                    </tr>
                    <tr>
                        <td><?=translate("No. fax", "Faximile")?>:</td>
                        <td>
                            <?=$reqNomorFax?>
                        </td>
                    </tr>
                    <tr>
                        <td><?=translate("Kontak", "Contact")?>:</td>
                        <td>
                            <?=$reqKontakPerson?>
                        </td>
                    </tr>
                    <tr>
                        <td><?=translate("Hp.", "Handphone")?>:</td>
                        <td>
                            <?=$reqKontakPersonHp?>
                        </td>
                    </tr>
                    <tr>
                        <td>E-mail:</td>
                        <td>
                            <?=$reqEmail?> 
                        </td>
                    </tr>
                    <tr>
                        <td>Website:</td>
                        <td>
                            <?=$reqWebsite?>
                        </td>
                    </tr>
                    <tr>
                        <td><?=translate("Kualifikasi", "Qualification")?>:</td>
                        <td>
                            <?=$reqKualifikasi?>
                        </td>
                    </tr> 
                </tbody> 
            </table>      

            <div class="form-actions" style="margin-bottom: 2%">
            	<input type="hidden" name="reqId" value="<?=$reqId?>">
            	<?php /*?><input type="hidden" name="reqBahasa" value="ID"><?php */?>
                <a href="main/index/registrasi_rekanan_cv"  class="btn btn-primary pull-right"><?=translate("Lanjut", "Next")?> <i class="fa fa-arrow-right"></i></a>
            </div> 
               <?php /*?> <button type="submit" value="<?=translate("Simpan", "Save")?>" class="btn-lanjut pull-right"><?php */?>
                          
        </form>               	
        </div>
      </div>
    </div>
  </div>
</div> 