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
$this->load->model("Paket");
$this->load->model("PaketTahap"); 
$this->load->model("PaketRekanan");
$this->load->model("PaketDokumen");
$this->load->model("RekananEvaluasiAdminTawar");
$this->load->model("RekananEvaluasiTeknisTawar");
$this->load->model("PaketEvaluasiAdminTawar");
$this->load->model("PaketEvaluasiTeknisTawar");
$this->load->model("PaketEvaluasiHargaTawar");

include_once("functions/string.func.php");
include_once("functions/date.func.php");
include_once("functions/default.func.php");

/* create objects */
$paket = new Paket();
$paket_keterangan = new Paket();  
$paket_tahap_jadwal = new PaketTahap();
$paket_rekanan = new PaketRekanan();
$rekanan_evaluasi_admin_tawar = new RekananEvaluasiAdminTawar();
$rekanan_evaluasi_teknis_tawar = new RekananEvaluasiTeknisTawar();

$paket_dokumen = new PaketDokumen();
$paket_evaluasi_admin_tawar = new PaketEvaluasiAdminTawar();
$paket_evaluasi_teknis_tawar = new PaketEvaluasiTeknisTawar();
$paket_evaluasi_harga_tawar = new PaketEvaluasiHargaTawar();

$paket_rekanan->selectByParamsPaketLelangV2(array("A.PAKET_ID" => $reqId, "A.REKANAN_ID" => $this->REKANAN_ID), -1, -1, " AND TANGGAL_DAFTAR IS NOT NULL ");
$paket_rekanan->firstRow();
$reqPaketRekananId = $paket_rekanan->getField("PAKET_REKANAN_ID");
$kirimPenawaran = $paket_rekanan->getField("KIRIM_PENAWARAN"); // 0: belum kirim, 1: sudah kirim
$reqKirimPenawaranPassword = $paket_rekanan->getField("KIRIM_PENAWARAN_PASSWORD");
$reqKirimPenawaranPassword2 = $paket_rekanan->getField("KIRIM_PENAWARAN_PASSWORD2");

$rekanan_evaluasi_admin_tawar->selectByParams(array("PAKET_REKANAN_ID" => $reqPaketRekananId));
$rekanan_evaluasi_admin_tawar->firstRow();

$rekanan_evaluasi_teknis_tawar->selectByParams(array("PAKET_REKANAN_ID" => $reqPaketRekananId));
$rekanan_evaluasi_teknis_tawar->firstRow();
if ($rekanan_evaluasi_admin_tawar->getField('memenuhi_syarat') == '0' || $rekanan_evaluasi_teknis_tawar->getField('memenuhi_syarat') == '0') { 
  // redirect(base_url().'main/index/403');
  $reqLulus = 0;
} else {
  $reqLulus = 1;
}

$paket->selectByParamsMonitoring(array(), -1, -1, " AND PAKET_ID = '".$reqId."' ");
$paket->firstRow();
if ($paket->getField("PUBLISH_PAKET") == 0 && ($this->USER_TYPE_ID == '6' || $this->USER_TYPE_ID == '')) { // khusus PENYEDIA di cek 
  echo "Sorry tidak ada paket";
  exit();
}

$paketInfo->getPaket($reqId);
$reqNama = $paketInfo->nama;
$reqSistemSampul = $paketInfo->sistem_sampul;
$reqMetodeLelang = $paketInfo->metode_lelang_id;
 
$paket_tahap_jadwal->selectByParamsJadwal(array("TAMPILKAN" => "1"), -1, -1, " AND PAKET_ID = '".$reqId."' ");
//echo $paket_tahap_jadwal->query;exit;

/* CHECK APAKAH SUDAH DIPASSWORD */
$paket_dokumen->selectByParams(array("PAKET_ID" => $reqId, "REKANAN_USER_ID" => coalesce($this->ID,0), "NOT COALESCE(FILE_PASSWORD, 'X')" => 'X'));
$paket_dokumen->firstRow();

