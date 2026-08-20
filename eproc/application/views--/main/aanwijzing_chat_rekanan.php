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
$this->load->model("PaketAanwijzing");
$this->load->model("PaketTahap");
$this->load->model("PaketRekanan");
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
$paket_rekanan = new PaketRekanan();

$arrAanwijzing                  = AANWIJZING;

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
$reqMetodeEvaluasi = $paketInfo->metode_evaluasi;
$reqUUID = $paketInfo->uuid;

$jenis_tahap = $paket_tahap_metode->getJenisTahapById($reqId);
$paket_aanwijzing_first->selectByParams(array("PAKET_ID" => $reqId, "PARENT_ID" => 0));
$totalAan = $paket_aanwijzing_first->firstRow();
$aktif_aanwitzing = $paket_tahap->getCountByParamsAktif(array("URUT" => $arrAanwijzing[$jenis_tahap], "PAKET_ID" => $reqId, "TAMPILKAN" => 1));
$aktif_aanwitzing2 = $paket_tahap->getCountByParamsBerlalu(array("URUT" => $arrAanwijzing[$jenis_tahap], "PAKET_ID" => $reqId, "TAMPILKAN" => 1));
if($aktif_aanwitzing > 0 && $aktif_aanwitzing2 < 1 )
{
  $cekAktif = 1;
} else {
  $cekAktif = 0;
}

$paket_aanwijzing->selectByParams(array("PAKET_ID" => $reqId, "PARENT_ID" => 0));


