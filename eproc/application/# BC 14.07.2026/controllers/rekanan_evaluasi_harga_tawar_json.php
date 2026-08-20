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

class rekanan_evaluasi_harga_tawar_json extends CI_Controller {

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

	// function evaluasi_penawaran1()
	// {

	// 	$this->load->library("paketinfo"); $paketInfo = new paketinfo();
	// 	$this->load->model("PaketEvaluasiHargaTawar");
	// 	$this->load->model("PaketRekanan");
	// 	$this->load->model("PaketDokumen");
	// 	$this->load->model("RekananEvaluasiHargaTawar");
	// 	$this->load->model("PaketTahap");
	// 	$this->load->model("RekananPaketPenawaran");


	// 	$paket_tahap_metode = new PaketTahap();
	// 	$paket_tahap = new PaketTahap();

	// 	$paket_evaluasi_harga = new PaketEvaluasiHargaTawar();
	// 	$paket_rekanan = new PaketRekanan();

	// 	$reqId = $this->input->post("reqId");
	// 	$submitSimpan = $this->input->post("submitSimpan");
	// 	$reqPaketRekananId = $_POST["reqPaketRekananId"];
	// 	$reqPaketEvaluasiId = $_POST["reqPaketEvaluasiId"];
	// 	$reqEvaluasiHargaSyarat = $_POST["reqEvaluasiHargaSyarat"];
	// 	$reqUraian = $_POST["reqUraian"];
	// 	$reqKeterangan = $_POST["reqKeterangan"];

	// 	// Koreksi Aritkatika
	// 	$reqLinkFile= $_FILES['reqLinkFile'];
	// 	$reqRekananId = $_POST['reqRekananId'];
	// 	$reqPaketPenawaranId = $_POST['reqPaketPenawaranId'];
	// 	$reqQuantity = $_POST['reqQuantity'];
	// 	$reqPenawaranKoreksi = $_POST['reqPenawaranKoreksi'];
	// 	$reqPenawaranSebelumnya = $_POST["reqPenawaranSebelumnya"];

	// 	if($submitSimpan == "Simpan")
	// 	{
	// 		for($i=0;$i<count($reqPaketRekananId);$i++)
	// 		{
	// 			$rekanan_evaluasi_harga = new RekananEvaluasiHargaTawar();
	// 			$check = $rekanan_evaluasi_harga->getCountByParams(array("PAKET_REKANAN_ID" => $reqPaketRekananId[$i], "PAKET_EVAL_HARGA_TAWAR_ID" => $reqPaketEvaluasiId[$i]));
	// 			$rekanan_evaluasi_harga->setField('CREATED_BY', $this->USER_LOGIN_ID);

	// 			if($check == 0)
	// 			{
	// 				$rekanan_evaluasi_harga->setField("PAKET_REKANAN_ID", $reqPaketRekananId[$i]);
	// 				$rekanan_evaluasi_harga->setField("PAKET_EVAL_HARGA_TAWAR_ID", $reqPaketEvaluasiId[$i]);
	// 				$rekanan_evaluasi_harga->setField("MEMENUHI_SYARAT", $reqEvaluasiHargaSyarat[$i]);
	// 				$rekanan_evaluasi_harga->setField("URAIAN", $reqUraian[$i]);
	// 				$rekanan_evaluasi_harga->setField("KETERANGAN", $reqKeterangan[$i]);
	// 				$rekanan_evaluasi_harga->insertSyarat1();
	// 			}
	// 			else
	// 			{
	// 				$rekanan_evaluasi_harga->setField("MEMENUHI_SYARAT", $reqEvaluasiHargaSyarat[$i]);
	// 				$rekanan_evaluasi_harga->setField("PAKET_REKANAN_ID", $reqPaketRekananId[$i]);
	// 				$rekanan_evaluasi_harga->setField("PAKET_EVAL_HARGA_TAWAR_ID", $reqPaketEvaluasiId[$i]);
	// 				$rekanan_evaluasi_harga->setField("URAIAN", $reqUraian[$i]);
	// 				$rekanan_evaluasi_harga->setField("KETERANGAN", $reqKeterangan[$i]);
	// 				$rekanan_evaluasi_harga->updateSyarat1();
	// 			}
	// 			unset($rekanan_evaluasi_harga);

	// 			// Koreksi Aritatika
	// 			$paket_rekanan = new PaketRekanan();

	// 			$paket_rekanan->setField("FIELD1", "NILAI_PENAWARAN");
	// 			$paket_rekanan->setField("FIELD1_VALUE", dotToNo($reqPenawaranKoreksi[$i]));
	// 			$paket_rekanan->setField("FIELD2", "NILAI_PENAWARAN_SEBELUMNYA");
	// 			$paket_rekanan->setField("FIELD2_VALUE", dotToNo($reqPenawaranSebelumnya[$i]));
	// 			$paket_rekanan->setField("PAKET_REKANAN_ID", $reqPaketRekananId[$i]);
	// 			$paket_rekanan->updateTwoField();

