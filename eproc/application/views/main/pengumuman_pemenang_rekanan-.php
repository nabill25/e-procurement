<?php
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 * 
 */
$this->libsession->cekSession('free');   

// if($this->USER_TYPE_ID != "6")
//     redirect("app");

$this->load->library("paketinfo"); $paketInfo = new paketinfo();
include_once("functions/string.func.php");
include_once("functions/default.func.php");
include_once("functions/date.func.php");
$this->load->model("Paket");
$this->load->model("PaketTahap");
$this->load->model("PaketRekanan");
$this->load->model("PaketDokumen");
$this->load->model("Paketpemenang"); 
$this->load->model("RekananEvaluasiTeknisTawar");
$this->load->model("RekananEvaluasiAdminTawar");
$this->load->model("RekananEvaluasiHargaTawar");
$this->load->model("MatrixEvaluasi");
$this->load->model("RekananPaketPenawaran");
$this->load->model("PaketPenawaran");
$this->load->model("PermohonanPaket");
$this->load->model("PaketNegoisasi");

$paket = new Paket();
$paket_rekanan = new PaketRekanan();
$paket_rekanan_nilai = new PaketRekanan();
$matrix_evaluasi = new MatrixEvaluasi();
$paket_penawaran = new PaketPenawaran();
$paket_dokumen = new PaketDokumen();
$getpaket_pemenang = new Paketpemenang();
$getpaket_pemenang_c = new Paketpemenang();
$paket_negosiasi = new PaketNegoisasi();  

$reqId = $this->input->get("reqId"); 

$paketInfo->getPaket($reqId);
$reqNamaPaket = $paketInfo->nama;
$reqJenisPekerjaan = $paketInfo->jenis;
$reqJenisPekerjaanId = $paketInfo->jenis_id;
$reqMetodeEvaluasi = $paketInfo->metode_evaluasi;
$reqMetodeEvaluasiId = $paketInfo->metode_evaluasi_id;
$reqOwnerEstimate  = $paketInfo->nilai_owner_estimate;
$reqRekananIdPemenang = $paketInfo->rekanan_id_pemenang;
$reqMetodePengadaan = $paketInfo->metode_lelang_id;
$reqJenisPegadaaan = $paketInfo->jenis_pengadaan;
$reqPermohonanId = $paketInfo->permohonan_paket_id;
// echo $reqPermohonanId.'---';
if ($reqPermohonanId) {
  $permohonan_paket = new PermohonanPaket();
  // $permohonan_paket->selectByParams(array("A.PERMOHONAN_PAKET_ID" => $reqPermohonanId, "A.UNIT_KERJA_ID" => $this->UNIT_KERJA_ID));
  $permohonan_paket->selectByParams(array("A.PERMOHONAN_PAKET_ID" => $reqPermohonanId));
  $permohonan_paket->firstRow();
  $reqPL = $permohonan_paket->getField("PENGADAANLANGSUNG");
}

$paket->selectByParamsMonitoring(array(), -1, -1, " AND PAKET_ID = '".$reqId."' ");
$paket->firstRow();

// $paket_rekanan->selectByParams(array("PAKET_ID" => $reqId), -1, -1, " AND A.TANGGAL_DAFTAR IS NOT NULL AND A.LULUS_PENDAFTARAN = 1 AND A.LULUS_KUALIFIKASI = 1 ");
$paket_rekanan->selectByParams3(array("PAKET_ID" => $reqId), -1, -1, " AND A.TANGGAL_DAFTAR IS NOT NULL AND A.LULUS_PENDAFTARAN = 1 AND A.LULUS_KUALIFIKASI = 1 ");
while($paket_rekanan->nextRow())
{

  // ambil nilai koreksi
  $paket_penawaran->selectByParamsRekananPaketPenawaran(array('B.PAKET_REKANAN_ID' => $paket_rekanan->getField("PAKET_REKANAN_ID")), -1, -1, " AND 1=1");
  $paket_penawaran->firstRow(); 

  // ambil negosiasi
  $paket_negosiasi->selectByParams(array("A.PAKET_PENAWARAN_ID" => $paket_rekanan->getField("PAKET_PENAWARAN_ID")));  
  $paket_negosiasi->firstRow();   
  $jumlahNegosiasi[] =  $paket_negosiasi->getField("TOTAL");
	$arrRekananId[] = $paket_rekanan->getField("REKANAN_ID");
	$arrRekanan[] = $paket_rekanan->getField("REKANAN");
	$arrPaketRekananId[] = $paket_rekanan->getField("PAKET_REKANAN_ID");
	$arrPaketRekananNilaiAuction[] = $paket_rekanan->getField("NILAI_PENAWARAN");
  $arrPaketRekananNilaiSebelumnya[] = $paket_rekanan->getField("UNIT_PRICE");
  $arrPaketRekananNilai[] = $paket_penawaran->getField("JUMLAH_KOREKSI");
	$arrPaketRekananLulus[] = $paket_rekanan->getField("LULUS_PENAWARAN");
}

