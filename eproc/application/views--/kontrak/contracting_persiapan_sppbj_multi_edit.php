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
$this->load->library("paketinfo"); $paketInfo = new paketinfo();
$this->load->model("Paket");
$this->load->model("Contracting");
$this->load->model("Contractingrekanan");
$this->load->model("Paketpemenang");
$this->load->model("Region");
$this->load->model("Rekanan");
$this->load->model("PaketRekanan");
$this->load->model("PaketNegoisasi");

$reqId = $this->input->get("reqId"); // contractingrekananid
$reqRekananId = $this->input->get("reqRekananId"); // contractingrekananid
// echo $reqRekananId; die;
$paket = new Paket();
$contracting = new Contractingrekanan();
$region = new Region();
$rekanan = new Rekanan();
$sppbj = new Contractingrekanan();

$contracting->selectByParams(array("A.CONTRACTINGREKANANID" => $reqId));
$contracting->firstRow();
$reqNamaPaket = $contracting->getField("NAMA");
$reqPanitiaStr = $contracting->getField("PANITIA_STR");
$reqPenggunaStr = $contracting->getField("PENGGUNA_STR");
$reqPpkStr = $contracting->getField("PPK_STR");
$reqPemenangStr = $contracting->getField("PEMENANG_NAMA");

// get data contracting_rekanan_proses1
$sppbj->selectViewSPPBJ(array("A.CONTRACTINGREKANANID" => $reqId, "REKANAN_ID" => $reqRekananId));
$sppbj->firstRow();

$reqPaketId = $sppbj->getField('PAKET_ID') ?: '-';
$reqContractingRekananProses1Id = $sppbj->getField('CONTRACTINGREKANANPROSES1ID') ?: '-';
$reqContractingRekananId = $sppbj->getField('CONTRACTINGREKANANID') ?: '-';
$reqCode = $sppbj->getField('CR_SPPBJ_CODE') ?: '-';
$reqTanggal = dateToPageCheck($sppbj->getField('CR_SPPBJ_TANGGAL')) ?: '-';
$reqDirut = $sppbj->getField('CR_SPPBJ_DIRUT') ?: '-';
$reqDirutAlamat = $sppbj->getField('CR_SPPBJ_DIRUT_ALAMAT') ?: '-';
$reqDirutKota = $sppbj->getField('CR_SPPBJ_DIRUT_KOTA') ?: '-';
$reqDirutKotaStr = $sppbj->getField('CR_SPPBJ_DIRUT_KOTA_STR') ?: '-';
$reqDirutJabatan = $sppbj->getField('CR_SPPBJ_DIRUT_JABATAN') ?: '-';
$reqJaminanPelaksanaan = $sppbj->getField('CR_SPPBJ_JAMINAN_PELAKSANA') ?: '-';
$reqJaminanBesar = $sppbj->getField('CR_SPPBJ_JAMINAN_BESAR') ?: 0;
$reqJaminanJangkaDari = dateToPageCheck($sppbj->getField('CR_SPPBJ_JAMINAN_JANGKA_DARI')) ?: date('d-m-Y');
$reqJaminanJangkaSampai = dateToPageCheck($sppbj->getField('CR_SPPBJ_JAMINAN_JANGKA_SAMPAI')) ?: date('d-m-Y');
$reqJaminanNilai = $sppbj->getField('CR_SPPBJ_JAMINAN_NILAI') ?: 0;
$reqPejabatBerwenang = $sppbj->getField('CR_SPPBJ_PEJABAT_BERWENANG') ?: '-';
$reqNIP = $sppbj->getField('CR_SPPBJ_NIP') ?: '-';
$reqJabatan = $sppbj->getField('CR_SPPBJ_JABATAN') ?: '-';
$reqPPN = $sppbj->getField('CR_SPPBJ_PPN') ?: '-';
$reqPelaksanaanDari = dateToPageCheck($sppbj->getField('CR_SPPBJ_PELAKSANAAN_DARI')) ?: '-';
$reqPelaksanaanSampai = dateToPageCheck($sppbj->getField('CR_SPPBJ_PELAKSANAAN_SAMPAI')) ?: '-';
$reqCreatedBy = $sppbj->getField('CR_SPPBJ_CREATED_BY') ?: '-';
$reqCreatedDate = $sppbj->getField('CR_SPPBJ_CREATED_DATE') ?: '-';
$reqNilai = $sppbj->getField('CR_SPPBJ_NILAI') ?: '-';
$reqContractingStatusKontrakId = $sppbj->getField('CONTRACTINGSTATUSKONTRAKID') ?: '-';
$reqRekananId = $sppbj->getField('REKANAN_ID') ?: '-';

