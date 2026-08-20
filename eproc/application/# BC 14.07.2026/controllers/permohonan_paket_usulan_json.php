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

class permohonan_paket_usulan_json extends CI_Controller {

	function __construct() {
		parent::__construct(); $this->db->query("SET TIME ZONE 'Asia/Jakarta'");

		//kauth
		if (!$this->kauth->getInstance()->hasIdentity())
		{
			// trow to unauthenticated page!
			//redirect('Login');
		}

		/* GLOBAL VARIABLE */
		$this->ID = isset($this->kauth->getInstance()->getIdentity()->REKANAN_ID) ? $this->kauth->getInstance()->getIdentity()->REKANAN_ID : '';

		$this->USER_LOGIN_ID =  isset($this->kauth->getInstance()->getIdentity()->USER_LOGIN_ID) ? $this->kauth->getInstance()->getIdentity()->USER_LOGIN_ID : '';
		$this->USER_LOGIN =  isset($this->kauth->getInstance()->getIdentity()->USER_LOGIN) ? $this->kauth->getInstance()->getIdentity()->USER_LOGIN : '';
		$this->USER_NAMA =  isset($this->kauth->getInstance()->getIdentity()->USER_NAMA) ? $this->kauth->getInstance()->getIdentity()->USER_NAMA : '';
		$this->USER_TYPE_ID =  isset($this->kauth->getInstance()->getIdentity()->USER_TYPE_ID) ? $this->kauth->getInstance()->getIdentity()->USER_TYPE_ID : '';
		$this->REKANAN_ID =  isset($this->kauth->getInstance()->getIdentity()->REKANAN_ID) ? $this->kauth->getInstance()->getIdentity()->REKANAN_ID : '';
		$this->UNIT_KERJA_ID =  isset($this->kauth->getInstance()->getIdentity()->UNIT_KERJA_ID) ? $this->kauth->getInstance()->getIdentity()->UNIT_KERJA_ID : '';
		$this->NIP =  isset($this->kauth->getInstance()->getIdentity()->NIP) ? $this->kauth->getInstance()->getIdentity()->NIP : '';
		$this->APPROVAL_UNIT =  isset($this->kauth->getInstance()->getIdentity()->APPROVAL_UNIT) ? $this->kauth->getInstance()->getIdentity()->APPROVAL_UNIT : '';
		$this->LOGIN_TIME = isset($this->kauth->getInstance()->getIdentity()->LOGIN_TIME) ? $this->kauth->getInstance()->getIdentity()->LOGIN_TIME : '';
		$this->LOGIN_DATE = isset($this->kauth->getInstance()->getIdentity()->LOGIN_DATE) ? $this->kauth->getInstance()->getIdentity()->LOGIN_DATE : '';
		$this->REKANAN = isset($this->kauth->getInstance()->getIdentity()->NAMA) ? $this->kauth->getInstance()->getIdentity()->NAMA : '';
		$this->REKANAN_KODE = isset($this->kauth->getInstance()->getIdentity()->KODE) ? $this->kauth->getInstance()->getIdentity()->KODE : '';
		$this->REKANAN_PKP = isset($this->kauth->getInstance()->getIdentity()->PKP) ? $this->kauth->getInstance()->getIdentity()->PKP : '';
		$this->REKANAN_NPWP = isset($this->kauth->getInstance()->getIdentity()->NPWP) ? $this->kauth->getInstance()->getIdentity()->NPWP : '';
		$this->REKANAN_STATUS_PERUSAHAAN = isset($this->kauth->getInstance()->getIdentity()->STATUS_PERUSAHAAN) ? $this->kauth->getInstance()->getIdentity()->STATUS_PERUSAHAAN : '';
		$this->REKANAN_STATUS_VALIDASI = isset($this->kauth->getInstance()->getIdentity()->STATUS_VALIDASI) ? $this->kauth->getInstance()->getIdentity()->STATUS_VALIDASI : '';
		$this->DEPARTMENT = isset($this->kauth->getInstance()->getIdentity()->DEPARTMENT) ? $this->kauth->getInstance()->getIdentity()->DEPARTMENT : '';
	}

	function permohonan_usulan_add()
	{
		$this->load->model(array("PermohonanPaket","Importsirup","PermohonanPaketAnalisaFile"));
		$this->load->library("FileHandler");
		$this->load->library("kauth");  $userLogin = new kauth();
		$file = new FileHandler();

		$sirup = new Importsirup();
		$permohonan_paket_analisa = new PermohonanPaket();

		$sirupId = $this->input->post("sirupId");
		$reqId = $this->input->post("reqId");
		$reqMode = $this->input->post("reqMode");
		$reqJudul = $this->input->post("reqJudul");
		$reqKodePR = $this->input->post("reqKodePR");

		$reqToday = date('Y-m-d H:i:s'); 

		$sirup->selectByParams(array("ID" => $sirupId));
	  	$sirup->firstRow();

		switch ($sirup->getField("NAMA_JENIS_PEKERJAAN")) {
			case 'Jasa Konsultansi': $reqJenisBarangJasa = '2'; break;
			case 'Barang': $reqJenisBarangJasa = '3'; break;
			case 'Pekerjaan Konstruksi': $reqJenisBarangJasa = '1'; break;
			case 'Jasa Lainnya': $reqJenisBarangJasa = '4'; break;
			default: $reqJenisBarangJasa = '0'; break;
		}

		$permohonan_paket_analisa->setField('TAHUN_ANGGARAN', $sirup->getField("TAHUN"));
		$permohonan_paket_analisa->setField('SUMBER_DANA_KETERANGAN', $sirup->getField("SUMBER_DANA"));
		$permohonan_paket_analisa->setField('PERMOHONAN_PAKET_ANALISA_KATEGORI_ID', '1'); // Produk dalam negeri
		$permohonan_paket_analisa->setField('CREATED_BY', $this->USER_LOGIN_ID);
		$permohonan_paket_analisa->setField('CREATED_DATE', $reqToday);

		// echo CommaToNo($reqPerkiraanBiayaHarga);
		if($reqMode == 'insert')
		{
			if($permohonan_paket_analisa->insertAnalisa())
			{
				$analisaId = $permohonan_paket_analisa->id;
				// ikn 20251124
				$permohonan_paket_insert = new PermohonanPaket();
				$permohonan_paket_insert->setField("PERMOHONAN_PAKET_ANALISA_ID", $analisaId);
				$permohonan_paket_insert->setField("NAMA", $sirup->getField("NAMA_PAKET"));
				$permohonan_paket_insert->setField("TAHUN_ANGGARAN", $sirup->getField("TAHUN"));
				$permohonan_paket_insert->setField("JENIS_BARANG_JASA", $reqJenisBarangJasa);
				// $permohonan_paket_insert->setField('JUMLAH', dotToNo($reqNilai));
				$permohonan_paket_insert->setField("NILAI", CommaToNo($sirup->getField("NILAI_PAGU")));
				$permohonan_paket_insert->setField("PERKIRAAN_BIAYA_HARGA", CommaToNo($sirup->getField("NILAI_PAGU")));
				$permohonan_paket_insert->setField('WAKTU_PENGGUNA_BARANGJASA', $sirup->getField("WAKTU_AWAL"));
				$permohonan_paket_insert->setField('RENCANA_PENGADAAN', $sirup->getField("WAKTU_AKHIR"));
				$permohonan_paket_insert->setField('KODE_RUP', $sirup->getField("KODE_RUP"));
				$permohonan_paket_insert->setField('KODE_PR', $sirup->getField("KODE_PR"));
				$permohonan_paket_insert->setField('SIRUP_ID', $sirupId);
				// $permohonan_paket_insert->setField("CARA_PENGADAAN", $reqCaraPengadaan);
				$permohonan_paket_insert->setField('USER_LOGIN_ID', $this->USER_LOGIN_ID);
				$permohonan_paket_insert->setField('UNIT_KERJA_ID', $this->UNIT_KERJA_ID);
				$permohonan_paket_insert->setField("LAST_CREATE_USER", $this->USER_NAMA);
				$permohonan_paket_insert->setField("LAST_CREATE_DATE", $reqToday);
				$permohonan_paket_insert->setField("NILAI_RAB_PR", $sirup->getField("NILAI_PAGU_PR"));

				$permohonan_paket_insert->insertPermohonan();
				$permohonanId = $permohonan_paket_insert->id;

				// Insert Rekam Jejak
				$this->load->library("librekamjejak");
				$this->librekamjejak->insertRJ('210','','null',$permohonanId,'210'); // param 1: Posisi/'null', param 2: Keterangan/'null', param 3: Paket_id/'null', param 4: permohonan_id/'null'
				// End Insert Rekam Jejak
				echo "Data berhasil disimpan.";
			} else {
				echo "Data gagal disimpan.";
			}

		}
		else
		{
			$permohonan_paket_analisa->setField("PERMOHONAN_PAKET_ANALISA_ID", $usulanId);
			$permohonan_paket_analisa->setField('UPDATED_BY', $this->USER_LOGIN_ID);
			$permohonan_paket_analisa->setField('UPDATED_DATE', $reqToday);
			if($permohonan_paket_analisa->updateAnalisa())
			{
				$permohonan_paket_update = new PermohonanPaket();
				$permohonan_paket_update->setField("PERMOHONAN_PAKET_ANALISA_ID", $usulanId);
				$permohonan_paket_update->setField("NAMA", $reqNamaPaket);
				$permohonan_paket_update->setField("TAHUN_ANGGARAN", $reqTahunAnggaran);
				$permohonan_paket_update->setField("ANGGARAN", $reqAnggaran);
				$permohonan_paket_update->setField("JENIS_BARANG_JASA", $reqJenisBarangJasa);
				$permohonan_paket_update->setField("NILAI", CommaToNo($reqPerkiraanBiayaHarga));
				$permohonan_paket_update->setField("PERKIRAAN_BIAYA_HARGA", CommaToNo($reqPerkiraanBiayaHarga));
				$permohonan_paket_update->setField('WAKTU_PENGGUNA_BARANGJASA', dateToDBCheck($reqWaktuPenggunaBarangjasa));
				$permohonan_paket_update->setField('RENCANA_PENGADAAN', dateToDBCheck($reqRencanaPengadaan));
				$permohonan_paket_update->setField("CARA_PENGADAAN", $reqCaraPengadaan);
				$permohonan_paket_update->setField("LAST_CREATE_USER", $this->USER_NAMA);
				$permohonan_paket_update->setField("LAST_CREATE_DATE", $reqToday);

				$permohonan_paket_update->updatePermohonan();

				// Insert Anggaran
				// 20-11-2023 ikn
				$permohonan_paket_anggaran_select = new PermohonanPaket();
				$permohonan_paket_anggaran_select->selectByParamsAnggaran2(array("A.PERMOHONAN_PAKET_ID" => $reqPermohonanPaketId));
				// echo $permohonan_paket_anggaran_select->countRow().'----'; die;
				if ($permohonan_paket_anggaran_select->countRow() > 0) { // update
					$reqPermohonanPaketId = $this->input->post("reqPermohonanPaketId");
					$permohonan_paket_anggaran_insert = new PermohonanPaket();
					$permohonan_paket_anggaran_insert->setField("PERMOHONAN_PAKET_ID", $reqPermohonanPaketId);
					$permohonan_paket_anggaran_insert->setField("INTEGRATION_IMPORT_RKA_BUDGET_ID", $reqIntegrationImportRkaBudgetId);
					$permohonan_paket_anggaran_insert->setField("DEPARTMENT", $reqDepartment);
					$permohonan_paket_anggaran_insert->setField("SEGMENT2_DESC", $reqDepartment);
					$permohonan_paket_anggaran_insert->setField("SEGMENT3_DESC", $reqMataAnggaran);
					$permohonan_paket_anggaran_insert->setField("SEGMENT4_DESC", $reqKegiatan);
					$permohonan_paket_anggaran_insert->setField("SEGMENT5_DESC", $reqSumberDana);
					$permohonan_paket_anggaran_insert->setField("BUDGET_AMT", $reqBudgetAmt);
					$permohonan_paket_anggaran_insert->setField("REMAIN_AMT", $reqRemainAmt);
					$permohonan_paket_anggaran_insert->setField('CREATED_BY', $this->USER_LOGIN_ID);
					$permohonan_paket_anggaran_insert->updatePermohonanAnggaran2();
				} else { // Insert
					$reqPermohonanPaketId = $this->input->post("reqPermohonanPaketId");
					$permohonan_paket_anggaran_insert = new PermohonanPaket();
					$permohonan_paket_anggaran_insert->setField("PERMOHONAN_PAKET_ID", $reqPermohonanPaketId);
					$permohonan_paket_anggaran_insert->setField("INTEGRATION_IMPORT_RKA_BUDGET_ID", $reqIntegrationImportRkaBudgetId);
					$permohonan_paket_anggaran_insert->setField("DEPARTMENT", $reqDepartment);
					$permohonan_paket_anggaran_insert->setField("SEGMENT2_DESC", $reqDepartment);
					$permohonan_paket_anggaran_insert->setField("SEGMENT3_DESC", $reqMataAnggaran);
					$permohonan_paket_anggaran_insert->setField("SEGMENT4_DESC", $reqKegiatan);
					$permohonan_paket_anggaran_insert->setField("SEGMENT5_DESC", $reqSumberDana);
					$permohonan_paket_anggaran_insert->setField("BUDGET_AMT", $reqBudgetAmt);
					$permohonan_paket_anggaran_insert->setField("REMAIN_AMT", $reqRemainAmt);
					$permohonan_paket_anggaran_insert->setField('CREATED_BY', $this->USER_LOGIN_ID);
					$permohonan_paket_anggaran_insert->insertPermohonanAnggaran2();
				}

				// END Insert Anggaran
			}

			$permohonan_paket_file_delete = new PermohonanPaketAnalisaFile();
			$permohonan_paket_file_delete->setField("PERMOHONAN_PAKET_ANALISA_ID", $usulanId);
			$permohonan_paket_file_delete->deletePermohonan();

			for($i=0; $i<count($reqJudul);$i++)
			{
				if($reqJudul[$i] == "")
				{}
				else
				{
					$permohonan_paket_analisa_file = new PermohonanPaketAnalisaFile();
					$permohonan_paket_analisa_file->setField("PERMOHONAN_PAKET_ANALISA_ID", $usulanId);
					$renameFile = md5($this->randomKode().date("dmYHis").$reqLinkFile['name'][$i].$usulanId).".".getExtension($reqLinkFile['name'][$i]);
					if($file->uploadToDirArray('reqLinkFile', $FILE_DIR, $renameFile, $i))
					{
						$insertLinkFilesSize = $file->uploadedSize;
						$insertLinkFilesExe =  $file->uploadedExtension;
						$insertLinkFile =  $renameFile;
					}
					else
					{
						$insertLinkFile =  $reqLinkFileTemp[$i];
						$insertLinkFilesExe =  $reqLinkFileTempTipe[$i];
						$insertLinkFilesSize = $reqLinkFileTempUkuran[$i];
					}
					$permohonan_paket_analisa_file->setField("PATH_FILE", $insertLinkFile);
					$permohonan_paket_analisa_file->setField("TIPE", $insertLinkFilesExe);
					$permohonan_paket_analisa_file->setField("UKURAN", ValToNullDB($insertLinkFilesSize));
					$permohonan_paket_analisa_file->setField("JUDUL", $reqJudul[$i]);
					$permohonan_paket_analisa_file->setField('CREATED_BY', $this->USER_LOGIN_ID);
					$permohonan_paket_analisa_file->setField('CREATED_DATE', $reqToday);
					$permohonan_paket_analisa_file->insert();
				}
				unset($permohonan_paket_analisa_file);
			}
			echo "Data berhasil diupdate.";
		}
			// echo "Data berhasil disimpan.-".$usulanId."-".$reqMetodePengadaan;

	}