if (is_array($arrRekanan)) {
  $arrRekananId = $arrRekananId;
  $arrRekanan = $arrRekanan;
  $arrPaketRekananId = $arrPaketRekananId;
  $arrPaketRekananNilaiSebelumnya = $arrPaketRekananNilaiSebelumnya;
  $arrPaketRekananNilai = $arrPaketRekananNilai;
  $arrPaketRekananLulus = $arrPaketRekananLulus;
} else {
  $arrRekananId = array();
  $arrRekanan = array();
  $arrPaketRekananId = array();
  $arrPaketRekananNilaiSebelumnya = array();
  $arrPaketRekananNilai = array();
  $arrPaketRekananLulus = array();
}


// Pemenang
$getpaket_pemenang->selectByParams(array("A.PAKET_ID" => $reqId, 'A.PUBLISH' => '1'), -1, -1);
$getpaket_pemenang_count = $getpaket_pemenang_c->getCountByParams(array("A.PAKET_ID" => $reqId, 'A.PUBLISH' => '1'));

$i = 0;

$paket_rekanan_nilai->selectNilaiPenawaran2(array("PAKET_ID" => $reqId));
while($paket_rekanan_nilai->nextRow())
{
	$arrUrutan[] = $paket_rekanan_nilai->getField("PAKET_REKANAN_ID");	
}

function getUrutan($reqPaketRekananId, $arrUrutan)
{
	$key = array_search($reqPaketRekananId, $arrUrutan);
	return $key + 1;	
}

$matrix_evaluasi->selectByParams(array("A.PAKET_JENIS_ID" => $reqJenisPekerjaanId, "A.PAKET_METODE_EVALUASI_ID" => $reqMetodeEvaluasiId));
$matrix_evaluasi->firstRow();


$paket_tahap = new PaketTahap();
$paket_tahap_metode = new PaketTahap();

$jenis_tahap = $paket_tahap_metode->getJenisTahapById($reqId);

$arrPengumumanPemenang          = PENGUMUMAN_PEMENANG;

$aktif_pengumuman = $paket_tahap->getCountByParamsAktif(array("URUT" => $arrPengumumanPemenang[$jenis_tahap], "PAKET_ID" => $reqId, "TAMPILKAN" => 1));
$aktif_pengumuman2 = $paket_tahap->getCountByParamsBerlalu(array("URUT" => $arrPengumumanPemenang[$jenis_tahap], "PAKET_ID" => $reqId, "TAMPILKAN" => 1));

?>
<script>
function setEvaluasiPenawaran(ctrl, ctrl_change)
{
	//get the state of the check box
	if (ctrl.checked == true) {
		//the box is checked, so show the table
		document.getElementById(ctrl_change).value = 1;
	} else {
		//hide the table
		document.getElementById(ctrl_change).value = 0;
	}
}
</script> 

<style type="text/css">
  table th {
    background-color: #967adc;
    color: #fff;
  }
</style>

