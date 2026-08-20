<?php
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 *
 */ 
$this->libsession->cekSession();

include_once("functions/string.func.php");
include_once("functions/date.func.php");
include_once("functions/default.func.php");

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
$reqPermohonanId = $this->input->get("reqPermohonanId") ?: '0';

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
    $reqLokasiPekerjaan = $permohonan_paket->getField("LOKASI_PEKERJAAN");
    // $reqNilaiPekerjaan = $permohonan_paket->getField("NILAI");
    $reqNilaiPekerjaan = $permohonan_paket->getField("NILAI_HPS_PR") ?: $permohonan_paket->getField("NILAI");
    $reqNilaiPekerjaanPermohonan = ceil($permohonan_paket->getField("NILAI"));
    $reqPL = $permohonan_paket->getField("PENGADAANLANGSUNG");
    $reqPermohonanKeterangan = $permohonan_paket->getField("KETERANGAN");
    $reqKodeRUP = $permohonan_paket->getField("KODE_RUP");
    $reqKodePR = $permohonan_paket->getField("KODE_PR");
    $reqStrategiPengadaan = $permohonan_paket->getField("STRATEGI_PENGADAAN");
    $reqJenisPekerjaan = $permohonan_paket->getField("NAMA_JENIS_PEKERJAAN");
    switch ($reqJenisPekerjaan) {
      case 'Pekerjaan Konstruksi':
        $reqJenisPekerjaan = '1';
        break;
      case 'Jasa Konsultansi':
        $reqJenisPekerjaan = '2';
        break;
      case 'Barang':
        $reqJenisPekerjaan = '3';
        break;
      case 'Jasa Lainnya':
        $reqJenisPekerjaan = '4';
        break; 
      default:
        break;
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
 
  $reqUUID = $paket->getField("PAKET_UUID");
  $reqPublishPaket = $paket->getField("PUBLISH_PAKET");
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
  $reqNilaiPekerjaanPermohonan = ceil($paket->getField("NILAI_PERMOHONAN"));
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
    $reqKodeRUP = $permohonan_paket->getField("KODE_RUP");
    $reqKodePR = $permohonan_paket->getField("KODE_PR");
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
  $reqMultiBidangUsaha = $paket->getField("MULTI_BIDANG_USAHA");
  //echo "dds".$reqBidingMenit;exit;
}

?>

<script type="text/javascript">
 
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
          if (data == 'Data Gagal Tersimpan') {
            alertError3(data);
          } else {
            alertSuccess2('Data berhasil disimpan');
            setTimeout(function() {
              location.reload();
            }, 2000);
          }
        }
      });

    });

  }); 

function createRowNotaDinas()
{
  $(function () {
    $.get("main/loadUrl/main/permohonan_lelang_add_nota_dinas_template", function (data) {
      $("#tbodyPermohonanPaketFile").append(data);
    });
  });
}

$('#reqMetodeEvaluasi')
.on('change', function(){
    alert($('#reqMetodeEvaluasi option:selected').val());
});

function CekHPSMaksimal(id)
{
  var txtHPS = document.getElementById(id);
  var numHPS = txtHPS.value;
  totalHPS = numHPS.replaceAll(".","");
  totalHPS = numHPS.replaceAll(",","");
  var txtMaks = document.getElementById('reqHPSMaksimal');
  var numMaks = txtMaks.value;
  totalMaks = numMaks.replaceAll(".","");
  totalMaks = numMaks.replaceAll(",","");

  converttotalHPS = parseInt(Math.ceil(totalHPS))
  converttotalMaks = parseInt(Math.ceil(totalMaks));
  // alert(converttotalHPS+'---'+converttotalMaks);

  if (converttotalHPS > converttotalMaks) {
    alertError3('Harga Perkiraan Sendiri tidak boleh diatas Harga Perkiraan Sendiri yang sudah di tetapkan yaitu '+formatNumber(numMaks)) ;
    txtHPS.value = numMaks;
  }
  // alert(numHPS+'--'+numMaks);
}

