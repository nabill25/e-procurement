<?php
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 *
 */
date_default_timezone_set('Asia/Jakarta');

include_once("functions/string.func.php");
include_once("functions/default.func.php");
include_once("functions/date.func.php");
$this->load->model("UnitKerja");
$this->load->model("Paket");
$this->load->model("PaketBidangUsaha");
$this->load->model("RekananKualifikasi");
$this->load->model("PermohonanPaket");
$this->load->model("PermohonanPaketFile");
$this->load->model(array("PaketPanitia","PaketTahap"));

$paket = new Paket();
$rekanan_kualifikasi = new RekananKualifikasi();

$reqId = $this->input->get("reqId");
$reqPermohonanId = $this->input->get("reqPermohonanId") ?: '0';

$paket_tahap_metode = new PaketTahap();
$paket_tahap = new PaketTahap();
$arrAuction = NEGOSIASI;
$jenis_tahap = $paket_tahap_metode->getJenisTahapById($reqId);
$paket_tahap->selectByParams(array("URUT" => $arrAuction[$jenis_tahap], "PAKET_ID" => $reqId, "TAMPILKAN" => 1));
$paket_tahap->firstRow();
$paket_tanggal_awal = $paket_tahap->getField("TANGGAL_AWAL");
$paket_tanggal_akhir = $paket_tahap->getField("TANGGAL_AKHIR2");
// Format YMD
if (strtotime($paket_tanggal_awal) >= strtotime(date('Y-m-d H:i:s')) ) {
  $exTglAwal = explode('-',datetimeToPage($paket_tahap->getField("TANGGAL_AWAL"), "date"));
  $exTglAwalYear = $exTglAwal[2];
  $exTglAwalMonth = $exTglAwal[1]-1;
  $exTglAwalDate = $exTglAwal[0];
} else {
  $exTglAwalYear = date('Y');
  $exTglAwalMonth = date('m')-1;
  $exTglAwalDate = date('d');
}

$exTglAkhir = explode('-',$paket_tahap->getField("TANGGAL_AKHIR"));
$exTglAkhirYear = $exTglAkhir[2];
$exTglAkhirMonth = $exTglAkhir[1]-1;
$exTglAkhirDate = $exTglAkhir[0];

