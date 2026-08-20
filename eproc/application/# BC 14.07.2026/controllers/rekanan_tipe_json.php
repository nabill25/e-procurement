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

class rekanan_tipe_json extends CI_Controller {

	function __construct() {
		parent::__construct(); $this->db->query("SET TIME ZONE 'Asia/Jakarta'");

		//kauth
		if (!$this->kauth->getInstance()->hasIdentity())
		{
			// trow to unauthenticated page!
			//redirect('Login');
		}

		/* GLOBAL VARIABLE */
		$this->ID = isset($this->kauth->getInstance()->getIdentity()->REKANAN_ID)?$this->kauth->getInstance()->getIdentity()->REKANAN_ID:'';
	}

	function combo()
	{
		$this->load->model('RekananTipe');
		$rekanan_tipe = new RekananTipe();

		$rekanan_tipe->selectByParams(array('STATUS' => '1'));

		$i = 0;
		while($rekanan_tipe->nextRow())
		{
			$arr_json[$i]['id']		= $rekanan_tipe->getField("REKANAN_TIPE_ID");
			$arr_json[$i]['text']	= $rekanan_tipe->getField("NAMA");
			$i++;
		}

		echo json_encode($arr_json);
	}

	function comboJenis()
	{
		$this->load->model('RekananSertifikatJenis');
		$sertifikat_jenis = new RekananSertifikatJenis();

		$sertifikat_jenis->selectByParams(array(),-1,-1," ORDER BY NAMA ASC");

		$i = 0;
		while($sertifikat_jenis->nextRow())
		{
			if ($sertifikat_jenis->getField("ALIAS")) {
				$alias = ' - '.$sertifikat_jenis->getField("ALIAS");
			} else {
				$alias = '';
			}
			$arr_json[$i]['id']		= $sertifikat_jenis->getField("REKANAN_SERTIFIKAT_JENIS_ID");
			$arr_json[$i]['text']	= $sertifikat_jenis->getField("NAMA").' '.$alias;
			$i++;
		}

		echo json_encode($arr_json);
	}

	function add()
	{
		$this->load->model('RekananTipe');

		$rekanan_tipe	= new RekananTipe();

		$reqId		= $this->input->post('reqId');
		$reqMode	= $this->input->post('reqMode');
		$reqNama		= $this->input->post('reqNama');
		$reqStatus		= $this->input->post('reqStatus');

		if($reqMode == "insert")
		{
			$rekanan_tipe->setField("NAMA", $reqNama);
			$rekanan_tipe->setField("STATUS", $reqStatus);
			$rekanan_tipe->setField("CREATED_BY", $this->USER_LOGIN_ID);
			$rekanan_tipe->insert();
			echo "Data berhasil disimpan.";
		}
		else
		{
			$rekanan_tipe->setField("NAMA", $reqNama);
			$rekanan_tipe->setField("STATUS", $reqStatus);
			$rekanan_tipe->setField("REKANAN_TIPE_ID", $reqId);
			$rekanan_tipe->setField("CREATED_BY", $this->USER_LOGIN_ID);
			if($rekanan_tipe->update()){
				echo "Data berhasil diubah.";
			} else {
				echo "Data gagal diubah, silahkan dicoba kembali.";
			}
		}

	}

	function delete()
	{
		$this->load->model('RekananTipe');

		$rekanan_tipe	= new RekananTipe();

		$reqId		= $this->input->get('reqId');

		$rekanan_tipe	= new RekananTipe();
		$rekanan_tipe->setField("REKANAN_TIPE_ID", $reqId);
		if($rekanan_tipe->delete()) {
			echo "Data berhasil dihapus.";
		} else {
			echo "Data gagal dihapus.";
		}

	}

	function json()
	{
		$this->load->model("RekananTipe");
		$rekanan_tipe = new RekananTipe();

		$aColumns 			= array('REKANAN_TIPE_ID', 'NAMA', 'STATUS');
		$aColumnsAlias		= array('REKANAN_TIPE_ID', 'NAMA', 'STATUS');

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

			if ( trim($sOrder) == "ORDER BY REKANAN_TIPE_ID desc" )
			{
				$sOrder = " ORDER BY REKANAN_TIPE_ID ASC";

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

		$statement = " AND (UPPER(NAMA) LIKE '%".strtoupper($_GET['sSearch'])."%')";
		$allRecord = $rekanan_tipe->getCountByParams(array(), $statement, $sOrder);
		if($_GET['sSearch'] == "")
			$allRecordFilter = $allRecord;
		else
			$allRecordFilter =  $rekanan_tipe->getCountByParams(array(), $statement, $sOrder);

		$rekanan_tipe->selectByParams(array(), $dsplyRange, $dsplyStart, $statement, $sOrder);

		$output = array(
			"sEcho" => intval($_GET['sEcho']),
			"iTotalRecords" => $allRecord,
			"iTotalDisplayRecords" => $allRecordFilter,
			"aaData" => array()
		);

		while($rekanan_tipe->nextRow())
		{
			$row = array();
			for ( $i=0 ; $i<count($aColumns) ; $i++ )
			{
				if($aColumns[$i]=='NO')		$row[] = $number;
					elseif($aColumns[$i]=='STATUS'){
						if( $rekanan_tipe->getField(trim($aColumns[$i])) == 1)	$st = '<span class="badge badge-primary">Aktif</span>';
						else												$st = '<span class="badge badge-danger">Non Aktif</span>';
						$row[] = $st;
					}
					else	$row[] = $rekanan_tipe->getField(trim($aColumns[$i]));
			}
			$output['aaData'][] = $row;
		}
		echo json_encode( $output );
	}

}
?>
