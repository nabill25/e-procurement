<?php
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 *
 */

/* INCLUDE FILE */
$this->load->library("kauth");  $userLogin = new kauth();
$this->load->model("Contractingfile");
include_once("functions/string.func.php");
include_once("functions/date.func.php");
include_once("functions/default.func.php");
$this->load->model(array("Contracting","Contractingsuratpesanan"));
$this->load->model("Paketpemenang");
$this->load->model("Rekanan");
$this->load->model("Contractingrekanan");

$contracting = new Contracting();
$getpaket_pemenang = new Paketpemenang();
$rekanan = new Rekanan();
$contractingrekanan = new Contractingrekanan();
$spkpks = new Contractingrekanan();
$sppbj = new Contractingrekanan();
$legal = new Contractingrekanan();
$suratpensanan = new Contractingsuratpesanan();

$reqId  = $this->input->get("reqId");

$sppbj->selectViewSPPBJ(array("A.CONTRACTINGREKANANPROSES1ID" => $reqId));
$sppbj->firstRow();
$reqNilaiSPPBJ = $sppbj->getField('CR_SPPBJ_NILAI') ?: '';
$reqDirutSPPBJ = $sppbj->getField('CR_SPPBJ_DIRUT') ?: '';
$reqDirutJabatanSPPBJ = $sppbj->getField('CR_SPPBJ_DIRUT_JABATAN') ?: '';
$reqPejabatBerwenangSPPBJ = $sppbj->getField('CR_SPPBJ_PEJABAT_BERWENANG') ?: '';
$reqJabatanSPPBJ = $sppbj->getField('CR_SPPBJ_JABATAN') ?: '';
$reqContractingRekananId = $sppbj->getField('CONTRACTINGREKANANID') ?: '';

$contracting->selectByParams(array("A.CONTRACTINGREKANANID" => $reqContractingRekananId));
$contracting->firstRow();
$reqNamaPaket = $contracting->getField("NAMA");
$reqPanitiaStr = $contracting->getField("PANITIA_STR");
$reqPenggunaStr = $contracting->getField("PENGGUNA_STR");
$reqPpkStr = $contracting->getField("PPK_STR");
$reqPemenangStr = $contracting->getField("PEMENANG_NAMA");

$spkpks->selectViewPKSSPK(array("A.CONTRACTINGREKANANPROSES1ID" => $reqId));
$spkpks->firstRow();
$reqContractingRekananProses1Id = $spkpks->getField('CONTRACTINGREKANANPROSES1ID') ?: '-';
$reqJenisKontrak = $contracting->getField('JENIS_KONTRAK') ?: '-';
$reqCode = $spkpks->getField('CR_CODE') ?: '';
$reqJnsKontrakStr = $spkpks->getField('JNS_KONTRAK_STR') ?: '';
$reqRekananProses1Id = $spkpks->getField('CONTRACTINGREKANANPROSES1ID') ?: '-';
$reqPaketId = $spkpks->getField('PAKET_ID') ?: '-';
$reqContractingRekananId = $spkpks->getField('CONTRACTINGREKANANID') ?: '-';
$reqJenisPengadaan = $spkpks->getField('CR_JENIS_PENGADAAN') ?: '-';
$reqJenisPengadaanStr = $spkpks->getField('CR_JENIS_PENGADAAN_STR') ?: '-';
$reqJenisPekerjaan = $spkpks->getField('CR_JENIS_PEKERJAAN') ?: '';
$reqJenisPekerjaanStr = $spkpks->getField('CR_JENIS_PEKERJAAN_STR') ?: '';
$reqContractingjeniskontrakid = $spkpks->getField('CONTRACTINGJENISKONTRAKID') ?: '-';
$reqJenisKontrakStr = $spkpks->getField('CR_JENIS_KONTRAK_STR') ?: '-';
$reqWaktuPelaksanaanDari = dateToPageCheck($spkpks->getField('CR_WAKTU_PELAKSANAAN_DARI')) ?: '';
$reqWaktuPelaksanaanSampai = dateToPageCheck($spkpks->getField('CR_WAKTU_PELAKSANAAN_SAMPAI')) ?: '';
$reqLingkupPekerjaan = $spkpks->getField('CR_LINGKUP_PEKERJAAN') ?: '-';
$reqNilaiKontrak = $spkpks->getField('CR_NILAI_KONTRAK') ?: $reqNilaiSPPBJ;
$reqMetodePembayaran = $spkpks->getField('CR_METODE_PEMBAYARAN') ?: '-';
$reqPihak1Nama = $spkpks->getField('CR_PIHAK1_NAMA') ?: $reqPejabatBerwenangSPPBJ;
$reqPihak1Jabatan = $spkpks->getField('CR_PIHAK1_JABATAN') ?: $reqJabatanSPPBJ;
$reqPihak2Nama = $spkpks->getField('CR_PIHAK2_NAMA') ?: $reqDirutSPPBJ;
$reqPihak2Jabatan = $spkpks->getField('CR_PIHAK2_JABATAN') ?: $reqDirutJabatanSPPBJ;
$reqPihak2 = $spkpks->getField('CR_PIHAK2_PERUSAHAAN') ?: '';
$reqCreatedBy = $spkpks->getField('CR_CREATED_BY') ?: '-';
$reqContractingStatusKontrakId = $spkpks->getField('CONTRACTINGSTATUSKONTRAKID') ?: '-';
$reqRakananId = $contracting->getField('REKANAN_ID') ?: 0;

