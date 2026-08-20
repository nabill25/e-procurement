<?php
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 * 
 */
$this->libsession->cekSession();

$this->load->library("kauth");  $userLogin = new kauth();
$this->load->library("paketinfo"); $paketInfo = new paketinfo();
$this->load->model("PermohonanPaket");
$this->load->model("PermohonanPaketFile");
$this->load->library("FileHandler");
include_once("functions/string.func.php");
include_once("functions/date.func.php");
include_once("functions/default.func.php");

$reqId= $this->input->get("reqId");

$permohonan_paket = new PermohonanPaket();
$permohonan_paket_analisa = new PermohonanPaket();
$permohonan_paket_file = new PermohonanPaketFile();
$file = new FileHandler();

if($reqId == '')
{
  $reqMode ='insert';
  $reqMetodePengadaan = '0';
}
else {

  $reqMode = 'update';

  $permohonan_paket->selectByParams(array("A.PERMOHONAN_PAKET_ID" => coalesce($reqId, 0), "A.USER_LOGIN_ID" => $this->USER_LOGIN_ID));
  $permohonan_paket->firstRow();

  $reqUserLoginId = $permohonan_paket->getField("USER_LOGIN_ID");
  $reqNotaDinas = $permohonan_paket->getField("NOTA_DINAS");
  $reqNomorPPA = $permohonan_paket->getField("NO_PPA");
  $reqTanggal = dateToPageCheck($permohonan_paket->getField("TANGGAL"));
  $reqNamaPaket = $permohonan_paket->getField("NAMA");
  $reqNilai = $permohonan_paket->getField("NILAI");
  $reqKeterangan = $permohonan_paket->getField("KETERANGAN");
  $reqAlasan = $permohonan_paket->getField("ALASAN_TOLAK");
  $reqMetodePengadaan = $permohonan_paket->getField("PENGADAANLANGSUNG") ?: '0';
  if ($reqNilai == '') {
    $reqNilai = $permohonan_paket->getField("PERKIRAAN_BIAYA_HARGA");
  }
  $reqPermohonanPaketAnalisaId = $permohonan_paket->getField("PERMOHONAN_PAKET_ANALISA_ID");
  $reqBudgetAwal = $permohonan_paket->getField("BUDGET_AWAL");
  $reqBudgetTerpakai = $permohonan_paket->getField("BUDGET_TERPAKAI");
  $reqBudgetAkhir = $permohonan_paket->getField("BUDGET_AKHIR");
  $reqBudgetAkhir = $permohonan_paket->getField("BUDGET_AKHIR");
  $reqTahunAnggaran = $permohonan_paket->getField("TAHUN_ANGGARAN");

  if ($this->USER_LOGIN_ID != $reqUserLoginId) {
    redirect(base_url('main/index/404'));
  }
}

?>
<style type="text/css">
#setNilaiHPS { color: red; font-size: 10px; }
</style>
<script type="text/javascript">

