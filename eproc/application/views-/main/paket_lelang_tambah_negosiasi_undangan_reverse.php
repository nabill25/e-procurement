<?php
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 * 
 */
date_default_timezone_set('Asia/Jakarta');

$reqId = httpFilterRequest("reqId");
$this->libsession->cekSession($reqId);

$this->load->library("kauth");  $userLogin = new kauth();
$this->load->library("paketinfo"); $paketInfo = new paketinfo();
include_once("functions/string.func.php");
include_once("functions/default.func.php");
include_once("functions/date.func.php");
$this->load->model("PaketRekanan");
$this->load->model("Paket");
$this->load->model("Metode");
$this->load->model("RekananPaketPenawaran");
$this->load->model("PaketNegoisasi");

$paket_rekanan = new PaketRekanan();
$paket_rekanan_satuan = new PaketRekanan();
$rekanan_paket_penawaran = new RekananPaketPenawaran();

$reqMode = httpFilterGet("reqMode");


$paketInfo->getPaket($reqId);
$reqNamaPaket = $paketInfo->nama;
$reqRekananIdPemenang = $paketInfo->rekanan_id_pemenang;
$bidding = $paketInfo->bidding;
$reqUUID = $paketInfo->uuid; 

$paket_rekanan_satuan->selectByParams(array("PAKET_ID" => $reqId, "A.REKANAN_ID" => coalesce($reqRekananIdPemenang, 0)), -1, -1, " AND A.TANGGAL_DAFTAR IS NOT NULL AND A.LULUS_PENDAFTARAN = 1 AND A.LULUS_KUALIFIKASI = 1 ");
$paket_rekanan_satuan->firstRow();

$paket_rekanan->selectByParams(array("A.PAKET_ID" => $reqId, "A.REKANAN_ID" => coalesce($reqRekananIdPemenang, 0)), -1, -1, " AND A.TANGGAL_DAFTAR IS NOT NULL AND A.LULUS_PENDAFTARAN = 1 AND A.LULUS_KUALIFIKASI = 1 ");

$i = 0;
while($paket_rekanan->nextRow())
{
  $arrRekananId[$i] = $paket_rekanan->getField("REKANAN_ID");
  $arrRekanan[$i] = $paket_rekanan->getField("REKANAN");
  $arrPaketRekananId[$i] = $paket_rekanan->getField("PAKET_REKANAN_ID");
  $arrPaketRekananNilai[$i] = $paket_rekanan->getField("NILAI_PENAWARAN");
  $arrDiemailNegosiasi[$i] = $paket_rekanan->getField("DI_EMAIL_NEGOSIASI");

  if($reqRekananIdPemenang == $paket_rekanan->getField("REKANAN_ID"))
    $indexRekananPememenang = $i;

  $i++;
}

if (is_array($arrRekanan)) {
  $arrRekananId = $arrRekananId;
  $arrRekanan = $arrRekanan;
  $arrPaketRekananId = $arrPaketRekananId;
  $arrPaketRekananNilai = $arrPaketRekananNilai;
  $arrDiemailNegosiasi = $arrDiemailNegosiasi;
} else {
  $arrRekananId = array();
  $arrRekanan = array();
  $arrPaketRekananId = array();
  $arrPaketRekananNilai = array();
  $arrDiemailNegosiasi = array();
}
 

$rekanan_paket_penawaran->selectByParamsEvaluasiRekanan($arrPaketRekananId, array("A.PAKET_ID" => $reqId));

$metode = new Metode();
// $metode->selectByParams(array("UPPER(A.NAMA)" => "KLARIFIKASI TEKNIS & NEGOSIASI"), -1, -1, $reqId);
$metode->selectByParams(array("UPPER(A.NAMA)" => "PEMBUKTIAN & NEGOSIASI"), -1, -1, $reqId);
$metode->firstRow();
?>
<script type="text/javascript">
$(document).ready(function() {

  $(function(){
    $('#ff').form({
      url:'paket_negoisasi_json/undanganauction',
      onSubmit:function(){
        $("#reqTanggalNegosiasi").val($("#reqTanggal").val());
        // return $(this).form('validate');
        var v=$(this).form('validate');
        if(v) showLoad();  // show the message box
        return v;
      },
      success:function(data){
        // alert(data);return false;
        // alert(data);
        $.messager.alert('Info', data, 'info');
        hideLoad();
        document.location.href = 'main/index/paket_lelang_tambah_negosiasi_undangan_reverse/?reqId=<?=$reqId?>';
      }
    });

  });

});
</script>

