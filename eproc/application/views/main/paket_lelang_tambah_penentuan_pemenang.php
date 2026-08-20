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
$this->load->library("paketinfo"); $paketInfo = new paketinfo();
$this->load->model("PaketRekanan");
$this->load->model("Paket");
$this->load->model("PaketPanitia");
$this->load->model("Paketpemenang");
$this->load->model("RekananEvaluasiTeknisTawar");
$this->load->model("RekananEvaluasiAdminTawar");
$this->load->model("RekananEvaluasiHargaTawar");
$this->load->model("PaketNegoisasi");
$this->load->model("PaketTahap");
$this->load->model("PaketDokumen");
$this->load->library("FileHandler");
include_once("functions/string.func.php");
include_once("functions/date.func.php");
include_once("functions/default.func.php");

$arrPenetapanPemenang = PENETAPAN_PENYEDIA;

$paket_rekanan = new PaketRekanan();
$paket_pemenang = new Paketpemenang();
$getpaket_pemenang = new Paketpemenang();
$countpaket_pemenang = new Paketpemenang();
$paket_negosiasi = new PaketNegoisasi();
$paket_tahap = new PaketTahap();
$paket_tahap_metode = new PaketTahap();

$reqMode = $this->input->get("reqMode");
$reqPemenang= $this->input->post('reqPemenang');
$reqNegosiasi= $this->input->post('reqNegosiasi');
$reqTanggal= $this->input->post('reqTanggal');
// $reqKeterangan= $this->input->post('reqKeterangan');
$submitSimpan= $this->input->post('submitSimpan');

$paketInfo->getPaket($reqId);
$reqNama = $paketInfo->nama;
$reqNamaPaket = $paketInfo->nama;
$reqJenisPekerjaan = $paketInfo->jenis;
$reqJenisPekerjaanId = $paketInfo->jenis_id;
$reqMetodeEvaluasi = $paketInfo->metode_evaluasi;
$reqMetodeLelang = $paketInfo->metode_lelang_id;
$reqMetodeEvaluasiId = $paketInfo->metode_evaluasi_id;
$bidding = $paketInfo->bidding;
$reqPaketMetodeLelangId = $paketInfo->metode_lelang_id;
$reqRekananIdPemenang = $paketInfo->rekanan_id_pemenang;
$reqMetodePengadaan = $paketInfo->metode_lelang_id;
$reqUserLoginId = $paketInfo->user_login_id;
$reqMultiPemenang = $paketInfo->multi_pemenang; // Kontrak Payung
$reqNilai = $paketInfo->nilai;
$reqSistemSampul = $paketInfo->sistem_sampul; 
$reqUUID = $paketInfo->uuid; 
$reqNilaiShow = currencyToPage($paketInfo->nilai);

$paket_panitia = new PaketPanitia();
$paket_panitia->selectByParams(array("A.PAKET_ID" => $reqId));

if ($bidding == '0' || $bidding == '') { // jika Sistem Negosiasi
  $paket_rekanan->selectByParams3(array("A.PAKET_ID" => $reqId), -1, -1, " AND LULUS_PENDAFTARAN = 1 AND LULUS_KUALIFIKASI = 1 AND LULUS_PENAWARAN = 1 AND KIRIM_PENAWARAN = 1 AND A.REKANAN_ID NOT IN (SELECT REKANAN_ID FROM PAKET_PEMENANG WHERE PAKET_ID=$reqId )  ORDER BY A.NILAI_PENAWARAN ASC", $reqId);
} else { // jika Sistem Negosiasi nya Bidding
  // $paket_rekanan->selectByParams4(array("A.PAKET_ID" => $reqId), -1, -1, " AND LULUS_PENDAFTARAN = 1 AND LULUS_KUALIFIKASI = 1 AND LULUS_PENAWARAN = 1 AND KIRIM_PENAWARAN = 1 AND A.REKANAN_ID NOT IN (SELECT REKANAN_ID FROM PAKET_PEMENANG WHERE PAKET_ID=$reqId ) ORDER BY A.NILAI_PENAWARAN ASC", $reqId);
  //die("Fitur Penentuan Pemenang untuk Sistem Negosiasi Bidding sedang dalam pengecekan .".$reqId);
  $paket_rekanan->selectByParams4(array("A.PAKET_ID" => $reqId), -1, -1, " AND LULUS_PENDAFTARAN = 1 AND A.REKANAN_ID NOT IN (SELECT REKANAN_ID FROM PAKET_PEMENANG WHERE PAKET_ID=$reqId ) ORDER BY A.NILAI_PENAWARAN ASC", $reqId);
}
  // $paket_rekanan->selectByParams(array("PAKET_ID" => $reqId), -1, -1, " AND LULUS_PENDAFTARAN = 1 AND LULUS_KUALIFIKASI = 1 AND LULUS_PENAWARAN = 1 AND KIRIM_PENAWARAN = 1 AND A.REKANAN_ID NOT IN (SELECT REKANAN_ID FROM PAKET_PEMENANG WHERE PAKET_ID=$reqId ) ", $reqId);
  $getpaket_pemenang->selectByParams(array("PAKET_ID" => $reqId), -1, -1);
  $countpaket_pemenang = $countpaket_pemenang->getCountByParams(array("A.PAKET_ID" => $reqId));

