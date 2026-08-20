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

class rekanan_search_json extends CI_Controller {

	function __construct() {
		parent::__construct(); $this->db->query("SET TIME ZONE 'Asia/Jakarta'");

		//kauth
		if (!$this->kauth->getInstance()->hasIdentity())
		{
		}
		/* GLOBAL VARIABLE */
		$this->ID = $this->kauth->getInstance()->getIdentity()->REKANAN_ID;
    $this->USER_LOGIN_ID =  $this->kauth->getInstance()->getIdentity()->USER_LOGIN_ID;
    $this->USER_LOGIN =  $this->kauth->getInstance()->getIdentity()->USER_LOGIN;
    $this->USER_NAMA =  $this->kauth->getInstance()->getIdentity()->USER_NAMA;
    $this->USER_TYPE_ID =  $this->kauth->getInstance()->getIdentity()->USER_TYPE_ID;
    $this->REKANAN_ID =  $this->kauth->getInstance()->getIdentity()->REKANAN_ID;
    $this->UNIT_KERJA_ID =  $this->kauth->getInstance()->getIdentity()->UNIT_KERJA_ID;
    $this->NIP =  $this->kauth->getInstance()->getIdentity()->NIP;
    $this->LOGIN_TIME = $this->kauth->getInstance()->getIdentity()->LOGIN_TIME;
    $this->LOGIN_DATE = $this->kauth->getInstance()->getIdentity()->LOGIN_DATE;
    $this->REKANAN = $this->kauth->getInstance()->getIdentity()->NAMA;
    $this->REKANAN_KODE = $this->kauth->getInstance()->getIdentity()->KODE;
    $this->REKANAN_PKP = $this->kauth->getInstance()->getIdentity()->PKP;
    $this->REKANAN_NPWP = $this->kauth->getInstance()->getIdentity()->NPWP;
    $this->REKANAN_STATUS_PERUSAHAAN = $this->kauth->getInstance()->getIdentity()->STATUS_PERUSAHAAN;
    $this->REKANAN_STATUS_VALIDASI = $this->kauth->getInstance()->getIdentity()->STATUS_VALIDASI;
	}

