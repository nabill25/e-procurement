<?php
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 *
 */

$this->libsession->cekSession();

include_once("functions/string.func.php");
include_once("functions/default.func.php");
include_once("functions/date.func.php");
$this->load->model(array("Contracting","Contractingsuratpesanan"));
$this->load->model("Paketpemenang");
$this->load->model("Region");
$this->load->model("Rekanan");
$this->load->model("Contractingrekanan");

$getProses = $this->session->userdata('setProsesKontrak');

$contracting = new Contracting();
$getpaket_pemenang = new Paketpemenang();
$region = new Region();
$rekanan = new Rekanan();

$reqId = $this->input->get("reqId"); // CONTRACTINGREKANANPROSES1ID
$reqConRekId = $this->input->get("reqConRekId"); // CONTRACTINGREKANANID
$reqSuratPesananId = $this->input->get("reqSuratPesananId") ?: 0; // SURATPESANANID


$getMenu = new Contracting();
// $kontrak = new Contracting();
$contractingrekanan = new Contractingrekanan();
$spkpks = new Contractingrekanan();
$sppbj = new Contractingrekanan();
$proses4 = new Contractingrekanan();
$legal = new Contractingrekanan();
$suratpensanan = new Contractingsuratpesanan();

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

if ($reqSuratPesananId) {
  $reqSubmit = 'update';

  $v = $this->libkontrak->getNilaiKontrakPenyediaByNilai(" AND REKANAN_ID = ".$reqRakananId." AND CONTRACTINGREKANANID = ".$reqContractingRekananId);
  $sisaNilaiKontrak = $v['sisa'];
} else {
  $reqSubmit = 'simpan';
  $sisaNilaiKontrak = '';
}

$suratpensanan->selectByParams(array("A.SURATPESANANID" => $reqSuratPesananId));
$suratpensanan->firstRow();
$reqNoSuratPesanan = $suratpensanan->getField('NOMOR_SURAT') ?: '';
$reqTglSuratPesanan = dateToPageCheck($suratpensanan->getField('TANGGAL')) ?: '';

?>

<script type="text/javascript">
$(document).ready(function() {

  $(function(){
    $('#ff').form({
      url:'contracting_json/addSuratPesanan',
      onSubmit:function(){
        var v=$(this).form('validate');
        var aa = $('#reqSisaNilaiKontrak').val();
        const tot = getsum();
        // alert(aa+'----'+tot);
        if(v) {
          showLoad();  // show the message box
          if (tot > aa) {
            alertError2("Total "+tot+" melebihi nilai kontrak tersisa "+FormatNumberya(aa));
            hideLoad();
            return false;
          } else {
            return v;
          }
        } else {
          return false;
        }
        // if(v) showLoad();  // show the message box
      },
      success:function(str){
        var isNotif = str.split("--");
        if(isNotif[0] == "1") {
          alertError3(isNotif[1]);
          hideLoad();
        } else if(isNotif[0] == "0") {
          alertSuccess2(isNotif[1]);
          setTimeout(function () {
            document.location.href = "kontrak/index/contracting_pelaksanaan_kontrak_payung?reqId=<?=$reqConRekId?>";
          }, 1000);
        }

      }
    });
  });

  $('#reqPelaksanaanDari, #reqPelaksanaanSampai, #reqTglSuratPesanan').datebox({
    editable: false
  });

});

function createRowMaterial()
{
  $(function () {
    $.get("main/loadUrl/main/data_material_surat_pesanan_template?reqConRekId=<?= $reqConRekId ?>", function (data) {
      $("#tbodyMaterial").append(data);
    });
  });
}
</script>

