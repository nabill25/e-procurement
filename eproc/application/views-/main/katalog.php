<?php
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 *
 */

 if ($this->USER_TYPE_ID == '') {
   redirect(base_url().'main');
 }
// 180724 Pengecualian untuk user dibawah ini
if($this->USER_TYPE_ID != "1" && $this->USER_TYPE_ID != "4" && $this->USER_TYPE_ID != "6" && $this->USER_TYPE_ID != "10" && $this->USER_TYPE_ID != "12" && $this->USER_TYPE_ID != "20")
{ } else { redirect(base_url().'main/index/403'); }
?>
<link rel="stylesheet" type="text/css" href="<?=base_url() ?>assets/new/css/plugins/forms/checkboxes-radios.css">

<style type="text/css">
 .media-body {margin-left: 10px }.list-group-item {padding: .5em !important }h1 {font-size: 13px }h2 {font-size: 13px }.match-height {padding: 0 15px }#btnfull {width: 100%;cursor: default;}.cursorPoin {cursor: pointer;}.pagingPadd {background-color: #fff;padding: 10px 0 6px 10px }.backWhite {background-color: #fff;}.card-title {display: -webkit-box;height: 38px;max-height: 38px;font-size: 14px;font-weight: 600;line-height: 19px;overflow-x: hidden;overflow-y: hidden;white-space: normal;word-wrap: break-word;}.card-name {color: rgba(0, 0, 0, 0.701961);display: -webkit-box;height: 38px;max-height: 38px;font-size: 14px;font-weight: 600;line-height: 19px;overflow-x: hidden;overflow-y: hidden;white-space: normal;word-wrap: break-word;}.shake {-webkit-animation: cartShake .3s;-webkit-animation-delay: 200ms;}@-webkit-keyframes cartShake {0% {transform: rotate(0deg);}5% {transform: rotate(-70deg);}10% {transform: rotate(-60deg);}15% {transform: rotate(-65deg);}20% {transform: rotate(-50deg);}30% {transform: rotate(-40deg);}40% {transform: rotate(-30deg);}50% {transform: rotate(-20deg);}60% {transform: rotate(20deg);}65% {transform: rotate(25deg);}70% {transform: rotate(30deg);}75% {transform: rotate(35deg);}80% {transform: rotate(40deg);}85% {transform: rotate(45deg);}90% {transform: rotate(50deg);}95% {transform: rotate(55deg);}97% {transform: rotate(57deg);}100% {transform: rotate(0deg);}}.tree, .tree ul {margin:0;padding:0;list-style:none }.tree ul {margin-left:.1em;position:relative }.tree ul ul {margin-left:1.5em }.tree ul:before {content:"";display:block;width:0;position:absolute;top:0;bottom:0;left:0;}.tree li {margin:0;padding:.2rem 1em;line-height:2em;color:#000;font-weight:normal;position:relative }ul.tree li:hover {background-color: rgb(243, 244, 245);border-radius: 10px;color: #369;cursor: pointer;}.tree ul li:before {content:"";display:block;width:10px;height:0;margin-top:-1px;position:absolute;top:1em;left:0 }.tree ul li:last-child:before {color: #369;height:auto;top:1em;left: .8em;bottom:0 }.tree ul li {left: .5em;}.tree ul li:hover, .tree ul li a:hover {left: .7em;color: #967adc;}.indicator {margin-right:5px;}.tree li a {text-decoration: none;color:#000;}.tree li button, .tree li button:active, .tree li button:focus {text-decoration: none;color:#369;border:none;background:transparent;margin:0px 0px 0px 0px;padding:0px 0px 0px 0px;outline: 0;}
  /*https://codepen.io/nguyenanhtuan/pen/qBQwrKj*/ a.list-hover{position:relative;color:red;text-decoration:none;&:hover{&::after{transform:scaleX(1);transform-origin:left}}&::after{position:absolute;content:"";top:100%;left:0;width:100%;height:1.5px;background-color:red;transform:scaleX(0);transform-origin:right;transition:transform 350ms;}} .card { border-radius: 15px 15px 0 0 }
</style>
<?php
include_once("functions/string.func.php");
include_once("functions/date.func.php");
include_once("functions/default.func.php");

$this->load->library("Pagination");


