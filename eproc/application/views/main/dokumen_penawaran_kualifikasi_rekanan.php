<?php
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 * 
 */

$this->libsession->cekSessionKualifikasi($this->input->get("reqId"));

$this->load->library("paketinfo"); $paketInfo = new paketinfo();
$this->load->model("PaketDokumen");
$this->load->model("PaketEvaluasiKualifikasi");
$this->load->model("PaketRekanan");
$this->load->model("PaketRekananPassword");
$this->load->model("PaketTahap");
include_once("functions/string.func.php");
include_once("functions/encrypt2.func.php");
include_once("functions/date.func.php");
include_once("functions/default.func.php");

$paket_dokumen = new PaketDokumen();
$paket_evaluasi_kualifikasi = new PaketEvaluasiKualifikasi();
$paket_rekanan = new PaketRekanan();
$paket_rekanan_password = new PaketRekananPassword();
$paket_tahap = new PaketTahap();
$paket_tahap_metode = new PaketTahap();

$reqId = httpFilterRequest("reqId");

$FILE_DIR = "uploads/penawaran/";

$paket_rekanan->selectByParamsPaketLelang(array("PAKET_ID" => $reqId, "REKANAN_ID" => $this->ID), -1, -1, " AND TANGGAL_DAFTAR IS NOT NULL ");
$paket_rekanan->firstRow();
if($paket_rekanan->getField("PAKET_REKANAN_ID") == "")
  exit;
$kirimPenawaran = $paket_rekanan->getField("KIRIM_PENAWARAN"); // 0: belum kirim, 1: sudah kirim
$reqKirimPenawaranKode = $paket_rekanan->getField("KIRIM_PENAWARAN_KODE");

$paket_rekanan_password->selectByParamsLimit1(array("PAKET_REKANAN_ID" => $paket_rekanan->getField("PAKET_REKANAN_ID")), -1, -1, " ORDER BY PAKET_REKANAN_PASSWORD_ID DESC");
$paket_rekanan_password->firstRow();
$reqPenawaranPasswrd = $paket_rekanan_password->getField("PENAWARAN_PASSWORD");
$reqPenawaranPasswrd2 = $paket_rekanan_password->getField("PENAWARAN_PASSWORD2");

// echo $reqKirimPenawaranKode; die();

/* VALIDASI WAKTU HABIS */
$jenis_tahap = $paket_tahap_metode->getJenisTahapById($reqId);

$arrDokumenPenawaran            = UPLOAD_DOKUMEN_KUALIFIKASI; // ikn

$aktif_dok_penawaran1 = $paket_tahap->getCountByParamsAktif(array("URUT" => $arrDokumenPenawaran[$jenis_tahap], "PAKET_ID" => $reqId, "TAMPILKAN" => 1, "HADIR" => 0));
// echo $aktif_dok_penawaran1; die();
if($aktif_dok_penawaran1 == 0) // waktu nya habis
  $kirimPenawaran = "1"; // dianggap sudah melakukan penawaran

$paketInfo->getPaket($reqId);
$reqNama = $paketInfo->nama;
$reqSistemSampul = $paketInfo->sistem_sampul;
$reqMetodeLelang = $paketInfo->metode_lelang_id;
$reqUUID = $paketInfo->uuid;

/* CHECK APAKAH SUDAH DIPASSWORD */
$paket_dokumen->selectByParams(array("PAKET_ID" => $reqId, "REKANAN_USER_ID" => coalesce($this->ID,0), "NOT COALESCE(FILE_PASSWORD, 'X')" => 'X'));
$paket_dokumen->firstRow();

$paket_evaluasi_kualifikasi->selectByParamsRekananDokumen($this->ID, array("A.PAKET_ID" => $reqId));
//echo $paket_evaluasi_kualifikasi->query;exit;
?>

