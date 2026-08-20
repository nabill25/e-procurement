<?php
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 *
 */
$this->libsession->cekSession();
$this->load->library("kauth");  $userLogin = new kauth();
$this->load->library("libapiui");
$this->load->library("paketinfo"); $paketInfo = new paketinfo();
$this->load->library("FileHandler");
$this->load->library("libplanning");
$this->load->model(array("Importsirup","PermohonanPaket","PermohonanPaketAnalisaFile","Masterchecklist","Permohonanpaketapproval","Permohonanpaketapprovalrevisi"));
include_once("functions/string.func.php");
include_once("functions/date.func.php");
include_once("functions/default.func.php");

$libapiui = new libapiui();
$libapiuiCekKirimFile = new libplanning();

$sirupId= $this->input->get("sirupId") ?: '0'; // Sirup ID
$reqId = $this->input->get("reqId") ?: '0'; // Analisa ID
$reqPerId = $this->input->get("reqPerId") ?: '0'; // Permohonan ID

$permohonan_paket = new PermohonanPaket();
$sirup = new Importsirup();
$libplanning = new libplanning();
$file = new FileHandler();
$permohonanpaketapproval = new Permohonanpaketapproval();
$permohonanpaketapprovalAll = new Permohonanpaketapproval();
$Permohonanpaketapprovalrevisi = new Permohonanpaketapprovalrevisi();

$permohonanpaketapprovalAll->selectByParams(array("PERMOHONAN_PAKET_ID" => $reqPerId));  
$permohonanpaketapproval->selectByParams(array("PERMOHONAN_PAKET_ID" => $reqPerId, "APPROVED_BY" => $this->USER_LOGIN_ID));  
$approvalStatusIndividu = $permohonanpaketapproval->countRow();

$countCek = $libapiuiCekKirimFile->headerPermohonanDokumenCount($reqId);

$permohonan_paket->selectByParams(array("A.PERMOHONAN_PAKET_ID" => $reqPerId)); 
if($permohonan_paket->countRow() > 0) // Add
{
  $permohonan_paket->firstRow();
  $reqSirupId = $permohonan_paket->getField("SIRUP_ID");
  $reqStatusApprovalAnalisa = $permohonan_paket->getField("APPROVAL"); // Approval Analisa
  $reqNoteKasubdit = $permohonan_paket->getField("NOTE_KASUBDIT"); // Approval Analisa
  $reqNama = $permohonan_paket->getField("NAMA");
  $reqTanggalWaktuPelaksanaan = $permohonan_paket->getField("TANGGAL_WAKTU_PELAKSANAAN");
  $reqLokasiPekerjaan = $permohonan_paket->getField("LOKASI_PEKERJAAN");
  $reqJenisKontrak = $permohonan_paket->getField("JENIS_KONTRAK");
  $reqPengadaanBypass = $permohonan_paket->getField("PENGADAAN_BYPASS");
  $reqKodeSirupLKPP = $permohonan_paket->getField("KODE_SIRUP_LKPP");
  $reqNilaiRABPR = $permohonan_paket->getField("NILAI_RAB_PR");

  $reqMode = 'insert';

  $sirup->selectByParams(array("ID" => $reqSirupId));
  $sirup->firstRow();

  $reqMetodePemilihan = $sirup->getField("METODE_PEMILIHAN");
  $reqNamaJenisPekerjaan = $sirup->getField("NAMA_JENIS_PEKERJAAN");

  $reqTahun = $sirup->getField("TAHUN");
  $reqKodeRUP = $sirup->getField("KODE_RUP");
  $reqKodeSA = $sirup->getField("KODE_SA");
  $reqDPSJ = $sirup->getField("KODE_DPSJ");
  $reqNoUrut = $sirup->getField("NO_URUT");
  $reqKategoriPaketID = $sirup->getField("KATEGORI_PAKET_ID");
  $reqNamaPaket = $sirup->getField("NAMA_PAKET");
  $reqNilaiPagu = $sirup->getField("NILAI_PAGU");
  $reqListKegiatan = $sirup->getField("LIST_KEGIATAN");
  $reqWaktuAwal = $sirup->getField("WAKTU_AWAL");
  $reqWaktuAkhir = $sirup->getField("WAKTU_AKHIR");
  $reqStatusProses = $sirup->getField("STATUS_PROSES");
  $reqName = $sirup->getField("NAME");
  $reqKategoriPaket = $sirup->getField("KATEGORI_PAKET");
  $reqNamaSA = $sirup->getField("NAMA_SA");
  $reqNamaDPJS = $sirup->getField("NAMA_DPSJ");
  $reqHasilVerifikasi = $sirup->getField("HASIL_VERIFIKASI");
  $reqCreatedBy = $sirup->getField("CREATED_BY");
  $reqCreatedAt = $sirup->getField("CREATED_AT");
  $reqUpdatedBy = $sirup->getField("UPDATED_BY");
  $reqUpdatedAt = $sirup->getField("UPDATED_AT");
  $reqImportDate = $sirup->getField("IMPORT_DATE");

  $reqRequisitionHeaderId = $libplanning->getRequisitionHeaderId($reqSirupId);
  // GET PR
  // $dataPR = $libapiui->getPR('2025',$reqKodeSA);
}
else // Edit
{
  redirect(base_url().'main/index/rencana_umum_pengadaan_persiapan');
}

 // echo $reqTahunAnggaran; die;
