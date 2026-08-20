<?php
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 * 
 */

$this->load->model(array("Dashpaket","Queryfree","PaketTahap"));

include_once("functions/string.func.php");
include_once("functions/date.func.php");
include_once("functions/default.func.php");
/* create objects */
$dataNya = new Dashpaket();

$bulan = $this->input->get("bulan") ?: '';
$tahun = $this->input->get("tahun");
$unitkerjaid = $this->input->get("uki");
$user_login_id = $this->input->get("uid");
$jenis = $this->input->get("jenis");
$rekananid = $this->input->get("rekananid");



if ($tahun != 'all'){
  switch ($jenis) {
    case 'perencanaan': 
      $dataNya->selectPermohonan(array("A.APPROVAL" => "1", "A.TAHUN_ANGGARAN" => $tahun),-1,-1,"");
      break;
    case 'persiapan': 
      $dataNya->selectPermohonan(array("A.TAHUN_ANGGARAN" => $tahun),-1,-1," AND KODE_PR IS NOT NULL");
      break;
    case 'pemilihanDivisi': 
      $dataNya->selectPemilihan(array("A.TAHUN_ANGGARAN" => $tahun,"A.PEMBUAT" => $this->USER_LOGIN_ID),-1,-1,""); 
      break;
    case 'pemilihan': 
      $dataNya->selectPemilihan(array("A.TAHUN_ANGGARAN" => $tahun),-1,-1,""); 
      break;
    case 'kontrak': 
      $dataNya->selectKontrakProses(array("A.TAHUN_ANGGARAN" => $tahun),-1,-1,"");
      break;
    case 'selesai': 
      $dataNya->selectKontrakSelesai(array("A.TAHUN_ANGGARAN" => $tahun),-1,-1,"");
      break;
    case 'terkontrak': 
      $dataNya->selectTerKontrak(array("A.TAHUN_ANGGARAN" => $tahun, "A.REKANAN_ID" => $rekananid),-1,-1,"");
      break;
    default: 
      break;
  }
} else {
  switch ($jenis) {
    case 'perencanaan': 
      $dataNya->selectPermohonan(array("A.APPROVAL" => "1"),-1,-1,"");
      break;
    case 'persiapan': 
      $dataNya->selectPermohonan(array(),-1,-1," AND KODE_PR IS NOT NULL");
      break;
    case 'pemilihanDivisi': 
      $dataNya->selectPemilihan(array("A.PEMBUAT" => $this->USER_LOGIN_ID),-1,-1,""); 
      break;
    case 'pemilihan': 
      $dataNya->selectPemilihan(array(),-1,-1,"");
      break;
    case 'kontrak': 
      $dataNya->selectKontrakProses(array(),-1,-1,"");
      break;
    case 'selesai': 
      $dataNya->selectKontrakSelesai(array(),-1,-1,"");
      break;
    case 'terkontrak': 
      $dataNya->selectTerKontrak(array("A.REKANAN_ID" => $rekananid),-1,-1,"");
      break;
    default: 
      break;
  }
}

