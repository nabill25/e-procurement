<?php
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 * 
 */
$reqId = $this->input->get("reqId");
$this->libsession->cekSession($reqId);

$this->load->library("paketinfo"); $paketInfo = new paketinfo();
include_once("functions/string.func.php");
include_once("functions/default.func.php");
include_once("functions/date.func.php");
$this->load->model("Metode");
$this->load->model(array("Paket","PaketTahap","PaketPenawaran"));

$metode = new Metode();
$paket_tahap = new PaketTahap();
$paket_tahap_metode = new PaketTahap();

$setKembali = ($this->input->get("back")) ? $this->input->get("back") : '';
if ($setKembali) {
    $setKembali = $setKembali;
} else {
    $setKembali = ($this->input->get("amp;back")) ? $this->input->get("amp;back") : '';
}

$paketInfo->getPaket($reqId);
$reqMetodeLelangId = $paketInfo->metode_lelang_id;
$reqPublishPaket = $paketInfo->publish_paket;
$reqRescheduleKe = $paketInfo->reschedule_ke ? $paketInfo->reschedule_ke : 0;
$reqReschedule1 = $paketInfo->reschedule_1;
$reqReschedule2 = $paketInfo->reschedule_2;
$reqReschedule3 = $paketInfo->reschedule_3;
$reqReschedule4 = $paketInfo->reschedule_4;
$reqReschedule5 = $paketInfo->reschedule_5;
$reqReschedule6 = $paketInfo->reschedule_6;
$reqReschedule7 = $paketInfo->reschedule_7;
$reqReschedule8 = $paketInfo->reschedule_8;
$reqReschedule9 = $paketInfo->reschedule_9;
$reqReschedule10 = $paketInfo->reschedule_10;
$reqUUID = $paketInfo->uuid;

$bukaSchedule = 0;
if ($reqRescheduleKe == 0 && $reqReschedule1 != '') {
    $bukaSchedule = 1;
} else if ($reqRescheduleKe == 1 && $reqReschedule2 != '') {
    $bukaSchedule = 1;
} else if ($reqRescheduleKe == 2 && $reqReschedule3 != '') {
    $bukaSchedule = 1;
} else if ($reqRescheduleKe == 3 && $reqReschedule4 != '') {
    $bukaSchedule = 1;
} else if ($reqRescheduleKe == 4 && $reqReschedule5 != '') {
    $bukaSchedule = 1;
} else if ($reqRescheduleKe == 5 && $reqReschedule6 != '') {
    $bukaSchedule = 1;
} else if ($reqRescheduleKe == 6 && $reqReschedule7 != '') {
    $bukaSchedule = 1;
} else if ($reqRescheduleKe == 7 && $reqReschedule8 != '') {
    $bukaSchedule = 1;
} else if ($reqRescheduleKe == 8 && $reqReschedule9 != '') {
    $bukaSchedule = 1;
} else if ($reqRescheduleKe == 9 && $reqReschedule10 != '') {
    $bukaSchedule = 1; 
} else {
    $bukaSchedule = 0;
}

$reqExistData = $metode->getCountByParams(array("PAKET_ID" => $reqId));
$metode->selectByParams(array(), -1, -1, $reqId);

if($reqMetodeLelangId == 1 || $reqMetodeLelangId == 3)
{
    $tempPublishTanggalJam = $paketInfo->publish_paket_tanggal;
    $arrPublishTanggalJam = explode(" ", $tempPublishTanggalJam);
    $arrPublishJamMenit = explode(":", $arrPublishTanggalJam[1]);
    $tempPublishTanggal = $arrPublishTanggalJam[0];
    $tempPublishJam = $arrPublishJamMenit[0];
    $tempPublishMenit = $arrPublishJamMenit[1];
}

$reqNama = $paketInfo->nama;
$reqPermohonanId = $paketInfo->permohonan_paket_id;
$reqNilai = $paketInfo->nilai; 

$paket_penawaran = new PaketPenawaran();
$paket_penawaran->selectByParams(array("A.PAKET_ID" => $reqId, "ITEM_CHILD" => "0"));
if ($paket_penawaran->countRow() == 0 ) {
    // ikn 20241105 By Pass tidak musti upload BoQ
    $paket_penawaran_insert = new PaketPenawaran();
    $paket_penawaran_insert->setField("PAKET_ID", $reqId);
    $paket_penawaran_insert->setField("NAMA", $reqNama);
    $paket_penawaran_insert->setField("LAST_CREATE_USER", $this->USER_LOGIN_ID);
    $paket_penawaran_insert->setField("JUMLAH", CommaToNo($reqNilai));
    $paket_penawaran_insert->setField("PERMOHONAN_PAKET_ID", $reqPermohonanId);
    $paket_penawaran_insert->insert2();
}

