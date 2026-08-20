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
$this->load->model(array("PaketEvaluasiAdminTawar","PaketEvaluasiTeknisTawar","PaketEvaluasiHargaTawar","PaketDokumen")); 
$this->load->model("PaketKlarifikasi");
$this->load->model(array("PaketRekanan","Rekanan"));
$this->load->library("FileHandler");
include_once("functions/string.func.php");
include_once("functions/date.func.php");
include_once("functions/default.func.php");


$paket_evaluasi_admin = new PaketEvaluasiAdminTawar();
$paket_evaluasi_teknis = new PaketEvaluasiTeknisTawar();
$paket_evaluasi_harga = new PaketEvaluasiHargaTawar();

$paket_rekanan = new PaketRekanan();
$paket_klarifikasi = new PaketKlarifikasi();
$rekanan = new Rekanan();
$file = new FileHandler();

$paketInfo->getPaket($reqId);
$reqNama = $paketInfo->nama;
$reqJenisPengadaan = $paketInfo->jenis;
$reqMetodeEvaluasi = $paketInfo->metode_evaluasi;
$reqMetodePengadaan = $paketInfo->metode_lelang_id;
$reqBidding = $paketInfo->bidding;
$reqRekananIdPemenang = $paketInfo->rekanan_id_pemenang;
$reqSistemSampul = $paketInfo->sistem_sampul;
$reqMetodeEvaluasiId = $paketInfo->metode_lelang_id;
$reqMultiPemenang = $paketInfo->multi_pemenang;

$reqRekananId = $this->input->get('reqRekananId') ? $this->input->get('reqRekananId') : '';
if ($reqBidding == 1) { // e-Reverse Auction
  $paket_rekanan->selectUrutPenawaran(array("A.PAKET_ID" => $reqId, "A.REKANAN_ID" => $reqRekananId, "A.LULUS_PENDAFTARAN" => 1, "A.KIRIM_PENAWARAN" => 1, "A.LULUS_PENAWARAN" => 1), -1, -1, "", "", " ORDER BY NILAI_PENAWARAN ASC, KIRIM_PENAWARAN_TANGGAL ASC ");
  // echo $paket_rekanan->countRow(); die();
  if ($paket_rekanan->countRow() == 0 ) // bukan peserta
  {
    redirect(base_url('main/index/404'));
  }
} else { // Negosiasi
  if ($reqMultiPemenang == '1') { // Multi Pemenang
    $paket_rekanan->selectUrutPenawaran(array("A.PAKET_ID" => $reqId, "A.REKANAN_ID" => $reqRekananId, "A.LULUS_PENDAFTARAN" => 1, "A.KIRIM_PENAWARAN" => 1, "A.LULUS_PENAWARAN" => 1), -1, -1, "", "", " ORDER BY NILAI_PENAWARAN ASC, KIRIM_PENAWARAN_TANGGAL ASC ");
    // echo $paket_rekanan->countRow(); die();
    if ($paket_rekanan->countRow() == 0 ) // bukan peserta
    {
      redirect(base_url('main/index/404'));
    }
  } else {
    // tampilkan rekanan yang sudah pernah di klarifikasi
    $paket_dokumen_klarifikasi = new PaketDokumen();
    $paket_dokumen_klarifikasi->selectByParamsGroupRekId(array("PAKET_ID" => $reqId), -1, -1, " AND VERIFIKASI != ''  AND REKANAN_USER_ID NOT IN (".$reqRekananIdPemenang.")"); // yang sudah pernah di klarifikasi
    while($paket_dokumen_klarifikasi->nextRow())
    {
      $arrRekId[] = $paket_dokumen_klarifikasi->getField("REKANAN_USER_ID");
    }  
    // echo "<pre>"; print_r($arrRekId); die;
    // echo $reqRekananId; die;
    if ($reqRekananId != $reqRekananIdPemenang && !in_array($reqRekananId, $arrRekId)) // bukan pemenang
    {
      redirect(base_url('main/index/404'));
    }
  }
}

