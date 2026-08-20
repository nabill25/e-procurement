<?php
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 * 
 */

$this->load->model("Users");
$user_login = new Users();
$reqId  = $this->input->get("reqId");

$user_login->selectByParams(array("USER_LOGIN_ID"=>$reqId),-1,-1);
$user_login->firstRow();
// echo $user_login->query;exit;
$tmpNamaUser = $user_login->getField("USER_LOGIN");
$tmpNama = $user_login->getField("USER_NAMA");
$tmpAlamat = $user_login->getField("USER_ALAMAT");
$tmpTipe = $user_login->getField("USER_TYPE_ID");
$tmpTipeStr = $user_login->getField("USER_TYPE");
$tmpJabatan = $user_login->getField("USER_JABATAN");
$tmpTelepon = $user_login->getField("USER_TELEPON");
$tmpUnitKerja = $user_login->getField("UNIT_KERJA_ID");
$tmpUnitKerjaStr = $user_login->getField("UNIT_KERJA");
$tmpNip = $user_login->getField("NIP");
$tmpPanitiaPL = $user_login->getField("CHILD_PL");
$tmpPPK = $user_login->getField("PPK");
$tmpVPPengadaan = $user_login->getField("VP_PENGADAAN");
$tmpAdminRup = $user_login->getField("ADMIN_RUP");
$tmpUserJabatanPanitia = $user_login->getField("USER_JABATAN_PANITIA"); 
$tmpPenunjukPIC = $user_login->getField("PENUNJUK_PIC"); 
$tmpTender = $user_login->getField("TENDER"); 
$tmpLegal = $user_login->getField("LEGAL"); 
$tmpdirektorat = $user_login->getField("DIREKTORAT_ID"); 
$tmpdirektoratStr = $user_login->getField("DIREKTORAT_STR"); 
$tmpDepartment = $user_login->getField("DEPARTMENT"); 

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

    <link rel="stylesheet" type="text/css" href="<?=base_url() ?>assets/new/css/vendors.css">
    <link rel="stylesheet" type="text/css" href="<?=base_url() ?>assets/new/vendors/css/ui/prism.min.css">
    <link rel="stylesheet" type="text/css" href="<?=base_url() ?>assets/new/css/app.css">
    <link rel="stylesheet" type="text/css" href="<?=base_url() ?>assets/new/css/style.css">
 
  </head>

<!-- <body class="body-popup"> -->
<body>
 
          <div class="row"> 
            <div class="col-md-12">
              <table class="table table-bordered">
                <tbody>
                  <tr>
                    <td width="25%">Username:</td><td><?= $tmpNamaUser ?></td>
                  </tr>
                  <tr>
                    <td>Nama:</td><td><?= $tmpNama ?></td>
                  </tr>
                  <tr>
                    <td>NPP:</td><td><?= $tmpNip ?></td>
                  </tr>
                  <tr>
                    <td>Jabatan:</td><td><?= $tmpJabatan ?></td>
                  </tr>
                  <tr>
                    <td>Telepon:</td><td><?= $tmpTelepon ?></td>
                  </tr>
                  <tr>
                    <td>Perusahaan:</td><td><?= $tmpUnitKerjaStr ?></td>
                  </tr>
                  <tr>
                    <td>Direktorat:</td><td><?= $tmpdirektoratStr ?></td>
                  </tr>
                  <tr>
                    <td>Divisi:</td><td><?= $tmpDepartment ?></td>
                  </tr>
                  <tr>
                    <td>Tipe User:</td><td><?= $tmpTipeStr ?></td>
                  </tr>
                  <tr>
                    <td>Alamat:</td><td><?= $tmpAlamat ?></td>
                  </tr> 
                </tbody>
              </table>
            </div>
          </div> 

  </body>
</html>
