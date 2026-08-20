<?php
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 *
 */

$this->libsession->cekSession('blockpenyedia');

// cek allowed url
if ($this->libsession->cekUrl($this->uri->segment(3, ""))) {} else { redirect(base_url()); }

/* INCLUDE FILE */
$this->load->library("kauth");  $userLogin = new kauth();
$this->load->model("IjinUsaha");
$this->load->model("Rekanan");
$this->load->model("RekananIjinUsaha");
$this->load->model("RekananBidangUsaha");
include_once("functions/string.func.php");
include_once("functions/date.func.php");
include_once("functions/default.func.php");

/* VARIABLE */
$submitSimpan = httpFilterPost("submitSimpan");
$reqBatal = httpFilterPost("reqBatal");
$reqNomorIjin = httpFilterPost('reqNomorIjin');
$reqTanggalIjin = httpFilterPost('reqTanggalIjin');
$reqTanggalBerakhir = httpFilterPost('reqTanggalBerakhir');
$reqInstansiPemberiIjin = httpFilterPost('reqInstansiPemberiIjin');
$reqId = httpFilterRequest('reqId');
$reqBidangUsahaId = $_POST["reqBidangUsahaId"];
$reqLinkFile= $_FILES['reqLinkFile'];
$reqLinkFileTemp = httpFilterPost("reqLinkFileTemp");
$reqLinkFileTempTipe = httpFilterPost("reqLinkFileTempTipe");
$reqLinkFileTempUkuran = httpFilterPost("reqLinkFileTempUkuran");

$reqIjinUsaha = httpFilterRequest('reqIjinUsaha') ?? '0';

// if ($reqIjinUsaha == 1) { // ijin usaha SIUP tidak bisa diubah
//   redirect(base_url('main/index/data_administrasi_ijin_usaha'));
// }

/* create objects */
$ijin_usaha = new IjinUsaha();
$rekanan_ijin_usaha = new RekananIjinUsaha();
$rekanan_bidang_usaha = new RekananBidangUsaha();

$reqId = $this->ID;
$rekanan_ijin_usaha->selectByParams(array("REKANAN_ID" => $this->ID, "IJIN_USAHA_ID"=>$reqIjinUsaha));
$rekanan_ijin_usaha->firstRow();

$reqIjinUsahaId = $rekanan_ijin_usaha->getField("REKANAN_IJIN_USAHA_ID");
$reqNomor = $rekanan_ijin_usaha->getField("NO_IJIN");
$reqTanggalIjin = $rekanan_ijin_usaha->getField("TANGGAL");
$reqTanggalBerakhir = $rekanan_ijin_usaha->getField("TANGGAL_BERAKHIR");
$reqInstansi = $rekanan_ijin_usaha->getField("INSTANSI");
$reqBidang = $rekanan_ijin_usaha->getField("IJIN_USAHA") ?: 'NIB';
$reqLinkFileTemp= $rekanan_ijin_usaha->getField("PATH_FILE");
$reqLinkFileTempTipe= $rekanan_ijin_usaha->getField("TIPE");
$reqLinkFileTempUkuran= $rekanan_ijin_usaha->getField("UKURAN");
$reqLinkFileTempNama= $rekanan_ijin_usaha->getField("NAMA_FILE");
$reqLinkFile2Temp= $rekanan_ijin_usaha->getField("PATH_FILE2");
$reqPKKPR= $rekanan_ijin_usaha->getField("PKKPR");
$reqTanggaPKKPR= $rekanan_ijin_usaha->getField("TANGGAL_PKKPR");
$reqTanggaPKKPRBerakhir = $rekanan_ijin_usaha->getField("TANGGAL_PKKPR_BERAKHIR");

