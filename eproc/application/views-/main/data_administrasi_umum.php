<?php
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 *
 */

$this->libsession->cekSession();

$this->load->model("UserLogin");
$user_login = new UserLogin();

if ($this->REKANAN_TIPE_ID == '7')
    redirect(base_url('main'));

$this->load->library("kauth");
$userLogin = new kauth();
$this->load->model(array("Rekanan","Bank"));
include_once("functions/string.func.php");
include_once("functions/date.func.php");
include_once("functions/default.func.php");
$rekanan = new Rekanan();

$rekanan->selectByParams(array("A.REKANAN_ID"=>$this->ID),-1,-1);
$rekanan->firstRow();

$tempKualifikasi = $rekanan->getField("REKANAN_KUALIFIKASI");
$tempMail = $rekanan->getField("EMAIL");
$tempFax = $rekanan->getField("FAX_KODE")."-".$rekanan->getField("FAX");
$tempTelepon = $rekanan->getField("TELEPON_KODE")."-".$rekanan->getField("TELEPON");
$tempKota = $rekanan->getField("KOTA");
$tempAlamat = $rekanan->getField("ALAMAT");
$tempKodepos = $rekanan->getField("KODEPOS");
if($rekanan->getField("STATUS_PERUSAHAAN") == 0)
{
    $tempStatus = "Pusat";
} else {
    $tempStatus = "Cabang";
}
$tempNPWP = $rekanan->getField("NPWP");
$tempNPWPFILE = $rekanan->getField("NPWP_FILE");
$tempNama= $rekanan->getField("NAMA");
$tempKontakPerson= $rekanan->getField("KONTAK_PERSON");
$tempKontakPersonHp= $rekanan->getField("KONTAK_PERSON_HP");
$tempWebsite= $rekanan->getField("WEBSITE");


$reqPKP = $rekanan->getField("PKP");
$reqMasaBerlakuPKP = $rekanan->getField("PKP_TANGGAL");
$reqLinkFileTemp = $rekanan->getField("PKP_TANGGAL");
$reqMasaBerlakuPKP = $rekanan->getField("PKP_TANGGAL");
$reqLinkFileTemp = $rekanan->getField("NAMA_FILE_PKP");

$reqStatusPKP = $rekanan->getField("STATUS_PKP");
$reqSKTPKP = $rekanan->getField("SKT_PKP_NOMOR");
$reqSKTPKPFileTemp = $rekanan->getField("SKT_PKP_FILE");
$reqNamaFileSKTPKP = $rekanan->getField("NAMA_SKT_PKP_FILE");
$tempCPFILE = $rekanan->getField("COMPANY_PROFILE_FILE");

?>

