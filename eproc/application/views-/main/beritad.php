<?php
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 * 
 */

$this->libsession->cekSession('free');   

include_once("functions/string.func.php");
include_once("functions/date.func.php");
include_once("functions/default.func.php");

$this->load->library("Pagination");
$this->load->model("Berita");
$id	= $this->input->get("id");
$berita	= new Berita();
$berita->selectByParams(array("BERITA_ID" => $id), 4, 0);
$berita->firstRow();
?>

<section id="backColor">
  <div class="row"> 
    <div class="col-md-12 col-sm-12">
      <div class="card border-bottom-primary" style="zoom: 1;"> 
        <div class="card-header">
          <h4 class="card-title"></h4>
          <div class="heading-elements">
            <ul class="list-inline mb-0">
              <li><a data-action="expand"><i class="ft-maximize"></i></a></li>
              <li><a data-action="close"><i class="ft-x"></i></a></li>
            </ul>
          </div>
        </div>
        <div class="card-body"> 
            <blockquote>
              <p><h4><b><?=$berita->getField("NAMA")?></b> <br><small><?=getFormattedDate($berita->getField("TANGGAL"))?></small>
              </h4></p>
                <?=$berita->getField("KETERANGAN")?> <br>
             <?php 
                if (file_exists('uploads/berita/'.$berita->getField("LAMPIRAN")) && $berita->getField("LAMPIRAN") != '') { ?>
                <a href="uploads/berita/<?=$berita->getField("LAMPIRAN")?>" target="_blank" style="margin-bottom: 5%">
                    <i class="fa fa-download" aria-hidden="true"> Download&nbsp;Berkas</i>
                </a>
            <?php }?>
            </blockquote> 
            <div class="form-actions">
                <a href="<?= base_url('/main/index/berita') ?>" class="<?= CLASS_BTN_DANGER ?>"> <?= BTN_KEMBALI ?> </a> 
            </div>
        </div>
      </div>
    </div> 

  </div>  
</section> 