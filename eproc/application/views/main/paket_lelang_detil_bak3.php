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
$this->load->model("RekananEvaluasiAdmin");
$this->load->model("PaketRekanan");
$this->load->model("PaketPanitia");
$this->load->model("PaketRekananDaftar");
$this->load->model("PaketPihakLain");
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
$paket_pihak_lain = new PaketPihakLain();
$rekanan_evaluasi_admin = new RekananEvaluasiAdmin();
$paket_dokumen = new PaketDokumen();
$paket_panitia = new PaketPanitia();

/* VARIABLES */
$reqId = httpFilterRequest("reqId");

if($reqMode == "reset")
{
	$paket->setField("FIELD", "ALASAN");	
	$paket->setField("FIELD_VALUE", "''");	
	$paket->setField("PAKET_ID", $reqId);		
	$paket->updateByField();	
}

$paket->selectByParamsMonitoring(array(), -1, -1, " AND PAKET_ID = '".$reqId."' ");
$paket->firstRow();
$pra_kualifikasi_cek = $paket->getField("PAKET_METODE_KUALIFIKASI_ID");
//echo 'asdasd'.$pra_kualifikasi_cek;
$paket_user_id = $paket->getField("USER_LOGIN_ID");
$alasan = $paket->getField("ALASAN");
$paket_metode_lelang_id = $paket->getField("PAKET_METODE_LELANG_ID");
$rekanan_id_pemenang = $paket->getField("REKANAN_ID_PEMENANG");
$publish_ba_penawaran = $paket->getField("PUBLISH_BA_PENAWARAN");
$publish_ba_kualifikasi = $paket->getField("PUBLISH_BA_KUALIFIKASI");
$sistem_sampul = $paket->getField("SISTEM_SAMPUL");
$publish_ba_evaluasi_sampul1 = $paket->getField("PUBLISH_BA_EVALSAMPUL1");
$publish_ba_penawaran_sampul2 = $paket->getField("PUBLISH_BA_PENAWARAN2");

$paket_tahap_jadwal->selectByParamsJadwal(array("TAMPILKAN" => "1"), -1, -1, " AND PAKET_ID = '".$reqId."' ");

$jenis_tahap = $paket_tahap_metode->getJenisTahapById($reqId);


//cuman keterangan, yang dibawah ini adalah array urutan tahap, keynya = metode paket, valuenya= urutan tahap *ben gk mbingungi
$arrDokumenLelang 		 			 = array(0, 4,  4,  4,  4,  4,  4,  4,  4,  0, 0, 4,  4,  4,  4,  4);
$arrEvaluasiKualifikasi  			 = array(0, 5,  6,  5,  6,  5,  6,  5,  5,  0, 0, 5,  0,  5,  0,  0);
$arrEvaluasiKualifikasi1 	 		 = array(0, 6,  8,  6,  8,  6,  8,  6,  6,  0, 0, 6,  0,  6,  0,  0);
$arrAanwijzing           	 		 = array(0, 10, 5,  10, 5,  9,  5,  10, 10, 0, 0, 10, 5,  10, 5,  5);
$arrDokumenPenawaran 	 	 		 = array(0, 11, 6,  11, 6,  10, 6,  11, 11, 0, 0, 11, 6,  11, 6,  6);
$arrDokumenPenawaran1 	 	 	 	 = array(0, 11, 8,  11, 8,  10, 8,  11, 11, 0, 0, 11, 6,  11, 6,  6);
//$arrUploadPasswordPenawaran	 	 = array(0, 12, 7,  12, 7,  11, 7,  12, 12, 0, 0, 12, 7,  12, 7);
$arrPembukaanAuction	 			 = array(0, 12, 7,  12, 7,  11, 7,  12, 12, 0, 0, 12, 7,  12, 7,  7);
$arrEvaluasiPenawaran	 			 = array(0, 13, 8,  13, 8,  12, 8,  13, 13, 0, 0, 13, 8,  13, 8,  8);
$arrNegosiasi			 		  	 = array(0, 14, 9,  14, 9,  13, 9,  14, 14, 0, 0, 16, 11, 16, 11, 9);
$arrPengumumanPemenang	 	 		 = array(0, 15, 10, 15, 10, 14, 10, 15, 15, 0, 0, 17, 12, 17, 12, 10);

//$arrUploadPasswordPenawaranSampul2	 = array(0, 0,  0,  0, 	0,  0, 	0,  0, 	0, 	0, 0, 14, 10, 15, 10);
$arrPembukaanAuctionSampul2	 	 	 = array(0, 0,  0,  0, 	0,  0, 	0,  0, 	0, 	0, 0, 14, 9,  14, 9,  0);
$arrEvaluasiPenawaranSampul2	 	 = array(0, 0,  0,  0, 	0,  0, 	0,  0, 	0, 	0, 0, 15, 10, 15, 10, 0);


function ind_long_time($timestamp)
{
	if( ! empty($timestamp))
	{
		$timestamp = strtotime($timestamp);
		
		return date('H', $timestamp).':'.date('i', $timestamp).' WIB';
	}
	else
		return FALSE;
}

$paket_rekanan->selectByParamsPaketLelangV2(array("A.PAKET_ID" => $reqId, "A.REKANAN_ID" => $this->REKANAN_ID), -1, -1, " AND TANGGAL_DAFTAR IS NOT NULL ");
$paket_rekanan->firstRow();
$reqPaketRekananId = $paket_rekanan->getField("PAKET_REKANAN_ID");
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


$status_aanwitzing = $paket_tahap->getCountByParams(array("URUT" => $arrAanwijzing[$jenis_tahap], "PAKET_ID" => $reqId, "TAMPILKAN" => 1));
$status_dok_kualifikasi1 = $paket_tahap->getCountByParams(array("URUT" => $arrEvaluasiKualifikasi[$jenis_tahap], "PAKET_ID" => $reqId, "TAMPILKAN" => 1));
$status_dok_kualifikasi2 = $paket_tahap->getCountByParams(array("URUT" => $arrEvaluasiKualifikasi1[$jenis_tahap], "PAKET_ID" => $reqId, "TAMPILKAN" => 1));
$status_dok_penawaran1 = $paket_tahap->getCountByParams(array("URUT" => $arrDokumenPenawaran[$jenis_tahap], "PAKET_ID" => $reqId, "TAMPILKAN" => 1, "HADIR" => 0));
$status_dok_penawaran2 = $paket_tahap->getCountByParams(array("URUT" => $arrDokumenPenawaran1[$jenis_tahap], "PAKET_ID" => $reqId, "TAMPILKAN" => 1, "HADIR" => 0));
$status_pengumuman = $paket_tahap->getCountByParams(array("URUT" => $arrPengumumanPemenang[$jenis_tahap], "PAKET_ID" => $reqId, "TAMPILKAN" => 1));