	function permohonan_usulan_add_file()
	{
		$this->load->model(array("PermohonanPaket","Importsirup","PermohonanPaketAnalisaFile"));
		$this->load->library("FileHandler");
		$this->load->library("kauth");  $userLogin = new kauth();
		$file = new FileHandler();

		$sirup = new Importsirup();
		$permohonan_paket_analisa = new PermohonanPaket();
		$permohonan_paket = new PermohonanPaket();

		$sirupId = $this->input->post("sirupId");
		$reqId = $this->input->post("reqId"); // Analisa ID
		$reqPerId = $this->input->post("reqPerId"); // Analisa ID
		$reqMode = $this->input->post("reqMode");
		$reqJudul = $this->input->post("reqJudul");

		$reqNama = $this->input->post("reqNama");
		$reqTanggalWaktuPelaksanaan = $this->input->post("reqTanggalWaktuPelaksanaan");
		$reqLokasiPekerjaan = $this->input->post("reqLokasiPekerjaan");
		$reqNilaiRABPR = $this->input->post("reqNilaiRABPR");
		$reqJenisKontrak = $this->input->post("reqJenisKontrak");
		$reqPengadaanBypass = $this->input->post("reqPengadaanBypass");
		$reqKodeSirupLKPP = $this->input->post("reqKodeSirupLKPP");

		$reqToday = date('Y-m-d H:i:s');

		$reqLinkFile			= $_FILES['reqLinkFile'];
		$reqLinkFileTemp 		= $_POST["reqLinkFileTemp"];
		$reqLinkFileTempTipe 	= $_POST["reqLinkFileTempTipe"];
		$reqLinkFileTempUkuran 	= $_POST["reqLinkFileTempUkuran"];
		$FILE_DIR = "uploads/permohonan_paket/";

		$sirup->selectByParams(array("ID" => $sirupId));
	  	$sirup->firstRow(); 

		$permohonan_paket_analisa->setField('CREATED_BY', $this->USER_LOGIN_ID);
		$permohonan_paket_analisa->setField('CREATED_DATE', $reqToday);

		// Update Paket Permohonan 
		$permohonan_paket->setField("PERMOHONAN_PAKET_ANALISA_ID", $reqId);
		$permohonan_paket->setField('NAMA', $reqNama);
		// $permohonan_paket->setField('NILAI_RAB_PR', dotToNo($reqNilaiRABPR));
		$permohonan_paket->setField('TANGGAL_WAKTU_PELAKSANAAN', $reqTanggalWaktuPelaksanaan);
		$permohonan_paket->setField('LOKASI_PEKERJAAN', $reqLokasiPekerjaan);
		$permohonan_paket->setField('JENIS_KONTRAK', $reqJenisKontrak);
		// $permohonan_paket->setField('KODE_SIRUP_LKPP', $reqKodeSirupLKPP);
		$permohonan_paket->setField('PENGADAAN_BYPASS', $reqPengadaanBypass);
		$permohonan_paket->setField('UPDATED_BY', $this->USER_LOGIN_ID);
		$permohonan_paket->updatePermohonanPerencana();

		// echo CommaToNo($reqPerkiraanBiayaHarga);
		if($reqMode == 'insert')
		{ 
			echo "Data berhasil disimpan.";
		}
		else
		{ 
		    
			echo "Data berhasil diupdate.";
		}
			// echo "Data berhasil disimpan.-".$usulanId."-".$reqMetodePengadaan;

	}

