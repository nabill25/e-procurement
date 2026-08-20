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
$this->load->model("PaketEvaluasiHargaTawar");
$this->load->model("PaketRekanan");
$this->load->model("PaketDokumen");
$this->load->model("RekananPaketPenawaran");
$this->load->model("RekananEvaluasiHargaTawar");
$this->load->model("RekananEvaluasiTeknisTawar");
$this->load->model("RekananEvaluasiAdminTawar");
$this->load->model("PaketTahap");


$paket_tahap_metode = new PaketTahap();
$paket_tahap = new PaketTahap();

$paket_evaluasi_harga = new PaketEvaluasiHargaTawar();
$paket_rekanan = new PaketRekanan();
$rekanan_paket_penawaran = new RekananPaketPenawaran();
$rekanan_paket_penawaran2 = new RekananPaketPenawaran();

$submitSimpan = $this->input->post("submitSimpan");
$reqPaketRekananId = $_POST["reqPaketRekananId"];
$reqPaketEvaluasiId = $_POST["reqPaketEvaluasiId"];
$reqEvaluasiHargaSyarat = $_POST["reqEvaluasiHargaSyarat"];
$reqUraian = $_POST["reqUraian"];
$reqKeterangan = $_POST["reqKeterangan"];

$paketInfo->getPaket($reqId);
$reqNamaPaket = $paketInfo->nama;
$reqJenisPekerjaan = $paketInfo->jenis;
$reqMetodeEvaluasi = $paketInfo->metode_evaluasi;
$reqMetodeEvaluasiId = $paketInfo->metode_evaluasi_id;
$reqMetodeLelang = $paketInfo->metode_lelang_id;
$reqBobotHarga = $paketInfo->bobot_harga;
$reqPassingGrade = $paketInfo->passing_grade;

$paket_evaluasi_harga->selectByParams(array("PAKET_ID" => $reqId));
$paket_rekanan->selectByParams2(array("PAKET_ID" => $reqId), -1, -1, " AND A.TANGGAL_DAFTAR IS NOT NULL AND A.LULUS_PENDAFTARAN = 1 AND A.LULUS_KUALIFIKASI = 1 AND A.KIRIM_PENAWARAN = 1 AND A.LULUS_PENAWARAN_SAMPUL1 = 1");
while($paket_rekanan->nextRow())
{
  $arrRekananId[] = $paket_rekanan->getField("REKANAN_ID");
  $arrRekanan[] = $paket_rekanan->getField("REKANAN");
  $arrPaketRekananId[] = $paket_rekanan->getField("PAKET_REKANAN_ID");
  $arrPaketRekananNilai[] = $paket_rekanan->getField("NILAI_PENAWARAN");
  $arrPasswordDokumen[] = $paket_rekanan->getField("KIRIM_PENAWARAN_PASSWORD2");
  $arrNilaiPenawaran[] = $paket_rekanan->getField("NILAI_PENAWARAN");
}

if (is_array($arrRekanan)) {
  $arrRekananId = $arrRekananId;
  $arrRekanan = $arrRekanan;
  $arrPaketRekananId = $arrPaketRekananId;
  $arrPaketRekananNilai = $arrPaketRekananNilai;
  $arrPasswordDokumen = $arrPasswordDokumen;
  $arrNilaiPenawaran = $arrNilaiPenawaran;
} else {
  $arrRekananId = array();
  $arrRekanan = array();
  $arrPaketRekananId = array();
  $arrPaketRekananNilai = array();
  $arrPasswordDokumen = array();
  $arrNilaiPenawaran = array();
}


$jenis_tahap = $paket_tahap_metode->getJenisTahapById($reqId);
$arrPembukaanAuction            = PEMBUKAAN_AUCTION_2FILE;
$arrPembukaanPenawaran            = EVALUASI_PANAWARAN_SAMPUL2;

if($paket_tahap->getCountByParams(array("URUT" => $arrPembukaanAuction[$jenis_tahap], "PAKET_ID" => $reqId), " AND NOW() >= TANGGAL_AWAL ") > 0)
  $allowPassword = 1;