// Pengecualian untuk Masa Sanggah, tutup jadwal sampai evaluasi jika jadwal Masa Sanggah Sedang Berjalan atau sudah lewat
$arrSanggah = MASA_SANGGAH;
$jenis_tahap = $paket_tahap_metode->getJenisTahapById($reqId);
$aktif_masa_sanggah = $paket_tahap->getCountByParamsAktif(array("URUT" => $arrSanggah[$jenis_tahap], "PAKET_ID" => $reqId, "TAMPILKAN" => 1));
$aktif_masa_sanggah2 = $paket_tahap->getCountByParamsBerlalu(array("URUT" => $arrSanggah[$jenis_tahap], "PAKET_ID" => $reqId, "TAMPILKAN" => 1));
$listTutupJadwal = array("Pengumuman Tender Kualifikasi","Pendaftaran dan Download Dokumen Kualifikasi","Aanwijzing Kualifikasi Online","Upload Dokumen Kualifikasi","Evaluasi Kualifikasi","Pembuktian Kualifikasi","Penetapan dan Pengumuman Kualifikasi","Masa Sanggah Kualifikasi","Download Dokumen Pengadaan","Aanwijzing Online","Upload Dokumen Penawaran dan Enkripsi File 1","Pembukaan Penawaran File 1","Pengumuman Tender","Pendaftaran dan Download Dokumen Pengadaan","Upload Dokumen Penawaran dan Enkripsi","Pembukaan Penawaran","Pendaftaran dan  Download Dokumen Pengadaan",);
// echo $aktif_masa_sanggah .'-'. $aktif_masa_sanggah2;
if($aktif_masa_sanggah == 0 && $aktif_masa_sanggah2 == 0)
{
  $cekAktifSanggah = "buka";
} else {
  $cekAktifSanggah = "tutup";
}
// echo $cekAktifSanggah;
?>
<style type="text/css">
.table th {
    padding: 10px !important;
    background-color: #b7b7b7;
    color: #000;
}
</style>

<script type="text/javascript">
$(document).ready(function() {

    $(function(){
        $('#ff').form({
            url:'paket_tahap_json/add',
            onSubmit:function(){
                // return $(this).form('validate');
                var v=$(this).form('validate');
                if(v) showLoad();  // show the message box
                return v;
            },
            success:function(data){
                // alertError3(data);
                alertSuccess2('Data berhasil di simpan');
                setTimeout(function() {
                  document.location.href = data;
                }, 2000);
                // hideLoad();
            }
        });

        $("input[id^='reqTanggalSelesai']").datebox({
            onSelect: function(date){
                var idElement = $(this).attr("id").replace("reqTanggalSelesai", "");
                checkTanggal(idElement);
                // checkTanggalClash(idElement);
                cekJamMulai(idElement); // Tanggal selesai tidak boleh di bawah tanggal mulai

                var idElement2 = $(this).attr("id");
                var idElement3 = date.getFullYear()+'-'+(date.getMonth()+1)+'-'+date.getDate();
                var dateElement = date.getDay();
                var getDateVal = $(this).attr("data-setholiday");
                cekHolidayDate(getDateVal,dateElement,idElement2,idElement3);
            }
        });

        $("input[id^='reqTanggalMulai']").datebox({
            onSelect: function(date){
                cekJamMulai(idElement); // Tanggal selesai tidak boleh di bawah tanggal mulai
                var idElement2 = $(this).attr("id");
                var idElement3 = date.getFullYear()+'-'+(date.getMonth()+1)+'-'+date.getDate();
                var dateElement = date.getDay();
                var getDateVal = $(this).attr("data-setholiday");
                cekHolidayDate(getDateVal,dateElement,idElement2,idElement3);
            }
        });

        // $("input[id^='reqJamSelesai']").textbox({
        //   onChange: function(value){
        //     alert('The value has been changed to ' + value);
        //   }
        // });


    });

    for (var i = 0; i < 30; i++) {
  
      $('#reqTanggalMulai'+i).datebox({
        editable: false
      });

      $('#reqTanggalSelesai'+i).datebox({
        editable: false
      });
    }
});

