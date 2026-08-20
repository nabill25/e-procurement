<?php
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 * 
 */
$this->load->model("Rekanan");
$this->load->model("Users");
include_once("functions/string.func.php");
include_once("functions/date.func.php");
include_once("lib/phpqrcode/qrlib.php");

$reqStatus = $this->input->get("reqStatus");

header("Content-type: application/vnd-ms-excel");
header("Content-Disposition: attachment; filename=daftar-penyedia-terverifikasi-".date('YmdHis').".xls");

$user_login = new Users();
$rekanan = new Rekanan();
$rekanan->selectByParams2(array(), -1, -1, " AND (COALESCE(STATUS_VALIDASI, 0) = 1)  AND TANGGAL_HAPUS IS NULL ");

?>
<!doctype html>
<html>
	<head>
		<meta charset="utf-8">
		<base href="<?=base_url()?>" />
	</head>
	<body>
    <div class="kop-laporan">
            <div class="info">
                
            </div>
        </div>
        <div class="isi" align="center">
        	<b>DAFTAR PENYEDIA TERVERIFIKASI <?= SYSTEM_NAME_PT ?></b> <br>
            <?= SYSTEM_ALAMAT_PT ?> <br>
            Tanggal cetak: <?= getFormattedDateJson(date('Y-m-d')) ?>
        </div>
        <br>
        <div class="data-laporan">
       		<table id="example" class="display" cellspacing="0" width="100%" border="1">
              <thead>
                   <tr>
                   		<th>No.</th>
                        <th>No. Registrasi</th>    
                        <th>Nama</th>    
                        <th>Kota</th>    
                        <th>Tanggal Daftar</th>    
                        <th>Tanggal Approve</th>    
                        <th>Status Validasi</th>    
                        <th>Status Aktif</th>    
                    </tr>         
                </thead>
                <tbody>
			 <?php
			 	$number = 1;
				while($rekanan->nextRow())
				{
             ?>
             	<tr>
                	<td align="center"><?=$number?></td> 
                    <td><?=$rekanan->getField("KODE")?></td>
                    <td><?=$rekanan->getField("NAMA")?></td>
                    <td><?=$rekanan->getField("KOTA")?></td>
                    <td><?= getFormattedDateJson($rekanan->getField("TANGGAL_DAFTAR"))?></td>
                    <td><?= getFormattedDateJson($rekanan->getField("TANGGAL_VALIDASI"))?></td>
                    <td align="center">
                        <?php 
                        if ($rekanan->getField("USER_STATUS") == '1') {
                             echo 'Ya';
                        } else {
                             echo 'Tidak';
                        } 
                        ?>
                    </td>
                    <td align="center">
                        <?php 
                        if ($rekanan->getField("USER_AKTIF") == '1') {
                             echo 'Ya';
                        } else {
                             echo 'Tidak';
                        } 
                        ?>
                    </td>
                </tr>
			<?php
	           $number++;
			}
            ?>
             </tbody>
            </table>
        </div>
	</body>
</html>