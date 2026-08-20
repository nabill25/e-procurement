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
$this->load->model("PaketAanwijzing");
$this->load->library("FileHandler");
include_once("functions/string.func.php");
include_once("functions/date.func.php");
include_once("functions/default.func.php");

$paket_aanwijzing = new PaketAanwijzing();
$file = new FileHandler();


$paketInfo->getPaket($reqId);
$reqNama = $paketInfo->nama;
$reqParent = $this->input->get('reqParent') ? $this->input->get('reqParent') : '';

$paket_aanwijzing->selectByParamsKualifikasi(array("PAKET_ID" => $reqId, "paket_aanwijzing_kualifikasi_id" => $reqParent, "PARENT_ID" => 0 ));
?>
<script type="text/javascript">
$(document).ready(function() {

	$(function(){
		$('#ff').form({
			url:'aanwijzing_chat_json/dokumen_aanwijzing_kualifikasi_tanggapan',
			onSubmit:function(){
				return $(this).form('validate');
			},
			success:function(data){
				// alert(data);return false;
				document.location.href = 'main/index/aanwijzing_kualifikasi_chat_tanggapan/?reqId=<?=$reqId?>&reqParent=<?=$reqParent?>';
			}
		});
	});

});
</script>


<div class="row"> 
  <div class="col-md-12 col-sm-12">
    <div class="card">
      <div class="card-header card-head-inverse bg-primary">
        <h4 class="card-title text-white">Jawaban Aanwijzing Kualifikasi
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
                  <th>Para Pihak</th>
                  <th style="width: 70%">Tanya Jawab</th>  
                  <th style="width: 10%" class="text-center">Aksi</th>
                </tr>
                <?php
                  $i=1;
                  while($paket_aanwijzing->nextRow())
                  {
                    $tglupload = explode('.', $paket_aanwijzing->getField("TANGGAL_UPLOAD"));
                    if ($i=1) {
                        $reqRekananUserId = $paket_aanwijzing->getField("REKANAN_USER_ID");
                        // echo '<input type="hidden" name="reqPaketDokumenId" value="'.$paket_aanwijzing->getField("REKANAN_USER_ID").'" />';
                    }
                    // Get Parent
                    $paket_aanwijzing_parent = new PaketAanwijzing();
                    $paket_aanwijzing_parent->selectByParamsKualifikasi(array("PAKET_ID" => $reqId, "PARENT_ID" => $reqParent ));
                ?>
                <tr >
                    <td><i class="fa fa-user"></i> <?=$paket_aanwijzing->getField("KODE_CUT")?></td> 
                    <td>
                        <?=$paket_aanwijzing->getField("KETERANGAN")?> <br>
                        <small><i class="fa fa-clock-o"></i> <?=$tglupload[0] ?></small>
                    </td> 
                    <td> 
                      <!-- <a onClick="deleteData('aanwijzing_chat_json/deletekualifikasi/', 'Apa Intip2<?php // $paket_aanwijzing->getField("PAKET_AANWIJZING_KUALIFIKASI_ID")?>')" class="btn-aksi"><i class="fa fa-trash" aria-hidden="true"></i></a> -->
                    </td>
                </tr>
                <?php
                // if (count($paket_aanwijzing_parent->nextRow())>=1) {

                  while($paket_aanwijzing_parent->nextRow())
                    {
                    $tglupload_parent = explode('.', $paket_aanwijzing_parent->getField("TANGGAL_UPLOAD"));
                ?>
                  <tr >
                    <td><i class="fa fa-arrow-right" aria-hidden="true"></i> Jawab <small><?=$paket_aanwijzing->getField("KODE_CUT")?></small> </td>
                    <td>
                        <?=$paket_aanwijzing_parent->getField("KETERANGAN")?> <br>
                        <small><i class="fa fa-clock-o"></i> <?=$tglupload_parent[0] ?></small>
                    </td> 
                    <td class="text-center">
                    <?php 
                    if ($paket_aanwijzing_parent->getField("PATH_FILE")) { ?>
                    <a href="uploads/aanwijzing/<?=$paket_aanwijzing_parent->getField("PATH_FILE")?>" target="_blank" class="btn-aksi">
                        <?= ICON_DOWNLOAD ?>
                    </a>
                    <?php 
                    } ?>
                    <!-- <a onClick="deleteData('aanwijzing_chat_json/deletekualifikasi/', 'Intip2 ya<?php //$paket_aanwijzing_parent->getField("PAKET_AANWIJZING_KUALIFIKASI_ID")?>')" class="btn-aksi"><i class="fa fa-trash" aria-hidden="true"></i></a> -->
                    </td>
                </tr>
                <?php }  
                $i++;
                // }
              }
              ?>
              </tbody>
            </table>

            <form id="ff" class="easyui-form " method="post" novalidate enctype="multipart/form-data"> 

                <div class="row">
                  <div class="form-group col-md-12">
                    <label>Jawab</label>
                    <textarea name="reqKeterangan" id="reqKeterangan" cols="45" rows="5" class="easyui-validatebox form-control" required></textarea>
                  </div> 
                </div>

                <div class="row">
                  <div class="form-group col-md-12">
                    <label>Lampiran <?= UPLOAD_PDF_2MB ?></label>
                    <input type="file" class="form-control" name="reqLinkFile" id="reqLinkFilePDF" required validType="fileType['pdf', 'jpg']" />
                  </div> 
                </div>

                <div class="form-actions">
                  <input type="hidden" name="reqId" value="<?=$reqId?>" />
                  <input type="hidden" name="reqPaketDokumenId" value="<?=$reqParent?>" />
                  <input type="hidden" name="reqRekananUserId" value="<?=$reqRekananUserId?>" />
                  <a href="main/index/aanwijzing_kualifikasi_chat/?reqId=<?=$reqId?>" class="<?= CLASS_BTN_DANGER ?> mr-1"><i class="fa fa-arrow-left"></i> Kembali</a>
                  <button type="submit" class="<?= CLASS_BTN_PRIMARY ?>"><i class="fa fa-check-square-o"></i> Kirim Jawaban</button>
                </div> 
            </form>

        </div>
      </div>
    </div>
  </div> 
</div>  

<div class="span12">
 <div class="card">
  <h3 class="card-heading simple"></h3>
  <div class="card-body">

  </div>
 </div>
</div>
