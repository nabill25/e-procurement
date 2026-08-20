<?php
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 * 
 */

$reqId = $this->input->get("reqId");
$this->libsession->cekSession($reqId);

$this->load->library("paketinfo"); $paketInfo = new paketinfo();
include_once("functions/string.func.php");
include_once("functions/default.func.php");
include_once("functions/date.func.php");
$this->load->model("PaketEvaluasiTeknisTawar");
$this->load->model("PaketRekanan");
$this->load->model("PaketDokumen");
$this->load->model("RekananEvaluasiAdminTawar");
$this->load->model("RekananEvaluasiTeknisTawar");
$this->load->model(array("PaketTahap","Paketpemenang"));

$paket_tahap_metode = new PaketTahap();
$paket_tahap = new PaketTahap();

$paket_evaluasi_teknis = new PaketEvaluasiTeknisTawar();
$paket_rekanan = new PaketRekanan();

$paketInfo->getPaket($reqId);
$reqNamaPaket = $paketInfo->nama;
$reqJenisPekerjaan = $paketInfo->jenis;
$reqMetodeEvaluasi = $paketInfo->metode_evaluasi;
$reqMetodeEvaluasiId = $paketInfo->metode_evaluasi_id;
$reqMetodeLelang = $paketInfo->metode_lelang_id;
$reqBobotTeknis = $paketInfo->bobot_teknis;
$reqPassingGrade = $paketInfo->passing_grade;
$reqUUID = $paketInfo->uuid;

$paket_evaluasi_teknis->selectByParams(array("PAKET_ID" => $reqId));
$paket_rekanan->selectByParams2(array("PAKET_ID" => $reqId), -1, -1, " AND A.TANGGAL_DAFTAR IS NOT NULL AND A.LULUS_PENDAFTARAN = 1 AND A.LULUS_KUALIFIKASI = 1 AND A.KIRIM_PENAWARAN = 1 ");
while($paket_rekanan->nextRow())
{
  $arrRekananId[] = $paket_rekanan->getField("REKANAN_ID");
  $arrRekanan[] = $paket_rekanan->getField("REKANAN");
  $arrPaketRekananId[] = $paket_rekanan->getField("PAKET_REKANAN_ID");
  $arrPaketRekananNilai[] = $paket_rekanan->getField("NILAI_PENAWARAN");
  $arrPasswordDokumen[] = $paket_rekanan->getField("KIRIM_PENAWARAN_PASSWORD");
}

if (is_array($arrRekanan)) {
  $arrRekananId = $arrRekananId;
  $arrRekanan = $arrRekanan;
  $arrPaketRekananId = $arrPaketRekananId;
  $arrPaketRekananNilai = $arrPaketRekananNilai;
  $arrPasswordDokumen = $arrPasswordDokumen;
} else {
  $arrRekananId = array();
  $arrRekanan = array();
  $arrPaketRekananId = array();
  $arrPaketRekananNilai = array();
  $arrPasswordDokumen = array();
}

$jenis_tahap = $paket_tahap_metode->getJenisTahapById($reqId);
$arrPembukaanAuction            = PEMBUKAAN_AUCTION;

if($paket_tahap->getCountByParams(array("URUT" => $arrPembukaanAuction[$jenis_tahap], "PAKET_ID" => $reqId), " AND NOW() >= TANGGAL_AWAL ") > 0)
  $allowPassword = 1;
else
{
  $allowPassword = 0;
}
// echo $allowPassword; die();

$getpaket_pemenang = new Paketpemenang();
$getpaket_pemenang->selectByParams(array("PAKET_ID" => $reqId), -1, -1);
$getpaket_pemenang->firstRow();
if ($getpaket_pemenang->getField("PUBLISH") == '1') {
  $formReadOnly = 'readonly';
  $formDisable = 'disabled';
} else {
  $formReadOnly = '';
  $formDisable = '';
}
?>