// Get Rekanan
$rekanan->selectByParams(array("A.REKANAN_ID" => $reqRekananId), -1, -1);
$rekanan->firstRow();
$rekanan_nama = $rekanan->getField("NAMA");
$rekanan_npwp = $rekanan->getField("NPWP");
$rekanan_alamat = $rekanan->getField("ALAMAT");
$rekanan_telepon = $rekanan->getField("TELEPON_FULL");
$rekanan_email = $rekanan->getField("EMAIL");
$rekanan_kota = $rekanan->getField("KOTA");
$rekanan_kodepos = $rekanan->getField("KODEPOS");
?>

<script type="text/javascript">
$(document).ready(function() {

  $(function(){
    $('#ff').form({
      url:'contracting_json/addSppbj',
      onSubmit:function(){
        var v=$(this).form('validate');
        if(v) showLoad();  // show the message box
        return v;
      },
      success:function(data){
        // alert(data);return false;
        // alert(data);return false;
        // document.location.href = 'kontrak/index/contracting_persiapan';
        if (data === 'Data proses gagal diubah.') {
          alertError3(data);
          hideLoad();
        } else {
          alertSuccess2(data);
          setTimeout(function () {
            document.location.href = 'kontrak/index/contracting_persiapan_sppbj_multi?reqId=<?=$reqId?>&reqProses=1';
          }, 1000);
        }
      }
    });
  });

$("#reqJaminanPelaksanaan").click(countChecked);
});


function countChecked() {
  var n = $("#reqJaminanPelaksanaan:checked").length;
  //alert(n);
  if(n){
    $("#form-jaminan-pelaksanaan").show(0);
  }else{
    $("#form-jaminan-pelaksanaan").hide(0);
  }
}

