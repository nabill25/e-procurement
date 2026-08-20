<?php
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 *
 */

$this->libsession->cekSession();

$this->load->model("UserLogin");
$user_login = new UserLogin();

$this->load->library("kauth");  $userLogin = new kauth();
$this->load->model("Rekanan");
$this->load->model("RekananSertifikat");
include_once("functions/string.func.php");
include_once("functions/date.func.php");
include_once("functions/default.func.php");

$rekanan = new Rekanan();
$rekanan_sertifikat = new RekananSertifikat();
//
// $rekanan->selectByParams(array("REKANAN_ID"=>$this->ID),-1,-1);
// $rekanan->firstRow();

$reqCari = httpFilterPost("reqCari");
$reqInputCari = httpFilterPost("reqInputCari");
$reqMode = httpFilterGet("reqMode");
$reqParamKey = httpFilterGet("reqParamKey");
$statement = "";
$allRecord = $rekanan_sertifikat->getCountByParams(array('REKANAN_ID'=>$this->ID), $statement);
$rekanan_sertifikat->selectByParams(array('REKANAN_ID'=>$this->ID), -1, -1, $statement);
?>

<div class="row">
  <div class="col-md-12 col-sm-12">
    <div class="card">
      <div class="card-header card-head-inverse bg-primary">
        <h4 class="card-title text-white">Dokumen Teknis <?php if ($this->REKANAN_TIPE_ID == '7') { echo "Keahlian"; } else { echo "Perusahaan"; } ?>
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

						if ($this->libsession->cekChecklist('teknis_lain') || ($reqStatusUser == '0' && $reqStatusValidasi == '1')) // Check Checlist Verifikator
						{
	             if ($this->libsession->cekUrl($this->uri->segment(3, ""))) { ?>
	            <div class="badge badge-pill badge-warning">
		            <a href="main/index/data_teknis_sertifikat_lain_tambah" data-toogle=""><span class="fa fa-plus text-white icon-line-height" data-toggle="tooltip" data-placement="top" title="Tambah Data"></span> Tambah</a>
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

	        <table class="table table-bordered mb-0">
						<tbody>
					    <tr class="judul-kolom">
					      <th>No</th>
					      <th>Nama Dokumen</th>
                <th>Nomor</th>
					      <th>Instansi Pemberi</th>
					      <th>Tanggal Terbit</th>
					      <th>Tanggal Berakhir</th>
					      <th>File Dokumen Teknis</th>
					      <?php
		        		if (array_intersect($this->libsession->cekStatusValidasiRekanan(), $arrStatusValidasi)) {
                  if ($this->libsession->cekChecklist('teknis_lain') || ($reqStatusUser == '0' && $reqStatusValidasi == '1')) // Check Checlist Verifikator
									{
  			           if ($this->libsession->cekUrl($this->uri->segment(3, ""))) {?>
					      <th class="text-center" width="9%">Aksi</th>
					      <?php
                  }
                 }
					      } ?>
					    </tr>
					    <?php
						$i = 0;
						if($allRecord > 0){
							while($rekanan_sertifikat->nextRow()){
						?>
						   <tr>
								<td><?=$i+1?></td>
								<td>
									<?php
									if($rekanan_sertifikat->getField("JENIS") == '' || $rekanan_sertifikat->getField("JENIS") == 'Dokumen Teknis Lainnya') { ?>
										<!-- <span class="badge badge-primary"><?= $rekanan_sertifikat->getField("JENIS") ?></span><br> -->
										<?=$rekanan_sertifikat->getField("NAMA")?>
									<?php
									} else { ?>
										<span class="badge badge-warning"><?= $rekanan_sertifikat->getField("JENIS") ?></span><br>
										<?=$rekanan_sertifikat->getField("NAMA_SERTIFIKAT")?>
									<?php
									} ?>

								</td>
                <td><?=$rekanan_sertifikat->getField("NOMOR")?></td>
								<td><?=$rekanan_sertifikat->getField("INSTANSI_PEMBERI")?></td>
								<td><?=getFormattedDateJson($rekanan_sertifikat->getField("TANGGAL"))?></td>
								<td>
								<?php
		              if (strtotime($rekanan_sertifikat->getField("BERLAKU")) < 1) {
		                  echo '<span class="badge badge-pill badge-success"> Seumur hidup</span>';
		              } else {
		                if (strtotime($rekanan_sertifikat->getField("BERLAKU")) < strtotime(date('Y-m-d'))) {
		                  echo getFormattedDateJson($rekanan_sertifikat->getField("BERLAKU")). ' <span class="badge badge-pill badge-danger">Berakhir</span>';
		                } else {
		                  echo getFormattedDateJson($rekanan_sertifikat->getField("BERLAKU")).'';
		                }
		              }
		              ?>
								</td>
								<td>
								<?php
		              if ($rekanan_sertifikat->getField("NAMA_FILE") && file_exists('uploads/sertifikat/'.$rekanan_sertifikat->getField("PATH_FILE"))) {
		                 echo '<a href="'.base_url('uploads/sertifikat/').$rekanan_sertifikat->getField("PATH_FILE").'" class="badge badge-primary" target="_blank"><span class="fa fa-download"></span> Download file</a>';
		               } else { echo "-";} ?>
								</td>
								<?php
		            if (array_intersect($this->libsession->cekStatusValidasiRekanan(), $arrStatusValidasi)) {
                  if ($this->libsession->cekChecklist('teknis_lain') || ($reqStatusUser == '0' && $reqStatusValidasi == '1')) // Check Checlist Verifikator
									{
          	       if ($this->libsession->cekUrl($this->uri->segment(3, ""))) {?>
								<td class="text-center">
								<a class="btn-aksi" href="main/index/data_teknis_sertifikat_lain_tambah/?reqSertifikatId=<?=$rekanan_sertifikat->getField("REKANAN_SERTIFIKAT_ID")?>">
								<?= ICON_EDIT ?>
								</a>
		            <a  class="btn-aksi" onClick="deleteData('rekanan_sertifikat_json/delete/', '<?=$rekanan_sertifikat->getField("REKANAN_SERTIFIKAT_ID")?>')">
									<?= ICON_DELETE ?>
		            </a>
								</td>
								<?php
							      }
                  }
								} ?>
							</tr>
							<?php $i++;}}else{
							?>
							<tr>
								<td colspan="7">. : : Data belum ada : : .</td>
							</tr>
							<?php }?>
						</tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>
