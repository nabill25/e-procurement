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
$this->load->model("PaketEvaluasiAdminTawar");
$this->load->model("PaketEvaluasiTeknisTawar");
$this->load->model("PaketEvaluasiHargaTawar");
$this->load->model("MatrixEvaluasi");

$paket = new Paket();
$paket_evaluasi_admin = new PaketEvaluasiAdminTawar();
$paket_evaluasi_admin_count = new PaketEvaluasiAdminTawar();
$paket_evaluasi_teknis = new PaketEvaluasiTeknisTawar();
$paket_evaluasi_teknis_count = new PaketEvaluasiTeknisTawar();
$paket_evaluasi_harga = new PaketEvaluasiHargaTawar();
$paket_evaluasi_harga_count = new PaketEvaluasiHargaTawar();
$matrix_evaluasi = new MatrixEvaluasi();


$submitSimpan = httpFilterPost("submitSimpan");
$reqEvaluasiAdministrasi = isset($_POST["reqEvaluasiAdministrasi"]) ? $_POST["reqEvaluasiAdministrasi"] : '';
$reqEvaluasiTeknis = isset($_POST["reqEvaluasiTeknis"]) ? $_POST["reqEvaluasiTeknis"] : '';
$reqEvaluasiHarga = isset($_POST["reqEvaluasiHarga"]) ? $_POST["reqEvaluasiHarga"] : '';
$reqEvaluasiNumber =  isset($_POST["reqEvaluasiNumber"]) ? $_POST["reqEvaluasiNumber"] : '';
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
$paket_evaluasi_admin->selectByParams(array("PAKET_ID" => $reqId));
$paket_evaluasi_admin_count->selectByParams(array("PAKET_ID" => $reqId));
//echo $paket_evaluasi_admin->query;exit;
$paket_evaluasi_teknis->selectByParams(array("PAKET_ID" => $reqId));
$paket_evaluasi_teknis_count->selectByParams(array("PAKET_ID" => $reqId));
$paket_evaluasi_harga->selectByParams(array("PAKET_ID" => $reqId));
$paket_evaluasi_harga_count->getCountByParams(array("PAKET_ID" => $reqId));

$matrix_evaluasi->selectByParams(array("A.PAKET_JENIS_ID" => $reqJenisPekerjaanId, "A.PAKET_METODE_EVALUASI_ID" => $reqMetodeEvaluasiId));
$matrix_evaluasi->firstRow();

?>
<script language="javascript">
$(document).ready(function() {

  $(function(){
    $('#ff').form({
      url:'rekanan_evaluasi_admin_tawar_json/kriteria_penawaran',
      onSubmit:function(){
        // return $(this).form('validate');
        var v=$(this).form('validate');
        if(v) showLoad();  // show the message box
        return v;
      },
      success:function(data){
        //alert(data);return false;
        hideLoad();
        // alert("Data berhasil disimpan");
        alertSuccess2('Data berhasil disimpan'); 
        setTimeout(function() {
            document.location.href = 'main/index/paket_lelang_tambah_kriteria_penawaran/?reqId=<?=$reqId?>'; 
        }, 2000);
        
      }
    });

  });

   $('body').bind('cut copy paste', function (e) {
      e.preventDefault();
      alertError3('Lakukan pengisian dengan cara di ketik...!');
   });

});

function addRow(tableID)
{
  var table = document.getElementById(tableID);

  var rowCount = table.rows.length;
  var row = table.insertRow(rowCount);

  var cell2 = row.insertCell(0);
  cell2.innerHTML = rowCount + '<input type="hidden" name="reqCheck['+ (rowCount) +']" id="reqCheck'+ (rowCount) +'" value="1">';

  var cell3 = row.insertCell(1);
  var element2 = document.createElement("input");
  element2.type = "text";
  element2.name = "reqEvaluasiAdministrasi["+ (rowCount) +"]";
  element2.size = 120;
  element2.setAttribute("class", "form-control span10");
  cell3.appendChild(element2);

  var cell2 = row.insertCell(2);
  cell2.style.textAlign = "center";
  cell2.innerHTML = '<input type="checkbox" style="cursor:pointer" name="reqWajib['+ (rowCount) +']" id="reqWajib" value="1">';


  var cell4 = row.insertCell(3);
  cell4.innerHTML = '<a title="#" onclick="addRow(\'dataTableAdmin\')" class="btn-aksi"><i class="fa fa-plus" aria-hidden="true"></i></a>';

  var rowLast = table.rows[rowCount - 1];
  var cell5 = rowLast.deleteCell(3);
  var cell6 = rowLast.insertCell(3);
  cell6.innerHTML = '<a title="#" onclick="$(this).parent().parent().remove();" class="btn-aksi"><i class="fa fa-trash" aria-hidden="true"></i></a>';
}