?>

<link rel="stylesheet" type="text/css" href="lib/DataTables-1.10.7/media/css/jquery.dataTables.css">
<link rel="stylesheet" type="text/css" href="lib/DataTables-1.10.7/extensions/Responsive/css/dataTables.responsive.css">
<link rel="stylesheet" type="text/css" href="lib/DataTables-1.10.7/examples/resources/syntax/shCore.css">
<link rel="stylesheet" type="text/css" href="lib/DataTables-1.10.7/examples/resources/demo.css">
<script type="text/javascript" language="javascript" src="lib/DataTables-1.10.7/media/js/jquery.dataTables.js"></script>
<script type="text/javascript" language="javascript" src="lib/DataTables-1.10.7/extensions/Responsive/js/dataTables.responsive.js"></script>
<script type="text/javascript" language="javascript" src="lib/DataTables-1.10.7/examples/resources/syntax/shCore.js"></script>
<script type="text/javascript" language="javascript" src="lib/DataTables-1.10.7/examples/resources/demo.js"></script>
<link rel="stylesheet" type="text/css" href="<?=base_url() ?>assets/new/vendors/css/extensions/zoom.css">
<script src="<?=base_url()?>assets/new/vendors/js/extensions/zoom.min.js"></script>

<script type="text/javascript">

$(document).ready(function() {
  $(function(){
    $('#ff').form({
      url:'permohonan_paket_usulan_json/permohonan_usulan_add_file',
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
        $("#ff").trigger("reset");
        if (data == 'Data berhasil disimpan.' || data == 'Data berhasil diupdate.') {
          alertSuccess2(data);
        } else {
          alertError2('Data gagal disimpan, silahkan dicoba kembali.');
        }
        setTimeout(function () { 
          document.location.href = 'main/index/permohonan_paket_usulan_add/?reqId=<?= $reqId ?>&reqPerId=<?= $reqPerId ?>';
        }, 2000);
      }
    });
  });
}); 

function createRowNotaDinas()
{
  $(function () {
    $.get("main/loadUrl/main/permohonan_lelang_add_nota_dinas_template", function (data) {
      $("#tbodyPermohonanPaketAnalisaFile").append(data);
    });
  });
}

function FormatNumberya(id)
{
   var a = parseFloat(id);
   var nilai = FormatCurrency(a);
   return nilai;
}

// ------------
// Jquery Dependency
$(document).ready(function() {
  $(function(){
    $("input[data-type='currency']").on({
        keyup: function() {
          formatCurrencyDecimal($(this));
        },
        blur: function() {
          formatCurrencyDecimal($(this), "blur");
        }
    });
  });

  $('#btnLihat').on('click', function () { 
    openAdd("main/loadUrl/main/rencana_umum_pengadaan_persiapan_lihat/?reqId="+<?= $reqId ?>+"&sirupId="+<?= $reqSirupId ?>);
  });

  // $('#btnKirimEsign').on('click', function () { 
  //   openAddFrame("main/loadUrl/main/rencana_umum_pengadaan_persiapan_kirim_file_esign/?reqId=<?= $reqId ?>&sirupId=<?= $reqSirupId ?>");
  // });

  $('#btnKirimKasubdit').on('click', function () { 
    openAddFrame("main/loadUrl/main/rencana_umum_pengadaan_persiapan_kirim_ke_kasubdit/?reqId=<?= $reqId ?>&reqPerId=<?= $reqPerId ?>");
  });


});

