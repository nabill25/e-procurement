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

class metode_json extends CI_Controller {

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
	
	function get_bulan_rekening_koran() 
	{
		$this->load->library("kauth");  $userLogin = new kauth(); 
		$this->load->model("Metode");
		$metode = new Metode();
		
		$reqId = httpFilterGet("reqId");
		
		/* LOGIN CHECK */
		if ($userLogin->checkUserLogin()) 
		{ 
			$userLogin->retrieveUserInfo();
		}
		
		$arrBulanTahun = explode("-", $reqId);
		$reqBulan = $arrBulanTahun[0];
		$year = $year1 = $year2 = $arrBulanTahun[1];
		
		$month = $reqBulan;
		if($month <= 0) 
		{
			$year = date("Y") - 1;
			$month = 12 + $month;
			$monthname = getNameMonth($month);
		}
		else
			$monthname = getNameMonth($month);
		$month1 = $reqBulan - 1;
		if($month1 <= 0) 
		{
			$year1 = date("Y") - 1;
			$month1 = 12 + $month1;
			$monthname1 = getNameMonth($month1);
		}	
		else
			$monthname1 = getNameMonth($month1);
		$month2 = $reqBulan - 2;
		if($month2 <= 0) 
		{
			$year2 = date("Y") - 1;
			$month2 = 12 + $month2;
			$monthname2 = getNameMonth($month2);
		}									
		else
			$monthname2 = getNameMonth($month2);
				
		
		$met[0]['REKENING_KORAN'] = $monthname2." ".$year2.", ".$monthname1." ".$year1.", ".$monthname." ".$year;
		
		echo json_encode($met);
	}
	
	function get_bulan_rekening_koran_panel() 
	{
		$this->load->library("kauth");  $userLogin = new kauth(); 
		$this->load->model("Metode");
		$metode = new Metode();
		
		$reqId = httpFilterGet("reqId");
		
		/* LOGIN CHECK */
		if ($userLogin->checkUserLogin()) 
		{ 
			$userLogin->retrieveUserInfo();
		}
		
		$arrBulanTahun = explode("-", $reqId);
		$reqBulan = $arrBulanTahun[0];
		$year = $year1 = $year2 = $year3 = $year4 = $year5 = $arrBulanTahun[1];
		
		$month = $reqBulan;
		if($month <= 0) 
		{
			$year = date("Y") - 1;
			$month = 12 + $month;
			$monthname = getNameMonth($month);
		}
		else
			$monthname = getNameMonth($month);
		$month1 = $reqBulan - 1;
		if($month1 <= 0) 
		{
			$year1 = date("Y") - 1;
			$month1 = 12 + $month1;
			$monthname1 = getNameMonth($month1);
		}	
		else
			$monthname1 = getNameMonth($month1);
		$month2 = $reqBulan - 2;
		if($month2 <= 0) 
		{
			$year2 = date("Y") - 1;
			$month2 = 12 + $month2;
			$monthname2 = getNameMonth($month2);
		}									
		else
			$monthname2 = getNameMonth($month2);
		
		
		$month3 = $reqBulan - 3;
		if($month3 <= 0) 
		{
			$year3 = date("Y") - 1;
			$month3 = 12 + $month3;
			$monthname3 = getNameMonth($month3);
		}									
		else
			$monthname3 = getNameMonth($month3);
		
		$month4 = $reqBulan - 4;
		if($month4 <= 0) 
		{
			$year4 = date("Y") - 1;
			$month4 = 12 + $month4;
			$monthname4 = getNameMonth($month4);
		}									
		else
			$monthname4 = getNameMonth($month4);
		
		$month5 = $reqBulan - 5;
		if($month5 <= 0) 
		{
			$year5 = date("Y") - 1;
			$month5 = 12 + $month5;
			$monthname5 = getNameMonth($month5);
		}									
		else
			$monthname5 = getNameMonth($month5);
				
		$met = array("REKENING_KORAN" => $monthname5." ".$year5.", ".$monthname4." ".$year4.", ".$monthname3." ".$year3.", ".$monthname2." ".$year2.", ".$monthname1." ".$year1.", ".$monthname." ".$year,
					 "REKENING_KORAN_SET_VALID" => $month5.$year5.", ".$month4.$year4.", ".$month3.$year3.", ".$month2.$year2.", ".$month1.$year1.", ".$month.$year);
		
		echo json_encode($met);
	}
	