<script type="text/javascript">
$(function(){
  $('#ffUpload').form({
    url:'dokumen_pengadaan_upload_rekanan/upload_evaluasi',
    onSubmit:function(){
      if($(this).form('validate'))
      {
      var win = $.messager.progress({
                    title:'Upload Data',
                    msg:'Mengupload data...'
                  });
      }
      else
        $('input:file').MultiFile('reset');
      return $(this).form('validate');
    },
    success:function(data){
      if (data === 'Dokumen berhasil diupload.') { alertSuccess2(data); 
      } else {
        alertError2(data);
      }
      $.messager.progress('close');
      setTimeout(function() {
        document.location.reload();
      }, 2000);
    }
  });

  $('#ff').form({
    url:'rekanan_evaluasi_teknis_tawar_json/evaluasi_penawaran',
    onSubmit:function(){
      return $(this).form('validate');
    },
    success:function(data){
      // $.messager.alert('Info', data, 'info');
      alertSuccess2(data);
      $('#reqTextSimpan').html('<i class="fa fa-check-square-o"></i> Update');
    }
  });
});

function myFunction(a) {
    var id = "myPass"+a;
    var copyText = document.getElementById(id);
    copyText.select();
    copyText.setSelectionRange(0, 99999)
    document.execCommand("copy");
    // alert("Copied the text: " + copyText.value);
    alertSuccess2("Password disalin "+copyText.value);
  }

  function sumNilai(a,id) {
    var passingGrade  = $('#reqPassingGrade').val();
    var bobotTeknis   = $('#reqBobotTeknis'+id).val();
    var minimal = passingGrade * bobotTeknis / 100;
    var hitung        = bobotTeknis * a / 100;
    // alert(bobotTeknis+'-'+minimal+'-'+hitung);
    if (hitung < minimal) {
      alert('Nilai Teknis dibawah ambang batas '+minimal+'');
    }

    return $('#reqNilaiTeknis'+id).val(hitung.toFixed(2));
  }

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
        <h4 class="card-title text-white">Evaluasi Teknis</h4>
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
                  <li role="presentation" style="width: 33% !important;"><a href="main/index/evaluasi_penawaran_administrasi_sampul1/?reqId=<?=$reqId?>"><i class="fa fa-check" aria-hidden="true"></i>
                    <p>Evaluasi Administrasi</p>
                    </a></li>
                  <li role="presentation" class="active" style="width: 33% !important;"><a href="main/index/evaluasi_penawaran_teknis_sampul1/?reqId=<?=$reqId?>" ><i class="fa fa-pencil" aria-hidden="true"></i>
                    <p>Evaluasi Teknis</p>
                    </a></li>
                  <li role="presentation" style="width: 33% !important;"><a href="main/index/evaluasi_penawaran_rekapitulasi_sampul1/?reqId=<?=$reqId?>"><i class="fa fa-list-alt" aria-hidden="true"></i>
                    <p>Rekapitulasi</p>
                    </a></li>
                </ul>
              </div>
              <!-- <a href="main/index/evaluasi_penawaran_administrasi_sampul1/?reqId=<?=$reqId?>" class="btn btn-primary"> <span class="fa fa-pencil-square-o"></span> Evaluasi Administrasi</a>
              <a href="main/index/evaluasi_penawaran_teknis_sampul1/?reqId=<?=$reqId?>" class="btn btn-primary disabled"><span class="fa fa-pencil"></span> Evaluasi Teknis</a>
              <a href="main/index/evaluasi_penawaran_rekapitulasi_sampul1/?reqId=<?=$reqId?>" class="btn btn-primary"><span class="fa fa-list-alt"></span> Rekapitulasi</a> -->
            </div>
          </div>
          <div class="table-responsive">
            <table class="table table-bordered table-hover" >
              <tr>
                  <td width="30%"> Pekerjaan </td>
                  <td> <?=$reqNamaPaket?> </td>
                </tr>
                <tr>
                  <td width="30%"> Jenis Pekerjaan</td>
                  <td> <?=$reqJenisPekerjaan?> </td>
                </tr>
                  <tr>
                    <td width="30%"> Metode Evaluasi</td>
                    <td> <?=$reqMetodeEvaluasi?> </td>
                  </tr>
                <tr>
                <?php
                $this->load->model("Masterdokumentemplate");
                $master_dokumen = new Masterdokumentemplate();
                $master_dokumen->selectByParams(array('B.NAMA' => 'Dokumen Template Evaluasi Teknis'));
                if ($master_dokumen->countRow() > 0) {
                  $master_dokumen->firstRow();
                 ?>
                <tr>
                  <td>Template BA Evaluasi Teknis</td>
                  <td>
                    <a href="uploads/template/<?=$master_dokumen->getField('PATH_FILE')?>" target="_blank" class="btn-sm btn-success round">
                    <?= ICON_DOWNLOAD ?> <small>Download Template</small></a>
                  </td>
                </tr>
                <?php
                } ?>
                <td width="30%">Upload BA Evaluasi Teknis</td>
                <td>
                    <form id="ffUpload" method="post" novalidate enctype="multipart/form-data">
                    <input name="reqLinkFile" type="file" multiple class="maxsize-20240" accept="xls|xlsx|pdf" id="reqLinkFile" value="" />
                    <br><?= UPLOAD_XLS_XLSX_PDF_2MB ?>
                    <script>
                    // wait for document to load
                    $( "#reqLinkFile" ).bind( "change", function() {
                        document.querySelector('#reqSubmit').click();
                      });
                    </script>
                    <input type="hidden" name="reqId" value="<?=$reqId?>" />
                    <input type="hidden" name="reqNamaDokumen" id="reqNamaDokumen" value="Evaluasi penawaran administrasi" />
                    <input type="hidden" name="reqJenisDokumen" id="reqJenisDokumen" value="EVALUASI_PENAWARAN_TEKNIS" />
                    <input type="submit" name="reqSubmit" id="reqSubmit" value="" style="display:none">
                    </form>
                </td>
              </tr>
              <tr>
                <?php
                $paket_dokumen = new PaketDokumen();
                $paket_dokumen->selectByParams(array("PAKET_ID" => $reqId, "JENIS_DOKUMEN" => "EVALUASI_PENAWARAN_TEKNIS"));
                $paket_dokumen->firstRow();
                $dokumen = $paket_dokumen->getField("PATH_FILE");
                if($dokumen == "")
                {}
                else
                {
                ?>
                <td>Download BA Evaluasi Teknis </td>
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
            <!-- <div class="alert alert-info">Evaluasi Data Teknis</div> -->

            <form id="ff" method="post" novalidate enctype="multipart/form-data">
              <table class="table table-bordered table-hover">
                  <tr>
                    <th width="2%">No</th>
                    <th width="30%">
                      <?php
                      if ($reqMetodeLelang == 1 || $reqMetodeLelang == 7 || $reqMetodeLelang == 10) { // tender & tender cepat
                         echo "Nama Peserta";
                      } else {
                        echo "Nama Penyedia";
                      }?>
                    </th>
                    <?php
                    while($paket_evaluasi_teknis->nextRow())
                    {
                      // $pecahspasi = str_word_count($paket_evaluasi_teknis->getField("NAMA"));
                      // $gabungkata = '';
                      // $PecahStr = explode(" ", $paket_evaluasi_teknis->getField("NAMA"));
                      // for ( $i = 0; $i < $pecahspasi; $i++ ) {
                      //   if (($i % 3) == 0) {
                      //     $gabungkata .= $PecahStr[$i].' <br>';
                      //   } else {
                      //     $gabungkata .= $PecahStr[$i].' ';
                      //   }
                      // }
                      // echo '<th width="10%">'.$paket_evaluasi_teknis->getField("PAKET_EVAL_ADMIN_TAWAR_ID").'</th>';
                      // echo '<th width="10%">'.$gabungkata.'</th>';
                      $a[] = $paket_evaluasi_teknis->getField("PAKET_EVAL_TEKNIS_TAWAR_ID");
                      $b[] = $paket_evaluasi_teknis->getField("NAMA");
                    }
                    ?>
                    <th width="35%" class="text-center">Hasil Evaluasi</th>
                    <!-- <th width="30%">Keterangan</th> -->
                    <?php
                    if ($reqMetodeEvaluasiId == '2') { ?>
                    <th width="10%" class="text-center">Skor Teknis</th>
                    <th width="10%" class="text-center">Bobot Teknis</th>
                    <th width="10%" class="text-center">Nilai Teknis</th>
                    <?php
                    } ?>
                  </tr>
                  <?php
                  $no =1;
                  $check = 0;
                  $countStatusMemenuhiSyarat = 0;
                  echo '<input type="hidden" class="form-control" name="reqPassingGrade" id="reqPassingGrade" value="'.$reqPassingGrade.'">';
                if($allowPassword == 1)
                {
                  for($i=0;$i<count($arrRekanan);$i++)
                  {
                    $timeGetNotif = 1500;
                  ?>
                    <tr>
                      <td widtd="10px"><?= $no ?></td>
                      <td>
                        <?php
                          echo '<b>'.$arrRekanan[$i].'<b><br>';
                        if($allowPassword == 1) {
                          $password =  $arrPasswordDokumen[$i];

                          // echo '<small onClick="return showChat(\''.$arrRekanan[$i].'\',\''.$arrRekananId[$i].'\')" class="badge badge-primary" style="cursor:pointer"> <i class="fa fa-comment"></i> Chat Penyedia <span id="reqNotif'.$arrRekananId[$i].'"></span></small>
                          //       <a onClick="openAdd(\'main/loadUrl/main/evaluasi_penawaran_dokumen_popup/?reqId='.$reqId.'&rekanan='.$arrRekananId[$i].'&file='.$reqMetodePenyempaian.'&tahap=teknis\');">
                          //   <small class="badge badge-danger" style="margin-top:1%"> <i class="fa fa-folder-open-o"></i>  lihat dokumen</small>';
                          echo '<a onClick="openAdd(\'main/loadUrl/main/evaluasi_penawaran_dokumen_popup/?reqId='.$reqId.'&rekanan='.$arrRekananId[$i].'&file='.$reqMetodePenyempaian.'&tahap=teknis\');">
                            <small class="badge badge-danger" style="margin-top:1%"> <i class="fa fa-folder-open-o"></i>  lihat dokumen</small></a>';

                          $this->load->model(array("Paketundanganklarifikasi"));
                          $paketundangan = new Paketundanganklarifikasi();
                          $paketundangan->selectByParams(array("PAKET_ID" => $reqId, "REKANAN_ID" => $arrRekananId[$i]));
                          $paketundangan->firstRow();
                          $reqTanggalUndangan = dateToPageCheck($paketundangan->getField("TANGGAL_UNDANGAN"));
                          $reqTempat = $paketundangan->getField("TEMPAT");
                          $reqPelaksanaan = $paketundangan->getField("PELAKSANAAN");
                          $reqDokumenDibawa = $paketundangan->getField("DOKUMEN_DIBAWA");
                          $reqPeserta = $paketundangan->getField("PESERTA");
                          $reqKeterangan = $paketundangan->getField("KETERANGAN");

                          if($paketundangan->countRow() > 0) {
                            echo '<small class="badge badge-primary ml-1" style="cursor:pointer" onClick="openAddFrame(\'main/loadUrl/main/setup_undangan_klarifikasi?reqId='.$reqId.'&rekanan='.$arrRekananId[$i].'\')"><i class="fa fa-check-square-o"></i> Update Undangan </small>';
                          } else {
                            echo '<small class="badge badge-danger ml-1" style="cursor:pointer" onClick="openAddFrame(\'main/loadUrl/main/setup_undangan_klarifikasi?reqId='.$reqId.'&rekanan='.$arrRekananId[$i].'\')"><i class="fa fa-cogs"></i> Setup Undangan </small>';
                          }
                        }
                            // <a onClick="return myFunction(\''.$arrRekanan[$i].'\')">
                            // <div class="input-group" style="margin-top:1%">
                            //   <div class="input-group-prepend">
                            //     <i class="fa fa-copy"></i> &nbsp;&nbsp;
                            //   </div>
                            //   <input class="form-control" type="text" value="'.$password.'" id="myPass'.$arrRekanan[$i].'" style="border:none; height:10px; cursor:copy; font-size:11px" readonly>
                            // </div>
                            // </a>
                        ?>
                        <script>
                          setInterval("getNotif(<?= '\''.$arrRekanan[$i].'\''.','.'\''.$arrRekananId[$i].'\''  ?>);",<?= $timeGetNotif ?>);
                        </script>
                      </td>
                      <?php
                      for ($g=0; $g < count($a) ; $g++) {
                        // cek admin
                        $statusAdmin = 0;
                        $rekanan_evaluasi_admin_tawar = new RekananEvaluasiAdminTawar();
                        // $rekanan_evaluasi_admin_tawar->selectByParams(array("PAKET_REKANAN_ID" => $arrPaketRekananId[$i], "PAKET_EVAL_ADMIN_TAWAR_ID" => $a[$g]));
                        $rekanan_evaluasi_admin_tawar->selectByParams(array("PAKET_REKANAN_ID" => $arrPaketRekananId[$i]));
                        $rekanan_evaluasi_admin_tawar->firstRow();
                        $statusAdmin += $rekanan_evaluasi_admin_tawar->getField("MEMENUHI_SYARAT");
                        // echo $a[$g];
                        // end cek admin
                      }

                      for ($j=0; $j < count($a) ; $j++) {
                        $status = "";
                        $uraian = "";
                        $rekanan_evaluasi_teknis_tawar = new RekananEvaluasiTeknisTawar();
                        $rekanan_evaluasi_teknis_tawar->selectByParams(array("PAKET_REKANAN_ID" => $arrPaketRekananId[$i], "PAKET_EVAL_TEKNIS_TAWAR_ID" => $a[$j]));
                        $rekanan_evaluasi_teknis_tawar->firstRow();
                        $status = $rekanan_evaluasi_teknis_tawar->getField("MEMENUHI_SYARAT");
                        $uraian = $rekanan_evaluasi_teknis_tawar->getField("URAIAN");
                        $keterangan = $rekanan_evaluasi_teknis_tawar->getField("KETERANGAN");
                        $reqSkorTeknis = $rekanan_evaluasi_teknis_tawar->getField("SKOR_TEKNIS");
                        $reqNilaiTeknis = $rekanan_evaluasi_teknis_tawar->getField("NILAI_TEKNIS");

                        // Login untuk tombol Simpan dan Update
                        if($rekanan_evaluasi_teknis_tawar->countRow() > 0) {
                          $countStatusMemenuhiSyarat++;
                        }

                        $paket_dokumen = new PaketDokumen();
                        $paket_dokumen->selectByParams(array("PAKET_ID" => $reqId, "REKANAN_USER_ID" => $arrRekananId[$i], "JENIS_DOKUMEN" => "PENAWARAN", "TRIM(NAMA)" => trim($b[$j])));
                        $paket_dokumen->firstRow();
                        $dokumen  = $paket_dokumen->getField("PATH_FILE");
                        $password = "";

                        if($allowPassword == 1)
                          $password =  $arrPasswordDokumen[$i];
                      ?>

                      <!-- <td align="center"> -->
                        <?php
                        // if($dokumen == "" || $statusAdmin == '0')
                        // {
                        ?>
                          <!-- <img src="images/icon-hapus.png"> -->
                        <?php
                        // }
                        // else
                        // {
                        ?>
                        <!-- <a href="uploads/penawaran/<?=$dokumen?>" target="_blank"><img src="images/icon-download.png"></a> -->
                        <?php
                        // }
                        ?>
                      <!-- </td> -->
                      <?php
                      } ?>
                      <td style="font-size: 12px">
                        <?php
                        if ($statusAdmin >= 1) { ?>
                          <input type="hidden" name="reqPaketRekananId[]" value="<?=$arrPaketRekananId[$i]?>" />
                          <input type="hidden" name="reqRekananId[]" value="<?=$arrRekananId[$i]?>" />
                          <input type="hidden" name="reqPaketEvaluasiId[]" value="<?=$paket_evaluasi_teknis->getField("PAKET_EVAL_TEKNIS_TAWAR_ID")?>" />
                          <input type="hidden" name="reqEvaluasiTeknisSyarat[]" id="reqEvaluasiTeknisSyarat<?=$check?>" value="<?=$status?>" />
                          <input type="radio" name="reqPenilaian<?=$check?>" value="1" onClick="$('#reqEvaluasiTeknisSyarat<?=$check?>').val('1'); $('#reqUraian<?=$check?>').val('');  $('#reqUraian<?=$check?>').hide(); $('#reqKeterangan<?=$check?>').show()" <?php if($status == "1") { ?> checked <?php } ?> <?= $formDisable ?>> Memenuhi Syarat &nbsp;&nbsp;
                          <input type="radio" name="reqPenilaian<?=$check?>" value="0" onClick="$('#reqEvaluasiTeknisSyarat<?=$check?>').val('0'); $('#reqUraian<?=$check?>').val('');  $('#reqUraian<?=$check?>').show();  $('#reqKeterangan<?=$check?>').hide(); $('#reqSkorTeknis<?=$arrRekananId[$i]?>').val('0'); $('#reqNilaiTeknis<?=$arrRekananId[$i]?>').val('0');" <?php if($status == "0") { ?> checked <?php } ?> <?= $formDisable ?>> Tidak Memenuhi Syarat
                         <br><br>
                        <?php
                        } ?>
                      <!-- </td>
                      <td> -->
                      <?php
                        if ($statusAdmin >= 1) { ?>
                          <textarea name="reqUraian[]" class="form-control" id="reqUraian<?=$check?>" <?php if($status == "1" || $status == "") { ?> style="display:none;" <?php } ?> placeholder="alasan tidak memenuhi syarat.." <?= $formReadOnly ?>><?=$uraian?></textarea>
                          <textarea name="reqKeterangan[]" <?php if($status == "0") { ?> style="display:none;" <?php } ?> class="form-control" id="reqKeterangan<?=$check?>" placeholder="keterangan tambahan.." <?= $formReadOnly ?>><?=$keterangan?></textarea>
                      <?php
                      } ?>
                      </td>

                      <?php
                      if ($reqMetodeEvaluasiId == '2') { ?>
                      <td width="10%" class="text-center">
                        <?php
                        if ($statusAdmin >= 1) { ?>
                        <input type="text" class="form-control" name="reqSkorTeknis[]" id="reqSkorTeknis<?=$arrRekananId[$i]?>" onChange="return sumNilai(this.value, <?=$arrRekananId[$i]?>)"
                        OnFocus="addCommas('reqSkorTeknis<?=$arrRekananId[$i]?>')"
                        OnKeyUp="addCommas('reqSkorTeknis<?=$arrRekananId[$i]?>')"
                        OnBlur="addCommas('reqSkorTeknis<?=$arrRekananId[$i]?>')" maxlength="5" value="<?= $reqSkorTeknis ?>" <?= $formReadOnly ?>>
                        <?php 
                        } ?>
                      </td>
                      <td width="10%" class="text-center">
                        <?php
                        if ($statusAdmin >= 1) { ?>
                        <input type="text" class="form-control" name="reqBobotTeknis[]" id="reqBobotTeknis<?=$arrRekananId[$i]?>" value="<?= $reqBobotTeknis ?>" readonly="">
                        <?php 
                        } ?>
                      </td>
                      <td width="10%" class="text-center">
                        <?php
                        if ($statusAdmin >= 1) { ?>
                        <input type="text" class="form-control" name="reqNilaiTeknis[]" id="reqNilaiTeknis<?=$arrRekananId[$i]?>" value="<?= $reqNilaiTeknis ?>" readonly>
                        <?php 
                        } ?>
                      </td>
                      <?php
                      } ?>

                  <?php
                  $no++;
                  $check++;
                  $timeGetNotif = $timeGetNotif + 1000;
                  }
                  unset($rekanan_evaluasi_teknis_tawar);
                  unset($paket_dokumen);
                } // end of  if($allowPassword == 1)
                ?>
              </table>

              <div class="form-actions">
                <input type="hidden" name="reqId" value="<?=$reqId?>" />
                <input type="hidden" name="submitSimpan" value="Simpan" />
                <a href="main/index/paket_detil/?eid=<?=$reqId?>&key=<?=$reqUUID?>" class="<?= CLASS_BTN_DANGER ?> mr-1"> <?= BTN_KEMBALI ?> </a>
                <?php 
                if ($getpaket_pemenang->getField("PUBLISH") == '1') { } else { ?>
                <button type="submit" name="reqSimpan" id="reqSimpan" class="<?= CLASS_BTN_PRIMARY ?>"> <?php if($countStatusMemenuhiSyarat > 0 ) { echo '<span id="reqTextSimpan"> <i class="fa fa-check-square-o"></i> Update </span>'; } else { echo '<span id="reqTextSimpan"><i class="fa fa-check-square-o" id="reqTextSimpan"></i> Simpan</span>'; } ?></button>
                <?php 
                } ?>
                <a href="main/index/evaluasi_penawaran_rekapitulasi_sampul2/?reqId=<?=$reqId?>" class="<?= CLASS_BTN_INFO ?> mr-1 pull-right"> <span class="fa fa-refresh"></span> Update Rekapitulasi  </a>
                <?php /*?><input type="button" onclick="windowOpenerPopup(350,450,'Cetak Close','main/loadUrl/main/cetak_penawaran_teknis/?reqId=<?=$reqId?>');" name="varCetak" id="varCetak" value="Cetak" class="btn-cetak"/><?php */?>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<script type="text/javascript">
function showChat(a,b) {
    // $('#qnimate').slideToggle("slow");
    getChatNegoBox();
    $('#qnimate').slideToggle("slow");
    $('#titlePenyedia').html(a);
    $('#reqRekananId').val(b);
    updateRead();
}

function getNotif(a,b) {
  console.log(a);
  $.getJSON("chat_json/getNotif/<?= $reqId ?>/1/"+b, function(data) {
    if (data.countchat > 0) {
      $('#reqNotif'+data.id).html('<span class="badge badge-warning" style="border-radius:50%">&nbsp;</span>');
    } else {
      $('#reqNotif'+data.id).html('');
    }
  });
}

function updateRead() {
  var rekananid = $('#reqRekananId').val();
  if (rekananid) {
    $.getJSON("chat_json/updateRead?reqId=<?= $reqId ?>&reqJenis=1&reqRekananId="+rekananid, function(data) {
    });
  }
}

$(function(){
  $("#removeClass").click(function () {
    // $('#qnimate').slideToggle("slow");
    $('#qnimate').hide("slow");
  });
});
$(document).ready(function() {
  $(function(){
    $('#ffnego').form({
      url:'<?= base_url('chat_json/negoshoutbox') ?>',
      onSubmit:function(){
        var v=$(this).form('validate');
        if(v) {
          $('#submitPesan').removeClass('fa fa-send');
          $('#submitPesan').addClass('fa fa-refresh');
          return v;
        } else {
          hideLoad();
          return false;
        }
      },
      success:function(data){
        getChatNegoBox();
        $('#reqPesanNego').val('');
        $('#submitPesan').removeClass('fa fa-refresh');
        $('#submitPesan').addClass('fa fa-send');
      }
    });

  });

  setInterval("getChatNegoBox();",3000);
  // setInterval("getStatus();",1500);
});

setInterval(function(){ $('.popup-messages').scrollTop($('.direct-chat-messages').outerHeight());  }, 100);

function getChatNegoBox() {
  $('.popup-messages').scrollTop($('.direct-chat-messages').outerHeight());
  var rekananid = $('#reqRekananId').val();
  if (rekananid) {
    $.getJSON("chat_json/chatNegoBox?reqId=<?= $reqId ?>&reqJenis=1&reqRekananId="+rekananid, function(data) {
      $('.direct-chat-messages').html(data);
    });
  }
  $('.popup-messages').scrollTop($('.direct-chat-messages').outerHeight());
}

</script>
<div class="popup-box chat-popup" id="qnimate">
  <div class="popup-head">
    <div class="popup-head-left pull-left" style="font-size:12px">  <span id="titlePenyedia"></span>
    </div>
    <div class="popup-head-right pull-right">
      <button data-widget="remove" id="removeClass" class="chat-header-button pull-right" type="button" style="cursor: pointer"><i class="fa fa-close"></i></button>
    </div>
  </div>
  <div class="popup-messages">
    <div class="direct-chat-messages" id="chatNegoBox">
    </div>
  </div>
  <div class="popup-messages-footer">
    <form id="ffnego" class="easyui-form form-horizontal" method="post" novalidate enctype="multipart/form-data">
      <input type="hidden" name="reqId" value="<?=$reqId?>">
      <input type="hidden" id="reqRekananId" name="reqRekananId" value="">
      <input type="hidden" id="reqJenisChat" name="reqJenisChat" value="1">
      <fieldset>
        <div class="input-group" style="padding: 5px">
          <input type="text" id="reqPesanNego" required="" name="reqPesanNego" class="form-control easyui-validatebox" style="border-radius: 5px 0 0 5px;" placeholder="Tulis pesan disini...">
          <div class="input-group-append">
            <button class="btn btn-danger btn-search-x" type="submit"><span class="fa fa-send" id="submitPesan"></span></button>
          </div>
        </div>
      </fieldset>
    </form>
  <div class="btn-footer">
  </div>
  </div>
</div>
