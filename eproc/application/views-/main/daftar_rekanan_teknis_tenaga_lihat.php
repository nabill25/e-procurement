<?php
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 *
 */

$this->load->library("kauth");  $userLogin = new kauth();
$this->load->model("Rekanan");
$this->load->model("RekananTenagaAhliSertifikat");
$this->load->model("RekananTenagaAhli");
$this->load->model("RekananTenagaAhliPengalaman");
$this->load->model("RekananDaftarTenagaAhli");
include_once("functions/string.func.php");
include_once("functions/date.func.php");
include_once("functions/default.func.php");

$rekanan = new Rekanan();
$rekanan_tenaga_ahli = new RekananTenagaAhli();
$rtap = new RekananTenagaAhliPengalaman();
$rekanan_tasertifikat = new RekananTenagaAhliSertifikat();

$reqId = $this->input->get("reqId");
$reqPaketId =  $this->input->get("reqPaketId") ?: 0;

$reqSubmit = $this->input->post("reqSubmit");
$reqCatatan = $_POST["reqCatatan"];
$reqDaftarId = $_POST["reqDaftarId"];

$FILE_DIR = "uploads/tenaga_ahli_sertifikat/";

if($reqSubmit == "Submit")
{
    for($i=0;$i<count($reqDaftarId);$i++)
    {
        $rekanan_daftar_tenaga_ahli = new RekananDaftarTenagaAhli();
        $rekanan_daftar_tenaga_ahli->setField("CATATAN", $reqCatatan[$i]);
        $rekanan_daftar_tenaga_ahli->setField("REKANAN_DAFTAR_TENAGA_AHLI_ID", $reqDaftarId[$i]);
        $rekanan_daftar_tenaga_ahli->updateCatatan();
        unset($rekanan_daftar_tenaga_ahli);
    }
}

//
// if($reqPaketId == "")
//     $statement = "";
// else
//     $statement = " AND EXISTS(SELECT 1 FROM REKANAN_DAFTAR_TENAGA_AHLI X WHERE X.REKANAN_TENAGA_AHLI_ID = REKANAN_TENAGA_AHLI_ID AND X.PAKET_ID = '".$reqPaketId."') ";

$allRecord = 1;
// $rs = $rekanan_tenaga_ahli->selectByParamsSyarat(array('B.REKANAN_ID'=>$reqId, "B.PAKET_ID" => $reqPaketId), -1, -1, $statement);
$allRecord = $rekanan_tenaga_ahli->getCountByParams(array('REKANAN_ID'=>$reqId), $statement);
$rs = $rekanan_tenaga_ahli->selectByParams(array('REKANAN_ID'=>$reqId), -1, -1, $statement);
$whereIn = NULL;
// foreach ($rs as $v)
// {
//     // prepare IN query
//     $whereIn .= "'".$v['REKANAN_TENAGA_AHLI_ID']."',";
// }
// $whereIn = "(".trim($whereIn,',').")";
// $rsp = $rtap->selectByParamsExtended(array('REKANAN_TENAGA_AHLI_ID'=>array($whereIn,FALSE,'IN')));
//
// if (is_array($rsp)) {
//     foreach ($rsp as $v) {
//         $dpengalaman[$v['REKANAN_TENAGA_AHLI_ID']][] = $v;
//     }
// }

$rekanan->selectByParams(array("A.REKANAN_ID"=>$reqId),-1,-1);
$rekanan->firstRow();
$tempNama= $rekanan->getField("NAMA");
$reqStatusValidasi = $rekanan->getField("STATUS_VALIDASI");
$reqUserStatus = $rekanan->getField("USER_STATUS");
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
    <link rel="stylesheet" type="text/css" href="<?=base_url() ?>assets/new/css/app.css">
    <link rel="stylesheet" type="text/css" href="<?=base_url() ?>assets/new/css/core/menu/menu-types/horizontal-menu.css">
    <link rel="stylesheet" type="text/css" href="<?=base_url() ?>assets/new/css/core/colors/palette-gradient.css">
    <link rel="stylesheet" type="text/css" href="<?=base_url() ?>assets/new/css/core/colors/palette-callout.css">
    <link rel="stylesheet" type="text/css" href="<?=base_url() ?>assets/new/css/style.css">
    <script language="JavaScript" src="<?=base_url() ?>jslib/elementDis.js"></script>
  </head>
