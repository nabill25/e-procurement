<?php 
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 * 
 */
if($this->USER_TYPE_ID == "")
    redirect("main");

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
$this->load->model("PaketRekanan");
$this->load->model("PaketTahap");


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
$paket_rekanan = new PaketRekanan();

$reqPaketId= $this->input->get("reqPaketId");

$reqPaketId = $paket_getid->getPaketId(array("MD5('".$this->ID."' || A.PAKET_ID)" => $reqPaketId));

$arrPengaftaran = PENDAFTARAN;
$paket_tahap_metode = new PaketTahap();
$jenis_tahap = $paket_tahap_metode->getJenisTahapById($reqPaketId);

// http://localhost:8011/eprocv13/main/index/registrasi_paket/?reqPaketId=75883741f77a612cdff0a828f94792d1

$paket_pendaftaran = new Paket();
$pendaftaran = $paket_pendaftaran->getPaketPendaftaran($reqPaketId,$arrPengaftaran[$jenis_tahap]);
// echo $reqPaketId; die();
if($this->USER_TYPE_ID != "6" || $pendaftaran == 0 || $reqPaketId == "0") // tolak jika bukan Penyedia, diluar jam pendaftaran, kode encrypt (ID+peket_id) tidak cocok
    redirect(base_url('main'));
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

$paket_rekanan->selectByParams(array("A.PAKET_ID" => $reqPaketId, "A.REKANAN_ID" => $this->ID));
$paket_rekanan->firstRow();


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

	$(function(){
		$('#ff').form({
			url:'paket_json/daftar',
			onSubmit:function(){
				return $(this).form('validate');
			},
			success:function(data){
				if(data == "Registrasi paket berhasil.")
					document.location.href = "main/index/paket_detil/?reqId=<?=$reqPaketId?>";
			}
		});

	
		// extend the 'equals' rule
		$.extend($.fn.validatebox.defaults.rules, {
			kelengkapan: {
				validator: function(value,param){
					return value == "Data Lengkap";
				},
				message: 'Data belum lengkap.'
			}
		});
				
	});

	
</script>

