<?php
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 * 
 */

$this->libsession->cekSession();
$this->load->model("Masterpengaturan");
// URL Cron Jobs: http://url-buyer/cronjobs_notif_dokexpired/sendMail
?>

<script type="text/javascript">
$(document).ready(function() {
  $("#switch1").click(countChecked);
});

function countChecked() {
  var n = $("#switch1:checked").length;
  // alert(n);
  if(n){
    $("#textAktif").html('Aktif');
    var stat = 'y';
  }else{
    $("#textAktif").html('Non Aktif');
    var stat = 'n';
  }

  $.get("master_pengaturan_json/update/"+stat, function (data) { 
    const myArr = JSON.parse(data);
    if (myArr.data == '1') {
      alertSuccess2(myArr.message);
    } else {
      alertError2(myArr.message);
    }
  });
}

</script>

<div class="row">

  <?php 
  $master_pengaturan = new Masterpengaturan();
  $master_pengaturan->selectByParams(array('ID' => 1));
  $master_pengaturan->firstRow();

  $reqAktif = $master_pengaturan->getField("AKTIF"); 
?>
	<div class="col-md-6">
	  <div class="card">
      <div class="card-content">
        <div class="card-body">
          <div class="media">
            <div class="media-body text-left">
                <h3 class="success">Pengaturan Kirim Email Pemberitahuan</h3>
            </div>
            <div class="media-right media-middle">
              <i class="fa fa-envelope-o success font-large-2 float-right"></i>
            </div>
          </div> 
					<p class="card-text"><p><code>Aktivasi pengiriman email pemberitahuan via email secara otomatis kepada penyedia,<br> &nbsp; pemberitahuan akan di kirim sebanyak 6x perhari ke penyedia</code></p></p>
          <?php 
          if ($reqAktif == 'y') { ?>
          <input name="reqAktif" type="checkbox" class="switch" id="switch1" checked="checked" style="cursor:pointer" /> 
          <span id="textAktif">Aktif</span>
          <?php 
          } else {
          ?>
          <input name="reqAktif" type="checkbox" class="switch" id="switch1"  style="cursor:pointer"/> 
          <span id="textAktif">Non Aktif</span>
          <?php 
          } ?>

          <?php 
          if (file_exists('logs/notif/logs_notif_mail_dok_expired.txt')) { ?>
          <div class="col-md-2 pull-right badge badge-dark">
            <a href="<?= base_url('logs/notif/logs_notif_mail_dok_expired.txt') ?>" target="_blank">
              <span class="fa fa-history"></span> view log
            </a>
          </div>
          <?php 
          } else { ?>
          <div class="col-md-3 pull-right badge badge-danger" style="opacity: 0.6;">
              <span class="fa fa-history"></span> belum ada logs
          </div>
          <?php 
          } ?>
        </div>
      </div>
	  </div>
	</div>

	<div class="col-md-6">
	  <div class="card">
      <div class="card-content">
        <div class="card-body">
          <div class="media">
            <div class="media-body text-left">
                <h3 class="deep-orange">Dokumen Expired Penyedia</h3>
            </div>
            <div class="media-right media-middle">
              <i class="fa fa-times-circle orange font-large-2 float-right"></i>
            </div>
          </div> 
					<p class="card-text"><p><code>Penyedia yang memiliki legalitas atau dokumen sudah kadaluarsa</code></p></p>
          <a href="<?= base_url('main/index/master_pengaturan_dok_expired') ?>" class="btn btn-xs round btn-min-width box-shadow-1 btn-primary text-white"> Lihat data dokumen expired</a>
        </div>
      </div>
	  </div>
	</div>
  
</div>

