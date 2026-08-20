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

$this->load->library("kauth");  $userLogin = new kauth();
$this->load->model("Rekanan");
$this->load->model("RekananIjinUsaha");
$this->load->model("RekananBidangUsaha");

include_once("functions/string.func.php");
include_once("functions/date.func.php");
include_once("functions/default.func.php");

$rekanan_ijin_usaha = new RekananIjinUsaha();
$rekanan_bidang_usaha = new RekananBidangUsaha();
$rekanan = new Rekanan();

$submitSimpan	= httpFilterPost("submitSimpan");
$reqBatal	= httpFilterPost("reqBatal");
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

$reqIjinUsahaId = $this->input->get('reqIjinUsahaId') ?: '';
$reqTipe	= $this->input->get("reqTipe") ?: '0';
$reqId = $this->ID;

if($reqIjinUsahaId == '') {

	$reqMode = 'insert';
}
else {
	$reqMode='update';
	$reqId = $this->ID;
	$rekanan_ijin_usaha->selectByParams(array("REKANAN_ID" => $this->ID, "REKANAN_IJIN_USAHA_ID"=>$reqIjinUsahaId));
	$rekanan_ijin_usaha->firstRow();

	$reqIjinUsahaId = $rekanan_ijin_usaha->getField("REKANAN_IJIN_USAHA_ID");
	$reqNomor = $rekanan_ijin_usaha->getField("NO_IJIN");
	$reqTanggalIjin = $rekanan_ijin_usaha->getField("TANGGAL");
	$reqTanggalBerakhir = $rekanan_ijin_usaha->getField("TANGGAL_BERAKHIR");
	$reqInstansi = $rekanan_ijin_usaha->getField("INSTANSI");
	$reqBidang = $rekanan_ijin_usaha->getField("IJIN_USAHA");
	$reqLinkFileTemp= $rekanan_ijin_usaha->getField("PATH_FILE");
	$reqLinkFileTempTipe= $rekanan_ijin_usaha->getField("TIPE");
	$reqLinkFileTempUkuran= $rekanan_ijin_usaha->getField("UKURAN");
	$reqLinkFileTempNama= $rekanan_ijin_usaha->getField("NAMA_FILE");
	$reqNamaPemegang = $rekanan_ijin_usaha->getField("NAMA_PEMEGANG");

	$rekanan_bidang_usaha->selectByParamsMonitoring(array("REKANAN_ID" => $this->ID,
	"IJIN_USAHA_ID"=>$reqTipe, "REKANAN_BIDANG_USAHA_INFO_ID" => $reqIjinUsahaId));

}


?><script type="text/javascript">
$(document).ready(function() {

	$(function(){
    $('#ff').form({
      url: 'rekanan_ijin_usaha_json/registrasi_sbu',
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
          alertError3('Silahkan Isi Bidang Usaha');
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

        // Debug (opsional)
        // console.log('Data akan dikirim:', JSON.stringify(ary));

        return true; // lanjut submit
      },
      success: function(data) {
				if (data == 'Data Gagal Tersimpan') {
          alertError3(data);
        } else {
          alertSuccess2('Data berhasil disimpan');
          setTimeout(function() {
						document.location.href = 'main/index/data_administrasi_sbu';
          }, 2000);
        }

      },
      onLoadError: function() {
        hideLoad();
        alertError3('Terjadi kesalahan koneksi. Silahkan coba lagi.');
      }
    });
  });

  $('#reqTanggalIjin, #reqTanggalBerakhir').datebox({
    editable: false
  });


});
</script>