$legal->selectViewLegal(array("A.CONTRACTINGREKANANPROSES1ID" => $reqId));
$legal->firstRow();
$reqLegalNomorPKS = $legal->getField('CR_LEGAL_NOMOR_PKS') ?: '';
$reqLegalTanggal = dateToPageCheck($legal->getField('CR_LEGAL_TANGGAL')) ?: '';
$reqLegalNomorRekanan = $legal->getField('CR_LEGAL_NOMOR_REKANAN') ?: '';
$reqLegalTanggalRekanan = dateToPageCheck($legal->getField('CR_LEGAL_TANGGAL_REKANAN')) ?: '';
$reqLegalCreatedBy = $legal->getField('CR_LEGAL_CREATED_BY') ?: '';
$reqLegalCreatedDate = $legal->getField('CR_LEGAL_CREATED_DATE') ?: '';
$reqLegalUpdatedBy = $legal->getField('CR_LEGAL_UPDATED_BY') ?: '';
$reqLegalUpdatedDate = $legal->getField('CR_LEGAL_UPDATED_DATE') ?: '';

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
    <!-- END VENDOR CSS-->
    <!-- BEGIN ROBUST CSS-->
    <link rel="stylesheet" type="text/css" href="<?=base_url() ?>assets/new/css/app.css">
    <!-- <link rel="stylesheet" type="text/css" href="<?=base_url() ?>css/core.css"> -->
    <!-- END ROBUST CSS-->
    <!-- BEGIN Page Level CSS-->
    <link rel="stylesheet" type="text/css" href="<?=base_url() ?>assets/new/css/core/menu/menu-types/horizontal-menu.css">
    <link rel="stylesheet" type="text/css" href="<?=base_url() ?>assets/new/css/core/colors/palette-gradient.css">
    <link rel="stylesheet" type="text/css" href="<?=base_url() ?>assets/new/css/core/colors/palette-callout.css">
    <!-- END Page Level CSS-->
    <!-- BEGIN Custom CSS-->
    <link rel="stylesheet" type="text/css" href="<?=base_url() ?>assets/new/css/style.css">

    <!-- FONT AWESOME -->
    <link rel="stylesheet" href="lib/font-awesome-4.7.0/css/font-awesome.css" type="text/css">

    <script type="text/javascript" src="js/jquery-1.6.1.min.js"></script>
    <!-- PAGINATION -->
    <link rel="stylesheet" type="text/css" href="lib/drupal/index.css" />
     <script src="lib/emodal/eModal.js"></script>
     <style type="text/css">
       #reqKodeSeachPenyediaautocomplete-list {
          position: relative;
          margin-top: 10px;
          background: #fff;
          width: 100%;
        }
        #reqKodeSeachPenyediaautocomplete-list div {
          margin: 5px;
        }
     </style>
    <script>
    function openAdd(pageUrl) {
        eModal.iframe(pageUrl, 'Eprocurement | <?= SYSTEM_NAME_PT ?>')
    }

    function closePopup() {
      eModal.close();
    }

    function closePopupReload() {
      eModal.close();
      location.reload();
    }
    </script>
    <script type="text/javascript">
    $(function(){
      $('#ffEditSuratPesananMaterial').form({
        url:'contracting_json/updateSuratPesananMaterial',
        onSubmit:function(){
          var v=$(this).form('validate');
          if(v) // showLoad();  // show the message box
          return v;
        },
        success:function(data){
          $.messager.alert('Info', data, 'info');
          setTimeout(function () {
            window.top.location.reload();
          }, 1000);
        }
      });

    });

  </script>
  </head>

