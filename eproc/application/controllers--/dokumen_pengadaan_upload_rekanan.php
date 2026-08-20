<?php
/* INCLUDE FILE */
include_once("functions/string.func.php");
include_once("functions/default.func.php");
include_once("functions/date.func.php");
include_once("functions/pdf.func.php");
include_once("functions/encrypt2.func.php");

defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * @package 	eProcurement Application
 * @author 		eproc2025
 * @since 		25. Version 3.1
 *
 */

class dokumen_pengadaan_upload_rekanan extends CI_Controller {

	function __construct() {
		parent::__construct(); $this->db->query("SET TIME ZONE 'Asia/Jakarta'");

		if (!$this->kauth->getInstance()->hasIdentity())
		{
			//redirect('login');
		}

		$this->ID = $this->kauth->getInstance()->getIdentity()->REKANAN_ID;

		$this->USER_LOGIN_ID =  $this->kauth->getInstance()->getIdentity()->USER_LOGIN_ID;
		$this->USER_LOGIN =  $this->kauth->getInstance()->getIdentity()->USER_LOGIN;
		$this->USER_NAMA =  $this->kauth->getInstance()->getIdentity()->USER_NAMA;
		$this->USER_TYPE_ID =  $this->kauth->getInstance()->getIdentity()->USER_TYPE_ID;
		$this->REKANAN_ID =  $this->kauth->getInstance()->getIdentity()->REKANAN_ID;
		$this->UNIT_KERJA_ID =  $this->kauth->getInstance()->getIdentity()->UNIT_KERJA_ID;
		$this->NIP =  $this->kauth->getInstance()->getIdentity()->NIP;
		$this->LOGIN_TIME = $this->kauth->getInstance()->getIdentity()->LOGIN_TIME;
		$this->LOGIN_DATE = $this->kauth->getInstance()->getIdentity()->LOGIN_DATE;
		$this->REKANAN = $this->kauth->getInstance()->getIdentity()->REKANAN;
		$this->REKANAN_KODE = $this->kauth->getInstance()->getIdentity()->REKANAN_KODE;
		$this->REKANAN_PKP = $this->kauth->getInstance()->getIdentity()->REKANAN_PKP;
		$this->REKANAN_NPWP = $this->kauth->getInstance()->getIdentity()->REKANAN_NPWP;
		$this->REKANAN_STATUS_PERUSAHAAN = $this->kauth->getInstance()->getIdentity()->STATUS_PERUSAHAAN;
		$this->REKANAN_STATUS_VALIDASI = $this->kauth->getInstance()->getIdentity()->STATUS_VALIDASI;
	}

	function json()
	{

		$this->load->model("PaketDokumen");
		$this->load->library("FileHandler");

		$paket_dokumen = new PaketDokumen();
		$file = new FileHandler();

		$reqMode = $this->input->post("reqMode");
		$reqId = $this->input->post("reqId");
		$reqCetak= $this->input->post('reqCetak');
		$reqNamaDokumen= $this->input->post('reqNamaDokumen');
		$reqDokumenKe = $this->input->post('reqDokumenKe');
		$reqKeterangan= $this->input->post('reqKeterangan');
		$reqJenisDokumen= $this->input->post('reqJenisDokumen');
		$reqLinkFile= $_FILES['reqLinkFile'.$reqDokumenKe];
		$reqBayar= $this->input->post('reqBayar');
		$reqToken= $this->input->post('reqToken');
		$reqDokumenId = httpFilterGet('reqDokumenId');
		$submitSimpan= $this->input->post('submitSimpan');
		$FILE_DIR = "uploads/penawaran/";

		$renameFile = md5(date("dmYHis").$reqLinkFile['name'].$this->ID).".".getExtension($reqLinkFile['name']);
		if($file->uploadToDir('reqLinkFile'.$reqDokumenKe, $FILE_DIR, $renameFile))
		{
			$paket_dokumen->setField("REKANAN_USER_ID", $this->ID);
			$paket_dokumen->setField("PAKET_ID", $reqId);
			$paket_dokumen->setField("NAMA", $reqNamaDokumen);
			$paket_dokumen->setField("UKURAN", $file->uploadedSize);
			$paket_dokumen->setField("TIPE", $file->uploadedExtension);
			$paket_dokumen->setField("PATH_FILE", $file->uploadedFileName);
			$paket_dokumen->setField("JENIS_DOKUMEN", $reqJenisDokumen);
			$paket_dokumen->setField("KETERANGAN", $reqKeterangan);
			$paket_dokumen->setField("FILE_PASSWORD", $reqToken);
			$paket_dokumen->setField('CREATED_BY', $this->USER_LOGIN_ID);
			$paket_dokumen->insert();
			echo "Dokumen berhasil diupload.";
		}
		else
			echo "Dokumen gagal diupload.";
	}

