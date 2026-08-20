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

class group_json extends CI_Controller {

	function __construct() {
		parent::__construct(); $this->db->query("SET TIME ZONE 'Asia/Jakarta'"); 
		if (!$this->kauth->getInstance()->hasIdentity()) //kauth
		{ }       
		/* GLOBAL VARIABLE */
		$this->ID = $this->kauth->getInstance()->getIdentity()->REKANAN_ID;   
	}	
	
	function json() 
	{
		$this->load->model("Group");
		$group = new Group();
		
		$reqNama = $this->input->post("reqNama");
		$reqSearch = $this->input->post("reqSearch");
		
		$aColumns 			= array('USER_TYPE_ID','NAMA','AKTIF');
		$aColumnsAlias		= array('USER_TYPE_ID','NAMA','AKTIF');
		
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
			
			
			//Check if there is an order by clause
			if ( trim($sOrder) == "ORDER BY AKTIF desc" )
			{
				$sOrder = " ORDER BY AKTIF DESC";
				 
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
			$sWhere = " and (";
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
		
		$statement = " AND (UPPER(NAMA) LIKE '%".strtoupper($_GET['sSearch'])."%') OR (UPPER(AKTIF) LIKE '%".strtoupper($_GET['sSearch'])."%')";
		$allRecord = $group->getCountByParams(array(), $statement, $sOrder);
		if($_GET['sSearch'] == "")
			$allRecordFilter = $allRecord;
		else	
			$allRecordFilter =  $group->getCountByParams(array(), $statement, $sOrder);

		$group->selectByParams(array(), $dsplyRange, $dsplyStart, $statement, $sOrder);
		

		$output = array(
			"sEcho" => intval($_GET['sEcho']),
			"iTotalRecords" => $allRecord,
			"iTotalDisplayRecords" => $allRecordFilter,
			"aaData" => array()
		);
		
		while($group->nextRow())		
		{		
			$row = array();		
			for ( $i=0 ; $i<count($aColumns) ; $i++ )		
			{	
				if($aColumns[$i]=='NO'){
					$row[] = $number;
				}  elseif($aColumns[$i]=='AKTIF'){
						if( $group->getField(trim($aColumns[$i])) == '1') {
							$st = '<span class="badge badge-primary">Aktif</span>';					
						} else {
							$st = '<span class="badge badge-danger">Non Aktif</span>';					
						}															
						$row[] = $st;
				} else {
					$row[] = $group->getField(trim($aColumns[$i]));
				}	
			}
			$output['aaData'][] = $row;
		}
		echo json_encode( $output );	
	}
	
	function add() 
	{
		$this->load->model('Group');
		
		$group	= new Group();
		
		$reqId		= $this->input->post('reqId');
		$reqMode	= $this->input->post('reqMode');
		$reqNama		= $this->input->post('reqNama');
		$reqAktif		= $this->input->post('reqAktif'); 
		
		if($reqMode == "insert")
		{
			$group->setField("NAMA", $reqNama);
			$group->setField("AKTIF", $reqAktif);
			$group->setField("CREATED_BY", $this->USER_LOGIN_ID);
			$group->insert();
			echo "Data berhasil disimpan.";
		}
		else
		{ 
			$group->setField("NAMA", $reqNama);
			$group->setField("AKTIF", $reqAktif);
			$group->setField("USER_TYPE_ID", $reqId);
			$group->setField("CREATED_BY", $this->USER_LOGIN_ID);
			if($group->update()){
				echo "Data berhasil diubah.";
			} else {
				echo "Data gagal diubah, silahkan dicoba kembali.";
			}
		}
		
	}
	
	// function delete() 
	// {
	// 	$this->load->model('Group');
		
	// 	$group	= new Group();
		
	// 	$reqId		= $this->input->get('reqId');
		
	// 	$group	= new Group();
	// 	$group->setField("USER_TYPE_ID", $reqId);
	// 	$group->delete();
	// 	echo "Data berhasil dihapus.";
	// } 
	
}
?>
