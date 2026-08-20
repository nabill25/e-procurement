<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * @package 	eProcurement Application
 * @author 		eproc2025
 * @since 		25. Version 3.1
 * 
 */

include_once("functions/string.func.php");
include_once("functions/date.func.php");

class katalog_search_json extends CI_Controller {

	function __construct() {
		parent::__construct(); $this->db->query("SET TIME ZONE 'Asia/Jakarta'");

		//kauth
		if (!$this->kauth->getInstance()->hasIdentity())
		{
			// trow to unauthenticated page!
			//redirect('Login');
		}

		/* GLOBAL VARIABLE */
		$this->ID = isset($this->kauth->getInstance()->getIdentity()->REKANAN_ID) ? $this->kauth->getInstance()->getIdentity()->REKANAN_ID : '';

	    $this->USER_LOGIN_ID =  isset($this->kauth->getInstance()->getIdentity()->USER_LOGIN_ID) ? $this->kauth->getInstance()->getIdentity()->USER_LOGIN_ID : '';
	    $this->USER_LOGIN =  isset($this->kauth->getInstance()->getIdentity()->USER_LOGIN) ? $this->kauth->getInstance()->getIdentity()->USER_LOGIN : '';
	    $this->USER_NAMA =  isset($this->kauth->getInstance()->getIdentity()->USER_NAMA) ? $this->kauth->getInstance()->getIdentity()->USER_NAMA : '';
	    $this->USER_TYPE_ID =  isset($this->kauth->getInstance()->getIdentity()->USER_TYPE_ID) ? $this->kauth->getInstance()->getIdentity()->USER_TYPE_ID : '';
	    $this->REKANAN_ID =  isset($this->kauth->getInstance()->getIdentity()->REKANAN_ID) ? $this->kauth->getInstance()->getIdentity()->REKANAN_ID : '';
	    $this->UNIT_KERJA_ID =  isset($this->kauth->getInstance()->getIdentity()->UNIT_KERJA_ID) ? $this->kauth->getInstance()->getIdentity()->UNIT_KERJA_ID : '';
	    $this->NIP =  isset($this->kauth->getInstance()->getIdentity()->NIP) ? $this->kauth->getInstance()->getIdentity()->NIP : '';
	    $this->LOGIN_TIME = isset($this->kauth->getInstance()->getIdentity()->LOGIN_TIME) ? $this->kauth->getInstance()->getIdentity()->LOGIN_TIME : '';
	    $this->LOGIN_DATE = isset($this->kauth->getInstance()->getIdentity()->LOGIN_DATE) ? $this->kauth->getInstance()->getIdentity()->LOGIN_DATE : '';
	    $this->REKANAN = isset($this->kauth->getInstance()->getIdentity()->NAMA) ? $this->kauth->getInstance()->getIdentity()->NAMA : '';
	    $this->REKANAN_KODE = isset($this->kauth->getInstance()->getIdentity()->KODE) ? $this->kauth->getInstance()->getIdentity()->KODE : '';
	    $this->REKANAN_PKP = isset($this->kauth->getInstance()->getIdentity()->PKP) ? $this->kauth->getInstance()->getIdentity()->PKP : '';
	    $this->REKANAN_NPWP = isset($this->kauth->getInstance()->getIdentity()->NPWP) ? $this->kauth->getInstance()->getIdentity()->NPWP : '';
	    $this->REKANAN_STATUS_PERUSAHAAN = isset($this->kauth->getInstance()->getIdentity()->STATUS_PERUSAHAAN) ? $this->kauth->getInstance()->getIdentity()->STATUS_PERUSAHAAN : '';
	    $this->REKANAN_STATUS_VALIDASI = isset($this->kauth->getInstance()->getIdentity()->STATUS_VALIDASI) ? $this->kauth->getInstance()->getIdentity()->STATUS_VALIDASI : '';
	}  