$jenis_tahap = $paket_tahap_metode->getJenisTahapById($reqId);
$aktif_penentuan_pemenang = $paket_tahap->getCountByParamsAktif(array("URUT" => $arrPenetapanPemenang[$jenis_tahap], "PAKET_ID" => $reqId, "TAMPILKAN" => 1));
$aktif_penentuan_pemenang2 = $paket_tahap->getCountByParamsBerlalu(array("URUT" => $arrPenetapanPemenang[$jenis_tahap], "PAKET_ID" => $reqId, "TAMPILKAN" => 1));

if($aktif_penentuan_pemenang  > 0 || $aktif_penentuan_pemenang2  > 0) {
  $info = "1";
} else {
  $info = "0";
}

?>
<script type="text/javascript">
$(function(){
  $('#ffUpload').form({
    url:'dokumen_pengadaan_upload_rekanan/upload_evaluasi',
    onSubmit:function(){
      if($(this).form('validate'))
      {
      var win = $.messager.progress({
                    title:'Upload Data',
                    msg:'Mengupload data...'
                  });
      }
      else
        $('input:file').MultiFile('reset');
      return $(this).form('validate');
    },
    success:function(data){
      alertSuccess2(data); 
      $.messager.progress('close');
      setTimeout(function() {
        document.location.reload();
      }, 2000);
    }
  });

  // $(function(){
    $('#ff').form({
      url:'paket_json/penentuan_pemenang',
      onSubmit:function(){
        // return $(this).form('validate');
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
        // alert(data);return false;
        hideLoad();
        document.location.href = 'main/index/paket_lelang_tambah_penentuan_pemenang/?reqId=<?=$reqId?>';
      }
    });
  // });


});

  function submitValidasi(kode)
  {
    if(confirm("Setujui Pemenang?"))
    {
      $.getJSON('paket_panitia_json/pemenang_validasi_json/?reqId=<?=$reqId?>&reqKode='+kode,
      function(data){
        // alert(data);
        alertSuccess2(data.PESAN);
        setTimeout(function() {
          document.location.reload();
        }, 2000);
      });
    }
  }

  function submitPublish(kode,status)
  {
    if (status === '1') {
      var pesan = 'Publish Pemenang?';
    } else {
      var pesan = 'Batalkan Publish Pemenang?';
    }
    if(confirm(pesan))
    {
      $.getJSON('paket_json/pemenang_publish_json/?reqKode='+kode+'&reqStatus='+status,
      function(data){
        // alert(data);
        // alert(data.PESAN);
        alertSuccess2(data.PESAN);
        setTimeout(function() {
          document.location.reload();
        }, 2000);
      });
    }
  }

  function submitPublish2(reqid,status)
  {
    var nilai = <?= $reqNilai ?>;
    var nilaiShow = '<?= $reqNilaiShow ?>';
    if (status === '1') {
      var pesan = 'Publish Pemenang?';
    } else {
      var pesan = 'Batalkan Publish Pemenang?';
    }

    if (status === '1' && nilai > 5000000000) { // before publish check nilai > 5M required upload
      var tot = $('#reqTotalDokumen').val();
      if (tot === '0') {
        alertError3('Nilai diatas 5M wajib upload file Penetapan Pemenang.');
        return false;
      }
    }

    if(confirm(pesan))
    {
      $.getJSON('paket_json/pemenang_publish_json2/?reqId='+reqid+'&reqStatus='+status,
      function(data){
        // alert(data);
        // alert(data.PESAN);
        alertSuccess2(data.PESAN);
        setTimeout(function() {
          document.location.reload();
        }, 2000);
      });
    }

  }
$(document).ready(function() {
  $('#btnTolak').on('click', function ()
  {
    $.messager.prompt('Tolak',"Apakah anda tidak setuju, masukan catatan ?",function(r){
      if (r){
        $.getJSON("paket_panitia_json/pemenang_validasi_json_tolak/?reqNote3="+r+"&reqId=<?=$reqId?>",
        function(data){
          if (data.PESAN === 'Tolak Penetapan berhasil.') {
            alertSuccess2(data.PESAN);
          } else {
            alertError2(data.PESAN);
          }
          setTimeout(function() {
            document.location.reload();
          }, 2000);
        });
      }
    });
  });
} );

</script>

<style type="text/css">
  tr > th {
    vertical-align: middle;
    text-align: center;
  }
</style>

