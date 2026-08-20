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
$this->load->model("Sppjb");
$this->load->model("Paketpemenang");
$this->load->model("RekananPengurus");
$this->load->library("FileHandler");
include_once("functions/string.func.php");
include_once("functions/date.func.php");
include_once("functions/default.func.php");

$sppjb = new Sppjb();
$file = new FileHandler();
$getpaket_pemenang = new Paketpemenang();

$reqMode = httpFilterRequest("reqMode");
$reqId = httpFilterRequest("reqId");
$pemenang = httpFilterRequest("pemenang");
$reqCetak= httpFilterPost('reqCetak');
$reqNamaDokumen= httpFilterPost('reqNamaDokumen');
$reqKeterangan= httpFilterPost('reqKeterangan');
$reqLinkFile= $_FILES['reqLinkFile'];
$reqBayar= httpFilterPost('reqBayar');
$reqDokumenId = httpFilterGet('reqDokumenId');
$submitSimpan= httpFilterPost('submitSimpan');

$paketInfo->getPaket($reqId);
$reqNama = $paketInfo->nama;

$getpaket_pemenang->selectByParams(array("PAKET_PEMENANG_ID" => $pemenang), -1, -1);
$getpaket_pemenang->firstRow();
$reqRekananName = $getpaket_pemenang->getField("NAMA");
$sppjb->selectByParams(array("PAKET_ID" => $reqId, "PAKET_PEMENANG_ID" => $pemenang));
$sppjb->firstRow();
$reqSppjbId = $sppjb->getField("SPPJB_ID");
$reqKode = $sppjb->getField("KODE");
$reqTanggal = dateToPageCheck($sppjb->getField("TANGGAL"));
$reqNamaDirut = $sppjb->getField("NAMA_DIRUT");
$reqAlamatDirut = $sppjb->getField("ALAMAT_DIRUT");
$reqKota = $sppjb->getField("KOTA_DIRUT");
$reqPPN = $sppjb->getField("PPN");
$reqPersenJaminan = $sppjb->getField("PERSEN_JAMINAN");
$reqTMTJaminan = dateToPageCheck($sppjb->getField("TMT_JAMINAN"));
$reqJangkaWaktu = $sppjb->getField("JANGKA_WAKTU");
$reqPenandaTangan = $sppjb->getField("PENANDA_TANGAN");
$reqJabatanPenandaTangan = $sppjb->getField("PENANDA_TANGAN_JABATAN");
$reqJangkaWaktuJaminan = $sppjb->getField("JANGKA_WAKTU_JAMINAN");

if($reqSppjbId == "")
{
	// $rekanan_pengurus = new RekananPengurus();
	// $rekanan_pengurus->selectByParamsDirektur(array("A.REKANAN_ID" => $paketInfo->rekanan_id_pemenang));
	// $rekanan_pengurus->firstRow();
	// $reqNamaDirut = $rekanan_pengurus->getField("NAMA");
	// $reqAlamatDirut = $rekanan_pengurus->getField("ALAMAT");
	// $reqKota = $rekanan_pengurus->getField("KOTA");
}

?> 
<script type="text/javascript">
$(document).ready(function() {
	
	$(function(){
		$('#ff').form({
			url:'sppjb_json/add',
			onSubmit:function(){
				return $(this).form('validate');
			},
			success:function(data){
				//alert(data);return false;
				document.location.href = 'main/index/paket_lelang_tambah_sppjb_tambah/?reqId=<?=$reqId?>&pemenang=<?=$pemenang?>';	
			}
		});
		
	});
	
});
</script>

