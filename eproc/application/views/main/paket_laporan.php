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
$reqUUID = $paketInfo->uuid;

$paket_dokumen->selectByParams(array("PAKET_ID" => $reqId, "JENIS_DOKUMEN" => "LAPORAN_PAKET"));
?>
<script type="text/javascript">
$(document).ready(function() {

  $(function(){
    $('#ff').form({
      url:'paket_dokumen_json/dokumen_laporan',
      onSubmit:function(){
        var v=$(this).form('validate');
        if(v) showLoad();  // show the message box
        return v;
      },
      success:function(data){
        document.location.href = 'main/index/paket_laporan/?reqId=<?=$reqId?>';
        alertSuccess();
        hideLoad();
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
            <a href="main/index/paket_detil/?eid=<?=$reqId?>&key=<?=$reqUUID?>" class="<?= CLASS_BTN_DANGER ?>"> <?= BTN_KEMBALI ?></a>        
            <a href="main/loadUrl/report/paket_cetak_pdf/?reqId=<?=$reqId?>" class="<?= CLASS_BTN_INFO ?>" target="_blank"> <?= BTN_PRINT ?> Laporan</a>        
            <!-- <a href="main/loadUrl/report/paket_cetak_word/?reqId=<?=$reqId?>" class="<?= CLASS_BTN_SUCCESS ?>" target="_blank"> <i class="fa fa-file-word-o"></i> Cetak Laporan</a>         -->
          </div>
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
          </div> <br>

          <div class="card mb-1 border-blue border-darken-1">
            <div class="card-content">
              <div class="p-1">
                <div class="alert bg-primary alert-icon-left alert-arrow-left alert-dismissible mb-2" role="alert">
                  <span class="alert-icon"><i class="fa fa-th"></i></span>  <strong>Upload Laporan</strong>
                </div>
                <form id="ff" class="easyui-form form-horizontal" method="post" novalidate enctype="multipart/form-data">
                  <div class="row">
                    <div class="form-group col-md-12 mb-2">
                      <label>Nama Dokumen</label>
                      <input type="text" name="reqNamaDokumen" id="reqNamaDokumen" class="form-control easyui-validatebox span9" required/>
                    </div>
                  </div>
                  <div class="row">
                    <div class="form-group col-md-12 mb-2">
                      <label style="width: 100%">Keterangan</label>
                      <textarea name="reqKeterangan" id="reqKeterangan" cols="45" rows="5" class="easyui-validatebox span9" style="width: 100%"></textarea>
                    </div>
                  </div>
                  <div class="row">
                    <div class="form-group col-md-12 mb-2">
                      <label>File</label>
                      <input type="file" name="reqLinkFile" id="reqLinkFilePDF" class="easyui-validatebox" required validType="fileType['zip', 'pdf']" />
                      <?= UPLOAD_PDF_ZIP_10MB ?>
                    </div>
                  </div>

                  <div class="form-actions">
                    <input type="hidden" name="reqId" value="<?=$reqId?>" />
                    <button type="submit" class="<?= CLASS_BTN_PRIMARY ?>"><?= BTN_UPLOAD ?></button>
                  </div>
                </form>
              </div>
            </div>
          </div>

        </div>
      </div>
    </div>
  </div>
</div>
