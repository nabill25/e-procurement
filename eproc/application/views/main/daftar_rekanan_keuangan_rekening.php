<?php
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 *
 */

$this->load->library("paketinfo"); $paketInfo = new paketinfo();
$this->load->model("RekananRekeningKoran");
$this->load->model("Rekanan");
include_once("functions/string.func.php");
include_once("functions/date.func.php");
include_once("functions/default.func.php");

$reqMode =  $this->input->get("reqMode");
$reqKoreksi =  $this->input->get("reqKoreksi");
$reqSubmit =  $this->input->get("reqSubmit");
$reqId = $this->input->get("reqId");
$reqPaketId =  $this->input->get("reqPaketId");

$tahun = date("Y");
$reqTahunPajak = $this->input->get("reqTahunPajak");
if ($reqTahunPajak == '') {
  $reqTahunPajak = $tahun;
} else {
  $reqTahunPajak = $reqTahunPajak;
}

$FILE_DIR = "uploads/rekening_koran/";
$rekanan_tahun = new RekananRekeningKoran();
$rekanan_get_nama = new Rekanan();

//$allRecord = $rekanan_tahun->getCountByParamsTahun(array('REKANAN_ID'=>$reqId), $statement);
//$rekanan_tahun->selectByParamsTahun(array('REKANAN_ID'=>$reqId), -1, -1, $statement);

$rekanan_tahun_select = new RekananRekeningKoran();
$rekanan_tahun_selectGet = new RekananRekeningKoran();
$rekanan_koran = new RekananRekeningKoran();

$allRecord_select = $rekanan_tahun_select->getCountByParamsTahun(array('REKANAN_ID'=>$reqId), $statement);

$allRecord_select_tahun = $rekanan_tahun_select->getCountByParamsTahun(array('REKANAN_ID'=>$reqId, "TAHUN"=>$tahun), $statement);

$rekanan_tahun_select->selectByParamsTahunSelect(array('REKANAN_ID'=>$reqId), -1, -1, $statement);

if($reqTahunPajak == ""){
	if($allRecord_select_tahun == 0)
	{
		$rekanan_tahun_selectGet->selectByParamsTahunSelect(array('REKANAN_ID'=>$reqId), -1, -1, $statement);
		$rekanan_tahun_selectGet->firstRow();
		$reqTahunPajak = $rekanan_tahun_selectGet->getField("TAHUN");
	}else $reqTahunPajak = $tahun;
}

$rekanan_get_nama->selectByParams(array("A.REKANAN_ID"=>$reqId),-1,-1);
$rekanan_get_nama->firstRow();
$tempNama_getNama= $rekanan_get_nama->getField("NAMA");
$reqStatusValidasi = $rekanan_get_nama->getField("STATUS_VALIDASI");
$reqUserStatus = $rekanan_get_nama->getField("USER_STATUS");

if($reqPaketId == "")
{}
else
{
	$paketInfo->getPaket($reqPaketId);
	$reqTahun = getYear($paketInfo->tanggal_tahap);
	$reqBulan = (int)getMonth($paketInfo->tanggal_tahap);
	$year = $year1 = $year2 = $reqTahun;

	$arrSyaratBulan = explode(", ",$paketInfo->syarat_rekening_koran_bulan);
	$info_bulan='';
	for($i=0; $i < 3; $i++){
			if($info_bulan == '')
				$info_bulan .= getNameMonth(substr($arrSyaratBulan[$i],0,-4))." ".substr($arrSyaratBulan[$i],strlen($arrSyaratBulan[$i])-4,strlen($arrSyaratBulan[$i]));
			else
				$info_bulan .= ', '.getNameMonth(substr($arrSyaratBulan[$i],0,-4))." ".substr($arrSyaratBulan[$i],strlen($arrSyaratBulan[$i])-4,strlen($arrSyaratBulan[$i]));
	}
}
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <base href="<?=base_url();?>" />

    <link rel="icon" href="../../favicon.ico">

    <title><?= SYSTEM_NAME.' | '.SYSTEM_NAME_PT ?></title>
    <link rel="stylesheet" type="text/css" href="<?=base_url() ?>assets/new/css/vendors.css">
    <link rel="stylesheet" type="text/css" href="<?=base_url() ?>assets/new/vendors/css/ui/prism.min.css">
    <!-- END VENDOR CSS-->
    <!-- BEGIN ROBUST CSS-->
    <link rel="stylesheet" type="text/css" href="<?=base_url() ?>assets/new/css/app.css">
    <!-- <link rel="stylesheet" type="text/css" href="<?=base_url() ?>css/core.css"> -->
    <!-- END ROBUST CSS-->
    <!-- BEGIN Page Level CSS-->
    <link rel="stylesheet" type="text/css" href="<?=base_url() ?>assets/new/css/core/menu/menu-types/horizontal-menu.css">
    <link rel="stylesheet" type="text/css" href="<?=base_url() ?>assets/new/css/core/colors/palette-gradient.css">
    <link rel="stylesheet" type="text/css" href="<?=base_url() ?>assets/new/css/core/colors/palette-callout.css">
    <!-- END Page Level CSS-->
    <!-- BEGIN Custom CSS-->
    <link rel="stylesheet" type="text/css" href="<?=base_url() ?>assets/new/css/style.css">

    <style type="text/css">
        .table th, .table td {
            padding: 5px 6px;
        }
    </style>

  </head>

