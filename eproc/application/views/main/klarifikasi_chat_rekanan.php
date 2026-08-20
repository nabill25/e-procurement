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
$this->load->model("PaketKlarifikasi");
$this->load->model("PaketTahap");
$this->load->model("PaketRekanan");
$this->load->library("FileHandler");
include_once("functions/string.func.php");
include_once("functions/date.func.php");
include_once("functions/default.func.php");

$paket_klarifikasi = new PaketKlarifikasi();
$paket_tahap = new PaketTahap();
$paket_tahap_metode = new PaketTahap();
$file = new FileHandler();
$paket_rekanan = new PaketRekanan();

$arrNegosiasi = NEGOSIASI;

$reqMode = httpFilterRequest("reqMode");
$reqId = httpFilterRequest("reqId");
$reqCetak= httpFilterPost('reqCetak');
$reqNamaDokumen= httpFilterPost('reqNamaDokumen');
$reqKeterangan= httpFilterPost('reqKeterangan');
$reqLinkFile= isset($_FILES['reqLinkFile']) ? $_FILES['reqLinkFile'] : '';
$reqBayar= httpFilterPost('reqBayar');
$reqDokumenId = httpFilterGet('reqDokumenId');
$submitSimpan= httpFilterPost('submitSimpan');

$paket_rekanan->selectByParamsPaketLelangV2(array("A.PAKET_ID" => $reqId, "A.REKANAN_ID" => $this->REKANAN_ID), -1, -1, " AND TANGGAL_DAFTAR IS NOT NULL ");
$paket_rekanan->firstRow();
$reqLulusPendaftaran = $paket_rekanan->getField("LULUS_PENDAFTARAN");
if($reqLulusPendaftaran == 0) // bukan penyedia yang diundang dan lulus
  redirect(base_url()."main");

$paketInfo->getPaket($reqId);
$reqNama = $paketInfo->nama;
$reqJenisPengadaan = $paketInfo->jenis;
$reqRekananIdPemenang = $paketInfo->rekanan_id_pemenang;
$reqMetodeEvaluasi = $paketInfo->metode_evaluasi;
$reqBidding = $paketInfo->bidding;
$reqUUID = $paketInfo->uuid;

if ($reqBidding == 1) { // e-Reverse Auction
  $paket_rekanan->selectUrutPenawaran(array("A.PAKET_ID" => $reqId, "A.REKANAN_ID" => $this->ID, "A.LULUS_PENDAFTARAN" => 1, "A.KIRIM_PENAWARAN" => 1, "A.LULUS_PENAWARAN" => 1), -1, -1, "", "", " ORDER BY NILAI_PENAWARAN ASC, KIRIM_PENAWARAN_TANGGAL ASC ");
  if ($paket_rekanan->countRow() == 0) // hanya untuk penyedia yang terundang
    redirect(base_url('main'));

} else { // Negosiasi
  if($reqRekananIdPemenang != $this->ID) // hanya untuk pemenang dan terundang untuk negosiasi
    redirect(base_url('main'));
}


$jenis_tahap = $paket_tahap_metode->getJenisTahapById($reqId);
$aktif_klarifikasi = $paket_tahap->getCountByParamsAktif(array("URUT" => $arrNegosiasi[$jenis_tahap], "PAKET_ID" => $reqId, "TAMPILKAN" => 1));
$aktif_klarifikasi2 = $paket_tahap->getCountByParamsBerlalu(array("URUT" => $arrNegosiasi[$jenis_tahap], "PAKET_ID" => $reqId, "TAMPILKAN" => 1));
if($aktif_klarifikasi > 0 && $aktif_klarifikasi2 < 1 )
{
  $cekAktif = 1;
} else {
  $cekAktif = 0;
}

$paket_klarifikasi->selectByParams(array("PAKET_ID" => $reqId, "REKANAN_USER_ID" => $this->ID));
?>
<script type="text/javascript">
$(document).ready(function() {

  $(function(){
    $('#ff').form({
      url:'klarifikasi_chat_json/dokumen_klarifikasi_rekanan',
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
        // console.log(data);return false;
        document.location.href = 'main/index/klarifikasi_chat_rekanan/?reqId=<?=$reqId?>';
        hideLoad();
      }
    });

  });

});
</script>

<style type="text/css">
.table th {
    padding: 10px !important;
    background-color: #967adc;
    color: #000;
}
</style>

