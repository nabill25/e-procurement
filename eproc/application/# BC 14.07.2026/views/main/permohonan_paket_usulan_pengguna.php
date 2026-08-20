<?php
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 *
 */
$this->libsession->cekSession();

$this->load->library("kauth");  $userLogin = new kauth();
$this->load->library("libapiui");
$this->load->library("paketinfo"); $paketInfo = new paketinfo();
$this->load->library("FileHandler");
$this->load->model(array("Importsirup","PermohonanPaket","PermohonanPaketAnalisaFile"));
include_once("functions/string.func.php");
include_once("functions/date.func.php");
include_once("functions/default.func.php");

$libapiui = new libapiui();

$sirupId= $this->input->get("sirupId") ?: '0';
$reqId = $this->input->get("reqId") ?: '0';
$permohonan_paket_analisa = new PermohonanPaket();
$permohonan_paket_anggaran = new PermohonanPaket();
$sirup = new Importsirup();
$file = new FileHandler();

if($sirupId) // Add
{
  $reqMode = 'insert';
  $sirup->selectByParams(array("ID" => $sirupId));
  $sirup->firstRow();

  $reqTahun = $sirup->getField("TAHUN");
  $reqKodeRUP = $sirup->getField("KODE_RUP");
  $reqKodePR = $sirup->getField("KODE_PR");
  $reqKodeSA = $sirup->getField("KODE_SA");
  $reqDPSJ = $sirup->getField("KODE_DPSJ");
  $reqNoUrut = $sirup->getField("NO_URUT");
  $reqKategoriPaketID = $sirup->getField("KATEGORI_PAKET_ID");
  $reqNamaPaket = $sirup->getField("NAMA_PAKET");
  $reqNilaiPagu = $sirup->getField("NILAI_PAGU");
  $reqNilaiPaguPR = $sirup->getField("NILAI_PAGU_PR");
  $reqListKegiatan = $sirup->getField("LIST_KEGIATAN");
  $reqWaktuAwal = $sirup->getField("WAKTU_AWAL");
  $reqWaktuAkhir = $sirup->getField("WAKTU_AKHIR");
  $reqStatusProses = $sirup->getField("STATUS_PROSES");
  $reqName = $sirup->getField("NAME");
  $reqKategoriPaket = $sirup->getField("KATEGORI_PAKET");
  $reqNamaSA = $sirup->getField("NAMA_SA");
  $reqNamaDPSJ = $sirup->getField("NAMA_DPSJ");
  $reqMetodePemilihan = $sirup->getField("METODE_PEMILIHAN");
  $reqNamaJenisPekerjaan = $sirup->getField("NAMA_JENIS_PEKERJAAN");
  $reqHasilVerifikasi = $sirup->getField("HASIL_VERIFIKASI");
  $reqCreatedBy = $sirup->getField("CREATED_BY");
  $reqCreatedAt = $sirup->getField("CREATED_AT");
  $reqUpdatedBy = $sirup->getField("UPDATED_BY");
  $reqUpdatedAt = $sirup->getField("UPDATED_AT");
  $reqImportDate = $sirup->getField("IMPORT_DATE");
}
else // Edit
{
  $reqMode = 'update';
}

// Update Kode PR Berdasarkan RUP

?>

<script type="text/javascript">

$(document).ready(function() { 

  $(function(){
    $('#ff').form({
      url:'permohonan_paket_usulan_json/permohonan_usulan_add',
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
        $("#ff").trigger("reset");
        if (data == 'Data berhasil disimpan.') {
          alertSuccess2(data);
          setTimeout(function () {
            <?php
            if ($reqMode =='insert') {?>
            document.location.href = 'main/index/rencana_umum_pengadaan_persiapan';
            <?php
            } else { ?>
            // document.location.href = 'main/index/permohonan_paket_usulan_pengguna/?reqId=<?= $reqId ?>';
            <?php
            } ?>
          }, 2000);
        } else {
          alertError2('Data gagal disimpan, silahkan dicoba kembali.');
          setTimeout(function () {
            document.location.href = 'main/index/rencana_umum_pengadaan';
          }, 2000);
        }
      }
    });
  });
});
 

function FormatNumberya(id)
{
   var a = parseFloat(id);
   var nilai = FormatCurrency(a);
   return nilai;
}

