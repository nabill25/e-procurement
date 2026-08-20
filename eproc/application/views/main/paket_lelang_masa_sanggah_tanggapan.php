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
$this->load->model("PaketDokumen");
$this->load->model("Rekanan");
$this->load->library("FileHandler");
include_once("functions/string.func.php");
include_once("functions/date.func.php");
include_once("functions/default.func.php");

$paket_dokumen = new PaketDokumen();
$file = new FileHandler();

$reqMode = httpFilterRequest("reqMode");
$reqId = httpFilterRequest("reqId");
$reqCetak= httpFilterPost('reqCetak');
$reqNamaDokumen= httpFilterPost('reqNamaDokumen');
$reqKeterangan= httpFilterPost('reqKeterangan');
$reqLinkFile= $_FILES['reqLinkFile'];
$reqBayar= httpFilterPost('reqBayar');
$reqDokumenId = httpFilterGet('reqDokumenId');
$submitSimpan= httpFilterPost('submitSimpan');

$paketInfo->getPaket($reqId);
$reqNama = $paketInfo->nama;
$reqParent = $this->input->get('reqParent') ? $this->input->get('reqParent') : '';

$paket_dokumen->selectByParams(array("PAKET_ID" => $reqId, "JENIS_DOKUMEN" => "SANGGAH", "paket_dokumen_id" => $reqParent, "PARENT_ID" => 0 ));
?>
<script type="text/javascript">
$(document).ready(function() {

	$(function(){
		$('#ff').form({
			url:'paket_sanggah_json/dokumen_sanggah_tanggapan',
			onSubmit:function(){
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
				document.location.href = 'main/index/paket_lelang_masa_sanggah_tanggapan/?reqId=<?=$reqId?>&reqParent=<?=$reqParent?>';
			}
		});

	});

});
</script>


<div class="row">
  <div class="col-md-12 col-sm-12">
    <div class="card">
      <div class="card-header card-head-inverse bg-primary">
        <h4 class="card-title text-white">Jawab Sanggah
        </h4>
        <div class="heading-elements">
            <ul class="list-inline mb-0">
              <li><a data-action="expand"><i class="ft-maximize"></i></a></li>
            </ul>
        </div>
      </div>
      <div class="card-content collapse show border-info border-darken-2">
        <div class="card-body area-datatable">
            <table  class="table table-bordered mb-1" id="tbl_bidang">
              <tbody>
                <tr class="judul-kolom">
                  <th width="2%">No.</th>
                  <!-- <th colspan="2">Nama Dokumen</th> -->
                  <th>Sanggah/Jawab</th>
                  <th width="15%">Tanggal</th>
                  <th width="9%">Aksi</th>
                </tr>
                <?php
                  $i=1;
                  while($paket_dokumen->nextRow())
                  {
                    if ($i=1) {
                      $reqRekananUserId = $paket_dokumen->getField("REKANAN_USER_ID");
                      $rekanan = new Rekanan();
                      $rekanan->selectByParams(array("A.REKANAN_ID"=>$reqRekananUserId),-1,-1);
                      $rekanan->firstRow();
                      $rekananNama = $rekanan->getField("NAMA");
                      // echo '<input type="hidden" name="reqPaketDokumenId" value="'.$paket_dokumen->getField("REKANAN_USER_ID").'" />';
                    }
                    // Get Parent
                    $paket_dokumen_parent = new PaketDokumen();
                    $paket_dokumen_parent->selectByParams(array("PAKET_ID" => $reqId, "JENIS_DOKUMEN" => "SANGGAH", "PARENT_ID" => $reqParent ));
                ?>
                <tr >
                    <td><?=$i?>.</td>
                    <td><?= $rekananNama.'<br>'.$paket_dokumen->getField("KETERANGAN")?></td>
                    <td><?=getFormattedDate($paket_dokumen->getField("TANGGAL_UPLOAD"))?></td>
                    <td>
                    <?php
                    if ($paket_dokumen->getField("PATH_FILE")) { ?>
                    <a href="uploads/lelang/<?=$paket_dokumen->getField("PATH_FILE")?>" target="_blank" class="btn-aksi">
                        <?= ICON_DOWNLOAD ?>
                    </a>
                    <?php
                    } ?>
                    </td>
                </tr>
                <?php
                  while($paket_dokumen_parent->nextRow())
                    {
                ?>
                  <tr >
                    <td></td>
                    <td> <i class="fa fa-arrow-right" aria-hidden="true"></i> <?=$paket_dokumen_parent->getField("KETERANGAN")?></td>
                    <td><?=getFormattedDate($paket_dokumen_parent->getField("TANGGAL_UPLOAD"))?></td>
                    <td>
                      <?php
                      if ($paket_dokumen_parent->getField("PATH_FILE")) { ?>
                      <a href="uploads/lelang/<?=$paket_dokumen_parent->getField("PATH_FILE")?>" target="_blank" class="btn-aksi">
                          <?= ICON_DOWNLOAD ?>
                      </a>
                      <?php
                      } ?>
                      <a onClick="deleteData('paket_sanggah_json/delete/', '<?=$paket_dokumen_parent->getField("PAKET_DOKUMEN_ID")?>')" class="btn-aksi">
                        <?= ICON_DELETE ?>
                      </a>
                    </td>
                </tr>
                <?php }
                $i++;
              }
              ?>
              </tbody>
            </table>

          <div class="card mb-1 border-blue border-darken-1" style="padding:10px">

            <form id="ff" class="easyui-form " method="post" novalidate enctype="multipart/form-data">
                <div class="alert alert-info">Jawab Sanggah</div>

                <div class="row">
                  <div class="form-group col-md-12">
                    <!-- <label>Jawab</label> -->
                    <textarea name="reqKeterangan" id="reqKeterangan" cols="45" rows="5" class="easyui-validatebox form-control" required></textarea>
                  </div>
                </div>

                <div class="row">
                  <div class="form-group col-md-12">
                    <label>Lampiran</label>
                    <input type="file" class="form-control easyui-validatebox" name="reqLinkFile" id="reqLinkFilePDF" validType="fileType['pdf', 'jpg']" required />
                  </div>
                </div>

                <div class="form-actions">
                  <input type="hidden" name="reqId" value="<?=$reqId?>" />
                  <input type="hidden" name="reqPaketDokumenId" value="<?=$reqParent?>" />
                  <input type="hidden" name="reqRekananUserId" value="<?=$reqRekananUserId?>" />
                  <a href="main/index/paket_lelang_masa_sanggah/?reqId=<?=$reqId?>" class="<?= CLASS_BTN_DANGER ?>"><i class="fa fa-arrow-left"></i> Kembali</a>
                  <button type="submit" class="<?= CLASS_BTN_PRIMARY ?>"><i class="fa fa-check-square-o"></i> Kirim</button>
                </div>
            </form>
          </div>

        </div>
      </div>
    </div>
  </div>
</div>
