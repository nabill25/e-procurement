<?php
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 *
 */

/* INCLUDE FILE */
$this->load->library("kauth");  $userLogin = new kauth();
$this->load->model(array("Rekanan","Negara"));
$this->load->model("RekananSaham");
$this->load->model("RekananPajak");
$this->load->model("RekananNeraca");
include_once("functions/string.func.php");
include_once("functions/date.func.php");
include_once("functions/default.func.php");

/* create objects */
$rekanan_saham  = new RekananSaham();
$rekanan_pkp  = new Rekanan(); // tipe ?
$rekanan_spt  = new RekananPajak(); // tipe 1
$rekanan_pph  = new RekananPajak(); // tipe 2
$rekanan_ppn  = new RekananPajak(); // tipe 3
$rekanan_neraca = new RekananNeraca();

$rekanan_tahun_select = new RekananNeraca();
$rekanan_tahun = new RekananNeraca();
$rekanan_tahun_selectGet = new RekananNeraca();
$rekanan_get_nama = new Rekanan();


/* VARIABLE */
$reqPaketId = $this->input->post("reqPaketId");
$reqId = $this->input->get("reqId");
$reqCari = $this->input->post("reqCari");
$reqTahunNeraca= $this->input->post('reqTahunNeraca');
$reqTahun = $this->input->post('reqTahun');

if($reqTahun == "")
  $reqTahun = date('Y');

/* VALIDATION */
//echo $reqId.':asd';
// trigger the validation

/* ACTION BY REQMODE */
$allRecord_S = $rekanan_saham->getCountByParams(array("STATUS"=>1, "REKANAN_ID"=>$reqId));
$rekanan_saham->selectByParams(array("STATUS"=>1, "REKANAN_ID"=>$reqId), -1, -1);

$rekanan_get_nama = new Rekanan();
$rekanan_get_nama->selectByParams(array("A.REKANAN_ID"=>$reqId),-1,-1);
$rekanan_get_nama->firstRow();
$tempNama_getNama= $rekanan_get_nama->getField("NAMA");
$reqStatusValidasi = $rekanan_get_nama->getField("STATUS_VALIDASI");
$reqUserStatus = $rekanan_get_nama->getField("USER_STATUS");
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

  </head>

