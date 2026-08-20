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

$this->load->library("kauth");  $userLogin = new kauth();
$this->load->library("libapi");
$this->load->library("paketinfo"); $paketInfo = new paketinfo();
$this->load->model("PermohonanPaket");
$this->load->model("PermohonanPaketAnalisaFile");
$this->load->library("FileHandler");
include_once("functions/string.func.php");
include_once("functions/date.func.php");
include_once("functions/default.func.php");

$libapi = new libapi();
$url = URL_API_AGNES.'/mataanggaran';
// $date = date('Y');
$department = $this->DEPARTMENT;

$usulanId= $this->input->get("usulanId");
$permohonan_paket_analisa = new PermohonanPaket();
$permohonan_paket_anggaran = new PermohonanPaket();
// $permohonan_paket_analisa_file = new PermohonanPaketAnalisaFile();
$file = new FileHandler();

if($usulanId == '')
{
  $date = isset($_GET['tahun']) ? $_GET['tahun'] : date('Y');
  $reqMode ='insert';
  $editKegiatan = '';
  $reqRencanaPengadaanEx = date('m-Y');
  $reqWaktuPenggunaBarangjasaEx = date('m-Y');
}
else
{
  $reqMode = 'update';
  $permohonan_paket_analisa->selectByParamsUsulan(array("A.PERMOHONAN_PAKET_ANALISA_ID" => coalesce($usulanId, 0), "A.CREATED_BY" => $this->USER_LOGIN_ID));
  $permohonan_paket_analisa->firstRow();

  $date = isset($_GET['tahun']) ? $_GET['tahun'] : (int)$permohonan_paket_analisa->getField("TAHUN_ANGGARAN");
  $reqPermohonanPaketId = $permohonan_paket_analisa->getField("PERMOHONAN_PAKET_ID");
  $reqPermohonanPaketAnalisaId = $permohonan_paket_analisa->getField("PERMOHONAN_PAKET_ANALISA_ID");
  $reqTahunAnggaran = $date ?: $permohonan_paket_analisa->getField("TAHUN_ANGGARAN");
  $reqKomoditasId = $permohonan_paket_analisa->getField("KOMODITAS_ID");
  $reqKomoditasNama = $permohonan_paket_analisa->getField("KOMODITAS_NAMA");
  $reqNamaKebutuhan = $permohonan_paket_analisa->getField("NAMA_KEBUTUHAN");
  $reqAnalisaKebutuhan = $permohonan_paket_analisa->getField("ANALISA_KEBUTUHAN_ID");
  $reqNamaKebutuhanNama = $permohonan_paket_analisa->getField("AK_NAMA");
  $reqAnalisaPasar = $permohonan_paket_analisa->getField("ANALISA_PASAR_ID");
  $reqAnalisaPasarNama = $permohonan_paket_analisa->getField("AP_NAMA");
  $reqIdentifikasiResiko = $permohonan_paket_analisa->getField("IDENTIFIKASI_RESIKO");
  $reqIdentifikasiResikoKeterangan = $permohonan_paket_analisa->getField("IDENTIFIKASI_RESIKO_KETERANGAN");
  $reqPembuat = $permohonan_paket_analisa->getField("PEMBUAT");
  $reqTanggalBuat = $permohonan_paket_analisa->getField("TANGGAL_BUAT");
  $reqPejabatBerwenang = $permohonan_paket_analisa->getField("PEJABAT_BERWENANG");
  $reqAlasanTolak = $permohonan_paket_analisa->getField("ALASAN_TOLAK");
  $reqAlasanTolakBy = $permohonan_paket_analisa->getField("ALASAN_TOLAK_BY");
  $reqAlasanTolakDate = $permohonan_paket_analisa->getField("ALASAN_TOLAK_DATE");
  $reqApproval = $permohonan_paket_analisa->getField("APPROVAL");
  $reqPublish = $permohonan_paket_analisa->getField("PUBLISH");
  $reqPublishDate = $permohonan_paket_analisa->getField("PUBLISH_DATE");
  $reqPermohonanPaketAnalisaIdEn = $permohonan_paket_analisa->getField("PERMOHONAN_PAKET_ANALISA_ID_ENCRYPT");
  $reqNamaPaket = $permohonan_paket_analisa->getField("NAMA");
  $reqAnggaran = $permohonan_paket_analisa->getField("ANGGARAN");
  $reqJenisBarangJasa = $permohonan_paket_analisa->getField("JENIS_BARANG_JASA");
  $reqNilai = $permohonan_paket_analisa->getField("NILAI");
  // echo $reqNilai; die;
  $reqPerkiraanBiayaHarga = $permohonan_paket_analisa->getField("PERKIRAAN_BIAYA_HARGA");
  // $reqWaktuPenggunaBarangjasa = dateToPageCheck($permohonan_paket_analisa->getField("WAKTU_PENGGUNA_BARANGJASA"));
  // $reqRencanaPengadaan = dateToPageCheck($permohonan_paket_analisa->getField("RENCANA_PENGADAAN"));
  // extract date
  $wpbj = explode('-',$permohonan_paket_analisa->getField("WAKTU_PENGGUNA_BARANGJASA"));
  $wpbj2 = $wpbj[1].'-'.$wpbj[0];
  $reqWaktuPenggunaBarangjasa = $wpbj2;
  $reqWaktuPenggunaBarangjasaMonth = $wpbj[1];
  $reqWaktuPenggunaBarangjasaYear = $wpbj[0];
  $rp = explode('-',$permohonan_paket_analisa->getField("RENCANA_PENGADAAN"));
  $rp2 = $rp[1].'-'.$rp[0];
  $reqRencanaPengadaan = $rp2;
  $reqRencanaPengadaanMonth = $rp[1];
  $reqRencanaPengadaanYear = $rp[0];
  $reqCaraPengadaan = $permohonan_paket_analisa->getField("CARA_PENGADAAN");
  $reqAnalisaKategori = $permohonan_paket_analisa->getField("KATEGORI");
  $reqAnalisaJenisBelanja = $permohonan_paket_analisa->getField("JENIS_BELANJA");
  $reqSumberDanaKeterangan = $permohonan_paket_analisa->getField("SUMBER_DANA_KETERANGAN");
  $reqNote = $permohonan_paket_analisa->getField("NOTE");

  // Anggaran
  $permohonan_paket_anggaran->selectByParamsAnggaran(array("A.PERMOHONAN_PAKET_ID" => coalesce($reqPermohonanPaketId, 0), "A.CREATED_BY" => $this->USER_LOGIN_ID));
  $permohonan_paket_anggaran->firstRow();
  $reqPermohonanPaketAnggaranId = $permohonan_paket_anggaran->getField("PERMOHONAN_PAKET_ANGGARAN_ID");
  // $reqPermohonanPaketId = $permohonan_paket_anggaran->getField("PERMOHONAN_PAKET_ID");
  $reqMataAnggaran = $permohonan_paket_anggaran->getField("MATA_ANGGARAN");
  $reqKegiatan = $permohonan_paket_anggaran->getField("KEGIATAN");
  $reqSumberDana = $permohonan_paket_anggaran->getField("SUMBER_DANA");
  $reqBudgetRemaining = $permohonan_paket_anggaran->getField("BUDGET_REMAINING");
  $reqDepartment = $permohonan_paket_anggaran->getField("DEPARTMENT");
  $reqDepartmentCode = $permohonan_paket_anggaran->getField("DEPARTMENT_CODE");
  $reqKodeMataAnggaran = $permohonan_paket_anggaran->getField("KODE_MATA_ANGGARAN");
  $reqKodeKegiatan = $permohonan_paket_anggaran->getField("KODE_KEGIATAN");
  $reqTotalBudget = $permohonan_paket_anggaran->getField("TOTAL_BUDGET");
  $reqTipeTransaksi = $permohonan_paket_anggaran->getField("TIPE_TRANSAKSI");

  // untuk ambil kegiatan form edit
  $libapii = new libapi();
  $editKegiatan = $libapii->getA($url,$date,$department,$reqMataAnggaran,$reqKegiatan);

}
 // echo $reqTahunAnggaran; die;
