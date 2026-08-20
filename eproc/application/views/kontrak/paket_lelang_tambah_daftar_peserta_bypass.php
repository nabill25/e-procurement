<?php
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 * 
 */
$reqId = $this->input->get("reqId");
$this->libsession->cekSession($reqId);   

$this->load->library("kauth");  $userLogin = new kauth(); 
$this->load->model(array("PaketRekanan","PaketTahap","PaketPenawaran"));
$this->load->library("paketinfo"); $paketInfo = new paketinfo();
include_once("functions/string.func.php");
include_once("functions/date.func.php");
include_once("functions/default.func.php");
$this->load->library("KMail");

$paket_rekanan = new PaketRekanan();
$paket_rekanan_total = new PaketRekanan();
$paket_penawaran = new PaketPenawaran();

$reqMode = $this->input->post("reqMode");
$submitSimpan = $this->input->post("submitSimpan");

$reqLulusPendaftaran = isset($_POST["reqLulusPendaftaran"])?$_POST["reqLulusPendaftaran"]:'';
$reqLulusKeterangan = isset($_POST["reqLulusKeterangan"])?$_POST["reqLulusKeterangan"]:'';
$reqPaketRekananId = isset($_POST["reqPaketRekananId"])?$_POST["reqPaketRekananId"]:'';
$reqPaketRekananIdUser = $this->input->post("reqPaketRekananIdUser");
$reqLulusPendaftaranUser = $this->input->post("reqLulusPendaftaranUser");


$paket_rekanan->selectByParams(array("PAKET_ID" => $reqId));
$totalPaket = $paket_rekanan_total->getCountByParams(array("PAKET_ID" => $reqId));
$paketInfo->getPaket($reqId);
$reqRekananIdPemenang = $paketInfo->rekanan_id_pemenang;

$paket_penawaran->selectByParams(array("PAKET_ID" => $reqId));
$paket_penawaran->firstRow();

// Cek Jadwal Pembukaan Penawaran
$paket_tahap_metode = new PaketTahap();
$paket_tahap = new PaketTahap();

$jenis_tahap = $paket_tahap_metode->getJenisTahapById($reqId);
$arrPembukaanAuction = PEMBUKAAN_AUCTION; 
?> 
<script type="text/javascript">
$(document).ready(function() {
	$(function(){
    $('#ff').form({
      url:'paket_rekanan_json/undang_pemilihan_bypass',
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
        alertSuccess2(data);
        setTimeout(function() {
          location.reload();
          // window.location = '<?php echo "kontrak/index/paket_lelang_tambah_daftar_peserta_bypass/?reqId=$reqId"; ?>';
          // hideLoad();
        }, 2000);
      }
    });

  });
	
});

</script>

<div class="row"> 
  <div class="col-md-12 col-sm-12">
    <div class="card">
      <div class="card-header card-head-inverse bg-primary">
        <h4 class="card-title text-white">Daftar Peserta <?= $paketInfo->metode_lelang_nama ?>
          
          <?php 
          if ($reqRekananIdPemenang == '') { ?>
            <div class="badge badge-pill badge-warning">
              <a id="btnAdd" onClick="openAdd('main/loadUrl/main/rekanan_lookup/?reqId=<?=$reqId?>');" data-toogle=""><span class="fa fa-plus text-white icon-line-height" data-toggle="tooltip" data-placement="top" title="Tambah Data"></span> Tambah </a>
            </div>
          <?php 
          } ?>
        </h4>
        <a class="heading-elements-toggle"><i class="fa fa-ellipsis-v font-medium-3"></i></a> 
        <div class="heading-elements">
            <ul class="list-inline mb-0">
              <li><a data-action="expand"><i class="ft-maximize"></i></a></li>
            </ul>
        </div>
      </div>

      <form id="ff" class="easyui-form form-horizontal" method="post" data-options="novalidate:true" enctype="multipart/form-data">
      <div class="card-content collapse show border-info border-darken-2">
        <div class="card-body area-datatable">

            <div class="table-responsive">
              <table class="table table-bordered table-hover" id="tbl_bidang">
                  <thead>
                    <tr>
                      <td>Nama Penyedia</td>
                      <td>Alamat</td>
                      <td>Email</td>
                      <td width="5%">Aksi</td>
                    </tr>
                    </thead>
                  <tbody id="tbodyRekanan">
                    <?php
                    $i=1;
                    while($paket_rekanan->nextRow())
                    {
                    ?>
                      <tr>
                        <td><?=$paket_rekanan->getField("REKANAN")?></td>
                        <td><?=$paket_rekanan->getField("ALAMAT")?></td>
                        <td><?=$paket_rekanan->getField("EMAIL")?></td>
                        <td>
                          <input type="hidden" name="reqRekananId[]" id="reqRekananId<?=$i?>" value="<?=$paket_rekanan->getField("REKANAN_ID")?>">
                          <?php
                          if ($reqRekananIdPemenang == '') { // pemenang sudah di tetapkan ?>
                            <a title="#" onclick="$(this).parent().parent().remove();">
                              <?= ICON_DELETE ?>
                            </a>
                          <?php
                          } else { echo "-"; } ?>
                        </td>
                      </tr>
                    <?php
                    }
                    ?>
                    </tbody>
                </table>
            </div>

            <div class="form-actions">
              <div>
                <input type="hidden" id="reqMetodeLelangId" value="bypass" />
                <input type="hidden" name="reqId" value="<?=$reqId?>"> <!-- PaketID -->
                <input type="hidden" name="submitSimpan" value="Simpan" />
                <input type="hidden" name="reqPaketPenawaranId" value="<?= $paket_penawaran->getField("PAKET_PENAWARAN_ID") ?>">
              </div>
              <a href="kontrak/index/paket_detil_bypass/?eid=<?=$reqId?>&key=<?=$paketInfo->uuid?>" class="<?= CLASS_BTN_DANGER ?> mr-1"> <?= BTN_KEMBALI ?> </a> 
              <?php 
              if ($reqRekananIdPemenang == '') { ?>
              <button type="submit" class="mr-1 <?= CLASS_BTN_PRIMARY ?>"><?= BTN_SIMPAN ?></button>
              <?php 
              } ?>
            </div> 
            
        </div>
      </div>
      </form>
      
    </div>
  </div> 
</div>  