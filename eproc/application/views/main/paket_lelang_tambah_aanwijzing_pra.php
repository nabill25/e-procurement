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
$this->load->model("Aanwijzing");
$this->load->library("FileHandler");
include_once("functions/string.func.php");
include_once("functions/date.func.php");
include_once("functions/default.func.php");

$aanwijzing = new Aanwijzing();
$aanwijzing_pasal = new Aanwijzing();

$submitSimpan = $this->input->post("submitSimpan");
$reqId = $this->input->get("reqId");

$aanwijzing->selectByParams(array("PAKET_ID" => $reqId, "AANWIJZING_PARENT_ID" => 0),-1,-1, '', ' ORDER BY KODE ASC');

?> 
<script language="javascript">
$(document).ready(function() {
  
  $(function(){
    $('#ff').form({
      url:'aanwijzing_json/aanwijzing_pra',
      onSubmit:function(){
        return $(this).form('validate');
      },
      success:function(data)
      {
        $.messager.alert('Info', data, 'info');
        // document.location.href = 'main/index/paket_lelang_tambah_aanwijzing_pra/?reqId=<?=$reqId?>';
      }
    });
    
  });
  
});

function addRowPasal2(tableID) 
{
  var table = document.getElementById(tableID);

  var rowCount = table.rows.length;
  var row = table.insertRow(rowCount);
  
  var cell2 = row.insertCell(0);
  cell2.innerHTML = '<input name="reqLinkFile[]"  type="file" id="reqLinkFilePDF" size="100" class="easyui-validatebox" required /><small> <br>Format file .zip & Maksimal ukuran file 10MB </small><input type="hidden" name="reqLinkFileTemp[]"><input type="hidden" name="reqPageCount[]">';

  var cell3 = row.insertCell(1);
  cell3.innerHTML = '<input type="text" name="reqKeterangan[]"  class="form-control easyui-validatebox span6">';

  var cell3 = row.insertCell(2);
  cell3.innerHTML = '<input type="text" name="subPageCount[]"  class="form-control easyui-validatebox span6" onkeypress="return isNumberKey(event)">';
  
  var cell5 = row.insertCell(3);
  cell5.innerHTML = '';

}
</script>

<style type="text/css">
  ul.f {list-style-type: decimal;}
</style>