function changeJamSelesai(idElement) {
    checkTanggalClash(idElement);
}

function cekHolidayDate(a,b,c,d) {
 if (a === '1') { // cek tanggal merah dan libur
  // cek sabtu minggu
  var dayNames= ["Minggu","Senin","Selasa","Rabu","Kamis","jum'at","Sabtu"];
  var dayNumber= ["1","2","3","4","5","6","7"];
  if (dayNumber[b] == "1" || dayNumber[b] == "7") {
    alertError3("Jadwal ini tidak bisa pada hari "+dayNames[b]+ ", Silahkan cari tanggal lain");
    $('#'+c).datebox('setValue', '');
  }
  // cek tanggal merah
  $.getJSON('paket_metode_lelang_json/cektanggalmerah/?id='+d,
  function(dataJson){
   if(dataJson.message == "1")
   {
    alertError3("Jadwal bertepatan dengan tanggal merah "+dataJson.data);
    $('#'+c).datebox('setValue', '');
   }
  });
 }
}

function checkTanggal(idElement)
{
    var awal = $('#reqTanggalMulai'+idElement).datebox('getValue');
    var selesai = $('#reqTanggalSelesai'+idElement).datebox('getValue');
    var triggerAkhir = $('#reqTriggerTanggalAkhir'+idElement).val();
    if(awal == "")
    {
        alertError3("Tentukan tanggal mulai.");
        $('#reqTanggalSelesai'+idElement).datebox('setValue', '');
        return;
    }

    var dt1   = parseInt(awal.substring(0,2),10);
    var mon1  = parseInt(awal.substring(3,5),10);
    var yr1   = parseInt(awal.substring(6,10),10);
    var dt2   = parseInt(selesai.substring(0,2),10);
    var mon2  = parseInt(selesai.substring(3,5),10);
    var yr2   = parseInt(selesai.substring(6,10),10);
    var date1 = new Date(yr1, mon1, dt1);
    var date2 = new Date(yr2, mon2, dt2);


    if(date2 < date1)
    {
        alertError3("Tanggal selesai tidak boleh lebih kecil dari tanggal mulai.");
        $('#reqTanggalSelesai'+idElement).datebox('setValue', '');
    }

    if(triggerAkhir == "1")
    {
        $('#reqTanggalMulai'+(Number(idElement)+1)).datebox('setValue', selesai);
    }

}

function checkTanggalClash(idElement)
{
    var selesai = $('#reqTanggalSelesai'+idElement).datebox('getValue');
    var reqJamSelesai = $('#reqJamSelesai'+idElement).val();
    var reqMenitSelesai = $('#reqMenitSelesai'+idElement).val();
    var tgl = selesai+' '+reqJamSelesai+':'+reqMenitSelesai;
    var id = <?= $reqId; ?>;
    
    if(selesai != '' && reqJamSelesai != '' && reqMenitSelesai != ''){
    // cek jadwal existing
      $.getJSON('paket_metode_lelang_json/cekjadwalexisting/?tgl='+tgl+'&reqId='+id,
      function(dataJson){
       if(dataJson.message == "1")
       {
        alertError3("Jadwal bentrok dengan "+dataJson.jadwal +" "+dataJson.data);
        $('#reqJamSelesai'+idElement).val('');
        $('#reqMenitSelesai'+idElement).val('');
        $('#reqTanggalSelesai'+idElement).datebox('setValue', '');
       }
      });
    }
}

function checkNotifikasi(id, notifikasi)
{
    if(notifikasi == "PENAWARAN")
    {
        if($("#reqHadir"+id).is(":checked"))
            $("#lblNotifikasi"+notifikasi).text("Melalui offline");
        else
            $("#lblNotifikasi"+notifikasi).text("Melalui online");
    }
}

$('#my-modal').on('hidden.bs.modal', function () {
  window.alert('hidden event fired!');
});

// //Disable cut copy paste
// $(document).bind('cut copy paste', function (e) {
//     e.preventDefault();
// });

// //Disable mouse right click
// $(document).on("contextmenu",function(e){
//     return false;
// });

function dateToTimeStamp(str) {
  var date = new Date(str);
  alert(date);
  return date.getTime();
}

