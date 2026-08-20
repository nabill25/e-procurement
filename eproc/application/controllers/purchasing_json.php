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
		$this->load->model("Contracting");

		$contracting = new Contracting();
		$reqSearch = $this->input->get("reqSearch");
		$getTahun = $_GET['tahun'];

		ini_set("memory_limit","500M");
		ini_set('max_execution_time', 5200);

		$aColumns = array("PAKET_ID", "NAMA", "NILAI", "PAKET_METODE_LELANG", "JENIS_KONTRAK","PENGGUNA_STR","PEMENANG");
		$aColumnsAlias = array("A.PAKET_ID", "A.NAMA", "A.NILAI", "A.PAKET_METODE_LELANG", "A.JENIS_KONTRAK", "A.PENGGUNA_STR","PEMENANG");

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

		switch ($this->USER_TYPE_ID) {
			// case '3': // Panitia
			// $statement .= "   AND A.PANITIA = '".$this->USER_LOGIN_ID."'";
			// 	break;
			case '12': // PENGELOLA KONTRAK
				if ($getTahun == 'all') {
					if ($this->LEGAL == '1') {
						$statement .= " AND A.SELESAI = '1' AND A.PAKET_METODE_LELANG_ID IN (1,2,5,6,8) AND A.STATUS_KONTRAK = 'Belum dibuat' ";
					} else {
						$statement .= " AND A.SELESAI = '1' AND A.PAKET_METODE_LELANG_ID IN (1,2,5,6,8) AND A.STATUS_KONTRAK = 'Belum dibuat' AND A.PPK = '".$this->USER_LOGIN_ID."' ";
					}
				} else {
					if ($this->LEGAL == '1') {
						$statement .= " AND A.SELESAI = '1' AND A.PAKET_METODE_LELANG_ID IN (1,2,5,6,8) AND A.STATUS_KONTRAK = 'Belum dibuat' AND A.TAHUN = '".$getTahun."'";
					} else {
						$statement .= " AND A.SELESAI = '1' AND A.PAKET_METODE_LELANG_ID IN (1,2,5,6,8) AND A.STATUS_KONTRAK = 'Belum dibuat' AND A.TAHUN = '".$getTahun."' AND A.PPK = '".$this->USER_LOGIN_ID."' ";
					}
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

				if($aColumns[$i] == "NAMA")
					$row[] = $contracting->getField($aColumns[$i]).'<br>
							 <small class="badge badge-info" style="font-size:9px; padding:3px 10px">'.$contracting->getField('PAKET_METODE_LELANG').'</small>
							 <small class="badge badge-primary" style="font-size:9px; padding:3px 10px">'.$contracting->getField('PENGGUNA_STR').'</small>
							 ';
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
		$reqPersenJaminan			= $this->input->post('reqPersenJaminan');
		$reqNilaiJaminan			= $this->input->post('reqNilaiJaminan');
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
			$paketUraian = $paket->getField("URAIAN"); 
			$paketNilai = $paket->getField("NILAI"); 
			$paketJenis = $paket->getField("PAKET_JENIS_ID"); 

			$kontrak->setField("PAKET_ID", $reqId);
			$kontrak->setField("NAMA", $paketNama);
			$kontrak->setField("URAIAN", $paketUraian);
			$kontrak->setField("NILAI", $paketNilai);
			$kontrak->setField("CONTRACTINGPROSESID", "1");
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
			$insert = $kontrak->insertContracting();
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
		$reqLingkupPekerjaan			= $_POST["reqLingkupPekerjaan"]; // $this->input->post('reqLingkupPekerjaan');
		$reqRekananId					= $this->input->post('reqRekanan'); 
		$reqJnsKontrak					= $this->input->post('reqJnsKontrak'); 
 
		 // Insert 2 table CONTRACTING_REKANAN & CONTRACTING_REKANAN_PROSES1
		$this->load->model("Paket");		
		$paket = new Paket();
		$paket->selectByParamsMonitoring(array(), -1, -1, " AND PAKET_ID = '".$reqId."' ");
		$paket->firstRow();
		$paketNama = $paket->getField("NAMA"); 
		$paketUraian = $paket->getField("URAIAN"); 
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

			$arrJson["PESAN"] = "Data berhasil diproses";
			$arrJson["FLOW"] = $reqContractingStatusKontrakId;
		}
		else {
			$arrJson["PESAN"] = "Data gagal diproses, silahkan dicoba kembali!";
			$arrJson["FLOW"] = '0';
		}

		echo json_encode($arrJson);

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
		$reqLingkupPekerjaan			= $_POST["reqLingkupPekerjaan"]; // $this->input->post('reqLingkupPekerjaan');
		$reqContractingRekananProses1Id	= $this->input->post('reqContractingRekananProses1Id');
		$reqContractingRekananId		= $this->input->post('reqContractingRekananId');

		$reqLegalNomorPKS			= $this->input->post('reqLegalNomorPKS'); 
		$reqLegalTanggal			= $this->input->post('reqLegalTanggal'); 
		$reqLegalNomorRekanan		= $this->input->post('reqLegalNomorRekanan'); 
		$reqLegalTanggalRekanan		= $this->input->post('reqLegalTanggalRekanan'); 
		 
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
		$proses1->setField("CONTRACTINGSTATUSKONTRAKID", 3); 
		$proses1->setField('CR_UPDATED_BY', $this->USER_LOGIN_ID); 

		$proses1->setField("CR_LEGAL_NOMOR_PKS", $reqLegalNomorPKS);
		$proses1->setField("CR_LEGAL_TANGGAL", dateToDBCheck($reqLegalTanggal));
		$proses1->setField("CR_LEGAL_NOMOR_REKANAN", $reqLegalNomorRekanan);
		// $proses1->setField("CR_LEGAL_TANGGAL_REKANAN", dateToDBCheck($reqLegalTanggalRekanan));
		
		$insert = $proses1->updateProses1Kontrak();
		if ($insert) { echo "Data berhasil di simpan.";
		} else { echo "Data gagal di simpan."; }
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
			$delivery->setField('CREATED_BY', $this->USER_LOGIN_ID); 
			$insert = $delivery->inserDelivery();
          	unset($delivery);
        }

		if ($insert) { echo "Data berhasil disimpan.";
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
			$delivery->setField('CREATED_BY', $this->USER_LOGIN_ID); 
			$insert = $delivery->inserDelivery();
          	unset($delivery);
        }

		if ($insert) { echo "Data berhasil disimpan.";
		} else { echo "Data gagal disimpan, silahkan coba kembali."; }
	}

	function addDeliverableEdit() // Done
	{
		$this->load->model("Contractingdeliverable");
		// echo "<pre>"; print_r($this->input->post()); die();   
		$deliverableid	= $this->input->post('deliverableid'); 
		$status	= $this->input->post('status'); 
  
		$delivery	= new Contractingdeliverable();
		$delivery->setField("DELIVERABLEID", $deliverableid);
		$delivery->setField("STATUS", $status);
		$delivery->setField('UPDATED_BY', $this->USER_LOGIN_ID); 
		$insert = $delivery->updateDelivery();
      	unset($delivery);

		if ($insert) { echo "Data berhasil disimpan.";
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

	function addPayment() // Done
	{
		$this->load->model("Contractingpayment");
		// echo "<pre>"; print_r($this->input->post()); die();   
		$contractingrekananid	= $this->input->post('contractingrekananid'); 
		$payterminke			= $_POST["payteminke"];
		$paynilai				= CommaToDot(dotToNo($_POST["paynilai"]));
		$payprogres				= $_POST["payprogres"]; 
		$payketerangan			= $_POST["payketerangan"]; 
		
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
			$payment->setField('CREATED_BY', $this->USER_LOGIN_ID); 
			$insert = $payment->insertPayment();
          	unset($payment);
        }

		if ($insert) { echo "Data berhasil disimpan.";
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

		if ($insert) { echo "Data berhasil disimpan.";
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
		$reqTanggal			= date('Y-m-d H:i:s');
		
		$reqLampiran		= $_FILES['reqLampiran'];
		$reqLampiranTemp 	= $this->input->post("reqLampiranTemp");
		
		$FILE_DIR = "uploads/payment/";
		 
		$payment->setField("PAYMENTID", $paymentid);
		$payment->setField("STATUS", $status);  
		
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
		
		$payment->setField('UPDATED_BY', $this->USER_LOGIN_ID); 
		$insert = $payment->updatePayment();
		
		if ($insert) { echo "Data berhasil disimpan.";
		} else { echo "Data gagal disimpan, silahkan coba kembali."; }
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
		
		if ($insert) { echo "Data berhasil disimpan.";
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
		$this->load->model("Contractingsanksi");
		// echo "<pre>"; print_r($this->input->post()); die();   
		$contractingrekananid	= $this->input->post('contractingrekananid'); 
		$hariterlambat			= $_POST["hariterlambat"];
		$nilaisanksi			= $_POST["nilaisanksi"];
		$nilaipekerjaan			= CommaToDot(dotToNo($_POST["nilaipekerjaan"]));
		$nilaidenda				= CommaToDot(dotToNo($_POST["nilaidenda"])); 

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

	function editanksi2() // Done
	{
		$this->load->model("Contractingsanksi");
		// echo "<pre>"; print_r($this->input->post()); die();   
		$sanksiid	= $this->input->post('sanksiid'); 
		$hariterlambat			= $this->input->post('hariterlambat');
		$nilaisanksi			= $this->input->post('nilaisanksi');
		$nilaipekerjaan			= CommaToDot(dotToNo($this->input->post('nilaipekerjaan')));
		$nilaidenda				= CommaToDot(dotToNo($this->input->post('nilaidenda'))); 
 
		$sanksi	= new Contractingsanksi();
		$sanksi->setField("SANKSIID", $sanksiid);
		$sanksi->setField("HARI_TERLAMBAT", $hariterlambat);
		$sanksi->setField("NILAI_SANKSI", $nilaisanksi);
		$sanksi->setField("NILAI_PEKERJAAN", $nilaipekerjaan);
		$sanksi->setField("NILAI_DENDA", $nilaidenda);
		$sanksi->setField('CREATED_BY', $this->USER_LOGIN_ID); 
		$insert = $sanksi->editSanksi2();
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
		$proses4->setField("CR_PERUBAHAN", "1");   
		$proses4->setField("CR_PERUBAHAN_ALASAN", $reqPerubahanAlasan);   
		$proses4->setField('CR_PERUBAHAN_UPDATED_BY', $this->USER_LOGIN_ID); 

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
		$proses4->setField("CR_PEMUTUSAN", "1");   
		$proses4->setField("CR_PEMUTUSAN_ALASAN", $reqPerubahanAlasan);   
		$proses4->setField('CR_PEMUTUSAN_UPDATED_BY', $this->USER_LOGIN_ID); 

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
		$proses4->setField("CR_DENDA", "1");   
		$proses4->setField("CR_DENDA_ALASAN", $reqPerubahanAlasan);   
		$proses4->setField('CR_DENDA_UPDATED_BY', $this->USER_LOGIN_ID); 

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
		
		if ($insert) { echo "Data berhasil disimpan."; 
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
		
		if ($insert) { echo "Data berhasil disimpan."; 
		} else { echo "Data gagal disimpan, silahkan coba kembali."; }
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

}
?>
