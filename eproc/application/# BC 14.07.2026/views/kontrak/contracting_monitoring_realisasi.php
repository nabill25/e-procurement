<?php
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 * 
 */

$reqId = httpFilterRequest("reqId");
$reqProses = httpFilterRequest("reqProses");
$getTahun = $this->session->userdata('setTahunKontrak');
$this->session->set_userdata('setProsesKontrak',$reqProses);

$this->libsession->cekSessionKontrakPPK($reqId);

$this->load->library("kauth");  $userLogin = new kauth();

$this->LEGAL =  $this->kauth->getInstance()->getIdentity()->LEGAL;

include_once("functions/string.func.php");
include_once("functions/date.func.php");
include_once("functions/default.func.php");

$this->load->model("Contracting");
$this->load->model("Contractingrekanan");

$contracting = new Contracting();
$spkpks = new Contractingrekanan();

$contracting->selectByParams(array("A.CONTRACTINGREKANANID" => $reqId));
$contracting->firstRow();

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

<script type="text/javascript">
function prosesKontrak(flow,aidi,m) {
  $.messager.confirm('Konfirmasi',m,function(r){
    if (r){
      $.getJSON("contracting_json/proseskontrak/?reqAidi="+aidi+"&flow="+flow,
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
      <?= $this->libkontrak->getMenu($reqId,$reqProses); ?>
    </div>
  </div>
  <div class="col-md-9 col-sm-9">
    <div class="card">
      <div class="card-content collapse show border-info border-darken-2">
        <div class="card-body">
          <h4 class="mb-2">Realisasi Pekerjaan</h4>

          <?= $this->libkontrak->getInfoKontrak($reqId); ?> 
          
          <div class="form-actions table-responsive">
            <table id="deliveriable" class="border-double table mb-0 table-bordered mb-2" style="width: 100%">
              <tr class="backcolornew">
                <th>Realisasi</th>
                <th>Keterangan</th>
                <th width="20%">Tanggal Realisasi</th>
                <th width="20%">Tanggal</th>
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
                  <td><?= $datadelivery->getField('DELIVERY_NAMA') ?></td>
                  <td><?= $datadelivery->getField('LINGKUP') ?></td>
                  <td class="text-center">
                    <?= getFormattedDateShort($datadelivery->getField('TANGGAL_DELIVERY_DARI')); ?> <br> s/d <br>
                    <?= getFormattedDateShort($datadelivery->getField('TANGGAL_DELIVERY_SAMPAI')); ?></td>
                  <td>
                    <small>
                    Aktual Pekerjaan: <?= $datadelivery->getField('TANGGAL') ? getFormattedDateShort($datadelivery->getField('TANGGAL')) : '-'; ?> <br>
                    Laporan Selesai: <?= $datadelivery->getField('TANGGAL_TERIMA') ? getFormattedDateShort($datadelivery->getField('TANGGAL_TERIMA')) : '-'; ?>
                    </small>
                  </td>
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

            <!-- <a href="kontrak/index/contracting_detail?reqId=<?= $reqId; ?>" class="<?= CLASS_BTN_DANGER ?> mr-1 text-white"> <i class="fa fa-arrow-left"></i> Kembali </a> -->

          </div>
        </div>
      </div>
    </div>
  </div>
</div>
