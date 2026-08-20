<?php
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 * 
 */
include_once("functions/string.func.php");
include_once("functions/default.func.php");
include_once("functions/date.func.php");

$this->load->library("paketinfo"); $paketInfo = new paketinfo();
$this->load->model("Paket");
$this->load->model("RekananTenagaAhli");
$this->load->model("RekananPajak");
$this->load->model("RekananPeralatan");
$this->load->model("RekananSertifikat");
$this->load->model("BidangUsaha");
$this->load->model("RekananBidangUsaha");
$this->load->model("Rekanan");
$this->load->model("RekananIjinUsaha");
$this->load->model("RekananNeraca");
$this->load->model("Users");
$this->load->model("IjinUsaha");
$this->load->model("RekananAkta");
$this->load->model("RekananPengurus");
$this->load->model("RekananDaftarPengalaman");
$this->load->model("RekananDaftarTenagaAhli");
$this->load->model("RekananDaftarPeralatan");
$this->load->model("RekananDaftarSertifikat");
$this->load->model("RekananRekeningKoran");
$this->load->model("PaketEvaluasiSyaratDaftar");
$this->load->model("PaketRekananDaftar");

/* create objects */
$rekanan = new Rekanan();
$rekanan_tenaga_ahli = new RekananTenagaAhli();
$rekanan_sertifikat = new RekananSertifikat();
$rekanan_peralatan = new RekananPeralatan();
$rekanan_ijin = new RekananIjinUsaha();
$rekanan_pengurus = new RekananPengurus();
$ijin_usaha = new IjinUsaha();
$rekanan_akta = new RekananAkta();
$user_login = new Users();
$paket = new Paket();
$paket_getid = new Paket();
$paket_pengalaman = new Paket();
$paket_tampil = new Paket();
$rekanan_pkp 	= new Rekanan(); // tipe ?
$rekanan_daftar_pengalaman = new RekananDaftarPengalaman();
$rekanan_daftar_tenaga_ahli = new RekananDaftarTenagaAhli();
$rekanan_daftar_peralatan = new RekananDaftarPeralatan();
$rekanan_daftar_sertifikat = new RekananDaftarSertifikat();
$paket_rekanan_daftar = new PaketRekananDaftar();

$reqPaketId= $this->input->get("reqPaketId");

$reqPaketId = $paket_getid->getPaketId(array("MD5('".$this->ID."' || A.PAKET_ID)" => $reqPaketId));

//rekening koran
$paketInfo->getPaket($reqPaketId);

$pengalaman = $rekanan_daftar_pengalaman->getPaketPengalaman($reqPaketId, $this->ID);
if($pengalaman == 0)	$status_pengalaman = translate('Data Belum Lengkap', 'Incomplete');
else					$status_pengalaman = translate('Data Lengkap', 'Complete');


$reqTahun = getYear($paketInfo->tanggal_tahap);
$reqBulan = (int)getMonth($paketInfo->tanggal_tahap);

// set syarat untuk rekening koran
if($paketInfo->syarat_rekening_koran > 0){
	$paket_rekening_koran = new Paket();
	$arrSyaratBulanRekeningKoran = explode(", ",$paketInfo->syarat_rekening_koran_bulan);
	
	$rekening_koran = $paket_rekening_koran->getPaketRekeningKoran($this->ID, getValueArrayMonth($arrSyaratBulanRekeningKoran));
	if($rekening_koran == 3){
		$status_rekening_koran = translate('Data Lengkap', 'Complete');
	}else{
		$status_rekening_koran = translate('Data Belum Lengkap', 'Incomplete');
	}
}

// set syarat untuk teknis tenaga ahli
if($paketInfo->syarat_teknis_tenaga_ahli > 0){
	$tenaga_ahli = $rekanan_daftar_tenaga_ahli->getPaketTenagaAhli($reqPaketId, $this->ID);	
	
	if($tenaga_ahli == 0)	$status_tenaga_ahli = translate('Data Belum Lengkap', 'Incomplete');
	else					$status_tenaga_ahli = translate('Data Lengkap', 'Complete');
}

// set syarat untuk teknis peralatan
if($paketInfo->syarat_teknis_peralatan > 0){
	$peralatan = $rekanan_daftar_peralatan->getPaketPeralatan($reqPaketId, $this->ID);	
	if($peralatan == 0)		$status_peralatan = translate('Data Belum Lengkap', 'Incomplete');
	else					$status_peralatan = translate('Data Lengkap', 'Complete');
}

// set syarat untuk teknis sertifikat
if($paketInfo->syarat_teknis_sertifikat > 0){
	$sertifikat = $rekanan_daftar_sertifikat->getPaketSertifikat($reqPaketId, $this->ID);	
	if($sertifikat == 0)	$status_sertifikat = translate('Data Belum Lengkap', 'Incomplete');
	else					$status_sertifikat = translate('Data Lengkap', 'Complete');
}

// set syarat untuk ijin usaha
$rekanan_ijin_usaha_siup = new RekananIjinUsaha();
$rekanan_ijin_usaha_siup->selectByParams(array("REKANAN_ID" => $this->ID, "IJIN_USAHA_ID"=>1));
$rekanan_ijin_usaha_siup->firstRow();

if ($paketInfo->jenis_id == 3)//pengadaan harus ngecek bidang usaha SIUP
    $ijin_usaha = $paket_tampil->getPaketAktif($this->ID, $reqPaketId, " AND IJIN_USAHA_ID = 1");
else
    $ijin_usaha = 1;//CUMAN NGECEK SIUP, TANPA NGELIAT BIDANG USAHA --PAK WARSILAN
	
//echo $paket_tampil->query;
if($ijin_usaha == 0)	$status_ijin_usaha = translate('Data Belum Lengkap', 'Incomplete');
if($rekanan_ijin_usaha_siup->getField("PATH_FILE") =='')$status_ijin_usaha = translate('Data Belum Lengkap', 'Incomplete');
if($ijin_usaha > 0 && $rekanan_ijin_usaha_siup->getField("PATH_FILE"))	$status_ijin_usaha = translate('Data Lengkap', 'Complete');

// set syarat untuk landasan hukum
$rekanan_akta->selectByParams(array("REKANAN_ID"=>$this->ID, "AKTA_TYPE_ID"=>1),-1,-1);
$rekanan_akta->firstRow();

$tempNomor= $rekanan_akta->getField("NOMOR");
$tempTanggal= dateToPageCheck($rekanan_akta->getField("TANGGAL"));
$tempNotaris= $rekanan_akta->getField("NOTARIS");
$tempLinkFileTemp= $rekanan_akta->getField("PATH_FILE");
		
if($tempNomor == '' || $tempTanggal == '' || $tempNotaris == '' || $tempLinkFileTemp == '')	$status_landasan_hukum = translate('Data Belum Lengkap', 'Incomplete');
else					$status_landasan_hukum = translate('Data Lengkap', 'Complete');


// set syarat untuk keuangan pph
if($paketInfo->syarat_keuangan_pph > 0){
	$keuangan_pph = new Paket();
	$arrSyaratBulanPPH = explode(", ",$paketInfo->syarat_keuangan_bulan_pph);
	
	$val_keuangan_pph = $keuangan_pph->getPaketPajakRekanan($this->ID, getValueArrayMonth($arrSyaratBulanPPH), 2);
	if($val_keuangan_pph== 3){
		$status_keuangan_PPH = translate('Data Lengkap', 'Complete');
	}else{
		$status_keuangan_PPH = translate('Data Belum Lengkap', 'Incomplete');
	}
}

// set syarat untuk keuangan ppn
if($paketInfo->syarat_keuangan_ppn > 0){
	$keuangan_ppn = new Paket();
	$arrSyaratBulanPPN = explode(", ",$paketInfo->syarat_keuangan_bulan_ppn);
	
	$val_keuangan_ppn = $keuangan_ppn->getPaketPajakRekanan($this->ID, getValueArrayMonth($arrSyaratBulanPPN), 3);
	if($val_keuangan_ppn == 3){
		$status_keuangan_PPN = translate('Data Lengkap', 'Complete');
	}else{
		$status_keuangan_PPN = translate('Data Belum Lengkap', 'Incomplete');
	}
}

// set syarat untuk keuangan pkp
if($paketInfo->syarat_keuangan_pkp > 0){
	$rekanan_pkp->selectByParams(array("REKANAN_ID"=>$this->ID), -1, -1);
	$rekanan_pkp->firstRow();
	$tempNoSurat_PKP = $rekanan_pkp->getField("PKP");
	$tempTanggal_PKP = dateToPageCheck($rekanan_pkp->getField("PKP_TANGGAL"));
	$tempJabatan_PKP = $rekanan_pkp->getField("NPWP");
	if($tempNoSurat_PKP && $tempTanggal_PKP && $tempJabatan_PKP)
	$status_keuangan_PKP= translate('Data Lengkap', 'Complete');
	else
	$status_keuangan_PKP= translate('Data Belum Lengkap', 'Incomplete');
}

// set syarat untuk keuangan spt
if($paketInfo->syarat_keuangan_spt > 0){
	
	$rekanan_spt = new RekananPajak();
	$rekanan_spt->selectByParams(array("REKANAN_ID"=>$this->ID, 'TAHUN'=>$paketInfo->syarat_keuangan_spt_tahun, "TIPE"=>1), -1, -1, "", "");
	$rekanan_spt->firstRow();
	if($rekanan_spt->getField("NOMOR") == ''){
		$simpan_status = 1;
	}
			
	
	if($simpan_status == ''){
		$status_keuangan_SPT= translate('Data Lengkap', 'Complete');
	}else{
		$status_keuangan_SPT= translate('Data Belum Lengkap', 'Incomplete');
	}
}

