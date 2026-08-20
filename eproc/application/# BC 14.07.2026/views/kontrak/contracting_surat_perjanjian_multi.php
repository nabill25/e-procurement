<?php
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 * 
 */

$this->libsession->cekSession();

$this->LEGAL =  $this->kauth->getInstance()->getIdentity()->LEGAL;

if ($this->LEGAL == '1') { // Bagian legal keluar, inputan khusu pengelola kontrak
  redirect(base_url().'main/index/403');
}

include_once("functions/string.func.php");
include_once("functions/default.func.php");
include_once("functions/date.func.php");
$this->load->library("paketinfo"); $paketInfo = new paketinfo();
$this->load->model("Paket");
$this->load->model("Contracting");
$this->load->model("Paketpemenang");
$this->load->model("Region");
$this->load->model("Rekanan");
$this->load->model("PaketRekanan");
$this->load->model("PaketNegoisasi");

$paket = new Paket();
$contracting = new Contracting();
$getpaket_pemenang = new Paketpemenang();
$region = new Region();
$rekanan = new Rekanan();
$paket_negosiasi = new PaketNegoisasi();
$paket_rekanan = new PaketRekanan();

$reqId = $this->input->get("reqId");
$tahun = $this->input->get("tahun");
$jnskontrak = $this->input->get("jnskontrak");
$id = $this->input->get("id");

$paketInfo->getPaket($reqId);
$bidding = $paketInfo->bidding;
$reqMultiPemenang = $paketInfo->multi_pemenang;

$contracting->selectByParams(array("A.PAKET_ID" => $reqId));
$contracting->firstRow();
$reqNamaPaket = $contracting->getField("NAMA");
$reqNilai = $contracting->getField("NILAI");
$reqPanitiaStr = $contracting->getField("PANITIA_STR");
$reqPenggunaStr = $contracting->getField("PENGGUNA_STR");
$reqJabatan = $contracting->getField("JABATAN");
$reqNIP = $contracting->getField("NIP");
$reqPpkStr = $contracting->getField("PPK_STR");
$reqPemenangStr = $contracting->getField("PEMENANG_NAMA");
// echo $reqMultiPemenang.'---'; die;
// get data pemenang
if ($reqMultiPemenang == '0') {
  // echo "1 Pemanang";
  $getpaket_pemenang->selectByParams(array("PAKET_ID" => $reqId, "PERINGKAT" => '1'), -1, -1);
  $getpaket_pemenang->firstRow();
  $rekanan_id = $getpaket_pemenang->getField("REKANAN_ID");
  $rekanan->selectByParams(array("A.REKANAN_ID" => $rekanan_id), -1, -1);
  $rekanan->firstRow();
  $rekanan_nama = $rekanan->getField("NAMA");
  $rekanan_npwp = $rekanan->getField("NPWP");
  $rekanan_alamat = $rekanan->getField("ALAMAT");
  $rekanan_telepon = $rekanan->getField("TELEPON_FULL");
  $rekanan_email = $rekanan->getField("EMAIL");
  $rekanan_kota = $rekanan->getField("KOTA");
  $rekanan_kodepos = $rekanan->getField("KODEPOS");
} else {
  // echo "Multi Pemanang";
  $getpaket_pemenang->selectByParams(array("PAKET_ID" => $reqId, "PERINGKAT" => '1'), -1, -1); 
}
// get data paket rekanan (penawaran)
$paket_rekanan->selectByParams3(array("A.PAKET_ID" => $reqId, "A.REKANAN_ID" => $rekanan_id), -1, -1);
$paket_rekanan->firstRow();
?>

