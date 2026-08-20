<?php
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 * 
 */

/* INCLUDE FILE */
$this->load->library("paketinfo"); $paketInfo = new paketinfo();
$this->load->model("Rekanan");
include_once("functions/string.func.php");
include_once("functions/date.func.php");
include_once("functions/default.func.php");

$this->load->model("RekananRekeningKoran");
$this->load->model("MataUang");
$this->load->model("Bank");

/* create objects */
$rekanan = new Rekanan();
$rekanan_koran = new RekananRekeningKoran();
$mata_uang = new MataUang();
$bank = new Bank();

$reqRekeningKoranId= $this->input->get("reqRekeningKoranId");

$reqPaketId = $this->input->get("reqPaketId");
$reqId = $this->ID;

$rekanan_koran->selectByParams(array("REKANAN_REKENING_KORAN_ID"=>$reqRekeningKoranId, "REKANAN_ID" => $this->ID),-1,-1);
$rekanan_koran->firstRow();

$reqNoRekening = $rekanan_koran->getField('NOMOR');
$reqNamaBank = $rekanan_koran->getField('NAMA');
$reqBulan = $rekanan_koran->getField('BULAN');
$reqAuditor = $rekanan_koran->getField('TAHUN');
$reqMataUang = $rekanan_koran->getField('MATA_UANG_ID');
$reqNilai = $rekanan_koran->getField('NILAI');
$reqRekeningKoranId = $rekanan_koran->getField('REKANAN_REKENING_KORAN_ID');
$reqLinkFileTemp= $rekanan_koran->getField("PATH_FILE");
$reqLinkFileTempTipe= $rekanan_koran->getField("TIPE");
$reqLinkFileTempUkuran= $rekanan_koran->getField("UKURAN");
$reqBankId = $rekanan_koran->getField("BANK_ID");

$bank = new Bank();
$bank->selectByParams();

if($reqRekeningKoranId=='')
	$reqMode='insert';
else 
	$reqMode='update';



//rekening koran
$paketInfo->getPaket($reqPaketId);

$reqTahun = getYear($paketInfo->tanggal_tahap);
$reqBulan = (int)getMonth($paketInfo->tanggal_tahap);												
$year = $year1 = $year2 = $reqTahun;

$arrSyaratBulan = explode(", ",$paketInfo->syarat_rekening_koran_bulan);

$info_bulan='';
for($i=0; $i < 3; $i++){
	$loop_rekening_koran = new RekananRekeningKoran();
	$loop_rekening_koran->selectByParams(array("REKANAN_ID"=>$reqId, 'BULAN || TAHUN'=>$arrSyaratBulan[$i]));
	$loop_rekening_koran->firstRow();
	if($loop_rekening_koran->getField("BULAN_TAHUN") == ''){
		if($info_bulan == '')
			$info_bulan .= getNameMonth(substr($arrSyaratBulan[$i],0,-4))." ".substr($arrSyaratBulan[$i],strlen($arrSyaratBulan[$i])-4,strlen($arrSyaratBulan[$i]));
		else
			$info_bulan .= ', '.getNameMonth(substr($arrSyaratBulan[$i],0,-4))." ".substr($arrSyaratBulan[$i],strlen($arrSyaratBulan[$i])-4,strlen($arrSyaratBulan[$i]));
	}
		
	unset($loop_rekening_koran);
}

?>


<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <base href="<?=base_url();?>" />
    
    <link rel="icon" href="../../favicon.ico">

    <title><?= SYSTEM_NAME.' | '.SYSTEM_NAME_PT ?></title>

    <!-- Bootstrap core CSS -->
    <link href="lib/bootstrap/dist/css/bootstrap.css" rel="stylesheet">

    
    <link rel="stylesheet" href="css/gaya.css" type="text/css">
    <link rel="stylesheet" href="css/gaya-bootstrap.css" type="text/css">
    
    <!-- FONT AWESOME -->
    <link rel="stylesheet" href="lib/font-awesome-4.7.0/css/font-awesome.css" type="text/css">
    
    <!-- PAGINATION -->
    <link rel="stylesheet" type="text/css" href="lib/drupal/index.css" />  	

    <!-- EASYUI -->
    <link rel="stylesheet" type="text/css" href="lib/eproc/themes/default/easyui.css">
	<script type="text/javascript" src="js/jquery-1.6.1.min.js"></script>
    <script type="text/javascript" src="lib/eproc/jquery.easyui.min.js"></script>
    <script type="text/javascript" src="lib/eproc/kalender-easyui.js"></script>
    <script type="text/javascript" src="lib/eproc/allfunc.js"></script>

	<script type="text/javascript">
    $(document).ready(function() {
         $(function(){
            $('#ff').form({
                url:'rekanan_rekening_koran_json/data_keuangan_rekening_koran_syarat',
                onSubmit:function(){
                    return $(this).form('validate');
                },
                success:function(data){
					//alert(data);return false;
					if(data == "1")
						top.setElementValue('reqDataRekeningKoranLabel','Data Lengkap');
						
					top.reloadRekeningKoran();
					top.closePopup();
                }
            });
            
        });
        
    });
    </script>
 </head>