	function get_bulan_rekening_koran_set_validasi()
	{
		$this->load->library("kauth");  $userLogin = new kauth(); 
		$this->load->model("Metode");
		$metode = new Metode();
		
		$reqId = httpFilterGet("reqId");
		
		/* LOGIN CHECK */
		if ($userLogin->checkUserLogin()) 
		{ 
			$userLogin->retrieveUserInfo();
		}
		
		$arrBulanTahun = explode("-", $reqId);
		$reqBulan = $arrBulanTahun[0];
		$year = $year1 = $year2 = $arrBulanTahun[1];
		
		$month = $reqBulan;
		if($month <= 0) 
		{
			$year = date("Y") - 1;
			$month = 12 + $month;
			$monthname = getNameMonth($month);
		}
		else
			$monthname = getNameMonth($month);
		$month1 = $reqBulan - 1;
		if($month1 <= 0) 
		{
			$year1 = date("Y") - 1;
			$month1 = 12 + $month1;
			$monthname1 = getNameMonth($month1);
		}	
		else
			$monthname1 = getNameMonth($month1);
		$month2 = $reqBulan - 2;
		if($month2 <= 0) 
		{
			$year2 = date("Y") - 1;
			$month2 = 12 + $month2;
			$monthname2 = getNameMonth($month2);
		}									
		else
			$monthname2 = getNameMonth($month2);
				
		$met = array("REKENING_KORAN" => $monthname2." ".$year2.", ".$monthname1." ".$year1.", ".$monthname." ".$year,"REKENING_KORAN_SET_VALID" => $month2.$year2.", ".$month1.$year1.", ".$month.$year);
		
		echo json_encode($met);
	}
	
	function get_metode_evaluasi()
	{
		$this->load->library("kauth");  $userLogin = new kauth(); 
		$this->load->model("Metode");
		$metode = new Metode();
		
		$reqId = httpFilterGet("reqId");
		
		/* LOGIN CHECK */
		if ($userLogin->checkUserLogin()) 
		{ 
			$userLogin->retrieveUserInfo();
		}
		
		$metode->selectByParamsMetodeEvaluasi(array('PAKET_JENIS_ID'=>$reqId));
		$met = array();
		$i=0;
		
		while($metode->nextRow()){
			$met[$i]['PAKET_METODE_EVALUASI_ID'] = $metode->getField('PAKET_METODE_EVALUASI_ID');
			$met[$i]['PAKET_METODE_EVALUASI'] = $metode->getField('PAKET_METODE_EVALUASI');
			$i++;
		}
		echo json_encode($met);
	}
	
	function get_metode_kualifikasi()
	{
		$this->load->library("kauth");  $userLogin = new kauth(); 
		$this->load->model("Metode");
		$metode = new Metode();
		
		$reqId = httpFilterGet("reqId");
		
		/* LOGIN CHECK */
		if ($userLogin->checkUserLogin()) 
		{ 
			$userLogin->retrieveUserInfo();
		}
		
		$metode->selectByParamsMetodeKualifikasi(array('PAKET_METODE_LELANG_ID'=>$reqId));
		$met = array();
		$i=0;
		
		while($metode->nextRow()){
			$met[$i]['PAKET_METODE_KUALIFIKASI_ID'] = $metode->getField('PAKET_METODE_KUALIFIKASI_ID');
			$met[$i]['PAKET_METODE_KUALIFIKASI'] = $metode->getField('PAKET_METODE_KUALIFIKASI');
			$i++;
		}
		echo json_encode($met);
	}
	
