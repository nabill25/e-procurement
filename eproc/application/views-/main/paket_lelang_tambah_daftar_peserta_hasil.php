<?php
$reqId = $this->input->get("reqId");
$this->libsession->cekSession($reqId);   

$this->load->library("kauth");  $userLogin = new kauth(); 
$this->load->model("PaketRekanan");
$this->load->model("PaketTahap");
$this->load->model("RekananEvaluasiKualifikasiTawar");
$this->load->model("PaketPanitia");
$this->load->library("paketinfo"); $paketInfo = new paketinfo();
include_once("functions/string.func.php");
include_once("functions/date.func.php");
include_once("functions/default.func.php");
$this->load->library("KMail");

$paket_rekanan = new PaketRekanan();
$paket_rekanan_total = new PaketRekanan();
$paket_tahap = new PaketTahap();
$paket_tahap_metode = new PaketTahap();

$reqMode = $this->input->post("reqMode");
$submitSimpan = $this->input->post("submitSimpan");

$arrKualifikasiPengumuman            = PENGUMUMAN_KUALIFIKASI;

$jenis_tahap = $paket_tahap_metode->getJenisTahapById($reqId);

$status_pengumuman_kualifikasi = $paket_tahap->getCountByParamsAktif(array("URUT" => $arrKualifikasiPengumuman[$jenis_tahap], "PAKET_ID" => $reqId, "TAMPILKAN" => 1));
$status_pengumuman_kualifikasi2 = $paket_tahap->getCountByParamsBerlalu(array("URUT" => $arrKualifikasiPengumuman[$jenis_tahap], "PAKET_ID" => $reqId, "TAMPILKAN" => 1));

if($status_pengumuman_kualifikasi > 0 || $status_pengumuman_kualifikasi2 > 0 )
{
  $cekAktif = 1;
} else {
  $cekAktif = 0;
} 

$reqLulusPendaftaran = isset($_POST["reqLulusPendaftaran"])?$_POST["reqLulusPendaftaran"]:'';
$reqLulusKeterangan = isset($_POST["reqLulusKeterangan"])?$_POST["reqLulusKeterangan"]:'';
$reqPaketRekananId = isset($_POST["reqPaketRekananId"])?$_POST["reqPaketRekananId"]:'';
$reqPaketRekananIdUser = $this->input->post("reqPaketRekananIdUser");
$reqLulusPendaftaranUser = $this->input->post("reqLulusPendaftaranUser");


$paket_rekanan->selectByParams(array("PAKET_ID" => $reqId));
$totalPaket = $paket_rekanan_total->getCountByParams(array("PAKET_ID" => $reqId));
$paketInfo->getPaket($reqId);

//echo $paket_rekanan->query;exit;
?> 
<script type="text/javascript">
$(document).ready(function() {
  $(function(){
    $('#ff').form({
      url:'paket_rekanan_json/verifikasi_peserta_kualifikasi',
      onSubmit:function(){
        publishEvaluasi();
        return $(this).form('validate');
      },
      success:function(data){
        // $.messager.alert('Informasi',data, 'info');
        // document.location.reload();
      }
    });
    
  });

  // window.onload=function(){
  //   document.getElementById("reqSimpan").click();
  // }; 
  
});

 

function publishEvaluasi()
{
  $.messager.confirm("Konfirmasi","Publish/Update Hasil Evaluasi Kualifikasi?",function(r){
    if (r){
      $.get( "paket_json/set_publish_evaluasi_kualifikasi/?reqId=<?=$reqId?>", function( data ) {
          if(data == "1")
          {
            // $("#reqSimpan").css("display", "none");
            alertSuccess2('Publish Evaluasi Kualifikasi berhasil.');
            setTimeout(function () {
              document.location.reload();
            }, 1000);
          }
          // else
            // $.messager.alert('Info', data, 'info');
      });
    }
  });
}

function publishEvaluasiLoad() {
  $.get( "paket_json/set_publish_evaluasi_kualifikasi/?reqId=<?=$reqId?>", function( data ) {
    alertSuccess2('Publish Evaluasi Kualifikasi berhasil.');
    $('#loading').hide();
  });
}

function validasiPublish(id)
{
  msg = "Validasi publish hasil kualifikasi?";

  $.messager.confirm('Konfirmasi',msg,function(r){
    if (r){
      $.get( "paket_json/set_validasi_publish_hasil_kualifikasi_paket/?reqId="+id, function( data ) {
        $.messager.alertReload('Informasi',data, 'info');
      });
    }
  });
}
 
</script>

