<?php
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 * 
 */
$this->load->library("crfs_protect"); $csrf = new crfs_protect('_crfs_rr');
$reqKualifikasi='';
?>
<script type="text/javascript">
$(document).ready(function() {
	
	$(function(){
		$('#ff').form({
			url:'<?= base_url('rekanan_json/registrasi') ?>',
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
        // $.messager.alert('Info', data, 'info'); 
        hideLoad();
				arrData = data.split("-");
				if(arrData[0] == "0")
					$.messager.alert('Informasi',arrData[2],'info');				
				else
					document.location.href = 'login/action/?reqUser='+arrData[0]+'&reqPasswd='+arrData[1]; 
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

    $('input[name="reqJenisPerusahaan"]').on('change', function() {
      alert('aaa');
		});

	});	
		$("#chk_agreement").click(countChecked);
	
});

function countChecked() {
	  var n = $("#chk_agreement:checked").length;
	  //alert(n);
	  if(n){
		  $("#reqSubmit").show(0);
	  }else{
		  $("#reqSubmit").hide(0);
	  }
}

function test() {
  alert('bbbb');
}
</script>

<section id="backColor">
  <div class="row"> 

    <div class="col-md-8 col-sm-12">
      <div class="card border-bottom-primary" style="zoom: 1;"> 
        <div class="card-header">
          <h4 class="card-title"><i class="ft-user"></i> Registrasi <small>Penyedia</small></h4>
          <div class="heading-elements">
            <ul class="list-inline mb-0">
              <li><a data-action="expand"><i class="ft-maximize"></i></a></li>
              <!-- <li><a data-action="close"><i class="ft-x"></i></a></li> -->
            </ul>
          </div>
        </div>
        <div class="card-body"> 
          <div class="form-body">

            <form id="ff" class="easyui-form form-horizontal" method="post" novalidate enctype="multipart/form-data">

              <div class="row">
                <div class="form-group col-md-3 mb-2">
                  <label style="width: 100%">Bentuk Usaha</label>
                  <input type="text" name="reqJenisPerusahaan" id="reqJenisPerusahaan" class="form-control easyui-combobox span1"  
                  data-options="valueField:'id',textField:'text',url:'rekanan_tipe_json/combo',
                                          onSelect: function(rec){
                                          if(rec.id === '7') {
                                            $('#labelNamaPerusahaan').text('Nama Perorangan');
                                            $('#fstatus').hide();
                                            $('#fkualifikasi').hide();
                                            $('#kualifikasiKecil').attr('checked', 'checked');
                                          } else {
                                            $('#labelNamaPerusahaan').text('Nama Perusahaan');
                                            $('#fstatus').show();
                                            $('#fkualifikasi').show();  
                                          }
                                          }"
                  style="width: 200% !important" required />
                </div>
                <div class="form-group col-md-9 mb-2">
                  <label for="projectinput6" id="labelNamaPerusahaan">Nama Perusahaan</label> 
                  <input type="text" name="reqNamaPerusahaan" maxlength="255" accesskey="n" title="Nama Perusahaan harus diisi" class="form-control easyui-validatebox span7" value="<?=isset($reqNamaPerusahaan)?$reqNamaPerusahaan:''?>" id="reqNamaPerusahaan" required />
                </div>
              </div>

              <div class="row">
                <div class="form-group col-md-12 mb-2">
                  <label>Alamat</label>
                  <textarea name="reqAlamat" rows="5" accesskey="l" id="reqAlamat" title="Alamat harus diisi" class="form-control easyui-validatebox span6" required ><?=isset($reqAlamat)?$reqAlamat:''?></textarea>
                </div> 
              </div> 

              <div class="row">
                <div class="form-group col-md-4 mb-2">
                  <label style="width: 100%">Provinsi</label>
                  <input type="text" name="reqRegionId" class="easyui-combobox span3" data-options="valueField:'id',textField:'text',url:'region_json/combo'" style="width: 250%" required value="<?=isset($reqRegionId)?$reqRegionId:''?>" />
                </div> 
                <div class="form-group col-md-5 mb-2">
                  <label>Kota</label>
                  <input type="text" name="reqKota" accesskey="t" value="<?=isset($reqKota)?$reqKota:''?>" id="reqKota" title="Kota harus diisi" class="form-control easyui-validatebox span3" required />
                </div> 
                <div class="form-group col-md-3 mb-2">
                  <label style="width: 100%">Kode Pos</label>
                  <input type="text" name="reqKodepos" value="<?=isset($reqKodepos)?$reqKodepos:''?>" title="Kodepos harus diisi" class="form-control easyui-validatebox span3" maxlength="6" required  />
                </div> 
              </div>

              <div class="row" id="fstatus">
                <div class="form-group col-md-12 mb-2">
                  <label style="width: 100%">Status</label>
                    <input value="0" name="reqStatus" id="reqStatus-0" type="radio" checked /> Pusat &nbsp;
                    <input value="1" name="reqStatus" id="reqStatus-1" type="radio" /> Cabang &nbsp;
                    <input value="2" name="reqStatus" id="reqStatus-2" type="radio" /> Join Operation
                </div> 
              </div>

              <div class="row">
                <div class="form-group col-md-7 mb-2">
                  <label>NPWP</label>
                  <input type="hidden" id="reqNPWPStatus" value="0" />
                  <input type="text" id="reqNPWP" name="reqNPWP"  class="form-control easyui-validatebox span3" accesskey="n" value="<?=isset($reqNPWP)?$reqNPWP:''?>" onkeydown="return format_npwp(event, 'reqNPWP');" maxlength="20" validType="remote['fungsi_json/check_npwp','reqNPWP', $('input[name=\'reqStatus\']:checked').val()]" invalidMessage="NPWP sudah digunakan." required /> 
                </div> 
                <div class="form-group col-md-5 mb-2">
                  <label>File NPWP</label> 
                  <input type="file" name="reqLinkFileNPWP" id="reqLinkFilePDF" size="30"  required  class="easyui-validatebox"  validType="fileType['pdf']" />
                  <small> <br>Format file .pdf & Maksimal ukuran file 2MB </small>
                  <input type="hidden" name="reqLinkFileNPWPTemp" value="<?=isset($reqLinkFileNPWPTemp)?$reqLinkFileNPWPTemp:''?>">
                  <input type="hidden" name="reqLinkFileTempNPWPNama" value="<?=isset($reqLinkFileTempNPWPNama)?$reqLinkFileTempNPWPNama:''?>">
                </div> 
              </div>

              <div class="row">
                <div class="form-group col-md-5 mb-2">
                  <label> PKP </label>
                  <input type="hidden" id="reqNPWPStatus" value="0" />
                  <input type="text" id="reqPKP" name="reqPKP"  class="form-control easyui-validatebox span3" accesskey="n" value="<?=isset($reqPKP)?$reqPKP:''?>"  maxlength="50"   /> 
                </div> 
                <div class="form-group col-md-2 mb-2">
                  <label style="width: 100%"> Tanggal </label> 
                  <input type="text" style="width:120px" name="reqMasaBerlakuPKP" title="Tanggal harus diisi" id="reqMasaBerlakuPKP" class="form-control easyui-datebox" value="<?=isset($reqMasaBerlakuPKP)?$reqMasaBerlakuPKP:''?>"  />
                </div> 

                <div class="form-group col-md-5 mb-2">
                  <label style="width: 100%"> File PKP </label>
                  <input type="file" name="reqLinkFile" id="reqLinkFilePDF" size="30" maxlength="1" class="easyui-validatebox"  validType="fileType['pdf']" />
                  <small> <br>Format file .pdf & Maksimal ukuran file 2MB </small>
                  <input type="hidden" name="reqLinkFileTemp" value="<?=isset($reqLinkFileTemp)?$reqLinkFileTemp:''?>">
                  <input type="hidden" name="reqLinkFileTempNama" value="<?=isset($reqLinkFileTempNama)?$reqLinkFileTempNama:''?>">
                </div> 
              </div>

              <div class="row">
                <div class="form-group col-md-2 mb-2">
                  <label> No. Telepon </label>
                  <input type="text" name="reqKodeTelepon" maxlength="4" value="<?=isset($reqKodeTelepon)?$reqKodeTelepon:''?>" accesskey="o" onkeypress="return isNumberKey(event)"  id="reqKodeTelepon" title="Kode telepon harus diisi"  class="form-control easyui-validatebox span1" placeholder="Kode Area"  />
                </div> 
                <div class="form-group col-md-10 mb-2">
                  <label> <small>(kode area tidak perlu diisi jika nomor telepon yang dicantumkan adalah nomor ponsel).</small></label>
                  <input type="text" name="reqNomorTelepon" maxlength="11" value="<?=isset($reqNomorTelepon)?$reqNomorTelepon:''?>" onkeypress="return isNumberKey(event)"  id="reqNomorTelepon" title="Nomor telepon harus diisi"  class="form-control easyui-validatebox span3" required />
                </div> 
              </div>

              <div class="row">
                <div class="form-group col-md-2 mb-2">
                  <label> No. Fax </label>
                  <input type="text" name="reqKodeFax" class="form-control easyui-validatebox span1" onkeypress="return isNumberKey(event)"  maxlength="4" value="<?=isset($reqKodeFax)?$reqKodeFax:''?>" accesskey="f" id="reqKodeFax" />
                </div> 
                <div class="form-group col-md-10 mb-2">
                  <label>&nbsp;</label>
                  <input type="text" name="reqNomorFax" class="form-control easyui-validatebox span3" onkeypress="return isNumberKey(event)"  maxlength="11" value="<?=isset($reqNomorFax)?$reqNomorFax:''?>" id="reqNomorFax"  />
                </div> 
              </div>

              <div class="row">
                <div class="form-group col-md-6 mb-2">
                  <label> Kontak Person <small>(Nama)</small></label>
                  <input type="text" name="reqKontakPerson" class="form-control easyui-validatebox span3" value="<?=isset($reqKontakPerson)?$reqKontakPerson:''?>" id="reqKontakPerson" />
                </div> 
                <div class="form-group col-md-6 mb-2">
                  <label> No. HP </label>
                  <input type="text" name="reqKontakPersonHp" class="form-control easyui-validatebox span3" maxlength="15" value="<?=isset($reqKontakPersonHp)?$reqKontakPersonHp:''?>" id="reqKontakPersonHp"  />
                </div>
              </div>

              <div class="row">
                <div class="form-group col-md-6 mb-2">
                  <label> Email </label>
                  <input type="text" name="reqEmail" accesskey="e" value="<?=isset($reqEmail)?$reqEmail:''?>" id="reqEmail" class="form-control easyui-validatebox span4" data-options="required:true,validType:['email','remote[\'fungsi_json/check_email\',\'reqEmail\']']" invalidMessage="Format email salah atau email sudah digunakan." required />
                </div> 
                <div class="form-group col-md-6 mb-2">
                  <label> Website </label>
                  <input type="text" name="reqWebsite" accesskey="e" value="<?=isset($reqWebsite)?$reqWebsite:''?>" id="reqWebsite" class="form-control easyui-validatebox span4" />
                </div> 
              </div>

              <div class="row" id="fkualifikasi">
                <div class="form-group col-md-12 mb-2">
                  <label style="width: 100%"> Kualifikasi </label>
                  <input value="1" <?php if($reqKualifikasi == 1 || $reqKualifikasi == '') echo 'checked'?> name="reqKualifikasi" type="radio"  id="kualifikasiKecil"/> Kecil &nbsp;
                  <input value="2" name="reqKualifikasi" <?php if($reqKualifikasi == 2) echo 'checked'?> type="radio" id="kualifikasiNonKecil"/> Non-Kecil
                </div> 
              </div>

              <div class="row">
                <div class="form-group col-md-6 mb-2">
                  <label> Username </label>
                  <input type="text" name="reqUserLogin" accesskey="p" value="<?=isset($reqUserLogin)?$reqUserLogin:''?>" id="reqUserLogin" title="User login harus diisi" class="form-control easyui-validatebox span4" validType="remote['fungsi_json/check_username','reqUserLogin']" invalidMessage="Username sudah digunakan." required maxlength="50" />
                </div> 
                <div class="form-group col-md-6 mb-2">
                  <label> Password </label>
                  <input type="password" name="reqPassword" accesskey="p" value="<?=isset($reqPassword)?$reqPassword:''?>" id="reqPassword" title="Password harus diisi" class="form-control easyui-validatebox span4" required  data-options="required:true,validType:['length','number']" />
                </div> 
              </div>

              <div class="row">
                <div class="form-group col-md-3 mb-2">
                  <label> Ketik kode yang ditampilkan </label>
                  <img src="<?= base_url() ?>main/loadUrl/main/CaptchaSecurityImages/?&width=100&height=40&characters=4" id="captchaImage2" />&nbsp;&nbsp;&nbsp;<img src="images/Refresh.png" width="25" height="25" onclick="reloadCaptcha2()" style="cursor:pointer" title="refresh captcha">
                </div> 
                <div class="form-group col-md-9 mb-2">
                  <label> &nbsp; </label>
                  <input id="label" required name="security_code" type="text" title="Kode harus diisi" class="form-control easyui-validatebox" validType="remote['fungsi_json/captcha_validation', 'security_code']" invalidMessage="Kode validasi salah."  />
                </div> 
              </div> 
                <?=$csrf->echoInputField();?>


              <div class="form-actions">
                <input name="reqSetuju" type="checkbox" id="chk_agreement" accesskey="e" value="1" style="margin-bottom: 5%; cursor: pointer;" />
                Dengan ini saya menyatakan bahwa data-data tersebut adalah data yang benar dan dapat dipertanggungjawabkan. <br>

                <a href="main" class="btn btn-danger mr-1 text-white"> <i class="fa fa-arrow-left"></i> Kembali </a>  
                <button type="submit" class="btn btn-primary" name="reqSubmit" id="reqSubmit" style="display:none"> 
                  <i class="fa fa-check-square-o"></i> <?=translate("Daftar", "Register")?>
                </button>
              </div>
               <?=$csrf->echoInputField();?>

            </form>

          </div>
        </div>
      </div>
    </div> 

    <div class="col-md-4 col-sm-12">
      <div class="card border-bottom-primary" style="zoom: 1;"> 
        <div class="card-header">
          <div class="alert alert-icon-right alert-info alert-dismissible" role="alert">
            <span class="alert-icon"><i class="fa fa-info"></i></span>
            <strong>Penjelasan Registrasi.</strong>
          </div>
        </div>
        <div class="card-body"> 
          <div class="card-text">
              <ul class="list-style-square">
                  <li>Silahkan isi form Registrasi dengan lengkap dan sesuai dengan data perusahaan</li>
                  <li>Kolom yang wajib diisi ditandai dengan gambar <img src="lib/eproc/themes/default/images/validatebox_warning.png"></li>
                  <li>Isi kolom username dan password </li>
                  <li>Checklist pernyataan menunjukan bahwa data-data yang diisi benar dan dapat dipertanggungjawabkan</li> 
                  <li>Kemudian akan muncul tombol Daftar, silahkan klik tombol Daftar </li> 
                  <li>Sistem akan mengirim email Nomor Registrasi yang nantinya digunakan ketika akan melakukan Pembuktian Berkas </li> 
                  <li>Setelah Registrasi berhasil, silahkan lengkapi data identitas perusahaan dll.</li> 
                  <li>Lakukan Pembuktian Berkas dengan membawa berkas asli sesuai dengan data yang sudah di input pada aplikasi eproc</li> 
                  <!-- <li>..
                      <ul class="list-style-square">
                          <li>....</li>
                      </ul>
                  </li>  -->
              </ul>
                <a onclick="openAdd('main/loadUrl/main/registrasi_video')" class="btn btn-danger mr-1 text-white" style="width: 100%"> <i class="fa fa-play"></i> Lihat Video </a>  
          </div>
        </div>
      </div>
    </div> 

  </div>  
</section> 