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

class rekanan_peralatan_json extends CI_Controller {

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

	function get_data_peralatan()
	{
		$this->load->model("RekananPeralatan");
		$rekanan_peralatan = new RekananPeralatan();

		$reqSearch = $this->input->get("reqSearch");

		$met = array();
		$i=0;

		$rekanan_peralatan->selectByParams(array("REKANAN_ID" => $this->ID), -1, -1, "AND (UPPER(A.JENIS) LIKE '%".strtoupper($reqSearch)."%')");
		while($rekanan_peralatan->nextRow())
		{
			$met[$i]['id'] = $rekanan_peralatan->getField('REKANAN_PERALATAN_ID');
			$met[$i]['text'] = $rekanan_peralatan->getField('JENIS');
			$met[$i]['JENIS'] = $rekanan_peralatan->getField('JENIS');
			$met[$i]['KAPASITAS'] = $rekanan_peralatan->getField('KAPASITAS');
			$met[$i]['MERK'] = $rekanan_peralatan->getField('MERK');
			$met[$i]['JUMLAH'] = $rekanan_peralatan->getField('JUMLAH');
			$i++;
		}
		echo json_encode($met);
	}

	function data_teknis_peralatan_tambah()
	{
		/* INCLUDE FILE */
		$this->load->library("kauth");  $userLogin = new kauth();
		$this->load->model("RekananPeralatan");
		$this->load->model("Rekanan");

		/* create objects */
		$rekanan = new Rekanan();
		$rekanan_peralatan = new RekananPeralatan();
		$file = new FileHandler();

		/* LOGIN CHECK */
		if ($userLogin->checkUserLogin())
		{
			$userLogin->retrieveUserInfo();
		}

		$reqTipe= $this->input->post('reqTipe');
		$reqJml= $this->input->post('reqJml');
		$reqKapasitas= $this->input->post('reqKapasitas');
		$reqKapasitasSat= $this->input->post('reqKapasitasSat');
		$reqMerk= $this->input->post('reqMerk');
		$reqTahun= $this->input->post('reqTahun');
		$reqKondisi= $this->input->post('reqKondisi');
		$reqLokasi= $this->input->post('reqLokasi');
		$reqKepemilikan= $this->input->post('reqKepemilikan');
		$reqId= $this->input->post('reqId');
		$reqSubmit= $this->input->post('reqSubmit');
		$reqLinkFile= $_FILES['reqLinkFile'];

		$FILE_DIR = "uploads/peralatan/";

		if($reqSubmit == 'Batal'){
			//header("Location: main/?pg=data_administrasi_umum");
			echo '<script language="javascript">';
			echo "document.location='main/?pg=data_teknis_peralatan'";
			echo '</script>';
			exit;
		}

		$cek_file = new RekananPeralatan();
		$cek_file->selectByParams(array("REKANAN_ID"=>$reqId,"REKANAN_PERALATAN_ID"=>$reqPeralatanId));
		$cek_file->firstRow();
		$hasil_cek_file = $cek_file->getField("PATH_FILE");
		unset($cek_file);

		$cek_file = formatTextToDb($file->getFileName('reqLinkFile'));

		if($reqSubmit == 'Submit'){
			//echo $reqNPWP;
			if($hasil_cek_file == '' && $cek_file == ''){
				echo '<script language="javascript">';
				echo "$.jGrowl('Lengkapi file terlebih dahulu, Data gagal disimpan');";
				echo '</script>';
			}else{
				$rekanan_peralatan->setField("REKANAN_ID",$userLogin->userRekanan);
				$rekanan_peralatan->setField("JUMLAH",$reqJml);
				$rekanan_peralatan->setField("KAPASITAS",$reqKapasitas);
				$rekanan_peralatan->setField("KAPASITAS_SATUAN",$reqKapasitasSat);
				$rekanan_peralatan->setField("MERK",$reqMerk);
				$rekanan_peralatan->setField("TAHUN",$reqTahun);
				$rekanan_peralatan->setField("KONDISI",$reqKondisi);
				$rekanan_peralatan->setField("LOKASI",$reqLokasi);
				$rekanan_peralatan->setField("BUKTI_KEPEMILIKAN",$reqKepemilikan);
				$rekanan_peralatan->setField("JENIS",$reqTipe);

				$cek = formatTextToDb($file->getFileName('reqLinkFile'));
				if($cek != "")
				{
					$renameFile = $rekanan_peralatan->getNextId("REKANAN_PERALATAN_ID","REKANAN_PERALATAN").formatTextToDb($file->getFileName('reqLinkFile'));
					$varSource=$FILE_DIR.$reqLinkFileTemp;

					if($file->uploadToDir('reqLinkFile', $FILE_DIR, $renameFile))
					{
						if($reqLinkFileTemp != ''){
							if($file->delete($varSource)){}
						}
						$insertLinkFile = $file->uploadedFileName;
						$insertLinkFilesSize = $file->uploadedSize;
						$insertLinkFilesExe = $file->uploadedExtension;
					}
				}else{
					$insertLinkFile = $reqLinkFileTemp;
					$insertLinkFilesSize = 'NULL';
					$insertLinkFilesExe = $reqLinkFileTempTipe;
				}

				$rekanan_peralatan->setField("UKURAN", $insertLinkFilesSize);
				$rekanan_peralatan->setField("TIPE", $insertLinkFilesExe);
				$rekanan_peralatan->setField("PATH_FILE", $insertLinkFile);

				if($rekanan_peralatan->insert())
				{
					echo '<script language="javascript">';
					echo "document.location='main/?pg=data_teknis_peralatan'";
					echo '</script>';

					echo '<script language="javascript">';
					echo "$.jGrowl('Data berhasil disimpan');";
					echo '</script>';
					/*echo '<script language="javascript">';
					echo "alert('Data berhasil disimpan')";
					echo '</script>';*/
					$alertMsg .= "Data berhasil diupdate";
				}
				else
				{
					$alertMsg .= "Update failed : ".$rekanan_peralatan->query;
				}
			}
		}
    }

