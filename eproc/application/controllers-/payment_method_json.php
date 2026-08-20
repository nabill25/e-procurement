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

class payment_method_json extends CI_Controller {

	function __construct() {
		parent::__construct(); $this->db->query("SET TIME ZONE 'Asia/Jakarta'"); 
		
		if (!$this->kauth->getInstance()->hasIdentity())
		{
		}       
		
		$this->ID = $this->kauth->getInstance()->getIdentity()->REKANAN_ID;   
	}	
	
	function json() 
	{
		$this->load->model("PaymentMethod");
		$payment_method = new PaymentMethod();
		
		$reqKeterangan = $this->input->post("reqKeterangan");
		$reqId = $this->input->get("reqId");
		$reqSearch = $this->input->post("reqSearch");
		$reqAgamaId = $this->input->post("reqAgamaId");
		
		$aColumns 			= array('PAYMENT_METHOD_ID', 'NAMA');
		$aColumnsAlias		= array('PAYMENT_METHOD_ID', 'NAMA');
		
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
			
			if ( trim($sOrder) == "ORDER BY PAYMENT_METHOD_ID desc" )
			{
				$sOrder = " ORDER BY PAYMENT_METHOD_ID ASC";
				 
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
		
		$statement = " AND (UPPER(NAMA) LIKE '%".strtoupper($_GET['sSearch'])."%')";
		$allRecord = $payment_method->getCountByParams(array(), $statement, $sOrder);
		if($_GET['sSearch'] == "")
			$allRecordFilter = $allRecord;
		else	
			$allRecordFilter =  $payment_method->getCountByParams(array(), $statement, $sOrder);

		$payment_method->selectByParams(array(), $dsplyRange, $dsplyStart, $statement, $sOrder);
		
		$output = array(
			"sEcho" => intval($_GET['sEcho']),
			"iTotalRecords" => $allRecord,
			"iTotalDisplayRecords" => $allRecordFilter,
			"aaData" => array()
		);
		
		while($payment_method->nextRow())		
		{		
			$row = array();		
			for ( $i=0 ; $i<count($aColumns) ; $i++ )		
			{	
				if($aColumns[$i]=='NO')		$row[] = $number;
					elseif($aColumns[$i]=='TANGGAL' || $aColumns[$i]=='TANGGAL_MULAI' || $aColumns[$i]=='TANGGAL_AKHIR')	$row[] = getFormattedDateJson($payment_method->getField(trim($aColumns[$i])));
					elseif($aColumns[$i]=='STATUS'){
						if( $payment_method->getField(trim($aColumns[$i])) == 1)	$st = 'Berlaku';					
						else												$st = 'Tidak Berlaku';				
						$row[] = $st;
					}
					elseif($aColumns[$i]=='UNIT_KERJA')	$row[] = $payment_method->getField(trim($aColumns[$i]))."*".$payment_method->getField("SK_PANITIA_ID");
					else	$row[] = $payment_method->getField(trim($aColumns[$i]));
			}
			$output['aaData'][] = $row;
		}
		echo json_encode( $output );	
	}
	
	function add() 
	{
		$this->load->model('PaymentMethod');
		$payment_method	= new PaymentMethod();
		$reqId		= $this->input->post('reqId');
		$reqMode	= $this->input->post('reqMode');
		$reqNama		= $this->input->post('reqNama');
		
		if($reqMode == "insert")
		{
			$payment_method	= new PaymentMethod();
			$payment_method->setField("PAYMENT_METHOD_ID", $reqId);
			$payment_method->setField("NAMA", $reqNama);
			$payment_method->insert();
			
		}
		else
		{
			$payment_method	= new PaymentMethod();
			$payment_method->setField("PAYMENT_METHOD_ID", $reqId);
			$payment_method->setField("NAMA", $reqNama);
			$payment_method->update();
		}
		
		echo "Data berhasil disimpan.";
	}
	
	function delete() 
	{
		$this->load->model('PaymentMethod');
		$payment_method	= new PaymentMethod();
		$reqId		= $this->input->get('reqId');
		$reqNama		= $this->input->post('reqNama');
		$payment_method	= new PaymentMethod();
		$payment_method->setField("PAYMENT_METHOD_ID", $reqId);
		$payment_method->setField("NAMA", $reqNama);
		$payment_method->delete();
		echo "Data berhasil disimpan.";
	}
	
	function combo() 
	{
		$this->load->model('PaymentMethod');
		$payment_method = new PaymentMethod();
		$payment_method->selectByParams();
		
		$i = 0;
		while($payment_method->nextRow())
		{
			$arr_json[$i]['id']		= $payment_method->getField("PAYMENT_METHOD_ID");
			$arr_json[$i]['text']	= $payment_method->getField("NAMA");
			$i++;
		}
		
		echo json_encode($arr_json);
	}
	
}
?>
