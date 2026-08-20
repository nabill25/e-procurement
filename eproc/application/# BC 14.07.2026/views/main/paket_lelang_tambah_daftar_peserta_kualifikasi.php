<?php
$reqId = $this->input->get("reqId");
$this->libsession->cekSession($reqId);   

$this->load->library("kauth");  $userLogin = new kauth(); 
$this->load->model("PaketRekanan");
$this->load->library("paketinfo"); $paketInfo = new paketinfo();
include_once("functions/string.func.php");
include_once("functions/date.func.php");
include_once("functions/default.func.php");
$this->load->library("KMail");

$paket_rekanan = new PaketRekanan();
$paket_rekanan_total = new PaketRekanan();

$reqMode = $this->input->post("reqMode");
$submitSimpan = $this->input->post("submitSimpan");

$reqLulusPendaftaran = isset($_POST["reqLulusPendaftaran"])?$_POST["reqLulusPendaftaran"]:'';
$reqLulusKeterangan = isset($_POST["reqLulusKeterangan"])?$_POST["reqLulusKeterangan"]:'';
$reqPaketRekananId = isset($_POST["reqPaketRekananId"])?$_POST["reqPaketRekananId"]:'';
$reqPaketRekananIdUser = $this->input->post("reqPaketRekananIdUser");
$reqLulusPendaftaranUser = $this->input->post("reqLulusPendaftaranUser");


$paket_rekanan->selectByParams(array("PAKET_ID" => $reqId, 'LULUS_KUALIFIKASI_PRA' => '1'));
$totalPaket = $paket_rekanan_total->getCountByParams(array("PAKET_ID" => $reqId));
$paketInfo->getPaket($reqId);
//echo $paket_rekanan->query;exit;
?> 
<script type="text/javascript">

</script>

<div class="row"> 
  <div class="col-md-12 col-sm-12">
    <div class="card">
      <div class="card-header card-head-inverse bg-primary">
        <h4 class="card-title text-white">Daftar Peserta <?= $paketInfo->metode_lelang_nama ?></h4>
        <a class="heading-elements-toggle"><i class="fa fa-ellipsis-v font-medium-3"></i></a> 
        <div class="heading-elements">
            <ul class="list-inline mb-0">
              <li><a data-action="expand"><i class="ft-maximize"></i></a></li>
              <!-- <li><a data-action="close"><i class="ft-x"></i></a></li> -->
            </ul>
        </div>
      </div>

      <form id="ff" class="easyui-form form-horizontal" method="post" data-options="novalidate:true" enctype="multipart/form-data">
      <div class="card-content collapse show border-info border-darken-2">
        <div class="card-body area-datatable">

            <div class="table-responsive">
              <table class="table table-bordered mb-0"> 
                <tbody>
                  <tr class="judul-kolom">
                    <th style="width: 3%">No.</th>
                    <th >Nama Peserta <?= $paketInfo->metode_lelang_nama ?></th>
                    <!-- <th>Syarat</th> -->
                    <?php 
                    if ($paketInfo->metode_lelang_id == '2' || $paketInfo->metode_lelang_id == '3' || $paketInfo->metode_lelang_id == '5' || $paketInfo->metode_lelang_id == '6' || $paketInfo->metode_lelang_id == '8' ) { ?>
                    <th style="width: 20%">Diundang</th>
                    <?php 
                    } ?>
                     <?php 
                    if ($paketInfo->metode_lelang_id == '1'|| $paketInfo->metode_lelang_id == '7' || $paketInfo->metode_lelang_id == '10' ) { ?>
                    <th style="width: 20%">Tanggal Daftar</th>
                    <?php 
                    } ?>
                  </tr>
                   <?php
                   if ($paket_rekanan->countRow() == 0) {
                     echo '<tr><td colspan="3" class="text-center">. : : Belum ada peserta : : .</td></tr>';
                   } else 
                   {
    								  $i=1;
    								  while($paket_rekanan->nextRow())
    								  {
    									  $disable = "";
    							  ?>
                    	<tr>
                        <td><?=$i?>.</td>
                        <td> 
                          <a title="#" onClick="openAdd('main/loadUrl/main/data_rekanan/?reqId=<?=$paket_rekanan->getField("REKANAN_ID")?>');">
                            <i class="fa fa-eye btn btn-info btn-sm text-white" style="padding: 2px 4px !important"></i>
                            <?= '<b>'.$paket_rekanan->getField("FULL_NAMA_REKANAN").'</b>'; ?></a>
                        </td> 
                        <?php 
                        if ($paketInfo->metode_lelang_id != '1' && $paketInfo->metode_lelang_id != '7' && $paketInfo->metode_lelang_id != '10' ) { ?>
                        <td> <?=getFormattedDate($paket_rekanan->getField("TANGGAL_UNDANG"))?> </td>
                        <?php 
                        } else { ?>
                        <td> <?=getFormattedDate($paket_rekanan->getField("TANGGAL_DAFTAR")).' <br> <small> '.$paket_rekanan->getField("JAM_DAFTAR").'</small>'?> 
                        </td>
                        <?php 
                        } ?> 
                      </tr>
                      <?php
                        $i++;
                      }
                    }
                    ?>
                </tbody>
              </table>   
            </div>

            <div class="form-actions"> 
              <a href="main/index/paket_detil/?eid=<?=$reqId?>&key=<?= $paketInfo->uuid ?>" class="<?= CLASS_BTN_DANGER ?> mr-1"> <?= BTN_KEMBALI ?> </a> 
              <?php 
              if ($totalPaket > 0) { ?>
              <!-- <a class="<?php // CLASS_BTN_INFO ?>" href="main/loadUrl/report/daftar_peserta_lelang_excel/?reqId=<?php // $reqId?>" target="_blank" ><?php // BTN_PRINT ?></a> -->
              <?php 
              } ?>
            </div> 
            
        </div>
      </div>
      </form>
      
    </div>
  </div> 
</div>  