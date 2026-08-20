<?php
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 *
 */

$this->load->library("paketinfo"); $paketInfo = new paketinfo();
$this->load->model("Rekanan");
$this->load->model("RekananSaham");
$this->load->model("RekananPajak");
$this->load->model("RekananNeraca");
include_once("functions/string.func.php");
include_once("functions/date.func.php");
include_once("functions/default.func.php");

$rekanan = new Rekanan();

$rekanan_pph	= new RekananPajak(); // tipe 2
$rekanan_ppn	= new RekananPajak(); // tipe 3

/* VARIABLE */
$reqId = $this->input->get("reqId");
$reqPaketId =  $this->input->get("reqPaketId");
$reqTipe = $this->input->get("reqTipe");
$reqMode = $this->input->get("reqMode");
$reqKeuangan = $this->input->get("reqKeuangan");
$reqKoreksi = $this->input->get("reqKoreksi");
$reqSubmit = $this->input->get("reqSubmit");

$rekanan->selectByParams(array("A.REKANAN_ID"=>$reqId),-1,-1);
$rekanan->firstRow();
$tempNama_getNama= $rekanan->getField("NAMA");
$reqStatusValidasi = $rekanan->getField("STATUS_VALIDASI");
$reqUserStatus = $rekanan->getField("USER_STATUS");

$tahun= date("Y");

$reqTahunPajak = $this->input->get("reqTahunPajak");

if($reqTahunPajak == ""){
	$reqTahunPajak = $tahun;
}
else
	$reqTahunPajak = $reqTahunPajak;

$allRecord_PPH = $rekanan_pph->getCountByParams(array("TIPE"=>2, "REKANAN_ID"=>$reqId));
$rekanan_pph->selectByParams(array("TIPE"=>2, "REKANAN_ID"=>$reqId, "TAHUN"=>$reqTahunPajak), -1, -1);

$allRecord_PPN = $rekanan_ppn->getCountByParams(array("TIPE"=>3, "REKANAN_ID"=>$reqId));
$rekanan_ppn->selectByParams(array("TIPE"=>3, "REKANAN_ID"=>$reqId, "TAHUN"=>$reqTahunPajak), -1, -1);

