<?php
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 *
 */
$this->load->library("kauth");  $userLogin = new kauth();
$this->load->model(array("Paket","PaketTahap","PaketDokumen","Paketpemenang","RekananEvaluasiAdmin","RekananEvaluasiAdminTawar","RekananEvaluasiTeknisTawar","RekananEvaluasiHargaTawar","PaketRekanan","PaketPanitia","PaketRekananDaftar","PaketPihakLain","PermohonanPaket","PaketPenawaran"));

include_once("functions/string.func.php");
include_once("functions/date.func.php");
include_once("functions/default.func.php");

/* create objects */
$paket = new Paket();
$paket_keterangan = new Paket();
$paket_tahap_jadwal = new PaketTahap();
$paket_tahap = new PaketTahap();
$paket_tahap_metode = new PaketTahap();
$paket_rekanan = new PaketRekanan();
$paket_rekanan_lulus_penawaran = new PaketRekanan();
$paket_pihak_lain = new PaketPihakLain();
$rekanan_evaluasi_admin = new RekananEvaluasiAdmin();
$rekanan_evaluasi_admin_tawar = new RekananEvaluasiAdminTawar();
$rekanan_evaluasi_teknis_tawar = new RekananEvaluasiTeknisTawar();
$rekanan_evaluasi_harga_tawar = new RekananEvaluasiHargaTawar();
$paket_dokumen = new PaketDokumen();
$paket_panitia = new PaketPanitia();
$permohonan_paket = new PermohonanPaket();

/* VARIABLES */
$reqId = httpFilterRequest("eid");
$reqKey = httpFilterRequest("key");

$reqMode = '';
if($reqMode == "reset")
{
	$paket->setField("FIELD", "ALASAN");
	$paket->setField("FIELD_VALUE", "''");
	$paket->setField("PAKET_ID", $reqId);
	$paket->updateByField();
}

$paket->selectByParamsMonitoring2(array(), -1, -1, " AND PAKET_ID = '".$reqId."' ");
$paket->firstRow();
if ($paket->getField("PUBLISH_PAKET") == 0 && ($this->USER_TYPE_ID == '6' || $this->USER_TYPE_ID == '')) { // khusus PENYEDIA di cek
  // echo "Maaf, paket tidak tersedia";
	// exit();
	redirect(base_url());
}
// 29.08.2025 cek key UUID
if ($paket->getField("PAKET_UUID") == '' || $reqKey == '' || $paket->getField("PAKET_UUID") != $reqKey) {
	redirect(base_url());
}

$wajib = '<span class="badge badge-primary"><small style="font-size: 10px">wajib dilengkapi</small>';
$optional = '<span class="badge badge-success"><small style="font-size: 10px">Optional</small>';
$sudahwajib = '<span class="badge badge-success"><small style="font-size: 10px"><i class="fa fa-check"></i> lengkap</small>';

//echo $paket->query;exit;
$pra_kualifikasi_cek = $paket->getField("PAKET_METODE_KUALIFIKASI_ID"); // 1 File atau 2 File
$metode_evaluasi_cek = $paket->getField("PAKET_METODE_EVALUASI_ID"); // 2-Sistem Nilai, 7-Sistem Harga Terendah
$paket_jenis_cek = $paket->getField("PAKET_JENIS_ID"); // 1-PK, 2-JASKON, 3-B, 4-JL

$paket_user_id = $paket->getField("USER_LOGIN_ID");
$alasan = $paket->getField("ALASAN");
$alasan_ulang = $paket->getField("ALASAN_ULANG");
$multi_pemenang = $paket->getField("MULTI_PEMENANG");
$multi_bidang_usaha = $paket->getField("MULTI_BIDANG_USAHA");
$ppk = $paket->getField("PPK");
// 1:Tender, 2:Pengadaan langsung, 3:Tender Terbatas, 4:Seleksi Langsung, 5:Penunjukan Langsung, 6:e-Purchasing, 7:Tender Cepat, 8:Kompetisi, 9:Pembelian Langsung Offline, 10:Tender Kualifikasi, 11:Penunjukan Langsung Khusus
$paket_metode_lelang_id = $paket->getField("PAKET_METODE_LELANG_ID");

