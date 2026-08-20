<?php 
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 * 
 */

date_default_timezone_set("Asia/Jakarta");

if($this->USER_TYPE_ID == "")
    redirect(base_url('main'));

$this->load->model("PaketRekanan");
$this->load->model("PaketTahap");
$this->load->model("RekananPaketPenawaran");
$this->load->model("Kebijakan");
$this->load->library("paketinfo"); 
$paketInfo = new paketinfo();

$reqId = $this->input->get("reqId");
$arrAuction = NEGOSIASI;

$paket_rekanan = new PaketRekanan();
$paket_tahap_metode = new PaketTahap();
$paket_tahap = new PaketTahap();
$adaRekanan = $paket_rekanan->getCountByParams(array("A.PAKET_ID" => $reqId, "A.LULUS_PENDAFTARAN" => "1", "A.LULUS_PENAWARAN" => "1", "A.REKANAN_ID" => $this->REKANAN_ID));

if($adaRekanan == 0)
  redirect(base_url('main'));

$jumlahRekanan = $paket_rekanan->getCountByParams(array("A.PAKET_ID" => $reqId, "A.LULUS_PENDAFTARAN" => "1", "A.LULUS_PENAWARAN" => "1"));


$paket_rekanan->selectByParamsPaketLelangPengadaan(array("A.PAKET_ID" => $reqId, "A.REKANAN_ID" => $this->REKANAN_ID));
$paket_rekanan->firstRow();

$nilaiPenawaran = numberToIna($paket_rekanan->getField("NILAI_PENAWARAN"));
$kodeRekanan = $paket_rekanan->getField("KODE_REKANAN");
// echo $nilaiPenawaran; die;

$rekanan_paket_penawaran = new RekananPaketPenawaran();
$jumlahItemPenawaran = $rekanan_paket_penawaran->getCountByParams(array("PAKET_REKANAN_ID" => $paket_rekanan->getField("PAKET_REKANAN_ID")));

$paketInfo->getPaket($reqId); 

$jenis_tahap = $paket_tahap_metode->getJenisTahapById($reqId);
$aktif_auction = $paket_tahap->getCountByParamsAktif(array("URUT" => $arrAuction[$jenis_tahap], "PAKET_ID" => $reqId, "TAMPILKAN" => 1));
$aktif_auction2 = $paket_tahap->getCountByParamsBerlalu(array("URUT" => $arrAuction[$jenis_tahap], "PAKET_ID" => $reqId, "TAMPILKAN" => 1));
if($aktif_auction > 0 || $aktif_auction2 > 0 )
{
  $cekAktif = 1;
} else {
  $cekAktif = 0;
} 


$minutes_to_add = $paketInfo->bidding_menit;
$mulai_bidding = $paketInfo->bidding;
$reqUUID = $paketInfo->uuid;
$todate = date('Y-m-d H:i:s');
// $time = new DateTime($paketInfo->bidding_mulai);
// $time->add(new DateInterval('PT' . $minutes_to_add . 'M')); // tambah menit
// $time_end_bidding = $time->format('Y-m-d H:i:s');
$time_end_bidding = $paketInfo->bidding_mulai;
$bidding = $paketInfo->bidding;

if ($time_end_bidding != '' && $bidding == '1') {
  if ($todate > $time_end_bidding && $bidding == '1') {
    // $tutup_bidding = '1'.$todate.'>'.$time_end_bidding;
    $tutup_bidding = '1';
  } else {
    $tutup_bidding = '0';
  }
} else {
  $tutup_bidding = '0';
}
?>
<!-- <link rel="stylesheet" href="<?=base_url()?>css/core.css" type="text/css"> -->
<link rel="stylesheet" href="<?=base_url()?>css/core.css" type="text/css">

<style type="text/css">
@keyframes myanimation {
  0%   {
    -webkit-transform: scale(1);
    -moz-transform: scale(1);
    -o-transform: scale(1);
    -ms-transform: scale(1);
    transform: scale(1)
  }
  50%  {
    -webkit-transform: scale(2);
    -moz-transform: scale(2);
    -o-transform: scale(2);
    -ms-transform: scale(2);
    transform: scale(2)
  }
  100%  {
    -webkit-transform: scale(1);
    -moz-transform: scale(1);
    -o-transform: scale(1);
    -ms-transform: scale(1);
    transform: scale(1)
  }
}