else
{
  $allowPassword = 0;
}

if($paket_tahap->getCountByParams(array("URUT" => $arrPembukaanPenawaran[$jenis_tahap], "PAKET_ID" => $reqId), " AND NOW() >= TANGGAL_AWAL ") > 0)
  $allowPassword2 = 1;
else
{
  $allowPassword2 = 0;
}

$rekanan_paket_penawaran->selectByParamsEvaluasiRekanan($arrPaketRekananId, array("A.PAKET_ID" => $reqId));
$rekanan_paket_penawaran2->selectByParamsEvaluasiRekanan($arrPaketRekananId, array("A.PAKET_ID" => $reqId));

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
      alert(data);
      $.messager.progress('close');
      document.location.reload();
    }
  });

  $('#ff').form({
    url:'rekanan_evaluasi_harga_tawar_json/evaluasi_penawaran',
    onSubmit:function(){
      return $(this).form('validate');
    },
    success:function(data){
      // $.messager.alert('Info', data, 'info');
      alertSuccess2(data);
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
    var bobotHarga   = $('#reqBobotHarga'+id).val();
    var hitung        = bobotHarga * a / 100;
    if (hitung <= passingGrade) {
      return $('#reqNilaiHarga'+id).val(hitung.toFixed(2));
    } else {
      alert('Nilai Harga Diatas Passing Grade ('+passingGrade+')');
      return $('#reqNilaiHarga'+id).val(0);
    }
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
        <h4 class="card-title text-white">Evaluasi Harga</h4>
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
          if ($allowPassword2 == 0) { ?>
          <div class="alert alert-danger" style="color:#fff">
            <span style="color: #fff">
              Evaluasi Penawaran File 2 belum mulai.
            </span>
          </div>
          <?php
          } ?>
          <div class="row">
            <div class="form-group col-md-12 mb-2">
              <div class="col-md-12">
                <ul class="nav nav-tabs process-model more-icon-preocess" role="tablist">
                  <li role="presentation" class="active" style="width: 50% !important"><a href="main/index/evaluasi_penawaran_harga_sampul2/?reqId=<?=$reqId?>"><i class="fa fa-money" aria-hidden="true"></i>
                    <p>Evaluasi Harga</p>
                    </a></li>
                  <li role="presentation" style="width: 50% !important"><a href="main/index/evaluasi_penawaran_rekapitulasi_sampul2/?reqId=<?=$reqId?>"><i class="fa fa-list-alt" aria-hidden="true"></i>
                    <p>Rekapitulasi</p>
                    </a></li>
                </ul>
              </div>
              <!-- <a href="main/index/evaluasi_penawaran_harga_sampul2/?reqId=<?=$reqId?>" class="btn btn-primary disabled"><i class="fa fa-money"></i> Koreksi Aritkatik & Evaluasi Harga</a> -->
              <!-- <a href="main/index/evaluasi_penawaran_aritmatika_sampul2/?reqId=<?=$reqId?>" class="btn btn-primary"><span class="fa fa-pencil"></span> Perbandingan Aritmatika</a> -->
              <!-- <a href="main/index/evaluasi_penawaran_rekapitulasi_sampul2/?reqId=<?=$reqId?>" class="btn btn-primary"><span class="fa fa-list-alt"></span> Rekapitulasi</a> -->
            </div>
          </div>

          <div class="table-responsive">
            <table class="table table-bordered table-hover">
              <tr>
                <td width="30%"> Pekerjaan </td>
                <td> <?=$reqNamaPaket?> </td>
              </tr>
              <tr>
                <td width="30%"> Jenis Pekerjaan </td>
                <td> <?=$reqJenisPekerjaan?> </td>
              </tr>
              <tr>
                <td> Metode Evaluasi </td>
                <td> <?=$reqMetodeEvaluasi?>  </td>
              </tr>
              <tr>
                <td>Upload BA Evaluasi Harga </td>
                <td>
                    <form id="ffUpload" method="post" novalidate enctype="multipart/form-data">
                    <input name="reqLinkFile" type="file" multiple class="maxsize-20240" accept="xls|xlsx|pdf" id="reqLinkFile" value="" />
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
                    <input type="hidden" name="reqNamaDokumen" id="reqNamaDokumen" value="Evaluasi penawaran harga" />
                    <input type="hidden" name="reqJenisDokumen" id="reqJenisDokumen" value="EVALUASI_PENAWARAN_HARGA" />
                    <input type="submit" name="reqSubmit" id="reqSubmit" value="" style="display:none">
                    </form>
                </td>
              </tr>
              <tr>
                <?php
                $paket_dokumen = new PaketDokumen();
                $paket_dokumen->selectByParams(array("PAKET_ID" => $reqId, "JENIS_DOKUMEN" => "EVALUASI_PENAWARAN_HARGA"));
                $paket_dokumen->firstRow();
                $dokumen = $paket_dokumen->getField("PATH_FILE");
                if($dokumen == "")
                {}
                else
                {
                ?>
                <td>Download BA Evaluasi Harga </td>
                <td>
                <a href="uploads/penawaran/<?=$dokumen?>" target="_blank"><img src="images/icon-download.png"></a>
                </td>
                <?php
                }
                ?>
              </tr>
            </table>

            <!-- <div class="alert alert-info">Evaluasi Harga</div> -->
            <form id="ff" method="post" novalidate enctype="multipart/form-data">
              <table class="table table-bordered table-hover">
                  <tr>
                    <th width="2%">No</th>
                    <th width="30%">Peserta</th>
                    <th>Penawaran</th>
                    <th>Penawaran Terkoreksi</th>
                    <?php
                    while($paket_evaluasi_harga->nextRow())
                    {
                      // $pecahspasi = str_word_count($paket_evaluasi_harga->getField("NAMA"));
                      // $gabungkata = '';
                      // $PecahStr = explode(" ", $paket_evaluasi_harga->getField("NAMA"));
                      // for ( $i = 0; $i < $pecahspasi; $i++ ) {
                      //   if (($i % 3) == 0) {
                      //     $gabungkata .= $PecahStr[$i].' <br>';
                      //   } else {
                      //     $gabungkata .= $PecahStr[$i].' ';
                      //   }
                      // }
                      // echo '<th width="10%">'.$paket_evaluasi_harga->getField("PAKET_EVAL_ADMIN_TAWAR_ID").'</th>';
                      // echo '<th width="10%">'.$gabungkata.'</th>';
                      $a[] = $paket_evaluasi_harga->getField("PAKET_EVAL_HARGA_TAWAR_ID");
                      $b[] = $paket_evaluasi_harga->getField("NAMA");
                    }
                    ?>
                    <th width="35%" class="text-center">Hasil Evaluasi</th>
                    <!-- <th width="30%">Keterangan</th> -->
                    <?php
                    if ($reqMetodeEvaluasiId == '2') { ?>
                    <th width="8%" class="text-center">Skor Harga</th>
                    <th width="8%" class="text-center">Bobot Harga</th>
                    <th width="8%" class="text-center">Nilai Harga</th>
                    <?php
                    } ?>
                  </tr>

                  <?php
                  echo '<input type="hidden" class="form-control" name="reqPassingGrade" id="reqPassingGrade" value="'.$reqPassingGrade.'">';
                  while($rekanan_paket_penawaran->nextRow())
                  {
                    $style = "";
                    if($rekanan_paket_penawaran->getField("QUANTITY") == 0)
                      $style = ' style="display:none" ';

                    $no = 1;
                    $check = 0;
                    for($i=0;$i<count($arrRekanan);$i++)
                    {

                      for ($g=0; $g < count($a) ; $g++) {
                        // cek admin
                        $statusAdmin = 0;
                        $rekanan_evaluasi_admin_tawar = new RekananEvaluasiAdminTawar();
                        // $rekanan_evaluasi_admin_tawar->selectByParams(array("PAKET_REKANAN_ID" => $arrPaketRekananId[$i], "PAKET_EVAL_ADMIN_TAWAR_ID" => $a[$g]));
                        $rekanan_evaluasi_admin_tawar->selectByParams(array("PAKET_REKANAN_ID" => $arrPaketRekananId[$i]));
                        $rekanan_evaluasi_admin_tawar->firstRow();
                        $statusAdmin += $rekanan_evaluasi_admin_tawar->getField("MEMENUHI_SYARAT");
                        // end cek admin

                        // cek teknis
                        $statusTeknik = 0;
                        $rekanan_evaluasi_teknis_tawar = new RekananEvaluasiTeknisTawar();
                        // $rekanan_evaluasi_teknis_tawar->selectByParams(array("PAKET_REKANAN_ID" => $arrPaketRekananId[$i], "PAKET_EVAL_TEKNIS_TAWAR_ID" => $a[$g]));
                        $rekanan_evaluasi_teknis_tawar->selectByParams(array("PAKET_REKANAN_ID" => $arrPaketRekananId[$i]));
                        $rekanan_evaluasi_teknis_tawar->firstRow();
                        $statusTeknik += $rekanan_evaluasi_teknis_tawar->getField("MEMENUHI_SYARAT");
                        // cek teknis
                      }

                      if ($reqMetodePengadaan == 7) {
                        $statusAdmin += 1;
                        $statusTeknik += 1;
                      } else {
                        $statusAdmin += $statusAdmin;
                        $statusTeknik += $statusTeknik;
                      }

                      $arrSummary["SUMMARY"][$no] = $rekanan_paket_penawaran->getField("SUMMARY");
                      $idElement = $arrPaketRekananId[$i]."-".$rekanan_paket_penawaran->getField("PAKET_PENAWARAN_ID");
                    if($allowPassword2 == 1)
                    {
                    ?>
                      <tr>
                        <td widtd="10px"><?= $no ?></td>
                        <td>
                          <?php
                            echo '<b>'.$arrRekanan[$i].'<b><br>';
                            if($allowPassword == 1) {
                              $password =  $arrPasswordDokumen[$i];
                              echo '<a onClick="openAdd(\'main/loadUrl/main/evaluasi_penawaran_dokumen_popup/?reqId='.$reqId.'&rekanan='.$arrRekananId[$i].'&file='.$reqMetodePenyempaian.'&tahap=harga\');"> <small class="badge badge-danger" style="margin-top:1%"> <i class="fa fa-folder-open-o"></i>  lihat dokumen</small>';
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
                        </td>
                        <td align="right" <?=$style?>>
                          <?php
                          // echo $statusAdmin.'-'.$statusTeknik.'<br>';
                          if ($statusAdmin >= 1 && $statusTeknik >= 1)
                          { ?>
                            <?=numberToIna($rekanan_paket_penawaran->getField("UP_".$arrPaketRekananId[$i]))?>
                            <?php
                            if($rekanan_paket_penawaran->getField("BOQ_".$arrPaketRekananId[$i]) == "")
                              {}
                              else
                              {
                              ?>
                            <br>
                           <a href="uploads/penawaran/<?=$rekanan_paket_penawaran->getField("BOQ_".$arrPaketRekananId[$i])?>" class="badge badge-dark">download rincian BoQ</a>
                            <?php
                            }
                            ?>
                          <?php
                          } ?>
                          </td>
                          <td align="center" <?=$style?>>
                          <?php
                          if ($statusAdmin >= 1 && $statusTeknik >= 1)
                          { ?>
                            <input class="form-control" id="reqUnitPriceKoreksi<?=$idElement?>" name="reqUnitPriceKoreksi<?=$idElement?>" value="<?=numberToIna($rekanan_paket_penawaran->getField("UPK_".$arrPaketRekananId[$i]))?>" class="easyui-validatebox"style="text-align:right; width:100% !important;" OnFocus="FormatAngka('reqUnitPriceKoreksi<?=$idElement?>')" OnKeyUp="FormatUang('reqUnitPriceKoreksi<?=$idElement?>');  summary('<?=$arrPaketRekananId[$i]?>');" OnBlur="FormatUang('reqUnitPriceKoreksi<?=$idElement?>')" style="width: 100%">
                          <?php
                          } ?>
                          </td>
                          <!-- <td align="center" <?=$style?>> -->
                            <input class="form-control" id="reqJumlahKoreksi<?=$idElement?>" name="reqJumlahKoreksi<?=$idElement?>" value="<?=numberToIna($rekanan_paket_penawaran->getField("SUMKOREKSI_".$arrPaketRekananId[$i]))?>" class="easyui-validatebox" style="text-align:right; width:100% !important; background:#F8F8F8" type="hidden">
                          <!-- </td>       -->
                        <?php
                        // while($paket_evaluasi_harga->nextRow())
                        // {
                        //   $a = $paket_evaluasi_harga->getField("PAKET_EVAL_HARGA_TAWAR_ID");
                        //   $b = $paket_evaluasi_harga->getField("NAMA");
                        // }
                        for ($j=0; $j < count($a) ; $j++) {
                        $status = "";
                        $uraian = "";
                        $rekanan_evaluasi_harga_tawar = new RekananEvaluasiHargaTawar();
                        $rekanan_evaluasi_harga_tawar->selectByParams(array("PAKET_REKANAN_ID" => $arrPaketRekananId[$i], "PAKET_EVAL_HARGA_TAWAR_ID" => $a[$j]));
                        $rekanan_evaluasi_harga_tawar->firstRow();
                        // $status     = !$rekanan_evaluasi_harga_tawar->getField("MEMENUHI_SYARAT") ? $rekanan_evaluasi_harga_tawar->getField("MEMENUHI_SYARAT") : '';
                        $status     = $rekanan_evaluasi_harga_tawar->getField("MEMENUHI_SYARAT");
                        $uraian     = $rekanan_evaluasi_harga_tawar->getField("URAIAN");
                        $keterangan = $rekanan_evaluasi_harga_tawar->getField("KETERANGAN");
                        $reqSkorHarga = $rekanan_evaluasi_harga_tawar->getField("SKOR_HARGA");
                        $reqNilaiHarga = $rekanan_evaluasi_harga_tawar->getField("NILAI_HARGA");

                        $paket_dokumen = new PaketDokumen();
                        $paket_dokumen->selectByParams(array("PAKET_ID" => $reqId, "REKANAN_USER_ID" => $arrRekananId[$i], "JENIS_DOKUMEN" => "PENAWARAN_HARGA", "TRIM(NAMA)" => trim($b[$j])));
                        $paket_dokumen->firstRow();
                        $dokumen  = $paket_dokumen->getField("PATH_FILE");
                        $password = "";

                        if($allowPassword == 1)
                          $password =  $arrPasswordDokumen[$j];
                        ?>
                        <!-- <td align="center"> -->
                        <?php
                        // if($statusAdmin >= 1 && $statusTeknik >= 1)
                        // {
                        ?>
                          <!-- <a href="uploads/penawaran/<?=$dokumen?>" target="_blank"><img src="images/icon-download.png"></a> -->
                        <?php
                        // }
                        // else
                        // {
                        ?>
                          <!-- <img src="images/icon-hapus.png"> -->
                        <?php
                        // }
                        ?>
                        <!-- </td> -->
                        <?php
                        } ?>
                        <td style="font-size: 12px">
                        <?php
                        if ($statusAdmin >= 1 && $statusTeknik >= 1)
                        { ?>
                          <input type="hidden" name="reqPaketRekananId[]" value="<?=$arrPaketRekananId[$i]?>" />
                          <input type="hidden" name="reqPaketEvaluasiId[]" value="<?=$paket_evaluasi_harga->getField("PAKET_EVAL_HARGA_TAWAR_ID")?>" />
                          <input type="hidden" name="reqEvaluasiHargaSyarat[]" id="reqEvaluasiHargaSyarat<?=$check?>" value="<?=$status?>" />
                          <input type="radio" name="reqPenilaian<?=$check?>" value="1" onClick="$('#reqEvaluasiHargaSyarat<?=$check?>').val('1'); $('#reqUraian<?=$check?>').val('');  $('#reqUraian<?=$check?>').hide(); $('#reqKeterangan<?=$check?>').show()" <?php if($status == "1") { ?> checked <?php } ?>> Memenuhi Syarat <br>
                          <input type="radio" name="reqPenilaian<?=$check?>" value="0" onClick="$('#reqEvaluasiHargaSyarat<?=$check?>').val('0'); $('#reqUraian<?=$check?>').val('');  $('#reqUraian<?=$check?>').show();  $('#reqKeterangan<?=$check?>').hide(); $('#reqSkorHarga<?=$arrRekananId[$i]?>').val('0'); $('#reqNilaiHarga<?=$arrRekananId[$i]?>').val('0');" <?php if($status == "0") { ?> checked <?php } ?>> Tidak Memenuhi Syarat
                          <br><br>
                        <?php
                        } ?>
                       <!--  </td>
                        <td> -->
                        <?php
                        if ($statusAdmin >= 1 && $statusTeknik >= 1)
                        { ?>
                          <textarea name="reqUraian[]" class="form-control" id="reqUraian<?=$check?>" <?php if($status == "1" || $status == "") { ?> style="display:none;" <?php } ?> placeholder="alasan tidak memenuhi syarat.."><?=$uraian?></textarea>
                          <textarea name="reqKeterangan[]" class="form-control" id="reqKeterangan<?=$check?>" <?php if($status == "0") { ?> style="display:none;" <?php } ?> placeholder="keterangan tambahan.."><?=$keterangan?></textarea>
                        <?php
                        } ?>

                        <?php
                        if ($reqMetodeEvaluasiId == '2') { ?>
                        <td width="8%" class="text-center">
                          <input type="text" class="form-control" name="reqSkorHarga[]" id="reqSkorHarga<?=$arrRekananId[$i]?>" onChange="return sumNilai(this.value, <?=$arrRekananId[$i]?>)"
                          OnFocus="addCommas('reqSkorHarga<?=$arrRekananId[$i]?>')"
                          OnKeyUp="addCommas('reqSkorHarga<?=$arrRekananId[$i]?>')"
                          OnBlur="addCommas('reqSkorHarga<?=$arrRekananId[$i]?>')" maxlength="5" value="<?= $reqSkorHarga ?>">
                        </td>
                        <td width="8%" class="text-center">
                          <input type="text" class="form-control" name="reqBobotHarga[]" id="reqBobotHarga<?=$arrRekananId[$i]?>" value="<?= $reqBobotHarga ?>" readonly="">
                        </td>
                        <td width="8%" class="text-center">
                          <input type="text" class="form-control" name="reqNilaiHarga[]" id="reqNilaiHarga<?=$arrRekananId[$i]?>" value="<?= $reqNilaiHarga ?>" readonly>
                        </td>
                        <?php
                        } ?>
                        </td>
                      </tr>
                    <?php
                    $no++;
                    $check++;
                    }
                    }
                  } // end of while($rekanan_paket_penawaran->nextRow())
                  unset($rekanan_evaluasi_harga_tawar);
                  unset($paket_dokumen);

                while($rekanan_paket_penawaran2->nextRow())
                  {
                  ?>
                  <input type="hidden" name="reqPaketPenawaranId[]" value="<?php echo $rekanan_paket_penawaran2->getField("PAKET_PENAWARAN_ID")?>">
                  <input type="hidden" name="reqQuantity[]" id="reqQuantity<?php echo $rekanan_paket_penawaran2->getField("PAKET_PENAWARAN_ID")?>" value="<?php echo $rekanan_paket_penawaran2->getField("QUANTITY")?>">
                  <?php
                  } ?>

              </table>

              <div class="form-actions">
                <input type="hidden" name="reqId" value="<?=$reqId?>" />
                <input type="hidden" name="submitSimpan" value="Simpan" />
                <a href="main/index/paket_detil/?reqId=<?=$reqId?>" class="btn btn-danger mr-1 text-white"> <?= BTN_KEMBALI ?> </a>
                <button type="submit" name="reqSimpan" id="reqSimpan" class="btn btn-primary"> <?= BTN_SIMPAN ?></button>
              </div>
            </form>

          </div>
        </div>

      </div>
    </div>
  </div>
</div>