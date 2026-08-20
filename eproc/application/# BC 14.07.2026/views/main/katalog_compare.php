<?php
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 *
 */

// $this->libsession->cekSession();
include_once("functions/string.func.php");
include_once("functions/date.func.php");
include_once("functions/default.func.php");

$this->load->model("Katalog");
$this->load->model("Katalogfoto");
$this->load->model("Katalogkategorirekanan");
$this->load->model("Katalogcompare");
$id = httpFilterRequest("id") ? httpFilterRequest("id") : '';

if ($id == '')
	redirect(base_url().'main/index/katalog');

$katalog = new Katalog;
$katalog_foto = new Katalogfoto;
$katalog_kategori_rekanan = new Katalogkategorirekanan;
$katalog_compare = new Katalogcompare;
$katalog_compare_total = new Katalogcompare;

session_start();
$arrStatement = array('A.SESSIONID' => session_id());
$katalog_compare->selectByParams($arrStatement, 0, 0);
$totalCompare = $katalog_compare_total->getCountByParams($arrStatement);

if ($totalCompare < 1)
	redirect(base_url('main/index/katalog'));

switch ($totalCompare) {
	case '1': $width = ' width="100%"'; break;
	case '2': $width = ' width="50%"'; break;
	case '3': $width = ' width="33%"'; break;
	case '4': $width = ' width="25%"'; break;
	case '5': $width = ' width="20%"'; break;
	default:
		$width = ' width="100%"';
		break;
}

if ($totalCompare > 0) {
	while($katalog_compare->nextRow())
	{
		$dataKatalogid[] 	= $katalog_compare->getField('KATALOGID') ? $katalog_compare->getField('KATALOGID') : '-';
		$dataUsernama[] 	= $katalog_compare->getField('USER_NAMA') ? $katalog_compare->getField('USER_NAMA') : '-';
		$dataNoProduk[] 	= $katalog_compare->getField('NOPRODUK') ? $katalog_compare->getField('NOPRODUK') : '-';
		$dataNamaProduk[] 	= $katalog_compare->getField('NAMAPRODUK') ? '<a style="color:#000" href="'.base_url('main/index/katalog_detail?id=').$katalog_compare->getField('KATALOGID').'">'.$katalog_compare->getField('NAMAPRODUK').'</a>' : '-';
		$dataHarga[] 		= number_format($katalog_compare->getField('HARGA'),2,',','.');
		$dataMerek[] 		= $katalog_compare->getField('MEREK') ? $katalog_compare->getField('MEREK') : '-';
		$dataMOdeltype[] 	= $katalog_compare->getField('MODELTYPE') ? $katalog_compare->getField('MODELTYPE') : '-';
		$dataDiameter[] 	= $katalog_compare->getField('DIAMETER') ? $katalog_compare->getField('DIAMETER') : '-';
		$dataPanjang[] 		= $katalog_compare->getField('PANJANG') ? $katalog_compare->getField('PANJANG') : '-';
		$dataLebar[] 		= $katalog_compare->getField('LEBAR') ? $katalog_compare->getField('LEBAR') : '-';
		$dataTinggi[] 		= $katalog_compare->getField('TINGGI') ? $katalog_compare->getField('TINGGI') : '-';
		$dataKemasan[] 		= $katalog_compare->getField('KEMASAN') ? $katalog_compare->getField('KEMASAN') : '-';
		$dataLamagaransi[] 	= $katalog_compare->getField('LAMAGARANSI').' '.$katalog_compare->getField('LAMAGARANSI2');
		$dataNoprodukpenyedia[]	= $katalog_compare->getField('NOPRODUKPENYEDIA') ? $katalog_compare->getField('NOPRODUKPENYEDIA') : '-';
		$dataUnitpengukuran[] 	= $katalog_compare->getField('UNITPENGUKURAN') ? $katalog_compare->getField('UNITPENGUKURAN') : '-';
		$dataTersedia[] 	= $katalog_compare->getField('JUMLAHSTOCK_READY') ? $katalog_compare->getField('JUMLAHSTOCK_READY') : '-';
		if ($katalog_compare->getField('NAMAPRODUK') == '1') {
			$dataJenisproduk[] 	= 'Lokal';
		} else {
			$dataJenisproduk[] 	= 'Import';
		}
		switch ($katalog_compare->getField('JUMLAHSTOCK')) {
			case '1':
				$dataJumlahstock[] = '<span class="badge round padd-badge badge-primary">Tersedia</span>';
				break;
			case '2':
				$dataJumlahstock[] = '<span class="badge round padd-badge badge-success">Pre-order</span>';
				break;
			case '3':
				$dataJumlahstock[] = '<span class="badge round padd-badge badge-danger">Hubungi Penyedia</span>';
				break;

			default:
				$dataJumlahstock[] = '';
				# code...
				break;
		}
		$dataTkdnproduk[] 		= $katalog_compare->getField('TKDNPRODUK') ? $katalog_compare->getField('TKDNPRODUK') : '-';
		$dataBerlakusampai[] 	= $katalog_compare->getField('BERLAKUSAMPAI') ? $katalog_compare->getField('BERLAKUSAMPAI') : '-';
		$dataNomortest[] 		= $katalog_compare->getField('NOMORTEST') ? $katalog_compare->getField('NOMORTEST') : '-';
	}
} else {
	$dataKatalogid 	=  array();
	$dataUsernama 	=  array();
	$dataNoProduk 	=  array();
	$dataNamaProduk 	=  array();
	$dataHarga 		=  array();
	$dataMerek 		=  array();
	$dataMOdeltype 	=  array();
	$dataDiameter 	=  array();
	$dataPanjang 		=  array();
	$dataLebar 		=  array();
	$dataTinggi 		=  array();
	$dataKemasan 		=  array();
	$dataLamagaransi 	=  array();
	$dataNoprodukpenyedia	=  array();
	$dataUnitpengukuran 	=  array();
	$dataJenisproduk =  array();
	$dataJumlahstock =  array();
	$dataTkdnproduk 		=  array();
	$dataBerlakusampai 	=  array();
	$dataNomortest 		=  array();
}
// $Katalogfoto = new Katalogfoto();
// $Katalogfoto->selectByParams(array('KATALOGID' => $katalogid), -1, -1);