// echo $paket_metode_lelang_id; die;
$paket_metode_nama = $paket->getField("METODE_LELANG");
$rekanan_id_pemenang_negosiasi = $paket->getField("REKANAN_ID_PEMENANG");
$publish_ba_penawaran = $paket->getField("PUBLISH_BA_PENAWARAN");
$publish_ba_kualifikasi = $paket->getField("PUBLISH_BA_KUALIFIKASI");
$sistem_sampul = $paket->getField("SISTEM_SAMPUL");
$publish_ba_evaluasi_sampul1 = $paket->getField("PUBLISH_BA_EVALSAMPUL1");
$publish_ba_evaluasi_sampul2 = $paket->getField("PUBLISH_BA_EVALSAMPUL2");
$publish_ba_penawaran_sampul2 = $paket->getField("PUBLISH_BA_PENAWARAN2");
$publish_eval_kualifikasi = $paket->getField("PUBLISH_EVALKUALIFIKASI");
$bidding = $paket->getField("BIDDING");
$reqPermohonanId = $paket->getField("PERMOHONAN_PAKET_ID");
$reqMetodePengadaan = $paket->getField("PAKET_METODE_LELANG_ID");
// echo $reqPermohonanId;
if ($reqPermohonanId) {
  $permohonan_paket = new PermohonanPaket();
  // $permohonan_paket->selectByParams(array("A.PERMOHONAN_PAKET_ID" => $reqPermohonanId, "A.UNIT_KERJA_ID" => $this->UNIT_KERJA_ID));
  $permohonan_paket->selectByParams(array("A.PERMOHONAN_PAKET_ID" => $reqPermohonanId));
  $permohonan_paket->firstRow();
  $reqPL = $permohonan_paket->getField("PENGADAANLANGSUNG");
}

// ik 20260712 By Pass tidak musti upload BoQ
$paket_penawaran = new PaketPenawaran();
$paket_penawaran->selectByParams(array("A.PAKET_ID" => $reqId, "ITEM_CHILD" => "0"));
if ($paket_penawaran->countRow() == 0 ) {
    $paket_penawaran_insert = new PaketPenawaran();
    $paket_penawaran_insert->setField("PAKET_ID", $reqId);
    $paket_penawaran_insert->setField("NAMA", $paket->getField("NAMA"));
    $paket_penawaran_insert->setField("LAST_CREATE_USER", $this->USER_LOGIN_ID);
    $paket_penawaran_insert->setField("JUMLAH", CommaToNo($paket->getField("NILAI")));
    $paket_penawaran_insert->setField("PERMOHONAN_PAKET_ID", $reqPermohonanId);
    $paket_penawaran_insert->insert2();
}

// cek pemenang
$getpaket_pemenang = new Paketpemenang();
$getpaket_pemenang->selectByParams(array("PAKET_ID" => $reqId), -1, -1);
$getpaket_pemenang->firstRow();
$cekPublishPemenang = $getpaket_pemenang->getField("PUBLISH");
if ($bidding == '1') { // Auction
	$rekanan_id_pemenang = ($getpaket_pemenang->getField("PAKET_PEMENANG_ID")) ? $getpaket_pemenang->getField("REKANAN_ID") : $getpaket_pemenang->getField("REKANAN_ID");
} else { // Negosiasi
	$rekanan_id_pemenang = ($getpaket_pemenang->getField("PAKET_PEMENANG_ID")) ? $getpaket_pemenang->getField("REKANAN_ID") : $rekanan_id_pemenang_negosiasi;
}

$paket_tahap_jadwal->selectByParamsJadwal(array("TAMPILKAN" => "1"), -1, -1, " AND PAKET_ID = '".$reqId."' ");

$jenis_tahap = $paket_tahap_metode->getJenisTahapById($reqId);
 

$reqPaketRekananId = '';
if ($this->REKANAN_ID) {
	$paket_rekanan->selectByParamsPaketLelangV2(array("A.PAKET_ID" => $reqId, "A.REKANAN_ID" => $this->REKANAN_ID), -1, -1, " AND TANGGAL_DAFTAR IS NOT NULL ");
	$paket_rekanan->firstRow();
	$reqPaketRekananId = $paket_rekanan->getField("PAKET_REKANAN_ID");
} 

$reqTanggalDaftar = $paket_rekanan->getField("TANGGAL_DAFTAR");
$reqKodeRekanan = $paket_rekanan->getField("KODE_REKANAN");
$reqAanwijzing = $paket_rekanan->getField("AANWIJZING");
$reqLulusKualifikasi = $paket_rekanan->getField("LULUS_KUALIFIKASI");
$reqLulusKualifikasiKeterangan = $paket_rekanan->getField("LULUS_KUALIFIKASI_KETERANGAN");
$reqLulusPendaftaran = $paket_rekanan->getField("LULUS_PENDAFTARAN");
$reqLulusPendaftaranKeterangan = $paket_rekanan->getField("LULUS_PENDAFTARAN_KETERANGAN");
$reqKirimPenawaran = $paket_rekanan->getField("KIRIM_PENAWARAN");
$reqKirimPenawaranPassword = $paket_rekanan->getField("KIRIM_PENAWARAN_PASSWORD");
$reqKirimPenawaranPassword2 = $paket_rekanan->getField("KIRIM_PENAWARAN_PASSWORD2");

