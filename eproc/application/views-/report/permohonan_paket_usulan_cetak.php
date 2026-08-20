<?php
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 * 
 */
$this->load->model("Paket");
$this->load->model("PermohonanPaket");
include_once("functions/string.func.php");
include_once("functions/date.func.php");
include_once("lib/phpqrcode/qrlib.php");

header("Content-type: application/vnd-ms-excel");
header("Content-Disposition: attachment; filename=permohonan-paket-usulan.xls");

$reqStatus = $this->input->get("reqStatus");
$reqMode = $this->input->get("reqMode");


$arrStatement = array("A.CREATED_BY" => $this->USER_LOGIN_ID);	 
$statement = " AND (A.APPROVAL != '1' OR A.APPROVAL IS NULL)";

$permohonan_paket = new PermohonanPaket();
$permohonan_paket->selectByParamsUsulan($arrStatement, $dsplyRange, $dsplyStart, $statement);
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
        	USULAN KEBUTUHAN <?= SYSTEM_NAME_PT ?> <br>
            user : <?= $this->USER_NAMA ?>
        </div>
        <br>
        <div class="data-laporan">
       		<table id="example" class="display" cellspacing="0" width="100%" border="1">
              <thead>
                   <tr>
                   		<th>No.</th>
                        <th>Tahun Anggaran</th>    
                        <th>Nama Paket</th>
                        <th>Produk Dalam Negeri</th>
                        <!-- <th>Jenis Belanja</th> -->
                        <!-- <th>Sumber Dana</th> -->
                        <th>RAB / Harga Perkiraan</th>
                        <th>Rencana Pengadaan</th>    
                        <th>Waktu Penggunaan</th>
                        <!-- <th>Identifikasi Resiko</th> -->
                        <!-- <th>Anggaran</th> -->
                        <!-- <th>Nama Paket</th> -->
                        <th>Cara Pengadaan</th>    
                        <th>Jenis Barang Jasa</th>
                        <th>Status</th>
                        <th>Catatan / Keterangan Tambahan</th>
                    </tr>         
                </thead>
                <tbody>
			 <?php
			 	$number = 1;
				while($permohonan_paket->nextRow())
				{
             ?>
             	<tr>
                	<td align="center"><?=$number?></td>
                    <td align="center"><?=$permohonan_paket->getField("TAHUN_ANGGARAN")?></td>
                    <td><?=$permohonan_paket->getField("NAMA")?></td>
                    <td align="center"><?=$permohonan_paket->getField("KATEGORI_STR")?></td>
                    <!-- <td align="center"><?php //$permohonan_paket->getField("JENIS_BELANJA_STR")?></td> -->
                    <!-- <td> -->
                        <?php 
                        // if ($permohonan_paket->getField("AK_NAMA") == 'External') {
                        //     echo $permohonan_paket->getField("AK_NAMA").'<br>'.$permohonan_paket->getField("SUMBER_DANA_KETERANGAN");
                        // } else {
                        //     echo $permohonan_paket->getField("AK_NAMA");
                        // }
                        ?>
                            
                    <!-- </td> -->
                    <td><?=numberToIna($permohonan_paket->getField("PERKIRAAN_BIAYA_HARGA"))?></td>
                    <td align="center"><?= getFormattedDateShort3($permohonan_paket->getField("RENCANA_PENGADAAN"))?></td>
                    <td align="center"><?= getFormattedDateShort3($permohonan_paket->getField("WAKTU_PENGGUNA_BARANGJASA"))?></td>
                    <!-- <td> -->
                        <?php 
                        // if($permohonan_paket->getField("IDENTIFIKASI_RESIKO") == '1') {
                        //     echo "Ya <br>".$permohonan_paket->getField("IDENTIFIKASI_RESIKO_KETERANGAN");
                        // } else {
                        //     echo "-";
                        // } 
                        ?>
                    <!-- </td> -->
                    <!-- <td><?php //$permohonan_paket->getField("ANGGARAN")?></td> -->
                    <!-- <td><?php // $permohonan_paket->getField("NAMA")?></td> -->
                    <td align="center"><?=$permohonan_paket->getField("CARA_PENGADAAN_STR")?></td>
                    <td align="center"><?=$permohonan_paket->getField("JENIS_BARANG_JASA_STR")?></td>
                    <td align="center"><?=$permohonan_paket->getField("STATUS")?></td>
                    <td><?=$permohonan_paket->getField("NOTE")?></td>
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