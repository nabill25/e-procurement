<?php
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 * 
 */

$this->libsession->cekSession('blockpenyedia');   

// cek allowed url 
if ($this->libsession->cekUrl($this->uri->segment(3, ""))) {} else { redirect(base_url()); }

/* INCLUDE FILE */
$this->load->library("kauth");  $userLogin = new kauth(); 
$this->load->model("Rekanan");
$this->load->model("RekananPajak");
include_once("functions/string.func.php");
include_once("functions/date.func.php");
include_once("functions/default.func.php");

/* create objects */
$rekanan = new Rekanan();
$rekanan_pph  = new RekananPajak(); // tipe 2
$rekanan_ppn  = new RekananPajak(); // tipe 3
$rekanan_tahun_select = new RekananPajak();
$rekanan_tahun_selectGet = new RekananPajak();

$reqId = $this->ID;
$reqTahunPajak = $this->input->get("reqTahunPajak");

$reqSubmit= httpFilterPost("reqSubmit");
$reqTahunPajak= httpFilterRequest('reqTahunPajak');
$reqBulanPPH = $_POST["reqBulanPPH"];
$reqNomorPPH = $_POST["reqNomorPPH"];
$reqTanggalPPH = $_POST["reqTanggalPPH"];
$reqLinkFilePPH= $_FILES['reqLinkFilePPH'];
$reqLinkFilePPHTemp = $_POST["reqLinkFilePPHTemp"];
$reqLinkFilePPHTempNama = $_POST["reqLinkFilePPHTempNama"];

$reqBulanPPN = $_POST["reqBulanPPN"];
$reqNomorPPN = $_POST["reqNomorPPN"];
$reqTanggalPPN = $_POST["reqTanggalPPN"];
$reqLinkFilePPN= $_FILES['reqLinkFilePPN'];
$reqLinkFilePPNTemp = $_POST["reqLinkFilePPNTemp"];
$reqLinkFilePPNTempNama = $_POST["reqLinkFilePPNTempNama"];

$rekanan->selectByParams(array("REKANAN_ID"=>$this->ID),-1,-1);
$rekanan->firstRow();

$tahun = date("Y");
$allRecord_select = $rekanan_tahun_select->getCountByParamsTahun(array('REKANAN_ID'=>$this->ID, 'TIPE'=>'2'), $statement);

$allRecord_select_tahun = $rekanan_tahun_select->getCountByParamsTahun(array('REKANAN_ID'=>$this->ID, 'TIPE' => '2', "TAHUN"=>$tahun), $statement);

$rekanan_tahun_select->selectByParamsTahunSelect(array('REKANAN_ID'=>$this->ID, 'TIPE' => '2'), -1, -1, $statement);

if($reqTahunPajak == ""){ 
  /*if($allRecord_select_tahun == 0){
    $rekanan_tahun_selectGet->selectByParamsTahunSelect(array('REKANAN_ID'=>$reqId, 'TIPE' => '2'), -1, -1, $statement);
    $rekanan_tahun_selectGet->firstRow();
    $reqTahunPajak = $rekanan_tahun_selectGet->getField("TAHUN");
  }else*/ $reqTahunPajak = $tahun;
}

$allRecord_PPH = $rekanan_pph->getCountByParams(array("TIPE"=>2, "REKANAN_ID"=>$this->ID, "TAHUN"=>$reqTahunPajak));
$rekanan_pph->selectByParamsMonitoring(array("TIPE"=>2, "REKANAN_ID"=>$this->ID, "TAHUN" => $reqTahunPajak), -1, -1);

$i=1;
while($rekanan_pph->nextRow()){
  $arrNomorPPH[$i] = $rekanan_pph->getField('NOMOR');
  $arrTanggalPPH[$i] = dateToPageCheck($rekanan_pph->getField('TANGGAL'));
  $arrLinkFilePPH[$i] = $rekanan_pph->getField('PATH_FILE');
  $arrLinkFilePPHNama[$i] = $rekanan_pph->getField('NAMA_FILE');
  $i++;
}

