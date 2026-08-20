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
$this->load->model("Rekanan");
$this->load->library("paketinfo"); $paketInfo = new paketinfo();
$this->load->model("PaketRekanan");
$this->load->model("PaketPernyataanMinat");
$this->load->model("PaketPaktaIntegritas");

/* create objects */
$rekanan = new Rekanan();
$paket_rekanan = new PaketRekanan();
$paket_pernyataan_minat = new PaketPernyataanMinat();
$paket_pakta_integritas = new PaketPaktaIntegritas();

/* VARIABLE */
$reqId = $this->input->get('reqId');
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
                url:'paket_pakta_integritas_json/add',
                onSubmit:function(){
                    return $(this).form('validate');
                },
                success:function(data){
                  // $.messager.alert('Info', data, 'info'); 
        					if(data == "1")
        						top.setPaktaIntegritas();
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
        <span class="alert-icon"><i class="fa fa-th"></i></span>  <strong>Pakta Integritas</strong>
      </div> 
      <div class="p-1">
        <form id="ff" class="easyui-form form-horizontal" method="post" data-options="novalidate:true" enctype="multipart/form-data">
            
          <table class="table table-bordered table-hover">
              <tr class="gelap">
                <td width="21%" colspan="3">Dengan ini saya menyatakan :</td>
              </tr>
              <tr class="gelap">
                <td width="2%" valign="top">1</td>
                <td width="90%" align="justify">Tidak akan melakukan praktek KKN;</td>
                <td width="5%" valign="top"><input type="checkbox" style="cursor: pointer;" name="reqCheck1" id="reqCheck1" value="1" onclick="countChecked()" /></td>
              </tr>
              <tr class="gelap">
                <td width="2%" valign="top">2</td>
                <td width="90%" align="justify">Akan melaporkan kepada pihak yang berwajib/berwenang apabila mengetahui ada indikasi KKN di dalam proses pengadaan/pekerjaan ini;</td>
                <td width="5%" valign="top"><input type="checkbox" style="cursor: pointer;" name="reqCheck2" id="reqCheck2" value="1" onclick="countChecked()" /></td>
              </tr>
              <tr class="gelap">
                <td width="2%" valign="top">3</td>
                <td width="90%" align="justify">Dalam proses pengadaan/pekerjaan ini, berjanji akan melaksanakan tugas secara bersih, transparan, dan professional dalam arti akan mengarahkan segala kemampuan dan sumber daya secara optimal untuk memberikan hasil kerja terbaik mulai dari penyiapan penawaran, pelaksanaa dan penyelesaian pekerjaan/kegiatan ini;</td>
                <td width="5%" valign="top"><input type="checkbox" style="cursor: pointer;" name="reqCheck3" id="reqCheck3" value="1" onclick="countChecked()" /></td>
                </td>
              </tr>
              <tr class="gelap">
                <td width="2%" valign="top">4</td>
                <td width="90%" align="justify">Apabila saya melanggar hal – hal yang telah saya nyatakan dalam PAKTA INTEGRITAS ini, saya bersedia dikenakan sanksi moral, sanksi administrasi serta dituntut ganti rugi dan pidana sesuai dengan ketentuan peraturan perundang – undangan yang berlaku;</td>
                <td width="5%" valign="top"><input type="checkbox" style="cursor: pointer;" name="reqCheck4" id="reqCheck4" value="1" onclick="countChecked()" /></td>
              </tr>
                </table>
                <script>
                function countChecked() {
                  var n1 = $("#reqCheck1:checked").length;
                  var n2 = $("#reqCheck2:checked").length;
                  var n3 = $("#reqCheck3:checked").length;
                  var n4 = $("#reqCheck4:checked").length;
                  //alert(n);
                  if(n1 && n2 && n3 && n4){
                      $("#reqSubmit").show(0);
                  }else{
                      $("#reqSubmit").hide(0);
                  }
                }
                </script>  
                <div class="area-tombol">
                  <div id="reqSubmit" style="display:none; text-align:center;">
                    <input type="hidden" name="reqId" value="<?=$reqId?>" />
                    <input type="hidden" name="submitSimpan" value="Simpan"/>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                  </div>
                </div>
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
