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
$kataloglogistikEstimasiSampai = $kataloglogistik->getField('ESTIMASI_SAMPAI');
$kataloglogistikFileSuratPesanan = $kataloglogistik->getField('FILE_SURAT_PESANAN');
$kataloglogistikPathFileSuratPesanan = $kataloglogistik->getField('PATH_FILE_SURAT_PESANAN');
$kataloglogistikFileBuktiKirim = $kataloglogistik->getField('FILE_BUKTI_KIRIM');
$kataloglogistikPathFileBuktiKirim = $kataloglogistik->getField('PATH_FILE_BUKTI_KIRIM');
$kataloglogistikPathFileBuktiTerima = $kataloglogistik->getField('PATH_FILE_BUKTI_TERIMA');

  $reqMetodePengadaan = $paket->getField("PAKET_METODE_LELANG_ID");
  $reqNamaPaket = $paket->getField("NAMA");
  $reqLokasiPekerjaan = $paket->getField("LOKASI");
  $reqNilaiPekerjaan = $paket->getField("NILAI");

  if ($reqId == '' || $reqMetodePengadaan != '6')
    redirect(base_url('app'));

  $katalogrekanan->selectByParams(array('A.PAKET_ID' => $reqId));
  $katalogrekananRow->selectByParams(array('A.PAKET_ID' => $reqId));
  $katalogrekananRow->firstRow();
  // echo $katalogrekananRow->getField('STATUS'); die();
  if ($katalogrekananRow->getField('STATUS') == '' || $katalogrekananRow->getField('STATUS') == '0')
    redirect(base_url('app'));

  if ($katalogrekananRow->getField('REKANAN_ID') != $this->ID)
    redirect(base_url('app'));

?>

<script type="text/javascript">

