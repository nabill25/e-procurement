<?php
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 * 
 */

// $this->libsession->cekSession();
// 180724 Pengecualian untuk user dibawah ini
if($this->USER_TYPE_ID != "1" && $this->USER_TYPE_ID != "4" && $this->USER_TYPE_ID != "6" && $this->USER_TYPE_ID != "10") 
{ } else { redirect(base_url().'main/index/403'); }

include_once("functions/string.func.php");
include_once("functions/date.func.php");
include_once("functions/default.func.php");

$this->load->model("Katalog");
$this->load->model("Katalogfoto");
$this->load->model("Katalogkategorirekanan");
$id = httpFilterRequest("id") ? httpFilterRequest("id") : '-';

if ($id == '-')
	redirect(base_url().'main/index/katalog');

$katalog = new Katalog;
$katalog_foto = new Katalogfoto;
$katalog_kategori_rekanan = new Katalogkategorirekanan;

$arrStatement = array('A.KATALOGID' => $id, 'A.STATUS' => '1', 'A.PUBLISH' => '1');
$katalog->selectByParams($arrStatement, -1, -1);
$katalog->firstRow();
$katalogid = $katalog->getField("KATALOGID");
if ($katalogid == '')
	redirect(base_url('main/index/katalog'));

$Katalogfoto = new Katalogfoto();
$Katalogfoto->selectByParams(array('KATALOGID' => $katalogid), -1, -1);
$katalog_kategori_rekanan->selectByParams(array('KATALOGID' => $katalogid), -1, -1);

// if ($Katalogfoto->countRow() > 0) {
	while($Katalogfoto->nextRow())
	{
		$dataKatalogFoto[] =  'images/katalog/'.$Katalogfoto->getField("path_file");
	}
// } else {
// 	$Katalogfoto = array("");
// }
?>

<link rel="stylesheet" type="text/css" href="<?=base_url() ?>assets/new/vendors/css/extensions/zoom.css">
<script src="<?=base_url()?>assets/new/vendors/js/extensions/zoom.min.js"></script>

<script type="text/javascript">
$(function(){
	$('#ffkatalog').form({
		url:'<?= base_url('katalog_json/addLaporan') ?>',
		onSubmit:function(){
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
			hideLoad();
		    alertSuccess2(data);
		    $('#ffkatalog').trigger("reset");
		}
	});

});
</script>

<style type="text/css">
	h1 { font-size: 1.5em }
	.font12 { font-size: 12px }
	.font14 { font-size: 14px }
	.merek { font-size: 1em }
	.namapt { font-size: 1.3em }
	.nilai { font-size: 1.7em }
	.tdHead { width: 25%; font-weight: bold }
	.tdHead2 { width: 25%; font-weight: bold }
	.tdContent { width: 75%; font-weight: normal }
	.tdContent2 { width: 35%; font-weight: normal }
	.padd-badge { padding: 6px 10px  }

.preview-thumbnail.nav-tabs {
  border: none;
  margin-top: 15px; }
.preview-thumbnail.nav-tabs li {
  width: 10%;
  margin-right: 1%; }
.preview-thumbnail.nav-tabs li img {
  max-width: 100%;
  display: block; }
.preview-thumbnail.nav-tabs li a {
  padding: 0;
  margin: 0; }
.preview-thumbnail.nav-tabs li:last-of-type {
  margin-right: 0; }
</style>