function addRowTeknis(tableID)
{
  var table = document.getElementById(tableID);

  var rowCount = table.rows.length;
  var row = table.insertRow(rowCount);


  var cell2 = row.insertCell(0);
  cell2.innerHTML = rowCount;

  var cell3 = row.insertCell(1);
  var element2 = document.createElement("input");
  element2.type = "text";
  element2.name = "reqEvaluasiTeknis["+ (rowCount) +"]";
  element2.size = 120;
  element2.setAttribute("class", "form-control span10");
  cell3.appendChild(element2);

  var cell2 = row.insertCell(2);
  cell2.style.textAlign = "center";
  cell2.innerHTML = '<input type="checkbox" style="cursor:pointer" name="reqWajibTeknis['+ (rowCount) +']" id="reqWajibTeknis" value="1">';

  var cell4 = row.insertCell(3);
  cell4.innerHTML = '<a title="#" onclick="addRowTeknis(\'dataTableTeknis\')" class="btn-aksi"><i class="fa fa-plus" aria-hidden="true"></i></a>';

  var rowLast = table.rows[rowCount - 1];
  var cell5 = rowLast.deleteCell(3);
  var cell6 = rowLast.insertCell(3);
  cell6.innerHTML = '<a title="#" onclick="$(this).parent().parent().remove();" class="btn-aksi"><i class="fa fa-trash" aria-hidden="true"></i></a>';
}

function addRowHarga(tableID)
{
  var table = document.getElementById(tableID);

  var rowCount = table.rows.length;
  var row = table.insertRow(rowCount);


  var cell2 = row.insertCell(0);
  cell2.innerHTML = rowCount;

  var cell3 = row.insertCell(1);
  var element2 = document.createElement("input");
  element2.type = "text";
  element2.name = "reqEvaluasiHarga["+ (rowCount) +"]";
  element2.size = 120;
  element2.setAttribute("class", "form-control span10");
  cell3.appendChild(element2);

  var cell2 = row.insertCell(2);
  cell2.style.textAlign = "center";
  cell2.innerHTML = '<input type="checkbox" style="cursor:pointer" name="reqWajibHarga['+ (rowCount) +']" id="reqWajibHarga" value="1">';

  var cell4 = row.insertCell(3);
  cell4.innerHTML = '<a title="#" onclick="addRowHarga(\'dataTableHarga\')" class="btn-aksi"><i class="fa fa-plus" aria-hidden="true"></i></a>';

  var rowLast = table.rows[rowCount - 1];
  var cell5 = rowLast.deleteCell(3);
  var cell6 = rowLast.insertCell(3);
  cell6.innerHTML = '<a title="#" onclick="$(this).parent().parent().remove();" class="btn-aksi"><i class="fa fa-trash" aria-hidden="true"></i></a>';
}
</script>

