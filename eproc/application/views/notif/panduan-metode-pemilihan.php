<?php
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 * 
 */
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
          <div class="row">
            <div class="col-md-12 col-sm-12">
              <!-- <div class="card"> -->
                <!-- <div class="card-content collapse show border-info border-darken-2"> -->
                  <div class="card-body" style="padding-right: 2px; text-align: center;">
                    <?php
                    $this->load->model("Masterdokumentemplate");
                    $master_dokumen = new Masterdokumentemplate();
                    $master_dokumen->selectByParams(array('B.NAMA' => 'Panduan Metode Pemilihan'));
                    if ($master_dokumen->countRow() > 0) {
                      $master_dokumen->firstRow();
                      if ($master_dokumen->getField('TIPE') == 'pdf') {
                       echo '<object data="'.base_url('uploads/template/'.$master_dokumen->getField('PATH_FILE')).'" type="application/pdf" width="100%" height="500px">
                              <p>Alternative text - include a link <a href="'.base_url('uploads/template/'.$master_dokumen->getField('PATH_FILE')).'">to the PDF!</a></p>
                            </object>';
                      } else {
                        echo '<img src="'.base_url('uploads/template/'.$master_dokumen->getField('PATH_FILE')).' " style="width: 55%;">';

                      }
                    } else {
                      echo "Tidak ada panduan / panduan belum di upload";
                    } ?>
                  </div>
                <!-- </div> -->
              <!-- </div> -->
            </div>
          </div>
        </div>
      </div>
    </div>

</body>
</html>