$allRecord_PPN = $rekanan_ppn->getCountByParams(array("TIPE"=>3, "REKANAN_ID"=>$this->ID, "TAHUN"=>$reqTahunPajak));
$rekanan_ppn->selectByParamsMonitoring(array("TIPE"=>3, "REKANAN_ID"=>$this->ID, "TAHUN" => $reqTahunPajak), -1, -1);

//echo '---'.$allRecord_PPN;
$i=1;
while($rekanan_ppn->nextRow()){
  $arrNomorPPN[$i] = $rekanan_ppn->getField('NOMOR');
  $arrTanggalPPN[$i] = dateToPageCheck($rekanan_ppn->getField('TANGGAL'));
  $arrLinkFilePPN[$i] = $rekanan_ppn->getField('PATH_FILE');
  $arrLinkFilePPNNama[$i] = $rekanan_ppn->getField('NAMA_FILE');
  $i++;
}

?>
<script type="text/javascript">
$(document).ready(function() {
  
  $(function(){
    $('#ff').form({
      url:'rekanan_pajak_json/data_administrasi_keuangan_pajak_tambah',
      onSubmit:function(){
       var v=$(this).form('validate');
        if(v) { 
            showLoad();
            return v;
        } else {
            hideLoad();
            return false;
        }
      },
      success:function(data){
        document.location.href = 'main/index/data_perpajakan_pajak_bulanan/?reqTahunPajak=<?=$reqTahunPajak?>';    
        hideLoad();
      }
    });
    
  });
  
});
</script>

