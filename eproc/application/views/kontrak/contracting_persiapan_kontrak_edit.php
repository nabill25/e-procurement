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
$this->load->model(array("Contracting","Paketpemenang","Region","Rekanan","Contractingrekanan","Userlogin"));

$getProses = $this->session->userdata('setProsesKontrak');

$contracting = new Contracting();
$getpaket_pemenang = new Paketpemenang();
$region = new Region();
$rekanan = new Rekanan();

$reqId = $this->input->get("reqId"); // contractingrekananid
$reqBack = $this->input->get("back"); // contractingrekananid
$this->libsession->cekSessionKontrakPPK($reqId);

$getMenu = new Contracting();
// $kontrak = new Contracting();
$contractingrekanan = new Contractingrekanan();
$spkpks = new Contractingrekanan();
$sppbj = new Contractingrekanan();
$proses4 = new Contractingrekanan();
$legal = new Contractingrekanan();

$contracting->selectByParams(array("A.CONTRACTINGREKANANID" => $reqId));
$contracting->firstRow();
$reqNamaPaket = $contracting->getField("NAMA");
$reqPanitiaStr = $contracting->getField("PANITIA_STR");
$reqPenggunaStr = $contracting->getField("PENGGUNA_STR");
$reqPpkStr = $contracting->getField("PPK_STR");
$reqPemenangStr = $contracting->getField("PEMENANG_NAMA");

$sppbj->selectViewSPPBJ(array("A.CONTRACTINGREKANANID" => $reqId));
$sppbj->firstRow();
$reqNilaiSPPBJ = $sppbj->getField('CR_SPPBJ_NILAI') ?: '';
$reqDirutSPPBJ = $sppbj->getField('CR_SPPBJ_DIRUT') ?: '';
$reqDirutJabatanSPPBJ = $sppbj->getField('CR_SPPBJ_DIRUT_JABATAN') ?: '';
$reqPejabatBerwenangSPPBJ = $sppbj->getField('CR_SPPBJ_PEJABAT_BERWENANG') ?: '';
$reqJabatanSPPBJ = $sppbj->getField('CR_SPPBJ_JABATAN') ?: '';

$spkpks->selectViewPKSSPK(array("A.CONTRACTINGREKANANID" => $reqId));
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
$reqContractingjeniskontrakid = $spkpks->getField('CONTRACTINGJENISKONTRAKID') ?: '';
$reqJenisKontrakStr = $spkpks->getField('CR_JENIS_KONTRAK_STR') ?: '-';
$reqWaktuPelaksanaanDari = dateToPageCheck($spkpks->getField('CR_WAKTU_PELAKSANAAN_DARI')) ?: '';
$reqWaktuPelaksanaanSampai = dateToPageCheck($spkpks->getField('CR_WAKTU_PELAKSANAAN_SAMPAI')) ?: '';
$reqLingkupPekerjaan = $spkpks->getField('CR_LINGKUP_PEKERJAAN') ?: '-';
$reqNilaiKontrak = $spkpks->getField('CR_NILAI_KONTRAK') ?: $reqNilaiSPPBJ;
$reqMetodePembayaran = $spkpks->getField('CR_METODE_PEMBAYARAN') ?: '-';
$reqNamaKegiatan = $spkpks->getField('CR_NAMA_KEGIATAN') ?: '';
// $reqPihak1Nama = $spkpks->getField('CR_PIHAK1_NAMA') ?: $reqPejabatBerwenangSPPBJ;
// $reqPihak1Jabatan = $spkpks->getField('CR_PIHAK1_JABATAN') ?: $reqJabatanSPPBJ;
$reqPihak2Nama = $spkpks->getField('CR_PIHAK2_NAMA') ?: $reqDirutSPPBJ;
$reqPihak2Jabatan = $spkpks->getField('CR_PIHAK2_JABATAN') ?: $reqDirutJabatanSPPBJ;
$reqPihak2 = $spkpks->getField('CR_PIHAK2_PERUSAHAAN') ?: '';
$reqCreatedBy = $spkpks->getField('CR_CREATED_BY') ?: '-';
$reqContractingStatusKontrakId = $spkpks->getField('CONTRACTINGSTATUSKONTRAKID') ?: '-';
$reqRakananId = $spkpks->getField('REKANAN_ID') ?: '-';
$reqPO = $spkpks->getField('CR_PO') ?: '-';
$reqTanggalHasilPemilihan = dateToPageCheck($spkpks->getField('CR_TGL_HASIL_TERIMA_PEMILIHAN')) ?: '';
$reqPenyelesaianAwal = dateToPageCheck($spkpks->getField('CR_PENYELESAIAN_KONTRAK_AWAL')) ?: '';
$reqPenyelesaianAkhir = dateToPageCheck($spkpks->getField('CR_PENYELESAIAN_KONTRAK_AKHIR')) ?: '';
$reqMasaGaransi = $spkpks->getField('CR_MASA_GARANSI') ?: '1';
$reqMasaGaransiPeriode = $spkpks->getField('CR_MASA_GARANSI_PERIODE') ?: '1';
$reqUnitKerjaId = $spkpks->getField('UNIT_KERJA_ID');

