<?php
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

$reqSuratPesananId  = $this->input->get("reqSuratPesananId"); // suratpesananid
$reqview  = $this->input->get("reqview"); // View

$suratpensanan->selectByParamsSuratPesanan(array("A.SURATPESANANID" => $reqSuratPesananId));
$suratpensanan->firstRow();
$reqNoSuratPesanan = $suratpensanan->getField('NOMOR_SURAT') ?: '';
$reqTglSuratPesanan = $suratpensanan->getField('TANGGAL') ?: '';
$reqId = $suratpensanan->getField('CONTRACTINGREKANANPROSES1ID') ?: '';


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
$reqRakananId = $spkpks->getField('REKANAN_ID') ?: '-';

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
          if(v) {
            return v;
          } else {
            return false;
           // showLoad();  // show the message box
          }
        },
        success:function(data){
          $.messager.alert('Info', data, 'info');
          setTimeout(function () {
            window.top.location.reload();
          }, 1000);
        }
      });

    });

    // $(document).ready(function() {
    //   $('#reqTanggalTerima').datebox({
    //     editable: false
    //   });
    // });

  </script>
  </head>

<body class="body-popup">

     <div class="card mb-1 border-darken-1">
      <div class="card-content">
        <div class="p-1">
          <div class="alert bg-primary alert-icon-left alert-arrow-left alert-dismissible mb-2" role="alert">
            <span class="alert-icon"><i class="fa fa-th"></i></span>  <strong>Realisasi Pekerjaan</strong>
          </div>

            <form id="ffEditSuratPesananMaterial" class="easyui-form form-horizontal" method="post" novalidate enctype="multipart/form-data" style="padding:0 50px">

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

              <table class="table table-bordered">
                <tr> <td width="20%">Nomor Surat Pesanan</td> <td>: <?=$reqNoSuratPesanan?></td></tr>
                <tr> <td width="20%">Tanggal</td> <td>: <?= getFormattedDate($reqTglSuratPesanan)?></td></tr>
                <tr> <td width="20%">Nilai Pekerjaan</td> <td>: <?= numberToIna($reqNilaiKontrak) ?></td></tr>
              </table>
              <div class="row">
                <div class="form-group col-md-12 mb-2" style="margin: 10px 0 solid #b7b7b7">
                  <h4>Daftar Barang Jasa</h4>
                  <table class="table table-bordered">
                    <thead>
                      <tr>
                        <th width="35%">Deskripsi</th>
                        <th width="8%">Harga Satuan</th>
                        <th width="5%" class="text-center">Vol/Qty</th>
                        <th width="14%">Total</th>
                        <th width="13%" class="text-center">Status Terima</th>
                        <th width="13%" class="text-center">Tanggal</th>
                        <th width="13%" class="text-center">Presentase</th>
                        <th width="25%">Keterangan</th>
                      </tr>
                    </thead>
                    <tbody id="tbodyMaterial">
                      <?php
                        $suratpensananmaterial = new Contractingsuratpesanan();
                        $suratpensananmaterial->selectByParamsSuratPesananMaterial(array("A.SURATPESANANID" => $reqSuratPesananId));
                        if ($suratpensananmaterial->countRow() > 0) {
                          while($suratpensananmaterial->nextRow())
                          {
                            $no = $suratpensananmaterial->getField('SURATPESANANMATERIALID');
                            $sumTotal += $suratpensananmaterial->getField('TOTAL');
                            if ($suratpensananmaterial->getField('STATUS_TERIMA') != '' && $suratpensananmaterial->getField('STATUS_TERIMA') == '1') {
                              $status = 1;
                            } else {
                              $status = 0;
                            }
                            ?>
                            <tr>
                              <td>
                                <?= $suratpensananmaterial->getField('NAMA');  ?>
                                <input type="hidden" name="reqMaterial[]" value="<?= $suratpensananmaterial->getField('SURATPESANANMATERIALID') ?>" style="width: 300px;"  readonly/>
                              </td>
                              <td> <?= numberToIna($suratpensananmaterial->getField('HARGA_SATUAN')) ?></td>
                              <td><?= $suratpensananmaterial->getField('QTY').' '.$suratpensananmaterial->getField('SATUAN') ?></td>
                              <td><?= numberToIna($suratpensananmaterial->getField('TOTAL')) ?></td>
                              <td>
                                <?php if ($reqview == '1') {
                                  if ($status == '1') { echo "Terima"; } else { echo "Belum"; }
                                } else { ?>
                                <select class="form-control" name="reqStatusTerima[]">
                                 <option <?php if ($status == '1') { echo "selected"; } ?> value="1">Terima</option>
                                 <option <?php if ($status == '0') { echo "selected"; } ?> value="0">Belum</option>
                                </select>
                                <?php
                                } ?>
                              </td>
                              <td>
                                  <input type="text" name="reqTanggalTerima[]" id="reqTanggalTerima" class="form-control easyui-datebox" value="<?= datetimeToPage($suratpensananmaterial->getField('TANGGAL_TERIMA'), "date") ?>" required style="width: 120%" <?php if ($reqview == '1') { echo "readonly"; } else { } ?>/>
                              </td>
                              <td>
                                  <input type="text" name="reqPersentase[]" id="reqPersentase" class="form-control easyui-validatebox" value="<?= $suratpensananmaterial->getField('PERSENTASE') ?>" id="reqPersentase" value=""  OnFocus="FormatAngka('reqPersentase')" OnKeyUp="FormatUang('reqPersentase')" OnBlur="FormatUang('reqPersentase')" required <?php if ($reqview == '1') { echo "readonly"; } else { } ?>/>
                              </td>
                              <td>
                                <input type="text" class="form-control easyui-validatebox" name="reqKeterangan[]" value="<?= $suratpensananmaterial->getField('STATUS_KETERANGAN') ?>" <?php if ($reqview == '1') { echo "readonly"; } else { } ?>/>
                              </td>
                            </tr>
                          <?php
                          }
                        }
                       ?>
                    </tbody>
                    <tfoot>
                      <tr>
                        <td colspan="3" style="text-align:right;"> <b>TOTAL</b> </td>
                        <td colspan="5"><span id="sumTotal"><?= numberToIna($sumTotal); ?></span></td>
                      </tr>
                    </tfoot>
                  </table>

                  <script type="text/javascript">
                     function calculate(no)
                      {
                        // alert(no);
                          var hargasatuan = document.getElementById('reqhargaSatuan'+no).value;
                          var qty = document.getElementById('reqQty'+no).value;
                          // alert('aaa');
                          var hargasatuanParsing = parseFloat(hargasatuan.split('.').join(""));
                          var qtyParsing = parseFloat(qty.split('.').join(""));
                          var total = qtyParsing * hargasatuanParsing;
                          $('#reqTotal'+no).val(FormatNumberya(total));
                          sum();
                      }
                      function sum() {
                        var aa = 0;
                        $('.sumTotal').each(function () {
                          totalSum = DeleteComma(this.value);
                          aa += parseInt(totalSum);
                        });
                        $('#sumTotal').html(FormatNumberya(aa));
                      }
                      function DeleteComma(MyString)
                      {
                      return MyString.replace(/\./g,'');
                      }

                      function FormatNumberya(id)
                      {
                         var a = parseFloat(id);
                         var nilai = FormatCurrency(a);
                         return nilai;
                      }
                  </script>
                </div>
              </div>
              <?php
              if ($reqview == '1') { } else { ?>
              <div class="form-actions">
                <input type="hidden" name="suratpesananid" id="suratpesananid" value="<?=$reqSuratPesananId?>"/>
                <input type="hidden" name="reqContractingRekananId" id="reqContractingRekananId" value="<?=$reqContractingRekananId?>"/>
                <input type="hidden" name="reqSubmit" id="reqSubmit" value="Submit"/>
                <button type="submit" class="<?= CLASS_BTN_PRIMARY ?>"><?= BTN_SIMPAN ?></button>
              </div>
              <?php
              } ?>
            </form>
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
