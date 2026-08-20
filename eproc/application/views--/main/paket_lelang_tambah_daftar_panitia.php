<?php
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 *
 */
$reqId = $this->input->get("reqId");
$this->libsession->cekSession($reqId);

$this->load->library("kauth");  $userLogin = new kauth();
$this->load->library("paketinfo"); $paketInfo = new paketinfo();
$this->load->model("PaketPanitia");
$this->load->model("SKPanitia");
include_once("functions/string.func.php");
include_once("functions/date.func.php");
include_once("functions/default.func.php");

$paket_panitia = new PaketPanitia();
$sk_panitia = new SKPanitia();

$reqMode = $this->input->get("reqMode");
$submitSimpan = $this->input->post("submitSimpan");
$reqRekananId = isset($_POST["reqRekananId"]) ? $_POST["reqRekananId"] : '';

$paketInfo->getPaket($reqId);
$reqNama = $paketInfo->nama;
$reqUUID = $paketInfo->uuid;

$paket_panitia->selectByParams2Group(array("A.PAKET_ID" => $reqId));
?>

<script type="text/javascript">
$(document).ready(function() {

	$(function(){
		$('#ff').form({
			url:'paket_panitia_json/add',
			onSubmit:function(){
				return $(this).form('validate');
			},
			success:function(data){
        alertSuccess2(data);
          setTimeout(function() {
            location.reload();
          }, 2000);
			}
		});

	});

  $('input:radio[name=reqKetua]').change(function() {
    var text = this.value;
    const myArray = text.split("----");

    $.messager.confirm('Confirm','Apakah '+myArray[1]+' sebagai Pengelola Tim ?',function(r){
      if (r){
        $.getJSON('paket_panitia_json/tunjuk_ketua_json/?reqId=<?=$reqId?>&reqNIP='+myArray[0],
        function(data){
          // alert(data);
          if (data.PESAN === 'Data berhasil disimpan.') {
            alertSuccess2(data.PESAN);
          } else {
            alertError2(data.PESAN);
          }
          setTimeout(function() {
            document.location.reload();
          }, 2000);
        });
      }
    });
  });

});

function kunciPanitia()
{
  $.messager.confirm("Konfirmasi","Setelah Kunci Tim Pengadaan, anda tidak dapat merubah Tim Pengadaan. Apakah yakin ?",function(r){
    if (r){
      $.get( "paket_panitia_json/kunci_tim_pengadaan/?reqId=<?=$reqId?>", function( data ) {
        if(data == "1") {
          alertSuccess2('Kunci Tim Pengadaan berhasil disimpan.');
        } else {
          alertError2('Kunci Tim Pengadaan gagal disimpan, silahkan dicoba kembali.');
        }
        setTimeout(function() {
          document.location.reload();
        }, 2000);
      });
    }
  });
}

</script>