?>

<style type="text/css">
#reqMataAnggaran { width: 65% !important;}
/*#reqKegiatan { width: 45% !important;}  */
@media (max-width: 991.98px)
{
  #reqMataAnggaran, #reqKegiatan { width: 50% !important;}
}
</style>

<script type="text/javascript">

$(document).ready(function() {
  $(function(){
    $('#ff').form({
      url:'permohonan_paket_usulan_json/permohonan_usulan_add',
      onSubmit:function(){

        // var msgAnalisa = $('#reqAnalisaKebutuhan').combobox('getValue');
        // if (msgAnalisa == '2') { // Sumber Dana External wajib isi text
        //   var msgSumber = $('#reqSumberDanaKeterangan').val();
        //   var textmsgSumber = $.trim($(msgSumber).text());
        //   if (textmsgSumber == '') {
        //     alertError2('Kerangan Sumber Dana dari wajib di isi');
        //     return false;
        //   }
        // }

        var msg = $('#reqNote').val();
        var textmsg = $.trim($(msg).text());
        if (textmsg == '') {
          alertError2('Catatan / Keterangan Tambahan wajib di isi');
          return false;
        }

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
        alertSuccess2(data);
        // hideLoad();
        $("#ff").trigger("reset");
        // console.log(data);return false;
        //$.messager.alert('Info', data, 'info');
        setTimeout(function () { 
          <?php
            if ($reqMode =='insert') {?>
            document.location.href = 'main/index/permohonan_paket_usulan';
            <?php
            } else { ?>
            document.location.href = 'main/index/permohonan_paket_usulan/?usulanId=<?= $usulanId ?>';
            <?php
            } ?>
        }, 2000);
      }
    });
  });

  $('#idIdentifikasiResiko, #idSumberDanaText').hide();

  <?php
  if($usulanId) {
    if ($reqIdentifikasiResiko == '1') {
      echo  '$(\'#idIdentifikasiResiko\').show();';
    }

    if ($reqAnalisaKebutuhan == '2') {
      echo  '$(\'#idSumberDanaText\').show();';
    }


  }  ?>
  $(function(){
    $('input[name="reqIdentifikasiResiko"]').on('change', function() {
        var radioValue = $('input[name="reqIdentifikasiResiko"]:checked').val();
        if(radioValue == "1")
        {
          $('#idIdentifikasiResiko').show();
        } else {
          $('#idIdentifikasiResiko').hide();
        }
    });
  });

  // $('#reqTanggal, #reqTanggal2').datebox({
  // $('#reqTanggal').datebox({
  //   editable: false
  // });

});