<div class="row"> 
  <div class="col-md-12 col-sm-12">
    <div class="card">
      <div class="card-header card-head-inverse bg-primary">
        <h4 class="card-title text-white">Pendaftaran Paket Tender  
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

	    	<?php
			if($paket_rekanan->getField("LULUS_PENDAFTARAN_KETERANGAN") == "")
			{}
			else
			{
			?>
	            <div class="alert alert-danger">
	                <ul>
	                    <li>Pendaftaran paket anda ditolak dengan alasan : <?=$paket_rekanan->getField("LULUS_PENDAFTARAN_KETERANGAN")?>.</li>
	                </ul>
	            </div>                          
	        <?php
			}
			?>
	        <form id="ff" class="easyui-form form-horizontal" method="post" enctype="multipart/form-data">
				
	            <div class="alert alert-info">Syarat dan Ketentuan : <br><b style="font-size: 16px">
	            	<?=translate("Untuk melakukan pendaftaran paket di ".SYSTEM_NAME." ".SYSTEM_NAME_PT.", diharapkan untuk melengkapi data sebagai berikut:", "Please fill out the form below and click Submit to submit your application for consideration. <font color='red'>Fields with an asterisk (*) are required.</font>")?></b>
	        	</div>

		          <table class="syarat-ketentuan table table-bordered table-hover">
		                <?php
		                if($adaPersyaratan > 0)
		                {
		                ?>  
		                  <tr>
		                      <th align="center"><?=translate("Persyaratan", "Requirements")?></th>
		                      <th align="center"><?=translate("Informasi Tambahan", "Additional Information")?></th>
		                      <th align="center"><?=translate("Kelengkapan", "Applications")?></th>
		                  </tr>
		               <?php
		                }
		                else
		                {
		               ?>
		                  <tr>
		                    <td colspan="3">
		                    		<i class="fa fa-hand-o-right"></i> Data Administrasi <br>
		                    		<i class="fa fa-hand-o-right"></i> Data Keuangan <br> 
		                    		<i class="fa fa-hand-o-right"></i> Data Perpajakan <br> 
		                    		<i class="fa fa-hand-o-right"></i> Data Teknis <br> 
		                    		<i class="fa fa-hand-o-right"></i> Dan sudah terverifikasi serta memiliki SKT (Surat Keterangan Terdaftar) <?=SYSTEM_NAME." ".SYSTEM_NAME_PT ?> <br> 
		                    		<br>
		                    		Silahkan klik tombol <b>'Lanjut'</b> di bawah ini untuk melanjutkan proses pendaftaran paket. 
	                    	</td>
		                  </tr>                           
		               <?php
		                }
		               ?>
		                   <?php
		                  $css = "terang";
		                  if($adaAktaPendirian > 0){ 
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
		                        <div class="card-block">
						            <fieldset>
						              <div class="input-group"> 

		                      			<input type="text" readonly style="width: auto" class="info-kelengkapan-pendaftaran form-control easyui-validatebox" data-options="required:true,validType:'kelengkapan'" id="reqDataLandasanHukumLabel" name="reqDataLandasanHukumLabel" value="<?=$status_landasan_hukum?>"></input>
						                <div class="input-group-append">
		                      				<a  class="area-klik btn btn-info" style="text-decoration:none; font-size:12px; color: #fff" title="#" onClick="openAdd('main/loadUrl/main/data_administrasi_landasan_hukum_syarat/?&reqAktaType=1')">
					                      	
					                      <i class="fa fa-search"></i> <?=translate("Lengkapi", "Browse")?></a>
						                </div>
						              </div>
						            </fieldset>
					          	</div>
					          	

		                        <div class="tabel-pengalaman" style="margin-top: 10px">
		                            <table class="table table-striped">
		                                <thead>
		                                <tr>
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
		                  // if($paketInfo->syarat_ijin_siup > 0 && $reqPaketId <= 532){
		                  if($paketInfo->syarat_ijin_siup > 0){
		                  ?>
		                  <tr>
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
		                        
		                        <div class="card-block">
						            <fieldset>
						              <div class="input-group">
		                        		<input type="text" readonly style="width: auto" class="info-kelengkapan-pendaftaran form-control easyui-validatebox" data-options="required:true,validType:'kelengkapan'" id="reqDataIjinUsahaLabel" name="reqDataIjinUsahaLabel" value="<?=$status_ijin_usaha?>">
						                <div class="input-group-append">
					                      <a  class="area-klik btn btn-info" style="text-decoration:none; font-size:12px; color: #fff" title="#" onClick="openAdd('main/loadUrl/main/data_administrasi_ijin_usaha_syarat/?reqPaketId=<?=$reqPaketId?>&reqIjinUsaha=1'); return false">
					                      <i class="fa fa-search"></i> <?=translate("Lengkapi", "Browse")?></a>
						                </div>
						              </div>
						            </fieldset>
					          	</div>

		                        <div class="tabel-pengalaman" style="margin-top: 10px">
		                            <table class="table table-striped">
		                                <thead>
		                                <tr>
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

		                  if($paketInfo->syarat_sbu > 0){
		                  ?>
		                  <tr>
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
		                    
			                    <div class="card-block">
						            <fieldset>
						              <div class="input-group">
			                    		<input type="text" readonly style="width: auto" class="info-kelengkapan-pendaftaran form-control easyui-validatebox" data-options="required:true,validType:'kelengkapan'" id="reqDataSBU" name="reqDataSBU" value="<?=$status_sbu?>"></input>
						                <div class="input-group-append">
					                      <a  class="area-klik btn btn-info" style="text-decoration:none; font-size:12px; color: #fff" title="#" onClick="openAdd('main/loadUrl/main/data_administrasi_sbu_syarat/?reqPaketId=<?=$reqPaketId?>'); return false">
					                      <i class="fa fa-search"></i> <?=translate("Lengkapi", "Browse")?></a>
						                </div>
						              </div>
						            </fieldset>
					          	</div>

		                      
		                        <div class="tabel-pengalaman" style="margin-top: 10px">
		                            <table class="table table-striped">
		                                <thead>
		                                <tr>
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
		                  ?>
		                                   
		          </table> 
		        <div class="form-actions">
		        	<input type="hidden" name="reqPaketId" value="<?=md5($this->ID.$reqPaketId)?>">
		            <input type="hidden" name="reqKirim" id="reqKirim" value="Simpan"/>
				    <a href="main/index/paket_lelang" class="btn btn-danger mr-1 text-white"> <i class="fa fa-arrow-left"></i> Kembali </a> 
				    <button type="submit" class="btn btn-primary pull-right">Lanjut <i class="fa fa-arrow-right"></i></button>
			  	</div> 

				<!-- <div>
		            <a href="main/index/paket_lelang" class="btn btn-danger">Batal</a>
		            <button type="submit" class="btn btn-primary">Lanjut >> </button>
				</div> -->
			</form> 
        	
        </div>
      </div>
    </div>
  </div> 
</div>  