	function files() 
	{
		$this->load->model("PermohonanPaketAnalisaFile");
		$permohonananalisafile = new PermohonanPaketAnalisaFile();
		
		$reqKeterangan = $this->input->post("reqKeterangan");
		$reqId = $this->input->get("reqId"); // Analisa File ID
		$reqSearch = $this->input->post("reqSearch");
		$reqAgamaId = $this->input->post("reqAgamaId");
		
		$aColumns 			= array('PERMOHONAN_PAKET_ANALISA_FILE_ID','JUDUL','PATH_FILE','FILE_TTE','FILE_SHARE','ESIGN_PATH_FILE');
		$aColumnsAlias		= array('PERMOHONAN_PAKET_ANALISA_FILE_ID','JUDUL','PATH_FILE','FILE_TTE','FILE_SHARE','ESIGN_PATH_FILE');
		
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
			
			if ( trim($sOrder) == "ORDER BY PERMOHONAN_PAKET_ANALISA_FILE_ID desc" )
			{
				$sOrder = " ORDER BY PERMOHONAN_PAKET_ANALISA_FILE_ID DESC ";
				 
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
		
		$statement = " AND PERMOHONAN_PAKET_ANALISA_ID = ".$reqId." AND (UPPER(JUDUL) LIKE '%".strtoupper($_GET['sSearch'])."%')";
		$allRecord = $permohonananalisafile->getCountByParams(array(), $statement, $sOrder);
		if($_GET['sSearch'] == "")
			$allRecordFilter = $allRecord;
		else	
			$allRecordFilter =  $permohonananalisafile->getCountByParams(array(), $statement, $sOrder);

		$permohonananalisafile->selectByParams(array(), $dsplyRange, $dsplyStart, $statement, $sOrder);
		
		$output = array(
			"sEcho" => intval($_GET['sEcho']),
			"iTotalRecords" => $allRecord,
			"iTotalDisplayRecords" => $allRecordFilter,
			"aaData" => array()
		);
		
		while($permohonananalisafile->nextRow())		
		{		
			$row = array();		
			for ( $i=0 ; $i<count($aColumns) ; $i++ )		
			{	
				if($aColumns[$i]=='NO') {
					$row[] = $number;
				} elseif($aColumns[$i]=='FILE_TTE' || $aColumns[$i]=='FILE_SHARE') { 
					if ($permohonananalisafile->getField(trim($aColumns[$i])) == '1') {
						$row[] = '<span class="badge badge-primary">Ya</span>';
					} else {
						$row[] = '-';
					}
				} elseif($aColumns[$i]=='PATH_FILE') { 
					$row[] = '<a href="uploads/permohonan_paket/'.$permohonananalisafile->getField(trim($aColumns[$i])).'" target="_blank"><span class="badge badge-primary"><i class="fa fa-download"> Download</i></span></a>';
				} elseif($aColumns[$i]=='JUDUL') { 
					$row[] = $permohonananalisafile->getField(trim($aColumns[$i]));
				} else {	
					$row[] = $permohonananalisafile->getField(trim($aColumns[$i]));
				}
			}
			$output['aaData'][] = $row;
		}
		echo json_encode( $output );	
	}

	function deleteFileAnalisa() 
	{
		$this->load->model('PermohonanPaketAnalisaFile');
		$reqId		= $this->input->get('reqId');
		$permohonananalisafile	= new PermohonanPaketAnalisaFile();
		$permohonananalisafile->setField("PERMOHONAN_PAKET_ANALISA_FILE_ID", $reqId);
		$permohonananalisafile->deleteByID();
		
		echo "Data berhasil disimpan.";
	}

	function addfiles()
	{
		$this->load->model(array("PermohonanPaket","Importsirup","PermohonanPaketAnalisaFile"));
		$this->load->library("FileHandler");
		$this->load->library("kauth");  $userLogin = new kauth();
		$file = new FileHandler();

		$reqId = $this->input->post("reqId"); // Analisa ID
		$reqFileId = $this->input->post("reqFileId"); // Analisa File ID
		$reqMode = $this->input->post("reqMode"); 

		$reqNama = $this->input->post("reqNama");
		$reqFileTTE = $this->input->post("reqFileTTE");
		$reqFileShare = $this->input->post("reqFileShare");

		$reqToday = date('Y-m-d H:i:s');

		$reqLinkFile			= $_FILES['reqLinkFile'];
		$reqLinkFileTemp 		= $_POST["reqLinkFileTemp"];
		$reqLinkFileTempTipe 	= $_POST["reqLinkFileTempTipe"];
		$reqLinkFileTempUkuran 	= $_POST["reqLinkFileTempUkuran"];
		$FILE_DIR = "uploads/permohonan_paket/";

		$permohonan_paket_analisa_file = new PermohonanPaketAnalisaFile();
		$permohonan_paket_analisa_file->setField("PERMOHONAN_PAKET_ANALISA_ID", $reqId);
		$renameFile = md5($this->randomKode().date("dmYHis").$reqLinkFile['name'].$reqId).".".getExtension($reqLinkFile['name']);
		if($file->uploadToDir('reqLinkFile', $FILE_DIR, $renameFile))
		{
			$insertLinkFilesSize = $file->uploadedSize;
			$insertLinkFilesExe =  $file->uploadedExtension;
			$insertLinkFile =  $renameFile;
		}
		else
		{
			$insertLinkFile =  $reqLinkFileTemp;
			$insertLinkFilesExe =  $reqLinkFileTempTipe;
			$insertLinkFilesSize = $reqLinkFileTempUkuran;
		}

		// Parsing untuk eSign
		$arrayRep = array(' ','/');
		$nomor_surat = strtoupper(str_replace($arrayRep,"-",$reqNama)).'-'.$this->randomKode();
		// End Parsing untuk eSign

		$permohonan_paket_analisa_file->setField("FILE_TTE", $reqFileTTE);
		$permohonan_paket_analisa_file->setField("FILE_SHARE", $reqFileShare);
		$permohonan_paket_analisa_file->setField("PATH_FILE", $insertLinkFile);
		$permohonan_paket_analisa_file->setField("TIPE", $insertLinkFilesExe);
		$permohonan_paket_analisa_file->setField("UKURAN", ValToNullDB($insertLinkFilesSize));
		$permohonan_paket_analisa_file->setField("JUDUL", $reqNama);
		$permohonan_paket_analisa_file->setField('CREATED_BY', $this->USER_LOGIN_ID);
		$permohonan_paket_analisa_file->setField('CREATED_DATE', $reqToday);
		$permohonan_paket_analisa_file->setField('ESIGN_NOMOR_SURAT', $nomor_surat);

		if($reqMode == 'insert')
		{ 
			$permohonan_paket_analisa_file->insert();
			unset($permohonan_paket_analisa_file);
			echo "Data berhasil disimpan.";
		}
		else
		{ 
			$permohonan_paket_analisa_file->setField('PERMOHONAN_PAKET_ANALISA_FILE_ID', $reqFileId);
  			$permohonan_paket_analisa_file->update();
			unset($permohonan_paket_analisa_file);
			echo "Data berhasil diupdate.";
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

	function permohonan_usulan_add_kirim_ke_kasubdit()
	{
		$this->load->model(array("PermohonanPaket","Importsirup","PermohonanPaketAnalisaFile"));
		$permohonan_paket = new PermohonanPaket();

		$sirupId = $this->input->post("sirupId");
		$reqId = $this->input->post("reqId"); // Analisa ID
		$reqPerId = $this->input->post("reqPerId"); // Permohonan ID

		// Update Status
		$permohonan_paket->setField('UPDATED_BY', $this->USER_LOGIN_ID);
		$permohonan_paket->setField('PERMOHONAN_PAKET_ANALISA_ID', $reqId);
		$permohonan_paket->setField('APPROVAL', '5');
		$permohonan_paket->updateApprovalAnalisa();

		// Insert Rekam Jejak
	    $this->load->library("librekamjejak");
	    $this->librekamjejak->insertRJ('1012','','null',$reqPerId,'1012'); // param 1: Posisi/'null', param 2: Keterangan/'', param 3: Paket_id/'null', param 4: permohonan_id/'null'

		echo "Data berhasil diteruskan";
	}

	function kembali_permohonan_ke_staff()
	{
		$this->load->model(array("PermohonanPaket","Importsirup","PermohonanPaketAnalisaFile"));
		$permohonan_paket = new PermohonanPaket();

		$reqId = $this->input->post("reqId"); // Analisa ID
		$reqAlasan =  $this->input->post('reqAlasan');
		$reqPerId = $this->input->post("reqPerId"); // Permohonan ID

		// Update Status
		$permohonan_paket->setField('UPDATED_BY', $this->USER_LOGIN_ID);
		$permohonan_paket->setField('PERMOHONAN_PAKET_ANALISA_ID', $reqId);
		$permohonan_paket->setField('APPROVAL', '4');
		$permohonan_paket->setField('NOTE_KASUBDIT', $reqAlasan);
		$permohonan_paket->updateApprovalAnalisaWithNote();

		// Insert Rekam Jejak
	    $this->load->library("librekamjejak");
	    $this->librekamjejak->insertRJ('1013',$reqAlasan,'null',$reqPerId,'1013'); // param 1: Posisi/'null', param 2: Keterangan/'', param 3: Paket_id/'null', param 4: permohonan_id/'null'

		echo "Data berhasil diteruskan";

	}

	function permohonan_usulan_add_file_kirim()
	{
		$this->load->model(array("PermohonanPaketAnalisaFile"));
		$this->load->library(array("kauth","libapiui"));  
		$userLogin = new kauth();
		$libapiui = new libapiui();
      
		$sirupId = $this->input->post("sirupId");
		$reqId = $this->input->post("reqId"); // Analisa ID
		$reqToday = date('Y-m-d H:i:s');

		$FILE_DIR = "uploads/permohonan_paket/";

      	$permohonan_paket_file = new PermohonanPaketAnalisaFile();
      	$permohonan_paket_file->selectByParams(array("PERMOHONAN_PAKET_ANALISA_ID" => $reqId, "FILE_TTE" => "1"));

      	$no=1;
	  	while($permohonan_paket_file->nextRow())
        {
      		$kirimFile = $libapiui->postEsignPengajuan($permohonan_paket_file->getField("PATH_FILE"),$permohonan_paket_file->getField("ESIGN_NOMOR_SURAT"));
 			
 			if ($kirimFile->code == '400') { 
 				// Update Status
      			$permohonan_paket_fileU = new PermohonanPaketAnalisaFile();
				$permohonan_paket_fileU->setField('PERMOHONAN_PAKET_ANALISA_FILE_ID', $permohonan_paket_file->getField("PERMOHONAN_PAKET_ANALISA_FILE_ID"));
				$permohonan_paket_fileU->setField('ESIGN_STATUS', $kirimFile->message);
				$permohonan_paket_fileU->setField('UPDATED_BY', $this->USER_LOGIN_ID);
				$permohonan_paket_fileU->updateEsign400();
				if ($no==1) {
	 				echo $kirimFile->message.' '.$permohonan_paket_file->getField("ESIGN_NOMOR_SURAT").'<br>';
				}
 			}

 			if ($kirimFile->code == '200') {  // Berhasil kirim update table 
 				// Update Status
      			$permohonan_paket_fileU = new PermohonanPaketAnalisaFile();
				$permohonan_paket_fileU->setField('PERMOHONAN_PAKET_ANALISA_FILE_ID', $permohonan_paket_file->getField("PERMOHONAN_PAKET_ANALISA_FILE_ID"));
				$permohonan_paket_fileU->setField('ESIGN_ID', $kirimFile->data->id);
				$permohonan_paket_fileU->setField('ESIGN_PATH_FILE', $kirimFile->data->file_dokumen);
				$permohonan_paket_fileU->setField('ESIGN_STATUS', $kirimFile->message);
				$permohonan_paket_fileU->setField('UPDATED_BY', $this->USER_LOGIN_ID);
				$permohonan_paket_fileU->updateEsign200();
				if ($no==1) {
 					echo $kirimFile->message;
 				}
 			}
 			$no++;
        }
	}


	// function permohonan_usulan_add()
	// {
	// 	$this->load->model(array("PermohonanPaket","Importsirup","PermohonanPaketAnalisaFile"));
	// 	$this->load->library("FileHandler");
	// 	$this->load->library("kauth");  $userLogin = new kauth();
	// 	$file = new FileHandler();

	// 	$sirup = new Importsirup();
	// 	$permohonan_paket_analisa = new PermohonanPaket();

	// 	$sirupId = $this->input->post("sirupId");
	// 	$reqId = $this->input->post("reqId");
	// 	$reqMode = $this->input->post("reqMode");
	// 	$reqJudul = $this->input->post("reqJudul");
	// 	$reqKodePR = $this->input->post("reqKodePR");

	// 	$reqToday = date('Y-m-d H:i:s');

	// 	$reqLinkFile			= $_FILES['reqLinkFile'];
	// 	$reqLinkFileTemp 		= $_POST["reqLinkFileTemp"];
	// 	$reqLinkFileTempTipe 	= $_POST["reqLinkFileTempTipe"];
	// 	$reqLinkFileTempUkuran 	= $_POST["reqLinkFileTempUkuran"];
	// 	$FILE_DIR = "uploads/permohonan_paket/";

	// 	$sirup->selectByParams(array("ID" => $sirupId));
	//   $sirup->firstRow();

	// 	switch ($sirup->getField("NAMA_JENIS_PEKERJAAN")) {
	// 		case 'Jasa Konsultansi': $reqJenisBarangJasa = '2'; break;
	// 		case 'Barang': $reqJenisBarangJasa = '3'; break;
	// 		case 'Pekerjaan Konstruksi': $reqJenisBarangJasa = '1'; break;
	// 		case 'Jasa Lainnya': $reqJenisBarangJasa = '4'; break;
	// 		default: $reqJenisBarangJasa = '0'; break;
	// 	}

	// 	$permohonan_paket_analisa->setField('TAHUN_ANGGARAN', $sirup->getField("TAHUN"));
	// 	$permohonan_paket_analisa->setField('PERMOHONAN_PAKET_ANALISA_KATEGORI_ID', '1'); // Produk dalam negeri
	// 	$permohonan_paket_analisa->setField('CREATED_BY', $this->USER_LOGIN_ID);
	// 	$permohonan_paket_analisa->setField('CREATED_DATE', $reqToday);

	// 	// echo CommaToNo($reqPerkiraanBiayaHarga);
	// 	if($reqMode == 'insert')
	// 	{
	// 		if($permohonan_paket_analisa->insertAnalisa())
	// 		{
	// 			$analisaId = $permohonan_paket_analisa->id;
	// 			// ikn 20251124
	// 			$permohonan_paket_insert = new PermohonanPaket();
	// 			$permohonan_paket_insert->setField("PERMOHONAN_PAKET_ANALISA_ID", $analisaId);
	// 			$permohonan_paket_insert->setField("NAMA", $sirup->getField("NAMA_PAKET"));
	// 			$permohonan_paket_insert->setField("TAHUN_ANGGARAN", $sirup->getField("TAHUN"));
	// 			$permohonan_paket_insert->setField("JENIS_BARANG_JASA", $reqJenisBarangJasa);
	// 			// $permohonan_paket_insert->setField('JUMLAH', dotToNo($reqNilai));
	// 			$permohonan_paket_insert->setField("NILAI", CommaToNo($sirup->getField("NILAI_PAGU")));
	// 			$permohonan_paket_insert->setField("PERKIRAAN_BIAYA_HARGA", CommaToNo($sirup->getField("NILAI_PAGU")));
	// 			$permohonan_paket_insert->setField('WAKTU_PENGGUNA_BARANGJASA', $sirup->getField("WAKTU_AWAL"));
	// 			$permohonan_paket_insert->setField('RENCANA_PENGADAAN', $sirup->getField("WAKTU_AKHIR"));
	// 			$permohonan_paket_insert->setField('KODE_RUP', $sirup->getField("KODE_RUP"));
	// 			$permohonan_paket_insert->setField('KODE_PR', $reqKodePR);
	// 			$permohonan_paket_insert->setField('SIRUP_ID', $sirupId);
	// 			// $permohonan_paket_insert->setField("CARA_PENGADAAN", $reqCaraPengadaan);
	// 			$permohonan_paket_insert->setField('USER_LOGIN_ID', $this->USER_LOGIN_ID);
	// 			$permohonan_paket_insert->setField('UNIT_KERJA_ID', $this->UNIT_KERJA_ID);
	// 			$permohonan_paket_insert->setField("LAST_CREATE_USER", $this->USER_NAMA);
	// 			$permohonan_paket_insert->setField("LAST_CREATE_DATE", $reqToday);

	// 			$permohonan_paket_insert->insertPermohonan();
	// 			$permohonanId = $permohonan_paket_insert->id;

	// 			// echo "Data berhasil disimpan.";
	// 			// Insert Rekam Jejak
	// 			$this->load->library("librekamjejak");
	// 			$this->librekamjejak->insertRJ('1011','','null',$permohonanId,'1011'); // param 1: Posisi/'null', param 2: Keterangan/'null', param 3: Paket_id/'null', param 4: permohonan_id/'null'
	// 			// End Insert Rekam Jejak
	// 		}

	// 		for($i=0; $i<count($reqJudul);$i++)
	// 		{
	// 			if($reqJudul[$i] == "")
	// 			{}
	// 			else
	// 			{
	// 				$permohonan_paket_analisa_file = new PermohonanPaketAnalisaFile();
	// 				$permohonan_paket_analisa_file->setField("PERMOHONAN_PAKET_ANALISA_ID", $analisaId);
	// 				$renameFile = md5(date("dmYHis").$reqLinkFile['name'][$i].$analisaId).".".getExtension($reqLinkFile['name'][$i]);
	// 				if($file->uploadToDirArray('reqLinkFile', $FILE_DIR, $renameFile, $i))
	// 				{
	// 					$insertLinkFilesSize = $file->uploadedSize;
	// 					$insertLinkFilesExe =  $file->uploadedExtension;
	// 					$insertLinkFile =  $renameFile;
	// 				}
	// 				else
	// 				{
	// 					$insertLinkFile =  $reqLinkFileTemp[$i];
	// 					$insertLinkFilesExe =  $reqLinkFileTempTipe[$i];
	// 					$insertLinkFilesSize = $reqLinkFileTempUkuran[$i];
	// 				}
	// 				$permohonan_paket_analisa_file->setField("PATH_FILE", $insertLinkFile);
	// 				$permohonan_paket_analisa_file->setField("TIPE", $insertLinkFilesExe);
	// 				$permohonan_paket_analisa_file->setField("UKURAN", ValToNullDB($insertLinkFilesSize));
	// 				$permohonan_paket_analisa_file->setField("JUDUL", $reqJudul[$i]);
	// 				$permohonan_paket_analisa_file->setField('CREATED_BY', $this->USER_LOGIN_ID);
	// 				$permohonan_paket_analisa_file->setField('CREATED_DATE', $reqToday);
	// 				$permohonan_paket_analisa_file->insert();
	// 			}
	// 			unset($permohonan_paket_analisa_file);
	// 		}
	// 		echo "Data berhasil disimpan.";
	// 	}
	// 	else
	// 	{
	// 		$permohonan_paket_analisa->setField("PERMOHONAN_PAKET_ANALISA_ID", $usulanId);
	// 		$permohonan_paket_analisa->setField('UPDATED_BY', $this->USER_LOGIN_ID);
	// 		$permohonan_paket_analisa->setField('UPDATED_DATE', $reqToday);
	// 		if($permohonan_paket_analisa->updateAnalisa())
	// 		{
	// 			$permohonan_paket_update = new PermohonanPaket();
	// 			$permohonan_paket_update->setField("PERMOHONAN_PAKET_ANALISA_ID", $usulanId);
	// 			$permohonan_paket_update->setField("NAMA", $reqNamaPaket);
	// 			$permohonan_paket_update->setField("TAHUN_ANGGARAN", $reqTahunAnggaran);
	// 			$permohonan_paket_update->setField("ANGGARAN", $reqAnggaran);
	// 			$permohonan_paket_update->setField("JENIS_BARANG_JASA", $reqJenisBarangJasa);
	// 			$permohonan_paket_update->setField("NILAI", CommaToNo($reqPerkiraanBiayaHarga));
	// 			$permohonan_paket_update->setField("PERKIRAAN_BIAYA_HARGA", CommaToNo($reqPerkiraanBiayaHarga));
	// 			$permohonan_paket_update->setField('WAKTU_PENGGUNA_BARANGJASA', dateToDBCheck($reqWaktuPenggunaBarangjasa));
	// 			$permohonan_paket_update->setField('RENCANA_PENGADAAN', dateToDBCheck($reqRencanaPengadaan));
	// 			$permohonan_paket_update->setField("CARA_PENGADAAN", $reqCaraPengadaan);
	// 			$permohonan_paket_update->setField("LAST_CREATE_USER", $this->USER_NAMA);
	// 			$permohonan_paket_update->setField("LAST_CREATE_DATE", $reqToday);

	// 			$permohonan_paket_update->updatePermohonan();

	// 			// Insert Anggaran
	// 			// 20-11-2023 ikn
	// 			$permohonan_paket_anggaran_select = new PermohonanPaket();
	// 			$permohonan_paket_anggaran_select->selectByParamsAnggaran2(array("A.PERMOHONAN_PAKET_ID" => $reqPermohonanPaketId));
	// 			// echo $permohonan_paket_anggaran_select->countRow().'----'; die;
	// 			if ($permohonan_paket_anggaran_select->countRow() > 0) { // update
	// 				$reqPermohonanPaketId = $this->input->post("reqPermohonanPaketId");
	// 				$permohonan_paket_anggaran_insert = new PermohonanPaket();
	// 				$permohonan_paket_anggaran_insert->setField("PERMOHONAN_PAKET_ID", $reqPermohonanPaketId);
	// 				$permohonan_paket_anggaran_insert->setField("INTEGRATION_IMPORT_RKA_BUDGET_ID", $reqIntegrationImportRkaBudgetId);
	// 				$permohonan_paket_anggaran_insert->setField("DEPARTMENT", $reqDepartment);
	// 				$permohonan_paket_anggaran_insert->setField("SEGMENT2_DESC", $reqDepartment);
	// 				$permohonan_paket_anggaran_insert->setField("SEGMENT3_DESC", $reqMataAnggaran);
	// 				$permohonan_paket_anggaran_insert->setField("SEGMENT4_DESC", $reqKegiatan);
	// 				$permohonan_paket_anggaran_insert->setField("SEGMENT5_DESC", $reqSumberDana);
	// 				$permohonan_paket_anggaran_insert->setField("BUDGET_AMT", $reqBudgetAmt);
	// 				$permohonan_paket_anggaran_insert->setField("REMAIN_AMT", $reqRemainAmt);
	// 				$permohonan_paket_anggaran_insert->setField('CREATED_BY', $this->USER_LOGIN_ID);
	// 				$permohonan_paket_anggaran_insert->updatePermohonanAnggaran2();
	// 			} else { // Insert
	// 				$reqPermohonanPaketId = $this->input->post("reqPermohonanPaketId");
	// 				$permohonan_paket_anggaran_insert = new PermohonanPaket();
	// 				$permohonan_paket_anggaran_insert->setField("PERMOHONAN_PAKET_ID", $reqPermohonanPaketId);
	// 				$permohonan_paket_anggaran_insert->setField("INTEGRATION_IMPORT_RKA_BUDGET_ID", $reqIntegrationImportRkaBudgetId);
	// 				$permohonan_paket_anggaran_insert->setField("DEPARTMENT", $reqDepartment);
	// 				$permohonan_paket_anggaran_insert->setField("SEGMENT2_DESC", $reqDepartment);
	// 				$permohonan_paket_anggaran_insert->setField("SEGMENT3_DESC", $reqMataAnggaran);
	// 				$permohonan_paket_anggaran_insert->setField("SEGMENT4_DESC", $reqKegiatan);
	// 				$permohonan_paket_anggaran_insert->setField("SEGMENT5_DESC", $reqSumberDana);
	// 				$permohonan_paket_anggaran_insert->setField("BUDGET_AMT", $reqBudgetAmt);
	// 				$permohonan_paket_anggaran_insert->setField("REMAIN_AMT", $reqRemainAmt);
	// 				$permohonan_paket_anggaran_insert->setField('CREATED_BY', $this->USER_LOGIN_ID);
	// 				$permohonan_paket_anggaran_insert->insertPermohonanAnggaran2();
	// 			}

	// 			// END Insert Anggaran
	// 		}

	// 		$permohonan_paket_file_delete = new PermohonanPaketAnalisaFile();
	// 		$permohonan_paket_file_delete->setField("PERMOHONAN_PAKET_ANALISA_ID", $usulanId);
	// 		$permohonan_paket_file_delete->deletePermohonan();

	// 		for($i=0; $i<count($reqJudul);$i++)
	// 		{
	// 			if($reqJudul[$i] == "")
	// 			{}
	// 			else
	// 			{
	// 				$permohonan_paket_analisa_file = new PermohonanPaketAnalisaFile();
	// 				$permohonan_paket_analisa_file->setField("PERMOHONAN_PAKET_ANALISA_ID", $usulanId);
	// 				$renameFile = md5(date("dmYHis").$reqLinkFile['name'][$i].$usulanId).".".getExtension($reqLinkFile['name'][$i]);
	// 				if($file->uploadToDirArray('reqLinkFile', $FILE_DIR, $renameFile, $i))
	// 				{
	// 					$insertLinkFilesSize = $file->uploadedSize;
	// 					$insertLinkFilesExe =  $file->uploadedExtension;
	// 					$insertLinkFile =  $renameFile;
	// 				}
	// 				else
	// 				{
	// 					$insertLinkFile =  $reqLinkFileTemp[$i];
	// 					$insertLinkFilesExe =  $reqLinkFileTempTipe[$i];
	// 					$insertLinkFilesSize = $reqLinkFileTempUkuran[$i];
	// 				}
	// 				$permohonan_paket_analisa_file->setField("PATH_FILE", $insertLinkFile);
	// 				$permohonan_paket_analisa_file->setField("TIPE", $insertLinkFilesExe);
	// 				$permohonan_paket_analisa_file->setField("UKURAN", ValToNullDB($insertLinkFilesSize));
	// 				$permohonan_paket_analisa_file->setField("JUDUL", $reqJudul[$i]);
	// 				$permohonan_paket_analisa_file->setField('CREATED_BY', $this->USER_LOGIN_ID);
	// 				$permohonan_paket_analisa_file->setField('CREATED_DATE', $reqToday);
	// 				$permohonan_paket_analisa_file->insert();
	// 			}
	// 			unset($permohonan_paket_analisa_file);
	// 		}
	// 		echo "Data berhasil diupdate.";
	// 	}
	// 		// echo "Data berhasil disimpan.-".$usulanId."-".$reqMetodePengadaan;

	// }

	public function addfileImportExcel()
	{

		$this->load->model("PermohonanPaket");
		$this->load->model("PermohonanPaketAnalisaFile");
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

		$FILE_DIR = "uploads/lampiran/";
		$FILE_DIR2 = FCPATH ."uploads/lampiran/";

		/* UPLOAD FILE */
		$renameFile = md5(date("dmYHis").$reqLinkFile['name'].$this->ID).".".getExtension($reqLinkFile['name']);
		if($file->uploadToDir('reqLinkFile', $FILE_DIR, $renameFile))
		{
			$insertLinkFilesSize = $file->uploadedSize;
			$insertLinkFilesExe =  $file->uploadedExtension;
			$insertLinkFile =  $renameFile;
			// Import Excel Jika ada file baru yang di upload
			$inputFileName = $FILE_DIR2.$insertLinkFile;
			$sheetname = 'Import Permohonan';

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
             	[A] =>
	            [B] => No 				: 1
	            [C] => Tahun Anggaran 	: 2024
	            [D] => Mata Anggaran 	: Biaya Bantuan Litigasi
	            [E] => Kegiatan 		: Biaya Pendampingan Litigas Hukum
	            [F] => Tipe Anggaran 	: OPEX
	            [G] => Sumber Dana 			: Sumber Dana Internal
	            [H] => Anggaran Kegiatan 	: 1,200,000
	            [I] => Nama Paket Pengadaan : Jasa Konsultasi POS Bantuan Hukum
	            [J] => Harga Perkiraan 		: 350,000,000
	            [K] => Produk Dalam Negeri 	: 1--Ya
	            [L] => Cara Pengadaan 		: 2--Penyedia
	            [M] => Jenis Barang/Jasa 	: 2--Jasa Konsultansi
	            [N] => Mulai Rencana Peng. 	: Jun-24
	            [O] => Waktu Peng. B/J 		: Dec-24
	            [P] => Catatan 				: Pendampingan Hukum
	            [Q] =>
                **/

                foreach ($allDataInSheet as $key => $value) {
                	if ($key >= 7) {
	                	// Parsing data ke Parameter
	                	$k = explode('--',$value['K']);
	                	$l = explode('--',$value['L']);
	                	$m = explode('--',$value['M']);
	                	$reqTahunAnggaran = $value['C'];
	                	$reqNamaKebutuhan = "";
	                	$reqAnalisaKebutuhan = "";
	                	$reqAnalisaKategori = $k[0]; // Produk dalam negeri
	                	$reqAnalisaJenisBelanja = "";
	                	$reqNote = $value['P'];
	                	$reqSumberDanaKeterangan = "";
	                	$reqIdentifikasiResiko = "";
	                	$reqIdentifikasiResikoKeterangan = "";
						$reqToday = date('Y-m-d H:i:s');
	                	$reqNamaPaket = $value['I'];
	                	$reqAnggaran = "";
	                	$reqJenisBarangJasa = $m[0];
	                	$reqPerkiraanBiayaHarga = $value['J'];
	                	$reqWaktuPenggunaBarangjasa = $this->parsingMonht($value['O']);
	                	$reqRencanaPengadaan = $this->parsingMonht($value['N']);
	                	$reqCaraPengadaan = $l[0];
	                	$reqMataAnggaran = $value['D'];
	                	$reqKegiatan = $value['E'];
	                	$reqSumberDana = $value['G'];
	                	$reqBudgetRemaining = $value['H'];
	                	$reqDepartment = $this->DEPARTMENT;
	                	$reqDepartmentCode = "";
	                	$reqKodeMataAnggaran = "";
	                	$reqKodeKegiatan = "";
	                	$reqTotalBudget = 0;
	                	$reqTipeTransaksi = $value['F'];

	                	$filepath 	= 'logs/importpermohonan/importpermohonan_' . date('Y-m-d') . '.txt';
						$textNya   	= "Import Baris ke-".$key." ### ".$value['B']." ### ".$value['C']." ### ".$value['D']." ### ".$value['E']." ### ".$value['F']." ### ".$this->USER_LOGIN_ID."::".$this->USER_NAMA." ### ".date('Y-m-d H:i:s');

	                	if ($value['C']) // Jika Tahun Anggaran Terisi
	                	{
							$permohonan_paket_analisa = new PermohonanPaket();

		                	//echo "sddsa".dotToNo($reqNilai);exit;
							$permohonan_paket_analisa->setField('TAHUN_ANGGARAN', $reqTahunAnggaran);
							// $permohonan_paket_analisa->setField('KOMODITAS_ID', $reqKomoditas);
							$permohonan_paket_analisa->setField('NAMA_KEBUTUHAN', $reqNamaKebutuhan);
							$permohonan_paket_analisa->setField('ANALISA_KEBUTUHAN_ID', $reqAnalisaKebutuhan);
							$permohonan_paket_analisa->setField('PERMOHONAN_PAKET_ANALISA_KATEGORI_ID', $reqAnalisaKategori);
							$permohonan_paket_analisa->setField('PERMOHONAN_PAKET_ANALISA_JENIS_BELANJA_ID', $reqAnalisaJenisBelanja);
							$permohonan_paket_analisa->setField('NOTE', $reqNote);
							$permohonan_paket_analisa->setField('SUMBER_DANA_KETERANGAN', $reqSumberDanaKeterangan);
							// $permohonan_paket_analisa->setField('ANALISA_PASAR_ID', $reqAnalisaPasar);
							$permohonan_paket_analisa->setField('IDENTIFIKASI_RESIKO', $reqIdentifikasiResiko);
							$permohonan_paket_analisa->setField('IDENTIFIKASI_RESIKO_KETERANGAN', $reqIdentifikasiResikoKeterangan);
							$permohonan_paket_analisa->setField('CREATED_BY', $this->USER_LOGIN_ID);
							$permohonan_paket_analisa->setField('CREATED_DATE', $reqToday);

		                	if($permohonan_paket_analisa->insertAnalisa())
							{
								$analisaId = $permohonan_paket_analisa->id;
								// ikn 20201026
								$permohonan_paket_insert = new PermohonanPaket();
								$permohonan_paket_insert->setField("PERMOHONAN_PAKET_ANALISA_ID", $analisaId);
								$permohonan_paket_insert->setField("NAMA", $reqNamaPaket);
								$permohonan_paket_insert->setField("TAHUN_ANGGARAN", $reqTahunAnggaran);
								$permohonan_paket_insert->setField("ANGGARAN", $reqAnggaran);
								$permohonan_paket_insert->setField("JENIS_BARANG_JASA", $reqJenisBarangJasa);
								// $permohonan_paket_insert->setField('JUMLAH', dotToNo($reqNilai));
								$permohonan_paket_insert->setField("NILAI", CommaToNo($reqPerkiraanBiayaHarga));
								$permohonan_paket_insert->setField("PERKIRAAN_BIAYA_HARGA", CommaToNo($reqPerkiraanBiayaHarga));
								$permohonan_paket_insert->setField('WAKTU_PENGGUNA_BARANGJASA', dateToDBCheck($reqWaktuPenggunaBarangjasa));
								$permohonan_paket_insert->setField('RENCANA_PENGADAAN', dateToDBCheck($reqRencanaPengadaan));
								$permohonan_paket_insert->setField("CARA_PENGADAAN", $reqCaraPengadaan);
								$permohonan_paket_insert->setField('USER_LOGIN_ID', $this->USER_LOGIN_ID);
								$permohonan_paket_insert->setField('UNIT_KERJA_ID', $this->UNIT_KERJA_ID);
								$permohonan_paket_insert->setField("LAST_CREATE_USER", $this->USER_NAMA);
								$permohonan_paket_insert->setField("LAST_CREATE_DATE", $reqToday);

								$permohonan_paket_insert->insertPermohonan();
								$permohonanId = $permohonan_paket_insert->id;

								// Insert Anggaran
								// 20-11-2023 ikn
								$permohonan_paket_anggaran_insert = new PermohonanPaket();
								$permohonan_paket_anggaran_insert->setField("PERMOHONAN_PAKET_ID", $permohonanId);
								$permohonan_paket_anggaran_insert->setField("MATA_ANGGARAN", $reqMataAnggaran);
								$permohonan_paket_anggaran_insert->setField("KEGIATAN", $reqKegiatan);
								$permohonan_paket_anggaran_insert->setField("SUMBER_DANA", $reqSumberDana);
								$permohonan_paket_anggaran_insert->setField("BUDGET_REMAINING", CommaToNo($reqBudgetRemaining));
								$permohonan_paket_anggaran_insert->setField("DEPARTMENT", $reqDepartment);
								$permohonan_paket_anggaran_insert->setField("DEPARTMENT_CODE", $reqDepartmentCode);
								$permohonan_paket_anggaran_insert->setField("KODE_MATA_ANGGARAN", $reqKodeMataAnggaran);
								$permohonan_paket_anggaran_insert->setField("KODE_KEGIATAN", $reqKodeKegiatan);
								$permohonan_paket_anggaran_insert->setField("TOTAL_BUDGET", $reqTotalBudget);
								$permohonan_paket_anggaran_insert->setField("TIPE_TRANSAKSI", $reqTipeTransaksi);
								$permohonan_paket_anggaran_insert->setField('CREATED_BY', $this->USER_LOGIN_ID);
								$permohonan_paket_anggaran_insert->insertPermohonanAnggaran();
								// END Insert Anggaran

								// echo "Data berhasil disimpan.";
								// Insert Rekam Jejak
								$this->load->library("librekamjejak");
								$this->librekamjejak->insertRJ('101','','null',$permohonanId,'101'); // param 1: Posisi/'null', param 2: Keterangan/'null', param 3: Paket_id/'null', param 4: permohonan_id/'null'
								// End Insert Rekam Jejak
							}

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

	function parsingMonht($month)
	{
		$monthVal = explode("-",$month);
		switch ($monthVal[0]) {
			case 'Jan': $m = '01'; break;
			case 'Feb': $m = '02'; break;
			case 'Mar': $m = '03'; break;
			case 'Apr': $m = '04'; break;
			case 'May': $m = '05'; break;
			case 'Jun': $m = '06'; break;
			case 'Jul': $m = '07'; break;
			case 'Aug': $m = '08'; break;
			case 'Sep': $m = '09'; break;
			case 'Oct': $m = '10'; break;
			case 'Nov': $m = '11'; break;
			case 'Dec': $m = '12'; break;

			default: $m = '01'; break;
		}

		return '01-'.$m.'-20'.$monthVal[1];
	}

	function permohonan_usulan_monitoring_json()
	{
		$this->load->library("kauth");  $userLogin = new kauth();
		$this->load->model("PermohonanPaket");
		$permohonan_paket = new PermohonanPaket();

		ini_set("memory_limit","500M");
		ini_set('max_execution_time', 5200);

		$reqStatus= $this->input->get("reqStatus");

		$aColumns = array("PERMOHONAN_PAKET_ANALISA_ID","POSTING","APPROVAL","PERMOHONAN_PAKET_ID","TAHUN_ANGGARAN", "NAMA", "KATEGORI_STR", "JENIS_BELANJA_STR", "PERKIRAAN_BIAYA_HARGA", "RENCANA_PENGADAAN", "WAKTU_PENGGUNA_BARANGJASA");
		$aColumnsAlias = array("PERMOHONAN_PAKET_ANALISA_ID","POSTING","APPROVAL","PERMOHONAN_PAKET_ID","TAHUN_ANGGARAN", "NAMA", "KATEGORI_STR", "JENIS_BELANJA_STR", "PERKIRAAN_BIAYA_HARGA", "RENCANA_PENGADAAN", "WAKTU_PENGGUNA_BARANGJASA");

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
				$sOrder = " ORDER BY A.PERMOHONAN_PAKET_ANALISA_ID ASC, A.CREATED_DATE ASC";

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

		$statement='';
		$searchJson='';

		$statement .= " AND (A.APPROVAL != '1' OR A.APPROVAL IS NULL)";

		$searchJson .= " AND (UPPER(A.NAMA_KEBUTUHAN) LIKE '%".strtoupper($_GET['sSearch'])."%' OR UPPER(A.TAHUN_ANGGARAN) LIKE '%".strtoupper($_GET['sSearch'])."%' OR UPPER(A.KATEGORI_STR) LIKE '%".strtoupper($_GET['sSearch'])."%' OR UPPER(A.JENIS_BELANJA_STR) LIKE '%".strtoupper($_GET['sSearch'])."%' OR UPPER(A.JENIS_BARANG_JASA_STR) LIKE '%".strtoupper($_GET['sSearch'])."%')";

		$allRecord = $permohonan_paket->getCountByParamsUsulan(array("A.CREATED_BY" => $this->USER_LOGIN_ID), $statement);
		if($_GET['sSearch'] == "")
			$allRecordFilter = $allRecord;
		else
			$allRecordFilter = $permohonan_paket->getCountByParamsUsulan(array("A.CREATED_BY" => $this->USER_LOGIN_ID), $statement.$searchJson);

		$permohonan_paket->selectByParamsUsulan(array("A.CREATED_BY" => $this->USER_LOGIN_ID), $dsplyRange, $dsplyStart, $statement.$searchJson, $sOrder);

		$output = array(
			"sEcho" => intval($_GET['sEcho']),
			"iTotalRecords" => $allRecord,
			"iTotalDisplayRecords" => $allRecordFilter,
			"aaData" => array()
		);

		while($permohonan_paket->nextRow())
		{
			$row = array();
			for ( $i=0 ; $i<count($aColumns) ; $i++ )
			{
				// $this->load->library("librekamjejak");
				// $statusApprove = $this->librekamjejak->statusPerencanaan($permohonan_paket->getField('STATUS'),$permohonan_paket->getField('STATUS_ID'));

				if ($permohonan_paket->getField('APPROVAL') == '1') { // DITERIMA
					$statusApprove = '<span class="badge badge-success" style="font-size:10px">Terima</span>';
				} else if ($permohonan_paket->getField('APPROVAL') == '2') { // ditolak
					$statusApprove = '<span class="badge badge-danger" style="font-size:10px">Tolak</span>';
				} else if ($permohonan_paket->getField('APPROVAL') == '3') { // diteruskan
					$statusApprove = '<span class="badge badge-success" style="font-size:10px">Draft</span>';
				} else if ($permohonan_paket->getField('APPROVAL') == '6') { // To Be Approve
					$statusApprove = '<span class="badge badge-dark" style="font-size:10px">To Be Approve</span>';
				} else {
					$statusApprove = '<span class="badge badge-info" style="font-size:10px">Input</span>';
				}
				// if($aColumns[$i] == "TANGGAL")
				// 	$row[] = getFormattedDate($permohonan_paket->getField($aColumns[$i]));
				// else if($aColumns[$i] == "KETERANGAN")
				//  $row[] = truncate($permohonan_paket->getField($aColumns[$i]), 5)."...";
				// if($aColumns[$i] == "NAMA_KEBUTUHAN")
				if($aColumns[$i] == "NAMA") {
					$row[] = $statusApprove.' <br>'.$permohonan_paket->getField($aColumns[$i]);
				}
				else if($aColumns[$i] == "TAHUN_ANGGARAN") {
					if ($permohonan_paket->getField('APPROVAL') != '2' && $permohonan_paket->getField('APPROVAL') != '0') {
						$row[] = $permohonan_paket->getField($aColumns[$i]);
					} else {
						$row[] = '<input style="cursor:pointer" class="check" type="checkbox" value="'.$permohonan_paket->getField('PERMOHONAN_PAKET_ANALISA_ID').'"> '.$permohonan_paket->getField($aColumns[$i]);
					}
				}
				else if($aColumns[$i] == "PERKIRAAN_BIAYA_HARGA") {
					$row[] = numberToIna($permohonan_paket->getField($aColumns[$i]));
				}
				else if($aColumns[$i] == "WAKTU_PENGGUNA_BARANGJASA" || $aColumns[$i] == "RENCANA_PENGADAAN") {
					$row[] = substr(getFormattedDateShort($permohonan_paket->getField($aColumns[$i])),3,10);
				}
				else {
					$row[] = $permohonan_paket->getField($aColumns[$i]);
				}
			}

			$output['aaData'][] = $row;
		}

		echo json_encode( $output );
	}

	function permohonan_usulan_monitoring_divisi_json()
	{
		$this->load->library("kauth");  $userLogin = new kauth();
		$this->load->model("PermohonanPaket");
		$permohonan_paket = new PermohonanPaket();

		ini_set("memory_limit","500M");
		ini_set('max_execution_time', 5200);

		$reqStatus= $this->input->get("reqStatus");

		$aColumns = array("PERMOHONAN_PAKET_ANALISA_ID","POSTING","APPROVAL","PERMOHONAN_PAKET_ID","TAHUN_ANGGARAN", "NAMA", "KATEGORI_STR", "JENIS_BELANJA_STR", "PERKIRAAN_BIAYA_HARGA", "RENCANA_PENGADAAN", "WAKTU_PENGGUNA_BARANGJASA", "PEMBUAT");
		$aColumnsAlias = array("PERMOHONAN_PAKET_ANALISA_ID","POSTING","APPROVAL","PERMOHONAN_PAKET_ID","TAHUN_ANGGARAN", "NAMA", "KATEGORI_STR", "JENIS_BELANJA_STR", "PERKIRAAN_BIAYA_HARGA", "RENCANA_PENGADAAN", "WAKTU_PENGGUNA_BARANGJASA", "PEMBUAT");

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
			if ( trim($sOrder) == "ORDER BY PERMOHONAN_PAKET_ANALISA_ID asc, PERMOHONAN_PAKET_ANALISA_ID asc" )
			{
				$sOrder = " ORDER BY A.PERMOHONAN_PAKET_ANALISA_ID ASC, A.CREATED_DATE ASC";

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

		$statement='';
		$searchJson='';

		$statement .= " AND (A.APPROVAL != '1' OR A.APPROVAL IS NULL) AND DEPARTMENT = '".$this->DEPARTMENT."'";

		$searchJson .= " AND (UPPER(A.NAMA_KEBUTUHAN) LIKE '%".strtoupper($_GET['sSearch'])."%' OR UPPER(A.TAHUN_ANGGARAN) LIKE '%".strtoupper($_GET['sSearch'])."%' OR UPPER(A.KATEGORI_STR) LIKE '%".strtoupper($_GET['sSearch'])."%' OR UPPER(A.JENIS_BELANJA_STR) LIKE '%".strtoupper($_GET['sSearch'])."%' OR UPPER(A.JENIS_BARANG_JASA_STR) LIKE '%".strtoupper($_GET['sSearch'])."%' OR UPPER(A.PEMBUAT) LIKE '%".strtoupper($_GET['sSearch'])."%')";

		$allRecord = $permohonan_paket->getCountByParamsUsulan(array(), $statement);
		if($_GET['sSearch'] == "")
			$allRecordFilter = $allRecord;
		else
			$allRecordFilter = $permohonan_paket->getCountByParamsUsulan(array(), $statement.$searchJson);

		$permohonan_paket->selectByParamsUsulan(array(), $dsplyRange, $dsplyStart, $statement.$searchJson, $sOrder);
		// echo $permohonan_paket->query; die;
		/* Output */
		$output = array(
			"sEcho" => intval($_GET['sEcho']),
			"iTotalRecords" => $allRecord,
			"iTotalDisplayRecords" => $allRecordFilter,
			"aaData" => array()
		);

		while($permohonan_paket->nextRow())
		{
			$row = array();
			for ( $i=0 ; $i<count($aColumns) ; $i++ )
			{
				// $this->load->library("librekamjejak");
				// $statusApprove = $this->librekamjejak->statusPerencanaan($permohonan_paket->getField('STATUS'),$permohonan_paket->getField('STATUS_ID'));

				if ($permohonan_paket->getField('APPROVAL') == '1') { // DITERIMA
					$statusApprove = '<span class="badge badge-success" style="font-size:10px">Terima</span>';
				} else if ($permohonan_paket->getField('APPROVAL') == '2') { // ditolak
					$statusApprove = '<span class="badge badge-danger" style="font-size:10px">Tolak</span>';
				} else if ($permohonan_paket->getField('APPROVAL') == '3') { // diteruskan
					$statusApprove = '<span class="badge badge-success" style="font-size:10px">Draft</span>';
				} else if ($permohonan_paket->getField('APPROVAL') == '6') { // To Be Approve
					$statusApprove = '<span class="badge badge-dark" style="font-size:10px">To Be Approve</span>';
				} else {
					$statusApprove = '<span class="badge badge-info" style="font-size:10px">Input</span>';
				}
				// if($aColumns[$i] == "TANGGAL")
				// 	$row[] = getFormattedDate($permohonan_paket->getField($aColumns[$i]));
				// else if($aColumns[$i] == "KETERANGAN")
				//  $row[] = truncate($permohonan_paket->getField($aColumns[$i]), 5)."...";
				// if($aColumns[$i] == "NAMA_KEBUTUHAN")
				if($aColumns[$i] == "NAMA") {
					$row[] = $statusApprove.' <br>'.$permohonan_paket->getField($aColumns[$i]);
				}
				else if($aColumns[$i] == "TAHUN_ANGGARAN") {
					if ($permohonan_paket->getField('APPROVAL') != '2' && $permohonan_paket->getField('APPROVAL') != '0') {
						$row[] = $permohonan_paket->getField($aColumns[$i]);
					} else {
						$row[] = $permohonan_paket->getField($aColumns[$i]);
					}
				}
				else if($aColumns[$i] == "PERKIRAAN_BIAYA_HARGA") {
					$row[] = numberToIna($permohonan_paket->getField($aColumns[$i]));
				}
				else if($aColumns[$i] == "WAKTU_PENGGUNA_BARANGJASA" || $aColumns[$i] == "RENCANA_PENGADAAN") {
					$row[] = substr(getFormattedDateShort($permohonan_paket->getField($aColumns[$i])),3,10);
				}
				else {
					$row[] = $permohonan_paket->getField($aColumns[$i]);
				}
			}

			$output['aaData'][] = $row;
		}

		echo json_encode( $output );
	}

	function permohonan_usulan_monitoring_admin_json()
	{
		$this->load->library("kauth");  $userLogin = new kauth();
		$this->load->model("PermohonanPaket");
		$permohonan_paket = new PermohonanPaket();

		ini_set("memory_limit","500M");
		ini_set('max_execution_time', 5200);

		$reqStatus= $this->input->get("reqStatus");

		$aColumns = array("PERMOHONAN_PAKET_ANALISA_ID","POSTING","APPROVAL","PERMOHONAN_PAKET_ID","TAHUN_ANGGARAN", "NAMA", "KATEGORI_STR", "JENIS_BELANJA_STR", "PERKIRAAN_BIAYA_HARGA", "RENCANA_PENGADAAN", "WAKTU_PENGGUNA_BARANGJASA", "JENIS_BARANG_JASA_STR", "CARA_PENGADAAN_STR","PEMBUAT","KODE_RUP");
		$aColumnsAlias = array("PERMOHONAN_PAKET_ANALISA_ID","POSTING","APPROVAL","PERMOHONAN_PAKET_ID","TAHUN_ANGGARAN", "NAMA", "KATEGORI_STR", "JENIS_BELANJA_STR", "PERKIRAAN_BIAYA_HARGA", "RENCANA_PENGADAAN", "WAKTU_PENGGUNA_BARANGJASA", "JENIS_BARANG_JASA_STR", "CARA_PENGADAAN_STR","PEMBUAT","KODE_RUP");

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

			if ( trim($sOrder) == "ORDER BY PERMOHONAN_PAKET_ANALISA_ID asc, PERMOHONAN_PAKET_ANALISA_ID asc" )
			{
				$sOrder = " ORDER BY A.PERMOHONAN_PAKET_ANALISA_ID ASC, A.CREATED_DATE ASC";

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

		$statement='';
		$searchJson='';

		// $statement .= " AND (A.APPROVAL = '3') "; // 3: diteruskan
		// $statement .= " AND (A.APPROVAL = '3') AND (A.ADMIN_RUP = '".$this->USER_LOGIN_ID."') "; // 3: diteruskan
		$statement .= " AND (A.APPROVAL IN ('3','3241','3251')) AND (A.ADMIN_RUP = '".$this->USER_LOGIN_ID."') "; // 3: diteruskan

		// $searchJson .= " AND (UPPER(A.NAMA_KEBUTUHAN) LIKE '%".strtoupper($_GET['sSearch'])."%' OR UPPER(A.TAHUN_ANGGARAN) LIKE '%".strtoupper($_GET['sSearch'])."%')";

		$searchJson .= " AND (UPPER(NAMA) LIKE '%".strtoupper($_GET['sSearch'])."%' OR UPPER(TAHUN_ANGGARAN) LIKE '%".strtoupper($_GET['sSearch'])."%' OR UPPER(PEMBUAT) LIKE '%".strtoupper($_GET['sSearch'])."%' )";



		$allRecord = $permohonan_paket->getCountByParamsUsulan(array(), $statement);
		if($_GET['sSearch'] == "")
			$allRecordFilter = $allRecord;
		else
			// $allRecordFilter = $permohonan_paket->getCountByParamsUsulan(array("A.CREATED_BY" => $this->USER_LOGIN_ID), $statement.$searchJson);
			$allRecordFilter = $permohonan_paket->getCountByParamsUsulan(array(), $statement.$searchJson);

		$permohonan_paket->selectByParamsUsulan(array(), $dsplyRange, $dsplyStart, $statement.$searchJson, $sOrder);

		/* Output */
		$output = array(
			"sEcho" => intval($_GET['sEcho']),
			"iTotalRecords" => $allRecord,
			"iTotalDisplayRecords" => $allRecordFilter,
			"aaData" => array()
		);

		while($permohonan_paket->nextRow())
		{
			$row = array();
			for ( $i=0 ; $i<count($aColumns) ; $i++ )
			{
				// $this->load->library("librekamjejak");
				// $statusApprove = $this->librekamjejak->statusPerencanaan($permohonan_paket->getField('STATUS'),$permohonan_paket->getField('STATUS_ID'));
				if ($permohonan_paket->getField('APPROVAL') == '1') { // DITERIMA
					$statusApprove = '<span class="badge badge-success" style="font-size:10px">Terima</span>';
				} else if ($permohonan_paket->getField('APPROVAL') == '2') { // ditolak
					$statusApprove = '<span class="badge badge-danger" style="font-size:10px">Tolak</span>';
				} else if ($permohonan_paket->getField('APPROVAL') == '3') { // diteruskan
					$statusApprove = '<span class="badge badge-success" style="font-size:10px">Draft</span>';
				} else {
					$statusApprove = '<span class="badge badge-info" style="font-size:10px">Input</span>';
				}
				// if($aColumns[$i] == "TANGGAL")
				// 	$row[] = getFormattedDate($permohonan_paket->getField($aColumns[$i]));
				// else if($aColumns[$i] == "KETERANGAN")
				//  $row[] = truncate($permohonan_paket->getField($aColumns[$i]), 5)."...";
				if($aColumns[$i] == "NAMA")
					$row[] = $statusApprove.' <small class="badge badge-info" style="font-size:10px">Pengguna: <i>'.$permohonan_paket->getField('PEMBUAT').'</i></small><br>'.$permohonan_paket->getField($aColumns[$i]);
				else if($aColumns[$i] == "TAHUN_ANGGARAN")
					$row[] = '<input style="cursor:pointer" class="check" type="checkbox" value="'.$permohonan_paket->getField('PERMOHONAN_PAKET_ANALISA_ID').'"> '.$permohonan_paket->getField($aColumns[$i]);
				else if($aColumns[$i] == "PERKIRAAN_BIAYA_HARGA")
					$row[] = numberToIna($permohonan_paket->getField($aColumns[$i]));
				else if($aColumns[$i] == "WAKTU_PENGGUNA_BARANGJASA" || $aColumns[$i] == "RENCANA_PENGADAAN")
					$row[] = substr(getFormattedDateShort($permohonan_paket->getField($aColumns[$i])),3,10);
				else
					$row[] = $permohonan_paket->getField($aColumns[$i]);
			}

			$output['aaData'][] = $row;
		}

		echo json_encode( $output );
	}

	function permohonan_usulan_monitoring_admin_tobeapproved_json()
	{
		$this->load->library("kauth");  $userLogin = new kauth();
		$this->load->model("PermohonanPaket");
		$permohonan_paket = new PermohonanPaket();

		ini_set("memory_limit","500M");
		ini_set('max_execution_time', 5200);

		$reqStatus= $this->input->get("reqStatus");

		$aColumns = array("PERMOHONAN_PAKET_ANALISA_ID","POSTING","APPROVAL","PERMOHONAN_PAKET_ID","TAHUN_ANGGARAN", "NAMA", "KATEGORI_STR", "JENIS_BELANJA_STR", "PERKIRAAN_BIAYA_HARGA", "RENCANA_PENGADAAN", "WAKTU_PENGGUNA_BARANGJASA", "JENIS_BARANG_JASA_STR", "CARA_PENGADAAN_STR","PEMBUAT","KODE_RUP");
		$aColumnsAlias = array("PERMOHONAN_PAKET_ANALISA_ID","POSTING","APPROVAL","PERMOHONAN_PAKET_ID","TAHUN_ANGGARAN", "NAMA", "KATEGORI_STR", "JENIS_BELANJA_STR", "PERKIRAAN_BIAYA_HARGA", "RENCANA_PENGADAAN", "WAKTU_PENGGUNA_BARANGJASA", "JENIS_BARANG_JASA_STR", "CARA_PENGADAAN_STR","PEMBUAT","KODE_RUP");

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

			$sOrder = substr_replace( $sOrder, "", -2 );

			if ( trim($sOrder) == "ORDER BY PERMOHONAN_PAKET_ANALISA_ID asc, PERMOHONAN_PAKET_ANALISA_ID asc" )
			{
				$sOrder = " ORDER BY A.PERMOHONAN_PAKET_ANALISA_ID ASC, A.CREATED_DATE ASC";

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

		$statement='';
		$searchJson='';

		$statement .= " AND (A.APPROVAL IN ('6')) AND (A.ADMIN_RUP = '".$this->USER_LOGIN_ID."') "; // 3: diteruskan

		$searchJson .= " AND (UPPER(NAMA) LIKE '%".strtoupper($_GET['sSearch'])."%' OR UPPER(TAHUN_ANGGARAN) LIKE '%".strtoupper($_GET['sSearch'])."%' OR UPPER(PEMBUAT) LIKE '%".strtoupper($_GET['sSearch'])."%' )";


		$allRecord = $permohonan_paket->getCountByParamsUsulan(array(), $statement);
		if($_GET['sSearch'] == "")
			$allRecordFilter = $allRecord;
		else
			$allRecordFilter = $permohonan_paket->getCountByParamsUsulan(array(), $statement.$searchJson);

		$permohonan_paket->selectByParamsUsulan(array(), $dsplyRange, $dsplyStart, $statement.$searchJson, $sOrder);
		// echo $permohonan_paket->query; die;
		/* Output */
		$output = array(
			"sEcho" => intval($_GET['sEcho']),
			"iTotalRecords" => $allRecord,
			"iTotalDisplayRecords" => $allRecordFilter,
			"aaData" => array()
		);

		while($permohonan_paket->nextRow())
		{
			$row = array();
			for ( $i=0 ; $i<count($aColumns) ; $i++ )
			{
				if ($permohonan_paket->getField('APPROVAL') == '1') { // DITERIMA
					$statusApprove = '<span class="badge badge-success" style="font-size:10px">Terima</span>';
				} else if ($permohonan_paket->getField('APPROVAL') == '2') { // ditolak
					$statusApprove = '<span class="badge badge-danger" style="font-size:10px">Tolak</span>';
				} else if ($permohonan_paket->getField('APPROVAL') == '3') { // diteruskan
					$statusApprove = '<span class="badge badge-success" style="font-size:10px">Draft</span>';
				} else if ($permohonan_paket->getField('APPROVAL') == '6') { // To Be Approve
					$statusApprove = '<span class="badge badge-dark" style="font-size:10px">To Be Approve</span>';
				} else {
					$statusApprove = '<span class="badge badge-info" style="font-size:10px">Input</span>';
				}
				if($aColumns[$i] == "NAMA")
					$row[] = $statusApprove.' <small class="badge badge-info" style="font-size:10px">Pengguna: <i>'.$permohonan_paket->getField('PEMBUAT').'</i></small><br>'.$permohonan_paket->getField($aColumns[$i]);
				else if($aColumns[$i] == "TAHUN_ANGGARAN")
					$row[] = '<input style="cursor:pointer" class="check" type="checkbox" value="'.$permohonan_paket->getField('PERMOHONAN_PAKET_ANALISA_ID').'"> '.$permohonan_paket->getField($aColumns[$i]);
				else if($aColumns[$i] == "PERKIRAAN_BIAYA_HARGA")
					$row[] = numberToIna($permohonan_paket->getField($aColumns[$i]));
				else if($aColumns[$i] == "WAKTU_PENGGUNA_BARANGJASA" || $aColumns[$i] == "RENCANA_PENGADAAN")
					$row[] = substr(getFormattedDateShort($permohonan_paket->getField($aColumns[$i])),3,10);
				else
					$row[] = $permohonan_paket->getField($aColumns[$i]);
			}

			$output['aaData'][] = $row;
		}

		echo json_encode( $output );
	}

	function permohonan_usulan_monitoring_admin_rup_json()
	{
		$this->load->library("kauth");  $userLogin = new kauth();
		$this->load->model("PermohonanPaket");
		$permohonan_paket = new PermohonanPaket();

		ini_set("memory_limit","500M");
		ini_set('max_execution_time', 5200);

		$reqStatus= $this->input->get("reqStatus");
		$reqSearch = $this->input->post("reqSearch");


		$aColumns = array("PERMOHONAN_PAKET_ANALISA_ID","POSTING","APPROVAL", "TAHUN_ANGGARAN", "NAMA_KEBUTUHAN", "PERKIRAAN_BIAYA_HARGA", "WAKTU_PENGGUNA_BARANGJASA", "RENCANA_PENGADAAN", "CARA_PENGADAAN_STR","PEMBUAT");
		$aColumnsAlias = array("PERMOHONAN_PAKET_ANALISA_ID","POSTING","APPROVAL", "TAHUN_ANGGARAN", "NAMA_KEBUTUHAN", "PERKIRAAN_BIAYA_HARGA", "WAKTU_PENGGUNA_BARANGJASA", "RENCANA_PENGADAAN", "CARA_PENGADAAN_STR","PEMBUAT");

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
			if ( trim($sOrder) == "ORDER BY TAHUN_ANGGARAN ASC" )
			{
				$sOrder = " ORDER BY TAHUN_ANGGARAN ASC";

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
		$searchJson='';

		// $statement .= " AND (A.APPROVAL != '0') "; // 0: belom diteruskan
		$statement .= " AND (A.APPROVAL != '0') AND (A.ADMIN_RUP = '".$this->USER_LOGIN_ID."') "; // 3: diteruskan

		$searchJson .= " AND (UPPER(A.NAMA_KEBUTUHAN) LIKE '%".strtoupper($_GET['sSearch'])."%' OR UPPER(A.TAHUN_ANGGARAN) LIKE '%".strtoupper($_GET['sSearch'])."%' OR UPPER(A.CARA_PENGADAAN_STR) LIKE '%".strtoupper($_GET['sSearch'])."%')";

		$allRecord = $permohonan_paket->getCountByParamsUsulan(array(), $statement);
		if($_GET['sSearch'] == "")
			$allRecordFilter = $allRecord;
		else
			$allRecordFilter = $permohonan_paket->getCountByParamsUsulan(array(), $statement.$searchJson);

		$permohonan_paket->selectByParamsUsulan(array(), $dsplyRange, $dsplyStart, $statement.$searchJson, $sOrder);

		/* Output */
		$output = array(
			"sEcho" => intval($_GET['sEcho']),
			"iTotalRecords" => $allRecord,
			"iTotalDisplayRecords" => $allRecordFilter,
			"aaData" => array()
		);

		while($permohonan_paket->nextRow())
		{
			$row = array();
			for ( $i=0 ; $i<count($aColumns) ; $i++ )
			{
				if ($permohonan_paket->getField('APPROVAL') == '1') { // DITERIMA
					$statusApprove = '<span class="badge badge-success" style="font-size:10px">Terima</span>';
				} else if ($permohonan_paket->getField('APPROVAL') == '2') { // ditolak
					$statusApprove = '<span class="badge badge-danger" style="font-size:10px">Tolak</span>';
				} else if ($permohonan_paket->getField('APPROVAL') == '3') { // diteruskan
					$statusApprove = '<span class="badge badge-success" style="font-size:10px">Draft</span>';
				} else {
					$statusApprove = '<span class="badge badge-info" style="font-size:10px">Input</span>';
				}
				if($aColumns[$i] == "NAMA_KEBUTUHAN")
					// $row[] = $statusApprove.' '.$permohonan_paket->getField($aColumns[$i]);
					$row[] = $statusApprove.' <small class="badge badge-info" style="font-size:10px">User: <i>'.$permohonan_paket->getField('PEMBUAT').'</i></small><br>'.$permohonan_paket->getField($aColumns[$i]);
				else if($aColumns[$i] == "PERKIRAAN_BIAYA_HARGA")
					$row[] = numberToIna($permohonan_paket->getField($aColumns[$i]));
				else if($aColumns[$i] == "WAKTU_PENGGUNA_BARANGJASA" || $aColumns[$i] == "RENCANA_PENGADAAN")
					$row[] = getFormattedDateShort($permohonan_paket->getField($aColumns[$i]));
				else
					$row[] = $permohonan_paket->getField($aColumns[$i]);
			}

			$output['aaData'][] = $row;
		}

		echo json_encode( $output );
	}

	function permohonan_usulan_monitoring_validator_json()
	{
		$this->load->library("kauth");  $userLogin = new kauth();
		$this->load->model("PermohonanPaket");
		$permohonan_paket = new PermohonanPaket();

		ini_set("memory_limit","500M");
		ini_set('max_execution_time', 5200);

		$reqStatus= $this->input->get("reqStatus");

		$aColumns = array("PERMOHONAN_PAKET_ANALISA_ID","POSTING","APPROVAL", "TAHUN_ANGGARAN", "NAMA_KEBUTUHAN", "KATEGORI_STR", "JENIS_BELANJA_STR", "PERKIRAAN_BIAYA_HARGA", "WAKTU_PENGGUNA_BARANGJASA", "RENCANA_PENGADAAN", "JENIS_BARANG_JASA_STR", "CARA_PENGADAAN_STR","PEMBUAT");
		$aColumnsAlias = array("PERMOHONAN_PAKET_ANALISA_ID","POSTING","APPROVAL", "TAHUN_ANGGARAN", "NAMA_KEBUTUHAN", "KATEGORI_STR", "JENIS_BELANJA_STR", "PERKIRAAN_BIAYA_HARGA", "WAKTU_PENGGUNA_BARANGJASA", "RENCANA_PENGADAAN", "JENIS_BARANG_JASA_STR", "CARA_PENGADAAN_STR","PEMBUAT");

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


			//Remove the last space / comma
			$sOrder = substr_replace( $sOrder, "", -2 );

			//Check if there is an order by clause
			if ( trim($sOrder) == "ORDER BY PERMOHONAN_PAKET_ANALISA_ID asc, PERMOHONAN_PAKET_ANALISA_ID asc" )
			{
				$sOrder = " ORDER BY A.PERMOHONAN_PAKET_ANALISA_ID ASC, A.CREATED_DATE ASC";
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

		$statement='';
		$searchJson='';

		$statement .= " AND (A.APPROVAL IN ('41','41242')) AND (A.VALIDATOR_1 = '".$this->USER_LOGIN_ID."') "; // 3: diteruskan

		$searchJson .= " AND (UPPER(A.NAMA_KEBUTUHAN) LIKE '%".strtoupper($_GET['sSearch'])."%' OR UPPER(A.TAHUN_ANGGARAN) LIKE '%".strtoupper($_GET['sSearch'])."%' OR UPPER(A.KATEGORI_STR) LIKE '%".strtoupper($_GET['sSearch'])."%' OR UPPER(A.JENIS_BELANJA_STR) LIKE '%".strtoupper($_GET['sSearch'])."%' OR UPPER(A.JENIS_BARANG_JASA_STR) LIKE '%".strtoupper($_GET['sSearch'])."%')";

		$allRecord = $permohonan_paket->getCountByParamsUsulan(array(), $statement);
		if($_GET['sSearch'] == "")
			$allRecordFilter = $allRecord;
		else
			$allRecordFilter = $permohonan_paket->getCountByParamsUsulan(array(), $statement.$searchJson);

		$permohonan_paket->selectByParamsUsulan(array(), $dsplyRange, $dsplyStart, $statement.$searchJson, $sOrder);

		/* Output */
		$output = array(
			"sEcho" => intval($_GET['sEcho']),
			"iTotalRecords" => $allRecord,
			"iTotalDisplayRecords" => $allRecordFilter,
			"aaData" => array()
		);

		while($permohonan_paket->nextRow())
		{
			$row = array();
			for ( $i=0 ; $i<count($aColumns) ; $i++ )
			{
				$this->load->library("librekamjejak");
				$statusApprove = $this->librekamjejak->statusPerencanaan($permohonan_paket->getField('STATUS'),$permohonan_paket->getField('STATUS_ID'));

				if($aColumns[$i] == "NAMA_KEBUTUHAN")
					$row[] = $statusApprove.' <small class="badge badge-info" style="font-size:10px">Unit Instalasi: <i>'.$permohonan_paket->getField('PEMBUAT').'</i></small><br>'.$permohonan_paket->getField($aColumns[$i]);
				else if($aColumns[$i] == "PERKIRAAN_BIAYA_HARGA")
					$row[] = numberToIna($permohonan_paket->getField($aColumns[$i]));
				else if($aColumns[$i] == "WAKTU_PENGGUNA_BARANGJASA" || $aColumns[$i] == "RENCANA_PENGADAAN")
					$row[] = getFormattedDateShort($permohonan_paket->getField($aColumns[$i]));
				else
					$row[] = $permohonan_paket->getField($aColumns[$i]);
			}

			$output['aaData'][] = $row;
		}

		echo json_encode( $output );
	}

	function permohonan_usulan_monitoring_validator2_json()
	{
		$this->load->library("kauth");  $userLogin = new kauth();
		$this->load->model("PermohonanPaket");
		$permohonan_paket = new PermohonanPaket();

		ini_set("memory_limit","500M");
		ini_set('max_execution_time', 5200);

		$reqStatus= $this->input->get("reqStatus");

		$aColumns = array("PERMOHONAN_PAKET_ANALISA_ID","POSTING","APPROVAL", "TAHUN_ANGGARAN", "NAMA_KEBUTUHAN", "KATEGORI_STR", "JENIS_BELANJA_STR", "PERKIRAAN_BIAYA_HARGA", "WAKTU_PENGGUNA_BARANGJASA", "RENCANA_PENGADAAN", "JENIS_BARANG_JASA_STR", "CARA_PENGADAAN_STR","PEMBUAT");
		$aColumnsAlias = array("PERMOHONAN_PAKET_ANALISA_ID","POSTING","APPROVAL", "TAHUN_ANGGARAN", "NAMA_KEBUTUHAN", "KATEGORI_STR", "JENIS_BELANJA_STR", "PERKIRAAN_BIAYA_HARGA", "WAKTU_PENGGUNA_BARANGJASA", "RENCANA_PENGADAAN", "JENIS_BARANG_JASA_STR", "CARA_PENGADAAN_STR","PEMBUAT");

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


			//Remove the last space / comma
			$sOrder = substr_replace( $sOrder, "", -2 );

			//Check if there is an order by clause
			if ( trim($sOrder) == "ORDER BY PERMOHONAN_PAKET_ANALISA_ID asc, PERMOHONAN_PAKET_ANALISA_ID asc" )
			{
				$sOrder = " ORDER BY A.PERMOHONAN_PAKET_ANALISA_ID ASC, A.CREATED_DATE ASC";
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

		$statement='';
		$searchJson='';

		$statement .= " AND (A.APPROVAL IN ('42','42251')) AND (A.VALIDATOR_2 = '".$this->USER_LOGIN_ID."') "; // 3: diteruskan

		$searchJson .= " AND (UPPER(A.NAMA_KEBUTUHAN) LIKE '%".strtoupper($_GET['sSearch'])."%' OR UPPER(A.TAHUN_ANGGARAN) LIKE '%".strtoupper($_GET['sSearch'])."%' OR UPPER(A.KATEGORI_STR) LIKE '%".strtoupper($_GET['sSearch'])."%' OR UPPER(A.JENIS_BELANJA_STR) LIKE '%".strtoupper($_GET['sSearch'])."%' OR UPPER(A.JENIS_BARANG_JASA_STR) LIKE '%".strtoupper($_GET['sSearch'])."%')";

		$allRecord = $permohonan_paket->getCountByParamsUsulan(array(), $statement);
		if($_GET['sSearch'] == "")
			$allRecordFilter = $allRecord;
		else
			$allRecordFilter = $permohonan_paket->getCountByParamsUsulan(array(), $statement.$searchJson);

		$permohonan_paket->selectByParamsUsulan(array(), $dsplyRange, $dsplyStart, $statement.$searchJson, $sOrder);

		/* Output */
		$output = array(
			"sEcho" => intval($_GET['sEcho']),
			"iTotalRecords" => $allRecord,
			"iTotalDisplayRecords" => $allRecordFilter,
			"aaData" => array()
		);

		while($permohonan_paket->nextRow())
		{
			$row = array();
			for ( $i=0 ; $i<count($aColumns) ; $i++ )
			{
				$this->load->library("librekamjejak");
				$statusApprove = $this->librekamjejak->statusPerencanaan($permohonan_paket->getField('STATUS'),$permohonan_paket->getField('STATUS_ID'));

				if($aColumns[$i] == "NAMA_KEBUTUHAN")
					$row[] = $statusApprove.' <small class="badge badge-info" style="font-size:10px">Unit Instalasi: <i>'.$permohonan_paket->getField('PEMBUAT').'</i></small><br>'.$permohonan_paket->getField($aColumns[$i]);
				else if($aColumns[$i] == "PERKIRAAN_BIAYA_HARGA")
					$row[] = numberToIna($permohonan_paket->getField($aColumns[$i]));
				else if($aColumns[$i] == "WAKTU_PENGGUNA_BARANGJASA" || $aColumns[$i] == "RENCANA_PENGADAAN")
					$row[] = getFormattedDateShort($permohonan_paket->getField($aColumns[$i]));
				else
					$row[] = $permohonan_paket->getField($aColumns[$i]);
			}

			$output['aaData'][] = $row;
		}

		echo json_encode( $output );
	}

	function permohonan_usulan_monitoring_approval_json()
	{
		$this->load->library("kauth");  $userLogin = new kauth();
		$this->load->model("PermohonanPaket");
		$permohonan_paket = new PermohonanPaket();

		ini_set("memory_limit","500M");
		ini_set('max_execution_time', 5200);

		$reqStatus= $this->input->get("reqStatus");

		$aColumns = array("PERMOHONAN_PAKET_ANALISA_ID","POSTING","APPROVAL", "TAHUN_ANGGARAN", "NAMA_KEBUTUHAN", "KATEGORI_STR", "JENIS_BELANJA_STR", "PERKIRAAN_BIAYA_HARGA", "WAKTU_PENGGUNA_BARANGJASA", "RENCANA_PENGADAAN", "JENIS_BARANG_JASA_STR", "CARA_PENGADAAN_STR","PEMBUAT");
		$aColumnsAlias = array("PERMOHONAN_PAKET_ANALISA_ID","POSTING","APPROVAL", "TAHUN_ANGGARAN", "NAMA_KEBUTUHAN", "KATEGORI_STR", "JENIS_BELANJA_STR", "PERKIRAAN_BIAYA_HARGA", "WAKTU_PENGGUNA_BARANGJASA", "RENCANA_PENGADAAN", "JENIS_BARANG_JASA_STR", "CARA_PENGADAAN_STR","PEMBUAT");

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


			//Remove the last space / comma
			$sOrder = substr_replace( $sOrder, "", -2 );

			//Check if there is an order by clause
			if ( trim($sOrder) == "ORDER BY PERMOHONAN_PAKET_ANALISA_ID asc, PERMOHONAN_PAKET_ANALISA_ID asc" )
			{
				$sOrder = " ORDER BY A.PERMOHONAN_PAKET_ANALISA_ID ASC, A.CREATED_DATE ASC";
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

		$statement='';
		$searchJson='';

		$statement .= " AND (A.APPROVAL IN ('51','51252')) AND (A.APPROVAL_1 = '".$this->USER_LOGIN_ID."') "; // 3: diteruskan

		$searchJson .= " AND (UPPER(A.NAMA_KEBUTUHAN) LIKE '%".strtoupper($_GET['sSearch'])."%' OR UPPER(A.TAHUN_ANGGARAN) LIKE '%".strtoupper($_GET['sSearch'])."%' OR UPPER(A.KATEGORI_STR) LIKE '%".strtoupper($_GET['sSearch'])."%' OR UPPER(A.JENIS_BELANJA_STR) LIKE '%".strtoupper($_GET['sSearch'])."%' OR UPPER(A.JENIS_BARANG_JASA_STR) LIKE '%".strtoupper($_GET['sSearch'])."%')";

		$allRecord = $permohonan_paket->getCountByParamsUsulan(array(), $statement);
		if($_GET['sSearch'] == "")
			$allRecordFilter = $allRecord;
		else
			$allRecordFilter = $permohonan_paket->getCountByParamsUsulan(array(), $statement.$searchJson);

		$permohonan_paket->selectByParamsUsulan(array(), $dsplyRange, $dsplyStart, $statement.$searchJson, $sOrder);

		/* Output */
		$output = array(
			"sEcho" => intval($_GET['sEcho']),
			"iTotalRecords" => $allRecord,
			"iTotalDisplayRecords" => $allRecordFilter,
			"aaData" => array()
		);

		while($permohonan_paket->nextRow())
		{
			$row = array();
			for ( $i=0 ; $i<count($aColumns) ; $i++ )
			{
				$this->load->library("librekamjejak");
				$statusApprove = $this->librekamjejak->statusPerencanaan($permohonan_paket->getField('STATUS'),$permohonan_paket->getField('STATUS_ID'));

				if($aColumns[$i] == "NAMA_KEBUTUHAN")
					$row[] = $statusApprove.' <small class="badge badge-info" style="font-size:10px">Unit Instalasi: <i>'.$permohonan_paket->getField('PEMBUAT').'</i></small><br>'.$permohonan_paket->getField($aColumns[$i]);
				else if($aColumns[$i] == "PERKIRAAN_BIAYA_HARGA")
					$row[] = numberToIna($permohonan_paket->getField($aColumns[$i]));
				else if($aColumns[$i] == "WAKTU_PENGGUNA_BARANGJASA" || $aColumns[$i] == "RENCANA_PENGADAAN")
					$row[] = getFormattedDateShort($permohonan_paket->getField($aColumns[$i]));
				else
					$row[] = $permohonan_paket->getField($aColumns[$i]);
			}

			$output['aaData'][] = $row;
		}

		echo json_encode( $output );
	}

	function permohonan_usulan_monitoring_approval_kpa_json()
	{
		$this->load->library("kauth");  $userLogin = new kauth();
		$this->load->model("PermohonanPaket");
		$permohonan_paket = new PermohonanPaket();

		ini_set("memory_limit","500M");
		ini_set('max_execution_time', 5200);

		$reqStatus= $this->input->get("reqStatus");

		$aColumns = array("PERMOHONAN_PAKET_ANALISA_ID","POSTING","APPROVAL", "TAHUN_ANGGARAN", "NAMA_KEBUTUHAN", "KATEGORI_STR", "JENIS_BELANJA_STR", "PERKIRAAN_BIAYA_HARGA", "WAKTU_PENGGUNA_BARANGJASA", "RENCANA_PENGADAAN", "JENIS_BARANG_JASA_STR", "CARA_PENGADAAN_STR","PEMBUAT");
		$aColumnsAlias = array("PERMOHONAN_PAKET_ANALISA_ID","POSTING","APPROVAL", "TAHUN_ANGGARAN", "NAMA_KEBUTUHAN", "KATEGORI_STR", "JENIS_BELANJA_STR", "PERKIRAAN_BIAYA_HARGA", "WAKTU_PENGGUNA_BARANGJASA", "RENCANA_PENGADAAN", "JENIS_BARANG_JASA_STR", "CARA_PENGADAAN_STR","PEMBUAT");

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


			//Remove the last space / comma
			$sOrder = substr_replace( $sOrder, "", -2 );

			//Check if there is an order by clause
			if ( trim($sOrder) == "ORDER BY PERMOHONAN_PAKET_ANALISA_ID asc, PERMOHONAN_PAKET_ANALISA_ID asc" )
			{
				$sOrder = " ORDER BY A.PERMOHONAN_PAKET_ANALISA_ID ASC, A.CREATED_DATE ASC";
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

		$statement='';
		$searchJson='';

		$statement .= " AND (A.APPROVAL IN ('52')) AND (A.APPROVAL_2 = '".$this->USER_LOGIN_ID."') "; // 3: diteruskan

		$searchJson .= " AND (UPPER(A.NAMA_KEBUTUHAN) LIKE '%".strtoupper($_GET['sSearch'])."%' OR UPPER(A.TAHUN_ANGGARAN) LIKE '%".strtoupper($_GET['sSearch'])."%' OR UPPER(A.KATEGORI_STR) LIKE '%".strtoupper($_GET['sSearch'])."%' OR UPPER(A.JENIS_BELANJA_STR) LIKE '%".strtoupper($_GET['sSearch'])."%' OR UPPER(A.JENIS_BARANG_JASA_STR) LIKE '%".strtoupper($_GET['sSearch'])."%')";

		$allRecord = $permohonan_paket->getCountByParamsUsulan(array(), $statement);
		if($_GET['sSearch'] == "")
			$allRecordFilter = $allRecord;
		else
			$allRecordFilter = $permohonan_paket->getCountByParamsUsulan(array(), $statement.$searchJson);

		$permohonan_paket->selectByParamsUsulan(array(), $dsplyRange, $dsplyStart, $statement.$searchJson, $sOrder);

		/* Output */
		$output = array(
			"sEcho" => intval($_GET['sEcho']),
			"iTotalRecords" => $allRecord,
			"iTotalDisplayRecords" => $allRecordFilter,
			"aaData" => array()
		);

		while($permohonan_paket->nextRow())
		{
			$row = array();
			for ( $i=0 ; $i<count($aColumns) ; $i++ )
			{
				$this->load->library("librekamjejak");
				$statusApprove = $this->librekamjejak->statusPerencanaan($permohonan_paket->getField('STATUS'),$permohonan_paket->getField('STATUS_ID'));

				if($aColumns[$i] == "NAMA_KEBUTUHAN")
					$row[] = $statusApprove.' <small class="badge badge-info" style="font-size:10px">Unit Instalasi: <i>'.$permohonan_paket->getField('PEMBUAT').'</i></small><br>'.$permohonan_paket->getField($aColumns[$i]);
				else if($aColumns[$i] == "PERKIRAAN_BIAYA_HARGA")
					$row[] = numberToIna($permohonan_paket->getField($aColumns[$i]));
				else if($aColumns[$i] == "WAKTU_PENGGUNA_BARANGJASA" || $aColumns[$i] == "RENCANA_PENGADAAN")
					$row[] = getFormattedDateShort($permohonan_paket->getField($aColumns[$i]));
				else
					$row[] = $permohonan_paket->getField($aColumns[$i]);
			}

			$output['aaData'][] = $row;
		}

		echo json_encode( $output );
	}

	function delete_usulan()
	{
		$this->load->model('PermohonanPaket');
		$this->load->model('PermohonanPaketAnalisaFile');

		$reqId		= $this->input->get('reqId');
		$permohonan_paket_analisa_file	= new PermohonanPaketAnalisaFile();
		$permohonan_paket_analisa_file->setField("PERMOHONAN_PAKET_ANALISA_ID", $reqId);
		if($permohonan_paket_analisa_file->deletePermohonan())
		{

			$permohonan_paket_select	= new PermohonanPaket();
			$permohonan_paket_select->selectByParams(array("A.PERMOHONAN_PAKET_ANALISA_ID" => $reqId));
			$permohonan_paket_select->firstRow();
			$permohonanId = $permohonan_paket_select->getField("PERMOHONAN_PAKET_ID");

			$permohonan_paket_coa_delete = new PermohonanPaket();
			$permohonan_paket_coa_delete->setField("PERMOHONAN_PAKET_ID", $permohonanId);
			$permohonan_paket_coa_delete->deleteCoa();

			$permohonan_paket	= new PermohonanPaket();
			$permohonan_paket->setField("PERMOHONAN_PAKET_ANALISA_ID", $reqId);
			$permohonan_paket->setField("PERMOHONAN_PAKET_ID", $permohonanId);
			$permohonan_paket->deleteAnalisa();  // Delete PERMOHONAN_PAKET & PERMOHONAN_PAKET_ANALISA & REKAM_JEJAK
		}
		echo "Data berhasil dihapus.";
	}

	function posting_usulan()
	{
		$usulanId =  $this->input->get('usulanId');
		$permohonanId =  $this->input->get('permohonanId');

		$reqToday = date('Y-m-d H:i:s');

		$this->load->model("PermohonanPaket");

		$ppa = new PermohonanPaket();
		$ppa->selectByParamsUsulan(array("A.PERMOHONAN_PAKET_ANALISA_ID" => coalesce($usulanId, 0), "A.CREATED_BY" => $this->USER_LOGIN_ID));
		$ppa->firstRow();

		$reqAlasan = $ppa->getField("NOTE");
		$reqKodeRUP = $ppa->getField("KODE_RUP");

		if ($reqKodeRUP == '') {
			$permohonan_paket_analisa = new PermohonanPaket();
			$kode = $this->setKodeRUP($usulanId,$this->USER_LOGIN_ID);
		} else {
			$permohonan_paket_analisa = new PermohonanPaket();
			$kode = $reqKodeRUP;
		}

		$permohonan_paket_analisa->setField('KODE_RUP', $kode);
		$permohonan_paket_analisa->setField("PERMOHONAN_PAKET_ANALISA_ID", $usulanId);
		$permohonan_paket_analisa->setField("POSTING", 1);
		$permohonan_paket_analisa->setField("APPROVAL", 3);
		$permohonan_paket_analisa->setField("POSTING_BY", $this->USER_LOGIN_ID);
		$permohonan_paket_analisa->setField("USER_STATUS", "CURRENT_DATE");

		if($permohonan_paket_analisa->posting_analisa()){
			// Insert Rekam Jejak
			$this->load->library("librekamjejak");
			$this->librekamjejak->insertRJ('2',$reqAlasan,'null',$permohonanId,'2');
			// End Insert Rekam Jejak
			echo "Usulan Berhasil Diteruskan";
		}
		else {
			echo "Usulan Gagal Diteruskan";
		}

	}

	function posting_usulan_post()
	{

		$data    = array();
		$gabung  = '';
		$id      = $this->input->post('chkId');

		$dataId = explode(",",$id);

		// echo "<pre>"; print_r($dataId); die;
		$total = 0;
		foreach ($dataId as $key => $value) {

			$usulanId =  $value;

			$reqToday = date('Y-m-d H:i:s');

			$this->load->model("PermohonanPaket");

			$ppa = new PermohonanPaket();
			$ppa->selectByParamsUsulan(array("A.PERMOHONAN_PAKET_ANALISA_ID" => coalesce($usulanId, 0), "A.CREATED_BY" => $this->USER_LOGIN_ID));
			$ppa->firstRow();

			$reqAlasan = $ppa->getField("NOTE");
			$reqKodeRUP = $ppa->getField("KODE_RUP");
			$permohonanId = $ppa->getField("PERMOHONAN_PAKET_ID");

			if ($reqKodeRUP == '') {
				$permohonan_paket_analisa = new PermohonanPaket();
				$kode = $this->setKodeRUP($usulanId,$this->USER_LOGIN_ID);
			} else {
				$permohonan_paket_analisa = new PermohonanPaket();
				$kode = $reqKodeRUP;
			}

			$permohonan_paket_analisa->setField('KODE_RUP', $kode);
			$permohonan_paket_analisa->setField("PERMOHONAN_PAKET_ANALISA_ID", $usulanId);
			$permohonan_paket_analisa->setField("POSTING", 1);
			$permohonan_paket_analisa->setField("APPROVAL", 3);
			$permohonan_paket_analisa->setField("POSTING_BY", $this->USER_LOGIN_ID);
			$permohonan_paket_analisa->setField("USER_STATUS", "CURRENT_DATE");

			if($permohonan_paket_analisa->posting_analisa()){
				$total++;
				// Insert Rekam Jejak
				$this->load->library("librekamjejak");
				$this->librekamjejak->insertRJ('2',$reqAlasan,'null',$permohonanId,'2');
				// End Insert Rekam Jejak
			} else {
			}
		}

		if ($total > 0) {
		  $status   .= 'SUKSES';
		  $message  .= 'Usulan Berhasil Diteruskan';
		} else {
		  $status   .= 'GAGAL';
		  $message  .= 'Usulan Gagal Diteruskan';
		}
		echo json_encode(array('respon' => $status, 'message' => $message));
	}

	function delete_usulan_post()
	{
		$this->load->model('PermohonanPaket');
		$this->load->model('PermohonanPaketAnalisaFile');

		$data    = array();
		$gabung  = '';
		$id      = $this->input->post('chkId');

		$dataId = explode(",",$id);

		// echo "<pre>"; print_r($dataId); die;
		$total = 0;
		foreach ($dataId as $key => $value) {
			$reqId		= $value;
			$permohonan_paket_analisa_file	= new PermohonanPaketAnalisaFile();
			$permohonan_paket_analisa_file->setField("PERMOHONAN_PAKET_ANALISA_ID", $reqId);
			if($permohonan_paket_analisa_file->deletePermohonan())
			{

				$permohonan_paket_select	= new PermohonanPaket();
				$permohonan_paket_select->selectByParams(array("A.PERMOHONAN_PAKET_ANALISA_ID" => $reqId));
				$permohonan_paket_select->firstRow();
				$permohonanId = $permohonan_paket_select->getField("PERMOHONAN_PAKET_ID");

				$permohonan_paket_coa_delete = new PermohonanPaket();
				$permohonan_paket_coa_delete->setField("PERMOHONAN_PAKET_ID", $permohonanId);
				$permohonan_paket_coa_delete->deleteCoa();

				$permohonan_paket	= new PermohonanPaket();
				$permohonan_paket->setField("PERMOHONAN_PAKET_ANALISA_ID", $reqId);
				$permohonan_paket->setField("PERMOHONAN_PAKET_ID", $permohonanId);
				$permohonan_paket->deleteAnalisa();  // Delete PERMOHONAN_PAKET & PERMOHONAN_PAKET_ANALISA & REKAM_JEJAK
				$total++;
			}
		}

		if ($total > 0) {
		  $status   .= 'SUKSES';
		  $message  .= 'Usulan Berhasil Dihapus';
		} else {
		  $status   .= 'GAGAL';
		  $message  .= 'Usulan Gagal Dihapus, Silahkan dicoba kembali';
		}
		echo json_encode(array('respon' => $status, 'message' => $message));
	}

	function setKodeRUP($usulanId,$userloginid)
	{
		$this->load->model("UsersBase");
		$user_login = new UsersBase();
		$user_login->selectByParams(array("A.USER_LOGIN_ID" => $userloginid));
		$user_login->firstRow();
		$kode = $user_login->getField("KODE");


		$this->load->model(array("PermohonanPaket","Queryfree"));
		$ppa = new PermohonanPaket();
		$ppa->selectByParams(array("A.PERMOHONAN_PAKET_ANALISA_ID" => coalesce($usulanId, 0), "A.CREATED_BY" => $userloginid));
		$ppa->firstRow();
		$tahunAnggaran = $ppa->getField("TAHUN_ANGGARAN");

		$kodeRUP = $this->generateKodeRUP($tahunAnggaran,$kode);

		// cek kode RUP Existing
		$cekRUP = new Queryfree();
        $cekRUP->selectByParams("SELECT PERMOHONAN_PAKET_ID FROM PERMOHONAN_PAKET WHERE KODE_RUP = '$kodeRUP'");
        $totalRUP = $cekRUP->countRow();

        if ($totalRUP > 0) { // kalau kode RUP udah ada di database
        	for ($i=0; $i < $totalRUP ; $i++) {
        		$kodeRUP = $this->generateKodeRUP($tahunAnggaran,$kode);
        		// cek kode RUP Existing
				$cekRUP = new Queryfree();
		        $cekRUP->selectByParams("SELECT PERMOHONAN_PAKET_ID FROM PERMOHONAN_PAKET WHERE KODE_RUP = '$kodeRUP'");
		        $totalRUP = $cekRUP->countRow();

        		if ($totalRUP > 0) {
        			$i--;
        		} else {
					// echo $totalRUP.'-'.$kodeRUP; die;
        			return $kodeRUP;
        		}
        	}
        } else {
			// echo $totalRUP.'-'.$kodeRUP; die;
			return $kodeRUP;
        }


	}

	function generateKodeRUP($tahunAnggaran,$kode)
	{
		$tahunAnggaran = str_replace(" ","", $tahunAnggaran);
		if ($tahunAnggaran == '2024') { // Pengecualian, karena ada perubahan logik pengkodean RUP di bulan November 2024, khusus 2024 mengikuti nomor terakhir, tahun berikut nya sesuai logik di else (bawah)
			//Get Nomor Urut
			$getNomorUrut = new Queryfree();
	        $getNomorUrut->selectByParams("SELECT a.KODE_RUP::int FROM (
											SELECT SUBSTRING (KODE_RUP, 11, 5) KODE_RUP
											FROM PERMOHONAN_PAKET a  WHERE a.TAHUN_ANGGARAN = '$tahunAnggaran' AND KODE_RUP != '') a
											ORDER BY KODE_RUP DESC
											LIMIT 1");
			$getNomorUrut->firstRow();
			$ko = $getNomorUrut->getField("KODE_RUP");
			// $exNo = explode(".",$ko);
			$noRut = $ko + 1;

			$kodeRUP = str_replace(" ","",substr($tahunAnggaran,2,4).'.RP.'.$kode.'.'.generateZero($noRut, 3, 0));
		} else { // selain tahun anggaran 2024
			//Get Nomor Urut
			$getNomorUrut = new Queryfree();
	        // $getNomorUrut->selectByParams("SELECT count(TAHUN_ANGGARAN) + 1 total FROM PERMOHONAN_PAKET a  WHERE a.TAHUN_ANGGARAN = '$tahunAnggaran' AND KODE_RUP != ''");
	        // 09 Januari 2025
	        $getNomorUrut->selectByParams("SELECT
											    CASE
											        WHEN MAX(b.nomor + 1) IS NULL THEN 1
											        ELSE MAX(b.nomor + 1)
											    END AS total
											FROM (
											    SELECT a.nomor::int
											    FROM (
											        SELECT SUBSTRING(kode_rup,11) AS nomor
											        FROM PERMOHONAN_PAKET a
											        WHERE a.TAHUN_ANGGARAN = '$tahunAnggaran'
											          AND KODE_RUP != ''
											          AND SUBSTRING(kode_rup,11) ~ '^[0-9]+$'  -- <== hanya ambil yang berupa angka
											    ) a
											) b
											");
			$getNomorUrut->firstRow();
			$noRut = $getNomorUrut->getField("total");

			$kodeRUP = str_replace(" ","",substr($tahunAnggaran,2,4).'.RP.'.$kode.'.'.generateZero($noRut, 3, 0));
		}
		return $kodeRUP;
	}

	function tunjuk_pic()
	{
		$this->load->model("PermohonanPaket");

		$permohonan_paket = new PermohonanPaket();

		/* json set variable */
		$reqId =  $this->input->post('reqId');
		$reqPIC =  $this->input->post('reqPIC');

		$permohonan_paket = new PermohonanPaket();
		$permohonan_paket->setField("PERMOHONAN_PAKET_ANALISA_ID", $reqId);
		$permohonan_paket->setField("PIC", $reqPIC);
		$permohonan_paket->setField("PIC_BY", $this->USER_NAMA);

		if($permohonan_paket->tunjuk_pic())
			echo "PIC Berhasil Ditunjuk";
		else
			echo "PIC Gagal Ditunjuk";
	}

	function kembali_permohonan()
	{

		/**
		MATRIX APPROVAL PERENCANAAN

		0:belom, 						// unit
		2:tolak_verifikator  				// unit

		----- Approved
		1:approve, 					// admin_rup atau ppkom/perencana


		----- Verifikator
		3:teruskan_ke_verifikator 		// verifikator melihat Kategori ubah status 2/51=Reguler atau 2/41=Insidental
		 3241:tolak_dari_validator1
		 3251:tolak_dari_approval1


		----- Validator
		41:teruskan_ke_validator1 		// validator 1 ubah status 3241/42
		 41242:tolak_dari_validator2

		42:teruskan_ke_validator2 		// validator 2 ubah status 41242/51
		 42251:tolak_dari_approval1


		----- Approval
		51:teruskan_ke_approval1 		// pkpa melihat Nilai ubah status  3251/1= <2M_&_Reguler atau 42251/52 = >2M_&_Reguler
		 51252:tolak_dari_approval2

		52:teruskan_ke_approval2 		// kpa ubah status 51252/1 dan Tentukan PPKom
		**/
		$this->load->model("PermohonanPaket");

		$permohonan_paket_analisa = new PermohonanPaket();

		/* json set variable */
		$reqId =  $this->input->post('reqPermohonanPaketAnalisaId');
		$reqPermohonanPaketId =  $this->input->post('reqPermohonanPaketId');
		$reqAlasan =  $this->input->post('reqAlasan');
		$reqApprove =  $this->input->post('reqApprove'); //
		$reqPIC =  $this->input->post('reqPIC');

		$permohonan_paket_analisa = new PermohonanPaket();
		$permohonan_paket_analisa->setField("PERMOHONAN_PAKET_ANALISA_ID", $reqId);
		$permohonan_paket_analisa->setField("APPROVAL",$reqApprove );
		$reqToday = date('Y-m-d H:i:s');

		if ($reqApprove == '2' ||
			$reqApprove == '3241' ||
			$reqApprove == '3251' ||
			$reqApprove == '41242' ||
			$reqApprove == '42251' ||
			$reqApprove == '51252') { // Tolak
			$permohonan_paket_analisa->setField("ALASAN_TOLAK", $reqAlasan);
			$permohonan_paket_analisa->setField("ALASAN_TOLAK_BY", $this->USER_LOGIN_ID);
		} else {
			$permohonan_paket_analisa->setField("ALASAN_TOLAK", "");
			$permohonan_paket_analisa->setField("ALASAN_TOLAK_BY", $this->USER_LOGIN_ID);
		}

		if($permohonan_paket_analisa->kembali_permohonan()) {

			switch ($reqApprove) {
				case '2': // Tolak
				case '3241': // Tolak
				case '3251': // Tolak
				case '41242': // Tolak
				case '42251': // Tolak
				case '51252': // Tolak
					$this->load->library("librekamjejak");
					$this->librekamjejak->insertRJ('102',$reqAlasan,'null',$reqPermohonanPaketId,'102');
					break;

				case '41':
				case '51':
					$this->load->library("librekamjejak");
					if ($reqApprove == '51' && $this->USER_TYPE_ID == '17') {
						$this->librekamjejak->insertRJ('103',$reqAlasan,'null',$reqPermohonanPaketId,'103');
					} else if ($reqApprove == '51' && $this->USER_TYPE_ID == '22') {
						$this->librekamjejak->insertRJ('105',$reqAlasan,'null',$reqPermohonanPaketId,'105');
					} else { // 41
						$this->librekamjejak->insertRJ('103',$reqAlasan,'null',$reqPermohonanPaketId,'103');
					}
					break;

				case '42':
					$this->load->library("librekamjejak");
					$this->librekamjejak->insertRJ('104',$reqAlasan,'null',$reqPermohonanPaketId,'104');
					break;

				case '52':
					$this->load->library("librekamjejak");
					$this->librekamjejak->insertRJ('106',$reqAlasan,'null',$reqPermohonanPaketId,'106');
					break;

				case '1':
					$this->load->library("librekamjejak");

					$this->load->model('Userlogin');
					$cekNama = new Userlogin();
					$cekNama->selectByParams(array('USER_LOGIN_ID' => $reqPIC));
					$cekNama->firstRow();
					$PICNama = $cekNama->getField("USER_NAMA").' ('.$cekNama->getField("USER_JABATAN").')';

					if ($this->APPROVAL_UNIT == '1') { // PKPA
						$this->librekamjejak->insertRJ('106',$reqAlasan.' - PPKom: '.$PICNama,'null',$reqPermohonanPaketId,'106');
						$reqNilai =  $this->input->post('reqNilai');

						// if ($reqNilai < 2000000000) {
							// update permohonan_paket field user_login_id
							$permohonan_paket_update = new PermohonanPaket();
							$permohonan_paket_update->setField("PERMOHONAN_PAKET_ID", $reqPermohonanPaketId);
							$permohonan_paket_update->setField("USER_LOGIN_ID", $reqPIC);
							$permohonan_paket_update->setField("LAST_CREATE_USER", $this->USER_NAMA);
							$permohonan_paket_update->setField("LAST_CREATE_DATE", $reqToday);
							$permohonan_paket_update->updatePermohonanPIC();
						// }

					} else { // KPA
						$this->librekamjejak->insertRJ('107',$reqAlasan.' - PPKom: '.$PICNama,'null',$reqPermohonanPaketId,'107');
						// update permohonan_paket field user_login_id
						$permohonan_paket_update = new PermohonanPaket();
						$permohonan_paket_update->setField("PERMOHONAN_PAKET_ID", $reqPermohonanPaketId);
						$permohonan_paket_update->setField("USER_LOGIN_ID", $reqPIC);
						$permohonan_paket_update->setField("LAST_CREATE_USER", $this->USER_NAMA);
						$permohonan_paket_update->setField("LAST_CREATE_DATE", $reqToday);
						$permohonan_paket_update->updatePermohonanPIC();
					}


					break;

				default:
					// code...
					break;
			}

			echo "Data Berhasil Disimpan";
		} else {
			echo "Data Gagal Disimpan";
		}
	}

	function getMataAnggaran()
	{
		$this->load->model(array("Rka"));

		$reqUrl = $this->input->get("reqUrl");
		$reqDate = $this->input->get("reqDate");
		$reqDepartment = $this->input->get("reqDepartment");
		$reqDepartment = $this->DEPARTMENT;
		$reqMT = $this->input->get("reqMT");

		$getRka = new Rka();
        $getRka->selectByParams(array("START_DATE_YEAR" => $reqDate ,"SEGMENT2_DESC" => $reqDepartment, "SEGMENT3_DESC" => $reqMT));
        // echo $getRka->query;
        $html = '<option value="">-- Pilih kegiatan --</option>';
        if ($getRka->countRow() > 0) {
        	while($getRka->nextRow())
            {
        			$html .= '<option value="'.$getRka->getField("SEGMENT4_DESC").'" data-integration-import-rka-budget-id="'.$getRka->getField("INTEGRATION_IMPORT_RKA_BUDGET_ID").'" data-budget-amt="'.$getRka->getField("BUDGET_AMT").'" data-remain-amt="'.$getRka->getField("REMAIN_AMT").'" data-segment5-desc="'.$getRka->getField("SEGMENT5_DESC").'"  >'.$getRka->getField("SEGMENT4_DESC").' - '.$getRka->getField("SEGMENT5_DESC").'</option>';
        	}
        }
        $arrJson["PESAN"] = $html;
		echo json_encode($arrJson);

        // OLD
		// $this->load->library("libapi");
  //       $libapi = new libapi();
  //       $a = $libapi->getAnggaran($reqUrl,$reqDate,$reqDepartment);
  //       $dataMataAnggaran = $a->results->data;
  //       $arrMataAnggaran = $dataMataAnggaran;
  //       // Group data dulu
  //       $libapi2 = new libapi();
  //       $b = $libapi2->groupData($arrMataAnggaran);
  //       // End Group data dulu
  //       $data20 = array();
  //       foreach ($b as $key => $value) {
  //       	if ($reqMT == $key) {
		//         $data20[$key] = $value;
  //       	}
  //       }
        // if (count($data20) > 0) {
        // 	foreach ($data20 as $key => $value) {
        // 		foreach ($value as $key2 => $value2) {
        // 			$html .= '<option value="'.$value2->kegiatan.'" data-department-code="'.$value2->department_code.'" data-kode-mata-anggaran="'.$value2->kode_mata_anggaran.'" data-tipe-transaksi="'.$value2->tipe_transaksi.'" data-kode-kegiatan="'.$value2->kode_kegiatan.'" data-sumber-dana="'.$value2->sumber_dana.'" data-total-budget="'.$value2->total_budget.'" data-budget-remaining="'.$value2->budget_remaining.'">'.$value2->kegiatan.'</option>';
        // 		}
        // 	}
        // }
		// $arrJson["PESAN"] = $reqUrl.'-'.$reqDate.'-'.$reqDepartment.'-'.$reqMT;
		// $arrJson["PESAN"] = $html;
		// echo json_encode($arrJson);
	}

	function comboKomoditas()
	{
		$this->load->model('Komoditas');

		$komoditas = new Komoditas();

		$komoditas->selectByParams(array('A.AKTIF' => "1"));

		$i = 0;
		while($komoditas->nextRow())
		{
			$arr_json[$i]['id']		= $komoditas->getField("KOMODITAS_ID");
			$arr_json[$i]['text']	= $komoditas->getField("KOMODITAS_NAMA");
			$i++;
		}

		echo json_encode($arr_json);
	}

	function comboAnalisaKebutuhan()
	{
		$this->load->model('Analisakebutuhan');

		$analisa_kebutuhan = new Analisakebutuhan();

		$analisa_kebutuhan->selectByParams(array('A.AKTIF' => "1"));

		$i = 0;
		while($analisa_kebutuhan->nextRow())
		{
			$arr_json[$i]['id']		= $analisa_kebutuhan->getField("ANALISA_KEBUTUHAN_ID");
			$arr_json[$i]['text']	= $analisa_kebutuhan->getField("AK_NAMA");
			$i++;
		}

		echo json_encode($arr_json);
	}

	function comboAnalisaPasar()
	{
		$this->load->model('Analisapasar');

		$analisa_pasar = new Analisapasar();

		$analisa_pasar->selectByParams(array('A.AKTIF' => "1"));

		$i = 0;
		while($analisa_pasar->nextRow())
		{
			$arr_json[$i]['id']		= $analisa_pasar->getField("ANALISA_PASAR_ID");
			$arr_json[$i]['text']	= $analisa_pasar->getField("AP_NAMA");
			$i++;
		}

		echo json_encode($arr_json);
	}

	function comboAnalisaPaketJenis()
	{
		$this->load->model('PaketJenis');

		$paket_jenis = new PaketJenis();

		$paket_jenis->selectByParams(array('AKTIF' => "1"));

		$i = 0;
		while($paket_jenis->nextRow())
		{
			$arr_json[$i]['id']		= $paket_jenis->getField("PAKET_JENIS_ID");
			$arr_json[$i]['text']	= $paket_jenis->getField("NAMA");
			$i++;
		}

		echo json_encode($arr_json);
	}

	function comboAnalisaKategori()
	{
		$this->load->model('Permohonanpaketanalisakategori');

		$analisa_kategori = new Permohonanpaketanalisakategori();

		$analisa_kategori->selectByParams();

		$i = 0;
		while($analisa_kategori->nextRow())
		{
			$arr_json[$i]['id']		= $analisa_kategori->getField("PERMOHONAN_PAKET_ANALISA_KATEGORI_ID");
			$arr_json[$i]['text']	= $analisa_kategori->getField("NAMA");
			$i++;
		}

		echo json_encode($arr_json);
	}

	function PermohonanPaketAnalisaJenisBelanja()
	{
		$this->load->model('Permohonanpaketanalisajenisbelanja');

		$analisa_jenis_belanja = new Permohonanpaketanalisajenisbelanja();

		$analisa_jenis_belanja->selectByParams();

		$i = 0;
		while($analisa_jenis_belanja->nextRow())
		{
			$arr_json[$i]['id']		= $analisa_jenis_belanja->getField("PERMOHONAN_PAKET_ANALISA_JENIS_BELANJA_ID");
			$arr_json[$i]['text']	= $analisa_jenis_belanja->getField("NAMA");
			$i++;
		}

		echo json_encode($arr_json);
	}

	public function comboMonth()
	{
		$this->load->model("PermohonanPaket");
		$setMonth = new PermohonanPaket();
		$setMonth->selectMonth();

		$i = 0;
		while($setMonth->nextRow())
		{
			$arr_json[$i]['id']		= $setMonth->getField("MONTH_ANGKA");
			$arr_json[$i]['text']	= $setMonth->getField("MONTH_INA");
			$i++;
		}

		echo json_encode($arr_json);
	}

	public function comboYear()
	{
		$yearOld = date('Y') - 1;
		$year = date('Y');

		$yearNext[] = $yearOld;
		for ($i=1; $i < 5 ; $i++) {
			$yearNext[] = $yearOld + $i;
		}

		$variable = array('2000','2001');
		$i = 0;
		foreach ($yearNext as $i => $value) {
			$arr_json[$i]['id']		= $value;
			$arr_json[$i]['text']	= $value;
		}

		echo json_encode($arr_json);
	}

	public function ontobeapproved()
	{
		$data    = array();
		$gabung  = '';
		$id      = $this->input->post('chkId');

		$this->load->model("PermohonanPaket");
		$permohonan_paket_update = new PermohonanPaket();
		$permohonan_paket_update->setField('UPDATED_BY', $this->USER_LOGIN_ID);
		$permohonan_paket_update->setField("PERMOHONAN_PAKET_ANALISA_ID", $id);

		$exct = $permohonan_paket_update->updatePermohonanApprove();

		$message       = "";
		$status        = "";
		if ($exct) {
		  $status   .= 'SUKSES';
		  $message  .= ' Data berhasil di proses.';
		} else {
		  $status   .= 'GAGAL';
		  $message  .= ' Data gagal di proses.';
		}
		echo json_encode(array('respon' => $status, 'message' => $message));
	}

	public function ontodraft()
	{
		$data    = array();
		$gabung  = '';
		$id      = $this->input->post('chkId');

		$this->load->model("PermohonanPaket");
		$permohonan_paket_update = new PermohonanPaket();
		$permohonan_paket_update->setField('UPDATED_BY', $this->USER_LOGIN_ID);
		$permohonan_paket_update->setField("PERMOHONAN_PAKET_ANALISA_ID", $id);

		$exct = $permohonan_paket_update->updatePermohonanDraft();

		$message       = "";
		$status        = "";
		if ($exct) {
		  $status   .= 'SUKSES';
		  $message  .= ' Data berhasil di proses.';
		} else {
		  $status   .= 'GAGAL';
		  $message  .= ' Data gagal di proses.';
		}
		echo json_encode(array('respon' => $status, 'message' => $message));
	}

	public function ontorup()
	{
		$data    = array();
		$gabung  = '';
		$id      = $this->input->post('chkId');
		$ex 	 = explode(',',$id);

		$total = 0;
		foreach ($ex as $key => $value) {

			$this->load->model("PermohonanPaket");
			$permohonan_paket_update = new PermohonanPaket();

			// $kode = substr(date('Y'),2,4).generateZero($value, 5, 0);
			// // echo $kode.'<br>';
			// $permohonan_paket_update->setField('KODE_RUP', $kode);
			$permohonan_paket_update->setField('UPDATED_BY', $this->USER_LOGIN_ID);
			$permohonan_paket_update->setField("PERMOHONAN_PAKET_ANALISA_ID", $value);
			$exct = $permohonan_paket_update->updatePermohonanRUP();

			$permohonan_paket_select	= new PermohonanPaket();
			$permohonan_paket_select->selectByParams(array("A.PERMOHONAN_PAKET_ANALISA_ID" => $value));
			$permohonan_paket_select->firstRow();
			$permohonanId = $permohonan_paket_select->getField("PERMOHONAN_PAKET_ID");

			// Insert Rekam Jejak
			$this->load->library("librekamjejak");
			$this->librekamjejak->insertRJ('3','','null',$permohonanId,'3');
			// End Insert Rekam Jejak

			if ($exct) {
				$total ++;
			} else { }
		}


		$message       = "";
		$status        = "";
		if ($exct > 0) {
		  $status   .= 'SUKSES';
		  $message  .= ' Data berhasil di proses.';
		} else {
		  $status   .= 'GAGAL';
		  $message  .= ' Data gagal di proses.';
		}
		echo json_encode(array('respon' => $status, 'message' => $message));
	}


}
?>
