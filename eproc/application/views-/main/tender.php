<?php
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 *
 */

/**
 * 1:Tender Umum
 * 3:Tender Terbatas
 * 4:Seleksi Langsung
 * 7:Tender Cepat
 * 10:Tender Prakualifikasi
**/

include_once("functions/default.func.php");
include_once("functions/date.func.php");
include_once("functions/string.func.php");

$this->load->library("paketinfo"); $paketInfo = new paketinfo();
$this->load->library("Pagination");
$this->load->model("Paket");
$this->load->model("PaketRekanan");
$this->load->model("PaketPanitia");
$this->load->model("Paketpemenang");
$this->load->model("PaketTahap");

$paket = new Paket();

$showRecord = 5;
$pageView = "paket_json/tender/";

if($this->USER_TYPE_ID == 6) // Rekanan
{
  $arrStatement = array("JENIS_PENGADAAN" => "LELANG", "PUBLISH_PAKET" => "1", "A.PAKET_METODE_LELANG_ID|| IN " => "(1,3,7,10)");
  $rowCount = $paket->getCountByParamsPaketRekanan3($arrStatement, $this->REKANAN_ID);
  $paket->selectByParamsPaketRekanan($arrStatement, $showRecord, 0, $this->REKANAN_ID);
}
else
{
  if((int)$this->USER_TYPE_ID == 3 || $this->USER_TYPE_ID == 7 || $this->USER_TYPE_ID == 9 || $this->USER_TYPE_ID == 10)
  // 3:PANITIA, 7:KEPALA PENGADAAN,9:PERENCANA, 10:AUDITOR
    $arrStatement = array("JENIS_PENGADAAN" => "LELANG", "A.PAKET_METODE_LELANG_ID|| IN " => "(1,3,7,10)");
  else
    $arrStatement = array("JENIS_PENGADAAN" => "LELANG", "PUBLISH_PAKET" => "1", "A.PAKET_METODE_LELANG_ID|| IN " => "(1,3,7,10)");

  // $rowCount = $paket->getCountByParams($arrStatement, $this->REKANAN_ID);
  $rowCount = $paket->getCountByParams($arrStatement);
  $paket->selectByParamsMonitoring($arrStatement, $showRecord, 0);
  //echo $paket->query;exit;
}
	// echo $paket->query;exit;

$arrSerialized = serialize($arrStatement);
$arrSerialized = str_replace('"', '@', $arrSerialized);
$pagConfig = array('baseURL'=>$pageView, 'showRecord' => $showRecord, 'totalRows'=>$rowCount, 'perPage'=>$showRecord, 'contentDiv'=>'tbodyPaketLelang', 'arrSerialized' => $arrSerialized, 'searchVarible' => "reqPencarian");
// echo "<pre>"; print_r($pagConfig); die();
$pagination =  new Pagination($pagConfig);

?>

<?php
if($this->USER_TYPE_ID == "3") //PANITIA
{
?>
  <script>
  function updatePublishPaket(id,key)
  {

    if($('#reqPublish' + id).is(":checked")) {
      msg = "Apakah anda benar ingin Publish Paket ini?";
      $reqPub = 1;
    }
    else {
      msg = "Batalkan publish paket?";
      $reqPub = 0;
    }

    $.messager.confirm('Konfirmasi',msg,function(r){
      if (r){
        $.get( "paket_json/set_publish_paket/?reqId="+id+"&reqPub="+$reqPub, function( data ) {
          var n = data.split('--');
          $.messager.alert('Informasi',n[1], 'info');
          if(n[1] == "Paket belum divalidasi semua panitia.") {
            $('#reqPublish' + id).prop('checked', false);
          }

          if($('#reqPublish' + id).is(":checked")) {
            if (n[0] == '1') {
              $('#reqPublish' + id).prop('checked', false);
              setTimeout(function() {
                window.location.href = "<?= base_url('main/index/paket_detil/?eid=') ?>"+id+"&key="+key;
              }, 2000);
            } else {
              $('#reqPublish' + id).prop('checked', true);
            }
          }
          else {
          }
        });

      }
      else
      {
        if($('#reqPublish' + id).is(":checked"))
          $('#reqPublish' + id).prop('checked', false);
        else
          $('#reqPublish' + id).prop('checked', true);
      }

    });

  }

  function validasiPublish(id)
  {
  	msg = "Validasi publish paket?";

  	$.messager.confirm('Konfirmasi',msg,function(r){
  		if (r){
  			$.get( "paket_json/set_validasi_publish_paket/?reqId="+id, function( data ) {
  			  $.messager.alertReload('Informasi',data, 'info');
  			});
  		}
  	});
  }


  </script>
<?php
}
?>

