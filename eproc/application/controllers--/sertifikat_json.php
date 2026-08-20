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

class sertifikat_json extends CI_Controller {

	function __construct() {
		parent::__construct(); $this->db->query("SET TIME ZONE 'Asia/Jakarta'"); 
		
		//kauth
		if (!$this->kauth->getInstance()->hasIdentity())
		{
		}       
		
		/* GLOBAL VARIABLE */
		$this->ID = $this->kauth->getInstance()->getIdentity()->REKANAN_ID;   
		$this->USER_LOGIN_ID =  $this->kauth->getInstance()->getIdentity()->USER_LOGIN_ID;
	}	
	
	function json() 
	{
		$this->load->model("Sertifikat");
		$sertifikat = new Sertifikat();
		
		$reqSearch = $this->input->post("reqSearch"); 
		
		$aColumns 			= array('REKANAN_SERTIFIKAT_JENIS_ID', 'NAMA', 'ALIAS', 'NAMA');
		$aColumnsAlias 		= array('REKANAN_SERTIFIKAT_JENIS_ID', 'NAMA', 'ALIAS', 'NAMA');
		
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
					$sOrder .= $aColumnsAlias[ intval( $_GET['iSortCol_'.$i] ) ];
					if (substr_compare(( $_GET['sSortDir_'.$i] ), "asc", 0) == 0)
					{
						$sOrder .=" asc, ";
					} else
					{
						$sOrder .=" desc, ";
					}
				}
			}
			$sOrder = substr_replace( $sOrder, "", -2 );
			if ( trim($sOrder) == "ORDER BY REKANAN_SERTIFIKAT_JENIS_ID desc" )
			{
				$sOrder = " ORDER BY NAMA ASC ";
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
				 
				$sWhere .= $aColumnsAlias[$i]." LIKE '%' || :whereSpecificParam".$sWhereSpecificArrayCount." || '%' ";
				$sWhereSpecificArrayCount++;
				 
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
		
		$statement = "AND (UPPER(NAMA) LIKE '%".strtoupper($_GET['sSearch'])."%')";
		$allRecord = $sertifikat->getCountByParams(array(), $statement, $sOrder);
		if($_GET['sSearch'] == "")
			$allRecordFilter = $allRecord;
		else	
			$allRecordFilter =  $sertifikat->getCountByParams(array(), $statement, $sOrder);

		$sertifikat->selectByParams(array(), $dsplyRange, $dsplyStart, $statement, $sOrder);
		
		$output = array(
			"sEcho" => intval($_GET['sEcho']),
			"iTotalRecords" => $allRecord,
			"iTotalDisplayRecords" => $allRecordFilter,
			"aaData" => array()
		);
		
		while($sertifikat->nextRow())		
		{		
			$row = array();		
			for ( $i=0 ; $i<count($aColumns) ; $i++ )		
			{
				$row[] = $sertifikat->getField(trim($aColumns[$i]));
			}
			$output['aaData'][] = $row;
		}
		echo json_encode( $output );	
	}
	
	function add() 
	{
		$this->load->model('Sertifikat');
		
		$sertifikat	= new Sertifikat();
		
		$reqId		= $this->input->post('reqId');
		$reqMode	= $this->input->post('reqMode');
		
		$reqNama	= $this->input->post('reqNama');
		$reqAlias	= $this->input->post('reqAlias');
		
		if($reqMode == "insert")
		{
			$sertifikat	= new Sertifikat();
			$sertifikat->setField("NAMA", $reqNama);
			$sertifikat->setField("ALIAS", $reqAlias);
			$sertifikat->setField("CREATED_BY", $this->USER_LOGIN_ID);
			$sertifikat->insert();
			
		}
		else
		{
			$sertifikat	= new Sertifikat();
			$sertifikat->setField("REKANAN_SERTIFIKAT_JENIS_ID", $reqId);
			$sertifikat->setField("NAMA", $reqNama);
			$sertifikat->setField("ALIAS", $reqAlias);
			$sertifikat->setField("CREATED_BY", $this->USER_LOGIN_ID);
			$sertifikat->update();
		}
		
		echo "Data berhasil disimpan.";
	}
	
	function delete() 
	{
		$this->load->model('Sertifikat');
		$sertifikat	= new sertifikat();
		
		$reqId		= $this->input->get('reqId'); 
		
		$sertifikat->setField("REKANAN_SERTIFIKAT_JENIS_ID", $reqId);
		$sertifikat->setField("NAMA", $reqNama);
		$sertifikat->delete();
		
		echo "Data berhasil disimpan.";
	}
	
	function combo() 
	{
		$this->load->model('Sertifikat');
		$sertifikat	= new sertifikat();
		
		$sertifikat->selectByParams();
		
		$i = 0;
		while($sertifikat->nextRow())
		{
			$arr_json[$i]['id']		= $sertifikat->getField("REKANAN_SERTIFIKAT_JENIS_ID");
			$arr_json[$i]['text']	= $sertifikat->getField("NAMA");
			$i++;
		}
		
		echo json_encode($arr_json);
	}
	
}
?>
