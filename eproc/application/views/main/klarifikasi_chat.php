<?php
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 *
 */
// ---- Maret 26 Label Klarifikasi ganti jadi Pembuktian
$reqMode = httpFilterRequest("reqMode");
$reqId = httpFilterRequest("reqId");
$reqCetak= httpFilterPost('reqCetak');
$reqNamaDokumen= httpFilterPost('reqNamaDokumen');
$reqKeterangan= httpFilterPost('reqKeterangan');
$reqLinkFile= isset($_FILES['reqLinkFile']) ? $_FILES['reqLinkFile'] : '';
$reqBayar= httpFilterPost('reqBayar');
$reqDokumenId = httpFilterGet('reqDokumenId');
$submitSimpan= httpFilterPost('submitSimpan');

$this->libsession->cekSession($reqId);

$this->load->library("kauth");  $userLogin = new kauth();
$this->load->library("paketinfo"); $paketInfo = new paketinfo();
$this->load->model("PaketKlarifikasi");
$this->load->model("PaketTahap");
$this->load->model("PaketDokumen");
$this->load->model("PaketRekanan");
$this->load->library("FileHandler");
include_once("functions/string.func.php");
include_once("functions/date.func.php");
include_once("functions/default.func.php");

$paket_klarifikasi_first = new PaketKlarifikasi();
$paket_klarifikasi = new PaketKlarifikasi();
$paket_tahap = new PaketTahap();
$paket_tahap_metode = new PaketTahap();
$paket_rekanan = new PaketRekanan();
$paket_rekanan2 = new PaketRekanan();
$file = new FileHandler();

$arrNegosiasi                  = NEGOSIASI;

$paketInfo->getPaket($reqId);
$reqNama = $paketInfo->nama;
$reqJenisPengadaan = $paketInfo->jenis;
$reqMetodeEvaluasi = $paketInfo->metode_evaluasi;
$reqBidding = $paketInfo->bidding;
$reqRekananIdPemenang = $paketInfo->rekanan_id_pemenang ?? '0';
$reqMultiPemenang = $paketInfo->multi_pemenang;
$reqSistemSampul = $paketInfo->sistem_sampul;
$reqUUID = $paketInfo->uuid;

if ($reqBidding == 1) { // e-Reverse Auction
  $paket_rekanan->selectUrutPenawaran(array("A.PAKET_ID" => $reqId, "A.LULUS_PENDAFTARAN" => 1, "A.KIRIM_PENAWARAN" => 1, "A.LULUS_PENAWARAN" => 1), -1, -1, "", "", " ORDER BY NILAI_PENAWARAN ASC, KIRIM_PENAWARAN_TANGGAL ASC ");
   $urut=1;
} else { // Negosiasi
  if ($reqMultiPemenang == '1') { // Multi Pemenang
    $paket_rekanan->selectUrutPenawaran(array("A.PAKET_ID" => $reqId, "A.LULUS_PENDAFTARAN" => 1, "A.KIRIM_PENAWARAN" => 1, "A.LULUS_PENAWARAN" => 1), -1, -1, "", "", " ORDER BY NILAI_PENAWARAN ASC, KIRIM_PENAWARAN_TANGGAL ASC ");
  } else {
    // tampilkan rekanan yang sudah pernah di klarifikasi
    $paket_dokumen_klarifikasi = new PaketDokumen();
    $paket_dokumen_klarifikasi->selectByParamsGroupRekId(array("PAKET_ID" => $reqId), -1, -1, " AND VERIFIKASI != ''  AND REKANAN_USER_ID NOT IN (".$reqRekananIdPemenang.")"); // yang sudah pernah di klarifikasi
    while($paket_dokumen_klarifikasi->nextRow())
    {
      $arrRekId[] = $paket_dokumen_klarifikasi->getField("REKANAN_USER_ID");
    }
    if (is_array($arrRekId)) {
      $arrRekIdImp = implode(',',$arrRekId);
    } else {
      $arrRekIdImp = 0;
    }

    $paket_rekanan->selectByParams(array("PAKET_ID" => $reqId), -1, -1, " AND (A.REKANAN_ID = ".$reqRekananIdPemenang." OR A.REKANAN_ID IN (".$arrRekIdImp.") ) AND A.TANGGAL_DAFTAR IS NOT NULL AND A.LULUS_PENDAFTARAN = 1 AND A.LULUS_KUALIFIKASI = 1 "); // yang di undang di rekapitulasi

  }
}

