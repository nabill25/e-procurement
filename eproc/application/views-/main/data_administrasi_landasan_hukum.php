<?php
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 *
 */

$this->libsession->cekSession();

$this->load->model("UserLogin");

$this->load->library("kauth");  $userLogin = new kauth();
$this->load->model("RekananAkta");
$this->load->model("Rekanan");
include_once("functions/string.func.php");
include_once("functions/date.func.php");
include_once("functions/default.func.php");
/* create objects */
$rekanan = new Rekanan();
$rekanan_akta = new RekananAkta();
$rekanan_akta_perubahan = new RekananAkta();
$rekanan_akta_perubahan_row = new RekananAkta();

// $rekanan->selectByParams(array("REKANAN_ID"=>$this->ID),-1,-1);
// $rekanan->firstRow();


$rekanan_akta->selectByParams(array("REKANAN_ID"=>$this->ID, "AKTA_TYPE_ID" => 1),-1,-1, ' ORDER BY REKANAN_AKTA_ID DESC LIMIT 1');
$rekanan_akta->firstRow();
$tempNomor = $rekanan_akta->getField("NOMOR");
$tempNomorKemenkumham = $rekanan_akta->getField("NOMOR_KEMENKUMHAM");
$tempTanggal = getFormattedDateJson($rekanan_akta->getField("TANGGAL"));
$tempNotaris = $rekanan_akta->getField("NOTARIS");
$tempLinkFileTemp= $rekanan_akta->getField("NAMA_FILE");
$tempFileTemp= $rekanan_akta->getField("PATH_FILE");

$rekanan_akta_perubahan->selectByParams(array("REKANAN_ID"=>$this->ID, "AKTA_TYPE_ID" => 2),-1,-1, ' ORDER BY TANGGAL DESC LIMIT 1');
$rekanan_akta_perubahan->firstRow();
$tempNomor1 = $rekanan_akta_perubahan->getField("NOMOR");
$tempNomor1Kemenkumham = $rekanan_akta_perubahan->getField("NOMOR_KEMENKUMHAM");
$tempTanggal1 = getFormattedDateJson($rekanan_akta_perubahan->getField("TANGGAL"));
$tempNotaris1 = $rekanan_akta_perubahan->getField("NOTARIS");
$tempLinkFileTemp1 = $rekanan_akta_perubahan->getField("NAMA_FILE");
$tempFileTemp1 = $rekanan_akta_perubahan->getField("PATH_FILE");

?>

