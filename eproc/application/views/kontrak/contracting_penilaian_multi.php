<?php
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 * 
 */

$this->libsession->cekSession();

$this->load->library("kauth");  $userLogin = new kauth();
$this->load->library("paketinfo"); $paketInfo = new paketinfo();
$this->LEGAL =  $this->kauth->getInstance()->getIdentity()->LEGAL;

include_once("functions/string.func.php");
include_once("functions/date.func.php");
include_once("functions/default.func.php");
include_once("functions/penilaian.func.php");

$reqId = httpFilterRequest("reqId"); // contractingrekananid
$getTahun = $this->session->userdata('setTahunKontrak'); // tahun session

$this->load->library("paketinfo"); $paketInfo = new paketinfo();
$this->load->model("Contracting");
$this->load->model("Contractingrekanan");
$this->load->model("PaketRekanan");
$this->load->model("PaketPenilaian");
$this->load->model("Rekanan");
$this->load->model("Paketpemenang");

$contracting = new Contracting();
$spkpks = new Contractingrekanan();
$getpaket_pemenang = new Paketpemenang();
$getpaket_pemenang2 = new Paketpemenang();

$contracting->selectByParams(array("A.CONTRACTINGREKANANID" => $reqId));
$contracting->firstRow();

$reqJenisKontrak = $contracting->getField('JENIS_KONTRAK') ?: '-';
$reqRekananId = str_replace(array("{","}"),"",$contracting->getField('PEMENANG')) ?: '-';
$reqPaketId = $contracting->getField('PAKET_ID') ?: '-';

$PNG_TEMP_DIR = 'uploads/';

/* create objects */
$rekanan = new Rekanan();
$paketpenilaian = new PaketPenilaian();
$paketpenilaianChild = new PaketPenilaian();
$paketpenilaianChildCount = new PaketPenilaian();
$cekPenilaian = new PaketPenilaian();
$cekPenilaianTotal = new PaketPenilaian();
$paketpenilaianrekap = new PaketPenilaian();

$paketInfo->getPaket($reqPaketId);
$reqNama = $paketInfo->nama;
$bidding = $paketInfo->bidding;
$reqMultiPemenang = $paketInfo->multi_pemenang;

$rekanan->selectByParams(array("A.REKANAN_ID" => $reqRekananId), -1, -1, '');
$rekanan->firstRow();

// $paketpenilaianrekap->hasilNilai($reqPaketId,$reqRekananId);
$paketpenilaianrekap->getHasil($reqId,$reqRekananId);

$cekPenilaianTotal->selectPenilaian(array("PAKET_ID" => $reqPaketId, "CONTRACTINGREKANANID" => $reqId), "-1", "-1", " AND REKANAN_ID IN (".$reqRekananId.")");
$cekPenilaianTotal->firstRow();
$reqTemplate = $cekPenilaianTotal->getField("TEMPLATE");

if ($cekPenilaianTotal->countRow() > 0) {
  $paketpenilaian->selectParent(array("TEMPLATE" => $reqTemplate), -1, -1, '');
  $totalPenilaian = $paketpenilaian->countRow();
} 


$spkpks->selectViewPKSSPK(array("A.CONTRACTINGREKANANID" => $reqId));
$spkpks->firstRow();

$reqJenisKontrak = $contracting->getField('JENIS_KONTRAK') ?: '-';
$reqCode = $spkpks->getField('CR_CODE') ?: '-';
$reqJnsKontrakStr = $spkpks->getField('JNS_KONTRAK_STR') ?: ''; 
$reqContractingStatusKontrakId = $spkpks->getField('CONTRACTINGSTATUSKONTRAKID') ?: '-';

if ($reqMultiPemenang == '0') {
  $getpaket_pemenang->selectByParams(array("PAKET_ID" => $reqPaketId, "PERINGKAT" => '1'), -1, -1); 
  $getpaket_pemenang2->selectByParams(array("PAKET_ID" => $reqPaketId, "PERINGKAT" => '1'), -1, -1);
} else {
  $getpaket_pemenang->selectByParams(array("PAKET_ID" => $reqPaketId), -1, -1);
  $getpaket_pemenang2->selectByParams(array("PAKET_ID" => $reqPaketId), -1, -1);
}

  $totalPemenang = $getpaket_pemenang->countRow(); 
