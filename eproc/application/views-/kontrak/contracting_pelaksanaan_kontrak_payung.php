<?php
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 * 
 */

$reqId = httpFilterRequest("reqId");
$getTahun = $this->session->userdata('setTahunKontrak');

$this->libsession->cekSession();
$this->libsession->cekSessionKontrakPPK($reqId);

$this->load->library("kauth");  $userLogin = new kauth();

$this->LEGAL =  $this->kauth->getInstance()->getIdentity()->LEGAL;

include_once("functions/string.func.php");
include_once("functions/date.func.php");
include_once("functions/default.func.php");

$this->load->library("paketinfo"); $paketInfo = new paketinfo();
$this->load->model("Contracting");
$this->load->model("Contractingrekanan");
$this->load->model("Paketpemenang");

$contracting = new Contracting();
$spkpks = new Contractingrekanan();
$legal = new Contractingrekanan();
$kontrakPayung = new Contractingrekanan();
$getpaket_pemenang = new Paketpemenang();
$getpaket_pemenang2 = new Paketpemenang();

$contracting->selectByParams(array("A.CONTRACTINGREKANANID" => $reqId));
$contracting->firstRow();

$spkpks->selectViewPKSSPK(array("A.CONTRACTINGREKANANID" => $reqId));
$spkpks->firstRow();

$reqJenisKontrak = $contracting->getField('JENIS_KONTRAK') ?: '-';
$reqCode = $spkpks->getField('CR_CODE') ?: '-';
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
$reqNilaiKontrak = $spkpks->getField('CR_NILAI_KONTRAK') ?: '0';
$reqMetodePembayaran = $spkpks->getField('CR_METODE_PEMBAYARAN') ?: '-';
$reqPihak1Nama = $spkpks->getField('CR_PIHAK1_NAMA') ?: '-';
$reqPihak1Jabatan = $spkpks->getField('CR_PIHAK1_JABATAN') ?: '-';
$reqPihak2Nama = $spkpks->getField('CR_PIHAK2_NAMA') ?: '-';
$reqPihak2Jabatan = $spkpks->getField('CR_PIHAK2_JABATAN') ?: '-';
$reqPihak2 = $spkpks->getField('CR_PIHAK2_PERUSAHAAN') ?: '-';
$reqCreatedBy = $spkpks->getField('CR_CREATED_BY') ?: '-';
$reqContractingStatusKontrakId = $spkpks->getField('CONTRACTINGSTATUSKONTRAKID') ?: '-';
$reqRakananId = $spkpks->getField('REKANAN_ID') ?: '-';

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

$paketInfo->getPaket($reqPaketId);
$bidding = $paketInfo->bidding;
$reqMultiPemenang = $paketInfo->multi_pemenang;

if ($reqMultiPemenang == '0') {
  $getpaket_pemenang->selectByParams(array("PAKET_ID" => $reqPaketId, "PERINGKAT" => '1'), -1, -1); 
  $getpaket_pemenang2->selectByParams(array("PAKET_ID" => $reqPaketId, "PERINGKAT" => '1'), -1, -1);
} else {
  $getpaket_pemenang->selectByParams(array("PAKET_ID" => $reqPaketId), -1, -1);
  $getpaket_pemenang2->selectByParams(array("PAKET_ID" => $reqPaketId), -1, -1);
}

  $totalPemenang = $getpaket_pemenang->countRow();

  $proses4 = new Contractingrekanan();
  $proses4->selectProses4(array("A.CONTRACTINGREKANANID" => $reqId));
  $proses4->firstRow();
  $reqPerubahan = $proses4->getField('CR_PERUBAHAN') ?: 0;
  $reqPenyesuaian = $proses4->getField('CR_PENYESUAIAN') ?: 0;
  $reqKahar = $proses4->getField('CR_KAHAR') ?: 0;
  $reqBerakhir = $proses4->getField('CR_BERAKHIR') ?: 0;
  $reqPemutusan = $proses4->getField('CR_PEMUTUSAN') ?: 0;
  $reqKesempatan = $proses4->getField('CR_KESEMPATAN') ?: 0;
  $reqDenda = $proses4->getField('CR_DENDA') ?: 0;
  $reqKhusus = array($reqPerubahan, $reqPenyesuaian, $reqKahar,$reqBerakhir, $reqPemutusan, $reqKesempatan, $reqDenda);
  // echo "<pre>"; print_r($reqKhusus);
  if (in_array("1", $reqKhusus)) {
    $reqContractingStatusKontrakId = 6;
  }
  else
  {
    $kontrakPayung->selectProses1(array("A.CONTRACTINGREKANANID" => $reqId));
    $totalStatus2 = 0;
    $totalStatus1 = 0;
    $totalStatus0 = 0;
    while($kontrakPayung->nextRow())
    {
      if ($kontrakPayung->getField('CONTRACTINGSTATUSKONTRAKID') == '5') {
        $totalStatus2++;
      }

      if ($kontrakPayung->getField('CONTRACTINGSTATUSKONTRAKID') == '4') {
        $totalStatus1++;
      }

      if ($kontrakPayung->getField('CONTRACTINGSTATUSKONTRAKID') == '3') {
        $totalStatus0++;
      }
    }
    // echo $totalPemenang.'-'.$totalStatus2.'-'.$totalStatus1.'-'.$totalStatus0;

    if ($totalStatus0 == 0) {
      if ($totalStatus1 == 0) {
        if ($totalStatus2 == 0) {
        } else {
          $reqContractingStatusKontrakId = 5; // Status SPPBJ Penyedia Setuju
        }
      } else {
        $reqContractingStatusKontrakId = 4; // Status SPPBJ Persetujuan Penyedia
      }
    } else {
        $reqContractingStatusKontrakId = 3; // Status SPPBJ dibuat
    }
  }
