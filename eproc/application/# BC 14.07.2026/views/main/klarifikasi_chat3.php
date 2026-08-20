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

$this->libsession->cekSession($reqId);   

$this->load->library("kauth");  $userLogin = new kauth();
$this->load->library("paketinfo"); $paketInfo = new paketinfo();
$this->load->model("PaketKlarifikasi");
$this->load->model("PaketTahap"); 
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
$file = new FileHandler(); 

$arrNegosiasi                  = NEGOSIASI;

$paketInfo->getPaket($reqId);
$reqNama = $paketInfo->nama;
$reqJenisPengadaan = $paketInfo->jenis;
$reqMetodeEvaluasi = $paketInfo->metode_evaluasi;
$reqBidding = $paketInfo->bidding;
$reqRekananIdPemenang = $paketInfo->rekanan_id_pemenang;

if ($reqBidding == 1) { // e-Reverse Auction
  $paket_rekanan->selectUrutPenawaran(array("A.PAKET_ID" => $reqId, "A.LULUS_PENDAFTARAN" => 1, "A.KIRIM_PENAWARAN" => 1, "A.LULUS_PENAWARAN" => 1), -1, -1, "", "", " ORDER BY NILAI_PENAWARAN ASC, KIRIM_PENAWARAN_TANGGAL ASC ");
   $urut=1;
} else { // Negosiasi
  $paket_rekanan->selectByParams(array("PAKET_ID" => $reqId, "A.REKANAN_ID" => $reqRekananIdPemenang), -1, -1, " AND A.TANGGAL_DAFTAR IS NOT NULL AND A.LULUS_PENDAFTARAN = 1 AND A.LULUS_KUALIFIKASI = 1 ");
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
  $('#ffUpload').form({
    url:'dokumen_pengadaan_upload_rekanan/upload_evaluasi',
    onSubmit:function(){
      if($(this).form('validate'))
      {
      var win = $.messager.progress({
                    title:'Proses Upload',
                    msg:'Mengupload dokumen...'
                  });
      }
      else
        $('input:file').MultiFile('reset');
      return $(this).form('validate');
    },
    success:function(data){
      alert(data);
      alertSuccess2(data); 
      $.messager.progress('close');
      // setTimeout(function() {
      //   document.location.reload();
      // }, 2000);
    }
  }); 
});
 

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
        <h4 class="card-title text-white">Klarifikasi Teknis</h4>
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
                  <li class="active" role="presentation" style="width: 33% !important"><a href="main/index/paket_lelang_tambah_klarifikasi/?reqId=<?=$reqId?>"><i class="fa fa-check-circle" aria-hidden="true"></i>
                    <p>Klarifikasi</p>
                    </a>
                  </li>
                  <li role="presentation" style="width: 33% !important"><a href="main/index/paket_lelang_tambah_negosiasi_undangan/?reqId=<?=$reqId?>"><i class="fa fa-cogs" aria-hidden="true"></i>
                    <p>Setup Negosiasi</p>
                    </a>
                  </li>
                  <li role="presentation" style="width: 33% !important"><a href="main/index/paket_lelang_tambah_negosiasi/?reqId=<?=$reqId?>"><i class="fa fa-flag" aria-hidden="true"></i>
                    <p>Negosiasi</p>
                    </a>
                  </li> 
                </ul>
              </div> 
            </div> 
          </div>
          <?php 
          if ($cekAktif == '0') {
             echo '<div class="alert alert-danger" style="color:#fff">
                      <span style="color: #fff">
                        klarifikasi belum dimulai.
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
                  <td>Berita Acara Klarifikasi</td>
                  <td>
                    <form id="ffUpload" method="post" novalidate enctype="multipart/form-data">
                    <input name="reqLinkFile" type="file" multiple class="maxsize-20240" accept="xls|xlsx|pdf" id="reqLinkFile" value=""/>
                    <?= UPLOAD_XLS_XLSX_PDF_2MB ?>
                    <script>
                    // wait for document to load
                    $(function(){

                        // invoke plugin
                        $('#reqLinkFile').MultiFile({
                            onFileChange: function(){
                                $("#reqSubmit").click();
                            }
                        });

                    });
                    </script>
                    <input type="hidden" name="reqId" value="<?=$reqId?>" />
                    <input type="hidden" name="reqNamaDokumen" id="reqNamaDokumen" value="Berita Acara klarifikasi" />
                    <input type="hidden" name="reqJenisDokumen" id="reqJenisDokumen" value="BERITA_ACARA_KLARIFIKASI_TEKNIS" />
                    <input type="submit" name="reqSubmit" id="reqSubmit" value="" style="display:none">
                    </form>
                    <!-- <a href="main/index/klarifikasi_chat_ba/?reqId=<?=$reqId?>" target="_blank" <?=$style?> id="btnCetak"  class="btn btn-success text-white"><i class="fa fa-upload"></i> Upload BA klarifikasi</a> -->
                  </td>
                </tr>
                <tr>
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
                  <td>Download Berita Acara Klarifikasi</td>
                  <td>
                  <a href="uploads/penawaran/<?=$dokumen?>" target="_blank"><img src="images/icon-download.png"></a>
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
                    <th style="width: 12%; text-align: center">Chat Klarifikasi</th>
                  </tr>
                  <?php 
                    $i=1;
                    while($paket_rekanan->nextRow())
                    { 
                  ?>
                  <tr >
                      <td><i class="fa fa-user"></i> <?= $paket_rekanan->getField("FULL_NAMA_REKANAN")?></td>
                      <td>
                        <a href="main/index/klarifikasi_chat_tanggapan/?reqId=<?=$reqId?>&reqRekananId=<?=$paket_rekanan->getField("REKANAN_ID")?>" class="btn-aksi">
                            <i class="fa fa-commenting" aria-hidden="true" style="color: #000"></i>
                        </a>  
                      </td>
                  </tr>
                   <?php
                      $i++;
                    } 
                ?>
                </tbody>
              </table>
            </div>

            <div class="form-actions mt-3">
              <input type="hidden" name="reqId" value="<?=$reqId?>" />
              <a href="main/index/paket_detil/?reqId=<?=$reqId?>" class="btn btn-danger text-white"> <?= BTN_KEMBALI ?> </a> 
              <a href="main/index/klarifikasi_chat/?reqId=<?=$reqId?>" class="btn btn-info"><?= BTN_REFRESH ?></a>
              <?php 
              if ($aktif_klarifikasi2 == '1') { ?>
              <a href="main/loadUrl/report/klarifikasi_cetak_pdf/?reqId=<?=$reqId?>" target="_blank" <?=$style?> id="btnCetak"  class="btn btn-primary text-white"><?= BTN_PRINT_BA ?> Hasil klarifikasi</a>
              <?php 
              } ?>
              <!-- <button type="submit" class="btn btn-primary"><i class="fa fa-check-square-o"></i> Simpan</button> -->
            </div> 

        </div>
      </div>
    </div>
  </div> 
</div>   