<body>

  <div class="card mb-1">
    <div class="card-content">
      <div class="p-1">
        <div class="alert bg-primary alert-icon-left alert-arrow-left alert-dismissible mb-2" role="alert">
          <span class="alert-icon"><i class="fa fa-th"></i></span>  <strong>Susunan Kepemilikan Saham</strong>
        </div>
        <p class="alert alert-primary" style="background-color: transparent !important;"><b><?= $tempNama_getNama ?></b></p>
        <div class="table-responsive">
          <table class="table table-bordered table-hover">
            <tr class="judul-kolom">
                <th>No.</th>
                <!-- <th>Kepemilikan</th> -->
                <th>Pemegang Saham</th>
                <th>No. KTP</th>
                <th>No. NPWP</th>
                <th>Kewarganegaraan</th>
                <th>Negara</th>
                <th>Alamat</th>
                <th>Persentase</th>
                <th>Nominal Saham</th>
                <!-- <th width="10%">File KTP/NPWP <br>atau Kepemilikan Saham</th> -->
            </tr>
             <?php if($allRecord_S > 0){
              $i = 0;
                        $rcI=0;
              while($rekanan_saham->nextRow()){
            ?>
            <tr>
                <td><?=$rcI+1?></td>
                <!-- <td><?=$rekanan_saham->getField("KEPEMILIKAN")?></td> -->
                <td>
                  <?php if ($rekanan_saham->getField("KEPEMILIKAN") == "Instansi"): ?>
                    <span class="badge badge-info"><?= $rekanan_saham->getField("KEPEMILIKAN") ?></span><br>
                  <?php else: ?>
                    <span class="badge badge-warning"><?= $rekanan_saham->getField("KEPEMILIKAN") ?></span><br>
                  <?php endif; ?>
                  <?=$rekanan_saham->getField("NAMA")?>
                  <?php
                  if ($rekanan_saham->getField("JENIS_KELAMIN") == 'L') {
                    echo '<span class="badge badge-danger btn-xs"><b>Laki-Laki</b></span>';
                  } else {
                    echo '<span class="badge badge-primary btn-xs"><b>Perempuan</b></span>';
                  }
                 ?>
                </td>
                <td><?=$rekanan_saham->getField("KTP")?></td>
                <td><?=$rekanan_saham->getField("NPWP")?></td>
                <td><?=$rekanan_saham->getField("KEWARGANEGARAAN")?></td>
                <td>
                  <?php 
                  if ($rekanan_saham->getField("KEWARGANEGARAAN") == 'Asing') {
                                $negara = new negara();
                                $negara->selectByParams(array("NAMA" => $rekanan_saham->getField("NEGARA")), -1, -1);
                                $negara->firstRow();
                                echo $negara->getField("NAMA");
                          } ?>
                  </td>
                <td><?=$rekanan_saham->getField("ALAMAT")?></td>
                <td align="center"><?=$rekanan_saham->getField("JUMLAH_SAHAM")?>%</td>
                <td align="center"><?=str_replace(",-","",currencyToPage($rekanan_saham->getField("NOMINAL_SAHAM")))?></td>
                <!-- <td> -->
                  <?php
                  // if ($rekanan_saham->getField("PATH_FILE") != '') {
                  ?>
                  <!-- <a href="<?php // echo base_url('uploads/kepemilikan_saham').'/'.$rekanan_saham->getField("PATH_FILE") ?>" target="_blank" class="badge badge-primary"><span class="fa fa-download"></span></a> -->
                  <?php
                  // } else { echo "-"; } 
                  ?>
                <!-- </td> -->

            </tr>
            <?php $i++;$rcI++;}}else{?>
            <tr >
                <td colspan="5">.: data belum ada :.</td>
            </tr>
            <?php }?>
          </table>
        </div>
        <?php 
          if ($this->USER_TYPE_ID == '2' && ($reqStatusValidasi != '1' || $reqUserStatus == '0')) { ?>
          <form id="ff2" class="easyui-form " method="post" novalidate enctype="multipart/form-data">
            <div class="form-actions card-content collapse show border-info border-darken-2 mt-2">
              <div class="card-body">
                <?php
                $checked = '';
                $cekData = new Rekanan();
                $cekData->selectByParamsRekananChecklist(array("REKANANID"=>$reqId),-1,-1);
                $cekData->firstRow();
                if ($cekData->getField("saham") == '1') {
                  $checked = 'checked';
                }
                echo '<input class="mb-1" type="checkbox" name="saham" id="checkjenis" onclick="return updateChecklist(\''.$reqId.'\',\'saham\')" style="cursor:pointer" '.$checked.'> Ya, Lengkap ';
                ?>
                <input type="" class="form-control easyui-validatebox span2" required maxlength="255" style="height: 30px !important;" name="" placeholder="catatan" id="catatanjenis" value="<?=$cekData->getField("saham_note")?>" onChange="return updateChecklist('<?= $reqId ?>','saham')">
                <small><sup>*</sup>&nbsp;Tekan enter setelah mengisi catatan</small>
              </div>
            </div>
          </form>
          <?php 
          } ?>
      </div>
    </div>
  </div>


    <script src="lib/bootstrap/libs/jquery/1.12.4/jquery.min.js"></script>
    <script>window.jQuery || document.write('<script src="lib/bootstrap/assets/js/vendor/jquery.min.js"><\/script>')</script>
    <script src="lib/bootstrap/dist/js/bootstrap.min.js"></script>
    <link rel="stylesheet" type="text/css" href="<?=base_url()?>lib/eproc/themes/default/easyui.css">
    <script type="text/javascript" src="<?=base_url()?>lib/eproc/jquery.easyui.min.js"></script>
    <script type="text/javascript" src="<?=base_url()?>lib/eproc/kalender-easyui.js"></script>
    <script type="text/javascript" src="<?=base_url()?>lib/eproc/allfunc.js"></script>

    <script type="text/javascript">
    function updateChecklist(rekananid,jenis) {
      var n = $("#checkjenis:checked").length;
      if (n == 1) {
          $('#catatanjenis').validatebox({ required:false  });
          $('#catatanjenis').val('');
      } else {
          $('#catatanjenis').validatebox({ required:true  });
      }
      var c = $("#catatanjenis").val();
      // alert(n+'-'+c+'-'+rekananid+'-'+jenis); return false;
        $.getJSON("rekanan_json/updateChecklist2/?rekananid="+rekananid+"&jenis="+jenis+"&status="+n+"&catatan="+c,
          function(data){
            if (data.RESPONSE === 'Gagal') {
              $.messager.alert('Info', data.PESAN, 'info');
              if (n === 0) { // kalau gagal balik ke awal
                $("#checkjenis").prop("checked", true);
              } else {
                $("#checkjenis").prop("checked", false);
              }
            }
        });
    }
    </script>

  </body>
</html>
