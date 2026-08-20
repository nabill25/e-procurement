<?php
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 * 
 */
include_once("functions/string.func.php");
include_once("functions/date.func.php");
include_once("functions/default.func.php");
$reqId  = $this->input->get("reqId");
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
    
  	<script type="text/javascript" src="js/jquery-1.6.1.min.js"></script>
    <link rel="stylesheet" type="text/css" href="lib/drupal/index.css" /> 
    <script src="lib/emodal/eModal.js"></script>
    <style type="text/css">
      .table th {
        padding: 10px !important;
        background-color: #b7b7b7;
        color: #000;
      }
      .table-responsive {
        padding: 10px;
        border: 1px solid rgba(0, 0, 0, .1);
        border-radius: 10px;
        -webkit-box-shadow: 0px 17px 11px -4px rgba(0,0,0,0.16);
        -moz-box-shadow: 0px 17px 11px -4px rgba(0,0,0,0.16);
        box-shadow: 0px 17px 11px -4px rgba(0,0,0,0.16);
      }
    </style>
  </head>

<body class="body-popup">

    <div class="card mb-1 border-darken-1">
      <div class="card-content">
        <div class="p-1">
          <div class="alert bg-primary alert-icon-left alert-arrow-left alert-dismissible mb-2" role="alert">
            <span class="alert-icon"><i class="fa fa-th"></i></span>  <strong>Rekam Jejak Reschedule Jadwal</strong>
          </div> 
          
          <?php 
          
          $this->load->model("Paket");
          $alasan_reschedule = new Paket();

          $paket_reschedule_rekamjejak1 = new Paket();
          $paket_reschedule_rekamjejak1->selectByParamsReschedule(array(), '-1', '-1', ' AND PAKET_ID = '.$reqId.' AND reschedule_1 is not null');
          if($paket_reschedule_rekamjejak1->countRow() > 0)
          {
            $alasan_reschedule->selectById($reqId);
            $alasan_reschedule->firstRow();
            $no=1; ?>
            <div class="table-responsive mb-2">
              <h4>Reschedule 1</h4>
              <div class="alert alert-danger"> Alasan: <?= $alasan_reschedule->getField("RESCHEDULE_1") ?> </div>
              <table class="table mb-0 table-bordered">
                <tbody>
                  <tr valign="top" class="judul-kolom">
                    <th valign="middle" style="width:1%; text-align: center">No</th>
                    <th valign="middle" style="width:48%; text-align: center">Tahapan</th>
                    <th valign="top" style="width: 50%; text-align: center"> Waktu Reschedule </th>
                  </tr>  
                  <?php 
                  while($paket_reschedule_rekamjejak1->nextRow())
                  {  ?>
                    <tr>
                      <td width="5px"><?= $no ?></td>
                      <td><?= $paket_reschedule_rekamjejak1->getField('NAMA');  ?></td>
                      <td class="text-center">
                        <?php $tgl_awal = explode(' ', $paket_reschedule_rekamjejak1->getField('reschedule_1_awal'));
                              echo getFormattedDate($tgl_awal[0]).' '.addWIB($tgl_awal[1]) ?> 
                        s.d
                        <?php $tgl_akhir = explode(' ', $paket_reschedule_rekamjejak1->getField('reschedule_1_akhir'));
                              echo getFormattedDate($tgl_akhir[0]).' '.addWIB($tgl_akhir[1]) ?> 
                      </td> 
                    </tr>

                  <?php 
                  $no++;
                  } 
                  ?>
                </tbody>
              </table>
            </div> 
          <?php 
          } ?>

          <?php 
          $this->load->model("Paket");
          $paket_reschedule_rekamjejak2 = new Paket();
          $paket_reschedule_rekamjejak2->selectByParamsReschedule(array(), '-1', '-1', ' AND PAKET_ID = '.$reqId.' AND reschedule_2 is not null ');
          if ($paket_reschedule_rekamjejak2->countRow() > 0) 
          {
            $alasan_reschedule->selectById($reqId);
            $alasan_reschedule->firstRow();
            $no=1; ?>
            <div class="table-responsive mb-2">
              <h4>Reschedule 2</h4>
              <div class="alert alert-danger"> Alasan: <?= $alasan_reschedule->getField("RESCHEDULE_2") ?> </div>
              <table class="table mb-0 table-bordered">
                <tbody>
                  <tr valign="top" class="judul-kolom">
                    <th valign="middle" style="width:1%; text-align: center">No</th>
                    <th valign="middle" style="width:48%; text-align: center">Tahapan</th>
                    <th valign="top" style="width: 50%; text-align: center"> Waktu Reschedule </th>
                  </tr>  
                  <?php 
                  while($paket_reschedule_rekamjejak2->nextRow())
                  {  ?>
                    <tr>
                      <td width="5px"><?= $no ?></td>
                      <td><?= $paket_reschedule_rekamjejak2->getField('NAMA');  ?></td>
                      <td class="text-center">
                        <?php $tgl_awal = explode(' ', $paket_reschedule_rekamjejak2->getField('reschedule_2_awal'));
                              echo getFormattedDate($tgl_awal[0]).' '.addWIB($tgl_awal[1]) ?> 
                        s.d
                        <?php $tgl_akhir = explode(' ', $paket_reschedule_rekamjejak2->getField('reschedule_2_akhir'));
                              echo getFormattedDate($tgl_akhir[0]).' '.addWIB($tgl_akhir[1]) ?> 
                      </td> 
                    </tr>

                  <?php 
                  $no++;
                  } 
                  ?>
                </tbody>
              </table>
            </div>
          <?php 
          } ?>

          <?php 
          $this->load->model("Paket");
          $paket_reschedule_rekamjejak3 = new Paket();
          $paket_reschedule_rekamjejak3->selectByParamsReschedule(array(), '-1', '-1', ' AND PAKET_ID = '.$reqId.' AND reschedule_3 is not null ');
          if ($paket_reschedule_rekamjejak3->countRow() > 0) 
          {
            $alasan_reschedule->selectById($reqId);
            $alasan_reschedule->firstRow();
            $no=1; ?>
            <div class="table-responsive mb-2">
              <h4>Reschedule 3</h4>
              <div class="alert alert-danger"> Alasan: <?= $alasan_reschedule->getField("RESCHEDULE_3") ?> </div>
              <table class="table mb-0 table-bordered">
                <tbody>
                  <tr valign="top" class="judul-kolom">
                    <th valign="middle" style="width:1%; text-align: center">No</th>
                    <th valign="middle" style="width:48%; text-align: center">Tahapan</th>
                    <th valign="top" style="width: 50%; text-align: center"> Waktu Reschedule </th>
                  </tr>  
                  <?php 
                  while($paket_reschedule_rekamjejak3->nextRow())
                  {  ?>
                    <tr>
                      <td width="5px"><?= $no ?></td>
                      <td><?= $paket_reschedule_rekamjejak3->getField('NAMA');  ?></td>
                      <td class="text-center">
                        <?php $tgl_awal = explode(' ', $paket_reschedule_rekamjejak3->getField('reschedule_3_awal'));
                              echo getFormattedDate($tgl_awal[0]).' '.addWIB($tgl_awal[1]) ?> 
                        s.d
                        <?php $tgl_akhir = explode(' ', $paket_reschedule_rekamjejak3->getField('reschedule_3_akhir'));
                              echo getFormattedDate($tgl_akhir[0]).' '.addWIB($tgl_akhir[1]) ?> 
                      </td> 
                    </tr>

                  <?php 
                  $no++;
                  } 
                  ?>
                </tbody>
              </table>
            </div>
          <?php 
          } ?>

          <?php 
          $this->load->model("Paket");
          $paket_reschedule_rekamjejak4 = new Paket();
          $paket_reschedule_rekamjejak4->selectByParamsReschedule(array(), '-1', '-1', ' AND PAKET_ID = '.$reqId.' AND reschedule_4 is not null ');
          if ($paket_reschedule_rekamjejak4->countRow() > 0) 
          {
            $alasan_reschedule->selectById($reqId);
            $alasan_reschedule->firstRow();
            $no=1; ?>
            <div class="table-responsive mb-2">
              <h4>Reschedule 4</h4>
              <div class="alert alert-danger"> Alasan: <?= $alasan_reschedule->getField("RESCHEDULE_4") ?> </div>
              <table class="table mb-0 table-bordered">
                <tbody>
                  <tr valign="top" class="judul-kolom">
                    <th valign="middle" style="width:1%; text-align: center">No</th>
                    <th valign="middle" style="width:48%; text-align: center">Tahapan</th>
                    <th valign="top" style="width: 50%; text-align: center"> Waktu Reschedule </th>
                  </tr>  
                  <?php 
                  while($paket_reschedule_rekamjejak4->nextRow())
                  {  ?>
                    <tr>
                      <td width="5px"><?= $no ?></td>
                      <td><?= $paket_reschedule_rekamjejak4->getField('NAMA');  ?></td>
                      <td class="text-center">
                        <?php $tgl_awal = explode(' ', $paket_reschedule_rekamjejak4->getField('reschedule_4_awal'));
                              echo getFormattedDate($tgl_awal[0]).' '.addWIB($tgl_awal[1]) ?> 
                        s.d
                        <?php $tgl_akhir = explode(' ', $paket_reschedule_rekamjejak4->getField('reschedule_4_akhir'));
                              echo getFormattedDate($tgl_akhir[0]).' '.addWIB($tgl_akhir[1]) ?> 
                      </td> 
                    </tr>

                  <?php 
                  $no++;
                  } 
                  ?>
                </tbody>
              </table>
            </div>
          <?php 
          } ?>

          <?php 
          $this->load->model("Paket");
          $paket_reschedule_rekamjejak5 = new Paket();
          $paket_reschedule_rekamjejak5->selectByParamsReschedule(array(), '-1', '-1', ' AND PAKET_ID = '.$reqId.' AND reschedule_5 is not null ');
          if ($paket_reschedule_rekamjejak5->countRow() > 0) 
          {
            $alasan_reschedule->selectById($reqId);
            $alasan_reschedule->firstRow();
            $no=1; ?>
            <div class="table-responsive mb-2">
              <h4>Reschedule 5</h4>
              <div class="alert alert-danger"> Alasan: <?= $alasan_reschedule->getField("RESCHEDULE_5") ?> </div>
              <table class="table mb-0 table-bordered">
                <tbody>
                  <tr valign="top" class="judul-kolom">
                    <th valign="middle" style="width:1%; text-align: center">No</th>
                    <th valign="middle" style="width:48%; text-align: center">Tahapan</th>
                    <th valign="top" style="width: 50%; text-align: center"> Waktu Reschedule </th>
                  </tr>  
                  <?php 
                  while($paket_reschedule_rekamjejak5->nextRow())
                  {  ?>
                    <tr>
                      <td width="5px"><?= $no ?></td>
                      <td><?= $paket_reschedule_rekamjejak5->getField('NAMA');  ?></td>
                      <td class="text-center">
                        <?php $tgl_awal = explode(' ', $paket_reschedule_rekamjejak5->getField('reschedule_5_awal'));
                              echo getFormattedDate($tgl_awal[0]).' '.addWIB($tgl_awal[1]) ?> 
                        s.d
                        <?php $tgl_akhir = explode(' ', $paket_reschedule_rekamjejak5->getField('reschedule_5_akhir'));
                              echo getFormattedDate($tgl_akhir[0]).' '.addWIB($tgl_akhir[1]) ?> 
                      </td> 
                    </tr>

                  <?php 
                  $no++;
                  } 
                  ?>
                </tbody>
              </table>
            </div>
          <?php 
          } ?>

          <?php 
          $this->load->model("Paket");
          $paket_reschedule_rekamjejak6 = new Paket();
          $paket_reschedule_rekamjejak6->selectByParamsReschedule(array(), '-1', '-1', ' AND PAKET_ID = '.$reqId.' AND reschedule_6 is not null ');
          if ($paket_reschedule_rekamjejak6->countRow() > 0) 
          {
            $alasan_reschedule->selectById($reqId);
            $alasan_reschedule->firstRow();
            $no=1; ?>
            <div class="table-responsive mb-2">
              <h4>Reschedule 6</h4>
              <div class="alert alert-danger"> Alasan: <?= $alasan_reschedule->getField("RESCHEDULE_6") ?> </div>
              <table class="table mb-0 table-bordered">
                <tbody>
                  <tr valign="top" class="judul-kolom">
                    <th valign="middle" style="width:1%; text-align: center">No</th>
                    <th valign="middle" style="width:48%; text-align: center">Tahapan</th>
                    <th valign="top" style="width: 50%; text-align: center"> Waktu Reschedule </th>
                  </tr>  
                  <?php 
                  while($paket_reschedule_rekamjejak6->nextRow())
                  {  ?>
                    <tr>
                      <td width="5px"><?= $no ?></td>
                      <td><?= $paket_reschedule_rekamjejak6->getField('NAMA');  ?></td>
                      <td class="text-center">
                        <?php $tgl_awal = explode(' ', $paket_reschedule_rekamjejak6->getField('reschedule_6_awal'));
                              echo getFormattedDate($tgl_awal[0]).' '.addWIB($tgl_awal[1]) ?> 
                        s.d
                        <?php $tgl_akhir = explode(' ', $paket_reschedule_rekamjejak6->getField('reschedule_6_akhir'));
                              echo getFormattedDate($tgl_akhir[0]).' '.addWIB($tgl_akhir[1]) ?> 
                      </td> 
                    </tr>

                  <?php 
                  $no++;
                  } 
                  ?>
                </tbody>
              </table>
            </div>
          <?php 
          } ?>

          <?php 
          $this->load->model("Paket");
          $paket_reschedule_rekamjejak7 = new Paket();
          $paket_reschedule_rekamjejak7->selectByParamsReschedule(array(), '-1', '-1', ' AND PAKET_ID = '.$reqId.' AND reschedule_7 is not null ');
          if ($paket_reschedule_rekamjejak7->countRow() > 0) 
          {
            $alasan_reschedule->selectById($reqId);
            $alasan_reschedule->firstRow();
            $no=1; ?>
            <div class="table-responsive mb-2">
              <h4>Reschedule 7</h4>
              <div class="alert alert-danger"> Alasan: <?= $alasan_reschedule->getField("RESCHEDULE_7") ?> </div>
              <table class="table mb-0 table-bordered">
                <tbody>
                  <tr valign="top" class="judul-kolom">
                    <th valign="middle" style="width:1%; text-align: center">No</th>
                    <th valign="middle" style="width:48%; text-align: center">Tahapan</th>
                    <th valign="top" style="width: 50%; text-align: center"> Waktu Reschedule </th>
                  </tr>  
                  <?php 
                  while($paket_reschedule_rekamjejak7->nextRow())
                  {  ?>
                    <tr>
                      <td width="5px"><?= $no ?></td>
                      <td><?= $paket_reschedule_rekamjejak7->getField('NAMA');  ?></td>
                      <td class="text-center">
                        <?php $tgl_awal = explode(' ', $paket_reschedule_rekamjejak7->getField('reschedule_7_awal'));
                              echo getFormattedDate($tgl_awal[0]).' '.addWIB($tgl_awal[1]) ?> 
                        s.d
                        <?php $tgl_akhir = explode(' ', $paket_reschedule_rekamjejak7->getField('reschedule_7_akhir'));
                              echo getFormattedDate($tgl_akhir[0]).' '.addWIB($tgl_akhir[1]) ?> 
                      </td> 
                    </tr>

                  <?php 
                  $no++;
                  } 
                  ?>
                </tbody>
              </table>
            </div>
          <?php 
          } ?>

          <?php 
          $this->load->model("Paket");
          $paket_reschedule_rekamjejak8 = new Paket();
          $paket_reschedule_rekamjejak8->selectByParamsReschedule(array(), '-1', '-1', ' AND PAKET_ID = '.$reqId.' AND reschedule_8 is not null ');
          if ($paket_reschedule_rekamjejak8->countRow() > 0) 
          {
            $alasan_reschedule->selectById($reqId);
            $alasan_reschedule->firstRow();
            $no=1; ?>
            <div class="table-responsive mb-2">
              <h4>Reschedule 8</h4>
              <div class="alert alert-danger"> Alasan: <?= $alasan_reschedule->getField("RESCHEDULE_8") ?> </div>
              <table class="table mb-0 table-bordered">
                <tbody>
                  <tr valign="top" class="judul-kolom">
                    <th valign="middle" style="width:1%; text-align: center">No</th>
                    <th valign="middle" style="width:48%; text-align: center">Tahapan</th>
                    <th valign="top" style="width: 50%; text-align: center"> Waktu Reschedule </th>
                  </tr>  
                  <?php 
                  while($paket_reschedule_rekamjejak8->nextRow())
                  {  ?>
                    <tr>
                      <td width="5px"><?= $no ?></td>
                      <td><?= $paket_reschedule_rekamjejak8->getField('NAMA');  ?></td>
                      <td class="text-center">
                        <?php $tgl_awal = explode(' ', $paket_reschedule_rekamjejak8->getField('reschedule_8_awal'));
                              echo getFormattedDate($tgl_awal[0]).' '.addWIB($tgl_awal[1]) ?> 
                        s.d
                        <?php $tgl_akhir = explode(' ', $paket_reschedule_rekamjejak8->getField('reschedule_8_akhir'));
                              echo getFormattedDate($tgl_akhir[0]).' '.addWIB($tgl_akhir[1]) ?> 
                      </td> 
                    </tr>

                  <?php 
                  $no++;
                  } 
                  ?>
                </tbody>
              </table>
            </div>
          <?php 
          } ?>

          <?php 
          $this->load->model("Paket");
          $paket_reschedule_rekamjejak9 = new Paket();
          $paket_reschedule_rekamjejak9->selectByParamsReschedule(array(), '-1', '-1', ' AND PAKET_ID = '.$reqId.' AND reschedule_9 is not null ');
          if ($paket_reschedule_rekamjejak9->countRow() > 0) 
          {
            $alasan_reschedule->selectById($reqId);
            $alasan_reschedule->firstRow();
            $no=1; ?>
            <div class="table-responsive mb-2">
              <h4>Reschedule 9</h4>
              <div class="alert alert-danger"> Alasan: <?= $alasan_reschedule->getField("RESCHEDULE_9") ?> </div>
              <table class="table mb-0 table-bordered">
                <tbody>
                  <tr valign="top" class="judul-kolom">
                    <th valign="middle" style="width:1%; text-align: center">No</th>
                    <th valign="middle" style="width:48%; text-align: center">Tahapan</th>
                    <th valign="top" style="width: 50%; text-align: center"> Waktu Reschedule </th>
                  </tr>  
                  <?php 
                  while($paket_reschedule_rekamjejak9->nextRow())
                  {  ?>
                    <tr>
                      <td width="5px"><?= $no ?></td>
                      <td><?= $paket_reschedule_rekamjejak9->getField('NAMA');  ?></td>
                      <td class="text-center">
                        <?php $tgl_awal = explode(' ', $paket_reschedule_rekamjejak9->getField('reschedule_9_awal'));
                              echo getFormattedDate($tgl_awal[0]).' '.addWIB($tgl_awal[1]) ?> 
                        s.d
                        <?php $tgl_akhir = explode(' ', $paket_reschedule_rekamjejak9->getField('reschedule_9_akhir'));
                              echo getFormattedDate($tgl_akhir[0]).' '.addWIB($tgl_akhir[1]) ?> 
                      </td> 
                    </tr>

                  <?php 
                  $no++;
                  } 
                  ?>
                </tbody>
              </table>
            </div>
          <?php 
          } ?>

          <?php 
          $this->load->model("Paket");
          $paket_reschedule_rekamjejak10 = new Paket();
          $paket_reschedule_rekamjejak10->selectByParamsReschedule(array(), '-1', '-1', ' AND PAKET_ID = '.$reqId.' AND reschedule_10 is not null ');
          if ($paket_reschedule_rekamjejak10->countRow() > 0) 
          {
            $alasan_reschedule->selectById($reqId);
            $alasan_reschedule->firstRow();
            $no=1; ?>
            <div class="table-responsive mb-2">
              <h4>Reschedule 10</h4>
              <div class="alert alert-danger"> Alasan: <?= $alasan_reschedule->getField("RESCHEDULE_10") ?> </div>
              <table class="table mb-0 table-bordered">
                <tbody>
                  <tr valign="top" class="judul-kolom">
                    <th valign="middle" style="width:1%; text-align: center">No</th>
                    <th valign="middle" style="width:48%; text-align: center">Tahapan</th>
                    <th valign="top" style="width: 50%; text-align: center"> Waktu Reschedule </th>
                  </tr>  
                  <?php 
                  while($paket_reschedule_rekamjejak10->nextRow())
                  {  ?>
                    <tr>
                      <td width="5px"><?= $no ?></td>
                      <td><?= $paket_reschedule_rekamjejak10->getField('NAMA');  ?></td>
                      <td class="text-center">
                        <?php $tgl_awal = explode(' ', $paket_reschedule_rekamjejak10->getField('reschedule_10_awal'));
                              echo getFormattedDate($tgl_awal[0]).' '.addWIB($tgl_awal[1]) ?> 
                        s.d
                        <?php $tgl_akhir = explode(' ', $paket_reschedule_rekamjejak10->getField('reschedule_10_akhir'));
                              echo getFormattedDate($tgl_akhir[0]).' '.addWIB($tgl_akhir[1]) ?> 
                      </td> 
                    </tr>

                  <?php 
                  $no++;
                  } 
                  ?>
                </tbody>
              </table>
            </div>
          <?php 
          } ?>

        </div>
      </div>
    </div> 
    
    <script src="lib/bootstrap/libs/jquery/1.12.4/jquery.min.js"></script>
    <script>window.jQuery || document.write('<script src="lib/bootstrap/assets/js/vendor/jquery.min.js"><\/script>')</script>
    <script src="lib/bootstrap/dist/js/bootstrap.min.js"></script>
    <link rel="stylesheet" type="text/css" href="<?=base_url()?>lib/eproc/themes/default/easyui.css">
    <script type="text/javascript" src="<?=base_url()?>lib/eproc/jquery.easyui.min.js"></script>
    <script type="text/javascript" src="<?=base_url()?>lib/eproc/kalender-easyui.js"></script>
    <script type="text/javascript" src="<?=base_url()?>lib/eproc/allfunc.js"></script>
  </body>
</html>
