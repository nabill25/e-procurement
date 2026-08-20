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

class evaluasi_admin_json extends CI_Controller {

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
	
	function evaluasi_admin_frame_json() 
	{
		
		$this->load->library("kauth");  $userLogin = new kauth(); 
		$this->load->model("EvaluasiAdmin");
		$evaluasi_admin = new EvaluasiAdmin();
		
		/* LOGIN CHECK
		if ($userLogin->checkUserLogin()) 
		{ 
			$userLogin->retrieveUserInfo();
		}*/
		
		ini_set("memory_limit","500M");
		ini_set('max_execution_time', 520);
		
		
		$aColumns = array("EVALUASI_NUMBER", "NAMA", "TIPE");
		$aColumnsAlias = array("EVALUASI_NUMBER", "NAMA", "TIPE");
		
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
		
		
		$allRecord = $evaluasi_admin->getCountByParams(array());
		if($_GET['sSearch'] == "")
			$allRecordFilter = $allRecord;
		else	
			$allRecordFilter = $evaluasi_admin->getCountByParams(array(), " AND (UPPER(NAMA) LIKE '%".strtoupper($_GET['sSearch'])."%')");
		
		$evaluasi_admin->selectByParams(array(), $dsplyRange, $dsplyStart, " AND (UPPER(NAMA) LIKE '%".strtoupper($_GET['sSearch'])."%')", $sOrder);     		
		//echo $evaluasi_admin->query;
		/* Output */
		$output = array(
			"sEcho" => intval($_GET['sEcho']),
			"iTotalRecords" => $allRecord,
			"iTotalDisplayRecords" => $allRecordFilter,
			"aaData" => array()
		);
		
		while($evaluasi_admin->nextRow())
		{
			$row = array();
			for ( $i=0 ; $i<count($aColumns) ; $i++ )
			{
				if($aColumns[$i] == "TANGGAL")
					$row[] = getFormattedDate($evaluasi_admin->getField($aColumns[$i]));
				else if($aColumns[$i] == "KETERANNGAN")
					$row[] = truncate($evaluasi_admin->getField($aColumns[$i]), 5)."...";
				else if($aColumns[$i] == "JUMLAH")
					$row[] = currencyToPage($evaluasi_admin->getField($aColumns[$i]));
				else
					$row[] = $evaluasi_admin->getField($aColumns[$i]).$sOrder;
			}
			
			$output['aaData'][] = $row;
		}
		
		echo json_encode( $output );
	}
	
	function nomor_filter_json()
	{
		$this->load->library("kauth");  $userLogin = new kauth(); 
		$this->load->model("EvaluasiAdmin");
		/* LOGIN CHECK 
		if ($userLogin->checkUserLogin()) 
		{ 
			$userLogin->retrieveUserInfo();
		}*/
		
		$reqEvaluasiNumber = httpFilterGet("reqEvaluasiNumber");
		$reqOld= httpFilterGet("reqOld");
		$reqMode = httpFilterGet("reqMode");
		
		$reqBaru = httpFilterGet("reqBaru");
		
		if($reqMode == 'nomor'){
			$evaluasi_admin = new EvaluasiAdmin();
			if($reqBaru == 'baru')
				$evaluasi_admin->selectByParams(array('EVALUASI_NUMBER'=>$reqEvaluasiNumber));
			elseif($reqBaru == 'lama')
				$evaluasi_admin->selectByParams(array('EVALUASI_NUMBER'=>$reqEvaluasiNumber, "NOT EVALUASI_NUMBER " => $reqOld));
			//echo $evaluasi_admin->query;
			$evaluasi_admin->firstRow();
			if($reqEvaluasiNumber == $evaluasi_admin->getField("EVALUASI_NUMBER"))
				echo json_encode(false);
			else
				echo json_encode(true);
		}
	}
	
	function evaluasi_admin_frame_add()
	{
		/* INCLUDE FILE */
		$this->load->library("kauth");  $userLogin = new kauth(); 
		$this->load->model("EvaluasiAdmin");
		
		/* create objects */
		$evaluasi_admin = new EvaluasiAdmin();
		
		/* LOGIN CHECK */
		if ($userLogin->checkUserLogin()) 
		{ 
			$userLogin->retrieveUserInfo();
		}
		
		
		/* VARIABLE */
		$reqId	= httpFilterRequest("reqId");
		$reqEvaluasiNumber	= httpFilterPost("reqEvaluasiNumber");
		$reqNama	= httpFilterPost("reqNama");
		$reqTipe	= httpFilterPost("reqTipe");
		$reqSubmit	= httpFilterPost("reqSubmit");
		
		$tempEvaluasiNumber = $reqEvaluasiNumber;
		$tempNama = $reqNama;
		$tempTipe = $reqTipe;
		
		/* VALIDATION */
		$validate->setValidate(array(
								$reqEvaluasiNumber,
								$reqNama
										));
		$validate->setMessage(array(
								'Anda belum mengisi Nomor',
								'Anda belum mengisi Nama'
										));
		
		if($reqSubmit == "Simpan" || $reqSubmit == "Edit")	$validate->notEmpty();
				
		if($reqSubmit == "Simpan" || $reqSubmit == "Edit") $alertMsg .= $validate->getMessage();
		// trigger the validation
		/* ACTION BY REQMODE */
		if(($reqSubmit == "Simpan" || $reqSubmit == "Edit") && $validate->statNotEmpty){	
			$evaluasi_admin->setField("EVALUASI_NUMBER",$reqEvaluasiNumber);
			$evaluasi_admin->setField("KETERANGAN",$reqTipe);
			$evaluasi_admin->setField("NAMA",$reqNama);
			$evaluasi_admin->setField("TIPE",$reqTipe);
			
			if($reqId == "")
			{
				if($evaluasi_admin->insert())
				{
					$alertMsg .= "Data Tersimpan";
					echo '<script language="javascript">';
					echo "window.opener.location.href = '".base_url()."main/loadUrl/main/evaluasi_admin_frame/?reqStatus=simpan';";
					echo "window.close();";
					echo '</script>';
				}
			}
			else
			{
				if($evaluasi_admin->update())
				{
					$alertMsg .= "Data Tersimpan";
					echo '<script language="javascript">';
					echo "window.opener.location.href = '".base_url()."main/loadUrl/main/evaluasi_admin_frame/?reqStatus=simpan';";
					echo "window.close();";
					echo '</script>';
				}
			}	
		}
	}
	
	function combo() 
	{
		$this->load->model('EvaluasiAdmin');
		$evaluasi_admin = new EvaluasiAdmin();
		
		$evaluasi_admin->selectByParams();
		
		$i = 0;
		while($evaluasi_admin->nextRow())
		{
			$arr_json[$i]['id']		= $evaluasi_admin->getField("EVALUASI_NUMBER");
			$arr_json[$i]['text']	= $evaluasi_admin->getField("NAMA");
			$i++;
		}
		
		echo json_encode($arr_json);
	}
}
?>
