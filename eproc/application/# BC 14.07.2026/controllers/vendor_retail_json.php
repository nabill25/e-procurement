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

class vendor_retail_json extends CI_Controller {

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
		$this->load->model('Vendorretail');

		$vendorretail	= new Vendorretail();

		$reqId		= $this->input->post('reqId');
		$reqMode	= $this->input->post('reqMode');
		$reqRekananTipe	= $this->input->post('reqRekananTipe');
    $reqNama	= $this->input->post('reqNama');
    $reqNPWP	= $this->input->post('reqNPWP');
    $reqTeleponKode	= $this->input->post('reqTeleponKode');
    $reqTelepon	= $this->input->post('reqTelepon');
    $reqWhatsapp	= $this->input->post('reqWhatsapp');
    $reqTanggalDaftar	= $this->input->post('reqTanggalDaftar');
    $reqKota	= $this->input->post('reqKota');
    $reqRegionId	= $this->input->post('reqRegionId');
    $reqKontakPerson	= $this->input->post('reqKontakPerson');
    $reqKontakPersonHP	= $this->input->post('reqKontakPersonHP');
    $reqAlamat	= $this->input->post('reqAlamat');

		if($reqMode == "insert")
		{
			$vendorretail	= new Vendorretail();
			$vendorretail->setField("REKANAN_TIPE_ID", $reqRekananTipe);
			$vendorretail->setField("NAMA",$reqNama); // Generate Kode
			$vendorretail->setField("NPWP",$reqNPWP);
      $vendorretail->setField("TELEPON_KODE",$reqTeleponKode);
			$vendorretail->setField("TELEPON",$reqTelepon);
			$vendorretail->setField("WHATSAPP", $reqWhatsapp);
      $vendorretail->setField("TANGGAL_DAFTAR",dateToDBCheck($reqTanggalDaftar));
      $vendorretail->setField("KOTA",$reqKota);
			$vendorretail->setField("REGION_ID",$reqRegionId);
      $vendorretail->setField("KONTAK_PERSON",$reqKontakPerson);
      $vendorretail->setField("KONTAK_PERSON_HP",$reqKontakPersonHP);
			$vendorretail->setField("ALAMAT",$reqAlamat);
			$vendorretail->setField('CREATED_BY', $this->USER_LOGIN_ID);
			$vendorretail->insert();

		}
		else
		{
			$vendorretail	= new Vendorretail();
			$vendorretail->setField("REKANAN_RETAIL_ID", $reqId);
      $vendorretail->setField("REKANAN_TIPE_ID", $reqRekananTipe);
			$vendorretail->setField("NAMA",$reqNama); // Generate Kode
			$vendorretail->setField("NPWP",$reqNPWP);
      $vendorretail->setField("TELEPON_KODE",$reqTeleponKode);
			$vendorretail->setField("TELEPON",$reqTelepon);
			$vendorretail->setField("WHATSAPP", $reqWhatsapp);
      $vendorretail->setField("TANGGAL_DAFTAR",dateToDBCheck($reqTanggalDaftar));
			$vendorretail->setField("REGION_ID",$reqRegionId);
      $vendorretail->setField("KONTAK_PERSON",$reqKontakPerson);
      $vendorretail->setField("KONTAK_PERSON_HP",$reqKontakPersonHP);
			$vendorretail->setField("ALAMAT",$reqAlamat);
			$vendorretail->setField('CREATED_BY', $this->USER_LOGIN_ID);
			$vendorretail->update();
		}

		echo "Data berhasil disimpan.";
	}

	function delete()
	{
		$this->load->model('Vendorretail');

		$vendorretail	= new Vendorretail();

		$reqId		= $this->input->get('reqId');
		$reqNama		= $this->input->post('reqNama');

		$vendorretail	= new Vendorretail();
		$vendorretail->setField("REKANAN_RETAIL_ID", $reqId);
		$vendorretail->delete();

		echo "Data berhasil disimpan.";
	}

	function combo()
	{
		$this->load->model('Vendorretail');
		$vendorretail = new Vendorretail();

		$vendorretail->selectByParams();

		$i = 0;
		while($vendorretail->nextRow())
		{
			$arr_json[$i]['id']		= $vendorretail->getField("REKANAN_RETAIL_ID");
			$arr_json[$i]['text']	= $vendorretail->getField("NAMA");
			$i++;
		}

		echo json_encode($arr_json);
	}

  function json()
	{
		$this->load->model("Vendorretail");
		$vendorretail = new Vendorretail();

		$reqSearch = $this->input->post("reqSearch");

		$aColumns 			= array('REKANAN_RETAIL_ID','NAMA','NPWP','ALAMAT','KONTAK_PERSON','KONTAK_PERSON_HP');
		$aColumnsAlias		= array('REKANAN_RETAIL_ID','NAMA','NPWP','ALAMAT','KONTAK_PERSON','KONTAK_PERSON_HP');

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

			if ( trim($sOrder) == "ORDER BY REKANAN_RETAIL_ID desc" )
			{
				$sOrder = " ORDER BY REKANAN_RETAIL_ID ASC";

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
		$allRecord = $vendorretail->getCountByParams(array(), $statement, $sOrder);
		if($_GET['sSearch'] == "")
			$allRecordFilter = $allRecord;
		else
			$allRecordFilter =  $vendorretail->getCountByParams(array(), $statement, $sOrder);

		$vendorretail->selectByParams(array(), $dsplyRange, $dsplyStart, $statement, $sOrder);

		$output = array(
			"sEcho" => intval($_GET['sEcho']),
			"iTotalRecords" => $allRecord,
			"iTotalDisplayRecords" => $allRecordFilter,
			"aaData" => array()
		);

		while($vendorretail->nextRow())
		{
			$row = array();
			for ( $i=0 ; $i<count($aColumns) ; $i++ )
			{
				if($aColumns[$i]=='NO')		$row[] = $number;
        elseif($aColumns[$i]=='KONTAK_PERSON'){
          $row[] = $vendorretail->getField('KONTAK_PERSON') .'<br>'.$vendorretail->getField('KONTAK_PERSON_HP');
        } else {
          $row[] = $vendorretail->getField(trim($aColumns[$i]));
        }
			}
			$output['aaData'][] = $row;
		}
		echo json_encode( $output );
	}



}
?>