<body>

 <div class="card mb-1">
    <div class="card-content">
      <div class="p-1">
        <div class="alert bg-primary alert-icon-left alert-arrow-left alert-dismissible mb-2" role="alert">
          <span class="alert-icon"><i class="fa fa-th"></i></span>  <strong>REKENING KORAN</strong>
        </div>
        <p class="alert alert-primary" style="background-color: transparent !important;"><b><?= $tempNama_getNama ?></b></p>
        <div class="table-responsive">
        	<?php
            if($info_bulan == "")
      			{}
      			else
      			{
      			?>
                  <div class="alert alert-info">
                      Rekening koran yang wajib dipenuhi bulan : <?=$info_bulan?>
                  </div>
      			<?php
      			}
      			?>

            <div class="row">
              <div class="col-md-2">
                <select name="reqTahunPajak" id="reqTahunPajak" class="form-control mb-1" onChange="document.location.href='main/loadUrl/main/daftar_rekanan_keuangan_rekening/?reqId=<?=$reqId?>&reqTahunPajak='+this.value" >
                    <?php
                    for($i=date('Y')-5;$i<=date('Y')+1; $i++)
                    {
                    ?>
                      <option value="<?=$i?>" <?php if($i == $reqTahunPajak) { ?> selected="selected" <?php } ?>><?=$i?></option>
                    <?php
                    }
                    ?>
                </select>
              </div>
            </div>

            <form id="ff" class="form-horizontal" role="form">
                <table class="table table-responsive table-bordered" cellpadding="2" cellspacing="1" border="0" width="100%">
                    <tr class="judul-kolom">
                        <th>No.</th>
                        <th style="width: 50%;">Bank</th>
                        <th style="width: 30%;">Nomor Rekening</th>
                        <th style="width: 10%;">Mata Uang</th>
                        <!-- <th>Nilai</th> -->
                        <!-- <th>Kurs Bulan Terkait (IDR)</th> -->
                        <!-- <th>Nominal (IDR)</th> -->
                        <th style="width: 10%;">File <br>Rekening Koran</th>
                    </tr>
                     <?php
					  $allRecord = $rekanan_tahun->getCountByParamsTahun(array('REKANAN_ID'=>$reqId, "TAHUN"=>$reqTahunPajak), $statement);
					  $rekanan_tahun->selectByParamsTahun(array('REKANAN_ID'=>$reqId, "TAHUN"=>$reqTahunPajak), -1, -1, $statement);
					  if($allRecord > 0){
						  while($rekanan_tahun->nextRow()){
					  ?>
                       <tr class="judul-kolom2">
                        <td colspan="5"><strong><?=getNameMonth($rekanan_tahun->getField("BULAN"))." ".$rekanan_tahun->getField("TAHUN")?></strong></td>
                      </tr>
                      <?php
                        $i = 1; $tmpTotal = 0;
                        $rekanan_koran->selectByParams(array('REKANAN_ID'=>$reqId, 'BULAN'=>$rekanan_tahun->getField("BULAN"),'TAHUN'=>$rekanan_tahun->getField("TAHUN")), -1, -1, $statement);
                        while($rekanan_koran->nextRow()){
                            $tmpTotal += $rekanan_koran->getField("NOMINAL");
                        ?>
                    <tr class="gelap">
                        <td><?=$i?></td>
                        <td><?=$rekanan_koran->getField("NAMA")?></td>
                        <td><?=$rekanan_koran->getField("NOMOR")?></td>
                        <td title=""><span title=""> </span><?=$rekanan_koran->getField("MATAUANG")?></td>
                        <!-- <td title=""><?php // echo numberToIna($rekanan_koran->getField("NILAI"))?></td> -->
                        <!-- <td><?php // echo numberToIna($rekanan_koran->getField("KURS"))?></td> -->
                        <!-- <td><?php // echo numberToIna($rekanan_koran->getField("NOMINAL"))?></td> -->
                        <td> <?php if ($rekanan_koran->getField("PATH_FILE") == '')
								{}
								else
								{
							 ?>
                        		<a href="<?=$FILE_DIR.str_replace("'", "''", $rekanan_koran->getField("PATH_FILE"))?>" class="badge badge-primary" target="_blank"><span class="fa fa-download"></span> Download</a>
                       		 <?php
								}
							 ?>
                       </td>
                      </tr>
                    <?php $i++;}?>
                    <!-- <tr class="terang">
                        <td colspan="6" align="right"><strong>Total Bulan <?php // echo getNameMonth($rekanan_tahun->getField("BULAN"))." ".$rekanan_tahun->getField("TAHUN")?></strong></td>
                        <td><strong><?php // echo numberToIna($tmpTotal)?></strong></td>
                        <td></td>
                    </tr> -->
                    <?php }}else{?>
                    <tr class="<?=$css?>">
                        <td colspan="7">.: data belum ada :.</td>
                    </tr>
                    <?php }?>
                </table>
                <?php
				 $kodeKoreksi = "REKENING_KORAN";
				 if($reqMode == 'koreksi')
				 {
					 $this->load->model("PaketRekananDaftar");
					 $paket_rekanan_daftar = new PaketRekananDaftar();

					 if($reqSubmit == "Submit")
					 {
						 $paket_rekanan_daftar->setField("PAKET_ID", $reqPaketId);
						 $paket_rekanan_daftar->setField("REKANAN_ID", $reqId);
						 $paket_rekanan_daftar->setField("KODE", $kodeKoreksi);
						 $paket_rekanan_daftar->setField("CATATAN", $reqKoreksi);
						 $paket_rekanan_daftar->setField("CREATED_BY", $this->USER_LOGIN_ID);
						 $paket_rekanan_daftar->delete();
						 $paket_rekanan_daftar->insert();
					 }

					 ?>
					 <form name="frm" method="post">
					 <fieldset style="margin-top:10px;">
					  <hr>Koreksi
					  <table class="" cellspacing="1" width="100%">
						<tr>
							<td>
							<textarea name="reqKoreksi" class="form-control" style="width:99%"><?=$paket_rekanan_daftar->getCatatan($reqPaketId, $reqId, $kodeKoreksi)?></textarea>
							<input type="hidden" name="reqPaketId" value="<?=$reqPaketId?>" />
							<input type="hidden" name="reqId" value="<?=$reqId?>" />
							<input type="hidden" name="reqMode" value="<?=$reqMode?>" />
                            <br>
							</td>
						</tr>
						<tr>
							<td>
								 <input type="submit" name="reqSubmit" class="btn btn-primary" value="Submit" />
							</td>
						</tr>
					</table>
					</fieldset>
					</form>
				 <?php
				 }
				 ?>
            </form>

        </div>
        <?php 
        if ($this->USER_TYPE_ID == '2' && ($reqStatusValidasi != '1' || $reqUserStatus == '0')) { ?>
				<form id="ff2" class="easyui-form " method="post" novalidate enctype="multipart/form-data">
					<div class="form-actions card-content collapse show border-info border-darken-2 mt-2">
						<div class="card-body">
							<?php
							$checked = '';
							$cekData = new Rekanan();
							$cekData->selectByParamsRekananChecklist(array("REKANANID"=>$reqId),-1,-1);
							$cekData->firstRow();
							if ($cekData->getField("rekening_koran") == '1') {
								$checked = 'checked';
							}
							echo '<input class="mb-1" type="checkbox" name="checkrekening_koran" id="checkjenis" onclick="return updateChecklist(\''.$reqId.'\',\'rekening_koran\')" style="cursor:pointer" '.$checked.'> Ya, Lengkap ';
							?>
							<input type="" class="form-control easyui-validatebox span2" required maxlength="255" style="height: 30px !important;" name="" placeholder="catatan" id="catatanjenis" value="<?=$cekData->getField("rekening_koran_note")?>" onChange="return updateChecklist('<?= $reqId ?>','rekening_koran')">
							<small><sup>*</sup>&nbsp;Tekan enter setelah mengisi catatan</small>
						</div>
					</div>
				</form>
        <?php 
        } ?>
      </div>
    </div>
  </div>


    <script src="lib/bootstrap/libs/jquery/1.12.4/jquery.min.js"></script>
    <script>window.jQuery || document.write('<script src="lib/bootstrap/assets/js/vendor/jquery.min.js"><\/script>')</script>
    <script src="lib/bootstrap/dist/js/bootstrap.min.js"></script>
    <link rel="stylesheet" type="text/css" href="<?=base_url()?>lib/eproc/themes/default/easyui.css">
    <script type="text/javascript" src="<?=base_url()?>lib/eproc/jquery.easyui.min.js"></script>
    <script type="text/javascript" src="<?=base_url()?>lib/eproc/kalender-easyui.js"></script>
    <script type="text/javascript" src="<?=base_url()?>lib/eproc/allfunc.js"></script>

		<script type="text/javascript">
    function updateChecklist(rekananid,jenis) {
      var n = $("#checkjenis:checked").length;
      if (n == 1) {
          $('#catatanjenis').validatebox({ required:false  });
          $('#catatanjenis').val('');
      } else {
          $('#catatanjenis').validatebox({ required:true  });
      }
      var c = $("#catatanjenis").val();
      // alert(n+'-'+c+'-'+rekananid+'-'+jenis); return false;
        $.getJSON("rekanan_json/updateChecklist2/?rekananid="+rekananid+"&jenis="+jenis+"&status="+n+"&catatan="+c,
          function(data){
            if (data.RESPONSE === 'Gagal') {
              $.messager.alert('Info', data.PESAN, 'info');
              if (n === 0) { // kalau gagal balik ke awal
                $("#checkjenis").prop("checked", true);
              } else {
                $("#checkjenis").prop("checked", false);
              }
            }
        });
    }
    </script>

  </body>
</html>
