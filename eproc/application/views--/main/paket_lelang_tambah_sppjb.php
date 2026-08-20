<?php
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 * 
 */
 // if($this->USER_TYPE_ID == "")
//     redirect("main");

$this->load->library("kauth");  $userLogin = new kauth(); 
$this->load->library("paketinfo"); $paketInfo = new paketinfo();
$this->load->model("Sppjb");
$this->load->model("Paketpemenang");
$this->load->model("RekananPengurus");
$this->load->library("FileHandler");
include_once("functions/string.func.php");
include_once("functions/date.func.php");
include_once("functions/default.func.php");

$reqId = $this->input->get("reqId");

$sppjb = new Sppjb();
$file = new FileHandler();
$getpaket_pemenang = new Paketpemenang();
$countpaket_pemenang = new Paketpemenang();

$getpaket_pemenang->selectByParams(array("PAKET_ID" => $reqId), -1, -1);
$countpaket_pemenang = $countpaket_pemenang->getCountByParams(array("A.PAKET_ID" => $reqId));

?>  

<div class="row"> 
  <div class="col-md-12 col-sm-12">
    <div class="card">
      <div class="card-header card-head-inverse bg-primary">
        <h4 class="card-title text-white">SPPBJ</h4>
        <a class="heading-elements-toggle"><i class="fa fa-ellipsis-v font-medium-3"></i></a> 
        <div class="heading-elements">
            <ul class="list-inline mb-0">
              <li><a data-action="expand"><i class="ft-maximize"></i></a></li>
              <!-- <li><a data-action="close"><i class="ft-x"></i></a></li> -->
            </ul>
        </div>
      </div>

      <div class="card mb-1 border-blue border-darken-1">
        <div class="card-content">
          <div class="p-1"> 
            <div class="table-responsive">
              <table class="border-double table mb-0">
                <thead>
                  <th>No</th>
                  <th>Pemenang</th>
                  <th>Tanggal Penetapan</th>
                  <th>Keterangan</th>
                  <th>SPPBJ</th>
                </thead>
                <tbody>
                  <?php
                  if ($countpaket_pemenang > 0) {
                    $no=1;
                    while($getpaket_pemenang->nextRow())
                    {
                      echo '
                      <tr>
                       <td style="width:5%; text-align:center">'.$no.'</td>
                       <td>'.$getpaket_pemenang->getField("NAMA").'</td>
                       <td>'.getFormattedDate($getpaket_pemenang->getField("TANGGAL_PENETAPAN")).'</td>
                       <td>'.$getpaket_pemenang->getField("KETERANGAN").'</td>
                       <td style="width:5%; text-align:center"><a href="main/index/paket_lelang_tambah_sppjb_tambah/?reqId='.$reqId.'&pemenang='.$getpaket_pemenang->getField("PAKET_PEMENANG_ID").'" class="btn-aksi")><i class="fa fa-legal"></i></a></td>
                      </tr>';
                      $no++;
                    }
                  } else {
                    echo '<tr><td colspan="4">Pemenang belum di terapkan</td>';
                  }
                  ?> 
                </tbody>
              </table>
              <br><hr>
              <div class="form-actions">
                <a href="main/index/paket_detil/?reqId=<?=$reqId?>" class="btn btn-danger text-white"> <i class="fa fa-arrow-left"></i> Kembali </a> 
              </div> 
            </div>
          </div>
        </div>
      </div> 

    </div>
  </div> 
</div>      