// ------------
// Jquery Dependency
$(document).ready(function() {
  $(function(){
    $("input[data-type='currency']").on({
        keyup: function() {
          formatCurrencyDecimal($(this));
        },
        blur: function() {
          formatCurrencyDecimal($(this), "blur");
        }
    });
  });
});

// -----------

</script>

<div class="row">
  <div class="col-md-12 col-sm-12">
    <div class="card">
      <div class="card-header card-head-inverse bg-primary">
        <h4 class="card-title text-white">Usulan Paket</h4>
        <a class="heading-elements-toggle"><i class="fa fa-ellipsis-v font-medium-3"></i></a>
        <div class="heading-elements">
            <ul class="list-inline mb-0">
              <li><a data-action="expand"><i class="ft-maximize"></i></a></li>
              <!-- <li><a data-action="close"><i class="ft-x"></i></a></li> -->
            </ul>
        </div>
      </div>


      <form id="ff" class="easyui-form form-horizontal" method="post" novalidate enctype="multipart/form-data">
      <div class="card-content collapse show border-info border-darken-2">
          <div class="card-body">
          <?php
          if($reqAlasanTolak == '')
          {}
          else
          {
          ?>
            <div class="card mb-1 border-blue border-darken-1">
              <div class="card-content">
                <div class="p-1">
                 <div class="row">
                  <div class="form-group col-md-12 mb-2">
                    <h4 style="color: red">Alasan Dikembalikan</h4>
                    <span style="font-weight: normal"><?=$reqAlasanTolak?></span>
                  </div>
                </div>
                </div>
              </div>
            </div>
          <?php
          }
          ?>

          <?php 
          if ($reqKodePR == '') { ?>
            <div class="alert alert-danger" id="setNotifPR">Kode PR belum tersedia, RUP ini belum bisa diteruskan.</div>
          <?php 
           } 

          if ($reqNilaiPaguPR > $reqNilaiPagu) {  ?>
            <div class="alert alert-danger">Nilai RAB Lebih Besar Dari Nilai Pagu RUP , Silahkan Lakukan Revisi !</div>
          <?php
          }
          ?>

          <table class="table table-responsive table-bordered table-hover">
            <tbody>
              <tr>
                <td width="10%" colspan="2" style="background: #f6db00; color:#000"><b>Tahun</b></td>
                <td width="90%" colspan="6"><?= $reqTahun ?></td> 
              </tr>
              <tr>
                <td width="10%" colspan="2"style="background: #f6db00; color:#000"><b>Kode RUP</b></td>
                <td width="90%" colspan="6"><?= $reqKodeRUP ?></td>
              </tr>
              <tr>
                <td width="10%" colspan="2"style="background: #f6db00; color:#000"><b>Kode PR</b></td>
                <td width="90%" colspan="6"><?= $reqKodePR ?></td>
              </tr>
              <tr>
                <td width="11%" colspan="2" style="background: #f6db00; color:#000"><b>SA</b></td>
                <td width="25%" colspan="2"><?= $reqKodeSA.' - '.$reqNamaSA ?></td>
                <td width="11%" colspan="2" style="background: #f6db00; color:#000"><b>DPSJ</b></td>
                <td width="25%" colspan="2">
                  <?= $reqDPSJ ?>
                  <br>
                  <?php
                  $namaDPSJ = parsePostgresArray2($reqNamaDPSJ);
                  foreach ($namaDPSJ as $key => $value) {
                    echo $value.'<br>';
                  }
                  ?>
                  </td>
              </tr> 
              <tr>
                <td colspan="2" style="background: #f6db00; color:#000"><b>Nama Paket</b></td>
                <td colspan="6"><?= $reqNamaPaket ?></td>
              </tr>
              <tr>
                <td colspan="2" style="background: #f6db00; color:#000"><b>Metode Pemilihan</b></td>
                <td colspan="2"><?= $reqMetodePemilihan ?></td>
                <td colspan="2" style="background: #f6db00; color:#000"><b>Nama Jenis Pekerjaan</b></td>
                <td colspan="2"><?= $reqNamaJenisPekerjaan ?></td>
              </tr>
              <tr>
                <td colspan="2" style="background: #f6db00; color:#000"><b>Nilai Pagu RUP</b></td>
                <td colspan="2"><?= currencyToPage($reqNilaiPagu) ?></td>
                <td colspan="2" style="background: #f6db00; color:#000"><b>Nilai RAB</b></td>
                <td colspan="2"><?= currencyToPage($reqNilaiPaguPR) ?></td>
              </tr>
              <tr>
                <td colspan="2" style="background: #f6db00; color:#000"><b>Waktu Awal</b></td>
                <td colspan="2"><?= str_replace('<br>',' ',getFormattedDateYMJson($reqWaktuAwal)) ?></td>
                <td colspan="2" style="background: #f6db00; color:#000"><b>Waktu Akhir</b></td>
                <td colspan="2"><?= str_replace('<br>',' ',getFormattedDateYMJson($reqWaktuAkhir)) ?></td>
              </tr>
              <tr>
                <td colspan="2" style="background: #f6db00; color:#000"><b>Status Proses</b></td>
                <td colspan="2"><?= $reqStatusProses ?></td>
                <td colspan="2" style="background: #f6db00; color:#000"><b>Name</b></td>
                <td colspan="2"><?= $reqName ?></td>
              </tr>
              <tr>
                <td colspan="2" style="background: #f6db00; color:#000"><b>Kategori Paket</b></td>
                <td colspan="2"><?= $reqKategoriPaket ?></td> 
              </tr>
              <!-- <tr>
                <td width="25%" colspan="2"><b>HasilVerifikasi</b> </td>
                <td width="25%" colspan="2"><?php // echo $reqHasilVerifikasi ?></td>
                <td width="25%" colspan="2"><b>Dibuat oleh</b></td>
                <td width="25%" colspan="2"><?php // echo $reqCreatedBy ?></td>
              </tr>
              <tr>
                <td width="25%" colspan="2"><b>Tanggal buat</b></td>
                <td width="25%" colspan="2"><?php // echo $reqCreatedAt ?></td>
                <td width="25%" colspan="2"><b>Diubah oleh</b></td>
                <td width="25%" colspan="2"><?php // echo $reqUpdatedByRUP ?></td>
              </tr>
              <tr>
                <td width="25%" colspan="2"><b>Tanggal ubah</b></td>
                <td width="25%" colspan="2"><?php // echo $reqUpdatedAt ?></td>
                <td width="25%" colspan="2"><b>Tanggal import</b></td>
                <td width="25%" colspan="2"><?php // echo $reqImportDate ?></td>
              </tr> -->
            </tbody>
          </table>
          <?php 
          if ($reqNilaiPaguPR > $reqNilaiPagu) { ?>
              <a href="main/index/rencana_umum_pengadaan" class="<?= CLASS_BTN_DANGER ?> mr-1 text-white"> <i class="fa fa-arrow-left"></i> Kembali </a>
          <?php   
          } else {
            if ($reqKodePR != '') {
          ?>
            <div class="form-actions">
              <input type="hidden" name="sirupId" value="<?=$sirupId?>" />
              <input type="hidden" name="reqId" value="<?=$reqId?>" />
              <input type="hidden" name="reqMode" value="<?=$reqMode?>" />
              <a href="main/index/rencana_umum_pengadaan" class="<?= CLASS_BTN_DANGER ?> mr-1 text-white"> <i class="fa fa-arrow-left"></i> Kembali </a>
              <button type="submit" class="<?= CLASS_BTN_PRIMARY ?>"> <?= BTN_KIRIM ?></button>
            </div>
          <?php 
            } else { ?>
              <a href="main/index/rencana_umum_pengadaan" class="<?= CLASS_BTN_DANGER ?> mr-1 text-white"> <i class="fa fa-arrow-left"></i> Kembali </a>
          <?php 
            }
          } ?>

          </div>
        </div>
      </form>

    </div>
  </div>
</div>

<?php
function parsePostgresArray($string) {
    $clean = trim($string, '{}');
    $items = str_getcsv($clean);

    $result = [];
    foreach ($items as $item) {
        $item = trim($item, '"');
        $parts = explode("|", $item);
        $result[] = [
            'kode'  => $parts[0] ?? null,
            'nama'  => $parts[1] ?? null,
            'nilai' => $parts[2] ?? null,
        ];
    }
    return $result;
}

function parsePostgresArray2($string) {
    $string = trim($string, '{}');
    preg_match_all('/"([^"]*)"/', $string, $matches);
    return $matches[1]; // array hasil
}
 ?>
