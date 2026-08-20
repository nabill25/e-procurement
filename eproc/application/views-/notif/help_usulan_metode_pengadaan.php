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
    <link rel="stylesheet" type="text/css" href="<?=base_url() ?>assets/new/css/app.css">
  </head>

<body class="body-popup">

  <div class="card mb-1 border-darken-1">
    <div class="card-content" style="padding:0 9%">
      <div class="col-md-12">
          <h2 style="margin-top: 2%; text-align: center">METODE PEMILIHAN PENYEDIA</h2>
             <table class="table table-bordered">
               <tr style="background-color: #fbecc9;">
                 <th class="text-center">Nilai Pengadaan</th>
                 <th class="text-center">Metode Pemilihan</th>
                 <th class="text-center">Kriteria Barang/Jasa</th>
                 <th class="text-center">Pelaksana Pemilihan</th>
               </tr>
               <tr>
                 <td rowspan="2" class="text-center">s.d <br> Rp. 50.000.000,-</td>
                 <td class="text-center">Pembelian Langsung</td>
                 <td class="text-center">Barang/Jasa yang terdekat di toko atau penyedia jasa</td>
                 <td class="text-center" rowspan="5">Pejabat Pengadaan</td>
               </tr>
               <tr>
                 <td class="text-center">e-Purchasing</td>
                 <td class="text-center">Barang/Jasa yang terdekat di e-katalog atau toko daring</td>
               </tr>
               <tr>
                 <td class="text-center" rowspan="2">diatas <br> Rp. 50.000.000,- <br> s.d <br> Rp.1.000.000.000,-</td>
                 <td class="text-center">Pengadaan Langsung</td>
                 <td class="text-center">Barang/Jasa hasil survey pasar terdapat > 1 (satu) Penyedia</td>
               </tr>
               <tr>
                 <td class="text-center">Penunjukan Langsung</td>
                 <td class="text-center">Barang/Jasa Khusus atau Keadaan Tertentu</td>
               </tr>
               <tr>
                 <td class="text-center" rowspan="2">diatas <br> Rp.1.000.000.000,-</td>
                 <td class="text-center">Penunjukan Langsung</td>
                 <td class="text-center">Barang/Jasa Khusus atau Keadaan Tertentu</td>
               </tr>
               <tr>
                 <td class="text-center">Tender</td>
                 <td class="text-center">Barang/Jasa yang bersifat kompleks atau tidak kompleks</td>
                 <td class="text-center">Panitia Pengadaan</td>
               </tr>
               <tr>
                 <td class="text-center">diatas <br> Rp.50.000.000,-</td>
                 <td class="text-center">Kompetisi</td>
                 <td class="text-center">Barang/Jasa Praktik Bisnis Mapan dalam hal dapat disediakan oleh minimal 2 (dua) Penyedia</td>
                 <td class="text-center">Pejabat Pengadaan</td>
               </tr>
             </table><br><br>
          </p>
        </div>
      </div>
    </div>
  </div>  
  </body>
</html>
