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

class rup_json extends CI_Controller {

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
		$this->KODE_SA =  isset($this->kauth->getInstance()->getIdentity()->KODE_SA) ? $this->kauth->getInstance()->getIdentity()->KODE_SA : '';
		$this->KODE_DPSJ =  isset($this->kauth->getInstance()->getIdentity()->KODE_DPSJ) ? $this->kauth->getInstance()->getIdentity()->KODE_DPSJ : '';
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
		$this->load->library("kauth");  $userLogin = new kauth();
		$this->load->model("Importsirup");
		$importrup = new Importsirup();

		ini_set("memory_limit","500M");
		ini_set('max_execution_time', 5200);

		$reqStatus= $this->input->get("reqStatus");
		$reqFilterSA= $this->input->get("reqFilterSA");
		$reqFilterDPSJ= $this->input->get("reqFilterDPSJ");

		$aColumns = array("ID","TAHUN","KODE_RUP","KODE_PR_RUP","NAMA_PAKET","NILAI_PAGU","NILAI_PAGU_PR","WAKTU_AWAL","WAKTU_AKHIR","STATUS_PROSES","CREATED_BY","NAMA_SA","PERMOHONAN_PAKET_ID","PERMOHONAN_PAKET_ANALISA_ID");
		$aColumnsAlias = array("ID","TAHUN","KODE_RUP","KODE_PR_RUP","NAMA_PAKET","NILAI_PAGU","NILAI_PAGU_PR","WAKTU_AWAL","WAKTU_AKHIR","STATUS_PROSES","CREATED_BY","NAMA_SA","PERMOHONAN_PAKET_ID","PERMOHONAN_PAKET_ANALISA_ID");

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

			if ( trim($sOrder) == "ORDER BY ID asc, ID asc" )
			{
				$sOrder = " ORDER BY ID ASC, ID ASC";
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

		$statement="";
		// $statement .="AND CREATED_BY = '1' ";
		$searchJson='';

    /*
		if($reqStatus == '')
		{}
		elseif($reqStatus == '0')
			$statement .= " AND A.POSTING IS NULL ";
		elseif($reqStatus == '1')
			$statement .= " AND D.PAKET_ID IS NULL AND A.POSTING IS NOT NULL ";
		elseif($reqStatus == '2')
			$statement .= " AND D.PAKET_ID IS NOT NULL AND A.POSTING IS NOT NULL ";
      */
		if ($reqFilterSA != '' && $reqFilterSA != '- Pilih Kode SA -') {
			$statement .= " AND KODE_SA = '".$reqFilterSA."' ";
		}

		if ($reqFilterDPSJ != '' && $reqFilterDPSJ != '- Pilih Kode DPSJ -') {
			$statement .= " AND KODE_DPSJ = '".$reqFilterDPSJ."' ";
		}
			

		$searchJson .= " AND (UPPER(TAHUN) LIKE '%".strtoupper($_GET['sSearch'])."%' OR UPPER(KODE_RUP) LIKE '%".strtoupper($_GET['sSearch'])."%' OR UPPER(NAMA_PAKET) LIKE '%".strtoupper($_GET['sSearch'])."%' OR UPPER(CREATED_BY) LIKE '%".strtoupper($_GET['sSearch'])."%' OR UPPER(STATUS_PROSES) LIKE '%".strtoupper($_GET['sSearch'])."%' )";

	if($this->USER_TYPE_ID == 9) // PENGGUNA
    {
		$statement .= " AND A.PERMOHONAN_PAKET_ID IS NULL AND A.KODE_SA IN (".$this->KODE_SA.") AND A.KODE_DPSJ IN (".$this->KODE_DPSJ.") ";
		$allRecord = $importrup->getCountByParams(array(), $statement);

		if($_GET['sSearch'] == "")
			$allRecordFilter = $allRecord;
		else
			$allRecordFilter = $importrup->getCountByParams(array(), $statement.$searchJson);

		$importrup->selectByParams(array(), $dsplyRange, $dsplyStart, $statement.$searchJson, $sOrder);

	}

	if($this->USER_TYPE_ID == 7 || $this->USER_TYPE_ID == 27 || $this->USER_TYPE_ID == 28) // KA SUBDIV; PERENCANAN; PPK
    {
		$allRecord = $importrup->getCountByParams(array(), $statement);

		if($_GET['sSearch'] == "")
			$allRecordFilter = $allRecord;
		else
			$allRecordFilter = $importrup->getCountByParams(array(), $statement.$searchJson);

		$importrup->selectByParams(array(), $dsplyRange, $dsplyStart, $statement.$searchJson, $sOrder);
	}

		/* Output */
		$output = array(
			"sEcho" => intval($_GET['sEcho']),
			"iTotalRecords" => $allRecord,
			"iTotalDisplayRecords" => $allRecordFilter,
			"aaData" => array()
		);

		while($importrup->nextRow())
		{

			$row = array();
			for ( $i=0 ; $i<count($aColumns) ; $i++ )
			{
			 	if($aColumns[$i] == "TANGGAL") {
					$row[] = getFormattedDateS($importrup->getField($aColumns[$i]));
			 	} else if($aColumns[$i] == "WAKTU_AWAL" || $aColumns[$i] == "WAKTU_AKHIR" ) {
		          $row[] = getFormattedDateYMJson($importrup->getField($aColumns[$i]));
			 	} else if($aColumns[$i] == "NILAI_PAGU" || $aColumns[$i] == "NILAI_PAGU_PR") {
		          $row[] = currencyToPage($importrup->getField($aColumns[$i]));
		        } else if($aColumns[$i] == "NAMA") {
		          $row[] = str_replace(",","",$importrup->getField($aColumns[$i]));
		        }
				else {
					$row[] = $importrup->getField($aColumns[$i]);
				}
			}

			$output['aaData'][] = $row;
		}

		echo json_encode( $output );
	}


