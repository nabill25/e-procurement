<?php
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 * 
 */

if($this->USER_TYPE_ID == "")
  redirect("main");

$this->load->model("PaketRekanan");
$this->load->model("RekananEvaluasiKeuangan");
$this->load->model("PaketRekananKualifikasi");
include_once("functions/string.func.php");
include_once("functions/date.func.php");
include_once("functions/default.func.php");

$paket_rekanan = new PaketRekanan();

$reqId = $this->input->get("reqId");

$paket_rekanan->selectByParams(array("PAKET_ID" => $reqId), -1, -1, " AND A.TANGGAL_DAFTAR IS NOT NULL AND A.LULUS_PENDAFTARAN = 1 ");
while($paket_rekanan->nextRow())
{
	$arrRekanan[] = $paket_rekanan->getField("REKANAN");
	$arrPaketRekananId[] = $paket_rekanan->getField("PAKET_REKANAN_ID");
	$arrPaketRekananNilai[] = $paket_rekanan->getField("NILAI_PENAWARAN");
}

?>

<script>
function updateMemenuhiSyarat(id)
{

	if($('#reqMemenuhiSyarat' + id).is(":checked"))
		msg = "Rekanan memenuhi syarat rekening koran?";
	else
		msg = "Rekanan tidak memenuhi syarat rekening koran?";

	$.messager.confirm('Konfirmasi',msg,function(r){
		if (r){
			$.get( "rekanan_evaluasi_keuangan_json/set_evaluasi_kualifikasi_rekening_koran/?reqId="+id, function( data ) {
			  $.messager.alert('Informasi',data, 'info');
			});
		}
		else
		{
			if($('#reqMemenuhiSyarat' + id).is(":checked"))
				$('#reqMemenuhiSyarat' + id).prop('checked', false);	
			else
				$('#reqMemenuhiSyarat' + id).prop('checked', true);	
		}
	});	
	
}
</script>