// echo $dataNya->query;
 
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <base href="<?=base_url();?>" />

    <link rel="icon" href="../../favicon.ico">

    <title><?= SYSTEM_NAME.' | '.SYSTEM_NAME_PT ?></title>
    <script src="<?=base_url()?>assets/new/vendors/js/jquery.min.3.6.0.js"></script>
    <!-- BEGIN VENDOR CSS-->
    <link rel="stylesheet" type="text/css" href="<?=base_url() ?>assets/new/css/vendors.css">
    <link rel="stylesheet" type="text/css" href="<?=base_url() ?>assets/new/vendors/css/ui/prism.min.css">
    <link rel="stylesheet" type="text/css" href="<?=base_url() ?>assets/new/vendors/css/ui/prism.min.css">
    <link rel="stylesheet" type="text/css" href="<?=base_url() ?>assets/new/vendors/css/extensions/toastr.css">
    <!-- END VENDOR CSS-->
    <!-- BEGIN ROBUST CSS-->
    <link rel="stylesheet" type="text/css" href="<?=base_url() ?>assets/new/css/plugins/animate/animate.css">
    <link rel="stylesheet" type="text/css" href="<?=base_url() ?>assets/new/css/app.css">
    <!-- END ROBUST CSS-->
    <!-- BEGIN Page Level CSS-->
    <link rel="stylesheet" type="text/css" href="<?=base_url() ?>assets/new/css/core/menu/menu-types/horizontal-menu.css">
    <link rel="stylesheet" type="text/css" href="<?=base_url() ?>assets/new/css/core/colors/palette-gradient.css">
    <link rel="stylesheet" type="text/css" href="<?=base_url() ?>assets/new/css/core/colors/palette-callout.css">
    <!-- END Page Level CSS-->
    <!-- BEGIN Custom CSS-->
    <link rel="stylesheet" type="text/css" href="<?=base_url() ?>assets/new/css/style.css">
    <link rel="stylesheet" type="text/css" href="lib/DataTables-1.10.7/media/css/jquery.dataTables.css">
    <link rel="stylesheet" type="text/css" href="lib/DataTables-1.10.7/extensions/Responsive/css/dataTables.responsive.css">
    <link rel="stylesheet" type="text/css" href="lib/DataTables-1.10.7/examples/resources/syntax/shCore.css">
    <link rel="stylesheet" type="text/css" href="lib/DataTables-1.10.7/examples/resources/demo.css">
    <script type="text/javascript" language="javascript" src="lib/DataTables-1.10.7/media/js/jquery.dataTables.js"></script>
    <script type="text/javascript" language="javascript" src="lib/DataTables-1.10.7/extensions/Responsive/js/dataTables.responsive.js"></script>
    <script type="text/javascript" language="javascript" src="lib/DataTables-1.10.7/examples/resources/syntax/shCore.js"></script>
    <script type="text/javascript" language="javascript" src="lib/DataTables-1.10.7/examples/resources/demo.js"></script>
    <script type="text/javascript" language="javascript" class="init">
      $(document).ready(function() {
        $('#prosesDash').DataTable({
          "iDisplayLength": 10,
          // "aaSorting": [[0, 'desc']],
          "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
        });
      });
    </script>
  <style>
  #prosesDash_length { display: none;}
  </style>
  </head>

<body style="background: #fff">

