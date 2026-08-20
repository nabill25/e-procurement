<?php
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 * 
 */

$this->load->library("kauth");  $userLogin = new kauth(); 

$this->LEGAL =  $this->kauth->getInstance()->getIdentity()->LEGAL;

include_once("functions/string.func.php");
include_once("functions/date.func.php");
include_once("functions/default.func.php");

$reqId = httpFilterRequest("reqId");
$reqProses = httpFilterRequest("reqProses");
$getTahun = $this->session->userdata('setTahunKontrak');


$this->load->model("Contracting");
$this->load->model("Contractingrekanan");

$contracting = new Contracting();
$spkpks = new Contractingrekanan();
$legal = new Contractingrekanan();
$proses5 = new Contractingrekanan();
$textMonitoring = new Contractingrekanan();

$contracting->selectByParams(array("A.CONTRACTINGREKANANID" => $reqId)); 
$contracting->firstRow();

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
$reqJenisPekerjaan = $spkpks->getField('CR_JENIS_PEKERJAAN') ?: '-';  
$reqJenisPekerjaanStr = $spkpks->getField('CR_JENIS_PEKERJAAN_STR') ?: '-';  
$reqContractingjeniskontrakid = $spkpks->getField('CONTRACTINGJENISKONTRAKID') ?: '-';  
$reqJenisKontrakStr = $spkpks->getField('CR_JENIS_KONTRAK_STR') ?: '-';  
$reqWaktuPelaksanaanDari = $spkpks->getField('CR_WAKTU_PELAKSANAAN_DARI') ?: '-';  
$reqWaktuPelaksanaanSampai = $spkpks->getField('CR_WAKTU_PELAKSANAAN_SAMPAI') ?: '-';  
$reqLingkupPekerjaan = $spkpks->getField('CR_LINGKUP_PEKERJAAN') ?: '-';  
$reqNilaiKontrak = $spkpks->getField('CR_NILAI_KONTRAK') ?: '-';  
$reqMetodePembayaran = $spkpks->getField('CR_METODE_PEMBAYARAN') ?: '-';  
$reqPihak1Nama = $spkpks->getField('CR_PIHAK1_NAMA') ?: '-';  
$reqPihak1Jabatan = $spkpks->getField('CR_PIHAK1_JABATAN') ?: '-';  
$reqPihak2Nama = $spkpks->getField('CR_PIHAK2_NAMA') ?: '-';  
$reqPihak2Jabatan = $spkpks->getField('CR_PIHAK2_JABATAN') ?: '-';  
$reqPihak2 = $spkpks->getField('CR_PIHAK2_PERUSAHAAN') ?: '-';  
$reqCreatedBy = $spkpks->getField('CR_CREATED_BY') ?: '-';  
$reqContractingStatusKontrakId = $spkpks->getField('CONTRACTINGSTATUSKONTRAKID') ?: '-';   
$reqRakananId = $spkpks->getField('REKANAN_ID') ?: '-';   

$legal->selectViewLegal(array("A.CONTRACTINGREKANANID" => $reqId)); 
$legal->firstRow();
$reqLegalNomorPKS = $legal->getField('CR_LEGAL_NOMOR_PKS') ?: '-';   
$reqLegalTanggal = $legal->getField('CR_LEGAL_TANGGAL') ?: '-';   
$reqLegalNomorRekanan = $legal->getField('CR_LEGAL_NOMOR_REKANAN') ?: '-';   
$reqLegalTanggalRekanan = $legal->getField('CR_LEGAL_TANGGAL_REKANAN') ?: '-';   
$reqLegalCreatedBy = $legal->getField('CR_LEGAL_CREATED_BY') ?: '-';   
$reqLegalCreatedDate = $legal->getField('CR_LEGAL_CREATED_DATE') ?: '-';   
$reqLegalUpdatedBy = $legal->getField('CR_LEGAL_UPDATED_BY') ?: '-';   
$reqLegalUpdatedDate = $legal->getField('CR_LEGAL_UPDATED_DATE') ?: '-';   

$proses5->selectProses5(array("A.CONTRACTINGREKANANID" => $reqId)); 
$proses5->firstRow();

if ($proses5->countRow() > 0) {
  $reqSubmit = 'update';
} else {
  $reqSubmit = 'simpan';
} 