// }

?>

<link rel="stylesheet" type="text/css" href="<?=base_url() ?>assets/new/vendors/css/ui/jquery-ui.min.css">
<link rel="stylesheet" type="text/css" href="<?=base_url() ?>assets/new/css/plugins/ui/jqueryui.css">
<script src="<?=base_url()?>assets/new/js/scripts/ui/jquery-ui/navigations.js"></script>
<style type="text/css">
  small { font-weight: bold; font-size: 11.5px }
  tr.backcolornew {
    background: #b11016 !important;
    color: #fff;
  }
</style>

<?= $this->libchat->kontrak($reqId); ?>
<div class="row">
  <div class="col-md-3 col-sm-3">
    <div class="jqueryui-ele-container">
      <?= $this->libkontrak->getMenu($reqId); ?>
    </div>
  </div>
  <div class="col-md-9 col-sm-9">
    <div class="card">
      <div class="card-content collapse show border-info border-darken-2">
        <div class="card-body">
          <h4 class="mb-2">Penilaian Kinerja</h4>

          <div class="form-actions"> 

              <div class="form-actions table-responsive">
                <table class="table table-bordered table-hover">
                  <tbody>
                    <tr>
                      <th width="2%">No</th>
                      <th>No. <?= $reqJnsKontrakStr ?></th>
                      <th>Penyedia</th>
                      <th width="13%">Aksi</th>
                    </tr>
                    <?php 
                    $no = 1; 
                    while($getpaket_pemenang->nextRow())
                    { 
                      $contractingrekananSPPBJ = new Contractingrekanan();
                      $contractingrekananSPPBJ->selectViewPKSSPK(array("A.PAKET_ID" => $reqPaketId, "A.REKANAN_ID" => $getpaket_pemenang->getField("REKANAN_ID")));
                      $contractingrekananSPPBJ->firstRow(); 
                      ?>
                      <tr>
                        <td width="2%"><?= $no ?></td>
                        <td>
                          <?= $contractingrekananSPPBJ->getField("CR_LEGAL_NOMOR_PKS") ?: '-'; ?>
                        </td>
                        <td><?= $getpaket_pemenang->getField("NAMA"); ?>
                        </td>
                        <td>
                          <?php 
                          $cekPenilaianTotal = new PaketPenilaian();
                          $cekPenilaianTotal->selectPenilaian(array("PAKET_ID" => $reqPaketId, "CONTRACTINGREKANANID" => $reqId), "-1", "-1", " AND REKANAN_ID IN (".$getpaket_pemenang->getField("REKANAN_ID").")");
                          if ($cekPenilaianTotal->countRow() > 0) { ?>
                            <a href="kontrak/index/contracting_penilaian_multi_tambah/?reqId=<?= $reqId ?>&reqRekananId=<?= $getpaket_pemenang->getField("REKANAN_ID") ?>&reqTemplate=<?= $reqTemplate ?>" class="badge badge-primary" style="margin-bottom: 1%;"> <i class="fa fa-pencil"></i></a>
                            <a href="main/loadUrl/report/paket_penilaian_multi_pdf/?reqId=<?=$reqId?>&pemenang=<?=$getpaket_pemenang->getField("REKANAN_ID")?>&reqTemplate=<?= $reqTemplate ?>" target="_blank" class="badge badge-info" style="margin-bottom: 1%;"><i class="fa fa-print"></i></a>
                          <?php 
                          } else {
                            if ($this->LEGAL != '1' && $this->USER_TYPE_ID != '20') {
                              echo '<a onclick="openAdd(\'main/loadUrl/notif/template-penilaian?reqId='.$reqId.'&reqRekananId='.$getpaket_pemenang->getField("REKANAN_ID").'&multi=1\')" class="badge badge-danger text-white"> <i class="fa fa-gavel"></i> Nilai </a>';
                            }
                          } ?> 

                        </td>
                      </tr>
                      <!--   -->
                    <?php 
                    $no++;
                    } ?>
                  </tbody>
                </table>
              </div> 
 
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