$userPPK = new Userlogin();
$userPPK->selectByParams(array("UNIT_KERJA_ID" => $reqUnitKerjaId, "USER_TYPE_ID" => "28", "USER_AKTIF" => '1'));
$userPPK->firstRow();
$reqPPK = $userPPK->getField("USER_NAMA");
$reqPPKJabatan = $userPPK->getField("USER_JABATAN");
$reqPihak1Nama = $spkpks->getField('CR_PIHAK1_NAMA') ?: $reqPPK;
$reqPihak1Jabatan = $spkpks->getField('CR_PIHAK1_JABATAN') ?: $reqPPKJabatan;

$legal->selectViewLegal(array("A.CONTRACTINGREKANANID" => $reqId));
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

$proses4->selectProses4(array("A.CONTRACTINGREKANANID" => $reqId));
$proses4->firstRow();

if ($proses4->countRow() > 0) {
  $reqSubmit = 'update';
} else {
  $reqSubmit = 'simpan';
}

?>

<script type="text/javascript">
$(document).ready(function() {

  $(function(){
    $('#ff').form({
      url:'contracting_json/addSPKPKS',
      onSubmit:function(){
        var v=$(this).form('validate');
        if(v) showLoad();  // show the message box
        return v;
      },
      success:function(str){
        var isNotif = str.split("--");
        if(isNotif[0] == "1") {
          alertError3(isNotif[1]);
        } else if(isNotif[0] == "0") {
          alertSuccess2(isNotif[1]);
        }
        setTimeout(function () {
          <?php 
          if ($reqBack) { ?>
            document.location.href = 'kontrak/index/<?= $reqBack ?>?reqId=<?= $reqId ?>';
          <?php 
          } else { ?>
            document.location.href = 'kontrak/index/contracting_persiapan_kontrak?reqId=<?= $reqId ?>';
          <?php 
          } ?>
         }, 2000);
      }
    });
  });

  $('#reqPelaksanaanDari, #reqPelaksanaanSampai, #reqLegalTanggal, #reqTanggalHasilPemilihan, #reqPenyelesaianAwal, #reqPenyelesaianAkhir').datebox({
    editable: false
  });

  $("#reqMasaGaransi").click(countChecked);

  function countChecked() {
    var n = $("#reqMasaGaransi:checked").length;
    //alert(n);
    if(n){
      $("#masa-garansi-periode").show(0);
    }else{
      $("#masa-garansi-periode").hide(0);
    }
  }


});
</script>
<div class="row">
  <div class="col-md-12 col-sm-12">
    <div class="card">
      <div class="card-header card-head-inverse bg-primary">
        <h4 class="card-title text-white">Edit Kontrak</h4>
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
                    <tr> <td class="text-right"><?= SYSTEM_NAME_PT ?></td> </tr>
                    <tr> <td class="text-right"><?= SYSTEM_ALAMAT_PT ?></td> </tr>
                  </table>
              </div>
            </div>
            <div class="row">
              <div class="form-group col-md-5 mb-2">
                <label style="width: 100%">Nomor <?= $reqJnsKontrakStr ?> <?= SYSTEM_NAME_PT ?></label>
                <input type="text" name="reqLegalNomorPKS" id="reqLegalNomorPKS" class="form-control easyui-validatebox" value="<?=$reqLegalNomorPKS?>" required/>
              </div>
              <div class="form-group col-md-3 mb-2">
                <label style="width: 100%">Nomor PO</label>
                <input type="text" name="reqPO" id="reqPO" class="form-control easyui-validatebox" value="<?=$reqPO?>" required/>
              </div>
              <div class="form-group col-md-2 mb-2">
                <label style="width: 100%">Tanggal <?= $reqJnsKontrakStr ?></label>
                <input type="text" name="reqLegalTanggal" id="reqLegalTanggal" class="form-control easyui-datebox" value="<?=$reqLegalTanggal?>" required style="width: 200%"/>
              </div>
              <div class="form-group col-md-2 mb-2">
                <label style="width: 100%">Terima Laporan Hasil Pemilihan</label>
                <input type="text" name="reqTanggalHasilPemilihan" id="reqTanggalHasilPemilihan" class="form-control easyui-datebox" value="<?=$reqTanggalHasilPemilihan?>" required style="width: 200%"/>
              </div>
            </div>
            <div class="row">
              <!-- <div class="form-group col-md-12 mb-2">
                <label>Nomor PKS Penyedia</label>
                <input type="text" name="reqLegalNomorRekanan" id="reqLegalNomorRekanan" class="form-control easyui-validatebox" value="<?php // $reqLegalNomorRekanan?>" required/>
              </div>  -->
              <!-- <div class="form-group col-md-4 mb-2">
                <label style="width: 100%">Tanggal</label>
                <input type="text" name="reqLegalTanggalRekanan" id="reqLegalTanggalRekanan" class="form-control easyui-datebox" value="<?php // $reqLegalTanggalRekanan ?>" required style="width: 200%"/>
              </div>  -->
            </div>
            <div class="row">
              <!-- <div class="form-group col-md-5 mb-2">
                <label>Nomor Kontrak</label>
                <input type="text" name="reqCode" id="reqCode" class="form-control easyui-validatebox" value="<?php //$reqCode?>" required/>
              </div>  -->
              <div class="form-group col-md-12 mb-2">
                <label>Nilai Kontrak</label>
                <input title="Nilai Pekerjaan" class="form-control easyui-validatebox span3"  name="reqNilaiKontrak" type="text" id="reqNilaiKontrak" value="<?=numberToIna($reqNilaiKontrak)?>"  OnFocus="FormatAngka('reqNilaiKontrak')" OnKeyUp="FormatUang('reqNilaiKontrak')" OnBlur="FormatUang('reqNilaiKontrak')" required />
              </div>
            </div>
            <div class="row">
              <div class="form-group col-md-3 mb-2">
                <label>Metode Pembayaran</label><br>
                  <input type="radio" name="reqMetodePembayaran" title="" value="1" id="reqMetodePembayaran1"  <?php if($reqMetodePembayaran == '1' || $reqMetodePembayaran == '') { ?> checked="checked" <?php } ?> /> Sekaligus &nbsp;&nbsp;&nbsp;
                  <input type="radio" name="reqMetodePembayaran" title="" value="2" id="reqMetodePembayaran2"  <?php if($reqMetodePembayaran == '2') { ?> checked="checked" <?php } ?> /> Termin &nbsp;&nbsp;&nbsp;
                  <input type="radio" name="reqMetodePembayaran" title="" value="3" id="reqMetodePembayaran3"  <?php if($reqMetodePembayaran == '3') { ?> checked="checked" <?php } ?> /> Bulanan
              </div>
              <div class="form-group col-md-2 mb-2">
                <label style="width: 100%">Jenis Pengadaan</label>
                <input type="text" name="reqJenisPengadaan" class="easyui-combobox span4" data-options="valueField:'id',textField:'text',url:'contracting_json/comboJenisPengadaan'"  value="<?=$reqJenisPengadaan?>" style="width: 200% !important" required/>
              </div>
              <div class="form-group col-md-3 mb-2">
                <label style="width: 100%">Jenis Kontrak</label>
                <input type="text" name="reqContractingjeniskontrakid" class="easyui-combobox span4" data-options="valueField:'id',textField:'text',url:'contracting_json/comboJenisKontrak'"  value="<?=$reqContractingjeniskontrakid?>" style="width: 300% !important" required/>
              </div>
              <div class="form-group col-md-3 mb-2">
                <label style="width: 100%">Nama Kegiatan</label>
                <input type="text" name="reqNamaKegiatan" class="easyui-combobox span4" data-options="valueField:'id',textField:'text',url:'contracting_json/comboKegiatan'"  value="<?=$reqNamaKegiatan?>" style="width: 300% !important" required/>
              </div>
              <!-- <div class="form-group col-md-3 mb-2">
                <label>Jenis Pekerjaan</label><br>
                  <input type="radio" name="reqJenisPekerjaan" title="" value="1" id="reqJenisPekerjaan1"  <?php //if($reqJenisPekerjaan == '1' || $reqJenisPekerjaan == '') { ?> checked="checked" <?php //} ?> /> TI <br>
                  <input type="radio" name="reqJenisPekerjaan" title="" value="2" id="reqJenisPekerjaan2"  <?php //if($reqJenisPekerjaan == '2') { ?> checked="checked" <?php //} ?> /> Hasil Pekerjaan (umum)
              </div> -->
            </div>

            <div class="row">
              <div class="form-group col-md-4 mb-2">
                <label style="width: 100%">Tanggal Kontrak (awal dan akhir)</label>
                <input type="text" name="reqPelaksanaanDari" id="reqPelaksanaanDari" class="form-control easyui-datebox" value="<?=$reqWaktuPelaksanaanDari?>" required style="width: 180%"/> <span style="margin:0 2%">s/d</span>
                <input type="text" name="reqPelaksanaanSampai" id="reqPelaksanaanSampai" class="form-control easyui-datebox" value="<?=$reqWaktuPelaksanaanSampai?>" required style="width: 180%"/>
              </div>
              <div class="form-group col-md-4 mb-2">
                <label style="width: 100%">Tanggal Penyelesaian Tagihan (awal dan akhir)</label>
                <input type="text" name="reqPenyelesaianAwal" id="reqPenyelesaianAwal" class="form-control easyui-datebox" value="<?=$reqPenyelesaianAwal?>" required style="width: 180%"/> <span style="margin:0 2%">s/d</span>
                <input type="text" name="reqPenyelesaianAkhir" id="reqPenyelesaianAkhir" class="form-control easyui-datebox" value="<?=$reqPenyelesaianAkhir?>" required style="width: 180%"/>
              </div>
              <div class="form-group col-md-2 mb-2">
                <label></label><br>
                <input type="checkbox" name="reqMasaGaransi" id="reqMasaGaransi" value="1" <?php if($reqMasaGaransi == "1") { ?> checked="checked" <?php }?> style="cursor: pointer;"> Masa Garansi?
              </div>
              <div class="form-group col-md-2 mb-2" id="masa-garansi-periode" <?php if($reqMasaGaransi == "1") { ?> style="display:display" <?php } else {?> style="display: none;" <?php } ?>
                <label>&nbsp;</label>
                <select class="form-control" name="reqMasaGaransiPeriode">
                  <option value="1" <?php if($reqMasaGaransiPeriode == '1') { echo "selected"; } ?>>1 Bulan</option>
                  <option value="6" <?php if($reqMasaGaransiPeriode == '6') { echo "selected"; } ?>>6 Bulan</option>
                  <option value="12" <?php if($reqMasaGaransiPeriode == '12') { echo "selected"; } ?>>12 Bulan</option>
                  <option value="24" <?php if($reqMasaGaransiPeriode == '24') { echo "selected"; } ?>>24 Bulan</option>
                </select>
              </div>
            </div>

            <div class="row">
              <!-- <div class="form-group col-md-12" style="margin-bottom: 0px !important;"><h3>PIHAK I</h3><hr></div> -->
              <div class="form-group col-md-6 mb-2">
                <label>Nama (Pihak I)</label>
                <input type="text" name="reqPihak1Nama" id="reqPihak1Nama" class="form-control easyui-validatebox" value="<?=$reqPihak1Nama?>" <?php if ($reqPihak1Nama) { echo 'required'; } else { echo 'required'; } ?> />
              </div>
              <div class="form-group col-md-6 mb-2">
                <label>Jabatan (Pihak I)</label>
                <input type="text" name="reqPihak1Jabatan" id="reqPihak1Jabatan" class="form-control easyui-validatebox" value="<?=$reqPihak1Jabatan?>" <?php if ($reqPihak1Jabatan) { echo 'required'; } else { echo 'required'; } ?>/>
              </div>
            </div>
            <div class="row">
              <div class="form-group col-md-6 mb-2">
                <label>Nama (Pihak II)</label>
                <input type="text" name="reqPihak2Nama" id="reqPihak2Nama" class="form-control easyui-validatebox" value="<?=$reqPihak2Nama?>" <?php if ($reqPihak2Nama) { echo 'readonly'; } else { echo 'required'; } ?>/>
              </div>
              <div class="form-group col-md-6 mb-2">
                <label>Jabatan (Pihak II)</label>
                <input type="text" name="reqPihak2Jabatan" id="reqPihak2Jabatan" class="form-control easyui-validatebox" value="<?=$reqPihak2Jabatan?>" <?php if ($reqPihak2Jabatan) { echo 'readonly'; } else { echo 'required'; } ?>/>
              </div>
            </div>
           <!--  <div class="row">
              <div class="form-group col-md-12 mb-2">
                <label style="width: 100%">Lingkup Pekerjaan</label>
                <textarea name="reqLingkupPekerjaan" id="reqLingkupPekerjaan" cols="45" rows="10" class="textarea-tinymce"  required  style="width: 100%"><?php // echo $reqLingkupPekerjaan?></textarea>
              </div>
            </div> -->

            <div class="form-actions">
              <input type="hidden" name="reqId" value="0">
              <input type="hidden" name="reqContractingRekananProses1Id" value="<?=$reqContractingRekananProses1Id?>">
              <input type="hidden" name="reqContractingRekananId" value="<?=$reqContractingRekananId?>">
              <input type="hidden" name="reqContractingStatusKontrakId" value="<?=$reqContractingStatusKontrakId?>">              
              <input type="hidden" name="reqJanganIntip2" value="<?= 'Jan'.md5('ikn-****') ?>">
              <input type="hidden" name="reqBahasa" value="ID">
              <?php
              if ($reqSubmit == 'update') { // ada perubahan kontrak di monitoring
              $back = $this->input->get("back");
              ?>
              <a href="kontrak/index/<?= $back ?>?reqId=<?=$reqId?>&reqProses=<?= $getProses ?>" class="<?= CLASS_BTN_DANGER ?> mr-1 text-white"> <?= BTN_KEMBALI ?> </a>
              <?php
              } else
              { // tidak ada perubahan ?>
              <a href="kontrak/index/contracting_persiapan_kontrak?reqId=<?=$reqId?>" class="<?= CLASS_BTN_DANGER ?> mr-1"> <?= BTN_KEMBALI ?> </a>
              <?php
              } ?>
              <button type="submit" class="<?= CLASS_BTN_PRIMARY ?>"><?= BTN_SIMPAN ?></button>
            </div>

          </form>
        </div>
      </div>
    </div>
  </div>
</div>
