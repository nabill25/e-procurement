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
header("Content-Disposition: attachment; filename=daftar-penyedia-".date('YmdHis').".xls");

$user_login = new Users();
$rekanan = new Rekanan();
$rekanan->selectByParams(array(), -1, -1, " AND (COALESCE(STATUS_VALIDASI, 0) = 0 OR COALESCE(STATUS_VALIDASI, 0) = 10 OR COALESCE(STATUS_VALIDASI, 0) = 4)  AND TANGGAL_HAPUS IS NULL ");

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
        	<b>DAFTAR PENYEDIA <?= SYSTEM_NAME_PT ?></b> <br>
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
                        <th>Status</th>    
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
                    <td align="center">
                        <?php 
                            switch ($rekanan->getField('STATUS_VALIDASI')) {
                            // 0=Belum 1=Validasi 2=Hapus 3=Kirim ke Rekomendator, 4=Kirim ke Validator
                            case '0':
                                $user_login->selectByParams(array("REKANAN_ID"=>$rekanan->getField('REKANAN_ID')),-1,-1);
                                $user_login->firstRow();
                                if ($user_login->getField('USER_STATUS') == '2') {
                                    echo '<span style="color:blue">Sudah Kirim Berkas</span>';
                                } else {
                                    echo '<span style="color:red">Melengkapi Berkas</span>';
                                }
                                break;
                            case '10':
                                $user_login->selectByParams(array("REKANAN_ID"=>$rekanan->getField('REKANAN_ID')),-1,-1);
                                $user_login->firstRow();
                                if ($user_login->getField('USER_STATUS') == '2') {
                                    echo '<span style="color:yellow">Berkasi dikembalikan</span>';
                                } else {
                                    echo '<span style="color:red">Melengkapi Berkas</span>';
                                }
                                break;
                            case '1':
                                echo 'Terverifikasi';
                                break;
                            case '3':
                                echo 'Menunggu Approval Penyelia';
                                break;
                            case '4':
                                echo 'Menunggu Aprroval VMS';
                                break;
                            // default: // default 0
                            //  break;
                        } ?>
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