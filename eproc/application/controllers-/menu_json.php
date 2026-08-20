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

class menu_json extends CI_Controller {

	function __construct() {
		parent::__construct(); $this->db->query("SET TIME ZONE 'Asia/Jakarta'"); 
		
		if (!$this->kauth->getInstance()->hasIdentity())
		{
		}       
		
		$this->ID = $this->kauth->getInstance()->getIdentity()->REKANAN_ID;   
	}	
	
	function json() 
	{
		$this->load->model("Menu");
		$this->load->model("UserType");
		$menu = new Menu();
		$user_type = new UserType();
		
		$reqKeterangan = $this->input->post("reqKeterangan");
		$reqId = $this->input->get("reqId");
		$reqSearch = $this->input->post("reqSearch");
		$reqAgamaId = $this->input->post("reqAgamaId");
		
		$aColumns 			= array('MENUID', 'NAMAMENU','LINKMENU','HAKAKSES','STATUSAKTIF');
		$aColumnsAlias		= array('MENUID', 'NAMAMENU','LINKMENU','HAKAKSES','STATUSAKTIF');
		
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
			
			if ( trim($sOrder) == "ORDER BY MENUID desc" )
			{
				$sOrder = " ORDER BY MENUID DESC";
				 
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
		
		$statement = " AND (UPPER(NAMAMENU) LIKE '%".strtoupper($_GET['sSearch'])."%') OR (UPPER(LINKMENU) LIKE '%".strtoupper($_GET['sSearch'])."%')";
		$allRecord = $menu->getCountByParams(array(), $statement, $sOrder);
		if($_GET['sSearch'] == "")
			$allRecordFilter = $allRecord;
		else	
			$allRecordFilter =  $menu->getCountByParams(array(), $statement, $sOrder);

		$menu->selectByParams(array(), $dsplyRange, $dsplyStart, $statement, $sOrder);
		
		$output = array(
			"sEcho" => intval($_GET['sEcho']),
			"iTotalRecords" => $allRecord,
			"iTotalDisplayRecords" => $allRecordFilter,
			"aaData" => array()
		);
		
		while($menu->nextRow())		
		{		
			$row = array();		
			for ( $i=0 ; $i<count($aColumns) ; $i++ )		
			{	
				if($aColumns[$i]=='NO'){
					$row[] = $number;
				} elseif($aColumns[$i]=='HAKAKSES')	
				{
					$gabung = '';
					if($menu->getField(trim($aColumns[$i]))) {
						$f = explode(',', $menu->getField(trim($aColumns[$i])));
						$gabung = '';
						if ($f > 1) {
							// 1	ADMIN
							// 2	ADMIN VMS
							// 3	PANITIA
							// 6	PENYEDIA
							// 7	KEPALA PENGADAAN
							// 9	PENGGUNA
							// 10	AUDIT
							// 11	PEJABAT PENGADAAN
							// 12	PENGELOLA KONTRAK
							// 13	PPHP
							// 17	ADMIN RUP
							// 18	APPROVAL VMS
							// 19	REKOMENDASI VMS
							// 20	PEMERIKSA KONTRAK
							// 21	UNIT / INSTALASI
							// 22	VALIDATOR UNIT
							// 23	APPROVAL UNIT
							// 24	ADMIN RUP
							foreach ($f as $key => $value) {
								$user_type->selectByParams(array('USER_TYPE_ID' => $value));
								$user_type->firstRow();
								switch ($value) {
									case '1': 
									case '19': 
										$gabung .= '<span class="badge badge-primary" style="margin-top:2px">'.ucwords(strtolower($user_type->getField("NAMA"))).'</span>&nbsp;';
										 break; 
									case '2': 
									case '20': 
										$gabung .= '<span class="badge badge-danger" style="margin-top:2px">'.ucwords(strtolower($user_type->getField("NAMA"))).'</span>&nbsp;';
										 break; 
									case '3': 
									case '21': 
										$gabung .= '<span class="badge badge-info" style="margin-top:2px">'.ucwords(strtolower($user_type->getField("NAMA"))).'</span>&nbsp;';
										 break; 
									case '6': 
										$gabung .= '<span class="badge badge-success" style="margin-top:2px">'.ucwords(strtolower($user_type->getField("NAMA"))).'</span>&nbsp;';
										 break; 
									case '7': 
									case '23': 
										$gabung .= '<span class="badge badge-primary" style="margin-top:2px">'.ucwords(strtolower($user_type->getField("NAMA"))).'</span>&nbsp;';
										 break; 
									case '9': 
									case '24': 
										$gabung .= '<span class="badge badge-info" style="margin-top:2px">'.ucwords(strtolower($user_type->getField("NAMA"))).'</span>&nbsp;';
										 break; 
									case '10': 
									case '17': 
										$gabung .= '<span class="badge badge-success" style="margin-top:2px">'.ucwords(strtolower($user_type->getField("NAMA"))).'</span>&nbsp;';
										 break; 
									case '11': 
										$gabung .= '<span class="badge badge-danger" style="margin-top:2px">'.ucwords(strtolower($user_type->getField("NAMA"))).'</span>&nbsp;';
										 break; 
									case '12': 
									case '22': 
										$gabung .= '<span class="badge badge-secondary" style="margin-top:2px">'.ucwords(strtolower($user_type->getField("NAMA"))).'</span>&nbsp;';
										 break; 
									case '18': 
										$gabung .= '<span class="badge badge-warning" style="margin-top:2px">'.ucwords(strtolower($user_type->getField("NAMA"))).'</span>&nbsp;';
										 break; 
									default: 
										// $gabung = 'primary';
										$gabung .= '<span class="badge badge-danger" style="margin-top:2px">'.ucwords(strtolower($user_type->getField("NAMA"))).'</span>&nbsp;';
										break;
								}
							}
						} else {
							$gabung .= '';
						}
					}
					$row[] = $gabung;
					// $row[] = getFormattedDateJson($menu->getField(trim($aColumns[$i])));
				} elseif($aColumns[$i]=='STATUSAKTIF'){
						if( $menu->getField(trim($aColumns[$i])) == 'Y') {
							$st = '<span class="badge badge-primary">Aktif</span>';					
						} else {
							$st = '<span class="badge badge-danger">Non Aktif</span>';					
						}															
						$row[] = $st;
				} elseif($aColumns[$i]=='UNIT_KERJA'){
					$row[] = $menu->getField(trim($aColumns[$i]))."*".$menu->getField("SK_PANITIA_ID");
				} else {
					$row[] = $menu->getField(trim($aColumns[$i]));
				}	
			}
			$output['aaData'][] = $row;
		}
		echo json_encode( $output );	
	}
	
	function add() 
	{
		$this->load->model('Menu');
		
		$menu	= new Menu();
		
		$reqId		= $this->input->post('reqId');
		$reqMode	= $this->input->post('reqMode');
		// echo "<pre>"; print_r($this->input->post());
		$reqNamamenu		= $this->input->post('reqNamamenu');
		$reqLinkmenu		= $this->input->post('reqLinkmenu');
		$reqHakakses		= $this->input->post('reqHakakses');
		// echo count($reqHakakses);
		// if (count($reqHakakses) > 1) {
			$gabung = '';
		  foreach ($reqHakakses as $key => $value) {
		  	if ($key < (count($reqHakakses)-1)) {
				$gabung .= $value.',';
		  	} else {
				$gabung .= $value;
		  	}
		  }
		// }
		$reqStatusaktif		= $this->input->post('reqStatusaktif');
		
		if($reqMode == "insert")
		{
			$menu	= new Menu();
			$menu->setField("NAMAMENU", $reqNamamenu);
			$menu->setField("LINKMENU", $reqLinkmenu);
			$menu->setField("HAKAKSES", $gabung);
			$menu->setField("STATUSAKTIF", $reqStatusaktif);
			$menu->setField("CREATED_BY", $this->USER_LOGIN_ID);
			$menu->insert();
			echo "Data berhasil disimpan.";
		}
		else
		{
			$menu	= new Menu();
			$menu->setField("MENUID", $reqId);
			$menu->setField("NAMAMENU", $reqNamamenu);
			$menu->setField("LINKMENU", $reqLinkmenu);
			$menu->setField("HAKAKSES", $gabung);
			$menu->setField("STATUSAKTIF", $reqStatusaktif);
			$menu->setField("CREATED_BY", $this->USER_LOGIN_ID);
			$menu->update();
			echo "Data berhasil diubah.";
		}
		
	}
	
	function delete() 
	{
		$this->load->model('Menu');
		
		$menu	= new Menu();
		
		$reqId		= $this->input->get('reqId');
		
		$menu	= new Menu();
		$menu->setField("MENUID", $reqId);
		$menu->delete();
		echo "Data berhasil dihapus.";
	} 
	
}
?>