// -----------
var thisURLGetFileOracle = "<?= base_url('rup_json/getAttachment?reqRequisitionHeaderId='.$reqRequisitionHeaderId) ?>";

$(document).ready(function() {
    setTimeout(function() {
        reloadFileRemote();
    }, 1000); 
  // $('#reqTanggalWaktuPelaksanaan').datebox({
  //     editable: false
  //   });
});

function reloadFileRemote() {
    $('#loadingSide').show();
    $("#dataRemote").html('');
    $.get(thisURLGetFileOracle, function(response) {
        $("#dataRemote").html(response);
    })
    .always(function() {
        $('#loadingSide').hide();
    });
}

</script>

<div class="row" style="margin-top:-30px">
      <div class="col-md-12">
        <div class="form-group alert">
          <div class="float-right">
          <?php
          // if ($reqPerId) {
          //   $this->load->library("librekamjejak"); $librekamjejak = new librekamjejak();
          //   echo $librekamjejak->buttonRJ($reqPerId); }
          ?>
          <a id="btnLihat" title="Lihat" class="<?= CLASS_BTN_SUCCESS ?>" style="padding:.4rem 1rem"> <i class="fa fa-eye"></i> Lihat Detail RUP </a>
          </div>
          &nbsp;
        </div>
      </div>
    </div>

  <div class="sidebar-detached sidebar-left">
    <div class="sidebar">
      <div class="bug-list-sidebar-content">
          <!-- File Remote -->
        <div class="card">
          <div class="card-header">
            <h4 class="card-title">File PR Oracle</h4>
            <a class="heading-elements-toggle"><i class="ft-ellipsis-h font-medium-3"></i></a>
            <div class="heading-elements">
                <ul class="list-inline mb-0">
                    <li><a onclick="reloadFileRemote()"><i class="fa fa-refresh"></i></a></li>
                </ul>
            </div>
          </div>
          <!-- bug-list search -->
          <div class="card-content collapse show" style="">
            <div class="border-top-blue-grey border-top-lighten-5">
            </div>
            <!-- /bug-list search -->

            <div class="card-body ">
                <img id="loadingSide" src="<?php echo base_url('images') ?>/loader-page.gif" alt="Loading..." style="left:25% !important;" />
                <ul class="list-group card">
                    <div id="dataRemote">
                    </div>
                </ul> 
              </div>
          </div>
        </div>
        <!--/ File Remote --> 
      </div>
    </div>
  </div>

  <div class="content-detached content-right">
    <div class="content-body">
      <section class="row">
        <div class="col-12">
          <div class="card"> 
            <div class="card-content">
              <div class="card-body" id="body-content-pr">
              <div class="alert bg-primary alert-icon-left alert-arrow-left alert-dismissible mb-2" role="alert">
                <span class="alert-icon"><i class="fa fa-th"></i></span>  <strong><?= $reqNama ?></strong>
              </div>
                <ul class="nav nav-tabs nav-linetriangle">
                    <li class="nav-item">
                        <a class="nav-link active" id="pr1-tab1" data-toggle="tab" href="#pr1" aria-controls="pr1" aria-expanded="true"><i class="fa fa-align-justify"></i> Checklist Kelengkapan</a>
                    </li> 
                    <li class="nav-item">
                        <a class="nav-link" id="pr2-tab2" data-toggle="tab" href="#pr2" aria-controls="pr2" aria-expanded="true"><i class="fa fa-check-square-o"></i> Appproval</a>
                    </li>  
                </ul>
                <div id="content-import-notif"></div>
                <div class="tab-content px-1 pt-1">
                    <div role="tabpanel" style="margin-top:2%" class="tab-pane active" id="pr1" aria-labelledby="pr1-tab1" aria-expanded="true">
                  <?php 
                  if ($reqStatusApprovalAnalisa == '4') {
                     echo '<div class="alert alert-success"> <b><i>Catatan Kasubdit </i></b>: '.$reqNoteKasubdit.'</div>';
                   } ?>
                      <div class="row">
                        <table class="table table-bordered">
                          <thead>
                            <tr>
                              <th width="5%">No</th>
                              <th>Dokumen Kelengkapan</th>
                              <th width="20%">Checklist</th>
                              <!-- <th width="20%">Oleh</th> -->
                            </tr>
                          </thead>
                          <tbody>
                        <?php 
                          $masterchecklist = new Masterchecklist();
                          $masterchecklist->selectByParams(array("METODE_PEMILIHAN" => $reqMetodePemilihan, "PAKET_JENIS" => $reqNamaJenisPekerjaan),-1,-1,'','',$reqPerId); 
                          // echo $masterchecklist->query;
                          $no=1;
                          $totalWajib = 0;
                          $totalChecked = 0;
                          while($masterchecklist->nextRow())   
                          {
                            $wajib = '';
                            $data_wajib = 'no';
                            if ($masterchecklist->getField("WAJIB") == '1') {
                              $wajib = '<sup>*</sup>';
                              $totalWajib++;
                              $data_wajib = 'ya';
                            }

                            if ($masterchecklist->getField("WAJIB") == '1' && $masterchecklist->getField("APPROVED") == '1') {
                              $totalChecked++;
                            }

                            $checked = '';
                            $checked2 = '';
                            if ($masterchecklist->getField("APPROVED") == '1') {
                              $checked = 'checked';
                              $checked2 = '<span class="fa fa-check-square-o"></span> Ya, Lengkap';
                            }

                            echo '<tr>';
                            echo    '<td>'.$no.'</td>';
                            echo    '<td>'.$masterchecklist->getField("NAMA").' '.$wajib.'</td>';
                            if ($approvalStatusIndividu > 0 || $reqStatusApprovalAnalisa > 3) { 
                              echo    '<td>'.$checked2.'</td>';
                            } else {
                              echo    '<td>
                                          <input class="mb-1" type="checkbox" name="checklengkap[]" id="checklengkap'.$masterchecklist->getField("MASTER_CHECKLIST_ID").'" onclick="return updateChecklist(\''.$reqPerId.'\',\''.$masterchecklist->getField("MASTER_CHECKLIST_ID").'\')" style="cursor:pointer" data-wajib="'.$data_wajib.'" '.$checked.'> Ya, Lengkap

                                       </td>';
                            }
                            // echo    '<td>
                            //             <small>Dibuat oleh: '.$masterchecklist->getField("APPROVED_BY").'</small><br>
                            //             <small>Diubah oleh: '.$masterchecklist->getField("UPDATED_BY").'</small>
                            //           </td>';
                              
                            echo '</tr>';
                          $no++;
                          }

                         ?>
                          </tbody>
                        </table>
                        <input type="hidden" value="<?= $totalWajib ?>" name="totalWajib">
                        <input type="hidden" value="<?= $totalChecked ?>" name="totalChecked">
                        <?php 
                        if ($approvalStatusIndividu > 0) 
                        { 
                          if ($countCek && ($reqStatusApprovalAnalisa == '3' || $reqStatusApprovalAnalisa == '4')) {
                            $readonly = '';
                          } else {
                            $readonly = 'readonly';
                          }
                        ?>
                          <div class="alert alert-success text-center" style="width:100%">
                            . : : Anda telah menerima kelengkapan dokumen ini  : : .
                            <!-- <br> <b>Silahkan upload Dokumen Final</b> -->
                          </div>

                          <form id="ff" class="easyui-form form-horizontal" method="post" novalidate enctype="multipart/form-data" style="width:100%">
                            <div class="card mb-1 border-blue border-darken-1 col-md-12">
                              <div class="card-content">

                                <div class="row mt-2">
                                  <div class="form-group col-md-12 mb-2">
                                    <label>Nama Paket Pengadaan</label>
                                    <input type="text" class="form-control easyui-validatebox span8" name="reqNama" value="<?=$reqNama?>" title="Nama Paket harus diisi"  required <?= $readonly ?>>
                                  </div>
                                  <div class="form-group col-md-3 mb-2">
                                    <label style="width: 100%">Waktu Pelaksanaan </label>
                                    <input type="text" class="form-control easyui-validatebox span8" name="reqTanggalWaktuPelaksanaan" value="<?=$reqTanggalWaktuPelaksanaan?>" title="Waktu Pelaksanaan harus diisi"  required <?= $readonly ?>>
                                  </div>
                                  <div class="form-group col-md-9 mb-2">
                                    <label>Lokasi Pekerjaan</label>
                                    <input type="text" class="form-control easyui-validatebox span8" name="reqLokasiPekerjaan" value="<?=$reqLokasiPekerjaan?>" title="Lokasi Pekerjaan harus diisi"  required  <?= $readonly ?>>
                                  </div> 
                                  <div class="form-group col-md-4 mb-2">
                                    <label>Jenis Kontrak</label>
                                       <input type="text" name="reqJenisKontrak" class="easyui-combobox span4" data-options="valueField:'id',textField:'text',url:'contracting_json/comboJenisKontrak'"  value="<?=$reqJenisKontrak?>" style="width: 300% !important" required <?= $readonly ?>/>
                                  </div>
                                  <div class="form-group col-md-4 mb-2">
                                    <label>Pengadaan Bypass</label>
                                       <input type="text" name="reqPengadaanBypass" class="easyui-combobox span4" data-options="valueField:'id',textField:'text',url:'contracting_json/comboPengadaanBypass'"  value="<?=$reqPengadaanBypass?>" style="width: 300% !important" required <?= $readonly ?>/>
                                  </div>
                                  <!-- <div class="form-group col-md-4 mb-2">
                                    <label>Kode SIRUP LKPP</label>
                                    <input type="text" class="form-control easyui-validatebox span8" name="reqKodeSirupLKPP" value="<?php // echo $reqKodeSirupLKPP?>" title="Kode SIRUP LKPP harus diisi" <?php // echo $readonly ?> >
                                  </div> -->
                                </div>

                                <?php 
                                if ($countCek && ($reqStatusApprovalAnalisa == '3' || $reqStatusApprovalAnalisa == '4')) { ?>
                                <button type="submit" class="<?= CLASS_BTN_PRIMARY ?> mb-1"> <i class="fa fa-check-square-o"></i> Simpan</button>
                                <?php  
                                } ?>

                                <?php  
                                  $this->load->model(array("PermohonanPaketAnalisaFile"));
                                  $permohonanpaketanalisafile = new PermohonanPaketAnalisaFile();
                                  $statement = " AND PERMOHONAN_PAKET_ANALISA_ID = ".$reqId."";
                                  $allRecord = $permohonanpaketanalisafile->getCountByParams(array(), $statement, $sOrder);
                                if ($allRecord >= 1 && $this->LEVEL_PERENCANA == '2' && ($reqStatusApprovalAnalisa == '3' || $reqStatusApprovalAnalisa == '4')) { ?>
                                <!-- <a id="btnKirimEsign" class="<?= CLASS_BTN_DARK ?> pull-right"> <i class="fa fa-check-square-o"></i> Kirim file ke e-Sign</a> -->
                                <a id="btnKirimKasubdit" class="<?= CLASS_BTN_DARK ?> pull-right"> <i class="fa fa-send"></i> Teruskan ke Kasubdit</a>
                                <?php 
                                } ?>
                              </div>
                            </div>

                            <div class="card mb-1 border-blue border-darken-1 col-md-12">
                              <div class="card-content">
                                <div class="p-1 mt-1">
                                  <div class="alert bg-primary alert-icon-left alert-arrow-left alert-dismissible mb-2" role="alert">
                                    <span class="alert-icon"><i class="fa fa-th"></i></span>  <strong>Upload Dokumen Final</strong>
                                  </div>
                                  <div class="table-responsive">

                                    <div class="row">
                                      <div class="col-md-12 col-sm-12">
                                        <div class="card"> 
                                          <div class="card-content collapse show border-info border-darken-2">
                                            <div class="card-body area-datatable">
                                              <?php 
                                              if ($countCek && ($reqStatusApprovalAnalisa == '3' || $reqStatusApprovalAnalisa == '4')) { ?>
                                              <div class="row" id="sticker">
                                                <div class="form-group col-md-12 mb-2">
                                                  <a id="btnAdd" title="Tambah" class="<?= CLASS_BTN_PRIMARY ?>"><span class="fa fa-plus"></span> Tambah</a>
                                                  <a id="btnEdit" title="Ubah" class="<?= CLASS_BTN_INFO ?>"><span class="fa fa-pencil"></span> Edit</a>
                                                  <a id="btnDelete" title="Hapus" class="<?= CLASS_BTN_DANGER ?>"><span class="fa fa-trash"></span> Hapus</a>
                                                </div>
                                              </div>
                                              <?php 
                                              } ?>
                                              <table id="example" class="border-double table mb-0 table-bordered" style="width: 100%">
                                                <thead>
                                                  <tr>
                                                    <th>Id</th>
                                                    <th>Nama Dokumen</th>
                                                    <th width="10px">File</th>
                                                    <th width="10px">E-Sign</th>
                                                    <th width="10px">Share</th>
                                                  </tr>
                                                </thead>
                                              </table>
                                            </div>
                                          </div>
                                        </div>
                                      </div>
                                    </div>
                                  </div>

                                    <input type="hidden" name="sirupId" value="<?=$sirupId?>" />
                                    <input type="hidden" name="reqId" value="<?=$reqId?>" />
                                    <input type="hidden" name="reqPerId" value="<?=$reqPerId?>" />
                                    <input type="hidden" name="reqMode" value="<?php if($totalFile > 0) { echo "update"; } else { echo 'insert'; }?>" />

                                </div>
                              </div>
                            </div>
                          </form>

                        <?php 
                        } else { 

                          if ($reqStatusApprovalAnalisa == '2') {
                            $cekStatus = '2';
                          }

                          // if ($reqStatusApprovalAnalisa <= 3) {   // sebelum ditahap kasubdit dan ppk
                          if (($this->USER_TYPE_ID == '27' && $this->LEVEL_PERENCANA == '1') || $this->USER_TYPE_ID == '27' && $this->LEVEL_PERENCANA == '2') {
                            // code...
                        ?>

                          <div class="form-group col-md-12 mb-1 border-blue border-darken-1" style="padding: 10px 15px;">
                            <label><b>TINDAK LANJUT CHECKLIST</b></label><br>
                            <input type="radio" <?php if($cekStatus == '1') echo 'checked';?>  name="reqApproval" value="1" required /> DITERIMA &nbsp;&nbsp;&nbsp;
                            <input type="radio" <?php if($cekStatus == '2') echo 'checked';?> name="reqApproval" value="0" /> DIKEMBALIKAN
                          </div>
                          <?php 
                          }
                          // } ?>

                          <div class="table-responsive" id="catatanRevisi" style="<?php if($cekStatus != '2') echo 'display: none';?>">
                            <a id="btnAddRevisi" title="Tambah" class="<?= CLASS_BTN_PRIMARY ?> mb-1"><span class="fa fa-plus"></span> Catatan Reviu</a>
                          </div>
                          <div class="col-md-12">
                            <p><b>HISTORY REVISI</b></p>
                          </div>
                          <table class="table table-bordered">
                            <thead>
                              <tr>
                                <th width="5%">No</th>
                                <th>Catatan</th>
                                <th width="15%" class="text-center">File</th>
                                <th width="25%">Tanggal</th>
                              </tr>
                            </thead>
                            <tbody> 
                              <?php 
                              $no=1;
                              $Permohonanpaketapprovalrevisi->selectByParams(array("PERMOHONAN_PAKET_ID" => $reqPerId));  
                              if ($Permohonanpaketapprovalrevisi->countRow() > 0) {
                                while ($Permohonanpaketapprovalrevisi->nextRow()) {
                                  $tglApproved = explode(" ", $Permohonanpaketapprovalrevisi->getField('CREATED_DATE'));
                                  echo '<tr>';
                                  echo    '<td>'.$no.'</td>';
                                  echo    '<td>'.$Permohonanpaketapprovalrevisi->getField('CATATAN').'</td>';
                                  if ($Permohonanpaketapprovalrevisi->getField('FILE')) {
                                  echo    '<td class="text-center"><a target="_blank" href="uploads/permohonan/'.$Permohonanpaketapprovalrevisi->getField('FILE').'" class="badge badge-primary"><span class="fa fa-download"></span> Download</a></td>';
                                  } else {
                                  echo    '<td class="text-center"><a target="_blank">-</td>';
                                  }
                                  echo    '<td>'.getFormattedDate($tglApproved[0]).' '.$tglApproved[1].'</td>';
                                  echo '</tr>';
                                $no++;
                                 } 
                              } else {
                                echo '<tr><td colspan="4" class="text-center">. : : Data belum ada : : .</td></tr>';
                              }
                              ?>
                            </tbody>
                          </table>
                        <?php 
                        } ?>
                        

                      </div>
                    </div> 
                    <div role="tabpanel" style="margin-top:2%" class="tab-pane" id="pr2" aria-labelledby="pr2-tab2" aria-expanded="true"> 
                      <p>HISTORY APPROVAL</p>
                      <table class="table table-bordered">
                        <thead>
                          <tr>
                            <th width="5%">No</th>
                            <th>Nama</th>
                            <th width="30%">Tanggal Approve</th>
                          </tr>
                        </thead>
                        <tbody>
                          <?php 
                          $no=1;
                          if ($permohonanpaketapprovalAll->countRow() > 0) {
                            while ($permohonanpaketapprovalAll->nextRow()) {
                              $tglApproved = explode(" ", $permohonanpaketapprovalAll->getField('CREATED_DATE'));
                              echo '<tr>';
                              echo    '<td>'.$no.'</td>';
                              echo    '<td>'.$permohonanpaketapprovalAll->getField('APPROVED_BY_STR').'</td>';
                              echo    '<td>'.getFormattedDate($tglApproved[0]).' '.$tglApproved[1].'</td>';
                              echo '</tr>';
                            $no++;
                             } 
                          } else {
                            echo '<tr><td colspan="3" class="text-center">. : : Data belum ada : : .</td></tr>';
                          }
                          ?>
                        </tbody>
                      </table>
                    </div>  
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>
    </div>
  </div>
 
