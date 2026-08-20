<?php
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 * 
 */
$reqMode = $this->input->get("reqMode");
$reqId = $this->input->get("reqId");

$this->libsession->cekSession($reqId);   

$this->load->library("paketinfo"); $paketInfo = new paketinfo();
$this->load->model("PaketDokumen");
$this->load->model(array("PaketRekanan","PaketTahap"));

include_once("functions/string.func.php");
include_once("functions/date.func.php");
include_once("functions/default.func.php");

$paket_rekanan = new PaketRekanan();
$paket_rekanan_count = new PaketRekanan();


$paket_rekanan->selectByParams(array("PAKET_ID" => $reqId), -1, -1, " AND A.TANGGAL_DAFTAR IS NOT NULL AND A.LULUS_PENDAFTARAN = 1 AND LULUS_KUALIFIKASI = 1 AND A.KIRIM_PENAWARAN = 1 ");
$prc = $paket_rekanan_count->getCountByParams(array("PAKET_ID" => $reqId)," AND A.TANGGAL_DAFTAR IS NOT NULL AND A.LULUS_PENDAFTARAN = 1 AND LULUS_KUALIFIKASI = 1 AND A.KIRIM_PENAWARAN = 1 ");

$paketInfo->getPaket($reqId);
$reqNamaPaket = $paketInfo->nama;
$reqMetodeLelangId = $paketInfo->metode_lelang_id;
$reqUUID = $paketInfo->uuid;

$paket_tahap_metode = new PaketTahap();
$paket_tahap = new PaketTahap();

$jenis_tahap = $paket_tahap_metode->getJenisTahapById($reqId);
$arrPembukaanAuction = PEMBUKAAN_AUCTION;
$aktif_pembukaan = $paket_tahap->getCountByParamsAktif(array("URUT" => $arrPembukaanAuction[$jenis_tahap], "PAKET_ID" => $reqId, "TAMPILKAN" => 1));
$aktif_pembukaan2 = $paket_tahap->getCountByParamsBerlalu(array("URUT" => $arrPembukaanAuction[$jenis_tahap], "PAKET_ID" => $reqId, "TAMPILKAN" => 1));

