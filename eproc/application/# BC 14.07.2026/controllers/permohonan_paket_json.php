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

class permohonan_paket_json extends CI_Controller {

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
		$this->USER_JABATAN_PANITIA = isset($this->kauth->getInstance()->getIdentity()->USER_JABATAN_PANITIA) ? $this->kauth->getInstance()->getIdentity()->USER_JABATAN_PANITIA : '';
		
	}

	function permohonan_lelang_add()
	{
		$this->load->model("PaketPenawaran");
		$this->load->model("PermohonanPaket");
		$this->load->model("PermohonanPaketFile");
		$this->load->library("FileHandler");
		$file = new FileHandler();
		$this->load->library("kauth");  $userLogin = new kauth();

		$paket_penawaran = new PaketPenawaran();
		$permohonan_paket = new PermohonanPaket();

		$reqId = $this->input->post("reqId");
		$reqMode = $this->input->post("reqMode");
		$reqNama = $this->input->post("reqNama");
		$reqKeterangan = $this->input->post("reqKeterangan");
		$reqNotaDinas = $this->input->post("reqNotaDinas");
		$reqNamaPaket = $this->input->post("reqNamaPaket");
		$reqNilai = $this->input->post("reqNilai");
		$reqNomorPPA = $this->input->post("reqNomorPPA");
		$reqTanggal = $this->input->post("reqTanggal");
		$reqJudul	 = $this->input->post("reqJudul");
		$reqUrut	 = $this->input->post("reqUrut");
		$reqMetodePengadaan	 = $this->input->post("reqMetodePengadaan");
		// jatim
		$reqTahunAnggaran	 = $this->input->post("reqTahunAnggaran");
		//COA
		// $reqNomorCOA	 = $this->input->post("reqNomorCOA");
		// $reqKeteranganCOA	 = $this->input->post("reqKeteranganCOA");
		// $reqBudgetAwal	 = $this->input->post("reqBudgetAwal");
		// $reqBudgetTerpakai	 = $this->input->post("reqBudgetTerpakai");
		// $reqBudgetAkhir	 = $this->input->post("reqBudgetAkhir");

		$reqLinkFile			= $_FILES['reqLinkFile'];
		$reqLinkFileTemp 		= $_POST["reqLinkFileTemp"];
		$reqLinkFileTempTipe 	= $_POST["reqLinkFileTempTipe"];
		$reqLinkFileTempUkuran 	= $_POST["reqLinkFileTempUkuran"];
		$FILE_DIR = "uploads/permohonan_paket/";
		//echo "sddsa".dotToNo($reqNilai);exit;
		$permohonan_paket->setField('PERMOHONAN_PAKET_ID', $reqId);
		$permohonan_paket->setField('USER_LOGIN_ID', $this->USER_LOGIN_ID);
		$permohonan_paket->setField('UNIT_KERJA_ID', $this->UNIT_KERJA_ID);
		$permohonan_paket->setField('NOTA_DINAS', $reqNotaDinas);
		$permohonan_paket->setField('NO_PPA', $reqNomorPPA);
		$permohonan_paket->setField('NAMA', $reqNamaPaket);
		$permohonan_paket->setField('NILAI', CommaToNo($reqNilai));
		$permohonan_paket->setField('KETERANGAN', $reqKeterangan);
		$permohonan_paket->setField('NO_PPA', $reqNomorPPA);
		$permohonan_paket->setField('TANGGAL', dateToDBCheck($reqTanggal));
		$permohonan_paket->setField("LAST_CREATE_USER", $this->USER_NAMA);
		$permohonan_paket->setField("PENGADAANLANGSUNG", $reqMetodePengadaan);
		// jatim
		$permohonan_paket->setField("TAHUN_ANGGARAN", $reqTahunAnggaran);
		$permohonan_paket->setField('CREATED_BY', $this->USER_LOGIN_ID);

		// $permohonan_paket->setField("NOMOR", $reqNomorCOA);
		// $permohonan_paket->setField("KETERANGAN", $reqKeteranganCOA);
		// $permohonan_paket->setField("BUDGET_AWAL", dotToNo($reqBudgetAwal));
		// $permohonan_paket->setField("BUDGET_TERPAKAI", dotToNo($reqBudgetTerpakai));
		// $permohonan_paket->setField("BUDGET_AKHIR", dotToNo($reqBudgetAkhir));

		if($reqMode == 'insert')
		{
			if($permohonan_paket->insert())
			{

				// ikn 20191107
				$paket_penawaran = new PaketPenawaran();
				$paket_penawaran->setField("PAKET_ID", '0');
				$paket_penawaran->setField("NAMA", $reqNamaPaket);
				$paket_penawaran->setField("LAST_CREATE_USER", $this->USER_LOGIN_ID);
				$paket_penawaran->setField("JUMLAH", CommaToNo($reqNilai));
				$paket_penawaran->setField("PERMOHONAN_PAKET_ID", $permohonan_paket->id);
				$paket_penawaran->insert2();

				$reqId = $permohonan_paket->id;
				// Insert Rekam Jejak
			    $this->load->library("librekamjejak");
			    $this->librekamjejak->insertRJ('1','','null',$reqId,'1'); // param 1: Posisi/'null', param 2: Keterangan/'', param 3: Paket_id/'null', param 4: permohonan_id/'null'
			    // End Insert Rekam Jejak
				echo "Data berhasil disimpan.";
			}

			// // input multiple COA
			// for($i=0; $i<count($reqNomorCOA);$i++)
			// {
			// 	if($reqNomorCOA[$i] == "")
			// 	{}
			// 	else
			// 	{
			// 		$permohonan_paket_coa = new PermohonanPaket();
			// 		$permohonan_paket_coa->setField("NOMOR", $reqNomorCOA[$i]);
			// 		$permohonan_paket_coa->setField("KETERANGAN", $reqKeteranganCOA[$i]);
			// 		$permohonan_paket_coa->setField("BUDGET_AWAL", dotToNo($reqBudgetAwal[$i]));
			// 		$permohonan_paket_coa->setField("BUDGET_TERPAKAI", dotToNo($reqBudgetTerpakai[$i]));
			// 		$permohonan_paket_coa->setField("BUDGET_AKHIR", dotToNo($reqBudgetAkhir[$i]));
			// 		$permohonan_paket_coa->setField("PERMOHONAN_PAKET_ID", $reqId);
			// 		$permohonan_paket_coa->setField('CREATED_BY', $this->USER_LOGIN_ID);
			// 		$permohonan_paket_coa->insertcoa();
			// 	}
			// 	unset($permohonan_paket_coa);
			// }

			for($i=0; $i<count($reqJudul);$i++)
			{
				if($reqJudul[$i] == "")
				{}
				else
				{
					$permohonan_paket_file = new PermohonanPaketFile();
					$permohonan_paket_file->setField("PERMOHONAN_PAKET_ID", $reqId);
					$renameFile = md5(date("dmYHis").$reqLinkFile['name'][$i].$reqId).".".getExtension($reqLinkFile['name'][$i]);
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
					$permohonan_paket_file->setField("PAKET_ID", 'null');
					$permohonan_paket_file->setField("PATH_FILE", $insertLinkFile);
					$permohonan_paket_file->setField("TIPE", $insertLinkFilesExe);
					$permohonan_paket_file->setField("UKURAN", ValToNullDB($insertLinkFilesSize));
					$permohonan_paket_file->setField("JUDUL", $reqJudul[$i]);
					$permohonan_paket_file->setField("URUT", coalesce($reqUrut[$i], $i+1));
					$permohonan_paket_file->setField('CREATED_BY', $this->USER_LOGIN_ID);
					$permohonan_paket_file->insert();
				}
				unset($permohonan_paket_file);
			}
		}
		else
		{
			$permohonan_paket->setField('PERMOHONAN_PAKET_ID', $reqId);
			$permohonan_paket->setField('USER_LOGIN_ID', $this->USER_LOGIN_ID);
			$permohonan_paket->setField('UNIT_KERJA_ID', $this->UNIT_KERJA_ID);
			$permohonan_paket->setField('NOTA_DINAS', $reqNotaDinas);
			$permohonan_paket->setField('NO_PPA', $reqNomorPPA);
			$permohonan_paket->setField('NAMA', $reqNamaPaket);
			$permohonan_paket->setField('NILAI', CommaToNo($reqNilai));
			$permohonan_paket->setField('KETERANGAN', $reqKeterangan);
			$permohonan_paket->setField('NO_PPA', $reqNomorPPA);
			$permohonan_paket->setField('TANGGAL', dateToDBCheck($reqTanggal));
			$permohonan_paket->setField("LAST_CREATE_USER", $this->USER_NAMA);
			$permohonan_paket->setField("PENGADAANLANGSUNG", $reqMetodePengadaan);
			// $permohonan_paket->update();

			// jatim
			$permohonan_paket->setField("TAHUN_ANGGARAN", $reqTahunAnggaran);
			$permohonan_paket->setField('CREATED_BY', $this->USER_LOGIN_ID);
			// $permohonan_paket->setField("BUDGET_AWAL", dotToNo($reqBudgetAwal));
			// $permohonan_paket->setField("BUDGET_TERPAKAI", dotToNo($reqBudgetTerpakai));
			// $permohonan_paket->setField("BUDGET_AKHIR", dotToNo($reqBudgetAkhir));
			// $permohonan_paket->setField("TAHUN_ANGGARAN", $reqTahunAnggaran);

			if($permohonan_paket->update())
			{

				// Insert Rekam Jejak
			    $this->load->library("librekamjejak");
			    $this->librekamjejak->insertRJ('108','','null',$reqId,'108'); // param 1: Posisi/'null', param 2: Keterangan/'', param 3: Paket_id/'null', param 4: permohonan_id/'null'
			    // End Insert Rekam Jejak
				echo "Data berhasil disimpan.";

				// $paket_penawaran_delete = new PaketPenawaran();
				// $paket_penawaran_delete->setField("PERMOHONAN_PAKET_ID", $reqId);
				// if ($paket_penawaran_delete->deletePermohonan()) {
				// ikn 20191107
				// }
				$paket_penawaran_select = new PaketPenawaran();
				$paket_penawaran_select->selectByParams(array("A.PERMOHONAN_PAKET_ID" => coalesce($reqId, 0)));
  				$paket_penawaran_select->firstRow();
				if ($paket_penawaran_select->getField("PAKET_PENAWARAN_ID")) {
					$paket_penawaran = new PaketPenawaran();
					$paket_penawaran->setField("PAKET_ID", '0');
					$paket_penawaran->setField("NAMA", $reqNamaPaket);
					$paket_penawaran->setField("LAST_CREATE_USER", $this->USER_LOGIN_ID);
					$paket_penawaran->setField("JUMLAH", CommaToNo($reqNilai));
					$paket_penawaran->setField("PERMOHONAN_PAKET_ID", $reqId);
					$paket_penawaran->update2();
				} else {
					$paket_penawaran = new PaketPenawaran();
					$paket_penawaran->setField("PAKET_ID", '0');
					$paket_penawaran->setField("NAMA", $reqNamaPaket);
					$paket_penawaran->setField("LAST_CREATE_USER", $this->USER_LOGIN_ID);
					$paket_penawaran->setField("JUMLAH", CommaToNo($reqNilai));
					$paket_penawaran->setField("PERMOHONAN_PAKET_ID", $reqId);
					$paket_penawaran->insert2();
				}
			}

			// if ($reqNomorCOA) {
			// 	// input multiple COA
			// 	$permohonan_paket_coa_delete = new PermohonanPaket();
			// 	$permohonan_paket_coa_delete->setField("PERMOHONAN_PAKET_ID", $reqId);
			// 	$permohonan_paket_coa_delete->deleteCoa();

			// 	for($i=0; $i<count($reqNomorCOA);$i++)
			// 	{
			// 		if($reqNomorCOA[$i] == "")
			// 		{}
			// 		else
			// 		{
			// 			$permohonan_paket_coa = new PermohonanPaket();
			// 			$permohonan_paket_coa->setField("NOMOR", $reqNomorCOA[$i]);
			// 			$permohonan_paket_coa->setField("KETERANGAN", $reqKeteranganCOA[$i]);
			// 			$permohonan_paket_coa->setField("BUDGET_AWAL", dotToNo($reqBudgetAwal[$i]));
			// 			$permohonan_paket_coa->setField("BUDGET_TERPAKAI", dotToNo($reqBudgetTerpakai[$i]));
			// 			$permohonan_paket_coa->setField("BUDGET_AKHIR", dotToNo($reqBudgetAkhir[$i]));
			// 			$permohonan_paket_coa->setField("PERMOHONAN_PAKET_ID", $reqId);
			// 			$permohonan_paket_coa->setField('CREATED_BY', $this->USER_LOGIN_ID);
			// 			$permohonan_paket_coa->insertcoa();
			// 		}
			// 		unset($permohonan_paket_coa);
			// 	}
			// }


			$permohonan_paket_file_delete = new PermohonanPaketFile();
			$permohonan_paket_file_delete->setField("PERMOHONAN_PAKET_ID", $reqId);
			if ($permohonan_paket_file_delete->deletePermohonan()) {
				for($i=0; $i<count($reqJudul);$i++)
				{
					if($reqJudul[$i] == "")
					{}
					else
					{
						$permohonan_paket_file = new PermohonanPaketFile();
						$permohonan_paket_file->setField("PERMOHONAN_PAKET_ID", $reqId);
						$renameFile = md5(date("dmYHis").$reqLinkFile['name'][$i].$reqId).".".getExtension($reqLinkFile['name'][$i]);
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
						$permohonan_paket_file->setField("PAKET_ID", 'null');
						$permohonan_paket_file->setField("PATH_FILE", $insertLinkFile);
						$permohonan_paket_file->setField("TIPE", $insertLinkFilesExe);
						$permohonan_paket_file->setField("UKURAN", ValToNullDB($insertLinkFilesSize));
						$permohonan_paket_file->setField("JUDUL", $reqJudul[$i]);
						$permohonan_paket_file->setField("URUT", coalesce($reqUrut[$i], $i+1));
						$permohonan_paket_file->setField('CREATED_BY', $this->USER_LOGIN_ID);
						$permohonan_paket_file->insert();
					}
					unset($permohonan_paket_file);
				}
			}
		}
			echo "Data berhasil disimpan.-".$reqId."-".$reqMetodePengadaan;

	}

	function tetapkan_metode()
	{
		$this->load->model("PermohonanPaket");

		$permohonan_paket = new PermohonanPaket();

		$reqId =  $this->input->post('reqId'); // permohonan_paket_analisa_id
		$reqPermohonanId =  $this->input->post('reqPermohonanId');  // permohonan_paket_id
		$reqPIC =  $this->input->post('reqPIC');
		$reqMetodePengadaan =  $this->input->post('reqMetodePengadaan');
		if ($reqMetodePengadaan == '2') { // Purchasing
			$reqCaraPengadaan = 'Purchasing';
		} else if ($reqMetodePengadaan == '0') { // Tender
			$reqCaraPengadaan = 'Sourcing';
		} else {
			$reqCaraPengadaan = 'Lainnya';
		}
		// $reqMetode =  $this->input->post('reqMetode');

		$permohonan_paket = new PermohonanPaket();
		$permohonan_paket->setField("PERMOHONAN_PAKET_ID", $reqPermohonanId);
		$permohonan_paket->setField("PERMOHONAN_PAKET_ANALISA_ID", $reqId);
		$permohonan_paket->setField("PIC", $reqPIC);
		$permohonan_paket->setField("PENGADAANLANGSUNG", $reqMetodePengadaan);
		$permohonan_paket->setField("STRATEGI_PENGADAAN", $reqCaraPengadaan);
		// $permohonan_paket->setField("PAKET_METODE_LELANG_ID", $reqMetode);
		$permohonan_paket->setField("PIC_BY", $this->USER_NAMA);
		$permohonan_paket->setField("UPDATED_BY", $this->USER_LOGIN_ID);

		if($permohonan_paket->tetapkanmetode()) {
			// Insert Rekam Jejak
		    $this->load->library("librekamjejak");
		    $this->librekamjejak->insertRJ('611','','null',$reqPermohonanId,'611');
		    // param 1: Posisi/'1-22', param 2: Keterangan/'', param 3: Paket_id/'null', param 4: permohonan_id/'null'
		    // End Insert Rekam Jejak
			echo "Berhasil Diteruskan";
		} else {
			echo "Gagal Diteruskan";
		}
	}

	function tunjuk_pic()
	{
		$this->load->model("PermohonanPaket");

		$permohonan_paket = new PermohonanPaket();

		$reqId =  $this->input->post('reqId'); // permohonan_paket_analisa_id
		$reqPermohonanId =  $this->input->post('reqPermohonanId');  // permohonan_paket_id
		$reqPIC =  $this->input->post('reqPIC'); 

		$permohonan_paket = new PermohonanPaket();
		$permohonan_paket->setField("PERMOHONAN_PAKET_ID", $reqPermohonanId);
		$permohonan_paket->setField("PERMOHONAN_PAKET_ANALISA_ID", $reqId);
		$permohonan_paket->setField("PIC", $reqPIC);
		$permohonan_paket->setField("PIC_BY", $this->USER_NAMA);
		$permohonan_paket->setField("UPDATED_BY", $this->USER_LOGIN_ID);

		if($permohonan_paket->tunjuk_pic()) {
			// Insert Rekam Jejak
		    $this->load->library("librekamjejak");
		    $this->librekamjejak->insertRJ('6','','null',$reqPermohonanId,'6');
		    // param 1: Posisi/'1-22', param 2: Keterangan/'', param 3: Paket_id/'null', param 4: permohonan_id/'null'
		    // End Insert Rekam Jejak
			echo "Berhasil Diteruskan";
		} else {
			echo "Gagal Diteruskan";
		}
	}

	function resend_permohonan()
	{
		$reqId =  $this->input->get('reqId'); // permohonan_paket_analisa_id
		$permohonanId =  $this->input->get('permohonanId');  // permohonan_paket_id
		$this->load->model("PermohonanPaket");
		$this->load->model("PaketPenawaran");

		$permohonan_paket = new PermohonanPaket();
		$permohonan_paket->setField("PERMOHONAN_PAKET_ANALISA_ID", $reqId);
		$permohonan_paket->setField("UPDATED_BY", $this->USER_LOGIN_ID);

		if($permohonan_paket->resendPermohonan()) {
			 // Insert Rekam Jejak
		    $this->load->library("librekamjejak");
		    $this->librekamjejak->insertRJ('811','','null',$permohonanId,'811'); // param 1: Posisi/'null', param 2: Keterangan/'', param 3: Paket_id/'null', param 4: permohonan_id/'null'
		    // End Insert Rekam Jejak
			echo "Data Berhasil Dikirim";
		}else {
			echo "Data Gagal Dikirim";
		}

	}

	function approve_permohonan()
	{
		$reqId =  $this->input->post('reqId'); // permohonan_paket_analisa_id
		$permohonanId =  $this->input->post('reqPermohonanId');  // permohonan_paket_id
		$this->load->model("PermohonanPaket");
		$this->load->model("PaketPenawaran");

		$reqMetodePengadaan =  $this->input->post('reqMetodePengadaan');
		if ($reqMetodePengadaan == '2') { // Purchasing
			$reqCaraPengadaan = 'Purchasing';
		} else if ($reqMetodePengadaan == '0') { // Tender
			$reqCaraPengadaan = 'Sourcing';
		} else {
			$reqCaraPengadaan = 'Lainnya';
		}
		$reqMetode =  $this->input->post('reqMetode');

		$permohonan_paket = new PermohonanPaket();
		$permohonan_paket->setField("PERMOHONAN_PAKET_ANALISA_ID", $reqId);
		$permohonan_paket->setField("PERMOHONAN_PAKET_ID", $permohonanId);
		$permohonan_paket->setField("PENGADAANLANGSUNG", $reqMetodePengadaan);
		$permohonan_paket->setField("STRATEGI_PENGADAAN", $reqCaraPengadaan);
		$permohonan_paket->setField("PAKET_METODE_LELANG_ID", $reqMetode);
		$permohonan_paket->setField("UPDATED_BY", $this->USER_LOGIN_ID);
		$permohonan_paket->setField("POSTING", '1');
		$permohonan_paket->setField("POSTING_BY", $this->USER_NAMA);

		if($permohonan_paket->approvePermohonan()) {
			 // Insert Rekam Jejak
		    $this->load->library("librekamjejak");
		    $this->librekamjejak->insertRJ('511','','null',$permohonanId,'511'); // param 1: Posisi/'null', param 2: Keterangan/'', param 3: Paket_id/'null', param 4: permohonan_id/'null'
		    // End Insert Rekam Jejak
			echo "Data Berhasil Disetujui";
		}else {
			echo "Data Gagal Disetujui";
		}

	}

	function permohonan_lelang_addv2()
	{
		$this->load->model("PaketPenawaran");
		$this->load->model("PermohonanPaket");
		$this->load->model("PermohonanPaketFile");
		$this->load->library("FileHandler");
		$file = new FileHandler();
		$this->load->library("kauth");  $userLogin = new kauth();

		$paket_penawaran = new PaketPenawaran();
		$permohonan_paket = new PermohonanPaket();

		$reqId = $this->input->post("reqId");
		$reqMode = $this->input->post("reqMode");
		$reqNama = $this->input->post("reqNama");
		$reqKeterangan = $this->input->post("reqKeterangan");
		$reqNotaDinas = $this->input->post("reqNotaDinas");
		$reqNamaPaket = $this->input->post("reqNamaPaket");
		$reqNilai = $this->input->post("reqNilai");
		$reqNomorPPA = $this->input->post("reqNomorPPA");
		$reqTanggal = $this->input->post("reqTanggal");
		$reqJudul	 = $this->input->post("reqJudul");
		$reqUrut	 = $this->input->post("reqUrut");
		$reqMetodePengadaan	 = $this->input->post("reqMetodePengadaan");
		$reqTahunAnggaran	 = $this->input->post("reqTahunAnggaran");

		$reqLinkFile			= $_FILES['reqLinkFile'];
		$reqLinkFileTemp 		= $_POST["reqLinkFileTemp"];
		$reqLinkFileTempTipe 	= $_POST["reqLinkFileTempTipe"];
		$reqLinkFileTempUkuran 	= $_POST["reqLinkFileTempUkuran"];
		$FILE_DIR = "uploads/permohonan_paket/";
		//echo "sddsa".dotToNo($reqNilai);exit;
		$permohonan_paket->setField('PERMOHONAN_PAKET_ID', $reqId);
		$permohonan_paket->setField('USER_LOGIN_ID', $this->USER_LOGIN_ID);
		$permohonan_paket->setField('UNIT_KERJA_ID', $this->UNIT_KERJA_ID);
		$permohonan_paket->setField('NAMA', $reqNamaPaket);
		$permohonan_paket->setField('NILAI', CommaToNo($reqNilai));
		$permohonan_paket->setField("LAST_CREATE_USER", $this->USER_NAMA);
		$permohonan_paket->setField("PENGADAANLANGSUNG", $reqMetodePengadaan);
		// jatim
		$permohonan_paket->setField("TAHUN_ANGGARAN", $reqTahunAnggaran);
		$permohonan_paket->setField('CREATED_BY', $this->USER_LOGIN_ID);

		if($reqMode == 'insert')
		{
			if($permohonan_paket->insertv2())
			{

				// ikn 20191107
				$paket_penawaran = new PaketPenawaran();
				$paket_penawaran->setField("PAKET_ID", '0');
				$paket_penawaran->setField("NAMA", $reqNamaPaket);
				$paket_penawaran->setField("LAST_CREATE_USER", $this->USER_LOGIN_ID);
				$paket_penawaran->setField("JUMLAH", CommaToNo($reqNilai));
				$paket_penawaran->setField("PERMOHONAN_PAKET_ID", $permohonan_paket->id);
				$paket_penawaran->insert2();

				$reqId = $permohonan_paket->id;
				// Insert Rekam Jejak
			    $this->load->library("librekamjejak");
			    $this->librekamjejak->insertRJ('1','','null',$reqId,'1'); // param 1: Posisi/'null', param 2: Keterangan/'', param 3: Paket_id/'null', param 4: permohonan_id/'null'
			    // End Insert Rekam Jejak
				echo "Data berhasil disimpan.";
			}

		}
		else
		{
			$permohonan_paket->setField('PERMOHONAN_PAKET_ID', $reqId);
			$permohonan_paket->setField('USER_LOGIN_ID', $this->USER_LOGIN_ID);
			$permohonan_paket->setField('UNIT_KERJA_ID', $this->UNIT_KERJA_ID);
			$permohonan_paket->setField('NAMA', $reqNamaPaket);
			$permohonan_paket->setField('NILAI', CommaToNo($reqNilai));
			$permohonan_paket->setField("LAST_CREATE_USER", $this->USER_NAMA);
			$permohonan_paket->setField("PENGADAANLANGSUNG", $reqMetodePengadaan);
			// $permohonan_paket->update();

			// jatim
			$permohonan_paket->setField("TAHUN_ANGGARAN", $reqTahunAnggaran);
			$permohonan_paket->setField('CREATED_BY', $this->USER_LOGIN_ID);

			if($permohonan_paket->updatev2())
			{

				// Insert Rekam Jejak
			    $this->load->library("librekamjejak");
			    $this->librekamjejak->insertRJ('108','','null',$reqId,'108'); // param 1: Posisi/'null', param 2: Keterangan/'', param 3: Paket_id/'null', param 4: permohonan_id/'null'
			    // End Insert Rekam Jejak
				echo "Data berhasil disimpan.";

				$paket_penawaran_select = new PaketPenawaran();
				$paket_penawaran_select->selectByParams(array("A.PERMOHONAN_PAKET_ID" => coalesce($reqId, 0)));
  				$paket_penawaran_select->firstRow();
				if ($paket_penawaran_select->getField("PAKET_PENAWARAN_ID")) {
					$paket_penawaran = new PaketPenawaran();
					$paket_penawaran->setField("PAKET_ID", '0');
					$paket_penawaran->setField("NAMA", $reqNamaPaket);
					$paket_penawaran->setField("LAST_CREATE_USER", $this->USER_LOGIN_ID);
					$paket_penawaran->setField("JUMLAH", CommaToNo($reqNilai));
					$paket_penawaran->setField("PERMOHONAN_PAKET_ID", $reqId);
					$paket_penawaran->update2();
				} else {
					$paket_penawaran = new PaketPenawaran();
					$paket_penawaran->setField("PAKET_ID", '0');
					$paket_penawaran->setField("NAMA", $reqNamaPaket);
					$paket_penawaran->setField("LAST_CREATE_USER", $this->USER_LOGIN_ID);
					$paket_penawaran->setField("JUMLAH", CommaToNo($reqNilai));
					$paket_penawaran->setField("PERMOHONAN_PAKET_ID", $reqId);
					$paket_penawaran->insert2();
				}
			}

		}
			echo "Data berhasil disimpan.-".$reqId."-".$reqMetodePengadaan;

	}

	function permohonan_lelang_monitoring_json()
	{
		$this->load->library("kauth");  $userLogin = new kauth();
		$this->load->model("PermohonanPaket");
		$permohonan_paket = new PermohonanPaket();

		ini_set("memory_limit","500M");
		ini_set('max_execution_time', 5200);

		$reqStatus= $this->input->get("reqStatus");

		$aColumns = array("PERMOHONAN_PAKET_ID", "POSTING", "STATUS_KETERANGAN","PUBLISH","KODE_RUP","TAHUN_ANGGARAN", "NAMA", "PERKIRAAN_BIAYA_HARGA", "NAMA_PIC","KODE_PR","BOQ_FILE");
		$aColumnsAlias = array("A.PERMOHONAN_PAKET_ID", "POSTING", "STATUS_KETERANGAN", "PUBLISH","KODE_RUP", "TAHUN_ANGGARAN", "NAMA", "PERKIRAAN_BIAYA_HARGA", "NAMA_PIC","KODE_PR","BOQ_FILE");

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

			if ( trim($sOrder) == "ORDER BY RECRUITMENT asc, RECRUITMENT asc" )
			{
				$sOrder = " ORDER BY A.RECRUITMENT_ID ASC, A.TANGGAL_AWAL_REN ASC";
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

		$statement="";
		$statement .="AND G.APPROVAL='1' ";
		$searchJson='';

		if($reqStatus == '')
		{}
		elseif($reqStatus == '0')
			$statement .= " AND A.POSTING IS NULL ";
		elseif($reqStatus == '1')
			$statement .= " AND D.PAKET_ID IS NULL AND A.POSTING IS NOT NULL ";
		elseif($reqStatus == '2')
			$statement .= " AND D.PAKET_ID IS NOT NULL AND A.POSTING IS NOT NULL ";


		$searchJson .= " AND (UPPER(A.NAMA) LIKE '%".strtoupper($_GET['sSearch'])."%' OR UPPER(A.NOTA_DINAS) LIKE '%".strtoupper($_GET['sSearch'])."%' OR UPPER(A.KODE_RUP) LIKE '%".strtoupper($_GET['sSearch'])."%')";

		if($this->USER_TYPE_ID == 9) // PENGGUNA
        {
			$allRecord = $permohonan_paket->getCountByParams(array("A.USER_LOGIN_ID" => $this->USER_LOGIN_ID), $statement);

			if($_GET['sSearch'] == "")
				$allRecordFilter = $allRecord;
			else
				$allRecordFilter = $permohonan_paket->getCountByParams(array("A.USER_LOGIN_ID" => $this->USER_LOGIN_ID), $statement.$searchJson);

			$permohonan_paket->selectByParams(array("A.USER_LOGIN_ID" => $this->USER_LOGIN_ID), $dsplyRange, $dsplyStart, $statement.$searchJson, $sOrder);

		} else if($this->USER_TYPE_ID == 7) // KEPALA PENGADAAN
		{

			$allRecord = $permohonan_paket->getCountByParams(array("E.VP_PENGADAAN" => $this->USER_LOGIN_ID), $statement);

			if($_GET['sSearch'] == "")
				$allRecordFilter = $allRecord;
			else
				$allRecordFilter = $permohonan_paket->getCountByParams(array("E.VP_PENGADAAN" => $this->USER_LOGIN_ID), $statement.$searchJson);

			$permohonan_paket->selectByParams(array("E.VP_PENGADAAN" => $this->USER_LOGIN_ID), $dsplyRange, $dsplyStart, $statement.$searchJson, $sOrder);
		}



		/* Output */
		$output = array(
			"sEcho" => intval($_GET['sEcho']),
			"iTotalRecords" => $allRecord,
			"iTotalDisplayRecords" => $allRecordFilter,
			"aaData" => array()
		);

		while($permohonan_paket->nextRow())
		{
			$ulang = $permohonan_paket->getField('PAKET_ID_ULANG');
			if ($ulang != '') { $keterangan_ulang = '<div class="badge badge-danger">Paket Gagal</div>';
			} else { $keterangan_ulang = ''; }

			$row = array();
			for ( $i=0 ; $i<count($aColumns) ; $i++ )
			{
				if($aColumns[$i] == "TANGGAL" || $aColumns[$i] == "NILAI" || $aColumns[$i] == "NAMA_PIC")
					if ($permohonan_paket->getField($aColumns[$i]) == ''):
						$row[] = '<small style="color:#b7b7b7">-- Not Set --</small>';
					else:
						if($aColumns[$i] == "NILAI") {
							$row[] = numberToIna($permohonan_paket->getField($aColumns[$i]));
						} else {
							$row[] = getFormattedDate($permohonan_paket->getField($aColumns[$i]));
						}
					endif;
				else if($aColumns[$i] == "TANGGAL")
					$row[] = getFormattedDateS($permohonan_paket->getField($aColumns[$i]));
				else if($aColumns[$i] == "NAMA")
					if ($permohonan_paket->getField("PUBLISH") == '1') {
						if ($permohonan_paket->getField("POSTING") == '1') {
							$row[] = $permohonan_paket->getField($aColumns[$i]).'<br> <span class="badge badge-info">Sudah Diteruskan</span> <span class="badge badge-primary">Sedang Publish</span><br>'.$keterangan_ulang;
						} else {
							$row[] = $permohonan_paket->getField($aColumns[$i]).'<br> <span class="badge badge-primary">Sedang Publish</span><br>'.$keterangan_ulang;
						}
					} else {
						if ($permohonan_paket->getField("POSTING") == '1' && $permohonan_paket->getField("NAMA_PIC") == '') {
							$row[] = $permohonan_paket->getField($aColumns[$i]).'<br> <span class="badge badge-info">Sudah Diteruskan</span><br>'.$keterangan_ulang;
						} else {
							$row[] = $permohonan_paket->getField($aColumns[$i]).'<br>'.$keterangan_ulang;
						}
					}
				else if($aColumns[$i] == "KETERANGAN")
					$row[] = truncate($permohonan_paket->getField($aColumns[$i]), 5)."...";
				// else if($aColumns[$i] == "NILAI")
				// 	$row[] = numberToIna($permohonan_paket->getField($aColumns[$i]));
				else if($aColumns[$i] == "STATUS_KETERANGAN")
					$row[] = "<div align='center'>".$permohonan_paket->getField($aColumns[$i])."</div>";
				else if($aColumns[$i] == "PERKIRAAN_BIAYA_HARGA")
					$row[] = numberToIna($permohonan_paket->getField($aColumns[$i]));
				else if($aColumns[$i] == "BOQ_FILE")
					if ($permohonan_paket->getField("BOQ_FILE") == '') {
						$row[] = '<span class="badge badge-danger">kosong</span>';
					} else {
						$row[] = '<span class="badge badge-primary">ada</span>';
					}

				// else if($aColumns[$i] == "PERMOHONAN_PAKET_ANALISA_ID")
				// 	$row[] = '<a class="badge badge-primary" style="color:#fff" onclick="make('.$permohonan_paket->getField('PERMOHONAN_PAKET_ANALISA_ID').')"><span class="fa fa-eye">Lihat</span></a>';
				else
					$row[] = $permohonan_paket->getField($aColumns[$i]);
			}

			$output['aaData'][] = $row;
		}

		echo json_encode( $output );
	}

	function permohonan_lelang_monitoring_rup_json()
	{
		$this->load->library("kauth");  $userLogin = new kauth();
		$this->load->model("PermohonanPaket");
		$permohonan_paket = new PermohonanPaket();

		ini_set("memory_limit","500M");
		ini_set('max_execution_time', 5200);

		$reqStatus= $this->input->get("reqStatus");

		$aColumns = array("PERMOHONAN_PAKET_ID", "POSTING", "STATUS_KETERANGAN","PUBLISH","TAHUN_ANGGARAN","NO_PPA", "TANGGAL", "NAMA", "NILAI", "NAMA_PIC","RENCANA_PENGADAAN");
		$aColumnsAlias = array("A.PERMOHONAN_PAKET_ID", "POSTING", "STATUS_KETERANGAN", "PUBLISH", "TAHUN_ANGGARAN","NO_PPA", "TANGGAL", "NAMA", "NILAI", "NAMA_PIC","RENCANA_PENGADAAN");

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

			if ( trim($sOrder) == "ORDER BY RECRUITMENT asc, RECRUITMENT asc" )
			{
				$sOrder = " ORDER BY A.RECRUITMENT_ID ASC, A.TANGGAL_AWAL_REN ASC";

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

		// $statement="AND G.APPROVAL='1' ";
		$statement="";
		$searchJson='';

		$searchJson .= " AND (UPPER(A.NAMA) LIKE '%".strtoupper($_GET['sSearch'])."%' OR UPPER(A.NOTA_DINAS) LIKE '%".strtoupper($_GET['sSearch'])."%')";

		$allRecord = $permohonan_paket->getCountByParams(array("G.APPROVAL" => 1), $statement);
		if($_GET['sSearch'] == "")
			$allRecordFilter = $allRecord;
		else
			$allRecordFilter = $permohonan_paket->getCountByParams(array("G.APPROVAL" => 1), $statement.$searchJson);

		$permohonan_paket->selectByParams(array("G.APPROVAL" => 1), $dsplyRange, $dsplyStart, $statement.$searchJson, $sOrder);

		/* Output */
		$output = array(
			"sEcho" => intval($_GET['sEcho']),
			"iTotalRecords" => $allRecord,
			"iTotalDisplayRecords" => $allRecordFilter,
			"aaData" => array()
		);

		while($permohonan_paket->nextRow())
		{
			$ulang = $permohonan_paket->getField('PAKET_ID_ULANG');
			if ($ulang != '') { $keterangan_ulang = '<div class="badge badge-danger">Paket Gagal</div>';
			} else { $keterangan_ulang = ''; }

			$row = array();
			for ( $i=0 ; $i<count($aColumns) ; $i++ )
			{
				if($aColumns[$i] == "TANGGAL" || $aColumns[$i] == "NILAI" || $aColumns[$i] == "NAMA_PIC")
					if ($permohonan_paket->getField($aColumns[$i]) == ''):
						$row[] = '<small style="color:#b7b7b7">-- Not Set --</small>';
					else:
						if($aColumns[$i] == "NILAI") {
							$row[] = numberToIna($permohonan_paket->getField($aColumns[$i]));
						} else {
							$row[] = getFormattedDate($permohonan_paket->getField($aColumns[$i]));
						}
					endif;
				else if($aColumns[$i] == "TANGGAL")
					$row[] = getFormattedDateS($permohonan_paket->getField($aColumns[$i]));
				else if($aColumns[$i] == "TAHUN_ANGGARAN")
					$row[] = '<input style="cursor:pointer" class="check" type="checkbox" value="'.$permohonan_paket->getField('PERMOHONAN_PAKET_ANALISA_ID').'"> '.$permohonan_paket->getField($aColumns[$i]);
				else if($aColumns[$i] == "NAMA")
					if ($permohonan_paket->getField("PUBLISH") == '1') {
						if ($permohonan_paket->getField("POSTING") == '1') {
							$row[] = $permohonan_paket->getField($aColumns[$i]).'<br> <span class="badge badge-info">Sudah Diteruskan</span> <span class="badge badge-primary">Sedang Publish</span><br>'.$keterangan_ulang;
						} else {
							$row[] = $permohonan_paket->getField($aColumns[$i]).'<br> <span class="badge badge-primary">Sedang Publish</span><br>'.$keterangan_ulang;
						}
					} else {
						if ($permohonan_paket->getField("POSTING") == '1' && $permohonan_paket->getField("NAMA_PIC") == '') {
							$row[] = $permohonan_paket->getField($aColumns[$i]).'<br> <span class="badge badge-info">Sudah Diteruskan</span><br>'.$keterangan_ulang;
						} else {
							$row[] = $permohonan_paket->getField($aColumns[$i]).'<br>'.$keterangan_ulang;
						}
					}
				else if($aColumns[$i] == "KETERANGAN")
					$row[] = truncate($permohonan_paket->getField($aColumns[$i]), 5)."...";
				else if($aColumns[$i] == "STATUS_KETERANGAN")
					$row[] = "<div align='center'>".$permohonan_paket->getField($aColumns[$i])."</div>";
				else if($aColumns[$i] == "RENCANA_PENGADAAN")
					$row[] = substr(getFormattedDateShort($permohonan_paket->getField($aColumns[$i])),3,10);
					// $row[] = getFormattedDateShort($permohonan_paket->getField($aColumns[$i]));
				else
					$row[] = $permohonan_paket->getField($aColumns[$i]);
			}

			$output['aaData'][] = $row;
		}

		echo json_encode( $output );
	}

	function permohonan_lelang_monitoring_unit_json()
	{
		$this->load->library("kauth");  $userLogin = new kauth();
		$this->load->model("PermohonanPaket");
		$permohonan_paket = new PermohonanPaket();

		ini_set("memory_limit","500M");
		ini_set('max_execution_time', 5200);

		$reqStatus= $this->input->get("reqStatus");

		$aColumns = array("PERMOHONAN_PAKET_ID", "POSTING", "STATUS_KETERANGAN","PUBLISH","TAHUN_ANGGARAN","NO_PPA", "TANGGAL", "NAMA", "NILAI", "NAMA_PIC");
		$aColumnsAlias = array("A.PERMOHONAN_PAKET_ID", "POSTING", "STATUS_KETERANGAN", "PUBLISH", "TAHUN_ANGGARAN","NO_PPA", "TANGGAL", "NAMA", "NILAI", "NAMA_PIC");

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

			if ( trim($sOrder) == "ORDER BY RECRUITMENT asc, RECRUITMENT asc" )
			{
				$sOrder = " ORDER BY A.RECRUITMENT_ID ASC, A.TANGGAL_AWAL_REN ASC";

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

		// $statement="AND G.APPROVAL='1' ";
		$statement="";
		$searchJson='';

		if($reqStatus == '')
		{}
		elseif($reqStatus == '0')
			$statement .= " AND A.POSTING IS NULL ";
		elseif($reqStatus == '1')
			$statement .= " AND D.PAKET_ID IS NULL AND A.POSTING IS NOT NULL ";
		elseif($reqStatus == '2')
			$statement .= " AND D.PAKET_ID IS NOT NULL AND A.POSTING IS NOT NULL ";


		$searchJson .= " AND (UPPER(A.NAMA) LIKE '%".strtoupper($_GET['sSearch'])."%' OR UPPER(A.NOTA_DINAS) LIKE '%".strtoupper($_GET['sSearch'])."%')";

		$allRecord = $permohonan_paket->getCountByParams(array("G.APPROVAL" => 1, "G.CREATED_BY" => $this->USER_LOGIN_ID), $statement);
		if($_GET['sSearch'] == "")
			$allRecordFilter = $allRecord;
		else
			$allRecordFilter = $permohonan_paket->getCountByParams(array("G.APPROVAL" => 1, "G.CREATED_BY" => $this->USER_LOGIN_ID), $statement.$searchJson);

		$permohonan_paket->selectByParams(array("G.APPROVAL" => 1, "G.CREATED_BY" => $this->USER_LOGIN_ID), $dsplyRange, $dsplyStart, $statement.$searchJson, $sOrder);

		/* Output */
		$output = array(
			"sEcho" => intval($_GET['sEcho']),
			"iTotalRecords" => $allRecord,
			"iTotalDisplayRecords" => $allRecordFilter,
			"aaData" => array()
		);

		while($permohonan_paket->nextRow())
		{
			$ulang = $permohonan_paket->getField('PAKET_ID_ULANG');
			if ($ulang != '') { $keterangan_ulang = '<div class="badge badge-danger">Paket Gagal</div>';
			} else { $keterangan_ulang = ''; }

			$row = array();
			for ( $i=0 ; $i<count($aColumns) ; $i++ )
			{
				if($aColumns[$i] == "TANGGAL" || $aColumns[$i] == "NILAI" || $aColumns[$i] == "NAMA_PIC")
					if ($permohonan_paket->getField($aColumns[$i]) == ''):
						$row[] = '<small style="color:#b7b7b7">-- Not Set --</small>';
					else:
						if($aColumns[$i] == "NILAI") {
							$row[] = numberToIna($permohonan_paket->getField($aColumns[$i]));
						} else {
							$row[] = getFormattedDate($permohonan_paket->getField($aColumns[$i]));
						}
					endif;
				else if($aColumns[$i] == "TANGGAL")
					$row[] = getFormattedDateS($permohonan_paket->getField($aColumns[$i]));
				else if($aColumns[$i] == "NAMA")
					if ($permohonan_paket->getField("PUBLISH") == '1') {
						if ($permohonan_paket->getField("POSTING") == '1') {
							$row[] = $permohonan_paket->getField($aColumns[$i]).'<br> <span class="badge badge-info">Sudah Diteruskan</span> <span class="badge badge-primary">Sedang Publish</span><br>'.$keterangan_ulang;
						} else {
							$row[] = $permohonan_paket->getField($aColumns[$i]).'<br> <span class="badge badge-primary">Sedang Publish</span><br>'.$keterangan_ulang;
						}
					} else {
						if ($permohonan_paket->getField("POSTING") == '1' && $permohonan_paket->getField("NAMA_PIC") == '') {
							$row[] = $permohonan_paket->getField($aColumns[$i]).'<br> <span class="badge badge-info">Sudah Diteruskan</span><br>'.$keterangan_ulang;
						} else {
							$row[] = $permohonan_paket->getField($aColumns[$i]).'<br>'.$keterangan_ulang;
						}
					}
				else if($aColumns[$i] == "KETERANGAN")
					$row[] = truncate($permohonan_paket->getField($aColumns[$i]), 5)."...";
				else if($aColumns[$i] == "STATUS_KETERANGAN")
					$row[] = "<div align='center'>".$permohonan_paket->getField($aColumns[$i])."</div>";
				else
					$row[] = $permohonan_paket->getField($aColumns[$i]);
			}

			$output['aaData'][] = $row;
		}

		echo json_encode( $output );
	}

	function permohonan_lelang_panitia_monitoring_json()
	{
		$this->load->library("kauth");  $userLogin = new kauth();
		$this->load->model("PermohonanPaket");

		$permohonan_paket = new PermohonanPaket();

		ini_set("memory_limit","500M");
		ini_set('max_execution_time', 5200);
		$statement = '';

		$reqStatus= $this->input->get("reqStatus");

		$aColumns = array("PERMOHONAN_PAKET_ID", "STATUS", "KAJI_ULANG", "PERMOHONAN_PAKET_ID_ENCRYPT", "STATUS_KETERANGAN", "NO_PPA", "TANGGAL", "NAMA", "NILAI_HPS_PR", "NAMA_PIC","KODE_RUP","KODE_PR");
		$aColumnsAlias = array("A.PERMOHONAN_PAKET_ID", "STATUS", "KAJI_ULANG", "PERMOHONAN_PAKET_ID", "STATUS_KETERANGAN",  "NO_PPA", "TANGGAL", "NAMA", "NILAI_HPS_PR", "NAMA_PIC","KODE_RUP","KODE_PR");

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

			if ( trim($sOrder) == "ORDER BY RECRUITMENT asc, RECRUITMENT asc" )
			{
				$sOrder = " ORDER BY A.RECRUITMENT_ID ASC, A.TANGGAL_AWAL_REN ASC";

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

		if($reqStatus == '')
		{}
		elseif($reqStatus == '0')
			$statement .= " AND A.POSTING IS NOT NULL ";
		elseif($reqStatus == '1')
			$statement .= " AND D.PAKET_ID IS NULL AND A.POSTING IS NOT NULL ";
		elseif($reqStatus == '2')
			$statement .= " AND D.PAKET_ID IS NOT NULL AND A.POSTING IS NOT NULL ";

		//$statement  .= " AND A.UNIT_KERJA_ID = '".$this->UNIT_KERJA_ID."' ";
		if ($this->USER_TYPE_ID == 3) { // Pokka Ketua
			$statement .= " AND A.POSTING IS NOT NULL AND PIC = ".$this->USER_LOGIN_ID."";
		}

		if ($this->USER_TYPE_ID == 3) { // Pokja
			$statement .= " AND KAJI_ULANG = '1' ";
		}
		// 	$statement .= " AND A.POSTING IS NOT NULL AND PIC = ".$this->USER_LOGIN_ID."";
		// }

		if ($this->USER_TYPE_ID == 11) { // Purchaser dan panitia
			$statement .= " AND A.PIC = '".$this->USER_LOGIN_ID."'";
			// echo $statement;
		}

		$searchJson .= " AND (UPPER(A.NAMA) LIKE '%".strtoupper($_GET['sSearch'])."%' OR UPPER(A.NOTA_DINAS) LIKE '%".strtoupper($_GET['sSearch'])."%')";
		$allRecord = $permohonan_paket->getCountByParams(array("A.UNIT_KERJA_ID" => $this->UNIT_KERJA_ID),$statement);
		if($_GET['sSearch'] == "")
			$allRecordFilter = $allRecord;
		else
			$allRecordFilter = $permohonan_paket->getCountByParams(array("A.UNIT_KERJA_ID" => $this->UNIT_KERJA_ID), $statement.$searchJson);

		$permohonan_paket->selectByParams(array("A.UNIT_KERJA_ID" => $this->UNIT_KERJA_ID), $dsplyRange, $dsplyStart, $statement.$searchJson, $sOrder);

		/* Output */
		$output = array(
			"sEcho" => intval($_GET['sEcho']),
			"iTotalRecords" => $allRecord,
			"iTotalDisplayRecords" => $allRecordFilter,
			"aaData" => array()
		);

		while($permohonan_paket->nextRow())
		{
		  $ulang = $permohonan_paket->getField('PAKET_ID_ULANG');
			if ($ulang != '') { $keterangan_ulang = '<div class="badge badge-danger">Paket Gagal</div>';
			} else { $keterangan_ulang = ''; }

			$row = array();
			for ( $i=0 ; $i<count($aColumns) ; $i++ )
			{
				if($aColumns[$i] == "TANGGAL")
					$row[] = getFormattedDate($permohonan_paket->getField($aColumns[$i]));
				else if($aColumns[$i] == "NILAI_HPS_PR")
					$row[] = numberToIna($permohonan_paket->getField($aColumns[$i]));
				else if($aColumns[$i] == "NAMA")
					$row[] = str_replace(",","",$permohonan_paket->getField($aColumns[$i])).'<br>'.$keterangan_ulang;
				else if($aColumns[$i] == "KETERANGAN")
					$row[] = truncate($permohonan_paket->getField($aColumns[$i]), 5)."...";
				else if($aColumns[$i] == "STATUS_KETERANGAN")
					$row[] = "<div align='center'>".$permohonan_paket->getField($aColumns[$i])."</div>";
				else
					$row[] = $permohonan_paket->getField($aColumns[$i]);
			}

			$output['aaData'][] = $row;
		}

		echo json_encode( $output );
	}

	function permohonan_lelang_panitia_monitoring_kajiulang_json()
	{
		$this->load->library("kauth");  $userLogin = new kauth();
		$this->load->model("PermohonanPaket");

		$permohonan_paket = new PermohonanPaket();

		ini_set("memory_limit","500M");
		ini_set('max_execution_time', 5200);
		$statement = '';

		$reqStatus= $this->input->get("reqStatus");

		$aColumns = array("PERMOHONAN_PAKET_ID", "STATUS", "PERMOHONAN_PAKET_ID_ENCRYPT", "STATUS_KETERANGAN", "NO_PPA", "TANGGAL", "NAMA", "NILAI_HPS_PR", "NAMA_PIC","KODE_RUP","KODE_PR","KAJI_ULANG");
		$aColumnsAlias = array("A.PERMOHONAN_PAKET_ID", "STATUS", "PERMOHONAN_PAKET_ID", "STATUS_KETERANGAN",  "NO_PPA", "TANGGAL", "NAMA", "NILAI_HPS_PR", "NAMA_PIC","KODE_RUP","KODE_PR","KAJI_ULANG");

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

			if ( trim($sOrder) == "ORDER BY RECRUITMENT asc, RECRUITMENT asc" )
			{
				$sOrder = " ORDER BY A.RECRUITMENT_ID ASC, A.TANGGAL_AWAL_REN ASC";

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


		if ($this->USER_TYPE_ID == 3) { // Purchaser dan panitia

			// Cek Kelompok Kerja
			$this->load->model("Queryfree");
			$getPokjaID = new Queryfree();
  			$getPokjaID->selectByParams("SELECT sk_panitia_id, user_login_id, a.nama, a.nip, b.nip
										FROM panitia a 
										JOIN user_login b on a.nip=b.nip
										WHERE USER_LOGIN_ID = ".$this->USER_LOGIN_ID."
										");
  			$getPokjaID->firstRow();
  			$SK = $getPokjaID->getField("SK_PANITIA_ID");

			$statement .= " AND A.POSTING IS NOT NULL AND SK_PANITIA_ID = ".$SK." AND A.KAJI_ULANG = '0' AND STRATEGI_PENGADAAN = 'Sourcing'"; 
			// $statement .= " AND A.POSTING IS NOT NULL AND PIC = ".$this->USER_LOGIN_ID." AND A.KAJI_ULANG = '0' AND STRATEGI_PENGADAAN = 'Sourcing'"; 
			$statement .= " AND D.PAKET_ID IS NULL AND A.POSTING IS NOT NULL ";
		} else if ($this->USER_TYPE_ID == 27 || $this->USER_TYPE_ID == 28){
			$statement .= " AND A.POSTING IS NOT NULL AND A.KAJI_ULANG = '0' AND STRATEGI_PENGADAAN = 'Sourcing' AND PIC IS NOT NULL"; 
			$statement .= " AND D.PAKET_ID IS NULL AND A.POSTING IS NOT NULL ";
		} else if ($this->USER_TYPE_ID == 7){ // KPP atau Manager Pengadaan
			$statement .= " AND A.POSTING IS NOT NULL AND A.KAJI_ULANG = '0' AND STRATEGI_PENGADAAN = 'Sourcing' AND PIC IS NOT NULL"; 
			$statement .= " AND D.PAKET_ID IS NULL AND A.POSTING IS NOT NULL ";
		}

		$searchJson .= " AND (UPPER(A.NAMA) LIKE '%".strtoupper($_GET['sSearch'])."%' OR UPPER(A.NOTA_DINAS) LIKE '%".strtoupper($_GET['sSearch'])."%')";
		$allRecord = $permohonan_paket->getCountByParams(array("A.UNIT_KERJA_ID" => $this->UNIT_KERJA_ID),$statement);
		if($_GET['sSearch'] == "")
			$allRecordFilter = $allRecord;
		else
			$allRecordFilter = $permohonan_paket->getCountByParams(array("A.UNIT_KERJA_ID" => $this->UNIT_KERJA_ID), $statement.$searchJson);

		$permohonan_paket->selectByParams(array("A.UNIT_KERJA_ID" => $this->UNIT_KERJA_ID), $dsplyRange, $dsplyStart, $statement.$searchJson, $sOrder);

		/* Output */
		$output = array(
			"sEcho" => intval($_GET['sEcho']),
			"iTotalRecords" => $allRecord,
			"iTotalDisplayRecords" => $allRecordFilter,
			"aaData" => array()
		);

		while($permohonan_paket->nextRow())
		{
		  $ulang = $permohonan_paket->getField('PAKET_ID_ULANG');
			if ($ulang != '') { $keterangan_ulang = '<div class="badge badge-danger">Paket Gagal</div>';
			} else { $keterangan_ulang = ''; }

			$row = array();
			for ( $i=0 ; $i<count($aColumns) ; $i++ )
			{
				if($aColumns[$i] == "TANGGAL")
					$row[] = getFormattedDate($permohonan_paket->getField($aColumns[$i]));
				else if($aColumns[$i] == "NILAI_HPS_PR")
					$row[] = numberToIna($permohonan_paket->getField($aColumns[$i]));
				else if($aColumns[$i] == "NAMA")
					$row[] = $permohonan_paket->getField($aColumns[$i]).'<br>'.$keterangan_ulang;
				else if($aColumns[$i] == "KETERANGAN")
					$row[] = truncate($permohonan_paket->getField($aColumns[$i]), 5)."...";
				else if($aColumns[$i] == "STATUS_KETERANGAN")
					$row[] = "<div align='center'>".$permohonan_paket->getField($aColumns[$i])."</div>";
				else
					$row[] = $permohonan_paket->getField($aColumns[$i]);
			}

			$output['aaData'][] = $row;
		}

		echo json_encode( $output );
	}

	function permohonan_lelang_tunjuk_pic_json()
	{
		$this->load->library("kauth");  $userLogin = new kauth();
		$this->load->model("PermohonanPaket");

		$this->load->model("UserLogin");
		$user_login_jabatan = new UserLogin();
		$user_login_jabatan->selectByParams(array("USER_LOGIN_ID"=> $this->USER_LOGIN_ID));
		$user_login_jabatan->firstRow();

		$permohonan_paket = new PermohonanPaket();

		ini_set("memory_limit","500M");
		ini_set('max_execution_time', 5200);

		$reqStatus= $this->input->get("reqStatus");
		$reqStatusPIC = $this->input->get("reqStatusPIC");

		$aColumns = array("PERMOHONAN_PAKET_ID", "STATUS_PIC", "PERMOHONAN_PAKET_ID_ENCRYPT","PERMOHONAN_PAKET_ANALISA_ID","SIRUP_ID", "STATUS_KETERANGAN", "LAST_CREATE_USER", "NO_PPA", "TANGGAL", "NAMA", "NILAI_HPS_PR", "NAMA_PIC","KODE_RUP","KODE_PR","ALASAN_TOLAK");
		$aColumnsAlias = array("A.PERMOHONAN_PAKET_ID", "STATUS_PIC", "PERMOHONAN_PAKET_ID","PERMOHONAN_PAKET_ANALISA_ID","SIRUP_ID", "STATUS_KETERANGAN", "LAST_CREATE_USER",  "NO_PPA", "TANGGAL", "NAMA", "NILAI_HPS_PR", "NAMA_PIC","KODE_RUP","KODE_PR","ALASAN_TOLAK");

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

			if ( trim($sOrder) == "ORDER BY PERMOHONAN_PAKET_ID asc, PERMOHONAN_PAKET_ID asc" )
			{
				$sOrder = " ORDER BY A.PERMOHONAN_PAKET_ID ASC, A.PERMOHONAN_PAKET_ID ASC";
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

		/*if($reqStatus == '')
		{}
		elseif($reqStatus == '0')
			$statement .= " AND POSTING IS NULL ";
		elseif($reqStatus == '1')
			$statement .= " AND D.PAKET_ID IS NULL AND POSTING IS NOT NULL ";
		elseif($reqStatus == '2')
			$statement .= " AND D.PAKET_ID IS NOT NULL AND POSTING IS NOT NULL ";*/

		//$statement  .= " AND A.UNIT_KERJA_ID = '".$this->UNIT_KERJA_ID."' ";
		// if ($user_login_jabatan->getField('PENUNJUK_PIC') == '1') {
		// 	$statement .= " AND A.POSTING IS NOT NULL ";
		// } else {
			// $statement .= " AND A.POSTING IS NOT NULL AND E.VP_PENGADAAN = '".$this->USER_LOGIN_ID."'";
		// }

		if($this->USER_TYPE_ID == 7) // MANAJER PENGADAAN
	    {
			$statement .=" AND A.POSTING IS NOT NULL AND STRATEGI_PENGADAAN = 'Sourcing' ";
		} else if($this->USER_TYPE_ID == 11) // PELAKSANA PEMBELI AS PIC
	    {
			$statement .=" AND A.POSTING IS NOT NULL AND STRATEGI_PENGADAAN = 'Purchasing' ";
		} else {
			$statement .=" AND A.POSTING IS NOT NULL AND STRATEGI_PENGADAAN = '0' ";
		}

		if($reqStatusPIC == '1') // Belum Diproses
			$statement .= "  AND (A.PIC IS NULL) ";
		elseif($reqStatusPIC == '2') // Sudah Diproses
			$statement .= " AND PIC IS NOT NULL ";
		elseif($reqStatusPIC == '') // Semua atau 0
		{}


		$searchJson .= " AND (UPPER(A.NAMA) LIKE '%".strtoupper($_GET['sSearch'])."%'  OR UPPER(A.KODE_RUP) LIKE '%".strtoupper($_GET['sSearch'])."%' OR UPPER(A.KODE_PR) LIKE '%".strtoupper($_GET['sSearch'])."%')";
		$allRecord = $permohonan_paket->getCountByParams(array(),$statement);
		if($_GET['sSearch'] == "")
			$allRecordFilter = $allRecord;
		else
			$allRecordFilter = $permohonan_paket->getCountByParams(array(), $statement.$searchJson);

		$permohonan_paket->selectByParams(array(), $dsplyRange, $dsplyStart, $statement.$searchJson, $sOrder);
		//echo "dsds".$permohonan_paket->query;exit;
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
				if ($aColumns[$i] == "ALASAN_TOLAK") {
					if ($permohonan_paket->getField("STATUS_PIC") == '1') {
						$statusTolak = '-';
					} else {
						if ($permohonan_paket->getField("ALASAN_TOLAK") != '') {
							$statusTolak = '<span class="fa fa-exclamation-triangle" style="color:red"></span> '.$permohonan_paket->getField("ALASAN_TOLAK");
						} else {
							$statusTolak = '-';
						}
					}
				}

				if($aColumns[$i] == "TANGGAL" || $aColumns[$i] == "NILAI_HPS_PR" || $aColumns[$i] == "NAMA_PIC")
					if ($permohonan_paket->getField($aColumns[$i]) == ''):
						$row[] = '<small style="color:#b7b7b7">-- Not Set --</small>';
					else:
						if($aColumns[$i] == "NILAI_HPS_PR") {
							$row[] = numberToIna($permohonan_paket->getField($aColumns[$i]));
						} else {
							$row[] = getFormattedDate($permohonan_paket->getField($aColumns[$i]));
						}
					endif;
				else if($aColumns[$i] == "TANGGAL")
					$row[] = getFormattedDate($permohonan_paket->getField($aColumns[$i]));
				else if($aColumns[$i] == "NAMA")
					$row[] = str_replace(",","",$permohonan_paket->getField($aColumns[$i]));
				else if($aColumns[$i] == "NILAI_HPS_PR")
					$row[] = numberToIna($permohonan_paket->getField($aColumns[$i]));
				else if($aColumns[$i] == "ALASAN_TOLAK")
					$row[] = $statusTolak;
				else if($aColumns[$i] == "KETERANGAN")
					$row[] = truncate($permohonan_paket->getField($aColumns[$i]), 5)."...";
				else if($aColumns[$i] == "STATUS_KETERANGAN")
					$row[] = "<div align='center'>".$permohonan_paket->getField($aColumns[$i])."</div>";
				else
					$row[] = $permohonan_paket->getField($aColumns[$i]);
			}

			$output['aaData'][] = $row;
		}

		echo json_encode( $output );
	}

	function delete()
	{
		$this->load->model('PermohonanPaket');
		$this->load->model('PermohonanPaketFile');
		$this->load->model('PaketPenawaran');
		$this->load->model('Rekamjejak');

		$permohonan_paket	= new PermohonanPaket();
		$permohonan_paket_file	= new PermohonanPaketFile();
		$paket_penawaran	= new PaketPenawaran();
		$rekanjejak	= new Rekamjejak();

		$reqId		= $this->input->get('reqId');
		$permohonan_paket_file	= new PermohonanPaketFile();
		$permohonan_paket_file->setField("PERMOHONAN_PAKET_ID", $reqId);
		if($permohonan_paket_file->deletePermohonan())
		{
			$permohonan_paket	= new PermohonanPaket();
			$permohonan_paket->setField("PERMOHONAN_PAKET_ID", $reqId);
			$permohonan_paket->delete();

			$paket_penawaran->setField("PERMOHONAN_PAKET_ID", $reqId);
			$paket_penawaran->deletePermohonan();

			$rekanjejak->setField("ID", $reqId);
			$rekanjejak->deletePermohonan();
		}
		echo "Data berhasil disimpan.";
	}

	function publish_usulan()
	{
		$reqId =  $this->input->get('reqId');

		$this->load->model("PermohonanPaket");
		$this->load->model("PaketPenawaran");

		$paket_penawaran_usulan = new PermohonanPaket();

		$paket_penawaran_usulan->selectByParamsUsulan(array("A.PERMOHONAN_PAKET_ID" => $reqId));
		$paket_penawaran_usulan->firstRow();
		$ppai = $paket_penawaran_usulan->getField("PERMOHONAN_PAKET_ANALISA_ID");
		$publish = $paket_penawaran_usulan->getField("PUBLISH");

		if ($publish == '1') {
			// echo "Rencana Pengadaan Sudah Dipublish";
			$permohonan_paket_usulan = new PermohonanPaket();
			$permohonan_paket_usulan->setField("PERMOHONAN_PAKET_ANALISA_ID", $ppai);
			$permohonan_paket_usulan->setField("PUBLISH", 0);

			if($permohonan_paket_usulan->publish_permohonan())
				echo "Data Berhasil Di unpublish";
			else
				echo "Data Gagal Di unpublish";
		} else {
			$permohonan_paket_usulan = new PermohonanPaket();
			$permohonan_paket_usulan->setField("PERMOHONAN_PAKET_ANALISA_ID", $ppai);
			$permohonan_paket_usulan->setField("PUBLISH", 1);

			if($permohonan_paket_usulan->publish_permohonan())
				echo "Data Berhasil Dipublish";
			else
				echo "Data Gagal Dipublish";
		}

	}

	function publish_usulan_multi()
	{
		$id      = $this->input->post('chkId');

		$this->load->model("PermohonanPaket");
		$this->load->model("PaketPenawaran");

		$paket_penawaran_usulan = new PermohonanPaket();

		$permohonan_paket_usulan = new PermohonanPaket();
		$permohonan_paket_usulan->setField("PERMOHONAN_PAKET_ANALISA_ID", $id);
		$permohonan_paket_usulan->setField("PUBLISH", 1);

		$message       = "";
		$status        = "";
		if ($permohonan_paket_usulan->publish_permohonan_multi()) {
		  $status   .= 'SUKSES';
		  $message  .= ' Data berhasil di publish.';
		} else {
		  $status   .= 'GAGAL';
		  $message  .= ' Data gagal di publish.';
		}
		echo json_encode(array('respon' => $status, 'message' => $message));
	}

	function unpublish_usulan_multi()
	{
		$id      = $this->input->post('chkId');

		$this->load->model("PermohonanPaket");
		$this->load->model("PaketPenawaran");

		$paket_penawaran_usulan = new PermohonanPaket();

		$permohonan_paket_usulan = new PermohonanPaket();
		$permohonan_paket_usulan->setField("PERMOHONAN_PAKET_ANALISA_ID", $id);
		$permohonan_paket_usulan->setField("PUBLISH", 0);

		$message       = "";
		$status        = "";
		if ($permohonan_paket_usulan->publish_permohonan_multi()) {
		  $status   .= 'SUKSES';
		  $message  .= ' Data berhasil di unpublish.';
		} else {
		  $status   .= 'GAGAL';
		  $message  .= ' Data gagal di unpublish.';
		}
		echo json_encode(array('respon' => $status, 'message' => $message));
	}


	function kembali_permohonan()
	{
		$this->load->model("PermohonanPaket");

		$permohonan_paket = new PermohonanPaket();

		/* json set variable */
		$reqId =  $this->input->post('reqId');
		$reqAlasan =  $this->input->post('reqAlasan');

		$permohonan_paket = new PermohonanPaket();
		$permohonan_paket->setField("PERMOHONAN_PAKET_ID", $reqId);
		$permohonan_paket->setField("ALASAN_TOLAK", $reqAlasan);
		$permohonan_paket->setField("ALASAN_TOLAK_BY", $this->USER_NAMA);

		if($permohonan_paket->kembali()) {
			// Insert Rekam Jejak
		    $this->load->library("librekamjejak");
		    $this->librekamjejak->insertRJ('7',$reqAlasan,'null',$reqId,'7');
		    // param 1: Posisi/'1-22', param 2: Keterangan/'', param 3: Paket_id/'null', param 4: permohonan_id/'null'
		    // End Insert Rekam Jejak
			echo "Tolak Permohonan Berhasil";
		} else {
			echo "Tolak Permohonan Gagal";
		}

	}

	public function getUpdatePR()
	{
		sleep(0);

		$this->load->model("PermohonanPaket");
		$permohonan_paket = new PermohonanPaket();

		$statement="";
		// $statement .="AND G.APPROVAL='1' AND (KODE_PR IS NULL OR KODE_PR = '') AND KODE_RUP IS NOT NULL ";
		$statement .="AND G.APPROVAL='1' AND (KODE_PR IS NULL OR KODE_PR = '')";

		if($this->USER_TYPE_ID == 9) // PENGGUNA
        {
			$permohonan_paket->selectByParams(array("A.USER_LOGIN_ID" => $this->USER_LOGIN_ID), '-1', '-1', $statement);
		} else if($this->USER_TYPE_ID == 7) // KEPALA PENGADAAN
		{
			$permohonan_paket->selectByParams(array("E.VP_PENGADAAN" => $this->USER_LOGIN_ID), '-1', '-1', $statement);
		}

		$html  = '';

		if ($permohonan_paket->countRow() > 0) {
		$html .= '<p class="alert alert-warning">Terdapat <b>'.$permohonan_paket->countRow().'</b> data yang belum update No. PR nya, silahkan klik tombol ini <a style="color:#fff" class="round badge badge-danger" onclick="return aaa(\''.$this->USER_LOGIN_ID.'\')" id="btnUpdateNoPR"><span class="fa fa-cog"> Update No. PR</span></a></p>';

		}

		$html .= '<table class="table table-bordered table-hover">
	                  <tbody>
	                  	  <tr>
	                  	  	<th width="3%">No</th>
	                  	  	<th width="60%">Nama Paket</th>
	                  	  	<th width="15%">Kode RUP</th>
	                  	  	<th width="15%">No. PR</th>
	                      </tr>';
	    $totalDok = 0;
	    $no = 1;

	    while($permohonan_paket->nextRow())
		{
			$arryString = array('.',' ','-','_');
			$idnya = str_replace($arryString, '', $permohonan_paket->getField('KODE_RUP'));
	    	$html .= '
    				<tr>
	                    <td class="text-center">'.$no.'</td>
	                    <td>'.$permohonan_paket->getField('NAMA').'</td>
	                    <td>'.$permohonan_paket->getField('KODE_RUP').'</td>
	                    <td>'.$permohonan_paket->getField('KODE_PR').'<input id="'.$idnya.'" class="form-control easyui-validatebox span9" type="text" style="width:100%"><a style="color:#fff" class="round badge badge-danger" onclick="return bbb(\''.$permohonan_paket->getField('KODE_RUP').'\',\''.$idnya.'\')" id="btnUpdateNoPR"><span class="fa fa-cog"> Update manual No. PR</span></a></a></td>
	                </tr> ';

	    	$no++;
	    }
	    $html .= '
	                </tbody>
	              </table>';

		echo json_encode(array('respon' => 'false', 'message' => $html));
	}

	public function excUpdatePR($user_login_id)
	{
		sleep(0);

		$this->load->library("libapi");
		$this->load->model("PermohonanPaket");

        $url = URL_API_AGNES.'/purchase-request';
		$permohonan_paket = new PermohonanPaket();

		$statement="";
		$statement .="AND G.APPROVAL='1' AND (KODE_PR IS NULL OR KODE_PR = '') AND KODE_RUP IS NOT NULL ";

		if($this->USER_TYPE_ID == 9) // PENGGUNA
        {
			$permohonan_paket->selectByParams(array("A.USER_LOGIN_ID" => $this->USER_LOGIN_ID), '-1', '-1', $statement);
		} else if($this->USER_TYPE_ID == 7) // KEPALA PENGADAAN
		{
			$permohonan_paket->selectByParams(array("E.VP_PENGADAAN" => $this->USER_LOGIN_ID), '-1', '-1', $statement);
		}

		// $permohonan_paket->selectByParams(array("A.USER_LOGIN_ID" => $this->USER_LOGIN_ID), '-1', '-1', $statement);
		// echo $permohonan_paket->query; die;
		$total = 0;
		$totalRUP = $permohonan_paket->countRow();
		while($permohonan_paket->nextRow())
		{
            // untuk ambil PR by RUP
            $libapii = new libapi();
            $getPR = $libapii->getPRByRUP($url,$permohonan_paket->getField('KODE_RUP'));
            $dataPR = $getPR->results->data;
            if (count($dataPR) > 0) {

            	$permohonan_paket3 = new PermohonanPaket();
				$permohonan_paket3->setField('KODE_RUP', $permohonan_paket->getField('KODE_RUP'));
				$permohonan_paket3->setField('KODE_PR', $dataPR[0]->pr_number);
				$permohonan_paket3->setField('POSTING', '1');
				$permohonan_paket3->setField('POSTING_BY', $permohonan_paket->getField("PEMBUAT"));
				if($permohonan_paket3->updatePR()) {

				// Insert Rekam Jejak
				$this->load->library("librekamjejak");
				$this->librekamjejak->insertRJ('5','','null',$permohonan_paket->getField("PERMOHONAN_PAKET_ID"),'5');
				// End Insert Rekam Jejak

				}
            	// echo $dataPR[0]->pr_number;
				$total++;
            }
		}
		// echo $total.''.$totalRUP; die;
		if ($totalRUP == $total) {
			echo json_encode(array('respon' => 'true', 'message' => 'Berhasil'));
		} else {
			echo json_encode(array('respon' => 'false', 'message' => 'Gagal'));
		}

	}

	public function excManualUpdatePR($pr_number,$kode_rup)
	{
		$this->load->library("libapi");
		$this->load->model("PermohonanPaket");

        $url = URL_API_AGNES.'/purchase-request';
        if ($pr_number && $kode_rup) {
			$permohonan_paket = new PermohonanPaket();
			$statement="";
			$statement .="AND G.APPROVAL='1' AND (KODE_PR IS NULL OR KODE_PR = '') AND KODE_RUP = '".$kode_rup."' ";
			$permohonan_paket->selectByParams(array("A.USER_LOGIN_ID" => $this->USER_LOGIN_ID), '-1', '-1', $statement);

			$permohonan_paket3 = new PermohonanPaket();
			$permohonan_paket3->setField('KODE_RUP', $kode_rup);
			$permohonan_paket3->setField('KODE_PR', $pr_number);
			$permohonan_paket3->setField('POSTING', '1');
			$permohonan_paket3->setField('POSTING_BY', $this->USER_NAMA);
			if($permohonan_paket3->updatePR()) {

			// Insert Rekam Jejak
			$this->load->library("librekamjejak");
			$this->librekamjejak->insertRJ('5','','null',$permohonan_paket->getField("PERMOHONAN_PAKET_ID"),'5');
			// End Insert Rekam Jejak
			echo json_encode(array('respon' => 'true', 'message' => 'Berhasil'));
			} else {
			echo json_encode(array('respon' => 'false', 'message' => 'Gagal'));
			}
		} else {
        echo "keluar";
			echo json_encode(array('respon' => 'false', 'message' => 'Gagal'));
		}
	}

	public function updatePROne()
	{
		$this->load->library("libapi");
		$this->load->model("PermohonanPaket");

		$reqId		= $this->input->post('reqId');
		$pr_number	= $this->input->post('reqKodePR');

		$permohonan_paket3 = new PermohonanPaket();
		$permohonan_paket3->setField('PERMOHONAN_PAKET_ID', $reqId);
		$permohonan_paket3->setField('KODE_PR', $pr_number);
		$permohonan_paket3->setField('UPDATED_BY', $this->USER_LOGIN_ID);
		if($permohonan_paket3->updatePROne()) {
			echo "Data berhasil disimpan.";
		} else {
			echo "Data gagal disimpan.";
		}
	}
}
?>