<div class="row"> 
  <div class="col-md-12 col-sm-12">
    <div class="card">
      <div class="card-header card-head-inverse bg-primary">
        <h4 class="card-title text-white">Laporan Pajak Bulanan(PPH/PPN) </h4>
        <a class="heading-elements-toggle"><i class="fa fa-ellipsis-v font-medium-3"></i></a> 
        <div class="heading-elements">
            <ul class="list-inline mb-0">
              <li><a data-action="expand"><i class="ft-maximize"></i></a></li>
              <!-- <li><a data-action="close"><i class="ft-x"></i></a></li> -->
            </ul>
        </div>
      </div>
      
      <form id="ff" class="easyui-form form-horizontal" method="post" novalidate enctype="multipart/form-data">
      <div class="card-content collapse show border-info border-darken-2">
        <div class="card-body">
          <div class="row">
            <div class="form-group col-md-12 mb-2">
              <label>Tahun</label>
              <select name="reqTahunPajak" id="reqTahunPajak" onChange="document.location.href='main/index/data_perpajakan_pajak_bulanan_tambah/?reqTahunPajak='+this.value" class="form-control">
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
                  <span class="alert-icon"><i class="fa fa-th"></i></span>  <strong>Laporan Pajak Bulanan PPH</strong> 
                </div> 
                <div class="table-responsive">
                  <table class="border-double table mb-0">
                    <tbody>
                      <tr class="judul-kolom">
                        <th>Bulan</th>
                        <th>Nomor</th>
                        <th>Tanggal</th>
                        <th>File PPH <?= UPLOAD_PDF_2MB ?></th>
                      </tr>
                      <?php
                      // echo "<pre>"; print_r($arrNomorPPH).'...';
                      for($i=1;$i<=12;$i++)
                      {             
                      ?>

                      <script type="text/javascript">
                        $(document).ready(function() {
                          $('#reqTanggalPPH<?=$i?>').datebox({
                            editable: false
                          });
                        });
                      </script>

                       <tr>
                           <td><?=getNameMonth($i)?><input type="hidden" name="reqBulanPPH[]" value="<?=$i?>"></td>
                           <td><input id="reqNomorPPH<?=$i?>" name="reqNomorPPH[]" class="form-control easyui-validatebox" size="40" maxlength="50" value="<?=$arrNomorPPH[$i]?>" type="text" /></td>
                           <td><input type="text"  class="form-control easyui-datebox" style="width:120px" name="reqTanggalPPH[]" id="reqTanggalPPH<?=$i?>" value="<?=$arrTanggalPPH[$i]?>" /></td>
                           <td><input type="file" id="reqLinkFilePDF" name="reqLinkFilePPH[]" class="easyui-validatebox"  validType="fileType['pdf']" />
                              <input type="hidden" name="reqLinkFilePPHTemp[]" value="<?=$arrLinkFilePPH[$i]?>">
                               <input type="hidden" name="reqLinkFilePPHTempNama[]" value="<?=$arrLinkFilePPHNama[$i]?>">
                               <?php 
                               if ($arrLinkFilePPHNama[$i]) {
                                  echo '<br><span style="font-size:9px;">File : '.$arrLinkFilePPHNama[$i].'</span> <br><a href="'.base_url('uploads/ppn_pph/').$arrLinkFilePPH[$i].'" class="badge badge-primary"><span class="fa fa-download"></span> Download file</a>';
                                } else { echo '<br><span class="badge badge-danger">Belum upload</span>'; } ?>
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
                  <span class="alert-icon"><i class="fa fa-th"></i></span>  <strong>Laporan Pajak Bulanan PPN</strong> 
                </div> 
                <div class="table-responsive">
                  <table class="border-double table mb-0">
                    <tbody>
                      <tr class="judul-kolom">
                        <th>Bulan</th>
                        <th>Nomor</th>
                        <th>Tanggal</th>
                        <th>File PPN <small> (Format file .pdf & Maksimal ukuran file 2MB)</small></th>
                      </tr>
                      <?php
                      for($i=1;$i<=12;$i++)
                      {             
                      ?>
                      <script type="text/javascript">
                        $(document).ready(function() {
                          $('#reqTanggalPPN<?=$i?>').datebox({
                            editable: false
                          });
                        });
                      </script>
                       <tr >
                         <td><?=getNameMonth($i)?><input type="hidden" class="form-control easyui-validatebox" name="reqBulanPPN[]" value="<?=$i?>"></td>
                         <td><input id="reqNomorPPN<?=$i?>" name="reqNomorPPN[]" size="40" maxlength="50" class="form-control easyui-validatebox"  value="<?=$arrNomorPPN[$i]?>"type="text" /></td>
                         <td><input type="text" style="width:120px" class="form-control easyui-datebox" name="reqTanggalPPN[]" id="reqTanggalPPN<?=$i?>" value="<?=$arrTanggalPPN[$i]?>" /></td>
                         <td><input type="file" name="reqLinkFilePPN[]" class="easyui-validatebox"  validType="fileType['pdf']" />
                            <input type="hidden" name="reqLinkFilePPNTemp[]" value="<?=$arrLinkFilePPN[$i]?>">
                            <input type="hidden" name="reqLinkFilePPNTempNama[]" value="<?=$arrLinkFilePPNNama[$i]?>">
                            <!-- <span style="font-size:9px;">temp : <?php //$arrLinkFilePPNNama[$i]?></span> --> 
                            <?php 
                               if ($arrLinkFilePPNNama[$i]) {
                                  echo '<br><span style="font-size:9px;">File : '.$arrLinkFilePPNNama[$i].'</span> <br><a href="'.base_url('uploads/ppn_pph/').$arrLinkFilePPN[$i].'" class="badge badge-primary">Download file</a>';
                                } else { echo '<br><span class="badge badge-danger">Belum upload</span>'; } ?>
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

          <div class="form-actions">
            <input type="hidden" name="reqTahunPajak" value="<?=$reqTahunPajak?>" />
            <input type="hidden" name="reqSubmit" id="reqSubmit"/>
            <a href="main/index/data_perpajakan_pajak_bulanan" class="<?= CLASS_BTN_DANGER ?> mr-1"><?= BTN_KEMBALI ?></a> 
            <button type="submit" class="<?= CLASS_BTN_PRIMARY ?>"><?= BTN_SIMPAN ?></button>
          </div> 
          
        </div>
      </div>
      </form>
    </div>
  </div> 
</div>   