<div class="row"> 
  <div class="col-md-12 col-sm-12">
    <div class="card">
      <div class="card-header card-head-inverse bg-primary">
        <h4 class="card-title text-white">Pengumuman Hasil Kualifikasi</h4>
        <a class="heading-elements-toggle"><i class="fa fa-ellipsis-v font-medium-3"></i></a> 
        <div class="heading-elements">
            <ul class="list-inline mb-0">
              <li><a data-action="expand"><i class="ft-maximize"></i></a></li>
              <!-- <li><a data-action="close"><i class="ft-x"></i></a></li> -->
            </ul>
        </div>
      </div>

      <form id="ff" class="easyui-form form-horizontal" method="post" data-options="novalidate:true" enctype="multipart/form-data">
      <div class="card-content collapse show border-info border-darken-2">
        <div class="card-body area-datatable">
          <?php 
          if ($cekAktif == '0') {
             echo '<div class="alert alert-danger" style="color:#fff">
                      <span style="color: #fff">
                        Pengumuman Hasil Kualifikasi belum dimulai.
                      </span>
                    </div>';
           } else 
           { ?>
            <div class="table-responsive">
              <table class="table table-bordered mb-0"> 
                <tbody>
                  <tr class="judul-kolom">
                    <th style="width: 3%">No.</th>
                    <th >Nama Peserta</th>
                    <!-- <th>Syarat</th> -->
                    <?php 
                    if ($paketInfo->metode_lelang_id == '2' || $paketInfo->metode_lelang_id == '3' || $paketInfo->metode_lelang_id == '5' || $paketInfo->metode_lelang_id == '6' || $paketInfo->metode_lelang_id == '8' ) { ?>
                    <th style="width: 20%">Diundang</th>
                    <?php 
                    } ?>
                     <?php 
                    if ($paketInfo->metode_lelang_id == '1'|| $paketInfo->metode_lelang_id == '7' || $paketInfo->metode_lelang_id == '10' ) { ?>
                    <th style="width: 20%">Tanggal Daftar</th>
                    <th style="width: 20%; text-align:center;">Evaluasi Kualifikasi</th>
                    <th style="width: 5%; text-align:center;">Hasil <br>Evaluasi</th>
                    <?php 
                    } ?>
                  </tr>
                   <?php
                   if ($totalPaket == 0) {
                     echo '<tr><td colspan="8">. : : Belum ada peserta : : .</td></tr>';
                   } else 
                   {
                      $i=1;
                      while($paket_rekanan->nextRow())
                      {
                        $disable = "";

                        $rekanan_evaluasi_kualifikasi = new RekananEvaluasiKualifikasiTawar();
                        $rekanan_evaluasi_kualifikasi->selectByParams(array("PAKET_REKANAN_ID" => $paket_rekanan->getField("PAKET_REKANAN_ID")));
                        $rekanan_evaluasi_kualifikasi->firstRow();
                        $status = $rekanan_evaluasi_kualifikasi->getField("MEMENUHI_SYARAT");
                        $uraian = $rekanan_evaluasi_kualifikasi->getField("URAIAN");
                        $keterangan = $rekanan_evaluasi_kualifikasi->getField("KETERANGAN");

                        if($status == '1')
                        {
                          $status_kualifikasi = '<img class="text-center" src="images/centang-cetak.png">';
                          $keterangan_kualifikasi = $keterangan;
                        }
                        else
                        {
                          $status_kualifikasi = '<img class="text-center" src="images/uncentang-cetak.png">';
                          $keterangan_kualifikasi = $uraian;
                        }
                    ?>
                      <tr>
                        <td><?=$i?>.</td>
                        <td> 
                          <a title="#" onClick="openAdd('main/loadUrl/main/data_rekanan/?reqId=<?=$paket_rekanan->getField("REKANAN_ID")?>');">
                            <i class="fa fa-eye btn btn-info btn-sm text-white" style="padding: 2px 4px !important"></i>
                            <?= '<b>'.$paket_rekanan->getField("REKANAN").'</b>'; ?></a>
                        </td> 
                        <?php 
                        if ($paketInfo->metode_lelang_id != '1' && $paketInfo->metode_lelang_id != '7' && $paketInfo->metode_lelang_id != '10' ) { ?>
                        <td> <?=getFormattedDate($paket_rekanan->getField("TANGGAL_UNDANG"))?> </td>
                        <?php 
                        } else { ?>
                        <td> <?=getFormattedDate($paket_rekanan->getField("TANGGAL_DAFTAR")).' <br> <small> '.$paket_rekanan->getField("JAM_DAFTAR").'</small>'?> 
                        </td>
                        <td class="text-center">
                          <strong><?=$status_kualifikasi.'<br><small>'.$keterangan_kualifikasi.'</small>';?></strong>
                        </td>
                        <td style="text-align:center;">
                        <input type="hidden" name="reqPaketRekananId[]" value="<?=$paket_rekanan->getField("PAKET_REKANAN_ID")?>" />

                          <?php 
                          if($status == '1') {
                            // echo '<img class="text-center" src="images/centang-cetak.png">';
                            echo '<span class="badge badge-primary">Lulus</span>';
                            echo '<input value="1" name="reqLulusPendaftaran[]" style="cursor:pointer" type="hidden" checked>'; 
                          } else {
                            // echo '<img class="text-center" src="images/uncentang-cetak.png">';
                            echo '<span class="badge badge-danger">Tidak Lulus</span>';
                            echo '<input value="0" name="reqLulusPendaftaran[]" style="cursor:pointer" type="hidden">'; 
                          } ?>
                        </td>
                        <?php 
                        } ?> 
                      </tr>
                      <?php
                        $i++;
                      }
                    }
                    ?>
                </tbody>
              </table>   
            </div>

            <div class="form-actions">
              <input type="hidden" name="submitSimpan" value="Simpan" />
              <input type="hidden" id="reqMode" name="reqMode" />
              <input type="hidden" name="reqId" value="<?=$reqId?>" />
              <input type="hidden" name="reqPaketRekananId[]" value="<?=$arrPaketRekananId[$i]?>" />
              <a href="main/index/paket_detil/?eid=<?=$reqId?>&key=<?=$paketInfo->uuid?>" class="<?= CLASS_BTN_DANGER ?> mr-1"> <?= BTN_KEMBALI ?> </a> 
              <?php 
              $paket_panitia_belum_validasi = new PaketPanitia();
              $paket_panitia_belum_validasi->selectByParams(array("A.PAKET_ID" => $reqId),"","","AND VALIDASI_HASIL_KUALIFIKASI = '0' ");
              if ($paket_panitia_belum_validasi->countRow() == 0 ) 
              {
                if($paketInfo->publish_eval_kualifikasi == "1") { ?>
                   <button type="submit" name="reqSimpan" id="reqSimpan" class="<?= CLASS_BTN_INFO ?> mr-1">  
                    <span class="fa fa-edit"></span> Update Hasil Evaluasi Kualifikasi
                  </button>
                  <!-- <button type="submit" name="reqSimpan" id="reqSimpan" class="<?php //echo CLASS_BTN_PRIMARY ?> mr-1"> <i class="fa fa-check-square-o"></i> 
                    Publish Hasil Evaluasi Kualifikasi
                  </button> -->
                <?php 
                } else
                {
                  if ($cekAktif == '1') {
                 ?>
                  <button type="submit" name="reqSimpan" id="reqSimpan" class="<?= CLASS_BTN_SUCCESS ?> mr-1">  
                    <?= BTN_PUBLISH ?> Hasil Evaluasi Kualifikasi
                  </button>
                  <!-- <a onClick="publishEvaluasi();" id="btnPublish" class="<?= CLASS_BTN_SUCCESS ?>"><?= BTN_PUBLISH ?> Hasil Evaluasi Kualifikasi</a> -->
                <?php
                    }
                  } 
              }

                /* CHECK APAKAH PANITIA */
                $paket_panitia = new PaketPanitia();
                $adaValidasi = $paket_panitia->getCountByParams3(array("PAKET_ID" => $reqId, "NIP" => $this->NIP),"","","AND VALIDASI_HASIL_KUALIFIKASI = '0' ");
                if($adaValidasi > 0)
                {
               ?>
               <span class="<?= CLASS_BTN_SUCCESS ?>" style="margin-top: .2%">
                 <span class="fa fa-exclamation-triangle"></span> <a onClick="validasiPublish('<?=$reqId?>')"> Validasi untuk publish hasil kualifikasi? </a>
               </span>
               <?php
                }

                 echo '<span class="badge badge-danger" style="margin-top: .2%; cursor: pointer" onclick="openAdd(\'main/loadUrl/notif/validasi_hasil_kualifikasi?reqId='.$reqId.'\')">
                        <span class="fa fa-exclamation-triangle"></span>
                          '.$paket_panitia_belum_validasi->countRow().' Tim Pengadaan yang Belum Validasi
                        </span>
                        ';
              ?>
              <?php 
              if ($totalPaket > 0) { ?>
              <!-- <a class="<?= CLASS_BTN_INFO ?>" href="main/loadUrl/report/daftar_peserta_lelang_excel/?reqId=<?=$reqId?>" target="_blank" ><?= BTN_PRINT ?></a> -->
              <?php 
              } ?>
            </div> 
          <?php 
          } ?>
        </div>
      </div>
      </form>
      
    </div>
  </div> 
</div>  

<script type="text/javascript">
window.onload=function(){
  publishEvaluasiLoad();
};
</script>