<div class="row"> 
  <div class="col-md-12 col-sm-12">
    <div class="card">
      <div class="card-header card-head-inverse bg-primary">
        <h4 class="card-title text-white">Pengumuman Pemenang</h4>
        <a class="heading-elements-toggle"><i class="fa fa-ellipsis-v font-medium-3"></i></a> 
        <div class="heading-elements">
            <ul class="list-inline mb-0">
              <li><a data-action="expand"><i class="ft-maximize"></i></a></li>
              <!-- <li><a data-action="close"><i class="ft-x"></i></a></li> -->
            </ul>
        </div>
      </div>
      <div class="card-content collapse show border-info border-darken-2">
        <div class="card-body area-datatable"> 

          <form id="ff" class="easyui-form form-horizontal" method="post" data-options="novalidate:true" enctype="multipart/form-data">

            <div class="table-responsive">  
              <table class="table table-bordered table-hover">
                <tbody> 
                  <tr>
                    <td colspan="4"> 
                      <B><?=$paket->getField("NAMA")?></B> 
                    </td>
                  </tr> 
                  <tr>
                    <td width="25%" colspan="2">
                      <small><i class="fa fa-calendar"></i> <b>Tahun Anggaran</b></small> <br> 
                      <?=getYear($paket->getField("TANGGAL_TAHAP"))?>
                    </td>
                    <td width="25%" colspan="2">
                      <small><i class="fa fa-map-marker"></i> <b>Lokasi Pekerjaan</b></small> <br> 
                      <?=$paket->getField("LOKASI")?>
                    </td>
                  </tr> 
                  <tr>
                    <td width="25%" colspan="2">
                      <small><i class="fa fa-inbox"></i> <b>Jenis Pengadaan</b></small> <br> 
                      <?=$paket->getField("PAKET_JENIS")?>
                    </td>
                    <td width="25%" colspan="2">
                      <small><i class="fa fa-tag"></i> <b>Metode Pengadaan</b></small> <br> 
                      <?=$paket->getField("METODE_LELANG")?>
                    </td>
                  </tr> 
                  <tr>
                    <!-- <td width="25%" colspan="2">
                      <small><i class="fa fa-clipboard"></i> Metode Kualifikasi</small> <br> 
                      <?=$paket->getField("METODE_KUALIFIKASI")?>
                    </td> -->
                    <td width="25%" colspan="2">
                      <small><i class="fa fa-folder-open"></i> <b>Metode Penyampaian Penawaran</b></small> <br> 
                      <?=$paket->getField("SISTEM_SAMPUL")?> File
                    </td>
                    <td width="25%" colspan="2">
                      <small><i class="fa fa-exchange"></i> <b>Metode Evaluasi</b></small> <br> 
                      <?=$paket->getField("METODE_EVALUASI")?>
                    </td>
                  </tr> 
                  <tr>
                    <td width="25%" colspan="2">
                      <small><i class="fa fa-file-text"></i> <b>Kualifikasi Usaha</b></small> <br> 
                      <?=$paket->getField("REKANAN_KUALIFIKASI")?>
                    </td>
                    <td width="25%" colspan="2">
                      <small><i class="fa fa-clock-o"></i> <b>Sistem Negosiasi</b></small> <br>
                      <?php
                      if ($paket->getField("BIDDING") == 1) {
                        echo 'e-Reverse Auction '.$paket->getField("BIDDING_MENIT").' menit';
                      } else {
                        echo "Negosiasi";
                      }
                      ?>
                  </tr> 
                  <?php
                  // if($this->USER_TYPE_ID == 3 || $this->USER_TYPE_ID == 7 ) // PANITIA & EKSEKUTIF
                  // {
                  if ($paket_metode_lelang_id == '1') // ditampilkan hanya untuk Tender
                  {  
                  ?>
                  <tr>
                    <td width="25%" colspan="4">
                      <small><i class="fa fa-money"></i> <b>Perkiraan Nilai Pekerjaan</b></small> <br>
                      <?=$paket->getField("NILAI_MATA_UANG")?> <?=currencyToPage($paket->getField("NILAI"))?>
                    </td>
                    </td>
                  </tr>
                  <?php
                  } else { 
                    if ($this->USER_TYPE_ID != '6') { // bukan untuk penyedia 
                  ?>
                  <tr>
                    <td width="25%" colspan="4">
                      <small><i class="fa fa-money"></i> Perkiraan Nilai Pekerjaan</small> <br>
                      <?=$paket->getField("NILAI_MATA_UANG")?> <?=currencyToPage($paket->getField("NILAI"))?>
                    </td>
                    </td>
                  </tr>
                  <?php 
                    }
                  }
                  // }
                  ?>
                  <tr>
                    <td width="25%" colspan="4">
                      <small><i class="fa fa-suitcase"></i> <b>Bidang / Sub Bidang</b></small><br>
                      <?php if(trim($paket->getField("BIDANG_USAHA")) == "()") 
                          echo "-"; 
                         else 
                          echo str_replace(", (",", <br/> (", $paket->getField("BIDANG_USAHA")); ?>
                    </td>
                  </tr>
                  <tr>
                    <td width="25%" colspan="4">
                      <small><i class="fa fa-th-list"></i> <b>Persyaratan Peserta</b></small><br>
                      <?=$paket->getField("URAIAN")?>
                    </td>
                  </tr>  
                  <?php 
                    // echo $reqPermohonanId.'-'.$reqPL.'-'.$reqMetodePengadaan;
                    if (($reqPL == '0' && $reqMetodePengadaan == '2') || $reqMetodePengadaan != '2') { // Pengadaan langsung <= 300jt
                   ?>
                  <!-- <tr>
                    <td width="25%" colspan="4">
                      <div class="alert alert-info">PANITIA</div>
                      <table class="table table-hover">
                        <tr>
                          <td width="15%"><small><i class="fa fa-building-o"></i> Unit Kerja </small></td>
                          <td width="85%">: <?=$paket->getField("UNIT_KERJA")?></td>
                        </tr>
                        <tr>
                          <td><small><i class="fa fa-envelope-o"></i> Email </small></td>
                          <td>: <?=$paket->getField("EMAIL")?></td>
                        </tr>
                        <tr>
                          <td><small><i class="fa fa-phone"></i> Telepon </small></td>
                          <td>: <?=$paket->getField("TELEPON")?></td>
                        </tr>
                        <tr>
                          <td><small><i class="fa fa-map-marker"></i> Alamat </small></td>
                          <td>: <?=$paket->getField("ALAMAT")?></td>
                        </tr>
                      </table> 
                    </td>
                  </tr>  -->
                  <?php 
                  } ?>
                </tbody>
              </table>     

          <?php 
          if($aktif_pengumuman > 0  || $aktif_pengumuman2 > 0)
          {  ?>
              <h2>Penyedia Menawar</h2>
              <table class="table table-bordered table-hover"> 
                <tr class="judul-kolom">
                  <th align="center" valign="middle" width="2%">No.</th>
                  <th colspan="2" align="center" valign="middle">Uraian</th>
                  <?php
                  for($i=0;$i<count($arrRekanan);$i++)
                  {
                  ?>
                  <th style="text-align: center;"><?=$arrRekanan[$i]?></th>
                  <?php
                  }
                  ?>
                </tr>
                <?php 
                // 1-e-Tender ,2-Pengadaan Langsung ,5-Penunjukan Langsung Cepat,7-e-Tender Cepat
                if ($reqMetodePengadaan != 7) { ?>
                <tr>
                  <td valign="top"><strong>I</strong></td>
                  <td valign="top" colspan="2"><strong> EVALUASI ADMINISTRASI </strong></td>
                  <?php
                  for($i=0;$i<count($arrRekanan);$i++)
                  {
                      $rekanan_evaluasi_admin = new RekananEvaluasiAdminTawar();
                      $rekanan_evaluasi_admin->selectMemenuhiSyarat($reqId, $arrPaketRekananId[$i]);
                      $rekanan_evaluasi_admin->firstRow();
                      // echo $rekanan_evaluasi_admin->getField("JUMLAH_DILENGKAPI");
                      // if($rekanan_evaluasi_admin->getField("JUMLAH_EVALUASI") <= $rekanan_evaluasi_admin->getField("JUMLAH_DILENGKAPI"))
                      if($rekanan_evaluasi_admin->getField("JUMLAH_DILENGKAPI") >= 1)
                      {
                        $imgEvalAdmin = "images/centang.png";
                        $status_admin = "MEMENUHI SYARAT";
                        $keterangan_admin = $rekanan_evaluasi_admin->getField("KETERANGAN_LULUS");
                        $arrEvaluasiAdmin[$i] = 1;
                      }
                      else
                      {
                        $imgEvalAdmin = "images/delete-icon.png";
                        $status_admin = "TIDAK MEMENUHI SYARAT";                
                        $keterangan_admin = $rekanan_evaluasi_admin->getField("KETERANGAN_GAGAL");
                        $arrEvaluasiAdmin[$i] = 0;
                      }
                  ?>
                      <!-- <td align="center"><strong><?=$status_admin?></strong></td> -->
                      <td align="center">
                        <img src="<?=$imgEvalAdmin?>"> <br>
                        <small><?php echo $keterangan_admin ?></small>
                      </td>
                  <?php
                      unset($rekanan_evaluasi_admin);
                  }
                  ?>
                </tr>
                <tr>
                  <td valign="top"><strong>II</strong></td> 
                  <td valign="top" colspan="2"><strong> EVALUASI TEKNIS </strong></td>
                  <?php
                  for($i=0;$i<count($arrRekanan);$i++)
                  {
                      $rekanan_evaluasi_teknis = new RekananEvaluasiTeknisTawar();
                      $rekanan_evaluasi_teknis->selectMemenuhiSyarat($reqId, $arrPaketRekananId[$i]);
                      $rekanan_evaluasi_teknis->firstRow();
                      // if($rekanan_evaluasi_teknis->getField("JUMLAH_EVALUASI") <= $rekanan_evaluasi_teknis->getField("JUMLAH_DILENGKAPI"))
                      if($rekanan_evaluasi_teknis->getField("JUMLAH_DILENGKAPI") >= 1)
                      {
                        $imgEvalTeknis = "images/centang.png";
                        if ($reqMetodeEvaluasiId == '2') { 
                          $skor_teknis = 'Nilai Teknis <b>'.$rekanan_evaluasi_teknis->getField("NILAI_TEKNIS").'</b>';
                        } else {
                          $keterangan_teknis = $rekanan_evaluasi_teknis->getField("KETERANGAN_LULUS");
                        }
                        $status_teknis = "MEMENUHI SYARAT";
                        $arrEvaluasiTeknis[$i] = 1;
                      }
                      else
                      {
                        $imgEvalTeknis = "images/delete-icon.png";
                        $keterangan_teknis = $rekanan_evaluasi_teknis->getField("KETERANGAN_GAGAL");
                        $status_teknis = "TIDAK MEMENUHI SYARAT";               
                        $arrEvaluasiTeknis[$i] = 0;
                      } 
                  ?>
                      <!-- <td align="center"><strong><?=$status_teknis?></strong></td> -->
                      <td align="center">
                        <img src="<?=$imgEvalTeknis?>"> <br>
                        
                        <?php 
                        if ($reqMetodeEvaluasiId == '2') { ?>
                          <?= '<small>'.$skor_teknis.'</small>'; ?>
                        <?php 
                        } else { ?> 
                          <?= '<small>'.$keterangan_teknis.'</small>'; ?>
                        <?php 
                        } ?>
                      </td>
                  <?php
                      unset($rekanan_evaluasi_teknis);
                  }
                  ?>
                </tr>
                <?php 
                } ?>
                <tr>
                  <td valign="top">
                  <?php 
                  // 1-e-Tender ,2-Pengadaan Langsung ,5-Penunjukan Langsung Cepat,7-e-Tender Cepat
                  if ($reqMetodePengadaan != 7) { ?>
                    <strong>III</strong>
                  <?php 
                  } else { ?>
                    <strong>I</strong>
                  <?php 
                  } ?>
                  </td> 
                  <td valign="top" colspan="2"><strong> EVALUASI HARGA </strong></td>
                  <?php
                  for($i=0;$i<count($arrRekanan);$i++)
                  {
                      $rekanan_evaluasi_harga = new RekananEvaluasiHargaTawar();
                      $rekanan_evaluasi_harga->selectMemenuhiSyarat2($reqId, $arrPaketRekananId[$i]);
                      $rekanan_evaluasi_harga->firstRow();
                      // if($rekanan_evaluasi_harga->getField("JUMLAH_EVALUASI") <= $rekanan_evaluasi_harga->getField("JUMLAH_DILENGKAPI"))
                      if($rekanan_evaluasi_harga->getField("JUMLAH_DILENGKAPI") >= 1)
                      {
                        $imgEvalHarga = "images/centang.png";
                        if ($reqMetodeEvaluasiId == '2') { 
                            $skor_harga = 'Nilai Harga <b>'.$rekanan_evaluasi_harga->getField("NILAI_HARGA").'</b>';
                          } else {
                            $keterangan_harga = $rekanan_evaluasi_harga->getField("KETERANGAN_LULUS");
                          }
                        $status_harga = "MEMENUHI SYARAT";
                        $arrEvaluasiHarga[$i] = 1;
                      }
                      else
                      {
                        $imgEvalHarga = "images/delete-icon.png";
                        $keterangan_harga = $rekanan_evaluasi_harga->getField("KETERANGAN_GAGAL");
                        $status_harga = "TIDAK MEMENUHI SYARAT";               
                        $arrEvaluasiHarga[$i] = 0;
                      } 
                  ?>
                      <!-- <td align="center"><strong><?=$status_harga?></strong></td> -->
                      <td align="center">
                        <img src="<?=$imgEvalHarga?>"><br>
                        <?php 
                        if ($reqMetodeEvaluasiId == '2') { ?>
                          <?= '<small>'.$skor_harga.'</small>'; ?>
                        <?php 
                        } else { ?> 
                          <?= '<small>'.$keterangan_harga.'</small>'; ?>
                        <?php 
                        } ?>
                      </td>
                  <?php
                      unset($rekanan_evaluasi_harga);
                  }
                  ?>
                </tr>
                     
                  <?php
                  for($i=0;$i<count($arrRekanan);$i++)
                  {
                      if((int)$reqOwnerEstimate == 0)
                        $presentase = 0;
                      else 
                        $presentase = round(($arrPaketRekananNilai[$i] / $reqOwnerEstimate) * 100,2);
                      
                      $arrEvaluasiPresentase[$i] = $presentase;
                  ?>
                  <?php
                  }
                  ?>  
 
              </table>

              <h2>Pemenang</h2>
              <table class="table table-bordered table-hover"> 
                <tr class="judul-kolom">
                  <th style="width: 5%">Urutan</th>
                  <th>Nama Penyedia</th>
                  <!-- <th style="width: 15%">Tanggal Penetapan</th> -->
                  <!-- <th>Keterangan</th> -->
                </tr>
              <?php 
              if ($getpaket_pemenang_count == 0) {
                echo '<tr><td colspan="2">Pemenang Belum Ditetapkan</td></tr>';
              } else 
              {
                while($getpaket_pemenang->nextRow())
                { ?>
                  <tr>
                    <td align="center"> <?= $getpaket_pemenang->getField("PERINGKAT") ?></td> 
                    <td> <?= $getpaket_pemenang->getField("NAMA") ?></td> 
                    <!-- <td> <?= getFormattedDate($getpaket_pemenang->getField("TANGGAL_PENETAPAN")) ?></td>  -->
                    <!-- <td> <?= $getpaket_pemenang->getField("KETERANGAN") ?></td>  -->
                  </tr>  
                <?php 
                } 
              }?>
              </table> 
          <?php 
          } ?>

              <div class="form-actions">
                <a href="main/index/paket_detil/?reqId=<?=$reqId?>" class="btn btn-danger text-white"> <i class="fa fa-arrow-left"></i> Kembali </a>
              </div> 
            </div>
          </form>
        </div>
      
      </div>
    </div>
  </div> 
</div>   