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
$this->load->model("PaketBidangUsaha");

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

// cek blacklist

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
// echo $jenis_tahap; die();
// http://localhost:8011/eprocv13/main/index/registrasi_paket/?reqPaketId=75883741f77a612cdff0a828f94792d1

$paket_pendaftaran = new Paket();
$pendaftaran = $paket_pendaftaran->getPaketPendaftaran($reqPaketId,$arrPengaftaran[$jenis_tahap]);
// echo $pendaftaran; die();
if($this->USER_TYPE_ID != "6" || $pendaftaran == 0 || $reqPaketId == "0") // tolak jika bukan Penyedia, diluar jam pendaftaran, kode encrypt (ID+peket_id) tidak cocok
redirect(base_url('main'));
//rekening koran
$paketInfo->getPaket($reqPaketId);

$reqTahun = getYear($paketInfo->tanggal_tahap);
$reqBulan = (int)getMonth($paketInfo->tanggal_tahap);
$reqUUID = $paketInfo->uuid;

$paket_rekanan->selectByParams(array("A.PAKET_ID" => $reqPaketId, "A.REKANAN_ID" => $this->ID));
$paket_rekanan->firstRow();

if ($paket_rekanan->countRow() > 0) {// Penyedia yang sudah daftar tidak bisa balik kehalaman pendaftaran
  redirect(base_url('main/index/paket_detil/?reqId='.$reqPaketId));
}
?>

<script>
	$(function(){
		$('#ff').form({
			url:'paket_json/daftar',
			onSubmit:function(){
				var v=$(this).form('validate');
				  if(v) {
				    showLoad();
				    return v;
				  } else {
				    hideLoad();
				    return false;
				  }
			},
			success:function(data){
				if(data == "Registrasi paket berhasil.")
					document.location.href = "main/index/paket_detil/?eid=<?=$reqPaketId?>&key=<?=$reqUUID?>";
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

	            <div class="alert alert-info">
	            	<b style="font-size: 16px">
	            		Apakah anda yakin ingin mendaftar paket <?=$paketInfo->nama; ?> ?
	            	</b>
	        	</div>

		        <div class="form-actions">
		        	<input type="hidden" name="reqPaketId" value="<?=md5($this->ID.$reqPaketId)?>">
		            <input type="hidden" name="reqKirim" id="reqKirim" value="Simpan"/>
				    <a href="main/index/tender" class="<?= CLASS_BTN_DANGER ?>"> <i class="fa fa-arrow-left"></i> Kembali </a>
				    <button type="submit" class="<?= CLASS_BTN_PRIMARY ?> pull-right">Lanjut Daftar <i class="fa fa-arrow-right"></i></button>
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
