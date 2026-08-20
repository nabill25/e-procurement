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

$this->load->library("Pagination");
$this->load->model("Berita");

$showRecord = 5;
$pageView = "berita_json/berita/";

$arrStatement = array();	
$berita	= new Berita();
$rowCount = $berita->getCountByParams($arrStatement);
$berita->selectByParams($arrStatement, $showRecord, 0);

$arrSerialized = serialize($arrStatement);	
$arrSerialized = str_replace('"', '@', $arrSerialized);		
$pagConfig = array('baseURL'=>$pageView, 'showRecord' => $showRecord, 'totalRows'=>$rowCount, 'perPage'=>$showRecord, 'contentDiv'=>'tbodyData', 'arrSerialized' => $arrSerialized, 'searchVarible' => "reqPencarian");
$pagination =  new Pagination($pagConfig);	

?>

<section id="backColor">
  <div class="row"> 
    <div class="col-md-12 col-sm-12">
      <div class="card" style="zoom: 1;">
        <div class="card-header card-head-inverse bg-primary">
          <h4 class="card-title text-white">Berita dan Pengumuman<small></small></h4>
          <a class="heading-elements-toggle"><i class="fa fa-ellipsis-v font-medium-3"></i></a> 
        </div>
        <div class="card-content collapse show">
          <div class="card-body border-primary" id="tbodyData">
            <?php
              while($berita->nextRow())
              {
                $beritaId = $berita->getField("BERITA_ID");
            ?>  
            
              <blockquote class="blockquote pl-1 border-left-primary border-left-3">
                <p>
                  <h4>
                    <a href="main/index/beritad?id=<?=$beritaId?>"><?=$berita->getField("NAMA")?></a> <small></small>
                  </h4>
                </p>
                  <?php 
                  echo substr($berita->getField("KETERANGAN"), 0, 250);
                  ?>...
                  <footer class="blockquote-footer"> 
                    <cite title="Source Title"><?=getFormattedDate($berita->getField("TANGGAL"))?></cite>
                  </footer>
              </blockquote> 
            <?php
            }
            ?>  
            <?=$pagination->createLinks()?>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>