<div class="row"> 
  <div class="col-md-12 col-sm-12">
    <div class="card">
      <div class="card-header card-head-inverse bg-primary">
        <h4 class="card-title text-white">SPPBJ</h4>
        <a class="heading-elements-toggle"><i class="fa fa-ellipsis-v font-medium-3"></i></a> 
        <div class="heading-elements">
            <ul class="list-inline mb-0">
              <li><a data-action="expand"><i class="ft-maximize"></i></a></li>
              <!-- <li><a data-action="close"><i class="ft-x"></i></a></li> -->
            </ul>
        </div>
      </div>

      <form id="ff" class="easyui-form form-horizontal" method="post" novalidate enctype="multipart/form-data">
        <div class="card-content collapse show border-info border-darken-2">
          <div class="card-body area-datatable">

            <div class="row">
              <div class="form-group col-md-9 mb-2">
                <label>Nama Rekanan</label><br>
                <h2><?= $reqRekananName ?></h2>
              </div> 
              <div class="form-group col-md-9 mb-2">
                <label>Kode</label>
                <input type="text" name="reqKode" id="reqKode" class="form-control easyui-validatebox" value="<?=$reqKode?>" required/>
              </div> 
              <div class="form-group col-md-3 mb-2">
                <label style="width: 100%">Tanggal</label>
                <input type="text" name="reqTanggal" id="reqTanggal" class="form-control easyui-datebox" value="<?=$reqTanggal?>" required style="width: 200%"/>
              </div> 
            </div>
            <div class="row">
              <div class="form-group col-md-12 mb-2">
                <label>Nama Dirut</label>
                <input type="text" name="reqNamaDirut" id="reqNamaDirut" class="form-control easyui-validatebox" value="<?=$reqNamaDirut?>" required/>
              </div> 
            </div>
            <div class="row">
              <div class="form-group col-md-12 mb-2">
                <label>Kota</label>
                <input type="text" name="reqKota" id="reqKota" class="form-control easyui-validatebox" value="<?=$reqKota?>" required/>
              </div> 
            </div>
            <div class="row">
              <div class="form-group col-md-12 mb-2">
                <label style="width: 100%">Alamat</label>
                <textarea name="reqAlamatDirut" id="reqAlamatDirut" cols="45" rows="5" class="easyui-validatebox"  required  style="width: 100%"><?=$reqAlamatDirut?></textarea>
              </div> 
            </div>
            <div class="row">
              <div class="form-group col-md-12 mb-2">
                <label>PPN</label>
              	<input type="checkbox" name="reqPPN" id="reqPPN" value="1" <?php if($reqPPN == "1") { ?> checked="checked" <?php }?>>
              </div> 
            </div>
            <div class="row">
              <div class="form-group col-md-2 mb-2">
                <label>Persen Jaminan % <small>(diisi angka)</small></label>
                <input type="text" name="reqPersenJaminan" onkeypress="return isNumberKey(event)" id="reqPersenJaminan" class="form-control easyui-validatebox" value="<?=$reqPersenJaminan?>" required/>
              </div> 
              <div class="form-group col-md-4 mb-2">
                <label>Jangka Waktu Pekerjaan hari kalender <small>(diisi angka)</small></label>
                <input type="text" name="reqJangkaWaktu" onkeypress="return isNumberKey(event)" id="reqJangkaWaktu" class="form-control easyui-validatebox" value="<?=$reqJangkaWaktu?>" required/>
              </div> 
              <div class="form-group col-md-4 mb-2">
                <label>Jangka Waktu Jaminan Pelaksanaan hari kalender <small>(diisi angka)</small></label>
                <input type="text"  name="reqJangkaWaktuJaminan" onkeypress="return isNumberKey(event)" id="reqJangkaWaktuJaminan" class="form-control easyui-validatebox" value="<?=$reqJangkaWaktuJaminan?>" required/>
              </div> 
              <div class="form-group col-md-2 mb-2">
                <label style="width: 100%">TMT Jaminan Pelaksanaan</label> 
                <!-- Terhitung Mulai Tanggal -->
                <input type="text" name="reqTMTJaminan" id="reqTMTJaminan" class="form-control easyui-datebox" value="<?=$reqTMTJaminan?>" required style="width: 200%"/>
              </div> 
            </div>
            <div class="row">
              <div class="form-group col-md-6 mb-2">
                <label>Penanda Tangan</label>
            	  <input type="text" name="reqPenandaTangan" id="reqPenandaTangan" value="<?=$reqPenandaTangan?>" class="form-control easyui-validatebox" required/>
              </div> 
              <div class="form-group col-md-6 mb-2">
                <label>Jabatan Penanda Tangan</label>
              	<input type="text" name="reqJabatanPenandaTangan" id="reqJabatanPenandaTangan" value="<?=$reqJabatanPenandaTangan?>" class="form-control easyui-validatebox" required/>
              </div> 
            </div>
            <div class="form-actions">
              <input type="hidden" name="reqId" value="<?=$reqId?>" />
              <input type="hidden" name="reqPaketPemenangId" value="<?=$pemenang?>" />
              <input type="hidden" name="reqSppjbId" value="<?=$reqSppjbId?>" />
              <input type="hidden" name="submitSimpan" value="Simpan" />
              <a href="main/index/paket_lelang_tambah_sppjb/?reqId=<?=$reqId?>" class="btn btn-danger text-white"> <i class="fa fa-arrow-left"></i> Kembali </a> 
              <button type="submit" class="btn btn-primary"><i class="fa fa-check-square-o"></i> Simpan</button>
              <a class="btn btn-info" href="main/loadUrl/report/sppjb_pdf/?reqId=<?=$reqId?>-<?=$pemenang?>" target="_blank" ><i class="fa fa-print"></i> Cetak SPPBJ</a>
            </div> 
          </div>
        </div>
      </form>

    </div>
  </div> 
</div>      
              

<!--<div class="judul-grup">SPPJB</div>-->

<?php /*?> <div class="form-group">
    <table id="tbl_bidang">
      <tbody>
        <tr class="judul-kolom">
          <th>No.</th>
          <th colspan="2">Kode</th>
          <th>Tanggal</th>
          <th>Nama Dirut</th>
          <th>Alamat</th>
          <th>Kota</th>
          <th>PPN</th>
          <th>Persen Jaminan</th>
          <th>TMT Jaminan</th>
          <th>Jangka Waktu</th>
          <th>Penanda Tangan</th>
          <th>Jabatan Penanda Tangan</th>
          <th>Aksi</th>
        </tr>
        <?
          $i=1;
          while($sppjb->nextRow())
          {
        ?> 
        <tr >
            <td><?=$i?>.</td>
            <td colspan="2"><?=$sppjb->getField("KODE")?></td>
             <td><?=getFormattedDate($sppjb->getField("TANGGAL"))?></td>
              <td ><?=$sppjb->getField("NAMA_DIRUT")?></td>
              <td ><?=$sppjb->getField("ALAMAT_DIRUT")?></td>
              <td ><?=$sppjb->getField("KOTA_DIRUT")?></td>
              <td ><?=$sppjb->getField("PPN")?></td>
              <td ><?=$sppjb->getField("PERSEN_JAMINAN")?></td>
              <td ><?=getFormattedDate($sppjb->getField("TMT_JAMINAN"))?></td>
            <td><?=$sppjb->getField("JANGKA_WAKTU")?></td>
            <td><?=$sppjb->getField("PENANDA_TANGAN")?></td>
            <td><?=$sppjb->getField("PENANDA_TANGAN_JABATAN")?></td>
            <td>
            <a onClick="deleteData('sppjb_json/delete/', '<?=$sppjb->getField("PAKET_ID")?>')" class="btn-aksi"><i class="fa fa-trash" aria-hidden="true"></i></a>
            </td>
        </tr>
         <?
        $i++;
      }
      ?>
      </tbody>
    </table>
</div><?php */?>
