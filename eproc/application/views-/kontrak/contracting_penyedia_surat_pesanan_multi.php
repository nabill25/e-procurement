<?php
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 * 
 */

$reqId = httpFilterRequest("reqId");
$reqProses = httpFilterRequest("reqProses");
$getTahun = $this->session->userdata('setTahunKontrak');

$this->libsession->cekSessionKontrak($reqId);

$this->load->library("kauth");  $userLogin = new kauth();

$this->LEGAL =  $this->kauth->getInstance()->getIdentity()->LEGAL;

include_once("functions/string.func.php");
include_once("functions/date.func.php");
include_once("functions/default.func.php");

$this->load->library("paketinfo"); $paketInfo = new paketinfo();
$this->load->model(array("Paketpemenang","Contractingrekanan"));


$getpaket_pemenang = new Paketpemenang();
$spkpks = new Contractingrekanan();

$spkpks->selectViewPKSSPK(array("A.CONTRACTINGREKANANID" => $reqId, "A.REKANAN_ID" => $this->ID));
$spkpks->firstRow();

$reqCode = $spkpks->getField('CR_CODE') ?: '-';
$reqJnsKontrakStr = $spkpks->getField('JNS_KONTRAK_STR') ?: '';
$reqRekananProses1Id = $spkpks->getField('CONTRACTINGREKANANPROSES1ID') ?: '-';
$reqPaketId = $spkpks->getField('PAKET_ID') ?: '-';

$paketInfo->getPaket($reqPaketId);
$bidding = $paketInfo->bidding;
$reqMultiPemenang = $paketInfo->multi_pemenang;

// if ($reqMultiPemenang == '0') {
//   exit;
// } else {
  $getpaket_pemenang->selectByParams(array("PAKET_ID" => $reqPaketId), -1, -1);

// }
?>

<link rel="stylesheet" type="text/css" href="<?=base_url() ?>assets/new/vendors/css/ui/jquery-ui.min.css">
<link rel="stylesheet" type="text/css" href="<?=base_url() ?>assets/new/css/plugins/ui/jqueryui.css">
<script src="<?=base_url()?>assets/new/js/scripts/ui/jquery-ui/navigations.js"></script>
<style type="text/css">
  small { font-weight: bold; font-size: 11.5px }
  tr.backcolornew {
    background: #cf252d !important;
    color: #fff;
  }
</style>

<script type="text/javascript">
</script>