$(document).ready(function() {

  $(function(){
    $('#ff').form({
      url:'permohonan_paket_json/permohonan_lelang_add',
      onSubmit:function(){
        var v=$(this).form('validate');
        if(v) showLoad();  // show the message box
        return v;
      },
      success:function(data){
        var hasil=data.split('-');
        hideLoad();
        if (hasil[2] === '2') { //  Pembelian langsung
          alertSuccess2(hasil[0]);
          document.location.href = 'main/index/permohonan_paket_fungsional_add/?reqId='+hasil[1];
        } else {
          document.location.href = 'main/index/paket_lelang_tambah_rincian_pekerjaan_permohonan/?reqPer='+hasil[1];
        }
      }
    });

  });

  // $(function(){
  //   $('input[name="reqNilai"]').on('change', function() {
  //     var textValue = $('input[name="reqNilai"]').val();
  //         repTextValue = textValue.replace(/\./g,'');
  //         radioValue = $('input[name="reqMetodePengadaan"]:checked').val();
  //       if (repTextValue > 1000000000) {
  //         $("#reqMetodePengadaan-0").prop("checked", true);
  //       }
  //       if (repTextValue > 50000000 && repTextValue < 1000000000) {
  //         $("#reqMetodePengadaan-1").prop("checked", true);
  //       }
  //       if (repTextValue < 50000000) {
  //         $("#reqMetodePengadaan-2").prop("checked", true);
  //       }
  //   });
  // });

  // function cekReqNilai() {
  //   alert('aaa');
  //   var textValue = $('input[name="reqNilai"]').val();
  //       repTextValue = textValue.replace(/\./g,'');
  //       if (repTextValue <= 300000000) // <= 300jt
  //       {
  //         // alert('tampilkan');
  //           $('#pilihLaksanakan').show();
  //       } else {
  //         // alert('sembunyikan');
  //           $("#reqMetodePengadaan-0").prop("checked", true);
  //           // $('#pilihLaksanakan').hide();
  //       }
  // }

  //  $(function(){
  //   $('input[name="reqMetodePengadaan"]').on('change', function() {
  //       var textValue = $('input[name="reqNilai"]').val();
  //           repTextValue = textValue.replace(/\./g,'');
  //           radioValue = $('input[name="reqMetodePengadaan"]:checked').val();
  //       if(radioValue == "0") // Tender
  //       {
  //         if (repTextValue < 1000000000) {
  //           alertError2('Nilai HPS Tender > 1.000.000.000');
  //           $('#reqNilai').val('1.000.000.001');
  //           $('#setNilaiHPS').html('diatas Rp.1.000.000.000,-');
  //         }
  //       }
  //       if(radioValue == "1") // Non Tender
  //       {
  //         if (repTextValue > 1000000000) {
  //           alertError2('Nilai HPS Non Tender < 1.000.000.000');
  //           $('#reqNilai').val('1.000.000.000');
  //           $('#setNilaiHPS').html('diatas Rp. 50.000.000,- s.d Rp.1.000.000.000,-');
  //         }
  //       }
  //       if(radioValue == "2") // Purchasing
  //       {
  //         if (repTextValue > 50000000) {
  //           alertError2('Nilai HPS Purchasing < 50.000.000');
  //           $('#reqNilai').val('10.000.000');
  //           $('#setNilaiHPS').html('s.d Rp.50.000.000,-');
  //         }
  //       }
  //       if(radioValue == "3") // Kompetisi
  //       {
  //         if (repTextValue < 50000000) {
  //           alertError2('Nilai HPS Kompetisi > 50.000.000');
  //           $('#reqNilai').val('50.000.000');
  //           $('#setNilaiHPS').html('diatas Rp.50.000.000,-');
  //         }
  //       }
  //   });
  // });

  // function cekNilai(radioValue,repTextValue) {
  //   if(radioValue == "1")
  //     {
  //       if (repTextValue <= 300000000) // <= 300jt
  //       {
  //       } else {
  //         alert('Nilai harus dibawah atau sama dengan 300jt');
  //         $('#reqNilai').val('300.000.000');
  //       }
  //     }
  //     else if(radioValue == "2")
  //     {
  //       if (repTextValue > 300000000 && repTextValue <= 500000000) // > 300jt & <= 500
  //       {
  //       } else {
  //         alert('Nilai harus diatas 300jt & dibawah sama dengan 500jt');
  //         $('#reqNilai').val('300.000.001');
  //       }
  //     }
  // }
});

