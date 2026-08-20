<?php
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 * 
 */
 ?>

 <script type="text/javascript">
$(document).ready(function() {
	
	$(function(){
		$('#ff').form({
			url:'rekanan_json/lupa_password',
			onSubmit:function(){
        var v=$(this).form('validate');
        if(v) {
          showLoad();
          // $('#btnSend').html('<i class="fa fa-send-o"></i> Kirim Link Reset Password');
          return v;
        } else {
          hideLoad();
          // $('#btnSend').html('<i class="fa fa-send-o"></i> Proses . . .');
          return false;
        }
			},
			success:function(data){
        $('#msg-er').text(data);
				$("#reqEmail").val("");	
        hideLoad();
			}
		});
		
	});
	
});
</script>

<section id="backColor">
  <div class="row"> 

    <div class="col-md-8 col-sm-12">
      <div class="card border-bottom-primary" style="zoom: 1;"> 
        <div class="card-header">
          <h4 class="card-title">Lupa Password</h4>
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
                <label>Masukan email terdaftar</label>
                <input type="text" id="reqEmail" name="reqEmail" class="form-control easyui-validatebox span12" data-options="required:true,validType:['email']">
                <span style="color: red; font-size: 12px;" id="msg-er"></span>
              </div> 
            </div>
            <div class="form-actions">
              <!-- <a href="app" class="<?= CLASS_BTN_DANGER ?>"> <i class="fa fa-arrow-left"></i> Kembali </a>  -->
              <button type="submit" id="btnSend" class="<?= CLASS_BTN_PRIMARY ?>"><i class="fa fa-send-o"></i> Kirim Link Reset Password </button>
            </div> 
          </form>
        </div>
      </div>
    </div> 

    <div class="col-md-4 col-sm-12">
      <div class="card border-bottom-primary" style="zoom: 1;"> 
        <div class="card-header">
          <div class="alert alert-icon-right alert-info alert-dismissible" role="alert">
            <span class="alert-icon"><i class="fa fa-info"></i></span>
            <strong>Penjelasan Lupa Password.</strong>
          </div>
        </div>
        <div class="card-body" style="padding: 0 1.5rem !important"> 
          <div class="card-text">
              <ul class="list-style-square">
                  <li>Silahkan isi email yang telah terdaftar di aplikasi eproc</li>
                  <li>Klik tombol Kirim Link Reset Password</li>
                  <li>Aplikasi eproc akan mengirimkan email Link Reset Password</li>
                  <li>Cek email dan klik link yang ada diemail</li> 
                  <li>Aplikasi akan mengarahkan ke halaman Reset Password, kemudian isi password baru dan klik tombol Update </li> 
              </ul>
          </div>
        </div>
      </div>
    </div> 

  </div>  
</section> 
 