$paket_evaluasi_admin_tawar->selectByParamsRekananDokumen($this->ID, array("A.PAKET_ID" => $reqId));
//echo $paket_evaluasi_admin_tawar->query;exit;
$paket_evaluasi_teknis_tawar->selectByParamsRekananDokumen($this->ID, array("A.PAKET_ID" => $reqId));
$paket_evaluasi_harga_tawar->selectByParamsRekananDokumen($this->ID, array("A.PAKET_ID" => $reqId));

?>
 
<div class="row">  
  <div class="col-md-12 col-sm-12">
    <div class="card">
      <div class="card-content collapse show border-info border-darken-2">
        <div class="card-body"> 
          <table class="table table-bordered">
            <?php 
            if ($reqLulus == '0') {
              echo '<div class="alert alert-danger" style="color:#fff">
                      <span style="color: #fff">
                        Evaluasi Administrasi atau Teknis gagal, anda tidakß bisa meneruskan proses pengadaan selanjutnya.
                      </span>
                    </div>';
            }

            if ($reqMetodeLelang != '7') 
            { ?>
                  <?php
                  if($reqSistemSampul == "2")
                  {
                  ?>
                  <tr style="background-color: #967adc; color: #fff; font-size: 1.2em">
                    <td align="center" colspan="5"><b>FILE I</b> <br>
                     <a onClick="return myFunction('1','File I untuk membuka dokumen')">
                      <span class="fa fa-key"></span><small> Klik icon kunci untuk Copy Password pembuka Dokumen </small>
                     </a>
                     <input type="text" value="<?php echo $reqKirimPenawaranPassword  ?>*****" id="myPass1" style="border:none; height:10px; width:5px !important; cursor:copy;" readonly>
                    </td>
                  </tr>
                  <?php
                  } else { ?>
                  <tr style="background-color: #967adc; color: #fff; font-size: 1.2em">
                    <td align="center" colspan="5">
                      <a onClick="return myFunction('1','untuk membuka dokumen')">
                      <span class="fa fa-key"></span><small> Klik icon kunci untuk Copy Password pembuka Dokumen </small>
                     </a>
                     <input class="form-control" type="text" value="<?php echo $reqKirimPenawaranPassword  ?>*****" id="myPass1" style="width: auto; height:1px" readonly>
                    </td>
                  </tr>
                  <?php
                  }
                  ?>
                  <tr>
                    <th align="center" style="width: 5px">No.</th>
                    <th align="left" style="width:95%"> <?=translate(" Nama Dokumen", "Documents Name")?></th>
                    <!-- <th align="center"><?=translate("Ukuran File", "File Size")?></th> -->
                    <!-- <th align="center"><?=translate("Tgl Upload", "Upload date")?></th> -->
                    <th style="width: 10%; text-align: center"><?=translate("Aksi", "Action")?></th>
                  </tr>
                  <tr class="gelap" style="background-color: #b7b7b7; color: #000">
                      <!-- <td>I</td> -->
                      <td colspan="5">Dokumen Administrasi</td>
                  </tr>
                    <?php
                    $id = 1;
                    $i=1;
                    $jumlahDokumenAdmin = 0;
                    $jumlahUploadAdmin = 0;
                    $jumlahDokumenWajibBelumUpload = 0;
                    while($paket_evaluasi_admin_tawar->nextRow())
                    {
                    ?>
                    <tr class="terang">
                      <td style="width: 5px"><?=$i?>.</td>
                      <td> <?=$paket_evaluasi_admin_tawar->getField("NAMA")?> <?php if($paket_evaluasi_admin_tawar->getField("WAJIB") == '1'){ ?> <font color="#FF0000">* </font><?php } ?></td>
                     <!--  <td align="center" width="7%"> <small><?=round($paket_evaluasi_admin_tawar->getField("UKURAN") / 1024, 2)?>Kb </small></td>
                      <td align="center" width="10%"> <small><?=($paket_evaluasi_admin_tawar->getField("TANGGAL_UPLOAD"))?></small> </td> -->
                      <td align="center" class="kolom-aksi">
                      <?php
                      if($paket_evaluasi_admin_tawar->getField("PAKET_DOKUMEN_ID") == "")
                      { echo "-";
                      }
                      else
                      {
                      ?>
                          <a href="uploads/penawaran/<?=$paket_evaluasi_admin_tawar->getField("PATH_FILE")?>" target="_blank"><img src="images/icon-download.png" alt="" width="16" height="16" border="0" /></a>
                          <?php
                          $jumlahUploadAdmin++; ?>
                        <br>
                          <small class="badge badge-info" style="font-size: 9px"><?=round($paket_evaluasi_admin_tawar->getField("UKURAN") / 1024, 2)?> Kb </small>
                          <small class="badge badge-info" style="font-size: 9px"><?=($paket_evaluasi_admin_tawar->getField("TANGGAL_UPLOAD"))?></small>
                      <?php
                      }
                      ?>
                      </td>
                    </tr>
                    <?php
                      $i++;
                      $id++;
                      $jumlahDokumenAdmin++;
                    }
                    ?>
                  <tr class="gelap" style="background-color: #b7b7b7; color: #000">
                      <!-- <td>II</td> -->
                      <td colspan="5">Dokumen Teknis</td>
                  </tr>
                    <?php
                    $i=1;
                    $jumlahDokumenTeknis = 0;
                    $jumlahUploadTeknis = 0;
                    while($paket_evaluasi_teknis_tawar->nextRow())
                    {
                    ?>
                    <tr class="terang">
                      <td style="width: 5px"><?=$i?>.</td>
                      <td> <?=$paket_evaluasi_teknis_tawar->getField("NAMA")?> <?php if($paket_evaluasi_teknis_tawar->getField("WAJIB") == '1'){ ?> <font color="#FF0000">* </font><?php } ?></td>
                      <!-- <td align="center" width="7%"> <small><?=round($paket_evaluasi_teknis_tawar->getField("UKURAN") / 1024, 2)?>Kb </small></td>
                      <td align="center" width="10%"> <small><?=($paket_evaluasi_teknis_tawar->getField("TANGGAL_UPLOAD"))?></small> </td> -->
                      <td align="center" class="kolom-aksi">
                      <?php
                      if($paket_evaluasi_teknis_tawar->getField("PAKET_DOKUMEN_ID") == "")
                      { echo "-"; 
                      }
                      else
                      {
                      ?>
                          <a href="uploads/penawaran/<?=$paket_evaluasi_teknis_tawar->getField("PATH_FILE")?>" target="_blank"><img src="images/icon-download.png" alt="" width="16" height="16" border="0" /></a> 
                      <?php 
                          $jumlahUploadTeknis++; ?>
                          <br>
                          <small class="badge badge-info" style="font-size: 9px"><?=round($paket_evaluasi_teknis_tawar->getField("UKURAN") / 1024, 2)?> Kb </small>
                          <small class="badge badge-info" style="font-size: 9px"><?=($paket_evaluasi_teknis_tawar->getField("TANGGAL_UPLOAD"))?></small>
                      <?php
                      }
                      ?>
                      </td>
                    </tr>
                    <?php
                      $i++;
                      $id++;
                      $jumlahDokumenTeknis++;
                    }
                    ?>
            <?php 
            } // end of if ($reqMetodeLelang != '1' || $reqMetodeLelang; != '7') {  
            ?> 

            <?php
              if($reqSistemSampul == "1")
              {
                  ?>
                  <tr class="gelap" style="background-color: #b7b7b7; color: #000">
                      <!-- <td>III</td> -->
                      <td colspan="5">Dokumen Harga</td>
                  </tr>
                  <?php
                  $i=1;
                  $jumlahDokumenHarga = 0;
                  $jumlahUploadHarga = 0;
                  while($paket_evaluasi_harga_tawar->nextRow())
                  {
                  ?>
                  <tr class="terang">
                    <td style="width: 5px"><?=$i?>.</td>
                    <td> <?=$paket_evaluasi_harga_tawar->getField("NAMA")?>  <?php if($paket_evaluasi_harga_tawar->getField("WAJIB") == '1'){ ?> <font color="#FF0000">* </font><?php } ?></td>
                    <!-- <td align="center" width="7%"> <small><?=round($paket_evaluasi_harga_tawar->getField("UKURAN") / 1024, 2)?>Kb </small></td>
                    <td align="center" width="10%"> <small><?=($paket_evaluasi_harga_tawar->getField("TANGGAL_UPLOAD"))?></small> </td> -->
                    <td align="center" class="kolom-aksi">
                    <?php
                    if($paket_evaluasi_harga_tawar->getField("PAKET_DOKUMEN_ID") == "")
                    { echo "-";
                    }
                    else
                    {
                    ?>
                        <a href="uploads/penawaran/<?=$paket_evaluasi_harga_tawar->getField("PATH_FILE")?>" target="_blank"><img src="images/icon-download.png" alt="" width="16" height="16" border="0" />
                      </a>
                        <?php
                        if($kirimPenawaran == "1")
                        {}
                        else
                        {
                        ?>
                         -
                        <a onClick="deleteData('dokumen_pengadaan_upload_rekanan/delete_dokumen/', '<?=$paket_evaluasi_harga_tawar->getField("PAKET_DOKUMEN_ID")?>')"><img src="images/button_cancel.png" alt="" width="16" height="16" border="0" /></a>
                        <?php
                        }
                        $jumlahUploadHarga++;
                        ?>
                        <br>
                        <small class="badge badge-info" style="font-size: 9px"><?=round($paket_evaluasi_harga_tawar->getField("UKURAN") / 1024, 2)?> Kb </small>
                        <small class="badge badge-info" style="font-size: 9px"><?=($paket_evaluasi_harga_tawar->getField("TANGGAL_UPLOAD"))?></small>
                    <?php
                    }
                    ?>
                    </td>
                  </tr>
                  <?php
                    $i++;
                    $id++;
                    $jumlahDokumenHarga++;
                  }
              }
            ?>

            </table> 
          <div class="form-actions"> 
            <a href="main/index/paket_detil/?reqId=<?= $reqId ?>" class="<?= CLASS_BTN_DANGER ?> mr-1"> <?= BTN_KEMBALI ?> </a> 
            <?php 
            if ($reqLulus == '1') {
             ?>
            <a onClick="return showChat()" class="<?= CLASS_BTN_PRIMARY ?>" style="cursor:pointer"> <i class="fa fa-comment"></i> Chat Evaluasi Teknis </a>
            <?php 
            } ?>
          </div> 
        </div>
      </div>
    </div>
  </div> 
