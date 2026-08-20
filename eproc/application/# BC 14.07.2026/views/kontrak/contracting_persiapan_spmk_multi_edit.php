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
$reqRekananId = $this->input->get("reqRekananId"); // rekananid
// echo $reqRekananId; die;
$paket = new Paket();
$contracting = new Contractingrekanan();
$region = new Region();
$rekanan = new Rekanan();
$spmk = new Contractingrekanan();

$contracting->selectByParams(array("A.CONTRACTINGREKANANID" => $reqId));
$contracting->firstRow();
$reqNamaPaket = $contracting->getField("NAMA");
$reqPanitiaStr = $contracting->getField("PANITIA_STR");
$reqPenggunaStr = $contracting->getField("PENGGUNA_STR");
$reqPpkStr = $contracting->getField("PPK_STR");
$reqPemenangStr = $contracting->getField("PEMENANG_NAMA");

// get data contracting_rekanan_proses1
$spmk->selectSPMK(array("A.CONTRACTINGREKANANID" => $reqId, "A.REKANAN_ID" => $reqRekananId));
$spmk->firstRow();

$reqContractingRekananProses1SpmkId = $spmk->getField('CONTRACTINGREKANANPROSES1SPMKID') ?: ''; 
$reqNomor = $spmk->getField('NOMOR') ?: ''; 
$reqSPMKDari = dateToPageCheck($spmk->getField('SPMK_DARI')) ?: '';
$reqSPMKSampai = dateToPageCheck($spmk->getField('SPMK_SAMPAI')) ?: '';
$reqKeterangan = $spmk->getField('KETERANGAN') ?: ''; 

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
      url:'contracting_json/addSpmk',
      onSubmit:function(){
        var v=$(this).form('validate');
        if(v) showLoad();  // show the message box
        return v;
      },
      success:function(data){
        if (data === 'Data proses gagal diubah.') {
          alertError3(data);
          hideLoad();
        } else {
          alertSuccess2(data);
          setTimeout(function () {
            document.location.href = 'kontrak/index/contracting_persiapan_spmk_multi?reqId=<?=$reqId?>&reqProses=1';
          }, 1000);
        }
      }
    });
  });

  $('#reqSPMKDari, #reqSPMKSampai').datebox({
    editable: false
  });

});

</script>
<div class="row">
  <div class="col-md-12 col-sm-12">
    <div class="card">
      <div class="card-header card-head-inverse bg-primary">
        <h4 class="card-title text-white"> Surat Perinta Mulai Kerja (SPMK) </h4>
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
              <div class="form-group col-md-7 mb-2">
                <label>Nomor SPMK</label>
                <input type="text" name="reqNomor" id="reqNomor" class="form-control easyui-validatebox" value="<?=$reqNomor?>" required/>
              </div>
              <div class="form-group col-md-5 mb-2">
                <label style="width: 100%">Masa Pelaksanaan Pekerjaan</label>
                <input type="text" name="reqSPMKDari" id="reqSPMKDari" class="form-control easyui-datebox" value="<?=$reqSPMKDari?>" required style="width: 200%"/> <span style="margin:0 2%">s/d</span>
                <input type="text" name="reqSPMKSampai" id="reqSPMKSampai" class="form-control easyui-datebox" value="<?=$reqSPMKSampai?>" required style="width: 200%"/>
              </div>
            </div>  
            <div class="row"> 
              <div class="form-group col-md-12 mb-2">
                <label style="width: 100%">Keterangan</label>
                <textarea id="reqKeterangan" name="reqKeterangan" class="textarea-tinymce" style="width:100%; height:350px"><?=isset($reqKeterangan)?$reqKeterangan:''?></textarea>
              </div>
            </div> 

            <div class="form-actions">
              <input type="hidden" name="reqId" value="0">
              <input type="hidden" name="reqContractingRekananProses1SpmkId" value="<?=$reqContractingRekananProses1SpmkId?>">
              <input type="hidden" name="reqContractingRekananId" value="<?=$reqId?>">
              <input type="hidden" name="reqRekananId" value="<?=$reqRekananId?>">
              <input type="hidden" name="reqJanganIntip2" value="<?= 'Jan'.md5('ikn-****') ?>">
              <input type="hidden" name="reqBahasa" value="ID">
              <a href="kontrak/index/contracting_persiapan_spmk_multi?reqId=<?=$reqId?>&reqProses=1" class="<?= CLASS_BTN_DANGER ?> mr-1"> <?= BTN_KEMBALI ?> </a>
              <button type="submit" class="<?= CLASS_BTN_PRIMARY ?>"><?= BTN_SIMPAN ?></button>
            </div>

          </form>
        </div>
      </div>
    </div>
  </div>
</div>
