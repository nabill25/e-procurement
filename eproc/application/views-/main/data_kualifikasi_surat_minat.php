<?php
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 * 
 */
if($this->USER_TYPE_ID == "")
  redirect("main");

include_once("functions/date.func.php");
include_once("functions/default.func.php");
/* INCLUDE FILE */
$this->load->library("paketinfo"); $paketInfo = new paketinfo();
$this->load->model("Rekanan");
$this->load->model("PaketRekanan");
$this->load->model("PaketPernyataanMinat");

/* create objects */
$rekanan = new Rekanan();
$paket_rekanan = new PaketRekanan();
$paket_pernyataan_minat = new PaketPernyataanMinat();

/* VARIABLE */
$reqId = $this->input->get('reqId');

$FILE_DIR = "uploads/kualifikasi/";

/* VARIABLE */
$reqPaketRekananId = $paket_rekanan->getPaketRekananId($reqId, $this->ID);
if($reqPaketRekananId == "")
{
		echo '<script language="javascript">';
		echo 'alert("Anda tidak berhak mengakses halaman ini. IP address anda telah kami catat sebagai rekanan yang mencoba membuka halaman administrator.");';
		echo 'top.closePopup();';
		echo '</script>';
		exit;	
}

$paketInfo->getPaket($reqId);

$rekanan->selectByParamsRekananPengurusDirut(array("A.REKANAN_ID" => $this->ID));   
$rekanan->firstRow(); 
$tempNama = $rekanan->getField("NAMA");
$tempJabatan =  $rekanan->getField("JABATAN");
$tempAlamat =  $rekanan->getField("ALAMAT");
$tempTelepon =  $rekanan->getField("TELEPON");
$tempEmail =  $rekanan->getField("EMAIL");
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

    <link rel="stylesheet" type="text/css" href="<?=base_url() ?>assets/new/css/vendors.css">
    <link rel="stylesheet" type="text/css" href="<?=base_url() ?>assets/new/vendors/css/ui/prism.min.css">
    <!-- END VENDOR CSS-->
    <!-- BEGIN ROBUST CSS-->
    <link rel="stylesheet" type="text/css" href="<?=base_url() ?>assets/new/css/app.css">
    <!-- <link rel="stylesheet" type="text/css" href="<?=base_url() ?>css/core.css"> -->
    <!-- END ROBUST CSS-->
    <!-- BEGIN Page Level CSS-->
    <link rel="stylesheet" type="text/css" href="<?=base_url() ?>assets/new/css/core/menu/menu-types/horizontal-menu.css">
    <link rel="stylesheet" type="text/css" href="<?=base_url() ?>assets/new/css/core/colors/palette-gradient.css">
    <link rel="stylesheet" type="text/css" href="<?=base_url() ?>assets/new/css/core/colors/palette-callout.css">
    <!-- END Page Level CSS-->
    <!-- BEGIN Custom CSS-->
    <link rel="stylesheet" type="text/css" href="<?=base_url() ?>assets/new/css/style.css">

    <!-- EASYUI -->
    <link rel="stylesheet" type="text/css" href="<?=base_url()?>lib/eproc/themes/default/easyui.css">
    <script type="text/javascript" src="js/jquery-1.6.1.min.js"></script>
    <script type="text/javascript" src="<?=base_url()?>lib/eproc/jquery.easyui.min.js"></script>
    <script type="text/javascript" src="<?=base_url()?>lib/eproc/kalender-easyui.js"></script>
    <script type="text/javascript" src="<?=base_url()?>lib/eproc/allfunc.js"></script>

	<script type="text/javascript">
    $(document).ready(function() {
        
        $(function(){
            $('#ff').form({
                url:'paket_pernyataan_minat_json/add',
                onSubmit:function(){
                    return $(this).form('validate');
                },
                success:function(data){
					if(data == "1")
						top.setSuratPernyataanMinat();
	
					top.closePopup();
                }
            });
            
        });
        
    });
    </script>

</head>


<body>

