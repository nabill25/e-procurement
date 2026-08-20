<?php
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 * 
 */
$this->load->library("kauth");  $userLogin = new kauth();
$this->load->model("Paket");
$this->load->model("PaketTahap");
$this->load->model("PaketDokumen");
$this->load->model("Paketpemenang");
$this->load->model("RekananEvaluasiAdmin");
$this->load->model("RekananEvaluasiAdminTawar");
$this->load->model("RekananEvaluasiTeknisTawar");
$this->load->model("RekananEvaluasiHargaTawar");
$this->load->model("PaketRekanan");
$this->load->model("PaketPanitia");
$this->load->model("PaketRekananDaftar");
$this->load->model("PaketPihakLain");
$this->load->model("PermohonanPaket");

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
$reqId = httpFilterRequest("reqId");
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

//echo $paket->query;exit;
$pra_kualifikasi_cek = $paket->getField("PAKET_METODE_KUALIFIKASI_ID"); // 1 File atau 2 File
$metode_evaluasi_cek = $paket->getField("PAKET_METODE_EVALUASI_ID"); // 2-Sistem Nilai, 7-Sistem Harga Terendah
$paket_jenis_cek = $paket->getField("PAKET_JENIS_ID"); // 1-PK, 2-JASKON, 3-B, 4-JL

$paket_user_id = $paket->getField("USER_LOGIN_ID");
$alasan = $paket->getField("ALASAN");
$alasan_ulang = $paket->getField("ALASAN_ULANG");
$multi_pemenang = $paket->getField("MULTI_PEMENANG");
$ppk = $paket->getField("PPK");
// 1:Tender, 2:Pengadaan langsung, 3:Tender Terbatas, 4:Seleksi Langsung, 5:Penunjukan Langsung, 6:e-Purchasing, 7:Tender Cepat, 8:Kompetisi, 9:Pembelian Langsung Offline, 10:Tender Kualifikasi, 11:Penunjukan Langsung Khusus
$paket_metode_lelang_id = $paket->getField("PAKET_METODE_LELANG_ID");
if (($paket_metode_lelang_id != '1' && $paket_metode_lelang_id != '3' && $paket_metode_lelang_id != '7' && $paket_metode_lelang_id != '10' ) && $this->USER_TYPE_ID == "") { // selain Tender harus login
	redirect(base_url());
} else {
	if ($paket_metode_lelang_id != '1')
	{
		// 1:administrator, 3:Panitia, 6:Penyedia, 9:Perencana, 10:Audit, 11:Pembeli
		switch ($paket_metode_lelang_id) {
			case '2': // Pengadaan Langsung
			case '5': // Penunjukan Langsung
			case '8': // Kompetisi
				if ($this->USER_TYPE_ID != '1' && $this->USER_TYPE_ID != '3' && $this->USER_TYPE_ID != '6' && $this->USER_TYPE_ID != '9' && $this->USER_TYPE_ID != '10')
				{
					redirect(base_url());
				}
				break;

				case '6': // Purchasing
        case '9': // Pembelian Langsung Offline
				case '12': // Purchasing Pemerintah
					if ($this->USER_TYPE_ID != '1' && $this->USER_TYPE_ID != '3' && $this->USER_TYPE_ID != '9' && $this->USER_TYPE_ID != '6' && $this->USER_TYPE_ID != '11')
					{
						if ($ppk != $this->USER_LOGIN_ID) {
							$this->load->model("UserLogin");
							$user_login_jabatan = new UserLogin();
							$user_login_jabatan->selectByParams(array("USER_LOGIN_ID" => $this->USER_LOGIN_ID));
							$user_login_jabatan->firstRow();
							if ($user_login_jabatan->getField('PENUNJUK_PIC') != '1') {
								redirect(base_url());
							}
						}
					}
					break;

			default:
			// redirect(base_url());
				break;
		}
	}
}
 
$paket_metode_nama = $paket->getField("METODE_LELANG");
$reqPermohonanId = $paket->getField("PERMOHONAN_PAKET_ID");
?>

