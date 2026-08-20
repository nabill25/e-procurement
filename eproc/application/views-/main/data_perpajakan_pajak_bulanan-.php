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

$rekanan_pph  = new RekananPajak(); // tipe 2
$rekanan_ppn  = new RekananPajak(); // tipe 3


$rekanan->selectByParams(array("REKANAN_ID"=>$this->ID),-1,-1);
$rekanan->firstRow();

$tahun= date("Y");

$reqTahunPajak = $this->input->get("reqTahunPajak");

if($reqTahunPajak == ""){ 
  $reqTahunPajak = $tahun;
}

$allRecord_PPH = $rekanan_pph->getCountByParams(array("TIPE"=>2, "REKANAN_ID"=>$this->ID));
$rekanan_pph->selectByParams(array("TIPE"=>2, "REKANAN_ID"=>$this->ID, "TAHUN"=>$reqTahunPajak), -1, -1);

$allRecord_PPN = $rekanan_ppn->getCountByParams(array("TIPE"=>3, "REKANAN_ID"=>$this->ID));
$rekanan_ppn->selectByParams(array("TIPE"=>3, "REKANAN_ID"=>$this->ID, "TAHUN"=>$reqTahunPajak), -1, -1);

?> 

<div class="row"> 
  <div class="col-md-12 col-sm-12">
    <div class="card">
      <div class="card-header card-head-inverse bg-primary">
        <h4 class="card-title text-white">Laporan Pajak Bulanan(PPH/PPN) 
          <?php 
          $arrStatusValidasi = array('0','10');
          if (array_intersect($this->libsession->cekStatusValidasiRekanan(), $arrStatusValidasi)) {
           if ($this->libsession->cekUrl($this->uri->segment(3, ""))) { ?>
            <div class="badge badge-pill badge-warning">
              <a onClick="document.location.href='main/index/data_perpajakan_pajak_bulanan_tambah/?reqTahunPajak='+$('#reqTahunPajak').val();"><span class="fa fa-plus text-white icon-line-height" data-toggle="tooltip" data-placement="top" title="Tambah Data"></span> Tambah</a>
            </div>
          <?php 
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

          <div class="row">
            <div class="form-group col-md-12 mb-2">
              <label>Tahun Pajak</label>
              <select name="reqTahunPajak" id="reqTahunPajak" onChange="document.location.href='main/index/data_perpajakan_pajak_bulanan/?reqTahunPajak='+this.value" class="form-control" style="width: 150px !important">
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
          </div>
          <div class="card mb-1 border-blue border-darken-1">
            <div class="card-content">
              <div class="p-1">
                <div class="alert bg-primary alert-icon-left alert-arrow-left alert-dismissible mb-2" role="alert">
                  <span class="alert-icon"><i class="fa fa-th"></i></span>  <strong>Laporan Pajak Bulanan(PPH)</strong>  
                </div> 
                <div class="table-responsive">
                  <table class="border-double table mb-0">
                    <tbody>
                      <tr class="judul-kolom">
                        <th>Bulan</th>
                        <th>Nomor</th>
                        <th>Tanggal</th>
                        <th>File PPH</th>
                      </tr>
                      <?php  
                      if ($rekanan_pph->countRow() == 0) {
                        echo '<tr class="<?=$css?>">
                          <td colspan="3">. : : Data PPH belum ada : : .</td>
                        </tr>';
                      } else {
                      if($allRecord_PPH > 0){
                        $i = 0;
                        while($rekanan_pph->nextRow()){
                      ?>  
                      <tr>
                           <td><?= $rekanan_pph->getField("BULAN") ? getNameMonth($rekanan_pph->getField("BULAN")) : '-'?></td>
                           <td><?= $rekanan_pph->getField("NOMOR") ? $rekanan_pph->getField("NOMOR") : '-'?></td>
                           <td><?= $rekanan_pph->getField("TANGGAL") ? getFormattedDateJson($rekanan_pph->getField("TANGGAL")) : '-'?></td>
                           <?php 
                           if ($rekanan_pph->getField("PATH_FILE")) {
                            echo '<td><a target="_blank" href="'.base_url('uploads/ppn_pph/').$rekanan_pph->getField("PATH_FILE").'" class="badge badge-primary">Download file</a></td>';
                           } else {
                            echo '<td><span class="badge badge-danger">Belum upload</span></td>';
                           }
                            ?>
                      </tr>
                      <?php $i++;}}else{
                        ?>
                        <tr class="<?=$css?>">
                          <td colspan="3">. : : Data PPH belum ada : : .</td>
                        </tr>
                        <?php } ?>
                      <?php
                      }?>
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
                  <span class="alert-icon"><i class="fa fa-th"></i></span>  <strong>Laporan Pajak Bulanan(PPN)</strong>  
                </div> 
                <div class="table-responsive">
                  <table class="border-double table mb-0"> 
                    <tbody>
                      <tr class="judul-kolom">
                        <th>Bulan</th>
                        <th>Nomor</th>
                        <th>Tanggal</th>
                        <th>File PPN</th>
                      </tr>
                      <?php  
                      if ($rekanan_pph->countRow() == 0) {
                        echo '<tr class="<?=$css?>">
                          <td colspan="3">. : : Data PPN belum ada : : .</td>
                        </tr>';
                      } else {

                      if($allRecord_PPN > 0){
                        $i = 0;
                        while($rekanan_ppn->nextRow()){
                      ?>
                       <tr >
                           <td><?= $rekanan_ppn->getField("BULAN") ? getNameMonth($rekanan_ppn->getField("BULAN")) : '-'?></td>
                           <td><?= $rekanan_ppn->getField("NOMOR") ? $rekanan_ppn->getField("NOMOR") : '-'?></td>
                           <td><?= $rekanan_ppn->getField("TANGGAL") ? getFormattedDateJson($rekanan_ppn->getField("TANGGAL")) : '-'?></td>
                           <?php 
                           if ($rekanan_ppn->getField("PATH_FILE")) {
                            echo '<td><a target="_blank" href="'.base_url('uploads/ppn_pph/').$rekanan_ppn->getField("PATH_FILE").'" class="badge badge-primary">Download file</a></td>';
                           } else {
                            echo '<td><span class="badge badge-danger">Belum upload</span></td>';
                           }
                            ?>
                      </tr> 
                      <?php $i++;}}else{
                        ?>
                        <tr class="<?=$css?>">
                          <td colspan="3">. : : Data PPN belum ada : : .</td>
                        </tr>
                        <?php } ?>
                      <?php
                      }?>
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