$exJamAkhir = explode(':',$paket_tahap->getField("JAM_AKHIR"));

  $cek_paket_by_permohonan2 = new Paket();
  $cek_paket_by_permohonan2->selectByParams(array("A.PERMOHONAN_PAKET_ID" => $reqPermohonanId, "A.UNIT_KERJA_ID" => $this->UNIT_KERJA_ID));
  $cek_paket_by_permohonan2->firstRow();
  $reqId = ($reqId) ? $reqId : $cek_paket_by_permohonan2->getField("PAKET_ID");

  $paket->selectByParams(array("A.PAKET_ID" => coalesce($reqId, 0)));
  $paket->firstRow();

  $paket_panitia = new PaketPanitia();
  $idPanitia = $paket_panitia->getCountByParams(array("PAKET_ID" => $reqId, "NIP" => $this->NIP));

  $reqMetodePengadaan = $paket->getField("PAKET_METODE_LELANG_ID");
  $reqMetodeKualifikasi = $paket->getField("PAKET_METODE_KUALIFIKASI_ID");
  $reqMetodeEvaluasi = $paket->getField("PAKET_METODE_EVALUASI_ID");
  $reqJenisPekerjaan = $paket->getField("PAKET_JENIS_ID");
  $reqKualifikasiRekanan = $paket->getField("REKANAN_KUALIFIKASI_ID");
  $reqNamaPaket = $paket->getField("NAMA");
  $reqUraianKegiatan = $paket->getField("URAIAN");
  $reqLokasiPekerjaan = $paket->getField("LOKASI");
    $reqAlamatPanitia =  $paket->getField("ALAMAT");
  $arrTelp = explode(" ", trim($paket->getField("TELEPON")));
  $reqTelpPanitiaKode = $arrTelp[0];
  $reqTelpPanitia = $arrTelp[1];
  $reqEmailPanitia = $paket->getField("EMAIL");
  $reqNilaiPekerjaan = $paket->getField("NILAI");
  $reqPermohonanId = $paket->getField("PERMOHONAN_PAKET_ID");
  // Membatasi Pengadaan langsung <=300 juta
  if ($reqPermohonanId) {
    $permohonan_paket = new PermohonanPaket();
    $permohonan_paket->selectByParams(array("A.PERMOHONAN_PAKET_ID" => $reqPermohonanId, "A.UNIT_KERJA_ID" => $this->UNIT_KERJA_ID));
    $permohonan_paket->firstRow();
    $reqPL = $permohonan_paket->getField("PENGADAANLANGSUNG");
    $reqPermohonanKeterangan = $permohonan_paket->getField("KETERANGAN");
    $reqPermohonanNotaDinas = $permohonan_paket->getField("NOTA_DINAS");
    $reqPermohonanNoDisposisi = $permohonan_paket->getField("NO_PPA");
    $reqPermohonanTglDisposisi = $permohonan_paket->getField("TANGGAL");
    $reqKodeRUP = $permohonan_paket->getField("KODE_RUP");
    $reqKodePR = $permohonan_paket->getField("KODE_PR");
    if ($reqPL == '1') { // Pengadaan langsung <= 300jt
     $reqMetodePengadaan = '2';
    } else if ($reqPL == '2') { // ePurchasing Pejabat Pengadaan
     // $reqMetodePengadaan = '6';
    }
  }
  // End Membatasi Pengadaan langsung <=300 juta
  $reqPermohonan = $paket->getField("PERMOHONAN");
  $reqPermohonanNotaDinas = $paket->getField("PERMOHONAN_NOTA_DINAS");
  $reqMetodePenyampulan = $paket->getField("SISTEM_SAMPUL");
  $reqBahasa = $paket->getField("BAHASA");
  $reqMataUang = $paket->getField("NILAI_MATA_UANG");
  $reqBidingMenit = $paket->getField("BIDDING_MENIT");
  $reqBidding = $paket->getField("BIDDING");
  $reqBobotTeknis = $paket->getField("BOBOT_TEKNIS");
  $reqBobotHarga = $paket->getField("BOBOT_HARGA");
  $reqPassingGrade = $paket->getField("PASSING_GRADE");
  $reqMultiPemenang = $paket->getField("MULTI_PEMENANG"); // Untuk kontrak payung
  $reqBiddingMulai = $paket->getField("BIDDING_MULAI");

  if ($reqBidding == '1') {
    // Parsing Tanggal Mulai
    $exBiddingMulai = explode(' ',$reqBiddingMulai);
    $exBiddingMulaiDate = explode('-',$exBiddingMulai[0]);

    $date = date_create($reqBiddingMulai);
    date_add($date, date_interval_create_from_date_string('-'.$reqBidingMenit.' minute'));
    $exBiddingMulaiTime = explode(':',date_format($date, 'H:i:s'));
  } else {
    $reqBiddingMulai = $paket->getField("NEGOSIASI_MULAI");
    // Parsing Tanggal Mulai
    $exBiddingMulai = explode(' ',$reqBiddingMulai);
    $exBiddingMulaiDate = explode('-',$exBiddingMulai[0]);

    $exBiddingMulaiTime = explode(':',$exBiddingMulai[1]);
  }
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <base href="<?=base_url();?>" />
    
    <link rel="icon" href="../../favicon.ico">

    <title><?= SYSTEM_NAME.' | '.SYSTEM_NAME_PT ?></title>

    <link rel="stylesheet" type="text/css" href="<?=base_url() ?>assets/new/css/vendors.css">
    <link rel="stylesheet" type="text/css" href="<?=base_url() ?>assets/new/vendors/css/ui/prism.min.css">
    <!-- END VENDOR CSS-->
    <!-- BEGIN ROBUST CSS-->
    <link rel="stylesheet" type="text/css" href="<?=base_url() ?>assets/new/css/app.css">
    <!-- <link rel="stylesheet" type="text/css" href="<?=base_url() ?>css/core.css"> -->
    <!-- END ROBUST CSS-->
    <!-- BEGIN Page Level CSS-->
    <link rel="stylesheet" type="text/css" href="<?=base_url() ?>assets/new/css/core/menu/menu-types/horizontal-menu.css">
    <link rel="stylesheet" type="text/css" href="<?=base_url() ?>assets/new/css/core/colors/palette-gradient.css">
    <link rel="stylesheet" type="text/css" href="<?=base_url() ?>assets/new/css/core/colors/palette-callout.css">
    <!-- END Page Level CSS-->
    <!-- BEGIN Custom CSS-->
    <link rel="stylesheet" type="text/css" href="<?=base_url() ?>assets/new/css/style.css">
    
    <!-- FONT AWESOME -->
    <link rel="stylesheet" href="lib/font-awesome-4.7.0/css/font-awesome.css" type="text/css">
    
    <script type="text/javascript" src="js/jquery-1.6.1.min.js"></script>
    <!-- PAGINATION -->
    <link rel="stylesheet" type="text/css" href="lib/drupal/index.css" /> 
     <script src="lib/emodal/eModal.js"></script>