function calculate(a)
{
    persen = a.value;
    nilai = document.getElementById('reqNilai').value;
    // alert(persen+'--'+nilai);
    persenParsing = parseFloat(persen.split('.').join(""));
    nilaiParsing = parseFloat(nilai.split('.').join(""));
    total = nilaiParsing * (persenParsing/100);
    $('#reqNilaiJaminan').val(FormatNumberya(total));
}
function FormatNumberya(id)
{
   var a = parseFloat(id);
   var nilai = FormatCurrency(a);
   return nilai;
}
</script>
<div class="row">
  <div class="col-md-12 col-sm-12">
    <div class="card">
      <div class="card-header card-head-inverse bg-primary">
        <h4 class="card-title text-white">Edit SPPBJ </h4>
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
              <div class="form-group col-md-9 mb-2">
                <label>Nomor SPPBJ</label>
                <input type="text" name="reqKode" id="reqKode" class="form-control easyui-validatebox" value="<?=$reqCode?>" required/>
              </div>
              <div class="form-group col-md-3 mb-2">
                <label style="width: 100%">Tanggal SPPBJ</label>
                <input type="text" name="reqTanggal" id="reqTanggal" class="form-control easyui-datebox" value="<?=$reqTanggal?>" required style="width: 200%"/>
              </div>
            </div>
            <div class="row">
              <div class="form-group col-md-5 mb-2">
                <label>Pejabat Berwenang</label>
                <input type="text" name="reqPejabatBerwenang" id="reqPejabatBerwenang" value="<?=$reqPejabatBerwenang?>" class="form-control easyui-validatebox" required/>
              </div>
              <div class="form-group col-md-3 mb-2">
                <label>NPP</label>
                <input type="text" name="reqNIP" id="reqNIP" value="<?=$reqNIP?>" class="form-control easyui-validatebox" required/>
              </div>
              <div class="form-group col-md-4 mb-2">
                <label>Jabatan</label>
                <input type="text" name="reqJabatan" id="reqJabatan" value="<?=$reqJabatan?>" class="form-control easyui-validatebox" required/>
              </div>
            </div>
            <div class="row">
              <div class="form-group col-md-5 mb-2">
                <label>Nama Direktur <sup>Penyedia</sup></label>
                <input type="text" name="reqNamaDirut" id="reqNamaDirut" class="form-control easyui-validatebox" value="<?=$reqDirut?>" required/>
              </div>
              <div class="form-group col-md-3 mb-2">
                <label style="width: 100%">Kota <sup>Penyedia</sup></label>
                <input type="text" name="reqKota" class="easyui-combobox span4" data-options="valueField:'id',textField:'text',url:'region_json/combo'"  value="<?=$reqDirutKota?>" style="width: 300% !important" />
              </div>
              <div class="form-group col-md-4 mb-2">
                <label>Jabatan <sup>Penyedia</sup></label>
                <input type="text" name="reqJabatanDirut" id="reqJabatanDirut" value="<?=$reqDirutJabatan?>" class="form-control easyui-validatebox" required/>
              </div>
            </div>
            <div class="row">
              <div class="form-group col-md-12 mb-2">
                <label style="width: 100%">Alamat <sup>Penyedia</sup></label>
                <textarea name="reqAlamatDirut" id="reqAlamatDirut" cols="45" rows="5" class="easyui-validatebox"  required  style="width: 100%"><?=$reqDirutAlamat?></textarea>
              </div>
            </div>
            <div class="row">
              <div class="form-group col-md-4 mb-2">
                <label>Nilai Kontrak</label>
                <input title="Nilai Kontrak harus diisi" class="form-control easyui-validatebox span3"  name="reqNilai" type="text" id="reqNilai" value="<?=numberToIna($reqNilai)?>"  OnFocus="FormatAngka('reqNilai')" OnKeyUp="FormatUang('reqNilai')" OnBlur="FormatUang('reqNilai')" required/>
              </div>
              <div class="form-group col-md-5 mb-2">
                <label style="width: 100%">Masa Pelaksanaan Pekerjaan</label>
                <input type="text" name="reqPelaksanaanDari" id="reqPelaksanaanDari" class="form-control easyui-datebox" value="<?=$reqPelaksanaanDari?>" required style="width: 200%"/> <span style="margin:0 2%">s/d</span>
                <input type="text" name="reqPelaksanaanSampai" id="reqPelaksanaanSampai" class="form-control easyui-datebox" value="<?=$reqPelaksanaanSampai?>" required style="width: 200%"/>
              </div>
              <div class="form-group col-md-1 mb-2">
                <br><br>
                <input type="checkbox" name="reqPPN" id="reqPPN" value="1" <?php if($reqPPN == "1") { ?> checked="checked" <?php }?>  style="cursor: pointer;"> PPN
              </div>
              <div class="form-group col-md-2 mb-2">
                <br><br>
                <input type="checkbox" name="reqJaminanPelaksanaan" id="reqJaminanPelaksanaan" value="1" <?php if($reqJaminanPelaksanaan == "1") { ?> checked="checked" <?php }?> style="cursor: pointer;"> Jaminan Pelaksanaan
              </div>
            </div>
            <div class="row" id="form-jaminan-pelaksanaan" style="<?php if ($reqJaminanPelaksanaan == "1") { } else { echo 'display: none'; } ?>">
              <div class="form-group col-md-2 mb-2">
                <label>Persen Jaminan % <small>(diisi angka)</small></label>
                <input type="text" name="reqPersenJaminan" onkeypress="return isNumberKey(event)" id="reqPersenJaminan" class="form-control easyui-validatebox" value="<?=$reqJaminanBesar?>" onchange="calculate(this)"/>
              </div>
              <div class="form-group col-md-5 mb-2">
                <label>Nilai Jaminan <small>(diisi angka)</small></label>
                <input title="Nilai Jaminan harus diisi" class="form-control easyui-validatebox span3"  name="reqNilaiJaminan" type="text" id="reqNilaiJaminan" value="<?=numberToIna($reqJaminanNilai)?>"  OnFocus="FormatAngka('reqNilaiJaminan')" OnKeyUp="FormatUang('reqNilaiJaminan')" OnBlur="FormatUang('reqNilaiJaminan')"/>
              </div>
              <div class="form-group col-md-5 mb-2">
                <label style="width: 100%">Jangka Jaminan Pelaksanaan</label>
                <input type="text" name="reqJangkaDari" id="reqJangkaDari" class="form-control easyui-datebox" value="<?=$reqJaminanJangkaDari?>" style="width: 200%"/> <span style="margin:0 2%">s/d</span>
                <input type="text" name="reqJangkaSampai" id="reqJangkaSampai" class="form-control easyui-datebox" value="<?=$reqJaminanJangkaSampai?>" style="width: 200%"/>
              </div>
            </div>

            <div class="form-actions">
              <input type="hidden" name="reqId" value="0">
              <input type="hidden" name="reqContractingRekananProses1Id" value="<?=$reqContractingRekananProses1Id?>">
              <input type="hidden" name="reqContractingRekananId" value="<?=$reqContractingRekananId?>">
              <input type="hidden" name="reqJanganIntip2" value="<?= 'Jan'.md5('ikn-****') ?>">
              <input type="hidden" name="reqBahasa" value="ID">
              <a href="kontrak/index/contracting_persiapan_sppbj_multi?reqId=<?=$reqId?>&reqProses=1" class="<?= CLASS_BTN_DANGER ?> mr-1"> <?= BTN_KEMBALI ?> </a>
              <button type="submit" class="<?= CLASS_BTN_PRIMARY ?>"><?= BTN_SIMPAN ?></button>
            </div>

          </form>
        </div>
      </div>
    </div>
  </div>
</div>
