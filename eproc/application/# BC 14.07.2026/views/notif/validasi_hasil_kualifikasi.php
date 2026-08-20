<?php 
include_once("functions/string.func.php");
include_once("functions/date.func.php");
include_once("functions/default.func.php");

$this->load->model("PaketPanitia");

$reqId = $this->input->get("reqId");
$paket_panitia_belum_validasi = new PaketPanitia();
$paket_panitia_belum_validasi->selectByParams(array("A.PAKET_ID" => $reqId));
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
              <div class="card">
                <div class="card-content collapse show border-info border-darken-2">
                  <div class="card-body" style="padding-right: 2px">
                   <table class="table table-double mb-0"> 
                    <thead>
                      <tr>
                        <th>NPP</th>
                        <th>Nama</th>
                        <th>Jabatan</th> 
                        <th style="text-align: center">Validasi</th>
                      </tr>                                          
                    </thead>
                    <tbody id="tbodyPanitia">
                      <?php
                        $i=1; 
                        $totalPanitia=1;
                        $totalPanitiaSudahValidasi=1;
                        while($paket_panitia_belum_validasi->nextRow())
                        { 
                        ?> 
                        <tr>
                          <td><?=$paket_panitia_belum_validasi->getField("NIP")?></td>
                          <td><?=$paket_panitia_belum_validasi->getField("NAMA")?></td>
                          <td><?=$paket_panitia_belum_validasi->getField("JABATAN")?></td> 
                          <td class="text-center">
                            <?php  
                            if ($paket_panitia_belum_validasi->getField("VALIDASI_HASIL_KUALIFIKASI") == '1') {
                              $validasiDate = explode(' ', $paket_panitia_belum_validasi->getField("VALIDASI_HASIL_KUALIFIKASI_DATE"));
                              echo '<span style="color:#000"><span class="fa fa-check"></span><br><small>'.getFormattedDate($validasiDate[0]) .' '.$validasiDate[1].'</small>'; 
                            } else {
                              echo '<span style="color:#F00"><span class="fa fa-times"></span>'; 
                            }
                            ?>
                          </td>
                        </tr>
                      <?php
                        $i++;  
                      }
                      ?>
                    </tbody>
                  </table> 
                  </div>
                </div>
              </div>
            </div>
          </div> 
        </div>
      </div>
    </div>

</body>
</html>
