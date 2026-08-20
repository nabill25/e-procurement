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

class paket_progres_template_json extends CI_Controller {

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
	}	
	
	function paket_progres_template_combo_json() 
	{
		$this->load->library("kauth");  $userLogin = new kauth(); 
		$this->load->model("PaketProgresTemplate");
		$set= new PaketProgresTemplate();
		
		/* LOGIN CHECK */
		if ($userLogin->checkUserLogin()) 
		{ 
			$userLogin->retrieveUserInfo();
		}
		
		$reqStatus= httpFilterGet("reqStatus");
		
		$arr_json = array();
		$i = 0;
		
		// setkondisi
		//$statement= " AND A.KELOMPOK_PEGAWAI = 'D' ";
		//$statement= " AND A.PEGAWAI_ID = ".$userLogin->setId;
		$set->selectByParams(array(),-1,-1,$statement);
		while($set->nextRow()){
			$arr_json[$i]['id'] = $set->getField("PAKET_PROGRES_TEMPLATE_ID");
			$arr_json[$i]['text'] = $set->getField("NAMA");
			$arr_json[$i]['tipe'] = $set->getField("USER_TYPE_ID");
			$arr_json[$i]['keterangan'] = $set->getField("KETERANGAN");
			$i++;
		}
		
		echo json_encode($arr_json);
	}
	
	function pembagian_pic_monitoring_json() 
	{
		$this->load->model("PaketProgresTemplate");
		$set = new PaketProgresTemplate();
		
		/* LOGIN CHECK 
		if ($userLogin->checkUserLogin()) 
		{ 
			$userLogin->retrieveUserInfo();
		}*/
		
		ini_set("memory_limit","500M");
		ini_set('max_execution_time', 5200);
		
		$reqStatus= httpFilterGet("reqStatus");
		$aColumns = array("PAKET_ID", "NAMA", "UNIT_KERJA", "PAKET_JENIS", "LOKASI", "METODE_LELANG", "METODE_KUALIFIKASI", "REKANAN_KUALIFIKASI", "METODE_EVALUASI", "PEMENANG");
		$aColumnsAlias = array("PAKET_ID", "NAMA", "UNIT_KERJA", "PAKET_JENIS", "LOKASI", "METODE_LELANG", "METODE_KUALIFIKASI", "REKANAN_KUALIFIKASI", "METODE_EVALUASI", "PEMENANG");
		
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
					if (strcasecmp(( $_GET['sSortDir_'.$i] ), "asc") == 0)
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
			if ( trim($sOrder) == "ORDER BY RECRUITMENT asc, RECRUITMENT asc" )
			{
				/*
				* If there is no order by clause - ORDER BY INDEX COLUMN!!! DON'T DELETE IT!
				* If there is no order by clause there might be bugs in table display.
				* No order by clause means that the db is not responsible for the data ordering,
				* which means that the same row can be displayed in two pages - while
				* another row will not be displayed at all.
				*/
				$sOrder = " ORDER BY A.RECRUITMENT_ID ASC, A.TANGGAL_AWAL_REN ASC";
				 
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
		
		
		$statement .= " AND A.UNIT_KERJA_ID = '".$userLogin->unitKerjaId."' ";
		
		$searchJson .= " AND (UPPER(A.NAMA) LIKE '%".strtoupper($_GET['sSearch'])."%' OR UPPER(I.NAMA) LIKE '%".strtoupper($_GET['sSearch'])."%')";
		
		$allRecord = $set->getCountByParamsMonitoring(array(),$statement);
		if($_GET['sSearch'] == "")
			$allRecordFilter = $allRecord;
		else	
			$allRecordFilter = $set->getCountByParamsMonitoring(array(), $statement.$searchJson);
		
		$set->selectByParamsMonitoring(array(), $dsplyRange, $dsplyStart, $statement.$searchJson, $sOrder);
		//echo $set->query;
		/* Output */
		$output = array(
			"sEcho" => intval($_GET['sEcho']),
			"iTotalRecords" => $allRecord,
			"iTotalDisplayRecords" => $allRecordFilter,
			"aaData" => array()
		);
		
		while($set->nextRow())
		{
			$row = array();
			for ( $i=0 ; $i<count($aColumns) ; $i++ )
			{
				if($aColumns[$i] == "TANGGAL")
					$row[] = getFormattedDate($set->getField($aColumns[$i]));
				else if($aColumns[$i] == "KETERANGAN")
					$row[] = truncate($set->getField($aColumns[$i]), 5)."...";
				else
					$row[] = $set->getField($aColumns[$i]);
			}
			
			$output['aaData'][] = $row;
		}
		
		echo json_encode( $output );
	}
	
	function pic_tipe_combo_json() 
	{
		
		$this->load->library("kauth");  $userLogin = new kauth(); 
		$this->load->model("PaketProgresTemplate");
		
		$paket_progres_template= new PaketProgresTemplate();
		
		/* LOGIN CHECK */
		if ($userLogin->checkUserLogin()) 
		{ 
			$userLogin->retrieveUserInfo();
		}
		
		$reqTipe= httpFilterGet("reqTipe");
		$reqId= httpFilterGet("reqId");
		
		$arr_json = array();
		$i = 0;
		
		// paket_progres_templatekondisi
		if($reqTipe == "9")
			$paket_progres_template->selectByParamsPicUserLogin(array(),-1,-1, " AND USER_TYPE_ID = 9 ");
		else
		{
			if($reqId == "")
			{
				if($userLogin->unitKerjaId == 1 || $userLogin->unitKerjaId == 2 || $userLogin->unitKerjaId == 24)
					$statement .= " AND UPPER(NAMA_SEK) LIKE '%%' AND KD_PEL = '".generateZero($userLogin->unitKerjaId, 2)."' ";
				else
					$statement .= " AND KD_PEL = '".generateZero($userLogin->unitKerjaId, 2)."' ";
		
			}
			else
				$statement .= " AND NIPP = '".$reqId."' ";
		
			$paket_progres_template->selectByParamsPic(array(),-1,-1,$statement);
		}
		
		while($paket_progres_template->nextRow()){
			$arr_json[$i]['id'] = $paket_progres_template->getField("USER_LOGIN_ID");
			$arr_json[$i]['text'] = $paket_progres_template->getField("USER_NAMA");
			$i++;
		}
		
		$arr_json[$i]['id'] = "150000000";
		$arr_json[$i]['text'] = "KHUSUS TESTING";
		
		echo json_encode($arr_json);
	}
	
	
}
?>