?>

<link rel="stylesheet" type="text/css" href="<?=base_url() ?>assets/new/vendors/css/extensions/zoom.css">
<script src="<?=base_url()?>assets/new/vendors/js/extensions/zoom.min.js"></script>

<script type="text/javascript">
  $(document).ready(function(){
    jQuery(".compare").on('change', function () {
      var view = jQuery(this);
        var isAllow = view.data('allow');
        if (isAllow) {
          var value = $(this).data("value");
          var name = $(this).data("name");
          if ($('#compare'+value).is(":checked"))
          {
            var check = '1';
          } else {
            var check = '0';
          }
          // alert(check);
          $.post("katalog_json/compare",
          {
            name: name,
            value: value,
            check: check
          },
          function(data, status){
            location.reload();
          });
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
	.tdHead { width: 17%; background-color: #967adc; color: #fff; vertical-align: middle; }
	.tdHead2 { width: 25%; font-weight: bold }
	/*.tdContent { width: 75%; font-weight: normal }*/
	/*.tdContent2 { width: 35%; font-weight: normal }*/
	.padd-badge { padding: 6px 10px  }
</style>

<section id="backColor">
  <div class="row">
    <div class="col-md-12 col-sm-12">
      <div class="card" style="zoom: 1;">
        <div class="card-content collapse show">
          <div class="card-body border-primary" id="tbodyData">
          	<div class="row">
				<table class="table table-bordered">
					<tbody>
						<tr>
							<td class="tdHead">Gambar/Foto</td>
							<?php
							if (count($dataKatalogid) > 0) {
								// code...
								foreach ($dataKatalogid as $key => $value)
								{
				                $Katalogfoto = new Katalogfoto();
				                $Katalogfoto->selectByParams(array('KATALOGID' => $value), -1, -1);
				                $Katalogfoto->firstRow();
				                if (file_exists('images/katalog/'.$Katalogfoto->getField("path_file")) && $Katalogfoto->getField("path_file") != '') {
				                  $filenya = $Katalogfoto->getField("path_file");
				                } else {
				                  $filenya = 'katalognotfound.jpg';
				                }
							?>
							<td align="center">
                    			<img class="card-img-top img-fluid" src="images/katalog/<?= $filenya ?>" alt="eproc19.com" data-action="zoom" style="width:80px !important;height: 80px !important">
							</td>
	                      	<?php
								}
            	} ?>
						</tr>
						<tr> <td class="tdHead">Penyedia</td>
							<?php
							if (count($dataUsernama) > 0) {
								foreach ($dataUsernama as $key => $value) { echo '<td>'.$value.'</td>'; }
							}
							?>
						</tr>
						<tr> <td class="tdHead">Nomor Produk</td>
							<?php
							if (count($dataNoProduk) > 0) {
								foreach ($dataNoProduk as $key => $value) { echo '<td>'.$value.'</td>'; }
							}
							?>
						</tr>
						<tr> <td class="tdHead">Nama Produk</td>
							<?php
							if (count($dataNamaProduk) > 0) {
								foreach ($dataNamaProduk as $key => $value) {
										echo '<td>'.$value.'</td>';
									  }
							}
						  ?>
						</tr>
						<tr> <td class="tdHead">Harga</td>
							<?php
							if (count($dataHarga) > 0) {
								foreach ($dataHarga as $key => $value) { echo '<td>'.$value.'</td>'; }
							}
							?>
						</tr>
						<tr> <td class="tdHead">Merek</td>
							<?php
							if (count($dataMerek) > 0) {
								foreach ($dataMerek as $key => $value) { echo '<td>'.$value.'</td>'; }
							}
							?>
						</tr>
						<tr> <td class="tdHead">Model/Type</td>
							<?php
							if (count($dataMOdeltype) > 0) {
								foreach ($dataMOdeltype as $key => $value) { echo '<td>'.$value.'</td>'; }
							}
							?>
						</tr>
						<tr> <td class="tdHead">Diameter</td>
							<?php
							if (count($dataDiameter) > 0) {
								foreach ($dataDiameter as $key => $value) { echo '<td>'.$value.'</td>'; }
							}
							?>
						</tr>
						<tr> <td class="tdHead">Panjang</td>
							<?php
							if (count($dataPanjang) > 0) {
								foreach ($dataPanjang as $key => $value) { echo '<td>'.$value.'</td>'; }
							}
							?>
						</tr>
						<tr> <td class="tdHead">Lebar</td>
							<?php
							if (count($dataLebar) > 0) {
								foreach ($dataLebar as $key => $value) { echo '<td>'.$value.'</td>'; }
							}
							?>
						</tr>
						<tr> <td class="tdHead">Tinggi</td>
							<?php
							if (count($dataTinggi) > 0) {
								foreach ($dataTinggi as $key => $value) { echo '<td>'.$value.'</td>'; }
							}
							?>
						</tr>
						<tr> <td class="tdHead">Tahun Pembuatan Produk</td>
							<?php
							if (count($dataUnitpengukuran) > 0) {
								foreach ($dataUnitpengukuran as $key => $value) { echo '<td>'.$value.'</td>'; }
							}
							?>
						</tr>
						<tr> <td class="tdHead">TKDN(%)</td>
							<?php
							if (count($dataTkdnproduk) > 0) {
								foreach ($dataTkdnproduk as $key => $value) { echo '<td>'.$value.'</td>'; }
							}
							?>
						</tr>
						<tr> <td class="tdHead">Masa Berlaku</td>
							<?php
							if (count($dataBerlakusampai) > 0) {
								foreach ($dataBerlakusampai as $key => $value) {
									$tgl = explode(' ',$value);
									echo '<td>'.$tgl[0].'</td>';
								}
							}
							?>
						</tr>
						<tr> <td class="tdHead">Jenis Produk</td>
							<?php
							if (count($dataJenisproduk) > 0) {
								foreach ($dataJenisproduk as $key => $value) { echo '<td>'.$value.'</td>'; }
							}
							?>
						</tr>
						<tr> <td class="tdHead">Garansi</td>
							<?php
							if (count($dataLamagaransi) > 0) {
								foreach ($dataLamagaransi as $key => $value) { echo '<td>'.$value.'</td>'; }
							}
							?>
						</tr>
						<tr> <td class="tdHead">Jumlah Stok</td>
							<?php
							if (count($dataJumlahstock) > 0) {
								foreach ($dataJumlahstock as $key => $value) { echo '<td>'.$value.'</td>'; }
							}
							?>
						</tr>
						<tr> <td class="tdHead">Waktu Pengiriman (hari)</td>
							<?php
							if (count($dataTersedia) > 0) {
								foreach ($dataTersedia as $key => $value) { echo '<td>'.$value.'</td>'; }
							}
							?>
						</tr>
						<tr> <td class="tdHead">Kemasan</td>
							<?php
							if (count($dataKemasan) > 0) {
								foreach ($dataKemasan as $key => $value) { echo '<td>'.$value.'</td>'; }
							}
							?>
						</tr>
						<!-- <tr> <td class="tdHead">No. Produk Penyedia</td> -->
							<?php
							// if (count($dataNoprodukpenyedia) > 0) {
							// 	foreach ($dataNoprodukpenyedia as $key => $value) { echo '<td>'.$value.'</td>'; }
							// }
							?>
						<!-- </tr> -->
						<!-- <tr> <td class="tdHead">No. Test Report</td> -->
							<?php
							// if (count($dataNomortest) > 0) {
							// 	foreach ($dataNomortest as $key => $value) { echo '<td>'.$value.'</td>'; }
							// }
							?>
						<!-- </tr> -->
						<tr>
							<td class="tdHead"></td>
							<?php
							if (count($dataKatalogid) > 0) {
								foreach ($dataKatalogid as $key => $value)
								{ ?>
							<td align="center">
								<fieldset class="checkboxsas btn btn-danger btn-sm" id="btnfull">
		                          <label>
		                            <?php
		                            session_start();
		                            $Katalogcompare = new Katalogcompare();
		                            $cekCompareSession = $Katalogcompare->getCountByParams(array('KATALOGID' => $value, 'SESSIONID' => session_id()));
		                            if ($cekCompareSession > 0 ) {
		                              $checkProduk = ' checked';
		                            } else {
		                              $checkProduk = '';
		                            }
		                             ?>
		                            <input type="checkbox" class="cursorPoin compare" data-allow="true" id="compare<?= $value ?>" data-value="<?= $value ?>" data-name="<?= $dataNoProduk[$key] ?>" <?= $checkProduk ?>> Bandingkan
		                          </label>
		                      	</fieldset>
							</td>
	                      	<?php
                	}
              	} ?>
						</tr>
					</tbody>
				</table>
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
</section>
