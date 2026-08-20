<?php
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 * 
 */

$this->libsession->cekSession();

include_once("functions/string.func.php");
include_once("functions/default.func.php");
include_once("functions/date.func.php");

$this->load->model("Katalog");
$this->load->model("KatalogKategoriRekanan");
$this->load->library("crfs_protect"); $csrf = new crfs_protect('_crfs_rr');

$reqId = httpFilterRequest("reqId");
if ($reqId == '') {
  $reqMode = 'insert';
} else {
  $reqMode = 'update';
  $katalog = new Katalog();
  if($this->USER_TYPE_ID == "1" || $this->USER_TYPE_ID == "2"){  // 1:admin, 2:validator
    $katalog->selectByParams(array(), -1, -1, " AND A.KATALOGID = '".$reqId."'");
  } else {
    $katalog->selectByParams(array(), -1, -1, " AND A.KATALOGID = '".$reqId."' AND A.REKANAN_ID = '".$this->ID."' ");
  }
  $katalog->firstRow();
  $reqNoproduk = $katalog->getField("NOPRODUK");
  $reqNamaproduk = $katalog->getField("NAMAPRODUK");
  $reqHarga = $katalog->getField("HARGA");
  $reqMerek = $katalog->getField("MEREK");
  $reqModeltype = $katalog->getField("MODELTYPE");
  $reqDiameter = $katalog->getField("DIAMETER");
  $reqPanjang = $katalog->getField("PANJANG");
  $reqLebar = $katalog->getField("LEBAR");
  $reqTinggi = $katalog->getField("TINGGI");
  $reqUnitpengukuran = $katalog->getField("UNITPENGUKURAN");
  $reqTkdn = $katalog->getField("TKDNPRODUK");
  $reqBerlakusampai = explode(" ", $katalog->getField("BERLAKUSAMPAI"));
  $reqJenisproduk = $katalog->getField("JENISPRODUK");
  $reqLamaGaransi = $katalog->getField("LAMAGARANSI");
  $reqLamaGaransi2 = $katalog->getField("LAMAGARANSI2");
  $reqJumlahstock = $katalog->getField("JUMLAHSTOCK");
  $reqjumlahstockready = $katalog->getField("JUMLAHSTOCK_READY");
  $reqKemasan = $katalog->getField("KEMASAN");
  $reqStatus = $katalog->getField("STATUS");
  $reqKeteranganTambahan = $katalog->getField("KETERANGANTAMBAHAN");
  $reqPublish = $katalog->getField("PUBLISH");

  if ($reqPublish == '1') {
    redirect(base_url('main/index/katalog_rekanan'));
  }
}
?>
<script type="text/javascript">
$(document).ready(function() {

	$(function(){
		$('#ff').form({
			url:'<?= base_url('katalog_json/add') ?>',
      contentType: 'application/json',
      dataType: 'json',
			onSubmit:function(){
				var v=$(this).form('validate');
        if(v) {
          showLoad();
          // return v;
          var ary = [];
          $('#tbodyKategori tr').each(function (a, b) {
            var name = $('.attrName', b).text();
            var value = $('.attrValue', b).text();
            ary.push({ Name: name, Value: value });
          });
          // alert(JSON.stringify( ary));
          if (ary.length > 0) {
            return v;
          } else {
            alertError3('Silahkan Isi Kategori');
            hideLoad();
            return false;
          }
        } else {
          hideLoad();
          return false;
        }
			},
			success:function(data){
        var a = JSON.parse(data);
        if (a.message === 'sukses') {
          alertSuccess2(a.pesan);
          setTimeout(function() {
            location.href = "<?= base_url('main/index/katalog_rekanan') ?>";
          }, 2000);
        } else {
          alertError2(a.pesan);
          hideLoad();
        }
        // hideLoad();
				// arrData = data.split("-");
			}
		});

    $('#reqBerlakusampai').datebox({
    editable: false
  });

	});

	$(function(){
		$('input[name="reqStatus"]').on('change', function() {
			  var radioValue = $('input[name="reqStatus"]:checked').val();
			  if(radioValue == "0")
			  {
				$( "input[name*='reqSuratKuasa']" ).prop("disabled", "disabled");
				$( "input[name*='reqSuratKuasa']" ).val("");
				$("#reqSuratKuasaTanggal").datebox({ disabled:true, required:false });
				$("#reqSuratKuasaNomor").validatebox({ required:false });
				$("#reqSuratKuasaNotaris").validatebox({ required:false });
			  }
			  else
			  {
				$( "input[name*='reqSuratKuasa']" ).prop("disabled", "");
				$("#reqSuratKuasaTanggal").datebox({ disabled:false, required:true });
				$("#reqSuratKuasaNomor").validatebox({ required:true });
				$("#reqSuratKuasaNotaris").validatebox({ required:true });
			  }
		});

	});
		$("#chk_agreement").click(countChecked);

});

