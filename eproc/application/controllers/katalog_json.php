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
include_once("functions/default.func.php");

class katalog_json extends CI_Controller {

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

		$this->load->model("Katalog");
		$katalog = new Katalog();

		$aColumns 			= array('KATALOGID','PUBLISH_STATUS','NAMAPRODUK','MEREK','HARGA','STATUS','PUBLISH','FOTO','KATALOG');
		$aColumnsAlias		= array('KATALOGID','PUBLISH_STATUS', 'NAMAPRODUK','MEREK','HARGA','STATUS','PUBLISH','FOTO','KATALOG');

		/*
		 * Ordering
		 */
		if ( isset( $_GET['iSortCol_0'] ) )
		{
			$sOrder = " ORDER BY ";

			//Go over all sorting cols
			for ( $i=0 ; $i<intval( $_GET['iSortingCols'] ) ; $i++ )
			{
				//If need to sort by current col
				if ( $_GET[ 'bSortable_'.intval($_GET['iSortCol_'.$i]) ] == "true" )
				{
					//Add to the order by clause
					$sOrder .= $aColumnsAlias[ intval( $_GET['iSortCol_'.$i] ) ];

					//Determine if it is sorted asc or desc
					if (substr_compare(( $_GET['sSortDir_'.$i] ), "asc", 0) == 0)
					{
						$sOrder .=" asc, ";
					}else
					{
						$sOrder .=" desc, ";
					}
				}
			// echo $_GET['sSortDir_'.$i];
			}
			//Remove the last space / comma
			$sOrder = substr_replace( $sOrder, "", -2 );

			//Check if there is an order by clause
			if ( trim($sOrder) == "ORDER BY KATALOGID desc" )
			{
				$sOrder = " ORDER BY A.KATALOGID desc";

			}
			// echo $sOrder;
		}

		$sWhere = "";
		$nWhereGenearalCount = 0;
		if (isset($_GET['sSearch']))
		{
			$sWhereGenearal = $_GET['sSearch'];
		}
		else
		{
			$sWhereGenearal = '';
		}

		if ( $_GET['sSearch'] != "" )
		{
			//Set a default where clause in order for the where clause not to fail
			//in cases where there are no searchable cols at all.
			$sWhere = " AND (";
			for ( $i=0 ; $i<count($aColumnsAlias)+1 ; $i++ )
			{
				//If current col has a search param
				if ( $_GET['bSearchable_'.$i] == "true" )
				{
					//Add the search to the where clause
					$sWhere .= $aColumnsAlias[$i]." LIKE '%".$_GET['sSearch']."%' OR ";
					$nWhereGenearalCount += 1;
				}
			}
			$sWhere = substr_replace( $sWhere, "", -3 );
			$sWhere .= ')';
		}

		/* Individual column filtering */
		$sWhereSpecificArray = array();
		$sWhereSpecificArrayCount = 0;
		for ( $i=0 ; $i<count($aColumnsAlias) ; $i++ )
		{
			if ( $_GET['bSearchable_'.$i] == "true" && $_GET['sSearch_'.$i] != '' )
			{
				//If there was no where clause
				if ( $sWhere == "" )
				{
					$sWhere = "AND ";
				}
				else
				{
					$sWhere .= " AND ";
				}

				//Add the clause of the specific col to the where clause
				$sWhere .= $aColumnsAlias[$i]." LIKE '%' || :whereSpecificParam".$sWhereSpecificArrayCount." || '%' ";

				//Inc sWhereSpecificArrayCount. It is needed for the bind var.
				//We could just do count($sWhereSpecificArray) - but that would be less efficient.
				$sWhereSpecificArrayCount++;

				//Add current search param to the array for later use (binding).
				$sWhereSpecificArray[] =  $_GET['sSearch_'.$i];

			}
		}

		//If there is still no where clause - set a general - always true where clause
		if ( $sWhere == "" )
		{
			$sWhere = " AND 1=1";
		}

		//Bind variables.
		if ( isset( $_GET['iDisplayStart'] ))
		{
			$dsplyStart = $_GET['iDisplayStart'];
		}
		else{
			$dsplyStart = 0;
		}
		if ( isset( $_GET['iDisplayLength'] ) && $_GET['iDisplayLength'] != '-1' )
		{
			$dsplyRange = $_GET['iDisplayLength'];
			if ($dsplyRange > (2147483645 - intval($dsplyStart)))
			{
				$dsplyRange = 2147483645;
			}
			else
			{
				$dsplyRange = intval($dsplyRange);
			}
		}
		else
		{
			$dsplyRange = 2147483645;
		}

		// $statement .= " AND NOT A.USER_TYPE_ID = 6 ";
		 if($this->USER_TYPE_ID == "6") {
        	$statement .= ' AND A.REKANAN_ID = '.$this->ID.' ';
        }
		$statement .= "AND (UPPER(A.NAMAPRODUK) LIKE '%".strtoupper($_GET['sSearch'])."%' OR UPPER(A.NOPRODUK) LIKE '".strtoupper($_GET['sSearch'])."%')";
		$allRecord = $katalog->getCountByParams(array(), $statement);
		if($_GET['sSearch'] == "")
			$allRecordFilter = $allRecord;
		else
			$allRecordFilter =  $katalog->getCountByParams(array(), $statement);

        if($this->USER_TYPE_ID == "6") {
        	$statement .= ' AND A.REKANAN_ID = '.$this->ID.'';
			$katalog->selectByParams(array(), $dsplyRange, $dsplyStart, $statement, $sOrder);
		} else {
			$katalog->selectByParams(array(), $dsplyRange, $dsplyStart, $statement, $sOrder);
		}

		$output = array(
			"sEcho" => intval($_GET['sEcho']),
			"iTotalRecords" => $allRecord,
			"iTotalDisplayRecords" => $allRecordFilter,
			"aaData" => array()
		);

