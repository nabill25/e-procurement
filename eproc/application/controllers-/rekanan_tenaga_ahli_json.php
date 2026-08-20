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

class rekanan_tenaga_ahli_json extends CI_Controller {

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

	function get_data_tenaga_ahli()
	{
		$this->load->model("RekananTenagaAhli");
		$rekanan_tenaga_ahli = new RekananTenagaAhli();

		$reqSearch = $this->input->get("reqSearch");

		$met = array();
		$i=0;

		$rekanan_tenaga_ahli->selectByParams(array("REKANAN_ID" => $this->ID), -1, -1, "AND (UPPER(A.NAMA) LIKE '%".strtoupper($reqSearch)."%')");
		while($rekanan_tenaga_ahli->nextRow())
		{
			$met[$i]['id'] = $rekanan_tenaga_ahli->getField('REKANAN_TENAGA_AHLI_ID');
			$met[$i]['text'] = $rekanan_tenaga_ahli->getField('NAMA');
			$met[$i]['NAMA'] = $rekanan_tenaga_ahli->getField('NAMA');
			$met[$i]['PENDIDIKAN'] = $rekanan_tenaga_ahli->getField('PENDIDIKAN');
			$met[$i]['KTP'] = $rekanan_tenaga_ahli->getField('KTP');
			$i++;
		}
		echo json_encode($met);
	}

	function daftar_rekanan_teknis_tenaga_json()
	{
		$this->load->library("kauth");  $userLogin = new kauth();
		$this->load->model("RekananTenagaAhli");
		$rekanan_tenaga_ahli = new RekananTenagaAhli();

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
			$allRecord = $rekanan_tenaga_ahli->getCountByParams(array('REKANAN_ID'=>$reqId), $statement);
			$rekanan_tenaga_ahli->selectByParams(array('REKANAN_ID'=>$reqId), $_GET['iDisplayLength'], $_GET['iDisplayStart'], $statement);
			//$allRecord = 2;
		}
		else
		{
			$reqSearch = str_replace('\\', '', $reqSearch);
			$allRecord = 1;
			$rekanan_tenaga_ahli->selectByParams(array(), $_GET['iDisplayLength'], $_GET['iDisplayStart'], $statement.$reqSearch." ");

		}