function timeStampToDate(str) {
    var timestamp = parseInt(str, 10);
    var date = new Date(timestamp);
    return date.toISOString().substr(0, 10);
}


function cekJamMulai(a) {
 
    var Mulai = $('#reqTanggalMulai'+a).datebox('getValue');
    var reqJamMulai = $('#reqJamMulai'+a).val();
    var reqMenitMulai = $('#reqMenitMulai'+a).val();
    var tglMulai = Mulai+' '+reqJamMulai+':'+reqMenitMulai;

    var Selesai = $('#reqTanggalSelesai'+a).datebox('getValue');
    var reqJamSelesai = $('#reqJamSelesai'+a).val();
    var reqMenitSelesai = $('#reqMenitSelesai'+a).val();
    var tglSelesai = Selesai+' '+reqJamSelesai+':'+reqMenitSelesai;

    if (reqMenitSelesai != '' && reqJamSelesai == '') {
        alertError3("Jam Selesai tidak boleh kosong");
        $('#reqMenitSelesai'+a).val('');
        return false;
    }

    if (reqJamSelesai && reqMenitSelesai) {
        $.ajax({
            url : '<?= base_url() ?>paket_metode_lelang_json/comparetanggal',
            type: 'POST',
            data: {'tglMulai':tglMulai,'tglSelesai':tglSelesai},
            dataType: 'JSON',
            success: function(data)
            {
                if (data.data == '1') {
                    alertError3(data.message);
                    $('#reqJamSelesai'+a).val('');
                    $('#reqMenitSelesai'+a).val('');
                    return false;
                }
            },
            error: function (jqXHR, textStatus, errorThrown)
            {
                alertError3("Data gagal diproses, silahkan dicoba kembali");
            }
        });
    }

}

</script>