<script type="text/javascript">
function setSort(a) {
  $('#reqPencarian2').val(a);
  $('#submitCari').click();
 }
</script>

<style type="text/css">
#tbodyPaketLelang .col-md-12 .border-darken-1 a.hver:hover {
  color: #da4453 !important;
  cursor: pointer;
}
#tbodyPaketLelang .col-md-12 .border-darken-1:hover {
  border:  1px solid #da4453 !important;
  cursor: pointer;
}
</style>

<section id="backColor">
  <div class="row">

    <div class="col-md-12 col-sm-12">
      <div class="card border-bottom-primary" style="zoom: 1;">
        <div class="card-header">
          <!-- <h4 class="card-title">Tender</h4> -->
           <?php if($this->USER_TYPE_ID == "3") { // PANITIA  ?>
               <!-- <a href="main/index/paket_tambah" class="btn btn-primary"><i class="fa fa-check-square-o"></i> Tambah</a> -->
               <a title="#" onClick="openAdd('main/loadUrl/main/cetak_filter_tahun?reqMetode=tender');" class="<?= CLASS_BTN_INFO ?> mr-1 text-white" ><?= BTN_PRINT ?></a>
            <?php } ?>
          <div class="heading-elements">
            <ul class="list-inline mb-0">
              <li><a data-action="expand"><i class="ft-maximize"></i></a></li>
            </ul>
          </div>
        </div>
        <div class="card-body">
          <div class="row-fluid">
          </div>

          <div class="row">
            <div class="col-md-1 d-none d-sm-block">
              <input type="hidden" id="reqPencarian2" class="form-control" value="all">

              <div class="dropdown">
                <button class="btn btn-block btn-info round dropdown-menu-right dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                  <i class="ft-settings icon-left sm-hide pull-left"></i>
                </button>
                <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                  <a class="dropdown-item" onclick="setSort('all')">Tampilkan semua</a>
                  <a class="dropdown-item" onclick="setSort('0')">Tender Selesai</a>
                  <a class="dropdown-item" onclick="setSort('1')">Tender Berjalan</a>
                  <a class="dropdown-item" onclick="setSort('2')">Tender Batal</a>
                  <a class="dropdown-item" onclick="setSort('3')">Tender Gagal</a>
                </div>
              </div>
            </div>
            <div class="col-md-11">
              <div class="card-block">
                <fieldset>
                  <div class="input-group">
                    <input type="text" id="reqPencarian" class="form-control" placeholder="Cari Tender . . .">
                    <div class="input-group-append">
                      <button class="btn btn-danger" id="submitCari" onClick="<?=$pagination->createSearching2();?>" type="submit">Cari</button>
                    </div>
                  </div>
                </fieldset>
              </div>
            </div>
          </div>


          <div class="card-content collapse show" style="margin-top: 2%">
            <div class="table-responsive">

              <form id="ff" class="easyui-form form-horizontal" method="post" data-options="novalidate:true" enctype="multipart/form-data">
                <div id="tbodyPaketLelang">
                   <?php
                   $nomor=1;
                    while($paket->nextRow())
                    {
                      if(trim($paket->getField("ALASAN")) == "") { $batal = 0; } else { $batal = 1; }
                      if(trim($paket->getField("ALASAN_ULANG")) == "") { $batal_ulang = 0; } else { $batal_ulang = 1; }

                      if($batal == 1 || $batal_ulang == 1) {
                       $border = 'border-danger';
                       $style = 'color: red';
                      } else {
                       $border = 'border-primary';
                       $style = 'color: black';
                      }

                   ?>
                    <div class="col-md-12" style="padding:0px !important;">
                      <div class="card mb-1 <?= $border ?> border-darken-1">
                          <div class="p-1">
                            <a class="hver" style="<?= $style ?>" href="main/index/paket_detil/?eid=<?=$paket->getField("PAKET_ID")?>&key=<?=$paket->getField("PAKET_UUID")?>" >
                              <p class="mb-0">
                                <small style="font-size: 10px;">
                                  <i>
                                  <?php
                                  $this->load->library("libgeneratecode");
                                  $libgeneratecode = new libgeneratecode();
                                  echo "No. Paket: ".$libgeneratecode->nomorPaket($paket->getField("PAKET_ID"),$paket->getField("PAKET_METODE_LELANG_ID"));
                                  ?>
                                  </i>
                                </small>
                              </p>
                              <p class="mb-0">
                                <strong style="font-size: 1em"><?php // $nomor; ?> <?=strtoupper($paket->getField("NAMA"))?></strong>
                              </p>
                              <p class="mb-0">
                                <?php
                                if((int)$this->USER_TYPE_ID == 3)
                                { ?>
                                <small style="margin-right: 2%;"><i class="fa fa-bookmark"></i> RUP: <?=$paket->getField("KODE_rup")?></small>
                                <small style="margin-right: 2%;"><i class="fa fa-bookmark-o"></i> PR: <?=$paket->getField("KODE_PR")?></small>
                                <?php
                                } ?>
                                <small style="margin-right: 2%;"><i class="fa fa-calendar"></i> Tahun Anggaran: <?=getYear($paket->getField("TAHUN_ANGGARAN"))?></small>
                                <small style="margin-right: 2%;"><i class="fa fa-money"></i> Harga Perkiraan Sendiri
                                <?=$paket->getField("NILAI_MATA_UANG")?> <?=currencyToPage($paket->getField("NILAI_OWNER_ESTIMATE"))?></small>
                                <small style="margin-right: 2%;"><i class="fa fa-tag"></i> Metode Pengadaan:  <?=getYear($paket->getField("METODE_LELANG"))?></small>
                                <small style="margin-right: 2%;"><i class="fa fa-map-marker"></i> Lokasi Pekerjaan:  <?=getYear($paket->getField("LOKASI"))?></small>
                              </p>
                              <?php
                              $tahap = '';
                              $paket_tahap_tender = new PaketTahap();
                              $paket_tahap_tender->selectByJawdalAktif(array("A.PAKET_ID" => $paket->getField("PAKET_ID")), -1, -1);
                              while($paket_tahap_tender->nextRow())
                              {
                                $tahap .= '<span class="badge badge-primary mt-1">'.$paket_tahap_tender->getField("NAMA").'</span> &nbsp;';
                               ?>
                               <!-- <p class="mb-0  mt-1">
                                <span class="badge badge-warning" style="color: #000">
                                  Tahap : <?= $paket_tahap_tender->getField("NAMA") ?>
                                </span>
                               </p>  -->
                               <?php
                               }
                               if ($tahap != '') {
                                 echo '<span class="badge badge-danger mt-1">Tahap: </span> '. $tahap;
                                }
                               ?>
                              </a>
                              <p class="mb-0 mt-1">
                                <?php if($batal == 1) { ?>
                                 <div class="col-md-12 mt-1 mb-0" style="text-align:left; background-color:#da4453; color:#fff; font-weight: 400; font-size:85%; padding:.35em .4em !important; border-radius: .21rem !important">
                                  <i class="fa fa-remove"></i> Paket Dibatalkan
                                  <?php
                                  // if((int)$this->USER_TYPE_ID == 3) {
                                    echo '<br>Alasan: '.$paket->getField('ALASAN');
                                  // } ?>
                                 </div>
                                <?php }

                                if($batal_ulang == 1) {
                                ?>
                                 <div class="col-md-12 mt-1 mb-0" style="text-align:left; background-color:#da4453; color:#fff; font-weight: 400; font-size:85%; padding:.35em .4em !important; border-radius: .21rem !important">
                                  <i class="fa fa-refresh"></i> Paket Gagal
                                  <?php
                                  // if((int)$this->USER_TYPE_ID == 3) {
                                    echo '<br>Alasan: '.$paket->getField('ALASAN_ULANG');
                                  //} ?>
                                 </div>
                                <?php }

                                $akunBlackList = '';
                                if($this->USER_TYPE_ID)
                                {
                                  /* STATUS PENDAFTARAN REKANAN */
                                  $arrPengaftaran = PENDAFTARAN;
                                  $paket_tahap_metode = new PaketTahap();
                                  $jenis_tahap = $paket_tahap_metode->getJenisTahapById($paket->getField("PAKET_ID"));

                                  // Check User Apakah Masuk dalam Blacklist atau tidak dengan kurun waktu tertentu
                                  $this->load->model("Users");
                                  $user_login = new Users();
                                  $cekBlacklist = new Users();
                                  $cekBlacklist->selectBlacklistByRekananId($this->ID);
                                  $cekBlacklist->firstRow();
                                  $akunBlackList = $cekBlacklist->getField("BLACKLIST_ID");

                                  if($this->USER_TYPE_ID == "6" && $akunBlackList == '')
                                  {

                                    $paket_mengikuti1 = new Paket();
                                    $mengikuti = $paket_mengikuti1->getPaketMengikuti($this->REKANAN_ID, $paket->getField("PAKET_ID"));
                                    $pendaftaran = 0;
                                    if($mengikuti == 0)
                                    {

                                     $paket_pendaftaran1 = new Paket();
                                     $pendaftaran = $paket_pendaftaran1->getPaketPendaftaran($paket->getField("PAKET_ID"),$arrPengaftaran[$jenis_tahap]);
                                    }
                                    $validasi = 0;

                                    if ($paket->getField("PAKET_METODE_LELANG_ID") == "1" || $paket->getField("PAKET_METODE_LELANG_ID") == "4" || $paket->getField("PAKET_METODE_LELANG_ID") == "7" || $paket->getField("PAKET_METODE_LELANG_ID") == "10") {
                                      if($mengikuti == 1)
                                      {
                                       echo "<span class=\"badge badge-info\">
                                              <i class=\"fa fa-check\"></i> Anda telah mendaftar paket ini
                                             </span><br>";
                                       $validasi = 1;
                                      }
                                      elseif($pendaftaran == 0)
                                      {
                                       echo "<span class=\"badge badge-danger\">
                                              <span class=\"fa fa-info\"></span> Anda tidak dapat mendaftar paket ini. Waktu pendaftaran belum dimulai atau sudah berakhir
                                             </span><br>";
                                      }
                                    }
                                  }

                                  /* STATUS PEMBUAT PAKET PANITIA */
                                  if($this->USER_TYPE_ID == 3 || $this->USER_TYPE_ID == 7)
                                  {
                                  ?>
                                    <span class="badge badge-info">
                                      <i class="fa fa-cog"></i> Pembuat Paket : <strong><?=$paket->getField("USER_LOGIN")?></strong>
                                    </span>
                                  <?php
                                  } else
                                  {
                                   $pendaftaran = 0;
                                   $paket_mengikuti = new Paket();
                                   $mengikuti = $paket_mengikuti->getPaketMengikuti($this->REKANAN_ID, $paket->getField("PAKET_ID"));
                                   if($mengikuti == 0)
                                   {
                                    $paket_pendaftaran = new Paket();
                                    $pendaftaran = $paket_pendaftaran->getPaketPendaftaran($paket->getField("PAKET_ID"),$arrPengaftaran[$jenis_tahap]);
                                    if($pendaftaran == 1 && ($paket->getField("PAKET_METODE_LELANG_ID") == 1 || $paket->getField("PAKET_METODE_LELANG_ID") == 3 || $paket->getField("PAKET_METODE_LELANG_ID") == 4))
                                    {
                                     if($this->USER_LOGIN_ID == "")
                                     { }
                                    }
                                   } else
                                   {
                                    /* jika sudah mengikuti cek apakah gagal */
                                    $paket_rekanan_lulus = new PaketRekanan();
                                    $lulus_pendaftaran = $paket_rekanan_lulus->getLulusPendaftaran($this->REKANAN_ID, $paket->getField("PAKET_ID"));
                                    if($lulus_pendaftaran == "0")
                                    {
                                     $paket_pendaftaran = new Paket();
                                     $pendaftaran = $paket_pendaftaran->getPaketPendaftaran($paket->getField("PAKET_ID"),$arrPengaftaran[$jenis_tahap]);
                                    }
                                   }
                                  }

                                  /* CENTANG PUBLISH PAKET */
                                  if((int)$this->USER_TYPE_ID == 3 || (int)$this->USER_TYPE_ID == 7)
                                  {
                                  ?>
                                   <?php
                                   // if((int)$this->USER_TYPE_ID == 3 && $paket->getField("USER_LOGIN_ID") == $this->USER_LOGIN_ID && ($paket->getField("PAKET_METODE_LELANG_ID") == "1" ||  $paket->getField("PAKET_METODE_LELANG_ID") == "2" ||  $paket->getField("PAKET_METODE_LELANG_ID") == "5" ||  $paket->getField("PAKET_METODE_LELANG_ID") == "7"))
                                   if((int)$this->USER_TYPE_ID == 3 && $paket->getField("USER_LOGIN_ID") == $this->USER_LOGIN_ID && ($paket->getField("PAKET_METODE_LELANG_ID") == "1" ||  $paket->getField("PAKET_METODE_LELANG_ID") == "3" ||  $paket->getField("PAKET_METODE_LELANG_ID") == "7" ||  $paket->getField("PAKET_METODE_LELANG_ID") == "10"))
                                   {
                                    $paket_panitia_belum_validasi = new PaketPanitia();
                                      $paket_panitia_belum_validasi->selectByParams(array("A.PAKET_ID" => $paket->getField("PAKET_ID"), "VALIDASI_PUBLISH" => "0"));
                                      if ($paket_panitia_belum_validasi->countRow() > 0 ) {
                                        echo '<span class="badge badge-danger" style="margin-top: .2%; cursor: pointer" onclick="openAdd(\'main/loadUrl/notif/validasi_tim_pengadaan?reqId='.$paket->getField("PAKET_ID").'\')">
                                              <span class="fa fa-exclamation-triangle"></span>
                                                '.$paket_panitia_belum_validasi->countRow().' Anggota Dalam Tim Pengadaan Belum Validasi
                                              </span>
                                              ';
                                      } else {
                                   ?>
                                   <span class="badge badge-secondary" style="margin-top: .2%">
                                    <input type="checkbox" style="cursor: pointer;" name="reqPublish" id="reqPublish<?=$paket->getField("PAKET_ID")?>" onclick="updatePublishPaket('<?=$paket->getField("PAKET_ID")?>','<?=$paket->getField("PAKET_UUID")?>')" <?php if($paket->getField("PUBLISH_PAKET") == 1) { ?>  checked="checked" <?php } ?> />
                                    <small><b>Publish</b></small>
                                    </span>
                                    <?php
                                      $paket_panitia_belum_validasiTotal = new PaketPanitia();
                                      $paket_panitia_belum_validasiTotal->selectByParams(array("A.PAKET_ID" => $paket->getField("PAKET_ID")));
                                      if ($paket_panitia_belum_validasiTotal->countRow() > 0 ) { ?>
                                    <span class="badge badge-success" style="margin-top: .2%; cursor: pointer" onclick="openAdd('main/loadUrl/notif/validasi_tim_pengadaan?reqId=<?= $paket->getField("PAKET_ID")?>')"> Anggota dalam Tim Pengadaan Sudah Validasi </span>
                                   <?php
                                      }
                                    }
                                   }
                                   if(($paket->getField("PAKET_METODE_LELANG_ID") == "1" ||  $paket->getField("PAKET_METODE_LELANG_ID") == "2" || $paket->getField("PAKET_METODE_LELANG_ID") == "3" || $paket->getField("PAKET_METODE_LELANG_ID") == "4" || $paket->getField("PAKET_METODE_LELANG_ID") == "5" ||  $paket->getField("PAKET_METODE_LELANG_ID") == "7" || $paket->getField("PAKET_METODE_LELANG_ID") == "8" || $paket->getField("PAKET_METODE_LELANG_ID") == "10" || $paket->getField("PAKET_METODE_LELANG_ID") == "11"))
                                   {
                                    /* CHECK APAKAH PANITIA */
                                    $paket_panitia = new PaketPanitia();
                                    $adaValidasi = $paket_panitia->getCountByParams(array("PAKET_ID" => $paket->getField("PAKET_ID"), "NIP" => $this->NIP, "VALIDASI_PUBLISH" => "0"));
                                    if($adaValidasi > 0)
                                    {
                                   ?>
                                   <span class="badge badge-danger" style="margin-top: .2%">
                                     <span class="fa fa-exclamation-triangle"></span> <a onClick="validasiPublish('<?=$paket->getField("PAKET_ID")?>')"> Validasi untuk publish ? </a>
                                   </span>
                                   <?php
                                     }
                                    }
                                  }

                                  /* TOMBOL PENDAFTARAN PAKET OLEH REKANAN */
                                  if($this->USER_TYPE_ID == 6 && $paket->getField("PAKET_METODE_LELANG_ID") == "3"  && $akunBlackList == '') // khusus untuk tender terbatas
                                  {
                                    if($mengikuti == 1)
                                    {
                                        echo "<span class=\"badge badge-info\">
                                              <i class=\"fa fa-check\"></i> Anda telah diundang mengikuti paket ini
                                             </span><br>";
                                    } else {
                                      echo "<span class=\"badge badge-danger\">
                                              <i class=\"fa fa-close\"></i> Anda tidak diundang mengikuti paket ini
                                             </span><br>";
                                    }
                                  }

                                  // 1-e-Tender, 3-Tender Terbatas,  4-Seleksi Langsung, 7-e-Tender Cepat, 8-Kompetisi,10-Tender Kualifikasi,
                                  if($this->USER_TYPE_ID == 6 && $paket->getField("PAKET_METODE_LELANG_ID") == "1" || $paket->getField("PAKET_METODE_LELANG_ID") == "4" || $paket->getField("PAKET_METODE_LELANG_ID") == "7" || $paket->getField("PAKET_METODE_LELANG_ID") == "10" && $akunBlackList == '')
                                  {
                                  ?>
                                   <div class="area-aksi-paket-lelang">
                                   <?php
                                   if($this->USER_TYPE_ID == 6 && $pendaftaran == 1) // jika login adalah Penyedia dan Pendaftaran diBUKA
                                   {
                                    $mengikuti = $paket_mengikuti->getPaketMengikuti($this->REKANAN_ID, $paket->getField("PAKET_ID"));
                                    if($mengikuti == 1)
                                    {
                                     if($lulus_pendaftaran == "0")
                                     {
                                    ?>
                                      <span class="badge badge-danger">
                                        <span class="fa fa-close"></span>Anda tidak memenuhi syarat pendaftaran
                                      </span>
                                    <?php
                                     }
                                    } else
                                    {
                                      // cek kualifikasi dan bidang usaha
                                      $this->load->model("PaketBidangUsaha");
                                      $nilaiHPS = ceil($paket->getField("NILAI")/3);
                                     if ($paket->getField("REKANAN_KUALIFIKASI_ID") != 3 )
                                     {  // 1=kecil, 2=Non-Kecil
                                      $bidang_usaha_rekanan = new PaketBidangUsaha();
                                      $bidang_usaha_rekanan3 = new PaketBidangUsaha();
                                      // -- dengan kriteria shortlist pengalaman
                                      // $statement = " AND REKANAN_KUALIFIKASI_ID = '".$paket->getField("REKANAN_KUALIFIKASI_ID")."' AND STATUS_VALIDASI=1 AND USER_STATUS=1 AND A.REKANAN_ID='".$this->REKANAN_ID."' AND NILAI > ".$nilaiHPS." AND A.rekanan_id not in (SELECT rekanan_id FROM blacklist where current_date between tanggal_mulai and tanggal_selesai )";
                                      if ($paket->getField("MULTI_BIDANG_USAHA") == '1') { // Keseluruhan terpenuhi
                                        $statement = " AND STATUS_VALIDASI=1 AND USER_STATUS=1 AND VALIDASI ='1' AND A.REKANAN_ID='".$this->REKANAN_ID."' AND A.rekanan_id not in (SELECT rekanan_id FROM blacklist where current_date between tanggal_mulai and tanggal_selesai ) GROUP BY A.rekanan_id
                                            HAVING COUNT(DISTINCT A.bidang_usaha_id) = (
                                                SELECT COUNT(DISTINCT bidang_usaha_id)
                                                FROM paket_bidang_usaha
                                                WHERE paket_id = ".$paket->getField("PAKET_ID")."
                                            )";
                                        $cekKualifikasi = $bidang_usaha_rekanan3->selectByParamsRekananCountDaftar2(array(), -1, -1, $paket->getField("PAKET_ID"), $statement);
                                      } else { // Salah satu terpenuhi
                                        $statement = " AND REKANAN_KUALIFIKASI_ID = '".$paket->getField("REKANAN_KUALIFIKASI_ID")."' AND STATUS_VALIDASI=1 AND USER_STATUS=1 AND VALIDASI ='1' AND A.REKANAN_ID='".$this->REKANAN_ID."' AND A.rekanan_id not in (SELECT rekanan_id FROM blacklist where current_date between tanggal_mulai and tanggal_selesai )";
                                        $cekKualifikasi = $bidang_usaha_rekanan->selectByParamsRekananCountDaftar(array(), -1, -1, $paket->getField("PAKET_ID"), $statement);
                                      }
                                      if ($cekKualifikasi > 0)
                                      { // Generate reqPaketId dibuat dari ID + PAKET_ID
                                        if($batal != 1 && $batal_ulang != 1)
                                        {
                                    ?>
                                     <span>
                                      <a href="main/index/registrasi_paket/?reqPaketId=<?=md5($this->REKANAN_ID.$paket->getField("PAKET_ID"))?>" class="<?= CLASS_BTN_DARK ?>"><i class="fa fa-gavel"></i> <?=translate("Daftar Paket Pengadaan ini ?", "Register")?></a>
                                     </span>
                                    <?php
                                        }
                                      } else {
                                        echo '<span class="badge badge-danger"><span class="fa fa-remove"></span> Anda tidak memenuhi syarat pendaftaran..</span>';
                                      }
                                     } else {
                                      //  3=Kecil / Non-Kecil
                                      $bidang_usaha_rekanan = new PaketBidangUsaha();
                                      $bidang_usaha_rekanan3 = new PaketBidangUsaha();
                                      // -- dengan kriteria shortlist pengalaman
                                      // $statement = " AND STATUS_VALIDASI=1 AND USER_STATUS=1 AND A.REKANAN_ID='".$this->REKANAN_ID."' AND NILAI > ".$nilaiHPS." AND A.rekanan_id not in (SELECT rekanan_id FROM blacklist where current_date between tanggal_mulai and tanggal_selesai )";
                                      if ($paket->getField("MULTI_BIDANG_USAHA") == '1') { // Keseluruhan terpenuhi
                                        $statement = " AND STATUS_VALIDASI=1 AND USER_STATUS=1 AND VALIDASI ='1' AND A.REKANAN_ID='".$this->REKANAN_ID."' AND A.rekanan_id not in (SELECT rekanan_id FROM blacklist where current_date between tanggal_mulai and tanggal_selesai ) GROUP BY A.rekanan_id
                                            HAVING COUNT(DISTINCT A.bidang_usaha_id) = (
                                                SELECT COUNT(DISTINCT bidang_usaha_id)
                                                FROM paket_bidang_usaha
                                                WHERE paket_id = ".$paket->getField("PAKET_ID")."
                                            )";
                                        $cekKualifikasi = $bidang_usaha_rekanan3->selectByParamsRekananCountDaftar2(array(), -1, -1, $paket->getField("PAKET_ID"), $statement);
                                      } else { // Salah satu terpenuhi
                                        $statement = " AND STATUS_VALIDASI=1 AND USER_STATUS=1 AND VALIDASI ='1' AND A.REKANAN_ID='".$this->REKANAN_ID."' AND A.rekanan_id not in (SELECT rekanan_id FROM blacklist where current_date between tanggal_mulai and tanggal_selesai )";
                                        $cekKualifikasi = $bidang_usaha_rekanan->selectByParamsRekananCountDaftar(array(), -1, -1, $paket->getField("PAKET_ID"), $statement);
                                      }
                                      if ($cekKualifikasi > 0) {
                                      // Generate reqPaketId dibuat dari ID + PAKET_ID
                                        if($batal != 1 && $batal_ulang != 1)
                                        {
                                      ?>
                                       <span >
                                        <a href="main/index/registrasi_paket/?reqPaketId=<?=md5($this->REKANAN_ID.$paket->getField("PAKET_ID"))?>" class="<?= CLASS_BTN_DARK ?>"><i class="fa fa-gavel"></i> <?php //echo $pendaftaran; ?> <?=translate("Daftar Paket Pengadaan ini ?", "Register")?></a>
                                       </span>
                                    <?php
                                        }
                                      } else {
                                        echo '<span class="badge badge-danger"><span class="fa fa-remove"></span> Anda tidak memenuhi syarat pendaftaran.</span>';
                                      }
                                     }
                                    }
                                   }
                                   ?>
                                   </div>
                                <?php
                                  }
                                }
                                ?>
                              </p>
                          </div>
                        <!-- </a> -->
                      </div>
                    </div>

                   <?php
                   $nomor++;
                   } // end of while($paket->nextRow())
                   ?>
                  <?php echo $pagination->createLinks2()?>
                </div>
              </form>

            </div>
          </div>


        </div>
      </div>
    </div>

  </div>
</section>

<script type="text/javascript">
function sortby(a) {
  alert(a);
}
</script>
