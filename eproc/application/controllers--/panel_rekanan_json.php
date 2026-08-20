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

class panel_rekanan_json extends CI_Controller {

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
	
	function panel_evaluasi_monitoring_json() 
	{
		$this->load->library("kauth");  $userLogin = new kauth(); 
		$this->load->model("PanelRekanan");
		
		$panel_rekanan = new PanelRekanan();
		
		/* LOGIN CHECK */
		if ($userLogin->checkUserLogin()) 
		{ 
			$userLogin->retrieveUserInfo();
		}
		
		ini_set("memory_limit","500M");
		ini_set('max_execution_time', 5200);
		
		$reqMode=  $this->input->get("reqMode");
		$reqBidangUsaha =  $this->input->get("reqBidangUsaha");
		$reqId =  $this->input->get("reqId");
		
		$aColumns = array("REKANAN_ID", "PANEL_REKANAN_ID", "PERINGKAT", "FULL_NAMA_REKANAN", "TANGGAL_DAFTAR", "JUMLAH_PENGALAMAN", "NILAI_PENGALAMAN", "RATA_PENGALAMAN", "JUMLAH_REK_KORAN", "RATA_REK_KORAN", "NILAI_KEUANGAN", "NILAI_PANEL");
		$aColumnsAlias = array("A.REKANAN_ID", "A.PANEL_REKANAN_ID", "P.PERINGKAT", "B.NAMA", "A.TANGGAL_DAFTAR", "D.JUMLAH", "D.TOTAL", "RATA_PENGALAMAN", "F.TOTAL", "(CASE WHEN COALESCE(G.TOTAL, 0) = 0 THEN 0 ELSE (COALESCE(F.TOTAL, 0) / G.TOTAL) * 100 END)", "NILAI_KEUANGAN", "NILAI_PANEL");
		
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
		
		if($reqMode == "proses")
		{
			$panel_rekanan->setField("PANEL_ID", $reqId);
			$panel_rekanan->setField("PANEL_BIDANG_USAHA_ID", "");
			$panel_rekanan->callProsesPeringkatPanel();
		}
		
		if($reqMode == "proses_terpilih")
		{
			$panel_rekanan_proses = new PanelRekanan();
			$panel_rekanan_proses->setField("PANEL_ID", $reqId);
			$panel_rekanan_proses->setField("PANEL_BIDANG_USAHA_ID", $reqBidangUsaha);
			$panel_rekanan_proses->callProsesPeringkatPanel();
			
		}
		
		$searchJson= " AND (UPPER(A.KODE_REKANAN) LIKE '%".strtoupper($_GET['sSearch'])."%' OR UPPER(B.NAMA) LIKE '%".strtoupper($_GET['sSearch'])."%') ";
		
		if($reqBidangUsaha == "")
			$statement .= " AND A.PANEL_REKANAN_ID = 0 ";	
		else
		{
			$statement .= " AND P.PANEL_BIDANG_USAHA_ID = '".$reqBidangUsaha."' ";	
		}
		
		$allRecord = $panel_rekanan->getCountByParamsMonitoring(array(),$statement);
		if($_GET['sSearch'] == "")
			$allRecordFilter = $allRecord;
		else	
			$allRecordFilter = $panel_rekanan->getCountByParamsMonitoring(array(), $statement.$searchJson);
		
		$panel_rekanan->selectByParamsMonitoring(array(), $dsplyRange, $dsplyStart, $statement.$searchJson);
		//echo $panel_rekanan->query;
		/* Output */
		$output = array(
			"sEcho" => intval($_GET['sEcho']),
			"iTotalRecords" => $allRecord,
			"iTotalDisplayRecords" => $allRecordFilter,
			"aaData" => array()
		);
		while($panel_rekanan->nextRow())
		{
			$row = array();
			for ( $i=0 ; $i<count($aColumns) ; $i++ )
			{
				if($aColumns[$i] == "TANGGAL_DAFTAR")
					$row[] = getFormattedDate($panel_rekanan->getField($aColumns[$i]));
				else if($aColumns[$i] == "JUMLAH_REK_KORAN" || $aColumns[$i] == "NILAI_PENGALAMAN")
					$row[] = numberToIna($panel_rekanan->getField($aColumns[$i]));
				else if($aColumns[$i] == "NAMA")
					$row[] = strtoupper($panel_rekanan->getField($aColumns[$i]));
				else
					$row[] = $panel_rekanan->getField($aColumns[$i]);
			}
			
			$output['aaData'][] = $row;
		}
		
		echo json_encode( $output );
	}
	
	function panel_rekanan_peringkat_data_json()
	{
		$this->load->library("kauth");  $userLogin = new kauth(); 
		$this->load->model("PanelRekanan");
		$panel_rekanan = new PanelRekanan();
		
		$reqId =  $this->input->get("reqId");
		$reqBidangUsaha =  $this->input->get("reqBidangUsaha");
		
		$panel_rekanan->selectByParamsMonitoring(array("A.PANEL_ID" => $reqId, "MD5(P.PANEL_BIDANG_USAHA_ID)" => $reqBidangUsaha), -1, -1, " AND P.PERINGKAT IS NOT NULL ");
		$i=0;
		while($panel_rekanan->nextRow())
		{
				
			$met[$i]['PANEL_REKANAN_ID'] = $panel_rekanan->getField("PANEL_REKANAN_ID");
			$met[$i]['NILAI_PANEL'] = $panel_rekanan->getField("NILAI_PANEL");
			$met[$i]['PERINGKAT'] = $panel_rekanan->getField("PERINGKAT");
			$i++;
		}
		echo json_encode($met);	
	}



}
?>
