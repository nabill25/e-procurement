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
include_once("functions/default.func.php");

class dokumen_template_rekanan_json extends CI_Controller {

	function __construct() {
		parent::__construct(); $this->db->query("SET TIME ZONE 'Asia/Jakarta'");

		//kauth
		if (!$this->kauth->getInstance()->hasIdentity())
		{
		}

		$this->ID = $this->kauth->getInstance()->getIdentity()->REKANAN_ID;
		$this->USER_LOGIN_ID =  $this->kauth->getInstance()->getIdentity()->USER_LOGIN_ID;
		$this->USER_LOGIN =  $this->kauth->getInstance()->getIdentity()->USER_LOGIN;
		$this->USER_NAMA =  $this->kauth->getInstance()->getIdentity()->USER_NAMA;
		$this->USER_TYPE_ID =  $this->kauth->getInstance()->getIdentity()->USER_TYPE_ID;
		$this->REKANAN_ID =  $this->kauth->getInstance()->getIdentity()->REKANAN_ID;
		$this->UNIT_KERJA_ID =  $this->kauth->getInstance()->getIdentity()->UNIT_KERJA_ID;
		$this->NIP =  $this->kauth->getInstance()->getIdentity()->NIP;
		$this->LOGIN_TIME = $this->kauth->getInstance()->getIdentity()->LOGIN_TIME;
		$this->LOGIN_DATE = $this->kauth->getInstance()->getIdentity()->LOGIN_DATE;
		$this->REKANAN = $this->kauth->getInstance()->getIdentity()->REKANAN;
		$this->REKANAN_KODE = $this->kauth->getInstance()->getIdentity()->REKANAN_KODE;
		$this->REKANAN_PKP = $this->kauth->getInstance()->getIdentity()->REKANAN_PKP;
		$this->REKANAN_NPWP = $this->kauth->getInstance()->getIdentity()->REKANAN_NPWP;
		$this->REKANAN_STATUS_PERUSAHAAN = $this->kauth->getInstance()->getIdentity()->STATUS_PERUSAHAAN;
		$this->REKANAN_STATUS_VALIDASI = $this->kauth->getInstance()->getIdentity()->STATUS_VALIDASI;
	}

