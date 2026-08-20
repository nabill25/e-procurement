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
    <!-- <link rel="stylesheet" type="text/css" href="<?=base_url() ?>assets/new/css/vendors.css"> -->
    <!-- <link rel="stylesheet" type="text/css" href="<?=base_url() ?>assets/new/css/app.css"> -->
    <!-- <link rel="stylesheet" type="text/css" href="<?=base_url() ?>assets/new/css/style.css"> -->
  </head>

<body class="body-popup">

    <div class="card mb-1 border-darken-1">
      <div class="card-content">
        <div class="col-md-12">
            <?php
            // KHUSUS SAAT USULAN ID yang dibawa PERMOHONAN_PAKET_ANALISA_ID, Maka Konversi dulu ke PERMOHONAN_PAKET_ID
            $konversi = $this->input->get("konversi");
            $id = $this->input->get("id"); // Permohonan ID

            if ($konversi == 'ya') {
              $this->load->model("Permohonanpaket");
              $getPID = new Permohonanpaket();
              $getPID->selectByParams(array("A.PERMOHONAN_PAKET_ANALISA_ID" => $id));
              $getPID->firstRow();
              $id = $getPID->getField('PERMOHONAN_PAKET_ID');
            } 
            
            $paketid = $this->input->get("paketid"); // Paket ID 
            $conRekId = $this->input->get("conRekId"); // Contractingrekananid
              $this->load->library("librekamjejak"); 
              $librekamjejak = new librekamjejak();

            if ($conRekId) { // Untuk RJ Kontrak
              echo $librekamjejak->viewRJContract($conRekId);
            } else { 
              echo $librekamjejak->viewRJ($id,$paketid);
            }

            ?>
          </div>
        </div>
      </div>
      <?php
      if($this->USER_TYPE_ID == 10)
      {
        echo '<a href="main/loadUrl/report/rekamjejak_pdf/?id='.$id.'&paketid='.$paketid.'" target="_blank" class="'.CLASS_BTN_INFO.'" style="margin-top:1%"> '.BTN_PRINT.' Rekam Jejak </a>';
      } ?>
    </div>

  </body>
</html>
