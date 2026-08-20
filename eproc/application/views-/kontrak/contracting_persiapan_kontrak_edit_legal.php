<?php
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 * 
 */

$this->libsession->cekSession();   

$this->load->library("kauth");  $userLogin = new kauth(); 

$this->LEGAL =  $this->kauth->getInstance()->getIdentity()->LEGAL;

if ($this->LEGAL == '0') { // Halaman khusus Legal
  redirect("main");
}

include_once("functions/string.func.php");
include_once("functions/default.func.php");
include_once("functions/date.func.php");
$this->load->model("Contracting");
$this->load->model("Paketpemenang");
$this->load->model("Region");
$this->load->model("Rekanan");
$this->load->model("Contractingrekanan");

$contracting = new Contracting();
$getpaket_pemenang = new Paketpemenang();
$region = new Region();
$rekanan = new Rekanan();

$reqId = $this->input->get("reqId"); // contractingrekananid

$getMenu = new Contracting();
// $kontrak = new Contracting();
$contractingrekanan = new Contractingrekanan();
$spkpks = new Contractingrekanan();
$sppbj = new Contractingrekanan();
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

$spkpks->selectViewPKSSPK(array("A.CONTRACTINGREKANANID" => $reqId)); 
$spkpks->firstRow();
$reqContractingRekananProses1Id = $spkpks->getField('CONTRACTINGREKANANPROSES1ID') ?: '-';  
$reqJenisKontrak = $contracting->getField('JENIS_KONTRAK') ?: '-'; 
$reqCode = $spkpks->getField('CR_CODE') ?: ''; 
$reqRekananProses1Id = $spkpks->getField('CONTRACTINGREKANANPROSES1ID') ?: '-'; 
$reqPaketId = $spkpks->getField('PAKET_ID') ?: '-'; 
$reqContractingRekananId = $spkpks->getField('CONTRACTINGREKANANID') ?: '-';  
$reqJenisPengadaan = $spkpks->getField('CR_JENIS_PENGADAAN') ?: '-';  
$reqJenisPengadaanStr = $spkpks->getField('CR_JENIS_PENGADAAN_STR') ?: '-';  
$reqJenisPekerjaan = $spkpks->getField('CR_JENIS_PEKERJAAN') ?: '';  
$reqJenisPekerjaanStr = $spkpks->getField('CR_JENIS_PEKERJAAN_STR') ?: '';  
$reqContractingjeniskontrakid = $spkpks->getField('CONTRACTINGJENISKONTRAKID') ?: '-';  
$reqJenisKontrakStr = $spkpks->getField('CR_JENIS_KONTRAK_STR') ?: '-';  
$reqWaktuPelaksanaanDari = dateToPageCheck($spkpks->getField('CR_WAKTU_PELAKSANAAN_DARI')) ?: '-';  
$reqWaktuPelaksanaanSampai = dateToPageCheck($spkpks->getField('CR_WAKTU_PELAKSANAAN_SAMPAI')) ?: '-';  
$reqLingkupPekerjaan = $spkpks->getField('CR_LINGKUP_PEKERJAAN') ?: '-';  
$reqNilaiKontrak = $spkpks->getField('CR_NILAI_KONTRAK') ?: $reqNilaiSPPBJ;  
$reqMetodePembayaran = $spkpks->getField('CR_METODE_PEMBAYARAN') ?: '-';  
$reqPihak1Nama = $spkpks->getField('CR_PIHAK1_NAMA') ?: '';  
$reqPihak1Jabatan = $spkpks->getField('CR_PIHAK1_JABATAN') ?: '';  
$reqPihak2Nama = $spkpks->getField('CR_PIHAK2_NAMA') ?: '';  
$reqPihak2Jabatan = $spkpks->getField('CR_PIHAK2_JABATAN') ?: '';  
$reqPihak2 = $spkpks->getField('CR_PIHAK2_PERUSAHAAN') ?: '';  
$reqCreatedBy = $spkpks->getField('CR_CREATED_BY') ?: '-';  
$reqContractingStatusKontrakId = $spkpks->getField('CONTRACTINGSTATUSKONTRAKID') ?: '-';   
$reqRakananId = $spkpks->getField('REKANAN_ID') ?: '-';   
 
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
$rekanan->selectByParams(array("REKANAN_ID" => $reqRakananId), -1, -1);
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
      url:'contracting_json/addLegal',
      onSubmit:function(){
        var v=$(this).form('validate');
        if(v) showLoad();  // show the message box
        return v;
      },
      success:function(data){
        // alert(data);return false;
        hideLoad();
        alertSuccess2(data);  
      }
    });
  });

});
</script> 
<div class="row"> 
  <div class="col-md-12 col-sm-12">
    <div class="card">
      <div class="card-header card-head-inverse bg-primary">
        <h4 class="card-title text-white">Edit <?= $reqJenisKontrak ?></h4>
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
              <div class="form-group col-md-8 mb-2">
                <label style="width: 100%">Nomor <?= $reqJenisKontrak ?></label>
                <input type="text" name="reqLegalNomorPKS" id="reqLegalNomorPKS" class="form-control easyui-validatebox" value="<?=$reqLegalNomorPKS?>" required/>
              </div>    
              <div class="form-group col-md-4 mb-2">
                <label style="width: 100%">Tanggal <?= $reqJenisKontrak ?></label>
                <input type="text" name="reqLegalTanggal" id="reqLegalTanggal" class="form-control easyui-datebox" value="<?=$reqLegalTanggal?>" required style="width: 200%"/>
              </div>  
            </div> 
            <div class="row"> 
              <div class="form-group col-md-8 mb-2">
                <label>Nomor Penyedia</label>
                <input type="text" name="reqLegalNomorRekanan" id="reqLegalNomorRekanan" class="form-control easyui-validatebox" value="<?=$reqLegalNomorRekanan?>" required/>
              </div> 
              <div class="form-group col-md-4 mb-2">
                <label style="width: 100%">Tanggal</label>
                <input type="text" name="reqLegalTanggalRekanan" id="reqLegalTanggalRekanan" class="form-control easyui-datebox" value="<?= $reqLegalTanggalRekanan ?>" required style="width: 200%"/>
              </div> 
            </div>
 
            <div class="form-actions">
              <input type="hidden" name="reqId" value="0">
              <input type="hidden" name="reqContractingRekananProses1Id" value="<?=$reqContractingRekananProses1Id?>">  
              <input type="hidden" name="reqContractingRekananId" value="<?=$reqContractingRekananId?>">  
              <input type="hidden" name="reqJanganIntip2" value="<?= 'Jan'.md5('ikn-****') ?>">
              <input type="hidden" name="reqBahasa" value="ID">
              <a href="kontrak/index/contracting_persiapan_kontrak?reqId=<?=$reqId?>" class="btn btn-danger mr-1 text-white"> <i class="fa fa-arrow-left"></i>  Kembali </a> 
              <button type="submit" class="btn btn-primary mr-1"><i class="fa fa-check-square-o"></i> Simpan</button> 
            </div> 
            
          </form>
        </div>
      </div>
    </div>
  </div> 
</div>        