		while($katalog->nextRow())
		{
			$row = array();
			for ( $i=0 ; $i<count($aColumns) ; $i++ )
			{
				if($aColumns[$i]=='NO')
						$row[] = $number;
					elseif($aColumns[$i]=='STATUS' || $aColumns[$i]=='PUBLISH')
					{
						if($katalog->getField(trim($aColumns[$i])))
							$row[] = '<img src="images/centang.png">';
						else
							$row[] = '<img src="images/uncentang.png">';
					} elseif($aColumns[$i]=='HARGA')
					{
						$row[] = number_format($katalog->getField(trim($aColumns[$i])),2,',','.');
					} elseif($aColumns[$i]=='FOTO')
					{
						// if($katalog->getField(trim($aColumns[$i])) > 0) {
						// $row[] = '<a href="'.base_url().'main/index/katalog_foto?reqId='.$katalog->getField('KATALOGID').'" class="badge badge-dark text-center" style="color:#fff; cursor: pointer; text-align:center">'.$katalog->getField(trim($aColumns[$i])).'</a>';
						// } else {
						// $row[] = '<a href="'.base_url().'main/index/katalog_foto?reqId='.$katalog->getField('KATALOGID').'" class="badge badge-danger text-center" style="color:#fff; cursor: pointer; text-align:center">'.$katalog->getField(trim($aColumns[$i])).'</a>';
						// }

						if($katalog->getField(trim($aColumns[$i])) > 0) {
						$row[] = '<span class="badge badge-dark text-center" style="color:#fff; text-align:center">'.$katalog->getField(trim($aColumns[$i])).'</span>';
						} else {
						$row[] = '<span class="badge badge-danger text-center" style="color:#fff; text-align:center">'.$katalog->getField(trim($aColumns[$i])).'</span>';
						}

					} elseif($aColumns[$i]=='KATALOG')
					{
						// if($katalog->getField(trim($aColumns[$i])) > 0) {
						// 	$row[] = '<a href="'.base_url().'main/index/katalog_lampiran?reqId='.$katalog->getField('KATALOGID').'" class="badge badge-dark text-center" style="color:#fff; cursor: pointer; text-align:center">'.$katalog->getField(trim($aColumns[$i])).'</a>';
						// } else {
						// 	$row[] = '<a href="'.base_url().'main/index/katalog_lampiran?reqId='.$katalog->getField('KATALOGID').'" class="badge badge-danger text-center" style="color:#fff; cursor: pointer; text-align:center">'.$katalog->getField(trim($aColumns[$i])).'</a>';
						// }

						if($katalog->getField(trim($aColumns[$i])) > 0) {
							$row[] = '<span class="badge badge-dark text-center" style="color:#fff; text-align:center">'.$katalog->getField(trim($aColumns[$i])).'</span>';
						} else {
							$row[] = '<span class="badge badge-danger text-center" style="color:#fff; text-align:center">'.$katalog->getField(trim($aColumns[$i])).'</span>';
						}
					}
					else
						$row[] = $katalog->getField(trim($aColumns[$i]));
						//$row[] = $katalog->getField($aColumns[$i]);
			}
			$output['aaData'][] = $row;
		}