<div class="row">
  <div class="col-md-12 col-sm-12">
    <div class="card">
      <div class="card-header card-head-inverse bg-primary">
        <h4 class="card-title text-white">Setting Notifikasi</h4>
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
          <div class="row">
            <div class="form-group col-md-12 mb-2">
              <div class="col-md-12">
                <ul class="nav nav-tabs process-model more-icon-preocess" role="tablist"> 
                  <li role="presentation" class="active" style="width: 33% !important"><a href="main/index/paket_lelang_tambah_negosiasi_undangan_reverse/?reqId=<?=$reqId?>"><i class="fa fa-cogs" aria-hidden="true"></i>
                    <p>Setting Notifikasi Pembuktian</p>
                    </a>
                  </li>
                  <li role="presentation" style="width: 33% !important"><a href="main/index/klarifikasi_chat/?reqId=<?=$reqId?>"><i class="fa fa-check-circle" aria-hidden="true"></i>
                    <p>Pembuktian Dok. Penawaran</p>
                    </a>
                  </li>
                  <li role="presentation" style="width: 33% !important"><a href="main/index/paket_lelang_tambah_auction/?reqId=<?=$reqId?>"><i class="fa fa-flag" aria-hidden="true"></i>
                    <p>e-Reverse Auction</p>
                    </a>
                  </li>
                   
                </ul>
              </div> 
            </div>
          </div>
          <!-- <div class="table-responsive">  -->
            
            <div class="row">
              <div class="form-group col-md-12 mb-1">
                <label>Notifikasi untuk Pembuktian Dokumen :</label><br>
                <ul>
                <?php 
                $paket_rekanan = new PaketRekanan();
                $paket_rekanan->selectUrutPenawaran(array("A.PAKET_ID" => $reqId, "A.LULUS_PENDAFTARAN" => 1, "A.KIRIM_PENAWARAN" => 1, "A.LULUS_PENAWARAN" => 1), -1, -1, "", "", " ORDER BY NILAI_PENAWARAN ASC, KIRIM_PENAWARAN_TANGGAL ASC ");
                 $urut=1;
                 $totalRekanan = 0;
                while($paket_rekanan->nextRow())
                { 
                  echo '<li><span style="font-size: 16px; font-weight: bold">'.$paket_rekanan->getField("NAMA").'</span></li>';
                 $totalRekanan += $paket_rekanan->getField("DI_EMAIL");
                }?>
              </ul>
              </div>
            </div>
            <div class="row">
              <div class="form-group col-md-12 mb-1">
                <?php
                $arrJamAwal = explode(":", $metode->getField("JAM_AWAL"));
                $arrJamAkhir = explode(":", $metode->getField("JAM_AKHIR"));
                $arrTgl = explode(" ", $metode->getField("TANGGAL_AWAL"));
                $arrTgl2 = explode(" ", $metode->getField("TANGGAL_AKHIR"));
                $tglNegosiasiAwal =  getFormattedDate($arrTgl[0]).' '.$arrJamAwal[0].':'.$arrJamAwal[1];
                $tglNegosiasiAkhir =  getFormattedDate($arrTgl2[0]).' '.$arrJamAkhir[0].':'.$arrJamAkhir[1];
                $todayDate = date('Y-m-d H:i');
                echo 'Tanggal/Jam <b>'.$tglNegosiasiAwal.' s/d '.$tglNegosiasiAkhir.'</b><br>';
                $tutupUndangan = 0;

                $tglNegosiasiAwalFormat = $metode->getField("TANGGAL_AWAL");
                if ($todayDate < $tglNegosiasiAwalFormat)
                {
                  $tutupUndangan = 1;
                   echo '<div class="alert alert-danger" style="color:#fff">
                          <span style="color: #fff">
                            Tanggal Pembuktian belum mulai.
                          </span>
                         </div>
                        ';
                }

                if ($todayDate > $metode->getField("TANGGAL_AKHIR"))
                {
                  if ($bidding == "1") { $labelNego = 'e-Reverse Auction'; } else { $labelNego = "Negosiasi"; }
                  $tutupUndangan = 1;
                   echo '<div class="alert alert-danger" style="color:#fff">
                          <span style="color: #fff">
                            Tanggal Pembuktian sudah terlewat, silahkan ubah waktu Pembuktian, ubah jadwal <a href="'.base_url().'main/index/paket_lelang_tambah_jadwal/?reqId='.$reqId.'&back=1" style="color:#fff"> <i>klik disini</i></a>.
                          </span>
                         </div>
                        ';
                }

                 ?>
                <input type="hidden" name="reqTanggal" id="reqTanggal" class="form-control" value="<?=datetimeToPage($metode->getField("TANGGAL_AWAL"), "date")?>" required style="width: 200% !important; float: left; display:none"/>
                <?php $arrJamAwal = explode(":", $metode->getField("JAM_AWAL")); ?>
                <input name="reqJamMulai" class="form-control" style="width: 50px; display: inline;" type="hidden" id="reqJamMulai" value="<?=$arrJamAwal[0]?>" size="2" maxlength="2" onkeypress="return isNumberKey(event)"/>
                <input name="reqMenitMulai" class="form-control" style="width: 50px; display: inline;" type="hidden" id="reqMenitMulai" value="<?=$arrJamAwal[1]?>" size="2" maxlength="2" onkeypress="return isNumberKey(event)"/>
              </div>
            </div> 
            
            <div class="row">
              <div class="form-group col-md-12 mb-2">
                <?php 
                if ($totalRekanan > 0) { ?>
                <span class="badge badge-dark" style="padding: 5px 15px; font-size: .9em">Notifikasi sudah dikirim</span>
                <?php 
                } ?>
              </div>
            </div>

            <table class="table table-bordered table-hover" style="display: none">
              <?php
              $no = 0;
              $style="gelap";
              $totalNegosiasi = 0;
              // echo $rekanan_paket_penawaran->query; die;
              while($rekanan_paket_penawaran->nextRow())
              {
                  $displayElement = "";
                  if((int)$rekanan_paket_penawaran->getField("QUANTITY") == 0)
                      $displayElement = " style='display:none' ";

              ?>
                  <tr class="<?=$style?>">
                          <input type="hidden" name="reqQuantity[]" id="reqQuantity<?=$no?>" value="<?=$rekanan_paket_penawaran->getField("QUANTITY")?>">
                          <input type="hidden" name="reqPaketPenawaranId[]" id="reqPaketPenawaranId<?=$no?>" value="<?=$rekanan_paket_penawaran->getField("PAKET_PENAWARAN_ID")?>">

                      <td align="right" <?=$displayElement?>>
                        <?=numberToIna($rekanan_paket_penawaran->getField("SUMMARY"))?>
                      </td>
                      <td align="right" <?=$displayElement?>>
                          <?=numberToIna($rekanan_paket_penawaran->getField("SUMKOREKSI_".$arrPaketRekananId[$indexRekananPememenang]))?>
                        <?php
                        $totalSum += $rekanan_paket_penawaran->getField("SUMKOREKSI_".$arrPaketRekananId[$indexRekananPememenang]);
                       ?>
                      </td>
                      <?php
                      $arrSummary["SUMMARY"][$no] = $rekanan_paket_penawaran->getField("SUMMARY");
                      $arrSummary["SUM_".$arrPaketRekananId[$indexRekananPememenang]][$no] = $rekanan_paket_penawaran->getField("SUMKOREKSI_".$arrPaketRekananId[$indexRekananPememenang]);

                      /* AMBIL NILAI TERKECIL */
                      $arrPenawaran[]=0;
                      for($i=0;$i<count($arrRekanan);$i++)
                      {
                          $arrPenawaran[$i] = coalesce($rekanan_paket_penawaran->getField("UPK_".$arrPaketRekananId[$i]), $rekanan_paket_penawaran->getField("OE"));

                      }

                          $paket_negosiasi = new PaketNegoisasi();
                          $penawaranTerkecil = min($arrPenawaran);

                          $jumlahTerkecil =  round($penawaranTerkecil * toNumber($rekanan_paket_penawaran->getField("QUANTITY")), 2);

                          $penawaranNegosiasi = $paket_negosiasi->getUnitPrice(array("A.PAKET_PENAWARAN_ID" => $rekanan_paket_penawaran->getField("PAKET_PENAWARAN_ID")));
                          if($penawaranNegosiasi == "")
                          {
                              $penawaranNegosiasi = $penawaranTerkecil;
                          }
                          $jumlahNegosiasi =  round($penawaranNegosiasi * toNumber($rekanan_paket_penawaran->getField("QUANTITY")), 2);

                          $penawaranNegosiasi2 = $rekanan_paket_penawaran->getField("SUMKOREKSI_".$arrPaketRekananId[$indexRekananPememenang]); 
                      ?>
                      <!-- <td align="right" <?=$displayElement?>> -->
                          <input type="hidden" name="reqUnitPriceNegosiasi[]" id="reqUnitPriceNegosiasi<?=$no?>"
                          value="<?=numberToIna($penawaranNegosiasi2)?>"
                          OnFocus="FormatAngka('reqUnitPriceNegosiasi<?=$no?>')"
                          OnKeyUp="FormatUang('reqUnitPriceNegosiasi<?=$no?>'); summary();"
                          OnBlur="FormatUang('reqUnitPriceNegosiasi<?=$no?>')"  class="form-control"
                                  <?php  if($submitNegosiasi == true) { ?>
                                      style="text-align:right; width: 50%"
                                  <?php
                                  }
                                   else
                                  { ?>
                                      style="text-align:right;background-color:#EDEDED; width: 50%"
                          readonly <?php } ?> class="span1" >
                          <!-- </td>    -->
                      <td align="right" <?=$displayElement?>>
                          <input type="text" name="reqJumlahNegosiasi[]" class="form-control" id="reqJumlahNegosiasi<?=$no?>" value="<?=numberToIna($penawaranNegosiasi2)?>" style="text-align:right;background-color:#EDEDED; width: 50%" readonly class="span1">
                      </td>
                  </tr>
              <?php
                  $totalTerkecil += $jumlahTerkecil;
                  $totalNegosiasi += $jumlahNegosiasi;
                  unset($arrPenawaran);
                  $no++;
                  if($style == "gelap")
                      $style = "terang";
                  else
                      $style = "gelap";
              }
              ?>
              <tr colspan="100" style="display:none">
                  <td>
                      <textarea name="reqRekananIdArray"><?php print_r(serialize($arrPaketRekananId)); ?></textarea>
                  </td>
              </tr>
          </table>

          <!-- </div> -->
          <div class="form-actions">
            <input type="hidden" id="reqTanggalNegosiasi" name="reqTanggalNegosiasi" value="<?=datetimeToPage($metode->getField("TANGGAL_AWAL"), "date")?>" />
            <input type="hidden" name="reqId" value="<?=$reqId?>" />
            <input type="hidden" name="reqPaketRekananId" value="<?=$paket_rekanan_satuan->getField("PAKET_REKANAN_ID")?>" />
            <input type="hidden" name="reqPaketTahapId" value="<?=$metode->getField("PAKET_TAHAP_ID")?>" />
            <input type="hidden" name="reqUrut" value="<?=$metode->getField("URUT")?>" />
            <input type="hidden" name="submitSimpan" value="Simpan" />
            <a href="main/index/paket_detil/?eid=<?=$reqId?>&key=<?=$reqUUID?>" class="<?= CLASS_BTN_DANGER ?> mr-1"><?= BTN_KEMBALI ?></a>
            <?php
            // if ($tutupUndangan == 0) {  ?>
            <button type="submit" class="<?= CLASS_BTN_PRIMARY ?>"><i class="fa fa-send"></i> Kirim Notifikasi</button>
            <?php
            //} ?>
          </div>
        </div>
      </div>
      </form>

    </div>
  </div>
</div>
