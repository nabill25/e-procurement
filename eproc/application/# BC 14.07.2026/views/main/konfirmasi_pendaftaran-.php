<?php
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 *
 */
$this->libsession->cekSession('blockpenyedia');

$this->load->model("UserLogin");

$this->load->library("crfs_protect"); $csrf = new crfs_protect('_crfs_rr');
$this->load->library("kauth");  $userLogin = new kauth();
$this->load->model("Rekanan");
//echo $this->ID;exit;
?>
<script type="text/javascript">
$(document).ready(function() {

  $(function(){
    $('#ff').form({
      url:'rekanan_json/rekanan_konfirmasi',
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
        arrData = data.split("-");
        alertSuccess2(arrData[1]);
        setTimeout(function() {
            document.location.href = '<?= base_url(); ?>';
        }, 2000);
        hideLoad();
      }
    });

  });

  $("#chk_agreement").click(countChecked);

});

function konfirmasiReload(){
  location.reload();
}

function countChecked() {
  var n = $("#chk_agreement:checked").length;
  //alert(n);
  if(n){
    $("#reqSubmit").show(0);
  }else{
    $("#reqSubmit").hide(0);
  }
}

</script>

<link rel="stylesheet" type="text/css" href="<?=base_url() ?>assets/new/vendors/css/ui/jquery-ui.min.css">
<link rel="stylesheet" type="text/css" href="<?=base_url() ?>assets/new/css/plugins/ui/jqueryui.css">
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
            <strong>Checklist Kelengkapan</strong>
          </div>
          <form id="ff" class="easyui-form form-horizontal" method="post" novalidate enctype="multipart/form-data">
              <?php
                $jumlahUncentang = 0;
                $rekanan = new Rekanan();
                $rekanan_keuangan = new Rekanan();
                $rekanan_perpajakan = new Rekanan();
                $rekanan_teknis = new Rekanan();
                $rekanan_pakta_integritas = new Rekanan();
                if ($this->REKANAN_TIPE_ID == '7') { // Perorangan
                  $rekanan->selectByParamsKonfirmasiPerorangan($this->ID);
                  $rekanan_perpajakan->selectByParamsKonfirmasiPeroranganDataPerpajakan($this->ID);
                  $rekanan_teknis->selectByParamsKonfirmasiPeroranganDataTeknis($this->ID);
                } else {
                  $rekanan->selectByParamsKonfirmasiDataAdmin($this->ID);
                  $rekanan_keuangan->selectByParamsKonfirmasiDataKeuangan($this->ID);
                  $rekanan_perpajakan->selectByParamsKonfirmasiDataPerpajakan($this->ID);
                  $rekanan_teknis->selectByParamsKonfirmasiDataTeknis($this->ID);
                }
                  $rekanan_pakta_integritas->selectByParamsKonfirmasiPaktaIntegritas($this->ID);
                $no=1;
                if ($this->REKANAN_TIPE_ID == '7') { // Perorangan ?>
                <h4>Data Administrasi</h4>
                <table class="table table-striped">
                <?php
                  while($rekanan->nextRow())
                  {
                    $cekData = new Rekanan();
                    $cekData->selectByParamsRekananChecklist(array("REKANANID"=>$this->ID),-1,-1);
                    $cekData->firstRow();
                    $pesan = $rekanan->getField("FIELDNYA").'_note';
                ?>
                    <tr>
                      <td style="width: 2%"><?=$no;?></td>
                      <td style="width: 83%">
                        <?=$rekanan->getField("NAMA")?>
                        <?php
                        if ($rekanan->getField("WAJIB") == '*') {
                           echo '<span class="color:red">'.$rekanan->getField("WAJIB").'</span>';
                        } else {
                          echo "";
                        } ?>
                        <?php
                        if ($cekData->getField($rekanan->getField("FIELDNYA")) == '1') {
                          echo '<br><small class="badge badge-primary"><span class="fa fa-check"> </span> '.$cekData->getField("$pesan").'</small>';
                        } else {
                          if ($cekData->getField("$pesan") != '') {
                            echo '<br><small class="badge badge-danger"><span class="fa fa-remove"> </span> '.$cekData->getField("$pesan").'</small>';
                          }
                        }
                        ?>
                      </td>
                      <td style="width: 15%" class="text-center">
                        <img class="simbol" src="images/<?=$rekanan->getField("SIMBOL")?>.png">
                      </td>
                    </tr>
                      <?php
                   if($rekanan->getField("SIMBOL") == "uncentang" && $rekanan->getField("WAJIB") == '*')
                    $jumlahUncentang++;
                  $no++;
                  } ?>
              </table>
                <?php
                } else {  ?>
                <h4>Data Administrasi</h4>
                <table class="table table-striped">
                <?php
                  while($rekanan->nextRow())
                  {
                    $cekData = new Rekanan();
                    $cekData->selectByParamsRekananChecklist(array("REKANANID"=>$this->ID),-1,-1);
                    $cekData->firstRow();
                    $pesan = $rekanan->getField("FIELDNYA").'_note';
                ?>
                    <tr>
                      <td style="width: 2%"><?=$no;?></td>
                      <td style="width: 83%">
                        <?=$rekanan->getField("NAMA") ?>
                        <?php
                        if ($rekanan->getField("WAJIB") == '*') {
                           echo '<span class="color:red">'.$rekanan->getField("WAJIB").'</span>';
                        } else {
                          echo "";
                        } ?>

                        <?php
                        if ($cekData->getField($rekanan->getField("FIELDNYA")) == '1') {
                          echo '<br><small class="badge badge-primary"><span class="fa fa-check"> </span> '.$cekData->getField("$pesan").'</small>';
                        } else {
                          if ($cekData->getField("$pesan") != '') {
                            echo '<br><small class="badge badge-danger"><span class="fa fa-remove"> </span> '.$cekData->getField("$pesan").'</small>';
                          }
                        }
                        ?>
                      </td>
                      <td style="width: 15%" class="text-center">
                        <img class="simbol" src="images/<?=$rekanan->getField("SIMBOL")?>.png">
                      </td>
                    </tr>
                      <?php
                   if($rekanan->getField("SIMBOL") == "uncentang" && $rekanan->getField("WAJIB") == '*')
                    $jumlahUncentang++;
                  $no++;
                  } ?>
              </table>
            <?php
                }
              ?>

              <?php
                $no=1;
                if ($this->REKANAN_TIPE_ID == '7') { // Perorangan
                } else {  ?>
                <h4>Data Keuangan</h4>
                <table class="table table-striped">
                <?php
                  while($rekanan_keuangan->nextRow())
                  {
                    $cekData = new Rekanan();
                    $cekData->selectByParamsRekananChecklist(array("REKANANID"=>$this->ID),-1,-1);
                    $cekData->firstRow();
                    $pesan = $rekanan_keuangan->getField("FIELDNYA").'_note';
                ?>
                    <tr>
                      <td style="width: 2%"><?=$no;?></td>
                      <td style="width: 83%">
                        <?=$rekanan_keuangan->getField("NAMA") ?>
                       <?php
                        if ($rekanan_keuangan->getField("WAJIB") == '*') {
                           echo '<span class="color:red">'.$rekanan_keuangan->getField("WAJIB").'</span>';
                        } else {
                          echo "";
                        } ?>

                        <?php
                        if ($cekData->getField($rekanan_keuangan->getField("FIELDNYA")) == '1') {
                          echo '<br><small class="badge badge-primary"><span class="fa fa-check"> </span> '.$cekData->getField("$pesan").'</small>';
                        } else {
                          if ($cekData->getField("$pesan") != '') {
                            echo '<br><small class="badge badge-danger"><span class="fa fa-remove"> </span> '.$cekData->getField("$pesan").'</small>';
                          }
                        }
                        ?>
                      </td>
                      <td style="width: 15%" class="text-center">
                        <img class="simbol" src="images/<?=$rekanan_keuangan->getField("SIMBOL")?>.png">
                      </td>
                    </tr>
                      <?php
                   if($rekanan_keuangan->getField("SIMBOL") == "uncentang" && $rekanan_keuangan->getField("WAJIB") == '*')
                    $jumlahUncentang++;
                  $no++;
                  } ?>
                </table>
              <?php
                }
              ?>

              <?php
                $no=1;
                if ($this->REKANAN_TIPE_ID == '7') { // Perorangan ?>
                <h4>Data Perpajakan</h4>
                <table class="table table-striped">
                <?php
                  while($rekanan_perpajakan->nextRow())
                  {
                    $cekData = new Rekanan();
                    $cekData->selectByParamsRekananChecklist(array("REKANANID"=>$this->ID),-1,-1);
                    $cekData->firstRow();
                    $pesan = $rekanan_perpajakan->getField("FIELDNYA").'_note';
                ?>
                    <tr>
                      <td style="width: 2%"><?=$no;?></td>
                      <td style="width: 83%">
                        <?=$rekanan_perpajakan->getField("NAMA") ?>
                         <?php
                        if ($rekanan_perpajakan->getField("WAJIB") == '*') {
                           echo '<span class="color:red">'.$rekanan_perpajakan->getField("WAJIB").'</span>';
                        } else {
                          echo "";
                        } ?>
                        <?php
                        if ($cekData->getField($rekanan_perpajakan->getField("FIELDNYA")) == '1') {
                          echo '<br><small class="badge badge-primary"><span class="fa fa-check"> </span> '.$cekData->getField("$pesan").'</small>';
                        } else {
                          if ($cekData->getField("$pesan") != '') {
                            echo '<br><small class="badge badge-danger"><span class="fa fa-remove"> </span> '.$cekData->getField("$pesan").'</small>';
                          }
                        }
                        ?>
                      </td>
                      <td style="width: 15%" class="text-center">
                        <img class="simbol" src="images/<?=$rekanan_perpajakan->getField("SIMBOL")?>.png">
                      </td>
                    </tr>
                      <?php
                   if($rekanan_perpajakan->getField("SIMBOL") == "uncentang" && $rekanan_perpajakan->getField("WAJIB") == '*')
                    $jumlahUncentang++;
                  $no++;
                  } ?>
                </table>
                <?php
                } else { ?>
                <h4>Data Perpajakan</h4>
                <table class="table table-striped">
                <?php
                  while($rekanan_perpajakan->nextRow())
                  {
                    $cekData = new Rekanan();
                    $cekData->selectByParamsRekananChecklist(array("REKANANID"=>$this->ID),-1,-1);
                    $cekData->firstRow();
                    $pesan = $rekanan_perpajakan->getField("FIELDNYA").'_note';
                ?>
                    <tr>
                      <td style="width: 2%"><?=$no;?></td>
                      <td style="width: 83%">
                        <?=$rekanan_perpajakan->getField("NAMA") ?>
                         <?php
                        if ($rekanan_perpajakan->getField("WAJIB") == '*') {
                           echo '<span class="color:red">'.$rekanan_perpajakan->getField("WAJIB").'</span>';
                        } else {
                          echo "";
                        } ?>

                        <?php
                        if ($cekData->getField($rekanan_perpajakan->getField("FIELDNYA")) == '1') {
                          echo '<br><small class="badge badge-primary"><span class="fa fa-check"> </span> '.$cekData->getField("$pesan").'</small>';
                        } else {
                          if ($cekData->getField("$pesan") != '') {
                            echo '<br><small class="badge badge-danger"><span class="fa fa-remove"> </span> '.$cekData->getField("$pesan").'</small>';
                          }
                        }
                        ?>
                      </td>
                      <td style="width: 15%" class="text-center">
                        <img class="simbol" src="images/<?=$rekanan_perpajakan->getField("SIMBOL")?>.png">
                      </td>
                    </tr>
                      <?php
                   if($rekanan_perpajakan->getField("SIMBOL") == "uncentang" && $rekanan_perpajakan->getField("WAJIB") == '*')
                    $jumlahUncentang++;
                  $no++;
                  } ?>
                </table>
              <?php
                }
              ?>

              <?php
                $no=1;
                if ($this->REKANAN_TIPE_ID == '7') { // Perorangan ?>
                <h4>Data Teknis</h4>
               <table class="table table-striped">
                <?php
                  while($rekanan_teknis->nextRow())
                  {
                    $cekData = new Rekanan();
                    $cekData->selectByParamsRekananChecklist(array("REKANANID"=>$this->ID),-1,-1);
                    $cekData->firstRow();
                    $pesan = $rekanan_teknis->getField("FIELDNYA").'_note';
                ?>
                    <tr>
                      <td style="width: 2%"><?=$no;?></td>
                      <td style="width: 83%">
                        <?=$rekanan_teknis->getField("NAMA") ?>
                         <?php
                        if ($rekanan_teknis ->getField("WAJIB") == '*') {
                           echo '<span class="color:red">'.$rekanan_teknis  ->getField("WAJIB").'</span>';
                        } else {
                          echo "";
                        } ?>
                        <?php
                        if ($cekData->getField($rekanan_teknis->getField("FIELDNYA")) == '1') {
                          echo '<br><small class="badge badge-primary"><span class="fa fa-check"> </span> '.$cekData->getField("$pesan").'</small>';
                        } else {
                          if ($cekData->getField("$pesan") != '') {
                            echo '<br><small class="badge badge-danger"><span class="fa fa-remove"> </span> '.$cekData->getField("$pesan").'</small>';
                          }
                        }
                        ?>
                      </td>
                      <td style="width: 15%" class="text-center">
                        <img class="simbol" src="images/<?=$rekanan_teknis->getField("SIMBOL")?>.png">
                      </td>
                    </tr>
                      <?php
                   if($rekanan_teknis->getField("SIMBOL") == "uncentang" && $rekanan_teknis->getField("WAJIB") == '*')
                    $jumlahUncentang++;
                  $no++;
                  } ?>
                </table>
                <?php
                } else { ?>
               <h4>Data Teknis</h4>
               <table class="table table-striped">
                <?php
                  while($rekanan_teknis->nextRow())
                  {
                    $cekData = new Rekanan();
                    $cekData->selectByParamsRekananChecklist(array("REKANANID"=>$this->ID),-1,-1);
                    $cekData->firstRow();
                    $pesan = $rekanan_teknis->getField("FIELDNYA").'_note';
                ?>
                    <tr>
                      <td style="width: 2%"><?=$no;?></td>
                      <td style="width: 83%">
                        <?=$rekanan_teknis->getField("NAMA") ?>
                         <?php
                        if ($rekanan_teknis ->getField("WAJIB") == '*') {
                           echo '<span class="color:red">'.$rekanan_teknis  ->getField("WAJIB").'</span>';
                        } else {
                          echo "";
                        } ?>

                        <?php
                        if ($cekData->getField($rekanan_teknis->getField("FIELDNYA")) == '1') {
                          echo '<br><small class="badge badge-primary"><span class="fa fa-check"> </span> '.$cekData->getField("$pesan").'</small>';
                        } else {
                          if ($cekData->getField("$pesan") != '') {
                            echo '<br><small class="badge badge-danger"><span class="fa fa-remove"> </span> '.$cekData->getField("$pesan").'</small>';
                          }
                        }
                        ?>

                      </td>
                      <td style="width: 15%" class="text-center">
                        <img class="simbol" src="images/<?=$rekanan_teknis->getField("SIMBOL")?>.png">
                      </td>
                    </tr>
                      <?php
                   if($rekanan_teknis->getField("SIMBOL") == "uncentang" && $rekanan_teknis->getField("WAJIB") == '*')
                    $jumlahUncentang++;
                  $no++;
                  } ?>
                </table>
              <?php
                }
              ?>

              <h4>Lainnya</h4>
               <table class="table table-striped">
                <?php
                $no=1;
                  while($rekanan_pakta_integritas->nextRow())
                  {
                ?>
                    <tr>
                      <td style="width: 2%"><?=$no;?></td>
                      <td style="width: 83%">
                        <?=$rekanan_pakta_integritas->getField("NAMA") ?>
                         <?php
                        if ($rekanan_pakta_integritas ->getField("WAJIB") == '*') {
                           echo '<span class="color:red">'.$rekanan_pakta_integritas  ->getField("WAJIB").'</span>';
                        } else {
                          echo "";
                        } ?>

                        <?php
                        $this->load->model("Masterdokumentemplate");
                        $this->load->model("Dokumenrekanan");
                        $master_dokumen = new Masterdokumentemplate();
                        $dokumen_rekanan = new Dokumenrekanan();
                        $master_dokumen->selectByParams(array('B.NAMA' => 'Pakta Integritas'));
                        $dokumen_rekanan->selectByParams(array('REKANAN_ID' => $this->ID));
                        if ($master_dokumen->countRow() > 0) {
                          $master_dokumen->firstRow();
                         ?>
                          <a class="badge badge-dark" href="uploads/template/<?=$master_dokumen->getField('PATH_FILE')?>" target="_blank"> <small><span class="fa fa-book"></span> Download Template</small></a>
                        <?php
                        }
                          if ($dokumen_rekanan->countRow() > 0) {
                            $dokumen_rekanan->firstRow();
                            echo '<br><a style="color:#fff" href="uploads/pakta_integritas/'.$dokumen_rekanan->getField('PATH_FILE').'" target="_blank" class="badge badge-success"><span class="fa fa-check"></span>  Sudah berhasil upload, lihat dokumen</a>';
                          } else { }
                        ?>
                      </td>
                      <td style="width: 15%" class="text-center">
                        <img class="simbol" src="images/<?=$rekanan_pakta_integritas->getField("SIMBOL")?>.png"> <br>
                        <a class="<?= CLASS_BTN_PRIMARY ?> btn-sm mt-1" onclick="openAdd('main/loadUrl/main/pakta_integritas_add')"> <span class="fa fa-upload"></span> <?php if ($rekanan_pakta_integritas->getField("SIMBOL") == 'centang') { echo "Ubah"; } else { echo "Upload"; }  ?> Pakta Integritas</a>
                      </td>
                    </tr>
                      <?php
                   if($rekanan_pakta_integritas->getField("SIMBOL") == "uncentang" && $rekanan_pakta_integritas->getField("WAJIB") == '*')
                    $jumlahUncentang++;
                  $no++;
                  } ?>
                </table>

          <?php
          if ($jumlahUncentang > 0) {
             echo '<div class="alert alert-danger">
                    <b> Kurang '.$jumlahUncentang.' data belum dilengkapi (tanda * data wajib diisi)  </b>
                  </div>';
          } ?>

            <div class="card mb-1 border-blue border-darken-1" style="margin-top: 2%">
              <div class="card-content">
                <div class="p-1">
                  <div class="table-responsive">
                    <h4>Menyatakan dengan sesungguhnya bahwa</h4>
                    <table class="table table-bordered table-hover">
                      <tbody>
                        <?php
                        if ($this->REKANAN_TIPE_ID == '7') { // Perorangan ?>
                          <tr>
                            <td>
                              1. Saya tidak sedang dinyatakan pailit atau tidak sedang dihentikan atau tidak sedang menjalani sanksi pidana atau sedang dalam pengawasan pengadilan;
                            </td>
                          </tr>
                          <tr>
                            <td>
                              2. Saya tidak pernah dihukum berdasarkan putusan pengadilan atas tindakan yang berkaitan dengan kondite professional saya;
                            </td>
                          </tr>
                          <tr>
                            <td>
                             3. Apabila dikemudian hari ditemui bahwa data/dokumen yang saya sampaikan tidak benar dan ada pemalsuan, maka saya bersedia dikenakan sanksi administrasi yaitu dikenai Daftar Hitam;
                            </td>
                          </tr>
                        <?php
                        } else { ?>
                          <tr>
                            <td>
                              1. Saya/Perusahaan saya tidak sedang dinyatakan pailit atau kegiatan usahanya tidak sedang dihentikan atau tidak sedang menjalani sanksi pidana atau sedang dalam pengawasan pengadilan;
                            </td>
                          </tr>
                          <tr>
                            <td>
                              2. Saya tidak pernah dihukum berdasarkan putusan pengadilan atas tindakan yang berkaitan dengan kondite professional saya;
                            </td>
                          </tr>
                          <tr>
                            <td>
                             3. Apabila dikemudian hari ditemui bahwa data/dokumen yang kami sampaikan tidak benar dan ada pemalsuan, maka kami bersedia dikenakan sanksi administrasi yaitu dikenai Daftar Hitam;
                            </td>
                          </tr>
                        <?php
                        } ?>
                      </tbody>
                    </table>

                  </div>
                </div>
              </div>
            </div>

            <hr>
            <?php
            $rekanan = new Rekanan();
            $rekanan->selectByParams(array("A.REKANAN_ID"=>$this->ID),-1,-1);
            $rekanan->firstRow();
            $reqStatusValidasi= $rekanan->getField("STATUS_VALIDASI");

            $userRekanan = new Userlogin();
            $userRekanan->selectByParams(array("REKANAN_ID"=>$this->ID),-1,-1);
            $userRekanan->firstRow();
            $reqStatusUser= $userRekanan->getField("USER_STATUS");

            // echo $reqStatusValidasi.'--'.$reqStatusUser;
            if ($jumlahUncentang == 0) { // Muncul checklist jika persyaratan sudah lengkap semua
              if(($reqStatusValidasi == 0 || $reqStatusValidasi == 10)) {
              ?>
            <div>
                <input name="reqSetuju" type="checkbox" id="chk_agreement" accesskey="e" value="1" style="cursor: pointer;" />
                <?=translate("Dengan ini saya menyatakan bahwa data-data tersebut adalah data yang benar dan dapat dipertanggungjawabkan.", "Your submission of this form will constitute that you have read and understood the Terms and Conditions.")?>
            </div>

            <div class="form-actions">
              <input type="hidden" name="reqUncentang" value="<?=$jumlahUncentang?>">
              <button type="submit" name="reqSubmit" id="reqSubmit" class="<?= CLASS_BTN_PRIMARY ?>" style="display:none;">
                <i class="fa fa-send"></i> &nbsp;<?=translate("Kirim Dokumen Pendaftaran", "Submit Registration Documents")?>
              </button>
            </div>
            <?php
              }
            } ?>

          </form>
        </div>
      </div>
    </div>
  </div>
</div>