<style type="text/css">
  small { font-weight: bold; font-size: 11.5px }
  a.list-group-item { color: #000 !important; }
  .list-group-item { padding: 0.5rem 1.25rem !important; border: transparent !important; }
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
</script>

<div class="row">
  <?php
  if ((int)$this->USER_TYPE_ID != '')
  { // Untuk user login ?>
  <div class="col-md-3 col-sm-3">

		<div class="list-group">
			<a class="btn-primary text-white list-group-item disabled" style="color:#fff !important;"> Info Detail <?= $paket_metode_nama ?> </a>
        <?php
        if($ppk == $this->USER_LOGIN_ID) // KEPALA PENGADAAN
        {
          // if($this->USER_LOGIN_ID == $paket_user_id || $this->USER_TYPE_ID == 7) // KEPALA PENGADAAN
          if($ppk == $this->USER_LOGIN_ID)
          {
            // untuk label wajib dilengkapi dan lengkap
            $this->load->model("Metode");
            $this->load->model("Paket");
            $this->load->model("PaketDokumen");
            $this->load->model("PaketPanitia");
            $this->load->model("PaketEvaluasiAdminTawar");
            $this->load->model("PaketEvaluasiTeknisTawar");
            $this->load->model("PaketEvaluasiHargaTawar");
						$this->load->model("PaketEvaluasiKualifikasi");
            $this->load->model("Paketpemenang");
            $this->load->model("PaketBidangUsaha");

            $metode_c = new Metode();
            $paket_c = new Paket();
            $paket_dokumen_c = new PaketDokumen();
    				$paket_dokumen_k = new PaketDokumen();
            $paket_panitia_c = new PaketPanitia();
            $paket_evaluasi_admin_count_c = new PaketEvaluasiAdminTawar();
            $paket_evaluasi_teknis_count_c = new PaketEvaluasiTeknisTawar();
            $paket_evaluasi_harga_count_c = new PaketEvaluasiHargaTawar();
						$paket_evaluasi_kualifikasi_count = new PaketEvaluasiKualifikasi();
            $getpaket_pemenang_c = new Paketpemenang();
            $paket_bidang_usaha_c = new PaketBidangUsaha();

            $countJadwal = $metode_c->getCountByParams(array("PAKET_ID" => $reqId));
            $countDokumen = $paket_dokumen_c->getCountByParams(array("PAKET_ID" => $reqId, "JENIS_DOKUMEN" => "LELANG"));
    				$countDokumenKualifikasi = $paket_dokumen_k->getCountByParams(array("PAKET_ID" => $reqId, "JENIS_DOKUMEN" => "KUALIFIKASI"));
            $countPanitia = $paket_panitia_c->getCountByParams(array("PAKET_ID" => $reqId));
            $countPaket = $paket_c->getCountByParams(array("A.PAKET_ID" => coalesce($reqId, 0)));
            $countPaketEvaluasiAdminCOunt = $paket_evaluasi_admin_count_c->getCountByParams(array("PAKET_ID" => $reqId));
            $countPaketEvaluasiTeknisCOunt = $paket_evaluasi_teknis_count_c->getCountByParams(array("PAKET_ID" => $reqId));
            $countPaketEvaluasiHargaCOunt = $paket_evaluasi_harga_count_c->getCountByParams(array("PAKET_ID" => $reqId));
						$countPaketEvaluasiKualifikasi = $paket_evaluasi_kualifikasi_count->getCountByParams(array("PAKET_ID" => $reqId));
            $countPemenang = $getpaket_pemenang_c->getCountByParams(array("A.PAKET_ID" => $reqId));
            $countBidangUsaha = $paket_bidang_usaha_c->getCountByParams(array("PAKET_ID" => coalesce($reqId, 0)));
 
            // if ($paket_metode_lelang_id == '1' || $paket_metode_lelang_id == '7') {
            if ($paket_metode_lelang_id != '6' && $paket_metode_lelang_id != '9' && $paket_metode_lelang_id != '12') { // selain e-Purchasing & Pembelian offline & e-Purchasing Pemerintah
            ?>
            <a href="main/index/paket_lelang_tambah_daftar_panitia/?reqId=<?=$reqId?>" class="list-group-item"> <span class="fa fa-angle-double-right"></span> Tim Pengadaan
              <?php if ($countPanitia == 0) {
              	if ($paket_metode_lelang_id == '1' || $paket_metode_lelang_id == '3' || $paket_metode_lelang_id == '4' || $paket_metode_lelang_id == '8' || $paket_metode_lelang_id == '10') {
              		echo $wajib;
              	} else {
              		echo $optional;
              	}
              } else { echo $sudahwajib; }  ?>
            </a>
            <?php
            }

            // ---------------  PEMBELIAN LANGSUNG ----------------
            if ($paket_metode_lelang_id == '6')
            { // Purchasing/Pembelian Langsung
              $this->load->model("Katalogrekanan");
              $katalogrekananRow = new Katalogrekanan();
              $katalogrekananRow->selectByParams(array('A.PAKET_ID' => $reqId));
              $katalogrekananRow->firstRow();
            ?>
            <a href="main/index/katalog_cart/?reqId=<?=$reqId?>" class="list-group-item"> <span class="fa fa-angle-double-right"></span> Daftar Produk </a>
            <a href="main/index/katalog_negosiasi/?reqId=<?=$reqId?>" class="list-group-item"> <span class="fa fa-angle-double-right"></span> Negosiasi </a>
            <?php
            if ($katalogrekananRow->getField('STATUS') >= 2 ) { 
            $paket_ppk = new Paket();
            $paket_ppk->selectByParams(array("A.PAKET_ID" => coalesce($reqId, 0)));
            $paket_ppk->firstRow();
            $ppknya = $paket_ppk->getField("PPK");
            ?> 

            <a href="main/index/katalog_surat_pesanan/?reqId=<?=$reqId?>" class="list-group-item"> <span class="fa fa-angle-double-right"></span> Surat Pesanan </a>

            <?php
            } ?>
            <?php
            if ($katalogrekananRow->getField('STATUS') >= 4 ) { ?>
            <a href="main/index/katalog_tracking_pesanan/?reqId=<?=$reqId?>" class="list-group-item"> <span class="fa fa-angle-double-right"></span> Tracking Pesanan </a>
            <?php
            } ?>
            <?php
            if ($katalogrekananRow->getField('STATUS') != 6 ) {
	     				if(trim($alasan_ulang) == "" && trim($alasan) == "")
              {
	    			?>
            <!-- <a onClick="openAdd('main/loadUrl/main/paket_lelang_batal/?reqId=<?=$reqId?>');" class="list-group-item"> <span class="fa fa-angle-double-right"></span>  Batalkan Paket </a> -->
            <?php
	      			}
             }
            }

            // ---------------  PEMBELIAN OFFLINE ----------------
            if ($paket_metode_lelang_id == '9' || $paket_metode_lelang_id == '12')
            { // Pembelian Offline
            ?>
            <a href="main/index/purchasing_file/?reqId=<?=$reqId?>" class="list-group-item"> <span class="fa fa-angle-double-right"></span> Upload Dokumen </a>
            <?php
            if(trim($alasan_ulang) == "" && trim($alasan) == "")
            { ?>
				    <!-- <a onClick="openAdd('main/loadUrl/main/paket_lelang_batal/?reqId=<?=$reqId?>');" class="list-group-item"> <span class="fa fa-angle-double-right"></span>  Batalkan Paket </a> -->
			            <?php
				     }
            } ?>

          <?php
          } 
          }
    

				if((int)$this->USER_TYPE_ID != 6)
        { // selain Penyedia
        ?>
					<a onclick="openAddLg('main/loadUrl/main/rekam_jejak_view?id=<?= $reqPermohonanId ?>&paketid=<?= $reqId ?>')" class="list-group-item"> <span class="fa fa-angle-double-right"></span>  Rekam Jejak </a>
				<?php
				} ?>

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
          <div class="alert alert-icon-left alert-arrow-left alert-info mb-2" role="alert">
            <span class="alert-icon"><i class="fa fa-info"></i></span>
            <h4 style="color: #000; font-weight: bold"><?=$paket->getField("NAMA")?></h4>
          </div>
          <table class="table table-bordered table-hover">
            <tbody>
            	<?php
            	if ($paket_metode_lelang_id == '2' || $paket_metode_lelang_id == '3' || $paket_metode_lelang_id == '5')
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
                  <?=$paket->getField("PAKET_JENIS")?>
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
              if ($paket_metode_lelang_id != '6' && $paket_metode_lelang_id != '9' && $paket_metode_lelang_id != '12')
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
              if ($paket_metode_lelang_id != '6' && $paket_metode_lelang_id != '9'  && $paket_metode_lelang_id != '12')
              { // bukan Purchasing/Pembelian Langsung & bukan pembelian offline ?>
              <tr>
                <td width="25%" colspan="4">
                  <small><i class="fa fa-file-text"></i> Kualifikasi Usaha</small> <br>
                  <?=$paket->getField("REKANAN_KUALIFIKASI")?>
                </td> 
              </tr>
              <?php
              } 

              if ($paket_metode_lelang_id == '1' || $paket_metode_lelang_id == '3' || $paket_metode_lelang_id == '10') // ditampilkan hanya untuk Tender
              {
              ?>
              <tr>
                <td width="25%" colspan="4">
                  <small><i class="fa fa-money"></i> Harga Perkiraan</small> <br>
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
                  <small><i class="fa fa-money"></i> Harga Perkiraan</small> <br>
                  <?=$paket->getField("NILAI_MATA_UANG")?> <?=currencyToPage($paket->getField("NILAI_OWNER_ESTIMATE"))?>
                </td>
                </td>
              </tr>
              <?php
                }
              }
              // }
              ?>
              <?php
              if ($paket_metode_lelang_id != '6' && $paket_metode_lelang_id != '9' && $paket_metode_lelang_id != '12')
              { // bukan Purchasing/Pembelian Langsung & bukan pembelian offline ?>
              <tr>
                <td width="25%" colspan="4">
                  <small><i class="fa fa-suitcase"></i> Bidang / Sub Bidang</small><br>
                  <?php if(trim($paket->getField("BIDANG_USAHA")) == "()")
                      echo "-";
                     else
                      echo str_replace("---"," <br/> ", $paket->getField("BIDANG_USAHA"));
                      // echo $paket->getField("BIDANG_USAHA"); ?>
                </td>
              </tr>
              <tr>
                <td width="25%" colspan="4">
                  <small><i class="fa fa-th-list"></i> Persyaratan Peserta</small><br>
                  <?=$paket->getField("URAIAN")?>
                </td>
              </tr>
              <?php
              } else { ?>
                <tr>
                  <td width="25%" colspan="4">
                    <small><i class="fa fa-th-list"></i> Keterangan</small><br>
                    <?=$paket->getField("URAIAN")?>
                  </td>
                </tr>
              <?php
            } 
            ?>
            </tbody>
          </table> 

          <!-- </form> -->
          <div class="form-actions">
            <?php
            if ($this->USER_TYPE_ID != '6') {
              // 1-e-Tender, 3-Tender Terbatas ,7-e-Tender Cepat, 2-Pengadaan Langsung ,5-Penunjukan Langsung Cepat, 6-Pembelian langsung
              switch ($paket_metode_lelang_id) {
                case '1':
                case '3':
                case '7':
                case '10':
                  echo '<a href="main/index/tender" class="'.CLASS_BTN_DANGER.' mr-1"> '.BTN_KEMBALI.' </a>';
                  break;
                case '2':
                case '5':
                case '11':
                  echo '<a href="main/index/tendernon" class="'.CLASS_BTN_DANGER.' mr-1"> '.BTN_KEMBALI.' </a>';
                  break;
                case '6':
                  if ($this->USER_TYPE_ID == '11') {
                    echo '<a href="main/index/pembelian_langsung" class="'.CLASS_BTN_DANGER.' mr-1"> '.BTN_KEMBALI.' </a>';
                  }
                  break;

                default:
                  break;
              }
            }
             ?>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