// }

?>

<link rel="stylesheet" type="text/css" href="<?=base_url() ?>assets/new/vendors/css/ui/jquery-ui.min.css">
<link rel="stylesheet" type="text/css" href="<?=base_url() ?>assets/new/css/plugins/ui/jqueryui.css">
<script src="<?=base_url()?>assets/new/js/scripts/ui/jquery-ui/navigations.js"></script>
<style type="text/css">
  small { font-weight: bold; font-size: 11.5px }
  tr.backcolornew {
    background: #b11016 !important;
    color: #fff;
  }
  table.no-border tr td {
    border: transparent !important;
    padding: 2px 5px !important;
  }
</style>

<script type="text/javascript">
function prosesKontrak(flow,aidi,m) {
  $.messager.confirm('Konfirmasi',m,function(r){
    if (r){
      $.getJSON("contracting_json/proseskontrakmulti/?reqAidi="+aidi+"&flow="+flow,
      function(data){
        alertSuccess2(data.PESAN);
        setTimeout(function() {
          if (data.FLOW == '6') {
            location.href = "kontrak/index/contracting_pengelolaan?tahun=<?= $getTahun ?>";
          } else {
            location.reload();
          }
        }, 2000);
      });
    }
  });
}

function publishFile(delele_link, id, stat)
  {
    if (stat == '1') {
      var messa = 'Kirim Dokumen ke Penyedia ?';
    } else {
      var messa = 'Batal kirim Dokumen ke Penyedia ?';
    }

    $.messager.confirm('Konfirmasi',messa,function(r){
      if (r){
        var jqxhr = $.get( delele_link+'?reqId='+id+'&status='+stat, function(data) {
        })
        .done(function(data) {
          alertSuccess2(data);
          setTimeout(function() {
            document.location.reload();
          }, 2000);
        })
        .fail(function() {
          alertError2('Data gagal diproses, silahkan coba kembali'); // gagal
        });
      }
    });
  }
</script>

