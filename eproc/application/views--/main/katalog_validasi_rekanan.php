<?php
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 * 
 */

$this->libsession->cekSession();

$reqId = httpFilterRequest("reqId");


$this->load->model("Katalog");
$this->load->model("Rekanan");
$katalog = new Katalog();
$rekanan = new Rekanan();
$katalog->selectByParamsViewKatalog(array(), -1, -1, " AND A.REKANAN_ID = '".$reqId."' ORDER BY A.STATUS DESC ");

$katalog_total = new Katalog();
$katalog_publish = new Katalog();
$katalog_total->selectByParamsViewKatalog2(array(), -1, -1, " AND A.REKANAN_ID = '".$reqId."'");
$katalog_total->firstRow();
$katalogTotal = $katalog_total->getField('total_katalog');
$katalog_publish->selectByParamsViewKatalog2(array(), -1, -1, " AND A.REKANAN_ID = '".$reqId."' AND A.PUBLISH = '1'");
$katalog_publish->firstRow();
$katalogPublish = $katalog_publish->getField('total_katalog');
$katalogNonverified = $katalogTotal - $katalogPublish;

$rekanan->selectByParams2(array(), -1, -1, " AND A.REKANAN_ID = '".$reqId."' ");
$rekanan->firstRow();

$this->load->model("Katalogsuratpernyataan");

$Katalogsuratpernyataan = new Katalogsuratpernyataan();
$Katalogsuratpernyataan->selectByParams(array('A.CREATED_BY' => $rekanan->getField('USER_LOGIN_ID') ),-1,-1," ORDER BY SPID DESC LIMIT 3");
?>

<script src="<?=base_url()?>assets/new/vendors/js/extensions/listjs/list.min.js"></script>
<script src="<?=base_url()?>assets/new/js/scripts/extensions/list.js"></script>

<script type="text/javascript">

function publish(a) {
  var value = a;
  var name = $('#publish'+value).data("name");
  var rek = $('#publish'+value).data("rek");
  var value = $('#publish'+value).val();
  if ($('#publish'+value).is(":checked"))
  {
    var check = '1';
  } else {
    var check = '0';
  }
  $.post("katalog_json/publish",
  {
    name: name,
    value: value,
    check: check,
    rek: rek
  },
  function(data, status){
  	getNotif();
    var str = data;
    var isNotif = str.split("||");
    if (isNotif[0] === 'Gagal') {
      $('#publish'+value).prop('checked', false);
      alertError2(isNotif[1]);
    } else {
      alertSuccess2(isNotif[1]);
    }

    if (check === '1') {
	  $('#born'+value).val('1');
    } else {
	  $('#born'+value).val('0');
    }
	  $('#katalogPublish').html(isNotif[2]);
	  $('#katalogNonverified').html(isNotif[3]);
	  if(isNotif[3] == 0) {
	  	// $("#btnCetakSK").attr('style','display:display');
	  } else {
	  	// $("#btnCetakSK").attr('style','display:none');
	  }

  });
}

</script>

<style type="text/css">
  .card-text a {
    font-size: 11px;
  }
