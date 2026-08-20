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

class users_base_json extends CI_Controller {

	function __construct() {
		parent::__construct(); $this->db->query("SET TIME ZONE 'Asia/Jakarta'");

		if (!$this->kauth->getInstance()->hasIdentity()) { }

		$this->ID = $this->kauth->getInstance()->getIdentity()->REKANAN_ID;
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
		$this->REKANAN = $this->kauth->getInstance()->getIdentity()->REKANAN;
		$this->REKANAN_KODE = $this->kauth->getInstance()->getIdentity()->REKANAN_KODE;
		$this->REKANAN_PKP = $this->kauth->getInstance()->getIdentity()->REKANAN_PKP;
		$this->REKANAN_NPWP = $this->kauth->getInstance()->getIdentity()->REKANAN_NPWP;
		$this->REKANAN_STATUS_PERUSAHAAN = $this->kauth->getInstance()->getIdentity()->STATUS_PERUSAHAAN;
		$this->REKANAN_STATUS_VALIDASI = $this->kauth->getInstance()->getIdentity()->STATUS_VALIDASI;
	}

	function master_daftar_rekanan_json()
	{
		$this->load->model("UsersBase");
		$user_login = new UsersBase();

		$aColumns 			= array('USER_LOGIN_ID',  'USER_NAMA','USER_LOGIN','USER_STATUS','USER_AKTIF');
		$aColumnsAlias		= array('USER_LOGIN_ID',  'USER_NAMA','USER_LOGIN','USER_STATUS','USER_AKTIF');

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

			if ( trim($sOrder) == "ORDER BY user_login_ELIMINASI desc" )
			{
				$sOrder = " ORDER BY A.user_login asc";

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

		$statement = "AND (UPPER(A.USER_NAMA) LIKE '%".strtoupper($_GET['sSearch'])."%' OR UPPER(A.USER_LOGIN) LIKE '".strtoupper($_GET['sSearch'])."%')";
		$allRecord = $user_login->getCountByParams(array("A.USER_TYPE_ID" => 6), $statement);
		if($_GET['sSearch'] == "")
			$allRecordFilter = $allRecord;
		else
			$allRecordFilter =  $user_login->getCountByParams(array("A.USER_TYPE_ID" => 6), $statement);

		$user_login->selectByParams(array("A.USER_TYPE_ID" => 6), $dsplyRange, $dsplyStart, $statement, $sOrder);

		$output = array(
			"sEcho" => intval($_GET['sEcho']),
			"iTotalRecords" => $allRecord,
			"iTotalDisplayRecords" => $allRecordFilter,
			"aaData" => array()
		);

		while($user_login->nextRow())
		{
			$row = array();
			for ( $i=0 ; $i<count($aColumns) ; $i++ )
			{
				if($aColumns[$i]=='NO')
					$row[] = $number;
				elseif($aColumns[$i]=='USER_STATUS' || $aColumns[$i]=='USER_AKTIF')
				{
					if($user_login->getField(trim($aColumns[$i])) == '1')
						$row[] = '<img src="images/centang.png">';
					else
						$row[] = '<img src="images/uncentang.png">';
				}
				else
					$row[] = $user_login->getField(trim($aColumns[$i]));
			}

			$output['aaData'][] = $row;
		}
		echo json_encode( $output );
	}

	function master_daftar_rekanan_non_json()
	{

		$this->load->model("UsersBase");
		$user_login = new UsersBase();
		$user_login2 = new UsersBase();

		$aColumns 			= array('USER_LOGIN_ID','USER_TYPE','USER_NAMA','USER_LOGIN','USER_JABATAN','NIP','DEPARTMENT','USER_AKTIF','CATATAN_TOLAK');
		$aColumnsAlias		= array('USER_LOGIN_ID','USER_TYPE','USER_NAMA','USER_LOGIN','USER_JABATAN','NIP','DEPARTMENT','USER_AKTIF','CATATAN_TOLAK');

		/*
		 * Ordering
		 */
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
			if ( trim($sOrder) == "ORDER BY USER_LOGIN_ID desc" )
			{
				$sOrder = " ORDER BY A.USER_LOGIN_ID DESC";
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

		$statement .= " AND NOT A.USER_TYPE_ID = 6 ";
		$statement .= "AND (UPPER(A.USER_NAMA) LIKE '%".strtoupper($_GET['sSearch'])."%' OR UPPER(A.USER_LOGIN) LIKE '".strtoupper($_GET['sSearch'])."%' OR UPPER(B.NAMA) LIKE '".strtoupper($_GET['sSearch'])."%' OR UPPER(USER_JABATAN) LIKE '".strtoupper($_GET['sSearch'])."%')";
		$allRecord = $user_login->getCountByParams(array(), $statement);
		if($_GET['sSearch'] == "")
			$allRecordFilter = $allRecord;
		else
			$allRecordFilter =  $user_login->getCountByParams(array(), $statement);

		$user_login->selectByParams(array(), $dsplyRange, $dsplyStart, $statement, $sOrder);

		$output = array(
			"sEcho" => intval($_GET['sEcho']),
			"iTotalRecords" => $allRecord,
			"iTotalDisplayRecords" => $allRecordFilter,
			"aaData" => array()
		);

		while($user_login->nextRow())
		{
			$row = array();
			for ( $i=0 ; $i<count($aColumns) ; $i++ )
			{
				if($aColumns[$i]=='NO')
						$row[] = $number;
					elseif($aColumns[$i]=='USER_AKTIF')
					{
						if($user_login->getField(trim($aColumns[$i])) == '1') {
							$row[] = '<img src="images/centang.png">';
						} else if($user_login->getField(trim($aColumns[$i])) == '3') {
							$row[] = '<img src="images/uncentang.png"> <br><p class="alert alert-danger" style="font-size:10px; line-height: 0.9; padding:5px !important">'.$user_login->getField('CATATAN_TOLAK').'</p>';
						}
						else {
							$row[] = '<img src="images/uncentang.png">';
						}
					}

					elseif($aColumns[$i]=='USER_TYPE')
					{
						// $user_login2->selectByParams(array("USER_LOGIN_ID"=>$user_login->getField('USER_LOGIN_ID')),-1,-1);
						// $user_login2->firstRow();
						// if($user_login->getField('USER_TYPE') == 'PANITIA' && $user_login->getField('USER_JABATAN_PANITIA') != '1'){ // selain ketua
						// 	if ($user_login2->getField('TENDER') == '0') {
						// 		$row[] = $user_login->getField(trim($aColumns[$i])).' <br><small><b>Non-Tender</b></small>';
						// 	} else if ($user_login2->getField('TENDER') == '1') {
						// 		$row[] = $user_login->getField(trim($aColumns[$i])).' <br><small><b>Tender</b></small>';
						// 	} else if ($user_login2->getField('TENDER') == '2') {
						// 		$row[] = $user_login->getField(trim($aColumns[$i])).' <br><small><b>Tender & Non-Tender</b></small>';
						// 	}
						// } else { // ketua
						// 	if ($user_login2->getField('PENUNJUK_PIC') == '1') {
						// 		$row[] = $user_login->getField(trim($aColumns[$i])).' <br><small><b>Penunjuk PIC</b></small>';
						// 	} else {
						// 		$row[] = $user_login->getField(trim($aColumns[$i]));
						// 	}
						// }
						$group = '';
						switch ($user_login->getField('USER_TYPE')) {
							case 'PENGGUNA':
								if ($user_login->getField('LEVEL_PENGGUNA') == '1') {
									$group .= '<br><small><b><span class="badge badge-warning">PIC</span></b></small>';
								} else {
									if ($user_login->getField('KASI_PENGGUNA')) {
										$user_login2->selectByParams(array("USER_LOGIN_ID"=>$user_login->getField('KASI_PENGGUNA')),-1,-1);
										$user_login2->firstRow();
										$group .= '<br><small><b><span class="badge badge-primary">Staff</span></b></small>
													<small><b><span class="badge badge-primary">PIC: '.$user_login2->getField('USER_NAMA').'</span></b></small>';
									} else {
										$group .= '<br><small><b><span class="badge badge-primary">Staff</span></b></small>
													<small><b><span class="badge badge-danger">PIC belum ditetapkan</span></b></small>';
									}
								}
								break;
							case 'PEJABAT PENGADAAN':
								if ($user_login->getField('LEVEL_PEMBELI') == '1') {
									$group .= '<br><small><b><span class="badge badge-warning">Kasi</span></b></small>';
								} else {
									$group .= '<br><small><b><span class="badge badge-primary">Staff</span></b></small>';
								}
								break;
							case 'PERENCANAAN':
								if ($user_login->getField('LEVEL_PERENCANA') == '1') {
									$group .= '<br><small><b><span class="badge badge-primary">Staff</span></b></small>';
								} elseif ($user_login->getField('LEVEL_PERENCANA') == '2') {
									$group .= '<br><small><b><span class="badge badge-warning">Kasi</span></b></small>';
								} elseif ($user_login->getField('LEVEL_PERENCANA') == '3') {
									$group .= '<br><small><b><span class="badge badge-dark">Kasubdit</span></b></small>';
								}
								break;
							case 'PENGELOLA KONTRAK':
								if ($user_login->getField('LEVEL_KONTRAK') == '1') {
									if ($user_login->getField('PENUNJUK_PIC') == '1') {
										$group .= '<br><small><b>Persiapan <span class="badge badge-warning">(Kasi)</span></b></small>';
									} else {
										$group .= '<br><small><b>Persiapan <span class="badge badge-primary">(Staff)</span></b></small>';
									}
								} elseif ($user_login->getField('LEVEL_KONTRAK') == '2') {
									if ($user_login->getField('PENUNJUK_PIC') == '1') {
										$group .= '<br><small><b>Pengendalian <span class="badge badge-warning">(Kasi)</span></b></small>';
									} else {
										$group .= '<br><small><b>Pengendalian <span class="badge badge-primary">(Staff)</span></b></small>';
									}
								} elseif ($user_login->getField('LEVEL_KONTRAK') == '3') {
									if ($user_login->getField('PENUNJUK_PIC') == '1') {
										$group .= '<br><small><b>Penyelesaian <span class="badge badge-warning">(Kasi)</span></b></small>';
									} else {
										$group .= '<br><small><b>Penyelesaian <span class="badge badge-primary">(Staff)</span></b></small>';
									}
								}
								break;
						}
						$row[] = $user_login->getField(trim($aColumns[$i])).' '.$group;
						// else{
						// 	$row[] = $user_login->getField(trim($aColumns[$i]));
						// }
					}

					elseif($aColumns[$i]=='USER_JABATAN')
					{
						$user_login2->selectByParams(array("USER_LOGIN_ID"=>$user_login->getField('USER_LOGIN_ID')),-1,-1);
						$user_login2->firstRow();
						if ($user_login2->getField('USER_JABATAN_PANITIA') == '') {
							$row[] = $user_login->getField(trim($aColumns[$i]));
						} else {
							// $row[] = $user_login->getField(trim($aColumns[$i])).'<b> '.$user_login2->getField('USER_JABATAN_PANITIA_STR').'</b>';
							$row[] = $user_login->getField(trim($aColumns[$i]));
						}
					}
					else
						$row[] = $user_login->getField(trim($aColumns[$i]));
						//$row[] = $user_login->getField($aColumns[$i]);
			}
			$output['aaData'][] = $row;
		}

		echo json_encode( $output );

	}

	function logs_login()
	{

		$this->load->model("UsersBase");
		$user_login = new UsersBase();

		$aColumns 			= array('USER_LOGIN_ID','USER_TYPE','USER_LOGIN','USER_LAST_LOGIN','SELISIH_LOGIN');
		$aColumnsAlias		= array('USER_LOGIN_ID', 'USER_TYPE','USER_LOGIN','USER_LAST_LOGIN','SELISIH_LOGIN');

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
			if ( trim($sOrder) == "ORDER BY SELISIH_LOGIN DESC" )
			{
				/*
				* If there is no order by clause - ORDER BY INDEX COLUMN!!! DON'T DELETE IT!
				* If there is no order by clause there might be bugs in table display.
				* No order by clause means that the db is not responsible for the data ordering,
				* which means that the same row can be displayed in two pages - while
				* another row will not be displayed at all.
				*/
				$sOrder = " ORDER BY A.SELISIH_LOGIN DESC";

			}
		} else {
			$sOrder = " ORDER BY A.SELISIH_LOGIN DESC";
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

		// $statement .= " AND NOT A.USER_TYPE_ID = 6 ";
		$statement .= "AND A.USER_LAST_LOGIN IS NOT NULL AND (UPPER(A.USER_NAMA) LIKE '%".strtoupper($_GET['sSearch'])."%' OR UPPER(A.USER_LOGIN) LIKE '".strtoupper($_GET['sSearch'])."%')";
		$allRecord = $user_login->getCountByParams(array(), $statement);
		if($_GET['sSearch'] == "")
			$allRecordFilter = $allRecord;
		else
			$allRecordFilter =  $user_login->getCountByParams(array(), $statement);
		$user_login->selectByParams(array(), $dsplyRange, $dsplyStart, $statement, $sOrder);

		$output = array(
			"sEcho" => intval($_GET['sEcho']),
			"iTotalRecords" => $allRecord,
			"iTotalDisplayRecords" => $allRecordFilter,
			"aaData" => array()
		);

		while($user_login->nextRow())
		{
			$row = array();
			for ( $i=0 ; $i<count($aColumns) ; $i++ )
			{
				if($aColumns[$i]=='NO')
						$row[] = $number;
					elseif($aColumns[$i]=='USER_LAST_LOGIN')
					{
						$explode = explode(".", $user_login->getField(trim($aColumns[$i])));
						$row[] = $explode[0];
						// if($user_login->getField(trim($aColumns[$i])))
						// 	$row[] = '<img src="images/centang.png">';
						// else
						// 	$row[] = '<img src="images/uncentang.png">';
					}
					elseif($aColumns[$i]=='SELISIH_LOGIN')
					{
						if ($user_login->getField(trim($aColumns[$i]))) {
							$explode = explode(".", $user_login->getField(trim($aColumns[$i])));
							$explode = str_replace("00:", "", $explode);
							$row[] = $explode[0].' ago';
						} else {
							$row[] = $explode[0];
						}
					}
					else
						$row[] = $user_login->getField(trim($aColumns[$i]));
						//$row[] = $user_login->getField($aColumns[$i]);
			}
			$output['aaData'][] = $row;
		}

		echo json_encode( $output );

	}

	function logs_multirole()
	{

		$this->load->model("Userloginmulti");
		$user_login_multi = new Userloginmulti();

		$aColumns 			= array('USER_LOGIN_MULTI_REKAM_ID','KEGIATAN','CREATED_DATE');
		$aColumnsAlias		= array('USER_LOGIN_MULTI_REKAM_ID', 'KEGIATAN','CREATED_DATE');

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

			if ( trim($sOrder) == "ORDER BY USER_LOGIN_MULTI_REKAM_ID DESC" )
			{
				$sOrder = " ORDER BY A.USER_LOGIN_MULTI_REKAM_ID DESC";

			}
		} else {
			$sOrder = " ORDER BY A.USER_LOGIN_MULTI_REKAM_ID DESC";
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
				//If there was no where clause
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

		$allRecord = $user_login_multi->getCountByParamsRekam(array(), $statement);
		if($_GET['sSearch'] == "")
			$allRecordFilter = $allRecord;
		else
			$allRecordFilter =  $user_login_multi->getCountByParamsRekam(array(), $statement);
		$user_login_multi->selectByParamsRekam(array(), $dsplyRange, $dsplyStart, $statement, $sOrder);

		$output = array(
			"sEcho" => intval($_GET['sEcho']),
			"iTotalRecords" => $allRecord,
			"iTotalDisplayRecords" => $allRecordFilter,
			"aaData" => array()
		);

		while($user_login_multi->nextRow())
		{
			$row = array();
			for ( $i=0 ; $i<count($aColumns) ; $i++ )
			{
				if($aColumns[$i]=='NO')
						$row[] = $number;
					elseif($aColumns[$i]=='CREATED_DATE')
					{
						$explode = explode(".", $user_login_multi->getField(trim($aColumns[$i])));
						$row[] = $explode[0];
					} 
					else
						$row[] = $user_login_multi->getField(trim($aColumns[$i]));
						//$row[] = $user_login->getField($aColumns[$i]);
			}
			$output['aaData'][] = $row;
		}

		echo json_encode( $output );

	}

	function logs_file_delete($file)
	{
        $dir   = 'logs/hooks/';
		if ($file) {
			unlink($dir.$file);
		}
		redirect(base_url('main/index/logs_file'));
	}

	function reset_password_json()
	{

		$this->load->library("kauth");  $userLogin = new kauth();
		$this->load->model("UsersBase");
		$user_login = new UsersBase();

		$reqId = $this->input->post("reqId");

		$str = " UPDATE
					USER_LOGIN SET USER_PASSWORD    = ".password_hash($this->getField(""), PASSWORD_DEFAULT)."
					WHERE  USER_LOGIN_ID = ".$this->ID." ";

	}

	function reset_password()
	{

		$this->load->library("kauth");  $userLogin = new kauth();
		$this->load->model("UsersBase");
		$user_login = new UsersBase();

		$reqId = $this->input->post("reqId");
		$reqSubmit = $this->input->post("reqSubmit");
		$reqPasswordBaru = $this->input->post("reqPasswordBaru");
		$reqPasswordKonfirmasi = $this->input->post("reqPasswordKonfirmasi");

		$user_login->setField("USER_PASSWORD", password_hash($reqPasswordBaru,PASSWORD_DEFAULT));
		$user_login->setField("USER_LOGIN_ID", $this->USER_LOGIN_ID);
		$user_login->resetPasswordBaru();

		echo "Password berhasil di reset";

	}

	function sayembara_json()
	{
		$this->load->model("UsersBase");
		$user_login = new UsersBase();

		/* LOGIN CHECK
		if ($userLogin->checkUserLogin())
		{
			$userLogin->retrieveUserInfo();
		}*/

		ini_set("memory_limit","500M");
		ini_set('max_execution_time', 520);

		$reqKeterangan = $this->input->post("reqKeterangan");
		$reqId = $this->input->post("reqId");
		$reqMode = $this->input->post("reqMode");
		$reqTipe = $this->input->post("reqTipe");
		$reqSearch = $this->input->post("reqSearch");

		/*
		 * Paging
		 */
		$sLimit = "";
		if ( isset( $_GET['iDisplayStart'] ) && $_GET['iDisplayLength'] != '-1' )
		{
			$sLimit = "LIMIT ".mysql_real_escape_string( $_GET['iDisplayStart'] ).", ".
				mysql_real_escape_string( $_GET['iDisplayLength'] );
		}
		else{
			$_GET['iDisplayStart'] = $_GET['iDisplayLength'] = '-1';
		}

		/*
		 * Ordering
		 */
		$sOrder = "";
			if ( isset( $_GET['iSortCol_0'] ) )
			{
				$sOrder = "ORDER BY  ";
				for ( $i=0 ; $i<intval( $_GET['iSortingCols'] ) ; $i++ )
				{
					if ( $_GET[ 'bSortable_'.intval($_GET['iSortCol_'.$i]) ] == "true" )
					{
						$sOrder .= 'upper('.$aColumns[ intval( $_GET['iSortCol_'.$i] ) ].")
							".mysql_real_escape_string( $_GET['sSortDir_'.$i] ) .", ";
					}
				}

				$sOrder = substr_replace( $sOrder, "", -2 );
				if ( $sOrder == "ORDER BY" )
				{
					$sOrder = "";
				}
			}

		if($reqTipe == 'Ubah'){
			$user = new UsersBase();
			$user->setField("USER_LOGIN_ID",$reqId);
			$user->setField("USER_STATUS",$reqMode);

			if($user->update_status()){}
		}
		if($reqSearch == "")
		{
			$allRecord = $user_login->getCountByParams(array("USER_TYPE_ID"=>22), $statement);
			$user_login->selectByParamsSayembara(array("USER_TYPE_ID"=>22), $_GET['iDisplayLength'], $_GET['iDisplayStart'], $statement);
		}
		else
		{
			$reqSearch = str_replace('\\', '', $reqSearch);
			$allRecord = 1;
			//$user_login->selectByParams(array(), $_GET['iDisplayLength'], $_GET['iDisplayStart'], $statement.$reqSearch." ");

		}

		$column = array('USER_LOGIN_ID', 'NO', 'NO_REG','USER_NAMA','USER_LOGIN','USER_JABATAN','USER_STATUS');
			/*
			 * Output
			 */
			$output = array(
				"sEcho" => intval($_GET['sEcho']),
				"iTotalRecords" => $allRecord,
				"iTotalDisplayRecords" => $allRecord,
				"aaData" => array()
			);
			$number = 1;
			while($user_login->nextRow())
			{
				$row = array();
				for ( $i=0 ; $i<count($column) ; $i++ )
				{
					if($column[$i]=='NO')		$row[] = $number;
					elseif($column[$i]=='USER_STATUS'){
						if($user_login->getField(trim($column[$i]))){
							$row[] = "<img src='WEB-INF/base-main/images/centang.png'>";
						}else{
							$row[] = "<img src='WEB-INF/base-main/images/uncentang.png'>";
						}
					}
					else						$row[] = $user_login->getField(trim($column[$i]));
				}
				$number++;
				$output['aaData'][] = $row;
			}

			echo json_encode( $output );

	}

	function master_daftar_user_non_rekanan_add()
	{
		/* INCLUDE FILE */
		$this->load->library("kauth");  $userLogin = new kauth();
		$this->load->model("Users");
		$this->load->model("UsersBase");
		$this->load->model("PesertaLomba");
		$this->load->model("Rekanan");
		$this->load->model("UserType");
		$this->load->model("UnitKerja");

		/* create objects */
		$user_type = new UserType();
		$rekanan = new Rekanan();
		$user_login = new Users();
		$unitKerja = new UnitKerja();

		$peserta = new PesertaLomba();

		/* VARIABLE */
		$reqId	= $this->input->post("reqId");
		$reqMode	= $this->input->post("reqMode");
		$reqTipe	= $this->input->post("reqTipe");
		$reqTipe2	= $this->input->post("reqTipe2") ? $this->input->post("reqTipe2") : 0; // CHILD PL 				(NOT USE)
		$reqTipe3	= $this->input->post("reqTipe3") ? $this->input->post("reqTipe3") : 0; // PPK 					(NOT USE)
		$reqTipe5	= $this->input->post("reqTipe5") ? $this->input->post("reqTipe5") : 0; // Kepala Pengadaan		(NOT USE)
		$reqTipe6	= $this->input->post("reqTipe6") ? $this->input->post("reqTipe6") : 0; // Admin RUP 			(NOT USE)
		$reqTipe7	= $this->input->post("reqTipe7") ? $this->input->post("reqTipe7") : 0; // Tender atau Non Tender (NOT USE)
		$reqUserJabatanPanitia	= explode(':', $this->input->post("reqUserJabatanPanitia"));
 
		// reqTipe9 // level perencana
		// reqTipe8 // penunjuk PIC
		// reqTipe10 // Level Kontrak
		// reqTipe11 // Level Pengguna
		// reqTipe12 // Level PJP


		if ($reqTipe == '12') { // Pengelola Kontrak
			$reqTipe8	= $this->input->post("reqTipe8") ? $this->input->post("reqTipe8") : 0; // penunjuk PIC
			$reqTipe10	= $this->input->post("reqTipe10") ? $this->input->post("reqTipe10") : 0; // Level Kontrak
		} else {
			$reqTipe8	= '';
			$reqTipe10	= '';
		}

		if ($reqTipe == '27') { // Perenana
			$reqTipe9	= $this->input->post("reqTipe9") ? $this->input->post("reqTipe9") : 0; // Level Perencana 1:Kasubdit,2:Kasi,3:Staff
		} else {
			$reqTipe9	= '';
		}

		if ($reqTipe == '11') { // Pejabat Pengadaan
			$reqTipe12	= $this->input->post("reqTipe12") ? $this->input->post("reqTipe12") : 0; // Level PJP
		} else {
			$reqTipe12	= '';
		}

		if ($reqTipe == '9') { // Pengguna
			$reqTipe11	= $this->input->post("reqTipe11") ? $this->input->post("reqTipe11") : 0; // Level Pengguna

			if ($reqTipe11 == '1') { // PIC
				$reqTipe13	= 'null';
			} else {
				$reqTipe13	= $this->input->post("reqTipe13") ? $this->input->post("reqTipe13") : 'null'; // PIC Pengguna
			}

		} else {
			$reqTipe11	= '';
			$reqTipe13	= 'null';
		}

		if ($reqTipe == '3') { // Pelaksana Pengadaan
			$reqUserJabatanPanitia0 = '1';
			$reqUserJabatanPanitia1 = 'Ketua';
		} else {
			$reqUserJabatanPanitia0 = '';
			$reqUserJabatanPanitia1 = '';
		}

		$reqNamaUser	= $this->input->post("reqNamaUser");
		$reqNama = $this->input->post("reqNama");
		$reqPasswordRetype	= $this->input->post("reqPasswordRetype");
		$reqPassword	= $this->input->post("reqPassword");
		$reqAlamat	= $this->input->post("reqAlamat");
		$reqTipePeserta	= $this->input->post("reqTipePeserta");
		$reqJabatan	= $this->input->post("reqJabatan");
		$reqTelepon	= $this->input->post("reqTelepon");
		$reqSubmit	= $this->input->post("reqSubmit");
		$reqNamaTemp    = $this->input->post('reqNamaTemp');
		$reqUnitKerja   = $this->input->post('reqUnitKerja');
		$reqNip   = $this->input->post('reqNip');
		$reqDirektorat   = $this->input->post('reqDirektorat') ?: 0;
		$reqDepartment   = $this->input->post('reqDepartment');

		$tmpNamaUser = $reqNamaUser;
		$tmpNama = $reqNama;
		$tmpAlamat = $reqAlamat;
		$tmpPasswordRetype = $reqPasswordRetype;
		$tmpPassword = $reqPassword;
		$tmpTipe = $reqTipe;
		$tmpJabatan = $reqJabatan;
		$tmpTelepon = $reqTelepon;
		$tmpUnitKerja = $reqUnitKerja;
		$tmpNip = $reqNip;
		$tmpDirektorat = $reqDirektorat;
		$tmpDepartment = $reqDepartment;

		// trigger the validation

		if($reqSubmit == "Submit") {
			$allRecord = $user_login->getCountByParams_onedha(array(), " AND UPPER(USER_LOGIN) = '".strtoupper($reqNamaUser)."' ");
		}
		else {
			$allRecord = $user_login->getCountByParams_onedha(array(), " AND UPPER(USER_LOGIN) = '".strtoupper($reqNamaUser)."'  AND UPPER(USER_LOGIN) != '".strtoupper($reqNamaTemp)."' ");
		}
		// echo $allRecord."-asd";

		/* ACTION BY REQMODE */
		if($allRecord > 0 && $reqMode == "insert"){
			echo "Username telah ada, penyimpanan data tidak akan di proses..";
			return;
		}
		/*
		elseif($allRecord > 0 && $reqSubmit == "Update"){
			echo "Username telah ada, penyimpanan data tidak akan di proses.";
			return;
		}
		*/

		$user_login->setField("USER_LOGIN_ID",$reqId);
		$user_login->setField("USER_LOGIN",$reqNamaUser);
		$user_login->setField("USER_NAMA",$reqNama);
		$user_login->setField("USER_PASSWORD",password_hash($reqPassword,PASSWORD_DEFAULT));
		$user_login->setField("USER_TYPE_ID",$reqTipe);
		$user_login->setField("USER_JABATAN",$reqJabatan);
		$user_login->setField("USER_TELEPON",$reqTelepon);
		$user_login->setField("USER_ALAMAT",$reqAlamat);
		$user_login->setField("REKANAN_ID","null");
		$user_login->setField("USER_STATUS",'0');
		$user_login->setField('UNIT_KERJA_ID', $reqUnitKerja);
		$user_login->setField('NIP', $reqNip);
		$user_login->setField('CHILD_PL', $reqTipe2);
		$user_login->setField('PPK', $reqTipe3);
		$user_login->setField('VP_PENGADAAN', $reqTipe5);
		$user_login->setField('ADMIN_RUP', $reqTipe6);
		$user_login->setField('TENDER', $reqTipe7);
		$user_login->setField('PENUNJUK_PIC', $reqTipe8);
		$user_login->setField('LEVEL_KONTRAK', $reqTipe10);
		$user_login->setField('LEVEL_PEMBELI', $reqTipe12);
		$user_login->setField('LEVEL_PERENCANA', $reqTipe9);
		$user_login->setField('LEVEL_PENGGUNA', $reqTipe11);
		$user_login->setField('KASI_PENGGUNA', $reqTipe13);
		$user_login->setField('DEPARTMENT', $reqDepartment);
		$user_login->setField('DIREKTORAT_ID', $reqDirektorat);
		// $user_login->setField('USER_JABATAN_PANITIA', $reqUserJabatanPanitia[0]);
		// $user_login->setField('USER_JABATAN_PANITIA_STR', $reqUserJabatanPanitia[1]);
		$user_login->setField('USER_JABATAN_PANITIA', $reqUserJabatanPanitia0);
		$user_login->setField('USER_JABATAN_PANITIA_STR', $reqUserJabatanPanitia1);
		$user_login->setField('CREATED_BY', $this->USER_LOGIN_ID);

		if($reqMode == "insert")
		{
			if($user_login->insert())
				echo "Data berhasil di Simpan";
		}
		else
		{
			if($user_login->update())
				echo "Data berhasil di Update";
		}

	}

	function master_daftar_user_non_rekanan_add_akses()
	{
		/* INCLUDE FILE */
		$this->load->library("kauth");  $userLogin = new kauth();
		$this->load->model("Users");
		$this->load->model("Userloginmulti");

		/* create objects */
		$user_login_multi = new Userloginmulti();

		/* VARIABLE */ 
		// echo "<pre>"; print_r($this->input->post()); die;
		$reqId	= $this->input->post("reqId");
		$reqMode	= $this->input->post("reqMode");
		$reqTipe	= $this->input->post("reqTipe");
		// reqTipe8 // penunjuk PIC
		// reqTipe9 // level perencana
		// reqTipe10 // Level Kontrak
		// reqTipe11 // Level Pengguna
		// reqTipe12 // Level PJP

		if ($reqTipe == '12') { // Pengelola Kontrak
			$reqTipe8	= $this->input->post("reqTipe8") ? $this->input->post("reqTipe8") : 0; // penunjuk PIC
			$reqTipe10	= $this->input->post("reqTipe10") ? $this->input->post("reqTipe10") : 0; // Level Kontrak
		} else {
			$reqTipe8	= '';
			$reqTipe10	= '';
		}

		if ($reqTipe == '27') { // Perenana
			$reqTipe9	= $this->input->post("reqTipe9") ? $this->input->post("reqTipe9") : 0; // Level Perencana 1:Kasubdit,2:Kasi,3:Staff
		} else {
			$reqTipe9	= '';
		}

		if ($reqTipe == '11') { // Pejabat Pengadaan
			$reqTipe12	= $this->input->post("reqTipe12") ? $this->input->post("reqTipe12") : 0; // Level PJP
		} else {
			$reqTipe12	= '';
		}

		if ($reqTipe == '9') { // Pengguna
			$reqTipe11	= $this->input->post("reqTipe11") ? $this->input->post("reqTipe11") : 0; // Level Pengguna

			if ($reqTipe11 == '1') { // PIC
				$reqTipe13	= 'null';
			} else {
				$reqTipe13	= $this->input->post("reqTipe13") ? $this->input->post("reqTipe13") : 'null'; // PIC Pengguna
			}

		} else {
			$reqTipe11	= '';
			$reqTipe13	= 'null';
		}

		if ($reqTipe == '3') { // Pelaksana Pengadaan
			$reqUserJabatanPanitia0 = '1';
			$reqUserJabatanPanitia1 = 'Ketua';
		} else {
			$reqUserJabatanPanitia0 = '';
			$reqUserJabatanPanitia1 = '';
		}

		$reqSubmit	= $this->input->post("reqSubmit");
		$reqNamaTemp    = $this->input->post('reqNamaTemp');
		$reqNip   = $this->input->post('reqNip');
		$reqDirektorat   = $this->input->post('reqDirektorat') ?: 0;
		$reqDepartment   = $this->input->post('reqDepartment');

		$tmpTipe = $reqTipe;
		$tmpJabatan = $reqJabatan;
		$tmpTelepon = $reqTelepon;
		$tmpNip = $reqNip;
		$tmpDirektorat = $reqDirektorat;
		$tmpDepartment = $reqDepartment;

		$user_login_multi->setField("USER_LOGIN_ID",$reqId);
		$user_login_multi->setField("USER_TYPE_ID",$reqTipe);
		$user_login_multi->setField('PENUNJUK_PIC', $reqTipe8);
		$user_login_multi->setField('LEVEL_KONTRAK', $reqTipe10);
		$user_login_multi->setField('LEVEL_PEMBELI', $reqTipe12);
		$user_login_multi->setField('LEVEL_PERENCANA', $reqTipe9);
		$user_login_multi->setField('LEVEL_PENGGUNA', $reqTipe11);
		$user_login_multi->setField('KASI_PENGGUNA', $reqTipe13);
		$user_login_multi->setField('CREATED_BY', $this->USER_LOGIN_ID);

		// if($reqMode == "insert")
		// {
			if($user_login_multi->insert())
				echo "Data berhasil di Simpan";
		// }
		// else
		// {
		// 	if($user_login_multi->update())
		// 		echo "Data berhasil di Update";
		// }

	}

	function reset_password_daftar_user_non_rekanan()
	{
		$this->load->model("Users");
		$this->load->model("UsersBase");
		$user_login = new Users();

		$reqId =  $this->input->get('reqId');
		$reqPassword =  $this->input->get('reqPassword');
		//echo $reqPassword."fdsfdsfds";exit;
		$user_login->setField("USER_PASSWORD", password_hash($reqPassword,PASSWORD_DEFAULT));
		$user_login->setField("USER_LOGIN_ID", $reqId);
		$user_login->resetPasswordBaru();

			echo "Password berhasil di reset";
	}

	function ubah_status()
	{
		$this->load->model("UsersBase");

		$user_login = new UsersBase();

		/* json set variable */
		$reqId =  $this->input->get('reqId');

		$user_login->selectByParams(array("USER_LOGIN_ID"=>$reqId),-1,-1);
		$user_login->firstRow();
		$tmpStatus = $user_login->getField("USER_STATUS");

		if($tmpStatus == 1)
			$tmpStatus = 0;
		else
			$tmpStatus = 1;

		$user = new UsersBase();
		$user->setField("USER_LOGIN_ID", $reqId);
		$user->setField("USER_STATUS", $tmpStatus);

		if($user->update_status())
			$arrJson["PESAN"] = "Status berhasil di ubah";
		else
			$arrJson["PESAN"] = "Status gagal di ubah";

		echo json_encode($arrJson);

	}

	function ubah_status_aktif()
	{
		$this->load->model("UsersBase");

		$user_login = new UsersBase();

		/* json set variable */
		$reqId =  $this->input->get('reqId');

		$user_login->selectByParams(array("USER_LOGIN_ID"=>$reqId),-1,-1);
		$user_login->firstRow();
		$tmpStatus = $user_login->getField("USER_AKTIF");

		if($tmpStatus == 1)
			$tmpStatus = 0;
		else
			$tmpStatus = 1;

		$user = new UsersBase();
		$user->setField("USER_LOGIN_ID", $reqId);
		$user->setField("USER_AKTIF", $tmpStatus);
		$user->setField('CREATED_BY', $this->USER_LOGIN_ID);

		if($user->update_status_aktif2())
			$arrJson["PESAN"] = "Status Aktif berhasil di ubah";
		else
			$arrJson["PESAN"] = "Status Aktif gagal di ubah";

		echo json_encode($arrJson);

	}

	function ubah_status2()
	{
		$this->load->model("UsersBase");

		$user_login = new UsersBase();

		/* json set variable */
		$reqId =  $this->input->get('reqId');

		$user_login->selectByParams(array("REKANAN_ID"=>$reqId),-1,-1);
		$user_login->firstRow();
		$tmpStatus = $user_login->getField("USER_STATUS");

		if($tmpStatus == 1)
			$tmpStatus = 0;
		else
			$tmpStatus = 1;

		$user = new UsersBase();
		$user->setField("REKANAN_ID", $reqId);
		$user->setField("USER_STATUS", $tmpStatus);

		if($user->update_status2())
			$arrJson["PESAN"] = "Status berhasil di ubah";
		else
			$arrJson["PESAN"] = "Status gagal di ubah";

		echo json_encode($arrJson);

	}

	function delete()
	{
		$this->load->model("UsersBase");
		$user_login = new UsersBase();
		$reqId =  $this->input->get('reqId');
		$user_login->setField('USER_LOGIN_ID', $reqId);
		if($user_login->delete())
			echo "Data berhasil dihapus";
		else
			echo "Data gagal dihapus";
	}

	function get_data_daftar_pihak_lain()
	{
		$this->load->model("UsersBase");
		$user_base = new UsersBase();

		$reqId = $this->input->get("reqId");

		if($reqId == '')
		{}
		else
			$statement =" AND NOT EXISTS (SELECT 1 FROM PAKET_PIHAK_LAIN X WHERE X.USER_LOGIN_ID = A.USER_LOGIN_ID AND X.PAKET_ID = '".$reqId."')
						 and  a.user_type_id in (8,9)";

		$user_base->selectByParams(array(),-1,-1, $statement);

		$met = array();
		$i=0;

		while($user_base->nextRow()){
			$met[$i]['id'] = $user_base->getField('USER_LOGIN_ID');
			$met[$i]['text'] = $user_base->getField('USER_NAMA');
			$met[$i]['USER_NAMA'] = $user_base->getField('USER_NAMA');
			$met[$i]['USER_LOGIN'] = $user_base->getField('USER_LOGIN');
			$met[$i]['USER_TYPE'] = $user_base->getField('USER_TYPE');
			$i++;
		}
		echo json_encode($met);
	}

	function reset_password_rekanan()
	{

		$this->load->library("kauth");  $userLogin = new kauth();
		$this->load->model("UsersBase");
		$user_login = new UsersBase();

		$reqId = $this->input->post("reqId");
		$reqSubmit = $this->input->post("reqSubmit");
		$reqPasswordBaru = $this->input->post("reqPasswordBaru");
		$reqPasswordKonfirmasi = $this->input->post("reqPasswordKonfirmasi");


		$user_login->selectByUserLogin(array("MD5(REKANAN_ID || 'IKUN')" => $reqId));
		$user_login->firstRow();

		$reqUserLoginId = $user_login->getField("USER_LOGIN_ID");

		$user_login->setField("USER_PASSWORD", password_hash($reqPasswordBaru,PASSWORD_DEFAULT));
		$user_login->setField("USER_LOGIN_ID", $reqUserLoginId);
		$user_login->resetPasswordBaru();

		echo "Password berhasil di reset";

	}

	function reject_users()
	{ 
		$this->load->model("UsersBase"); 
		$user_login = new UsersBase();

		$reqId = $this->input->get("reqId");
		$reqNote3 = $this->input->get("reqNote3");

		$user_login->setField("USER_LOGIN_ID", $reqId);
		$user_login->setField("CATATAN_TOLAK", $reqNote3);
		$user_login->setField("USER_AKTIF", '3');
		$user_login->setField("UPDATED_BY", $this->USER_LOGIN_ID);
		if($user_login->update_catatan())
		{
			$arrJson["PESAN"] = "Akun User eProc Berhasil di kembalikan"; 
		} else {
			$arrJson["PESAN"] = "Akun User eProc Gagal di kembalikan, silakan coba beberapa saat lagi!"; 
		}

		echo json_encode($arrJson);

	}

	public function getUserGroup($userid)
	{
		sleep(0);

		$this->load->model(array("Userloginmulti","Userlogin"));
		$user_login_multi = new Userloginmulti();
		$user_login2 = new Userlogin();

		$user_login_cek = new Userlogin();
		$user_login_multi_cek = new Userloginmulti();
		$user_login_cek->selectByParams(array("USER_LOGIN_ID"=>$userid),-1,-1);
      	$user_login_cek->firstRow();
  		$user_login_multi_cek->selectByParams(array("USER_LOGIN_ID"=>$userid, "A.USER_TYPE_ID" => $user_login_cek->getField('USER_TYPE_ID')),-1,-1);
		if ($user_login_multi_cek->countRow() == 0) { 
			$penunjuk_pic = $user_login_cek->getField('PENUNJUK_PIC') ?: 0;
			$level_kontrak = $user_login_cek->getField('LEVEL_KONTRAK') ?: 0;
			$level_pembeli = $user_login_cek->getField('LEVEL_PEMBELI') ?: 0;
			$level_perencana = $user_login_cek->getField('LEVEL_PERENCANA') ?: 0;
			$level_pengguna = $user_login_cek->getField('LEVEL_PENGGUNA') ?: 0;
			$kasi_pengguna = $user_login_cek->getField('KASI_PENGGUNA') ?: 'null';

			$user_login_multi_insert = new Userloginmulti();
			$user_login_multi_insert->setField("USER_LOGIN_ID",$userid);
			$user_login_multi_insert->setField("USER_TYPE_ID",$user_login_cek->getField('USER_TYPE_ID'));
			$user_login_multi_insert->setField('PENUNJUK_PIC', $penunjuk_pic);
			$user_login_multi_insert->setField('LEVEL_KONTRAK', $level_kontrak);
			$user_login_multi_insert->setField('LEVEL_PEMBELI', $level_pembeli);
			$user_login_multi_insert->setField('LEVEL_PERENCANA', $level_perencana);
			$user_login_multi_insert->setField('LEVEL_PENGGUNA', $level_pengguna);
			$user_login_multi_insert->setField('KASI_PENGGUNA', $kasi_pengguna);
			$user_login_multi_insert->setField('CREATED_BY', $this->USER_LOGIN_ID);
			$user_login_multi_insert->insert();
		}

		$html  = '<table class="table table-bordered table-hover" id="contentGroupUser">
	                <tr style="background-color: #000; color:#fff">
	                  <th width="10px">No.</th>
	                  <th>Tipe User</th>
	                  <th width="10px">Aksi</th>
	                </tr> ';

  		$user_login_multi->selectByParams(array("USER_LOGIN_ID"=>$userid),-1,-1);
	    
		if ($user_login_multi->countRow() > 0) { 
		$no=1;
		while($user_login_multi->nextRow()) {
		  	$html .= '<tr>
					    <td>'.$no.'</td>
					    <td>';
			$html .= '	  '.$user_login_multi->getField('NAMA').'';
					      switch ($user_login_multi->getField('NAMA')) 
					      {
					        case 'PENGGUNA':
					          if ($user_login_multi->getField('LEVEL_PENGGUNA') == '1') {
					            $html .= '<br><small><b><span class="badge badge-warning">PIC</span></b></small>';
					          } else {
					            if ($user_login_multi->getField('KASI_PENGGUNA')) {
					              $user_login2->selectByParams(array("USER_LOGIN_ID"=>$user_login_multi->getField('KASI_PENGGUNA')),-1,-1);
					              $user_login2->firstRow();
					              $html .= '<br><small><b><span class="badge badge-primary">Staff</span></b></small>
					                    <small><b><span class="badge badge-primary">PIC: '.$user_login2->getField('USER_NAMA').'</span></b></small>';
					            } else {
					              $html .= '<br><small><b><span class="badge badge-primary">Staff</span></b></small>
					                    <small><b><span class="badge badge-danger">PIC belum ditetapkan</span></b></small>';
					            }
					          }
					          break;
					        case 'PEJABAT PENGADAAN':
					          if ($user_login_multi->getField('LEVEL_PEMBELI') == '1') {
					            $html .= '<br><small><b><span class="badge badge-warning">Kasi</span></b></small>';
					          } else {
					            $html .= '<br><small><b><span class="badge badge-primary">Staff</span></b></small>';
					          }
					          break;
					        case 'PERENCANAAN':
					          if ($user_login_multi->getField('LEVEL_PERENCANA') == '1') {
					            $html .= '<br><small><b><span class="badge badge-primary">Staff</span></b></small>';
					          } elseif ($user_login_multi->getField('LEVEL_PERENCANA') == '2') {
					            $html .= '<br><small><b><span class="badge badge-warning">Kasi</span></b></small>';
					          } elseif ($user_login_multi->getField('LEVEL_PERENCANA') == '3') {
					            $html .= '<br><small><b><span class="badge badge-dark">Kasubdit</span></b></small>';
					          }
					          break;
					        case 'PENGELOLA KONTRAK':
					          if ($user_login_multi->getField('LEVEL_KONTRAK') == '1') {
					            if ($user_login_multi->getField('PENUNJUK_PIC') == '1') {
					              $html .= '<br><small><b>Persiapan <span class="badge badge-warning">(Kasi)</span></b></small>';
					            } else {
					              $html .= '<br><small><b>Persiapan <span class="badge badge-primary">(Staff)</span></b></small>';
					            }
					          } elseif ($user_login_multi->getField('LEVEL_KONTRAK') == '2') {
					            if ($user_login_multi->getField('PENUNJUK_PIC') == '1') {
					              $html .= '<br><small><b>Pengendalian <span class="badge badge-warning">(Kasi)</span></b></small>';
					            } else {
					              $html .= '<br><small><b>Pengendalian <span class="badge badge-primary">(Staff)</span></b></small>';
					            }
					          } elseif ($user_login_multi->getField('LEVEL_KONTRAK') == '3') {
					            if ($user_login_multi->getField('PENUNJUK_PIC') == '1') {
					              $html .= '<br><small><b>Penyelesaian <span class="badge badge-warning">(Kasi)</span></b></small>';
					            } else {
					              $html .= '<br><small><b>Penyelesaian <span class="badge badge-primary">(Staff)</span></b></small>';
					            }
					          }
					        break;
					          default:
					          $html .= '';
					          break;
					        }
			$html .=	'</td>
					    <td><a id="btnDelete_'.$user_login_multi->getField('USER_LOGIN_MULTI_ID').'" style="color:#fff" class="badge badge-danger" onclick="return aaa(\''.$user_login_multi->getField('USER_LOGIN_MULTI_ID').'\')"><span class="fa fa-trash"></span></span></a>
                          	<span id="msgDelete_'.$user_login_multi->getField('USER_LOGIN_MULTI_ID').'"></span>
                        </td>
					  </tr>';
				$no++;
			}
		} else {
			$html .= '<tr><td colspan="3">. : : Tidak ada data : : .</td></tr>';
		}

		$html .= '</table>';

		echo json_encode(array('respon' => 'false', 'message' => $html));
	}

	public function excUserGroupDelete($userid,$id)
	{
		sleep(1);
		$this->load->model("Userloginmulti");
		$userloginmulti = new Userloginmulti();
		$userloginmulti->setField("USER_LOGIN_ID", $userid);
		$userloginmulti->setField("USER_LOGIN_MULTI_ID", $id);

		$html  = '';
		if($userloginmulti->deleteData())
		{
			$respon = "true";
			$html 	.= "Data Berhasil di hapus";
		} else {
			$respon  = "false";
			$html 	.= "Data Gagal di hapus, silahkan dicoba kembali";
		}

		echo json_encode(array('respon' => $respon, 'message' => $html));
	}

	public function excSplitRole($multiid,$userid)
	{
		sleep(1);
		$this->load->model(array("UsersBase","Userloginmulti"));
		$userlogin = new UsersBase();
		$userloginmulti = new Userloginmulti();
		$userloginmultihistory = new Userloginmulti();

		$userloginmulti->selectByParams(array("USER_LOGIN_MULTI_ID" => $multiid, "USER_LOGIN_ID" => $userid));
		$userloginmulti->firstRow();
		$user_login_id = $userloginmulti->getField('USER_LOGIN_ID');
		$user_type_id = $userloginmulti->getField('USER_TYPE_ID');
		$penunjuk_pic = $userloginmulti->getField('PENUNJUK_PIC');
		$level_kontrak = $userloginmulti->getField('LEVEL_KONTRAK');
		$level_perencana = $userloginmulti->getField('LEVEL_PERENCANA');
		$level_pembeli = $userloginmulti->getField('LEVEL_PEMBELI');
		$level_pengguna = $userloginmulti->getField('LEVEL_PENGGUNA');
		$kasi_pengguna = $userloginmulti->getField('KASI_PENGGUNA') ?: 'null';

		$userlogin->setField("USER_LOGIN_ID", $user_login_id);
		$userlogin->setField("USER_TYPE_ID", $user_type_id);
		$userlogin->setField("PENUNJUK_PIC", $penunjuk_pic);
		$userlogin->setField("LEVEL_KONTRAK", $level_kontrak);
		$userlogin->setField("LEVEL_PERENCANA", $level_perencana);
		$userlogin->setField("LEVEL_PEMBELI", $level_pembeli);
		$userlogin->setField("LEVEL_PENGGUNA", $level_pengguna);
		$userlogin->setField("KASI_PENGGUNA", $kasi_pengguna);
		$userlogin->setField("UPDATED_BY", $this->USER_LOGIN_ID);

		$html  = '';
		if($userlogin->updateRoleUser())
		{
			$userloginmultihistory->setField("USER_LOGIN_ID", $this->USER_LOGIN_ID);
			$userloginmultihistory->setField("USER_TYPE_ID_OLD", $this->USER_TYPE_ID);
			$userloginmultihistory->setField("USER_TYPE_ID_NEW", $user_type_id);
			$userloginmultihistory->setField("CREATED_BY", $this->USER_LOGIN_ID);
			$userloginmultihistory->insertHistory();

			if($this->kauth->reloadlocalAuthenticate($user_login_id))
			{
				$respon = "true";
				$html 	.= "Data Berhasil di simpan";
			} else {
				$respon  = "false";
				$html 	.= "Data Gagal di simpan, silahkan dicoba kembali..";
			}


		} else {
			$respon  = "false";
			$html 	.= "Data Gagal di simpan, silahkan dicoba kembali...";
		}

		echo json_encode(array('respon' => $respon, 'message' => $html));
	}

	

}
?>
