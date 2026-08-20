<?php
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 * 
 */
$this->libsession->cekSession();

$this->load->library("kauth");  $userLogin = new kauth();
$this->load->library("paketinfo"); $paketInfo = new paketinfo();
$this->load->model("PermohonanPaket");
$this->load->model("PermohonanPaketFile");
$this->load->library("FileHandler");
include_once("functions/string.func.php");
include_once("functions/date.func.php");
include_once("functions/default.func.php");

$reqId= $this->input->get("reqId");

$permohonan_paket = new PermohonanPaket();
$permohonan_paket_analisa = new PermohonanPaket();
$permohonan_paket_file = new PermohonanPaketFile();
$file = new FileHandler();

if($reqId == '')
{
  $reqMode ='insert';
  $reqMetodePengadaan = '0';
}
else {

  $reqMode = 'update';

}
  $permohonan_paket->selectByParams(array("A.PERMOHONAN_PAKET_ID" => coalesce($reqId, 0), "A.USER_LOGIN_ID" => $this->USER_LOGIN_ID));
  $permohonan_paket->firstRow();

  $reqUserLoginId = $permohonan_paket->getField("USER_LOGIN_ID");
  $reqNotaDinas = $permohonan_paket->getField("NOTA_DINAS");
  $reqNomorPPA = $permohonan_paket->getField("NO_PPA");
  $reqTanggal = dateToPageCheck($permohonan_paket->getField("TANGGAL"));
  $reqNamaPaket = $permohonan_paket->getField("NAMA");
  $reqNilai = $permohonan_paket->getField("NILAI");
  $reqKeterangan = $permohonan_paket->getField("KETERANGAN");
  $reqAlasan = $permohonan_paket->getField("ALASAN_TOLAK");
  $reqMetodePengadaan = $permohonan_paket->getField("PENGADAANLANGSUNG") ?: '0';
  if ($reqNilai == '') {
    $reqNilai = $permohonan_paket->getField("PERKIRAAN_BIAYA_HARGA");
  }
  $reqPermohonanPaketAnalisaId = $permohonan_paket->getField("PERMOHONAN_PAKET_ANALISA_ID");
  $reqBudgetAwal = $permohonan_paket->getField("BUDGET_AWAL");
  $reqBudgetTerpakai = $permohonan_paket->getField("BUDGET_TERPAKAI");
  $reqBudgetAkhir = $permohonan_paket->getField("BUDGET_AKHIR");
  $reqBudgetAkhir = $permohonan_paket->getField("BUDGET_AKHIR");
  $reqTahunAnggaran = $permohonan_paket->getField("TAHUN_ANGGARAN");

  if ($this->USER_LOGIN_ID != $reqUserLoginId) {
    redirect(base_url('main/index/404'));
  }

?>
<style type="text/css">
#setNilaiHPS { color: red; font-size: 10px; }
</style>
<script type="text/javascript">
  setTimeout(function () {
    document.getElementById('btnSubmit').click();
  }, 100);

$(document).ready(function() {

  $(function(){
    $('#ff').form({
      url:'permohonan_paket_json/permohonan_lelang_addv2',
      onSubmit:function(){
        var v=$(this).form('validate');
        // if(v) showLoad();  // show the message box
        return v;
      },
      success:function(data){
        var hasil=data.split('-');
        // hideLoad();
        if (hasil[2] === '2') { //  Pembelian langsung
          alertSuccess2(hasil[0]);
          document.location.href = 'main/index/permohonan_paket_fungsional_add/?reqId='+hasil[1];
        } else {
          document.location.href = 'main/index/paket_lelang_tambah_rincian_pekerjaan_permohonan/?reqPer='+hasil[1];
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
        <h4 class="card-title text-white">Rencana Pengadaan
        <?php
          $this->load->library("librekamjejak"); $librekamjejak = new librekamjejak();
          echo $librekamjejak->buttonRJ($reqId); ?>
        <!-- <a onclick="openAdd('main/loadUrl/main/permohonan_paket_usulan_lihat?usulanId=<?= $reqPermohonanPaketAnalisaId ?>')" class="btn btn-danger mr-1 text-white"  style="padding:.4rem 1rem">
          <i class="fa fa-paper-plane-o"></i> Lihat Usulan
        </a> -->
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
          if($reqAlasan == '')
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
                    <span style="font-weight: normal"><?=$reqAlasan?></span>
                  </div>
                </div>
                </div>
              </div>
            </div>
          <?php
          }
          ?>
          <!-- <div class="row">
            <div class="form-group col-md-12 mb-2">
              <label>No. SiRUP </label>
              <input type="text" name="reqNotaDinas" id="reqNotaDinas" class="form-control span5 easyui-validatebox" required value="<?=$reqNotaDinas?>"/>
            </div>
          </div> -->
          <div class="row"> 
            <div class="form-group col-md-2 mb-2">
              <label style="width: 100%">Tahun Anggaran</label>
              <input type="text" name="reqTahunAnggaran" id="reqTahunAnggaran" class="form-control span9 easyui-validatebox" required value="<?=$reqTahunAnggaran?>" readonly/> 
            </div>
          </div>
          <div class="row">
            <div class="form-group col-md-12 mb-2">
              <label>Nama Paket</label>
              <input type="text" name="reqNamaPaket" id="reqNamaPaket" class="form-control span9 easyui-validatebox" required value="<?=$reqNamaPaket?>" readonly/>
            </div>
          </div>
          <div class="row">
            <div class="form-group col-md-4 mb-12">
              <label>Harga Perkiraan </label> <!-- <span id="setNilaiHPS">diatas Rp.1.000.000.000,-</span></label> -->
              <input type="text" name="reqNilai" id="reqNilai" class="form-control span3 easyui-validatebox" required value="<?=$reqNilai?>" pattern="^\$\d{1,3}(,\d{3})*(\.\d+)?$" data-type="currency" readonly/>
                    <!-- <sup><i>gunakan tanda titik untuk decimal </i> (contoh: 89,000.50)</sup> -->
            </div>
          </div> 
          <input value="<?= $reqMetodePengadaan ?>" name="reqMetodePengadaan" id="reqMetodePengadaan" type="hidden"/>
               
          <div class="form-actions">
            <input type="hidden" name="reqId" value="<?=$reqId?>" />
            <input type="hidden" name="reqMode" value="<?=$reqMode?>" />
            <a href="main/index/permohonan_paket_fungsional" class="btn round btn-min-width box-shadow-1 btn-danger mr-1 text-white"> <i class="fa fa-arrow-left"></i> Kembali </a>
            <button type="submit" id="btnSubmit" class="btn round btn-min-width box-shadow-1 btn-primary ">Lanjut <i class="fa fa-arrow-right"></i></button>
            <?php
            if ($reqId != '' && $reqMetodePengadaan != '2') { 
              if ($reqNomorPPA != '') {
            ?>
              <a href="main/index/paket_lelang_tambah_rincian_pekerjaan_permohonan/?reqPer=<?= $reqId ?>" class="btn round btn-min-width box-shadow-1 btn-primary pull-right mr-1 text-white"> Lanjut <i class="fa fa-arrow-right"></i></a>
            <?php
             }
            } ?>
          </div>

        </div>
      </div>
      </form>

    </div>
  </div>
</div>