.list-group-item { padding: .7rem .9rem }
p.born { margin-bottom: 0; }
h3.name { margin-bottom: 0 }
h3.name a { color: #000; }
.fa { margin-right: 10px }
</style>

<section id="backColor">
  <div class="row">

    <div class="col-md-2 col-sm-12">
      <div class="card border-bottom-primary" style="zoom: 1;">
        <div class="card-body">
          <div class="card-text">
           <?php
            if($this->USER_TYPE_ID == "6") {  ?>
              <a href="<?= base_url() ?>main/index/katalog_rekanan" class="btn btn-success btn-lg mb-1 text-left" style="width: 100%; opacity: .3"><i class="fa fa-cogs fa-lg pull-right"></i> Katalog</a>
              <a href="<?= base_url() ?>main/index/katalog_rekanan_add" class="btn btn-success btn-lg mb-1 text-left" style="width: 100%;"><i class="fa fa-plus fa-lg pull-right"></i> Tambah Katalog</a>
              <a href="" class="btn btn-success btn-lg mb-1 text-left" style="width: 100%"><i class="fa fa-shopping-basket fa-lg pull-right"></i> Penawaran</a>
            <?php
            } ?>

            <?php
            if($this->USER_TYPE_ID == "1" || $this->USER_TYPE_ID == "2"){  // 1:admin, 2:validator?>
              <a href="<?= base_url() ?>main/index/katalog_rekanan" class="btn btn-success btn-lg mb-1 text-left" style="width: 100%;"><i class="fa fa-cogs fa-lg pull-right"></i> Katalog</a>
              <a href="<?= base_url() ?>main/index/katalog_validasi" class="btn btn-success btn-lg mb-1 text-left" style="width: 100%; opacity: .3"><i class="fa fa-edit fa-lg pull-right"></i> Verifikasi</a>
              <a href="<?= base_url() ?>main/index/katalog_laporan" class="btn btn-success btn-lg mb-1 text-left" style="width: 100%;"><i class="fa fa-flag fa-lg pull-right"></i> Laporan</a>
            <?php
            } ?>
          </div>
        </div>
      </div>
    </div>

    <div class="col-md-10 col-sm-12">
      <div class="card border-bottom-primary">
        <div class="card-header">
          <h4 class="card-title">Katalog <small> Verifikasi Detail</small></h4>

	  				<!-- <a style="<?php // if($katalogNonverified > 0){ echo 'display:none'; } ?>" href="main/loadUrl/report/katalog_skt_pdf/?reqId=<?php //$reqId?>&reqKode=<?php // $rekanan->getField('KODE') ?>" class="btn btn-primary text-white" id="btnCetakSK"> <i class="fa fa-print"></i> Cetak Surat Katalog Terdaftar </a>  -->
	  					
		  				<a href="main/index/katalog_validasi" class="<?= CLASS_BTN_DANGER ?> pull-right"> <i class="fa fa-arrow-left"></i> Kembali </a>
		  				<a target="_blank" href="main/loadUrl/report/katalog_skt_pdf/?reqId=<?=$reqId?>&reqKode=<?= $rekanan->getField('KODE') ?>" class="<?= CLASS_BTN_PRIMARY ?> mr-1 pull-right" id="btnCetakSK"> <i class="fa fa-print"></i> Cetak Surat Katalog Terdaftar </a>
	  					
	  					<div class="btn-group pull-right mr-1">
                <button type="button" class="<?= CLASS_BTN_INFO ?> dropdown dropdown-notification nav-item " aria-haspopup="true" aria-expanded="false"> Kontrak Katalog & SP Kewajaran Harga <span class="fa fa-chevron-down"></span>
                  <div class="dropdown-menu dropdown-menu-right">
					  				<?php 
					  				while($Katalogsuratpernyataan->nextRow())
										{
										  if (file_exists('uploads/vms/surat_pernyataan/'.$Katalogsuratpernyataan->getField("PATH_SP"))) { 
										    $filenya = $Katalogsuratpernyataan->getField("PATH_SP");
										  	?>
                  				<a href="uploads/vms/surat_pernyataan/<?= $filenya ?>" target="_blank">
                  					<span class="dropdown-item" style="font-size:.7em"><?= $Katalogsuratpernyataan->getField("FILE_SP") ?> </span>
                  				</a>
										<?php 
										  } else {
										    echo "-";
										  }

										} ?> 
                  </div>
                </button>  
              </div>

        </div>
        <div class="card-body area-datatable">
          <div class="form-body">
          	<!-- <hr> -->
          	<div class="row"> 
	          	<div class="col-xl-12 col-lg-12 col-12" style="padding: 10px">
	          		<div class="media-body">
						<ul class="ml-2 px-0 list-unstyled">
							<li class="text-bold-800"> <h2><?= $rekanan->getField('NAMA') ?></h2></li>
							<li><span class="fa fa-phone"></span> <?= $rekanan->getField('TELEPON')?></li>
							<li><span class="fa fa-envelope-o"></span> <?= $rekanan->getField('EMAIL')?></li>
							<li><span class="fa fa-building-o"></span><?= $rekanan->getField('ALAMAT')?>, <?= $rekanan->getField('KOTA')?></li>
						</ul>
					</div><hr>
	          	</div>
	        </div>
          	<div class="row" style="margin-left: 15%"> 
		          	<div class="col-xl-3 col-lg-6 col-12">
			            <div class="card bg-gradient-directional-info">
			                <div class="card-content">
			                    <div class="card-body">
			                        <div class="media d-flex">
			                            <div class="align-self-center">
			                                <i class="icon-social-dropbox text-white font-large-2 float-left"></i>
			                            </div>
			                            <div class="media-body text-white text-right">
			                                <h3 class="text-white"><?= $katalogTotal ?></h3>
			                                <span>Total Katalog</span>
			                            </div>
			                        </div>
			                    </div>
			                </div>
			            </div>
			        </div>
			        <div class="col-xl-3 col-lg-6 col-12">
			            <div class="card bg-gradient-directional-success">
			                <div class="card-content">
			                    <div class="card-body">
			                        <div class="media d-flex">
			                            <div class="align-self-center">
			                                <i class="icon-check text-white font-large-2 float-left"></i>
			                            </div>
			                            <div class="media-body text-white text-right">
			                                <h3 class="text-white" id="katalogPublish"><?= $katalogPublish ?></h3>
			                                <span>Verifikasi</span>
			                            </div>
			                        </div>
			                    </div>
			                </div>
			            </div>
			        </div>
			        <div class="col-xl-3 col-lg-6 col-12">
			            <div class="card bg-gradient-directional-danger">
			                <div class="card-content">
			                    <div class="card-body">
			                        <div class="media d-flex">
			                            <div class="align-self-center">
			                                <i class="icon-close text-white font-large-2 float-left"></i>
			                            </div>
			                            <div class="media-body text-white text-right">
			                                <h3 class="text-white" id="katalogNonverified"><?= $katalogNonverified ?></h3>
			                                <span>Belum Verifikasi</span>
			                            </div>
			                        </div>
			                    </div>
			                </div>
			            </div>
			        </div>
		    </div>
		    <hr>
            <section id="lists">
                <div id="basic-list">
                  <!-- <div id="sticker"> -->
                  <div>
                    <input type="text" class="search form-control round border-primary mb-1" placeholder="Cari berdasarkan Nama Produk, Nomor Produk Penyedia" />
                    <div class="row">
                        <div class="col-md-6 col-sm-12">
                            <button class="sort btn btn-block btn-outline-warning btn-round mb-2" data-sort="name">Sort by Nama Produk</button>
                        </div>
                        <div class="col-md-6 col-sm-12">
                            <button class="sort btn btn-block btn-outline-success btn-round mb-2" data-sort="born">Sort by Publish</button>
                        </div>
                    </div>
                  </div>

                    <ul class="list-group list" style="height: 700px; overflow-y: scroll;">
                    	<?php
                    	if ($katalog->countRow() > 0) {
                    	while($katalog->nextRow())
	                      	{
	                        	if ($katalog->getField("STATUS") == '1') {
	                        		$backColor = '#fff';
	                        	} else {
	                        		$backColor = '#e7858f';
	                        	}

	                        	if ($katalog->getField("PUBLISH") == '1') {
	                        		$checkPublish = ' checked=""';
	                        	} else {
	                        		$checkPublish = '';
	                        	}
	                      	?>
	                        <li class="list-group-item" style="background-color: <?= $backColor ?>">
	                            <h3 class="name">
	                            	<a onClick="openAdd('main/loadUrl/main/katalog_validasi_rekanan_detail_produk?reqId=<?= $katalog->getField("KATALOGID") ?>');">
		                            	<?php
		                            	if ($katalog->getField("NOPRODUKPENYEDIA")) { ?>
		                            		<small style="font-size: .7rem"><?= $katalog->getField("NOPRODUKPENYEDIA") ?></small><br>
		                            	<?php
		                            	} ?>
		                            	<?= $katalog->getField("NAMAPRODUK") ?>
	                            	</a>
		                            	<div class="pull-right">
			                        		<?php
			                            	if ($katalog->getField("STATUS") == 1) { ?>
			                            		<input type="checkbox" data-allow="true" id="publish<?= $katalog->getField("KATALOGID") ?>" data-rek="<?= $katalog->getField("REKANAN_ID") ?>" value="<?= $katalog->getField("KATALOGID") ?>" data-name="<?= $katalog->getField("NAMAPRODUK") ?>" class="publish" name="validasi" style="cursor: pointer;" onclick="publish(<?= $katalog->getField("KATALOGID") ?>)" <?= $checkPublish ?>>
			                            	<?php
			                            	} ?>
		                            	</div>
	                            </h3>
	                            <p class="born">
	                            	<input type="hidden" value="<?= $katalog->getField("PUBLISH") ?>" id="born<?= $katalog->getField("KATALOGID") ?>">
	                            	<span class="fa fa-money"></span> Rp. <?= number_format($katalog->getField("HARGA"),2,',','.') ?>
	                            </p>
	                        </li>
	                        <?php
	                        }
	                      }?>
                    </ul>
                </div>
			</section>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>
