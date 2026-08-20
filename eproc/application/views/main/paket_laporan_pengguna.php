<?php
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 * 
 */

$reqMode = httpFilterRequest("reqMode");
$reqId = httpFilterRequest("reqId");
$reqCetak= httpFilterPost('reqCetak');
$reqNamaDokumen= httpFilterPost('reqNamaDokumen');
$reqKeterangan= httpFilterPost('reqKeterangan');
$reqLinkFile= isset($_FILES['reqLinkFile']) ? $_FILES['reqLinkFile'] : '';
$reqBayar= httpFilterPost('reqBayar');
$reqDokumenId = httpFilterGet('reqDokumenId');
$submitSimpan= httpFilterPost('submitSimpan');

$this->libsession->cekSession();

$this->load->library("kauth");  $userLogin = new kauth();
$this->load->library("paketinfo"); $paketInfo = new paketinfo();
$this->load->model("PaketDokumen");
$this->load->library("FileHandler");
include_once("functions/string.func.php");
include_once("functions/date.func.php");
include_once("functions/default.func.php");

$paket_dokumen = new PaketDokumen();
$file = new FileHandler();


$paketInfo->getPaket($reqId);
$reqNama = $paketInfo->nama;
$reqPaketUUID = $paketInfo->uuid;

$paket_dokumen->selectByParams(array("PAKET_ID" => $reqId, "JENIS_DOKUMEN" => "LAPORAN_PAKET"));
?> 

<style type="text/css">
  table th {
    background-color: #967adc;
    color: #fff;
  }
</style>

<div class="row">
  <div class="col-md-12 col-sm-12">
    <div class="card">
      <div class="card-header card-head-inverse bg-primary">
        <h4 class="card-title text-white">Laporan <?= $paketInfo->metode_lelang_nama ?></h4>
        <a class="heading-elements-toggle"><i class="fa fa-ellipsis-v font-medium-3"></i></a>
        <div class="heading-elements">
            <ul class="list-inline mb-0">
              <li><a data-action="expand"><i class="ft-maximize"></i></a></li>
              <!-- <li><a data-action="close"><i class="ft-x"></i></a></li> -->
            </ul>
        </div>
      </div>
      <div class="card-content collapse show border-info border-darken-2">
        <div class="card-body area-datatable">
          <div class="col-md-12" style="margin-bottom:5px; padding: 10px 0px !important;">
            <a href="main/index/paket_detil/?eid=<?=$reqId?>&key=<?= $reqPaketUUID ?>" class="<?= CLASS_BTN_DANGER ?>"> <?= BTN_KEMBALI ?></a>        
          </div>
          <div class="table-responsive">
            <table class="table table-bordered mb-0">
              <tbody>
                <tr class="judul-kolom">
                  <th width="2%">No.</th>
                  <th colspan="2">Nama Dokumen</th>
                  <th>Keterangan</th>
                  <th width="10%">Ukuran File</th>
                  <th width="15%">Tgl Upload</th>
                  <th width="2%">Aksi</th>
                </tr>
                <?php 
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
                            <i class="fa fa-download" aria-hidden="true"></i>
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
</div>
