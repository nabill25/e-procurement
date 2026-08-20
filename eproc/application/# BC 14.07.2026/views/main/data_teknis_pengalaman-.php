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
//$this->load->library("rekananijinusahainfo");  $ijin_usaha_tanggal_berakhir = new rekananijinusahainfo();
include_once("functions/string.func.php");
include_once("functions/date.func.php");
include_once("functions/default.func.php");

/* create objects */
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

$allRecord_K = $rekanan_pengalaman->getCountByParams(array("REKANAN_ID"=>$this->ID,"KONTRAK_STATUS"=>1)," ");
$rekanan_pengalaman->selectByParams(array("REKANAN_ID"=>$this->ID,"KONTRAK_STATUS"=>1), -1, -1," ");

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
                  <span class="alert-icon"><i class="fa fa-th"></i></span>  <strong>Pekerjaan Selesai</strong>
                  <?php
                  if (array_intersect($this->libsession->cekStatusValidasiRekanan(), $arrStatusValidasi)) {
                   if ($this->libsession->cekUrl($this->uri->segment(3, ""))) { ?>
                    <div class="badge badge-pill badge-warning">
                      <a href="main/index/data_teknis_pengalaman_selesai_tambah"> <span class="fa fa-plus text-white icon-line-height" data-toggle="tooltip" data-placement="top" title="Tambah Pekerjaan Selesai"></span> Tambah</a>
                    </div>
                  <?php
                   }
                  } ?>
                </div>
                <div class="table-responsive">
                  <table class="border-double table mb-0">
                    <tbody>
                      <tr class="judul-kolom">
                        <th width="5%">No</th>
                        <th>Nama Pekerjaan</th>
                        <th>Bidang Pekerjaan</th>
                        <th>Lokasi</th>
                        <th>File Kontrak</th>
                        <?php
                        if (array_intersect($this->libsession->cekStatusValidasiRekanan(), $arrStatusValidasi)) {
                         if ($this->libsession->cekUrl($this->uri->segment(3, ""))) { ?>
                        <th width="5%">Aksi</th>
                        <?php
                         }
                        } ?>
                      </tr>
                      <?php if($allRecord_K > 0){
                        $i = 0;
                        while($rekanan_pengalaman->nextRow()){
                        if($i % 2 == 0) $css = "gelap";
                        else      $css = "terang";
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
                         <?php
                        if (array_intersect($this->libsession->cekStatusValidasiRekanan(), $arrStatusValidasi)) {
                         if ($this->libsession->cekUrl($this->uri->segment(3, ""))) { ?>
                        <td>
                          <a class="btn-aksi" href="main/index/data_teknis_pengalaman_selesai_tambah/?reqPengalamanId=<?=$rekanan_pengalaman->getField("REKANAN_PENGALAMAN_ID")?>">
                            <i class="fa fa-pencil" aria-hidden="true"></i>
                          </a>
                          <a class="btn-aksi" onClick="deleteData('rekanan_pengalaman_json/delete/', '<?=$rekanan_pengalaman->getField("REKANAN_PENGALAMAN_ID")?>')">
                            <i class="fa fa-trash" aria-hidden="true"></i>
                          </a>
                        </td>
                        <?php
                         }
                        } ?>
                      </tr>
                      <tr id="reqDetil<?=$i?>" style="display:none">
                        <td colspan="6">
                          <table width="100%" border="0" cellpadding="2" cellspacing="1" class="table table-responsive table-bordered">
                            <tr class="judul-kolom2">
                              <td colspan="4">Pemberi Tugas</td>
                            </tr>
                            <tr class="gelap">
                              <td colspan="2">Nama</td>
                              <td width="3%">:</td>
                              <td width="76%"><?=$rekanan_pengalaman->getField("PEMBERI_TUGAS")?></td>
                            </tr>
                            <tr class="terang">
                              <td colspan="2">Alamat</td>
                              <td>:</td>
                              <td><?=$rekanan_pengalaman->getField("PEMBERI_TUGAS_ALAMAT")?></td>
                            </tr>
                            <tr>
                              <td colspan="4">&nbsp;</td>
                            </tr>
                            <tr class="judul-kolom2">
                              <td colspan="4">Kontrak</td>
                            </tr>
                            <tr class="gelap">
                              <td colspan="2">No</td>
                              <td>:</td>
                              <td><?=$rekanan_pengalaman->getField("KONTRAK_NOMOR")?></td>
                            </tr>
                            <tr class="terang">
                              <td colspan="2">Tanggal</td>
                              <td>:</td>
                              <td><?=getFormattedDate($rekanan_pengalaman->getField("KONTRAK_TANGGAL"))?></td>
                            </tr>
                            <tr class="gelap">
                              <td colspan="2">Nilai</td>
                              <td>:</td>
                              <td><?=currencyToPage($rekanan_pengalaman->getField("KONTRAK_NILAI"))?></td>
                            </tr>
                            <tr>
                              <td colspan="4">&nbsp;</td>
                            </tr>
                            <tr class="judul-kolom2">
                              <td colspan="4">Tanggal Selesai</td>
                            </tr>
                            <tr class="gelap">
                              <td colspan="2">Penyerahan</td>
                              <td>:</td>
                              <td><?=getFormattedDate($rekanan_pengalaman->getField("BA_TANGGAL"))?></td>
                            </tr>
                          </table>
                        </td>
                      </tr>
                      <?php $i++;
                          }
                        }
                        else
                        {
                        if($i % 2 == 0) $css = "gelap";
                        else      $css = "terang";
                        ?>
                          <tr class="<?=$css?>">
                              <td colspan="5">. : : Data belum ada : : .</td>
                          </tr>
                      <?php }?>
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
                  <span class="alert-icon"><i class="fa fa-th"></i></span>  <strong>Pekerjaan Dalam Progress</strong>
                  <?php
                  if (array_intersect($this->libsession->cekStatusValidasiRekanan(), $arrStatusValidasi)) {
                   if ($this->libsession->cekUrl($this->uri->segment(3, ""))) { ?>
                    <div class="badge badge-pill badge-warning">
                      <a href="main/index/data_teknis_pengalaman_progress_tambah"> <span class="fa fa-plus text-white icon-line-height" data-toggle="tooltip" data-placement="top" title="Tambah Pekerjaan Dalam Progress"></span>  Tambah</a>
                    </div>
                  <?php
                   }
                  } ?>
                </div>
                <div class="table-responsive">
                  <table class="border-double table mb-0">
                    <tbody>
                      <tr class="judul-kolom">
                        <th width="5%">No</th>
                        <th>Nama Pekerjaan</th>
                        <th>Bidang Pekerjaan</th>
                        <th>%</th>
                        <th>Lokasi</th>
                        <th>File Kontrak</th>
                        <?php
                        if (array_intersect($this->libsession->cekStatusValidasiRekanan(), $arrStatusValidasi)) {
                         if ($this->libsession->cekUrl($this->uri->segment(3, ""))) { ?>
                        <th>Aksi</th>
                        <?php
                         }
                        } ?>
                      </tr>
                       <?php if($allRecord_progress > 0)
                         {
                          $i = 0;
                          while($rekanan_pengalaman_progress->nextRow())
                          {
                          if($i % 2 == 0) $css = "gelap";
                          else      $css = "terang";
                        ?>
                      <tr >
                        <td><?=$i+1?></td>
                        <td><a class="taut" onclick="displayElement('reqDetilProgress<?=$i?>')" style="cursor:pointer"
                              id="rekanan<?=$i?>"><span class="badge badge-primary"> <small>Lihat detil</small></span> <?=$rekanan_pengalaman_progress->getField("NAMA")?></a></td>
                       <input type="hidden" id="valTem<?=$i?>"></td>
                       <td><small><?=$rekanan_pengalaman_progress->getField("PENGALAMAN_BIDANG")?></small></td>
                       <td><?=$rekanan_pengalaman_progress->getField("PROGRESS")?></td>
                       <td><?=$rekanan_pengalaman_progress->getField("LOKASI")?></td>
                       <td>
                          <?php
                          if ($rekanan_pengalaman_progress->getField("NAMA_FILE")) {
                             echo '<a href="'.base_url('uploads/pengalaman/').$rekanan_pengalaman_progress->getField("PATH_FILE").'" class="badge badge-primary" target="_blank">Download file</a>';
                           } ?>
                        </td>
                       <?php
                        if (array_intersect($this->libsession->cekStatusValidasiRekanan(), $arrStatusValidasi)) {
                         if ($this->libsession->cekUrl($this->uri->segment(3, ""))) { ?>
                       <td><a class="btn-aksi" href="main/index/data_teknis_pengalaman_progress_tambah/?reqPengalamanId=<?=$rekanan_pengalaman_progress->getField("REKANAN_PENGALAMAN_ID")?>">
                          <i class="fa fa-pencil" aria-hidden="true"></i>
                          </a>
                          <a class="btn-aksi" onClick="deleteData('rekanan_pengalaman_json/delete/', '<?=$rekanan_pengalaman_progress->getField("REKANAN_PENGALAMAN_ID")?>')">
                              <i class="fa fa-trash" aria-hidden="true"></i>
                          </a>
                       </td>
                       <?php
                        }
                       } ?>
                      </tr>
                      <tr id="reqDetilProgress<?=$i?>" style="display:none">
                        <td colspan="7">
                          <table width="100%" border="0" cellpadding="2" cellspacing="1" class="table table-responsive table-bordered">
                            <tr class="judul-kolom2">
                              <td colspan="4">Pemberi Tugas</td>
                            </tr>
                            <tr class="gelap">
                              <td colspan="2">Nama</td>
                              <td width="3%">:</td>
                              <td width="76%"><?=$rekanan_pengalaman_progress->getField("PEMBERI_TUGAS")?></td>
                            </tr>
                            <tr class="terang">
                              <td colspan="2">Alamat</td>
                              <td>:</td>
                              <td><?=$rekanan_pengalaman_progress->getField("PEMBERI_TUGAS_ALAMAT")?></td>
                            </tr>
                            <tr>
                              <td colspan="4">&nbsp;</td>
                            </tr>
                            <tr class="judul-kolom2">
                              <td colspan="4">Kontrak</td>
                            </tr>
                            <tr class="gelap">
                              <td colspan="2">No</td>
                              <td>:</td>
                              <td><?=$rekanan_pengalaman_progress->getField("KONTRAK_NOMOR")?></td>
                            </tr>
                            <tr class="terang">
                              <td colspan="2">Tanggal</td>
                              <td>:</td>
                              <td><?=$rekanan_pengalaman_progress->getField("KONTRAK_TANGGAL")?></td>
                            </tr>
                            <tr class="gelap">
                              <td colspan="2">Nilai</td>
                              <td>:</td>
                              <td><?=currencyToPage($rekanan_pengalaman_progress->getField("KONTRAK_NILAI"))?></td>
                            </tr>
                            <tr>
                              <td colspan="4">&nbsp;</td>
                            </tr>
                            <tr class="judul-kolom2">
                              <td colspan="4">Laporan Progress</td>
                            </tr>
                            <tr class="gelap">
                              <td colspan="2">Tanggal Progress</td>
                              <td>:</td>
                              <td><?=getFormattedDate($rekanan_pengalaman_progress->getField("PROGRESS_TANGGAL"))?></td>
                            </tr>
                            <tr class="terang">
                              <td colspan="2">(%) Progress</td>
                              <td>:</td>
                              <td><?=$rekanan_pengalaman_progress->getField("PROGRESS")?></td>
                            </tr>
                          </table>
                        </td>
                      </tr>
                        <?php
                          $i++;
                          }
                        }
                        else
                        {
                        ?>
                          <tr>
                            <td colspan="7">. : : Data belum ada : : .</td>
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
    </div>
  </div>
</div>

<script language="JavaScript" src="<?= base_url() ?>jslib/displayElement.js"></script>
