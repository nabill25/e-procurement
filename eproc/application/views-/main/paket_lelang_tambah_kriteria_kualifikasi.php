<?php
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 * 
 */
/* VARIABLES */
$reqMode = httpFilterRequest("reqMode");
$reqId = httpFilterRequest("reqId");

$this->libsession->cekSession($reqId);   

$this->load->library("kauth");  $userLogin = new kauth();
$this->load->library("paketinfo"); $paketInfo = new paketinfo();
include_once("functions/string.func.php");
include_once("functions/date.func.php");
include_once("functions/default.func.php");
$this->load->model("Paket");
$this->load->model("PaketEvaluasiKualifikasi");
$this->load->model("PaketEvaluasiTeknisTawar");
$this->load->model("PaketEvaluasiHargaTawar");
$this->load->model("MatrixEvaluasi");

$paket = new Paket();
$paket_evaluasi_kualifikasi = new PaketEvaluasiKualifikasi();
$paket_evaluasi_kualifikasi_count = new PaketEvaluasiKualifikasi();

$submitSimpan = httpFilterPost("submitSimpan");
$reqCheck =  isset($_POST["reqCheck"]) ? $_POST["reqCheck"] : '';

$paketInfo->getPaket($reqId);
$reqNama = $paketInfo->nama;
$reqKualifikasi = $paketInfo->kualifikasi;
$reqKualifikasiId = $paketInfo->kualifikasi_id;
$reqMetodeLelangId = $paketInfo->metode_lelang_id;
$reqNilai = $paketInfo->nilai;

$reqNama =$paketInfo->nama;
$reqJenisPekerjaanId = $paketInfo->jenis_id;
$reqMetodeEvaluasiId = $paketInfo->metode_evaluasi_id;
$reqJenisPekerjaan  = $paketInfo->jenis;
$reqMetodeEvaluasi  = $paketInfo->metode_evaluasi;
$reqSistemSampul  = $paketInfo->sistem_sampul; 
$reqUUID  = $paketInfo->uuid; 
$paket->selectByParamsMonitoring(array(), -1, -1, " AND PAKET_ID = '".$reqId."' ");
$paket->firstRow();

if ($paket->getField('publish_paket') == '1') { // close input form when paket is publish
  $tutupForm = 'readonly';
  $tutupHapus = '1';
} else {
  $tutupForm = '';
  $tutupHapus = '0';
}

//set up 16-10-2012
$paket_evaluasi_kualifikasi->selectByParams(array("PAKET_ID" => $reqId));
$paket_evaluasi_kualifikasi_count->selectByParams(array("PAKET_ID" => $reqId));

?>
<script language="javascript">
$(document).ready(function() {

  $(function(){
    $('#ff').form({
      url:'rekanan_evaluasi_kualifikasi_json/kriteria_penawaran',
      onSubmit:function(){
        // return $(this).form('validate');
        var v=$(this).form('validate');
        if(v) showLoad();  // show the message box
        return v;
      },
      success:function(data){
        hideLoad();
        alertSuccess2(data); 
        setTimeout(function() {
            document.location.href = 'main/index/paket_lelang_tambah_kriteria_kualifikasi/?reqId=<?=$reqId?>'; 
        }, 2000);
        
      }
    });

  });

});

function cekLenght(idnya) { 
  // alert('aaa');
  if($("#"+idnya).val().length >= 255) {
    $("#"+idnya).val('');
    $("#"+idnya+"text").html('<span style="color:red; font-size:10px;"><i>Tidak boleh melebihi 255 Karakter, silahkan di sesuaikan !</i></span>');
  } else {
    if($("#"+idnya).val().length >= 2) {
      $("#"+idnya+"text").html('');
    }
  }
}

function addRow(tableID)
{
  var table = document.getElementById(tableID);

  var rowCount = table.rows.length;
  var row = table.insertRow(rowCount);

  var cell2 = row.insertCell(0);
  cell2.innerHTML = rowCount + '<input type="hidden" name="reqCheck['+ (rowCount) +']" id="reqCheck'+ (rowCount) +'" value="1">';

  var cell3 = row.insertCell(1);
  cell3.innerHTML = '<input name="reqEvaluasiKualifikasi['+ (rowCount) +']" type="text" id="reqEvaluasiKualifikasi'+ (rowCount) +'"  OnKeyUp="cekLenght(\'reqEvaluasiKualifikasi'+ (rowCount) +'\')" class="form-control span10"/><div id="reqEvaluasiKualifikasi'+ (rowCount) +'text"></div><div id="startButton'+ (rowCount) +'" onClick="return speachToText(\''+ (rowCount) +'\')"> <span style="cursor: pointer;"></span></div>';

  var cell2 = row.insertCell(2);
  cell2.style.textAlign = "center";
  cell2.innerHTML = '<input type="checkbox" style="cursor:pointer" name="reqWajib['+ (rowCount) +']" id="reqWajib" value="1">';


  var cell4 = row.insertCell(3);
  cell4.innerHTML = '<a title="#" onclick="addRow(\'dataTableKualifikasi\')" class="btn-aksi"><i class="fa fa-plus-square" aria-hidden="true"></i></a>';

  var rowLast = table.rows[rowCount - 1];
  var cell5 = rowLast.deleteCell(3);
  var cell6 = rowLast.insertCell(3);
  cell6.innerHTML = '<a title="#" onclick="$(this).parent().parent().remove();" class="btn-aksi"><i class="fa fa-trash-o" aria-hidden="true"></i></a>';
}

// $('body').bind('cut copy paste', function (e) {
//   e.preventDefault();
//   alertError3('Lakukan pengisian dengan cara di ketik');
// });
 
</script>