$paket_aanwijzing_validasi = new PaketAanwijzingValidasi();
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
?>
<script type="text/javascript">
$(document).ready(function() {

  $(function(){
    $('#ff').form({
      url:'aanwijzing_chat_json/dokumen_aanwijzing_rekanan',
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
          document.location.href = 'main/index/aanwijzing_chat_rekanan/?reqId=<?=$reqId?>';
        }, 1000);
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
        <h4 class="card-title text-white">Aanwijzing</h4>
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
                        Aanwijzing belum dimulai atau sudah selesai.
                      </span>
                    </div>';
           } ?>
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
                <?php
                  $this->load->model("PaketDokumen");
                  $paket_dokumen = new PaketDokumen();
                  $paket_dokumen->selectByParams(array("PAKET_ID" => $reqId, "JENIS_DOKUMEN" => "BERITA_ACARA_AANWIJZING"));
                  $paket_dokumen->firstRow();
                  $dokumen = $paket_dokumen->getField("PATH_FILE");
                  if($dokumen == "")
                  {}
                  else
                  {
                  ?>
                  <td>Download Berita Acara & Addendum</td>
                  <td>
                    <a href="uploads/penawaran/<?=$dokumen?>" target="_blank" class="btn-sm btn-success round">
                      <?= ICON_DOWNLOAD ?> Download
                    </a>
                  </td>
                  <?php
                  }
                  ?>
                </tr>
                <?php 
                $this->load->model("Aanwijzingaddendum");
                $aan = new Aanwijzingaddendum();
                $aan->selectByParams(array("PAKET_ID"=>$reqId)); 
                if ($aan->countRow() > 0) { 
                ?>
                <tr>
                  <td>Addendum</td>
                  <td>
                    <a onclick="openAdd('main/loadUrl/main/aanwijzing_addendum_rekanan?reqAidi=<?= $reqId ?>')" class="badge badge-primary text-white"> <i class="fa fa-plus-circle"></i> Lihat addendum </a>
                  </td>
                </tr>
                <?php 
                } ?>
              </table>
            </div>

            <div class="alert alert-success mb-2" style="color:#fff">
              <span style="color: #fff">
                 Setelah proses aanwijzing selesai, semua peserta/rekanan yang sudah mendaftar paket ini dinyatakan sudah mengerti dan paham mengenai syarat dan ketentuan.
              </span>
            </div>
              
            <div class="table-responsive">
              <table  class="table table-bordered mb-1" id="tbl_bidang">
                <tbody>
                  <tr class="judul-kolom">
                    <th># ID</th>
                    <th style="width: 70%">Tanya/Jawab</th>  
                    <th style="width: 10%; text-align: center">Aksi</th>
                  </tr>
                  <?php
                  if ($totalAan=='') {
                      echo '<tr><td colspan="6">. : Tidak ada data : .</td></tr>';
                    } else {
                    $i=1;
                    while($paket_aanwijzing->nextRow())
                    {
                      $tglupload = explode('.', $paket_aanwijzing->getField("TANGGAL_UPLOAD"));
                      if ($i=1) {
                          $reqRekananUserId = $paket_aanwijzing->getField("REKANAN_USER_ID");
                          // echo '<input type="hidden" name="reqPaketDokumenId" value="'.$paket_aanwijzing->getField("REKANAN_USER_ID").'" />';
                      }
                      // Get Parent
                      $paket_aanwijzing_parent_first = new PaketAanwijzing();
                      $paket_aanwijzing_parent_first->selectByParams(array("PAKET_ID" => $reqId, "PARENT_ID" => $paket_aanwijzing->getField("PAKET_AANWIJZING_ID") ));
                      $paket_aanwijzing_parent_first->firstRow();
                  ?>
                  <tr style="
                  <?php 
                  if ($paket_aanwijzing->getField("REKANAN_USER_ID") == $this->REKANAN_ID) {
                    echo 'background-color: #ff9969; font-weight: bold;';
                  } else {
                    echo 'background-color: #ffd6c3; font-weight: bold;';
                    } ?>
                  ">
                      <td>
                        <?php 
                        if ($paket_aanwijzing->getField("REKANAN_USER_ID") == $this->REKANAN_ID) {
                          echo '<i class="fa fa-user" style="color:#000; opacity:1"></i> <span style="color:#000; opacity:1">'. $paket_aanwijzing->getField("KODE_CUT") .'</span>' ; 
                        } else {
                          echo '<i class="fa fa-user" style="color:#000; opacity:1"></i> <span style="color:#000; opacity:1">'. $paket_aanwijzing->getField("KODE_CUT") .'</span>' ; 
                        }
                        ?> 
                      </td> 
                      <td style="color: #000; opacity: 1 !important">
                          <?=$paket_aanwijzing->getField("KETERANGAN")?> <br>
                          <small><i class="fa fa-clock-o"></i> <?=$tglupload[0] ?></small>
                      </td> 
                      <td style="width: 10%; text-align: center"> 
                        <?php 
                        $paket_aanwijzing_parent_count = new PaketAanwijzing();
                        $cekJawab = $paket_aanwijzing_parent_count->getCountByParams(array("PAKET_ID" => $reqId, "PARENT_ID" => $paket_aanwijzing->getField("PAKET_AANWIJZING_ID") )); 
                        if ($paket_aanwijzing->getField("REKANAN_USER_ID") == $this->REKANAN_ID && $cekAktif == '1' && $cekJawab == '0') { 
                          ?>
                        <a onClick="deleteData('aanwijzing_chat_json/delete/', '<?=$paket_aanwijzing->getField("PAKET_AANWIJZING_ID")?>')" class="btn-aksi">
                          <?= ICON_DELETE ?>
                        </a>
                        <?php 
                        } ?>
                      </td>
                  </tr>
                  <?php
                  // if (count($paket_aanwijzing_parent->nextRow())>=1) {
                    $paket_aanwijzing_parent = new PaketAanwijzing();
                      $paket_aanwijzing_parent->selectByParams(array("PAKET_ID" => $reqId, "PARENT_ID" => $paket_aanwijzing->getField("PAKET_AANWIJZING_ID") ));
                    while($paket_aanwijzing_parent->nextRow())
                      {
                      $tglupload_parent = explode('.', $paket_aanwijzing_parent->getField("TANGGAL_UPLOAD"));
                  ?>
                    <tr >
                      <td><i class="fa fa-arrow-right" aria-hidden="true"></i> Jawab<small><i><b> <?=$paket_aanwijzing->getField("KODE_CUT")?> </b></i></small>
                      </td>
                      <td>
                          <?=$paket_aanwijzing_parent->getField("KETERANGAN")?> <br>
                          <small><i class="fa fa-clock-o"></i> <?=$tglupload_parent[0] ?></small>
                      </td> 
                      <td style="width: 10%; text-align: center">
                      <?php 
                      if ($paket_aanwijzing_parent->getField("PATH_FILE")) { ?>
                      <a href="uploads/aanwijzing/<?=$paket_aanwijzing_parent->getField("PATH_FILE")?>" target="_blank" class="btn-sm btn-success round">
                          <?= ICON_DOWNLOAD ?>
                      </a>
                      <?php 
                      } ?> 
                      </td>
                  </tr>
                  <?php }  
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
                  <span class="alert-icon"><i class="fa fa-th"></i></span>  <strong>Aanwijzing</strong>  
                </div> 
                <div class="row">
                  <div class="form-group col-md-12 mb-2">
                    <label>Pertanyaan</label>
                    <textarea name="reqKeterangan" id="reqKeterangan" cols="45" rows="5" class="easyui-validatebox form-control" required></textarea>
                  </div> 
                </div>  

                <input type="hidden" name="reqId" value="<?=$reqId?>" /> 
                <a href="main/index/aanwijzing_chat_rekanan/?reqId=<?=$reqId?>" class="<?= CLASS_BTN_INFO ?>"><i class="fa fa-refresh"></i> Refresh</a>
                <button type="submit" class="<?= CLASS_BTN_PRIMARY ?> mr-1"><i class="fa fa-check-square-o"></i> Kirim</button>
                <p class="mt-1"><b>* Pastikan mengirim Pertanyaan sebelum tanggal dan jam berakhir.</b></p>
              </form>
            </div>
            <!-- <div class="form-actions mt-2">
              <a href="main/index/paket_detil/?reqId=<?=$reqId?>" class="btn btn-danger"><i class="fa fa-arrow-left"></i> Kembali</a>
            </div>   -->
          <?php 
          } ?>

            <div class="form-actions">
              <!-- <input type="hidden" name="reqId" value="<?=$reqId?>" /> -->
              <a href="main/index/paket_detil/?eid=<?=$reqId?>&key=<?=$reqUUID?>" class="<?= CLASS_BTN_DANGER ?> mr-1"> <i class="fa fa-arrow-left"></i> Kembali </a> 
              <?php  ?>
              <?php 
              if ($cekAktif == '0') { ?>
              <a href="main/loadUrl/report/aanwijzing_cetak_pdf/?reqId=<?=$reqId?>" target="_blank" class="<?= CLASS_BTN_PRIMARY ?>"><i class="fa fa-print"></i> Hasil Aanwijzing</a>
              <?php 
              } ?>
              <!-- <button type="submit" class="btn btn-primary"><i class="fa fa-check-square-o"></i> Simpan</button> -->
            </div> 

        </div>
      </div>
    </div>
  </div> 
</div>   