<div class="row">
  <div class="col-md-12 col-sm-12">
    <div class="card">
      <div class="card-header card-head-inverse bg-primary">
        <h4 class="card-title text-white">Sertifikat Badan Usaha Konstruksi</h4>
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
          <div class="row">
            <div class="form-group col-md-12 mb-2">
              <label>Nomor Sertifikat</label>
              <input name="reqNomorIjin" type="text" title="Nomor sertifikat harus diisi" class="form-control easyui-validatebox span4" id="reqNomorIjin" value="<?=$reqNomor?>" size="50" required />
            </div>
          </div>
          <div class="row">
            <div class="form-group col-md-2 mb-2">
              <label style="width: 100%">Tanggal sertifikat</label>
              <input type="text" title="Tanggal sertifikat harus diisi" class="form-control easyui-datebox span2" name="reqTanggalIjin" style="width: 200% !important" id="reqTanggalIjin" value="<?=dateToPageCheck($reqTanggalIjin)?>" required />
            </div>
            <div class="form-group col-md-2 mb-2">
              <label style="width: 100%">Tanggal berakhir</label>
              <input type="text" title="Tanggal berakhir harus diisi" class="form-control easyui-datebox span2" name="reqTanggalBerakhir" style="width: 200% !important" id="reqTanggalBerakhir" value="<?=dateToPageCheck($reqTanggalBerakhir)?>" required />
            </div>
            <div class="form-group col-md-8 mb-2">
              <label>Lembaga Sertifikasi</label>
              <input name="reqInstansiPemberiIjin" title="Lembaga Sertifikasi harus diisi" class="form-control easyui-validatebox span4" type="text" id="reqInstansiPemberiIjin" value="<?=$reqInstansi?>" size="80" required />
            </div>
          </div> 
          <div class="row">
            <div class="form-group col-md-12 mb-2">
              <label>File SBU <?= UPLOAD_PDF_2MB ?></label><br>
              <input type="hidden" name="reqLinkFileTemp" value="<?=$reqLinkFileTemp?>" />
              <input type="hidden" name="reqLinkFileTempTipe" value="<?=$reqLinkFileTempTipe?>" />
              <input type="hidden" name="reqLinkFileTempUkuran" value="<?=$reqLinkFileTempUkuran?>" />
              <input type="file" name="reqLinkFile" id="reqLinkFilePDF" size="30" <?php if($reqLinkFileTemp == "") { ?> required <?php } ?> class="easyui-validatebox"  validType="fileType['pdf']"/>
              <input type="hidden" name="reqLinkFileTempNama" value="<?=$reqLinkFileTempNama?>">
              <?php
              // if ($reqLinkFileTempNama) {
              //    echo "File :".$reqLinkFileTempNama;
              //  }
               ?>
            </div>
          </div>
          <div class="card mb-1 border-blue border-darken-1">
            <div class="card-content">
              <div class="p-1">
                <div class="alert bg-primary alert-icon-left alert-arrow-left alert-dismissible mb-2" role="alert">
                  <span class="alert-icon"><i class="fa fa-th"></i></span>  <strong>Bidang Usaha</strong>
                  <?php
                  if ($reqStatusValidasi != '1' || $reqIjinUsahaId == '') { ?>
                    <div class="badge badge-pill badge-warning">
                      <a id="btnAdd" onClick="openAdd('main/loadUrl/main/bidang_usaha_sbu');">
                        <span class="fa fa-plus text-white icon-line-height" data-toggle="tooltip" data-placement="top" title="Tambah Bidang Usaha"> Tambah</span>
                      </a>
                    </div>
                  <?php
                  } ?>
                </div>

                <div class="table-responsive">
                  <table class="table table-bordered table mb-0">
                    <thead>
                      <tr class="judul-kolom">
                        <!-- <th>Kode</th>    -->
                        <th>Bidang usaha</th>
                        <th>Aksi</th>
                      </tr>
                    </thead>
                    <tbody id="tbodyBidangUsaha">
                      <?php
                      $rekanan_bidang_usaha->selectByParamsMonitoring(array("REKANAN_ID" => $this->ID, "IJIN_USAHA_ID"=>$reqTipe/*, "REKANAN_BIDANG_USAHA_INFO_ID" => $reqIjinUsahaId*/));
                      if ($rekanan_bidang_usaha->countRow() > 0) {
                        while($rekanan_bidang_usaha->nextRow())
                        {
                      ?>
                        <tr>
                          <!-- <td><?=$rekanan_bidang_usaha->getField("BIDANG_USAHA_ID")?></td> -->
                          <td><?=$rekanan_bidang_usaha->getField("NAMA")?></td>
                          <td><input type="hidden" name="reqBidangUsahaId[]" value="<?=$rekanan_bidang_usaha->getField("BIDANG_USAHA_ID")?>" /><a title="#" class="btn-aksi" onclick="$(this).parent().parent().remove();"><i class="fa fa-trash" aria-hidden="true"></i></a></td>
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
            <input type="hidden" name="reqId" value="<?=$reqId?>" />
            <input type="hidden" name="reqMode" value="<?=$reqMode?>" />
            <input type="hidden" name="reqIjinUsahaId" value="<?=$reqIjinUsahaId?>" />
            <input type="hidden" name="reqTipe" value="<?=$reqTipe?>" />
            <a href="main/index/data_administrasi_sbu" class="<?= CLASS_BTN_DANGER ?> mr-1"><?= BTN_KEMBALI ?></a>
            <button type="submit" class="<?= CLASS_BTN_PRIMARY ?> mr-1"><?= BTN_SIMPAN ?></button>
            <?php
              if($reqIjinUsahaId == "") {} else { ?>
              <a href="rekanan_ijin_usaha_json/data_administrasi_ijin_usaha_sbu_hapus?reqIjinUsaha=<?= $reqTipe ?>" class="<?= CLASS_BTN_SUCCESS ?>" onclick="return confirm('Hapus data ini?');"> <i class="fa fa-close"></i> Hapus </a>
              <?php
              } ?>
          </div>

        </div>
      </div>
      </form>
    </div>
  </div>
</div>
