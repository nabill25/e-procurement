<?php
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 *
 */

$this->load->library("kauth");  $userLogin = new kauth();
$this->load->model(array("Contractingaddendum","Contractingrekanan","Contractingaddendumjenis"));
include_once("functions/string.func.php");
include_once("functions/date.func.php");
include_once("functions/default.func.php");

$reqId  = $this->input->get("reqAidi"); // paketid
$reqConRekId  = $this->input->get("reqConRekId"); // contractingrekananid 
$reqAddendumId  = $this->input->get("reqAddendumId") ?: 0; // addendumid 

if($reqAddendumId=='0')
  $reqMode= 'simpan';
else
  $reqMode ='update';

$dataaddendum = new Contractingaddendum();
$dataaddendum->selectByParams(array("CONTRACTING_ADDENDUM_ID"=>$reqAddendumId));
$dataaddendum->firstRow();

$spkpks = new Contractingrekanan();
$spkpks->selectViewPKSSPK(array("A.CONTRACTINGREKANANID" => $reqConRekId));
$spkpks->firstRow();
$reqWaktuPelaksanaanDari = $spkpks->getField('CR_WAKTU_PELAKSANAAN_DARI') ?: '-';
$reqWaktuPelaksanaanSampai = $spkpks->getField('CR_WAKTU_PELAKSANAAN_SAMPAI') ?: '-';
$reqWaktuPenyelesaianDari = $spkpks->getField('CR_PENYELESAIAN_KONTRAK_AWAL') ?: '-';
$reqWaktuPenyelesaianSampai = $spkpks->getField('CR_PENYELESAIAN_KONTRAK_AKHIR') ?: '-';
$reqNilaiKontrak = $dataaddendum->getField('ADDENDUM_NILAI') ?: $spkpks->getField('CR_NILAI_KONTRAK');
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
    <link rel="stylesheet" href="lib/font-awesome-4.7.0/css/font-awesome.css" type="text/css">
    <script type="text/javascript" src="js/jquery-1.6.1.min.js"></script>
    <link rel="stylesheet" type="text/css" href="lib/drupal/index.css" /> 
     <script src="lib/emodal/eModal.js"></script>
     <style type="text/css">
       #reqKodeSeachPenyediaautocomplete-list {
          position: relative;
          margin-top: 10px;
          background: #fff;
          width: 100%;
        }
        #reqKodeSeachPenyediaautocomplete-list div {
          margin: 5px;
        }
     </style>
    <script>
    function openAdd(pageUrl) {
        eModal.iframe(pageUrl, 'Eprocurement | <?= SYSTEM_NAME_PT ?>')
    }

    function closePopup() {
      eModal.close();
    }

    function closePopupReload() {
      eModal.close();
      location.reload();
    }
    </script>
    <script type="text/javascript">
    $(function(){
      $('#ffAddDeliverable').form({
        url:'contracting_json/addAddendum',
        onSubmit:function(){
          var v=$(this).form('validate');
          if(v) {
            return v;
          } else {
            return false;
           // showLoad();  // show the message box
          }
        },
        success:function(data){
          $.messager.alert('Info', data, 'info');
          setTimeout(function () {
            window.top.location.reload();
          }, 1500);
        }
      });

    });

    $(document).ready(function() {
      $('#reqTanggal,#reqTanggalKontrakDari, #reqTanggalKontrakSampai, #reqTanggalPenyelesaianKontrakDari, #reqTanggalPenyelesaianKontrakAkhir').datebox({
        editable: false
      });

    });

  </script>
  </head>

