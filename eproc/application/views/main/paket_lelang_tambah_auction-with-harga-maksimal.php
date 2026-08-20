<?php
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 * 
 */
date_default_timezone_set("Asia/Jakarta");
$reqId = $this->input->get("reqId");
$this->libsession->cekSession($reqId);

$this->load->library("kauth");  $userLogin = new kauth();
$this->load->model("PaketRekanan");
include_once("functions/string.func.php");
include_once("functions/date.func.php");
include_once("functions/default.func.php");
$this->load->model("PaketTahap");
$this->load->library("paketinfo"); $paketInfo = new paketinfo();

$paket_tahap_metode = new PaketTahap();
$paket_tahap = new PaketTahap();
$paket_tahap_info = new PaketTahap();


$arrAuction = NEGOSIASI;

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

$paket_tahap_info->selectByParamsJadwal(array("URUT" => $arrAuction[$jenis_tahap], "PAKET_ID" => $reqId, "TAMPILKAN" => 1));
$paket_tahap_info->firstRow();
// echo $aktif_auction2.'-'.$cekAktif;

$minutes_to_add = $paketInfo->bidding_menit;
$mulai_bidding = $paketInfo->bidding;
$todate = date('Y-m-d H:i:s');
// $time = new DateTime($paketInfo->bidding_mulai);
// $time->add(new DateInterval('PT' . $minutes_to_add . 'M')); // tambah menit
// $time_end_bidding = $time->format('Y-m-d H:i:s');
$time_end_bidding = $paketInfo->bidding_mulai;
$bidding = $paketInfo->bidding;

if ($todate > $time_end_bidding && $bidding == '1') {
  // $tutup_bidding = '1'.$todate.'>'.$time_end_bidding;
  $tutup_bidding = '1';
} else {
  $tutup_bidding = '0';
}
?>

<link rel="stylesheet" href="<?=base_url()?>css/core.css" type="text/css">

<script src="lib/countdown/js/jquery.min.js"></script>
<script src="<?=base_url()?>assets/new/js/core/libraries/jquery_ui/jquery-ui.min.js"></script>
<script src="lib/countdown/js/jquery.plugin.min.js"></script>
<script src="lib/countdown/js/jquery.countdown.js"></script>
<script>
$( document ).ready(function() {
  countDown();
  setInterval(function(){ start(); }, 1000);
});

function start()
{
  $.get( "bidding_json/ambil_nilai_terkecil/?reqId=<?=$reqId?>", function( data ) {
    $("#hargaTerendah").text(data.harga);
    $("#rekananTerendah").text(data.kode);
  }, "json" );

  $.getJSON('bidding_json/ambil_penawaran_rekanan/?reqId=<?=$reqId?>', function (data)
  {
    $.each(data, function (i, SingleElement) {
      $("#urut"+SingleElement.KODE_REKANAN).html(SingleElement.NILAI_URUT);
      $("#nilai"+SingleElement.KODE_REKANAN).html(SingleElement.NILAI_PENAWARAN);
      $("#reCalc").click();
    });
  });
}

function countDown()
{
  // $(function () {
    $.get( "bidding_json/ambil_jam/?reqId=<?=$reqId?>", function( data ) {
      if(data == "")
        $("#btnMulai").show();
      else
      {
        arrData = data.split("-");
        $("#defaultCountdown").show();
        $("#btnMulai").hide();
        var austDay = new Date();
        austDay = new Date(arrData[2], arrData[1] - 1, arrData[0], arrData[3], arrData[4], arrData[5]);
        $('#defaultCountdown').countdown('destroy')
        $('#defaultCountdown').countdown({until: austDay, format: 'HMS', onExpiry: liftOff, padZeroes:true});
        $("#defaultCountdown").text('Selesai');
      }
    });
  // });
}

function liftOff() {
  alert('Waktu e-Reverse Auction selesai.');
  location.reload();
}

function mulaiBidding()
{
  if(confirm("Mulai Proses Reverse Auction?"))
  {
    $.get( "bidding_json/mulai/?reqId=<?=$reqId?>", function( data ) {
      alert(data);
      location.reload();
      countDown();
    });
  }
}

function resetBidding()
{
  if(confirm("Reset bidding?"))
  {
    $.get( "bidding_json/mulai/?reqStatus=reset&reqId=<?=$reqId?>", function( data ) {
      alert(data);
      countDown();
    });
  }
}
</script>


<!-- RANK TABLE -->
<script type='text/javascript' src="<?=base_url()?>lib/rankTable/rankTable.js"></script>

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
</style>

