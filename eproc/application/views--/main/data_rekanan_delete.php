<?php
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 * 
 */

$this->load->model("Rekanan");
$this->load->model("Rekanandelete");

$reqId = $this->input->get("reqId");
$reqValidasi = $this->input->get("reqValidasi");

$rekanan = new Rekanan();
$rekanandel = new Rekanandelete();

$rekanan->selectByParams(array("A.REKANAN_ID"=> $reqId),-1,-1);
$rekanan->firstRow();
$reqRekananTipeId = $rekanan->getField("REKANAN_TIPE_ID");
$reqKode = $rekanan->getField("KODE");
$tempKualifikasi = $rekanan->getField("REKANAN_KUALIFIKASI");
$tempMail = $rekanan->getField("EMAIL");
$tempFax = $rekanan->getField("FAX_KODE")."-".$rekanan->getField("FAX");
$tempTelepon = $rekanan->getField("TELEPON_KODE")."-".$rekanan->getField("TELEPON");
$tempKota = $rekanan->getField("KOTA");
$tempAlamat = $rekanan->getField("ALAMAT");
$tempKodepos = $rekanan->getField("KODEPOS");
$tempStatus = $rekanan->getField("STATUS_CP");
$tempNPWP = $rekanan->getField("NPWP");
$tempNama= $rekanan->getField("NAMA");
$tempNPWPFILE = $rekanan->getField("NPWP_FILE");
$tempKontakPerson= $rekanan->getField("KONTAK_PERSON");
$tempKontakPersonHp= $rekanan->getField("KONTAK_PERSON_HP");
$tempWebsite= $rekanan->getField("WEBSITE");
$reqPKP = $rekanan->getField("PKP");
$reqMasaBerlakuPKP = $rekanan->getField("PKP_TANGGAL");
$reqLinkFileTemp = $rekanan->getField("PKP_TANGGAL");
$reqMasaBerlakuPKP = $rekanan->getField("PKP_TANGGAL");
$reqLinkFileTemp = $rekanan->getField("NAMA_FILE_PKP");

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
    <link rel="stylesheet" type="text/css" href="<?=base_url() ?>assets/new/css/app.css">
    <link rel="stylesheet" type="text/css" href="<?=base_url() ?>assets/new/css/core/menu/menu-types/horizontal-menu.css">
    <link rel="stylesheet" type="text/css" href="<?=base_url() ?>assets/new/css/core/colors/palette-gradient.css">
    <link rel="stylesheet" type="text/css" href="<?=base_url() ?>assets/new/css/core/colors/palette-callout.css">
    <link rel="stylesheet" type="text/css" href="<?=base_url() ?>assets/new/css/style.css">
    <script type="text/javascript" src="js/jquery-1.6.1.min.js"></script>

    <link rel="stylesheet" type="text/css" href="lib/drupal/index.css" />
    <script src="lib/emodal/eModal.js"></script>

    <style type="text/css">
      ul.menu-icons li {list-style-type:none;}
      ul { padding-left: 2px; }
    </style>

    <script type="text/javascript">
      function closePopup() {
        eModal.close();
      }

      setTimeout(function(){
        $.ajax({
          url : '<?= base_url('rekanan_json/getRekananDelete/'.$reqId) ?>',
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
      }, 500);

      function aaa(a) {
        $.ajax({
          url : '<?= base_url('rekanan_json/excRekananDelete/'.$reqId.'/') ?>'+a,
          type: "GET",
          dataType: "JSON",
          beforeSend: function() {
            $('#btnDelete_'+a).html('<span class="fa fa-spinner fa-spin">');
          },
          success: function(data)
          {
            if (data.respon == 'true') {
              getData('<?= $reqId ?>');
            } else {
              $('#btnDelete_'+a).html(data.message);
              setTimeout(function(){
                getData('<?= $reqId ?>');
              }, 2000);

            }
            if (a == '14') { // Penghapusan Terakhir
              // alert(data.message);
              top.closePopup();
              top.reloadMonitoring();
            }
          },
          error: function (jqXHR, textStatus, errorThrown) { },
        });
      }

      function getData(a) {
        if (a) {
          $.ajax({
            url : '<?= base_url('rekanan_json/getRekananDelete/') ?>'+a,
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
        } else {
          alertError3('Pilih data dahulu');
        }
      }
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
                <table class="table table-bordered table-hover">
                  <tbody>
                      <tr>
                          <td width="20%">Nama Perusahaan:</td>
                          <td>
                           <?=$tempNama?>
                          </td>
                      </tr>
                      <tr>
                          <td width="20%">NPWP:</td>
                          <td>
                           <?=$tempNPWP?>
                          </td>
                      </tr>
                      <tr>
                          <td width="20%">Alamat:</td>
                          <td>
                            <?=$tempAlamat?>, <?=$tempKota?>, <?=$rekanan->getField("REGION")?> <?=$tempKodepos?>
                          </td>
                      </tr>
                      <tr>
                          <td width="20%">No. telepon:</td>
                          <td>
                            <?=$tempTelepon?>
                          </td>
                      </tr>
                  </tbody>
                </table>

                <h5><u>Dokumen Perusahaan</u></h5>
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
    <script type="text/javascript" src="<?=base_url()?>lib/eproc/jquery.easyui.min.js"></script>
    <script type="text/javascript" src="<?=base_url()?>lib/eproc/kalender-easyui.js"></script>
    <script type="text/javascript" src="<?=base_url()?>lib/eproc/allfunc.js"></script>
  </body>
</html>
