<?php
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 *
 */

$this->libsession->cekSession();

if ($this->REKANAN_TIPE_ID != '7')
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

$this->load->model("UserLogin");
$user_login = new UserLogin();
$adaKelengkapanData = $user_login->getCountByParams(array("USER_LOGIN_ID"=> $this->USER_LOGIN_ID, "USER_STATUS|| IN " => "(0,2)"));

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

$reqNamaPerusahaan = $rekanan->getField("NAMA");
$reqAlamat = $rekanan->getField("ALAMAT");
$reqKota = $rekanan->getField("KOTA");
$reqProvinsi = $rekanan->getField("REGION");
$reqKodePos = $rekanan->getField("KODEPOS");
$reqStatus = $rekanan->getField("STATUS_CP");
$reqNPWP = $rekanan->getField("NPWP");
$reqLinkFileNPWPTemp = $rekanan->getField("NAMA_FILE_NPWP");
$reqPKP = $rekanan->getField("PKP");
$reqKTP = $rekanan->getField("KTP");
$reqKTPFile = $rekanan->getField("KTP_FILE");
$reqNamaFileKTP = $rekanan->getField("NAMA_FILE_KTP");
$reqMasaBerlakuPKP = $rekanan->getField("PKP_TANGGAL");
$reqLinkFileTemp = $rekanan->getField("PKP_TANGGAL");
$reqMasaBerlakuPKP = $rekanan->getField("PKP_TANGGAL");
$reqLinkFileTemp = $rekanan->getField("NAMA_FILE_PKP");
$reqNomorTelepon = $rekanan->getField("TELEPON_FULL");
$reqNomorFax = $rekanan->getField("FAX_FULL");
$reqEmail = $rekanan->getField("EMAIL");
$reqKualifikasi = $rekanan->getField("REKANAN_KUALIFIKASI");
$reqKontakPerson = $rekanan->getField("KONTAK_PERSON");
$reqKontakPersonHp = $rekanan->getField("KONTAK_PERSON_HP");
$reqWebsite = $rekanan->getField("WEBSITE");
?>