$name = httpFilterRequest("name") ? httpFilterRequest("name") : '-';
$subKaetgoriLabel = httpFilterRequest("kategori") ? httpFilterRequest("kategori") : '-';

$this->load->model("Katalogkategori");
$this->load->model("Katalog");
$this->load->model("Katalogfoto");
$this->load->model("Katalogcompare");

$katalog_kategori_url = new Katalogkategori();
$katalog_kategori = new Katalogkategori();
$katalog = new Katalog();
$katalog_count = new Katalog();

if ($name == '-' && $subKaetgoriLabel == '-') {
$katalog_kategori->selectByParams(array(), -1, -1, " AND A.KATEGORI_PARENT_ID = '0' ");
?>

  <script type="text/javascript">
  $(document).ready(function(){
    $(".xdata").hide();
    $("#less").hide();
    $("#show").click(function(){
        $(".xdata").slideDown();
        $("#show").hide();
        $("#less").show();
    });
    $("#less").click(function(){
        $(".xdata").hide();
        $("#less").hide();
        $("#show").show();
    });
  });
  </script>

  <section id="backColor">
    <div class="row">

      <?php
      $totaldata = 0;
      while($katalog_kategori->nextRow())
      {
        $pclass = '';
        if ($totaldata > 15) {
          $pclass = 'xdata';
        }
      ?>
        <div class="col-xl-3 col-lg-6 col-12 <?= $pclass ?>">
          <a href="<?= base_url('main/index/katalog?name='.$katalog_kategori->getField("url")) ?>" class="list-hover">
            <div class="card">
              <div class="card-content">
                <div class="card-body">
                  <div class="media d-flex">
                    <!-- <div class="align-self-center">
                      <i class="icon-speech warning font-large-1 float-left"></i>
                    </div> -->
                    <div class="media-body text-left">
                      <h4><?= $katalog_kategori->getField("nama_kategori_2"); ?></h4>
                      <!-- <span>New Comments</span> -->
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </a>
        </div>
      <?php
        if ($totaldata == 15) {
          echo '<div class="col-xl-12 text-center">
                  <button type="submit" class="btn round btn-min-width box-shadow-1 btn-primary" id="show"> Lihat Selengkapnya <i class="fa fa-chevron-down"></i></button>
                </div>';
        }
      $totaldata++;
      } ?>
      <div class="col-xl-12 text-center">
        <button type="submit" class="btn round btn-min-width box-shadow-1 btn-danger" id="less"> Tutup Selengkapnya <i class="fa fa-chevron-up"></i></button>
      </div>
    </div>
  </section>
<?php
} else
{
  if ($subKaetgoriLabel != '-') {
    $katalog_kategori_url->selectByParams(array(), -1, -1, " AND A.URL3 = '".$subKaetgoriLabel."' AND A.KATEGORI_PARENT_ID != '0' ");
    $katalog_kategori_url->firstRow();
    $id = $katalog_kategori_url->getField("KATEGORI_PARENT_ID");
  } else {
    $katalog_kategori_url->selectByParams(array(), -1, -1, " AND A.URL = '".$name."' AND A.KATEGORI_PARENT_ID = '0' ");
    $katalog_kategori_url->firstRow();
    $id = $katalog_kategori_url->getField("KATEGORI_ID");
  }
  $katalog_kategori->selectByParams(array(), -1, -1, " AND A.KATEGORI_PARENT_ID != '0' AND A.KATEGORI_PARENT_ID = '".$id."' ");
?>

  <section id="backColor">
    <div class="row">
      <div class="col-md-3 col-sm-12">
        <div class="card border-bottom-primary" style="zoom: 1;">
          <div class="card-body" style="padding: .5rem !important ">
            <div class="card-text">

              <ul id="tree1">
               <?php
                $result = array();
                while($katalog_kategori->nextRow())
                {
                      $result[$katalog_kategori->getField("nama_kategori_2")][] = $katalog_kategori->getField("nama").'||'.$katalog_kategori->getField("url3");
                  ?>
                <?php
                }
                // echo "<pre>"; print_r($result);
                ?>
                <?php
                 $menuLeftKatalog  = '';
                foreach ($result as $key => $value) {
                  $totalSub = count($value);

                  if ($totalSub == 0) {
                     $menuLeftKatalog .= '
                     <li><a> '.$key.'</a></li>
                     ';
                  } else
                  {
                     $menuLeftKatalog .= '
                     <li>
                      <a>'.$key.'</a>';
                    foreach ($value as $keySub ) {
                      $valSubKategori = explode("||", $keySub);
                      $menuLeftKatalog .= '
                        <ul>
                          <a href="'.base_url('main/index/katalog?kategori='.$valSubKategori[1]).'" style="cursor: pointer">
                            <li> '.$valSubKategori[0].'</li>
                          </a>
                        </ul> ';
                    }
                    $menuLeftKatalog .= '
                     </li>';
                  }
               ?>
                <?php
                } ?>
                <?= $menuLeftKatalog; ?>
              </ul>

            </div>
          </div>
        </div>
      </div>

      <div class="col-md-9 col-sm-12">
        <section id="basic-examples">
          <div class="row match-height">
            <?php
            // echo $id;
            $showRecord = 12;
            $pageView = "katalog_json/katalog_paging/";

            if ($subKaetgoriLabel != '-') {
              $katalog_kategori_url->selectByParams(array(), -1, -1, " AND A.URL3 = '".$subKaetgoriLabel."' AND A.KATEGORI_PARENT_ID != '0' ");
              $katalog_kategori_url->firstRow();
              $id = $katalog_kategori_url->getField("KATEGORI_ID");

              $arrStatement = array('A.KATEGORI_ID' => $id, 'A.STATUS' => '1', 'A.PUBLISH' => '1');
              $katalog->selectByParamsViewKatalogByKategori($arrStatement, $showRecord, 0);
              $rowCount = $katalog_count->getCountByParamsViewKatalogByKategori($arrStatement);

              // $urlShare = base_url('main/index/katalog?kategori='.$subKaetgoriLabel);
              $urlShare = base_url().'/main/index/katalog?kategori='.$subKaetgoriLabel;
            } else {
              $arrStatement = array('A.KATEGORI_PARENT_ID' => $id, 'A.STATUS' => '1', 'A.PUBLISH' => '1');
              // $arrStatement = array('A.URL' => $name, 'A.STATUS' => '1', 'A.PUBLISH' => '1');
              $katalog->selectByParamsViewKatalogByKategori2($arrStatement, $showRecord, 0);
              // echo $katalog->query;exit;
              $rowCount = $katalog_count->getCountByParamsViewKatalogByKategori2($arrStatement);
              // $urlShare = base_url('main/index/katalog?name='.$name);
              $urlShare = base_url().'/main/index/katalog?name='.$name;
            }

            $arrSerialized = serialize($arrStatement);
            $arrSerialized = str_replace('"', '@', $arrSerialized);
            $pagConfig = array('baseURL'=>$pageView, 'showRecord' => '\''.$showRecord.'||'.$name.'||'.$subKaetgoriLabel.'\'', 'totalRows'=>$rowCount, 'perPage'=>$showRecord, 'contentDiv'=>'tbodyKatalog', 'arrSerialized' => $arrSerialized, 'searchVarible' => "reqPencarian");
            // echo "<pre>"; print_r($pagConfig); die();
            $pagination =  new Pagination($pagConfig);

            if ($name != '-') {
              $labelDanger = 'Kategori '.ucwords(str_replace("-", " ", $name));
            } else {
              $labelDanger = 'Kategori '.ucwords(str_replace("-", " ", $subKaetgoriLabel));
            }

            ?>

            <div class="col-xl-12 col-md-12 col-sm-12 mb-2" id="searchCol">
              <div class="card-block">
                <fieldset>
                  <div class="input-group">
                    <input type="text" id="reqPencarian" class="form-control" placeholder="Cari Berdasarkan Nama Produk di <?= $labelDanger ?> ">
                    <div class="input-group-append">
                      <button class="btn btn-danger" onClick="<?= $pagination->createSearching();?>" type="submit">Cari</button>
                      <!-- <button class="btn btn-danger" type="submit">Cari</button> -->
                    </div>
                  </div>
                </fieldset>
              </div>
            </div>

            <!-- <div id="tbodyKatalog">  -->
            <?php
            if ($rowCount <= 0) {
              echo '<div class="col-xl-12 col-md-12 col-sm-12"><h5 class="alert alert-danger" style="width:100%">Tidak ada Produk di '.$labelDanger.'</h5></div>';
            } else
            {
              if ($rowCount == 1) {
                // $heightProd = ' height: 481.5px;';
                $heightProd = ' height: auto;';
              } else if ($rowCount == 2) {
                // $heightProd = ' height: 441.5px;';
                $heightProd = ' height: auto;';
              } else if ($rowCount == 3) {
                // $heightProd = ' height: 491.5px;';
                $heightProd = ' height: auto;';
              }else {
                // $heightProd = ' height: 441.5px;';
                $heightProd = ' height: auto;';
              }
            ?>
            <div id="fb-root"></div>
            <script async defer crossorigin="anonymous" src="https://connect.facebook.net/id_ID/sdk.js#xfbml=1&version=v6.0&appId=348026045405004&autoLogAppEvents=1"></script>
            <div class="row match-height" id="tbodyKatalog" style="width: 100%">
              <script type="text/javascript">
                $(document).ready(function(){
                  jQuery(".compare").on('change', function () {
                    var view = jQuery(this);
                      var isAllow = view.data('allow');
                      if (isAllow) {
                        var value = $(this).data("value");
                        var name = $(this).data("name");
                        if ($('#compare'+value).is(":checked"))
                        {
                          var check = '1';
                        } else {
                          var check = '0';
                        }
                        // alert(check);
                        $.post("katalog_json/compare",
                        {
                          name: name,
                          value: value,
                          check: check
                        },
                        function(data, status){
                          // alert(data + "\nStatus: " + status);
                          var str = data;
                          var isNotif = str.split("||");
                          $('#totalBanding').html(isNotif[2]+' Produk');
                          if (isNotif[0] === 'Gagal') {
                            // this.checked = false;
                            $('#compare'+value).prop('checked', false);
                            alertError2(isNotif[1]);
                          } else {
                            $('.btn-github').addClass('bounceIn');
                            setTimeout(function() {
                              $('.btn-github').removeClass('bounceIn');
                            }, 1000);
                            $('.fa-random').addClass('shake');
                            setTimeout(function() {
                              $('.fa-random').removeClass('shake');
                            }, 1000);
                          }
                          // $('.btn-github').removeClass('bounceIn');
                          // $('.btn-github').addClass('shake');
                        });
                      }
                  });

                  // $("#cardTitle a").click(function() {
                  //   var a = $(this).data("id");
                  //   $('#tbodyKatalog').hide();
                  //   $('#tbodyKatalog').hide();searchCol
                  //   $('#detailProduk').html(a);
                  // });
                });
              </script>
              <?php
              // echo $id;
              // echo "<pre>"; print_r($katalog); die();
                while($katalog->nextRow())
                {
                  $katalogid = $katalog->getField("KATALOGID");
                  $Katalogfoto = new Katalogfoto();
                  $Katalogfoto->selectByParams(array('KATALOGID' => $katalogid), -1, -1);
                  $Katalogfoto->firstRow();
                  if (file_exists('images/katalog/'.$Katalogfoto->getField("path_file")) && $Katalogfoto->getField("path_file") != '') {
                    $filenya = $Katalogfoto->getField("path_file");
                  } else {
                    // $filenya = '2748558.png';
                    $filenya = 'katalognotfound.jpg';
                  }
                ?>
                <div class="col-xl-3 col-md-6 col-sm-12">
                  <div class="card" style="<?= $heightProd ?>">
                    <div class="card-content">
                      <img class="card-img-top img-fluid" src="images/katalog/<?= $filenya ?>" alt="<?= base_url() ?>">
                      <div class="card-body">
                        <h1 class="card-title" id="cardTitle"><a href="<?= 'main/index/katalog_detail?id='.$katalog->getField("KATALOGID") ?>"><?= $katalog->getField("NAMAPRODUK") ?></a></h1>
                        <h2 class="card-name"><?= $katalog->getField("USER_NAMA") ?></h2>
                        <p class="card-text mb-2">Rp.  <?= number_format($katalog->getField("HARGA"), 0, ',', '.') ?></p>
                        <fieldset class="checkboxsas btn btn-danger btn-sm" id="btnfull">
                            <label>
                              <?php
                              session_start();
                              $Katalogcompare = new Katalogcompare();
                              $cekCompareSession = $Katalogcompare->getCountByParams(array('KATALOGID' => $katalogid, 'SESSIONID' => session_id()));
                              if ($cekCompareSession > 0 ) {
                                $checkProduk = ' checked';
                              } else {
                                $checkProduk = '';
                              }
                               ?>
                              <input type="checkbox" class="cursorPoin compare" data-allow="true" id="compare<?= $katalog->getField("KATALOGID") ?>" data-value="<?= $katalog->getField("KATALOGID") ?>" data-name="<?= $katalog->getField("NAMAPRODUK") ?>" <?= $checkProduk ?>> Bandingkan
                            </label>
                        </fieldset>
                        <!-- <hr> -->
                        <div class="social-buttons text-center mt-1">
                          <!-- Social Icons Outline Buttons -->
                            <a target="_blank" href="https://www.facebook.com/sharer/sharer.php?u=<?= $urlShare ?>" class="fb-xfbml-parse-ignore btn btn-social-icon btn-sm btn-facebook"><span class="fa fa-facebook"></span></a>
                            <a target="_blank" href="https://twitter.com/share?url=<?= $urlShare ?>" class="btn btn-social-icon btn-sm btn-twitter"><span class="fa fa-twitter" style="color: #fff"></span></a>
                          <!-- </div> -->
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <?php
                }  ?>
              <div class="col-xl-12 col-md-12 col-sm-12 pagingPadd">
                <?php echo $pagination->createLinks()?>
              </div>
            </div>

            <?php
            } ?>

          </div>
        </section>
      </div>

    </div>
  </section>

  <?php
  $katalogcompareTotalAll = new Katalogcompare();
  $cekTotalAll = $katalogcompareTotalAll->getCountByParams(array('SESSIONID' => session_id()));
  if ($cekTotalAll > 0) {
    $cekTotalAll = $cekTotalAll.' Produk';
  } else {
    $cekTotalAll = '';
  }
  ?>
  <a href="<?= base_url('main/index/katalog_compare?id='.session_id()) ?>" class="btn btn-social width-200 mr-1 mb-1 btn-github animated" data-animation="zoomInLeft" style="position: fixed;bottom: 30px;left: 30px;"><span class="fa fa-random font-medium-3"></span> <small style="font-size: .9em">Bandingkan <span id="totalBanding"><?= $cekTotalAll ?></span></small></a>

<?php
} ?>

