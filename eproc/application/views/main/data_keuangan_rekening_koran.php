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
include_once("functions/string.func.php");
include_once("functions/date.func.php");
include_once("functions/default.func.php");;
$this->load->model("RekananRekeningKoran");
$this->load->library("rekananijinusahainfo");

$ijin_usaha_tanggal_berakhir = new rekananijinusahainfo();

$rekanan = new Rekanan();
$rekanan_tahun_select = new RekananRekeningKoran();
$rekanan_tahun = new RekananRekeningKoran();
$rekanan_koran = new RekananRekeningKoran();
$rekanan_tahun_selectGet = new RekananRekeningKoran();

$reqTahunPajak= $this->input->get('reqTahunPajak') ?: date("Y");
$reqMode = $this->input->post("reqMode");
$reqParamKey = $this->input->post("reqParamKey");

// $rekanan->selectByParams(array("A.REKANAN_ID"=>$this->ID),-1,-1);
// $rekanan->firstRow();

$tahun = date("Y");
$allRecord_select = $rekanan_tahun_select->getCountByParamsTahun(array('REKANAN_ID'=>$this->ID), $statement);

$allRecord_select_tahun = $rekanan_tahun_select->getCountByParamsTahun(array('REKANAN_ID'=>$this->ID, "TAHUN"=>$tahun), $statement);

$rekanan_tahun_select->selectByParamsTahunSelect(array('REKANAN_ID'=>$this->ID), -1, -1, $statement);

if($reqTahunPajak == ""){
  if($allRecord_select_tahun == 0){
    $rekanan_tahun_selectGet->selectByParamsTahunSelect(array('REKANAN_ID'=>$this->ID), -1, -1, $statement);
    $rekanan_tahun_selectGet->firstRow();
    $reqTahunPajak = $rekanan_tahun_selectGet->getField("TAHUN");
  }
  else $reqTahunPajak = $tahun;
}
?>

