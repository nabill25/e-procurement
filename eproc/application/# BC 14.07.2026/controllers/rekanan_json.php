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

class rekanan_json extends CI_Controller {

	function __construct() {
		parent::__construct(); $this->db->query("SET TIME ZONE 'Asia/Jakarta'");

		//kauth
		if (!$this->kauth->getInstance()->hasIdentity())
		{
			// trow to unauthenticated page!
			//redirect('Login');
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
		$this->REKANAN_EMAIL = $this->kauth->getInstance()->getIdentity()->REKANAN_EMAIL;
		$this->REKANAN_PKP = $this->kauth->getInstance()->getIdentity()->PKP;
		$this->REKANAN_NPWP = $this->kauth->getInstance()->getIdentity()->NPWP;
		$this->REKANAN_STATUS_PERUSAHAAN = $this->kauth->getInstance()->getIdentity()->STATUS_PERUSAHAAN;
		$this->REKANAN_STATUS_VALIDASI = $this->kauth->getInstance()->getIdentity()->STATUS_VALIDASI;
	}

	function json()
	{
		$this->load->library("kauth");  $userLogin = new kauth();
		$this->load->model("Rekanan");

		$rekanan = new Rekanan();
		$reqSearch = $this->input->get("reqSearch");

		ini_set("memory_limit","500M");
		ini_set('max_execution_time', 5200);

		$reqStatus= $this->input->post("reqStatus");
		$reqBidangUsaha = $this->input->post("reqBidangUsaha");

		$aColumns = array("REKANAN_ID", "KODE", "NAMA", "JUMLAH_PENILAIAN", "RATA_PENILAIAN", "NAMA_PENILAIAN");
		$aColumnsAlias = array("A.REKANAN_ID", "A.KODE", "A.NAMA", "B.JUMLAH_PENILAIAN", "B.RATA_PENILAIAN", "B.RATA_PENILAIAN");

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
			if ( trim($sOrder) == "ORDER BY A.REKANAN_ID asc" )
			{
				/*
				* If there is no order by clause - ORDER BY INDEX COLUMN!!! DON'T DELETE IT!
				* If there is no order by clause there might be bugs in table display.
				* No order by clause means that the db is not responsible for the data ordering,
				* which means that the same row can be displayed in two pages - while
				* another row will not be displayed at all.
				*/
				$sOrder = " ORDER BY COALESCE(B.RATA_PENILAIAN, 0) DESC";

			}
		}


		/*
		 * Filtering
		 * NOTE this does not match the built-in DataTables filtering which does it
		 * word by word on any field. It's possible to do here, but concerned about efficiency
		 * on very large tables.
		 */
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


		$searchJson= " AND (UPPER(A.NAMA) LIKE '%".strtoupper($reqSearch)."%' ) ";

		/*$statement .= "   AND NOT COALESCE(A.STATUS_VALIDASI, 0) IN (0, 2) ";
		if($reqBidangUsaha == "")
		{}
		else
		{
			$statement .= " AND EXISTS(SELECT 1 FROM REKANAN_BIDANG_USAHA X WHERE X.REKANAN_ID = A.REKANAN_ID AND X.BIDANG_USAHA_ID LIKE '".$reqBidangUsaha."%') ";
		}*/

		$allRecord = $rekanan->getCountByParams(array(),$statement);
		if($_GET['sSearch'] == "")
			$allRecordFilter = $allRecord;
		else
			$allRecordFilter = $rekanan->getCountByParams(array(), $statement.$searchJson);

		$rekanan->selectByParamsCari(array(), $dsplyRange, $dsplyStart, $statement.$searchJson, $sOrder);
		//echo $rekanan->query;exit;
		/* Output */
		$output = array(
			"sEcho" => intval($_GET['sEcho']),
			"iTotalRecords" => $allRecord,
			"iTotalDisplayRecords" => $allRecordFilter,
			"aaData" => array()
		);
		while($rekanan->nextRow())
		{
			$row = array();
			for ( $i=0 ; $i<count($aColumns) ; $i++ )
			{
				if($aColumns[$i] == "TANGGAL")
					$row[] = getFormattedDate($rekanan->getField($aColumns[$i]));
				else if($aColumns[$i] == "KETERANGAN")
					$row[] = truncate($rekanan->getField($aColumns[$i]), 5)."...";
				else if($aColumns[$i] == "NAMA")
					$row[] = strtoupper($rekanan->getField($aColumns[$i]));
				else
					$row[] = $rekanan->getField($aColumns[$i]);
			}

			$output['aaData'][] = $row;
		}

		echo json_encode( $output );
	}

	function daftar_penilaian_rekanan_monitoring_json()
	{
		$this->load->library("kauth");  $userLogin = new kauth();
		$this->load->model("Rekanan");

		$rekanan = new Rekanan();

		/* LOGIN CHECK */
		if ($userLogin->checkUserLogin())
		{
			$userLogin->retrieveUserInfo();
		}

		ini_set("memory_limit","500M");
		ini_set('max_execution_time', 5200);

		$reqStatus= $this->input->post("reqStatus");
		$reqBidangUsaha = $this->input->post("reqBidangUsaha");

		$aColumns = array("REKANAN_ID", "KODE", "NAMA", "JUMLAH_PENILAIAN", "RATA_PENILAIAN", "NAMA_PENILAIAN");
		$aColumnsAlias = array("A.REKANAN_ID", "A.KODE", "A.NAMA", "B.JUMLAH_PENILAIAN", "B.RATA_PENILAIAN", "B.RATA_PENILAIAN");

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
			if ( trim($sOrder) == "ORDER BY A.REKANAN_ID asc" )
			{
				/*
				* If there is no order by clause - ORDER BY INDEX COLUMN!!! DON'T DELETE IT!
				* If there is no order by clause there might be bugs in table display.
				* No order by clause means that the db is not responsible for the data ordering,
				* which means that the same row can be displayed in two pages - while
				* another row will not be displayed at all.
				*/
				$sOrder = " ORDER BY COALESCE(B.RATA_PENILAIAN, 0) DESC";

			}
		}


		/*
		 * Filtering
		 * NOTE this does not match the built-in DataTables filtering which does it
		 * word by word on any field. It's possible to do here, but concerned about efficiency
		 * on very large tables.
		 */
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


		$searchJson= " AND (UPPER(A.NAMA) LIKE '%".strtoupper($_GET['sSearch'])."%' OR UPPER(A.KODE) LIKE '%".strtoupper($_GET['sSearch'])."%') ";

		$statement .= "   AND NOT COALESCE(A.STATUS_VALIDASI, 0) IN (0, 2) ";
		if($reqBidangUsaha == "")
		{}
		else
		{
			$statement .= " AND EXISTS(SELECT 1 FROM REKANAN_BIDANG_USAHA X WHERE X.REKANAN_ID = A.REKANAN_ID AND X.BIDANG_USAHA_ID LIKE '".$reqBidangUsaha."%') ";
		}

		$allRecord = $rekanan->getCountByParams(array(),$statement);
		if($_GET['sSearch'] == "")
			$allRecordFilter = $allRecord;
		else
			$allRecordFilter = $rekanan->getCountByParams(array(), $statement.$searchJson);

		$rekanan->selectByParamsDaftarPenilaianRekanan(array(), $dsplyRange, $dsplyStart, $statement.$searchJson, $sOrder);
		//echo $rekanan->query;
		/* Output */
		$output = array(
			"sEcho" => intval($_GET['sEcho']),
			"iTotalRecords" => $allRecord,
			"iTotalDisplayRecords" => $allRecordFilter,
			"aaData" => array()
		);
		while($rekanan->nextRow())
		{
			$row = array();
			for ( $i=0 ; $i<count($aColumns) ; $i++ )
			{
				if($aColumns[$i] == "TANGGAL")
					$row[] = getFormattedDate($rekanan->getField($aColumns[$i]));
				else if($aColumns[$i] == "KETERANGAN")
					$row[] = truncate($rekanan->getField($aColumns[$i]), 5)."...";
				else if($aColumns[$i] == "NAMA")
					$row[] = strtoupper($rekanan->getField($aColumns[$i]));
				else
					$row[] = $rekanan->getField($aColumns[$i]);
			}

			$output['aaData'][] = $row;
		}

