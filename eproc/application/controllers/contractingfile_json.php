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

class contractingfile_json extends CI_Controller {

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

		$this->USER_LOGIN_ID =  $this->kauth->getInstance()->getIdentity()->USER_LOGIN_ID;
		$this->USER_LOGIN =  $this->kauth->getInstance()->getIdentity()->USER_LOGIN;
		$this->USER_NAMA =  $this->kauth->getInstance()->getIdentity()->USER_NAMA;
		$this->USER_TYPE_ID =  $this->kauth->getInstance()->getIdentity()->USER_TYPE_ID;
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
		$this->load->model("Contractingfile");

		$contractingfile = new Contractingfile();
		$reqSearch = $this->input->get("reqSearch");

		ini_set("memory_limit","500M");
		ini_set('max_execution_time', 5200);

		$aColumns = array("CONTRACTINGFILEID", "FILE_NAME", "FILE_KETERANGAN", "CONTRACTINGPROSESID", "FILE_PUBLISH_PENYEDIA","CREATED_BY");
		$aColumnsAlias = array("A.CONTRACTINGFILEID", "A.FILE_NAME", "A.FILE_KETERANGAN", "A.CONTRACTINGPROSESID", "A.FILE_PUBLISH_PENYEDIA", "A.CREATED_BY");

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
			if ( trim($sOrder) == "ORDER BY A.PAKET_ID asc" )
			{
				/*
				* If there is no order by clause - ORDER BY INDEX COLUMN!!! DON'T DELETE IT!
				* If there is no order by clause there might be bugs in table display.
				* No order by clause means that the db is not responsible for the data ordering,
				* which means that the same row can be displayed in two pages - while
				* another row will not be displayed at all.
				*/
				$sOrder = " ORDER BY COALESCE(A.PAKET_ID, 0) DESC";

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


		$statement = "AND (UPPER(NAMA) LIKE '%".strtoupper($_GET['sSearch'])."%')";

		$allRecord = $contractingfile->getCountByParams(array(),$statement);
		if($_GET['sSearch'] == "")
			$allRecordFilter = $allRecord;
		else
			$allRecordFilter = $contractingfile->getCountByParams(array(), $statement.$searchJson);

		$contractingfile->selectByParams(array(), $dsplyRange, $dsplyStart, $statement.$searchJson, $sOrder);
		// echo $contractingfile->query;exit;
		/* Output */
		$output = array(
			"sEcho" => intval($_GET['sEcho']),
			"iTotalRecords" => $allRecord,
			"iTotalDisplayRecords" => $allRecordFilter,
			"aaData" => array()
		);
		while($contractingfile->nextRow())
		{
			$row = array();
			for ( $i=0 ; $i<count($aColumns) ; $i++ )
			{
				$pemenangStr = '<span class="badge badge-danger">Belum Ditetapkan</span>';
				if ($aColumns[$i] == "PEMENANG") {
					if ($contractingfile->getField($aColumns[$i]) != '') {
						$pemenangStr = '<span class="badge badge-primary">Sudah Ditetapkan</span>';
					}
				}

				if($aColumns[$i] == "NAMA")
					$row[] = $contractingfile->getField($aColumns[$i]).'<br>
							 <small class="badge badge-info" style="font-size:9px; padding:3px 10px">'.$contractingfile->getField('PAKET_METODE_LELANG').'</small>
							 <small class="badge badge-primary" style="font-size:9px; padding:3px 10px">'.$contractingfile->getField('PENGGUNA_STR').'</small>
							 ';
				else if($aColumns[$i] == "KETERANGAN") 
					$row[] = truncate($contractingfile->getField($aColumns[$i]), 5)."...";
				else if($aColumns[$i] == "PEMENANG")
					$row[] = $pemenangStr;
				else if($aColumns[$i] == "NAMA")
					$row[] = strtoupper($contractingfile->getField($aColumns[$i]));
				else if($aColumns[$i] == "NILAI")
          			$row[] = numberToIna($contractingfile->getField($aColumns[$i]));
				else
					$row[] = $contractingfile->getField($aColumns[$i]);
			}

			$output['aaData'][] = $row;
		}

		echo json_encode( $output );
	}  

	function deleteFile()  // Done
	{
		$this->load->model('Contractingfile');
		
		$file	= new Contractingfile();
		
		$reqId		= $this->input->get('reqId');
		
		$file->setField("CONTRACTINGFILEID", $reqId);
		$file->delete();

		echo "Data berhasil dihapus.";
	}

	function publishFile() // Done
	{
		$this->load->model('Contractingfile');
		
		$file	= new Contractingfile();
		
		$reqId		= $this->input->get('reqId');
		$status		= $this->input->get('status');
		
		$file->setField("CONTRACTINGFILEID", $reqId);
		$file->setField("FILE_PUBLISH_PENYEDIA", $status);
		$file->publishFile();

		if ($status == '1')
			echo "Data berhasil dipublish.";
		else
			echo "Data berhasil di unpublish.";
	}

}
?>