<script src="<?=base_url()?>assets/new/vendors/js/extensions/bootstrap-treeview.min.js"></script>

<script type="text/javascript">
$(document).ready(function(){
  $.fn.extend({
    treed: function (o) {

      var openedClass = 'fa fa-minus-circle';
      var closedClass = 'fa fa-plus-circle';

      if (typeof o != 'undefined'){
        if (typeof o.openedClass != 'undefined'){
        openedClass = o.openedClass;
        }
        if (typeof o.closedClass != 'undefined'){
        closedClass = o.closedClass;
        }
      };

        //initialize each of the top levels
        var tree = $(this);
        tree.addClass("tree");
        tree.find('li').has("ul").each(function () {
            var branch = $(this); //li with children ul
            branch.prepend("<i class='indicator glyphicon " + closedClass + "'></i>");
            branch.addClass('branch');
            branch.on('click', function (e) {
                if (this == e.target) {
                    var icon = $(this).children('i:first');
                    icon.toggleClass(openedClass + " " + closedClass);
                    $(this).children().children().toggle();
                }
            })
            branch.children().children().toggle();
        });
        //fire event from the dynamically added icon
      tree.find('.branch .indicator').each(function(){
        $(this).on('click', function () {
            $(this).closest('li').click();
        });
      });
        //fire event to open branch if the li contains an anchor instead of text
        tree.find('.branch>a').each(function () {
            $(this).on('click', function (e) {
                $(this).closest('li').click();
                e.preventDefault();
            });
        });
        //fire event to open branch if the li contains a button instead of text
        tree.find('.branch>button').each(function () {
            $(this).on('click', function (e) {
                $(this).closest('li').click();
                e.preventDefault();
            });
        });
    }
  });

//Initialization of treeviews

$('#tree1').treed();
$('#tree2').treed({openedClass:'glyphicon-folder-open', closedClass:'glyphicon-folder-close'});
$('#tree3').treed({openedClass:'glyphicon-chevron-right', closedClass:'glyphicon-chevron-down'});

});
</script>
