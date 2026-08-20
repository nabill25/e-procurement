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

class rekanan_sertifikat_json extends CI_Controller {

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

	function get_data_sertifikat()
	{
		$this->load->model("RekananSertifikat");
		$rekanan_sertifikat = new RekananSertifikat();
		$reqSearch = $this->input->get("reqSearch");
		$met = array();
		$i=0;

		$rekanan_sertifikat->selectByParams(array("REKANAN_ID" => $this->ID), -1, -1, "AND (UPPER(A.NAMA) LIKE '%".strtoupper($reqSearch)."%')");
		while($rekanan_sertifikat->nextRow())
		{
			$met[$i]['id'] = $rekanan_sertifikat->getField('REKANAN_SERTIFIKAT_ID');
			$met[$i]['text'] = $rekanan_sertifikat->getField('NAMA');
			$met[$i]['NAMA'] = $rekanan_sertifikat->getField('NAMA');
			$met[$i]['NOMOR'] = $rekanan_sertifikat->getField('NOMOR');
			$met[$i]['TANGGAL'] = getFormattedDate($rekanan_sertifikat->getField('TANGGAL'));
			$i++;
		}
		echo json_encode($met);
	}

	function daftar_rekanan_teknis_sertifikat_json()
	{
		$this->load->library("kauth");  $userLogin = new kauth();
		$this->load->model("RekananSertifikat");
		$rekanan_sertifikat = new RekananSertifikat();

		/* LOGIN CHECK
		if ($userLogin->checkUserLogin())
		{
			$userLogin->retrieveUserInfo();
		}*/

		ini_set("memory_limit","500M");
		ini_set('max_execution_time', 520);

		$reqKeterangan = $this->input->post("reqKeterangan");
		$reqId = $this->input->post("reqId");
		$reqSearch = httpFilterGet("reqSearch");

		/*
		 * Paging
		 */
		$sLimit = "";
		if ( isset( $_GET['iDisplayStart'] ) && $_GET['iDisplayLength'] != '-1' )
		{
			$sLimit = "LIMIT ".mysql_real_escape_string( $_GET['iDisplayStart'] ).", ".
				mysql_real_escape_string( $_GET['iDisplayLength'] );
		}
		else{
			$_GET['iDisplayStart'] = $_GET['iDisplayLength'] = '-1';
		}

		/*
		 * Ordering
		 */
		$sOrder = "";
			if ( isset( $_GET['iSortCol_0'] ) )
			{
				$sOrder = "ORDER BY  ";
				for ( $i=0 ; $i<intval( $_GET['iSortingCols'] ) ; $i++ )
				{
					if ( $_GET[ 'bSortable_'.intval($_GET['iSortCol_'.$i]) ] == "true" )
					{
						$sOrder .= 'upper('.$aColumns[ intval( $_GET['iSortCol_'.$i] ) ].")
							".mysql_real_escape_string( $_GET['sSortDir_'.$i] ) .", ";
					}
				}

				$sOrder = substr_replace( $sOrder, "", -2 );
				if ( $sOrder == "ORDER BY" )
				{
					$sOrder = "";
				}
			}

		//$statement = "' AND REKANAN_ID = '".$reqId."'";
		if($reqSearch == "")
		{
			$allRecord = $rekanan_sertifikat->getCountByParams(array('REKANAN_ID'=>$reqId), $statement);
			$rekanan_sertifikat->selectByParams(array('REKANAN_ID'=>$reqId), $_GET['iDisplayLength'], $_GET['iDisplayStart'], $statement);
			//$allRecord = 2;
		}
		else
		{
			$reqSearch = str_replace('\\', '', $reqSearch);
			$allRecord = 1;
			$rekanan_sertifikat->selectByParams(array(), $_GET['iDisplayLength'], $_GET['iDisplayStart'], $statement.$reqSearch." ");

		}

		$column = array('REKANAN_SERTIFIKAT_ID', 'NO', 'NAMA','NOMOR','TANGGAL','BERLAKU');
			/*
			 * Output
			 */
			$output = array(
				"sEcho" => intval($_GET['sEcho']),
				"iTotalRecords" => $allRecord,
				"iTotalDisplayRecords" => $allRecord,
				"aaData" => array()
			);
			$number = 1;
			while($rekanan_sertifikat->nextRow())
			{
				$row = array();
				for ( $i=0 ; $i<count($column) ; $i++ )
				{
					if($column[$i]=='NO')		$row[] = $number;
					elseif($column[$i]=='TANGGAL' || $column[$i]=='BERLAKU') $row[] = getFormattedDateJson($rekanan_sertifikat->getField(trim($column[$i])));
					else						$row[] = $rekanan_sertifikat->getField(trim($column[$i]));
				}
				$number++;
				$output['aaData'][] = $row;
			}
			//echo $number;
			echo json_encode( $output );
	}