	function upload_validasi()
	{
		// echo "string"; die();
		ini_set('memory_limit', '-1');

		$this->load->model("PaketDokumen");
		$this->load->model("PaketRekanan");
		$this->load->model("PaketTahap");
		$this->load->library("FileHandler");
		$this->load->library("paketinfo"); $paketInfo = new paketinfo();

		$verifyToken = md5('unique' . $_POST['timestamp']);
		if ($_POST['token'] == $verifyToken)
		{}
		else
			exit;

		$paket_rekanan = new PaketRekanan();
		$paket_dokumen = new PaketDokumen();
		$paket_tahap = new PaketTahap();
		$paket_tahap_metode = new PaketTahap();
		$file = new FileHandler();

		$reqMode = $this->input->post("reqMode");
		$reqId = $this->input->post("reqId");
		$reqCetak= $this->input->post('reqCetak');
		$reqNamaDokumen= $this->input->post('reqNamaDokumen');
		$reqNamaDokumen2= $this->input->post('reqNamaDokumen');
		$reqDokumenKe = $this->input->post('reqDokumenKe');
		$reqKeterangan= $this->input->post('reqKeterangan');
		$reqJenisDokumen= $this->input->post('reqJenisDokumen');
		$reqLinkFile= $_FILES['Filedata'];
		$reqBayar= $this->input->post('reqBayar');
		$reqDokumenId = httpFilterGet('reqDokumenId');
		$submitSimpan= $this->input->post('submitSimpan');
		$reqPengirim = $this->input->post('reqPengirim');

		$FILE_DIR = "uploads/penawaran/";

		$paketInfo->getPaket($reqId);
		$paket_rekanan->selectByParamsPaketLelang(array("PAKET_ID" => $reqId, "REKANAN_ID" => $reqPengirim));
		$paket_rekanan->firstRow();

		/* CEK 1 SAMPUL ATAU 2 SAMPUL */
		if($paketInfo->sistem_sampul == "2")
		{
			if($reqJenisDokumen == "PENAWARAN_HARGA") {
				$reqToken= sha1("2_".$paketInfo->pr_group_number.$paket_rekanan->getField("TANGGAL_DAFTAR_ENKRIPSI").$paket_rekanan->getField("KODE_REKANAN").$reqId.$paketInfo->user_login_id).PASS_END_PENAWARAN;
			}
			else {
				$reqToken= sha1("1_".$paketInfo->pr_group_number.$paket_rekanan->getField("TANGGAL_DAFTAR_ENKRIPSI").$paket_rekanan->getField("KODE_REKANAN").$reqId.$paketInfo->user_login_id).PASS_END_PENAWARAN;
			}
		}
		else {
			$reqToken= sha1($paketInfo->pr_group_number.$paket_rekanan->getField("TANGGAL_DAFTAR_ENKRIPSI").$paket_rekanan->getField("KODE_REKANAN").$reqId.$paketInfo->user_login_id).PASS_END_PENAWARAN;
		}


		// echo $reqToken; die();

		/* VALIDASI WAKTU HABIS */
		$jenis_tahap = $paket_tahap_metode->getJenisTahapById($reqId);
		$arrDokumenPenawaran  = DOKUMEN_PENAWARAN; // ikn 2019.08 tender cepat
		$aktif_dok_penawaran1 = $paket_tahap->getCountByParamsAktif(array("URUT" => $arrDokumenPenawaran[$jenis_tahap], "PAKET_ID" => $reqId, "TAMPILKAN" => 1, "HADIR" => 0));

		if($aktif_dok_penawaran1 == 0)
		{
			echo "Waktu pemasukan / upload penawaran telah berakhir. Dokumen penawaran gagal diupload.";
			return;
		}

		// $renameFile = md5(date("dmYHis").$reqLinkFile['name'].$reqPengirim).".".getExtension($reqLinkFile['name']); // Parameter ini nama file nya di enkripsi
		$clean = preg_replace('/[^A-Za-z0-9]/', '_', $reqNamaDokumen2);
		$hash = hash('crc32', $reqPengirim.date("dmYHis"));
		$renameFile = substr($clean, 0, 75).'_'.$reqPengirim.'_'.$hash.".".getExtension($reqLinkFile['name']);
		if($file->uploadToDir('Filedata', $FILE_DIR, $renameFile))
		{
			//password for the pdf file
			$password = $reqToken;
			//name of the original file (unprotected)
			$origFile = $FILE_DIR.$renameFile;
			//name of the destination file (password protected and printing rights removed)
			// $destFile = $FILE_DIR."enc".$renameFile;
			$destFile = $FILE_DIR."enc_".$renameFile;
			//encrypt the book and create the protected file
			//unlink($origFile);

			echo "berhasil diupload.";
			if(pdfEncrypt($origFile, $password, $destFile ))
			{
				unlink($origFile);

				$paket_dokumen->setField("REKANAN_USER_ID", $reqPengirim);
				$paket_dokumen->setField("PAKET_ID", $reqId);
				$paket_dokumen->setField("NAMA", $reqNamaDokumen);
				$paket_dokumen->setField("UKURAN", $file->uploadedSize);
				$paket_dokumen->setField("TIPE", $file->uploadedExtension);
				$paket_dokumen->setField("PATH_FILE", "enc_".$renameFile);
				$paket_dokumen->setField("JENIS_DOKUMEN", $reqJenisDokumen);
				$paket_dokumen->setField("KETERANGAN", $reqLinkFile['name']);
				$paket_dokumen->setField("PARENT_ID", '0');
				$paket_dokumen->setField('CREATED_BY', $this->USER_LOGIN_ID);
				$paket_dokumen->insert();
				// echo $paket_dokumen->query(); die();
				echo "berhasil diupload.";

			}
			else
			{
				unlink($origFile);
				echo "gagal diupload.";
			}
		}
	}

