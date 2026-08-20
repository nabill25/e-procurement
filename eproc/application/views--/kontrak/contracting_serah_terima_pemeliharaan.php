<?php
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 * 
 */

$this->libsession->cekSession();

$this->load->library("kauth");  $userLogin = new kauth();

$this->LEGAL =  $this->kauth->getInstance()->getIdentity()->LEGAL;

include_once("functions/string.func.php");
include_once("functions/date.func.php");
include_once("functions/default.func.php");

$reqId = httpFilterRequest("reqId");
$getTahun = $this->session->userdata('setTahunKontrak');


$this->load->model("Contracting");
$this->load->model("Contractingrekanan");
$this->load->model("PaketPenilaian");

$contracting = new Contracting();
$spkpks = new Contractingrekanan();
$proses5 = new Contractingrekanan();

$contracting->selectByParams(array("A.CONTRACTINGREKANANID" => $reqId));
$contracting->firstRow();
$reqRekananId = str_replace(array("{","}"),"",$contracting->getField('PEMENANG')) ?: '-';


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

$proses5->selectProses5(array("A.CONTRACTINGREKANANID" => $reqId));
$proses5->firstRow();

if ($proses5->countRow() > 0) {
  $reqSubmit = 'update';
} else {
  $reqSubmit = 'simpan';
}

$reqContractingRekananProses5Id = $proses5->getField('CONTRACTINGREKANANPROSES5ID') ?: '-';
$reqBastMasaNomor = $proses5->getField('CR_BAST_MASA_NOMOR') ?: '-';
$reqBastMasaTanggal = $proses5->getField('CR_BAST_MASA_TANGGAL') ?: '-';
$reqBastMasaNamaPenyedia = $proses5->getField('CR_BAST_MASA_NAMA_PENYEDIA') ?: '-';
$reqBastMasaJabatanPenyedia = $proses5->getField('CR_BAST_MASA_JABATAN_PENYEDIA') ?: '-';
$reqBastMasaNamaPenerima = $proses5->getField('CR_BAST_MASA_NAMA_PENERIMA') ?: '-';
$reqBastMasaJabatanPenerima = $proses5->getField('CR_BAST_MASA_JABATAN_PENERIMA') ?: '-';
$reqBastMasaStatus = $proses5->getField('CR_BAST_MASA_STATUS') ?: '-';

