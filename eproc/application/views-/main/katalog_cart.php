<?php
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 * 
 */

$this->libsession->cekSession();

include_once("functions/string.func.php");
include_once("functions/default.func.php");
include_once("functions/date.func.php");
$this->load->model("Paket");
$this->load->model("Katalogrekanan");

$paket = new Paket();
$katalogrekanan = new Katalogrekanan();
$katalogrekananRow = new Katalogrekanan();
$katalogrekananGroupPenyedia = new Katalogrekanan();

$reqId = $this->input->get("reqId");

$totalPenyedia = $katalogrekananGroupPenyedia->selectByParamsGroupByPenyedia(array()," AND A.PAKET_ID = '".$reqId."'");
// echo $totalPenyedia; die();

$paket->selectByParamsMonitoring(array("A.PAKET_ID" => coalesce($reqId, 0)));
$paket->firstRow();
$multi_pemenang = $paket->getField("MULTI_PEMENANG");
$ppk = $paket->getField("PPK");

  // echo '---'.$this->USER_LOGIN_ID;
  if ($paket->getField("USER_LOGIN_ID") != $this->USER_LOGIN_ID && $ppk != $this->USER_LOGIN_ID) {
    redirect(base_url('main'));
  }

  $reqMetodePengadaan = $paket->getField("PAKET_METODE_LELANG_ID");
  $reqMetodeKualifikasi = $paket->getField("PAKET_METODE_KUALIFIKASI_ID");
  $reqMetodeEvaluasi = $paket->getField("PAKET_METODE_EVALUASI_ID");
  $reqJenisPekerjaan = $paket->getField("PAKET_JENIS_ID");
  $reqJenisPekerjaanStr = $paket->getField("PAKET_JENIS");
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
  $reqUUID = $paket->getField("PAKET_UUID");

  if ($reqId == '' || $reqMetodePengadaan != '6')
    redirect(base_url('main'));

  $katalogrekanan->selectByParams(array('A.PAKET_ID' => $reqId));
  $katalogrekananRow->selectByParams(array('A.PAKET_ID' => $reqId));
  $katalogrekananRow->firstRow();
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
  } else if (status === 2) {
    var alertMessage = 'Apakah pembelian selesai?';
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
</script>

<div class="row">
  <div class="col-md-12 col-sm-12">
    <div class="card">
      <div class="card-header card-head-inverse bg-primary">
        <h4 class="card-title text-white">Daftar Produk</h4>
        <a class="heading-elements-toggle"><i class="fa fa-ellipsis-v font-medium-3"></i></a>
        <div class="heading-elements">
            <ul class="list-inline mb-0">
              <li><a data-action="expand"><i class="ft-maximize"></i></a></li>
              <!-- <li><a data-action="close"><i class="ft-x"></i></a></li> -->
            </ul>
        </div>
      </div>
      <div class="card-content collapse show border-info border-darken-2">
        <div class="card-body">
          <?php
          if ($totalPenyedia > 1) { ?>
            <div class="alert alert-danger">
              <button type="button" class="close" data-dismiss="alert">&times;</button>
              <b><u>Tidak boleh ada <?= $totalPenyedia ?> penyedia dalam Pembelian Langsung, hanya diperbolehkan ada 1 penyedia, silahkan hapus penyedia. </u></b>
            </div>
          <?php
          } ?>

          <form id="ff" class="easyui-form form-horizontal" method="post" novalidate enctype="multipart/form-data">

            <div id="invoice-template" class="card-body">
              <!-- Invoice Company Details -->
              <div id="invoice-company-details" class="row">
                <div class="col-md-6 col-sm-12 text-md-left">
                  <div class="media">
                    <div class="media-body">
                      <ul class="px-0 list-unstyled">
                        <li class="text-bold-900"><h3><?=$reqNamaPaket?></h3></li>
                        <li><?=$reqMataUang.' '.numberToIna($reqNilaiPekerjaan)?></li>
                        <li><?= $reqLokasiPekerjaan ?></li>
                        <li><?= $paket->getField("PAKET_JENIS").' '.$paket->getField("METODE_LELANG") ?></li>
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
                          <th class="text-right">Harga Satuan</th>
                          <th class="text-center">Qty</th>
                          <th class="text-right">Total</th>
                          <?php
                          if ($katalogrekananRow->getField('STATUS') == '0' || $katalogrekananRow->getField('STATUS') == '1' ) {  ?>
                          <th class="text-right" width="2%"></th>
                          <?php
                          } ?>
                        </tr>
                      </thead>
                      <tbody>
                        <?php
                        $no=1;
                        $totalBayar=0;
                        while($katalogrekanan->nextRow())
                        {
                          $totalBayar += $katalogrekanan->getField('QTY') * $katalogrekanan->getField('HARGA');
                        ?>
                        <tr>
                          <th scope="row"><?=$no?></th>

                          <td>
                            <?php
                            if ($totalPenyedia > 1) { ?>
                              <span class="badge badge-danger"><?= $katalogrekanan->getField('USER_NAMA') ?></span><br>
                            <?php
                            } ?>
                            <a onClick="openAdd('main/loadUrl/main/katalog_validasi_rekanan_detail_produk?reqId=<?= $katalogrekanan->getField("KATALOGID") ?>');">
                              <?= $katalogrekanan->getField('NAMAPRODUK') ?>
                            </a>
                          </td>

                          <td class="text-right">
                            <?= number_format($katalogrekanan->getField('HARGA'),2,',','.') ?>
                          </td>

                          <td class="text-center" width="100px">
                            <?php
                            if ($katalogrekananRow->getField('STATUS') == '0') {
                              if ($ppk != $this->USER_LOGIN_ID) { 
                              // 0 : masih proses 1 : Negosiasi penyedia 2 : penyedia setuju 3 close
                            ?>
                              <input type="number" class="form-control" name="reqQty"
                                onkeypress="return isNumberKey(event)"
                                maxlength="5" style="width: 100px"
                                value="<?= $katalogrekanan->getField('QTY') ?>"
                                data-katalog="<?= $katalogrekanan->getField('KATALOGID') ?>"
                                data-paket="<?= $katalogrekanan->getField('PAKET_ID') ?>"
                                required onChange="cartupdate(this)" >
                            <?php
                              } else {
                                echo $katalogrekanan->getField('QTY');
                              }
                            } else { echo $katalogrekanan->getField('QTY'); } ?>
                          </td>

                          <td class="text-right">
                            <?= number_format(($katalogrekanan->getField('QTY') * $katalogrekanan->getField('HARGA')),2,',','.') ?>
                          </td>

                          <?php
                          if ($katalogrekananRow->getField('STATUS') == '0' || $katalogrekananRow->getField('STATUS') == '1' ) {  
                          ?>
                          <td class="text-right" style="width: 10px">
                            <?php 
                            if ($ppk != $this->USER_LOGIN_ID) { 
                            ?>
                            <a onclick="delcart('<?= $katalogrekanan->getField('KATALOGREKANANID').'||'.$katalogrekanan->getField('PAKET_ID') ?>')"><span class="fa fa-trash"></span>
                            </a>
                            <?php 
                            } ?>
                          </td>
                          <?php
                          } ?>

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
                  $selisih = $reqNilaiPekerjaan - $totalBayar;
                  if ($selisih < 0) {
                   echo '<div class="col-md-12 col-sm-12 text-center text-md-left">
                          <div class="alert alert-danger">
                            <b><u>Pembelian melebihi anggaran '.numberToIna($selisih).' </u></b>
                          </div>
                         </div>  ';
                  } ?>
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
                            <td class="text-bold-800"></td>
                            <td class="text-bold-800 text-right">
                              <?php
                              if ($selisih < 0) {  ?>
                                <h4><b style="color: red;text-decoration: line-through;"><?= number_format($totalBayar,2,',','.') ?></b></h4>
                              <?php
                              } else { ?>
                                <h4><b><?= number_format($totalBayar,2,',','.') ?></b></h4>
                              <?php
                              } ?>
                              <span style="color: red; font-size: 10px"><i>Harga Belum/Sudah termasuk Ongkos Kirim</i></span>
                            </td>
                            <td width="2%"></td>
                          </tr>
                        </tbody>
                      </table>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Invoice Footer -->
              <!-- <div id="invoice-footer">
                <div class="row">
                  <div class="col-md-7 col-sm-12">
                    <h6>Terms & Condition</h6>
                    <p>You know, being a cartupdate pilot isn't always the healthiest business in the world. We predict too much for the next year and yet far too little for the next 10.</p>
                  </div>
                </div>
              </div> -->
              <!--/ Invoice Footer -->

            </div>


            <div class="form-actions">
              <input type="hidden" name="reqId" value="<?=$reqId?>">
              <input type="hidden" name="reqBahasa" value="ID">
              <?php 
              if ($ppk != $this->USER_LOGIN_ID) { 
              ?>
              <a href="<?php if($reqId == "") { ?>main/index/paket_lelang<?php } else { ?>main/index/paket_detil/?eid=<?=$reqId?>&key=<?=$reqUUID?><?php } ?>" class="<?= CLASS_BTN_DANGER ?> mr-1 text-white"> <i class="fa fa-arrow-left"></i> Kembali </a>
              <?php 
              } else 
              {
              ?>
              <a href="<?php if($reqId == "") { ?>main/index/paket_lelang<?php } else { ?>main/index/paket_detil_kontrak/?reqId=<?=$reqId?><?php } ?>" class="<?= CLASS_BTN_DANGER ?> mr-1"> <i class="fa fa-arrow-left"></i> Kembali </a> 
              <?php 
              } ?>

              <!-- <button type="submit" class="btn btn-primary mr-1"><i class="fa fa-check-square-o"></i> Simpan</button> -->
              <?php
              if ($ppk != $this->USER_LOGIN_ID) 
              { 
                if ($selisih >= 0) {
                  switch ($katalogrekananRow->getField('STATUS')) {
                    case '0':
                      if ($totalPenyedia > 1) {} else {
                        echo '<a style="color:#fff" onclick="statusupdate(this)" class="'.CLASS_BTN_INFO.' mr-1"
                                data-katalogrekanan="'.$katalogrekananRow->getField('KATALOGREKANANID').'"
                                data-status="0"
                                data-katalog="'.$katalogrekananRow->getField('KATALOGID').'"
                                data-paket="'.$katalogrekananRow->getField('PAKET_ID').'"> <i class="fa fa-money"></i>
                                Lakukan Negosiasi dengan Penyedia
                              </a>';
                      }
                      break;
                    case '1':
                      echo '<a href="'.base_url('main/index/katalog_negosiasi?reqId='.$reqId).'" class="'.CLASS_BTN_INFO.' mr-1">
                              <i class="fa fa-gavel"></i> Negosiasi
                            </a>';
                      echo '<span class="badge badge-danger">Proses Negosiasi, Menunggu Persetujuan Negosiasi dengan Penyedia</span>';
                      break;
                    case '2':
                      // echo '<a style="color:#fff" onclick="statusupdate(this)" class="btn btn-dark mr-1"
                      //         data-katalogrekanan="'.$katalogrekananRow->getField('KATALOGREKANANID').'"
                      //         data-status="2"
                      //         data-katalog="'.$katalogrekananRow->getField('KATALOGID').'"
                      //         data-paket="'.$katalogrekananRow->getField('PAKET_ID').'"> <i class="fa fa-close"></i>
                      //         Close Pembelian
                      //       </a>';
                      echo '<span class="badge badge-info">Penyedia telah Setuju dengan Negosiasi</span>';
                      break;

                    default:
                      echo '';
                      break;
                  }
                } 

                if ($katalogrekananRow->getField('STATUS') == '0' || $katalogrekananRow->getField('STATUS') == '') {  ?>
                  <a href="main/index/katalog_search/?reqId=<?=$reqId?>" class="<?= CLASS_BTN_SUCCESS ?> pull-right mr-1"> <i class="fa fa-search"></i> Cari Katalog</a>
                <?php
                } 
              }?>
            </div>

          </form>
        </div>
      </div>
    </div>
  </div>
</div>