function reloadAA(aa){ 
  <?php 
  if($usulanId == '')
  { ?>
  var valnya = '<?= $urlReload = base_url('main/index/permohonan_paket_usulan_add?tahun='); ?>';
  <?php 
  } else { ?>
  var valnya = '<?= $urlReload = base_url('main/index/permohonan_paket_usulan_add?usulanId='.$usulanId.'&tahun='); ?>';
  <?php 
  } ?>
  window.location = valnya+aa;
}

function createRowNotaDinas()
{
  $(function () {
    $.get("main/loadUrl/main/permohonan_lelang_add_nota_dinas_template", function (data) {
      $("#tbodyPermohonanPaketAnalisaFile").append(data);
    });
  });
}

// COA
function createRowCOA()
{
  $(function () {
    $.get("main/loadUrl/main/permohonan_lelang_add_nota_dinas_coa_template", function (data) {
      $("#tbodyPermohonanCOA").append(data);
    });
  });
}

function calculate(no)
{
    awal = document.getElementById('reqBudgetAwal'+no).value;
    pakai = document.getElementById('reqBudgetTerpakai'+no).value;

    awalParsing = parseFloat(awal.split('.').join(""));
    pakaiParsing = parseFloat(pakai.split('.').join(""));
    total = awalParsing - pakaiParsing;
    $('#reqBudgetAkhir'+no).val(FormatNumberya(total));
}
function FormatNumberya(id)
{
   var a = parseFloat(id);
   var nilai = FormatCurrency(a);
   return nilai;
}

// $('#reqAnalisaKebutuhan')
// .on('change', function(){
//     alert('aa');
// });

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

// -----------

</script>

