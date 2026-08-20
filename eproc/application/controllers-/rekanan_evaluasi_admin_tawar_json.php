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

class rekanan_evaluasi_admin_tawar_json extends CI_Controller {

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

	function add_administrasi()
	{
		$this->load->model("RekananEvaluasiAdminTawar");

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

	function set_evaluasi_admin()
	{
		$this->load->library("kauth");  $userLogin = new kauth();
		$this->load->model("RekananEvaluasiAdminTawar");
		$rekanan_evaluasi_admin = new RekananEvaluasiAdminTawar();

		$reqId = httpFilterGet("reqId");
		$arrId = explode(";", $reqId);

		$reqPaketRekananId = $arrId[0];
		$reqEvaluasiAdminId = $arrId[1];

		/* LOGIN CHECK */
		if ($userLogin->checkUserLogin())
		{
			$userLogin->retrieveUserInfo();
		}

		//, ,
		$check = $rekanan_evaluasi_admin->getCountByParams(array("PAKET_REKANAN_ID" => $reqPaketRekananId, "PAKET_EVAL_ADMIN_TAWAR_ID" => $reqEvaluasiAdminId));

		if($check == 0)
		{
			$rekanan_evaluasi_admin->setField("PAKET_REKANAN_ID", $reqPaketRekananId);
			$rekanan_evaluasi_admin->setField("PAKET_EVAL_ADMIN_TAWAR_ID", $reqEvaluasiAdminId);
			$rekanan_evaluasi_admin->insertStatus();
		}
		else
		{
			$rekanan_evaluasi_admin->setField("STATUS", "(SELECT DECODE(COALESCE(STATUS, 0), 0, 1, 0) FROM PAKET_EVAL_ADMIN_TAWAR X WHERE X.PAKET_EVAL_ADMIN_TAWAR_ID = A.PAKET_EVAL_ADMIN_TAWAR_ID)");
			$rekanan_evaluasi_admin->setField("PAKET_REKANAN_ID", $reqPaketRekananId);
			$rekanan_evaluasi_admin->setField("PAKET_EVAL_ADMIN_TAWAR_ID", $reqEvaluasiAdminId);
			$rekanan_evaluasi_admin->update();
		}
		$met = array();
		$i=0;

		$met[0]['STATUS'] = 1;
		echo json_encode($met);
	}

	function kriteria_penawaran()
	{
		$this->load->library("kauth");  $userLogin = new kauth();
		$this->load->library("paketinfo"); $paketInfo = new paketinfo();
		$this->load->model("PaketEvaluasiAdminTawar");
		$this->load->model("PaketEvaluasiTeknisTawar");
		$this->load->model("PaketEvaluasiHargaTawar");
		$this->load->model("MatrixEvaluasi");

		$paket_evaluasi_admin = new PaketEvaluasiAdminTawar();
		$paket_evaluasi_teknis = new PaketEvaluasiTeknisTawar();
		$paket_evaluasi_harga = new PaketEvaluasiHargaTawar();
		$matrix_evaluasi = new MatrixEvaluasi();

		/* VARIABLES */
		$reqMode = $this->input->post("reqMode");
		$reqId = $this->input->post("reqId");

		$submitSimpan = $this->input->post("submitSimpan");
		$reqEvaluasiAdministrasi = $_POST["reqEvaluasiAdministrasi"];
		$reqEvaluasiTeknis = $_POST["reqEvaluasiTeknis"];
		$reqEvaluasiHarga = $_POST["reqEvaluasiHarga"];
		$reqEvaluasiNumber =  $_POST["reqEvaluasiNumber"];
		$reqCheck =  $_POST["reqCheck"];
		$reqWajib =  $_POST["reqWajib"];
		$reqWajibTeknis=  $_POST["reqWajibTeknis"];
		$reqWajibHarga=  $_POST["reqWajibHarga"];

		// echo "<pre>";
		// print_r($reqEvaluasiHarga);
		// print_r($reqWajibHarga); die();
		// print_r($this->input->post()); die();

		$paketInfo->getPaket($reqId);
		$reqNama = $paketInfo->nama;
		$reqKualifikasi = $paketInfo->kualifikasi;
		$reqKualifikasiId = $paketInfo->kualifikasi_id;
		$reqMetodeLelangId = $paketInfo->metode_lelang_id;
		$reqNilai = $paketInfo->nilai;

		$reqNama =$paketInfo->nama;
		$reqJenisPekerjaanId = $paketInfo->jenis_id;
		$reqMetodeEvaluasiId = $paketInfo->metode_evaluasi_id;
		$reqJenisPekerjaan  = $paketInfo->jenis;
		$reqMetodeEvaluasi  = $paketInfo->metode_evaluasi;
		$reqSistemSampul	= $paketInfo->sistem_sampul;

		if($submitSimpan == "Simpan")
		{
			if ($reqEvaluasiAdministrasi) {
				$paket_evaluasi_admin->setField("PAKET_ID", $reqId);
				$paket_evaluasi_admin->delete();

				for($i=1; $i<=count($reqEvaluasiAdministrasi);$i++)
				{
					if($reqEvaluasiAdministrasi[$i] == "") {}
					else
					{
						if($reqCheck[$i] == 1)
						{
							$paket_evaluasi_admin_insert = new PaketEvaluasiAdminTawar();
							$paket_evaluasi_admin_insert->setField("PAKET_ID", $reqId);
							$paket_evaluasi_admin_insert->setField("NAMA", removeRegex($reqEvaluasiAdministrasi[$i]));
							$paket_evaluasi_admin_insert->setField("EVALUASI_NUMBER", "NULL");
							$paket_evaluasi_admin_insert->setField("WAJIB", coalesce($reqWajib[$i],0));
							$paket_evaluasi_admin_insert->setField('CREATED_BY', $this->USER_LOGIN_ID);
							$paket_evaluasi_admin_insert->insert();
						}
						unset($paket_evaluasi_admin_insert);
					}
				}
			}

			if ($reqEvaluasiTeknis) {
				$paket_evaluasi_teknis->setField("PAKET_ID", $reqId);
				$paket_evaluasi_teknis->delete();

				for($i=1; $i<=count($reqEvaluasiTeknis);$i++)
				{
					if($reqEvaluasiTeknis[$i] == "") {}
					else
					{
						$paket_evaluasi_teknis_insert = new PaketEvaluasiTeknisTawar();
						$paket_evaluasi_teknis_insert->setField("PAKET_ID", $reqId);
						$paket_evaluasi_teknis_insert->setField("NAMA", removeRegex($reqEvaluasiTeknis[$i]));
						$paket_evaluasi_teknis_insert->setField("WAJIB", coalesce($reqWajibTeknis[$i],0));
						$paket_evaluasi_teknis_insert->setField('CREATED_BY', $this->USER_LOGIN_ID);
						$paket_evaluasi_teknis_insert->insert();
					}
						unset($paket_evaluasi_teknis_insert);
				}
			}

			if ($reqEvaluasiHarga) {
				$paket_evaluasi_harga->setField("PAKET_ID", $reqId);
				$paket_evaluasi_harga->delete();

				for($i=1; $i<=count($reqEvaluasiHarga);$i++)
				{
					if($reqEvaluasiHarga[$i] == "") {}
					else
					{
						$paket_evaluasi_harga_insert = new PaketEvaluasiHargaTawar();
						$paket_evaluasi_harga_insert->setField("PAKET_ID", $reqId);
						$paket_evaluasi_harga_insert->setField("NAMA", removeRegex($reqEvaluasiHarga[$i]));
						$paket_evaluasi_harga_insert->setField("WAJIB", coalesce($reqWajibHarga[$i],0));
						$paket_evaluasi_harga_insert->setField('CREATED_BY', $this->USER_LOGIN_ID);
						$paket_evaluasi_harga_insert->insert();
					}
						unset($paket_evaluasi_harga_insert);
				}
			}

			// Insert Rekam Jejak
	        $this->load->library("librekamjejak");
	        $this->librekamjejak->insertRJ('13',$reqNamaDokumen,$reqId,'null','13'); // param 1: Posisi/'null', param 2: Keterangan/'', param 3: Paket_id/'null', param 4: permohonan_id/'null'
	        // End Insert Rekam Jejak

			echo "Data berhasil di Simpan";
		}

	}

	function evaluasi_penawaran()
	{

		$this->load->library("paketinfo"); $paketInfo = new paketinfo();
		$this->load->model("PaketEvaluasiAdminTawar");
		$this->load->model("PaketRekanan");
		$this->load->model("PaketDokumen");
		$this->load->model("RekananEvaluasiAdminTawar");
		$this->load->model("PaketTahap");

		$paket_tahap_metode = new PaketTahap();
		$paket_tahap = new PaketTahap();

		$paket_evaluasi_admin = new PaketEvaluasiAdminTawar();
		$paket_rekanan = new PaketRekanan();

		// $reqId = $this->input->get("reqId");
		$reqId = $this->input->post("reqId");
		$submitSimpan = $this->input->post("submitSimpan");
		// echo $reqId; die();
		$reqPaketRekananId = $_POST["reqPaketRekananId"];
		$reqPaketEvaluasiId = $_POST["reqPaketEvaluasiId"];
		$reqEvaluasiAdminSyarat = $_POST["reqEvaluasiAdminSyarat"];
		$reqUraian = $_POST["reqUraian"];
		$reqKeterangan = $_POST["reqKeterangan"];

		if($submitSimpan == "Simpan")
		{
			// Insert Rekam Jejak
		    $this->load->library("librekamjejak");
		    $this->librekamjejak->insertRJ('18','',$reqId,'null','18'); // param 1: Posisi/'null', param 2: Keterangan/'', param 3: Paket_id/'null', param 4: permohonan_id/'null'
		    // End Insert Rekam Jejak
			if (isset($reqPaketRekananId) > 0) {
				for($i=0;$i<count($reqPaketRekananId);$i++)
				{
					$rekanan_evaluasi_admin = new RekananEvaluasiAdminTawar();
					$check = $rekanan_evaluasi_admin->getCountByParams(array("PAKET_REKANAN_ID" => $reqPaketRekananId[$i], "PAKET_EVAL_ADMIN_TAWAR_ID" => $reqPaketEvaluasiId[$i]));

					$rekanan_evaluasi_admin->setField('CREATED_BY', $this->USER_LOGIN_ID);
					if($check == 0)
					{
						if ($reqEvaluasiAdminSyarat[$i] != '') {
							$rekanan_evaluasi_admin->setField("PAKET_REKANAN_ID", $reqPaketRekananId[$i]);
							$rekanan_evaluasi_admin->setField("PAKET_EVAL_ADMIN_TAWAR_ID", $reqPaketEvaluasiId[$i]);
							$rekanan_evaluasi_admin->setField("MEMENUHI_SYARAT", $reqEvaluasiAdminSyarat[$i]);
							$rekanan_evaluasi_admin->setField("URAIAN", $reqUraian[$i]);
							$rekanan_evaluasi_admin->setField("KETERANGAN", $reqKeterangan[$i]);
							$rekanan_evaluasi_admin->insertSyarat();
						}
					}
					else
					{
						$rekanan_evaluasi_admin->setField("MEMENUHI_SYARAT", $reqEvaluasiAdminSyarat[$i]);
						$rekanan_evaluasi_admin->setField("PAKET_REKANAN_ID", $reqPaketRekananId[$i]);
						$rekanan_evaluasi_admin->setField("PAKET_EVAL_ADMIN_TAWAR_ID", $reqPaketEvaluasiId[$i]);
						$rekanan_evaluasi_admin->setField("URAIAN", $reqUraian[$i]);
						$rekanan_evaluasi_admin->setField("KETERANGAN", $reqKeterangan[$i]);
						$rekanan_evaluasi_admin->updateSyarat();
					}
					unset($rekanan_evaluasi_admin);
				}
			} else { }
			echo 'Data berhasil di simpan.';
		}

	}

}
?>