	// https://stackoverflow.com/questions/10313332/how-to-highlight-search-results
	function json()
	{
    $this->load->model('Rekananpotensi');
    $this->load->library("Pagination");

    $search2 = explode("||", $this->input->post("search2"));
    $reqStatusValidasi = $this->input->post("search3");
    $reqKualifikasi = $this->input->post("search4");
    $reqName = $this->input->post("name");
    $reqPage = $this->input->post("page");
    $reqPencarian = strtolower($this->input->post("search"));
    $reqPencarian2 = $search2[0];
    $reqId = $search2[1];
    $reqShow = $this->input->post("show");
    $reqContent = $this->input->post("content");
    $reqArrStatement = unserialized($this->input->post("array_serialized"));

    // words to find
		// $words = array($reqPencarian);

    $rekanan_potensi = new Rekananpotensi();
		$rekanan_potensi_count = new Rekananpotensi();

    if(isset($reqPage))
    {

	    $dsplyStart = !empty($reqPage)?$reqPage:0;
	    $dsplyRange = $reqShow;

	    $queryStatusValidasi = '';
	    if ($reqStatusValidasi == '1') {
	    $queryStatusValidasi .= " AND STATUS_VALIDASI = '1' ";
	    } else {
	    $queryStatusValidasi .= " AND STATUS_VALIDASI IN (0,2,3,4,10) ";
	    }

	    switch ($reqKualifikasi) {
	    	case '3': // Kecil / Non-Kecil
		    	$queryStatusValidasi .= ' ';
	    		break;

	    	case '2': // Non-Kecil
		    	$queryStatusValidasi .= ' AND A.REKANAN_KUALIFIKASI_ID = 2';
	    		break;

	    	case '1': // Kecil
		    	$queryStatusValidasi .= ' AND A.REKANAN_KUALIFIKASI_ID = 1';
	    		break;

	    	default:
	    		// code...
	    		break;
	    }

	    //get rows
	    // $statement= " AND (NAMAPRODUK LIKE '%".$reqPencarian."%') ";
	    switch ($reqPencarian2) {

	    	case '1': // Data Admin = view_potensi_admin_pengurus
	    			$statement = "AND (LOWER(B.NAMA || ' ' || A.NAMA) LIKE '%".$reqPencarian."%' OR pengurus.nama LIKE '%".$reqPencarian."%') ".$queryStatusValidasi;
	          $reqArrStatement = array();
	          $rekanan_potensi->selectByParamsAllSearch($reqArrStatement, $dsplyRange, $dsplyStart, $statement);
	          $rowCount = $rekanan_potensi_count->getCountselectByParamsAllSearch($reqArrStatement, $statement);
						// $rowCount = $rekanan_potensi->countRow();
	    		break;
	    	case '2': // Data Teknis = view_potensi_teknis_pengalaman, view_potensi_teknis_sertifikat
	    			$statement = "AND (LOWER(B.NAMA || ' ' || A.NAMA) LIKE '%".$reqPencarian."%' OR pengalaman.nama LIKE '%".$reqPencarian."%' OR sertifikat.nama LIKE '%".$reqPencarian."%') ".$queryStatusValidasi;

	          $reqArrStatement = array();
	          $rekanan_potensi->selectByParamsAllSearch($reqArrStatement, $dsplyRange, $dsplyStart, $statement);
	          $rowCount = $rekanan_potensi_count->getCountselectByParamsAllSearch($reqArrStatement, $statement);
						// $rowCount = $rekanan_potensi->countRow();
	    		break;
	    	case '3': // KBLI
	    			$statement = "AND (LOWER(B.NAMA || ' ' || A.NAMA) LIKE '%".$reqPencarian."%' OR kbli.bidang_usaha_nama LIKE '%".$reqPencarian."%') ".$queryStatusValidasi;
	          $reqArrStatement = array();
	          $rekanan_potensi->selectByParamsAllSearch($reqArrStatement, $dsplyRange, $dsplyStart, $statement);
	          $rowCount = $rekanan_potensi_count->getCountselectByParamsAllSearch($reqArrStatement, $statement);
						// $rowCount = $rekanan_potensi->countRow();
	    		break;
	    	case '4': // Approval
	    		break;

	    	default: // valu:0 = all
	    			$statement = "AND (LOWER(B.NAMA || ' ' || A.NAMA) LIKE '%".$reqPencarian."%' OR pengurus.nama LIKE '%".$reqPencarian."%' OR pengalaman.nama LIKE '%".$reqPencarian."%' OR sertifikat.nama LIKE '%".$reqPencarian."%' OR kbli.bidang_usaha_nama LIKE '%".$reqPencarian."%') ".$queryStatusValidasi;
	          $reqArrStatement = array();
	          $rekanan_potensi->selectByParamsAllSearch($reqArrStatement, $dsplyRange, $dsplyStart, $statement);
	          $rowCount = $rekanan_potensi_count->getCountselectByParamsAllSearch($reqArrStatement, $statement);
	    		break;
	    }
      // echo $rekanan_potensi->query;
      // echo $rowCount.'<br>'.$rekanan_potensi_count->query; die();
      $arrSerialized = serialize($statement);
      $arrSerialized = str_replace('"', '@', $arrSerialized);
  		$pagConfig = array('baseURL'=>'rekanan_search_json/json', 'showRecord' => '\''.$reqShow.'\'', 'totalRows'=>$rowCount, 'currentPage'=>$dsplyStart, 'perPage'=>$dsplyRange, 'contentDiv'=>$reqContent, 'searchText' => $reqPencarian, 'arrSerialized' => $this->input->post("array_serialized"));
      // echo "<pre>"; print_r($pagConfig); die();
      $pagination =  new Pagination($pagConfig);

          echo '<span>Total Pencarian: <b>'.$rowCount.'</b></span><br><br>';
          // echo "<pre>"; print_r($rekanan_potensi); die();
        if ($rowCount == 0) { ?>
        	<div class="col-xl-12 col-md-12 col-sm-12"><h5 class="" style="width:100%">. : : Data yang dicari dengan kata kunci <?= $reqPencarian ?> tidak ada : : .</h5></div>
				<?php
				} else
        {
          while($rekanan_potensi->nextRow())
          {
            if ($rekanan_potensi->getField("STATUS_VALIDASI") == '1') {
            	$stylePublish = '<img src="images/centang.png">';
            } else {
            	$stylePublish = '<img src="images/uncentang.png">';
            }

            switch ($rekanan_potensi->getField("REKANAN_KUALIFIKASI_ID")) {
				    	case '3': // Kecil / Non-Kecil
					    	$setKualifikasi = '<span class="badge badge-primary">'.$rekanan_potensi->getField("NAMA_KUALIFIKASI").'</span>';
				    		break;

				    	case '2': // Non-Kecil
					    	$setKualifikasi = '<span class="badge badge-danger">'.$rekanan_potensi->getField("NAMA_KUALIFIKASI").'</span>';
				    		break;

				    	case '1': // Kecil
					    	$setKualifikasi = '<span class="badge badge-success">'.$rekanan_potensi->getField("NAMA_KUALIFIKASI").'</span>';
				    		break;

				    	default:
					    	$setKualifikasi = '-';
				    		break;
				    }
          ?>

          <div class="col-xl-12 col-md-12 col-sm-12 backWhite pagingPadd2 mb-1">
          	<div class="media">
							<div class="media-body pl-1">
								<h5 class="media-heading">
		      	    	<a onClick="openAdd('main/loadUrl/main/data_rekanan_potensi?reqId=<?= $rekanan_potensi->getField("REKANAN_ID") ?>&reqType=<?= $reqPencarian2 ?>');">
										<?= $stylePublish.' '.$rekanan_potensi->getField("NAMA") ?>
									</a>
								</h5>
								<?php
								$html = '';
								switch ($reqPencarian2) {
						    	case '1': // Data Admin = view_potensi_admin_pengurus
						      		$html .= '<p class="containerKet" style="height:auto !important">';
						      		$ex = explode(' || ',$rekanan_potensi->getField("KEYWORDS_PENGURUS"));
						      		// echo "<pre>"; print_r($ex); die();
					      			for ($i=0; $i < count($ex) ; $i++) {
												$html .=  '<i class="fa fa-circle" style="font-size:9px" aria-hidden="true"></i> '.ucwords($this->highlightKeywords( $ex[$i], strtolower($reqPencarian))).' <sup><i>pengurus</i></sup>&nbsp;';
					      			}
											$html .= '</p>';
						    		break;
						    	case '2': // Data Teknis = view_potensi_teknis_pengalaman, view_potensi_teknis_sertifikat
						      		$html .= '<p class="containerKet" style="height:auto !important">';
						      		$ex2 = explode(' || ',$rekanan_potensi->getField("KEYWORDS_PENGALAMAN"));
					      			for ($i2=0; $i2 < count($ex2) ; $i2++) {
												$html .=  '<i class="fa fa-circle" style="font-size:9px" aria-hidden="true"></i> '.ucwords($this->highlightKeywords( $ex2[$i2], strtolower($reqPencarian))).' <sup><i>pengalaman</i></sup>&nbsp;';
					      			}

					      			$html .= '<br>';
					      			$ex3 = explode(' || ',$rekanan_potensi->getField("KEYWORDS_SERTIFIKAT"));
					      			for ($i3=0; $i3 < count($ex3) ; $i3++) {
												$html .=  '<i class="fa fa-circle" style="font-size:9px" aria-hidden="true"></i> '.ucwords($this->highlightKeywords( $ex3[$i3], strtolower($reqPencarian))).' <sup><i>sertifikat</i></sup> &nbsp;';
					      			}
											$html .= '</p>';
						    		break;
						    	case '3': // KBLI
						      		$html .= '<p class="containerKet" style="height:auto !important">';
						      		$ex4 = explode(' || ',$rekanan_potensi->getField("KEYWORDS_KBLI"));
					      			for ($i4=0; $i4 < count($ex4) ; $i4++) {
						      			$ex44 = explode('--',$ex4[$i4]);
												$html .=  '<i class="fa fa-circle" style="font-size:9px" aria-hidden="true"></i> '.ucwords($this->highlightKeywords( $ex44[0], strtolower($reqPencarian))).' <sup><i>'.$ex44[1].'</i></sup> &nbsp;';
					      			}
											$html .= '</p>';
						    		break;
						    	case '4': // Approval
						    		break;

						    	default: // valu:0 = all
						    			$html .= '<p class="containerKet" style="height:auto !important">';
						    			$ex = explode(' || ',$rekanan_potensi->getField("KEYWORDS_PENGURUS"));
						      		// echo "<pre>"; print_r($ex); die();
					      			for ($i=0; $i < count($ex) ; $i++) {
												$html .=   '<i class="fa fa-circle" style="font-size:9px" aria-hidden="true"></i> '.ucwords($this->highlightKeywords( $ex[$i], strtolower($reqPencarian))).' <sup><i>pengurus</i></sup> &nbsp;';
					      			}
					      			$html .= '<br>';
						      		$ex2 = explode(' || ',$rekanan_potensi->getField("KEYWORDS_PENGALAMAN"));
					      			for ($i2=0; $i2 < count($ex2) ; $i2++) {
												$html .=  '<i class="fa fa-circle" style="font-size:9px" aria-hidden="true"></i> '.ucwords($this->highlightKeywords( $ex2[$i2], strtolower($reqPencarian))).' <sup><i>pengalaman</i></sup>&nbsp;';
					      			}

					      			$html .= '<br>';
					      			$ex3 = explode(' || ',$rekanan_potensi->getField("KEYWORDS_SERTIFIKAT"));
					      			for ($i3=0; $i3 < count($ex3) ; $i3++) {
												$html .=  '<i class="fa fa-circle" style="font-size:9px" aria-hidden="true"></i> '.ucwords($this->highlightKeywords( $ex3[$i3], strtolower($reqPencarian))).' <sup><i>sertifikat</i></sup> &nbsp;';
					      			}

					      			$html .= '<br>';
						      		$ex4 = explode(' || ',$rekanan_potensi->getField("KEYWORDS_KBLI"));
					      			for ($i4=0; $i4 < count($ex4) ; $i4++) {
						      			$ex44 = explode('--',$ex4[$i4]);
												$html .=  '<i class="fa fa-circle" style="font-size:9px" aria-hidden="true"></i> '.ucwords($this->highlightKeywords( $ex44[0], strtolower($reqPencarian))).' <sup><i>'.$ex44[1].'</i></sup> &nbsp;';
					      			}

											$html .= '</p>';
						    		break;
						    }
						    echo $html;
						    ?>
						    <hr>
								<span class="fa fa-pencil cursor2"> Kualifikasi: <?=$setKualifikasi ?: '-'?></span><br>
								<span class="fa fa-map-marker cursor2"> <?=strip_tags($rekanan_potensi->getField("ALAMAT")).' '.$rekanan_potensi->getField("KOTA") ?: '-'?></span><br>
								<span class="fa fa-id-card cursor2"> <?=$rekanan_potensi->getField("NPWP") ?: '-'?></span>
								<span class="fa fa-phone cursor2"> <?=$rekanan_potensi->getField("TELEPON") ?: '-'?></span>
								<span class="fa fa-envelope cursor2"> <?=$rekanan_potensi->getField("EMAIL") ?: '-'?></span>
								<span class="fa fa-globe cursor2"> <?=$rekanan_potensi->getField("WEBSITE") ?: '-'?></span>
      			</span>
					</div>
				</div>
      </div>

		    <?php
		    } ?>
      <div class="col-xl-12 col-md-12 col-sm-12 pagingPadd">
        <?php echo $pagination->createLinks4()?>
      </div>
				<?php
	   	} // end if ($rowCount < 1) {
		}
	}

 	function highlightKeywords($text, $keyword) {
		$wordsAry = explode(" ", $keyword);
		$wordsCount = count($wordsAry);

		for($i=0;$i<$wordsCount;$i++) {
			$highlighted_text = "<span style='font-weight:bold; background-color: yellow'>$wordsAry[$i]</span>";
			$text = str_ireplace($wordsAry[$i], $highlighted_text, $text);
		}

		return $text;
	}

}

?>
