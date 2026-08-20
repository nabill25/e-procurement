<?php
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 *
 */

$this->libsession->cekSession();

$this->load->model("UserLogin");
$this->load->model("RekananTenagaAhliSertifikat");
$user_login = new UserLogin();
$adaKelengkapanData = $user_login->getCountByParams(array("USER_LOGIN_ID"=> $this->USER_LOGIN_ID, "USER_STATUS|| IN " => "(0,2)"));

/* INCLUDE FILE */
$this->load->library("kauth");  $userLogin = new kauth();
$this->load->model("RekananTenagaAhli");
$this->load->model("RekananTenagaAhliPengalaman");
include_once("functions/string.func.php");
include_once("functions/date.func.php");
include_once("functions/default.func.php");

$reqId = $this->input->get("reqId");
$rekanan_tenaga_ahli = new RekananTenagaAhli();

$rtap = new RekananTenagaAhliPengalaman();
$allRecord = $rekanan_tenaga_ahli->getCountByParams(array('REKANAN_ID'=>$this->ID), $statement);
$rs = $rekanan_tenaga_ahli->selectByParams(array('REKANAN_ID'=>$this->ID), -1, -1, $statement);
// echo "<pre>"; print_r($rs); die();
?>
<script language="JavaScript" src="jslib/elementDis.js"></script>
<style type="text/css">
	.judul-kolom2 { background: #019966; color: #fff; }
</style>
<div class="row">
  <div class="col-md-12 col-sm-12">
    <div class="card">
      <div class="card-header card-head-inverse bg-primary">
        <h4 class="card-title text-white"><?=translate("Tenaga Ahli Tetap", "Experts")?>
        	<?php
					$arrStatusValidasi = array('0','10');
					if (array_intersect($this->libsession->cekStatusValidasiRekanan(), $arrStatusValidasi)) {
						$rekanan = new Rekanan();
						$rekanan->selectByParams(array("A.REKANAN_ID"=>$this->ID),-1,-1);
						$rekanan->firstRow();
						$reqStatusValidasi= $rekanan->getField("STATUS_VALIDASI");

						$userRekanan = new Userlogin();
						$userRekanan->selectByParams(array("REKANAN_ID"=>$this->ID),-1,-1);
						$userRekanan->firstRow();
						$reqStatusUser= $userRekanan->getField("USER_STATUS");

						if ($this->libsession->cekChecklist('tenaga_ahli') || ($reqStatusUser == '0' && $reqStatusValidasi == '1')) // Check Checlist Verifikator
						{
					 		if ($this->libsession->cekUrl($this->uri->segment(3, ""))) { ?>
	            <div class="badge badge-pill badge-warning">
	            	<a href="main/index/data_teknis_tenaga_ahli_tambah" data-toogle=""><span class="fa fa-plus text-white icon-line-height" data-toggle="tooltip" data-placement="top" title="Tambah Data"></span> Tambah</a>
	        	</div>
	        <?php
							}
	         	}
	        } ?>
        </h4>
        <a class="heading-elements-toggle"><i class="fa fa-ellipsis-v font-medium-3"></i></a>
        <div class="heading-elements">
            <ul class="list-inline mb-0">
              <li><a data-action="expand"><i class="ft-maximize"></i></a></li>
              <!-- <li><a data-action="close"><i class="ft-x"></i></a></li> -->
            </ul>
        </div>
      </div>
      <div class="card-content collapse show border-info border-darken-2">
        <div class="card-body">

					<?php if (array_intersect($this->libsession->cekStatusValidasiRekanan(), $arrStatusValidasi)) { } else { echo $this->libsession->cekStatusValidasiRekananStr();  } ?>

        	<div class="table-responsive">

	          <table class="table table-bordered mb-0">
	          	<tbody>
	                <tr class="judul-kolom">
		                <th width="1%">No</th>
		                <th><?=translate("Nama", "Full Name")?></th>
										<th>No. KTP</th>
										<th>No. NPWP</th>
										<th>Tempat Lahir</th>
										<th>Tanggal Lahir</th>
										<th>Alamat</th>
		                <?php
										if (array_intersect($this->libsession->cekStatusValidasiRekanan(), $arrStatusValidasi)) {
											if ($this->libsession->cekChecklist('tenaga_ahli') || ($reqStatusUser == '0' && $reqStatusValidasi == '1')) // Check Checlist Verifikator
											{ if ($this->libsession->cekUrl($this->uri->segment(3, ""))) { ?>
		                <th class="text-center" width="9%" class="kolom-aksi" width="2%">Aksi</th>
		                <?php
		                		}
											}
		                } ?>
	                </tr>
	            	<tr>
		            	<?php
		            	if ($rekanan_tenaga_ahli->countRow() == 0 ) {
		            		echo '<tr><td colspan="4">. : : Data tidak ada : : .</td></tr>';
		            	} else
		            	{
							$no=1;
							$style="gelap";
							while($rekanan_tenaga_ahli->nextRow())
							{
						?>
						  <tr class="<?=$style?>">
							<td align="center" valign="top" width="2%"><?=$no?></td>
							<td valign="top">
							<a class="taut kolom-buka-show-hide" onclick="displayElement('reqDetil<?=$no?>')" style="cursor:pointer" id="rekanan<?=$i?>">
								<?=$rekanan_tenaga_ahli->getField("NAMA")?>
								<?php
								if ($rekanan_tenaga_ahli->getField("JENIS_KELAMIN") == 'L') {
									echo '<sup>Laki-Laki</sup>';
								} else {
									echo '<sup>Perempuan</sup>';
								}
							 ?> <br>
							 <span class="badge badge-primary"> <small>Lihat detil</small></span>
							</a>
							</td>
							<td valign="top" >
								<?= $rekanan_tenaga_ahli->getField("KTP")?>
							</td>
							<td valign="top" >
								<?= $rekanan_tenaga_ahli->getField("NPWP")?>
							</td>
							<td valign="top" >
								<?=getFormattedDate($rekanan_tenaga_ahli->getField("TEMPAT_LAHIR"))?>
							</td>
							<td valign="top" >
								<?=getFormattedDate($rekanan_tenaga_ahli->getField("TANGGAL_LAHIR"))?>
							</td>
							<td valign="top" >
								<?=getFormattedDate($rekanan_tenaga_ahli->getField("ALAMAT"))?>
							</td>
							<?php
							if (array_intersect($this->libsession->cekStatusValidasiRekanan(), $arrStatusValidasi)) {
								if ($this->libsession->cekChecklist('tenaga_ahli') || ($reqStatusUser == '0' && $reqStatusValidasi == '1')) // Check Checlist Verifikator
								{
							 		if ($this->libsession->cekUrl($this->uri->segment(3, ""))) { ?>
                  <td valign="top" class="kolom-aksi text-center">
                    <a href="main/index/data_teknis_tenaga_ahli_tambah/?reqTenagaAhliId=<?=$rekanan_tenaga_ahli->getField("REKANAN_TENAGA_AHLI_ID")?>" class="btn-aksi">
                    	<?= ICON_EDIT ?>
                    <!--<img src="images/icn_edit.gif" title="Edit Data" width="16" height="16" border="0" />--> </a>
                    <a onClick="deleteData('rekanan_tenaga_ahli_json/delete/', '<?=$rekanan_tenaga_ahli->getField("REKANAN_TENAGA_AHLI_ID")?>')" class="btn-aksi">
                    	<?= ICON_DELETE ?>
                    </a>
							</td>
							<?php
									}
							 	}
							} ?>
						  </tr>
						  <tr id="reqDetil<?=$no?>" style="display:none;">
							<td></td>
							<td colspan="6">
		                        <div class="area-show-hide-konten">
			                        <!--<table width="100%" border="0" cellpadding="2" cellspacing="1">-->
                  <table style="width:100% !important">
									  <tr class="judul-kolom2">
											<th width="10px" style="text-align:center">No</th>
											<th colspan="6">Pendidikan</th>
									  </tr>
									  <?php
									  $array_pendidikan = explode("* ",$rekanan_tenaga_ahli->getField("PENDIDIKAN"));
									  //print_r($array_pendidikan);
									  $x=0;
									  while($x < count($array_pendidikan)){
										  $array_pendidikan_isi = explode("-",$array_pendidikan[$x]);
										  $nmJurusan = str_replace("(","",$array_pendidikan_isi[0]);
										  $nmPendidikan = str_replace(")","",$array_pendidikan_isi[1]);
									  ?>
									  <tr class="judul-kolom4">
											<td width="10px" style="text-align:center"><?=$x+1?></td>
											<td colspan="6"><?= $nmJurusan.' - '.$nmPendidikan?></td>
									  </tr>
									  <?php $x++;}?>
									</table>
                  <table style="width:100% !important">
									  <tr class="judul-kolom2">
											<th width="10px" style="text-align:center">No</th>
											<th width="200px" style="text-align:left"><?=translate("Nama Proyek", "Project")?></th>
											<!-- <th width="100px" style="text-align:center"><?=translate("Posisi/Jabatan", "Position")?></th> -->
											<th width="50px" style="text-align:center"><?=translate("Periode/Lama", "Period")?></th>
											<!-- <th width="50px" style="text-align:center"><?=translate("Tahun", "Year")?></th> -->
											<th width="100px" style="text-align:center"><?=translate("Instansi", "Place")?></th>
											<th width="50px" style="text-align:center"><?=translate("Nama Perusahaan", "Workplace")?></th>
									  </tr>
									  <?php
                       $rekanan_tenaga_ahli_pengalaman = new RekananTenagaAhliPengalaman();
                       $rekanan_tenaga_ahli_pengalaman->selectByParams(array('REKANAN_TENAGA_AHLI_ID'=>$rekanan_tenaga_ahli->getField("REKANAN_TENAGA_AHLI_ID")), -1, -1, $statement);

                       $y=1;
                       while($rekanan_tenaga_ahli_pengalaman->nextRow())
                    {
                    ?>
                    <tr class="judul-kolom4">
											<td width="10px" style="text-align:center"><?=$y?></td>
											<td>
												<span class="badge badge-info"><?= $rekanan_tenaga_ahli_pengalaman->getField("POSISI") ?></span><br>
												<?=$rekanan_tenaga_ahli_pengalaman->getField("PENGALAMAN") ?> - <?=$rekanan_tenaga_ahli_pengalaman->getField("PEKERJAAN") ?>
											</td>
                        <!-- <td style="text-align:center"></td> -->
                        <!-- <td style="text-align:center"></td> -->
                        <td style="text-align:center"><?=$rekanan_tenaga_ahli_pengalaman->getField("PERIODE") ?></td>
                        <td style="text-align:center"><?=$rekanan_tenaga_ahli_pengalaman->getField("INSTANSI") ?></td>
                        <td style="text-align:center"><?=$rekanan_tenaga_ahli_pengalaman->getField("NAMA_PERUSAHAAN") ?></td>
                    </tr>
										<?php
                       $y++;
                     }
                     unset($rekanan_tenaga_ahli_pengalaman);
										?>
									</table>
                  <table style="width:100% !important">
									  <tr class="judul-kolom2">
											<th width="10px" style="text-align:center">No</th>
											<th width="100px" style="text-align:center"><?=translate("Keahlian", "Expertise")?></th>
											<th width="300px" style="text-align:center"><?=translate("No. Serifikat", "Cert. Number")?></th>
											<th width="200px" style="text-align:center">File Sertifikat</th>
											<th width="100px" style="text-align:center"><?=translate("Instansi/Penerbit", "Expertise")?></th>
											<th width="100px" style="text-align:center"><?=translate("Tanggal Berlaku", "Expertise")?></th>
									  </tr>
									   <?php
									  // $array_keahlian = explode(" # ",$rekanan_tenaga_ahli->getField("SERTIFIKAT"));
									 // echo $array_keahlian->query;exit;
									  // echo "<pre>"; print_r($array_keahlian);
                     $rekanan_tenaga_ahli_sertifikat = new RekananTenagaAhliSertifikat();
                     $rekanan_tenaga_ahli_sertifikat->selectByParams(array('REKANAN_TENAGA_AHLI_ID'=>$rekanan_tenaga_ahli->getField("REKANAN_TENAGA_AHLI_ID")), -1, -1);

                     $y=1;
                     while($rekanan_tenaga_ahli_sertifikat->nextRow())
                    {
										$FILE_DIR = "uploads/tenaga_ahli_sertifikat/";
									  // for($i=0;$i<count($array_keahlian);$i++)
									  // {
									  ?>
									  <tr class="judul-kolom4">
										<td width="10px" style="text-align:center"><?=$i+1?></td>
									  <td><?= $rekanan_tenaga_ahli_sertifikat->getField("KEAHLIAN") ?></td>
									  <td><?= $rekanan_tenaga_ahli_sertifikat->getField("NOMOR") ?></td>

									  <td>
									  	<a href=" <?= $FILE_DIR.$rekanan_tenaga_ahli_sertifikat->getField("PATH_FILE") ?>" class="badge badge-primary" target="_blank">
									  		<?= $rekanan_tenaga_ahli_sertifikat->getField("NAMA_FILE") ?>
									  	</a>
									  	</td>
									  <td><?= $rekanan_tenaga_ahli_sertifikat->getField("INSTANSI") ?></td>
									  <td><?= $rekanan_tenaga_ahli_sertifikat->getField("TANGGAL_BERLAKU") ?></td>
									  </tr>
									  <?php
									  }
									  ?>
									</table>
									  <?php
										$no++;
									  }
									}
								  ?>
								</div>
							</td>
						</tr>
					</tr>
				</tbody>
	          </table>

	        </div>
        </div>
      </div>
    </div>
  </div>
</div>
