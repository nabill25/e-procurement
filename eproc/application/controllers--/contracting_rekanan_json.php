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

class contracting_rekanan_json extends CI_Controller {

	function __construct() {
		parent::__construct(); $this->db->query("SET TIME ZONE 'Asia/Jakarta'");

		//kauth
		if (!$this->kauth->getInstance()->hasIdentity()) { }

		$this->ID = $this->kauth->getInstance()->getIdentity()->REKANAN_ID;
		$this->USER_LOGIN_ID =  $this->kauth->getInstance()->getIdentity()->USER_LOGIN_ID;
		$this->USER_LOGIN =  $this->kauth->getInstance()->getIdentity()->USER_LOGIN;
		$this->USER_NAMA =  $this->kauth->getInstance()->getIdentity()->USER_NAMA;
		$this->USER_TYPE_ID =  $this->kauth->getInstance()->getIdentity()->USER_TYPE_ID;
		$this->LEGAL =  $this->kauth->getInstance()->getIdentity()->LEGAL;
		$this->REKANAN_ID =  $this->kauth->getInstance()->getIdentity()->REKANAN_ID;
		$this->UNIT_KERJA_ID =  $this->kauth->getInstance()->getIdentity()->UNIT_KERJA_ID;
		$this->LEVEL_KONTRAK =  $this->kauth->getInstance()->getIdentity()->LEVEL_KONTRAK;
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
		$this->load->model("Contracting");
		$this->load->model(array("Contractingrekanan","Contractingpayment"));

		$contracting = new Contracting();
		$reqSearch = $this->input->get("reqSearch");
      	$getTahun = $this->session->userdata('setTahunKontrak');
		$reqProses= $this->input->get("reqProses");

		ini_set("memory_limit","500M");
		ini_set('max_execution_time', 5200);

		$aColumns = array("PAKET_ID","CONTRACTINGREKANANID","NAMA", "PAKET_METODE_LELANG","PAKET_METODE_LELANG_ID","PENGGUNA_STR","PEMENANG","CR_SPPBJ_NILAI","TAHUN_SPPBJ","CONTRACTINGREKANANID","JNS_KONTRAK","PIC_KONTRAK_STR","CONTRACTINGPROSESID_STR","CONTRACTING_STATUS_KONTRAK");
		$aColumnsAlias = array("A.PAKET_ID","CONTRACTINGREKANANID","A.NAMA", "A.PAKET_METODE_LELANG","PAKET_METODE_LELANG_ID", "A.PENGGUNA_STR","PEMENANG","CR_SPPBJ_NILAI","TAHUN_SPPBJ","CONTRACTINGREKANANID","JNS_KONTRAK","PIC_KONTRAK_STR","CONTRACTINGPROSESID_STR","CONTRACTING_STATUS_KONTRAK");

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

		if ($this->LEGAL == '1') { // PENG. KONTRAK BAGIAN LEGAL, PEMERIKSA
			$statement = "AND (UPPER(NAMA) LIKE '%".strtoupper($_GET['sSearch'])."%')";
		} else {
			if ($this->USER_TYPE_ID == '20') { // KASUBDIT
				$statement = "AND (UPPER(NAMA) LIKE '%".strtoupper($_GET['sSearch'])."%') AND A.JNS_KONTRAK IN ('0','1','3')  AND CONTRACTINGPROSESID IN ('1','2')";
			} else {
				// $statement = "AND (UPPER(NAMA) LIKE '%".strtoupper($_GET['sSearch'])."%') AND A.PPK = '".$this->USER_LOGIN_ID."' ";
				$statement = "AND (UPPER(NAMA) LIKE '%".strtoupper($_GET['sSearch'])."%') ";
			}
		}

		if ($this->USER_TYPE_ID == '12' || $this->USER_TYPE_ID == '20')
		{ // 12 Pengelola Kontrak, 20 Pemeriksa Kontrak
			switch ($this->LEVEL_KONTRAK) {
				case '1': // Staff
					$getProses = '1';
					// $statement .= " AND CONTRACTINGPROSESID IN ('1','2','3','4','5') ";
					if ($this->PENUNJUK_PIC == '1') { // KASI
						$statement .= " AND CONTRACTINGPROSESID IN ('1','2') ";
					} else {
						$statement .= " AND CONTRACTINGPROSESID IN ('1','2') AND PIC_KONTRAK = '".$this->USER_LOGIN_ID."' ";
					}
					break;

				case '2': // Pengendali
					$getProses = '1';
					$statement .= " AND CONTRACTINGPROSESID IN ('3','4','5') ";
					// code...
					break;

				case '3': // Penyelesai
					$getProses = '1';
					$statement .= " AND CONTRACTINGPROSESID IN ('3','4','5') ";
					break;
				
				default:
					// code...
					break;
			} 
			// if ($reqProses == '1') {
			// 	$getProses = '1';
			// 	$statement .= " AND CONTRACTINGPROSESID IN ('0','1') ";
			// } else if ($reqProses == '2') {
			// 	$getProses = '2';
			// 	$statement .= " AND CONTRACTINGPROSESID = '2' ";
			// } else if ($reqProses == '3') {
			// 	$getProses = '3';
			// 	$statement .= " AND CONTRACTINGPROSESID = '3' ";
			// } else if ($reqProses == '4') {
			// 	$getProses = '4';
			// 	$statement .= " AND CONTRACTINGPROSESID in ('3','4') ";
			// } else if ($reqProses == '5') {
			// 	$getProses = '5';
			// 	$statement .= " AND CONTRACTINGPROSESID = '5' ";
			// } else if ($reqProses == '6') {
			// 	$getProses = '6';
			// 	$statement .= " AND CONTRACTINGPROSESID = '6' ";
			// }

		} else if ($this->USER_TYPE_ID == '28') { // PPK
			$statement .= " AND CONTRACTINGPROSESID IN ('0','1','2') ";
			// $statement .= " AND CONTRACTINGPROSESID IN ('0','1') ";
		} else if ($this->USER_TYPE_ID == '') {
			echo "hahahahaaaaaa kamu ngapain disini"; die();
		}

		if ($getTahun == 'all' || $getTahun == '') {
		} else {
			$statement .= " AND A.TAHUN_SPPBJ::text[] && array['".$getTahun."'] ";
		}
		
		$statement .= " AND PIC_KONTRAK IS NOT NULL ";

		$allRecord = $contracting->getCountByParams(array(),$statement);
		if($_GET['sSearch'] == "")
			$allRecordFilter = $allRecord;
		else
			$allRecordFilter = $contracting->getCountByParams(array(), $statement.$searchJson);

		$contracting->selectByParamsViewContracting(array(), $dsplyRange, $dsplyStart, $statement.$searchJson, $sOrder);
		// echo $contracting->query;exit;
		/* Output */
		$output = array(
			"sEcho" => intval($_GET['sEcho']),
			"iTotalRecords" => $allRecord,
			"iTotalDisplayRecords" => $allRecordFilter,
			"aaData" => array()
		);

		while($contracting->nextRow())
		{
			$row = array();
			for ( $i=0 ; $i<count($aColumns) ; $i++ )
			{
				$pemenangStr = '';
				if ($aColumns[$i] == "PEMENANG") {
					if ($contracting->getField($aColumns[$i]) != '') {
						$this->load->model("Rekanan");
						$rekanan = new Rekanan();
						$val = $contracting->getField($aColumns[$i]);
						$val = str_replace(['{', '}'], ['(', ')'], $val);
						$rekanan->selectByParams(array("A.REKANAN_ID|| IN" => $val));
						$rekanan->firstRow();
						$pemenangStr = $rekanan->getField('NAMA');
					}
				}

				$perubahan 	= '';
				 

				if ($reqProses == '3') {
					$proses4 = new Contractingrekanan();
	     			$proses4->selectProses4(array("A.CONTRACTINGREKANANID" => $contracting->getField('CONTRACTINGREKANANID')));
					$proses4->firstRow();
					$reqPerubahanAlasan = $proses4->getField('CR_PERUBAHAN_ALASAN') ?: '';
					$reqKaharAlasan = $proses4->getField('CR_KAHAR_ALASAN') ?: '';
					$reqPemutusanAlasan = $proses4->getField('CR_PEMUTUSAN_ALASAN') ?: '';
					$reqDendaAlasan = $proses4->getField('CR_DENDA_ALASAN') ?: '';

					if ($reqPerubahanAlasan != '') {
						$perubahan .= '<small class="badge badge-danger" style="font-size:9px; padding:3px 10px; margin-right:2px"><i class="fa fa-check"></i>Perubahan Kontrak</small>';
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
				}

				if($aColumns[$i] == "NAMA") {
					$this->load->library("libgeneratecode");
                    $libgeneratecode = new libgeneratecode();
    	            $noPaket = '<small style="font-size: 10px;"><i> No. Paket: '.$libgeneratecode->nomorPaket($contracting->getField("PAKET_ID"),$contracting->getField('PAKET_METODE_LELANG_ID')).'</i></small><br>';
					 
					// $row[] = '<a href="kontrak/index/contracting_detail?reqId='.$contracting->getField('CONTRACTINGREKANANID').'&reqProses='.$getProses.'">'.$contracting->getField($aColumns[$i]).'<br>
					// 	<small class="badge badge-info" style="font-size:9px; padding:3px 10px">'.$contracting->getField('PAKET_METODE_LELANG').'</small>
					// 	<small class="badge badge-primary" style="font-size:9px; padding:3px 10px">'.$contracting->getField('PENGGUNA_STR').'</small></a><br>
					// 	'.$perubahan;

					$row[] = $contracting->getField($aColumns[$i]).'<br>
						<small class="badge badge-info" style="font-size:9px; padding:3px 10px">'.$contracting->getField('PAKET_METODE_LELANG').'</small>
						<small class="badge badge-primary" style="font-size:9px; padding:3px 10px">'.$contracting->getField('PENGGUNA_STR').'</small><br>
						'.$perubahan;

				} else if($aColumns[$i] == "TAHUN_SPPBJ") {
					$tglBAST = '';
					$proses5 = new Contractingrekanan();
                  	$proses5->selectProses5(array("A.PAKET_ID" => $contracting->getField('PAKET_ID')));
                  	while($proses5->nextRow())
					{
						$tglBAST .= getFormattedDate($proses5->getField("CR_BAST_PEKERJAAN_TANGGAL")).'<br>' ?: '-';
					}
					$row[] = $tglBAST;
				} else if($aColumns[$i] == "KETERANGAN") {
					$row[] = truncate($contracting->getField($aColumns[$i]), 5)."...";
				} else if($aColumns[$i] == "CONTRACTINGPROSESID_STR") {
					if ($contracting->getField("CONTRACTINGPROSESID") == '1' || $contracting->getField("CONTRACTINGPROSESID") == '2') {
						$row[] = 'Persiapan';
					} else if ($contracting->getField("CONTRACTINGPROSESID") == '3') {
						$row[] = 'Pengendalian';
					} if ($contracting->getField("CONTRACTINGPROSESID") == '4' || $contracting->getField("CONTRACTINGPROSESID") == '5') {
						$row[] = 'Penyelesaian';
					}
				}
				else if($aColumns[$i] == "PEMENANG") {
					$row[] = $pemenangStr;
				}
				else if($aColumns[$i] == "JNS_KONTRAK") {
					$row[] = $this->libkontrak->jenisKontrak($contracting->getField($aColumns[$i]));
				}
				else if($aColumns[$i] == "NAMA") {
					$row[] = strtoupper($contracting->getField($aColumns[$i]));
				}
				else if($aColumns[$i] == "NILAI") {
          			$row[] = numberToIna($contracting->getField($aColumns[$i]));
				}
          		else if($aColumns[$i] == "CR_NILAI_KONTRAK") {
          			$row[] = numberToIna($contracting->getField($aColumns[$i]));
          		}
          		else if($aColumns[$i] == "CR_SPPBJ_NILAI") {
          			if ($contracting->getField("CR_SPPBJ_NILAI")) {
	          			$row[] = numberToIna($contracting->getField($aColumns[$i]));
          			} else {
	          			$row[] = numberToIna($contracting->getField("CR_NILAI_KONTRAK"));
          			}
          			
          		}
          		else if($aColumns[$i] == "PENGGUNA_STR") {
          			$row[] = $kontrakKet;
          		}
				else {
					$row[] = $contracting->getField($aColumns[$i]);
				}
			}

			$output['aaData'][] = $row;
		}

		echo json_encode( $output );
	}

	function jsonPengendalian()
	{
		$this->load->library("kauth");  $userLogin = new kauth();
		$this->load->model("Contracting");
		$this->load->model(array("Contractingrekanan","Contractingpayment","Userlogin"));

		$contracting = new Contracting();
		$reqSearch = $this->input->get("reqSearch");
      	$getTahun = $this->session->userdata('setTahunKontrak');
		$reqProses= $this->input->get("reqProses");

		ini_set("memory_limit","500M");
		ini_set('max_execution_time', 5200);

		$aColumns = array("PAKET_ID","CONTRACTINGREKANANID","NAMA", "PAKET_METODE_LELANG","PAKET_METODE_LELANG_ID","PENGGUNA_STR","PEMENANG","CR_SPPBJ_NILAI","TAHUN_SPPBJ","CONTRACTINGREKANANID","JNS_KONTRAK","PIC_PENGENDALI","CONTRACTINGPROSESID_STR","CONTRACTING_STATUS_KONTRAK");
		$aColumnsAlias = array("A.PAKET_ID","CONTRACTINGREKANANID","A.NAMA", "A.PAKET_METODE_LELANG","PAKET_METODE_LELANG_ID", "A.PENGGUNA_STR","PEMENANG","CR_SPPBJ_NILAI","TAHUN_SPPBJ","CONTRACTINGREKANANID","JNS_KONTRAK","PIC_PENGENDALI","CONTRACTINGPROSESID_STR","CONTRACTING_STATUS_KONTRAK");

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

		if ($this->LEGAL == '1') { // PENG. KONTRAK BAGIAN LEGAL, PEMERIKSA
			$statement = "AND (UPPER(NAMA) LIKE '%".strtoupper($_GET['sSearch'])."%')";
		} else {
			if ($this->USER_TYPE_ID == '20') { // KASUBDIT
				$statement = "AND (UPPER(NAMA) LIKE '%".strtoupper($_GET['sSearch'])."%') AND A.JNS_KONTRAK IN ('0','1','3')  AND CONTRACTINGPROSESID IN ('3','4','5')";
			} else {
				// $statement = "AND (UPPER(NAMA) LIKE '%".strtoupper($_GET['sSearch'])."%') AND A.PPK = '".$this->USER_LOGIN_ID."' ";
				$statement = "AND (UPPER(NAMA) LIKE '%".strtoupper($_GET['sSearch'])."%') ";
			}
		}

		if ($this->USER_TYPE_ID == '12' || $this->USER_TYPE_ID == '20')
		{ // 12 Pengelola Kontrak, 20 Pemeriksa Kontrak
			switch ($this->LEVEL_KONTRAK) {
				case '1': // Staff
					if ($this->PENUNJUK_PIC == '1') { // KASI
						// $statement .= " AND CONTRACTINGPROSESID IN ('3','4','5') AND (CR_PERUBAHAN = '1' OR CR_PEMUTUSAN = '1') ";
						$statement .= " AND CONTRACTINGPROSESID IN ('3','4','5') ";
					} else {
						// $statement .= " AND CONTRACTINGPROSESID IN ('3','4','5') AND PIC_KONTRAK = '".$this->USER_LOGIN_ID."' AND (CR_PERUBAHAN = '1' OR CR_PEMUTUSAN = '1') ";
						$statement .= " AND CONTRACTINGPROSESID IN ('3','4','5') AND PIC_KONTRAK = '".$this->USER_LOGIN_ID."' ";
					}
					// $statement .= " AND CONTRACTINGPROSESID IN ('1','2','3','4','5') ";
					break;

				case '2': // Pengendali
					if ($this->PENUNJUK_PIC == '1') { // KASI
						$statement .= " AND CONTRACTINGPROSESID IN ('3','4','5')";
					} else {
						$statement .= " AND CONTRACTINGPROSESID IN ('3','4','5') AND PIC_PENGENDALI = ".$this->USER_LOGIN_ID." ";
					}
					// code...
					break;

				case '3': // Penyelesai
					$statement .= " AND CONTRACTINGPROSESID IN ('3','4','5') ";
					break;
				
				default:
					// code...
					break;
			}  
		} else if ($this->USER_TYPE_ID == '28') { // PPK
			$statement .= " AND CONTRACTINGPROSESID IN ('3','4','5') ";
			// $statement .= " AND CONTRACTINGPROSESID IN ('0','1') ";
		} else if ($this->USER_TYPE_ID == '') {
			echo "hahahahaaaaaa kamu ngapain disini"; die();
		}

		if ($getTahun == 'all' || $getTahun == '') {
		} else {
			$statement .= " AND A.TAHUN_SPPBJ::text[] && array['".$getTahun."'] ";
		}
		
		$statement .= " AND PIC_KONTRAK IS NOT NULL ";

		$allRecord = $contracting->getCountByParams(array(),$statement);
		if($_GET['sSearch'] == "")
			$allRecordFilter = $allRecord;
		else
			$allRecordFilter = $contracting->getCountByParams(array(), $statement.$searchJson);

		$contracting->selectByParamsViewContracting(array(), $dsplyRange, $dsplyStart, $statement.$searchJson, $sOrder);
		// echo $contracting->query;exit;
		/* Output */
		$output = array(
			"sEcho" => intval($_GET['sEcho']),
			"iTotalRecords" => $allRecord,
			"iTotalDisplayRecords" => $allRecordFilter,
			"aaData" => array()
		);

		while($contracting->nextRow())
		{
			$row = array();
			for ( $i=0 ; $i<count($aColumns) ; $i++ )
			{
				$pemenangStr = '';
				if ($aColumns[$i] == "PEMENANG") {
					if ($contracting->getField($aColumns[$i]) != '') {
						$this->load->model("Rekanan");
						$rekanan = new Rekanan();
						$val = $contracting->getField($aColumns[$i]);
						$val = str_replace(['{', '}'], ['(', ')'], $val);
						$rekanan->selectByParams(array("A.REKANAN_ID|| IN" => $val));
						$rekanan->firstRow();
						$pemenangStr = $rekanan->getField('NAMA');
					}
				}

				$perubahan 	= '';
				 

				if ($reqProses == '3') {
					$proses4 = new Contractingrekanan();
	     			$proses4->selectProses4(array("A.CONTRACTINGREKANANID" => $contracting->getField('CONTRACTINGREKANANID')));
					$proses4->firstRow();
					$reqPerubahanAlasan = $proses4->getField('CR_PERUBAHAN_ALASAN') ?: '';
					$reqKaharAlasan = $proses4->getField('CR_KAHAR_ALASAN') ?: '';
					$reqPemutusanAlasan = $proses4->getField('CR_PEMUTUSAN_ALASAN') ?: '';
					$reqDendaAlasan = $proses4->getField('CR_DENDA_ALASAN') ?: '';

					if ($reqPerubahanAlasan != '') {
						$perubahan .= '<small class="badge badge-danger" style="font-size:9px; padding:3px 10px; margin-right:2px"><i class="fa fa-check"></i>Perubahan Kontrak</small>';
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
				}

				if($aColumns[$i] == "NAMA") {
					$this->load->library("libgeneratecode");
                    $libgeneratecode = new libgeneratecode();
    	            $noPaket = '<small style="font-size: 10px;"><i> No. Paket: '.$libgeneratecode->nomorPaket($contracting->getField("PAKET_ID"),$contracting->getField('PAKET_METODE_LELANG_ID')).'</i></small><br>';
					 
					// $row[] = '<a href="kontrak/index/contracting_detail?reqId='.$contracting->getField('CONTRACTINGREKANANID').'&reqProses='.$getProses.'">'.$contracting->getField($aColumns[$i]).'<br>
					// 	<small class="badge badge-info" style="font-size:9px; padding:3px 10px">'.$contracting->getField('PAKET_METODE_LELANG').'</small>
					// 	<small class="badge badge-primary" style="font-size:9px; padding:3px 10px">'.$contracting->getField('PENGGUNA_STR').'</small></a><br>
					// 	'.$perubahan;

					$row[] = $contracting->getField($aColumns[$i]).'<br>
						<small class="badge badge-info" style="font-size:9px; padding:3px 10px">'.$contracting->getField('PAKET_METODE_LELANG').'</small>
						<small class="badge badge-primary" style="font-size:9px; padding:3px 10px">'.$contracting->getField('PENGGUNA_STR').'</small><br>
						'.$perubahan;

				} else if($aColumns[$i] == "TAHUN_SPPBJ") {
					$tglBAST = '';
					$proses5 = new Contractingrekanan();
                  	$proses5->selectProses5(array("A.PAKET_ID" => $contracting->getField('PAKET_ID')));
                  	while($proses5->nextRow())
					{
						$tglBAST .= getFormattedDate($proses5->getField("CR_BAST_PEKERJAAN_TANGGAL")).'<br>' ?: '-';
					}
					$row[] = $tglBAST;
				} else if($aColumns[$i] == "KETERANGAN") {
					$row[] = truncate($contracting->getField($aColumns[$i]), 5)."...";
				} else if($aColumns[$i] == "CONTRACTINGPROSESID_STR") {
					if ($contracting->getField("CONTRACTINGPROSESID") == '1' || $contracting->getField("CONTRACTINGPROSESID") == '2') {
						$row[] = 'Persiapan';
					} else if ($contracting->getField("CONTRACTINGPROSESID") == '3') {
						$row[] = 'Pengendalian';
					} if ($contracting->getField("CONTRACTINGPROSESID") == '4' || $contracting->getField("CONTRACTINGPROSESID") == '5') {
						$row[] = 'Penyelesaian';
					}
				}
				else if($aColumns[$i] == "PEMENANG") {
					$row[] = $pemenangStr;
				}
				else if($aColumns[$i] == "PIC_PENGENDALI") {
					if ($contracting->getField("PIC_PENGENDALI")) {
						$userLoginAkun = new Userlogin();
						$userLoginAkun->selectByParams(array("USER_LOGIN_ID" => $contracting->getField("PIC_PENGENDALI")));
						$userLoginAkun->firstRow();
						$row[] = $userLoginAkun->getField("USER_NAMA");
					} else {
						$row[] = '-';
					}

				}
				else if($aColumns[$i] == "JNS_KONTRAK") {
					$row[] = $this->libkontrak->jenisKontrak($contracting->getField($aColumns[$i]));
				}
				else if($aColumns[$i] == "NAMA") {
					$row[] = strtoupper($contracting->getField($aColumns[$i]));
				}
				else if($aColumns[$i] == "NILAI") {
          			$row[] = numberToIna($contracting->getField($aColumns[$i]));
				}
          		else if($aColumns[$i] == "CR_NILAI_KONTRAK") {
          			$row[] = numberToIna($contracting->getField($aColumns[$i]));
          		}
          		else if($aColumns[$i] == "CR_SPPBJ_NILAI") {
          			if ($contracting->getField("CR_SPPBJ_NILAI")) {
	          			$row[] = numberToIna($contracting->getField($aColumns[$i]));
          			} else {
	          			$row[] = numberToIna($contracting->getField("CR_NILAI_KONTRAK"));
          			}
          			
          		}
          		else if($aColumns[$i] == "PENGGUNA_STR") {
          			$row[] = $kontrakKet;
          		}
				else {
					$row[] = $contracting->getField($aColumns[$i]);
				}
			}

			$output['aaData'][] = $row;
		}

		echo json_encode( $output );
	}

	function jsonPenyelesaian()
	{
		$this->load->library("kauth");  $userLogin = new kauth();
		$this->load->model("Contracting");
		$this->load->model(array("Contractingrekanan","Contractingpayment","Userlogin"));

		$contracting = new Contracting();
		$reqSearch = $this->input->get("reqSearch");
      	$getTahun = $this->session->userdata('setTahunKontrak');
		$reqProses= $this->input->get("reqProses");

		ini_set("memory_limit","500M");
		ini_set('max_execution_time', 5200);

		$aColumns = array("PAKET_ID","CONTRACTINGREKANANID","NAMA", "PAKET_METODE_LELANG","PAKET_METODE_LELANG_ID","PENGGUNA_STR","PEMENANG","CR_SPPBJ_NILAI","TAHUN_SPPBJ","CONTRACTINGREKANANID","JNS_KONTRAK","PIC_PENYELESAIAN","CONTRACTINGPROSESID_STR","CONTRACTING_STATUS_KONTRAK");
		$aColumnsAlias = array("A.PAKET_ID","CONTRACTINGREKANANID","A.NAMA", "A.PAKET_METODE_LELANG","PAKET_METODE_LELANG_ID", "A.PENGGUNA_STR","PEMENANG","CR_SPPBJ_NILAI","TAHUN_SPPBJ","CONTRACTINGREKANANID","JNS_KONTRAK","PIC_PENYELESAIAN","CONTRACTINGPROSESID_STR","CONTRACTING_STATUS_KONTRAK");

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

		if ($this->LEGAL == '1') { // PENG. KONTRAK BAGIAN LEGAL, PEMERIKSA
			$statement = "AND (UPPER(NAMA) LIKE '%".strtoupper($_GET['sSearch'])."%')";
		} else {
			if ($this->USER_TYPE_ID == '20') { // KASUBDIT
				$statement = "AND (UPPER(NAMA) LIKE '%".strtoupper($_GET['sSearch'])."%') AND A.JNS_KONTRAK IN ('0','1','3')  AND CONTRACTINGPROSESID IN ('3','4','5')";
			} else {
				// $statement = "AND (UPPER(NAMA) LIKE '%".strtoupper($_GET['sSearch'])."%') AND A.PPK = '".$this->USER_LOGIN_ID."' ";
				$statement = "AND (UPPER(NAMA) LIKE '%".strtoupper($_GET['sSearch'])."%') ";
			}
		}

		if ($this->USER_TYPE_ID == '12' || $this->USER_TYPE_ID == '20')
		{ // 12 Pengelola Kontrak, 20 Pemeriksa Kontrak
			switch ($this->LEVEL_KONTRAK) {
				case '1': // Staff
					// $statement .= " AND CONTRACTINGPROSESID IN ('1','2','3','4','5') ";
					$statement .= " AND CONTRACTINGPROSESID IN ('3','4','5') ";
					break;

				case '2': // Pengendali
					if ($this->PENUNJUK_PIC == '1') { // KASI
						$statement .= " AND CONTRACTINGPROSESID IN ('3','4','5')";
					} else {
						$statement .= " AND CONTRACTINGPROSESID IN ('3','4','5') AND PIC_PENGENDALI = ".$this->USER_LOGIN_ID." ";
					}
					// code...
					break;

				case '3': // Penyelesai
					$statement .= " AND CONTRACTINGPROSESID IN ('3','4','5') ";
					break;
				
				default:
					// code...
					break;
			}  
		} else if ($this->USER_TYPE_ID == '28') { // PPK
			$statement .= " AND CONTRACTINGPROSESID IN ('3','4','5') ";
			// $statement .= " AND CONTRACTINGPROSESID IN ('0','1') ";
		} else if ($this->USER_TYPE_ID == '') {
			echo "hahahahaaaaaa kamu ngapain disini"; die();
		}

		if ($getTahun == 'all' || $getTahun == '') {
		} else {
			$statement .= " AND A.TAHUN_SPPBJ::text[] && array['".$getTahun."'] ";
		}
		
		$statement .= " AND PIC_KONTRAK IS NOT NULL ";

		$allRecord = $contracting->getCountByParams(array(),$statement);
		if($_GET['sSearch'] == "")
			$allRecordFilter = $allRecord;
		else
			$allRecordFilter = $contracting->getCountByParams(array(), $statement.$searchJson);

		$contracting->selectByParamsViewContracting(array(), $dsplyRange, $dsplyStart, $statement.$searchJson, $sOrder);
		// echo $contracting->query;exit;
		/* Output */
		$output = array(
			"sEcho" => intval($_GET['sEcho']),
			"iTotalRecords" => $allRecord,
			"iTotalDisplayRecords" => $allRecordFilter,
			"aaData" => array()
		);

		while($contracting->nextRow())
		{
			$row = array();
			for ( $i=0 ; $i<count($aColumns) ; $i++ )
			{
				$pemenangStr = '';
				if ($aColumns[$i] == "PEMENANG") {
					if ($contracting->getField($aColumns[$i]) != '') {
						$this->load->model("Rekanan");
						$rekanan = new Rekanan();
						$val = $contracting->getField($aColumns[$i]);
						$val = str_replace(['{', '}'], ['(', ')'], $val);
						$rekanan->selectByParams(array("A.REKANAN_ID|| IN" => $val));
						$rekanan->firstRow();
						$pemenangStr = $rekanan->getField('NAMA');
					}
				}

				$perubahan 	= '';
				 

				if ($reqProses == '3') {
					$proses4 = new Contractingrekanan();
	     			$proses4->selectProses4(array("A.CONTRACTINGREKANANID" => $contracting->getField('CONTRACTINGREKANANID')));
					$proses4->firstRow();
					$reqPerubahanAlasan = $proses4->getField('CR_PERUBAHAN_ALASAN') ?: '';
					$reqKaharAlasan = $proses4->getField('CR_KAHAR_ALASAN') ?: '';
					$reqPemutusanAlasan = $proses4->getField('CR_PEMUTUSAN_ALASAN') ?: '';
					$reqDendaAlasan = $proses4->getField('CR_DENDA_ALASAN') ?: '';

					if ($reqPerubahanAlasan != '') {
						$perubahan .= '<small class="badge badge-danger" style="font-size:9px; padding:3px 10px; margin-right:2px"><i class="fa fa-check"></i>Perubahan Kontrak</small>';
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
				}

				if($aColumns[$i] == "NAMA") {
					$this->load->library("libgeneratecode");
                    $libgeneratecode = new libgeneratecode();
    	            $noPaket = '<small style="font-size: 10px;"><i> No. Paket: '.$libgeneratecode->nomorPaket($contracting->getField("PAKET_ID"),$contracting->getField('PAKET_METODE_LELANG_ID')).'</i></small><br>';
					 
					// $row[] = '<a href="kontrak/index/contracting_detail?reqId='.$contracting->getField('CONTRACTINGREKANANID').'&reqProses='.$getProses.'">'.$contracting->getField($aColumns[$i]).'<br>
					// 	<small class="badge badge-info" style="font-size:9px; padding:3px 10px">'.$contracting->getField('PAKET_METODE_LELANG').'</small>
					// 	<small class="badge badge-primary" style="font-size:9px; padding:3px 10px">'.$contracting->getField('PENGGUNA_STR').'</small></a><br>
					// 	'.$perubahan;

					$row[] = $contracting->getField($aColumns[$i]).'<br>
						<small class="badge badge-info" style="font-size:9px; padding:3px 10px">'.$contracting->getField('PAKET_METODE_LELANG').'</small>
						<small class="badge badge-primary" style="font-size:9px; padding:3px 10px">'.$contracting->getField('PENGGUNA_STR').'</small><br>
						'.$perubahan;

				} else if($aColumns[$i] == "TAHUN_SPPBJ") {
					$tglBAST = '';
					$proses5 = new Contractingrekanan();
                  	$proses5->selectProses5(array("A.PAKET_ID" => $contracting->getField('PAKET_ID')));
                  	while($proses5->nextRow())
					{
						$tglBAST .= getFormattedDate($proses5->getField("CR_BAST_PEKERJAAN_TANGGAL")).'<br>' ?: '-';
					}
					$row[] = $tglBAST;
				} else if($aColumns[$i] == "KETERANGAN") {
					$row[] = truncate($contracting->getField($aColumns[$i]), 5)."...";
				} else if($aColumns[$i] == "CONTRACTINGPROSESID_STR") {
					if ($contracting->getField("CONTRACTINGPROSESID") == '1' || $contracting->getField("CONTRACTINGPROSESID") == '2') {
						$row[] = 'Persiapan';
					} else if ($contracting->getField("CONTRACTINGPROSESID") == '3') {
						$row[] = 'Pengendalian';
					} if ($contracting->getField("CONTRACTINGPROSESID") == '4' || $contracting->getField("CONTRACTINGPROSESID") == '5') {
						$row[] = 'Penyelesaian';
					}
				}
				else if($aColumns[$i] == "PEMENANG") {
					$row[] = $pemenangStr;
				}
				else if($aColumns[$i] == "PIC_PENYELESAIAN") {
					if ($contracting->getField("PIC_PENYELESAIAN")) {
						$userLoginAkun = new Userlogin();
						$userLoginAkun->selectByParams(array("USER_LOGIN_ID" => $contracting->getField("PIC_PENYELESAIAN")));
						$userLoginAkun->firstRow();
						$row[] = $userLoginAkun->getField("USER_NAMA");
					} else {
						$row[] = '-';
					}

				}
				else if($aColumns[$i] == "JNS_KONTRAK") {
					$row[] = $this->libkontrak->jenisKontrak($contracting->getField($aColumns[$i]));
				}
				else if($aColumns[$i] == "NAMA") {
					$row[] = strtoupper($contracting->getField($aColumns[$i]));
				}
				else if($aColumns[$i] == "NILAI") {
          			$row[] = numberToIna($contracting->getField($aColumns[$i]));
				}
          		else if($aColumns[$i] == "CR_NILAI_KONTRAK") {
          			$row[] = numberToIna($contracting->getField($aColumns[$i]));
          		}
          		else if($aColumns[$i] == "CR_SPPBJ_NILAI") {
          			if ($contracting->getField("CR_SPPBJ_NILAI")) {
	          			$row[] = numberToIna($contracting->getField($aColumns[$i]));
          			} else {
	          			$row[] = numberToIna($contracting->getField("CR_NILAI_KONTRAK"));
          			}
          			
          		}
          		else if($aColumns[$i] == "PENGGUNA_STR") {
          			$row[] = $kontrakKet;
          		}
				else {
					$row[] = $contracting->getField($aColumns[$i]);
				}
			}

			$output['aaData'][] = $row;
		}

		echo json_encode( $output );
	}

	function selesai()
	{
		$this->load->library("kauth");  $userLogin = new kauth();
		$this->load->model("Contracting");
		$this->load->model(array("Contractingrekanan","Contractingpayment","Userlogin"));

		$contracting = new Contracting();
		$reqSearch = $this->input->get("reqSearch");
      	$getTahun = $this->session->userdata('setTahunKontrak');
		$reqProses= $this->input->get("reqProses");

		ini_set("memory_limit","500M");
		ini_set('max_execution_time', 5200);

		$aColumns = array("PAKET_ID","CONTRACTINGREKANANID","NAMA", "PAKET_METODE_LELANG","PAKET_METODE_LELANG_ID","PENGGUNA_STR","PEMENANG","CR_SPPBJ_NILAI","TAHUN_SPPBJ","CONTRACTINGREKANANID","JNS_KONTRAK","PIC_PENGENDALI","CONTRACTINGPROSESID_STR","CONTRACTING_STATUS_KONTRAK","GRADE");
		$aColumnsAlias = array("A.PAKET_ID","CONTRACTINGREKANANID","A.NAMA", "A.PAKET_METODE_LELANG","PAKET_METODE_LELANG_ID", "A.PENGGUNA_STR","PEMENANG","CR_SPPBJ_NILAI","TAHUN_SPPBJ","CONTRACTINGREKANANID","JNS_KONTRAK","PIC_PENGENDALI","CONTRACTINGPROSESID_STR","CONTRACTING_STATUS_KONTRAK","GRADE");

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

		if ($this->LEGAL == '1') { // PENG. KONTRAK BAGIAN LEGAL, PEMERIKSA
			$statement = " AND (UPPER(NAMA) LIKE '%".strtoupper($_GET['sSearch'])."%')";
		} else {
			if ($this->USER_TYPE_ID == '20') { // KASUBDIT
				$statement .= " AND (UPPER(NAMA) LIKE '%".strtoupper($_GET['sSearch'])."%') AND A.JNS_KONTRAK IN ('0','1','3')  AND CONTRACTINGPROSESID IN ('6')";
			} else {
				// $statement = "AND (UPPER(NAMA) LIKE '%".strtoupper($_GET['sSearch'])."%') AND A.PPK = '".$this->USER_LOGIN_ID."' ";
				$statement = "AND (UPPER(NAMA) LIKE '%".strtoupper($_GET['sSearch'])."%') ";
			}
		}

		if ($this->USER_TYPE_ID == '12')
		{ // 12 Pengelola Kontrak, 20 Pemeriksa Kontrak
			switch ($this->LEVEL_KONTRAK) {
				case '1': // Staff
					// $statement .= " AND CONTRACTINGPROSESID IN ('1','2','3','4','5') ";
					$statement .= " AND CONTRACTINGPROSESID IN ('6') ";
					break;

				case '2': // Pengendali
					if ($this->PENUNJUK_PIC == '1') { // KASI
						$statement .= " AND CONTRACTINGPROSESID IN ('6')";
					} else {
						$statement .= " AND CONTRACTINGPROSESID IN ('6') AND PIC_PENGENDALI = ".$this->USER_LOGIN_ID." ";
					}
					// code...
					break;

				case '3': // Penyelesai
					$statement .= " AND CONTRACTINGPROSESID IN ('6') ";
					break;
				
				default:
					// code...
					break;
			}  
		} else if ($this->USER_TYPE_ID == '28') { // PPK
			$statement .= " AND CONTRACTINGPROSESID IN ('6') ";
			// $statement .= " AND CONTRACTINGPROSESID IN ('0','1') ";
		} else if ($this->USER_TYPE_ID == '') {
			echo "hahahahaaaaaa kamu ngapain disini"; die();
		}

		if ($getTahun == 'all' || $getTahun == '') {
		} else {
			$statement .= " AND A.TAHUN_SPPBJ::text[] && array['".$getTahun."'] ";
		}
		
		$statement .= " AND PIC_KONTRAK IS NOT NULL ";

		$allRecord = $contracting->getCountByParams(array(),$statement);
		if($_GET['sSearch'] == "")
			$allRecordFilter = $allRecord;
		else
			$allRecordFilter = $contracting->getCountByParams(array(), $statement.$searchJson);

		$contracting->selectByParamsViewContracting(array(), $dsplyRange, $dsplyStart, $statement.$searchJson, $sOrder);
		// echo $contracting->query;exit;
		/* Output */
		$output = array(
			"sEcho" => intval($_GET['sEcho']),
			"iTotalRecords" => $allRecord,
			"iTotalDisplayRecords" => $allRecordFilter,
			"aaData" => array()
		);

		while($contracting->nextRow())
		{
			$row = array();
			for ( $i=0 ; $i<count($aColumns) ; $i++ )
			{
				$pemenangStr = '';
				if ($aColumns[$i] == "PEMENANG") {
					if ($contracting->getField($aColumns[$i]) != '') {
						$this->load->model("Rekanan");
						$rekanan = new Rekanan();
						$val = $contracting->getField($aColumns[$i]);
						$val = str_replace(['{', '}'], ['(', ')'], $val);
						$rekanan->selectByParams(array("A.REKANAN_ID|| IN" => $val));
						$rekanan->firstRow();
						$pemenangStr = $rekanan->getField('NAMA');
					}
				}

				$perubahan 	= '';
				 

				$proses4 = new Contractingrekanan();
     			$proses4->selectProses4(array("A.CONTRACTINGREKANANID" => $contracting->getField('CONTRACTINGREKANANID')));
				$proses4->firstRow();
				$reqPerubahanAlasan = $proses4->getField('CR_PERUBAHAN_ALASAN') ?: '';
				$reqKaharAlasan = $proses4->getField('CR_KAHAR_ALASAN') ?: '';
				$reqPemutusanAlasan = $proses4->getField('CR_PEMUTUSAN_ALASAN') ?: '';
				$reqDendaAlasan = $proses4->getField('CR_DENDA_ALASAN') ?: '';

				if ($reqPerubahanAlasan != '') {
					$perubahan .= '<small class="badge badge-danger" style="font-size:9px; padding:3px 10px; margin-right:2px"><i class="fa fa-check"></i>Perubahan Kontrak</small>';
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

				if($aColumns[$i] == "NAMA") {
					$this->load->library("libgeneratecode");
                    $libgeneratecode = new libgeneratecode();
    	            $noPaket = '<small style="font-size: 10px;"><i> No. Paket: '.$libgeneratecode->nomorPaket($contracting->getField("PAKET_ID"),$contracting->getField('PAKET_METODE_LELANG_ID')).'</i></small><br>';
					 
					$row[] = $contracting->getField($aColumns[$i]).'<br>
						<small class="badge badge-info" style="font-size:9px; padding:3px 10px">'.$contracting->getField('PAKET_METODE_LELANG').'</small> <br>
						'.$perubahan;

				} else if($aColumns[$i] == "TAHUN_SPPBJ") {
					$tglBAST = '';
					$proses5 = new Contractingrekanan();
                  	$proses5->selectProses5(array("A.PAKET_ID" => $contracting->getField('PAKET_ID')));
                  	while($proses5->nextRow())
					{
						$tglBAST .= getFormattedDate($proses5->getField("CR_BAST_PEKERJAAN_TANGGAL")).'<br>' ?: '-';
					}
					$row[] = $tglBAST;
				} else if($aColumns[$i] == "KETERANGAN") {
					$row[] = truncate($contracting->getField($aColumns[$i]), 5)."...";
				} else if($aColumns[$i] == "GRADE") {
					$row[] = $contracting->getField($aColumns[$i]).'<br>('.round($contracting->getField('TOTAL_SKOR'),2).') <br><div class="stars read-only" data-rating="'.round($contracting->getField('TOTAL_SKOR'),0).'"></div>';
				} else if($aColumns[$i] == "CONTRACTINGPROSESID_STR") {
					if ($contracting->getField("CONTRACTINGPROSESID") == '1' || $contracting->getField("CONTRACTINGPROSESID") == '2') {
						$row[] = 'Persiapan';
					} else if ($contracting->getField("CONTRACTINGPROSESID") == '3') {
						$row[] = 'Pengendalian';
					} else if ($contracting->getField("CONTRACTINGPROSESID") == '4' || $contracting->getField("CONTRACTINGPROSESID") == '5') {
						$row[] = 'Penyelesaian';
					} else {
						$row[] = 'Selesai';
					}
				}
				else if($aColumns[$i] == "PEMENANG") {
					$row[] = $pemenangStr;
				}
				else if($aColumns[$i] == "PIC_PENGENDALI") {
					if ($contracting->getField("PIC_PENGENDALI")) {
						$userLoginAkun = new Userlogin();
						$userLoginAkun->selectByParams(array("USER_LOGIN_ID" => $contracting->getField("PIC_PENGENDALI")));
						$userLoginAkun->firstRow();
						$row[] = $userLoginAkun->getField("USER_NAMA");
					} else {
						$row[] = '-';
					}

				}
				else if($aColumns[$i] == "JNS_KONTRAK") {
					$row[] = $this->libkontrak->jenisKontrak($contracting->getField($aColumns[$i]));
				}
				else if($aColumns[$i] == "NAMA") {
					$row[] = strtoupper($contracting->getField($aColumns[$i]));
				}
				else if($aColumns[$i] == "NILAI") {
          			$row[] = numberToIna($contracting->getField($aColumns[$i]));
				}
          		else if($aColumns[$i] == "CR_NILAI_KONTRAK") {
          			$row[] = numberToIna($contracting->getField($aColumns[$i]));
          		}
          		else if($aColumns[$i] == "CR_SPPBJ_NILAI") {
          			if ($contracting->getField("CR_SPPBJ_NILAI")) {
	          			$row[] = numberToIna($contracting->getField($aColumns[$i]));
          			} else {
	          			$row[] = numberToIna($contracting->getField("CR_NILAI_KONTRAK"));
          			}
          			
          		}
          		else if($aColumns[$i] == "PENGGUNA_STR") {
          			$row[] = $kontrakKet;
          		}
				else {
					$row[] = $contracting->getField($aColumns[$i]);
				}
			}

			$output['aaData'][] = $row;
		}

		echo json_encode( $output );
	}

	function jsonlegal()
	{
		$this->load->library("kauth");  $userLogin = new kauth();
		$this->load->model("Contracting");
		$this->load->model("Contractingrekanan");

		$contracting = new Contracting();
		$reqSearch = $this->input->get("reqSearch");
  		$getTahun = $this->session->userdata('setTahunKontrak');
		// $reqProses= $this->input->get("reqProses");
		// $reqProses = $_GET['reqProses'];
		$reqProses= $this->input->get("reqProses");

		ini_set("memory_limit","500M");
		ini_set('max_execution_time', 5200);

		$aColumns = array("PAKET_ID", "NAMA", "PAKET_METODE_LELANG","PENGGUNA_STR","PEMENANG","STATUS");
		$aColumnsAlias = array("A.PAKET_ID", "A.NAMA", "A.PAKET_METODE_LELANG", "A.PENGGUNA_STR","PEMENANG","STATUS");

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
			if ( trim($sOrder) == "ORDER BY A.CONTRACTINGPROSESID asc" )
			{
				$sOrder = " ORDER BY COALESCE(A.CONTRACTINGPROSESID, 0) DESC";

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

				$sWhere .= $aColumnsAlias[$i]." LIKE '%' || :whereSpecificParam".$sWhereSpecificArrayCount." || '%' ";
				$sWhereSpecificArrayCount++;
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

		if ($this->USER_TYPE_ID == '12' || $this->USER_TYPE_ID == '20') { // 12 Pengelola Kontrak, 20 Pemeriksa Kontrak
			$getProses = '';
			$statement .= " AND CONTRACTINGPROSESID IN ('1','2','3','4','5','6')";
		} else if ($this->USER_TYPE_ID == '') {
			echo "hahahahaaaaaa kamu ngapai disini"; die();
		}

		$allRecord = $contracting->getCountByParams(array(),$statement);
		if($_GET['sSearch'] == "")
			$allRecordFilter = $allRecord;
		else
			$allRecordFilter = $contracting->getCountByParams(array(), $statement.$searchJson);

		$contracting->selectByParamsViewContracting(array(), $dsplyRange, $dsplyStart, $statement.$searchJson, $sOrder);
		// echo $contracting->query;exit;
		/* Output */
		$output = array(
			"sEcho" => intval($_GET['sEcho']),
			"iTotalRecords" => $allRecord,
			"iTotalDisplayRecords" => $allRecordFilter,
			"aaData" => array()
		);

		while($contracting->nextRow())
		{
			$row = array();
			for ( $i=0 ; $i<count($aColumns) ; $i++ )
			{
				$pemenangStr = '';
				if ($aColumns[$i] == "PEMENANG") {
					if ($contracting->getField($aColumns[$i]) != '') {
						$this->load->model("Rekanan");
						$rekanan = new Rekanan();
						// $rekanan->selectByParams(array("REKANAN_ID" => $contracting->getField($aColumns[$i])));
						$val = $contracting->getField($aColumns[$i]);
						$val = str_replace(['{', '}'], ['(', ')'], $val);
						$rekanan->selectByParams(array("A.REKANAN_ID|| IN" => $val));

						$rekanan->firstRow();
						$pemenangStr = $rekanan->getField('NAMA');
					}
				}

				$perubahan 	= '';
				if ($aColumns[$i] == "PENGGUNA_STR") {
					if ($contracting->getField($aColumns[$i]) != '') {
						// $this->load->model("Contractingrekanan");
              			$spkpks = new Contractingrekanan();
						$spkpks->selectViewPKSSPK(array("A.CONTRACTINGREKANANID" => $contracting->getField('CONTRACTINGREKANANID')));
              			$spkpks->firstRow();
              			$reqWaktuPelaksanaanDari = $spkpks->getField('CR_WAKTU_PELAKSANAAN_DARI') ?: '-';
             			$reqWaktuPelaksanaanSampai = $spkpks->getField('CR_WAKTU_PELAKSANAAN_SAMPAI') ?: '-';
             			$kontrakKet = getFormattedDateShort2(dateTimeToPageCheck($reqWaktuPelaksanaanDari)).' s/d<br>'.getFormattedDateShort2(dateTimeToPageCheck($reqWaktuPelaksanaanSampai));
					}
				}

				$proses4 = new Contractingrekanan();
     		$proses4->selectProses4(array("A.CONTRACTINGREKANANID" => $contracting->getField('CONTRACTINGREKANANID')));
				$proses4->firstRow();
				$reqPerubahanAlasan = $proses4->getField('CR_PERUBAHAN_ALASAN') ?: '';
				$reqKaharAlasan = $proses4->getField('CR_KAHAR_ALASAN') ?: '';
				$reqPemutusanAlasan = $proses4->getField('CR_PEMUTUSAN_ALASAN') ?: '';
				$reqDendaAlasan = $proses4->getField('CR_DENDA_ALASAN') ?: '';

				if ($reqPerubahanAlasan != '') {
					$perubahan .= '<small class="badge badge-danger" style="font-size:9px; padding:3px 10px; margin-right:2px"><i class="fa fa-check"></i>Perubahan Kontrak</small>';
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

				// get status kontrak
				$prosesStatus = new Contractingrekanan();
				$prosesStatus->selectByParams(array("A.CONTRACTINGREKANANID" => $contracting->getField('CONTRACTINGREKANANID')));
				$prosesStatus->firstRow();

				if($aColumns[$i] == "NAMA")
					$row[] = '<a href="kontrak/index/contracting_detaillegal?reqId='.$contracting->getField('CONTRACTINGREKANANID').'&reqProses='.$getProses.'">'.$contracting->getField($aColumns[$i]).'<br>
						<small class="badge badge-info" style="font-size:9px; padding:3px 10px">'.$contracting->getField('PAKET_METODE_LELANG').'</small>
						<small class="badge badge-primary" style="font-size:9px; padding:3px 10px">'.$contracting->getField('PENGGUNA_STR').'</small></a><br>
						'.$perubahan;
				else if($aColumns[$i] == "STATUS")
					$row[] = $prosesStatus->getField("CONTRACTINGPROSESID_STR");
				else if($aColumns[$i] == "KETERANGAN")
					$row[] = truncate($contracting->getField($aColumns[$i]), 5)."...";
				else if($aColumns[$i] == "PEMENANG")
					$row[] = $pemenangStr;
				else if($aColumns[$i] == "NAMA")
					$row[] = strtoupper($contracting->getField($aColumns[$i]));
				else if($aColumns[$i] == "NILAI")
          			$row[] = numberToIna($contracting->getField($aColumns[$i]));
          		else if($aColumns[$i] == "PENGGUNA_STR")
          			$row[] = $kontrakKet;
				else
					$row[] = $contracting->getField($aColumns[$i]);
			}

			$output['aaData'][] = $row;
		}

		echo json_encode( $output );
	}

	function jsonSelesai()
	{
		$this->load->library("kauth");  $userLogin = new kauth();
		$this->load->model("Contracting");
		$this->load->model("Contractingrekanan");

		$contracting = new Contracting();
		$reqSearch = $this->input->get("reqSearch");
      	$getTahun = $this->session->userdata('setTahunKontrak');

		ini_set("memory_limit","500M");
		ini_set('max_execution_time', 5200);

		$aColumns = array("PAKET_ID", "NAMA", "CR_NILAI_KONTRAK","JNS_KONTRAK");
		$aColumnsAlias = array("A.PAKET_ID", "A.NAMA", "A.CR_NILAI_KONTRAK","JNS_KONTRAK");

		/*
		 * Ordering
		 */
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


		if ($this->LEGAL == '1') { // PENG. KONTRAK BAGIAN LEGAL, PEMERIKSA
			$statement = "AND (UPPER(NAMA) LIKE '%".strtoupper($_GET['sSearch'])."%')";
		} else {
			if ($this->USER_TYPE_ID == '20') {
				$statement = "AND (UPPER(NAMA) LIKE '%".strtoupper($_GET['sSearch'])."%') AND A.JNS_KONTRAK IN ('0','1','3')";
			} else {
				$statement = "AND (UPPER(NAMA) LIKE '%".strtoupper($_GET['sSearch'])."%') AND A.PPK = '".$this->USER_LOGIN_ID."' ";
			}
		}

		if ($this->USER_TYPE_ID == '12' || $this->USER_TYPE_ID == '20')
		{ // 12 Pengelola Kontrak, 20 Pemeriksa Kontrak

			$getProses = '6';
			$statement .= " AND CONTRACTINGPROSESID = '6' ";

			if ($getTahun == 'all' || $getTahun == '') {
			} else {
				$statement .= " AND A.TAHUN_SPPBJ::text[] && array['".$getTahun."'] ";
			}
		} else if ($this->USER_TYPE_ID == '') {
			echo "hahahahaaaaaa kamu ngapain disini"; die();
		}

		$allRecord = $contracting->getCountByParams(array(),$statement);
		if($_GET['sSearch'] == "")
			$allRecordFilter = $allRecord;
		else
			$allRecordFilter = $contracting->getCountByParams(array(), $statement.$searchJson);

		$contracting->selectByParamsViewContracting(array(), $dsplyRange, $dsplyStart, $statement.$searchJson, $sOrder);
		// echo $contracting->query;exit;
		/* Output */
		$output = array(
			"sEcho" => intval($_GET['sEcho']),
			"iTotalRecords" => $allRecord,
			"iTotalDisplayRecords" => $allRecordFilter,
			"aaData" => array()
		);

		while($contracting->nextRow())
		{
			$row = array();
			for ( $i=0 ; $i<count($aColumns) ; $i++ )
			{
				$pemenangStr = '';
				if ($aColumns[$i] == "PEMENANG") {
					if ($contracting->getField($aColumns[$i]) != '') {
						$this->load->model("Rekanan");
						$rekanan = new Rekanan();
						// $rekanan->selectByParams(array("REKANAN_ID" => $contracting->getField($aColumns[$i])));
						$val = $contracting->getField($aColumns[$i]);
						$val = str_replace(['{', '}'], ['(', ')'], $val);
						$rekanan->selectByParams(array("A.REKANAN_ID|| IN" => $val));

						$rekanan->firstRow();
						$pemenangStr = $rekanan->getField('NAMA');
					}
				}

				$perubahan 	= '';
				if ($aColumns[$i] == "PENGGUNA_STR") {
					if ($contracting->getField($aColumns[$i]) != '') {
						// $this->load->model("Contractingrekanan");
              			$spkpks = new Contractingrekanan();
						$spkpks->selectViewPKSSPK(array("A.CONTRACTINGREKANANID" => $contracting->getField('CONTRACTINGREKANANID')));
              			$spkpks->firstRow();
              			$reqWaktuPelaksanaanDari = $spkpks->getField('CR_WAKTU_PELAKSANAAN_DARI') ?: '-';
             			$reqWaktuPelaksanaanSampai = $spkpks->getField('CR_WAKTU_PELAKSANAAN_SAMPAI') ?: '-';
             			$kontrakKet = getFormattedDateShort2(dateTimeToPageCheck($reqWaktuPelaksanaanDari)).' s/d<br>'.getFormattedDateShort2(dateTimeToPageCheck($reqWaktuPelaksanaanSampai));
					}
				}


				if($aColumns[$i] == "NAMA")
					$row[] = '<a target="_blank" href="kontrak/index/contracting_detail?reqId='.$contracting->getField('CONTRACTINGREKANANID').'&reqProses='.$getProses.'">'.$contracting->getField($aColumns[$i]).'<br>
						<small class="badge badge-info" style="font-size:9px; padding:3px 10px">'.$contracting->getField('PAKET_METODE_LELANG').'</small>
						<small class="badge badge-primary" style="font-size:9px; padding:3px 10px">'.$contracting->getField('PENGGUNA_STR').'</small></a><br>
						'.$perubahan;
				else if($aColumns[$i] == "KETERANGAN")
					$row[] = truncate($contracting->getField($aColumns[$i]), 5)."...";
				else if($aColumns[$i] == "PEMENANG")
					$row[] = $pemenangStr;
				else if($aColumns[$i] == "JNS_KONTRAK")
					$row[] = $this->libkontrak->jenisKontrak($contracting->getField($aColumns[$i]));
				else if($aColumns[$i] == "NAMA")
					$row[] = strtoupper($contracting->getField($aColumns[$i]));
				else if($aColumns[$i] == "CR_NILAI_KONTRAK")
          			$row[] = numberToIna($contracting->getField($aColumns[$i]));
          		else if($aColumns[$i] == "PENGGUNA_STR")
          			$row[] = $kontrakKet;
				else
					$row[] = $contracting->getField($aColumns[$i]);
			}

			$output['aaData'][] = $row;
		}

		echo json_encode( $output );
	}

	function jsonDashboard()
	{
		$this->load->library("kauth");  $userLogin = new kauth();
		$this->load->model("Contracting");
		$this->load->model(array("Contractingrekanan","Contractingpayment","Userlogin"));

		$contracting = new Contracting();
		$reqSearch = $this->input->get("reqSearch");
      	$getTahun = $this->session->userdata('setTahunKontrak');
		$reqProses= $this->input->get("reqProses");

		ini_set("memory_limit","500M");
		ini_set('max_execution_time', 5200);

		$aColumns = array("PAKET_ID","CONTRACTINGREKANANID","NAMA", "PAKET_METODE_LELANG","PAKET_METODE_LELANG_ID","PENGGUNA_STR","PEMENANG","CR_SPPBJ_NILAI","TAHUN_SPPBJ","CONTRACTINGREKANANID","JNS_KONTRAK","PIC_PENGENDALI","CONTRACTINGPROSESID_STR","CONTRACTING_STATUS_KONTRAK");
		$aColumnsAlias = array("A.PAKET_ID","CONTRACTINGREKANANID","A.NAMA", "A.PAKET_METODE_LELANG","PAKET_METODE_LELANG_ID", "A.PENGGUNA_STR","PEMENANG","CR_SPPBJ_NILAI","TAHUN_SPPBJ","CONTRACTINGREKANANID","JNS_KONTRAK","PIC_PENGENDALI","CONTRACTINGPROSESID_STR","CONTRACTING_STATUS_KONTRAK");

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

		if ($this->USER_TYPE_ID != '') { // PPK
			$statement .= " AND CONTRACTINGPROSESID IN ('0','1','2','3','4','5','6') ";
			// $statement .= " AND CONTRACTINGPROSESID IN ('0','1') ";
		} else if ($this->USER_TYPE_ID == '') {
			echo "hahahahaaaaaa kamu ngapain disini"; die();
		}

		if ($getTahun == 'all' || $getTahun == '') {
		} else {
			$statement .= " AND A.TAHUN = '".$getTahun."' ";
		}
		
		// $statement .= " AND PIC_KONTRAK IS NOT NULL ";

		$allRecord = $contracting->getCountByParams(array(),$statement);
		if($_GET['sSearch'] == "")
			$allRecordFilter = $allRecord;
		else
			$allRecordFilter = $contracting->getCountByParams(array(), $statement.$searchJson);

		$contracting->selectByParamsViewContracting(array(), $dsplyRange, $dsplyStart, $statement.$searchJson, $sOrder);
		// echo $contracting->query;exit;
		/* Output */
		$output = array(
			"sEcho" => intval($_GET['sEcho']),
			"iTotalRecords" => $allRecord,
			"iTotalDisplayRecords" => $allRecordFilter,
			"aaData" => array()
		);

		while($contracting->nextRow())
		{
			$row = array();
			for ( $i=0 ; $i<count($aColumns) ; $i++ )
			{
				$pemenangStr = '';
				if ($aColumns[$i] == "PEMENANG") {
					if ($contracting->getField($aColumns[$i]) != '') {
						$this->load->model("Rekanan");
						$rekanan = new Rekanan();
						$val = $contracting->getField($aColumns[$i]);
						$val = str_replace(['{', '}'], ['(', ')'], $val);
						$rekanan->selectByParams(array("A.REKANAN_ID|| IN" => $val));
						$rekanan->firstRow();
						$pemenangStr = $rekanan->getField('NAMA');
					}
				}

				$perubahan 	= '';
				 

				if ($reqProses == '3') {
					$proses4 = new Contractingrekanan();
	     			$proses4->selectProses4(array("A.CONTRACTINGREKANANID" => $contracting->getField('CONTRACTINGREKANANID')));
					$proses4->firstRow();
					$reqPerubahanAlasan = $proses4->getField('CR_PERUBAHAN_ALASAN') ?: '';
					$reqKaharAlasan = $proses4->getField('CR_KAHAR_ALASAN') ?: '';
					$reqPemutusanAlasan = $proses4->getField('CR_PEMUTUSAN_ALASAN') ?: '';
					$reqDendaAlasan = $proses4->getField('CR_DENDA_ALASAN') ?: '';

					if ($reqPerubahanAlasan != '') {
						$perubahan .= '<small class="badge badge-danger" style="font-size:9px; padding:3px 10px; margin-right:2px"><i class="fa fa-check"></i>Perubahan Kontrak</small>';
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
				}

				if($aColumns[$i] == "NAMA") {
					$this->load->library("libgeneratecode");
                    $libgeneratecode = new libgeneratecode();
    	            $noPaket = '<small style="font-size: 10px;"><i> No. Paket: '.$libgeneratecode->nomorPaket($contracting->getField("PAKET_ID"),$contracting->getField('PAKET_METODE_LELANG_ID')).'</i></small><br>';
					 
					// $row[] = '<a href="kontrak/index/contracting_detail?reqId='.$contracting->getField('CONTRACTINGREKANANID').'&reqProses='.$getProses.'">'.$contracting->getField($aColumns[$i]).'<br>
					// 	<small class="badge badge-info" style="font-size:9px; padding:3px 10px">'.$contracting->getField('PAKET_METODE_LELANG').'</small>
					// 	<small class="badge badge-primary" style="font-size:9px; padding:3px 10px">'.$contracting->getField('PENGGUNA_STR').'</small></a><br>
					// 	'.$perubahan;

					$row[] = $contracting->getField($aColumns[$i]).'<br>
						<small class="badge badge-info" style="font-size:9px; padding:3px 10px">'.$contracting->getField('PAKET_METODE_LELANG').'</small>
						<small class="badge badge-primary" style="font-size:9px; padding:3px 10px">'.$contracting->getField('PENGGUNA_STR').'</small><br>
						'.$perubahan;

				} else if($aColumns[$i] == "TAHUN_SPPBJ") {
					$tglBAST = '';
					$proses5 = new Contractingrekanan();
                  	$proses5->selectProses5(array("A.PAKET_ID" => $contracting->getField('PAKET_ID')));
                  	while($proses5->nextRow())
					{
						$tglBAST .= getFormattedDate($proses5->getField("CR_BAST_PEKERJAAN_TANGGAL")).'<br>' ?: '-';
					}
					$row[] = $tglBAST;
				} else if($aColumns[$i] == "KETERANGAN") {
					$row[] = truncate($contracting->getField($aColumns[$i]), 5)."...";
				} else if($aColumns[$i] == "CONTRACTINGPROSESID_STR") {
					if ($contracting->getField("CONTRACTINGPROSESID") == '1' || $contracting->getField("CONTRACTINGPROSESID") == '2') {
						$row[] = 'Persiapan';
					} else if ($contracting->getField("CONTRACTINGPROSESID") == '3') {
						$row[] = 'Pengendalian';
					} if ($contracting->getField("CONTRACTINGPROSESID") == '4' || $contracting->getField("CONTRACTINGPROSESID") == '5') {
						$row[] = 'Penyelesaian';
					}
				}
				else if($aColumns[$i] == "PEMENANG") {
					$row[] = $pemenangStr;
				}
				else if($aColumns[$i] == "PIC_PENGENDALI") {
					if ($contracting->getField("PIC_PENGENDALI")) {
						$userLoginAkun = new Userlogin();
						$userLoginAkun->selectByParams(array("USER_LOGIN_ID" => $contracting->getField("PIC_PENGENDALI")));
						$userLoginAkun->firstRow();
						$row[] = $userLoginAkun->getField("USER_NAMA");
					} else {
						$row[] = '-';
					}

				}
				else if($aColumns[$i] == "JNS_KONTRAK") {
					$row[] = $this->libkontrak->jenisKontrak($contracting->getField($aColumns[$i]));
				}
				else if($aColumns[$i] == "NAMA") {
					$row[] = strtoupper($contracting->getField($aColumns[$i]));
				}
				else if($aColumns[$i] == "NILAI") {
          			$row[] = numberToIna($contracting->getField($aColumns[$i]));
				}
          		else if($aColumns[$i] == "CR_NILAI_KONTRAK") {
          			$row[] = numberToIna($contracting->getField($aColumns[$i]));
          		}
          		else if($aColumns[$i] == "CR_SPPBJ_NILAI") {
          			if ($contracting->getField("CR_SPPBJ_NILAI")) {
	          			$row[] = numberToIna($contracting->getField($aColumns[$i]));
          			} else {
	          			$row[] = numberToIna($contracting->getField("CR_NILAI_KONTRAK"));
          			}
          			
          		}
          		else if($aColumns[$i] == "PENGGUNA_STR") {
          			$row[] = $kontrakKet;
          		}
				else {
					$row[] = $contracting->getField($aColumns[$i]);
				}
			}

			$output['aaData'][] = $row;
		}

		echo json_encode( $output );
	}

	function jsonWorkList()
	{
		$this->load->library("kauth");  $userLogin = new kauth();
		$this->load->model("Contracting");
		$this->load->model(array("Contractingrekanan","Contractingpayment","Userlogin"));

		$contracting = new Contracting();
		$reqSearch = $this->input->get("reqSearch");
      	$getTahun = $this->session->userdata('setTahunKontrak');
		$reqProses= $this->input->get("reqProses");

		ini_set("memory_limit","500M");
		ini_set('max_execution_time', 5200);

		$aColumns = array("PAKET_ID","CONTRACTINGREKANANID","NAMA", "PAKET_METODE_LELANG","PAKET_METODE_LELANG_ID","PENGGUNA_STR","PEMENANG","CR_SPPBJ_NILAI","TAHUN_SPPBJ","CONTRACTINGREKANANID","JNS_KONTRAK","PIC_PENGENDALI","CONTRACTINGPROSESID_STR","CONTRACTING_STATUS_KONTRAK");
		$aColumnsAlias = array("A.PAKET_ID","CONTRACTINGREKANANID","A.NAMA", "A.PAKET_METODE_LELANG","PAKET_METODE_LELANG_ID", "A.PENGGUNA_STR","PEMENANG","CR_SPPBJ_NILAI","TAHUN_SPPBJ","CONTRACTINGREKANANID","JNS_KONTRAK","PIC_PENGENDALI","CONTRACTINGPROSESID_STR","CONTRACTING_STATUS_KONTRAK");

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

		if ($this->LEGAL == '1') { // PENG. KONTRAK BAGIAN LEGAL, PEMERIKSA
			$statement = "AND (UPPER(NAMA) LIKE '%".strtoupper($_GET['sSearch'])."%')";
		} else {
			if ($this->USER_TYPE_ID == '20') { // KASUBDIT
				$statement = "AND (UPPER(NAMA) LIKE '%".strtoupper($_GET['sSearch'])."%') AND A.JNS_KONTRAK IN ('0','1','3')  AND CONTRACTINGPROSESID IN ('3','4','5')";
			} else {
				// $statement = "AND (UPPER(NAMA) LIKE '%".strtoupper($_GET['sSearch'])."%') AND A.PPK = '".$this->USER_LOGIN_ID."' ";
				$statement = "AND (UPPER(NAMA) LIKE '%".strtoupper($_GET['sSearch'])."%') ";
			}
		}

		if ($this->USER_TYPE_ID == '12' || $this->USER_TYPE_ID == '20')
		{ // 12 Pengelola Kontrak, 20 Pemeriksa Kontrak
			switch ($this->LEVEL_KONTRAK) {
				case '1': // Staff
					if ($this->PENUNJUK_PIC == '1') { // KASI
						$statement .= " AND CONTRACTINGPROSESID IN ('1','3','4','5') ";
					} else {
						$statement .= " AND CONTRACTINGPROSESID IN ('3','4','5') AND PIC_KONTRAK = '".$this->USER_LOGIN_ID."'";
					}
					// $statement .= " AND CONTRACTINGPROSESID IN ('1','2','3','4','5') ";
					break;

				case '2': // Pengendali
					if ($this->PENUNJUK_PIC == '1') { // KASI
						$statement .= " AND CONTRACTINGPROSESID IN ('3','4','5')";
					} else {
						$statement .= " AND CONTRACTINGPROSESID IN ('3','4','5') AND PIC_PENGENDALI = ".$this->USER_LOGIN_ID." ";
					}
					// code...
					break;

				case '3': // Penyelesai
					$statement .= " AND CONTRACTINGPROSESID IN ('3','4','5') ";
					break;
				
				default:
					// code...
					break;
			}  
		} else if ($this->USER_TYPE_ID == '28') { // PPK
			$statement .= " AND CONTRACTINGPROSESID IN ('3','4','5') ";
			// $statement .= " AND CONTRACTINGPROSESID IN ('0','1') ";
		} else if ($this->USER_TYPE_ID == '') {
			echo "hahahahaaaaaa kamu ngapain disini"; die();
		}

		if ($getTahun == 'all' || $getTahun == '') {
		} else {
			$statement .= " AND A.TAHUN_SPPBJ::text[] && array['".$getTahun."'] ";
		}
		
		$statement .= " AND PIC_KONTRAK IS NOT NULL ";

		$allRecord = $contracting->getCountByParams(array(),$statement);
		if($_GET['sSearch'] == "")
			$allRecordFilter = $allRecord;
		else
			$allRecordFilter = $contracting->getCountByParams(array(), $statement.$searchJson);

		$contracting->selectByParamsViewContracting(array(), $dsplyRange, $dsplyStart, $statement.$searchJson, $sOrder);
		// echo $contracting->query;exit;
		/* Output */
		$output = array(
			"sEcho" => intval($_GET['sEcho']),
			"iTotalRecords" => $allRecord,
			"iTotalDisplayRecords" => $allRecordFilter,
			"aaData" => array()
		);

		while($contracting->nextRow())
		{
			$row = array();
			for ( $i=0 ; $i<count($aColumns) ; $i++ )
			{
				$pemenangStr = '';
				if ($aColumns[$i] == "PEMENANG") {
					if ($contracting->getField($aColumns[$i]) != '') {
						$this->load->model("Rekanan");
						$rekanan = new Rekanan();
						$val = $contracting->getField($aColumns[$i]);
						$val = str_replace(['{', '}'], ['(', ')'], $val);
						$rekanan->selectByParams(array("A.REKANAN_ID|| IN" => $val));
						$rekanan->firstRow();
						$pemenangStr = $rekanan->getField('NAMA');
					}
				}

				$perubahan 	= '';
				 

					$proses4 = new Contractingrekanan();
	     			$proses4->selectProses4(array("A.CONTRACTINGREKANANID" => $contracting->getField('CONTRACTINGREKANANID')));
					$proses4->firstRow();
					$reqPerubahanAlasan = $proses4->getField('CR_PERUBAHAN_ALASAN') ?: '';
					$reqKaharAlasan = $proses4->getField('CR_KAHAR_ALASAN') ?: '';
					$reqPemutusanAlasan = $proses4->getField('CR_PEMUTUSAN_ALASAN') ?: '';
					$reqDendaAlasan = $proses4->getField('CR_DENDA_ALASAN') ?: '';

					if ($reqPerubahanAlasan != '') {
						$perubahan .= '<small class="badge badge-danger" style="font-size:9px; padding:3px 10px; margin-right:2px"><i class="fa fa-check"></i>Perubahan Kontrak</small>';
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

				if($aColumns[$i] == "NAMA") {
					$this->load->library("libgeneratecode");
                    $libgeneratecode = new libgeneratecode();
    	            $noPaket = '<small style="font-size: 10px;"><i> No. Paket: '.$libgeneratecode->nomorPaket($contracting->getField("PAKET_ID"),$contracting->getField('PAKET_METODE_LELANG_ID')).'</i></small><br>';
					 
					// $row[] = '<a href="kontrak/index/contracting_detail?reqId='.$contracting->getField('CONTRACTINGREKANANID').'&reqProses='.$getProses.'">'.$contracting->getField($aColumns[$i]).'<br>
					// 	<small class="badge badge-info" style="font-size:9px; padding:3px 10px">'.$contracting->getField('PAKET_METODE_LELANG').'</small>
					// 	<small class="badge badge-primary" style="font-size:9px; padding:3px 10px">'.$contracting->getField('PENGGUNA_STR').'</small></a><br>
					// 	'.$perubahan;

					$row[] = $contracting->getField($aColumns[$i]).'<br>
						<small class="badge badge-info" style="font-size:9px; padding:3px 10px">'.$contracting->getField('PAKET_METODE_LELANG').'</small>
						<small class="badge badge-primary" style="font-size:9px; padding:3px 10px">'.$contracting->getField('PENGGUNA_STR').'</small><br>
						'.$perubahan;

				} else if($aColumns[$i] == "TAHUN_SPPBJ") {
					$tglBAST = '';
					$proses5 = new Contractingrekanan();
                  	$proses5->selectProses5(array("A.PAKET_ID" => $contracting->getField('PAKET_ID')));
                  	while($proses5->nextRow())
					{
						$tglBAST .= getFormattedDate($proses5->getField("CR_BAST_PEKERJAAN_TANGGAL")).'<br>' ?: '-';
					}
					$row[] = $tglBAST;
				} else if($aColumns[$i] == "KETERANGAN") {
					$row[] = truncate($contracting->getField($aColumns[$i]), 5)."...";
				} else if($aColumns[$i] == "CONTRACTINGPROSESID_STR") {
					if ($contracting->getField("CONTRACTINGPROSESID") == '1' || $contracting->getField("CONTRACTINGPROSESID") == '2') {
						$row[] = 'Persiapan';
					} else if ($contracting->getField("CONTRACTINGPROSESID") == '3') {
						$row[] = 'Pengendalian';
					} if ($contracting->getField("CONTRACTINGPROSESID") == '4' || $contracting->getField("CONTRACTINGPROSESID") == '5') {
						$row[] = 'Penyelesaian';
					}
				}
				else if($aColumns[$i] == "PEMENANG") {
					$row[] = $pemenangStr;
				}
				else if($aColumns[$i] == "PIC_PENGENDALI") {
					if ($contracting->getField("PIC_PENGENDALI")) {
						$userLoginAkun = new Userlogin();
						$userLoginAkun->selectByParams(array("USER_LOGIN_ID" => $contracting->getField("PIC_PENGENDALI")));
						$userLoginAkun->firstRow();
						$row[] = $userLoginAkun->getField("USER_NAMA");
					} else {
						$row[] = '-';
					}

				}
				else if($aColumns[$i] == "JNS_KONTRAK") {
					$row[] = $this->libkontrak->jenisKontrak($contracting->getField($aColumns[$i]));
				}
				else if($aColumns[$i] == "NAMA") {
					$row[] = strtoupper($contracting->getField($aColumns[$i]));
				}
				else if($aColumns[$i] == "NILAI") {
          			$row[] = numberToIna($contracting->getField($aColumns[$i]));
				}
          		else if($aColumns[$i] == "CR_NILAI_KONTRAK") {
          			$row[] = numberToIna($contracting->getField($aColumns[$i]));
          		}
          		else if($aColumns[$i] == "CR_SPPBJ_NILAI") {
          			if ($contracting->getField("CR_SPPBJ_NILAI")) {
	          			$row[] = numberToIna($contracting->getField($aColumns[$i]));
          			} else {
	          			$row[] = numberToIna($contracting->getField("CR_NILAI_KONTRAK"));
          			}
          			
          		}
          		else if($aColumns[$i] == "PENGGUNA_STR") {
          			$row[] = $kontrakKet;
          		}
				else {
					$row[] = $contracting->getField($aColumns[$i]);
				}
			}

			$output['aaData'][] = $row;
		}

		echo json_encode( $output );
	}

	function po()
	{
		$this->load->library("kauth");  $userLogin = new kauth();
		$this->load->model("Contracting");
		$this->load->model(array("Contractingrekanan","Contractingpayment"));

		$contracting = new Contracting();
		$reqSearch = $this->input->get("reqSearch");
      	$getTahun = $this->session->userdata('setTahunKontrak');
		$reqProses= $this->input->get("reqProses");

		ini_set("memory_limit","500M");
		ini_set('max_execution_time', 5200);

		$aColumns = array("PAKET_ID","CONTRACTINGREKANANID","NAMA", "CR_PO", "PAKET_METODE_LELANG","PAKET_METODE_LELANG_ID","PENGGUNA_STR","PEMENANG","CR_SPPBJ_NILAI","TAHUN_SPPBJ","CONTRACTINGREKANANID","JNS_KONTRAK","PIC_KONTRAK_STR","CONTRACTINGPROSESID_STR","CONTRACTING_STATUS_KONTRAK");
		$aColumnsAlias = array("A.PAKET_ID","CONTRACTINGREKANANID","A.NAMA", "A.CR_PO", "A.PAKET_METODE_LELANG","PAKET_METODE_LELANG_ID", "A.PENGGUNA_STR","PEMENANG","CR_SPPBJ_NILAI","TAHUN_SPPBJ","CONTRACTINGREKANANID","JNS_KONTRAK","PIC_KONTRAK_STR","CONTRACTINGPROSESID_STR","CONTRACTING_STATUS_KONTRAK");

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
						$sOrder .=" ASC, ";
					}else
					{
						$sOrder .=" DESC, ";
					}
				}
			}

			$sOrder = substr_replace( $sOrder, "", -2 );

			if ( trim($sOrder) == "ORDER BY A.PAKET_ID ASC" )
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

		if ($this->LEGAL == '1') { // PENG. KONTRAK BAGIAN LEGAL, PEMERIKSA
			$statement = "AND (UPPER(NAMA) LIKE '%".strtoupper($_GET['sSearch'])."%')";
		} else {
			if ($this->USER_TYPE_ID == '20') { // KASUBDIT
				$statement = "AND (UPPER(NAMA) LIKE '%".strtoupper($_GET['sSearch'])."%') AND A.JNS_KONTRAK IN ('0','1','3')  AND CONTRACTINGPROSESID IN ('1','2')";
			} else {
				$statement = "AND (UPPER(NAMA) LIKE '%".strtoupper($_GET['sSearch'])."%') ";
			}
		}

		if ($this->USER_TYPE_ID == '12' || $this->USER_TYPE_ID == '20')
		{ // 12 Pengelola Kontrak, 20 Pemeriksa Kontrak
			switch ($this->LEVEL_KONTRAK) {
				case '1': // Staff
					$getProses = '1';
					if ($this->PENUNJUK_PIC == '1') { // KASI
						$statement .= " AND CONTRACTINGPROSESID IN ('1','2','3') ";
					} else {
						$statement .= " AND CONTRACTINGPROSESID IN ('1','2','3') AND PIC_KONTRAK = '".$this->USER_LOGIN_ID."' ";
					}
					break;

				case '2': // Pengendali
					$getProses = '1';
					$statement .= " AND CONTRACTINGPROSESID IN ('3','4','5') ";
					// code...
					break;

				case '3': // Penyelesai
					$getProses = '1';
					$statement .= " AND CONTRACTINGPROSESID IN ('3','4','5') ";
					break;
				
				default:
					// code...
					break;
			}  

		} else if ($this->USER_TYPE_ID == '28') { // PPK
			$statement .= " AND CONTRACTINGPROSESID IN ('0','1','2') ";
		} else if ($this->USER_TYPE_ID == '') {
			echo "hahahahaaaaaa kamu ngapain disini"; die();
		}

		if ($getTahun == 'all' || $getTahun == '') {
		} else {
			$statement .= " AND A.TAHUN_SPPBJ::text[] && array['".$getTahun."'] ";
		}
		
		$statement .= " AND PIC_KONTRAK IS NOT NULL ";

		$allRecord = $contracting->getCountByParams(array(),$statement);
		if($_GET['sSearch'] == "")
			$allRecordFilter = $allRecord;
		else
			$allRecordFilter = $contracting->getCountByParams(array(), $statement.$searchJson);

		$contracting->selectByParamsViewContracting(array(), $dsplyRange, $dsplyStart, $statement.$searchJson, $sOrder);
		// echo $contracting->query;exit;
		/* Output */
		$output = array(
			"sEcho" => intval($_GET['sEcho']),
			"iTotalRecords" => $allRecord,
			"iTotalDisplayRecords" => $allRecordFilter,
			"aaData" => array()
		);

		while($contracting->nextRow())
		{
			$row = array();
			for ( $i=0 ; $i<count($aColumns) ; $i++ )
			{
				$pemenangStr = '';
				if ($aColumns[$i] == "PEMENANG") {
					if ($contracting->getField($aColumns[$i]) != '') {
						$this->load->model("Rekanan");
						$rekanan = new Rekanan();
						$val = $contracting->getField($aColumns[$i]);
						$val = str_replace(['{', '}'], ['(', ')'], $val);
						$rekanan->selectByParams(array("A.REKANAN_ID|| IN" => $val));
						$rekanan->firstRow();
						$pemenangStr = $rekanan->getField('NAMA');
					}
				}

				$perubahan 	= '';
				 

				if ($reqProses == '3') {
					$proses4 = new Contractingrekanan();
	     			$proses4->selectProses4(array("A.CONTRACTINGREKANANID" => $contracting->getField('CONTRACTINGREKANANID')));
					$proses4->firstRow();
					$reqPerubahanAlasan = $proses4->getField('CR_PERUBAHAN_ALASAN') ?: '';
					$reqKaharAlasan = $proses4->getField('CR_KAHAR_ALASAN') ?: '';
					$reqPemutusanAlasan = $proses4->getField('CR_PEMUTUSAN_ALASAN') ?: '';
					$reqDendaAlasan = $proses4->getField('CR_DENDA_ALASAN') ?: '';

					if ($reqPerubahanAlasan != '') {
						$perubahan .= '<small class="badge badge-danger" style="font-size:9px; padding:3px 10px; margin-right:2px"><i class="fa fa-check"></i>Perubahan Kontrak</small>';
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
				}

				if($aColumns[$i] == "NAMA") {
					$this->load->library("libgeneratecode");
                    $libgeneratecode = new libgeneratecode();
    	            $noPaket = '<small style="font-size: 10px;"><i> No. Paket: '.$libgeneratecode->nomorPaket($contracting->getField("PAKET_ID"),$contracting->getField('PAKET_METODE_LELANG_ID')).'</i></small><br>';
					 
					// $row[] = '<a href="kontrak/index/contracting_detail?reqId='.$contracting->getField('CONTRACTINGREKANANID').'&reqProses='.$getProses.'">'.$contracting->getField($aColumns[$i]).'<br>
					// 	<small class="badge badge-info" style="font-size:9px; padding:3px 10px">'.$contracting->getField('PAKET_METODE_LELANG').'</small>
					// 	<small class="badge badge-primary" style="font-size:9px; padding:3px 10px">'.$contracting->getField('PENGGUNA_STR').'</small></a><br>
					// 	'.$perubahan;

					$row[] = $contracting->getField($aColumns[$i]).'<br>
						<small class="badge badge-info" style="font-size:9px; padding:3px 10px">'.$contracting->getField('PAKET_METODE_LELANG').'</small>
						<small class="badge badge-primary" style="font-size:9px; padding:3px 10px">'.$contracting->getField('PENGGUNA_STR').'</small><br>
						'.$perubahan;

				} else if($aColumns[$i] == "TAHUN_SPPBJ") {
					$tglBAST = '';
					$proses5 = new Contractingrekanan();
                  	$proses5->selectProses5(array("A.PAKET_ID" => $contracting->getField('PAKET_ID')));
                  	while($proses5->nextRow())
					{
						$tglBAST .= getFormattedDate($proses5->getField("CR_BAST_PEKERJAAN_TANGGAL")).'<br>' ?: '-';
					}
					$row[] = $tglBAST;
				} else if($aColumns[$i] == "KETERANGAN") {
					$row[] = truncate($contracting->getField($aColumns[$i]), 5)."...";
				} else if($aColumns[$i] == "CONTRACTINGPROSESID_STR") {
					if ($contracting->getField("CONTRACTINGPROSESID") == '1' || $contracting->getField("CONTRACTINGPROSESID") == '2') {
						$row[] = 'Persiapan';
					} else if ($contracting->getField("CONTRACTINGPROSESID") == '3') {
						$row[] = 'Pengendalian';
					} if ($contracting->getField("CONTRACTINGPROSESID") == '4' || $contracting->getField("CONTRACTINGPROSESID") == '5') {
						$row[] = 'Penyelesaian';
					}
				}
				else if($aColumns[$i] == "PEMENANG") {
					$row[] = $pemenangStr;
				}
				else if($aColumns[$i] == "JNS_KONTRAK") {
					$row[] = $this->libkontrak->jenisKontrak($contracting->getField($aColumns[$i]));
				}
				else if($aColumns[$i] == "NAMA") {
					$row[] = strtoupper($contracting->getField($aColumns[$i]));
				}
				else if($aColumns[$i] == "NILAI") {
          			$row[] = numberToIna($contracting->getField($aColumns[$i]));
				}
          		else if($aColumns[$i] == "CR_NILAI_KONTRAK") {
          			$row[] = numberToIna($contracting->getField($aColumns[$i]));
          		}
          		else if($aColumns[$i] == "CR_SPPBJ_NILAI") {
          			if ($contracting->getField("CR_SPPBJ_NILAI")) {
	          			$row[] = numberToIna($contracting->getField($aColumns[$i]));
          			} else {
	          			$row[] = numberToIna($contracting->getField("CR_NILAI_KONTRAK"));
          			}
          			
          		}
          		else if($aColumns[$i] == "PENGGUNA_STR") {
          			$row[] = $kontrakKet;
          		}
				else {
					$row[] = $contracting->getField($aColumns[$i]);
				}
			}

			$output['aaData'][] = $row;
		}

		echo json_encode( $output );
	}

}
?>
