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

$this->load->library("kauth");  $userLogin = new kauth();
$this->load->model("Rekanan");
//$this->load->library("rekananijinusahainfo");  $ijin_usaha_tanggal_berakhir = new rekananijinusahainfo();
include_once("functions/string.func.php");
include_once("functions/date.func.php");
include_once("functions/default.func.php");

$rekanan = new Rekanan();
$rekanan_pkp  = new Rekanan(); // tipe ?

$rekanan_pkp->selectByParams(array("A.REKANAN_ID"=>$this->ID), -1, -1);
$rekanan_pkp->firstRow();
$reqNoSuratPKP = $rekanan_pkp->getField("PKP");
$reqTanggalPKP = getFormattedDateJson($rekanan_pkp->getField("PKP_TANGGAL"));
$reqNPWP = $rekanan_pkp->getField("NPWP");

$reqStatusPKP = $rekanan_pkp->getField("STATUS_PKP");
$reqSKTPKP = $rekanan_pkp->getField("SKT_PKP_NOMOR");
$reqSKTPKPFileTemp = $rekanan_pkp->getField("SKT_PKP_FILE");
$reqNamaFileSKTPKP = $rekanan_pkp->getField("NAMA_SKT_PKP_FILE");

?>

<div class="row">
  <div class="col-md-12 col-sm-12">
    <div class="card">
      <div class="card-header card-head-inverse bg-primary">
        <h4 class="card-title text-white">
          <?php
          $arrStatusValidasi = array('0','10');
          if($reqStatusPKP == '0')
          {
            echo "Non PKP";
          }
          else if($reqStatusPKP == '1')
          {
            echo "PKP";
          } else {
            echo "PKP / Non PKP";
          }
          if (array_intersect($this->libsession->cekStatusValidasiRekanan(), $arrStatusValidasi)) {
            $rekanan = new Rekanan();
            $rekanan->selectByParams(array("A.REKANAN_ID"=>$this->ID),-1,-1);
            $rekanan->firstRow();
            $reqStatusValidasi= $rekanan->getField("STATUS_VALIDASI");

            $userRekanan = new Userlogin();
            $userRekanan->selectByParams(array("REKANAN_ID"=>$this->ID),-1,-1);
            $userRekanan->firstRow();
            $reqStatusUser= $userRekanan->getField("USER_STATUS");

            if ($this->libsession->cekChecklist('pkp') || ($reqStatusUser == '0' && $reqStatusValidasi == '1')) // Check Checlist Verifikator
            { 
              if ($this->libsession->cekUrl($this->uri->segment(3, ""))) { ?>
            <div class="badge badge-pill badge-warning">
              <a href="main/index/data_perpajakan_pkp_ubah" data-toogle=""><span class="fa fa-pencil text-white icon-line-height" data-toggle="tooltip" data-placement="top" title="Ubah Data"></span> Edit</a>
            </div>
          <?php
              }
            }
           }
          ?>
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
        <?php if (array_intersect($this->libsession->cekStatusValidasiRekanan(), $arrStatusValidasi)) { } else { echo $this->libsession->cekStatusValidasiRekananStr();  } ?>
        <table class="table table-bordered mb-0">

        <?php
        if($reqStatusPKP == '0')
        {
        ?>
          <tr>
            <td style="width: 20%">File Non PKP</td>
            <td>:
            <?php
              $arrFile = explode(";", $rekanan_pkp->getField("NAMA_NON_PKP_FILE"));
              for($iFile=0;$iFile<count($arrFile);$iFile++)
              {
            ?>
                <?php if (file_exists('uploads/rekanan/'.$rekanan_pkp->getField("NON_PKP_FILE")) && $rekanan_pkp->getField("NON_PKP_FILE") != '' ) {
                echo $arrFile[$iFile]; ?> <br>
                <a href="<?= base_url('uploads/rekanan').'/'.$rekanan_pkp->getField("NON_PKP_FILE") ?>" class="badge badge-primary" target="_blank"><span class="fa fa-download"></span> Download file PKP</a>
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
              <td style="width: 20%">Nomor SPPKP</td> <td>: <?=$reqNoSuratPKP?> </td>
            </tr>
            <tr>
              <td style="width: 20%">Tanggal</td> <td>:<?= getFormattedDateJson($reqTanggalPKP) ?></td>
            </tr>
            <tr>
              <td>File SPPKP</td>
              <td>:
              <?php
                  $arrFile = explode(";", $rekanan_pkp->getField("NAMA_FILE_PKP"));
                  for($iFile=0;$iFile<count($arrFile);$iFile++)
                  {
              ?>
              <?php if (file_exists('uploads/rekanan/'.$rekanan_pkp->getField("PKP_FILE")) && $rekanan_pkp->getField("PKP_FILE") != '' ) {
              echo $arrFile[$iFile]; ?><br>
              <a href="<?= base_url('uploads/rekanan').'/'.$rekanan_pkp->getField("PKP_FILE") ?>" class="badge badge-primary" target="_blank"><span class="fa fa-download"></span> Download file PKP</a>
              <?php } else { echo '<span class="badge badge-danger">Belum upload</span>';} ?>
              <?php
                  }
              ?>
              </td>
          </tr>
          <!-- <tr>
              <td>Nomor. SKT PKP</td>
              <td>: <?php // echo $reqSKTPKP ?></td>
          </tr>
          <tr>
              <td>File SKT PKP</td>
              <td>:
                <?php // if (file_exists('uploads/rekanan/'.$rekanan_pkp->getField("SKT_PKP_FILE")) && $rekanan_pkp->getField("SKT_PKP_FILE") != '' ) { ?>
                <?php // echo $rekanan_pkp->getField("NAMA_SKT_PKP_FILE") ?> <br>
                <a href="<?php // echo  base_url('uploads/rekanan').'/'.$reqSKTPKPFileTemp ?>" class="badge badge-primary" target="_blank"><span class="fa fa-download"></span> Download file SKT PKP</a>
                <?php // } else { echo '<span class="badge badge-danger">Belum upload</span>';} ?>
              </td>
          </tr> -->
          <?php
          } ?>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>
