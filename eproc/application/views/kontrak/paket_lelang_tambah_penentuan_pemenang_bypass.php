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
$this->load->model(array("PaketRekanan","Paket","PaketPanitia","Paketpemenang","RekananEvaluasiTeknisTawar","RekananEvaluasiAdminTawar","RekananEvaluasiHargaTawar","PaketNegoisasi","PaketTahap","PaketDokumen"));
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
  $paket_rekanan->selectByParams3(array("A.PAKET_ID" => $reqId), -1, -1, " AND LULUS_PENDAFTARAN = 1 AND LULUS_KUALIFIKASI = 1 AND LULUS_PENAWARAN = 1 AND A.REKANAN_ID NOT IN (SELECT REKANAN_ID FROM PAKET_PEMENANG WHERE PAKET_ID=$reqId )  ORDER BY A.NILAI_PENAWARAN ASC", $reqId);
} else { // jika Sistem Negosiasi nya Bidding
  $paket_rekanan->selectByParams4(array("A.PAKET_ID" => $reqId), -1, -1, " AND LULUS_PENDAFTARAN = 1 AND A.REKANAN_ID NOT IN (SELECT REKANAN_ID FROM PAKET_PEMENANG WHERE PAKET_ID=$reqId ) ORDER BY A.NILAI_PENAWARAN ASC", $reqId);
}
// echo $paket_rekanan->query; die;
  $getpaket_pemenang->selectByParams(array("PAKET_ID" => $reqId), -1, -1);
  $countpaket_pemenang = $countpaket_pemenang->getCountByParams(array("A.PAKET_ID" => $reqId));

?>
<script type="text/javascript">
$(function(){ 
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
        document.location.href = 'kontrak/index/paket_lelang_tambah_penentuan_pemenang_bypass/?reqId=<?=$reqId?>';
      }
    });

});  

function reloadMonitoring(){ location.reload(); }
</script>

<style type="text/css">
  tr > th { vertical-align: middle; text-align: center; }
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
                      <th>Penawaran</th>
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
                      
                      $rekanan_evaluasi_admin = new RekananEvaluasiAdminTawar();
                      $rekanan_evaluasi_admin->selectMemenuhiSyarat($reqId, $paket_rekanan->getField("PAKET_REKANAN_ID"));
                      $rekanan_evaluasi_admin->firstRow();

                      $rekanan_evaluasi_teknis = new RekananEvaluasiTeknisTawar();
                      $rekanan_evaluasi_teknis->selectMemenuhiSyarat($reqId, $paket_rekanan->getField("PAKET_REKANAN_ID"));
                      $rekanan_evaluasi_teknis->firstRow();

                      $rekanan_evaluasi_harga = new RekananEvaluasiHargaTawar();
                      $rekanan_evaluasi_harga->selectMemenuhiSyarat2($reqId, $paket_rekanan->getField("PAKET_REKANAN_ID"));
                      $rekanan_evaluasi_harga->firstRow();

                      if ($paket_rekanan->getField("PAKET_PENAWARAN_ID")) {
                        $paket_negosiasi->selectByParams(array("A.PAKET_PENAWARAN_ID" => $paket_rekanan->getField("PAKET_PENAWARAN_ID")));
                        
                        $paket_negosiasi->firstRow();
                        $penawaranNegosiasi =  $paket_negosiasi->getField("UNIT_PRICE");
                        $jumlahNegosiasi =  $paket_negosiasi->getField("TOTAL");
                        $setujui =  $paket_negosiasi->getField("SETUJUI");
                      }
                      
                    ?>
                    <tr>
                      <td><?=$no?></td>
                      <td><?=$paket_rekanan->getField("REKANAN")?></td>
                      <td class="text-right"> 
                        <a onClick="openAddFrame('main/loadUrl/kontrak/paket_lelang_tambah_penentuan_pemenang_hargapenawaran_bypass?reqPaketPenawaranId=<?= $paket_rekanan->getField("PAKET_PENAWARAN_ID") ?>&reqPaketRekananId=<?= $paket_rekanan->getField("PAKET_REKANAN_ID") ?>')" class="pull-left"><span class="fa fa-pencil"></span> 
                        </a>
                          
                        <?= numberToIna($paket_rekanan->getField("UNIT_PRICE")) ?> 
                      </td>
                      <?php
                      if ($bidding == '0' || $bidding == '') { // jika Sistem Negosiasi  ?>
                      <td class="text-right">
                        <a onClick="openAddFrame('main/loadUrl/kontrak/paket_lelang_tambah_penentuan_pemenang_hargapenawarannego_bypass?reqPaketPenawaranId=<?= $paket_rekanan->getField("PAKET_PENAWARAN_ID") ?>&reqPaketRekananId=<?= $paket_rekanan->getField("PAKET_REKANAN_ID") ?>')" class="pull-left"><span class="fa fa-pencil"></span> 
                        </a>

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
                        <td>
                          <?php 
                          echo numberToIna($paket_rekanan->getField("NILAI_PENAWARAN"));
                          ?>
                        </td>
                      <?php
                        } else {
                          echo '<td></td>';
                        }
                      } ?>
                      <td>
                        <?php 

                           echo '<input type="radio" value="'.$paket_rekanan->getField("REKANAN_ID").'" name="reqPemenang" style="cursor:pointer">';
                         ?>
                         <input type="number" name="reqPeringkat<?= $paket_rekanan->getField("REKANAN_ID") ?>" id="points" name="points" min="<?= $nominimal ?>" class="form-control ml-1" value="<?= $nourut ?>" style="display: inline; width: 72%">
                      </td> 
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
                  <a href="kontrak/index/paket_detil_bypass/?eid=<?=$reqId?>&key=<?=$reqUUID?>" class="<?= CLASS_BTN_DANGER ?> mr-1"> <?= BTN_KEMBALI ?></a> 

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
                </div>
              </div>
            </div>
          </div> 
        </div>
      </div>

    </div>
  </div>
</div>
