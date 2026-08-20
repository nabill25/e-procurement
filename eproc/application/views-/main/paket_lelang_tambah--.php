<?php
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 * 
 */
$this->libsession->cekSession();

// if($this->USER_TYPE_ID == "")
//     redirect("app");

include_once("functions/string.func.php");
include_once("functions/default.func.php");
include_once("functions/date.func.php");
$this->load->model("UnitKerja");
$this->load->model("Paket");
$this->load->model("PaketBidangUsaha");
$this->load->model("RekananKualifikasi");
$this->load->model("PermohonanPaket");
$this->load->model("PermohonanPaketFile");
$this->load->model("PaketPanitia");

$paket = new Paket();
$rekanan_kualifikasi = new RekananKualifikasi();

$reqId = $this->input->get("reqId");
$reqPermohonanId = $this->input->get("reqPermohonanId");

// cek apakah sudah di input permohonan ke paket
$cek_paket_by_permohonan = new Paket();
$cekPermohonanCount = $cek_paket_by_permohonan->getCountByParams(array("A.PERMOHONAN_PAKET_ID" => $reqPermohonanId, "A.UNIT_KERJA_ID" => $this->UNIT_KERJA_ID));
// if($reqId == "")
if($cekPermohonanCount == 0 && $reqId == "")
{
  // Default data unit kerja
	$unit_kerja = new UnitKerja();
  $unit_kerja->selectByParams(array('UNIT_KERJA_ID'=>$this->UNIT_KERJA_ID));
  $unit_kerja->firstRow();
  $reqAlamatPanitia =  $unit_kerja->getField("ALAMAT");
	$arrTelp = explode(" ", trim($unit_kerja->getField("TELEPON")));
	$reqTelpPanitiaKode = $arrTelp[0];
	$reqTelpPanitia = $arrTelp[1];
	$reqEmailPanitia = $unit_kerja->getField("EMAIL");
	$reqKualifikasiRekanan = 3;
	$reqMataUang = "IDR";
  $reqMultiPemenang = "0"; // Defaul bukan kontrak payung

	if($reqPermohonanId == "")
	{}
	else
	{
		$permohonan_paket = new PermohonanPaket();
		$permohonan_paket->selectByParams(array("A.PERMOHONAN_PAKET_ID" => $reqPermohonanId, "A.UNIT_KERJA_ID" => $this->UNIT_KERJA_ID));
		$permohonan_paket->firstRow();
		$reqPermohonanId = $permohonan_paket->getField("PERMOHONAN_PAKET_ID");
		$reqPermohonan = $permohonan_paket->getField("NAMA");
    $reqPermohonanNotaDinas = $permohonan_paket->getField("NOTA_DINAS");
    $reqPermohonanNoDisposisi = $permohonan_paket->getField("NO_PPA");
		$reqPermohonanTglDisposisi = $permohonan_paket->getField("TANGGAL");
		$reqPermohonanUserLogin = $permohonan_paket->getField("USER_LOGIN_ID");
		$reqNamaPaket = $permohonan_paket->getField("NAMA");
    $reqNilaiPekerjaan = $permohonan_paket->getField("NILAI");
    $reqPL = $permohonan_paket->getField("PENGADAANLANGSUNG");
		$reqPermohonanKeterangan = $permohonan_paket->getField("KETERANGAN");
    if ($reqPL == '1') { // Pengadaan langsung <= 300jt
	   $reqJenisPekerjaan = '3';  // 1 Pekerjaan Konstruksi, 2 Jasa Konsultansi, 3 Barang, 4 Jasa Lainnya, 5 Katalog
     $reqMetodePengadaan = '2'; // Pengadaan Langsung
    } else if ($reqPL == '2') { // ePurchasing Pejabat Pengadaan
     $reqJenisPekerjaan = '5';  // 1 Pekerjaan Konstruksi, 2 Jasa Konsultansi, 3 Barang, 4 Jasa Lainnya, 5 Katalog
     // $reqMetodePengadaan = '6'; // e-Purchasing
    }
  }
  $reqBidding = 0;


}
else
{
  $cek_paket_by_permohonan2 = new Paket();
  $cek_paket_by_permohonan2->selectByParams(array("A.PERMOHONAN_PAKET_ID" => $reqPermohonanId, "A.UNIT_KERJA_ID" => $this->UNIT_KERJA_ID));
  $cek_paket_by_permohonan2->firstRow();
  $reqId = ($reqId) ? $reqId : $cek_paket_by_permohonan2->getField("PAKET_ID");

  $paket->selectByParams(array("A.PAKET_ID" => coalesce($reqId, 0)));
  $paket->firstRow();

  $paket_panitia = new PaketPanitia();
  $idPanitia = $paket_panitia->getCountByParams(array("PAKET_ID" => $reqId, "NIP" => $this->NIP));

  // echo '---'.$this->USER_LOGIN_ID;
  /*
  if ($paket->getField("USER_LOGIN_ID") != $this->USER_LOGIN_ID) { // khusus pembuat paket
  // if (($paket->getField("USER_LOGIN_ID") != $this->USER_LOGIN_ID) || $idPanitia == 0) {
    redirect(base_url('main'));
    // echo $paket->getField("USER_LOGIN_ID").'-'.$this->USER_LOGIN_ID.'-'.$idPanitia;
  } */

  $reqMetodePengadaan = $paket->getField("PAKET_METODE_LELANG_ID");
  $reqMetodeKualifikasi = $paket->getField("PAKET_METODE_KUALIFIKASI_ID");
  $reqMetodeEvaluasi = $paket->getField("PAKET_METODE_EVALUASI_ID");
  $reqJenisPekerjaan = $paket->getField("PAKET_JENIS_ID");
	$reqKualifikasiRekanan = $paket->getField("REKANAN_KUALIFIKASI_ID");
	$reqNamaPaket = $paket->getField("NAMA");
	$reqUraianKegiatan = $paket->getField("URAIAN");
	$reqLokasiPekerjaan = $paket->getField("LOKASI");
    $reqAlamatPanitia =  $paket->getField("ALAMAT");
	$arrTelp = explode(" ", trim($paket->getField("TELEPON")));
	$reqTelpPanitiaKode = $arrTelp[0];
	$reqTelpPanitia = $arrTelp[1];
	$reqEmailPanitia = $paket->getField("EMAIL");
	$reqNilaiPekerjaan = $paket->getField("NILAI");
	$reqPermohonanId = $paket->getField("PERMOHONAN_PAKET_ID");
  // Membatasi Pengadaan langsung <=300 juta
  if ($reqPermohonanId) {
    $permohonan_paket = new PermohonanPaket();
    $permohonan_paket->selectByParams(array("A.PERMOHONAN_PAKET_ID" => $reqPermohonanId, "A.UNIT_KERJA_ID" => $this->UNIT_KERJA_ID));
    $permohonan_paket->firstRow();
    $reqPL = $permohonan_paket->getField("PENGADAANLANGSUNG");
    $reqPermohonanKeterangan = $permohonan_paket->getField("KETERANGAN");
    $reqPermohonanNotaDinas = $permohonan_paket->getField("NOTA_DINAS");
    $reqPermohonanNoDisposisi = $permohonan_paket->getField("NO_PPA");
    $reqPermohonanTglDisposisi = $permohonan_paket->getField("TANGGAL");
    if ($reqPL == '1') { // Pengadaan langsung <= 300jt
     $reqMetodePengadaan = '2';
    } else if ($reqPL == '2') { // ePurchasing Pejabat Pengadaan
     // $reqMetodePengadaan = '6';
    }
  }
  // End Membatasi Pengadaan langsung <=300 juta
	$reqPermohonan = $paket->getField("PERMOHONAN");
	$reqPermohonanNotaDinas = $paket->getField("PERMOHONAN_NOTA_DINAS");
	$reqMetodePenyampulan = $paket->getField("SISTEM_SAMPUL");
	$reqBahasa = $paket->getField("BAHASA");
	$reqMataUang = $paket->getField("NILAI_MATA_UANG");
	$reqBidingMenit = $paket->getField("BIDDING_MENIT");
  $reqBidding = $paket->getField("BIDDING");
  $reqBobotTeknis = $paket->getField("BOBOT_TEKNIS");
  $reqBobotHarga = $paket->getField("BOBOT_HARGA");
  $reqPassingGrade = $paket->getField("PASSING_GRADE");
	$reqMultiPemenang = $paket->getField("MULTI_PEMENANG"); // Untuk kontrak payung

	//echo "dds".$reqBidingMenit;exit;

}

