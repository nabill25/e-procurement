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
$this->load->model(array("RekananTenagaAhli","RekananTenagaAhliSertifikat","RekananTenagaAhliPengalaman","RekananTenagaAhliPendidikan","Pendidikan","Rekanan"));
include_once("functions/string.func.php");
include_once("functions/date.func.php");
include_once("functions/default.func.php");

$rekanan = new Rekanan();
$pendidikan = new Pendidikan();
$rekanan_tenaga_ahli = new RekananTenagaAhli();
$rekanan_tenaga_ahli_detil = new RekananTenagaAhli();

$reqTenagaAhliId = $this->input->get("reqTenagaAhliId") ?: '0';

if ($reqTenagaAhliId != '0') {
  $rekanan_tenaga_ahli->selectByParams(array("REKANAN_TENAGA_AHLI_ID"=>$reqTenagaAhliId, "REKANAN_ID" => $this->ID),-1,-1);
  $rekanan_tenaga_ahli->firstRow();
  $reqNama = $rekanan_tenaga_ahli->getField('NAMA');
  $reqTptLahir = $rekanan_tenaga_ahli->getField('TEMPAT_LAHIR');
  $reqTglLahir = dateToPageCheck($rekanan_tenaga_ahli->getField('TANGGAL_LAHIR'));
  $reqAlamat = $rekanan_tenaga_ahli->getField('ALAMAT');
  $reqKTP = $rekanan_tenaga_ahli->getField('KTP');
  $reqNPWP = $rekanan_tenaga_ahli->getField('NPWP');
  $reqJenisKelamin = $rekanan_tenaga_ahli->getField('JENIS_KELAMIN');
}

if($reqTenagaAhliId=='0')
  $reqMode='insert';
else
  $reqMode='update';

?>
<script type="text/javascript">
$(document).ready(function() {

  $(function(){
    $('#ff').form({
      url:'rekanan_tenaga_ahli_json/data_teknis_tenaga_ahli_ubah',
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
        //alert(data);return false;
        if (data == 'Data Gagal Tersimpan') {
          alertError3(data);
        } else {
          alertSuccess2('Data berhasil disimpan');
          setTimeout(function() {
            document.location.href = 'main/index/data_teknis_tenaga_ahli';
          }, 2000);
        }
      }
    });

  });

  $('#reqTglLahir').datebox({
    editable: false
  });

});

function createRowPengalaman()
{
  $(function () {
    $.get("main/loadUrl/main/data_teknis_tenaga_ahli_pengalaman_template", function (data) {
      $("#tbodyPengalaman").append(data);
    });
  });
}

function createRowPendidikan()
{
  $(function () {
    $.get("main/loadUrl/main/data_teknis_tenaga_ahli_pendidikan_template", function (data) {
      $("#tbodyPendidikan").append(data);
    });
  });
}

function createRowSerifikat()
{
  $(function () {
    $.get("main/loadUrl/main/data_teknis_tenaga_ahli_sertifikat_template", function (data) {
      $("#tbodySertifikat").append(data);
    });
  });
}
</script>