function createRowNotaDinas()
{
  $(function () {
    $.get("main/loadUrl/main/permohonan_lelang_add_nota_dinas_template", function (data) {
      $("#tbodyPermohonanPaketFile").append(data);
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
});

</script> 

<div class="row">
  <div class="col-md-12 col-sm-12">
    <div class="card">
      <div class="card-header card-head-inverse bg-primary">
        <h4 class="card-title text-white">Rencana Pengadaan
        <?php
          $this->load->library("librekamjejak"); $librekamjejak = new librekamjejak();
          echo $librekamjejak->buttonRJ($reqId); ?>
        <!-- <a onclick="openAdd('main/loadUrl/main/permohonan_paket_usulan_lihat?usulanId=<?= $reqPermohonanPaketAnalisaId ?>')" class="btn btn-danger mr-1 text-white"  style="padding:.4rem 1rem">
          <i class="fa fa-paper-plane-o"></i> Lihat Usulan
        </a> -->
        </h4>
        <a class="heading-elements-toggle"><i class="fa fa-ellipsis-v font-medium-3"></i></a>
        <div class="heading-elements">
            <ul class="list-inline mb-0">
              <li><a data-action="expand"><i class="ft-maximize"></i></a></li>
              <!-- <li><a data-action="close"><i class="ft-x"></i></a></li> -->
            </ul>
        </div>
      </div>

      <form id="ff" class="easyui-form form-horizontal" method="post" novalidate enctype="multipart/form-data">
      <div class="card-content collapse show border-info border-darken-2">
          <div class="card-body">
          <?php
          if($reqAlasan == '')
          {}
          else
          {
          ?>
            <div class="card mb-1 border-blue border-darken-1">
              <div class="card-content">
                <div class="p-1">
                 <div class="row">
                  <div class="form-group col-md-12 mb-2">
                    <h4 style="color: red">Alasan Dikembalikan</h4>
                    <span style="font-weight: normal"><?=$reqAlasan?></span>
                  </div>
                </div>
                </div>
              </div>
            </div>
          <?php
          }
          ?>
          <!-- <div class="row">
            <div class="form-group col-md-12 mb-2">
              <label>No. SiRUP </label>
              <input type="text" name="reqNotaDinas" id="reqNotaDinas" class="form-control span5 easyui-validatebox" required value="<?=$reqNotaDinas?>"/>
            </div>
          </div> -->
          <div class="row">
            <div class="form-group col-md-7 mb-2">
              <label>No. Nota</label>
              <input type="text" name="reqNomorPPA" id="reqNomorPPA" class="form-control span5 easyui-validatebox" required value="<?=$reqNomorPPA?>"/>
            </div>
            <div class="form-group col-md-3 mb-2">
              <label style="width: 100%">Tanggal Nota</label>
              <input type="text" name="reqTanggal" id="reqTanggal" class="form-control span2 easyui-datebox" required value="<?=$reqTanggal?>" style="width: 300% !important"/>
            </div>
            <div class="form-group col-md-2 mb-2">
              <label style="width: 100%">Tahun Anggaran</label>
              <select class="form-control" id="setyear" name="reqTahunAnggaran">
                <?php
                $selected = '';
                $kurangdari = date('Y')+3;
                      echo '<option value="">-- Pilih Tahun --</option>';
                for ($i= (date('Y')-1); $i <= $kurangdari; $i++) {
                     if ($i == date('Y') || $i == $reqTahunAnggaran) {
                     // if ($i == $getTahun) {
                      $selected = 'selected';
                     } else {
                      $selected = '';
                     }
                      echo '<option value="'.$i.'" '.$selected.'>'.$i.'</option>';
                }
                ?>
              </select>
            </div>
          </div>
          <div class="row">
            <div class="form-group col-md-12 mb-2">
              <label>Nama Paket</label>
              <input type="text" name="reqNamaPaket" id="reqNamaPaket" class="form-control span9 easyui-validatebox" required value="<?=$reqNamaPaket?>"/>
            </div>
          </div>
          <div class="row">
            <div class="form-group col-md-4 mb-12">
              <label>Harga Perkiraan </label> <!-- <span id="setNilaiHPS">diatas Rp.1.000.000.000,-</span></label> -->
              <input type="text" name="reqNilai" id="reqNilai" class="form-control span3 easyui-validatebox" required value="<?=$reqNilai?>" pattern="^\$\d{1,3}(,\d{3})*(\.\d+)?$" data-type="currency" />
                    <sup><i>gunakan tanda titik untuk decimal </i> (contoh: 89,000.50)</sup>
            </div>
          </div>

          
          <!-- <div class="row" id="pilihLaksanakan">
            <div class="form-group col-md-12 mb-2">
              <label style="width: 100%">Usulan Metode Pengadaan <a onclick="openAdd('main/loadUrl/notif/help_usulan_metode_pengadaan')" class="fa fa-question-circle" style="color:red;font-size: 1.3em;"></a></label> -->
              <?php
              // $pl = array(
              //             '0' => 'Tender',
              //             '1' => 'Pengadaan Langsung / Penunjukan Langsung',
              //             '2' => 'Purchasing (Katalog)',
              //             '3' => 'Kompetisi',
              //           );
              // $pl = array(
              //             '0' => 'Dilaksanakan oleh Unit Kerja Pengadaan',
              //             // '1' => 'Dilaksanakan oleh pengguna <span style="color:red;">( <u><i>Maksimal 300.000.000</i></u> )</span>',
              //             '2' => 'Dilaksanakan oleh Pejabat Pengadaan (Pembelian melalui katalog)</span>',
              //           );
              // foreach ($pl as $key => $value) {
              //   if ($reqMetodePengadaan == $key) {
              //     $checked = 'checked';
              //   } else {
              //     $checked = '';
              //   }
                  ?>
                <!-- <input value="<?php // $key ?>" name="reqMetodePengadaan" id="reqMetodePengadaan-<?php //$key ?>" type="radio" <?php // $checked ?>/>  -->
                <input value="<?= $reqMetodePengadaan ?>" name="reqMetodePengadaan" id="reqMetodePengadaan" type="hidden"/>
                &nbsp; <?php // $value ?> &nbsp; <br>
              <?php
              // }
              ?>
            <!-- </div>
          </div> -->
          <!-- <div class="row">
            <div class="form-group col-md-12 mb-2">
              <label style="width: 100%">Pengadaan Langsung ?</label>
              <?php /*
              $pl = array(
                          '0' => 'Tidak',
                          '1' => '<= 300jt',
                          '2' => '> 300jt & <= 500jt',
                        );
              foreach ($pl as $key => $value) {
                if ($reqMetodePengadaan == $key) {
                  $checked = 'checked';
                } else {
                  $checked = '';
                }
                  */?>
                <input value="<?= $key ?>" name="reqMetodePengadaan" id="reqMetodePengadaan-0" type="radio" <?= $checked ?>/>
                &nbsp; <?= $value ?> &nbsp;
              <?php
              //}
              ?>
            </div>
          </div> -->
          <!-- <div class="row">
            <div class="form-group col-md-12 mb-2">
              <label style="width: 100%">Keterangan</label>
              <textarea name="reqKeterangan" style="width: 100%" id="reqKeterangan" cols="45" rows="5" class="textarea-tinymce easyui-validatebox span9" required><?=$reqKeterangan?></textarea>
            </div>
          </div> -->
          <div class="card mb-1 border-blue border-darken-1">
            <div class="card-content">
              <div class="p-1">
                <div class="alert bg-primary alert-icon-left alert-arrow-left alert-dismissible mb-2" role="alert">
                  <span class="alert-icon"><i class="fa fa-th"></i></span>  <strong>Upload Dokumen</strong>
                  <!-- <a onclick="createRowNotaDinas()"> <span class="fa fa-plus text-white font-medium-2 icon-line-height btn btn-danger mr-1" data-toggle="tooltip" data-placement="top" title="Tambah"></span>  tambah
                  </a>  -->
                </div>
                <div class="table-responsive">
                  <table class="border-double table mb-0">
                    <thead>
                        <tr class="judul-kolom">
                            <th>Nama Dokumen</th>
                            <th>File <small> Format file .pdf.zip & Maksimal ukuran file 2MB </small></th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="tbodyPermohonanPaketFile">
                        <?php
                        $permohonan_paket_file = new PermohonanPaketFile();
                        $no = 1;
                        $permohonan_paket_file->selectByParams(array("PERMOHONAN_PAKET_ID" => coalesce($reqId, 0)));
                        //echo $permohonan_paket_file->query;exit;
                        if ($permohonan_paket_file->countRow() > 0) {
                          while($permohonan_paket_file->nextRow())
                          {
                        ?>
                         <tr id="tr1<?=$no?>">
                             <td>
                                <input class="form-control easyui-validatebox span4" type="hidden"  name="reqPermohonanPaketFileId[]" id="reqPermohonanPaketFileId<?=$no?>" value="<?=$permohonan_paket_file->getField("PERMOHONAN_PAKET_FILE_ID")?>"/>

                                <input class="form-control easyui-validatebox span4" type="text" required name="reqJudul[]" id="reqJudul<?=$no?>" value="<?=$permohonan_paket_file->getField("JUDUL")?>" <?php if ($no <= 4) {?> readonly <?php } ?>/>
                             </td>
                             <td>
                                <input type="file" name="reqLinkFile[]" id="reqLinkFile<?=$no?>" size="30" class="easyui-validatebox" validType="fileType['pdf','zip']"/>
                                <input type="hidden" name="reqLinkFileTemp[]" id="reqLinkFileTemp<?=$no?>" value="<?=$permohonan_paket_file->getField("PATH_FILE")?>">
                                <input type="hidden" name="reqLinkFileTempTipe[]" id="reqLinkFileTempTipe<?=$no?>" value="<?=$permohonan_paket_file->getField("TIPE")?>">
                                <input type="hidden" name="reqLinkFileTempUkuran[]" id="reqLinkFileTempUkuran<?=$no?>" value="<?=$permohonan_paket_file->getField("UKURAN")?>">
                                <input type="hidden" name="reqUrut[]" id="reqUrut<?=$no?>" value="<?=$permohonan_paket_file->getField("URUT")?>">
                                <br>
                             </td>
                             <td>
                                <?php
                                if ($permohonan_paket_file->getField("PATH_FILE")) {
                                   echo '<a href="uploads/permohonan_paket/'.$permohonan_paket_file->getField("PATH_FILE").'" target="_blank"><i class="fa fa-download"></i></a>';
                                 } ?>

                                 <?php
                                 if($no <=5)
                                 {}
                                 else
                                 {
                                 ?>
                                    <a onClick="deleteDataTable('permohonan_paket_file_json/delete/', '<?=$permohonan_paket_file->getField("PERMOHONAN_PAKET_FILE_ID")?>', 'tr1<?=$no?>')"
                                        class="btn-aksi"><i class="fa fa-trash" aria-hidden="true" ></i></a>
                                        </a>
                                 <?php
                                 }
                                 ?>
                              </td>
                         </tr>

                        <?php
                            $no = $no + 1;
                          }
                        }

                        if($no == 1)
                        {
                            // $arrDokumenWajib = array("Ijin Prinsip", "Nota Dinas", "KAK/Spesifikasi", "HPS");
                            $arrDokumenWajib = array("Nota/Memo/Ijin Prinsip", "KAK/TOR/Spesifikasi", "HPS");
                            for($i=0; $i<count($arrDokumenWajib);$i++)
                            {
                        ?>
                                 <tr id="tr1<?=$no?>">
                                     <td>
                                            <input class="form-control easyui-validatebox" type="hidden"  name="reqPermohonanPaketFileId[]" id="reqPermohonanPaketFileId<?=$no?>" value="" />
                                            <input class="form-control easyui-validatebox span4" type="text" required name="reqJudul[]" id="reqJudul<?=$no?>" value="<?=$arrDokumenWajib[$i]?>" readonly />
                                     </td>
                                     <td>
                                            <input type="file" name="reqLinkFile[]" id="reqLinkFile[]<?=$no?>" size="30" class="easyui-validatebox" validType="fileType['pdf','zip']" required />
                                            <!-- <small> <br>Format file .pdf & Maksimal ukuran file 10MB </small> -->
                                            <input type="hidden" name="reqLinkFileTemp[]" id="reqLinkFileTemp[]<?=$no?>" value="">
                                            <input type="hidden" name="reqLinkFileTempTipe[]" id="reqLinkFileTempTipe[]<?=$no?>" value="">
                                            <input type="hidden" name="reqLinkFileTempUkuran[]" id="reqLinkFileTempUkuran[]<?=$no?>" value="">
                                            <input type="hidden" name="reqUrut[]" id="reqUrut<?=$no?>" value="<?=$no?>">
                                            <?php
                                            if ($permohonan_paket_file->getField("PATH_FILE")) {
                                            ?>
                                            <br>file : <?=$permohonan_paket_file->getField("PATH_FILE")?>
                                            <?php
                                             } ?>
                                     </td>
                                     <td>
                                     </td>
                                </tr>
                            <?php
                            $no++;
                            }
                        }
                        ?>
                    </tbody>
                  </table>
                  <a onclick="createRowNotaDinas()" class="btn round btn-min-width box-shadow-1 btn-primary m-1 text-white">
                    <i class="fa fa-plus"></i> Tambah Dokumen
                  </a>
                </div>
              </div>
            </div>
          </div>
          <div class="form-actions">
            <input type="hidden" name="reqId" value="<?=$reqId?>" />
            <input type="hidden" name="reqMode" value="<?=$reqMode?>" />
            <a href="main/index/permohonan_paket_fungsional" class="btn round btn-min-width box-shadow-1 btn-danger mr-1 text-white"> <i class="fa fa-arrow-left"></i> Kembali </a>
            <button type="submit" class="btn round btn-min-width box-shadow-1 btn-primary "> <i class="fa fa-check-square-o"></i> Simpan</button>
            <?php
            if ($reqId != '' && $reqMetodePengadaan != '2') { 
              if ($reqNomorPPA != '') {
            ?>
              <a href="main/index/paket_lelang_tambah_rincian_pekerjaan_permohonan/?reqPer=<?= $reqId ?>" class="btn round btn-min-width box-shadow-1 btn-primary pull-right mr-1 text-white"> Lanjut <i class="fa fa-arrow-right"></i></a>
            <?php
             }
            } ?>
          </div>

        </div>
      </div>
      </form>

    </div>
  </div>
</div>