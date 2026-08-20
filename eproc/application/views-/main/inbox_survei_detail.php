<?php
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 * 
 */

/* INCLUDE FILE */
$this->load->library("kauth");  $userLogin = new kauth(); 
$this->load->model("Inbox");
include_once("functions/string.func.php");
include_once("functions/date.func.php");
include_once("functions/default.func.php");
$id  = $this->input->get("id"); 

$dataInbox    = new Inbox();
$dataInboxReplay    = new Inbox();
$arrStatement = array("A.INBOXID" => $id, "A.PARENT" => 0, "A.INBOXCATEGORYID" => '2');
$dataInbox->selectByParams($arrStatement, -1, -1);
$dataInbox->firstRow();

$inboxid = $dataInbox->getField("INBOXID");
$inbox_subject = $dataInbox->getField("INBOX_SUBJECT");
$ic_name = $dataInbox->getField("IC_NAME");
$inbox_content = $dataInbox->getField("INBOX_CONTENT");
$inbox_from = $dataInbox->getField("INBOX_FROM");
$inbox_to = $dataInbox->getField("INBOX_TO");
$inbox_status = $dataInbox->getField("STATUS");
$inbox_parent = $dataInbox->getField("PARENT");
$inbox_file = $dataInbox->getField("INBOX_FILE");
$inbox_file_nama = $dataInbox->getField("INBOX_FILE_NAMA");
$inbox_file_size = $dataInbox->getField("INBOX_FILE_SIZE");
$inbox_file_type = $dataInbox->getField("INBOX_FILE_TYPE");
$created_by = $dataInbox->getField("CREATED_BY");
$created_by_str = $dataInbox->getField("CREATED_BY_STR");
$created_date = $dataInbox->getField("CREATED_DATE");
$dateEx = explode(' ', $dataInbox->getField("CREATED_DATE"));
$dateEx1 = getFormattedDate($dateEx[0]);
$dateEx2 = $dateEx[1];
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <base href="<?=base_url();?>" />
    <link rel="icon" href="../../favicon.ico">
    <title><?= SYSTEM_NAME.' | '.SYSTEM_NAME_PT ?></title>
    <link rel="stylesheet" type="text/css" href="<?=base_url() ?>assets/new/css/vendors.css">
    <link rel="stylesheet" type="text/css" href="<?=base_url() ?>assets/new/vendors/css/ui/prism.min.css">
    <link rel="stylesheet" type="text/css" href="<?=base_url() ?>assets/new/css/app.css">
    <link rel="stylesheet" type="text/css" href="<?=base_url() ?>assets/new/css/core/menu/menu-types/horizontal-menu.css">
    <link rel="stylesheet" type="text/css" href="<?=base_url() ?>assets/new/css/core/colors/palette-gradient.css">
    <link rel="stylesheet" type="text/css" href="<?=base_url() ?>assets/new/css/core/colors/palette-callout.css">
    <link rel="stylesheet" type="text/css" href="<?=base_url() ?>assets/new/css/style.css">
    <link rel="stylesheet" href="lib/font-awesome-4.7.0/css/font-awesome.css" type="text/css">
    <link rel="stylesheet" type="text/css" href="<?=base_url() ?>assets/new/vendors/css/extensions/zoom.css">
    <script src="<?=base_url()?>assets/new/vendors/js/extensions/zoom.min.js"></script>
    <script type="text/javascript" src="js/jquery-1.6.1.min.js"></script>
    <link rel="stylesheet" type="text/css" href="lib/drupal/index.css" />  
    <style type="text/css">
      .card-header {
        padding: .5rem 1.5rem !important;
      }
    </style>
  </head>
     <div class="card mb-1 border-darken-1">
      <div class="card-content">
        <div class="p-1">
          <div class="alert bg-primary alert-icon-left alert-arrow-left alert-dismissible mb-2" role="alert">
            <span class="alert-icon"><i class="fa fa-th"></i></span>  <strong><?= $inbox_subject ?></strong>
          </div> 
          <div class="p-1" >  
            <div class="row match-height">
              <div class="col-lg-12 col-xl-12"> 
                <div id="accordionWrap5" role="tablist" aria-multiselectable="true">
                  <div class="card collapse-icon accordion-icon-rotate">
                    
                    <div id="heading51"  class="card-header border-success">
                      <a data-toggle="collapse" data-parent="#accordionWrap5" href="#accordion51" aria-expanded="true" aria-controls="accordion51" class="card-title lead success"><?= $inbox_from ?></a><br>
                          <small style="font-size: 11px; top: 0px"> <span class="fa fa-clock-o"></span> <?= $dateEx1.' '.$dateEx2 ?></small>
                    </div>
                    <div id="accordion51" role="tabpanel" aria-labelledby="heading51" class="card-collapse collapse show" aria-expanded="true">
                      <div class="card-content">
                        <div class="card-body">
                          <?= $inbox_content ?>
                          <?php 
                          if ($inbox_file != '-') { ?>
                          <dt><small><br>Lampiran</small><br><span class="fa fa-paperclip"></span> <a href="uploads/inbox/<?= $inbox_file ?>" target="_blank" style="color: #000"><?= $inbox_file_nama ?></a></dt>
                          <?php 
                          } 
                          if ($this->USER_TYPE_ID != '2') { // Bukan Administrator VMS
                          ?>
                          <hr>
                          <a href="main/index/inbox_survei_penyedia_add?id=<?=$inboxid?>" class="<?= CLASS_BTN_PRIMARY ?> icon-action-undo iconsize pull-left" target="_blank"> Reply</a><br><br>
                          <?php 
                          } ?>
                        </div>
                      </div>
                    </div> 
                    <?php 
                    $no=1;
                    if ($this->USER_TYPE_ID == '2') { // Administrator VMS
                      $dataInboxReplay->selectByPenerima(array("A.PARENT" => $inboxid, "A.INBOXCATEGORYID" => '2'), -1, -1); // 1:RFI 2:Survey 3:Complain
                    } else {
                      $dataInboxReplay->selectByPenerima(array("A.PARENT" => $inboxid, "A.INBOXCATEGORYID" => '2', "A.CREATED_BY" => $this->REKANAN_ID), -1, -1); // 1:RFI 2:Survey 3:Complain
                    }
                    if ($dataInboxReplay->countRow() > 0) {  
                      while ($dataInboxReplay->nextRow()) {  
                        $dateEx = explode(' ', $dataInboxReplay->getField("CREATED_DATE"));
                        $dateEx1 = getFormattedDate($dateEx[0]);
                        $dateEx2 = $dateEx[1];
                     ?>
                        <div id="heading<?= $no ?>"  class="card-header mt-1  border-success">
                          <a data-toggle="collapse" data-parent="#accordionWrap5" href="#accordion<?= $no ?>" aria-expanded="true" aria-controls="accordion<?= $no ?>" class="card-title lead success"><?= $dataInboxReplay->getField("INBOX_FROM") ?></a><br>
                            <small style="font-size: 11px; top: 0px"> <span class="fa fa-clock-o"></span> <?= $dateEx1.' '.$dateEx2 ?></small>
                        </div>
                        <div id="accordion<?= $no ?>" role="tabpanel" aria-labelledby="heading<?= $no ?>" class="card-collapse collapse">
                          <div class="card-content">
                            <div class="card-body">
                              <?= $dataInboxReplay->getField("INBOX_CONTENT") ?>
                              <?php 
                              if ($dataInboxReplay->getField("INBOX_FILE") != '-') { ?>
                              <dt><small><br>Lampiran</small><br><span class="fa fa-paperclip"></span> <a href="uploads/inbox/<?= $dataInboxReplay->getField("INBOX_FILE") ?>" target="_blank" style="color: #000"><?= $dataInboxReplay->getField("INBOX_FILE_NAMA") ?></a></dt>
                              <?php 
                              } ?>
                              <?php 
                              if ($dataInboxReplay->getField("CREATED_BY") != $this->REKANAN_ID && $this->USER_TYPE_ID != '2') { 
                               ?>
                              <hr>
                              <a href="main/index/inbox_survei_penyedia_add?id=<?=$inboxid?>" class="<?= CLASS_BTN_PRIMARY ?> icon-action-undo iconsize pull-left" target="_blank"> Reply</a><br><br>
                              <?php 
                              } ?>
                            </div>
                          </div>
                        </div> 
                    <?php 
                      $no++;
                      }
                    } ?>
                  </div>
                </div>
              </div> 
            </div>
          </div>
        </div>
      </div>
    </div>
    
    <script src="lib/bootstrap/libs/jquery/1.12.4/jquery.min.js"></script> 

    <script src="<?=base_url()?>assets/new/vendors/js/vendors.min.js"></script>
    <script src="<?=base_url()?>assets/new/vendors/js/ui/jquery.sticky.js"></script>
    <script src="<?=base_url()?>assets/new/vendors/js/ui/prism.min.js"></script>
    <script src="<?=base_url()?>assets/new/js/core/app-menu.js"></script>
    <script src="<?=base_url()?>assets/new/js/core/app.js"></script>
    <script src="<?=base_url()?>assets/new/js/scripts/ui/breadcrumbs-with-stats.js"></script>
    <script src="<?=base_url()?>assets/new/js/scripts/tooltip/tooltip.js"></script>
  </body>
</html>
