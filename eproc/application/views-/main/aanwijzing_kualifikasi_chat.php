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
$this->load->model("PaketAanwijzing");
$this->load->model("PaketTahap");
$this->load->model("Aanwijzing");
$this->load->model("PaketAanwijzingValidasi");
$this->load->library("FileHandler");
include_once("functions/string.func.php");
include_once("functions/date.func.php");
include_once("functions/default.func.php");

$paket_aanwijzing_first = new PaketAanwijzing();
$paket_aanwijzing = new PaketAanwijzing();
$paket_tahap = new PaketTahap();
$paket_tahap_metode = new PaketTahap();
$file = new FileHandler();
$paket_aanwijzing_validasi = new PaketAanwijzingValidasi();

$arrAanwijzing = AANWIJZING_KUALIFIKASI;

$paketInfo->getPaket($reqId);
$reqNama = $paketInfo->nama;
$reqJenisPengadaan = $paketInfo->jenis;
$reqMetodeEvaluasi = $paketInfo->metode_evaluasi;
$reqUUID = $paketInfo->uuid;
$jenis_tahap = $paket_tahap_metode->getJenisTahapById($reqId);
$paket_aanwijzing_first->selectByParamsKualifikasi(array("PAKET_ID" => $reqId, "PARENT_ID" => 0));
$totalAan = $paket_aanwijzing_first->firstRow();
$aktif_aanwitzing = $paket_tahap->getCountByParamsAktif(array("URUT" => $arrAanwijzing[$jenis_tahap], "PAKET_ID" => $reqId, "TAMPILKAN" => 1));
$aktif_aanwitzing2 = $paket_tahap->getCountByParamsBerlalu(array("URUT" => $arrAanwijzing[$jenis_tahap], "PAKET_ID" => $reqId, "TAMPILKAN" => 1));
 
if($aktif_aanwitzing > 0 || $aktif_aanwitzing2 > 0 )
{
  $cekAktif = 1;
} else {
  $cekAktif = 0;
} 

$paket_aanwijzing_validasi->selectByParamsValidasi(array("NIP" => $this->NIP, "A.PAKET_ID" => $reqId));
$paket_aanwijzing_validasi->firstRow();

if($this->USER_TYPE_ID == 6)
{
  if($paket_aanwijzing_validasi->getField("KODE") == "")
  {
    $paket_aanwijzing_validasi->setField("PAKET_ID", $reqId);
    $paket_aanwijzing_validasi->setField("USER_LOGIN_ID", $this->USER_LOGIN_ID);
    $paket_aanwijzing_validasi->setField("KODE", $this->USER_LOGIN_ID);
    $paket_aanwijzing_validasi->setField("JENIS", "REKANAN");
    $paket_aanwijzing_validasi->insert();
  }
}
 

$paket_aanwijzing->selectByParamsKualifikasi(array("PAKET_ID" => $reqId, "PARENT_ID" => 0));
?>
<script type="text/javascript">