		echo json_encode( $output );
	}

	function json_pejabat()
	{

		$this->load->model("Paket");
		$paket = new Paket();

		$aColumns 			= array('PAKET_ID','NAMA','NILAI_OWNER_ESTIMATE','STATUS','KODE_PR','PAKET_UUID');
		$aColumnsAlias		= array('PAKET_ID', 'NAMA','NILAI_OWNER_ESTIMATE','STATUS','KODE_PR','PAKET_UUID');

		if ( isset( $_GET['iSortCol_0'] ) )
		{
			$sOrder = " ORDER BY ";
			for ( $i=0 ; $i<intval( $_GET['iSortingCols'] ) ; $i++ )
			{
				if ( $_GET[ 'bSortable_'.intval($_GET['iSortCol_'.$i]) ] == "true" )
				{
					$sOrder .= $aColumnsAlias[ intval( $_GET['iSortCol_'.$i] ) ];
					if (strcasecmp(( $_GET['sSortDir_'.$i] ), "asc") == 1)
					{
						$sOrder .=" asc, ";
					}else
					{
						$sOrder .=" desc, ";
					}
				}
			}

			$sOrder = substr_replace( $sOrder, "", -2 );
			if ( trim($sOrder) == "ORDER BY NAMA desc" )
			{
				$sOrder = " ORDER BY A.NAMA ASC";

			}
		}

		$sWhere = "";
		$nWhereGenearalCount = 0;
		if (isset($_GET['sSearch']))
		{
			$sWhereGenearal = $_GET['sSearch'];
		}
		else
		{
			$sWhereGenearal = '';
		}

		if ( $_GET['sSearch'] != "" )
		{
			$sWhere = " AND (";
			for ( $i=0 ; $i<count($aColumnsAlias)+1 ; $i++ )
			{
				if ( $_GET['bSearchable_'.$i] == "true" )
				{
					$sWhere .= $aColumnsAlias[$i]." LIKE '%".$_GET['sSearch']."%' OR ";
					$nWhereGenearalCount += 1;
				}
			}
			$sWhere = substr_replace( $sWhere, "", -3 );
			$sWhere .= ')';
		}

		$sWhereSpecificArray = array();
		$sWhereSpecificArrayCount = 0;
		for ( $i=0 ; $i<count($aColumnsAlias) ; $i++ )
		{
			if ( $_GET['bSearchable_'.$i] == "true" && $_GET['sSearch_'.$i] != '' )
			{
				if ( $sWhere == "" )
				{
					$sWhere = "AND ";
				}
				else
				{
					$sWhere .= " AND ";
				}

				$sWhere .= $aColumnsAlias[$i]." LIKE '%' || :whereSpecificParam".$sWhereSpecificArrayCount." || '%' ";
				$sWhereSpecificArrayCount++;
				$sWhereSpecificArray[] =  $_GET['sSearch_'.$i];
			}
		}

		if ( $sWhere == "" )
		{
			$sWhere = " AND 1=1";
		}

		if ( isset( $_GET['iDisplayStart'] ))
		{
			$dsplyStart = $_GET['iDisplayStart'];
		}
		else{
			$dsplyStart = 0;
		}
		if ( isset( $_GET['iDisplayLength'] ) && $_GET['iDisplayLength'] != '-1' )
		{
			$dsplyRange = $_GET['iDisplayLength'];
			if ($dsplyRange > (2147483645 - intval($dsplyStart)))
			{
				$dsplyRange = 2147483645;
			}
			else
			{
				$dsplyRange = intval($dsplyRange);
			}
		}
		else
		{
			$dsplyRange = 2147483645;
		}

		$statement .= "AND (UPPER(A.NAMA) LIKE '%".strtoupper($_GET['sSearch'])."%' OR UPPER(KODE_PR) LIKE '%".strtoupper($_GET['sSearch'])."%') ";
    	$statement .= ' AND A.PAKET_METODE_LELANG_ID = \'6\' AND A.USER_LOGIN_ID = '.$this->USER_LOGIN_ID.'';
		$allRecord = $paket->getCountByParams(array(), $statement);
		if($_GET['sSearch'] == "")
			$allRecordFilter = $allRecord;
		else
			$allRecordFilter =  $paket->getCountByParams(array(), $statement);

		$paket->selectByParamsWithKatalog(array(), $dsplyRange, $dsplyStart, $statement, $sOrder);

		$output = array(
			"sEcho" => intval($_GET['sEcho']),
			"iTotalRecords" => $allRecord,
			"iTotalDisplayRecords" => $allRecordFilter,
			"aaData" => array()
		);

		while($paket->nextRow())
		{
			$row = array();
			for ( $i=0 ; $i<count($aColumns) ; $i++ )
			{
				if($aColumns[$i]=='NO') {
					$row[] = $number;
				} elseif($aColumns[$i]=='STATUS')
				{
				  if ($paket->getField("ALASAN") != "") {
					$row[] = '<span class="badge badge-danger"> Paket di Batalkan <br> Alasan: '.$paket->getField("ALASAN").' </span>';
				  } else
				  {
					switch ($paket->getField(trim($aColumns[$i]))) {
						case '0': $row[] = '<span class="badge badge-danger"> Proses Pemilihan </span>';
							break;
						case '1': $row[] = '<span class="badge badge-warning"> Negosiasi </span>';
							break;
						case '2': $row[] = '<span class="badge badge-dark"> Penyedia Setuju </span>';
							break;
						case '3': $row[] = '<span class="badge badge-info"> Surat Pesanan </span>';
							break;
						case '4': $row[] = '<span class="badge badge-primary"> Proses </span>';
							break;
						case '5': $row[] = '<span class="badge badge-primary"> Dikirim </span>';
							break;
						case '6': $row[] = '<span class="badge badge-primary"> Diterima </span>';
							break;

						default: $row[] = '-';
							break;
					}
				    }
				} elseif($aColumns[$i]=='NILAI_OWNER_ESTIMATE')
				{
					$row[] = number_format($paket->getField(trim($aColumns[$i])),2,',','.');
				}
				else {
					$row[] = $paket->getField(trim($aColumns[$i]));
				}

			}
			$output['aaData'][] = $row;
		}

		echo json_encode( $output );
	}

	function penawaran()
	{

		$this->load->model("Katalog");
		$katalog = new Katalog();

		$aColumns 			= array('PAKET_ID','STATUS_STR','NOINVOICE','NAMA_PAKET','TOTAL','STATUS');
		$aColumnsAlias		= array('PAKET_ID','STATUS_STR','NOINVOICE','NAMA_PAKET','TOTAL','STATUS');

		if ( isset( $_GET['iSortCol_0'] ) )
		{
			$sOrder = " ORDER BY ";

			//Go over all sorting cols
			for ( $i=0 ; $i<intval( $_GET['iSortingCols'] ) ; $i++ )
			{
				//If need to sort by current col
				if ( $_GET[ 'bSortable_'.intval($_GET['iSortCol_'.$i]) ] == "true" )
				{
					//Add to the order by clause
					$sOrder .= $aColumnsAlias[ intval( $_GET['iSortCol_'.$i] ) ];

					//Determine if it is sorted asc or desc
					if (strcasecmp(( $_GET['sSortDir_'.$i] ), "asc") == 1)
					{
						$sOrder .=" asc, ";
					}else
					{
						$sOrder .=" desc, ";
					}
				}
			}

			//Remove the last space / comma
			$sOrder = substr_replace( $sOrder, "", -2 );

			//Check if there is an order by clause
			if ( trim($sOrder) == "ORDER BY CREATED_DATE desc" )
			{
				$sOrder = " ORDER BY A.CREATED_DATE ASC";

			}
		}

		$sWhere = "";
		$nWhereGenearalCount = 0;
		if (isset($_GET['sSearch']))
		{
			$sWhereGenearal = $_GET['sSearch'];
		}
		else
		{
			$sWhereGenearal = '';
		}

		if ( $_GET['sSearch'] != "" )
		{
			//Set a default where clause in order for the where clause not to fail
			//in cases where there are no searchable cols at all.
			$sWhere = " AND (";
			for ( $i=0 ; $i<count($aColumnsAlias)+1 ; $i++ )
			{
				//If current col has a search param
				if ( $_GET['bSearchable_'.$i] == "true" )
				{
					//Add the search to the where clause
					$sWhere .= $aColumnsAlias[$i]." LIKE '%".$_GET['sSearch']."%' OR ";
					$nWhereGenearalCount += 1;
				}
			}
			$sWhere = substr_replace( $sWhere, "", -3 );
			$sWhere .= ')';
		}

		/* Individual column filtering */
		$sWhereSpecificArray = array();
		$sWhereSpecificArrayCount = 0;
		for ( $i=0 ; $i<count($aColumnsAlias) ; $i++ )
		{
			if ( $_GET['bSearchable_'.$i] == "true" && $_GET['sSearch_'.$i] != '' )
			{
				//If there was no where clause
				if ( $sWhere == "" )
				{
					$sWhere = "AND ";
				}
				else
				{
					$sWhere .= " AND ";
				}

				//Add the clause of the specific col to the where clause
				$sWhere .= $aColumnsAlias[$i]." LIKE '%' || :whereSpecificParam".$sWhereSpecificArrayCount." || '%' ";

				//Inc sWhereSpecificArrayCount. It is needed for the bind var.
				//We could just do count($sWhereSpecificArray) - but that would be less efficient.
				$sWhereSpecificArrayCount++;

				//Add current search param to the array for later use (binding).
				$sWhereSpecificArray[] =  $_GET['sSearch_'.$i];

			}
		}

		//If there is still no where clause - set a general - always true where clause
		if ( $sWhere == "" )
		{
			$sWhere = " AND 1=1";
		}

		//Bind variables.
		if ( isset( $_GET['iDisplayStart'] ))
		{
			$dsplyStart = $_GET['iDisplayStart'];
		}
		else{
			$dsplyStart = 0;
		}
		if ( isset( $_GET['iDisplayLength'] ) && $_GET['iDisplayLength'] != '-1' )
		{
			$dsplyRange = $_GET['iDisplayLength'];
			if ($dsplyRange > (2147483645 - intval($dsplyStart)))
			{
				$dsplyRange = 2147483645;
			}
			else
			{
				$dsplyRange = intval($dsplyRange);
			}
		}
		else
		{
			$dsplyRange = 2147483645;
		}

		// $statement .= " AND NOT A.USER_TYPE_ID = 6 ";
		 if($this->USER_TYPE_ID == "6") {
        	$statement .= ' AND A.REKANAN_ID = '.$this->ID.' ';
        }
		$allRecord = $katalog->getCountByParamsPenawaran(array(), $statement);
		$statement .= "AND (UPPER(A.NOINVOICE) LIKE '%".strtoupper($_GET['sSearch'])."%')";
		if($_GET['sSearch'] == "")
			$allRecordFilter = $allRecord;
		else
			$allRecordFilter =  $katalog->getCountByParamsPenawaran(array(), $statement);

        if($this->USER_TYPE_ID == "6") {
        	$statement .= ' AND A.REKANAN_ID = '.$this->ID.'';
			$katalog->selectByParamsPenawaran(array(), $dsplyRange, $dsplyStart, $statement, $sOrder);
		} else {
			$katalog->selectByParamsPenawaran(array(), $dsplyRange, $dsplyStart, $statement, $sOrder);
		}

		$output = array(
			"sEcho" => intval($_GET['sEcho']),
			"iTotalRecords" => $allRecord,
			"iTotalDisplayRecords" => $allRecordFilter,
			"aaData" => array()
		);

		while($katalog->nextRow())
		{
			$row = array();
			for ( $i=0 ; $i<count($aColumns) ; $i++ )
			{
				if($aColumns[$i]=='NO') {
					$row[] = $number;
				} elseif($aColumns[$i]=='STATUS')
				{
				  if ($katalog->getField("ALASAN") != "") {
					$row[] = '<span class="badge badge-danger"> Paket di Batalkan <br> Alasan: '.$katalog->getField("ALASAN").' </span>';
				  } else
				  {
					switch ($katalog->getField(trim($aColumns[$i]))) {
						case '0': $row[] = '<span class="badge badge-danger"> Proses Pemilihan </span>';
							break;
						case '1': $row[] = '<span class="badge badge-warning"> Negosiasi </span>';
							break;
						case '2': $row[] = '<span class="badge badge-dark"> Penyedia Setuju </span>';
							break;
						case '3': $row[] = '<span class="badge badge-info"> Surat Pesanan </span>';
							break;
						case '4': $row[] = '<span class="badge badge-primary"> Proses </span>';
							break;
						case '5': $row[] = '<span class="badge badge-primary"> Dikirim </span>';
							break;
						case '6': $row[] = '<span class="badge badge-primary"> Diterima </span>';
							break;

						default: $row[] = '-';
							break;
					}
				    }
				} else
				{
					$row[] = $katalog->getField(trim($aColumns[$i]));
				}
			}
			$output['aaData'][] = $row;
		}

		echo json_encode( $output );
	}

	function add()
	{
		// echo "<pre>";
		// print_r($this->input->post()); die();
		/* INCLUDE FILE */
		$this->load->library("kauth");  $userLogin = new kauth();
		$this->load->model("Katalog");
		$this->load->model("KatalogKategoriRekanan");

		/* create objects */
		$katalog = new Katalog();
		$katalog_riwayat_harga = new Katalog();

		/* VARIABLE */
		$reqId	= $this->input->post("reqId");
		$reqMode	= $this->input->post("reqMode");

		$reqNoproduk	= $this->input->post("reqNoproduk");
		$reqNamaproduk 	= $this->input->post("reqNamaproduk");
		$reqHarga		= $this->input->post("reqHarga");
		$reqHargaold	= $this->input->post("reqHargaold");
		$reqMerek		= $this->input->post("reqMerek");
		$reqModeltype	= $this->input->post("reqModeltype");
		$reqDiameter	= $this->input->post("reqDiameter") ? $this->input->post("reqDiameter") : 0;
		$reqPanjang		= $this->input->post("reqPanjang") ? $this->input->post("reqPanjang") : 0;
		$reqLebar		= $this->input->post("reqLebar") ? $this->input->post("reqLebar") : 0;
		$reqTinggi		= $this->input->post("reqTinggi") ? $this->input->post("reqTinggi") : 0;
		$reqUnitpengukuran    	= $this->input->post('reqUnitpengukuran');
		$reqTkdn   		= $this->input->post('reqTkdn');
		$reqBerlakusampai   = $this->input->post('reqBerlakusampai') ? $this->input->post('reqBerlakusampai') : NULL;
		$reqJenisproduk   	= $this->input->post('reqJenisproduk');
		$reqLamaGaransi   	= $this->input->post('reqLamaGaransi');
		$reqLamaGaransi2   	= $this->input->post('reqLamaGaransi2');
		$reqJumlahstock   	= $this->input->post('reqJumlahstock') ?: 0;
		$reqjumlahstockready   	= $this->input->post('reqjumlahstockready') ?: '';
		$reqKemasan   		= $this->input->post('reqKemasan');
		$reqStatus   		= $this->input->post('reqStatus');
		$reqKeteranganTambahan   = str_replace("'","''",$_POST['reqKeteranganTambahan']);
		$reqKomoditas   	= $_POST["reqKomoditas"];
		$reqUserId   		= $this->ID;

		$katalog->setField("KATALOGID",$reqId);
		$katalog->setField("NOPRODUK",$reqNoproduk);
		$katalog->setField("NAMAPRODUK",$reqNamaproduk);
		$katalog->setField("HARGA",CommaToDot(dotToNo($reqHarga)));
		$katalog->setField("MEREK",$reqMerek);
		$katalog->setField("MODELTYPE",$reqModeltype);
		$katalog->setField("DIAMETER",$reqDiameter);
		$katalog->setField("PANJANG",$reqPanjang);
		$katalog->setField("LEBAR",$reqLebar);
		$katalog->setField("TINGGI",$reqTinggi);
		$katalog->setField("UNITPENGUKURAN",$reqUnitpengukuran);
		$katalog->setField("TKDNPRODUK",$reqTkdn);
		$katalog->setField("BERLAKUSAMPAI",dateToDBCheck($reqBerlakusampai));
		$katalog->setField('JENISPRODUK', $reqJenisproduk);
		$katalog->setField('LAMAGARANSI', $reqLamaGaransi);
		$katalog->setField('LAMAGARANSI2', $reqLamaGaransi2);
		$katalog->setField('JUMLAHSTOCK', $reqJumlahstock);
		$katalog->setField('JUMLAHSTOCK_READY', $reqjumlahstockready);
		$katalog->setField('KEMASAN', $reqKemasan);
		$katalog->setField('STATUS', $reqStatus);
		$katalog->setField('KETERANGANTAMBAHAN', $reqKeteranganTambahan);
		$katalog->setField('CREATED_BY', $reqUserId);


		if($reqMode == "insert")
		{
			if($katalog->insert()) {
				$katalogid = $katalog->id;
				for($i=0; $i<count($reqKomoditas);$i++)
	      {
	        $katalog_kategori_rekanan = new KatalogKategoriRekanan();
	        $katalog_kategori_rekanan->setField('KATEGORI_ID', $reqKomoditas[$i]);
	        $katalog_kategori_rekanan->setField('KATALOGID', $katalogid);
		  		$katalog_kategori_rekanan->setField('CREATED_BY', $reqUserId);
	        $katalog_kategori_rekanan->insert();
	        unset($katalog_kategori_rekanan);
	      }

				$arrJson["message"] = "sukses";
				$arrJson["pesan"] = "Data berhasil di simpan";
			} else
			{
				$arrJson["message"] = "gagal";
				$arrJson["pesan"] = "Data gagal di simpan";
			}
		}
		else
		{
			$katalog_kategori_rekanan_del = new KatalogKategoriRekanan();
	        $katalog_kategori_rekanan_del->setField('KATALOGID', $reqId);
	        $katalog_kategori_rekanan_del->delete();
	        unset($katalog_kategori_rekanan_del);

	        for($i=0; $i<count($reqKomoditas);$i++)
	        {
	          $katalog_kategori_rekanan = new KatalogKategoriRekanan();
	          $katalog_kategori_rekanan->setField('KATEGORI_ID', $reqKomoditas[$i]);
	          $katalog_kategori_rekanan->setField('KATALOGID', $reqId);
			  $katalog_kategori_rekanan->setField('CREATED_BY', $reqUserId);
	          $katalog_kategori_rekanan->insert();
	          unset($katalog_kategori_rekanan);
	        }
	        $reqHargaold = explode('.', $reqHargaold);
	        $reqHarga 	 = str_replace('.', '', $reqHarga);
	        // 	echo $reqHarga.'-'.$reqHargaold[0];
	        if ($reqHarga == $reqHargaold[0]) {
	        } else {
				$katalog_riwayat_harga->setField("HARGALAMA",CommaToDot(dotToNo($reqHargaold[0])));
				$katalog_riwayat_harga->setField("HARGABARU",CommaToDot(dotToNo($reqHarga)));
			  	$katalog_riwayat_harga->setField('CREATED_BY', $reqUserId);
	        	$katalog_riwayat_harga->setField('KATALOGID', $reqId);
	        	$katalog_riwayat_harga->insertRiwayatHarga();
	        }

				if($katalog->update()) {
					$arrJson["message"] = "sukses";
					$arrJson["pesan"] = "Data berhasil di update";
				} else
				{
					$arrJson["message"] = "gagal";
					$arrJson["pesan"] = "Data gagal di update, silahkan coba kembali";
				}
		}

		echo json_encode($arrJson);
	}

	function addLaporan()
	{
		$this->load->library("kauth");  $userLogin = new kauth();
		$this->load->model("Katalog");

		/* create objects */
		$katalog = new Katalog();

		/* VARIABLE */
		$reqKatalogid		= $this->input->post("reqKatalogid");
		$reqJenisLaporan	= $this->input->post("reqJenisLaporan");
		$reqNama			= $this->input->post("reqNama");
		$reqEmail			= $this->input->post("reqEmail");
		$reqTelepon			= $this->input->post("reqTelepon");
		$reqAlasan			= $this->input->post("reqAlasan");

		$katalog->setField("KATALOGID",$reqKatalogid);
		$katalog->setField("NAMA",$reqNama);
		$katalog->setField("EMAIL",$reqEmail);
		$katalog->setField("TELEPON",$reqTelepon);
		$katalog->setField("ALASAN",$reqAlasan);
		$katalog->setField("JENISLAPORAN",$reqJenisLaporan);
		$katalog->setField('BROWSER', $_SERVER['HTTP_USER_AGENT']);

		if($katalog->insertLaporan())
			echo "Laporan anda terkirim, terima kasih atas laporan nya.";

	}

	function ubah_status()
	{
		$this->load->model("Katalog");

		$katalog = new Katalog();

		/* json set variable */
		$reqId =  $this->input->get('reqId');

		$katalog->selectByParams(array("KATALOGID"=>$reqId),-1,-1);
		$katalog->firstRow();
		$tmpStatus = $katalog->getField("STATUS");

		if($tmpStatus == 1)
			$tmpStatus = 0;
		else
			$tmpStatus = 1;

		$user = new Katalog();
		$user->setField("KATALOGID", $reqId);
		$user->setField("STATUS", $tmpStatus);

		if($user->update_status())
			$arrJson["PESAN"] = "Status berhasil di ubah";
		else
			$arrJson["PESAN"] = "Status gagal di ubah";

		echo json_encode($arrJson);

	}

	function delete()
	{
		$this->load->model("Katalog");
		$katalog = new Katalog();
		$reqId =  $this->input->get('reqId');
		$katalog->setField('KATALOGID', $reqId);
		// if($katalog->delete())
		if($katalog->deleteAll())
			echo "Data berhasil dihapus";
		else
			echo "Data gagal dihapus";
	}

	function delkatrek()
	{
		$this->load->model("Katalogrekanan");
		$katalogrekanan = new Katalogrekanan();
	    $reqId = $this->input->post("reqId");
	    $paketid = $this->input->post("paketid");
		$katalogrekanan->setField('KATALOGREKANANID', $reqId);
		$katalogrekanan->setField('PAKET_ID', $paketid);
		if($katalogrekanan->deleteKatalogRekanan()) {
			echo "Sukses||Data berhasil dihapus";
			// Insert Rekam Jejak
	        $this->load->library("librekamjejak");
	        $this->librekamjejak->insertRJ('355','',$paketid,'null','355');
	        // End Insert Rekam Jejak
		}
		else {
			echo "Gagal||Data gagal dihapus";
		}
	}

	function katalog_paging()
  	{

	    $this->load->model('Katalog');
	    $this->load->model('Katalogfoto');
	    $this->load->model("Katalogkategori");
		$this->load->model("Katalogcompare");
	    $this->load->library("Pagination");


	    $reqName = $this->input->post("name");
	    $reqPage = $this->input->post("page");
	    $reqPencarian = $this->input->post("search");
	    // $reqShow = $this->input->post("show");
	    $reqShowGabung = explode("||", $this->input->post("show"));
	    $reqShow = $reqShowGabung[0];
	    $name = $reqShowGabung[1];
	    $subKaetgoriLabel = $reqShowGabung[2];
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
	      $statement= " AND (UPPER(A.NAMAPRODUK) LIKE '%".strtoupper($reqPencarian)."%') ";
	      if ($subKaetgoriLabel != '-') {
            $katalog_kategori_url->selectByParams(array(), -1, -1, " AND A.URL3 = '".$subKaetgoriLabel."' AND A.KATEGORI_PARENT_ID != '0' ");
            $katalog_kategori_url->firstRow();
            $id = $katalog_kategori_url->getField("KATEGORI_ID");

            $reqArrStatement = array('A.KATEGORI_ID' => $id);
            $katalog->selectByParamsViewKatalogByKategori($reqArrStatement, $dsplyRange, $dsplyStart, $statement);
            $rowCount = $katalog_count->getCountByParamsViewKatalogByKategori($reqArrStatement, $statement);
          } else {
          	$katalog_kategori_url->selectByParams(array(), -1, -1, " AND A.URL = '".$name."' AND A.KATEGORI_PARENT_ID = '0' ");
			$katalog_kategori_url->firstRow();
			$id = $katalog_kategori_url->getField("KATEGORI_ID");

            $reqArrStatement = array('A.KATEGORI_PARENT_ID' => $id);
            $katalog->selectByParamsViewKatalogByKategori2($reqArrStatement, $dsplyRange, $dsplyStart, $statement);
            $rowCount = $katalog_count->getCountByParamsViewKatalogByKategori2($reqArrStatement, $statement);
          }
          // echo $katalog->query; die();
          // echo $id;
          $arrSerialized = serialize($statement);
          $arrSerialized = str_replace('"', '@', $arrSerialized);
          // $pagConfig = array('baseURL'=>$pageView, 'showRecord' => '\''.$showRecord.'||'.$name.'||'.$subKaetgoriLabel.'\'', 'totalRows'=>$rowCount, 'perPage'=>$showRecord, 'contentDiv'=>'tbodyKatalog', 'arrSerialized' => $arrSerialized, 'searchVarible' => "reqPencarian");
      		$pagConfig = array('baseURL'=>'katalog_json/katalog_paging', 'showRecord' => '\''.$reqShow.'||'.$name.'||'.$subKaetgoriLabel.'\'', 'totalRows'=>$rowCount, 'currentPage'=>$dsplyStart, 'perPage'=>$dsplyRange, 'contentDiv'=>$reqContent, 'searchText' => $reqPencarian, 'arrSerialized' => $this->input->post("array_serialized"));

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
                      <img class="card-img-top img-fluid" src="images/katalog/<?= $filenya ?>" alt="eproc19.com">
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
                          <!-- <div class="fb-share-button" data-href="https://eproc.paljaya.com" data-layout="button" data-size="large"> -->
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
	      <?php
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

	function cart()
	{
		session_start();
		$this->load->library("kauth");  $userLogin = new kauth();
		$this->load->model("Katalogrekanan");
		$this->load->model("Katalog");
		/* create objects */
		$katalogrekanan = new Katalogrekanan();
		$katalogrekananGetData = new Katalogrekanan();
		$katalogrekananTotal = new Katalogrekanan();
		$katalogrekananTotalCek = new Katalogrekanan();
		$katalog = new Katalog();

		/* VARIABLE */
		$katalogid	= $this->input->post("katalog");
		$paketid	= $this->input->post("paket");

		$katalog->selectByParams(array(), -1, -1, " AND A.KATALOGID = '".$katalogid."' ");
		$katalog->firstRow();

		$cekQty = $katalogrekananTotalCek->getCountByParams(array('A.PAKET_ID' => $paketid, "A.KATALOGID" => $katalogid));

		$reqUserId   = $this->USER_LOGIN_ID;

		$katalogrekanan->setField('KATALOGID', $katalogid);
		$katalogrekanan->setField('PAKET_ID', $paketid);
		$katalogrekanan->setField('NAMAPRODUK', $katalog->getField("NAMAPRODUK"));
		$katalogrekanan->setField('MEREK', $katalog->getField("MEREK"));
		$katalogrekanan->setField('MODELTYPE', $katalog->getField("MODELTYPE"));
		$katalogrekanan->setField('HARGA', $katalog->getField("HARGA"));
		$katalogrekanan->setField('REKANAN_ID', $katalog->getField("REKANAN_ID"));
		$katalogrekanan->setField('CREATED_BY', $reqUserId);
		$katalogrekanan->setField('BROWSER', $_SERVER['HTTP_USER_AGENT']);
		$katalogrekanan->setField('QTY', '1');
		$katalogrekanan->setField('STATUS', '0');

		if ($cekQty > 0) { // update QTY
			$katalogrekananGetData->selectByParams(array(), -1, -1, " AND A.KATALOGID = '".$katalogid."' AND A.PAKET_ID = '".$paketid."' ");
			$katalogrekananGetData->firstRow();
			$qtyTambah = $katalogrekananGetData->getField('QTY') + 1;
			$katalogrekanan->setField('QTYUPDATE', $qtyTambah);
			if($katalogrekanan->updateQty())
	    		$cekTotal = $katalogrekananTotal->getCountByParams(array('A.PAKET_ID' => $paketid));
				echo "Sukses||Katalog ".$katalog->getField("NAMAPRODUK")." berhasil di Tambah||".$cekTotal;
		} else {
			if($katalogrekanan->insert())
	    		$cekTotal = $katalogrekananTotal->getCountByParams(array('A.PAKET_ID' => $paketid));
				echo "Sukses||Katalog ".$katalog->getField("NAMAPRODUK")." berhasil di Tambah||".$cekTotal;
		}

	}

	function cartupdate()
	{
		session_start();
		$this->load->library("kauth");  $userLogin = new kauth();
		$this->load->model("Katalogrekanan");
		$this->load->model("Katalog");
		/* create objects */
		$katalogrekanan = new Katalogrekanan();
		$katalogrekananGetData = new Katalogrekanan();
		$katalog = new Katalog();

		/* VARIABLE */
		$katalogid	= $this->input->post("katalog");
		$paketid	= $this->input->post("paket");
		$qty	= $this->input->post("qty");

		$katalog->selectByParams(array(), -1, -1, " AND A.KATALOGID = '".$katalogid."' ");
		$katalog->firstRow();

		$reqUserId   = $this->USER_LOGIN_ID;

		$katalogrekanan->setField('KATALOGID', $katalogid);
		$katalogrekanan->setField('PAKET_ID', $paketid);
		$katalogrekanan->setField('CREATED_BY', $reqUserId);

		$qtyTambah = $qty;
		$katalogrekanan->setField('QTYUPDATE', $qtyTambah);
		if($katalogrekanan->updateQty())
			echo "Sukses||Qty ".$katalog->getField("NAMAPRODUK")." berhasil di Update";
	}

	function cartupdateNego()
	{
		session_start();
		$this->load->library("kauth");  $userLogin = new kauth();
		$this->load->model("Katalogrekanan");
		$this->load->model("Katalog");
		$this->load->model("Kataloglogistik");
		/* create objects */
		$katalogrekanan = new Katalogrekanan();
		$katalogrekananGetData = new Katalogrekanan();
		$kataloglogistik = new Kataloglogistik();
		$katalog = new Katalog();

		/* VARIABLE */
		$reqKatalogrekanan	= $this->input->post("reqKatalogrekanan");
		$reqHargaNego	= $this->input->post("reqHargaNego");
		$reqId	= $this->input->post("reqId");
		$reqOngkosKirim	= $this->input->post("reqOngkosKirim");
		// echo "<pre>"; print_r($reqKatalogrekanan); die();

		$reqUserId   = $this->USER_LOGIN_ID;

		// update ongkos kirim
		$kataloglogistik->setField('PAKET_ID', $reqId);
		$kataloglogistik->setField('UPDATED_BY', $reqUserId);
		$kataloglogistik->setField('ONGKOS_KIRIM', CommaToDot(dotToNo($reqOngkosKirim)));
		$kataloglogistik->updateongkir();
		// end update ongkos kirim


		$katalogrekanan->setField('CREATED_BY', $reqUserId);
		foreach ($reqHargaNego as $key => $value) {
			$katalogrekanan->setField('HARGA_NEGO', CommaToDot(dotToNo($value)));
			$katalogrekanan->setField('KATALOGREKANANID', $reqKatalogrekanan[$key]);
			$katalogrekanan->updateHargaNego();
		}

		echo "Sukses||Harga Negosiasi berhasil di kirim ke Penyedia";
	}

	function statusupdate()
	{
		session_start();
		$this->load->library("kauth");  $userLogin = new kauth();
		$this->load->model("Katalogrekanan");
		$this->load->model("Katalog");
		$this->load->model("Kataloglogistik");
		/* create objects */
		$katalogrekanan = new Katalogrekanan();
		$katalogrekananGetData = new Katalogrekanan();
		$kataloglogistik = new Kataloglogistik();
		$katalog = new Katalog();

		/* VARIABLE */
		$katalogid	= $this->input->post("katalog");
		$paketid	= $this->input->post("paket");
		$katalogrekananid	= $this->input->post("katalogrekanan");
		$status	= $this->input->post("status");

		$katalog->selectByParams(array(), -1, -1, " AND A.KATALOGID = '".$katalogid."' ");
		$katalog->firstRow();

		$reqUserId   = $this->USER_LOGIN_ID;

		$katalogrekanan->setField('KATALOGID', $katalogid);
		$katalogrekanan->setField('PAKET_ID', $paketid);
		$katalogrekanan->setField('CREATED_BY', $reqUserId);
		$katalogrekanan->setField('UPDATED_BY', $reqUserId);
		switch ($status) {
			case '10':
				$updateStatus = '0';
				$statusMessage = 'Ulangi Proses Pemilihan Produk';

				// hapus ke table katalog_logistik
				$kataloglogistik->setField('PAKET_ID', $paketid);
				$kataloglogistik->setField('CREATED_BY', $reqUserId);
				$kataloglogistik->deleteKatalogLogistik();
				// end hapus ke table katalog_logistik

				// Insert Rekam Jejak
		        $this->load->library("librekamjejak");
		        $this->librekamjejak->insertRJ('356','',$paketid,'null','356');
		        // End Insert Rekam Jejak

				break;
			case '0':
				$updateStatus = '1';
				$statusMessage = 'Silahkan melakukan Negosiasi dengan Penyedia';

				// input ke table katalog_logistik
				$kataloglogistik->setField('PAKET_ID', $paketid);
				$kataloglogistik->setField('CREATED_BY', $reqUserId);
				$kataloglogistik->insert();
				// end input ke table katalog_logistik

				// Insert Rekam Jejak
		        $this->load->library("librekamjejak");
		        $this->librekamjejak->insertRJ('350','',$paketid,'null','350');
		        // End Insert Rekam Jejak

				break;
			case '1':
				$updateStatus = '2';
				$statusMessage = 'Penyedia telah menyetujui Negosiasi';

				// Insert Rekam Jejak
		        $this->load->library("librekamjejak");
		        $this->librekamjejak->insertRJ('351','',$paketid,'null','351');
		        // End Insert Rekam Jejak

				break;
			case '2':
				$updateStatus = '3';
				$statusMessage = 'Upload Surat Pesanan.';

				// Insert Rekam Jejak
		        $this->load->library("librekamjejak");
		        $this->librekamjejak->insertRJ('352','',$paketid,'null','352');
		        // End Insert Rekam Jejak

				break;
			case '3':
				$updateStatus = '4';
				$statusMessage = 'Pesanan Diproses.';

				// Insert Rekam Jejak
		        $this->load->library("librekamjejak");
		        $this->librekamjejak->insertRJ('353','',$paketid,'null','353');
		        // End Insert Rekam Jejak

				break;
			case '5':
				$updateStatus = '6';
				$statusMessage = 'Pesanan Diterima.';

				// Insert Rekam Jejak
		        $this->load->library("librekamjejak");
		        $this->librekamjejak->insertRJ('354','',$paketid,'null','354');
		        // End Insert Rekam Jejak

				break;
			default:
				break;
		}

		if ($status == '0' || $status == '1') { // Jika di nego setujui update dengan nomor invoice
			$noinvoice = $this->generateInvoice($paketid);

			$katalogrekanan->setField('STATUS', $updateStatus);
			$katalogrekanan->setField('STATUSAWAL', $status);
			$katalogrekanan->setField('NOINVOICE', $noinvoice);

			if($katalogrekanan->updateStatus()) {
				echo "Sukses||".$statusMessage;
			}
		} else if ($status == '10') {
			$katalogrekanan->setField('STATUS', $updateStatus);
			$katalogrekanan->setField('STATUSAWAL', '1');

			if($katalogrekanan->updateStatusAja()) {
				echo "Sukses||".$statusMessage;
			}

		} else {
			$katalogrekanan->setField('STATUS', $updateStatus);
			$katalogrekanan->setField('STATUSAWAL', $status);

			if($katalogrekanan->updateStatusAja()) {
				echo "Sukses||".$statusMessage;
			}
		}
	}

	function generateInvoice($paketid)
	{
		$kataAwal = 'INV-PR';
		$date = date('ymd');
		$acak = $this->incrementalHash();

		return $kataAwal.'/'.$date.'/'.$acak.$paketid;
	}

	function incrementalHash(){
	  	$seed = str_split('abcdefghijklmnpqrstuvwxyz'
                 .'ABCDEFGHJKLMNPQRSTUVWXYZ'
                 .'123456789'); // and any other characters
		shuffle($seed); // probably optional since array_is randomized; this may be redundant
		$rand = '';
		foreach (array_rand($seed, 4) as $k) $rand .= $seed[$k];

		return $rand;
	}

	function publish()
	{
		session_start();
		$this->load->library("kauth");  $userLogin = new kauth();
		$this->load->model("Katalog");
		/* create objects */
		$katalog = new Katalog();
		$katalog_publish = new Katalog();
		$katalog_total = new Katalog();

		/* VARIABLE */
		$name	= $this->input->post("name");
		$value	= $this->input->post("value");
		$check	= $this->input->post("check");
		$rekanan_id	= $this->input->post("rek");


		$reqUserId   = $this->USER_LOGIN_ID;

		$katalog->setField('KATALOGID', $value);
		$katalog->setField('CREATED_BY', $reqUserId);
		$katalog->setField('PUBLISH_BY', $reqUserId);

		if ($check == '1') { // Publish
			$katalog->setField('PUBLISH', '1');
			$katalog->updatePublish();

			$katalog_total->selectByParamsViewKatalog2(array(), -1, -1, " AND A.REKANAN_ID = '".$rekanan_id."'");
			$katalog_total->firstRow();
			$katalogTotal = $katalog_total->getField('total_katalog');
			$katalog_publish->selectByParamsViewKatalog2(array(), -1, -1, " AND A.REKANAN_ID = '".$rekanan_id."' AND A.PUBLISH = '1'");
			$katalog_publish->firstRow();
			$katalogPublish = $katalog_publish->getField('total_katalog');
			$katalogNonverified = $katalogTotal - $katalogPublish;
			echo "Sukses||Data ".$name." berhasil di Publish||".$katalogPublish."||".$katalogNonverified;
		} else {
			$katalog->setField('PUBLISH', '0');
			$katalog->updatePublish();

			$katalog_total->selectByParamsViewKatalog2(array(), -1, -1, " AND A.REKANAN_ID = '".$rekanan_id."'");
			$katalog_total->firstRow();
			$katalogTotal = $katalog_total->getField('total_katalog');
			$katalog_publish->selectByParamsViewKatalog2(array(), -1, -1, " AND A.REKANAN_ID = '".$rekanan_id."' AND A.PUBLISH = '1'");
			$katalog_publish->firstRow();
			$katalogPublish = $katalog_publish->getField('total_katalog');
			$katalogNonverified = $katalogTotal - $katalogPublish;
			echo "Sukses||Data ".$name." batal Publish||".$katalogPublish."||".$katalogNonverified;
		}

	}

	function negoshoutbox()
	{
		$this->load->model("NegoShoutbox");
		$nego_shoutbox = new NegoShoutbox();

		$time = time();
		$reqUserId   = $this->USER_LOGIN_ID;
		$pesan	= $_POST['reqPesanNego'];
		$reqId	= $_POST['reqId'];

		$nego_shoutbox->setField("JAM", $time);
		$nego_shoutbox->setField("NAMA", $this->USER_NAMA);
		$nego_shoutbox->setField("PESAN", formatTextToDb($pesan));
		$nego_shoutbox->setField("IP_ADDRESS", $_SERVER['REMOTE_ADDR']);
		$nego_shoutbox->setField("PAKET_ID", $reqId);
		$nego_shoutbox->setField("REKANAN_ID", ValToNullDB($this->ID));
		$nego_shoutbox->insert2();
	}

	function chatNegoBox()
	{
		$this->load->model("NegoShoutbox");
		$nego_shoutbox = new NegoShoutbox();
		$reqId	= $_GET['reqId'];

		$nego_shoutbox->selectByParams(array(), -1, -1, " AND A.PAKET_ID = '".$reqId."'");
		$html 	= '-';
		while($nego_shoutbox->nextRow())
		{
			$nama = $nego_shoutbox->getField('NAMA');
			$pesan = $nego_shoutbox->getField('PESAN');
			$waktu = $nego_shoutbox->getField('WAKTU');
			$html 		.= '<div class="direct-chat-info clearfix" style="margin-top:2px; margin-bottom:-6px">
					    		<span class="direct-chat-name pull-left"><small>'.$nama.'</small></span>
					      </div>
					      <div class="direct-chat-text">
					        '.$pesan.'
					      </div>
					      <div class="direct-chat-info clearfix">
					        <span class="direct-chat-timestamp pull-right">'.$waktu.'</span>
					      </div>';

		}



		require_once('lib/JSON.php');
		$json = new Services_JSON();
		$out = $json->encode($html);
		// return $out;
		print $out;
	}

	function getstatus()
	{
		$this->load->model("Katalogrekanan");
		$katalogrekanan = new Katalogrekanan();
		$reqId	= $_GET['reqId'];

		$katalogrekanan->selectByParams(array(), -1, -1, " AND A.PAKET_ID = '".$reqId."'");
		$katalogrekanan->firstRow();

		$html = $katalogrekanan->getField('STATUS');

		require_once('lib/JSON.php');
		$json = new Services_JSON();
		$out = $json->encode($html);
		// return $out;
		print $out;
	}

	function downloadDoc()
	{
		header("Content-Type: application/vnd.ms-word");
		header("Expires: 0");
		header("Cache-Control:  must-revalidate, post-check=0, pre-check=0");
		header("Content-disposition: attachment; filename=\"mydocument_name.doc\"");
		$mydata = 'hai';
		$output = $this->load->view("myreport", $mydata);
		echo $data;
		exit;
	}

	function uploadsp()
	{
		$this->load->model("Katalogrekanan");
		$this->load->model("Kataloglogistik");
		$this->load->library("FileHandler");

		$Kataloglogistik = new Kataloglogistik();
		$katalogrekanan = new Katalogrekanan();
		$file = new FileHandler();

		$reqId = $this->input->post("reqId");
		$reqLinkFile= $_FILES['reqLinkFile'];
		$FILE_DIR = "images/katalog/";

		$renameFile = md5(date("dmYHis").$reqLinkFile['name'].$this->USER_LOGIN_ID).".".getExtension($reqLinkFile['name']);
		if($file->uploadToDir('reqLinkFile'.$reqDokumenKe, $FILE_DIR, $renameFile))
		{
			$Kataloglogistik->setField("UPDATED_BY", $this->USER_LOGIN_ID);
			$Kataloglogistik->setField("PAKET_ID", $reqId);
			$Kataloglogistik->setField("FILE_SURAT_PESANAN", $reqLinkFile['name']);
			$Kataloglogistik->setField("PATH_FILE_SURAT_PESANAN", $file->uploadedFileName);
			$Kataloglogistik->updateSP();

			// update status katalog_rekanan
			$katalogrekanan->setField("PAKET_ID", $reqId);
			$katalogrekanan->setField('STATUSAWAL', '2');
			$katalogrekanan->setField('STATUS', '3');
			$katalogrekanan->setField("UPDATED_BY", $this->USER_LOGIN_ID);
			$katalogrekanan->updateStatusAja();

			echo "Surat Pesanan berhasil diupload.";

			// Insert Rekam Jejak
	        $this->load->library("librekamjejak");
	        $this->librekamjejak->insertRJ('352','',$reqId,'null','352');
	        // End Insert Rekam Jejak
		}
		else
			echo "Surat Pesanan gagal diupload.";
	}

	function prosespesanan()
	{
		$this->load->model("Katalogrekanan");
		$this->load->model("Kataloglogistik");
		$this->load->library("FileHandler");

		$Kataloglogistik = new Kataloglogistik();
		$katalogrekanan = new Katalogrekanan();
		$file = new FileHandler();

		$reqId = $this->input->post("reqId");
		$reqEstimasiSampai = $this->input->post("reqEstimasiSampai");
		$reqLinkFile= $_FILES['reqLinkFile'];
		$FILE_DIR = "images/katalog/";

		$renameFile = md5(date("dmYHis").$reqLinkFile['name'].$this->USER_LOGIN_ID).".".getExtension($reqLinkFile['name']);
		if($file->uploadToDir('reqLinkFile'.$reqDokumenKe, $FILE_DIR, $renameFile))
		{
			$Kataloglogistik->setField("UPDATED_BY", $this->USER_LOGIN_ID);
			$Kataloglogistik->setField("PAKET_ID", $reqId);
			$Kataloglogistik->setField("ESTIMASI_SAMPAI", dateToDBCheck($reqEstimasiSampai));
			$Kataloglogistik->setField("FILE_BUKTI_KIRIM", $reqLinkFile['name']);
			$Kataloglogistik->setField("PATH_FILE_BUKTI_KIRIM", $file->uploadedFileName);
			$Kataloglogistik->updateBuktiKirim();

			// update status katalog_rekanan
			$katalogrekanan->setField("PAKET_ID", $reqId);
			$katalogrekanan->setField('STATUSAWAL', '4');
			$katalogrekanan->setField('STATUS', '5');
			$katalogrekanan->setField("UPDATED_BY", $this->USER_LOGIN_ID);
			$katalogrekanan->updateStatusAja();

			// Insert Rekam Jejak
	        $this->load->library("librekamjejak");
	        $this->librekamjejak->insertRJ('357','',$reqId,'null','357');
	        // End Insert Rekam Jejak

			echo "Pesanan Dikirim.";
		}
		else
			echo "Pesanan Dikirim gagal.";
    }

    function terimapesanan()
	{
		$this->load->model("Katalogrekanan");
		$this->load->model("Kataloglogistik");
		$this->load->library("FileHandler");

		$Kataloglogistik = new Kataloglogistik();
		$katalogrekanan = new Katalogrekanan();
		$file = new FileHandler();

		$reqId = $this->input->post("reqId");
		$reqLinkFile= $_FILES['reqLinkFile'];
		$FILE_DIR = "images/katalog/";

		$renameFile = md5(date("dmYHis").$reqLinkFile['name'].$this->USER_LOGIN_ID).".".getExtension($reqLinkFile['name']);
		if($file->uploadToDir('reqLinkFile'.$reqDokumenKe, $FILE_DIR, $renameFile))
		{
			$Kataloglogistik->setField("UPDATED_BY", $this->USER_LOGIN_ID);
			$Kataloglogistik->setField("PAKET_ID", $reqId);
			$Kataloglogistik->setField("FILE_BUKTI_TERIMA", $reqLinkFile['name']);
			$Kataloglogistik->setField("PATH_FILE_BUKTI_TERIMA", $file->uploadedFileName);
			$Kataloglogistik->updateBuktiTerima();

			// update status katalog_rekanan
			$katalogrekanan->setField("PAKET_ID", $reqId);
			$katalogrekanan->setField('STATUSAWAL', '5');
			$katalogrekanan->setField('STATUS', '6');
			$katalogrekanan->setField("UPDATED_BY", $this->USER_LOGIN_ID);
			$katalogrekanan->updateStatusAja();

			// Insert Rekam Jejak
	        $this->load->library("librekamjejak");
	        $this->librekamjejak->insertRJ('358','',$reqId,'null','358');
	        // End Insert Rekam Jejak

			echo "Pesanan Sudah Diterima.";
		}
		else
			echo "Pesanan Gagal Diterima.";
    }

    function uploadpernyataan()
	{
		$this->load->model("Katalogsuratpernyataan");
		$this->load->library("FileHandler");

		$Katalogsuratpernyataan = new Katalogsuratpernyataan();
		$file = new FileHandler();

		$reqLinkFile= $_FILES['reqLinkFile'];
		$FILE_DIR = "uploads/vms/surat_pernyataan/";

		$renameFile = md5(date("dmYHis").$reqLinkFile['name'].$this->USER_LOGIN_ID).".".getExtension($reqLinkFile['name']);
		if($file->uploadToDir('reqLinkFile'.$reqDokumenKe, $FILE_DIR, $renameFile))
		{
			$Katalogsuratpernyataan->setField("FILE_SP", $reqLinkFile['name']);
			$Katalogsuratpernyataan->setField("PATH_SP", $file->uploadedFileName);
			$Katalogsuratpernyataan->setField("CREATED_BY", $this->USER_LOGIN_ID);
			$Katalogsuratpernyataan->insert();

			echo "Surat Pernyataan berhasil diupload.";
		}
		else
			echo "Surat Pernyataan gagal diupload.";
	}

 }
?>
