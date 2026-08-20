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
$this->load->library("kauth");  $userLogin = new kauth();
include_once("functions/string.func.php");
include_once("functions/default.func.php");
include_once("functions/date.func.php");
$this->load->model(array("PaketNegoisasi","Rekanan"));

$reqPaketPenawaranId = $this->input->get("reqPaketPenawaranId");

$paket_negoisasi = new PaketNegoisasi();
$paket_negoisasi->selectByParamsMonitoring(array("A.PAKET_PENAWARAN_ID" => $reqPaketPenawaranId));
$paket_negoisasi->firstRow();
$penawaranNegosiasi =  $paket_negoisasi->getField("UNIT_PRICE");
$jumlahNegosiasi =  $paket_negoisasi->getField("TOTAL");
$setujui =  $paket_negoisasi->getField("SETUJUI");
$reqId =  $paket_negoisasi->getField("PAKET_ID");
$paketPenawaranId = $reqPaketPenawaranId;

$paketInfo->getPaket($reqId);
$reqNamaPaket = $paketInfo->nama;
$reqJenisPekerjaan = $paketInfo->jenis;
$reqJenisPekerjaanId = $paketInfo->jenis_id;
$reqMetodeEvaluasi = $paketInfo->metode_evaluasi;
$reqRekananIdPemenang = $paketInfo->rekanan_id_pemenang;

$rekanan = new Rekanan();
$rekanan->selectByParams(array("A.REKANAN_ID" => $reqRekananIdPemenang));
$rekanan->firstRow();
$rekananNama =  $rekanan->getField("NAMA");

?>

<!doctype html>
<html>
<head>
<meta charset="UTF-8">
<title><?= SYSTEM_NAME.' | '.SYSTEM_NAME_PT ?></title>
 