<div class="row">
  <div class="col-md-12 col-sm-12">
    <div class="card">
      <div class="card-header card-head-inverse bg-primary">
        <h4 class="card-title text-white">Syarat Dokumen Penawaran <?= $paketInfo->metode_lelang_nama ?></h4>
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
                 <?php 
                  if($reqSistemSampul == "2")
                  {
                  ?>
                  <div class="alert alert-info">FILE I</div>
                  <?php 
                  }
                  ?>
                  <div class="alert alert-danger">DOKUMEN ADMINISTRASI</div>
                  <div class="table-responsive">
                    <table class="table table-bordered table-hover" id="dataTableAdmin">
                      <tbody>
                        <tr>
                          <th align="center" width="1%">No.</th>
                          <th align="center">Dokumen yang di persyaratkan</th>
                          <th align="center" style="text-align: center;" width="1%">Wajib</th>
                          <th align="center" width="1%">Aksi</th>
                        </tr>
                        <?php 
                        $i = 1;
                        $style="gelap";
                        while($paket_evaluasi_admin->nextRow())
                        {
                        ?>
                          <tr class="<?=$style?>">
                            <td><?=$i?></td>
                            <td>
                              <input name="reqEvaluasiAdministrasi[<?=$i?>]" type="text" id="reqEvaluasiAdministrasi" value="<?=$paket_evaluasi_admin->getField("NAMA")?>" class="form-control span10" <?= $tutupForm ?> />
                              <input type="hidden" name="reqCheck[<?=$i?>]" id="reqCheck<?=$i?>" value="1">
                            </td>
                            <td style="width: 10px; text-align: center;">
                              <input name="reqWajib[<?=$i?>]" type="checkbox" style="cursor:pointer" id="reqWajib" value="1" <?php if($paket_evaluasi_admin->getField("WAJIB") == '1') { ?> checked <?php } ?> />
                            </td>
                            <td>
                              <?php 
                              if ($tutupHapus == '0') {  ?>
                              <a title="#" onclick="$(this).parent().parent().remove();" class="btn-aksi"><i class="fa fa-trash" aria-hidden="true"></i></a>
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
                          if ($paket_evaluasi_admin_count == 1) { ?>
                          <tr class="<?=$style?>">
                            <td><?=$i?></td>
                            <td>
                              <input name="reqEvaluasiAdministrasi[<?=$i?>]" type="text" id="reqEvaluasiAdministrasi" value="" class="form-control span10" />
                              <input type="hidden" name="reqCheck[<?=$i?>]" id="reqCheck<?=$i?>" value="1">
                            </td>
                            <td style="width: 10px; text-align: center;">
                              <input name="reqWajib[<?=$i?>]" type="checkbox" style="cursor:pointer" id="reqWajib" value="1" />
                            </td>
                            <!-- <td></td> -->
                            <td>
                              <a title="#" onclick="addRow('dataTableAdmin')" class="btn-aksi"><i class="fa fa-plus" aria-hidden="true"></i></a>
                            </td>
                          </tr>
                          <?php
                          } ?>
                      </tbody>
                    </table>
                  </div>

                  <div class="alert alert-danger">DOKUMEN TEKNIS</div>
                  <div class="table-responsive">
                    <table class="table table-bordered table-hover" id="dataTableTeknis">
                        <tbody>
                          <tr class="judul-kolom">
                             <th align="center" width="1%">No.</th>
                             <th align="center">Dokumen yang di persyaratkan</th>
                             <th align="center" width="1%">Wajib</th>
                             <th align="center" width="1%">Aksi</th>
                          </tr>
                          <?php
                          $i = 1;
                          $style="gelap";
                          while($paket_evaluasi_teknis->nextRow())
                          {
                        ?>
                        <tr class="<?=$style?>">
                            <td><?=$i?></td>
                            <td>
                            <input name="reqEvaluasiTeknis[<?=$i?>]" type="text" id="reqEvaluasiTeknis" value="<?=$paket_evaluasi_teknis->getField("NAMA")?>" class="form-control span10" <?= $tutupForm ?>/>
                            </td>
                            <td style="width: 10px; text-align: center;">
                            <input name="reqWajibTeknis[<?=$i?>]" type="checkbox" style="cursor:pointer" id="reqWajibTeknis" value="1" <?php if($paket_evaluasi_teknis->getField("WAJIB") == '1') { ?> checked <?php } ?>/>
                            </td>
                            <td>
                              <?php 
                              if ($tutupHapus == '0') {  ?>
                                <a title="#" onclick="$(this).parent().parent().remove();" class="btn-aksi"><i class="fa fa-trash" aria-hidden="true"></i></a></a>
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
                          if ($paket_evaluasi_teknis_count == 1) { ?>
                          <tr class="<?=$style?>">
                            <td><?=$i?></td>
                            <td>
                              <input name="reqEvaluasiTeknis[<?=$i?>]" type="text" id="reqEvaluasiTeknis" value="" class="form-control span10" />
                            </td>
                            <td style="width: 10px; text-align: center;">
                              <input name="reqWajibTeknis[<?=$i?>]" type="checkbox" style="cursor:pointer" id="reqWajibTeknis" value="1" />
                            </td>
                            <!-- <td></td> -->
                            <td><a title="#" onclick="addRowTeknis('dataTableTeknis')" class="btn-aksi"><i class="fa fa-plus" aria-hidden="true"></i></a></td>
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

            <div class="card mb-1 border-blue border-darken-1">
              <div class="card-content">
                <div class="p-1">
                <?php
                if($reqMetodeLelangId == "6")
                {}
                else // Bukan Pembelian Langsung
                {
                if($reqSistemSampul == "2")
                {
                ?>
                  <div class="alert alert-info">FILE II</div>
                  <?php
                  }
                  ?>
                  <div class="alert alert-danger">DOKUMEN HARGA</div>
                  <div class="table-responsive">
                    <table class="table table-bordered table-hover" id="dataTableHarga">
                        <tbody>
                          <tr class="judul-kolom">
                             <th align="center" width="1%">No.</th>
                             <th align="center">Dokumen yang di persyaratkan</th>
                              <th align="center" style="text-align: center;" width="1%">Wajib</th>
                              <th align="center" width="1%">Aksi</th>
                          </tr>
                          <?php
                            $i = 1;
                            $style="gelap";
                            while($paket_evaluasi_harga->nextRow())
                            {
                            ?>
                            <tr class="<?=$style?>">
                            <td><?=$i?></td>
                            <td>
                              <input name="reqEvaluasiHarga[<?=$i?>]" type="text" id="reqEvaluasiHarga" value="<?=$paket_evaluasi_harga->getField("NAMA")?>" class="form-control span10" <?= $tutupForm ?>/>
                            </td>
                            <td style="width: 10px; text-align: center;">
                              <input name="reqWajibHarga[<?=$i?>]" type="checkbox" style="cursor:pointer" id="reqWajibHarga" value="1" <?php if($paket_evaluasi_harga->getField("WAJIB") == '1') { ?> checked <?php } ?>/>
                            </td>
                            <td>
                              <?php 
                              if ($tutupHapus == '0') {  ?>
                              <a title="#" onclick="$(this).parent().parent().remove();" class="btn-aksi"><i class="fa fa-trash" aria-hidden="true"></i></a></a>
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
                          if ($paket_evaluasi_harga_count == 1) { ?>
                         <tr class="<?=$style?>">
                            <td><?=$i?></td>
                            <td>
                            <input name="reqEvaluasiHarga[<?=$i?>]" type="text" id="reqEvaluasiHarga" value="" class="form-control span10" />
                            </td>
                            <td style="width: 10px; text-align: center;">
                              <input name="reqWajibHarga[<?=$i?>]" type="checkbox" style="cursor:pointer" id="reqWajibHarga" value="1" />
                            </td>
                            <!-- <td></td> -->
                            <td><a title="#" onclick="addRowHarga('dataTableHarga')" class="btn-aksi"><i class="fa fa-plus" aria-hidden="true"></i></a></td>
                          </tr>
                          <?php
                          } ?>
                        </tbody>
                    </table>
                  </div>
                <?php
                }
                ?>
                <!-- <div class="alert alert-info">EVALUASI HARGA</div>
                <div class="table-responsive">
                  <table class="table table-bordered table-hover" id="tbl_bidang">
                      <tbody>
                        <tr class="judul-kolom">
                          <th>Uraian</th>
                        </tr>
                        <tr >
                          <td><?=$matrix_evaluasi->getField("KETERANGAN_HARGA")?></td>
                        </tr>
                      </tbody>
                  </table>
                </div>

                <div class="alert alert-info">REKAPITULASI</div>
                <div class="table-responsive">
                  <table class="table table-bordered table-hover" id="tbl_bidang">
                      <tbody>
                        <tr class="judul-kolom">
                          <th>Uraian</th>
                        </tr>
                          <tr >
                            <td><?=$matrix_evaluasi->getField("KETERANGAN_REKAP")?></td>
                          </tr>
                      </tbody>
                  </table>
                </div> -->

              </div>
            </div>
          </div>

          <div class="form-actions">
            <input type="hidden" name="reqId" value="<?=$reqId?>" />
            <input type="hidden" name="reqNama" value="<?=$reqNama?>" />
            <input type="hidden" name="reqJenisPekerjaanId" value="<?=$reqJenisPekerjaanId?>" />
            <input type="hidden" name="reqMetodeEvaluasiId" value="<?=$reqMetodeEvaluasiId?>" />
            <input type="hidden" name="reqJenisPekerjaan" value="<?=$reqJenisPekerjaan?>" />
            <input type="hidden" name="reqMetodeEvaluasi" value="<?=$reqMetodeEvaluasi?>" />
            <input type="hidden" name="submitSimpan" value="Simpan" />
            <a href="main/index/paket_detil/?reqId=<?=$reqId?>" class="<?= CLASS_BTN_DANGER ?> mr-1"> <?= BTN_KEMBALI ?> </a>
            <button type="submit" class="<?= CLASS_BTN_PRIMARY ?>"><?= BTN_SIMPAN ?></button>
          </div>

        </div>
      </div>
      </form>

    </div>
  </div>
</div>