<div class="row">
  <div class="col-md-12 col-sm-12">
    <div class="card">
      <div class="card-header card-head-inverse bg-primary">
        <h4 class="card-title text-white">Tim Pengadaan </h4>
        <a class="heading-elements-toggle"><i class="fa fa-ellipsis-v font-medium-3"></i></a>
        <div class="heading-elements">
            <ul class="list-inline mb-0">
              <li><a data-action="expand"><i class="ft-maximize"></i></a></li>
              <!-- <li><a data-action="close"><i class="ft-x"></i></a></li> -->
            </ul>
        </div>
      </div>

      <form id="ff" class="easyui-form form-horizontal" method="post" data-options="novalidate:true" enctype="multipart/form-data">
      <div class="card-content collapse show border-info border-darken-2">
        <div class="card-body area-datatable">

            <div class="table-responsive">
              <table class="table table-bordered mb-0">
                <thead>
                  <tr class="judul-kolom">
                    <td>NIP/NUP</td>
                    <td>Nama</td>
                    <td width="15%">Jabatan</td>
                    <!-- <td>Ketua</td> -->
                    <!-- <td>Pakta Integritas</td> -->
                    <td class="text-center" width="15%">Aksi</td>
                  </tr>
                </thead>
                <tbody id="tbodyPanitia">
                  <?php
            				$i=1;
            				$style="gelap";
                    $kunciPanitia = 0;
                    $ketuaKah = 0;
            				while($paket_panitia->nextRow())
            				{
                      if ($paket_panitia->getField("KUNCI_PANITIA") == '1') {
                        $kunciPanitia++;
                      }

                      if ($paket_panitia->getField("KETUA") == '1') {
                        $nipKetua = $paket_panitia->getField("NIP");
                        $ketuaKah++;
                      }

            					$input = $paket_panitia->getField("NAMA").";".$paket_panitia->getField("NIP").";".$paket_panitia->getField("JABATAN").";".$paket_panitia->getField("KETUA");
            				?>
                    <tr>
                      <td><?=$paket_panitia->getField("NIP")?></td>
                      <td><?=$paket_panitia->getField("NAMA")?></td>
                      <!-- <td><?=$paket_panitia->getField("JABATAN")?></td> -->
                      <td>
                        <?php
                        if ($paket_panitia->getField("FUNGSI") == 'PEMBUAT') {
                          echo "PIC";
                        }
                        else {
                          if ($paket_panitia->getField("KETUA") == '1') {
                            echo "Ketua";
                          } else {
                            echo "Anggota <br>";
                          }
                            if ((int)$this->USER_TYPE_ID == 3 && $paketInfo->user_login_id == $this->USER_LOGIN_ID && $kunciPanitia == 0 && $ketuaKah == 0) {
                              // echo '<input name="reqKetua" type="radio" value="'.$paket_panitia->getField("NIP").'----'.$paket_panitia->getField("NAMA").'"> <small>Pengelola Tim ?</small>';
                            }
                          // }
                        }?>
                      </td>
                      <!-- <td>
                        <?php
                          // if ($paket_panitia->getField("KODE") == '')
                          //   echo '<span style="color:#F00">~ BELUM VALIDASI ~</span>';
                          // else


                         ?>
                      </td> -->
                      <td class="text-center">
                        <?php
                        if ($paket_panitia->getField("KODE") == '' && $paket_panitia->getField("FUNGSI") != 'PEMBUAT')
                        {
                         ?>
                      	<input type="hidden" name="reqNIP[]" value="<?=$paket_panitia->getField("NIP")?>">
                          <?php
                          if ($kunciPanitia > 0 ) {}
                          else
                          {
                              if ((int)$this->USER_TYPE_ID == 3 && $paketInfo->user_login_id == $this->USER_LOGIN_ID  && $ketuaKah == 0 || ($this->NIP == $nipKetua && $paket_panitia->getField("NIP") != $this->NIP)) {
                           ?>
                            <a title="#" onclick="$(this).parent().parent().remove();" class="btn-aksi"><i class="fa fa-trash" aria-hidden="true"></i></a>
                        <?php
                              }
                          }
                        } else {
                          if ($paket_panitia->getField("FUNGSI") == 'PEMBUAT') {
                            echo '<span style="color:green">PEMBUAT PAKET</span>';
                          } else {
                            echo '<span style="color:#F00"><span class="fa fa-check"></span> SUDAH VALIDASI</span>';
                          }
                        } ?>
                      </td>
                    </tr>
                  <?php
          				  $i++;
          				  if($style == "gelap")
          					  $style = "terang";
          				  else
          					  $style = "gelap";
          				}
          				?>
                </tbody>
                <?php
                if ($kunciPanitia > 0 ) {}
                else
                {
                  if ((int)$this->USER_TYPE_ID == 3 && $paketInfo->user_login_id == $this->USER_LOGIN_ID && $ketuaKah == 0 || $this->NIP == $nipKetua) {
                ?>
                <thead>
                  <tr>
                    <div class="badge badge-pill badge-warning mb-1">
                      <a id="btnAdd" onClick="openAdd('main/loadUrl/main/panitia/?reqId=<?=$reqId?>');" data-toogle=""><span class="fa fa-plus text-white icon-line-height" data-toggle="tooltip" data-placement="top" title="Tambah Data"></span> Tambah </a>
                    </div>
                  </tr>
                </thead>
                <?php
                  }
                } ?>
              </table>
            </div>

            <div class="form-actions">
          		<input type="hidden" name="reqId" value="<?=$reqId?>">
              <a href="main/index/paket_detil/?eid=<?=$reqId?>&key=<?=$reqUUID?>" class="<?= CLASS_BTN_DANGER ?> mr-1"> <?= BTN_KEMBALI ?> </a>
              <a href="main/loadUrl/report/pakta_integritas_panitia_cetak_pdf/?reqId=<?=$reqId?>" target="_blank" class="<?= CLASS_BTN_PRIMARY ?>"><span class="fa fa-print"></span> Cetak Pakta Integritas</a>
              <?php
              if ((int)$this->USER_TYPE_ID == 3 && $kunciPanitia == 0 && $this->NIP == $nipKetua) {
              ?>
                <a onClick="kunciPanitia();" id="btnPublish" class="<?= CLASS_BTN_SUCCESS ?>"><span class="fa fa-lock"></span> Kunci Tim Pengadaan</a>
              <?php
              }

              if ($kunciPanitia > 0 ) { }
              else
              {
                if ((int)$this->USER_TYPE_ID == 3 && $paketInfo->user_login_id == $this->USER_LOGIN_ID && $ketuaKah == 0 || $this->NIP == $nipKetua) {?>
              <button type="submit" class="<?= CLASS_BTN_PRIMARY ?> pull-right"><?= BTN_SIMPAN ?></button>
              <?php
                }
              } ?>
          		<input type="hidden" name="submitSimpan" value="Simpan" />
            </div>

        </div>
      </div>
      </form>

    </div>
  </div>
</div>