		$column = array('REKANAN_SERTIFIKAT_ID', 'NO', 'NAMA','PENDIDIKAN','PENGALAMAN','SERTIFIKAT');
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
			while($rekanan_tenaga_ahli->nextRow())
			{
				$row = array();
				for ( $i=0 ; $i<count($column) ; $i++ )
				{
					if($column[$i]=='NO')		$row[] = $number;
					else						$row[] = $rekanan_tenaga_ahli->getField(trim($column[$i]));
				}
				$number++;
				$output['aaData'][] = $row;
			}
			//echo $number;
			echo json_encode( $output );
	}

	function get_data_kualifikasi_tenaga_ahli()
	{
		$this->load->library("kauth");  $userLogin = new kauth();
		$this->load->model("RekananTenagaAhli");
		$this->load->model("RekananTenagaAhliPengalaman");
		$rekanan_tenaga_ahli = new RekananTenagaAhli();
		$rtap = new RekananTenagaAhliPengalaman();
		$reqId = httpFilterGet("reqId");

		/* LOGIN CHECK */
		if ($userLogin->checkUserLogin())
		{
			$userLogin->retrieveUserInfo();
		}
		if($reqId == ""){}
		else
			$statement= " AND NOT REKANAN_TENAGA_AHLI_ID IN (".$reqId.")";

		$rs = $rekanan_tenaga_ahli->selectByParams(array("REKANAN_ID" => $userLogin->userRekanan), -1, -1, $statement);
		$met = array();
		$i=0;
		$whereIn = NULL;
		foreach ($rs as $v)
		{
		//    prepare IN query
			$whereIn .= "'".$v['REKANAN_TENAGA_AHLI_ID']."',";
		}
		$whereIn = "(".trim($whereIn,',').")";
		$rsp = $rtap->selectByParamsExtended(array('REKANAN_TENAGA_AHLI_ID'=>array($whereIn,FALSE,'IN')));

		foreach ($rsp as $v) {
			$dpengalaman[$v['REKANAN_TENAGA_AHLI_ID']] .= $v['POSISI'].'#'.$v['PEKERJAAN'].'#'.$v['PERIODE'].'#'.$v['NAMA_PERUSAHAAN'].'#'.$v['PENGALAMAN'].'<br/>';
		}

		while($rekanan_tenaga_ahli->nextRow()){
			$met[$i]['NAMA'] = $rekanan_tenaga_ahli->getField('NAMA');
			$met[$i]['PENDIDIKAN'] = $rekanan_tenaga_ahli->getField('PENDIDIKAN');
			$met[$i]['PENGALAMAN'] = $dpengalaman[$rekanan_tenaga_ahli->getField('REKANAN_TENAGA_AHLI_ID')];
			$met[$i]['SERTIFIKAT'] = $rekanan_tenaga_ahli->getField('SERTIFIKAT');
			$met[$i]['REKANAN_TENAGA_AHLI_ID'] = $rekanan_tenaga_ahli->getField('REKANAN_TENAGA_AHLI_ID');
			$i++;
		}
		echo json_encode($met);
	}

	function data_teknis_tenaga_ahli_ubah()
	{
		/* INCLUDE FILE */
		$this->load->library("kauth");  $userLogin = new kauth();
		$this->load->model("RekananTenagaAhli");
		$this->load->model("RekananTenagaAhliSertifikat");
		$this->load->model("RekananTenagaAhliPengalaman");
		$this->load->model("RekananTenagaAhliPendidikan");
		$this->load->model("Pendidikan");
		$this->load->model("Rekanan");
		$this->load->library("FileHandler");
		$file = new FileHandler();

		$rekanan = new Rekanan();
		$pendidikan = new Pendidikan();
		$rekanan_tenaga_ahli = new RekananTenagaAhli();
		$rekanan_tenaga_ahli_detil = new RekananTenagaAhli();

		$reqTenagaAhliId = $this->input->post("reqTenagaAhliId");
		$reqNama = $this->input->post("reqNama");
		$reqTptLahir = $this->input->post("reqTptLahir");
		$reqTglLahir = $this->input->post("reqTglLahir");
		$reqAlamat = $this->input->post("reqAlamat");
		$reqKTP = $this->input->post("reqKTP");
		$reqNPWP = $this->input->post("reqNPWP");
		$reqId = $this->input->post("reqId");
		$reqSubmit = $this->input->post("reqSubmit");
		$submitSimpan = $this->input->post("submitSimpan");
    $reqMode = $this->input->post("reqMode");
		$reqJenisKelamin = $this->input->post("reqJenisKelamin");

		$reqPosisi = $_POST["reqPosisi"];
		$reqJumlahTahun = $_POST["reqJumlahTahun"];
		$reqPekerjaan = $_POST["reqPekerjaan"];
		$reqLama = $_POST["reqLama"];
		$reqInstansi = $_POST["reqInstansi"];
		$reqNamaPerusahaan = $_POST["reqNamaPerusahaan"];
		$reqKeahlian = $_POST["reqKeahlian"];
		$reqInstansi2 = $_POST["reqInstansi2"];
		$reqTglBerlaku = $_POST["reqTglBerlaku"] ?: '';
		$reqNoSertifikat = $_POST["reqNoSertifikat"];
		$reqSertifikatId = $_POST["reqSertifikatId"];
		$reqPendidikan = $_POST["reqPendidikan"];
		$reqJurusan = $_POST["reqJurusan"];
		// echo "<pre>"; print_r($_POST); die;
		$reqLinkFile= $_FILES['reqLinkFile'];

		$reqLinkFileTemp = $_POST["reqLinkFileTemp"];
		$reqLinkFileTempTipe = $_POST["reqLinkFileTempTipe"];
		$reqLinkFileTempUkuran = $_POST["reqLinkFileTempUkuran"];
		$reqLinkFileTempNama = $_POST["reqLinkFileTempNama"];

		$FILE_DIR = "uploads/tenaga_ahli_sertifikat/";

		$reqId = $this->ID;

		if($reqMode=='insert')
		{
			$rekanan_tenaga_ahli->setField("REKANAN_ID", $this->ID);
			$rekanan_tenaga_ahli->setField("NAMA", $reqNama);
			$rekanan_tenaga_ahli->setField("TEMPAT_LAHIR", $reqTptLahir);
			$tgllahir = "TO_DATE('".$reqTglLahir."','DD-MM-YYYY')";
			// $rekanan_tenaga_ahli->setField("TANGGAL_LAHIR", $tgllahir);
			$rekanan_tenaga_ahli->setField("TANGGAL_LAHIR", "TO_DATE('".$reqTglLahir."','DD-MM-YYYY')");
			$rekanan_tenaga_ahli->setField("ALAMAT", $reqAlamat);
			$rekanan_tenaga_ahli->setField("KTP", $reqKTP);
      $rekanan_tenaga_ahli->setField("NPWP", $reqNPWP);
			$rekanan_tenaga_ahli->setField("JENIS_KELAMIN", $reqJenisKelamin);
			$rekanan_tenaga_ahli->setField('CREATED_BY', $this->USER_LOGIN_ID);

			if ($rekanan_tenaga_ahli->insert())
			{
				$id = $rekanan_tenaga_ahli->id;

				for($i=0; $i<count($reqPendidikan);$i++)
				{
						if($reqPendidikan[$i] == "")
						{}
						else
						{
							$pendidikan = new RekananTenagaAhliPendidikan();
							$pendidikan->setField("REKANAN_TENAGA_AHLI_ID", $id);
							$pendidikan->setField("PENDIDIKAN", $reqPendidikan[$i]);
							$pendidikan->setField("JURUSAN", $reqJurusan[$i]);
							$pendidikan->setField('CREATED_BY', $this->USER_LOGIN_ID);
							$pendidikan->insert();
							unset($pendidikan);
						}
				}

				for($i=0; $i<count($reqPosisi);$i++)
				{
						if($reqPosisi[$i] == "")
						{}
						else
						{
							$pengalaman = new RekananTenagaAhliPengalaman();
							$pengalaman->setField("REKANAN_TENAGA_AHLI_ID", $id);
							$pengalaman->setField("POSISI", $reqPosisi[$i]);
							$pengalaman->setField("PENGALAMAN", $reqJumlahTahun[$i]);
							$pengalaman->setField("PEKERJAAN", $reqPekerjaan[$i]);
							$pengalaman->setField("PERIODE", $reqLama[$i]);
							$pengalaman->setField("INSTANSI", $reqInstansi[$i]);
							$pengalaman->setField("NAMA_PERUSAHAAN", $reqNamaPerusahaan[$i]);
							$pengalaman->setField('CREATED_BY', $this->USER_LOGIN_ID);

							$pengalaman->insert();
							unset($pengalaman);
						}
				}
				for($i=0; $i<count($reqKeahlian);$i++)
				{
					if($reqKeahlian[$i] == "")
					{}
					else
					{
						$sertifikat = new RekananTenagaAhliSertifikat();
						$sertifikat->setField("REKANAN_TENAGA_AHLI_ID", $id);
						$sertifikat->setField("KEAHLIAN", $reqKeahlian[$i]);
						$sertifikat->setField("NOMOR", $reqNoSertifikat[$i]);
						$sertifikat->setField("INSTANSI", $reqInstansi2[$i]);
						if ($reqTglBerlaku[$i]) {
						$sertifikat->setField("TANGGAL_BERLAKU", "TO_DATE('".$reqTglBerlaku[$i]."','DD-MM-YYYY')");
						} else {
						$sertifikat->setField("TANGGAL_BERLAKU", "null");
						}
						$renameFile = md5(date("dmYHis").$reqLinkFile['name'][$i].$this->ID).".".getExtension($reqLinkFile['name'][$i]);
						if($file->uploadToDirArray('reqLinkFile', $FILE_DIR, $renameFile, $i))
						{
							$insertLinkFilesSize = $file->uploadedSize;
							$insertLinkFilesExe =  $file->uploadedExtension;
							$insertLinkFile =  $renameFile;
							$insertLinkFileNama = $reqLinkFile['name'][$i];
						}
						else
						{
							$insertLinkFile =  $reqLinkFileTemp[$i];
							$insertLinkFilesExe =  $reqLinkFileTempTipe[$i];
							$insertLinkFilesSize = $reqLinkFileTempUkuran[$i];
							$insertLinkFileNama = $reqLinkFileTempNama[$i];
						}
						$sertifikat->setField("PATH_FILE", $insertLinkFile);
						$sertifikat->setField("TIPE", $insertLinkFilesExe);
						$sertifikat->setField("UKURAN", $insertLinkFilesSize);
						$sertifikat->setField("NAMA_FILE", $insertLinkFileNama);
						$sertifikat->setField('CREATED_BY', $this->USER_LOGIN_ID);

						$sertifikat->insert();
						unset($sertifikat);
					}
				}

				// for($i=0; $i<count($reqKeahlian);$i++)
				// {
				// 	if($reqKeahlian[$i] == "")
				// 	{}
				// 	else
				// 	{

				// 		$sertifikat = new RekananTenagaAhliSertifikat();
				// 		$sertifikat->setField("REKANAN_TENAGA_AHLI_ID", $id);
				// 		$sertifikat->setField("KEAHLIAN", $reqKeahlian[$i]);
				// 		$sertifikat->setField("NOMOR", $reqNoSertifikat[$i]);
				// 		$sertifikat->insert();
				// 		unset($sertifikat);
				// 	}
				// }
				echo "Data berhasil di Simpan";
			}
		}
		else
		{
			$rekanan_tenaga_ahli->setField("REKANAN_ID", $this->ID);
			$rekanan_tenaga_ahli->setField("NAMA", $reqNama);
			$rekanan_tenaga_ahli->setField("TEMPAT_LAHIR", $reqTptLahir);
			$rekanan_tenaga_ahli->setField("TANGGAL_LAHIR", "TO_DATE('".$reqTglLahir."','DD-MM-YYYY')");
			$rekanan_tenaga_ahli->setField("ALAMAT", $reqAlamat);
			$rekanan_tenaga_ahli->setField("KTP", $reqKTP);
			$rekanan_tenaga_ahli->setField("NPWP", $reqNPWP);
			$rekanan_tenaga_ahli->setField("JENIS_KELAMIN", $reqJenisKelamin);
			$rekanan_tenaga_ahli->setField("REKANAN_TENAGA_AHLI_ID", $reqTenagaAhliId);
			$rekanan_tenaga_ahli->setField('CREATED_BY', $this->USER_LOGIN_ID);
			if($rekanan_tenaga_ahli->update())
			{
				$id = $reqTenagaAhliId;

				$rekanan_tenaga_ahli_detil->setField("REKANAN_TENAGA_AHLI_ID", $reqTenagaAhliId);
				$rekanan_tenaga_ahli_detil->delete_spp();

				//$id = $rekanan_tenaga_ahli->id;

				for($i=0; $i<count($reqPendidikan);$i++)
				{
					if($reqPendidikan[$i] == "")
					{}
					else
					{
						$pendidikan = new RekananTenagaAhliPendidikan();
						$pendidikan->setField("REKANAN_TENAGA_AHLI_ID", $id);
						$pendidikan->setField("PENDIDIKAN", $reqPendidikan[$i]);
						$pendidikan->setField("JURUSAN", $reqJurusan[$i]);
						$pendidikan->setField('CREATED_BY', $this->USER_LOGIN_ID);
						$pendidikan->insert();
						unset($pendidikan);
					}
				}

				for($i=0; $i<count($reqPosisi);$i++)
				{
					if($reqPosisi[$i] == "")
					{}
					else
					{
						$pengalaman = new RekananTenagaAhliPengalaman();
						$pengalaman->setField("REKANAN_TENAGA_AHLI_ID", $id);
						$pengalaman->setField("POSISI", $reqPosisi[$i]);
						$pengalaman->setField("PENGALAMAN", $reqJumlahTahun[$i]);
						$pengalaman->setField("PEKERJAAN", $reqPekerjaan[$i]);
						$pengalaman->setField("PERIODE", $reqLama[$i]);
						$pengalaman->setField("INSTANSI", $reqInstansi[$i]);
						$pengalaman->setField("NAMA_PERUSAHAAN", $reqNamaPerusahaan[$i]);
						$pengalaman->setField('CREATED_BY', $this->USER_LOGIN_ID);
						$pengalaman->insert();
						unset($pengalaman);
					}
				}

				for($i=0; $i<count($reqKeahlian);$i++)
				{
					if($reqKeahlian[$i] == "")
					{}
					else
					{
						$sertifikat = new RekananTenagaAhliSertifikat();
						$sertifikat->setField("REKANAN_TENAGA_AHLI_ID", $id);
						$sertifikat->setField("KEAHLIAN", $reqKeahlian[$i]);
						$sertifikat->setField("NOMOR", $reqNoSertifikat[$i]);
						$sertifikat->setField("INSTANSI", $reqInstansi2[$i]);
						if ($reqTglBerlaku[$i]) {
							$sertifikat->setField("TANGGAL_BERLAKU", "TO_DATE('".$reqTglBerlaku[$i]."','DD-MM-YYYY')");
						} else {
							$sertifikat->setField("TANGGAL_BERLAKU", "null");
						}
						$renameFile = md5(date("dmYHis").$reqLinkFile['name'][$i].$this->ID).".".getExtension($reqLinkFile['name'][$i]);
						if($file->uploadToDirArray('reqLinkFile', $FILE_DIR, $renameFile, $i))
						{
							$insertLinkFilesSize = $file->uploadedSize;
							$insertLinkFilesExe =  $file->uploadedExtension;
							$insertLinkFile =  $renameFile;
							$insertLinkFileNama = $reqLinkFile['name'][$i];
						}
						else
						{
							$insertLinkFile =  $reqLinkFileTemp[$i];
							$insertLinkFilesExe =  $reqLinkFileTempTipe[$i];
							$insertLinkFilesSize = $reqLinkFileTempUkuran[$i];
							$insertLinkFileNama = $reqLinkFileTempNama[$i];
						}
						$sertifikat->setField("PATH_FILE", $insertLinkFile);
						$sertifikat->setField("TIPE", $insertLinkFilesExe);
						$sertifikat->setField("UKURAN", $insertLinkFilesSize);
						$sertifikat->setField("NAMA_FILE", $insertLinkFileNama);
						$sertifikat->setField('CREATED_BY', $this->USER_LOGIN_ID);

						$sertifikat->insert();
						unset($sertifikat);
					}
				}

		echo "Data berhasil di simpan";
		}
	  }
	}

	function delete()
	{
		$this->load->model("RekananTenagaAhli");

		$reqId = $this->input->get("reqId");

		$rekanan_detil = new RekananTenagaAhli();
		$rekanan_detil->setField("REKANAN_TENAGA_AHLI_ID", $reqId);

		if($rekanan_detil->delete_spp())
		{
			$rekanan_tenaga_ahli = new RekananTenagaAhli();

			$rekanan_tenaga_ahli->setField("REKANAN_TENAGA_AHLI_ID", $reqId);
			$rekanan_tenaga_ahli->setField('REKANAN_ID', $this->ID);
			$rekanan_tenaga_ahli->delete();

			echo 'Data berhasil dihapus.';
		}
		else
		{
			echo 'Data gagal dihapus.';
		}

	}

}
?>