<div class="row">
  <div class="col-md-12 col-sm-12">
    <div class="card">
      <div class="card-header card-head-inverse bg-primary">
        <h4 class="card-title text-white">Jadwal <?= $paketInfo->metode_lelang_nama ?></h4>
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
        <div class="card-body area-datatable">
            <a href="main/loadUrl/report/jadwal_pdf/?reqId=<?=$reqId?>" class="<?= CLASS_BTN_INFO ?> mr-1 text-white mb-2" target="_blank"> <?= BTN_PRINT ?> Jadwal </a>
            <?php
            if ($reqRescheduleKe < 10 && $reqPublishPaket == 1) { ?>
            <a <?php echo 'onClick="openAdd(\'main/loadUrl/main/reschedule?reqId='.$reqId.'&reqKe='.$reqRescheduleKe.'&back='.$setKembali.'\');"'; ?> class="<?= CLASS_BTN_DANGER ?> mr-1 text-white mb-2" target="_blank"> <?= BTN_RESCHEDULE ?> Jadwal </a>
            <?php
            } ?>

            <?php 
            if ($reqRescheduleKe >= 1) { ?>
            <a <?php echo 'onClick="openAdd(\'main/loadUrl/main/reschedule_rekamjejak?reqId='.$reqId.'\');"'; ?> class="<?= CLASS_BTN_DARK ?> mr-1 text-white mb-2" target="_blank"> <i class="fa fa-paw"></i> Rekam Jejak Reschedule Jadwal </a>
            <?php 
            } ?>

            <?php
            if ($reqPublishPaket == 1) {
                $html = '<div class="alert alert-danger" style="color:#fff">
                            <span style="color: #fff">
                            Paket Pengadaan '.$paketInfo->metode_lelang_nama.' <b><u>sudah di publish</u></b>,'; ?>

            <?php
            if ($reqRescheduleKe >= 10) {
                $html .= ' Jadwal tidak bisa diubah karna Sudah dilakukan <b><u>'.$reqRescheduleKe.' kali</u></b> reschedule ';
            } else {
                $html .= 'jika ada perubahan jadwal silahkan klik tombol Reschedule Jadwal dan isi alasan perubahan  <br> Sudah dilakukan <b><u>'.$reqRescheduleKe.' kali</u></b> reschedule. <br>
                        Note : Maksimal 10 kali reschedule
                      </span>';
            }
            if ($reschedule_ke >= 0) {
                $html .= $reqReschedule1 ? '<br><hr>' : '';
                $html .= '<p style="color:#fff">
                          <b>Alasan</b> <br>';
                $html .= $reqReschedule1 ? 'Reschedule 1 : <u>'.$reqReschedule1.'</u><br>' : '';
                // $html .= '<a onClick="openAdd(\'main/loadUrl/main/reschedule?reqId='.$reqId.'&reqKe='.$reqRescheduleKe.'&back='.$setKembali.'\');"><b><i><u> Lihat detail reschedule 1</u></i></b> </a><br>';
                $html .= $reqReschedule2 ? 'Reschedule 2 : <u>'.$reqReschedule2.'</u><br>' : '';
                $html .= $reqReschedule3 ? 'Reschedule 3 : <u>'.$reqReschedule3.'</u><br>' : '';
                $html .= $reqReschedule4 ? 'Reschedule 4 : <u>'.$reqReschedule4.'</u><br>' : '';
                $html .= $reqReschedule5 ? 'Reschedule 5 : <u>'.$reqReschedule5.'</u><br>' : '';
                $html .= $reqReschedule6 ? 'Reschedule 6 : <u>'.$reqReschedule6.'</u><br>' : '';
                $html .= $reqReschedule7 ? 'Reschedule 7 : <u>'.$reqReschedule7.'</u><br>' : '';
                $html .= $reqReschedule8 ? 'Reschedule 8 : <u>'.$reqReschedule8.'</u><br>' : '';
                $html .= $reqReschedule9 ? 'Reschedule 9 : <u>'.$reqReschedule9.'</u><br>' : '';
                $html .= $reqReschedule10 ? 'Reschedule 10 : <u>'.$reqReschedule10.'</u>' : '';
                $html .= '</p>';
            }
                $html .=  '</div>';

                echo $html;
             } ?>
            <div class="table-responsive">
              <table class="table mb-0 table-bordered">
                <tbody>
                    <tr valign="top" class="judul-kolom">
                      <th rowspan="3" valign="middle" style="width:1%; text-align: center; vertical-align: middle;">No</th>
                      <th rowspan="3" valign="middle" style="width:40%; text-align: center; vertical-align: middle;">Tahapan <br><?= $paketInfo->metode_lelang_nama ?></th>
                      <!-- <th rowspan="3" valign="middle" style="width:2%; text-align: center">Hadir</th> -->
                      <th rowspan="3" valign="middle" style="width:2%; text-align: center; vertical-align: middle;">Tampil
                      <!-- <input type="checkbox" name="reqTampillAll" id="reqTampillAll" onChange="cek_semua_tampil(document.frmInformasiAdd.reqTampil)" style="cursor: pointer;"/>
                        <label for="reqTampillAll"></label> -->
                        </th>
                      <th colspan="5" valign="top" style=" text-align: center"> Waktu Pelaksanaan </th>
                    </tr>
                    <tr valign="top" class="judul-kolom">
                      <th colspan="2" style=" text-align: center"> Mulai </th>
                      <th colspan="2" style=" text-align: center">Selesai</th>
                      <th rowspan="2" valign="middle" style="width:2%; text-align: center; vertical-align: middle;">Selisih</th>
                      </tr>
                    <tr valign="top" class="judul-kolom">
                      <th style=" text-align: center" width="10%"> Tanggal </th>
                      <th style=" text-align: center" width="12%"> Jam </th>
                      <th style=" text-align: center" width="10%"> Tanggal </th>
                      <th style=" text-align: center" width="12%"> Jam </th>
                    </tr>

                    <?php
                    $i=1; $no=1; $stat = ''; $stat_m = '';
                    while($metode->nextRow())
                    {
                        if($stat == '') $comma = '';    else    $comma = ', ';
                        $stat .= $comma."#reqJamSelesai$i, #reqJamMulai$i";

                        if($stat_m == '') $comma = '';  else    $comma = ', ';
                        $stat_m .= $comma."#reqMenitSelesai$i, #reqMenitMulai$i";

                        $disabledTanggalAwal = $metode->getField("TANGGAL_AWAL_DISABLED");
                        $triggerTanggalAkhir = $metode->getField("TANGGAL_AKHIR_TRIGGER");

                        // jika paket sudah publish jadwal tidak bisa di input
                        if ($reqPublishPaket == 1 && $bukaSchedule == 0)
                        {
                            ?>
                        <tr valign="top" class="gelap">
                          <td><?=$no?>.</td>
                            <td>
                                <?php
                                $namaJadwal = $metode->getField("NAMA");
                                echo $namaJadwal; ?>
                            </td>
                            <td>
                                <?php 
                                if($metode->getField("TAMPILKAN_CENTANG") == 1 && $metode->getField("TAMPILKAN") == 1):
                                  echo '<i class="fa fa-check-square-o"></i>';
                                else:
                                  echo '';
                                endif; ?>
                            </td>
                            <td align="center">
                                <?php
                                if($i == 1 && $metode->getField("TANGGAL_AWAL") == '')
                                    $tmpTanggalMulai = date("d-m-Y");
                                else
                                    $tmpTanggalMulai = datetimeToPage($metode->getField("TANGGAL_AWAL"), "date");
                                ?>
                                <?=$tmpTanggalMulai?>
                            </td>
                            <td align="center">
                                <?php
                                $arrJamAwal = explode(":", $metode->getField("JAM_AWAL"));
                                $arrJamAkhir = explode(":", $metode->getField("JAM_AKHIR"));

                                echo $arrJamAwal[0].':'.$arrJamAwal[1];
                                ?>
                            </td>
                            <td align="center">
                                 <?=datetimeToPage($metode->getField("TANGGAL_AKHIR"), "date")?>
                            </td>
                            <td align="center">
                                 <?=$arrJamAkhir[0]?>  <?php if($arrJamAkhir[1]) {  echo ' :'.$arrJamAkhir[1]; }?>
                                <input type="hidden" name="reqTahapanLelang[<?=$i?>]" value="<?=$namaJadwal?>" />
                                <input type="hidden" name="reqTriggerTanggalAkhir[<?=$i?>]" id="reqTriggerTanggalAkhir<?=$i?>" value="<?=$triggerTanggalAkhir?>" />
                            </td>
                            <td style="padding: .75rem .2rem !important; text-align: center">
                                <?php 
                                echo hitungSelisih($metode->getField("TANGGAL_AWAL"),$metode->getField("TANGGAL_AKHIR"));
                                 ?>
                            </td>
                        </tr>
                    <?php
                        } else
                        { // if ($reqPublishPaket == 1 && $bukaSchedule == 0)

                    ?>
                        <tr valign="top" class="gelap">
                          <td><?=$no?>.</td>
                            <td>
                                <?php
                                $namaJadwal = $metode->getField("NAMA");
                                // if ($namaJadwal == 'Masa Sanggah') {
                                    echo $namaJadwal; 
                                // }
                                ?>
                            </td>
                            <td>
                                <?php 
                                if($reqExistData == "0") { 
                                    if($metode->getField("TAMPILKAN")):
                                      $tmpTampilCentang = 1;
                                    else:
                                      $tmpTampilCentang = 0;
                                    endif; 
                                } else {
                                    if($metode->getField("TAMPILKAN_CENTANG")):
                                      $tmpTampilCentang = 1;
                                    else:
                                      $tmpTampilCentang = 0;
                                    endif; 
                                }

                                ?>
                                <input type="checkbox" name="reqTampil[<?=$i?>]" id="reqTampil<?=$i?>" value="1"  id="reqTampil" <?php if($tmpTampilCentang == 1) { ?> checked="checked" <?php } else { if ($tmpTampilCentang == 1) { echo 'checked="checked"'; } } ?>  class="form-controls" style="cursor: pointer;"/>
                                <label for="reqTampil"></label>
                            </td>
                            <td>
                                <?php
                                if($i == 1 && $metode->getField("TANGGAL_AWAL") == '')
                                    $tmpTanggalMulai = date("d-m-Y");
                                else
                                    $tmpTanggalMulai = datetimeToPage($metode->getField("TANGGAL_AWAL"), "date");
                                ?>
                                <input type="text" class="form-controls easyui-datebox span2" name="reqTanggalMulai[<?=$i?>]" id="reqTanggalMulai<?=$i?>"
                                <?php if ($metode->getField("CEK_TANGGAL_MERAH") == '1') { echo 'data-setholiday="1"'; } else { echo 'data-setholiday="0"'; } ?>
                                 value="<?=$tmpTanggalMulai?>" <?php if($disabledTanggalAwal == "1") { ?> readonly style="background:#F1F1F1; width: 140% !important" <?php } else { ?> style="background:#F1F1F1; width: 140% !important"  <?php } ?> 
                                 <?php 
                                 // if (in_array($namaJadwal,$listTutupJadwal) && $cekAktifSanggah == 'tutup') { echo "readonly"; } ?>
                                 />
                                 
                                 <!-- <input type="checkbox" name="reqTampil[<?=$i?>]" id="reqTampil<?=$i?>" value="1"  id="reqTampil"  checked="checked" class="form-controls" style="display: none;"/> -->
                            </td>
                            <td class="text-center" style="padding: 10px 5px !important">
                                <?php
                                $arrJamAwal = explode(":", $metode->getField("JAM_AWAL"));
                                ?>
                                <input name="reqJamMulai[<?=$i?>]" type="text" id="reqJamMulai<?=$i?>" value="<?=$arrJamAwal[0]?>" onChange="return cekJamMulai('<?=$i?>')" size="2" maxlength="2" <?php if($disabledTanggalAwal == "1") { ?> readonly style="background:#F1F1F1; width: 50px; display: inline;" <?php } ?> class="form-control" style="width: 50px; display: inline;"
                                <?php 
                                 // if (in_array($namaJadwal,$listTutupJadwal) && $cekAktifSanggah == 'tutup') { echo "readonly"; } ?>
                                />
                                :
                                <input name="reqMenitMulai[<?=$i?>]" type="text" id="reqMenitMulai<?=$i?>" value="<?=$arrJamAwal[1]?>" onChange="return cekJamMulai('<?=$i?>')" size="2" maxlength="2" <?php if($disabledTanggalAwal == "1") { ?> readonly style="background:#F1F1F1;width: 50px; display: inline;" <?php } ?> class="form-control" style="width: 50px; display: inline;"
                                <?php 
                                 // if (in_array($namaJadwal,$listTutupJadwal) && $cekAktifSanggah == 'tutup') { echo "readonly"; } ?>
                                />
                            </td>
                            <td>
                                <input type="text" class="form-control easyui-datebox span2" name="reqTanggalSelesai[<?=$i?>]" id="reqTanggalSelesai<?=$i?>"
                                <?php if ($metode->getField("CEK_TANGGAL_MERAH") == '1') { echo 'data-setholiday="1"'; } else { echo 'data-setholiday="0"'; } ?>
                                value="<?=datetimeToPage($metode->getField("TANGGAL_AKHIR"), "date")?>" style="width: 140% !important" <?php if ($metode->getField("TANGGAL_AKHIR_MANDATORY") == '1') { echo 'required=""'; } else { } ?>
                                <?php 
                                 // if (in_array($namaJadwal,$listTutupJadwal) && $cekAktifSanggah == 'tutup') { echo "readonly"; } ?>
                                />
                            </td>
                                <?php
                                $arrJamAkhir = explode(":", $metode->getField("JAM_AKHIR"));
                                ?>
                            <td class="text-center" style="padding: 10px 5px !important">
                                <input name="reqJamSelesai[<?=$i?>]" type="text" value="<?=$arrJamAkhir[0]?>" onChange="return cekJamMulai('<?=$i?>')" id="reqJamSelesai<?=$i?>" size="2" maxlength="2" <?php if($triggerTanggalAkhir == "1") { ?> onKeyUp="$('#reqJamMulai<?=$i+1?>').val(this.value);" <?php } ?> class="form-control" style="width: 50px; display: inline;" <?php if($metode->getField("CEK_TANGGAL_CLASH") == "1") { ?> onChange="changeJamSelesai('<?=$i?>')" <?php } ?>
                                <?php 
                                 // if (in_array($namaJadwal,$listTutupJadwal) && $cekAktifSanggah == 'tutup') { echo "readonly"; } ?>
                                />
                                :
                                <input name="reqMenitSelesai[<?=$i?>]" type="text" value="<?=$arrJamAkhir[1]?>" id="reqMenitSelesai<?=$i?>" size="2" onChange="return cekJamMulai('<?=$i?>')" maxlength="2" <?php if($triggerTanggalAkhir == "1") { ?> onKeyUp="$('#reqMenitMulai<?=$i+1?>').val(this.value);" <?php } ?> class="form-control" style="width: 50px; display: inline;" <?php if($metode->getField("CEK_TANGGAL_CLASH") == "1") { ?> onChange="changeJamSelesai('<?=$i?>')" <?php } ?>
                                <?php 
                                 // if (in_array($namaJadwal,$listTutupJadwal) && $cekAktifSanggah == 'tutup') { echo "readonly"; } ?>
                                />

                                <input type="hidden" name="reqTanggalChash[<?=$i?>]" value="<?= $metode->getField("CEK_TANGGAL_CLASH")?>" />
                                <input type="hidden" name="reqTahapanLelang[<?=$i?>]" value="<?=$namaJadwal?>" />
                                <input type="hidden" name="reqTriggerTanggalAkhir[<?=$i?>]" id="reqTriggerTanggalAkhir<?=$i?>" value="<?=$triggerTanggalAkhir?>" />
                            </td>
                            <td style="padding: .75rem .2rem !important; text-align: center">
                                <?php 
                                echo hitungSelisih($metode->getField("TANGGAL_AWAL"),$metode->getField("TANGGAL_AKHIR"));
                                 ?>
                            </td>
                        </tr>
                    <?php
                        } // if ($reqPublishPaket == 1) {
                        $i++;
                        $no++;
                    }
                    ?>
                </tbody>
              </table>
            </div>

            <div class="form-actions">
                <input type="hidden" name="reqId" value="<?=$reqId?>">
                <input type="hidden" name="back" value="<?=$setKembali?>"> <!-- set kembali -->
                <input type="hidden" name="submitSimpan" value="<?php if($reqExistData == "0") { ?>Simpan<?php } else { ?>Update<?php } ?>" />
                <?php
                if ($bukaSchedule == 1) { ?>
                <input type="hidden" name="submitReschedule" value="1" />
                <input type="hidden" name="rescheduleKe" value="<?=$reqRescheduleKe ?>" />
                <?php
                 } else { ?>
                <input type="hidden" name="submitReschedule" value="0" />
                <input type="hidden" name="rescheduleKe" value="0" />
                <?php
                 } ?>

                <?php
                if ($setKembali == '1') { ?>
                    <a href="main/index/paket_detil/?eid=<?=$reqId?>&key=<?= $reqUUID ?>" class="<?= CLASS_BTN_DANGER ?> mr-1"> <?= BTN_KEMBALI ?> </a>
                    <?php
                    if ($reqPublishPaket == 1 && $bukaSchedule == 0) {?>
                        <!-- <a href="main/index/paket_lelang_tambah_rekanan/?reqId=<?=$reqId?>" class="btn btn-danger mr-1 text-white"> <i class="fa fa-arrow-left"></i> Kembali </a>  -->
                    <?php
                    } else { ?>
                        <button type="submit" class="<?= CLASS_BTN_PRIMARY ?> pull-right"><?php if($reqExistData == "0") { ?>Simpan<?php } else { ?>Update<?php } ?> <i class="fa fa-check-square-o"></i></button>
                    <?php
                    } ?>
                <?php
                } else { ?>
                    <a href="main/index/paket_lelang_tambah_rincian_pekerjaan/?reqId=<?=$reqId?>" class="<?= CLASS_BTN_DANGER ?> mr-1"> <?= BTN_KEMBALI ?> </a>
                    <!-- <button type="submit" class="btn btn-primary pull-right">Simpan <i class="fa fa-arrow-right"></i></button> -->
                    <?php
                    if ($reqPublishPaket == 1 && $bukaSchedule == 0) {
                        // 1-e-Tender ,2-Pengadaan Langsung, 3-Tender Terbatas, ,5-Penunjukan Langsung Cepat,7-e-Tender Cepat, 8:Kompetisi
                        if ($reqMetodeLelangId == 2 || $reqMetodeLelangId == 3 || $reqMetodeLelangId == 5 || $reqMetodeLelangId == 8) {
                        ?>
                            <a href="main/index/paket_lelang_tambah_rekanan/?reqId=<?=$reqId?>" class="<?= CLASS_BTN_PRIMARY ?> mr-1 pull-right"><?= BTN_LANJUT ?></a>
                    <?php
                        }
                    } else {
                      if($reqExistData == "0") { ?>
                        <button type="submit" class="<?= CLASS_BTN_PRIMARY ?> pull-right"><?= BTN_SIMPAN ?></button>
                  <?php
                    } else { ?>
                        <button type="submit" class="<?= CLASS_BTN_PRIMARY ?> pull-right"><?= BTN_UBAH ?></button>

                  <?php
                      }
                    } ?>
                <?php
                } ?>
            </div>
        </div>
      </div>
      </form>

    </div>
  </div>
</div>
