<?php
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 * 
 */

$this->libsession->cekSession($this->input->get("reqId"));   

$this->load->library("kauth");  $userLogin = new kauth(); 
$this->load->library("paketinfo"); $paketInfo = new paketinfo();
$this->load->model("PaketDokumen");
$this->load->model("PaketRekanan");
$this->load->library("FileHandler");
include_once("functions/string.func.php");
include_once("functions/date.func.php");
include_once("functions/default.func.php");

$paket_dokumen = new PaketDokumen();
$paket_rekanan = new PaketRekanan();

$reqMode = $this->input->post("reqMode");
$reqId = $this->input->get("reqId");
$reqCetak= $this->input->post('reqCetak');
$reqNamaDokumen= $this->input->post('reqNamaDokumen');
$reqKeterangan= $this->input->post('reqKeterangan');
$reqLinkFile= $_FILES['reqLinkFile'];
$reqBayar= $this->input->post('reqBayar');
$reqDokumenId = httpFilterGet('reqDokumenId');
$submitSimpan= $this->input->post('submitSimpan');

$paketInfo->getPaket($reqId);
$reqNama = $paketInfo->nama;
$reqUUID = $paketInfo->uuid;

$paket_dokumen->selectByParams(array("PAKET_ID" => $reqId, "JENIS_DOKUMEN" => "LELANG"));
?> 

<div class="row"> 
  <div class="col-md-12 col-sm-12">
    <div class="card">
      <div class="card-header card-head-inverse bg-primary">
        <h4 class="card-title text-white">Dokumen Pengadaan 
        </h4>
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
            <div class="table-responsive">
            	<table class="table table-bordered table-hover" id="tbl_bidang">
                <tbody>
                    <tr class="judul-kolom">
                      <th width="2%">No.</th>
                      <th colspan="2">Nama Dokumen</th>
                      <th>Keterangan</th>
                      <th>Ukuran File</th>
                      <th width="5%" class="text-center">Aksi</th>
                    </tr>
                    <?php
          			  $i=1;
          			  $status_bayar = $paket_rekanan->getPaketRekananBayar($reqId, $this->ID);
          			  while($paket_dokumen->nextRow())
          			  {
          			  ?> 
            			 <tr >
            				<td><?=$i?>.</td>
            				<td colspan="2"><?=$paket_dokumen->getField("NAMA")?> </td>
            				<td><?=$paket_dokumen->getField("KETERANGAN")?></td>
            				<td><?=round($paket_dokumen->getField("UKURAN") / 1024, 2)?> Kb</td>
                    <td align="center">
                      <!-- <a href="uploads/lelang/<?=$paket_dokumen->getField("PATH_FILE")?>" class="btn-aksi" target="_blank"><i class="fa fa-download" aria-hidden="true"></i></a> -->
                      <a href="<?= base_url('download_dokumen_json/getDok/'.$reqId.'/'.$paket_dokumen->getField("PAKET_DOKUMEN_ID").'/') ?><?=$paket_dokumen->getField("PATH_FILE")?>" class="btn-aksi" target="_blank">
                        <?= ICON_DOWNLOAD ?> Download
                      </a>
                    </td>
            			</tr>
                 <?php
            			$i++;
            		  }
            		  ?>
                </tbody>
              </table>

              <div>
                  <a href="main/index/paket_detil/?eid=<?=$reqId?>&key=<?=$reqUUID?>" class="<?= CLASS_BTN_DANGER ?>"><i class="fa fa-arrow-left"></i> Kembali</a>
              </div>
            </div>
        </div>
      </div>
    </div>
  </div> 
</div>   