<div class="row">
  <div class="col-md-12 col-sm-12">
    <div class="card">
      <div class="card-header card-head-inverse bg-primary">
        <h4 class="card-title text-white">Surat Pesanan</h4>
        <a class="heading-elements-toggle"><i class="fa fa-ellipsis-v font-medium-3"></i></a>
        <div class="heading-elements">
            <ul class="list-inline mb-0">
              <li><a data-action="expand"><i class="ft-maximize"></i></a></li>
              <!-- <li><a data-action="close"><i class="ft-x"></i></a></li> -->
            </ul>
        </div>
      </div>
      <div class="card-content collapse show border-info border-darken-2">
        <div class="card-body">
          <form id="ff" class="easyui-form form-horizontal" method="post" novalidate enctype="multipart/form-data">
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
            <div class="row">
              <div class="form-group col-md-10 mb-2">
                <label style="width: 100%">Nomor Surat Pesanan</label>
                <input type="text" name="reqNoSuratPesanan" id="reqNoSuratPesanan" class="form-control easyui-validatebox" value="<?=$reqNoSuratPesanan?>" required/>
              </div>
              <div class="form-group col-md-2 mb-2">
                <label style="width: 100%">Tanggal</label>
                <input type="text" name="reqTglSuratPesanan" id="reqTglSuratPesanan" class="form-control easyui-datebox" value="<?=$reqTglSuratPesanan?>" required style="width: 200%"/>
              </div>
            </div>

            <div class="row">
              <div class="form-group col-md-12 mb-2">
                <label>Nilai Pekerjaan</label>
                <input title="Nilai Pekerjaan" class="form-control easyui-validatebox span3"  name="reqNilaiKontrak" type="text" id="reqNilaiKontrak" value="<?=numberToIna($reqNilaiKontrak)?>"  OnFocus="FormatAngka('reqNilaiKontrak')" OnKeyUp="FormatUang('reqNilaiKontrak')" OnBlur="FormatUang('reqNilaiKontrak')" required readonly />
              </div>
            </div>

            <h4>Daftar Barang Jasa</h4>
            <table class="table table-bordered">
              <thead>
                <tr>
                  <th>Deskripsi</th>
                  <!-- <th width="30%">Deskripsi</th> -->
                  <th width="10%" class="text-center">Vol/Qty</th>
                  <th width="10%" class="text-center">Satuan</th>
                  <th width="20%">Harga Satuan</th>
                  <th width="20%">Total</th>
                  <th width="5px" class="text-center">Aksi</th>
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
                      ?>


                      <tr>
                        <td>
                          <input type="text" name="reqMaterial[]" required class="easyui-combobox span2"  id="reqMaterial<?=$no?>" data-options="valueField:'id',textField:'text',url:'contracting_json/comboMaterial/<?= $reqConRekId?>',onSelect: function(rec){
                              $.get('contracting_json/comboMaterialData/'+rec.id, function (data) {
                                  const obj = JSON.parse(data);
                                  $('#reqDeskripsi<?=$no?>').val(obj.keterangan);
                                  $('#reqhargaSatuan<?=$no?>').val(FormatNumberya(obj.harga_satuan));
                                  $('#reqNama<?=$no?>').val(obj.nama);
                                  $('#reqSatuan<?=$no?>').val(obj.satuan);
                                  $('#reqSifat<?=$no?>').val(obj.sifat);
                                  $('#reqQtyOld<?=$no?>').val(obj.qty);
                                  calculate(<?=$no?>);
                              });
                          }"  value="<?= $suratpensananmaterial->getField('MATERIALID') ?>" style="width: 300px;" />
                          <input type="hidden" class="form-control easyui-validatebox span6"  required name="reqNama[]" id="reqNama<?=$no?>" value="<?= $suratpensananmaterial->getField('NAMA') ?>"/>
                          <input type="hidden" class="form-control easyui-validatebox span6"  required name="reqSifat[]" id="reqSifat<?=$no?>" value="<?= $suratpensananmaterial->getField('SIFAT') ?>"/>
                        </td>

                        <td>
                          <input type="text" class="form-control easyui-validatebox span6"  required name="reqQty[]" id="reqQty<?=$no?>" OnFocus="FormatAngka('reqQty<?=$no?>')" OnKeyUp="calculate(<?=$no?>); sum();" OnBlur="FormatUang('reqQty<?=$no?>')" onchange="calculate(<?=$no?>); sum();"  value="<?= $suratpensananmaterial->getField('QTY') ?>"/>
                        </td>
                        <td>
                          <input type="text" class="form-control easyui-validatebox span6"  required name="reqSatuan[]" id="reqSatuan<?=$no?>" value="<?= $suratpensananmaterial->getField('SATUAN') ?>" readonly/>
                        </td>
                        <td>
                          <input type="text" class="form-control easyui-validatebox span6"  required name="reqhargaSatuan[]" id="reqhargaSatuan<?=$no?>" value="<?= $suratpensananmaterial->getField('HARGA_SATUAN') ?>" readonly/>
                        </td>
                        <td>
                          <input type="text" class="form-control easyui-validatebox span6 sumTotal"  required name="reqTotal[]" id="reqTotal<?=$no?>" value="<?= $suratpensananmaterial->getField('TOTAL') ?>" readonly/>
                        </td>
                        <td>
                           <a title="#" onclick="$(this).parent().parent().remove(); sum();" class="btn-aksi"><i class="fa fa-trash" aria-hidden="true"></i></a>
                        </td>

                      </tr>
                    <?php
                    }
                  }
                 ?>
              </tbody>
              <tfoot>
                <tr>
                  <td colspan="4" style="text-align:right;"> <b>TOTAL</b> </td>
                  <td colspan="2"><span id="sumTotal"><?= numberToIna($sumTotal); ?></span></td>
                </tr>
              </tfoot>
            </table>

            <div class="badge badge-pill badge-warning">
              <a onclick="createRowMaterial()"> <span class="fa fa-plus text-white icon-line-height" data-toggle="tooltip" data-placement="top" title="Tambah Barang Jasa"></span> Tambah</a>
            </div>

            <div class="form-actions">
              <input type="hidden" name="reqId" value="0">
              <input type="hidden" name="reqContractingRekananProses1Id" value="<?=$reqContractingRekananProses1Id?>">
              <input type="hidden" name="reqContractingRekananId" value="<?=$reqContractingRekananId?>">
              <input type="hidden" name="reqJanganIntip2" value="<?= 'Jan'.md5('ikn-****') ?>">
              <input type="hidden" name="reqBahasa" value="ID">
              <input type="hidden" name="reqSuratPesananId" value="<?= $reqSuratPesananId ?>">
              <input type="hidden" name="reqSisaNilaiKontrak" id="reqSisaNilaiKontrak" value="<?= $sisaNilaiKontrak ?>">
              <input type="hidden" name="reqSubmit" value="<?= $reqSubmit ?>">
              <a href="kontrak/index/contracting_pelaksanaan_kontrak_payung?reqId=<?=$reqConRekId?>&reqProses=<?= $getProses ?>" class="<?= CLASS_BTN_DANGER ?> mr-1"> <?= BTN_KEMBALI ?></a>
              <button id="btnSimpan" type="submit" class="<?= CLASS_BTN_PRIMARY ?>"><?= BTN_SIMPAN ?></button>
            </div>

          </form>
        </div>
      </div>
    </div>
  </div>
