<?php
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 * 
 */

$this->libsession->cekSession();

// if($this->USER_TYPE_ID == "")
//     redirect("main");

include_once("functions/string.func.php");
include_once("functions/default.func.php");
include_once("functions/date.func.php");
$this->load->model("Paket");
$this->load->model("Katalogrekanan");
$this->load->model("Kataloglogistik");

$paket = new Paket();
$katalogrekanan = new Katalogrekanan();
$kataloglogistik = new Kataloglogistik();
$katalogrekananRow = new Katalogrekanan();
$katalogrekananGroupPenyedia = new Katalogrekanan();

$reqId = $this->input->get("reqId");


$paket->selectByParamsMonitoring(array("A.PAKET_ID" => coalesce($reqId, 0)));
$paket->firstRow();

$kataloglogistik->selectByParams(array("A.PAKET_ID" => coalesce($reqId, 0)));
$kataloglogistik->firstRow();
$kataloglogistikOngkosKirim = $kataloglogistik->getField('ONGKOS_KIRIM');

  $reqMetodePengadaan = $paket->getField("PAKET_METODE_LELANG_ID");
  $reqNamaPaket = $paket->getField("NAMA");
  $reqLokasiPekerjaan = $paket->getField("LOKASI");
  $reqNilaiPekerjaan = $paket->getField("NILAI");

  if ($reqId == '' || $reqMetodePengadaan != '6')
    redirect(base_url('main'));

  $katalogrekanan->selectByParams(array('A.PAKET_ID' => $reqId));
  $katalogrekananRow->selectByParams(array('A.PAKET_ID' => $reqId));
  $katalogrekananRow->firstRow();
  // echo $katalogrekananRow->getField('STATUS'); die();
  if ($katalogrekananRow->getField('STATUS') == '' || $katalogrekananRow->getField('STATUS') == '0')
    redirect(base_url('main'));

  if ($katalogrekananRow->getField('REKANAN_ID') != $this->ID)
    redirect(base_url('main'));
?>

<script type="text/javascript">
function delcart(z) {
  if (confirm('Apakah anda akan menghapus data ini?')) {
    var view = z;
    var a = view.split("||");
    var reqId = a[0];
    var paketid = a[1];
    $.post("katalog_json/delkatrek",
    {
      reqId: reqId,
      paketid: paketid
    },
    function(data, status){
        var str = data;
        var isNotif = str.split("||");
        if (isNotif[0] === 'Gagal') {
          alertError2(isNotif[1]);
        } else {
          alertSuccess2(isNotif[1]);
          location.reload();
        }
    });
  } else {
      // alert('Why did you press cancel? You should have confirmed');
      return false;
  }
}

function cartupdate(c) {
  var a = c.value;
  var katalog = $(c).data("katalog");
  var paket = $(c).data("paket");
  var qty = a;
  $.post("katalog_json/cartupdate",
    {
      katalog: katalog,
      paket: paket,
      qty: qty
    },
    function(data, status){
        var str = data;
        var isNotif = str.split("||");
        if (isNotif[0] === 'Gagal') {
          alertError2(isNotif[1]);
        } else {
          alertSuccess2(isNotif[1]);
        }
          setTimeout(function() {
            location.reload(); }, 1800);
    });
}

function statusupdate(c) {
  var katalog = $(c).data("katalog");
  var paket = $(c).data("paket");
  var katalogrekanan = $(c).data("katalogrekanan");
  var status = $(c).data("status");
  // alert(katalog+'-'+paket+'-'+katalogrekanan+'-'+status);
  if (status === 0) {
    var alertMessage = 'Apakah akan melakukan Negosiasi dengan Penyedia?';
  } else if (status === 1) {
    var alertMessage = 'Apakah anda setuju dengan hasil Negosiasi?';
  }
  if (confirm(alertMessage)) {
    $.post("katalog_json/statusupdate",
      {
        katalog: katalog,
        paket: paket,
        katalogrekanan: katalogrekanan,
        status: status
      },
      function(data, status){
          var str = data;
          var isNotif = str.split("||");
          if (isNotif[0] === 'Gagal') {
            alertError2(isNotif[1]);
          } else {
            alertSuccess2(isNotif[1]);
          }
          setTimeout(function() {
            location.reload(); }, 1800);
      });
  } else {
    return false;
  }
}