<script type="text/javascript" language="javascript" class="init">
var oTable;
$(document).ready(function() {

  oTable = $('#example').dataTable({ bJQueryUI: true,"iDisplayLength": 10,
    /* UNTUK MENGHIDE KOLOM ID */
    "aoColumns": [
             {"bVisible": false},
             null,
             null,
             null,
             null,
          ],
    "bSort":true,
    "bProcessing": true,
    "bServerSide": true,
    "sAjaxSource": "permohonan_paket_usulan_json/files?reqId=<?= $reqId ?>",
    "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
    columnDefs: [{ className: 'never', targets: [0] },{ className: 'text-center', targets: [3,4] }]
    });//.rowGrouping({iGroupingColumnIndex: 0});
    oTable.fnSort( [ [0,'desc'] ] );

    new $.fn.dataTable.Responsive( oTable );
      var anSelectedData = '';
      var anSelectedId = '';
      var anSelectedIdDelete = '';
      var anSelectedDownload = '';
      var anSelectedPosition = '';

      function fnGetSelected( oTableLocal )
      {
        var aReturn = new Array();
        var aTrs = oTableLocal.fnGetNodes();
        for ( var i=0 ; i<aTrs.length ; i++ )
        {
          if ( $(aTrs[i]).hasClass('row_selected') )
          {
            aReturn.push( aTrs[i] );
            anSelectedPosition = i;
          }
        }
        return aReturn;
      }

      $("#example tbody").click(function(event) {
          $(oTable.fnSettings().aoData).each(function (){
            $(this.nTr).removeClass('row_selected');
          });
          $(event.target.parentNode).addClass('row_selected');
          //
          var anSelected = fnGetSelected(oTable);
          anSelectedData = String(oTable.fnGetData(anSelected[0]));
          var element = anSelectedData.split(',');
          anSelectedId = element[0];
          anSelectedIdDelete = element[1];
      });

       $('#btnAdd').on('click', function () {
        openAddFrame("main/loadUrl/main/permohonan_paket_analisa_files?reqId=<?= $reqId ?>");
        $('div.flexmenumobile').hide()
        $('div.flexoverlay').css('display', 'none')

      });

      $('#btnEdit').on('click', function () {
        if(anSelectedData == "")
        {
          alertError3("Pilih data dahulu");
        return false;
          }
        openAddFrame("main/loadUrl/main/permohonan_paket_analisa_files/?reqFileId="+anSelectedId);
        $('div.flexmenumobile').hide()
        $('div.flexoverlay').css('display', 'none')
      });

      $('#btnDelete').on('click', function () {
        if(anSelectedData == "")
          {
            alertError3("Pilih data dahulu");
          return false;
            }
        deleteData("permohonan_paket_usulan_json/deleteFileAnalisa/", anSelectedId);
      }); 
} );

