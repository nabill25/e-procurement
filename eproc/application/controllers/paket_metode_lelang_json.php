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

class paket_metode_lelang_json extends CI_Controller {

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
                $this->USER_LOGIN =  $this->kauth->getInstance()->getIdentity()->USER_LOGIN;
	}

	function combo()
	{
		$this->load->model('Metode');

		$reqJenisPekerjaan = $this->input->get("reqJenisPekerjaan");

		$paket_metode_lelang = new Metode();

		$paket_metode_lelang->selectByParamsMetodeLelang(array('PAKET_JENIS_ID'=>$reqJenisPekerjaan, ' B.AKTIF' => "1", 'B.TENDER' => "1"));

		$i = 0;
		while($paket_metode_lelang->nextRow())
		{
			$arr_json[$i]['id']		= $paket_metode_lelang->getField("PAKET_METODE_LELANG_ID");
			$arr_json[$i]['text']	= $paket_metode_lelang->getField("PAKET_METODE_LELANG");
			$i++;
		}

		echo json_encode($arr_json);
	}

	function combopl()
	{
		$this->load->model('Metode');

		$reqJenisPekerjaan = $this->input->get("reqJenisPekerjaan");

		$paket_metode_lelang = new Metode();

		$paket_metode_lelang->selectByParamsMetodeLelang(array('PAKET_JENIS_ID'=>$reqJenisPekerjaan, ' B.AKTIF' => "1", ' A.PAKET_METODE_LELANG_ID' => "2"));

		$i = 0;
		while($paket_metode_lelang->nextRow())
		{
			$arr_json[$i]['id']		= $paket_metode_lelang->getField("PAKET_METODE_LELANG_ID");
			$arr_json[$i]['text']	= $paket_metode_lelang->getField("PAKET_METODE_LELANG");
			$i++;
		}

		echo json_encode($arr_json);
	}

	function combokatalog()
	{
		$this->load->model('Metode');

		$paket_metode_lelang = new Metode();

		$nilaiK = $this->input->get("nilaiK");

		// if ($nilaiK == '1') { // Nilai dibawah 100
		// 	$paket_metode_lelang->selectByParamsMetodeLelang(array('B.TENDER' => "3"));
		// } else { // Nilai diawas 100
		// 	$paket_metode_lelang->selectByParamsMetodeLelang(array('B.TENDER' => "3", "B.PAKET_METODE_LELANG_ID" => "6"));
		// }
			$paket_metode_lelang->selectByParamsMetodeLelang(array('B.TENDER' => "3"));

		$i = 0;
		while($paket_metode_lelang->nextRow())
		{
			$arr_json[$i]['id']		= $paket_metode_lelang->getField("PAKET_METODE_LELANG_ID");
			$arr_json[$i]['text']	= $paket_metode_lelang->getField("PAKET_METODE_LELANG");
			$i++;
		}

		echo json_encode($arr_json);
	}

	function combopur()
	{
		$this->load->model('Metode');

		$reqJenisPekerjaan = $this->input->get("reqJenisPekerjaan");

		$paket_metode_lelang = new Metode();

		$paket_metode_lelang->selectByParamsMetodeLelang(array('PAKET_JENIS_ID'=>$reqJenisPekerjaan, ' B.AKTIF' => "1", ' A.PAKET_METODE_LELANG_ID' => "6"));
		// echo $paket_metode_lelang->query; exit();
		$i = 0;
		while($paket_metode_lelang->nextRow())
		{
			$arr_json[$i]['id']		= $paket_metode_lelang->getField("PAKET_METODE_LELANG_ID");
			$arr_json[$i]['text']	= $paket_metode_lelang->getField("PAKET_METODE_LELANG");
			$i++;
		}

		echo json_encode($arr_json);
	}

	public function cektanggalmerah()
	{
		$this->load->model('Tanggalmerah');
		$tanggalmerah = new Tanggalmerah();

		$a = $this->input->get("id");
		$tanggalmerah->selectByParams(array('TM_DATE'=>$a));

		if ($tanggalmerah->countRow() > 0) {
			$message 	= '1';
			while($tanggalmerah->nextRow())
			{
				$data 		= $tanggalmerah->getField("TM_NOTE");
			}
		} else {
			$message 	= '0';
			$data 		= '';
		}

		$arrFinal = array("message" => $message, "data" => $data);
		echo json_encode($arrFinal);
	}

	public function cekjadwalexisting()
	{
		$this->load->model('Metode');
		$tanggalmerah = new Metode();

		$a = $this->input->get("tgl");
		$b = $this->input->get("reqId");
		$tanggalmerah->selectByParamsJadwalClash2($a,$b,$this->USER_LOGIN_ID);
		$today = date('d-m-Y H:i');

		if ($a > $today) { // cek jadwal jika diatas tanggal hari ini
// echo $tanggalmerah->countRow(); die;
			if ($tanggalmerah->countRow() > 0) {
				$message 	= '1';
				$data = $tanggalmerah->countRow().' Pengadaan <br><br>';
				$jadwal = '';
				while($tanggalmerah->nextRow())
				{
					$data 		.= $tanggalmerah->getField("NAMA_PAKET");
					$data 	.= '('.$tanggalmerah->getField("NAMA").')<br><br>';
				}
			} else {
				$message 	= '0';
				$data 		= '';
			}
		} else {
			$message 	= '0';
			$data 		= '';
		}

		$arrFinal = array("message" => $message, "data" => $data, "jadwal" => $jadwal);
		echo json_encode($arrFinal);
	}

	public function comparetanggal()
	{
		$compareTglMulai = strtotime($this->input->post("tglMulai"));
		$compareTglSelesai = strtotime($this->input->post("tglSelesai"));

		if($compareTglSelesai < $compareTglMulai)
		{
			$message = 'Tanggal Selesai tidak bisa di bawah tanggal Mulai';
			$data 	 = '1';
		} else {
			$message = '';
			$data 	 = '0';
		}

		$arrFinal = array("message" => $message, "data" => $data);
		echo json_encode($arrFinal);
	}

}
?>