<div class="row">
  <div class="col-md-12 col-sm-12">
    <div class="card">
      <div class="card-header card-head-inverse bg-primary">
        <h4 class="card-title text-white">Syarat Dokumen Kualifikasi</h4>
        <a class="heading-elements-toggle"><i class="fa fa-ellipsis-v font-medium-3"></i></a>
        <div class="heading-elements">
            <ul class="list-inline mb-0">
              <li><a data-action="expand"><i class="ft-maximize"></i></a></li>
              <!-- <li><a data-action="close"><i class="ft-x"></i></a></li> -->
            </ul>
        </div>
      </div>

      <form id="ff" class="easyui-form form-horizontal" method="post" data-options="novalidate:true" enctype="multipart/form-data">
      <div class="card-content collapse show border-info border-darken-2">
        <div class="card-body area-datatable">

            <div class="table-responsive">
              <table class="table table-bordered mb-0">
                <tr>
                  <td width="20%">Jenis Pengadaan</td>
                  <td><?=$paket->getField('paket_jenis')?></td>
                </tr>
                <tr>
                  <td>Metode Evaluasi</td>
                  <td><?=$paket->getField('metode_evaluasi')?> </td>
                </tr>
              </table>
            </div>

            <?php 
            if ($reqMetodeLelangId != 7 ) { // selain tender cepat 
            ?>
            <div class="card mb-1 border-blue border-darken-1" style="margin-top: 1%">
              <div class="card-content">
                <div class="p-1"> 
                  <div class="alert alert-danger">DOKUMEN KUALIFIKASI</div>
                  <div class="table-responsive">
                    <table class="table table-bordered table-hover" id="dataTableKualifikasi">
                      <tbody>
                        <tr>
                          <th align="center" width="1%">No.</th>
                          <th align="center">Dokumen yang di persyaratkan</th>
                          <th align="center" style="text-align: center;" width="1%">Wajib</th>
                          <th align="center" width="1%">#</th>
                        </tr>
                        <?php 
                        $i = 1;
                        $style="gelap";
                        while($paket_evaluasi_kualifikasi->nextRow())
                        {
                        ?>
                          <tr class="<?=$style?>">
                            <td><?=$i?></td>
                            <td>
                              <input name="reqEvaluasiKualifikasi[<?=$i?>]" OnKeyUp="cekLenght('reqEvaluasiKualifikasi<?=$i?>')" type="text" id="reqEvaluasiKualifikasi<?=$i?>" value="<?=$paket_evaluasi_kualifikasi->getField("NAMA")?>" class="form-control span10" <?= $tutupForm ?> />
                              <div id="reqEvaluasiKualifikasi<?=$i?>text"></div>
                              <input type="hidden" name="reqCheck[<?=$i?>]" id="reqCheck<?=$i?>" value="1">
                            </td>
                            <td style="width: 10px; text-align: center;">
                              <input name="reqWajib[<?=$i?>]" type="checkbox" style="cursor:pointer" id="reqWajib" value="1" <?php if($paket_evaluasi_kualifikasi->getField("WAJIB") == '1') { ?> checked <?php } ?> />
                            </td>
                            <td>
                              <?php 
                              if ($tutupHapus == '0') {  ?>
                              <a title="#" onclick="$(this).parent().parent().remove();" class="btn-aksi"><i class="fa fa-trash-o" aria-hidden="true"></i></a>
                              <?php 
                              } ?>
                            </td>
                          </tr>
                          <?php
                            $i++;
                            if($style == "gelap")
                                $style = "terang";
                            else
                                $style = "gelap";
                          }
                          ?>

                          <?php
                          if ($paket_evaluasi_kualifikasi_count == 1) { ?>
                          <tr class="<?=$style?>">
                            <td><?=$i?></td>
                            <td>
                              <input name="reqEvaluasiKualifikasi[<?=$i?>]" OnKeyUp="cekLenght('reqEvaluasiKualifikasi<?=$i?>')" type="text" id="reqEvaluasiKualifikasi<?=$i?>" value="" class="form-control span10" />
                              <div id="reqEvaluasiKualifikasi<?=$i?>text"></div>
                              <input type="hidden" name="reqCheck[<?=$i?>]" id="reqCheck<?=$i?>" value="1">
                            </td>
                            <td style="width: 10px; text-align: center;">
                              <input name="reqWajib[<?=$i?>]" type="checkbox" style="cursor:pointer" id="reqWajib" value="1" />
                            </td>
                            <!-- <td></td> -->
                            <td>
                              <a title="#" onclick="addRow('dataTableKualifikasi')" class="btn-aksi"><i class="fa fa-plus-square" aria-hidden="true"></i></a>
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
            <?php 
            } ?>
  

          <div class="form-actions">
            <input type="hidden" name="reqId" value="<?=$reqId?>" />
            <input type="hidden" name="reqNama" value="<?=$reqNama?>" />
            <input type="hidden" name="reqJenisPekerjaanId" value="<?=$reqJenisPekerjaanId?>" />
            <input type="hidden" name="reqMetodeEvaluasiId" value="<?=$reqMetodeEvaluasiId?>" />
            <input type="hidden" name="reqJenisPekerjaan" value="<?=$reqJenisPekerjaan?>" />
            <input type="hidden" name="reqMetodeEvaluasi" value="<?=$reqMetodeEvaluasi?>" />
            <input type="hidden" name="submitSimpan" value="Simpan" />
            <a href="main/index/paket_detil/?eid=<?=$reqId?>&key=<?=$reqUUID?>" class="<?= CLASS_BTN_DANGER ?> mr-1"> <?= BTN_KEMBALI ?> </a>
            <button type="submit" class="<?= CLASS_BTN_PRIMARY ?>"><?= BTN_SIMPAN ?></button>
          </div>

        </div>
      </div>
      </form>

    </div>
  </div>
</div>