<div class="row">
  <div class="col-md-12 col-sm-12">
    <div class="card">
      <div class="card-header card-head-inverse bg-primary">
        <h4 class="card-title text-white">Pembuktian</h4>
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
          <div class="row">
            <div class="form-group col-md-12 mb-2">
              <div class="col-md-12">
                <ul class="nav nav-tabs process-model more-icon-preocess" role="tablist">
                  <li class="active" role="presentation" style="width: 50% !important"><a href="main/index/klarifikasi_chat_rekanan/?reqId=<?=$reqId?>#area-chat"><i class="fa fa-check-circle" aria-hidden="true"></i>
                    <p>Pembuktian</p>
                    </a>
                  </li>
                  <?php
                  if ($reqBidding == 1) { // e-Reverse Auction ?>
                  <li role="presentation" style="width: 50% !important"><a href="main/index/auction_rekanan/?reqId=<?=$reqId?>"><i class="fa fa-flag" aria-hidden="true"></i>
                    <p>e-Reverse Auction</p>
                    </a>
                  </li>
                <?php } else { ?>
                  <li role="presentation" style="width: 50% !important"><a href="main/index/negosiasi_rekanan/?reqId=<?=$reqId?>"><i class="fa fa-flag" aria-hidden="true"></i>
                    <p>Negosiasi</p>
                    </a>
                  </li>
                <?php } ?>
                </ul>
              </div>
            </div>
          </div>
          <?php
          if ($cekAktif == '0') {
             echo '<div class="alert alert-danger" style="color:#fff">
                      <span style="color: #fff">
                        Pembuktian belum dimulai atau sudah selesai
                      </span>
                    </div>';
           } ?>

            <!-- <div class="alert alert-success mb-2" style="color:#fff">
              <span style="color: #fff">
                 Setelah proses Klarifikasi selesai, semua peserta/rekanan yang sudah mendaftar paket ini dinyatakan sudah mengerti dan paham mengenai syarat dan ketentuan.
              </span>
            </div> -->

            <div class="table-responsive" id="area-chat">
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
                      <td width="2%">
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
            </div>

          <?php
          if ($cekAktif == '1') { ?>
            <div class="card mb-1 border-blue border-darken-1" style="padding:10px">
              <form id="ff" class="easyui-form form-horizontal" method="post" novalidate enctype="multipart/form-data">
                <div class="alert bg-primary alert-icon-left alert-arrow-left alert-dismissible mb-2" role="alert">
                  <span class="alert-icon"><i class="fa fa-th"></i></span>  <strong>Pembuktian</strong>
                </div>
                <div class="row">
                  <div class="form-group col-md-12 mb-2">
                    <label>Ketik pesan disini</label>
                    <textarea name="reqKeterangan" id="reqKeterangan" cols="45" rows="5" class="easyui-validatebox form-control" required></textarea>
                  </div>
                </div>

                <input type="hidden" name="reqId" value="<?=$reqId?>" />
                <button type="submit" class="<?= CLASS_BTN_PRIMARY ?> mr-1"><?= BTN_KIRIM ?></button>
                <a href="main/index/klarifikasi_chat_rekanan/?reqId=<?=$reqId?>" class="<?= CLASS_BTN_INFO ?>"><i class="fa fa-refresh"></i> Refresh</a>
              </form>
            </div>
          <?php
          } ?>

            <div class="form-actions">
              <a href="main/index/paket_detil/?eid=<?=$reqId?>&key=<?=$reqUUID?>" class="<?= CLASS_BTN_DANGER ?> mr-1"> <?= BTN_KEMBALI ?></a>
              <?php
              $this->load->model("PaketDokumen");
              $paket_dokumen = new PaketDokumen();
              $paket_dokumen->selectByParams(array("PAKET_ID" => $reqId, "JENIS_DOKUMEN" => "BERITA_ACARA_KLARIFIKASI_TEKNIS"));
              $paket_dokumen->firstRow();
              $dokumen = $paket_dokumen->getField("PATH_FILE");
              if($dokumen == "")
              {}
              else
              {
              ?>
                <a href="uploads/penawaran/<?=$dokumen?>" target="_blank" class="<?= CLASS_BTN_SUCCESS ?> mr-1"><i class="fa fa-download"></i> Download Berita Acara Pembuktian</a>
              <?php
              }
              ?>
              <?php  ?>
              <?php
              if ($cekAktif == '0') { ?>
              <a href="main/loadUrl/report/klarifikasi_cetak_pdf/?reqId=<?=$reqId?>" target="_blank" class="<?= CLASS_BTN_PRIMARY ?>"><?= BTN_PRINT ?> Hasil Pembuktian</a>
              <?php
              } ?>
            </div>

        </div>
      </div>
    </div>
  </div>
</div>
