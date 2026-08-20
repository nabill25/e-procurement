<?php
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 * 
 */

$reqPermohonanId = httpFilterRequest("reqPermohonanId"); // Permohonan ID

$this->libsession->cekSession($reqId);

$this->load->library("kauth");  $userLogin = new kauth();
$this->load->library("paketinfo"); $paketInfo = new paketinfo();
$this->load->model(array("Paketkajiulang","Permohonanpaket")); 
$this->load->library("FileHandler");
include_once("functions/string.func.php");
include_once("functions/date.func.php");
include_once("functions/default.func.php");

$paket_kaji_ulang = new Paketkajiulang();
$permohonan = new Permohonanpaket();

if ($this->USER_TYPE_ID == '3') { // Pokja
  // $permohonan->selectByParams(array("A.PERMOHONAN_PAKET_ID" => $reqPermohonanId, "A.PIC" => $this->USER_LOGIN_ID));
  $permohonan->selectByParams(array("A.PERMOHONAN_PAKET_ID" => $reqPermohonanId));
  $permohonan->firstRow();

  // Cek Kelompok Kerja
  $this->load->model("Queryfree");
  $getPokjaID = new Queryfree();
    $getPokjaID->selectByParams("SELECT sk_panitia_id, user_login_id, a.nama, a.nip, b.nip
                FROM panitia a 
                JOIN user_login b on a.nip=b.nip
                WHERE USER_LOGIN_ID = ".$this->USER_LOGIN_ID."
                ");
    $getPokjaID->firstRow();
    $SK = $getPokjaID->getField("SK_PANITIA_ID");

  if($permohonan->getField("KAJI_ULANG") == '1' || $SK != $permohonan->getField("SK_PANITIA_ID")) {
    redirect('/main/index/permohonan_paket_kaji_ulang');
  }
}

if ($this->USER_TYPE_ID == '27') { // Perencana
  $permohonan->selectByParams(array("A.PERMOHONAN_PAKET_ID" => $reqPermohonanId),-1,-1," AND A.POSTING IS NOT NULL AND A.PIC IS NOT NULL");
  $permohonan->firstRow();
  if($permohonan->getField("KAJI_ULANG") == '1' || $permohonan->getField("PERMOHONAN_PAKET_ID") == '') {
    redirect('/main/index/permohonan_paket_kaji_ulang');
  }
}

$paket_kaji_ulang->selectByParams(array("A.PERMOHONAN_PAKET_ID" => $reqPermohonanId));

?>
<script type="text/javascript">
$(document).ready(function() {

	$(function(){
		$('#ff').form({
			url:'kajiulang_chat_json/dokumen_aanwijzing_tanggapan',
			onSubmit:function(){
				return $(this).form('validate');
			},
			success:function(data){
        alertSuccess2(data);
        setTimeout(function() {
				  document.location.href = 'main/index/permohonan_paket_kaji_ulang_add/?reqPermohonanId=<?=$reqPermohonanId?>';
        }, 2000);
			}
		});

	});

  $('#btnTeruskan').on('click', function (){  

    $.messager.confirm('Konfirmasi',"Yakin Kaji Ulang sudah selesai, lanjut proses ke Permohonan Paket?",function(r){
      if (r){ 
        $.post("kajiulang_chat_json/approve", 
          { reqId: <?= $reqPermohonanId ?> }, 
          function(data){
              $.messager.alert('Info', data, 'info');
              setTimeout(function() {
                document.location.href = 'main/index/permohonan_paket_kaji_ulang';
              }, 2000);
          }
        );
      }
    });
  });

  $('#btnTeruskan2').on('click', function (){  

    $.messager.confirm('Konfirmasi',"Update HPS?",function(r){
      if (r){ 
        $.get("api_ui/updatehps/<?= $reqPermohonanId ?>", function(data){
            $.messager.alert('Info', data, 'info');
            setTimeout(function() {
                document.location.href = 'main/index/permohonan_paket_kaji_ulang_add/?reqPermohonanId=<?= $reqPermohonanId ?>';
            }, 2000);
        });
      }
    });

  });

});
</script>

<style type="text/css">
input::placeholder { opacity: 0.3 !important; } table th { padding: 5px !important; } .terang { background-color: rgba(245, 247, 250, .5);} .headerTR {background-color: #77c8e5 !important;}
</style>

<div class="row">
  <div class="col-md-12 col-sm-12">
    <div class="card">
      <div class="card-header card-head-inverse bg-primary" id="card-header-klarifikasi">
        <h4 class="card-title text-white">Kaji Ulang
        </h4>
        <div class="heading-elements">
            <ul class="list-inline mb-0">
              <li><a data-action="expand"><i class="ft-maximize"></i></a></li>
            </ul>
        </div>
      </div>
      <div class="card-content collapse show border-info border-darken-2">
        <div class="card-body area-datatable">
            <table  class="table table-bordered mb-1" id="tbl_bidang">
              <tbody>
                <?php
                if ($paket_kaji_ulang->countRow() < 0) {
                   echo '<tr><td colspan="2">Belum ada chat</td></tr>';
                } else {
                  $i=1;
                  while($paket_kaji_ulang->nextRow())
                  {
                    $tglupload = explode('.', $paket_kaji_ulang->getField("CREATED_DATE"));
                ?>
                  <tr >
                    <td width="80%">
                      <i class="fa fa-user"></i> <?=$paket_kaji_ulang->getField("USER_NAMA")?> <br>
                        <?=$paket_kaji_ulang->getField("KETERANGAN")?> <br>
                        <?php if ($paket_kaji_ulang->getField("PATH_FILE")) { ?>
                          <a href="uploads/kajiulang/<?=$paket_kaji_ulang->getField("PATH_FILE")?>" target="_blank" class="badge badge-primary">
                              <i class="fa fa-download" aria-hidden="true"></i> Donwload
                          </a><br>
                        <?php } ?>
                        <small><i class="fa fa-clock-o"></i> <?=$tglupload[0] ?></small>
                    </td>
                    <td width="3%">
                      <?php
                        if ($paket_kaji_ulang->getField("CREATED_BY") == $this->USER_LOGIN_ID)
                        { ?>
                        <a onClick="deleteData('kajiulang_chat_json/delete/', '<?=$paket_kaji_ulang->getField("PAKET_KAJI_ULANG_ID")?>')" class="btn-aksi">
                          <?= ICON_DELETE ?>
                        </a>
                      <?php
                      } ?>
                    </td>
                  </tr>
                <?php
                  $i++;
                  }
                }
              ?>
              </tbody>
            </table>

            <form id="ff" class="easyui-form " method="post" novalidate enctype="multipart/form-data">

                <div class="row">
                  <div class="form-group col-md-12">
                    <label>Ketik pesan disini</label>
                    <textarea name="reqKeterangan" id="reqKeterangan" cols="45" rows="5" class="easyui-validatebox form-control" required></textarea>
                  </div>
                </div>

                <div class="row">
                  <div class="form-group col-md-12">
                    <label>Lampiran <?= UPLOAD_PDF_2MB ?></label>
                    <input type="file" class="form-control" name="reqLinkFile" id="reqLinkFilePDF" required validType="fileType['pdf']" />
                  </div>
                </div>

                <div class="form-actions">
                  <input type="hidden" name="reqPermohonanId" value="<?=$reqPermohonanId?>" />
                  <a href="main/index/permohonan_paket_kaji_ulang" class="<?= CLASS_BTN_DANGER ?> mr-1"><?= BTN_KEMBALI ?></a>
                  <button type="submit" class="<?= CLASS_BTN_PRIMARY ?> mr-1"><?= BTN_KIRIM ?></button>

                  <a href="main/index/permohonan_paket_kaji_ulang_add/?reqPermohonanId=<?=$reqPermohonanId?>" class="<?= CLASS_BTN_INFO ?> mr-1 pull-right"><?= BTN_REFRESH ?></a>
                  <?php
                    if ($this->USER_TYPE_ID == 27 && $this->LEVEL_PERENCANA == '3') { // PERENCANA > KASUBDIT  ?>
                      <a id="btnTeruskan2" class="<?= CLASS_BTN_INFO ?> pull-right mr-1"><i class="fa fa-check-square-o"></i> Update HPS </a>
                      <a id="btnTeruskan" class="<?= CLASS_BTN_WARNING ?> pull-right mr-1"><i class="fa fa-check-square-o"></i> Selesai Kaji Ulang </a>
                    <?php
                    } ?> 
                </div>
            </form>

        </div>
      </div>
    </div>
  </div>
</div>

<script type="text/javascript">
  function test(file,id) {
    // var n = $("#check"+id+":checked").length;
    var n = $('input[name="admin'+id+'"]:checked').val();
    var c = $("#catatan"+id).val();
    // alert(n);
    $.getJSON("kajiulang_chat_json/updateChecklistPenawaran/?id="+id+"&status="+n+"&catatan="+c,
      function(data){
        if (data.RESPONSE === 'Gagal') {
          alertError3(data.PESAN); 
        } else {
          alertSuccess2(data.PESAN); 
        }
    });
  }

  function myFunction(a) {
    var id = "myPass"+a;
    var copyText = document.getElementById(id);
    copyText.select();
    copyText.setSelectionRange(0, 99999)
    document.execCommand("copy");
    // alert("Copied the text: " + copyText.value);
    alertSuccess2("Password disalin "+copyText.value);
  }
</script> 