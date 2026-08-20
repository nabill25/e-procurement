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
  </head>

<body class="body-popup">

    <div class="card mb-1 border-darken-1">
      <div class="card-content">
        <div class="col-md-12">
          <h4 style="margin-top: 0%; text-align: center"><i><b>REKAM KAJI ULANG</b></i></h4><br>
          <table  class="table table-bordered mb-1" id="tbl_bidang">
            <tbody>
              <?php
              $id = $this->input->get("id"); // Permohonan ID
              
              $this->load->model(array("Paketkajiulang","Permohonanpaket")); 
              $paket_kaji_ulang = new Paketkajiulang();
              $paket_kaji_ulang->selectByParams(array("A.PERMOHONAN_PAKET_ID" => $id));

              while($paket_kaji_ulang->nextRow())
              {
                $tglupload = explode('.', $paket_kaji_ulang->getField("CREATED_DATE"));
              ?>
                <tr >
                  <td width="80%">
                    <i class="fa fa-user"></i> <?=$paket_kaji_ulang->getField("USER_NAMA")?> <br>
                      <?=$paket_kaji_ulang->getField("KETERANGAN")?> <br>
                      <?php if ($paket_kaji_ulang->getField("PATH_FILE")) { ?>
                        <a href="uploads/kajiulang/<?=$paket_kaji_ulang->getField("PATH_FILE")?>" target="_blank" class="badge badge-primary">
                            <i class="fa fa-download" aria-hidden="true"></i> Donwload
                        </a><br>
                      <?php } ?>
                      <small><i class="fa fa-clock-o"></i> <?=$tglupload[0] ?></small>
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

  </body>
</html>