<div class="row"> 
  <div class="col-md-12 col-sm-12">
    <div class="card">
      <div class="card-header card-head-inverse bg-primary">
        <h4 class="card-title text-white">Upload Materi Aanwijzing 
            <div class="badge badge-glow badge-pill badge-warning">
            <a title="#" onclick="addRowPasal2('dataTablePasal')" data-toogle=""><span class="fa fa-plus text-white font-medium-2 icon-line-height" data-toggle="tooltip" data-placement="top" title="Tambah Data"></span> </a>
        </div>
        </h4>
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

            <div class="table-responsive">
              <table class="table table-bordered mb-0" id="dataTablePasal"> 
                <tbody>
                  <tr class="judul-kolom">
                     <th align="center">File </th>
                     <th colspan="" align="center">Nama</th>
                     <th colspan="" align="center" style="width: 10%">Jumlah Halaman</th>
                     <th colspan="" align="center">Aksi</th>
                  </tr>
                  <?
                  $style='';
                    while($aanwijzing->nextRow())
                    {
                    ?>
                      <tr class="<?=$style?>">
                        <td>
                          <input name="reqLinkFile[]"  type="file" id="reqLinkFile" size="100" validType="fileType['zip']"/>
                          <small> <br>Format file .zip & Maksimal ukuran file 10MB </small>

                          <span style="font-size:9px;"><br>file : <a href="uploads/aanwijzing/<?=$aanwijzing->getField("FILE_UPLOAD")?>"><?=$aanwijzing->getField("FILE_UPLOAD") ?></a></span>
                          <input type="hidden" name="reqLinkFileTemp[]" value="<?=$aanwijzing->getField("FILE_UPLOAD")?>">
                          <input type="hidden" name="reqPageCount[]" value="<?=$aanwijzing->getField("FILE_COUNT")?>">
                        </td>
                        <td>
                          <input type="text" name="reqKeterangan[]" value="<?=$aanwijzing->getField("KETERANGAN")?>" class="form-control easyui-validatebox span6">
                        </td>
                        <td>
                          <input type="text" name="subPageCount[]" value="<?=$aanwijzing->getField("FILE_COUNT")?>" class="form-control easyui-validatebox span6" onkeypress="return isNumberKey(event)">
                        </td>
                        <td align="center" style="text-align: center; width: 30px">
                          <a title="#" onclick="deleteData('aanwijzing_json/delete/', '<?=$aanwijzing->getField("AANWIJZING_ID")?>')" class="btn-aksi"><i class="fa fa-trash" aria-hidden="true"></i></a>
                        </td>
                      </tr>
                    <?
                      $tempNama = $aanwijzing->getField("NAMA");
                      }
                  ?>
                </tbody>
              </table>   
            </div>

            <div class="form-actions">
              <input type="hidden" name="submitSimpan" value="Pasal" />
              <input type="hidden" name="reqId" value="<?=$reqId?>" />
              <a href="main/index/paket_detil/?reqId=<?=$reqId?>" class="btn btn-danger mr-1 text-white"> <i class="fa fa-arrow-left"></i> Kembali </a> 
              <button type="submit" class="btn btn-primary"><i class="fa fa-check-square-o"></i> Simpan</button>
            </div> 
          <hr>
        <div class="card-content collapse show border-info border-darken-2">
          <div class="card-body">
            <h4>Petunjuk Upload File/Dokumen Materi Aanwijzing</h4>
            <ul class="f">
              <li>File yang diupload untuk Materi Aanwijzing harus dalam bentuk .zip </li>
              <li>Dan didalam .zip tersebut terdapat file-file dengan format .jpg</li>
              <li>Jika file aanwijzing masih dalam bentuk .pdf silahkan, ikuti langkah dibawah ini:
                <ul class="list-style-square">
                  <li>Buka url <a href="https://pdftoimage.com" target="_blank">https://pdftoimage.com</a></li>
                  <li>Upload file PDF (klik tombol UPLOAD FILES), tunggu proses upload sampai selesai</li>
                  <li>Kemudian klik tombol DOWNLOAD ALL</li> 
                </ul>
              </li> 
              <li>Sebelum file .zip di upload ke Materi Aanwijzing, perhatikan ketentuan dibawah ini:</li>
                <ul class="list-style-square">
                  <li>Penamaan file .zip harus sama dengan nama file yang ada di dalamnya (nama file .zip = nama file .jpg)</li>
                  <li>Dan tambahkan nomor urut pada nama file .jpg, contoh:</li>
                    <ul class="list-style-square">
                      <li> Nama file .zip <b>materi_aanwijzing_paket_pengadaan_atk.zip</b> </li>
                      <li> Nama file .jpg ditambahkan nomor urut sbb: <br>
                          <b>materi_aanwijzing_paket_pengadaan_atk_1.jpg</b> <br>
                          <b>materi_aanwijzing_paket_pengadaan_atk_2.jpg</b> <br>
                          <b>materi_aanwijzing_paket_pengadaan_atk_3.jpg</b> <br>
                          <b>materi_aanwijzing_paket_pengadaan_atk_4.jpg</b> <br>
                          <b>materi_aanwijzing_paket_pengadaan_atk_5.jpg</b> <br>
                          <b>materi_aanwijzing_paket_pengadaan_atk_6.jpg</b> <br>
                          <b>materi_aanwijzing_paket_pengadaan_atk_7.jpg</b> 
                      </li>
                    </ul>
                </ul>
                <li>Jika file aanwijzing masih dalam bentuk .word silahkan, <i>save as</i> ke format .pdf kemudian, ikuti langhak 3 & 4</li>
                <li>Setelah mengikuti langkah-langkah diatas silahkan, upload Materi Aanwijzing</li>
            </ul>
          </div>
        </div>
        </div>
      </div>
      </form>
    
    </div>
  </div> 
</div>  