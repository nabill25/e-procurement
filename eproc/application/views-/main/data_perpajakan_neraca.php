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
// tipe 3
$rekanan_neraca = new RekananNeraca();

// $rekanan->selectByParams(array("REKANAN_ID"=>$this->ID),-1,-1);
// $rekanan->firstRow();

//echo $rekanan_ppn->query;exit;
$tahun= date("Y");
$reqTahunNeraca = $this->input->get("reqTahunNeraca");

if($reqTahunNeraca == ""){
  $reqTahunNeraca = $tahun;
}

$rekanan_neraca->selectByParams(array("REKANAN_ID"=>$this->ID, "TAHUN"=>$reqTahunNeraca), -1, -1);
$rekanan_neraca->firstRow();
//echo $rekanan_neraca->query;exit;
$reqModalNeraca = numberToIna($rekanan_neraca->getField("MODAL"));
$reqAuditNamaNeraca = $rekanan_neraca->getField("AUDIT_NAMA");
$reqAuditNomorNeraca = $rekanan_neraca->getField("AUDIT_NOMOR");
$reqAuditTanggalNeraca = getFormattedDateJson($rekanan_neraca->getField("AUDIT_TANGGAL"));
$reqAuditKeteranganNeraca = $rekanan_neraca->getField("AUDIT_KESIMPULAN");
$reqLinkFileTemp = $rekanan_neraca->getField("NAMA_FILE");
$tempFileTemp = $rekanan_neraca->getField("PATH_FILE");
$reqLinkFileTemp2 = $rekanan_neraca->getField("NAMA_FILE2");
$tempFileTemp2 = $rekanan_neraca->getField("PATH_FILE2");

$allrecord_neraca_tahun = $rekanan_neraca->getCountByParamsTahun(array("REKANAN_ID"=>$this->ID, "TAHUN"=>$reqTahunNeraca));
$rekanan_neraca->selectByParamsTahun(array("REKANAN_ID"=>$this->ID, "TAHUN"=>$reqTahunNeraca), -1, -1);

?>

<div class="row">
  <div class="col-md-12 col-sm-12">
    <div class="card">
      <div class="card-header card-head-inverse bg-primary">
        <h4 class="card-title text-white">Neraca
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

            if ($this->libsession->cekChecklist('neraca') || ($reqStatusUser == '0' && $reqStatusValidasi == '1')) // Check Checlist Verifikator
            {
              if ($this->libsession->cekUrl($this->uri->segment(3, ""))) { ?>
            <div class="badge badge-pill badge-warning">
              <a onClick="document.location.href='main/index/data_perpajakan_neraca_tambah/?reqTahunNeraca='+$('#reqTahunNeraca').val();"><span class="fa fa-plus text-white icon-line-height" data-toggle="tooltip" data-placement="top" title="Tambah Data"></span> Tambah</a>
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
      <form id="ff" class="easyui-form form-horizontal" method="post" data-options="novalidate:true" enctype="multipart/form-data">
      <div class="card-content collapse show border-info border-darken-2">
        <div class="card-body">

        <?php if (array_intersect($this->libsession->cekStatusValidasiRekanan(), $arrStatusValidasi)) { } else { echo $this->libsession->cekStatusValidasiRekananStr();  } ?>

          <div class="row">
            <div class="form-group col-md-12 mb-2">
              <label>Tahun pajak</label>
                <select name="reqTahunNeraca" id="reqTahunNeraca" onChange="document.location.href='main/index/data_perpajakan_neraca/?reqTahunNeraca='+this.value" class="form-control" style="width: 150px !important">
                 <?php
                  for($i=date('Y')-2;$i<=date('Y')+1; $i++)
                  {
                  ?>
                    <option value="<?=$i?>" <?php if($i == $reqTahunNeraca) { ?> selected="selected" <?php } ?>><?=$i?></option>
                  <?php
                  }
                  ?>
                </select>
            </div>
          </div>
          <div class="card mb-1 border-blue border-darken-1">
            <div class="card-content">
              <div class="p-1">
                <div class="alert bg-primary alert-icon-left alert-arrow-left alert-dismissible mb-2" role="alert">
                  <span class="alert-icon"><i class="fa fa-th"></i></span>  <strong>Neraca</strong>
                </div>
                <div class="table-responsive">
                  <table class="table table-bordered table mb-0">
                    <tbody>
                      <tr>
                        <td style="width: 20%">Modal (kekayaan bersih)</td>
                        <td><?=$reqModalNeraca?> </td>
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
                  <span class="alert-icon"><i class="fa fa-th"></i></span>  <strong>Audit</strong>
                </div>
                <div class="table-responsive">
                  <table class="table table-bordered table mb-0">
                    <tbody>
                      <tr>
                        <td style="width: 20%">KAP</td>
                        <td>: <?=$reqAuditNamaNeraca?> </td>
                      </tr>
                      <tr>
                        <td style="width: 20%">Nomor</td>
                        <td>: <?=$reqAuditNomorNeraca?></td>
                      </tr>
                      <tr>
                        <td style="width: 20%">Tanggal</td>
                        <td>: <?=$reqAuditTanggalNeraca?> </td>
                      </tr>
                      <tr>
                        <td style="width: 20%">Kesimpulan</td>
                        <td>: <?=$reqAuditKeteranganNeraca?> </td>
                      </tr>
                      <tr>
                        <td style="width: 20%">File Neraca / K A P</td>
                        <td>:
                        <?php
                        if($tempFileTemp == "")
                        {}
                        else
                        {
                          echo $reqLinkFileTemp.'<br><a href="'.base_url('uploads/neraca_keuangan/').$tempFileTemp.'" class="badge badge-primary" target="_blank"><span class="fa fa-download"></span> Download file</a>';
                        }
                          ?>
                        </td>
                      </tr>
                      <tr>
                        <td style="width: 20%">File Laba Rugi</td>
                        <td>:
                        <?php
                        if($tempFileTemp2 == "")
                        {}
                        else
                        {
                          echo $reqLinkFileTemp2.'<br><a href="'.base_url('uploads/neraca_keuangan/').$tempFileTemp2.'" class="badge badge-primary" target="_blank"><span class="fa fa-download"></span> Download file</a>';
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
        </div>
      </div>
     </form>
    </div>
  </div>
</div>