</div>   

<script src="<?=base_url()?>assets/new/js/core/libraries/jquery_ui/jquery-ui.min.js"></script>

<script type="text/javascript">
  function myFunction(a,b) {
    var id = "myPass"+a;
    var copyText = document.getElementById(id);
    copyText.select();
    copyText.setSelectionRange(0, 99999)
    document.execCommand("copy");
    alertSuccess2("Password "+b+" Berhasil disalin. Silahkan unduh dokumen dan paste password");
  }

function showChat() {
    $('#qnimate').show("slow");  
}
$(function(){
  $("#removeClass").click(function () {
    $('#qnimate').slideToggle("slow");
  });
});
$(document).ready(function() {
  window.onload=function(){
    showChat();
    $('#loading').hide();
  }; 
  
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
        // $('.popup-messages').scrollTop($('.direct-chat-messages').outerHeight());    
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
    $.getJSON("chat_json/chatNegoBox?reqId=<?= $reqId ?>&reqJenis=1&reqRekananId="+rekananid, function(data) {
      $('.direct-chat-messages').html(data);
      // if (data == 'Tidak ada pesan') {} else { $('#qnimate').show("slow");  }
      // if (data == 'Tidak ada pesan') {} else { alertSuccess2('Silahkan cek chat evaluasi teknis'); } 
    });
  }
} 

</script>

<div class="popup-box chat-popup" id="qnimate">
  <div class="popup-head">
    <div class="popup-head-left pull-left">  Chat Evaluasi Teknis</span>
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
      <input type="hidden" id="reqRekananId" name="reqRekananId" value="<?= $this->ID ?>">      
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
