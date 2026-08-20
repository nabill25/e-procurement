<?php
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 * 
 */

$reqId = httpFilterRequest("reqId");

$this->libsession->cekSession($reqId);

$this->load->library("kauth");  $userLogin = new kauth();

include_once("functions/string.func.php");
include_once("functions/date.func.php");
include_once("functions/default.func.php");

if ($reqId == '') {
  redirect(base_url().'main/index/403');
}

$this->load->model("Contracting");
$this->load->model("Contractingrekanan");

$kontrak = new Contracting();
$getMenu = new Contracting();
$contractingrekanan = new Contractingrekanan();

$contractingrekanan->selectByParams(array("A.CONTRACTINGREKANANID" => $reqId));
$contractingrekanan->firstRow();
$contractingprosesid = $contractingrekanan->getField('CONTRACTINGPROSESID');

$getMenu->selectMenuProses(array("A.CONTRACTINGPROSESID" => $contractingprosesid));
$getMenu->firstRow();
$cp_name = $getMenu->getField('CP_NAME');
$cp_link = $getMenu->getField('CP_LINK');

$kontrak->selectByParams(array("A.CONTRACTINGREKANANID" => $reqId));
$kontrak->firstRow();
$kontrak_nama = $kontrak->getField('NAMA');
$kontrak_nilai = $kontrak->getField('NILAI');
$kontrak_paket_metode_lelang = $kontrak->getField('PAKET_METODE_LELANG');
$paket_pemenang = $kontrak->getField('PEMENANG');
$paket_id = $kontrak->getField('PAKET_ID');
?>

<link rel="stylesheet" type="text/css" href="<?=base_url() ?>assets/new/vendors/css/ui/jquery-ui.min.css">
<link rel="stylesheet" type="text/css" href="<?=base_url() ?>assets/new/css/plugins/ui/jqueryui.css">
<script src="<?=base_url()?>assets/new/js/scripts/ui/jquery-ui/navigations.js"></script>
<style type="text/css">
  small { font-weight: bold; font-size: 11.5px }
</style>