<body class="body-popup">

     <div class="card mb-1 border-darken-1">
      <div class="card-content">
        <div class="p-1">
          <div class="form-actions">
                  <table class="table table-bordered table-hover">
                    <tbody>
                      <tr>
                        <td width="25%" colspan="2">
                          <small>Nomor <?= $reqJnsKontrakStr ?> <?= SYSTEM_NAME_PT ?> </small> <br> <?= $reqLegalNomorPKS ?>
                        </td>
                        <td width="25%" colspan="2">
                          <small>Tanggal  <?= $reqJnsKontrakStr ?></small> <br> <?= $reqLegalTanggal ?>
                        </td>
                      </tr>
                      <tr>
                        <!-- <td width="50%" colspan="4">
                          <small>Nomor PKS Penyedia</small> <br> <?= $reqLegalNomorRekanan ?>
                        </td> -->
                        <!-- <td width="25%" colspan="2">
                          <small>Tanggal </small> <br> <?= $reqLegalTanggalRekanan ?>
                        </td>  -->
                      </tr>
                      <tr>
                        <td width="13%">
                          <small>Nilai Pekerjaan Maksimal </small> <br>  <?= currencyToPage($reqNilaiKontrak) ?>
                        </td>
                        <!-- <td width="25%" colspan="2">
                          <small>Nomor Kontrak</small> <br> <?= $reqCode ?>
                        </td> -->
                        <td width="12%">
                          <small>Metode Pembayaran </small> <br>
                          <?php
                          if ($reqMetodePembayaran == '1') {
                             echo "Sekaligus";
                          } else { echo "Termin"; } ?>
                        </td>
                        <td width="12%">
                          <small>Jenis Pengadaan</small> <br> <?= $reqJenisPengadaanStr ?>
                        </td>
                        <td width="13%">
                          <small>Jenis Kontrak</small> <br> <?= $reqJenisKontrakStr ?>
                        </td>
                      </tr>
                      <tr>
                        <!-- <td width="25%" colspan="2">
                          <small>Jenis Pekerjaan</small> <br> <?= $reqJenisPekerjaanStr ?>
                        </td> -->
                      </tr>
                      <tr>
                        <td width="25%" colspan="4">
                          <small>Jangka Waktu Pelaksanaan </small> <br> <?= getFormattedDateShort2(dateTimeToPageCheck($reqWaktuPelaksanaanDari)).' s/d '.getFormattedDateShort2(dateTimeToPageCheck($reqWaktuPelaksanaanSampai)) ?>
                        </td>

                      </tr>
                      <tr>
                        <td colspan="4">
                          <small>Lingkup Pekerjaan</small> <br> <?= $reqLingkupPekerjaan ?>
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
        </div>
      </div>
    </div>

    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="lib/bootstrap/libs/jquery/1.12.4/jquery.min.js"></script>
    <script>window.jQuery || document.write('<script src="lib/bootstrap/assets/js/vendor/jquery.min.js"><\/script>')</script>
    <script src="lib/bootstrap/dist/js/bootstrap.min.js"></script>

    <!-- EASYUI -->
    <link rel="stylesheet" type="text/css" href="<?=base_url()?>lib/eproc/themes/default/easyui.css">
    <script type="text/javascript" src="<?=base_url()?>lib/eproc/jquery.easyui.min.js"></script>
    <script type="text/javascript" src="<?=base_url()?>lib/eproc/kalender-easyui.js"></script>
    <script type="text/javascript" src="<?=base_url()?>lib/eproc/allfunc.js"></script>

  </body>
</html>
