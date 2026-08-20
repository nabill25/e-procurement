<?php
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 * 
 */
$this->load->library("crfs_protect"); $csrf = new crfs_protect('_crfs_rlt');
include_once("functions/string.func.php");
include_once("functions/date.func.php");
include_once("functions/default.func.php");
?>
<script type="text/javascript">
$(document).ready(function() {

	$(function(){
		$('#ffkontak').form({
			url:'kontak_json/kontak_add',
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
                hideLoad();
				$("#btnReset").click();
				reloadCaptcha ();
        alertSuccess2(data);
			}
		});

	});

});
</script>
<section id="backColor">
  <div class="row">
    <div class="col-md-12 col-sm-12">
      <div class="card card border-bottom-primary box-shadow-0 " style="zoom: 1;">
        <div class="card-header">
          <h4 class="card-title"><i class="ft-user"></i> Kontak <small>kami</small></h4>
          <div class="heading-elements">
            <ul class="list-inline mb-0">
              <li><a data-action="expand"><i class="ft-maximize"></i></a></li>
              <!-- <li><a data-action="close"><i class="ft-x"></i></a></li> -->
            </ul>
          </div>
        </div>
        <div class="card-body">
          <div class="form-body">

            <div class="col-md-6 offset-md-3" style="zoom: 1;"> 
              <div class="card-body">
                <div class="card-text">
                 <form id="ffkontak" class="easyui-form form-horizontal" method="post" novalidate enctype="multipart/form-data">

                      <div class="row">
                        <div class="form-group col-md-12 mb-2">
                          <label>Nama</label>
                            <input type="text" name="reqNamaPerusahaan" maxlength="100" accesskey="n" title="Nama harus diisi" class="form-control easyui-validatebox span6" id="reqNamaPerusahaan" required value="<?=$reqNamaPerusahaan?>" />
                        </div>
                      </div>

                      <div class="row">
                        <div class="form-group col-md-12 mb-2">
                          <label> Email </label>
                            <input type="text" name="reqEmail" accesskey="e" id="reqEmail" class="form-control easyui-validatebox span6" value="<?=$reqEmail?>"  data-options="required:true,validType:'email'" />
                        </div>
                      </div>

                      <div class="row">
                        <div class="form-group col-md-12 mb-2">
                          <label> Telpon/HP </label>
                            <input type="text" name="reqTelepon" accesskey="t" id="reqTelepon" title="Kontak Person harus diisi" class="form-control easyui-validatebox span6" value="<?=$reqTelepon?>" />
                        </div>
                      </div>

                      <div class="row">
                        <div class="form-group col-md-12 mb-2">
                          <label> Subyek </label>
                            <input type="text" name="reqSubyek" accesskey="p" id="reqSubyek" title="Subyek harus diisi" class="form-control easyui-validatebox span6"  required value="<?=$reqSubyek?>"/>
                        </div>
                      </div>

                      <div class="row">
                        <div class="form-group col-md-12 mb-2">
                          <label> Pesan </label>
                            <textarea name="reqKeterangan" rows="4" required accesskey="l" id="reqKeterangan" title="Pesan harus diisi" class="form-control easyui-validatebox span6"><?=$reqKeterangan?></textarea>
                        </div>
                      </div>

                      <div class="row">
                        <div class="form-group col-md-5 mb-2">
                          <label style="color: #fff"> Ketik kode</label>
                          <img src="<?= base_url() ?>main/loadUrl/main/CaptchaSecurityImages/?&width=100&height=40&characters=4" id="captchaImage3" />&nbsp;&nbsp;&nbsp;
                          <i class="fa fa-refresh fa-2x" onclick="reloadCaptcha3()" style="cursor:pointer;" title="refresh captcha"></i>
                        </div>
                        <div class="form-group col-md-7 mb-2">
                          <label> &nbsp; </label>
                          <input required name="security_code" type="text" title="Kode harus diisi" class="form-control easyui-validatebox" validType="remote['fungsi_json/captcha_validation', 'security_code']" invalidMessage="Kode validasi salah." placeholder="Ketik kode security"  />
                        </div>
                      </div>

                      <div class="form-actions">
                        <input type="hidden" name="reqKirim" id="reqKirim" value="Simpan" />
                        <button type="submit" class="btn round btn-min-width box-shadow-1 btn-primary"><i class="fa fa-check-square-o"></i> Kirim</button>
                        <button type="reset" id="btnReset" class="btn round btn-min-width box-shadow-1 btn-danger"><i class="fa fa-refresh"></i> Reset</button>
                      </div>

                      </div>
                     <?=$csrf->echoInputField();?>
                  </form>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>


  </div>
</section>