function countChecked() {
	  var n = $("#chk_agreement:checked").length;
	  //alert(n);
	  if(n){
		  $("#reqSubmit").show(0);
	  }else{
		  $("#reqSubmit").hide(0);
	  }
}

function getval(sel)
{
    if (sel.value == '1') {
      $('#readyStock').show();
    } else {
      $('#readyStock').hide();
    }
}
</script>

<style type="text/css">
  .card-text a {
    font-size: 11px;
  }
</style>

<section id="backColor">
  <div class="row">

    <div class="col-md-2 col-sm-12">
      <div class="card border-bottom-primary" style="zoom: 1;">
        <div class="card-body">
          <div class="card-text">
            <?php
            if($this->USER_TYPE_ID == "6") {
            // get Notification Penawaran
              $this->load->model("Katalog");
              $katalog = new Katalog();
              $statement = " AND A.REKANAN_ID = ".$this->ID." AND (A.STATUS='1' OR A.STATUS='3' OR A.STATUS='4' OR A.STATUS='5' )";
              $totalPenawaran = $katalog->getCountByParamsPenawaran(array(), $statement);
            ?>
              <a href="<?= base_url() ?>main/index/katalog_rekanan" class="btn btn-success btn-lg mb-1 text-left" style="width: 100%;"><i class="fa fa-cogs fa-lg pull-right"></i> Katalog</a>
              <a href="<?= base_url() ?>main/index/katalog_rekanan_add" class="btn btn-success btn-lg mb-1 text-left" style="width: 100%; opacity: .3"><i class="fa fa-plus fa-lg pull-right"></i> Tambah Katalog</a>
              <a href="<?= base_url() ?>main/index/katalog_penawaran" class="btn btn-success btn-lg mb-1 text-left" style="width: 100%"><i class="fa fa-shopping-basket fa-lg pull-right"></i> Penawaran <?= '<span class="badge badge-danger" style="opacity: 1">'.$totalPenawaran.'</span>'; ?></a>
              <a href="<?= base_url() ?>main/index/katalog_pernyataan" class="btn btn-success btn-lg mb-1 text-left" style="width: 100%"><i class="fa fa-upload fa-lg pull-right"></i> Upload <br>Kontrak Katalog & <br>Surat Pernyataan<br> Kewajaran Harga</a>
            <?php
            } ?>

            <?php
            if($this->USER_TYPE_ID == "1" || $this->USER_TYPE_ID == "2"){  // 1:admin, 2:validator?>
              <a href="<?= base_url() ?>main/index/katalog_rekanan" class="btn btn-success btn-lg mb-1 text-left" style="width: 100%; opacity: .3"><i class="fa fa-cogs fa-lg pull-right"></i> Katalog</a>
              <a href="<?= base_url() ?>main/index/katalog_validasi" class="btn btn-success btn-lg mb-1 text-left" style="width: 100%;"><i class="fa fa-edit fa-lg pull-right"></i> Verifikasi</a>
              <a href="<?= base_url() ?>main/index/katalog_laporan" class="btn btn-success btn-lg mb-1 text-left" style="width: 100%;"><i class="fa fa-flag fa-lg pull-right"></i> Laporan</a>
            <?php
            } ?>
          </div>
        </div>
      </div>
    </div>

    <div class="col-md-10 col-sm-12">
    	<div class="card border-bottom-primary" style="zoom: 1;">
        <div class="card-header">
          <?php
          if ($reqId) {
            if($this->USER_TYPE_ID == "1" || $this->USER_TYPE_ID == "2"){  // 1:admin, 2:validator
              echo ' <h4 class="card-title">Lihat <small>Katalog</small></h4>';
            } else {
              echo ' <h4 class="card-title">Update <small>Katalog</small></h4>';
            }
           } else {
            echo ' <h4 class="card-title">Tambah <small>Katalog</small></h4>';
           }
            ?>

          <div class="heading-elements">
            <ul class="list-inline mb-0">
              <li><a data-action="expand"><i class="ft-maximize"></i></a></li>
              <!-- <li><a data-action="close"><i class="ft-x"></i></a></li> -->
            </ul>
          </div>
        </div>
        <div class="card-body">
          <div class="form-body">

            <form id="ff" class="easyui-form form-horizontal" method="post" novalidate enctype="multipart/form-data">

              <div class="row">
                <div class="form-group col-md-12 mb-2">
                  <label>Nomor Produk</label>
                  <input type="text" name="reqNoproduk" maxlength="255" accesskey="n" title="Nama Produk harus diisi" class="form-control easyui-validatebox span7" value="<?=isset($reqNoproduk)?$reqNoproduk:''?>" id="reqNoproduk" maxlength="255" required />
                </div>
              </div>

              <div class="row">
                <div class="form-group col-md-12 mb-2">
                  <label>Nama Produk</label>
                  <input type="text" name="reqNamaproduk" maxlength="255" accesskey="n" title="Nama Produk harus diisi" class="form-control easyui-validatebox span7" value="<?=isset($reqNamaproduk)?$reqNamaproduk:''?>" id="reqNamaproduk" maxlength="255" required />
                </div>
              </div>

              <div class="row">
                <div class="form-group col-md-4 mb-2">
                  <label>Harga (<small><b>Rp.</b></small>)
                    <small style="color: red; font-weight: bold"><i>Perubahan harga akan tercatat dalam sistem</i> </small>
                  </label>
                  <input type="text" name="reqHarga" accesskey="n" title="harga harus diisi" class="form-control easyui-validatebox span7" value="<?=isset($reqHarga)?numberToIna($reqHarga):''?>" id="reqHarga" required  OnFocus="FormatAngka('reqHarga')" OnKeyUp="FormatUang('reqHarga')" OnBlur="FormatUang('reqHarga')"/>
                  <input type="hidden" name="reqHargaold" value="<?=isset($reqHarga)?$reqHarga:''?>" />
                  <?php
                  if ($reqId) { ?>
                  <small style="color:blue; font-weight: bold">
                    <a onClick="openAdd('main/loadUrl/main/katalog_riwayat_harga?reqId=<?= $reqId ?>');"><span class="fa fa-eye"></span> lihat history perubahan harga</a>
                  </small>
                  <?php
                  } ?>
                </div>
                <div class="form-group col-md-4 mb-2">
                  <label>Merek</label>
                  <input type="text" name="reqMerek" maxlength="255" accesskey="n" title="Merek harus diisi" class="form-control easyui-validatebox span7" value="<?=isset($reqMerek)?$reqMerek:''?>" id="reqMerek" maxlength="255" required />
                </div>
                <div class="form-group col-md-4 mb-2">
                  <label>Model/Type</label>
                  <input type="text" name="reqModeltype" accesskey="n" title="Merek harus diisi" class="form-control easyui-validatebox span7" value="<?=isset($reqModeltype)?$reqModeltype:''?>" maxlength="255" id="reqModeltype" />
                </div>
              </div>

              <div class="row">
                <div class="form-group col-md-3 mb-2">
                  <label>Diameter (<small><b>cm</b></small>)</label>
                  <input type="text" name="reqDiameter" accesskey="n" title="Diameter harus diisi" class="form-control easyui-validatebox span7" value="<?=isset($reqDiameter)?$reqDiameter:''?>" id="reqDiameter" OnFocus="CekDouble('reqDiameter')" OnKeyUp="CekDouble('reqDiameter')" OnBlur="CekDouble('reqDiameter')" maxlength="10"/>
                  <small>Desimal gunakan titik (99.35)</small>
                </div>
                <div class="form-group col-md-3 mb-2">
                  <label>Panjang (<small><b>cm</b></small>)</label>
                  <input type="text" name="reqPanjang" accesskey="n" title="Panjang harus diisi" class="form-control easyui-validatebox span7" value="<?=isset($reqPanjang)?$reqPanjang:''?>" id="reqPanjang"  OnFocus="CekDouble('reqPanjang')" OnKeyUp="CekDouble('reqPanjang')" OnBlur="CekDouble('reqPanjang')" maxlength="10"/>
                  <small>Desimal gunakan titik (99.35)</small>
                </div>
                <div class="form-group col-md-3 mb-2">
                  <label>Lebar (<small><b>cm</b></small>)</label>
                  <input type="text" name="reqLebar" accesskey="n" title="Lebar harus diisi" class="form-control easyui-validatebox span7" value="<?=isset($reqLebar)?$reqLebar:''?>" id="reqLebar"  OnFocus="CekDouble('reqLebar')" OnKeyUp="CekDouble('reqLebar')" OnBlur="CekDouble('reqLebar')" maxlength="10"/>
                  <small>Desimal gunakan titik (99.35)</small>
                </div>
                <div class="form-group col-md-3 mb-2">
                  <label>Tinggi (<small><b>cm</b></small>)</label>
                  <input type="text" name="reqTinggi" accesskey="n" title="Tinggi harus diisi" class="form-control easyui-validatebox span7" value="<?=isset($reqTinggi)?$reqTinggi:''?>" id="reqTinggi"  OnFocus="CekDouble('reqTinggi')" OnKeyUp="CekDouble('reqTinggi')" OnBlur="CekDouble('reqTinggi')" maxlength="10"/>
                  <small>Desimal gunakan titik (99.35)</small>
                </div>
              </div>

              <div class="row">
                <div class="form-group col-md-3 mb-2">
                  <label>Tahun Pembuatan Produk</label>
                  <input type="text" name="reqUnitpengukuran" accesskey="n" title="Tahun Pembuatan Produk harus diisi" class="form-control easyui-validatebox span7" value="<?=isset($reqUnitpengukuran)?$reqUnitpengukuran:''?>" maxlength="255" id="reqUnitpengukuran" />
                </div>
                <div class="form-group col-md-4 mb-2">
                  <label>TKDN <small>(Tingkat Komponen Dalam Negeri)</small></label>
                  <input type="text" name="reqTkdn" accesskey="n" title="TKDN harus diisi" class="form-control easyui-validatebox span7" value="<?=isset($reqTkdn)?$reqTkdn:''?>" maxlength="255" id="reqTkdn" />
                </div>
                <div class="form-group col-md-2 mb-2">
                  <label style="width: 100%"> Masa Berlaku</label>
                  <input type="text" style="width:120px" name="reqBerlakusampai" title="Masa Berlaku harus diisi" id="reqBerlakusampai" class="form-control easyui-datebox" value="<?=isset($reqBerlakusampai[0])?dateToPageCheck($reqBerlakusampai[0]):''?>"  />
                </div>
                <div class="form-group col-md-3 mb-2">
                  <label style="width: 100%">Jenis Produk</label>
                    <input value="1" name="reqJenisproduk" id="reqJenisproduk-0" type="radio" <?php  if ($reqJenisproduk == '1' || $reqJenisproduk == '') { echo "checked"; } ?> /> Lokal &nbsp;
                    <input value="2" name="reqJenisproduk" id="reqJenisproduk-1" type="radio" <?php  if ($reqJenisproduk == '2') { echo "checked"; } ?>/> Import &nbsp;
                </div>
              </div>

              <div class="row">
                <div class="form-group col-md-2 mb-2">
                  <label>Garansi</label>
                  <input type="text" name="reqLamaGaransi" accesskey="n" title="Garansi harus diisi" class="form-control easyui-validatebox span7" value="<?=isset($reqLamaGaransi)?$reqLamaGaransi:''?>" id="reqLamaGaransi" />
                </div>
                <div class="form-group col-md-2 mb-2">
                  <label>Lama Garansi</label>
                  <select name="reqLamaGaransi2" class="form-control">
                    <option value=""></option>
                    <?php
                      $dataLamaGaransi2 = array('Hari','Bulan','Tahun');
                      foreach ($dataLamaGaransi2 as $key => $value) {
                        if ($value == $reqLamaGaransi2) {
                          $selectLamaGaransi2 = ' selected';
                        } else {
                          $selectLamaGaransi2 = '';
                        }
                        echo '<option value="'.$value.'" '.$selectLamaGaransi2.'>'.$value.'</option>';
                      }
                     ?>
                  </select>
                </div>
                <div class="form-group col-md-3 mb-2">
                  <label>Jumlah Stok</label>
                  <select name="reqJumlahstock" class="form-control" onchange="getval(this);" required>
                    <option value=""></option>
                    <?php
                      $datareqJumlahstock = array('1' => 'Tersedia','2' => 'Pre-Order','3' => 'Hubungi Penyedia');
                      foreach ($datareqJumlahstock as $key => $value) {
                        if ($key == $reqJumlahstock) {
                          $selectJumlahstock = ' selected';
                        } else {
                          $selectJumlahstock = '';
                        }
                        echo '<option value="'.$key.'" '.$selectJumlahstock.'>'.$value.'</option>';
                      }
                     ?>
                  </select>
                </div>
                <div class="form-group col-md-2 mb-2" id="readyStock" style="<?php if($reqJumlahstock == '1') { } else { echo 'display:none'; } ?>">
                  <label style="width: 100%">Waktu Pengiriman (Hari)</label>
                  <input type="text" name="reqjumlahstockready" accesskey="n" title="Ready kapan" class="form-control easyui-validatebox span7" value="<?=isset($reqjumlahstockready)?$reqjumlahstockready:''?>" id="reqjumlahstockready" maxlength="100"/>
                </div>
                <div class="form-group col-md-2 mb-2">
                  <label>Kemasan</label>
                  <select name="reqKemasan" class="form-control" required>
                    <?php
                      $datareqKemasan = array('Pcs','Bundel');
                      foreach ($datareqKemasan as $key => $value) {
                        if ($value == $reqKemasan) {
                          $selectKemasan = ' selected';
                        } else {
                          $selectKemasan = '';
                        }
                        echo '<option value="'.$value.'" '.$selectKemasan.'>'.$value.'</option>';
                      }
                     ?>
                  </select>
                </div>
                <div class="form-group col-md-3 mb-2">
                  <label style="width: 100%">Status</label>
                    <input value="1" name="reqStatus" id="reqStatus-0" type="radio" <?php  if ($reqStatus == '1' || $reqStatus == '') { echo "checked"; } ?> /> Aktif &nbsp;
                    <input value="0" name="reqStatus" id="reqStatus-1" type="radio"<?php  if ($reqStatus == '0') { echo "checked"; } ?> /> Non Aktif &nbsp;
                </div>
              </div>

              <div class="alert bg-primary alert-icon-left alert-arrow-left alert-dismissible mb-2" role="alert">
                <span class="alert-icon"><i class="fa fa-th"></i></span>  <strong>Kategori</strong>
                <div class="badge badge-warning">
                  <a onClick="openAdd('main/loadUrl/main/katalog_kategori_popup');"> <span class="fa fa-plus text-whiteicon-line-height" data-toggle="tooltip" data-placement="top" title="Tambah Kategori"></span> Tambah</a>
                </div>
              </div>
              <div class="form-group col-md-12 mb-2">
                <div class="table-responsive">
                  <table class="table table-bordered table mb-0">
                    <tbody id="tbodyKategori">
                      <?php
                      $katalog_kategori_rekanan = new KatalogKategoriRekanan();
                      $katalog_kategori_rekanan->selectByParams(array("A.KATALOGID" => coalesce($reqId, 0), 'A.CREATED_BY' => $this->ID));
                      // echo $katalog_kategori_rekanan->query;
                      while($katalog_kategori_rekanan->nextRow())
                      {
                      ?>
                      <tr>
                        <td class="attrName" width="75%"><?=$katalog_kategori_rekanan->getField("NAMA")?></td>
                        <td class="attrValue" align="center" width="5%">
                          <input type="hidden" name="reqKomoditas[]" value="<?=$katalog_kategori_rekanan->getField("KATEGORI_ID")?>" />
                          <a title="#" onclick="$(this).parent().parent().remove();" class="btn-aksi"><i class="fa fa-trash" aria-hidden="true"></i></a>
                        </td>
                      </tr>
                      <?php
                      }
                      ?>
                    </tbody>
                  </table>
                </div>
              </div>


              <div class="row">
                <div class="form-group col-md-12 mb-2">
                  <label>Keterangan Tambahan / Deskripsi Produk</label>
                  <textarea name="reqKeteranganTambahan" rows="15" accesskey="l" id="reqKeteranganTambahan" title="Alamat harus diisi" class="form-control easyui-validatebox span6 textarea-tinymce"><?=isset($reqKeteranganTambahan)?$reqKeteranganTambahan:''?></textarea>
                </div>
              </div>
              <div class="form-actions">
                  <a href="<?= base_url() ?>main/index/katalog_rekanan" class="<?= CLASS_BTN_DANGER ?> mr-1"> <i class="fa fa-arrow-left"></i> Kembali </a>
              <?php
              if($this->USER_TYPE_ID == "6"){  // 1:admin, 2:validator
               ?>
                 <input type="hidden" name="reqMode" value="<?= $reqMode ?>">
                  <input type="hidden" name="reqId" value="<?= $reqId ?>">
                  <button type="submit" class="<?= CLASS_BTN_PRIMARY ?>" name="reqSubmit" id="reqSubmit">
                    <i class="fa fa-check-square-o"></i> <?=translate("Simpan", "Register")?>
                  </button>
              <?php
              } ?>
              </div>

            </form>

          </div>
        </div>
      </div>

    </div>

  </div>
</section>