function statusupdate(c) {
  var katalog = $(c).data("katalog");
  var paket = $(c).data("paket");
  var katalogrekanan = $(c).data("katalogrekanan");
  var status = $(c).data("status");
  // alert(katalog+'-'+paket+'-'+katalogrekanan+'-'+status); return false;
  if (status === 0) {
    var alertMessage = 'Apakah akan melakukan Negosiasi dengan Penyedia?';
  } else if (status === 1) {
    var alertMessage = 'Apakah anda setuju dengan hasil Negosiasi?';
  } else if (status === 3) {
    var alertMessage = 'Proses Pesanan?';
  } else if (status === 4) {
    var alertMessage = 'Kirim Pesanan?';
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
  $('#ff').form({
    url:'<?= base_url('katalog_json/prosespesanan') ?>',
    onSubmit:function(){
      var v=$(this).form('validate');
      if(v) {
        showLoad();
        return v;
      } else {
        hideLoad();
        return false;
      }
    },
    success:function(data){
      // $.messager.alert('Info', data, 'info');
      location.reload();
    }
  });

  $('#reqTanggal').datebox({
    editable: false
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
              <a href="<?= base_url() ?>main/index/katalog_pernyataan" class="btn btn-success btn-lg mb-1 text-left" style="width: 100%"><i class="fa fa-upload fa-lg pull-right"></i> Upload <br> Kontrak Katalog</a>
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
          <h4 class="card-title">Katalog <small> Tracking Pesanan</small></h4>
          <div class="heading-elements" id="tombol">
            <span></span>
          </div>
        </div>
        <div class="card-body area-datatable">
          <div class="form-body">

             <div class="col-md-12">
              <ul class="nav nav-tabs process-model more-icon-preocess" role="tablist">
                <li role="presentation" class="<?php if($katalogrekananRow->getField('STATUS') == '4') { echo 'active'; } ?>" style="width: 33% !important">
                  <i class="fa fa-cogs" aria-hidden="true"></i> <p>Diproses</p>
                </li>
                <li role="presentation" class="<?php if($katalogrekananRow->getField('STATUS') == '5') { echo 'active'; } ?>" style="width: 33% !important">
                  <i class="fa fa-truck" aria-hidden="true"></i> <p>Dikirim</p>
                </li>
                <li role="presentation" class="<?php if($katalogrekananRow->getField('STATUS') == '6') { echo 'active'; } ?>" style="width: 33% !important">
                  <i class="fa fa-check-square-o" aria-hidden="true"></i><p>Diterima</p>
                </li>
              </ul>
            </div>
            <hr>
            <?php
            if($katalogrekananRow->getField('STATUS') == '4')
            { ?>
              <div class="alert alert-danger">
                <b><u>Untuk Kirim Barang silahkan isi Tanggal Estimasi Pesanan Sampai dan Lampirkan Surat Jalan atau lainnya (sebagai bukti pesanan dikirim)</u></b>
              </div>
              <form id="ff" class="easyui-form form-horizontal" method="post" novalidate enctype="multipart/form-data">
                <div class="row">
                  <div class="form-group col-md-4 mb-2">
                    <label style="width: 100%">Estimasi Pesanan Sampai</label>
                    <input type="text" style="width: 200% !important" name="reqEstimasiSampai" class="form-control easyui-datebox span2" id="reqTanggal" value="<?=$reqEstimasiSampai?>" required/>
                  </div>
                </div>
                <div class="row">
                  <div class="form-group col-md-12 mb-2">
                    <label>Bukti Kirim (Surat Jalan/Resi/Nomor Pesanan) <small> (Format file .pdf & Maksimal ukuran file 2MB) </small></label> <br>
                    <input type="hidden" name="reqId" value="<?=$reqId?>" />
                    <input type="file" name="reqLinkFile" id="reqLinkFilePDF" size="30" <?php if($reqId == "") { ?> required <?php } ?>  class="easyui-validatebox"  validType="fileType['pdf']" required/>
                     <input type="hidden" name="reqLinkFileTempNama" value="<?=$reqLinkFileTempNama?>">
                  </div>
                </div>
                <div class="form-actions">
                  <a href="main/index/katalog_penawaran" class="<?= CLASS_BTN_DANGER ?> mr-1"> <i class="fa fa-arrow-left"></i> Kembali </a>
                  <button type="submit" class="<?= CLASS_BTN_PRIMARY ?>"><i class="fa fa-truck"></i> Kirim Barang</button>
                </div>
              </form>
            <?php
            } ?>

            <?php
            if($katalogrekananRow->getField('STATUS') == '5')
            { ?>
            <div class="col-md-12">
              <table class="table table-bordered table-hover mt-1 mb-1">
                <tr>
                  <td width="20%">Estimasi Sampai</td>
                  <td>
                    <?= getFormattedDate($kataloglogistikEstimasiSampai) ?>
                    <?php
                    $tgl1 = new DateTime($kataloglogistikEstimasiSampai);
                    $tgl2 = new DateTime(date("Y-m-d"));
                    $d = $tgl1->diff($tgl2)->days;
                    if ($kataloglogistikEstimasiSampai >= date('Y-m-d')) {
                      if ($d >=1) {
                        echo '<span class="badge badge-primary">pesanan sampai '.$d.' hari lagi</span>';
                      } else if ($d == 0) {
                        echo '<span class="badge badge-warning"> pesanan sampai hari ini </span>';
                      }
                    } else {
                      echo '<span class="badge badge-danger"> pesanan melebihi '.$d.' hari ini </span>';
                    }
                    ?>
                  </td>
                </tr>
                <tr>
                  <td>Bukti Kirim</td>
                  <td>
                    <a href="<?= base_url('images/katalog').'/'.$kataloglogistikPathFileBuktiKirim ?>" target="_blank" class="<?= CLASS_BTN_PRIMARY ?> btn-sm">
                      <span class="fa fa-download"></span> Download
                    </a>
                  </td>
                </tr>
              </table>
            </div>
            <?php
            } ?>

            <?php
            if($katalogrekananRow->getField('STATUS') == '6')
            { ?>
            <div class="col-md-12">
              <table class="table table-bordered table-hover mt-1 mb-1">
                <tr>
                  <td width="20%">Bukti Kirim</td>
                  <td>
                    <a href="<?= base_url('images/katalog').'/'.$kataloglogistikPathFileBuktiKirim ?>" target="_blank" class="<?= CLASS_BTN_PRIMARY ?> btn-sm">
                      <span class="fa fa-download"></span> Download
                    </a>
                  </td>
                </tr>
                <tr>
                  <td width="20%">Cetak Chat Negosiasi</td>
                  <td>
                    <a href="<?= base_url('main/loadUrl/report/katalog_cetak_chat_pdf/?reqId='.$reqId) ?>" target="_blank" class="btn btn-primary mr-1 btn-sm"><i class="fa fa-print"></i> </a>
                  </td>
                </tr>
                <tr>
                  <td width="20%">Surat Pesanan</td>
                  <td>
                    <a style="text-align: left" href="images/katalog/<?= $kataloglogistikPathFileSuratPesanan ?>" class="<?= CLASS_BTN_PRIMARY ?> btn-sm" target="_blank" /><span class="fa fa-download"></span> Download Surat Pesanan </a>
                  </td>
                </tr>
                <tr>
                  <td width="20%">Tanda Terima</td>
                  <td>
                    <?php
                    if ($kataloglogistikPathFileBuktiTerima != '') { ?>
                      <a style="text-align: left" href="images/katalog/<?= $kataloglogistikPathFileBuktiTerima ?>" class="<?= CLASS_BTN_PRIMARY ?> btn-sm" target="_blank" /><span class="fa fa-download"></span> Download Tanda Terima </a>
                    <?php
                  } else { echo "-"; }
                     ?>
                  </td>
                </tr>
              </table>
            </div>
            <?php
            } ?>

          </div>
        </div>
      </div>
    </div>
  </div>
</section>
