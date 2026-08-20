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
include_once("functions/string.func.php");
include_once("functions/date.func.php");
include_once("functions/default.func.php");
$this->load->model("Rekanan");
$this->load->model("RekananRekeningKoran");

/* create objects */
$rekanan = new Rekanan();
$rekanan_koran 	= new RekananRekeningKoran();

$reqSahamId= httpFilterRequest('reqSahamId');
$reqPemegangSaham= httpFilterPost('reqPemegangSaham');
$reqNomorKTP= httpFilterPost('reqNomorKTP');
$reqAlamat= httpFilterPost('reqAlamat');
$reqPersentase= httpFilterPost('reqPersentase');
$reqId= httpFilterPost('reqId');
$reqSubmit= httpFilterPost('reqSubmit');

$reqRekananId= $this->ID;
$rekanan->selectByParams(array("REKANAN_ID"=>$this->ID),-1,-1);
$rekanan->firstRow();

$allRecord_S = $rekanan_koran->getCountByParams(array("REKANAN_ID"=>$this->ID));
$rekanan_koran->selectByParams(array("REKANAN_ID" => $this->ID),-1,-1);

?>
<script type="text/javascript">
$(document).ready(function() {
	$(function(){
		$('#ff').form({
			url:'rekanan_rekening_koran_json/registrasi',
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
				//alert(data);return false;
        // alertSuccess2(data);     
        hideLoad();
				document.location.href = 'main/index/registrasi_rekanan_kepemilikan_saham';			
			}
		});
	});
	
});