<div class="row">
    <div class="col-md-12 col-sm-12">
        <div class="card">
            <div class="card-header card-head-inverse bg-primary">
                <h4 class="card-title text-white">Profil Perusahaan
                    <?php
                    $arrStatusValidasi = array('0','10');
                    if (array_intersect($this->libsession->cekStatusValidasiRekanan(), $arrStatusValidasi)) {
                        $rekanan = new Rekanan();
                        $rekanan->selectByParams(array("A.REKANAN_ID"=>$this->ID),-1,-1);
                        $rekanan->firstRow();
                        $reqStatusValidasi= $rekanan->getField("STATUS_VALIDASI");

                        $userRekanan = new Userlogin();
                        $userRekanan->selectByParams(array("REKANAN_ID"=>$this->ID),-1,-1);
                        $userRekanan->firstRow();
                        $reqStatusUser= $userRekanan->getField("USER_STATUS");

                      if ($this->libsession->cekChecklist('npwp') || ($reqStatusUser == '0' && $reqStatusValidasi == '1')) // Check Checlist Verifikator
                      {
                        if ($this->libsession->cekUrl('data_administrasi_umum_ubah_profile')) { ?>
                          <div class="badge badge-pill badge-warning">
                              <a href="main/index/data_administrasi_umum_ubah_profile">
                                  <span class="fa fa-pencil text-white icon-line-height" data-toggle="tooltip" data-placement="top" title="edit"> Ubah</span>
                              </a>
                          </div>
                      <?php
                         }

                         if ($this->libsession->cekUrl('data_administrasi_umum_ubah_profile_email')) { ?>
                            <div class="badge badge-pill badge-warning">
                                <a href="main/index/data_administrasi_umum_ubah_profile_email">
                                    <span class="fa fa-pencil text-white icon-line-height" data-toggle="tooltip" data-placement="top" title="edit"> Ubah Email</span>
                                </a>
                            </div>
                        <?php
                         }
                       }
                    } else {
                        // echo "string";
                    } ?>
                </h4>
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
                    <?php
                    if (array_intersect($this->libsession->cekStatusValidasiRekanan(), $arrStatusValidasi)) { } else { echo $this->libsession->cekStatusValidasiRekananStr();  } ?>

                    <form id="ff" class="easyui-form form-horizontal" method="post" data-options="novalidate:true" enctype="multipart/form-data">

                        <div class="card mb-1 border-blue border-darken-1">
                            <div class="table-responsive">
                                <table class="table mb-0">
                                    <tbody>
                                        <tr>
                                            <td style="width: 20%">Nama Perusahaan</td>
                                            <td>: <?=$tempNama?></td>
                                        </tr>
                                        <tr>
                                            <td style="width: 20%">NPWP</td>
                                            <td>:
                                                <?=$tempNPWP?>
                                                <?php
                                                if ($tempNPWPFILE != '' && file_exists('uploads/rekanan/'.$tempNPWPFILE)) {
                                                // if ($tempNPWPFILE) {
                                                    ?>
                                                <a target="_blank" href="<?= base_url('uploads/rekanan/').$tempNPWPFILE ?>" class="badge badge-primary"><span class="fa fa-download"></span> Download file NPWP</a>
                                                <?php
                                                } else { echo '<span class="badge badge-danger">Belum upload</span>';} ?>
                                            </td>
                                        </tr>
                                        <!-- <tr>
                                            <td style="width: 20%">Status Kantor Perusahaan :</td>
                                            <td><?=$tempStatus?></td>
                                        </tr> -->
                                        <tr>
                                            <td style="width: 20%">Alamat</td>
                                            <td>: <?=$tempAlamat?></td>
                                        </tr> 
                                        <tr>
                                            <td style="width: 20%">Provinsi</td>
                                            <td>:
                                                <?=$rekanan->getField("NAMAPROPINSI")?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="width: 20%">Kabupaten/Kota</td>
                                            <td>:
                                                <?=$rekanan->getField("NAMAKABKOTA")?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="width: 20%">Kecamatan</td>
                                            <td>:
                                                <?=$rekanan->getField("NAMAKECAMATAN")?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="width: 20%">Kelurahan</td>
                                            <td>:
                                                <?=$rekanan->getField("KELURAHAN")?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="width: 20%">Kodepos</td>
                                            <td>: <?=$tempKodepos?></td>
                                        </tr>
                                        <tr>
                                            <td style="width: 20%">No. Telepon</td>
                                            <td>: <?=$tempTelepon?></td>
                                        </tr>
                                        <tr>
                                            <td style="width: 20%">Fax</td>
                                            <td>: <?=$tempFax?></td>
                                        </tr>
                                        <tr>
                                            <td style="width: 20%">Kontak Person</td>
                                            <td>: <?=$tempKontakPerson?></td>
                                        </tr>
                                        <tr>
                                            <td style="width: 20%">HP</td>
                                            <td>: <?=$tempKontakPersonHp?></td>
                                        </tr>
                                        <tr>
                                            <td style="width: 20%">e-Mail</td>
                                            <td>: <?=$tempMail?></td>
                                        </tr>
                                        <tr>
                                            <td style="width: 20%">Website</td>
                                            <td>: <?=$tempWebsite?></td>
                                        </tr>
                                        <tr>
                                            <td style="width: 20%">Kualifikasi Usaha</td>
                                            <td>: <?=$tempKualifikasi?></td>
                                        </tr>
                                        <tr>
                                            <td style="width: 20%">Company Profile</td>
                                            <td>:
                                                <?php
                                                if ($tempCPFILE != '' && file_exists('uploads/rekanan/'.$tempCPFILE)) {
                                                // if ($tempCPFILE) {
                                                    ?>
                                                <a target="_blank" href="<?= base_url('uploads/rekanan/').$tempCPFILE ?>" class="badge badge-primary"><span class="fa fa-download"></span> Download Company Profile</a>
                                                <?php
                                                } else { echo '<span class="badge badge-danger">Belum upload</span>';} ?>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>


                        <div class="card mb-1 border-blue border-darken-1">
                            <div class="card-content">
                                <div class="p-1">
                                    <div class="alert bg-primary alert-icon-left alert-arrow-left alert-dismissible mb-2" role="alert">
                                        <span class="alert-icon"><i class="fa fa-th"></i></span>
                                        <strong>Informasi Pembayaran</strong>

                                        <?php
                                        // if (array_intersect($this->libsession->cekStatusValidasiRekanan(), $arrStatusValidasi)) {
                                        //      if ($this->libsession->cekUrl('data_administrasi_umum_ubah_profile')) { 
                                        ?>
                                                <!-- <div class="badge badge-pill badge-primary">
                                                    <a onclick="openAdd('main/loadUrl/main/bank_rekanan')">
                                                        <span class="fa fa-plus text-white icon-line-height" data-toggle="tooltip" data-placement="top" title="tambah"> Tambah Informasi Pembayaran</span>
                                                    </a>
                                                </div> -->
                                            <?php
                                            //  }
                                            // } else {
                                            //     // echo "string";
                                            // }  
                                            ?>
                                    </div>
                                    <div class="table-responsive">
                                        <table class="border-double table mb-0">
                                            <tbody>
                                                <tr>
                                                    <td style="width: 20%">Bank</td>
                                                    <td>: <?=$rekanan->getField("BANK")?></td>
                                                </tr>
                                                <tr>
                                                    <td style="width: 20%">No. Rekening</td>
                                                    <td>: <?=$rekanan->getField("BANK_REKENING")?></td>
                                                </tr>
                                                <tr>
                                                    <td style="width: 20%">Atas Nama</td>
                                                    <td>: <?=$rekanan->getField("BANK_PEMILIK")?></td>
                                                </tr>
                                                <tr>
                                                    <td style="width: 20%">Cabang</td>
                                                    <td>: <?=$rekanan->getField("BANK_CABANG")?></td>
                                                </tr>
                                                <!-- <tr>
                                                    <td style="width: 20%">Cara Pembayaran</td>
                                                    <td>: <?=$rekanan->getField("PAYMENT_METHOD")?></td>
                                                </tr>
                                                <tr>
                                                    <td style="width: 20%">Mata Uang</td>
                                                    <td>: <?=$rekanan->getField("MATA_UANG_KODE")?></td>
                                                </tr> -->
                                            </tbody>
                                        </table> <br>
                                        <?php
                                        /* create objects */
                                        $bank = new Bank();
                                        $bank->selectByParamsRekanan(array("REKANAN_ID"=>$this->ID),-1,-1);
                                        if ($bank->countRow() > 0) {
                                        ?>
                                        <table class="table table-bordered table mb-0">
                                          <thead>
                                            <tr>
                                              <th>Bank</th>
                                              <th>No. Rekening</th>
                                              <th>Atas Nama</th>
                                              <th>Cabang</th>
                                            </tr>
                                          </thead>
                                          <tbody id="tbodyDeliverable">
                                        <?php
                                          while($bank->nextRow()) {
                                          ?>
                                          <tr>
                                             <td><?= $bank->getField('BANK_NAMA') ?></td>
                                             <td><?= $bank->getField('BANK_REKENING') ?></td>
                                             <td><?= $bank->getField('BANK_PEMILIK') ?></td>
                                             <td><?= $bank->getField('BANK_CABANG') ?></td>
                                          </tr>
                                        <?php
                                            } ?>
                                          </tbody>
                                        </table>
                                        <?php
                                        } ?>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- <div class="card mb-1 border-blue border-darken-1">
                            <div class="card-content">
                                <div class="p-1">
                                    <div class="alert bg-primary alert-icon-left alert-arrow-left alert-dismissible mb-2" role="alert">
                                        <span class="alert-icon"><i class="fa fa-th"></i></span>
                                        <strong>Incoterm</strong>
                                    </div>
                                    <div class="table-responsive">
                                        <table class="table mb-0">
                                            <tbody>
                                                <tr>
                                                    <td style="width: 20%">Incoterm I :</td>
                                                    <td>
                                                        <?=$rekanan->getField("INCOTERM1")?>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td style="width: 20%">Incoterm II :</td>
                                                    <td>
                                                        <?=$rekanan->getField("INCOTERM2")?>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>

                                    </div>
                                </div>
                            </div>
                        </div>   -->

                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
