<?php
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 * 
 */
if($this->USER_TYPE_ID == "")
    redirect("main");

$this->load->library("kauth");  $userLogin = new kauth(); 
$this->load->library("paketinfo"); $paketInfo = new paketinfo();
include_once("functions/string.func.php");
include_once("functions/date.func.php");
include_once("functions/default.func.php");
$this->load->model("PaketPanitia");
$this->load->model("PaketPihakLain");
$paket_panitia = new PaketPanitia();
$paket_pihak_lain = new PaketPihakLain();


/* VARIABLES */
$reqMode = $this->input->post("reqMode");
$reqId = $this->input->get("reqId");
$submitSimpan = $this->input->post("submitSimpan");
$reqRekananId = $_POST["reqRekananId"];

$paketInfo->getPaket($reqId);
$reqNama = $paketInfo->nama;

$i = 0;
$paket_panitia->selectByParamsPaktaIntegritas(array("A.PAKET_ID" => $reqId));

if ($paket_panitia->countRow() > 0) {
	while($paket_panitia->nextRow())
	{
		$arrNama[$i] = $paket_panitia->getField("NAMA");
		$arrJenis[$i] = "PANITIA";
		$arrKode[$i] = $paket_panitia->getField("NIP");
		$arrKodeQr[$i] = $paket_panitia->getField("KODE");
		$i++;
	}
} else {
	$arrNama = array();
	$arrJenis =  array();
	$arrKode =  array();
	$arrKodeQr =  array();
}

// $paket_pihak_lain->selectByParamsPaktaIntegritas(array("A.PAKET_ID" => $reqId));
// while($paket_pihak_lain->nextRow())
// {
// 	$arrNama[$i] = $paket_pihak_lain->getField("USER_NAMA");
// 	// $arrJenis[$i] = "FUNGSIONAL";	
// 	$arrJenis[$i] = "PENGGUNA";	// ikn 20190313
// 	$arrKode[$i] = $paket_pihak_lain->getField("NIP");
// 	$arrKodeQr[$i] = $paket_pihak_lain->getField("KODE");
// 	$i++;
// }
?> 
<script type="text/javascript">
	function submitValidasi(kode, jenis)
	{
		if(confirm("Validasi pakta integritas?"))
		{
			$.getJSON('paket_pakta_integritas_json/pakta_integritas_validasi_json/?reqId=<?=$reqId?>&reqKode='+kode+'&reqJenis='+jenis,
			function(data){
				alertSuccess2(data.PESAN); 
          setTimeout(function() {
			  		document.location.reload();			
          }, 2000);
			});		
		}
	}
</script>

<div class="row"> 
  <div class="col-md-12 col-sm-12">
    <div class="card">
      <div class="card-header card-head-inverse bg-primary">
        <h4 class="card-title text-white">Pakta Integritas
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
        <div class="card-body area-datatable">
	       <form id="ff" class="easyui-form form-horizontal" method="post" data-options="novalidate:true" enctype="multipart/form-data">
	    	<table class="table table-bordered table-hover" id="tbl_bidang">
	          <tbody>
	            <tr>
	              <th style="width: 2%">No.</th>
	              <th >Nama</th>
	              <th style="width: 15%" class="text-center">Aksi</th>
	            </tr>
	            <?php
				$no = 1;
				for($i=0;$i<count($arrNama);$i++)
				{
					if($arrJenis[$i] == $reqJenis)
					{}
					else
					{						
				?>
				  <tr>
					<td colspan="3"><strong><?=$arrJenis[$i]?></strong></td>
				  </tr>
				<?php
					}
				?>
				<tr >
					<td><?=$no?>.</td>
					<td><?=$arrNama[$i]?></td>
					<td class="text-center"><?php
						if($arrKodeQr[$i] == "")
						{
							if($this->NIP == $arrKode[$i])
							{
						?>
							<a title="#" onclick="submitValidasi('<?=$arrKode[$i]?>', '<?=$arrJenis[$i]?>')" class="<?= CLASS_BTN_DANGER ?>" style="color:#fff">Validasi</a>
						<?php
							}
							else
							{
							?>
							<span style="color:#F00"><span class="fa fa-times"></span>  BELUM VALIDASI </span>
							<?php
							}
						}
						else
						{
						?>
							<span style="color:#063"><span class="fa fa-check"></span>  SUDAH VALIDASI </span>
						<?php
						}
						?></td>
				</tr>
	            <?php
				  $reqJenis = $arrJenis[$i];
				  $no++;
				}
				?>
	          </tbody>
	        </table>

	        <div>
	          <a href="main/index/paket_detil/?reqId=<?=$reqId?>" class="<?= CLASS_BTN_DANGER ?>" ><i class="fa fa-arrow-left"></i> Kembali</a>
	          <a class="<?= CLASS_BTN_INFO ?>" href="main/loadUrl/report/pakta_integritas_pdf/?reqId=<?=$reqId?>" target="_blank" ><i class="fa fa-print"></i> Cetak Pakta Integritas</a> 
	        </div>
	       </form>
           
        </div>
      </div>
    </div>
  </div> 
</div>   