<div class="row"> 
  <div class="col-md-12 col-sm-12">
    <div class="card">
      <div class="card-header card-head-inverse bg-primary">
        <h4 class="card-title text-white">Evaluasi Kualifikasi</h4>
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
          <div class="row">
            <div class="form-group col-md-12 mb-2"> 
              <a href="main/index/evaluasi_kualifikasi_administrasi/?reqId=<?=$reqId?>" class="btn btn-primary"> <span class="fa fa-pencil-square-o"></span> Evaluasi Administrasi</a>
              <a href="main/index/evaluasi_kualifikasi_skk/?reqId=<?=$reqId?>" class="btn btn-primary disabled"><span class="fa fa-money"></span> Data Keuangan</a>
              <a href="main/index/evaluasi_kualifikasi_pengalaman/?reqId=<?=$reqId?>" class="btn btn-primary"><span class="fa fa-cogs"></span> Data Teknis</a>
              <a href="main/index/evaluasi_kualifikasi_rekapitulasi/?reqId=<?=$reqId?>" class="btn btn-primary"><span class="fa fa-list-alt"></span> Rekapitulasi</a>
            </div> 
          </div>
          <div class="table-responsive">  
            <hr>
            <div class="tab-navigasi-sub" style="margin-bottom: 2%">
              <a class="btn btn-success btn-sm" href="main/index/evaluasi_kualifikasi_skk/?reqId=<?=$reqId?>">SKK</a>
              <a class="btn btn-success btn-sm disabled" href="main/index/evaluasi_kualifikasi_koran/?reqId=<?=$reqId?>">Rekening Koran</a>
            </div> 

            <table class="table table-bordered table-hover">
 
                <tr class="judul-kolom">
                    <th style="width: 10px">No</th>
                    <th>Nama Perusahaan</th>
                    <th>Rekening 3 Bulan Terakhir</th>
                    <th>Nilai</th>
                </tr>
                <?php
                for($i=0;$i<count($arrRekanan);$i++)
                {
                    $rekanan_evaluasi_skk = new RekananEvaluasiKeuangan();
                    //echo $arrPaketRekananId[$i].'/';
                    $rekanan_evaluasi_skk->selectByParams(array("PAKET_REKANAN_ID" => $arrPaketRekananId[$i]));
                    $rekanan_evaluasi_skk->firstRow();
                ?>
                  <tr class="terang">
                        <td valign="top"><?=$i+1?></td>     
                        <td valign="top"><?=$arrRekanan[$i]?></td>
                        <td valign="top"><?=str_replace('&gt;', '>', str_replace('&lt;', '<', $rekanan_evaluasi_skk->getField("REKENING_KORAN")))?></td>
                        <td valign="top"><input type="checkbox" style="cursor: pointer;" name="reqMemenuhiSyarat<?=$arrPaketRekananId[$i]?>" id="reqMemenuhiSyarat<?=$arrPaketRekananId[$i]?>" value="1" onclick="updateMemenuhiSyarat('<?=$arrPaketRekananId[$i]?>')"  <?php if($rekanan_evaluasi_skk->getField("LULUS_REKENING_KORAN") == 1) { ?> checked="checked" <?php }  ?> />
                            <label for="reqMemenuhiSyarat">Memenuhi Syarat</label>
            
                        <br>
                        <div class="area-catatan-panitia">
                            <div class="judul">
                            Catatan Untuk Peserta (apabila tidak memenuhi syarat) :
                            </div>
                            <div class="isi">
                            <?php
                              $paket_rekanan_kualifikasi = new PaketRekananKualifikasi();
                              $paket_rekanan_kualifikasi->selectByParams(array("A.PAKET_REKANAN_ID" => $arrPaketRekananId[$i], "A.KODE" => "REKENING_KORAN"));
                              $paket_rekanan_kualifikasi->firstRow();
                              $reqCatatan = $paket_rekanan_kualifikasi->getField("CATATAN");
                              unset($paket_rekanan_kualifikasi);
                            ?>
                            <form method="post" id="catatanForm<?=$arrPaketRekananId[$i]?>">
                                <textarea class="form-control" rows="3" name="reqCatatan"><?=$reqCatatan?></textarea>
                                <input type="hidden" name="reqPaketRekananId" value="<?=$arrPaketRekananId[$i]?>">
                                <input type="hidden" name="reqKode" value="REKENING_KORAN">
                                <input type="hidden" name="submitSimpan" value="Catatan"> <br>
                                <input type="submit" value="Simpan" class="btn btn-primary">
                            </form>
                          <script>
                            // Attach a submit handler to the form
                            $( "#catatanForm<?=$arrPaketRekananId[$i]?>" ).submit(function( event ) {
                                
                              // Stop form from submitting normally
                              event.preventDefault();
                             
                              // Get some values from elements on the page:
                              var $form = $( this ),
                                catatan = $form.find( "textarea[name='reqCatatan']" ).val(),
                                paketRekananId = $form.find( "input[name='reqPaketRekananId']" ).val(),
                                kode = $form.find( "input[name='reqKode']" ).val(),
                                url = 'paket_rekanan_kualifikasi_json/add';
                             
                              // Send the data using post
                              var posting = $.post( url, { reqCatatan: catatan, reqPaketRekananId: paketRekananId, reqKode: kode } );
                             
                              // Put the results in a div
                              posting.done(function( data ) {
                                  $.messager.alert('Informasi',data, 'info');
                              });
                            });
                            </script>                                                                                       
                                                       
                          </div>
                      </div>            
                                              
                        </td>        
                  </tr>
                <?php
                    unset($rekanan_evaluasi_skk);
                }
                ?>
            </table> 

             <div>
                <a href="main/index/paket_detil/?reqId=<?=$reqId?>" class="btn btn-danger"><i class="fa fa-arrow-left"></i> Kembali</a>
                <a class="btn btn-info" href="main/loadUrl/report/evaluasi_kualifikasi_koran_excel/?reqId=<?=$reqId?>" target="_blank" ><span class="fa fa-print"></span> Cetak</a>
              </div>

          </div>
        </div>
      </div>
    </div>
  </div> 
</div>    