<div class="row">
  <div class="col-md-12 col-sm-12">
    <div class="card">
      <div class="card-header card-head-inverse bg-primary">
        <h4 class="card-title text-white">Tenaga Ahli Tetap</h4>
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
              <label>Nama</label>
              <input name="reqNama" type="text" id="reqNama" size="80" title="Nama harus diisi" class="form-control easyui-validatebox span4" value="<?=$reqNama?>" required />
            </div>
          </div>
          <div class="row">
            <div class="form-group col-md-2 mb-2">
              <label style="width: 100%">Jenis Kelamin</label>
              <select name="reqJenisKelamin" class="easyui-combobox span2" style="width: 200%">
                <option value="L" <?php if($reqJenisKelamin == "L") { ?> selected <?php } ?>>Laki-Laki</option>
                <option value="P" <?php if($reqJenisKelamin == "P") { ?> selected <?php } ?>>Perempuan</option>
              </select>
            </div>
            <div class="form-group col-md-3 mb-2">
              <label>Tempat Lahir</label>
              <input name="reqTptLahir" type="text" id="reqTptLahir" title="Tempat lahir harus diisi" class="form-control easyui-validatebox span3" value="<?=$reqTptLahir?>" required />
            </div>
            <div class="form-group col-md-2 mb-2">
              <label>Tanggal Lahir</label>
              <input style="width: 200% !important" type="text" name="reqTglLahir" id="reqTglLahir" title="Tanggal lahir harus diisi" class="form-control easyui-datebox span2" value="<?=$reqTglLahir?>" required />
            </div>
          </div>
          <div class="row">
            <div class="form-group col-md-12 mb-2">
              <label>Alamat</label>
              <textarea name="reqAlamat" id="reqAlamat" cols="45" class="form-control easyui-validatebox span4" rows="5" required ><?=$reqAlamat?></textarea>
            </div>
          </div>
          <div class="row">
            <div class="form-group col-md-12 mb-2">
              <label>No. KTP</label>
              <input name="reqKTP" type="text" id="reqKTP" class="form-control easyui-validatebox span4" required value="<?=$reqKTP?>" />
            </div>
          </div>
          <div class="row">
            <div class="form-group col-md-12 mb-2">
              <label>No. NPWP</label>
              <input type="hidden" id="reqNPWPStatus" value="0" />
              <input type="text" id="reqNPWP" name="reqNPWP"  class="form-control easyui-validatebox span4" accesskey="n" value="<?=$reqNPWP?>" onkeydown="return format_npwp(event, 'reqNPWP');" maxlength="20"  required />
            </div>
          </div>

          <div class="card mb-1 border-blue border-darken-1">
            <div class="card-content">
              <div class="p-1">
                <div class="alert bg-primary alert-icon-left alert-arrow-left alert-dismissible mb-2" role="alert">
                  <span class="alert-icon"><i class="fa fa-th"></i></span>  <strong>Pendidikan/Jurusan</strong>
                  <div class="badge badge-pill badge-warning">
                    <a onclick="createRowPendidikan()"> <span class="fa fa-plus text-white icon-line-height" data-toggle="tooltip" data-placement="top" title="Tambah Pendidikan/Jurusan"></span> Tambah</a>
                  </div>
                </div>
                <div class="table-responsive">
                  <table class="border-double table mb-0">
                    <thead>
                      <tr>
                        <th>Pendidikan</th>
                        <th>Jurusan</th>
                        <th>Aksi</th>
                      </tr>
                    </thead>
                    <tbody id="tbodyPendidikan">
                      <?php
                      $ada = 0;
                      $no = 1;
                      $rekanan_pendidikan = new RekananTenagaAhliPendidikan();
                      $rekanan_pendidikan->selectByParams(array("REKANAN_TENAGA_AHLI_ID"=>$reqTenagaAhliId));

                      while($rekanan_pendidikan->nextRow())
                      {
                      ?>
                      <tr>
                        <td width="10%">
                          <input type="text" name="reqPendidikan[]" class="form-control easyui-combobox span2"  id="reqPendidikan<?=$no?>" data-options="valueField:'id',textField:'text',url:'pendidikan_json/combo'"  required value="<?=$rekanan_pendidikan->getField("PENDIDIKAN")?>" style="width: 150%" />
                        </td>
                        <td>
                          <input type="text" class="form-control easyui-validatebox span6"  name="reqJurusan[]" required id="reqJurusan<?=$no?>" value="<?=$rekanan_pendidikan->getField("JURUSAN")?>" />
                        </td>
                        <td width="5%">
                          <a onClick="deleteData('rekanan_tenaga_ahli_pendidikan_json/delete/', '<?=$rekanan_pendidikan->getField("REKANAN_TENAGA_AHLI_PEND_ID")?>')" ><i class="fa fa-trash" aria-hidden="true"></i></a>
                        </td>
                      </tr>
                      <?php
                      }
                      $no++;
                      $ada++;
                      ?>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>

          <div class="card mb-1 border-blue border-darken-1">
            <div class="card-content">
              <div class="p-1">
                <div class="alert bg-primary alert-icon-left alert-arrow-left alert-dismissible mb-2" role="alert">
                  <span class="alert-icon"><i class="fa fa-th"></i></span>  <strong>Pengalaman</strong>
                  <div class="badge badge-pill badge-warning">
                    <a onclick="createRowPengalaman()"> <span class="fa fa-plus text-white icon-line-height" data-toggle="tooltip" data-placement="top" title="Tambah Pengalaman"></span> Tambah</a>
                  </div>
                </div>
                <div class="table-responsive">
                  <table class="border-double table mb-0">
                    <thead>
                      <tr>
                        <th>Pekerjaan <br>(Nama Proyek)</th>
                        <th>Posisi/Jabatan <br>(dalam proyek)</th>
                        <th>Periode/Lama <br>(bulan)</th>
                        <th>Tahun</th>
                        <th>Nama Instansi <br>(Pengguna Jasa)</th>
                        <th>Nama Perusahaan <br>Tempat Bekerja</th>
                        <th>Aksi</th>
                      </tr>
                    </thead>
                    <style type="text/css">
                      .numberbox {
                        height: 38px !important ;
                      }
                    </style>
                    <tbody id="tbodyPengalaman">
                      <?php
                      $ada = 0;
                      $no = 1;
                      $rekanan_pengalaman = new RekananTenagaAhliPengalaman();
                      $rekanan_pengalaman->selectByParams(array("REKANAN_TENAGA_AHLI_ID"=>$reqTenagaAhliId));
                      while($rekanan_pengalaman->nextRow()){
                      ?>
                      <tr class="gelap">
                        <td>
                          <input type="text" class="form-control easyui-validatebox" required name="reqPekerjaan[]" id="reqPekerjaan0<?=$no?>" value="<?=$rekanan_pengalaman->getField("PEKERJAAN")?>">
                        </td>
                        <td>
                          <input type="text" class="form-control easyui-validatebox" required name="reqPosisi[]" id="reqPosisi0<?=$no?>" value="<?=$rekanan_pengalaman->getField("POSISI")?>">
                        </td>
                        <td>
                          <input type="text" class="form-control easyui-numberbox" required  name="reqLama[]" id="reqLama0<?=$no?>" style="width:80px; height: 0px !important; padding: .65rem 1rem;" value="<?=$rekanan_pengalaman->getField("PERIODE")?>" maxlength="2">
                        </td>
                        <td>
                          <input type="text" class="form-control easyui-numberbox" required name="reqJumlahTahun[]" id="reqJumlahTahun0<?=$no?>" style="width:80px" value="<?=$rekanan_pengalaman->getField("PENGALAMAN")?>" maxlength="4">
                        </td>
                        <td>
                          <input type="text" class="form-control easyui-validatebox" required name="reqInstansi[]" id="reqInstansi0<?=$no?>" value="<?=$rekanan_pengalaman->getField("INSTANSI")?>">
                        </td>
                        <td>
                          <input type="text" class="form-control easyui-validatebox" required name="reqNamaPerusahaan[]" id="reqNamaPerusahaan<?=$no?>" value="<?=$rekanan_pengalaman->getField("NAMA_PERUSAHAAN")?>">
                        </td>
                        <td width="5%">
                          <a onClick="deleteData('rekanan_tenaga_ahli_pengalaman_json/delete/', '<?=$rekanan_pengalaman->getField("REKANAN_TENAGA_AHLI_PENG_ID")?>')" ><i class="fa fa-trash" aria-hidden="true"></i></a>
                        </td>
                      </tr>
                      <?php
                      }
                      $no++;
                      $ada++;
                      ?>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>

          <div class="card mb-1 border-blue border-darken-1">
            <div class="card-content">
              <div class="p-1">
                <div class="alert bg-primary alert-icon-left alert-arrow-left alert-dismissible mb-2" role="alert">
                  <span class="alert-icon"><i class="fa fa-th"></i></span>  <strong>Sertifikasi Keahlian</strong>
                  <div class="badge badge-pill badge-warning">
                    <a onclick="createRowSerifikat()"> <span class="fa fa-plus text-white icon-line-height" data-toggle="tooltip" data-placement="top" title="Tambah Sertifikasi Keahlian"></span> Tambah</a>
                  </div>
                </div>
                <div class="table-responsive">
                  <table class="border-double table mb-0">
                    <thead>
                        <tr>
                            <th>Keahlian</th>
                            <th>No. Sertifikat</th>
                            <th>File Sertifikat <?= UPLOAD_PDF_2MB ?></th>
                            <th>Instansi/Penerbit</th>
                            <th width="100px">Tanggal Berlaku</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="tbodySertifikat">
                      <?php
                      $ada = 0;
                      $no = 1;
                      $rekanan_sertifikat = new RekananTenagaAhliSertifikat();
                      $rekanan_sertifikat->selectByParams(array("REKANAN_TENAGA_AHLI_ID"=>$reqTenagaAhliId));
                      while($rekanan_sertifikat->nextRow())
                      {
                      $reqLinkFileTemp = $rekanan_sertifikat->getField("PATH_FILE");
                      ?>
                        <tr>
                          <td>
                            <input class="form-control easyui-validatebox span3" type="text" required name="reqKeahlian[]" id="reqKeahlian<?=$no?>" value="<?=$rekanan_sertifikat->getField("KEAHLIAN")?>" />
                          </td>
                          <td>
                            <input class="form-control easyui-validatebox span3" required name="reqNoSertifikat[]" type="text" id="reqNoSertifikat<?=$no?>" value="<?=$rekanan_sertifikat->getField("NOMOR")?>" />
                          </td>
                          <td>
                            <input type="file" name="reqLinkFile[]" id="reqLinkFile[]<?=$no?>" size="30" <?php if($reqLinkFileTemp == "") { ?>  required  <?php } ?> class="easyui-validatebox span2" value="<?=$rekanan_sertifikat->getField("PATH_FILE")?>" validType="fileType['pdf']" />
                            <input type="hidden" name="reqLinkFileTemp[]" id="reqLinkFileTemp[]<?=$no?>" value="<?=$rekanan_sertifikat->getField("PATH_FILE")?>">
                            <input type="hidden" name="reqLinkFileTempTipe[]" id="reqLinkFileTempTipe[]<?=$no?>" value="<?=$rekanan_sertifikat->getField("TIPE")?>">
                            <input type="hidden" name="reqLinkFileTempUkuran[]" id="reqLinkFileTempUkuran[]<?=$no?>" value="<?=$rekanan_sertifikat->getField("UKURAN")?>">
                            <input type="hidden" name="reqLinkFileTempNama[]" id="reqLinkFileTempNama[]<?=$no?>" value="<?=$rekanan_sertifikat->getField("NAMA_FILE")?>">
                            <?php
                            if ($rekanan_sertifikat->getField("NAMA_FILE")) {
                               echo "<br><small>File :".$rekanan_sertifikat->getField("NAMA_FILE").'</small> <a href="'.base_url('uploads/tenaga_ahli_sertifikat/').$rekanan_sertifikat->getField("PATH_FILE").'" class="badge badge-primary" target="_blank">Download file</a>';
                             } ?>
                          </td>
                          <td>
                            <input class="form-control easyui-validatebox span3" type="text" required name="reqInstansi2[]" id="reqInstansi2<?=$no?>" value="<?=$rekanan_sertifikat->getField("INSTANSI")?>" />
                          </td>
                          <td>
                            <?php
                            if ($rekanan_sertifikat->getField('TANGGAL_BERLAKU') != '') {
                              $a = explode(" ",$rekanan_sertifikat->getField('TANGGAL_BERLAKU'));
                              $b = explode("-",$a[0]);
                              $c = $b[2].'-'.$b[1].'-'.$b[0];
                            } else {
                              $c = '';
                            }

                            ?>
                            <input style="width: 200% !important" type="text" name="reqTglBerlaku[]" id="reqTglBerlaku<?=$no?>" title="Tanggal berlaku harus diisi" class="form-control easyui-datebox span2" value="<?= $c ?>" />

                          </td>
                          <td width="5%">
                            <a onClick="deleteData('rekanan_tenaga_ahli_sertifikat_json/delete/', '<?=$rekanan_sertifikat->getField("REKANAN_TENAGA_AHLI_SERT_ID")?>')"
                              ><i class="fa fa-trash" aria-hidden="true"></i></a>
                          </td>
                        </tr>
                        <?php
                      }
                      $no++;
                      $ada++;
                       ?>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>

          <div class="form-actions">
            <input type="hidden" name="reqTenagaAhliId" value="<?=$reqTenagaAhliId?>" />
            <input type="hidden" name="reqMode" value="<?=$reqMode?>" />
            <a href="main/index/data_teknis_tenaga_ahli" class="<?= CLASS_BTN_DANGER ?> mr-1"><?= BTN_KEMBALI ?></a>
            <button type="submit" class="<?= CLASS_BTN_PRIMARY ?>"><?= BTN_SIMPAN ?></button>
          </div>
        </div>
      </div>
      </form>

    </div>
  </div>
</div>
