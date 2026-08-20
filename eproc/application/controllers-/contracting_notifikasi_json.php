<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * @package 	eProcurement Application
 * @author 		eproc2025
 * @since 		25. Version 3.1
 *
 */

include_once("functions/default.func.php");
include_once("functions/string.func.php");
include_once("functions/date.func.php");

class contracting_notifikasi_json extends CI_Controller {

	function __construct() {
		parent::__construct(); $this->db->query("SET TIME ZONE 'Asia/Jakarta'");

		if (!$this->kauth->getInstance()->hasIdentity()) { }

		$this->ID = $this->kauth->getInstance()->getIdentity()->REKANAN_ID;
		$this->USER_LOGIN_ID =  $this->kauth->getInstance()->getIdentity()->USER_LOGIN_ID;
		$this->USER_LOGIN =  $this->kauth->getInstance()->getIdentity()->USER_LOGIN;
		$this->USER_NAMA =  $this->kauth->getInstance()->getIdentity()->USER_NAMA;
		$this->USER_TYPE_ID =  $this->kauth->getInstance()->getIdentity()->USER_TYPE_ID;
		$this->LEGAL =  $this->kauth->getInstance()->getIdentity()->LEGAL;
		$this->REKANAN_ID =  $this->kauth->getInstance()->getIdentity()->REKANAN_ID;
		$this->UNIT_KERJA_ID =  $this->kauth->getInstance()->getIdentity()->UNIT_KERJA_ID;
		$this->NIP =  $this->kauth->getInstance()->getIdentity()->NIP;
		$this->LOGIN_TIME = $this->kauth->getInstance()->getIdentity()->LOGIN_TIME;
		$this->LOGIN_DATE = $this->kauth->getInstance()->getIdentity()->LOGIN_DATE;
		$this->REKANAN = $this->kauth->getInstance()->getIdentity()->NAMA;
		$this->REKANAN_KODE = $this->kauth->getInstance()->getIdentity()->KODE;
		$this->REKANAN_EMAIL = $this->kauth->getInstance()->getIdentity()->REKANAN_EMAIL;
		$this->REKANAN_PKP = $this->kauth->getInstance()->getIdentity()->PKP;
		$this->REKANAN_NPWP = $this->kauth->getInstance()->getIdentity()->NPWP;
		$this->REKANAN_STATUS_PERUSAHAAN = $this->kauth->getInstance()->getIdentity()->STATUS_PERUSAHAAN;
		$this->REKANAN_STATUS_VALIDASI = $this->kauth->getInstance()->getIdentity()->STATUS_VALIDASI;
	}

