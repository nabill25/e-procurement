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
$this->load->model("RekananSaham");
$this->load->model("RekananPajak");
$this->load->model("RekananNeraca");
include_once("functions/string.func.php");
include_once("functions/date.func.php");
include_once("functions/default.func.php");

$rekanan = new Rekanan();
$rekanan_saham  = new RekananSaham();
$rekanan_pkp  = new Rekanan(); // tipe ?
$rekanan_spt  = new RekananPajak(); // tipe 1
$rekanan_pph  = new RekananPajak(); // tipe 2
$rekanan_ppn  = new RekananPajak(); // tipe 3
$rekanan_neraca = new RekananNeraca();

// $rekanan->selectByParams(array("REKANAN_ID"=>$this->ID),-1,-1);
// $rekanan->firstRow();

$allRecord_S = $rekanan_saham->getCountByParams(array("STATUS"=>1, "REKANAN_ID"=>$this->ID));
$rekanan_saham->selectByParams(array("STATUS"=>1, "REKANAN_ID"=>$this->ID), -1, -1);

$rekanan_pkp->selectByParams(array("A.REKANAN_ID"=>$this->ID), -1, -1);
$rekanan_pkp->firstRow();
$reqNoSuratPKP = $rekanan_pkp->getField("PKP");
$reqTanggalPKP = getFormattedDateJson($rekanan_pkp->getField("PKP_TANGGAL"));
$reqNPWP = $rekanan_pkp->getField("NPWP");

$allRecord_SPT = $rekanan_spt->getCountByParams(array("TIPE"=>1, "REKANAN_ID"=>$this->ID));
$rekanan_spt->selectByParams(array("TIPE"=>1, "REKANAN_ID"=>$this->ID), -1, -1, "", " ORDER BY TAHUN ASC ");

$allRecord_PPH = $rekanan_pph->getCountByParams(array("TIPE"=>2, "REKANAN_ID"=>$this->ID));
$rekanan_pph->selectByParams(array("TIPE"=>2, "REKANAN_ID"=>$this->ID), -1, -1);

$allRecord_PPN = $rekanan_ppn->getCountByParams(array("TIPE"=>3, "REKANAN_ID"=>$this->ID));
$rekanan_ppn->selectByParams(array("TIPE"=>3, "REKANAN_ID"=>$this->ID), -1, -1);
//echo $rekanan_ppn->query;exit;
if($reqTahunNeraca == ""){
  $reqTahunNeraca = date("Y");
}
$rekanan_neraca->selectByParams(array("REKANAN_ID"=>$this->ID), -1, -1);
$rekanan_neraca->firstRow();
$reqTahunNeraca = $rekanan_neraca->getField("TAHUN");
$reqModalNeraca = numberToIna($rekanan_neraca->getField("MODAL"));
$reqAuditNamaNeraca = $rekanan_neraca->getField("AUDIT_NAMA");
$reqAuditNomorNeraca = $rekanan_neraca->getField("AUDIT_NOMOR");
$reqAuditTanggalNeraca = getFormattedDateJson($rekanan_neraca->getField("AUDIT_TANGGAL"));
$reqAuditKeteranganNeraca = $rekanan_neraca->getField("AUDIT_KESIMPULAN");

$allrecord_neraca_tahun = $rekanan_neraca->getCountByParamsTahun(array("REKANAN_ID"=>$this->ID));
$rekanan_neraca->selectByParamsTahun(array("REKANAN_ID"=>$this->ID), -1, -1);
?>