<div class="row">
  <div class="col-md-12 col-sm-12">
    <div class="card">
      <div class="card-header card-head-inverse bg-primary">
        <h4 class="card-title text-white">Rekening Koran
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

            if ($this->libsession->cekChecklist('rekening_koran') || ($reqStatusUser == '0' && $reqStatusValidasi == '1')) // Check Checlist Verifikator
            {
              if ($this->libsession->cekUrl($this->uri->segment(3, ""))) {
           ?>
            <div class="badge badge-pill badge-warning">
              <a onClick="document.location.href='main/index/data_keuangan_rekening_koran_tambah/?reqTahunPajak='+$('#reqTahunPajak').val();" data-toogle=""><span class="fa fa-plus text-white icon-line-height" data-toggle="tooltip" data-placement="top" title="Tambah Data"></span> Tambah</a>
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

            <div class="col-md-12" style="margin-bottom: 2%">
              Tahun Rekening Koran  :
              <select name="reqTahunPajak" id="reqTahunPajak" onChange="document.location.href='main/index/data_keuangan_rekening_koran/?reqTahunPajak='+this.value" class="form-control" style="width: 150px !important">
               <?php
                for($i=date('Y')-2;$i<=date('Y')+1; $i++)
                {
                ?>
                  <option value="<?=$i?>" <?php if($i == $reqTahunPajak) { ?> selected="selected" <?php } ?>><?=$i?></option>
                <?php
                }
                ?>
              </select>
            </div>
            <table class="table table-bordered mb-0">
              <tbody>
                <tr class="judul-kolom">
                  <th width="10px">No</th>
                  <th>Bank</th>
                  <th>Nomor Rekening</th>
                  <th width="20px">Mata Uang</th>
                  <!-- <th>Nilai</th> -->
                  <!-- <th>Kurs(IDR)</th> -->
                  <!-- <th>Nominal(IDR)</th> -->
                  <th>File Rekening Koran</th>
                  <?php
                  if (array_intersect($this->libsession->cekStatusValidasiRekanan(), $arrStatusValidasi)) {
                    if ($this->libsession->cekUrl($this->uri->segment(3, ""))) {
                   ?>
                  <th class="text-center" width="10%">Aksi</th>
                  <?php
                   } else {
                    echo '<th>#</th>';
                   }
                  } else {
                    echo '<th>#</th>';
                  } ?>
                </tr>
                <?php
                $allRecord = $rekanan_tahun->getCountByParamsTahun(array('REKANAN_ID'=>$this->ID, "TAHUN"=>$reqTahunPajak), $statement);
                $rekanan_tahun->selectByParamsTahun(array('REKANAN_ID'=>$this->ID, "TAHUN"=>$reqTahunPajak), -1, -1, $statement);
                if($allRecord > 0){
                  while($rekanan_tahun->nextRow()){
                ?>
                <tr>
                <td colspan="9"><strong><?=getNameMonth($rekanan_tahun->getField("BULAN"))." ".$rekanan_tahun->getField("TAHUN")?><!--Juli 2011--></strong></td>
                </tr>
                <?php
                $i = 1; $tmpTotal = 0;
                $rekanan_koran->selectByParams(array('REKANAN_ID'=>$this->ID, 'BULAN'=>$rekanan_tahun->getField("BULAN"),'TAHUN'=>$rekanan_tahun->getField("TAHUN")), -1, -1, $statement);
                while($rekanan_koran->nextRow()){
                  $tmpTotal += $rekanan_koran->getField("NOMINAL");
                ?>
                <tr>
                <td><?=$i?></td>
                <td><?=$rekanan_koran->getField("NAMA")?></td>
                <td><?=$rekanan_koran->getField("NOMOR")?></td>
                <td class="text-center" title="INDONESIA RUPIAH"><span title="INDONESIA RUPIAH"><?=$rekanan_koran->getField("MATAUANG")?></span></td>
                <!-- <td title="INDONESIA RUPIAH"><?php // echo numberToIna($rekanan_koran->getField("NILAI"))?></td> -->
                <!-- <td><?php //numberToIna($rekanan_koran->getField("KURS"))?></td> -->
                <!-- <td><?php // echo numberToIna($rekanan_koran->getField("NOMINAL"))?></td> -->
                <td>
                  <?=$rekanan_koran->getField("NAMA_FILE")?><br>
                  <?php echo '<a href="'.base_url('uploads/rekening_koran/').$rekanan_koran->getField("PATH_FILE").'" class="badge badge-primary" target="_blank"><span class="fa fa-download"></span> Download file</a>'; ?>


                  </td>
                <?php
                if (array_intersect($this->libsession->cekStatusValidasiRekanan(), $arrStatusValidasi)) {
                  if ($this->libsession->cekChecklist('rekening_koran') || ($reqStatusUser == '0' && $reqStatusValidasi == '1')) // Check Checlist Verifikator
                  {
                    if ($this->libsession->cekUrl($this->uri->segment(3, ""))) {
                 ?>
                <td class="text-center">
                  <a href="main/index/data_keuangan_rekening_koran_tambah/?reqRekeningKoranId=<?=$rekanan_koran->getField("REKANAN_REKENING_KORAN_ID")?>" class="btn-aksi">
                   <?= ICON_EDIT ?>
                  </a>
                  <a onClick="deleteData('rekanan_rekening_koran_json/delete/', '<?=$rekanan_koran->getField("REKANAN_REKENING_KORAN_ID")?>')" class="btn-aksi">
                    <?= ICON_DELETE ?>
                  </a>
                </td>
                <?php
                    }
                  }
                } ?>
                </tr>
                <?php $i++;}?>
                <!-- <tr>
                <td colspan="4" align="right"><strong>Total Bulan <?php // echo getNameMonth($rekanan_tahun->getField("BULAN"))." ".$rekanan_tahun->getField("TAHUN")?></strong></td>
                <td class="bghijau"><strong><?php // echo numberToIna($tmpTotal)?></strong></td>
                <td></td>
                </tr>
                <tr>
                <td colspan="7">&nbsp;</td>
                </tr> -->
                <?php }
                }else{
                ?>
                <tr>
                  <td colspan="6">. : : Data belum ada : : .</td>
                </tr>
                <?php }?>
              </tbody>
            </table>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>
