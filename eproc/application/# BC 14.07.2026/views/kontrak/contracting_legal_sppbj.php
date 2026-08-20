<?php
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 * 
 */

$reqId = httpFilterRequest("reqId");

// echo "string"; die;
$this->libsession->cekSession();

$this->load->library("kauth");  $userLogin = new kauth();

include_once("functions/string.func.php");
include_once("functions/date.func.php");
include_once("functions/default.func.php");

$this->load->model("Contracting");
$this->load->model("Contractingrekanan");
$this->load->model("Contractingfile");

$getMenu = new Contracting();
$kontrak = new Contracting();
$contractingrekanan = new Contractingrekanan();
$sppbj = new Contractingrekanan();

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

$sppbj->selectProses1(array("A.CONTRACTINGREKANANID" => $reqId));
$sppbj->firstRow();

$reqPaketId = $sppbj->getField('PAKET_ID') ?: '-';
$reqContractingRekananId = $sppbj->getField('CONTRACTINGREKANANID') ?: '-';
$reqCode = $sppbj->getField('CR_SPPBJ_CODE') ?: '-';
$reqTanggal = $sppbj->getField('CR_SPPBJ_TANGGAL') ?: '-';
$reqDirut = $sppbj->getField('CR_SPPBJ_DIRUT') ?: '-';
$reqDirutAlamat = $sppbj->getField('CR_SPPBJ_DIRUT_ALAMAT') ?: '-';
$reqDirutKota = $sppbj->getField('NAMA_KOTA') ?: '-';
$reqDirutJabatan = $sppbj->getField('CR_SPPBJ_DIRUT_JABATAN') ?: '-';
$reqJaminanPelaksanaan = $sppbj->getField('CR_SPPBJ_JAMINAN_PELAKSANA') ?: '-';
$reqJaminanBesar = $sppbj->getField('CR_SPPBJ_JAMINAN_BESAR') ?: '-';
$reqJaminanJangkaDari = $sppbj->getField('CR_SPPBJ_JAMINAN_JANGKA_DARI') ?: '-';
$reqJaminanJangkaSampai = $sppbj->getField('CR_SPPBJ_JAMINAN_JANGKA_SAMPAI') ?: '-';
$reqJaminanNilai = $sppbj->getField('CR_SPPBJ_JAMINAN_NILAI') ?: '-';
$reqPejabatBerwenang = $sppbj->getField('CR_SPPBJ_PEJABAT_BERWENANG') ?: '-';
$reqNIP = $sppbj->getField('CR_SPPBJ_NIP') ?: '-';
$reqJabatan = $sppbj->getField('CR_SPPBJ_JABATAN') ?: '-';
$reqPPN = $sppbj->getField('CR_SPPBJ_PPN') ?: '-';
$reqPelaksanaanDari = $sppbj->getField('CR_SPPBJ_PELAKSANAAN_DARI') ?: '-';
$reqPelaksanaanSampai = $sppbj->getField('CR_SPPBJ_PELAKSANAAN_SAMPAI') ?: '-';
$reqCreatedBy = $sppbj->getField('CR_SPPBJ_CREATED_BY') ?: '-';
$reqCreatedDate = $sppbj->getField('CR_SPPBJ_CREATED_DATE') ?: '-';
$reqNilai = $sppbj->getField('CR_SPPBJ_NILAI') ?: '-';
$reqContractingStatusKontrakId = $sppbj->getField('CONTRACTINGSTATUSKONTRAKID') ?: '-';
?>

<link rel="stylesheet" type="text/css" href="<?=base_url() ?>assets/new/vendors/css/ui/jquery-ui.min.css">
<link rel="stylesheet" type="text/css" href="<?=base_url() ?>assets/new/css/plugins/ui/jqueryui.css">
<script src="<?=base_url()?>assets/new/js/scripts/ui/jquery-ui/navigations.js"></script>