	function get_data_kualifikasi_sertifikat()
	{
		$this->load->library("kauth");  $userLogin = new kauth();
		$this->load->model("RekananSertifikat");
		$rekanan_sertifikat = new RekananSertifikat();

		$reqId = httpFilterGet("reqId");

		/* LOGIN CHECK */
		if ($userLogin->checkUserLogin())
		{
			$userLogin->retrieveUserInfo();
		}

		if($reqId == ""){}
		else
			$statement= " AND NOT REKANAN_SERTIFIKAT_ID IN (".$reqId.")";

		$rekanan_sertifikat->selectByParams(array("REKANAN_ID" => $userLogin->userRekanan), -1, -1, $statement);
		//$rekanan_sertifikat->selectByParams(array("REKANAN_ID" => $userLogin->userRekanan), -1, -1);
		$met = array();
		$i=0;

		while($rekanan_sertifikat->nextRow()){
			$met[$i]['NAMA'] = $rekanan_sertifikat->getField('NAMA');
			$met[$i]['NOMOR'] = $rekanan_sertifikat->getField('NOMOR');
			$met[$i]['TANGGAL'] = getFormattedDate($rekanan_sertifikat->getField('TANGGAL'));
			$met[$i]['REKANAN_SERTIFIKAT_ID'] = $rekanan_sertifikat->getField('REKANAN_SERTIFIKAT_ID');
			$i++;
		}
		echo json_encode($met);
	}