$reqContractingRekananProses5Id = $proses5->getField('CONTRACTINGREKANANPROSES5ID') ?: '';   
$reqBastMasaNomor = $proses5->getField('CR_BAST_MASA_NOMOR') ?: '';   
$reqBastMasaTanggal = $proses5->getField('CR_BAST_MASA_TANGGAL') ?: '';   
$reqBastMasaNamaPenyedia = $proses5->getField('CR_BAST_MASA_NAMA_PENYEDIA') ?: $reqPihak2Nama;   
$reqBastMasaJabatanPenyedia = $proses5->getField('CR_BAST_MASA_JABATAN_PENYEDIA') ?: $reqPihak2Jabatan;   
$reqBastMasaNamaPenerima = $proses5->getField('CR_BAST_MASA_NAMA_PENERIMA') ?: $reqPihak1Nama;   
$reqBastMasaJabatanPenerima = $proses5->getField('CR_BAST_MASA_JABATAN_PENERIMA') ?: $reqPihak1Jabatan;   
$reqBastMasaStatus = $proses5->getField('CR_BAST_MASA_STATUS') ?: '-';   
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <base href="<?=base_url();?>" />
    
    <link rel="icon" href="../../favicon.ico">

    <title><?= SYSTEM_NAME ?></title>

    <!-- Bootstrap core CSS -->
    <link href="lib/bootstrap/dist/css/bootstrap.css" rel="stylesheet">

    <link rel="stylesheet" href="css/core.css" type="text/css">
    <link href='http://fonts.googleapis.com/css?family=Roboto:400,300,700' rel='stylesheet' type='text/css'> 
    <!-- BEGIN VENDOR CSS-->
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
    <!-- <link rel="stylesheet" type="text/css" href="lib/drupal/index.css" />     -->
    <script type="text/javascript"> 
    $(function(){
      $('#ff').form({
        url:'contracting_json/addBASTPemeliharaan',
        onSubmit:function(){
          return $(this).form('validate');
        },
        success:function(data){
          window.top.location.reload();
        }
      });

      $('#reqBastMasaTanggal').datebox({
        editable: false
      });
    });
  </script>
  </head>

<body style="background: #fff">
 
 <div class="card mb-1 border-darken-1">
  <div class="card-content">
    <div class="p-1">
      <div class="alert bg-primary alert-icon-left alert-arrow-left alert-dismissible mb-2" role="alert">
        <span class="alert-icon"><i class="fa fa-th"></i></span>  <strong>Berita Acara Serah Terima (BAST) Masa Pemeliharaan </strong>
      </div> 
      <div class="p-1">
        <form id="ff" class="form-horizontal" role="form" method="post" novalidate >
          <div class="row">
            <div class="form-group col-md-8 mb-2">
              <label>Nomor BAST</label>
              <input type="text" name="reqBastMasaNomor" id="reqBastMasaNomor" class="form-control easyui-validatebox" value="<?=$reqBastMasaNomor?>" required/>
            </div> 
            <div class="form-group col-md-3 mb-2" style="margin-left:1%">
              <label style="width: 100%">Tanggal BAST</label>
              <input type="text" name="reqBastMasaTanggal" id="reqBastMasaTanggal" class="form-control easyui-datebox" value="<?=$reqBastMasaTanggal?>" required style="width: 200%"/>
            </div> 
            <div class="form-group col-md-6 mb-2">
              <label>Nama Penyedia</label>
              <input type="text" name="reqBastMasaNamaPenyedia" id="reqBastMasaNamaPenyedia" class="form-control easyui-validatebox" value="<?=$reqBastMasaNamaPenyedia?>" required/>
            </div> 
            <div class="form-group col-md-6 mb-2" style="margin-left:1%">
              <label>Jabatan Penyedia</label>
              <input type="text" name="reqBastMasaJabatanPenyedia" id="reqBastMasaJabatanPenyedia" class="form-control easyui-validatebox" value="<?=$reqBastMasaJabatanPenyedia?>" required/>
            </div> 
            <div class="form-group col-md-6 mb-2">
              <label>Nama Penerima</label>
              <input type="text" name="reqBastMasaNamaPenerima" id="reqBastMasaNamaPenerima" class="form-control easyui-validatebox" value="<?=$reqBastMasaNamaPenerima?>" required/>
            </div> 
            <div class="form-group col-md-6 mb-2" style="margin-left:1%">
              <label>Jabatan Penerima</label>
              <input type="text" name="reqBastMasaJabatanPenerima" id="reqBastMasaJabatanPenerima" class="form-control easyui-validatebox" value="<?=$reqBastMasaJabatanPenerima?>" required/>
            </div> 
            <div class="form-group col-md-2 mb-2">
              <label>Status</label>
              <select class="form-control" name="reqBastMasaStatus">
               <option <?php if ($reqBastMasaStatus == '0') { echo "selected"; } ?> value="0">Proses</option>
               <option <?php if ($reqBastMasaStatus == '1') { echo "selected"; } ?> value="1">Selesai</option>
              </select>
            </div>
            <div class="form-actions">
              <input type="hidden" name="reqId" value="0">
              <input type="hidden" name="reqContractingRekananProses1Id" value="<?=$reqContractingRekananProses1Id?>">  
              <input type="hidden" name="reqContractingRekananProses5Id" value="<?=$reqContractingRekananProses5Id?>">  
              <input type="hidden" name="reqContractingRekananId" value="<?=$reqContractingRekananId?>">  
              <input type="hidden" name="reqPaketId" value="<?=$reqPaketId?>">  
              <input type="hidden" name="reqSubmit" value="<?=$reqSubmit?>">  
              <input type="hidden" name="reqJanganIntip2" value="<?= 'Jan'.md5('ikn-****') ?>">
              <input type="hidden" name="reqBahasa" value="ID">
            </div> 
          </div> 
          <div class="form-actions">
            <button type="submit" class="<?= CLASS_BTN_PRIMARY ?>"><?= BTN_SIMPAN ?></button>
          </div>  
        </form>
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