$aktif_pengumuman_pra = $paket_tahap->getCountByParamsAktif(array("URUT" => 7, "PAKET_ID" => $reqId, "TAMPILKAN" => 1));
$aktif_aanwitzing = $paket_tahap->getCountByParamsAktif(array("URUT" => $arrAanwijzing[$jenis_tahap], "PAKET_ID" => $reqId, "TAMPILKAN" => 1));
$aktif_dok_kualifikasi1 = $paket_tahap->getCountByParamsAktif(array("URUT" => $arrEvaluasiKualifikasi[$jenis_tahap], "PAKET_ID" => $reqId, "TAMPILKAN" => 1));
$aktif_dok_kualifikasi2 = $paket_tahap->getCountByParamsAktif(array("URUT" => $arrEvaluasiKualifikasi1[$jenis_tahap], "PAKET_ID" => $reqId, "TAMPILKAN" => 1));
$aktif_dok_kualifikasi3 = $paket_tahap->getCountByParamsBerlalu(array("URUT" => $arrEvaluasiKualifikasi1[$jenis_tahap], "PAKET_ID" => $reqId, "TAMPILKAN" => 1));
$aktif_upload_password = 1;//$paket_tahap->getCountByParamsAktif(array("URUT" => $arrUploadPasswordPenawaran[$jenis_tahap], "PAKET_ID" => $reqId, "TAMPILKAN" => 1));
$aktif_upload_password2 = 1;//$paket_tahap->getCountByParamsBerlalu(array("URUT" => $arrUploadPasswordPenawaran[$jenis_tahap], "PAKET_ID" => $reqId, "TAMPILKAN" => 1));


$aktif_upload_password_sampul2 = 1; //$paket_tahap->getCountByParamsAktif(array("URUT" => $arrUploadPasswordPenawaranSampul2[$jenis_tahap], "PAKET_ID" => $reqId, "TAMPILKAN" => 1));
$aktif_upload_password_sampul2_2 = 1; //$paket_tahap->getCountByParamsBerlalu(array("URUT" => $arrUploadPasswordPenawaranSampul2[$jenis_tahap], "PAKET_ID" => $reqId, "TAMPILKAN" => 1));

/* JIKA PASCA TIDAK PERLU DATA KUALIFIKASI */
if($pra_kualifikasi_cek == 2)
{
	$status_dok_kualifikasi1 = 0;
	$status_dok_kualifikasi2 = 0;	
	$aktif_dok_kualifikasi1 = 0;
	$aktif_dok_kualifikasi2 = 0;	
}

//echo '---'.$jenis_tahap.'--'.$aktif_dok_kualifikasi2.'---'.$pra_kualifikasi_cek;

/* APABILA KUALIFIKASI GAGAL, MAKA TIDAK BERHAK MELANJUTKAN KE DOKUMEN PENAWARAN */

$aktif_dok_penawaran1 = $paket_tahap->getCountByParamsAktif(array("URUT" => $arrDokumenPenawaran[$jenis_tahap], "PAKET_ID" => $reqId, "TAMPILKAN" => 1, "HADIR" => 0));
$aktif_dok_penawaran2 = $paket_tahap->getCountByParamsBerlalu(array("URUT" => $arrDokumenPenawaran[$jenis_tahap], "PAKET_ID" => $reqId, "TAMPILKAN" => 1));
if($aktif_dok_kualifikasi1 > 0 || $aktif_dok_kualifikasi2 > 0)
{
	//echo "mazuk".$reqLulusKualifikasi;
	if($reqLulusKualifikasi == 0)
	{
		$aktif_dok_penawaran1 = 0;	
		$aktif_dok_penawaran2 = 0;	
	}	
}

$aktif_pengumuman = $paket_tahap->getCountByParamsAktif(array("URUT" => $arrPengumumanPemenang[$jenis_tahap], "PAKET_ID" => $reqId, "TAMPILKAN" => 1));

$aktif_negosiasi = $paket_tahap->getCountByParamsAktif(array("URUT" => $arrNegosiasi[$jenis_tahap], "PAKET_ID" => $reqId, "TAMPILKAN" => 1));
$aktif_negosiasi2 = $paket_tahap->getCountByParamsBerlalu(array("URUT" => $arrNegosiasi[$jenis_tahap], "PAKET_ID" => $reqId, "TAMPILKAN" => 1));
if($aktif_negosiasi > 0 || $aktif_negosiasi2 > 0)
{
	/* CHECK APAKAH REKANAN PEMENANG */	
	if($this->REKANAN_ID == $rekanan_id_pemenang)
		$aktif_negosiasi =1;	
	else
		$aktif_negosiasi =0;		
}

//pihak lain tambahan by K
$paket_pihak_lain->selectByParamsPaketLelang(array("PAKET_ID" => $reqId, "USER_LOGIN_ID" => $this->USER_LOGIN_ID), -1, -1);
$paket_pihak_lain->firstRow();
$idPihakLain = $paket_pihak_lain->getField('USER_LOGIN_ID');
$idPanitia = $paket_panitia->getCountByParams(array("PAKET_ID" => $reqId, "NIP" => $this->NIP));


$sampul1 = "";
if($sistem_sampul == "2")
	$sampul1 = translate(" Sampul 1", " Cover 1");
?>