	function json()
	{
		$this->load->model("Dokumentemplaterekanan");
		$dokumen = new Dokumentemplaterekanan();

		$reqKeterangan = $this->input->post("reqKeterangan");
		$reqId = $this->input->get("reqId");
		$reqSearch = $this->input->post("reqSearch");
		$reqAgamaId = $this->input->post("reqAgamaId");

		$aColumns 			= array('TEMPLATE_REKANAN_ID', 'NAMA', 'KETERANGAN', 'LAMPIRAN');
		$aColumnsAlias		= array('TEMPLATE_REKANAN_ID', 'NAMA', 'KETERANGAN', 'LAMPIRAN');
		$FILE_DIR 			= "uploads/dokumen_template/";

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

			if ( trim($sOrder) == "ORDER BY TEMPLATE_REKANAN_ID desc" )
			{
				$sOrder = " ORDER BY TEMPLATE_REKANAN_ID DESC ";
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

		$statement = "AND (UPPER(NAMA) LIKE '%".strtoupper($_GET['sSearch'])."%')";
		$allRecord = $dokumen->getCountByParams(array(), $statement, $sOrder);
		if($_GET['sSearch'] == "")
			$allRecordFilter = $allRecord;
		else
			$allRecordFilter =  $dokumen->getCountByParams(array(), $statement, $sOrder);

		$dokumen->selectByParams(array(), $dsplyRange, $dsplyStart, $statement, $sOrder);

		$output = array(
			"sEcho" => intval($_GET['sEcho']),
			"iTotalRecords" => $allRecord,
			"iTotalDisplayRecords" => $allRecordFilter,
			"aaData" => array()
		);

		while($dokumen->nextRow())
		{
			$row = array();
			for ( $i=0 ; $i<count($aColumns) ; $i++ )
			{
				if($aColumns[$i]=='NO')		$row[] = $number;
				elseif($aColumns[$i]=='TANGGAL')
					$row[] = getFormattedDateJson($dokumen->getField(trim($aColumns[$i])));
				elseif($aColumns[$i]=='KETERANGAN')
						$row[] = substr($dokumen->getField(trim($aColumns[$i])), 0, 200); 
				elseif($aColumns[$i]=='LAMPIRAN') {
					if ($dokumen->getField("LAMPIRAN")) {
						$row[] = '<a href="'.$FILE_DIR.$dokumen->getField("LAMPIRAN").'" target="_blank" class="badge badge-primary"><span class="fa fa-download"> Download </span></a>';
					} else {
						$row[] = '-';
					}
				}
				else	$row[] = $dokumen->getField(trim($aColumns[$i]));
			}
			$output['aaData'][] = $row;
		}
		echo json_encode( $output );
	}

	function add()
	{
		$this->load->model('Dokumentemplaterekanan');
		$this->load->library("FileHandler");
		$dokumen	= new Dokumentemplaterekanan();
		$file = new FileHandler();
		// echo "<prev>"; print_r($this->input->post()); die();
		$reqId		= $this->input->post('reqId');
		$reqMode	= $this->input->post('reqMode');

		$reqNama				= $this->input->post('reqNama');
		$reqKeterangan	= str_replace("'","''",$_POST["reqKeterangan"]);
		$reqTanggal			= $this->input->post('reqTanggal');

		$reqLinkFile		= $_FILES['reqLinkFile'];
		$reqLinkFileTemp = $this->input->post("reqLinkFileTemp");

		$FILE_DIR = "uploads/dokumen_template/";

		if($reqMode == "insert")
		{
			$dokumen	= new Dokumentemplaterekanan();
			$dokumen->setField("TEMPLATE_REKANAN_ID", $reqId);
			$dokumen->setField("NAMA", $reqNama);
			$dokumen->setField("KETERANGAN", $reqKeterangan);

			$renameFile = md5(date("dmYHis").$reqLinkFile['name'].$this->ID).".".getExtension($reqLinkFile['name']);
			if($file->uploadToDir('reqLinkFile', $FILE_DIR, $renameFile))
			{
				$insertLinkFile =  $renameFile;
			}
			else
			{
				$insertLinkFile =  $reqLinkFileTemp;
			}
			$dokumen->setField("LAMPIRAN", $insertLinkFile);

			$dokumen->setField("TANGGAL", dateToDBCheck($reqTanggal));
			$dokumen->setField("USER_LOGIN_ID", $this->USER_LOGIN_ID);

			$dokumen->insert();

		}
		else
		{
			$dokumen	= new Dokumentemplaterekanan();
			$dokumen->setField("TEMPLATE_REKANAN_ID", $reqId);
			$dokumen->setField("NAMA", $reqNama);
			$dokumen->setField("NAMA", $reqNama);
			$dokumen->setField("KETERANGAN", $reqKeterangan);

			$renameFile = md5(date("dmYHis").$reqLinkFile['name'].$this->ID).".".getExtension($reqLinkFile['name']);
			if($file->uploadToDir('reqLinkFile', $FILE_DIR, $renameFile))
			{
				$insertLinkFile =  $renameFile;
			}
			else
			{
				$insertLinkFile =  $reqLinkFileTemp;
			}
			$dokumen->setField("LAMPIRAN", $insertLinkFile);

			$dokumen->setField("TANGGAL", dateToDBCheck($reqTanggal));
			$dokumen->setField("USER_LOGIN_ID", $this->USER_LOGIN_ID);
			$dokumen->update();
		}

		echo "Data berhasil disimpan.";
	}

	function delete()
	{
		$this->load->model('Dokumentemplaterekanan');

		$dokumen	= new Dokumentemplaterekanan();
		$reqId		= $this->input->get('reqId');
		$reqNama	= $this->input->post('reqNama');
		$dokumen	= new Dokumentemplaterekanan();
		$dokumen->setField("TEMPLATE_REKANAN_ID", $reqId);
		$dokumen->delete();

		echo "Data berhasil disimpan.";
	}

	function combo()
	{
		$this->load->model('Dokumentemplaterekanan');
		$dokumen = new Dokumentemplaterekanan();

		$dokumen->selectByParams();

		$i = 0;
		while($dokumen->nextRow())
		{
			$arr_json[$i]['id']		= $dokumen->getField("TEMPLATE_REKANAN_ID");
			$arr_json[$i]['text']	= $dokumen->getField("NAMA");
			$i++;
		}

		echo json_encode($arr_json);
	}

}
?>
