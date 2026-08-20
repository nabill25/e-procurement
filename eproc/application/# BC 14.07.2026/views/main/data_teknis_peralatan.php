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
$this->load->model("RekananPeralatan");
include_once("functions/string.func.php");
include_once("functions/date.func.php");
include_once("functions/default.func.php");

$rekanan = new Rekanan();
$rekanan_peralatan = new RekananPeralatan();

//
// $rekanan->selectByParams(array("REKANAN_ID"=>$this->ID),-1,-1);
// $rekanan->firstRow();

$statement = '';
if($reqCari == 'Submit' ){
	$statement = " AND UPPER(JENIS) LIKE '%".strtoupper($reqInputCari)."%' ";
}

$allRecord = $rekanan_peralatan->getCountByParams(array('REKANAN_ID'=>$this->ID), $statement);
$rekanan_peralatan->selectByParams(array('REKANAN_ID'=>$this->ID), -1, -1, $statement);
?>

<div class="row">
  <div class="col-md-12 col-sm-12">
    <div class="card">
      <div class="card-header card-head-inverse bg-primary">
        <h4 class="card-title text-white">Peralatan
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

            if ($this->libsession->cekChecklist('peralatan') || ($reqStatusUser == '0' && $reqStatusValidasi == '1')) // Check Checlist Verifikator
            { 
           		if ($this->libsession->cekUrl($this->uri->segment(3, ""))) { ?>
            <div class="badge badge-pill badge-warning">
              <a href="main/index/data_teknis_peralatan_tambah" data-toogle=""><span class="fa fa-plus text-white icon-line-height" data-toggle="tooltip" data-placement="top" title="Tambah Data"></span> Tambah</a>
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

          <table class="table table-bordered table mb-0">
         	<tbody>
              <tr class="judul-kolom">
                <th width="5%">No</th>
                <th>Jenis</th>
                <th>Lokasi</th>
                <th>Kepemilikan</th>
                <th>Kondisi</th>
                <th>Keterangan</th>
                <?php
                if (array_intersect($this->libsession->cekStatusValidasiRekanan(), $arrStatusValidasi)) {
									if ($this->libsession->cekChecklist('peralatan') || ($reqStatusUser == '0' && $reqStatusValidasi == '1')) // Check Checlist Verifikator
                  { 
                 		if ($this->libsession->cekUrl($this->uri->segment(3, ""))) { ?>
                  <th class="text-center" width="9%">Aksi</th>
                <?php
										}
                 }
                } ?>
              </tr>
	             <?php
				$i = 0;
				if($allRecord > 0){
					while($rekanan_peralatan->nextRow()){
				?>
				<tr>
					<td><?=$i+1?></td>
          <td><?=$rekanan_peralatan->getField("JENIS")?></td>
					<td><?=$rekanan_peralatan->getField("LOKASI")?></td>
          <td><?=$rekanan_peralatan->getField("BUKTI_KEPEMILIKAN")?></td>
					<td><?=$rekanan_peralatan->getField("KONDISI")?></td>
          <td>
            <p>
            Merek: <?=$rekanan_peralatan->getField("MERK")?><br>
            Th. Pembuatan: <?=$rekanan_peralatan->getField("TAHUN")?><br>
            Jumlah: <?=$rekanan_peralatan->getField("JUMLAH").' '.$rekanan_peralatan->getField("KAPASITAS_SATUAN")?><br>
            Kapasitas: <?=$rekanan_peralatan->getField("KAPASITAS")?><br>
            File Peralatan:
            <?php
            if ($rekanan_peralatan->getField("NAMA_FILE")) {
               echo '<a href="'.base_url('uploads/peralatan/').$rekanan_peralatan->getField("PATH_FILE").'" class="badge badge-primary" target="_blank">Download file</a>';
             } ?>
          </p>
          </td>
           <?php
          if (array_intersect($this->libsession->cekStatusValidasiRekanan(), $arrStatusValidasi)) {
						if ($this->libsession->cekChecklist('peralatan') || ($reqStatusUser == '0' && $reqStatusValidasi == '1')) // Check Checlist Verifikator
            { 
           		if ($this->libsession->cekUrl($this->uri->segment(3, ""))) { ?>
					<td class="text-center">
						<a class="btn-aksi" href="main/index/data_teknis_peralatan_tambah/?reqPeralatanId=<?=$rekanan_peralatan->getField("REKANAN_PERALATAN_ID")?>">
              <?= ICON_EDIT ?>
            </a>
						<a class="btn-aksi" onClick="deleteData('rekanan_peralatan_json/delete/', '<?=$rekanan_peralatan->getField("REKANAN_PERALATAN_ID")?>')">
              <?= ICON_DELETE ?>
            </a>
					</td>
          <?php
						}	
           }
          } ?>
				</tr>
				<?php $i++;
          }
        }else{
				?>
				<tr>
					<td colspan="7" class="text-center">. : : Data belum ada : : .</td>
				</tr>
				<?php }?>
          	</tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>
