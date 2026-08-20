<?php
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 * 
 */
$reqId = $this->input->get("reqId");
$this->libsession->cekSessionKualifikasi($reqId);   

$this->load->library("paketinfo"); $paketInfo = new paketinfo();
$this->load->model("Paket");
$this->load->model("PaketTahap"); 
$this->load->model("PaketRekanan");
$this->load->model("PaketDokumen");
$this->load->model("PaketEvaluasiKualifikasi");

include_once("functions/string.func.php");
include_once("functions/date.func.php");
include_once("functions/default.func.php");

/* create objects */
$paket = new Paket();
$paket_keterangan = new Paket();  
$paket_rekanan = new PaketRekanan();
$paket_evaluasi_kualifikasi = new PaketEvaluasiKualifikasi(); 

$paket->selectByParamsMonitoring(array(), -1, -1, " AND PAKET_ID = '".$reqId."' ");
$paket->firstRow();
if ($paket->getField("PUBLISH_PAKET") == 0 && ($this->USER_TYPE_ID == '6' || $this->USER_TYPE_ID == '')) { // khusus PENYEDIA di cek 
  echo "Sorry tidak ada paket";
  exit();
}

$paket_evaluasi_kualifikasi->selectByParamsRekananDokumen($this->ID, array("A.PAKET_ID" => $reqId));

$paketInfo->getPaket($reqId);
$reqNama = $paketInfo->nama;
$reqSistemSampul = $paketInfo->sistem_sampul;
$reqMetodeLelang = $paketInfo->metode_lelang_id;
$reqUUID = $paketInfo->uuid;
 
?>
 
<div class="row">  
  <div class="col-md-12 col-sm-12">
    <div class="card">
      <div class="card-content collapse show border-info border-darken-2">
        <div class="card-body"> 
          <table class="table table-bordered"> 
            <tr class="gelap" style="background-color: #b7b7b7; color: #000">
                <td colspan="3">Dokumen Kualifikasi</td>
            </tr>
            <?php
            $i=1;
            while($paket_evaluasi_kualifikasi->nextRow())
            {
            ?>
            <tr class="terang">
              <td style="width: 5px"><?=$i?>.</td>
              <td> <?=$paket_evaluasi_kualifikasi->getField("NAMA")?>  <?php if($paket_evaluasi_kualifikasi->getField("WAJIB") == '1'){ ?> <font color="#FF0000">* </font><?php } ?></td>
              <td align="center" class="kolom-aksi" width="20%">
              <?php
              if($paket_evaluasi_kualifikasi->getField("PAKET_DOKUMEN_ID") == "")
              { echo "-";
              }
              else
              {
              ?>
                  <a href="uploads/penawaran/<?=$paket_evaluasi_kualifikasi->getField("PATH_FILE")?>" target="_blank">
                    <?= ICON_DOWNLOAD ?>
                </a> 
                  <br>
                  <small style="font-size: 9px"><?=getFormattedDateView($paket_evaluasi_kualifikasi->getField("TANGGAL_UPLOAD"))?></small> - 
                  <small style="font-size: 9px"><?=round($paket_evaluasi_kualifikasi->getField("UKURAN") / 1024, 2)?> Kb </small>
              <?php
              }
              ?>
              </td>
            </tr>
            <?php
              $i++;
              $id++;
            }
            ?>

            </table> 
          <div class="form-actions"> 
            <a href="main/index/paket_detil/?eid=<?= $reqId ?>&key=<?=$reqUUID?>" class="<?= CLASS_BTN_DANGER ?> mr-1"> <?= BTN_KEMBALI ?> </a> 
            <a onClick="return showChat()" class="<?= CLASS_BTN_PRIMARY ?>" style="cursor:pointer"> <i class="fa fa-comment"></i> Chat Pembuktian Kualifikasi </a>
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
    $.getJSON("chat_json/chatNegoBox?reqId=<?= $reqId ?>&reqJenis=3&reqRekananId="+rekananid, function(data) {
      $('.direct-chat-messages').html(data);
      // if (data == 'Tidak ada pesan') {} else { $('#qnimate').show("slow");  }
      // if (data == 'Tidak ada pesan') {} else { alertSuccess2('Silahkan cek chat evaluasi teknis'); } 
    });
  }
} 

</script>

<div class="popup-box chat-popup" id="qnimate">
  <div class="popup-head">
    <div class="popup-head-left pull-left">  Chat Pembuktian Kualifikasi</span>
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