<div class="row">
    <div class="col-md-12 col-sm-12">
        <div class="card">
            <div class="card-header card-head-inverse bg-primary">
                <h4 class="card-title text-white">Profil Perorangan
                    <?php
                    $arrStatusValidasi = array('0','10');
                    if (array_intersect($this->libsession->cekStatusValidasiRekanan(), $arrStatusValidasi)) {
                      if ($this->libsession->cekChecklist('npwp')) // Check Checlist Verifikator
                      {?>
                    <div class="badge badge-glow badge-pill badge-warning">
                        <a href="main/index/data_administrasi_umum_ubah_profile_perorangan">
                            <span class="fa fa-pencil text-white font-medium-2 icon-line-height" data-toggle="tooltip" data-placement="top" title="edit"></span>
                        </a>
                    </div>
                    <?php
                      }
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
                    if (array_intersect($this->libsession->cekStatusValidasiRekanan(), $arrStatusValidasi)) { } else { echo NOTIF_PENYEDIA_TERVERIFIKASI;  } ?>
                    <form id="ff" class="easyui-form form-horizontal" method="post" data-options="novalidate:true" enctype="multipart/form-data">

                        <div class="card mb-1 border-blue border-darken-1">
                			<div class="table-responsive">

                                <table class="table table-bordered table-hover">
                                    <tbody>
                                        <tr>
                                            <td width="23%">Nama Perorangan:</td>
                                            <td>
                                                <?= str_replace("Konsultan Perorangan", "",$reqNamaPerusahaan)?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><?=translate("Alamat", "Address")?>:</td>
                                            <td>
                                                <?=$reqAlamat?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="width: 20%">Provinsi</td>
                                            <td><?=$rekanan->getField("NAMAPROPINSI")?></td>
                                        </tr>
                                        <tr>
                                            <td style="width: 20%">Kabupaten/Kota</td>
                                            <td><?=$rekanan->getField("NAMAKABKOTA")?></td>
                                        </tr>
                                        <tr>
                                            <td style="width: 20%">Kecamatan</td>
                                            <td><?=$rekanan->getField("NAMAKECAMATAN")?></td>
                                        </tr>
                                        <tr>
                                            <td style="width: 20%">Kelurahan</td>
                                            <td><?=$rekanan->getField("KELURAHAN")?></td>
                                        </tr>
                                        <tr>
                                            <td><?=translate("Kode Pos", "Postal Code")?>:</td>
                                            <td>
                                                <?=$reqKodePos?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><?=translate("NPWP", "Taxpayer Registration Number")?>:</td>
                                            <td>
                                                <?=$reqNPWP?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>File NPWP:</td>
                                            <td>
                                                <?php
                                                    $arrFile = explode(";", $rekanan->getField("NAMA_FILE_NPWP"));
                                                    for($iFile=0;$iFile<count($arrFile);$iFile++)
                                                    {
                                                ?>
                                                        <?=$arrFile[$iFile]?>
                                                        <?php if ($arrFile[$iFile]) { ?>
                                                        <a target="_blank" href="<?= base_url('uploads/rekanan/').$rekanan->getField("NPWP_FILE") ?>" class="badge badge-primary">Download file NPWP</a>
                                                        <?php } ?>
                                                <?php
                                                    }
                                                ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>KTP:</td>
                                            <td>
                                                <?=$reqKTP?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>File KTP:</td>
                                            <td>
                                                <?= $reqNamaFileKTP ?>
                                                <?php if ($reqKTP) { ?>
                                                <a target="_blank" href="<?= base_url('uploads/rekanan/').$reqKTPFile ?>" class="badge badge-primary">Download file KTP</a>
                                                <?php } ?>
                                            </td>
                                        </tr>
                                        <?php
                                        if($reqPKP == '')
                                        {}
                                        else
                                        {
                                        ?>
                                        <tr>
                                            <td><?=translate("PKP", "PKP")?>:</td>
                                            <td>
                                                <?=$reqPKP?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><?=translate("Tanggal", "Date")?>:</td>
                                            <td>
                                                <?=getFormattedDateJson($reqMasaBerlakuPKP)?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>File:</td>
                                            <td>
                                            <?php
                                                $arrFile = explode(";", $rekanan->getField("NAMA_FILE_PKP"));
                                                for($iFile=0;$iFile<count($arrFile);$iFile++)
                                                {
                                            ?>
                                                    <?=$arrFile[$iFile]?>
                                                    <?php if ($arrFile[$iFile]) { ?>
                                                    <a href="<?= base_url('uploads/rekanan/').$rekanan->getField("PKP_FILE") ?>" class="badge badge-primary">Download file PKP</a>
                                                    <?php } else { echo "-";} ?>
                                            <?php
                                                }
                                            ?>
                                            </td>
                                        </tr>
                                        <?php
                                        }
                                        ?>
                                        <tr>
                                            <td><?=translate("No. telepon", "Telephone")?>:</td>
                                            <td>
                                                <?=$reqNomorTelepon?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><?=translate("No. fax", "Faximile")?>:</td>
                                            <td>
                                                <?=$reqNomorFax?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><?=translate("Kontak", "Contact")?>:</td>
                                            <td>
                                                <?=$reqKontakPerson?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><?=translate("Hp.", "Handphone")?>:</td>
                                            <td>
                                                <?=$reqKontakPersonHp?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>E-mail:</td>
                                            <td>
                                                <?=$reqEmail?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>Website:</td>
                                            <td>
                                                <?=$reqWebsite?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><?=translate("Kualifikasi", "Qualification")?>:</td>
                                            <td>
                                                <?=$reqKualifikasi?>
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
                                        <strong>Informasi Pembayaran (Nomor Rekening)</strong>
                                    </div>
                                    <div class="table-responsive">
                                        <table class="border-double table mb-0">
                                            <tbody>
                                                <tr>
                                                    <td style="width: 20%">Bank :</td>
                                                    <td>
                                                        <?=$rekanan->getField("BANK")?>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td style="width: 20%">No. Rekening :</td>
                                                    <td>
                                                        <?=$rekanan->getField("BANK_REKENING")?>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td style="width: 20%">Atas Nama :</td>
                                                    <td>
                                                        <?=$rekanan->getField("BANK_PEMILIK")?>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td style="width: 20%">Cabang :</td>
                                                    <td>
                                                        <?= $rekanan->getField("BANK_CABANG")?>
                                                    </td>
                                                </tr>
                                                <!-- <tr>
                                                    <td style="width: 20%">Cara Pembayaran :</td>
                                                    <td>
                                                        <?php // echo $rekanan->getField("PAYMENT_METHOD")?>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td style="width: 20%">Mata Uang :</td>
                                                    <td>
                                                        <?php // echo $rekanan->getField("MATA_UANG_KODE")?>
                                                    </td>
                                                </tr> -->
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

            		</form>
                </div>
            </div>
        </div>
    </div>
</div>
