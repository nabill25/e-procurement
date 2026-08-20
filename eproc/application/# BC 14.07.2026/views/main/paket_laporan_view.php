<?php
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 * 
 */
  include_once("functions/string.func.php");
  include_once("functions/date.func.php");
  include_once("functions/default.func.php");
  $reqId = $this->input->get("reqId"); // Paket ID
  $this->load->model("PaketDokumen");
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
             <div class="table-responsive">
            <table class="table table-bordered mb-0">
              <tbody>
                <tr class="judul-kolom">
                  <th width="2%">No.</th>
                  <th colspan="2">Nama Dokumen</th>
                  <th>Keterangan</th>
                  <th>Ukuran File</th>
                  <th>Tgl Upload</th>
                  <th width="9%">Aksi</th>
                </tr>
                <?php 
                $paket_dokumen = new PaketDokumen();

                $paket_dokumen->selectByParams(array("PAKET_ID" => $reqId, "JENIS_DOKUMEN" => "LAPORAN_PAKET"));

                if ($paket_dokumen->countRow() == 0) {
                  echo '<tr><td colspan="7" class="text-center">. : : Data tidak ada : : .</td></tr>';
                } else 
                {
                  $i=1;
                  while($paket_dokumen->nextRow())
                  {
                ?>
                <tr >
                    <td><?=$i?>.</td>
                    <td colspan="2"><?=$paket_dokumen->getField("NAMA")?></td>
                    <td><?=$paket_dokumen->getField("KETERANGAN")?></td>
                    <td><?=round($paket_dokumen->getField("UKURAN") / 1024, 2)?> Kb</td>
                    <td><?=getFormattedDate($paket_dokumen->getField("TANGGAL_UPLOAD"))?></td>
                    <td>
                      <a href="uploads/lelang/<?=$paket_dokumen->getField("PATH_FILE")?>" target="_blank" class="btn-aksi">
                        <?= ICON_DOWNLOAD ?>
                      </a>
                    </td>
                </tr>
                 <?php 
                $i++;
                }
              }
              ?>
              </tbody>
            </table>
          </div>
          </div>
        </div>
      </div> 
    </div>

  </body>
</html>
