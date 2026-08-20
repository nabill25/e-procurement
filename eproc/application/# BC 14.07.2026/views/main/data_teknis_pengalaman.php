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
$this->load->model("RekananPengalaman");
include_once("functions/string.func.php");
include_once("functions/date.func.php");
include_once("functions/default.func.php");

$rekanan = new Rekanan();
$rekanan_pengalaman       = new RekananPengalaman(); // tipe 0
$rekanan_pengalaman_progress  = new RekananPengalaman(); // tipe 0

$reqCari = httpFilterPost("reqCari");
$reqInputCari = httpFilterPost("reqInputCari");
$reqMode = httpFilterGet("reqMode");
$reqParamKey = httpFilterGet("reqParamKey");

//$this->ID = 449;
$statement = '';
if($reqCari == 'Submit' ){
  $statement = " AND UPPER(NAMA) LIKE '%".strtoupper($reqInputCari)."%' ";
}

$allRecord_K = $rekanan_pengalaman->getCountByParams(array("REKANAN_ID"=>$this->ID)," ");
$rekanan_pengalaman->selectByParams(array("REKANAN_ID"=>$this->ID), -1, -1," ");

$allRecord_progress = $rekanan_pengalaman_progress->getCountByParams(array("REKANAN_ID"=>$this->ID,"KONTRAK_STATUS"=>2)," ");
$rekanan_pengalaman_progress->selectByParams(array("REKANAN_ID"=>$this->ID,"KONTRAK_STATUS"=>2), -1, -1," ");

?>