if($paketInfo->syarat_ijin_siujk > 0){
	// set syarat untuk ijin usaha siujk
	$rekanan_ijin_usaha_siujk = new RekananIjinUsaha();
	$rekanan_ijin_usaha_siujk->selectByParams(array("REKANAN_ID" => $this->ID, "IJIN_USAHA_ID"=>3));
	$rekanan_ijin_usaha_siujk->firstRow();
	
	$paket_siujk = new Paket();
	$ijin_usaha_siujk = $paket_siujk->getPaketAktif($this->ID, $reqPaketId, " AND IJIN_USAHA_ID = 3");
	if($ijin_usaha_siujk == 0)	$status_ijin_usaha_siujk = translate('Data Belum Lengkap', 'Incomplete');
	if($rekanan_ijin_usaha_siujk->getField("PATH_FILE") =='')$status_ijin_usaha_siujk = translate('Data Belum Lengkap', 'Incomplete');
	if($ijin_usaha_siujk > 0 && $rekanan_ijin_usaha_siujk->getField("PATH_FILE")) $status_ijin_usaha_siujk = translate('Data Lengkap', 'Complete');
}

if($paketInfo->syarat_ijin_siui > 0){
	// set syarat untuk ijin usaha siui
	$rekanan_ijin_usaha_siui = new RekananIjinUsaha();
	$rekanan_ijin_usaha_siui->selectByParams(array("REKANAN_ID" => $this->ID, "IJIN_USAHA_ID"=>4));
	$rekanan_ijin_usaha_siui->firstRow();
	
	$paket_siui = new Paket();
	$ijin_usaha_siui = $paket_siui->getPaketAktif($this->ID, $reqPaketId, " AND IJIN_USAHA_ID = 4");
	if($ijin_usaha_siui == 0)	$status_ijin_usaha_siui = translate('Data Belum Lengkap', 'Incomplete');
	if($rekanan_ijin_usaha_siui->getField("PATH_FILE") =='')$status_ijin_usaha_siui = translate('Data Belum Lengkap', 'Incomplete');
	if($ijin_usaha_siui > 0 && $rekanan_ijin_usaha_siui->getField("PATH_FILE")) $status_ijin_usaha_siui = translate('Data Lengkap', 'Complete');
}

if($paketInfo->syarat_ijin_lain > 0){
	// set syarat untuk ijin usaha lain
	$rekanan_ijin_usaha_lain = new RekananIjinUsaha();
	$rekanan_ijin_usaha_lain->selectByParams(array("REKANAN_ID" => $this->ID, "IJIN_USAHA_ID"=>5));
	$rekanan_ijin_usaha_lain->firstRow();
	
	$paket_lain = new Paket();
	$ijin_usaha_lain = $paket_lain->getPaketAktif($this->ID, $reqPaketId, " AND IJIN_USAHA_ID = 5");
	if($ijin_usaha_lain == 0)	$status_ijin_usaha_lain = translate('Data Belum Lengkap', 'Incomplete');
	if($rekanan_ijin_usaha_lain->getField("PATH_FILE") =='')$status_ijin_usaha_lain = translate('Data Belum Lengkap', 'Incomplete');
	if($ijin_usaha_lain > 0 && $rekanan_ijin_usaha_lain->getField("PATH_FILE")) $status_ijin_usaha_lain = translate('Data Lengkap', 'Complete');
}

if($paketInfo->syarat_sbu > 0){
	// set syarat untuk ijin usaha lain
	$rekanan_sbu = new RekananIjinUsaha();
	$rekanan_sbu->selectByParams(array("REKANAN_ID" => $this->ID, "IJIN_USAHA_ID"=>99));
	$rekanan_sbu->firstRow();
	
	$paket_lain = new Paket();
	$ijin_sbu = $paket_lain->getPaketAktif($this->ID, $reqPaketId, " AND IJIN_USAHA_ID = 99 ");
	if($ijin_sbu == 0)	$status_sbu = translate('Data Belum Lengkap', 'Incomplete');
	if($rekanan_sbu->getField("PATH_FILE") =='') $status_sbu = translate('Data Belum Lengkap', 'Incomplete');
	if($ijin_sbu > 0 && $rekanan_sbu->getField("PATH_FILE")) $status_sbu = translate('Data Lengkap', 'Complete');
}
if($paketInfo->syarat_neraca > 0){
	// set syarat untuk ijin usaha lain	
	$set_rekanan_neraca = new RekananNeraca();
	$neraca_syarat = $set_rekanan_neraca->getCountByParams(array("REKANAN_ID" => $this->ID), " AND TAHUN IN (".str_replace("/", ",",$paketInfo->syarat_neraca_tahun).") ");
	
	if($neraca_syarat == 0)	
		$status_keuangan_neraca = translate('Data Belum Lengkap', 'Incomplete');
	else
		$status_keuangan_neraca = translate('Data Lengkap', 'Complete');
		
	unset($set_rekanan_neraca);
}

if($paketInfo->syarat_admin_klasifikasi > 0){
	$rekanan->selectByParams(array("REKANAN_ID"=>$this->ID),-1,-1);
	$rekanan->firstRow();
	
	$tempKualifikasi = $rekanan->getField("REKANAN_KUALIFIKASI_ID");
	$tempKualifikasiNama = $rekanan->getField("REKANAN_KUALIFIKASI");
	
	if($paketInfo->kualifikasi_id == '3'){ // aim: 20160527 Kecil / Non Kecil
		if($tempKualifikasi == 2 || $tempKualifikasi == 1){
			$status_klasifikasi = translate('Data Lengkap', 'Complete');
		}else{
			$status_klasifikasi = translate('Data Belum Lengkap', 'Incomplete');
		}
	}else{
		if($tempKualifikasi == 2){
			if($paketInfo->kualifikasi_id == $tempKualifikasi){
				$status_klasifikasi = translate('Data Lengkap', 'Complete');
			}else{
				$status_klasifikasi = translate('Data Belum Lengkap', 'Incomplete');
			}
		}
		elseif($tempKualifikasi == 1){
			if($paketInfo->kualifikasi_id == $tempKualifikasi){
				$status_klasifikasi = translate('Data Lengkap', 'Complete');
			}else{
				$status_klasifikasi = translate('Data Belum Lengkap', 'Incomplete');
			}
		}
	}
}

$i=1;
$j=0;
$adaPengalaman = 0;
$adaAktaPendirian = 0;
$adaPersyaratan = 0;

$paket_evaluasi_syarat_daftar = new PaketEvaluasiSyaratDaftar();
$paket_evaluasi_syarat_daftar->selectByParamsPersyaratan($this->ID, array("A.PAKET_ID" => $reqPaketId));
while($paket_evaluasi_syarat_daftar->nextRow())
{
	$adaPersyaratan++;
	
	if($paket_evaluasi_syarat_daftar->getField("EVALUASI_NUMBER") == "1")
		$adaAktaPendirian = 1;

	if($paket_evaluasi_syarat_daftar->getField("EVALUASI_NUMBER") == "14")
		$adaPengalaman = 1;
		
	if($paket_evaluasi_syarat_daftar->getField("KETERANGAN") == "")
		$ket = "-";
	else
		$ket = $paket_evaluasi_syarat_daftar->getField("KETERANGAN");
			
	if($paket_evaluasi_syarat_daftar->getField("EVALUASI_NUMBER") == "")
	{
		$arrSyaratLain[$j]["ID"] =  $paket_evaluasi_syarat_daftar->getField("PAKET_EVAL_SYARAT_DAFTAR_ID");
		$arrSyaratLain[$j]["NAMA"] =  $paket_evaluasi_syarat_daftar->getField("NAMA");
		$arrSyaratLain[$j]["KETERANGAN"] =  $ket;

		if($paket_evaluasi_syarat_daftar->getField("KELENGKAPAN") == "")
			$kelengkapanSyarat = "";
		else
			$kelengkapanSyarat = translate('Data Lengkap', 'Complete');
		
		$arrSyaratLain[$j]["KELENGKAPAN"] =  $kelengkapanSyarat;
		$j++;
	}
	else
	{
		$arrSyaratDaftar[$paket_evaluasi_syarat_daftar->getField("EVALUASI_NUMBER")]["NAMA"] =  $paket_evaluasi_syarat_daftar->getField("NAMA");		
		$arrSyaratDaftar[$paket_evaluasi_syarat_daftar->getField("EVALUASI_NUMBER")]["KETERANGAN"] = $ket;
	}
}
?>

<script>
function reloadAkta()
{
	$("#daftarAkta").empty();
	$.getJSON('syarat_daftar_json/reloadAkta', function (data) 
	{
				
		Result = data; //Use this data for further creation of your elements.
		$.each(data, function (i, SingleElement) {
			$('#daftarAkta').append('<tr><td>'+SingleElement.NOMOR+'</td><td>'+SingleElement.TANGGAL+'</td><td>'+SingleElement.NOTARIS+'</td></tr>');
		});
		
	
	});	
}