<section id="backColor">
  <div class="row">
    <div class="col-md-12 col-sm-12">
      <div class="card" style="zoom: 1;">
        <div class="card-content collapse show">
          <div class="card-body border-primary" id="tbodyData">
          	<div class="row">
			  			<div class="col-md-5 text-center">
								<?php
								if (!$dataKatalogFoto) {
								?>
									<img src="images/katalog/katalognotfound.jpg" width="70%">
								<?php
								} else
								{ ?>
				      	<div id="carousel-example-generic" class="carousel slide" data-ride="carousel">
									<ol class="carousel-indicators">
									<?php
										foreach ($dataKatalogFoto as $key => $value) {
									?>
										<li data-target="#carousel-example-generic" data-slide-to="<?= $key ?>" class="<?php if($key==0) { echo"active"; } ?>"></li>
									<?php
									} ?>
									</ol>
									<div class="carousel-inner" role="listbox">
										<?php
										foreach ($dataKatalogFoto as $key2 => $value2) {
						                  if (file_exists($value2)) {
						                  	if ($key2==0) {
						                  		$actv = 'active';
						                  	} else {
						                  		$actv = '';
						                  	}
						                    echo '<div class="carousel-item '.$actv.'">
													<img src="'.$value2.'" alt="eproc19.com" style="width:100%">
												  </div> ';
						                  } else {
						                  }
										} ?>
									</div>

									<a class="carousel-control-prev" href="#carousel-example-generic" role="button" data-slide="prev">
										<span class="carousel-control-prev-icon" aria-hidden="true"></span>
										<span class="sr-only">Previous</span>
									</a>
									<a class="carousel-control-next" href="#carousel-example-generic" role="button" data-slide="next">
										<span class="carousel-control-next-icon" aria-hidden="true"></span>
										<span class="sr-only">Next</span>
									</a>
								</div>
						 		 <div class="col-md-12">
						 		 	<ul class="preview-thumbnail nav nav-tabs">
										<?php
										foreach ($dataKatalogFoto as $key3 => $value3) {
						                  if (file_exists($value3)) {
						                    echo '<li><a><img src="'.$value3.'" alt="eproc19.com" data-action="zoom"/></a></li>';
						                  } else {
						                  }
										} ?>
									</ul>
								</div>
								<?php
							} ?>
			  </div>
			  <div class="col-md-7">
			    <div class="card">
			      <div class="card-content mt-1">
			        	<h1 style="font-weight: bold"><?= $katalog->getField('NAMAPRODUK') ?></h1>
			        	<p class="merek"><?= $katalog->getField('MEREK') ?></p> <hr>
			        	<p class="namapt"><?= $katalog->getField('USER_NAMA') ?></p><hr>
			        	<p>Stok:
			        		<?php
			        		switch ($katalog->getField('JUMLAHSTOCK')) {
			        			case '1':
			        				echo '<span class="badge round padd-badge badge-primary">Tersedia</span>';
			        				break;
			        			case '2':
			        				echo '<span class="badge round padd-badge badge-success">Pre-order</span>';
			        				break;
			        			case '3':
			        				echo '<span class="badge round padd-badge badge-danger">Hubungi Penyedia</span>';
			        				break;

			        			default:
			        				# code...
			        				break;
			        		}
			        	 ?>
		        		</p><hr>
			        	<p class="nilai">Rp. <?= number_format($katalog->getField('HARGA'),2,',','.') ?></p><hr>
			        	<p class="kategori">Kategori: <br>
			        		<?php
			        		while($katalog_kategori_rekanan->nextRow())
							{ ?>
			        		<small class="badge border-info round badge-info padd-badge">
			        			<a href="main/index/katalog?kategori=<?= strtolower(str_replace(" ", "-", $katalog_kategori_rekanan->getField('NAMA'))) ?>">
			        			<?= $katalog_kategori_rekanan->getField('NAMA') ?>
			        			</a>
			        		</small>
			        		<?php
			        		} ?>
			        	</p>
			      </div>
			    </div>
			  </div>
			  <div class="col-md-12 col-sm-12 mt-2">
				  <ul class="nav nav-tabs nav-underline">
						<li class="nav-item">
							<a class="nav-link active show" id="baseIcon-tabspec" data-toggle="tab" aria-controls="tabspec" href="#tabspec" aria-expanded="true"><i class="icon-settings font-medium-3"></i> Spesifikasi</a>
						</li>
						<li class="nav-item">
							<a class="nav-link" id="baseIcon-tabsdesc" data-toggle="tab" aria-controls="tabsdesc" href="#tabsdesc" aria-expanded="true"><i class="icon-list font-medium-3"></i> Deskripsi Produk</a>
						</li>
						<li class="nav-item">
							<a class="nav-link" id="baseIcon-lamp" data-toggle="tab" aria-controls="lamp" href="#tabIcon42" aria-expanded="false"><i class="icon-folder font-medium-3"></i> Lampiran</a>
						</li>
						<li class="nav-item">
							<a class="nav-link" id="baseIcon-lap" data-toggle="tab" aria-controls="lap" href="#lap" aria-expanded="false"><i class="icon-note font-medium-3"></i> Laporkan</a>
						</li>
					</ul>
					<div class="tab-content px-1 pt-1">
						<div role="tabpanel" class="tab-pane active mt-1" id="tabspec" aria-expanded="true" aria-labelledby="baseIcon-tabspec">
							<table class="table table-bordered">
								<tbody>
									<tr> <td class="tdHead">Nomor Produk</td><td class="tdContent"><?= $katalog->getField('NOPRODUK') ?></td></tr>
									<tr> <td class="tdHead">Nama Produk</td><td class="tdContent"><?= $katalog->getField('NAMAPRODUK') ?></td></tr>
									<tr> <td class="tdHead">Merek</td><td class="tdContent"><?= $katalog->getField('MEREK') ?></td></tr>
									<tr> <td class="tdHead">Model/Type</td><td class="tdContent"><?= $katalog->getField('MODELTYPE') ?></td></tr>
									<tr> <td class="tdHead">Dimensi</td>
										 <td class="tdContent">
										 	<table class="table table-bordered">
										 		<tr>
										 			<td class="tdHead2">Diameter</td><td class="tdContent2"><?= $katalog->getField('DIAMETER') ?> cm</td>
										 			<td class="tdHead2">Panjang</td><td class="tdContent2"><?= $katalog->getField('PANJANG') ?> cm</td>
										 		</tr>
										 		<tr>
										 			<td class="tdHead2">Lebar</td><td class="tdContent2"><?= $katalog->getField('LEBAR') ?> cm</td>
										 			<td class="tdHead2">Tinggi</td><td class="tdContent2"><?= $katalog->getField('TINGGI') ?> cm</td>
										 		</tr>
										 	</table>
										 </td>
									</tr>
									<tr> <td class="tdHead">Kemasan</td><td class="tdContent"><?= $katalog->getField('KEMASAN') ?></td></tr>
									<tr> <td class="tdHead">Garansi</td><td class="tdContent"><?= $katalog->getField('LAMAGARANSI').' '.$katalog->getField('LAMAGARANSI2') ?></td></tr>
									<!-- <tr> <td class="tdHead">No. Produk Penyedia</td><td class="tdContent"><?= $katalog->getField('NOPRODUKPENYEDIA') ?></td></tr> -->
									<tr> <td class="tdHead">Tahun Pembuatan Produk</td><td class="tdContent"><?= $katalog->getField('UNITPENGUKURAN') ?></td></tr>
									<tr> <td class="tdHead">Jenis Produk</td><td class="tdContent"><?php if ($katalog->getField('JENISPRODUK') == '1') { echo "Lokal";} else { echo "Import";} ?></td></tr>
									<tr> <td class="tdHead">TKDN(%)</td><td class="tdContent"><?= $katalog->getField('TKDNPRODUK') ?></td></tr>
									<tr> <td class="tdHead">Berlaku Sampai</td><td class="tdContent"><?php $tglEx = explode(' ',$katalog->getField('BERLAKUSAMPAI')); echo getFormattedDate($tglEx[0]); ?></td></tr>
									<!-- <tr> <td class="tdHead">Type</td><td class="tdContent"><?= $katalog->getField('MODELTYPE') ?></td></tr> -->
									<!-- <tr> <td class="tdHead">No. Test Report</td><td class="tdContent"><?= $katalog->getField('NOMORTEST') ?></td></tr> -->
								</tbody>
							</table>
                            <hr>
							<footer class="blockquote-footer text-left pl-1">
                                <cite title="Source Title"><?= SYSTEM_NAME.' - '.SYSTEM_NAME_PT ?></cite>
                            </footer>
						</div>
						<div role="tabpanel" class="tab-pane mt-1" id="tabsdesc" aria-expanded="true" aria-labelledby="baseIcon-tabsdesc">
	                            <div class="media">
	                                <div class="media-body pl-1 font14">
	                                    <?= $katalog->getField('KETERANGANTAMBAHAN') ?>
	                                </div>
	                            </div>
	                            <hr>
								<footer class="blockquote-footer text-left pl-1">
	                                <cite title="Source Title"><?= SYSTEM_NAME.' - '.SYSTEM_NAME_PT ?></cite>
	                            </footer>
						</div>
						<div class="tab-pane mt-1" id="tabIcon42" aria-labelledby="baseIcon-lamp">
							<?php
							$this->load->model("Kataloglampiran");
							$Kataloglampiran = new Kataloglampiran();
							$Kataloglampiran_count = new Kataloglampiran();

							$Kataloglampiran->selectByParams(array('KATALOGID' => $katalogid));
							$contLampiran = $Kataloglampiran_count->getCountByParams(array('KATALOGID' => $katalogid));
							if ($contLampiran < 1) {
								echo '<span style="color:red">Tidak ada Lampiran</span>';
							} else
							{
								while($Kataloglampiran->nextRow())
								{
									if (file_exists('images/katalog/'.$Kataloglampiran->getField("path_file")) && $Kataloglampiran->getField("path_file") != '') {
					                  $filenya = $Kataloglampiran->getField("path_file");
					                } else {
														$filenya = 'katalognotfound.jpg';
					                }
								?>
									<a href="<?= 'images/katalog/'.$filenya ?>" target="_blank" class="btn btn-sm btn-social btn-block mb-1 btn-outline-yahoo"><i class="fa fa-download"></i> <?= $Kataloglampiran->getField('FILE') ?></a>
							<?php
								}
							} ?>
							<hr>
							<footer class="blockquote-footer text-left pl-1">
                                <cite title="Source Title"><?= SYSTEM_NAME.' - '.SYSTEM_NAME_PT ?></cite>
                            </footer>
						</div>
						<div class="tab-pane mt-1" id="lap" aria-labelledby="baseIcon-lap">
							<form id="ffkatalog" class="easyui-form form-horizontal" method="post" novalidate enctype="multipart/form-data" style="padding: 10px 10%">
							 <div class="alert alert-success">
							 	<ul>
							 		<li>Fitur ini dapat digunakan untuk mengirim pengaduan kepada Administrator <?= SYSTEM_NAME.' '.SYSTEM_NAME_PT ?></li>
							 		<li>Administrator <?= SYSTEM_NAME.' '.SYSTEM_NAME_PT ?> akan menjaga kerahasiaan data dan informasi yang Bapak/Ibu/Saudara/i berikan</li>
							 		<li>Silahkan mengisi formulir dibawah ini dengan baik, benar, dan dapat dipertanggungjawabkan </li>
							 	</ul>
							 </div> <hr>
				              <div class="row">
				                <div class="form-group col-md-12 mb-2">
				                  <label>Jenis Laporan</label>
				                  	<?php
				                  	$arrayJenis = array('Lainnya','Salah Kategori','Harga Tidak Wajar','Transaksi','Pelanggaran Merek Dagang'); ?>
                  					<select name="reqJenisLaporan" class="form-control" required>
                  						<?php
                  						foreach ($arrayJenis as $key => $valJenis) {
                  							echo '<option>'.$valJenis.'</option>';
                  						 } ?>
                  					</select>
				                </div>
				              </div>
				              <div class="row">
				                <div class="form-group col-md-12 mb-2">
				                  <label>Nama</label>
				                  <input type="text" name="reqNama" maxlength="50" accesskey="n" title="Nama harus diisi" class="form-control easyui-validatebox span7" value="<?=isset($reqNama)?$reqNama:''?>" id="reqNama" required />
				                </div>
				              </div>
				              <div class="row">
				                <div class="form-group col-md-12 mb-2">
				                  <label>Email</label>
				                  <input type="email" name="reqEmail" maxlength="50" accesskey="n" title="Email harus diisi" class="form-control easyui-validatebox span7" value="<?=isset($reqEmail)?$reqEmail:''?>" id="reqEmail" required />
				                </div>
				              </div>
				              <div class="row">
				                <div class="form-group col-md-12 mb-2">
				                  <label>Telepon/WA</label>
				                  <input type="text" name="reqTelepon" maxlength="50" accesskey="n" title="Email harus diisi" class="form-control easyui-validatebox span7" value="<?=isset($reqTelepon)?$reqTelepon:''?>" id="reqTelepon" required />
				                </div>
				              </div>
				              <div class="row">
				                <div class="form-group col-md-12 mb-2">
				                  <label>Alasan</label>
				                  <input type="text" name="reqAlasan" accesskey="n" title="Email harus diisi" class="form-control easyui-validatebox span7" value="<?=isset($reqAlasan)?$reqAlasan:''?>" id="reqAlasan" required />
				                </div>
				              </div>
				              <div class="form-actions">
				                <input type="hidden" name="reqKatalogid" value="<?= $katalogid ?>">
				                  <button type="submit" class="btn btn-primary" name="reqSubmit" id="reqSubmit">
				                  	<i class="fa fa-check-square-o"></i> <?=translate("Kirim Laporan", "Send")?>
			                	  </button>
				              </div>
				            </form>
				            <hr>
							<footer class="blockquote-footer text-left pl-1">
                                <cite title="Source Title"><?= SYSTEM_NAME.' - '.SYSTEM_NAME_PT ?></cite>
                            </footer>
						</div>
					</div>
				</div>
			</div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>
