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

class master_pengaturan_json extends CI_Controller {

	function __construct() {
		parent::__construct(); $this->db->query("SET TIME ZONE 'Asia/Jakarta'");
		//kauth
		if (!$this->kauth->getInstance()->hasIdentity())
		{
		}

		/* GLOBAL VARIABLE */
		$this->ID = isset($this->kauth->getInstance()->getIdentity()->REKANAN_ID) ? $this->kauth->getInstance()->getIdentity()->REKANAN_ID : '';

		$this->USER_LOGIN_ID =  isset($this->kauth->getInstance()->getIdentity()->USER_LOGIN_ID) ? $this->kauth->getInstance()->getIdentity()->USER_LOGIN_ID : '';
		$this->USER_LOGIN =  isset($this->kauth->getInstance()->getIdentity()->USER_LOGIN) ? $this->kauth->getInstance()->getIdentity()->USER_LOGIN : '';
		$this->USER_NAMA =  isset($this->kauth->getInstance()->getIdentity()->USER_NAMA) ? $this->kauth->getInstance()->getIdentity()->USER_NAMA : '';
		$this->USER_TYPE_ID =  isset($this->kauth->getInstance()->getIdentity()->USER_TYPE_ID) ? $this->kauth->getInstance()->getIdentity()->USER_TYPE_ID : '';
		$this->REKANAN_ID =  isset($this->kauth->getInstance()->getIdentity()->REKANAN_ID) ? $this->kauth->getInstance()->getIdentity()->REKANAN_ID : '';
		$this->UNIT_KERJA_ID =  isset($this->kauth->getInstance()->getIdentity()->UNIT_KERJA_ID) ? $this->kauth->getInstance()->getIdentity()->UNIT_KERJA_ID : '';
		$this->NIP =  isset($this->kauth->getInstance()->getIdentity()->NIP) ? $this->kauth->getInstance()->getIdentity()->NIP : '';
		$this->LOGIN_TIME = isset($this->kauth->getInstance()->getIdentity()->LOGIN_TIME) ? $this->kauth->getInstance()->getIdentity()->LOGIN_TIME : '';
		$this->LOGIN_DATE = isset($this->kauth->getInstance()->getIdentity()->LOGIN_DATE) ? $this->kauth->getInstance()->getIdentity()->LOGIN_DATE : '';
		$this->REKANAN = isset($this->kauth->getInstance()->getIdentity()->NAMA) ? $this->kauth->getInstance()->getIdentity()->NAMA : '';
		$this->REKANAN_KODE = isset($this->kauth->getInstance()->getIdentity()->KODE) ? $this->kauth->getInstance()->getIdentity()->KODE : '';
		$this->REKANAN_PKP = isset($this->kauth->getInstance()->getIdentity()->PKP) ? $this->kauth->getInstance()->getIdentity()->PKP : '';
		$this->REKANAN_NPWP = isset($this->kauth->getInstance()->getIdentity()->NPWP) ? $this->kauth->getInstance()->getIdentity()->NPWP : '';
		$this->REKANAN_STATUS_PERUSAHAAN = isset($this->kauth->getInstance()->getIdentity()->STATUS_PERUSAHAAN) ? $this->kauth->getInstance()->getIdentity()->STATUS_PERUSAHAAN : '';
		$this->REKANAN_STATUS_VALIDASI = isset($this->kauth->getInstance()->getIdentity()->STATUS_VALIDASI) ? $this->kauth->getInstance()->getIdentity()->STATUS_VALIDASI : '';
	}

	function json() 
	{
		$this->load->model("Masterpengaturan");
		$master_pengaturan = new Masterpengaturan(); 
		
		$aColumns 			= array('REKANAN_ID', 'NAMA', 'JENIS', 'MENU', 'TANGGAL_BERAKHIR');
		$aColumnsAlias		= array('REKANAN_ID', 'NAMA', 'JENIS', 'MENU', 'TANGGAL_BERAKHIR');
		
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
			
			if ( trim($sOrder) == "ORDER BY ID desc" )
			{
				$sOrder = " ORDER BY ID DESC ";
				 
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
		
		$statement = "AND (UPPER(NAMA) LIKE '%".strtoupper($_GET['sSearch'])."%') OR (UPPER(JENIS) LIKE '%".strtoupper($_GET['sSearch'])."%')";
		$allRecord = $master_pengaturan->getCountByParamsDokExpired(array(), $statement, $sOrder);
		if($_GET['sSearch'] == "")
			$allRecordFilter = $allRecord;
		else	
			$allRecordFilter =  $master_pengaturan->getCountByParamsDokExpired(array(), $statement, $sOrder);

		$master_pengaturan->selectByParamsDokExpired(array(), $dsplyRange, $dsplyStart, $statement, $sOrder);
		
		$output = array(
			"sEcho" => intval($_GET['sEcho']),
			"iTotalRecords" => $allRecord,
			"iTotalDisplayRecords" => $allRecordFilter,
			"aaData" => array()
		);
		
		while($master_pengaturan->nextRow())		
		{		
			$row = array();		
			for ( $i=0 ; $i<count($aColumns) ; $i++ )		
			{	
				if($aColumns[$i]=='NO')		$row[] = $number;
				elseif($aColumns[$i]=='TANGGAL_BERAKHIR') {
					$row[] = getFormattedDateJson(str_replace(" 00:00:00", "", $master_pengaturan->getField(trim($aColumns[$i]))));
				}
				else	$row[] = $master_pengaturan->getField(trim($aColumns[$i]));
			}
			$output['aaData'][] = $row;
		}
		echo json_encode( $output );	
	}

	function update($stat)
	{
		$this->load->model("Masterpengaturan");
		$this->load->library("kauth");  
		$userLogin = new kauth();

		$master_peng	= new Masterpengaturan();
		 
		$master_peng->setField("AKTIF", $stat);
		$master_peng->setField("ID", 1);

		if ($master_peng->update()) {
			echo json_encode(array('data' => '1', 'message' => 'Data berhasil di update'));	
		} else {
			echo json_encode(array('data' => '0', 'message' => 'Data gagal di update, silahkan dicoba kembali'));	
		}
	}

	function add()
	{
		$this->load->model("Masterpengaturan");
		$this->load->library("kauth");  $userLogin = new kauth();

    $reqTmNote = $this->input->post("reqTmzzNote");
		$reqTmDate = $this->input->post("reqTmDate");
    // echo "<pre>"; print_r($reqTmNote); die();
    // echo count($reqTmNote); die;
		$master_tanggal_delete = new Masterpengaturan();

      if ($master_tanggal_delete->deteleTanggal()) {
        // echo "string"; die;
  		for($i=0; $i<count($reqTmNote);$i++)
  		{
  			if($reqTmNote[$i] == "")
  			{}
  			else
  			{
  				$master_tanggal_add = new Masterpengaturan();
  				$master_tanggal_add->setField("TM_NOTE", $reqTmNote[$i]);
  				$master_tanggal_add->setField("TM_DATE", dateToDBCheck($reqTmDate[$i]));
  				$master_tanggal_add->setField('CREATED_BY', $this->USER_LOGIN_ID);
  				$master_tanggal_add->insert();
  			}
  			unset($master_tanggal_add);
  		}
      echo "Data berhasil disimpan";
    } else {
      echo "Data gagal disimpan";
    }
	}

}
?>