	function jsonPersiapan()
	{
		$this->load->library("kauth");  $userLogin = new kauth();
		$this->load->model("Importsirup");
		$importrup = new Importsirup();
		$cekpermohonanUlang = new Importsirup();

		ini_set("memory_limit","500M");
		ini_set('max_execution_time', 5200);

		$reqStatus= $this->input->get("reqStatus");

		$aColumns = array("PERMOHONAN_PAKET_ANALISA_ID","SIRUP_ID","PERMOHONAN_PAKET_ID","TAHUN_ANGGARAN","KODE_RUP","KODE_PR","NAMA","NILAI","NILAI_RAB_PR","NILAI_HPS_PR","WAKTU_PENGGUNA_BARANGJASA","RENCANA_PENGADAAN","APPROVAL_PERMOHONAN_PAKET_ANALISA","APPROVAL_PERMOHONAN_PAKET_ANALISA_TEXT","STRATEGI_PENGADAAN","TOTAL_APPROVED");
		$aColumnsAlias = array("PERMOHONAN_PAKET_ANALISA_ID","SIRUP_ID","PERMOHONAN_PAKET_ID","TAHUN_ANGGARAN","KODE_RUP","KODE_PR","NAMA","NILAI","NILAI_RAB_PR","NILAI_HPS_PR","WAKTU_PENGGUNA_BARANGJASA","RENCANA_PENGADAAN","APPROVAL_PERMOHONAN_PAKET_ANALISA","APPROVAL_PERMOHONAN_PAKET_ANALISA_TEXT","STRATEGI_PENGADAAN","TOTAL_APPROVED");

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

			if ( trim($sOrder) == "ORDER BY PERMOHONAN_PAKET_ANALISA_ID asc, PERMOHONAN_PAKET_ANALISA_ID asc" )
			{
				$sOrder = " ORDER BY PERMOHONAN_PAKET_ANALISA_ID ASC, PERMOHONAN_PAKET_ANALISA_ID ASC";
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

		$statement="";
		// $statement .="AND CREATED_BY = '1' ";
		$searchJson='';

		switch ($reqStatus) { 
			case '1': // Approved
				$statement .=" AND A.APPROVAL_PERMOHONAN_PAKET_ANALISA IN ('1') ";
				break;
			case '2': // Revisi oleh Unit Kerja
				$statement .=" AND A.APPROVAL_PERMOHONAN_PAKET_ANALISA IN ('2') ";
				break;
			case '22': // Revisi oleh Unit Kerja
				$statement .=" AND A.APPROVAL_PERMOHONAN_PAKET_ANALISA IN ('2','3') ";
				break;
			case '3': // Pengecekan Perencanaan
				$statement .=" AND A.APPROVAL_PERMOHONAN_PAKET_ANALISA IN ('3') ";
				break;
			case '33': // Pengecekan Perencanaan & Revisi Pengecekan Perencanaan
				$statement .=" AND A.APPROVAL_PERMOHONAN_PAKET_ANALISA IN ('3','4') ";
				break;
			case '4': // Revisi Pengecekan Perencanaan
				$statement .=" AND A.APPROVAL_PERMOHONAN_PAKET_ANALISA IN ('4') ";
				break;
			case '5': // Pengecekan Kasubdit
				$statement .=" AND A.APPROVAL_PERMOHONAN_PAKET_ANALISA IN ('5') ";
				break;
			case '6': // Persetujuan PPK
				$statement .=" AND A.APPROVAL_PERMOHONAN_PAKET_ANALISA IN ('6') ";
				break;
			
			default:
				$statement .=" AND A.APPROVAL_PERMOHONAN_PAKET_ANALISA IN ('1','2','3','4','5','6') ";
				break;
		}

		$searchJson .= " AND (UPPER(TAHUN_ANGGARAN) LIKE '%".strtoupper($_GET['sSearch'])."%' OR UPPER(KODE_RUP) LIKE '%".strtoupper($_GET['sSearch'])."%' OR UPPER(NAMA) LIKE '%".strtoupper($_GET['sSearch'])."%' OR UPPER(KODE_PR) LIKE '%".strtoupper($_GET['sSearch'])."%')";

		if($this->USER_TYPE_ID == 9) // PENGGUNA
	    {
			// $statement .=" AND A.APPROVAL_PERMOHONAN_PAKET_ANALISA IN ('3','6','1','2') ";
			$allRecord = $importrup->getCountByParamsPersiapan(array("A.CREATED_BY" => $this->USER_LOGIN_ID), $statement);

			if($_GET['sSearch'] == "")
				$allRecordFilter = $allRecord;
			else
				$allRecordFilter = $importrup->getCountByParamsPersiapan(array("A.CREATED_BY" => $this->USER_LOGIN_ID), $statement.$searchJson);

			$importrup->selectByParamsPersiapan(array("A.CREATED_BY" => $this->USER_LOGIN_ID), $dsplyRange, $dsplyStart, $statement.$searchJson, $sOrder);

		}

		// if($this->USER_TYPE_ID == 7) // KA SUBDIV
	 //    {
		// 		$statement .=" AND A.APPROVAL_PERMOHONAN_PAKET_ANALISA IN ('1','2','3','4','6') ";
		// }

		if($this->USER_TYPE_ID == 28) // PPK
	    {
	    	if ($reqStatus) {
				$statement .= $statement;
	    	} else {
				$statement .=" AND A.APPROVAL_PERMOHONAN_PAKET_ANALISA IN ('1','6') ";
	    	}
		}

		if($this->USER_TYPE_ID == 27 || $this->USER_TYPE_ID == 28 || $this->USER_TYPE_ID == 10 || $this->USER_TYPE_ID == 25) // PERENCANAN; PPK; AUDITOR; DIREKTUR
	    {
			$allRecord = $importrup->getCountByParamsPersiapan(array(), $statement);

			if($_GET['sSearch'] == "")
				$allRecordFilter = $allRecord;
			else
				$allRecordFilter = $importrup->getCountByParamsPersiapan(array(), $statement.$searchJson);

			$importrup->selectByParamsPersiapan(array(), $dsplyRange, $dsplyStart, $statement.$searchJson, $sOrder);
		}

		/* Output */
		$output = array(
			"sEcho" => intval($_GET['sEcho']),
			"iTotalRecords" => $allRecord,
			"iTotalDisplayRecords" => $allRecordFilter,
			"aaData" => array()
		);

		while($importrup->nextRow())
		{

			$row = array();
			for ( $i=0 ; $i<count($aColumns) ; $i++ )
			{
				
			 	if($aColumns[$i] == "WAKTU_PENGGUNA_BARANGJASA" || $aColumns[$i] == "RENCANA_PENGADAAN" ) {
		          $row[] = getFormattedDateYMJson($importrup->getField($aColumns[$i]));
			 	} else if($aColumns[$i] == "NILAI" || $aColumns[$i] == "NILAI_RAB_PR" || $aColumns[$i] == "NILAI_HPS_PR") {
		          $row[] = str_replace(",-","",currencyToPage($importrup->getField($aColumns[$i])));
		        } else if($aColumns[$i] == "NAMA") {
				  $cekpermohonanUlang->selectByParamsPersiapan(array("PERMOHONAN_PAKET_ID_PARENT" => $importrup->getField("PERMOHONAN_PAKET_ID")));
				  $tenderUlang = '';
				  if ($cekpermohonanUlang->countRow() > 0) {
				  	$tenderUlang = '<br><div class="col-md-12 mt-1 mb-0" style="text-align:left; background-color:#da4453; color:#fff; font-weight: 400; font-size:85%; padding:.35em .4em !important; border-radius: .21rem !important">
                                  <i class="fa fa-refresh"></i> Paket Gagal</div>';
				  }

		          $row[] = str_replace(",","",$importrup->getField($aColumns[$i])).$tenderUlang;
		        } else if($aColumns[$i] == "APPROVAL_PERMOHONAN_PAKET_ANALISA_TEXT") {
		        	switch ($importrup->getField($aColumns[$i])) {
		        		case 'Approved':
		        			$badge = '<span class="badge badge-primary"><i class="fa fa-check-square-o"></i> Disetujui PPK dan <br> Sudah Diteruskan Pemilihan Penyedia</span>';
		        			break;
		        		case 'Pengecekan Perencanaan':
		        			$badge = '<span class="badge badge-dark">'.$importrup->getField($aColumns[$i]).'</span>';
		        			break;
		        		case 'Revisi oleh Unit Kerja':
		        			$badge = '<span class="badge badge-danger">'.$importrup->getField($aColumns[$i]).'</span>';
		        			break;
		        		case 'Pengecekan Kasubdit':
		        			$badge = '<span class="badge badge-warning">'.$importrup->getField($aColumns[$i]).'</span>';
		        			break;
		        		case 'Revisi Pengecekan Perencanaan':
		        			$badge = '<span class="badge badge-danger">'.$importrup->getField($aColumns[$i]).'</span>';
		        			break;
		        		case 'Persetujuan PPK':
		        			$badge = '<span class="badge badge-primary">'.$importrup->getField($aColumns[$i]).'</span>';
		        			break;
		        		
		        		default:
		        			// code...
		        			break;
		        	}
		          $row[] = $badge;
		        } else if($aColumns[$i] == "TOTAL_APPROVED") {
		        	if ($importrup->getField($aColumns[$i]) > 0) {
		          		$row[] = '<span class="fa fa-check-square"></span> '.$importrup->getField($aColumns[$i]).' user';
		        	} else {
		          		$row[] = '-';
		        	}
		        }
				else {
					$row[] = $importrup->getField($aColumns[$i]);
				}
			}

			$output['aaData'][] = $row;
		}

		echo json_encode( $output );
	}

	function getAttachment()
	{
		$reqRequisitionHeaderId= $this->input->get("reqRequisitionHeaderId");

		$this->load->library("libapiui");
		$libapiui = new libapiui();
 		$dataAttachment = $libapiui->getAttachment($reqRequisitionHeaderId);

 		echo '<small>Total '.count($dataAttachment).' file</small>';
 		if (count($dataAttachment) > 0) {
 			$par = $this->parsingField($dataAttachment);
 			$html = '';
 			foreach ($par as $key) {
 				$pretyFileName = str_replace("_", " ",$key['FILE_NAME']);
 				
 				$html  .= '<li class="list-group-item d-flex justify-content-between lh-condensed">
			              <div>
			                  <a href="'.URL_API_DOWNLOAD_FILE_PR.$key['DOCUMENT_ID'].'">
			                	<p class="my-0" style="line-height:14px">'.$pretyFileName.' <br><small style="mt-1">'.$key['UPLOAD_DATE'].' </small></p>
			                  </a>
			              </div>
			              <span class="text-muted">
			              </span>
			            </li>';
 			}
 		} 

		echo $html;
	}

	function parsingField(array $data)
	{
	    // Sort berdasarkan UPLOAD_DATE (jika ada)
	    usort($data, function($a, $b) {

	        $dateA = isset($a->UPLOAD_DATE) ? $a->UPLOAD_DATE : null;
	        $dateB = isset($b->UPLOAD_DATE) ? $b->UPLOAD_DATE : null;

	        return $dateB <=> $dateA;
	    });

	    $result = [];

	    foreach ($data as $item) {
	        $result[] = [
	            'REQUISITION_HEADER_ID' => $item->REQUISITION_HEADER_ID ?? null,
	            'ORG_ID'                => $item->ORG_ID ?? null,
	            'ORG_NAME'              => $item->ORG_NAME ?? null,
	            'PR_NUMBER'             => $item->PR_NUMBER ?? null,
	            'PRE_DESC'              => $item->PRE_DESC ?? null,
	            'ENTITY_NAME'           => $item->ENTITY_NAME ?? null,
	            'DOCUMENT_ID'           => $item->DOCUMENT_ID ?? null,
	            'CATEGORY'              => $item->CATEGORY ?? null,
	            'TYPE'                  => $item->TYPE ?? null,
	            'FILE_NAME'             => $item->FILE_NAME ?? null,
	            'FILE_CONTENT_TYPE'     => $item->FILE_CONTENT_TYPE ?? null,
	            'FILE_FORMAT'           => $item->FILE_FORMAT ?? null,
	            'UPLOAD_DATE'           => $item->UPLOAD_DATE ?? null,
	        ];
	    }

	    return $result;
	}

	function combosa() 
	{
		$this->load->model('Importsirup');
		$kodesa = new Importsirup();

		$kodesa->selectSA(array());

		$i = 0;
		while($kodesa->nextRow())
		{
			$arr_json[$i]['id']		= $kodesa->getField("KODE_SA");
			$arr_json[$i]['text']	= $kodesa->getField("NAMA");
			$i++;
		}

		echo json_encode($arr_json);
	}

	function combodpsj() 
	{
		$this->load->model('Importsirup');
		$kodesa = new Importsirup();

		$kodesa->selectDPSJ(array());

		$i = 0;
		while($kodesa->nextRow())
		{
			$arr_json[$i]['id']		= $kodesa->getField("KODE_DPSJ");
			$arr_json[$i]['text']	= $kodesa->getField("NAMA");
			$i++;
		}

		echo json_encode($arr_json);
	}


}
?>