	function data_teknis_sertifikat_lain_ubah()
	{
		/* INCLUDE FILE */
		$this->load->library("kauth");  $userLogin = new kauth();
		$this->load->model("Rekanan");
		$this->load->model("RekananSertifikat");
		$this->load->library("FileHandler");
		$file = new FileHandler();

		$rekanan = new Rekanan();
		$rekanan_sertifikat = new RekananSertifikat();

		$reqSertifikatId			= $this->input->post("reqSertifikatId");
		$reqJenis = $this->input->post('reqJenis');
		$reqJenisSertifikat = $this->input->post('reqJenisSertifikat') ?: 0;
		$reqNama = $this->input->post('reqNama');
		$reqNomor = $this->input->post('reqNomor');
		$reqTanggalTerbit = $this->input->post('reqTanggalTerbit');
		$reqBerlakuHingga = $this->input->post('reqBerlakuHingga');
		$reqSubmit	= $this->input->post("reqSubmit");
		$reqSimpan	= $this->input->post("reqSimpan");
		$reqBatal	= $this->input->post("reqBatal");
		$reqInstansiPemberi	= $this->input->post("reqInstansiPemberi");
		$reqLinkFile = $_FILES['reqLinkFile'];
		$reqLinkFileTemp = $this->input->post("reqLinkFileTemp");
		$reqLinkFileTempTipe = $this->input->post("reqLinkFileTempTipe");
		$reqLinkFileTempUkuran = $this->input->post("reqLinkFileTempUkuran");
		$reqLinkFileTempNama = $this->input->post("reqLinkFileTempNama");
		$reqMode = $this->input->post("reqMode");

		$FILE_DIR = "uploads/sertifikat/";

		$reqId = $this->ID;

		//$cek_file = formatTextToDb($file->getFileName('reqLinkFile'));
		if($reqMode=='insert')
		{
			$rekanan_sertifikat->setField("REKANAN_ID",$this->ID);
			$rekanan_sertifikat->setField("NOMOR",$reqNomor);
			$rekanan_sertifikat->setField("NAMA",$reqNama);
			$rekanan_sertifikat->setField("JENIS",$reqJenis);
			$rekanan_sertifikat->setField("REKANAN_JENIS_SERTIFIKAT_ID",$reqJenisSertifikat);
			$rekanan_sertifikat->setField("TANGGAL",dateToDBCheck($reqTanggalTerbit));
			$rekanan_sertifikat->setField("BERLAKU",dateToDBCheck($reqBerlakuHingga));
			$rekanan_sertifikat->setField("INSTANSI_PEMBERI",$reqInstansiPemberi);

			/* UPLOAD FILE */
			$renameFile = md5(date("dmYHis").$reqLinkFile['name'].$this->ID).".".getExtension($reqLinkFile['name']);
			if($file->uploadToDir('reqLinkFile', $FILE_DIR, $renameFile))
			{
				$insertLinkFilesSize = $file->uploadedSize;
				$insertLinkFilesExe =  $file->uploadedExtension;
				$insertLinkFile =  $renameFile;
				$insertLinkFileNama =  $reqLinkFile['name'];
			}
			else
			{
				$insertLinkFilesSize = $reqLinkFileTempUkuran;
				$insertLinkFilesExe =  $reqLinkFileTempTipe;
				$insertLinkFile =  $reqLinkFileTemp;
				$insertLinkFileNama =  $reqLinkFileTempNama;
			}
			/* END UPLOAD FILE */

			$rekanan_sertifikat->setField("UKURAN", $insertLinkFilesSize);
			$rekanan_sertifikat->setField("TIPE", $insertLinkFilesExe);
			$rekanan_sertifikat->setField("PATH_FILE", $insertLinkFile);
			$rekanan_sertifikat->setField("NAMA_FILE", $insertLinkFileNama);
			$rekanan_sertifikat->setField('CREATED_BY', $this->USER_LOGIN_ID);

			if($rekanan_sertifikat->insert())
			{
				echo "Data berhasil disimpan";
			}
			else
			{
				echo "Data Gagal Tersimpan";
			}
		}
		else
		{
			/* UPLOAD FILE */
			$renameFile = md5(date("dmYHis").$reqLinkFile['name'].$this->ID).".".getExtension($reqLinkFile['name']);
			if($file->uploadToDir('reqLinkFile', $FILE_DIR, $renameFile))
			{
				$insertLinkFilesSize = $file->uploadedSize;
				$insertLinkFilesExe =  $file->uploadedExtension;
				$insertLinkFile =  $renameFile;
				$insertLinkFileNama =  $reqLinkFile['name'];
			}
			else
			{
				$insertLinkFilesSize = $reqLinkFileTempUkuran;
				$insertLinkFilesExe =  $reqLinkFileTempTipe;
				$insertLinkFile =  $reqLinkFileTemp;
				$insertLinkFileNama =  $reqLinkFileTempNama;
			}
			/* END UPLOAD FILE */

			$rekanan_sertifikat->setField("REKANAN_SERTIFIKAT_ID",$reqSertifikatId);
			$rekanan_sertifikat->setField("NOMOR",$reqNomor);
			$rekanan_sertifikat->setField("NAMA",$reqNama);
			$rekanan_sertifikat->setField("JENIS",$reqJenis);
			$rekanan_sertifikat->setField("REKANAN_JENIS_SERTIFIKAT_ID",$reqJenisSertifikat);
			$rekanan_sertifikat->setField("TANGGAL",dateToDBCheck($reqTanggalTerbit));
			$rekanan_sertifikat->setField("BERLAKU",dateToDBCheck($reqBerlakuHingga));
			$rekanan_sertifikat->setField("REKANAN_ID",$this->ID);
			$rekanan_sertifikat->setField("INSTANSI_PEMBERI",$reqInstansiPemberi);

			$rekanan_sertifikat->setField("UKURAN", $insertLinkFilesSize);
			$rekanan_sertifikat->setField("TIPE", $insertLinkFilesExe);
			$rekanan_sertifikat->setField("PATH_FILE", $insertLinkFile);
			$rekanan_sertifikat->setField("NAMA_FILE", $insertLinkFileNama);
			$rekanan_sertifikat->setField('CREATED_BY', $this->USER_LOGIN_ID);

			//if($rekanan->update()){}
			if($rekanan_sertifikat->update())
			{
				echo "Data berhasil diupdate";
			}
			else
			{
				echo "Data Gagal Tersimpan";
			}
		}
	}

	function delete()
	{
		/* INCLUDE FILE */
		$this->load->model("Rekanan");
		$this->load->model("RekananSertifikat");

		$rekanan = new Rekanan();
		$rekanan_sertifikat = new RekananSertifikat();

		$reqId= $this->input->get('reqId');

		$rekanan_sertifikat->setField("REKANAN_SERTIFIKAT_ID", $reqId);
		$rekanan_sertifikat->setField('REKANAN_ID', $this->ID);

		if($rekanan_sertifikat->delete())
		{
			echo "Data telah dihapus";
		}
		else
		{
			echo "Data gagal dihapus";
		}
	}
}
?>
