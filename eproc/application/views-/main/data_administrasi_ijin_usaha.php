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

/* INCLUDE FILE */
$this->load->library("kauth");  $userLogin = new kauth();
//$this->load->library("rekananijinusahainfo");  $ijin_usaha_tanggal_berakhir = new rekananijinusahainfo();
$this->load->model("Rekanan");
$this->load->model("IjinUsaha");
$this->load->model("RekananIjinUsaha");
$this->load->model("RekananBidangUsaha");

include_once("functions/string.func.php");
include_once("functions/date.func.php");
include_once("functions/default.func.php");

$rekanan_ijin_usaha = new RekananIjinUsaha();
$rekanan_bidang_usaha = new RekananBidangUsaha();
$rekanan = new Rekanan();
$ijin_usaha = new IjinUsaha();

// $rekanan->selectByParams(array("A.REKANAN_ID"=>$reqId),-1,-1);
// $rekanan->firstRow();

// 1   SIUP
// 3   SIUJK
// 4   SIUI
// 5   Lain-lain
// 99  SBU
// $tempRekananIjinUsahaId[] = null;
$rekanan_ijin_usaha->selectByParams(array("REKANAN_ID" => $this->ID), -1,-1,  " AND NOT IJIN_USAHA_ID = 99 ");
//echo $rekanan_ijin_usaha->query;
//$rekanan_ijin_usaha->firstRow();
$i=0;
while($rekanan_ijin_usaha->nextRow()){
  $tempRekananIjinUsahaId[$i] = $rekanan_ijin_usaha->getField("REKANAN_IJIN_USAHA_ID");
  $tempIjinUsahaId[$i] = $rekanan_ijin_usaha->getField("IJIN_USAHA_ID");
  $tempNomor[$i] = $rekanan_ijin_usaha->getField("NO_IJIN");
  $tempTanggalIjin[$i] = getFormattedDate($rekanan_ijin_usaha->getField("TANGGAL"));
  $tempTanggalBerakhir[$i] = getFormattedDate($rekanan_ijin_usaha->getField("TANGGAL_BERAKHIR"));
  $tempTanggalBerakhirAsli[$i] = $rekanan_ijin_usaha->getField("TANGGAL_BERAKHIR");
  $tempInstansi[$i] = $rekanan_ijin_usaha->getField("INSTANSI");
  $tempBidang[$i] = $rekanan_ijin_usaha->getField("IJIN_USAHA");
  $tempLinkFileTemp[$i]= $rekanan_ijin_usaha->getField("NAMA_FILE");
  $tempFileTemp[$i]= $rekanan_ijin_usaha->getField("PATH_FILE");
  $tempPKKPR[$i]= $rekanan_ijin_usaha->getField("PKKPR");
  $tempFileTemp2[$i]= $rekanan_ijin_usaha->getField("PATH_FILE2");
  $tempTanggalPKKPR[$i]= $rekanan_ijin_usaha->getField("TANGGAL_PKKPR");
  $tempTanggalPKKPRBerakhir[$i]= $rekanan_ijin_usaha->getField("TANGGAL_PKKPR_BERAKHIR");
  $i++;
}
?>
<div class="row">
    <div class="col-md-12 col-sm-12">
        <div class="card">
            <div class="card-header card-head-inverse bg-primary">
              <h4 class="card-title text-white">N I B
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

                    if ($this->libsession->cekChecklist('nib') || ($reqStatusUser == '0' && $reqStatusValidasi == '1')) // Check Checlist Verifikator
                    {
                      if ($this->libsession->cekUrl($this->uri->segment(3, ""))) { 
                        // echo $tempRekananIjinUsahaId.'---';
                        if (!$tempRekananIjinUsahaId) { ?>
                      <div class="badge badge-pill badge-warning">
                        <a href="main/index/data_administrasi_ijin_usaha_ubah" data-toogle=""><span class="fa fa-plus text-white icon-line-height" data-toggle="tooltip" data-placement="top" title="Tambah Data"></span> Tambah</a>
                      </div>
                  <?php
                        }
                      }
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
                if (array_intersect($this->libsession->cekStatusValidasiRekanan(), $arrStatusValidasi)) { } else { echo $this->libsession->cekStatusValidasiRekananStr();  }

                  if ($rekanan_ijin_usaha->countRow() == 0) {
                    echo ". : : Data belum ada : : .";
                  } else {
                ?>
                <form id="ff" class="easyui-form form-horizontal" method="post" data-options="novalidate:true" enctype="multipart/form-data">
                  <?php
                  for($x=0; $x < count($tempRekananIjinUsahaId); $x++){
                  $rekanan_bidang_usaha->selectByParamsMonitoring(array("REKANAN_ID" => $this->ID, "IJIN_USAHA_ID"=>$tempIjinUsahaId[$x]));
                  ?>
                    <div class="card mb-1 border-blue border-darken-1">
                      <div class="card-content">
                        <div class="p-1">
                          <div class="alert bg-primary alert-icon-left alert-arrow-left alert-dismissible mb-2" role="alert">
                            <span class="alert-icon"><i class="fa fa-th"></i></span>  
                            <!-- <strong>Ijin <?=$tempBidang[$x]?> &nbsp;</strong> -->
                            <?php
                            if (array_intersect($this->libsession->cekStatusValidasiRekanan(), $arrStatusValidasi)) {
                                if ($this->libsession->cekChecklist('nib') || ($reqStatusUser == '0' && $reqStatusValidasi == '1')) // Check Checlist Verifikator
                                {
                                  if ($this->libsession->cekUrl($this->uri->segment(3, ""))) {
                            // if ($tempIjinUsahaId[$x] != '1') // SIUP gak bisa diubah setelah terverifikasi
                            // { ?>
                              <div class="badge badge-pill badge-warning">
                                <a href="main/index/data_administrasi_ijin_usaha_ubah?reqIjinUsaha=<?=$tempIjinUsahaId[$x]?>"> <span class="fa fa-pencil text-white icon-line-height" data-toggle="tooltip" data-placement="top" title="Ubah Ijin <?=$tempBidang[$x]?>"></span> Ubah</a>
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
                                  <td style="width: 20%">Nomor Ijin</td>
                                  <td>: <?=$tempNomor[$x]?> </td>
                                </tr>
                                <tr>
                                  <td style="width: 20%">Tanggal Cetak</td>
                                  <td>: <?=$tempTanggalIjin[$x]?></td>
                                </tr>
                                <tr>
                                  <td style="width: 20%">File <?=$tempBidang[$x]?></td>
                                  <td>:
                                    <?php
                                      $arrFile = explode(";", $tempLinkFileTemp[$x]);
                                      for($iFile=0;$iFile<count($arrFile);$iFile++)
                                      {
                                        if ($tempFileTemp[$x]) {
                                          if(file_exists('uploads/ijin_usaha/'.$tempFileTemp[$x])) {
                                            echo $arrFile[$iFile].' <br><a href="'.base_url('uploads/ijin_usaha/').$tempFileTemp[$x].'" class="badge badge-primary" target="_blank"><span class="fa fa-download"></span> Download file</a>';
                                          }
                                        } else {
                                          echo "-";
                                        }
                                      }
                                    ?>
                                  </td>
                                </tr>
                                <?php 
                                if ($tempPKKPR[$x] == '1') { ?>
                                <tr>
                                  <td colspan="2"><b>PKKPR</b></td>
                                </tr>
                                <tr>
                                  <td style="width: 20%">Tanggal Terbit</td>
                                  <td>: <?= getFormattedDateJson($tempTanggalPKKPR[$x]) ?></td>
                                </tr>
                                <tr>
                                  <td style="width: 20%">Tanggal Berakhir</td>
                                  <td>: <?= getFormattedDateJson($tempTanggalPKKPRBerakhir[$x]) ?></td>
                                </tr>
                                <tr>
                                  <td style="width: 20%">File PKKPR</td>
                                  <td>:
                                    <?php
                                      $arrFile2 = explode(";", $tempFileTemp2[$x]);
                                      for($iFile=0;$iFile<count($arrFile2);$iFile++)
                                      {
                                        if ($tempFileTemp2[$x]) {
                                          if(file_exists('uploads/ijin_usaha/'.$tempFileTemp2[$x])) {
                                            echo '<a href="'.base_url('uploads/ijin_usaha/').$tempFileTemp2[$x].'" class="badge badge-primary" target="_blank"><span class="fa fa-download"></span> Download file</a>';
                                          }
                                        } else {
                                          echo "-";
                                        }
                                      }
                                    ?>
                                  </td>
                                </tr>
                                <?php 
                                } else { ?>
                                  <tr>
                                    <td colspan="2"><b>SELFT DECLARE</b></td>
                                  </tr>
                                  <tr>
                                  <td style="width: 20%">File</td>
                                  <td>:
                                    <?php
                                      $arrFile2 = explode(";", $tempFileTemp2[$x]);
                                      for($iFile=0;$iFile<count($arrFile2);$iFile++)
                                      {
                                        if ($tempFileTemp2[$x]) {
                                          if(file_exists('uploads/ijin_usaha/'.$tempFileTemp2[$x])) {
                                            echo '<a href="'.base_url('uploads/ijin_usaha/').$tempFileTemp2[$x].'" class="badge badge-primary" target="_blank"><span class="fa fa-download"></span> Download file</a>';
                                          }
                                        } else {
                                          echo "-";
                                        }
                                      }
                                    ?>
                                  </td>
                                </tr>
                                <?php 
                                } ?>
                              </tbody>
                            </table>
                          </div> <br>

                          <?php
                          if ($rekanan_bidang_usaha->countRow() > 0) { ?>
                          <h2> Bidang usaha</h2>
                            <div class="table-responsive">

                              <table class="table table-bordered table-hover" id="tbl_bidang">
                                <tbody>
                                  <tr class="judul-kolom">
                                    <th width="2%">No</th>
                                    <!-- <th>Kode</th> -->
                                    <th>Bidang usaha</th>
                                  </tr>
                                <?php
                                $i=1;
                                while($rekanan_bidang_usaha->nextRow())
                                {
                                ?>
                                  <tr >
                                    <td><?=$i?>.</td>
                                    <!-- <td><?=$rekanan_bidang_usaha->getField("BIDANG_USAHA_ID")?></td> -->
                                    <td>
                                      <?php  
                                      if ($rekanan_bidang_usaha->getField("VALIDASI") == 1 ) {
                                         echo '<i class="fa fa-check-square" aria-hidden="true" style="color:blue"></i>';
                                      } else {
                                         echo '<i class="fa fa-minus-square" aria-hidden="true" style="color:red"></i>';
                                      } ?>
                                      <?=$rekanan_bidang_usaha->getField("NAMA")?></td>
                                  </tr>
                                <?php
                                $i++;
                                }
                                ?>
                                </tbody>
                              </table>
                            </div>
                          <?php
                          } ?>
                            <hr>
                        </div>
                      </div>
                    </div>

                <?php
                  }
                }
                ?>
                </form>
              </div>
            </div>
        </div>
    </div>
</div>