$reqKirimPenawaranKelengkapan = $paket_rekanan->getField("KIRIM_PENAWARAN_LENGKAP");
$reqKirimPenawaranKelengkapanAlasan = $paket_rekanan->getField("KIRIM_PENAWARAN_ALASAN");
$reqKirimPenawaranKelengkapanSampul2 = $paket_rekanan->getField("KIRIM_PENAWARAN_LENGKAP2");
$reqKirimPenawaranKelengkapanAlasanSampul2 = $paket_rekanan->getField("KIRIM_PENAWARAN_ALASAN2");
$reqLulusPenawaranSampul1 = $paket_rekanan->getField("LULUS_PENAWARAN_SAMPUL1");
 
?>

<style type="text/css">
  small { font-weight: bold; font-size: 11.5px }
  a.list-group-item { color: #000 !important; }
  .list-group-item { padding: 0.5rem 1.25rem !important; border: transparent !important; }
  .list-group a.list-group-item:hover  { color: #000 !important;  transition: 0.3s; background-color: #F7CA18 !important;}
</style>

<script type="text/javascript">
$(document).ready(function() {

	$('#btnKirim').on('click', function () {
		$.messager.defaults.ok = 'Ya';
		$.messager.defaults.cancel = 'Tidak';
		$.messager.confirm('Konfirmasi',"Kirim Undangan ke Penyedia?",function(r){
		  if (r){
			  var win = $.messager.progress({
									  title:'<?= SYSTEM_NAME.' | '.SYSTEM_NAME_PT ?>',
									  msg:'Proses kirim undangan via email...'
								  });
			  $.get("paket_rekanan_json/undang_pemilihan_email/?reqId=<?=$reqId?>", function( data ) {
				  $.messager.progress('close');
				  $.messager.alert('Informasi',data, 'info');
			  });
		  }
	  });
	});
});
function reloadMonitoring(){ document.location.href = 'kontrak/index/contracting_paket_sppbj'; }
</script>

<div class="row">
  <?php
  if ((int)$this->USER_TYPE_ID != '')
  { // Untuk user login 

    $this->load->model(array("Metode","Paketpemenang","PaketRekanan"));

    $getpaket_pemenang_c = new Paketpemenang();
    $metode_c = new Metode();
    $paket_rekanan_c = new PaketRekanan();

    $countJadwal = $metode_c->getCountByParams(array("PAKET_ID" => $reqId));
    $countPemenang = $getpaket_pemenang_c->getCountByParams(array("A.PAKET_ID" => $reqId));
    $countPeserta = $paket_rekanan_c->getCountByParams(array("A.PAKET_ID" => $reqId));

    $totalDiLengkapi = 0;

    ?>
  <div class="col-md-3 col-sm-3">

		<div class="list-group">
			<a class="text-white list-group-item disabled" style="color:#fff !important; background-color: #000 !important;"> Info Detail <?= $paket_metode_nama ?> </a>
        <?php
        if($this->USER_LOGIN_ID == $paket_user_id || $idPanitia > 0 || $this->USER_TYPE_ID == 7) // KEPALA PENGADAAN
        {
          if($this->USER_LOGIN_ID == $paket_user_id || $idPanitia > 0)
          {
            // untuk label wajib dilengkapi dan lengkap
            $this->load->model(array("Metode","Paket","PaketDokumen","PaketPanitia","PaketEvaluasiAdminTawar","PaketEvaluasiTeknisTawar","PaketEvaluasiHargaTawar","PaketEvaluasiKualifikasi","Paketpemenang","PaketBidangUsaha"));
            ?> 
            <a href="kontrak/index/paket_lelang_tambah_bypass/?reqId=<?=$reqId?>" class="list-group-item"> <span class="ft-arrow-right"></span> Edit Paket Pengadaan </span>
            </a> 

          <?php
          }

          if($this->USER_LOGIN_ID == $paket_user_id || $idPanitia > 0 || $this->USER_TYPE_ID == 7) // KEPALA PENGADAAN
          { 
            ?>
            <a href="kontrak/index/paket_lelang_tambah_jadwal_bypass/?reqId=<?=$reqId?>&back=1" class="list-group-item"> <span class="ft-arrow-right"></span> Jadwal <?php if ($countJadwal == 0) { echo $wajib; } else { echo $sudahwajib; $totalDiLengkapi++; }  ?> </a>

            <a href="kontrak/index/paket_lelang_tambah_daftar_peserta_bypass/?reqId=<?=$reqId?>" class="list-group-item"> <span class="ft-arrow-right"></span> Daftar Peserta <?php if ($countPeserta == 0) { echo $wajib; } else { echo $sudahwajib; $totalDiLengkapi++; }  ?></a>

 
            <a href="kontrak/index/paket_lelang_tambah_penentuan_pemenang_bypass/?reqId=<?=$reqId?>" class="list-group-item"> <span class="ft-arrow-right"></span>
                <?php
                // 1:Tender, 2:Pengadaan langsung, 3:Tender Terbatas, 4:Seleksi Langsung, 5:Penunjukan Langsung, 6:e-Purchasing, 7:Tender Cepat, 8:Kompetisi
                switch ($paket_metode_lelang_id) {
                  case '1':
                  case '2':
                  case '3':
                  case '4':
                  case '7':
                  case '8':
                  case '10':
                    if ($countPemenang == 0) { echo "Penetapan Pemenang ".$wajib; } else { echo "Penetapan Pemenang ".$sudahwajib; $totalDiLengkapi++; }
                    // if ($countPemenang == 0) { echo "Penetapan Pemenang "; } else { echo "Penetapan Pemenang "; }
                    break;
                  case '5':
                  case '6':
                  case '9':
                  case '11':
                  case '12':
                  if ($countPemenang == 0) { echo "Penetapan Penyedia ".$wajib; } else { echo "Penetapan Penyedia ".$sudahwajib; $totalDiLengkapi++; }
                  // if ($countPemenang == 0) { echo "Penetapan Penyedia "; } else { echo "Penetapan Penyedia "; }
                    break;
                }
                ?>
            </a> 
            <?php 
            }
          }
        ?>
          <?php 
          if ($totalDiLengkapi >= 3) { ?>
					<a onclick="openAdd('kontrak/loadUrl/kontrak/contracting_penunjukan_pic_approve/?reqId=<?= $reqId ?>')" class="list-group-item"> <span class="ft-arrow-right"></span>  Tunjuk PIC Kontrak </a>
          <?php 
          } ?>
					<a onclick="openAddLg('main/loadUrl/main/rekam_jejak_view?id=<?= $reqPermohonanId ?>&paketid=<?= $reqId ?>')" class="list-group-item"> <span class="ft-arrow-right"></span>  Rekam Jejak </a>
      </ul>
    </div>
  </div>
  <?php
  } // if ((int)$this->USER_TYPE_ID != '') { ?>

  <?php
  if ((int)$this->USER_TYPE_ID != '') { // Untuk user login ?>
  <div class="col-md-9 col-sm-9">
  <?php
  } else { ?>
  <div class="col-md-12 col-sm-12">
  <?php
  } ?>
    <div class="card">
      <div class="card-content collapse show border-info border-darken-2">
        <div class="card-body">
          <h4 style="color: #000; font-weight: bold"><?=$paket->getField("NAMA")?></h4>
              <hr>
              <!-- </div> -->
              <table class="table table-bordered table-hover">
                <tbody>
                	<?php
                	if ($paket_metode_lelang_id == '2' || $paket_metode_lelang_id == '3' || $paket_metode_lelang_id == '5' || $paket_metode_lelang_id == '8')
                  {
                  	if ($this->USER_LOGIN_ID == $paket_user_id || $idPanitia > 0) {
	                		if ($paket->getField("PUBLISH_PAKET")  != '1')
	                		{
	                			$this->load->library("libvalidasi"); $libvalidasi = new libvalidasi();
		                    $countCekValidasi = $libvalidasi->cekValidasiPublishPaket($reqId);
		                    if ($countCekValidasi['count'] > 0) { }
		                    else
		                    {
	                			?>
			                	<tr>
			                		<td colspan="4">
			                  		<button type="button" id="btnKirim" class="<?= CLASS_BTN_SUCCESS ?>"> <?= BTN_KIRIM ?> Undangan</button>
			                		</td>
			                	</tr>
	                	<?php
	                			}
	                		}
	                	}
                	}
                	?>
                  <tr>
                    <td width="25%" colspan="2">
                      <small><i class="fa fa-calendar"></i> Tahun Anggaran</small> <br>
                      <?=getYear($paket->getField("TAHUN_ANGGARAN"))?>
                    </td>
                    <td width="25%" colspan="2">
                      <small><i class="fa fa-map-marker"></i> Lokasi Pekerjaan</small> <br>
                      <?=$paket->getField("LOKASI")?>
                    </td>
                  </tr>
                  <tr>
                    <td width="25%" colspan="2">
                      <small><i class="fa fa-inbox"></i> Jenis Pengadaan</small> <br>
                      <?= str_replace("Katalog", "Purchasing", $paket->getField("PAKET_JENIS")) ?>
                    </td>
                    <td width="25%" colspan="2">
                      <small><i class="fa fa-tag"></i> Metode Pengadaan</small> <br>
                      <?=$paket->getField("METODE_LELANG")?>
                      <?php
                      if($paket->getField("PAKET_METODE_LELANG_ID") == '1') {
                      	if ($paket->getField("MULTI_PEMENANG") == '1') {
                      		echo '&nbsp;<span style="font-size:11px">( Pemanang lebih dari satu )</span>';
                      	}
                      }  ?>
                    </td>
                  </tr>
                  <?php
                  if ($paket_metode_lelang_id != '6' && $paket_metode_lelang_id != '9')
                  { // bukan Purchasing/Pembelian Langsung & bukan pembelian offline ?>
                  <tr>
                    <!-- <td width="25%" colspan="2">
                      <small><i class="fa fa-clipboard"></i> Metode Kualifikasi</small> <br>
                      <?=$paket->getField("METODE_KUALIFIKASI")?>
                    </td> -->
                    <td width="25%" colspan="2">
                      <small><i class="fa fa-folder-open"></i> Metode Penyampaian Penawaran</small> <br>
                      <?=$paket->getField("SISTEM_SAMPUL")?> File
                    </td>
                    <td width="25%" colspan="2">
                      <small><i class="fa fa-exchange"></i> Metode Evaluasi</small> <br>
                      <?=$paket->getField("METODE_EVALUASI")?>
                    </td>
                  </tr>
                  <?php
                  } ?>

                  <?php
                  if ($paket_metode_lelang_id != '6' && $paket_metode_lelang_id != '9')
                  { // bukan Purchasing/Pembelian Langsung & bukan pembelian offline ?>
                  <tr>
                    <td width="25%" colspan="4">
                      <small><i class="fa fa-file-text"></i> Kualifikasi Usaha</small> <br>
                      <?=$paket->getField("REKANAN_KUALIFIKASI")?>
                    </td> 
                  </tr>
                  <?php
                  } ?>
                  <?php
                  // if($this->USER_TYPE_ID == 3 || $this->USER_TYPE_ID == 7 ) // PANITIA & EKSEKUTIF
                  // {
                  if ($paket_metode_lelang_id == '1' || $paket_metode_lelang_id == '2' || $paket_metode_lelang_id == '3' || $paket_metode_lelang_id == '4' || $paket_metode_lelang_id == '10') // ditampilkan hanya untuk Tender
                  {
                  ?>
                  <tr>
                    <td width="25%" colspan="4">
                      <small><i class="fa fa-money"></i> Harga Perkiraan Sendiri</small> <br>
                      <?=$paket->getField("NILAI_MATA_UANG")?> <?=currencyToPage($paket->getField("NILAI_OWNER_ESTIMATE"))?>
                    </td>
                    </td>
                  </tr>
                  <?php
                  } else {
                    if ($this->USER_TYPE_ID != '' && $this->USER_TYPE_ID != '6') { // bukan untuk penyedia
                  ?>
                  <tr>
                    <td width="25%" colspan="4">
                      <small><i class="fa fa-money"></i> Harga Perkiraan Sendiri</small> <br>
                      <?=$paket->getField("NILAI_MATA_UANG")?> <?=currencyToPage($paket->getField("NILAI_OWNER_ESTIMATE"))?>
                    </td>
                    </td>
                  </tr>
                  <?php
                    }
                  }
                  // }
                  ?> 
                </tbody>
              </table> 

          <!-- </form> -->
          <div class="form-actions">
            <?php
            if ($this->USER_TYPE_ID != '6') { 
                  echo '<a href="kontrak/index/contracting_bypass" class="'.CLASS_BTN_DANGER.' mr-1"> '.BTN_KEMBALI.' </a>';
            }
             ?> 

              <?php
            if ($reqRescheduleKe >= 1) { ?>
            <a <?php echo 'onClick="openAdd(\'main/loadUrl/main/reschedule_rekamjejak?reqId='.$reqId.'\');"'; ?> class="<?= CLASS_BTN_DARK ?> mr-1 text-white" target="_blank"> <i class="fa fa-paw"></i> Rekam Jejak Reschedule Jadwal </a>
            <?php
            } ?>
          </div>
        </div>
      </div>
    </div>
  </div>
</div> 