<div class="row">
  <div class="col-md-12 col-sm-12">
    <div class="card">
      <div class="card-header card-head-inverse bg-primary">
        <h4 class="card-title text-white">e-Reverse Auction</h4>
        <a class="heading-elements-toggle"><i class="fa fa-ellipsis-v font-medium-3"></i></a>
        <div class="heading-elements">
            <ul class="list-inline mb-0">
              <a href="main/loadUrl/report/paket_cetak_auction_word/?reqPaketId=<?=$reqId?>" class="<?= CLASS_BTN_PRIMARY ?>"><span class="fa fa-print"></span> Cetak Laporan Word</a>
              <a href="<?= base_url('main/index/paket_lelang_tambah_auction/?reqId='.$reqId) ?>" class="hidden-xs <?= CLASS_BTN_INFO ?>"> <span class="fa fa-refresh"></span> Refresh</a>
              <li><a data-action="expand"><i class="ft-maximize"></i></a></li>
              <!-- <li><a data-action="close"><i class="ft-x"></i></a></li> -->
            </ul>
        </div>
      </div>
      <div class="card-content collapse show border-info border-darken-2">
        <div class="card-body area-datatable">
          <?php
          $paket = new Paket();
          $paket->selectByParams(array("A.PAKET_ID" => coalesce($reqId, 0)));
          $paket->firstRow();
          $reqBidingMenit = $paket->getField("BIDDING_MENIT");
          $reqBidding = $paket->getField("BIDDING");
          $reqBiddingMulai = $paket->getField("BIDDING_MULAI");
          // Parsing Tanggal Mulai
          $exBiddingMulai = explode(' ',$reqBiddingMulai);
          $exBiddingMulaiDate = explode('-',$exBiddingMulai[0]);

          $date = date_create($reqBiddingMulai);
          date_add($date, date_interval_create_from_date_string('-'.$reqBidingMenit.' minute'));
          $exBiddingMulaiTime = explode(':',date_format($date, 'H:i:s'));
          $realDateBidding = '<br> <u>Akan dimulai pada: '.getFormattedDate($exBiddingMulai[0]).' '.addWIB($exBiddingMulaiTime[0].':'.$exBiddingMulaiTime[1]).' selama '.$reqBidingMenit.' menit </u>';

          if ($cekAktif == '0') {
             echo '<div class="alert alert-danger" style="color:#fff">
                      <span style="color: #fff">
                        e-Reverse Auction belum dimulai <br>
                        Jadwal: '.getFormattedDate($paket_tahap_info->getField('TANGGAL_AWAL')).' '.addWIB($paket_tahap_info->getField("JAM_AWAL")).' s/d '.getFormattedDate($paket_tahap_info->getField('TANGGAL_AKHIR')).' '.addWIB($paket_tahap_info->getField("JAM_AKHIR")).''.$realDateBidding.'
                      </span>
                    </div>';
          }

          if ($aktif_auction2 == '0' && $cekAktif == '1') {
             echo '<div class="alert alert-info" style="color:#fff">
                      <span style="color: #fff">
                        Jadwal e-Reverse Auction sudah mulai .<br>
                        Jadwal: '.getFormattedDate($paket_tahap_info->getField('TANGGAL_AWAL')).' '.addWIB($paket_tahap_info->getField("JAM_AWAL")).' s/d '.getFormattedDate($paket_tahap_info->getField('TANGGAL_AKHIR')).' '.addWIB($paket_tahap_info->getField("JAM_AKHIR")).''.$realDateBidding.'
                      </span>
                    </div>';
          }

          if ($aktif_auction2 == '1' && $cekAktif == '1') {
             echo '<div class="alert alert-danger" style="color:#fff">
                      <span style="color: #fff">
                        Jadwal e-Reverse Auction sudah selesai .<br>
                        Jadwal: '.getFormattedDate($paket_tahap_info->getField('TANGGAL_AWAL')).' '.addWIB($paket_tahap_info->getField("JAM_AWAL")).' s/d '.getFormattedDate($paket_tahap_info->getField('TANGGAL_AKHIR')).' '.addWIB($paket_tahap_info->getField("JAM_AKHIR")).''.$realDateBidding.'
                      </span>
                    </div>';
          }
          ?>
            <div class="row">
              <div class="col-md-8">
                <div class="area-peserta">
                	<div class="alert alert-info"><i class="fa fa-users" aria-hidden="true"></i> Peserta  e-Reverse Auction <?= $paketInfo->metode_lelang_nama ?></div>
                    <div class="inner">
                    <table id="table" class="table table-hover table-striped" >
                      <thead>
                        <tr>
                          <th class="anim:update anim:number" style="width: 2%">Peringkat</th>
                          <th class="anim:update anim:sort anim:number text-center" style="display:none">Score</th>
                          <th class="anim:id">Peserta <?= $paketInfo->metode_lelang_nama ?></th>
                          <th class="anim:constant">Nilai Penawaran</th>
                          <!-- <th class="anim:constant">Chat</th> -->
                        </tr>
                      </thead>
                        <tbody>
      	 	               <?php
                          $paket_rekanan = new PaketRekanan();
                          $paket_rekanan->selectUrutPenawaran(array("A.PAKET_ID" => $reqId, "A.LULUS_PENDAFTARAN" => 1, "A.KIRIM_PENAWARAN" => 1, "A.LULUS_PENAWARAN" => 1), -1, -1, "", "", " ORDER BY NILAI_PENAWARAN ASC, KIRIM_PENAWARAN_TANGGAL ASC ");
                      	   $urut=1;
                          while($paket_rekanan->nextRow())
                          {
                            $timeGetNotif = 1500;

                       		?>
                            <tr onClick="">
                              <td class="text-center"><?=$urut?></td>
                              <td style="display:none" id="urut<?=$paket_rekanan->getField("KODE_REKANAN")?>"><?=coalesce($paket_rekanan->getField("NILAI_URUT"), "-1")?></td>
                              <td>
                                <?php
                                // if ($aktif_auction2 == '1' && $cekAktif == '1') {
                                  echo $paket_rekanan->getField("NAMA");
                                // } else {
                                  // echo "* * * *  * * * *  * * * *  * * * *";
                                  // echo $paket_rekanan->getField("KODE_REKANAN");
                                // }
                                ?>
                              </td>
                              <?php 
                                // if ($aktif_auction2 == '1' && $cekAktif == '1') {
                               ?>
                              <td id="nilai<?=$paket_rekanan->getField("KODE_REKANAN")?>"> 
                                <?php echo numberToIna($paket_rekanan->getField("NILAI_PENAWARAN"))?>
                              </td>
                              <?php 
                              // } else { ?>
                                <!-- <td id="nilai"> 
                                  * * *  * * *  * * *  * * *
                                </td> -->
                              <?php 
                              // } ?>
                              <!-- <td> 
                                <small onClick="return showChat('Chat dengan <?= $paket_rekanan->getField("KODE_REKANAN") ?>','<?= $paket_rekanan->getField("REKANAN_ID") ?>')" class="badge badge-primary" style="cursor:pointer"> <i class="fa fa-comment"></i> Chat Penyedia <span id="reqNotif<?= $paket_rekanan->getField("REKANAN_ID") ?>"></span>
                              </td> -->
                            </tr>

                            <script>
                              setInterval("getNotif(<?= '\''.$paket_rekanan->getField("KODE_REKANAN").'\''.','.'\''.$paket_rekanan->getField("REKANAN_ID").'\''  ?>);",<?= $timeGetNotif ?>);
                            </script>

                        	<?php
        			             $urut++;
                           $timeGetNotif = $timeGetNotif + 1000;
                          }
                        	?>
                          <tr style="display:none">
                            <td>2</td>
                            <td>-999999999999</td>
                            <td>Joe</td>
                            <td style="display:none"><span class="up" id="reCalc">&uarr;</span> <span class="down">&darr;</span></td>
                          </tr>
                        </tbody>
                    </table>
                  </div>
                </div>
              </div>
              <div class="col-md-4">
                <div class="col-md-12 text-center">
                  <?php
                  if ($paketInfo->penawaran_harga_maksimal == '') {
                    echo '<span class="badge badge-danger" style="margin-bottom:1%">Penawaran Harga Maksimal belum ditentukan</span>';
                  } else {
                    if ($aktif_auction2 == '0' && $cekAktif == '1') {
                    ?>
                    <div class="alert alert-danger text-center">
                      <label id="defaultCountdown" style="display:none; font-size: 2.5em;font-family:'Futura-Condensed'">- : - : -</label>
                      <!-- <input type="button" id="btnMulai" class="<?= CLASS_BTN_WARNING ?> btn-sm" value="S t a r t" onClick="mulaiBidding();" style="padding: 10px 10%;"> -->
                      <!-- <input type="button" id="btnReset" class="btn btn-warning btn-sm" value="Reset" onClick="resetBidding();"> -->
                    </div>
                    <?php
                    }
                  } ?>
                </div>
                <div class="col-md-12">
                  <?php 
                  // if ($aktif_auction2 == '1' && $cekAktif == '1') {
                 ?>
                  <div class="alert alert-info">
                    Harga Terendah
                    <div class="nilai">
                      <div class="nominal">Rp. <span id="hargaTerendah">-</span></div>
                        <div class="keterangan">
                          <div class="keterangan">Peserta <?= $paketInfo->metode_lelang_nama ?> :<br>
                            <?php 
                              // if ($aktif_auction2 == '1' && $cekAktif == '1') {
                             ?>
                               <span><span id="rekananTerendah">-</span></span>
                             <?php 
                             // } else {  
                             //  echo "* * * * * * * * * * * * * * * *";
                             //  } ?>
                           </div>
                        </div>
                    </div>
                  </div>
                  <?php 
                  // } ?>
                  <span class="pull-right"><small>
                    <?php
                    // if ($tutup_bidding == '0')
                    //{?>
                    <a data-toggle="modal" data-target=".bs-example-modal-lg" class="fa fa-edit badge badge-danger" style="color:#fff;font-weight: bold;"> Ubah </a>
                    <?php
                    //} ?>
                    PENAWARAN HARGA MAKSIMAL</small><br>
                    <h2 id="harga-maksimal"> Rp. <?php echo $paketInfo->penawaran_harga_maksimal ? numberToIna($paketInfo->penawaran_harga_maksimal) : '<span style="font-size:12px; color:red">belum ditentukan</span>'; ?></h2>
                    <small>HPS: <?php echo numberToIna($paketInfo->nilai_owner_estimate); ?></small>
                  </span>
                </div>
              </div>
              <div class="col-md-12"><hr></div>
              <div class="col-md-12 area-auction"> 
                <div class="form-actions">
                  <a href="main/index/klarifikasi_chat/?reqId=<?=$reqId?>" class="<?= CLASS_BTN_DANGER ?> mr-1"> <?= BTN_KEMBALI ?> </a>
                  <?php 
                    // $filepath = 'logs/Auction-log_' .$reqId.'_'.date('Y-m-d') . '.txt'; 
                    $filepath = 'logs/Auction-log_' .$reqId . '.txt'; 
                    if (file_exists($filepath)) { ?>
                    <a href="<?= $filepath ?>" target="_blank" style="cursor:pointer" class="<?= CLASS_BTN_DARK ?>"> <i class="fa fa-cogs"></i> View Log Penawaran </a>
                  <?php 
                  } ?>
                  <!-- <a href="main/index/paket_detil/?reqId=<?=$reqId?>" class="btn btn-info mr-1 text-white"><span class="fa fa-info"></span> Syarat dan Ketentuan </a>  -->
                </div>
              </div>
            </div>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
  <div class="modal-dialog modal-lg" role="document">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">PENAWARAN HARGA MAKSIMAL</h4>
      </div>
      <div class="modal-body">
        <form id="ffhargamaksimal" class="form-horizontal" role="form" method="post" novalidate style="padding:0 50px">
          <input type="hidden" name="reqId" value="<?=$reqId?>" readonly />
          <div class="form-group col-md-12 mb-2">
            <input type="text" name="reqPenawaranHargaMaksimal" value="<?=$paketInfo->penawaran_harga_maksimal?>" title="" class="form-control easyui-validatebox span9" id="reqPenawaranHargaMaksimal" OnFocus="FormatAngka('reqPenawaranHargaMaksimal')" OnKeyUp="FormatUang('reqPenawaranHargaMaksimal')" OnBlur="FormatUang('reqPenawaranHargaMaksimal')" required >
          </div>
          <div class="form-group col-md-12 mb-2">
            <button type="submit" class="<?= CLASS_BTN_PRIMARY ?>"><i class="fa fa-check-square-o"></i> Simpan</button>
            <button type="button" class="<?= CLASS_BTN_DANGER ?>" data-dismiss="modal" id="btnTutup">Tutup</button>
          </div>
        </form>
      </div>
      <div class="modal-footer">
      </div>
    </div>
  </div>
</div> 

<script type="text/javascript"> 

$(function(){
  $('#ffhargamaksimal').form({
    url:'bidding_json/updateHargaMaksimal',
    onSubmit:function(){
      return $(this).form('validate');
    },
    success:function(data){
      var aa = $('#reqPenawaranHargaMaksimal').val();
      $('#harga-maksimal').text('Rp. '+aa);
      alertSuccess2(data);
      $('#btnTutup').click();
      setTimeout(function() {
        location.reload();
      }, 1000);
    }
  });

});

function openAdd22(pageUrl) {
  // alert(pageUrl);
    $.get(pageUrl, function (data) {
      $("#isipop").html(data);
      $("#code").modal("show");
    });
}

</script> 

<div id="code" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
  <div class="modal-dialog modal-xl" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">CHAT ROOM</h4>
      </div>
      <div class="modal-body" id="isipop"></div>
      <div class="modal-footer"></div>
    </div>
  </div>
</div> 


<?= $this->libchat->auction($reqId); ?>


<!-- <script type="text/javascript" src="lib/shoutbox2/javascript/jquery.js"></script>
<script type="text/javascript" src="lib/shoutbox2/javascript/jquery.form.js"></script> -->