if($reqIjinUsahaId == "") {
  $reqMode = "insert";
}
else {
  $reqMode = "update";
  $pkkpr = '';
  if ($reqPKKPR == '0') {
    $pkkpr = 'display:none';
  }

}
?>
<script type="text/javascript">
$(document).ready(function() {

  $(function(){
    $('#ff').form({
      url: 'rekanan_ijin_usaha_json/data_administrasi_ijin_usaha_ubah',
      onSubmit: function() {
        var v = $(this).form('validate');
        if (!v) return false;

        showLoad(); // tampilkan loading

        var ary = [];

        // Ambil data dari tabel bidang usaha
        $('#tbodyBidangUsaha tr').each(function() {
          var name = $(this).find('td:eq(0)').text().trim(); // kolom pertama = nama bidang usaha
          var value = $(this).find('input[name="reqBidangUsahaId[]"]').val(); // ambil value hidden input

          if (name !== '' && value !== undefined && value !== '') {
            ary.push({ Name: name, Value: value });
          }
        });

        // Validasi: pastikan minimal satu bidang usaha diisi
        if (ary.length === 0) {
          alertError3('Silahkan Isi K B L I');
          hideLoad();
          return false;
        }

        // Hapus dulu hidden input lama jika sudah ada
        $('#ff input[name="jsonBidangUsaha"]').remove();

        // Tambahkan input hidden baru berisi JSON data bidang usaha
        $('<input>').attr({
          type: 'hidden',
          name: 'jsonBidangUsaha',
          value: JSON.stringify(ary)
        }).appendTo('#ff');

        // console.log('Data akan dikirim:', JSON.stringify(ary));

        return true; // lanjut submit
      },
      success: function(data) {
        if (data == 'Data Gagal Tersimpan') {
          alertError3(data);
        } else {
          alertSuccess2('Data berhasil disimpan');
          setTimeout(function() {
            document.location.href = 'main/index/data_administrasi_ijin_usaha';
          }, 2000);
        }
      },
      onLoadError: function() {
        hideLoad();
        alertError3('Terjadi kesalahan koneksi. Silahkan coba lagi.');
      }
    });
  });


  $('#reqTanggalIjin, #reqTanggalBerakhir, #reqTanggaPKKPR, #reqTanggaPKKPRBerakhir').datebox({
    editable: false
  });

});

$(document).ready(function() {
    $('input:radio[name=reqPKKPR]').change(function() {
      if (this.value == '1') {
        $('#displayTglPRRPR').show();
        // $('#displayTglPRRPRFile').show();
        $('#reqTanggaPKKPR').datebox({ required:true });
        // $('#reqTanggaPKKPRBerakhir').datebox({ required:true });
        // $('#reqLinkFile2PDF').validatebox('options').required = true;
        $('#reqLinkFile2PDF').validatebox('validate');
      }
      else if (this.value == '0') {
        $('#displayTglPRRPR').hide();
        // $('#displayTglPRRPRFile').hide();
        $('#reqTanggaPKKPR').datebox({ required:false });
        // $('#reqTanggaPKKPRBerakhir').datebox({ required:false });
        // $('#reqLinkFile2PDF').validatebox('options').required = false;
        $('#reqLinkFile2PDF').validatebox('validate');
      }
    });
  });
</script>