if($reqPaketId == "")
{}
else
{
	$paketInfo->getPaket($reqPaketId);

	if($reqTipe == 2){
	$arrSyaratBulan = explode(", ",$paketInfo->syarat_keuangan_bulan_pph);
	}elseif($reqTipe == 3){
	$arrSyaratBulan = explode(", ",$paketInfo->syarat_keuangan_bulan_ppn);
	}

	$info='';
	for($i=0; $i < 3; $i++){
		if($info == '')
			$info .= getNameMonth(substr($arrSyaratBulan[$i],0,-4))." ".substr($arrSyaratBulan[$i],strlen($arrSyaratBulan[$i])-4,strlen($arrSyaratBulan[$i]));
		else
			$info .= ', '.getNameMonth(substr($arrSyaratBulan[$i],0,-4))." ".substr($arrSyaratBulan[$i],strlen($arrSyaratBulan[$i])-4,strlen($arrSyaratBulan[$i]));
	}

	$info = "Pajak yang harus dipenuhi adalah bulan : ".$info;

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
    <link rel="stylesheet" type="text/css" href="<?=base_url() ?>assets/new/css/app.css">
    <link rel="stylesheet" type="text/css" href="<?=base_url() ?>assets/new/css/core/menu/menu-types/horizontal-menu.css">
    <link rel="stylesheet" type="text/css" href="<?=base_url() ?>assets/new/css/core/colors/palette-gradient.css">
    <link rel="stylesheet" type="text/css" href="<?=base_url() ?>assets/new/css/core/colors/palette-callout.css">
    <link rel="stylesheet" type="text/css" href="<?=base_url() ?>assets/new/css/style.css">

  </head>

<body>

<div class="card mb-1">
    <div class="card-content">
      <div class="p-1">
        <div class="alert bg-primary alert-icon-left alert-arrow-left alert-dismissible mb-2" role="alert">
          <span class="alert-icon"><i class="fa fa-th"></i></span>  <strong>Laporan Pajak Bulanan (PPN)</strong>
        </div>
        <p class="alert alert-primary" style="background-color: transparent !important;"><b><?= $tempNama_getNama ?></b></p>

        <div class="row">
              <div class="col-md-2">  
                <select name="reqTahunPajak" id="reqTahunPajak" class="form-control mb-1" onChange="document.location.href='main/loadUrl/main/daftar_rekanan_pajak_bulanan/?reqPaketId=<?=$reqPaketId?>&reqTipe=<?=$reqTipe?>&reqId=<?=$reqId?>&reqTahunPajak='+this.value" >
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
        <div class="table-responsive">
      		<?php
            if($info == "")
      		{}
      		else
      		{
      		?>
            <div class="informasi">
              <ul>
                <li><?=$info?></li>
              </ul>
            </div>
      		<?php
      		}

          // if($reqTipe == "2" || $reqTipe == "")
          // {
          ?>
           <!--  <div class="alert alert-info" role="alert"> Laporan Pajak Bulanan(PPH)</div>
            <table class="table table-bordered table-hover" border="0" cellpadding="2" cellspacing="1" id="tbl_bidang">
              <tbody>
                <tr class="judul-kolom">
                  <th width="10%">Bulan</th>
                  <th>Nomor</th>
                  <th width="20%">Tanggal</th>
                  <th class="text-center" width="20%">File PPH</th>
                </tr>
                <?php
                // if($allRecord_PPH > 0){
                //     $i = 0;
                //     while($rekanan_pph->nextRow()){
                ?>
                <tr>
                     <td><?php // echo getNameMonth($rekanan_pph->getField("BULAN"))?></td>
                     <td><?php // echo $rekanan_pph->getField("NOMOR")?></td>
                     <td><?php // echo getFormattedDateJson($rekanan_pph->getField("TANGGAL"))?></td>
                     <td>
                        <?php
                        // if($rekanan_pph->getField("PATH_FILE") == "")
                        // {}
                        // else
                        // {
                        ?>
                            <a href="uploads/ppn_pph/<?php // echo str_replace("'", "''", $rekanan_pph->getField("PATH_FILE"))?>" target="_blank"><span class="fa fa-download"></span> Download</a>
                        <?php
                        // }
                        ?>
                       </td>
                </tr>
                <?php // $i++;}}else{
                ?>
                <tr class="<?php // echo $css?>">
                    <td colspan="4">.: data Pph belum ada :.</td>
                </tr>
                <?php // }?>
              </tbody>
            </table> -->
          <?php
          // }
          if($reqTipe == "3" || $reqTipe == "")
          {
          ?>
          <div class="alert alert-info" role="alert"> Laporan Pajak Bulanan(PPN)</div>
            <table class="table table-bordered table-hover" border="0" cellpadding="2" cellspacing="1" id="tbl_bidang">
                <tr class="judul-kolom">
                  <th width="10%">Bulan</th>
                  <th>Nomor</th>
                  <th width="20%">Tanggal</th>
                  <th class="text-center" width="20%">File PPN</th>
                </tr>
                <?php if($allRecord_PPN > 0){
                    $i = 0;
                    while($rekanan_ppn->nextRow()){
                ?>
                 <tr >
                     <td><?=getNameMonth($rekanan_ppn->getField("BULAN"))?></td>
                     <td><?=$rekanan_ppn->getField("NOMOR")?></td>
                     <td><?=getFormattedDateJson($rekanan_ppn->getField("TANGGAL"))?></td>
                     <td class="text-center">
                    <?php
                    if($rekanan_ppn->getField("PATH_FILE") == "")
                    {}
                    else
                    {
                    ?>
                        <a href="uploads/ppn_pph/<?=str_replace("'", "''", $rekanan_ppn->getField("PATH_FILE"))?>" class="badge badge-primary" target="_blank"><span class="fa fa-download"></span> Download</a>
                    <?php
                    }
                    ?>
                    </td>
                </tr>
                <?php $i++;}}else{
                ?>
                <tr class="<?=$css?>">
                    <td colspan="4">.: data PPN belum ada :.</td>
                </tr>
                <?php }?>
              </tbody>
            </table>
          <?php
          }
          ?>
        </div>

        <?php
         $kodeKoreksi = $reqKeuangan;
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
                 $paket_rekanan_daftar->setField("CREATED_BY", $userLogin->UID);
                 $paket_rekanan_daftar->delete();
                 $paket_rekanan_daftar->insert();
             }

             ?>
             <form name="frm" method="post">
             <fieldset style="margin-top:10px;">
              <table width="100%">
                <tr>
                    <td>
                    Koreksi
                    <textarea class="form-control" name="reqKoreksi" style="width:99%"><?=$paket_rekanan_daftar->getCatatan($reqPaketId, $reqId, $kodeKoreksi)?></textarea>
                    <input type="hidden" name="reqPaketId" value="<?=$reqPaketId?>" />
                    <input type="hidden" name="reqId" value="<?=$reqId?>" />
                    <input type="hidden" name="reqMode" value="<?=$reqMode?>" />
                    <input type="hidden" name="reqKeuangan" value="<?=$reqKeuangan?>" />
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
      </div>
      <?php 
      if ($this->USER_TYPE_ID == '2' && ($reqStatusValidasi != '1' || $reqUserStatus == '0')) {?>
			<form id="ff2" class="easyui-form " method="post" novalidate enctype="multipart/form-data">
				<div class="form-actions card-content collapse show border-info border-darken-2 mt-2">
					<div class="card-body">
						<?php
						$checked = '';
						$cekData = new Rekanan();
						$cekData->selectByParamsRekananChecklist(array("REKANANID"=>$reqId),-1,-1);
						$cekData->firstRow();
						if ($cekData->getField("ppn") == '1') {
							$checked = 'checked';
						}
						echo '<input class="mb-1" type="checkbox" name="checkppn" id="checkjenis" onclick="return updateChecklist(\''.$reqId.'\',\'ppn\')" style="cursor:pointer" '.$checked.'> Ya, Lengkap ';
						?>
						<input type="" class="form-control easyui-validatebox span2" required maxlength="255" style="height: 30px !important;" name="" placeholder="catatan" id="catatanjenis" value="<?=$cekData->getField("ppn_note")?>" onChange="return updateChecklist('<?= $reqId ?>','ppn')">
						<small><sup>*</sup>&nbsp;Tekan enter setalah mengisi catatan</small>
					</div>
				</div>
			</form>
      <?php 
      } ?>
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
