<?php
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 *
 */

$this->libsession->cekSession();    // block halaman khusus untuk masing-masing user

$this->load->model("Paket");
$reqId = httpFilterRequest("reqId");
$paket = new Paket();
$paket->selectByParamsMonitoring(array(), -1, -1, " AND PAKET_ID = '".$reqId."' ");
$paket->firstRow();
$paket_metode_lelang_id = $paket->getField("PAKET_METODE_LELANG_ID"); // 1-e-Tender ,7-e-Tender Cepat, 2-Pengadaan Langsung ,5-Penunjukan Langsung Cepat, 6-Pembelian langsung

if ($reqId == '' || $paket_metode_lelang_id != '6')
  redirect(base_url('main'));
 ?>
<link rel="stylesheet" type="text/css" href="<?=base_url() ?>assets/new/css/plugins/forms/checkboxes-radios.css">

<style type="text/css">
  .media-body { margin-left: 10px }
  .list-group-item { padding: .5em !important }
  h1 { font-size: 13px }
  h2 { font-size: 13px }
  .match-height { padding: 0 15px }
  #btnfull { width: 100%; cursor: default; }
  .cursorPoin { cursor: pointer; }
  .pagingPadd { background-color: #fff; padding: 10px 0 20px 10px }
  .pagingPadd2 { background-color: #fff; padding: 15px 10px }
  .backWhite { background-color: #fff;}
  .cursor { cursor: pointer; padding: 0px 20px 10px 23px; }
  .cursor2 { padding: 0px 10px 0px 13px; font-size: .9em}
  .labelActive { color: #1a73e8; border-bottom: 3px solid #1a73e8;}
  .btn-search-x { padding: 0 50px }
  .media-heading { font-weight: bold; color: #1a73e8; font-size: 1.3em; }
  .containerKet {height: 42px; max-height: 42px; font-size: 14px; overflow-x: hidden; overflow-y: hidden;}
</style>

<?php
include_once("functions/string.func.php");
include_once("functions/date.func.php");
include_once("functions/default.func.php");

$this->load->library("Pagination");


$this->load->model("Katalogkategori");
$this->load->model("Katalog");
$this->load->model("Katalogcompare");
$this->load->model("Katalogrekanan");

$katalog_kategori_url = new Katalogkategori();
$katalog_kategori = new Katalogkategori();
$katalog = new Katalog();
$katalogrekananTotal = new Katalogrekanan();
$katalog_count = new Katalog();
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
</script>

<section id="backColor">
  <div class="row">

    <div class="col-md-12 col-sm-12">
      <section id="basic-examples">
        <div class="row match-height">
          <?php
          // echo $id;
          $id = 0;
          $showRecord = 10;
          $pageView = "katalog_search_json/json/";

			$arrStatement = array('A.KATEGORI_PARENT_ID' => $id, 'A.STATUS' => '1', 'A.PUBLISH' => '1');
			$katalog->selectByParamsViewKatalogByKategori2($arrStatement, $showRecord, 0);
			// echo $katalog->query;exit;
			$rowCount = $katalog_count->getCountByParamsViewKatalogByKategori2($arrStatement);
			// $urlShare = base_url('main/index/katalog?name='.$name);
			// $urlShare = 'http://pdpal.eproc19.com/main/index/katalog?name='.$name;

          $arrSerialized = serialize($arrStatement);
          $arrSerialized = str_replace('"', '@', $arrSerialized);
          $pagConfig = array('baseURL'=>$pageView, 'showRecord' => '\''.$showRecord.'\'', 'totalRows'=>$rowCount, 'perPage'=>$showRecord, 'contentDiv'=>'tbodyKatalog', 'arrSerialized' => $arrSerialized, 'searchVarible' => "reqPencarian");
          // echo "<pre>"; print_r($pagConfig); die();
          $pagination =  new Pagination($pagConfig);

          ?>
          <div class="col-xl-12 col-md-12 col-sm-12 mb-1">
            <div class="heading-elements mb-2 pull-left" id="tombol">
              <a href="main/index/katalog_cart/?reqId=<?= $reqId ?>" class="<?= CLASS_BTN_DANGER ?>"> <i class="fa fa-arrow-left"></i> Kembali </a>
            </div>
          </div>
          <div class="col-xl-12 col-md-12 col-sm-12 mb-2" id="searchCol">
            <div class="card-block">
              <fieldset>
                <div class="input-group">
                  <input type="text" id="reqPencarian" class="form-control" style="border-radius: 20px 0 0 20px; height: 40px">
                  <div class="input-group-append">
                    <button class="btn btn-danger btn-search-x" id="submitCari" onClick="<?= $pagination->createSearching2();?>" type="submit"><span class="fa fa-search"></span> cari</button>
                  </div>
                </div>
              </fieldset>
            </div>
            <input type="hidden" id="reqPencarian2" class="form-control" value="0||<?=$reqId ?>">
          </div>
          <div class="col-md-12">
          	<span class="fa fa-search cursor labelActive" onClick=" setValueSearch('0||<?=$reqId ?>')" id="search0"> Semua</span>
          	<span class="fa fa-building-o cursor" onClick=" setValueSearch('1||<?=$reqId ?>')" id="search1"> Perusahaan</span>
          	<span class="fa fa-tag cursor" onClick=" setValueSearch('2||<?=$reqId ?>')"  id="search2"> Merek</span>
          	<span class="fa fa-list cursor" onClick=" setValueSearch('3||<?=$reqId ?>')"  id="search3"> Kategori</span>
          </div>
          <div class="col-xl-12 col-md-12 col-sm-12 mb-2"><hr></div>

          <div class="match-height" id="tbodyKatalog" style="width: 100%; margin-bottom: 70px">
            <script type="text/javascript">
              // $(document).ready(function(){
              //   jQuery(".compare").on('change', function () {
              //     var view = jQuery(this);
              //       var isAllow = view.data('allow');
              //       if (isAllow) {
              //         var value = $(this).data("value");
              //         var name = $(this).data("name");
              //         if ($('#compare'+value).is(":checked"))
              //         {
              //           var check = '1';
              //         } else {
              //           var check = '0';
              //         }
              //         // alert(check);
              //         $.post("katalog_json/compare",
              //         {
              //           name: name,
              //           value: value,
              //           check: check
              //         },
              //         function(data, status){
              //           var str = data;
              //           var isNotif = str.split("||");
              //           $('#totalBanding').html(isNotif[2]+' Produk');
              //           if (isNotif[0] === 'Gagal') {
              //             // this.checked = false;
              //             $('#compare'+value).prop('checked', false);
              //             alertError2(isNotif[1]);
              //           } else {
              //             $('.btn-github').addClass('bounceIn');
              //             setTimeout(function() {
              //               $('.btn-github').removeClass('bounceIn');
              //             }, 1000);
              //             $('.fa-random').addClass('shake');
              //             setTimeout(function() {
              //               $('.fa-random').removeClass('shake');
              //             }, 1000);
              //           }
              //         });
              //       }
              //   });

              // });
            </script>

	            <!-- <div class="col-xl-12 col-md-12 col-sm-12 backWhite pagingPadd2 mb-1">
	            	<div class="media">
        					<div class="media-body pl-1">
        						<h5 class="media-heading"> <a href="#"> Cookie candy </a> </h5>
        						Cookie candy dragée marzipan gingerbread pie pudding. Brownie fruitcake wafer bonbon gummi bears apple pie. Brownie lemon drops chocolate cake donut croissant cotton candy. Cookie candy dragée marzipan gingerbread pie pudding. Brownie fruitcake wafer bonbon gummi bears apple pie. Brownie lemon drops chocolate cake donut croissant cotton candy. <hr>
        						<fieldset class="checkboxsas btn btn-danger btn-sm">
        							<input type="checkbox" class="cursorPoin compare" data-allow="true" id="compare2" data-value="2" data-name="sanyo" <?= $checkProduk ?>>Bandingkan
        		              	</fieldset>
        		              	<fieldset class="checkboxsas btn btn-dark btn-sm">
        							<a id="cart21" data-val="<?= $i ?>" onclick="test('21')"><span class="fa fa-shopping-cart cart"></span> </a>
        		              	</fieldset>
        						<a href="">
        							<span class="fa fa-building-o cursor2"> PT Mayora Tbk.</span>
        						</a>
        			          	<span class="fa fa-tag cursor2"> Nikon</span>
        			          	<span class="fa fa-money cursor2"> Rp. 2.999.0000.000</span>
        			          	<span class="fa fa-list cursor2"> excavator, mini excavator, Spare part excavator</span>
        					</div>
        					</div>
	            </div> -->

          </div>

        </div>
      </section>
    </div>

  </div>
</section>

<?php
$katalogcompareTotalAll = new Katalogcompare();
$katalogrekananTotal = new Katalogrekanan();
$cekTotalAll = $katalogcompareTotalAll->getCountByParams(array('SESSIONID' => session_id()));
$cekTotal = $katalogrekananTotal->getCountByParams(array('A.PAKET_ID' => $reqId));

if ($cekTotalAll > 0) {
  $cekTotalAll = $cekTotalAll.' Produk';
} else {
  $cekTotalAll = '';
}

if ($cekTotal > 0) {
  $cekTotalCart = $cekTotal;
} else {
  $cekTotalCart = '0';
}
?>
<a href="<?= base_url('main/index/katalog_compare?id='.session_id()) ?>" class="btn-social width-200 mr-1 mb-1 <?= CLASS_BTN_DANGER ?> danger-animated animated" data-animation="zoomInLeft" style="position: fixed;bottom: 30px;left: 30px;"><span class="fa fa-random font-medium-3"></span> <small style="font-size: .9em"> Bandingkan <span id="totalBanding"><?= $cekTotalAll ?></span></small></a>
<a href="<?= base_url('main/index/katalog_cart/?reqId='.$reqId) ?>" class="btn round btn-social width-100 mr-1 mb-1 btn-github animated" data-animation="zoomInLeft" style="position: fixed;bottom: 80px;left: 30px;"><span class="fa fa-shopping-cart font-medium-3"></span> <small style="font-size: .9em"> <span id="totalCart"><?= $cekTotalCart ?></span></small></a>
