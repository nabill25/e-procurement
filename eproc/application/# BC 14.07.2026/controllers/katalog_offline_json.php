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
include_once("functions/default.func.php");

class katalog_offline_json extends CI_Controller {

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
	    $this->LOGIN_TIME = isset($this->kauth->getInstance()->getIdentity()->LOGIN_TIME) ? $this->kauth->getInstance()->getIdentity()->LOGIN_TIME : '';
	    $this->LOGIN_DATE = isset($this->kauth->getInstance()->getIdentity()->LOGIN_DATE) ? $this->kauth->getInstance()->getIdentity()->LOGIN_DATE : '';
	    $this->REKANAN = isset($this->kauth->getInstance()->getIdentity()->NAMA) ? $this->kauth->getInstance()->getIdentity()->NAMA : '';
	    $this->REKANAN_KODE = isset($this->kauth->getInstance()->getIdentity()->KODE) ? $this->kauth->getInstance()->getIdentity()->KODE : '';
	    $this->REKANAN_PKP = isset($this->kauth->getInstance()->getIdentity()->PKP) ? $this->kauth->getInstance()->getIdentity()->PKP : '';
	    $this->REKANAN_NPWP = isset($this->kauth->getInstance()->getIdentity()->NPWP) ? $this->kauth->getInstance()->getIdentity()->NPWP : '';
	    $this->REKANAN_STATUS_PERUSAHAAN = isset($this->kauth->getInstance()->getIdentity()->STATUS_PERUSAHAAN) ? $this->kauth->getInstance()->getIdentity()->STATUS_PERUSAHAAN : '';
	    $this->REKANAN_STATUS_VALIDASI = isset($this->kauth->getInstance()->getIdentity()->STATUS_VALIDASI) ? $this->kauth->getInstance()->getIdentity()->STATUS_VALIDASI : '';
	}

	function json_pejabat_offline()
	{

		$this->load->model("Paket");
		$paket = new Paket();

		$aColumns 			= array('PAKET_ID','NAMA','NILAI_OWNER_ESTIMATE','KODE_PR','PAKET_UUID');
		$aColumnsAlias		= array('PAKET_ID', 'NAMA','NILAI_OWNER_ESTIMATE','KODE_PR','PAKET_UUID');

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

		$statement .= "AND (UPPER(A.NAMA) LIKE '%".strtoupper($_GET['sSearch'])."%' OR UPPER(KODE_PR) LIKE '%".strtoupper($_GET['sSearch'])."%' )";
    	$statement .= ' AND A.PAKET_METODE_LELANG_ID = \'9\' AND A.USER_LOGIN_ID = '.$this->USER_LOGIN_ID.'';
		$allRecord = $paket->getCountByParams(array(), $statement);
		if($_GET['sSearch'] == "")
			$allRecordFilter = $allRecord;
		else
			$allRecordFilter =  $paket->getCountByParams(array(), $statement);

		$paket->selectByParamsWithKatalog(array(), $dsplyRange, $dsplyStart, $statement, $sOrder);

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
				} elseif($aColumns[$i]=='NAMA') {
					if ($paket->getField('ALASAN') != "") {
						$row[] = $paket->getField(trim($aColumns[$i])).'<br><span class="badge badge-danger"> Paket di Batalkan </span><br><p style="font-size:10px"> Alasan: '.$paket->getField("ALASAN").'</p> ';
					} else {
						$row[] = $paket->getField(trim($aColumns[$i]));
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

	public function addfile() // Done
	{
		$this->load->library("kauth");  $userLogin = new kauth();
		$this->load->model("Purchasingfile");
		$this->load->library("FileHandler");

		$file = new FileHandler();
		$pfile = new Purchasingfile();

		// echo "<pre>"; print_r($this->input->post()); die();
		$reqPaketid = $this->input->post("reqPaketid");
		$reqLinkFile= $_FILES['reqLinkFile'];

		$FILE_DIR = "uploads/purchasing/";

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

		$pfile->setField("PAKET_ID", $reqPaketid);
		$pfile->setField("FILE_NAMA", $reqNama);
		$pfile->setField("FILE_NAMA_ENCRYPT", $insertLinkFile);
		$pfile->setField("FILE_PATH", $FILE_DIR);
		$pfile->setField("FILE_EXTENTION", $insertLinkFilesExe);
		$pfile->setField("FILE_SIZE", $insertLinkFilesSize);
		$pfile->setField("FILE_TANGGAL", dateToDBCheck(date('d-m-Y')));
		$pfile->setField("FILE_JENIS", $reqJenis);
		$pfile->setField("FILE_KETERANGAN", $reqKeterangan);
		$pfile->setField("CREATED_BY", $this->USER_LOGIN_ID);
		$insert = $pfile->insertFile();

		if($insert) {
			echo "Dokumen berhasil disimpan.";
			// Insert Rekam Jejak
	        $this->load->library("librekamjejak");
	        $this->librekamjejak->insertRJ('359','Dokumen: '.$reqNama,$reqPaketid,'null','359');
	        // End Insert Rekam Jejak
		}
		else {
			echo "Dokumen gagal disimpan, silahkan dicoba kembali!";
		}

	}

	function deleteFile()  // Done
	{
		$this->load->model('Purchasingfile');

		$file	= new Purchasingfile();

		$reqId		= $this->input->get('reqId');

		$file->setField("PURCHASINGFILEID", $reqId);
		$file->delete();

		echo "Data berhasil dihapus.";
	}


 }
?>
