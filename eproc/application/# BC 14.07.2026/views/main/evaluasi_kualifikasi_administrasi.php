<?php
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 * 
 */

if($this->USER_TYPE_ID == "")
  redirect("main");

$this->load->library("paketinfo"); $paketInfo = new paketinfo();
$this->load->model("PaketEvaluasiAdmin");
$this->load->model("PaketRekanan");
$this->load->model("PaketRekananKualifikasi");
$this->load->model("PaketKriteriaEvaluasi");
$this->load->model("PaketPernyataanMinat");
$this->load->model("RekananEvaluasiAdmin");
include_once("functions/string.func.php");
include_once("functions/date.func.php");
include_once("functions/default.func.php");

$paket_kriteria_evaluasi = new PaketKriteriaEvaluasi();
$paket_evaluasi_admin = new PaketEvaluasiAdmin();
$paket_rekanan = new PaketRekanan();
$paket_rekanan_height = new PaketRekanan();

$reqId = $this->input->get("reqId");

$paketInfo->getPaket($reqId);
$reqNamaPaket = $paketInfo->nama;

$paket_kriteria_evaluasi->selectByParams(array("PAKET_ID" => $reqId));
$paket_kriteria_evaluasi->firstRow();

$paket_rekanan->selectByParams(array("PAKET_ID" => $reqId), -1, -1, " AND A.TANGGAL_DAFTAR IS NOT NULL AND A.LULUS_PENDAFTARAN = 1 ");
$paket_evaluasi_admin->selectByParamsProses(array("PAKET_ID" => $reqId));
$i = 0;
while($paket_evaluasi_admin->nextRow())
{
	$arrNamaEvaluasiAdmin[$i] = $paket_evaluasi_admin->getField("NAMA");
	$arrIdEvaluasiAdmin[$i] = $paket_evaluasi_admin->getField("EVALUASI_NUMBER"); 
	$i++;
}

?>

