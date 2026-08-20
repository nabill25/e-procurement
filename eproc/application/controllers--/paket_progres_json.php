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

class paket_progres_json extends CI_Controller {

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
	
	function pembagian_pic_add() 
	{
		$this->load->model("PaketProgres");
		$this->load->library("kauth");  $userLogin = new kauth(); 
		
		$reqId = httpFilterPost("reqId");
		$reqMode = httpFilterPost("reqMode");
		
		$reqPegawaiId= $_POST["reqPegawaiId"];
		$reqNamaId= $_POST["reqNamaId"];
		$reqNama= $_POST["reqNama"];
		$reqKeterangan= $_POST["reqKeterangan"];
		$reqUrut= $_POST["reqUrut"];
		$reqTanggalAwalRen= $_POST["reqTanggalAwalRen"];
		$reqTanggalAkhirRen= $_POST["reqTanggalAkhirRen"];
		$reqArrayIndex= $_POST["reqArrayIndex"];
		$set_loop= $reqArrayIndex;
							
		if($reqMode == "insert")
		{
			$set= new PaketProgres();
			$set->setField("PAKET_ID", $reqId);
			$set->delete();
			unset($set);
			
			for($i=0;$i<=$set_loop;$i++)
			{
				if($reqPegawaiId[$i] == "")
				{}
				else
				{
				$index = $i;
				$set= new PaketProgres();
				
				$set->setField("TANGGAL_AWAL_REN", dateToDBCheck($reqTanggalAwalRen[$index]));
				$set->setField("TANGGAL_AKHIR_REN", dateToDBCheck($reqTanggalAkhirRen[$index]));
				$set->setField("NAMA", $reqNama[$index]);
				$set->setField("PAKET_PROGRES_TEMPLATE_ID", $reqNamaId[$index]);
				$set->setField("USER_LOGIN_ID", ValToNullDB($reqPegawaiId[$index]));
				$set->setField("KETERANGAN", $reqKeterangan[$index]);
				$set->setField("PAKET_ID", ValToNullDB($reqId));
				$set->setField("URUT", $reqUrut[$index]);
				$set->setField("RECRUITMENT_PROGRES_ID", $reqRowId[$i]);
				$set->setField("LAST_CREATE_USER", $userLogin->idUser);
				$set->setField("LAST_CREATE_DATE", "CURRENT_DATE");
				if($set->insert()){}
				//echo $set->query;
				unset($set);
				}
			}
			echo "Data berhasil disimpan.";
		}
	}
	
	function progres_pelaksanaan_monitoring_json() 
	{
		$this->load->library("kauth");  $userLogin = new kauth(); 
		$this->load->model("PaketProgres");
		$paket_progres_work_order = new PaketProgres();
		
		/* LOGIN CHECK 
		if ($userLogin->checkUserLogin()) 
		{ 
			$userLogin->retrieveUserInfo();
		}*/
		
		ini_set("memory_limit","500M");
		ini_set('max_execution_time', 5200);
		
		$reqPaketId= httpFilterGet("reqPaketId");
		$reqStatus= httpFilterGet("reqStatus");
		
		$aColumns = array("PAKET", "PAKET_PROGRES_ID", "NAMA", "KETERANGAN", "USER_LOGIN", "TANGGAL_AWAL_REN", "TANGGAL_AKHIR_REN", "STATUS_INFO", "STATUS_ENTRI", "STATUS", "USER_LOGIN_ID", "PAKET_PROGRES_ID");
		$aColumnsAlias = array("PAKET", "PAKET_PROGRES_ID", "A.NAMA", "KETERANGAN", "B.USER_NAMA", "TANGGAL_AWAL_REN", "TANGGAL_AKHIR_REN", "STATUS_INFO", "STATUS_ENTRI", "STATUS", "USER_LOGIN_ID", "PAKET_PROGRES_ID");
		
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
			if ( trim($sOrder) == "" )
			{
				/*
				* If there is no order by clause - ORDER BY INDEX COLUMN!!! DON'T DELETE IT!
				* If there is no order by clause there might be bugs in table display.
				* No order by clause means that the db is not responsible for the data ordering,
				* which means that the same row can be displayed in two pages - while
				* another row will not be displayed at all.
				*/
				$sOrder = " ORDER BY A.PAKET_ID ASC, A.NO_URUT ASC";
				 
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
		
		$searchJson= " AND (UPPER(A.NAMA) LIKE '%".strtoupper($_GET['sSearch'])."%' OR UPPER(A.NAMA) LIKE '%".strtoupper($_GET['sSearch'])."%')";
		
		//$statement= " AND A.USER_LOGIN_ID = ".$userLogin->pegawaiId;
		
		if($reqStatus==""){}
		elseif($reqStatus=="1")
		{
			$statement.= " AND COALESCE(STATUS,0) = 0";
		}
		elseif($reqStatus=="2")
		{
			$statement.= " AND COALESCE(STATUS,0) > 0";
		}
		
		if($reqPaketId == ""){}
		else
			$statement.= " AND A.PAKET_ID = ".$reqPaketId;
		
		
		$statement .= " AND D.UNIT_KERJA_ID = '".$userLogin->unitKerjaId."' ";
		$statement .= " AND EXISTS(SELECT 1 FROM PAKET_PROGRES X INNER JOIN PAKET Y ON X.PAKET_ID = Y.PAKET_ID AND X.USER_LOGIN_ID = '".$userLogin->UID."' WHERE Y.PAKET_ID = D.PAKET_ID) ";
			
		$allRecord = $paket_progres_work_order->getCountByParamsWorkOrder(array() ,$userLogin->UID, $statement);
		if($_GET['sSearch'] == "")
			$allRecordFilter = $allRecord;
		else	
			$allRecordFilter = $paket_progres_work_order->getCountByParamsWorkOrder(array() ,$userLogin->UID, $statement.$searchJson);
		
		$paket_progres_work_order->selectByParamsWorkOrder(array(), $dsplyRange, $dsplyStart ,$userLogin->UID,$statement.$searchJson);
		//echo $paket_progres_work_order->query;
		/* Output */
		$output = array(
			"sEcho" => intval($_GET['sEcho']),
			"iTotalRecords" => $allRecord,
			"iTotalDisplayRecords" => $allRecordFilter,
			"aaData" => array()
		);
		
		while($paket_progres_work_order->nextRow())
		{
			$row = array();
			for ( $i=0 ; $i<count($aColumns) ; $i++ )
			{
				if($aColumns[$i] == "TANGGAL_AWAL_REN" || $aColumns[$i] == "TANGGAL_AKHIR_REN")
					$row[] = getFormattedDate($paket_progres_work_order->getField($aColumns[$i]));
				else if($aColumns[$i] == "KETERANGAN")
					$row[] = truncate($paket_progres_work_order->getField($aColumns[$i]), 5)."...";
				else
					$row[] = $paket_progres_work_order->getField($aColumns[$i]);
			}
			
			$output['aaData'][] = $row;
		}
		
		echo json_encode( $output );
	}
	
	function delete_pembagian_pic_add()
	{
			$this->load->model("PaketProgres");
			$set= new PaketProgres();
			$set->setField('PAKET_PROGRES_ID', $reqId);
			//echo $reqId
			if($set->deleteDetil())
				$alertMsg .= "Data berhasil dihapus";
			else
				$alertMsg .= "Error ".$set->getErrorMsg();
			//echo $set->query;
			//echo "asd";
	}
	
}
?>
