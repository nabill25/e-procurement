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

//ob_start();
/* INCLUDE FILE */
$this->load->library("crfs_protect"); $csrf = new crfs_protect();
$this->load->library("kauth");  $userLogin = new kauth();
$this->load->model("Rekanan");

include_once("functions/string.func.php");
include_once("functions/date.func.php");
include_once("functions/default.func.php");

/* create objects */
$rekanan = new Rekanan();

$rekanan->selectByParams(array("A.REKANAN_ID"=>$this->ID),-1,-1);
$rekanan->firstRow();

$reqEmail = $rekanan->getField("EMAIL");

?>
<script type="text/javascript">

$(document).ready(function() {
  $(function(){
    $('#ff').form({
      url:'<?= base_url('rekanan_json/data_administrasi_umum_ubah_profile_email') ?>',
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
        alertSuccess2(data);
        setTimeout(function() {
          document.location.href = 'main/index/data_administrasi_umum_ubah_profile_email';
        }, 1800);
        // hideLoad();
      }
    });

  });


});

function sendCode() {
  showLoad();
  var a = $('#reqEmail').val();
  // alert(a); return false;
  $.post("rekanan_json/set_email_kode_verifikasi",
    {
      mail: a
    },
    function(data, status){
      var str = data;
      var isNotif = str.split("||");
      if (isNotif[0] === 'Gagal') {
        alertError2(isNotif[1]);
      } else {
        alertSuccess2(isNotif[1]);
      }
      setTimeout(function() {
        hideLoad();
      }, 1800);
    });
}

function setmailtext(a) {
  $('#mail-new').html(a.value);
}
</script>

<style type="text/css">
  sup {
    font-style: italic;
    color: red;
  }
</style>

<section id="backColor">
  <div class="row">

    <div class="col-md-12 col-sm-12">
      <div class="card border-bottom-primary" style="zoom: 1;">
        <div class="card-header">
          <h4 class="card-title"><i class="ft-user"></i> Ubah Profil Perusahaan <small>email</small></h4>
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
                <div class="form-group col-md-6 mb-2">
                  <label style="width: 100%">e-Mail <sup>Official Perusahaan</sup></label>
                  <input type="text" name="reqEmail" accesskey="e" value="" id="reqEmail" onkeyup="setmailtext(this)" class="form-control easyui-validatebox span4" data-options="required:true,validType:['email','remote[\'fungsi_json/check_email_ubah\',\'reqEmail\',\'<?= $reqEmail?>\']']" invalidMessage="Format email salah atau email sudah digunakan." required />
                  <a onclick="sendCode()" class="badge badge-dark" style="color:#fff"><span class="fa fa-send"></span> Klik disini untuk Kirim Konfirmasi Kode ke email <span id="mail-new"> </span></a>

                </div>
                <div class="form-group col-md-6 mb-2">
                  <label>Konfirmasi Kode</label>
                  <input type="text" name="reqEmailKodeVerifikasi" accesskey="e" value="<?=isset($reqEmailKodeVerifikasi)?$reqEmailKodeVerifikasi:''?>" id="reqEmailKodeVerifikasi" class="form-control easyui-validatebox span4" data-options="required:true,validType:['remote[\'rekanan_json/check_kode_email\',\'reqEmailKodeVerifikasi\']']" invalidMessage="Kode tidak cocok, untuk mendapatkan kode klik tombol kirim kode kemudian silahkan cek email." required />
                </div>
              </div>
                <h5>Ketentuan:</h5>
                <ul style="list-style-type:disc;">
                  <li>
                    <b>e-Mail</b>: Isi dengan email official perusahaan karena email tersebut akan menjadi media komunikasi ketika mengikuti kegiatan pengadaan.
                  </li>
                  <li>
                    <b>Konfirmasi Kode</b>: masukan kode yang di kirim ke email kemudian klik simpan<br>
                  </li>
                  <li>
                    Sebelum klik simpan <b> <i style="color:red"> "Pastikan email yang di input sudah benar." </i></b>
                  </li>
                </ul>

              <div class="form-actions">
                <a href="main/index/data_administrasi_umum" class="<?= CLASS_BTN_DANGER ?> mr-1"> <i class="fa fa-arrow-left"></i> Kembali </a>
                <button type="submit" class="<?= CLASS_BTN_PRIMARY ?>"><i class="fa fa-check-square-o"></i> Simpan</button>
              </div>

    		    </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>