<body class="body-popup">
	<div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="area-main popup">
                    <div class="judul-halaman">Data Keuangan - Rekening Koran</div>
                    <div class="inner">
                        <div class="area-konten">
                            
                            <div class="area-konten-inner">

							<?php
                            if($info_bulan == "")
							{}
							else
							{
							?>
                            <div class="informasi">
                                <ul>
                                    <li>Silahkan melengkapi terlebih dahulu Rekening Koran bulan <?=$info_bulan?>.</li>
                                </ul>
                            </div>                            
                            <?php
							}
							?>
                                <form id="ff" class="easyui-form form-horizontal" method="post" data-options="novalidate:true" enctype="multipart/form-data">
                                    
                                    <div class="judul-grup">Data Rekening Koran</div>
                                    
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="inputEmail" class="col-md-3 control-label harus-diisi">a. Nomor Rekening</label>
                                                <div class="col-md-8">
                                                  <input name="reqNoRekening" type="text" title="Nomor rekening harus diisi" class="form-control easyui-validatebox"  required style="width:250px" id="reqNoRekening" value="<?=$reqNoRekening?>" />
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="inputEmail" class="col-md-3 control-label harus-diisi">b. Bank</label>
                                                <div class="col-md-8">
                                                    <input type="text" name="reqBankId" class="easyui-combobox"  
                                                            data-options="valueField:'id',textField:'text',url:'bank_json/combo', required:true"  value="<?=$reqBankId?>" />
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="inputEmail" class="col-md-3 control-label">c. Bulan</label>
                                                <div class="col-md-8">
                                                  <select name="reqBulan" id="reqBulan" class="easyui-combobox">
                                                    <option value="1" <?php if($reqBulan == 1) echo "selected";?>>Januari</option>
                                                    <option value="2" <?php if($reqBulan == 2) echo "selected";?>>Februari</option>
                                                    <option value="3" <?php if($reqBulan == 3) echo "selected";?>>Maret</option>
                                                    <option value="4" <?php if($reqBulan == 4) echo "selected";?>>April</option>
                                                    <option value="5" <?php if($reqBulan == 5) echo "selected";?>>Mei</option>
                                                    <option value="6" <?php if($reqBulan == 6) echo "selected";?>>Juni</option>
                                                    <option value="7" <?php if($reqBulan == 7) echo "selected";?>>Juli</option>
                                                    <option value="8" <?php if($reqBulan == 8) echo "selected";?>>Agustus</option>
                                                    <option value="9" <?php if($reqBulan == 9) echo "selected";?>>September</option>
                                                    <option value="10" <?php if($reqBulan == 10) echo "selected";?>>Oktober</option>
                                                    <option value="11" <?php if($reqBulan == 11) echo "selected";?>>November</option>
                                                    <option value="12" <?php if($reqBulan == 12) echo "selected";?>>Desember</option>
                                                </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="inputEmail" class="col-md-3 control-label">d. Tahun</label>
                                                <div class="col-md-8">
                                                    <input name="reqAuditor" type="text" id="reqAuditor" value="<?=$reqAuditor?>" style="width:100px;" maxlength="4" class="form-control easyui-validatebox"  required />
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="inputEmail" class="col-md-3 control-label">e. Mata Uang</label>
                                                <div class="form-kolom">
                                                  <input type="text" name="reqMataUang" style="width:300px" class="form-control easyui-combobox" 
                                                            data-options="valueField:'id',textField:'text',url:'mata_uang_json/combo', required:true" value="<?=$reqMataUang?>" />
                                                </div>
                                                
                                                <div class="form-kolom">
                                                 <input style="width:280px" name="reqNilai" class="form-control easyui-validatebox" type="text" id="reqNilai" value="<?=$reqNilai?>" 
                                                     OnFocus="FormatAngka('reqNilai')" OnKeyUp="FormatUang('reqNilai')" OnBlur="FormatUang('reqNilai')" required/>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="inputEmail" class="col-md-3 control-label">f. File *</label>
                                                <div class="col-md-8">
                                                      <input type="hidden" name="reqLinkFileTemp" value="<?=$reqLinkFileTemp?>" />
                                                      <input type="hidden" name="reqLinkFileTempTipe" value="<?=$reqLinkFileTempTipe?>" />
                                                      <input type="hidden" name="reqLinkFileTempUkuran" value="<?=$reqLinkFileTempUkuran?>" />
                                                      <input type="file" name="reqLinkFile" id="reqLinkFilePDF" size="30" class="easyui-validatebox" <?php if($reqLinkFileTemp == "") { ?> required <? } ?> validType="fileType['pdf']" />
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="inputEmail" class="col-md-3 control-label">&nbsp;</label>
                                                <div class="col-md-4">
                                                    <input type="hidden" name="reqPaketId" value="<?=$reqPaketId?>" />
                                                    <input type="hidden" name="reqMode" value="<?=$reqMode?>" />
                                                    <input type="hidden" name="reqRekeningKoranId" value="<?=$reqRekeningKoranId?>" />
                                                    <button type="submit" class="btn btn-primary">Simpan</button>
                                                    <button type="button" class="btn btn-primary" onClick="top.closePopup()">Batal</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                </form>
                            
                            </div>
                            
                        </div>
                    </div>
                </div>
            </div>
        </div>
	</div>        
</body>