<div class="row">
  <div class="col-md-12 col-sm-12">
    <div class="card">
      <div class="card-header card-head-inverse bg-primary">
        <h4 class="card-title text-white">Kepemilikan Saham - <small>Data susunan kepemilikan saham diperlukan jika jenis perusahaan Anda PT atau CV. </small>
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

            if ($this->libsession->cekChecklist('saham') || ($reqStatusUser == '0' && $reqStatusValidasi == '1')) // Check Checlist Verifikator
            {
              if ($this->libsession->cekUrl($this->uri->segment(3, ""))) { ?>
            <div class="badge badge-pill badge-warning">
                <a href="main/index/data_administrasi_kepemilikan_saham_tambah" data-toogle=""><span class="fa fa-plus text-white icon-line-height" data-toggle="tooltip" data-placement="top" title="Tambah Data"></span> Tambah</a>
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
          <div class="table-responsive">
            <table class="table table-bordered mb-0">
              <tbody>
                <tr class="judul-kolom">
                  <th>No</th>
                  <th>Kepemilikan</th>
                  <th>Pemegang Saham</th>
                  <th>No. KTP</th>
                  <th>No. NPWP</th>
                  <th>Alamat</th>
                  <th>Persentase</th>
                  <th>Nominal Saham</th>
                  <!-- <th width="10%" style="text-align:center">File KTP/NPWP <br>atau Kepemilikan Saham</th> -->
                  <?php
                  if (array_intersect($this->libsession->cekStatusValidasiRekanan(), $arrStatusValidasi)) {
                    if ($this->libsession->cekChecklist('saham') || ($reqStatusUser == '0' && $reqStatusValidasi == '1')) // Check Checlist Verifikator
                    { ?>
                  <th class="text-center" width="9%">Aksi</th>
                  <?php
                    }
                  } ?>
                </tr>
                <?php if($allRecord_S > 0){
                    $i = 0;
                    while($rekanan_saham->nextRow()){
                      if($i%2 == 0)   $css = 'gelap';
                      else      $css = 'terang';
                  ?>
                 <tr>
                   <td><?=$i+1?></td>
                   <td><?=$rekanan_saham->getField("KEPEMILIKAN")?></td>
                   <td>
                     <?=$rekanan_saham->getField("NAMA")?>
                     <?php
                     if ($rekanan_saham->getField("JENIS_KELAMIN") == 'L') {
                       echo '<sup>Laki-Laki</sup>';
                     } else {
                       echo '<sup>Perempuan</sup>';
                     }
                    ?>
                   </td>
                   <td>
                    <?php
                    if ($rekanan_saham->getField("KEPEMILIKAN") == 'Instansi') {
                     echo '-';
                    } else {
                      echo $rekanan_saham->getField("KTP");
                    }
                    ?>
                   </td>
                   <td>
                    <?php
                    if ($rekanan_saham->getField("KEPEMILIKAN") == 'Instansi') {
                     echo '-';
                    } else {
                      echo $rekanan_saham->getField("NPWP");
                    }
                    ?>
                   </td>
                   <td><?=$rekanan_saham->getField("ALAMAT")?></td>
                   <td><?=$rekanan_saham->getField("JUMLAH_SAHAM")?>%</td>
                   <td><?= str_replace(",-","",currencyToPage($rekanan_saham->getField("NOMINAL_SAHAM")))?></td>
                   <!-- <td> -->
                    <?php
                    // if ($rekanan_saham->getField("PATH_FILE")) {
                    //   if (file_exists('uploads/kepemilikan_saham/'.$rekanan_saham->getField("PATH_FILE"))) {
                      ?>
                        <!-- <a href="<?php // echo base_url('uploads/kepemilikan_saham/').$rekanan_saham->getField("PATH_FILE") ?>" class="badge badge-primary" target="_blank"><span class="fa fa-download"></span> Download file</a> -->
                    <?php
                     //  }
                     // } else {
                     //  echo "-";
                     // } 
                     ?>
                  <!-- </td> -->

                    <?php
                    if (array_intersect($this->libsession->cekStatusValidasiRekanan(), $arrStatusValidasi)) {
                      if ($this->libsession->cekChecklist('saham') || ($reqStatusUser == '0' && $reqStatusValidasi == '1')) // Check Checlist Verifikator
                      {
                        if ($this->libsession->cekUrl($this->uri->segment(3, ""))) { ?>
                   <td class="text-center">
                      <a href="main/index/data_administrasi_kepemilikan_saham_tambah/?reqSahamId=<?=$rekanan_saham->getField("REKANAN_SAHAM_ID")?>" class="btn-aksi">
                        <?= ICON_EDIT ?>
                      </a>
                      <a onClick="deleteData('rekanan_saham_json/delete/', '<?=$rekanan_saham->getField("REKANAN_SAHAM_ID")?>')" class="btn-aksi">
                        <?= ICON_DELETE ?>
                      </a>
                    </td>
                    <?php
                        }
                      }
                    } ?>
                </tr>
                <?php $i++;}}else{?>
                <tr class="">
                    <td colspan="9">. : : <?=translate("Data belum ada", "no data found")?> : : .</td>
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
