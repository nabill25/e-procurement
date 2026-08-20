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

class sap_pr_kontrak_json extends CI_Controller {

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
	
	function maintenance_po_number_frame_json() 
	{
		$this->load->library("kauth");  $userLogin = new kauth(); 
		$this->load->model("SAP/SapPrKontrak");
		$sap_pr_kontrak = new SapPrKontrak();
		
		/* LOGIN CHECK
		if ($userLogin->checkUserLogin()) 
		{ 
			$userLogin->retrieveUserInfo();
		}*/
		
		ini_set("memory_limit","500M");
		ini_set('max_execution_time', 520);
		
		
		$aColumns = array("PR_GROUP_NUMBER", "PR_NUMBER", "KONTRAK_KE", "PO_NUMBER");
		$aColumnsAlias = array("PR_GROUP_NUMBER", "PR_NUMBER", "KONTRAK_KE", "PO_NUMBER");
		
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
			if ( trim($sOrder) == "ORDER BY EVALUASI_NUMBER asc" )
			{
				/*
				* If there is no order by clause - ORDER BY INDEX COLUMN!!! DON'T DELETE IT!
				* If there is no order by clause there might be bugs in table display.
				* No order by clause means that the db is not responsible for the data ordering,
				* which means that the same row can be displayed in two pages - while
				* another row will not be displayed at all.
				*/
				$sOrder = " ORDER BY EVALUASI_NUMBER ASC";
				 
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
		
		
		$allRecord = $sap_pr_kontrak->getCountByParams(array());
		if($_GET['sSearch'] == "")
			$allRecordFilter = $allRecord;
		else	
			$allRecordFilter = $sap_pr_kontrak->getCountByParams(array(), " AND (UPPER(PR_GROUP_NUMBER) LIKE '%".strtoupper($_GET['sSearch'])."%' OR UPPER(PR_NUMBER) LIKE '%".strtoupper($_GET['sSearch'])."%' OR UPPER(PO_NUMBER) LIKE '%".strtoupper($_GET['sSearch'])."%')");
		
		$sap_pr_kontrak->selectByParams(array(), $dsplyRange, $dsplyStart, " AND (UPPER(PR_GROUP_NUMBER) LIKE '%".strtoupper($_GET['sSearch'])."%' OR UPPER(PR_NUMBER) LIKE '%".strtoupper($_GET['sSearch'])."%' OR UPPER(PO_NUMBER) LIKE '%".strtoupper($_GET['sSearch'])."%')", $sOrder);     		
		//echo $sap_pr_kontrak->query;
		/* Output */
		$output = array(
			"sEcho" => intval($_GET['sEcho']),
			"iTotalRecords" => $allRecord,
			"iTotalDisplayRecords" => $allRecordFilter,
			"aaData" => array()
		);
		
		while($sap_pr_kontrak->nextRow())
		{
			$row = array();
			for ( $i=0 ; $i<count($aColumns) ; $i++ )
			{
				if($aColumns[$i] == "TANGGAL")
					$row[] = getFormattedDate($sap_pr_kontrak->getField($aColumns[$i]));
				else if($aColumns[$i] == "KETERANNGAN")
					$row[] = truncate($sap_pr_kontrak->getField($aColumns[$i]), 5)."...";
				else if($aColumns[$i] == "JUMLAH")
					$row[] = currencyToPage($sap_pr_kontrak->getField($aColumns[$i]));
				else
					$row[] = $sap_pr_kontrak->getField($aColumns[$i]).$sOrder;
			}
			
			$output['aaData'][] = $row;
		}
		
		echo json_encode( $output );
	}
	
	function reset_po()
	{
		$reqPrGroup = $this->input->get("reqPrGroup");
		$reqKontrakKe = $this->input->get("reqKontrakKe");
		
		$this->load->model("SAP/SapPrKontrak");
		$sap_pr_kontrak = new SapPrKontrak();
		$sap_pr_kontrak->setField("FIELD", "PO_NUMBER");
		$sap_pr_kontrak->setField("FIELD_VALUE", "");
		$sap_pr_kontrak->setField("PR_GROUP_NUMBER", $reqPrGroup);
		$sap_pr_kontrak->setField("KONTRAK_KE", $reqKontrakKe);
		$sap_pr_kontrak->updateByField();
	}
	
	
}
?>
