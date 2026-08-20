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

class contracting_penyedia_json extends CI_Controller {

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

	function contracting_paket()
	{
		$this->load->library("kauth");  $userLogin = new kauth();
		$this->load->model(array("Contractingpenyedia","Contractingrekanan"));

		$contractingpenyedia = new Contractingpenyedia();
		$reqSearch = $this->input->get("reqSearch");
		$getTahun = $_GET['tahun'];

		ini_set("memory_limit","500M");
		ini_set('max_execution_time', 5200);

		$aColumns = array("CONTRACTINGREKANANID", "NAMA","JNS_KONTRAK_STR","CR_SPPBJ_NILAI","CR_LEGAL_NOMOR_PKS","CONTRACTING_STATUS_KONTRAK");
		$aColumnsAlias = array("A.CONTRACTINGREKANANID", "A.NAMA","A.JNS_KONTRAK_STR","A.CR_SPPBJ_NILAI","CR_LEGAL_NOMOR_PKS","CONTRACTING_STATUS_KONTRAK");

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

			if ( trim($sOrder) == "ORDER BY A.PAKET_ID asc" )
			{
				$sOrder = " ORDER BY COALESCE(A.PAKET_ID, 0) DESC";

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

		switch ($this->USER_TYPE_ID) {
			case '6': // PENYEDIA
				$statement .= " AND A.REKANAN_ID = '".$this->REKANAN_ID."' ";
			break;

			default:
				echo "hahahahaaaaaa kamu ngapai disini"; die();
				break;
		}

		$allRecord = $contractingpenyedia->getCountByParams(array(),$statement);
		if($_GET['sSearch'] == "")
			$allRecordFilter = $allRecord;
		else
			$allRecordFilter = $contractingpenyedia->getCountByParams(array(), $statement.$searchJson);

		$contractingpenyedia->selectByParamsViewContracting(array(), $dsplyRange, $dsplyStart, $statement.$searchJson, $sOrder);
		// echo $contractingpenyedia->query;exit;
		/* Output */
		$output = array(
			"sEcho" => intval($_GET['sEcho']),
			"iTotalRecords" => $allRecord,
			"iTotalDisplayRecords" => $allRecordFilter,
			"aaData" => array()
		);
		while($contractingpenyedia->nextRow())
		{
			$row = array();
			for ( $i=0 ; $i<count($aColumns) ; $i++ )
			{

				$perubahan 	= '';
				$proses4 = new Contractingrekanan();
     			$proses4->selectProses4(array("A.CONTRACTINGREKANANID" => $contractingpenyedia->getField('CONTRACTINGREKANANID')));
				$proses4->firstRow();
				$reqPerubahanAlasan = $proses4->getField('CR_PERUBAHAN_ALASAN') ?: '';
				$reqKaharAlasan = $proses4->getField('CR_KAHAR_ALASAN') ?: '';
				$reqPemutusanAlasan = $proses4->getField('CR_PEMUTUSAN_ALASAN') ?: '';
				$reqDendaAlasan = $proses4->getField('CR_DENDA_ALASAN') ?: '';

				if ($reqPerubahanAlasan != '') {
					$perubahan .= '<small class="badge badge-danger" style="font-size:9px; padding:3px 10px; margin-right:2px"><i class="fa fa-check"></i>Perubanan Kontrak</small>';
				}

				if ($reqKaharAlasan != '') {
					$perubahan .= '<small class="badge badge-danger" style="font-size:9px; padding:3px 10px; margin-right:2px"><i class="fa fa-check"></i>Keadaan Kabar</small>';
				}

				if ($reqPemutusanAlasan != '') {
					$perubahan .= '<small class="badge badge-danger" style="font-size:9px; padding:3px 10px; margin-right:2px"><i class="fa fa-check"></i>Pemutusan Kontrak</small>';
				}

				if ($reqDendaAlasan != '') {
					$perubahan .= '<small class="badge badge-danger" style="font-size:9px; padding:3px 10px; margin-right:2px"><i class="fa fa-check"></i>Sanksi dan Denda</small>';
				}

				$kontrakKet = '';
				if ($aColumns[$i] == "CR_SPPBJ_CODE") {
					if ($contractingpenyedia->getField($aColumns[$i]) != '') {
						// $this->load->model("Contractingrekanan");
              			$spkpks = new Contractingrekanan();
						$spkpks->selectViewPKSSPK(array("A.CONTRACTINGREKANANID" => $contractingpenyedia->getField('CONTRACTINGREKANANID')));
              			$spkpks->firstRow();
              			$reqWaktuPelaksanaanDari = $spkpks->getField('CR_WAKTU_PELAKSANAAN_DARI') ?: '-';
             			$reqWaktuPelaksanaanSampai = $spkpks->getField('CR_WAKTU_PELAKSANAAN_SAMPAI') ?: '-';
             			$kontrakKet = getFormattedDateShort2(dateTimeToPageCheck($reqWaktuPelaksanaanDari)).' s/d<br>'.getFormattedDateShort2(dateTimeToPageCheck($reqWaktuPelaksanaanSampai));
					}
				}

				if($aColumns[$i] == "NAMA") {
					$row[] = $contractingpenyedia->getField($aColumns[$i]).'<br>
						'.$perubahan;
				}
				else if($aColumns[$i] == "CR_SPPBJ_NILAI") {
          			if ($contractingpenyedia->getField("CR_SPPBJ_NILAI")) {
	          			$row[] = numberToIna($contractingpenyedia->getField($aColumns[$i]));
          			} else {
	          			$row[] = numberToIna($contractingpenyedia->getField("CR_NILAI_KONTRAK"));
          			}
				}
          		else if($aColumns[$i] == "CR_SPPBJ_CODE")  {
          			$row[] = $kontrakKet;
          		}
				else {
					$row[] = $contractingpenyedia->getField($aColumns[$i]);
				}
			}

			$output['aaData'][] = $row;
		}

		echo json_encode( $output );
	}

}
?>