<script>
function updateMemenuhiSyarat(id)
{

	if($('#reqStatusAdministrasi' + id).is(":checked"))
		msg = "Rekanan memenuhi syarat administrasi?";
	else
		msg = "Rekanan tidak memenuhi syarat administrasi?";

	$.messager.confirm('Konfirmasi',msg,function(r){
		if (r){
			$.get( "paket_rekanan_json/set_evaluasi_kualifikasi_administrasi/?reqId="+id, function( data ) {
			  $.messager.alert('Informasi',data, 'info');
			});
		}
		else
		{
			if($('#reqStatusAdministrasi' + id).is(":checked"))
				$('#reqStatusAdministrasi' + id).prop('checked', false);	
			else
				$('#reqStatusAdministrasi' + id).prop('checked', true);	
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
              <a href="main/index/evaluasi_kualifikasi_administrasi/?reqId=<?=$reqId?>" class="btn btn-primary disabled"> <span class="fa fa-pencil-square-o"></span> Evaluasi Administrasi</a>
              <a href="main/index/evaluasi_kualifikasi_skk/?reqId=<?=$reqId?>" class="btn btn-primary"><span class="fa fa-money"></span> Data Keuangan</a>
              <a href="main/index/evaluasi_kualifikasi_pengalaman/?reqId=<?=$reqId?>" class="btn btn-primary"><span class="fa fa-cogs"></span> Data Teknis</a>
              <a href="main/index/evaluasi_kualifikasi_rekapitulasi/?reqId=<?=$reqId?>" class="btn btn-primary"><span class="fa fa-list-alt"></span> Rekapitulasi</a>
            </div> 
          </div>
          <div class="table-responsive"> 
            <table class="table table-bordered table-hover"> 
              <tr>
                <th>No.</th>
                <th>Nama Perusahaan</th>
                <?php
                for($i=0; $i<count($arrNamaEvaluasiAdmin);$i++)
                {
                ?>
                <th><?=$arrNamaEvaluasiAdmin[$i]?></th>
                <?php
                }
                ?>
                <th>Status</th>
              </tr>
              <?php
              $no=1;
              $style="gelap";						
              while($paket_rekanan->nextRow())
              {
              ?>
                  <tr class="<?=$style?>">
                    <td valign="top" class="rowcol"><?=$no?>.</td>
                    <td valign="top" class="rowcol1" style="">
                    <a onclick="openAdd('main/loadUrl/main/data_rekanan/?reqId=<?=$paket_rekanan->getField("REKANAN_ID")?>')" style="text-decoration:none"><?=$paket_rekanan->getField("REKANAN")?></a>
                    </td>
                    <?php
                    for($i=0; $i<count($arrIdEvaluasiAdmin);$i++)
                    {
                        $rekanan_evaluasi_admin = new RekananEvaluasiAdmin();
                        $rekanan_evaluasi_admin->selectByParamsV2(array("A.EVALUASI_NUMBER" => $arrIdEvaluasiAdmin[$i], "A.PAKET_REKANAN_ID" => $paket_rekanan->getField("PAKET_REKANAN_ID")));
                        $rekanan_evaluasi_admin->firstRow();
                    ?>
                        <td valign="top">
                        
                        <?php
                        if($rekanan_evaluasi_admin->getField("TIPE_ENTRI") == "")
                        {
                              if($rekanan_evaluasi_admin->getField("PATH_FILE") == "")
                              {
                              ?>
                                  <div id="data-kosong"><em><img src="images/uncentang.png"></em></div>
                              <?php
                              }
                              else
                              {
                              ?>
                                  <br><br><a href="uploads/kualifikasi/<?=$rekanan_evaluasi_admin->getField("PATH_FILE")?>" target="_blank">Download Dokumen</a>                                   	
                              <?php
                              }
                        }
                        else
                        {
                        ?>
                          <?=$rekanan_evaluasi_admin->getField("URAIAN")?>   
                          <?php
                          if($rekanan_evaluasi_admin->getField("TIPE_ENTRI") == "UPLOAD")
                          {
                              if($rekanan_evaluasi_admin->getField("URAIAN") == "Pakta Integritas telah terisi." && $arrIdEvaluasiAdmin[$i] == 1)
                              {
                          ?>
                              <a href="main/loadUrl/report/pakta_integritas_pdf/?reqId=<?=$reqId?>&reqRekananId=<?=md5($paket_rekanan->getField("REKANAN_ID"))?>" target="_blank"><i class="fa fa-print" aria-hidden="true"></i></a>                               
                          <?php
                              }
                              
                              if($rekanan_evaluasi_admin->getField("URAIAN") == "Surat Pernyataan Minat telah terisi." && $arrIdEvaluasiAdmin[$i] == 2)
                              {
                          ?>
                                  <a href="main/loadUrl/report/pernyataan_minat_pdf/?reqId=<?=$reqId?>&reqRekananId=<?=md5($paket_rekanan->getField("REKANAN_ID"))?>&reqPaketRekananId=<?=md5($paket_rekanan->getField("PAKET_REKANAN_ID"))?>" target="_blank"><i class="fa fa-print" aria-hidden="true"></i></a>                               
                                                                    
                          <?php
                                  $paket_pernyataan_minat = new PaketPernyataanMinat();
                                  $paket_pernyataan_minat->selectByParams(array("PAKET_REKANAN_ID" => $paket_rekanan->getField("PAKET_REKANAN_ID")));	
                                  $paket_pernyataan_minat->firstRow();
                                  if($paket_pernyataan_minat->getField("PENERIMA_KUASA") == "")
                                  {}
                                  else
                                  {
                                  ?>
                                  <br>
                                  <?php 
                                  if ($paket_pernyataan_minat->getField("PENERIMA_KUASA")) { ?>
                                    <strong>dikuasakan kepada :</strong>
                                    <table cellpadding="0" cellspacing="0">
                                    <tr>
                                        <td>Nama</td><td><?=$paket_pernyataan_minat->getField("PENERIMA_KUASA")?></td> 
                                    </tr>
                                    <tr>
                                        <td>Jabatan</td><td><?=$paket_pernyataan_minat->getField("PENERIMA_KUASA_JABATAN")?></td> 
                                    </tr>
                                    <tr>    
                                        <td>KTP</td><td><?=$paket_pernyataan_minat->getField("PENERIMA_KUASA_KTP")?></td> 
                                    </tr>
                                    <?php 
                                    if ($paket_pernyataan_minat->getField("PENERIMA_KUASA_FILE")) { ?>
                                      <tr>
                                          <td>File</td>
                                          <td>
                                              <a href="uploads/kualifikasi/<?=$paket_pernyataan_minat->getField("PENERIMA_KUASA_FILE")?>" target="_blank"><img src="images/download.png"></a>
                                          </td> 
                                      </tr>
                                    <?php 
                                    } ?>
                                    </table>
                                  <?php	
                                   }
                                  }
                              }
                               
                          }
                        }
                          ?>
                        </td>
                    <?php
                       unset($rekanan_evaluasi_admin);
                    }
                    ?>
                    <td valign="top" >
                      <form action="/" id="catatanForm<?=$paket_rekanan->getField("PAKET_REKANAN_ID")?>">
                        <!-- <input type="checkbox" style="cursor:pointer" name="reqStatusAdministrasi" value="<?php //$paket_rekanan->getField("PAKET_REKANAN_ID")?>" id="reqStatusAdministrasi<?php //$paket_rekanan->getField("PAKET_REKANAN_ID")?>" onclick="updateMemenuhiSyarat('<?php //$paket_rekanan->getField("PAKET_REKANAN_ID")?>')"  <?php // if($paket_rekanan->getField("LULUS_ADMINISTRASI") == 1) {?> checked="checked" <?php // } ?>  /> -->
                        <!-- <label for="reqStatus">Memenuhi Syarat</label> -->
                        <!-- <br> -->
                        <div class="area-catatan-panitia" style="margin:10px">
                            <div class="isi">
                            <?php
                              $paket_rekanan_kualifikasi = new PaketRekananKualifikasi();
                              $paket_rekanan_kualifikasi->selectByParams(array("A.PAKET_REKANAN_ID" => $paket_rekanan->getField("PAKET_REKANAN_ID"), "A.KODE" => "EVALUASI_ADMIN"));
                              $paket_rekanan_kualifikasi->firstRow();
                              $reqCatatan = $paket_rekanan_kualifikasi->getField("CATATAN");
                              $reqNilai = $paket_rekanan_kualifikasi->getField("NILAI");
                              unset($paket_rekanan_kualifikasi);
                            ?>
                              <input type="checkbox" style="cursor:pointer" name="reqStatusAdministrasi"  id="reqStatusAdministrasi"  <?php if($paket_rekanan->getField("LULUS_ADMINISTRASI") == 1) {?> checked="checked" <?php } ?>  />
                                 <label for="reqStatus" style="font-size: 10px">Memenuhi Syarat</label>


                                <input type="text" name="reqNilai" id="reqNilai" value="<?= $reqNilai ?>" style="width:65px" maxlength="3" class="form-control" placeholder="Nilai" /><br>
                                <!-- <small style="font-weight: bold"> Catatan Untuk Peserta (apabila tidak memenuhi syarat) :</small> -->
                                <textarea style=" height:150px; width: 100%" class="form-control" name="reqCatatan"><?=$reqCatatan?></textarea>
                                <input type="hidden" name="reqPaketRekananId" value="<?=$paket_rekanan->getField("PAKET_REKANAN_ID")?>">
                                <input type="hidden" name="reqKode" value="EVALUASI_ADMIN">
                                <input type="submit" value="Simpan" class="btn btn-primary" style="margin-top: 5px">
                                <!-- <small style="font-weight: bold"> Catatan Untuk Peserta (apabila tidak memenuhi syarat) :</small> -->

                    </form>
                              <script>
                              // Attach a submit handler to the form
                              $( "#catatanForm<?=$paket_rekanan->getField("PAKET_REKANAN_ID")?>" ).submit(function( event ) {
                                  
                                // Stop form from submitting normally
                                event.preventDefault();
                               
                                // Get some values from elements on the page:
                                var $form = $( this ),
                                  catatan = $form.find( "textarea[name='reqCatatan']" ).val(),
                                  paketRekananId = $form.find( "input[name='reqPaketRekananId']" ).val(),
                                  kode = $form.find( "input[name='reqKode']" ).val(),
                                  // status = $form.find( "input[name='reqStatusAdministrasi']" ).val(),
                                  status = $('#reqStatusAdministrasi:checked').val();
                                  nilai = $form.find( "input[name='reqNilai']" ).val(),
                                  url = 'paket_rekanan_kualifikasi_json/add';
                               
                                // Send the data using post
                                var posting = $.post( url, { reqCatatan: catatan, reqPaketRekananId: paketRekananId, reqKode: kode, reqStatus: status, reqNilai: nilai } );
                               
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
                  $no++;
                  if($style == "gelap")
                      $style = "terang";
                  else
                      $style = "gelap";
              
              }
              ?>
            </table>
            <div style="margin-bottom: 5%">
              <a href="main/index/paket_detil/?reqId=<?=$reqId?>" class="btn btn-danger"><i class="fa fa-arrow-left"></i> Kembali</a>
              <!-- <a class="btn btn-primary" href="main/loadUrl/report/evaluasi_kualifikasi_administrasi_excel/?reqId=<?=$reqId?>" target="_blank"><i class="fa fa-print"></i> Cetak</a> -->
            </div>
          </div>
        </div>
      </div>
    </div>
  </div> 
</div>    