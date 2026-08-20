<?php
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 * 
 */
$this->libsession->cekSession();
$this->load->library("paketinfo"); $paketInfo = new paketinfo();
$this->load->model("Masterdokumentemplate");
$master_dokumen = new Masterdokumentemplate();
$FILE_DIR = "uploads/template/";
$master_dokumen->selectByParams();
?>

<script src="lib/ajax-upload/jquery1.12.4.min.js" type="text/javascript"></script>
<link href="lib/ajax-upload/style.css" rel="stylesheet" type="text/css">
<style type="text/css">
  th {
    /*text-align: center;*/
    background-color: #ffffe0
  }
  .ft-download, .ft-trash {font-size:1.1rem; margin: 0 5px;}
</style>

<div class="row">
  <div class="col-md-12 col-sm-12">
    <div class="card">
      <div class="card-header card-head-inverse bg-primary">
        <h4 class="card-title text-white">Upload Template</h4>
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
            <div class="alert alert-info">Format template dengan ekstensi .pdf .doc</div>
              <table class="table table-bordered">
                <?php
                $id = 1;
                $i=1;
                while($master_dokumen->nextRow())
                {
                ?>
                <tr class="terang">
                  <td style="width: 5px"><?=$i?>.</td>
                  <td> <?=$master_dokumen->getField("NAMA")?></td>
                  <td align="center" class="kolom-aksi" width="10%">
                    <?php
                    if ($master_dokumen->getField("PATH_FILE") == '')
                    {
                     ?>
                      <form action="master_dokumen_template_upload/upload_validasi" method="post" enctype="multipart/form-data" id="upload_form<?=$id?>">
                        <input name="Filedata" type="file" onChange="$('#btnUpload<?=$id?>').click()" />
                        <input name="reqDokumenKe" type="hidden" value="<?=$id?>" />
                        <input name="reqNamaDokumen" type="hidden" value="<?=$master_dokumen->getField("NAMA")?>" />
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
                      var allowed_file_types    = ['application/pdf','application/msword','jpg','jpeg','png']; //allowed file types
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
                              if($.inArray(ifile.type, ['application/pdf','application/msword','image/gif','image/png','image/jpg','image/jpeg']) == -1) {
                                alert(ifile.type);
                              // if(allowed_file_types.indexOf(ifile.type) === -1){ //check unsupported file
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
                            $.messager.alert('Info', 'Pastikan file yang ada kirim sudah sesuai dengan ketentuan.', 'info');
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
                      } else {
                       ?>
                      <a href="uploads/template/<?=$master_dokumen->getField("PATH_FILE")?>" target="_blank">
                        <span class="ft-download"></span>
                      </a>
                      <a onClick="deleteData('master_dokumen_template_upload/delete_dokumen/', '<?=$master_dokumen->getField("ID_UPLOAD")?>')"><span class="ft-trash"></span>
                      </a>
                      <br>
                      <small class="badge badge-info" style="font-size: 9px"><?= $master_dokumen->getField("TIPE");?></small>
                      <small class="badge badge-info" style="font-size: 9px"><?=round($master_dokumen->getField("UKURAN") / 1024, 2)?> Kb </small>
                      <small class="badge badge-info" style="font-size: 9px"><?=($master_dokumen->getField("TANGGAL_UPLOAD"))?></small>
                      <?php
                      }
                       ?>
                  </td>
                </tr>
                <?php
                  $i++;
                  $id++;
                  $jumlahDokumenAdmin++;
                }
                ?>
            </table>

          </div>
        </div>
      </div>
    </div>
  </div>
</div>
