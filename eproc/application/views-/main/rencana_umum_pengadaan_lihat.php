<?php
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 *
 */
/* INCLUDE FILE */
$this->load->model("Importsirup");
include_once("functions/string.func.php");
include_once("functions/date.func.php");
include_once("functions/default.func.php");
include_once("functions/blob.func.php");

/* VARIABLE */
$reqId  = $this->input->get("reqId");
$sirup = new Importsirup();

$sirup->selectByParams(array("ID" => $reqId));
$sirup->firstRow();

$reqTahun = $sirup->getField("TAHUN");
$reqKodeRUP = $sirup->getField("KODE_RUP");
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
$reqKodePR = $sirup->getField("KODE_PR");


?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <base href="<?=base_url();?>" />

    <link rel="icon" href="../../favicon.ico">

    <title><?= SYSTEM_NAME.' | '.SYSTEM_NAME_PT ?></title>

    <link rel="stylesheet" type="text/css" href="<?=base_url() ?>assets/new/css/vendors.css">
    <link rel="stylesheet" type="text/css" href="<?=base_url() ?>assets/new/vendors/css/ui/prism.min.css">
    <link rel="stylesheet" type="text/css" href="<?=base_url() ?>assets/new/css/app.css">
    <link rel="stylesheet" type="text/css" href="<?=base_url() ?>assets/new/css/core/menu/menu-types/horizontal-menu.css">
    <link rel="stylesheet" type="text/css" href="<?=base_url() ?>assets/new/css/core/colors/palette-gradient.css">
    <link rel="stylesheet" type="text/css" href="<?=base_url() ?>assets/new/css/core/colors/palette-callout.css">
    <link rel="stylesheet" type="text/css" href="<?=base_url() ?>assets/new/css/style.css">
    <script type="text/javascript" src="js/jquery-1.6.1.min.js"></script>
    <link rel="stylesheet" type="text/css" href="lib/drupal/index.css" />
    <script src="lib/emodal/eModal.js"></script>
    <script>
    function openAdd(pageUrl) {
        eModal.iframe(pageUrl, 'Eprocurement')
    }
    function closePopup() {
        eModal.close();
    }
    </script>
  </head>

<body class="body-popup">

<div class="card mb-1 border-darken-1">
  <div class="card-content">
    <div class="p-1">
      <div class="alert bg-primary alert-icon-left alert-arrow-left alert-dismissible mb-2" role="alert">
        <span class="alert-icon"><i class="fa fa-th"></i></span>  <strong>Rencana Umum Pengadaan</strong>
      </div>
      <div class="p-1">
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
                <td colspan="2" style="background: #f6db00; color:#000"><b>List Kegiatan</b></td>
                <td colspan="6">
                  <table class="table table-bordered table-hover">
                    <thead>
                      <tr style="background-color: #000; color:#fff">
                        <th>Kode</th>
                        <th>Nama</th>
                        <th>Nilai</th>
                      </tr>
                    </thead>
                    <tbody>
                  <?php
                  $listKegiatan = parsePostgresArray($reqListKegiatan);
                  foreach ($listKegiatan as $key => $value) {
                    echo '
                    <tr>
                      <td>'.$value['kode'].'</td>
                      <td>'.$value['nama'].'</td>
                      <td>'.$value['nilai'].'</td>
                    </tr>
                    ';
                  }
                  ?>
                    </tbody>
                </table>
                </td>
              </tr>
              <tr>
                <td colspan="2" style="background: #f6db00; color:#000"><b>Waktu Awal</b></td>
                <td colspan="2"><?= str_replace('<br>',' ',getFormattedDateYMJson($reqWaktuAwal)) ?></td>
                <td colspan="2" style="background: #f6db00; color:#000"><b>Waktu Akhir</b></td>
                <td colspan="2"><?= str_replace('<br>',' ',getFormattedDateYMJson($reqWaktuAkhir)) ?></td>
              </tr>
              <tr>
                <td colspan="2" style="background: #f6db00; color:#000"><b>Kategori Paket</b></td>
                <td colspan="2"><?= $reqKategoriPaket ?></td>
                <td colspan="2" style="background: #f6db00; color:#000"><b>Name</b></td>
                <td colspan="2"><?= $reqName ?></td>
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

      </div>
    </div>
  </div>
</div>

  <script src="lib/bootstrap/libs/jquery/1.12.4/jquery.min.js"></script>
  <script>window.jQuery || document.write('<script src="lib/bootstrap/assets/js/vendor/jquery.min.js"><\/script>')</script>
  <script src="lib/bootstrap/dist/js/bootstrap.min.js"></script>
  <link rel="stylesheet" type="text/css" href="<?=base_url()?>lib/eproc/themes/default/easyui.css">
  <script type="text/javascript" src="<?=base_url()?>lib/eproc/jquery.easyui.min.js"></script>
  <script type="text/javascript" src="<?=base_url()?>lib/eproc/kalender-easyui.js"></script>
  <script type="text/javascript" src="<?=base_url()?>lib/eproc/allfunc.js"></script>
  </body>
</html>

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
