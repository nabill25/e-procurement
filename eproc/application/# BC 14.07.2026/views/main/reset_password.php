<?php
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 *
 */
include_once("functions/string.func.php");
include_once("functions/date.func.php");
include_once("functions/default.func.php");
$this->load->library("kauth");  $userLogin = new kauth();
$this->load->model("Users");

$user_login = new Users();

$reqId = $this->input->get("reqId");
$reqValidasi = $this->input->get("reqValidasi");


if($reqValidasi == md5(date("dmY")))
{}
else
{
	echo '<script language="javascript">';
	echo 'alert("Sesi ubah password anda telah habis, silahkan request kembali, Terima Kasih.");';
	echo 'top.location.href = "'.base_url().'main/index/lupa_password";';
	echo '</script>';
	exit;
}

?>
<script type="text/javascript">
$(document).ready(function() {

	$(function(){
		$('#ff').form({
			url:'users_base_json/reset_password_rekanan',
			onSubmit:function(){
				return $(this).form('validate');
			},
			success:function(data){
				alert(data);
				document.location.href = 'index.php';
			}
		});
				$.extend($.fn.validatebox.defaults.rules, {
				equals: {
				validator: function(value,param){
					return value == $(param[0]).val();
				},
				message: 'Password tidak sama.'
			}
		});
	});

});

$(document).on('click','#showPassUbah',function(){
	$('#reqPasswordBaru').attr("type","text"); $(this).hide(); $('#hidePassUbah').show();
});
$(document).on('click','#hidePassUbah',function(){
	$('#reqPasswordBaru').attr("type","password"); $(this).hide(); $('#showPassUbah').show();
});
$(document).on('click','#showPassUbah2',function(){
	$('#reqPasswordKonfirmasi').attr("type","text"); $(this).hide(); $('#hidePassUbah2').show();
});
$(document).on('click','#hidePassUbah2',function(){
	$('#reqPasswordKonfirmasi').attr("type","password"); $(this).hide(); $('#showPassUbah2').show();
});
</script>

<section id="backColor">
  <div class="row">

    <div class="col-md-12 col-sm-12">
      <div class="card border-bottom-primary" style="zoom: 1;">
        <div class="card-header">
          <h4 class="card-title"><?=translate("Reset Password", "Reset Password")?></h4>
          <div class="heading-elements">
            <ul class="list-inline mb-0">
              <li><a data-action="expand"><i class="ft-maximize"></i></a></li>
              <!-- <li><a data-action="close"><i class="ft-x"></i></a></li> -->
            </ul>
          </div>
        </div>
        <div class="card-body">
            <form id="ff" class="easyui-form form-horizontal" method="post" novalidate enctype="multipart/form-data">
                <div class="row">
                    <div class="form-group col-md-12 mb-2">
                      <label><?=translate("Reset Password", "Reset Password")?>
		      <i id="showPassUbah" class="fa fa-eye-slash" style="cursor:pointer" title="lihat password"></i><i id="hidePassUbah" class="fa fa-eye" style="cursor:pointer;display:none" title="Sembunyikan password"></i>
		      </label>
                        <input type="password" required name="reqPasswordBaru" class="form-control easyui-validatebox" style="width:90%" id="reqPasswordBaru" />
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-md-12 mb-2">
                      <label><?=translate("Konfirmasi Password Baru", "Confirm New Password")?>
		      <i id="showPassUbah2" class="fa fa-eye-slash" style="cursor:pointer" title="lihat password"></i><i id="hidePassUbah2" class="fa fa-eye" style="cursor:pointer;display:none" title="Sembunyikan password"></i>
		      </label>
                      <input type="password" required name="reqPasswordKonfirmasi" class="form-control easyui-validatebox" style="width:90%" id="reqPasswordKonfirmasi" validType="equals['#reqPasswordBaru']" />
                    </div>
                </div>
                <div class="form-actions">
                  <input type="hidden" name="reqId" id="reqId" value="<?=$reqId?>"/>
                  <input type="hidden" name="reqSubmit" id="reqSubmit"/>
                  <button type="submit" class="btn btn-primary"><i class="fa fa-check-square-o"></i> Update</button>
                </div>
            </form>
        </div>
      </div>
    </div>

  </div>
</section>
