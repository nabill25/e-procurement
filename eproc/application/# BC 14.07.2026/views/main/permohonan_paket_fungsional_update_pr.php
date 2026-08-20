<?php 
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 * 
 */
$this->libsession->cekSession('blockpenyedia');

?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <base href="<?=base_url();?>" />

    <link rel="icon" href="../../favicon.ico">

    <title><?= SYSTEM_NAME.' | '.SYSTEM_NAME_PT ?></title>

    <link rel="stylesheet" type="text/css" href="<?=base_url() ?>assets/new/css/vendors.css">
    <link rel="stylesheet" type="text/css" href="<?=base_url() ?>assets/new/vendors/css/ui/prism.min.css">
    <link rel="stylesheet" type="text/css" href="<?=base_url() ?>assets/new/vendors/css/extensions/toastr.css">
    <!-- END VENDOR CSS-->
    <!-- BEGIN ROBUST CSS-->
    <link rel="stylesheet" type="text/css" href="<?=base_url() ?>assets/new/css/app.css">
    <!-- <link rel="stylesheet" type="text/css" href="<?=base_url() ?>css/core.css"> -->
    <!-- END ROBUST CSS-->
    <!-- BEGIN Page Level CSS-->
    <link rel="stylesheet" type="text/css" href="<?=base_url() ?>assets/new/css/core/menu/menu-types/horizontal-menu.css">
    <link rel="stylesheet" type="text/css" href="<?=base_url() ?>assets/new/css/core/colors/palette-gradient.css">
    <link rel="stylesheet" type="text/css" href="<?=base_url() ?>assets/new/css/core/colors/palette-callout.css">
    <!-- END Page Level CSS-->
    <!-- BEGIN Custom CSS-->
    <link rel="stylesheet" type="text/css" href="<?=base_url() ?>assets/new/css/style.css">

   <script type="text/javascript" src="js/jquery-1.6.1.min.js"></script>

    <!-- PAGINATION -->
    <link rel="stylesheet" type="text/css" href="lib/drupal/index.css" />
    <script src="lib/emodal/eModal.js"></script>
    <script>
    function openAdd(pageUrl) {
        eModal.iframe(pageUrl, '<? SYSTEM_NAME.' '.SYSTEM_NAME_PT ?>')
      }
    function closePopup() {
      eModal.close();
    }
    </script>


    <style type="text/css">
      ul.menu-icons li {list-style-type:none;}
      ul { padding-left: 2px; }
    </style>

    <script type="text/javascript">
      function closePopup() {
        eModal.close();
      }

      setTimeout(function(){
        getDataRUP();
      }, 500);

      function aaa(a) {
        $.ajax({
          url : '<?= base_url('permohonan_paket_json/excUpdatePR/') ?>'+a,
          type: "GET",
          dataType: "JSON",
          beforeSend: function() {
            $('#btnUpdateNoPR').html('<span class="fa fa-spinner fa-spin"></span> Proses Update No. PR ke Agnes . . .');
          },
          success: function(data)
          {
            if (data.respon == 'true') {
          		alertSuccess2('Proses Update No. PR ke Agnes berhasil di proses.');
            } else {
          		alertSuccess2('Hanya sebagian No. PR berhasil di update, silahkan cek Agnes dan pastikan No. PR sudah tersedia.');
            }

            setTimeout(function () {
            	getDataRUP();
						}, 2000);
            top.reloadMonitoring();

            $('#btnUpdateNoPR').html('<span class="fa fa-cog"> Update No. PR</span>');
          },
          error: function (jqXHR, textStatus, errorThrown) { 
             getDataRUP();
          	$('#btnUpdateNoPR').html('<span class="fa fa-cog"> Update No. PR</span>');
          	alertError3('Proses Update No. PR ke Agnes belum bisa di proses, silahkan cek koneksi dengan Sistem Agnes dan pastikan No. PR sudah tersedia.');
        	},
        });
      }

      function bbb(kode_rup,idnya) {
        var pr_number = $('#'+idnya).val();
        if (pr_number) {
          $.ajax({
            url : '<?= base_url('permohonan_paket_json/excManualUpdatePR/') ?>'+pr_number+'/'+kode_rup,
            type: "GET",
            dataType: "JSON",
            beforeSend: function() {
              $('#'+idnya).html('<span class="fa fa-spinner fa-spin"></span> Proses Update No. PR ...');
            },
            success: function(data)
            {
              if (data.respon == 'true') {
                alertSuccess2('Proses Update No. PR berhasil di proses.');
              }

              setTimeout(function () {
                getDataRUP();
              }, 2000);
              top.reloadMonitoring();

              $('#'+idnya).html('<span class="fa fa-cog"> Update No. PR</span>');
            },
            error: function (jqXHR, textStatus, errorThrown) { 
               getDataRUP(); 
            },
          });
        } else {
          alertError3('Isi No. PR');
        }
      }

      function getDataRUP() {
      	$.ajax({
          url : '<?= base_url('permohonan_paket_json/getUpdatePR') ?>',
          type: "GET",
          dataType: "JSON",
          beforeSend: function() {
            $('#showData').html('Load data...');
          },
          success: function(data)
          {
            $('#showData').html(data.message);
          },
          error: function (jqXHR, textStatus, errorThrown) { },
        });
      }

      function alertError3(a) {
        toastr.error(a, "Perhatian!", {
          progressBar: !0
        })
      }

      function alertSuccess2(a) {
        toastr.success(a, "Sukses", {
          progressBar: !0
        })
      }

      // function getData(a) {
      //   if (a) {
      //     $.ajax({
      //       url : '<?= base_url('rekanan_json/getRekananDelete/') ?>'+a,
      //       type: "GET",
      //       dataType: "JSON",
      //       beforeSend: function() {
      //         $('#showData').html('Load data...');
      //       },
      //       success: function(data)
      //       {
      //         $('#showData').html(data.message);
      //       },
      //       error: function (jqXHR, textStatus, errorThrown) { },
      //     });
      //   } else {
      //     alertError3('Pilih data dahulu');
      //   }
      // }

    </script>

  </head>

<!-- <body class="body-popup"> -->
<body>

    <div class="card mb-1 border-darken-1">
      <div class="card-content">
        <div class="p-1">
          <div class="row">
            <div class="col-md-12 col-sm-12">
              <div class="card"> 

                <h5><u>Daftar Rencana Pengadaan</u></h5>
                <div id="showData"></div>

              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <script src="lib/bootstrap/libs/jquery/1.12.4/jquery.min.js"></script>
    <script>window.jQuery || document.write('<script src="lib/bootstrap/assets/js/vendor/jquery.min.js"><\/script>')</script>
    <script src="lib/bootstrap/dist/js/bootstrap.min.js"></script>
    <link rel="stylesheet" type="text/css" href="<?=base_url()?>lib/eproc/themes/default/easyui.css">
    <script src="<?=base_url()?>assets/new/vendors/js/extensions/toastr.min.js"></script>
    <script type="text/javascript" src="<?=base_url()?>lib/eproc/jquery.easyui.min.js"></script>
    <script type="text/javascript" src="<?=base_url()?>lib/eproc/kalender-easyui.js"></script>
    <script type="text/javascript" src="<?=base_url()?>lib/eproc/allfunc.js"></script>
  </body>
</html>
