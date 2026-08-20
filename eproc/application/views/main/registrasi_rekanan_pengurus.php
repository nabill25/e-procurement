<?php
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 * 
 */
$this->libsession->cekSession();   

// if($this->USER_TYPE_ID == "")
//     redirect("app");

/* INCLUDE FILE */
$this->load->library("crfs_protect"); $csrf = new crfs_protect('_crfs_rr');
$this->load->library("kauth");  $userLogin = new kauth(); 
$this->load->model("Rekanan");
$this->load->model("RekananPengurus");
$this->load->model("RekananTipe");
//$this->load->library("rekananijinusahainfo");  $ijin_usaha_tanggal_berakhir = new rekananijinusahainfo();
include_once("functions/string.func.php");
include_once("functions/date.func.php");
include_once("functions/default.func.php");

$reqId = httpFilterRequest('reqId');
/* create objects */
$reqId = $reqId;

//$reqRekananId = 1782;

$rekanan = new Rekanan();
$rekanan_pengurus_komisaris = new RekananPengurus();
$rekanan_pengurus_direksi = new RekananPengurus();
$rekanan_pengurus = new RekananPengurus();
$rekanan_tipe = new RekananTipe();

/* DATA VIEW */
$allRecord_komisaris = $rekanan_pengurus_komisaris->getCountByParams(array("REKANAN_ID"=>$this->ID,"TIPE"=>1));
$rekanan_pengurus_komisaris ->selectByParams(array("REKANAN_ID"=>$this->ID,"TIPE"=>1),-1,-1);



$allRecord_direksi = $rekanan_pengurus_direksi->getCountByParams(array("REKANAN_ID"=>$this->ID,"TIPE"=>2));
$rekanan_pengurus_direksi ->selectByParams(array("REKANAN_ID"=>$this->ID ,"TIPE"=>2),-1,-1);

$rekanan->selectByParams(array("REKANAN_ID"=>$this->ID),-1,-1);
$rekanan->firstRow();

$tempRekananTipeID = $rekanan->getField("REKANAN_TIPE_ID"); // 1 PT, 2 CV, 3 Firma, 4 Koperasi, 5 UD, 6 Lainnya

?>
<script type="text/javascript">
$(document).ready(function() {
    
    $(function(){
        $('#ff').form({
            url:'rekanan_pengurus_json/registrasi',
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
                // alertSuccess2(data);     
                hideLoad();
                document.location.href = 'main/index/registrasi_rekanan_rekening_koran';     
            }
        });
        
    });
    
});

function createRowKomisaris()
{
    $(function () {
        $.get("main/loadUrl/main/registrasi_rekanan_komisaris_template", function (data) {
            $("#tbodyKomisaris").append(data);
        });
    }); 
}

function createRowDireksi()
{
    $(function () {
        $.get("main/loadUrl/main/registrasi_rekanan_direksi_template", function (data) {
            $("#tbodyDireksi").append(data);
        });
    }); 
}
</script>

<link rel="stylesheet" type="text/css" href="<?=base_url() ?>assets/new/vendors/css/ui/jquery-ui.min.css">
<link rel="stylesheet" type="text/css" href="<?=base_url() ?>assets/new/css/plugins/ui/jqueryui.css">
<!-- <script src="<?=base_url()?>assets/new/js/core/libraries/jquery_ui/jquery-ui.min.js"></script> -->
<!-- <script src="<?=base_url()?>assets/new/js/scripts/ui/jquery-ui/navigations.js"></script> -->
<style type="text/css">
  small { font-weight: bold; font-size: 11.5px }
</style>

