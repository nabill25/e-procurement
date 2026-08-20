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

$this->load->model("Contracting");
$this->load->model("Contractingrekanan");
$this->load->model("PaketRekanan");
$this->load->model("PaketPenilaian");
$this->load->model("Rekanan");

$contracting = new Contracting();

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

$rekanan->selectByParams(array("A.REKANAN_ID" => $reqRekananId), -1, -1, '');
$rekanan->firstRow();

// $paketpenilaianrekap->hasilNilai($reqPaketId,$reqRekananId);
$paketpenilaianrekap->getHasil($reqId,$reqRekananId);

$cekPenilaianTotal->selectPenilaian(array("PAKET_ID" => $reqPaketId,"A.REKANAN_ID" => $reqRekananId, "CONTRACTINGREKANANID" => $reqId));
$cekPenilaianTotal->firstRow();
// $reqTemplate = $cekPenilaianTotal->getField("TEMPLATE");

// if ($cekPenilaianTotal->countRow() > 0) { 
  $paketpenilaian->selectParent(array(), -1, -1, '');
  $totalPenilaian = $paketpenilaian->countRow();
// } 

$spkpks = new Contractingrekanan();
$spkpks->selectViewPKSSPK(array("A.CONTRACTINGREKANANID" => $reqId));
$spkpks->firstRow();
$reqNama = $spkpks->getField('NAMA') ?: '-';
$reqPICKontrak = $spkpks->getField('PIC_KONTRAK') ?: '-';
$reqPICPengendali = $spkpks->getField('PIC_PENGENDALI') ?: '-';
$reqPICPenyelesaian = $spkpks->getField('PIC_PENYELESAIAN') ?: '-';
$reqPengguna = $spkpks->getField('PENGGUNA') ?: '-';
$reqPO = $spkpks->getField('CR_PO') ?: '-';
$reqJnsKontrakStr = $spkpks->getField('JNS_KONTRAK_STR') ?: ''; 

$legal = new Contractingrekanan();
$legal->selectViewLegal(array("A.CONTRACTINGREKANANID" => $reqId));
$legal->firstRow();
$reqLegalNomorPKS = $legal->getField('CR_LEGAL_NOMOR_PKS') ?: '-';

$nomor = $paketInfo->pr_group_number."/PENILAIAN.REKANAN/".getYear($paketInfo->tanggal);
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
  .process-model li {
    display: inline-block;
    width: 33%;
    text-align: center;
    float: none;
  }
</style>

<script type="text/javascript">
function approvalPenilaian(delele_link, id, stat)
  {
    if (stat == '1') {
      var messa = 'Setujui Penilaian ini?';
    } else {
      var messa = 'Batal setujui Penilaian ini ?';
    }

    $.messager.confirm('Konfirmasi',messa,function(r){
      if (r){
        var jqxhr = $.get( delele_link+'?reqId='+id+'&status='+stat, function(data) {
        })
        .done(function(data) {
          alertSuccess2(data);
          setTimeout(function() {
            document.location.reload();
          }, 2000);
        })
        .fail(function() {
          alertError2('Gagal diproses, silahkan coba kembali'); // gagal
        });
      }
    });
  }
