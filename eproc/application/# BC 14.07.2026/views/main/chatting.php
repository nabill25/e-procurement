<?php
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 * 
 */

include_once("functions/encrypt.func.php");

$reqRekananIdArry = $_GET['reqRekananId'];
$reqId = $_GET['reqPaketId']; // Paket ID
$reqRekananId = explode('||||||', $reqRekananIdArry);
$totalRekanan = count($reqRekananId)

 ?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <base href="<?=base_url();?>" />

    <link rel="icon" href="../../favicon.ico">

    <title><?= SYSTEM_NAME_PT ?></title>
    <link rel="stylesheet" type="text/css" href="<?=base_url() ?>assets/new/css/vendors.css">
    <link rel="stylesheet" type="text/css" href="<?=base_url() ?>assets/new/vendors/css/ui/prism.min.css">
    <link rel="stylesheet" type="text/css" href="<?=base_url() ?>assets/new/css/app.css">
    <link rel="stylesheet" type="text/css" href="<?=base_url() ?>assets/new/css/style.css">
    <script type="text/javascript" src="js/jquery-1.6.1.min.js"></script>
  </head>
  <style type="text/css">
  .direct-chat-text {
    text-align: left !important;
  }
  .popup-messages2 {
      background: #fff none repeat scroll 0 0 !important;
      border-color: 1px solid #3f9684 !important;
      height: 395px !important;
      overflow: auto !important;
  }
  .popup-messages2 .chat-box-single-line {
      border-bottom: 1px solid #a4c6b5;
      height: 12px;
      margin: 7px 0 20px;
      position: relative;
      text-align: center;
  }
  .popup-messages2 abbr.timestamp {
      background: #3f9684 none repeat scroll 0 0;
      color: #fff;
      padding: 0 11px;
  }
  </style>

  <script type="text/javascript">
    function showChat(a,b) {
        $('.direct-chat-messages').html('Proses pengambilan data...');
        $('#titlePenyedia').html(a);
        $('#reqRekananId').val(b);
        updateRead();
    }

    function getNotif(a,b) {
      console.log(a);
      $.getJSON("chat_json/getNotif/<?= $reqId ?>/4/"+b, function(data) {
        if (data.countchat > 0) {
          $('#reqNotif'+data.id).html('<span class="badge badge-warning" style="border-radius:50%">&nbsp;</span>');
        } else {
          $('#reqNotif'+data.id).html('');
        }
      });
    }

    function updateRead() {
      var rekananid = $('#reqRekananId').val();
      if (rekananid) {
        $.getJSON("chat_json/updateRead?reqId=<?= $reqId ?>&reqJenis=4&reqRekananId="+rekananid, function(data) {
        });
      }
    }

    $(function(){
      $("#removeClass").click(function () {
        updateRead();
        // $('#qnimate').slideToggle("slow");
      });
    });
    $(document).ready(function() {
      $(function(){
        $('#ffnego').form({
          url:'<?= base_url('chat_json/negoshoutbox') ?>',
          onSubmit:function(){
            var v=$(this).form('validate');
            if(v) {
              $('#submitPesan').removeClass('fa fa-send');
              $('#submitPesan').addClass('fa fa-refresh');
              return v;
            } else {
              hideLoad();
              return false;
            }
          },
          success:function(data){
            getChatNegoBox();
            $('#reqPesanNego').val('');
            $('#submitPesan').removeClass('fa fa-refresh');
            $('#submitPesan').addClass('fa fa-send');
          }
        });

      });

      setInterval("getChatNegoBox();",3000);
      // setInterval("getStatus();",1500);
    });

    // setInterval(function(){ $('.popup-messages').scrollTop($('.direct-chat-messages').outerHeight());  }, 100);

    function getChatNegoBox() {
      $('.popup-messages2').scrollTop($('.direct-chat-messages').outerHeight());
      var rekananid = $('#reqRekananId').val();
      if (rekananid) {
        $.getJSON("chat_json/chatNegoBox?reqId=<?= $reqId ?>&reqJenis=4&reqRekananId="+rekananid, function(data) {
          $('.direct-chat-messages').html(data);
        });
      }
      // $('.popup-messages').scrollTop($('.direct-chat-messages').outerHeight());
    }

    </script>

<body class="body-popup">

  <div class="card mb-1 border-darken-1">
      <div class="card-content">
        <div class="p-1">
          <div class="row">
            <div class="col-md-12 col-sm-12">
              <!-- <h4>Chat Room</h4> -->
              <div class="card-body" style="padding-right: 2px; text-align: center;">
                <div class="row">
                  <div class="col-md-3 text-left">
                    <h4 class="text-center">Kirim Pesan ke Peyedia </h4>
                    <?php 
                    for ($i=0; $i < $totalRekanan; $i++) { 
                      $this->load->model("Rekanan");
                      $rekanan = new Rekanan();
                      $rekanan->selectByParams(array("A.REKANAN_ID" => $reqRekananId[$i]));
                      $rekanan->firstRow();
                    ?>
                      <div class="users-list-padding media-list" style="border: 0.1em solid #b7b7b7; margin-bottom:1%; border-radius: 20px;">
                        <a onClick="return showChat('<?= $rekanan->getField("NAMA") ?>','<?= $rekanan->getField("REKANAN_ID") ?>')" class="media border-0"> 
                          <div class="media-body w-100" >
                            <h6 class="list-group-item-heading"> <?= $rekanan->getField("NAMA"); ?></h6> 
                          </div>
                        </a>
                      </div>
                    <?php 
                    }
                    ?>
                  </div>

                  <div class="col-md-9">
                    <div id="qnimate">
                      <div class="row">
                        <div class="col-md-12">
                          <div class="popup-head-left pull-left" style="font-size:1.2em">  <span id="titlePenyedia">
                          <i style="color:#fff">x x x x x</i>
                          </span>
                          </div> 
                        </div>
                        <div class="col-md-12">
                            <div class="popup-messages2">
                              <div class="direct-chat-messages" id="chatNegoBox">
                            </div>
                          </div>
                        </div>
                        <div class="col-md-12">
                          <form id="ffnego" class="easyui-form form-horizontal" method="post" novalidate enctype="multipart/form-data">
                            <input type="hidden" name="reqId" value="<?=$reqId?>">
                            <input type="hidden" id="reqRekananId" name="reqRekananId" value="">
                            <input type="hidden" id="reqJenisChat" name="reqJenisChat" value="4">
                            <fieldset>
                              <div class="input-group" style="padding: 5px">
                                <input type="text" id="reqPesanNego" required="" name="reqPesanNego" class="form-control easyui-validatebox" style="border-radius: 5px 0 0 5px;" placeholder="Tulis pesan disini...">
                                <div class="input-group-append">
                                  <button class="btn btn-danger btn-search-x" type="submit"><span class="fa fa-send" id="submitPesan"></span></button>
                                </div>
                              </div>
                            </fieldset>
                          </form> 
                        </div>
                      </div>
                    </div>
                  </div>

                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

           <!-- <script src="<?=base_url()?>assets/new/vendors/js/jquery.min.3.6.0.js"></script> -->

    <!-- <script src="lib/bootstrap/dist/js/bootstrap.min.js"></script> -->
    <link rel="stylesheet" type="text/css" href="<?=base_url()?>lib/eproc/themes/default/easyui.css">
    <script type="text/javascript" src="<?=base_url()?>lib/eproc/jquery.easyui.min.js"></script> 
</body>
</html>