	// 			unset($paket_rekanan);
	// 			// End Koreksi Aritatika
	// 		}


	// 		for($i=0;$i<count($reqPaketPenawaranId);$i++)
	// 		{
	// 			if($reqQuantity[$i] == "0")
	// 			{}
	// 			else
	// 			{
	// 				for($j=0;$j<count($reqPaketRekananId);$j++)
	// 				{
	// 					$rekanan_paket_penawaran = new RekananPaketPenawaran();

	// 					$rekanan_paket_penawaran->setField("UNIT_PRICE_KOREKSI", dotToNo($this->input->post("reqUnitPriceKoreksi".$reqPaketRekananId[$j]."-".$reqPaketPenawaranId[$i])));
	// 					$rekanan_paket_penawaran->setField("JUMLAH_KOREKSI", dotToNo($this->input->post("reqUnitPriceKoreksi".$reqPaketRekananId[$j]."-".$reqPaketPenawaranId[$i])));
	// 					// $rekanan_paket_penawaran->setField("JUMLAH_KOREKSI", dotToNo($this->input->post("reqJumlahKoreksi".$reqPaketRekananId[$j]."-".$reqPaketPenawaranId[$i])));
	// 					$rekanan_paket_penawaran->setField("PAKET_REKANAN_ID", $reqPaketRekananId[$j]);
	// 					$rekanan_paket_penawaran->setField("PAKET_PENAWARAN_ID", $reqPaketPenawaranId[$i]);
	// 					$rekanan_paket_penawaran->updateKoreksi();

	// 					unset($rekanan_paket_penawaran);
	// 				}
	// 			}
	// 		}

	// 		// Insert Rekam Jejak
	// 	    $this->load->library("librekamjejak");
	// 	    $this->librekamjejak->insertRJ('20','',$reqId,'null'); // param 1: Posisi/'null', param 2: Keterangan/'', param 3: Paket_id/'null', param 4: permohonan_id/'null'
	// 	    // End Insert Rekam Jejak

	// 		echo 'Data berhasil di simpan';
	// 	}

	// }