</script>

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
              <?php
              // Cek Penilaian Approval 
              $this->load->model(array("Queryfree"));
              $countApproval = new Queryfree();
              $countApproval->selectByParams("SELECT APPROVAL_UNIT,APPROVAL_KASUBDIT,APPROVAL_PPK FROM PAKET_PENILAIAN_REKANAN 
                                              WHERE CONTRACTINGREKANANID = ".$reqId." GROUP BY APPROVAL_UNIT,APPROVAL_KASUBDIT,APPROVAL_PPK");
              $countApproval->firstRow();
              $apprUnit = $countApproval->getField("APPROVAL_UNIT"); 
              $apprKasubdit = $countApproval->getField("APPROVAL_KASUBDIT"); 
              $apprPPK = $countApproval->getField("APPROVAL_PPK"); 

              if ($cekPenilaianTotal->countRow() > 0 && $this->LEGAL != '1' && $this->USER_TYPE_ID != '20'
                  && ($reqPengguna == $this->USER_LOGIN_ID) || (($this->LEVEL_KONTRAK == '2' && $reqPICPengendali == $this->USER_LOGIN_ID) || ($this->LEVEL_KONTRAK == '3' && $reqPICPenyelesaian == $this->USER_LOGIN_ID))
                  )
              { ?>
              <a href="main/loadUrl/report/paket_penilaian_pdf/?reqId=<?=$reqId?>&pemenang=<?=$reqRekananId?>" target="_blank" class="<?= CLASS_BTN_INFO ?> mr-1" style="margin-bottom: 1%;"><i class="fa fa-print"></i> Cetak</a>

                <?php 
                if ($apprKasubdit != '1' && $apprPPK != '1') { ?>
                  <a href="kontrak/index/contracting_penilaian_tambah/?reqId=<?= $reqId ?>&reqRekananId=<?= $reqRekananId ?>" class="<?= CLASS_BTN_SUCCESS ?>" style="margin-bottom: 1%;"> <i class="fa fa-pencil"></i> Edit Penilaian</a>
              <?php
                }
              } ?>

              <?php 

              if ($cekPenilaianTotal->countRow() > 0 && $this->USER_TYPE_ID == '20' && $apprKasubdit != '1')
              { 
                ?>
                  <a href="kontrak/index/contracting_penilaian_tambah/?reqId=<?= $reqId ?>&reqRekananId=<?= $reqRekananId ?>" class="<?= CLASS_BTN_SUCCESS ?>" style="margin-bottom: 1%;"> <i class="fa fa-pencil"></i> Edit Penilaian</a>
              <?php 
              }

              if ($cekPenilaianTotal->countRow() > 0 && $this->USER_TYPE_ID == '28' && $apprPPK != '1')
              { 
                ?>
                  <a href="kontrak/index/contracting_penilaian_tambah/?reqId=<?= $reqId ?>&reqRekananId=<?= $reqRekananId ?>" class="<?= CLASS_BTN_SUCCESS ?>" style="margin-bottom: 1%;"> <i class="fa fa-pencil"></i> Edit Penilaian</a>
              <?php 
              } ?>

              <?php 
              if ($cekPenilaianTotal->countRow() > 0 )
              {?>
                <div class="col-md-12">
                  <ul class="nav nav-tabs process-model more-icon-preocess" role="tablist">

                    <?php  
                      if ($apprUnit == '1') {
                        echo '<li role="presentation" class="active"><i class="fa fa-check" aria-hidden="true"></i><p>PIC Unit Setuju</p></li>';
                      } else {
                        echo '<li role="presentation"><i class="fa fa-close" aria-hidden="true"></i><p>Approval PIC Unit</p></li>';
                      }
                    ?>

                    <?php  
                    if ($this->USER_TYPE_ID == '20') { 
                      if ($apprKasubdit == '1') {
                        echo '<li role="presentation" class="active"><i class="fa fa-check" aria-hidden="true"></i><p>Kasubdit Setuju</p></li>';
                      } else {
                        echo '<li role="presentation" style="cursor:pointer" onClick="approvalPenilaian(\'contracting_json/approvalKasubdit\', '.$reqId.',\'1\')"><i class="fa fa-close" aria-hidden="true"></i><p>Approval Kasubdit <br><span class="badge badge-primary">Setujui ? </span></p></li>';
                      }
                    ?>
                      
                    <?php 
                    } else {
                      if ($apprKasubdit == '1') {
                        echo '<li role="presentation" class="active"><i class="fa fa-check" aria-hidden="true"></i><p>Kasubdit Setuju</p></li>';
                      } else {
                        echo '<li role="presentation"><i class="fa fa-close" aria-hidden="true"></i><p>Approval Kasubdit</p></li>';
                      }
                    } ?>

                    <?php  
                    if ($this->USER_TYPE_ID == '28') { 
                      if ($apprPPK == '1') {
                        echo '<li role="presentation" class="active"><i class="fa fa-check" aria-hidden="true"></i><p>PPK Setuju</p></li>';
                      } else {
                        echo '<li role="presentation" style="cursor:pointer" onClick="approvalPenilaian(\'contracting_json/approvalPPK\', '.$reqId.',\'1\')"><i class="fa fa-close" aria-hidden="true"></i><p>Approval PPK <br><span class="badge badge-primary">Setujui ? </span></p></li>';
                      }
                    ?>
                      
                    <?php 
                    } else {
                      if ($apprPPK == '1') {
                        echo '<li role="presentation" class="active"><i class="fa fa-check" aria-hidden="true"></i><p>PPK Setuju</p></li>';
                      } else {
                        echo '<li role="presentation"><i class="fa fa-close" aria-hidden="true"></i><p>Approval PPK</p></li>';
                      }
                    } ?>
                    
                  </ul>
                </div>
              <?php 
              } ?>

              <div class="form-actions">
                <table class="table table-bordered table-hover">
                  <tbody>
                    <tr>
                      <td width="25%" colspan="4">
                        <small>Paket Pengadaan</small> <br> <?= $reqNama ?>
                      </td> 
                    </tr>
                    <tr>
                      <td width="25%" colspan="2">
                        <small>Nomor PO</small> <br> <?= $reqPO ?>
                      </td>
                      <td width="25%" colspan="2">
                        <small>Nomor <?= $reqJnsKontrakStr.' '.SYSTEM_NAME_PT ?> </small> <br> <?= $reqLegalNomorPKS ?>
                      </td>
                    </tr>
                    <tr>
                      <td width="25%" colspan="2">
                        <small>Kode Penyedia</small> <br> <?= $rekanan->getField("KODE") ?>
                      </td>
                      <td width="25%" colspan="2">
                        <small>Nama Pemenang</small> <br> <?= $rekanan->getField("NAMA") ?>
                      </td>
                    </tr> 
                  </tbody>
                </table>
              </div>

          <?php
          if ($cekPenilaianTotal->countRow() > 0 )
          {
          ?>
              <div class="area-dokumen">


              <?php
              $nourut = 1;
                while($paketpenilaian->nextRow())
                {
                  $paketpenilaianChild->selectChild(array("PPT_PARENT_ID" => $paketpenilaian->getField("PPT_ID")), -1, -1, '');
                  $total = $paketpenilaianChildCount->getCountByParams(array("PPT_PARENT_ID" => $paketpenilaian->getField("PPT_ID")));
                 ?>
                <table class="table table-bordered">
                  <tr class="tr" style="background: #103A6C !important; color: #fff;">
                    <td colspan="7" class="td">
                    <?= '<b>'.$paketpenilaian->getField("KODE").'. '.$paketpenilaian->getField("NAMA").'</b>'?>
                    </td>
                  </tr>
                  <tr class="tr-bc">
                    <th class="td" align="center" valign="middle" width="7%">No.</th>
                    <th class="td" align="left" valign="middle" width="50%">Deskripsi Penilaian</th>
                    <th class="td" align="center" valign="middle">Sangat Buruk</th>
                    <th class="td" align="center" valign="middle">Buruk</th>
                    <th class="td" align="center" valign="middle">Cukup</th>
                    <th class="td" align="center" valign="middle">Baik</th>
                    <th class="td" align="center" valign="middle">Sangat Baik</th>
                  </tr>
                  <?php
                  $no     = 1;
                  $noChild  = 0;
                  $nilaiTotal.$nourut = 0;
                  while($paketpenilaianChild->nextRow())
                  {
                    $cekPenilaian->selectPenilaian(array("PAKET_ID" => $reqPaketId,"A.REKANAN_ID" => $reqRekananId, "PPT_ID" => $paketpenilaianChild->getField("PPT_ID"), "PPT_PARENT_ID" => $paketpenilaianChild->getField("PPT_PARENT_ID")), -1, -1, '');
                    $cekPenilaian->firstRow();
                    $nilai = $cekPenilaian->getField("NILAI");
                    $note  = $cekPenilaian->getField("NOTE");
                    $nilaiTotal.$nourut += $cekPenilaian->getField("NILAI");
                  ?>
                  <tr class="gelap">
                    <td class="td" valign="top"><strong><?=$no?></strong></td>
                    <td class="td" valign="top"><b><?=$paketpenilaianChild->getField("NAMA")?></b><br><?=$paketpenilaianChild->getField("NOTE")?></td>
                    <?php
                    switch ($nilai) {
                      case '1':
                        echo
                        '
                          <td class="td" align="center" valign="top">&#10004;</td>
                          <td class="td" align="center" valign="top"></td>
                          <td class="td" align="center" valign="top"></td>
                          <td class="td" align="center" valign="top"></td>
                          <td class="td" align="center" valign="top"></td>
                        ';
                        break;
                      case '2':
                        echo
                        '
                          <td class="td" align="center" valign="top"></td>
                          <td class="td" align="center" valign="top">&#10004;</td>
                          <td class="td" align="center" valign="top"></td>
                          <td class="td" align="center" valign="top"></td>
                          <td class="td" align="center" valign="top"></td>
                        ';
                        break;

                      case '3':
                        echo
                        '
                          <td class="td" align="center" valign="top"></td>
                          <td class="td" align="center" valign="top"></td>
                          <td class="td" align="center" valign="top">&#10004;</td>
                          <td class="td" align="center" valign="top"></td>
                          <td class="td" align="center" valign="top"></td>
                        ';
                        break;

                      case '4':
                        echo
                        '
                          <td class="td" align="center" valign="top"></td>
                          <td class="td" align="center" valign="top"></td>
                          <td class="td" align="center" valign="top"></td>
                          <td class="td" align="center" valign="top">&#10004;</td>
                          <td class="td" align="center" valign="top"></td>
                        ';
                        break;

                      case '5':
                        echo
                        '
                          <td class="td" align="center" valign="top"></td>
                          <td class="td" align="center" valign="top"></td>
                          <td class="td" align="center" valign="top"></td>
                          <td class="td" align="center" valign="top"></td>
                          <td class="td" align="center" valign="top">&#10004;</td>
                        ';
                        break;

                      default:
                        echo
                        ' <td class="td" align="center" valign="top"></td>
                          <td class="td" align="center" valign="top"></td>
                          <td class="td" align="center" valign="top"></td>
                          <td class="td" align="center" valign="top"></td>
                          <td class="td" align="center" valign="top"></td>
                        ';
                        break;
                    }
                    ?>

                    </tr>
                  <?php $no++; $noChild++;
                  } ?>
                  <tfoot>
                    <tr>
                      <td colspan="2" class="text-center"><b>TOTAL</b></td>
                      <td colspan="5" class="text-center"><b><?= $nilaiTotal.$nourut ?></b></td>
                    </tr>
                  </tfoot>
                  </table>
                  <!-- <div class="isi" style="margin:5px 0 20px 0; border:1px solid #000; padding: 20px 10px"> -->
                    <!-- Komentar <i>(Comments)</i> : <?=$note?> -->
                  <!-- </div> -->
                  <?php
                } ?>
              </div>

              <div class="isi">
                <h4>Hasil Penilaian :</h4>
              </div>
              <div class="area-dokumen">
                <table class="table table-bordered">
                  <tr class="tr" style="background:#103A6C !important; color:#fff">
                    <td class="td" align="center" valign="middle" width="7%">No.</td>
                    <td class="td" align="center" valign="middle" width="48%">Aspek Penilaian</td>
                    <td class="td" align="center" valign="middle" width="18%">Total Skor <br>(Dari Maks. 20)</td>
                    <td class="td" align="center" valign="middle" width="10%">Bobot (%)</td>
                    <td class="td" align="center" valign="middle" width="30%">Skor Tertimbang</td>
                  </tr>
                  <?php
                  $noHasil=1;
                  // echo "<pre>"; print_r($paketpenilaianrekap); die();
                  while ($paketpenilaianrekap->nextRow()) {
                    $totalNilai += $paketpenilaianrekap->getField("RATA2_SKOR");
                    $totalSkorUI += $paketpenilaianrekap->getField("SKOR_UI");
                    $totalPresentasi += $paketpenilaianrekap->getField("PRESENTASI");
                    $totalSkorTertimbangUI += $paketpenilaianrekap->getField("SKOR_TERTIMBANG_UI");
                   ?>
                  <tr>
                    <td class="td" align="center" valign="middle"><?=$noHasil?></td>
                    <td class="td" align="left" valign="middle"><?=$paketpenilaianrekap->getField("NAMA")?></td>
                    <td class="td" align="center" valign="middle"><?= round($paketpenilaianrekap->getField("SKOR_UI"),2) ?></td>
                    <td class="td" align="center" valign="middle"><?=$paketpenilaianrekap->getField("PRESENTASI")?>%</td>
                    <td class="td" align="center" valign="middle"><?= round($paketpenilaianrekap->getField("SKOR_TERTIMBANG_UI"),2) ?></td>
                  </tr>
                  <?php $noHasil++;
                    } ?>
                  <tr class="tr-bc">
                    <td class="td" colspan="3" align="center" valign="middle"><b>TOTAL SKOR AKHIR</b></td>
                    <td class="td" align="center" valign="middle"><?= $totalPresentasi; ?>%</td>
                    <td class="td" align="center" valign="middle"><?= $totalSkorTertimbangUI; ?></td>
                  </tr>
                  <tr class="tr-bc">
                    <td class="td" colspan="4" align="center" valign="middle"><b>NILAI AKHIR</b></td>
                    <td class="td" align="center" valign="middle">
                      <?= $totalSkorTertimbangUI * 5; ?> <br>
                      <?= setGrade($totalSkorTertimbangUI * 5); ?>
                    </td>
                  </tr>
                </table>
              </div>
          <?php
          } // end if
          else {
            if ($this->LEGAL != '1' && $this->USER_TYPE_ID != '20' && $this->USER_TYPE_ID != '28') {
              // cek tagihan musti selesai semua
             if($this->libkontrak->cekTagihanSelesai($reqId)) {
                echo '<a class="'.CLASS_BTN_SUCCESS.'" href="kontrak/index/contracting_penilaian_tambah?reqId='.$reqId.'&reqRekananId='.$reqRekananId.'"> <i class="fa fa-gavel"></i> Penilaian belum di isi, silahkan klik di sini untuk mengisi nilai ...! </a>';
             } else {
              $levelKontrak = $this->LEVEL_KONTRAK;
              if ($levelKontrak == '2') // Pengendali
              { 
                echo '<div class="col-md-12 alert alert-danger">Belum bisa melakukan penilaian karena Realisasi belum selesai</div>';
              } else if ($levelKontrak == '3') { // Penyelesaian
                echo '<div class="col-md-12 alert alert-danger">Belum bisa melakukan penilaian karena Tagihan belum selesai</div>';
              }
             }
            // echo '<a onclick="openAdd(\'main/loadUrl/notif/template-penilaian?reqId='.$reqId.'&reqRekananId='.$reqRekananId.'\')" class="'.CLASS_BTN_SUCCESS.'"> <i class="fa fa-gavel"></i> Penilaian belum di isi, silahkan klik di sini untuk mengisi nilai ...! </a>';
            }
          }

           ?>

              <!-- <div class="area-dokumen">
                <table>
                  <tr class="sub-judul1">
                    <td align="center" valign="middle" width="7%">No.</td>
                    <td align="left" valign="middle" width="73%">Keterangan</td>
                    <td align="center" valign="middle" width="10%">Nilai</td>
                    <td align="center" valign="middle" width="10%">Grade</td>
                  </tr>
                  <?php
                  // $noHasil=1;
                  // while ($paketpenilaianrekap->nextRow()) {
                   ?>
                  <tr class="gelap">
                    <td align="center" valign="middle"><?php //$noHasil?></td>
                    <td align="left" valign="middle"><?php //$paketpenilaianrekap->getField("NAMA")?></td>
                    <td align="center" valign="middle"><?php //$paketpenilaianrekap->getField("NILAI")?></td>
                    <td align="center" valign="middle"><?php //$paketpenilaianrekap->getField("GRADE")?></td>
                  </tr>
                  <?php //$noHasil++; } ?>
                </table>
              </div> -->
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