<div class="row"> 
  <div class="col-md-12 col-sm-12">
    <div class="card">
      <div class="card-content collapse show border-info border-darken-2">
        <div class="card-body">
          <!-- <div class="alert bg-primary alert-icon-left alert-arrow-left alert-dismissible mb-2" role="alert">
            <span class="alert-icon"><i class="fa fa-th"></i></span>  
            <strong><?php //translate("Pendaftaran Daftar Rekanan Perusahaan (DRP)", "Registration")?></strong>  
          </div>  -->
          <form id="ff" class="easyui-form form-horizontal" method="post" data-options="novalidate:true" enctype="multipart/form-data">

            <div class="card mb-1 border-blue border-darken-1">
              <div class="card-content">
                <div class="p-1">
                  <div class="alert bg-primary alert-icon-left alert-arrow-left alert-dismissible mb-2" role="alert">
                    <span class="alert-icon"><i class="fa fa-th"></i></span>  <strong><?=translate("KOMISARIS", "COMMISSIONER")?> </strong> <?php 
                    if ($tempRekananTipeID == '1') {
                         echo '<span class="badge badge-pill badge-danger">wajib</span>';
                     } else {
                         echo '<span class="badge badge-pill badge-danger">jika ada</span>';
                     } 
                     ?>
                     <small style="font-weight: normal"><?=translate("Data komisaris diperlukan jika jenis perusahaan Anda adalah PT (Perseroan Terbatas)","Data commissioner is required if you are PT (Perseroan Terbatas)")?>
                     </small> &nbsp;
                    <a onclick="createRowKomisaris()"> <span class="fa fa-plus text-white font-medium-2 icon-line-height" data-toggle="tooltip" data-placement="top" title="Tambah Komisaris"></span> </a> 
                  </div> 
                  <div class="table-responsive">
                    <table class="table table-bordered table-hover" id="tbl_bidang">
                        <thead>
                            <tr>
                              <th><?=translate("Nama", "Fullname")?> </th>
                              <th><?=translate("No. KTP", "Identity Card (ID)")?></th>
                              <th><?=translate("Jabatan dalam Perusahaan", "Occupation")?></th>
                              <th><?=translate("File", "File")?> (<small> Format file .pdf & Maksimal ukuran file 2MB </small>)</th>
                              <th><?=translate("Aksi", "Action")?></th>
                            </tr>
                        </thead>
                      <tbody id="tbodyKomisaris">
                        <?php
                        if($allRecord_komisaris > 0){
                            $no_komisaris = 1;
                            while($rekanan_pengurus_komisaris->nextRow()){
                        ?>
                         <tr id="tr1<?=$no_komisaris?>">
                             <td>
                                <input class="form-control easyui-validatebox span2" type="hidden"  name="reqRekananPengurusId[]" id="reqRekananPengurusId<?=$no_komisaris?>" value="<?=$rekanan_pengurus_komisaris->getField("REKANAN_PENGURUS_ID")?>" />
                                <input class="form-control easyui-validatebox span2" type="text" required name="reqKomisarisNama[]" id="reqKomisarisNama<?=$no_komisaris?>" value="<?=$rekanan_pengurus_komisaris->getField("NAMA")?>" />
                             </td>
                             <td>
                                <input class="form-control easyui-validatebox span2" type="text" required name="reqKomisarisKTP[]" id="reqKomisarisKTP<?=$no_komisaris?>" value="<?=$rekanan_pengurus_komisaris->getField("KTP")?>" />
                             </td>
                             <td>
                                <input class="form-control easyui-validatebox span2" type="text" required name="reqKomisarisJabatan[]" id="reqKomisarisJabatan<?=$no_komisaris?>" value="<?=$rekanan_pengurus_komisaris->getField("JABATAN")?>" />
                             </td>
                             <td>
                                <input type="file" name="reqLinkFile[]" id="reqLinkFile[]<?=$no_komisaris?>" size="30" class="easyui-validatebox span2" validType="fileType['pdf']" />
                                <input type="hidden" name="reqLinkFileTemp[]" id="reqLinkFileTemp[]<?=$no_komisaris?>" value="<?=$rekanan_pengurus_komisaris->getField("PATH_FILE")?>">
                                <input type="hidden" name="reqLinkFileTempTipe[]" id="reqLinkFileTempTipe[]<?=$no_komisaris?>" value="<?=$rekanan_pengurus_komisaris->getField("TIPE_FILE")?>">
                                <input type="hidden" name="reqLinkFileTempUkuran[]" id="reqLinkFileTempUkuran[]<?=$no_komisaris?>" value="<?=$rekanan_pengurus_komisaris->getField("UKURAN")?>">
                                <input type="hidden" name="reqLinkFileTempNama[]" id="reqLinkFileTempNama[]<?=$no_komisaris?>" value="<?=$rekanan_pengurus_komisaris->getField("NAMA_FILE")?>">
                                <?php 
                                if ($rekanan_pengurus_komisaris->getField("NAMA_FILE")) {
                                    echo '<br>File :'.$rekanan_pengurus_komisaris->getField("NAMA_FILE");
                                 } ?>
                             </td>
                             <td>
                                <a onClick="deleteDataTable('rekanan_pengurus_json/delete/', '<?=$rekanan_pengurus_komisaris->getField("REKANAN_PENGURUS_ID")?>', 'tr1<?=$no_komisaris?>')" 
                                class="btn-aksi"><i class="fa fa-trash" aria-hidden="true"></i></a>
                                  </a>
                             </td>
                        </tr> 
                       
                        <?php
                                $no_komisaris = $no_komisaris + 1;
                              }
                            } 
                        ?>
                         <tr id="tr1<?=$no_komisaris?>">
                             <td>
                                <input class="form-control easyui-validatebox span2" type="hidden"  name="reqRekananPengurusId[]" id="reqRekananPengurusId<?=$no_komisaris?>" value="" />
                                <input class="form-control easyui-validatebox span2" type="text"  name="reqKomisarisNama[]" id="reqKomisarisNama<?=$no_komisaris?>" value="" />
                             </td>
                             <td>
                                <input class="form-control easyui-validatebox span2" type="text"  name="reqKomisarisKTP[]" id="reqKomisarisKTP<?=$no_komisaris?>" value="" />
                             </td>
                             <td>
                                <input class="form-control easyui-validatebox span2" type="text"  name="reqKomisarisJabatan[]" id="reqKomisarisJabatan<?=$no_komisaris?>" value="" />
                             </td>
                             <td>
                                <input type="file" name="reqLinkFile[]" id="reqLinkFile[]<?=$no_komisaris?>" size="30" class="easyui-validatebox span2" validType="fileType['pdf']" />
                                <input type="hidden" name="reqLinkFileTemp[]" id="reqLinkFileTemp[]<?=$no_komisaris?>" value="">
                                <input type="hidden" name="reqLinkFileTempTipe[]" id="reqLinkFileTempTipe[]<?=$no_komisaris?>" value="">
                                <input type="hidden" name="reqLinkFileTempUkuran[]" id="reqLinkFileTempUkuran[]<?=$no_komisaris?>" value="">
                                <input type="hidden" name="reqLinkFileTempNama[]" id="reqLinkFileTempNama[]<?=$no_komisaris?>" value="">
                             </td>
                             <td>
                                <a onClick="deleteDataTable('rekanan_pengurus_json/delete/', '<?=$rekanan_pengurus_komisaris->getField("REKANAN_PENGURUS_ID")?>', 'tr1<?=$no_komisaris?>')" 
                                class="btn-aksi"><i class="fa fa-trash" aria-hidden="true"></i></a>
                                  </a>
                             </td>
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
                    <span class="alert-icon"><i class="fa fa-th"></i></span>  <strong><?=translate("DIREKSI", "DIRECTORS")?> </strong>  
                    <span class="badge badge-pill badge-danger">wajib</span> 
                    <a onclick="createRowDireksi()"> <span class="fa fa-plus text-white font-medium-2 icon-line-height" data-toggle="tooltip" data-placement="top" title="Tambah Direksi"></span> </a> 
                  </div> 
                  <div class="table-responsive"> 
                    <table class="table table-bordered table-hover" id="tbl_bidang">
                        <thead>
                            <tr>
                              <th><?=translate("Nama", "Fullname")?></th>
                              <th><?=translate("No. KTP", "Identity Card (ID)")?></th>
                              <th><?=translate("Jabatan dalam Perusahaan", "Occupation")?></th>
                              <th><?=translate("File", "File")?> (<small> Format file .pdf & Maksimal ukuran file 2MB </small>)</th>
                              <th><?=translate("Aksi", "Action")?></th>
                            </tr>
                        </thead>
                      <tbody id="tbodyDireksi">
                        <?php
                        if($allRecord_direksi > 0){
                            $no_direksi = 0;
                            while($rekanan_pengurus_direksi->nextRow()){
                        ?>
                        <tr id="tr<?=$no_direksi?>">
                             <td>
                             <input class="form-control easyui-validatebox span2" type="hidden"  name="reqRekananPengurusId[]" id="reqRekananPengurusId<?=$no_direksi?>" value="<?=$rekanan_pengurus_direksi->getField("REKANAN_PENGURUS_ID")?>" />
                                <input class="form-control easyui-validatebox span2" type="text" required name="reqDireksiNama[]" id="reqDireksiNama<?=$no_direksi?>" value="<?=$rekanan_pengurus_direksi->getField("NAMA")?>" />
                             </td>
                             <td>
                                <input class="form-control easyui-validatebox span2" type="text" required name="reqDireksiKTP[]" id="reqDireksiKTP<?=$no_direksi?>" value="<?=$rekanan_pengurus_direksi->getField("KTP")?>" />
                             </td>
                             <td>
                                <input class="form-control easyui-validatebox span2" type="text" required name="reqDireksiJabatan[]" id="reqDireksiJabatan<?=$no_direksi?>" value="<?=$rekanan_pengurus_direksi->getField("JABATAN")?>" />
                             </td>
                             <td>
                                <input type="file" name="reqLinkFileDireksi[]" id="reqLinkFileDireksi[]<?=$no_direksi?>" size="30"  class="easyui-validatebox span2" validType="fileType['pdf']" />
                                <input type="hidden" name="reqLinkFileDireksiTemp[]" id="reqLinkFileDireksiTemp[]<?=$no_direksi?>" value="<?=$rekanan_pengurus_direksi->getField("PATH_FILE")?>">
                                <input type="hidden" name="reqLinkFileDireksiTempTipe[]" id="reqLinkFileDireksiTempTipe[]<?=$no_direksi?>" value="<?=$rekanan_pengurus_direksi->getField("TIPE_FILE")?>">
                                <input type="hidden" name="reqLinkFileDireksiTempUkuran[]" id="reqLinkFileDireksiTempUkuran[]<?=$no_direksi?>" value="<?=$rekanan_pengurus_direksi->getField("UKURAN")?>">
                                <input type="hidden" name="reqLinkFileDireksiTempNama[]" id="reqLinkFileDireksiTempNama[]<?=$no_direksi?>" value="<?=$rekanan_pengurus_direksi->getField("NAMA_FILE")?>">
                                <?php 
                                if ($rekanan_pengurus_direksi->getField("NAMA_FILE")) {
                                    echo '<br> File : '.$rekanan_pengurus_direksi->getField("NAMA_FILE");
                                 } ?>
                             </td>
                             <td>
                                  <a onClick="deleteDataTable('rekanan_pengurus_json/delete/', '<?=$rekanan_pengurus_direksi->getField("REKANAN_PENGURUS_ID")?>', 'tr<?=$no_direksi?>')" 
                                class="btn-aksi"><i class="fa fa-trash" aria-hidden="true"></i></a>
                                  </a>
                             </td>
                        </tr> 
                        <?php
                            $no_direksi = $no_direksi + 1;
                            }
                        } 
                        ?> 
                         <tr id="tr<?=$no_direksi?>">
                             <td>
                             <input class="form-control easyui-validatebox span2" type="hidden"  name="reqRekananPengurusId[]" id="reqRekananPengurusId<?=$no_direksi?>" value="" />
                                <input class="form-control easyui-validatebox span2" type="text"  name="reqDireksiNama[]" id="reqDireksiNama<?=$no_direksi?>" value="" />
                             </td>
                             <td>
                                <input class="form-control easyui-validatebox span2" type="text"  name="reqDireksiKTP[]" id="reqDireksiKTP<?=$no_direksi?>" value="" />
                             </td>
                             <td>
                                <input class="form-control easyui-validatebox span2" type="text"  name="reqDireksiJabatan[]" id="reqDireksiJabatan<?=$no_direksi?>" value="" />
                             </td>
                             <td>
                                <input type="file" name="reqLinkFileDireksi[]" id="reqLinkFileDireksi[]<?=$no_direksi?>" size="30" class="easyui-validatebox span2" validType="fileType['pdf']" />
                                <input type="hidden" name="reqLinkFileDireksiTemp[]" id="reqLinkFileDireksiTemp[]<?=$no_direksi?>" value="">
                                <input type="hidden" name="reqLinkFileDireksiTempTipe[]" id="reqLinkFileDireksiTempTipe[]<?=$no_direksi?>" value="">
                                <input type="hidden" name="reqLinkFileDireksiTempUkuran[]" id="reqLinkFileDireksiTempUkuran[]<?=$no_direksi?>" value="">
                                <input type="hidden" name="reqLinkFileDireksiTempNama[]" id="reqLinkFileDireksiTempNama[]<?=$no_direksi?>" value="">
                             </td>
                             <td>
                                  <a onClick="deleteDataTable('rekanan_pengurus_json/delete/', '<?=$rekanan_pengurus_direksi->getField("REKANAN_PENGURUS_ID")?>', 'tr<?=$no_direksi?>')" 
                                class="btn-aksi"><i class="fa fa-trash" aria-hidden="true"></i></a>
                                  </a>
                             </td>
                        </tr>
                      </tbody>
                    </table> 
                  </div>
                </div>
              </div>
            </div>


            <div class="form-actions">
                <input type="hidden" name="reqPengurusID" value="<?=$reqPengurusID?>" />
                <input type="hidden" name="reqRekananId" value="<?=$reqRekananId?>" />
                <input type="hidden" name="reqId" value="<?=$reqId?>" />
                <input type="hidden" name="reqMode" value="<?=$reqMode?>" />
                <input type="hidden" name="reqId" value="<?=$reqId?>">
                <a href="main/index/registrasi_rekanan_ijin_usaha"  class="btn btn-danger"><i class="fa fa-arrow-left"></i> <?=translate("Kembali", "Back")?></a>
                <button type="submit" class="btn btn-primary pull-right"><?=translate("Lanjut", "Next")?> <i class="fa fa-arrow-right"></i></button>
            </div>   
 
          </form>                 
        </div>
      </div>
    </div>
  </div>
</div> 