	function  daftar_rekanan_teknis_peralatan_json()
	{
		$this->load->library("kauth");  $userLogin = new kauth();
		$this->load->model("RekananPeralatan");
		$rekanan_peralatan = new RekananPeralatan();

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
			$allRecord = $rekanan_peralatan->getCountByParams(array('REKANAN_ID'=>$reqId), $statement);
			$rekanan_peralatan->selectByParams(array('REKANAN_ID'=>$reqId), $_GET['iDisplayLength'], $_GET['iDisplayStart'], $statement);
			//$allRecord = 2;
		}
		else
		{
			$reqSearch = str_replace('\\', '', $reqSearch);
			$allRecord = 1;
			$rekanan_peralatan->selectByParams(array(), $_GET['iDisplayLength'], $_GET['iDisplayStart'], $statement.$reqSearch." ");

		}

		$column = array('REKANAN_PERALATAN_ID', 'NO', 'JENIS','JUMLAH','KAPASITAS','MERK', 'TAHUN','KONDISI','LOKASI','BUKTI_KEPEMILIKAN');
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
			while($rekanan_peralatan->nextRow())
			{
				$row = array();
				for ( $i=0 ; $i<count($column) ; $i++ )
				{
					if($column[$i]=='NO')		$row[] = $number;
					else						$row[] = $rekanan_peralatan->getField(trim($column[$i]));
				}
				$number++;
				$output['aaData'][] = $row;
			}
			//echo $number;
			echo json_encode( $output );
	}

	function get_data_kualifikasi_peralatan()
	{
		$this->load->library("kauth");  $userLogin = new kauth();
		$this->load->model("RekananPeralatan");
		$rekanan_peralatan = new RekananPeralatan();

		$reqId = httpFilterGet("reqId");

		/* LOGIN CHECK */
		if ($userLogin->checkUserLogin())
		{
			$userLogin->retrieveUserInfo();
		}

		if($reqId == ""){}
		else
			$statement= " AND NOT REKANAN_PERALATAN_ID IN (".$reqId.")";

		$rekanan_peralatan->selectByParams(array("REKANAN_ID" => $userLogin->userRekanan), -1, -1, $statement);
		//echo $rekanan_peralatan->query;
		//$rekanan_peralatan->selectByParams(array("REKANAN_ID" => $userLogin->userRekanan), -1, -1);
		$met = array();
		$i=0;

		while($rekanan_peralatan->nextRow()){
			$met[$i]['JENIS'] = $rekanan_peralatan->getField('JENIS');
			$met[$i]['JUMLAH'] = $rekanan_peralatan->getField('JUMLAH');
			$met[$i]['KAPASITAS'] = $rekanan_peralatan->getField('KAPASITAS');
			$met[$i]['TAHUN'] = $rekanan_peralatan->getField('TAHUN');
			$met[$i]['KONDISI'] = $rekanan_peralatan->getField('KONDISI');
			$met[$i]['BUKTI_KEPEMILIKAN'] = $rekanan_peralatan->getField('BUKTI_KEPEMILIKAN');
			$met[$i]['REKANAN_PERALATAN_ID'] = $rekanan_peralatan->getField('REKANAN_PERALATAN_ID');
			$i++;
		}
		echo json_encode($met);
	}

	function data_teknis_peralatan_ubah()
	{
		/* INCLUDE FILE */
		$this->load->library("kauth");  $userLogin = new kauth();
		$this->load->model("RekananPeralatan");
		$this->load->model("Rekanan");
		$this->load->library("FileHandler");
		$file = new FileHandler();

		/* create objects */
		$rekanan = new Rekanan();
		$rekanan_peralatan = new RekananPeralatan();

		$reqPeralatanId= $this->input->post('reqPeralatanId');
		$reqTipe= $this->input->post('reqTipe');
		$reqJml= $this->input->post('reqJml');
		$reqKapasitas= $this->input->post('reqKapasitas');
		$reqKapasitasSat= $this->input->post('reqKapasitasSat');
		$reqMerk= $this->input->post('reqMerk');
		$reqTahun= $this->input->post('reqTahun');
		$reqKondisi= $this->input->post('reqKondisi');
		$reqLokasi= $this->input->post('reqLokasi');
		$reqKepemilikan= $this->input->post('reqKepemilikan');
		$reqId= $this->input->post('reqId');
		$reqSubmit= $this->input->post('reqSubmit');
		$reqLinkFile= $_FILES['reqLinkFile'];
		$reqLinkFileTemp = $this->input->post("reqLinkFileTemp");
		$reqLinkFileTempTipe = $this->input->post("reqLinkFileTempTipe");
		$reqLinkFileTempUkuran = $this->input->post("reqLinkFileTempUkuran");
		$reqLinkFileTempNama = $this->input->post("reqLinkFileTempNama");
		$reqMode = $this->input->post("reqMode");

		$FILE_DIR = "uploads/peralatan/";

		if($reqMode== 'insert')
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
			$rekanan_peralatan->setField("REKANAN_ID",$this->ID);
			$rekanan_peralatan->setField("JUMLAH",$reqJml);
			$rekanan_peralatan->setField("KAPASITAS",$reqKapasitas);
			$rekanan_peralatan->setField("KAPASITAS_SATUAN",$reqKapasitasSat);
			$rekanan_peralatan->setField("MERK",$reqMerk);
			$rekanan_peralatan->setField("TAHUN",$reqTahun);
			$rekanan_peralatan->setField("KONDISI",$reqKondisi);
			$rekanan_peralatan->setField("LOKASI",$reqLokasi);
			$rekanan_peralatan->setField("BUKTI_KEPEMILIKAN",$reqKepemilikan);
			$rekanan_peralatan->setField("JENIS",$reqTipe);

			$rekanan_peralatan->setField("UKURAN", $insertLinkFilesSize);
			$rekanan_peralatan->setField("TIPE", $insertLinkFilesExe);
			$rekanan_peralatan->setField("PATH_FILE", $insertLinkFile);
			$rekanan_peralatan->setField("NAMA_FILE", $insertLinkFileNama);
			$rekanan_peralatan->setField('CREATED_BY', $this->USER_LOGIN_ID);

			if($rekanan_peralatan->insert())
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
			$rekanan_peralatan->setField("UKURAN", $insertLinkFilesSize);
			$rekanan_peralatan->setField("TIPE", $insertLinkFilesExe);
			$rekanan_peralatan->setField("PATH_FILE", $insertLinkFile);
			$rekanan_peralatan->setField("NAMA_FILE", $insertLinkFileNama);

			$rekanan_peralatan->setField("REKANAN_PERALATAN_ID",$reqPeralatanId);
			$rekanan_peralatan->setField("JUMLAH",$reqJml);
			$rekanan_peralatan->setField("KAPASITAS",$reqKapasitas);
			$rekanan_peralatan->setField("KAPASITAS_SATUAN",$reqKapasitasSat);
			$rekanan_peralatan->setField("MERK",$reqMerk);
			$rekanan_peralatan->setField("TAHUN",$reqTahun);
			$rekanan_peralatan->setField("KONDISI",$reqKondisi);
			$rekanan_peralatan->setField("LOKASI",$reqLokasi);
			$rekanan_peralatan->setField("BUKTI_KEPEMILIKAN",$reqKepemilikan);
			$rekanan_peralatan->setField("JENIS",$reqTipe);
			$rekanan_peralatan->setField("REKANAN_ID",$this->ID);
			$rekanan_peralatan->setField('CREATED_BY', $this->USER_LOGIN_ID);

			if($rekanan_peralatan->update())
			{
				echo "Data Berhasil di Update";
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
		$this->load->model("RekananPeralatan");

		$rekanan = new Rekanan();
		$rekanan_peralatan = new RekananPeralatan();

		$reqId= $this->input->get('reqId');

		$rekanan_peralatan->setField("REKANAN_PERALATAN_ID", $reqId);
		$rekanan_peralatan->setField("REKANAN_ID", $this->ID);

		if($rekanan_peralatan->delete())
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