	function json()
	{
		$this->load->library("kauth");  $userLogin = new kauth();
		$this->load->model("Contractingnotifikasi");

		$contractingnotifikasi = new Contractingnotifikasi();
		$reqSearch = $this->input->get("reqSearch");
		$reqId = $this->input->get("reqId");

		ini_set("memory_limit","500M");
		ini_set('max_execution_time', 5200);

		$aColumns = array("CONTRACTING_NOTIFIKASI_ID","PAKET_ID","JUDUL", "TANGGAL_NOTIFIKASI_DARI", "TANGGAL_NOTIFIKASI_SAMPAI","PEMBUAT");
		$aColumnsAlias = array("CONTRACTING_NOTIFIKASI_ID","PAKET_ID","JUDUL", "TANGGAL_NOTIFIKASI_DARI", "TANGGAL_NOTIFIKASI_SAMPAI","PEMBUAT");

		if ( isset( $_GET['iSortCol_0'] ) )
		{
			$sOrder = " ORDER BY ";

			for ( $i=0 ; $i<intval( $_GET['iSortingCols'] ) ; $i++ )
			{
				if ( $_GET[ 'bSortable_'.intval($_GET['iSortCol_'.$i]) ] == "true" )
				{
					$sOrder .= $aColumnsAlias[ intval( $_GET['iSortCol_'.$i] ) ];

					if (strcasecmp(( $_GET['sSortDir_'.$i] ), "asc") == 0)
					{
						$sOrder .=" asc, ";
					}else
					{
						$sOrder .=" desc, ";
					}
				}
			}

			$sOrder = substr_replace( $sOrder, "", -2 );

			if ( trim($sOrder) == "ORDER BY A.CONTRACTING_NOTIFIKASI_ID asc" )
			{
				$sOrder = " ORDER BY COALESCE(A.CONTRACTING_NOTIFIKASI_ID, 0) DESC";
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

		$statement = "AND PAKET_ID = ".$reqId." AND (UPPER(JUDUL) LIKE '%".strtoupper($_GET['sSearch'])."%')";

		$allRecord = $contractingnotifikasi->getCountByParams(array(),$statement);
		if($_GET['sSearch'] == "")
			$allRecordFilter = $allRecord;
		else
			$allRecordFilter = $contractingnotifikasi->getCountByParams(array(), $statement.$searchJson);

		$contractingnotifikasi->selectByParams(array(), $dsplyRange, $dsplyStart, $statement.$searchJson, $sOrder);

		$output = array(
			"sEcho" => intval($_GET['sEcho']),
			"iTotalRecords" => $allRecord,
			"iTotalDisplayRecords" => $allRecordFilter,
			"aaData" => array()
		);
		while($contractingnotifikasi->nextRow())
		{
			$row = array();
			for ( $i=0 ; $i<count($aColumns) ; $i++ )
			{ 

				if($aColumns[$i] == "JUDUL")
					$row[] = $contractingnotifikasi->getField($aColumns[$i]);
				elseif($aColumns[$i]=='TANGGAL_NOTIFIKASI_DARI' || $aColumns[$i]=='TANGGAL_NOTIFIKASI_SAMPAI')
					$row[] = getFormattedDateJson($contractingnotifikasi->getField(trim($aColumns[$i])));
				else
					$row[] = $contractingnotifikasi->getField($aColumns[$i]);
			}

			$output['aaData'][] = $row;
		}

		echo json_encode( $output );
	} 

	function add() 
	{
		$this->load->model('Contractingnotifikasi');
		$this->load->library("FileHandler"); 
		
		$reqId		= $this->input->post('reqId');
		$reqPaketId		= $this->input->post('reqPaketId');
		$reqMode	= $this->input->post('reqMode');

		$reqJudul			= $this->input->post('reqJudul');
		$reqTanggalNotifikasiDari			= $this->input->post('reqTanggalNotifikasiDari');
		$reqTanggalNotifikasiSampai			= $this->input->post('reqTanggalNotifikasiSampai');
		$reqTanggal			= date('Y-m-d H:i:s');
		
		
		if($reqMode == "insert")
		{
			$contracting = new Contractingnotifikasi();
			$contracting->setField("PAKET_ID", $reqPaketId);
			$contracting->setField("JUDUL", $reqJudul); 
			$contracting->setField("TANGGAL_NOTIFIKASI_DARI", dateToDBCheck($reqTanggalNotifikasiDari)); 
			// $contracting->setField("TANGGAL_NOTIFIKASI_SAMPAI", dateToDBCheck($reqTanggalNotifikasiSampai)); 
			$contracting->setField("CREATED_BY", $this->USER_LOGIN_ID);
			$contracting->insert();
			
		}
		else
		{
			$contracting = new Contractingnotifikasi();
			$contracting->setField("CONTRACTING_NOTIFIKASI_ID", $reqId);
			$contracting->setField("JUDUL", $reqJudul); 
			$contracting->setField("TANGGAL_NOTIFIKASI_DARI", dateToDBCheck($reqTanggalNotifikasiDari)); 
			// $contracting->setField("TANGGAL_NOTIFIKASI_SAMPAI", dateToDBCheck($reqTanggalNotifikasiSampai)); 
			$contracting->setField("CREATED_BY", $this->USER_LOGIN_ID);
			$contracting->update();
		}
		
		echo "Data berhasil disimpan.";
	}
	
	function delete() 
	{
		$this->load->model('Contractingnotifikasi');
		
		$reqId		= $this->input->get('reqId');
		
		$contracting	= new Contractingnotifikasi();
		$contracting->setField("CONTRACTING_NOTIFIKASI_ID", $reqId);
		$contracting->delete();
		
		echo "Data berhasil disimpan.";
	}

}
?>