</div>

<script type="text/javascript">
 function calculate(no)
  {
      // Sifat=
      // 1:Berubah (Qty boleh diatas Vol/Qty yang sudah di tentukan,
      // 2:Tetap (Vol/Qty tidak boleh lebih dari Vol/Qty yang sudah di tentukan)

      var hargasatuan = $('#reqhargaSatuan'+no).val();
      var qty = $('#reqQty'+no).val();
      var reqMaterial = $('#reqMaterial'+no).combobox('getValue');
      var contractingRekananId = <?= $reqContractingRekananId ?>;
      var hargasatuanParsing = parseFloat(hargasatuan.split('.').join(""));
      var qtyParsing = parseFloat(qty.split('.').join(""));
      var total = qtyParsing * hargasatuanParsing;
      // var qtyOld = $('#reqQtyOld'+no).val();
      var sifat = $('#reqSifat'+no).val();
      if (sifat === '2') {
        $.get("contracting_json/cekSisaQty/"+contractingRekananId+"/"+reqMaterial, function (data) {
          const obj = JSON.parse(data);
          if (parseInt(qty) > obj.sisa) {
            $('#reqQty'+no).val(0);
            $('#reqTotal'+no).val(0);
            alertError2("Qty melebihi Maksimal, "+obj.nama+" tersisa "+obj.sisa);
            sum();
          } else {
            $('#reqTotal'+no).val(FormatNumberya(total));
            sum();
          }
        });
      } else {
        $('#reqTotal'+no).val(FormatNumberya(total));
        sum();
      }

      // Cek Nilai Sisa Kontrak Available
      $.get("contracting_json/cekSisaNilaiKontrak/"+contractingRekananId+"/"+<?= $reqRakananId?>, function (data) {
        sum();
        const obj = JSON.parse(data);
        const tot = getsum();
        $('#reqSisaNilaiKontrak').val(obj.sisa);
        if (tot > obj.sisa) {
          alertError2("Total "+tot+" melebihi nilai kontrak tersisa "+FormatNumberya(obj.sisa));
          $('#btnSimpan').hide();
          // return false;
        } else {
          $('#btnSimpan').show();
          sum();
        }
      });


  }

  function sum() {
    var aa = 0;
    $('.sumTotal').each(function () {
      totalSum = DeleteComma(this.value);
      aa += parseInt(totalSum);
    });
    $('#sumTotal').html(FormatNumberya(aa));
  }

  function getsum() {
    var aa = 0;
    $('.sumTotal').each(function () {
      totalSum = DeleteComma(this.value);
      aa += parseInt(totalSum);
    });
    return aa;
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