function createRowRekeningKoran()
{
	$(function () {
		$.get("main/loadUrl/main/registrasi_rekanan_rekening_koran_template", function (data) {
			$("#tbodyRekeningKoran").append(data);
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
          <div class="alert bg-primary alert-icon-left alert-arrow-left alert-dismissible mb-2" role="alert">
            <span class="alert-icon"><i class="fa fa-th"></i></span>  
            <strong>Rekening Koran</strong>   <span class="badge badge-pill badge-danger">wajib</span>
            <a onclick="createRowRekeningKoran()"> <span class="fa fa-plus text-white font-medium-2 icon-line-height" data-toggle="tooltip" data-placement="top" title="Tambah Rekening Koran"></span> </a> 
          </div> 
          <form id="ff" class="easyui-form form-horizontal" method="post" novalidate enctype="multipart/form-data">
          
            <table class="table table-bordered table-hover" id="tbl_bidang">
              <thead>
                  <tr class="judul-kolom">
                    <th>No. Rekening</th>
                    <th>Bank</th>
                    <th>Periode</th>
                    <th>Saldo Akhir (IDR)</th>
                    <th>File (<small> Format file .pdf & Maksimal ukuran file 2MB </small>) wajib</th>
                    <th><?=translate("Aksi", "Action")?></th>
                  </tr>
              </thead>
              <tbody  id="tbodyRekeningKoran">
                <?php
                $no =1;
                while($rekanan_koran->nextRow())
                {
                ?>  
                 <tr id="tr1<?=$no?>">
                     <td>
                       <input class="form-control easyui-validatebox span1" type="hidden"  name="reqRekananRekeningKoranId[]" id="reqRekananRekeningKoranId<?=$no?>" value="<?=$rekanan_koran->getField("REKANAN_REKENING_KORAN_ID")?>" />
                         <input name="reqNoRekening[]" type="text" title="Nomor rekening harus diisi" class="form-control easyui-validatebox span1" id="reqNoRekening<?=$no?>" value="<?=$rekanan_koran->getField("NOMOR")?>"  />
                    </td>
                     <td>
                      <input type="text" name="reqBankId[]" id="reqBankId<?=$no?>" class="easyui-combobox span2"
                              data-options="valueField:'id',textField:'text',url:'bank_json/combo'" value="<?=$rekanan_koran->getField("BANK_ID")?>"  />
      
                     </td>
                     <td>
                      <select name="reqPeriode[]" id="reqPeriode<?=$no?>" class="easyui-combobox span2"  >
                        <?php
                        $bulanTerpilih = generateZero($rekanan_koran->getField("BULAN"), 2);
                        $tahunTerpilih = $rekanan_koran->getField("TAHUN");
                        $periodeTerpilih = $bulanTerpilih.$tahunTerpilih;
                        for($i=0;$i<=3;$i++)
                        {
                          $reqLastMonth = strtotime("-".$i." months", strtotime(date("d-m-Y")));
                          $bulan = strftime ( '%m' , $reqLastMonth );
                          $tahun = strftime ( '%Y' , $reqLastMonth );
                          $periode =$bulan.$tahun;
                        ?>
                            <option value="<?=$periode?>" <?php if($periode == $periodeTerpilih) { ?> selected <?php } ?>><?=getNamePeriode($periode)?></option>
                        <?php
                        }
                        ?>
                        </select>
                     </td>
                      <td><input name="reqNilai[]" class="form-control easyui-validatebox span1"  type="text" id="reqNilai<?=$no?>" value="<?=numberToIna($rekanan_koran->getField("NILAI"))?>" 
                       OnFocus="FormatAngka('reqNilai<?=$no?>')" OnKeyUp="FormatUang('reqNilai<?=$no?>')" OnBlur="FormatUang('reqNilai<?=$no?>')"/>
                     </td>
                     <td>
                      <input type="file" name="reqLinkFile[]" id="reqLinkFile<?=$no?>" size="30" class="easyui-validatebox span1" validType="fileType['pdf']" />
                      <input type="hidden" name="reqLinkFileTemp[]" id="reqLinkFileTemp<?=$no?>" value="<?=$rekanan_koran->getField("PATH_FILE")?>">
                       <input type="hidden" name="reqLinkFileTempTipe[]" id="reqLinkFileTempTipe<?=$no?>" value="">
                      <input type="hidden" name="reqLinkFileTempUkuran[]" id="reqLinkFileTempUkuran<?=$no?>" value="">
                      <input type="hidden" name="reqLinkFileTempNama[]" id="reqLinkFileTempNama<?=$no?>" value="">
                      <br>temp : <?=$rekanan_koran->getField("NAMA_FILE")?>
                     </td>
                     <td> 
                      <a onClick="deleteDataTable('rekanan_rekening_koran_json/delete/', '<?=$rekanan_koran->getField("REKANAN_REKENING_KORAN_ID")?>', 'tr1<?=$no?>')" class="btn-aksi">
                      <i class="fa fa-trash" aria-hidden="true"></i>
                      </a>
                </tr> 
                <?php
                  $no++;
                   }
                ?>
                <tr id="tr1<?=$no?>">
                     <td>
                      <input class="form-control easyui-validatebox span1" type="hidden"  name="reqRekananRekeningKoranId[]" id="reqRekananRekeningKoranId<?=$no?>" value="<?=$rekanan_koran->getField("REKANAN_SAHAM_ID")?>" />
                         <input name="reqNoRekening[]" type="text" title="Nomor rekening harus diisi" required class="form-control span1" id="reqNoRekening<?=$no?>" value=""  />
                      </td>
                     <td>
                        <input type="text" name="reqBankId[]" id="reqBankId<?=$no?>" class="easyui-combobox span2" 
                              data-options="valueField:'id',textField:'text',url:'bank_json/combo'" value=""  />
                      </td>
                     <td>
                      <select name="reqPeriode[]" id="reqPeriode<?=$no?>" required class="easyui-combobox span2"  >
                        <?php
                          for($i=0;$i<=3;$i++)
                          {
                            $reqLastMonth = strtotime("-".$i." months", strtotime(date("d-m-Y")));
                            $bulan = strftime ( '%m' , $reqLastMonth );
                            $tahun = strftime ( '%Y' , $reqLastMonth );
                            $periode =$bulan.$tahun;
                          ?>
                            <option value="<?=$periode?>"><?=getNamePeriode($periode)?></option>
                        <?php
                          }
                          ?>
                        </select>
                     </td>
                      <td><input name="reqNilai[]" class="form-control easyui-validatebox span1"  type="text" id="reqNilai<?=$no?>" value="<?=numberToIna($reqNilai)?>" 
                       OnFocus="FormatAngka('reqNilai<?=$no?>')" OnKeyUp="FormatUang('reqNilai<?=$no?>')" OnBlur="FormatUang('reqNilai<?=$no?>')"/>
                     </td>
                     <td>
                       <input type="file" name="reqLinkFile[]" id="reqLinkFile<?=$no?>" size="30" class="easyui-validatebox span1" validType="fileType['pdf']" />
                      <input type="hidden" name="reqLinkFileTemp[]" id="reqLinkFileTemp<?=$no?>" value="">
                       <input type="hidden" name="reqLinkFileTempTipe[]" id="reqLinkFileTempTipe<?=$no?>" value="">
                      <input type="hidden" name="reqLinkFileTempUkuran[]" id="reqLinkFileTempUkuran<?=$no?>" value="">
                      <input type="hidden" name="reqLinkFileTempNama[]" id="reqLinkFileTempNama<?=$no?>" value="">
                      <!-- temp :  -->
                     </td>
                     <td> 
                       <a title="#" onclick="$(this).parent().parent().remove();" class="btn-aksi"><i class="fa fa-trash" aria-hidden="true"></i></a>
                     </td>
                </tr> 
              </tbody>
            </table>

            <div class="form-actions">
                <input type="hidden" name="reqRekananId" value="<?=$reqRekananId?>" />
                <input type="hidden" name="reqMode" value="<?=$reqMode?>" />
        	      <input type="hidden" name="reqId" value="<?=$reqId?>">
                <a href="main/index/registrasi_rekanan_pengurus"  class="btn btn-danger"><i class="fa fa-arrow-left"></i> <?=translate("Kembali", "Back")?></a>
                <button type="submit" class="btn btn-primary pull-right"><?=translate("Lanjut", "Next")?> <i class="fa fa-arrow-right"></i></button>
            </div>   
 
          </form>                 
        </div>
      </div>
    </div>
  </div>
</div> 
 