function formatNumber(num) {
  var str = num.toString().replace("$", ""), parts = false, output = [], i = 1, formatted = null;
  if(str.indexOf(",") > 0) {
    parts = str.split(",");
    str = parts[0];
  }
  str = str.split("").reverse();
  for(var j = 0, len = str.length; j < len; j++) {
    if(str[j] != ".") {
      output.push(str[j]);
      if(i%3 == 0 && j < (len - 1)) {
        output.push(".");
      }
      i++;
    }
  }
  formatted = output.reverse().join("");
  return (formatted + ((parts) ? "," + parts[1].substr(0, 2) : ""));
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
        <h4 class="card-title text-white">
          <?php
          if($cekPermohonanCount == 0 && $reqId == "")
          { echo 'Tambah Pengadaan'; } else { 
            echo 'Edit Pengadaan  <a class="'.CLASS_BTN_INFO.'" onclick="openAdd(\'main/loadUrl/main/permohonan_lelang_panitia_add/?reqId='.$reqPermohonanId.'\')"> Lihat Detail Permohonan</a>'; 
          } ?>

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
                <input type="hidden" name="reqNamaPaket" class="form-control easyui-validatebox span9"  value="<?php echo $reqPermohonanNotaDinas?> - <?php echo $reqPermohonan?>" readonly />
                <input type="hidden" name="reqPermohonanId" value="<?php echo $reqPermohonanId?>">
                <input type="hidden" name="reqPermohonanUserLogin" value="<?=isset($reqPermohonanUserLogin) ? $reqPermohonanUserLogin : ''?>">
            <?php
            }
            ?>

            <div class="row">
              <div class="form-group col-md-12 mb-2">
                <label>Nama Pengadaan</label>
                <input type="text" name="reqNamaPaket" title="Nama paket harus diisi" class="form-control easyui-validatebox span9"  value="<?=$reqNamaPaket?>" readonly/>
              </div>
            </div>
            <div class="row">
              <div class="form-group col-md-1 mb-2">
                <label style="width: 100%">Mata Uang</label>
                <input type="text" name="reqMataUang" class="form-control easyui-combobox span2" data-options="valueField:'id',textField:'text',url:'mata_uang_json/comboMataUang'"  value="<?=$reqMataUang?>" style="width:105%" />
              </div>
              <div class="form-group col-md-11 mb-2">
                <label>Harga Perkiraan Sendiri</label>
                <input title="Nilai pekerjaan harus diisi" class="form-control span9 easyui-validatebox"  name="reqNilaiPekerjaan" type="text" id="reqNilaiPekerjaan" value="<?=number_format($reqNilaiPekerjaan)?>"  readonly pattern="^\$\d{1,3}(,\d{3})*(\.\d+)?$" OnBlur="CekHPSMaksimal('reqNilaiPekerjaan')" data-type="currency"/>
                <!-- <sup><i>gunakan tanda titik untuk decimal </i> (contoh: 89,000.50)</sup> -->

                <input class="form-control"  name="reqNilaiPekerjaanPermohonan" type="hidden" id="reqHPSMaksimal" value="<?=$reqNilaiPekerjaanPermohonan?>" readonly/>
              </div>
            </div>
            <div class="row">
              <div class="form-group col-md-12 mb-2">
                <label>Lokasi Pekerjaan</label>
                <input title="Lokasi pekerjaan harus diisi" class="form-control easyui-validatebox span3"  name="reqLokasiPekerjaan" type="text" id="reqLokasiPekerjaan" value="<?=isset($reqLokasiPekerjaan) ? $reqLokasiPekerjaan : $reqPermohonan ?>" readonly />
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
                                        }"  value="<?=isset($reqJenisPekerjaan) ? $reqJenisPekerjaan : ''?>" style="width: 300%" <?php if ($reqPublishPaket == '1') { echo "readonly"; } ?>/>
              </div>
              <div class="form-group col-md-3 mb-2">
                <label style="width: 100%">Metode Pengadaan</label>
                <input type="text" name="reqMetodePengadaan" class="easyui-combobox span3"  id="reqMetodePengadaan"
                        data-options="valueField:'id',textField:'text',url:'paket_metode_lelang_json/combo/?reqJenisPekerjaan=<?=isset($reqJenisPekerjaan) ? $reqJenisPekerjaan : ''?>',
                                        onSelect: function(rec){
                                            $('#reqMetodeKualifikasi').combobox('reload', 'paket_metode_kualifikasi_json/combo/?reqMetodePengadaan='+rec.id);
                                            $('#reqMetodeEvaluasi').combobox('reload', 'paket_metode_evaluasi_json/combo2/?reqJenisPekerjaan='+rec.id);
                                            $('#reqMetodePenyampulan').combobox('reload', 'paket_metode_penyampaian_json/combo/?reqJenisPekerjaan='+rec.id);
                                        }"
                                        value="<?=isset($reqMetodePengadaan) ? $reqMetodePengadaan : ''?>" required style="width: 300%" <?php if ($reqPublishPaket == '1') { echo "readonly"; } ?>/>
              </div>
              <input type="hidden" name="reqMetodeKualifikasi" value="2"/> <!-- Ik1026  -->
              <?php
              } else { // Pembelian Langsung / Purchasing / Pengadaan Langsung ?>
                <div class="form-group col-md-3 mb-2">
                  <label style="width: 100%">Metode Pengadaan</label> 
                  <input type="text" name="reqMetodePengadaan" class="easyui-combobox span3"
                          data-options="valueField:'id',textField:'text',url:'paket_metode_lelang_json/combokatalog?nilaiK=<?= $nilaiK ?>', onSelect: function(rec){
                                          if(rec.id == 2) {
                                            $('#displayJenisPengadaan').show(); $('#displaySistemNegosiasi').show(); $('#displayKualifikasiUsaha').show();
                                          } else {
                                            $('#displayJenisPengadaan').hide(); $('#displaySistemNegosiasi').hide(); $('#displayKualifikasiUsaha').hide();
                                          }
                                        }"  value="<?= $reqMetodePengadaan?>" required style="width: 300%"/>
                </div>
                <div class="form-group col-md-3 mb-2" style="<?php if($reqMetodePengadaan == '2') {}else { ?>display:none <?php } ?>" id="displayJenisPengadaan">
                  <label style="width: 100%">Jenis Pengadaan</label>
                  <input type="text" name="reqJenisPekerjaan" class="easyui-combobox span3" id="reqIjinUsaha"
                          data-options="valueField:'id',textField:'text',url:'paket_jenis_json/combo'"  value="<?=isset($reqJenisPekerjaan) ? $reqJenisPekerjaan : ''?>" required style="width: 300%"/>
                </div> 
                <input type="hidden" name="reqMetodeKualifikasi" value="2"/>
                <input type="hidden" name="reqMetodePenyampulan" value="1"/>
                <input type="hidden" name="reqMetodeEvaluasi" value="7"/>  <!-- Harga Terendah  -->
                <input type="hidden" name="reqKualifikasiRekanan" value="<?= $reqKualifikasiRekanan ?>"/>
                <input type="hidden" name="reqBidding" value="0"/> <!-- Negosiasi bukan Auction -->
                <!-- <input type="hidden" name="reqBidangUsahaId" value="" /> --> 
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
              <?php
              } ?>

              <?php
              if ($reqPL != '2') { // Bukan Pembelian Langsung / Purchasing
              ?>
              <div class="form-group col-md-3 mb-2">
                <label style="width: 100%">Metode Penyampaian Penawaran</label>
                <input type="text" name="reqMetodePenyampulan" class="easyui-combobox span4"  id="reqMetodePenyampulan"
                        data-options="valueField:'id',textField:'text',url:'paket_metode_penyampaian_json/combo/?reqJenisPekerjaan=<?=isset($reqMetodePengadaan)?$reqMetodePengadaan:''?>'"  value="<?= isset($reqMetodePenyampulan) ? $reqMetodePenyampulan : ''?>" required style="width: 300%" <?php if ($reqPublishPaket == '1') { echo "readonly"; } ?>/>
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
                                            } else if ($('#reqMetodeEvaluasi').combobox('getValue') == '10') {
                                              $('#tBobotTeknis').show();
                                              $('#tBobotHarga').hide();
                                              $('#tPassingGrade').show();
                                            } else {
                                              $('#tBobotTeknis').hide();
                                              $('#tBobotHarga').hide();
                                              $('#tPassingGrade').hide();
                                            }
                                            //alert($('#reqMetodeEvaluasi').combobox('getValue'));
                                          }"  value="<?=isset($reqMetodeEvaluasi) ? $reqMetodeEvaluasi : ''?>" required style="width: 300%" <?php if ($reqPublishPaket == '1') { echo "readonly"; } ?>/>
              </div>
              <?php
              } ?>
            </div>

            <?php
            if ($reqPL != '2') { // Pembelian Langsung / Purchasing
            ?>
            <input type="hidden" name="reqBidding" value="0"/> <!-- Negosiasi bukan Auction -->

            <div class="row"> 
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

              <input type="hidden" name="reqMultiPemenang" value="0" />
              <input type="hidden" name="reqKualifikasiRekanan" value="3"/>
               
              <div class="form-group col-md-1 mb-2" id="tBobotTeknis" <?php if ($reqMetodeEvaluasi == '2' || $reqMetodeEvaluasi == '10') {} else { ?> style="display:none" <?php } ?>>
                <label><small style="font-weight: bold">Bobot Teknis</small></label>
                <input title="Bobot Teknis harus diisi" class="form-control easyui-validatebox span3"  name="reqBobotTeknis" type="text" id="reqBobotTeknis" value="<?=$reqBobotTeknis?>"
                      OnFocus="addCommas('reqBobotTeknis')"
                      OnKeyUp="addCommas('reqBobotTeknis')"
                      OnBlur="addCommas('reqBobotTeknis')" maxlength="5" <?php if ($reqPublishPaket == '1') { echo "readonly"; } ?>/>
              </div>
              <div class="form-group col-md-1 mb-2" id="tBobotHarga" <?php if ($reqMetodeEvaluasi == '2') {} else { ?> style="display:none" <?php } ?>>
                <label><small style="font-weight: bold">Bobot Harga</small></label>
                <input title="Bobot Harga harus diisi" class="form-control easyui-validatebox span3"  name="reqBobotHarga" type="text" id="reqBobotHarga" value="<?=$reqBobotHarga?>"
                      OnFocus="addCommas('reqBobotHarga')"
                      OnKeyUp="addCommas('reqBobotHarga')"
                      OnBlur="addCommas('reqBobotHarga')" maxlength="5" <?php if ($reqPublishPaket == '1') { echo "readonly"; } ?>/>
              </div>
              <div class="form-group col-md-2 mb-2" id="tPassingGrade" <?php if ($reqMetodeEvaluasi == '2' || $reqMetodeEvaluasi == '10') {} else { echo 'style="display:none"'; } ?>>
                <label><small style="font-weight: bold">Ambang Batas</small></label>
                <input title="Passing Grade Teknis harus diisi" class="form-control easyui-validatebox span3"  name="reqPassingGrade" type="text" id="reqPassingGrade" value="<?=$reqPassingGrade?>"
                      OnFocus="addCommas('reqPassingGrade')"
                      OnKeyUp="addCommas('reqPassingGrade')"
                      OnBlur="addCommas('reqPassingGrade')" maxlength="5" <?php if ($reqPublishPaket == '1') { echo "readonly"; } ?>/>
              </div>
            </div>
            <?php
            } ?>

            <input type="hidden" name="reqUraianKegiatan" value="">

            <div class="form-actions">
              <input type="hidden" name="reqId" value="<?=$reqId?>">
              <input type="hidden" name="reqBahasa" value="ID">
              <?php
              if($cekPermohonanCount == 0 && $reqId == "")
              { 
                $btnSimpan = BTN_SIMPAN;
                ?>
              <a href="kontrak/index/contracting_bypass" class="<?= CLASS_BTN_DANGER ?> mr-1 text-white"> <?= BTN_KEMBALI ?> </a>
              <?php
              } else { 
                $btnSimpan = BTN_UBAH;
                ?>
              <a href="kontrak/index/paket_detil_bypass/?eid=<?=$reqId?>&key=<?=$reqUUID?>" class="<?= CLASS_BTN_DANGER ?> mr-1 text-white"> <i class="fa fa-arrow-left"></i> Lengkapi data lainnya </a>
              <?php
              } 
                if ($reqPublishPaket == '1') { } else { 
                ?>
              <button type="submit" class="<?= CLASS_BTN_PRIMARY ?> mr-1"><?= $btnSimpan ?></button>
              <?php
                }
              if ($reqPL != '2')
              {
                if($cekPermohonanCount == 0 && $reqId == "")
                {} else
                {?>
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