<div class="card mb-1 border-darken-1">
  <div class="card-content">
    <div class="p-1">
      <div class="alert bg-primary alert-icon-left alert-arrow-left alert-dismissible mb-2" role="alert">
        <span class="alert-icon"><i class="fa fa-th"></i></span>  <strong>Surat Pernyataan Minat</strong>
      </div> 
      <div class="p-1">
        <form id="ff" class="easyui-form form-horizontal" method="post" data-options="novalidate:true" enctype="multipart/form-data">
            
          <table class="table table-striped table-hover">
            <tr class="gelap">
              <td width="21%">a. Nama<span class="merah">*</span></td>
              <td width="2%">:</td>
              <td width="77%">
              <input class="span8" name="reqNama" type="text" title="Nama harus diisi" class="required form-controls" id="reqNama" value="<?=$tempNama?>" size="50" />
              </td>
            </tr>
            <tr class="terang">
              <td width="21%">b. Jabatan<span class="merah">*</span></td>
              <td width="2%">:</td>
              <td width="77%">
              <input class="span8" name="reqJabatan" type="text" title="Jabatan harus diisi" class="required form-controls" id="reqJabatan" value="<?=$tempJabatan?>" size="50" />
              </td>
            </tr>
            <tr class="gelap">
              <td width="21%">c. Alamat<span class="merah">*</span></td>
              <td width="2%">:</td>
              <td width="77%">
              <input class="span8" name="reqAlamat" type="text" title="Alamat harus diisi" class="required form-controls" id="reqAlamat" value="<?=$tempAlamat?>" size="50" />
              </td>
            </tr>
            <tr class="terang">
              <td width="21%">d. Telepon/Fax<span class="merah">*</span></td>
              <td width="2%">:</td>
              <td width="77%">
              <input class="span8" name="reqTelepon" type="text" title="Telepon/Fax harus diisi" class="required form-controls" id="reqTelepon" value="<?=$tempTelepon?>" size="50" />
              </td>
            </tr>
            <tr class="gelap">
              <td width="21%">e. Email<span class="merah">*</span></td>
              <td width="2%">:</td>
              <td width="77%">
              <input class="span8" name="reqEmail" type="text" title="Email harus diisi" class="required form-controls" id="reqEmail" value="<?=$tempEmail?>" size="50" />
              </td>
            </tr>
            <tr class="terang">
              <td width="21%" colspan="3"><strong>Apabila surat pernyataan minat dikuasakan, isi form berikut : </strong></td>                    
            </tr>
            <tr class="gelap">
              <td width="21%">a. Penerima Kuasa</td>
              <td width="2%">:</td>
              <td width="77%">
              <input class="span8" name="reqPenerimaKuasa" type="text" id="reqPenerimaKuasa" value="" size="50" />
              </td>
            </tr>
            <tr class="terang">
              <td width="21%">b. Jabatan</td>
              <td width="2%">:</td>
              <td width="77%">
              <input class="span8" name="reqPenerimaKuasaJabatan" type="text" id="reqPenerimaKuasaJabatan" value="" size="50" />
              </td>
            </tr>
            <tr class="gelap">
              <td width="21%">c. No. KTP</td>
              <td width="2%">:</td>
              <td width="77%">
              <input class="span8" name="reqPenerimaKuasaNoKTP" type="text" id="reqPenerimaKuasaNoKTP" value="" size="50" />
              </td>
            </tr>
            <tr class="terang">
              <td width="21%" valign="top">d. Upload Surat Kuasa</td>
              <td width="2%" valign="top">:</td>
              <td width="77%">
              <input class="span8" name="reqPenerimaKuasaUpload" type="file" id="reqPenerimaKuasaUpload" value="" size="50" />
              <br />Apabila dikuasakan, anda <strong>wajib</strong> mengupload scan dokumen surat kuasa dan ktp yang diberi kuasa dalam 1(satu) file pdf.
              </td>
            </tr>
              </table>
              <table width="100%" border="0" cellpadding="2" cellspacing="2">
              <tr>
                  <td valign="top" style="width: 100px">
                      <input name="reqSetuju" type="checkbox" id="chk_agreement" accesskey="e"/>
                  </td>
                  <td align="justify">
                      Dengan ini saya menyatakan dengan sebenarnya bahwa setelah mengetahui pengadaan yang akan dilaksanakan oleh <?=$paketInfo->unit_kerja?>,
                      saya berminat untuk mengikuti proses pelelangan pekerjaan "<strong><?=$paketInfo->nama?></strong>" sampai dengan selesai.
                  </td>
                </tr>
                <tr> 
                  <td align="justify" colspan="2">
                      <input type="hidden" name="reqId" value="<?=$reqId?>" />
                      <input type="hidden" name="reqPaketId" value="<?=$reqPaketId?>" />
                      <input type="hidden" name="reqIjinUsahaId" value="<?=$tempIjinUsahaId?>" />
                      <input type="hidden" name="reqTipe" value="<?=$reqTipe?>" />
                      <input type="hidden" name="reqPaketRekananId" value="<?=$reqPaketRekananId?>" />
                      <input type="hidden" name="submitSimpan" value="Simpan"/>
                      <button type="submit" class="btn btn-primary">Simpan</button>
                  </td>
                </tr>
               </table> 
              <script>
              function countChecked() {
                var n = $("#chk_agreement:checked").length;
                //alert(n);
                if(n){
                    $("#reqSubmit").show(0);
                }else{
                    $("#reqSubmit").hide(0);
                }
              }
              $("#chk_agreement").click(countChecked);
              </script>  
              <!-- <div class="area-tombol">
              <div id="reqSubmit" style="display:none; text-align:center; ">
              </div>
              </div> -->
              </td>
            </tr>
            <tr>
              <td colspan="3">&nbsp;</td>
            </tr>
          </table>
        </form>
      </div>
    </div>
  </div>
</div>

</body>