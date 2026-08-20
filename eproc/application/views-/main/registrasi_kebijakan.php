<?php
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 * 
 */
 ?>
 <!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <base href="<?=base_url();?>" />
    
    <link rel="icon" href="../../favicon.ico">

    <title><?= SYSTEM_NAME_PT ?></title>

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
    
   <script type="text/javascript" src="js/jquery-1.6.1.min.js"></script>
    
    <!-- PAGINATION -->
    <link rel="stylesheet" type="text/css" href="lib/drupal/index.css" /> 
    <script src="lib/emodal/eModal.js"></script>
   
  </head>

<body class="body-popup">

  <div class="card mb-1 border-darken-1">
    <div class="card-content">
      <div class="col-md-12">
          <h2 style="margin-top: 2%; text-align: center">VENDOR POLICY</h2>

          <ol>
            <li>Vendor Management digunakan dalam upaya mendukung penerapan Good Corporate Governance (GCG).</li>
            <li>Meningkatkan transparansi, persaingan usaha yang sehat dan kompetitif dengan melakukan pengelolaan vendor management.</li>
            <li>Dalam melaksanakan vendor management maka perlu dibuat sebuah pedoman sebagai acuan di lingkungan <?= SYSTEM_NAME_PT ?></li>
            <li>Seluruh vendor dan Divisi di lingkungan <?= SYSTEM_NAME_PT ?> yang terlibat dalam kegiatan vendor management wajib mengikuti pedoman yang berlaku.</li>
            <li>Perusahaan yang berafiliasi wajib mencantumkan dan menginformasikan kepada <?= SYSTEM_NAME_PT ?>.</li>
            <li>Masa Berlaku Sanksi Daftar Hitam:</li>
              <ol type="a">
                <li>Sanksi Daftar Hitam berlaku sejak tanggal Surat Keputusan ditetapkan dan tidak berlaku surut (nonretroaktif)</li>
                <li>Vendor yang terkena Sanksi Daftar Hitam dapat menyelesaikan pekerjaan lain, jika kontrak pekerjaan tersebut ditandatangani sebelum pengenaan sanksi.</li>
                <li>Vendor yang dikenakan Sanksi Daftar Hitam berlaku selama 2 (dua) tahun</li>
              </ol>
          </ol>

          <h2 style="margin-top: 2%; text-align: center">DISCLAIMER</h2>

          <ol>
            <li>Surat Permohonan Rekanan adalah surat permohonan yang dibuat oleh calon vendor pada saat melaksanakan registrasi vendor</li>
            <li>Surat Keterangan terdaftar/ Surat Pemberitahuan <?= SYSTEM_NAME_PT ?> adalah keterangan dalam format sertifikat atau surat yang berisi penjelasan bahwa Perusahaan yang tercantum di dalamnya telah terdaftar sebagai Rekanan <?= SYSTEM_NAME_PT ?>.</li>
            <li>Hak Perusahaan yang sudah menjadi Rekanan <?= SYSTEM_NAME_PT ?> adalah mendapatkan kesempatan untuk mengikuti pengadaan barang dan jasa di <?= SYSTEM_NAME_PT ?>, dengan syarat memenuhi kualifikasi dan persyaratan yang ditetapkan oleh <?= SYSTEM_NAME_PT ?>.</li>
            <li>Kewajiban Perusahaan yang sudah menjadi Rekanan <?= SYSTEM_NAME_PT ?></li>
              <ol type="a">
                <li>Memberikan data-data/dokumen perusahaan yang sah dan masih berlaku. Apabila terjadi perubahan data/dokumen, perusahaan wajib menginformasikannya kepada <?= SYSTEM_NAME_PT ?></li>
                <li>Mematuhi ketentuan Pengadaan Barang/Jasa di <?= SYSTEM_NAME_PT ?> serta menjunjung prinsip Good Corporate Governance  (GCG).</li>
                <li>Tidak masuk dalam daftar hitam antara lain Daftar Hitam dari Bank Indonesia, Bank atau Instansi/lembaga lain yang berwenang;</li>
                <li>Tidak masuk dalam daftar kredit macet dari Bank Indonesia, Bank atau Instansi/ lembaga lain yang berwenang;</li>
                <li>Tidak dalam pengawasan pengadilan, tidak pailit, kegiatan usahanya tidak sedang dihentikan, dan atau Direksi yang bertindak untuk dan atas nama perusahaan tidak sedang menjalani sanksi pidana yang dibuktikan dengan surat pernyataan yang ditandatangani oleh Direktur Perusahaan.</li>
              </ol>
            <li><?= SYSTEM_NAME_PT ?> tidak berkewajiban memberikan pekerjaan kepada perusahaan yang tercatat sebagai rekanan</li>
            <li>Pemutakhiran/pembaharuan Data vendor merupakan daftar vendor yang sudah tidak melakukan perubahan data / passive selama 1 (satu) tahun. <?= SYSTEM_NAME_PT ?> dapat meminta vendor tersebut untuk melakukan proses pemuktakhiran terhadap data vendor.  </li>
            <li>Perusahaan dapat dikeluarkan dari daftar Rekanan Terdaftar di <?= SYSTEM_NAME_PT ?> apabila terdapat data vendor yang tidak melakukan perubahan data selama 1 (satu) tahun. <?= SYSTEM_NAME_PT ?> akan meminta masing-masing vendor  untuk melakukan pemutakhiran data. Apabila vendor tersebut tidak akan direkomendasikan pada proses pengadaan barang/jasa.</li>
            <li>Apabila <?= SYSTEM_NAME_PT ?> menerima laporan atau aduan dari pihak luar perihal hal yang tidak baik mengenai perusahaan rekanan, maka <?= SYSTEM_NAME_PT ?> akan melakukan konfirmasi dan klarifikasi kepada rekanan dimaksud, dan apabila terbukti benar maka <?= SYSTEM_NAME_PT ?> akan mencabut Surat Terdaftar Rekanan <?= SYSTEM_NAME_PT ?> atas nama perusahaan tersebut.</li>
          </ol>

        </div>
      </div>
    </div>
  </div> 

    <script src="lib/bootstrap/libs/jquery/1.12.4/jquery.min.js"></script>
    <script>window.jQuery || document.write('<script src="lib/bootstrap/assets/js/vendor/jquery.min.js"><\/script>')</script>
    <script src="lib/bootstrap/dist/js/bootstrap.min.js"></script><link rel="stylesheet" type="text/css" href="<?=base_url()?>lib/eproc/themes/default/easyui.css">
    <script type="text/javascript" src="<?=base_url()?>lib/eproc/jquery.easyui.min.js"></script>
    <script type="text/javascript" src="<?=base_url()?>lib/eproc/kalender-easyui.js"></script>
    <script type="text/javascript" src="<?=base_url()?>lib/eproc/allfunc.js"></script>
  </body>
</html>