@keyframes myanimation2 {
  0%   {
    -webkit-transform: rotate(-20deg);;
    -moz-transform: rotate(-20deg);;
    -o-transform: rotate(-20deg);;
    -ms-transform: rotate(-20deg);;
    transform: rotate(-20deg);
  }
  10%   {
    -webkit-transform: rotate(20deg);;
    -moz-transform: rotate(20deg);;
    -o-transform: rotate(20deg);;
    -ms-transform: rotate(20deg);;
    transform: rotate(20deg);
  }
  20%   {
    -webkit-transform: rotate(-20deg);;
    -moz-transform: rotate(-20deg);;
    -o-transform: rotate(-20deg);;
    -ms-transform: rotate(-20deg);;
    transform: rotate(-20deg);
  }
  30%   {
    -webkit-transform: rotate(20deg);;
    -moz-transform: rotate(20deg);;
    -o-transform: rotate(20deg);;
    -ms-transform: rotate(20deg);;
    transform: rotate(20deg);
  }
  40%   {
    -webkit-transform: rotate(-20deg);;
    -moz-transform: rotate(-20deg);;
    -o-transform: rotate(-20deg);;
    -ms-transform: rotate(-20deg);;
    transform: rotate(-20deg);
  }
  50%  {
    -webkit-transform: rotate(20deg);;
    -moz-transform: rotate(20deg);;
    -o-transform: rotate(20deg);;
    -ms-transform: rotate(20deg);;
    transform: rotate(20deg);
  }
  100%  {
    -webkit-transform: rotate(-20deg);;
    -moz-transform: rotate(-20deg);;
    -o-transform: rotate(-20deg);;
    -ms-transform: rotate(-20deg);;
    transform: rotate(-20deg);
  }
}
#key {
  animation-name: myanimation2;
  animation-duration: 1s;
  animation-iteration-count: infinite;
  /*-webkit-animation: pulse 0.8s ease infinite;
  -moz-animation: pulse 0.8s ease infinite;
  -ms-animation: pulse 0.8s ease infinite;
  -o-animation: pulse 0.8s ease infinite;
  animation: pulse 0.8s ease infinite;*/

}
</style>

<!-- <script src="<?=base_url()?>lib/countdown/js/jquery.min.js"></script> -->
<script src="<?=base_url()?>assets/new/js/core/libraries/jquery_ui/jquery-ui.min.js"></script>
<script src="<?=base_url()?>lib/countdown/js/jquery.plugin.min.js"></script>
<script src="<?=base_url()?>lib/countdown/js/jquery.countdown.js"></script>


<!-- RANK TABLE -->
<script type='text/javascript' src="lib/rankTable/rankTable.js"></script>
  
<style type='text/css'>
#rank table{
  width:100%;
}
#rank table thead th{
  color:#FFF;
  background:#18a9d8;
}
#rank table tbody td{
  background:#f2faff;
  padding:3px;
}
#rank table tr.user td{
  background: #d8b218;
  color:#FFF;
}
.nominal { font-size: 2em; font-weight: bold; margin-bottom: 5px }
#rekananTerendah { font-size: 1em; font-weight: bold; margin-bottom: 5px;}
.keterangan { text-align: right; }
#nilaiPenawaran, .fa-key { font-size:2em }
.keterangan { font-size: .9em }
</style>

<script>
$( document ).ready(function() {

  countDown();  
  setInterval(function(){ start(); }, 1000);
  
});

function start()
{
  // $.get( "bidding_json/ambil_nilai_terkecil/?reqId=<?=$reqId?>", function( data ) {
  //   $("#hargaTerendah").text(data.harga);
  //   if(data.kode == '<?=$kodeRekanan?>')
  //     $("#key").show();
  //   else
  //     $("#key").hide();
  // }, "json" );


  $.get( "bidding_json/ambil_nilai_terkecil/?reqId=<?=$reqId?>", function( data ) {
    $("#hargaTerendah").text(data.harga);
    if(data.kode == '<?=$kodeRekanan?>'){
      if (data.show == '1') {
        $("#key").show();
      } else {
        $("#key").hide();
      }
    } else {
      $("#key").hide();
    }
  }, "json" );
  

  $.get( "bidding_json/ambil_reset/?reqId=<?=$reqId?>", function( data ) {
    
    if(data == "1")
        countDown();    
  }); 
  
  variable = $("#reqPenawaran").attr("disabled");
  if( typeof variable === 'undefined' || variable === null )
    return;
  
  countDown();  
  
}
  
