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
$this->load->model("Rekanan");
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

?>

<div class="row">
    <div class="col-md-12 col-sm-12">
        <div class="card">
            <div class="card-header card-head-inverse bg-primary">
                <h4 class="card-title text-white">Profil Perusahaan
                    <?php
                    $arrStatusValidasi = array('0','10');
                    if (array_intersect($this->libsession->cekStatusValidasiRekanan(), $arrStatusValidasi)) {
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
                                        <tr>
                                            <td>Status PKP</td> 
                                            <td>: <?php if($reqStatusPKP == '1') { echo "PKP"; } else { echo "Non PKP"; } ?></td>
                                        </tr>
                                        <?php
                                        if($reqStatusPKP == '0')
                                        { ?>
                                             <tr>
                                                <td>File Non PKP</td>
                                                <td>: 
                                                <?php
                                                    $arrFile = explode(";", $rekanan->getField("NAMA_NON_PKP_FILE"));
                                                    for($iFile=0;$iFile<count($arrFile);$iFile++)
                                                    {
                                                ?>
                                                        <?php if (file_exists('uploads/rekanan/'.$rekanan->getField("NON_PKP_FILE")) && $rekanan->getField("NON_PKP_FILE") != '' ) {
                                                        echo $arrFile[$iFile]; ?>
                                                        <a href="<?= base_url('uploads/rekanan').'/'.$rekanan->getField("NON_PKP_FILE") ?>" class="badge badge-primary" target="_blank"><span class="fa fa-download"></span> Download file PKP</a>
                                                        <?php } else { echo '<span class="badge badge-danger">Belum upload</span>';} ?>
                                                <?php
                                                    }
                                                ?>
                                                </td>
                                            </tr>
                                        <?php
                                        }
                                        else
                                        {
                                        ?>
                                            <tr>
                                                <td>Nomor. PKP</td>
                                                <td>: <?=$reqPKP?></td>
                                            </tr>
                                            <tr>
                                                <td><?=translate("Tanggal", "Date")?></td>
                                                <td>: <?=getFormattedDateJson($reqMasaBerlakuPKP)?></td>
                                            </tr>
                                            <tr>
                                                <td>File PKP</td>
                                                <td>: 
                                                <?php
                                                    $arrFile = explode(";", $rekanan->getField("NAMA_FILE_PKP"));
                                                    for($iFile=0;$iFile<count($arrFile);$iFile++)
                                                    {
                                                ?>
                                                        <?php if (file_exists('uploads/rekanan/'.$rekanan->getField("PKP_FILE")) && $rekanan->getField("PKP_FILE") != '' ) {
                                                        echo $arrFile[$iFile]; ?>
                                                        <a href="<?= base_url('uploads/rekanan').'/'.$rekanan->getField("PKP_FILE") ?>" class="badge badge-primary" target="_blank"><span class="fa fa-download"></span> Download file PKP</a>
                                                        <?php } else { echo '<span class="badge badge-danger">Belum upload</span>';} ?>
                                                <?php
                                                    }
                                                ?>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>Nomor. SKT PKP</td> 
                                                <td>: <?= $reqSKTPKP ?></td>
                                            </tr>
                                            <tr>
                                                <td>File SKT PKP</td>
                                                <td>: 
                                                  <?php if (file_exists('uploads/rekanan/'.$rekanan->getField("SKT_PKP_FILE")) && $rekanan->getField("SKT_PKP_FILE") != '' ) { ?>
                                                  <?= $rekanan->getField("NAMA_SKT_PKP_FILE") ?>
                                                  <a href="<?= base_url('uploads/rekanan').'/'.$reqSKTPKPFileTemp ?>" class="badge badge-primary" target="_blank"><span class="fa fa-download"></span> Download file SKT PKP</a>
                                                  <?php } else { echo '<span class="badge badge-danger">Belum upload</span>';} ?>
                                                </td>
                                            </tr>
                                        <?php
                                        }
                                        ?>
                                        <!-- <tr>
                                            <td style="width: 20%">Status Kantor Perusahaan :</td>
                                            <td><?=$tempStatus?></td>
                                        </tr> -->
                                        <tr>
                                            <td style="width: 20%">Alamat</td>
                                            <td>: <?=$tempAlamat?></td>
                                        </tr>
                                        <tr>
                                            <td style="width: 20%">Kota</td>
                                            <td>: <?=$tempKota?></td>
                                        </tr>
                                        <tr>
                                            <td style="width: 20%">Provinsi</td>
                                            <td>: 
                                                <?=$rekanan->getField("REGION")?>
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
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <?php if ($rekanan->getField("STATUS_PERUSAHAAN") == 0) { ?>
                        <div class="card mb-1 border-blue border-darken-1">
                            <div class="card-content">
                                <div class="p-1">
                                    <div class="alert bg-primary alert-icon-left alert-arrow-left alert-dismissible mb-2" role="alert">
                                        <span class="alert-icon"><i class="fa fa-th"></i></span>
                                        <strong>Informasi Perusahaan</strong>
                                    </div>
                                    <div class="table-responsive">
                                        <table class="border-double table mb-0">
                                            <tbody>
                                                <tr>
                                                    <td style="width: 20%">No. Telepon</td>
                                                    <td>: 
                                                        ( <?=$rekanan->getField("TELEPON_KODE_PUSAT")?> ) <?=$rekanan->getField("TELEPON_PUSAT")?>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td style="width: 20%">Fax</td>
                                                    <td>: 
                                                        ( <?=$rekanan->getField("FAX_KODE_PUSAT")?> ) <?=$rekanan->getField("FAX_PUSAT")?>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td style="width: 20%">Email</td>
                                                    <td>: 
                                                        <?=$rekanan->getField("EMAIL_PUSAT")?>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td style="width: 20%">Alamat</td>
                                                    <td>: 
                                                        <?=$rekanan->getField("ALAMAT_PUSAT")?>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php } ?>

                        <div class="card mb-1 border-blue border-darken-1">
                            <div class="card-content">
                                <div class="p-1">
                                    <div class="alert bg-primary alert-icon-left alert-arrow-left alert-dismissible mb-2" role="alert">
                                        <span class="alert-icon"><i class="fa fa-th"></i></span>
                                        <strong>Informasi Pembayaran</strong>
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
                                        </table>
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
