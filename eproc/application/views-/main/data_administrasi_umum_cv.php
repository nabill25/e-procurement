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

$FILE_DIR = "uploads/rekanan/";

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
$reqCVFile = $rekanan->getField("CV_FILE");
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
                <h4 class="card-title text-white">CV ( Daftar Riwayat Hidup )
                    <?php
                    $arrStatusValidasi = array('0','10');
                    if (array_intersect($this->libsession->cekStatusValidasiRekanan(), $arrStatusValidasi)) {
                      if ($this->libsession->cekChecklist('cv')) // Check Checlist Verifikator
                      {?>
                    <div class="badge badge-glow badge-pill badge-warning">
                        <a href="main/index/registrasi_rekanan_cv">
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
                    if ($reqCVFile) {
                    if (array_intersect($this->libsession->cekStatusValidasiRekanan(), $arrStatusValidasi)) { } else { echo NOTIF_PENYEDIA_TERVERIFIKASI;  } ?>
                    <iframe src="<?=$FILE_DIR.str_replace("'", "''", $reqCVFile)?>" style="width:100%; height:600px;"></iframe>
                    <?php
                  } else {
                    echo ". : : Data belum ada : : .";
                  } ?>
                </div>
            </div>
        </div>
    </div>
</div>
