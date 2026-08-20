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
<style type="text/css">
  sup {
    font-style: italic;
    color: red;
  }
</style>
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
          $("#ff").trigger("reset");
          alertSuccess2('Registrasi Berhasil Silahkan Login.');
          $("#reqSubmit").hide(0);
          reloadCaptcha2();
          setTimeout(function () {
            window.location.href = '<?= base_url() ?>';
          }, 5000);
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
                <div class="form-group col-md-6 mb-2">
                  <label>NPWP <sup>Perusahaan</sup></label>
                  <input type="hidden" id="reqNPWPStatus" value="0" />
                  <input type="text" id="reqNPWP" name="reqNPWP"  class="form-control easyui-validatebox span3" accesskey="n" value="<?=isset($reqNPWP)?$reqNPWP:''?>" onkeydown="return format_npwp(event, 'reqNPWP');" maxlength="20" validType="remote['fungsi_json/check_npwp','reqNPWP', $('input[name=\'reqStatus\']:checked').val()]" invalidMessage="NPWP sudah digunakan." required /> 
                </div> 
                <div class="form-group col-md-6 mb-2">
                  <label> Email <sup>Official Perusahaan</sup> </label>
                  <input type="text" name="reqEmail" accesskey="e" value="<?=isset($reqEmail)?$reqEmail:''?>" id="reqEmail" class="form-control easyui-validatebox span4" data-options="required:true,validType:['email','remote[\'fungsi_json/check_email\',\'reqEmail\']']" invalidMessage="Format email salah atau email sudah digunakan." required />
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
                  <label style="color:#fff"> Kode ikn ketik</label>
                  <img src="<?= base_url() ?>main/loadUrl/main/CaptchaSecurityImages/?&width=100&height=40&characters=4" id="captchaImage2" />&nbsp;&nbsp;&nbsp;<i class="fa fa-refresh fa-2x" onclick="reloadCaptcha2()" style="cursor:pointer;" title="refresh captcha"></i>
                </div> 
                <div class="form-group col-md-9 mb-2">
                  <label> &nbsp; </label>
                  <input id="label" required name="security_code" type="text" title="Kode harus diisi" class="form-control easyui-validatebox" validType="remote['fungsi_json/captcha_validation', 'security_code']" invalidMessage="Kode validasi salah." style="width: 20%" />
                </div> 
              </div> 
                <?=$csrf->echoInputField();?>


              <div class="form-actions">
                <input name="reqSetuju" type="checkbox" id="chk_agreement" accesskey="e" value="1" style="cursor: pointer;" />
                Dengan ini saya menyatakan bahwa data-data tersebut adalah data yang benar dan dapat dipertanggungjawabkan. <br> 
                <span style="margin-left: 2%">Kebijakan Penyedia/Vendor <a onclick="openAdd('main/loadUrl/main/registrasi_kebijakan')"> <i>klik disini</i></a></span><br>

                <a href="main" class="btn btn-danger mr-1 text-white" style="margin-top: 3%"> <i class="fa fa-arrow-left"></i> Kembali </a>  
                <button type="submit" class="btn btn-primary" name="reqSubmit" id="reqSubmit" style="display:none; margin-top: 3%"> 
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