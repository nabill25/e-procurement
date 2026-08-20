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

class kontak_json extends CI_Controller {

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

	function json()
	{
		$this->load->model('Kontak');
		$kontak = new Kontak();

		$reqId = $this->input->get("reqId");

		$aColumns 			= array("KONTAK_ID", "NAMA", "TANGGAL", "EMAIL", "SUBYEK" , "IPADDRESS", "PESAN");
		$aColumnsAlias		= array("KONTAK_ID", "NAMA", "TANGGAL", "EMAIL", "SUBYEK" , "IPADDRESS", "PESAN");

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
					if (substr_compare(( $_GET['sSortDir_'.$i] ), "asc", 0) == 0)
					{
						$sOrder .=" asc, ";
					}else
					{
						$sOrder .=" desc, ";
					}
				}
			// echo $_GET['sSortDir_'.$i];
			}
			//Remove the last space / comma
			$sOrder = substr_replace( $sOrder, "", -2 );

			//Check if there is an order by clause
			if ( trim($sOrder) == "ORDER BY KONTAK_ID desc" )
			{
				/*
				* If there is no order by clause - ORDER BY INDEX COLUMN!!! DON'T DELETE IT!
				* If there is no order by clause there might be bugs in table display.
				* No order by clause means that the db is not responsible for the data ordering,
				* which means that the same row can be displayed in two pages - while
				* another row will not be displayed at all.
				*/
				$sOrder = " ORDER BY TANGGAL DESC";

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

		$statement = " AND ((UPPER(NAMA) LIKE '%".strtoupper($_GET['sSearch'])."%') OR (UPPER(PERUSAHAAN) LIKE '%".strtoupper($_GET['sSearch'])."%') OR (UPPER(SUBYEK) LIKE '%".strtoupper($_GET['sSearch'])."%'))";
		$allRecord = $kontak->getCountByParams(array(), $statement);
		if($_GET['sSearch'] == "")
			$allRecordFilter = $allRecord;
		else
			$allRecordFilter =  $kontak->getCountByParams(array(), $statement);

		$kontak->selectByParams(array(), $dsplyRange, $dsplyStart, $statement, $sOrder);

		$output = array(
			"sEcho" => intval($_GET['sEcho']),
			"iTotalRecords" => $allRecord,
			"iTotalDisplayRecords" => $allRecordFilter,
			"aaData" => array()
		);

		while($kontak->nextRow())
		{
			$row = array();
			for ( $i=0 ; $i<count($aColumns) ; $i++ )
			{
				if($aColumns[$i]=='NO')		$row[] = $number;
					elseif($aColumns[$i]=='TANGGAL' || $aColumns[$i]=='TANGGAL_MULAI' || $aColumns[$i]=='TANGGAL_AKHIR')	$row[] = getFormattedDateJson($kontak->getField(trim($aColumns[$i])));
					elseif($aColumns[$i]=='STATUS'){
						if( $kontak->getField(trim($aColumns[$i])) == 1)	$st = 'Berlaku';
						else												$st = 'Tidak Berlaku';
						$row[] = $st;
					}
					elseif($aColumns[$i]=='UNIT_KERJA')	$row[] = $kontak->getField(trim($aColumns[$i]))."*".$kontak->getField("SK_PANITIA_ID");
					else	$row[] = $kontak->getField(trim($aColumns[$i]));
			}
			$output['aaData'][] = $row;
		}
		echo json_encode( $output );
	}

	function set_status()
	{
		/* create objects */
		$this->load->model('Kontak');
		$kontak = new Kontak();

		$reqId = httpFilterGet("reqId");
		$reqNilai = httpFilterGet("reqNilai");

			$kontak->setField("FIELD", "STATUS");
			$kontak->setField("FIELD_VALUE", $reqNilai);
			$kontak->setField("KONTAK_ID", $reqId);
			$kontak->updateByField();

		$met = array();
		$i=0;

		$met[0]['STATUS'] = 1;
		echo json_encode($met);
	}

	function captcha()
	{
		// Begin the session
		session_start();

		// To avoid case conflicts, make the input uppercase and check against the session value
		// If it's correct, echo '1' as a string
		if(strtoupper($_GET['reqCaptcha']) == $_SESSION['captcha_id'])
			echo 'true';
		// Else echo '0' as a string
		else
			echo 'false';
	}

	function kontak_add()
	{
		$this->load->library("crfs_protect"); $csrf = new crfs_protect('_crfs_rlt');
		$this->load->model("Kontak");
		$this->load->model("Rekanan");
		$this->load->library("KMail");

		/* create objects */
		$kontak = new Kontak();
		$rekanan = new Rekanan();

		$reqId= $this->input->post("reqId");
		$reqTelepon = $this->input->post("reqTelepon");
		// $reqJenisPerusahaan = $this->input->post("reqJenisPerusahaan");
		$reqNamaPerusahaan = $this->input->post("reqNamaPerusahaan");
		$reqEmail = $this->input->post("reqEmail");
		$reqSubyek = $this->input->post("reqSubyek");
		$reqKeterangan = $this->input->post("reqKeterangan");
		$reqKirim = $this->input->post("reqKirim");
		$security_code = $this->input->post("security_code");

		if($reqKirim == 'Simpan')
		{
			if (!$csrf->isTokenValid($_POST['_crfs_rlt']))
				exit();
			// echo "string"; die();

			$kontak->setField('NAMA', $reqNamaPerusahaan);
			$kontak->setField('EMAIL', $reqEmail);
			$kontak->setField('TELEPON', $reqTelepon);
			$kontak->setField('SUBYEK', $reqSubyek);
			$kontak->setField('PESAN', $reqKeterangan);
			$kontak->setField('IPADDRESS', $_SERVER['REMOTE_ADDR']);
			$kontak->setField('STATUS', 1);
			if($kontak->insert())
			{
				$reqNama = "";
				// $reqJenisPerusahaan = "";
				$reqNamaPerusahaan = "";
				$reqEmail = "";
				$reqSubyek = "";
				$reqKeterangan = "";

				echo "Pesan anda telah kami terima, terima kasih";

			} else {
				echo "Pesan gagal dikirim, silahkan coba beberapa saat lagi!";
			}

		}
	}


}
?>