<div class="row">
  <div class="col-md-12 col-sm-12">
    <div class="card">
      <div class="card-header card-head-inverse bg-primary">
        <h4 class="card-title text-white">Permohonan Paket Usulan dan Analisa Kebutuhan
          <?php
          if ($usulanId) {
            $this->load->library("librekamjejak"); $librekamjejak = new librekamjejak();
            echo $librekamjejak->buttonRJ($reqPermohonanPaketId); }
          ?>
        </h4>
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
          <?php
          if($reqAlasanTolak == '')
          {}
          else
          {
          ?>
            <div class="card mb-1 border-blue border-darken-1">
              <div class="card-content">
                <div class="p-1">
                 <div class="row">
                  <div class="form-group col-md-12 mb-2">
                    <h4 style="color: red">Alasan Dikembalikan</h4>
                    <span style="font-weight: normal"><?=$reqAlasanTolak?></span>
                  </div>
                </div>
                </div>
              </div>
            </div>
          <?php
          }
          ?>
          <div class="card mb-1 border-blue border-darken-1">
            <div class="card-content">
              <div class="p-1">
                <div class="row">
                  <!-- <h3 class="col-md-12 text-center" style="background-color:maroon; padding:5px 0; color: #fff">DATA DI AMBIL DARI RKA AGNES</h3> -->
                  <div class="form-group col-md-2 mb-2">
                    <label style="width: 100%">Tahun Anggaran</label>
                    <!-- <select class="form-control" id="setyear" name="reqTahunAnggaran" onchange="getComboTA(this)"> --> 
                    <select class="form-control" id="setyear" name="reqTahunAnggaran" onChange="return reloadAA($(this).val())">
                      <?php 
                      $selected = '';
                      $kurangdari = date('Y')+3;
                      for ($i= (date('Y')-1); $i <= $kurangdari; $i++) {
                           if ($i == $reqTahunAnggaran) {
                           // if ($i == $getTahun) {
                            $selected = 'selected';
                           } else {
                            if ($reqTahunAnggaran == "" && $i == $date) {
                              $selected = 'selected';
                            } else {
                              $selected = '';
                            }
                           }
                            echo '<option value="'.$i.'" '.$selected.'>'.$i.'</option>';
                      }
                      ?>
                    </select>
                  </div>
                  <div class="form-group col-md-10 mb-2">
                    <?php
                    // echo $url.'---'.$date.'----'.$department;
                    $a = $libapi->getAnggaran($url,$date,$department);
                    $dataMataAnggaran = $a->results->data;

                    ?>
                    <label style="width: 100%">Mata Anggaran</label>
                    <select id="reqMataAnggaran" name="reqMataAnggaran" class="easyui-combobox span3" required>
                      <option></option>
                      <?php
                      if (count($dataMataAnggaran) > 0) {

                        // Group data dulu
                        $libapi2 = new libapi();
                        $b = $libapi2->groupData($dataMataAnggaran);
                        // End Group data dulu

                        // echo "<pre>"; print_r($result);
                        foreach ($b as $key => $value) {
                          $data20[$key] = $value;
                          if ($reqMataAnggaran == $key) {
                          echo '<option value="'.$key.'" selected>'.$key.'</option>';
                          } else {
                          echo '<option value="'.$key.'">'.$key.'</option>';
                          }
                        }
                      } else {
                        echo '<option>.:: Tidak ada data ::.</option>';
                      } ?>
                    </select>

                    <script type="text/javascript">
                      $(document).ready(function() {
                        $('#reqMataAnggaran').combobox({
                          onChange: function(nv,ov){
                            $('#reqKegiatan').html('<option>-- Pengambilan data --</option>');
                            resetForm();
                            $.getJSON("permohonan_paket_usulan_json/getMataAnggaran/?reqUrl=<?= $url ?>&reqDate=<?= $date ?>&reqDepartment=<?= $department ?>&reqMT="+nv,
                              function(data){
                                $('#reqKegiatan').html(data.PESAN);
                              })
                          },
                        });
                      });

                      function getComboA(att) {
                        var value = att.value;
                            department_code = att.options[att.selectedIndex].getAttribute('data-department-code');
                            kode_mata_anggaran = att.options[att.selectedIndex].getAttribute('data-kode-mata-anggaran');
                            tipe_transaksi = att.options[att.selectedIndex].getAttribute('data-tipe-transaksi');
                            kode_kegiatan = att.options[att.selectedIndex].getAttribute('data-kode-kegiatan');
                            sumber_dana = att.options[att.selectedIndex].getAttribute('data-sumber-dana');
                            total_budget = att.options[att.selectedIndex].getAttribute('data-total-budget');
                            budget_remaining = att.options[att.selectedIndex].getAttribute('data-budget-remaining');
                        $('#reqDepartmentCode').val(department_code);
                        $('#reqKodeMataAnggaran').val(kode_mata_anggaran);
                        $('#reqKodeKegiatan').val(kode_kegiatan);
                        $('#reqSumberDana').val(sumber_dana);
                        $('#reqTotalBudget').val(total_budget);
                        $('#reqBudgetRemaining').val(budget_remaining);
                        $('#reqTipeTransaksi').val(tipe_transaksi);
                        // $('#reqAnalisaJenisBelanja').combobox('setValue', toTitleCase(tipe_transaksi));
                        // $('#reqAnalisaJenisBelanja').combobox('setValue', 2);
                      }

                      function getComboTA(att) {
                        var value = att.value;
                        var nv = $('#reqMataAnggaran').combobox('getValue'); 
                        $('#reqKegiatan').html('<option>-- Pengambilan data --</option>');
                        resetForm();
                        $.getJSON("permohonan_paket_usulan_json/getMataAnggaran/?reqUrl=<?= $url ?>&reqDate="+value+"&reqDepartment=<?= $department ?>&reqMT="+nv,
                          function(data){
                            alert(data.PESAN);
                            // $('#reqMataAnggaran').combobox('clear').combobox('loadData', data.PESAN);
                            $('#reqKegiatan').html(data.PESAN);
                        });

                        // $.getJSON("permohonan_paket_usulan_json/updateMataAnggaran/?reqUrl=<?= $url ?>&reqDate="+value+"&reqDepartment=<?= $department ?>",
                        //   function(data){
                        //     $('#reqKegiatan').html(data.PESAN); 
                        // });
                      }

                      function resetForm() {
                        $('#reqDepartmentCode').val('');
                        $('#reqKodeMataAnggaran').val('');
                        $('#reqKodeKegiatan').val('');
                        $('#reqSumberDana').val('');
                        $('#reqTotalBudget').val('');
                        $('#reqBudgetRemaining').val('');
                        $('#reqTipeTransaksi').val('');
                        // $('#reqAnalisaJenisBelanja').combobox('setValue', '');
                      }

                      function toTitleCase(str) {
                        var lcStr = str.toLowerCase();
                        return lcStr.replace(/(?:^|\s)\w/g, function(match) {
                            return match.toUpperCase();
                        });
                      }
                    </script>
                  </div>
                  <!-- <div class="form-group col-md-3 mb-2">
                    <label style="width: 100%">Divisi</label> -->
                    <!-- <input type="text" name="reqDivisi" class="easyui-combobox span3" id="reqDivisi"data-options="valueField:'id',textField:'text',url:'permohonan_paket_usulan_json/comboAnalisaKebutuhan',onSelect: function(rec){
                                            if ($('#reqDivisi').combobox('getValue') == '1') {
                                              $('#idSumberDanaText').hide();
                                            } else {
                                              $('#idSumberDanaText').show();
                                            }}"  value="<?php //isset($reqDivisi) ? $reqDivisi : ''?>" style="width: 300%"/> -->
                  <!-- </div>  -->
                  <!-- <div class="form-group col-md-3 mb-2">
                    <label style="width: 100%">Sub Divisi</label>
                    <input type="text" name="reqSubDivisi" class="easyui-combobox span3" id="reqSubDivisi"data-options="valueField:'id',textField:'text',url:'permohonan_paket_usulan_json/comboAnalisaKebutuhan',onSelect: function(rec){
                                            if ($('#reqSubDivisi').combobox('getValue') == '1') {
                                              $('#idSumberDanaText').hide();
                                            } else {
                                              $('#idSumberDanaText').show();
                                            }}"  value="<?php //isset($reqSubDivisi) ? $reqSubDivisi : ''?>" style="width: 300%"/>
                  </div>  -->
                  <!-- <div class="form-group col-md-10 mb-2">
                    <label>Nama Kebutuhan</label>
                    <input type="text" name="reqNamaKebutuhan" id="reqNamaKebutuhan" class="form-control span5 easyui-validatebox" required value="<?php //$reqNamaKebutuhan?>"/>
                  </div>  -->
                </div>
                <div class="row">
                  <div class="form-group col-md-6 mb-2">
                    <label style="width: 100%">Kegiatan</label>
                    <select name="reqKegiatan" class="form-control easyui-validatebox span3" id="reqKegiatan" onchange="getComboA(this)" required>
                      <option value=""></option>
                      <?= $editKegiatan ?>
                    </select>
                  </div>
                  <div class="form-group col-md-2 mb-2">
                    <label style="width: 100%">Sumber Dana</label>
                    <input  type="text" name="reqSumberDana" class="form-control span3" id="reqSumberDana" readonly="" value="<?= $reqSumberDana ?>">
                    <!-- <input type="text" name="reqAnalisaKebutuhan" class="easyui-combobox span3" id="reqAnalisaKebutuhan"data-options="valueField:'id',textField:'text',url:'permohonan_paket_usulan_json/comboAnalisaKebutuhan',onSelect: function(rec){
                                            if ($('#reqAnalisaKebutuhan').combobox('getValue') == '1') {
                                              $('#idSumberDanaText').hide();
                                            } else {
                                              $('#idSumberDanaText').show();
                                            }}"  value="" required style="width: 300%"/> -->
                  </div>
                  <div class="form-group col-md-4 mb-2">
                    <label style="width: 100%">Anggaran Kegiatan</label>
                    <input type="text" name="reqBudgetRemaining" id="reqBudgetRemaining" class="form-control span5 easyui-validatebox" readonly="" value="<?= $reqBudgetRemaining ?>" />

                  </div>
                  <input  type="hidden" name="reqDepartment" class="form-control col-md-3 span3" id="reqDepartment" value="<?= $this->DEPARTMENT ?>">
                  <input  type="hidden" name="reqDepartmentCode" class="form-control col-md-3 span3" id="reqDepartmentCode" value="<?= $reqDepartmentCode ?>">
                  <input  type="hidden" name="reqKodeMataAnggaran" class="form-control col-md-3 span3" id="reqKodeMataAnggaran" value="<?= $reqKodeMataAnggaran ?>">
                  <input  type="hidden" name="reqKodeKegiatan" class="form-control col-md-3 span3" id="reqKodeKegiatan" value="<?= $reqKodeKegiatan ?>">
                  <input  type="hidden" name="reqTipeTransaksi" class="form-control col-md-3 span3" id="reqTipeTransaksi" value="<?= $reqTipeTransaksi ?>">
                  <input  type="hidden" name="reqTotalBudget" class="form-control col-md-3 span3" id="reqTotalBudget" value="<?= $reqTotalBudget ?>">
                </div>
              </div>
            </div>
          </div>
                <div class="row">
                  <div class="form-group col-md-12 mb-2">
                    <label>Nama Paket</label>
                    <input type="text" name="reqNamaPaket" id="reqNamaPaket" class="form-control span9 easyui-validatebox" required value="<?=$reqNamaPaket?>"/>
                  </div>
                </div>
                <div class="row">
                  <div class="form-group col-md-6 mb-2">
                    <label>Harga Perkiraan</label>
                    <input type="text" name="reqPerkiraanBiayaHarga" id="reqPerkiraanBiayaHarga" class="form-control span9 easyui-validatebox" required value="<?=$reqPerkiraanBiayaHarga?>" pattern="^\$\d{1,3}(,\d{3})*(\.\d+)?$" data-type="currency"/>
                    <sup><i>gunakan tanda titik untuk decimal </i> (contoh: 89,000.50)</sup>
                  </div>
                  <!-- <div class="form-group col-md-3 mb-2"> -->
                    <!-- <label style="width: 100%">Identifikasi Resiko?</label> -->
                    <?php
                    // $pl = array(
                    //             '0' => 'Tidak ada',
                    //             '1' => 'Ada <span style="color:red; font-size:11px">( <u><i>Isi keterangan jika ada identifikasi resiko</i></u> )</span>',
                    //           );
                    // foreach ($pl as $key => $value) {
                    //   if ($reqIdentifikasiResiko == $key) {
                    //     $checked = 'checked';
                    //   } else {
                    //     $checked = '';
                    //   }
                        ?>
                      <!-- <input value="<?php // $key ?>" name="reqIdentifikasiResiko" id="reqIdentifikasiResiko-<?php // $key ?>" type="radio" <?php // $checked ?> style="cursor:pointer"/>
                      &nbsp; <?php // $value ?> &nbsp; <br> -->
                    <?php
                    //}
                    ?>
                  <!-- </div>  -->
                  <div class="form-group col-md-3 mb-2">
                    <label style="width: 100%">Produk Dalam Negeri</label>
                    <input type="text" name="reqAnalisaKategori" class="easyui-combobox span3" id="reqAnalisaKategori"
                            data-options="valueField:'id',textField:'text',url:'permohonan_paket_usulan_json/comboAnalisaKategori'"  value="<?=isset($reqAnalisaKategori) ? $reqAnalisaKategori : ''?>" required style="width: 300%"/>
                  </div>
                  <!-- <div class="form-group col-md-3 mb-2">
                    <label style="width: 100%">Jenis Belanja</label>
                    <input type="text" name="reqAnalisaJenisBelanja" class="easyui-combobox span3" id="reqAnalisaJenisBelanja"
                            data-options="valueField:'id',textField:'text',url:'permohonan_paket_usulan_json/PermohonanPaketAnalisaJenisBelanja'"  value="<?php //isset($reqAnalisaJenisBelanja) ? $reqAnalisaJenisBelanja : ''?>" required style="width: 300%"/>
                  </div>  -->
                  <!-- <div class="form-group col-md-3 mb-2">
                    <label>Kode Anggaran</label>
                    <input type="text" name="reqAnggaran" id="reqAnggaran" class="form-control span5 easyui-validatebox" required value="<?=$reqAnggaran?>"/>
                  </div>   -->
                  <!-- <div class="form-group col-md-3 mb-2">
                    <label style="width: 100%">Komoditas</label>
                    <input type="text" name="reqKomoditas" class="easyui-combobox span3" id="reqKomoditas"
                            data-options="valueField:'id',textField:'text',url:'permohonan_paket_usulan_json/comboKomoditas'"  value="<?php //isset($reqKomoditasId) ? $reqKomoditasId : ''?>" required style="width: 300%"/>
                  </div>
                  <div class="form-group col-md-3 mb-2">
                    <label style="width: 100%">Analisa Pasar</label>
                    <input type="text" name="reqAnalisaPasar" class="easyui-combobox span3" id="reqAnalisaPasar"
                            data-options="valueField:'id',textField:'text',url:'permohonan_paket_usulan_json/comboAnalisaPasar'"  value="<?php //isset($reqAnalisaPasar) ? $reqAnalisaPasar : ''?>" required style="width: 300%"/>
                  </div>  -->
                </div>
               <!--  <div class="row" id="idIdentifikasiResiko">
                  <div class="form-group col-md-12 mb-2">
                    <label>Keterangan Identifikasi Resiko <span style="color:red; font-size:11px">( <u><i>Tulis Risiko yang akan terjadi dan dampaknya serta Bagaimana memitigasi Risikonya ?</i></u> )</span></label>
                      <textarea name="reqIdentifikasiResikoKeterangan" class="textarea-tinymce" style="width:100%; height:150px"><?php // isset($reqIdentifikasiResikoKeterangan)?$reqIdentifikasiResikoKeterangan:''?></textarea>
                  </div>
                </div>   -->
                <!-- <div class="row" id="idSumberDanaText">
                  <div class="form-group col-md-12 mb-2">
                    <label>Sumber Dana dari ? <span style="color:red; font-size:11px">( <u><i>Tulis keterangan sumber dana berasal dari mana saja</i></u> )</span></label>
                      <textarea name="reqSumberDanaKeterangan" id="reqSumberDanaKeterangan" class="textarea-tinymce" style="width:100%; height:150px"><?php //isset($reqSumberDanaKeterangan)?$reqSumberDanaKeterangan:''?></textarea>
                  </div>
                </div>  -->

          <!-- <div class="card mb-1 border-blue border-darken-1">
            <div class="card-content">
              <div class="p-1">
                <div class="alert bg-primary alert-icon-left alert-arrow-left alert-dismissible mb-2" role="alert">
                  <span class="alert-icon"><i class="fa fa-th"></i></span>  <strong>COA</strong>
                </div>
                <div class="table-responsive">
                  <table class="border-double table mb-0">
                    <thead>
                        <tr class="judul-kolom">
                            <th>Nomor COA</th>
                            <th>Keterangan</th>
                            <th>Anggaran Awal</th>
                            <th>Anggaran Terpakai</th>
                            <th>Sisa Anggaran</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="tbodyPermohonanCOA">
                      <?php
                      // $permohonan_paket_coa = new PermohonanPaket();
                      // $noRand = 2021;
                      // if($reqPermohonanPaketId != '')
                      // {
                      //   $permohonan_paket_coa->selectByParamsCoa(array("A.PERMOHONAN_PAKET_ID" => coalesce($reqPermohonanPaketId, 0)));
                      //   while($permohonan_paket_coa->nextRow())
                      //   {
                      ?>

                      <tr>
                        <td>
                          <input type="text" name="reqNomorCOA[]" id="reqNomorCOA<?php //$noRand?>" class="form-control span3 easyui-validatebox" value="<?php // $permohonan_paket_coa->getField("NOMOR") ?>" />
                       </td>
                       <td>
                          <input type="text" name="reqKeteranganCOA[]" id="reqKeteranganCOA<?php //$noRand?>" class="form-control span3 easyui-validatebox" value="<?php // $permohonan_paket_coa->getField("KETERANGAN") ?>"/>
                       </td>
                       <td>
                          <input type="text" name="reqBudgetAwal[]" id="reqBudgetAwal<?php //$noRand?>" class="form-control span3 easyui-validatebox" OnFocus="FormatAngka('reqBudgetAwal<?php //$noRand?>')" OnKeyUp="FormatUang('reqBudgetAwal<?php //$noRand?>')" OnBlur="FormatUang('reqBudgetAwal<?php //$noRand?>')" onchange="calculate('<?php //$noRand?>');"  value="<?php // $permohonan_paket_coa->getField("BUDGET_AWAL") ?>"/>
                       </td>
                       <td>
                          <input type="text" name="reqBudgetTerpakai[]" id="reqBudgetTerpakai<?php //$noRand?>" class="form-control span3 easyui-validatebox" OnFocus="FormatAngka('reqBudgetTerpakai<?php //$noRand?>')" OnKeyUp="FormatUang('reqBudgetTerpakai<?php //$noRand?>')" OnBlur="FormatUang('reqBudgetTerpakai<?php //$noRand?>')" onchange="calculate('<?php //$noRand?>');"  value="<?php // $permohonan_paket_coa->getField("BUDGET_TERPAKAI") ?>"/>
                       </td>
                       <td>
                          <input type="text" name="reqBudgetAkhir[]" id="reqBudgetAkhir<?php //$noRand?>" class="form-control span3 easyui-validatebox" OnKeyUp="FormatUang('reqBudgetAkhir<?php //$noRand?>')" OnBlur="FormatUang('reqBudgetAkhir<?php //$noRand?>')" onchange="calculate('<?php //$noRand?>');"   value="<?php // $permohonan_paket_coa->getField("BUDGET_AKHIR") ?>"/>
                       </td>
                       <td>
                           <a title="#" onclick="$(this).parent().parent().remove();" class="btn-aksi"><i class="fa fa-trash" aria-hidden="true"></i></a>
                       </td>
                      </tr>

                      <?php
                      // $noRand++;
                      //   }
                      // } else
                      // {
                        ?>
                        <tr>
                          <td>
                            <input type="text" name="reqNomorCOA[]" id="reqNomorCOA22" class="form-control span3 easyui-validatebox" value=""required />
                         </td>
                         <td>
                            <input type="text" name="reqKeteranganCOA[]" id="reqKeteranganCOA22" class="form-control span3 easyui-validatebox" value=""/>
                         </td>
                         <td>
                            <input type="text" name="reqBudgetAwal[]" id="reqBudgetAwal22" class="form-control span3 easyui-validatebox" OnFocus="FormatAngka('reqBudgetAwal22')" OnKeyUp="FormatUang('reqBudgetAwal22')" OnBlur="FormatUang('reqBudgetAwal22')" onchange="calculate('22');"  value="" required/>
                         </td>
                         <td>
                            <input type="text" name="reqBudgetTerpakai[]" id="reqBudgetTerpakai22" class="form-control span3 easyui-validatebox" OnFocus="FormatAngka('reqBudgetTerpakai22')" OnKeyUp="FormatUang('reqBudgetTerpakai22')" OnBlur="FormatUang('reqBudgetTerpakai22')" onchange="calculate('22');"  value=""/>
                         </td>
                         <td>
                            <input type="text" name="reqBudgetAkhir[]" id="reqBudgetAkhir22" class="form-control span3 easyui-validatebox" OnKeyUp="FormatUang('reqBudgetAkhir22')" OnBlur="FormatUang('reqBudgetAkhir22')" onchange="calculate('22');"   value=""/>
                         </td>
                         <td></td>
                        </tr>
                      <?php
                      // }

                      ?>
                    </tbody>
                  </table>
                  <a onclick="createRowCOA()" class="<?php // CLASS_BTN_PRIMARY ?>m-1 text-white">
                    <i class="fa fa-plus"></i> Tambah COA
                  </a>
                </div>
              </div>
            </div>
          </div> -->

                <div class="row">
                  <div class="form-group col-md-3 mb-2">
                    <label>Cara Pengadaan</label>
                    <?php
                    $arrayCaraPengadaan = array(
                      '1' => 'Swakelola',
                      '2' => 'Penyedia',
                      // '3' => 'Purchasing',
                    );
                     ?>
                     <select name="reqCaraPengadaan" id="reqCaraPengadaan" class="form-control span9 easyui-validatebox">
                       <option value="">-- Pilih --</option>
                       <?php
                        foreach ($arrayCaraPengadaan as $key => $value) {
                          if ($key == $reqCaraPengadaan) {
                            $selected = 'selected';
                          } else {
                            $selected = '';
                          }
                          echo "<option value='".$key."' ".$selected.">".$value."</option>";
                        }
                        ?>
                     </select>
                  </div>
                  <div class="form-group col-md-3 mb-2">
                    <label style="width: 100%">Jenis Barang/Jasa</label>
                    <input type="text" name="reqJenisBarangJasa" class="easyui-combobox span3" id="reqJenisBarangJasa"
                            data-options="valueField:'id',textField:'text',url:'permohonan_paket_usulan_json/comboAnalisaPaketJenis'"  value="<?=isset($reqJenisBarangJasa) ? $reqJenisBarangJasa : ''?>" required style="width: 300%"/>
                  </div>
                  <div class="form-group col-md-3 mb-2">
                    <label style="width: 100%">Mulai Rencana Pengadaan</label>
                    <input type="text" name="reqRencanaPengadaanMonth" class="easyui-combobox span3" id="reqRencanaPengadaanMonth"
                            data-options="valueField:'id',textField:'text',url:'permohonan_paket_usulan_json/comboMonth'"  value="<?= $reqRencanaPengadaanMonth ?>" required style="width: 100%"/>

                    <input type="text" name="reqRencanaPengadaanYear" class="easyui-combobox span3" id="reqRencanaPengadaanYear"
                            data-options="valueField:'id',textField:'text',url:'permohonan_paket_usulan_json/comboYear'"  value="<?= $reqRencanaPengadaanYear ?>" required style="width: 150%"/>

                    <!-- <input type="text" name="reqRencanaPengadaan" id="reqTanggal" class="form-control span2 easyui-validatebox" required value="<?=$reqRencanaPengadaan?>" onkeydown="return format_date_ym(event, 'reqTanggal');" maxlength="7" style="width: 100% !important"/> -->
                    <!-- <sup><i>isi dengan format bulan-tahun </i> (contoh: <?= $reqRencanaPengadaanEx ?>)</sup> -->

                  </div>
                  <div class="form-group col-md-3 mb-2">
                    <label style="width: 100%">Waktu Penggunaan B/J </label>
                    <input type="text" name="reqWaktuPenggunaBarangjasaMonth" class="easyui-combobox span3" id="reqWaktuPenggunaBarangjasaMonth"
                            data-options="valueField:'id',textField:'text',url:'permohonan_paket_usulan_json/comboMonth'"  value="<?= $reqWaktuPenggunaBarangjasaMonth ?>" required style="width: 100%"/>

                    <input type="text" name="reqWaktuPenggunaBarangjasaYear" class="easyui-combobox span3" id="reqWaktuPenggunaBarangjasaYear"
                            data-options="valueField:'id',textField:'text',url:'permohonan_paket_usulan_json/comboYear'"  value="<?= $reqWaktuPenggunaBarangjasaYear ?>" required style="width: 150%"/>
                    <!-- <input type="text" name="reqWaktuPenggunaBarangjasa" id="reqTanggal2" class="form-control span2 easyui-validatebox" required value="<?=$reqWaktuPenggunaBarangjasa?>" onkeydown="return format_date_ym(event, 'reqTanggal2');" maxlength="7" style="width: 100% !important"/>
                    <sup><i>isi dengan format bulan-tahun </i> (contoh: <?= $reqWaktuPenggunaBarangjasaEx ?>)</sup> -->
                  </div>
                </div>

                <script type="text/javascript">
                  function myformatter(date){
                      var y = date.getFullYear();
                      var m = date.getMonth()+1;
                      var d = date.getDate();
                      // return y+'-'+(m<10?('0'+m):m)+'-'+(d<10?('0'+d):d);
                      // return (d<10?('0'+d):d)+'-'+(m<10?('0'+m):m)+'-'+y;
                      return '01-'+(m<10?('0'+m):m)+'-'+y;
                  }
                  function myparser(s){
                    return s;
                      // if (!s) return new Date();
                      // var ss = (s.split('-'));
                      // var y = parseInt(ss[0],10);
                      // var m = parseInt(ss[1],10);
                      // var d = parseInt(ss[2],10);
                      // if (!isNaN(y) && !isNaN(m) && !isNaN(d)){
                      //     return new Date(y,m-1,d);
                      // } else {
                      //     return new Date();
                      // }
                  }
                </script>

                <div class="row" id="idKeteranganTambahan">
                  <div class="form-group col-md-12 mb-2">
                    <label>Catatan / Keterangan Tambahan</label>
                      <textarea name="reqNote" id="reqNote" class="textarea-tinymce easyui-validatebox" required style="width:100%; height:250px"><?=isset($reqNote)?$reqNote:''?></textarea>
                      <span class="error-note" style="color: #a80000;font-weight: bold;"></span>
                  </div>
                </div>


          <div class="form-actions">
            <input type="hidden" name="usulanId" value="<?=$usulanId?>" />
            <input type="hidden" name="reqMode" value="<?=$reqMode?>" />
            <input type="hidden" name="reqPermohonanPaketId" value="<?=$reqPermohonanPaketId?>" />
            <a href="main/index/permohonan_paket_usulan" class="<?= CLASS_BTN_DANGER ?> mr-1 text-white"> <i class="fa fa-arrow-left"></i> Kembali </a>
            <button type="submit" class="<?= CLASS_BTN_PRIMARY ?>"> <i class="fa fa-check-square-o"></i> Simpan</button>
          </div>

        </div>
      </div>
      </form>

    </div>
  </div>
</div>
