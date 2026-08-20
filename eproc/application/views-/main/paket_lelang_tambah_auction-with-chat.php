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

$(function(){
  $('#ffhargamaksimal').form({
    url:'bidding_json/updateHargaMaksimal',
    onSubmit:function(){
      return $(this).form('validate');
    },
    success:function(data){
      $('#btnTutup').click();
      var aa = $('#reqPenawaranHargaMaksimal').val();
      $('#harga-maksimal').text('Rp. '+aa);
      alertSuccess2(data);
      setTimeout(function() {
        location.reload();
      }, 2000);
    }
  });

});
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
          if ($cekAktif == '0') {
             echo '<div class="alert alert-danger" style="color:#fff">
                      <span style="color: #fff">
                        e-Reverse Auction belum dimulai <br>
                        Jadwal: '.getFormattedDate($paket_tahap_info->getField('TANGGAL_AWAL')).' '.addWIB($paket_tahap_info->getField("JAM_AWAL")).' s/d '.getFormattedDate($paket_tahap_info->getField('TANGGAL_AKHIR')).' '.addWIB($paket_tahap_info->getField("JAM_AKHIR")).'
                      </span>
                    </div>';
          }

          if ($aktif_auction2 == '0' && $cekAktif == '1') {
             echo '<div class="alert alert-info" style="color:#fff">
                      <span style="color: #fff">
                        Jadwal e-Reverse Auction sudah mulai .<br>
                        Jadwal: '.getFormattedDate($paket_tahap_info->getField('TANGGAL_AWAL')).' '.addWIB($paket_tahap_info->getField("JAM_AWAL")).' s/d '.getFormattedDate($paket_tahap_info->getField('TANGGAL_AKHIR')).' '.addWIB($paket_tahap_info->getField("JAM_AKHIR")).'
                      </span>
                    </div>';
          }

          if ($aktif_auction2 == '1' && $cekAktif == '1') {
             echo '<div class="alert alert-danger" style="color:#fff">
                      <span style="color: #fff">
                        Jadwal e-Reverse Auction sudah selesai .<br>
                        Jadwal: '.getFormattedDate($paket_tahap_info->getField('TANGGAL_AWAL')).' '.addWIB($paket_tahap_info->getField("JAM_AWAL")).' s/d '.getFormattedDate($paket_tahap_info->getField('TANGGAL_AKHIR')).' '.addWIB($paket_tahap_info->getField("JAM_AKHIR")).'
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
                        </tr>
                      </thead>
                        <tbody>
      	 	               <?php
                          $paket_rekanan = new PaketRekanan();
                          $paket_rekanan->selectUrutPenawaran(array("A.PAKET_ID" => $reqId, "A.LULUS_PENDAFTARAN" => 1, "A.KIRIM_PENAWARAN" => 1, "A.LULUS_PENAWARAN" => 1), -1, -1, "", "", " ORDER BY NILAI_PENAWARAN ASC, KIRIM_PENAWARAN_TANGGAL ASC ");
                      	   $urut=1;
                          while($paket_rekanan->nextRow())
                          {
                       		?>
                            <tr onClick="">
                              <td class="text-center"><?=$urut?></td>
                              <td style="display:none" id="urut<?=$paket_rekanan->getField("KODE_REKANAN")?>"><?=coalesce($paket_rekanan->getField("NILAI_URUT"), "-1")?></td>
                              <td>
                                <?php
                                if ($aktif_auction2 == '1' && $cekAktif == '1') {
                                  echo $paket_rekanan->getField("NAMA");
                                } else {
                                  echo $paket_rekanan->getField("KODE_REKANAN");
                                }
                                ?>
                              </td>
                              <td id="nilai<?=$paket_rekanan->getField("KODE_REKANAN")?>"><?=numberToIna($paket_rekanan->getField("NILAI_PENAWARAN"))?></td>
                            </tr>
                        	<?php
        			             $urut++;
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
                    echo '<span class="badge badge-danger" style="margin-bottom:1%">Penawaran Harga Maksilam belum ditentukan</span>';
                  } else {
                    if ($aktif_auction2 == '0' && $cekAktif == '1') {
                    ?>
                    <div class="alert alert-danger text-center">
                      <label id="defaultCountdown" style="display:none; font-size: 2.5em;font-family:'Futura-Condensed'">- : - : -</label>
                      <input type="button" id="btnMulai" class="<?= CLASS_BTN_PRIMARY ?> btn-sm" value="Start" onClick="mulaiBidding();">
                      <!-- <input type="button" id="btnReset" class="btn btn-warning btn-sm" value="Reset" onClick="resetBidding();"> -->
                    </div>
                    <?php
                    }
                  } ?>
                </div>
                <div class="col-md-12">
                  <div class="alert alert-info">
                    Harga Terendah
                    <div class="nilai">
                      <div class="nominal">Rp. <span id="hargaTerendah">-</span></div>
                        <div class="keterangan">
                          <div class="keterangan">Peserta <?= $paketInfo->metode_lelang_nama ?> :<br><span><span id="rekananTerendah">-</span></span></div>
                        </div>
                    </div>
                  </div>
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
                <div class="card mb-1 border-blue border-darken-1">
                  <div class="card-content">
                    <div class="p-1">
                      <div class="alert bg-primary alert-icon-left alert-arrow-left alert-dismissible mb-2" role="alert">
                        <span class="alert-icon"><i class="fa fa-th"></i></span>  <strong>Chat</strong>
                      </div>
                      <div class="table-responsive area-chatting">
                        <div class="panel panel-primary">
                          <div class="panel-body">
                             <script type="text/javascript">
                                                        var count = 0;
                                                        var files = 'lib/shoutbox2/';
                                                        var lastTime = 0;

                                String.prototype.lpad = function(length) {
                                  var padString = "0";
                                  var str = this;
                                  while (str.length < length)
                                    str = padString + str;
                                  return str;
                                }
                                  function prepare(response) {

                                    count++;
                                    var d = new Date();
                                    var mytime = String(d.getDate()).lpad(2) +'/'+String((Number(d.getMonth())+1)).lpad(2)+'/'+d.getFullYear()+ ' ' + String(d.getHours()).lpad(2)+':'+String(d.getMinutes()).lpad(2)+':'+String(d.getSeconds()).lpad(2);
                                    var string = '<div class="shoutbox-list" id="list-'+count+'">'
                                        + '<span class="shoutbox-list-time">'+mytime+'</span>'
                                        + '<span class="shoutbox-list-nick">'+response.nickname+':</span>'
                                        + '<span class="shoutbox-list-message">'+response.message+'</span>'
                                        +'</div>';
                                          if( typeof response.waktu === 'undefined' || response.waktu === null )
                                          {}
                                          else
                                            mytime = response.waktu;

                                          var string = '<li class="left clearfix">' +
                                          '    <div class="chat-body clearfix">' +
                                          '        <div class="waktu" style="text-align:right">'+mytime+'</div>' +
                                          '        <div class="data">' +
                                          '            <span class="nama">'+response.nickname+'</span>' +
                                          '            <span class="isi">'+response.message+'</span>' +
                                          '        </div>' +
                                          '    </div>' +
                                          '</li>';

                                    return string;
                                  }


                                  function success(response, status)  {
                                    if(status == 'success') {
                                      lastTime = response.time;
                                      $('#daddy-shoutbox-response').html('<img src="'+files+'images/accept.png" />');
                                      $('.chat').prepend(prepare(response));
                                      $('#btn-input').val('');
                                      $('#btn-input').focus();
                                      $('#list-'+count).fadeIn('slow');
                                      timeoutID = setTimeout(refresh, 3000);
                                    }
                                  }

                                  function validate(formData, jqForm, options) {
                                    if(($("#btn-input").val()) == '')
                                    {
                                      alert('Isi pesan terlebih dahulu.');
                                      return false;
                                    }

                                    $('#daddy-shoutbox-response').html('<img src="'+files+'images/loader.gif" />');
                                    clearTimeout(timeoutID);
                                  }

                                  function refresh() {
                                    $.getJSON("bidding_shoutbox_json/bidding_shoutbox/?reqId=<?=$reqId?>&action=view&time="+lastTime, function(json) {
                                      if(json.length) {
                                        for(i=0; i < json.length; i++) {
                                          $('.chat').prepend(prepare(json[i]));
                                        }
                                        var j = i-1;
                                        lastTime = json[j].time;
                                      }
                                      //alert(lastTime);
                                    });

                                    timeoutID = setTimeout(refresh, 3000);
                                  }

                                  // wait for the DOM to be loaded
                                  $(document).ready(function() {
                                      var options = {
                                        dataType:       'json',
                                        beforeSubmit:   validate,
                                        success:        success
                                      };
                                      $('#daddy-shoutbox-form').ajaxForm(options);
                                      timeoutID = setTimeout(refresh, 100);
                                  });
                            </script>

                              <ul class="chat">
                              </ul>
                          </div>
                          <!-- <div class="panel-footer"> -->
                          <div class="input-group">
                              <form id="daddy-shoutbox-form" action="bidding_shoutbox_json/bidding_shoutbox/?action=add" method="post" style="width:100%; clear:both">
                                  <input type="hidden" name="nickname" value="PANITIA" />
                                  <input type="hidden" name="reqId" value="<?=$reqId?>" readonly />
                                  <input type="hidden" name="reqHalaman" value="0" readonly />
                                  <input type="hidden" name="reqKode" value="0" readonly />
                                  <input type="submit" value="Submit"  style="display:none" />

                                  <!-- <input id="btn-input" type="text" name="message" class="form-control mt-1" placeholder="Ketik pesan..." style="float:left; width:calc(100% - 100px);" /> -->
                                  <div class="input-group mt-1">
                                    <input id="btn-input" type="text" name="message" class="form-control" placeholder="Ketik pesan..." />
                                    <div class="input-group-append">
                                        <button class="btn btn-primary" id="btn-chat" style="float:left; cursor: pointer;">Kirim</button>
                                    </div>
                                  </div>
                              </form>
                          </div>
                          <!-- </div> -->
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="form-actions">
                  <a href="main/index/paket_detil/?reqId=<?=$reqId?>" class="<?= CLASS_BTN_DANGER ?>"> <?= BTN_KEMBALI ?> </a>
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
            <button type="submit" class="btn btn-primary"><i class="fa fa-check-square-o"></i> Simpan</button>
            <button type="button" class="btn btn-danger" data-dismiss="modal" id="btnTutup">Tutup</button>
          </div>
        </form>
      </div>
      <div class="modal-footer">
      </div>
    </div>
  </div>
</div>

<script type="text/javascript" src="lib/shoutbox2/javascript/jquery.js"></script>
<script type="text/javascript" src="lib/shoutbox2/javascript/jquery.form.js"></script>
