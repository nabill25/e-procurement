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
header("Content-Disposition: attachment; filename=rencana_pengadaan.xls");

$reqStatus = $this->input->get("reqStatusRencanaPengadaan");
$reqMode = $this->input->get("reqMode");

if($reqMode == "unit") // Unit Instalasi
{
	$arrStatement = array("A.APPROVAL" => 1, "A.CREATED_BY" => $this->USER_LOGIN_ID);
	if($reqStatus == '')
	{}
	elseif($reqStatus == '0')
		$statement .= " AND A.POSTING_PERMOHONAN IS NULL ";
	elseif($reqStatus == '1')
		$statement .= " AND A.PAKET_ID IS NULL AND A.POSTING_PERMOHONAN IS NOT NULL ";
	elseif($reqStatus == '2')
		$statement .= " AND A.PAKET_ID IS NOT NULL AND A.POSTING_PERMOHONAN IS NOT NULL ";
}
elseif($reqMode == "vppengadaan") // Kepala Pengadaan
{
	$arrStatement = array("A.UNIT_KERJA_ID" => $this->UNIT_KERJA_ID, "A.VP_PENGADAAN" => $this->USER_LOGIN_ID);
	$statement .= " AND A.POSTING_PERMOHONAN IS NOT NULL";
	if($reqStatus == '1')
		$statement .= "  AND PIC IS NULL ";
	elseif($reqStatus == '2')
		$statement .= " AND PIC IS NOT NULL ";
}
elseif($reqMode == "ppkom") // PPKom
{
	$arrStatement = array("A.APPROVAL" => 1, "A.UNIT_KERJA_ID" => $this->UNIT_KERJA_ID, "A.USER_LOGIN_ID" => $this->USER_LOGIN_ID);
	 
	if($reqStatus == '')
	{}
	elseif($reqStatus == '0')
		$statement .= " AND A.POSTING_PERMOHONAN IS NULL ";
	elseif($reqStatus == '1')
		$statement .= " AND A.PAKET_ID IS NULL AND A.POSTING_PERMOHONAN IS NOT NULL ";
	elseif($reqStatus == '2')
		$statement .= " AND A.PAKET_ID IS NOT NULL AND A.POSTING_PERMOHONAN IS NOT NULL ";
}
elseif($reqMode == "adminrup") // ADMIN RUP
{
	$arrStatement = array("A.APPROVAL" => 1, "A.UNIT_KERJA_ID" => $this->UNIT_KERJA_ID); 
	$statement .= '';
}
elseif($reqMode == "panitia")
{
	$arrStatement = array("A.UNIT_KERJA_ID" => $this->UNIT_KERJA_ID);
	if($reqStatus == '')
	{}
	elseif($reqStatus == '0')
		$statement .= " AND A.POSTING_PERMOHONAN IS NOT NULL ";
	elseif($reqStatus == '1')
		$statement .= " AND A.PAKET_ID IS NULL AND A.POSTING_PERMOHONAN IS NOT NULL ";
	elseif($reqStatus == '2')
		$statement .= " AND A.PAKET_ID IS NOT NULL AND A.POSTING_PERMOHONAN IS NOT NULL ";

		//$statement  .= " AND A.UNIT_KERJA_ID = '".$this->UNIT_KERJA_ID."' ";
		$statement .= " AND PIC = '".$this->NIP."'";
}

