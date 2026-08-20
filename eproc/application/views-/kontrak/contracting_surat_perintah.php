<?php
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 * 
 */

$this->libsession->cekSession();

$this->LEGAL =  $this->kauth->getInstance()->getIdentity()->LEGAL;

if ($this->LEGAL == '1') { // Bagian legal keluar, inputan khusus pengelola kontrak
  redirect(base_url().'main/index/404');
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

$paketInfo->getPaket($reqId);
$bidding = $paketInfo->bidding;
$reqJenisPengadaan = $paketInfo->jenis_id;

$contracting->selectByParams(array("A.PAKET_ID" => $reqId));
$contracting->firstRow();
$reqNamaPaket = $contracting->getField("NAMA");
$reqNilai = $contracting->getField("NILAI");
$reqPanitiaStr = $contracting->getField("PANITIA_STR");
$reqPenggunaStr = $contracting->getField("PENGGUNA_STR");
$reqPpkStr = $contracting->getField("PPK_STR");
$reqPemenangStr = $contracting->getField("PEMENANG_NAMA");

// get data pemenang
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

// get data paket rekanan (penawaran)
$paket_rekanan->selectByParams3(array("A.PAKET_ID" => $reqId, "A.REKANAN_ID" => $rekanan_id), -1, -1);
$paket_rekanan->firstRow();

?>

<script type="text/javascript">
$(document).ready(function() {

  $(function(){
    $('#ff').form({
      url:'contracting_json/addSPK',
      onSubmit:function(){
        var v=$(this).form('validate');
        if(v) showLoad();  // show the message box
        return v;
      },
      success:function(data){
        hideLoad();
        if (data == 'Data gagal simpan.') {
          alertError2(data);
        } else {
          alertSuccess2(data);
        }
        setTimeout(function() {
          if (data == 'Data gagal simpan.') {
          } else {
            document.location.href = 'kontrak/index/contracting_persiapan?tahun=<?=$tahun?>';
          }
        }, 2000);
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
</script>
<style type="text/css">
  /*.fa { width: 2%; text-align: center; }*/
</style>
<div class="row">
  <div class="col-md-12 col-sm-12">
    <div class="card">
      <div class="card-header card-head-inverse bg-primary">
        <h4 class="card-title text-white">Surat Perintah Kerja (SPK) </h4>
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
              <div class="form-group col-md-5 mb-2">
                <label>Nomor Kontrak</label>
                <input type="text" name="reqKode" id="reqKode" class="form-control easyui-validatebox" value="<?=$reqKode?>" required/>
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
              <div class="form-group col-md-4 mb-2">
                <label>Nilai Pekerjaan <small>(hasil pemilihan)</small></label>
                <input title="Nilai Pekerjaan" class="form-control easyui-validatebox span3"  name="reqNilaiKontrak" type="text" id="reqNilaiKontrak" value="<?=numberToIna($totalAkhirNego)?>"  OnFocus="FormatAngka('reqNilaiKontrak')" OnKeyUp="FormatUang('reqNilaiKontrak')" OnBlur="FormatUang('reqNilaiKontrak')" required />
              </div>
              <div class="form-group col-md-3 mb-2">
                <label>Metode Pembayaran</label><br>
                  <input type="radio" name="reqMetodePembayaran" title="" value="1" id="reqMetodePembayaran1"  <?php if($reqMetodePembayaran == '1' || $reqMetodePembayaran == '') { ?> checked="checked" <?php } ?> /> Sekaligus<br>
                  <input type="radio" name="reqMetodePembayaran" title="" value="2" id="reqMetodePembayaran2"  <?php if($reqMetodePembayaran == '2') { ?> checked="checked" <?php } ?> /> Tempo
              </div>
            </div>
            <div class="row">
              <div class="form-group col-md-2 mb-2">
                <label style="width: 100%">Jenis Pengadaan</label>
                <input type="text" name="reqJenisPengadaan" class="easyui-combobox span4" data-options="valueField:'id',textField:'text',url:'contracting_json/comboJenisPengadaan'"  value="<?=$reqJenisPengadaan?>" style="width: 200% !important" required/>
              </div>
              <div class="form-group col-md-3 mb-2">
                <label style="width: 100%">Jenis Kontrak</label>
                <input type="text" name="reqContractingjeniskontrakid" class="easyui-combobox span4" data-options="valueField:'id',textField:'text',url:'contracting_json/comboJenisKontrak'"  value="<?=$reqContractingjeniskontrakid?>" style="width: 300% !important" required/>
              </div>
              <div class="form-group col-md-4 mb-2">
                <label style="width: 100%">Jangka Waktu Pekerjaan</label>
                <input type="text" name="reqPelaksanaanDari" id="reqPelaksanaanDari" class="form-control easyui-datebox" value="<?=$reqWaktuPelaksanaanDari?>" required style="width: 175%"/> <span style="margin:0 2%">s/d</span>
                <input type="text" name="reqPelaksanaanSampai" id="reqPelaksanaanSampai" class="form-control easyui-datebox" value="<?=$reqWaktuPelaksanaanSampai?>" required style="width: 175%"/>
              </div>
              <div class="form-group col-md-3 mb-2">
                <label>Jenis Pekerjaan</label><br>
                  <input type="radio" name="reqJenisPekerjaan" title="" value="1" id="reqJenisPekerjaan1"  <?php if($reqJenisPekerjaan == '1' || $reqJenisPekerjaan == '') { ?> checked="checked" <?php } ?> /> TI <br>
                  <input type="radio" name="reqJenisPekerjaan" title="" value="2" id="reqJenisPekerjaan2"  <?php if($reqJenisPekerjaan == '2') { ?> checked="checked" <?php } ?> /> Hasil Pekerjaan (umum)
              </div>
            </div>
            <div class="row">
              <!-- <div class="form-group col-md-12" style="margin-bottom: 0px !important;"><h3>PIHAK I</h3><hr></div> -->
              <div class="form-group col-md-6 mb-2">
                <label>Nama (Pihak I)</label>
                <input type="text" name="reqPihak1Nama" id="reqPihak1Nama" class="form-control easyui-validatebox" value="<?=$reqPihak1Nama?>" required/>
              </div>
              <div class="form-group col-md-6 mb-2">
                <label>Jabatan (Pihak I)</label>
                <input type="text" name="reqPihak1Jabatan" id="reqPihak1Jabatan" class="form-control easyui-validatebox" value="<?=$reqPihak1Jabatan?>" required/>
              </div>
            </div>
            <div class="row">
              <div class="form-group col-md-6 mb-2">
                <label>Nama (Pihak II)</label>
                <input type="text" name="reqPihak2Nama" id="reqPihak2Nama" class="form-control easyui-validatebox" value="<?=$reqPihak2Nama?>" required/>
              </div>
              <div class="form-group col-md-6 mb-2">
                <label>Jabatan (Pihak II)</label>
                <input type="text" name="reqPihak2Jabatan" id="reqPihak2Jabatan" class="form-control easyui-validatebox" value="<?=$reqPihak2Jabatan?>" required/>
              </div>
            </div>
            <div class="row">
              <div class="form-group col-md-12 mb-2">
                <label style="width: 100%">Lingkup Pekerjaan</label>
                <textarea name="reqLingkupPekerjaan" id="reqLingkupPekerjaan" cols="45" rows="10" class="textarea-tinymce"  required  style="width: 100%"><?=$reqLingkupPekerjaan?></textarea>
              </div>
            </div>

            <div class="form-actions">
              <input type="hidden" name="reqId" value="<?=$reqId?>">
              <input type="hidden" name="reqRekanan" value="<?=$rekanan_id?>">
              <input type="hidden" name="reqJnsKontrak" value="0">
              <input type="hidden" name="reqJanganIntip2" value="<?= 'Jan'.md5('ikn-****') ?>">
              <input type="hidden" name="reqBahasa" value="ID">
              <a href="kontrak/index/contracting_paket?tahun=<?=$tahun?>" class="btn btn-danger mr-1 text-white"> <i class="fa fa-arrow-left"></i> Kembali </a>
              <button type="submit" class="btn btn-primary mr-1"><i class="fa fa-check-square-o"></i> Simpan</button>
            </div>

          </form>
        </div>
      </div>
    </div>
  </div>
</div>
