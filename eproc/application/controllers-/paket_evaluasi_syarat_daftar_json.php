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

class paket_evaluasi_syarat_daftar_json extends CI_Controller {

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

	function add()
	{
		// echo "<pre>"; print_r($this->input->post()); die();
		$this->load->library("paketinfo"); $paketInfo = new paketinfo();
		$this->load->model("PaketRekanan");
		$this->load->model("Paket");
		$this->load->model("EvaluasiSyaratDaftar");
		$this->load->model("PaketEvaluasiSyaratDaftar");

		$paket_rekanan = new PaketRekanan();
		$paket = new Paket();
		$evaluasi_syarat_daftar = new EvaluasiSyaratDaftar();
		$paket_evaluasi_syarat_daftar = new PaketEvaluasiSyaratDaftar();

		$reqBulan = date("m");
		$reqTahun = date("Y");

		/* VARIABLES */
		$reqMode = $this->input->post("reqMode");
		$reqId = $this->input->post("reqId");
		$reqNama = $this->input->post("reqNama");
		$submitSimpan = $this->input->post("submitSimpan");
		$reqRekananId = isset($_POST["reqRekananId"]) ? $_POST["reqRekananId"] : '';
		$reqSyarat = isset($_POST["reqSyarat"]) ? $_POST["reqSyarat"] : '';
		$reqRekeningKoranBatas = $this->input->post("reqRekeningKoranBatas");
		$reqKeuanganPPNBatas = $this->input->post("reqKeuanganPPNBatas");
		$reqKeuanganPPHBatas = $this->input->post("reqKeuanganPPHBatas");
		$reqSertifikatSyaratText= $this->input->post("reqSertifikatSyaratText");
		$reqKlasifikasiBatas= $this->input->post("reqKlasifikasiBatas");

		$reqEvaluasi = $_POST["reqEvaluasi"];
		$reqEvaluasiNumber = $_POST["reqEvaluasiNumber"];
		$reqEvaluasiBulan = isset($_POST["reqEvaluasiBulan"]) ? $_POST["reqEvaluasiBulan"] : '';
		$reqEvaluasiValue = isset($_POST["reqEvaluasiValue"]) ? $_POST["reqEvaluasiValue"] : '';
		$reqEvaluasiKeterangan = $_POST["reqEvaluasiKeterangan"];
		$reqCheck = isset($_POST["reqCheck"]) ? $_POST["reqCheck"] : array(0);
		$reqFieldName = $_POST["reqFieldName"];
		$reqFieldInfo = $_POST["reqFieldInfo"];

		if($submitSimpan == "SimpanSyarat")
		{
			$paket_evaluasi_syarat_daftar->setField("PAKET_ID", $reqId);

			if($paket_evaluasi_syarat_daftar->delete())
			{
				$paket_syarat_set_null = new Paket();
				$paket_syarat_set_null->setField("PAKET_ID", $reqId);
				$paket_syarat_set_null->update_set_null_syarat();
				unset($paket_syarat_set_null);

				$reqSyarat = 0;
				for($i=1;$i<=count($reqEvaluasi);$i++)
				{
					if($reqEvaluasi[$i] == "") {}
					else
					{
						$setCheck = isset($reqCheck[$i]) ? $reqCheck[$i] : 0;
						if($setCheck == 1)
						{
							$aa = $reqEvaluasiNumber[$i] ? $reqEvaluasiNumber[$i] : 0;
							$reqSyarat = 1;
							$paket_evaluasi_syarat_daftar_insert = new PaketEvaluasiSyaratDaftar();
							$paket_evaluasi_syarat_daftar_insert->setField("PAKET_ID", $reqId);
							$paket_evaluasi_syarat_daftar_insert->setField("NAMA", $reqEvaluasi[$i]);
							$paket_evaluasi_syarat_daftar_insert->setField("EVALUASI_NUMBER", $aa);
							$paket_evaluasi_syarat_daftar_insert->setField("KETERANGAN", $reqEvaluasiKeterangan[$i]);
							$paket_evaluasi_syarat_daftar_insert->insert();
							unset($paket_evaluasi_syarat_daftar_insert);

							if($reqFieldName[$i] == "")
							{}
							else
							{
								$paket_syarat = new Paket();
								$paket_syarat->setField("FIELD", $reqFieldName[$i]);
								$paket_syarat->setField("FIELD_VALUE", 1);
								$paket_syarat->setField("PAKET_ID", $reqId);
								$paket_syarat->update_dyna();
								unset($paket_syarat);
							}

							if($reqFieldInfo[$i] == "")
							{}
							else
							{
								$paket_syarat = new Paket();
								$paket_syarat->setField("FIELD", $reqFieldInfo[$i]);
								$paket_syarat->setField("FIELD_VALUE", "'".$reqEvaluasiValue[$i]."'");
								$paket_syarat->setField("PAKET_ID", $reqId);
								$paket_syarat->update_dyna();
								unset($paket_syarat);
							}

						}
					}
				}
				if($reqSyarat > 0)
					echo "Syarat pendaftaran berhasil disimpan.";
				else
					echo "Syarat Pendaftaran gagal di simpan, tambahkan salah satu syarat.";
			}
			else
			{
				echo "Syarat pendaftaran telah diupload oleh peserta, syarat tidak dapat diubah.";
			}

		}

	}

}
?>