<div class="row">
  <div class="col-md-12 col-sm-12">
    <div class="card">
      <div class="card-header card-head-inverse bg-primary">
        <h4 class="card-title text-white">Akta Pendirian  </h4>
        <a class="heading-elements-toggle"><i class="fa fa-ellipsis-v font-medium-3"></i></a>
        <div class="heading-elements">
            <ul class="list-inline mb-0">
              <li><a data-action="expand"><i class="ft-maximize"></i></a></li>
              <!-- <li><a data-action="close"><i class="ft-x"></i></a></li> -->
            </ul>
        </div>
      </div>
      <form id="ff" class="easyui-form form-horizontal" method="post" data-options="novalidate:true" enctype="multipart/form-data">
        <div class="card-content collapse show border-info border-darken-2">
          <div class="card-body">
            <?php
            $arrStatusValidasi = array('0','10');
            if (array_intersect($this->libsession->cekStatusValidasiRekanan(), $arrStatusValidasi)) { } else { echo $this->libsession->cekStatusValidasiRekananStr();  } ?>

            <div class="card mb-1 border-blue border-darken-1">
              <div class="card-content">
                <div class="p-1">
                  <div class="alert bg-primary alert-icon-left alert-arrow-left alert-dismissible mb-2" role="alert">
                    <span class="alert-icon"><i class="fa fa-th"></i></span>  <strong>Akta Pendirian</strong>
                    <?php
                    if (array_intersect($this->libsession->cekStatusValidasiRekanan(), $arrStatusValidasi)) {
                      $rekanan = new Rekanan();
                      $rekanan->selectByParams(array("A.REKANAN_ID"=>$this->ID),-1,-1);
                      $rekanan->firstRow();
                      $reqStatusValidasi= $rekanan->getField("STATUS_VALIDASI");

                      $userRekanan = new Userlogin();
                      $userRekanan->selectByParams(array("REKANAN_ID"=>$this->ID),-1,-1);
                      $userRekanan->firstRow();
                      $reqStatusUser= $userRekanan->getField("USER_STATUS");

                      if ($this->libsession->cekChecklist('akta') || ($reqStatusUser == '0' && $reqStatusValidasi == '1')) // Check Checlist Verifikator
                      {
                        if ($this->libsession->cekUrl($this->uri->segment(3, ""))) {
                     ?>
                    <div class="badge badge-pill badge-warning">
                      <a href="main/index/data_administrasi_landasan_hukum_ubah?reqAktaType=1"> <span class="fa fa-pencil text-white icon-line-height" data-toggle="tooltip" data-placement="top" title="Ubah Akta Pendirian"></span> Ubah </a>
                    </div>
                    <?php
                        }
                      }
                    } ?>
                  </div>
                  <div class="table-responsive">
                    <table class="border-double table mb-0">
                      <tbody>
                        <tr>
                          <td style="width: 20%">Nomor Akta</td>
                          <td>: <?=$tempNomor?> </td>
                        </tr>
                        <tr>
                          <td style="width: 20%">Nomor SK KEMENKUMHAM</td>
                          <td>: <?=$tempNomorKemenkumham?> </td>
                        </tr>
                        <tr>
                          <td style="width: 20%">Tanggal</td>
                          <td>: <?=$tempTanggal?> </td>
                        </tr>
                        <tr>
                          <td style="width: 20%">Nama Notaris</td>
                          <td>: <?=$tempNotaris?> </td>
                        </tr>
                        <tr>
                          <td style="width: 20%">File Akta Pendirian dan SK KEMENKUMHAM</td>
                          <td>:
                            <?php
                              $arrFile = explode(";", $tempLinkFileTemp);
                              for($iFile=0;$iFile<count($arrFile);$iFile++)
                              {
                                if ($tempFileTemp) {
                                  if (file_exists('uploads/landasan_hukum/'.$tempFileTemp)) {
                                    // code...
                                  echo $arrFile[$iFile].' <br><a href="'.base_url('uploads/landasan_hukum/').$tempFileTemp.'" class="badge badge-primary" target="_blank"><span class="fa fa-download"></span> Download file</a>';
                                  }
                                } else {
                                  echo '-';
                                }
                            ?>
                            <?php
                              }
                            ?>
                          </td>
                        </tr>
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>
            </div>

            <div class="card mb-1 border-blue border-darken-1">
              <div class="card-content">
                <div class="p-1">
                  <div class="alert bg-primary alert-icon-left alert-arrow-left alert-dismissible mb-2" role="alert">
                    <span class="alert-icon"><i class="fa fa-th"></i></span>  <strong>Akta Perubahan Terakhir</strong>
                    <?php
                    $arrStatusValidasi = array('0','10');
                    if (array_intersect($this->libsession->cekStatusValidasiRekanan(), $arrStatusValidasi)) {
                      if ($this->libsession->cekChecklist('akta') || ($reqStatusUser == '0' && $reqStatusValidasi == '1')) // Check Checlist Verifikator
                      {
                        if ($this->libsession->cekUrl($this->uri->segment(3, ""))) {
                     ?>
                    <div class="badge badge-pill badge-warning">
                      <a href="main/index/data_administrasi_landasan_hukum_ubah?reqAktaType=2"> <span class="fa fa-pencil text-white font-medium-2 icon-line-height" data-toggle="tooltip" data-placement="top" title="Ubah Akta Perubahan Terakhir"></span> Ubah</a>
                    </div>
                    <?php
                        }
                      }
                    } ?>
                  </div>
                  <div class="table-responsive">
                    <table class="table table-bordered">
                      <tr>
                        <th>No Akta</th>
                        <th>Nomor SK KEMENKUMHAM</th>
                        <th width="15%">Tanggal</th>
                        <th>Nama Notaris</th>
                        <th>File Akta Perubahan dan SK KEMENKUMHAM</th>
                      </tr>
                      <?php
                      $rekanan_akta_perubahan_row->selectByParams(array("REKANAN_ID"=>$this->ID, "AKTA_TYPE_ID" => 2),-1,-1, ' ORDER BY TANGGAL DESC');

                      while($rekanan_akta_perubahan_row->nextRow())
                      { ?>
                      <tr>
                        <td><?= $rekanan_akta_perubahan_row->getField("NOMOR") ?></td>
                        <td><?= $rekanan_akta_perubahan_row->getField("NOMOR_KEMENKUMHAM") ?></td>
                        <td><?= getFormattedDateJson($rekanan_akta_perubahan_row->getField("TANGGAL")) ?></td>
                        <td><?= $rekanan_akta_perubahan_row->getField("NOTARIS") ?></td>
                        <td><?php
                            if ($rekanan_akta_perubahan_row->getField("PATH_FILE") != '') {
                                  if (file_exists('uploads/landasan_hukum/'.$rekanan_akta_perubahan_row->getField("PATH_FILE"))) {
                              ?>
                              <?=$rekanan_akta_perubahan_row->getField("NAMA_FILE")?><br>
                              <a href="<?= base_url('uploads/landasan_hukum/').$rekanan_akta_perubahan_row->getField("PATH_FILE") ?>" class="badge badge-primary" target="_blank"><span class="fa fa-download" target="_blank"></span> Download file</a>
                            <?php
                              }
                            } else {
                              echo '-';
                            }
                             ?>
                        </td>
                      </tr>
                      <?php
                      } ?>
                    </table>

                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>