setTimeout(function () { document.location.reload(); }, 300000);

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
      // alert(data);
      alertSuccess2(data); 
      $.messager.progress('close');
      setTimeout(function() {
        document.location.reload();
      }, 2000);
    }
  }); 
});


  <?php
  if($this->USER_TYPE_ID == 3 || $this->USER_TYPE_ID == 7)
  {
  ?>
  function publishAanwijzing()
  {
    $.getJSON('paket_aanwijzing_validasi_json/aanwijzing_publish_validasi_json/?reqId=<?=$reqId?>',
    function(dataJson){
      if(dataJson.PESAN == "1")
      {
        if(confirm("Publish berita acara aanwijzing dan email pemberitahuan ke peserta?"))
        {
          $(".loader").fadeIn("slow");
          var jqxhr = $.get( "aanwijzing_json/set_publish_aanwijzing/?reqId=<?=$reqId?>", function(data) {
            $(".loader").fadeOut("slow");
            alert(data);
            $("#btnPublish").css("display", "none");
          })
            .fail(function() {
            alert( "error" );
            });
        }
      }
      else
        alert(dataJson.PESAN);

    });

  }
  <?php
  }
  ?>

  <?php
  if($this->USER_TYPE_ID == 3 || $this->USER_TYPE_ID == 9 || $this->USER_TYPE_ID == 7)
  {
  ?>
  function submitValidasi(kode, jenis)
  {
    if(confirm("Validasi berita acara aanwijzing ?"))
    {
      $.getJSON('paket_aanwijzing_validasi_json/aanwijzing_validasi_json/?reqId=<?=$reqId?>&reqKode='+kode+'&reqJenis='+jenis,
      function(data){
        alert(data.PESAN);
        $("#tombolValidasi").css("display", "none");
      });
    }
  }
  <?php
  }
  ?> 

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
        <h4 class="card-title text-white">Aanwijzing Kualifikasi</h4>
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
                        Aanwijzing Kualifikasi belum dimulai.
                      </span>
                    </div>';
          } else 
          { ?>
            <div class="table-responsive mb-1">
              <table class="table table-bordered mb-0">
                <tr>
                  <td width="25%">Pengadaan</td>
                  <td><?=$reqNama?></td>
                </tr>
                <tr>
                  <td width="25%">Jenis Pengadaan</td>
                  <td><?=$reqJenisPengadaan?></td>
                </tr>
                <tr>
                  <td>Metode Evaluasi</td>
                  <td><?=$reqMetodeEvaluasi?> </td>
                </tr> 
                <tr>
                  <td>Berita Acara & Addendum</td>
                  <td>
                    <form id="ffUpload" method="post" novalidate enctype="multipart/form-data">
                    <input name="reqLinkFile" type="file" multiple class="maxsize-20240" accept="xls|xlsx|pdf" id="reqLinkFile" value=""/>
                    <br><?= UPLOAD_XLS_XLSX_PDF_2MB ?>
                    <script>
                    // wait for document to load
    		            $( "#reqLinkFile" ).bind( "change", function() {
                      document.querySelector('#reqSubmit').click();
                    });
                    </script>
                    <input type="hidden" name="reqId" value="<?=$reqId?>" />
                    <input type="hidden" name="reqNamaDokumen" id="reqNamaDokumen" value="Berita Acara Aanwijzing Kualifikasi" />
                    <input type="hidden" name="reqJenisDokumen" id="reqJenisDokumen" value="BERITA_ACARA_AANWIJZING_KUALIFIKASI" />
                    <input type="submit" name="reqSubmit" id="reqSubmit" value="" style="display:none">
                    </form>
                    <!-- <a href="main/index/aanwijzing_chat_ba/?reqId=<?=$reqId?>" target="_blank" <?=$style?> id="btnCetak"  class="btn btn-success text-white"><i class="fa fa-upload"></i> Upload BA Aanwijzing</a> -->
                  </td>
                </tr> 
                <tr>
                <?php
                  $this->load->model("PaketDokumen");
                  $paket_dokumen = new PaketDokumen();
                  $paket_dokumen->selectByParams(array("PAKET_ID" => $reqId, "JENIS_DOKUMEN" => "BERITA_ACARA_AANWIJZING_KUALIFIKASI"));
                  $paket_dokumen->firstRow();
                  $dokumen = $paket_dokumen->getField("PATH_FILE");
                  if($dokumen == "")
                  {}
                  else
                  {
                  ?>
                  <td>Download Berita Acara & Addendum</td>
                  <td>
                    <a href="uploads/penawaran/<?=$dokumen?>" target="_blank">
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
                    <th>Peserta</th>
                    <th style="width: 38%">Pertanyaan</th>  
                    <th style="width: 38%">Jawaban</th>  
                    <th style="width: 7%; text-align: center">Aksi</th>
                  </tr>
                  <?php
                  if ($totalAan=='') {
                    echo '<tr><td colspan="6">. : Tidak ada data : .</td></tr>';
                  } else {

                    $i=1;
                    while($paket_aanwijzing->nextRow())
                    {
                      $tglupload = explode('.', $paket_aanwijzing->getField("TANGGAL_UPLOAD"));
                      // Get Parent
                      $paket_aanwijzing_parent = new PaketAanwijzing();
                       $paket_aanwijzing_parent->selectByParamsKualifikasi(array("PAKET_ID" => $reqId, "PARENT_ID" => $paket_aanwijzing->getField("PAKET_AANWIJZING_KUALIFIKASI_ID") ));
                       // $paket_aanwijzing_parent->firstRow();
                  ?>
                  <tr >
                      <td><i class="fa fa-user"></i> <?= $paket_aanwijzing->getField("KODE_CUT")?></td>
                      <td>
                        <?=$paket_aanwijzing->getField("KETERANGAN")?> <br>
                        <small><i class="fa fa-clock-o"></i> <?=$tglupload[0] ?></small>
                      </td> 
                      <td> 
                        <?php
                        while ($paket_aanwijzing_parent->nextRow()) {
                          $tglupload2 = explode('.', $paket_aanwijzing_parent->getField("TANGGAL_UPLOAD"));
                        if ($paket_aanwijzing_parent->getField("KETERANGAN") == '') {
                           echo '<span class="badge badge-danger">Belum</span>';
                         } else {
                           echo $paket_aanwijzing_parent->getField("KETERANGAN").'<br>
                            <small><i class="fa fa-clock-o"></i> '.$tglupload2[0].'</small><br>';
                         }
                          if ($paket_aanwijzing_parent->getField("PATH_FILE")) { ?>
                          <a href="uploads/aanwijzing/<?=$paket_aanwijzing_parent->getField("PATH_FILE")?>" target="_blank" class="badge badge-primary">
                              <i class="fa fa-download" aria-hidden="true"></i> Donwload
                          </a><br><br>
                          <?php 
                          }
                        }
                        ?>
                      </td> 
                      <td>
                        <a href="main/index/aanwijzing_kualifikasi_chat_tanggapan/?reqId=<?=$reqId?>&reqParent=<?=$paket_aanwijzing->getField("PAKET_AANWIJZING_KUALIFIKASI_ID")?>" class="btn-aksi">
                            <i class="fa fa-comments-o fa-2x" aria-hidden="true" style="color: #000"></i>
                        </a> 
                        <!-- <a onClick="deleteData('aanwijzing_chat_json/deletekualifikasi2/', '<?php //$paket_aanwijzing->getField("PAKET_AANWIJZING_KUALIFIKASI_ID")?>')" class="btn-aksi"><i class="fa fa-trash" aria-hidden="true"></i></a> -->
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

            <div class="col-md-12 alert alert-warning mt-1">Catatan: Halaman aanwijzing akan terefresh otomatis setiap 5 menit</div>

            <div class="form-actions mt-3">
              <input type="hidden" name="reqId" value="<?=$reqId?>" />
              <a href="main/index/paket_detil/?eid=<?=$reqId?>&key=<?=$reqUUID?>" class="<?= CLASS_BTN_DANGER ?> mr-1"> <?= BTN_KEMBALI ?> </a> 
              <a href="main/index/aanwijzing_kualifikasi_chat/?reqId=<?=$reqId?>" class="<?= CLASS_BTN_INFO ?> mr-1"><?= BTN_REFRESH ?></a>
              <?php 
              if ($aktif_aanwitzing2 == '1') { ?>
              <a href="main/loadUrl/report/aanwijzing_kualifikasi_cetak_pdf/?reqId=<?=$reqId?>" target="_blank" <?=$style?> id="btnCetak"  class="<?= CLASS_BTN_PRIMARY ?>"><?= BTN_PRINT_BA ?> Hasil Aanwijzing</a>
              <?php 
              } ?>
            </div> 
          <?php 
          } ?>
        </div>
      </div>
    </div>
  </div> 
</div>   