$(function(){
  $("#addClass").click(function () {
    // $('#qnimate').addClass('popup-box-on');
    $('#qnimate').slideToggle("slow");
  });

  $("#removeClass").click(function () {
    // $('#qnimate').removeClass('popup-box-on');
    $('#qnimate').slideToggle("slow");
  });
});

</script>

<style type="text/css">
  .card-text a {
    font-size: 11px;
  }
</style>

<section id="backColor">
  <div class="row">

    <div class="col-md-2 col-sm-12">
      <div class="card border-bottom-primary" style="zoom: 1;">
        <div class="card-body">
          <div class="card-text">
           <?php
            if($this->USER_TYPE_ID == "6") {
              // get Notification Penawaran
              $this->load->model("Katalog");
              $katalog = new Katalog();
              $statement = ' AND A.REKANAN_ID = '.$this->ID.' AND A.STATUS=\'1\' OR A.STATUS=\'3\' OR A.STATUS=\'4\' OR A.STATUS=\'5\' ';
              $totalPenawaran = $katalog->getCountByParamsPenawaran(array(), $statement);?>
              <a href="<?= base_url() ?>main/index/katalog_rekanan" class="btn btn-success btn-lg mb-1 text-left" style="width: 100%;"><i class="fa fa-cogs fa-lg pull-right"></i> Katalog</a>
              <a href="<?= base_url() ?>main/index/katalog_rekanan_add" class="btn btn-success btn-lg mb-1 text-left" style="width: 100%;"><i class="fa fa-plus fa-lg pull-right"></i> Tambah Katalog</a>
              <a href="<?= base_url() ?>main/index/katalog_penawaran" class="btn btn-success btn-lg mb-1 text-left" style="width: 100%; opacity: .3"><i class="fa fa-shopping-basket fa-lg pull-right"></i> Penawaran <?= '<span class="badge badge-danger" style="opacity: 1">'.$totalPenawaran.'</span>'; ?></a>
              <a href="<?= base_url() ?>main/index/katalog_pernyataan" class="btn btn-success btn-lg mb-1 text-left" style="width: 100%"><i class="fa fa-upload fa-lg pull-right"></i> Upload Pernyataan</a>
            <?php
            } ?>

            <?php
            if($this->USER_TYPE_ID == "1" || $this->USER_TYPE_ID == "2"){  // 1:admin, 2:validator?>
              <a href="<?= base_url() ?>main/index/katalog_rekanan" class="btn btn-success btn-lg mb-1 text-left" style="width: 100%; opacity: .3"><i class="fa fa-cogs fa-lg pull-right"></i> Katalog</a>
              <a href="<?= base_url() ?>main/index/katalog_validasi" class="btn btn-success btn-lg mb-1 text-left" style="width: 100%;"><i class="fa fa-edit fa-lg pull-right"></i> Validasi</a>
              <a href="<?= base_url() ?>main/index/katalog_laporan" class="btn btn-success btn-lg mb-1 text-left" style="width: 100%;"><i class="fa fa-flag fa-lg pull-right"></i> Laporan</a>
            <?php
            } ?>
          </div>
        </div>
      </div>
    </div>

    <div class="col-md-10 col-sm-12">
      <div class="card border-bottom-primary" style="zoom: 1;">
        <div class="card-header">
          <h4 class="card-title">Katalog <small> Negosiasi</small></h4>
          <div class="heading-elements" id="tombol">
            <span></span>
          </div>
        </div>
        <div class="card-body area-datatable">
          <div class="form-body">
            <?php
            if ($totalPenyedia > 1) { ?>
              <div class="alert alert-danger">
                <button type="button" class="close" data-dismiss="alert">&times;</button>
                <b><u>Tidak boleh ada <?= $totalPenyedia ?> penyedia dalam Pembelian Langsung, hanya diperbolehkan ada 1 penyedia, silahkan hapus penyedia. </u></b>
              </div>
            <?php
            } ?>

            <div id="invoice-template" class="card-body">
              <!-- Invoice Company Details -->
              <div id="invoice-company-details" class="row">
                <div class="col-md-6 col-sm-12 text-md-left">
                  <div class="media">
                    <div class="media-body">
                      <ul class="px-0 list-unstyled">
                        <li class="text-bold-900"><h3><?=$reqNamaPaket?></h3></li>
                        <li><?= $reqLokasiPekerjaan ?></li>
                      </ul>
                    </div>
                  </div>
                </div>
                <div class="col-md-6 col-sm-12 text-center text-md-right">
                  <?php
                  if ($katalogrekananRow->getField('STATUS') != '0') {
                    echo '<h3>#'.$katalogrekananRow->getField('NOINVOICE').'</h3>';
                  }
                  ?>

                    <ul class="px-0 list-unstyled">
                      <li class="text-bold-800"><h4><i><u><?= $katalogrekananRow->getField('USER_NAMA') ?></u></i></h4></li>
                      <li>NPWP: <?= $katalogrekananRow->getField('NPWP') ?></li>
                      <li><?= $katalogrekananRow->getField('ALAMAT').', '.$katalogrekananRow->getField('KOTA').' - '.$katalogrekananRow->getField('KODEPOS') ?></li>
                      <li><?= $katalogrekananRow->getField('EMAIL') ?></li>
                      <li><?= $katalogrekananRow->getField('TELEPON_KODE').' - '.$katalogrekananRow->getField('TELEPON') ?></li>
                    </ul>
                </div>
              </div>
              <!--/ Invoice Company Details -->

              <!-- Invoice Items Details -->
              <div id="invoice-items-details" class="pt-5">
                <div class="row">
                  <div class="table-responsive col-sm-12">
                    <table class="table">
                      <thead>
                        <tr>
                          <th width="2%">#</th>
                          <th width="58%">Produk</th>
                          <th class="text-right" width="20%">Harga Satuan</th>
                          <th class="text-right" width="25%">Harga Nego</th>
                          <th class="text-center">Qty</th>
                          <th class="text-right">Total Nego</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php
                        $no=1;
                        $totalBayar=0;
                        $totalBayarHargaAwal=0;
                        while($katalogrekanan->nextRow())
                        {
                          $totalBayar += $katalogrekanan->getField('QTY') * $katalogrekanan->getField('HARGA_NEGO');
                          $totalBayarHargaAwal += $katalogrekanan->getField('QTY') * $katalogrekanan->getField('HARGA');
                        ?>
                        <tr>
                          <th scope="row"><?=$no?></th>

                          <td>
                            <a onClick="openAdd('main/loadUrl/main/katalog_validasi_rekanan_detail_produk?reqId=<?= $katalogrekanan->getField("KATALOGID") ?>');">
                              <?= $katalogrekanan->getField('NAMAPRODUK') ?>
                            </a>
                          </td>

                          <td class="text-right">
                            <?php
                              echo number_format($katalogrekanan->getField('HARGA'),2,',','.'); ?>
                          </td>

                          <td class="text-right">
                            <?php  echo number_format($katalogrekanan->getField('HARGA_NEGO'),2,',','.'); ?>
                          </td>

                          <td class="text-center" width="40px">
                            <?php  echo $katalogrekanan->getField('QTY'); ?>
                          </td>

                          <td class="text-right">
                            <?= number_format(($katalogrekanan->getField('QTY') * $katalogrekanan->getField('HARGA_NEGO')),2,',','.') ?>
                          </td>

                        </tr>
                        <?php
                        $no++;
                        } ?>
                      </tbody>
                    </table>
                  </div>
                </div>
                <div class="row">
                  <?php
                  $selisih = $reqNilaiPekerjaan - ($totalBayar+$kataloglogistikOngkosKirim);
                  if ($selisih < 0) {
                   echo '<div class="col-md-12 col-sm-12 text-center text-md-left">
                          <div class="alert alert-danger">
                            <b><u>Pembelian melebihi anggaran '.numberToIna($selisih).' </u></b>
                          </div>
                         </div>  ';
                  } ?>

                  <?php
                  if ($katalogrekananRow->getField('STATUS') == '1' ) {
                    echo '<div class="col-md-12 col-sm-12 text-right">
                            <a href="'.base_url('main/index/katalog_negosiasi_rekanan/?reqId='.$reqId).'" class="'.CLASS_BTN_DANGER.'"><i class="fa fa-refresh"></i> Ambil Harga Negosiasi</a>
                         </div>  ';
                  }
                  ?>

                  <div class="col-md-7 col-sm-12 text-center text-md-left">
                    <p class="lead"><b>Payment Methods:</b></p>
                    <div class="row">
                      <div class="col-md-8">
                      <table class="table table-borderless table-sm">
                        <tbody>
                          <tr>
                            <td>Bank:</td><td class="text-right"><?= $katalogrekananRow->getField('NAMA') ?></td>
                          </tr>
                          <tr>
                            <td>No. Rekening:</td><td class="text-right"><?= $katalogrekananRow->getField('BANK_REKENING') ?></td>
                          </tr>
                          <tr>
                            <td>Atas Nama:</td><td class="text-right"><?= $katalogrekananRow->getField('BANK_PEMILIK') ?></td>
                          </tr>
                        </tbody>
                      </table>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-5 col-sm-12">
                    <p class="lead"><b>TOTAL</b></p>
                    <div class="table-responsive">
                      <table class="table">
                        <tbody>
                          <tr class="bg-grey bg-lighten-4">
                            <td class="text-bold-800">Total Negosiasi <br><span style="font-size: 10px;"><i><b>Belum Biaya Kirim</b></i></span></td>
                            <td class="text-bold-800 text-right">
                              <?php
                              if ($selisih < 0) {  ?>
                                <h4><b style="color: red;text-decoration: line-through;"><?= number_format($totalBayar,2,',','.') ?></b></h4>
                              <?php
                              } else { ?>
                                <h4><b><?= number_format($totalBayar,2,',','.') ?></b></h4>
                              <?php
                              } ?>
                            </td>
                          </tr>
                          <tr class="bg-grey bg-lighten-4">
                            <td class="text-bold-800">Total Harga Awal</td>
                            <td class="text-bold-800 text-right">
                                <h4><b><?= number_format($totalBayarHargaAwal,2,',','.') ?></b></h4>
                            </td>
                          </tr>
                          <tr class="bg-grey bg-lighten-4">
                            <td class="text-bold-800">
                              <?php
                              $hitungPersentaseNegoTotal = round((($totalBayarHargaAwal - $totalBayar) / $totalBayarHargaAwal) * 100,2);
                              if ($hitungPersentaseNegoTotal < 100) {
                                echo '<div style="color:red;"> Turun <span class="fa fa-chevron-down"></span> '.$hitungPersentaseNegoTotal.'%</div>';
                              } ?>
                            </td>
                            <td class="text-bold-800 text-right">
                                <h4><b><?= number_format(($totalBayar-$totalBayarHargaAwal),2,',','.') ?></b></h4>
                            </td>
                          </tr>
                          <tr class="bg-grey bg-lighten-4">
                            <td class="text-bold-800">Biaya Kirim</td>
                            <td class="text-bold-800 text-right">
                              <h4><b><?= number_format($kataloglogistikOngkosKirim,2,',','.') ?></b></h4>
                            </td>
                          </tr>
                          <tr class="bg-grey bg-lighten-4">
                            <td class="text-bold-800">Total Bayar <br><span style="font-size: 10px;"><i><b>Termasuk Ongkos Kirim</b></i></span></td>
                            <td class="text-bold-800 text-right">
                              <h4><b><?= number_format(($totalBayar+$kataloglogistikOngkosKirim),2,',','.') ?></b></h4>
                            </td>
                          </tr>
                        </tbody>
                      </table>
                    </div>
                  </div>
                </div>
              </div>
            </div>


            <div class="form-actions">
              <a href="main/index/katalog_penawaran" class="<?= CLASS_BTN_DANGER ?> mr-1"> <i class="fa fa-arrow-left"></i> Kembali </a>
              <!-- <button type="submit" class="'.CLASS_BTN_DANGER.' mr-1"><i class="fa fa-check-square-o"></i> Simpan</button> -->
              <?php
              if ($selisih >= 0) {
                switch ($katalogrekananRow->getField('STATUS')) {
                  case '0':
                    break;
                  case '1':
                    echo '<a id="addClass" class="'.CLASS_BTN_INFO.' mr-1" style="color:#fff">
                            <i class="fa fa-comment"></i> Chat Negosiasi
                          </a>';
                    if ($totalBayar > 0) {
                      if($totalBarang == $isiHargaNego) {
                    echo '<a style="color:#fff" onclick="statusupdate(this)" class="'.CLASS_BTN_PRIMARY.' mr-1"
                            data-katalogrekanan="'.$katalogrekananRow->getField('KATALOGREKANANID').'"
                            data-status="1"
                            data-katalog="'.$katalogrekananRow->getField('KATALOGID').'"
                            data-paket="'.$katalogrekananRow->getField('PAKET_ID').'"> <i class="fa fa-check-square-o"></i>
                            Setujui Negosiasi
                          </a>';
                      }
                    }
                    echo '<span class="badge badge-danger">Proses Negosiasi, Menunggu Persetujuan Negosiasi dengan Penyedia</span>';
                    break;
                  case '2':
                    echo '<a target="_blank" href="'.base_url('main/loadUrl/report/katalog_cetak_chat_pdf/?reqId='.$reqId).'" class="'.CLASS_BTN_PRIMARY.' mr-1"><i class="fa fa-print"></i> Cetak Chat Negosiasi</a>';
                    echo '<span class="badge badge-info">Penyedia telah Setuju dengan Negosiasi</span>';
                    break;
                  case '3':
                    echo '<a target="_blank" href="'.base_url('main/loadUrl/report/katalog_cetak_pdf/?reqId='.$reqId).'" class="'.CLASS_BTN_PRIMARY.' mr-1"><i class="fa fa-print"></i> Cetak Hasil Negosiasi</a>';
                    break;

                  default:
                    echo 'Negosiasi';
                    break;
                }
              }
              ?>

            </div>

          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<?php
if ($katalogrekananRow->getField('STATUS') == '1')
{ ?>
  <script type="text/javascript">
    $(document).ready(function() {
      $(function(){
        $('#ffnego').form({
          url:'<?= base_url('katalog_json/negoshoutbox') ?>',
          onSubmit:function(){
            $('#submitPesan').removeClass('fa fa-send');
            $('#submitPesan').addClass('fa fa-refresh');
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
    });
    function getChatNegoBox() {
      $('.popup-messages').scrollTop($('.direct-chat-messages').outerHeight());
      $.getJSON("katalog_json/chatNegoBox?reqId=<?= $reqId ?>", function(data) {
        $('.direct-chat-messages').html(data);
        // alert(data);
      });
    }
  </script>
<?php
} ?>

<div class="popup-box chat-popup" id="qnimate">
  <div class="popup-head">
    <div class="popup-head-left pull-left">  Chat Negosiasi
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
      <fieldset>
        <div class="input-group" style="padding: 5px">
          <input type="text" id="reqPesanNego" name="reqPesanNego" class="form-control easyui-validatebox" style="border-radius: 5px 0 0 5px;" placeholder="Tulis pesan disini..." required="">
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