?>

<script type="text/javascript">

<?php
if ($reqPL != '2')  // selain 2 (Pembelian Langsung) KBLI wajib di isi
{ ?>
  $(document).ready(function() {

  	$(function(){
  		$('#ff').form({
  			url:'paket_json/add',
  			onSubmit:function(){
  				// return $(this).form('validate');
          var v=$(this).form('validate');
          if(v) {
            showLoad();  // show the message box
            var ary = [];
            // $(function () {
                $('#tbodyBidangUsaha tr').each(function (a, b) {
                  var name = $('.attrName', b).text();
                  var value = $('.attrValue', b).text();
                  ary.push({ Name: name, Value: value });
                });
                // alert(JSON.stringify( ary));
                if (ary.length > 0) {
                  return v;
                } else {
                  alertError3('Silahkan Isi Bidang Usaha');
                  hideLoad();
                  return false;
                }
            // });
          }
        },
        success:function(data){
					// $.messager.alert('Info', data, 'info');return false;
					if (data == 'Data Gagal Tersimpan') {
						alertError3(data);
            hideLoad();
					} else {
						alertSuccess2('Data berhasil disimpan');
						setTimeout(function() {
							location.reload();
					    // hideLoad();
          	}, 2000);
					}
          // hideLoad();
  			}
  		});

  	});

  });
<?php
} else { // Pembelian Langsung tidak input KBLI
  ?>
  $(document).ready(function() {

    $(function(){
      $('#ff').form({
        url:'paket_json/add',
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
          // hideLoad();
          // $.messager.alert('Info', data, 'info');return false;
          // location.reload();
          hideLoad();
          alertSuccess2('Data berhasil disimpan');
          location.reload();
          // setTimeout(function() {
          //     document.location.href = 'main/index/pembelian_langsung';
          // }, 2000);
        }
      });

    });

  });

