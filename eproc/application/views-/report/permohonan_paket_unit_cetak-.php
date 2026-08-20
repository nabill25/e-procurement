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
header("Content-Disposition: attachment; filename=paket_permohonan_pekerjaan.xls");

$reqStatus = $this->input->get("reqStatusRencanaPengadaan");
$reqMode = $this->input->get("reqMode");

if($reqMode == "unit")
{
	$arrStatement = array("A.USER_LOGIN_ID" => $this->USER_LOGIN_ID);
	if($reqStatus == '')
	{}
	elseif($reqStatus == '0')
		$statement .= " AND A.POSTING IS NULL ";
	elseif($reqStatus == '1')
		$statement .= " AND D.PAKET_ID IS NULL AND A.POSTING IS NOT NULL ";
	elseif($reqStatus == '2')
		$statement .= " AND D.PAKET_ID IS NOT NULL AND A.POSTING IS NOT NULL ";
}
elseif($reqMode == "exec")
{
	$arrStatement = array("A.UNIT_KERJA_ID" => $this->UNIT_KERJA_ID);
	$statement .= " AND A.POSTING IS NOT NULL";
	if($reqStatus == '1')
		$statement .= "  AND PIC IS NULL ";
	elseif($reqStatus == '2')
		$statement .= " AND PIC IS NOT NULL ";
}
elseif($reqMode == "panitia")
{
	$arrStatement = array("A.UNIT_KERJA_ID" => $this->UNIT_KERJA_ID);
	if($reqStatus == '')
		{}
		elseif($reqStatus == '0')
			$statement .= " AND A.POSTING IS NOT NULL ";
		elseif($reqStatus == '1')
			$statement .= " AND D.PAKET_ID IS NULL AND A.POSTING IS NOT NULL ";
		elseif($reqStatus == '2')
			$statement .= " AND D.PAKET_ID IS NOT NULL AND A.POSTING IS NOT NULL ";

		//$statement  .= " AND A.UNIT_KERJA_ID = '".$this->UNIT_KERJA_ID."' ";
		$statement .= " AND PIC = '".$this->NIP."'";
}

	$permohonan_paket = new PermohonanPaket();
	$permohonan_paket->selectByParamsUsulanView($arrStatement, $dsplyRange, $dsplyStart, $statement);

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
        	DAFTAR PERMOHONAN PAKET PEKERJAAN <?= SYSTEM_NAME_PT ?>
        </div>
        <br>
        <div class="data-laporan">
       		<table id="example" class="display" cellspacing="0" width="100%" border="1">
              <thead>
                   <tr>
                   		<th width="90px">No.</th>
                        <!-- <th width="90px">No. SiRUP</th> -->
                        <th width="90px">Tahun Anggaran</th>
                        <th width="90px">No. Nota</th>
                        <th width="90px">Tanggal Nota</th>
                        <th width="120px">Nama Paket</th>
                        <th width="90px">Nilai HPS</th>
                        <!-- <th width="90px">Budget Awal</th>
                        <th width="90px">Budget Terpakai</th>
                        <th width="90px">Budget Akhir</th> -->
                        <!-- <th width="150px">Keterangan</th> -->
                        <!-- <th width="200px">Unit Kerja</th>     -->
                        <!-- <th width="90px">Fungsional</th>      -->
                        <!-- <th width="90px">Divisi</th> -->
                        <th width="90px">PIC</th>
                    </tr>
                </thead>
                <tbody>
			 <?php
			 	$number = 1;
				while($permohonan_paket->nextRow())
				{

             ?>
             	<tr style="background-color:#20c997">
              	<td align="center"><?=$number?></td>
                  <!-- <td align="center"><?=$permohonan_paket->getField("NOTA_DINAS")?></td> -->
                  <td align="center"><?=$permohonan_paket->getField("TAHUN_ANGGARAN")?></td>
                  <td align="center"><?=$permohonan_paket->getField("NO_PPA")?></td>
                  <td align="center"><?=getFormattedDateJson($permohonan_paket->getField("TANGGAL"))?></td>
                  <td align="center"><?=$permohonan_paket->getField("NAMA")?></td>
                  <td align="center"><?=numberToIna($permohonan_paket->getField("NILAI"))?></td>
                  <!-- <td align="center"><?=numberToIna($permohonan_paket->getField("BUDGET_AWAL"))?></td> -->
                  <!-- <td align="center"><?=numberToIna($permohonan_paket->getField("BUDGET_TERPAKAI"))?></td> -->
                  <!-- <td align="center"><?=numberToIna($permohonan_paket->getField("BUDGET_AKHIR"))?></td> -->
                  <!-- <td align="center"><?=$permohonan_paket->getField("KETERANGAN")?></td> -->
                  <!-- <td align="center"><?=$permohonan_paket->getField("UNIT_KERJA")?></td> -->
                  <!-- <td align="center"><?=$permohonan_paket->getField("USER_LOGIN")?></td> -->
                  <!-- <td align="center"><?=$permohonan_paket->getField("DEPARTEMEN")?></td> -->
                  <td align="center"><?=$permohonan_paket->getField("NAMA_PIC")?></td>
              </tr>
							<?php
							$permohonan_paket_coa = new PermohonanPaket();
							$noRand = 2021;
							$permohonan_paket_coa->selectByParamsCoa(array("A.PERMOHONAN_PAKET_ID" => coalesce($permohonan_paket->getField("PERMOHONAN_PAKET_ID"), 0)));
							if ($permohonan_paket_coa->countRow() > 0) {
							?>
							<thead>
                 <tr>
									 <th></th>
                      <th align="center">Nomor COA</th>
                      <th align="center">Keterangan</th>
											<th align="center">Anggaran Awal</th>
											<th align="center">Anggaran Terpakai</th>
                      <th align="center">Sisa Anggaran</th>
                  </tr>
              </thead>
								<?php
								while($permohonan_paket_coa->nextRow())
								{ ?>
								<tbody>
									<tr>
										<td align="center"></td>
										<td align="center"><?= $permohonan_paket_coa->getField("NOMOR") ?></td>
										<td align="center"><?= $permohonan_paket_coa->getField("KETERANGAN") ?></td>
										<td align="center"><?= numberToIna($permohonan_paket_coa->getField("BUDGET_AWAL")) ?></td>
										<td align="center"><?= numberToIna($permohonan_paket_coa->getField("BUDGET_TERPAKAI")) ?></td>
										<td align="center"><?= numberToIna($permohonan_paket_coa->getField("BUDGET_AKHIR")) ?></td>
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
