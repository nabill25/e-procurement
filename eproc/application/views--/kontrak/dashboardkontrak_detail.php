<?php
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 *
 */

include_once("functions/string.func.php");
include_once("functions/date.func.php");
include_once("functions/default.func.php");

//kauth
if (!$this->kauth->getInstance()->hasIdentity())
{
}

$this->load->model(array("Contracting","Contractingrekanan","PaketRekanan","PaketPenawaran","RekananPaketPenawaran","DashPermohonanPaket"));
$contracting = new Contracting();

$this->USER_LOGIN_ID =  $this->kauth->getInstance()->getIdentity()->USER_LOGIN_ID;
$this->USER_LOGIN =  $this->kauth->getInstance()->getIdentity()->USER_LOGIN;
$this->LEGAL =  $this->kauth->getInstance()->getIdentity()->LEGAL;

$getTahun = $this->input->get("tahun");
// echo $getTahun.'--'.$this->LEGAL; die();
// $getTahun = $this->session->userdata('setTahunKontrak');

$statement = '';
if ($getTahun == 'all') {
  if ($this->LEGAL == '1') { // PENG. KONTRAK BAGIAN LEGAL
    $statement .= " AND A.SELESAI = '1' AND A.PAKET_METODE_LELANG_ID IN (1,2,3,4,5,7,8,10,11) AND A.STATUS_KONTRAK = 'Belum dibuat' ";
  } else {
    if ($this->USER_TYPE_ID == '20') { // PEMERIKSAN
    $statement .= " AND A.SELESAI = '1' AND A.PAKET_METODE_LELANG_ID IN (1,2,3,4,5,7,8,10,11) AND A.STATUS_KONTRAK = 'Belum dibuat' AND PPK IS NOT NULL";
    } else {
      $statement .= " AND A.SELESAI = '1' AND A.PAKET_METODE_LELANG_ID IN (1,2,3,4,5,7,8,10,11) AND A.STATUS_KONTRAK = 'Belum dibuat' AND A.PPK = '".$this->USER_LOGIN_ID."' ";
    }
  }
} else {
  if ($this->LEGAL == '1') { // PENG. KONTRAK BAGIAN LEGAL
    $statement .= " AND A.SELESAI = '1' AND A.PAKET_METODE_LELANG_ID IN (1,2,3,4,5,7,8,10,11) AND A.STATUS_KONTRAK = 'Belum dibuat' AND A.TAHUN = '".$getTahun."'";
  } else {
    if ($this->USER_TYPE_ID == '20') { // PEMERIKSAN
    $statement .= " AND A.SELESAI = '1' AND A.PAKET_METODE_LELANG_ID IN (1,2,3,4,5,7,8,10,11) AND A.STATUS_KONTRAK = 'Belum dibuat' AND A.TAHUN = '".$getTahun."' AND PPK IS NOT NULL";
    } else {
      $statement .= " AND A.SELESAI = '1' AND A.PAKET_METODE_LELANG_ID IN (1,2,3,4,5,7,8,10,11) AND A.STATUS_KONTRAK = 'Belum dibuat' AND A.TAHUN = '".$getTahun."' AND A.PPK = '".$this->USER_LOGIN_ID."' ";
    }
  }
}

$contracting->selectByParamsViewContracting(array(),-1,-1,$statement);

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
    <style type="text/css">
      ul.menu-icons li {list-style-type:none;}
      ul { padding-left: 2px; }
    </style>
  </head>

<body style="background: #fff">

 <div class="card mb-1 border-darken-1">
  <div class="card-content">
    <div class="p-1">
      <div class="alert bg-primary alert-icon-left alert-arrow-left alert-dismissible mb-2" role="alert">
        <span class="alert-icon"><i class="fa fa-th"></i></span>  <strong> Paket Pengadaan </strong>  <small>Selesai Pemilihan</small>
      </div>
      <div class="p-1">
        <table id="prosesDash" class="border-double table mb-0 table-bordered">
          <thead>
            <tr>
              <th>Paket Pengadaan</th>
              <!-- <th>Jenis Kontrak</th> -->
              <th width="200px">Pemenang</th>
              <th>Harga Penawaran</th>
              <th>Harga Final/Akhir</th>
            </tr>
          </thead>
          <tbody>
          <?php
          while($contracting->nextRow())
          {
            $pemenangStr = '<span class="badge badge-danger">Belum Ditetapkan</span>';
            if ($contracting->getField('PEMENANG') != '') {
              $pemenangStr = '<span class="badge badge-primary">Sudah Ditetapkan</span>';
            }

            // Nomor SPPBJ
            $contracting_rekanan = new Contractingrekanan();
            $contracting_rekanan->selectViewSPPBJ(array("PAKET_ID" => $contracting->getField('PAKET_ID')));
            $noSPPBJ = '';
            while ($contracting_rekanan->nextRow()) {
              $noSPPBJ .= $contracting_rekanan->getField('CR_SPPBJ_CODE').'<br>';
            }

            // Harga Penawaran
            $hargaPenawaran = '';
            $hargaFinal = '';
            $pemenang = explode(",",str_replace(array("{","}"),"",$contracting->getField('PEMENANG')));
            foreach ($pemenang as $key => $value) {
              $paket_rekanan = new PaketRekanan();
              $paket_rekanan->selectByParams(array("A.PAKET_ID" => $contracting->getField('PAKET_ID'), "A.REKANAN_ID" => $value));
              $paket_rekanan->firstRow();
              $paketRekananId = $paket_rekanan->getField('PAKET_REKANAN_ID');

              $paket_penawaran = new PaketPenawaran();
              $paket_penawaran->selectByParams(array("A.PAKET_ID" => $contracting->getField('PAKET_ID')));
              $paket_penawaran->firstRow();
              $paketPenawaranId = $paket_penawaran->getField('PAKET_PENAWARAN_ID') ?: 0;

              $rekanan_paket_penawaran = new RekananPaketPenawaran();
              $rekanan_paket_penawaran->selectByParams(array("PAKET_PENAWARAN_ID" => $paketPenawaranId, "PAKET_REKANAN_ID" => $paketRekananId));
              $rekanan_paket_penawaran->firstRow();
              $hargaPenawaran .= number_format((float)$rekanan_paket_penawaran->getField('UNIT_PRICE'), 2, ',', '.').'<br>';

              // Harga Final/Akhir
              $dataNya = new DashPermohonanPaket();
              $dataNya->selectByParams(array("A.PAKET_ID" => $contracting->getField('PAKET_ID'), "A.PEMENANG" => $value),-1,-1);
              $dataNya->firstRow();
              $hargaFinal .= number_format((float)$dataNya->getField('HARGA_NEGOSIASI'), 2, ',', '.').'<br>';
            }


            echo '
                  <tr>
                   <td>'.$contracting->getField('NAMA').' <small>'.$noSPPBJ.'</small><br>
                      <small class="badge badge-info" style="font-size:9px; padding:3px 10px">'.$contracting->getField('PAKET_METODE_LELANG').'</small>
                      <small class="badge badge-primary" style="font-size:9px; padding:3px 10px">'.$contracting->getField('PENGGUNA_STR').'</small>
                   </td>
                   <!-- <td>'.$contracting->getField('JENIS_KONTRAK').'</td> -->
                   <td>'.$pemenangStr.'</td>
                   <td>'.$hargaPenawaran.'</td>
                   <td>'.$hargaFinal.'</td>
                  </tr>
                 ';
          } ?>
          </tbody>
        </table>

      </div>
    </div>
  </div>
</div>
  </body>
</html>