function countDown()
{
  if($('#defaultCountdown').text() == "0 : 0 : 0")
  {
    $("#reqPenawaran").prop("disabled", "disabled");  
    $("#submitPenawaran").prop("disabled", "disabled");
  }
  
  // $(function () {
    $.get( "bidding_json/ambil_jam/?reqId=<?=$reqId?>", function( data ) {
      if(data == "")
      {
        $("#reqPenawaran").prop("disabled", "disabled");
        $("#submitPenawaran").prop("disabled", "disabled");
      }
      else
      {
        $("#reqPenawaran").prop("disabled", "");
        $("#submitPenawaran").prop("disabled", "");
        arrData = data.split("-");
        $("#defaultCountdown").show();  
        var austDay = new Date();
        austDay = new Date(arrData[2], arrData[1] - 1, arrData[0], arrData[3], arrData[4], arrData[5]);
        $('#defaultCountdown').countdown('destroy')
        $('#defaultCountdown').countdown({until: austDay, format: 'HMS', onExpiry: liftOff, padZeroes:true});
      
        if($('#defaultCountdown').text() == "0 : 0 : 0")
        {
          $("#reqPenawaran").prop("disabled", "disabled");        
          $("#submitPenawaran").prop("disabled", "disabled");
        }
      }
    });
  // });
}

function liftOff() { 
  alert('Waktu e-Reverse Auction selesai.'); 
  location.reload();
  $("#reqPenawaran").prop("disabled", "disabled");    
  $("#submitPenawaran").prop("disabled", "disabled");
}

</script>
<script type="text/javascript">
$(document).ready(function() {
  
  // $(function(){
    $('#ff').form({
      url:'bidding_json/kirim_penawaran',
      onSubmit:function(){
        return $(this).form('validate');
      },
      success:function(data){
        $("#reqPenawaran").val("");
        arrData = data.split("-");
        if(arrData[0] == "1") {
          // $.messager.alert('Info', arrData[1], 'info'); 
         alertSuccess2(arrData[1]+' '+arrData[2]);
        } else if(arrData[0] == "0") {
         alertError2(arrData[1]);
        }

        $("#nilaiPenawaran").text(arrData[2]);
        return false;
      }
    });
    
  // });

  // $(function(){
    $('#ffRekanan').form({
      url:'bidding_json/kirim_penawaran_rincian',
      onSubmit:function(){
        return $(this).form('validate');
      },
      success:function(data){
        
        arrData = data.split("-");
        if(arrData[0] == "0")
          // $.messager.alert('Info', arrData[1], 'info'); 
         alertSuccess2(arrData[1]);
        else
        {
          $(".itemPenawaran").val("");
          $("#nilaiPenawaran").text(arrData[1]);
        }
          
        return false;
      }
    });
    
  // });
  
    
});

function summary()
{
  var reqTotal = 0;
   
  $("table input[id^=reqUnitPriceKoreksi]").each(function() {
    var txtQuantity = $(this).attr("id").replace("reqUnitPriceKoreksi", "reqQuantity");
    var txtJumlah = $(this).attr("id").replace("reqUnitPriceKoreksi", "reqJumlah");
    
    var jumlah = (Number(FormatAngkaNumber($(this).val())) * Number($("#"+txtQuantity).val()));
  
    reqTotal = reqTotal + jumlah;
  
    $("#"+txtJumlah).val(FormatCurrency(jumlah));
  
  });
  
  var reqPPN = Number(reqTotal) * Number(0.10);
  reqPPN = Math.round(reqPPN);
  var reqTotalPPN = reqTotal + reqPPN;
  
  $("#reqTotal").val(FormatCurrency(reqTotal));
  $("#reqPPN").val(FormatCurrency(reqPPN));
  $("#reqTotalPPN").val(FormatCurrency(reqTotalPPN));   

}
</script>

