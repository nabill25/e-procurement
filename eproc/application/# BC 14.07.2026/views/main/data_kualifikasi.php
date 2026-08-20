<?php
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 * 
 */

if($this->USER_TYPE_ID == "")
	redirect("main");
// Waktu Pemasukan Penawaran mulai dari Pemasukan data kualifikasi s/d Evaluasi data kualifikasi 

include_once("functions/string.func.php");
include_once("functions/date.func.php");
include_once("functions/default.func.php");
include_once("functions/recordcoloring.func.php");

$this->load->library("paketinfo"); $paketInfo = new paketinfo();
$this->load->model("RekananEvaluasiPersonil");
$this->load->model("RekananEvaluasiPeralatan");
$this->load->model("RekananEvaluasiSertifikatLain");
$this->load->model("PaketEvaluasiSertifikatLain");
$this->load->model("PaketEvaluasiPeralatan");
$this->load->model("PaketEvaluasiPeralatanDetil");
$this->load->model("PaketEvaluasiPersonil");
$this->load->model("PaketRekanan");
$this->load->model("RekananEvaluasiKeuangan");
$this->load->model("RekananEvaluasiPengalaman");
$this->load->model("RekananEvaluasiAdmin");
$this->load->model("RekananPengalaman");
$this->load->model("RekananIjinUsaha");
$this->load->model("RekananNeraca");
$this->load->model("RekananRekeningKoran");
$this->load->model("RekananBidangUsaha");
$this->load->model("RekananAkta");
$this->load->model("RekananSaham");
$this->load->model("RekananPajak");
$this->load->model("RekananPengurus");
$this->load->model("PaketEvaluasiAdmin");
$this->load->model("PaketEvaluasiKeuangan");
$this->load->model("PaketKriteriaEvaluasi");
$this->load->model("PaketEvaluasiKemampuanDasar");
$this->load->model("PaketBidangUsaha");
$this->load->model("PaketEvaluasiPengalaman");
$this->load->model("PaketTahap");
$this->load->model("PaketRekananKualifikasi");
$this->load->model("PaketPaktaIntegritas");
$this->load->model("PaketPernyataanMinat");


$paket_evaluasi_admin = new PaketEvaluasiAdmin();
$paket_kriteria_evaluasi = new PaketKriteriaEvaluasi();
$paket_rekanan = new PaketRekanan();
$paket_tahap = new PaketTahap();
$paket_tahap_metode = new PaketTahap();
$paket_pakta_integritas = new PaketPaktaIntegritas();
$paket_pernyataan_minat = new PaketPernyataanMinat();

$reqId = $this->input->get("reqId");

$paketInfo->getPaket($reqId);
$reqNama = $paketInfo->nama;
$reqKualifikasi = $paketInfo->kualifikasi;
$reqKualifikasiId = $paketInfo->kualifikasi_id;
$reqNilai = $paketInfo->nilai;
$reqTahun = getYear($paketInfo->tanggal_tahap);
$reqBulan = (int)getMonth($paketInfo->tanggal_tahap);

$FILE_DIR_KUALIFIKASI = "uploads/kualifikasi/";

$jenis_tahap = $paket_tahap_metode->getJenisTahapById($reqId);
//$arrEvaluasiKualifikasi  = array(0, 5, 6, 5, 6, 5, 6, 5, 5);
//$arrEvaluasiKualifikasi1 = array(0, 6, 8, 6, 8, 6, 8, 6, 6);
// echo $jenis_tahap; die(); 
$arrEvaluasiKualifikasi  = array(0, 5,  6,  5,  6,  5,  6,  5,  5,  0, 0, 5,  0,  5,  0);
$arrEvaluasiKualifikasi1 = array(0, 6,  8,  6,  8,  6,  8,  6,  6,  0, 0, 6,  0,  6,  0);

$aktif_dok_kualifikasi1 = $paket_tahap->getCountByParamsAktif(array("URUT" => $arrEvaluasiKualifikasi[$jenis_tahap], "PAKET_ID" => $reqId, "TAMPILKAN" => 1));
$aktif_dok_kualifikasi2 = $paket_tahap->getCountByParamsAktif(array("URUT" => $arrEvaluasiKualifikasi1[$jenis_tahap], "PAKET_ID" => $reqId, "TAMPILKAN" => 1));

if($aktif_dok_kualifikasi1 > 0  || $aktif_dok_kualifikasi2 > 0)
	$aktif_entri = 1;
else
	$aktif_entri = 0;

$reqPaketRekananId = $paket_rekanan->getPaketRekananId($reqId, $this->ID);

$paket_evaluasi_admin->selectByParamsProses(array("PAKET_ID" => $reqId));
$i = 0;
while($paket_evaluasi_admin->nextRow())
{
	$arrIdEvaluasiAdmin[$i] = $paket_evaluasi_admin->getField("EVALUASI_NUMBER"); 
	$arrNamaEvaluasiAdmin[$i] = $paket_evaluasi_admin->getField("NAMA"); 
	$i++;
}
$paket_kriteria_evaluasi->selectByParams(array("PAKET_ID" => $reqId));
$paket_kriteria_evaluasi->firstRow();

function checkEvaluasi($arr, $search)
{
	if(count($arr) == 0)
		return false;
	
	if (in_array($search, $arr))
		return true;
	else
		return false;
}

$number = 1;