if($aktif_pembukaan  > 0 || $aktif_pembukaan2  > 0) 
{ $info = "1";
} else {
  $info = "0";
}
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
        <h4 class="card-title text-white">Dokumen Penawaran Peserta <?= $paketInfo->metode_lelang_nama ?></h4>
        <a class="heading-elements-toggle"><i class="fa fa-ellipsis-v font-medium-3"></i></a> 
        <div class="heading-elements">
            <ul class="list-inline mb-0">
              <li><a data-action="expand"><i class="ft-maximize"></i></a></li>
              <!-- <li><a data-action="close"><i class="ft-x"></i></a></li> -->
            </ul>
        </div>
      </div>

      <form id="ff" class="easyui-form form-horizontal" method="post" data-options="novalidate:true" enctype="multipart/form-data">
      <div class="card-content collapse show border-info border-darken-2">

        <div class="card-body area-datatable">
          <div class="table-responsive">
            <table class="table table-bordered mb-0"> 
              <tr>
                <th align="center" width="5px">No.</th>
                <th align="center">Peserta <?= $paketInfo->metode_lelang_nama ?></th>
                <th align="center">NPWP</th>
                <th align="center">Alamat</th>
              </tr>
              <?php
              $i=1;
              $arrRekananId[$i] = 0;
              $arrRekanan[$i] = 0;      
              if ($prc < 1) {
                 echo '<tr><td colspan="4">. : Tidak ada data : .</td></tr>';
              } else {
                $style="gelap";   
                $no = 1;
                while($paket_rekanan->nextRow())
                {
                    $arrRekananId[$i] = $paket_rekanan->getField("REKANAN_ID");
                    $arrRekanan[$i] = $paket_rekanan->getField("REKANAN");
                ?>
                <tr class="<?=$style?>">
                  <td><?=$i?>.</td>
                  <td>
                    <a title="#" onclick="disposeall(); displayElement('filerekanan<?=$i?>')" class="taut">
                      <?php 
                      if ($info == 0) {  echo "Peserta ".$no; } else { 
                        echo $paket_rekanan->getField("REKANAN");
                      }?> <span class="badge badge-dark"> File yg diUpload</span>
                    </a>
                  </td>
                  <td> <?=$paket_rekanan->getField("NPWP")?> </td>
                  <td> <?=$paket_rekanan->getField("ALAMAT")?> </td>
                </tr>
                  <?php
                  $i++;
                  $no++;
                  if($style == "gelap")
                      $style = "terang";
                  else
                      $style = "gelap";
                  }
                }
                ?>
            </table>   
          </div>
          
          <?php
          for($i=1;$i<=count($arrRekananId);$i++)
          {
              $paket_dokumen = new PaketDokumen();
              $paket_dokumen->selectByParams(array("REKANAN_USER_ID" => $arrRekananId[$i], "PAKET_ID" => $reqId), -1, -1, " AND JENIS_DOKUMEN LIKE 'PENAWARAN%' AND JENIS_DOKUMEN != 'PENAWARAN_KUALIFIKASI' ");
              
          ?>
          <div id="filerekanan<?=$i?>" style="display:none; margin-top: 20px">
            <table class="table table-bordered table-hover">
              <tr class="judul-kolom" style="background-color: #b3b3b3">
                <td colspan="6">
                  <?php 
                  if ($info == 0) {  echo "<h4><small>Dokumen Penawaran</small><b> Peserta ".$i."</b></h4>"; } else { ?>
                   <h4><small>Dokumen Penawaran </small><b><?php echo $arrRekanan[$i]?></b></h4>
                  <?php 
                  } ?>
                </td>
              </tr>
              <tr class="judul-kolom" style="background-color: #967adc; color: #fff">
                <td align="center" width="5px">No.</td>
                <td colspan="2">Nama File</td>
                <td align="center" width="20%">File</td>
                <!-- <td align="center">Ukuran File</td> -->
                <!-- <td align="center">Tgl Upload</td> -->
              </tr>
              <?php
              $no=1;
              $style="gelap";
              while($paket_dokumen->nextRow())
              {
              ?>                                        
                  <tr class="<?=$style?>">
                      <td><?=$no?>.</td>
                      <td colspan="2"><?=$paket_dokumen->getField("NAMA")?></td>
                      <td> 
                        <?=$paket_dokumen->getField("KETERANGAN")?> <br>
                        <small class="badge badge-info" style="font-size: 9px"><?=round($paket_dokumen->getField("UKURAN") / 1024, 2)?> Kb </small>
                        <small class="badge badge-info" style="font-size: 9px"><?=($paket_dokumen->getField("TGL_JAM_UPLOAD"))?></small>
                      </td>
                      <!-- <td align="right"> <?php // round($paket_dokumen->getField("UKURAN") / 1024, 2)?> Kb </td> -->
                      <!-- <td align="center">  -->
                        <?php 
                        // $ex = explode(" ", $paket_dokumen->getField("TGL_JAM_UPLOAD"));
                        // echo $ex[0];
                        ?> 
                      <!-- </td> -->
                  </tr>
              <?php
                  $no++;
              }
              ?>                        
            </table> 
          </div>
          <?php
            unset($paket_dokumen);
          }
          ?>  

          <div class="form-actions">
            <a href="main/index/paket_detil/?eid=<?=$reqId?>&key=<?= $reqUUID ?>" class="<?= CLASS_BTN_DANGER ?> mr-1"> <?= BTN_KEMBALI ?> </a> 
            <?php 
            // if (($reqMetodeLelangId == '1' || $reqMetodeLelangId == '7')) 
            // {
            ?>
            <!-- <a href="main/loadUrl/report/dokumen_penawaran_ba_pdf/?reqId=<?php // echo $reqId; ?>" target="_blank" class="<?php // echo CLASS_BTN_INFO; ?>"> <?php // echo BTN_PRINT_BA; ?> Pemasukan Penawaran</a> -->
            <?php 
            // } ?>
          </div> 
        </div>
      </div>
      </form>
    
    </div>
  </div> 
</div>   