<div class="row"> 
  <div class="col-md-12 col-sm-12">
    <div class="card">
      <div class="card-header card-head-inverse bg-primary">
        <h4 class="card-title text-white">e-Reverse Auction</h4>
        <a class="heading-elements-toggle"><i class="fa fa-ellipsis-v font-medium-3"></i></a> 
        <div class="heading-elements"> 
            <ul class="list-inline mb-0">
              <a href="<?= base_url('main/index/auction_rekanan/?reqId='.$reqId) ?>" class="hidden-xs <?= CLASS_BTN_INFO ?>"> <span class="fa fa-refresh"></span> Refresh</a>
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
                        e-Reverse Auction belum dimulai, baca syarat dan ketentuan <a class="badge badge-primary" data-toggle="modal" data-target=".bs-example-modal-lg"> klik disini </a>
                      </span>
                    </div>'; 
          } 
          // echo 'Aktif Auction:'.$aktif_auction2.'<br> Cek Aktif:'.$cekAktif.'<br>tutup_bidding.:'.$tutup_bidding;
          // if ($aktif_auction2 == '0' && $cekAktif == '1') {
          if ($aktif_auction2 == '0' && $cekAktif == '1') {
             echo '<div class="alert alert-info" style="color:#fff">
                      <span style="color: #fff">
                        Jadwal e-Reverse Auction akan di mulai, baca syarat dan ketentuan <a class="badge badge-primary" data-toggle="modal" data-target=".bs-example-modal-lg"> klik disini </a>
                      </span>
                    </div>'; 
          } 

          if ($aktif_auction2 == '1' && $cekAktif == '1') {
             echo '<div class="alert alert-danger" style="color:#fff">
                      <span style="color: #fff">
                        Jadwal e-Reverse Auction sudah selesai. 
                      </span>
                    </div>'; 
          } 
          ?>
          <div class="row">
            <div class="col-md-4"> 
              <?php 
              // if ($aktif_auction2 == '0' && $cekAktif == '1') { 
              if ($aktif_auction2 == '0' && $cekAktif == '1' && $tutup_bidding == '0') { 
              // if ($tutup_bidding == '0') { 
              ?>
              <div class="alert alert-danger text-center">
                <label id="defaultCountdown" style="display:none; font-size: 2.5em;font-family:'Futura-Condensed'">- : - : -</label> 
              </div>
              <?php 
              } ?>
              <!-- <div class="alert alert-info text-center">
                <div class="nilai">
                  <div class="nominal"><?php // $jumlahRekanan?></div>
                </div>
                Jumlah Peserta
              </div>  -->
            </div>
            <div class="col-md-5"> 
              <div class="col-md-12">
                <div class="alert alert-info text-center"> 
                  Penawaran Anda : <br><label id="nilaiPenawaran"><?=$nilaiPenawaran?></label>
                </div>
              </div>
              <div class="col-md-12">
                <?php
                // if($jumlahItemPenawaran == 1)
                // {
                if ($aktif_auction2 == '0' && $cekAktif == '1' && $tutup_bidding == '0')
                // if ($tutup_bidding == '0') 
                {
                ?>                                
                <form id="ff" class="easyui-form form-horizontal" method="post" novalidate enctype="multipart/form-data">
                  <div class="input-group mt-1">
                    <input type="text" placeholder="ketikkan penawaran..." name="reqPenawaran" id="reqPenawaran"  OnFocus="FormatAngka('reqPenawaran')" OnKeyUp="FormatUang('reqPenawaran')" style="float:left; width:70%;">
                    <div class="input-group-append">
                      <input type="hidden" name="reqId" value="<?=$reqId?>">
                      <button id="submitPenawaran" class="btn btn-primary" type="submit" >Submit</button>
                    </div>
                  </div>
                </form>
                <?php
                }
                ?>
              </div>
            </div>
            <div class="col-md-3"> 
              <h4>Status Penawaran</h4>
              <div class="nominal text-center">
                <!-- <div class="ikon" id="key" style="display:none"><i class="fa fa-key" aria-hidden="true"></i></div> -->
                <div class="ikon" id="key" style="margin-top:-10px; display:none"><img src="<?= base_url('images/throphy.png') ?>" style="width: 110px;"></div>
              </div>
              <div class="keterangan">
                <div class="keterangan">Ikon trophy menunjukkan harga penawaran Anda adalah yang terendah *</div>
              </div>
            </div>
            <div class="col-md-12" style="color:red; margin-bottom: 1%;">
              <i>
                *Peserta dapat menekan tombol Refresh sebagai tanda dimulainya pemasukan penawaran harga secara berulang
              </i>
            </div>
            <div class="col-md-12 area-auction">
              <div class="form-actions">
                <a href="main/index/paket_detil/?eid=<?=$reqId?>&key=<?= $reqUUID ?>" class="<?= CLASS_BTN_DANGER ?> mr-1"> <i class="fa fa-arrow-left"></i> Kembali </a> 
                <a onClick="return showChat()" class="<?= CLASS_BTN_PRIMARY ?>" style="cursor:pointer"> <i class="fa fa-comment"></i> Chat </a>
              </div> 
            </div>
          </div>
        </div>
      </div>
    </div>
  </div> 