	function upload_validasi_noncrypt()
	{
		// echo "string"; die();
		ini_set('memory_limit', '-1');

		$this->load->model("PaketDokumen");
		$this->load->model("PaketRekanan");
		$this->load->model("PaketTahap");
		$this->load->library("FileHandler");
		$this->load->library("paketinfo"); $paketInfo = new paketinfo();

		$verifyToken = md5('unique' . $_POST['timestamp']);
		if ($_POST['token'] == $verifyToken)
		{}
		else
			exit;

		$paket_rekanan = new PaketRekanan();
		$paket_dokumen = new PaketDokumen();
		$paket_tahap = new PaketTahap();
		$paket_tahap_metode = new PaketTahap();
		$file = new FileHandler();

		$reqMode = $this->input->post("reqMode");
		$reqId = $this->input->post("reqId");
		$reqCetak= $this->input->post('reqCetak');
		$reqNamaDokumen= $this->input->post('reqNamaDokumen');
		$reqDokumenKe = $this->input->post('reqDokumenKe');
		$reqKeterangan= $this->input->post('reqKeterangan');
		$reqJenisDokumen= $this->input->post('reqJenisDokumen');
		$reqLinkFile= $_FILES['Filedata'];
		$reqBayar= $this->input->post('reqBayar');
		$reqDokumenId = httpFilterGet('reqDokumenId');
		$submitSimpan= $this->input->post('submitSimpan');
		$reqPengirim = $this->input->post('reqPengirim');

		$FILE_DIR = "uploads/penawaran/";

		/* VALIDASI WAKTU HABIS */
		$jenis_tahap = $paket_tahap_metode->getJenisTahapById($reqId);
		$arrDokumenPenawaran  = UPLOAD_DOKUMEN_KUALIFIKASI; // ikn 2019.08 tender cepat
		$aktif_dok_penawaran1 = $paket_tahap->getCountByParamsAktif(array("URUT" => $arrDokumenPenawaran[$jenis_tahap], "PAKET_ID" => $reqId, "TAMPILKAN" => 1, "HADIR" => 0));

		if($aktif_dok_penawaran1 == 0)
		{
			echo "Waktu pemasukan / upload penawaran telah berakhir. Dokumen penawaran gagal diupload.";
			return;
		}

		// $renameFile = md5(date("dmYHis").$reqLinkFile['name'].$reqPengirim).".".getExtension($reqLinkFile['name']);
		$hash = hash('crc32', $reqPengirim.date("dmYHis"));
		$renameFile = substr($reqNamaDokumen, 0, 75).'_'.$reqPengirim.'_'.$hash.".".getExtension($reqLinkFile['name']);

		if($file->uploadToDir('Filedata', $FILE_DIR, $renameFile))
		{
			$paket_dokumen->setField("REKANAN_USER_ID", $reqPengirim);
			$paket_dokumen->setField("PAKET_ID", $reqId);
			$paket_dokumen->setField("NAMA", $reqNamaDokumen);
			$paket_dokumen->setField("UKURAN", $file->uploadedSize);
			$paket_dokumen->setField("TIPE", $file->uploadedExtension);
			$paket_dokumen->setField("PATH_FILE", $renameFile);
			$paket_dokumen->setField("JENIS_DOKUMEN", $reqJenisDokumen);
			$paket_dokumen->setField("KETERANGAN", $reqLinkFile['name']);
			$paket_dokumen->setField("PARENT_ID", '0');
			$paket_dokumen->setField('CREATED_BY', $this->USER_LOGIN_ID);
			$paket_dokumen->insert();
			echo "berhasil diupload.";
		}
		else
		{
			echo "gagal diupload.";
		}
	}

	function upload()
	{

		$this->load->model("PaketDokumen");
		$this->load->library("FileHandler");

		$paket_dokumen = new PaketDokumen();
		$file = new FileHandler();

		$reqMode = $this->input->post("reqMode");
		$reqId = $this->input->post("reqId");
		$reqCetak= $this->input->post('reqCetak');
		$reqNamaDokumen= $this->input->post('reqNamaDokumen');
		$reqDokumenKe = $this->input->post('reqDokumenKe');
		$reqKeterangan= $this->input->post('reqKeterangan');
		$reqJenisDokumen= $this->input->post('reqJenisDokumen');
		$reqLinkFile= $_FILES['reqLinkFile'.$reqDokumenKe];
		$reqBayar= $this->input->post('reqBayar');
		$reqDokumenId = httpFilterGet('reqDokumenId');
		$submitSimpan= $this->input->post('submitSimpan');
		$FILE_DIR = "uploads/penawaran/";
		$reqToken= md5($this->ID.$reqId);

		$renameFile = md5(date("dmYHis").$reqLinkFile['name'].$this->ID).".".getExtension($reqLinkFile['name']);
		if($file->uploadToDir('reqLinkFile'.$reqDokumenKe, $FILE_DIR, $renameFile))
		{
			//password for the pdf file
			$password = $reqToken;
			//name of the original file (unprotected)
			$origFile = $FILE_DIR.$renameFile;
			//name of the destination file (password protected and printing rights removed)
			$destFile = $FILE_DIR."enc".$renameFile;
			//encrypt the book and create the protected file
			$paket_dokumen->setField("REKANAN_USER_ID", $this->ID);
			$paket_dokumen->setField("PAKET_ID", $reqId);
			$paket_dokumen->setField("NAMA", $reqNamaDokumen);
			$paket_dokumen->setField("UKURAN", $file->uploadedSize);
			$paket_dokumen->setField("TIPE", $file->uploadedExtension);
			$paket_dokumen->setField("PATH_FILE", "enc".$renameFile);
			$paket_dokumen->setField("JENIS_DOKUMEN", $reqJenisDokumen);
			$paket_dokumen->setField("KETERANGAN", $reqLinkFile['name']);
			$paket_dokumen->setField('CREATED_BY', $this->USER_LOGIN_ID);
			$paket_dokumen->insert();

			/*if(pdfEncrypt($origFile, $password, $destFile ))
			{
				unlink($origFile);

				$paket_dokumen->setField("REKANAN_USER_ID", $this->ID);
				$paket_dokumen->setField("PAKET_ID", $reqId);
				$paket_dokumen->setField("NAMA", $reqNamaDokumen);
				$paket_dokumen->setField("UKURAN", $file->uploadedSize);
				$paket_dokumen->setField("TIPE", $file->uploadedExtension);
				$paket_dokumen->setField("PATH_FILE", "enc".$renameFile);
				$paket_dokumen->setField("JENIS_DOKUMEN", $reqJenisDokumen);
				$paket_dokumen->setField("KETERANGAN", $reqLinkFile['name']);
				$paket_dokumen->insert();

				echo "Dokumen berhasil diupload.";

			}
			else
				echo "Dokumen gagal diupload.";*/
		}
	}

