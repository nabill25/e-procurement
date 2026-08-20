<?php
$this->libsession->cekSession();   
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 * 
 */

$this->load->library("kauth");  $userLogin = new kauth();
$this->load->library("paketinfo"); $paketInfo = new paketinfo();
$this->load->model(array("PaketDokumen","PaketTahap"));
$this->load->library("FileHandler");
include_once("functions/string.func.php");
include_once("functions/date.func.php");
include_once("functions/default.func.php");

$paket_dokumen = new PaketDokumen();
$file = new FileHandler();
$paket_tahap = new PaketTahap();
$paket_tahap_metode = new PaketTahap();

$reqMode = httpFilterRequest("reqMode");
$reqId = httpFilterRequest("reqId");
$reqCetak= httpFilterPost('reqCetak');
$reqNamaDokumen= httpFilterPost('reqNamaDokumen');
$reqKeterangan= httpFilterPost('reqKeterangan');
$reqLinkFile= isset($_FILES['reqLinkFile']) ? $_FILES['reqLinkFile'] : '';
$reqBayar= httpFilterPost('reqBayar');
$reqDokumenId = httpFilterGet('reqDokumenId');
$submitSimpan= httpFilterPost('submitSimpan');

$paketInfo->getPaket($reqId);
$reqNama = $paketInfo->nama;
$reqUUID = $paketInfo->uuid;

$jenis_tahap  = $paket_tahap_metode->getJenisTahapById($reqId);
$arrSanggah = MASA_SANGGAH;

$aktif_sanggah = $paket_tahap->getCountByParamsAktif(array("URUT" => $arrSanggah[$jenis_tahap], "PAKET_ID" => $reqId, "TAMPILKAN" => 1));
$aktif_sanggah2 = $paket_tahap->getCountByParamsBerlalu(array("URUT" => $arrSanggah[$jenis_tahap], "PAKET_ID" => $reqId, "TAMPILKAN" => 1));

if($aktif_sanggah  > 0 || $aktif_sanggah2  > 0) {
  $cekAktif = "1";
}
else
{
  $cekAktif = "0";
}

$paket_dokumen->selectByParams(array("PAKET_ID" => $reqId, "JENIS_DOKUMEN" => "SANGGAH", "PARENT_ID" => 0));
?>
<script type="text/javascript">
setTimeout(function () { document.location.reload(); }, 300000);
  
$(document).ready(function() {

  $(function(){
    $('#ff').form({
      url:'paket_sanggah_json/dokumen_sanggah_panitia',
      onSubmit:function(){
        return $(this).form('validate');
      },
      success:function(data){
        //alert(data);return false;
        document.location.href = 'main/index/paket_lelang_masa_sanggah/?reqId=<?=$reqId?>';
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
        <h4 class="card-title text-white">Sanggahan</h4>
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
                        Sanggah belum dimulai.
                      </span>
                    </div>';
          } else 
          { ?>
            <div class="table-responsive">
              <table class="table table-bordered mb-0" id="tbl_bidang">
                <tbody>
                  <tr class="judul-kolom">
                    <th width="2%">No.</th>
                    <th colspan="2">Penyedia</th>
                    <th width="30%">Sanggah</th>
                    <th width="30%">Jawab</th>
                    <th width="9%" class="text-center">Aksi</th>
                  </tr>
                  <?php
                    $i=1;
                    if ($paket_dokumen->countRow() <= 0) {
                      echo '<tr><td colspan="6">. : : Tidak ada data : : .</td></tr>';
                    } else 
                    { 
                      while($paket_dokumen->nextRow())
                      {
                        $parentDate = explode(" ", $paket_dokumen->getField("TGL_JAM_UPLOAD"));
                        // Get Parent
                        $paket_dokumen_parent = new PaketDokumen();
                        $paket_dokumen_parent->selectByParams(array("PAKET_ID" => $reqId, "JENIS_DOKUMEN" => "SANGGAH", "PARENT_ID" => $paket_dokumen->getField("PAKET_DOKUMEN_ID") ));
                    ?>
                      <tr >
                          <td><?=$i?>.</td>
                          <td colspan="2"><?= $paket_dokumen->getField("NMREKANAN")?></td>
                          <td>
                            <?=$paket_dokumen->getField("KETERANGAN")?> <br>
                            <small><i class="fa fa-clock-o"></i> <?= getFormattedDate($paket_dokumen->getField("TANGGAL_UPLOAD")).' '.$parentDate[1] ?></small><br>
                            <?php 
                            if ($paket_dokumen->getField("PATH_FILE")) { ?>
                            <a href="uploads/lelang/<?=$paket_dokumen->getField("PATH_FILE")?>" target="_blank" class="badge badge-primary">
                              <?= ICON_DOWNLOAD ?> Donwload 
                            </a>
                            <?php 
                            } ?>
                          </td>
                          <td>
                            <?php 
                            while($paket_dokumen_parent->nextRow())
                            {
                              $childDate = explode(" ", $paket_dokumen_parent->getField("TGL_JAM_UPLOAD"));

                            ?> 
                              <?=$paket_dokumen_parent->getField("KETERANGAN")?> <br>
                              <small><i class="fa fa-clock-o"></i> <?= getFormattedDate($paket_dokumen_parent->getField("TANGGAL_UPLOAD")).' '.$childDate[1] ?></small><br>
                              <?php 
                              if ($paket_dokumen_parent->getField("PATH_FILE")) { ?>
                              <a href="uploads/lelang/<?=$paket_dokumen_parent->getField("PATH_FILE")?>" target="_blank" class="badge badge-primary">
                                <?= ICON_DOWNLOAD ?> Donwload 
                              </a>
                              <?php 
                              } ?>
                              <br>
                            <?php 
                            } ?>
                          </td>
                          <td class="text-center">
                            <a href="main/index/paket_lelang_masa_sanggah_tanggapan/?reqId=<?=$reqId?>&reqParent=<?=$paket_dokumen->getField("PAKET_DOKUMEN_ID")?>" class="btn-aksi">
                                <i class="fa fa-comments-o fa-2x" aria-hidden="true"></i>
                            </a>
                            <a onClick="deleteData('paket_dokumen_json/delete/', '<?=$paket_dokumen->getField("PAKET_DOKUMEN_ID")?>')" class="btn-aksi">
                              <?= ICON_DELETE ?>
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
            <div class="col-md-12 alert alert-warning mt-1">Catatan: Halaman Sanggah akan terefresh otomatis setiap 5 menit</div>

            <div class="form-actions mt-1">
              <input type="hidden" name="reqId" value="<?=$reqId?>" />
              <a href="main/index/paket_detil/?eid=<?=$reqId?>&key=<?=$reqUUID?>" class="<?= CLASS_BTN_DANGER ?> mr-1"> <?= BTN_KEMBALI ?> </a> 
              <a href="main/index/paket_lelang_tambah_jadwal/?reqId=<?=$reqId?>&back=1" class="<?= CLASS_BTN_INFO ?> pull-right ml-1"> <span class="fa fa-pencil-square-o"></span> Sanggah Terbukti Benar ?  </a> 
            </div> 
          <?php 
          } ?>
        </div>
      </div>
    </div>
  </div> 
</div>   
