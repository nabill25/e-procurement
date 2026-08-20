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

class contracting_json extends CI_Controller {

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
		$this->PENUNJUK_PIC = $this->kauth->getInstance()->getIdentity()->PENUNJUK_PIC;
		$this->UNIT_KERJA_ID =  $this->kauth->getInstance()->getIdentity()->UNIT_KERJA_ID;
		$this->NIP =  $this->kauth->getInstance()->getIdentity()->NIP;
		$this->LOGIN_TIME = $this->kauth->getInstance()->getIdentity()->LOGIN_TIME;
		$this->LOGIN_DATE = $this->kauth->getInstance()->getIdentity()->LOGIN_DATE;
		$this->LEVEL_PEMBELI = $this->kauth->getInstance()->getIdentity()->LEVEL_PEMBELI;
		$this->LEVEL_PERENCANA = $this->kauth->getInstance()->getIdentity()->LEVEL_PERENCANA;
		$this->LEVEL_PENGGUNA = $this->kauth->getInstance()->getIdentity()->LEVEL_PENGGUNA;
		$this->KASI_PENGGUNA = $this->kauth->getInstance()->getIdentity()->KASI_PENGGUNA;
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
		$this->load->model(array("Contracting","PaketDokumen"));

		$contracting = new Contracting();
		$reqSearch = $this->input->get("reqSearch");
		$getTahun = $_GET['tahun'];

		ini_set("memory_limit","500M");
		ini_set('max_execution_time', 5200);

		$aColumns = array("PAKET_ID","NILAI", "NAMA", "NILAI", "HARGA_PEMILIHAN", "PAKET_METODE_LELANG", "JENIS_KONTRAK","PENGGUNA_STR","PEMENANG","APPROVE_PPK","PIC_KONTRAK_STR");
		$aColumnsAlias = array("A.PAKET_ID", "A.NILAI", "A.NAMA", "A.NILAI", "A.HARGA_PEMILIHAN", "A.PAKET_METODE_LELANG", "A.JENIS_KONTRAK", "A.PENGGUNA_STR","PEMENANG","APPROVE_PPK","PIC_KONTRAK_STR");

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
			// case '3': // Panitia
			// $statement .= "   AND A.PANITIA = '".$this->USER_LOGIN_ID."'";
			// 	break;
			case '12': // PENGELOLA KONTRAK
				if ($getTahun == 'all') {
					if ($this->LEGAL == '1') {
						$statement .= " AND A.SELESAI = '1' AND A.PAKET_METODE_LELANG_ID IN (1,2,3,4,5,7,8,10,11) AND A.STATUS_KONTRAK = 'Belum dibuat' AND A.APPROVE_PPK = '1' AND A.PIC_KONTRAK IS NULL ";
					} else {
						$statement .= " AND A.SELESAI = '1' AND A.PAKET_METODE_LELANG_ID IN (1,2,3,4,5,7,8,10,11) AND A.STATUS_KONTRAK = 'Belum dibuat' AND A.APPROVE_PPK = '1' AND A.PIC_KONTRAK IS NULL ";
					}
				} else {
					if ($this->LEGAL == '1') {
						$statement .= " AND A.SELESAI = '1' AND A.PAKET_METODE_LELANG_ID IN (1,2,3,4,5,7,8,10,11) AND A.STATUS_KONTRAK = 'Belum dibuat' AND A.APPROVE_PPK = '1' AND A.PIC_KONTRAK IS NULL AND A.TAHUN = '".$getTahun."'";
					} else {
						$statement .= " AND A.SELESAI = '1' AND A.PAKET_METODE_LELANG_ID IN (1,2,3,4,5,7,8,10,11) AND A.STATUS_KONTRAK = 'Belum dibuat' AND A.APPROVE_PPK = '1' AND A.PIC_KONTRAK IS NULL AND A.TAHUN = '".$getTahun."'";
					}
				} 
			break;

			case '7': // MANAGER PENGADAAN / KPP
				$statement .= " AND A.SELESAI = '1' AND A.PAKET_METODE_LELANG_ID IN (1,3,4,5,7,8,10,11) AND A.STATUS_KONTRAK = 'Belum dibuat' AND A.APPROVE_MANAGER IS NULL "; 
			break;

			case '11': // PJP
				if ($this->LEVEL_PEMBELI == '1') { // Hanya Kasi
				$statement .= " AND A.SELESAI = '1' AND A.PAKET_METODE_LELANG_ID IN (2) AND A.STATUS_KONTRAK = 'Belum dibuat' AND A.APPROVE_MANAGER IS NULL "; 
				}
			break;


			case '27': // PERENCANA
				if ($this->LEVEL_PERENCANA == '2') { // Hanya Kasi
				$statement .= " AND A.SELESAI = '1' AND A.PAKET_METODE_LELANG_ID IN (2) AND A.STATUS_KONTRAK = 'Belum dibuat' AND A.APPROVE_MANAGER IS NULL "; 
				}
			break;

			case '28': // PPK
				if ($getTahun == 'all') { 
					$statement .= " AND A.SELESAI = '1' AND A.PAKET_METODE_LELANG_ID IN (1,2,3,4,5,7,8,10,11) AND A.STATUS_KONTRAK = 'Belum dibuat' AND A.APPROVE_MANAGER = '1' AND A.APPROVE_PPK IS NULL ";
				} else {
					$statement .= " AND A.SELESAI = '1' AND A.PAKET_METODE_LELANG_ID IN (1,2,3,4,5,7,8,10,11) AND A.STATUS_KONTRAK = 'Belum dibuat' AND A.APPROVE_MANAGER = '1' AND A.APPROVE_PPK IS NULL AND A.TAHUN = '".$getTahun."'";
				}
			break;

			default:
				echo "hahahahaaaaaa kamu ngapai disini"; die();
				break;
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
				$pemenangStr = '<span class="badge badge-danger">Belum Ditetapkan</span>';
				if ($aColumns[$i] == "PEMENANG") {
					if ($contracting->getField($aColumns[$i]) != '') {
						$pemenangStr = '<span class="badge badge-primary">Sudah Ditetapkan</span>';
					}
				}

				// DOKUMEN PENETAPAN PEMENANG
				$paket_dokumen = new PaketDokumen();
	            $paket_dokumen->selectByParams(array("PAKET_ID" => $contracting->getField('PAKET_ID'), "JENIS_DOKUMEN" => "PENETAPAN_PEMENANG"));
	            $paket_dokumen->firstRow();
	            $dokumen = $paket_dokumen->getField("PATH_FILE");
	            if($dokumen == "")
              	{ $dokumen = ''; } else { $dokumen = '<small class="badge badge-info mt-1"><span class="fa fa-download"></span><a href="uploads/penawaran/'.$dokumen.'" target="_blank">Download BAHPL</a></small>'; }