$cekPenilaianTotal = new PaketPenilaian();
$cekPenilaianTotal->selectPenilaian(array("PAKET_ID" => $reqPaketId,"REKANAN_ID" => $reqRekananId, "CONTRACTINGREKANANID" => $reqId));
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
          } else if (data.FLOW == '100') {
            location.href = "kontrak/index/contracting_serah_terima?tahun=<?= $getTahun ?>";
          } else if (data.FLOW == '200') {
            location.href = "kontrak/index/contracting_serah_terima?tahun=<?= $getTahun ?>";
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

  $(function(){
    $('#ffApproveKontrak').form({
      url:'contracting_json/approveKontrak',
      onSubmit:function(){
        var v=$(this).form('validate');
        if(v) // showLoad();  // show the message box
        return v;
      },
      success:function(data){
        window.top.location.reload();
      }
    });

  });

  // function approvalKontrak(aidi) {
  //   openAdd('main/loadUrlKontrak/kontrak/contracting_approval_pemeriksa?reqAidi='+aidi);
  // }
</script>

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
          <h4 class="mb-2">Berita Acara Serah Terima (BAST) Masa Pemeliharaan</h4>

          <div class="row mb-1">
            <div class="col-md-12">
              <?php
              if ($this->LEGAL != '1' && $cekPenilaianTotal->countRow() > 0) { // Pengelola Kontrak bukan legal dan sudah isi penilaian
                echo $this->libkontrak->getStatusKontrakTeruskan($reqContractingStatusKontrakId,$reqId,$this->USER_TYPE_ID,$this->LEGAL,'PENUTUPAN'); 
              } else {
                echo '<div class="alert alert-danger text-center" style="font-weight:bold; margin-top:1%">. : : Kontrak tidak bisa di selesaikan karena penilaian belum di isi : : .</div>';
              }
              ?>
            </div>
          </div> 

          <div class="row mb-1">
            <div class="col-md-12">
              <?php
              // if ($this->LEGAL != '1' && $cekPenilaianTotal->countRow() > 0) { // Pengelola Kontrak bukan legal dan sudah isi penilaian
              //   echo $this->libkontrak->getStatusKontrakTeruskan($reqContractingStatusKontrakId,$reqId,$this->USER_TYPE_ID,$this->LEGAL,'PENUTUPAN');
              // } else {
              //   echo '<div class="alert alert-danger text-center" style="font-weight:bold; margin-top:1%">. : : Kontrak tidak bisa di selesaikan karena penilaian belum di isi : : .</div>';
              // }
              ?>
            </div>
          </div>

          <div class="card mb-1 border-blue border-darken-1">
            <div class="card-content">
              <div class="p-1">
                <h5>Dokumen BAST
                <?php
                if ($reqContractingStatusKontrakId >= '99') { // Status Penutupan?>
                <small style="font-size:0.7em">
                  <a onclick="openAdd('main/loadUrlKontrak/kontrak/contracting_file_add?reqAidi=<?= $reqId ?>&reqProses=5&reqJenis=BAST Masa Pemeliharaan')" class="badge badge-primary pull-right mr-1 text-white"> <i class="fa fa-plus-circle"></i> Tambah Dokumen BAST </a>
                </small>
                <?php
                } ?>
                </h5>
                <div class="table-responsive">
                  <table id="dokumenFileIdTable" class="border-double table mb-0 table-bordered mb-2" style="width: 100%">
                    <?= $this->libkontrak->getTableFile($reqId," AND A.FILE_JENIS = 'BAST Masa Pemeliharaan' ") ?>
                  </table>
                </div>
              </div>
            </div>
          </div>

          <div class="form-actions">

            <?php
            if ($this->LEGAL != '1' && $this->USER_TYPE_ID != '20') { // Peng. Kontrak bagian Legal, pemeriksa ?>
            <a onclick="openAdd('main/loadUrlKontrak/kontrak/contracting_serah_terima_pemeliharaan_edit?reqId=<?= $reqId ?>')" class="<?= CLASS_BTN_PRIMARY ?> mb-1"> <?= BTN_UBAH ?> Data BAST Masa Pemeliharaan </a>
            <?php
            }?>

            <table class="table table-bordered table-hover">
              <tbody>
                <tr>
                  <td width="25%" colspan="2">
                    <small>Nomor BAST</small> <br> <?= $reqBastMasaNomor ?>
                  </td>
                  <td width="25%" colspan="2">
                    <small>Tanggal BAST</small> <br> <?= getFormattedDate($reqBastMasaTanggal) ?>
                  </td>
                </tr>
                <tr>
                  <td width="25%" colspan="2">
                    <small>Nama Penyedia</small> <br> <?= $reqBastMasaNamaPenyedia ?>
                  </td>
                  <td width="25%" colspan="2">
                    <small>Jabatan Penyedia</small> <br> <?= $reqBastMasaJabatanPenyedia ?>
                  </td>
                </tr>
                <tr>
                  <td width="25%" colspan="2">
                    <small>Nama Penerima</small> <br> <?= $reqBastMasaNamaPenerima ?>
                  </td>
                  <td width="25%" colspan="2">
                    <small>Jabatan Penerima</small> <br> <?= $reqBastMasaJabatanPenerima ?>
                  </td>
                </tr>
                <tr>
                  <td width="20%" colspan="4">
                    <small>Status</small> <br>
                    <?php
                    if(str_replace(' ','',$reqBastMasaStatus) == '1') {
                      echo '<span class="badge badge-primary">Selesai</span>';
                    } else {
                      echo '<span class="badge badge-danger">Proses</span>';
                    }
                    ?>
                  </td>
                </tr>

              </tbody>
            </table>
            <hr>
            <h4>Deliverable Pekerjaan</h4>
            <table id="deliveriable" class="border-double table mb-0 table-bordered mb-2" style="width: 100%">
              <tr class="backcolornew">
                <th>Lingkup</th>
                <th>Hasil Pekerjaan</th>
                <th width="100px">Status</th>
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

            <a href="kontrak/index/contracting_detail?reqId=<?= $reqId; ?>" class="<?= CLASS_BTN_DANGER ?>"> <?= BTN_KEMBALI ?> </a>

          </div>
        </div>
      </div>
    </div>
  </div>
</div>