<link href="lib/startbootstrap-sb-admin-2-1.0.7/bower_components/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">
<script src="lib/startbootstrap-sb-admin-2-1.0.7/bower_components/jquery/dist/jquery.min.js"></script>
<script src="lib/startbootstrap-sb-admin-2-1.0.7/bower_components/bootstrap/dist/js/bootstrap.js"></script>

 
<style>
.chat {list-style: none;margin: 0;padding: 0;}.chat li {margin-bottom: 3px;padding-bottom: 3px;border-bottom: 1px dotted #B3A9A9;}.chat li.left .chat-body {*margin-left: 60px;margin-left:0px;}.chat li.right .chat-body {margin-right: 60px;*border:1px solid cyan;}.chat li.left .chat-body .header{padding-left:10px;padding-right:10px;}.chat li.left .chat-body .header .primary-font{font-size:14px !important;}.chat li.left .chat-body .header small.pull-right.text-muted{font-size:13px !important;color:#f25824;padding-top:3px;}.chat li .chat-body p {margin: 0;color: #777777;font-size:14px !important;padding:0 10px;}.panel .slidedown .glyphicon, .chat .glyphicon {margin-right: 5px;}.panel-body {overflow-y: scroll;height: 270px;}::-webkit-scrollbar-track {-webkit-box-shadow: inset 0 0 6px rgba(0,0,0,0.3);background-color: #F5F5F5;}::-webkit-scrollbar {width: 12px;background-color: #F5F5F5;}::-webkit-scrollbar-thumb {-webkit-box-shadow: inset 0 0 6px rgba(0,0,0,.3);background-color: #555;}.uraian{border:1px solid #e5e5e5;padding:10px;-webkit-border-top-left-radius: 4px;-webkit-border-top-right-radius: 4px;-moz-border-radius-topleft: 4px;-moz-border-radius-topright: 4px;border-top-left-radius: 4px;border-top-right-radius: 4px;}.unit-price{background:#f6f6f6;padding:10px;border:1px solid #e5e5e5;border-width:0px 1px 1px 1px;-webkit-border-bottom-right-radius: 4px;-webkit-border-bottom-left-radius: 4px;-moz-border-radius-bottomright: 4px;-moz-border-radius-bottomleft: 4px;border-bottom-right-radius: 4px;border-bottom-left-radius: 4px;}</style> <style> .col-md-12.area-negosiasi-chat{*border:1px solid red;}.triangle-isosceles.right .header{*background:pink;}.triangle-isosceles.right p{text-align:right;*background:yellow;*padding-right:0px;display:inline-block;width:100%;*margin-top:10px;float:right;}.col-md-12.area-negosiasi-chat .panel-footer{}.col-md-12.area-negosiasi-chat .panel-footer input.form-control.input-sm{width:calc(100% - 50px);}.col-md-12.area-negosiasi-chat .panel-footer span.input-group-btn{*width:50px;float:left;}.col-md-12.area-negosiasi-chat .panel-footer span.input-group-btn button{margin-top:0px;padding:0 7px !important;font-size:16px !important;line-height:normal !important;}#daddy-shoutbox-form input.form-control.input-sm{*padding-top:16px;*padding-bottom:16px;*color:#333 !important;*border:1px solid red !important;font-size:16px !important;line-height:normal !important;}
  input[type="file"] { width: 0px; }
  .file-upload { display: block; width: 30px; border: none; outline: none; margin-bottom: 1px;
  font: 16px/28px 'Open Sans','Helvetica Neue',Helvetica,sans-serif; color: #3f3f3f;font-weight: 300;-webkit-border-radius: 0;cursor: pointer;text-align: center;} .modal-footer { display: none; }

</style>

</head>

<body>

  <?php
    $total = round($paket_negoisasi->getField("QUANTITY") * $paket_negoisasi->getField("UNIT_PRICE"), 2);
  ?>

  <div class="card mb-1 border-darken-1">
    <div class="card-content">
      <div class="p-1"> 
        <p style="font-size: 1.2em;"><b><?= $rekananNama ?></b></p>
          <!-- <form id="ff" class="form-horizontal" role="form" method="post" novalidate > -->
          <div class="row">
            <div class="form-group col-md-12">
              <!-- <label>Rincian : <?=$paket_negoisasi->getField("QUANTITY")?> x &nbsp;&nbsp;</label> -->
              <label>Harga Negosiasi</label>
              <input class="form-control" type="text" name="reqUnitPrice" id="reqUnitPrice<?=$paketPenawaranId?>" value="<?=$penawaranNegosiasi?>"  OnKeyUp="summaryDetil(); saveDetil('<?=$paketPenawaranId?>', 'reqUnitPrice<?=$paketPenawaranId?>', event); " pattern="^\$\d{1,3}(,\d{3})*(\.\d+)?$" data-type="currency" style=" text-align:right; width: 100%; margin-bottom: 5px" <?php  if($setujui == "") { ?>  <?php } else { ?> disabled <?php } ?>>
              <!-- = <span id="reqTotalNegosiasi" style="font-weight:normal; font-size:14px"><?=numberToIna($total)?></span> <br> -->
              <sup>contoh decimal: 89,000.50</sup>
              <div class="badge badge-primary" style="font-size:14px; color: #fff; text-align: center; width: 100%; padding:5px 0">Tekan enter setelah mengubah harga.</div>
            </div>
          </div>

          <div class="panel panel-primary" >
            <div class="alert bg-primary alert-icon-left alert-arrow-left alert-dismissible mb-2" role="alert" style="margin-bottom: 0px !important">
              <span class="alert-icon"><i class="fa fa-comments" aria-hidden="true"></i></span>  <strong>Chat</strong>
            </div>
            <div>
              <div class="panel-body area-negosiasi-chat">
               <script type="text/javascript">
                      var count = 0;
                      var files = 'lib/shoutbox2/';
                      var lastTime = 0; 

                      function duplicateVal() {
                        var v = $('#reqUnitPrice<?=$paketPenawaranId?>').val();
                        // const result = Math.trunc(Number(v.replace(/,/g, '')));
                        $('#btn-input2').val(v);
                      }

                      function success(response, status)  {
                        if(status == 'success') {
                          lastTime = response.time;
                          $('#daddy-shoutbox-response').html('<img src="'+files+'images/accept.png" />');
                          // $('.chat').prepend(prepareDirect(response));
                          $('#btn-input').val('');
                          $('#btn-input2').val('');
                          $('#btn-input').focus();
                          $('#list-'+count).fadeIn('slow');
                          refresh();
                          // timeoutID = setTimeout(refresh, 3000);
                        }
                      }

                      function validate(formData, jqForm, options) {
                        if(($("#btn-input").val()) == '' && ($("#btn-input2").val()) == '')
                        {
                          alert('Isi pesan terlebih dahulu.');
                          return false;
                        }

                        $('#daddy-shoutbox-response').html('<img src="'+files+'images/loader.gif" />');
                        clearTimeout(timeoutID);
                        
                        $('#file-to-upload').val(''); 
                        $('#file-status, #file-name').hide();
                      }

                      function refresh() {
                        $.getJSON("nego_shoutbox_json/nego_shoutbox/?reqPaketPenawaranId=<?=$reqPaketPenawaranId?>&action=view&time="+lastTime+"&rekananid="+<?= $reqRekananIdPemenang ?>, function(json) { 
                          $('.kolomchatting<?=$reqPaketPenawaranId?>').html(json.message);
                        });

                        $.getJSON("paket_negoisasi_json/ambil_negosiasi/?reqId=<?=$reqPaketPenawaranId?>", function(json) {
                            if(json.SETUJUI == "1")
                          {
                            $("#setujui<?=$reqPaketPenawaranId?>").show();
                            $("#reqUnitPrice<?=$reqPaketPenawaranId?>").attr('disabled', true);
                            $("#reqUnitPriceNegosiasi<?=$reqPaketPenawaranId?>").attr('disabled', true);
                          }
                        });

                        timeoutID = setTimeout(refresh, 3000);
                      }

                      // wait for the DOM to be loaded
                      $(document).ready(function() {
                          var options = {
                            dataType:       'json',
                            beforeSubmit:   validate,
                            success:        success
                          };
                          $('#daddy-shoutbox-form').ajaxForm(options);
                          $('.chat').html('');
                          timeoutID = setTimeout(refresh, 100);
                          refresh();
                          // alert('aaa');
                      });

                  function summaryDetil()
                  {
                      var txtQuantity = Number('<?=$paket_negoisasi->getField("QUANTITY")?>');
                      var txtHarga = Number(FormatAngkaNumber($("#reqUnitPrice<?=$paketPenawaranId?>").val()));
                      var jumlah = (txtHarga * txtQuantity).toFixed(2);
                      $("#reqTotalNegosiasi").text(FormatCurrencyBaru(jumlah));
                  }

                  function saveDetil(paketPenawaranId, inputId, event)
                  {
                    if(event.keyCode == "13")
                    {
                      if(confirm("Ubah data negosiasi rincian terpilih ?"))
                      {
                        var nilai = $("#"+inputId).val();

                        $("#reqUnitPriceNegosiasi<?=$paketPenawaranId?>").val(nilai);
                        summary();
                        duplicateVal();

                        $('#btn-chat').click();
                        $.getJSON("paket_negoisasi_json/negosiasi/?reqId="+paketPenawaranId+"&reqNilai="+nilai, function(json) {
                          alert(json);
                        });

                        $.getJSON("nego_shoutbox_json/nego_shoutbox/?reqPaketPenawaranId=<?=$reqPaketPenawaranId?>&action=view&time="+lastTime+"&rekananid="+<?= $reqRekananIdPemenang ?>, function(json) { 
                          $('.kolomchatting<?=$reqPaketPenawaranId?>').html(json.message);
                        });

                        $('#btn-input2').val('');

                      }
                    }

                  }

                  // === 2. Enter untuk mengirim pesan ===
                  $('#btn-input').on('keypress', function (e) {
                      if (e.which === 13) {
                          e.preventDefault();
                          $('#btn-chat').trigger('click');
                      }
                  });

                  $('#btn-chat').on('click', function (e) {
                        e.preventDefault();

                        var form = $('#daddy-shoutbox-form')[0];
                        var data = new FormData(form);

                        $.ajax({
                            url: 'nego_shoutbox_json/nego_shoutbox/?action=add',
                            type: 'POST',
                            data: data,
                            contentType: false,
                            processData: false,
                            cache: false,
                            dataType: 'json',
                            beforeSend: function() {
                                // console.log("Upload mulai…");
                                // if(($("#btn-input").val()) == '')
                                // {
                                //   alert('Isi pesan terlebih dahulu.');
                                //   return false;
                                // }
                                $('#btn-chat').prop('disabled', true);  // cegah double klik
                            },
                            success: function (response) {
                                console.log("Berhasil:", response);

                                // reset input setelah kirim
                                $('#btn-input').val('');
                                $('#file-to-upload').val('');
                                $('#file-status, #file-name').hide();

                                // refresh chat
                                $('.chat').html('');
                                timeoutID = setTimeout(refresh, 100);
                                refresh();
                            },
                            error: function (xhr) {
                                console.log("ERROR upload:", xhr.responseText);
                            },
                            complete: function() {
                                $('#btn-chat').prop('disabled', false);
                            }
                        });

                    });
 

                  $(document).ready(function() {
                    $(function(){
                      $("input[data-type='currency']").on({
                          keyup: function() {
                            formatCurrencyDecimal($(this));
                          },
                          blur: function() {
                            formatCurrencyDecimal($(this), "blur");
                          }
                      });
                    });
                  });

                  $('#file-to-upload').on('change', function () {
                      if (this.files.length > 0) {
                          var fileName = this.files[0].name;

                          $('#file-status').show();        // tanda cek ✔
                          $('#file-name').text(fileName).show();   // tampilkan nama file
                      } else {
                          $('#file-status').hide();
                          $('#file-name').hide();
                      }
                  });

                  </script>
                <ul class="chat kolomchatting<?=$reqPaketPenawaranId?>"></ul>
              </div>

              <div class="panel-footer">
                <div class="input-group" style="width:100%; margin-top: 1%">
                  <!-- <form id="daddy-shoutbox-form" action="nego_shoutbox_json/nego_shoutbox/?action=add" method="post" style="width:100%;" enctype="multipart/form-data"> -->
                  <form id="daddy-shoutbox-form" method="post" style="width:100%;" enctype="multipart/form-data">
                    <input type="hidden" name="nickname" value="Pelaksana Pengadaan" />
                    <input type="hidden" name="reqPaketPenawaranId" value="<?=$reqPaketPenawaranId?>" readonly />
                    <input type="hidden" name="reqHalaman" value="0" readonly />
                    <input type="hidden" name="reqKode" value="0" readonly />
                    <input type="hidden" name="reqRekananId" value="<?= $reqRekananIdPemenang ?>" readonly />

                    <div class="input-group">
                      <label for="file-to-upload" class="file-upload"><span class="fa fa-file-pdf-o"></span></label>
                      <input type="file" name="reqLinkFile" id="file-to-upload">
                      <input id="btn-input" type="text" name="message" class="form-control input-sm" placeholder="Type your message here..." />
                      <input type="submit" value="Submit"  style="display:none" />
                      <div class="input-group-append">
                        <button class="btn btn-primary btn-sm" id="btn-chat" style="left: 0 !important;">Kirim</button>
                      </div>
                    </div>
                    <input id="btn-input2" type="hidden" name="message2" class="form-control input-sm" placeholder="Type your message here..." />
                    <span id="file-status" style="color:green;display:none;">
                      <i class="fa fa-check"></i> File terlampir
                    </span>
                  </form>
                </div>
              </div>
            </div>
          </div>
      </div>
    </div>
  </div>

<script type="text/javascript" src="lib/shoutbox2/javascript/jquery.js"></script>
<script type="text/javascript" src="lib/shoutbox2/javascript/jquery.form.js"></script>

</body>
</html>