<div class="row">
    <div class="col-md-12">
    	<div class="area-main">
        	<div class="judul-halaman"><?=$paket->getField("NAMA")?></div>
            <div class="inner">
                <div class="area-konten">
                    <div class="area-konten-inner">
                        <form id="ff" class="easyui-form form-horizontal" method="post" data-options="novalidate:true" enctype="multipart/form-data">
                            <div class="row">
                                <!--<div class="col-md-9 col-md-pull-3 area-keterangan-paket-lelang">-->
                                <div class="col-md-9 area-keterangan-paket-lelang"> 
								<?
                                if($reqPaketRekananId == "")
                                {}
                                else
                                {
                                $paket_keterangan->selectByPaketRekananKeterangan($reqId, $reqPaketRekananId, $this->REKANAN_ID, $arrEvaluasiKualifikasi[$jenis_tahap], $arrDokumenPenawaran[$jenis_tahap]);
          
                                ?>
                                <div class="frameblue">
                                <?
                                if($alasan == "")
                                {
                                ?>
                                  <ul class="verifikasi-keterangan">
                                      <li class="info"><span><?=translate("Anda telah mendaftar paket pada", "You have successfully signed up on")?> <?=getFormattedDate($reqTanggalDaftar)?><?=translate(" dengan no. registrasi", ". Your registration rumber")?> : <?=$reqKodeRekanan?>.</span></li>
                                  <?
                                  if($reqLulusPendaftaran == 0)
                                  {
                                  ?>
                                      <li class="gagal">
                                          <?php /*?>Verifikasi data pendaftaran anda gagal dengan alasan : <?=$reqLulusPendaftaranKeterangan?>. Anda tidak dapat melanjutkan proses lelang.<?php */?>
                                          <span><?=translate("Verifikasi data pendaftaran anda gagal dengan alasan sebagai berikut", "Tender registration failed, the following reasons")?> :</span>
                                          <?
                                          $paket_rekanan_daftar = new PaketRekananDaftar();
                                          $paket_rekanan_daftar->selectByParamsCatatan(array("PAKET_REKANAN_ID" => $reqPaketRekananId));
                                          ?>
                                          <table class="verifikasi-gagal">
                                              <tr class="judul-kolom">
                                                  <td>Data</td>
                                                  <td><?=translate("Catatan", "Note")?></td>
                                              </tr>
                                          <?
                                          $class = "gelap";
                                          while($paket_rekanan_daftar->nextRow())
                                          {
                                          ?>
                                              <tr class="<?=$class?>">
                                                  <td><?=$paket_rekanan_daftar->getField("KODE")?></td>
                                                  <td><?=$paket_rekanan_daftar->getField("CATATAN")?></td>
                                              </tr>                                    
                                          <?
                                              if($class == "gelap")
                                                  $class = "terang";
                                              else
                                                  $class = "gelap";									
                                          }
                                          ?>    
                                          </table>
                                          <span><?=translate("Anda tidak dapat melanjutkan proses lelang.", "You cannot proceed to the next stage.")?></span>
                                      </li>                        
                                  <?							
                                  }
                                  elseif($reqLulusPendaftaran == 2)
                                  {
                                  ?>
                                      <li class="info"><span><?=translate("Data pendaftaran anda sedang kami verifikasi.", "we are verify your data")?></span></li>
                                  <?							
                                  }
                                  else
                                  {
                                      
                                      if($status_dok_kualifikasi1 > 0 || $status_dok_kualifikasi2 > 0)
                                      {
                                          if($rekanan_evaluasi_admin->getCountByParams(array("PAKET_REKANAN_ID" => $reqPaketRekananId)) > 0)
                                          {
                                          ?>                           
                                          <li class="info"><span><?=translate("Anda telah memasukkan data kualifikasi", "You have completed the prequalification data")?>.</span></li>
                                          <?
                                          }
                                          else
                                          {
                                          ?>                           
                                          <li class="info"><span><?=translate("Anda belum memasukkan data kualifikasi", "You have not completed the pra-qualification data")?>.</span></li>
                                          <?								
                                          }
                                      }
                                      if($publish_ba_kualifikasi == "1")
                                      {
                                          if($reqLulusKualifikasi == 0)
                                          {}
                                          else
                                          {
                                          ?>
                                              <li class="info"><span><?=translate("Anda telah lolos pada tahap kualifikasi", "You have passed the prequalification stage")?>.</span></li>                                    
                                          <?	
                                          }
                                      }							
                                      if($status_aanwitzing > 0)
                                      {
                                          if($reqAanwijzing == "")
                                          {
                                      ?>                           
                                          <li class="info"><span><?=translate("Anda belum mengikuti Aanwijzing", "You have not completed the Aanwijzing Chat")?>.</span></li>
                                      <?
                                          }
                                          else
                                          {
                                      ?>                           
                                          <li class="info"><span><?=translate("Anda telah mengikuti Aanwijzing", "You have completed the Aanwijzing Chat")?>.</span></li>
                                      <?								
                                          }
                                      }
                                      if($status_dok_penawaran1 > 0 || $status_dok_penawaran2 > 0)
                                      {
                                          if($reqKirimPenawaran  == "1")
                                          {
                                          ?>                           
                                          <li class="info"><span><?=translate("Anda telah memasukkan dokumen penawaran", "You have completed the Bidding Documents")?>.</span></li>
                                          <?
										  /*
                                              if($reqKirimPenawaranPassword == "")
                                              {
                                              ?>
                                                  <li class="gagal"><span><?=translate("Anda belum mengupload password dokumen penawaran".$sampul1, "You have not uploaded bidding document ".$sampul1." password")?>.</span></li>
                                              <?
                                              }
                                              else
                                              {
                                              ?>
                                                  <li class="info"><span><?=translate("Anda telah mengupload password dokumen penawaran".$sampul1, "You have uploaded the bidding document ".$sampul1." password")?>.</span></li>                                    
                                              <?	
                                              }
										  */
                                          }
                                          else
                                          {
                                          ?>                           
                                          <li class="info"><span><?=translate("Anda belum memasukkan dokumen penawaran", "You have not completed the bidding documents")?>.</span></li>
                                          <?								
                                          }
                                      }
          
                                      if($publish_ba_evaluasi_sampul1  == "1")
                                      {
                                          if($reqLulusPenawaranSampul1 == "1")
                                          {
                                          ?>
                                              <li class="info"><span><?=translate("Anda telah lolos evaluasi penawaran Sampul 1", "You have passed the bid evaluation of Cover 1")?>.</span></li>     
                                              
                                              <?
                                              if($reqKirimPenawaranPassword2 == "")
                                              {
                                              ?>
                                                  <li class="gagal"><span><?=translate("Anda belum mengupload password dokumen penawaran Sampul 2", "You have not uploaded bidding document cover 2 password")?>.</span></li>
                                              <?
                                              }
                                              else
                                              {
                                              ?>
                                                  <li class="info"><span><?=translate("Anda telah mengupload password dokumen penawaran Sampul 2", "You have uploaded the bidding document cover 2 password")?>.</span></li>                                    
                                              <?	
                                              }
                                                 
                                          }
                                          else
                                          {
                                          ?>
                                              <li class="gagal"><span><?=translate("Anda gagal pada tahap evaluasi penawaran Sampul 1", "You have failed the bid evaluation of Cover 1")?>.</span></li>
                                          <?	
                                          }
                                      }							
                                      
                                  }
                                  ?>                  
                                 
                                  
                                  <?
                                  if($publish_ba_kualifikasi == "1")
                                  {
                                      if($reqLulusKualifikasi == 0)
                                      {
                                      ?>
                                          <li class="gagal"><span><?=translate("Anda gagal pada tahap kualifikasi", "You failed the prequalification stage")?>, <?=translate("Alasan", "Reason")?> : <?=$reqLulusKualifikasiKeterangan;?>.</span></li>
                                      <?
                                      }
                                  }
                                  while($paket_keterangan->nextRow())
                                  {
                                      if($paket_keterangan->getField("KETERANGAN") == "")
                                      {}
                                      else
                                      {
                                  ?>
                                          <li class="gagal"><span><?=$paket_keterangan->getField("KETERANGAN")?>.</span></li>                    
                                  <?
                                      }
                                  }
                                  ?>    
                                  </ul>
                                   <?
                                }
                                else
                                {
                                ?>
                                <?=translate("Paket dibatalkan / diulang dengan alasan", "Tender canceled, with reason")?> : <?=$alasan?>
          
                                <?
                                }
                                ?>
                                </div>
                                <?
                                }
                                ?>    
                                
                                	<div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="inputEmail" class="col-md-3 control-label"> Tgl Pembuatan Paket  :</label>
                                                <div class="col-md-8">
                                                     <?=getFormattedDate($paket->getField("TANGGAL_TAHAP"))?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="inputEmail" class="col-md-3 control-label"> Tahun Anggaran  :</label>
                                                <div class="col-md-8">
                                                    <?=getYear($paket->getField("TANGGAL_TAHAP"))?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="inputEmail" class="col-md-3 control-label"> Unit Kerja  :</label>
                                                <div class="col-md-8">
                                                     <?=$paket->getField("UNIT_KERJA")?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="inputEmail" class="col-md-3 control-label">Bidang / Sub Bidang :</label>
                                                <div class="col-md-8">
                                                     <? if(trim($paket->getField("BIDANG_USAHA")) == "()") echo "-"; else echo str_replace(", (",", <br/>(", $paket->getField("BIDANG_USAHA")); ?> 
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="inputEmail" class="col-md-3 control-label">Uraian Paket Lelang :</label>
                                                <div class="col-md-8">
                                                    <?=$paket->getField("URAIAN")?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="inputEmail" class="col-md-3 control-label"> Lokasi Pekerjaan  :</label>
                                                <div class="col-md-8">
                                                    <?=$paket->getField("LOKASI")?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="inputEmail" class="col-md-3 control-label"> Jenis Pekerjaan  :</label>
                                                <div class="col-md-8">
                                                    <?=$paket->getField("PAKET_JENIS")?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="inputEmail" class="col-md-3 control-label"> Metode Lelang  :</label>
                                                <div class="col-md-8">
                                                    <?=$paket->getField("METODE_LELANG")?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="inputEmail" class="col-md-3 control-label">Metode Kualifikasi :</label>
                                                <div class="col-md-8">
                                                    <?=$paket->getField("METODE_KUALIFIKASI")?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="inputEmail" class="col-md-3 control-label">Kualifikasi :</label>
                                                <div class="col-md-8">
                                                    <?=$paket->getField("REKANAN_KUALIFIKASI")?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="inputEmail" class="col-md-3 control-label"> Metode Evaluasi  :</label>
                                                <div class="col-md-8">
                                                     <?=$paket->getField("METODE_EVALUASI")?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
									<?
                                    if($this->USER_TYPE_ID == 3 || $this->USER_TYPE_ID == 7)
                                    {
                                    ?>                                    
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="inputEmail" class="col-md-3 control-label"> Perkiraan Nilai Pekerjaan  :</label>
                                                <div class="col-md-8">
                                                     <?=currencyToPage($paket->getField("NILAI"))?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>  
                                    <?
									}
									?>                                  
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="inputEmail" class="col-md-3 control-label"> Alamat  :</label>
                                                <div class="col-md-8">
                                                      <?=$paket->getField("ALAMAT")?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="inputEmail" class="col-md-3 control-label">  Email   :</label>
                                                <div class="col-md-8">
                                                     <?=$paket->getField("EMAIL")?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="inputEmail" class="col-md-3 control-label"> Telepon  :</label>
                                                <div class="col-md-8">
                                                      <?=$paket->getField("TELEPON")?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                </div>
                                
                                <div class="col-md-3 area-submenu-paket-lelang">
                                
                                	<div id="accordion">
                                      <h3>Section 1</h3>
                                      <div>
                                        <p>
                                        Mauris mauris ante, blandit et, ultrices a, suscipit eget, quam. Integer
                                        ut neque. Vivamus nisi metus, molestie vel, gravida in, condimentum sit
                                        amet, nunc. Nam a nibh. Donec suscipit eros. Nam mi. Proin viverra leo ut
                                        odio. Curabitur malesuada. Vestibulum a velit eu ante scelerisque vulputate.
                                        </p>
                                      </div>
                                      <h3>Section 2</h3>
                                      <div>
                                        <p>
                                        Sed non urna. Donec et ante. Phasellus eu ligula. Vestibulum sit amet
                                        purus. Vivamus hendrerit, dolor at aliquet laoreet, mauris turpis porttitor
                                        velit, faucibus interdum tellus libero ac justo. Vivamus non quam. In
                                        suscipit faucibus urna.
                                        </p>
                                      </div>
                                      <h3>Section 3</h3>
                                      <div>
                                        <p>
                                        Nam enim risus, molestie et, porta ac, aliquam ac, risus. Quisque lobortis.
                                        Phasellus pellentesque purus in massa. Aenean in pede. Phasellus ac libero
                                        ac tellus pellentesque semper. Sed ac felis. Sed commodo, magna quis
                                        lacinia ornare, quam ante aliquam nisi, eu iaculis leo purus venenatis dui.
                                        </p>
                                        <ul>
                                          <li>List item one</li>
                                          <li>List item two</li>
                                          <li>List item three</li>
                                        </ul>
                                      </div>
                                      <h3>Section 4</h3>
                                      <div>
                                        <p>
                                        Cras dictum. Pellentesque habitant morbi tristique senectus et netus
                                        et malesuada fames ac turpis egestas. Vestibulum ante ipsum primis in
                                        faucibus orci luctus et ultrices posuere cubilia Curae; Aenean lacinia
                                        mauris vel est.
                                        </p>
                                        <p>
                                        Suspendisse eu nisl. Nullam ut libero. Integer dignissim consequat lectus.
                                        Class aptent taciti sociosqu ad litora torquent per conubia nostra, per
                                        inceptos himenaeos.
                                        </p>
                                      </div>
                                    </div>
                                
									<?
                                    if($this->USER_LOGIN_ID == $paket_user_id || $idPanitia > 0 || $this->USER_TYPE_ID == 7)
                                    {
                                    ?>                                
                                	<div class="inner">
                                    
                                    	<div class="judul-grup">Sub Menu</div>
                                    	<ul id="accordion">
                                        	
											<?
                                            if($this->USER_LOGIN_ID == $paket_user_id || $this->USER_TYPE_ID == 7)//$paket_user_id)
                                            {
                                            ?>                                        
                                                <li>
                                                    <a href="main/index/paket_lelang_tambah/?reqId=<?=$reqId?>"><span>Edit Paket Lelang</span>
                                                    <ul>
                                                        <li>Edit paket lelang yang telah ada.</li>
                                                    </ul>
                                                    </a>
                                                </li>
                                                
                                                <li>
                                                    <a href="main/index/paket_lelang_tambah_dokumen_lelang/?reqId=<?=$reqId?>"><span>Dokumen Lelang</span>
                                                    <ul>
                                                        <li>Dokumen lelang yang telah diupload.</li>
                                                    </ul>
                                                    </a>
                                                </li>
												<?
                                                if($pra_kualifikasi_cek == 1)
                                                {
                                                ?> 
                                                <li>
                                                    <a href="main/index/paket_lelang_tambah_kriteria_kualifikasi/?reqId=<?=$reqId?>"><span>Kriteria Kualifikasi</span>
                                                    <ul>
                                                        <li>Kriteria Kualifikasi.</li>
                                                    </ul>
                                                    </a>
                                                </li>
                                                <?
												}
												?>
                                                <li>
                                                    <a href="main/index/paket_lelang_tambah_kriteria_penawaran/?reqId=<?=$reqId?>"><span>Kriteria Penawaran</span>
                                                    <ul>
                                                        <li>Kriteria Penawaran.</li>
                                                    </ul>
                                                    </a>
                                                </li>
                                                <li>
                                                    <a href="main/index/paket_lelang_tambah_dokumen_aritmatika/?reqId=<?=$reqId?>"><span>OE (Owner Estimate)</span>
                                                    <ul>
                                                        <li>Standard estimasi.</li>
                                                    </ul>
                                                    </a>
                                                </li>
                                                
                                                <li>
                                                    <a href="main/index/paket_lelang_tambah_daftar_panitia/?reqId=<?=$reqId?>"><span>Daftar panitia</span>
                                                    <ul>
                                                        <li>Daftar panitia lelang.</li>
                                                    </ul>
                                                    </a>
                                                </li>
											<?
											}
                                            if($this->USER_LOGIN_ID == $paket_user_id || $idPanitia > 0 || $this->USER_TYPE_ID == 7)
                                            {
                                            ?>                                                
                                                <li>
                                                    <a href="main/index/paket_lelang_tambah_pakta_integritas/?reqId=<?=$reqId?>"><span>Pakta Integritas</span>
                                                    <ul>
                                                        <li>Validasi pakta integritas paket lelang.</li>
                                                    </ul>
                                                    </a>
                                                </li>
                                                
                                                <li>
                                                    <a href="main/index/paket_lelang_tambah_daftar_peserta/?reqId=<?=$reqId?>"><span>Daftar Peserta</span>
                                                    <ul>
                                                        <li>Daftar peserta lelang.</li>
                                                    </ul>
                                                    </a>
                                                </li>
                                                
                                                <li>
                                                    <a href="main/index/paket_lelang_tambah_daftar_pihak_lain/?reqId=<?=$reqId?>"><span>Unit Fungsional &amp; Konsultan</span>
                                                    <ul>
                                                        <li>Daftar Unit Fungsional &amp; Konsultan.</li>
                                                    </ul>
                                                    </a>
                                                </li>
                                                
                                                <li>
                                                    <a href="main/index/paket_lelang_tambah_aanwijzing_pra/?reqId=<?=$reqId?>"><span>Materi Aanwijzing</span>
                                                    <ul>
                                                        <li>Materi untuk Aanwijzing.</li>
                                                    </ul>
                                                    </a>
                                                </li>
                                                <div class="sparator"></div>
                                                <div class="sparator"></div>
											   <?
                                                if($paket_tahap->getCountByParams(array("URUT" => $arrEvaluasiKualifikasi[$jenis_tahap], "PAKET_ID" => $reqId, "TAMPILKAN" => 1)) > 0 || $paket_tahap->getCountByParams(array("URUT" => $arrEvaluasiKualifikasi1[$jenis_tahap], "PAKET_ID" => $reqId, "TAMPILKAN" => 1)) > 0)
                                                {
                                                ?>
                                                    <?
                                                    if($pra_kualifikasi_cek == 1)
                                                    {
                                                    ?>                                                   
                                                        <li>
                                                            <a href="main/index/evaluasi_kualifikasi_administrasi/?reqId=<?=$reqId?>"><span>Evaluasi Kualifikasi</span>
                                                            <ul>
                                                                <li>Evaluasi kualifikasi.</li>
                                                            </ul>
                                                            </a>
                                                        </li>
                                                <?
													}
												}
												if($pra_kualifikasi_cek == 1)
												{
													if($paket_tahap->getCountByParams(array("URUT" => 7, "PAKET_ID" => $reqId, "TAMPILKAN" => 1)) > 0 || $paket_tahap->getCountByParams(array("URUT" => $arrEvaluasiKualifikasi1[$jenis_tahap], "PAKET_ID" => $reqId, "TAMPILKAN" => 1)) > 0)
													{
													?>
                                                        <li>
                                                            <a href="main/index/paket_lelang_tambah_pengumuman_prakualifikasi/?reqId=<?=$reqId?>"><span>Pengumuman Pra-Kualifikasi</span>
                                                            <ul>
                                                                <li>Pengumuman Pra-Kualifikasi.</li>
                                                            </ul>
                                                            </a>
                                                        </li>
													<?
													}
												}
												if($paket_tahap->getCountByParams(array("URUT" => $arrAanwijzing[$jenis_tahap], "PAKET_ID" => $reqId, "TAMPILKAN" => 1)) > 0)
												{
												?>
                                                <li>
                                                    <a href="main/index/aanwijzing/?reqId=<?=$reqId?>"><span>Aanwijzing</span>
                                                    <ul>
                                                        <li>Pertemuan Aanwijzing.</li>
                                                    </ul>
                                                    </a>
                                                </li>
												<?
												}							 
												if($paket_tahap->getCountByParams(array("URUT" => $arrDokumenPenawaran[$jenis_tahap], "PAKET_ID" => $reqId, "TAMPILKAN" => 1)) > 0 || $paket_tahap->getCountByParams(array("URUT" => $arrDokumenPenawaran1[$jenis_tahap], "PAKET_ID" => $reqId, "TAMPILKAN" => 1)) > 0)
												{
													if($paket_metode_lelang_id == "9")
													{
													?>
                                                    <li>
                                                        <a href="main/index/paket_lelang_tambah_auction/?reqId=<?=$reqId?>"><span>e-Bidding</span>
                                                        <ul>
                                                            <li>e-Bidding</li>
                                                        </ul>
                                                        </a>
                                                    </li>
                                                    
                                                    <?	
													}
													else
													{
												?>
                                                <li>
                                                    <a href="main/index/paket_lelang_tambah_dokumen_penawaran/?reqId=<?=$reqId?>"><span>Dokumen Penawaran</span>
                                                    <ul>
                                                        <li>Dokumen penawaran yang telah diajukan.</li>
                                                    </ul>
                                                    </a>
                                                </li>
												<?
													}
                                                }
                                                ?>           
												<?
                                                $paket_tahap_auction = new PaketTahap();
                                                $paket_tahap_auction->selectByParams(array("URUT" => $arrPembukaanAuction[$jenis_tahap], "PAKET_ID" => $reqId));
                                                $paket_tahap_auction->firstRow();
                                                if($paket_tahap_auction->getField("HADIR") == "1")
                                                    $link_auction = "paket_lelang_tambah_pembukaan_auction_manual";
                                                else
                                                {
                                                    if($sistem_sampul == "2")
                                                        $link_auction = "paket_lelang_tambah_pembukaan_auction_sampul1";								
                                                    else
                                                        $link_auction = "paket_lelang_tambah_pembukaan_auction";								
                                                    
                                                }
												if($paket_metode_lelang_id == "9")
												{}
												else
												{
                                                ?>                                                                                     
                                                <li>
                                                    <a href="main/index/<?=$link_auction?>/?reqId=<?=$reqId?>"><span>Pembukaan Penawaran<?=$sampul1?></span>
                                                    <ul>
                                                        <li>Pembukaan dokumen penawaran.</li>
                                                    </ul>
                                                    </a>
                                                </li>
												<?
												}
                                                if($sistem_sampul == "2")
                                                {
                                                ?>
                                                   <li>
                                                   	 <a href="main/index/evaluasi_penawaran_administrasi_sampul1/?reqId=<?=$reqId?>"><span>Evaluasi Penawaran Sampul 1</span>
                                                    <ul>
                                                        <li>Evaluasi penawaran Sampul 1 yang telah diajukan</li>
                                                    </ul>
                                                    </a>
                                                    </li>
                                                   <li>
                                                   	 <a href="main/index/paket_lelang_tambah_pembukaan_auction_sampul2/?reqId=<?=$reqId?>"><span>Pembukaan Penawaran Sampul 2</span>
                                                    <ul>
                                                        <li>Pembukaan dokumen penawaran Sampul 2</li>
                                                    </ul>
                                                    </a>
                                                    </li>
                                                   <li>
                                                   	 <a href="main/index/evaluasi_penawaran_harga_sampul2/?reqId=<?=$reqId?>"><span>Evaluasi Penawaran Sampul 2</span>
                                                    <ul>
                                                        <li>Evaluasi penawaran Sampul 2 yang telah diajukan</li>
                                                    </ul>
                                                    </a>
                                                    </li>                                                      
                                                <?
                                                }
                                                else
                                                {
                                                ?>
                                                   <li>
                                                   	 <a href="main/index/evaluasi_penawaran_administrasi/?reqId=<?=$reqId?>"><span>Evaluasi Penawaran</span>
                                                    <ul>
                                                        <li>Evaluasi penawaran yang telah diajukan</li>
                                                    </ul>
                                                    </a>
                                                    </li>  
                                                <?
                                                }					
                                                ?>
                                                
                                                <li>
                                                	 <a href="main/index/paket_lelang_tambah_negosiasi_setup/?reqId=<?=$reqId?>"><span>Setup Negosiasi</span>
                                                    <ul>
                                                        <li>Setting nilai awal negosiasi.</li>
                                                    </ul>
                                                    </a>
                                                </li>
                                                
                                                <li>
                                                    <a href="main/index/paket_lelang_tambah_negosiasi/?reqId=<?=$reqId?>"><span>Negosiasi</span>
                                                    <ul>
                                                        <li>Negosiasi.</li>
                                                    </ul>
                                                    </a>
                                                </li>
                                                
                                                <li>
                                                    <a href="main/index/paket_lelang_tambah_penentuan_pemenang/?reqId=<?=$reqId?>"><span>Penentuan Pemenang</span>
                                                    <ul>
                                                        <li>Hasil penentuan pemenang.</li>
                                                    </ul>
                                                    </a>
                                                </li>

                                                <li>
                                                    <a href="main/index/paket_lelang_tambah_pengumuman_pemenang/?reqId=<?=$reqId?>"><span>Pengumuman Pemenang</span>
                                                    <ul>
                                                        <li>Daftar pemenang lelang</li>
                                                    </ul>
                                                    </a>
                                                </li>
                                                <li>
                                                    <a href="main/index/paket_lelang_tambah_sppjb/?reqId=<?=$reqId?>"><span>Penetapan Penyedia</span>
                                                    <ul>
                                                        <li>Surat penunjukan penyedia barang/jasa</li>
                                                    </ul>
                                                    </a>
                                                </li>
                                        	<?
											}
											?>       
                                        </ul>                                
                                    </div>                                    
									<?
                                    }
                                    ?> 

									<?
                                    if(($this->USER_TYPE_ID == 8 or $this->USER_TYPE_ID == 9) and ($idPihakLain !='' or $paket->getField("USER_LOGIN_ID_FUNGSIONAL") == $this->USER_LOGIN_ID)) 
                                    {
                                    ?>                                
                                	<div class="inner">
                                    
                                    	<div class="judul-grup">Sub Menu</div>
                                    	<ul id="accordion">                                    
                                            <li>
                                                <a href="main/index/dokumen_lelang_fungsional/?reqId=<?=$reqId?>"><span>Dokumen Lelang</span>
                                                <ul>
                                                    <li>Dokumen lelang yang telah diupload.</li>
                                                </ul>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="main/index/aanwijzing/?reqId=<?=$reqId?>"><span>Aanwijzing</span>
                                                <ul>
                                                    <li>Pertemuan Aanwijzing.</li>
                                                </ul>
                                                </a>
                                            </li>
											<?
                                            if($idPihakLain == "")
                                            {}
                                            else
                                            {
                                            ?>
                                                <li>
                                                    <a href="main/index/pakta_integritas/?reqId=<?=$reqId?>"><span>Pakta Integritas</span>
                                                    <ul>
                                                        <li>Validasi pakta integritas paket lelang.</li>
                                                    </ul>
                                                    </a>
                                                </li>                                            
                                            <?
                                            }
                                            ?>                                            
                                        </ul>                                
                                    </div>                                    
									<?
                                    }
                                    ?>      

									<?
                                    if($this->USER_TYPE_ID == 10)
                                    {
                                    ?>                                
                                	<div class="inner">
                                    	<div class="judul-grup">Sub Menu</div>
                                    	<ul id="accordion">              
                                            <li>
                                                <a href="main/index/dokumen_lelang_rekanan/?reqId=<?=$reqId?>"><span>Dokumen Lelang</span>
                                                <ul>
                                                    <li>Dokumen lelang yang telah diupload.</li>
                                                </ul>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="main/loadUrl/report/aanwijzing_cetak_pdf/?reqId=<?=$reqId?>" target="_blank"><span>BA Aanwijzing</span>
                                                <ul>
                                                    <li>Berita Acara Aanwijzing.</li>
                                                </ul>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="main/loadUrl/report/dokumen_penawaran_ba_pdf/?reqId=<?=$reqId?>" target="_blank"><span>BA Pemasukan Penawaran</span>
                                                <ul>
                                                    <li>Berita Acara Pemasukan Penawaran.</li>
                                                </ul>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="main/loadUrl/report/dokumen_pembukaan_penawaran_ba_pdf/?reqId=<?=$reqId?>" target="_blank"><span>BA Pembukaan Penawaran</span>
                                                <ul>
                                                    <li>Berita Acara Pembukaan Penawaran.</li>
                                                </ul>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="main/loadUrl/report/negosiasi_cetak_pdf/?reqId=<?=$reqId?>" target="_blank"><span>BA Negosiasi</span>
                                                <ul>
                                                    <li>Berita Acara Negosiasi.</li>
                                                </ul>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="main/loadUrl/report/sppjb_pdf/?reqId=<?=$reqId?>" target="_blank"><span>Pemenang Lelang</span>
                                                <ul>
                                                    <li>Rekanan pemenang lelang.</li>
                                                </ul>
                                                </a>
                                            </li>
                                            <?
												
												$FILE_DIR = "uploads/pemenang/";
                                            	$this->load->model("PaketDokumen");
												$paket_dokumen = new PaketDokumen();
												$paket_dokumen->selectByParams(array("PAKET_ID" => $reqId, "JENIS_DOKUMEN" => "PENGUMUMAN_PEMENANG"));
												$paket_dokumen->firstRow();
											?>
                                            	<? if($paket_dokumen->getField("PATH_FILE") =='')
												{}
												else
												{
												?>
                                                <li>
                                                    <a href="<?=$FILE_DIR.str_replace("'", "''", $paket_dokumen->getField("PATH_FILE"))?>" target="_blank"><span>Pengumuman Pemenang</span>    
                                                    <ul>
                                                        <li>Pengumuman Pemenang</li>
                                                    </ul>
                                                </li>                                                    
                                                <?
												}
												?>
                                        </ul>                                
                                    </div>                                    
									<?
                                    }
                                    ?>           
                                    
                                    
									<?
                                    if($reqPaketRekananId != "")
                                    {
										if($alasan == "" || $publish_ba_penawaran == "2")
										{		
										
											if($reqLulusPendaftaran == 1)	
											{						
										?>                                
												<div class="inner">
										
											<div class="judul-grup">Sub Menu</div>
											<ul id="accordion">
												<?
												if($reqLulusPendaftaran == 1)
												{
												?>                                        
													<li>
													   <a href="main/index/dokumen_lelang_rekanan/?reqId=<?=$reqId?>"><span><?=translate("Dokumen Lelang", "RFP Documents")?></span>
														<ul>
															<li><?=translate("Dokumen lelang yang telah diupload", "Tender Documents")?></li>
														</ul>
														</a>
													</li> 
													<?
													if($pra_kualifikasi_cek > 0) 
													{
														if($aktif_dok_kualifikasi1 > 0  || $aktif_dok_kualifikasi2 > 0 || $aktif_dok_kualifikasi3 > 0)
														{
														?>
															<li>
																<a href="main/index/data_kualifikasi/?reqId=<?=$reqId?>"><span><?=translate("Data Kualifikasi", "Submit Qualification")?></span>
																<ul>
																	<li><?=translate("Pengisian data kualifikasi perusahaan peserta", "Submit Qualification")?></li>
																</ul>
																</a>
															</li>
														 <?
														}
													}  
													
													if($pra_kualifikasi_cek == 1 && $aktif_pengumuman_pra > 0)
													{
													?>
														<li>
															<a href="main/index/pengumuman_prakualifikasi/?reqId=<?=$reqId?>"><span>Pengumuman Pra-Kualifikasi</span>
															<ul>
																<li>Pengumuman Pra-Kualifikasi</li>
															</ul>
															</a>
														</li>                           
													 <?
													}	
													$aanwijzing_tampil = true;
													if($pra_kualifikasi_cek == 1)
													{
														if($reqLulusKualifikasi == "0")
														{
															$aanwijzing_tampil = false;
														}												
													}
													if($aanwijzing_tampil == true && $aktif_aanwitzing > 0)
													{
													?>
														<li>
															<a href="main/index/aanwijzing/?reqId=<?=$reqId?>"><span>Aanwijzing</span>
															<ul>
																<li><?=translate("Pemberian penjelasan dokumen lelang secara online", "Aanwijzing")?></li>
															</ul>
															</a>
														</li>           
													 <?
													}
																			 
													if($aktif_dok_penawaran1 > 0 || $aktif_dok_penawaran2 > 0)
													{
														if($paket_metode_lelang_id == "9")
														{
														?>
                                                            <li>
                                                                <a href="main/index/auction_rekanan/?reqId=<?=$reqId?>"><span>e-Bidding</span>
                                                                <ul>
                                                                    <li>e-Bidding</li>
                                                                </ul>
                                                                </a>
                                                            </li>                                                             
                                                        <?
														}
														else
														{
													?>
                                                            <li>
                                                                <a href="main/index/dokumen_penawaran_rekanan/?reqId=<?=$reqId?>"><span><?=translate("Dokumen Penawaran", "Submit Proposal")?></span>
                                                                <ul>
                                                                    <li><?=translate("Dokumen penawaran yang telah diajukan", "Submit Proposal")?></li>
                                                                </ul>
                                                                </a>
                                                            </li>          
													<?
														}
														
													}
													?>
													<?
													
													/*if($reqKirimPenawaran == "1")
													{		
			
														if($aktif_upload_password > 0 || $aktif_upload_password2 > 0)
														{
															
															if($sampul1 == "")
																$uploadPassTitle = " Penawaran";
															else
																$uploadPassTitle = $sampul1;													
														?>
															<li>
																<a href="main/index/dokumen_penawaran_password/?reqId=<?=$reqId?>"><span><?=translate("Upload Password".$uploadPassTitle, "Upload Certificate File")?></span>
																<ul>
																	<li><?=translate("Upload password dokumen penawaran", "Upload certificate file")?><?=$sampul1?>.</li>
																</ul>
																</a>
															</li>   
														<?
														}	
													}*/
													//if($aktif_upload_password > 0 || $aktif_upload_password2 > 0){
													if($aktif_dok_penawaran2 > 0) {
														/* CEK APAKAH SUDAH MEMASUKKAN DOKUMEN PENAWARAN */
														$sudahUpload = $paket_dokumen->getCountByParams(array("PAKET_ID" => $reqId, "REKANAN_USER_ID" => $this->ID), " AND JENIS_DOKUMEN LIKE 'PENAWARAN%' ");
														if($sudahUpload > 0)
														{
													?>
															<li>
																<a href="main/loadUrl/report/dokumen_penawaran_ba_pdf?reqId=<?=$reqId?>" target="_blank"><span><?=translate("BA Pemasukan Penawaran", "Minutes of Submission of Bids")?></span>
																<ul>
																	<li><?=translate("Cetak berita acara pemasukan penawaran.", "Minutes of Submission of Bids")?>.</li>
																</ul>
																</a>
															</li>    
													<?					
														}
													}
													
													if($publish_ba_penawaran == "1" || $publish_ba_penawaran == "2"){
														/* CEK APAKAH SUDAH MEMASUKKAN DOKUMEN PENAWARAN */
														$sudahUpload = $paket_dokumen->getCountByParams(array("PAKET_ID" => $reqId, "REKANAN_USER_ID" => $this->ID), " AND JENIS_DOKUMEN LIKE 'PENAWARAN%' ");
														if($sudahUpload > 0)
														{		
															
															if($sistem_sampul == "2")
															{
															?>
																<li>
																	<a href="main/index/pembukaan_auction_rekanan_sampul1/?reqId=<?=$reqId?>"><span><?=translate("Pembukaan Penawaran Sampul 1", "Bids Opening Cover 1")?></span>
																	<ul>
																		<li><?=translate("Pembukaan penawaran Sampul 1 oleh panitia.", "Bids Opening Cover 1")?>.</li>
																	</ul>
																	</a>
																</li>    
															<?	
															}
															else
															{
													?>
																<li>
																	<a href="main/index/pembukaan_auction_rekanan/?reqId=<?=$reqId?>"><span><?=translate("Pembukaan Penawaran", "Bids Opening")?></span>
																	<ul>
																		<li><?=translate("Pembukaan penawaran oleh panitia.", "Bids Opening")?>.</li>
																	</ul>
																	</a>
																</li>    
													<?
															}
														}
													}			
			
			
													if($reqLulusPenawaranSampul1 == "1")
													{		
														/*
														if($aktif_upload_password_sampul2 > 0 || $aktif_upload_password_sampul2_2 > 0)
														{
														?>
															<li>
																<a href="main/index/dokumen_penawaran_password_sampul2/?reqId=<?=$reqId?>"><span>Upload Password Sampul 2</span>
																<ul>
																	<li>Upload password dokumen penawaran Sampul 2.</li>
																</ul>
																</a>
															</li>    
														<?
														}
														*/	
														
														if($publish_ba_penawaran_sampul2 == "1" || $publish_ba_penawaran_sampul2 == "2")
														{
														?>
															<li>
																<a href="main/index/pembukaan_auction_rekanan_sampul2/?reqId=<?=$reqId?>"><span>Pembukaan Penawaran Sampul 2</span>
																<ul>
																	<li>Pembukaan penawaran Sampul 2 oleh panitia.</li>
																</ul>
																</a>
															</li>                                       
														<?	
														}
														
													}						
													if($aktif_negosiasi > 0)
													{
													?>
														<li>
															<a href="main/index/negosiasi_rekanan/?reqId=<?=$reqId?>"><span><?=translate("Negosiasi", "Negotiation")?></span>
															<ul>
																<li> <?=translate("Negosiasi dan klarifikasi", "Negotiation")?></li>
															</ul>
															</a>
														</li>                                                 
													<?
													}										
													if($aktif_pengumuman > 0)
													{							
													?>		
														<li>
															<a href="main/index/pengumuman_pemenang/?reqId=<?=$reqId?>"><span>Pengumuman Pemenang</span>
															<ul>
																<li>Daftar Pemenang Lelang</li>
															</ul>
															</a>
														</li>                                        
													<?
													}
												}
												?>       
											</ul>                                
										</div>                                    
										<?
											}
										}
                                    }
                                    ?>   

								  <?
                                  if((int)$this->USER_TYPE_ID == 3 && $this->USER_LOGIN_ID == $paket_user_id)
                                  {
                                      if(trim($alasan) == "")
                                      {
                                  ?>                          
                                    <a title="#" onClick="openAdd('main/loadUrl/main/paket_lelang_batal/?reqId=<?=$reqId?>');" class="btn-batal" style="width:96% !important; text-align:center">Batalkan Paket</a>
                                  <?
									  }
								  }
                                  ?>                                                                                                                  
                                </div>
                                
                            </div>
                            
                            <!---------------------- TAHAPAN LELANG ---------------------->
                            <div class="judul-grup"> Tahapan Lelang </div>
                            
                            <div class="area-tahapan-lelang">
                            	<table>
                                    <tbody>
									  <?
                                      $i=1;
                                      $style="gelap";
                                      while($paket_tahap_jadwal->nextRow())
                                      {
										  $now = $paket_tahap_jadwal->getField("AKTIF");
										  ?>
											<tr>
											  <td <? if($now == 1) { ?>style="font-weight:bold" <? } ?>><?=$i?>.</td>
											  <td <? if($now == 1) { ?>style="font-weight:bold" <? } ?>> <?=$paket_tahap_jadwal->getField("NAMA")?> <? if($paket_tahap_jadwal->getField("HADIR") == 1) { ?>*<? } ?></td>
											  <td <? if($now == 1) { ?>style="font-weight:bold" <? } ?>> <?=getFormattedDate($paket_tahap_jadwal->getField("TANGGAL_AWAL"))?><?=addWIB($paket_tahap_jadwal->getField("JAM_AWAL"))?> <? if($paket_tahap_jadwal->getField("TANGGAL_AKHIR") == "") {} else { ?> s.d <?=getFormattedDate($paket_tahap_jadwal->getField("TANGGAL_AKHIR"))?><?=addWIB($paket_tahap_jadwal->getField("JAM_AKHIR"))?> <? } ?> </td>
											</tr>
                                      <?
									  		$i++;
                                      }
                                      ?>
                                    <tr>
                                      <td colspan="3"> <div class="keterangan"> Keterangan: pada tahap bertanda <span class="merah">*</span> rekanan diwajibkan hadir</div></td>
                                    </tr>
                                    <tr>
                                      <td colspan="3"><!--<a href="main/main/index/paket_lelang" class="btn btn-primary">Kembali</a>-->
                                       
                                      </td>
                                    </tr>
                                  </tbody>
                                  </table>

                            </div>
                            
                            <div class="row">
                                <div class="col-md-12">
                                	<div class="area-tombol-bawah">
                                    	<a href="main/index/paket_lelang" class="btn-kembali">Kembali</a>
                                        
                                        <!--<a onclick="$('#alumniForm').submit()" style="cursor:pointer" class="btn-lanjut pull-right">Lanjut</a>-->
                                    </div>
                                </div>
							</div>
                            
						</form>  
                    </div>
                    
                </div>
            </div>
        </div>
    </div>
</div>

<style>
body * { 
/*-webkit-transform: translateZ(100);
-moz-transform: translateZ(100);
-ms-transform: translateZ(100);
transform: translateZ(100); */
}
</style>

<script>
jQuery(function ($) {
    $('#accordion li').hover(function () {
        $(this).find('ul').stop(true, true).slideDown()
    }, function () {
        $(this).find('ul').stop(true, true).slideUp()
    }).find('ul').hide()
})
</script>