$paket_klarifikasi->selectByParams(array("PAKET_ID" => $reqId, "REKANAN_USER_ID" => $reqRekananId));
$reqRekananUserId = $reqRekananId; // rekanan yang sedang di chat
 
$rekanan->selectByParams(array("A.REKANAN_ID" => $reqRekananId), -1, -1);
$rekanan->firstRow();
$rekanan_nama = $rekanan->getField("NAMA");
$rekanan_npwp = $rekanan->getField("NPWP");
$rekanan_alamat = $rekanan->getField("ALAMAT");
$rekanan_telepon = $rekanan->getField("TELEPON_FULL");
$rekanan_email = $rekanan->getField("EMAIL");
$rekanan_kota = $rekanan->getField("KOTA");
$rekanan_kodepos = $rekanan->getField("KODEPOS");
?>
<script type="text/javascript">
$(document).ready(function() {

	$(function(){
		$('#ff').form({
			url:'klarifikasi_chat_json/dokumen_aanwijzing_tanggapan',
			onSubmit:function(){
				return $(this).form('validate');
			},
			success:function(data){
				// alert(data);return false;
        alertSuccess2(data);
        setTimeout(function() {
				  document.location.href = 'main/index/klarifikasi_chat_tanggapan/?reqId=<?=$reqId?>&reqRekananId=<?=$reqRekananId?>';
        }, 2000);
			}
		});

	});

});
</script>

<style type="text/css">
input::placeholder {
  opacity: 0.3 !important;
}
table th {
  padding: 5px !important;
}
.terang {
  background-color: rgba(245, 247, 250, .5);
}
.headerTR {
  background-color: #77c8e5 !important;
}
</style>