<?= $this->libchat->kontrakPenyedia($reqId); ?>
<div class="row">
  <div class="col-md-3 col-sm-3">
    <div class="jqueryui-ele-container">
      <?= $this->libkontrak->getMenuPenyedia($reqId); ?>
    </div>
  </div>
  <div class="col-md-9 col-sm-9">
    <div class="card">
      <div class="card-content collapse show border-info border-darken-2">
        <div class="card-body">
          <h4 class="mb-2">Surat Pesanan</h4>
          <div class="form-actions">

                <div class="form-actions mt-3 table-responsive">
                  <table id="dokumenFileIdTable" class="border-double table mb-0 table-bordered mb-2" style="width: 100%">
                    <?= $this->libkontrak->getTableFilePenyedia($reqId," AND FILE_PUBLISH_PENYEDIA = '1' AND A.FILE_JENIS = 'Surat Pesanan' AND (A.REKANAN_ID = $this->ID OR A.CREATED_BY =$this->USER_LOGIN_ID )") ?>
                  </table>
                </div>

            <?php
              $no = 1;

              while($getpaket_pemenang->nextRow())
              {
                $contractingrekananSPPBJ = new Contractingrekanan();
                $contractingrekananSPPBJ->selectViewPKSSPK(array("A.PAKET_ID" => $reqPaketId, "A.REKANAN_ID" => $getpaket_pemenang->getField("REKANAN_ID")));
                $contractingrekananSPPBJ->firstRow();
                $nilaiKontrak += $contractingrekananSPPBJ->getField("CR_NILAI_KONTRAK");
                ?>
              <?php
              $no++;
              } ?>

            <div class="form-actions mt-3 table-responsive">
            <!-- <h4>List Surat Pesanan</h4>  -->
            <table id="deliveriable" class="border-double table mb-0 table-bordered mb-2 table-responsive">
              <tr class="backcolornew">
                <th width="5%">Nomor</th>
                <th width="75%">Penyedia</th>
                <th width="5%" style="text-align:center;">Daftar <br>Barang Jasa</th>
                <th width="25%" style="text-align: center;">Total</th>
                <th width="20%" class="text-center">Aksi</th>
              </tr>
              <?php
              $this->load->model("Contractingsuratpesanan");
              $datasuratpesanan = new Contractingsuratpesanan();
              $datasuratpesanan->selectByParamsSuratPesanan(array("CONTRACTINGREKANANID"=>$reqId, "B.REKANAN_ID" => $this->ID));
              if ($datasuratpesanan->countRow() > 0) {
                $no=1;
                while($datasuratpesanan->nextRow()) {
                  $sumTotal += $datasuratpesanan->getField('TOTAL');

                  $datasuratpesananmaterial = new Contractingsuratpesanan();
                  $datasuratpesananmaterial->selectByParamsSuratPesananMaterial(array("SURATPESANANID"=>$datasuratpesanan->getField('SURATPESANANID')));
                  $totalMaterial = $datasuratpesananmaterial->countRow();
                  $statusTerima = 0;
                  while($datasuratpesananmaterial->nextRow()) {
                    if ($datasuratpesananmaterial->getField('STATUS_TERIMA') == '1') {
                      $statusTerima += 1;
                    }
                  }

                  if ($totalMaterial == $statusTerima) {
                    $totalBarangJasa = $statusTerima.' <span class="fa fa-check"></span>';
                  } else {
                    $totalBarangJasa = $statusTerima.' / '.$totalMaterial;
                  }


                ?>
                <tr>
                  <td><?= $datasuratpesanan->getField('NOMOR_SURAT') ?></td>
                  <td><?= $datasuratpesanan->getField('REKANAN') ?></td>
                  <td style="text-align:center"><?= $totalBarangJasa ?></td>
                  <td style="text-align:right;"><?= currencyToPage($datasuratpesanan->getField('TOTAL')) ?></td>
                  <td>
                    <a onclick="openAdd('main/loadUrlKontrak/kontrak/contracting_update_surat_pesanan_material?reqSuratPesananId=<?= $datasuratpesanan->getField('SURATPESANANID')?>&reqview=1')"> <i class="fa fa-eye"></i></a>
                  </td>
                </tr>
                <?php
                $no++;
                }
              } else { echo '<tr><td colspan="4">. : : Tidak ada data : : .</td></tr>';} ?>
              <tfoot>
                <tr>
                  <td colspan="3" style="text-align: right;">
                  TOTAL
                    <?php
                    if ($sumTotal > $nilaiKontrak) {
                       echo '<br><span class="badge badge-danger">Total Surat Pesanan diatas Nilai Kontrak</span>';
                       $colorSumTotal = '<b style="color:red">'.currencyToPage($sumTotal).'</b';
                     } else {
                       $colorSumTotal = '<b style="color:black">'.currencyToPage($sumTotal).'</b';
                     } ?>
                  </td>
                  <td style="text-align:right;">
                    <?= $colorSumTotal; ?>

                  </td>
                </tr>
              </tfoot>
            </table>
          </div>

            <!-- <a href="kontrak/index/contracting_penyedia_detail?reqId=<?= $reqId; ?>" class="<?= CLASS_BTN_DANGER ?> mr-1"> <?= BTN_KEMBALI ?></a>  -->

          </div>
        </div>
      </div>
    </div>
  </div>
</div>