<div class="row">
  <div class="col-md-12 col-sm-12">
    <div class="card">
      <div class="card-header card-head-inverse bg-primary">
        <h4 class="card-title text-white">Penetapan Pemenang <?= $paketInfo->metode_lelang_nama ?></h4>
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

          <?php
          if ($info == 0) { ?>
          <div class="alert alert-danger" style="color:#fff">
            <span style="color: #fff">
              Penetapan Pemenang belum mulai.
            </span>
          </div>
          <?php
          } ?>
      <?php
      if ($info == 1)
      {  ?>
        <?php
        //echo $reqUserLoginId." ".$this->USER_LOGIN_ID;
        if ($this->USER_LOGIN_ID == $reqUserLoginId)
        { ?>

          <?php

            $i=1;
            $totalPanitia=1;
            $totalPanitiaSudahValidasi=1;
            while($paket_panitia->nextRow())
            {
              $input = $paket_panitia->getField("NAMA").";".$paket_panitia->getField("NIP").";".$paket_panitia->getField("JABATAN").";".$paket_panitia->getField("KETUA");
            ?>
              <td style="text-align: center">
                <?php
                if ($paket_panitia->getField("VALIDASI_PEMENANG") != '1') {
                  if ($this->NIP == $paket_panitia->getField("NIP")) {
                  } else {
                  }
                } else {
                  $totalPanitiaSudahValidasi++;
                } ?>
              </td>
            </tr>
          <?php
            $i++;
            $totalPanitia++;
          }
          $totalPanitiaSetuju = $totalPanitia;
          // echo $totalPanitia;
          // echo $totalPanitiaSetuju;
          // echo $totalPanitiaSudahValidasi;
          ?>
        <?php
        } else
        {
          if ($countpaket_pemenang > 0)
          {
        ?>
          <table class="table table-double mb-0 table-bordered">
            <thead>
              <tr>
                <th>NPP</th>
                <th>Nama</th>
                <th>Jabatan</th>
                <th style="text-align: center">Status</th>
                <th style="text-align: center">Aksi</th>
              </tr>
            </thead>
            <tbody id="tbodyPanitia">
              <?php
                $i=1;
                $totalPanitia=1;
                $totalPanitiaSudahValidasi=1;
                while($paket_panitia->nextRow())
                {
                  $input = $paket_panitia->getField("NAMA").";".$paket_panitia->getField("NIP").";".$paket_panitia->getField("JABATAN").";".$paket_panitia->getField("KETUA");
                ?>
                <tr>
                  <td class="text-center" style="width: 25%"><?=$paket_panitia->getField("NIP")?></td>
                  <td class="text-center"><?=$paket_panitia->getField("NAMA")?></td>
                  <td class="text-center" style="width: 20%">
                    <?php 
                      if ($paket_panitia->getField("KETUA") == '1') {
                        echo "Ketua";
                      } else {
                        echo "Anggota";
                      } ?>
                  </td>
                  <td style="text-align: center; width: 22%">
                    <?php
                      // if ($this->NIP == $paket_panitia->getField("NIP")) {
                        if ($paket_panitia->getField("VALIDASI_PEMENANG") == '') {
                          echo '-';
                        } else if ($paket_panitia->getField("VALIDASI_PEMENANG") == '2') {
                          echo '<i class="fa fa-close btn btn-danger" style="padding:3px 8px !important"> di tolak</i>';
                          echo '<br><small>Catatan: '.$paket_panitia->getField("VALIDASI_PEMENANG_CATATAN").'</small>';
                        } else if ($paket_panitia->getField("VALIDASI_PEMENANG") == '1') {
                          echo '<i class="fa fa-check-square-o btn btn-primary" style="padding:3px 8px !important"> di terima </i>';
                        }

                      // } else {
                      //   // echo '<i class="fa fa-close btn btn-danger" style="padding:3px 8px !important"></i>';
                      //   echo "-";
                      // } ?>
                  </td>
                  <td style="text-align: center; width: 15%">
                    <?php
                    if ($paket_panitia->getField("VALIDASI_PEMENANG") != '1') {
                    } else {
                      $totalPanitiaSudahValidasi++;
                    }
                      if ($this->NIP == $paket_panitia->getField("NIP")) {
                        if ($paket_panitia->getField("VALIDASI_PEMENANG") == '') {
                          echo '<a title="#" onclick="submitValidasi(\''.$paket_panitia->getField("PAKET_PANITIA_ID").'\')" class="'.CLASS_BTN_PRIMARY.' btn-sm mr-1" style="color:#fff">Terima?</a>';
                          echo '<a title="#" class="'.CLASS_BTN_DANGER.' btn-sm" id="btnTolak"  data-id="" style="color:#fff">Tolak</a>';
                        } else if ($paket_panitia->getField("VALIDASI_PEMENANG") == '2') {
                          echo '<a title="#" onclick="submitValidasi(\''.$paket_panitia->getField("PAKET_PANITIA_ID").'\')" class="'.CLASS_BTN_PRIMARY.' btn-sm mr-1" style="color:#fff">Terima?</a>';
                        } else if ($paket_panitia->getField("VALIDASI_PEMENANG") == '1') {
                          echo '<a title="#" class="'.CLASS_BTN_DANGER.' btn-sm" id="btnTolak"  data-id="" style="color:#fff">Tolak ?</a>';
                        }
                      } else {
                      //   // echo '<i class="fa fa-close btn btn-danger" style="padding:3px 8px !important"></i>';
                      //   echo "-";
                      }
                    ?>
                  </td>
                </tr>
              <?php
                $i++;
                $totalPanitia++;
              }
              $totalPanitiaSetuju = $totalPanitia-1;
              // echo $totalPanitia;
              // echo $totalPanitiaSetuju;
              // echo $totalPanitiaSudahValidasi;
              ?>
            </tbody>
          </table>
        <?php
      } else { echo '<div class="col-md-12 alert alert-danger">Pemenang belum di tetapkan</div>';}
        } ?>

        <?php
        if ($this->USER_LOGIN_ID == $reqUserLoginId)
        { ?>
        <div class="card-content collapse show border-info border-darken-2" id="page2">
          <div class="card-body area-datatable">
            <div class="table-responsive">
              <form id="ff" class="easyui-form form-horizontal" method="post" data-options="novalidate:true" enctype="multipart/form-data">
                <table class="table table-bordered table-hover">
                <tr>
                  <td width="30%"> Pekerjaan</td>
                  <td> <?=$reqNamaPaket?> </td>
                </tr>
                <tr>
                  <td width="20%"> Jenis Pekerjaan</td>
                  <td> <?=$reqJenisPekerjaan?> </td>
                </tr>
                <tr>
                  <td width="20%"> Metode Evaluasi</td>
                  <td> <?=$reqMetodeEvaluasi?> </td>
                </tr>
              </table>
                <table class="table table-bordered">
                  <thead>
                    <tr style="background: #967adc; color: #fff">
                      <th style="width: 2%">No</th>
                      <th>
                      <?php
                      if ($reqMetodeLelang == 1 || $reqMetodeLelang == 7 || $reqMetodeLelang == 10) { // tender & tender cepat
                         echo "Nama Peserta";
                      } else {
                        echo "Nama Penyedia";
                      }?>
                      </th>
                      <?php
                      if ($reqMetodeLelang != '7') { // Selain Tender Cepat ?>
                      <th width="15%">Evaluasi <br> Administrasi</th>
                      <th width="15%">Evaluasi <br> Teknis</th>
                      <?php
                      } ?>
                      <th width="15%">Evaluasi <br> Harga</th>

                      <?php 
                      if ($reqMetodeEvaluasiId == '2') {
                        echo '<th width="5%">Total <br> Kombinasi</th>';
                      } ?>

                      <th width="15%">Penawaran <br> Terkoreksi</th>

                      <?php
                      if ($bidding == '0' || $bidding == '') { // jika Sistem Negosiasi  ?>
                      <th>Negosiasi</th>
                      <?php
                      } else { ?>
                      <th>Harga <br> e-Reverse Auction</th>
                      <?php
                      } ?>

                      <th width="10%"><?php if ($reqMultiPemenang == '0') { echo "Pilih <br> Urutan Pemenang"; } else { echo "&nbsp;Pilih Pemenang &nbsp;"; } ?></th>
                      <!-- <th>Urutan ke</th> -->
                    </tr>
                    <tr style="background: #967adc; color: #fff">
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                      $no=1;
                      $noLulus=1;
                    if ($paket_rekanan->countRow() == 0) {
                      echo '<td colspan="8">. : : Data tidak ada : : .</td>';
                    } else
                    {
                    while($paket_rekanan->nextRow())
                    {
                      //tambahan akmal 2021-11-29
                      //jika rekanan tidak membuat penawaran maka skip / continue
                      if($paket_rekanan->getField("PAKET_PENAWARAN_ID")==''){
                        continue;
                      }
                      //end tambahan akmal
                      
                      $rekanan_evaluasi_admin = new RekananEvaluasiAdminTawar();
                      $rekanan_evaluasi_admin->selectMemenuhiSyarat($reqId, $paket_rekanan->getField("PAKET_REKANAN_ID"));
                      $rekanan_evaluasi_admin->firstRow();

                      $rekanan_evaluasi_teknis = new RekananEvaluasiTeknisTawar();
                      $rekanan_evaluasi_teknis->selectMemenuhiSyarat($reqId, $paket_rekanan->getField("PAKET_REKANAN_ID"));
                      $rekanan_evaluasi_teknis->firstRow();

                      $rekanan_evaluasi_harga = new RekananEvaluasiHargaTawar();
                      $rekanan_evaluasi_harga->selectMemenuhiSyarat2($reqId, $paket_rekanan->getField("PAKET_REKANAN_ID"));
                      $rekanan_evaluasi_harga->firstRow();

                      $paket_negosiasi->selectByParams(array("A.PAKET_PENAWARAN_ID" => $paket_rekanan->getField("PAKET_PENAWARAN_ID")));
                      
                      $paket_negosiasi->firstRow();
                      $penawaranNegosiasi =  $paket_negosiasi->getField("UNIT_PRICE");
                      $jumlahNegosiasi =  $paket_negosiasi->getField("TOTAL");
                      $setujui =  $paket_negosiasi->getField("SETUJUI");
                      
                    ?>
                    <tr>
                      <td><?=$no?></td>
                      <td><?=$paket_rekanan->getField("REKANAN")?></td>
                      <?php
                      if ($reqMetodeLelang != '7') { // Selain Tender Cepat ?>
                      <td style="text-align:center">
                        <?php
                        if($rekanan_evaluasi_admin->getField("JUMLAH_DILENGKAPI") >= 1)
                        {
                          $status_admin = '<img src="images/centang-cetak.png">';
                          $keterangan_admin = $rekanan_evaluasi_admin->getField("KETERANGAN_LULUS");
                          $arrEvaluasiAdmin[$i] = 1;
                        }
                        else
                        {
                          $status_admin = '<img src="images/uncentang-cetak.png">';
                          $keterangan_admin = $rekanan_evaluasi_admin->getField("KETERANGAN_GAGAL");
                          $arrEvaluasiAdmin[$i] = 0;
                        }
                        echo $status_admin.'<br><small>'.$keterangan_admin.'</small>';
                        ?>
                      </td>
                      <td style="text-align:center">
                      <?php
                        if($rekanan_evaluasi_admin->getField("JUMLAH_DILENGKAPI") >= 1 && $rekanan_evaluasi_teknis->getField("JUMLAH_DILENGKAPI") >= 1)
                        {
                          $status_teknis = '<img src="images/centang.png">';
                          $arrEvaluasiTeknis[$i] = 1;
                          if ($reqMetodeEvaluasiId == '2' || $reqMetodeEvaluasiId == '10') {
                            $skor_teknis = 'Nilai Teknis <b>'.$rekanan_evaluasi_teknis->getField("NILAI_TEKNIS").'</b><br>'.$rekanan_evaluasi_teknis->getField("KETERANGAN_LULUS");
                            $skor_teknis_angka[$arrPaketRekananId[$i]] = $rekanan_evaluasi_teknis->getField("NILAI_TEKNIS");
                          } else {
                            $keterangan_teknis = $rekanan_evaluasi_teknis->getField("KETERANGAN_LULUS");
                          }
                          // $arrEvaluasiTeknis[$i] = 1;
                        }
                        else
                        {
                          $status_teknis = '<img src="images/uncentang.png">';
                          $skor_teknis = 'Nilai Teknis <b>'.$rekanan_evaluasi_teknis->getField("NILAI_TEKNIS").'</b>';
                          $keterangan_teknis = $rekanan_evaluasi_teknis->getField("KETERANGAN_GAGAL");
                          $arrEvaluasiTeknis[$i] = 0;
                        }
                        // echo $status_teknis.'<br><small>'.$keterangan_teknis.'</small>';
                          if ($reqMetodeEvaluasiId == '2' || $reqMetodeEvaluasiId == '10') { ?>
                            <?= $status_teknis.'<br><small>'.$skor_teknis.'</small><br><small>'.$keterangan_teknis.'</small>'; ?>
                          <?php
                          } else { ?>
                            <?= $status_teknis.'<br><small>'.$keterangan_teknis.'</small>'; ?>
                          <?php
                          } ?>
                      </td>
                      <?php
                      } ?>
                      <td style="text-align:center">
                      <?php
                        if ($reqMetodePengadaan != 7) {
                          if($rekanan_evaluasi_admin->getField("JUMLAH_DILENGKAPI") >= 1 && $rekanan_evaluasi_teknis->getField("JUMLAH_DILENGKAPI") >= 1 && $rekanan_evaluasi_harga->getField("JUMLAH_DILENGKAPI") >= 1)
                          {
                            $status_harga = '<img src="images/centang.png">';
                            $arrEvaluasiHarga[$i] = 1;
                            if ($reqMetodeEvaluasiId == '2') {
                              $skor_harga = 'Nilai Harga <b>'.$rekanan_evaluasi_harga->getField("NILAI_HARGA").'</b><br>'.$rekanan_evaluasi_harga->getField("KETERANGAN_LULUS");
                              $skor_harga_angka[$arrPaketRekananId[$i]] = $rekanan_evaluasi_harga->getField("NILAI_HARGA");
                            } else {
                              $keterangan_harga = $rekanan_evaluasi_harga->getField("KETERANGAN_LULUS");
                            }

                          }
                          else
                          {
                            $status_harga = '<img src="images/uncentang.png">';
                            $skor_harga = 'Nilai Harga <b>'.$rekanan_evaluasi_harga->getField("NILAI_HARGA").'</b>';
                            $keterangan_harga = $rekanan_evaluasi_harga->getField("KETERANGAN_GAGAL");
                            $arrEvaluasiHarga[$i] = 0;
                          }
                        } else
                        {
                          if($rekanan_evaluasi_harga->getField("JUMLAH_DILENGKAPI") >= 1)
                          {
                            $status_harga = '<img src="images/centang.png">';
                            $arrEvaluasiHarga[$i] = 1;
                            if ($reqMetodeEvaluasiId == '2') {
                              $skor_harga = 'Nilai Harga <b>'.$rekanan_evaluasi_harga->getField("NILAI_HARGA").'</b>';
                            } else {
                              $keterangan_harga = $rekanan_evaluasi_harga->getField("KETERANGAN_LULUS");
                            }
                          }
                          else
                          {
                            $status_harga = '<img src="images/uncentang.png">';
                            $skor_harga = 'Nilai Harga <b>'.$rekanan_evaluasi_harga->getField("NILAI_HARGA").'</b>';
                            $keterangan_harga = $rekanan_evaluasi_harga->getField("KETERANGAN_GAGAL");
                            $arrEvaluasiHarga[$i] = 0;
                          }
                        }

                        // echo $status_harga.'<br><small>'.$keterangan_harga.'</small>';
                        if ($reqMetodeEvaluasiId == '2') { ?>
                          <?= $status_harga.'<br><small>'.$skor_harga.'<br>'.$keterangan_harga.'</small>'; ?>
                        <?php
                        } else { ?>
                          <?= $status_harga.'<br><small>'.$keterangan_harga.'</small>'; ?>
                        <?php
                        } ?>
                      </td>

                      <?php 
                      if ($reqMetodeEvaluasiId == '2') {
                        $totalKombinasi = $rekanan_evaluasi_harga->getField("NILAI_HARGA") + $rekanan_evaluasi_teknis->getField("NILAI_TEKNIS");
                        echo '<td style="text-align:center">'.$totalKombinasi.'</td>';
                      } ?>

                      <td><?=numberToIna($paket_rekanan->getField("JUMLAH_KOREKSI"))?></td>

                      <?php
                      if ($bidding == '0' || $bidding == '') { // jika Sistem Negosiasi  ?>
                      <td>
                        <?php
                        if ($reqRekananIdPemenang == $paket_rekanan->getField("REKANAN_ID")) {
                          echo numberToIna($jumlahNegosiasi).'';
                        } else {
                          echo "";
                        }

                        ?>
                      </td>
                      <?php
                      } else { 
                      if($rekanan_evaluasi_admin->getField("JUMLAH_DILENGKAPI") >= 1 && $rekanan_evaluasi_teknis->getField("JUMLAH_DILENGKAPI") >= 1 && $rekanan_evaluasi_harga->getField("JUMLAH_DILENGKAPI") >= 1)
                      { 
                          ?>
                        <td><?=numberToIna($paket_rekanan->getField("NILAI_PENAWARAN"))?></td>
                      <?php
                        } else {
                          echo '<td></td>';
                        }
                      } ?>
                      <td>
                        <?php
                        if ($reqMetodeLelang != '7') { // Selain Tender Cepat  
                          $jumlahKelulusan = 3; // 3 evaluasi
                          $totalEvaluasi =  $arrEvaluasiAdmin[$i] + $arrEvaluasiTeknis[$i] + $arrEvaluasiHarga[$i];
                        } else {
                          $jumlahKelulusan = 1; // 1 Evaluasi
                          $totalEvaluasi =  $arrEvaluasiHarga[$i];
                        }

                        if ($jumlahKelulusan == $totalEvaluasi) { 
                          if ($getpaket_pemenang->countRow() > 0) {
                            $nourut = $getpaket_pemenang->countRow()+$noLulus;
                            $nominimal = $noLulus+1;
                          } else {
                            $nourut = $noLulus;
                            $nominimal = 1;
                            $noLulus++;
                          }
                        }



                        if ($jumlahKelulusan == $totalEvaluasi) { 
                        // if ($totalPanitiaSudahValidasi >= $totalPanitiaSetuju) {
                           echo '<input type="radio" value="'.$paket_rekanan->getField("REKANAN_ID").'" name="reqPemenang" style="cursor:pointer">';
                        // } else {
                           // echo "-";
                        // }
                         ?>
                         <input type="number" name="reqPeringkat<?= $paket_rekanan->getField("REKANAN_ID") ?>" id="points" name="points" min="<?= $nominimal ?>" class="form-control ml-1" value="<?= $nourut ?>" style="display: inline; width: 72%">
                         <?php 
                         } ?>
                      </td>
                      <!-- <th width="10%">
                        <input type="number" name="reqPeringkat<?= $paket_rekanan->getField("REKANAN_ID") ?>" id="points" name="points" min="1" class="form-control" value="<?= $no ?>">
                      </th> -->
                    </tr>
                    <?php
                      $no++;
                      if ($getpaket_pemenang->countRow() > 0) {
                        if ($jumlahKelulusan == $totalEvaluasi) {
                          $noLulus++;
                        }
                      } else {
                      }
                      unset($rekanan_evaluasi_admin);
                      unset($rekanan_evaluasi_teknis);
                      unset($rekanan_evaluasi_harga);
                    }
                    } ?>
                  </tbody>
                </table>
                <!-- <div class="form-actions"> -->
                  <input type="hidden" name="reqId" value="<?=$reqId?>" />
                  <input type="hidden" name="submitSimpan" value="Simpan" />
                  <a href="main/index/paket_detil/?eid=<?=$reqId?>&key=<?=$reqUUID?>" class="<?= CLASS_BTN_DANGER ?> mr-1"> <?= BTN_KEMBALI ?></a>
                  <?php 
                  if ($reqMetodeLelang != '7') { // Selain Tender Cepat  
                    if ($reqSistemSampul == '1') { // 1 file 
                  ?>
                    <!-- <a href="main/index/evaluasi_penawaran_harga/?reqId=<?php // echo $reqId?>" class="<?php // echo  CLASS_BTN_INFO ?>"> <span class="fa fa-pencil-square-o"></span> Edit Evaluasi Penawaran  </a>  -->
                  <?php 
                    } else { // 2 File ?>
                    <!-- <a href="main/index/evaluasi_penawaran_harga_sampul2/?reqId=<?php // echo $reqId?>" class="<?php // echo  CLASS_BTN_INFO ?>"> <span class="fa fa-pencil-square-o"></span> Edit Evaluasi Penawaran  </a>  -->
                  <?php 
                    }
                  } else { // Tender Cepat ?>
                    <!-- <a href="main/index/evaluasi_penawaran_harga/?reqId=<?php // echo $reqId?>" class="<?php // echo  CLASS_BTN_INFO ?>"> <span class="fa fa-pencil-square-o"></span> Edit Evaluasi Penawaran  </a>  -->
                  <?php 
                  } ?>

                  <button type="submit" name="reqSubmit" id="reqSubmit" class="<?= CLASS_BTN_PRIMARY ?> pull-right"><?= BTN_SIMPAN ?> </button>
                <!-- </div>  -->
              </form>
            </div>
          </div>
        </div>
        <?php
        } ?>


          <hr>
          <div class="card mb-1 border-blue border-darken-1">
            <div class="card-content">
              <div class="p-1">
                <div class="alert bg-primary alert-icon-left alert-arrow-left alert-dismissible mb-2" role="alert">
                  <span class="alert-icon"><i class="fa fa-th"></i></span>  <strong>Pemenang <?php if ($reqMultiPemenang == '0') { } else { echo "(Multi Winner)"; } ?></strong>
                </div>
                <div class="table-responsive">
                  <table class="border-double table mb-0">
                    <thead>
                      <th width="<?php if ($reqMultiPemenang == '0') { echo "20%"; } else { echo "10%"; } ?>" class="text-left"><?php if ($reqMultiPemenang == '0') { echo "Urutan"; } else { echo "Pemenang"; } ?></th>
                      <th style="text-align: left;"><?php if ($reqMultiPemenang == '0') { echo "Nama Peserta"; } else { echo "Nama Pemenang"; } ?></th>
                      <!-- <th>Tanggal Penetapan</th> -->
                      <!-- <th> -->
                        <!-- File Penetapan Pemenang -->
                        <!-- <br><small>Format file .xls .xlsx .pdf & <br>Maksimal ukuran file 2MB </small> -->
                      <!-- </th> -->
                      <!-- <th>Publish</th> -->
                      <?php
                      if ($this->USER_LOGIN_ID == $reqUserLoginId) {
                       ?>
                      <th style="text-align:center" width="15%">Aksi</th>
                      <?php
                      } ?>
                    </thead>
                    <tbody>
                      <?php
                      if ($countpaket_pemenang > 0) {
                        $no=1;
                        while($getpaket_pemenang->nextRow())
                        {
                          ?>
                          <tr>
                           <td style="width:5%; <?php if ($reqMultiPemenang == '0') {} else { echo "text-align: center"; } ?>">
                            <?= $getpaket_pemenang->getField("PERINGKAT")?>
                              <?php 
                              if ($reqMultiPemenang == '0') {  
                                if ($no > 1) { 
                                  echo " <small>( Pemenang Cadangan ".$cadangan." )</small>";
                                } else { 
                                  echo " <small>( Pemenang )</small>";
                                }
                              } 
                              ?>
                           </td>
                           <td><?= $getpaket_pemenang->getField("NAMA")?> </td>
                           <!-- <td><?= getFormattedDate($getpaket_pemenang->getField("TANGGAL_PENETAPAN"))?></td> -->
                          <?php
                          if ($this->USER_LOGIN_ID == $reqUserLoginId)
                          { ?>
                              <td style="text-align:center">
                                <a onclick="deleteData('paket_pemenang_json/delete/', '<?= $getpaket_pemenang->getField("PAKET_PEMENANG_ID")?>')" class="") style="color:#fff">
                                  <span class="fa fa-trash btn btn-danger" style="padding:3px 8px !important"></span>
                                </a>
                              </td>
                          <?php
                          } ?>
                          </tr>
                        <?php
                          $no++;
                          if ($no > 1) { $cadangan++; }
                        } // end of while
                      } else {
                        echo  '<tr><td colspan="4">Pemenang belum di tetapkan</td>';
                      }
                      ?>
                    </tbody>
                  </table>
                  <?php
                  if ($this->USER_LOGIN_ID == $reqUserLoginId)
                  { ?>
                    <div class="col-md-12">
                      <?php
                       // echo $totalPanitiaSudahValidasi.'-'.$totalPanitiaSetuju;
                        // if ($totalPanitiaSudahValidasi >= $totalPanitiaSetuju) {
                          $this->load->library("libvalidasi"); $libvalidasi = new libvalidasi();
                          $cekPanitiaSetuju = $libvalidasi->cekPanitiaSetuju($totalPanitiaSetuju,$totalPanitiaSudahValidasi);
                          if ($cekPanitiaSetuju) {
                          if ($countpaket_pemenang > 0) {
                            if ($getpaket_pemenang->getField("PUBLISH") == '' || $getpaket_pemenang->getField("PUBLISH") == '0') { ?>
                              <a title="#" onclick="submitPublish2('<?= $reqId?>','1')" class="<?= CLASS_BTN_PRIMARY ?> pull-right" style="color:#fff; margin-top: 1%"><?= BTN_PUBLISH ?> Pemenang</a>
                        <?php
                            } else { ?>
                              <a title="#" onclick="submitPublish2('<?= $reqId?>','0')" class="<?= CLASS_BTN_DANGER ?> pull-right" style="color:#fff; margin-top: 1%"><i class="fa fa-close"></i> Batal publish</a>
                              <!-- <a title="#" onclick="openAdd('main/loadUrl/app/data_pemenang/?reqId=<?=$reqId?>')" class="btn btn-secondary pull-right mr-1" style="color:#fff; margin-top: 1%"> Panitia sudah setuju</a> -->
                        <?php
                            } ?>

                            <?php
                            // 1:Tender, 2:Pengadaan Langsung, 3:Tender Terbatas, 4:Seleksi Langsung, 5:Penunjukan Langsung, 6:e-Purchasing, 7:Tender Cepat, 8:Kompetisi
                            if ($reqPaketMetodeLelangId == '1' || $reqPaketMetodeLelangId == '3' || $reqPaketMetodeLelangId == '5' || $reqPaketMetodeLelangId == '7' || $reqPaketMetodeLelangId == '8' || $reqPaketMetodeLelangId == '10') {  ?>
                              <a title="#" onclick="openAdd('main/loadUrl/main/data_pemenang/?reqId=<?=$reqId?>')" class="<?= CLASS_BTN_SECONDARY ?> pull-right mr-1" style="color:#fff; margin-top: 1%"><span class="fa fa-check-square-o"></span> Tim Pengadaan</a>
                            <?php
                            } 

                            if ($this->USER_TYPE_ID == '11') 
                            { 
                            ?>
                              <form id="ffUpload" method="post" novalidate enctype="multipart/form-data" class="mt-1">
                                <input name="reqLinkFile" type="file" multiple class="maxsize-20240" accept="xls|xlsx|pdf" id="reqLinkFile" value=""/>
                                <br><span class="badge badge-dark"  style="margin-top: 5px">Upload File Penetapan Pemenang</span>
                                <small>Format file .xls .xlsx .pdf & Maksimal ukuran file 2MB </small>
                                <script>
                                // wait for document to load

                                  // $(function(){

                                    // invoke plugin
                                   // $('#reqLinkFile').MultiFile({
                                     //   onFileChange: function(){
                                       //     $("#reqSubmit2").click();
                                       // }
                                   // });

                               // });

                                  $( "#reqLinkFile" ).bind( "change", function() {
                                   document.querySelector('#reqSubmit2').click();
                                  });
                                </script>
                                <input type="hidden" name="reqId" value="<?=$reqId?>" />
                                <input type="hidden" name="reqNamaDokumen" id="reqNamaDokumen" value="File Penetapan Pemenang" />
                                <input type="hidden" name="reqJenisDokumen" id="reqJenisDokumen" value="PENETAPAN_PEMENANG" />
                                <input type="submit" name="reqSubmit2" id="reqSubmit2" value="" style="display:none">
                              </form>

                              <?php
                            }

                              $paket_dokumen = new PaketDokumen();
                              $paket_dokumen->selectByParams(array("PAKET_ID" => $reqId, "JENIS_DOKUMEN" => "PENETAPAN_PEMENANG"));
                              $paket_dokumen->firstRow();
                              $dokumen = $paket_dokumen->getField("PATH_FILE");
                              if($dokumen == "")
                              {
                                echo '<input type="hidden" name="reqTotalDokumen" id="reqTotalDokumen" value="0" />';
                              }
                              else
                              {

                              ?>
                              <a href="uploads/penawaran/<?=$dokumen?>" target="_blank" class="badge badge-danger round" style="margin-top: 5px; padding: 5px 15px;"> 
                                <?= ICON_DOWNLOAD ?> Download File Penetapan Pemenang 
                              </a>
                              <input type="hidden" name="reqTotalDokumen" id="reqTotalDokumen" value="1" />

                              <?php
                              }
                              ?>
                        <?php
                          }
                        } else {
                        ?>
                            <a title="#" onclick="openAdd('main/loadUrl/main/data_pemenang/?reqId=<?=$reqId?>')" class="<?= CLASS_BTN_DANGER ?> pull-right" style="color:#fff; margin-top: 1%"> <b><?= $totalPanitiaSetuju-$totalPanitiaSudahValidasi ?> </b> Panitia belum setuju</a>
                      <?php
                        }
                       ?>
                    </div>
                  <?php
                  } ?>
                </div>
              </div>
            </div>
          </div>
      <?php
      } ?>
        </div>
      </div>

    </div>
  </div>
</div>