function reloadMonitoring()
{
  oTable.fnReloadAjax("permohonan_paket_usulan_json/files?reqId=<?= $reqId ?>");
}

function reloadMonitoringReload()
{
  location.reload();
}


</script>
  <script type="text/javascript">
    function updateChecklist(reqPerId, masterchecklistId) {
      let checkbox = $("#checklengkap" + masterchecklistId);
      let data_wajib = $("#checklengkap" + masterchecklistId).data('wajib');
      let n = checkbox.prop("checked") ? 1 : 0;

       // 🔥 HITUNG HANYA CHECKLIST WAJIB YANG TER-CHECK
      let totalWajibChecked = $('input[name="checklengkap[]"][data-wajib="ya"]:checked').length;

      $('input[name="totalChecked"]').val(totalWajibChecked);

      // Lanjut request
      $.getJSON("permohonan_paket_checklist_json/updateChecklist/?reqPerId=" + reqPerId +
      "&masterchecklistId=" + masterchecklistId +
      "&status=" + n,
      function (data) {
        if (data.RESPONSE === 'Gagal') {
          checkbox.prop("checked", !checkbox.prop("checked"));
          alertError2(data.PESAN);
        } else {
          alertSuccess2(data.PESAN);
        }
      });
    } 

    function updateFileCheck(reqId, jenis) {
      let checkbox = $("#"+ jenis + reqId);
      let n = checkbox.prop("checked") ? 1 : 0;
      // Lanjut request
      $.getJSON("permohonan_paket_checklist_json/updateFileCheck/?reqId=" + reqId +
      "&jenis=" + jenis +
      "&status=" + n,
      function (data) {
        if (data.RESPONSE === 'Gagal') {
          checkbox.prop("checked", !checkbox.prop("checked"));
          alertError2(data.PESAN);
        } else {
          alertSuccess2(data.PESAN);
        }
      });
    } 

    $(document).ready(function() {
      $('input:radio[name=reqApproval]').change(function() {
        var n = this.value;
        if (this.value == '1') {
          var totalWajib = $('input[name="totalWajib"]').val();
          var totalChecked = $('input[name="totalChecked"]').val();

          if (totalWajib === totalChecked) {
            $('#catatanRevisi').hide();
            $.messager.confirm('Konfirmasi',"Anda yakin akan menerima checklist ini? setelah diterima, anda tidak akan bisa lagi untuk merubah",function(r){
              if (r){
                $.post("permohonan_paket_approval_json/approval/", 
                {
                    approval: n,
                    permohonanId: <?= $reqPerId ?>
                }, 
                function(data) {
                  var json = JSON.parse(data);
                  if (json.RESPONSE === 'Gagal') { 
                    alertError2(json.PESAN);
                  } else {
                    alertSuccess2(json.PESAN);
                  }
                  setTimeout(function() {
                    location.reload();
                  }, 3000);
                });
              } else {
                $('input[name="reqApproval"]').prop('checked', false);
              }
            });
          } else {
            alertError2('Checklist dokumen yang wajib belum lengkap, silahkan checklist dahulu!');
            $('input[name="reqApproval"]').prop('checked', false);
          }
        }
        else if (this.value == '0') {
          $('#catatanRevisi').show();
        }
      });

      $('#btnAddRevisi').on('click', function () {
        openAddFrame("main/loadUrl/main/permohonan_paket_revisi_add?reqId="+<?= $reqPerId ?>);

      });
    });
 
    </script>
