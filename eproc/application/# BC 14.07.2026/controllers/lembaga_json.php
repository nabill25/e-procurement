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

class lembaga_json extends CI_Controller {

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
	
	function daftar_rekanan_pengalaman_progress_json() 
	{
		//$lembaga = new Lembaga();
		
		/* LOGIN CHECK 
		if ($userLogin->checkUserLogin()) 
		{ 
			$userLogin->retrieveUserInfo();
		}*/
		
		ini_set("memory_limit","500M");
		ini_set('max_execution_time', 520);
		
		$reqKeterangan = httpFilterRequest("reqKeterangan");
		$reqId = httpFilterRequest("reqId");
		$reqSearch = httpFilterGet("reqSearch");
		
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
		
				
		if($reqSearch == "")
		{	
			$allRecord = 1;
			//$lembaga->selectByParams(array(), $_GET['iDisplayLength'], $_GET['iDisplayStart'], $statement);
		}
		else
		{
			$reqSearch = str_replace('\\', '', $reqSearch);
			$allRecord = 1;
			//$lembaga->selectByParams(array(), $_GET['iDisplayLength'], $_GET['iDisplayStart'], $statement.$reqSearch." ");
		
		}
		
		$column = array('LEMBAGA_ID', 'NO', 'VISI','MISI');
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
			/*while($lembaga->nextRow())
			{
				$row = array();
				for ( $i=0 ; $i<count($column) ; $i++ )
				{
					if($column[$i]=='NO')		$row[] = $number;
					else						$row[] = $lembaga->getField(trim($column[$i]));
				}
				
				$output['aaData'][] = $row;
			}*/
			
			echo json_encode( $output );
	}
	
	function daftar_rekanan_pengalaman_selesai_json() 
	{
		
		//$lembaga = new Lembaga();
		
		/* LOGIN CHECK 
		if ($userLogin->checkUserLogin()) 
		{ 
			$userLogin->retrieveUserInfo();
		}*/
		
		ini_set("memory_limit","500M");
		ini_set('max_execution_time', 520);
		
		$reqKeterangan = httpFilterRequest("reqKeterangan");
		$reqId = httpFilterRequest("reqId");
		$reqSearch = httpFilterGet("reqSearch");
		
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
		
				
		if($reqSearch == "")
		{	
			$allRecord = 1;
			//$lembaga->selectByParams(array(), $_GET['iDisplayLength'], $_GET['iDisplayStart'], $statement);
		}
		else
		{
			$reqSearch = str_replace('\\', '', $reqSearch);
			$allRecord = 1;
			//$lembaga->selectByParams(array(), $_GET['iDisplayLength'], $_GET['iDisplayStart'], $statement.$reqSearch." ");
		
		}
		
		$column = array('LEMBAGA_ID', 'NO', 'VISI','MISI');
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
			/*while($lembaga->nextRow())
			{
				$row = array();
				for ( $i=0 ; $i<count($column) ; $i++ )
				{
					if($column[$i]=='NO')		$row[] = $number;
					else						$row[] = $lembaga->getField(trim($column[$i]));
				}
				
				$output['aaData'][] = $row;
			}*/
			
			echo json_encode( $output );
	}
	
	function error_sistem_kurang_json()
	{
		ini_set("memory_limit","500M");
		ini_set('max_execution_time', 520);
		
		$reqKeterangan = httpFilterRequest("reqKeterangan");
		$reqId = httpFilterRequest("reqId");
		$reqSearch = httpFilterGet("reqSearch");
		
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
		
				
		if($reqSearch == "")
		{	
			$allRecord = 0;
			//$lembaga->selectByParams(array(), $_GET['iDisplayLength'], $_GET['iDisplayStart'], $statement);
		}
		else
		{
			$reqSearch = str_replace('\\', '', $reqSearch);
			$allRecord = 0;
			//$lembaga->selectByParams(array(), $_GET['iDisplayLength'], $_GET['iDisplayStart'], $statement.$reqSearch." ");
		
		}
		
		$column = array('LEMBAGA_ID', 'NO', 'VISI','MISI','MISI');
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
			/*while($lembaga->nextRow())
			{
				$row = array();
				for ( $i=0 ; $i<count($column) ; $i++ )
				{
					if($column[$i]=='NO')		$row[] = $number;
					else						$row[] = $lembaga->getField(trim($column[$i]));
				}
				
				$output['aaData'][] = $row;
			}*/
			
			echo json_encode( $output );
	}
	
	function error_sistem_lebih_json()
	{
		//$this->load->library("kauth");  $userLogin = new kauth(); 
		//$this->load->model("Lembaga.php");
		//$lembaga = new Lembaga();
		
		/* LOGIN CHECK 
		if ($userLogin->checkUserLogin()) 
		{ 
			$userLogin->retrieveUserInfo();
		}*/
		
		ini_set("memory_limit","500M");
		ini_set('max_execution_time', 520);
		
		$reqKeterangan = httpFilterRequest("reqKeterangan");
		$reqId = httpFilterRequest("reqId");
		$reqSearch = httpFilterGet("reqSearch");
		
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
		
				
		if($reqSearch == "")
		{	
			$allRecord = 0;
			//$lembaga->selectByParams(array(), $_GET['iDisplayLength'], $_GET['iDisplayStart'], $statement);
		}
		else
		{
			$reqSearch = str_replace('\\', '', $reqSearch);
			$allRecord = 0;
			$lembaga->selectByParams(array(), $_GET['iDisplayLength'], $_GET['iDisplayStart'], $statement.$reqSearch." ");
		
		}
		
		$column = array('LEMBAGA_ID', 'NO', 'VISI','MISI','MISI');
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
			/*while($lembaga->nextRow())
			{
				$row = array();
				for ( $i=0 ; $i<count($column) ; $i++ )
				{
					if($column[$i]=='NO')		$row[] = $number;
					else						$row[] = $lembaga->getField(trim($column[$i]));
				}
				
				$output['aaData'][] = $row;
			}*/
			
			echo json_encode( $output );
	}
	
}
?>