	function add_administrasi()
	{

		$this->load->model("RekananEvaluasiAdminTawar");
		$this->load->library("FileHandler");

		$reqPaketRekananId = $_POST["reqPaketRekananId"];
		$reqPaketEvaluasiId = $_POST["reqPaketEvaluasiId"];
		$reqUraian = $_POST["reqUraian"];

		$tidak_ada = 0;
		for($i=0;$i<count($reqPaketRekananId);$i++)
		{
			if(trim($reqUraian[$i]) == "")
				$tidak_ada++;

			$rekanan_evaluasi_admin = new RekananEvaluasiAdminTawar();
			$check = $rekanan_evaluasi_admin->getCountByParams(array("PAKET_REKANAN_ID" => $reqPaketRekananId[$i], "PAKET_EVAL_ADMIN_TAWAR_ID" => $reqPaketEvaluasiId[$i]));
			if($check == 0)
			{
				if(trim($reqUraian[$i]) == "")
				{}
				else
				{
					$rekanan_evaluasi_admin->setField("URAIAN", $reqUraian[$i]);
					$rekanan_evaluasi_admin->setField("PAKET_REKANAN_ID", $reqPaketRekananId[$i]);
					$rekanan_evaluasi_admin->setField("PAKET_EVAL_ADMIN_TAWAR_ID", $reqPaketEvaluasiId[$i]);
					$rekanan_evaluasi_admin->insertUraian();
				}
			}
			else
			{
				$rekanan_evaluasi_admin->setField("URAIAN", $reqUraian[$i]);
				$rekanan_evaluasi_admin->setField("PAKET_REKANAN_ID", $reqPaketRekananId[$i]);
				$rekanan_evaluasi_admin->setField("PAKET_EVAL_ADMIN_TAWAR_ID", $reqPaketEvaluasiId[$i]);
				$rekanan_evaluasi_admin->updateUraian();
			}
			unset($rekanan_evaluasi_admin);
		}

		if($tidak_ada > 0)
			echo "Lengkapi data terlebih dahulu.";
		else
			echo "1";

	}