	function get_metode_lelang()
	{
		$this->load->library("kauth");  $userLogin = new kauth(); 
		$this->load->model("Metode");
		$metode = new Metode();
		
		$reqId = httpFilterGet("reqId");
		
		/* LOGIN CHECK */
		if ($userLogin->checkUserLogin()) 
		{ 
			$userLogin->retrieveUserInfo();
		}
		/* PEMBELIAN LANGSUNG TIDAK PERLU */
		$metode->selectByParamsMetodeLelang(array('PAKET_JENIS_ID'=>$reqId), -1, -1, " AND NOT A.PAKET_METODE_LELANG_ID = 6 ");
		$met = array();
		$i=0;
		
		while($metode->nextRow()){
			$met[$i]['PAKET_METODE_LELANG_ID'] = $metode->getField('PAKET_METODE_LELANG_ID');
			$met[$i]['PAKET_METODE_LELANG'] = $metode->getField('PAKET_METODE_LELANG');
			$i++;
		}
		echo json_encode($met);
	}

	function tahap()
	{
		$this->load->model("Metode");
		$metode = new Metode();
		 
		$reqKeterangan = $this->input->post("reqKeterangan");
		$reqId = $this->input->post("reqId");
		$reqMode = $this->input->post("reqMode");
		$reqTipe = $this->input->post("reqTipe");
		$reqSearch = $this->input->post("reqSearch");
		
		$aColumns 			= array('METODE_ID', 'PAKET_METODE_LELANG_NAMA', 'PAKET_JENIS_NAMA', 'JENIS_TAHAP', 'SISTEM_SAMPUL', 'AKTIF');
		$aColumnsAlias		= array('METODE_ID', 'PAKET_METODE_LELANG_NAMA', 'PAKET_JENIS_NAMA', 'JENIS_TAHAP', 'SISTEM_SAMPUL', 'AKTIF');
		
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

			if ( trim($sOrder) == "ORDER BY METODE_ID desc" )
			{
				$sOrder = " ORDER BY METODE_ID DESC";

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
		
		$statement = "AND (UPPER(PAKET_METODE_LELANG_NAMA) LIKE '%".strtoupper($_GET['sSearch'])."%')";
		$allRecord = $metode->getCountByParamsMatrix(array(), $statement, $sOrder);
		if($_GET['sSearch'] == "")
			$allRecordFilter = $allRecord;
		else	
			$allRecordFilter =  $metode->getCountByParamsMatrix(array(), $statement, $sOrder);

		$metode->selectByParamsMatrix(array(), $dsplyRange, $dsplyStart, $statement, $sOrder);
		
		$output = array(
			"sEcho" => intval($_GET['sEcho']),
			"iTotalRecords" => $allRecord,
			"iTotalDisplayRecords" => $allRecordFilter,
			"aaData" => array()
		);
		
		while($metode->nextRow())		
		{		
			$row = array();		
			for ( $i=0 ; $i<count($aColumns) ; $i++ )		
			{	
				if($aColumns[$i]=='NO')		$row[] = $number;
				elseif($aColumns[$i]=='AKTIF'){
					if( $metode->getField(trim($aColumns[$i])) == 1)	$st = '<img src="images/centang.png">';
					else												$st = '<img src="images/uncentang.png">';
					$row[] = $st;
				}
				elseif($aColumns[$i]=='JENIS_TAHAP'){ 
					$row[] = '<span class="badge badge-primary" style="cursor:pointer" onClick="viewJadwal(\''.$metode->getField(trim($aColumns[$i])).'\')"><i class="fa fa-eye"></i> Lihat</span>';
				}
				elseif($aColumns[$i]=='SISTEM_SAMPUL'){ 
					$row[] = $metode->getField(trim($aColumns[$i])).' File';
				}
				else	$row[] = $metode->getField(trim($aColumns[$i]));
			}
			$output['aaData'][] = $row;
		}
		echo json_encode( $output );
	}

	function metode_combo()
	{
		$this->load->model("Bidangusaha");
		$bidang_usaha = new Bidangusaha();
		
		$jenis = $this->input->get("jenis");
		
		$bidang_usaha->selectByParamsGroup($jenis);
		// echo $user_login->query;exit;

		$arr_json = array();
		$i = 0;
		while($bidang_usaha->nextRow())
		{
			$arr_json[$i]['id'] = $bidang_usaha->getField("BIDANG_USAHA_PARENT_ID");
			$arr_json[$i]['text'] = trim($bidang_usaha->getField("BIDANG_USAHA_PARENT_ID"));
			$i++;
		}
		echo json_encode($arr_json);
	}
	
}
?>