<body>

  <div class="card mb-1">
    <div class="card-content">
      <div class="p-1">
        <div class="alert bg-primary alert-icon-left alert-arrow-left alert-dismissible mb-2" role="alert">
          <span class="alert-icon"><i class="fa fa-th"></i></span>  <strong>TENAGA AHLI</strong>
        </div>
        <p class="alert alert-primary" style="background-color: transparent !important;"><b><?= $tempNama ?></b></p>
        <div class="table-responsive">
            <form action="" name="frmDaftarAlamat" method="post" enctype="multipart/form-data">
                <table class="table table-bordered" border="0" cellpadding="2" cellspacing="1" id="tbl_bidang">
                  <tbody>
                    <tr>
                        <th style="width:10px">No</th>
                        <th>Nama</th>
                        <th>No. KTP</th>
    										<th>No. NPWP</th>
    										<th>Tempat Tanggal Lahir</th>
    										<th>Alamat</th>
                    </tr>
                    <?php
                    $i = 0;
                    if($allRecord > 0)
                    {
                        while($rekanan_tenaga_ahli->nextRow())
                        {
                    ?>
                            <tr class="<?=$style?>">
                                 <td align="center" valign="top"><?=$i+1?></td>
                                 <td valign="top"> <a onclick="displayElement('reqDetil<?=$i?>')" style="cursor:pointer" id="rekanan<?=$i?>">
                                    <?=$rekanan_tenaga_ahli->getField("NAMA")?>
                                    <?php
                    								if ($rekanan_tenaga_ahli->getField("JENIS_KELAMIN") == 'L') {
                                      echo '<span class="badge badge-danger btn-xs"><b>Laki-Laki</b></span>';
                                    } else {
                                      echo '<span class="badge badge-primary btn-xs"><b>Perempuan</b></span>';
                                    }
                    							 ?> <br>
                                    <span class="badge badge-primary"><i class="fa fa-eye"></i> <small>Lihat detil</small></span>
                                </td>
                                <td valign="top"><?=$rekanan_tenaga_ahli->getField("KTP")?></td>
                                <td valign="top"><?=$rekanan_tenaga_ahli->getField("NPWP")?></td>
                                <td valign="top"><?=$rekanan_tenaga_ahli->getField("TEMPAT_LAHIR")?>, <?=getFormattedDate($rekanan_tenaga_ahli->getField("TANGGAL_LAHIR"))?></td>
                               <td valign="top"><?=$rekanan_tenaga_ahli->getField("ALAMAT")?></td>
                            </tr>
                            <tr id="reqDetil<?=$i?>" style="display:none;">
                                <td colspan="5">

                                <div class="area-show-hide-konten">
                                 <table class="table-bordered">
                                    <tr style="background: #b3b3b3; font-weight: bold">
                                        <td width="10px" style="text-align:center">No</td>
                                        <td style="text-align:center">Pendidikan</td>
                                        <td style="text-align:center" colspan="5">Jurusan</td>
                                      </tr>
                                    <?php
                                    $array_pendidikan = explode("* ",$rekanan_tenaga_ahli->getField("PENDIDIKAN"));
                                    //print_r($array_pendidikan);
                                    $x=0;
                                    while($x < count($array_pendidikan)){
                                    $array_pendidikan_isi = explode("-",$array_pendidikan[$x]);

                                    $nmJurusan = str_replace("(","",$array_pendidikan_isi[0]);
                                    $nmPendidikan = str_replace(")","",$array_pendidikan_isi[1]);
                                    ?>
                                    <tr class="judul-kolom4">
                                        <td width="10px" style="text-align:center"><?=$x+1?></td>
                                        <td style="text-align:center"><?=$nmJurusan?></td>
                                        <td style="text-align:center" colspan="5"><?=$nmPendidikan?></td>
                                      </tr>
                                    <?php $x++;}?>
                                    <tr style="background: #b3b3b3; font-weight: bold">
                                        <td width="10px" style="text-align:center">No</td>
                                        <td width="100px" style="text-align:center">Nama Proyek</td>
                                        <td width="100px" style="text-align:center">Posisi/Jabatan</td>
                                        <td width="105px" style="text-align:center">Periode/Lama</td>
                                        <td width="50px" style="text-align:center">Tahun</td>
                                        <td width="100px" style="text-align:center">Instansi</td>
                                        <td width="50px" style="text-align:center">Nama Perusahaan</td>
                                      </tr>
                                  <?php
                                     $rekanan_tenaga_ahli_pengalaman = new RekananTenagaAhliPengalaman();
                                     $rekanan_tenaga_ahli_pengalaman->selectByParams(array('REKANAN_TENAGA_AHLI_ID'=>$rekanan_tenaga_ahli->getField("REKANAN_TENAGA_AHLI_ID")), -1, -1, $statement);
                                     $y=1;
                                     while($rekanan_tenaga_ahli_pengalaman->nextRow())
                                  {
                                  ?>
                                    <tr class="judul-kolom4">
                                        <td width="10px" style="text-align:center"><?=$y?></td>
                                        <td style="text-align:center"><?=$rekanan_tenaga_ahli_pengalaman->getField("PEKERJAAN") ?></td>
                                        <td style="text-align:center"><?=$rekanan_tenaga_ahli_pengalaman->getField("POSISI") ?></td>
                                        <td style="text-align:center"><?=$rekanan_tenaga_ahli_pengalaman->getField("PERIODE") ?></td>
                                        <td style="text-align:center"><?=$rekanan_tenaga_ahli_pengalaman->getField("PENGALAMAN") ?></td>
                                        <td style="text-align:center"><?=$rekanan_tenaga_ahli_pengalaman->getField("INSTANSI") ?></td>
                                        <td style="text-align:center"><?=$rekanan_tenaga_ahli_pengalaman->getField("NAMA_PERUSAHAAN") ?></td>
                                    </tr>
                                    <?php
                                       $y++;
                                     }
                                     unset($rekanan_tenaga_ahli_pengalaman);
                                    ?>

                                    <tr style="background: #b3b3b3; font-weight: bold">
                                            <th width="10px" style="text-align:center">No</th>
                                            <th width="100px" style="text-align:center"><?=translate("Keahlian", "Expertise")?></th>
                                            <th width="300px" style="text-align:center"><?=translate("No. Serifikat", "Cert. Number")?></th>
                                            <th width="200px" style="text-align:center">File Sertifikat</th>
                                            <th width="100px" style="text-align:center"><?=translate("Instansi/Penerbit", "Expertise")?></th>
                                            <th width="100px" style="text-align:center"><?=translate("Tanggal Berlaku", "Expertise")?></th>
                                      </tr>
                                       <?php
                                      // $array_keahlian = explode(" # ",$rekanan_tenaga_ahli->getField("SERTIFIKAT"));
                                     // echo $array_keahlian->query;exit;
                                      // echo "<pre>"; print_r($array_keahlian);
                                     $rekanan_tenaga_ahli_sertifikat = new RekananTenagaAhliSertifikat();
                                     $rekanan_tenaga_ahli_sertifikat->selectByParams(array('REKANAN_TENAGA_AHLI_ID'=>$rekanan_tenaga_ahli->getField("REKANAN_TENAGA_AHLI_ID")), -1, -1);

                                     $y=1;
                                     while($rekanan_tenaga_ahli_sertifikat->nextRow())
                                    {
                                        $FILE_DIR = "uploads/tenaga_ahli_sertifikat/";
                                      // for($i=0;$i<count($array_keahlian);$i++)
                                      // {
                                      ?>
                                      <tr class="judul-kolom4">
                                        <td width="10px" style="text-align:center"><?=$i+1?></td>
                                      <td><?= $rekanan_tenaga_ahli_sertifikat->getField("KEAHLIAN") ?></td>
                                      <td><?= $rekanan_tenaga_ahli_sertifikat->getField("NOMOR") ?></td>

                                      <td>
                                        <a href=" <?= $FILE_DIR.$rekanan_tenaga_ahli_sertifikat->getField("PATH_FILE") ?>" class="badge badge-primary" target="_blank">
                                            <?= $rekanan_tenaga_ahli_sertifikat->getField("NAMA_FILE") ?>
                                        </a>
                                        </td>
                                      <td><?= $rekanan_tenaga_ahli_sertifikat->getField("INSTANSI") ?></td>
                                      <td><?= $rekanan_tenaga_ahli_sertifikat->getField("TANGGAL_BERLAKU") ?></td>
                                      </tr>
                                      <?php
                                      }
                                      ?>

                                </table>
                                </div>
                                 </td>
                                </tr>
                                <?php
                                $i++;
                                }
                                }
                                else
                                {
                                ?>
                                <tr>
                                    <td colspan="3">.: data belum ada :.</td>
                                </tr>
                            <?php
                            }
                            ?>
                  </tbody>

                </table>
            </form>
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
              if ($cekData->getField("tenaga_ahli") == '1') {
                $checked = 'checked';
              }
              echo '<input class="mb-1" type="checkbox" name="checktenaga_ahli" id="checkjenis" onclick="return updateChecklist(\''.$reqId.'\',\'tenaga_ahli\')" style="cursor:pointer" '.$checked.'> Ya, Lengkap ';
              ?>
              <input type="" class="form-control easyui-validatebox span2" required maxlength="255" style="height: 30px !important;" name="" placeholder="catatan" id="catatanjenis" value="<?=$cekData->getField("tenaga_ahli_note")?>" onChange="return updateChecklist('<?= $reqId ?>','tenaga_ahli')">
              <small><sup>*</sup>&nbsp;Tekan enter setalah mengisi catatan</small>
            </div>
          </div>
        </form>
        <?php 
        } ?>
      </div>
    </div>
  </div>

  <!-- Bootstrap core JavaScript
  ================================================== -->
  <!-- Placed at the end of the document so the pages load faster -->
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
