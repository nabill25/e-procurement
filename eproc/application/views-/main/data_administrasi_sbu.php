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

// $rekanan->selectByParams(array("A.REKANAN_ID"=>$this->ID),-1,-1);
// $rekanan->firstRow();

$rekanan_ijin_usaha->selectByParams(array("REKANAN_ID" => $this->ID, "IJIN_USAHA_ID" => 99));
//echo $rekanan_ijin_usaha->query;
//$rekanan_ijin_usaha->firstRow();
$i=0;
while($rekanan_ijin_usaha->nextRow()){
  $reqRekananIjinUsahaId[$i] = $rekanan_ijin_usaha->getField("REKANAN_IJIN_USAHA_ID");
  $reqIjinUsahaId[$i] = $rekanan_ijin_usaha->getField("IJIN_USAHA_ID");
  $reqNomor[$i] = $rekanan_ijin_usaha->getField("NO_IJIN");
  $reqTanggalIjin[$i] = getFormattedDate($rekanan_ijin_usaha->getField("TANGGAL"));
  $reqTanggalBerakhir[$i] = getFormattedDate($rekanan_ijin_usaha->getField("TANGGAL_BERAKHIR"));
  $reqTanggalBerakhirAsli[$i] = $rekanan_ijin_usaha->getField("TANGGAL_BERAKHIR");
  $reqInstansi[$i] = $rekanan_ijin_usaha->getField("INSTANSI");
  $reqBidang[$i] = $rekanan_ijin_usaha->getField("IJIN_USAHA");
  $reqLinkFileTemp[$i]= $rekanan_ijin_usaha->getField("NAMA_FILE");
  $tempFileTemp[$i] = $rekanan_ijin_usaha->getField("PATH_FILE");
  $reqNamaPemegang[$i] = $rekanan_ijin_usaha->getField("NAMA_PEMEGANG");
  $i++;
}

if ($rekanan_ijin_usaha->countRow()) {
if(count($reqIjinUsahaId) > 0)  $valueIjinUsaha = getValueArrayMonth($reqIjinUsahaId);
}
?>

<div class="row">
  <div class="col-md-12 col-sm-12">
    <div class="card">
      <div class="card-header card-head-inverse bg-primary">
        <h4 class="card-title text-white">Sertifikat Badan Usaha Konstruksi
         <?php
          $arrStatusValidasi = array('0','10');
          if (array_intersect($this->libsession->cekStatusValidasiRekanan(), $arrStatusValidasi)) {
            if ($this->libsession->cekUrl($this->uri->segment(3, ""))) {
              if ($rekanan_ijin_usaha->countRow() <= 0) {
            ?>
            <div class="badge badge-pill badge-warning">
              <a href="main/index/data_administrasi_sbu_ubah" data-toogle=""><span class="fa fa-plus text-white icon-line-height" data-toggle="tooltip" data-placement="top" title="Tambah Data"></span> Tambah</a>
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
          <form id="ff" class="easyui-form form-horizontal" method="post" data-options="novalidate:true" enctype="multipart/form-data">
          <?php
          if ($rekanan_ijin_usaha->countRow() == 0 ) {
            echo ". : : Data tidak ada : : .";
          } else
          {
            for($x=0; $x < count($reqRekananIjinUsahaId); $x++){
            $rekanan_bidang_usaha->selectByParamsMonitoring(array("REKANAN_ID" => $this->ID, "IJIN_USAHA_ID"=>$reqIjinUsahaId[$x] /*"REKANAN_BIDANG_USAHA_INFO_ID" => $reqRekananIjinUsahaId[$x]*/));
            //echo $rekanan_bidang_usaha->query;
          ?>

          <?php if (array_intersect($this->libsession->cekStatusValidasiRekanan(), $arrStatusValidasi)) { } else { echo $this->libsession->cekStatusValidasiRekananStr();  }?>

          <div class="card mb-1 border-blue border-darken-1">
            <div class="card-content">
              <div class="p-1">
                <div class="alert bg-primary alert-icon-left alert-arrow-left alert-dismissible mb-2" role="alert">
                  <span class="alert-icon"><i class="fa fa-th"></i></span>  <strong>Sertifikat Badan Usaha - <?=$reqTanggalIjin[$x]?> s/d <?=$reqTanggalBerakhir[$x]?></strong>
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

                    if ($this->libsession->cekChecklist('sbu') || ($reqStatusUser == '0' && $reqStatusValidasi == '1')) // Check Checlist Verifikator
                    {
                      if ($this->libsession->cekUrl($this->uri->segment(3, ""))) {
                   ?>
                    <div class="badge badge-pill badge-warning">
                      <a href="main/index/data_administrasi_sbu_ubah/?reqIjinUsahaId=<?=$reqRekananIjinUsahaId[$x]?>&reqTipe=<?=$reqIjinUsahaId[$x]?>"> <span class="fa fa-pencil text-white icon-line-height" data-toggle="tooltip" data-placement="top" title="Ubah Sertifikat Badan Usaha <?=$tempBidang[$x]?>"></span> Ubah </a>
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
                        <td style="width: 20%">Nomor sertifikat</td>
                        <td>: <?=$reqNomor[$x]?></td>
                      </tr>
                      <tr>
                        <td style="width: 20%">Tanggal sertifikat</td>
                        <td>: <?=$reqTanggalIjin[$x]?> </td>
                      </tr>
                      <tr>
                        <td style="width: 20%">Tanggal berakhir</td>
                        <td>:
                          <?php
                          if (strtotime($reqTanggalBerakhirAsli[$x]) < 1) {
                            echo "-";
                          } else {
                            if (strtotime($reqTanggalBerakhirAsli[$x]) < strtotime(date('Y-m-d'))) {
                              echo $reqTanggalBerakhir[$x]. ' <span class="badge badge-pill badge-danger">Berakhir</span>';
                            } else {
                              echo $reqTanggalBerakhir[$x].'';
                            }
                          }
                          ?>
                        </td>
                      </tr>
                      <tr>
                        <td style="width: 20%">Lembaga Sertifikasi</td>
                        <td>: <?=$reqInstansi[$x]?> </td>
                      </tr> 
                      <tr>
                        <td style="width: 20%">File SBU</td>
                        <td>:
                          <?php
                            $arrFile = explode(";", $tempFileTemp[$x]);
                            for($iFile=0;$iFile<count($arrFile);$iFile++)
                            {
                              echo $reqLinkFileTemp[$iFile].' <br><a href="'.base_url('uploads/ijin_usaha/').$arrFile[$iFile].'" class="badge badge-primary" target="_blank"><span class="fa fa-download"></span> Download file</a>';
                            }
                          ?>
                        </td>
                      </tr>
                    </tbody>
                  </table>
                </div> <br>
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
                        $style="gelap";
                        while($rekanan_bidang_usaha->nextRow())
                        {
                        ?>
                          <tr class="gelap">
                            <td><?=$i?>.</td>
                            <!-- <td><?=$rekanan_bidang_usaha->getField("BIDANG_USAHA_ID")?></td> -->
                            <td><?=$rekanan_bidang_usaha->getField("NAMA")?></td>
                          </tr>
                      <?php
                      $i++;
                      if($style == "gelap")
                        $style = "terang";
                      else
                        $style = "gelap";
                      }
                      ?>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>
          <?php }
          } ?>
        </div>
      </div>
    </div>
  </div>
</div>