				if($aColumns[$i] == "NAMA")
					$row[] = str_replace(",","",$contracting->getField($aColumns[$i])).'<br>
							 <small class="badge badge-info" style="font-size:9px; padding:3px 10px">'.$contracting->getField('PAKET_METODE_LELANG').'</small>
							 ';
							 // <small class="badge badge-primary" style="font-size:9px; padding:3px 10px">'.$contracting->getField('PENGGUNA_STR').'</small>
				else if($aColumns[$i] == "KETERANGAN")
					$row[] = truncate($contracting->getField($aColumns[$i]), 5)."...";
				else if($aColumns[$i] == "PEMENANG")
					$row[] = $pemenangStr.' <br> '.$dokumen;
				else if($aColumns[$i] == "NAMA")
					$row[] = strtoupper($contracting->getField($aColumns[$i]));
				else if($aColumns[$i] == "NILAI" || $aColumns[$i] == "HARGA_PEMILIHAN")
          			$row[] = numberToIna($contracting->getField($aColumns[$i]));
				else
					$row[] = $contracting->getField($aColumns[$i]);
			}

			$output['aaData'][] = $row;
		}

		echo json_encode( $output );
	}

	function contracting_paket_sppbj()
	{
		$this->load->library("kauth");  $userLogin = new kauth();
		$this->load->model("Contracting");

		$contracting = new Contracting();
		$reqSearch = $this->input->get("reqSearch");
		$getTahun = $_GET['tahun'];

		ini_set("memory_limit","500M");
		ini_set('max_execution_time', 5200);

		$aColumns = array("PAKET_ID","PIC_KONTRAK","PEMENANG","NILAI","NAMA", "NILAI", "PAKET_METODE_LELANG", "JENIS_KONTRAK","PENGGUNA_STR","APPROVE_PPK","PIC_KONTRAK_STR");
		$aColumnsAlias = array("A.PAKET_ID","PIC_KONTRAK","PEMENANG", "A.NILAI", "A.NAMA", "A.NILAI", "A.PAKET_METODE_LELANG", "A.JENIS_KONTRAK", "A.PENGGUNA_STR","APPROVE_PPK","PIC_KONTRAK_STR");

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

		
		if ($this->PENUNJUK_PIC == '1') { // KASI BISA LIHAT SEMUA
			if ($getTahun == 'all') {
				$statement .= " AND A.SELESAI = '1' AND A.PAKET_METODE_LELANG_ID IN (1,2,3,4,5,7,8,10,11) AND A.STATUS_KONTRAK = 'Belum dibuat' AND A.APPROVE_PPK = '1' AND A.PIC_KONTRAK IS NOT NULL ";
			} else {
				$statement .= " AND A.SELESAI = '1' AND A.PAKET_METODE_LELANG_ID IN (1,2,3,4,5,7,8,10,11) AND A.STATUS_KONTRAK = 'Belum dibuat' AND A.APPROVE_PPK = '1' AND A.PIC_KONTRAK IS NOT NULL AND A.TAHUN = '".$getTahun."'";
			} 
		} else {
			if ($getTahun == 'all') {
				$statement .= " AND A.SELESAI = '1' AND A.PAKET_METODE_LELANG_ID IN (1,2,3,4,5,7,8,10,11) AND A.STATUS_KONTRAK = 'Belum dibuat' AND A.APPROVE_PPK = '1' AND A.PIC_KONTRAK = ".$this->USER_LOGIN_ID." ";
			} else {
				$statement .= " AND A.SELESAI = '1' AND A.PAKET_METODE_LELANG_ID IN (1,2,3,4,5,7,8,10,11) AND A.STATUS_KONTRAK = 'Belum dibuat' AND A.APPROVE_PPK = '1' AND A.PIC_KONTRAK = ".$this->USER_LOGIN_ID." AND A.TAHUN = '".$getTahun."'";
			} 
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
				$pemenangStr = '<span class="badge badge-danger">Belum Ditetapkan</span>';
				if ($aColumns[$i] == "PEMENANG") {
					if ($contracting->getField($aColumns[$i]) != '') {
						$pemenangStr = '<span class="badge badge-primary">Sudah Ditetapkan</span>';
					}
				}

				if($aColumns[$i] == "NAMA")
					$row[] = $contracting->getField($aColumns[$i]).'<br>
							 <small class="badge badge-info" style="font-size:9px; padding:3px 10px">'.$contracting->getField('PAKET_METODE_LELANG').'</small>
							 ';
							 // <small class="badge badge-primary" style="font-size:9px; padding:3px 10px">'.$contracting->getField('PENGGUNA_STR').'</small>
				else if($aColumns[$i] == "KETERANGAN")
					$row[] = truncate($contracting->getField($aColumns[$i]), 5)."...";
				else if($aColumns[$i] == "PEMENANG")
					$row[] = $pemenangStr;
				else if($aColumns[$i] == "NAMA")
					$row[] = strtoupper($contracting->getField($aColumns[$i]));
				else if($aColumns[$i] == "NILAI")
          			$row[] = numberToIna($contracting->getField($aColumns[$i]));
				else
					$row[] = $contracting->getField($aColumns[$i]);
			}

			$output['aaData'][] = $row;
		}

		echo json_encode( $output );
	}

	function approve_ppk()
	{
		$reqId =  $this->input->post('reqId'); // paket_id
		$this->load->model("Paket");

		$paket = new Paket();
		$paket->setField("PAKET_ID", $reqId);
		$paket->setField("APPROVE_PPK", "1");
		$paket->setField("CREATED_BY", $this->USER_LOGIN_ID);

		if($paket->approvePaketPPK()) {
			 // Insert Rekam Jejak
		    $this->load->library("librekamjejak");
		    $this->librekamjejak->insertRJ('90','',$reqId,'null','90'); 
		    // param 1: Posisi/'null', param 2: Keterangan/'', param 3: Paket_id/'null', param 4: permohonan_id/'null'
		    // End Insert Rekam Jejak
			echo "Data Berhasil Disetujui";
		}else {
			echo "Data Gagal Disetujui";
		}
	}

	function approve_manager()
	{
		$reqId =  $this->input->post('reqId'); // paket_id
		$this->load->model("Paket");

		$paket = new Paket();
		$paket->setField("PAKET_ID", $reqId);
		$paket->setField("APPROVE_MANAGER", "1");
		$paket->setField("CREATED_BY", $this->USER_LOGIN_ID);

		if($paket->approvePaketManager()) {
			 // Insert Rekam Jejak
		    $this->load->library("librekamjejak");
		    $this->librekamjejak->insertRJ('907','',$reqId,'null','907'); 
		    // param 1: Posisi/'null', param 2: Keterangan/'', param 3: Paket_id/'null', param 4: permohonan_id/'null'
		    // End Insert Rekam Jejak
			echo "Data Berhasil Diteruskan";
		}else {
			echo "Data Gagal Diteruskan";
		}
	}

	function addSppbj() // Done
	{
		$this->load->model("Contracting");

		$proses1	= new Contracting();
		$kontrak	= new Contracting();
		// echo "<pre>"; print_r($this->input->post()); die();
		$reqId		= $this->input->post('reqId');

		$reqContractingRekananProses1Id	= $this->input->post('reqContractingRekananProses1Id');
		$reqContractingRekananId	= $this->input->post('reqContractingRekananId');
		$reqKode					= $this->input->post('reqKode');
		$reqTanggal					= $this->input->post('reqTanggal');
		$reqPejabatBerwenang		= $this->input->post('reqPejabatBerwenang');
		$reqNIP						= $this->input->post('reqNIP');
		$reqJabatan					= $this->input->post('reqJabatan');
		$reqNamaDirut				= $this->input->post('reqNamaDirut');
		$reqKota					= $this->input->post('reqKota');
		$reqJabatanDirut			= $this->input->post('reqJabatanDirut');
		$reqAlamatDirut				= $this->input->post('reqAlamatDirut');
		$reqNilai					= $this->input->post('reqNilai');
		$reqPelaksanaanDari			= $this->input->post('reqPelaksanaanDari');
		$reqPelaksanaanSampai		= $this->input->post('reqPelaksanaanSampai');
		$reqPPN						= $this->input->post('reqPPN');
		$reqJaminanPelaksanaan		= $this->input->post('reqJaminanPelaksanaan');
		$reqPersenJaminan			= $this->input->post('reqPersenJaminan') ?: 0;
		$reqNilaiJaminan			= $this->input->post('reqNilaiJaminan') ?: 0;
		$reqJangkaMaksimal			= $this->input->post('reqJangkaMaksimal');
		$reqJangkaDari				= $this->input->post('reqJangkaDari');
		$reqJangkaSampai			= $this->input->post('reqJangkaSampai');
		$reqRekananId				= $this->input->post('reqRekanan');
		$reqJnsKontrak				= $this->input->post('reqJnsKontrak');

		if($reqContractingRekananProses1Id== '') // Insert 2 table CONTRACTING_REKANAN & CONTRACTING_REKANAN_PROSES1
		{
			$this->load->model("Paket");
			$paket = new Paket();
			$paket->selectByParamsMonitoring(array(), -1, -1, " AND PAKET_ID = '".$reqId."' ");
			$paket->firstRow();
			$paketNama = $paket->getField("NAMA");
			$paketUraian = str_replace("'","''",$paket->getField("URAIAN"));
			$paketNilai = $paket->getField("NILAI");
			$paketJenis = $paket->getField("PAKET_JENIS_ID");

			$kontrak->setField("PAKET_ID", $reqId);
			$kontrak->setField("NAMA", $paketNama);
			$kontrak->setField("URAIAN", $paketUraian);
			$kontrak->setField("NILAI", $paketNilai);
			$kontrak->setField("CONTRACTINGPROSESID", "1"); // awal input SPPBJ
			$kontrak->setField("REKANAN_ID", $reqRekananId);
			$kontrak->setField("JNS_KONTRAK", $reqJnsKontrak);
	  		$kontrak->setField('CREATED_BY', $this->USER_LOGIN_ID);

			$kontrak->setField("CR_SPPBJ_CODE", $reqKode);
			$kontrak->setField("CR_SPPBJ_TANGGAL", dateToDBCheck($reqTanggal));
			$kontrak->setField("CR_SPPBJ_DIRUT", $reqNamaDirut);
			$kontrak->setField("CR_SPPBJ_DIRUT_ALAMAT", $reqAlamatDirut);
			$kontrak->setField("CR_SPPBJ_DIRUT_KOTA", $reqKota);
			$kontrak->setField("CR_SPPBJ_DIRUT_JABATAN", $reqJabatanDirut);
			$kontrak->setField("CR_SPPBJ_JAMINAN_PELAKSANA", $reqJaminanPelaksanaan);
			$kontrak->setField("CR_SPPBJ_JAMINAN_BESAR", $reqPersenJaminan);
			$kontrak->setField("CR_SPPBJ_JAMINAN_JANGKA_DARI", dateToDBCheck($reqJangkaDari));
			$kontrak->setField("CR_SPPBJ_JAMINAN_JANGKA_SAMPAI", dateToDBCheck($reqJangkaSampai));
			$kontrak->setField("CR_SPPBJ_JAMINAN_NILAI", CommaToDot(dotToNo($reqNilaiJaminan)));
			$kontrak->setField("CR_SPPBJ_PEJABAT_BERWENANG", $reqPejabatBerwenang);
			$kontrak->setField("CR_SPPBJ_NIP", $reqNIP);
			$kontrak->setField("CR_SPPBJ_JABATAN", $reqJabatan);
			$kontrak->setField("CR_SPPBJ_PPN", $reqPPN);
			$kontrak->setField("CR_SPPBJ_PELAKSANAAN_DARI",dateToDBCheck($reqPelaksanaanDari));
			$kontrak->setField("CR_SPPBJ_PELAKSANAAN_SAMPAI",dateToDBCheck($reqPelaksanaanSampai));
			$kontrak->setField("CR_SPPBJ_JAMINAN_MAKSIMAL_PENYERAHAN",dateToDBCheck($reqJangkaMaksimal));
  			$kontrak->setField('CR_SPPBJ_CREATED_BY', $this->USER_LOGIN_ID);
			$kontrak->setField("CR_SPPBJ_NILAI", CommaToDot(dotToNo($reqNilai)));
  			$kontrak->setField('STATUS', 0);
  			$kontrak->setField('CR_JENIS_PENGADAAN', $paketJenis);
			$insert = $kontrak->insertContracting();
			if ($insert) {
        		$diContract = $kontrak->CONTRACTINGREKANANID;

				// Insert Rekam Jejak
		        $this->load->library("librekamjejak");
		        $this->librekamjejak->insertRJ('250','Nomor: '.$reqKode,'null','null','250',$diContract);
		        // End Insert Rekam Jejak

				echo "Data berhasil disimpan.-".$diContract;
			} else { echo "Data gagal simpan."; }

		}
		else
		{
			$kontrak->setField("CONTRACTINGREKANANPROSES1ID", $reqContractingRekananProses1Id);
			$kontrak->setField("CR_SPPBJ_CODE", $reqKode);
			$kontrak->setField("CR_SPPBJ_TANGGAL", dateToDBCheck($reqTanggal));
			$kontrak->setField("CR_SPPBJ_DIRUT", $reqNamaDirut);
			$kontrak->setField("CR_SPPBJ_DIRUT_ALAMAT", $reqAlamatDirut);
			$kontrak->setField("CR_SPPBJ_DIRUT_KOTA", $reqKota);
			$kontrak->setField("CR_SPPBJ_DIRUT_JABATAN", $reqJabatanDirut);
			$kontrak->setField("CR_SPPBJ_JAMINAN_PELAKSANA", $reqJaminanPelaksanaan);
			$kontrak->setField("CR_SPPBJ_JAMINAN_BESAR", $reqPersenJaminan);
			$kontrak->setField("CR_SPPBJ_JAMINAN_JANGKA_DARI", dateToDBCheck($reqJangkaDari));
			$kontrak->setField("CR_SPPBJ_JAMINAN_JANGKA_SAMPAI", dateToDBCheck($reqJangkaSampai));
			$kontrak->setField("CR_SPPBJ_JAMINAN_NILAI", CommaToDot(dotToNo($reqNilaiJaminan)));
			$kontrak->setField("CR_SPPBJ_PEJABAT_BERWENANG", $reqPejabatBerwenang);
			$kontrak->setField("CR_SPPBJ_NIP", $reqNIP);
			$kontrak->setField("CR_SPPBJ_JABATAN", $reqJabatan);
			$kontrak->setField("CR_SPPBJ_PPN", $reqPPN);
			$kontrak->setField("CR_SPPBJ_PELAKSANAAN_DARI",dateToDBCheck($reqPelaksanaanDari));
			$kontrak->setField("CR_SPPBJ_PELAKSANAAN_SAMPAI",dateToDBCheck($reqPelaksanaanSampai));
			$kontrak->setField("CR_SPPBJ_JAMINAN_MAKSIMAL_PENYERAHAN",dateToDBCheck($reqJangkaMaksimal));
  			$kontrak->setField('CR_SPPBJ_UPDATED_BY', $this->USER_LOGIN_ID);
			$kontrak->setField("CR_SPPBJ_NILAI", CommaToDot(dotToNo($reqNilai)));
			$insert = $kontrak->updateProses1();
			if ($insert) {
				// Insert Rekam Jejak
		        $this->load->library("librekamjejak");
		        $this->librekamjejak->insertRJ('250','Nomor: '.$reqKode,'null','null','250',$reqContractingRekananId);
		        // End Insert Rekam Jejak
		        echo "Data berhasil diubah.";
			} else { echo "Data proses gagal diubah."; }
		}
	}

	function addSppbjNon($reqId,$tahun) // Done
	{
		$this->load->model(array("Contracting","Paketpemenang"));

		$proses1	= new Contracting();
		$kontrak	= new Contracting();
		$getpaket_pemenang = new Paketpemenang();
 
		$this->load->model("Paket");
		$paket = new Paket();
		$paket->selectByParamsMonitoring(array(), -1, -1, " AND PAKET_ID = '".$reqId."' ");
		$paket->firstRow();
		$paketNama = $paket->getField("NAMA");
		$paketUraian = str_replace("'","''",$paket->getField("URAIAN"));
		$paketNilai = $paket->getField("NILAI");
		$paketJenis = $paket->getField("PAKET_JENIS_ID");

	  	$getpaket_pemenang->selectByParams(array("PAKET_ID" => $reqId, "PERINGKAT" => '1'), -1, -1);
	  	$getpaket_pemenang->firstRow();
	  	$reqRekananId = $getpaket_pemenang->getField("REKANAN_ID");

		$kontrak->setField("PAKET_ID", $reqId);
		$kontrak->setField("NAMA", $paketNama);
		$kontrak->setField("URAIAN", $paketUraian);
		$kontrak->setField("NILAI", $paketNilai);
		$kontrak->setField("CONTRACTINGPROSESID", "1"); // awal input SPPBJ
		$kontrak->setField("REKANAN_ID", $reqRekananId);
		$kontrak->setField("JNS_KONTRAK", '0');
  		$kontrak->setField('CREATED_BY', $this->USER_LOGIN_ID);
		$kontrak->setField('STATUS', 3);
		$kontrak->setField('CR_JENIS_PENGADAAN', $paketJenis);
		$insert = $kontrak->insertContractingNonSPPBJ();
		if ($insert) {
    		$diContract = $kontrak->CONTRACTINGREKANANID; 
    		// Insert Rekam Jejak
	        $this->load->library("librekamjejak");
	        $this->librekamjejak->insertRJ('2501','Nama Paket: '.$paketNama,'null','null','2501',$diContract);
	        // End Insert Rekam Jejak
	        echo "Data berhasil disimpan";
		} else { echo "Data gagal simpan."; } 
	}

	function addSppbjMulti() // Done
	{
		$this->load->model("Contracting");

		$proses1	= new Contracting();
		$kontrak	= new Contracting();
		// echo "<pre>"; print_r($this->input->post()); die();
		$reqId		= $this->input->post('reqId');

		$reqContractingRekananProses1Id	= $this->input->post('reqContractingRekananProses1Id');
		$reqContractingRekananId	= $this->input->post('reqContractingRekananId');
		$reqKode					= $this->input->post('reqKode');
		$reqTanggal					= $this->input->post('reqTanggal');
		$reqPejabatBerwenang		= $this->input->post('reqPejabatBerwenang');
		$reqNIP						= $this->input->post('reqNIP');
		$reqJabatan					= $this->input->post('reqJabatan');
		$reqNamaDirut				= $this->input->post('reqNamaDirut');
		$reqKota					= $this->input->post('reqKota');
		$reqJabatanDirut			= $this->input->post('reqJabatanDirut');
		$reqAlamatDirut				= $this->input->post('reqAlamatDirut');
		$reqNilai					= $this->input->post('reqNilai');
		$reqPelaksanaanDari			= $this->input->post('reqPelaksanaanDari');
		$reqPelaksanaanSampai		= $this->input->post('reqPelaksanaanSampai');
		$reqPPN						= $this->input->post('reqPPN');
		$reqJaminanPelaksanaan		= $this->input->post('reqJaminanPelaksanaan');
		$reqPersenJaminan			= $this->input->post('reqPersenJaminan') ?: 0;
		$reqNilaiJaminan			= $this->input->post('reqNilaiJaminan') ?: 0;
		$reqJangkaDari				= $this->input->post('reqJangkaDari');
		$reqJangkaSampai			= $this->input->post('reqJangkaSampai');
		$reqRekananId				= $this->input->post('reqRekanan');
		$reqJnsKontrak				= $this->input->post('reqJnsKontrak');
		$reqContractingRekananId	= $this->input->post('reqContractingRekananId');

		if($reqContractingRekananProses1Id== '') // Insert 1 table CONTRACTING_REKANAN_PROSES1
		{
			$this->load->model("Paket");
			$paket = new Paket();
			$paket->selectByParamsMonitoring(array(), -1, -1, " AND PAKET_ID = '".$reqId."' ");
			$paket->firstRow();
			$paketNama = $paket->getField("NAMA");
			$paketUraian = str_replace("'","''",$paket->getField("URAIAN"));
			$paketNilai = $paket->getField("NILAI");
			$paketJenis = $paket->getField("PAKET_JENIS_ID");

			$kontrak->setField("PAKET_ID", $reqId);
			$kontrak->setField("NAMA", $paketNama);
			$kontrak->setField("URAIAN", $paketUraian);
			$kontrak->setField("NILAI", $paketNilai);
			$kontrak->setField("CONTRACTINGPROSESID", "1"); // awal input SPPBJ
			$kontrak->setField("REKANAN_ID", $reqRekananId);
			$kontrak->setField("JNS_KONTRAK", $reqJnsKontrak);
	  		$kontrak->setField('CREATED_BY', $this->USER_LOGIN_ID);

			$kontrak->setField("CR_SPPBJ_CODE", $reqKode);
			$kontrak->setField("CR_SPPBJ_TANGGAL", dateToDBCheck($reqTanggal));
			$kontrak->setField("CR_SPPBJ_DIRUT", $reqNamaDirut);
			$kontrak->setField("CR_SPPBJ_DIRUT_ALAMAT", $reqAlamatDirut);
			$kontrak->setField("CR_SPPBJ_DIRUT_KOTA", $reqKota);
			$kontrak->setField("CR_SPPBJ_DIRUT_JABATAN", $reqJabatanDirut);
			$kontrak->setField("CR_SPPBJ_JAMINAN_PELAKSANA", $reqJaminanPelaksanaan);
			$kontrak->setField("CR_SPPBJ_JAMINAN_BESAR", $reqPersenJaminan);
			$kontrak->setField("CR_SPPBJ_JAMINAN_JANGKA_DARI", dateToDBCheck($reqJangkaDari));
			$kontrak->setField("CR_SPPBJ_JAMINAN_JANGKA_SAMPAI", dateToDBCheck($reqJangkaSampai));
			$kontrak->setField("CR_SPPBJ_JAMINAN_NILAI", CommaToDot(dotToNo($reqNilaiJaminan)));
			$kontrak->setField("CR_SPPBJ_PEJABAT_BERWENANG", $reqPejabatBerwenang);
			$kontrak->setField("CR_SPPBJ_NIP", $reqNIP);
			$kontrak->setField("CR_SPPBJ_JABATAN", $reqJabatan);
			$kontrak->setField("CR_SPPBJ_PPN", $reqPPN);
			$kontrak->setField("CR_SPPBJ_PELAKSANAAN_DARI",dateToDBCheck($reqPelaksanaanDari));
			$kontrak->setField("CR_SPPBJ_PELAKSANAAN_SAMPAI",dateToDBCheck($reqPelaksanaanSampai));
  			$kontrak->setField('CR_SPPBJ_CREATED_BY', $this->USER_LOGIN_ID);
			$kontrak->setField("CR_SPPBJ_NILAI", CommaToDot(dotToNo($reqNilai)));
  			$kontrak->setField('STATUS', 0);
  			$kontrak->setField('CR_JENIS_PENGADAAN', $paketJenis);
  			$kontrak->setField('CONTRACTINGREKANANID', $reqContractingRekananId);
			$insert = $kontrak->insertContractingSPPBJMulti();
			if ($insert) { echo "Data berhasil disimpan.";
			} else { echo "Data gagal simpan."; }

		}
		else
		{
			$kontrak->setField("CONTRACTINGREKANANPROSES1ID", $reqContractingRekananProses1Id);
			$kontrak->setField("CR_SPPBJ_CODE", $reqKode);
			$kontrak->setField("CR_SPPBJ_TANGGAL", dateToDBCheck($reqTanggal));
			$kontrak->setField("CR_SPPBJ_DIRUT", $reqNamaDirut);
			$kontrak->setField("CR_SPPBJ_DIRUT_ALAMAT", $reqAlamatDirut);
			$kontrak->setField("CR_SPPBJ_DIRUT_KOTA", $reqKota);
			$kontrak->setField("CR_SPPBJ_DIRUT_JABATAN", $reqJabatanDirut);
			$kontrak->setField("CR_SPPBJ_JAMINAN_PELAKSANA", $reqJaminanPelaksanaan);
			$kontrak->setField("CR_SPPBJ_JAMINAN_BESAR", $reqPersenJaminan);
			$kontrak->setField("CR_SPPBJ_JAMINAN_JANGKA_DARI", dateToDBCheck($reqJangkaDari));
			$kontrak->setField("CR_SPPBJ_JAMINAN_JANGKA_SAMPAI", dateToDBCheck($reqJangkaSampai));
			$kontrak->setField("CR_SPPBJ_JAMINAN_NILAI", CommaToDot(dotToNo($reqNilaiJaminan)));
			$kontrak->setField("CR_SPPBJ_PEJABAT_BERWENANG", $reqPejabatBerwenang);
			$kontrak->setField("CR_SPPBJ_NIP", $reqNIP);
			$kontrak->setField("CR_SPPBJ_JABATAN", $reqJabatan);
			$kontrak->setField("CR_SPPBJ_PPN", $reqPPN);
			$kontrak->setField("CR_SPPBJ_PELAKSANAAN_DARI",dateToDBCheck($reqPelaksanaanDari));
			$kontrak->setField("CR_SPPBJ_PELAKSANAAN_SAMPAI",dateToDBCheck($reqPelaksanaanSampai));
  			$kontrak->setField('CR_SPPBJ_UPDATED_BY', $this->USER_LOGIN_ID);
			$kontrak->setField("CR_SPPBJ_NILAI", CommaToDot(dotToNo($reqNilai)));
			$insert = $kontrak->updateProses1();
			if ($insert) { echo "Data berhasil diubah.";
			} else { echo "Data proses gagal diubah."; }
		}
	}

	function addSpmk() //
	{
		$this->load->model("Contracting");

		$proses1	= new Contracting();
		$kontrak	= new Contracting();
		// echo "<pre>"; print_r($this->input->post()); die();
		$reqId		= $this->input->post('reqId');

		$reqContractingRekananProses1SpmkId	= $this->input->post('reqContractingRekananProses1SpmkId');
		$reqContractingRekananId	= $this->input->post('reqContractingRekananId');
		$reqNomor					= $this->input->post('reqNomor');
		$reqSPMKDari				= $this->input->post('reqSPMKDari');
		$reqSPMKSampai				= $this->input->post('reqSPMKSampai');
		$reqSPMKSampai				= $this->input->post('reqSPMKSampai');
		$reqRekananId				= $this->input->post('reqRekananId');
    	$reqKeterangan 				= str_replace("'","''",$_POST["reqKeterangan"]);

		if($reqContractingRekananProses1SpmkId== '') //
		{
	  		$kontrak->setField('CONTRACTINGREKANANID', $reqContractingRekananId);
			$kontrak->setField("NOMOR", $reqNomor);
			$kontrak->setField("SPMK_DARI", dateToDBCheck($reqSPMKDari));
			$kontrak->setField("SPMK_SAMPAI", dateToDBCheck($reqSPMKSampai));
			$kontrak->setField("KETERANGAN", $reqKeterangan);
			$kontrak->setField("REKANAN_ID", $reqRekananId);
	  		$kontrak->setField('CREATED_BY', $this->USER_LOGIN_ID);
			$insert = $kontrak->insertSPMK();
			if ($insert) {
				$this->load->library("librekamjejak");
				$this->librekamjejak->insertRJ('261','Nomor: '.$reqNomor,'null','null','261',$reqContractingRekananId);
				echo "Data berhasil disimpan.";
			} else { echo "Data gagal simpan."; }

		}
		else
		{
	  		$kontrak->setField('CONTRACTINGREKANANPROSES1SPMKID', $reqContractingRekananProses1SpmkId);
	  		$kontrak->setField('CONTRACTINGREKANANID', $reqContractingRekananId);
			$kontrak->setField("NOMOR", $reqNomor);
			$kontrak->setField("SPMK_DARI", dateToDBCheck($reqSPMKDari));
			$kontrak->setField("SPMK_SAMPAI", dateToDBCheck($reqSPMKSampai));
			$kontrak->setField("KETERANGAN", $reqKeterangan);
			$kontrak->setField("REKANAN_ID", $reqRekananId);
	  		$kontrak->setField('UPDATED_BY', $this->USER_LOGIN_ID);
			$insert = $kontrak->updateSPMK();
			if ($insert) {
				$this->load->library("librekamjejak");
				$this->librekamjejak->insertRJ('261','Nomor: '.$reqNomor,'null','null','261',$reqContractingRekananId);
				echo "Data berhasil diubah.";
			} else { echo "Data proses gagal diubah."; }
		}
	}

	function addSPK() // Done
	{
		$this->load->model("Contracting");

		$proses1	= new Contracting();
		$kontrak	= new Contracting();
		// echo "<pre>"; print_r($this->input->post()); die();
		$reqId		= $this->input->post('reqId');

		// echo "<pre>"; print_r($this->input->post()); die();
		$reqContractingRekananProses1Id	= $this->input->post('reqContractingRekananProses1Id');
		$reqKode						= $this->input->post('reqKode');
		$reqNilaiKontrak				= $this->input->post('reqNilaiKontrak');
		$reqMetodePembayaran			= $this->input->post('reqMetodePembayaran');
		$reqJenisPengadaan				= $this->input->post('reqJenisPengadaan');
		$reqJenisPekerjaan				= $this->input->post('reqJenisPekerjaan');
		$reqContractingjeniskontrakid	= $this->input->post('reqContractingjeniskontrakid');
		$reqPelaksanaanDari				= $this->input->post('reqPelaksanaanDari');
		$reqPelaksanaanSampai			= $this->input->post('reqPelaksanaanSampai');
		$reqPihak1Nama					= $this->input->post('reqPihak1Nama');
		$reqPihak1Jabatan				= $this->input->post('reqPihak1Jabatan');
		$reqPihak2Nama					= $this->input->post('reqPihak2Nama');
		$reqPihak2Jabatan				= $this->input->post('reqPihak2Jabatan');
		$reqLingkupPekerjaan			= str_replace("'","''",$_POST["reqLingkupPekerjaan"]); // $this->input->post('reqLingkupPekerjaan');
		$reqRekananId					= $this->input->post('reqRekanan');
		$reqJnsKontrak					= $this->input->post('reqJnsKontrak');

		 // Insert 2 table CONTRACTING_REKANAN & CONTRACTING_REKANAN_PROSES1
		$this->load->model("Paket");
		$paket = new Paket();
		$paket->selectByParamsMonitoring(array(), -1, -1, " AND PAKET_ID = '".$reqId."' ");
		$paket->firstRow();
		$paketNama = $paket->getField("NAMA");
		$paketUraian = str_replace("'","''",$paket->getField("URAIAN"));
		$paketNilai = $paket->getField("NILAI");
		$paketJenis = $paket->getField("PAKET_JENIS_ID");

		$proses1->setField("PAKET_ID", $reqId);
		$proses1->setField("NAMA", $paketNama);
		$proses1->setField("URAIAN", $paketUraian);
		$proses1->setField("NILAI", $paketNilai);
		$proses1->setField("CONTRACTINGPROSESID", "1");
		$proses1->setField("REKANAN_ID", $reqRekananId);
		$proses1->setField("JNS_KONTRAK", $reqJnsKontrak);
  		$proses1->setField('CREATED_BY', $this->USER_LOGIN_ID);

  	// $proses1->setField("CONTRACTINGREKANANPROSES1ID", $reqContractingRekananProses1Id);
		$proses1->setField("CR_CODE", $reqKode);
		$proses1->setField("CR_NILAI_KONTRAK", CommaToDot(dotToNo($reqNilaiKontrak)));
		$proses1->setField("CR_METODE_PEMBAYARAN", $reqMetodePembayaran);
		$proses1->setField("CR_JENIS_PENGADAAN", $reqJenisPengadaan);
		$proses1->setField("CR_JENIS_PEKERJAAN", $reqJenisPekerjaan);
		$proses1->setField("CONTRACTINGJENISKONTRAKID", $reqContractingjeniskontrakid);
		$proses1->setField("CR_WAKTU_PELAKSANAAN_DARI", dateToDBCheck($reqPelaksanaanDari));
		$proses1->setField("CR_WAKTU_PELAKSANAAN_SAMPAI", dateToDBCheck($reqPelaksanaanSampai));
		$proses1->setField("CR_PIHAK1_NAMA", $reqPihak1Nama);
		$proses1->setField("CR_PIHAK1_JABATAN", $reqPihak1Jabatan);
		$proses1->setField("CR_PIHAK2_NAMA", $reqPihak2Nama);
		$proses1->setField("CR_PIHAK2_JABATAN", $reqPihak2Jabatan);
		$proses1->setField("CR_LINGKUP_PEKERJAAN", $reqLingkupPekerjaan);
		$proses1->setField("CONTRACTINGSTATUSKONTRAKID", 3);
		$proses1->setField('CR_UPDATED_BY', $this->USER_LOGIN_ID);
		$proses1->setField('STATUS', 0);

		$insert = $proses1->insertContractingSPK();
		if ($insert) { echo "Data berhasil disimpan.";
		} else { echo "Data gagal simpan."; }
	}

	function proseskontrak() // Done
	{
		$this->load->model("Contracting");
		$this->load->model("Contractingrekanan");
		$proses1	= new Contracting();
		$getRekananProses1 = new Contractingrekanan();

		$reqContractingRekananProses1Id	= $this->input->get('reqAidi');
		$reqContractingStatusKontrakId	= $this->input->get('flow');

		$getRekananProses1->selectByParams(array("CONTRACTINGREKANANPROSES1ID" => $reqContractingRekananProses1Id ), -1, -1);
		$getRekananProses1->firstRow();
		$reqContractingRekananId = $getRekananProses1->getField('CONTRACTINGREKANANID');

		$proses1->setField("CONTRACTINGREKANANPROSES1ID", $reqContractingRekananProses1Id);
		$proses1->setField("CONTRACTINGSTATUSKONTRAKID", $reqContractingStatusKontrakId);
		$proses1->setField("CONTRACTINGREKANANID", $reqContractingRekananId);
		$proses1->setField("CREATED_BY", $this->USER_LOGIN_ID);

		$insert = $proses1->updateStatus();

		if($insert) {

			// Rekam Jejak
			switch ($reqContractingStatusKontrakId) {
			 case '1': // Konfirmasi Penyedia
			  	$this->load->library("librekamjejak"); $this->librekamjejak->insertRJ('251','','null','null','251',$reqContractingRekananId);
			   break;
			 case '2': // Approve
			  	$this->load->library("librekamjejak"); $this->librekamjejak->insertRJ('252','','null','null','252',$reqContractingRekananId);
			   break;
			 case '31': // Approve Pemeriksa
			 	$this->load->library("librekamjejak"); $this->librekamjejak->insertRJ('256','','null','null','256',$reqContractingRekananId);
			   break;
			 case '4': // Persetujuan Penyedia Kontrak
			 	$this->load->library("librekamjejak"); $this->librekamjejak->insertRJ('257','','null','null','257',$reqContractingRekananId);
			   break;
			 case '5': // Approve
			 	$this->load->library("librekamjejak"); $this->librekamjejak->insertRJ('258','','null','null','258',$reqContractingRekananId);
			   break;
			 case '51': // kembalikan spk/pks
			 	$this->load->library("librekamjejak"); $this->librekamjejak->insertRJ('259','','null','null','259',$reqContractingRekananId);
			   break;
			 case '6': // Pelaksanaan
			 	$this->load->library("librekamjejak"); $this->librekamjejak->insertRJ('260','','null','null','260',$reqContractingRekananId);
			   break;
			 case '7': // Perubahan Kontrak
			 	$this->load->library("librekamjejak"); $this->librekamjejak->insertRJ('272','','null','null','272',$reqContractingRekananId);
			   break;
			 case '8': // Penyesuaian Harga
			 	$this->load->library("librekamjejak"); $this->librekamjejak->insertRJ('273','','null','null','273',$reqContractingRekananId);
			   break;
			 case '9': // Keadaan Kahar
			 	$this->load->library("librekamjejak"); $this->librekamjejak->insertRJ('274','','null','null','274',$reqContractingRekananId);
			   break;
			 case '10': // Berakhir Kontrak
			 	$this->load->library("librekamjejak"); $this->librekamjejak->insertRJ('275','','null','null','275',$reqContractingRekananId);
			   break;
			 case '11': // Pemutusan Kontrak
			 	$this->load->library("librekamjejak"); $this->librekamjejak->insertRJ('276','','null','null','276',$reqContractingRekananId);
			   break;
			 case '12': // Pemberian Kesempatan
			 	$this->load->library("librekamjejak"); $this->librekamjejak->insertRJ('277','','null','null','277',$reqContractingRekananId);
			   break;
			 case '13': // Denda dan Ganti Rugi
			 	$this->load->library("librekamjejak"); $this->librekamjejak->insertRJ('278','','null','null','278',$reqContractingRekananId);
			   break;
			 case '100': // Penutupan Kontrak
			 	$this->load->library("librekamjejak"); $this->librekamjejak->insertRJ('265','','null','null','265',$reqContractingRekananId);
			   break;
			 case '200': // Selesai Kontrak
			 	$this->load->library("librekamjejak"); $this->librekamjejak->insertRJ('267','','null','null','267',$reqContractingRekananId);
			   break;
			 case '110': // Proses Approval Kasubdit
			 	$this->load->library("librekamjejak"); $this->librekamjejak->insertRJ('902','','null','null','902',$reqContractingRekananId);
			   break;
			 case '111': // Proses Approval PPK
			 	$this->load->library("librekamjejak"); $this->librekamjejak->insertRJ('903','','null','null','903',$reqContractingRekananId);
			   break;
			 case '112': // PPK Approve
			 	$this->load->library("librekamjejak"); $this->librekamjejak->insertRJ('904','','null','null','904',$reqContractingRekananId);
			   break;
			 case '113': // Approval Kasubdit
			 	$this->load->library("librekamjejak"); $this->librekamjejak->insertRJ('905','','null','null','905',$reqContractingRekananId);
			   break;
			 case '114': // Tolak Kasubdit (Kontrak)
			 	$this->load->library("librekamjejak"); $this->librekamjejak->insertRJ('906','','null','null','906',$reqContractingRekananId);
			   break;
			 case '115': // Kasubdit menolak SPPBJ
			 	$this->load->library("librekamjejak"); $this->librekamjejak->insertRJ('908','','null','null','908',$reqContractingRekananId);
			   break;
			 case '116': // PPK menolak SPPBJ
			 	$this->load->library("librekamjejak"); $this->librekamjejak->insertRJ('909','','null','null','909',$reqContractingRekananId);
			   break;

			 default:
			   break;
			}
			// End Insert Rekam Jejak

			$arrJson["PESAN"] = "Data berhasil diproses";
			$arrJson["FLOW"] = $reqContractingStatusKontrakId;

		}
		else {
			$arrJson["PESAN"] = "Data gagal diproses, silahkan dicoba kembali..!";
			$arrJson["FLOW"] = '0';
		}

		echo json_encode($arrJson);

	}

	function proseskontrakmulti() // Done
	{
		$this->load->model("Contracting");
		$this->load->model("Contractingrekanan");
		$proses1	= new Contracting();
		$getRekananProses1 = new Contractingrekanan();


		if ($this->USER_TYPE_ID == '6') { // Penyedia
			$reqContractingRekananProses1Id	= $this->input->get('reqAidi');
			$reqContractingStatusKontrakId	= $this->input->get('flow');

			$getRekananProses1->selectByParams(array("CONTRACTINGREKANANPROSES1ID" => $reqContractingRekananProses1Id ), -1, -1);
			$getRekananProses1->firstRow();
			$reqContractingRekananId = $getRekananProses1->getField('CONTRACTINGREKANANID');

			$proses1->setField("CONTRACTINGREKANANPROSES1ID", $reqContractingRekananProses1Id);
			$proses1->setField("CONTRACTINGSTATUSKONTRAKID", $reqContractingStatusKontrakId);
			$proses1->setField("CONTRACTINGREKANANID", $reqContractingRekananId);
			$proses1->setField("CREATED_BY", $this->USER_LOGIN_ID);
		} else { // Pengelola Kontrak
			$reqContractingRekananId	= $this->input->get('reqAidi');
			$reqContractingStatusKontrakId	= $this->input->get('flow');
			$getRekananProses1->selectByParams(array("A.CONTRACTINGREKANANID" => $reqContractingRekananId ), -1, -1);
			$getRekananProses1->firstRow();
			$reqContractingRekananProses1Id = $getRekananProses1->getField('CONTRACTINGREKANANPROSES1ID');

			$proses1->setField("CONTRACTINGREKANANPROSES1ID", $reqContractingRekananProses1Id);
			$proses1->setField("CONTRACTINGSTATUSKONTRAKID", $reqContractingStatusKontrakId);
			$proses1->setField("CONTRACTINGREKANANID", $reqContractingRekananId);
			$proses1->setField("CREATED_BY", $this->USER_LOGIN_ID);
		}

		$insert = $proses1->updateStatusMulti();


		if($insert) {

			// Rekam Jejak
			switch ($reqContractingStatusKontrakId) {
			 case '1': // Konfirmasi Penyedia
			  	$this->load->library("librekamjejak"); $this->librekamjejak->insertRJ('251','','null','null','251',$reqContractingRekananId);
			   break;
			 case '2': // Approve
			  	$this->load->library("librekamjejak"); $this->librekamjejak->insertRJ('252','','null','null','252',$reqContractingRekananId);
			   break;
			 case '31': // Approve Pemeriksa
			 	$this->load->library("librekamjejak"); $this->librekamjejak->insertRJ('256','','null','null','256',$reqContractingRekananId);
			   break;
			 case '4': // Persetujuan Penyedia Kontrak
			 	$this->load->library("librekamjejak"); $this->librekamjejak->insertRJ('257','','null','null','257',$reqContractingRekananId);
			   break;
			 case '5': // Approve
			 	$this->load->library("librekamjejak"); $this->librekamjejak->insertRJ('258','','null','null','258',$reqContractingRekananId);
			   break;
			 case '51': // kembalikan spk/pks
			 	$this->load->library("librekamjejak"); $this->librekamjejak->insertRJ('259','','null','null','259',$reqContractingRekananId);
			   break;
			 case '6': // Pelaksanaan
			 	$this->load->library("librekamjejak"); $this->librekamjejak->insertRJ('260','','null','null','260',$reqContractingRekananId);
			   break;
			 case '7': // Perubahan Kontrak
			 	$this->load->library("librekamjejak"); $this->librekamjejak->insertRJ('272','','null','null','272',$reqContractingRekananId);
			   break;
			 case '8': // Penyesuaian Harga
			   break;
			 	$this->load->library("librekamjejak"); $this->librekamjejak->insertRJ('273','','null','null','273',$reqContractingRekananId);
			 case '9': // Keadaan Kahar
			 	$this->load->library("librekamjejak"); $this->librekamjejak->insertRJ('274','','null','null','274',$reqContractingRekananId);
			   break;
			 case '10': // Berakhir Kontrak
			 	$this->load->library("librekamjejak"); $this->librekamjejak->insertRJ('275','','null','null','275',$reqContractingRekananId);
			   break;
			 case '11': // Pemutusan Kontrak
			 	$this->load->library("librekamjejak"); $this->librekamjejak->insertRJ('276','','null','null','276',$reqContractingRekananId);
			   break;
			 case '12': // Pemberian Kesempatan
			 	$this->load->library("librekamjejak"); $this->librekamjejak->insertRJ('277','','null','null','277',$reqContractingRekananId);
			   break;
			 case '13': // Denda dan Ganti Rugi
			 	$this->load->library("librekamjejak"); $this->librekamjejak->insertRJ('278','','null','null','278',$reqContractingRekananId);
			   break;
			 case '100': // Penutupan Kontrak
			 	$this->load->library("librekamjejak"); $this->librekamjejak->insertRJ('265','','null','null','265',$reqContractingRekananId);
			   break;
			 case '200': // Selesai Kontrak
			 	$this->load->library("librekamjejak"); $this->librekamjejak->insertRJ('267','','null','null','267',$reqContractingRekananId);
			   break;

			 default:
			   break;
			}
			// End Insert Rekam Jejak

			$arrJson["PESAN"] = "Data berhasil diproses";
			$arrJson["FLOW"] = $reqContractingStatusKontrakId;
		}
		else {
			$arrJson["PESAN"] = "Data gagal diproses, silahkan dicoba kembali!";
			$arrJson["FLOW"] = '0';
		}

		echo json_encode($arrJson);

	}

	public function addfileDaftarHitam() // Done
	{
		$this->load->library("kauth");  $userLogin = new kauth();
		$this->load->model("Blacklistkontrak");
		$this->load->library("FileHandler");

		$file = new FileHandler();
		$insertBlacklist = new Blacklistkontrak();

		$reqSubmit = $this->input->post("reqSubmit");
		$reqNoSK = $this->input->post("reqNoSK");
		$reqJudul = $this->input->post("reqJudul");
		$reqKeterangan = $this->input->post("reqKeterangan");
		$reqTanggalBerlaku = $this->input->post("reqTanggalBerlaku");
		$contractingrekananid = $this->input->post("contractingrekananid");
		$reqRekananId = $this->input->post("reqRekananId");
		$reqLinkFile= $_FILES['reqLinkFile'];
    	$reqLinkFileTemp    = $_POST["reqLinkFileTemp"];

		$FILE_DIR = "uploads/kontrak/";

		/* UPLOAD FILE */
		$renameFile = '__SK_Blacklist_Kontrak_'.md5(date("dmYHis").$reqLinkFile['name'].$this->ID).".".getExtension($reqLinkFile['name']);
		if($file->uploadToDir('reqLinkFile', $FILE_DIR, $renameFile))
		{
			$insertLinkFilesSize = $file->uploadedSize;
			$insertLinkFilesExe =  $file->uploadedExtension;
			$insertLinkFile =  $renameFile;
		}
		else
		{
			$insertLinkFilesSize = $reqLinkFileTempUkuran;
			$insertLinkFilesExe =  $reqLinkFileTempTipe;
			$insertLinkFile =  $reqLinkFileTemp;
		}
		/* END UPLOAD FILE */

		$insertBlacklist->setField("REKANAN_ID", $reqRekananId);
		$insertBlacklist->setField("CONTRACTING_REKANAN_ID", $contractingrekananid);
		$insertBlacklist->setField("JUDUL", $reqJudul);
		$insertBlacklist->setField("KETERANGAN", $reqKeterangan);
		$insertBlacklist->setField("TANGGAL_BERLAKU", dateToDBCheck($reqTanggalBerlaku));
		$insertBlacklist->setField("FILE", $insertLinkFile);
		$insertBlacklist->setField("NO_SK", $reqNoSK);
		$insertBlacklist->setField("CREATED_BY", $this->USER_LOGIN_ID);

		if ($reqSubmit == 'insert') {
			$insert = $insertBlacklist->insert();
		} else {
			$insert = $insertBlacklist->update();
		}

		if($insert)
			echo "Dokumen berhasil disimpan.";
		else
			echo "Dokumen gagal disimpan, silahkan dicoba kembali!";

	}

	public function addfile() // Done
	{
		$this->load->library("kauth");  $userLogin = new kauth();
		$this->load->model("Contractingfile");
		$this->load->library("FileHandler");

		$file = new FileHandler();
		$cfile = new Contractingfile();

		// echo "<pre>"; print_r($this->input->post()); die();
		$contractingrekananid = $this->input->post("contractingrekananid");
		$contractingprosesid = $this->input->post("contractingprosesid");
		$reqLinkFile= $_FILES['reqLinkFile'];

		$FILE_DIR = "uploads/kontrak/";

		/* UPLOAD FILE */
		$renameFile = md5(date("dmYHis").$reqLinkFile['name'].$this->ID).".".getExtension($reqLinkFile['name']);
		if($file->uploadToDir('reqLinkFile', $FILE_DIR, $renameFile))
		{
			$insertLinkFilesSize = $file->uploadedSize;
			$insertLinkFilesExe =  $file->uploadedExtension;
			$insertLinkFile =  $renameFile;
		}
		else
		{
			$insertLinkFilesSize = $reqLinkFileTempUkuran;
			$insertLinkFilesExe =  $reqLinkFileTempTipe;
			$insertLinkFile =  $reqLinkFileTemp;
		}
		/* END UPLOAD FILE */

		$reqNama = $this->input->post("reqNama");
		$reqJenis = $this->input->post("reqJenis");
		$reqKeterangan = $this->input->post("reqKeterangan");
		$reqPublishPenyedia = $this->input->post("reqPublishPenyedia");

		$cfile->setField("CONTRACTINGREKANANID", $contractingrekananid);
		$cfile->setField("CONTRACTINGPROSESID", $contractingprosesid);
		$cfile->setField("FILE_NAMA", $reqNama);
		$cfile->setField("FILE_NAMA_ENCRYPT", $insertLinkFile);
		$cfile->setField("FILE_PATH", $FILE_DIR);
		$cfile->setField("FILE_EXTENTION", $insertLinkFilesExe);
		$cfile->setField("FILE_SIZE", $insertLinkFilesSize);
		$cfile->setField("FILE_TANGGAL", dateToDBCheck(date('d-m-Y')));
		$cfile->setField("FILE_JENIS", $reqJenis);
		$cfile->setField("FILE_KETERANGAN", $reqKeterangan);
		$cfile->setField("FILE_PUBLISH_PENYEDIA", $reqPublishPenyedia);
		$cfile->setField("CREATED_BY", $this->USER_LOGIN_ID);
		$insert = $cfile->insertFile();

		if($insert)
			echo "Dokumen berhasil disimpan.";
		else
			echo "Dokumen gagal disimpan, silahkan dicoba kembali!";

	}

	public function addfileMulti() // Done
	{
		$this->load->library("kauth");  $userLogin = new kauth();
		$this->load->model("Contractingfile");
		$this->load->library("FileHandler");

		$file = new FileHandler();
		$cfile = new Contractingfile();

		// echo "<pre>"; print_r($this->input->post()); die();
		$contractingrekananid = $this->input->post("contractingrekananid");
		$contractingprosesid = $this->input->post("contractingprosesid");
		$reqRekananId = $this->input->post("reqRekananId") ?: 0;
		$reqLinkFile= $_FILES['reqLinkFile'];

		$FILE_DIR = "uploads/kontrak/";

		/* UPLOAD FILE */
		$renameFile = md5(date("dmYHis").$reqLinkFile['name'].$this->ID).".".getExtension($reqLinkFile['name']);
		if($file->uploadToDir('reqLinkFile', $FILE_DIR, $renameFile))
		{
			$insertLinkFilesSize = $file->uploadedSize;
			$insertLinkFilesExe =  $file->uploadedExtension;
			$insertLinkFile =  $renameFile;
		}
		else
		{
			$insertLinkFilesSize = $reqLinkFileTempUkuran;
			$insertLinkFilesExe =  $reqLinkFileTempTipe;
			$insertLinkFile =  $reqLinkFileTemp;
		}
		/* END UPLOAD FILE */

		$reqNama = $this->input->post("reqNama");
		$reqJenis = $this->input->post("reqJenis");
		$reqKeterangan = $this->input->post("reqKeterangan");
		$reqPublishPenyedia = $this->input->post("reqPublishPenyedia");

		$cfile->setField("CONTRACTINGREKANANID", $contractingrekananid);
		$cfile->setField("CONTRACTINGPROSESID", $contractingprosesid);
		$cfile->setField("REKANAN_ID", $reqRekananId);
		$cfile->setField("FILE_NAMA", $reqNama);
		$cfile->setField("FILE_NAMA_ENCRYPT", $insertLinkFile);
		$cfile->setField("FILE_PATH", $FILE_DIR);
		$cfile->setField("FILE_EXTENTION", $insertLinkFilesExe);
		$cfile->setField("FILE_SIZE", $insertLinkFilesSize);
		$cfile->setField("FILE_TANGGAL", dateToDBCheck(date('d-m-Y')));
		$cfile->setField("FILE_JENIS", $reqJenis);
		$cfile->setField("FILE_KETERANGAN", $reqKeterangan);
		$cfile->setField("FILE_PUBLISH_PENYEDIA", $reqPublishPenyedia);
		$cfile->setField("CREATED_BY", $this->USER_LOGIN_ID);
		$insert = $cfile->insertFileMulti();

		if($insert)
			echo "Dokumen berhasil disimpan.";
		else
			echo "Dokumen gagal disimpan, silahkan dicoba kembali!";

	}

	public function addfilejaminan() // Done
	{
		$this->load->library("kauth");  $userLogin = new kauth();
		$this->load->model("Contractingjaminan");
		$this->load->library("FileHandler");

		$file = new FileHandler();
		$cfile = new Contractingjaminan();

		$contractingrekananid = $this->input->post("contractingrekananid");
		$paketid = $this->input->post("paketid");
		$reqLinkFile= $_FILES['reqLinkFile'];
    	$reqLinkFileTemp    = $_POST["reqLinkFileTemp"];

		$FILE_DIR = "uploads/kontrak/";

		/* UPLOAD FILE */
		$renameFile = 'Jaminan_'.$contractingrekananid.'_'.md5(date("dmYHis").$reqLinkFile['name'].$this->ID).".".getExtension($reqLinkFile['name']);
		if($file->uploadToDir('reqLinkFile', $FILE_DIR, $renameFile))
		{
			$insertLinkFilesSize = $file->uploadedSize;
			$insertLinkFilesExe =  $file->uploadedExtension;
			$insertLinkFile =  $renameFile;
		}
		else
		{
			$insertLinkFilesSize = $reqLinkFileTempUkuran;
			$insertLinkFilesExe =  $reqLinkFileTempTipe;
			$insertLinkFile =  $reqLinkFileTemp;
		}
		/* END UPLOAD FILE */

		$reqNomor = $this->input->post("reqNomor");
		$reqTanggal = $this->input->post("reqTanggal");
		$reqMode = $this->input->post("reqMode");
		$contractingjaminanid = $this->input->post("contractingjaminanid");

		$cfile->setField("CONTRACTING_JAMINAN_ID", $contractingjaminanid);
		$cfile->setField("CONTRACTINGREKANANID", $contractingrekananid);
		$cfile->setField("PAKET_ID", $paketid);
		$cfile->setField("NOMOR", $reqNomor);
		$cfile->setField("TANGGAL_JAMINAN", dateToDBCheck($reqTanggal));
		$cfile->setField("FILE_JAMINAN", $insertLinkFile);
		$cfile->setField("CREATED_BY", $this->USER_LOGIN_ID);

		if ($reqMode == 'simpan') {
			$insert = $cfile->insertJaminan();
		} else {
			$insert = $cfile->updateJaminan();
		}

		if ($insert) { echo "Data berhasil disimpan."; 
		// End update Status
		} else { echo "Data gagal disimpan, silahkan coba kembali."; }

	}

	public function addfilejaminanAll() // Done
	{
		$this->load->library("kauth");  $userLogin = new kauth();
		$this->load->model("Contractingjaminan");
		$this->load->library("FileHandler");

		$file = new FileHandler();
		$cfile = new Contractingjaminan();

		$contractingrekananid = $this->input->post("contractingrekananid");
		$paketid = $this->input->post("paketid");
		$reqLinkFile= $_FILES['reqLinkFile'];
    	$reqLinkFileTemp    = $_POST["reqLinkFileTemp"];

		$FILE_DIR = "uploads/kontrak/";

		/* UPLOAD FILE */
		$renameFile = 'Jaminan_'.$contractingrekananid.'_'.md5(date("dmYHis").$reqLinkFile['name'].$this->ID).".".getExtension($reqLinkFile['name']);
		if($file->uploadToDir('reqLinkFile', $FILE_DIR, $renameFile))
		{
			$insertLinkFilesSize = $file->uploadedSize;
			$insertLinkFilesExe =  $file->uploadedExtension;
			$insertLinkFile =  $renameFile;
		}
		else
		{
			$insertLinkFilesSize = $reqLinkFileTempUkuran;
			$insertLinkFilesExe =  $reqLinkFileTempTipe;
			$insertLinkFile =  $reqLinkFileTemp;
		}

		$reqLinkFile2= $_FILES['reqLinkFile2'];
    	$reqLinkFile2Temp    = $_POST["reqLinkFile2Temp"];

		$FILE_DIR = "uploads/kontrak/";

		/* UPLOAD FILE */
		$renameFile2 = 'Konfirmasi_'.$contractingrekananid.'_'.md5(date("dmYHis").$reqLinkFile2['name'].$this->ID).".".getExtension($reqLinkFile2['name']);
		if($file->uploadToDir('reqLinkFile2', $FILE_DIR, $renameFile2))
		{
			$insertLinkFilesSize = $file->uploadedSize;
			$insertLinkFilesExe =  $file->uploadedExtension;
			$insertLinkFile2 =  $renameFile2;
		}
		else
		{
			$insertLinkFilesSize = $reqLinkFile2TempUkuran;
			$insertLinkFilesExe =  $reqLinkFile2TempTipe;
			$insertLinkFile2 =  $reqLinkFile2Temp;
		}

		$reqNomor = $this->input->post("reqNomor");
		$reqTanggal = $this->input->post("reqTanggal");
		$reqTanggalKonfirmasiKebank = $this->input->post("reqTanggalKonfirmasiKebank");
		$reqTanggalKonfirmasiOlehBank = $this->input->post("reqTanggalKonfirmasiOlehBank");
		$konfirmasi = $this->input->post("konfirmasi");
		$reqMode = $this->input->post("reqMode");
		$contractingjaminanid = $this->input->post("contractingjaminanid");

		$cfile->setField("CONTRACTING_JAMINAN_ID", $contractingjaminanid);
		$cfile->setField("CONTRACTINGREKANANID", $contractingrekananid);
		$cfile->setField("PAKET_ID", $paketid);
		$cfile->setField("NOMOR", $reqNomor);
		$cfile->setField("TANGGAL_JAMINAN", dateToDBCheck($reqTanggal));
		$cfile->setField("TANGGAL_KONFIRMASI_KEBANK", dateToDBCheck($reqTanggalKonfirmasiKebank));
		$cfile->setField("TANGGAL_KONFIRMASI_OLEH_BANK", dateToDBCheck($reqTanggalKonfirmasiOlehBank));
		$cfile->setField("KONFIRMASI", $konfirmasi);
		$cfile->setField("FILE_JAMINAN", $insertLinkFile);
		$cfile->setField("FILE_KONFIRMASI", $insertLinkFile2);
		$cfile->setField("CREATED_BY", $this->USER_LOGIN_ID);

		if ($reqMode == 'simpan') {
			$insert = $cfile->insertJaminan();
		} else {
			$insert = $cfile->updateJaminanAll();
		}

		if ($insert) { echo "Data berhasil disimpan."; 
		// End update Status
		} else { echo "Data gagal disimpan, silahkan coba kembali."; }

	}

	public function addfilejaminanAllUpdate() // Done
	{
		$this->load->library("kauth");  $userLogin = new kauth();
		$this->load->model("Contractingjaminan");
		$cfile = new Contractingjaminan();

		$contractingrekananid = $this->input->post("contractingrekananid");
		$paketid = $this->input->post("paketid");
		$konfirmasi = $this->input->post("konfirmasi");
		$reqMode = $this->input->post("reqMode");
		$contractingjaminanid = $this->input->post("contractingjaminanid");

		$cfile->setField("CONTRACTING_JAMINAN_ID", $contractingjaminanid);
		$cfile->setField("CONTRACTINGREKANANID", $contractingrekananid);
		$cfile->setField("KONFIRMASI", $konfirmasi);
		$cfile->setField("CREATED_BY", $this->USER_LOGIN_ID);

		$insert = $cfile->updateJaminanAllUpdate();

		if ($insert) { echo "Data berhasil disimpan."; 
		// End update Status
		} else { echo "Data gagal disimpan, silahkan coba kembali."; }

	}

	public function addfileImportExcel()
	{
		$this->load->library("kauth");  $userLogin = new kauth();
		$this->load->model(array("Contractingfile","Contractingmaterial","Satuan"));
		$this->load->library("FileHandler");
		require_once APPPATH . "/third_party/PHPExcel.php";

		$file = new FileHandler();
		$cfile = new Contractingfile();

		// echo "<pre>"; print_r($this->input->post()); die();
		$contractingrekananid = $this->input->post("contractingrekananid");
		$contractingprosesid = $this->input->post("contractingprosesid");
		$reqLinkFile= $_FILES['reqLinkFile'];

		$FILE_DIR = "uploads/kontrak/";

		/* UPLOAD FILE */
		$renameFile = md5(date("dmYHis").$reqLinkFile['name'].$this->ID).".".getExtension($reqLinkFile['name']);
		if($file->uploadToDir('reqLinkFile', $FILE_DIR, $renameFile))
		{
			$insertLinkFilesSize = $file->uploadedSize;
			$insertLinkFilesExe =  $file->uploadedExtension;
			$insertLinkFile =  $renameFile;

			// Import Excel Jika ada file baru yang di upload
			$inputFileName = $FILE_DIR.$insertLinkFile;
			$sheetname = 'Barang Jasa';

			try {
                $inputFileType = PHPExcel_IOFactory::identify($inputFileName);
                $objReader = PHPExcel_IOFactory::createReader($inputFileType);
				$objReader->setLoadSheetsOnly($sheetname);
                $objPHPExcel = $objReader->load($inputFileName);
				// $sheetCount = $objPHPExcel->getSheetCount();
				// $sheetNames = $objPHPExcel->getSheetNames();
                $allDataInSheet = $objPHPExcel->getActiveSheet()->toArray(null, true, true, true);
				// echo "<pre>"; print_r($allDataInSheet); die;

                /**
                 * B: No
                 * C: Deskripsi
                 * D: Vol/Qty
                 * E: Satuan
                 * F: Harga Satuan
                **/

                foreach ($allDataInSheet as $key => $value) {
                	if ($key >= 7) {
	                	$dataSatuan	= new Satuan();
	                	$dataSatuan->selectByParams(array("KODE" => str_replace(" ","",$value['E'])));
						$dataSatuan->firstRow();
						$satuan = $dataSatuan->getField("SATUANID");

	                	if ($value['C']) {
		                	$dataMaterial	= new Contractingmaterial();
							$dataMaterial->setField("CONTRACTINGREKANANID", $contractingrekananid);
							$dataMaterial->setField("NAMA", $value['C']);
							$dataMaterial->setField("QTY", $value['D']);
							$dataMaterial->setField("SATUANID", $satuan);
							$dataMaterial->setField("HARGA_SATUAN", str_replace(",","",$value['F']));
							$dataMaterial->setField("SIFAT", "2"); // Sifat 2=Tetap
							$dataMaterial->setField('CREATED_BY', $this->USER_LOGIN_ID);
							$insert = $dataMaterial->inserMaterial();

							$filepath = 'logs/importmaterial/importlog_' . date('Y-m-d') . '.txt';
							$textNya   = $value['C']." ### ".$value['D']." ### ".$value['E']." ### ".$value['F']." ### ".$this->USER_LOGIN_ID." ### ".$contractingrekananid." ### ".date('Y-m-d H:i:s');

							if ($insert) {
								$handle = fopen($filepath, "a+");
								$text = "Sukses: ".$textNya;
								$arr = array(' ', '<br>');
								$logtext = str_replace($arr, "", $text);
							} else {
								$handle = fopen($filepath, "a+");
								$text = "Gagal: ".$textNya;
								$arr = array(' ', '<br>');
								$logtext = str_replace($arr, "", $text);
							}

							fwrite($handle, $logtext . "\r\n");
							fclose($handle);
				          	unset($dataMaterial);
	                	}
                	}
              	}
         	} catch (Exception $e) {
            	die('Error loading file "' . pathinfo($inputFileName, PATHINFO_BASENAME)
                        . '": ' .$e->getMessage());
        	}

			// End Import Excel

		}
		else
		{
			$insertLinkFilesSize = $reqLinkFileTempUkuran;
			$insertLinkFilesExe =  $reqLinkFileTempTipe;
			$insertLinkFile =  $reqLinkFileTemp;
		}
		/* END UPLOAD FILE */


		$reqNama = $this->input->post("reqNama");
		$reqJenis = $this->input->post("reqJenis");
		$reqKeterangan = $this->input->post("reqKeterangan");
		$reqPublishPenyedia = $this->input->post("reqPublishPenyedia");

		$cfile->setField("CONTRACTINGREKANANID", $contractingrekananid);
		$cfile->setField("CONTRACTINGPROSESID", $contractingprosesid);
		$cfile->setField("FILE_NAMA", $reqNama);
		$cfile->setField("FILE_NAMA_ENCRYPT", $insertLinkFile);
		$cfile->setField("FILE_PATH", $FILE_DIR);
		$cfile->setField("FILE_EXTENTION", $insertLinkFilesExe);
		$cfile->setField("FILE_SIZE", $insertLinkFilesSize);
		$cfile->setField("FILE_TANGGAL", dateToDBCheck(date('d-m-Y')));
		$cfile->setField("FILE_JENIS", $reqJenis);
		$cfile->setField("FILE_KETERANGAN", $reqKeterangan);
		$cfile->setField("FILE_PUBLISH_PENYEDIA", $reqPublishPenyedia);
		$cfile->setField("CREATED_BY", $this->USER_LOGIN_ID);
		$insert = $cfile->insertFile();

		if($insert)
			echo "Dokumen berhasil disimpan.";
		else
			echo "Dokumen gagal disimpan, silahkan dicoba kembali!";

	}

	function addSPKPKS() // Done
	{
		$this->load->model("Contracting");
		$proses1	= new Contracting();
		// echo "<pre>"; print_r($this->input->post()); die();
		$reqCode						= $this->input->post('reqCode');
		$reqNilaiKontrak				= $this->input->post('reqNilaiKontrak');
		$reqMetodePembayaran			= $this->input->post('reqMetodePembayaran');
		$reqJenisPengadaan				= $this->input->post('reqJenisPengadaan');
		$reqJenisPekerjaan				= $this->input->post('reqJenisPekerjaan');
		$reqContractingjeniskontrakid	= $this->input->post('reqContractingjeniskontrakid');
		$reqPelaksanaanDari				= $this->input->post('reqPelaksanaanDari');
		$reqPelaksanaanSampai			= $this->input->post('reqPelaksanaanSampai');
		$reqPihak1Nama					= $this->input->post('reqPihak1Nama');
		$reqPihak1Jabatan				= $this->input->post('reqPihak1Jabatan');
		$reqPihak2Nama					= $this->input->post('reqPihak2Nama');
		$reqPihak2Jabatan				= $this->input->post('reqPihak2Jabatan');
		$reqLingkupPekerjaan			= str_replace("'","''",$_POST["reqLingkupPekerjaan"]); // $this->input->post('reqLingkupPekerjaan');
		$reqContractingRekananProses1Id	= $this->input->post('reqContractingRekananProses1Id');
		$reqContractingRekananId		= $this->input->post('reqContractingRekananId');

		$reqLegalNomorPKS			= $this->input->post('reqLegalNomorPKS');
		$reqLegalTanggal			= $this->input->post('reqLegalTanggal');
		$reqLegalNomorRekanan		= $this->input->post('reqLegalNomorRekanan');
		$reqLegalTanggalRekanan		= $this->input->post('reqLegalTanggalRekanan');

		$reqPO		= $this->input->post('reqPO');
		$reqTanggalHasilPemilihan		= $this->input->post('reqTanggalHasilPemilihan');
		$reqPenyelesaianAwal		= $this->input->post('reqPenyelesaianAwal');
		$reqPenyelesaianAkhir		= $this->input->post('reqPenyelesaianAkhir');
		$reqMasaGaransi				= $this->input->post('reqMasaGaransi');
		$reqMasaGaransiPeriode		= $this->input->post('reqMasaGaransiPeriode');
		$reqContractingStatusKontrakId = $this->input->post('reqContractingStatusKontrakId');
		$reqNamaKegiatan			= $this->input->post('reqNamaKegiatan');

		$proses1->setField("CONTRACTINGREKANANID", $reqContractingRekananId);
		$proses1->setField("CONTRACTINGREKANANPROSES1ID", $reqContractingRekananProses1Id);
		$proses1->setField("CR_CODE", $reqCode);
		$proses1->setField("CR_NILAI_KONTRAK", CommaToDot(dotToNo($reqNilaiKontrak)));
		$proses1->setField("CR_METODE_PEMBAYARAN", $reqMetodePembayaran);
		$proses1->setField("CR_JENIS_PENGADAAN", $reqJenisPengadaan);
		$proses1->setField("CR_JENIS_PEKERJAAN", $reqJenisPekerjaan);
		$proses1->setField("CONTRACTINGJENISKONTRAKID", $reqContractingjeniskontrakid);
		$proses1->setField("CR_WAKTU_PELAKSANAAN_DARI", dateToDBCheck($reqPelaksanaanDari));
		$proses1->setField("CR_WAKTU_PELAKSANAAN_SAMPAI", dateToDBCheck($reqPelaksanaanSampai));
		$proses1->setField("CR_PIHAK1_NAMA", $reqPihak1Nama);
		$proses1->setField("CR_PIHAK1_JABATAN", $reqPihak1Jabatan);
		$proses1->setField("CR_PIHAK2_NAMA", $reqPihak2Nama);
		$proses1->setField("CR_PIHAK2_JABATAN", $reqPihak2Jabatan);
		$proses1->setField("CR_LINGKUP_PEKERJAAN", $reqLingkupPekerjaan);
		if ($reqContractingStatusKontrakId == '51' || $reqContractingStatusKontrakId == '6') { // status tetap tidak berubah
			$proses1->setField("CONTRACTINGSTATUSKONTRAKID", $reqContractingStatusKontrakId);
		} else {
			$proses1->setField("CONTRACTINGSTATUSKONTRAKID", 3);
		}
		$proses1->setField('CR_UPDATED_BY', $this->USER_LOGIN_ID);

		$proses1->setField("CR_LEGAL_NOMOR_PKS", $reqLegalNomorPKS);
		$proses1->setField("CR_LEGAL_TANGGAL", dateToDBCheck($reqLegalTanggal));
		$proses1->setField("CR_LEGAL_NOMOR_REKANAN", $reqLegalNomorRekanan);
		$proses1->setField("CR_PO", $reqPO);
		$proses1->setField("CR_TGL_HASIL_TERIMA_PEMILIHAN", dateToDBCheck($reqTanggalHasilPemilihan));
		$proses1->setField("CR_PENYELESAIAN_KONTRAK_AWAL", dateToDBCheck($reqPenyelesaianAwal));
		$proses1->setField("CR_PENYELESAIAN_KONTRAK_AKHIR", dateToDBCheck($reqPenyelesaianAkhir));
		$proses1->setField("CR_MASA_GARANSI", $reqMasaGaransi);
		$proses1->setField("CR_MASA_GARANSI_PERIODE", $reqMasaGaransiPeriode);
		$proses1->setField("CR_NAMA_KEGIATAN", $reqNamaKegiatan);

		// $proses1->setField("CR_LEGAL_TANGGAL_REKANAN", dateToDBCheck($reqLegalTanggalRekanan));

		$insert = $proses1->updateProses1Kontrak();
		if ($insert) {
			$this->load->library("librekamjejak");
			$this->librekamjejak->insertRJ('253','Nomor: '.$reqLegalNomorPKS,'null','null','253',$reqContractingRekananId);
			echo "0--Data berhasil di simpan.";
		} else { echo "1--Data gagal di simpan."; }
	}

	function addLegal() //
	{
		$this->load->model("Contracting");
		$proses1	= new Contracting();
		// echo "<pre>"; print_r($this->input->post()); die();
		$reqLegalNomorPKS			= $this->input->post('reqLegalNomorPKS');
		$reqLegalTanggal			= $this->input->post('reqLegalTanggal');
		$reqLegalNomorRekanan		= $this->input->post('reqLegalNomorRekanan');
		// $reqLegalTanggalRekanan		= $this->input->post('reqLegalTanggalRekanan');
		$reqContractingRekananProses1Id	= $this->input->post('reqContractingRekananProses1Id');
		$reqContractingRekananId		= $this->input->post('reqContractingRekananId');

		$proses1->setField("CONTRACTINGREKANANID", $reqContractingRekananId);
		$proses1->setField("CONTRACTINGREKANANPROSES1ID", $reqContractingRekananProses1Id);
		$proses1->setField("CR_LEGAL_NOMOR_PKS", $reqLegalNomorPKS);
		$proses1->setField("CR_LEGAL_TANGGAL", dateToDBCheck($reqLegalTanggal));
		$proses1->setField("CR_LEGAL_NOMOR_REKANAN", $reqLegalNomorRekanan);
		$proses1->setField("CR_LEGAL_TANGGAL_REKANAN", dateToDBCheck($reqLegalTanggalRekanan));
		$proses1->setField("CONTRACTINGJENISKONTRAKID", $reqContractingjeniskontrakid);
		$proses1->setField('CR_LEGAL_UPDATED_BY', $this->USER_LOGIN_ID);
		$insert = $proses1->updateProses1Legal();
		if ($insert) { echo "Data berhasil di simpan.";
		} else { echo "Data gagal di simpan."; }
	}

	function addDeliverable() // Done
	{
		$this->load->model("Contractingdeliverable");
		// echo "<pre>"; print_r($this->input->post()); die();
		$contractingrekananid	= $this->input->post('contractingrekananid');
		$lingkup				= $_POST["lingkup"];
		$deliveryname			= $_POST["deliveryname"];
		$status					= $_POST["status"];
		$tanggalDeliveryDari	= $this->input->post('reqTanggalDeliveryDari');
		$tanggalDeliverySampai	= $this->input->post('reqTanggalDeliverySampai');


		// hapus data semua dulu kemudian insert
		$delivery2	= new Contractingdeliverable();
		$delivery2->setField("CONTRACTINGREKANANID", $contractingrekananid);
		$delivery2->delAll();
      	unset($delivery2);

		for($i=0; $i<count($lingkup);$i++)
        {
			$delivery	= new Contractingdeliverable();
			$delivery->setField("CONTRACTINGREKANANID", $contractingrekananid);
			$delivery->setField("LINGKUP", $lingkup[$i]);
			$delivery->setField("DELIVERY_NAMA", $deliveryname[$i]);
			$delivery->setField("STATUS", $status[$i]);
			$delivery->setField("TANGGAL_DELIVERY_DARI", dateToDBCheck($tanggalDeliveryDari[$i]));
			$delivery->setField("TANGGAL_DELIVERY_SAMPAI", dateToDBCheck($tanggalDeliverySampai[$i]));
			$delivery->setField('CREATED_BY', $this->USER_LOGIN_ID);
			$insert = $delivery->inserDelivery();
          	unset($delivery);
        }

		if ($insert) {
			$this->load->library("librekamjejak");
			$this->librekamjejak->insertRJ('254','','null','null','254',$contractingrekananid);
			echo "Data berhasil disimpan.";
		} else { echo "Data gagal disimpan, silahkan coba kembali."; }
	}

	function addDeliverable2() // Done
	{
		$this->load->model("Contractingdeliverable");
		// echo "<pre>"; print_r($this->input->post()); die();
		$contractingrekananid	= $this->input->post('contractingrekananid');
		$lingkup				= $_POST["lingkup"];
		$deliveryname			= $_POST["deliveryname"];
		$status					= $_POST["status"];

		for($i=0; $i<count($lingkup);$i++)
        {
			$delivery	= new Contractingdeliverable();
			$delivery->setField("CONTRACTINGREKANANID", $contractingrekananid);
			$delivery->setField("LINGKUP", $lingkup[$i]);
			$delivery->setField("DELIVERY_NAMA", $deliveryname[$i]);
			$delivery->setField("STATUS", $status[$i]);
			$delivery->setField("TANGGAL_DELIVERY_DARI", dateToDBCheck($tanggalDeliveryDari[$i]));
			$delivery->setField("TANGGAL_DELIVERY_SAMPAI", dateToDBCheck($tanggalDeliverySampai[$i]));
			$delivery->setField('CREATED_BY', $this->USER_LOGIN_ID);
			$insert = $delivery->inserDelivery();
          	unset($delivery);
        }

		if ($insert) { echo "Data berhasil disimpan.";
		} else { echo "Data gagal disimpan, silahkan coba kembali."; }
	}

	function addDeliverableEdit() // Done
	{
		$this->load->library("FileHandler");
		$this->load->model("Contractingdeliverable");
		$file = new FileHandler();
		$reqTanggal			= date('Y-m-d H:i:s');

		// echo "<pre>"; print_r($this->input->post()); die();
		$deliverableid	= $this->input->post('deliverableid');
		$status	= $this->input->post('status');
		$reqTanggal	= $this->input->post('reqTanggal');
		$reqPersentase	= $this->input->post('reqPersentase');
		$reqContractingRekananId	= $this->input->post('reqContractingRekananId');
		$reqTanggalTerima	= $this->input->post('reqTanggalTerima');
		$reqKeterangan	= $this->input->post('reqKeterangan');

		$reqLinkFile = $_FILES['reqLinkFile'];
    	$reqLinkFileTemp    = $_POST["reqLinkFileTemp"];
		$FILE_DIR = "uploads/kontrak/";

		$delivery	= new Contractingdeliverable();
		
		$renameFile = md5($this->randomKode().date("dmYHis").$reqLinkFile['name'].$this->ID).".".getExtension($reqLinkFile['name']);
		if($file->uploadToDir('reqLinkFile', $FILE_DIR, $renameFile))
		{
			$insertLinkFile =  $renameFile;
		}
		else
		{
			$insertLinkFile =  $reqLinkFileTemp;
		}
		$delivery->setField("FILE_NAMA", $insertLinkFile);

		$delivery->setField("DELIVERABLEID", $deliverableid);
		$delivery->setField("TANGGAL_TERIMA", dateToDBCheck($reqTanggalTerima));
		$delivery->setField("KETERANGAN", $reqKeterangan);
		$delivery->setField("STATUS", $status);
		$delivery->setField("TANGGAL", dateToDBCheck($reqTanggal));
		$delivery->setField("PRESENTASE", $reqPersentase);
		$delivery->setField('UPDATED_BY', $this->USER_LOGIN_ID);
		$insert = $delivery->updateDelivery();
      	unset($delivery);

		if ($insert) {
			$this->load->library("librekamjejak");
			$this->librekamjejak->insertRJ('263','','null','null','263',$reqContractingRekananId);
			echo "Data berhasil disimpan.";
		} else { echo "Data gagal disimpan, silahkan coba kembali."; }
	}

	function addDeliverableEdit2() // Done
	{
		$this->load->model("Contractingdeliverable");
		// echo "<pre>"; print_r($this->input->post()); die();
		$deliverableid	= $this->input->post('deliverableid');
		$lingkup				= $this->input->post("lingkup");
		$deliveryname			= $this->input->post("deliveryname");
		$status	= $this->input->post('status');

		$delivery	= new Contractingdeliverable();
		$delivery->setField("DELIVERABLEID", $deliverableid);
		$delivery->setField("LINGKUP", $lingkup);
		$delivery->setField("DELIVERY_NAMA", $deliveryname);
		$delivery->setField("STATUS", $status);
		$delivery->setField('UPDATED_BY', $this->USER_LOGIN_ID);
		$insert = $delivery->updateDelivery2();
      	unset($delivery);

		if ($insert) { echo "Data berhasil disimpan.";
		} else { echo "Data gagal disimpan, silahkan coba kembali."; }
	}

	function addDeliverableEditBAPP() // Done
	{
		$this->load->library("FileHandler");
		$this->load->model("Contractingdeliverable");
		$file = new FileHandler();
		$reqTanggal			= date('Y-m-d H:i:s');

		// echo "<pre>"; print_r($this->input->post()); die();
		$reqContractingRekananId	= $this->input->post('reqContractingRekananId');
		$deliverableid	= $this->input->post('deliverableid');

		$reqLinkFile = $_FILES['reqLinkFile'];
    	$reqLinkFileTemp    = $_POST["reqLinkFileTemp"];
		$FILE_DIR = "uploads/kontrak/";

		$delivery	= new Contractingdeliverable();
		
		$renameFile = 'BAPP_'.md5($this->randomKode().date("dmYHis").$reqLinkFile['name'].$this->ID).".".getExtension($reqLinkFile['name']);
		if($file->uploadToDir('reqLinkFile', $FILE_DIR, $renameFile))
		{
			$insertLinkFile =  $renameFile;
		}
		else
		{
			$insertLinkFile =  $reqLinkFileTemp;
		}
		$delivery->setField("FILE_BAPP", $insertLinkFile);
		$delivery->setField("DELIVERABLEID", $deliverableid);
		$delivery->setField('UPDATED_BY', $this->USER_LOGIN_ID);
		$insert = $delivery->updateDeliveryBAPP();
      	unset($delivery);

		if ($insert) {
			$this->load->library("librekamjejak");
			$this->librekamjejak->insertRJ('2631','','null','null','2631',$reqContractingRekananId);
			echo "Data berhasil disimpan.";
		} else { echo "Data gagal disimpan, silahkan coba kembali."; }
	}

	function editFileAddendumPenyedia() // Done
	{
		$this->load->library("FileHandler");
		$this->load->model("Contractingaddendum");
		$file = new FileHandler();
		$reqTanggal			= date('Y-m-d H:i:s');

		$reqContractingRekananId	= $this->input->post('reqContractingRekananId');
		$contractingaddendumid	= $this->input->post('contractingaddendumid');

		$reqLinkFile = $_FILES['reqLinkFile'];
    	$reqLinkFileTemp    = $_POST["reqLinkFileTemp"];
		$FILE_DIR = "uploads/kontrak/";

		$addendum	= new Contractingaddendum();
		
		$renameFile = 'ADDENDUM_PENYEDIA_'.md5($this->randomKode().date("dmYHis").$reqLinkFile['name'].$this->ID).".".getExtension($reqLinkFile['name']);
		if($file->uploadToDir('reqLinkFile', $FILE_DIR, $renameFile))
		{
			$insertLinkFile =  $renameFile;
		}
		else
		{
			$insertLinkFile =  $reqLinkFileTemp;
		}
		$addendum->setField("ADDENDUM_FILE_PENYEDIA", $insertLinkFile);
		$addendum->setField("CONTRACTING_ADDENDUM_ID", $contractingaddendumid);
		$addendum->setField('UPDATED_BY', $this->USER_LOGIN_ID);
		$insert = $addendum->updateFileAddendumPenyedia();
      	unset($addendum);

		if ($insert) {
			$this->load->library("librekamjejak");
			$this->librekamjejak->insertRJ('2721','','null','null','2721',$reqContractingRekananId);
			echo "Data berhasil disimpan.";
		} else { echo "Data gagal disimpan, silahkan coba kembali."; }
	}

	function delDeliveryPayment()  // Done
	{
		$this->load->model('Contractingpayment');
		$payment	= new Contractingpayment();
		$reqAidi		= $this->input->get('reqAidi');

		$payment->setField("DELIVERABLEID", $reqAidi);
		$insert = $payment->deleteDeliveryPayment();

		if($insert) {
			$arrJson["PESAN"] = "Data berhasil di hapus";
		}
		else {
			$arrJson["PESAN"] = "Data gagal di hapus, silahkan dicoba kembali!";
		}

		echo json_encode($arrJson);
	}

	function delAddendum()  // Done
	{
		$this->load->model('Contractingaddendum');

		$addendum	= new Contractingaddendum();

		$reqAidi		= $this->input->get('reqAidi');

		$addendum->setField("CONTRACTING_ADDENDUM_ID", $reqAidi);
		$insert = $addendum->delete();

		if($insert) {
			$arrJson["PESAN"] = "Data berhasil di hapus";
		}
		else {
			$arrJson["PESAN"] = "Data gagal di hapus, silahkan dicoba kembali!";
		}

		echo json_encode($arrJson);
	}

	function delJaminan()  // Done
	{
		$this->load->model('Contractingjaminan');

		$jaminan	= new Contractingjaminan();

		$reqAidi		= $this->input->get('reqAidi');

		$jaminan->setField("CONTRACTING_JAMINAN_ID", $reqAidi);
		$insert = $jaminan->delete();

		if($insert) {
			$arrJson["PESAN"] = "Data berhasil di hapus";
		}
		else {
			$arrJson["PESAN"] = "Data gagal di hapus, silahkan dicoba kembali!";
		}

		echo json_encode($arrJson);
	}

	function delDelivery()  // Done
	{
		$this->load->model('Contractingdeliverable');

		$delivery	= new Contractingdeliverable();

		$reqAidi		= $this->input->get('reqAidi');

		$delivery->setField("DELIVERABLEID", $reqAidi);
		$insert = $delivery->delete();

		if($insert) {

			$arrJson["PESAN"] = "Data berhasil di hapus";
		}
		else {
			$arrJson["PESAN"] = "Data gagal di hapus, silahkan dicoba kembali!";
		}

		echo json_encode($arrJson);
	}

	function addMaterial() // Done
	{
		$this->load->model("Contractingmaterial");

		// echo "<pre>"; print_r($this->input->post()); die();
		$contractingrekananid	= $this->input->post('contractingrekananid');
		$material				= $_POST["material"];
		$qty					= $_POST["qty"];
		$satuanid				= $_POST["satuanid"];
		$reqSifat				= $_POST["reqSifat"];
		// $keterangan				= str_replace("'","''",$_POST["keterangan"]);
		$hargasatuan			= CommaToDot(dotToNo($_POST["hargasatuan"]));

		// hapus data semua dulu kemudian insert
		$dataMaterial2	= new Contractingmaterial();
		$dataMaterial2->setField("CONTRACTINGREKANANID", $contractingrekananid);
		$dataMaterial2->delAll();
      	unset($dataMaterial2);

		for($i=0; $i<count($material);$i++)
        {
			$dataMaterial	= new Contractingmaterial();
			$dataMaterial->setField("CONTRACTINGREKANANID", $contractingrekananid);
			$dataMaterial->setField("NAMA", $material[$i]);
			$dataMaterial->setField("QTY", $qty[$i]);
			$dataMaterial->setField("SATUANID", $satuanid[$i]);
			// $dataMaterial->setField("KETERANGAN", str_replace("'","''",$keterangan[$i]));
			$dataMaterial->setField("HARGA_SATUAN", $hargasatuan[$i]);
			$dataMaterial->setField("SIFAT", $reqSifat);
			$dataMaterial->setField('CREATED_BY', $this->USER_LOGIN_ID);
			$insert = $dataMaterial->inserMaterial();
          	unset($dataMaterial);
        }

		if ($insert) {
			$this->load->library("librekamjejak");
			$this->librekamjejak->insertRJ('262','','null','null','262',$contractingrekananid);
			echo "0--Data berhasil disimpan.";
		} else { echo "1--Data gagal disimpan, silahkan coba kembali."; }
	}

	function addMaterialPerubahan() // Done
	{
		$this->load->model("Contractingmaterial");
		// echo "<pre>"; print_r($this->input->post()); die();
		$contractingrekananid	= $this->input->post('contractingrekananid');
		$material				= $_POST["material"];
		$keterangan				= str_replace("'","''",$_POST["keterangan"]);
		$hargasatuan			= CommaToDot(dotToNo($_POST["hargasatuan"]));

		for($i=0; $i<count($material);$i++)
        {
			$dataMaterial	= new Contractingmaterial();
			$dataMaterial->setField("CONTRACTINGREKANANID", $contractingrekananid);
			$dataMaterial->setField("NAMA", $material[$i]);
			$dataMaterial->setField("KETERANGAN", str_replace("'","''",$keterangan[$i]));
			$dataMaterial->setField("HARGA_SATUAN", $hargasatuan[$i]);
			$dataMaterial->setField('CREATED_BY', $this->USER_LOGIN_ID);
			$insert = $dataMaterial->inserMaterial();
          	unset($dataMaterial);
        }

		if ($insert) { echo "0--Data berhasil disimpan.";
		} else { echo "1--Data gagal disimpan, silahkan coba kembali."; }
	}

	function editMaterial() // Done
	{
		$this->load->model("Contractingmaterial");
		// echo "<pre>"; print_r($this->input->post()); die();
		$reqMaterialid	= $this->input->post('reqMaterialid');
		$material				= $_POST["material"];
		$keterangan				= str_replace("'","''",$_POST["keterangan"]);
		$hargasatuan			= CommaToDot(dotToNo($_POST["hargasatuan"]));

		for($i=0; $i<count($material);$i++)
        {
			$dataMaterial	= new Contractingmaterial();
			$dataMaterial->setField("MATERIALID", $reqMaterialid);
			$dataMaterial->setField("NAMA", $material[$i]);
			$dataMaterial->setField("KETERANGAN", str_replace("'","''",$keterangan[$i]));
			$dataMaterial->setField("HARGA_SATUAN", $hargasatuan[$i]);
			$dataMaterial->setField('UPDATED_BY', $this->USER_LOGIN_ID);
			$insert = $dataMaterial->updateMaterial();
          	unset($dataMaterial);
        }

		if ($insert) { echo "0--Data berhasil disimpan.";
		} else { echo "1--Data gagal disimpan, silahkan coba kembali."; }
	}

	function deleteMaterial()  // Done
	{
		$this->load->model('Contractingmaterial');
		$material	= new Contractingmaterial();
		$reqAidi		= $this->input->get('reqId');

		$material->setField("MATERIALID", $reqAidi);
		$del = $material->delete();

		if($del) {
			$arrJson["PESAN"] = "Data berhasil di hapus";
		}
		else {
			$arrJson["PESAN"] = "Data gagal di hapus, silahkan dicoba kembali!";
		}

		echo json_encode($arrJson);
	}

	function addPayment() // Done
	{ 
		$this->load->model("Contractingpayment");
		// echo "<pre>"; print_r($this->input->post()); die();
		$contractingrekananid	= $this->input->post('contractingrekananid');
		$payterminke			= $_POST["payteminke"];
		$paynilai				= CommaToDot(dotToNo($_POST["paynilai"]));
		$payprogres				= $_POST["payprogres"];
		$payketerangan			= $_POST["payketerangan"];
		$reqPayDateDari			= $this->input->post('reqPayDateDari');
		$reqPayDateSampai		= $this->input->post('reqPayDateSampai');
		$reqDeliverableId		= $this->input->post('reqDeliverableId');


		// hapus data semua dulu kemudian insert
		$payment2	= new Contractingpayment();
		$payment2->setField("CONTRACTINGREKANANID", $contractingrekananid);
		$payment2->delAll();
      	unset($payment2);

		for($i=0; $i<count($payterminke);$i++)
        {
			$payment	= new Contractingpayment();
			$payment->setField("CONTRACTINGREKANANID", $contractingrekananid);
			$payment->setField("PAY_TERMIN_KE", $payterminke[$i]);
			$payment->setField("PAY_NILAI", $paynilai[$i]);
			$payment->setField("PAY_PROGRES", $payprogres[$i]);
			$payment->setField("PAY_KETERANGAN", $payketerangan[$i]);
			$payment->setField("PAY_DATE_DARI", dateToDBCheck($reqPayDateDari[$i]));
			$payment->setField("PAY_DATE_SAMPAI", dateToDBCheck($reqPayDateSampai[$i]));
			$payment->setField("DELIVERABLEID_FK", $reqDeliverableId[$i]);
			$payment->setField('CREATED_BY', $this->USER_LOGIN_ID);
			$insert = $payment->insertPayment();
          	unset($payment);
        }

		if ($insert) {
			$this->load->library("librekamjejak");
			$this->librekamjejak->insertRJ('255','','null','null','255',$contractingrekananid);
			echo "Data berhasil disimpan.";
		} else { echo "Data gagal disimpan, silahkan coba kembali."; }
	}

	function addPayment2() // Done
	{
		$this->load->model("Contractingpayment");
		// echo "<pre>"; print_r($this->input->post()); die();
		$contractingrekananid	= $this->input->post('contractingrekananid');
		$payterminke			= $_POST["payteminke"];
		$paynilai				= CommaToDot(dotToNo($_POST["paynilai"]));
		$payprogres				= $_POST["payprogres"];
		$payketerangan			= $_POST["payketerangan"];

		for($i=0; $i<count($payterminke);$i++)
        {
			$payment	= new Contractingpayment();
			$payment->setField("CONTRACTINGREKANANID", $contractingrekananid);
			$payment->setField("PAY_TERMIN_KE", $payterminke[$i]);
			$payment->setField("PAY_NILAI", $paynilai[$i]);
			$payment->setField("PAY_PROGRES", $payprogres[$i]);
			$payment->setField("PAY_KETERANGAN", $payketerangan[$i]);
			$payment->setField('CREATED_BY', $this->USER_LOGIN_ID);
			$insert = $payment->insertPayment();
          	unset($payment);
        }

		if ($insert) {
			$this->load->library("librekamjejak");
			$this->librekamjejak->insertRJ('255','','null','null','255',$contractingrekananid);
			echo "Data berhasil disimpan.";
		} else { echo "Data gagal disimpan, silahkan coba kembali."; }
	}

	function addPaymentMerger() // Done
	{  
		$this->load->model("Contractingpayment");
		// echo "<pre>"; print_r($this->input->post()); die();
		$reqSubmit 				= $this->input->post('reqSubmit');
		$deliverableid 			= $this->input->post('deliverableid');
		$contractingrekananid 	= $this->input->post('contractingrekananid');
		$reqdeliveryname		= $this->input->post('reqdeliveryname');
		$reqlingkup				= $this->input->post('reqlingkup');
		$reqTanggalDeliveryDari	= $this->input->post('reqTanggalDeliveryDari');
		$reqTanggalDeliverySampai	= $this->input->post('reqTanggalDeliverySampai');
		$payterminke			= $this->input->post('payterminke');
		$paynilai				= CommaToDot(dotToNo($this->input->post('paynilai')));
		$payprogres				= $this->input->post('payprogres');
		$reqPayDateDari			= $this->input->post('reqPayDateDari');
		$reqPayDateSampai		= $this->input->post('reqPayDateSampai');
 
		$paymentMerger	= new Contractingpayment();
		$paymentMerger->setField("DELIVERABLEID", $deliverableid);
		$paymentMerger->setField("CONTRACTINGREKANANID", $contractingrekananid);
		$paymentMerger->setField("DELIVERY_NAMA", $reqdeliveryname);
		$paymentMerger->setField("LINGKUP", $reqlingkup);
		$paymentMerger->setField("TANGGAL_DELIVERY_DARI", dateToDBCheck($reqTanggalDeliveryDari));
		$paymentMerger->setField("TANGGAL_DELIVERY_SAMPAI", dateToDBCheck($reqTanggalDeliverySampai));

		$paymentMerger->setField("PAY_TERMIN_KE", $payterminke);
		$paymentMerger->setField("PAY_NILAI", $paynilai);
		$paymentMerger->setField("PAY_PROGRES", $payprogres);
		$paymentMerger->setField("PAY_DATE_DARI", dateToDBCheck($reqPayDateDari));
		$paymentMerger->setField("PAY_DATE_SAMPAI", dateToDBCheck($reqPayDateSampai));
		$paymentMerger->setField('CREATED_BY', $this->USER_LOGIN_ID);

		if ($reqSubmit == 'Simpan') {
			$insert = $paymentMerger->insertPaymentMerger();
		} else {
			$insert = $paymentMerger->updatePaymentMerger();
		}
      	unset($paymentMerger);

		if ($insert) {
			$this->load->library("librekamjejak");
			$this->librekamjejak->insertRJ('255','','null','null','255',$contractingrekananid);
			echo "Data berhasil disimpan.";
		} else { echo "Data gagal disimpan, silahkan coba kembali."; }
	}

	public function editPayment() // Done
	{
		$this->load->model('Contractingpayment');
		$this->load->library("FileHandler");
		$payment	= new Contractingpayment();
		$file = new FileHandler();

		$paymentid			= $this->input->post('paymentid');
		$status				= $this->input->post('status');
		$reqPayProgres		= $this->input->post('reqPayProgres');
		$reqDeliverableidfk		= $this->input->post('reqDeliverableidfk');
		$reqTanggal			= date('Y-m-d H:i:s');

		$reqLampiran		= $_FILES['reqLampiran'];
		$reqLampiranTemp 	= $this->input->post("reqLampiranTemp");
		$reqContractingRekananId 	= $this->input->post("reqContractingRekananId");
		$paynilai				= CommaToDot(dotToNo($_POST["paynilai"]));
		$paypotongan			= CommaToDot(dotToNo($_POST["paypotongan"]));

		$reqPayNomor		= $this->input->post('reqPayNomor');
		$reqPayDate			= $this->input->post('reqPayDate');
		$reqLampiran2		= $_FILES['reqLampiran2'];
		$reqLampiranTemp2 	= $this->input->post("reqLampiranTemp2");
		$reqPayDateTerimaHardcopy 	= $this->input->post('reqPayDateTerimaHardcopy');
		$reqPayDatePenyerahan		= $this->input->post('reqPayDatePenyerahan');

		$FILE_DIR = "uploads/payment/";

		$payment->setField("PAYMENTID", $paymentid);
		$payment->setField("STATUS", $status);
		$payment->setField("PAY_PROGRES", $reqPayProgres);
		$payment->setField("DELIVERABLEID_FK", $reqDeliverableidfk);
		$payment->setField("PAY_NILAI", $paynilai);
		$payment->setField("PAY_POTONGAN", $paypotongan);
		$payment->setField("PAY_NOMOR", $reqPayNomor);
		$payment->setField("PAY_DATE", dateToDBCheck($reqPayDate));
		$payment->setField("PAY_DATE_TERIMA_HARDCOPY", dateToDBCheck($reqPayDateTerimaHardcopy));
		$payment->setField("PAY_DATE_PENYERAHAN", dateToDBCheck($reqPayDatePenyerahan));

		$renameFile = md5(date("dmYHis").$reqLampiran['name'].$this->ID).".".getExtension($reqLampiran['name']);
		if($file->uploadToDir('reqLampiran', $FILE_DIR, $renameFile))
		{
			$insertLinkFile =  $renameFile;
		}
		else
		{
			$insertLinkFile =  $reqLampiranTemp;
		}
		$payment->setField("PAY_LAMPIRAN", $insertLinkFile);

		$renameFile2 = md5(date("dmYHis").$reqLampiran2['name'].$this->ID).".".getExtension($reqLampiran2['name']);
		if($file->uploadToDir('reqLampiran2', $FILE_DIR, $renameFile2))
		{
			$insertLinkFile2 =  $renameFile2;
		}
		else
		{
			$insertLinkFile2 =  $reqLampiranTemp;
		}
		$payment->setField("PAY_LAMPIRAN_BAP", $insertLinkFile2);

		$payment->setField('UPDATED_BY', $this->USER_LOGIN_ID);
		$insert = $payment->updatePayment();

		if ($insert) {
			$this->load->library("librekamjejak");
			$this->librekamjejak->insertRJ('264','','null','null','264',$reqContractingRekananId);
			echo "Data berhasil disimpan.";
		} else { echo "Data gagal disimpan, silahkan coba kembali."; }
	}

	public function addJampel() // Done
	{
		$this->load->model('Contractingjaminanpemeliharaan');
		$this->load->library("FileHandler");
		$jampel	= new Contractingjaminanpemeliharaan();
		$file = new FileHandler(); 

		$reqSubmit			= $this->input->post('reqSubmit');
		$reqContractingJempelId			= $this->input->post('reqContractingJempelId');
		$reqNomor			= $this->input->post('reqNomor');
		$nilai				= CommaToDot(dotToNo($_POST["nilai"]));
		$reqMasa			= $this->input->post('reqMasa');
		$reqTanggalMulai	= $this->input->post('reqTanggalMulai');
		$reqTanggalAkhir	= $this->input->post('reqTanggalAkhir');
		$reqContractingRekananId		= $this->input->post('reqContractingRekananId');
		$reqPaketId			= $this->input->post('reqPaketId');
		$reqTanggal			= date('Y-m-d H:i:s');

		$reqLampiran		= $_FILES['reqLampiran'];
		$reqLampiranTemp 	= $this->input->post("reqLampiranTemp");

		$FILE_DIR = "uploads/payment/";
 
		$jampel->setField("CONTRACTING_JAMPEL_ID", $reqContractingJempelId);
		$jampel->setField("NOMOR", $reqNomor);
		$jampel->setField("NILAI", $nilai);
		$jampel->setField("MASA", $reqMasa);
		$jampel->setField("TANGGAL_MULAI", dateToDBCheck($reqTanggalMulai));
		$jampel->setField("TANGGAL_AKHIR", dateToDBCheck($reqTanggalAkhir));
		$jampel->setField("CONTRACTINGREKANANID", $reqContractingRekananId);
		$jampel->setField("PAKET_ID", $reqPaketId);

		$renameFile = 'Jampel_'.md5(date("dmYHis").$reqLampiran['name'].$this->ID).".".getExtension($reqLampiran['name']);
		if($file->uploadToDir('reqLampiran', $FILE_DIR, $renameFile))
		{
			$insertLinkFile =  $renameFile;
		}
		else
		{
			$insertLinkFile =  $reqLampiranTemp;
		}
		$jampel->setField("FILE_JAMINAN", $insertLinkFile); 

		$jampel->setField('CREATED_BY', $this->USER_LOGIN_ID);

		if ($reqSubmit == 'simpan') {
			$insert = $jampel->insertJaminan();
		} else {
			$insert = $jampel->updateJaminan();
		}

		if ($insert) {
			$this->load->library("librekamjejak");
			$this->librekamjejak->insertRJ('361','','null','null','361',$reqContractingRekananId);
			echo "Data berhasil disimpan.";
		} else { echo "Data gagal disimpan, silahkan coba kembali."; }
	}

	public function updateprogres()
	{
		$this->load->model('Contractingpayment');
		$payment	= new Contractingpayment();

		$paymentid	= $this->input->get('paymentid');
		$presen		= $this->input->get('presen');

		$payment->setField('PAYMENTID', $paymentid);
		$payment->setField('PAY_PROGRES', $presen);

		if($payment->updatePaymentProgres())
			$pesan = "Data berhasil disimpan.";
		else
			$pesan = "Data gagal disimpan.";

		$arrFinal = array("PESAN" => $pesan);

		echo json_encode($arrFinal);

	}

	public function editPayment2() // Done
	{
		$this->load->model('Contractingpayment');
		$this->load->library("FileHandler");
		$payment	= new Contractingpayment();
		$file = new FileHandler();

		$paymentid			= $this->input->post('paymentid');
		$reqTanggal			= date('Y-m-d H:i:s');

		$payterminke			= $_POST["payteminke"];
		$paynilai				= CommaToDot(dotToNo($_POST["paynilai"]));
		$payprogres				= $_POST["payprogres"];
		$payketerangan			= $_POST["payketerangan"];

		$payment->setField("PAY_TERMIN_KE", $payterminke);
		$payment->setField("PAY_NILAI", $paynilai);
		$payment->setField("PAY_PROGRES", $payprogres);
		$payment->setField("PAY_KETERANGAN", $payketerangan);
		$payment->setField("PAYMENTID", $paymentid);
		$payment->setField('UPDATED_BY', $this->USER_LOGIN_ID);
		$insert = $payment->updatePayment2();

		if ($insert) {
			$this->load->library("librekamjejak");
			$this->librekamjejak->insertRJ('264','','null','null','264',$reqContractingRekananId);
			echo "Data berhasil disimpan.";
		} else { echo "Data gagal disimpan, silahkan coba kembali."; }
	}

	function delPayment()  // Done
	{
		$this->load->model('Contractingpayment');

		$payment	= new Contractingpayment();

		$reqAidi		= $this->input->get('reqAidi');

		$payment->setField("PAYMENTID", $reqAidi);
		$insert = $payment->delete();

		if($insert) {
			$arrJson["PESAN"] = "Data berhasil di hapus";
		}
		else {
			$arrJson["PESAN"] = "Data gagal di hapus, silahkan dicoba kembali!";
		}

		echo json_encode($arrJson);
	}

	function addSLA() // Done
	{
		$this->load->model("Contractingsla");
		// echo "<pre>"; print_r($this->input->post()); die();
		$contractingrekananid	= $this->input->post('contractingrekananid');
		$slaavaibility			= $_POST["slaavaibility"];
		$slawaktu				= $_POST["slawaktu"];
		$sladenda				= CommaToDot(dotToNo($_POST["sladenda"]));
		$slabiayamaintanance	= CommaToDot(dotToNo($_POST["slabiayamaintanance"]));
		$slanilaidenda			= CommaToDot(dotToNo($_POST["slanilaidenda"]));

		// hapus data semua dulu kemudian insert
		$sla2	= new Contractingsla();
		$sla2->setField("CONTRACTINGREKANANID", $contractingrekananid);
		$sla2->delAll();
      	unset($sla2);

		for($i=0; $i<count($slaavaibility);$i++)
        {
			$sla	= new Contractingsla();
			$sla->setField("CONTRACTINGREKANANID", $contractingrekananid);
			$sla->setField("SLA_AVAILABILITY", $slaavaibility[$i]);
			$sla->setField("SLA_WAKTU", $slawaktu[$i]);
			$sla->setField("SLA_DENDA", $sladenda[$i]);
			$sla->setField("SLA_BIAYA_MAINTANANCE", $slabiayamaintanance[$i]);
			$sla->setField("SLA_NILAI_DENDA", $slanilaidenda[$i]);
			$sla->setField('CREATED_BY', $this->USER_LOGIN_ID);
			$insert = $sla->insertSla();
          	unset($sla);
        }

		if ($insert) { echo "Data berhasil disimpan.";
		} else { echo "Data gagal disimpan, silahkan coba kembali.."; }
	}

	function addSanksi() // Done
	{
		$this->load->model("Contractingsanksi");
		// echo "<pre>"; print_r($this->input->post()); die();
		$contractingrekananid	= $this->input->post('contractingrekananid');
		$hariterlambat			= $_POST["hariterlambat"];
		$nilaisanksi			= $_POST["nilaisanksi"];
		$nilaipekerjaan			= CommaToDot(dotToNo($_POST["nilaipekerjaan"]));
		$nilaidenda				= CommaToDot(dotToNo($_POST["nilaidenda"]));

		// hapus data semua dulu kemudian insert
		$sanksi2	= new Contractingsanksi();
		$sanksi2->setField("CONTRACTINGREKANANID", $contractingrekananid);
		$sanksi2->delAll();
      	unset($sanksi2);

		for($i=0; $i<count($hariterlambat);$i++)
        {
			$sanksi	= new Contractingsanksi();
			$sanksi->setField("CONTRACTINGREKANANID", $contractingrekananid);
			$sanksi->setField("HARI_TERLAMBAT", $hariterlambat[$i]);
			$sanksi->setField("NILAI_SANKSI", $nilaisanksi[$i]);
			$sanksi->setField("NILAI_PEKERJAAN", $nilaipekerjaan[$i]);
			$sanksi->setField("NILAI_DENDA", $nilaidenda[$i]);
			$sanksi->setField('CREATED_BY', $this->USER_LOGIN_ID);
			$insert = $sanksi->insertSanksi();
          	unset($sanksi);
        }

		if ($insert) { echo "Data berhasil disimpan.";
		} else { echo "Data gagal disimpan, silahkan coba kembali.."; }
	}

	function addSanksi2() // Done
	{
		$this->load->library("FileHandler");
		$file = new FileHandler();

		$this->load->model("Contractingsanksi");

		// echo "<pre>"; print_r($this->input->post()); die();
		$contractingrekananid	= $this->input->post('contractingrekananid');
		$rowkey					= $_POST["rowkey"];
		$hariterlambat			= $_POST["hariterlambat"];
		$nilaisanksi			= $_POST["nilaisanksi"];
		$paymentid				= $_POST["paymentid"];
		$nilaipekerjaan			= CommaToDot(dotToNo($_POST["nilaipekerjaan"]));
		$nilaidenda				= CommaToDot(dotToNo($_POST["nilaidenda"]));
		$carabayar   			= $this->input->post('carabayar'); // array key => Dipotong/Disetor
		$reqLampiran   			= $_FILES['reqLampiran'];
		$reqLampiran2   			= $_FILES['reqLampiran2'];
		
		$FILE_DIR = "uploads/payment/";

		foreach ($rowkey as $key => $rowid) {

		    $sanksi = new Contractingsanksi();

		    $caraBayarVal = isset($carabayar[$rowid]) ? $carabayar[$rowid] : '';
		    $insertLinkFile = '';

		    if (isset($reqLampiran['name'][$rowid]) &&
		        $reqLampiran['name'][$rowid] != ''
		    ) {

		        $_FILES['file_temp']['name']     = $reqLampiran['name'][$rowid];
		        $_FILES['file_temp']['type']     = $reqLampiran['type'][$rowid];
		        $_FILES['file_temp']['tmp_name'] = $reqLampiran['tmp_name'][$rowid];
		        $_FILES['file_temp']['error']    = $reqLampiran['error'][$rowid];
		        $_FILES['file_temp']['size']     = $reqLampiran['size'][$rowid];

		        $renameFile = 'Invoice_' .
		            md5($this->randomKode().date("dmYHis").$reqLampiran['name'][$rowid].$this->ID) .
		            '.' . getExtension($reqLampiran['name'][$rowid]);

		        if ($file->uploadToDir('file_temp', $FILE_DIR, $renameFile)) {
		            $insertLinkFile = $renameFile;
		        }
		    }

		    if (isset($reqLampiran2['name'][$rowid]) &&
		        $reqLampiran2['name'][$rowid] != ''
		    ) {

		        $_FILES['file_temp']['name']     = $reqLampiran2['name'][$rowid];
		        $_FILES['file_temp']['type']     = $reqLampiran2['type'][$rowid];
		        $_FILES['file_temp']['tmp_name'] = $reqLampiran2['tmp_name'][$rowid];
		        $_FILES['file_temp']['error']    = $reqLampiran2['error'][$rowid];
		        $_FILES['file_temp']['size']     = $reqLampiran2['size'][$rowid];

		        $renameFile2 = 'Invoice2_' .
		            md5($this->randomKode().date("dmYHis").$reqLampiran2['name'][$rowid].$this->ID) .
		            '.' . getExtension($reqLampiran2['name'][$rowid]);

		        if ($file->uploadToDir('file_temp', $FILE_DIR, $renameFile2)) {
		            $insertLinkFile2 = $renameFile2;
		        }
		    }

		    $sanksi->setField("BUKTI_BAYAR", '');
		    $sanksi->setField("INVOICE_FILE", $insertLinkFile);
		    $sanksi->setField("INVOICE_FILE_TTD", $insertLinkFile2);
		    $sanksi->setField("CONTRACTINGREKANANID", $contractingrekananid);
		    $sanksi->setField("HARI_TERLAMBAT", $hariterlambat[$rowid]);
		    $sanksi->setField("NILAI_SANKSI", $nilaisanksi[$rowid]);
		    $sanksi->setField("NILAI_PEKERJAAN", $nilaipekerjaan[$rowid]);
		    $sanksi->setField("NILAI_DENDA", $nilaidenda[$rowid]);
		    $sanksi->setField("CARA_BAYAR", $caraBayarVal);
		    $sanksi->setField("PAYMENTID_FK", $paymentid[$rowid]);
		    $sanksi->setField("CREATED_BY", $this->USER_LOGIN_ID);

		    $insert = $sanksi->insertSanksi();
		    unset($sanksi);
		}



		// for($i=0; $i<count($hariterlambat);$i++)
  		//       {
		// foreach ($hariterlambat as $key => $hari) {

		// 	$sanksi	= new Contractingsanksi();
		// 	// =============================
		//     // UPLOAD FILE (jika Disetor)
		//     // =============================
		//     if (
		//         $carabayar[$key] == 'Disetor' &&
		//         !empty($reqLampiran['name'][$key])
		//     ) {
		// 		$renameFile = 'Sanksi_'.md5($this->randomKode().date("dmYHis").$reqLampiran['name'][$key].$this->ID).".".getExtension($reqLampiran['name'][$key]);
		// 		if($file->uploadToDir('reqLampiran', $FILE_DIR, $renameFile))
		// 		{
		// 			$insertLinkFile =  $renameFile;
		// 		}
		// 		else
		// 		{
		// 			$insertLinkFile =  $reqLampiranTemp;
		// 		}
		// 		$sanksi->setField("BUKTI_BAYAR", $insertLinkFile);
		// 	} else {
		// 		$sanksi->setField("BUKTI_BAYAR", '');
		// 	}

		// 	$sanksi->setField("CONTRACTINGREKANANID", $contractingrekananid);
		// 	$sanksi->setField("HARI_TERLAMBAT", $hariterlambat[$key]);
		// 	$sanksi->setField("NILAI_SANKSI", $nilaisanksi[$key]);
		// 	$sanksi->setField("NILAI_PEKERJAAN", $nilaipekerjaan[$key]);
		// 	$sanksi->setField("NILAI_DENDA", $nilaidenda[$key]);
		// 	$sanksi->setField("CARA_BAYAR", $carabayar[$key]);
		// 	$sanksi->setField("PAYMENTID_FK", $paymentid[$key]); 
		// 	$sanksi->setField('CREATED_BY', $this->USER_LOGIN_ID);
		// 	$insert = $sanksi->insertSanksi();
  		//         	unset($sanksi);
  		//       }

		if ($insert) { echo "Data berhasil disimpan.";
		} else { echo "Data gagal disimpan, silahkan coba kembali.."; }
	}

	function editanksi2() // Done
	{
		$this->load->library("FileHandler");
		$file = new FileHandler();

		$this->load->model("Contractingsanksi");

		// echo "<pre>"; print_r($this->input->post()); die();
		$sanksiid	= $this->input->post('sanksiid');
		$hariterlambat			= $this->input->post('hariterlambat');
		$nilaisanksi			= $this->input->post('nilaisanksi');
		$paymentid				= $_POST["paymentid"];
		$nilaipekerjaan			= CommaToDot(dotToNo($this->input->post('nilaipekerjaan')));
		$nilaidenda				= CommaToDot(dotToNo($this->input->post('nilaidenda')));
		$carabayar   			= $this->input->post('carabayar'); // array key => Dipotong/Disetor
		$reqLampiran   			= $_FILES['reqLampiran'];
		$reqLampiranTemp 		= $this->input->post("reqLampiranTemp");
		$reqLampiran2   			= $_FILES['reqLampiran2'];
		$reqLampiran2Temp 		= $this->input->post("reqLampiran2Temp");

		$FILE_DIR = "uploads/payment/";

		$renameFile = 'Invoice_'.md5(date("dmYHis").$reqLampiran['name'].$this->ID).".".getExtension($reqLampiran['name']);
		if($file->uploadToDir('reqLampiran', $FILE_DIR, $renameFile))
		{
			$insertLinkFile =  $renameFile;
		}
		else
		{
			$insertLinkFile =  $reqLampiranTemp;
		}

		$renameFile2 = 'Invoice_'.md5(date("dmYHis").$reqLampiran2['name'].$this->ID).".".getExtension($reqLampiran2['name']);
		if($file->uploadToDir('reqLampiran2', $FILE_DIR, $renameFile2))
		{
			$insertLinkFile2 =  $renameFile2;
		}
		else
		{
			$insertLinkFile2 =  $reqLampiran2Temp;
		}

		$sanksi	= new Contractingsanksi();
		$sanksi->setField("SANKSIID", $sanksiid);
		$sanksi->setField("HARI_TERLAMBAT", $hariterlambat);
		$sanksi->setField("NILAI_SANKSI", $nilaisanksi);
		$sanksi->setField("NILAI_PEKERJAAN", $nilaipekerjaan);
		$sanksi->setField("NILAI_DENDA", $nilaidenda);
	    $sanksi->setField("CARA_BAYAR", $carabayar);
	    $sanksi->setField("PAYMENTID_FK", $paymentid);
		$sanksi->setField("BUKTI_BAYAR", '');
		$sanksi->setField("INVOICE_FILE", $insertLinkFile);
		$sanksi->setField("INVOICE_FILE_TTD", $insertLinkFile2);
		$sanksi->setField('CREATED_BY', $this->USER_LOGIN_ID);
		$insert = $sanksi->editSanksi2();
      	unset($sanksi);

		if ($insert) { echo "Data berhasil disimpan.";
		} else { echo "Data gagal disimpan, silahkan coba kembali.."; }
	}

	function editanksi22() // Done
	{
		$this->load->library("FileHandler");
		$file = new FileHandler();

		$this->load->model("Contractingsanksi");

		// echo "<pre>"; print_r($this->input->post()); die();
		$sanksiid	= $this->input->post('sanksiid');
		$paymentid				= $_POST["paymentid"];
		$reqLampiran   			= $_FILES['reqLampiran'];
		$reqLampiranTemp 		= $this->input->post("reqLampiranTemp");

		$FILE_DIR = "uploads/payment/";

		$renameFile = 'Sanksi_Denda_'.md5(date("dmYHis").$reqLampiran['name'].$this->ID).".".getExtension($reqLampiran['name']);
		if($file->uploadToDir('reqLampiran', $FILE_DIR, $renameFile))
		{
			$insertLinkFile =  $renameFile;
		}
		else
		{
			$insertLinkFile =  $reqLampiranTemp;
		}

		$sanksi	= new Contractingsanksi();
		$sanksi->setField("SANKSIID", $sanksiid);
		$sanksi->setField("BUKTI_BAYAR", $insertLinkFile);
		$sanksi->setField('CREATED_BY', $this->USER_LOGIN_ID);
		$insert = $sanksi->editSanksi22();
      	unset($sanksi);

		if ($insert) { echo "Data berhasil disimpan.";
		} else { echo "Data gagal disimpan, silahkan coba kembali.."; }
	}

	function delSanksi()  // Done
	{
		$this->load->model('Contractingsanksi');

		$sanksi	= new Contractingsanksi();

		$reqAidi		= $this->input->get('reqAidi');

		$sanksi->setField("SANKSIID", $reqAidi);
		$insert = $sanksi->delete();

		if($insert) {

			$arrJson["PESAN"] = "Data berhasil di hapus";
		}
		else {
			$arrJson["PESAN"] = "Data gagal di hapus, silahkan dicoba kembali!";
		}

		echo json_encode($arrJson);
	}

	function getPaymentNilai()  // Done
	{
		$this->load->model('Contractingpayment');
		$payment	= new Contractingpayment();

		$reqId		= $this->input->get('reqId'); // Contractingrekananid
		$paymentid		= $this->input->get('paymentid'); // paymentid

		$payment->selectByParams(array('PAYMENTID' => $paymentid, "A.CONTRACTINGREKANANID" => $reqId)); 
		$payment->firstRow();
		echo $payment->getField('PAY_NILAI');
	}

	function addSanksiKetentuan() // Done
	{
		$this->load->model("Contractingsanksi");
		// echo "<pre>"; print_r($this->input->post()); die();
		$contractingrekananid	= $this->input->post('contractingrekananid');
		$jenis					= $this->input->post('jenis');
		$reqSubmit					= $this->input->post('reqSubmit');
		$desc			= str_replace("'","''",$_POST["reqDesc"]);

		$sanksi	= new Contractingsanksi();
		$sanksi->setField("CONTRACTINGREKANANID", $contractingrekananid);
		$sanksi->setField("JENIS", $jenis);
		$sanksi->setField("DESC", $desc);
		$sanksi->setField('CREATED_BY', $this->USER_LOGIN_ID);
		if ($reqSubmit == 'Simpan') {
		$insert = $sanksi->insertSanksiKetentuan();
		} else {
		$insert = $sanksi->updateSanksiKetentuan();
		}
      	unset($sanksi);

		if ($insert) { echo "Data berhasil disimpan.";
		} else { echo "Data gagal disimpan, silahkan coba kembali.."; }
	}

	public function addPerubahanKontrak() // Done
	{
		$this->load->model('Contracting');
		$this->load->library("FileHandler");
		$proses4	= new Contracting();
		$statusProses	= new Contracting();
		$file = new FileHandler();

		$reqLinkFile		= $_FILES['reqLinkFile'];
		$reqLinkFileTemp 	= $this->input->post("reqLinkFileTemp");

		$reqContractingRekananProses4Id	= $this->input->post('reqContractingRekananProses4Id');
		$reqPaketId						= $this->input->post('reqPaketId');
		$reqContractingRekananId		= $this->input->post('reqContractingRekananId');
		$reqPerubahanAlasan				= $this->input->post('reqPerubahanAlasan');
		$reqContractingRekananProses1Id	= $this->input->post('reqContractingRekananProses1Id');
		$reqSubmit						= $this->input->post('reqSubmit');
		$reqTanggal						= date('Y-m-d H:i:s');

		$FILE_DIR = "uploads/kontrak/";

		$renameFile = "Perubahan_".md5(date("dmYHis").$reqLinkFile['name'].$this->ID).".".getExtension($reqLinkFile['name']);
		if($file->uploadToDir('reqLinkFile', $FILE_DIR, $renameFile))
		{
			$insertLinkFile =  $renameFile;
		}
		else
		{
			$insertLinkFile =  $reqLinkFileTemp;
		}

		$proses4->setField("PAKET_ID", $reqPaketId);
		$proses4->setField("CONTRACTINGREKANANID", $reqContractingRekananId);
		$proses4->setField("CR_PERUBAHAN", "1");
		$proses4->setField("CR_PERUBAHAN_ALASAN", $reqPerubahanAlasan);
		$proses4->setField('CR_PERUBAHAN_UPDATED_BY', $this->USER_LOGIN_ID);
		$proses4->setField("CR_PERUBAHAN_FILE", $insertLinkFile);

		if ($reqSubmit == 'simpan') {
			$insert = $proses4->insertProses4Perubahan();
		} else {
			$proses4->setField("CONTRACTINGREKANANPROSES4ID", $reqContractingRekananProses4Id);
			$insert = $proses4->updateProses4Perubahan();
		}

		if ($insert) { echo "Data berhasil disimpan.";
		// update Status
		$statusProses->setField("CONTRACTINGREKANANPROSES1ID", $reqContractingRekananProses1Id);
		$statusProses->setField("CONTRACTINGSTATUSKONTRAKID", 7);
		$statusProses->updateStatus();
		// End update Status
		} else { echo "Data gagal disimpan, silahkan coba kembali."; }

	}

	public function addAddendum() // Done
	{
		$this->load->model('Contractingaddendum');
		$this->load->library("FileHandler");
		$file = new FileHandler();
		$addendum	= new Contractingaddendum();

		$reqAddendumId					= $this->input->post('reqAddendumId');
		$reqPaketId						= $this->input->post('reqId'); // Paketid
		$reqContractingRekananId		= $this->input->post('reqContractingRekananId');
		$reqMode						= $this->input->post('reqMode');
		$reqNomor						= $this->input->post('reqNomor');
		$reqAddendumKe					= $this->input->post('reqAddendumKe');
		$reqTanggal						= $this->input->post('reqTanggal');
		$reqTanggalKontrakDari			= $this->input->post('reqTanggalKontrakDari');
		$reqTanggalKontrakSampai		= $this->input->post('reqTanggalKontrakSampai');
		$reqTanggalPenyelesaianKontrakDari	= $this->input->post('reqTanggalPenyelesaianKontrakDari');
		$reqTanggalPenyelesaianKontrakAkhir	= $this->input->post('reqTanggalPenyelesaianKontrakAkhir');
		$reqJenis							= $this->input->post('reqJenis');
		$reqKeterangan						= $this->input->post('reqKeterangan');
		$reqNilaiKontrak					= CommaToDot(dotToNo($this->input->post('reqNilaiKontrak')));

		if (!empty($reqJenis)) {
		    $jenisGabung = implode(', ', $reqJenis);
		} else {
		    $jenisGabung = '';
		}

		$reqLinkFile		= $_FILES['reqLinkFile'];
		$reqLinkFileTemp 	= $this->input->post("reqLinkFileTemp");

		$FILE_DIR = "uploads/kontrak/";

		$renameFile = "Addendum_Persetujuan_".md5(date("dmYHis").$reqLinkFile['name'].$this->ID).".".getExtension($reqLinkFile['name']);
		if($file->uploadToDir('reqLinkFile', $FILE_DIR, $renameFile))
		{
			$insertLinkFile =  $renameFile;
		}
		else
		{
			$insertLinkFile =  $reqLinkFileTemp;
		}

		$reqLinkFile2		= $_FILES['reqLinkFile2'];
		$reqLinkFile2Temp 	= $this->input->post("reqLinkFile2Temp");

		$renameFile2 = "Addendum_".md5(date("dmYHis").$reqLinkFile2['name'].$this->ID).".".getExtension($reqLinkFile2['name']);
		if($file->uploadToDir('reqLinkFile2', $FILE_DIR, $renameFile2))
		{
			$insertLinkFile2 =  $renameFile2;
		}
		else
		{
			$insertLinkFile2 =  $reqLinkFile2Temp;
		}

		$addendum->setField("PAKET_ID", $reqPaketId);
		$addendum->setField("CONTRACTINGREKANANID", $reqContractingRekananId);
		$addendum->setField("CONTRACTING_ADDENDUM_ID", $reqAddendumId);
		$addendum->setField("NOMOR", $reqNomor);
		$addendum->setField("ADDENDUM_KE", $reqAddendumKe);
		$addendum->setField("JENIS", $jenisGabung);
		$addendum->setField("TANGGAL", dateToDBCheck($reqTanggal));
		$addendum->setField("TANGGAL_KONTRAK_DARI", dateToDBCheck($reqTanggalKontrakDari));
		$addendum->setField("TANGGAL_KONTRAK_SAMPAI", dateToDBCheck($reqTanggalKontrakSampai));
		$addendum->setField("TANGGAL_PENYELESAIAN_KONTRAK_AWAL", dateToDBCheck($reqTanggalPenyelesaianKontrakDari));
		$addendum->setField("TANGGAL_PENYELESAIAN_KONTRAK_AKHIR", dateToDBCheck($reqTanggalPenyelesaianKontrakAkhir));
		$addendum->setField("ADDENDUM_FILE_PERSETUJUAN", $insertLinkFile);
		$addendum->setField("ADDENDUM_FILE", $insertLinkFile2);
		$addendum->setField("KETERANGAN", $reqKeterangan);
		$addendum->setField("ADDENDUM_NILAI", $reqNilaiKontrak);
		$addendum->setField('CREATED_BY', $this->USER_LOGIN_ID);

		if ($reqMode == 'simpan') {
			$insert = $addendum->insertAddendum();
		} else {
			$insert = $addendum->updateAddendum();
		}

		if ($insert) { echo "Data berhasil disimpan."; 
		// End update Status
		} else { echo "Data gagal disimpan, silahkan coba kembali."; }
	}

	public function addPenyesuaianKontrak() // Done
	{
		$this->load->model('Contracting');
		$proses4	= new Contracting();
		$statusProses	= new Contracting();

		$reqContractingRekananProses4Id	= $this->input->post('reqContractingRekananProses4Id');
		$reqPaketId						= $this->input->post('reqPaketId');
		$reqContractingRekananId		= $this->input->post('reqContractingRekananId');
		$reqPerubahanAlasan				= $this->input->post('reqPerubahanAlasan');
		$reqContractingRekananProses1Id	= $this->input->post('reqContractingRekananProses1Id');
		$reqSubmit						= $this->input->post('reqSubmit');
		$reqTanggal						= date('Y-m-d H:i:s');

		$proses4->setField("PAKET_ID", $reqPaketId);
		$proses4->setField("CONTRACTINGREKANANID", $reqContractingRekananId);
		$proses4->setField("CR_PENYESUAIAN", "1");
		$proses4->setField("CR_PENYESUAIAN_ALASAN", $reqPerubahanAlasan);
		$proses4->setField('CR_PENYESUAIAN_UPDATED_BY', $this->USER_LOGIN_ID);

		if ($reqSubmit == 'simpan') {
			$insert = $proses4->insertProses4Penyesuaian();
		} else {
			$proses4->setField("CONTRACTINGREKANANPROSES4ID", $reqContractingRekananProses4Id);
			$insert = $proses4->updateProses4Penyesuaian();
		}

		if ($insert) { echo "Data berhasil disimpan.";
		// update Status
		$statusProses->setField("CONTRACTINGREKANANPROSES1ID", $reqContractingRekananProses1Id);
		$statusProses->setField("CONTRACTINGSTATUSKONTRAKID", 8);
		$statusProses->updateStatus();
		// End update Status
		} else { echo "Data gagal disimpan, silahkan coba kembali."; }
	}

	public function addKaharKontrak() // Done
	{
		$this->load->model('Contracting');
		$proses4	= new Contracting();
		$statusProses	= new Contracting();

		$reqContractingRekananProses4Id	= $this->input->post('reqContractingRekananProses4Id');
		$reqPaketId						= $this->input->post('reqPaketId');
		$reqContractingRekananId		= $this->input->post('reqContractingRekananId');
		$reqPerubahanAlasan				= $this->input->post('reqPerubahanAlasan');
		$reqContractingRekananProses1Id	= $this->input->post('reqContractingRekananProses1Id');
		$reqSubmit						= $this->input->post('reqSubmit');
		$reqTanggal						= date('Y-m-d H:i:s');

		$proses4->setField("PAKET_ID", $reqPaketId);
		$proses4->setField("CONTRACTINGREKANANID", $reqContractingRekananId);
		$proses4->setField("CR_KAHAR", "1");
		$proses4->setField("CR_KAHAR_ALASAN", $reqPerubahanAlasan);
		$proses4->setField('CR_KAHAR_UPDATED_BY', $this->USER_LOGIN_ID);

		if ($reqSubmit == 'simpan') {
			$insert = $proses4->insertProses4Kahar();
		} else {
			$proses4->setField("CONTRACTINGREKANANPROSES4ID", $reqContractingRekananProses4Id);
			$insert = $proses4->updateProses4Kahar();
		}

		if ($insert) { echo "Data berhasil disimpan.";
		// update Status
		$statusProses->setField("CONTRACTINGREKANANPROSES1ID", $reqContractingRekananProses1Id);
		$statusProses->setField("CONTRACTINGSTATUSKONTRAKID", 9);
		$statusProses->updateStatus();
		// End update Status
		} else { echo "Data gagal disimpan, silahkan coba kembali."; }
	}

	public function addBerakhirKontrak() // Done
	{
		$this->load->model('Contracting');
		$proses4	= new Contracting();
		$statusProses	= new Contracting();

		$reqContractingRekananProses4Id	= $this->input->post('reqContractingRekananProses4Id');
		$reqPaketId						= $this->input->post('reqPaketId');
		$reqContractingRekananId		= $this->input->post('reqContractingRekananId');
		$reqPerubahanAlasan				= $this->input->post('reqPerubahanAlasan');
		$reqContractingRekananProses1Id	= $this->input->post('reqContractingRekananProses1Id');
		$reqSubmit						= $this->input->post('reqSubmit');
		$reqTanggal						= date('Y-m-d H:i:s');

		$proses4->setField("PAKET_ID", $reqPaketId);
		$proses4->setField("CONTRACTINGREKANANID", $reqContractingRekananId);
		$proses4->setField("CR_BERAKHIR", "1");
		$proses4->setField("CR_BERAKHIR_ALASAN", $reqPerubahanAlasan);
		$proses4->setField('CR_BERAKHIR_UPDATED_BY', $this->USER_LOGIN_ID);

		if ($reqSubmit == 'simpan') {
			$insert = $proses4->insertProses4Berakhir();
		} else {
			$proses4->setField("CONTRACTINGREKANANPROSES4ID", $reqContractingRekananProses4Id);
			$insert = $proses4->updateProses4Berakhir();
		}

		if ($insert) { echo "Data berhasil disimpan.";
		// update Status
		$statusProses->setField("CONTRACTINGREKANANPROSES1ID", $reqContractingRekananProses1Id);
		$statusProses->setField("CONTRACTINGSTATUSKONTRAKID", 10);
		$statusProses->updateStatus();
		// End update Status
		} else { echo "Data gagal disimpan, silahkan coba kembali."; }
	}

	public function addPemutusanKontrak() // Done
	{ 
		$this->load->model('Contracting');
		$this->load->library("FileHandler");
		$proses4	= new Contracting();
		$statusProses	= new Contracting();
		$file = new FileHandler();

		$reqLinkFile		= $_FILES['reqLinkFile'];
		$reqLinkFileTemp 	= $this->input->post("reqLinkFileTemp");

		$FILE_DIR = "uploads/kontrak/";

		$renameFile = "Pemutusan_".md5(date("dmYHis").$reqLinkFile['name'].$this->ID).".".getExtension($reqLinkFile['name']);
		if($file->uploadToDir('reqLinkFile', $FILE_DIR, $renameFile))
		{
			$insertLinkFile =  $renameFile;
		}
		else
		{
			$insertLinkFile =  $reqLinkFileTemp;
		}

		$reqContractingRekananProses4Id	= $this->input->post('reqContractingRekananProses4Id');
		$reqPaketId						= $this->input->post('reqPaketId');
		$reqContractingRekananId		= $this->input->post('reqContractingRekananId');
		$reqPerubahanAlasan				= $this->input->post('reqPerubahanAlasan');
		$reqContractingRekananProses1Id	= $this->input->post('reqContractingRekananProses1Id');
		$reqSubmit						= $this->input->post('reqSubmit');
		$reqTanggal						= date('Y-m-d H:i:s');

		$proses4->setField("PAKET_ID", $reqPaketId);
		$proses4->setField("CONTRACTINGREKANANID", $reqContractingRekananId);
		$proses4->setField("CR_PEMUTUSAN", "1");
		$proses4->setField("CR_PEMUTUSAN_ALASAN", $reqPerubahanAlasan);
		$proses4->setField('CR_PEMUTUSAN_UPDATED_BY', $this->USER_LOGIN_ID);
		$proses4->setField("CR_PEMUTUSAN_FILE", $insertLinkFile);

		if ($reqSubmit == 'simpan') {
			$insert = $proses4->insertProses4Pemutusan();
		} else {
			$proses4->setField("CONTRACTINGREKANANPROSES4ID", $reqContractingRekananProses4Id);
			$insert = $proses4->updateProses4Pemutusan();
		}

		if ($insert) { echo "Data berhasil disimpan.";
		// update Status
		$statusProses->setField("CONTRACTINGREKANANPROSES1ID", $reqContractingRekananProses1Id);
		$statusProses->setField("CONTRACTINGSTATUSKONTRAKID", 11);
		$statusProses->updateStatus();
		// End update Status
		} else { echo "Data gagal disimpan, silahkan coba kembali."; }
	}

	public function addKesempatanKontrak() // Done
	{
		$this->load->model('Contracting');
		$proses4	= new Contracting();
		$statusProses	= new Contracting();

		$reqContractingRekananProses4Id	= $this->input->post('reqContractingRekananProses4Id');
		$reqPaketId						= $this->input->post('reqPaketId');
		$reqContractingRekananId		= $this->input->post('reqContractingRekananId');
		$reqPerubahanAlasan				= $this->input->post('reqPerubahanAlasan');
		$reqContractingRekananProses1Id	= $this->input->post('reqContractingRekananProses1Id');
		$reqSubmit						= $this->input->post('reqSubmit');
		$reqTanggal						= date('Y-m-d H:i:s');

		$proses4->setField("PAKET_ID", $reqPaketId);
		$proses4->setField("CONTRACTINGREKANANID", $reqContractingRekananId);
		$proses4->setField("CR_KESEMPATAN", "1");
		$proses4->setField("CR_KESEMPATAN_ALASAN", $reqPerubahanAlasan);
		$proses4->setField('CR_KESEMPATAN_UPDATED_BY', $this->USER_LOGIN_ID);

		if ($reqSubmit == 'simpan') {
			$insert = $proses4->insertProses4Kesempatan();
		} else {
			$proses4->setField("CONTRACTINGREKANANPROSES4ID", $reqContractingRekananProses4Id);
			$insert = $proses4->updateProses4Kesempatan();
		}

		if ($insert) { echo "Data berhasil disimpan.";
		// update Status
		$statusProses->setField("CONTRACTINGREKANANPROSES1ID", $reqContractingRekananProses1Id);
		$statusProses->setField("CONTRACTINGSTATUSKONTRAKID", 12);
		$statusProses->updateStatus();
		// End update Status
		} else { echo "Data gagal disimpan, silahkan coba kembali."; }
	}

	public function addDendaKontrak() // Done
	{
		$this->load->library("FileHandler");
		$this->load->model('Contracting');
		$file = new FileHandler();
		$proses4	= new Contracting();
		$statusProses	= new Contracting();
		
		$reqLinkFile		= $_FILES['reqLinkFile'];
		$reqLinkFileTemp 	= $this->input->post("reqLinkFileTemp");
		$FILE_DIR = "uploads/kontrak/";

		$reqContractingRekananProses4Id	= $this->input->post('reqContractingRekananProses4Id');
		$reqPaketId						= $this->input->post('reqPaketId');
		$reqContractingRekananId		= $this->input->post('reqContractingRekananId');
		$reqPerubahanAlasan				= $this->input->post('reqPerubahanAlasan');
		$reqContractingRekananProses1Id	= $this->input->post('reqContractingRekananProses1Id');
		$reqSubmit						= $this->input->post('reqSubmit');
		$reqTanggal						= date('Y-m-d H:i:s');

		$renameFile = "Denda_".md5(date("dmYHis").$reqLinkFile['name'].$this->ID).".".getExtension($reqLinkFile['name']);
		if($file->uploadToDir('reqLinkFile', $FILE_DIR, $renameFile))
		{
			$insertLinkFile =  $renameFile;
		}
		else
		{
			$insertLinkFile =  $reqLinkFileTemp;
		}

		$proses4->setField("PAKET_ID", $reqPaketId);
		$proses4->setField("CONTRACTINGREKANANID", $reqContractingRekananId);
		$proses4->setField("CR_DENDA", "1");
		$proses4->setField("CR_DENDA_ALASAN", $reqPerubahanAlasan);
		$proses4->setField('CR_DENDA_UPDATED_BY', $this->USER_LOGIN_ID);
		$proses4->setField("CR_DENDA_FILE", $insertLinkFile);

		if ($reqSubmit == 'simpan') {
			$insert = $proses4->insertProses4Denda();
		} else {
			$proses4->setField("CONTRACTINGREKANANPROSES4ID", $reqContractingRekananProses4Id);
			$insert = $proses4->updateProses4Denda();
		}

		if ($insert) { echo "Data berhasil disimpan.";
		// update Status
		$statusProses->setField("CONTRACTINGREKANANPROSES1ID", $reqContractingRekananProses1Id);
		$statusProses->setField("CONTRACTINGSTATUSKONTRAKID", 13);
		$statusProses->updateStatus();
		// End update Status
		} else { echo "Data gagal disimpan, silahkan coba kembali."; }
	}

	public function addBASTHasil() // BAST Hasil Pekerjaan
	{
		$this->load->model('Contracting');
		$proses5	= new Contracting();
		$statusProses	= new Contracting();

		$reqContractingRekananProses5Id	= $this->input->post('reqContractingRekananProses5Id');
		$reqContractingRekananProses1Id	= $this->input->post('reqContractingRekananProses1Id');
		$reqPaketId						= $this->input->post('reqPaketId');
		$reqRekananId					= $this->input->post('reqRekananId');
		$reqContractingRekananId		= $this->input->post('reqContractingRekananId');
		$reqBastPekerjaanNomor			= $this->input->post('reqBastPekerjaanNomor');
		$reqBastPekerjaanTanggal		= $this->input->post('reqBastPekerjaanTanggal');
		$reqBastPekerjaanNamaPenyedia	= $this->input->post('reqBastPekerjaanNamaPenyedia');
		$reqBastPekerjaanJabatanPenyedia= $this->input->post('reqBastPekerjaanJabatanPenyedia');
		$reqBastPekerjaanNamaPenerima	= $this->input->post('reqBastPekerjaanNamaPenerima');
		$reqBastPekerjaanJabatanPenerima= $this->input->post('reqBastPekerjaanJabatanPenerima');
		$reqBastPekerjaanStatus= $this->input->post('reqBastPekerjaanStatus');
		$reqSubmit						= $this->input->post('reqSubmit');
		$reqTanggal						= date('Y-m-d H:i:s');

		$proses5->setField("PAKET_ID", $reqPaketId);
		$proses5->setField("REKANAN_ID", $reqRekananId);
		$proses5->setField("CONTRACTINGREKANANID", $reqContractingRekananId);
		$proses5->setField("CR_BAST_PEKERJAAN_NOMOR", $reqBastPekerjaanNomor);
		$proses5->setField("CR_BAST_PEKERJAAN_TANGGAL", dateToDBCheck($reqBastPekerjaanTanggal));
		$proses5->setField("CR_BAST_PEKERJAAN_NAMA_PENYEDIA", $reqBastPekerjaanNamaPenyedia);
		$proses5->setField("CR_BAST_PEKERJAAN_JABATAN_PENYEDIA", $reqBastPekerjaanJabatanPenyedia);
		$proses5->setField("CR_BAST_PEKERJAAN_NAMA_PENERIMA", $reqBastPekerjaanNamaPenerima);
		$proses5->setField("CR_BAST_PEKERJAAN_JABATAN_PENERIMA", $reqBastPekerjaanJabatanPenerima);
		$proses5->setField("CR_BAST_PEKERJAAN_STATUS", $reqBastPekerjaanStatus);
		$proses5->setField('CREATED_BY', $this->USER_LOGIN_ID);

		if ($reqSubmit == 'simpan') {
			$insert = $proses5->insertProses5Hasil();
		} else {
			$proses5->setField("CONTRACTINGREKANANPROSES5ID", $reqContractingRekananProses5Id);
			$insert = $proses5->updateProses5Hasil();
		}

		if ($insert) {
			$this->load->library("librekamjejak");
			$this->librekamjejak->insertRJ('268','Nomor: '.$reqBastPekerjaanNomor,'null','null','268',$reqContractingRekananId);
			echo "Data berhasil disimpan.";
		} else { echo "Data gagal disimpan, silahkan coba kembali."; }
	}

	public function addBASTHasilMulti() // BAST Hasil Pekerjaan
	{
		$this->load->model('Contracting');
		$proses5	= new Contracting();
		$statusProses	= new Contracting();

		$reqContractingRekananProses5Id	= $this->input->post('reqContractingRekananProses5Id');
		$reqContractingRekananProses1Id	= $this->input->post('reqContractingRekananProses1Id');
		$reqPaketId						= $this->input->post('reqPaketId');
		$reqRekananId					= $this->input->post('reqRekananId');
		$reqContractingRekananId		= $this->input->post('reqContractingRekananId');
		$reqBastPekerjaanNomor			= $this->input->post('reqBastPekerjaanNomor');
		$reqBastPekerjaanTanggal		= $this->input->post('reqBastPekerjaanTanggal');
		$reqBastPekerjaanNamaPenyedia	= $this->input->post('reqBastPekerjaanNamaPenyedia');
		$reqBastPekerjaanJabatanPenyedia= $this->input->post('reqBastPekerjaanJabatanPenyedia');
		$reqBastPekerjaanNamaPenerima	= $this->input->post('reqBastPekerjaanNamaPenerima');
		$reqBastPekerjaanJabatanPenerima= $this->input->post('reqBastPekerjaanJabatanPenerima');
		$reqBastPekerjaanStatus= $this->input->post('reqBastPekerjaanStatus');
		$reqSubmit						= $this->input->post('reqSubmit');
		$reqTanggal						= date('Y-m-d H:i:s');

		$proses5->setField("PAKET_ID", $reqPaketId);
		$proses5->setField("REKANAN_ID", $reqRekananId);
		$proses5->setField("CONTRACTINGREKANANID", $reqContractingRekananId);
		$proses5->setField("CR_BAST_PEKERJAAN_NOMOR", $reqBastPekerjaanNomor);
		$proses5->setField("CR_BAST_PEKERJAAN_TANGGAL", dateToDBCheck($reqBastPekerjaanTanggal));
		$proses5->setField("CR_BAST_PEKERJAAN_NAMA_PENYEDIA", $reqBastPekerjaanNamaPenyedia);
		$proses5->setField("CR_BAST_PEKERJAAN_JABATAN_PENYEDIA", $reqBastPekerjaanJabatanPenyedia);
		$proses5->setField("CR_BAST_PEKERJAAN_NAMA_PENERIMA", $reqBastPekerjaanNamaPenerima);
		$proses5->setField("CR_BAST_PEKERJAAN_JABATAN_PENERIMA", $reqBastPekerjaanJabatanPenerima);
		$proses5->setField("CR_BAST_PEKERJAAN_STATUS", $reqBastPekerjaanStatus);
		$proses5->setField('CREATED_BY', $this->USER_LOGIN_ID);

		if ($reqSubmit == 'simpan') {
			$insert = $proses5->insertProses5HasilMulti();
		} else {
			$proses5->setField("CONTRACTINGREKANANPROSES5ID", $reqContractingRekananProses5Id);
			$insert = $proses5->updateProses5Hasil();
		}

		if ($insert) {
			$this->load->library("librekamjejak");
			$this->librekamjejak->insertRJ('268','Nomor: '.$reqBastPekerjaanNomor,'null','null','268',$reqContractingRekananId);
			echo "Data berhasil disimpan.";
		} else { echo "Data gagal disimpan, silahkan coba kembali."; }
	}

	public function addBASTPemeliharaan() // BAST Masa Pemiliharaan
	{
		$this->load->model('Contracting');
		$proses5	= new Contracting();
		$statusProses	= new Contracting();

		$reqContractingRekananProses5Id	= $this->input->post('reqContractingRekananProses5Id');
		$reqContractingRekananProses1Id	= $this->input->post('reqContractingRekananProses1Id');
		$reqPaketId						= $this->input->post('reqPaketId');
		$reqContractingRekananId		= $this->input->post('reqContractingRekananId');
		$reqBastMasaNomor			= $this->input->post('reqBastMasaNomor');
		$reqBastMasaTanggal		= $this->input->post('reqBastMasaTanggal');
		$reqBastMasaNamaPenyedia	= $this->input->post('reqBastMasaNamaPenyedia');
		$reqBastMasaJabatanPenyedia= $this->input->post('reqBastMasaJabatanPenyedia');
		$reqBastMasaNamaPenerima	= $this->input->post('reqBastMasaNamaPenerima');
		$reqBastMasaJabatanPenerima= $this->input->post('reqBastMasaJabatanPenerima');
		$reqBastMasaStatus= $this->input->post('reqBastMasaStatus');
		$reqSubmit						= $this->input->post('reqSubmit');
		$reqTanggal						= date('Y-m-d H:i:s');

		$proses5->setField("PAKET_ID", $reqPaketId);
		$proses5->setField("CONTRACTINGREKANANID", $reqContractingRekananId);
		$proses5->setField("CR_BAST_MASA_NOMOR", $reqBastMasaNomor);
		$proses5->setField("CR_BAST_MASA_TANGGAL", dateToDBCheck($reqBastMasaTanggal));
		$proses5->setField("CR_BAST_MASA_NAMA_PENYEDIA", $reqBastMasaNamaPenyedia);
		$proses5->setField("CR_BAST_MASA_JABATAN_PENYEDIA", $reqBastMasaJabatanPenyedia);
		$proses5->setField("CR_BAST_MASA_NAMA_PENERIMA", $reqBastMasaNamaPenerima);
		$proses5->setField("CR_BAST_MASA_JABATAN_PENERIMA", $reqBastMasaJabatanPenerima);
		$proses5->setField("CR_BAST_MASA_STATUS", $reqBastMasaStatus);
		$proses5->setField('CREATED_BY', $this->USER_LOGIN_ID);

		if ($reqSubmit == 'simpan') {
			$insert = $proses5->insertProses5Pemeliharaan();
		} else {
			$proses5->setField("CONTRACTINGREKANANPROSES5ID", $reqContractingRekananProses5Id);
			$insert = $proses5->updateProses5Pemeliharaan();
		}

		if ($insert) {
			$this->load->library("librekamjejak");
			$this->librekamjejak->insertRJ('269','Nomor: '.$reqBastMasaNomor,'null','null','269',$reqContractingRekananId);
			echo "Data berhasil disimpan.";
		} else { echo "Data gagal disimpan, silahkan coba kembali."; }
	}

	function approveKontrak() // Done
	{
		$this->load->model("Contracting");
		$proses1	= new Contracting();

		$reqPemeriksaNama= $this->input->post('reqPemeriksaNama');
		$reqPemeriksaJabatan= $this->input->post('reqPemeriksaJabatan');
		$contractingrekananid= $this->input->post('contractingrekananid');

		$proses1->setField("CR_PEMERIKSA_NAMA", $reqPemeriksaNama);
		$proses1->setField("CR_PEMERIKSA_JABATAN", $reqPemeriksaJabatan);
		$proses1->setField("CR_PEMERIKSA_APPROVAL", '1');
		$proses1->setField("CONTRACTINGREKANANID", $contractingrekananid);
		$proses1->setField("CREATED_BY", $this->USER_LOGIN_ID);

		$insert = $proses1->updatePemeriksa();

		if($insert) {

			$arrJson["PESAN"] = "Data berhasil diproses";
			$arrJson["FLOW"] = $reqContractingStatusKontrakId;
		}
		else {
			$arrJson["PESAN"] = "Data gagal diproses, silahkan dicoba kembali!";
			$arrJson["FLOW"] = '0';
		}

		echo json_encode($arrJson);

	}

	function comboJenisPengadaan()
	{
		$this->load->model('Contracting'); $contracting = new Contracting();

		$contracting->selectJenisPengadaan(array('AKTIF' => '1'));
		$i = 0;
		while($contracting->nextRow())
		{
			$arr_json[$i]['id']		= $contracting->getField("PAKET_JENIS_ID");
			$arr_json[$i]['text']	= $contracting->getField("NAMA");
			$i++;
		}
		echo json_encode($arr_json);
	}

	function comboJenisPekerjaan()
	{
		$this->load->model('Contracting'); $contracting = new Contracting();

		$contracting->selectJenisPekerjaan();
		$i = 0;
		while($contracting->nextRow())
		{
			$arr_json[$i]['id']		= $contracting->getField("CONTRACTINGJENISPEKERJAANID");
			$arr_json[$i]['text']	= $contracting->getField("JP_NAME");
			$i++;
		}
		echo json_encode($arr_json);
	}

	function comboJenisKontrak()
	{
		$this->load->model('Contracting'); $contracting = new Contracting();

		$contracting->selectJenisKontrak();
		$i = 0;
		while($contracting->nextRow())
		{
			$arr_json[$i]['id']		= $contracting->getField("CONTRACTINGJENISKONTRAKID");
			$arr_json[$i]['text']	= $contracting->getField("JK_NAME");
			$i++;
		}
		echo json_encode($arr_json);
	}

	function comboPengadaanBypass()
	{
		$variable = array(
							'1' => 'Ya',
							'0' => 'Tidak',
						);
		// krsort($variable);

		$i = 0;
		foreach ($variable as $key => $value) {
			$arr_json[$i]['id']		= $key;
			$arr_json[$i]['text']	= $value;
			$i++;
		}

		echo json_encode($arr_json);
	}

	function comboStatusKontrak()
	{
		$this->load->model('Contracting'); $contracting = new Contracting();

		$contracting->selectStatusKontrak();
		$i = 0;
		while($contracting->nextRow())
		{
			$arr_json[$i]['id']		= $contracting->getField("CONTRACTINGSTATUSKONTRAKID");
			$arr_json[$i]['text']	= $contracting->getField("SK_NAME");
			$i++;
		}
		echo json_encode($arr_json);
	}

	function comboMaterial($contractingrekananid)
	{
		$this->load->model('Contractingmaterial'); $material = new Contractingmaterial();

		$material->selectByParams(array("CONTRACTINGREKANANID" => $contractingrekananid));

		$i = 0;
		while($material->nextRow())
		{
			$arr_json[$i]['id']		= $material->getField("MATERIALID");
			$arr_json[$i]['text']	= $material->getField("NAMA");
			$i++;
		}
		echo json_encode($arr_json);
	}

	function comboMaterialAll()
	{
		$this->load->model('Contractingmaterial'); $material = new Contractingmaterial();

		$material->selectByParams();

		$i = 0;
		while($material->nextRow())
		{
			$arr_json[$i]['id']		= $material->getField("MATERIALID");
			$arr_json[$i]['text']	= $material->getField("NAMA");
			$i++;
		}
		echo json_encode($arr_json);
	}

	function comboKegiatan()
	{
		$this->load->model('Contractingkegiatan'); $kegiatan = new Contractingkegiatan();

		$kegiatan->selectByParams();

		$i = 0;
		while($kegiatan->nextRow())
		{
			$arr_json[$i]['id']		= $kegiatan->getField("CONTRACTING_KEGIATAN_ID");
			$arr_json[$i]['text']	= $kegiatan->getField("KEGIATAN");
			$i++;
		}
		echo json_encode($arr_json);
	}

	function comboMaterialData($materialid)
	{
		$this->load->model('Contractingmaterial'); $material = new Contractingmaterial();

		$material->selectByParams(array("MATERIALID" => $materialid));
		$material->firstRow();
		$arr_json['nama']			= $material->getField("NAMA");
		$arr_json['harga_satuan']	= $material->getField("HARGA_SATUAN");
		$arr_json['satuan']	= $material->getField("SATUAN_STR");
		$arr_json['sifat']	= $material->getField("SIFAT");
		$arr_json['qty']	= $material->getField("QTY");
		$arr_json['keterangan']		= $material->getField("KETERANGAN");

		echo json_encode($arr_json);
	}

	function comboSatuanData()
	{
		$this->load->model('Satuan'); $satuan = new Satuan();

		$satuan->selectByParams(array());

		$i = 0;
		while($satuan->nextRow())
		{
			$arr_json[$i]['id']		= $satuan->getField("SATUANID");
			$arr_json[$i]['text']	= $satuan->getField("KODE");
			$i++;
		}
		echo json_encode($arr_json);
	}

	function comboJenisContrack()
	{
		$variable = array(
							'1' => 'Surat Perjanjian',
							'0' => 'SPK',
							// '3' => 'Surat Pesanan'
						);

		// sort key DESC → 1 dulu, lalu 0
		krsort($variable);

		$i = 0;
		foreach ($variable as $key => $value) {
			$arr_json[$i]['id']		= $key;
			$arr_json[$i]['text']	= $value;
			$i++;
		}

		echo json_encode($arr_json);
	}

	function getPemenang($reqId,$multiPemenang)
	{
		$this->load->model('Paketpemenang');
		$getpaket_pemenang = new Paketpemenang();

  		$getpaket_pemenang->selectByParams(array("PAKET_ID" => $reqId), -1, -1);

  		if ($multiPemenang == '1') { // Kontrak Payung
		  $getpaket_pemenang->selectByParams(array("PAKET_ID" => $reqId), -1, -1," AND B.REKANAN_ID NOT IN (SELECT REKANAN_ID FROM view_contracting_rekanan_proses1_sppbj WHERE PAKET_ID = $reqId)");
		} else {
		  $getpaket_pemenang->selectByParams(array("PAKET_ID" => $reqId, "PERINGKAT" => '1'), -1, -1);
		}

		$i = 0;
		while($getpaket_pemenang->nextRow())
		{
			$arr_json[$i]['id']		= $getpaket_pemenang->getField("REKANAN_ID");
			$arr_json[$i]['text']	= $getpaket_pemenang->getField("NAMA");
			$i++;
		}

		echo json_encode($arr_json);
	}

	public function getDataPemenang($rekananid)
	{
		$this->load->model("Rekanan");
		$this->load->model("RekananPengurus");
		$rekanan = new Rekanan();
		$rekananpengurus = new RekananPengurus();

		$rekanan->selectByParams(array("A.REKANAN_ID" => $rekananid), -1, -1);
		$rekanan->firstRow();

		$rekananpengurus->selectByParams(array("REKANAN_ID" => $rekananid), 1, -1, " AND lower(jabatan) like  '%direktur%'");
		$rekananpengurus->firstRow();

		$rekanan_nama = $rekanan->getField("NAMA");
		$rekanan_npwp = $rekanan->getField("NPWP");
		$rekanan_telepon = $rekanan->getField("TELEPON_FULL");
		$rekanan_email = $rekanan->getField("EMAIL");
		$rekanan_alamat = $rekanan->getField("ALAMAT");
		$rekanan_kota = $rekanan->getField("KOTA");
		$rekanan_kodepos = $rekanan->getField("KODEPOS");
		$rekanan_region = $rekanan->getField("NAMAPROPINSI");

		$rekanan_direktur = $rekananpengurus->getField("NAMA");
		$rekanan_jabatan = $rekananpengurus->getField("JABATAN");

		echo json_encode(array(
								'nama' => $rekanan_nama,
								'npwp' => $rekanan_npwp,
								'telepon' => $rekanan_telepon,
								'email' => $rekanan_email,
								'alamat' => $rekanan_alamat.' '.$rekanan_kota.' '.$rekanan_kodepos,
								'direktur' => $rekanan_direktur,
								'jabatan' => $rekanan_jabatan,
								'kota' => $rekanan_region,
							));
	}

	function cekSisaQty($contractingrekananid,$materialid)
	{
		$this->load->model('Contracting'); $cekSisaQty = new Contracting();

		$cekSisaQty->cekSisaQty($contractingrekananid,$materialid);
		$cekSisaQty->firstRow();

		$arr_json['nama']			= $cekSisaQty->getField("NAMA");
		$arr_json['qtymaksimal']	= $cekSisaQty->getField("QTY_MAKSIMAL");
		$arr_json['qtytotal']		= $cekSisaQty->getField("QTY_TOTAL");
		$arr_json['sisa']			= $cekSisaQty->getField("SISA");

		echo json_encode($arr_json);
	}

	function cekSisaNilaiKontrak($contractingrekananid,$rekananid)
	{
		$v = $this->libkontrak->getNilaiKontrakPenyediaByNilai(" AND REKANAN_ID = ".$rekananid." AND CONTRACTINGREKANANID = ".$contractingrekananid);

		$arr_json['nilai_kontrak']	= $v['nilai_kontrak'];
		$arr_json['total']			= $v['total'];
		$arr_json['sisa']			= $v['sisa'];

		echo json_encode($arr_json);

	}

	function deleteSppbj()
	{
		$this->load->model('Sppjb');

		$sppjb	= new Sppjb();

		$reqId		= $this->input->get('reqId');
		$reqKode	= $this->input->post('reqKode');

		// $sppjb		= new Sppjb();
		$sppjb->setField("PAKET_ID", $reqId);
		$sppjb->delete();
		echo "Data berhasil dihapus.";
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
			echo "Data berhasil dikirim ke penyedia.";
		else
			echo "Data berhasil di batalkan ke penyedia.";
	}

	function approvalAddendum() // Done
	{
		$this->load->model('Contractingaddendum');

		$addendum	= new Contractingaddendum();

		$reqId		= $this->input->get('reqId');
		$status		= $this->input->get('status');

		$addendum->setField("CONTRACTING_ADDENDUM_ID", $reqId);
		$addendum->setField("APPROVED_KASUBDIT", $status);
		$addendum->approvalKasuhdit();

		if ($status == '1')
			echo "Data berhasil disetujui.";
		else
			echo "Data berhasil batal seuju.";
	}

	function approvalPPK() // Done
	{
		$this->load->model('PaketPenilaian');
		$modelPenilaian	= new PaketPenilaian();

		$reqId		= $this->input->get('reqId');
		$status		= $this->input->get('status');

		$modelPenilaian->setField("CONTRACTINGREKANANID", $reqId);
		$modelPenilaian->setField("APPROVAL_PPK", $status);
		$modelPenilaian->setField("CREATED_BY", $this->USER_LOGIN_ID);
		$modelPenilaian->setApprovalPpk();

		if ($status == '1')
			echo "Penilaian berhasil disetujui.";
		else
			echo "Penilaian berhasil batal seuju.";
	}

	function approvalKasubdit() // Done
	{
		$this->load->model('PaketPenilaian');
		$modelPenilaian	= new PaketPenilaian();

		$reqId		= $this->input->get('reqId');
		$status		= $this->input->get('status');

		$modelPenilaian->setField("CONTRACTINGREKANANID", $reqId);
		$modelPenilaian->setField("APPROVAL_KASUBDIT", $status);
		$modelPenilaian->setField("CREATED_BY", $this->USER_LOGIN_ID);
		$modelPenilaian->setApprovalKasubdit();

		if ($status == '1')
			echo "Penilaian berhasil disetujui.";
		else
			echo "Penilaian berhasil batal seuju.";
	}

	function approvalPICUnit() // Done
	{
		$this->load->model('PaketPenilaian');
		$modelPenilaian	= new PaketPenilaian();

		$reqId		= $this->input->post('reqId');
		$status		= $this->input->post('status');

		$modelPenilaian->setField("CONTRACTINGREKANANID", $reqId);
		$modelPenilaian->setField("APPROVAL_UNIT", $status);
		$modelPenilaian->setField("CREATED_BY", $this->USER_LOGIN_ID);
		$modelPenilaian->setApprovalUnit();

		if ($status == '1')
			echo "Penilaian berhasil disetujui.";
		else
			echo "Penilaian berhasil batal seuju.";
	}

	function approvalAddendumPenyedia() // Done
	{
		$this->load->model('Contractingaddendum');

		$addendum	= new Contractingaddendum();

		$reqId		= $this->input->get('reqId');
		$status		= $this->input->get('status');

		$addendum->setField("CONTRACTING_ADDENDUM_ID", $reqId);
		$addendum->setField("APPROVED_PENYEDIA", $status);
		$addendum->approvalPenyedia();

		if ($status == '1')
			echo "Data berhasil disetujui.";
		else
			echo "Data berhasil batal setuju.";
	}

	function approvalAddendumClose() // Done
	{
		$this->load->model('Contractingaddendum');

		$addendum	= new Contractingaddendum();

		$reqId		= $this->input->get('reqId');
		$status		= $this->input->get('status');

		$addendum->setField("CONTRACTING_ADDENDUM_ID", $reqId);
		$addendum->setField("STATUS", $status);

		if ($addendum->addendumClose())
			echo "Data berhasil diupdate.";
		else
			echo "Data gagal diupdate.";
	}


	// Kontrak Payung
	// Surat Pesanan
	function addSuratPesanan()
	{
		$this->load->model("Contractingsuratpesanan");

		$reqSubmit						= $this->input->post('reqSubmit');
		$reqSuratPesananId				= $this->input->post('reqSuratPesananId');
		$reqNoSuratPesanan				= $this->input->post('reqNoSuratPesanan');
		$reqTglSuratPesanan				= $this->input->post('reqTglSuratPesanan');
		$reqContractingRekananProses1Id	= $this->input->post('reqContractingRekananProses1Id');
		$reqContractingRekananId		= $this->input->post('reqContractingRekananId');

		$suratpensanan	= new Contractingsuratpesanan();
		$suratpensanan->setField("NOMOR_SURAT", $reqNoSuratPesanan);
		$suratpensanan->setField("TANGGAL", dateToDBCheck($reqTglSuratPesanan));
		$suratpensanan->setField("CONTRACTINGREKANANPROSES1ID", $reqContractingRekananProses1Id);
		$suratpensanan->setField('CREATED_BY', $this->USER_LOGIN_ID);

		if ($reqSubmit == 'simpan') {

			$insert = $suratpensanan->insert();

	        $suratpesananid = $suratpensanan->id;

			$suratpensananmaterial	= new Contractingsuratpesanan();
			$suratpensananmaterial->setField("SURATPESANANID", $suratpesananid);
			$reqMaterial		= $_POST["reqMaterial"];
			$reqNama			= $_POST["reqNama"];
			$reqSatuan			= $_POST["reqSatuan"];
			$reqSifat			= $_POST["reqSifat"];
			$reqDeskripsi		= $_POST["reqDeskripsi"];
			$reqhargaSatuan		= CommaToDot(dotToNo($_POST["reqhargaSatuan"]));
			$reqQty				= CommaToDot(dotToNo($_POST["reqQty"]));
			$reqTotal			= CommaToDot(dotToNo($_POST["reqTotal"]));

			// // hapus data semua dulu kemudian insert
			// $delivery2	= new Contractingsuratpesanan();
			// $delivery2->setField("SURATPESANANID", $contractingrekananid);
			// $delivery2->delAll();
	  //     	unset($delivery2);

			for($i=0; $i<count($reqMaterial);$i++)
	        {
				$suratpensananmaterial	= new Contractingsuratpesanan();
				$suratpensananmaterial->setField("SURATPESANANID", $suratpesananid);
				$suratpensananmaterial->setField("MATERIALID", $reqMaterial[$i]);
				$suratpensananmaterial->setField("NAMA", $reqNama[$i]);
				$suratpensananmaterial->setField("HARGA_SATUAN", $reqhargaSatuan[$i]);
				$suratpensananmaterial->setField("KETERANGAN", $reqDeskripsi[$i]);
				$suratpensananmaterial->setField("QTY", $reqQty[$i]);
				$suratpensananmaterial->setField("TOTAL", $reqTotal[$i]);
				$suratpensananmaterial->setField("SATUAN", $reqSatuan[$i]);
				$suratpensananmaterial->setField("SIFAT", $reqSifat[$i]);
				$suratpensananmaterial->setField('CREATED_BY', $this->USER_LOGIN_ID);
				$insertSPMaterial = $suratpensananmaterial->inserMaterial();
	          	unset($suratpensananmaterial);
	        }

			if ($insertSPMaterial) {
				$this->load->library("librekamjejak");
				$this->librekamjejak->insertRJ('270','No. Surat Pesanan: '.$reqNoSuratPesanan,'null','null','270',$reqContractingRekananId);
				echo "0--Data berhasil disimpan.";
			} else { echo "1--Data gagal disimpan, silahkan coba kembali."; }

		} else { // Update
			$suratpesananid 		= $this->input->post('reqSuratPesananId');
			$suratpensanan->setField("SURATPESANANID", $suratpesananid);

			$suratpensanan->update();

			$suratpensananmaterial	= new Contractingsuratpesanan();
			$suratpensananmaterial->setField("SURATPESANANID", $suratpesananid);
			$reqMaterial		= $_POST["reqMaterial"];
			$reqNama			= $_POST["reqNama"];
			$reqSatuan			= $_POST["reqSatuan"];
			$reqSifat			= $_POST["reqSifat"];
			$reqDeskripsi		= $_POST["reqDeskripsi"];
			$reqhargaSatuan		= CommaToDot(dotToNo($_POST["reqhargaSatuan"]));
			$reqQty				= CommaToDot(dotToNo($_POST["reqQty"]));
			$reqTotal			= CommaToDot(dotToNo($_POST["reqTotal"]));

			// hapus data semua dulu kemudian insert
			$suratpesananmaterialDel	= new Contractingsuratpesanan();
			$suratpesananmaterialDel->setField("SURATPESANANID", $suratpesananid);
			$suratpesananmaterialDel->delMaterialAll();
	      	unset($suratpesananmaterialDel);

			for($i=0; $i<count($reqMaterial);$i++)
	        {
				$suratpensananmaterial	= new Contractingsuratpesanan();
				$suratpensananmaterial->setField("SURATPESANANID", $suratpesananid);
				$suratpensananmaterial->setField("MATERIALID", $reqMaterial[$i]);
				$suratpensananmaterial->setField("NAMA", $reqNama[$i]);
				$suratpensananmaterial->setField("HARGA_SATUAN", $reqhargaSatuan[$i]);
				$suratpensananmaterial->setField("KETERANGAN", $reqDeskripsi[$i]);
				$suratpensananmaterial->setField("QTY", $reqQty[$i]);
				$suratpensananmaterial->setField("TOTAL", $reqTotal[$i]);
				$suratpensananmaterial->setField("SATUAN", $reqSatuan[$i]);
				$suratpensananmaterial->setField("SIFAT", $reqSifat[$i]);
				$suratpensananmaterial->setField('CREATED_BY', $this->USER_LOGIN_ID);
				$insertSPMaterial = $suratpensananmaterial->inserMaterial();
	          	unset($suratpensananmaterial);
	        }
			if ($insertSPMaterial) {
				$this->load->library("librekamjejak");
				$this->librekamjejak->insertRJ('270','No. Surat Pesanan: '.$reqNoSuratPesanan,'null','null','270',$reqContractingRekananId);
				echo "0--Data berhasil disimpan.";
			} else { echo "1--Data gagal disimpan, silahkan coba kembali."; }

		}
	}

	function delSuratPesanan()  //
	{
		$this->load->model("Contractingsuratpesanan");

		$reqId = $this->input->get("reqId");

		$delSuratPesananMaterial = new Contractingsuratpesanan();

		$delSuratPesananMaterial->setField("SURATPESANANID", $reqId);
		if($delSuratPesananMaterial->delMaterialAll())
		{
			$delSuratPesanan = new Contractingsuratpesanan();
			$delSuratPesanan->setField("SURATPESANANID", $reqId);
			if($delSuratPesanan->delSuratPesanan())
			{
				echo 'Data berhasil dihapus.';
			} else {
				echo 'Data gagal dihapus.';
			}
		}
		else
		{
			echo 'Data gagal dihapus.';
		}
	}

	function updateSuratPesananMaterial()
	{
		$this->load->model("Contractingsuratpesanan");

		$reqSubmit						= $this->input->post('reqSubmit');
		$reqSuratPesananId				= $this->input->post('reqSuratPesananId');

		$suratpensananmaterial	= new Contractingsuratpesanan();
		$suratpensananmaterial->setField("SURATPESANANID", $suratpesananid);
		$reqMaterial		= $_POST["reqMaterial"];
		$reqStatusTerima	= $_POST["reqStatusTerima"];
		$reqKeterangan		= $_POST["reqKeterangan"];
		$reqContractingRekananId	= $_POST["reqContractingRekananId"];
		$reqTanggalTerima	= $_POST["reqTanggalTerima"];
		$reqPersentase	= $_POST["reqPersentase"];

		for($i=0; $i<count($reqMaterial);$i++)
        {
			$suratpensananmaterial	= new Contractingsuratpesanan();
			$suratpensananmaterial->setField("STATUS_TERIMA", $reqStatusTerima[$i]);
			$suratpensananmaterial->setField("SURATPESANANMATERIALID", $reqMaterial[$i]);
			$suratpensananmaterial->setField("STATUS_KETERANGAN", $reqKeterangan[$i]);
			$suratpensananmaterial->setField("TANGGAL_TERIMA", dateToDBCheck($reqTanggalTerima[$i]));
			$suratpensananmaterial->setField("PRESENTASE", $reqPersentase[$i]);
			$suratpensananmaterial->setField('CREATED_BY', $this->USER_LOGIN_ID);
			$insertSPMaterial = $suratpensananmaterial->updateSuratPesananMaterial();
          	unset($suratpensananmaterial);
        }

		if ($insertSPMaterial) {
			$this->load->library("librekamjejak");
			$this->librekamjejak->insertRJ('271','','null','null','271',$reqContractingRekananId);
			echo "Data berhasil disimpan.";
		} else { echo "Data gagal disimpan, silahkan coba kembali."; }

	}

	function addCatatan() // Done
	{
		$this->load->model("Contractingcatatan");
		// echo "<pre>"; print_r($this->input->post()); die();
		$reqId	= $this->input->post('reqId');
		$reqJenis	= $this->input->post('reqJenis');
		$pesan				= $_POST["reqPesan"];

		// hapus data semua dulu kemudian insert
		$catatan2	= new Contractingcatatan();
		$catatan2->setField("PAKET_ID", $reqId);
		$catatan2->setField("JENIS", $reqJenis);
		$catatan2->setField('CREATED_BY', $this->USER_LOGIN_ID);
		$catatan2->delAll();
      	unset($catatan2);

      	if ($pesan) {
			for($i=0; $i<count($pesan);$i++)
	        {
				$catatan	= new Contractingcatatan();
				$catatan->setField("PAKET_ID", $reqId);
				$catatan->setField("PESAN", $pesan[$i]);
				$catatan->setField("JENIS", $reqJenis);
				$catatan->setField('CREATED_BY', $this->USER_LOGIN_ID);
				$insert = $catatan->inserCatatan();
	          	unset($catatan);
	        }

			if ($insert) {
				$this->load->library("librekamjejak");
				$this->librekamjejak->insertRJ('279','','null','null','279',$reqId);
				echo "Data berhasil disimpan.";
			} else { echo "Data gagal disimpan, silahkan coba kembali."; }
		} else {
			echo 'Catatan tidak boleh kosong';
		}
	}

	function addCatatanPenyedia() // Done
	{
		$this->load->model("Contractingcatatan");
		// echo "<pre>"; print_r($this->input->post()); die();
		$reqId	= $this->input->post('reqId');
		$reqJenis	= $this->input->post('reqJenis');
		$pesan				= $_POST["reqPesan"];

		// hapus data semua dulu kemudian insert
		$catatan2	= new Contractingcatatan();
		$catatan2->setField("PAKET_ID", $reqId);
		$catatan2->setField("JENIS", $reqJenis);
		$catatan2->setField("CREATED_BY", $this->USER_LOGIN_ID);
		$catatan2->delAllPenyedia();
      	unset($catatan2);

      	if ($pesan) {
			for($i=0; $i<count($pesan);$i++)
	        {
				$catatan	= new Contractingcatatan();
				$catatan->setField("PAKET_ID", $reqId);
				$catatan->setField("PESAN", $pesan[$i]);
				$catatan->setField("JENIS", $reqJenis);
				$catatan->setField('CREATED_BY', $this->USER_LOGIN_ID);
				$insert = $catatan->inserCatatan();
	          	unset($catatan);
	        }

			if ($insert) {
				$this->load->library("librekamjejak");
				$this->librekamjejak->insertRJ('279','','null','null','279',$reqId);
				echo "Data berhasil disimpan.";
			} else { echo "Data gagal disimpan, silahkan coba kembali."; }
		} else {
			echo 'Catatan tidak boleh kosong';
		}
	}

	function contracting_pembeli()
	{

		$this->load->model("Paket");
		$paket = new Paket();

		$reqStatus= $this->input->get("reqStatus");

		$aColumns 			= array('PAKET_ID','NAMA','NILAI_OWNER_ESTIMATE','STATUS','METODE_LELANG');
		$aColumnsAlias		= array('PAKET_ID', 'NAMA','NILAI_OWNER_ESTIMATE','STATUS','METODE_LELANG');

		if ( isset( $_GET['iSortCol_0'] ) )
		{
			$sOrder = " ORDER BY ";

			for ( $i=0 ; $i<intval( $_GET['iSortingCols'] ) ; $i++ )
			{
				if ( $_GET[ 'bSortable_'.intval($_GET['iSortCol_'.$i]) ] == "true" )
				{
					$sOrder .= $aColumnsAlias[ intval( $_GET['iSortCol_'.$i] ) ];
					if (strcasecmp(( $_GET['sSortDir_'.$i] ), "asc") == 1)
					{
						$sOrder .=" asc, ";
					}else
					{
						$sOrder .=" desc, ";
					}
				}
			}

			$sOrder = substr_replace( $sOrder, "", -2 );

			if ( trim($sOrder) == "ORDER BY NAMA desc" )
			{
				$sOrder = " ORDER BY A.NAMA ASC";

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

		$statement='';

		if($reqStatus == '0')
			$statement .= " AND A.PAKET_METODE_LELANG_ID IN ('6','9','12') ";
		else
			$statement .= " AND A.PAKET_METODE_LELANG_ID IN ('".$reqStatus."') ";

		$statement .= "AND (UPPER(A.NAMA) LIKE '%".strtoupper($_GET['sSearch'])."%' ) ";
    	$statement .= ' AND A.PPK = '.$this->USER_LOGIN_ID.'';
		$allRecord = $paket->getCountByParams(array(), $statement);

		if($_GET['sSearch'] == "")
			$allRecordFilter = $allRecord;
		else
			$allRecordFilter =  $paket->getCountByParams(array(), $statement);

		$paket->selectByParamsWithKatalog(array(), $dsplyRange, $dsplyStart, $statement, $sOrder);
		// echo $paket->query; die;
		$output = array(
			"sEcho" => intval($_GET['sEcho']),
			"iTotalRecords" => $allRecord,
			"iTotalDisplayRecords" => $allRecordFilter,
			"aaData" => array()
		);

		while($paket->nextRow())
		{
			$row = array();
			for ( $i=0 ; $i<count($aColumns) ; $i++ )
			{
				if($aColumns[$i]=='NO') {
					$row[] = $number;
				} elseif($aColumns[$i]=='STATUS')
				{
				  if ($paket->getField("ALASAN") != "") {
					$row[] = '<span class="badge badge-danger"> Paket di Batalkan <br> Alasan: '.$paket->getField("ALASAN").' </span>';
				  } else
				  {
					switch ($paket->getField(trim($aColumns[$i]))) {
						case '0': $row[] = '<span class="badge badge-danger"> Proses Pemilihan </span>';
							break;
						case '1': $row[] = '<span class="badge badge-warning"> Negosiasi </span>';
							break;
						case '2': $row[] = '<span class="badge badge-dark"> Penyedia Setuju </span>';
							break;
						case '3': $row[] = '<span class="badge badge-info"> Surat Pesanan </span>';
							break;
						case '4': $row[] = '<span class="badge badge-primary"> Proses </span>';
							break;
						case '5': $row[] = '<span class="badge badge-primary"> Dikirim </span>';
							break;
						case '6': $row[] = '<span class="badge badge-primary"> Diterima </span>';
							break;

						default: $row[] = '-';
							break;
					}
				    }
				} elseif($aColumns[$i]=='NILAI_OWNER_ESTIMATE')
				{
					$row[] = number_format($paket->getField(trim($aColumns[$i])),2,',','.');
				}
				else {
					$row[] = $paket->getField(trim($aColumns[$i]));
				}

			}
			$output['aaData'][] = $row;
		}

		echo json_encode( $output );
	}

	function pic_combo_json()
	{
		$this->load->model("UserLogin");
		$user_login = new UserLogin();

		$unitkerja = $this->input->get("unitkerja");

		$user_login->selectByParams(array(),-1,-1," AND UNIT_KERJA_ID = '".$unitkerja."' AND USER_AKTIF = '1' AND USER_TYPE_ID = '12' AND LEGAL != '1' AND LEVEL_KONTRAK = '1' ");

		$arr_json = array();
		$i = 0;
		while($user_login->nextRow())
		{
			$arr_json[$i]['id'] = $user_login->getField("USER_LOGIN_ID");
			$arr_json[$i]['text'] = trim($user_login->getField("USER_NAMA"));
			$i++;
		}
		echo json_encode($arr_json);
	}

	function pic_pengendali_combo_json()
	{
		$this->load->model("UserLogin");
		$user_login = new UserLogin();

		$unitkerja = $this->input->get("unitkerja");

		$user_login->selectByParams(array(),-1,-1," AND UNIT_KERJA_ID = '".$unitkerja."' AND USER_AKTIF = '1' AND USER_TYPE_ID = '12' AND LEGAL != '1' AND LEVEL_KONTRAK = '2' ");

		$arr_json = array();
		$i = 0;
		while($user_login->nextRow())
		{
			$arr_json[$i]['id'] = $user_login->getField("USER_LOGIN_ID");
			$arr_json[$i]['text'] = trim($user_login->getField("USER_NAMA"));
			$i++;
		}
		echo json_encode($arr_json);
	}

	function pic_penyelesai_combo_json()
	{
		$this->load->model("UserLogin");
		$user_login = new UserLogin();

		$unitkerja = $this->input->get("unitkerja");

		$user_login->selectByParams(array(),-1,-1," AND UNIT_KERJA_ID = '".$unitkerja."' AND USER_AKTIF = '1' AND USER_TYPE_ID = '12' AND LEGAL != '1' AND LEVEL_KONTRAK = '3' ");

		$arr_json = array();
		$i = 0;
		while($user_login->nextRow())
		{
			$arr_json[$i]['id'] = $user_login->getField("USER_LOGIN_ID");
			$arr_json[$i]['text'] = trim($user_login->getField("USER_NAMA"));
			$i++;
		}
		echo json_encode($arr_json);
	}

	function tunjuk_pic()
	{
		$this->load->model("Paket");

		$reqId =  $this->input->post('reqId'); // paket ID
		$reqPIC =  $this->input->post('reqPIC'); 

		$paket = new Paket();
		$paket->setField("PAKET_ID", $reqId);
		$paket->setField("PIC", $reqPIC);
		$paket->setField("CREATED_BY", $this->USER_LOGIN_ID);

		if($paket->updatePICKontrak()) {
			// Insert Rekam Jejak
		    $this->load->library("librekamjejak");
		    $this->librekamjejak->insertRJ('901','',$reqId,'null','901');
		    // param 1: Posisi/'1-22', param 2: Keterangan/'', param 3: Paket_id/'null', param 4: permohonan_id/'null'
		    // End Insert Rekam Jejak
			echo "Berhasil Diteruskan";
		} else {
			echo "Gagal Diteruskan";
		}
	}

	function update_po()
	{
		$this->load->model("Contracting");

		$reqId =  $this->input->post('reqId'); // ContractingRekananID 
		$reqNoPO =  $this->input->post('reqNoPO');

		$contracting = new Contracting();
		$contracting->setField("CONTRACTINGREKANANID", $reqId);
		$contracting->setField("CR_PO", $reqNoPO);
		$contracting->setField("CREATED_BY", $this->USER_LOGIN_ID);

		if($contracting->updatePO()) {
			// Insert Rekam Jejak
		    $this->load->library("librekamjejak");
		    $this->librekamjejak->insertRJ('99901','',$reqId,'null','99901',$reqId);
		    // param 1: Posisi/'1-22', param 2: Keterangan/'', param 3: Paket_id/'null', param 4: permohonan_id/'null'
		    // End Insert Rekam Jejak
			echo "PO Berhasil diubah";
		} else {
			echo "PO Gagal diubah";
		}
	}

	function tunjuk_pic_pengendali()
	{
		$this->load->model("Contracting");

		$reqId =  $this->input->post('reqId'); // paket ID
		$reqContractId =  $this->input->post('reqContractId'); // Contract ID
		
		$reqPIC =  $this->input->post('reqPIC'); 
		$reqNamaPengawasUnitKerja =  $this->input->post('reqNamaPengawasUnitKerja'); 

		$contracting = new Contracting();
		$contracting->setField("CONTRACTINGREKANANID", $reqContractId);
		$contracting->setField("PIC", $reqPIC);
		$contracting->setField("CR_NAMA_PENGAWAS_UNIT_KERJA", $reqNamaPengawasUnitKerja);
		$contracting->setField("CREATED_BY", $this->USER_LOGIN_ID);

		if($contracting->updatePICPengendaliKontrak()) {
			// Insert Rekam Jejak
		    $this->load->library("librekamjejak");
		    $this->librekamjejak->insertRJ('910','',$reqId,'null','910',$reqContractId);
		    // param 1: Posisi/'1-22', param 2: Keterangan/'', param 3: Paket_id/'null', param 4: permohonan_id/'null'
		    // End Insert Rekam Jejak
			echo "Berhasil Diteruskan";
		} else {
			echo "Gagal Diteruskan";
		}
	}

	function tunjuk_pic_penyelesai()
	{
		$this->load->model("Contracting");

		$reqId =  $this->input->post('reqId'); // paket ID
		$reqContractId =  $this->input->post('reqContractId'); // Contract ID
		
		$reqPIC =  $this->input->post('reqPIC'); 

		$contracting = new Contracting();
		$contracting->setField("CONTRACTINGREKANANID", $reqContractId);
		$contracting->setField("PIC", $reqPIC);
		$contracting->setField("CREATED_BY", $this->USER_LOGIN_ID);

		if($contracting->updatePICPenyelesaiKontrak()) {
			// Insert Rekam Jejak
		    $this->load->library("librekamjejak");
		    $this->librekamjejak->insertRJ('910','',$reqId,'null','910',$reqContractId);
		    // param 1: Posisi/'1-22', param 2: Keterangan/'', param 3: Paket_id/'null', param 4: permohonan_id/'null'
		    // End Insert Rekam Jejak
			echo "Berhasil Diteruskan";
		} else {
			echo "Gagal Diteruskan";
		}
	}

	function randomKode($length = 4) {
	    $chars = 'ABCDEFGHJKMNPQRSTUVWXYZ123456789';
	    $result = '';

	    for ($i = 0; $i < $length; $i++) {
	        $result .= $chars[random_int(0, strlen($chars) - 1)];
	    }

	    return $result;
	}

	function jsonBlacklistkontrak()
	{
		$this->load->model("Blacklistkontrak");
		$blacklist = new Blacklistkontrak();

		ini_set("memory_limit","500M");
		ini_set('max_execution_time', 520);

		$reqSearch = $this->input->post("reqSearch");

		$aColumns 			= array('BLACKLISTKONTRAK_ID','REKANAN_ID','CONTRACTING_REKANAN_ID','NO_SK','PENYEDIA','JUDUL','TANGGAL_BERLAKU','KETERANGAN','FILE');
		$aColumnsAlias		= array('BLACKLISTKONTRAK_ID','REKANAN_ID','CONTRACTING_REKANAN_ID','NO_SK','PENYEDIA','JUDUL','TANGGAL_BERLAKU','KETERANGAN','FILE');

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

			if ( trim($sOrder) == "ORDER BY BLACKLISTKONTRAK_ID desc" )
			{
				$sOrder = " ORDER BY NO_SK ASC";

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

		$statement = " AND (UPPER(A.PENYEDIA) LIKE '%".strtoupper($_GET['sSearch'])."%' OR (UPPER(A.NO_SK) LIKE '%".strtoupper($_GET['sSearch'])."%' OR (UPPER(A.JUDUL) LIKE '%".strtoupper($_GET['sSearch'])."%' OR UPPER(A.KETERANGAN) LIKE '".strtoupper($_GET['sSearch'])."%')))";
		$allRecord = $blacklist->getCountByParams(array(), $statement);
		if($_GET['sSearch'] == "")
			$allRecordFilter = $allRecord;
		else
			$allRecordFilter =  $blacklist->getCountByParams(array(), $statement);

		$blacklist->selectByParams(array(), $dsplyRange, $dsplyStart, $statement, $sOrder);

		$output = array(
			"sEcho" => intval($_GET['sEcho']),
			"iTotalRecords" => $allRecord,
			"iTotalDisplayRecords" => $allRecordFilter,
			"aaData" => array()
		);

		while($blacklist->nextRow())
		{
			$row = array();
			for ( $i=0 ; $i<count($aColumns) ; $i++ )
			{
				if($aColumns[$i]=='NO') {
					$row[] = $number;
				} elseif($aColumns[$i]=='NO_SK') { 
					$row[] = $blacklist->getField(trim($aColumns[$i]));
				} elseif($aColumns[$i]=='FILE') { 
					if ($blacklist->getField(trim('FILE'))) {
						$row[] = '<a href="uploads/kontrak/'.$blacklist->getField('FILE').'" target="_blank" class="badge badge-primary"><span class="fa fa-download"></span> Download</a>';
					} else {
						$row[] = '-';
					}

					$row[] = $blacklist->getField(trim($aColumns[$i]));
				} elseif($aColumns[$i]=='TANGGAL_BERLAKU') {
					$row[] = getFormattedDateJson(str_replace(" 00:00:00", "", $blacklist->getField(trim($aColumns[$i]))));
				} else {
					$row[] = $blacklist->getField(trim($aColumns[$i]));
				}
			}
			$output['aaData'][] = $row;
		}
		echo json_encode( $output );
	}

	function penilaianPengguna()
	{
		$this->load->model("Contracting");
		$contractingPenilaian = new Contracting();

		ini_set("memory_limit","500M");
		ini_set('max_execution_time', 520);

		$reqSearch = $this->input->post("reqSearch");

		$aColumns 			= array('CONTRACTINGREKANANID','REKANAN_ID','NAMA','CR_NILAI_KONTRAK','JNS_KONTRAK_STR','REKANAN_ID_STR','APPROVAL_UNIT');
		$aColumnsAlias		= array('CONTRACTINGREKANANID','REKANAN_ID','NAMA','CR_NILAI_KONTRAK','JNS_KONTRAK_STR','REKANAN_ID_STR','APPROVAL_UNIT');

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

			if ( trim($sOrder) == "ORDER BY CONTRACTINGREKANANID desc" )
			{
				$sOrder = " ORDER BY CONTRACTINGREKANANID ASC";

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

		$statement = " AND (UPPER(A.NAMA) LIKE '%".strtoupper($_GET['sSearch'])."%' OR (UPPER(A.REKANAN_ID_STR) LIKE '%".strtoupper($_GET['sSearch'])."%' OR UPPER(A.JNS_KONTRAK_STR) LIKE '".strtoupper($_GET['sSearch'])."%'))";

		if ($this->LEVEL_PENGGUNA == '1') { // PIC PENGGUNA
			$this->load->model("Queryfree");
		    $cekPengguna = new Queryfree();
		    $cekPengguna->selectByParams("SELECT STRING_AGG(USER_LOGIN_ID::text, ',' ORDER BY USER_LOGIN_ID) AS USER_LOGIN_ID
											FROM USER_LOGIN 
											WHERE USER_TYPE_ID = '9' AND (LEVEL_PENGGUNA != '1' OR LEVEL_PENGGUNA is null OR LEVEL_PENGGUNA != '') AND KASI_PENGGUNA = $this->USER_LOGIN_ID");
		    $cekPengguna->firstRow();
			$statement .= " AND STATUS_KONTRAK = 'Sudah dibuat' AND A.CONTRACTINGPROSESID = '3' AND PENGGUNA IN (".$cekPengguna->getField('USER_LOGIN_ID').") ";
		} else { // Staff
			$statement .= " AND STATUS_KONTRAK = 'Sudah dibuat' AND A.CONTRACTINGPROSESID = '3' AND PENGGUNA = '".$this->USER_LOGIN_ID."' ";
		}

		$allRecord = $contractingPenilaian->getCountByParamsViewContractingPenilaian(array(), $statement);
		if($_GET['sSearch'] == "")
			$allRecordFilter = $allRecord;
		else
			$allRecordFilter =  $contractingPenilaian->getCountByParamsViewContractingPenilaian(array(), $statement);

		$contractingPenilaian->selectByParamsViewContractingPenilaian(array(), $dsplyRange, $dsplyStart, $statement, $sOrder);

		$output = array(
			"sEcho" => intval($_GET['sEcho']),
			"iTotalRecords" => $allRecord,
			"iTotalDisplayRecords" => $allRecordFilter,
			"aaData" => array()
		);

		while($contractingPenilaian->nextRow())
		{
			$row = array();
			for ( $i=0 ; $i<count($aColumns) ; $i++ )
			{
				if($aColumns[$i]=='NO') {
					$row[] = $number;
				} elseif($aColumns[$i] == 'NO_SK') { 
					$row[] = $contractingPenilaian->getField(trim($aColumns[$i])); 
				} elseif($aColumns[$i] == 'CR_NILAI_KONTRAK') { 
					$row[] = number_format($contractingPenilaian->getField(trim($aColumns[$i])),0,',','.');
				} elseif($aColumns[$i] == 'APPROVAL_UNIT') { 
					$this->load->model("Queryfree");
				    $cekApprovalUnit = new Queryfree();
				    $cekApprovalUnit->selectByParams("SELECT APPROVAL_UNIT FROM PAKET_PENILAIAN_REKANAN WHERE CONTRACTINGREKANANID = ".$contractingPenilaian->getField('CONTRACTINGREKANANID')."");
		    		$cekApprovalUnit->firstRow();
					if ($cekApprovalUnit->getField('APPROVAL_UNIT') == '1') {
						$row[] = '<span class="badge badge-primary"><a class="fa fa-check"></a></span>';
					} else {
						$row[] = '<span class="badge badge-danger"><a class="fa fa-close"></a></span>';
					}
				} else {
					$row[] = $contractingPenilaian->getField(trim($aColumns[$i]));
				}
			}
			$output['aaData'][] = $row;
		}
		echo json_encode( $output );
	}

}
?>
