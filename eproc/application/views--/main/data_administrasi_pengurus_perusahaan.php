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
$this->load->model("Rekanan");
$this->load->model("RekananPengurus");
$this->load->model("RekananTipe");
include_once("functions/string.func.php");
include_once("functions/date.func.php");
include_once("functions/default.func.php");
/* create objects */

$rekanan = new Rekanan();
$rekanan_pengurus_komisaris = new RekananPengurus();
$rekanan_pengurus_direksi = new RekananPengurus();
$rekanan_pengurus = new RekananPengurus();
$rekanan_tipe = new RekananTipe();

/* DATA VIEW */
$allRecord_komisaris = $rekanan_pengurus_komisaris->getCountByParams(array("REKANAN_ID"=>$this->ID,"TIPE"=>1));
$rekanan_pengurus_komisaris ->selectByParams(array("REKANAN_ID"=>$this->ID,"TIPE"=>1),-1,-1);

$allRecord_direksi = $rekanan_pengurus_direksi->getCountByParams(array("REKANAN_ID"=>$this->ID,"TIPE"=>2));
$rekanan_pengurus_direksi ->selectByParams(array("REKANAN_ID"=>$this->ID,"TIPE"=>2),-1,-1);

$rekanan->selectByParams(array("A.REKANAN_ID"=>$this->ID),-1,-1);
$rekanan->firstRow();

$tempRekananTipeID = $rekanan->getField("REKANAN_TIPE_ID");
?>