<?php
} ?>


function createRowNotaDinas()
{
	$(function () {
		$.get("main/loadUrl/main/permohonan_lelang_add_nota_dinas_template", function (data) {
			$("#tbodyPermohonanPaketFile").append(data);
		});
	});
}

function getComboA() {
  alert('aaaa');
}

$('#reqMetodeEvaluasi')
.on('change', function(){
    alert($('#reqMetodeEvaluasi option:selected').val());
});

</script>

<div class="row">
  <div class="col-md-12 col-sm-12">
    <div class="card">
      <div class="card-header card-head-inverse bg-primary">
        <h4 class="card-title text-white">
          <?php
          if($cekPermohonanCount == 0 && $reqId == "")
          { echo 'Tambah Pengadaan'; } else { echo 'Edit Pengadaan'; } ?>

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
            if($reqPermohonanId == "")
            {}
            else
            {
            ?>
            <!-- <div class="card mb-1 border-blue border-darken-1" style="padding: 5px 10px 0 10px; background-color: #fff3f3">
            <div class="row">
              <div class="form-group col-md-12 mb-2">
                <label><b>Referensi Permohonan</b></label>
                <p> -->
                  <?php // echo $reqPermohonanNoDisposisi.' - '.getFormattedDate($reqPermohonanTglDisposisi); ?><br>
                  <?php // echo $reqPermohonan?>
                <!-- </p> -->
                <!-- <label><b>Keterangan</b></label> <br> -->
                <?php //$reqPermohonanKeterangan?>
              <!-- /div>
            </div>
            </div> -->
                <input type="hidden" name="reqNamaPaket" class="form-control easyui-validatebox span9"  value="<?php echo $reqPermohonanNotaDinas?> - <?php echo $reqPermohonan?>" readonly />
                <input type="hidden" name="reqPermohonanId" value="<?php echo $reqPermohonanId?>">
                <input type="hidden" name="reqPermohonanUserLogin" value="<?=isset($reqPermohonanUserLogin) ? $reqPermohonanUserLogin : ''?>">
            <?php
            }
            ?>

            <div class="row">
              <div class="form-group col-md-12 mb-2">
                <label>Nama Pengadaan</label>
                <input type="text" name="reqNamaPaket" title="Nama paket harus diisi" class="form-control easyui-validatebox span9"  value="<?=$reqNamaPaket?>" required readonly/>
              </div>
            </div>
            <div class="row">
              <div class="form-group col-md-1 mb-2">
                <label style="width: 100%">Mata Uang</label>
                <input type="text" name="reqMataUang" class="form-control easyui-combobox span2" data-options="valueField:'id',textField:'text',url:'mata_uang_json/comboMataUang'"  value="<?=$reqMataUang?>" style="width:100%" />
              </div>
              <div class="form-group col-md-11 mb-2">
                <label>Harga Perkiraan</label>
                <input title="Nilai pekerjaan harus diisi" class="form-control easyui-validatebox span3"  name="reqNilaiPekerjaan" type="text" id="reqNilaiPekerjaan" value="<?=numberToIna($reqNilaiPekerjaan)?>"  OnFocus="FormatAngka('reqNilaiPekerjaan')" OnKeyUp="FormatUang('reqNilaiPekerjaan')" OnBlur="FormatUang('reqNilaiPekerjaan')" required readonly/>
              </div>
            </div>
            <div class="row">
              <div class="form-group col-md-12 mb-2">
                <label>Lokasi Pekerjaan</label>
                <input title="Lokasi pekerjaan harus diisi" class="form-control easyui-validatebox span3"  name="reqLokasiPekerjaan" type="text" id="reqLokasiPekerjaan" value="<?=isset($reqLokasiPekerjaan) ? $reqLokasiPekerjaan : $this->libbreadcrumb->unitkerja($this->UNIT_KERJA_ID) ?>" required />
              </div>
            </div>
            <div class="row">
              <div class="form-group col-md-12 mb-2">
                <a onClick="openAdd('main/loadUrl/notif/panduan-metode-pemilihan');"> <span class="fa fa-info-circle"></span> Panduan Metode Pemilihan </a>
              </div>
            </div>
            <div class="row">
              <?php
              if ($reqPL != '2')
              { // Bukan Pembelian Langsung / Purchasing
              ?>
              <div class="form-group col-md-3 mb-2">
                <label style="width: 100%">Jenis Pengadaan</label>
                <input type="text" name="reqJenisPekerjaan" class="easyui-combobox span3" id="reqIjinUsaha"
                        data-options="valueField:'id',textField:'text',url:'paket_jenis_json/combo',
                                        onSelect: function(rec){
                                            $('#reqMetodePengadaan').combobox('reload', 'paket_metode_lelang_json/combo/?reqJenisPekerjaan='+rec.id);
                                        }"  value="<?=isset($reqJenisPekerjaan) ? $reqJenisPekerjaan : ''?>" required style="width: 300%"/>
              </div>
              <div class="form-group col-md-3 mb-2">
                <label style="width: 100%">Metode Pengadaan</label>
                <input type="text" name="reqMetodePengadaan" class="easyui-combobox span3"  id="reqMetodePengadaan"
                        data-options="valueField:'id',textField:'text',url:'paket_metode_lelang_json/combo/?reqJenisPekerjaan=<?=isset($reqJenisPekerjaan) ? $reqJenisPekerjaan : ''?>',
                                        onSelect: function(rec){
                                            $('#reqMetodeKualifikasi').combobox('reload', 'paket_metode_kualifikasi_json/combo/?reqMetodePengadaan='+rec.id);
                                            $('#reqMetodeEvaluasi').combobox('reload', 'paket_metode_evaluasi_json/combo2/?reqJenisPekerjaan='+rec.id);
                                            $('#reqMetodePenyampulan').combobox('reload', 'paket_metode_penyampaian_json/combo/?reqJenisPekerjaan='+rec.id);
                                            if(rec.id == '1')
                                            { $('#reqMultiPemenang').show(); } else
                                            { $('#reqMultiPemenang').hide(); };
                                        }"  
                                        value="<?=isset($reqMetodePengadaan) ? $reqMetodePengadaan : ''?>" required style="width: 300%"/>
              </div>
              <div class="form-group col-md-3 mb-2" style="display: none">
                <label style="width: 100%">Metode Kualifikasi</label>
                <input type="text" name="reqMetodeKualifikasi" class="easyui-combobox span3"  id="reqMetodeKualifikasi-old"
                        data-options="valueField:'id',textField:'text',url:'paket_metode_kualifikasi_json/combo/?reqMetodePengadaan=<?=isset($reqMetodePengadaan) ? $reqMetodePengadaan : ''?>'"  value="2" required style="width: 300%"/>
              </div>
              <?php
              } else { // Pembelian Langsung / Purchasing ?>
                <input type="hidden" name="reqJenisPekerjaan" value="<?=isset($reqJenisPekerjaan) ? $reqJenisPekerjaan : ''?>"/> <!-- Barang  -->
                <!-- <input type="text" name="reqMetodePengadaan" value="<?php //isset($reqMetodePengadaan) ? $reqMetodePengadaan : ''?>"/> -->
                <div class="form-group col-md-12 mb-2">
                  <label style="width: 100%">Metode Pengadaan</label>
                  <?php 
                  if ($reqNilaiPekerjaan <= 100000000) {
                     $nilaiK = '1';
                  } else {
                     $nilaiK = '2';
                  } 
                  ?>
                  <input type="text" name="reqMetodePengadaan" class="easyui-combobox span3"
                          data-options="valueField:'id',textField:'text',url:'paket_metode_lelang_json/combokatalog?nilaiK=<?= $nilaiK ?>'"  value="<?= $reqMetodePengadaan?>" required style="width: 300%"/>
                </div>
                <input type="hidden" name="reqMetodeKualifikasi" value="2"/>
                <input type="hidden" name="reqMetodeEvaluasi" value="7"/>  <!-- Harga Terendah  -->
                <input type="hidden" name="reqKualifikasiRekanan" value="<?= $reqKualifikasiRekanan ?>"/>
                <!-- <input type="hidden" name="reqBidangUsahaId" value="" /> -->
              <?php
              } ?>

              <?php
              if ($reqPL != '2') { // Bukan Pembelian Langsung / Purchasing
              ?>
              <div class="form-group col-md-3 mb-2">
                <label style="width: 100%">Metode Penyampaian Penawaran</label>
                <input type="text" name="reqMetodePenyampulan" class="easyui-combobox span4"  id="reqMetodePenyampulan"
                        data-options="valueField:'id',textField:'text',url:'paket_metode_penyampaian_json/combo/?reqJenisPekerjaan=<?=isset($reqMetodePengadaan)?$reqMetodePengadaan:''?>'"  value="<?= isset($reqMetodePenyampulan) ? $reqMetodePenyampulan : ''?>" required style="width: 300%" />
              </div>
              <div class="form-group col-md-3 mb-2">
                <label style="width: 100%">Metode Evaluasi</label>
                <input type="text" name="reqMetodeEvaluasi" class="easyui-combobox span4"  id="reqMetodeEvaluasi"
                        data-options="valueField:'id',textField:'text',url:'paket_metode_evaluasi_json/combo2/?reqJenisPekerjaan=<?=isset($reqMetodePengadaan)?$reqMetodePengadaan:''?>',
                                            onSelect: function(rec){
                                            if ($('#reqMetodeEvaluasi').combobox('getValue') == '2') {
                                              $('#tBobotTeknis').show();
                                              $('#tBobotHarga').show();
                                              $('#tPassingGrade').show();
                                            } else {
                                              $('#tBobotTeknis').hide();
                                              $('#tBobotHarga').hide();
                                              $('#tPassingGrade').hide();
                                            }
                                            //alert($('#reqMetodeEvaluasi').combobox('getValue'));
                                          }"  value="<?=isset($reqMetodeEvaluasi) ? $reqMetodeEvaluasi : ''?>" required style="width: 300%" />
              </div>
              <?php
              } ?>
            </div>

            <?php
            if ($reqPL != '2') { // Pembelian Langsung / Purchasing
            ?>
            <div class="row">
              <!-- <div class="form-group col-md-3 mb-2">
                <label style="width: 100%">Metode Penyampaian Penawaran</label>
                <select name="reqMetodePenyampulan" id="reqMetodePenyampulan" class="easyui-combobox span2" required style="width: 300%">
                  <option value="1" <?php // if($reqMetodePenyampulan == "1") { ?> selected <?php // } ?>>1 File</option>
                  <option value="2" <?php // if($reqMetodePenyampulan == "2") { ?> selected <?php // } ?>>2 File</option>
                </select>
              </div>  -->
              <div class="form-group col-md-2 mb-2">
                <label style="width: 100%">Sistem Negosiasi</label>
                <select name="reqBidding" class="easyui-combobox span2" data-options="
                                        onSelect: function(rec){
                                            if(rec.value == '1')
                                            {
                                                $('#tdBidingMenit').show();
                                                $('#reqBidingMenit').validatebox({
                                                    required: true
                                                });
                                            }
                                            else
                                            {
                                                $('#tdBidingMenit').hide();
                                                $('#reqBidingMenit').validatebox({
                                                    required: false
                                                });
                                            }
                                        }" style="width: 200%">
                <option value="0" <?php if($reqBidding == "0") { ?> selected <?php } ?>>Chatting Nego</option>
                <option value="1" <?php if($reqBidding == "1") { ?> selected <?php } ?>>e-Reverse Auction</option>
                </select>
              </div>
              <div id="tdBidingMenit" <?php if ($reqBidding == '1') {} else { ?> style="display:none" <?php } ?> >
                <!-- <div class="row"> -->
                  <div class="form-group col-md-12 mb-2">
                    <label>Waktu Reverse Auction <small>(menit)</small></label>
                    <input name="reqBidingMenit" id="reqBidingMenit" class="form-control easyui-validatebox span1"
                      type="text" id="reqBidingMenit" value="<?=isset($reqBidingMenit)?$reqBidingMenit:''?>"
                      OnFocus="FormatAngka('reqBidingMenit')"
                      OnKeyUp="FormatUang('reqBidingMenit')"
                      OnBlur="FormatUang('reqBidingMenit')" maxlength="3"
                      <?php if($reqBidding == '1') { ?> required <?php } ?> />
                  </div>
                <!-- </div>  -->
              </div>
              <div class="form-group col-md-3 mb-2">
                <label style="width: 100%">Kualifikasi Usaha</label>
                <?php
                $rekanan_kualifikasi->selectByParams();
                while ($rekanan_kualifikasi->nextRow())
                {
                ?>
                  <input type="radio" name="reqKualifikasiRekanan" title="Kualifikasi rekanan salah satu harus diisi" value="<?= $rekanan_kualifikasi->getField('REKANAN_KUALIFIKASI_ID')?>" id="<?= 'reqKualifikasiRekanan_'.$rekanan_kualifikasi->getField('REKANAN_KUALIFIKASI_ID')?>"  <?php if($reqKualifikasiRekanan == $rekanan_kualifikasi->getField('REKANAN_KUALIFIKASI_ID')) { ?> checked="checked" <?php } ?> />
                  <?= $rekanan_kualifikasi->getField('NAMA')?>
                <?php
                }
                ?>
              </div> 
              <div class="form-group col-md-2 mb-2" id="reqMultiPemenang" <?php if ($reqMetodePengadaan == '1') {} else { ?> style="display:none" <?php } ?>>
                <label style="width: 100%">Pemenang lebih dari satu ?</label>
                  <input type="radio" name="reqMultiPemenang" title="Kontrak Payung" value="0" id=""  <?php if($reqMultiPemenang == "0") { ?> checked="checked" <?php } ?> /> Tidak &nbsp;
                  <input type="radio" name="reqMultiPemenang" title="Kontrak Payung" value="1" id=""  <?php if($reqMultiPemenang == "1") { ?> checked="checked" <?php } ?> /> Ya
              </div>
              <div class="form-group col-md-1 mb-2" id="tBobotTeknis" <?php if ($reqMetodeEvaluasi == '2') {} else { ?> style="display:none" <?php } ?>>
                <label><small style="font-weight: bold">Bobot Teknis</small></label>
                <input title="Bobot Teknis harus diisi" class="form-control easyui-validatebox span3"  name="reqBobotTeknis" type="text" id="reqBobotTeknis" value="<?=$reqBobotTeknis?>"
                      OnFocus="addCommas('reqBobotTeknis')"
                      OnKeyUp="addCommas('reqBobotTeknis')"
                      OnBlur="addCommas('reqBobotTeknis')" maxlength="5"/>
              </div>
              <div class="form-group col-md-1 mb-2" id="tBobotHarga" <?php if ($reqMetodeEvaluasi == '2') {} else { ?> style="display:none" <?php } ?>>
                <label><small style="font-weight: bold">Bobot Harga</small></label>
                <input title="Bobot Harga harus diisi" class="form-control easyui-validatebox span3"  name="reqBobotHarga" type="text" id="reqBobotHarga" value="<?=$reqBobotHarga?>"
                      OnFocus="addCommas('reqBobotHarga')"
                      OnKeyUp="addCommas('reqBobotHarga')"
                      OnBlur="addCommas('reqBobotHarga')" maxlength="5"/>
              </div>
              <div class="form-group col-md-1 mb-2" id="tPassingGrade" <?php if ($reqMetodeEvaluasi == '2') {} else { ?> style="display:none" <?php } ?>>
                <label><small style="font-weight: bold">Passing Grade</small></label>
                <input title="Passing Grade Teknis harus diisi" class="form-control easyui-validatebox span3"  name="reqPassingGrade" type="text" id="reqPassingGrade" value="<?=$reqPassingGrade?>"
                      OnFocus="addCommas('reqPassingGrade')"
                      OnKeyUp="addCommas('reqPassingGrade')"
                      OnBlur="addCommas('reqPassingGrade')" maxlength="5"/>
              </div>
            </div>
            <?php
            } ?>

            <?php
            if ($reqPL != '2')
            { // Bukan Pembelian Langsung / Purchasing
            ?>
            <div class="row">
              <div class="form-group col-md-12 mb-2">
                <label>Persyaratan Peserta</label>
                <textarea id="idGuestBookIsi" name="reqUraianKegiatan" class="textarea-tinymce" style="width:100%; height:350px"><?=isset($reqUraianKegiatan)?$reqUraianKegiatan:''?></textarea>
              </div>
            </div>
            <?php
            } else { ?>
             <div class="row">
                <div class="form-group col-md-12 mb-2">
                  <label>Keterangan</label>
                  <textarea id="idGuestBookIsi" name="reqUraianKegiatan" class="textarea-tinymce" style="width:100%; height:350px"><?=isset($reqUraianKegiatan)?$reqUraianKegiatan:''?></textarea>
                </div>
              </div>
            <?php
            } ?>

            <?php
            if ($reqPL != '2') { // Bukan Pembelian Langsung / Purchasing
            ?>
            <div class="card mb-1 border-blue border-darken-1">
              <div class="card-content">

                <?php
                // if ($reqMetodePengadaan == '1' || $reqMetodePengadaan == '7' || $reqMetodePengadaan == '10') { // 1:Tender Umum, 3-Tender Terbatas, 7:Tender Cepat, 10:Tender Kualifikasi
                //   $nilaiHPS3 = ceil($reqNilaiPekerjaan/3);
                ?>
                <!-- <div class="p-1">
                  <div class="alert alert-danger" style="color: #fff !important">KRITERIA SHORTLIST</div>
                  <label>Nilai Kontrak Pengalaman Sejenis Tertinggi (Minimal)</label>
                  <input title="Nilai pekerjaan harus diisi" class="form-control easyui-validatebox span3"  name="reqNilaiPekerjaanx" type="text" id="reqNilaiPekerjaanx" value="<?php // numberToIna($nilaiHPS3)?>"  OnFocus="FormatAngka('reqNilaiPekerjaanx')" OnKeyUp="FormatUang('reqNilaiPekerjaanx')" OnBlur="FormatUang('reqNilaiPekerjaanx')" required readonly/>
                  <br>
                  <div class="alert bg-primary alert-icon-left alert-arrow-left alert-dismissible mb-2" role="alert">
                    <span class="alert-icon"><i class="fa fa-th"></i></span>  <strong>Bidang Usaha</strong>
                    <div class="badge badge-pill badge-warning">
                        <a onClick="openAdd('main/loadUrl/main/bidang_usaha');">
                          <span class="fa fa-plus text-white font-medium-2 icon-line-height" data-toggle="tooltip" data-placement="top" title="Tambah Bidang Usaha"></span> Tambah</a>
                        </a>
                    </div>
                  </div>
                  <div class="table-responsive">
                    <table class="border-double table mb-0">
                      <tbody id="tbodyBidangUsaha"> -->
                        <?php
                        // $paket_bidang_usaha = new PaketBidangUsaha();
                        // $paket_bidang_usaha->selectByParams(array("PAKET_ID" => coalesce($reqId, 0)));
                        // while($paket_bidang_usaha->nextRow())
                        // {
                        ?>
                        <!-- <tr>
                          <td class="attrName" width="75%"><?php // $paket_bidang_usaha->getField("NAMA")?></td>
                          <td class="attrValue" align="center" width="5%">
                            <input type="hidden" name="reqBidangUsahaId[]" value="<?php //$paket_bidang_usaha->getField("BIDANG_USAHA_ID")?>" />
                            <a title="#" onclick="$(this).parent().parent().remove();" class="btn-aksi"><i class="fa fa-trash" aria-hidden="true"></i></a>
                          </td>
                          <td style="width: 15%"> -->
                          <?php
                            // $paket_bidang_usaha_check = new PaketBidangUsaha();
                            // if ($reqKualifikasiRekanan == '3') { // 1:Kecil 2:Non-kecil 3:kecil/non-kecil
                            //   $paket_bidang_usaha_check->selectCountByParamsBidangUsahaSortlist(array("a.BIDANG_USAHA_ID" => $paket_bidang_usaha->getField("BIDANG_USAHA_ID"), "a.NILAI|| >= " => $nilaiHPS3));
                            // } else { // sesuai kualifikasi usaha
                            //   $paket_bidang_usaha_check->selectCountByParamsBidangUsahaSortlist(array("a.BIDANG_USAHA_ID" => $paket_bidang_usaha->getField("BIDANG_USAHA_ID"), 'a.REKANAN_KUALIFIKASI_ID' => $reqKualifikasiRekanan, "a.NILAI|| >= " => $nilaiHPS3));
                            // }
                            // $paket_bidang_usaha_check->firstRow();
                            // $totalPenyedia = $paket_bidang_usaha_check->getField("COUNT") ? $paket_bidang_usaha_check->getField("COUNT") : 0;
                            // if ($totalPenyedia==0) {
                            //   echo '<span class="badge badge-danger" style="padding:5px 10px">'.$totalPenyedia.'</span> Penyedia';
                            // } else {
                            //   echo '<a onClick="openAdd(\'main/loadUrl/main/bidang_usaha_checkSortlist?id='.$paket_bidang_usaha->getField("BIDANG_USAHA_ID").'|-|'.$reqKualifikasiRekanan.'|-|'.$nilaiHPS3.'\');"><span class="badge badge-primary" style="padding:5px 10px">'.$totalPenyedia.'</span> Penyedia</a>';
                            // }
                           ?>
                          <!-- </td>
                        </tr> -->
                        <?php
                        // }
                        ?>
                      <!-- </tbody>
                    </table>
                  </div>
                </div> -->
                <?php
                // } else { // 2:Pengadaan Langsung, 5:Penunjukan Langsung  ?>
                <div class="p-1">
                  <div class="alert alert-danger" style="color: #fff !important">Input Bidang Usaha Sesuai dengan Paket Pengadaan</div>
                  <div class="alert bg-primary alert-icon-left alert-arrow-left alert-dismissible mb-2" role="alert">
                    <span class="alert-icon"><i class="fa fa-th"></i></span>  <strong>Bidang Usaha</strong>
                    <div class="badge badge-pill badge-warning">
                        <a onClick="openAdd('main/loadUrl/main/bidang_usaha_all');">
                          <span class="fa fa-plus text-white font-medium-2 icon-line-height" data-toggle="tooltip" data-placement="top" title="Tambah Bidang Usaha"></span> Tambah</a>
                        </a>
                    </div>
                    <!-- <a onClick="openAdd('main/loadUrl/main/bidang_usaha');"> <span class="fa fa-plus text-white font-medium-2 icon-line-height" data-toggle="tooltip" data-placement="top" title="Tambah Bidang Usaha"></span> </a> -->
                  </div>
                  <div class="table-responsive">
                    <table class="border-double table mb-0">
                      <tbody id="tbodyBidangUsaha">
                        <?php
                        $paket_bidang_usaha = new PaketBidangUsaha();
                        $paket_bidang_usaha->selectByParams(array("PAKET_ID" => coalesce($reqId, 0)));
                        while($paket_bidang_usaha->nextRow())
                        {
                        ?>
                        <tr>
                          <td class="attrName" width="75%"><?=$paket_bidang_usaha->getField("NAMA")?></td>
                          <td class="attrValue" align="center" width="5%">
                            <input type="hidden" name="reqBidangUsahaId[]" value="<?=$paket_bidang_usaha->getField("BIDANG_USAHA_ID")?>" />
                            <a title="#" onclick="$(this).parent().parent().remove();" class="btn-aksi"><i class="fa fa-trash" aria-hidden="true"></i></a>
                          </td>
                          <td style="width: 15%">
                          <?php
                            $paket_bidang_usaha_check = new PaketBidangUsaha();
                            if ($reqKualifikasiRekanan == '3') { // 1:Kecil 2:Non-kecil 3:kecil/non-kecil
                              $paket_bidang_usaha_check->selectCountByParamsBidangUsaha(array("a.BIDANG_USAHA_ID" => $paket_bidang_usaha->getField("BIDANG_USAHA_ID")));
                            } else {
                              $paket_bidang_usaha_check->selectCountByParamsBidangUsaha(array("a.BIDANG_USAHA_ID" => $paket_bidang_usaha->getField("BIDANG_USAHA_ID"), 'b.REKANAN_KUALIFIKASI_ID' => $reqKualifikasiRekanan));
                            }
                            $paket_bidang_usaha_check->firstRow();
                            $totalPenyedia = $paket_bidang_usaha_check->getField("COUNT") ? $paket_bidang_usaha_check->getField("COUNT") : 0;
                            if ($totalPenyedia==0) {
                              echo '<span class="badge badge-danger" style="padding:5px 10px">'.$totalPenyedia.'</span> Penyedia';
                            } else {
                              echo '<a onClick="openAdd(\'main/loadUrl/main/bidang_usaha_check?id='.$paket_bidang_usaha->getField("BIDANG_USAHA_ID").'|-|'.$reqKualifikasiRekanan.'\');"><span class="badge badge-primary" style="padding:5px 10px">'.$totalPenyedia.'</span> Penyedia</a>';
                            }
                           ?>
                          </td>
                        </tr>
                        <?php
                        }
                        ?>
                      </tbody>
                    </table>
                  </div>
                </div>
                <?php
                // } ?>

              </div>
            </div>
            <?php
            } ?>

             
            <div class="form-actions">
              <input type="hidden" name="reqId" value="<?=$reqId?>">
              <input type="hidden" name="reqBahasa" value="ID">
              <?php 
              if($cekPermohonanCount == 0 && $reqId == "")
              { ?>
              <a href="main/index/permohonan_paket_panitia" class="<?= CLASS_BTN_DANGER ?> mr-1 text-white"> <?= BTN_KEMBALI ?> </a>
              <?php 
              } else { ?>
              <a href="main/index/paket_detil/?reqId=<?=$reqId?>" class="<?= CLASS_BTN_DANGER ?> mr-1 text-white"> <?= BTN_KEMBALI ?> </a>
              <?php 
              } ?>
              <!-- <a href="<?php //if($reqId == "") { ?>main/index/paket_lelang<? //} else { ?>main/index/paket_detil/?reqId=<?php //$reqId?><?php //} ?>" class="btn btn-danger mr-1 text-white"> <?php // BTN_KEMBALI ?> </a>  -->
              <button type="submit" class="<?= CLASS_BTN_PRIMARY ?> mr-1"><?= BTN_SIMPAN ?></button>
              <?php
              if ($reqPL != '2')
              {
                if($cekPermohonanCount == 0 && $reqId == "")
                {} else
                {?>
                  <a href="main/index/paket_lelang_tambah_rincian_pekerjaan/?reqId=<?=$reqId?>" class="<?= CLASS_BTN_PRIMARY ?> pull-right mr-1"><?= BTN_LANJUT ?></a>
              <?php
                }
              }
              ?>
            </div>

          </form>
        </div>
      </div>
    </div>
  </div>
</div>