	function email()
	{
		$this->load->library("FileHandler");
		$this->load->library("paketinfo"); $paketInfo = new paketinfo();

		$this->load->library("KMail");
		$this->load->model("PaketRekanan");
		$this->load->model("PaketRekananPassword");

		$reqId = $this->input->get("reqId");

		$paket_rekanan = new PaketRekanan();
		$paket_rekanan_password = new PaketRekananPassword();
		$paket_rekanan->selectByParamsEmail(array("A.REKANAN_ID" => $this->ID, "A.PAKET_ID" => $reqId));
		$paket_rekanan->firstRow();

		$paketInfo->getPaket($reqId);

		/* BUAT FILE */
		/* CEK 1 FILE ATAU 2 FILE */
		if($paketInfo->sistem_sampul == "2")
		{
			// MD5 ganti ke sha1 2021-10-25
			$reqFilename  = FILENAME_PENAWARAN."1_".$paketInfo->pr_group_number."-".$paket_rekanan->getField("KODE_REKANAN");
			$reqToken	  = sha1("1_".$paketInfo->pr_group_number.$paket_rekanan->getField("TANGGAL_DAFTAR_ENKRIPSI").$paket_rekanan->getField("KODE_REKANAN").$reqId.$paketInfo->user_login_id).PASS_END_PENAWARAN;

			$reqFile = "uploads/penawaran/".$reqFilename.".cert";
			$myfile = fopen($reqFile, "w");
			$myfile = fopen($reqFile, "w") or die("Unable to open file!");
			$txt = $reqToken;
			fwrite($myfile, $txt);
			fclose($myfile);

			$reqFilename2 = FILENAME_PENAWARAN."2_".$paketInfo->pr_group_number."-".$paket_rekanan->getField("KODE_REKANAN");
			$reqToken2	  = sha1("2_".$paketInfo->pr_group_number.$paket_rekanan->getField("TANGGAL_DAFTAR_ENKRIPSI").$paket_rekanan->getField("KODE_REKANAN").$reqId.$paketInfo->user_login_id).PASS_END_PENAWARAN;

			$reqFile2 = "uploads/penawaran/".$reqFilename2.".cert";
			$myfile2 = fopen($reqFile2, "w");
			$myfile2 = fopen($reqFile2, "w") or die("Unable to open file!");
			$txt2 = $reqToken2;
			fwrite($myfile2, $txt2);
			fclose($myfile2);


			$mail = new KMail();
			$mail->AddAddress($paket_rekanan->getField("EMAIL") , $paket_rekanan->getField("NAMA"));
			$mail->Subject  =  "Konfirmasi Upload Dokumen Penawaran Sistem 2(dua) File";
			$mail->AddAttachment($reqFile, $reqFilename.".cert");
			$mail->AddAttachment($reqFile2, $reqFilename2.".cert");

			if($paketInfo->bahasa == "EN")
				$link_email = "dokumen_penawaran_upload2_en";
			else
				$link_email = "dokumen_penawaran_upload2";


			$body = file_get_contents(SYSTEM_URL_EMAIL."/main/loadUrl/email/".$link_email."/".$reqId."/".$this->ID);
			$mail->MsgHTML($body);
			if($mail->Send())
			{
				unlink($reqFile);
				unlink($reqFile2);

				// simpan password ke database untuk penyedia cek file2 sebelum dikirim
				$paket_rekanan_password->setField("PAKET_REKANAN_ID", $paket_rekanan->getField("PAKET_REKANAN_ID"));
				$paket_rekanan_password->setField("PENAWARAN_PASSWORD", $reqToken);
				$paket_rekanan_password->setField("PENAWARAN_PASSWORD2", $reqToken2);
				$paket_rekanan_password->setField("CREATED_BY", $this->ID);
				$paket_rekanan_password->insert();
				// simpan password ke database untuk penyedia cek file2 sebelum dikirim

				echo 'Email berhasil dikirim.';
			}
			else
				echo "Email gagal dikirim.";

			unset($mail);


		}
		else
		{
			$reqFilename= FILENAME_PENAWARAN.$paketInfo->pr_group_number."-".$paket_rekanan->getField("KODE_REKANAN");
			$reqToken= sha1($paketInfo->pr_group_number.$paket_rekanan->getField("TANGGAL_DAFTAR_ENKRIPSI").$paket_rekanan->getField("KODE_REKANAN").$reqId.$paketInfo->user_login_id).PASS_END_PENAWARAN;

			$reqFile = "uploads/penawaran/".$reqFilename.".cert";
			$myfile = fopen($reqFile, "w");
			$myfile = fopen($reqFile, "w") or die("Unable to open file!");
			$txt = $reqToken;
			fwrite($myfile, $txt);
			fclose($myfile);

			$mail = new KMail();
			$mail->AddAddress($paket_rekanan->getField("EMAIL") , $paket_rekanan->getField("NAMA"));
			$mail->Subject  =  "Konfirmasi Upload Dokumen Penawaran";
			$mail->AddAttachment($reqFile, $reqFilename.".cert");

			if($paketInfo->bahasa == "EN")
				$link_email = "dokumen_penawaran_upload_en";
			else
				$link_email = "dokumen_penawaran_upload";

			$body = file_get_contents(SYSTEM_URL_EMAIL."/main/loadUrl/email/".$link_email."/".$reqId."/".$this->ID);
			$mail->MsgHTML($body);
			if($mail->Send())
			{
				unlink($reqFile);

				// simpan password ke database untuk penyedia cek file2 sebelum dikirim
				$paket_rekanan_password->setField("PAKET_REKANAN_ID", $paket_rekanan->getField("PAKET_REKANAN_ID"));
				$paket_rekanan_password->setField("PENAWARAN_PASSWORD", $reqToken);
				$paket_rekanan_password->setField("PENAWARAN_PASSWORD2", '');
				$paket_rekanan_password->setField("CREATED_BY", $this->ID);
				$paket_rekanan_password->insert();
				// simpan password ke database untuk penyedia cek file2 sebelum dikirim

				echo 'Email berhasil dikirim.';
			}
			else
				echo "Email gagal dikirim.";

			unset($mail);
		}

	}

