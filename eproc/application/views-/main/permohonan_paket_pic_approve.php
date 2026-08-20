<?php
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 *
 */
if($this->USER_TYPE_ID == "")
    redirect("main");

/* INCLUDE FILE */
$this->load->model(array("Panitia","PermohonanPaket"));

include_once("functions/string.func.php");
include_once("functions/date.func.php");
include_once("functions/default.func.php");

/* VARIABLE */
$reqId  = $this->input->get("reqId"); // permohonan_paket_analisa_id
$sirupId  = $this->input->get("sirupId"); // kode_sirup
$permohonanId  = $this->input->get("permohonanId"); // permohonan_paket_id

$permohonan_paket = new PermohonanPaket();
$permohonan_paket->selectByParams(array("A.PERMOHONAN_PAKET_ID" => $permohonanId)); 
$permohonan_paket->firstRow();
$reqMetodePengadaan = $permohonan_paket->getField("STRATEGI_PENGADAAN");
$reqPaketMetodeLelangId = $permohonan_paket->getField("PAKET_METODE_LELANG_ID");

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
    <script type="text/javascript" src="js/jquery-1.6.1.min.js"></script>
    <link rel="stylesheet" type="text/css" href="lib/drupal/index.css" />
    <script src="lib/emodal/eModal.js"></script>
    <script type="text/javascript">
    function openAdd(pageUrl) {
      eModal.iframe(pageUrl, '<?= SYSTEM_NAME.' | '.SYSTEM_NAME_PT ?>')
    }
    function closePopup() {
      eModal.close();
    }
    $(function(){
      $('#ff').form({
        url:'permohonan_paket_json/approve_permohonan',
        onSubmit:function(){
          return $(this).form('validate');
        },
        success:function(data){
          //alert(data);return false;
           $.messager.alert('Info', data, 'info');
           top.reloadMonitoring6();
           top.closePopup();
           top.frames['mainFrame'].location.reload();
        }
      });

    });

    function createRowDokumenPanitia()
    {
      $(function () {
        $.get("main/loadUrl/main/panitia_add_template/?reqUnitKerja="+$("#reqUnitKerja").val(), function (data) {
          $("#tbDataDokumenPanitia").append(data);
        });
      });
    }

  $(document).ready(function() {
    $('input:radio[name=reqMetodePengadaan]').change(function() {
      if (this.value == '0') {
        // setPanitia(0);
        $('#ketStrategi').show();
        // $('#ketStrategi2').hide();
        $('#ketStrategi').html("<span class=\"badge badge-secondary\">Penunjukan Langsung</span> <span class=\"badge badge-secondary\">Tender Cepat</span> <span class=\"badge badge-secondary\">Tender</span> <span class=\"badge badge-secondary\">Tender Kualifikasi</span> <span class=\"badge badge-secondary\">Tender Terbatas</span> <span class=\"badge badge-secondary\">Seleksi</span> <span class=\"badge badge-secondary\">Kompetisi</span>");
      }
      else if (this.value == '2') {
        // setPanitia(2);
        // $('#ketStrategi').hide();
        // $('#ketStrategi2').show();
        $('#ketStrategi').html("<span class=\"badge badge-secondary\">Pengadaan Langsung</span> <span class=\"badge badge-secondary\">Pembelian Langsung</span> <span class=\"badge badge-secondary\">Pembelian Katalog</span> <span class=\"badge badge-secondary\">Pembelian Katalog Pemerintah</span> ");
        // $('#panitiaSourching').hide();
        // $('#panitiaPurchasing').show();
      } else {
        $('#ketStrategi').html("<span class=\"badge badge-secondary\">Transaksi < 25 juta</span>");
      }
    });
  });

  function setPanitia(a)
  {
    $(function () {
      $.get("main/loadUrl/main/panitia_pic_template?reqUnitKerja=<?= $reqUnitKerjaId ?>&reqSourching="+a+"", function (data) {
        $("#panitiaSourching").html(data);
      });
    });
  }
  </script>

  </head>