$permohonan_paket = new PermohonanPaket();
$permohonan_paket->selectByParamsUsulanView($arrStatement, $dsplyRange, $dsplyStart, $statement);
// echo $permohonan_paket->query; die;
// die;
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
        	RENCANA PENGADAAN <?= SYSTEM_NAME_PT ?> <br>
            user : <?= $this->USER_NAMA ?>
        </div>
        <br>
        <div class="data-laporan">
       		<table id="example" class="display" cellspacing="0" width="100%" border="1">
              <thead>
                   <tr>
                   		<th>No.</th>
                        <th>Tahun Anggaran</th>    
                        <th>Nama Kebutuhan</th>
                        <th>Kategori</th>
                        <th>Jenis Belanja</th>
                        <th>Analisa Kebutuhan</th>
                        <th>Perkiraan Biaya</th>
                        <th>Waktu Penggunaan</th>
                        <th>Rencana Pengadaan</th>    
                        <th>Identifikasi Resiko</th>
                        <th>Anggaran</th>
                        <th>No. Nota</th>
                        <th>Tanggal Nota</th>
                        <th>Nama Paket</th>
                        <th>Nilai HPS</th>
                        <th>Cara Pengadaan</th>    
                        <th>Jenis Barang Jasa</th>
                        <!-- <th>Status</th> -->
                        <!-- <th>Catatan / Keterangan Tambahan</th> -->
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
                    <td><?=$permohonan_paket->getField("NAMA_KEBUTUHAN")?></td>
                    <td align="center"><?=$permohonan_paket->getField("KATEGORI_STR")?></td>
                    <td align="center"><?=$permohonan_paket->getField("JENIS_BELANJA_STR")?></td>
                    <td><?=$permohonan_paket->getField("AK_NAMA")?></td>
                    <td><?=numberToIna($permohonan_paket->getField("PERKIRAAN_BIAYA_HARGA"))?></td>
                    <td><?=getFormattedDateJson($permohonan_paket->getField("WAKTU_PENGGUNA_BARANGJASA"))?></td>
                    <td align="center"><?=getFormattedDateJson($permohonan_paket->getField("RENCANA_PENGADAAN"))?></td>
                    <td>
                        <?php if($permohonan_paket->getField("IDENTIFIKASI_RESIKO") == '1') {
                            echo "Ya <br>".$permohonan_paket->getField("IDENTIFIKASI_RESIKO_KETERANGAN");
                        } else {
                            echo "-";
                        } ?>
                    </td>
                    <td><?=$permohonan_paket->getField("ANGGARAN")?></td>
                    <td><?= $permohonan_paket->getField("NO_PPA") ?: '-'?></td>
                    <td><?=getFormattedDate($permohonan_paket->getField("TANGGAL"))?></td>
                    <td><?=$permohonan_paket->getField("NAMA")?></td>
                    <td><?=numberToIna($permohonan_paket->getField("NILAI"))?></td>
                    <td align="center"><?=$permohonan_paket->getField("CARA_PENGADAAN_STR")?></td>
                    <td align="center"><?=$permohonan_paket->getField("JENIS_BARANG_JASA_STR")?></td>
                    <!-- <td align="center"><?=$permohonan_paket->getField("STATUS")?></td> -->
                    <!-- <td><?=$permohonan_paket->getField("NOTE")?></td> -->
                </tr>
                <?php
			$permohonan_paket_coa = new PermohonanPaket();
			$noRand = 2021;
			$permohonan_paket_coa->selectByParamsCoa(array("A.PERMOHONAN_PAKET_ID" => coalesce($permohonan_paket->getField("PERMOHONAN_PAKET_ID"), 0)));
			if ($permohonan_paket_coa->countRow() > 0) {
			?>
				<thead>
	                 <tr>
	                    <th colspan="2">Nomor COA</th>
	                    <th colspan="9">Keterangan</th>
					<th align="center" colspan="2">Anggaran Awal</th>
					<th align="center" colspan="2">Anggaran Terpakai</th>
	                    <th align="center" colspan="2">Sisa Anggaran</th>
	                  </tr>
	              </thead>
				<?php
				while($permohonan_paket_coa->nextRow())
				{ ?>
				<tbody>
					<tr> 
						<td colspan="2"><?= $permohonan_paket_coa->getField("NOMOR") ?></td>
						<td colspan="9"><?= $permohonan_paket_coa->getField("KETERANGAN") ?></td>
						<td align="center" colspan="2"><?= numberToIna($permohonan_paket_coa->getField("BUDGET_AWAL")) ?></td>
						<td align="center" colspan="2"><?= numberToIna($permohonan_paket_coa->getField("BUDGET_TERPAKAI")) ?></td>
						<td align="center" colspan="2"><?= numberToIna($permohonan_paket_coa->getField("BUDGET_AKHIR")) ?></td>
				 	</tr>
				</tbody>

				<?php
				$noRand++;
				}
			}?>
			<?php
	           $number++;
			}
            ?>
             </tbody>
            </table>
        </div>
	</body>
</html>
