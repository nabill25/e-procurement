<?php
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 * 
 */
$this->load->model("Paket");


include_once("functions/string.func.php");
include_once("functions/default.func.php");
include_once("functions/date.func.php");

$paket = new Paket();
$reqTahun= $this->input->get("reqTahun");
$reqMetode= $this->input->get("reqMetode");

if ($reqMetode == 'tender')
    $paket_metode = '1,3,7,10';
else
    $paket_metode = '2,4,5,8';

if($reqTahun == "")
	$reqTahun = date("Y");

$aColumns = array("TAHUN_ANGGARAN","KODE_RUP","KODE_PR", "NAMA_PEKERJAAN", "LOKASI", "JENIS_PEKERJAAN", "METODE_PEKERJAAN", "SISTEM_SAMPUL", "METODE_EVALUASI", "KUALIFIKASI_USAHA", "SISTEM_NEGOSIASI", "NILAI_OE", "PENGGUNA", "PIC_PAKET");

$statement = " AND J.TAHUN_ANGGARAN = '".$reqTahun."' AND A.PAKET_METODE_LELANG_ID IN (".$paket_metode.") ";
$paket->selectByParamsPaketPekerjaanLaporan(array(), $dsplyRange, $dsplyStart, $statement.$searchJson, $sOrder);

header("Content-type: application/vnd-ms-excel");
header("Content-Disposition: attachment; filename=Rekapitulasi_Pekerjaan_".$reqTahun.".xls");

?>
<!doctype html>
<html>
	<head>
		<meta charset="utf-8">
		<base href="" />
        
	</head>
	<body>
    	<div class="kop-laporan">
            <div class="info">
                <div class="judul-laporan">REKAPITULASI PEKERJAAN <?=$reqTahun?> </div>
            </div>
        </div>
        <div class="data-laporan">
		<table border="1">
			<tr class="header">
				<td>No.</td>
				<td>Tahun Anggaran</td>
				<td>Kode RUP</td>
				<td>Kode PR</td>
				<td>Nama Pekerjaan</td>
				<td>Lokasi Pekerjaan</td>
				<td>Jenis Pengadaan</td>
				<td>Metode Pengadaan</td>
				<td>Metode Penyampaian Penawaran</td>
				<td>Metode Evaluasi</td>
				<td>Kualifikasi Usaha</td>
				<td>Sistem Negosiasi</td>
				<td>Harga Perkiraan</td>
				<td>Pengguna</td>
				<td>PIC</td>
              </tr>
			  <?php
              	$no=1;
				while($paket->nextRow())
				{
					?>
                    <tr>
                    	<td><?= $no ?></td>
                    <?php
					for ( $i=0 ; $i<count($aColumns) ; $i++ )
					{
						if($aColumns[$i] == "BULAN")
							$row = getNameMonth((int)$paket->getField($aColumns[$i]));				
						else if(substr($aColumns[$i], 0,5) == "NILAI")
							$row = numberToIna($paket->getField($aColumns[$i]));			
						else if(substr($aColumns[$i], 0,5) == "TANGGAL_PPA")
							$row = getFormattedDateJson($paket->getField($aColumns[$i]));
						else if($aColumns[$i] == "PERSEN_OE")
							$row = str_replace(".", ",", $paket->getField($aColumns[$i]));	
						else if($aColumns[$i] == "SISTEM_SAMPUL")
							$row = $paket->getField($aColumns[$i]).' File';					
						else
							$row = $paket->getField($aColumns[$i]);
						
						?>
							<td><?=$row?></td>
						<?php
					}
					?>
                    </tr>
                    <?php
              	$no++;
				}
					  
			  ?>      
		</table>
        </div>
	</body>
</html>