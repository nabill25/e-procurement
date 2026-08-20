<?php
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 * 
 */
$reqId = httpFilterRequest("reqId");
$reqProses = httpFilterRequest("reqProses");

$this->libsession->cekSessionKontrakPPK($reqId);

if ($this->LEGAL == '1') // legal gak boleh akses ini
  redirect(base_url('kontrak/index/dashboardkontrak'));

$this->load->library("kauth");  $userLogin = new kauth();

include_once("functions/string.func.php");
include_once("functions/date.func.php");
include_once("functions/default.func.php");

$this->session->set_userdata('setProsesKontrak',$reqProses);

if ($reqId == '') {
  redirect(base_url().'main/index/403');
}

$this->load->model("Contracting");
$this->load->model(array("Contractingrekanan","PaketPenilaian","Contractingjaminan","Contractingdeliverable","Contractingjaminanpemeliharaan"));

$kontrak = new Contracting();
$getMenu = new Contracting();
$legal = new Contractingrekanan();
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

$legal->selectViewLegal(array("A.CONTRACTINGREKANANID" => $reqId));
$legal->firstRow();
$reqLegalNomorPKS = $legal->getField('CR_LEGAL_NOMOR_PKS') ?: '-';
$reqLegalTanggal = $legal->getField('CR_LEGAL_TANGGAL') ?: '-';
$reqLegalNomorRekanan = $legal->getField('CR_LEGAL_NOMOR_REKANAN') ?: '-';
$reqLegalTanggalRekanan = $legal->getField('CR_LEGAL_TANGGAL_REKANAN') ?: '-';
$reqLegalCreatedBy = $legal->getField('CR_LEGAL_CREATED_BY') ?: '-';
$reqLegalCreatedDate = $legal->getField('CR_LEGAL_CREATED_DATE') ?: '-';
$reqLegalUpdatedBy = $legal->getField('CR_LEGAL_UPDATED_BY') ?: '-';
$reqLegalUpdatedDate = $legal->getField('CR_LEGAL_UPDATED_DATE') ?: '-';

?>

<link rel="stylesheet" type="text/css" href="<?=base_url() ?>assets/new/vendors/css/ui/jquery-ui.min.css">
<link rel="stylesheet" type="text/css" href="<?=base_url() ?>assets/new/css/plugins/ui/jqueryui.css">
<script src="<?=base_url()?>assets/new/js/scripts/ui/jquery-ui/navigations.js"></script>
<style type="text/css">
  small { font-weight: bold; font-size: 11.5px }
  tr.backcolornew {
    background: #103A6C !important;
    color: #fff;
  }
</style>