	function json()
  	{

	    $this->load->model('Katalog');
	    $this->load->model('Katalogfoto');
	    $this->load->model("Katalogkategori");
		$this->load->model("Katalogcompare");
	    $this->load->library("Pagination"); 


	    $search2 = explode("||", $this->input->post("search2"));
	    $reqName = $this->input->post("name");
	    $reqPage = $this->input->post("page");
	    $reqPencarian = strtoupper($this->input->post("search"));
	    $reqPencarian2 = $search2[0];
	    $reqId = $search2[1];
	    $reqShow = $this->input->post("show"); 
	    $reqContent = $this->input->post("content");
	    $reqArrStatement = unserialized($this->input->post("array_serialized"));

	    $katalog_kategori_url = new Katalogkategori();
		$katalog_kategori = new Katalogkategori();
	    $katalog = new Katalog();
		$katalog_count = new Katalog();
	    if(isset($reqPage)){

	      $dsplyStart = !empty($reqPage)?$reqPage:0;
	      $dsplyRange = $reqShow;

	      //get rows
	      // $statement= " AND (UPPER(A.NAMAPRODUK) LIKE '%".strtoupper($reqPencarian)."%') ";
	      switch ($reqPencarian2) {
	      	case '1': // Perusahaan
		      	$statement= " AND (UPPER(A.USER_NAMA) LIKE '%".strtoupper($reqPencarian)."%') ";
	            $reqArrStatement = array();
	            $katalog->selectByParamsViewKatalogSearch($reqArrStatement, $dsplyRange, $dsplyStart, $statement);
	            $rowCount = $katalog_count->getCountByParamsViewKatalogSearch($reqArrStatement, $statement);
	      		break;
	      	case '2': // Merek
	      		$statement= " AND (UPPER(A.MEREK) LIKE '%".strtoupper($reqPencarian)."%')";
	            $reqArrStatement = array();
	            $katalog->selectByParamsViewKatalogSearch($reqArrStatement, $dsplyRange, $dsplyStart, $statement);
	            $rowCount = $katalog_count->getCountByParamsViewKatalogSearch($reqArrStatement, $statement);
	      		break;
	      	case '3': // Kategori
	      		$statement= " AND (UPPER(A.KATEGORI) LIKE '%".strtoupper($reqPencarian)."%')";
	            $reqArrStatement = array();
	            $katalog->selectByParamsViewKatalogSearch($reqArrStatement, $dsplyRange, $dsplyStart, $statement);
	            $rowCount = $katalog_count->getCountByParamsViewKatalogSearch($reqArrStatement, $statement);
	      		break;
	      	
	      	default: // valu:0 = all
	      		$statement= " AND (UPPER(A.NAMAPRODUK) LIKE '%".strtoupper($reqPencarian)."%') OR (UPPER(A.USER_NAMA) LIKE '%".strtoupper($reqPencarian)."%') OR (UPPER(A.MEREK) LIKE '%".strtoupper($reqPencarian)."%') OR (UPPER(A.KATEGORI) LIKE '%".strtoupper($reqPencarian)."%')";
	            $reqArrStatement = array();
	            $katalog->selectByParamsViewKatalogSearch($reqArrStatement, $dsplyRange, $dsplyStart, $statement);
	            $rowCount = $katalog_count->getCountByParamsViewKatalogSearch($reqArrStatement, $statement);
	      		break;
	      }
  
          // echo $katalog->query; die();

          $arrSerialized = serialize($statement);  
          $arrSerialized = str_replace('"', '@', $arrSerialized);   
          // $pagConfig = array('baseURL'=>$pageView, 'showRecord' => '\''.$showRecord.'||'.$name.'||'.$subKaetgoriLabel.'\'', 'totalRows'=>$rowCount, 'perPage'=>$showRecord, 'contentDiv'=>'tbodyKatalog', 'arrSerialized' => $arrSerialized, 'searchVarible' => "reqPencarian");
      		$pagConfig = array('baseURL'=>'katalog_search_json/json', 'showRecord' => '\''.$reqShow.'\'', 'totalRows'=>$rowCount, 'currentPage'=>$dsplyStart, 'perPage'=>$dsplyRange, 'contentDiv'=>$reqContent, 'searchText' => $reqPencarian, 'arrSerialized' => $this->input->post("array_serialized"));

          // echo "<pre>"; print_r($pagConfig); die();
          $pagination =  new Pagination($pagConfig);

	       ?>

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
                          $('.danger-animated').addClass('bounceIn');
                          setTimeout(function() {
                            $('.danger-animated').removeClass('bounceIn');
                          }, 1000);
                          $('.fa-random').addClass('shake');
                          setTimeout(function() {
                            $('.fa-random').removeClass('shake');
                          }, 1000);
                        }
                      });
                    } 
                }); 
              });
              function cart(z) {
                  var view = z; 
				  var a = view.split("||");
				  var katalog = a[0];
				  var paket = a[1]; 
                  // alert(katalog+'-'+paket);
                  // alert(check); 
                  $.post("katalog_json/cart",
                  {
                    katalog: katalog,
                    paket: paket
                  },
                  function(data, status){
                    var str = data;
                    var isNotif = str.split("||");
                    $('#totalCart').html(isNotif[2]+' ');
                    if (isNotif[0] === 'Gagal') {
                      alertError2(isNotif[1]);
                    } else {
                      $('.btn-github').addClass('bounceIn');
                      setTimeout(function() {
                        $('.btn-github').removeClass('bounceIn');
                      }, 1000);
                      $('.fa-shopping-cart').addClass('shake');
                      setTimeout(function() {
                        $('.fa-shopping-cart').removeClass('shake');
                      }, 1000);
                    }

                  });
                } 
            </script>
            <?php
            // echo $id;
            // echo "<pre>"; print_r($katalog); die();
            if ($rowCount < 1) {
            	echo '<div class="col-xl-12 col-md-12 col-sm-12"><h5 class="alert alert-danger" style="width:100%">Data yang dicari tidak ada</h5></div>';
            } else 
            {
            while($katalog->nextRow())
            { 
            	$katalogid = $katalog->getField("KATALOGID");
                $Katalogfoto = new Katalogfoto();
                $Katalogfoto->selectByParams(array('KATALOGID' => $katalogid), -1, -1);
                $Katalogfoto->firstRow();
                if (file_exists('images/katalog/'.$Katalogfoto->getField("path_file"))) {
                  $filenya = $Katalogfoto->getField("path_file");
                } else {
                  $filenya = '';
                }

                session_start();
                $Katalogcompare = new Katalogcompare();
                $cekCompareSession = $Katalogcompare->getCountByParams(array('KATALOGID' => $katalogid, 'SESSIONID' => session_id()));
                if ($cekCompareSession > 0 ) {
                  $checkProduk = ' checked';
                } else {
                  $checkProduk = '';
                }

                if ($katalog->getField("PUBLISH") == '1') {
                	$stylePublish = '<img src="images/centang.png">';
                } else {
                	$stylePublish = '<img src="images/uncentang.png">';
                }
            ?>

	            <div class="col-xl-12 col-md-12 col-sm-12 backWhite pagingPadd2 mb-1">
	            	<div class="media">  
					<div class="media-body pl-1">
						<h5 class="media-heading"> 
	            	    	<a onClick="openAdd('main/loadUrl/main/katalog_validasi_rekanan_detail_produk?reqId=<?= $katalog->getField("KATALOGID") ?>');">
								<?= $stylePublish.' '.$katalog->getField("NAMAPRODUK") ?> 
							</a> 
						</h5>
						<p class="containerKet">
							<?= strip_tags($katalog->getField("KETERANGANTAMBAHAN")) ?> <hr>
						</p>
						<fieldset class="checkboxsas btn btn-danger btn-sm"> 
							<input type="checkbox" class="cursorPoin compare" data-allow="true" id="compare<?= $katalog->getField("KATALOGID") ?>" data-value="<?= $katalog->getField("KATALOGID") ?>" data-name="<?= $katalog->getField("NAMAPRODUK") ?>" <?= $checkProduk ?>> Bandingkan 
		              	</fieldset>
		              	<?php 
		              	if ($katalog->getField("PUBLISH") == '1') { ?>
		              	<button class="checkboxsas btn btn-dark btn-sm" onclick="cart('<?= $katalog->getField("KATALOGID").'||'.$reqId ?>')">  <span class="fa fa-shopping-cart cart"></span>
		              	</button>
		              	<?php 
		              	} else { } ?>
		              	<?php 
						if ($reqPencarian2 == 1 && $reqPencarian != '') { 
							$labelPerusahaan = ' style="color:red;"';
						} else {
							$labelPerusahaan = '';
						}?>
	        	    	<a onClick="openAdd('main/loadUrl/main/katalog_validasi_rekanan_detail_rekanan?reqId=<?= $katalog->getField("REKANAN_ID") ?>');" <?= $labelPerusahaan ?>>
							<span class="fa fa-building-o cursor2"> <?= $katalog->getField("USER_NAMA") ?></span>
						</a>
						<?php 
						if ($reqPencarian2 == 2 && $reqPencarian != '') { 
							$labelMerek = ' style="color:red;"';
						} else {
							$labelMerek = '';
						}?>
			          	<span class="fa fa-tag cursor2" <?= $labelMerek ?>> <?= $katalog->getField("MEREK") ?></span>
			          	<span class="fa fa-money cursor2"> Rp. <?= number_format($katalog->getField("HARGA"),2,',','.') ?></span>

			          	<?php 
						if ($reqPencarian2 == 3 && $reqPencarian != '') { 
							$labelKategori = ' style="color:red;"';
						} else {
							$labelKategori = '';
						}?>
			          		<?php 
			          		if ($katalog->getField("KATEGORI")) {
			          			echo '<span class="fa fa-list cursor2" '.$labelKategori.'> ';
			          			$exKategori = explode(',', $katalog->getField("KATEGORI"));
			          			foreach ($exKategori as $key => $value) {
			          				$exKategori2 = explode('||', $value);
			          				$kategori_id[] = $exKategori2[0];
			          				$kategori_name[] = $exKategori2[1];

			          				echo $exKategori2[1].', ';
			          			}
			          			echo '</span>';
			          			// $exKategori2 = explode(',', $exKategori);
			          			// echo $exKategori2[1];
			          		}
			          		?>
		          		</span>
					</div>
					</div>
	            </div>

            <?php 
            } ?>
            <div class="col-xl-12 col-md-12 col-sm-12 pagingPadd">
              <?php echo $pagination->createLinks2()?> 
            </div>
	      <?php
	  	   } // end if ($rowCount < 1) {
		}
	}

	function compare()
	{ 
		session_start();
		$this->load->library("kauth");  $userLogin = new kauth();
		$this->load->model("Katalogcompare");
		/* create objects */
		$katalogcompare = new Katalogcompare();
		$katalogcompareTotal = new Katalogcompare();
		$katalogcompareTotalAll = new Katalogcompare();

		/* VARIABLE */
		$name	= $this->input->post("name");
		$value	= $this->input->post("value"); 
		$check	= $this->input->post("check"); 

		$reqUserId   = $this->ID;  

		$katalogcompare->setField('KATALOGID', $value);  
		$katalogcompare->setField('SESSIONID', session_id());  
		$katalogcompare->setField('BROWSER', $_SERVER['HTTP_USER_AGENT']);  


		$cekTotalAll = $katalogcompareTotalAll->getCountByParams(array('SESSIONID' => session_id()));
		if ($check == 1) {
			if ($cekTotalAll <= 2) { 
					if($katalogcompare->insert())
		        		$cekTotal = $katalogcompareTotal->getCountByParams(array('SESSIONID' => session_id()));
						echo "Sukses||Data ".$name." berhasil di Simpan||".$cekTotal; 
			} else {
				echo "Gagal||Bandingkan sudah ".$cekTotalAll." produk||".$cekTotalAll; 
			}
		
		}

		if ($check == 0) {
			if($katalogcompare->delete())
        		$cekTotal = $katalogcompareTotal->getCountByParams(array('SESSIONID' => session_id()));
				echo "Sukses||Data ".$name." berhasil di Hapus||".$cekTotal;
		}

	}  
 }	
?>