		echo json_encode( $output );
	}

	function daftar_rekanan_json()
	{
		$this->load->model("Rekanan");
		$rekanan = new Rekanan();
		$rekananDetail = new Rekanan();
		$cekUpdateAllow = new Rekanan();

		ini_set("memory_limit","500M");
		ini_set('max_execution_time', 520);

		$reqKeterangan = $this->input->post("reqKeterangan");
		$reqId = $this->input->post("reqId");
		$reqSearch = $this->input->get("reqSearch");
		$reqMode = $this->input->get("reqMode");
		$reqStatus = $this->input->get("reqStatus");

		$aColumns 			= array('REKANAN_ID', 'KODE', 'NAMA','KOTA','TANGGAL_DAFTAR','USER_VALIDASI', 'USER_STATUS', 'STATUS_VALIDASI', 'USER_AKTIF','USER_LOGIN_ID');
		$aColumnsAlias		= array('REKANAN_ID', 'KODE', 'NAMA','KOTA','TANGGAL_DAFTAR','USER_VALIDASI', 'USER_STATUS', 'STATUS_VALIDASI', 'USER_AKTIF','USER_LOGIN_ID');

		if ( isset( $_GET['iSortCol_0'] ) )
		{
			$sOrder = " ORDER BY ";

			for ( $i=0 ; $i<intval( $_GET['iSortingCols'] ) ; $i++ )
			{
				if ( $_GET[ 'bSortable_'.intval($_GET['iSortCol_'.$i]) ] == "true" )
				{
					$sOrder .= $aColumnsAlias[ intval( $_GET['iSortCol_'.$i] ) ];

					if (substr_compare(( $_GET['sSortDir_'.$i] ), "asc", 0) == 0)
					{
						$sOrder .=" asc, ";
					}else
					{
						$sOrder .=" desc, ";
					}
				}
			}
			$sOrder = substr_replace( $sOrder, "", -2 );

			if ( trim($sOrder) == "ORDER BY rekanan_ELIMINASI desc" )
			{
				$sOrder = " ORDER BY A.rekanan asc";

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

		switch ($reqStatus) {
			case '00': $statement .= " AND COALESCE(U.USER_STATUS, 0) = 0 AND COALESCE(A.STATUS_VALIDASI, 0) = 0 AND TANGGAL_HAPUS IS NULL "; break;
			case '20': $statement .= " AND COALESCE(U.USER_STATUS, 0) = 2 AND COALESCE(STATUS_VALIDASI, 0) = 0 AND TANGGAL_HAPUS IS NULL "; break;
			case '24': $statement .= " AND COALESCE(U.USER_STATUS, 0) = 2 AND COALESCE(STATUS_VALIDASI, 0) = 4 AND TANGGAL_HAPUS IS NULL "; break;
			case '210': $statement .= " AND COALESCE(U.USER_STATUS, 0) = 2 AND COALESCE(STATUS_VALIDASI, 0) = 10 AND TANGGAL_HAPUS IS NULL "; break;
			case '11': $statement .= " AND COALESCE(U.USER_STATUS, 0) = 1 AND COALESCE(STATUS_VALIDASI, 0) = 1 AND TANGGAL_HAPUS IS NULL "; break;
			case '01': $statement .= " AND COALESCE(U.USER_STATUS, 0) = 0 AND COALESCE(STATUS_VALIDASI, 0) = 1 AND TANGGAL_HAPUS IS NULL "; break;
			default: $statement .= " AND COALESCE(U.USER_STATUS, 0) = 0 AND COALESCE(A.STATUS_VALIDASI, 0) = 0 AND TANGGAL_HAPUS IS NULL "; break;
		}

		//if($reqMode)	$arr_filter .= " AND D.BIDANG_USAHA_ID LIKE '".$reqMode."%' ";
		if($reqMode)	$arr_filter .= " AND EXISTS (SELECT 1
					FROM REKANAN_BIDANG_USAHA X
					WHERE X.REKANAN_ID = A.REKANAN_ID AND X.BIDANG_USAHA_ID LIKE '".$reqMode."%') ";

		$statement .= "AND (UPPER(A.NAMA) LIKE '%".strtoupper($_GET['sSearch'])."%' OR UPPER(B.NAMA) LIKE '".strtoupper($_GET['sSearch'])."%')";
		$allRecord = $rekanan->getCountByParamsAll(array(), $statement);
		if($_GET['sSearch'] == "")
			$allRecordFilter = $allRecord;
		else
			$allRecordFilter =  $rekanan->getCountByParamsAll(array(), $statement);

		$rekanan->selectByParamsAll(array(), $dsplyRange, $dsplyStart, $statement, $sOrder);

		$output = array(
			"sEcho" => intval($_GET['sEcho']),
			"iTotalRecords" => $allRecord,
			"iTotalDisplayRecords" => $allRecordFilter,
			"aaData" => array()
		);

		while($rekanan->nextRow())
		{
			$notifRevisiDokumen = '';
			if ($reqStatus == '20') { // Kirim Berkas
				$notifRevisiDokumen = '';
				$cekKelengkapanData = new Rekanan();
				$cekKelengkapanData->selectByParamsRekananChecklistCekKelengkapan(array("REKANANID"=>$rekanan->getField(trim('REKANAN_ID'))),-1,-1);
				$cekKelengkapanData->firstRow();
				if ($cekKelengkapanData->getField("JUMLAH_TERISI") > 0) {
					$notifRevisiDokumen = '<span class="badge badge-danger">'.$cekKelengkapanData->getField("JUMLAH_TERISI").' data dikembalikan</span>';
				}
			}
			$rekananDetail->selectByParams(array("A.REKANAN_ID" => $rekanan->getField(trim('REKANAN_ID'))));
			$rekananDetail->firstRow();
			$dateValidasi2 = explode(' ',$rekananDetail->getField(trim('DATE_VALIDASI_2')));

			$cekUpdateAllow->selectByParamsURLValidasiAllow(array("A.REKANAN_ID" => $rekanan->getField(trim('REKANAN_ID'))));
			$cekUpdateAllow->firstRow();

			$row = array();
			for ( $i=0 ; $i<count($aColumns) ; $i++ )
			{
				if($aColumns[$i]=='USER_VALIDASI') {
					$explodeRekanan = explode("||", $rekanan->getField(trim($aColumns[$i])));
				}

				if($aColumns[$i]=='NO') {
					$row[] = $number;
				} elseif($aColumns[$i]=='NAMA') {
					$row[] = $rekanan->getField(trim($aColumns[$i])).' '.$notifRevisiDokumen;
				} elseif($aColumns[$i]=='TANGGAL_DAFTAR') {
					$row[] = '<small> <span class="badge badge-primary">Daftar: '.strtoupper(getFormattedDateJson($rekanan->getField(trim($aColumns[$i])))).'</span><br>
						<span class="badge badge-warning">Approve: '.strtoupper(getFormattedDateJson($rekananDetail->getField(trim('TANGGAL_VALIDASI')))).'</span></small>';

				} elseif($aColumns[$i]=='USER_VALIDASI') {
					$row[] = $explodeRekanan[1].'<br><small>'.$explodeRekanan[2].'</small>';
				} elseif($aColumns[$i]=='USER_STATUS') {
					if ($rekanan->getField('STATUS_VALIDASI') == '1') {
						if($rekanan->getField(trim($aColumns[$i])) == '1') {
							if ($cekUpdateAllow->getField('UPDATED_DATE')) {
								$exPlodeDate = explode(' ',$cekUpdateAllow->getField('UPDATED_DATE'));
								$row[] = '<img src="images/centang.png"> <br>'.
										 '<small><span class="badge badge-dark"><i>Last Change</i><br>'.strtoupper(getFormattedDateJson($exPlodeDate[0])).'</span></small>';
							} else {
								$row[] = '<img src="images/centang.png">';
							}
						}
						else {
							if ($this->USER_TYPE_ID == 2) { // yang bisa hanya user VMS
							$row[] = '<img src="images/uncentang.png"><br>
									  <a href="'.base_url('main/index/daftar_rekanan_akses?reqId='.$rekanan->getField(trim('REKANAN_ID'))).'" class="badge badge-danger">Hak Akses</a>';
							} else {
							$row[] = '<img src="images/uncentang.png">';
							}
						}
					} else {
						$row[] = '-';
					}
				} elseif($aColumns[$i]=='USER_AKTIF') {
					if($rekanan->getField(trim($aColumns[$i])) == '1')
						$row[] = '<img src="images/centang.png">';
					else
						$row[] = '<img src="images/uncentang.png">';
				} else {
					$row[] = strtoupper($rekanan->getField(trim($aColumns[$i])));
				}
			}
			$output['aaData'][] = $row;
		}
		echo json_encode( $output );
	}

	function getstatus()
	{
		$this->load->model("Dashboardvms");
		$countData = new Dashboardvms();
		$countData->selectByParamsForVerifikasi();
		$countData->firstRow();

		$data['melengkapi'] = number_format($countData->getfield('melengkapi_berkas'),0, ",",".");
		$data['kirim'] = number_format($countData->getfield('kirim_berkas'),0, ",",".");
		$data['proses'] = number_format($countData->getfield('proses_approval'),0, ",",".");
		$data['tolak'] = number_format($countData->getfield('berkas_ditolak'),0, ",",".");
		$data['terverifikasi'] = number_format($countData->getfield('terverfikasi'),0, ",",".");
		$data['revisi'] = number_format($countData->getfield('revisi'),0, ",",".");

		echo json_encode( $data );
	}

	function daftar_rekanan_belum_json()
	{
		$this->load->model("Rekanan");
		$this->load->model("Users");
		$rekanan = new Rekanan();
		$user_login = new Users();

		ini_set("memory_limit","500M");
		ini_set('max_execution_time', 520);

		$reqKeterangan = $this->input->post("reqKeterangan");
		$reqId = $this->input->post("reqId");
		$reqSearch = $this->input->get("reqSearch");
		$reqMode = $this->input->get("reqMode");
		$reqStatus= $this->input->get("reqStatus");

		$aColumns 			= array('REKANAN_ID', 'KODE', 'NAMA','KOTA','TANGGAL_DAFTAR','STATUS_VALIDASI','USER_STATUS');
		$aColumnsAlias		= array('REKANAN_ID', 'KODE', 'NAMA','KOTA','TANGGAL_DAFTAR','STATUS_VALIDASI','USER_STATUS');

		if ( isset( $_GET['iSortCol_0'] ) )
		{
			$sOrder = " ORDER BY ";

			for ( $i=0 ; $i<intval( $_GET['iSortingCols'] ) ; $i++ )
			{
				if ( $_GET[ 'bSortable_'.intval($_GET['iSortCol_'.$i]) ] == "true" )
				{
					$sOrder .= $aColumnsAlias[ intval( $_GET['iSortCol_'.$i] ) ];
					if (substr_compare(( $_GET['sSortDir_'.$i] ), "asc", 0) == 0)
					{
						$sOrder .=" asc, ";
					}else
					{
						$sOrder .=" desc, ";
					}
				}
			}

			$sOrder = substr_replace( $sOrder, "", -2 );

			if ( trim($sOrder) == "ORDER BY REKANAN_ID DESC" )
			{
				$sOrder = " ORDER BY REKANAN_ID DESC";

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
		if ($this->USER_TYPE_ID == 19) { // Rekomendator VMS
			if ($reqStatus == '') {
				$statement .= " AND COALESCE(STATUS_VALIDASI, 0) = 3 AND TANGGAL_HAPUS IS NULL ";
			} else {
				switch ($reqStatus) {
					case '0': $statement .= " AND COALESCE(STATUS_VALIDASI, 0) = 0 AND TANGGAL_HAPUS IS NULL "; break;
					case '1': $statement .= " AND COALESCE(STATUS_VALIDASI, 0) = 1 AND TANGGAL_HAPUS IS NULL "; break;
					case '3': $statement .= " AND COALESCE(STATUS_VALIDASI, 0) = 3 AND TANGGAL_HAPUS IS NULL "; break;
					case '4': $statement .= " AND COALESCE(STATUS_VALIDASI, 0) = 4 AND TANGGAL_HAPUS IS NULL "; break;
					case 'all': $statement .= " AND TANGGAL_HAPUS IS NULL "; break;
					// default: $statement .= " AND COALESCE(STATUS_VALIDASI, 0) = 3 AND TANGGAL_HAPUS IS NULL "; break;
				}
			}
		} else if ($this->USER_TYPE_ID == 18) { // Validator VMS
			if ($reqStatus == '') {
				$statement .= " AND COALESCE(STATUS_VALIDASI, 0) = 4 AND TANGGAL_HAPUS IS NULL ";
			} else {
				switch ($reqStatus) {
					case '0': $statement .= " AND COALESCE(STATUS_VALIDASI, 0) = 0 AND TANGGAL_HAPUS IS NULL "; break;
					case '1': $statement .= " AND COALESCE(STATUS_VALIDASI, 0) = 1 AND TANGGAL_HAPUS IS NULL "; break;
					case '3': $statement .= " AND COALESCE(STATUS_VALIDASI, 0) = 3 AND TANGGAL_HAPUS IS NULL "; break;
					case '4': $statement .= " AND COALESCE(STATUS_VALIDASI, 0) = 4 AND TANGGAL_HAPUS IS NULL "; break;
					case 'all': $statement .= " AND TANGGAL_HAPUS IS NULL "; break;
					// default: $statement .= " AND COALESCE(STATUS_VALIDASI, 0) = 3 AND TANGGAL_HAPUS IS NULL "; break;
				}
			}
		} else if ($this->USER_TYPE_ID == 2 || $this->USER_TYPE_ID == 26) { // User VMS
			if ($reqStatus == '') {
				$statement .= " AND COALESCE(STATUS_VALIDASI, 0) = 0 AND TANGGAL_HAPUS IS NULL ";
			} else {
				switch ($reqStatus) {
					case '0': $statement .= " AND COALESCE(STATUS_VALIDASI, 0) = 0 AND TANGGAL_HAPUS IS NULL "; break;
					case '1': $statement .= " AND COALESCE(STATUS_VALIDASI, 0) = 1 AND TANGGAL_HAPUS IS NULL "; break;
					case '3': $statement .= " AND COALESCE(STATUS_VALIDASI, 0) = 3 AND TANGGAL_HAPUS IS NULL "; break;
					case '4': $statement .= " AND COALESCE(STATUS_VALIDASI, 0) = 4 AND TANGGAL_HAPUS IS NULL "; break;
					case '10': $statement .= " AND COALESCE(STATUS_VALIDASI, 0) = 10 AND TANGGAL_HAPUS IS NULL "; break;
					case 'all': $statement .= " AND TANGGAL_HAPUS IS NULL "; break;
					// default: $statement .= " AND COALESCE(STATUS_VALIDASI, 0) = 3 AND TANGGAL_HAPUS IS NULL "; break;
				}
			}
		} else {
		$statement .= " AND COALESCE(STATUS_VALIDASI, 0) != 1 AND TANGGAL_HAPUS IS NULL ";
		}
		//if($reqMode)	$arr_filter .= " AND D.BIDANG_USAHA_ID LIKE '".$reqMode."%' ";
		if($reqMode)	$arr_filter .= " AND EXISTS (SELECT 1
					FROM REKANAN_BIDANG_USAHA X
					WHERE X.REKANAN_ID = A.REKANAN_ID AND X.IJIN_USAHA_ID = A.IJIN_USAHA_ID AND X.BIDANG_USAHA_ID LIKE '".$reqMode."%') ";

		$statement .= "AND (UPPER(A.NAMA) LIKE '%".strtoupper($_GET['sSearch'])."%' OR UPPER(B.NAMA) LIKE '".strtoupper($_GET['sSearch'])."%')";
		$allRecord = $rekanan->getCountByParams(array(), $statement);
		if($_GET['sSearch'] == "")
			$allRecordFilter = $allRecord;
		else
			$allRecordFilter =  $rekanan->getCountByParams(array(), $statement);

		$rekanan->selectByParams(array(), $dsplyRange, $dsplyStart, $statement, $sOrder);

		$output = array(
			"sEcho" => intval($_GET['sEcho']),
			"iTotalRecords" => $allRecord,
			"iTotalDisplayRecords" => $allRecordFilter,
			"aaData" => array()
		);

		while($rekanan->nextRow())
		{
			$row = array();
			for ( $i=0 ; $i<count($aColumns) ; $i++ )
			{
				if($aColumns[$i]=='NO') {
					$row[] = $number;
				} elseif($aColumns[$i]=='NAMA'){
					$namaRekanan = str_replace(',','',$rekanan->getField($aColumns[$i]));
					switch ($rekanan->getField('STATUS_VALIDASI')) {
						// 0=Belum 1=Validasi 2=Hapus 3=Kirim ke Rekomendator, 4=Kirim ke Validator
						case '0':
							$user_login->selectByParams(array("REKANAN_ID"=>$rekanan->getField('REKANAN_ID')),-1,-1);
							$user_login->firstRow();
							if ($user_login->getField('USER_STATUS') == '2') {
								$row[] = $rekanan->getField($aColumns[$i]).'<br><span class="badge badge-info">Sudah Kirim Berkas</span>';
							} else {
								$row[] = $rekanan->getField($aColumns[$i]).'<br><span class="badge badge-danger">Melengkapi Berkas</span>';
							}
							break;
						case '10':
							$user_login->selectByParams(array("REKANAN_ID"=>$rekanan->getField('REKANAN_ID')),-1,-1);
							$user_login->firstRow();
							if ($user_login->getField('USER_STATUS') == '2') {
								$row[] = $rekanan->getField($aColumns[$i]).'<br><span class="badge badge-danger">Berkasi dikembalikan</span>';
							} else {
								$row[] = $rekanan->getField($aColumns[$i]).'<br><span class="badge badge-danger">Melengkapi Berkas</span>';
							}
							break;
						case '1':
							$row[] = $rekanan->getField($aColumns[$i]).'<br><span class="badge badge-primary">Terverifikasi</span>';
							break;
						case '3':
							$row[] = $namaRekanan.'<br><span class="badge badge-success">Menunggu Approval Penyelia</span>';
							break;
						case '4':
							$row[] = $namaRekanan.'<br><span class="badge badge-warning">Menunggu Aprroval VMS</span>';
							break;
						// default: // default 0
						// 	break;
					}
				} elseif($aColumns[$i]=='TANGGAL_DAFTAR'){
					$row[] = strtoupper(getFormattedDateJson($rekanan->getField(trim($aColumns[$i]))));
				} else {
					$row[] = strtoupper($rekanan->getField(trim($aColumns[$i])));
				}
			}
			$output['aaData'][] = $row;
		}
		echo json_encode( $output );
	}

	function daftar_rekanan_delete_json()
	{
		$this->load->model("Rekanan");
		$this->load->model("Users");
		$rekanan = new Rekanan();
		$user_login = new Users();

		ini_set("memory_limit","500M");
		ini_set('max_execution_time', 520);

		$reqKeterangan = $this->input->post("reqKeterangan");
		$reqId = $this->input->post("reqId");
		$reqSearch = $this->input->get("reqSearch");
		$reqMode = $this->input->get("reqMode");
		$reqStatus= $this->input->get("reqStatus");

		$aColumns 			= array('REKANAN_ID', 'KODE', 'NAMA','KOTA','TANGGAL_DAFTAR','STATUS_VALIDASI','USER_STATUS');
		$aColumnsAlias		= array('REKANAN_ID', 'KODE', 'NAMA','KOTA','TANGGAL_DAFTAR','STATUS_VALIDASI','USER_STATUS');

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
			if ( trim($sOrder) == "ORDER BY REKANAN_ID DESC" )
			{
				$sOrder = " ORDER BY REKANAN_ID DESC";

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

		/* Individual aColumns filtering */
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

		if($reqMode)	$arr_filter .= " AND EXISTS (SELECT 1
					FROM REKANAN_BIDANG_USAHA X
					WHERE X.REKANAN_ID = A.REKANAN_ID AND X.IJIN_USAHA_ID = A.IJIN_USAHA_ID AND X.BIDANG_USAHA_ID LIKE '".$reqMode."%') ";

		$statement .= "AND (UPPER(A.NAMA) LIKE '%".strtoupper($_GET['sSearch'])."%' OR UPPER(B.NAMA) LIKE '".strtoupper($_GET['sSearch'])."%')";
		$allRecord = $rekanan->getCountByParams(array(), $statement);
		if($_GET['sSearch'] == "")
			$allRecordFilter = $allRecord;
		else
			$allRecordFilter =  $rekanan->getCountByParams(array(), $statement);

		$rekanan->selectByParams(array(), $dsplyRange, $dsplyStart, $statement, $sOrder);

		$output = array(
			"sEcho" => intval($_GET['sEcho']),
			"iTotalRecords" => $allRecord,
			"iTotalDisplayRecords" => $allRecordFilter,
			"aaData" => array()
		);

		while($rekanan->nextRow())
		{
			$row = array();
			for ( $i=0 ; $i<count($aColumns) ; $i++ )
			{
				if($aColumns[$i]=='NO') {
					$row[] = $number;
				} elseif($aColumns[$i]=='NAMA'){
					$namaRekanan = str_replace(',','',$rekanan->getField($aColumns[$i]));
					switch ($rekanan->getField('STATUS_VALIDASI')) {
						// 0=Belum 1=Validasi 2=Hapus 3=Kirim ke Rekomendator, 4=Kirim ke Validator
						case '0':
							$user_login->selectByParams(array("REKANAN_ID"=>$rekanan->getField('REKANAN_ID')),-1,-1);
							$user_login->firstRow();
							if ($user_login->getField('USER_STATUS') == '2') {
								$row[] = $rekanan->getField($aColumns[$i]).'<br><span class="badge badge-info">Sudah Kirim Berkas</span>';
							} else {
								$row[] = $rekanan->getField($aColumns[$i]).'<br><span class="badge badge-danger">Melengkapi Berkas</span>';
							}
							break;
						case '10':
							$user_login->selectByParams(array("REKANAN_ID"=>$rekanan->getField('REKANAN_ID')),-1,-1);
							$user_login->firstRow();
							if ($user_login->getField('USER_STATUS') == '2') {
								$row[] = $rekanan->getField($aColumns[$i]).'<br><span class="badge badge-danger">Berkasi dikembalikan</span>';
							} else {
								$row[] = $rekanan->getField($aColumns[$i]).'<br><span class="badge badge-danger">Melengkapi Berkas</span>';
							}
							break;
						case '1':
							$row[] = $rekanan->getField($aColumns[$i]).'<br><span class="badge badge-primary">Terverifikasi</span>';
							break;
						case '3':
							$row[] = $namaRekanan.'<br><span class="badge badge-success">Menunggu Approval Penyelia</span>';
							break;
						case '4':
							$row[] = $namaRekanan.'<br><span class="badge badge-warning">Menunggu Aprroval VMS</span>';
							break;
						// default: // default 0
						// 	break;
					}
				} elseif($aColumns[$i]=='TANGGAL_DAFTAR'){
					$row[] = strtoupper(getFormattedDateJson($rekanan->getField(trim($aColumns[$i]))));
				} else {
					$row[] = strtoupper($rekanan->getField(trim($aColumns[$i])));
				}
			}
			$output['aaData'][] = $row;
		}
		echo json_encode( $output );
	}

	public function getRekananDelete($rekananid)
	{
		sleep(0);

		$this->load->model("Rekanandelete");
		$rekanandel = new Rekanandelete();
		$rekanandel->selectByParams($rekananid, $dsplyRange, $dsplyStart, $statement, $sOrder);

		$html  = '';

		$html .= '<table class="table table-bordered table-hover">
	                  <tbody>
	                  	  <tr>
	                  	  	<th width="3%">No Urut</th><th width="70%">File</th><th class="text-center" width="5%">Jumlah Data</th><th width="10%"> # </th>
	                      </tr>';
	    $totalDok = 0;
	    while($rekanandel->nextRow())
		{
			if ($rekanandel->getField('URUT') == '14') {
				if ($totalDok == 0) {
					$html .= '
		    				  <tr>
		                          <td class="text-center">'.$rekanandel->getField('URUT').'</td>
		                          <td>'.$rekanandel->getField('FIELD').'</td>
		                          <td class="text-center">'.$rekanandel->getField('TOTAL').'</td>
		                          <td width="15px" class="text-center">';
			        if ($rekanandel->getField('TOTAL') != 0) {
			         $html .= '
			                          	<a id="btnDelete_'.$rekanandel->getField('URUT').'" style="color:#fff" class="badge badge-danger" onclick="return aaa(\''.$rekanandel->getField('URUT').'\')"><span class="fa fa-trash"></span></span></a>
			                          	<span id="msgDelete_'.$rekanandel->getField('URUT').'"></span>
			                 ';
			        }

			        $html .= '
			                          </td>
			                      </tr> ';
				}
			} else {
		    	$html .= '
		    				  <tr>
		                          <td class="text-center">'.$rekanandel->getField('URUT').'</td>
		                          <td>'.$rekanandel->getField('FIELD').'</td>
		                          <td class="text-center">'.$rekanandel->getField('TOTAL').'</td>
		                          <td width="15px" class="text-center">';
		        if ($rekanandel->getField('TOTAL') != 0) {
		         $html .= '
		                          	<a id="btnDelete_'.$rekanandel->getField('URUT').'" style="color:#fff" class="badge badge-danger" onclick="return aaa(\''.$rekanandel->getField('URUT').'\')"><span class="fa fa-trash"></span></span></a>
		                          	<span id="msgDelete_'.$rekanandel->getField('URUT').'"></span>
		                 ';
		        }

		        $html .= '
		                          </td>
		                      </tr> ';
			}

			$totalDok += $rekanandel->getField('TOTAL');
	    }
	    $html .= '
	                  </tbody>
	                </table>';

	    $html .= '<p><b><i><u>Catatan: </u></i></b>
	    			<br> Hapus Sesuai dengan Nomor Urut.
	    			<br> Dahulukan Menghapus dari Nomor 1 s/d 13
	    		  </p>';

		echo json_encode(array('respon' => 'false', 'message' => $html));
	}

	public function excRekananDelete($rekananid,$action)
	{
		sleep(1);
		$this->load->model("Rekanandelete");
		$rekanandel = new Rekanandelete();
		$rekanandel->setField("REKANAN_ID", $rekananid);
		$rekanandel->setField("ACTION", $action);

		$html  = '';
		switch ($action) {
			case '1': // Ijin Usaha

				if($rekanandel->deleteData())
				{
					$respon = "true";
					$html 	.= "Data Ijin Usaha Berhasil di hapus";
				} else {
					$respon  = "false";
					$html 	.= "Data Ijin Usaha Gagal di hapus, silahkan dicoba kembali";
				}

				$this->logDelete($html,$rekananid);

				break;

			case '2': // Bidang Usaha

				if($rekanandel->deleteData())
				{
					$respon = "true";
					$html 	.= "Data Bidang Usaha Berhasil di hapus";
				} else {
					$respon  = "false";
					$html 	.= "Data Bidang Usaha Gagal di hapus, silahkan dicoba kembali";
				}

				$this->logDelete($html,$rekananid);

				break;

			case '3': // Landasan Hukum
				if($rekanandel->deleteData())
				{
					$respon = "true";
					$html 	.= "Data Landasan Hukum Berhasil di hapus";
				} else {
					$respon  = "false";
					$html 	.= "Data Landasan Hukum Gagal di hapus, silahkan dicoba kembali";
				}

				$this->logDelete($html,$rekananid);
				break;

			case '4': // Pengurus Perusahaan
				if($rekanandel->deleteData())
				{
					$respon = "true";
					$html 	.= "Data Pengurus Perusahaan Berhasil di hapus";
				} else {
					$respon  = "false";
					$html 	.= "Data Pengurus Perusahaan Gagal di hapus, silahkan dicoba kembali";
				}

				$this->logDelete($html,$rekananid);
				break;

			case '5': // Kepemilikan Saham
				if($rekanandel->deleteData())
				{
					$respon = "true";
					$html 	.= "Data Kepemilikan Saham Berhasil di hapus";
				} else {
					$respon  = "false";
					$html 	.= "Data Kepemilikan Saham Gagal di hapus, silahkan dicoba kembali";
				}

				$this->logDelete($html,$rekananid);
				break;

			case '6': // SBU
				if($rekanandel->deleteData())
				{
					$respon = "true";
					$html 	.= "Data SBU Berhasil di hapus";
				} else {
					$respon  = "false";
					$html 	.= "Data SBU Gagal di hapus, silahkan dicoba kembali";
				}

				$this->logDelete($html,$rekananid);
				break;

			case '7': // Rekening Koran
				if($rekanandel->deleteData())
				{
					$respon = "true";
					$html 	.= "Data Rekening Koran Berhasil di hapus";
				} else {
					$respon  = "false";
					$html 	.= "Data Rekening Koran Gagal di hapus, silahkan dicoba kembali";
				}

				$this->logDelete($html,$rekananid);
				break;

			case '8': // Neraca
				if($rekanandel->deleteData())
				{
					$respon = "true";
					$html 	.= "Data Neraca Berhasil di hapus";
				} else {
					$respon  = "false";
					$html 	.= "Data Neraca Gagal di hapus, silahkan dicoba kembali";
				}

				$this->logDelete($html,$rekananid);
				break;

			case '9': // SPT Tahunan, PPH & PPN
				if($rekanandel->deleteData())
				{
					$respon = "true";
					$html 	.= "Data SPT Tahunan, PPH & PPN Berhasil di hapus";
				} else {
					$respon  = "false";
					$html 	.= "Data SPT Tahunan, PPH & PPN Gagal di hapus, silahkan dicoba kembali";
				}

				$this->logDelete($html,$rekananid);
				break;

			case '10': // Tenaga Ahli
				if($rekanandel->deleteData())
				{
					$respon = "true";
					$html 	.= "Data Tenaga Ahli Berhasil di hapus";
				} else {
					$respon  = "false";
					$html 	.= "Data Tenaga Ahli Gagal di hapus, silahkan dicoba kembali";
				}

				$this->logDelete($html,$rekananid);
				break;

			case '11': // Pengalaman
				if($rekanandel->deleteData())
				{
					$respon = "true";
					$html 	.= "Data Pengalaman Berhasil di hapus";
				} else {
					$respon  = "false";
					$html 	.= "Data Pengalaman Gagal di hapus, silahkan dicoba kembali";
				}

				$this->logDelete($html,$rekananid);
				break;

			case '12': // Peralatan
				if($rekanandel->deleteData())
				{
					$respon = "true";
					$html 	.= "Data Peralatan Berhasil di hapus";
				} else {
					$respon  = "false";
					$html 	.= "Data Peralatan Gagal di hapus, silahkan dicoba kembali";
				}

				$this->logDelete($html,$rekananid);
				break;

			case '13': // Dokumen Teknis Perusahaan
				if($rekanandel->deleteData())
				{
					$respon = "true";
					$html 	.= "Data Dokumen Teknis Perusahaan Berhasil di hapus";
				} else {
					$respon  = "false";
					$html 	.= "Data Dokumen Teknis Perusahaan Gagal di hapus, silahkan dicoba kembali";
				}

				$this->logDelete($html,$rekananid);
				break;

			case '14': // Profile table USER_LOGIN & User Login table REKANAN
				if($rekanandel->deleteData())
				{
					$respon = "true";
					$html 	.= "Data User Vendor Berhasil di hapus";
				} else {
					$respon  = "false";
					$html 	.= "Data User Vendor Gagal di hapus, silahkan dicoba kembali";
				}

				$this->logDelete($html,$rekananid);
				break;

			// case '15': // User Login table REKANAN
			// 	if($rekanandel->deleteData())
			// 	{
			// 		$respon = "true";
			// 		$html 	.= "Data User Login Berhasil di hapus";
			// 	} else {
			// 		$respon  = "false";
			// 		$html 	.= "Data User Login Gagal di hapus, silahkan dicoba kembali";
			// 	}

			// 	$this->logDelete($html,$rekananid);
			// 	break;

			default:
				// code...
				break;
		}

		echo json_encode(array('respon' => $respon, 'message' => $html));

	}

	function logDelete($query2,$rekananid) {
      $filepath = 'logs/del/Delete_log_' . date('Y-m-d') . '.txt';
      $handle = fopen($filepath, "a+");

      $times = $this->CI->db->query_times;

      $text   = "TIME:".date('h:i:s')." ### DATE:".date('Y-m-d')." ### USERID:". $this->USER_LOGIN_ID." ### IP:". $this->getIP()." ### REKANAN_ID:". $rekananid." ### STATEMENT:". $query2."

        -----------------------------------------------------------------------------------------";

      $arr = array('', '<br>');
        $logtext = str_replace($arr, "", $text);
        fwrite($handle, $logtext . "\r\n");
      fclose($handle);
      if (!is_dir($filepath))
      {
        // mkdir($dir, 0777, true);
        // chmod($filepath, 0777, true);  //changed to add the zero
      }
    }

    // Mendapatkan IP pengunjung menggunakan $_SERVER
	private function getIp() {
	 $ipaddress = '';
	 if (isset($_SERVER['HTTP_CLIENT_IP']))
	     $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
	 else if(isset($_SERVER['HTTP_X_FORWARDED_FOR']))
	     $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
	 else if(isset($_SERVER['HTTP_X_FORWARDED']))
	     $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
	 else if(isset($_SERVER['HTTP_FORWARDED_FOR']))
	     $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
	 else if(isset($_SERVER['HTTP_FORWARDED']))
	     $ipaddress = $_SERVER['HTTP_FORWARDED'];
	 else if(isset($_SERVER['REMOTE_ADDR']))
	     $ipaddress = $_SERVER['REMOTE_ADDR'];
	 else
	     $ipaddress = 'IP tidak dikenali';
	 return $ipaddress;
	}

	function daftar_rekanan_hapus_json()
	{
		$this->load->model("Rekanan");
		$rekanan = new Rekanan();

		ini_set("memory_limit","500M");
		ini_set('max_execution_time', 520);

		$reqKeterangan = $this->input->post("reqKeterangan");
		$reqId = $this->input->post("reqId");
		$reqSearch = $this->input->get("reqSearch");
		$reqMode = $this->input->get("reqMode");

		$aColumns 			= array('REKANAN_ID', 'NAMA','TANGGAL_DAFTAR','TANGGAL_HAPUS','KODE', 'ALASAN_HAPUS');
		$aColumnsAlias		= array('REKANAN_ID',  'NAMA','TANGGAL_DAFTAR','TANGGAL_HAPUS','KODE', 'ALASAN_HAPUS');

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
			if ( trim($sOrder) == "ORDER BY rekanan_ELIMINASI desc" )
			{
				/*
				* If there is no order by clause - ORDER BY INDEX COLUMN!!! DON'T DELETE IT!
				* If there is no order by clause there might be bugs in table display.
				* No order by clause means that the db is not responsible for the data ordering,
				* which means that the same row can be displayed in two pages - while
				* another row will not be displayed at all.
				*/
				$sOrder = " ORDER BY A.rekanan asc";

			}
		}

		/*
		 * Filtering
		 * NOTE this does not match the built-in DataTables filtering which does it
		 * word by word on any field. It's possible to do here, but concerned about efficiency
		 * on very large tables.
		 */
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

		/* Individual aColumns filtering */
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

		$statement .= " AND STATUS_VALIDASI = 2 ";
		//if($reqMode)	$arr_filter .= " AND D.BIDANG_USAHA_ID LIKE '".$reqMode."%' ";
		/*if($reqMode)	$arr_filter .= " AND EXISTS (SELECT 1
					FROM REKANAN_BIDANG_USAHA X
					WHERE X.REKANAN_ID = A.REKANAN_ID AND X.IJIN_USAHA_ID = A.IJIN_USAHA_ID AND X.BIDANG_USAHA_ID LIKE '".$reqMode."%') ";*/

		$statement .= "AND (UPPER(A.NAMA) LIKE '%".strtoupper($_GET['sSearch'])."%' )";
		$allRecord = $rekanan->getCountByParams(array(), $statement);
		if($_GET['sSearch'] == "")
			$allRecordFilter = $allRecord;
		else
			$allRecordFilter =  $rekanan->getCountByParams(array(), $statement);

		$rekanan->selectByParams(array(), $dsplyRange, $dsplyStart, $statement, $sOrder);
		//echo $rekanan->query;exit;
		$output = array(
			"sEcho" => intval($_GET['sEcho']),
			"iTotalRecords" => $allRecord,
			"iTotalDisplayRecords" => $allRecordFilter,
			"aaData" => array()
		);

		while($rekanan->nextRow())
		{
			$row = array();
			for ( $i=0 ; $i<count($aColumns) ; $i++ )
			{
				if($aColumns[$i]=='NO')
						$row[] = $number;
					elseif($aColumns[$i]=='TANGGAL_DAFTAR' || $aColumns[$i]=='TANGGAL_VALIDASI' || $aColumns[$i]=='TANGGAL_HAPUS')
									$row[] = strtoupper(getFormattedDateJson($rekanan->getField(trim($aColumns[$i]))));
						else						$row[] = strtoupper($rekanan->getField(trim($aColumns[$i])));
			}
			$output['aaData'][] = $row;
		}
		echo json_encode( $output );
	}

	function daftar_rekanan_valid_json()
	{
		$this->load->model("Rekanan");
		$rekanan = new Rekanan();
		$rekananDetail = new Rekanan();
		$cekUpdateAllow = new Rekanan();

		ini_set("memory_limit","500M");
		ini_set('max_execution_time', 520);

		$reqKeterangan = $this->input->post("reqKeterangan");
		$reqId = $this->input->post("reqId");
		$reqSearch = $this->input->get("reqSearch");
		$reqMode = $this->input->get("reqMode");

		$aColumns 			= array('REKANAN_ID', 'KODE', 'SAP_KODE', 'NAMA','KOTA','TANGGAL_DAFTAR','USER_VALIDASI', 'USER_STATUS','USER_AKTIF','USER_LOGIN_ID');
		$aColumnsAlias		= array('REKANAN_ID', 'KODE', 'SAP_KODE', 'NAMA','KOTA','TANGGAL_DAFTAR','USER_VALIDASI', 'USER_STATUS','USER_AKTIF','USER_LOGIN_ID');

		if ( isset( $_GET['iSortCol_0'] ) )
		{
			$sOrder = " ORDER BY ";

			for ( $i=0 ; $i<intval( $_GET['iSortingCols'] ) ; $i++ )
			{
				if ( $_GET[ 'bSortable_'.intval($_GET['iSortCol_'.$i]) ] == "true" )
				{
					$sOrder .= $aColumnsAlias[ intval( $_GET['iSortCol_'.$i] ) ];

					if (substr_compare(( $_GET['sSortDir_'.$i] ), "asc", 0) == 0)
					{
						$sOrder .=" asc, ";
					}else
					{
						$sOrder .=" desc, ";
					}
				}
			}
			$sOrder = substr_replace( $sOrder, "", -2 );

			if ( trim($sOrder) == "ORDER BY rekanan_ELIMINASI desc" )
			{
				$sOrder = " ORDER BY A.rekanan asc";

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

		$statement .= "  AND STATUS_VALIDASI = 1 ";
		//if($reqMode)	$arr_filter .= " AND D.BIDANG_USAHA_ID LIKE '".$reqMode."%' ";
		if($reqMode)	$arr_filter .= " AND EXISTS (SELECT 1
					FROM REKANAN_BIDANG_USAHA X
					WHERE X.REKANAN_ID = A.REKANAN_ID AND X.BIDANG_USAHA_ID LIKE '".$reqMode."%') ";

		$statement .= "AND (UPPER(A.NAMA) LIKE '%".strtoupper($_GET['sSearch'])."%' OR UPPER(B.NAMA) LIKE '".strtoupper($_GET['sSearch'])."%')";
		$allRecord = $rekanan->getCountByParams(array(), $statement);
		if($_GET['sSearch'] == "")
			$allRecordFilter = $allRecord;
		else
			$allRecordFilter =  $rekanan->getCountByParams(array(), $statement);

		$rekanan->selectByParams2(array(), $dsplyRange, $dsplyStart, $statement, $sOrder);

		$output = array(
			"sEcho" => intval($_GET['sEcho']),
			"iTotalRecords" => $allRecord,
			"iTotalDisplayRecords" => $allRecordFilter,
			"aaData" => array()
		);

		while($rekanan->nextRow())
		{
			$rekananDetail->selectByParams(array("A.REKANAN_ID" => $rekanan->getField(trim('REKANAN_ID'))));
			$rekananDetail->firstRow();
			$dateValidasi2 = explode(' ',$rekananDetail->getField(trim('DATE_VALIDASI_2')));

			$cekUpdateAllow->selectByParamsURLValidasiAllow(array("A.REKANAN_ID" => $rekanan->getField(trim('REKANAN_ID'))));
			$cekUpdateAllow->firstRow();

			$row = array();
			for ( $i=0 ; $i<count($aColumns) ; $i++ )
			{
				if($aColumns[$i]=='USER_VALIDASI') {
					$explodeRekanan = explode("||", $rekanan->getField(trim($aColumns[$i])));
				}

				if($aColumns[$i]=='NO') {
					$row[] = $number;
				} elseif($aColumns[$i]=='TANGGAL_DAFTAR') {
					$row[] = '<small> <span class="badge badge-primary">Daftar: '.strtoupper(getFormattedDateJson($rekanan->getField(trim($aColumns[$i])))).'</span><br>
						<span class="badge badge-warning">Approve: '.strtoupper(getFormattedDateJson($rekananDetail->getField(trim('TANGGAL_VALIDASI')))).'</span></small>';

				} elseif($aColumns[$i]=='USER_VALIDASI') {
					$row[] = $explodeRekanan[1].'<br><small>'.$explodeRekanan[2].'</small>';
				} elseif($aColumns[$i]=='USER_STATUS') {
					if($rekanan->getField(trim($aColumns[$i])) == '1') {
						if ($cekUpdateAllow->getField('UPDATED_DATE')) {
							$exPlodeDate = explode(' ',$cekUpdateAllow->getField('UPDATED_DATE'));
							$row[] = '<img src="images/centang.png"> <br>'.
									 '<small><span class="badge badge-dark"><i>Last Change</i><br>'.strtoupper(getFormattedDateJson($exPlodeDate[0])).'</span></small>';
						} else {
							$row[] = '<img src="images/centang.png">';
						}
					}
					else {
						if ($this->USER_TYPE_ID == 2) { // yang bisa hanya user VMS
						$row[] = '<img src="images/uncentang.png"><br>
								  <a href="'.base_url('main/index/daftar_rekanan_akses?reqId='.$rekanan->getField(trim('REKANAN_ID'))).'" class="badge badge-danger">Hak Akses</a>';
						} else {
						$row[] = '<img src="images/uncentang.png">';
						}
					}
				} elseif($aColumns[$i]=='USER_AKTIF') {
					if($rekanan->getField(trim($aColumns[$i])) == '1')
						$row[] = '<img src="images/centang.png">';
					else
						$row[] = '<img src="images/uncentang.png">';
				} else {
					$row[] = strtoupper($rekanan->getField(trim($aColumns[$i])));
				}
			}
			$output['aaData'][] = $row;
		}
		echo json_encode( $output );
	}

	function set_akses()
	{
		$this->load->library("kauth");  $userLogin = new kauth();
		$this->load->model("Rekanan");
		/* create objects */
		$rekanan = new Rekanan();

		$reqId			= $this->input->post("reqId");
		$reqUrl			= $this->input->post("reqUrl");

		if ($reqUrl) {
			$url = implode(',', $reqUrl);
		} else {
			$url = '';
		}

		$rekanan->setField("REKANAN_ID", $reqId);
		$rekanan->setField("ALLOW_URL", $url);
		$rekanan->setField('CREATED_BY', $this->USER_LOGIN_ID);

		if($rekanan->updateAllowUrl())
		{
			echo "Data berhasil disimpan.";
		}
	}

	function set_email_kode_verifikasi()
	{
		$email			= $this->input->post("mail");
		// echo $email; die();
		$this->load->library("kauth");  $userLogin = new kauth();
		$this->load->model("Rekanan");
		$nomor = $this->generateInvoice();
		/* create objects */
		$rekanan = new Rekanan();
		// echo $this->generateInvoice(); die;
		$rekanan->setField("EMAIL_KODE_VERIFIKASI", $nomor);
		$rekanan->setField('REKANAN_ID', $this->ID);
		$rekanan->setField('CREATED_BY', $this->USER_LOGIN_ID);

		if($rekanan->updateEmailKodeVerifikasi())
		{
			$this->load->library("KMail");
			$mail = new KMail();
			$mail->AddAddress($email,'Kode Konfirmasi');
			$mail->Subject  =  "Kode Konfirmasi Perubahan Email";
			$body = file_get_contents(SYSTEM_URL_EMAIL."/main/loadUrl/email/konfirmasi_kode_perubahan_email/".$nomor."");
			// echo $body; die;
			$mail->MsgHTML($body);
			if($mail->Send())
			{
				echo "Sukses||Kode Berhasil di kirim.";
			}
			else {
				echo "Gagal||Kode Gagal di kirim.";
			}
			// redirect(base_url('main/index/data_administrasi_umum_ubah_profile_email'));
		} else {
			echo "Gagal||Kode Gagal di kirim, silahkan hubungi administrator";
		}

	}

	public function data_administrasi_umum_ubah_profile_email()
	{
		$reqEmail		= $this->input->post("reqEmail");
		$nomor			= $this->input->post("reqEmailKodeVerifikasi");

		$this->load->library("kauth");  $userLogin = new kauth();
		$this->load->model("Rekanan");
		$rekanan = new Rekanan();
		// echo $this->generateInvoice(); die;
		$rekanan->setField("EMAIL_KODE_VERIFIKASI", $nomor);
		$rekanan->setField("EMAIL", $reqEmail);
		$rekanan->setField('REKANAN_ID', $this->ID);
		$rekanan->setField('CREATED_BY', $this->USER_LOGIN_ID);

		if($rekanan->updateEmail())
		{
			echo "Email Berhasil di ubah.";
		} else {
			echo "Email Gagal di ubah.";
		}

	}

	function generateInvoice()
	{
		$kataAwal = 'JT-';
		$date = date('ymd');
		$acak = $this->incrementalHash();

		return $kataAwal.$acak;
	}

	function incrementalHash(){
	  	$seed = str_split('abcdefghijklmnpqrstuvwxyz'
                 .'ABCDEFGHJKLMNPQRSTUVWXYZ'
                 .'123456789'); // and any other characters
		shuffle($seed); // probably optional since array_is randomized; this may be redundant
		$rand = '';
		foreach (array_rand($seed, 6) as $k) $rand .= $seed[$k];

		return $rand;
	}

	function check_kode_email()
	{
		$reqParam1 = $this->input->post("reqParam1");

		if($reqParam1 == "")
			exit;

		$this->load->model("Rekanan");
		$rekanan = new Rekanan();

		$adaData = $rekanan->getCountByParams(array("A.EMAIL_KODE_VERIFIKASI" => $reqParam1));
		if($adaData == 0)
			echo "false";
		else
			echo "true";

	}

	function get_data_tambah_rekanan_email()
	{
		$this->load->library("kauth");  $userLogin = new kauth();
		$this->load->model("Rekanan");
		$rekanan = new Rekanan();

		$reqId = $this->input->post("reqId");
		$reqNamaRekanan = $this->input->post("reqNamaRekanan");

		/* LOGIN CHECK */
		if ($userLogin->checkUserLogin())
		{
			$userLogin->retrieveUserInfo();
		}

		//$rekanan->selectByParams(array());
		if($reqNamaRekanan == ''){
			if($reqId > 0)
			$rekanan->selectByParams(array("STATUS_VALIDASI"=>1), -1, -1, " AND NOT REKANAN_ID IN (".$reqId.")");
			else
			$rekanan->selectByParams(array("STATUS_VALIDASI"=>1), -1, -1);
			//echo $rekanan->query;
		}else{
			if($reqId > 0)
			$rekanan->selectByParams(array("STATUS_VALIDASI"=>1), -1, -1, " AND (UPPER(C.NAMA || ' ' || A.NAMA)) LIKE '%".strtoupper($reqNamaRekanan)."%' AND NOT REKANAN_ID IN (".$reqId.")");
			else
			$rekanan->selectByParams(array("STATUS_VALIDASI"=>1), -1, -1, " AND (UPPER(C.NAMA || ' ' || A.NAMA)) LIKE '%".strtoupper($reqNamaRekanan)."%'");
		}
		$met = array();
		$i=0;

		while($rekanan->nextRow()){
			$met[$i]['NAMA'] = $rekanan->getField('NAMA');
			$met[$i]['EMAIL'] = $rekanan->getField('EMAIL');
			$met[$i]['REKANAN_ID'] = $rekanan->getField('REKANAN_ID');
			$i++;
		}
		echo json_encode($met);
	}

	function master_user_whatsapp_monitoring_json()
	{
		$this->load->library("kauth");  $userLogin = new kauth();
		$this->load->model("Rekanan");
		$rekanan = new Rekanan();

		ini_set("memory_limit","500M");
		ini_set('max_execution_time', 5200);

		$reqStatus= $this->input->post("reqStatus");
		$reqBidangUsaha = $this->input->post("reqBidangUsaha");

		$aColumns = array("REKANAN_ID", "KODE", "NAMA", "WHATSAPP");
		$aColumnsAlias = array("A.REKANAN_ID", "A.KODE", "A.NAMA", "A.WHATSAPP");

		if ( isset( $_GET['iSortCol_0'] ) )
		{
			$sOrder = " ORDER BY ";

			for ( $i=0 ; $i<intval( $_GET['iSortingCols'] ) ; $i++ )
			{
				if ( $_GET[ 'bSortable_'.intval($_GET['iSortCol_'.$i]) ] == "true" )
				{
					$sOrder .= $aColumnsAlias[ intval( $_GET['iSortCol_'.$i] ) ];

					if (substr_compare(( $_GET['sSortDir_'.$i] ), "asc", 0) == 0)
					{
						$sOrder .=" asc, ";
					}else
					{
						$sOrder .=" desc, ";
					}
				}
			}
			$sOrder = substr_replace( $sOrder, "", -2 );

			if ( trim($sOrder) == "ORDER BY A.REKANAN_ID asc" )
			{
				$sOrder = " ORDER BY A.NAMA ASC ";

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


		$searchJson= " AND (UPPER(A.NAMA) LIKE '%".strtoupper($_GET['sSearch'])."%' OR UPPER(A.KODE) LIKE '%".strtoupper($_GET['sSearch'])."%') ";

		$statement .= "   AND NOT COALESCE(A.STATUS_VALIDASI, 0) IN (0, 2) AND WHATSAPP IS NOT NULL ";

		$allRecord = $rekanan->getCountByParams(array(),$statement);
		if($_GET['sSearch'] == "")
			$allRecordFilter = $allRecord;
		else
			$allRecordFilter = $rekanan->getCountByParams(array(), $statement.$searchJson);

		$rekanan->selectByParams(array(), $dsplyRange, $dsplyStart, $statement.$searchJson, $sOrder);
		//echo $rekanan->query;
		/* Output */
		$output = array(
			"sEcho" => intval($_GET['sEcho']),
			"iTotalRecords" => $allRecord,
			"iTotalDisplayRecords" => $allRecordFilter,
			"aaData" => array()
		);
		while($rekanan->nextRow())
		{
			$row = array();
			for ( $i=0 ; $i<count($aColumns) ; $i++ )
			{
				if($aColumns[$i] == "TANGGAL")
					$row[] = getFormattedDate($rekanan->getField($aColumns[$i]));
				else if($aColumns[$i] == "KETERANGAN")
					$row[] = truncate($rekanan->getField($aColumns[$i]), 5)."...";
				else if($aColumns[$i] == "NAMA")
					$row[] = strtoupper($rekanan->getField($aColumns[$i]));
				else
					$row[] = $rekanan->getField($aColumns[$i]);
			}

			$output['aaData'][] = $row;
		}

		echo json_encode( $output );
	}

	function reload_kualifikasi()
	{

		$this->load->library("kauth");  $userLogin = new kauth();
		$this->load->model("Rekanan");
		$rekanan = new Rekanan();
		$rekanan->selectByParams(array("A.REKANAN_ID"=>$this->ID),-1,-1);
		$rekanan->firstRow();

		$tempKualifikasiNama = $rekanan->getField("REKANAN_KUALIFIKASI");

		$i = 0;
		$met[$i]['KUALIFIKASI'] = $tempKualifikasiNama;
		echo json_encode($met);

	}

	function reload_PKP()
	{
		$this->load->library("kauth");  $userLogin = new kauth();
		$this->load->model("Rekanan");
		$rekanan = new Rekanan();
		$rekanan->selectByParams(array("A.REKANAN_ID"=>$this->ID),-1,-1);
		$rekanan->firstRow();

		$tempNoSurat_PKP = $rekanan->getField("PKP");
		$tempTanggal_PKP = dateToPageCheck($rekanan->getField("PKP_TANGGAL"));
		$tempJabatan_PKP = $rekanan->getField("NPWP");

		$i = 0;
		$met[$i]['PKP'] = $tempNoSurat_PKP;
		$met[$i]['PKP_TANGGAL'] = $tempTanggal_PKP;
		$met[$i]['NPWP'] = $tempJabatan_PKP;
		echo json_encode($met);
	}

	function data_administrasi_umum_ubah()
	{
		$this->load->library("kauth");  $userLogin = new kauth();
		$this->load->model("Rekanan");
		$this->load->model("Region");
		$this->load->model("Bank");
		$this->load->model("Incoterm");
		$this->load->model("PaymentMethod");
		$this->load->model("MataUang");
		$this->load->library("crfs_protect"); $csrf = new crfs_protect();

		/* create objects */
		$rekanan = new Rekanan();

		$reqId			= $this->input->post("reqId");
		$reqRekananTipe	= $this->input->post("reqRekananTipe");
		$reqNama	= $this->input->post("reqNama");
		$reqAlamat	= $this->input->post("reqAlamat");
		$reqKota	= $this->input->post("reqKota");
		$reqNPWP	= $this->input->post("reqNPWP");
		$reqStatus	= $this->input->post("reqStatus");
		$reqTeleponKode	= $this->input->post("reqTeleponKode");
		$reqTeleponNo	= $this->input->post("reqTeleponNo");
		$reqFaxKode	= $this->input->post("reqFaxKode");
		$reqFaxNo	= $this->input->post("reqFaxNo");
		$reqMail	= $this->input->post("reqMail");
		$reqKualifikasi	= $this->input->post("reqKualifikasi");
		$reqAlamatPusat	= $this->input->post("reqAlamatPusat");
		$reqTeleponKodePusat	= $this->input->post("reqTeleponKodePusat");
		$reqTeleponNoPusat	= $this->input->post("reqTeleponNoPusat");
		$reqFaxKodePusat	= $this->input->post("reqFaxKodePusat");
		$reqFaxNoPusat	= $this->input->post("reqFaxNoPusat");
		$reqMailPusat	= $this->input->post("reqMailPusat");
		$reqNoRekening  = $this->input->post("reqNoRekening");
		$reqAtasNama  = $this->input->post("reqAtasNama");
		$reqBankId  = $this->input->post("reqBankId");
		$reqKodepos	= $this->input->post("reqKodepos");
		$reqRegionId = $this->input->post("reqRegionId");
		$reqIncoterm1 = $this->input->post("reqIncoterm1");
		$reqIncoterm2 = $this->input->post("reqIncoterm2");
		$reqPaymentMethodId = $this->input->post("reqPaymentMethodId");
		$reqMataUang = $this->input->post("reqMataUang");
		$reqKontakPerson = $this->input->post("reqKontakPerson");
		$reqKontakPersonHp = $this->input->post("reqKontakPersonHp");
		$reqWebsite = $this->input->post("reqWebsite");


		$reqSubmit	= $this->input->post("reqSubmit");
		$reqSimpan	= $this->input->post("reqSimpan");
		$reqBatal	= $this->input->post("reqBatal");


		if ($csrf->isTokenValid($_POST['_csrf']))
		{
			//echo $reqNPWP;
			$rekanan->setField("REKANAN_ID", $this->ID);
			$rekanan->setField("REKANAN_TIPE_ID",$reqRekananTipe);
			$rekanan->setField("NAMA",$reqNama);
			$rekanan->setField("NPWP",$reqNPWP);
			$rekanan->setField("STATUS_PERUSAHAAN",$reqStatus);
			$rekanan->setField("ALAMAT",$reqAlamat);
			$rekanan->setField("KOTA",$reqKota);
			$rekanan->setField("TELEPON_KODE",$reqTeleponKode);
			$rekanan->setField("TELEPON",$reqTeleponNo);
			$rekanan->setField("FAX_KODE",$reqFaxKode);
			$rekanan->setField("FAX",$reqFaxNo);
			$rekanan->setField("EMAIL",$reqMail);
			$rekanan->setField("REKANAN_KUALIFIKASI_ID",$reqKualifikasi);

			$rekanan->setField("ALAMAT_PUSAT",$reqAlamatPusat);
			$rekanan->setField("TELEPON_KODE_PUSAT",$reqTeleponKodePusat);
			$rekanan->setField("TELEPON_PUSAT",$reqTeleponNoPusat);
			$rekanan->setField("FAX_KODE_PUSAT",$reqFaxKodePusat);
			$rekanan->setField("FAX_PUSAT",$reqFaxNoPusat);
			$rekanan->setField("EMAIL_PUSAT",$reqMailPusat);
			$rekanan->setField("KODEPOS",$reqKodepos);
			$rekanan->setField("REGION_ID",$reqRegionId);
			$rekanan->setField("BANK_ID",$reqBankId);
			$rekanan->setField("BANK_REKENING",$reqNoRekening);
			$rekanan->setField("BANK_PEMILIK",$reqAtasNama);
			$rekanan->setField("INCOTERM_ID",$reqIncoterm1);
			$rekanan->setField("INCOTERM2",$reqIncoterm2);
			$rekanan->setField("PAYMENT_METHOD_ID",$reqPaymentMethodId);
			$rekanan->setField("MATA_UANG_KODE",$reqMataUang);
			$rekanan->setField("KONTAK_PERSON_HP",$reqKontakPersonHp);
			$rekanan->setField("KONTAK_PERSON",$reqKontakPerson);
			$rekanan->setField("WEBSITE",$reqWebsite);

			if($rekanan->update())
			{
				echo "Data berhasil disimpan.";
			}
		}
	}

	function data_administrasi_umum_ubah_profile()
	{
		// echo "<pre>"; print_r($this->input->post()); die;

		$this->load->library("kauth");  $userLogin = new kauth();
		$this->load->model("Rekanan");
		$this->load->model("Region");
		$this->load->model("Bank");
		$this->load->model("Incoterm");
		$this->load->model("PaymentMethod");
		$this->load->model("MataUang");
		$this->load->library("crfs_protect"); $csrf = new crfs_protect();
		$this->load->library("FileHandler");
		$file = new FileHandler();

		/* create objects */
		$rekanan = new Rekanan();
		$reqId			= $this->input->post("reqId");
		$reqRekananTipe	= $this->input->post("reqRekananTipe");
		$reqNama	= $this->input->post("reqNama");
		$reqAlamat	= $this->input->post("reqAlamat");
		$reqKota	= $this->input->post("reqKota");
		$reqNPWP	= $this->input->post("reqNPWP");
		$reqMasaBerlakuPKP	= $this->input->post("reqMasaBerlakuPKP");
		$reqPKP		= $this->input->post("reqPKP");
		$reqStatus	= $this->input->post("reqStatus");
		$reqTeleponKode	= $this->input->post("reqTeleponKode");
		$reqTeleponNo	= $this->input->post("reqTeleponNo");
		$reqFaxKode	= $this->input->post("reqFaxKode");
		$reqFaxNo	= $this->input->post("reqFaxNo");
		$reqMail	= $this->input->post("reqMail");
		$reqKualifikasi	= $this->input->post("reqKualifikasi");
		$reqAlamatPusat	= $this->input->post("reqAlamatPusat");
		$reqTeleponKodePusat	= $this->input->post("reqTeleponKodePusat");
		$reqTeleponNoPusat	= $this->input->post("reqTeleponNoPusat");
		$reqFaxKodePusat	= $this->input->post("reqFaxKodePusat");
		$reqFaxNoPusat	= $this->input->post("reqFaxNoPusat");
		$reqMailPusat	= $this->input->post("reqMailPusat");
		$reqNoRekening  = $this->input->post("reqNoRekening");
		$reqAtasNama  = $this->input->post("reqAtasNama");
		$reqBankId  = $this->input->post("reqBankId");
		$reqKodepos	= $this->input->post("reqKodepos");
		$reqRegionId = $this->input->post("reqRegionId");
		$reqIncoterm1 = $this->input->post("reqIncoterm1");
		$reqIncoterm2 = $this->input->post("reqIncoterm2");
		$reqPaymentMethodId = $this->input->post("reqPaymentMethodId");
		$reqMataUang = $this->input->post("reqMataUang");
		$reqKontakPerson = $this->input->post("reqKontakPerson");
		$reqKontakPersonHp = $this->input->post("reqKontakPersonHp");
		$reqWebsite = $this->input->post("reqWebsite");
		$reqNPWPFile		= $_FILES['reqNPWPFile'];
		$reqNPWPFileTemp = $this->input->post("reqNPWPFileTemp");
		$reqNamaFileNPWP = $this->input->post("reqNamaFileNPWP");
		$reqPKPFile		= $_FILES['reqPKPFile'];
		$reqPKPFileTemp = $this->input->post("reqPKPFileTemp");
		$reqNamaFilePKP = $this->input->post("reqNamaFilePKP");
		$reqBankCabang = $this->input->post("reqBankCabang");

		// 7 Sept 2023
		$reqStatusPKP	= $this->input->post("reqStatusPKP");
		$reqSKTPKP	= $this->input->post("reqSKTPKP");
		$reqSKTPKPFile		= $_FILES['reqSKTPKPFile'];
		$reqSKTPKPFileTemp = $this->input->post("reqSKTPKPFileTemp");
		$reqNamaFileSKTPKP = $this->input->post("reqNamaFileSKTPKP");

		// 6 Jan 2024
		$reqNonPKPFile		= $_FILES['reqNonPKPFile'];
		$reqNonPKPFileTemp = $this->input->post("reqNonPKPFileTemp");
		$reqNamaFileNonPKP = $this->input->post("reqNamaFileNonPKP");

		$reqSubmit	= $this->input->post("reqSubmit");
		$reqSimpan	= $this->input->post("reqSimpan");
		$reqBatal	= $this->input->post("reqBatal");


		$reqRegionId	= $this->input->post("reqRegionId");
		$reqKota	= $this->input->post("reqKota");
		$reqKecamatan	= $this->input->post("reqKecamatan");
		$reqKelurahan	= $this->input->post("reqKelurahan");

		// Company Profile
		$reqLinkFile2 = $_FILES['reqLinkFile2'];
		$reqLinkFile2TempNama 	= $_POST["reqLinkFile2TempNama"];


		if ($csrf->isTokenValid($_POST['_csrf']))
		{

			$FILE_DIR = "uploads/rekanan/";

			// Upload NON PKP
			$renameFileNONPKP = 'NON_PKP_'.md5(date("dmYHis").$reqNonPKPFile['name'].$this->ID).".".getExtension($reqNonPKPFile['name']);
			if($file->uploadToDir('reqNonPKPFile', $FILE_DIR, $renameFileNONPKP))
			{
				$insertLinkFileNONPKP =  $renameFileNONPKP;
				$insertLinkFileNONPKPNama = $reqNonPKPFile['name'];
			}
			else
			{
				$insertLinkFileNONPKP =  $reqNonPKPFileTemp;
				$insertLinkFileNONPKPNama = $reqNamaFileNonPKP;
			}
			/* END Upload NON PKP */
			$rekanan->setField("NON_PKP_FILE", $insertLinkFileNONPKP);
			$rekanan->setField("NAMA_NON_PKP_FILE", $insertLinkFileNONPKPNama);

			// Upload SKT PKP
			$renameFileSKTPKP = 'SKT_PKP_'.md5(date("dmYHis").$reqSKTPKPFile['name'].$this->ID).".".getExtension($reqSKTPKPFile['name']);
			if($file->uploadToDir('reqSKTPKPFile', $FILE_DIR, $renameFileSKTPKP))
			{
				$insertLinkFilePKP =  $renameFileSKTPKP;
				$insertLinkFilePKPNama = $reqSKTPKPFile['name'];
			}
			else
			{
				$insertLinkFilePKP =  $reqSKTPKPFileTemp;
				$insertLinkFilePKPNama = $reqNamaFileSKTPKP;
			}
			/* END Upload SKT PKP */
			$rekanan->setField("STATUS_PKP", $reqStatusPKP);
			$rekanan->setField("SKT_PKP_NOMOR", $reqSKTPKP);
			$rekanan->setField("SKT_PKP_FILE", $insertLinkFilePKP);
			$rekanan->setField("NAMA_SKT_PKP_FILE", $insertLinkFilePKPNama);

			$renameFileNPWP = md5(date("dmYHis").$reqNPWPFile['name'].$this->ID).".".getExtension($reqNPWPFile['name']);
			if($file->uploadToDir('reqNPWPFile', $FILE_DIR, $renameFileNPWP))
			{
				$insertLinkFileNPWP =  $renameFileNPWP;
				$insertLinkFileNPWPNama = $reqNPWPFile['name'];
			}
			else
			{
				$insertLinkFileNPWP =  $reqNPWPFileTemp;
				$insertLinkFileNPWPNama = $reqNamaFileNPWP;
			}
			/* END UPLOAD FILE */
			$rekanan->setField("NPWP_FILE", $insertLinkFileNPWP);
			$rekanan->setField("NAMA_FILE_NPWP", $insertLinkFileNPWPNama);

			$renameFilePKP = md5(date("dmYHis").$reqPKPFile['name'].$this->ID).".".getExtension($reqPKPFile['name']);
			if($file->uploadToDir('reqPKPFile', $FILE_DIR, $renameFilePKP))
			{
				$insertLinkFilePKP =  $renameFilePKP;
				$insertLinkFilePKPNama = $reqPKPFile['name'];
			}
			else
			{
				$insertLinkFilePKP =  $reqPKPFileTemp;
				$insertLinkFilePKPNama = $reqNamaFilePKP;
			}
			/* END UPLOAD FILE */
			$rekanan->setField("PKP_FILE", $insertLinkFilePKP);
			$rekanan->setField("NAMA_FILE_PKP", $insertLinkFilePKPNama);

			// Upload COMPANY PROFILE
			$renameFileCP = 'COMPANY_PROFILE_'.md5(date("dmYHis").$reqLinkFile2['name'].$this->ID).".".getExtension($reqLinkFile2['name']);
			if($file->uploadToDir('reqLinkFile2', $FILE_DIR, $renameFileCP))
			{
				$reqLinkFile2 =  $renameFileCP;
				$reqLinkFile2Nama = $reqLinkFile2['name'];
			}
			else
			{
				$reqLinkFile2 =  $reqLinkFile2TempNama;
				$reqLinkFile2Nama = $reqNamaFileNonPKP;
			}
			/* END Upload COMPANY PROFILE */
			$rekanan->setField("COMPANY_PROFILE_FILE", $reqLinkFile2);

			//echo $reqNPWP;
			$rekanan->setField('CREATED_BY', $this->USER_LOGIN_ID);
			$rekanan->setField("REKANAN_ID", $this->ID);
			$rekanan->setField("REKANAN_TIPE_ID",$reqRekananTipe);
			$rekanan->setField("NAMA",$reqNama);
			$rekanan->setField("NPWP",$reqNPWP);
			$rekanan->setField("PKP_TANGGAL",dateToDBCheck($reqMasaBerlakuPKP));
			$rekanan->setField("PKP",$reqPKP);
			$rekanan->setField("STATUS_PERUSAHAAN",$reqStatus);
			$rekanan->setField("ALAMAT",$reqAlamat);
			$rekanan->setField("KOTA",$reqKota);
			$rekanan->setField("TELEPON_KODE",$reqTeleponKode);
			$rekanan->setField("TELEPON",$reqTeleponNo);
			$rekanan->setField("FAX_KODE",$reqFaxKode);
			$rekanan->setField("FAX",$reqFaxNo);
			$rekanan->setField("EMAIL",$reqMail);
			$rekanan->setField("REKANAN_KUALIFIKASI_ID",$reqKualifikasi);

			$rekanan->setField("ALAMAT_PUSAT",$reqAlamatPusat);
			$rekanan->setField("TELEPON_KODE_PUSAT",$reqTeleponKodePusat);
			$rekanan->setField("TELEPON_PUSAT",$reqTeleponNoPusat);
			$rekanan->setField("FAX_KODE_PUSAT",$reqFaxKodePusat);
			$rekanan->setField("FAX_PUSAT",$reqFaxNoPusat);
			$rekanan->setField("EMAIL_PUSAT",$reqMailPusat);
			$rekanan->setField("KODEPOS",$reqKodepos);
			$rekanan->setField("REGION_ID",$reqRegionId);
			$rekanan->setField("BANK_ID",$reqBankId);
			$rekanan->setField("BANK_REKENING",$reqNoRekening);
			$rekanan->setField("BANK_PEMILIK",$reqAtasNama);
			$rekanan->setField("INCOTERM_ID",$reqIncoterm1);
			$rekanan->setField("INCOTERM2",$reqIncoterm2);
			$rekanan->setField("PAYMENT_METHOD_ID",$reqPaymentMethodId);
			$rekanan->setField("MATA_UANG_KODE",$reqMataUang);
			$rekanan->setField("KONTAK_PERSON_HP",$reqKontakPersonHp);
			$rekanan->setField("KONTAK_PERSON",$reqKontakPerson);
			$rekanan->setField("WEBSITE",$reqWebsite);
			$rekanan->setField("BANK_CABANG",$reqBankCabang);

			$rekanan->setField("NAMAPROPINSI",$reqRegionId);
			$rekanan->setField("NAMAKABKOT",$reqKota);
			$rekanan->setField("NAMAKECAMATAN",$reqKecamatan);
			$rekanan->setField("KELURAHAN",$reqKelurahan);

			if($rekanan->updateprofile())
			{
				echo "Data berhasil disimpan.";
			}
		}
	}

	function data_administrasi_umum_ubah_profile_perorangan()
	{
		$this->load->library("kauth");  $userLogin = new kauth();
		$this->load->model("Rekanan");
		$this->load->model("Region");
		$this->load->model("Bank");
		$this->load->model("Incoterm");
		$this->load->model("PaymentMethod");
		$this->load->model("MataUang");
		$this->load->library("crfs_protect"); $csrf = new crfs_protect();
		$this->load->library("FileHandler");
		$file = new FileHandler();

		/* create objects */
		$rekanan = new Rekanan();

		$reqId			= $this->input->post("reqId");
		$reqRekananTipe	= $this->input->post("reqRekananTipe");
		$reqNama	= $this->input->post("reqNama");
		$reqAlamat	= $this->input->post("reqAlamat");
		$reqKota	= $this->input->post("reqKota");
		$reqNPWP	= $this->input->post("reqNPWP");
		$reqKTP	= $this->input->post("reqKTP");
		$reqStatus	= $this->input->post("reqStatus");
		$reqTeleponKode	= $this->input->post("reqTeleponKode");
		$reqTeleponNo	= $this->input->post("reqTeleponNo");
		$reqFaxKode	= $this->input->post("reqFaxKode");
		$reqFaxNo	= $this->input->post("reqFaxNo");
		$reqMail	= $this->input->post("reqMail");
		$reqKualifikasi	= $this->input->post("reqKualifikasi");
		$reqAlamatPusat	= $this->input->post("reqAlamatPusat");
		$reqTeleponKodePusat	= $this->input->post("reqTeleponKodePusat");
		$reqTeleponNoPusat	= $this->input->post("reqTeleponNoPusat");
		$reqFaxKodePusat	= $this->input->post("reqFaxKodePusat");
		$reqFaxNoPusat	= $this->input->post("reqFaxNoPusat");
		$reqMailPusat	= $this->input->post("reqMailPusat");
		$reqNoRekening  = $this->input->post("reqNoRekening");
		$reqAtasNama  = $this->input->post("reqAtasNama");
		$reqBankId  = $this->input->post("reqBankId");
		$reqKodepos	= $this->input->post("reqKodepos");
		$reqRegionId = $this->input->post("reqRegionId");
		$reqIncoterm1 = $this->input->post("reqIncoterm1");
		$reqIncoterm2 = $this->input->post("reqIncoterm2");
		$reqPaymentMethodId = $this->input->post("reqPaymentMethodId");
		$reqMataUang = $this->input->post("reqMataUang");
		$reqKontakPerson = $this->input->post("reqKontakPerson");
		$reqKontakPersonHp = $this->input->post("reqKontakPersonHp");
		$reqWebsite = $this->input->post("reqWebsite");
		$reqKTPFile		= $_FILES['reqKTPFile'];
		$reqKTPFileTemp = $this->input->post("reqKTPFileTemp");
		$reqNamaFileKTP = $this->input->post("reqNamaFileKTP");
		$reqNPWPFile		= $_FILES['reqNPWPFile'];
		$reqNPWPFileTemp = $this->input->post("reqNPWPFileTemp");
		$reqNamaFileNPWP = $this->input->post("reqNamaFileNPWP");
		$reqMasaBerlakuPKP	= $this->input->post("reqMasaBerlakuPKP");
		$reqPKP		= $this->input->post("reqPKP");
		$reqPKPFile		= $_FILES['reqPKPFile'];
		$reqPKPFileTemp = $this->input->post("reqPKPFileTemp");
		$reqNamaFilePKP = $this->input->post("reqNamaFilePKP");

		$reqSubmit	= $this->input->post("reqSubmit");
		$reqSimpan	= $this->input->post("reqSimpan");
		$reqBatal	= $this->input->post("reqBatal");

		$reqRegionId	= $this->input->post("reqRegionId");
		$reqKota	= $this->input->post("reqKota");
		$reqKecamatan	= $this->input->post("reqKecamatan");
		$reqKelurahan	= $this->input->post("reqKelurahan");
		$reqBankCabang = $this->input->post("reqBankCabang");
			

		if ($csrf->isTokenValid($_POST['_csrf']))
		{
			$FILE_DIR = "uploads/rekanan/";
			$renameFile = md5(date("dmYHis").$reqKTPFile['name'].$this->ID).".".getExtension($reqKTPFile['name']);
			if($file->uploadToDir('reqKTPFile', $FILE_DIR, $renameFile))
			{
				$insertLinkFile =  $renameFile;
				$insertLinkFileNama = $reqKTPFile['name'];
			}
			else
			{
				$insertLinkFile =  $reqKTPFileTemp;
				$insertLinkFileNama = $reqNamaFileKTP;
			}
			/* END UPLOAD FILE */
			$rekanan->setField("KTP_FILE", $insertLinkFile);
			$rekanan->setField("NAMA_FILE_KTP", $insertLinkFileNama);

			$renameFileNPWP = md5(date("dmYHis").$reqNPWPFile['name'].$this->ID).".".getExtension($reqNPWPFile['name']);
			if($file->uploadToDir('reqNPWPFile', $FILE_DIR, $renameFileNPWP))
			{
				$insertLinkFileNPWP =  $renameFileNPWP;
				$insertLinkFileNPWPNama = $reqNPWPFile['name'];
			}
			else
			{
				$insertLinkFileNPWP =  $reqNPWPFileTemp;
				$insertLinkFileNPWPNama = $reqNamaFileNPWP;
			}
			/* END UPLOAD FILE */
			$rekanan->setField("NPWP_FILE", $insertLinkFileNPWP);
			$rekanan->setField("NAMA_FILE_NPWP", $insertLinkFileNPWPNama);

			$renameFilePKP = md5(date("dmYHis").$reqPKPFile['name'].$this->ID).".".getExtension($reqPKPFile['name']);
			if($file->uploadToDir('reqPKPFile', $FILE_DIR, $renameFilePKP))
			{
				$insertLinkFilePKP =  $renameFilePKP;
				$insertLinkFilePKPNama = $reqPKPFile['name'];
			}
			else
			{
				$insertLinkFilePKP =  $reqPKPFileTemp;
				$insertLinkFilePKPNama = $reqNamaFilePKP;
			}
			/* END UPLOAD FILE */
			$rekanan->setField("PKP_FILE", $insertLinkFilePKP);
			$rekanan->setField("NAMA_FILE_PKP", $insertLinkFilePKPNama);

			//echo $reqNPWP;
			$rekanan->setField("REKANAN_ID", $this->ID);
			$rekanan->setField("REKANAN_TIPE_ID",$reqRekananTipe);
			$rekanan->setField("NAMA",$reqNama);
			$rekanan->setField("NPWP",$reqNPWP);
			$rekanan->setField("KTP",$reqKTP);
			$rekanan->setField("PKP_TANGGAL",dateToDBCheck($reqMasaBerlakuPKP));
			$rekanan->setField("PKP",$reqPKP);
			$rekanan->setField("STATUS_PERUSAHAAN",$reqStatus);
			$rekanan->setField("ALAMAT",$reqAlamat);
			$rekanan->setField("KOTA",$reqKota);
			$rekanan->setField("TELEPON_KODE",$reqTeleponKode);
			$rekanan->setField("TELEPON",$reqTeleponNo);
			$rekanan->setField("FAX_KODE",$reqFaxKode);
			$rekanan->setField("FAX",$reqFaxNo);
			$rekanan->setField("EMAIL",$reqMail);
			$rekanan->setField("REKANAN_KUALIFIKASI_ID",$reqKualifikasi);

			$rekanan->setField("ALAMAT_PUSAT",$reqAlamatPusat);
			$rekanan->setField("TELEPON_KODE_PUSAT",$reqTeleponKodePusat);
			$rekanan->setField("TELEPON_PUSAT",$reqTeleponNoPusat);
			$rekanan->setField("FAX_KODE_PUSAT",$reqFaxKodePusat);
			$rekanan->setField("FAX_PUSAT",$reqFaxNoPusat);
			$rekanan->setField("EMAIL_PUSAT",$reqMailPusat);
			$rekanan->setField("KODEPOS",$reqKodepos);
			$rekanan->setField("REGION_ID",$reqRegionId);
			$rekanan->setField("BANK_ID",$reqBankId);
			$rekanan->setField("BANK_REKENING",$reqNoRekening);
			$rekanan->setField("BANK_PEMILIK",$reqAtasNama);
			$rekanan->setField("INCOTERM_ID",$reqIncoterm1);
			$rekanan->setField("INCOTERM2",$reqIncoterm2);
			$rekanan->setField("PAYMENT_METHOD_ID",$reqPaymentMethodId);
			$rekanan->setField("MATA_UANG_KODE",$reqMataUang);
			$rekanan->setField("KONTAK_PERSON_HP",$reqKontakPersonHp);
			$rekanan->setField("KONTAK_PERSON",$reqKontakPerson);
			$rekanan->setField("WEBSITE",$reqWebsite);
			$rekanan->setField("BANK_CABANG",$reqBankCabang);

			$rekanan->setField("NAMAPROPINSI",$reqRegionId);
			$rekanan->setField("NAMAKABKOT",$reqKota);
			$rekanan->setField("NAMAKECAMATAN",$reqKecamatan);
			$rekanan->setField("KELURAHAN",$reqKelurahan);

			if($rekanan->updateprofileperorangan2())
			{
				echo "Data berhasil disimpan.";
			}
		}
	}

	function data_administrasi_umum_perorangan_ubah()
	{
		$this->load->library("kauth");  $userLogin = new kauth();
		$this->load->model("Rekanan");
		$this->load->model("Region");
		$this->load->model("Bank");
		$this->load->model("Incoterm");
		$this->load->model("PaymentMethod");
		$this->load->model("MataUang");
		$this->load->library("crfs_protect"); $csrf = new crfs_protect();
		$this->load->library("FileHandler");
		$file = new FileHandler();

		/* create objects */
		$rekanan = new Rekanan();

		$reqId			= $this->input->post("reqId");
		$reqRekananTipe	= $this->input->post("reqRekananTipe");
		$reqNama	= $this->input->post("reqNama");
		$reqAlamat	= $this->input->post("reqAlamat");
		$reqKota	= $this->input->post("reqKota");
		$reqNPWP	= $this->input->post("reqNPWP");
		$reqKTP	= $this->input->post("reqKTP");
		$reqStatus	= $this->input->post("reqStatus");
		$reqTeleponKode	= $this->input->post("reqTeleponKode");
		$reqTeleponNo	= $this->input->post("reqTeleponNo");
		$reqFaxKode	= $this->input->post("reqFaxKode");
		$reqFaxNo	= $this->input->post("reqFaxNo");
		$reqMail	= $this->input->post("reqMail");
		$reqKualifikasi	= $this->input->post("reqKualifikasi");
		$reqAlamatPusat	= $this->input->post("reqAlamatPusat");
		$reqTeleponKodePusat	= $this->input->post("reqTeleponKodePusat");
		$reqTeleponNoPusat	= $this->input->post("reqTeleponNoPusat");
		$reqFaxKodePusat	= $this->input->post("reqFaxKodePusat");
		$reqFaxNoPusat	= $this->input->post("reqFaxNoPusat");
		$reqMailPusat	= $this->input->post("reqMailPusat");
		$reqNoRekening  = $this->input->post("reqNoRekening");
		$reqAtasNama  = $this->input->post("reqAtasNama");
		$reqBankId  = $this->input->post("reqBankId");
		$reqKodepos	= $this->input->post("reqKodepos");
		$reqRegionId = $this->input->post("reqRegionId");
		$reqIncoterm1 = $this->input->post("reqIncoterm1");
		$reqIncoterm2 = $this->input->post("reqIncoterm2");
		$reqPaymentMethodId = $this->input->post("reqPaymentMethodId");
		$reqMataUang = $this->input->post("reqMataUang");
		$reqKontakPerson = $this->input->post("reqKontakPerson");
		$reqKontakPersonHp = $this->input->post("reqKontakPersonHp");
		$reqWebsite = $this->input->post("reqWebsite");
		$reqKTPFile		= $_FILES['reqKTPFile'];
		$reqKTPFileTemp = $this->input->post("reqKTPFileTemp");
		$reqNamaFileKTP = $this->input->post("reqNamaFileKTP");
		$reqNPWPFile		= $_FILES['reqNPWPFile'];
		$reqNPWPFileTemp = $this->input->post("reqNPWPFileTemp");
		$reqNamaFileNPWP = $this->input->post("reqNamaFileNPWP");


		$reqSubmit	= $this->input->post("reqSubmit");
		$reqSimpan	= $this->input->post("reqSimpan");
		$reqBatal	= $this->input->post("reqBatal");


		if ($csrf->isTokenValid($_POST['_csrf']))
		{
			$FILE_DIR = "uploads/rekanan/";
			$renameFile = md5(date("dmYHis").$reqKTPFile['name'].$this->ID).".".getExtension($reqKTPFile['name']);
			if($file->uploadToDir('reqKTPFile', $FILE_DIR, $renameFile))
			{
				$insertLinkFile =  $renameFile;
				$insertLinkFileNama = $reqKTPFile['name'];
			}
			else
			{
				$insertLinkFile =  $reqKTPFileTemp;
				$insertLinkFileNama = $reqNamaFileKTP;
			}
			/* END UPLOAD FILE */
			$rekanan->setField("KTP_FILE", $insertLinkFile);
			$rekanan->setField("NAMA_FILE_KTP", $insertLinkFileNama);

			$renameFileNPWP = md5(date("dmYHis").$reqNPWPFile['name'].$this->ID).".".getExtension($reqNPWPFile['name']);
			if($file->uploadToDir('reqNPWPFile', $FILE_DIR, $renameFileNPWP))
			{
				$insertLinkFileNPWP =  $renameFileNPWP;
				$insertLinkFileNPWPNama = $reqNPWPFile['name'];
			}
			else
			{
				$insertLinkFileNPWP =  $reqNPWPFileTemp;
				$insertLinkFileNPWPNama = $reqNamaFileNPWP;
			}
			/* END UPLOAD FILE */
			$rekanan->setField("NPWP_FILE", $insertLinkFileNPWP);
			$rekanan->setField("NAMA_FILE_NPWP", $insertLinkFileNPWPNama);

			//echo $reqNPWP;
			$rekanan->setField("REKANAN_ID", $this->ID);
			$rekanan->setField("REKANAN_TIPE_ID",$reqRekananTipe);
			$rekanan->setField("NAMA",$reqNama);
			$rekanan->setField("NPWP",$reqNPWP);
			$rekanan->setField("KTP",$reqKTP);
			$rekanan->setField("STATUS_PERUSAHAAN",$reqStatus);
			$rekanan->setField("ALAMAT",$reqAlamat);
			$rekanan->setField("KOTA",$reqKota);
			$rekanan->setField("TELEPON_KODE",$reqTeleponKode);
			$rekanan->setField("TELEPON",$reqTeleponNo);
			$rekanan->setField("FAX_KODE",$reqFaxKode);
			$rekanan->setField("FAX",$reqFaxNo);
			$rekanan->setField("EMAIL",$reqMail);
			$rekanan->setField("REKANAN_KUALIFIKASI_ID",$reqKualifikasi);

			$rekanan->setField("ALAMAT_PUSAT",$reqAlamatPusat);
			$rekanan->setField("TELEPON_KODE_PUSAT",$reqTeleponKodePusat);
			$rekanan->setField("TELEPON_PUSAT",$reqTeleponNoPusat);
			$rekanan->setField("FAX_KODE_PUSAT",$reqFaxKodePusat);
			$rekanan->setField("FAX_PUSAT",$reqFaxNoPusat);
			$rekanan->setField("EMAIL_PUSAT",$reqMailPusat);
			$rekanan->setField("KODEPOS",$reqKodepos);
			$rekanan->setField("REGION_ID",$reqRegionId);
			$rekanan->setField("BANK_ID",$reqBankId);
			$rekanan->setField("BANK_REKENING",$reqNoRekening);
			$rekanan->setField("BANK_PEMILIK",$reqAtasNama);
			$rekanan->setField("INCOTERM_ID",$reqIncoterm1);
			$rekanan->setField("INCOTERM2",$reqIncoterm2);
			$rekanan->setField("PAYMENT_METHOD_ID",$reqPaymentMethodId);
			$rekanan->setField("MATA_UANG_KODE",$reqMataUang);
			$rekanan->setField("KONTAK_PERSON_HP",$reqKontakPersonHp);
			$rekanan->setField("KONTAK_PERSON",$reqKontakPerson);
			$rekanan->setField("WEBSITE",$reqWebsite);

			if($rekanan->updateprofileperorangan2())
			{
				echo "Data berhasil disimpan.";
			}
		}
	}

	function data_administrasi_umum_syarat()
	{
		$this->load->model("Rekanan");

		/* create objects */
		$rekanan = new Rekanan();

		$reqId			= $this->input->post("reqId");
		$reqKualifikasi	= $this->input->post("reqKualifikasi");
		$reqSubmit	= $this->input->post("reqSubmit");
		$reqSimpan	= $this->input->post("reqSimpan");
		$reqKualifikasiSyarat = $this->input->post("reqKualifikasiSyarat");

		//echo $reqNPWP;
		$rekanan->setField("REKANAN_ID", $this->ID);
		$rekanan->setField("REKANAN_KUALIFIKASI_ID",$reqKualifikasi);
		if($rekanan->update_kualifikasi())
		{
			if($reqKualifikasiSyarat == 3)
				echo "1";
			else
			{
				if($reqKualifikasiSyarat == $reqKualifikasi)
					echo "1";
				else
					echo "0";
			}
		}
		else
			echo "0";
	}

	function data_administrasi_keuangan_pkp_ubah()
	{
		/* INCLUDE FILE */
		$this->load->library("kauth");  $userLogin = new kauth();
		$this->load->model("Rekanan");
		$this->load->library("crfs_protect"); $csrf = new crfs_protect();
		$this->load->library("FileHandler");
		$file = new FileHandler();

		/* create objects */
		$rekanan = new Rekanan();
		// $rekanan_pkp 	= new Rekanan(); // tipe ?

		$reqId = $this->ID;
		$rekanan->selectByParams(array("A.REKANAN_ID"=>$this->ID),-1,-1);
		$rekanan->firstRow();

		// $rekanan_pkp->setField('REKANAN_ID', $this->ID);
		// $rekanan_pkp->setField('PKP', $reqNoSurat);
		// $rekanan_pkp->setField('PKP_TANGGAL', dateToDBCheck($reqTanggal));
		// $rekanan_pkp->setField('NPWP', $reqJabatan);
		// $rekanan_pkp->setField('SKT_PKP_NOMOR', $reqSKTPKP);

		// 8 Agus 2024
		$reqStatusPKP = $this->input->post("reqStatusPKP");
		$reqNonPKPFile    = $_FILES['reqNonPKPFile'];
		$reqNonPKPFileTemp = $this->input->post("reqNonPKPFileTemp");
		$reqNamaFileNonPKP = $this->input->post("reqNamaFileNonPKP");
		$reqPKP = $this->input->post("reqPKP");
		$reqMasaBerlakuPKP	= $this->input->post("reqMasaBerlakuPKP");
		$reqPKPFile		= $_FILES['reqPKPFile'];
		$reqPKPFileTemp = $this->input->post("reqPKPFileTemp");
		$reqNamaFilePKP = $this->input->post("reqNamaFilePKP");

		$reqSKTPKP  = $this->input->post("reqSKTPKP");
		$reqSKTPKPFile    = $_FILES['reqSKTPKPFile'];
		$reqSKTPKPFileTemp = $this->input->post("reqSKTPKPFileTemp");
		$reqNamaFileSKTPKP = $this->input->post("reqNamaFileSKTPKP");

		// echo "<pre>"; print_r($this->input->post()); die;

		$FILE_DIR = "uploads/rekanan/";

		$rekanan->setField("PKP", $reqPKP);
		$rekanan->setField("PKP_TANGGAL",dateToDBCheck($reqMasaBerlakuPKP));
		$rekanan->setField('CREATED_BY', $this->USER_LOGIN_ID);
		$rekanan->setField("REKANAN_ID", $this->ID);

		// Upload NON PKP
		$renameFileNONPKP = 'NON_PKP_'.md5(date("dmYHis").$reqNonPKPFile['name'].$this->ID).".".getExtension($reqNonPKPFile['name']);
		if($file->uploadToDir('reqNonPKPFile', $FILE_DIR, $renameFileNONPKP))
		{
		  $insertLinkFileNONPKP =  $renameFileNONPKP;
		  $insertLinkFileNONPKPNama = $reqNonPKPFile['name'];
		}
		else
		{
		  $insertLinkFileNONPKP =  $reqNonPKPFileTemp;
		  $insertLinkFileNONPKPNama = $reqNamaFileNonPKP;
		}
		/* END Upload NON PKP */
		$rekanan->setField("NON_PKP_FILE", $insertLinkFileNONPKP);
		$rekanan->setField("NAMA_NON_PKP_FILE", $insertLinkFileNONPKPNama);

		// Upload SKT PKP
		$renameFileSKTPKP = 'SKT_PKP_'.md5(date("dmYHis").$reqSKTPKPFile['name'].$this->ID).".".getExtension($reqSKTPKPFile['name']);
		if($file->uploadToDir('reqSKTPKPFile', $FILE_DIR, $renameFileSKTPKP))
		{
		  $insertLinkFileSKTPKP =  $renameFileSKTPKP;
		  $insertLinkFileSKTPKPNama = $reqSKTPKPFile['name'];
		}
		else
		{
		  $insertLinkFileSKTPKP =  $reqSKTPKPFileTemp;
		  $insertLinkFileSKTPKPNama = $reqNamaFileSKTPKP;
		}
		/* END Upload SKT PKP */
		$rekanan->setField("STATUS_PKP", $reqStatusPKP);
		$rekanan->setField("SKT_PKP_NOMOR", $reqSKTPKP);
		$rekanan->setField("SKT_PKP_FILE", $insertLinkFileSKTPKP);
		$rekanan->setField("NAMA_SKT_PKP_FILE", $insertLinkFileSKTPKPNama);

		$renameFilePKP = md5(date("dmYHis").$reqPKPFile['name'].$this->ID).".".getExtension($reqPKPFile['name']);
		if($file->uploadToDir('reqPKPFile', $FILE_DIR, $renameFilePKP))
		{
		  $insertLinkFilePKP =  $renameFilePKP;
		  $insertLinkFilePKPNama = $reqPKPFile['name'];
		}
		else
		{
		  $insertLinkFilePKP =  $reqPKPFileTemp;
		  $insertLinkFilePKPNama = $reqNamaFilePKP;
		}
		/* END UPLOAD FILE */
		$rekanan->setField("PKP_FILE", $insertLinkFilePKP);
		$rekanan->setField("NAMA_FILE_PKP", $insertLinkFilePKPNama);

		if($rekanan->update_pkp())
		{
			echo "Data berhasil diupdate";
		}
		else
		{
			echo "Data Gagal Tersimpan";
		}
	}


	function get_data_rekanan()
	{

		$this->load->library("paketinfo"); $paketInfo = new paketinfo();
		$this->load->model("PaketBidangUsaha");

		/* create objects */
		$bidang_usaha = new PaketBidangUsaha();
		$bidang_usaha_rekanan = new PaketBidangUsaha();

		/* VARIABLES */
		$reqId = $this->input->get("reqId");
		$reqCheckbox = $this->input->get("reqCheckbox");
		$reqPencarian = $this->input->get("reqPencarian");
		$reqCheckboxKualifikasi = $this->input->get("reqCheckboxKualifikasi");

		$bidang_usaha->selectByParams(array("PAKET_ID" => $reqId));
		$paketInfo->getPaket($reqId);

		if($paketInfo->kualifikasi_id == 3) // 3:Kecil / Non-Kecil
		{
			$statement = " AND STATUS_VALIDASI=1";
		} else {
			// kualifikasi bidang usaha 1:Kecil, 2:Non-Kecil
			$statement = " AND REKANAN_KUALIFIKASI_ID = '".$paketInfo->kualifikasi_id."' AND STATUS_VALIDASI=1 ";
		}

		if($reqCheckboxKualifikasi == 1) {
			$statement = " AND STATUS_VALIDASI=1";
		} else {
			$statement = " AND STATUS_VALIDASI=1";
		}


		if($reqPencarian == "")
		{
			$statement = " AND STATUS_VALIDASI=1 AND A.REKANAN_ID not in (SELECT rekanan_id FROM blacklist where current_date between tanggal_mulai and tanggal_selesai )";
		}
		else
			$statement .= " AND UPPER(NAMA) LIKE '%".strtoupper($reqPencarian)."%' AND STATUS_VALIDASI=1 AND A.REKANAN_ID not in (SELECT rekanan_id FROM blacklist where current_date between tanggal_mulai and tanggal_selesai )";

		if($reqCheckbox == 1) {
			$bidang_usaha_rekanan->selectByParamsRekananTanpaBidangUsahaIkun(array(), -1, -1, $reqId, $statement);
		}
		else {
			if ($paketInfo->multi_bidang_usaha == '1') {
				$bidang_usaha_rekanan->selectByParamsRekananIkun2(array(), -1, -1, $reqId, $statement);
			} else {
				$bidang_usaha_rekanan->selectByParamsRekananIkun(array(), -1, -1, $reqId, $statement);
			}
		}

		$met = array();
		$i=0;

		while($bidang_usaha_rekanan->nextRow()){
			$alamat = str_replace("\r",'',$bidang_usaha_rekanan->getField("ALAMAT"));
			$alamat = str_replace("\n",'',$alamat);

			$met[$i]['id'] = $bidang_usaha_rekanan->getField("REKANAN_ID");
			$met[$i]['text'] = $bidang_usaha_rekanan->getField('NAMA');
			$met[$i]['NAMA'] = str_replace("'", "", $bidang_usaha_rekanan->getField("NAMA"));
			$met[$i]['ALAMAT'] = $alamat;
			$met[$i]['EMAIL'] = $bidang_usaha_rekanan->getField('EMAIL');
			$met[$i]['REKANAN_ID'] = $bidang_usaha_rekanan->getField('REKANAN_ID');
			$met[$i]['TOTAL_UNDANG'] = $bidang_usaha_rekanan->getField('TOTAL_UNDANG') ? $bidang_usaha_rekanan->getField('TOTAL_UNDANG').' kali' : ' &nbsp;&nbsp;0&nbsp;';
			$met[$i]['TOTAL_PEMENANG'] = $bidang_usaha_rekanan->getField('TOTAL_PEMENANG') ? $bidang_usaha_rekanan->getField('TOTAL_PEMENANG').' kali' : ' &nbsp;&nbsp;0&nbsp;';

			if ($bidang_usaha_rekanan->getField("SIUP_TGL_AWAL") == '') {
				$met[$i]['SIUP'] = '-';
			} else {
				if ($bidang_usaha_rekanan->getField("SIUP_TGL_AKHIR") == '') {
					$met[$i]['SIUP'] = 'Seumur Hidup <br>'.$bidang_usaha_rekanan->getField("SIUP_TGL_AKHIR");
				} else {
					if (strtotime($bidang_usaha_rekanan->getField("SIUP_TGL_AKHIR")) <= strtotime(date('d-m-Y'))) {
						$met[$i]['SIUP'] = 'Berakhir <br>'.$bidang_usaha_rekanan->getField("SIUP_TGL_AKHIR");
					} else {
						$met[$i]['SIUP'] = 'Masih Berlaku Sampai <br>'.$bidang_usaha_rekanan->getField("SIUP_TGL_AKHIR");
					}
				}
			}

			if ($bidang_usaha_rekanan->getField("IUJK_TGL_AWAL") == '') {
				$met[$i]['IUJK'] = '-';
			} else {
				if ($bidang_usaha_rekanan->getField("IUJK_TGL_AKHIR") == '') {
					$met[$i]['IUJK'] = 'Seumur Hidup <br>'.$bidang_usaha_rekanan->getField("IUJK_TGL_AKHIR");
				} else {
					if (strtotime($bidang_usaha_rekanan->getField("IUJK_TGL_AKHIR")) <= strtotime(date('d-m-Y'))) {
						$met[$i]['IUJK'] = 'Berakhir <br>'.$bidang_usaha_rekanan->getField("IUJK_TGL_AKHIR");
					} else {
						$met[$i]['IUJK'] = 'Masih Berlaku Sampai <br>'.$bidang_usaha_rekanan->getField("IUJK_TGL_AKHIR");
					}
				}
			}

			if ($bidang_usaha_rekanan->getField("SBUJK_TGL_AWAL") == '') {
				$met[$i]['SBUJK'] = '-';
			} else {
				if ($bidang_usaha_rekanan->getField("SBUJK_TGL_AKHIR") == '') {
					$met[$i]['SBUJK'] = 'Seumur Hidup <br>'.$bidang_usaha_rekanan->getField("SBUJK_TGL_AKHIR");
				} else {
					if (strtotime($bidang_usaha_rekanan->getField("SBUJK_TGL_AKHIR")) <= strtotime(date('d-m-Y'))) {
						$met[$i]['SBUJK'] = 'Berakhir <br>'.$bidang_usaha_rekanan->getField("SBUJK_TGL_AKHIR");
					} else {
						$met[$i]['SBUJK'] = 'Masih Berlaku Sampai <br>'.$bidang_usaha_rekanan->getField("SBUJK_TGL_AKHIR");
					}
				}
			}

			$i++;
		}
		echo json_encode($met);
	}

	function upload_cv()
	{
		$this->load->library("kauth");  $userLogin = new kauth();
		$this->load->model("Rekanan");
		$this->load->library("FileHandler");

		$rekanan = new Rekanan();
		$file = new FileHandler();
		// echo "<pre>"; print_r($this->input->post()); die();
		$reqMode = $this->input->post("reqMode");
		$reqRekananId = $this->input->post("reqRekananId");
		$reqLinkFile= $_FILES['reqLinkFile'];

		$FILE_DIR = "uploads/rekanan/";

		$rekanan->setField("REKANAN_ID", $reqRekananId);
		/* UPLOAD FILE */
		$renameFile = md5(date("dmYHis").$reqLinkFile['name'].$this->ID).".".getExtension($reqLinkFile['name']);
		if($file->uploadToDir('reqLinkFile', $FILE_DIR, $renameFile))
		{
			$insertLinkFile =  $renameFile;
			$insertLinkFileNamaCV = $reqLinkFile['name'];
		}
		else
		{
		}
		/* END UPLOAD FILE */
		$rekanan->setField("CV_FILE", $insertLinkFile);
		$rekanan->setField("NAMA_FILE_CV", $insertLinkFileNamaCV);
		$rekanan->update_cv();

		echo "Upload Daftar Riwayat Hidup berhasil disimpan";
	}

	function registrasi()
	{
		// echo "<pre>"; print_r($this->input->post()); exit(); die();

		$this->load->model("Rekanan");
		$this->load->model("RekananIjinUsaha");
		$this->load->model("Users");
		$this->load->model("IjinUsaha");
		$this->load->model("RekananAkta");
		$this->load->model("RekananPengurus");
		$this->load->library("KMail");
		$this->load->library("crfs_protect"); $csrf = new crfs_protect('_crfs_rr');
		$this->load->library("FileHandler");
		$file = new FileHandler();

		/* create objects */
		$rekanan = new Rekanan();
		$rekanan_ijin = new RekananIjinUsaha();
		$rekanan_pengurus = new RekananPengurus();
		$ijin_usaha = new IjinUsaha();
		$rekanan_akta = new RekananAkta();
		$user_login = new Users();

		$reqJenisPerusahaan= $this->input->post("reqJenisPerusahaan");
		$reqNamaPerusahaan= $this->input->post("reqNamaPerusahaan");
		$reqStatus= $this->input->post("reqStatus");
		$reqNPWP= $this->input->post("reqNPWP");
		$reqEmail= $this->input->post("reqEmail");
		$reqSetuju= $this->input->post("reqSetuju");
		$reqUserLogin= $this->input->post("reqUserLogin");
		$reqPassword= $this->input->post("reqPassword");
		$security_code= $this->input->post("security_code");
		$reqLinkFile		= $_FILES['reqLinkFile'];

		if($reqSetuju == "")
		{
			echo "0-0-Setujui terlebih dahulu pernyataan registrasi.";
			return;
		}

		if (!$csrf->isTokenValid($_POST['_crfs_rr']))
			exit();

		$kode = KODE_REKANAN.date('Y').generateZero($rekanan->getNextKode(), 5, 0);
		$rekanan = new Rekanan();
		$rekanan->setField('REKANAN_TIPE_ID', $reqJenisPerusahaan);
		$rekanan->setField('NAMA', $reqNamaPerusahaan);
		$rekanan->setField('EMAIL', $reqEmail);
		$rekanan->setField('NPWP', $reqNPWP);
		$rekanan->setField("IJIN_USAHA_ID", "NULL");
		$rekanan->setField("KODE", $kode);
		$rekanan->setField("STATUS_VALIDASI",0);
		$rekanan->setField("STATUS_CP","null");
		$rekanan->setField("BANK_ID","NULL");
		$rekanan->setField('REKANAN_KUALIFIKASI_ID', 3);


		if($rekanan->insertrevisi())
		{
			$id = $rekanan->id;

			$user_login->setField("USER_LOGIN",$reqUserLogin);
			$user_login->setField("USER_NAMA",$reqNamaPerusahaan);
			$user_login->setField("USER_PASSWORD", password_hash($reqPassword,PASSWORD_DEFAULT));
			$user_login->setField("USER_TYPE_ID", 6);
			$user_login->setField("USER_JABATAN", '');
			$user_login->setField("USER_TELEPON", $reqKodeTelepon.$reqNomorTelepon);
			$user_login->setField("USER_ALAMAT", $reqAlamat);
			$user_login->setField("REKANAN_ID", $id);
			$user_login->setField("USER_STATUS",'0');
			$user_login->setField("USER_AUTH",sha1($reqEmail));
			$user_login->setField("UNIT_KERJA_ID",'null');
			// $user_login->setField("CHILD_PL",null);
			if($user_login->insertRegis())
			{

				// $Ccs = array($_SESSION["ses_CabangEmail"]);
				$cbg = str_replace(dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR,'',dirname(__FILE__));
				$mail = new KMail($cbg);
				$mail->Subject  =  'Registrasi - '.SYSTEM_NAME.' '.SYSTEM_NAME_PT;
				$mail->AddAddress($reqEmail , $reqNamaPerusahaan);
				/*foreach($Ccs as $key => $val){
					$mail->AddBCC($val , $key);
				}*/
				$body = file_get_contents(SYSTEM_URL_EMAIL."/main/loadUrl/email/registrasi_rekanan/".$id);
				$mail->MsgHTML($body);

				if(!$mail->Send())
				{
					// echo "Mailer Error: " . $mail->ErrorInfo;
				}
				else
				{
					// echo 'Message has been sent.';
				}
				//echo md5($kode)."-Registrasi berhasil.";
				// echo $reqUserLogin."-".$reqPassword."-Registrasi berhasil.";
				echo $reqUserLogin."-aa-Registrasi berhasil.";
			}
			unset($_SESSION['security_code']);
	   }

	}

	function testmail()
	{
		// echo (extension_loaded('openssl')?'SSL loaded':'SSL not loaded')."\n"; die();
		$this->load->library("KMail");
		// 		if( ini_get('allow_url_fopen') ) {
		//     die('allow_url_fopen is enabled. file_get_contents should work well');
		// } else {
		//     die('allow_url_fopen is disabled. file_get_contents would not work');
		// }

		$cbg = str_replace(dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR,'',dirname(__FILE__));
		$mail = new KMail($cbg);
		$mail->Subject  =  'Registrasi - '.SYSTEM_NAME.' '.SYSTEM_NAME_PT;
		$mail->AddAddress('aminawm@gmail.com' , 'PT Future');
		$body = file_get_contents(SYSTEM_URL_EMAIL."/main/loadUrl/email/registrasi_rekanan/1587");
		// echo $body; die();
		$mail->MsgHTML($body);

		if(!$mail->Send())
		{
			echo "Mailer Error: " . $mail->ErrorInfo;
		}
		else
		{
			echo 'Message has been sent.';
		}
	}

	function validasi_rekanan_teruskan() // done
	{
		$this->load->model("Rekanan");
		$this->load->model("Users");
		$this->load->library("KMail");

		$rekanan = new Rekanan();
		$user_login = new Users();

		$reqNomorValidasi = $this->input->post("reqNomorValidasi");
		$reqEmail = $this->input->post("reqEmail");
		$reqRekananNama = $this->input->post("reqRekananNama");
		$reqPerusahaanEmail = $this->input->post("reqPerusahaanEmail");
		$submitSimpan = $this->input->post("submitSimpan");
		$reqNote1 = $this->input->post("reqNote1");

		$user_login->setField("KODE", $reqNomorValidasi);
		$user_login->setField("NOTE1", $reqNote1);
		$user_login->setField("DATE_VALIDASI_1", date('Y-m-d H:i:s'));
		$user_login->setField("USER_VALIDASI", $this->USER_LOGIN_ID.'||'.$this->USER_NAMA.'||'.$this->USER_LOGIN);
		if($user_login->validasiTeruskan())
		{
			echo ' Penyedia sudah di teruskan, menunggu rekomendasi & approval!';
		}

	}

	function validasi_rekanan_rekomendasi()
	{
		$this->load->model("Rekanan");
		$this->load->model("Users");

		$rekanan = new Rekanan();
		$user_login = new Users();

		$reqId = $this->input->get("reqId");
		$reqNote2 = $this->input->get("reqNote2");
		$reqStatus = $this->input->get("reqStatus");

		$user_login->setField("REKANAN_ID", $reqId);
		$user_login->setField("NOTE2", $reqNote2);
		$user_login->setField("STATUS_VALIDASI", $reqStatus);
		$user_login->setField("DATE_VALIDASI_2", date('Y-m-d H:i:s'));
		$user_login->setField("USER_VALIDASI", $this->USER_LOGIN_ID.'||'.$this->USER_NAMA.'||'.$this->USER_LOGIN);
		if($user_login->validasiRekomendasi())
		{
			if ($reqStatus == '4') {
				$arrJson["PESAN"] = "Penyedia sudah di teruskan, menunggu approval!";
			} else {
				$arrJson["PESAN"] = " Penyedia dikembalikan untuk dicek kembali!";
			}
			echo json_encode($arrJson);
		}

	}

	function validasi_rekanan_validator()
	{
		$this->load->model("Rekanan");
		$this->load->model("Users");

		$rekanan = new Rekanan();
		$user_login = new Users();

		$reqId = $this->input->get("reqId");
		$reqNote3 = $this->input->get("reqNote3");
		$reqStatus = $this->input->get("reqStatus");

		$user_login->setField("REKANAN_ID", $reqId);
		$user_login->setField("NOTE3", $reqNote3);
		$user_login->setField("STATUS_VALIDASI", $reqStatus);
		$user_login->setField("DATE_VALIDASI_3", date('Y-m-d H:i:s'));
		$user_login->setField("USER_VALIDASI", $this->USER_LOGIN_ID.'||'.$this->USER_NAMA.'||'.$this->USER_LOGIN);
		if($user_login->validasiValidator())
		{
			if ($reqStatus == '1') {
				$arrJson["PESAN"] = "Penyedia berhasil di verifikasi";
			} else {
				$arrJson["PESAN"] = " Penyedia dikembalikan untuk dicek kembali!";
			}
			echo json_encode($arrJson);
		}

	}

	function validasi_rekanan()
	{
		$this->load->model("Rekanan");
		$this->load->model("RekananIjinUsaha");
		$this->load->model("RekananAkta");
		$this->load->model("RekananPengurus");
		$this->load->model("Users");
		$this->load->library("KMail");

		$rekanan = new Rekanan();
		$rekanan_ijin_usaha = new RekananIjinUsaha();
		$rekanan_akta = new RekananAkta();
		$rekanan_pengurus_komisaris = new RekananPengurus();
		$rekanan_pengurus_direksi = new RekananPengurus();
		$user_login = new Users();

		// $reqNomorValidasi = $this->input->post("reqNomorValidasi");
		// $reqEmail = $this->input->post("reqEmail");
		// $reqRekananNama = $this->input->post("reqRekananNama");
		// $reqPerusahaanEmail = $this->input->post("reqPerusahaanEmail");
		// $submitSimpan = $this->input->post("submitSimpan");

		$reqId = $this->input->get("reqId");
		$reqNote3 = $this->input->get("reqNote3");
		$reqStatus = $this->input->get("reqStatus");

		$rekanan->selectByParams(array("A.REKANAN_ID"=>$reqId),-1,-1);
		$rekanan->firstRow();

		$reqNomorValidasi = $rekanan->getField("KODE");
		$reqEmail = $rekanan->getField("EMAIL");
		$reqRekananNama = $rekanan->getField("NAMA");

		// if($submitSimpan == "Simpan")
		// {
			$_SESSION['KODE_VALIDASI_SET_TO'] = $reqNomorValidasi;
			/*echo '<script language="javascript">';
			echo 'top.location.href = "main/?pg=validasi_konfirmasi";';
			echo '</script>';*/
			// $reqUserLogin = $user_login->getUserLoginByKode($reqNomorValidasi);

			$user_login->setField("KODE", $reqNomorValidasi);
			$user_login->setField("NOTE3", $reqNote3);
			$user_login->setField("DATE_VALIDASI_3", date('Y-m-d H:i:s'));
			$user_login->setField("USER_VALIDASI", $this->USER_LOGIN_ID.'||'.$this->USER_NAMA.'||'.$this->USER_LOGIN);
			if($user_login->validasi())
			{

				$Ccs = array($_SESSION["ses_CabangEmail"]);
				$cbg = str_replace(dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR,'',dirname(__FILE__));
				$mail = new KMail($cbg);
				$mail->Subject  =  'Informasi Verifikasi '.SYSTEM_NAME.' '.SYSTEM_NAME_PT;
				$mail->AddAddress($reqEmail , $reqRekananNama);
				/*foreach($Ccs as $key => $val){
						$mail->AddBCC($val , $key);
				}*/
				$body = file_get_contents(SYSTEM_URL_EMAIL."/main/loadUrl/email/validasi_konfirmasi_en/".$reqNomorValidasi);
				$mail->MsgHTML($body);
				//$mail->MsgHTML($message);

				if(!$mail->Send())
				{
					//echo "Mailer Error: " . $mail->ErrorInfo;
				}
				else
				{
					//echo 'Message has been sent.';
				}
				//mail($to,$subject,$message,$headers);

				$arrJson["PESAN"] = "Data berhasil diapprove";
				echo json_encode($arrJson);
			}
		// }

	}

	function validasi_rekanan_get()
	{
		$this->load->model("Rekanan");
		$this->load->model("Users");
		$this->load->library("KMail");

		$rekanan = new Rekanan();
		$user_login = new Users();

		$reqId = $this->input->get("reqId");
		// echo $reqNomorValidasi; die();
		$rekanan = new Rekanan();
		$rekanan->selectByParams(array("REKANAN_ID"=>$reqId),-1,-1);
		$rekanan->firstRow();

		$reqNomorValidasi = $rekanan->getField("KODE");

		$_SESSION['KODE_VALIDASI_SET_TO'] = $reqNomorValidasi;
		/*echo '<script language="javascript">';
		echo 'top.location.href = "main/?pg=validasi_konfirmasi";';
		echo '</script>';*/

		$reqUserLogin = $user_login->getUserLoginByKode($reqNomorValidasi);

		$user_login->setField("KODE", $reqNomorValidasi);
		$user_login->setField("USER_VALIDASI", $this->USER_LOGIN_ID.'||'.$this->USER_NAMA.'||'.$this->USER_LOGIN);
		if($user_login->validasi())
		{

			$Ccs = array($_SESSION["ses_CabangEmail"]);
			$cbg = str_replace(dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR,'',dirname(__FILE__));
			$mail = new KMail($cbg);
			$mail->Subject  =  'Informasi veririkasi '.SYSTEM_NAME.' '.SYSTEM_NAME_PT;
			$mail->AddAddress($rekanan->getField("EMAIL") , $rekanan->getField("NAMA"));
			/*foreach($Ccs as $key => $val){
					$mail->AddBCC($val , $key);
			}*/
			$body = file_get_contents(SYSTEM_URL_EMAIL."/main/loadUrl/email/validasi_konfirmasi_en/".$reqNomorValidasi);
			$mail->MsgHTML($body);
			//$mail->MsgHTML($message);

			if(!$mail->Send())
			{
				//echo "Mailer Error: " . $mail->ErrorInfo;
			}
			else
			{
				//echo 'Message has been sent.';
			}
			//mail($to,$subject,$message,$headers);

			$arrJson["PESAN"] = "Penyedia berhasil di approve";

			echo json_encode($arrJson);
		}

	}

	function update_pkp_delete()
	{
		$this->load->model("Rekanan");
		$this->load->model("RekananIjinUsaha");
		$this->load->model("RekananAkta");
		$this->load->model("RekananPengurus");
		$this->load->model("Users");
		$this->load->library("KMail");
		$reqId = $this->input->get("reqId");
		$rekanan_pkp = new Rekanan();

		$rekanan_pkp->setField('REKANAN_ID', $reqId);

		if($rekanan_pkp->update_pkp_delete())
		{
			echo "Data berhasil didelete";
		}
		else
		{
			echo "Data gagal didelete";
		}

	}

	function daftar_rekanan_belum_alasan()
	{
		$this->load->model("Rekanan");

		$reqSubject = $this->input->post("reqSubject");
		$reqIsi = $this->input->post("reqIsi");
		$reqId = $this->input->post("reqId");
		$reqSubmit = $this->input->post("reqSubmit");

		if($reqSubmit == 'Submit')
		{

			$rekanan = new Rekanan();
			$rekanan->setField("ALASAN_HAPUS", $reqIsi);
			$rekanan->setField("REKANAN_ID", $reqId);
			if($rekanan->updateAlasan())
			{
				echo "Data berhasil di hapus";
			}

		}

	}

	function get_data_daftar_rekanan()
	{
		$this->load->model("Rekanan");
		$rekanan = new Rekanan();

		$reqSearch = $this->input->get("reqSearch");

		$rekanan->selectByParamsCari(array(), -1, -1, "AND (UPPER(A.NAMA) LIKE '%".strtoupper($reqSearch)."%')");

		$i=0;

		while($rekanan->nextRow())
		{
			$met[$i]['id'] = $rekanan->getField('REKANAN_ID');
			$met[$i]['text'] = $rekanan->getField('NAMA');
			$met[$i]['REKANAN_ID'] = $rekanan->getField('REKANAN_ID');
			$met[$i]['NAMA'] = $rekanan->getField('NAMA');
			$met[$i]['KODE'] = $rekanan->getField('KODE');
			$met[$i]['NPWP'] = $rekanan->getField('NPWP');
			$met[$i]['ALAMAT'] = $rekanan->getField('ALAMAT');
			$met[$i]['KOTA'] = $rekanan->getField('KOTA');
			$i++;
		}
		echo json_encode($met);
	}

	function get_data_daftar_rekanan_validasi()
	{
		$this->load->model("Rekanan");
		$rekanan = new Rekanan();

		$reqSearch = $this->input->get("reqSearch");

		$rekanan->selectByParamsCari(array("STATUS_VALIDASI" => '1'), -1, -1, "AND (UPPER(A.NAMA) LIKE '%".strtoupper($reqSearch)."%')");

		$i=0;

		while($rekanan->nextRow())
		{
			$met[$i]['id'] = $rekanan->getField('REKANAN_ID');
			$met[$i]['text'] = $rekanan->getField('NAMA');
			$met[$i]['REKANAN_ID'] = $rekanan->getField('REKANAN_ID');
			$met[$i]['NAMA'] = $rekanan->getField('NAMA');
			$met[$i]['KODE'] = $rekanan->getField('KODE');
			$met[$i]['NPWP'] = $rekanan->getField('NPWP');
			$met[$i]['ALAMAT'] = $rekanan->getField('ALAMAT');
			$met[$i]['KOTA'] = $rekanan->getField('KOTA');
			$i++;
		}
		echo json_encode($met);
	}


	function rekanan_konfirmasi()
	{
		$this->load->model("Rekanan");
		$this->load->model("UserLogin");
		$this->load->library("KMail");
		//$this->load->library("crfs_protect"); $csrf = new crfs_protect('_crfs_rr');

		/* create objects */
		$rekanan = new Rekanan();

		$reqUncentang= $this->input->post("reqUncentang");

		if($reqUncentang > "0")
		{
			echo "0-Kelengkapan data belum lengkap \n Silahkan lengkapi dahulu.";
			return;
		}

		//echo "sdsd".$this->USER_NAMA;exit;

		$user_login = new UserLogin();
		$user_login->setField("USER_STATUS",'2');
		$user_login->setField("REKANAN_ID", $this->ID);
		$user_login->setField("USER_LOGIN_ID", $this->USER_LOGIN_ID);
		if($user_login->updateStatus())
		{
			$rekanan->setField("FIELD",'STATUS_VALIDASI');
			$rekanan->setField("FIELD_VALUE",'0');
			$rekanan->setField("REKANAN_ID", $this->ID);
			$rekanan->updateByField();
			// $Ccs = array($_SESSION["ses_CabangEmail"]);
			// $cbg = str_replace(dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR,'',dirname(__FILE__));
			// $mail = new KMail($cbg);
			// $mail->Subject  =  'Registrasi - '.SYSTEM_NAME.' '.SYSTEM_NAME_PT;
			// $mail->AddAddress($this->REKANAN_EMAIL, $this->USER_NAMA);
			// //foreach($Ccs as $key => $val){
			// //	$mail->AddBCC($val , $key);
			// //}
			// $body = file_get_contents(base_url()."main/loadUrl/email/registrasi_rekanan/".$this->ID);
			// $mail->MsgHTML($body);

			// if(!$mail->Send())
			// {
			// 	//echo "Mailer Error: " . $mail->ErrorInfo;
			// }
			// else
			// {
			// 	//echo 'Message has been sent.';
			// }
			// echo "1-Registrasi berhasil.";
			echo "1-Dokumen Pendaftaran sudah di kirim.";
		}

	}

	function revisi_rekanan()
	{
		$this->load->model("Rekanan");
		$this->load->model("UserLogin");
		$this->load->library("KMail");
		//$this->load->library("crfs_protect"); $csrf = new crfs_protect('_crfs_rr');

		/* create objects */
		$rekanan = new Rekanan();

		$reqId= $this->input->get("reqId");

		//echo "sdsd".$this->USER_NAMA;exit;

		$user_login = new UserLogin();
		$user_login->setField("USER_STATUS",'0');
		$user_login->setField("REKANAN_ID", $reqId);
		$user_login->setField("USER_LOGIN_ID", $this->USER_LOGIN_ID);
		if($user_login->updateStatusRevisiRekanan())
			echo "1";

	}


	function lupa_password()
	{
		$this->load->model("Rekanan");
		$this->load->library("KMail");

		/* create objects */
		$reqEmail = trim($this->input->post("reqEmail"));

		$rekanan = new Rekanan();

		$rekanan->selectByParamsSimple(array("A.EMAIL" => $reqEmail));
		$rekanan->firstRow();

		if($rekanan->getField("REKANAN_ID") == "")
		{
			echo "Email tidak ditemukan.";
			return;
		}

		$Ccs = array($_SESSION["ses_CabangEmail"]);
		$cbg = str_replace(dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR,'',dirname(__FILE__));
		$mail = new KMail($cbg);
		$mail->Subject  =  'Reset Password '.SYSTEM_NAME.' '.SYSTEM_NAME_PT;
		$mail->AddAddress($reqEmail, $rekanan->getField("NAMA"));
		//foreach($Ccs as $key => $val){
		//	$mail->AddBCC($val , $key);
		//}
		$body = file_get_contents(SYSTEM_URL_EMAIL."/main/loadUrl/email/lupa_password/".$rekanan->getField("REKANAN_ID"));
		$mail->MsgHTML($body);

		if(!$mail->Send())
		{
			//echo "Mailer Error: " . $mail->ErrorInfo;
		}
		else
		{
			//echo 'Message has been sent.';
		}
		echo "Link untuk melakukan reset password telah dikirim ke email anda.";

	}

	function upload_pakta_integritas()
	{
		$this->load->model("DokumenRekanan");
		$this->load->library("FileHandler");

		$dokumen_rekanan = new DokumenRekanan();
		$file = new FileHandler();

		$reqMode = $this->input->post("reqMode");
		$reqNamaDokumen= $this->input->post('reqNamaDokumen');
		$reqJenisDokumen= $this->input->post('reqJenisDokumen');
		$reqLinkFile= $_FILES['reqLinkFile'];
		$FILE_DIR = "uploads/pakta_integritas/";
		$renameFile = 'PAKTA_INTEGRITAS_'.md5(date("dmYHis").$reqLinkFile['name'].$this->USER_LOGIN_ID).".".getExtension($reqLinkFile['name']);

		if($file->uploadToDir('reqLinkFile', $FILE_DIR, $renameFile))
		{

			$dokumen_rekanan->setField("REKANAN_ID", $this->ID);
			$dokumen_rekanan->setField("PATH_FILE", $renameFile);
			$dokumen_rekanan->setField("TIPE", $file->uploadedExtension);
			$dokumen_rekanan->setField("UKURAN", $file->uploadedSize);
			$dokumen_rekanan->setField("NAMA_FILE", $reqLinkFile['name']);
			$dokumen_rekanan->setField('CREATED_BY', $this->USER_LOGIN_ID);
			$dokumen_rekanan->deletePI();
			$dokumen_rekanan->insertPI();

			echo "Pakta Integritas berhasil diupload.";

		} else {
			echo "Pakta Integritas gagal diupload";
		}
	}

	function updateChecklist()
	{

		$rekananid = $this->input->get("rekananid");
		$jenis = $this->input->get("jenis");
		$status = $this->input->get("status");

		$this->load->model("Rekanan");
		$cekData = new Rekanan();
		$insertChecklist = new Rekanan();

		$cekData->selectByParamsRekananChecklist(array("REKANANID"=>$rekananid),-1,-1);

		$insertChecklist->setField('REKANANID', $rekananid);
		$insertChecklist->setField('FIELD_VALUE', $status);
		$insertChecklist->setField('FIELD', strtoupper($jenis));
		$insertChecklist->setField('CREATED_BY', $this->USER_LOGIN_ID);

		if ($cekData->countRow() > 0) {
		} else {
			$insertChecklist->insertCheck();
		}

		$update = $insertChecklist->updateCheck();
		if ($update) {
			$arrJson['PESAN'] = 'Data berhasil disimpan';
			$arrJson['RESPONSE'] = 'Sukses';
		} else {
			$arrJson['PESAN'] = 'Data gagal disimpan';
			$arrJson['RESPONSE'] = 'Gagal';
		}

		echo json_encode($arrJson);
	}

	function updateChecklist2()
	{

		$rekananid = $this->input->get("rekananid");
		$jenis = $this->input->get("jenis");
		$jenis2 = $this->input->get("jenis").'_note';
		$status = $this->input->get("status");
		$catatan = $this->input->get("catatan");

		$this->load->model("Rekanan");
		$cekData = new Rekanan();
		$insertChecklist = new Rekanan();

		$cekData->selectByParamsRekananChecklist(array("REKANANID"=>$rekananid),-1,-1);

		$insertChecklist->setField('REKANANID', $rekananid);
		$insertChecklist->setField('FIELD', $jenis);
		$insertChecklist->setField('FIELD2', $jenis2);
		$insertChecklist->setField('FIELD_VALUE', $status);
		$insertChecklist->setField('FIELD_VALUE2', $catatan);
		$insertChecklist->setField('CREATED_BY', $this->USER_LOGIN_ID);

		if ($cekData->countRow() > 0) {
		} else {
			$insertChecklist->insertCheck();
		}

		$update = $insertChecklist->updateCheck2();
		if ($update) {
			$arrJson['PESAN'] = 'Data berhasil disimpan';
			$arrJson['RESPONSE'] = 'Sukses';
		} else {
			$arrJson['PESAN'] = 'Data gagal disimpan';
			$arrJson['RESPONSE'] = 'Gagal';
		}

		echo json_encode($arrJson);
	}

	function updateChecklistKBLI()
	{

		$rekananid = $this->input->get("rekananid");
		$id = $this->input->get("id");
		$status = $this->input->get("status");

		$this->load->model("RekananBidangUsaha");
		$updateValidasi = new RekananBidangUsaha();

		$updateValidasi->setField('REKANAN_ID', $rekananid);
		$updateValidasi->setField('REKANAN_BIDANG_USAHA_ID', $id);
		$updateValidasi->setField('VALIDASI', $status);
		$updateValidasi->setField('CREATED_BY', $this->USER_LOGIN_ID);

		$update = $updateValidasi->update_validasi();
		if ($update) {
			$arrJson['PESAN'] = 'Data berhasil disimpan';
			$arrJson['RESPONSE'] = 'Sukses';
		} else {
			$arrJson['PESAN'] = 'Data gagal disimpan';
			$arrJson['RESPONSE'] = 'Gagal';
		}

		echo json_encode($arrJson);
	}


}
?>