	function upload_evaluasi()
	{

		$this->load->model("PaketDokumen");
		$this->load->library("FileHandler");

		$paket_dokumen = new PaketDokumen();
		$file = new FileHandler();

		$reqMode = $this->input->post("reqMode");
		$reqId = $this->input->post("reqId");
		$reqCetak= $this->input->post('reqCetak');
		$reqNamaDokumen= $this->input->post('reqNamaDokumen');
		$reqDokumenKe = $this->input->post('reqDokumenKe');
		$reqRekananId = $this->input->post('reqRekananId');
		$reqKeterangan= $this->input->post('reqKeterangan');
		$reqJenisDokumen= $this->input->post('reqJenisDokumen');
		$reqLinkFile= $_FILES['reqLinkFile'.$reqRekananId];
		$reqBayar= $this->input->post('reqBayar');
		$reqDokumenId = httpFilterGet('reqDokumenId');
		$submitSimpan= $this->input->post('submitSimpan');
		$FILE_DIR = "uploads/penawaran/";
		$renameFile = md5(date("dmYHis").$reqLinkFile['name'].$this->USER_LOGIN_ID).".".getExtension($reqLinkFile['name']);

		if($file->uploadToDir('reqLinkFile'.$reqRekananId, $FILE_DIR, $renameFile))
		{

			$paket_dokumen->setField("REKANAN_USER_ID", "NULL");
			$paket_dokumen->setField("PAKET_ID", $reqId);
			$paket_dokumen->setField("NAMA", $reqNamaDokumen);
			$paket_dokumen->setField("UKURAN", $file->uploadedSize);
			$paket_dokumen->setField("TIPE", $file->uploadedExtension);
			$paket_dokumen->setField("PATH_FILE", $renameFile);
			$paket_dokumen->setField("JENIS_DOKUMEN", $reqJenisDokumen);
			$paket_dokumen->setField("KETERANGAN", $reqLinkFile['name']);
			$paket_dokumen->setField("FILE_PASSWORD", null);
			$paket_dokumen->setField("PARENT_ID", '0');
			$paket_dokumen->setField('CREATED_BY', $this->USER_LOGIN_ID);
			$paket_dokumen->deleteByJenisPaketAdmin();
			$paket_dokumen->insert();

			echo "Dokumen berhasil diupload.";

		} else {
			echo "Dokumen gagal diupload";
		}
	}

	function upload_negosiasi()
	{

		$this->load->model("PaketDokumen");
		$this->load->library("FileHandler");

		$paket_dokumen = new PaketDokumen();
		$file = new FileHandler();

		$reqMode = $this->input->post("reqMode");
		$reqId = $this->input->post("reqId");
		$reqCetak= $this->input->post('reqCetak');
		$reqNamaDokumen= $this->input->post('reqNamaDokumen');
		$reqDokumenKe = $this->input->post('reqDokumenKe');
		$reqRekananId = $this->input->post('reqRekananId');
		$reqKeterangan= $this->input->post('reqKeterangan');
		$reqJenisDokumen= $this->input->post('reqJenisDokumen');
		$reqLinkFile= $_FILES['reqLinkFile'.$reqRekananId];
		$reqBayar= $this->input->post('reqBayar');
		$reqDokumenId = httpFilterGet('reqDokumenId');
		$submitSimpan= $this->input->post('submitSimpan');
		$FILE_DIR = "uploads/penawaran/";
		$renameFile = "Negosiasi_".md5(date("dmYHis").$reqLinkFile['name'].$this->USER_LOGIN_ID).".".getExtension($reqLinkFile['name']);

		if($file->uploadToDir('reqLinkFile'.$reqRekananId, $FILE_DIR, $renameFile))
		{

			$paket_dokumen->setField("REKANAN_USER_ID", "NULL");
			$paket_dokumen->setField("PAKET_ID", $reqId);
			$paket_dokumen->setField("NAMA", $reqNamaDokumen);
			$paket_dokumen->setField("UKURAN", $file->uploadedSize);
			$paket_dokumen->setField("TIPE", $file->uploadedExtension);
			$paket_dokumen->setField("PATH_FILE", $renameFile);
			$paket_dokumen->setField("JENIS_DOKUMEN", $reqJenisDokumen);
			$paket_dokumen->setField("KETERANGAN", $reqLinkFile['name']);
			$paket_dokumen->setField("FILE_PASSWORD", null);
			$paket_dokumen->setField("PARENT_ID", '0');
			$paket_dokumen->setField('CREATED_BY', $this->USER_LOGIN_ID);
			$paket_dokumen->deleteByJenisPaketAdmin();
			$paket_dokumen->insert();

			echo "Dokumen berhasil diupload.";

		} else {
			echo "Dokumen gagal diupload";
		}
	}