<script type="text/javascript">
$(document).ready(function() {

  $(function(){
    $('#ff').form({
      url:'contracting_json/addSppbjMulti',
      onSubmit:function(){
        var v=$(this).form('validate');
        if(v) showLoad();  // show the message box
        return v;
      },
      success:function(data){
        hideLoad();
        // alert(data);return false;
        if (data == 'Data gagal simpan.') {
          alertError2(data);
        } else {
          alertSuccess2(data);
        }
        setTimeout(function() {
          if (data == 'Data gagal simpan.') {
          } else {
            document.location.href = 'kontrak/index/contracting_persiapan_sppbj_multi?reqId=<?=$id?>';
          }
        }, 2000);
      }
    });
  });

  $("#reqJaminanPelaksanaan").click(countChecked);

  $('#reqTanggal, #reqPelaksanaanDari, #reqPelaksanaanSampai, #reqJangkaDari, #reqJangkaSampai').datebox({
    editable: false
  });
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

function getDataPemenang(rekananid) {
  $(function () {
    $.get("contracting_json/getDataPemenang/"+rekananid, function (data) {
      const obj = JSON.parse(data);
      $('#rekananNama').html(obj.nama);
      $('#rekananNPWP').html(obj.npwp);
      $('#rekananTelepon').html(obj.telepon);
      $('#rekananEmail').html(obj.email);
      $('#rekananAlamat').html(obj.alamat);
      $('#reqNamaDirut').val(obj.direktur);
      $('#reqJabatanDirut').val(obj.jabatan);
      $('#reqKota').combobox('setValue',obj.kota);
      $('#reqAlamatDirut').val(obj.alamat);
    });
  });
}
</script>
<style type="text/css">
  /*.fa { width: 2%; text-align: center; }*/
  sup { font-style: italic; color: red;}
</style>
<div class="row">
  <div class="col-md-12 col-sm-12">
    <div class="card">
      <div class="card-header card-head-inverse bg-primary">
        <h4 class="card-title text-white">Buat SPPBJ </h4>
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
              <div class="form-group col-md-6 mb-2">
                  <label style="width: 100%">Pilih Nama Pemenang <sup>Penyedia</sup></label>
                  <input type="text" name="reqRekanan" class="easyui-combobox span4" data-options="valueField:'id',textField:'text',url:'contracting_json/getPemenang/<?= $reqId ?>/<?= $reqMultiPemenang ?>',onSelect: function(rec){
                                            getDataPemenang(rec.id);
                                            $('#formSPPBJ').show();
                                        }"  value="" style="width: 500% !important" />
                </div>
            </div>
            <div class="row">
              <div class="form-group col-md-6 mb-2" style="margin-bottom: 1px solid #b7b7b7">
                  <h3><b>Informasi Penyedia (pemenang)</b></h3>
                  <h2><span id="rekananNama"></span></h2>
                  <table style="width: 100%">
                    <tr> <td><i class="fa fa-id-card"></i> <span id="rekananNPWP"></span> <span class="badge badge-info">NPWP</span></td> </tr>
                    <tr> <td><i class="fa fa-phone"></i> Telepon: <span id="rekananTelepon"></span></td> </tr>
                    <tr> <td><i class="fa fa-envelope"></i> Email: <span id="rekananEmail"></span></td> </tr>
                    <tr> <td><i class="fa fa-map-marker"></i> <span id="rekananAlamat"></span></td> </tr>
                  </table>
              </div>
              <div class="form-group col-md-6 mb-2" style="margin-bottom: 1px solid #b7b7b7; text-align: right;">
                  <h3><b>Informasi Pengguna</b></h3>
                  <h2><?= $reqPenggunaStr.' ('.$reqJabatan.')' ?> </h2>
                  <table style="width: 100%">
                    <tr> <td><?= SYSTEM_NAME_PT ?></td> </tr>
                    <tr> <td><?= SYSTEM_ALAMAT_PT ?></td> </tr>
                  </table>
              </div>
            </div>
            <div id="formSPPBJ" style="display: none">
              <div class="row">
                <div class="form-group col-md-9 mb-2">
                  <label>Nomor SPPBJ</label>
                  <input type="text" name="reqKode" id="reqKode" class="form-control easyui-validatebox" value="<?=$reqKode?>" required/>
                </div>
                <div class="form-group col-md-3 mb-2">
                  <label style="width: 100%">Tanggal SPPBJ</label>
                  <input type="text" name="reqTanggal" id="reqTanggal" class="form-control easyui-datebox" value="<?=$reqTanggal?>" required style="width: 200%"/>
                </div>
              </div>
              <div class="row">
                <div class="form-group col-md-5 mb-2">
                  <label>Penanggung Jawab Kegiatan</label>
                  <input type="text" name="reqPejabatBerwenang" id="reqPejabatBerwenang" value="<?=$reqPenggunaStr?>" class="form-control easyui-validatebox" required/>
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
                  <input type="text" name="reqNamaDirut" id="reqNamaDirut" class="form-control easyui-validatebox" value="<?=$reqNamaDirut?>" required/>
                </div>
                <div class="form-group col-md-3 mb-2">
                  <label style="width: 100%">Kota <sup>Penyedia</sup></label>
                  <input type="text" name="reqKota" class="easyui-combobox span4" id="reqKota" data-options="valueField:'id',textField:'text',url:'region_json/combo'"  value="<?=$reqKota?>" style="width: 300% !important" />
                </div>
                <div class="form-group col-md-4 mb-2">
                  <label>Jabatan <sup>Penyedia</sup></label>
                  <input type="text" name="reqJabatanDirut" id="reqJabatanDirut" value="<?=$reqJabatanDirut?>" class="form-control easyui-validatebox" required/>
                </div>
              </div>
              <div class="row">
                <div class="form-group col-md-12 mb-2">
                  <label style="width: 100%">Alamat <sup>Penyedia</sup></label>
                  <textarea name="reqAlamatDirut" id="reqAlamatDirut" cols="45" rows="5" class="easyui-validatebox"  required  style="width: 100%"><?=$reqAlamatDirut?></textarea>
                </div>
              </div>

              <?php
              if ($bidding == '0' || $bidding == '') { // jika Sistem Negosiasi
                $paket_negosiasi->selectByParams(array("A.PAKET_PENAWARAN_ID" => $paket_rekanan->getField("PAKET_PENAWARAN_ID")));
                $paket_negosiasi->firstRow();
                $totalAkhirNego =  $paket_negosiasi->getField("TOTAL");
              } else { // jika Sistem Negosiasi nya Bidding
                $totalAkhirNego = $paket_rekanan->getField("NILAI_PENAWARAN");
              }
              ?>
              <div class="row">
                <div class="form-group col-md-4 mb-2">
                  <label>Nilai Kontrak  <small>(hasil pemilihan)</small></label>
                  <input title="Nilai Kontrak harus diisi" class="form-control easyui-validatebox span3"  name="reqNilai" type="text" id="reqNilai" value="<?=numberToIna($totalAkhirNego)?>"  OnFocus="FormatAngka('reqNilai')" OnKeyUp="FormatUang('reqNilai')" OnBlur="FormatUang('reqNilai')" required/>
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
              <div class="row" id="form-jaminan-pelaksanaan" style="display:none">
                <div class="form-group col-md-2 mb-2">
                  <label>Persen Jaminan % <small>(diisi angka)</small></label>
                  <input type="text" name="reqPersenJaminan" onkeypress="return isNumberKey(event)" id="reqPersenJaminan" class="form-control easyui-validatebox" maxlength="2" value="<?=$reqPersenJaminan?>" onchange="calculate(this)"/>
                </div>
                <div class="form-group col-md-5 mb-2">
                  <label>Nilai Jaminan <small>(diisi angka)</small></label>
                  <input title="Nilai Jaminan harus diisi" class="form-control easyui-validatebox span3"  name="reqNilaiJaminan" type="text" id="reqNilaiJaminan" value="<?=numberToIna($reqNilaiJaminan)?>"  OnFocus="FormatAngka('reqNilaiJaminan')" OnKeyUp="FormatUang('reqNilaiJaminan')" OnBlur="FormatUang('reqNilaiJaminan')"/>
                </div>
                <div class="form-group col-md-5 mb-2">
                  <label style="width: 100%">Jangka Jaminan Pelaksanaan</label>
                  <input type="text" name="reqJangkaDari" id="reqJangkaDari" class="form-control easyui-datebox" value="<?=$reqJangkaDari?>" style="width: 200%"/> <span style="margin:0 2%">s/d</span>
                  <input type="text" name="reqJangkaSampai" id="reqJangkaSampai" class="form-control easyui-datebox" value="<?=$reqJangkaSampai?>" style="width: 200%"/>
                </div>
              </div>

              <div class="form-actions">
                <input type="hidden" name="reqId" value="<?=$reqId?>">
                <!-- <input type="hidden" name="reqRekanan" value="<?=$rekanan_id?>"> -->
                <input type="hidden" name="reqContractingRekananProses1Id" value="">
                <input type="hidden" name="reqJnsKontrak" value="<?= $jnskontrak ?>">
                <input type="hidden" name="reqContractingRekananId" value="<?= $id ?>">
                <input type="hidden" name="reqJanganIntip2" value="<?= 'Jan'.md5('ikn-****') ?>">
                <input type="hidden" name="reqBahasa" value="ID">
                <a href="kontrak/index/contracting_persiapan_sppbj_multi?reqId=<?=$id?>" class="<?= CLASS_BTN_DANGER ?> mr-1"> <?= BTN_KEMBALI ?> </a>
                <button type="submit" class="<?= CLASS_BTN_PRIMARY ?>"><?= BTN_SIMPAN ?></button>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