	function evaluasi_penawaran()
	{

		$this->load->library("paketinfo"); $paketInfo = new paketinfo();
		$this->load->model("PaketEvaluasiHargaTawar");
		$this->load->model("PaketRekanan");
		$this->load->model("PaketDokumen");
		$this->load->model("RekananEvaluasiHargaTawar");
		$this->load->model("PaketTahap");
		$this->load->model("RekananPaketPenawaran");


		$paket_tahap_metode = new PaketTahap();
		$paket_tahap = new PaketTahap();

		$paket_evaluasi_harga = new PaketEvaluasiHargaTawar();
		$paket_rekanan = new PaketRekanan();

		$reqId = $this->input->post("reqId");
		$submitSimpan = $this->input->post("submitSimpan");
		$reqPaketRekananId = $_POST["reqPaketRekananId"];
		$reqPaketEvaluasiId = $_POST["reqPaketEvaluasiId"];
		$reqEvaluasiHargaSyarat = $_POST["reqEvaluasiHargaSyarat"];
		$reqUraian = $_POST["reqUraian"];
		$reqKeterangan = $_POST["reqKeterangan"];
		$reqSkorHarga = $_POST["reqSkorHarga"] ? $_POST["reqSkorHarga"] : '0';
		$reqNilaiHarga = $_POST["reqNilaiHarga"] ? $_POST["reqNilaiHarga"] : '0';
		// echo $reqSkorHarga;
		// Koreksi Aritkatika
		$reqLinkFile= $_FILES['reqLinkFile'];
		$reqRekananId = $_POST['reqRekananId'];
		$reqPaketPenawaranId = $_POST['reqPaketPenawaranId'];
		$reqQuantity = $_POST['reqQuantity'];
		$reqPenawaranKoreksi = $_POST['reqPenawaranKoreksi'];
		$reqPenawaranSebelumnya = $_POST["reqPenawaranSebelumnya"];

		if($submitSimpan == "Simpan")
		{
			if (isset($reqPaketRekananId) > 0) {
				for($i=0;$i<count($reqPaketRekananId);$i++)
				{
					$skor_harga = $reqSkorHarga[$i] ? $reqSkorHarga[$i] : '0';
					$skor_nilai = $reqNilaiHarga[$i] ? $reqNilaiHarga[$i] : '0';

					$rekanan_evaluasi_harga = new RekananEvaluasiHargaTawar();
					$check = $rekanan_evaluasi_harga->getCountByParams(array("PAKET_REKANAN_ID" => $reqPaketRekananId[$i], "PAKET_EVAL_HARGA_TAWAR_ID" => $reqPaketEvaluasiId[$i]));
					$rekanan_evaluasi_harga->setField('CREATED_BY', $this->USER_LOGIN_ID);

					if($check == 0)
					{
						if ($reqEvaluasiHargaSyarat[$i] != '') {
							$rekanan_evaluasi_harga->setField("PAKET_REKANAN_ID", $reqPaketRekananId[$i]);
							$rekanan_evaluasi_harga->setField("PAKET_EVAL_HARGA_TAWAR_ID", $reqPaketEvaluasiId[$i]);
							$rekanan_evaluasi_harga->setField("MEMENUHI_SYARAT", $reqEvaluasiHargaSyarat[$i]);
							$rekanan_evaluasi_harga->setField("URAIAN", $reqUraian[$i]);
							$rekanan_evaluasi_harga->setField("KETERANGAN", $reqKeterangan[$i]);
							$rekanan_evaluasi_harga->setField("SKOR_HARGA", $skor_harga);
							$rekanan_evaluasi_harga->setField("NILAI_HARGA", $skor_nilai);
							$rekanan_evaluasi_harga->insertSyarat();
						}
					}
					else
					{
						$rekanan_evaluasi_harga->setField("MEMENUHI_SYARAT", $reqEvaluasiHargaSyarat[$i]);
						$rekanan_evaluasi_harga->setField("PAKET_REKANAN_ID", $reqPaketRekananId[$i]);
						$rekanan_evaluasi_harga->setField("PAKET_EVAL_HARGA_TAWAR_ID", $reqPaketEvaluasiId[$i]);
						$rekanan_evaluasi_harga->setField("URAIAN", $reqUraian[$i]);
						$rekanan_evaluasi_harga->setField("KETERANGAN", $reqKeterangan[$i]);
						$rekanan_evaluasi_harga->setField("SKOR_HARGA", $skor_harga);
						$rekanan_evaluasi_harga->setField("NILAI_HARGA", $skor_nilai);
						$rekanan_evaluasi_harga->updateSyarat();
					}
					unset($rekanan_evaluasi_harga);

					// Koreksi Aritatika
					// close ikn 20211225
					// $paket_rekanan = new PaketRekanan();

					// $paket_rekanan->setField("FIELD1", "NILAI_PENAWARAN");
					// $paket_rekanan->setField("FIELD1_VALUE", dotToNo($reqPenawaranKoreksi[$i]));
					// $paket_rekanan->setField("FIELD2", "NILAI_PENAWARAN_SEBELUMNYA");
					// $paket_rekanan->setField("FIELD2_VALUE", dotToNo($reqPenawaranSebelumnya[$i]));
					// $paket_rekanan->setField("PAKET_REKANAN_ID", $reqPaketRekananId[$i]);
					// $paket_rekanan->updateTwoField();

					// unset($paket_rekanan);
					// End Koreksi Aritatika
				}


				for($i=0;$i<count($reqPaketPenawaranId);$i++)
				{
					if($reqQuantity[$i] == "0")
					{}
					else
					{
						for($j=0;$j<count($reqPaketRekananId);$j++)
						{
							$rekanan_paket_penawaran = new RekananPaketPenawaran();

							$rekanan_paket_penawaran->setField("UNIT_PRICE_KOREKSI", CommaToNo($this->input->post("reqUnitPriceKoreksi".$reqPaketRekananId[$j]."-".$reqPaketPenawaranId[$i])));
							$rekanan_paket_penawaran->setField("JUMLAH_KOREKSI", CommaToNo($this->input->post("reqUnitPriceKoreksi".$reqPaketRekananId[$j]."-".$reqPaketPenawaranId[$i])));
							// $rekanan_paket_penawaran->setField("JUMLAH_KOREKSI", dotToNo($this->input->post("reqJumlahKoreksi".$reqPaketRekananId[$j]."-".$reqPaketPenawaranId[$i])));
							$rekanan_paket_penawaran->setField("PAKET_REKANAN_ID", $reqPaketRekananId[$j]);
							$rekanan_paket_penawaran->setField("PAKET_PENAWARAN_ID", $reqPaketPenawaranId[$i]);
							$rekanan_paket_penawaran->updateKoreksi();

							unset($rekanan_paket_penawaran);
						}
					}
				}
			} else {}

			// Insert Rekam Jejak
		    $this->load->library("librekamjejak");
		    $this->librekamjejak->insertRJ('20','',$reqId,'null','20'); // param 1: Posisi/'null', param 2: Keterangan/'', param 3: Paket_id/'null', param 4: permohonan_id/'null'
		    // End Insert Rekam Jejak

			echo 'Data berhasil di simpan';
		}

	}
}
?>