</div>  

<?php 
$kebijakan = new Kebijakan();
$kebijakan->selectByParams(array("A.JENIS" => 'REV_AUCTION'));
$kebijakan->firstRow();

 ?>
<div class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
  <div class="modal-dialog modal-lg" role="document">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title"><?= $kebijakan->getField('TITLE') ?></h4>
      </div>
      <div class="modal-body">
       <?= $kebijakan->getField('TEXT') ?><br><br>
      </div>
      <!-- <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div> -->
    </div> 
  </div>
</div>

<script type="text/javascript">
function showChat() {
    $('#qnimate').show("slow");  
}
$(function(){
  $("#removeClass").click(function () {
    $('#qnimate').slideToggle("slow");
  });
});
$(document).ready(function() {
    // $('#qnimate').show("slow");  
  $(function(){
    $('#ffnego').form({
      url:'<?= base_url('chat_json/negoshoutboxWithFile') ?>',
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
        $('#file-to-upload').val('');
        $('#file-status, #file-name').hide();
        // $('.popup-messages').scrollTop($('.direct-chat-messages').outerHeight());    
      }
    });
    
  });
  setInterval("getChatNegoBox();",3000); 
  // setInterval("getStatus();",1500); 

  $('#file-to-upload').on('change', function () {
    if (this.files.length > 0) {
        const fileName = this.files[0].name;
        $('#file-status').show();
        $('#file-name').text(fileName).show();
    } else {
        $('#file-status, #file-name').hide();
    }
                    });

});

function getChatNegoBox() {
  $('.popup-messages').scrollTop($('.direct-chat-messages').outerHeight());    
  var rekananid = $('#reqRekananId').val();
  if (rekananid) {
    $.getJSON("chat_json/chatNegoBox?reqId=<?= $reqId ?>&reqJenis=2&reqRekananId="+rekananid, function(data) {
      $('.direct-chat-messages').html(data);
    });
  }
} 

</script>

<script type="text/javascript" src="<?= base_url() ?>lib/shoutbox2/javascript/jquery.js"></script>
<script type="text/javascript" src="<?= base_url() ?>lib/shoutbox2/javascript/jquery.form.js"></script>

<style type="text/css">
input[type="file"] {
    /*display: none;*/
    width: 0px;
}

.file-upload {
  display: block;
  width: 50px;
  /*padding: 10px 16px 10px 56px;*/
  border: none;
  outline: none;
  margin-bottom: 10px;
  font: 16px/28px 'Open Sans','Helvetica Neue',Helvetica,sans-serif;
  color: #3f3f3f;
  font-weight: 300;
  /*background: #ececec;*/
  -webkit-border-radius: 0;
  cursor: pointer;
  text-align: center;
  /*background: url(http://cdn.sstatic.net/stackoverflow/img/favicon.ico?v=4f32ecc8f43d) 2% / 45px no-repeat #ececec;*/
}
</style>
<div class="popup-box chat-popup" id="qnimate" style="width: 430 !important;">
  <div class="popup-head">
    <div class="popup-head-left pull-left">  Chat</span>
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
      <input type="hidden" id="reqJenisChat" name="reqJenisChat" value="2">      
      <fieldset>
        <div class="input-group" style="padding: 5px"> 
            <label for="file-to-upload" class="file-upload"><span class="fa fa-paperclip"></span></label>
            <input type="file" name="reqLinkFile" id="file-to-upload" class="easyui-validatebox" validType="fileType['pdf','xls','xlsx']">
            <input type="text" id="reqPesanNego" required="" name="reqPesanNego" class="form-control easyui-validatebox" style="border-radius: 5px 0 0 5px;" placeholder="Tulis pesan disini...">
            <div class="input-group-append">
              <button class="btn btn-danger btn-search-x" type="submit"><span class="fa fa-send" id="submitPesan"></span></button>
            </div>
        </div>
        <span id="file-status" style="color:green; margin-left: 5px; display:none;">
          <i class="fa fa-check"></i> File terlampir
        </span>
      </fieldset>
    </form>
  <div class="btn-footer">
  </div>
  </div>
</div>   

