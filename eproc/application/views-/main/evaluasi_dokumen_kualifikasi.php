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
$this->load->model("PaketEvaluasiKualifikasi");
$this->load->model("PaketRekanan");
$this->load->model("PaketDokumen");
$this->load->model("RekananEvaluasiKualifikasiTawar");
$this->load->model("PaketTahap");


$paket_tahap_metode = new PaketTahap();
$paket_tahap = new PaketTahap();

$paket_evaluasi_kualifikasi = new PaketEvaluasiKualifikasi();
$paket_rekanan = new PaketRekanan();

$paketInfo->getPaket($reqId);
$reqNamaPaket = $paketInfo->nama;
$reqJenisPekerjaan = $paketInfo->jenis;
$reqMetodeEvaluasi = $paketInfo->metode_evaluasi;
$reqMetodeLelang = $paketInfo->metode_lelang_id;
$reqMetodePenyempaian = $paketInfo->sistem_sampul;
$reqUUID = $paketInfo->uuid;

$paket_evaluasi_kualifikasi->selectByParams(array("PAKET_ID" => $reqId));
$paket_rekanan->selectByParams2(array("PAKET_ID" => $reqId), -1, -1, " AND A.TANGGAL_DAFTAR IS NOT NULL ");
while($paket_rekanan->nextRow())
{
	$arrRekananId[] = $paket_rekanan->getField("REKANAN_ID");
	$arrRekanan[] = $paket_rekanan->getField("REKANAN");
	$arrPaketRekananId[] = $paket_rekanan->getField("PAKET_REKANAN_ID");
	$arrPaketRekananNilai[] = $paket_rekanan->getField("NILAI_PENAWARAN");
	$arrPasswordDokumen[] = $paket_rekanan->getField("KIRIM_PENAWARAN_PASSWORD");
}
// echo "<pre>"; print_r($arrRekananId); die();
if (is_array($arrRekananId)) {
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
$arrPembukaanAuction            = EVALUASI_KUALIFIKASI_PRA;

$status_evaluasi_kualifikasi = $paket_tahap->getCountByParamsAktif(array("URUT" => $arrPembukaanAuction[$jenis_tahap], "PAKET_ID" => $reqId, "TAMPILKAN" => 1));
$status_evaluasi_kualifikasi2 = $paket_tahap->getCountByParamsBerlalu(array("URUT" => $arrPembukaanAuction[$jenis_tahap], "PAKET_ID" => $reqId, "TAMPILKAN" => 1));


if ($status_evaluasi_kualifikasi > 0 || $status_evaluasi_kualifikasi2 > 0)
	$allowPassword = 1;
else
{
	$allowPassword = 0;
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
       // $.messager.alert('Info', data, 'info');
			alertSuccess2(data); 
			setTimeout(function() {
        document.location.reload();
      }, 2000);
		}
	});

	$('#ff').form({
		url:'rekanan_evaluasi_kualifikasi_json/evaluasi_kualifikasi',
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
</script>
<style type="text/css">
  table th {
    background-color: #967adc;
    color: #fff;
  }
  .blink_me {
    animation: blinker 1.1s linear infinite;
  }

  @keyframes blinker {
    50% {
      opacity: 0;
    }
  }
</style>

<div class="row">
  <div class="col-md-12 col-sm-12">
    <div class="card">
      <div class="card-header card-head-inverse bg-primary">
        <h4 class="card-title text-white">Evaluasi Dokumen & Pembuktian Kualifikasi</h4>
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
          if ($allowPassword == '0') {
             echo '<div class="alert alert-danger" style="color:#fff">
                      <span style="color: #fff">
                        Evaluasi Dokumen & Pembuktian Kualifikasi belum dimulai.
                      </span>
                    </div>';
          } else 
          { ?>
            <div class="table-responsive">
              <table class="table table-bordered table-hover">
                <tr>
                  <td width="30%"> Pekerjaan</td>
                  <td> <?=$reqNamaPaket?> </td>
                </tr>
                <tr>
                  <td width="30%"> Jenis Pekerjaan</td>
                  <td> <?=$reqJenisPekerjaan?> </td>
                </tr>
                <tr>
                  <td width="20%"> Metode Evaluasi</td>
                  <td> <?=$reqMetodeEvaluasi?> </td>
                </tr> 
                <tr>
                  <td width="20%">Upload BA Evaluasi Kualifikasi</td>
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
                      <input type="hidden" name="reqNamaDokumen" id="reqNamaDokumen" value="Evaluasi Kualifikasi" />
                      <input type="hidden" name="reqJenisDokumen" id="reqJenisDokumen" value="EVALUASI_DOKUMEN_KUALIFIKASI" />
                      <input type="submit" name="reqSubmit" id="reqSubmit" value="" style="display:none">
                    </form>
                  </td>
                </tr>
                <tr>
                <?php
                $paket_dokumen = new PaketDokumen();
                $paket_dokumen->selectByParams(array("PAKET_ID" => $reqId, "JENIS_DOKUMEN" => "EVALUASI_DOKUMEN_KUALIFIKASI"));
                $paket_dokumen->firstRow();
                $dokumen = $paket_dokumen->getField("PATH_FILE");
                if($dokumen == "")
                {}
                else
                {
                ?>
                <td>Download BA Evaluasi Kualifikasi</td>
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
              <!--<table width="100%" border="0" cellpadding="2" cellspacing="1">-->
              <!-- <div class="alert alert-info">Evaluasi Administrasi</div> -->
              <form id="ff" method="post" novalidate enctype="multipart/form-data">

                  <table class="table table-bordered table-hover">
                      <tr>
                        <th width="2%">No</th>
                        <th>
                          <?php
                          if ($reqMetodeLelang == 1 || $reqMetodeLelang == 7 || $reqMetodeLelang == 10) { // tender & tender cepat
                             echo "Nama Peserta";
                          } else {
                            echo "Nama Penyedia";
                          }?>
                        </th>
                        <?php
                        while($paket_evaluasi_kualifikasi->nextRow())
                        {
                          $a[] = $paket_evaluasi_kualifikasi->getField("PAKET_EVAL_KUALIFIKASI_ID");
                          $b[] = $paket_evaluasi_kualifikasi->getField("NAMA");
                        }
                        ?>
                        <th width="35%" class="text-center">Evaluasi Kualifikasi</th>
                      </tr>

                      <?php
                      $no = 1;
                      $check = 0;
    									$countStatusMemenuhiSyarat = 0;
                      for($i=0;$i<count($arrRekanan);$i++)
                      {
                        $timeGetNotif = 1500;

                        $paket_dokumen = new PaketDokumen();
                        $paket_dokumen->selectByParams(array("PAKET_ID" => $reqId, "REKANAN_USER_ID" => $arrRekananId[$i], "JENIS_DOKUMEN" => "PENAWARAN_KUALIFIKASI"));
                        $jumlah_dokumen_kualifikasi = $paket_dokumen->countRow();

                      ?>
                        <tr>
                          <td widtd="10px"><?= $no ?></td>
                          <td>
                            <?php
                            echo '<b>'.$arrRekanan[$i].'<b><br>';
                            if($allowPassword == 1) {
                              if ($jumlah_dokumen_kualifikasi >=1 ) { 
                                $password =  $arrPasswordDokumen[$i];
                                echo '<small onClick="return showChat(\''.$arrRekanan[$i].'\',\''.$arrRekananId[$i].'\')" class="badge badge-primary" style="cursor:pointer"> <i class="fa fa-comment"></i> Chat Pembuktian kualifikasi <span id="reqNotif'.$arrRekananId[$i].'"></span></small>  
                                  <a onClick="openAdd(\'main/loadUrl/main/evaluasi_penawaran_dokumen_popup/?reqId='.$reqId.'&rekanan='.$arrRekananId[$i].'&file='.$reqMetodePenyempaian.'&tahap=kualifikasi\');"> <small class="badge badge-danger" style="margin-top:1%"> <i class="fa fa-folder-open-o"></i>  lihat dokumen</small>';
                              } else {
                                echo '<span class="badge badge-warning"><span class="fa fa-close"></span> belum upload dokumen</span>';
                              }
                            } else {
                              echo '<small class="badge badge-danger" style="margin-top:1%"> <i class="fa fa-close"></i> Evaluasi belum dimulai</small>';
                            }
                            ?>
                            <script>
                              setInterval("getNotif(<?= '\''.$arrRekanan[$i].'\''.','.'\''.$arrRekananId[$i].'\''  ?>);",<?= $timeGetNotif ?>);
                            </script>
                          </td>
                          <?php
                          for ($j=0; $j < count($a) ; $j++) {
                            $status = "";
                            $uraian = "";
                            $rekanan_evaluasi_kualifikasi = new RekananEvaluasiKualifikasiTawar();
                            $rekanan_evaluasi_kualifikasi->selectByParams(array("PAKET_REKANAN_ID" => $arrPaketRekananId[$i], "PAKET_EVAL_KUALIFIKASI_ID" => $a[$j]));
                            $rekanan_evaluasi_kualifikasi->firstRow();
                            $status = $rekanan_evaluasi_kualifikasi->getField("MEMENUHI_SYARAT");
                            $uraian = $rekanan_evaluasi_kualifikasi->getField("URAIAN");
                            $keterangan = $rekanan_evaluasi_kualifikasi->getField("KETERANGAN");

    												// Login untuk tombol Simpan dan Update
    												if($rekanan_evaluasi_kualifikasi->countRow() > 0) {
    													$countStatusMemenuhiSyarat++;
    												}
     
                          ?> 
                          <?php
                          } ?>
                          <td style="font-size: 12px">
                            <?php 
                            if($allowPassword == 1) {
                             ?>
                            <input type="hidden" name="reqPaketRekananId[]" value="<?=$arrPaketRekananId[$i]?>" />
                            <input type="hidden" name="reqPaketEvaluasiId[]" value="<?=$paket_evaluasi_kualifikasi->getField("PAKET_EVAL_KUALIFIKASI_ID")?>" />
                            <input type="hidden" name="reqEvaluasiAdminSyarat[]" id="reqEvaluasiAdminSyarat<?=$check?>" value="<?=$status?>" />

                            <input type="radio" name="reqPenilaian<?=$check?>" value="1" onClick="$('#reqEvaluasiAdminSyarat<?=$check?>').val('1'); $('#reqUraian<?=$check?>').val('');  $('#reqUraian<?=$check?>').hide(); $('#reqKeterangan<?=$check?>').show()" <?php if($status == "1") { ?> checked <?php } ?>> Memenuhi Syarat  &nbsp;&nbsp;
                            <input type="radio" name="reqPenilaian<?=$check?>" value="0" onClick="$('#reqEvaluasiAdminSyarat<?=$check?>').val('0'); $('#reqUraian<?=$check?>').val('');  $('#reqUraian<?=$check?>').show(); $('#reqKeterangan<?=$check?>').hide()" <?php if($status == "0") { ?> checked <?php } ?>> Tidak Memenuhi Syarat
                           <br><br>
                            <textarea class="form-control" name="reqUraian[]" id="reqUraian<?=$check?>" <?php if($status == "1" || $status == "") { ?> style="display:none;" <?php } ?> placeholder="alasan tidak memenuhi syarat..."><?=$uraian?></textarea>
                            <textarea class="form-control" name="reqKeterangan[]" id="reqKeterangan<?=$check?>" <?php if($status == "0") { ?> style="display:none;" <?php } ?> placeholder="keterangan tambahan.."><?=$keterangan?></textarea>
                            <?php 
                            } ?>
                          </td>
                        </tr>
                      <?php
                      $no++;
                      $check++;
                      $timeGetNotif = $timeGetNotif + 1000;
                      }
                      unset($paket_evaluasi_kualifikasi);
                      unset($paket_dokumen);
                      ?>

                  </table>

                  <div class="form-actions">
                    <input type="hidden" name="reqId" value="<?=$reqId?>" />
                    <input type="hidden" name="submitSimpan" value="Simpan" />
                    <a href="main/index/paket_detil/?eid=<?=$reqId?>&key=<?=$reqUUID?>" class="<?= CLASS_BTN_DANGER ?> mr-1"> <i class="fa fa-arrow-left"></i> Kembali </a>
                    <button type="submit" name="reqSimpan" id="reqSimpan" class="<?= CLASS_BTN_PRIMARY ?> mr-1"> <?php if($countStatusMemenuhiSyarat > 0 ) { echo '<span id="reqTextSimpan"> <i class="fa fa-check-square-o"></i> Update </span>'; } else { echo '<span id="reqTextSimpan"><i class="fa fa-check-square-o" id="reqTextSimpan"></i> Simpan</span>'; } ?></button>
                  </div>
                </div>
              </form>
          <?php 
          } ?>
        </div>
      </div>
    </div>
  </div>
</div>


<script type="text/javascript">
function showChat(a,b) {
    $('#qnimate').slideToggle("slow");
    $('#titlePenyedia').html(a);
    $('#reqRekananId').val(b);
    updateRead();
}

function getNotif(a,b) {
  console.log(a);
  $.getJSON("chat_json/getNotif/<?= $reqId ?>/3/"+b, function(data) {
    if (data.countchat > 0) {
      $('#reqNotif'+data.id).html('<span class="badge badge-warning blink_me" style="border-radius:50%;"><i class="fa fa-exclamation" style="padding:3px 2px;"></i></span>');
    } else {
      $('#reqNotif'+data.id).html('');
    }
  });
}

function updateRead() {
  var rekananid = $('#reqRekananId').val();
  if (rekananid) {
    $.getJSON("chat_json/updateRead?reqId=<?= $reqId ?>&reqJenis=3&reqRekananId="+rekananid, function(data) {
    });
  }
}

$(function(){
  $("#removeClass").click(function () {
    updateRead();
    $('#qnimate').slideToggle("slow");
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

// setInterval(function(){ $('.popup-messages').scrollTop($('.direct-chat-messages').outerHeight());  }, 100);

function getChatNegoBox() {
  $('.popup-messages').scrollTop($('.direct-chat-messages').outerHeight());
  var rekananid = $('#reqRekananId').val();
  if (rekananid) {
    $.getJSON("chat_json/chatNegoBox?reqId=<?= $reqId ?>&reqJenis=3&reqRekananId="+rekananid, function(data) {
      $('.direct-chat-messages').html(data);
    });
  }

  // $('.popup-messages').scrollTop($('.direct-chat-messages').outerHeight());
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
      <input type="hidden" id="reqJenisChat" name="reqJenisChat" value="3">
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
