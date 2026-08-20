<?php
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 * 
 */

$this->libsession->cekSession();

$this->load->library("kauth");  $userLogin = new kauth();
$this->load->library("paketinfo"); $paketInfo = new paketinfo();
$this->load->model("PaketRekanan");
$this->load->model("PaketDokumen");
$this->load->model("Rekanan");
include_once("functions/string.func.php");
include_once("functions/date.func.php");
include_once("functions/default.func.php");

$paket_dokumen = new PaketDokumen();
$paket_rekanan = new PaketRekanan();
$rekanan = new Rekanan();

$reqMode = $this->input->get("reqMode");
$reqId = $this->input->get("reqId");

/* VALIDASI */
$paket_rekanan_validasi = new PaketRekanan();
$paket_rekanan_validasi->selectByParamsPaketLelang(array("PAKET_ID" => $reqId, "REKANAN_ID" => $this->ID), -1, -1, " AND TANGGAL_DAFTAR IS NOT NULL ");
$paket_rekanan_validasi->firstRow();
if($paket_rekanan_validasi->getField("PAKET_REKANAN_ID") == "")
  exit;

$kirimPenawaran = $paket_rekanan_validasi->getField("KIRIM_PENAWARAN");
$reqKirimPenawaranKode = $paket_rekanan_validasi->getField("KIRIM_PENAWARAN_KODE");
// echo $kirimPenawaran; die();
if($kirimPenawaran == "1")
{
  echo '<script language="javascript">';
  // echo "alert('Penawaran sudah dikirim, silahkan periksa kembali dokumen penawaran anda sebelum masa penawaran berakhir.');";
  echo "alert('Dokumen Penawaran sudah dikirim.');";
  // echo "setTimeout(function(){
  //            document.location.href='".base_url()."main/index/dokumen_penawaran_rekanan/?reqId=".$reqId."';
  //         }, 3000);";
  echo "document.location.href='".base_url()."main/index/dokumen_penawaran_rekanan/?reqId=".$reqId."'";
  echo '</script>';
  exit;
}
$paketInfo->getPaket($reqId);
$reqNama = $paketInfo->nama;

$rekanan->selectByParams(array("A.REKANAN_ID"=>$this->ID),-1,-1);
$rekanan->firstRow();
$rekanan_nama = $rekanan->getField("NAMA");
$rekanan_email = $rekanan->getField("EMAIL");

?>
<script>
  $(function(){
    $('#ff').form({
      url:'paket_rekanan_json/kirim_penawaran',
      onSubmit:function(){
        // return $(this).form('validate');
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
        var isNotif = data.split("--");
        hideLoad();
        if(isNotif[0] == "1") {
          alertError3(isNotif[1]);
        } else if(isNotif[0] == "0") {
          alertSuccess2(isNotif[1]);
          setTimeout(function () {
            document.location.href = "main/index/dokumen_penawaran_password/?reqId=<?=$reqId?>";
          }, 1000);
        }
      }
    });

  });

  function kirimUlang()
  {
    if(confirm('Kirim ulang email sertifikasi?'))
    {
      var win = $.messager.progress({
                    title:'Kirim Email',
                    msg:'Mengirim email kode verifikasi dokumen penawaran...'
                  });
      var jqxhr = $.get( "dokumen_pengadaan_upload_rekanan/email/?reqId=<?=$reqId?>", function(data) {
        $.messager.progress('close');
        $.messager.alert('Info', data, 'info');
      })
      .fail(function() {
        $.messager.progress('close');
        $.messager.alert('Info', 'Kirim email kode verifikasi gagal.', 'info');
      });

    }

  }

</script>