$jenis_tahap = $paket_tahap_metode->getJenisTahapById($reqId);
$paket_klarifikasi_first->selectByParams(array("PAKET_ID" => $reqId, "PARENT_ID" => 0));
$totalAan = $paket_klarifikasi_first->firstRow();
$aktif_klarifikasi = $paket_tahap->getCountByParamsAktif(array("URUT" => $arrNegosiasi[$jenis_tahap], "PAKET_ID" => $reqId, "TAMPILKAN" => 1));
$aktif_klarifikasi2 = $paket_tahap->getCountByParamsBerlalu(array("URUT" => $arrNegosiasi[$jenis_tahap], "PAKET_ID" => $reqId, "TAMPILKAN" => 1));

if($aktif_klarifikasi > 0 || $aktif_klarifikasi2 > 0 )
{
  $cekAktif = 1;
} else {
  $cekAktif = 0;
}
$paket_klarifikasi->selectByParams(array("PAKET_ID" => $reqId, "PARENT_ID" => 0));
?>
<script type="text/javascript">
$(document).ready(function() {

  $(function(){
    $('#ff').form({
      url:'aanwijzing_chat_json/dokumen_sanggah_panitia',
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

$(function(){
  $('#ffUpload').form({
    url:'dokumen_pengadaan_upload_rekanan/upload_evaluasi',
    onSubmit:function(){

      // ambil file
      let fileInput = $('#reqLinkFile')[0];
      let file = fileInput.files[0];

      // validasi file kosong
      if (!file) {
        alert('Silakan pilih file terlebih dahulu');
        return false;
      }

      // validasi ekstensi
      let fileName = file.name.toLowerCase();
      if (!fileName.endsWith('.pdf') && !fileName.endsWith('.zip')) {
        alert('File harus PDF atau ZIP');
        return false;
      }

      // validasi ukuran (optional, 10MB)
      if (file.size > 10 * 1024 * 1024) {
        alert('Ukuran file maksimal 10MB');
        return false;
      }

      // validasi bawaan EasyUI
      if($(this).form('validate'))
      {
        $.messager.progress({
          title:'Proses Upload',
          msg:'Mengupload dokumen...'
        });
      }
      else
      {
        $('input:file').MultiFile('reset');
        return false;
      }

      return true;
    },

    success:function(data){
      if (data === 'Dokumen berhasil diupload.') {
        alertSuccess2(data);
      } else {
        alertError2(data);
      }

      $.messager.progress('close');

      setTimeout(function() {
        document.location.reload();
      }, 2000);
    }
  });
});

// $(function(){
//   $('#ffUpload').form({
//     url:'dokumen_pengadaan_upload_rekanan/upload_evaluasi',
//     onSubmit:function(){
//       if($(this).form('validate'))
//       {
//       var win = $.messager.progress({
//                     title:'Proses Upload',
//                     msg:'Mengupload dokumen...'
//                   });
//       }
//       else
//         $('input:file').MultiFile('reset');
//       return $(this).form('validate');
//     },
//     success:function(data){
//       // alert(data);
//       if (data === 'Dokumen berhasil diupload.') { alertSuccess2(data);
//       } else {
//         alertError2(data);
//       }
//       $.messager.progress('close');
//       setTimeout(function() {
//         document.location.reload();
//       }, 2000);
//     }
//   });
// });

</script>

<style type="text/css">
.table th {
    padding: 10px !important;
    background-color: #b7b7b7;
    color: #000;
}
</style>

<div class="row">
  <div class="col-md-12 col-sm-12">
    <div class="card">
      <div class="card-header card-head-inverse bg-primary">
        <h4 class="card-title text-white">Pembuktian Dokumen Penawaran</h4>
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
          if ($reqBidding == 1) { // e-Reverse Auction ?>
          <div class="row">
            <div class="form-group col-md-12 mb-2">
              <div class="col-md-12">
                <ul class="nav nav-tabs process-model more-icon-preocess" role="tablist">
                  <li role="presentation" style="width: 33% !important"><a href="main/index/paket_lelang_tambah_negosiasi_undangan_reverse/?reqId=<?=$reqId?>"><i class="fa fa-cogs" aria-hidden="true"></i>
                    <p>Setting Notifikasi Pembuktian</p>
                    </a>
                  </li>
                  <li class="active" role="presentation" style="width: 33% !important"><a href="main/index/klarifikasi_chat/?reqId=<?=$reqId?>"><i class="fa fa-check-circle" aria-hidden="true"></i>
                    <p>Pembuktian Dok. Penawaran</p>
                    </a>
                  </li>
                  <li role="presentation" style="width: 33% !important"><a href="main/index/paket_lelang_tambah_auction/?reqId=<?=$reqId?>"><i class="fa fa-flag" aria-hidden="true"></i>
                    <p>e-Reverse Auction</p>
                    </a>
                  </li>
                </ul>
              </div>
            </div>
          </div>
          <?php
          } else { ?>
          <div class="row">
            <div class="form-group col-md-12 mb-2">
              <div class="col-md-12">
                <ul class="nav nav-tabs process-model more-icon-preocess" role="tablist">
                  <li role="presentation" style="width: 33% !important"><a href="main/index/paket_lelang_tambah_negosiasi_undangan/?reqId=<?=$reqId?>"><i class="fa fa-cogs" aria-hidden="true"></i>
                    <p>Setting Notifikasi Pembuktian</p>
                    </a>
                  </li>
                  <li class="active" role="presentation" style="width: 33% !important"><a href="main/index/klarifikasi_chat/?reqId=<?=$reqId?>"><i class="fa fa-check-circle" aria-hidden="true"></i>
                    <p>Pembuktian Dok. Penawaran</p>
                    </a>
                  </li>
                  <?php
                  if ($reqMultiPemenang == '1') { // Multi
                  ?>
                    <li role="presentation" style="width: 33% !important">
                      <a onclick="return $.messager.alert('Info', 'Tender yang dilaksanakan untuk memperoleh lebih dari 1 (satu) Pemenang <br> maka Tim Pengadaan melakukan Negosiasi bersamaan dengan proses Pembuktian  untuk mendapatkan 1 (satu) harga dan teknis yang sama', 'info');"><i class="fa fa-flag" aria-hidden="true"></i><p>Negosiasi</p></a>
                    </li>
                  <?php
                   } else
                   { ?>
                    <li role="presentation" style="width: 33% !important">
                      <a href="main/index/paket_lelang_tambah_negosiasi/?reqId=<?=$reqId?>"><i class="fa fa-flag" aria-hidden="true"></i><p>Negosiasi</p>
                      </a>
                    </li>
                  <?php
                    }
                  ?>

                </ul>
              </div>
            </div>
          </div>
          <?php
          }

          if ($cekAktif == '0') {
             echo '<div class="alert alert-info" style="color:#fff">
                      <span style="color: #fff">
                        Pembuktian belum dimulai.
                      </span>
                    </div>';
           } ?>
            <div class="table-responsive mb-1">
              <table class="table table-bordered mb-0">
                <tr>
                  <td width="20%">Pengadaan</td>
                  <td><?=$reqNama?></td>
                </tr>
                <tr>
                  <td width="20%">Jenis Pengadaan</td>
                  <td><?=$reqJenisPengadaan?></td>
                </tr>
                <tr>
                  <td>Metode Evaluasi</td>
                  <td><?=$reqMetodeEvaluasi?> </td>
                </tr>
                <tr>
                  <td>Upload Berita Acara</td>
                  <td>
                    <form id="ffUpload" method="post" novalidate enctype="multipart/form-data">
                    <input name="reqLinkFile" type="file" multiple class="maxsize-20240" accept="pdf|zip" id="reqLinkFile" value=""/>
                    <br><?= UPLOAD_PDF_ZIP_10MB ?>
                    <script>
                    // wait for document to load
                    $( "#reqLinkFile" ).bind( "change", function() {
                  	document.querySelector('#reqSubmit').click();
                    });
                    </script>
                    <input type="hidden" name="reqId" value="<?=$reqId?>" />
                    <input type="hidden" name="reqNamaDokumen" id="reqNamaDokumen" value="Berita Acara Pembuktian Teknis" />
                    <input type="hidden" name="reqJenisDokumen" id="reqJenisDokumen" value="BERITA_ACARA_KLARIFIKASI_TEKNIS" />
                    <input type="submit" name="reqSubmit" id="reqSubmit" value="" style="display:none">
                    </form>
                  </td>
                </tr>
                <tr>
                <?php
                  $paket_dokumen = new PaketDokumen();
                  $paket_dokumen->selectByParams(array("PAKET_ID" => $reqId, "JENIS_DOKUMEN" => "BERITA_ACARA_KLARIFIKASI_TEKNIS"));
                  $paket_dokumen->firstRow();
                  $dokumen = $paket_dokumen->getField("PATH_FILE");
                  if($dokumen == "")
                  {}
                  else
                  {
                  ?>
                  <td>Download Berita Acara Pembuktian</td>
                  <td>
                  <a href="uploads/penawaran/<?=$dokumen?>" target="_blank" class="btn-sm btn-success round">
                    <?= ICON_DOWNLOAD ?> Download
                  </a>
                  </td>
                  <?php
                  }
                  ?>
                </tr>
              </table>
            </div>
            <div class="table-responsive">
              <table class="table table-bordered mb-0" id="tbl_bidang">
                <tbody>
                  <tr class="judul-kolom">
                    <th>Calon Pemenang</th>
                    <th style="width: 12%; text-align: center">Chat Pembuktian</th>
                    <?php
                    if ($reqBidding == 1) { // e-Reverse Auction ?>
                      <th style="width: 15%; text-align: center">Undang Reverse Auction</th>
                    <?php
                    } else {
                      if ($reqMultiPemenang == '1') { // Multi
                      } else { ?>
                      <th style="width: 15%; text-align: center">Undang Negosiasi</th>
                    <?php
                      }
                    } ?>
                  </tr>
                  <?php
                  if ($paket_rekanan->countRow() == 0) {
                    echo "<tr ><td colspan='2'>. : : Tidak ada Data : : .</td></tr >";
                  } else
                  {
                    $i=1;
                    while($paket_rekanan->nextRow())
                    {
                  ?>
                  <tr >
                      <td><i class="fa fa-user"></i> <?= $paket_rekanan->getField("FULL_NAMA_REKANAN")?></td>
                      <td align="center">
                        <a href="main/index/klarifikasi_chat_tanggapan/?reqId=<?=$reqId?>&reqRekananId=<?=$paket_rekanan->getField("REKANAN_ID")?>" class="btn-aksi">
                            <i class="fa fa-commenting" aria-hidden="true" style="color: #000"></i>
                        </a>
                      </td>
                      <?php
                      if ($reqBidding == 1) { // e-Reverse Auction ?>
                        <td>
                          <a onclick="return sendUndangan('Kirim email Undangan e-Reverse Auction \n ke <?= $paket_rekanan->getField("FULL_NAMA_REKANAN")?>?',<?= $reqId ?>,<?= $paket_rekanan->getField("PAKET_REKANAN_ID") ?>,'1')">
                              <i class="fa fa-send-o" aria-hidden="true" style="color: #000"> kirim undangan <br><br> <span class="badge badge-primary"> <b>Terkirim <?= (int)$paket_rekanan->getField("DI_EMAIL_NEGOSIASI_2") ?> kali</b></span></i>
                          </a>
                        </td>
                      <?php
                      } else {
                        if ($reqMultiPemenang == '1') { // Multi
                        } else { ?>
                        <td>
                          <a onclick="return sendUndangan('Kirim email Undangan Negosiasi \n ke <?= $paket_rekanan->getField("FULL_NAMA_REKANAN")?>?',<?= $reqId ?>,<?= $paket_rekanan->getField("PAKET_REKANAN_ID") ?>,'0')">
                              <i class="fa fa-send-o" aria-hidden="true" style="color: #000"> kirim undangan <br><br> <span class="badge badge-primary"> <b>Terkirim <?= (int)$paket_rekanan->getField("DI_EMAIL_NEGOSIASI_2") ?> kali</b></span></i>
                          </a>
                        </td>
                      <?php
                        }
                      } ?>
                  </tr>
                   <?php
                      $i++;
                    }
                  }
                ?>
                </tbody>
              </table>
               <?php
                // if ($reqBidding == 1) { // e-Reverse Auction
                //   if ($paket_rekanan->countRow() == '2') { true; } else { echo '<div class="alert alert-danger mt-1">Jumlah Calon Pemanang '.$paket_rekanan->countRow().' penyedia, silahkan ubah Sistem Negosiasi menjadi Chatting Nego <a href="'.base_url('main/index/paket_lelang_tambah/?reqId='.$reqId).'" class="'.CLASS_BTN_PRIMARY.' btn-sm"><span class="fa fa-pencil"> Ubah Sistem Negosiasi</span></a></div>'; }
                // } else { // Negosiasi
                //   if ($paket_rekanan->countRow() == '2') { echo '<div class="alert alert-danger">Jumlah Calon Pemanang '.$paket_rekanan->countRow().' penyedia, silahkan ubah Sistem Negosiasi menjadi Chatting Nego</div>'; } else { }
                // }
               ?>

            </div>

            <div class="form-actions mt-3">
              <input type="hidden" name="reqId" value="<?=$reqId?>" />
              <a href="main/index/paket_detil/?eid=<?=$reqId?>&key=<?=$reqUUID?>" class="<?= CLASS_BTN_DANGER ?>"> <?= BTN_KEMBALI ?> </a>
              <a href="main/index/paket_lelang_tambah_penentuan_pemenang/?reqId=<?=$reqId?>" class="<?= CLASS_BTN_WARNING ?> pull-right ml-1"> <span class="fa fa-gavel"></span> Lanjut Penetapan Pemenang ?  </a>
              <?php
              if ($reqBidding == 1) {
              } else { // Negosiasi
                if ($reqMultiPemenang == '1') {
                } else {
                  if ($reqSistemSampul == '1') { // 1 file
                  ?>
                    <a href="main/index/evaluasi_penawaran_rekapitulasi/?reqId=<?=$reqId?>" class="<?= CLASS_BTN_INFO ?> pull-right ml-1"> <span class="fa fa-plus"></span> Tambah Pembuktian ?  </a>
                  <?php
                  } else { ?>
                    <a href="main/index/evaluasi_penawaran_rekapitulasi_sampul2/?reqId=<?=$reqId?>" class="<?= CLASS_BTN_INFO ?> pull-right ml-1"> <span class="fa fa-plus"></span> Tambah Pembuktian ?  </a>
              <?php
                  }
                }
              } ?>
              <a onclick="openAddFrame('main/loadUrl/main/paket_lelang_tambah_popup/?reqId=<?= $reqId ?>');" class="<?= CLASS_BTN_PRIMARY ?> pull-right mr-1"><span class="fa fa-pencil"> Ubah Sistem Negosiasi</span></a>
            </div>

        </div>
      </div>
    </div>
  </div>
</div>

<script type="text/javascript">
function sendUndangan(notif,reqid,paketrekananid,reqJenis)
{
  if(confirm(notif) ){
    $(function () {
      $.get("paket_negoisasi_json/undanganNegosiasiChat?reqId="+reqid+"&reqPaketRekananId="+paketrekananid+"&reqJenis="+reqJenis, function (data) {
        alertSuccess2(data);
        setTimeout(function() {
          location.reload();
        }, 2000);
      });
    });
  }
}
</script>
