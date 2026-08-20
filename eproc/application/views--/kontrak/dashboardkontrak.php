<?php
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 * 
 */

$this->libsession->cekSession();
$this->load->model(array("Dashcontractingui","Contractingrekanan"));

if ($this->LEGAL == '1') // legal gak boleh akses ini
  redirect(base_url('kontrak/index/contracting_persiapan_legal?tahun=all'));

//kauth
if (!$this->kauth->getInstance()->hasIdentity())
{
}
$this->USER_LOGIN_ID =  $this->kauth->getInstance()->getIdentity()->USER_LOGIN_ID;
$this->USER_LOGIN =  $this->kauth->getInstance()->getIdentity()->USER_LOGIN;
$this->LEGAL =  $this->kauth->getInstance()->getIdentity()->LEGAL;

$count1 = new Dashcontractingui();
$count2 = new Dashcontractingui();
$count3 = new Dashcontractingui();
$count4 = new Dashcontractingui();

$getTahun = isset($_GET['tahun']) ? $_GET['tahun'] : date('Y');
// set session untuk set tahun yang di pilih pada dashboard kontrak
$this->session->set_userdata('setTahunKontrak',$getTahun);
// echo $this->session->userdata('setTahunKontrak');
if ($getTahun != 'all'){
  $tahun = 'Tahun '.$getTahun;
  $countPersiapan = $count1->getCountByParamsViewContracting(array("A.CONTRACTINGPROSESID|| IN " => "('1','2')")," AND TAHUN = '".$getTahun."'");
  $countPengendalian = $count2->getCountByParamsViewContracting(array("A.CONTRACTINGPROSESID|| IN " => "('3','4','5')")," AND TAHUN = '".$getTahun."'");
  $countPenyelesaian = $count3->getCountByParamsViewContracting(array("A.CONTRACTINGPROSESID|| IN " => "('3','4','5')")," AND TAHUN = '".$getTahun."'");
  $countSelesai = $count4->getCountByParamsViewContracting(array("A.CONTRACTINGPROSESID|| IN " => "('6')")," AND TAHUN = '".$getTahun."'");
} else {
  $tahun = ''; 
  
  $countPersiapan = $count1->getCountByParamsViewContracting(array("A.CONTRACTINGPROSESID|| IN " => "('1','2')"),"");
  $countPengendalian = $count2->getCountByParamsViewContracting(array("A.CONTRACTINGPROSESID|| IN " => "('3','4','5')"),"");
  $countPenyelesaian = $count3->getCountByParamsViewContracting(array("A.CONTRACTINGPROSESID|| IN " => "('3','4','5')"),"");
  $countSelesai = $count4->getCountByParamsViewContracting(array("A.CONTRACTINGPROSESID|| IN " => "('6')"),"");

}
?>

<link rel="stylesheet" type="text/css" href="lib/DataTables-1.10.7/media/css/jquery.dataTables.css">
<link rel="stylesheet" type="text/css" href="lib/DataTables-1.10.7/extensions/Responsive/css/dataTables.responsive.css">
<link rel="stylesheet" type="text/css" href="lib/DataTables-1.10.7/examples/resources/syntax/shCore.css">
<link rel="stylesheet" type="text/css" href="lib/DataTables-1.10.7/examples/resources/demo.css">
<script type="text/javascript" language="javascript" src="lib/DataTables-1.10.7/media/js/jquery.dataTables.js"></script>
<script type="text/javascript" language="javascript" src="lib/DataTables-1.10.7/extensions/Responsive/js/dataTables.responsive.js"></script>
<script type="text/javascript" language="javascript" src="lib/DataTables-1.10.7/examples/resources/syntax/shCore.js"></script>
<script type="text/javascript" language="javascript" class="init">
var oTable;
$(document).ready(function() {
           
  oTable = $('#example').dataTable({ bJQueryUI: true,"iDisplayLength": 10,
  /* UNTUK MENGHIDE KOLOM ID */
  "aoColumns": [
           {"bVisible": false},null,null,null,null,null,
           null,null,null,null,null, null,null,null
        ],
  "bSort":true,
  "bProcessing": true,
  "bServerSide": true,    
  "sAjaxSource": "contracting_rekanan_json/jsonWorkList",  
  "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],  
  columnDefs: [{ className: 'never', targets: [ 0,1,3,4,5,8,9] }]
  });

  new $.fn.dataTable.Responsive( oTable );
  var anSelectedData = '';
  var anSelectedId = '';
  var anSelectedDownload = '';
  var anSelectedPosition = '';

  $("#example tbody").click(function(event) {
      $(oTable.fnSettings().aoData).each(function (){
        $(this.nTr).removeClass('row_selected');
      });
      $(event.target.parentNode).addClass('row_selected');
      //
      var anSelected = fnGetSelected(oTable);
      anSelectedData = String(oTable.fnGetData(anSelected[0]));
      var element = anSelectedData.split(',');
      anSelectedId = element[0]; // paketId
      anSelectedKontrakId = element[1]; // kontrakId
  }); 
 
}); 