<script src="lib/ajax-upload/jquery1.12.4.min.js" type="text/javascript"></script>
<link href="lib/ajax-upload/style.css" rel="stylesheet" type="text/css">
<style type="text/css">
  th { background-color: #ffffe0; }
</style>

<div class="row">
  <div class="col-md-12 col-sm-12">
    <div class="card">
      <div class="card-header card-head-inverse bg-primary">
        <h4 class="card-title text-white"><?=translate("Upload Dokumen Kualifikasi", "Proposal Documents")?></h4>
        <a class="heading-elements-toggle"><i class="fa fa-ellipsis-v font-medium-3"></i></a>
        <div class="heading-elements">
            <ul class="list-inline mb-0">
              <li><a data-action="expand"><i class="ft-maximize"></i></a></li>
              <!-- <li><a data-action="close"><i class="ft-x"></i></a></li> -->
            </ul>
        </div>
      </div>
      <div class="card-content collapse show border-info border-darken-2">
        <div class="card-body area-datatable">
          <div class="table-responsive">
            <div class="alert alert-info">
                <span> - Format dokumen kualifikasi adalah PDF (.pdf)</span> <br> </span> 
                <span> - Pastikan dokumen terupload dengan benar, dokumen dapat dirubah sebelum masa Upload Dokumen kualifikasi berakhir, dokumen yang diakui adalah dokumen yang terakhir diupload<br></span>
                <span> - Batas maksimal ukuran file adalah 25 MB<br></span>
            </div>
            <?php
            if($aktif_dok_penawaran1 == 0)
            {
              if($paket_rekanan->getField("KIRIM_PENAWARAN") == "1")
              {}
              else
              {
            ?>
              <div class="alert alert-danger">
                <span>
                  Waktu upload / update dokumen kualifikasi telah usai atau belum mulai
                </span>
              </div>
            <?php
              }
            }
            ?>
          <table class="table table-bordered">  
                  <tr>
                    <th align="center" style="width: 5px">No.</th>
                    <th align="left" style="width:80%"> <?=translate(" Nama Dokumen", "Documents Name")?></th>
                    <th style="width: 20%; text-align: center">Dokumen</th>
                  </tr>
                  <tr class="gelap" style="background-color: #b7b7b7; color: #000">
                      <!-- <td>I</td> -->
                      <td colspan="5">Dokumen Kualifikasi</td>
                  </tr>
                    <?php
                    $id = 1;
                    $i=1;
                    $jumlahDokumenKualifikasi = 0;
                    $jumlahUploadAdmin = 0;
                    $jumlahDokumenWajibBelumUpload = 0;
                    while($paket_evaluasi_kualifikasi->nextRow())
                    {
                    ?>
                    <tr class="terang">
                      <td style="width: 5px"><?=$i?>.</td>
                      <td> <?=$paket_evaluasi_kualifikasi->getField("NAMA")?> <?php if($paket_evaluasi_kualifikasi->getField("WAJIB") == '1'){ ?> <font color="#FF0000">* </font><?php } ?></td>
                      <td align="center" class="kolom-aksi" width="25%">
                      <?php
                      if($paket_evaluasi_kualifikasi->getField("PAKET_DOKUMEN_ID") == "")
                      {
                    if($paket_evaluasi_kualifikasi->getField("WAJIB") == '1')
                      $jumlahDokumenWajibBelumUpload ++;

                          if($aktif_dok_penawaran1 == 0)
                              echo '<span class="badge badge-danger">Waktu habis</span>';
                          else
                          {
                        if($paket_rekanan->getField("KIRIM_PENAWARAN") == "1")
                        {
                          echo "Penawaran sudah dikirim";
                        }
                        else
                        {
                          $timestamp = time();
                      ?>
                          <form action="dokumen_pengadaan_upload_rekanan/upload_validasi_noncrypt" method="post" enctype="multipart/form-data" id="upload_form<?=$id?>">
                            <input name="Filedata" type="file" onChange="$('#btnUpload<?=$id?>').click()" />
                            <input name="timestamp" type="hidden" value="<?=$timestamp?>" />
                            <input name="token" type="hidden" value="<?=md5('unique'.$timestamp)?>" />
                            <input name="reqPengirim" type="hidden" value="<?=coalesce($this->ID,0)?>" />
                            <input name="reqId" type="hidden" value="<?=$reqId?>" />
                            <input name="reqDokumenKe" type="hidden" value="<?=$id?>" />
                            <input name="reqNamaDokumen" type="hidden" value="<?=$paket_evaluasi_kualifikasi->getField("NAMA")?>" />
                            <input name="reqJenisDokumen" type="hidden" value="PENAWARAN_KUALIFIKASI" />
                            <input name="btnUpload" id="btnUpload<?=$id?>" type="submit" value="Upload" style="display:none"/>
                          </form>
                          <div id="progressBar<?=$id?>" style="display:none">
                            <div id="progress-wrp<?=$id?>" class="progress-wrp"><div id="progress-bar<?=$id?>" class="progress-bar"></div >
                            <div id="status<?=$id?>" class="status">0%</div></div>
                            <div id="output<?=$id?>"></div>
                          </div>
                          <script type="text/javascript">
                          //configuration
                          // var max_file_size       = 10485760; //allowed file size. (1 MB = 1048576)
                          var max_file_size       = 104857600; //allowed file size. (1 MB = 1048576)
                          var allowed_file_types    = ['application/pdf']; //allowed file types
                          var result_output       = '#output<?=$id?>'; //ID of an element for response output
                          var my_form_id        = '#upload_form<?=$id?>'; //ID of an element for response output
                          var progress_bar_id     = '#progress-wrp<?=$id?>'; //ID of an element for response output
                          //on form submit
                          $(my_form_id).on( "submit", function(event) {
                            event.preventDefault();
                            var proceed = true; //set proceed flag
                            var error = []; //errors
                            var total_files_size = 0;

                            //reset progressbar
                            $("#progress-bar<?=$id?>").css("width", "0%");
                            $("#status<?=$id?>").text("0%");

                            if(!window.File && window.FileReader && window.FileList && window.Blob){ //if browser doesn't supports File API
                              error.push("Your browser does not support new File API! Please upgrade."); //push error text
                            }else{
                              var total_selected_files = this.elements['Filedata'].files.length; //number of files

                              var submit_btn  = $(this).find("input[type=submit]"); //form submit button

                               //iterate files in file input field
                              $(this.elements['Filedata'].files).each(function(i, ifile){
                                if(ifile.value !== ""){ //continue only if file(s) are selected
                                  if(allowed_file_types.indexOf(ifile.type) === -1){ //check unsupported file
                                    proceed = false; //set proceed flag to false
                                  }
                                  total_files_size = total_files_size + ifile.size; //add file size to total size
                                }
                              });

                              //if total file size is greater than max file size
                              if(total_files_size > max_file_size){
                                $.messager.alert('Info', 'Pastikan file PDF anda tidak melebihi kapasitas yang telah ditentukan.', 'info');
                                return;
                              }

                              if(proceed == false)
                              {
                                $.messager.alert('Info', 'Pastikan file yang ada kirim adalah format PDF.', 'info');
                                return;
                              }

                              $("#progressBar<?=$id?>").show();

                              var form_data = new FormData(this); //Creates new FormData object
                              var post_url = $(this).attr("action"); //get action URL of form

                                //jQuery Ajax to Post form data
                              $.ajax({
                                url : post_url,
                                type: "POST",
                                data : form_data,
                                contentType: false,
                                cache: false,
                                processData:false,
                                xhr: function(){
                                  //upload Progress
                                  var xhr = $.ajaxSettings.xhr();
                                  if (xhr.upload) {
                                    xhr.upload.addEventListener('progress', function(event) {
                                      var percent = 0;
                                      var position = event.loaded || event.position;
                                      var total = event.total;
                                      if (event.lengthComputable) {
                                        percent = Math.ceil(position / total * 100);
                                      }
                                      //update progressbar
                                      $("#progress-bar<?=$id?>").css("width", + percent +"%");
                                      $("#status<?=$id?>").text(percent +"%");
                                    }, true);
                                  }
                                  return xhr;
                                },
                                mimeType:"multipart/form-data"
                              }).done(function(res){ //
                                // alert(res);
                                document.location.reload();
                              });
                            }
                          });
                          </script>
                      <?php
                        }
                      }
                      }
                      else
                      {
                      ?>
                          <a href="uploads/penawaran/<?=$paket_evaluasi_kualifikasi->getField("PATH_FILE")?>" target="_blank">
                            <?= ICON_DOWNLOAD ?>
                          </a>
                          <?php
                          if($kirimPenawaran == "1")
                          {}
                          else
                          {
                          ?>
                           
                          <a onClick="deleteData('dokumen_pengadaan_upload_rekanan/delete_dokumen/', '<?=$paket_evaluasi_kualifikasi->getField("PAKET_DOKUMEN_ID")?>')">
                            <?= ICON_DELETE ?>
                          </a>
                      <?php
                          }
                          $jumlahUploadAdmin++; ?>
                        <br>
                          <small style="font-size: 9px"><?=getFormattedDateView($paket_evaluasi_kualifikasi->getField("TANGGAL_UPLOAD"))?></small> - 
                          <small style="font-size: 9px"><?=round($paket_evaluasi_kualifikasi->getField("UKURAN") / 1024, 2)?> Kb </small>
                      <?php
                      }
                      ?>
                      </td>
                    </tr>
                    <?php
                      $i++;
                      $id++;
                      $jumlahDokumenKualifikasi++;
                    }
                    ?>

                </table>

              <?php
              //if($jumlahDokumenWajibBelumUpload == 0)
             // {
              ?>
                <?php
                if($kirimPenawaran == "1")
                {}
                else
                {
                ?>

                  <?php 
                  if ($jumlahDokumenWajibBelumUpload == 0) {
                    echo '<div class="alert alert-warning">
                            <b>Dokumen Wajib sudah berhasil di upload </b> </li>
                          </div>';
                  } else {
                    echo '<div class="alert alert-danger">
                            <b>'.$jumlahDokumenWajibBelumUpload.' Dokumen Wajib belum diupload, silahkan di lengkapi..!! </b> </li>
                          </div>';
                  } ?>
                  

                  <div class="alert alert-info">
                    <ul>
                      <li><font color="#FF0000">*</font> Rekanan wajib mengupload dokumen kualifikasi.</li>
                      <li>Untuk merubah dokumen kualifikasi, silahkan klik tombol <?= ICON_DELETE ?> kemudian upload ulang file anda.</li> 
                    </ul>
                  </div>
                <?php
                }
                // }
                ?>

                <script type="text/javascript">
                  function jscopy(elementID){
                    var jc = document.getElementById(elementID).textContent;
                    cp(jc);
                  }
                  function cp(jc) {
                     var el = document.createElement('textarea');
                     el.value = jc;
                     el.setAttribute('readonly', '');
                     el.style = {position: 'absolute', left: '-9999px'};
                     document.body.appendChild(el);
                     el.select();
                     document.execCommand('copy');
                     document.body.removeChild(el);
                     alertSuccess2('Copy berhasil: '+el.value);
                     // paste();
                    }
                </script>

                <?php
                if($paket_rekanan->getField("KIRIM_PENAWARAN") == "1") {
                  echo '<div class="alert alert-danger" style="color:#fff">
                          <span style="color: #fff">
                            Dokumen kualifikasi sudah dikirim
                          </span>
                        </div>';
                }
                 ?>

                <hr>
                <div class="form-actions">
                  <input type="hidden" name="reqDokumenKe" id="reqDokumenKe" value="" />
                  <input type="hidden" name="reqNamaDokumen" id="reqNamaDokumen" value="" />
                  <input type="hidden" name="reqJenisDokumen" id="reqJenisDokumen" value="" />
                  <input type="hidden" name="reqId" value="<?=$reqId?>" />
                  <input type="hidden" name="submitSimpan" value="Simpan" />
                  <input type="submit" name="reqSubmit" id="reqSubmit" value="" style="display:none">
                  <a href="main/index/paket_detil/?eid=<?=$reqId?>&key=<?=$reqUUID?>" class="<?= CLASS_BTN_DANGER ?>"> <i class="fa fa-arrow-left"></i> Kembali </a>
                </div>

          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Notif Gagal Upload -->
<style type="text/css"> .wafixed { position: fixed; left: 30px; bottom: 30px; z-index: 999; } .blink_me { animation: blinker 1s linear infinite; } @keyframes blinker { 50% { opacity: 0; }}</style>
<a href="#code" data-toggle="modal" onclick="openAdd22()" class="wafixed btn round btn-min-width box-shadow-1 btn-danger btn-sm" style="color:#fff"> 
  <i class="fa fa-exclamation-triangle fa-2x blink_me"></i> <br>
  <span style="padding:10px">Gagal Upload ? klik disini </span>
</a>

<div id="code" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
  <div class="modal-dialog modal-md" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">Gagal Upload Dokumen Penawaran</h4>
      </div>
      <div class="modal-body">
        <p>Jika file PDF tidak bisa diupload, ikuti langkah-langkah dibawah ini:</p>
        <ol style="margin-left: -25px !important;">
          <li>Buka link ini <a href="https://docupub.com/pdfconvert" target="_blank">https://docupub.com/pdfconvert</a></li>
          <li>Pilih menu <b>Document Converter</b>
            <img class="img-responsive" src="<?= base_url('images/tutor-upload-1.png') ?>" style="width: 100%;">
          </li>
          <li>Pada halaman di bagian kanan, setting komponen seperti berikut:
            <img class="img-responsive" src="<?= base_url('images/tutor-upload-2.png') ?>" style="width: 50%;">
            <div class="jumbotron" style="padding: 0.4rem 1rem !important">
              <ul style="margin-left: -25px !important;">
                <li>Output format: PDF</li>
                <li>Compatibility: Acrobat 4.0 (PDF 1.3) <i>atau</i> Acrobat 5.0 (PDF 1.4) <i>atau</i> Acrobat 6.0 (PDF 1.5) </li>
                <li>Auto-Rotate Pages: Page By Page</li>
                <li>Resolution: 300</li>
                <li>Pilih file PDF yang gagal diupload</li>
                <li>Delivery Method: Wait for conversion in browser</li>
              </ul>
            </div>
          </li>
          <li>Klik tombol <b>Upload & Convert</b></li>
          <li>Tunggu proses konversi kemudian download file</li>
          <li>Upload kembali file hasil konversi ke Dokumen Penawaran</li>
          <li>Pastikan file sesuai pada tempatnya</li>
        </ol> 
      </div>
      <div class="modal-footer"></div>
    </div>
  </div>
</div> 
<!-- End Notif Gagal Upload -->

<script type="text/javascript">
  function handleCopyTextFromArea() {
    const area = document.querySelector('#clipboard-area')
    area.select();
    document.execCommand('copy')
  }

  function prosesSelanjutnya()
  {
    var url = '<?= base_url(); ?>';
    <?php
    //if($jumlahDokumenKualifikasi == $jumlahUploadAdmin && $jumlahDokumenTeknis == $jumlahUploadTeknis && $jumlahDokumenHarga == $jumlahUploadHarga)
    if($jumlahDokumenWajibBelumUpload == 0)
    {
      if($reqKirimPenawaranKode == "")
      {
    ?>
      var win = $.messager.progress({
                    title:'Kirim Email',
                    msg:'Mengirim email file .cert dokumen kualifikasi...'
                  });
      var jqxhr = $.get( "dokumen_pengadaan_upload_rekanan/email/?reqId=<?=$reqId?>", function() {
        $.messager.progress('close');
          document.location.href = 'main/index/dokumen_penawaran_term_condition/?reqId=<?=$reqId?>';
      })
      .fail(function() {
        $.messager.progress('close');
        $.messager.alert('Info', 'Kirim email file .cert gagal.', 'info');
      });
    <?php
      }
      else
      {
        if($aktif_dok_penawaran1 == 0)
          echo "$.messager.alert('Info', 'Waktu pemasukan penawaran telah berakhir, penawaran tidak lengkap.', 'info');";
        ?>
        document.location.href = url+'main/index/dokumen_penawaran_term_condition/?reqId=<?=$reqId?>';
        <?php
      }
    }
    else
    {
      if($aktif_dok_penawaran1 == 0)
        echo "$.messager.alert('Info', 'Waktu pemasukan penawaran telah berakhir, penawaran tidak lengkap.', 'info');";
      else
        echo "$.messager.alert('Info', 'Lengkapi terlebih dahulu dokumen kualifikasi.', 'info');";
    }
    ?>
  }
</script>