<script>
function openAdd(pageUrl) {
    eModal.iframe(pageUrl, 'Eprocurement | <?= SYSTEM_NAME_PT ?>')
}

function closePopup() {
  eModal.close();
}

function closePopupReload() {
  eModal.close();
  location.reload();
}
</script>   
  <script type="text/javascript">
    $(function(){
      $('#ff').form({
        url:'paket_json/addEditNegosiasi',
        onSubmit:function(){
          return $(this).form('validate');
        },
        success:function(data){
          $.messager.alert('Info', data, 'info');
          setTimeout(function () {
            window.top.location.reload();
          }, 1000);
        }
      });
    });
 
function createRowNotaDinas()
{
  $(function () {
    $.get("main/loadUrl/main/permohonan_lelang_add_nota_dinas_template", function (data) {
      $("#tbodyPermohonanPaketFile").append(data);
    });
  });
}

$('#reqMetodeEvaluasi')
.on('change', function(){
    alert($('#reqMetodeEvaluasi option:selected').val());
});

</script>

</head>

<body class="body-popup"> 
  <div class="row">
    <div class="col-md-12 col-sm-12">
      <div class="card"> 
          <div class="card-body">
            <form id="ff" class="easyui-form form-horizontal" method="post" novalidate enctype="multipart/form-data"> 
   
              <?php
              // if ($reqPL != '2') { // Pembelian Langsung / Purchasing
              ?>
              <div class="row"> 
                <div class="form-group col-md-12 mb-2">
                  <label style="width: 100%">Sistem Negosiasi</label>
                  <select name="reqBidding" class="easyui-combobox span2" data-options="
                                          onSelect: function(rec){
                                              if(rec.value == '1')
                                              {
                                                  $('#tdBidingMenit').show();
                                                  $('#reqBidingMenit').validatebox({
                                                      required: true
                                                  });
                                                  $('#reqMultiPemenang').hide();
                                              }
                                              else
                                              {
                                                  $('#tdBidingMenit').hide();
                                                  $('#reqBidingMenit').validatebox({
                                                      required: false
                                                  });
                                                  $('#reqMultiPemenang').show();
                                              }

                                          }" style="width: 200%" required>
                  <option value="0" <?php if($reqBidding == "0") { ?> selected <?php } ?>>Chatting Nego</option>
                  <option value="1" <?php if($reqBidding == "1") { ?> selected <?php } ?>>e-Reverse Auction</option>
                  </select>
                </div>
                <div id="tdBidingMenit" <?php if ($reqBidding == '1') {} else { ?> style="display:none" <?php } ?> style="width: 100%;" >
                  <!-- <div class="row"> -->
                    <div class="form-group col-md-12 mb-2">
                      <label>Waktu Reverse Auction <small>(menit)</small></label>
                      <input name="reqBidingMenit" id="reqBidingMenit" class="form-control easyui-validatebox span1"
                        type="text" id="reqBidingMenit" value="<?=isset($reqBidingMenit)?$reqBidingMenit:''?>"
                        OnFocus="FormatAngka('reqBidingMenit')"
                        OnKeyUp="FormatUang('reqBidingMenit')"
                        OnBlur="FormatUang('reqBidingMenit')" maxlength="3"
                        <?php if($reqBidding == '1') { ?> required <?php } ?> style="width: 80px;" />
                    </div>
                  <!-- </div>  -->
                </div>
                <!-- <div class="form-group col-md-12 mb-2" id="reqMultiPemenang" <?php // if ($reqBidding == '0') {} else { ?> style="display:none" <?php // } ?>> -->
                  <!-- <label style="width: 100%">Pemenang lebih dari satu ?</label> -->
                    <!-- <input type="radio" name="reqMultiPemenang" title="Kontrak Payung" value="0" id=""  <?php // if($reqMultiPemenang == "0") { ?> checked="checked" <?php // } ?> /> Tidak &nbsp; -->
                    <!-- <input type="radio" name="reqMultiPemenang" title="Kontrak Payung" value="1" id=""  <?php // if($reqMultiPemenang == "1") { ?> checked="checked" <?php // } ?> /> Ya -->
                <!-- </div> -->
                <script>
                  $(document).ready(function() {
                    $('#reqTanggalMulai').datebox({
                      editable: false
                    });
                  });
                    $(function(){
                      $('#reqTanggalMulai').datebox().datebox('calendar').calendar({
                        validator: function(date){
                          var now = new Date();
                          var d1 = new Date(<?= $exTglAwalYear.','.$exTglAwalMonth.','.$exTglAwalDate; ?>);
                          var d2 = new Date(<?= $exTglAkhirYear.','.$exTglAkhirMonth.','.$exTglAkhirDate; ?>);
                          return d1<=date && date<=d2;
                        }
                      });
                    });
                </script>
                <div class="form-group col-md-12 mb-2">
                  <label>Tanggal Mulai </label><br>
                  <?php 
                  if ($exBiddingMulaiDate[0]) {
                    $tglBid = $exBiddingMulaiDate[2].'-'.$exBiddingMulaiDate[1].'-'.$exBiddingMulaiDate[0]; 
                  } else {
                    $tglBid = '';
                  } 
                  ?>
                  <input name="reqTanggalMulai" id="reqTanggalMulai" class="form-control easyui-datebox"
                    type="text" id="reqTanggalMulai" value="<?= $tglBid; ?>" style="width: 200%"/> 
                      <input name="reqJamSelesai" type="text" value="<?= $exBiddingMulaiTime[0] ?: $exJamAkhir[0] ?>" id="reqJamSelesai" size="2" maxlength="2" class="form-control" style="width: 50px; display: inline;"/>
                            :
                      <input name="reqMenitSelesai" type="text" value="<?= $exBiddingMulaiTime[1] ?: $exJamAkhir[1] ?>" id="reqMenitSelesai" size="2" maxlength="2" class="form-control" style="width: 50px; display: inline;"/>
                      <br><small>( tanggal ini sekaligus sebagai informasi kirim undangan via email ke penyedia )</small>
                    <br><br>
                  <span class="alert alert-danger mt-2">
                  <?php  
                  $paket_tanggal_awalEx = explode(' ',$paket_tanggal_awal);
                  $paket_tanggal_akhirEx = explode(' ',$paket_tanggal_akhir);

                  echo '<u><b>Maksimal Jadwal Negosiasi</b></u> : '.getFormattedDate($paket_tanggal_awalEx[0]).' '.$paket_tanggal_awalEx[1].' s/d '.getFormattedDate($paket_tanggal_akhirEx[0]).' '.$paket_tanggal_akhirEx[1] ?> 
                  </span>
                  <input class="form-control easyui-validatebox span1" type="hidden" value="<?= datetimeToPage($paket_tahap->getField("TANGGAL_AWAL"), "date") ?>"/> 
                  <input class="form-control easyui-validatebox span1" type="hidden" value="<?= $paket_tahap->getField("TANGGAL_AKHIR") ?>"/>
                </div>
              </div>
              <?php
              // } ?>

              <div class="form-actions">
                <input type="hidden" name="reqId" value="<?=$reqId?>">
                <input type="hidden" name="reqBahasa" value="ID"> 
                <button type="submit" class="<?= CLASS_BTN_PRIMARY ?> mr-1"><?= BTN_SIMPAN ?></button> 
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>


    <script src="lib/bootstrap/libs/jquery/1.12.4/jquery.min.js"></script>
    <script>window.jQuery || document.write('<script src="lib/bootstrap/assets/js/vendor/jquery.min.js"><\/script>')</script>
    <script src="lib/bootstrap/dist/js/bootstrap.min.js"></script>
    <link rel="stylesheet" type="text/css" href="<?=base_url()?>lib/eproc/themes/default/easyui.css">
    <script type="text/javascript" src="<?=base_url()?>lib/eproc/jquery.easyui.min.js"></script>
    <script type="text/javascript" src="<?=base_url()?>lib/eproc/kalender-easyui.js"></script>
    <script type="text/javascript" src="<?=base_url()?>lib/eproc/allfunc.js"></script>

  </body>
</html>