<?php 
switch ($jenis) {
  case 'perencanaan': 
    $lableHeader = 'perencanaan';
    $lableHeaderTable = 'Harga Perkiraan';
    break;
  case 'persiapan': 
    $lableHeader = 'persiapan';
    $lableHeaderTable = 'Harga Perkiraan';
    break;
  case 'pemilihan': 
  case 'pemilihanDivisi':
    $lableHeader = 'Pemilihan';
    $lableHeaderTable = 'Harga Perkiraan';
    break;
  case 'kontrak': 
    $lableHeader = 'Kontrak';
    $lableHeaderTable = 'Nilai Kontrak';
    break;
  case 'selesai': 
    $lableHeader = 'Serah Terima';
    $lableHeaderTable = 'Nilai Kontrak';
    break;
  case 'terkontrak': 
    $lableHeader = 'Terkontrak';
    $lableHeaderTable = 'Nilai Kontrak';
    break;
  default: 
    break;
} ?>
   <div class="card mb-1 border-darken-1">
    <div class="card-content">
      <div class="p-1">
        <div class="alert bg-primary alert-icon-left alert-arrow-left alert-dismissible mb-2" role="alert">
          <span class="alert-icon"><i class="fa fa-th"></i></span>  <strong> <?=$lableHeader?>  </strong>  <small><?= $tahun?></small>
        </div>
        <div class="p-1"> 

        <?php 
        if ($jenis == 'pemilihan') 
        { ?>
          <table id="prosesDash" class="border-double table mb-0 table-bordered" style="width: 100%">
            <thead>
              <tr>
                <th style="width:2%">No</th>
                <th style="width:30% !important">Nama Paket</th>
                <th>Tahap</th>
                <th>Metode Pengadaan</th>
                <th>Divisi</th>
                <th style="width:15%"><?= $lableHeaderTable; ?></th>
                <th>Harga Final</th>
                <th>Penyedia</th>
              </tr>
            </thead>
            <tbody>
              <?php 
              $no=1;
              while($dataNya->nextRow()) 
              {  
                $hargaPerkiraan = currencyToPage($dataNya->getField('NILAI'));  
                if(trim($dataNya->getField("ALASAN")) == "") { $batal = 0; } else { $batal = 1; }
                  if(trim($dataNya->getField("ALASAN_ULANG")) == "") { $batal_ulang = 0; } else { $batal_ulang = 1; }
                ?>
                <tr>
                  <td align="center"><?= $no ?>..</td>
                  <td> 
                    <?= $dataNya->getField('NAMA') ?>
                    <?php if($batal == 1) { ?>
                     <div class="col-md-12 mt-1 mb-0" style="text-align:left; background-color:#da4453; color:#fff; font-weight: 400; font-size:85%; padding:.35em .4em !important; border-radius: .21rem !important">
                      <i class="fa fa-remove"></i> Paket Dibatalkan
                      <?php
                        echo '<br>Alasan: '.$dataNya->getField('ALASAN'); ?>
                     </div>
                    <?php }

                    if($batal_ulang == 1) {
                    ?>
                     <div class="col-md-12 mt-1 mb-0" style="text-align:left; background-color:#da4453; color:#fff; font-weight: 400; font-size:85%; padding:.35em .4em !important; border-radius: .21rem !important">
                      <i class="fa fa-refresh"></i> Paket Gagal
                      <?php
                        echo '<br>Alasan: '.$dataNya->getField('ALASAN_ULANG');
                      ?>
                     </div>
                    <?php } ?>
                  </td>  
                  <td>
                    <?php  
                    $tahap .= '<ul>';
                    $tahap = '';
                    if ($dataNya->getField('PPK') != '') {
                      $tahap .= '<span class="fa fa-check-square-o"></span> Selesai';
                    } else 
                    {
                      $paket_tahap_tender = new PaketTahap();
                      $paket_tahap_tender->selectByJawdalAktif(array("A.PAKET_ID" => $dataNya->getField("PAKET_ID")), -1, -1);
                      while($paket_tahap_tender->nextRow())
                      {
                        $tahap .= '<li>'.$paket_tahap_tender->getField("NAMA").'</li>'; 
                      } 
                    }
                      $tahap .= '</ul>';
                    echo $tahap;
                    ?>
                  </td>  
                  <td> <?= $dataNya->getField('METODE') ?></td>  
                  <td> <?= $dataNya->getField('DEPARTMENT') ?></td> 
                  <td> <?= $hargaPerkiraan ?></td>
                  <?php 
                    $dataFinalNya = new Dashpaket();
                    $dataFinalNya->selectHargaFinalByPaketId(array("PAKET_ID" => $dataNya->getField("PAKET_ID")),-1,-1,"");
                    $dataFinalNya->firstRow();
                   ?>
                  <td> <?= currencyToPage($dataFinalNya->getField('CR_NILAI_KONTRAK')) ?></td>
                  <td> <?= $dataFinalNya->getField('NAMA') ?></td>
                </tr>
              <?php 
              $no++;
              } ?>
            </tbody>
          </table>
        <?php 
        } else { ?>
          <table id="prosesDash" class="border-double table mb-0 table-bordered" style="width: 100%">
            <thead>
              <tr>
                <th style="width:2%">No</th>
                <th>Nama Paket</th>
                <th>Divisi</th>
                <th style="width:15%"><?= $lableHeaderTable; ?></th>
              </tr>
            </thead>
            <tbody>
              <?php 
              $no=1;
              while($dataNya->nextRow()) 
              { 
                switch ($jenis) {
                  case 'perencanaan': 
                  case 'persiapan': 
                    $hargaPerkiraan = currencyToPage($dataNya->getField('PERKIRAAN_BIAYA_HARGA'));
                    break;
                  case 'pemilihan': 
                  case 'pemilihanDivisi':
                    $hargaPerkiraan = currencyToPage($dataNya->getField('NILAI')); 
                    break;
                  case 'kontrak': 
                  case 'selesai': 
                    $hargaPerkiraan = currencyToPage($dataNya->getField('CR_NILAI_KONTRAK')); 
                    break;
                  case 'terkontrak': 
                    $hargaPerkiraan = currencyToPage($dataNya->getField('CR_NILAI_KONTRAK')); 
                    break;
                  default: 
                    break;
                }
                ?>
                <tr>
                  <td align="center"><?= $no ?></td>
                  <td> 
                    <?= $dataNya->getField('NAMA') ?>

                    <?php if ($jenis == 'kontrak') { ?>
                    <br><span class="badge badge-primary"> <?= ucwords('proses: '.strtolower($dataNya->getField('CP_NAME'))) ?></span>
                    <?php 
                    } ?>
                    
                  </td> 
                  <td> <?= $dataNya->getField('DEPARTMENT') ?></td> 
                  <td> <?= $hargaPerkiraan ?></td>
                </tr>
              <?php 
              $no++;
              } ?>
            </tbody>
          </table>
        <?php 
        } ?>
          
        </div>
      </div>
    </div>
  </div>  
</body>
</html>