	function upload_surat_penawaran()
	{
		$this->load->model("PaketDokumen");
		$this->load->library("FileHandler");

		$paket_dokumen = new PaketDokumen();
		$file = new FileHandler();

		$reqMode = $this->input->post("reqMode");
		$reqId = $this->input->post("reqId");
		$reqCetak= $this->input->post('reqCetak');
		$reqNamaDokumen= $this->input->post('reqNamaDokumen');
		$reqDokumenKe = $this->input->post('reqDokumenKe');
		$reqRekananId = $this->input->post('reqRekananId');
		$reqKeterangan= $this->input->post('reqKeterangan');
		$reqJenisDokumen= $this->input->post('reqJenisDokumen');
		$reqLinkFile= $_FILES['reqLinkFile'.$reqRekananId];
		$reqBayar= $this->input->post('reqBayar');
		$reqDokumenId = httpFilterGet('reqDokumenId');
		$submitSimpan= $this->input->post('submitSimpan');
		$FILE_DIR = "uploads/penawaran/";
		$renameFile = md5(date("dmYHis").$reqLinkFile['name'].$this->USER_LOGIN_ID).".".getExtension($reqLinkFile['name']);
		;
		if($file->uploadToDir('reqLinkFile'.$reqRekananId, $FILE_DIR, $renameFile))
		{
			$paket_dokumen->setField("REKANAN_USER_ID", $reqRekananId);
			$paket_dokumen->setField("PAKET_ID", $reqId);
			$paket_dokumen->setField("NAMA", $reqNamaDokumen);
			$paket_dokumen->setField("UKURAN", $file->uploadedSize);
			$paket_dokumen->setField("TIPE", $file->uploadedExtension);
			$paket_dokumen->setField("PATH_FILE", $renameFile);
			$paket_dokumen->setField("JENIS_DOKUMEN", $reqJenisDokumen);
			$paket_dokumen->setField("FILE_PASSWORD", null);
			$paket_dokumen->setField("PARENT_ID", '0');
			$paket_dokumen->setField("KETERANGAN", $reqLinkFile['name']);
			$paket_dokumen->setField('CREATED_BY', $this->USER_LOGIN_ID);
			$paket_dokumen->deleteByJenisPaket();
			$paket_dokumen->insert();
		}

		// ikn 20190905 gabung 2 form jadi 1
		$this->load->model("PaketRekanan");

		$paket_rekanan = new PaketRekanan();

		$reqId = $this->input->post("reqId");
		// $submitSimpan = $this->input->post("submitSimpan");
		$reqDataPenawaranHarga = $_POST["reqDataPenawaranHarga"];
		$reqPaketRekananId = $_POST["reqPaketRekananId"];

		if($submitSimpan == "Simpan")
		{
			for($i=0; $i<count($reqPaketRekananId);$i++)
			{
				$paket_rekanan_insert = new PaketRekanan();
				$paket_rekanan_insert->setField("PAKET_REKANAN_ID", $reqPaketRekananId[$i]);
				$paket_rekanan_insert->setField("FIELD", "NILAI_PENAWARAN");
				$paket_rekanan_insert->setField("FIELD_VALUE", coalesce(dotToNo($reqDataPenawaranHarga[$i]), "NULL"));
				$paket_rekanan_insert->update();
				unset($paket_rekanan_insert);
			}

			// echo 'Data berhasil di simpan.';
			echo "Data berhasil diupload.";
		} else {
			echo "Data gagal diupload.";
		}
		// end ikn 20190905 gabung 2 form jadi 1
	}

	function upload_password_penawaran()
	{

		$this->load->library("paketinfo"); $paketInfo = new paketinfo();
		$this->load->model("PaketTahap");
		$this->load->model("PaketRekanan");
		$this->load->library("FileHandler");


		$file = new FileHandler();

		$reqId = $this->input->post("reqId");
		$reqCetak= $this->input->post('reqCetak');
		$reqNamaDokumen= $this->input->post('reqNamaDokumen');
		$reqKeterangan= $this->input->post('reqKeterangan');
		$reqLinkFile= $_FILES['reqLinkFile'];
		$reqBayar= $this->input->post('reqBayar');
		$reqDokumenId = httpFilterGet('reqDokumenId');
		$submitSimpan= $this->input->post('submitSimpan');


		$paket_rekanan = new PaketRekanan();
		$paket_tahap = new PaketTahap();
		$paket_tahap_metode = new PaketTahap();

		$paketInfo->getPaket($reqId);
		$reqNama = $paketInfo->nama;
		$reqSistemSampul = $paketInfo->sistem_sampul;

		$paket_rekanan_validasi = new PaketRekanan();
		$paket_rekanan_validasi->selectByParamsPaketLelang(array("PAKET_ID" => $reqId, "REKANAN_ID" => $this->ID), -1, -1, " AND TANGGAL_DAFTAR IS NOT NULL ");
		$paket_rekanan_validasi->firstRow();

		if($reqSistemSampul == "2")
			$reqFileValidasi = FILENAME_PENAWARAN."1_".$paketInfo->pr_group_number."-".$paket_rekanan_validasi->getField("KODE_REKANAN").".cert";
		else
			$reqFileValidasi = FILENAME_PENAWARAN.$paketInfo->pr_group_number."-".$paket_rekanan_validasi->getField("KODE_REKANAN").".cert";


		$jenis_tahap = $paket_tahap_metode->getJenisTahapById($reqId);
		$arrDokumenPenawaran            = DOKUMEN_PENAWARAN; // ikn
		$aktif_dok_penawaran1 = $paket_tahap->getCountByParamsAktif(array("URUT" => $arrDokumenPenawaran[$jenis_tahap], "PAKET_ID" => $reqId, "TAMPILKAN" => 1, "HADIR" => 0));
		if($aktif_dok_penawaran1 == 0) { // waktu nya habis
			echo "1--Waktu upload / update dokumen penawaran telah usai.";
		} else {
			if($submitSimpan == "Simpan")
			{
				ini_set("memory_limit","500M");
				ini_set('max_execution_time', 520);
				ini_set('post_max_size', '128M');
				ini_set('upload_max_filesize', '128M');

				if(getExtension($reqLinkFile['name']) == "cert")
				{
					if($reqLinkFile['name'] == $reqFileValidasi)
					{
						$reqToken = file_get_contents($reqLinkFile['tmp_name']);
						if($reqToken == "")
						{
							echo '1--Terdapat kesalahan silahkan cek kembali file .cert anda.';
						}
						else
						{
							/* UPDATE KE PAKET_REKANAN */
							$paket_rekanan->setField("FIELD", "KIRIM_PENAWARAN_PASSWORD");
							$paket_rekanan->setField("FIELD_VALUE", $reqToken);
							$paket_rekanan->setField("REKANAN_ID", $this->ID);
							$paket_rekanan->setField("PAKET_ID", $reqId);
							$paket_rekanan->updateByRekananPaket();

							echo '0--Upload password dokumen penawaran berhasil.';
						}
					}
					else
					{
						echo "1--Pastikan nama file sertifikasi anda adalah ".$reqFileValidasi;
					}
				}
				else
				{
					echo '1--Upload gagal. pastikan ekstensi file .cert';
				}

			}
		}
	}

