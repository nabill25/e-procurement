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

class paket_panitia_json extends CI_Controller {

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
		$this->NIP =  $this->kauth->getInstance()->getIdentity()->NIP;
	}

	function add()
	{
		$this->load->model("PaketPanitia");
		$this->load->model("Panitia");

		$paket_panitia = new PaketPanitia();
		$paket_panitia_backup = new PaketPanitia();

		/* VARIABLES */
		$reqId = $this->input->post("reqId");
		$submitSimpan = $this->input->post("submitSimpan");
		$reqNIP = $this->input->post("reqNIP") ?: '';
		// echo "<pre>"; print_r($reqNIP); die;
		if($submitSimpan == "Simpan")
		{
			// ikn 20190306
			$paket_panitia_backup->setField("PAKET_ID", $reqId);
			if ($paket_panitia_backup->beforeDelete()) {
				$paket_panitia->setField("PAKET_ID", $reqId);
				$paket_panitia->deleteByPaktaIntegritas();
			}

			$namaPanitia = '';

			if ($reqNIP) {
				for($i=0; $i<=count($reqNIP);$i++)
				{
					$setNIP = isset($reqNIP[$i]) ? $reqNIP[$i] : '';
					if($setNIP == "")
					{}
					else
					{
						$panitia = new Panitia();
						$panitia->selectByParams(array("NIP" => $reqNIP[$i]));
						$panitia->firstRow();
						//echo $panitia->query;exit;
						$namaPanitia .= $panitia->getField("NAMA").', ';
						$paket_panitia = new PaketPanitia();
						$paket_panitia->setField("PAKET_PANITIA_SK_ID", "NULL");
						$paket_panitia->setField("NAMA", $panitia->getField("NAMA"));
						$paket_panitia->setField("NIP", $panitia->getField("NIP"));
						$paket_panitia->setField("JABATAN", $panitia->getField("JABATAN"));
						$paket_panitia->setField("KETUA", $panitia->getField("KETUA"));
						$paket_panitia->setField("STATUS", 1);
						$paket_panitia->setField("PAKET_ID", $reqId);
						$paket_panitia->setField('CREATED_BY', $this->USER_LOGIN_ID);
						$paket_panitia->insert();
						unset($paket_panitia);
						unset($panitia);
					}
				}
			}

			// Insert Rekam Jejak
      $this->load->library("librekamjejak");
      $this->librekamjejak->insertRJ('14','Tim Pengadaan ('.$namaPanitia.')',$reqId,'null','14'); // param 1: Posisi/'null', param 2: Keterangan/'', param 3: Paket_id/'null', param 4: permohonan_id/'null'
      // End Insert Rekam Jejak

			echo 'Data berhasil di simpan.';
		}

	}

	function delete_daftar_panitia()
	{
		$this->load->model("PaketPanitia");
		$set= new PaketPanitia();
		$set->setField('PAKET_PANITIA_ID', $reqId);
		//echo $reqId
		if($set->deletePanitia())
		{
			echo "Data berhasil dihapus";
		}
		else
			echo "Data gagal dihapus";
		//echo $set->query;
		//echo "asd";
	}

	function pemenang_validasi_json()
	{
		$this->load->model("PaketPanitia");
		$paket_panitia = new PaketPanitia();

		$reqId = $this->input->get("reqId"); //var_dump($reqId);die();
		$reqKode = $this->input->get("reqKode"); //var_dump($reqKode);die();

		$paket_panitia->setField("PAKET_ID", $reqId);
		$paket_panitia->setField("VALIDASI_PEMENANG", "1"); //var_dump($this->ID);die();
		$paket_panitia->setField("PAKET_PANITIA_ID", $reqKode);
		$paket_panitia->setField("NIP", $this->NIP);
		if($paket_panitia->updateValidasiPemenang())
			$pesan = "Validasi berhasil.";
		else
			$pesan = "Validasi gagal.";

		$arrFinal = array("PESAN" => $pesan);

		echo json_encode($arrFinal);
	}

	function pemenang_validasi_json_tolak()
	{
		$this->load->model("PaketPanitia");
		$paket_panitia = new PaketPanitia();

		$reqId = $this->input->get("reqId");
		$reqNote3 = $this->input->get("reqNote3");

		$paket_panitia->setField("PAKET_ID", $reqId);
		$paket_panitia->setField("VALIDASI_PEMENANG", "2"); //var_dump($this->ID);die();
		$paket_panitia->setField("VALIDASI_PEMENANG_CATATAN", $reqNote3);
		$paket_panitia->setField("NIP", $this->NIP);
		if($paket_panitia->updateValidasiPemenangTolak())
			$pesan = "Tolak Penetapan berhasil.";
		else
			$pesan = "Tolak Penetapan gagal.";

		$arrFinal = array("PESAN" => $pesan);

		echo json_encode($arrFinal);
	}

	function tunjuk_ketua_json()
	{
		$this->load->model("PaketPanitia");
		$paket_panitia = new PaketPanitia();

		$reqId = $this->input->get("reqId");
		$reqNIP = $this->input->get("reqNIP");

		$paket_panitia->setField("PAKET_ID", $reqId);
		$paket_panitia->setField("KETUA", "1");
		$paket_panitia->setField("NIP", $reqNIP);
		$paket_panitia->setField("UPDATED_BY", $this->USER_LOGIN_ID);
		if($paket_panitia->updateKetua())
			$pesan = "Data berhasil disimpan.";
		else
			$pesan = "Data gagal disimpan.";

		$arrFinal = array("PESAN" => $pesan);

		echo json_encode($arrFinal);
	}

	function kunci_tim_pengadaan()
	{
		$this->load->model("PaketPanitia");
		$paket_panitia = new PaketPanitia();

		$reqId = $this->input->get("reqId");

		$paket_panitia->setField("PAKET_ID", $reqId);
		$paket_panitia->setField("UPDATED_BY", $this->USER_LOGIN_ID);
		if($paket_panitia->updateKunciTimPengadaan())
			echo "1";
		else
			echo "0";
	}

}
?>