<div class="row">
  <div class="col-md-12 col-sm-12">
    <div class="card">
      <div class="card-header card-head-inverse bg-primary">
        <h4 class="card-title text-white"><?=translate("Dokumen Penawaran", "Proposal Documents")?></h4>
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
            <div class="form-group col-md-12">
              <ul class="nav nav-tabs nav-iconfall">
                <li class="nav-item">
                  <a class="nav-link" href="main/index/dokumen_penawaran_boq/?reqId=<?=$reqId?>">
                    <button class="btn" style="border-radius: 20px; padding: 2% 7%;"> 1. Nilai Penawaran</button>
                  </a>
                </li>
 
              <?php
              if($kirimPenawaran == "1")
              {
              ?>
                <li class="nav-item">
                  <a class="nav-link" href="main/index/dokumen_penawaran_rekanan/?reqId=<?=$reqId?>">
                    <button class="btn" style="border-radius: 20px; padding: 2% 7%;"> 2. Dokumen Penawaran</button>
                  </a>
                </li>
              <?php
              }
              else {
              ?>
                <li class="nav-item">
                  <a class="nav-link" href="main/index/dokumen_penawaran_rekanan/?reqId=<?=$reqId?>">
                    <button class="btn" style="border-radius: 20px; padding: 2% 7%;"> 2. Dokumen Penawaran</button>
                  </a>
                </li>
                <li class="nav-item"> 
                  <a class="nav-link active show" href="#">
                    <i class="fa fa-file-code-o"></i> <h4>3. Masukkan Kode Verifikasi</h4>
                  </a>
                </li>  
              <?php
              }
              ?>
              </ul> 
            </div>
          </div>
          <div class="table-responsive">
            <form id="ff" method="post" class="form-horizontal" role="form">
              <table class="table table-bordered table-hover">
                  <tr class="judul-kolom">
                    <th colspan="3">Terms and Condition <br><a onClick="kirimUlang()"><img src="images/send-mail.png"> Kirim Ulang Sertifikat ke <b><u><?= $rekanan_email ?></u></b></a></th>
                  </tr>
                  <tr class="gelap">
                    <td width="21%" colspan="3">Dengan ini saya menyatakan :</td>
                  </tr>
                  <tr class="gelap">
                    <td width="2%" valign="top">1</td>
                    <td width="90%" align="justify">Tidak akan melakukan praktek KKN;</td>
                    <td width="5%" valign="top"><input style="cursor: pointer;" type="checkbox" name="reqCheck1" id="reqCheck1" value="1" onclick="countChecked()" <?php if($checked == 1) { ?> checked <?php } ?> /></td>
                  </tr>
                  <tr class="gelap">
                    <td width="2%" valign="top">2</td>
                    <td width="90%" align="justify">Akan melaporkan kepada pihak yang berwajib/berwenang apabila mengetahui ada indikasi KKN di dalam proses pengadaan/pekerjaan ini;</td>
                    <td width="5%" valign="top"><input style="cursor: pointer;" type="checkbox" name="reqCheck2" id="reqCheck2" value="1" onclick="countChecked()" <?php if($checked == 1) { ?> checked <?php } ?> /></td>
                  </tr>
                  <tr class="gelap">
                    <td width="2%" valign="top">3</td>
                    <td width="90%" align="justify">Dalam proses pengadaan/pekerjaan ini, berjanji akan melaksanakan tugas secara bersih, transparan, dan professional dalam arti akan mengarahkan segala kemampuan dan sumber daya secara optimal untuk memberikan hasil kerja terbaik mulai dari penyiapan penawaran, pelaksanaa dan penyelesaian pekerjaan/kegiatan ini;</td>
                    <td width="5%" valign="top"><input style="cursor: pointer;" type="checkbox" name="reqCheck3" id="reqCheck3" value="1" onclick="countChecked()" <?php if($checked == 1) { ?> checked <?php } ?> /></td>
                    </td>
                  </tr>
                  <tr class="gelap">
                    <td width="2%" valign="top">4</td>
                    <td width="90%" align="justify">Apabila saya melanggar, saya bersedia dikenakan sanksi moral, sanksi administrasi serta dituntut ganti rugi dan dimasukkan kedalam daftar hitam;</td>
                    <td width="5%" valign="top"><input style="cursor: pointer;" type="checkbox" name="reqCheck4" id="reqCheck4" value="1" onclick="countChecked()" <?php if($checked == 1) { ?> checked <?php } ?> /></td>
                  </tr>
                  <tr class="gelap">
                    <td width="2%" valign="top">5</td>
                    <td width="90%" align="justify">Saya mengerti bahwa dokumen penawaran yang sudah dikirim tidak dapat diubah kembali;</td>
                    <td width="5%" valign="top"><input style="cursor: pointer;" type="checkbox" name="reqCheck5" id="reqCheck5" value="1" onclick="countChecked()" <?php if($checked == 1) { ?> checked <?php } ?> /></td>
                  </tr>
                    </table>
                    <div class="alert alert-info" style="color:#fff">
                      <span style="color: #fff">
                        <i class="fa fa-hand-o-right"></i> Pastikan koneksi jaringan internet stabil; <br>
                        <i class="fa fa-hand-o-right"></i> Pastikan dokumen penawaran yang diupload sudah benar; <br>
                        <i class="fa fa-hand-o-right"></i> Penawaran yang sudah dikirim tidak bisa diubah.<br>
                        <i class="fa fa-hand-o-right"></i> Checklist pernyataan diatas.<br>
                      </span>
                    </div>
                    <script>
                    function countChecked() {
                      var n1 = $("#reqCheck1:checked").length;
                      var n2 = $("#reqCheck2:checked").length;
                      var n3 = $("#reqCheck3:checked").length;
                      var n4 = $("#reqCheck4:checked").length;
                      var n5 = $("#reqCheck5:checked").length;
                      //alert(n);
                      if(n1 && n2 && n3 && n4 && n5){
                          $("#reqSubmit").show(0);
                      }else{
                          $("#reqSubmit").hide(0);
                      }
                    }
                    </script>
                    <div class="area-tombol">
                    <div id="reqSubmit" <?php if($checked == 1) { ?> style="" <?php } else { ?> style="display:none;" <?php } ?> >
                    <table align="center">
                    <tr>
                    <td style="text-align: center">Masukkan 5(lima) digit kode verifikasi yang dikirim via email <b><?= $rekanan_email ?></b> : <br>
                      <div class="position-relative has-icon-left mt-1 mb-1 text-center">
                        <input type="text" name="reqToken" placeholder="*****" class="form-control easyui-validatebox" required="" maxlength="5" style="vertical-align:top; border:1px solid #7aaccb; height:44px; width:200px;text-align:center; border-radius: 25px !important;"  >
                        <div class="form-control-position">
                          <i class="fa fa-lock font-medium-5 line-height-1 text-muted icon-align"></i>
                        </div>
                      </div>
                      <!-- <input type="text" class="" class=" mb-1"  name="reqToken" maxlength="5" style="vertical-align:top; border:1px solid #7aaccb; height:44px; width:100px;text-align:center; border-radius: 25px !important;" /> -->
                    </td>
                    </tr>
                    <tr>
                    <td>
                    <input type="hidden" name="reqId" value="<?=$reqId?>" />
                    <input type="hidden" name="submitSimpan" value="Simpan"/>
                    <button type="submit" class="<?= CLASS_BTN_PRIMARY ?>">Simpan dan Kirim Penawaran</button>
                    <a href="main/index/dokumen_penawaran_rekanan/?reqId=<?=$reqId?>" class="<?= CLASS_BTN_DANGER ?>">
                      <?=translate("Cek Dokumen Penawaran", "Proposal Documents")?>
                    </a>
                    </td>
                    </tr>
              </table>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
  </div>
</div>
