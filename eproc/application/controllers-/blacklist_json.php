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

class blacklist_json extends CI_Controller {

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

		$this->load->model("Blacklist");
		$this->load->model("Blacklistfile");
		$blacklist = new Blacklist();
		$blacklist_file = new Blacklistfile();

		ini_set("memory_limit","500M");
		ini_set('max_execution_time', 520);

		$reqKeterangan = $this->input->post("reqKeterangan");
		$reqId = $this->input->get("reqId");
		$reqSearch = $this->input->post("reqSearch");


		$aColumns 			= array('BLACKLIST_ID',  'NAMA', 'ALAMAT','NPWP','TANGGAL','ALASAN','NO_SK');
		$aColumnsAlias		= array('BLACKLIST_ID',  'NAMA', 'ALAMAT','NPWP','TANGGAL_MULAI','ALASAN','NO_SK');

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
			if ( trim($sOrder) == "ORDER BY BLACKLIST_ID desc" )
			{
				/*
				* If there is no order by clause - ORDER BY INDEX COLUMN!!! DON'T DELETE IT!
				* If there is no order by clause there might be bugs in table display.
				* No order by clause means that the db is not responsible for the data ordering,
				* which means that the same row can be displayed in two pages - while
				* another row will not be displayed at all.
				*/
				$sOrder = " ORDER BY NAMA ASC";

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

		$statement = "AND (UPPER(B.NAMA) LIKE '%".strtoupper($_GET['sSearch'])."%' OR UPPER(KOTA) LIKE '".strtoupper($_GET['sSearch'])."%')";
		$allRecord = $blacklist->getCountByParams(array(), $statement);
		if($_GET['sSearch'] == "")
			$allRecordFilter = $allRecord;
		else
			$allRecordFilter =  $blacklist->getCountByParams(array(), $statement);

		if ($this->USER_TYPE_ID == '') { // tampilan public hanya data blacklist yang aktif
			$blacklist->selectByParams(array(), $dsplyRange, $dsplyStart, $statement, $sOrder);
		} else { // tampilkan semua untuk admin vms
			$blacklist->selectByParamsAll(array(), $dsplyRange, $dsplyStart, $statement, $sOrder);
		}

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
					$blacklist_file->selectByParams(array("BLACKLIST_ID" => $blacklist->getField("BLACKLIST_ID")), -1, -1," ORDER BY BLACKLISTFILE_ID DESC LIMIT 1");
					$dokumenBlacklist = '';
					if ($blacklist_file->countRow() > 0) {
						while($blacklist_file->nextRow()) {
							if ($this->USER_TYPE_ID == '') { // tampilan public hanya dokumen yang di share
								if ($blacklist_file->getField('FILE_PUBLISH_PENYEDIA') == '1') {
								$dokumenBlacklist .= $blacklist_file->getField('FILE_NAMA').'<br><a href="'.$blacklist_file->getField('FILE_PATH').$blacklist_file->getField('FILE_NAMA_ENCRYPT').'" target="_blank" class="badge badge-primary"><span class="fa fa-download"></span> Download</a>';
								}
							} else { // tampilkan semua untuk admin vms
								$dokumenBlacklist .= $blacklist_file->getField('FILE_NAMA').'<br><a href="'.$blacklist_file->getField('FILE_PATH').$blacklist_file->getField('FILE_NAMA_ENCRYPT').'" target="_blank" class="badge badge-primary"><span class="fa fa-download"></span> Download</a>';
							}
						}
					}
					$row[] = $blacklist->getField(trim($aColumns[$i])).'<br>'.$dokumenBlacklist;
				} elseif($aColumns[$i]=='TANGGAL') {
					$tgl1 = new DateTime($blacklist->getField("TANGGAL_SELESAI"));
	                $tgl2 = new DateTime(date("Y-m-d"));
	                $d = $tgl1->diff($tgl2)->days;
	                if ($blacklist->getField("TANGGAL_SELESAI") >= date('Y-m-d')) {
	                    $note = '<span class="badge badge-primary">Blacklist sisa '.$d.' hari lagi</span>';
									// } else if ($d == 0) {
	                } else {
	                    $note = '<span class="badge badge-dark"> Blacklist selesai </span>';
	                }
					$row[] = getFormattedDate($blacklist->getField("TANGGAL_MULAI"))." s/d ".getFormattedDate($blacklist->getField("TANGGAL_SELESAI")).'<br>'.$note;
				} else {
					$row[] = $blacklist->getField(trim($aColumns[$i]));
				}
			}
			$output['aaData'][] = $row;
		}
		echo json_encode( $output );
	}

	function blacklist_add_coba()
	{
		/* INCLUDE FILE */
		$this->load->model("Blacklist");
		$this->load->model("Blacklistfile");
		$this->load->model("Rekanan");
		$this->load->library("FileHandler");

		/* create objects */
		$blacklist = new Blacklist();
		$file = new FileHandler();
		$bfile = new Blacklistfile();

		/* VARIABLE */
		$reqPerusahaan	= $this->input->post("reqPerusahaan");
		$reqNama	= $this->input->post("reqNama");
		$reqAlamat	= $this->input->post("reqAlamat");
		$reqAlasan	= $this->input->post("reqAlasan");
		$reqKota	= $this->input->post("reqKota");
		$reqNPWP	= $this->input->post("reqNPWP");
		$reqTanggalMulai	= $this->input->post("reqTanggalMulai");
		$reqTanggalSelesai	= $this->input->post("reqTanggalSelesai");
		$reqNoSk	= $this->input->post("reqNoSk");
		$reqId		= $this->input->post("reqId");
		$reqRekananId	= $this->input->post("reqRekananId");
		$reqSubmit	= $this->input->post("reqSubmit");
		/* VALIDATION */
		// trigger the validation
		/* ACTION BY REQMODE */
		if($reqSubmit == "Simpan")
		{
			//echo "fefef".$reqNama;exit;
			$blacklist->setField("ALAMAT",$reqAlamat);
			$blacklist->setField("ALASAN",$reqAlasan);
			$blacklist->setField("KOTA",$reqKota);
			$blacklist->setField("NPWP",$reqNPWP);
			$blacklist->setField("TANGGAL_MULAI",dateToDBCheck($reqTanggalMulai));
			$blacklist->setField("TANGGAL_SELESAI",dateToDBCheck($reqTanggalSelesai));
			$blacklist->setField("NO_SK",$reqNoSk);
			//$blacklist->setField("REKANAN_TIPE_ID", ValToNullDB($reqPerusahaan));

			$blacklist->setField("NAMA", $reqNama);
			$blacklist->setField("BLACKLIST_ID", $reqId);
			$blacklist->setField("REKANAN_ID",$reqRekananId);
			//$blacklist->setField("STATUS","null");
			$blacklist->setField("STATUS",1);
			if($blacklist->insert())
			{
				$reqBlacklistId = $blacklist->id;
				$reqLinkFile= $_FILES['reqLinkFile'];

				$FILE_DIR = "uploads/vms/";

				/* UPLOAD FILE */
				$renameFile = '__blacklist_'.md5(date("dmYHis").$reqLinkFile['name'].$this->ID).".".getExtension($reqLinkFile['name']);
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
				$reqKeterangan = $this->input->post("reqKeterangan");
				$reqPublishPenyedia = $this->input->post("reqPublishPenyedia");

				$bfile->setField("CONTRACTINGREKANANID", $contractingrekananid);
				$bfile->setField("BLACKLIST_ID", $reqBlacklistId);
				$bfile->setField("FILE_NAMA", $reqNama);
				$bfile->setField("FILE_NAMA_ENCRYPT", $insertLinkFile);
				$bfile->setField("FILE_PATH", $FILE_DIR);
				$bfile->setField("FILE_EXTENTION", $insertLinkFilesExe);
				$bfile->setField("FILE_SIZE", $insertLinkFilesSize);
				$bfile->setField("FILE_TANGGAL", dateToDBCheck(date('d-m-Y')));
				$bfile->setField("FILE_KETERANGAN", $reqKeterangan);
				$bfile->setField("FILE_PUBLISH_PENYEDIA", $reqPublishPenyedia);
				$bfile->setField("CREATED_BY", $this->USER_LOGIN_ID);
				$insert = $bfile->insertFile();

				if($insert)
					echo "Data berhasil di Simpan";
				else
					echo "Data gagal disimpan, silahkan dicoba kembali!";


			}
			else
			{
				echo "Data gagal di Simpan";
			}


		}
	}

	public function addfile() // Done
	{
		$this->load->library("kauth");  $userLogin = new kauth();
		$this->load->model("Blacklistfile");
		$this->load->library("FileHandler");

		$file = new FileHandler();
		$bfile = new Blacklistfile();

		// echo "<pre>"; print_r($this->input->post()); die();
		$reqBlacklistId = $this->input->post("reqBlacklistId");

		$reqLinkFile= $_FILES['reqLinkFile'];

		$FILE_DIR = "uploads/vms/";

		/* UPLOAD FILE */
		$renameFile = '__blacklist_'.md5(date("dmYHis").$reqLinkFile['name'].$this->ID).".".getExtension($reqLinkFile['name']);
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
		$reqKeterangan = $this->input->post("reqKeterangan");
		$reqPublishPenyedia = $this->input->post("reqPublishPenyedia");

		$bfile->setField("CONTRACTINGREKANANID", $contractingrekananid);
		$bfile->setField("BLACKLIST_ID", $reqBlacklistId);
		$bfile->setField("FILE_NAMA", $reqNama);
		$bfile->setField("FILE_NAMA_ENCRYPT", $insertLinkFile);
		$bfile->setField("FILE_PATH", $FILE_DIR);
		$bfile->setField("FILE_EXTENTION", $insertLinkFilesExe);
		$bfile->setField("FILE_SIZE", $insertLinkFilesSize);
		$bfile->setField("FILE_TANGGAL", dateToDBCheck(date('d-m-Y')));
		$bfile->setField("FILE_KETERANGAN", $reqKeterangan);
		$bfile->setField("FILE_PUBLISH_PENYEDIA", $reqPublishPenyedia);
		$bfile->setField("CREATED_BY", $this->USER_LOGIN_ID);
		$insert = $bfile->insertFile();

		if($insert)
			echo "Dokumen berhasil disimpan.";
		else
			echo "Dokumen gagal disimpan, silahkan dicoba kembali!";

	}

	function delete()
	{
		$this->load->model("Blacklist");
		$arsip = new Blacklist();
		$reqId		= $this->input->get("reqId");
		$arsip->setField('BLACKLIST_ID', $reqId);

		$blacklist = new Blacklist();
		$blacklist->selectByParamsSimple(array("A.BLACKLIST_ID" => $reqId));
		$blacklist->firstRow();
			if($arsip->delete())
				echo "Data berhasil dihapus";
		else
			echo "Data gagal dihapus ";
	}

}
?>
