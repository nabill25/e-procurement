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

class paket_lelang_tambah_negosiasi_item_json extends CI_Controller {

	function __construct() {
		parent::__construct(); $this->db->query("SET TIME ZONE 'Asia/Jakarta'");

		//kauth
		if (!$this->kauth->getInstance()->hasIdentity())
		{ }

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

	function json() 
	{
		$this->load->model("Paketnegosiasiitem");
		$negosiasiitem = new Paketnegosiasiitem();
		
		$reqId = $this->input->get("reqId"); // PAKET ID
		$reqSearch = $this->input->post("reqSearch");
		
		$aColumns 			= array('PAKET_NEGOSIASI_ITEM_ID','PAKET_ID','URAIAN','VOLUME','SATUAN_VOLUME','DURASI','SATUAN_DURASI','HARGA_SATUAN','JUMLAH_HARGA','NILAI_PENAWARAN','JUMLAH_PENAWARAN','PERSENTASE_PENAWARAN','NILAI_NEGOSIASI','JUMLAH_NEGOSIASI','PERSENTASE_NEGOSIASI','STATUS_NEGO');
		$aColumnsAlias		= array('PAKET_NEGOSIASI_ITEM_ID','PAKET_ID','URAIAN','VOLUME','SATUAN_VOLUME','DURASI','SATUAN_DURASI','HARGA_SATUAN','JUMLAH_HARGA','NILAI_PENAWARAN','JUMLAH_PENAWARAN','PERSENTASE_PENAWARAN','NILAI_NEGOSIASI','JUMLAH_NEGOSIASI','PERSENTASE_NEGOSIASI','STATUS_NEGO');
		
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
			
			if ( trim($sOrder) == "ORDER BY PAKET_NEGOSIASI_ITEM_ID desc" )
			{
				$sOrder = " ORDER BY PAKET_NEGOSIASI_ITEM_ID DESC ";
				 
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
		
		$statement = "AND PAKET_ID = ".$reqId." AND (UPPER(URAIAN) LIKE '%".strtoupper($_GET['sSearch'])."%')";
		$allRecord = $negosiasiitem->getCountByParams(array(), " AND PAKET_ID = ".$reqId."", $sOrder);
		if($_GET['sSearch'] == "")
			$allRecordFilter = $allRecord;
		else	
			$allRecordFilter =  $negosiasiitem->getCountByParams(array(), " AND PAKET_ID = ".$reqId."", $sOrder);

		$negosiasiitem->selectByParams(array(), $dsplyRange, $dsplyStart, $statement, $sOrder);
		
		$output = array(
			"sEcho" => intval($_GET['sEcho']),
			"iTotalRecords" => $allRecord,
			"iTotalDisplayRecords" => $allRecordFilter,
			"aaData" => array()
		);
		
		while($negosiasiitem->nextRow())		
		{		
			$row = array();		
			for ( $i=0 ; $i<count($aColumns) ; $i++ )		
			{	
				if($aColumns[$i]=='NO') {
					$row[] = $number;
				} elseif($aColumns[$i]=='STATUS_NEGO') { 
					if ($negosiasiitem->getField(trim($aColumns[$i])) == '0') {
						$row[] = '<span class="badge badge-primary">Input</span>';
					} elseif ($negosiasiitem->getField(trim($aColumns[$i])) == '1') {
						$row[] = '<span class="badge badge-success">Approved</span>';
					} elseif ($negosiasiitem->getField(trim($aColumns[$i])) == '2') {
						$row[] = '<span class="badge badge-warning">Dikirim ke penyedia</span>';
					} elseif ($negosiasiitem->getField(trim($aColumns[$i])) == '3') {
						$row[] = '<span class="badge badge-danger">Ditolak penyedia</span>';
					} else {
						$row[] = '-';
					} 
				} elseif($aColumns[$i]=='URAIAN') { 
					$row[] = $negosiasiitem->getField(trim($aColumns[$i]));
				} elseif($aColumns[$i]=='VOLUME') { 
					$row[] = $negosiasiitem->getField(trim($aColumns[$i])).' '.$negosiasiitem->getField('SATUAN_VOLUME');
				} elseif($aColumns[$i]=='PERSENTASE_NEGOSIASI' || $aColumns[$i]=='PERSENTASE_PENAWARAN') { 
					$row[] = $negosiasiitem->getField(trim($aColumns[$i])).'';
				} elseif($aColumns[$i]=='DURASI') { 
					$row[] = $negosiasiitem->getField(trim($aColumns[$i])).' '.$negosiasiitem->getField('SATUAN_DURASI');
				} elseif($aColumns[$i]=='HARGA_SATUAN' || $aColumns[$i]=='JUMLAH_HARGA' || $aColumns[$i]=='NILAI_PENAWARAN' || $aColumns[$i]=='JUMLAH_PENAWARAN' || $aColumns[$i]=='JUMLAH_NEGOSIASI') { 
					$row[] = number_format($negosiasiitem->getField(trim($aColumns[$i])),0,',','.');
				} elseif($aColumns[$i]=='NILAI_NEGOSIASI') { 
					$row[] = '<input type="hidden" value="'.$negosiasiitem->getField('PAKET_NEGOSIASI_ITEM_ID').'" name="idnego[]" ><input class="form-control" name="nilainegosiasi[]" type="text" value="'.number_format($negosiasiitem->getField(trim($aColumns[$i])),0,',','.').'">';
				} else {	
					$row[] = $negosiasiitem->getField(trim($aColumns[$i]));
				}
			}
			$output['aaData'][] = $row;
		}
		echo json_encode( $output );	
	}

	function deleteFileAnalisa() 
	{
		$this->load->model('Paketnegosiasiitem');
		$reqId		= $this->input->get('reqId');
		$negosiasiitem	= new Paketnegosiasiitem();
		$negosiasiitem->setField("PAKET_NEGOSIASI_ITEM_ID", $reqId);
		$negosiasiitem->deleteByID();
		
		echo "Data berhasil disimpan.";
	}

	function randomKode($length = 4) {
	    $chars = 'ABCDEFGHJKMNPQRSTUVWXYZ123456789';
	    $result = '';

	    for ($i = 0; $i < $length; $i++) {
	        $result .= $chars[random_int(0, strlen($chars) - 1)];
	    }

	    return $result;
	}

	function edit()
	{
		$this->load->model(array("Paketnegosiasiitem"));
		$negoitem = new Paketnegosiasiitem(); 

		$reqId = $this->input->post("reqId"); // Negosiasi Item ID
		$reqUraian = $this->input->post("reqUraian");
		$reqVolume = $this->input->post("reqVolume");
		$reqSatuanVolume = $this->input->post("reqSatuanVolume");
		$reqDurasi = $this->input->post("reqDurasi");
		$reqSatuanDurasi = $this->input->post("reqSatuanDurasi");
		$reqHargaSatuan = $this->input->post("reqHargaSatuan");
		$reqJumlahHarga = $this->input->post("reqJumlahHarga");
		$reqNilaiPenawaran = $this->input->post("reqNilaiPenawaran");
		$reqJumlahPenawaran = $this->input->post("reqJumlahPenawaran");
		$reqNilaiNegosiasi = $this->input->post("reqNilaiNegosiasi");
		$reqJumlahNegosiasi = $this->input->post("reqJumlahNegosiasi");

		$negoitem->setField('PAKET_NEGOSIASI_ITEM_ID', $reqId);
		$negoitem->setField('URAIAN', $reqUraian);
		$negoitem->setField('VOLUME', $reqVolume);
		$negoitem->setField('SATUAN_VOLUME', $reqSatuanVolume);
		$negoitem->setField('DURASI', $reqDurasi);
		$negoitem->setField('SATUAN_DURASI', $reqSatuanDurasi);
		$negoitem->setField('HARGA_SATUAN', dotToNo($reqHargaSatuan));
		$negoitem->setField('JUMLAH_HARGA', dotToNo($reqJumlahHarga));
		$negoitem->setField('NILAI_PENAWARAN', dotToNo($reqNilaiPenawaran));
		$negoitem->setField('JUMLAH_PENAWARAN', dotToNo($reqJumlahPenawaran));
		$negoitem->setField('NILAI_NEGOSIASI', dotToNo($reqNilaiNegosiasi));
		$negoitem->setField('JUMLAH_NEGOSIASI', dotToNo($reqJumlahNegosiasi));
		$negoitem->setField('UPDATED_BY', $this->USER_LOGIN_ID);
		$update = $negoitem->update();
 	
 		if ($update) {
			echo "Data berhasil diupdate";
 		} else {
			echo "Data gagal diupdate";
 		}
	}

	function teruskan()
	{
		$this->load->model(array("Paketnegosiasiitem"));
		$negosiasiitem = new Paketnegosiasiitem();

		$reqId = $this->input->post("reqId"); // Negosiasi Item ID
		$reqStatus = $this->input->post("reqStatus"); // Status

		$negosiasiitem->setField('UPDATED_BY', $this->USER_LOGIN_ID);
		$negosiasiitem->setField('PAKET_ID', $reqId);
		$negosiasiitem->setField('STATUS_NEGO', $reqStatus);
		if($negosiasiitem->updateStatusAll()) {
			// Insert Rekam Jejak
		    $this->load->library("librekamjejak");

		    switch ($reqStatus) {
		    	case '2': // Dikirim ke penyedia
				    $this->librekamjejak->insertRJ('3501','',$reqId,'null','3501'); // param 1: Posisi/'null', param 2: Keterangan/'', param 3: Paket_id/'null', param 4: permohonan_id/'null'
					echo "1||Data Berhasil Dikirim ke Penyedia";
		    		break;
		    	case '3': // Dikembalikan oleh Penyedia
				    $this->librekamjejak->insertRJ('3502','',$reqId,'null','3502'); // param 1: Posisi/'null', param 2: Keterangan/'', param 3: Paket_id/'null', param 4: permohonan_id/'null'
					echo "1||Data Berhasil Dikembalikan";
		    		break;
		    	case '4': // Diterima oleh penyedia
		    		// UPDATE TOTAL NEGO CHAT
					$this->load->model(array("Paketnegosiasiitem"));
					$negochat = new Paketnegosiasiitem(); 
					$negochat->setField('PAKET_ID', $reqId);
					$negochat->setField('UPDATED_BY', $this->USER_LOGIN_ID);
					$update = $negochat->updateNegoTerima();
				    $this->librekamjejak->insertRJ('3503','',$reqId,'null','3503'); // param 1: Posisi/'null', param 2: Keterangan/'', param 3: Paket_id/'null', param 4: permohonan_id/'null'
					echo "1||Negosiasi Berhasil Diterima";
		    		break;
		    	case '5': // Ditolak oleh Penyedia
				    $this->librekamjejak->insertRJ('3505','',$reqId,'null','3505'); // param 1: Posisi/'null', param 2: Keterangan/'', param 3: Paket_id/'null', param 4: permohonan_id/'null'
					echo "1||Data Berhasil Ditolak, Silahkan ubah harga nego";
		    		break;
		    	case '1': // Diterima oleh PJP
				    $this->librekamjejak->insertRJ('3504','',$reqId,'null','3504'); // param 1: Posisi/'null', param 2: Keterangan/'', param 3: Paket_id/'null', param 4: permohonan_id/'null'
					echo "1||Negosiasi Berhasil Diterima";
		    		break;
		    	
		    	default:
		    		// code...
		    		break;
		    }
		}
		// cek status dulu
		// $cekstatus = new Paketnegosiasiitem();
		// $cekstatus->selectByParams(array("PAKET_ID" => $reqId), -1, -1,"", " LIMIT 1");
		// $cekstatus->firstRow();

		// 0: Dibuat
		// 1: Approved
		// 2: Dikirim ke penyedia
		// 3: Ditolak penyedia

		// switch ($cekstatus->getField("STATUS_NEGO")) {
		// 	case '0': // Dibuat
		// 		if ($reqStatus == '2') { // Update Status
		// 			$negosiasiitem->setField('UPDATED_BY', $this->USER_LOGIN_ID);
		// 			$negosiasiitem->setField('PAKET_ID', $reqId);
		// 			$negosiasiitem->setField('STATUS_NEGO', '2');
		// 			$negosiasiitem->updateStatusAll();

		// 			// Insert Rekam Jejak
		// 		    $this->load->library("librekamjejak");
		// 		    $this->librekamjejak->insertRJ('3501','',$reqId,'null','3501'); // param 1: Posisi/'null', param 2: Keterangan/'', param 3: Paket_id/'null', param 4: permohonan_id/'null'
		// 		}
		// 		echo "1||Data berhasil dikirim";
		// 		break;

		// 	case '2': // Dibuat
		// 		if ($reqStatus == '2') { // Update Status 
		// 			echo "0||Data Sudah Dikirim ke Penyedia";
		// 		}
		// 		break;
			
		// 	default:
		// 		// code...
		// 		break;
		// }

	}

	public function addfileImportExcel()
	{
		$this->load->library("kauth");  $userLogin = new kauth();
		$this->load->model(array("Paketnegosiasiitem"));
		$this->load->library("FileHandler");
		require_once APPPATH . "/third_party/PHPExcel.php";

		$file = new FileHandler();

		// echo "<pre>"; print_r($this->input->post()); die();
		$reqId = $this->input->post("reqId"); // PaketID
		$reqLinkFile= $_FILES['reqLinkFile'];

		$FILE_DIR = "uploads/negosiasi/";

		/* UPLOAD FILE */
		$insertCount = 0;
		$renameFile = md5(date("dmYHis").$reqLinkFile['name'].$this->ID).".".getExtension($reqLinkFile['name']);
		if($file->uploadToDir('reqLinkFile', $FILE_DIR, $renameFile))
		{
			$insertLinkFilesSize = $file->uploadedSize;
			$insertLinkFilesExe =  $file->uploadedExtension;
			$insertLinkFile =  $renameFile;

			// Import Excel Jika ada file baru yang di upload
			$inputFileName = $FILE_DIR.$insertLinkFile;
			$sheetname = 'Negosiasi';

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
                 * A: No
                 * B: Uraian
                 * C: Volume
                 * D: Satuan
                 * E: Durasi
                 * F: Satuan
                 * G: Harga Satuan
                 * H: Jumlah Harga
                 * I: Harga Satuan Vendor
                 * J: Jumlah Harga Vendor
                 * K: Presentase
                 * L: Nilai Negosiasi 
                 * M: Jumlah Negosiasi
                 * N: Presentase
                **/

                foreach ($allDataInSheet as $key => $value) {
                	if ($key >= 4) {
	                	if ($value['B']) {
	                		$jumlahHarga = $value['C'] *  $value['E'] * str_replace(array(","," "),"",$value['G']); // Volume * Durasi * Harga Satuan
	                		$jumlahHargaPenawaran = $value['C'] *  $value['E'] * str_replace(array(","," "),"",$value['I']); // Volume * Durasi * Harga Satuan
	                		$jumlahHargaNegosiasi = $value['C'] *  $value['E'] * str_replace(array(","," "),"",$value['L']); // Volume * Durasi * Harga Satuan
							$negoitem = new Paketnegosiasiitem();
							$negoitem->setField("PAKET_ID", $reqId);
							$negoitem->setField("URAIAN", $value['B']);
							$negoitem->setField("VOLUME", $value['C']);
							$negoitem->setField("SATUAN_VOLUME", $value['D']);
							$negoitem->setField("DURASI", $value['E']);
							$negoitem->setField("SATUAN_DURASI", $value['F']);
							$negoitem->setField("HARGA_SATUAN", str_replace(array(","," "),"",$value['G']));
							$negoitem->setField("JUMLAH_HARGA", $jumlahHarga);
							$negoitem->setField("NILAI_PENAWARAN", str_replace(array(","," "),"",$value['I']));
							$negoitem->setField("JUMLAH_PENAWARAN", $jumlahHargaPenawaran);
							$negoitem->setField("NILAI_NEGOSIASI", str_replace(array(","," "),"",$value['L']));
							$negoitem->setField("JUMLAH_NEGOSIASI", $jumlahHargaNegosiasi);
							$negoitem->setField("FILE_NAMA", $renameFile);
							$negoitem->setField("STATUS_NEGO", '0');
							$negoitem->setField('CREATED_BY', $this->USER_LOGIN_ID);
							$insert = $negoitem->inserItem();

							$filepath = 'logs/importitemnego/importlog_' . date('Y-m-d') . '.txt';
							$textNya   = $value['B']." ### ".$value['C']." ".$value['D']." ### ".$value['E']." ".$value['F']." ### ".$value['G']." ### ".$value['H']." ".$this->USER_LOGIN_ID." ### ".$reqId." ### ".date('Y-m-d H:i:s');

							if ($insert) {
	                			$insertCount++;
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


		if($insertCount > 0)
			echo "Dokumen berhasil diimport.";
		else
			echo "Dokumen gagal disimpan, silahkan dicoba kembali!";

	}

	function updateAllItem()
	{
		$this->load->model(array("Paketnegosiasiitem"));
		$negoitem = new Paketnegosiasiitem(); 
		$negochat = new Paketnegosiasiitem(); 
		$reqId = $this->input->post("reqId"); // PaketID
		$paketnegosiasiitemid = $this->input->post("paketnegosiasiitemid"); // Negosiasi Item ID Array
		$reqNilainegosiasi = $this->input->post("reqNilainegosiasi"); // Array
		$reqJumlahnegosiasi = $this->input->post("reqJumlahnegosiasi"); // Array
		// echo "<pre>"; print_r($this->input->post()); die;

		// PPN
		$reqJumlahhargasatuan = $this->input->post("reqJumlahhargasatuan");
		$reqJumlahhargapenawaran = $this->input->post("reqJumlahhargapenawaran");
		$reqJumlahharganego = $this->input->post("reqJumlahharganego");

		// TOTAL JUMLAH
		$reqTotalHargaSatuan = $this->input->post("reqTotalHargaSatuan");
		$reqTotalHargaPenawaran = $this->input->post("reqTotalHargaPenawaran");
		$reqTotalHargaNego = $this->input->post("reqTotalHargaNego");
		$reqPPN = $this->input->post("reqPPN");

		// UPDATE TOTAL NEGO CHAT
		$negochat->setField('PAKET_ID', $reqId);
		$negochat->setField('UNIT_PRICE', CommaToDot(dotToNo($reqTotalHargaNego)));
		$negochat->setField('PPN_JUMLAH_HARGA_SATUAN', CommaToDot(dotToNo($reqJumlahhargasatuan)));
		$negochat->setField('PPN_JUMLAH_HARGA_PENAWARAN', CommaToDot(dotToNo($reqJumlahhargapenawaran)));
		$negochat->setField('PPN_JUMLAH_HARGA_NEGO', CommaToDot(dotToNo($reqJumlahharganego)));
		$negochat->setField('PPN', CommaToDot(dotToNo($reqPPN)));

		$negochat->setField('UPDATED_BY', $this->USER_LOGIN_ID);
		$update = $negochat->updateTotalNego();

 		if ($update) {
 			$negoitem->setField('UPDATED_BY', $this->USER_LOGIN_ID);
	        foreach ($reqNilainegosiasi as $key => $value) {
				$negoitem->setField('PAKET_NEGOSIASI_ITEM_ID', $paketnegosiasiitemid[$key]);
				$negoitem->setField('NILAI_NEGOSIASI', CommaToDot(dotToNo($reqNilainegosiasi[$key])));
				$negoitem->setField('JUMLAH_NEGOSIASI', CommaToDot(dotToNo($reqJumlahnegosiasi[$key])));
				$update2 = $negoitem->updateNilaiNego();
	        }

	        if ($update2) {
				echo "Data berhasil diupdate";
	 		} else {
				echo "Data gagal diupdate";
	 		}
 		} else {
			echo "Data gagal diupdate";
 		}
	}

	function updateAllItemRekanan()
	{
		$this->load->model(array("Paketnegosiasiitem"));
		$negoitem = new Paketnegosiasiitem(); 
		$negochat = new Paketnegosiasiitem(); 
		$reqId = $this->input->post("reqId"); // PaketID
		$paketnegosiasiitemid = $this->input->post("paketnegosiasiitemid"); // Negosiasi Item ID Array
		$reqNilainegosiasi = $this->input->post("reqNilainegosiasi"); // Array
		$reqJumlahnegosiasi = $this->input->post("reqJumlahnegosiasi"); // Array
		// echo "<pre>"; print_r($this->input->post()); die;

		// PPN
		$reqJumlahhargasatuan = $this->input->post("reqJumlahhargasatuan");
		$reqJumlahhargapenawaran = $this->input->post("reqJumlahhargapenawaran");
		$reqJumlahharganego = $this->input->post("reqJumlahharganego");

		// TOTAL JUMLAH
		$reqTotalHargaSatuan = $this->input->post("reqTotalHargaSatuan");
		$reqTotalHargaPenawaran = $this->input->post("reqTotalHargaPenawaran");
		$reqTotalHargaNego = $this->input->post("reqTotalHargaNego");

		// UPDATE TOTAL NEGO CHAT
		$negochat->setField('PAKET_ID', $reqId);
		$negochat->setField('UNIT_PRICE', CommaToDot(dotToNo($reqTotalHargaNego)));
		$negochat->setField('PPN_JUMLAH_HARGA_NEGO', CommaToDot(dotToNo($reqJumlahharganego)));
		$negochat->setField('UPDATED_BY', $this->USER_LOGIN_ID);
		$update = $negochat->updateTotalNegoRekanan();

 		if ($update) {
 			$negoitem->setField('UPDATED_BY', $this->USER_LOGIN_ID);
	        foreach ($reqNilainegosiasi as $key => $value) {
				$negoitem->setField('PAKET_NEGOSIASI_ITEM_ID', $paketnegosiasiitemid[$key]);
				$negoitem->setField('NILAI_NEGOSIASI', CommaToDot(dotToNo($reqNilainegosiasi[$key])));
				$negoitem->setField('JUMLAH_NEGOSIASI', CommaToDot(dotToNo($reqJumlahnegosiasi[$key])));
				$update2 = $negoitem->updateNilaiNego();
	        }

	        if ($update2) {
				echo "Data berhasil diupdate";
	 		} else {
				echo "Data gagal diupdate";
	 		}
 		} else {
			echo "Data gagal diupdate";
 		}
	}

	function deleteItem()
	{	
		$reqId		= $this->input->get('reqId');
		$this->load->model('Paketnegosiasiitem'); 
		$negosiasiitem	= new Paketnegosiasiitem();
		$negosiasiitem->setField("PAKET_NEGOSIASI_ITEM_ID", $reqId);
		$negosiasiitem->deleteItem(); 

		echo "Data berhasil dihapus.";
	}


}
?>
