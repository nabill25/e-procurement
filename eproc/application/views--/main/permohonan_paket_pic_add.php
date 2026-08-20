<?php
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 *
 */
if($this->USER_TYPE_ID == "" || $this->LEVEL_PERENCANA != '3') // Keluar Selain Kasubdit
    redirect("main");

/* INCLUDE FILE */
$this->load->model("PermohonanPaket");
$this->load->model("PermohonanPaketFile");
$this->load->model("SkPanitia");
$this->load->model("Panitia");
$this->load->model("PaketPenawaran");

include_once("functions/string.func.php");
include_once("functions/date.func.php");
include_once("functions/default.func.php");

/* VARIABLE */
$reqId  = $this->input->get("reqId"); // permohonan_paket_analisa_id

$permohonan_paket = new PermohonanPaket();
$permohonan_paket->selectByParams(array("A.PERMOHONAN_PAKET_ANALISA_ID" => $reqId)); 
$permohonan_paket->firstRow(); 

$sirupId  = $permohonan_paket->getField("SIRUP_ID"); // kode_sirup
$permohonanId  = $permohonan_paket->getField("PERMOHONAN_PAKET_ID"); // permohonan_paket_id
?> 
  <script type="text/javascript">
    function openAdd(pageUrl) {
      eModal.iframe(pageUrl, '<?= SYSTEM_NAME.' | '.SYSTEM_NAME_PT ?>')
    }
    function closePopup() {
      eModal.close();
    }
    $(function(){
      $('#ff').form({
        url:'permohonan_paket_json/tetapkan_metode',
        onSubmit:function(){
          return $(this).form('validate');
        },
        success:function(data){
          //alert(data);return false;
           $.messager.alert('Info', data, 'info'); 
           setTimeout(function () { 
              // document.location.href = 'main/index/permohonan_paket_pic_add/?reqId=<?php // $reqId ?>';
              document.location.href = 'main/index/rencana_umum_pengadaan_persiapan';
            }, 2000);
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
      if (this.value == '0') { // Sourcing
        // setPanitia(0);
        // $('#ketStrategi').show();
        // $('#ketStrategi2').hide();

        $('#ketStrategi').html("<span class=\"badge badge-secondary\">Penunjukan Langsung</span> <span class=\"badge badge-secondary\">Tender Cepat</span> <span class=\"badge badge-secondary\">Tender</span> <span class=\"badge badge-secondary\">Tender Kualifikasi</span> <span class=\"badge badge-secondary\">Tender Terbatas</span> <span class=\"badge badge-secondary\">Seleksi</span> <span class=\"badge badge-secondary\">Kontes</span>");
      }
      else if (this.value == '2') { // Purchasing
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

    $('#btnKirimEsign').on('click', function () { 
      openAddFrame("main/loadUrl/main/rencana_umum_pengadaan_persiapan_kirim_file_esign/?reqId=<?= $reqId ?>&sirupId=<?= $sirupId ?>");
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

  function reloadMonitoringReload()
  {
    location.reload();
  }

</script>


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
          $statusPR = $libplanning->ceStatusPR($sirupId);
          echo $libplanning->sirupHeader($reqId,$sirupId);

          if ($statusPR == 'APPROVED') { 
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
                              if ($reqMetodePengadaan == $key) {
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
                            <span class="badge badge-secondary">Penunjukan Langsung</span> <span class="badge badge-secondary">Tender Cepat</span> <span class="badge badge-secondary">Tender</span> <span class="badge badge-secondary">Tender Kualifikasi</span> <span class="badge badge-secondary">Tender Terbatas</span> <span class="badge badge-secondary">Seleksi</span> <span class="badge badge-secondary">Kontes</span>
                          </span>
                          <span id="ketStrategi2" style="display: none;">
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
                               ?>
                                  <!-- <option value="<?php // $paket_metode_lelang->getField('PAKET_METODE_LELANG_ID') ?>"><?php // $paket_metode_lelang->getField('NAMA') ?></option>  -->
                              <?php 
                              // } ?>
                            <!-- </select> -->
                          </span>
                        </div>
                      </td>
                    </tr> 
                  </tbody>
               </table>

              <?php
              $this->load->library("libplanning");
              $libplanning = new libplanning();
              echo $libplanning->headerPermohonanDokumen($reqId);
              ?>

              <div class="form-actions">
                <input type="hidden" name="reqId" value="<?=$reqId?>"> <!-- permohonan_paket_analisa_id -->
                <input type="hidden" name="reqPermohonanId" value="<?=$permohonanId?>"> <!-- permohonan_paket_id -->
                <input type="hidden" name="reqMode" value="<?=isset($reqMode)?$reqMode:''?>">
                <a href="main/index/rencana_umum_pengadaan_persiapan" class="<?= CLASS_BTN_DANGER ?> mr-1 text-white"> <i class="fa fa-arrow-left"></i> Kembali </a>
                <?php 
                $this->load->model(array("PermohonanPaketAnalisaFile"));
                $permohonanpaketanalisafile = new PermohonanPaketAnalisaFile();
                $statement = " AND PERMOHONAN_PAKET_ANALISA_ID = ".$reqId." AND FILE_TTE = '1'";
                $allRecord = $permohonanpaketanalisafile->getCountByParams(array(), $statement, $sOrder); 
                // echo $allRecord.'--';
                if ($allRecord >= 1) { // cek ada file yang musti di TTE gak ?
                  $permohonanpaketanalisafile2 = new PermohonanPaketAnalisaFile();
                  $statement2 = " AND PERMOHONAN_PAKET_ANALISA_ID = ".$reqId." AND ESIGN_STATUS != '' ";
                  $allRecord2 = $permohonanpaketanalisafile2->getCountByParams(array(), $statement2, $sOrder); 
                ?>
                  <span class="">
                    <a id="btnKirimEsign" class="<?= CLASS_BTN_DARK ?> mr-1"> <i class="fa fa-check-square-o"></i> Kirim file ke E-Sign</a>
                  </span>
                  <?php 
                  if ($allRecord2 >= 1) { // cek udah kirim belom
                   ?>
                    <button type="submit" class="<?= CLASS_BTN_PRIMARY ?>"><i class="fa fa-send"></i> Teruskan</button>
                  <?php 
                  }
                  ?>

                <?php 
                } else { ?>
                <button type="submit" class="<?= CLASS_BTN_PRIMARY ?>"><i class="fa fa-send"></i> Teruskan</button>
                <?php 
                } ?>
              </div>
              <!--<button onClick="top.closePopup()">hai</button> -->
           </form>
          <?php 
          } else {
            echo '<div class="alert alert-warning">Belum bisa menetapkan Metode Pengadaan karena status PR dalam proses '.$statusPR.'</div>';
            echo '<a href="main/index/rencana_umum_pengadaan_persiapan" class="'.CLASS_BTN_DANGER.' mr-1 text-white"> <i class="fa fa-arrow-left"></i> Kembali </a>';
          } ?>

        </div>
      </div>
    </div>
  </div> 