</script>

<style type="text/css">
.chart-content { padding: 5px; margin: 10px; }
.chart-legend{
  font-size: 0.8em;
  li {
    list-style: none;
    span {
      display: inline-block;
      width: 8px;
      height: 8px;
      margin-right: 5px;
    }
  }
}
.wfont, .ft-info, a .wfont, a .ft-info, .media-body { color: #fff !important; }
.border-right { border-right: 1px solid #dee2e6!important; }
.description-block { display: block; margin: 0px 0; text-align: center; }
</style>

<div class="row">
  <div class="form-group col-md-3">
    <!-- <label>Pilih Tahun</label> -->
    <select class="form-control" id="setyear" onChange="return window.location = $(this).val()">
      <?php
      $selected = '';
      $url = base_url('kontrak/index/dashboardkontrak?tahun=');
      $kurangdari = date('Y') - 5;
            echo '<option value="'.$url.'all">-- Pilih Tahun --</option>';
      for ($i= date('Y')+1; $i > $kurangdari   ; $i--) {
           // if ($i == date('Y') || $i == $getTahun) {
           if ($i == $getTahun) {
            $selected = 'selected';
           } else {
            $selected = '';
           }
            echo '<option value="'.$url.$i.'" '.$selected.'>'.$i.'</option>';
      }
      ?>
    </select>
  </div>
  <div class="form-group col-md-9 text-right">
    <a href="<?= base_url('dashboard_excel_json/laporan/'.$getTahun) ?>" class="<?= CLASS_BTN_PRIMARY ?> text-white" title="Lihat Kontrak"><span class="fa fa-file"></span> &nbsp; Download Laporan </a>

  </div>
</div>

<div class="row">
  <div class="col-md-12">
    <div class="form-group alert" style="border: 1px solid #dee2e6!important">
      <div class="float-left">
        <h5><b>Dashboard Kontrak
        <?php 
          if ($getTahun != 'all'){
            echo 'Tahun Anggaran '.$getTahun; 
          } else {
            echo 'Tahun Anggaran All'; 
          }
        ?> 
        </b></h5>
      </div>
      &nbsp;
    </div>
  </div>
</div>


<div class="row"> 

  <div class="col-md-2">
    <div class="card box-shadow-0 animated zoomIn wfont" data-appear="appear" data-animation="zoomIn" style="background-color: #4F6457;">
      <div class="card-content">
        <a onclick="openAdd('main/loadUrl/main/dashboard_kontrak_detail/?jenis=persiapan&tahun=<?=$getTahun?>&uki=<?=$unitkerja?>&uid=<?=$this->USER_LOGIN_ID?>')">
          <div class="card-body" style="">
              <div class="media">
                  <div class="media-body text-center">
                      <span style="margin-top: 15%; font-size: .9em;">Persiapan Kontrak</span>
                      <h2 class="wfont mt-2"><b><?php echo $countPersiapan ?> </b></h2>
                  </div> 
              </div>
              <div class="mt-1 text-center wfont">
                <?= '<small style="font-weight:bold">Paket</small>'; ?> 
              </div>
          </div>
        </a>
      </div>
    </div>
  </div>     

  <div class="col-md-2">
    <div class="card box-shadow-0 animated zoomIn wfont" data-appear="appear" data-animation="zoomIn" style="background-color: #D09683;">
      <div class="card-content">
        <a onclick="openAdd('main/loadUrl/main/dashboard_kontrak_detail/?jenis=pengendalian&tahun=<?=$getTahun?>&uki=<?=$unitkerja?>&uid=<?=$this->USER_LOGIN_ID?>')">
          <div class="card-body" style="">
              <div class="media">
                  <div class="media-body text-center">
                      <span style="margin-top: 15%; font-size: .9em;">Pengendalian Kontrak</span>
                      <h2 class="wfont mt-2"><b><?php echo $countPengendalian ?> </b></h2>
                  </div> 
              </div>
              <div class="mt-1 text-center wfont">
                <?= '<small style="font-weight:bold">Paket</small>'; ?> 
              </div>
          </div>
        </a>
      </div>
    </div>
  </div>   

  <div class="col-md-2">
    <div class="card box-shadow-0 animated zoomIn wfont" data-appear="appear" data-animation="zoomIn" style="background-color: #9BC01C;">
      <div class="card-content">
        <a onclick="openAdd('main/loadUrl/main/dashboard_kontrak_detail/?jenis=penyelesaian&tahun=<?=$getTahun?>&uki=<?=$unitkerja?>&uid=<?=$this->USER_LOGIN_ID?>')">
          <div class="card-body" style="">
              <div class="media">
                  <div class="media-body text-center">
                      <span style="margin-top: 15%; font-size: .9em;">Penyelesaian Kontrak</span>
                      <h2 class="wfont mt-2"><b><?php echo $countPenyelesaian ?> </b></h2>
                  </div> 
              </div>
              <div class="mt-1 text-center wfont">
                <?= '<small style="font-weight:bold">Paket</small>'; ?> 
              </div>
          </div>
        </a>
      </div>
    </div>
  </div>  

  <div class="col-md-2">
    <div class="card box-shadow-0 animated zoomIn wfont" data-appear="appear" data-animation="zoomIn" style="background-color: #B38867;">
      <div class="card-content">
        <a onclick="openAdd('main/loadUrl/main/dashboard_kontrak_detail/?jenis=selesai&tahun=<?=$getTahun?>&uki=<?=$unitkerja?>&uid=<?=$this->USER_LOGIN_ID?>')">
          <div class="card-body" style="">
              <div class="media">
                  <div class="media-body text-center">
                      <span style="margin-top: 15%; font-size: .9em;">Selesai Kontrak</span>
                      <h2 class="wfont mt-2"><b><?php echo $countSelesai ?> </b></h2>
                  </div> 
              </div>
              <div class="mt-1 text-center wfont">
                <?= '<small style="font-weight:bold">Paket</small>'; ?> 
              </div>
          </div>
        </a>
      </div>
    </div>
  </div>   
</div>


<!--   <div class="card box-shadow-0 animated zoomIn" data-appear="appear" data-animation="zoomIn">
    <div class="card-header card-head-inverse bg-primary">
      <h4 class="card-title text-white" style="font-size:.9em !important">Work List</h4>
      <a class="heading-elements-toggle"><i class="fa fa-ellipsis-v font-medium-3"></i></a>
      <div class="heading-elements">
          <ul class="list-inline mb-0">
            <li><a data-action="expand"><i class="ft-maximize"></i></a></li>
          </ul>
      </div>
    </div>
    <div class="card-content">
      <div class="card-body" style="padding: .7em" > 
        <table id="example" class="display table-bordered table-responsive" cellspacing="0" width="100%" style="border-bottom: none !important">
          <thead>
            <tr>
              <th width="1px">Id</th> 
              <th style="width: 45%">Paket Pengadaan</th>
              <th style="width: 10%">Metode Pengadaan</th>    
              <th style="width: 10%">Metode ID</th>    
              <th style="width: 10%">User</th>    
              <th style="width: 10%">Penyedia</th>   
              <th style="width: 10%">Nilai Kontrak</th>      
              <th style="width: 10%">Tanggal BAST</th>    
              <th style="width: 10%">Termin</th>   
              <th style="width: 10%">Jenis Kontrak</th>
              <th style="width: 10%">PIC Pengendali</th>
              <th style="width: 10%">Tahap</th>
              <th style="width: 10%">Status</th>
            </tr>       
          </thead>
        </table> 
    </div>
  </div>
</div> -->