<style type="text/css">
  small { font-weight: bold; font-size: 11.5px }
  sup { font-style: italic; color: red;}
  tr.backcolornew {
    background: #b11016 !important;
    color: #fff;
  }
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
          <h4 class="mb-2">Penetapan Surat Penunjukan Penyedia Barang/Jasa (SPPBJ)</h4>

        <?php
        if ($reqContractingStatusKontrakId > 0)
        { // sudah dikirim ke penyedia?>
          <div class="card mb-1 border-blue border-darken-1">
            <div class="card-content">
              <div class="p-1">
                <h5>Dokumen SPPBJ </h5>
                <div class="table-responsive">
                  <table id="dokumenFileIdTable" class="border-double table mb-0 table-bordered mb-2" style="width: 100%">
                    <?= $this->libkontrak->getTableFile($reqId," AND (A.FILE_JENIS = 'Dokumen SPPBJ' OR A.FILE_JENIS = 'Jaminan Pelaksanaan') ") ?>
                  </table>
                </div>
              </div>
            </div>
          </div>

          <div class="form-actions">
            <table class="table table-bordered table-hover">
              <tbody>
                <tr>
                  <td width="25%" colspan="2">
                    <small>Nomor SPPBJ</small> <br> <?= $reqCode ?>
                  </td>
                  <td width="25%" colspan="2">
                    <small>Tanggal SPPBJ</small> <br>  <?= getFormattedDateShort2(dateTimeToPageCheck($reqTanggal)) ?>
                  </td>
                </tr>
                <tr>
                  <td width="25%" colspan="2">
                    <small>Pejabat Berwenang</small> <br> <?= $reqPejabatBerwenang ?>
                  </td>
                  <td width="10%">
                    <small>NIP</small> <br>  <?= $reqNIP ?>
                  </td>
                  <td width="15%">
                    <small>Jabatan</small> <br> <?= $reqJabatan ?>
                  </td>
                </tr>
                <tr>
                  <td width="25%" colspan="2">
                    <small>Nama Direktur <sup>Penyedia</sup></small> <br> <i><?= $reqDirut ?> <br> <?= $reqDirutJabatan ?></i>
                  </td>
                  <td width="25%" colspan="2">
                    <small>Alamat <sup>Penyedia</sup></small> <br>  <?= $reqDirutAlamat ?> <br> <?= $reqDirutKota ?>
                  </td>
                </tr>
                <tr>
                  <td colspan="4">
                    <small>Nilai Kontrak <sup>(hasil pemilihan)</sup></small> <br> <?= currencyToPage($reqNilai) ?>
                  </td>
                </tr>
                <tr>
                  <td width="25%" colspan="2">
                    <small>Masa Pelaksanaan Pekerjaan </small> <br> <?= getFormattedDateShort2(dateTimeToPageCheck($reqPelaksanaanDari)).' s/d '.getFormattedDateShort2(dateTimeToPageCheck($reqPelaksanaanSampai)) ?>
                  </td>
                  <td width="10%">
                    <small>PPN</small> <br> <?php
                    if ($sppbj_ppn == '1') { echo "Ya";
                    } else { echo "Tidak"; }
                    ?>
                  </td>
                  <td width="15%">
                    <small>Jaminan Pelaksanaan</small> <br>
                    <?php
                    if ($reqJaminanPelaksanaan == '1') { echo "Ya";
                    } else { echo "Tidak"; } ?>
                  </td>
                </tr>
                <?php
                if ($reqJaminanPelaksanaan == '1') {   ?>
                <tr>
                  <td width="10%">
                    <small>Persen Jaminan </small> <br>  <?= $reqJaminanBesar ?> %
                  </td>
                  <td width="15%">
                    <small>Nilai Jaminan</small> <br>  <?= currencyToPage($reqJaminanNilai) ?>
                  </td>
                  <td width="25%" colspan="2">
                    <small>Jangka Jaminan Pelaksanaan</small> <br>  <?= getFormattedDateShort2(dateTimeToPageCheck($reqJaminanJangkaDari)).' s/d '.getFormattedDateShort2(dateTimeToPageCheck($reqJaminanJangkaSampai)) ?>
                  </td>
                </tr>
                <?php
                } ?>
              </tbody>
            </table>
          <?php
          } ?>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