$index_rekanan_evaluasi_pengalaman= 0;
$rekanan_evaluasi_pengalaman= new RekananEvaluasiPengalaman();
$rekanan_evaluasi_pengalaman->selectByParams(array("PAKET_REKANAN_ID" => $reqPaketRekananId),-1,-1);
while($rekanan_evaluasi_pengalaman->nextRow())
{
	$reqRekananPengalamanId= $rekanan_evaluasi_pengalaman->getField("REKANAN_PENGALAMAN_ID");
	$rekanan_pengalaman= new RekananPengalaman();
	$rekanan_pengalaman->selectByParams(array("REKANAN_PENGALAMAN_ID" => $reqRekananPengalamanId));
	while($rekanan_pengalaman->nextRow())
	{
		$arrEvalusasiPengalaman[$index_rekanan_evaluasi_pengalaman]["REKANAN_PENGALAMAN_ID"] = $rekanan_pengalaman->getField("REKANAN_PENGALAMAN_ID");
		$arrEvalusasiPengalaman[$index_rekanan_evaluasi_pengalaman]["NAMA"] = $rekanan_pengalaman->getField("NAMA");
		$arrEvalusasiPengalaman[$index_rekanan_evaluasi_pengalaman]["KONTRAK_NILAI"] = $rekanan_pengalaman->getField("KONTRAK_NILAI");
		$arrEvalusasiPengalaman[$index_rekanan_evaluasi_pengalaman]["JO"] = $rekanan_pengalaman->getField("JO");
		$arrEvalusasiPengalaman[$index_rekanan_evaluasi_pengalaman]["PENGALAMAN_BIDANG"] = $rekanan_pengalaman->getField("PENGALAMAN_BIDANG");
		$index_rekanan_evaluasi_pengalaman++;
	}
}
$rekanan_evaluasi_admin = new RekananEvaluasiAdmin();
$rekanan_evaluasi_admin->selectByParams(array("PAKET_REKANAN_ID" => $reqPaketRekananId));
$arrRekananAdmin[0] = "";
$i=1;
while($rekanan_evaluasi_admin->nextRow())
{
	$arrRekananAdmin[$rekanan_evaluasi_admin->getField("EVALUASI_NUMBER")] = $rekanan_evaluasi_admin->getField("URAIAN");
	$arrLinkFileDataAdministrasi[$rekanan_evaluasi_admin->getField("EVALUASI_NUMBER")] = $rekanan_evaluasi_admin->getField("PATH_FILE");
	$i++;	
}
// echo "<pre>"; print_r($arrRekananAdmin); die();
?>
<script language="javascript">

	$(function(){
		$('#ff').form({
			url:'paket_json/data_kualifikasi',
			onSubmit:function(){
				return $(this).form('validate');
			},
			success:function(data){
				$.messager.alert('Info', data, 'info');	
			}
		});
		
	});

	var varPengalamanNumber;
	function setPengalamanNumber(number)
	{
		//alert(number);
		this.varPengalamanNumber = number;
	}

	function addKualifikasiPengalaman(id, nama, bidang_usaha, nilai, jo)
	{
		$("#reqPengalaman"+varPengalamanNumber).val(nama);
		document.getElementById('reqPengalamanId'+varPengalamanNumber).value = id;
		document.getElementById('reqPengalamanLabel'+varPengalamanNumber).value = bidang_usaha;		
		document.getElementById('reqPengalamanNilai'+varPengalamanNumber).value = nilai;		
		document.getElementById('reqStatusPenyedia'+varPengalamanNumber).value = jo;
	}


	var varTenagaAhliNumber;
	function setTenagaAhliNumber(number)
	{
		this.varTenagaAhliNumber = number;		
	}

	function addKualifikasiPersonil(id, nama)
	{
		document.getElementById('reqPersonilId' + varTenagaAhliNumber).value = id;
		document.getElementById('reqPersonil' + varTenagaAhliNumber).value = nama;
		setElementValue('HapusPersonil' + varTenagaAhliNumber, '');
	}
	
	function setPeralatanNumber(number)
	{
		this.varPeralatanNumber = number;
	}


	function addKualifikasiPeralatan(id, nama)
	{
		document.getElementById('reqPeralatanId'+varPeralatanNumber).value = id;
		document.getElementById('reqPeralatan'+varPeralatanNumber).value = nama;
		setElementValue('HapusPeralatan'+varPeralatanNumber, '');
	}

	
	var varSertifikatLainNumber;		
	function setSertifikatLainNumber(number)
	{
		this.varSertifikatLainNumber = number;		
	}

	function addKualifikasiSertifikatLain(id, nama)
	{
		document.getElementById('reqSertifikatLainId'+ varSertifikatLainNumber).value = id;
		document.getElementById('reqSertifikatLain'+ varSertifikatLainNumber).value = nama;
		setElementValue('HapusSertifikatLain'+ varSertifikatLainNumber, '');
	}
	
					
	function addRowPenilaianJumlahPengalaman(tableID) {
		var table = document.getElementById(tableID);
		
		var rowCount = table.rows.length;
		var row = table.insertRow(rowCount);
		
		var jumlah= rowCount+1;
		var cell1 = row.insertCell(0);
		cell1.innerHTML =
		'<input type="hidden" name="reqPengalamanId[]" id="reqPengalamanId'+jumlah+'" /> '+
		'<input type="text" id="reqPengalaman'+jumlah+'" style="width:450px; " readonly="readonly" />'+
		//'<label id="reqPengalaman'+jumlah+'"></label>'+
		'<a onclick="setPengalamanNumber(\''+jumlah+'\'); openAdd(\'main/loadUrl/app/data_pengalaman_kualifikasi/?reqId=<?=$reqId?>&reqJumlah='+jumlah+'\'); return false"'+
		'style="cursor:pointer"><img src="images/icn_search.gif" /></a>';
		//'<a id="HapusPengalaman'+jumlah+'" style="display:'+jumlah+'"></a>';
		//$("#reqJumlahPenilaianPersonil"+indexRow).text(jumlah);
		
		
		//
		var table = document.getElementById('tablePenilaianJumlahPengalamanBidang');
		var rowCount = table.rows.length;
		var row = table.insertRow(rowCount);
		
		var jumlah= rowCount+1;
		var cell1 = row.insertCell(0);
		cell1.innerHTML ='<input type="text" id="reqPengalamanLabel'+jumlah+'" style="width:450px; " readonly="readonly" />';
		
		//
		var table = document.getElementById('tablePenilaianJumlahPengalamanKontrak');
		var rowCount = table.rows.length;
		var row = table.insertRow(rowCount);
		
		var jumlah= rowCount+1;
		var cell1 = row.insertCell(0);
		cell1.innerHTML ='<input type="text" id="reqPengalamanNilai'+jumlah+'" style="width:450px; " readonly="readonly" />';
		
		//
		var table = document.getElementById('tablePenilaianJumlahPengalamanJo');
		var rowCount = table.rows.length;
		var row = table.insertRow(rowCount);
		
		var jumlah= rowCount+1;
		var cell1 = row.insertCell(0);
		cell1.innerHTML ='<input type="text" id="reqStatusPenyedia'+jumlah+'" style="width:450px; " readonly="readonly" />';
								
	}
	
	function setLookupJumlahPersonil(row)
	{
		var jumlahData= $("#reqJumlahPenilaianTotalPersonil"+row).val();
		
		openAdd('main/loadUrl/app/data_tenaga_ahli/?reqMode=KUALIFIKASI&reqId=<?=$reqId?>&reqRow='+row+'&reqJumlah='+jumlahData); return false;
	}
	
	function addRowPenilaianJumlahPersonil(tableID, idRowValue, tempJumlah) {
		var table = document.getElementById(tableID);
		
		var indexRow= tableID.split('tablePenilaianJumlahPersonil'); 
		indexRow= indexRow[1];
		
		var rowCount = table.rows.length;
		var row = table.insertRow(rowCount);
		
		var jumlahDefault= parseInt($("#reqJumlahPenilaianPersonil"+indexRow).text());
		
		var jumlah= rowCount;
		
		var jumlahPersonal= "";
		tempJumlah= parseInt(tempJumlah) + 1;
		//alert(tempJumlah+" == "+jumlah);
		
		
		if(tempJumlah == jumlah)
			jumlahPersonal= tempJumlah -jumlahDefault;
		else
			jumlahPersonal= jumlah -jumlahDefault;
		
		var cell1 = row.insertCell(0);
		cell1.innerHTML =
		'Personil '+jumlahPersonal+
		'<input type="hidden" name="reqPaketEvalPersonilId[]" id="reqPaketEvalPersonilId'+indexRow+'-'+jumlah+'" value="'+idRowValue+'" /> '+
		'<input type="hidden" name="reqPersonilId[]" id="reqPersonilId'+indexRow+'-'+jumlah+'" /> '+
		'<input type="text" name="reqPersonil[]" id="reqPersonil'+indexRow+'-'+jumlah+'" style="width:360px" readonly /> '+
		'<a onclick="setTenagaAhliNumber(\''+indexRow+'-'+jumlah+'\'); setLookupJumlahPersonil(\''+indexRow+'\')"'+
		//'<a onclick="setTenagaAhliNumber(\''+indexRow+'-'+jumlah+'\'); setLookupJumlahPersonil(\''+indexRow+'\', \''+jumlah+'\')"'+
		'style="cursor:pointer"><img src="images/icn_search.gif" /></a>'+
		'<a id="HapusPersonil'+indexRow+'-'+jumlah+'" style="display:'+jumlah+'"></a>';
		$("#reqJumlahPenilaianTotalPersonil"+indexRow).val(jumlah);
	}
	
	function addRowPenilaianJumlahPeralatan(tableID, idRowValue) {
		var table = document.getElementById(tableID);
		
		var indexRow= tableID.split('tablePenilaianJumlahPeralatan'); 
		indexRow= indexRow[1];
		
		var rowCount = table.rows.length;
		var row = table.insertRow(rowCount);
		
		var jumlah= rowCount+1;
		//alert('satu:'+indexRow +'-'+jumlah);
		var cell1 = row.insertCell(0);
		cell1.innerHTML =
		'<input type="hidden" name="reqPaketEvalPeralatanDetilId[]" id="reqPaketEvalPeralatanDetilId'+indexRow+'-'+jumlah+'" value="'+idRowValue+'" /> '+
		'<input type="hidden" name="reqPeralatanId[]" id="reqPeralatanId'+indexRow+'-'+jumlah+'" /> '+
		'<input type="text" name="reqPeralatan[]" id="reqPeralatan'+indexRow+'-'+jumlah+'" style="width:360px" readonly /> '+
		'<a onclick="setPeralatanNumber(\''+indexRow+'-'+jumlah+'\'); openAdd(\'main/loadUrl/app/data_peralatan/?reqMode=KUALIFIKASI&reqId=<?=$reqId?>&reqRow='+indexRow+'&reqJumlah='+jumlah+'\'); return false"'+
		'style="cursor:pointer"><img src="images/icn_search.gif" /></a>'+
		'<a id="HapusPeralatan'+indexRow+'-'+jumlah+'" style="display:'+jumlah+'"></a>';
	}
	
	
	function addRowPenilaianJumlahSertifikatLain(tableID, idRowValue) {
		var table = document.getElementById(tableID);
		
		var indexRow= tableID.split('tablePenilaianJumlahSertifikatLain'); 
		indexRow= indexRow[1];
		
		var rowCount = table.rows.length;
		var row = table.insertRow(rowCount);
		
		var jumlah= rowCount+1;
		//alert(rowCount);
		
		var cell1 = row.insertCell(0);
		cell1.innerHTML = 
		'<input type="hidden" name="reqPaketEvalSertifikatLainId[]" id="reqPaketEvalSertifikatLainId'+indexRow+'-'+jumlah+'" value="'+idRowValue+'" /> '+
		'<input type="hidden" name="reqSertifikatLainId[]" id="reqSertifikatLainId'+indexRow+'-'+jumlah+'" /> '+
		'<input type="text" name="reqSertifikatLain[]" id="reqSertifikatLain'+indexRow+'-'+jumlah+'" style="width:360px" readonly /> '+
		'<a onclick="setSertifikatLainNumber(\''+indexRow+'-'+jumlah+'\'); openAdd(\'main/loadUrl/app/data_sertifikat/?reqMode=KUALIFIKASI&reqId=<?=$reqId?>&reqRow='+indexRow+'&reqJumlah='+jumlah+'\'	); return false"'+
		'style="cursor:pointer"><img src="images/icn_search.gif" /></a>'+
		'<a id="HapusSertifikatLain'+indexRow+'-'+jumlah+'" style="display:'+jumlah+'"></a>';
		//$("#reqJumlahPenilaianPersonil"+indexRow).text(jumlah);
	}
