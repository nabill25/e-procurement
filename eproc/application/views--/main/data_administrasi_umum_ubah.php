<?php
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 * 
 */

$this->libsession->cekSession('blockpenyedia');    
    
//ob_start();
/* INCLUDE FILE */
$this->load->library("crfs_protect"); $csrf = new crfs_protect();
$this->load->library("kauth");  $userLogin = new kauth(); 
$this->load->model("Rekanan");
$this->load->model("Region");
$this->load->model("Bank");
$this->load->model("Incoterm");
$this->load->model("PaymentMethod");
$this->load->model("MataUang");

include_once("functions/string.func.php");
include_once("functions/date.func.php");
include_once("functions/default.func.php");

/* create objects */
$rekanan = new Rekanan();
$region = new Region();
$bank = new Bank();
$incoterm = new Incoterm();
$payment_method = new PaymentMethod();
$mata_uang = new MataUang();

$rekanan->selectByParams(array("REKANAN_ID"=>$this->ID),-1,-1);
$rekanan->firstRow();

$reqKualifikasi = $rekanan->getField("REKANAN_KUALIFIKASI_ID");
$reqMail = $rekanan->getField("EMAIL");
$reqFaxKode = $rekanan->getField("FAX_KODE");
$reqFaxNo = $rekanan->getField("FAX");
$reqTeleponKode = $rekanan->getField("TELEPON_KODE");
$reqTeleponNo = $rekanan->getField("TELEPON");
$reqKota = $rekanan->getField("KOTA");
$reqAlamat = $rekanan->getField("ALAMAT");
$reqStatus = $rekanan->getField("STATUS_PERUSAHAAN");
$reqNPWP = $rekanan->getField("NPWP");
$reqPKP = $rekanan->getField("PKP");
$reqRekananTipe= $rekanan->getField("REKANAN_TIPE_ID");
$reqNama= $rekanan->getField("REKANAN_NAMA");

$reqMailPusat = $rekanan->getField("EMAIL_PUSAT");
$reqFaxKodePusat = $rekanan->getField("FAX_KODE_PUSAT");
$reqFaxNoPusat = $rekanan->getField("FAX_PUSAT");
$reqTeleponKodePusat = $rekanan->getField("TELEPON_KODE_PUSAT");
$reqTeleponNoPusat = $rekanan->getField("TELEPON_PUSAT");
$reqAlamatPusat = $rekanan->getField("ALAMAT_PUSAT");
$reqKodepos = $rekanan->getField("KODEPOS");

$reqRegionId = $rekanan->getField("REGION_ID");
$reqBankId = $rekanan->getField("BANK_ID");
$reqRekening = $rekanan->getField("BANK_REKENING");
$reqRekeningNama = $rekanan->getField("BANK_PEMILIK");
$reqIncoterm1 = $rekanan->getField("INCOTERM_ID");
$reqIncoterm2 = $rekanan->getField("INCOTERM2");
$reqPaymentMethodId = $rekanan->getField("PAYMENT_METHOD_ID");
$reqMataUang = $rekanan->getField("MATA_UANG_KODE");
$reqKontakPerson = $rekanan->getField("KONTAK_PERSON");
$reqKontakPersonHp = $rekanan->getField("KONTAK_PERSON_HP");
$reqWebsite = $rekanan->getField("WEBSITE");

if($reqIncoterm1 == "")
	$reqIncoterm1 = "CIF";

$region->selectByParams();
$bank->selectByParams();
$incoterm->selectByParams();
$payment_method->selectByParams();
$mata_uang->selectByParams();

?>
<script type="text/javascript">
$(document).ready(function() {
	
	$(function(){
		$('#ff').form({
			url:'rekanan_json/data_administrasi_umum_ubah',
			onSubmit:function(){
				return $(this).form('validate');
			},
			success:function(data){
				document.location.href = 'main/index/data_administrasi_umum';
			}
		});
		
	$('#reqBankId').combobox({
		filter: function(q, row){
			var opts = $(this).combobox('options');
			return row[opts.textField].toLowerCase().indexOf(q.toLowerCase()) >= 0;
		}
		});
		
	});
	
	
	
});
</script>