<body class="body-popup">

     <div class="card mb-1 border-darken-1">
      <div class="card-content">
        <div class="p-1">
          <div class="alert bg-primary alert-icon-left alert-arrow-left alert-dismissible mb-2" role="alert">
            <span class="alert-icon"><i class="fa fa-th"></i></span>  <strong>Addendum</strong>
          </div>
          <div class="p-1" >
            <form id="ffAddDeliverable" class="easyui-form form-horizontal" method="post" novalidate enctype="multipart/form-data" style="padding:0 10px">
              <?php 
              $jenisArray = explode(', ', $dataaddendum->getField('JENIS')); ?>
              <div class="table-responsive">
                <table class="table table-bordered table mb-0">
                  <tbody id="tbodyDeliverable">  
                    <tr>
                      <td> Tanggal Kontrak (awal dan akhir) <br>
                        <h5><?= getFormattedDateShort2(dateTimeToPageCheck($reqWaktuPelaksanaanDari)).' s/d '.getFormattedDateShort2(dateTimeToPageCheck($reqWaktuPelaksanaanSampai)) ?></h5>
                      </td>
                      <td> Tanggal Penyelesaian Tagihan (awal dan akhir) <br>
                        <h5><?= getFormattedDateShort2(dateTimeToPageCheck($reqWaktuPenyelesaianDari)).' s/d '.getFormattedDateShort2(dateTimeToPageCheck($reqWaktuPenyelesaianSampai)) ?></h5>
                      </td>
                    </tr> 
                    <?php 
                    if ($this->LEVEL_KONTRAK == '1') { ?>
                    <tr>
                      <td> No. Addendum </td>
                      <td>
                        <input type="text" name="reqNomor" class="form-control easyui-validatebox span2"  value="<?= $dataaddendum->getField('NOMOR') ?>" required/>
                      </td>
                    </tr> 
                    <tr>
                      <td> Tanggal </td>
                      <td>
                        <input type="text" name="reqTanggal" id="reqTanggal" title="" class="form-control easyui-datebox span2" value="<?= dateToPageCheck($dataaddendum->getField('TANGGAL'))?>" required style="width: 200% !important" />
                      </td>
                    </tr> 
                    <?php 
                    } ?>
                    <tr>
                      <td> Addendum Ke </td>
                      <td>
                        <input type="text" name="reqAddendumKe" class="form-control easyui-validatebox span2"  value="<?= $dataaddendum->getField('ADDENDUM_KE') ?>" style="width:100px" maxlength="1" required/>
                      </td>
                    </tr> 
                    <tr>
                      <td> Nilai Kontrak (Addendum) </td>
                      <td>
                        <input type="text" class="form-control easyui-validatebox" required name="reqNilaiKontrak" value="<?= $reqNilaiKontrak ?>" id="reqNilai" value="" OnFocus="FormatAngka('reqNilai')" OnKeyUp="FormatUang('reqNilai')" OnBlur="FormatUang('reqNilai')">
                      </td>
                    </tr> 
                    <tr>
                      <td></td>
                      <td>
                        <input type="checkbox" name="reqJenis[]" id="chkWaktuPelaksanaan" value="Waktu Pelaksanaan" <?= in_array('Waktu Pelaksanaan', $jenisArray) ? 'checked' : '' ?>> Waktu Pelaksanaan 
                      </td>
                    </tr> 
                    <tr>
                      <td> Masa Berlaku <br>Kontrak Addendum </td>
                      <td>
                        <input type="text" name="reqTanggalKontrakDari" id="reqTanggalKontrakDari" title="" class="form-control easyui-datebox span2" value="<?= dateToPageCheck($dataaddendum->getField('TANGGAL_KONTRAK_DARI'))?>" style="width: 170% !important" /> &nbsp;&nbsp; sd &nbsp; &nbsp;
                        <input type="text" name="reqTanggalKontrakSampai" id="reqTanggalKontrakSampai" title="" class="form-control easyui-datebox span2" value="<?= dateToPageCheck($dataaddendum->getField('TANGGAL_KONTRAK_SAMPAI'))?>" style="width: 170% !important" />
                      </td>
                    </tr> 
                    <tr>
                      <td> Tanggal Penyelesaian <br>Administrasi Penagihan </td>
                      <td>
                        <input type="text" name="reqTanggalPenyelesaianKontrakDari" id="reqTanggalPenyelesaianKontrakDari" title="" class="form-control easyui-datebox span2" value="<?= dateToPageCheck($dataaddendum->getField('TANGGAL_PENYELESAIAN_KONTRAK_AWAL'))?>" style="width: 170% !important" /> &nbsp;&nbsp; sd &nbsp;&nbsp;
                        <input type="text" name="reqTanggalPenyelesaianKontrakAkhir" id="reqTanggalPenyelesaianKontrakAkhir" title="" class="form-control easyui-datebox span2" value="<?= dateToPageCheck($dataaddendum->getField('TANGGAL_PENYELESAIAN_KONTRAK_AKHIR'))?>" style="width: 170% !important" />
                      </td>
                    </tr> 
                    <tr>
                      <td></td>
                      <td>
                        <input type="checkbox" name="reqJenis[]" value="Volume" <?= in_array('Volume', $jenisArray) ? 'checked' : '' ?>> Volume 
                        <input type="checkbox" name="reqJenis[]" value="Spesifikasi Teknis" <?= in_array('Spesifikasi Teknis', $jenisArray) ? 'checked' : '' ?>> Spesifikasi Teknis 
                        <input type="checkbox" name="reqJenis[]" value="Informasi Lainnya" <?= in_array('Informasi Lainnya', $jenisArray) ? 'checked' : '' ?>> Informasi Lainnya 
                      </td>
                    </tr> 
                    <tr>
                      <td>Keterangan Tambahan</td>
                      <td>
                        <textarea class="form-control" name="reqKeterangan"><?= $dataaddendum->getField('KETERANGAN') ?></textarea>
                      </td>
                    </tr> 
                    <tr>
                      <td>Dok. Persetujuan</td>
                      <td>
                        <?php 
                        if ($this->LEVEL_KONTRAK == '2') { // PENGENDALI
                        ?>
                          <input type="file" name="reqLinkFile" id="reqLinkFilePDF" class="easyui-validatebox" validType="fileType['pdf','zip','docx']" <?php if($dataaddendum->getField('ADDENDUM_FILE_PERSETUJUAN')) { } else { echo "required"; } ?> />
                          <?php echo UPLOAD_PDF_ZIP_DOC_10MB ?>
                        <?php 
                        } else { ?>
                          <a href="uploads/kontrak/<?= $dataaddendum->getField('ADDENDUM_FILE_PERSETUJUAN') ?>" target="_blank" class="badge badge-primary"><span class="fa fa-download"></span> Download</a>
                        <?php 
                        } ?>
                        
                        <input type="hidden" name="reqLinkFileTemp" id="reqLinkFileTemp" value="<?= $dataaddendum->getField('ADDENDUM_FILE_PERSETUJUAN') ?>" />
                      </td>
                    </tr>
                    <tr>
                      <td>Dok. Addendum</td>
                      <td>
                        <?php 
                        if ($this->LEVEL_KONTRAK == '1') { // PERSIAPAN
                        ?>
                          <input type="file" name="reqLinkFile2" id="reqLinkFile2PDF" class="easyui-validatebox" validType="fileType['pdf','zip','docx']" <?php if($dataaddendum->getField('ADDENDUM_FILE')) { } else { echo "required"; } ?> />
                          <?php echo UPLOAD_PDF_ZIP_DOC_10MB ?>
                        <?php 
                        } else { 
                          if ($dataaddendum->getField('ADDENDUM_FILE')) { 
                          ?>
                          <a href="uploads/kontrak/<?= $dataaddendum->getField('ADDENDUM_FILE') ?>" target="_blank" class="badge badge-primary"><span class="fa fa-download"></span> Download</a>
                        <?php 
                          } else {
                            echo "-";
                          }
                        } ?>
                        
                        <input type="hidden" name="reqLinkFile2Temp" id="reqLinkFile2Temp" value="<?= $dataaddendum->getField('ADDENDUM_FILE') ?>" />
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>

              <div class="form-actions">
                <input type="hidden" name="reqId" id="reqId" value="<?=$reqId?>"/>
                <input type="hidden" name="reqContractingRekananId" id="reqContractingRekananId" value="<?=$reqConRekId?>"/>
                <input type="hidden" name="reqAddendumId" id="reqAddendumId" value="<?=$reqAddendumId?>"/>
                <input type="hidden" name="reqMode" value="<?=$reqMode?>">
                <input type="hidden" name="reqSubmit" id="reqSubmit" value="Submit"/>
                <button type="submit" class="<?= CLASS_BTN_PRIMARY ?>"><?= BTN_SIMPAN ?></button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>

    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="lib/bootstrap/libs/jquery/1.12.4/jquery.min.js"></script>
    <script>window.jQuery || document.write('<script src="lib/bootstrap/assets/js/vendor/jquery.min.js"><\/script>')</script>
    <script src="lib/bootstrap/dist/js/bootstrap.min.js"></script>
    
    <!-- EASYUI -->
    <link rel="stylesheet" type="text/css" href="<?=base_url()?>lib/eproc/themes/default/easyui.css">
    <script type="text/javascript" src="<?=base_url()?>lib/eproc/jquery.easyui.min.js"></script>
    <script type="text/javascript" src="<?=base_url()?>lib/eproc/kalender-easyui.js"></script>
    <script type="text/javascript" src="<?=base_url()?>lib/eproc/allfunc.js"></script>

    <script type="text/javascript">
      $(function () {

          function toggleTanggalWajib() {
              if ($('#chkWaktuPelaksanaan').is(':checked')) {
                  // jadikan wajib
                  $('#reqTanggalKontrakDari, #reqTanggalKontrakSampai, #reqTanggalPenyelesaianKontrakDari, #reqTanggalPenyelesaianKontrakAkhir').datebox({
                      required: true
                  });
              } else {
                  // tidak wajib + kosongkan
                  $('#reqTanggalKontrakDari, #reqTanggalKontrakSampai, #reqTanggalPenyelesaianKontrakDari, #reqTanggalPenyelesaianKontrakAkhir').datebox('clear');
                  $('#reqTanggalKontrakDari, #reqTanggalKontrakSampai, #reqTanggalPenyelesaianKontrakDari, #reqTanggalPenyelesaianKontrakAkhir').datebox({
                      required: false
                  });
              }
          }

          // saat checkbox di klik
          $('#chkWaktuPelaksanaan').on('change', function () {
              toggleTanggalWajib();
          });

          // saat halaman pertama kali load (mode edit)
          toggleTanggalWajib();

      });
    </script>

  </body>
</html>
