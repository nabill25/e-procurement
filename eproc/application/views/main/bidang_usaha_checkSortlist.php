<?php 
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 * 
 */

$this->load->model("PaketBidangUsaha");
$idPecah = explode('|-|', $this->input->get("id"));
$bidang_usaha_id = $idPecah[0];
$reqKualifikasiRekanan = $idPecah[1];
$reqNilai= $idPecah[2];
$paket_bidang_usaha_check = new PaketBidangUsaha();
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <base href="<?=base_url();?>" />
    
    <link rel="icon" href="../../favicon.ico">

    <title><?= SYSTEM_NAME ?></title>

    <!-- Bootstrap core CSS -->
    <link href="lib/bootstrap/dist/css/bootstrap.css" rel="stylesheet">

    <link rel="stylesheet" href="css/core.css" type="text/css">
    <link href='http://fonts.googleapis.com/css?family=Roboto:400,300,700' rel='stylesheet' type='text/css'> 
    <!-- BEGIN VENDOR CSS-->
    <link rel="stylesheet" type="text/css" href="<?=base_url() ?>assets/new/css/vendors.css">
    <link rel="stylesheet" type="text/css" href="<?=base_url() ?>assets/new/vendors/css/ui/prism.min.css">
    <!-- END VENDOR CSS-->
    <!-- BEGIN ROBUST CSS-->
    <link rel="stylesheet" type="text/css" href="<?=base_url() ?>assets/new/css/app.css">
    <!-- <link rel="stylesheet" type="text/css" href="<?=base_url() ?>css/core.css"> -->
    <!-- END ROBUST CSS-->
    <!-- BEGIN Page Level CSS-->
    <link rel="stylesheet" type="text/css" href="<?=base_url() ?>assets/new/css/core/menu/menu-types/horizontal-menu.css">
    <link rel="stylesheet" type="text/css" href="<?=base_url() ?>assets/new/css/core/colors/palette-gradient.css">
    <link rel="stylesheet" type="text/css" href="<?=base_url() ?>assets/new/css/core/colors/palette-callout.css">
    <!-- END Page Level CSS-->
    <!-- BEGIN Custom CSS-->
    <link rel="stylesheet" type="text/css" href="<?=base_url() ?>assets/new/css/style.css">

    
    <!-- FONT AWESOME -->
    <link rel="stylesheet" href="lib/font-awesome-4.7.0/css/font-awesome.css" type="text/css">
    <script type="text/javascript" src="js/jquery-1.6.1.min.js"></script> 
  </head>

<body style="background: #fff">
	
    <div class="container-fluid container-treegrid">
    	
        <div class="row row-treegrid">
        	<div class="col-md-12 col-treegrid">
            	<div class="area-konten-atas">
                	<div class="judul-halaman">
                    		Bidang Usaha <?= $bidang_usaha_id ?>
                    	<div class="info" style="color: #fff !important">
							<i class="fa fa-warning" aria-hidden="true"></i> Kualifikasi Usaha 
                            <?php 
                            switch ($reqKualifikasiRekanan) {
                                 case '1':
                                     echo "Kecil";
                                     break;
                                 case '2':
                                     echo "Non-Kecil";
                                     break;
                                 
                                 default:
                                     echo "Kecil/Non-Kecil";
                                     break;
                             } ?> 
                        </div>
                    </div>
                </div>

                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th width="5%" style="text-align: center">No</th>
                            <!-- <th style="text-align: center">Bidang Usaha</th> -->
                            <th style="text-align: center">Ijin Usaha</th>
                            <th>Nama Penyedia</th>
                            <th>Nilai Kontrak Pengalaman Sejenis Tertinggi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        if ($reqKualifikasiRekanan == '3') { // 1:Kecil 2:Non-kecil 3:kecil/non-kecil
                          $paket_bidang_usaha_check->selectByParamsBidangUsahaSortList(array("a.BIDANG_USAHA_ID" => $bidang_usaha_id, "a.NILAI|| >= " => $reqNilai));
                        } else {
                          $paket_bidang_usaha_check->selectByParamsBidangUsahaSortList(array("a.BIDANG_USAHA_ID" => $bidang_usaha_id, 'a.REKANAN_KUALIFIKASI_ID' => $reqKualifikasiRekanan, "a.NILAI|| >= " => $reqNilai));
                        }
                        $no=1;
                        while($paket_bidang_usaha_check->nextRow())
                        {
                         ?>
                         <tr>
                            <th style="text-align: center"><?=$no?></th>
                            <!-- <th style="text-align: center; width: 15%"><?=$paket_bidang_usaha_check->getField("BIDANG_USAHA_ID")?></th> -->
                            <th style="text-align: center; width: 15%"><?=$paket_bidang_usaha_check->getField("IJIN_USAHA")?></th>
                            <th><?=$paket_bidang_usaha_check->getField("REKANAN_TIPE_CHAR").' '.$paket_bidang_usaha_check->getField("NAMA")?></th>
                            <th><?=numberToIna($paket_bidang_usaha_check->getField("NILAI")) ?></th>
                         </tr>
                        <?php 
                        $no++;
                        } ?>
                    </tbody>
                </table>
                
            </div>
        </div>        
    </div> 

</body>
</html>