</script>

<div class="row"> 
  <div class="col-md-12 col-sm-12">
    <div class="card">
      <div class="card-header card-head-inverse bg-primary">
        <h4 class="card-title text-white">Data Kualifikasi 
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
          <div class="card mb-1">
            <div class="table-responsive">
		        <form id="ff" class="easyui-form form-horizontal" method="post" data-options="novalidate:true" enctype="multipart/form-data">
					
		            <div class="alert alert-info">I. EVALUASI DATA ADMINISTRASI</div>
					<table class="table table-bordered table-hover">
						<?php
		                  $reqCatatan = "";
		                  $paket_rekanan_kualifikasi = new PaketRekananKualifikasi();
		                  $paket_rekanan_kualifikasi->selectByParams(array("A.PAKET_REKANAN_ID" => $reqPaketRekananId, "A.KODE" => "EVALUASI_ADMIN"));
		                  $paket_rekanan_kualifikasi->firstRow();
		                  $reqCatatan = $paket_rekanan_kualifikasi->getField("CATATAN");
		                  unset($paket_rekanan_kualifikasi);
		                  if($reqCatatan == "")
		                  {}
		                  else
		                  {
		                ?>                    
		                <tr>
		                  <td colspan="3" valign="top">
		                    <div class="area-catatan-panitia">
		                        <div class="judul">
		                        Catatan Panitia (Evaluasi Data Administrasi) :
		                        </div>
		                        <div class="isi">

		                        <?=str_replace("\n", "<br>", $reqCatatan)?>
		                        </div>
		                    </div>                         
		                  
		                  </td>
		                </tr>
		                <?php
		                  }
		                ?>
		                <tr class="judul-kolom">
		                  <td valign="top">No.</td>
		                  <td valign="top">Kriteria</td>
		                  <td valign="top">Data</td>
		                </tr>
		                <?php
		                if(checkEvaluasi($arrIdEvaluasiAdmin, 1))
		                {
		                ?>
		                
		                <script language="javascript">
		                function setPaktaIntegritas()
		                {
		                    $("#reqPaktaIntegritas").text("Pakta Integritas telah terisi.");
		                    $("#reqPaktaIntegritasText").val("Pakta Integritas telah terisi.");
		                    $("#btnPaktaIntegritas").hide();
		                }
		                </script> 
		                <tr class="gelap">
		                  <td valign="top"><?=$number?></td>
		                  <td valign="top">Pakta Integritas, dengan format:
		                    <div class="birulaut">Klik lengkapi kemudian centang setiap kolom yang terdapat pada Pakta Integritas</div></td>
		                  <td valign="top">
		                    <label id="reqPaktaIntegritas"><?php // $arrRekananAdmin[1]?></label>
		                    <input type="hidden" name="reqEvaluasiNumber[]" value="1" />
		                    <div style="display:none">
		                     <input type="file" name="reqLinkFileDataAdministrasi[]" />
		                    <input type="hidden" name="reqLinkFileDataAdministrasiTemp[]">
		                    </div>
		                      <?php
		                      // if($arrRekananAdmin[1] == "")
		                      // {
	                          	$paket_pakta_integritas->selectByParams(array("PAKET_ID" => $reqId, "USER_LOGIN_ID" => $this->USER_LOGIN_ID, 'JENIS' => 'REKANAN'));
	                          	$paket_pakta_integritas->firstRow();
	                    	    if($aktif_entri == 1)
		                        {
		                          	if ($paket_pakta_integritas->getField("USER_LOGIN_ID")) {
		                          		echo "Pakta Integritas telah terisi.";
		                    			echo '<input type="hidden" id="reqPaktaIntegritasText" name="reqEvaluasiAdmin[]" value="Pakta Integritas telah terisi.">';
		                          	} else {
		                      ?>
		                        <a id="btnPaktaIntegritas" onclick="openAdd('main/loadUrl/app/data_kualifikasi_pakta_integritas/?reqId=<?=$reqId?>'); return false" style="cursor:pointer" class="btn btn-primary btn-sm text-white">Lengkapi
		                        </a>                            
		                      <?php
		                      		}
		                        } else {
		                        	if ($paket_pakta_integritas->getField("USER_LOGIN_ID")) {
		                          		echo "Pakta Integritas telah terisi.";
		                    			echo '<input type="hidden" id="reqPaktaIntegritasText" name="reqEvaluasiAdmin[]" value="Pakta Integritas telah terisi.">';
		                          	} else {
		                          		echo "-";
		                          	}
		                        }
		                      // }
		                      ?>                        
		                   </td>
		                </tr>
		                <?php
		                    $number++;
		                }
		                if(checkEvaluasi($arrIdEvaluasiAdmin, 2))
		                {
		                ?>                 
		                   
		                <script language="javascript">
		                function setSuratPernyataanMinat()
		                {
		                    $("#reqSuratPernyataanMinat").text("Surat Pernyataan Minat telah terisi.");
		                    $("#reqSuratPernyataanText").val("Surat Pernyataan Minat telah terisi.");
		                    $("#btnSuratPernyataanMinat").hide();
		                }
		                </script>      
		                <tr class="terang">
		                  <td valign="top"><?=$number?></td>
		                  <td valign="top">Surat Pernyataan Minat, dengan format:
		                    <div class="birulaut">Klik lengkapi kemudian isi kolom yang terdapat pada form Surat Pernyataan Minat.</div></td>
		                  <td valign="top">
		                  <label id="reqSuratPernyataanMinat"><?php //$arrRekananAdmin[2]?></label>
		                  <input type="hidden" name="reqEvaluasiNumber[]" value="2" />
		                  <div style="display:none">
		                     <input type="file" name="reqLinkFileDataAdministrasi[]" />
		                    <input type="hidden" name="reqLinkFileDataAdministrasiTemp[]">
		                    </div>
		                  <?php
		                  // if($arrRekananAdmin[2] == "")
		                  // {
	                    	$paket_pernyataan_minat->selectByParams(array("PAKET_REKANAN_ID" => $reqPaketRekananId));
                          	$paket_pernyataan_minat->firstRow();
		                    if($aktif_entri == 1)
		                    {
	                          	if ($paket_pernyataan_minat->getField("PAKET_PERNYATAAN_MINAT_ID")) {
	                          		echo "Surat Pernyataan Minat telah terisi.";
		                 			echo '<input type="hidden" id="reqSuratPernyataanText" name="reqEvaluasiAdmin[]" value="Surat Pernyataan Minat telah terisi.">';
	                          	} else {
		                  ?>
		                    <a id="btnSuratPernyataanMinat" onclick="openAdd('main/loadUrl/app/data_kualifikasi_surat_minat/?reqId=<?=$reqId?>')" style="cursor:pointer" class="btn btn-primary btn-sm text-white">Lengkapi
		                    </a>                     
		                  <?php
		                  		}
		                    } else {
		                    	if ($paket_pernyataan_minat->getField("PAKET_PERNYATAAN_MINAT_ID")) {
	                          		echo "Surat Pernyataan Minat telah terisi.";
		                 			echo '<input type="hidden" id="reqSuratPernyataanText" name="reqEvaluasiAdmin[]" value="Surat Pernyataan Minat telah terisi.">';
	                          	} else {
	                          		echo "-";
	                          	}
		                    }
		                  // }
		                  ?>
		                  </td>
		                </tr>
		                <?php
		                    $number++;
		                }
		                if(checkEvaluasi($arrIdEvaluasiAdmin, 3))
		                {
		                    $ijin_usaha = new RekananIjinUsaha();
		                    $ijin_usaha->selectByParams(array("REKANAN_ID" => $this->ID, "NOT IJIN_USAHA_ID" => "99"));
		                ?>                    
		                <tr class="gelap">
		                  <td valign="top"><?=$number?></td>
		                  <td valign="top">Ijin Usaha</td>
		                  
		                  <td valign="top">
		                  <?php
		                  $tempIjinUsaha = '';
		                  while($ijin_usaha->nextRow())
		                  {
		                      $tempIjinUsaha .= 
		                    $ijin_usaha->getField("IJIN_USAHA").
		                    '<br>No : '.$ijin_usaha->getField("NO_IJIN").
		                    '<br>Tanggal : '.getFormattedDate($ijin_usaha->getField("TANGGAL")).
		                    '<br>Berlaku S/D : '.getFormattedDate($ijin_usaha->getField("TANGGAL_BERAKHIR")).
		                    '<br>Disahkan Oleh : '.$ijin_usaha->getField("INSTANSI").
		                    '<br>';
		                  ?>
		                    <strong><u><?=$ijin_usaha->getField("IJIN_USAHA")?></u></strong><br />
		                    No : <?=$ijin_usaha->getField("NO_IJIN")?><br />
		                    Tanggal : <?=getFormattedDate($ijin_usaha->getField("TANGGAL"))?><br />
		                    Berlaku S/D : <?=getFormattedDate($ijin_usaha->getField("TANGGAL_BERAKHIR"))?><br />
		                    Disahkan Oleh : <?=$ijin_usaha->getField("INSTANSI")?><br />
		                  <?php
		                  }
		                  ?>
		                  <?php /*?><?=$ijin_usaha->getField("IJIN_USAHA")?><br>No : <?=$ijin_usaha->getField("NO_IJIN")?><br>Tanggal : <?=getFormattedDate($ijin_usaha->getField("TANGGAL"))?><br>Berlaku S/D : <?=getFormattedDate($ijin_usaha->getField("TANGGAL"))?><br>Disahkan Oleh : <?=$ijin_usaha->getField("INSTANSI")?><?php */?>
		                   <input type="hidden" name="reqEvaluasiNumber[]" value="3" />
		                   <input type="hidden" name="reqEvaluasiAdmin[]" 
		                   value="<?=$tempIjinUsaha?>">
		                    <div style="display:none">
		                     <input type="file" name="reqLinkFileDataAdministrasi[]" />
		                    <input type="hidden" name="reqLinkFileDataAdministrasiTemp[]">
		                    </div>
		                    </td>
		                </tr>
		                <?php
		                    $number++;
		                }
		                if(checkEvaluasi($arrIdEvaluasiAdmin, 4))
		                {
		                    $akta = new RekananAkta();
		                    $akta->selectByParams(array("REKANAN_ID" => $this->ID));
		                    $akta->firstRow();
		                ?>                    
		                <tr class="terang">
		                  <td valign="top"><?=$number?></td>
		                  <td valign="top">Akta Pendirian Perusahaan</td>
		                  <td valign="top"><strong><u>Akta Pendirian</u></strong><br />
		                    No : <?=$akta->getField("NOMOR")?><br />
		                    Tanggal : <?=getFormattedDate($akta->getField("TANGGAL"))?><br />
		                    Disahkan Oleh : <?=$akta->getField("NOTARIS")?><br />
		                    <div style="display:none">
		                     <input type="file" name="reqLinkFileDataAdministrasi[]" />
		                    <input type="hidden" name="reqLinkFileDataAdministrasiTemp[]">
		                    </div>
		                    <input type="hidden" name="reqEvaluasiNumber[]" value="4" /><input type="hidden" name="reqEvaluasiAdmin[]" value="No : <?=$akta->getField("NOMOR")?><br />Tanggal : <?=getFormattedDate($akta->getField("TANGGAL"))?><br />Disahkan Oleh : <?=$akta->getField("NOTARIS")?>" />   
		                  </td>
		                </tr>
		                <?php
		                    $number++;
		                }
		                if(checkEvaluasi($arrIdEvaluasiAdmin, 5))
		                {
		                    $pengurus_komisaris = new RekananPengurus();
		                    $pengurus_komisaris->selectByParams(array("REKANAN_ID" => $this->ID, "TIPE" => 1));
		                    $pengurus_direksi = new RekananPengurus();
		                    $pengurus_direksi->selectByParams(array("REKANAN_ID" => $this->ID, "TIPE" => 2));
		                ?>                                        
		                <tr class="gelap">
		                  <td valign="top"><?=$number?></td>
		                  <td valign="top">Daftar Pengurus Perusahaan</td>
		                  <td valign="top">
		                  <strong><u>KOMISARIS</u></strong><br />
		                  <?php
		                  $pengurus="KOMISARIS<br>";
		                  while($pengurus_komisaris->nextRow())
		                  {
		                  ?>
		                    &raquo; <?=$pengurus_komisaris->getField("NAMA")?> (<?=$pengurus_komisaris->getField("JABATAN")?>)<br />
		                  <?php
		                    $pengurus = $pengurus."&raquo; ".$pengurus_komisaris->getField("NAMA")." (".$pengurus_komisaris->getField("JABATAN").")<br>";
		                  }
		                  $pengurus= $pengurus."DIREKSI<br>"
		                  ?>
		                    <u><strong>DIREKSI</strong></u><strong></strong><br />
		                  <?php
		                  while($pengurus_direksi->nextRow())
		                  {
		                  ?>
		                    &raquo; <?=$pengurus_direksi->getField("NAMA")?> (<?=$pengurus_direksi->getField("JABATAN")?>)<br />
		                  <?php
		                    $pengurus = $pengurus."&raquo; ".$pengurus_direksi->getField("NAMA")." (".$pengurus_direksi->getField("JABATAN").")<br>";
		                  }
		                  ?>
		                    <div style="display:none">
		                     <input type="file" name="reqLinkFileDataAdministrasi[]" />
		                    <input type="hidden" name="reqLinkFileDataAdministrasiTemp[]">
		                    </div>
		                    <input type="hidden" name="reqEvaluasiNumber[]" value="5" /><input type="hidden" name="reqEvaluasiAdmin[]" value="<?=$pengurus?>" />   
		                  </td>
		                </tr>
		                <?php
		                    $number++;
		                }
		                if(checkEvaluasi($arrIdEvaluasiAdmin, 6))
		                {
		                ?>                                        
		                <tr class="terang">
		                  <td valign="top"><?=$number?></td>
		                  <td valign="top">SBU</td>
		                  <td valign="top">
		                  <?php
		                  $ijin_usaha_sbu = new RekananIjinUsaha();
		                  $ijin_usaha_sbu->selectByParams(array("REKANAN_ID" => $this->ID, "IJIN_USAHA_ID" => "99"));        					             
		                  $sbu="SBU"."<br />";
		                  ?>                      
		                  <strong><u>SBU</u></strong><br />
		                  <?    php            
		                    while($ijin_usaha_sbu->nextRow())
		                    {	
		                  ?>
		                    &raquo; No. Ijin : <?=$ijin_usaha_sbu->getField("NO_IJIN")?><br />
		                    &raquo; Masa Berlaku : <?=getFormattedDate($ijin_usaha_sbu->getField("TANGGAL"))." s/d ".getFormattedDate($ijin_usaha_sbu->getField("TANGGAL_BERAKHIR"))?><br />
		                    &raquo; Disahkan Oleh : <?=$ijin_usaha_sbu->getField("INSTANSI")?><br /><br />
		                    <?php
		                    $sbu .= "No. Ijin : ".$ijin_usaha_sbu->getField("NO_IJIN")."<br>";
		                    $sbu .= "Masa Berlaku : ".getFormattedDate($ijin_usaha_sbu->getField("TANGGAL"))." s/d ".getFormattedDate($ijin_usaha_sbu->getField("TANGGAL_BERAKHIR"))."<br>";
		                    $sbu .= "Disahkan Oleh : ".$ijin_usaha_sbu->getField("INSTANSI")."<br><br />";
		                    }
		                    ?>
		                    <div style="display:none">
		                     <input type="file" name="reqLinkFileDataAdministrasi[]" />
		                    <input type="hidden" name="reqLinkFileDataAdministrasiTemp[]">
		                    </div>
		                    <input type="hidden" name="reqEvaluasiNumber[]" value="6" /><input type="hidden" name="reqEvaluasiAdmin[]" value="<?=$sbu?>" />   
		                </td>
		                </tr>
		                <?php
		                    $number++;
		                }
		                if(checkEvaluasi($arrIdEvaluasiAdmin, 7))
		                {
		                ?>                                        
		                <tr class="gelap">
		                  <td valign="top"><?=$number?></td>
		                  <td valign="top">PKP dan NPWP</td>
		                  <td valign="top"><u><strong>PKP</strong></u> : <?=$this->REKANAN_PKP?><br />
		                    <u><strong>NPWP</strong></u> : <?=$this->REKANAN_NPWP?>
		                    <div style="display:none">
		                     <input type="file" name="reqLinkFileDataAdministrasi[]" />
		                    <input type="hidden" name="reqLinkFileDataAdministrasiTemp[]">
		                    </div>
		                    <input type="hidden" name="reqEvaluasiNumber[]" value="7" /><input type="hidden" name="reqEvaluasiAdmin[]" value="PKP : <?=$this->REKANAN_PKP?> <br>NPWP : <?=$this->REKANAN_NPWP?>" /> 
		                  </td>
		                </tr>
		                <?php
		                    $number++;
		                }
		                if(checkEvaluasi($arrIdEvaluasiAdmin, 8))
		                {
		                    $saham = new RekananSaham();
		                    $saham->selectByParams(array("REKANAN_ID" => $this->ID));
		                ?>                                       
		                <tr class="terang">
		                  <td valign="top"><?=$number?></td>
		                  <td valign="top">Daftar Susunan Pemilik Modal</td>
		                  <td valign="top">
		                  <?php
		                  $modal="";
		                  while($saham->nextRow())
		                  {
		                  ?>
		                    &raquo; <?=$saham->getField("NAMA")?> (<?=$saham->getField("JUMLAH_SAHAM")?>%)<br />
		                  <?php
		                    $modal .= "&raquo; ".$saham->getField("NAMA")." (".$saham->getField("JUMLAH_SAHAM")."%)<br />";
		                  }
		                  ?>
		                  <div style="display:none">
		                     <input type="file" name="reqLinkFileDataAdministrasi[]" />
		                    <input type="hidden" name="reqLinkFileDataAdministrasiTemp[]">
		                    </div>
		                  <input type="hidden" name="reqEvaluasiNumber[]" value="8" /><input type="hidden" name="reqEvaluasiAdmin[]" value="<?=$modal?>" />                           
		                  </td>
		                </tr>
		                <?php
		                    $number++;
		                }
		                if(checkEvaluasi($arrIdEvaluasiAdmin, 9))
		                {
		                ?>                                        
		                <tr class="gelap">
		                  <td valign="top"><?=$number?></td>
		                  <td valign="top">Pelunasan Pajak Tahun Terakhir (SPT/PPH)</td>
		                  <td valign="top">
		                  <?php
		                  $pelunasan_pajak = "";
		                  $pajak = new RekananPajak();
		                  $pajak->selectByParamsKualifikasi($this->ID);
		                  while($pajak->nextRow())
		                  {
		                  ?>
		                    &raquo; <?=$pajak->getField("KETERANGAN")?><br />
		                  <?php
		                    $pelunasan_pajak .= "&raquo; ".$pajak->getField("KETERANGAN")."<br>";
		                  }
		                  ?>
		                  <div style="display:none">
		                     <input type="file" name="reqLinkFileDataAdministrasi[]" />
		                    <input type="hidden" name="reqLinkFileDataAdministrasiTemp[]">
		                    </div>
		                  <input type="hidden" name="reqEvaluasiNumber[]" value="9" /><input type="hidden" name="reqEvaluasiAdmin[]" value="<?=$pelunasan_pajak?>" />  
		                   </td>
		                </tr>
		                <?php
		                    $number++;
		                }
		                if(checkEvaluasi($arrIdEvaluasiAdmin, 10))
		                {
		                $neraca = new RekananNeraca();
		                $neraca->selectByParamsNeracaTerakhir(array("A.REKANAN_ID" => $this->ID));
		                $neraca->firstRow();
		                ?>                                        
		                <tr class="terang">
		                  <td valign="top"><?=$number?></td>
		                  <td valign="top">Neraca Perusahaan yang Telah Diaudit</td>
		                  <td valign="top"><strong><u>Neraca</u></strong><br />
		                                   Modal : <?=currencyToPage($neraca->getField("MODAL"))?><br />
		                                   Auditor : <?=$neraca->getField("AUDIT_NAMA")?><br />
		                                   Tanggal : <?=getFormattedDate($neraca->getField("AUDIT_TANGGAL"))?><br />
		                                   No. : <?=$neraca->getField("AUDIT_NOMOR")?><br />
		                  <div style="display:none">
		                     <input type="file" name="reqLinkFileDataAdministrasi[]" />
		                    <input type="hidden" name="reqLinkFileDataAdministrasiTemp[]">
		                    </div>
		                  <input type="hidden" name="reqEvaluasiNumber[]" value="10" /><input type="hidden" name="reqEvaluasiAdmin[]" value="Modal : <?=currencyToPage($neraca->getField("MODAL"))?><br />Auditor : <?=$neraca->getField("AUDIT_NAMA")?><br />Tanggal : <?=getFormattedDate($neraca->getField("AUDIT_TANGGAL"))?><br />No. : <?=$neraca->getField("AUDIT_NOMOR")?>" />
		                  </td>
		                </tr>
		                <?php
		                    $number++;
		                }
		                $style = "gelap";
		                for($i=0; $i<=count($arrIdEvaluasiAdmin); $i++)
		                {
		                    if($arrIdEvaluasiAdmin[$i] > 10)
		                    {
		                ?>
		                    <tr class="<?=$style?>">
		                      <td valign="top"><?=$number?></td>
		                      <td valign="top"><?=$arrNamaEvaluasiAdmin[$i]?></td>
		                      <td valign="top">
		                        <input type="hidden" name="reqEvaluasiNumber[]" value="<?=$arrIdEvaluasiAdmin[$i]?>" />
		                        <input type="hidden" name="reqEvaluasiAdmin[]" value="<?=$arrNamaEvaluasiAdmin[$i]?>"> 
		                        <?php
		                        	if($aktif_entri == 1)
									{
								?>                       
		                            File Upload : <input type="file" name="reqLinkFileDataAdministrasi[]" size="30" />
		                            <input type="hidden" name="reqLinkFileDataAdministrasiTemp[]" value="<?=$arrLinkFileDataAdministrasi[$i]?>">
		                        	<br />
								<?php
									}
								?>
								<?php
		                        if($arrLinkFileDataAdministrasi[$arrIdEvaluasiAdmin[$i]] == "") {}
		                        else
		                        {
		                        ?>
		                        temp : <?=$arrLinkFileDataAdministrasi[$arrIdEvaluasiAdmin[$i]]?>
		                        <?
		                        }
		                        ?>
		                       </td>
		                    </tr>
		                <?php
		                    $number++;
		                    if($style == "gelap")
		                        $style = "terang";
		                    else
		                        $style = "gelap";
		                    }
		                }					
		                ?>                                        
		        	</table> 

		            <div class="alert alert-info">II. EVALUASI KEUANGAN</div> 
		            <table class="table table-bordered table-hover">                 
		                <tr>
		                  <td style="width: 7%">1. </td> 
		                  <td colspan="2" valign="top"> 
		                  	SKK
		                  </td>
		                </tr>
		                <tr>
		                  <td style="width: 7%">2. </td> 
		                  <td colspan="2" valign="top"> 
		                  	Rekening Koran
		                  </td>
		                </tr>
		            </table>

		            <div class="alert alert-info">III. EVALUASI DATA TEKNIS</div> 
		            <table class="table table-bordered table-hover">                 
		                <tr>
		                  <td style="width: 7%">1. </td> 
		                  <td colspan="2" valign="top"> Penilaian Tenaga Ahli </td>
		                </tr>
		                <tr>
		                  <td style="width: 7%">2. </td> 
		                  <td colspan="2" valign="top"> Penilaian Pengalaman Pekerjaan </td>
		                </tr>
		                <tr>
		                  <td style="width: 7%">3. </td> 
		                  <td colspan="2" valign="top"> Penilaian Peralatan </td>
		                </tr>
		                <tr>
		                  <td style="width: 7%">4. </td> 
		                  <td colspan="2" valign="top"> Penilaian Sertifikat </td>
		                </tr>
		            </table>
		            
		            <hr>                        
		            <div>
						<input type="hidden" name="reqId" value="<?=$reqId?>" />
		            	<input type="hidden" name="submitSimpan" value="Simpan">

		                <a href="main/index/paket_detil/?reqId=<?=$reqId?>" class="btn btn-danger"><i class="fa fa-arrow-left"></i> Kembali</a>
		                <?php
						if($aktif_entri == 1)
						{                            
						?>
		               		 <button type="submit" class="btn btn-primary"><i class="fa fa-check-square-o"></i> Masukan Data Kualifikasi</button>
		                <?php
						} else {
							echo '<div style="margin-top:10px" class="alert alert-danger text-white"> Waktu Pemasukan Penawaran belum dimulai atau sudah berakhir</div>';
						}
						?>
		            </div>
				</form>   
            </div>
          </div>
        </div>
      </div>
    </div>
  </div> 
</div>   