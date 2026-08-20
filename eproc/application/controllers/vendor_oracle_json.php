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

class vendor_oracle_json extends CI_Controller {

	function __construct() {
		parent::__construct(); $this->db->query("SET TIME ZONE 'Asia/Jakarta'");

		//kauth
		if (!$this->kauth->getInstance()->hasIdentity())
		{
		}
		/* GLOBAL VARIABLE */
		$this->ID = $this->kauth->getInstance()->getIdentity()->REKANAN_ID;
	}


	function add()
	{
		$this->load->model('Vendororacle');
		$vendororacle	= new Vendororacle();

		$reqId		= $this->input->post('reqId');
    $reqMode	= $this->input->post('reqMode');
    $reqRekananId	= $this->input->post('reqRekananId');
		$reqJenis	= $this->input->post('reqJenis');

		$reqKodeOracle	= $this->input->post('reqKodeOracle');
    $reqCatatan	= $this->input->post('reqCatatan');

		if($reqMode == "insert")
		{
			$vendororacle	= new Vendororacle();
			$vendororacle->setField("REKANAN_ID", $reqRekananId);
			$vendororacle->setField("JENIS",$reqJenis);
			$vendororacle->setField("KODE_ORACLE",$reqKodeOracle);
      $vendororacle->setField("CATATAN",$reqCatatan);
			$vendororacle->setField('CREATED_BY', $this->USER_LOGIN_ID);
			$vendororacle->insert();

		}
		else
		{
			$vendororacle	= new Vendororacle();
			$vendororacle->setField("REKANAN_ORACLE_ID", $reqId);
      $vendororacle->setField("REKANAN_ID", $reqRekananId);
			$vendororacle->setField("JENIS",$reqJenis);
			$vendororacle->setField("KODE_ORACLE",$reqKodeOracle);
      $vendororacle->setField("CATATAN",$reqCatatan);
			$vendororacle->setField('CREATED_BY', $this->USER_LOGIN_ID);
			$vendororacle->update();
		}

		echo "Data berhasil disimpan.";
	}

  function json()
	{
		$this->load->model("Vendororacle");
		$vendororacle = new Vendororacle();

		$reqSearch = $this->input->post("reqSearch");

		$aColumns 			= array('REKANAN_ORACLE_ID','KODE_EPROC','KODE_ORACLE','NAMA','NPWP','ALAMAT','CATATAN');
		$aColumnsAlias		= array('REKANAN_ORACLE_ID','KODE_EPROC','KODE_ORACLE','NAMA','NPWP','ALAMAT','CATATAN');

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

			if ( trim($sOrder) == "ORDER BY REKANAN_ORACLE_ID desc" )
			{
				$sOrder = " ORDER BY REKANAN_ORACLE_ID ASC";

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
		$allRecord = $vendororacle->getCountByParams(array(), $statement, $sOrder);
		if($_GET['sSearch'] == "")
			$allRecordFilter = $allRecord;
		else
			$allRecordFilter =  $vendororacle->getCountByParams(array(), $statement, $sOrder);

		$vendororacle->selectByParams(array(), $dsplyRange, $dsplyStart, $statement, $sOrder);

		$output = array(
			"sEcho" => intval($_GET['sEcho']),
			"iTotalRecords" => $allRecord,
			"iTotalDisplayRecords" => $allRecordFilter,
			"aaData" => array()
		);

		while($vendororacle->nextRow())
		{
			$row = array();
			for ( $i=0 ; $i<count($aColumns) ; $i++ )
			{
				if($aColumns[$i]=='NO')		$row[] = $number;
        elseif($aColumns[$i]=='KODE_ORACLE'){
          if($vendororacle->getField('KODE_ORACLE') == '') {
            $row[] = '<span class="badge badge-danger">Belum diupdate</span>';
          } else {
            $row[] = $vendororacle->getField(trim($aColumns[$i]));
          }
        } else {
          $row[] = $vendororacle->getField(trim($aColumns[$i]));
        }
			}
			$output['aaData'][] = $row;
		}
		echo json_encode( $output );
	}



}
?>