function reloadIjinUsaha(ijinUsahaId)
{
	$("#daftarIjinUsaha"+ijinUsahaId).empty();
	$.getJSON('syarat_daftar_json/reloadIjinUsaha/?reqIjin='+ijinUsahaId, function (data) 
	{
				
		Result = data; //Use this data for further creation of your elements.
		$.each(data, function (i, SingleElement) {
			$("#daftarIjinUsaha"+ijinUsahaId).append('<tr><td>'+SingleElement.NOMOR+'</td><td>'+SingleElement.TANGGAL+'</td><td>'+SingleElement.INSTANSI+'</td></tr>');
		});
		
	
	});	
}

function reloadKualifikasi()
{
	$("#daftarKualifikasi").empty();
	$.getJSON('syarat_daftar_json/reloadKualifikasi', function (data) 
	{
		Result = data; //Use this data for further creation of your elements.
		$.each(data, function (i, SingleElement) {
			$("#daftarKualifikasi").append('<tr><td>'+SingleElement.KUALIFIKASI+'</td></tr>');
		});
	
	});	
}

function reloadRekeningKoran()
{
	$("#daftarRekeningKoran").empty();
	$.getJSON('syarat_daftar_json/reloadRekeningKoran/?reqPaketId=<?=$reqPaketId?>', function (data) 
	{
		Result = data; //Use this data for further creation of your elements.
		$.each(data, function (i, SingleElement) {
			$("#daftarRekeningKoran").append('<tr><td>'+SingleElement.PERIODE+'</td><td>'+SingleElement.NAMA+'</td><td align="right">'+SingleElement.NILAI+'</td></tr>');
		});
	
	});	
}


function reloadSPT()
{
	$("#daftarSPT").empty();
	$.getJSON('syarat_daftar_json/reloadSPT/?reqPaketId=<?=$reqPaketId?>', function (data) 
	{
		Result = data; //Use this data for further creation of your elements.
		$.each(data, function (i, SingleElement) {
			$("#daftarSPT").append('<tr><td>'+SingleElement.TAHUN+'</td><td>'+SingleElement.TANGGAL+'</td><td>'+SingleElement.NOMOR+'</td></tr>');
		});
	
	});	
}

function reloadNeraca()
{
	$("#daftarNeraca").empty();
	$.getJSON('syarat_daftar_json/reloadNeraca/?reqPaketId=<?=$reqPaketId?>', function (data) 
	{
		Result = data; //Use this data for further creation of your elements.
		$.each(data, function (i, SingleElement) {
			$("#daftarNeraca").append('<tr><td>'+SingleElement.NOMOR+'</td><td>'+SingleElement.TANGGAL+'</td><td align="right">'+SingleElement.MODAL+'</td></tr>');
		});
	
	});	
}

function reloadPajak(tipePajak)
{
	$("#daftarPajak"+tipePajak).empty();
	$.getJSON('syarat_daftar_json/reloadPajak/?reqPaketId=<?=$reqPaketId?>&reqTipe='+tipePajak, function (data) 
	{
		Result = data; //Use this data for further creation of your elements.
		$.each(data, function (i, SingleElement) {
			$("#daftarPajak"+tipePajak).append('<tr><td>'+SingleElement.PERIODE+'</td><td>'+SingleElement.NOMOR+'</td><td>'+SingleElement.TANGGAL+'</td></tr>');
		});
	
	});	
}

function reloadPKP()
{
	$("#daftarPKP").empty();
	$.getJSON('syarat_daftar_json/reloadPKP', function (data) 
	{
		Result = data; //Use this data for further creation of your elements.
		$.each(data, function (i, SingleElement) {
			$("#daftarPKP").append('<tr><td>'+SingleElement.PKP+'</td><td>'+SingleElement.PKP_TANGGAL+'</td><td>'+SingleElement.NPWP+'</td></tr>');
		});
	
	});	
}
</script>