<div class="row">
  <div class="col-md-12 col-sm-12">
    <div class="card">
      <div class="card-header card-head-inverse bg-primary">
        <h4 class="card-title text-white">Pengalaman </h4>
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
          $arrStatusValidasi = array('0','10');
          if (array_intersect($this->libsession->cekStatusValidasiRekanan(), $arrStatusValidasi)) { } else { echo $this->libsession->cekStatusValidasiRekananStr();  } ?>

          <div class="card mb-1 border-blue border-darken-1">
            <div class="card-content">
              <div class="p-1">
                <div class="alert bg-primary alert-icon-left alert-arrow-left alert-dismissible mb-2" role="alert">
                  <span class="alert-icon"><i class="fa fa-th"></i></span>  <strong>Pekerjaan</strong>
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

                    if ($this->libsession->cekChecklist('pengalaman') || ($reqStatusUser == '0' && $reqStatusValidasi == '1')) // Check Checlist Verifikator
                    {
                      if ($this->libsession->cekUrl($this->uri->segment(3, ""))) { ?>
                    <div class="badge badge-pill badge-warning">
                      <a href="main/index/data_teknis_pengalaman_selesai_tambah"> <span class="fa fa-plus text-white icon-line-height" data-toggle="tooltip" data-placement="top" title="Tambah Pekerjaan Selesai"></span> Tambah</a>
                    </div>
                  <?php
                      }
                   }
                  } ?>
                </div>
                <div class="table-responsive">
                  <table class="table table-bordered mb-0">
                    <tbody>
                      <tr class="judul-kolom">
                        <th width="5%">No</th>
                        <th>Nama Pekerjaan</th>
                        <th>Bidang Pekerjaan</th>
                        <th>Lokasi</th>
                        <th>File Kontrak</th>
                        <th>BAST</th>
                        <th>Status</th>
                        <?php
                        if (array_intersect($this->libsession->cekStatusValidasiRekanan(), $arrStatusValidasi)) {
                          if ($this->libsession->cekChecklist('pengalaman') || ($reqStatusUser == '0' && $reqStatusValidasi == '1')) // Check Checlist Verifikator
                          {
                            if ($this->libsession->cekUrl($this->uri->segment(3, ""))) { ?>
                        <th class="text-center" width="9%">Aksi</th>
                        <?php
                            }
                          }
                        } ?>
                      </tr>
                      <?php if($allRecord_K > 0){
                        $i = 0;
                        while($rekanan_pengalaman->nextRow()){
                      ?>
                      <tr >
                        <td><?=$i+1?></td>
                        <td>
                          <a class="taut" onclick="displayElement('reqDetil<?=$i?>')" style="cursor:pointer" id="rekanan<?=$i?>"><span class="badge badge-primary"> <small>Lihat detil</small></span> <?=$rekanan_pengalaman->getField("NAMA")?></a>
                        </td>
                        <input type="hidden" id="valTem<?=$i?>"></td>
                        <td><small><?=$rekanan_pengalaman->getField("PENGALAMAN_BIDANG")?></small></td>
                        <td><?=$rekanan_pengalaman->getField("LOKASI")?></td>
                        <td>
                          <?php
                          if ($rekanan_pengalaman->getField("NAMA_FILE")) {
                             echo '<a href="'.base_url('uploads/pengalaman/').$rekanan_pengalaman->getField("PATH_FILE").'" class="badge badge-primary" target="_blank">Download file</a>';
                           } ?>
                          </td>
                          <td>
                            <?php
                            if ($rekanan_pengalaman->getField("PATH_FILE_BA")) { ?>
                            <a href="<?= base_url('uploads/pengalaman/').$rekanan_pengalaman->getField("PATH_FILE_BA") ?>" class="badge badge-primary" target="_blank">Download file</a>
                            <?php
                            } ?>
                          </td>
                          <td>
                            <?php
                            if ($rekanan_pengalaman->getField("KONTRAK_STATUS") == '1') {
                              echo '<span class="badge badge-primary">Selesai</span>';
                            } else {
                              echo '<span class="badge badge-danger">Progres '.$rekanan_pengalaman->getField("PROGRESS").'%</span>';
                            }
                            ?>
                          </td>
                         <?php
                        if (array_intersect($this->libsession->cekStatusValidasiRekanan(), $arrStatusValidasi)) {
                          if ($this->libsession->cekChecklist('pengalaman') || ($reqStatusUser == '0' && $reqStatusValidasi == '1')) // Check Checlist Verifikator
                          {
                            if ($this->libsession->cekUrl($this->uri->segment(3, "")))
                            { ?>
                        <td class="text-center">
                          <a class="btn-aksi" href="main/index/data_teknis_pengalaman_selesai_tambah/?reqPengalamanId=<?=$rekanan_pengalaman->getField("REKANAN_PENGALAMAN_ID")?>">
                            <?= ICON_EDIT ?>
                          </a>
                          <a class="btn-aksi" onClick="deleteData('rekanan_pengalaman_json/delete/', '<?=$rekanan_pengalaman->getField("REKANAN_PENGALAMAN_ID")?>')">
                            <?= ICON_DELETE ?>
                          </a>
                        </td>
                        <?php
                          }
                         }
                        }
                          if (!in_array("0", $this->libsession->cekStatusValidasiRekanan())) {
                            if ($rekanan_pengalaman->getField("KONTRAK_STATUS") == '1') {
                              echo '<td><span class="badge badge-primary">Selesai</span></td>';
                            } else {
                              echo '<td><span class="badge badge-danger">Progres '.$rekanan_pengalaman->getField("PROGRESS").'%</span></td>';
                            }
                          }
                         ?>
                      </tr>
                      <tr id="reqDetil<?=$i?>" style="display:none">
                        <td colspan="6">
                          <table width="100%" border="0" cellpadding="2" cellspacing="1" class="table table-responsive table-bordered">
                            <tr class="judul-kolom2">
                              <td colspan="4">Pemberi Tugas</td>
                            </tr>
                            <tr class="gelap">
                              <td colspan="3">Nama</td>
                              <td width="76%">: <?=$rekanan_pengalaman->getField("PEMBERI_TUGAS")?></td>
                            </tr>
                            <tr class="terang">
                              <td colspan="3">Alamat</td>
                              <td>: <?=$rekanan_pengalaman->getField("PEMBERI_TUGAS_ALAMAT")?></td>
                            </tr>
                            <tr>
                              <td colspan="4">&nbsp;</td>
                            </tr>
                            <tr class="judul-kolom2">
                              <td colspan="4">Kontrak</td>
                            </tr>
                            <tr class="gelap">
                              <td colspan="3">No</td>
                              <td>: <?=$rekanan_pengalaman->getField("KONTRAK_NOMOR")?></td>
                            </tr>
                            <tr class="terang">
                              <td colspan="3">Tanggal</td>
                              <td>: <?=getFormattedDate($rekanan_pengalaman->getField("KONTRAK_TANGGAL"))?></td>
                            </tr>
                            <tr class="gelap">
                              <td colspan="3">Nilai</td>
                              <td>: <?=currencyToPage($rekanan_pengalaman->getField("KONTRAK_NILAI"))?></td>
                            </tr>
                            <tr class="gelap">
                              <td colspan="3">Keterangan</td>
                              <td>: <?=$rekanan_pengalaman->getField("KONTRAK_KETERANGAN")?></td>
                            </tr>
                            <tr>
                              <td colspan="4">&nbsp;</td>
                            </tr>
                            <?php
                            if($rekanan_pengalaman->getField("KONTRAK_STATUS") == 1) { ?>
                            <tr class="gelap">
                              <td colspan="3">Tanggal Selesai</td>
                              <td>: <?=getFormattedDate($rekanan_pengalaman->getField("BA_TANGGAL"))?></td>
                            </tr>
                            <?php
                            } else { ?>
                            <tr class="gelap">
                              <td colspan="3">Tanggal Progress</td>
                              <td>: <?=getFormattedDate($rekanan_pengalaman->getField("BA_TANGGAL"))?></td>
                            </tr>

                            <?php
                            } ?>
                          </table>
                        </td>
                      </tr>
                      <?php $i++;
                          }
                        }
                        else
                        {
                        ?>
                          <tr>
                              <td colspan="8">. : : Data belum ada : : .</td>
                          </tr>
                      <?php }?>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>


        </div>
      </div>
    </div>
  </div>
</div>

<script language="JavaScript" src="<?= base_url() ?>jslib/displayElement.js"></script>