<div class="row"> 
    <div class="col-md-12 col-sm-12">
        <div class="card">
            <div class="card-header card-head-inverse bg-primary">
                <h4 class="card-title text-white">Ubah Profil Perusahaan  
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
                <form id="ff" class="easyui-form form-horizontal" method="post" novalidate enctype="multipart/form-data">
                  <div class="row">
                    <div class="form-group col-md-2 mb-2">
                      <label style="width: 100%">Nama Perusahaan</label>
                      <input type="text" name="reqRekananTipe" class="form-control easyui-combobox span1" data-options="valueField:'id',textField:'text',url:'rekanan_tipe_json/combo'" style="width: 200% !important; cursor: no-drop;" value="<?=$reqRekananTipe?>" readonly=""/>
                    </div>
                    <div class="form-group col-md-10 mb-2">
                      <label for="projectinput6">&nbsp;</label> 
                      <input type="text" class="form-control easyui-validatebox span8" name="reqNama" value="<?=$reqNama?>" title="Nama perusahaan harus diisi"  required readonly="" >
                    </div>
                  </div> 

                  <div class="row">
                    <div class="form-group col-md-5 mb-2">
                      <label style="width: 100%">NPWP</label>
                    	<input type="text" class="form-control easyui-validatebox span4" value="<?=$reqNPWP?>" name="reqNPWP" id="reqNPWP" onkeydown="return format_npwp(event);" maxlength="20" required readonly="">
                    </div>
                    <div class="form-group col-md-5 mb-2">
                      <label style="width: 100%">PKP</label>
                      <input type="text" class="form-control easyui-validatebox span4" value="<?=$reqPKP?>" name="reqPKP" id="reqPKP" onkeydown="return format_npwp(event);" maxlength="20" required readonly="">
                    </div>
                    <div class="form-group col-md-2 mb-2">
                      <label style="width: 100%">Status Kantor Perusahaan</label> 
                    	<input type="radio" <?php if($reqStatus == '0') echo 'checked';?>  name="reqStatus" value="0" /> Pusat &nbsp;&nbsp;&nbsp;
        				      <input type="radio" <?php if($reqStatus == '1') echo 'checked';?> name="reqStatus" value="1" /> Cabang
                    </div>
                  </div>   
                  
                  <div class="row">
                    <div class="form-group col-md-12 mb-2">
                      <label>Alamat:</label>
                  	  <textarea name="reqAlamat" cols="50" rows="5" title="Alamat harus diisi" class="form-control easyui-validatebox span4" required ><?=$reqAlamat;?></textarea>
                    </div> 
                  </div> 

                  <div class="row">
                    <div class="form-group col-md-2 mb-2">
                      <label style="width: 100%">Provinsi</label>
                    	<input type="text" name="reqRegionId" class="easyui-combobox span4" data-options="valueField:'id',textField:'text',url:'region_json/combo'"  value="<?=$reqRegionId?>" style="width: 200% !important" />
                    </div>
                    <div class="form-group col-md-7 mb-2">
                      <label>Kab/Kota</label> 
                      <input type="text" name="reqKota" value="<?=$reqKota?>" title="Kota harus diisi" class="form-control easyui-validatebox span4" required  />
                    </div>
                    <div class="form-group col-md-3 mb-2">
                      <label>Kode Pos</label> 
                    	<input type="text" name="reqKodepos" value="<?=$reqKodepos?>" title="Kodepos harus diisi" class="form-control easyui-validatebox span2" maxlength="6" required  />
                    </div>
                  </div> 

                  <div class="row">
                    <div class="form-group col-md-1 mb-2">
                      <label style="width: 100%">No. Telepon</label>
                    	<input type="text" name="reqTeleponKode" id="reqTeleponKode" class="form-control easyui-validatebox span1" value="<?=$reqTeleponKode?>"> 
                    </div>
                    <div class="form-group col-md-5 mb-2">
                      <label><small>(kode area tidak perlu diisi jika nomor telepon yang dicantumkan adalah nomor ponsel) </small></label> 
                    	<input type="text" name="reqTeleponNo" id="reqTeleponNo" class="form-control easyui-validatebox span3" value="<?=$reqTeleponNo?>" required> 
                    </div>
                    <div class="form-group col-md-1 mb-2">
                      <label style="width: 100%">Fax</label>
                    	<input type="text" name="reqFaxKode" id="reqFaxKode" class="form-control easyui-validatebox span1" value="<?=$reqFaxKode?>">
                    </div>
                    <div class="form-group col-md-5 mb-2">
                      <label>&nbsp;</label> 
                    	<input type="text" name="reqFaxNo" id="reqFaxNo" class="form-control easyui-validatebox span3" value="<?=$reqFaxNo?>">
                    </div>
                  </div>     

                  <div class="row">
                    <div class="form-group col-md-6 mb-2">
                      <label style="width: 100%">Kontak Person</label>
                    	<input type="text" name="reqKontakPerson" id="reqKontakPerson" class="form-control easyui-validatebox span3" value="<?=$reqKontakPerson?>" placeholder="Nama Penanggung Jawab">
                    </div>
                    <div class="form-group col-md-6 mb-2">
                      <label>No. HP</label> 
                    	<input type="text" name="reqKontakPersonHp" id="reqKontakPersonHp" class="form-control easyui-validatebox span3" value="<?=$reqKontakPersonHp?>">
                    </div>
                  </div>  

                  <div class="row">
                    <div class="form-group col-md-6 mb-2">
                      <label style="width: 100%">eMail</label>
                    	 <input type="text" name="reqMail" value="<?=$reqMail?>"  title="Email harus diisi" class="form-control easyui-validatebox span4" data-options="required:true,validType:['email']" required  readonly=""/>
                    </div>
                    <div class="form-group col-md-6 mb-2">
                      <label>Website</label> 
                      <input type="text" name="reqWebsite" value="<?=$reqWebsite?>"  title="Email harus diisi" class="form-control easyui-validatebox span4" />
                    </div>
                  </div>   

                  <div class="row">
                    <div class="form-group col-md-12 mb-2">
                      <label style="width: 100%">Kualifikasi:</label>
                      <input type="radio" <?php if($reqKualifikasi == '1') echo 'checked';?>  name="reqKualifikasi" value="1" /> Kecil &nbsp;&nbsp;&nbsp;
                      <input type="radio" <?php if($reqKualifikasi == '2') echo 'checked';?> name="reqKualifikasi" value="2" /> Non Kecil
                    </div> 
                  </div> 

                  <div class="card mb-1 border-blue border-darken-1">
                    <div class="card-content">
                      <div class="p-1">
                        <div class="alert bg-primary alert-icon-left alert-arrow-left alert-dismissible mb-2" role="alert">
                          <span class="alert-icon"><i class="fa fa-th"></i></span>  <strong>Informasi Perusahaan Pusat <small>(<b>Diisi jika Status Kantor Perusahaan adalah Pusat</b>)</small></strong>  
                        </div> 

                        <div class="row">
                          <div class="form-group col-md-1 mb-2">
                            <label style="width: 100%">No. Telepon</label>
                            <input type="text" class="form-control easyui-validatebox span1" name="reqTeleponKodePusat" id="reqTeleponKodePusat" value="<?=$reqTeleponKodePusat?>">
                          </div>
                          <div class="form-group col-md-5 mb-2">
                            <label><small>(kode area tidak perlu diisi jika nomor telepon yang dicantumkan adalah nomor ponsel) </small></label> 
                            <input type="text" class="form-control easyui-validatebox span3" name="reqTeleponNoPusat" id="reqTeleponNoPusat" value="<?=$reqTeleponNoPusat?>" >
                          </div>
                          <div class="form-group col-md-1 mb-2">
                            <label style="width: 100%">Fax</label>
                          	<input type="text" class="form-control easyui-validatebox span1" name="reqFaxKodePusat" id="reqFaxKodePusat" value="<?=$reqFaxKodePusat?>">
                          </div>
                          <div class="form-group col-md-5 mb-2">
                            <label>&nbsp;</label> 
                          	<input type="text" class="form-control easyui-validatebox span3" name="reqFaxNoPusat" id="reqFaxNoPusat" value="<?=$reqFaxNoPusat?>">
                          </div>
                        </div>      

                        <div class="row">
                          <div class="form-group col-md-12 mb-2">
                            <label>Email</label>
                          	<input type="text" class="form-control easyui-validatebox span4" name="reqMailPusat" value="<?=$reqMailPusat?>" data-options="validType:['email']"  />
                          </div> 
                        </div> 
                        <div class="row">
                          <div class="form-group col-md-12 mb-2">
                            <label>Alamat</label>
                            <textarea name="reqAlamatPusat" cols="50" class="form-control easyui-validatebox span4" rows="5" ><?=$reqAlamatPusat?></textarea>
                          </div> 
                        </div> 

                      </div>
                    </div>
                  </div>

                  <div class="card mb-1 border-blue border-darken-1">
                    <div class="card-content">
                      <div class="p-1">
                        <div class="alert bg-primary alert-icon-left alert-arrow-left alert-dismissible mb-2" role="alert">
                          <span class="alert-icon"><i class="fa fa-th"></i></span>  <strong>Informasi Pembayaran</strong>  
                        </div> 

                        <div class="row">
                          <div class="form-group col-md-2 mb-2">
                            <label style="width: 100%">Mata Uang</label>
                        	  <input required type="text" name="reqMataUang" class="easyui-combobox span4" data-options="valueField:'id',textField:'text',url:'mata_uang_json/combo'" value="<?=$reqMataUang?>" />
                          </div> 
                          <div class="form-group col-md-6 mb-2">
                            <label style="width: 100%">Bank</label>
                            <input required type="text" name="reqBankId" id="reqBankId" class="easyui-combobox span4" data-options="valueField:'id',textField:'text',url:'bank_json/combo'"  value="<?=$reqBankId?>" style="width: 600% !important" />
                          </div> 
                          <div class="form-group col-md-4 mb-2">
                            <label>No Rekening</label>
                            <input type="text" name="reqNoRekening" value="<?=$reqRekening?>" title="No rekening harus diisi" class="form-control easyui-validatebox span4" required  />
                          </div> 
                        </div>  
                        
                        <div class="row">
                          <div class="form-group col-md-6 mb-2">
                            <label style="width: 100%">Atas Nama</label>
                            <input type="text" name="reqAtasNama" value="<?=$reqRekeningNama?>" title="Pemilik rekening harus diisi" class="form-control easyui-validatebox span4" required  />
                          </div>
                          <div class="form-group col-md-6 mb-2">
                            <label style="width: 100%">Cara Pembayaran</label> 
                            <input type="text" name="reqPaymentMethodId" class="easyui-combobox span4" data-options="valueField:'id',textField:'text',url:'payment_method_json/combo'" value="<?=$reqPaymentMethodId?>" style="width: 600% !important" required/>
                          </div>
                        </div>   
                         
                      </div>
                    </div>
                  </div>

                  <div class="card mb-1 border-blue border-darken-1">
                    <div class="card-content">
                      <div class="p-1">
                        <div class="alert bg-primary alert-icon-left alert-arrow-left alert-dismissible mb-2" role="alert">
                          <span class="alert-icon"><i class="fa fa-th"></i></span>  <strong>Incoterm</strong>  
                        </div> 

                        <div class="row">
                          <div class="form-group col-md-4 mb-2">
                            <label style="width: 100%">Incoterm I</label>
                        	  <input type="text" name="reqIncoterm1" class="easyui-combobox span4" data-options="valueField:'id',textField:'text',url:'incoterm_json/combo'" value="<?=$reqIncoterm1?>" style="width: 400% !important"  />
                          </div>
                          <div class="form-group col-md-8 mb-2">
                            <label>Incoterm II</label> 
                        	  <input type="text" class="form-control easyui-validatebox span4" name="reqIncoterm2" value="<?=$reqIncoterm2?>" maxlength="50"   />
                          </div>
                        </div>  
                      </div>
                    </div>
                  </div>
                  
                  <div class="form-actions">
                    <a href="main/index/data_administrasi_umum" class="btn btn-danger mr-1 text-white"> <i class="fa fa-arrow-left"></i> Kembali </a> 
                    <button type="submit" class="btn btn-primary"><i class="fa fa-check-square-o"></i> Simpan</button>
                  </div>  
                  <?=$csrf->echoInputField();?>

        		    </form>
              </div>
            </div>
        </div>
    </div> 
</div>  