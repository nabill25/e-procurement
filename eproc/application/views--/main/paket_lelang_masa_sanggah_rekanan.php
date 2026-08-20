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
$reqLinkFile= $_FILES['reqLinkFile'];
$reqBayar= httpFilterPost('reqBayar');
$reqDokumenId = httpFilterGet('reqDokumenId');
$submitSimpan= httpFilterPost('submitSimpan');

$this->libsession->cekSession($reqId);

$this->load->library("kauth");  $userLogin = new kauth();
$this->load->library("paketinfo"); $paketInfo = new paketinfo();
$this->load->model(array("PaketDokumen","Paketpemenang"));
$this->load->model("PaketTahap");
$this->load->library("FileHandler");
include_once("functions/string.func.php");
include_once("functions/date.func.php");
include_once("functions/default.func.php");

$paket_dokumen = new PaketDokumen();
$paket_tahap = new PaketTahap();
$paket_tahap_metode = new PaketTahap();
$file = new FileHandler();

$arrSanggah = MASA_SANGGAH;


$paketInfo->getPaket($reqId);
$reqNama = $paketInfo->nama;
$reqUUID = $paketInfo->uuid;

$jenis_tahap = $paket_tahap_metode->getJenisTahapById($reqId);
$paket_dokumen->selectByParams(array("PAKET_ID" => $reqId, "JENIS_DOKUMEN" => "SANGGAH", "PARENT_ID" => 0, "REKANAN_USER_ID" => $this->ID));
// echo "<pre>"; print_r($paket_dokumen); die();

$aktif_masa_sanggah = $paket_tahap->getCountByParamsAktif(array("URUT" => $arrSanggah[$jenis_tahap], "PAKET_ID" => $reqId, "TAMPILKAN" => 1));
$aktif_masa_sanggah2 = $paket_tahap->getCountByParamsBerlalu(array("URUT" => $arrSanggah[$jenis_tahap], "PAKET_ID" => $reqId, "TAMPILKAN" => 1));
// echo $aktif_masa_sanggah .'-'. $aktif_masa_sanggah2; die();
if($aktif_masa_sanggah > 0 && $aktif_masa_sanggah2 < 1 )
{
  $cekAktif = 1;
} else {
  $cekAktif = 0;
}

