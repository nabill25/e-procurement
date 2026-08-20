<?php
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 * 
 */

$reqId = $_GET['reqId']; // contractingrekananid
$reqRekananId = $_GET['reqRekananId']; // pemenang
$multi = $_GET['multi']; // multi
 ?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <base href="<?=base_url();?>" />

    <link rel="icon" href="../../favicon.ico">

    <title><?= SYSTEM_NAME_PT ?></title>
    <link rel="stylesheet" type="text/css" href="<?=base_url() ?>assets/new/css/vendors.css">
    <link rel="stylesheet" type="text/css" href="<?=base_url() ?>assets/new/vendors/css/ui/prism.min.css">
    <link rel="stylesheet" type="text/css" href="<?=base_url() ?>assets/new/css/app.css">
  </head>

<body class="body-popup">

  <div class="card mb-1 border-darken-1">
      <div class="card-content">
        <div class="p-1">
          <div class="alert alert-success">Silahkan pilih template penilaian yang sesuai.</div>
          <div class="row">
            <div class="col-md-12 col-sm-12">
              <div class="card-body" style="padding-right: 2px; text-align: center;">
                <div class="row">

                  <?php
                  $this->load->model("PaketPenilaian");
                  $paket_penilaian = new PaketPenilaian();
                  $paket_penilaian->selectTemplate(array());
                  if ($paket_penilaian->countRow() > 0) {
                    $arrayColor = array('btn-danger','btn-primary','btn-info','btn-success','btn-dark','btn-warning');
                    $no = 0;
                    while ($paket_penilaian->nextRow()) {
                      $parsingTemplate = $paket_penilaian->getField("TEMPLATE");
                      if ($multi == '1') {
                        $url = 'kontrak/index/contracting_penilaian_multi_tambah';
                      } else {
                        $url = 'kontrak/index/contracting_penilaian_tambah';
                      }
                      ?>
                      <div class="col-md-3">
                        <div class="card box-shadow-0 animated zoomIn" data-appear="appear" data-animation="zoomIn">
                          <a target="_blank" href='<?= base_url($url.'/?reqId=').$reqId.'&reqRekananId='.$reqRekananId.'&reqTemplate='.$parsingTemplate ?>'>
                            <div class="card-content btn <?= $arrayColor[$no] ?>" style="width:100%; color:#fff">
                                <div class="card-body" style="cursor: pointer; padding: .7em">
                                    <div class="media">
                                        <div class="media-body text-center">
                                            <h3 class="white"><?= $paket_penilaian->getField("TEMPLATE") ?></h3>
                                        </div>
                                    </div>
                                </div>
                            </div>
                          </a>
                        </div>
                      </div>
                <?php
                    $no++;
                    }
                  } else {
                    echo "Tidak ada template penilaian";
                  } ?>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
</body>
</html>
