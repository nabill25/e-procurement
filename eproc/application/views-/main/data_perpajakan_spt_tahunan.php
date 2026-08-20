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
$adaKelengkapanData = $user_login->getCountByParams(array("USER_LOGIN_ID"=> $this->USER_LOGIN_ID, "USER_STATUS|| IN " => "(0,2)"));

$this->load->library("kauth");  $userLogin = new kauth();
$this->load->model("Rekanan");
$this->load->model("RekananSaham");
$this->load->model("RekananPajak");
$this->load->model("RekananNeraca");
include_once("functions/string.func.php");
include_once("functions/date.func.php");
include_once("functions/default.func.php");

$rekanan = new Rekanan();
$rekanan_spt  = new RekananPajak(); // tipe 1

// $rekanan->selectByParams(array("REKANAN_ID"=>$this->ID),-1,-1);
// $rekanan->firstRow();


$allRecord_SPT = $rekanan_spt->getCountByParams(array("TIPE"=>1, "REKANAN_ID"=>$this->ID));
$rekanan_spt->selectByParams(array("TIPE"=>1, "REKANAN_ID"=>$this->ID), -1, -1, "", " ORDER BY TAHUN ASC ");

?>

<div class="row">
  <div class="col-md-12 col-sm-12">
    <div class="card">
      <div class="card-header card-head-inverse bg-primary">
        <h4 class="card-title text-white">SPT Tahunan
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

            if ($this->libsession->cekChecklist('spt_tahunan') || ($reqStatusUser == '0' && $reqStatusValidasi == '1')) // Check Checlist Verifikator
            {
              if ($this->libsession->cekUrl($this->uri->segment(3, ""))) { ?>
            <div class="badge badge-pill badge-warning">
              <a href="main/index/data_perpajakan_spt_tahunan_ubah" data-toogle=""><span class="fa fa-plus text-white icon-line-height" data-toggle="tooltip" data-placement="top" title="Tambah Data"></span> Tambah</a>
            </div>
          <?php
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

          <?php if (array_intersect($this->libsession->cekStatusValidasiRekanan(), $arrStatusValidasi)) { } else { echo $this->libsession->cekStatusValidasiRekananStr();  } ?>

          <table class="table table-bordered mb-0">
            <tbody>
              <tr class="judul-kolom">
                <th width="10">Tahun</th>
                <th>Nomor Tanda Terima Elektronik</th>
                <th>Tanggal Penyampaian</th>
                <th width="10%">File SPT dan Bukti Lapor</th>
                <?php
                if (array_intersect($this->libsession->cekStatusValidasiRekanan(), $arrStatusValidasi)) {
                  if ($this->libsession->cekChecklist('spt_tahunan') || ($reqStatusUser == '0' && $reqStatusValidasi == '1')) // Check Checlist Verifikator
                  {?>
                <th class="text-center" width="9%">Aksi</th>
                <?php
                  }
                } ?>
              </tr>
              <?php if($allRecord_SPT > 0){
                  $i = 0;
                  while($rekanan_spt->nextRow()){ 
                ?>
                <tr>
                  <td><?=$rekanan_spt->getField("TAHUN")?></td>
                  <td><?=$rekanan_spt->getField("NOMOR")?></td>
                  <td><?=getFormattedDateJson($rekanan_spt->getField("TANGGAL"))?></td>
                  <td><a href="<?= base_url('uploads/spt').'/'.$rekanan_spt->getField("PATH_FILE") ?>" target="_blank" class="badge badge-primary"><span class="fa fa-download"></span> Download</a></td>
                  <?php
                  if (array_intersect($this->libsession->cekStatusValidasiRekanan(), $arrStatusValidasi)) {
                    if ($this->libsession->cekChecklist('spt_tahunan') || ($reqStatusUser == '0' && $reqStatusValidasi == '1')) // Check Checlist Verifikator
                    {
                      if ($this->libsession->cekUrl($this->uri->segment(3, ""))) { ?>
                  <td class="text-center">
                    <a class="btn-aksi" href="main/index/data_perpajakan_spt_tahunan_ubah/?reqRekananPajakId=<?=$rekanan_spt->getField('REKANAN_PAJAK_ID')?>">
                      <?= ICON_EDIT ?>
                    </a>
                    <a class="btn-aksi" onClick="deleteData('rekanan_pajak_json/delete_spt/', '<?=$rekanan_spt->getField("REKANAN_PAJAK_ID")?>')">
                      <?= ICON_DELETE ?>
                    </a>
                  </td>
                  <?php
                    }
                   }
                  } ?>
                    </tr>
                    <?php $i++;}}else{ 
                    ?>
                    <tr>
                      <td colspan="5">. : : <?=translate("Data belum ada", "no data found")?> : : .</td>
                    </tr>
                    <?php }?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>