<div class="row">
  <div class="col-md-12 col-sm-12">
    <div class="card">
      <div class="card-header card-head-inverse bg-primary">
        <h4 class="card-title text-white">Pengurus Perusahaan
        </h4>
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
                  <span class="alert-icon"><i class="fa fa-th"></i></span>  <strong>KOMISARIS - <small>Data komisaris diperlukan jika jenis perusahaan Anda adalah PT (Perseroan Terbatas).</small></strong>
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

                    if ($this->libsession->cekChecklist('pengurus') || ($reqStatusUser == '0' && $reqStatusValidasi == '1')) // Check Checlist Verifikator
                    {
                      if ($this->libsession->cekUrl($this->uri->segment(3, ""))) {
                   ?>
                    <div class="badge badge-pill badge-warning">
                      <a href="main/index/data_administrasi_pengurus_perusahaan_tambah/?reqTipe=1"> <span class="fa fa-plus text-white icon-line-height" data-toggle="tooltip" data-placement="top" title="Tambah Data"></span> Tambah</a>
                    </div>
                  <?php
                      }
                    }
                  } ?>
                </div>
                <div class="table-responsive">
                  <table class="table table-bordered table mb-0 ">
                    <tbody>
                      <tr class="judul-kolom">
                        <th width="3%">No</th>
                        <th>Nama</th>
                        <th>KTP / Passport / KITAS</th>
                        <th>Jabatan dalam Perusahaan</th>
                        <th width="10%" style="text-align:center">File KTP atau Identitas</th>
                        <th width="10%" style="text-align:center">File NPWP</th>
                        <?php
                        if (array_intersect($this->libsession->cekStatusValidasiRekanan(), $arrStatusValidasi)) {
                          if ($this->libsession->cekChecklist('pengurus') || ($reqStatusUser == '0' && $reqStatusValidasi == '1')) // Check Checlist Verifikator
                          {?>
                        <th class="text-center" width="9%">Aksi</th>
                        <?php
                          }
                        } ?>
                      </tr>
                      <?php
                      if($allRecord_komisaris > 0){
                        $no_komisaris = 1;
                        while($rekanan_pengurus_komisaris->nextRow()){
                      ?>
                       <tr >
                           <td><?=$no_komisaris?>.</td>
                           <td>
                             <?=$rekanan_pengurus_komisaris->getField("NAMA")?>
                             <?php
                             if ($rekanan_pengurus_komisaris->getField("JENIS_KELAMIN") == 'L') {
                               echo '<sup>Laki-Laki</sup>';
                             } else {
                               echo '<sup>Perempuan</sup>';
                             }
                            ?>
                           </td>
                           <td>
                             KTP: <?=$rekanan_pengurus_komisaris->getField("KTP")?> <br>
                             NPWP: <?=$rekanan_pengurus_komisaris->getField("NPWP")?>
                           </td>
                           <td><?=$rekanan_pengurus_komisaris->getField("JABATAN")?></td>
                           <td>
                            <?php
                            if ($rekanan_pengurus_komisaris->getField("PATH_FILE")) {
                              if (file_exists('uploads/pengurus/'.$rekanan_pengurus_komisaris->getField("PATH_FILE"))) { ?>
                               <a href="<?= base_url('uploads/pengurus/').$rekanan_pengurus_komisaris->getField("PATH_FILE") ?>" class="badge badge-primary" target="_blank"><span class="fa fa-download"></span> Download file</a>
                              <?php
                              }
                            } else {
                              echo "-";
                            } ?>
                          </td>
                           <td>
                            <?php
                            if ($rekanan_pengurus_komisaris->getField("PATH_FILE2")) {
                              if (file_exists('uploads/pengurus/'.$rekanan_pengurus_komisaris->getField("PATH_FILE2"))) { ?>
                               <a href="<?= base_url('uploads/pengurus/').$rekanan_pengurus_komisaris->getField("PATH_FILE2") ?>" class="badge badge-primary" target="_blank"><span class="fa fa-download"></span> Download file</a>
                              <?php
                              }
                            } else {
                              echo "-";
                            } ?>
                          </td>
                           <?php
                            if (array_intersect($this->libsession->cekStatusValidasiRekanan(), $arrStatusValidasi)) {
                              if ($this->libsession->cekChecklist('pengurus') || ($reqStatusUser == '0' && $reqStatusValidasi == '1')) // Check Checlist Verifikator
                              {
                                if ($this->libsession->cekUrl($this->uri->segment(3, ""))) {
                             ?>
                           <td class="text-center">
                              <a href="main/index/data_administrasi_pengurus_perusahaan_tambah/?reqTipe=1&reqPengurusID=<?=$rekanan_pengurus_komisaris->getField("REKANAN_PENGURUS_ID")?>" class="btn-aksi">
                                <?= ICON_EDIT ?>
                              </a>
                              <a onClick="deleteData('rekanan_pengurus_json/delete/', '<?=$rekanan_pengurus_komisaris->getField("REKANAN_PENGURUS_ID")?>')" class="btn-aksi">
                                <?= ICON_DELETE ?>
                              </a>
                             </td>
                            <?php
                                }
                              }
                            } ?>
                      </tr>
                      <?php
                        $no_komisaris = $no_komisaris + 1;
                          }
                        }
                        else
                        {
                      ?>
                       <tr class="gelap">
                        <td colspan="6" align="center">
                        <div class="merah">. : : Data belum ada : : .</div>
                        </td>
                      </tr>
                      <?php
                      }
                      ?>
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
                  <span class="alert-icon"><i class="fa fa-th"></i></span>  <strong>DIREKSI </strong>
                  <?php
                  $arrStatusValidasi = array('0','10');
                  if (array_intersect($this->libsession->cekStatusValidasiRekanan(), $arrStatusValidasi)) {
                    if ($this->libsession->cekChecklist('pengurus') || ($reqStatusUser == '0' && $reqStatusValidasi == '1')) // Check Checlist Verifikator
                    {
                      if ($this->libsession->cekUrl($this->uri->segment(3, ""))) {
                     ?>
                    <div class="badge badge-pill badge-warning">
                      <a href="main/index/data_administrasi_pengurus_perusahaan_tambah/?reqTipe=2"> <span class="fa fa-plus text-white icon-line-height" data-toggle="tooltip" data-placement="top" title="Tambah Data"></span> Tambah</a>
                    </div>
                  <?php
                      }
                    }
                  } ?>
                </div>
                <div class="table-responsive">
                  <table class="table table-bordered table mb-0">
                    <tbody>
                      <tr class="judul-kolom">
                        <th width="3%">No</th>
                        <th>Nama</th>
                        <th>KTP / Passport / KITAS</th>
                        <th>Jabatan dalam Perusahaan</th>
                        <th width="10%" style="text-align:center">File KTP atau Identitas</th>
                        <th width="10%" style="text-align:center">File NPWP</th>
                        <?php
                        if (array_intersect($this->libsession->cekStatusValidasiRekanan(), $arrStatusValidasi)) {
                          if ($this->libsession->cekChecklist('pengurus') || ($reqStatusUser == '0' && $reqStatusValidasi == '1')) // Check Checlist Verifikator
                          {
                            ?>
                        <th class="text-center" width="9%">Aksi</th>
                        <?php
                          }
                        } ?>
                      </tr>
                      <?php
                        if($allRecord_direksi > 0){
                          $no_direksi = 1;
                          while($rekanan_pengurus_direksi->nextRow()){
                        ?>
                       <tr >
                           <td><?=$no_direksi?>.</td>
                           <td>
                             <?=$rekanan_pengurus_direksi->getField("NAMA")?>
                             <?php
                             if ($rekanan_pengurus_direksi->getField("JENIS_KELAMIN") == 'L') {
                               echo '<sup>Laki-Laki</sup>';
                             } else {
                               echo '<sup>Perempuan</sup>';
                             }
                            ?>
                           </td>
                           <td>
                             KTP: <?=$rekanan_pengurus_direksi->getField("KTP")?> <br>
                             NPWP: <?=$rekanan_pengurus_direksi->getField("NPWP")?>
                           </td>
                           <td><?=$rekanan_pengurus_direksi->getField("JABATAN")?></td>
                           <td>
                            <?php
                            if ($rekanan_pengurus_direksi->getField("PATH_FILE")) {
                              if (file_exists('uploads/pengurus/'.$rekanan_pengurus_direksi->getField("PATH_FILE"))) {
                            ?>
                                <a href="<?= base_url('uploads/pengurus/').$rekanan_pengurus_direksi->getField("PATH_FILE") ?>" class="badge badge-primary" target="_blank"><span class="fa fa-download"></span> Download file</a>
                            <?php
                              }
                            } else {
                              echo "-";
                            } ?>
                           </td>
                           <td>
                            <?php
                            if ($rekanan_pengurus_direksi->getField("PATH_FILE2")) {
                              if (file_exists('uploads/pengurus/'.$rekanan_pengurus_direksi->getField("PATH_FILE2"))) {
                            ?>
                                <a href="<?= base_url('uploads/pengurus/').$rekanan_pengurus_direksi->getField("PATH_FILE2") ?>" class="badge badge-primary" target="_blank"><span class="fa fa-download"></span> Download file</a>
                            <?php
                              }
                            } else {
                              echo "-";
                            } ?>
                           </td>
                           <?php
                            if (array_intersect($this->libsession->cekStatusValidasiRekanan(), $arrStatusValidasi)) {
                              if ($this->libsession->cekChecklist('pengurus') || ($reqStatusUser == '0' && $reqStatusValidasi == '1')) // Check Checlist Verifikator
                              {
                                if ($this->libsession->cekUrl($this->uri->segment(3, ""))) { ?>
                             <td class="text-center">
                              <a href="main/index/data_administrasi_pengurus_perusahaan_tambah/?reqTipe=2&reqPengurusID=<?=$rekanan_pengurus_direksi->getField("REKANAN_PENGURUS_ID")?>" class="btn-aksi">
                                <?= ICON_EDIT ?>
                              </a>
                              <a onClick="deleteData('rekanan_pengurus_json/delete/', '<?=$rekanan_pengurus_direksi->getField("REKANAN_PENGURUS_ID")?>')" class="btn-aksi">
                                <?= ICON_DELETE ?>
                              </a>
                             </td>
                            <?php
                                }
                              }
                            } ?>
                      </tr>
                      <?php
                        $no_direksi = $no_direksi + 1;
                        }
                      } else {
                      ?>
                      <tr valign="top" class="gelap">
                        <td colspan="6" align="center">
                        <div class="merah">. : : Data belum ada : : .</div>
                        </td>
                      </tr>
                      <?php
                      }
                      ?>
                    </tbody>
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