<div class="row">
    <div class="col-md-12">
    	<div class="area-main">
        	<div class="judul-halaman">Pendaftaran Paket Lelang</div>
            <div class="inner">
            	<div class="area-sidelook"></div>
                <div class="area-konten">
                    <div class="area-konten-inner">
                        <form id="ff" class="easyui-form form-horizontal" method="post" data-options="novalidate:true" enctype="multipart/form-data">
							
                            <div class="judul-grup">Syarat dan Ketentuan</div>
                            <div class="row">
                                <div class="col-md-12" style="border:1px solid yellow;">
                                    <div class="form-group" style="border:1px solid red;">

                                        <table class="syarat-ketentuan">
                                        <?php
                                        if($adaPersyaratan > 0)
                                        {
                                        ?>
                                          <tr>
                                            <td colspan="3"><?=translate("Untuk melakukan pendaftaran paket ke e-Procurement PT. Angkasa Pura Suport, diharapkan untuk mengisi syarat dan ketentuan paket dibawah.", "Please fill out the form below and click Submit to submit your application for consideration. <font color='red'>Fields with an asterisk (*) are required.</font>")?></td>
                                          </tr>
                                          <tr>
                                            <td>&nbsp;</td>
                                            <td>&nbsp;</td>
                                            <td>&nbsp;</td>
                                          </tr>
                                          <tr class="judul-kolom">
                                              <td align="center"><?=translate("Persyaratan", "Requirements")?></td>
                                              <td align="center"><?=translate("Informasi Tambahan", "Additional Information")?></td>
                                              <td align="center"><?=translate("Kelengkapan", "Applications")?></td>
                                          </tr>
                                       <?php
                                        }
                                        else
                                        {
                                       ?>
                                          <tr>
                                            <td colspan="3"><?=translate("Paket <strong>".$paketInfo->nama."</strong> tidak mempunyai persyaratan khusus untuk mendaftar, Silahkan klik tombol 'Daftar' di bawah ini untuk melanjutkan proses pendaftaran paket.", "Please click Register to submit your application for consideration.")?></td>
                                          </tr>                           
                                       <?php
                                        }
                                       ?>
                                          <?php
                                          $css = "terang";
                                          if($adaAktaPendirian > 0){
                                          if($css == "terang")	$css = "gelap";
                                          else					$css = "terang";
                                          ?>
                                          <tr class="<?=$css?>">
                                            <td valign="top"><label>Data Akta Pendirian<span class="merah">*</span></label></td>
                                            <td valign="top"><?=$arrSyaratDaftar[1]["KETERANGAN"]?></td>
                                            <td>
                                                
                                                <?php
                                                $catatan_panitia = $paket_rekanan_daftar->getCatatan($reqPaketId, $this->ID, "LANDASAN");
                                                if($catatan_panitia == "") {}
                                                else
                                                {
                                                ?>
                                                    <div class="area-catatan-panitia">
                                                        <div class="judul"><?=translate("Catatan Panitia", "Admin Note")?> :</div>
                                                        <div class="isi"><?=$catatan_panitia?></div>
                                                    </div>
                                                <?php
                                                }
                                                ?>
                                            
                                                <input type="text" readonly style="width:175px" id="reqDataLandasanHukumLabel" name="reqDataLandasanHukumLabel" value="<?=$status_landasan_hukum?>"></input>
                                              <a  class="area-klik btn-lookup" style="text-decoration:none; font-size:12px;" title="#" onClick="openAdd('main/loadUrl/main/data_administrasi_landasan_hukum_syarat/?&reqAktaType=1')">
                                              <img src="images/icn_search.gif" width="16"><?=translate("Lengkapi", "Browse")?></a>
            
                                                <div class="tabel-pengalaman">
                                                    <table style="margin-top:10px;">
                                                        <thead>
                                                        <tr class="judul-kolom">
                                                            <th>
                                                                <?=translate("No", "Reference Number")?>
                                                            </th>
                                                            <th>
                                                                <?=translate("Tanggal", "Issue Date")?>
                                                            </th>
                                                            <th>
                                                                <?=translate("Disahkan oleh", "Endorsed By")?>
                                                            </th>
                                                        </tr>
                                                        </thead>
                                                        <tbody id="daftarAkta">
                                                                <tr>
                                                                    <td class="nomor-urut"><?=$rekanan_akta->getField("NOMOR")?></td>
                                                                    <td class="tanggal"><?=dateToPage($rekanan_akta->getField("TANGGAL"))?></td>
                                                                    <td><?=$rekanan_akta->getField("NOTARIS")?></td>
                                                                </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                              
                                            </td>
                                          </tr>
                                          <?php
                                          }
                                          /* berlaku untuk paket $reqPaketId > 532 */
                                          if($paketInfo->syarat_ijin_siup > 0 && $reqPaketId <= 532){
                                          if($css == "terang")	$css = "gelap";
                                          else					$css = "terang";
                                          ?>
                                          <tr class="<?=$css?>">
                                            <td valign="top"><label>Data Ijin Usaha<span class="merah">*</span></label></td>
                                            <td valign="top"><?=$arrSyaratDaftar[2]["KETERANGAN"]?></td>
                                            <td>
                                                
                                                
                                                <?php
                                                $catatan_panitia = $paket_rekanan_daftar->getCatatan($reqPaketId, $this->ID, "SIUP");
                                                if($catatan_panitia == "") {}
                                                else
                                                {
                                                ?>
                                                    <div class="area-catatan-panitia">
                                                        <div class="judul"><?=translate("Catatan Panitia", "Admin Note")?> :</div>
                                                        <div class="isi"><?=$catatan_panitia?></div>
                                                    </div>
                                                <?php
                                                }
                                                ?>
                                                
                                                <input type="text" readonly style="width:175px" id="reqDataIjinUsahaLabel" name="reqDataIjinUsahaLabel" value="<?=$status_ijin_usaha?>"></input>
                                              <a  class="area-klik btn-lookup" style="text-decoration:none; font-size:12px;" title="#" onClick="openAdd('main/loadUrl/main/data_administrasi_ijin_usaha_syarat/?reqPaketId=<?=$reqPaketId?>&reqIjinUsaha=1'); return false">
                                              <img src="images/icn_search.gif" width="16"><?=translate("Lengkapi", "Browse")?></a>
            
                                                <div class="tabel-pengalaman">
                                                    <table style="margin-top:10px;">
                                                        <thead>
                                                        <tr class="judul-kolom">
                                                            <th>
                                                                <?=translate("No", "Reference Number")?>
                                                            </th>
                                                            <th>
                                                                <?=translate("Tanggal", "Issue Date")?>
                                                            </th>
                                                            <th>
                                                                <?=translate("Disahkan oleh", "Endorsed By")?>
                                                            </th>
                                                        </tr>
                                                        </thead>
                                                        <tbody id="daftarIjinUsaha1">
                                                                <tr>
                                                                    <td class="nomor-seri"><?=$rekanan_ijin_usaha_siup->getField("NO_IJIN")?></td>
                                                                    <td class="tanggal"><?=dateToPage($rekanan_ijin_usaha_siup->getField("TANGGAL"))?></td>
                                                                    <td><?=$rekanan_ijin_usaha_siup->getField("INSTANSI")?></td>
                                                                </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                                                                  
                                            </td>
                                          </tr>
            
                                          <?php
                                          }
                                          
                                          
                                          if($paketInfo->syarat_ijin_siujk > 0){
                                          if($css == "terang")	$css = "gelap";
                                          else					$css = "terang";
                                          ?>
                                          <tr class="<?=$css?>">
                                            <td valign="top"><label>Data Ijin SIUJK<span class="merah">*</span></label></td>
                                            <td valign="top"><?=$arrSyaratDaftar[3]["KETERANGAN"]?></td>
                                            <td>
                                            
                                                <?php
                                                $catatan_panitia = $paket_rekanan_daftar->getCatatan($reqPaketId, $this->ID, "SIUJK");
                                                if($catatan_panitia == "") {}
                                                else
                                                {
                                                ?>
                                                    <div class="area-catatan-panitia">
                                                        <div class="judul"><?=translate("Catatan Panitia", "Admin Note")?> :</div>
                                                        <div class="isi"><?=$catatan_panitia?></div>
                                                    </div>
                                                <?php
                                                }
                                                ?>                                
                                            
                                               <input type="text" readonly style="width:175px" id="reqDataIjinUsahaSIUJKLabel" name="reqDataIjinUsahaSIUJKLabel" value="<?=$status_ijin_usaha_siujk?>"></input>
                                              <a  class="area-klik btn-lookup" style="text-decoration:none; font-size:12px;" title="#" onClick="openAdd('main/loadUrl/main/data_administrasi_ijin_usaha_syarat/?reqPaketId=<?=$reqPaketId?>&reqIjinUsaha=3'); return false">
                                              <img src="images/icn_search.gif" width="16"><?=translate("Lengkapi", "Browse")?></a>
            
                                                <div class="tabel-pengalaman">
                                                    <table style="margin-top:10px;">
                                                        <thead>
                                                        <tr class="judul-kolom">
                                                            <th>
                                                                <?=translate("No", "Reference Number")?>
                                                            </th>
                                                            <th>
                                                                <?=translate("Tanggal", "Issue Date")?>
                                                            </th>
                                                            <th>
                                                                <?=translate("Disahkan oleh", "Endorsed By")?>
                                                            </th>
                                                        </tr>
                                                        </thead>
                                                        <tbody id="daftarIjinUsaha3">
                                                                <tr>
                                                                    <td class="nomor-seri"><?=$rekanan_ijin_usaha_siujk->getField("NO_IJIN")?></td>
                                                                    <td class="tanggal"><?=dateToPage($rekanan_ijin_usaha_siujk->getField("TANGGAL"))?></td>
                                                                    <td><?=$rekanan_ijin_usaha_siujk->getField("INSTANSI")?></td>
                                                                </tr>
                                                        </tbody>
                                                    </table>
                                                </div>                                  
                                            </td>
                                          </tr>
                                          <?php }
                                          
                                          if($paketInfo->syarat_ijin_siui > 0){
                                          if($css == "terang")	$css = "gelap";
                                          else					$css = "terang";
                                          ?>
                                          <tr class="<?=$css?>">
                                            <td valign="top"><label>Data Ijin SIUI<span class="merah">*</span></label></td>
                                            <td valign="top"><?=$arrSyaratDaftar[4]["KETERANGAN"]?></td>
                                            <td>
                                            
                                                <?php
                                                $catatan_panitia = $paket_rekanan_daftar->getCatatan($reqPaketId, $this->ID, "SIUI");
                                                if($catatan_panitia == "") {}
                                                else
                                                {
                                                ?>
                                                    <div class="area-catatan-panitia">
                                                        <div class="judul"><?=translate("Catatan Panitia", "Admin Note")?> :</div>
                                                        <div class="isi"><?=$catatan_panitia?></div>
                                                    </div>
                                                <?php
                                                }
                                                ?>                                
                                            
                                            <input type="text" readonly style="width:175px" id="reqDataIjinUsahaSIUILabel" name="reqDataIjinUsahaSIUILabel" value="<?=$status_ijin_usaha_siui?>"></input>
                                              <a  class="area-klik btn-lookup" style="text-decoration:none; font-size:12px;" title="#" onClick="openAdd('main/loadUrl/main/data_administrasi_ijin_usaha_syarat/?reqPaketId=<?=$reqPaketId?>&reqIjinUsaha=4'); return false">
                                              <img src="images/icn_search.gif" width="16"><?=translate("Lengkapi", "Browse")?></a>
            
                                                <div class="tabel-pengalaman">
                                                    <table style="margin-top:10px;">
                                                        <thead>
                                                        <tr class="judul-kolom">
                                                            <th>
                                                                <?=translate("No", "Reference Number")?>
                                                            </th>
                                                            <th>
                                                                <?=translate("Tanggal", "Issue Date")?>
                                                            </th>
                                                            <th>
                                                                <?=translate("Disahkan oleh", "Endorsed By")?>
                                                            </th>
                                                        </tr>
                                                        </thead>
                                                        <tbody id="daftarIjinUsaha4">
                                                                <tr>
                                                                    <td class="nomor-seri"><?=$rekanan_ijin_usaha_siui->getField("NO_IJIN")?></td>
                                                                    <td class="tanggal"><?=dateToPage($rekanan_ijin_usaha_siui->getField("TANGGAL"))?></td>
                                                                    <td><?=$rekanan_ijin_usaha_siui->getField("INSTANSI")?></td>
                                                                </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                                                                  
                                            </td>
                                          </tr>
                                          <?php }
                                          
                                          if($paketInfo->syarat_ijin_lain > 0){
                                          if($css == "terang")	$css = "gelap";
                                          else					$css = "terang";
                                          ?>
                                          <tr class="<?=$css?>">
                                            <td valign="top"><label>Data Ijin Lainnya<span class="merah">*</span></label></td>
                                            <td valign="top"><?=$arrSyaratDaftar[5]["KETERANGAN"]?></td>
                                            <td>
            
                                                <?php
                                                $catatan_panitia = $paket_rekanan_daftar->getCatatan($reqPaketId, $this->ID, "LAIN-LAIN");
                                                if($catatan_panitia == "") {}
                                                else
                                                {
                                                ?>
                                                    <div class="area-catatan-panitia">
                                                        <div class="judul"><?=translate("Catatan Panitia", "Admin Note")?> :</div>
                                                        <div class="isi"><?=$catatan_panitia?></div>
                                                    </div>
                                                <?php
                                                }
                                                ?>                                
                                            
                                            <input type="text" readonly style="width:175px" id="reqDataIjinUsahaLainLabel" name="reqDataIjinUsahaLainLabel" value="<?=$status_ijin_usaha_lain?>"></input>
                                              <a  class="area-klik btn-lookup" style="text-decoration:none; font-size:12px;" title="#" onClick="openAdd('main/loadUrl/main/data_administrasi_ijin_usaha_syarat/?reqPaketId=<?=$reqPaketId?>&reqIjinUsaha=5'); return false">
                                              <img src="images/icn_search.gif" width="16"><?=translate("Lengkapi", "Browse")?></a>
            
                                                <div class="tabel-pengalaman">
                                                    <table style="margin-top:10px;">
                                                        <thead>
                                                        <tr class="judul-kolom">
                                                            <th>
                                                                <?=translate("No", "Reference Number")?>
                                                            </th>
                                                            <th>
                                                                <?=translate("Tanggal", "Issue Date")?>
                                                            </th>
                                                            <th>
                                                                <?=translate("Disahkan oleh", "Endorsed By")?>
                                                            </th>
                                                        </tr>
                                                        </thead>
                                                        <tbody id="daftarIjinUsaha5">
                                                                <tr>
                                                                    <td class="nomor-seri"><?=$rekanan_ijin_usaha_lain->getField("NO_IJIN")?></td>
                                                                    <td class="tanggal"><?=dateToPage($rekanan_ijin_usaha_lain->getField("TANGGAL"))?></td>
                                                                    <td><?=$rekanan_ijin_usaha_lain->getField("INSTANSI")?></td>
                                                                </tr>
                                                        </tbody>
                                                    </table>
                                                </div>                                  
                                              
                                            </td>
                                          </tr>
                                          <?php }
                                          
                                          if($paketInfo->syarat_sbu > 0){
                                          if($css == "terang")	$css = "gelap";
                                          else					$css = "terang";
                                          ?>
                                          <tr class="<?=$css?>">
                                            <td valign="top"><label>Data Sertifikat Badan Usaha<span class="merah">*</span></label></td>
                                            <td valign="top"><?=$arrSyaratDaftar[6]["KETERANGAN"]?></td>
                                            <td>
                                            
                                                <?php
                                                $catatan_panitia = $paket_rekanan_daftar->getCatatan($reqPaketId, $this->ID, "SBU");
                                                if($catatan_panitia == "") {}
                                                else
                                                {
                                                ?>
                                                    <div class="area-catatan-panitia">
                                                        <div class="judul"><?=translate("Catatan Panitia", "Admin Note")?> :</div>
                                                        <div class="isi"><?=$catatan_panitia?></div>
                                                    </div>
                                                <?php
                                                }
                                                ?>                                
                                            
                                            <input type="text" readonly style="width:175px" id="reqDataSBU" name="reqDataSBU" value="<?=$status_sbu?>"></input>
                                              <a  class="area-klik btn-lookup" style="text-decoration:none; font-size:12px;" title="#" onClick="openAdd('main/loadUrl/main/data_administrasi_sbu_syarat/?reqPaketId=<?=$reqPaketId?>'); return false">
                                              <img src="images/icn_search.gif" width="16"><?=translate("Lengkapi", "Browse")?></a>
                                              
                                                <div class="tabel-pengalaman">
                                                    <table style="margin-top:10px;">
                                                        <thead>
                                                        <tr class="judul-kolom">
                                                            <th>
                                                                <?=translate("No", "Reference Number")?>
                                                            </th>
                                                            <th>
                                                                <?=translate("Tanggal", "Issue Date")?>
                                                            </th>
                                                            <th>
                                                                <?=translate("Disahkan oleh", "Endorsed By")?>
                                                            </th>
                                                        </tr>
                                                        </thead>
                                                        <tbody id="daftarIjinUsaha99">
                                                                <tr>
                                                                    <td class="nomor-seri"><?=$rekanan_sbu->getField("NO_IJIN")?></td>
                                                                    <td class="tanggal"><?=dateToPage($rekanan_sbu->getField("TANGGAL"))?></td>
                                                                    <td><?=$rekanan_sbu->getField("INSTANSI")?></td>
                                                                </tr>
                                                        </tbody>
                                                    </table>
                                                </div>           
                                              
                                            </td>
                                          </tr>
                                          <?php }
                                          
                                          if($paketInfo->syarat_admin_klasifikasi > 0){
                                          if($css == "terang")	$css = "gelap";
                                          else					$css = "terang";
                                          ?>
                                          <tr class="<?=$css?>">
                                            <td valign="top"><label>Data Kualifikasi
                                            <?php
                                            if($paketInfo->syarat_adm_kualifikasi_info != '')
                                                    echo ' - <label style="color:red">'.$paketInfo->syarat_adm_kualifikasi_info.'</label>';
                                            ?>
                                            <span class="merah">*</span></label></td>
                                            <td valign="top"><?=$arrSyaratDaftar[7]["KETERANGAN"]?></td>
                                            <td><input type="text" readonly style="width:175px" id="reqDataKlasifikasiLabel" name="reqDataKlasifikasiLabel" value="<?=$status_klasifikasi?>"></input>
                                              <a  class="area-klik btn-lookup" style="text-decoration:none; font-size:12px;" title="#" onClick="openAdd('main/loadUrl/main/data_administrasi_umum_syarat/?reqPaketId=<?=$reqPaketId?>'); return false">
                                              <img src="images/icn_search.gif" width="16"><?=translate("Lengkapi", "Browse")?></a>
            
                                                <div class="tabel-pengalaman">
                                                    <table style="margin-top:10px;">
                                                        <thead>
                                                        <tr class="judul-kolom">
                                                            <th>
                                                                <?=translate("Kualifikasi", "Qualification")?>
                                                            </th>
                                                        </tr>
                                                        </thead>
                                                        <tbody id="daftarKualifikasi">
                                                                <tr>
                                                                    <td><?=$tempKualifikasiNama?></td>
                                                                </tr>
                                                        </tbody>
                                                    </table>
                                                </div>           
                                                                                  
                                            </td>
                                          </tr>
                                          <?php }
                                          
                                          $paketInfo->getPaket($reqPaketId);
                                          if($paketInfo->syarat_rekening_koran > 0){
                                          if($css == "terang")	$css = "gelap";
                                          else					$css = "terang";
                                          ?>
                                          <tr class="<?=$css?>">
                                            <td valign="top"><label>Data Rekening Koran<span class="merah">*</span></label></td>
                                            <td valign="top"><?=$arrSyaratDaftar[8]["KETERANGAN"]?></td>
                                            <td>
            
                                                <?php
                                                $catatan_panitia = $paket_rekanan_daftar->getCatatan($reqPaketId, $this->ID, "REKENING_KORAN");
                                                if($catatan_panitia == "") {}
                                                else
                                                {
                                                ?>
                                                    <div class="area-catatan-panitia">
                                                        <div class="judul"><?=translate("Catatan Panitia", "Admin Note")?> :</div>
                                                        <div class="isi"><?=$catatan_panitia?></div>
                                                    </div>
                                                <?php
                                                }
                                                ?>                                
                                            
                                            <input type="text" readonly style="width:175px" id="reqDataRekeningKoranLabel" name="reqDataRekeningKoranLabel" value="<?=$status_rekening_koran?>"></input>
                                              <a  class="area-klik btn-lookup" style="text-decoration:none; font-size:12px;" title="#" onClick="openAdd('main/loadUrl/main/data_keuangan_rekening_koran_syarat/?reqPaketId=<?=$reqPaketId?>'); return false">
                                              <img src="images/icn_search.gif" width="16"><?=translate("Lengkapi", "Browse")?></a>
            
                                                <div class="tabel-pengalaman">
                                                    <table style="margin-top:10px;">
                                                        <thead>
                                                        <tr class="judul-kolom">
                                                            <th class="bulan">
                                                                <?=translate("Bulan", "Month")?>
                                                            </th>
                                                            <th class="bank">
                                                                Bank
                                                            </th>
                                                            <th>
                                                                <?=translate("Nilai", "Balance")?>
                                                            </th>
                                                        </tr>
                                                        </thead>
                                                        <tbody id="daftarRekeningKoran">
                                                            <?php
                                                            $rekanan_rekening_koran = new RekananRekeningKoran();
                                                            $rekanan_rekening_koran->selectByParams(array("K.REKANAN_ID" => $this->ID),-1,-1, " AND BULAN || TAHUN IN (".getValueArrayMonth($arrSyaratBulanRekeningKoran).") ");
                                                            while($rekanan_rekening_koran->nextRow())
                                                            {
                                                            ?>
                                                                <tr>
                                                                    <td><?=getNamePeriode($rekanan_rekening_koran->getField("PERIODE"))?></td>
                                                                    <td><?=$rekanan_rekening_koran->getField("NAMA")?></td>
                                                                    <td align="right"><?=numberToIna($rekanan_rekening_koran->getField("NILAI"))?></td>
                                                                </tr>
                                                            <?php
                                                            }
                                                            ?>
                                                        </tbody>
                                                    </table>
                                                </div>                                  
                                              
                                            </td>
                                          </tr>
                                          <?php }
                                          
                                          if($paketInfo->syarat_keuangan_spt > 0){
                                          if($css == "terang")	$css = "gelap";
                                          else					$css = "terang";
                                          ?>
                                          <tr class="<?=$css?>">
                                            <td valign="top"><label>Data SPT Tahunan
                                            <?php
                                            if($paketInfo->syarat_keuangan_info_spt != '')
                                                    echo ' - info( <label style="color:red">'.$paketInfo->syarat_keuangan_info_spt.'</label>)';
                                            ?><span class="merah">*</span></label></td>
                                            <td valign="top"><?=$arrSyaratDaftar[9]["KETERANGAN"]?></td>
                                            <td>
            
                                                <?php
                                                $catatan_panitia = $paket_rekanan_daftar->getCatatan($reqPaketId, $this->ID, "SPT");
                                                if($catatan_panitia == "") {}
                                                else
                                                {
                                                ?>
                                                    <div class="area-catatan-panitia">
                                                        <div class="judul"><?=translate("Catatan Panitia", "Admin Note")?> :</div>
                                                        <div class="isi"><?=$catatan_panitia?></div>
                                                    </div>
                                                <?php
                                                }
                                                ?>                                
                                            
                                            <input type="text" readonly style="width:175px" id="reqDataKeuanganSPT" name="reqDataKeuanganSPT" value="<?=$status_keuangan_SPT?>"></input>
                                              <a  class="area-klik btn-lookup" style="text-decoration:none; font-size:12px;" title="#" onClick="openAdd('main/loadUrl/main/data_administrasi_keuangan_spt_syarat/?reqPaketId=<?=$reqPaketId?>'); return false">
                                              <img src="images/icn_search.gif" width="16"><?=translate("Lengkapi", "Browse")?></a>
            
                                                <div class="tabel-pengalaman">
                                                    <table style="margin-top:10px;">
                                                        <thead>
                                                        <tr class="judul-kolom">
                                                            <th class="tahun">
                                                                <?=translate("Tahun", "Year")?>
                                                            </th>
                                                            <th class="tanggal">
                                                                <?=translate("Tanggal", "Issue Date")?>
                                                            </th>
                                                            <th>
                                                                <?=translate("Nomor", "SPT Number")?>
                                                            </th>
                                                        </tr>
                                                        </thead>
                                                        <tbody id="daftarSPT">
                                                                <tr>
                                                                    <td><?=$rekanan_spt->getField("TAHUN")?></td>
                                                                    <td><?=dateToPage($rekanan_spt->getField("TANGGAL"))?></td>
                                                                    <td><?=$rekanan_spt->getField("NOMOR")?></td>
                                                                </tr>
                                                        </tbody>
                                                    </table>
                                                </div>  
                                                                                  
                                            </td>
                                          </tr>
                                          <?php }
                                          
                                          
                                          if($paketInfo->syarat_neraca > 0){
                                          if($css == "terang")	$css = "gelap";
                                          else					$css = "terang";
                                          ?>
                                          <tr class="<?=$css?>">
                                            <td valign="top"><label>Data Neraca Keuangan<span class="merah">*</span></label></td>
                                            <td valign="top">Tahun <?=$paketInfo->syarat_neraca_tahun?></td>
                                            <td>
                                            
                                                <?php
                                                $catatan_panitia = $paket_rekanan_daftar->getCatatan($reqPaketId, $this->ID, "NERACA");
                                                if($catatan_panitia == "") {}
                                                else
                                                {
                                                ?>
                                                    <div class="area-catatan-panitia">
                                                        <div class="judul"><?=translate("Catatan Panitia", "Admin Note")?> :</div>
                                                        <div class="isi"><?=$catatan_panitia?></div>
                                                    </div>
                                                <?php
                                                }
                                                ?>                                
                                            
                                            <input type="text" readonly style="width:175px" id="reqDataKeuanganNeraca" name="reqDataKeuanganNeraca" value="<?=$status_keuangan_neraca?>"></input>
                                              <a  class="area-klik btn-lookup" style="text-decoration:none; font-size:12px;" title="#" onClick="openAdd('main/loadUrl/main/data_administrasi_keuangan_neraca_syarat/?reqPaketId=<?=$reqPaketId?>'); return false">
                                              <img src="images/icn_search.gif" width="16"><?=translate("Lengkapi", "Browse")?></a>
                                              
                                                <div class="tabel-pengalaman">
                                                    <table style="margin-top:10px;">
                                                        <thead>
                                                        <tr class="judul-kolom">
                                                            <th class="nomor">
                                                                <?=translate("Nomor", "Net Worth")?>
                                                            </th>
                                                            <th class="tanggal">
                                                                <?=translate("Tanggal", "Issue Date")?>
                                                            </th>
                                                            <th>
                                                                <?=translate("Kekayaan Bersih", "Net Worth")?>
                                                            </th>
                                                        </tr>
                                                        </thead>
                                                        <tbody id="daftarNeraca">
                                                            <?php
                                                            $rekanan_neraca = new RekananNeraca();
                                                            $rekanan_neraca->selectByParams(array("REKANAN_ID" => $this->ID),-1,-1, " AND TAHUN IN (".str_replace("/", ",",$paketInfo->syarat_neraca_tahun).") ");
                                                            while($rekanan_neraca->nextRow())
                                                            {
                                                            ?>
                                                                <tr>
                                                                    <td><?=$rekanan_neraca->getField("AUDIT_NOMOR")?></td>
                                                                    <td><?=dateToPage($rekanan_neraca->getField("AUDIT_TANGGAL"))?></td>
                                                                    <td align="right"><?=numberToIna($rekanan_neraca->getField("MODAL"))?></td>
                                                                </tr>
                                                            <?php
                                                            }
                                                            ?>
                                                        </tbody>
                                                    </table>
                                                </div>   
                                                                                  
                                            </td>
                                          </tr>
                                          <?php }
                                          
            
                                          if($paketInfo->syarat_keuangan_ppn > 0){
                                          if($css == "terang")	$css = "gelap";
                                          else					$css = "terang";
                                          ?>
                                          <tr class="<?=$css?>">
                                            <td valign="top"><label>Data Keuangan PPN<span class="merah">*</span></label></td>
                                            <td valign="top"><?=$arrSyaratDaftar[11]["KETERANGAN"]?></td>
                                            <td>
                                            
            
                                                <?php
                                                $catatan_panitia = $paket_rekanan_daftar->getCatatan($reqPaketId, $this->ID, "PPN");
                                                if($catatan_panitia == "") {}
                                                else
                                                {
                                                ?>
                                                    <div class="area-catatan-panitia">
                                                        <div class="judul"><?=translate("Catatan Panitia", "Admin Note")?> :</div>
                                                        <div class="isi"><?=$catatan_panitia?></div>
                                                    </div>
                                                <?php
                                                }
                                                ?>                                
                                            
                                            <input type="text" readonly style="width:175px" id="reqDataKeuanganPPN" name="reqDataKeuanganPPN" value="<?=$status_keuangan_PPN?>"></input>
                                              <a  class="area-klik btn-lookup" style="text-decoration:none; font-size:12px;" title="#" onClick="openAdd('main/loadUrl/main/data_administrasi_keuangan_pajak_syarat/?reqPaketId=<?=$reqPaketId?>&reqTipe=3'); return false">
                                              <img src="images/icn_search.gif" width="16"><?=translate("Lengkapi", "Browse")?></a>
                                              
                                                <div class="tabel-pengalaman">
                                                    <table style="margin-top:10px;">
                                                        <thead>
                                                        <tr class="judul-kolom">
                                                            <th class="bulan">
                                                                <?=translate("Bulan", "Month")?>
                                                            </th>
                                                            <th class="nomor">
                                                                <?=translate("Nomor", "Number")?>
                                                            </th>
                                                            <th>
                                                                <?=translate("Tanggal", "Issue Date")?>
                                                            </th>
                                                        </tr>
                                                        </thead>
                                                        <tbody id="daftarPajak3">
                                                            <?php
                                                            $rekanan_pajak = new RekananPajak();
                                                            $rekanan_pajak->selectByParams(array("REKANAN_ID" => $this->ID, "TIPE" => 3),-1,-1, " AND BULAN || TAHUN IN (".getValueArrayMonth($arrSyaratBulanPPN).") ");
                                                            while($rekanan_pajak->nextRow())
                                                            {
                                                            ?>
                                                                <tr>
                                                                    <td><?=getNamePeriode($rekanan_pajak->getField("PERIODE"))?></td>
                                                                    <td><?=$rekanan_pajak->getField("NOMOR")?></td>
                                                                    <td><?=dateToPage($rekanan_pajak->getField("TANGGAL"))?></td>
                                                                </tr>
                                                            <?php
                                                            }
                                                            ?>
                                                        </tbody>
                                                    </table>
                                                </div>       
                                              
                                            </td>
                                          </tr>
                                          <?php }
                                                                      
                                          
                                          if($paketInfo->syarat_keuangan_pph > 0){
                                          if($css == "terang")	$css = "gelap";
                                          else					$css = "terang";
                                          ?>
                                          <tr class="<?=$css?>">
                                            <td valign="top"><label>Data Keuangan PPh<span class="merah">*</span></label></td>
                                            <td valign="top"><?=$arrSyaratDaftar[12]["KETERANGAN"]?></td>
                                            <td>
                                            
                                                <?php
                                                $catatan_panitia = $paket_rekanan_daftar->getCatatan($reqPaketId, $this->ID, "PPH");
                                                if($catatan_panitia == "") {}
                                                else
                                                {
                                                ?>
                                                    <div class="area-catatan-panitia">
                                                        <div class="judul"><?=translate("Catatan Panitia", "Admin Note")?> :</div>
                                                        <div class="isi"><?=$catatan_panitia?></div>
                                                    </div>
                                                <?php
                                                }
                                                ?>                                
                                            
                                            <input type="text" readonly style="width:175px" id="reqDataKeuanganPPH" name="reqDataKeuanganPPH" value="<?=$status_keuangan_PPH?>"></input>
                                              <a  class="area-klik btn-lookup" style="text-decoration:none; font-size:12px;" title="#" onClick="openAdd('main/loadUrl/main/data_administrasi_keuangan_pajak_syarat/?reqPaketId=<?=$reqPaketId?>&reqTipe=2'); return false">
                                              <img src="images/icn_search.gif" width="16"><?=translate("Lengkapi", "Browse")?></a>
                                                <div class="tabel-pengalaman">
                                                    <table style="margin-top:10px;">
                                                        <thead>
                                                        <tr class="judul-kolom">
                                                            <th class="bulan">
                                                                <?=translate("Bulan", "Month")?>
                                                            </th>
                                                            <th class="nomor">
                                                                <?=translate("Nomor", "Number")?>
                                                            </th>
                                                            <th>
                                                                <?=translate("Tanggal", "Issue Date")?>
                                                            </th>
                                                        </tr>
                                                        </thead>
                                                        <tbody id="daftarPajak2">
                                                              <?php
                                                              $rekanan_pajak_pph = new RekananPajak();
                                                              $rekanan_pajak_pph->selectByParams(array("REKANAN_ID" => $this->ID, "TIPE" => 2),-1,-1, " AND BULAN || TAHUN IN (".getValueArrayMonth($arrSyaratBulanPPH).") ");
                                                              while($rekanan_pajak_pph->nextRow())
                                                              {
                                                              ?>
                                                                  <tr>
                                                                      <td><?=getNamePeriode($rekanan_pajak_pph->getField("PERIODE"))?></td>
                                                                      <td><?=$rekanan_pajak_pph->getField("NOMOR")?></td>
                                                                      <td><?=dateToPage($rekanan_pajak_pph->getField("TANGGAL"))?></td>
                                                                  </tr>
                                                              <?php
                                                              }
                                                              ?>
                                                        </tbody>
                                                    </table>
                                                </div>       
                                                                                
                                            </td>
                                          </tr>
                                          <?php }
            
                                          
                                          
                                          if($paketInfo->syarat_keuangan_pkp > 0){
                                          if($css == "terang")	$css = "gelap";
                                          else					$css = "terang";
                                          ?>
                                          <tr class="<?=$css?>">
                                            <td valign="top"><label>Data Keuangan PKP<span class="merah">*</span></label></td>
                                            <td valign="top"><?=$arrSyaratDaftar[13]["KETERANGAN"]?></td>
                                            <td>
                                            
                                                <?php
                                                $catatan_panitia = $paket_rekanan_daftar->getCatatan($reqPaketId, $this->ID, "PKP");
                                                if($catatan_panitia == "") {}
                                                else
                                                {
                                                ?>
                                                    <div class="area-catatan-panitia">
                                                        <div class="judul"><?=translate("Catatan Panitia", "Admin Note")?> :</div>
                                                        <div class="isi"><?=$catatan_panitia?></div>
                                                    </div>
                                                <?php
                                                }
                                                ?>                                
                                            
                                            
                                            <input type="text" readonly style="width:175px" id="reqDataKeuanganPKP" name="reqDataKeuanganPKP" value="<?=$status_keuangan_PKP?>"></input>
                                              <a  class="area-klik btn-lookup" style="text-decoration:none; font-size:12px;" title="#" onClick="openAdd('main/loadUrl/main/data_administrasi_keuangan_pkp_syarat/?reqPaketId=<?=$reqPaketId?>'); return false">
                                              <img src="images/icn_search.gif" width="16"><?=translate("Lengkapi", "Browse")?></a>
            
            
                                                <div class="tabel-pengalaman">
                                                    <table style="margin-top:10px;">
                                                        <thead>
                                                        <tr class="judul-kolom">
                                                            <th class="nomor">
                                                                <?=translate("No Surat", "Reference Number")?>
                                                            </th>
                                                            <th class="tanggal">
                                                                <?=translate("Tanggal", "Issue Date")?>
                                                            </th>
                                                            <th>
                                                                NPWP
                                                            </th>
                                                        </tr>
                                                        </thead>
                                                        <tbody id="daftarPKP">
                                                                <tr>
                                                                    <td><?=$tempNoSurat_PKP?></td>
                                                                    <td><?=$tempTanggal_PKP?></td>
                                                                    <td><?=$tempJabatan_PKP?></td>
                                                                </tr>
                                                        </tbody>
                                                    </table>
                                                </div>  	                           
                                            </td>
                                          </tr>
                                          <?php }
                                          
                                          if($adaPengalaman > 0){
                                          if($css == "terang")	$css = "gelap";
                                          else					$css = "terang";							  
                                          ?>
            
                                          <tr class="<?=$css?>">
                                            <td valign="top"><label>Data Teknis Pengalaman<span class="merah">*</span></label></td>
                                            <td valign="top"><?=$arrSyaratDaftar[14]["KETERANGAN"]?></td>
                                            <td>
                                                <input type="text" readonly style="width:175px" id="reqDataPengalamanLabel" name="reqDataPengalamanLabel" value="<?=$status_pengalaman?>"></input>
                                                <a class="area-klik btn-lookup" style="text-decoration:none; font-size:12px;" title="#" onClick="openAdd('main/loadUrl/main/data_pengalaman/?reqId=<?=$reqPaketId?>'); return false">
                                                <img src="images/icn_search.gif" width="16"> <?=translate("Lengkapi", "Browse")?></a>
            
                                                <div class="tabel-pengalaman">
                                                    <table style="margin-top:10px;">
                                                        <thead>
                                                        <tr class="judul-kolom">
                                                            <th>
                                                                <?=translate("Daftar Pengalaman", "Experience")?>
                                                            </th>
                                                            <th class="aksi">
                                                                <?=translate("Aksi", "Action")?>
                                                            </th>
                                                        </tr>
                                                        </thead>
                                                        <tbody id="daftarPengalaman">
                                                            <?php
                                                            $rekanan_daftar_pengalaman->selectByParams(array("A.PAKET_ID" => $reqPaketId, "A.REKANAN_ID" => $this->ID));
                                                            
                                                            while($rekanan_daftar_pengalaman->nextRow())
                                                            {
                                                                $daftarId = $rekanan_daftar_pengalaman->getField("REKANAN_DAFTAR_PENGALAMAN_ID");
                                                                $catatan = $rekanan_daftar_pengalaman->getField("CATATAN");
                                                            ?>
                                                                <tr>
                                                                    <td><input type="hidden" name="reqPengalamanSyarat[]"><?=$rekanan_daftar_pengalaman->getField("REKANAN_PENGALAMAN")?></td>
                                                                    <td align="center">
                                                                    <?php
                                                                    if($catatan == "")
                                                                        echo '<img src="images/accept.png">';
                                                                    else
                                                                    {
                                                                    ?>
                                                                        <script type="text/javascript">
                                                                        $(function() {
                                                                            $('#pengalamanBox<?=$daftarId?>').cluetip({splitTitle: '|', showTitle:true, cluetipClass: 'jtip', dropShadow:false, positionBy:'fixed'});
                                                                            
                                                                        });
                                                                        </script>   
                                                                        <a id="pengalamanBox<?=$daftarId?>" class="clueTipBox" title="Catatan|<?=$catatan?>"><img src="images/icon-hapus.png" width="15" height="15"></a>
                                                                    <?php
                                                                    }
                                                                    ?>
                                                                    </td>
                                                                </tr>
                                                            <?php
                                                            }
                                                            ?>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </td>
                                          </tr>
                                          
                                          <?php
                                          }
                                          if($paketInfo->syarat_teknis_tenaga_ahli > 0){
                                          if($css == "terang")	$css = "gelap";
                                          else					$css = "terang";
                                          ?>
                                          <tr class="<?=$css?>">
                                            <td valign="top"><label>Data Teknis Tenaga Ahli<span class="merah">*</span></label></td>
                                            <td valign="top"><?=$arrSyaratDaftar[15]["KETERANGAN"]?></td>
                                            <td><input type="text" readonly style="width:175px" id="reqDataTeknisTenagaAhliLabel" name="reqDataTeknisTenagaAhliLabel" value="<?=$status_tenaga_ahli?>"></input>
                                                <a class="area-klik btn-lookup" style="text-decoration:none; font-size:12px;" title="#" onClick="openAdd('main/loadUrl/main/data_tenaga_ahli/?reqId=<?=$reqPaketId?>'); return false">
                                                <img src="images/icn_search.gif" width="16"> <?=translate("Lengkapi", "Browse")?></a>
            
                                                <div class="tabel-pengalaman">
                                                    <table style="margin-top:10px;">
                                                        <thead>
                                                        <tr>
                                                            <th class="judul-kolom">
                                                                <?=translate("Daftar Tenaga Ahli", "Experts")?>
                                                            </th>
                                                            <th class="aksi">
                                                                <?=translate("Aksi", "Action")?>
                                                            </th>
                                                        </tr>
                                                        </thead>
                                                        <tbody id="daftarTenagaAhli">
                                                            <?php
                                                            $rekanan_daftar_tenaga_ahli->selectByParams(array("A.PAKET_ID" => $reqPaketId, "A.REKANAN_ID" => $this->ID));
                                                            while($rekanan_daftar_tenaga_ahli->nextRow())
                                                            {
                                                                $daftarId = $rekanan_daftar_tenaga_ahli->getField("REKANAN_DAFTAR_TENAGA_AHLI_ID");
                                                                $catatan = $rekanan_daftar_tenaga_ahli->getField("CATATAN");
                                                            ?>
                                                                <tr>
                                                                    <td><input type="hidden" name="reqTenagaAhliSyarat[]"><?=$rekanan_daftar_tenaga_ahli->getField("REKANAN_TENAGA_AHLI")?></td>
                                                                    <td align="center">
                                                                    <?
                                                                    if($catatan == "")
                                                                        echo '<img src="images/accept.png">';
                                                                    else
                                                                    {
                                                                    ?>
                                                                        <script type="text/javascript">
                                                                        $(function() {
                                                                            $('#tenagaBox<?=$daftarId?>').cluetip({splitTitle: '|', showTitle:true, cluetipClass: 'jtip', dropShadow:false, positionBy:'fixed'});
                                                                            
                                                                        });
                                                                        </script>   
                                                                        <a id="tenagaBox<?=$daftarId?>" class="clueTipBox" title="Catatan|<?=$catatan?>"><img src="images/icon-hapus.png" width="15" height="15"></a>
                                                                    <?php
                                                                    }
                                                                    ?>
                                                                    </td>
                                                                </tr>
                                                            <?php
                                                            }
                                                            ?>
                                                        </tbody>
                                                    </table>
                                                </div>                                  
                                              
                                            </td>
                                          </tr>
                                          <?php }
                                          
                                          if($paketInfo->syarat_teknis_peralatan > 0){
                                          if($css == "terang")	$css = "gelap";
                                          else					$css = "terang";
                                          ?>
                                          <tr class="<?=$css?>">
                                            <td valign="top"><label>Data Teknis Peralatan<span class="merah">*</span></label></td>
                                            <td valign="top"><?=$arrSyaratDaftar[16]["KETERANGAN"]?></td>
                                            <td><input type="text" readonly style="width:175px" id="reqDataPeralatanLabel" name="reqDataPeralatanLabel" value="<?=$status_peralatan?>"></input>
            
                                                <a class="area-klik btn-lookup" style="text-decoration:none; font-size:12px;" title="#" onClick="openAdd('main/loadUrl/main/data_peralatan/?reqId=<?=$reqPaketId?>'); return false">
                                                <img src="images/icn_search.gif" width="16"> <?=translate("Lengkapi", "Browse")?></a>
            
                                                <div class="tabel-pengalaman">
                                                    <table style="margin-top:10px;">
                                                        <thead>
                                                        <tr>
                                                            <th class="judul-kolom">
                                                                <?=translate("Daftar Peralatan", "Equipments")?>
                                                            </th>
                                                            <th class="aksi">
                                                                <?=translate("Aksi", "Action")?>
                                                            </th>
                                                        </tr>
                                                        </thead>
                                                        <tbody id="daftarPeralatan">
                                                            <?php
                                                            $rekanan_daftar_peralatan->selectByParams(array("A.PAKET_ID" => $reqPaketId, "A.REKANAN_ID" => $this->ID));
                                                            while($rekanan_daftar_peralatan->nextRow())
                                                            {
                                                                $daftarId = $rekanan_daftar_peralatan->getField("REKANAN_DAFTAR_PERALATAN_ID");
                                                                $catatan = $rekanan_daftar_peralatan->getField("CATATAN");
                                                            ?>
                                                                <tr>
                                                                    <td><input type="hidden" name="reqPeralatanSyarat[]"><?=$rekanan_daftar_peralatan->getField("REKANAN_PERALATAN")?></td>
                                                                    <td align="center">
                                                                    <?php
                                                                    if($catatan == "")
                                                                        echo '<img src="images/accept.png">';
                                                                    else
                                                                    {
                                                                    ?>
                                                                        <script type="text/javascript">
                                                                        $(function() {
                                                                            $('#peralatanBox<?=$daftarId?>').cluetip({splitTitle: '|', showTitle:true, cluetipClass: 'jtip', dropShadow:false, positionBy:'fixed'});
                                                                            
                                                                        });
                                                                        </script>   
                                                                        <a id="peralatanBox<?=$daftarId?>" class="clueTipBox" title="Catatan|<?=$catatan?>"><img src="images/icon-hapus.png" width="15" height="15"></a>
                                                                    <?php
                                                                    }
                                                                    ?>
                                                                    </td>
                                                                </tr>
                                                            <?php
                                                            }
                                                            ?>
                                                        </tbody>
                                                    </table>
                                                </div>                                             
                                            </td>
                                          </tr>
                                          <?php }
                                          
                                          if($paketInfo->syarat_teknis_sertifikat > 0){
                                          if($css == "terang")	$css = "gelap";
                                          else					$css = "terang";
                                          ?>
                                          <tr class="<?=$css?>">
                                            <td valign="top"><label>Data Teknis Sertifikat Lainnya
                                            <span class="merah">*</span></label></td>
                                            <td valign="top"><?=$arrSyaratDaftar[17]["KETERANGAN"]?></td>
                                            <td><input type="text" readonly style="width:175px" id="reqDataSertifikatLabel" name="reqDataSertifikatLabel" value="<?=$status_sertifikat?>"></input>
                                                <a class="area-klik btn-lookup" style="text-decoration:none; font-size:12px;" title="#" onClick="openAdd('main/loadUrl/main/data_sertifikat/?reqId=<?=$reqPaketId?>'); return false">
                                                <img src="images/icn_search.gif" width="16"> <?=translate("Lengkapi", "Browse")?></a>
            
                                                <div class="tabel-pengalaman">
                                                    <table style="margin-top:10px;">
                                                        <thead>
                                                        <tr class="judul-kolom">
                                                            <th>
                                                                <?=translate("Daftar Sertifikat Lain", "Certificate")?>
                                                            </th>
                                                            <th class="aksi">
                                                                <?=translate("Aksi", "Action")?>
                                                            </th>
                                                        </tr>
                                                        </thead>
                                                        <tbody id="daftarSertifikat">
                                                            <?php
                                                            $rekanan_daftar_sertifikat->selectByParams(array("A.PAKET_ID" => $reqPaketId, "A.REKANAN_ID" => $this->ID));
                                                            while($rekanan_daftar_sertifikat->nextRow())
                                                            {
                                                                $daftarId = $rekanan_daftar_sertifikat->getField("REKANAN_DAFTAR_SERTIFIKAT_ID");
                                                                $catatan = $rekanan_daftar_sertifikat->getField("CATATAN");
                                                            ?>
                                                                <tr>
                                                                    <td><input type="hidden" name="reqSertifikatSyarat[]"><?=$rekanan_daftar_sertifikat->getField("REKANAN_SERTIFIKAT")?></td>
                                                                    <td align="center">
                                                                    <?php
                                                                    if($catatan == "")
                                                                        echo '<img src="images/accept.png">';
                                                                    else
                                                                    {
                                                                    ?>
                                                                        <script type="text/javascript">
                                                                        $(function() {
                                                                            $('#sertifikatBox<?=$daftarId?>').cluetip({splitTitle: '|', showTitle:true, cluetipClass: 'jtip', dropShadow:false, positionBy:'fixed'});
                                                                            
                                                                        });
                                                                        </script>   
                                                                        <a id="sertifikatBox<?=$daftarId?>" class="clueTipBox" title="Catatan|<?=$catatan?>"><img src="images/icon-hapus.png" width="15" height="15"></a>
                                                                    <?php
                                                                    }
                                                                    ?>
                                                                    </td>
                                                                </tr>
                                                            <?php
                                                            }
                                                            ?>
                                                        </tbody>
                                                    </table>
                                                </div>   
            
                                            </td>
                                          </tr>
                                          <?php }
                                          ?>
                                          <?php
                                          for($i=0;$i<count($arrSyaratLain);$i++)
                                          {
                                            if($css == "terang")	$css = "gelap";
                                            else					$css = "terang";								  
                                          ?>
                                              <tr class="<?=$css?>">
                                                <td><label><?=$arrSyaratLain[$i]["NAMA"]?>
                                                <span class="merah">*</span></label></td>
                                                <td><?=$arrSyaratLain[$i]["KETERANGAN"]?></td>
                                                <td><input type="text" readonly style="width:175px" id="reqSyaratLain<?=$arrSyaratLain[$i]["ID"]?>" name="reqSyaratLain<?=$arrSyaratLain[$i]["ID"]?>" value="<?=$arrSyaratLain[$i]["KELENGKAPAN"]?>" title="<?=translate("Upload terlebih dahulu persyaratan", "Please upload a file.")?>" required></input>
                                                  <a  class="area-klik btn-lookup" style="text-decoration:none; font-size:12px;" title="#" onClick="openAdd('main/loadUrl/main/data_lain_syarat/?reqId=<?=$arrSyaratLain[$i]["ID"]?>&reqCaption=<?=$arrSyaratLain[$i]["NAMA"]?>', 'e-Procurement PT. Angkasa Pura Suport', 'width=945px,height=400px,left=80px,top=40px,resize=1,scrolling=1,midle=1'); return false">
                                                  <img src="images/icn_search.gif" width="16"><?=translate("Lengkapi", "Browse")?></a>
                                                </td>
                                              </tr>                              
                                          <?php
                                          }
                                          ?>
                                          <tr>
                                            <td colspan="3" align="center">
                                            	<input type="hidden" name="reqPaketId" value="<?=md5($this->ID.$reqPaketId)?>">
                                                <input type="hidden" name="reqKirim" id="reqKirim"/>
                                                <a onClick="setIdWithValue('reqKirim','Simpan');$('#alumniForm').submit()" style="cursor:pointer" class="btn-daftar"><?=translate("Daftar", "Register")?></a>
                                            </td>
                                          </tr>                              
                                      </table>



                                        <div class="col-md-8">
                                        </div>
                                    </div>
                                </div>
							</div>
                            
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                    	<label for="inputEmail" class="col-md-2 control-label">&nbsp;</label>
                                        <div class="col-md-4">
                                              <a href="main/index/paket_detil/?reqId=<?=$reqId?>" class="btn-kembali">Kembali</a>
                                        	<button type="submit" class="btn btn-primary">Tambah</button>
                                            <button type="submit" class="btn btn-primary">Simpan</button>
                                        </div>
                                    </div>
                                </div>
							</div>
						</form> 
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