<div class="row">
  <div class="col-md-12 col-sm-12">
    <div class="card">
      <div class="card-header card-head-inverse bg-primary">
        <h4 class="card-title text-white"><?=$reqBidang?>
        </h4>
        <a class="heading-elements-toggle"><i class="fa fa-ellipsis-v font-medium-3"></i></a>
        <div class="heading-elements">
            <ul class="list-inline mb-0">
              <li><a data-action="expand"><i class="ft-maximize"></i></a></li>
              <!-- <li><a data-action="close"><i class="ft-x"></i></a></li> -->
            </ul>
        </div>
      </div>
      <div class="card-content collapse show border-info border-darken-2">
        <div class="card-body">
          <form id="ff" class="easyui-form form-horizontal" method="post" novalidate enctype="multipart/form-data">
            <?php
            if($reqMode == "insert") {
              if ($this->REKANAN_TIPE_ID == '7') { ?>
                <div class="row" style="display: none">
                  <div class="form-group col-md-2 mb-2">
                    <label style="width: 100%">Jenis Ijin</label>
                    <input type="text" class="form-control easyui-validatebox span4" value="NIB" readonly>
                    <input type="hidden" name="reqIjinUsaha" value="2">
                  </div>
                </div>
            <?php
              } else {
             ?>
              <div class="row" style="display: none;">
                <div class="form-group col-md-12 mb-2">
                  <label style="width: 100%">Jenis Ijin</label>
                    <input type="hidden" name="reqIjinUsaha" value="2">
                  <!-- <input type="text" name="reqIjinUsaha" class="easyui-combobox span4"  id="reqIjinUsaha" data-options="valueField:'id',textField:'text',url:'ijin_usaha_json/combo',
                            onSelect : function(rec){
                            }
                            "  value="<?php //$reqIjinUsaha?>" required /> -->
                  <!-- <input type="text" name="reqIjinUsaha" class="easyui-combobox span4"  id="reqIjinUsaha" data-options="valueField:'id',textField:'text',url:'ijin_usaha_json/combo',
                            onSelect : function(rec){
                                  if(rec.id == 1 || rec.id == 2 )
                                        {
                                          $('#reqTanggalBerakhir').datebox({ required:false  });
                                            $('#labelTanggalBerakhir').removeClass();
                                            $('#labelTanggalBerakhir').addClass('col-md-3 control-label');
                                        }
                                        else
                                        {
                                          $('#reqTanggalBerakhir').datebox({ required:true  });
                                            $('#labelTanggalBerakhir').removeClass();
                                            $('#labelTanggalBerakhir').addClass('col-md-3 control-label harus-diisi');
                                        }
                            }
                            "  value="<?php //$reqIjinUsaha?>" required /> -->
                </div>
              </div>
            <?php
              }
            } else { ?>
              <input type="hidden" name="reqIjinUsaha" value="<?=$reqIjinUsaha?>">
            <?php
            }
            ?>

            <div class="row">
              <div class="form-group col-md-8 mb-2">
                <label>Nomor Ijin</label>
                <input name="reqNomorIjin" type="text" title="Nomor ijin harus diisi" class="form-control easyui-validatebox span4" id="reqNomorIjin" value="<?=$reqNomor?>" size="50" required />
              </div>
            </div> 

            <div class="row">
              <div class="form-group col-md-2 mb-2">
                <label style="width: 100%">Tanggal Cetak </label>
                <input type="text" title="Tanggal cetak harus diisi" class="form-control easyui-datebox span2" name="reqTanggalIjin" id="reqTanggalIjin" value="<?=dateToPageCheck($reqTanggalIjin)?>" required style="width: 200% !important"/>
              </div>
              <!-- <div class="form-group col-md-2 mb-2">
                <label style="width: 100%">Tanggal Berakhir </label>
                <input type="text" title="Tanggal berakhir harus diisi" class="form-control easyui-datebox span2" name="reqTanggalBerakhir" id="reqTanggalBerakhir" value="<?php // dateToPageCheck($reqTanggalBerakhir)?>" style="width: 200% !important"/>
              </div> -->
            </div>
            <div class="row">
              <div class="form-group col-md-8 mb-2">
                <label style="width: 100%">File <?=$reqBidang?> <?= UPLOAD_PDF_2MB ?></label>
                 <input type="file" name="reqLinkFile" id="reqLinkFilePDF" size="30" <?php if($reqLinkFileTemp == "") { ?> required <?php } ?> maxlength="1" class="easyui-validatebox"  validType="fileType['pdf']" />
                 <input type="hidden" name="reqLinkFileTemp" value="<?=$reqLinkFileTemp?>">
                 <input type="hidden" name="reqLinkFileTempTipe" value="<?=$reqLinkFileTempTipe?>">
                 <input type="hidden" name="reqLinkFileTempUkuran" value="<?=$reqLinkFileTempUkuran?>">
                 <input type="hidden" name="reqLinkFileTempNama" value="<?=$reqLinkFileTempNama?>">
                 <?php
                 if ($reqLinkFileTempNama) {
                    echo "File :".$reqLinkFileTempNama;
                  } ?>
              </div>
            </div>

            <div class="card mb-1 border-blue border-darken-1">
              <div class="card-content">
                <div class="p-1">

                  <div class="row">
                    <div class="form-group col-md-3 mb-2">
                      <!-- <label style="width: 100%">Jenis Dokumen</label> -->
                      <input type="radio" <?php if($reqPKKPR == '' || $reqPKKPR == '1') echo 'checked';?>  name="reqPKKPR" value="1" required/> PKKPR &nbsp;&nbsp;&nbsp;
                      <input type="radio" <?php if($reqPKKPR == '0') echo 'checked';?> name="reqPKKPR" value="0" required /> Self Declare
                    </div>
                  </div>
                  
                  <div class="row" id="displayTglPRRPR" style="<?= $pkkpr ?>">
                    <div class="form-group col-md-2 mb-2">
                      <label style="width: 100%">Tanggal Terbit </label>
                      <input type="text" title="Tanggal terbit harus diisi" class="form-control easyui-datebox span2" name="reqTanggaPKKPR" id="reqTanggaPKKPR" value="<?=dateToPageCheck($reqTanggaPKKPR)?>" required style="width: 200% !important"/>
                    </div>
                    <div class="form-group col-md-2 mb-2">
                      <label style="width: 100%">Tanggal Berakhir </label>
                      <input type="text" title="Tanggal berakhir harus diisi" class="form-control easyui-datebox span2" name="reqTanggaPKKPRBerakhir" id="reqTanggaPKKPRBerakhir" value="<?= dateToPageCheck($reqTanggaPKKPRBerakhir)?>" style="width: 200% !important"/>
                    </div>
                  </div>

                  <div class="row" id="displayTglPRRPRFile">
                    <div class="form-group col-md-8 mb-2">
                      <label style="width: 100%">File PKKPR <?= UPLOAD_PDF_2MB ?></label>
                       <input type="file" name="reqLinkFile2" id="reqLinkFile2PDF" size="30" <?php if($reqLinkFile2Temp == "") { ?> required <?php } ?> maxlength="1" class="easyui-validatebox"  validType="fileType['pdf']" />
                       <input type="hidden" name="reqLinkFile2Temp" value="<?=$reqLinkFile2Temp?>">
                       <?php
                       if ($reqLinkFile2Temp) {
                          echo "File :".$reqLinkFile2Temp;
                        } ?>
                    </div>
                  </div>

                </div>
              </div>
            </div>

            <div class="card mb-1 border-blue border-darken-1">
              <div class="card-content">
                <div class="p-1">
                  <div class="alert bg-primary alert-icon-left alert-arrow-left alert-dismissible mb-2" role="alert">
                    <span class="alert-icon"><i class="fa fa-th"></i></span>  <strong class="mr-1">K B L I</strong>
                    <div class="badge badge-pill badge-warning">
                      <a id="btnAdd" onClick="openAdd('main/loadUrl/main/bidang_usaha');">
                        <span class="fa fa-plus text-white icon-line-height" data-toggle="tooltip" data-placement="top" title="Tambah K B L I"> Tambah</span>
                      </a>
                    </div>
                  </div>
                  <div class="table-responsive">
                    <table class="border-double table-bordered table mb-0">
                      <thead>
                        <tr class="judul-kolom">
                          <!-- <th>Kode</th>    -->
                          <th>Bidang usaha </th>
                          <th>Aksi</th>
                        </tr>
                      </thead>
                      <tbody id="tbodyBidangUsaha">
                        <?php
                          $rekanan_bidang_usaha->selectByParamsMonitoring(array("REKANAN_ID" => $this->ID, "IJIN_USAHA_ID"=>$reqIjinUsaha));
                          if ($rekanan_bidang_usaha->countRow() > 0) {
                            while($rekanan_bidang_usaha->nextRow())
                            {
                            ?>
                            <tr>
                              <!-- <td><?=$rekanan_bidang_usaha->getField("BIDANG_USAHA_ID")?></td> -->
                              <td><?=$rekanan_bidang_usaha->getField("NAMA")?></td>
                              <td><input type="hidden" name="reqBidangUsahaId[]" value="<?=$rekanan_bidang_usaha->getField("BIDANG_USAHA_ID")?>" /><a title="#" onclick="$(this).parent().parent().remove();" class="btn-aksi"><i class="fa fa-trash" aria-hidden="true"></i></a></td>
                            </tr>
                            <?php
                            }
                          }
                        ?>
                      </tbody>
                    </table>
                  </div>
                  <div class="alert alert-danger mt-2">“Silahkan input semua kode KBLI yang di miliki pada dokumen”</div>
                </div>
              </div>
            </div>


            <div class="form-actions">
              <input type="hidden" name="reqMode" value="<?=$reqMode?>" />
              <a href="main/index/data_administrasi_ijin_usaha" class="<?= CLASS_BTN_DANGER ?> mr-1"> <i class="fa fa-arrow-left"></i> Kembali </a>
              <button type="submit" class="<?= CLASS_BTN_PRIMARY ?> mr-1"><i class="fa fa-check-square-o"></i> Simpan</button>
              <?php
              if($reqIjinUsahaId == "") {} else { ?>
              <a href="rekanan_ijin_usaha_json/data_administrasi_ijin_usaha_hapus?reqIjinUsaha=<?= $reqIjinUsaha ?>" class="<?= CLASS_BTN_SUCCESS ?>" onclick="return confirm('Hapus data ini?');"> <i class="fa fa-close"></i> Hapus </a>
              <?php
              } ?>
            </div>

          </form>
        </div>
      </div>
  </div>
</div>