	function delete_dokumen()
	{

		$this->load->model("PaketDokumen");
		$paket_dokumen = new PaketDokumen();

		$reqId = $this->input->get("reqId");

		$paket_dokumen->setField("PAKET_DOKUMEN_ID", $reqId);
		$paket_dokumen->setField("REKANAN_USER_ID", $this->ID);
		$paket_dokumen->deleteRekanan();
	}


	function upload_password_penawaran_sampul2()
	{

		$this->load->library("paketinfo"); $paketInfo = new paketinfo();
		$this->load->model("PaketTahap");
		$this->load->model("PaketRekanan");

		$reqMode = $this->input->post("reqMode");
		$reqId = $this->input->post("reqId");
		$reqCetak= $this->input->post('reqCetak');
		$reqNamaDokumen= $this->input->post('reqNamaDokumen');
		$reqKeterangan= $this->input->post('reqKeterangan');
		$reqLinkFile= $_FILES['reqLinkFile'];
		$reqBayar= $this->input->post('reqBayar');
		$reqDokumenId = httpFilterGet('reqDokumenId');
		$submitSimpan= $this->input->post('submitSimpan');


		$paket_rekanan_check = new PaketRekanan();
		$reqPaketRekananId = $paket_rekanan_check->getPaketRekananId($reqId, $this->ID);
		if($reqPaketRekananId == "")
			exit;

		$paket_rekanan = new PaketRekanan();
		$paket_tahap_metode = new PaketTahap();
		// $paket_tahap = new PaketTahap();
		// $paket_tahap_metode = new PaketTahap();

		// $jenis_tahap = $paket_tahap_metode->getJenisTahapById($reqId);
		// $arrUploadPasswordPenawaranSampul2	 = array(0, 0,  0,  0, 	0,  0, 	0,  0, 	0, 	0, 0, 15, 10, 15, 10);

		// $aktif_upload_password = $paket_tahap->getCountByParamsAktif(array("URUT" => $arrUploadPasswordPenawaranSampul2[$jenis_tahap], "PAKET_ID" => $reqId, "TAMPILKAN" => 1));

		$paket_tahap = new PaketTahap();
		$paket_tahap_metode = new PaketTahap();

		$jenis_tahap = $paket_tahap_metode->getJenisTahapById($reqId);
		$arrUploadPasswordPenawaranSampul2	 = UPLOAD_PASSWORD_PENAWARAN_SAMPUL2;

		$aktif_upload_password = $paket_tahap->getCountByParamsAktif(array("URUT" => $arrUploadPasswordPenawaranSampul2[$jenis_tahap], "PAKET_ID" => $reqId, "TAMPILKAN" => 1));

        if($aktif_upload_password == 0)
            echo 'Waktu Upload File .cert Penawaran habis atau belum mulai';
        else
        {
			$paketInfo->getPaket($reqId);
			$reqNama = $paketInfo->nama;
			$reqSistemSampul = $paketInfo->sistem_sampul;

			if($reqSistemSampul == "1")
				exit;

			$paket_rekanan_validasi = new PaketRekanan();
			$paket_rekanan_validasi->selectByParamsPaketLelang(array("PAKET_ID" => $reqId, "REKANAN_ID" => $this->ID), -1, -1, " AND TANGGAL_DAFTAR IS NOT NULL ");
			$paket_rekanan_validasi->firstRow();

			$reqFileValidasi = FILENAME_PENAWARAN."2_".$paketInfo->pr_group_number."-".$paket_rekanan_validasi->getField("KODE_REKANAN").".cert";

			if($submitSimpan == "Simpan")
			{
				if(getExtension($reqLinkFile['name']) == "cert")
				{
					if($reqLinkFile['name'] == $reqFileValidasi)
					{
						$reqToken = file_get_contents($reqLinkFile['tmp_name']);
						if($reqToken == "")
						{
							echo "Terdapat kesalahan silahkan cek kembali file .cert anda";
						}
						else
						{
							/* UPDATE KE PAKET_REKANAN */
							$paket_rekanan->setField("FIELD", "KIRIM_PENAWARAN_PASSWORD2");
							$paket_rekanan->setField("FIELD_VALUE", $reqToken);
							$paket_rekanan->setField("REKANAN_ID", $this->ID);
							$paket_rekanan->setField("PAKET_ID", $reqId);
							$paket_rekanan->updateByRekananPaket();

							echo "Upload password dokumen penawaran berhasil";
						}
					}
					else
					{
						echo "Pastikan nama file sertifikasi anda adalah ".$reqFileValidasi;
					}
				}
				else
				{
					echo "Upload gagal. pastikan ekstensi file .cert";
				}

			}
		}
	}

}
?>