<?= $this->libchat->kontrak($reqId); ?>
<div class="row">
  <div class="col-md-3 col-sm-3">
    <div class="jqueryui-ele-container">
      <?= $this->libkontrak->getMenu($reqId); ?>
    </div>
  </div>
  <div class="col-md-9 col-sm-9">
    <div class="card">
      <div class="card-content collapse show border-info border-darken-2">
        <div class="card-body">
          <h4 class="mb-2">Surat Pesanan</h4>

          <div class="row mb-1">
            <div class="col-md-12">
              <?php
              // Yang kirim ke penyedia bagian pengelola kontrak & Data PKS Sudah di isi, pemeriksa
              if ($this->LEGAL != '1' && $reqLegalNomorPKS != '-' && $this->USER_TYPE_ID != '20') {
                // echo $this->libkontrak->getStatusKontrakTeruskanMulti($reqContractingStatusKontrakId,$reqId,$this->USER_TYPE_ID,$this->LEGAL,'SPK-PKS');
              } else {
                if ($reqContractingStatusKontrakId == '4') { // informasi proses persetujuan penyedia untuk peng. kontrak
                  // echo $this->libkontrak->getStatusKontrakTeruskanMulti($reqContractingStatusKontrakId,$reqId,$this->USER_TYPE_ID,$this->LEGAL,'SPK-PKS');
                }
              }
              ?>
            </div>
          </div>

          <div class="card mb-1 border-blue border-darken-1">
            <div class="card-content">
              <div class="p-1">
                <h5>Dokumen Surat Pesanan
                <?php
                if ($reqContractingStatusKontrakId >= '2') { // Penyedia sudah approve ?>
                <small style="font-size:0.7em">
                  <a onclick="openAdd('main/loadUrlKontrak/kontrak/contracting_file_add_multi?reqAidi=<?= $reqId ?>&reqProses=3&reqJenis=Surat Pesanan')" class="badge badge-primary pull-right mr-1 text-white"> <i class="fa fa-plus-circle"></i> Tambah Dokumen </a>
                </small>
                <?php
                } ?>
                </h5>
                <div class="table-responsive">
                  <table id="dokumenFileIdTable" class="border-double table mb-0 table-bordered mb-2" style="width: 100%">
                    <?= $this->libkontrak->getTableFileMulti($reqId," AND A.FILE_JENIS = 'Surat Pesanan' ") ?>
                  </table>
                </div>
              </div>
            </div>
          </div>

            <?php
            $no = 1;
            while($getpaket_pemenang->nextRow())
            {
              $contractingrekananSPPBJ = new Contractingrekanan();
              $contractingrekananSPPBJ->selectViewPKSSPK(array("A.PAKET_ID" => $reqPaketId, "A.REKANAN_ID" => $getpaket_pemenang->getField("REKANAN_ID")));
              $contractingrekananSPPBJ->firstRow();
              $nilaiKontrak += $contractingrekananSPPBJ->getField("CR_NILAI_KONTRAK") / $getpaket_pemenang->countRow();
              // echo "AND REKANAN_ID = ".$getpaket_pemenang->getField("REKANAN_ID")." AND CONTRACTINGREKANANID = ".$reqId; die;
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
                      <td>Terpakai:</td><td><?= currencyToPage($v['total']); ?></td>
                    </tr>
                    <tr>
                      <td>Sisa Kontrak:</td><td><?= currencyToPage($v['sisa']); ?></td>
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
                              <th width="12%" class="text-center">Aksi</th>
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
                                  <a href="kontrak/index/contracting_pelaksanaan_kontrak_surat_pesanan_multi_edit?reqId=<?= $contractingrekananSPPBJ->getField("CONTRACTINGREKANANPROSES1ID") ?>&reqConRekId=<?= $contractingrekananSPPBJ->getField("CONTRACTINGREKANANID") ?>&reqSuratPesananId=<?= $datasuratpesananmaterial->getField('SURATPESANANID') ?>" class="mr-1"> <i class="fa fa-pencil"></i></a>
                                  
                                  <a target="_blank" href="main/loadUrl/report/surat_pesanan_pdf?reqId=<?= $contractingrekananSPPBJ->getField("CONTRACTINGREKANANPROSES1ID") ?>&reqConRekId=<?= $contractingrekananSPPBJ->getField("CONTRACTINGREKANANID") ?>&reqSuratPesananId=<?= $datasuratpesananmaterial->getField('SURATPESANANID') ?>" class="mr-1"><span class="fa fa-print"></span></a>
                                  
                                  <a onClick="deleteData('contracting_json/delSuratPesanan/', '<?= $datasuratpesananmaterial->getField('SURATPESANANID') ?>')"> <i class="fa fa-trash"></i></a>


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
                          <?php
                            $sumTotal.$rekId = 0;
                            if ($reqContractingStatusKontrakId >= '2' && $this->LEGAL != '1' && $this->USER_TYPE_ID != '20')
                            { // Penyedia sudah approve & Peng. Kontrak bagian Legal tapi User Pengguna, pemeriksa 
                              if ($sumTotal > $v['nilai_kontrak']) {}
                              else {
                              ?>
                            <a href="kontrak/index/contracting_pelaksanaan_kontrak_surat_pesanan_multi_edit?reqId=<?= $contractingrekananSPPBJ->getField("CONTRACTINGREKANANPROSES1ID") ?>&reqConRekId=<?= $contractingrekananSPPBJ->getField("CONTRACTINGREKANANID") ?>" class="<?= CLASS_BTN_PRIMARY ?> mb-1 btn-sm"> <i class="fa fa-plus"></i> Surat Pesanan</a>
                            <?php
                              }
                            }?>
                      </td>
                    </tr> 
                  </tbody>
                </table>
            <?php
            $no++;
            } ?>
          </div>
 
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

          <div class="form-actions mt-3 table-responsive">
            <!-- <h4>Sanksi dan Denda Keterlambatan</h4>
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
            </table> -->
            <!-- <div class="card mb-1 border-blue border-darken-1 table-responsive">
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

            <!-- <hr>
            <h4>Sanksi dan Denda Kelalaian</h4>
            <div class="card mb-1 border-blue border-darken-1 table-responsive">
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

            <a href="kontrak/index/contracting_detail?reqId=<?= $reqId; ?>&reqProses=3" class="<?= CLASS_BTN_DANGER ?> mr-1"> <?= BTN_KEMBALI ?></a>


          </div>
        </div>
      </div>
    </div>
  </div>
</div>
