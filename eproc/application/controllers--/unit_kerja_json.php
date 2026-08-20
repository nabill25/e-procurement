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

class unit_kerja_json extends CI_Controller {

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
	
	function json() 
	{
		$this->load->model("UnitKerja");
		$unit_kerja = new UnitKerja();
		
		$reqKeterangan = $this->input->post("reqKeterangan");
		$reqId = $this->input->get("reqId");
		$reqSearch = $this->input->post("reqSearch");
		$reqAgamaId = $this->input->post("reqAgamaId");
		
		$aColumns 			= array('UNIT_KERJA_ID', 'KODE', 'NAMA', 'ALAMAT', 'LOKASI', 'TELEPON', 'FAX', 'EMAIL');
		$aColumnsAlias		= array('UNIT_KERJA_ID', 'KODE', 'NAMA', 'ALAMAT', 'LOKASI', 'TELEPON', 'FAX', 'EMAIL');
		
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
			if ( trim($sOrder) == "ORDER BY UNIT_KERJA_ID desc" )
			{
				/*
				* If there is no order by clause - ORDER BY INDEX COLUMN!!! DON'T DELETE IT!
				* If there is no order by clause there might be bugs in table display.
				* No order by clause means that the db is not responsible for the data ordering,
				* which means that the same row can be displayed in two pages - while
				* another row will not be displayed at all.
				*/
				$sOrder = " ORDER BY UNIT_KERJA_ID ASC";
				 
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
		
		$statement = "AND (UPPER(NAMA) LIKE '%".strtoupper($_GET['sSearch'])."%')";
		$allRecord = $unit_kerja->getCountByParams(array(), $statement, $sOrder);
		if($_GET['sSearch'] == "")
			$allRecordFilter = $allRecord;
		else	
			$allRecordFilter =  $unit_kerja->getCountByParams(array(), $statement, $sOrder);

		$unit_kerja->selectByParams(array(), $dsplyRange, $dsplyStart, $statement, $sOrder);
		
		$output = array(
			"sEcho" => intval($_GET['sEcho']),
			"iTotalRecords" => $allRecord,
			"iTotalDisplayRecords" => $allRecordFilter,
			"aaData" => array()
		);
		
		while($unit_kerja->nextRow())		
		{		
			$row = array();		
			for ( $i=0 ; $i<count($aColumns) ; $i++ )		
			{	
				if($aColumns[$i]=='NO')		$row[] = $number;
					elseif($aColumns[$i]=='TANGGAL' || $aColumns[$i]=='TANGGAL_MULAI' || $aColumns[$i]=='TANGGAL_AKHIR')	$row[] = getFormattedDateJson($unit_kerja->getField(trim($aColumns[$i])));
					elseif($aColumns[$i]=='STATUS'){
						if( $unit_kerja->getField(trim($aColumns[$i])) == 1)	$st = 'Berlaku';					
						else												$st = 'Tidak Berlaku';				
						$row[] = $st;
					}
					elseif($aColumns[$i]=='UNIT_KERJA')	$row[] = $unit_kerja->getField(trim($aColumns[$i]))."*".$unit_kerja->getField("SK_PANITIA_ID");
					else	$row[] = $unit_kerja->getField(trim($aColumns[$i]));
			}
			$output['aaData'][] = $row;
		}
		echo json_encode( $output );	
	}	
	
	function unit_kerja_pic_json() 
	{
		$this->load->model("UnitKerja");
		$unit_kerja = new UnitKerja();
		
		$reqKeterangan = $this->input->post("reqKeterangan");
		$reqId = $this->input->post("reqId");
		$reqSearch = $this->input->post("reqSearch");
		
		$aColumns 			= array('UNIT_KERJA_ID', 'UNIT_KERJA', 'JUMLAH');
		$aColumnsAlias		= array('UNIT_KERJA_ID', 'UNIT_KERJA', 'JUMLAH');
		
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
			if ( trim($sOrder) == "ORDER BY unit_kerja_ELIMINASI desc" )
			{
				/*
				* If there is no order by clause - ORDER BY INDEX COLUMN!!! DON'T DELETE IT!
				* If there is no order by clause there might be bugs in table display.
				* No order by clause means that the db is not responsible for the data ordering,
				* which means that the same row can be displayed in two pages - while
				* another row will not be displayed at all.
				*/
				$sOrder = " ORDER BY A.unit_kerja asc";
				 
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
		
		$statement = "AND (UPPER(A.NAMA) LIKE '%".strtoupper($_GET['sSearch'])."%')";
		$allRecord = $unit_kerja->getCountByParams(array(), $statement);
		if($_GET['sSearch'] == "")
			$allRecordFilter = $allRecord;
		else	
			$allRecordFilter =  $unit_kerja->getCountByParams(array(), $statement);

		$unit_kerja->selectByParamsMonitoringPic(array(), $dsplyRange, $dsplyStart, $statement, $sOrder);
		
		$output = array(
			"sEcho" => intval($_GET['sEcho']),
			"iTotalRecords" => $allRecord,
			"iTotalDisplayRecords" => $allRecordFilter,
			"aaData" => array()
		);
		
		while($unit_kerja->nextRow())		
		{		
			$row = array();		
			for ( $i=0 ; $i<count($aColumns) ; $i++ )		
			{	
				if($aColumns[$i]=='NO')	$row[] = $number;
					else				$row[] = $unit_kerja->getField(trim($aColumns[$i]));
			}
			
			$output['aaData'][] = $row;
		}
		echo json_encode( $output );	
	}
	
	function add() 
	{
		$this->load->model('UnitKerja');
		
		$unit_kerja	= new UnitKerja();
		
		$reqId		= $this->input->post('reqId');
		$reqMode	= $this->input->post('reqMode');
		$reqKode	= $this->input->post('reqKode');
		$reqNama	= $this->input->post('reqNama');
		$reqAlamat	= $this->input->post('reqAlamat');
		$reqLokasi	= $this->input->post('reqLokasi');
		$reqTelepon	= $this->input->post('reqTelepon');
		$reqFax	= $this->input->post('reqFax');
		$reqEmail	= $this->input->post('reqEmail');
		
		if($reqMode == "insert")
		{
			$unit_kerja	= new UnitKerja();
			$unit_kerja->setField("UNIT_KERJA_ID", $reqId);
			$unit_kerja->setField("KODE",$reqKode);
			$unit_kerja->setField("NAMA",$reqNama);
			$unit_kerja->setField("ALAMAT",$reqAlamat);
			$unit_kerja->setField("LOKASI", $reqLokasi);
			$unit_kerja->setField("TELEPON",$reqTelepon );
			$unit_kerja->setField("FAX",$reqFax );
			$unit_kerja->setField("EMAIL",$reqEmail );
			$unit_kerja->insert();
			
		}
		else
		{
			$unit_kerja	= new UnitKerja();
			$unit_kerja->setField("UNIT_KERJA_ID", $reqId);
			$unit_kerja->setField("KODE",$reqKode);
			$unit_kerja->setField("NAMA",$reqNama);
			$unit_kerja->setField("ALAMAT",$reqAlamat);
			$unit_kerja->setField("LOKASI", $reqLokasi);
			$unit_kerja->setField("TELEPON",$reqTelepon );
			$unit_kerja->setField("FAX",$reqFax );
			$unit_kerja->setField("EMAIL",$reqEmail );
			$unit_kerja->update();
		}
		
		echo "Data berhasil disimpan.";
	}
	
	function delete() 
	{
		$this->load->model('UnitKerja');
		
		$unit_kerja	= new UnitKerja();
		
		$reqId		= $this->input->get('reqId');
		$reqNama		= $this->input->post('reqNama');
		
		$unit_kerja	= new UnitKerja();
		$unit_kerja->setField("UNIT_KERJA_ID", $reqId);
		$unit_kerja->delete();
		
		echo "Data berhasil disimpan.";
	}
	
	function combo() 
	{
		$this->load->model('UnitKerja');
		$unit_kerja = new UnitKerja();
		
		$unit_kerja->selectByParams();
		
		$i = 0;
		while($unit_kerja->nextRow())
		{
			$arr_json[$i]['id']		= $unit_kerja->getField("UNIT_KERJA_ID");
			$arr_json[$i]['text']	= $unit_kerja->getField("NAMA");
			$i++;
		}
		
		echo json_encode($arr_json);
	}	
	
	
}
?>