<div class="row">
  <div class="col-md-3 col-sm-3">
    <div class="jqueryui-ele-container">
      <?= $this->libkontrak->getMenu($reqId,$reqProses); ?>
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
            if ($contractingprosesid > 1)
            {
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

              $reqContractingRekananProses1Id = $spkpks->getField('CONTRACTINGREKANANPROSES1ID') ?: '-';
              $reqJenisKontrak = $contracting->getField('JENIS_KONTRAK') ?: '-';
              $reqCode = $spkpks->getField('CR_CODE') ?: '';
              $reqJnsKontrak = $spkpks->getField('JNS_KONTRAK') ?: ''; 
              $reqJnsKontrakStr = $spkpks->getField('JNS_KONTRAK_STR') ?: ''; 
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
              $reqPO = $spkpks->getField('CR_PO') ?: '-';
              $reqTglHasilTerimaPilihan = $spkpks->getField('CR_TGL_HASIL_TERIMA_PEMILIHAN') ?: '-';
              $reqPenyelesaianAwal = $spkpks->getField('CR_PENYELESAIAN_KONTRAK_AWAL') ?: '-';
              $reqPenyelesaianAkhir = $spkpks->getField('CR_PENYELESAIAN_KONTRAK_AKHIR') ?: '-';
              $reqMasaGaransi = $spkpks->getField('CR_MASA_GARANSI') ?: '-';
              $reqMasaGaransiPeriode = $spkpks->getField('CR_MASA_GARANSI_PERIODE') ?: '-';

              // Get Rekanan
              $rekanan->selectByParams(array("A.REKANAN_ID" => $reqRakananId), -1, -1);
              $rekanan->firstRow();
              $rekanan_nama = $rekanan->getField("NAMA");
              $rekanan_npwp = $rekanan->getField("NPWP");
              $rekanan_alamat = $rekanan->getField("ALAMAT");
              $rekanan_telepon = $rekanan->getField("TELEPON_FULL");
              $rekanan_email = $rekanan->getField("EMAIL");
              $rekanan_kota = $rekanan->getField("KOTA");
              $rekanan_kodepos = $rekanan->getField("KODEPOS");

              $this->load->library("paketinfo"); $paketInfo = new paketinfo();
              $paketInfo->getPaket($reqPaketId);
              $reqMultiPemenang = $paketInfo->multi_pemenang;
             ?>

            <div class="form-actions">

              <!-- ---------------------------  KONTRAK BIASA ------------------------ -->
              <!-- ------------------------------------------------------------------- -->
              <?php
              if ($reqJnsKontrak == '1' && $contractingprosesid >= 3)
              { // Jika JNS_KONTRAK PKS & PROSES KONTRAK SELESAI
                $sppbj = new Contractingrekanan();

                $sppbj->selectProses1(array("A.CONTRACTINGREKANANID" => $reqId));
                $sppbj->firstRow();

                $reqPaketId = $sppbj->getField('PAKET_ID') ?: '-';
                $reqContractingRekananId = $sppbj->getField('CONTRACTINGREKANANID') ?: '-';
                $reqCode = $sppbj->getField('CR_SPPBJ_CODE') ?: '-';
                $reqTanggal = $sppbj->getField('CR_SPPBJ_TANGGAL') ?: '-';
                $reqDirut = $sppbj->getField('CR_SPPBJ_DIRUT') ?: '-';
                $reqDirutAlamat = $sppbj->getField('CR_SPPBJ_DIRUT_ALAMAT') ?: '-';
                $reqDirutKota = $sppbj->getField('CR_SPPBJ_DIRUT_KOTA') ?: '-';
                $reqDirutJabatan = $sppbj->getField('CR_SPPBJ_DIRUT_JABATAN') ?: '-';
                $reqJaminanPelaksanaan = $sppbj->getField('CR_SPPBJ_JAMINAN_PELAKSANA') ?: '-';
                $reqJaminanBesar = $sppbj->getField('CR_SPPBJ_JAMINAN_BESAR') ?: '-';
                $reqJaminanJangkaDari = $sppbj->getField('CR_SPPBJ_JAMINAN_JANGKA_DARI') ?: '-';
                $reqJaminanJangkaSampai = $sppbj->getField('CR_SPPBJ_JAMINAN_JANGKA_SAMPAI') ?: '-';
                $reqJangkaMaksimal = $sppbj->getField('CR_SPPBJ_JAMINAN_MAKSIMAL_PENYERAHAN') ?: '-';
                $reqJaminanNilai = $sppbj->getField('CR_SPPBJ_JAMINAN_NILAI') ?: '-';
                $reqPejabatBerwenang = $sppbj->getField('CR_SPPBJ_PEJABAT_BERWENANG') ?: '-';
                $reqNIP = $sppbj->getField('CR_SPPBJ_NIP') ?: '-';
                $reqJabatan = $sppbj->getField('CR_SPPBJ_JABATAN') ?: '-';
                $reqPPN = $sppbj->getField('CR_SPPBJ_PPN') ?: '-';
                $reqPelaksanaanDari = $sppbj->getField('CR_SPPBJ_PELAKSANAAN_DARI') ?: '-';
                $reqPelaksanaanSampai = $sppbj->getField('CR_SPPBJ_PELAKSANAAN_SAMPAI') ?: '-';
                $reqCreatedBy = $sppbj->getField('CR_SPPBJ_CREATED_BY') ?: '-';
                $reqCreatedDate = $sppbj->getField('CR_SPPBJ_CREATED_DATE') ?: '-';
                $reqNilai = $sppbj->getField('CR_SPPBJ_NILAI') ?: 0;
                $reqContractingStatusKontrakId = $sppbj->getField('CONTRACTINGSTATUSKONTRAKID') ?: '0'; 
                if ($reqJnsKontrak != '0') { // Selain Surat Perjanjian 
              ?>
                <h4 class="mb-2">SPPBJ</h4>
                <?php 
              $contractingjaminan = new Contractingjaminan();
              $contractingjaminan->selectByParams(array("CONTRACTINGREKANANID" => $reqId)); 

              if ($contractingjaminan->countRow() > 0) {
              ?>
              <h4>Jaminan</h4>
              <table class="table table-bordered">
                <thead>
                  <tr class="backcolornew">
                    <td>Nomor Jaminan</td>
                    <td>Tanggal Jaminan</td>
                    <td width="100">File <br>Jaminan</td>
                    <td width="100">Tanggal Konfirmasi ke Bank</td>
                    <td width="100">Tanggal Konfirmasi oleh Bank</td>
                    <td width="100">Bukti Konfirmasi <br> Keabsahan</td>
                    <td width="100">Status</td>
                  </tr>
                </thead>
                <tbody>
                  <?php 
                  if ($contractingjaminan->countRow() > 0) {
                    while($contractingjaminan->nextRow())
                    { ?>
                      <tr>
                        <td><?= $contractingjaminan->getField("NOMOR")?></td>
                        <td><?= getFormattedDateShort2(dateTimeToPageCheck($contractingjaminan->getField("TANGGAL_JAMINAN")))?></td>
                        <td><a href="uploads/kontrak/<?= $contractingjaminan->getField("FILE_JAMINAN")?>" target="_blank" class="badge badge-primary"><span class="fa fa-download"> Download</span></a></td>
                        <td><?= getFormattedDateShort2(dateTimeToPageCheck($contractingjaminan->getField("TANGGAL_KONFIRMASI_KEBANK")))?></td>
                        <td><?= getFormattedDateShort2(dateTimeToPageCheck($contractingjaminan->getField("TANGGAL_KONFIRMASI_OLEH_BANK")))?></td>
                        <?php 
                        if ($contractingjaminan->getField("FILE_KONFIRMASI")) { ?>
                          <td><a href="uploads/kontrak/<?= $contractingjaminan->getField("FILE_KONFIRMASI")?>" target="_blank" class="badge badge-primary"><span class="fa fa-download"> Download</span></a></td>
                        <?php } else { ?>
                          <td>-</td>
                        <?php } 
                          if ($contractingjaminan->getField("KONFIRMASI") == '1') { ?>
                          <td><span class="badge badge-primary">Sesuai</span></td>
                        <?php } else if ($contractingjaminan->getField("KONFIRMASI") == '2') { ?>
                          <td><span class="badge badge-info">Cair</span></td>
                        <?php } else { ?>
                          <td><span class="badge badge-danger">Tidak Sesuai</span></td>
                        <?php } ?>
                      </tr>
                  <?php } 
                      } ?>
                </tbody>
              </table>
              <?php 
              } ?>
                
                <?= $this->libkontrak->getInfoSPPBJ($reqId); ?> 

              <?php
                }
              } ?>

            </div>

            <?php
            if ($contractingprosesid >= 3)
            { // SELESAI KONTRAK
              if ($reqJnsKontrak != '3' && $contractingprosesid >= 3)
              { ?>
                <!-- <div class="card mb-1 border-blue border-darken-1" style="padding: 5px 10px 0 10px; background-color: #fff3f3">
                  <div class="row">
                    <div class="form-group col-md-6 mb-2" style="margin-bottom: 1px solid #b7b7b7">
                        <h3><b>Informasi Penyedia (pemenang)</b></h3>
                        <h2><?= $rekanan_nama ?></h2>
                        <table style="width: 100%">
                          <tr> <td><i class="fa fa-id-card"></i> <?= $rekanan_npwp ?> <span class="badge badge-info">NPWP</span></td> </tr>
                          <tr> <td><i class="fa fa-phone"></i> Telepon: <?= $rekanan_telepon ?></td> </tr>
                          <tr> <td><i class="fa fa-envelope"></i> Email: <?= $rekanan_email ?></td> </tr>
                          <tr> <td><i class="fa fa-map-marker"></i> <?= $rekanan_alamat.', '.$rekanan_kota.' '.$rekanan_kodepos ?></td> </tr>
                        </table>
                    </div>
                    <div class="form-group col-md-6 mb-2" style="margin-bottom: 1px solid #b7b7b7; text-align: right;">
                        <h3><b>Informasi Pengguna</b></h3>
                        <h2><?= $reqPenggunaStr ?></h2>
                        <table style="width: 100%">
                          <tr> <td><?= SYSTEM_NAME_PT ?></td> </tr>
                          <tr> <td><?= SYSTEM_ALAMAT_PT ?></td> </tr>
                        </table>
                    </div>
                  </div>
                </div> -->
            <?php
              } else {
                $this->load->model("Paketpemenang");
                $getpaket_pemenang = new Paketpemenang();
                $getpaket_pemenang2 = new Paketpemenang();
                $getpaket_pemenang->selectByParams(array("PAKET_ID" => $reqPaketId), -1, -1);
                $getpaket_pemenang2->selectByParams(array("PAKET_ID" => $reqPaketId), -1, -1);
                ?>
               <h4>Data <?= $reqJnsKontrakStr ?></h4>
                  <table id="kontrak" class="border-double table mb-0 table-bordered mb-2" style="width: 100%">
                    <tbody>
                      <tr class="backcolornew">
                        <th>No. <?= $reqJnsKontrakStr ?></th>
                        <th>Penyedia</th>
                        <th width="8%">Aksi</th>
                      </tr>
                      <?php
                      if ($reqMultiPemenang == '0') {
                        $getpaket_pemenang->selectByParams(array("PAKET_ID" => $reqPaketId, "PERINGKAT" => '1'), -1, -1);
                      } else {
                        // echo "Multi Pemanang";
                        $getpaket_pemenang->selectByParams(array("PAKET_ID" => $reqPaketId), -1, -1);
                      }
                      $no = 1;
                      while($getpaket_pemenang->nextRow())
                      {
                        $contractingrekananSPPBJ = new Contractingrekanan();
                        $contractingrekananSPPBJ->selectViewPKSSPK(array("A.PAKET_ID" => $reqPaketId, "A.REKANAN_ID" => $getpaket_pemenang->getField("REKANAN_ID")));
                        $contractingrekananSPPBJ->firstRow();
                        ?>
                        <tr>
                          <td>
                            <?= $contractingrekananSPPBJ->getField("CR_LEGAL_NOMOR_PKS") ?: '-'; ?>
                          </td>
                          <td><?= $getpaket_pemenang->getField("NAMA"); ?></td>
                          <td>
                            <a onclick="openAdd('main/loadUrlKontrak/kontrak/contracting_view_kontrak?reqId=<?= $contractingrekananSPPBJ->getField("CONTRACTINGREKANANPROSES1ID") ?>&reqConRekId=<?= $contractingrekananSPPBJ->getField("CONTRACTINGREKANANID") ?>')" class=""> <i class="fa fa-eye"></i> </a>

                          </td>
                        </tr>
                        <!--   -->
                      <?php
                      $no++;
                      } ?>
                    </tbody>
                  </table>
            <?php
              }
            }?>

            <?php
              if ($reqJnsKontrak != '3') { ?>
               <h4 class="mb-2">Data Kontrak <?= $reqJenisKontrak ?></h4>
                <div class="form-actions">
                  <?= $this->libkontrak->getInfoKontrak($reqId); ?> 
 
                </div>
             <?php
              }
            }

            if ($reqJnsKontrak != '3' && $contractingprosesid >= 6)
            { // SELESAI KONTRAK SELAIN KONTRAK PAYUNG ?>
              <hr>
              <h4>Realisasi Pekerjaan</h4>
              <table id="deliveriable" class="border-double table mb-0 table-bordered mb-2" style="width: 100%">
              <tr class="backcolornew">
                <th>Lingkup</th>
                <th>Hasil Pekerjaan</th>
                <th width="20%">Tanggal</th>
                <th width="10px">Persentase</th>
                <th width="50px">Catatan</th>
                <th width="50px">Status</th> 
              </tr>
              <?php
              $this->load->model("Contractingdeliverable");
              $datadelivery = new Contractingdeliverable();
              $datadelivery->selectByParams(array("CONTRACTINGREKANANID"=>$reqId));
              if ($datadelivery->countRow() > 0) {
                while($datadelivery->nextRow()) {
                ?>
                <tr>
                  <td><?= $datadelivery->getField('LINGKUP') ?></td>
                  <td><?= $datadelivery->getField('DELIVERY_NAMA') ?></td>
                  <td>
                    Aktual Pekerjaan: <?= $datadelivery->getField('TANGGAL') ? getFormattedDateShort($datadelivery->getField('TANGGAL')) : '-'; ?> <br>
                    Laporan Selesai: <?= $datadelivery->getField('TANGGAL_TERIMA') ? getFormattedDateShort($datadelivery->getField('TANGGAL_TERIMA')) : '-'; ?></td>
                  <td><?= $datadelivery->getField('PRESENTASE') ? $datadelivery->getField('PRESENTASE').' %' : ''  ?></td>
                  <td>
                    <?php
                      if ($datadelivery->getField('FILE_BAPP')) {
                         echo '<a href="uploads/kontrak/'.$datadelivery->getField('FILE_BAPP').'" target="_blank" class="badge badge-primary"><span class="fa fa-download"> BAPP</span></a>';
                        
                        if ($datadelivery->getField('FILE_NAMA')) {
                           echo '<a href="uploads/kontrak/'.$datadelivery->getField('FILE_NAMA').'" target="_blank" class="badge badge-primary"><span class="fa fa-download"> File</span></a><br>';
                           echo $datadelivery->getField('KETERANGAN');
                         } else {
                           echo $datadelivery->getField('KETERANGAN');
                         }
                       }
                    ?>
                  </td>
                  <td width="100px">
                    <?php
                    if(str_replace(' ','',$datadelivery->getField('STATUS')) == 'Proses') {
                      echo '<span class="badge badge-danger">'.$datadelivery->getField('STATUS').'</span>';
                    } else {
                      echo '<span class="badge badge-primary">'.$datadelivery->getField('STATUS').'</span>';
                    }
                    ?>
                  </td> 
                </tr>
                <?php
                }
              } else { echo '<tr><td colspan="3">. : : Tidak ada data : : .</td></tr>';} ?>
            </table>

              <hr>
              <h4>Tagihan</h4>
              <table id="tabletermin" class="border-double table mb-0 table-bordered mb-2 table-responsive" style="width: 100%">
              <tr class="backcolornew">
                <th>Nomor Receipt</th>
                <th width="150px">Tanggal</th>
                <th width="150px">Tanggal Selesai Administrasi</th>
                <?php // if ($reqMetodePembayaran == 2) { ?>
                <th class="text-center">Tagihan</th>
                <?php
                // } ?>
                <th>Nilai Pembayaran</th>
                <th>Nilai Potongan</th>
                <th width="80px">Persentase</th>
                <th width="50px" class="text-center">Dokumen</th>
                <!-- <th width="50px">BA</th> -->
                <th width="100px">Status</th>
                <?php
                if ($this->LEGAL != '1') { // update status oleh peng. kontrak bukan legal ?>
                <th width="50px">Aksi</th>
                <?php
                } ?>
              </tr>
              <?php
              $this->load->model("Contractingpayment");
              $datapayment = new Contractingpayment();
              $datapayment->selectByParams(array("A.CONTRACTINGREKANANID"=>$reqId));
              if ($datapayment->countRow() > 0) {
                $tombolTutup = 0;
                while($datapayment->nextRow()) {
                  $totalPay += $datapayment->getField('PAY_NILAI');
                  $totalProgress += $datapayment->getField('PAY_PROGRES');
                  $statusPay = str_replace(' ','',$datapayment->getField('PAY_STATUS'));

                  // if ($datapayment->getField('PAY_LAMPIRAN') && $statusPay == 'Selesai') {
                  if ($statusPay == 'Selesai') {
                    $tombolTutup++;
                  }
                ?>
                <tr>
                  <td><?= $datapayment->getField('PAY_NOMOR') ?></td>
                  <td><?= getFormattedDateShort($datapayment->getField('PAY_DATE')) ?></td>
                  <td><?= getFormattedDateShort($datapayment->getField('PAY_DATE_DARI')).' sd '.getFormattedDateShort($datapayment->getField('PAY_DATE_SAMPAI')) ?></td>
                  <?php // if ($reqMetodePembayaran == 2) { ?>
                  <td class="text-center"><?= $datapayment->getField('PAY_TERMIN_KE') ?></td>
                  <?php
                  // } ?>
                  <td><?= currencyToPage($datapayment->getField('PAY_NILAI')) ?></td>
                  <td><?= currencyToPage($datapayment->getField('PAY_POTONGAN')) ?></td>
                  <td class="text-center">
                    <?php 
                    $presen = 0;
                    $ff = explode(',',$datapayment->getField('DELIVERABLEID_FK'));
                    if (is_array($ff)) {
                      $aArray = $ff;
                    }

                    $contractingdeliverable = new Contractingdeliverable();
                    $contractingdeliverable->selectByParams(array("CONTRACTINGREKANANID"=>$datapayment->getField('CONTRACTINGREKANANID')));
                    $no = 1;
                    $countProses = 0;
                    while ($contractingdeliverable->nextRow()) 
                    {  
                      if (in_array($contractingdeliverable->getField("DELIVERABLEID"), $aArray)) {
                         $presen += $contractingdeliverable->getField("PRESENTASE");
                       }  

                       // Cek status harus selesai semua, agar bisa update tagihan
                       if (trim($contractingdeliverable->getField("STATUS")) == 'Proses') {
                         $countProses++;
                       }
                    $no++;
                    } 
                    // echo $presen;
                    ?> 
                    <?php 
                    echo $datapayment->getField('PAY_PROGRES').' %';
                    // if ($datapayment->getField('PAY_PROGRES') == $presen) {
                      // echo $presen;
                    // } else {
                      // echo '<span style="cursor: pointer" onclick="return updateProg'.$datapayment->getField('PAYMENTID').'()" class="badge badge-warning btn-xs">Update</span>';
                      // echo '<span class="badge badge-warning btn-xs">Update</span>';
                    // }
                    ?>
                  </td>
                  <td class="text-center">
                  <?php
                    if (file_exists('uploads/payment/'.$datapayment->getField('PAY_LAMPIRAN')) && $datapayment->getField('PAY_LAMPIRAN') != '' ) {
                      echo '<a href="uploads/payment/'.$datapayment->getField('PAY_LAMPIRAN').'" target="_blank" class="badge badge-primary" style="margin-top:3%"><span class="fa fa-download"> BAST</span></a>';
                    } else {
                      // echo '-';
                    }

                    if (file_exists('uploads/payment/'.$datapayment->getField('PAY_LAMPIRAN_BAP')) && $datapayment->getField('PAY_LAMPIRAN_BAP') != '' ) {
                      echo '<a href="uploads/payment/'.$datapayment->getField('PAY_LAMPIRAN_BAP').'" target="_blank" class="badge badge-primary"><span class="fa fa-download"> BAP</span></a>';
                    } else {
                      echo '-';
                    }
                  ?>
                  </td> 
                  <td>
                    <?php 
                    if($statusPay == 'Selesai') {
                      echo '<span class="badge badge-primary">'.$datapayment->getField('PAY_STATUS').'</span>';
                    } else {
                      echo '<span class="badge badge-danger">'.$datapayment->getField('PAY_STATUS').'</span>';
                    }
                    ?>
                  </td>
                  <?php
                  if ($this->LEGAL != '1' && $this->USER_TYPE_ID != '20' && $this->LEVEL_KONTRAK == '3') { // update status oleh peng. kontrak bukan legal, pemeriksa kontrak ?>
                  <td>
                      <a style="color: #fff;" onclick="openAdd('main/loadUrlKontrak/kontrak/contracting_add_payment_edit?reqAidi=<?= $datapayment->getField('PAYMENTID') ?>-<?= $reqMetodePembayaran ?>&reqConRekId=<?= $reqId ?>')" class="badge badge-info"><span class="fa fa-edit"></span></a>
                  </td>
                  <?php
                  } else { echo '<td>-</td>'; }?>
                </tr>
                <script type="text/javascript">
                  function updateProg<?=$datapayment->getField('PAYMENTID')?>() {
                    $.getJSON("contracting_json/updateprogres/?paymentid="+<?= $datapayment->getField('PAYMENTID') ?>+"&presen="+<?= $presen ?>,function(data){
                      // alert(data.PESAN);
                      location.reload();
                    }); 
                  }
                </script>
                <?php
                }
              } else {
                if ($reqMetodePembayaran == 2) {
                  echo '<tr><td colspan="6">. : : Tidak ada data : : .</td></tr>';
                } else {
                  echo '<tr><td colspan="5">. : : Tidak ada data : : .</td></tr>';
                }
              } ?>
              <tfoot>
                <tr style="background-color:#b7b7b7; font-weight: bold;">
                  <td class="text-center" colspan="4">TOTAL</td>
                  <td><?= currencyToPage($totalPay) ?></td>
                  <td></td>
                  <td class="text-center"><?= $totalProgress.' %' ?></td>
                  <td colspan="3"></td>
                </tr>
              </tfoot>
            </table>

              <?php
              if ($reqJenisPekerjaan == '1') { // Hanya untuk pekerjaan TI ?>
              <hr>
              <h4>Service Level Agreement (SLA)</h4>
              <table id="tablesla" class="border-double table mb-0 table-bordered mb-2" style="width: 100%">
                <tr class="backcolornew">
                  <th width="100px">Availability</th>
                  <th>Waktu (jam)</th>
                  <th>Denda Keterlambatan </th>
                  <th>Biaya Maintanance</th>
                  <th>Nilai Denda</th>
                  <!-- <th width="100px">Status</th> -->
                </tr>
                <?php
                $this->load->model("Contractingsla");
                $datsla = new Contractingsla();
                $datsla->selectByParams(array("CONTRACTINGREKANANID"=>$reqId));
                if ($datsla->countRow() > 0) {
                  while($datsla->nextRow()) {
                  ?>
                  <tr>
                    <td><?= $datsla->getField('SLA_AVAILABILITY').' %' ?></td>
                    <td><?= $datsla->getField('SLA_WAKTU') ?></td>
                    <td><?= $datsla->getField('SLA_DENDA').' % dari nilai biaya bulanan maintanance' ?></td>
                    <td><?= currencyToPage($datsla->getField('SLA_BIAYA_MAINTANANCE')) ?></td>
                    <td><?= currencyToPage($datsla->getField('SLA_NILAI_DENDA')) ?></td>
                    <!-- <td><?php //$datsla->getField('SLA_STATUS') ?></td>  -->
                  </tr>
                  <?php
                  }
                } else { echo '<tr><td colspan="6">. : : Tidak ada data : : .</td></tr>';} ?>
              </table>
              <?php
              } ?>

              <h4>Addendum</h4>
              <div class="table-responsive">
                <table id="addendum" class="border-double table mb-0 table-bordered mb-2">
                  <tr class="backcolornew"> 
                    <!-- <th class="text-center">Status</th> -->
                    <th class="text-center">Approval <br>Kasubdit</th>
                    <th class="text-center">Approval <br>Penyedia</th>
                    <th class="text-center">No. Addendum</th>
                    <th class="text-center">Tanggal</th>
                    <th class="text-center">Dok. <br>Addendum</th>
                    <th class="text-center" width="10px">Addendum <br>Ke</th>
                    <th class="text-center">Dok. <br>Persetujuan</th>
                    <th class="text-center">Jenis</th>
                    <th class="text-center">Masa Berlaku <br> Kontrak Addendum</th>
                    <th class="text-center">Tanggal Penyelesaian <br> Administrasi Penagihan</th>
                    <th class="text-center">Nilai</th>
                    <th class="text-center">Keterangan</th>
                  </tr>
                  <?php
                  $this->load->model("Contractingaddendum");
                  $addendum = new Contractingaddendum();
                  $addendum->selectByParams(array("CONTRACTINGREKANANID"=>$reqId));
                  if ($addendum->countRow() > 0) {
                    while($addendum->nextRow()) {
                    ?>
                    <tr> 
                      <td class="text-center" <?php if ($this->USER_TYPE_ID == '20' && $addendum->getField('APPROVED_KASUBDIT') != '1') { echo 'style="background-color:#F7CA18"'; } ?>>
                        <?php 
                        if ($this->USER_TYPE_ID == '20') { // KA SUBDIT
                          if ($addendum->getField('APPROVED_KASUBDIT') == '1') {
                            echo '<a onClick="approvalAddendum(\'contracting_json/approvalAddendum/\', '.$addendum->getField("CONTRACTING_ADDENDUM_ID").',\'0\')" class="fa fa-check btn-xs btn-primary" style="padding:5px; border-radius:4px; color:#fff"><small>Disetujui</small></a>';
                          } else {
                            echo '<a onClick="approvalAddendum(\'contracting_json/approvalAddendum/\', '.$addendum->getField("CONTRACTING_ADDENDUM_ID").',\'1\')" class="btn-xs btn-danger" style="padding:5px; border-radius:4px; color:#fff"> <small class="blinking-element">Setujui?</small></a>';
                          }
                        } else {
                          if ($addendum->getField('APPROVED_KASUBDIT') == '1') {
                            echo '<span class="fa fa-check btn-xs btn-primary" style="padding:2px; border-radius:4px; color:#fff"> <small>Disetujui</small></span>';
                          } else {
                            echo '<span class="fa fa-times btn-xs btn-danger" style="padding:2px; border-radius:4px; color:#fff"> <small>Belum</small></span>';
                          }
                        }
                        ?>
                      </td>  
                      <td class="text-center"> 
                      <!-- Approval Penyedia --> 
                        <?php  
                          if ($addendum->getField('APPROVED_PENYEDIA') == '1') {
                            echo '<span class="fa fa-check btn-xs btn-primary" style="padding:2px; border-radius:4px; color:#fff"> <small>Disetujui</small></span>';
                          } else {
                            echo '<span class="fa fa-times btn-xs btn-danger" style="padding:2px; border-radius:4px; color:#fff"> <small>Belum</small></span>';
                          }
                        ?>
                      </td> 
                      <td><?= $addendum->getField('NOMOR') ?></td>
                      <td><?= getFormattedDateShort2(dateTimeToPageCheck($addendum->getField('TANGGAL'))) ?></td> 
                      <td class="text-center">
                        <?php 
                        if ($addendum->getField('ADDENDUM_FILE')) {
                          echo '<a href="uploads/kontrak/'.$addendum->getField('ADDENDUM_FILE').'" class="badge badge-primary" target="_blank" style="margin-bottom:5px"><i class="fa fa-download"></i> Download</a><br>';
                          if ($addendum->getField('ADDENDUM_FILE_PENYEDIA')) {
                          echo '<a href="uploads/kontrak/'.$addendum->getField('ADDENDUM_FILE_PENYEDIA').'" class="badge badge-success" target="_blank"><i class="fa fa-download"></i> TTD Penyedia</a>';
                          }
                        } else {
                          echo '-';
                        }
                         ?>
                      </td> 
                      <td class="text-center"><?= $addendum->getField('ADDENDUM_KE') ?></td> 
                      <td>
                        <?php 
                        if ($addendum->getField('ADDENDUM_FILE_PERSETUJUAN')) {
                          echo '<a href="uploads/kontrak/'.$addendum->getField('ADDENDUM_FILE_PERSETUJUAN').'" class="badge badge-primary" target="_blank"><i class="fa fa-download"></i> Download</a>';
                        } else {
                          echo '-';
                        }
                         ?>
                      </td> 
                      <td><?= $addendum->getField('JENIS') ?></td> 
                      <td class="text-center">
                        <?php  
                        if ($addendum->getField('TANGGAL_KONTRAK_DARI')) {
                          echo getFormattedDateShort2(dateTimeToPageCheck($addendum->getField('TANGGAL_KONTRAK_DARI'))).'<br> sd <br>'.getFormattedDateShort2(dateTimeToPageCheck($addendum->getField('TANGGAL_KONTRAK_SAMPAI')));
                        } else { echo "-";} ?>
                      </td> 
                      <td class="text-center">
                        <?php  
                        if ($addendum->getField('TANGGAL_PENYELESAIAN_KONTRAK_AWAL')) {
                          echo getFormattedDateShort2(dateTimeToPageCheck($addendum->getField('TANGGAL_PENYELESAIAN_KONTRAK_AWAL'))).'<br> sd <br>'.getFormattedDateShort2(dateTimeToPageCheck($addendum->getField('TANGGAL_PENYELESAIAN_KONTRAK_AKHIR')));
                        } else { echo "-";} ?>
                      </td> 
                      <td><?= currencyToPage($addendum->getField('ADDENDUM_NILAI')) ?></td> 
                      <td><?= $addendum->getField('KETERANGAN') ?></td> 
                    </tr>
                    <?php
                    }
                  } else { echo '<tr><td colspan="11" class="text-center">. : : Tidak ada data : : .</td></tr>';} ?>
                </table>
              </div>
              
              <h4>Denda Keterlambatan</h4>
              <div class="table-responsive">
                <table id="tablesanksi" class="border-double table mb-0 table-bordered mb-2" style="width: 100%">
                  <tr class="backcolornew"> 
                    <th class="text-center" width="280px">Tagihan</th>
                    <th class="text-center">Nilai <br>Sanksi /1000</th>
                    <th class="text-center">Nilai / <br>Bagian Pekerjaan </th>
                    <th class="text-center" width="10px">Hari <br>Keterlambatan</th>
                    <th class="text-center">Nilai Denda</th>
                    <th class="text-center">Cara Bayar</th>
                    <th class="text-center">Bukti Bayar</th>
                    <th class="text-center">Invoice</th>
                    <th class="text-center">Invoice TTD</th>
                  </tr>
                  <?php
                  $this->load->model("Contractingsanksi");
                  $datasanksi = new Contractingsanksi();
                  $datasanksi->selectByParams(array("A.CONTRACTINGREKANANID"=>$reqId));
                  if ($datasanksi->countRow() > 0) {
                    while($datasanksi->nextRow()) {
                    ?>
                    <tr> 
                      <td><?= $datasanksi->getField('PAY_TERMIN_KE') ?></td>
                      <td class="text-center"><?= $datasanksi->getField('NILAI_SANKSI') ?>/1000</td>
                      <td><?= currencyToPage($datasanksi->getField('NILAI_PEKERJAAN')) ?></td>
                      <td class="text-center"><?= $datasanksi->getField('HARI_TERLAMBAT') ?></td>
                      <td><?= currencyToPage($datasanksi->getField('NILAI_DENDA')) ?></td>
                      <td><?= $datasanksi->getField('CARA_BAYAR'); ?>
                      </td>
                      <td>
                        <?php  
                        if ($datasanksi->getField('BUKTI_BAYAR')) {
                          echo '<a href="uploads/payment/'.$datasanksi->getField('BUKTI_BAYAR').'" target="_blank" class="badge badge-primary"><span class="fa fa-download"> Download</span></a>';
                        }
                        ?>
                      </td>
                      <td>
                        <?php  
                        if ($datasanksi->getField('INVOICE_FILE')) {
                          echo '<a href="uploads/payment/'.$datasanksi->getField('INVOICE_FILE').'" target="_blank" class="badge badge-primary"><span class="fa fa-download"> Download</span></a>';
                        }
                        ?>
                      </td>
                      <td>
                        <?php  
                        if ($datasanksi->getField('INVOICE_FILE_TTD')) {
                          echo '<a href="uploads/payment/'.$datasanksi->getField('INVOICE_FILE_TTD').'" target="_blank" class="badge badge-primary"><span class="fa fa-download"> Download</span></a>';
                        }
                        ?>
                      </td>
                    </tr>
                    <?php
                    }
                  } else { echo '<tr><td class="text-center" colspan="9">. : : Tidak ada data : : .</td></tr>';} ?>
                </table> 
              </div>

              <?php 
              $proses4 = new Contractingrekanan();
              $proses4->selectProses4(array("A.CONTRACTINGREKANANID" => $reqId));
              $proses4->firstRow(); 

              $reqContractingRekananProses4Id = $proses4->getField('CONTRACTINGREKANANPROSES4ID') ?: '';
              $reqKahar = $proses4->getField('CR_KAHAR') ?: '';
              $reqPemutusan = $proses4->getField('CR_PEMUTUSAN_ALASAN') ?: '';
              $reqPemutusanFile = $proses4->getField('CR_PEMUTUSAN_FILE') ?: '';
              $reqPemutusanUpdated = $proses4->getField('CR_PEMUTUSAN_UPDATED_DATE') ? explode(' ',$proses4->getField('CR_PEMUTUSAN_UPDATED_DATE')) : '';
              $reqPemutusanUpdatedDate = $reqPemutusanUpdated[0];
              $reqPemutusanUpdatedDate2 = $reqPemutusanUpdated[1];

              if ($reqPemutusan == '') {} else
              {
                echo '<div class="card mb-1 border-blue border-darken-1" style="padding: 5px 10px 0 10px; background-color: #fff3f3">
                        <div class="row">
                          <div class="form-group col-md-12 mb-2">
                            <small>'.getFormattedDate($reqPemutusanUpdatedDate).' '.$reqPemutusanUpdatedDate2.'</small><br><b>Alasan Pemutusan Kontrak</b>: <i>'.$reqPemutusan.'</i><br>
                            <a href="uploads/kontrak/'.$reqPemutusanFile.'" target="_blank" class="badge badge-primary"><span class="fa fa-download"></span> Download File</a>
                          </div>
                        </div>
                      </div>'; 
              }
              ?>

              <?php 
              $dataJamPel = new Contractingjaminanpemeliharaan();
              $dataJamPel->selectByParams(array("CONTRACTINGREKANANID"=>$reqId));
              $dataJamPel->firstRow();
              $id = $dataJamPel->getField('CONTRACTING_JAMPEL_ID');
              if ($id != '') {
              ?>
              <h4>Jaminan Pemeliharaan</h4>
              <div class="form-actions table-responsive">
                <table id="tabletermin" class="border-double table mb-0 table-bordered mb-2" style="width: 100%"> 
                  <thead>
                    <tr class="backcolornew">
                      <td>Nomor</td>
                      <td>Nilai</td>
                      <td>Masa</td>
                      <td>Tanggal Mulai</td>
                      <td>Tanggal Akhir</td>
                      <td>File Jaminan</td>
                    </tr>
                  </thead>
                  <tbody id="tbodyDeliverable">   
                      <tr>
                        <td> <?= $dataJamPel->getField('NOMOR') ?></td> 
                        <td> <?= $dataJamPel->getField('NILAI') ?></td> 
                        <td> <?= $dataJamPel->getField('MASA'); ?> Bulan</td>
                        <td> <?= getFormattedDateShort2(dateTimeToPageCheck($dataJamPel->getField('TANGGAL_MULAI'))) ?></td> 
                        <td> <?= getFormattedDateShort2(dateTimeToPageCheck($dataJamPel->getField('TANGGAL_AKHIR'))) ?></td> 
                        <td>  
                          <?php 
                          if (file_exists('uploads/payment/'.$dataJamPel->getField('FILE_JAMINAN')) && $dataJamPel->getField('FILE_JAMINAN') != '' ) {
                            echo '<a href="uploads/payment/'.$dataJamPel->getField('FILE_JAMINAN').'" target="_blank" class="badge badge-primary"><span class="fa fa-download"> File Jaminan</span></a>';
                          } ?>
                        </td> 
                      </tr> 
                     
                  </tbody>
                </table> 
              </div>
              <?php 
              } ?>

              <!-- <hr>
              <h4>Sanksi dan Denda Keterlambatan </h4>
              <table id="tablesanksi" class="border-double table mb-0 table-bordered mb-2" style="width: 100%">
                <tr class="backcolornew">
                  <th>Nilai Sanksi</th>
                  <th>Nilai / Bagian Pekerjaan </th>
                  <th width="100px">Hari Keterlambatan</th>
                  <th>Nilai Denda</th>
                </tr>
                <?php
                // $this->load->model("Contractingsanksi");
                // $datasanksi = new Contractingsanksi();
                // $datasanksi->selectByParams(array("CONTRACTINGREKANANID"=>$reqId));
                // if ($datasanksi->countRow() > 0) {
                //   while($datasanksi->nextRow()) {
                  ?>
                  <tr>
                    <td><?php // echo $datasanksi->getField('NILAI_SANKSI') ?>/1000</td>
                    <td><?php // echo currencyToPage($datasanksi->getField('NILAI_PEKERJAAN')) ?></td>
                    <td><?php // echo $datasanksi->getField('HARI_TERLAMBAT') ?></td>
                    <td><?php // echo currencyToPage($datasanksi->getField('NILAI_DENDA')) ?></td>
                  </tr>
                  <?php
                //   }
                // } else { echo '<tr><td colspan="6">. : : Tidak ada data : : .</td></tr>';} ?>
              </table> -->
             <!--  <div class="card mb-1 border-blue border-darken-1">
                <div class="card-content">
                  <div class="p-1">
                  <?php
                  // $this->load->model("Contractingsanksi");
                  // $datasanksi = new Contractingsanksi();
                  // $datasanksi->selectByParamsKetentuan(array("CONTRACTINGREKANANID"=>$reqId, "JENIS" => "1"));
                  // if ($datasanksi->countRow() > 0) {
                  //   while($datasanksi->nextRow()) {
                    ?> <?php // echo $datasanksi->getField('KETERANGAN') ?>
                    <?php
                  //   }
                  // } else { echo '. : : Tidak ada keterangan : : .';} ?>
                  </div>
                </div>
              </div> -->

             <!--  <hr>
              <h4>Sanksi dan Denda Kelalaian
              </h4>
              <div class="card mb-1 border-blue border-darken-1">
                <div class="card-content">
                  <div class="p-1">
                  <?php
                  // $this->load->model("Contractingsanksi");
                  // $datasanksi = new Contractingsanksi();
                  // $datasanksi->selectByParamsKetentuan(array("CONTRACTINGREKANANID"=>$reqId, "JENIS" => "2"));
                  // if ($datasanksi->countRow() > 0) {
                  //   while($datasanksi->nextRow()) {
                    ?> <?php // echo $datasanksi->getField('KETERANGAN') ?>
                    <?php
                  //   }
                  // } else { echo '. : : Tidak ada keterangan : : .';} ?>
                  </div>
                </div>
              </div> -->

              <div class="col-md-12" style="padding:10px; margin-bottom:5%">
                <?php
                $cekPenilaianTotal = new PaketPenilaian();
                $cekPenilaianTotal->selectPenilaian(array("PAKET_ID" => $reqPaketId, "CONTRACTINGREKANANID" => $reqId), "-1", "-1", " AND A.REKANAN_ID = ".$reqRakananId."");
                $cekPenilaianTotal->firstRow();
                $reqTemplate = $cekPenilaianTotal->getField("TEMPLATE");
                ?>
               <a href="main/loadUrl/report/paket_penilaian_pdf/?reqId=<?=$reqId?>&pemenang=<?=$reqRakananId?>" target="_blank" class="<?= CLASS_BTN_INFO ?> mr-1 pull-right"><?= BTN_PRINT ?> Penilaian </a>
              </div>
            <?php
            } ?>

            <!-- ---------------------------  KONTRAK PAYUNG ------------------------ -->
            <!-- -------------------------------------------------------------------- -->
            <?php
            if ($reqJnsKontrak == '3')
            {
              $this->load->library("paketinfo"); $paketInfo = new paketinfo();
              $this->load->model("Paketpemenang");
              $getpaket_pemenang = new Paketpemenang();
              $getpaket_pemenang2 = new Paketpemenang();
              $getpaket_pemenang3 = new Paketpemenang();
              $getpaket_pemenang4 = new Paketpemenang();

              $paketInfo->getPaket($reqPaketId);
              $reqNama = $paketInfo->nama;
              $bidding = $paketInfo->bidding;
              $reqMultiPemenang = $paketInfo->multi_pemenang;
              $nilaiKontrak = 0;

              $getpaket_pemenang3->selectByParams(array("PAKET_ID" => $reqPaketId), -1, -1);
              $getpaket_pemenang4->selectByParams(array("PAKET_ID" => $reqPaketId), -1, -1);
              while($getpaket_pemenang4->nextRow())
              {
                $contractingrekananSPPBJ = new Contractingrekanan();
                $contractingrekananSPPBJ->selectViewPKSSPK(array("A.PAKET_ID" => $reqPaketId, "A.REKANAN_ID" => $getpaket_pemenang4->getField("REKANAN_ID")));
                $contractingrekananSPPBJ->firstRow();
                $nilaiKontrak += $contractingrekananSPPBJ->getField("CR_NILAI_KONTRAK") / $getpaket_pemenang3->countRow();
              }
              ?>
              <h4>Surat Pesanan</h4>
              <?php
              $no = 1;
              $nilaiKontrak = 0;
              if ($reqMultiPemenang == '0') {
                $getpaket_pemenang->selectByParams(array("PAKET_ID" => $reqPaketId, "PERINGKAT" => '1'), -1, -1);
              } else {
                // echo "Multi Pemanang";
                $getpaket_pemenang->selectByParams(array("PAKET_ID" => $reqPaketId), -1, -1);
              }
              while($getpaket_pemenang->nextRow())
              {
                $contractingrekananSPPBJ = new Contractingrekanan();
                $contractingrekananSPPBJ->selectViewPKSSPK(array("A.PAKET_ID" => $reqPaketId, "A.REKANAN_ID" => $getpaket_pemenang->getField("REKANAN_ID")));
                $contractingrekananSPPBJ->firstRow();
                $nilaiKontrak += $contractingrekananSPPBJ->getField("CR_NILAI_KONTRAK") / $getpaket_pemenang->countRow();
                $v = $this->libkontrak->getNilaiKontrakPenyedia(" AND REKANAN_ID = ".$getpaket_pemenang->getField("REKANAN_ID")." AND CONTRACTINGREKANANID = ".$reqId);
                ?>
                <div class="form-actions table-responsive">
                  <table class="table table-bordered table-hover">
                    <tbody>
                      <tr>
                        <td colspan="2" style="background-color: #283c4d; color:#fff"> <?= $getpaket_pemenang->getField("NAMA"); ?></td>
                      </tr>
                      <tr>
                        <td width="30%"> No. <?= $reqJnsKontrakStr ?></td>
                        <td> <?= $contractingrekananSPPBJ->getField("CR_LEGAL_NOMOR_PKS") ?: '-'; ?> </td>
                      </tr>
                      <tr>
                        <td>Nilai Kontrak Maksimal:</td>
                        <td><?= currencyToPage($v['nilai_kontrak']); ?></td>
                      </tr>
                      <tr>
                        <td colspan="2">
                            <h4>List Surat Pesanan</h4>
                            <table id="deliveriable" class="border-double table mb-0 table-bordered mb-2" style="width: 100%">
                              <tr class="backcolornew">
                                <th width="40%">Nomor</th>
                                <th width="25%">Tanggal</th>
                                <!-- <th>Material</th> -->
                                <!-- <th width="5px">Qty</th> -->
                                <!-- <th width="25%">Harga Satuan</th> -->
                                <th width="20%" style="text-align: center;">Total</th>
                                <th width="6%" class="text-center">Aksi</th>
                              </tr>
                              <?php
                              $this->load->model("Contractingsuratpesanan");
                              $datasuratpesananmaterial = new Contractingsuratpesanan();
                              $datasuratpesananmaterial->selectByParamsSuratPesanan(array("CONTRACTINGREKANANID" => $reqId, "B.REKANAN_ID" => $getpaket_pemenang->getField("REKANAN_ID")));
                              if ($datasuratpesananmaterial->countRow() > 0) {
                                $no=1;
                                $rekId = $getpaket_pemenang->getField("REKANAN_ID");
                                $sumTotal.$rekId = 0;
                                while($datasuratpesananmaterial->nextRow()) {
                                  $sumTotal.$rekId += $datasuratpesananmaterial->getField('TOTAL');
                                ?>
                                <tr>
                                  <td><?= $datasuratpesananmaterial->getField('NOMOR_SURAT') ?></td>
                                  <td><?= getFormattedDate($datasuratpesananmaterial->getField('TANGGAL')) ?></td>
                                  <td style="text-align:right;"><?= currencyToPage($datasuratpesananmaterial->getField('TOTAL')) ?></td>
                                  <td>
                                    <a onclick="openAdd('main/loadUrlKontrak/kontrak/contracting_update_surat_pesanan_material?reqSuratPesananId=<?= $datasuratpesananmaterial->getField('SURATPESANANID') ?>&reqview=1')"> <i class="fa fa-eye"></i></a>

                                  </td>
                                </tr>
                                <?php
                                $no++;
                                }
                              } else { echo '<tr><td colspan="4">. : : Tidak ada data : : .</td></tr>';} ?>
                              <tfoot>
                                <tr>
                                  <td colspan="2" style="text-align: right;">
                                  TOTAL
                                    <?php
                                    if ($datasuratpesananmaterial->countRow() > 0 && $sumTotal.$rekId > $v['nilai_kontrak']) {
                                       echo '<br><span class="badge badge-danger">Total Surat Pesanan diatas Nilai Kontrak</span>';
                                       $colorSumTotal = '<b style="color:red">'.currencyToPage($sumTotal.$rekId).'</b';
                                     } else {
                                       $colorSumTotal = '<b style="color:black">'.currencyToPage($sumTotal.$rekId).'</b';
                                     } ?>
                                  </td>
                                  <td style="text-align:right;">
                                    <?php
                                    if ($datasuratpesananmaterial->countRow() > 0 && $sumTotal.$rekId > $v['nilai_kontrak']) {}
                                    else {
                                      echo $colorSumTotal;
                                    }
                                    ?>

                                  </td>
                                </tr>
                              </tfoot>
                            </table>
                        </td>
                      </tr>
                    </tbody>
                  </table>
              <?php
              $no++;
              } ?>

              <h4>SPPBJ</h4>
              <table class="table table-bordered table-hover">
                <tbody>
                  <tr class="backcolornew">
                    <th width="2%">No</th>
                    <th>No. SPPBJ</th>
                    <th>Penyedia</th>
                    <th width="2%">Aksi</th>
                  </tr>
                  <?php
                  $getpaket_pemenangSppbj = new Paketpemenang();
                  if ($reqMultiPemenang == '0') {
                    $getpaket_pemenangSppbj->selectByParams(array("PAKET_ID" => $reqPaketId, "PERINGKAT" => '1'), -1, -1);
                  } else {
                    // echo "Multi Pemanang";
                    $getpaket_pemenangSppbj->selectByParams(array("PAKET_ID" => $reqPaketId), -1, -1);
                  }
                  $no = 1;
                  while($getpaket_pemenangSppbj->nextRow())
                  {
                    $contractingrekananSPPBJ = new Contractingrekanan();
                    $contractingrekananSPPBJ->selectViewSPPBJ(array("A.PAKET_ID" => $paket_id, "A.REKANAN_ID" => $getpaket_pemenangSppbj->getField("REKANAN_ID")));
                    $contractingrekananSPPBJ->firstRow();
                    ?>
                    <tr>
                      <td width="2%"><?= $no ?></td>
                      <td>
                        <?= $contractingrekananSPPBJ->getField("CR_SPPBJ_CODE") ?: '<span class="badge badge-danger">Belum di buat</span>'; ?>
                      </td>
                      <td><?= $getpaket_pemenangSppbj->getField("NAMA"); ?></td>
                      <td width="2%">
                          <a href="main/loadUrl/report/sppbj_multi_pdf?reqId=<?= $contractingrekananSPPBJ->getField("CONTRACTINGREKANANID") ?>&reqRekananId=<?= $getpaket_pemenangSppbj->getField("REKANAN_ID") ?>" class="btn-sm <?= CLASS_BTN_INFO ?>" target="_blank"> <span class="fa fa-print"></span> </a>
                      </td>
                    </tr>
                  <?php
                  $no++;
                  } ?>
                </tbody>
              </table>

              <div class="form-actions mt-3">
                <h4>Daftar Barang Jasa</h4>
                <table id="deliveriable" class="border-double table mb-0 table-bordered mb-2" style="width: 100%">
                  <tr class="backcolornew">
                    <th width="5px">No</th>
                    <th>Deskripsi</th>
                    <th width="10%">Vol/Qty</th>
                    <th width="10%">Satuan</th>
                    <th width="20%">Harga Satuan</th>
                  </tr>
                  <?php
                  $this->load->model("Contractingmaterial");
                  $datamaterial = new Contractingmaterial();
                  $datamaterial->selectByParams(array("CONTRACTINGREKANANID"=>$reqId));
                  if ($datamaterial->countRow() > 0) {
                    $no=1;
                    while($datamaterial->nextRow()) {
                    ?>
                    <tr>
                      <td width="10px"><?= $no; ?></td>
                      <td><?= $datamaterial->getField('NAMA') ?></td>
                      <td><?= $datamaterial->getField('QTY'); ?></td>
                      <td><?= $datamaterial->getField('SATUAN_STR'); ?></td>
                      <td><?= currencyToPage($datamaterial->getField('HARGA_SATUAN')) ?></td>
                    </tr>
                    <?php
                    $no++;
                    }
                  } else { echo '<tr><td colspan="3">. : : Tidak ada data : : .</td></tr>';} ?>
                </table>
              </div>


              <?php
              $datamaterialCek = new Contractingmaterial();
              $datamaterialCek->selectByParams(array("CONTRACTINGREKANANID"=>$reqId));
              $datamaterialCek->firstRow();
              $materialSifat = $datamaterialCek->getField('SIFAT'); // 1:Berubah, 2:Tetap
              if ($materialSifat == '1') {
              ?>
                <h4 class="mt-1">SPMK</h4>
                <div class="form-actions">
                  <table class="border-double table mb-0 table-bordered mb-2">
                    <tbody>
                      <tr class="backcolornew">
                        <th width="2%">No</th>
                        <th>No. SPMK</th>
                        <th>Penyedia</th>
                        <th width="2%">Aksi</th>
                      </tr>
                      <?php
                      $getpaket_pemenangSpmk = new Paketpemenang();
                      if ($reqMultiPemenang == '0') {
                        $getpaket_pemenangSpmk->selectByParams(array("PAKET_ID" => $reqPaketId, "PERINGKAT" => '1'), -1, -1);
                      } else {
                        // echo "Multi Pemanang";
                        $getpaket_pemenangSpmk->selectByParams(array("PAKET_ID" => $reqPaketId), -1, -1);
                      }
                      $totalPemenang = $getpaket_pemenangSpmk->countRow();
                      $no = 1;
                      while($getpaket_pemenangSpmk->nextRow())
                      {
                        $contractingrekananSPMK = new Contractingrekanan();
                        $contractingrekananSPMK->selectSPMK(array("A.CONTRACTINGREKANANID" => $reqId, "A.REKANAN_ID" => $getpaket_pemenangSpmk->getField("REKANAN_ID")));
                        $contractingrekananSPMK->firstRow();
                        ?>
                        <tr>
                          <td width="2%"><?= $no ?></td>
                          <td>
                            <?= $contractingrekananSPMK->getField("NOMOR") ?: '<span class="badge badge-danger">Belum di buat</span>'; ?>
                          </td>
                          <td><?= $getpaket_pemenangSpmk->getField("NAMA"); ?></td>
                          <td width="2%">
                            <a href="main/loadUrl/report/spmk_multi_pdf?reqId=<?= $contractingrekananSPMK->getField("CONTRACTINGREKANANID") ?>&reqRekananId=<?= $getpaket_pemenangSpmk->getField("REKANAN_ID") ?>" class="btn-sm <?= CLASS_BTN_INFO ?>" target="_blank"> <span class="fa fa-print"></span> </a>
                          </td>
                        </tr>
                      <?php
                      $no++;
                      } ?>
                    </tbody>
                  </table>
                </div>
              <?php
              } ?>

              <!-- <div class="form-actions mt-3 table-responsive">
                <h4>Sanksi dan Denda Keterlambatan</h4>
                <table id="tablesanksi" class="border-double table mb-0 table-bordered mb-2">
                  <tr class="backcolornew">
                    <th>Nilai Sanksi</th>
                    <th>Nilai / Bagian Pekerjaan </th>
                    <th width="100px">Hari Keterlambatan</th>
                    <th>Nilai Denda</th>
                  </tr>
                  <?php
                  // $this->load->model("Contractingsanksi");
                  // $datasanksi = new Contractingsanksi();
                  // $datasanksi->selectByParams(array("CONTRACTINGREKANANID"=>$reqId));
                  // if ($datasanksi->countRow() > 0) {
                  //   while($datasanksi->nextRow()) {
                    ?>
                    <tr>
                      <td><?php // echo $datasanksi->getField('NILAI_SANKSI') ?>/1000</td>
                      <td><?php // echo currencyToPage($datasanksi->getField('NILAI_PEKERJAAN')) ?></td>
                      <td><?php // echo $datasanksi->getField('HARI_TERLAMBAT') ?></td>
                      <td><?php // echo currencyToPage($datasanksi->getField('NILAI_DENDA')) ?></td>
                    </tr>
                    <?php
                  //   }
                  // } else { echo '<tr><td colspan="6">. : : Tidak ada data : : .</td></tr>';} ?>
                </table>
                <div class="card mb-1 border-blue border-darken-1 table-responsive">
                  <div class="card-content">
                    <div class="p-1">
                    <?php
                    // $this->load->model("Contractingsanksi");
                    // $datasanksi = new Contractingsanksi();
                    // $datasanksi->selectByParamsKetentuan(array("CONTRACTINGREKANANID"=>$reqId, "JENIS" => "1"));
                    // if ($datasanksi->countRow() > 0) {
                    //   while($datasanksi->nextRow()) {
                      ?> <?php // echo $datasanksi->getField('KETERANGAN') ?>
                      <?php
                    //   }
                    // } else { echo '. : : Tidak ada keterangan : : .';} ?>
                    </div>
                  </div>
                </div>

              </div> -->

              <h4 class="mt-1">Pembayaran</h4>
              <div class="form-actions">

                <table id="tabletermin" class="border-double table mb-0 table-bordered mb-2" style="width: 100%">
                  <tr class="backcolornew">
                    <th>Nomor</th>
                    <th width="150px">Tanggal</th>
                    <?php if ($reqMetodePembayaran == 2) { ?>
                    <th class="text-center">Pembayaran</th>
                    <?php
                    } ?>
                    <th width="100px">Progres</th>
                    <th width="50px" class="text-center">Dokumen</th>
                    <th width="100px">Status</th>
                  </tr>
                  <?php
                  $this->load->model("Contractingpayment");
                  $datapayment = new Contractingpayment();
                  $datapayment->selectByParams(array("CONTRACTINGREKANANID"=>$reqId));
                  if ($datapayment->countRow() > 0) {
                    $tombolTutup = 0;
                    while($datapayment->nextRow()) {
                      $statusPay = str_replace(' ','',$datapayment->getField('PAY_STATUS'));

                      if ($datapayment->getField('PAY_LAMPIRAN') && $statusPay == 'Selesai') {
                        $tombolTutup++;
                      }
                    ?>
                    <tr>
                      <td><?= $datapayment->getField('PAY_NOMOR') ?></td>
                      <td><?= getFormattedDateShort($datapayment->getField('PAY_DATE')) ?></td>
                      <?php if ($reqMetodePembayaran == 2) { ?>
                      <td class="text-center"><?= $datapayment->getField('PAY_TERMIN_KE') ?></td>
                      <?php
                      } ?>
                      <!-- <td><?= currencyToPage($datapayment->getField('PAY_NILAI')) ?></td> -->
                      <td><?= $datapayment->getField('PAY_PROGRES') ?> %</td>
                      <!-- <td><?= $datapayment->getField('PAY_KETERANGAN') ?></td> -->
                      <td class="text-center">
                      <?php
                        if (file_exists('uploads/payment/'.$datapayment->getField('PAY_LAMPIRAN_BAPP')) && $datapayment->getField('PAY_LAMPIRAN_BAPP') != '' ) {
                          echo '<a href="uploads/payment/'.$datapayment->getField('PAY_LAMPIRAN_BAPP').'" target="_blank" class="badge badge-primary"><span class="fa fa-download"> BAPP</span></a>';
                        } else {
                          // echo '-';
                        }
                      ?>
                      <?php
                        if (file_exists('uploads/payment/'.$datapayment->getField('PAY_LAMPIRAN')) && $datapayment->getField('PAY_LAMPIRAN') != '' ) {
                          echo '<a href="uploads/payment/'.$datapayment->getField('PAY_LAMPIRAN').'" target="_blank" class="badge badge-primary" style="margin-top:3%"><span class="fa fa-download"> Berita Acara</span></a>';
                        } else {
                          // echo '-';
                        }
                      ?>
                      </td>
                      <td>
                        <?php
                        if($statusPay == 'Selesai') {
                          echo '<span class="badge badge-primary">'.$datapayment->getField('PAY_STATUS').'</span>';
                        } else {
                          echo '<span class="badge badge-danger">'.$datapayment->getField('PAY_STATUS').'</span>';
                        }
                        ?>
                      </td>
                    </tr>
                    <?php
                    }
                  } else {
                    if ($reqMetodePembayaran == 2) {
                      echo '<tr><td colspan="6">. : : Tidak ada data : : .</td></tr>';
                    } else {
                      echo '<tr><td colspan="5">. : : Tidak ada data : : .</td></tr>';
                    }
                  } ?>
                </table>
              </div>

              <?php
              if ($contractingprosesid >= 6) { ?>
              <h4 class="mt-1">Penilaian</h4>
              <div class="form-actions">
                <table class="table table-bordered table-hover">
                  <tbody>
                    <tr class="backcolornew">
                      <th width="2%">No</th>
                      <th>No. <?= $reqJnsKontrakStr ?></th>
                      <th>Penyedia</th>
                      <th width="100px">Aksi</th>
                    </tr>
                    <?php
                    $no = 1;
                    if ($reqMultiPemenang == '0') {
                      $getpaket_pemenang->selectByParams(array("PAKET_ID" => $reqPaketId, "PERINGKAT" => '1'), -1, -1);
                    } else {
                      // echo "Multi Pemanang";
                      $getpaket_pemenang->selectByParams(array("PAKET_ID" => $reqPaketId), -1, -1);
                    }

                      $totalPemenang = $getpaket_pemenang->countRow();
                    // }
                    while($getpaket_pemenang->nextRow())
                    {
                      $cekPenilaianTotal = new PaketPenilaian();
                      $cekPenilaianTotal->selectPenilaian(array("PAKET_ID" => $reqPaketId, "CONTRACTINGREKANANID" => $reqId), "-1", "-1", " AND REKANAN_ID IN (".$getpaket_pemenang->getField("REKANAN_ID").")");
                      $cekPenilaianTotal->firstRow();
                      $reqTemplate = $cekPenilaianTotal->getField("TEMPLATE");

                      $contractingrekananSPPBJ = new Contractingrekanan();
                      $contractingrekananSPPBJ->selectViewPKSSPK(array("A.PAKET_ID" => $reqPaketId, "A.REKANAN_ID" => $getpaket_pemenang->getField("REKANAN_ID")));
                      $contractingrekananSPPBJ->firstRow();
                      ?>
                      <tr>
                        <td width="2%"><?= $no ?></td>
                        <td>
                          <?= $contractingrekananSPPBJ->getField("CR_LEGAL_NOMOR_PKS") ?: '-'; ?>
                        </td>
                        <td><?= $getpaket_pemenang->getField("NAMA"); ?>
                        </td>
                        <td>
                          <a href="main/loadUrl/report/paket_penilaian_multi_pdf/?reqId=<?=$reqId?>&pemenang=<?=$getpaket_pemenang->getField("REKANAN_ID")?>&reqTemplate=<?= $reqTemplate ?>" target="_blank" class="badge badge-info" style="margin-bottom: 1%;"><i class="fa fa-print"></i></a>
                        </td>
                      </tr>
                      <!--   -->
                    <?php
                    $no++;
                    } ?>
                  </tbody>
                </table>
              </div>

              <h4 class="mt-1">Hasil Pekerjaan</h4>
              <div class="form-actions">
                <table class="table table-bordered table-hover">
                  <tbody>
                    <tr class="backcolornew">
                      <th width="2%">No</th>
                      <th>No. BAST</th>
                      <th>Tanggal</th>
                      <th>Penyedia</th>
                      <th width="100px">Status</th>
                    </tr>
                    <?php
                    if ($reqMultiPemenang == '0') {
                      $getpaket_pemenang2->selectByParams(array("PAKET_ID" => $reqPaketId, "PERINGKAT" => '1'), -1, -1);
                    } else {
                      // echo "Multi Pemanang";
                      $getpaket_pemenang2->selectByParams(array("PAKET_ID" => $reqPaketId), -1, -1);
                    }
                    $no = 1;
                    while($getpaket_pemenang2->nextRow())
                    {
                      $proses5 = new Contractingrekanan();
                      $proses5->selectProses5(array("A.CONTRACTINGREKANANID" => $reqId, "A.REKANAN_ID" => $getpaket_pemenang2->getField("REKANAN_ID")));
                      $proses5->firstRow();
                      ?>
                      <tr>
                        <td width="2%"><?= $no ?></td>
                        <td><?= $proses5->getField("CR_BAST_PEKERJAAN_NOMOR") ?: '-'; ?></td>
                        <td><?= getFormattedDate($proses5->getField("CR_BAST_PEKERJAAN_TANGGAL")) ?: '-'; ?></td>
                        <td><?= $getpaket_pemenang2->getField("NAMA"); ?>
                        <td><?php if($proses5->getField("CR_BAST_PEKERJAAN_STATUS") == '1') { echo '<span class="badge badge-primary">Selesai</span>'; } else { echo '<span class="badge badge-danger">Proses</span>'; } ?></td>
                        </td>
                      </tr>
                      <!--   -->
                    <?php
                    $no++;
                    } ?>
                  </tbody>
                </table>
              </div>
              <?php
              } ?>

            <?php
            } ?>



            <!-- <a href="<?php // $cp_link.'?tahun='.$this->session->userdata('setTahunKontrak'); ?>" class="<?php // CLASS_BTN_DANGER ?>"> <?php // BTN_KEMBALI ?> </a> -->
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