$getpaket_pemenang = new Paketpemenang();
$getpaket_pemenang->selectByParams(array("PAKET_ID" => $reqId, "PERINGKAT" => '1'), -1, -1);
$getpaket_pemenang->firstRow();
?>
<script type="text/javascript">
$(document).ready(function() {

	$(function(){
		$('#ff').form({
			url:'paket_sanggah_json/dokumen_sanggah_rekanan',
			onSubmit:function(){
				// return $(this).form('validate');
        var v=$(this).form('validate');
        if(v) {
          showLoad();
          return v;
        } else {
          hideLoad();
          return false;
        }
			},
			success:function(data){
        hideLoad();
        alertSuccess2(data);
        setTimeout(function () {
  				document.location.href = 'main/index/paket_lelang_masa_sanggah_rekanan/?reqId=<?=$reqId?>';
        }, 1000);
			}
		});

	});

});
</script>

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
        <h4 class="card-title text-white">Sanggah
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
          <?php
          if ($cekAktif == '0') {
             echo '<div class="alert alert-danger" style="color:#fff">
                      <span style="color: #fff">
                        Masa Sanggah belum dimulai atau sudah selesai.
                      </span>
                    </div>';
           } 

          if ($getpaket_pemenang->getField("REKANAN_ID") == $this->REKANAN_ID) {
              echo '<div class="alert alert-info">Anda pemenang tidak bisa melakukan sanggah.</div>';
          } else 
          {
          ?>

          <div class="table-responsive">
            <table class="table table-bordered table-hover" id="tbl_bidang">
              <tbody>
                <tr class="judul-kolom">
                  <th width="2%">No.</th>
                  <!-- <th colspan="2">Nama Dokumen</th> -->
                  <th>Sanggah/Jawab</th>
                  <!-- <th>Tanggal</th> -->
                  <th style="text-align: center; width: 15%">File</th>
                </tr>
                <?php
                  $i=1;
                  $totalSanggah = 0;
                  while($paket_dokumen->nextRow())
                  {
          					// Get Parent
          					$paket_dokumen_parent = new PaketDokumen();
          					$paket_dokumen_parent->selectByParams(array("PAKET_ID" => $reqId, "JENIS_DOKUMEN" => "SANGGAH", "PARENT_ID" => $paket_dokumen->getField("PAKET_DOKUMEN_ID") ));
                    $parentDate = explode(" ", $paket_dokumen->getField("TGL_JAM_UPLOAD"));
          					// echo "<pre>"; print_r($paket_dokumen_parent); die();
          			      ?>
                      <tr >
                          <td><?=$i?>.</td>
                          <!-- <td colspan="2"><?php //$paket_dokumen->getField("NAMA")?></td> -->
                          <td>
                            <?=$paket_dokumen->getField("KETERANGAN")?> <br>
                            <small><i class="fa fa-clock-o"></i> <?= getFormattedDate($paket_dokumen->getField("TANGGAL_UPLOAD")).' '.$parentDate[1] ?></small>
                          </td>
                          <td style="text-align: center;">
                          <?php if ($paket_dokumen->getField("PATH_FILE") !== null && $paket_dokumen->getField("PATH_FILE") !== '') { ?>
                            <a href="uploads/lelang/<?=$paket_dokumen->getField("PATH_FILE")?>" target="_blank" class="btn-aksi">
                                <?= ICON_DOWNLOAD ?> Download
                            </a>
                          <?php 
                            }
                          // echo '<a onClick="deleteData('paket_sanggah_json/delete/', '$paket_dokumen->getField("PAKET_DOKUMEN_ID")')" class="btn-aksi"><i class="fa fa-trash" aria-hidden="true"></i></a>' ?>
                          </td>
                      </tr>

                			<?php
            				// if (count($paket_dokumen_parent->nextRow())>0) {
            				  while($paket_dokumen_parent->nextRow())
                      {
                        $childDate = explode(" ", $paket_dokumen_parent->getField("TGL_JAM_UPLOAD"));

                			?>
                				<tr>
                            <td></td>
                            <td>
                              <i class="fa fa-arrow-right" aria-hidden="true"></i> <?=$paket_dokumen_parent->getField("KETERANGAN")?><br>
                              <small><i class="fa fa-clock-o"></i> <?= getFormattedDate($paket_dokumen_parent->getField("TANGGAL_UPLOAD")).' '.$childDate[1] ?></small>
                            </td>
                            <!-- <td><?=round($paket_dokumen_parent->getField("UKURAN") / 1024, 2)?> Kb</td> -->
                            <!-- <td><?=getFormattedDate($paket_dokumen_parent->getField("TANGGAL_UPLOAD"))?></td> -->
                            <td style="text-align: center;">
                            <?php if ($paket_dokumen_parent->getField("PATH_FILE") != '') { ?>
                               <a href="uploads/lelang/<?=$paket_dokumen_parent->getField("PATH_FILE")?>" target="_blank" class="btn-aksi">
                                  <?= ICON_DOWNLOAD ?> Download
                              </a>
                            <?php } else {  } ?>
                            </td>
                        </tr>
      				        <?php
                      } //} ?>
                 <?php
                    $totalSanggah++;

                    $i++;
                  }
                  ?>
              </tbody>
            </table>
          </div>

          <?php
          if ($cekAktif == '1' && $totalSanggah == 0) { 
            // $paket_dokumen_cek = new PaketDokumen();
            // $paket_dokumen_cek->selectByParams(array("PAKET_ID" => $reqId, "JENIS_DOKUMEN" => "SANGGAH", "REKANAN_USER_ID" => $this->REKANAN_ID ));
            // if ($paket_dokumen_cek->countRow() == 0) { 
          ?>
            <div class="card mb-1 border-blue border-darken-1" style="padding:10px">
              <form id="ff" class="easyui-form form-horizontal" method="post" novalidate enctype="multipart/form-data">
                <div class="alert bg-primary alert-icon-left alert-arrow-left alert-dismissible mb-2" role="alert">
                  <span class="alert-icon"><i class="fa fa-th"></i></span>  <strong>Sanggah</strong>
                </div>
                <div class="row">
                  <div class="form-group col-md-12 mb-2">
                    <label>Keterangan</label>
                    <textarea name="reqKeterangan" id="reqKeterangan" cols="45" rows="5" class="easyui-validatebox form-control" required></textarea>
                  </div>
                </div>

                <div class="row">
                  <div class="form-group col-md-12 mb-2">
                    <label style="width: 100%">Surat Sanggahan dan Bukti Pendukung</label>
                    <input type="file" name="reqLinkFile" id="reqLinkFilePDF" class="form-control easyui-validatebox" validType="fileType['pdf', 'jpg']" required />
                  </div>
                </div>

                  <input type="hidden" name="reqId" value="<?=$reqId?>" />
                  <div class="form-actions">
                    <a href="main/index/paket_lelang_masa_sanggah_rekanan/?reqId=<?=$reqId?>" class="<?= CLASS_BTN_INFO ?>"><i class="fa fa-refresh"></i> Refresh</a>
                    <button type="submit" class="<?= CLASS_BTN_PRIMARY ?>"><i class="fa fa-check-square-o"></i> Kirim</button>
                  </div>
              </form>
            </div>
          <?php
            }
            // }
          } ?>

          <div class="form-actions">
            <a href="main/index/paket_detil/?eid=<?=$reqId?>&key=<?=$reqUUID?>" class="<?= CLASS_BTN_DANGER ?>"><i class="fa fa-arrow-left"></i> Kembali</a>
          </div>

        </div>

      </div>
    </div>
  </div>
</div>