<div class="row">
  <div class="col-md-3 col-sm-3">
    <div class="jqueryui-ele-container">
      <?= $this->libkontrak->getMenuLegal($reqId); ?>
    </div>
  </div>
  <div class="col-md-9 col-sm-9">
    <div class="card">
      <div class="card-content collapse show border-info border-darken-2">
        <div class="card-body">
          <!-- <h5>Paket Tender Detil</h5> -->
          <div class="form-actions">
            <?= $this->libkontrak->getInfoPaket($paket_id); ?>
            <?php
            if ($contractingprosesid > 1) {
              $this->load->model("Contracting");
              $this->load->model("Contractingrekanan");
              $this->load->model("Rekanan");

              $contracting = new Contracting();
              $spkpks = new Contractingrekanan();
              $rekanan = new Rekanan();

              $contracting->selectByParams(array("A.CONTRACTINGREKANANID" => $reqId));
              $contracting->firstRow();

              $contracting->selectByParams(array("A.CONTRACTINGREKANANID" => $reqId));
              $contracting->firstRow();
              $reqNamaPaket = $contracting->getField("NAMA");
              $reqPanitiaStr = $contracting->getField("PANITIA_STR");
              $reqPenggunaStr = $contracting->getField("PENGGUNA_STR");
              $reqPpkStr = $contracting->getField("PPK_STR");
              $reqPemenangStr = $contracting->getField("PEMENANG_NAMA");

              $spkpks->selectViewPKSSPK(array("A.CONTRACTINGREKANANID" => $reqId));
              $spkpks->firstRow();

              $reqJenisKontrak = $contracting->getField('JENIS_KONTRAK') ?: '-';
              $reqCode = $spkpks->getField('CR_CODE') ?: '-';
              $reqRekananProses1Id = $spkpks->getField('CONTRACTINGREKANANPROSES1ID') ?: '-';
              $reqPaketId = $spkpks->getField('PAKET_ID') ?: '-';
              $reqContractingRekananId = $spkpks->getField('CONTRACTINGREKANANID') ?: '-';
              $reqJenisPengadaan = $spkpks->getField('CR_JENIS_PENGADAAN') ?: '-';
              $reqJenisPengadaanStr = $spkpks->getField('CR_JENIS_PENGADAAN_STR') ?: '-';
              $reqJenisPekerjaan = $spkpks->getField('CR_JENIS_PEKERJAAN') ?: '-';
              $reqJenisPekerjaanStr = $spkpks->getField('CR_JENIS_PEKERJAAN_STR') ?: '-';
              $reqContractingjeniskontrakid = $spkpks->getField('CONTRACTINGJENISKONTRAKID') ?: '-';
              $reqJenisKontrakStr = $spkpks->getField('CR_JENIS_KONTRAK_STR') ?: '-';
              $reqWaktuPelaksanaanDari = $spkpks->getField('CR_WAKTU_PELAKSANAAN_DARI') ?: '-';
              $reqWaktuPelaksanaanSampai = $spkpks->getField('CR_WAKTU_PELAKSANAAN_SAMPAI') ?: '-';
              $reqLingkupPekerjaan = $spkpks->getField('CR_LINGKUP_PEKERJAAN') ?: '-';
              $reqNilaiKontrak = $spkpks->getField('CR_NILAI_KONTRAK') ?: '-';
              $reqMetodePembayaran = $spkpks->getField('CR_METODE_PEMBAYARAN') ?: '-';
              $reqPihak1Nama = $spkpks->getField('CR_PIHAK1_NAMA') ?: '-';
              $reqPihak1Jabatan = $spkpks->getField('CR_PIHAK1_JABATAN') ?: '-';
              $reqPihak2Nama = $spkpks->getField('CR_PIHAK2_NAMA') ?: '-';
              $reqPihak2Jabatan = $spkpks->getField('CR_PIHAK2_JABATAN') ?: '-';
              $reqPihak2 = $spkpks->getField('CR_PIHAK2_PERUSAHAAN') ?: '-';
              $reqCreatedBy = $spkpks->getField('CR_CREATED_BY') ?: '-';
              $reqContractingStatusKontrakId = $spkpks->getField('CONTRACTINGSTATUSKONTRAKID') ?: '-';
              $reqRakananId = $spkpks->getField('REKANAN_ID') ?: '-';
              $reqJnsKontrak = $spkpks->getField('JNS_KONTRAK') ?: '-';

              // Get Rekanan
              $rekanan->selectByParams(array("REKANAN_ID" => $reqRakananId), -1, -1);
              $rekanan->firstRow();
              $rekanan_nama = $rekanan->getField("NAMA");
              $rekanan_npwp = $rekanan->getField("NPWP");
              $rekanan_alamat = $rekanan->getField("ALAMAT");
              $rekanan_telepon = $rekanan->getField("TELEPON_FULL");
              $rekanan_email = $rekanan->getField("EMAIL");
              $rekanan_kota = $rekanan->getField("KOTA");
              $rekanan_kodepos = $rekanan->getField("KODEPOS");
             ?>

             <h4 class="mb-2">Data Kontrak <?= $reqJenisKontrak ?></h4>
              <div class="form-actions">

                  <table class="table table-bordered table-hover">
                    <tbody>
                      <tr>
                        <td width="20%" colspan="4">
                          <small>Nomor Kontrak</small> <br> <?= $reqCode ?>
                        </td>
                      </tr>
                      <tr>
                        <td width="25%" colspan="2">
                          <small>Jenis Pengadaan</small> <br> <?= $reqJenisPengadaanStr ?>
                        </td>
                        <td width="25%" colspan="2">
                          <small>Jenis Kontrak</small> <br> <?= $reqJenisKontrakStr ?>
                        </td>
                      </tr>
                      <tr>
                        <td width="25%" colspan="2">
                          <small>Jangka Waktu Pelaksanaan </small> <br> <?= getFormattedDateShort2(dateTimeToPageCheck($reqWaktuPelaksanaanDari)).' s/d '.getFormattedDateShort2(dateTimeToPageCheck($reqWaktuPelaksanaanSampai)) ?>
                        </td>
                        <td width="15%">
                          <small>Nilai Pekerjaan </small> <br>  <?= currencyToPage($reqNilaiKontrak) ?>
                        </td>
                        <td width="10%">
                          <small>Metode Pembayaran </small> <br>
                          <?php
                          if ($reqMetodePembayaran == '1') {
                             echo "Sekaligus";
                          } else { echo "Tempo"; } ?>
                        </td>
                      </tr>
                      <tr>
                        <td width="20%" colspan="3">
                          <small>Lingkup Pekerjaan</small> <br> <?= $reqLingkupPekerjaan ?>
                        </td>
                        <td width="15%">
                          <small>Jenis Pekerjaan</small> <br> <?= $reqJenisPekerjaanStr ?>
                        </td>
                      </tr>
                      <tr>
                        <td width="25%" colspan="2">
                          <small>PIHAK I </small> <br>
                          <?= $reqPihak1Nama ?> <br>
                          <i><?= $reqPihak1Jabatan ?></i>
                        </td>
                        <td width="25%" colspan="2">
                          <small>PIHAK II </small> <br>
                          <?= $reqPihak2Nama ?> <br>
                          <i><?= $reqPihak2Jabatan ?></i>
                        </td>
                      </tr>
                    </tbody>
                  </table>

              </div>
             <?php
            } ?>

          </div>
        </div>
      </div>
    </div>
  </div>
</div>