<div class="row">
  <div class="col-md-5 col-sm-5">
    <div class="card">
      <div class="card-header card-head-inverse bg-primary" id="card-header-klarifikasi">
        <h4 class="card-title text-white">Chat Pembuktian
        </h4>
        <div class="heading-elements">
            <ul class="list-inline mb-0">
              <li><a data-action="expand"><i class="ft-maximize"></i></a></li>
            </ul>
        </div>
      </div>
      <div class="card-content collapse show border-info border-darken-2">
        <div class="card-body area-datatable">
            <div class="form-group col-md-12 mb-1 border-blue border-darken-1" style="margin-bottom: 1px solid #b7b7b7; padding:10px; border-radius:10px">
              <h3><b>Informasi Penyedia</b></h3>
              <h2><?= $rekanan_nama ?></h2>
              <table style="width: 100%">
                <tr> <td><i class="fa fa-id-card"></i> <?= $rekanan_npwp ?> <span class="badge badge-info">NPWP</span></td> </tr>
                <tr> <td><i class="fa fa-phone"></i> Telepon: <?= $rekanan_telepon ?></td> </tr>
                <tr> <td><i class="fa fa-envelope"></i> Email: <?= $rekanan_email ?></td> </tr>
                <tr> <td><i class="fa fa-map-marker"></i> <?= $rekanan_alamat.', '.$rekanan_kota.' '.$rekanan_kodepos ?></td> </tr>
              </table>
            </div>
            <table  class="table table-bordered mb-1" id="tbl_bidang">
              <tbody>
                <?php
                if ($paket_klarifikasi->countRow() < 0) {
                   echo '<tr><td colspan="2">Belum ada chat</td></tr>';
                } else {
                  $i=1;
                  while($paket_klarifikasi->nextRow())
                  {
                    $tglupload = explode('.', $paket_klarifikasi->getField("TANGGAL_UPLOAD"));
                ?>
                  <tr >
                    <td width="90%">
                      <i class="fa fa-user"></i> <?=$paket_klarifikasi->getField("USER_NAMA")?> <br>
                        <?=$paket_klarifikasi->getField("KETERANGAN")?> <br>
                        <?php if ($paket_klarifikasi->getField("PATH_FILE")) { ?>
                          <a href="uploads/klarifikasi/<?=$paket_klarifikasi->getField("PATH_FILE")?>" target="_blank" class="badge badge-primary">
                              <i class="fa fa-download" aria-hidden="true"></i> Donwload
                          </a><br>
                        <?php } ?>
                        <small><i class="fa fa-clock-o"></i> <?=$tglupload[0] ?></small>
                    </td>
                    <td width="3%">
                      <?php
                        if ($paket_klarifikasi->getField("CREATED_BY") == $this->USER_LOGIN_ID)
                        { ?>
                        <a onClick="deleteData('klarifikasi_chat_json/delete/', '<?=$paket_klarifikasi->getField("PAKET_KLARIFIKASI_ID")?>')" class="btn-aksi">
                          <?= ICON_DELETE ?>
                        </a>
                      <?php
                      } ?>
                    </td>
                  </tr>
                <?php
                  $i++;
                  }
                }
              ?>
              </tbody>
            </table>

            <form id="ff" class="easyui-form " method="post" novalidate enctype="multipart/form-data">

                <div class="row">
                  <div class="form-group col-md-12">
                    <label>Ketik pesan disini</label>
                    <textarea name="reqKeterangan" id="reqKeterangan" cols="45" rows="5" class="easyui-validatebox form-control" required></textarea>
                  </div>
                </div>

                <div class="row">
                  <div class="form-group col-md-12">
                    <label>Lampiran <?= UPLOAD_PDF_2MB ?></label>
                    <input type="file" class="form-control" name="reqLinkFile" id="reqLinkFilePDF" required validType="fileType['pdf']" />
                  </div>
                </div>

                <div class="form-actions">
                  <input type="hidden" name="reqId" value="<?=$reqId?>" />
                  <input type="hidden" name="reqPaketDokumenId" value="<?=$reqParent?>" />
                  <input type="hidden" name="reqRekananUserId" value="<?=$reqRekananUserId?>" />
                  <a href="main/index/klarifikasi_chat?reqId=<?=$reqId?>" class="<?= CLASS_BTN_DANGER ?> mr-1"><?= BTN_KEMBALI ?></a>
                  <button type="submit" class="<?= CLASS_BTN_PRIMARY ?> mr-1"><?= BTN_KIRIM ?></button>
                  <a href="main/index/klarifikasi_chat_tanggapan/?reqId=<?=$reqId?>&reqRekananId=<?=$reqRekananUserId?>" class="<?= CLASS_BTN_INFO ?> mr-1 pull-right"><?= BTN_REFRESH ?></a>
                </div>
            </form>

        </div>
      </div>
    </div>
  </div>
  <div class="col-md-7 col-sm-7">
    <div class="card">
      <div class="card-header card-head-inverse bg-primary" id="card-header-klarifikasi">
        <h4 class="card-title text-white">Dokumen Penawaran
        </h4>
        <div class="heading-elements">
            <ul class="list-inline mb-0">
              <li><a data-action="expand"><i class="ft-maximize"></i></a></li>
            </ul>
        </div>
      </div>
      <div class="card-content collapse show border-info border-darken-2">
        <div class="card-body area-datatable" style="height: 490px; overflow-y: scroll;">
          <h3>Checklist Kelengkapan 
          <a href="main/loadUrl/report/klarifikasi_cetak_pdf/?reqId=<?=$reqId?>&reqRekananId=<?=$reqRekananUserId?>" target="_blank" id="btnCetak"  class="<?= CLASS_BTN_PRIMARY ?> text-white pull-right mb-1"><?= BTN_PRINT ?> Hasil Pembuktian</a>
          </h3> 
          <form id="ff2" class="easyui-form " method="post" novalidate enctype="multipart/form-data">

            <table  class="table table-bordered table-hover mb-1" id="tbl_bidang">
              <tr style="background-color: #40576a !important; color:#fff; padding:20px !important">
                <th width="1%" class="text-center">No.</th>
                <th>Dokumen</th>
                <th style="width: 120px;" class="text-center">Pembuktian</th>
              </tr>
              <?php
              if ($reqMetodePengadaan != 7) 
              { 
                $paket_evaluasi_admin->selectByParams(array("PAKET_ID" => $reqId));
                if ($paket_evaluasi_admin->countRow() > 0) {
                  echo '<tr class="headerTR"><td colspan="2">Dokumen Administrasi</td><td>Sesuai?</td></tr>';
                }
                $no = 1;
                while($paket_evaluasi_admin->nextRow())
                { ?>
                  <tr class="terang">
                    <td class="text-center" width="10px" rowspan="2"><?=$no?></td>
                    <td> 
                    <?php 
                    $paket_dokumen = new PaketDokumen();
                    $paket_dokumen->selectByParams(array("PAKET_ID" => $reqId, "REKANAN_USER_ID" => $reqRekananId, "TRIM(NAMA)" => trim($paket_evaluasi_admin->getField("NAMA"))));
                    $paket_dokumen->firstRow(); 
                    $checkedAdmin = '';
                    if ($paket_dokumen->getField("VERIFIKASI") == '1') {
                      $checkedAdmin = 'checked';
                    }
                    ?> 
                      <?php
                      if($paket_dokumen->getField("PATH_FILE") == "")
                      {
                      ?>
                        <?=$paket_evaluasi_admin->getField("NAMA")?> <?php if($paket_evaluasi_admin->getField("WAJIB") == '1'){ ?><font color="#FF0000">* </font><?php } ?>
                      <?php 
                      }
                      else
                      {
                      ?>
                        <a href="uploads/penawaran/<?=$paket_dokumen->getField("PATH_FILE")?>" target="_blank"><?= ICON_DOWNLOAD ?> &nbsp;
                          <?=$paket_evaluasi_admin->getField("NAMA")?> <?php if($paket_evaluasi_admin->getField("WAJIB") == '1'){ ?><font color="#FF0000">* </font><?php } ?>
                        </a>
                      <?php
                      }
                    ?>
                    </td>
                    <td class="text-left">
                      <?php 
                      if ($paket_dokumen->getField("PAKET_DOKUMEN_ID")) 
                      { ?> 
                        <input type="radio" value="1" id="check<?=$paket_dokumen->getField("PAKET_DOKUMEN_ID")?>" onclick="return test('<?= trim($paket_evaluasi_admin->getField("NAMA")) ?>','<?=$paket_dokumen->getField("PAKET_DOKUMEN_ID")?>')" <?php if($paket_dokumen->getField("VERIFIKASI") == "1") { ?> checked <?php } ?> name="admin<?=$paket_dokumen->getField("PAKET_DOKUMEN_ID")?>"> Ya <br>
                        <input type="radio" value="0" id="check<?=$paket_dokumen->getField("PAKET_DOKUMEN_ID")?>" onclick="return test('<?= trim($paket_evaluasi_admin->getField("NAMA")) ?>','<?=$paket_dokumen->getField("PAKET_DOKUMEN_ID")?>')" <?php if($paket_dokumen->getField("VERIFIKASI") == "0") { ?> checked <?php } ?> name="admin<?=$paket_dokumen->getField("PAKET_DOKUMEN_ID")?>"> Tidak
                      <?php 
                      } else {
                        echo '<span class="badge badge-danger">Tidak upload</span>';
                      } ?>
                    </td>
                  </tr>
                  <tr>
                    <td colspan="2">
                      <?php 
                      if ($paket_dokumen->getField("PAKET_DOKUMEN_ID")) 
                      { ?> 
                        <input type="" class="form-control easyui-validatebox span2" style="height: 30px !important;" name="" placeholder="catatan" id="catatan<?=$paket_dokumen->getField("PAKET_DOKUMEN_ID")?>" value="<?=$paket_dokumen->getField("CATATAN")?>" onChange="return test('<?= trim($paket_evaluasi_admin->getField("NAMA")) ?>','<?=$paket_dokumen->getField("PAKET_DOKUMEN_ID")?>')">
                        <small><sup>*</sup>&nbsp;Tekan enter setalah mengisi catatan</small>
                      <?php 
                      } else {
                        echo '';
                      } ?>
                    </td>
                  </tr>
                <?php 
                      unset($paket_dokumen);
                  $no++;
                } ?>

                <tr>
                  <td colspan="3">
                    <?php 
                    if ($reqSistemSampul == '1') { ?>
                      <a href="<?= base_url('main/index/evaluasi_penawaran_administrasi/?reqId='.$reqId) ?>" class="<?= CLASS_BTN_SUCCESS ?> btn-sm"> <span class="fa fa-pencil"></span> Ubah Hasih Eval. Administrasi</a>
                    <?php 
                    } else { ?>
                      <a href="<?= base_url('main/index/evaluasi_penawaran_administrasi_sampul1/?reqId='.$reqId) ?>" class="<?= CLASS_BTN_SUCCESS ?> btn-sm"> <span class="fa fa-pencil"></span> Ubah Hasih Eval. Administrasi</a>
                    <?php 
                    } ?>
                  </td>
                </tr>
                <tr><td colspan="3"></td></tr>
              <?php 
              } ?>

              <?php 
              if ($reqMetodePengadaan != 7) 
              { 
                $no = 1;
                $paket_evaluasi_teknis->selectByParams(array("PAKET_ID" => $reqId));
                if ($paket_evaluasi_teknis->countRow() > 0) {
                  echo '<tr class="headerTR"><td colspan="2">Dokumen Teknis</td><td>Sesuai?</td></tr>';
                }
                while($paket_evaluasi_teknis->nextRow())
                { ?>
                  <tr class="terang">
                    <td class="text-center" width="10px" rowspan="2"><?=$no?></td>
                    <td>
                    <?php 
                    $paket_dokumen = new PaketDokumen();
                    $paket_dokumen->selectByParams(array("PAKET_ID" => $reqId, "REKANAN_USER_ID" => $reqRekananId, "TRIM(NAMA)" => trim($paket_evaluasi_teknis->getField("NAMA"))));
                    $paket_dokumen->firstRow(); 
                    $checkedTeknis = '';
                    if ($paket_dokumen->getField("VERIFIKASI") == '1') {
                      $checkedTeknis = 'checked';
                    }
                    ?>
                      <?php
                      if($paket_dokumen->getField("PATH_FILE") == "")
                      { ?>
                        <?=$paket_evaluasi_teknis->getField("NAMA")?> <?php if($paket_evaluasi_teknis->getField("WAJIB") == '1'){ ?><font color="#FF0000">* </font><?php } ?>
                      <?php 
                      }
                      else
                      {
                      ?>
                        <a href="uploads/penawaran/<?=$paket_dokumen->getField("PATH_FILE")?>" target="_blank"><?= ICON_DOWNLOAD ?> &nbsp;
                        <?=$paket_evaluasi_teknis->getField("NAMA")?> <?php if($paket_evaluasi_teknis->getField("WAJIB") == '1'){ ?><font color="#FF0000">* </font><?php } ?>
                        </a>
                      <?php
                      }
                    ?>
                    </td>
                    <td class="text-left"> 
                      <?php 
                      if ($paket_dokumen->getField("PAKET_DOKUMEN_ID")) 
                      { ?>
                        <input type="radio" value="1" id="check<?=$paket_dokumen->getField("PAKET_DOKUMEN_ID")?>" onclick="return test('<?= trim($paket_evaluasi_teknis->getField("NAMA")) ?>','<?=$paket_dokumen->getField("PAKET_DOKUMEN_ID")?>')" <?php if($paket_dokumen->getField("VERIFIKASI") == "1") { ?> checked <?php } ?> name="admin<?=$paket_dokumen->getField("PAKET_DOKUMEN_ID")?>"> Ya <br>
                        <input type="radio" value="0" id="check<?=$paket_dokumen->getField("PAKET_DOKUMEN_ID")?>" onclick="return test('<?= trim($paket_evaluasi_teknis->getField("NAMA")) ?>','<?=$paket_dokumen->getField("PAKET_DOKUMEN_ID")?>')" <?php if($paket_dokumen->getField("VERIFIKASI") == "0") { ?> checked <?php } ?> name="admin<?=$paket_dokumen->getField("PAKET_DOKUMEN_ID")?>"> Tidak
                      <?php 
                      } else {
                        echo '<span class="badge badge-danger">Tidak upload</span>';
                      } ?>
                    </td>
                  </tr>
                  <tr>
                    <td colspan="2">
                      <?php 
                      if ($paket_dokumen->getField("PAKET_DOKUMEN_ID")) 
                      { ?> 
                        <input type="" class="form-control easyui-validatebox span2" style="height: 30px !important;" name="" placeholder="catatan" id="catatan<?=$paket_dokumen->getField("PAKET_DOKUMEN_ID")?>" value="<?=$paket_dokumen->getField("CATATAN")?>" onChange="return test('<?= trim($paket_evaluasi_teknis->getField("NAMA")) ?>','<?=$paket_dokumen->getField("PAKET_DOKUMEN_ID")?>')">
                        <small><sup>*</sup>&nbsp;Tekan enter setalah mengisi catatan</small>
                      <?php 
                      } else {
                        echo '';
                      } ?>
                    </td>
                  </tr>
                <?php 
                      unset($paket_dokumen);
                  $no++;
                } ?>

                <tr>
                  <td colspan="3">
                    <?php 
                    if ($reqSistemSampul == '1') { ?>
                      <a href="<?= base_url('main/index/evaluasi_penawaran_teknis/?reqId='.$reqId) ?>" class="<?= CLASS_BTN_SUCCESS ?> btn-sm"> <span class="fa fa-pencil"></span> Ubah Hasih Eval. Teknis</a>
                    <?php 
                    } else { ?>
                      <a href="<?= base_url('main/index/evaluasi_penawaran_teknis_sampul1/?reqId='.$reqId) ?>" class="<?= CLASS_BTN_SUCCESS ?> btn-sm"> <span class="fa fa-pencil"></span> Ubah Hasih Eval. Teknis</a>
                    <?php 
                    } ?>
                  </td>
                </tr>
                <tr><td colspan="3"></td></tr>
              <?php 
              } ?>

              <?php 
              $no = 1;
              $paket_evaluasi_harga->selectByParams(array("PAKET_ID" => $reqId));
              if ($paket_evaluasi_harga->countRow() > 0) {
                echo '<tr class="headerTR"><td colspan="2">Dokumen Harga</td><td>Sesuai?</td></tr>';
              }
              while($paket_evaluasi_harga->nextRow())
              { ?>
                <tr class="terang">
                  <td class="text-center" width="10px" rowspan="2"><?=$no?></td>
                  <td>
                  <?php 
                  $paket_dokumen = new PaketDokumen();
                  $paket_dokumen->selectByParams(array("PAKET_ID" => $reqId, "REKANAN_USER_ID" => $reqRekananId, "TRIM(NAMA)" => trim($paket_evaluasi_harga->getField("NAMA"))));
                  $paket_dokumen->firstRow(); 
                  $checkedHarga = '';
                  if ($paket_dokumen->getField("VERIFIKASI") == '1') {
                    $checkedHarga = 'checked';
                  }
                  ?>
                    <?php
                    if($paket_dokumen->getField("PATH_FILE") == "")
                    {
                      ?>
                      <?=$paket_evaluasi_harga->getField("NAMA")?> <?php if($paket_evaluasi_harga->getField("WAJIB") == '1'){ ?><font color="#FF0000">* </font><?php } ?>
                    <?php 
                    }
                    else
                    {
                    ?>
                      <a href="uploads/penawaran/<?=$paket_dokumen->getField("PATH_FILE")?>" target="_blank"><?= ICON_DOWNLOAD ?> &nbsp;
                        <?=$paket_evaluasi_harga->getField("NAMA")?> <?php if($paket_evaluasi_harga->getField("WAJIB") == '1'){ ?><font color="#FF0000">* </font><?php } ?>
                      </a>
                    <?php
                    }
                    ?>
                  </td>
                  <td class="text-left">
                    <?php 
                    if ($paket_dokumen->getField("PAKET_DOKUMEN_ID")) 
                    { ?> 
                      <input type="radio" value="1" id="check<?=$paket_dokumen->getField("PAKET_DOKUMEN_ID")?>" onclick="return test('<?= trim($paket_evaluasi_harga->getField("NAMA")) ?>','<?=$paket_dokumen->getField("PAKET_DOKUMEN_ID")?>')" <?php if($paket_dokumen->getField("VERIFIKASI") == "1") { ?> checked <?php } ?> name="admin<?=$paket_dokumen->getField("PAKET_DOKUMEN_ID")?>"> Ya <br>
                      <input type="radio" value="0" id="check<?=$paket_dokumen->getField("PAKET_DOKUMEN_ID")?>" onclick="return test('<?= trim($paket_evaluasi_harga->getField("NAMA")) ?>','<?=$paket_dokumen->getField("PAKET_DOKUMEN_ID")?>')" <?php if($paket_dokumen->getField("VERIFIKASI") == "0") { ?> checked <?php } ?> name="admin<?=$paket_dokumen->getField("PAKET_DOKUMEN_ID")?>"> Tidak
                    <?php 
                    } else {
                      echo '<span class="badge badge-danger">Tidak upload</span>';
                    } ?>
                  </td>
                </tr>
                <tr>
                  <td colspan="2">
                    <?php 
                    if ($paket_dokumen->getField("PAKET_DOKUMEN_ID")) 
                    { ?> 
                      <input type="" class="form-control easyui-validatebox span2" style="height: 30px !important;" name="" placeholder="catatan" id="catatan<?=$paket_dokumen->getField("PAKET_DOKUMEN_ID")?>" value="<?=$paket_dokumen->getField("CATATAN")?>" onChange="return test('<?= trim($paket_evaluasi_harga->getField("NAMA")) ?>','<?=$paket_dokumen->getField("PAKET_DOKUMEN_ID")?>')">
                      <small><sup>*</sup>&nbsp;Tekan enter setalah mengisi catatan</small>
                    <?php 
                    } else {
                      echo '';
                    } ?>
                    
                  </td>
                </tr>
              <?php 
                    unset($paket_dokumen);
                $no++;
              } ?>

              <tr>
                <td colspan="3">
                  <?php 
                  if ($reqSistemSampul == '1') { ?>
                    <a href="<?= base_url('main/index/evaluasi_penawaran_harga/?reqId='.$reqId) ?>" class="<?= CLASS_BTN_SUCCESS ?> btn-sm"> <span class="fa fa-pencil"></span> Ubah Hasih Eval. Harga</a>
                  <?php 
                  } else { ?>
                    <a href="<?= base_url('main/index/evaluasi_penawaran_harga_sampul2/?reqId='.$reqId) ?>" class="<?= CLASS_BTN_SUCCESS ?> btn-sm"> <span class="fa fa-pencil"></span> Ubah Hasih Eval. Harga</a>
                  <?php 
                  } ?>
                </td>
              </tr>

            </table>

            <div class="row">
              <div class="col-md-12">
                <div class="alert" style="border: 1px solid #b7b7b7;"> 
                  <?php 
                  $paket_rekanan2 = new PaketRekanan();
                  $paket_rekanan2->selectByParams(array("PAKET_ID" => $reqId, "A.REKANAN_ID" => $reqRekananId), -1, -1, " AND A.TANGGAL_DAFTAR IS NOT NULL ");
                  $paket_rekanan2->firstRow();
                  $arrPasswordDokumen = $paket_rekanan2->getField("KIRIM_PENAWARAN_PASSWORD");
                  $arrPasswordDokumen2 = $paket_rekanan2->getField("KIRIM_PENAWARAN_PASSWORD2");
                  if($reqSistemSampul == "1") {                
                   echo  '<a onClick="return myFunction(\''.$reqRekananId.'\')" style="margin-bottom:10px">
                            <div class="input-group text-center" style="margin-top:1%">
                              <div class="input-group-prepend">
                                <i class="fa fa-copy"></i> &nbsp;&nbsp; Copy Password
                              </div>
                              <input type="text" value="'.$arrPasswordDokumen.'" id="myPass'.$reqRekananId.'" style="border:none; height:10px; width:5px !important; cursor:copy;" readonly>
                            </div>
                          </a>';
                  } else {
                    echo  '<a onClick="return myFunction(\''.$reqRekananId.'\')" style="margin-bottom:10px">
                            <div class="input-group text-center" style="margin-top:1%">
                              <div class="input-group-prepend">
                                <i class="fa fa-copy"></i> &nbsp;&nbsp; Copy Password File I
                              </div>
                              <input type="text" value="'.$arrPasswordDokumen.'" id="myPass'.$reqRekananId.'" style="border:none; height:10px; width:5px !important; cursor:copy;" readonly>
                            </div>
                          </a>';
                    echo  '<a onClick="return myFunction(\''.$reqRekananId.'2\')" style="margin-bottom:10px">
                            <div class="input-group text-center" style="margin-top:1%">
                              <div class="input-group-prepend">
                                <i class="fa fa-copy"></i> &nbsp;&nbsp; Copy Password File II
                              </div>
                              <input type="text" value="'.$arrPasswordDokumen2.'" id="myPass'.$reqRekananId.'2" style="border:none; height:10px; width:5px !important; cursor:copy;" readonly>
                            </div>
                          </a>';
                  }
                  ?> 
                </div>
              </div>
             <!--  <div class="col-md-12">
                <label>Catatan</label>
                <textarea id="" name="reqCatatan" class="textarea-tinymce" style="width:100%; height:100px"><?=isset($reqUraianKegiatan)?$reqUraianKegiatan:''?></textarea>
              </div> -->
            </div>

           <!--  <div class="form-actions">
              <input type="hidden" name="reqId" value="<?=$reqId?>" />
              <input type="hidden" name="reqPaketDokumenId" value="<?=$reqParent?>" />
              <input type="hidden" name="reqRekananUserId" value="<?=$reqRekananUserId?>" />
              <button type="submit" class="<?= CLASS_BTN_PRIMARY ?> mr-1 pull-right mb-1"><?= BTN_SIMPAN ?></button>
            </div> -->
          </form>
        </div>
      </div>
    </div>
  </div>
</div>

<script type="text/javascript">
  function test(file,id) {
    // var n = $("#check"+id+":checked").length;
    var n = $('input[name="admin'+id+'"]:checked').val();
    var c = $("#catatan"+id).val();
    // alert(n);
    $.getJSON("klarifikasi_chat_json/updateChecklistPenawaran/?id="+id+"&status="+n+"&catatan="+c,
      function(data){
        if (data.RESPONSE === 'Gagal') {
          alertError3(data.PESAN); 
        } else {
          alertSuccess2(data.PESAN); 
        }
    });
  }

  function myFunction(a) {
    var id = "myPass"+a;
    var copyText = document.getElementById(id);
    copyText.select();
    copyText.setSelectionRange(0, 99999)
    document.execCommand("copy");
    // alert("Copied the text: " + copyText.value);
    alertSuccess2("Password disalin "+copyText.value);
  }
</script>

<!-- <div class="span12">
 <div class="card">
  <h3 class="card-heading simple"></h3>
  <div class="card-body">

  </div>
 </div>
</div> -->
