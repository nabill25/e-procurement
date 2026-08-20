<?php
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 *
 */

$this->libsession->cekSession();    // block halaman khusus untuk masing-masing user
 ?>

<link rel="stylesheet" type="text/css" href="<?=base_url() ?>assets/new/css/plugins/forms/checkboxes-radios.css">

<style type="text/css">
  .media-body {margin-left: 10px }.list-group-item {padding: .5em !important }h1 {font-size: 13px }h2 {font-size: 13px }.match-height {padding: 0 15px }#btnfull {width: 100%;cursor: default;}.cursorPoin {cursor: pointer;}.pagingPadd {background-color: #fff;padding: 10px 0 20px 10px }.pagingPadd2 {background-color: #fff;padding: 15px 10px }.backWhite {background-color: #fff;}.cursor {cursor: pointer;padding: 0px 20px 10px 23px;}.cursor2 {padding: 0px 10px 0px 13px;font-size: .9em}.labelActive {color: #1a73e8;border-bottom: 3px solid #1a73e8;}.btn-search-x {padding: 0 50px }.media-heading {font-weight: bold;color: #1a73e8;font-size: 1.3em;}.containerKet {font-size: 14px;overflow-x: hidden;overflow-y: hidden;}</style>
<?php

include_once("functions/string.func.php");
include_once("functions/date.func.php");
include_once("functions/default.func.php");

$this->load->library("Pagination");

$this->load->model("Rekananpotensi");

$rekanan = new Rekananpotensi();
$rekanan_count = new Rekananpotensi();
?>

<script type="text/javascript">
 function setValueSearch(a) {
  $('#reqPencarian2').val(a);
  var b = a.split("||");
  for (var i = 0; i < 4; i++) {
  	$('#search'+i).removeClass('labelActive');
  }
  $('#search'+b[0]).addClass('labelActive');
  $('#submitCari').click();
 }

$(document).ready(function() {
   $('#search4').on('change', function() {
    var selectedValue = $('#search4').val();
    $("#reqPencarian4").val(selectedValue);
    $('#submitCari').click();
  });

   $('input[type="checkbox"]').click(function(){
    if($(this).is(":checked")){
      //input element where you put value
      $("#reqPencarian3").val("1");
    }
    else if($(this).is(":not(:checked)")){
      $("#reqPencarian3").val("0");
      //  console.log( $("#isClicked").val());
    }
    $('#submitCari').click();
  });

   $('#submitCari').on('click', function() {
    $('#search-loader').show();
  });
});


</script>

<section id="backColor">
  <div class="row">

    <div class="col-md-12 col-sm-12">
      <section id="basic-examples">
        <div class="row match-height">
          <?php
          // echo $id;
          $showRecord = 5;
          $pageView = "rekanan_search_json/json/";

          // $arrStatement = array('STATUS_VALIDASI' => '1');
    			$arrStatement = array();
    			// $rekanan->selectByParamsAll($arrStatement, $showRecord, 0);
			    // // echo $rekanan->query;
			    // $rowCount = $rekanan_count->getCountselectByParamsAll($arrStatement);

          $arrSerialized = serialize($arrStatement);
          $arrSerialized = str_replace('"', '@', $arrSerialized);
          $pagConfig = array('baseURL'=>$pageView, 'showRecord' => '\''.$showRecord.'\'', 'totalRows'=>$rowCount, 'perPage'=>$showRecord, 'contentDiv'=>'tbodyKatalog', 'arrSerialized' => $arrSerialized, 'searchVarible' => "reqPencarian");
          // echo "<pre>"; print_r($pagConfig); die();
          $pagination =  new Pagination($pagConfig);

          ?>
          <div class="col-xl-12 col-md-12 col-sm-12 mb-2" id="searchCol">
            <div class="card-block">
              <fieldset>
                <div class="input-group">
                  <input type="text" id="reqPencarian" class="form-control" style="border-radius: 20px 0 0 20px; height: 40px">
                  <div class="input-group-append">
                    <button class="btn btn-danger btn-search-x" id="submitCari" onClick="<?= $pagination->createSearching4();?>" type="submit"><span class="fa fa-search"></span> cari</button>
                  </div>
                </div>
              </fieldset>
            </div>
            <input type="hidden" id="reqPencarian2" class="form-control" value="0||<?=$this->USER_TYPE_ID ?>">
            <input type="hidden" id="reqPencarian3" class="form-control" value="1">
            <input type="hidden" id="reqPencarian4" class="form-control" value="3"> <!-- Kualifikasi  -->
          </div>
          <div class="col-md-12">
          	<span class="icon-magnifier cursor labelActive" onClick=" setValueSearch('0||<?=$this->USER_TYPE_ID ?>')" id="search0"> Semua</span>
          	<span class="icon-notebook cursor" onClick=" setValueSearch('1||<?=$this->USER_TYPE_ID ?>')" id="search1"> Data Admin</span>
          	<span class="icon-docs cursor" onClick=" setValueSearch('2||<?=$this->USER_TYPE_ID ?>')"  id="search2"> Data Teknis</span>
            <span class="icon-list cursor" onClick=" setValueSearch('3||<?=$this->USER_TYPE_ID ?>')"  id="search3"> KBLI / SBU</span>
          	<input type="checkbox" name="reqApproval" checked=""> Approval
            <span class="ml-1" >
              <select class="kualifikasiid" id="search4">
                <option value="3">Kecil / Non-Kecil</option>
                <option value="2">Non-Kecil</option>
                <option value="1">Kecil</option>
              </select>
            </span>
          </div>
          <div class="col-xl-12 col-md-12 col-sm-12 mb-2"><hr></div>

          <div class="match-height" id="tbodyKatalog" style="width: 100%; margin-bottom: 70px">
            <img id="search-loader" src="images/loader-page.gif" style="display:none">
            <!-- <script type="text/javascript">
            </script>  -->
            <?php echo $pagination->createLinks4()?>
          </div>
        </div>
      </section>

    </div>

  </div>
</section>