<div class="card mb-1 border-darken-1">
    <div class="card-content">
      <div class="p-1">
        <div class="alert bg-primary alert-icon-left alert-arrow-left alert-dismissible mb-2" role="alert">
          <span class="alert-icon"><i class="fa fa-th"></i></span>  <strong>Persiapan</strong>
        </div>
        <div class="p-1">
          <?php
          $this->load->library("libplanning");
          $libplanning = new libplanning();
          echo $libplanning->sirupHeader($reqId,$sirupId);

          ?>
         <form id="ff" method="post" class="form-horizontal" role="form">
            <table class="table table-bordered table-hover p-1">
                <tbody>
                  <tr>
                    <td width="20%">Strategi Pengadaan</td>
                    <td width="80%">
                      <div class="row">
                        <div class="form-group col-md-12 mb-2">
                          <?php
                            $pl = array(
                              '0' => 'Sourcing',
                              '2' => 'Purchasing',
                              // '1' => 'Other',
                            );
                          foreach ($pl as $key => $value) {
                            if ($reqMetodePengadaan == $value) {
                              $checked = 'checked';
                            } else {
                              $checked = '';
                            }
                              ?>
                            <input value="<?= $key ?>" name="reqMetodePengadaan" id="reqMetodePengadaan-0" type="radio" <?= $checked ?>/>
                            &nbsp; <?= $value ?> &nbsp;
                          <?php
                          }
                          ?>
                        </div>
                        <span id="ketStrategi">
                          <?php if ($reqMetodePengadaan == 'Sourcing') { ?>
                            <span class="badge badge-secondary">Penunjukan Langsung</span> <span class="badge badge-secondary">Tender Cepat</span> <span class="badge badge-secondary">Tender</span> <span class="badge badge-secondary">Tender Kualifikasi</span> <span class="badge badge-secondary">Tender Terbatas</span> <span class="badge badge-secondary">Seleksi</span> <span class="badge badge-secondary">Kontes</span>
                          <?php 
                          } else { ?>
                            <span class="badge badge-secondary">Pengadaan Langsung</span> <span class="badge badge-secondary">Pembelian Langsung</span> <span class="badge badge-secondary">Pembelian Katalog</span> <span class="badge badge-secondary">Pembelian Katalog Pemerintah</span>
                          <?php 
                          } ?>
                        </span>

                        <span id="ketStrategi2" <?php if ($reqMetodePengadaan == 'Purchasing') { echo 'style="display: display;"'; } else { echo 'style="display: none;"'; } ?>>
                          <?php 
                          // $this->load->model("Paketmetodelelang");
                          // $paket_metode_lelang = new Paketmetodelelang();
                          // $paket_metode_lelang->selectByParams(array("TENDER" => '3')); 
                          ?>

                          <!-- <select class="form-control" name="reqMetode">
                                <option value="0">- Pilih -</option>  -->
                          <?php 
                            // while($paket_metode_lelang->nextRow())
                            // {
                            //     $selected = '';
                            //   if ($reqPaketMetodeLelangId == $paket_metode_lelang->getField('PAKET_METODE_LELANG_ID')) {
                            //     $selected = 'selected';
                            //   }
                             ?>
                                <!-- <option value="<?php // echo $paket_metode_lelang->getField('PAKET_METODE_LELANG_ID') ?>" <?php // echo $selected ?>><?php // echo $paket_metode_lelang->getField('NAMA') ?></option>  -->
                            <?php 
                            // } ?>
                          <!-- </select>
                        </span> -->
                      </div>
                    </td>
                  </tr> 
                </tbody>
             </table>

            <?php
            $this->load->library("libplanning");
            $libplanning = new libplanning();
            echo $libplanning->headerPermohonanDokumenEsign($reqId,$totalKirim); 
            ?> 

            <?php 
            if ($totalKirim == 0) { ?>
            <div class="form-actions">
              <input type="hidden" name="reqId" value="<?=$reqId?>"> <!-- permohonan_paket_analisa_id -->
              <input type="hidden" name="reqPermohonanId" value="<?=$permohonanId?>"> <!-- permohonan_paket_id -->
              <input type="hidden" name="reqMode" value="<?=isset($reqMode)?$reqMode:''?>">
              <a href="#" onClick="top.closePopup()" class="<?= CLASS_BTN_DANGER ?>"> <i class="fa fa-close"></i> Tutup</a>
              <button type="submit" class="<?= CLASS_BTN_PRIMARY ?>"><i class="fa fa-check-square-o"></i> Setujui</button>
            </div>
            <?php 
            } else {
              echo '<div class="alert alert-info"> Ada '.$totalKirim.' Dokumen belum di TTE..!</div>';
            } ?>
            <!--<button onClick="top.closePopup()">hai</button> -->
         </form>

        </div>
      </div>
    </div>
  </div>

    <script src="lib/bootstrap/libs/jquery/1.12.4/jquery.min.js"></script>
    <script>window.jQuery || document.write('<script src="lib/bootstrap/assets/js/vendor/jquery.min.js"><\/script>')</script>
    <script src="lib/bootstrap/dist/js/bootstrap.min.js"></script>
    <link rel="stylesheet" type="text/css" href="lib/eproc/themes/default/easyui.css">
    <script type="text/javascript" src="js/jquery-1.6.1.min.js"></script>
    <script type="text/javascript" src="lib/eproc/jquery.easyui.min.js"></script>
    <script type="text/javascript" src="lib/eproc/kalender-easyui.js"></script>
    <script type="text/javascript" src="lib/eproc/allfunc.js"></script>

  </body>
</html>
