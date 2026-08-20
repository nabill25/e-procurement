<?php
/**
 * @package 	eProcurement Application
 * @author 		eproc2025
 * @since 		25. Version 3.1
 * 
 */
 
include_once("functions/string.func.php");
include_once("functions/date.func.php");
include_once("functions/default.func.php");
require_once(APPPATH.'libraries/Rest.php');

// error_reporting(0);
/*
  https://medium.com/@cecepahmadfauzi93/rest-api-bd3547ad7ea
  https://medium.com/@erthru/membuat-restful-api-menggunakan-framework-codeigniter-3-part-2-163cadb32e3a
  https://github.com/atishamte/rest-server-codeigniter-2.2.x

  ------------- cURL --------------
  https://github.com/philsturgeon/codeigniter-curl
*/

class Api extends Rest {

	function __construct() {
		parent::__construct(); $this->db->query("SET TIME ZONE 'Asia/Jakarta'");
		$this->db->query("alter session set nls_date_format='YYYY-MM-DD'");
		$this->db->query("alter session set nls_numeric_characters='.,'");
		$this->HTTP_USER_AGENT = $_SERVER['HTTP_USER_AGENT'];
		$this->REMOTE_ADDR = $_SERVER['REMOTE_ADDR'];
		$this->REMOTE_HOST = $_SERVER['REMOTE_HOST'];
		$this->REMOTE_PORT = $_SERVER['REMOTE_PORT'];
		$this->HTTP_ACCEPT = $_SERVER['HTTP_ACCEPT'];
		$this->REQUEST_URI = $_SERVER['REQUEST_URI'];
		$this->HTTP_HOST = $_SERVER['HTTP_HOST'];
		$this->SERVER_SOFTWARE = $_SERVER['SERVER_SOFTWARE'];
		$this->REQUEST_METHOD = $_SERVER['REQUEST_METHOD'];
		$this->HTTP_TRY = $_SERVER['HTTP_TRY'];
		$this->title_api = 'eprocurement PAM JAYA';
	}

	public function get_index()
	{
		$arrayName = array('Status' => MESSAGE_NOUSER , );
		$this->response($arrayName, 201);
	}

	// --------------------------------- RENCANA UMUM PENGADAAN ---------------------------------
	public function get_rup() { echo "Use POST Method"; } // method POST
	public function post_rup()
	{

		parse_str(file_get_contents('php://input'), $_POST);
		$test = $_POST['test'] ?: null;
		$key = $_POST['key'] ?: null;
		$department = $_POST['department'] ?: null;

		$this->load->model("PermohonanPaket");

		$permohonan_paket = new PermohonanPaket();

		if ($department) {
			$permohonan_paket->selectByParamsUsulan(array("A.DEPARTMENT" => $department),-1,-1," AND A.KODE_RUP != '' AND APPROVAL = '1'");
		} else {
			$permohonan_paket->selectByParamsUsulan(array(),-1,-1," AND A.KODE_RUP != '' AND APPROVAL = '1'");
		}
		// $dataRUP[] = array();
		if ($permohonan_paket->countRow() > 0) {
			while ($permohonan_paket->nextRow()) {
				$dataRUP[] = array(
									'ID' => $permohonan_paket->getField("PERMOHONAN_PAKET_ID"),
									'Kode RUP' => $permohonan_paket->getField("KODE_RUP"),
									'Tahun Anggaran' => $permohonan_paket->getField("TAHUN_ANGGARAN"),
									'Nama Paket' => $permohonan_paket->getField("NAMA"),
									// 'Sumber dana' => $permohonan_paket->getField("AK_NAMA"),
									'Produk dalam negeri?' => $permohonan_paket->getField("KATEGORI_STR"),
									'Harga Perkiraan' => $permohonan_paket->getField("PERKIRAAN_BIAYA_HARGA"),
									'Pembuat' => $permohonan_paket->getField("PEMBUAT"),
									'Jabatan Pembuat' => $permohonan_paket->getField("USER_JABATAN"),
									'Department' => $permohonan_paket->getField("DEPARTMENT"),
									'Tanggal Buat' => $permohonan_paket->getField("TANGGAL_BUAT"),
								);
			}
			$countData = $permohonan_paket->countRow();
		} else {
			$dataRUP[] = array();
			$countData = 0;
		}

		$cekKey = $this->_cekKey($key, $message);

		$this->_inputRequestApi($key, $this->subArraysToString($dataRUP), 'get_rup'); // insert to table KEY_REQUEST

		if ($cekKey == 0): // false
			$this->response(
				[
					$this->title_api => [
						'key'			=> $key,
						'urlApi'		=> $this->REQUEST_URI,
						'reqDate'		=> date('Y-m-d H:i:s'),
						'totalRecord' 	=> 0,
						'status' 		=> 'failed',
						'message' 		=> $message,
						'data' 			=> array(),
						]
					],404);
		else:
			$this->_cekData($countData,$message);

			$this->response(
				[
					$this->title_api => [
						'key'			=> $key,
						'urlApi'		=> $this->REQUEST_URI,
						'reqDate'		=> date('Y-m-d H:i:s'),
						'totalRecord' 	=> $countData,
						'status' 		=> 'success',
						'message' 		=> $message,
						'data' 			=> $dataRUP,
						]
				],404);
		endif;
	}
	// --------------------------------- END RENCANA UMUM PENGADAAN ---------------------------------

	// --------------------------------- UPDATE NOMOR PR DI RENCANA UMUM PENGADAAN ---------------------------------
	public function get_updatepr() { echo "Use PUT Method"; } // method POST
	public function post_updatepr() { echo "Use PUT Method"; } // method POST
	public function put_updatepr()
	{
		// $key = $this->input->get('key') ?: null;

		// $method = $_SERVER['REQUEST_METHOD'];
    parse_str(file_get_contents('php://input'), $_PUT);
		$key 				= $_PUT['key'];
		$kode_rup   = $_PUT['kode_rup'];
		$kode_pr    = $_PUT['kode_pr'];

		// $key = $this->input->get('key') ?: null;
		// $kode_rup = $this->input->get('kode_rup') ?: null;
		// $kode_pr = $this->input->get('kode_pr') ?: null;


		$cekKey = $this->_cekKey($key, $message);

		if ($cekKey == 1) {
			$this->load->model("PermohonanPaket");

			$permohonan_paket = new PermohonanPaket();
			$permohonan_paket2 = new PermohonanPaket();

			$permohonan_paket->selectByParamsUsulan(array("KODE_RUP" => $kode_rup),-1,-1," AND APPROVAL = '1'");
			$permohonan_paket->firstRow();
			// $dataRUP[] = array();
			if ($permohonan_paket->countRow() > 0) {
				$permohonan_paket = new PermohonanPaket();
				$permohonan_paket->setField('KODE_RUP', $kode_rup);
				$permohonan_paket->setField('KODE_PR', $kode_pr);
				$permohonan_paket->setField('POSTING', '1');
				$permohonan_paket->setField('POSTING_BY', $permohonan_paket->getField("PEMBUAT"));
				if($permohonan_paket->updatePR()) {
				$permohonan_paket2->selectByParamsUsulan(array("KODE_RUP" => $kode_rup),-1,-1," AND APPROVAL = '1'");
				$permohonan_paket2->firstRow();

				// Insert Rekam Jejak
				$this->load->library("librekamjejak");
				$this->librekamjejak->insertRJ('5','','null',$permohonan_paket2->getField("PERMOHONAN_PAKET_ID"),'5');
				// End Insert Rekam Jejak

					$dataRUP[] = array(
												'Kode RUP' => $kode_rup,
												'Kode PR' => $kode_pr,
												'Nama Paket' => $permohonan_paket2->getField("NAMA"),
											);
					// echo "string1";
					$updatePR = 10;
					$countData = $permohonan_paket2->countRow();
					$message = str_replace("Key is Found","",$message);
					$message .= "Kode PR updated successfully";
				} else {
					// echo "string11";
					$dataRUP[] = array();
					$updatePR = 10;
					$countData = $permohonan_paket->countRow();
					$message = str_replace("Key is Found","",$message);
					$message .= "Kode PR failed to update";
				}
			} else {
				$dataRUP[] = array();
				$countData = 0;
				$updatePR = 0;
				// echo "string2";
				$message = str_replace("Key is Found","",$message);
				$message = "There are incorrect parameters";
			}
		} else {
				// echo "string3";
			$dataRUP[] = array();
			$countData = 0;
		}
		// echo $updatePR.'---';

		$this->_inputRequestApi($key, $this->subArraysToString($dataRUP), 'put_updatepr'); // insert to table KEY_REQUEST
		if ($cekKey == 0 || $updatePR == 0): // false
			$this->response(
				[
					$this->title_api => [
						'key'			=> $key,
						'urlApi'		=> $this->REQUEST_URI,
						'reqDate'		=> date('Y-m-d H:i:s'),
						'totalRecord' 	=> 0,
						'status' 		=> 'failed',
						'message' 		=> $message,
						'data' 			=> array(),
						]
					],404);
		else:
			// $this->_cekData($countData,$message);

			$this->response(
				[
					$this->title_api => [
						'key'			=> $key,
						'urlApi'		=> $this->REQUEST_URI,
						'reqDate'		=> date('Y-m-d H:i:s'),
						'totalRecord' 	=> $countData,
						'status' 		=> 'success',
						'message' 		=> $message,
						'data' 			=> $dataRUP,
						]
				],404);
		endif;
	}
	// --------------------------------- END UPDATE NOMOR PR DI RENCANA UMUM PENGADAAN ---------------------------------

	// --------------------------------- PAKET ALL ---------------------------------
	public function post_paket() { echo "Use GET Method"; } // method POST
	public function get_paket()
	{
		$this->load->model("Paket");
		$paket = new Paket();
		$paket->selectByParamsMonitoring(array("PUBLISH_PAKET" => '1'));
		$dataPaket[] = array();
		if ($paket->countRow() > 0) {
			while ($paket->nextRow()) {
				if ($paket->getField("PAKET_ID") > 0 && $paket->getField("PUBLISH_PAKET") == '1') {
				$dataPaket[] = array(
									'ID' => $paket->getField("PAKET_ID"),
									'Nama Paket' => $paket->getField("NAMA"),
								);
				}
			}
			$countData = $paket->countRow();
		} else {
			$dataPaket[] = array();
			$countData = 0;
		}

		$key = $this->input->get('key') ?: null;

		$cekKey = $this->_cekKey($key, $message);

		$this->_inputRequestApi($key, $this->subArraysToString($dataPaket), 'get_paket'); // insert to table KEY_REQUEST
		if ($cekKey == 0): // false
			$this->response(
				[
					$this->title_api => [
						'key'			=> $key,
						'urlApi'		=> $this->REQUEST_URI,
						'reqDate'		=> date('Y-m-d H:i:s'),
						'totalRecord' 	=> 0,
						'status' 		=> 'failed',
						'message' 		=> $message,
						'data' 			=> array(),
						]
					],404);
		else:
			$this->_cekData($countData,$message);

			$this->response(
				[
					$this->title_api => [
						'key'			=> $key,
						'urlApi'		=> $this->REQUEST_URI,
						'reqDate'		=> date('Y-m-d H:i:s'),
						'totalRecord' 	=> $countData,
						'status' 		=> 'success',
						'message' 		=> $message,
						'data' 			=> $dataPaket,
						]
				],404);
		endif;
	}
	// --------------------------------- END PAKET ALL ---------------------------------

	// --------------------------------- PAKET DETAIL ---------------------------------
	public function post_paketdetail() { echo "Use GET Method"; } // method POST
	public function get_paketdetail($id=null)
	{
		$this->load->model("Paket");
		$paket = new Paket();
  	$paket->selectByParamsMonitoring(array('A.PAKET_ID' => $id));
		$paket->firstRow();
		$dataPaket[] = array();
		if ($paket->countRow() > 0) {
			if ($paket->getField("BIDDING") == 1) {
      	$sistem_negosiasi = 'e-Reverse Auction '.$paket->getField("BIDDING_MENIT").' menit';
      } else {
          $sistem_negosiasi = "Negosiasi";
      }

        if(trim($paket->getField("BIDANG_USAHA")) == "()")
          $bidangUsaha = "-";
        else
          $bidangUsaha =  str_replace(","," <br/> ", $paket->getField("BIDANG_USAHA"));

				$dataPaket[] = array(
									'ID' => $paket->getField("PAKET_ID"),
									'Nama Paket' => $paket->getField("NAMA"),
									'Tahun' => getYear($paket->getField("TANGGAL_TAHAP")),
									'Lokasi Pekerjaan' => $paket->getField("LOKASI"),
									'Jenis Pengadaan' => $paket->getField("PAKET_JENIS"),
									'Metode Pengadaan' => $paket->getField("METODE_LELANG"),
									'Metode Penyempaian Pengadaan' => $paket->getField("SISTEM_SAMPUL").' File',
									'Metode Evaluasi' => $paket->getField("METODE_EVALUASI"),
									'Kualifikasi Usaha' => $paket->getField("REKANAN_KUALIFIKASI"),
									'Sistem Negosiasi' => $sistem_negosiasi,
									'Perkiraan Nilai Pekerjaan' => $paket->getField("NILAI_MATA_UANG").' '.currencyToPage($paket->getField("NILAI")),
									'Bidang / Sub Bidang' => $bidangUsaha,
									'Keterangan' => strip_tags(str_replace('\n', '<br>', $paket->getField("URAIAN"))),
								);
		} else {
			$dataPaket[] = array();
		}

		$key = $_GET["key"];
		$countData = $paket->countRow();

		$cekKey = $this->_cekKey($key, $message);

		$this->_inputRequestApi($key, $this->subArraysToString($dataPaket), 'get_paketdetail'); // insert to table KEY_REQUESTs
		if ($cekKey == 0 || $id == ''): // false cause key not found, data not found
			if ($id == '') { $message = 'ID not set';}
			$this->response([
				$this->title_api => [
					'key'			=> $key,
					'urlApi'		=> $this->REQUEST_URI,
					'reqDate'		=> date('Y-m-d H:i:s'),
					'totalRecord' 	=> 0,
					'status' 		=> 'failed',
					'message' 		=> $message,
					'data' 			=> array(),
				]
			],404);
		else:
			$this->_cekData($countData,$message);
			$this->response([
				$this->title_api => [
					'key'			=> $key,
					'urlApi'		=> $this->REQUEST_URI,
					'reqDate'		=> date('Y-m-d H:i:s'),
					'totalRecord' 	=> $countData,
					'status' 		=> 'success',
					'message' 		=> $message,
					'data' 			=> $dataPaket,
				]
			],200);
		endif;

	}
	// --------------------------------- END PAKET DETAIL ---------------------------------

	// --------------------------------- PAKET TENDER ---------------------------------
	public function post_pakettender() { echo "Use GET Method"; } // method POST
	public function get_pakettender()
	{
		$this->load->model("Paket");
		$paket = new Paket();
		$paket->selectByParamsMonitoring(array("JENIS_PENGADAAN" => "LELANG", "A.PAKET_METODE_LELANG_ID|| IN " => "(1,3,7)", "PUBLISH_PAKET" => "1"));
		$dataPaket[] = array();

		if ($paket->countRow() > 0) {
			while ($paket->nextRow()) {
				if ($paket->getField("PAKET_ID") > 0) {
				$dataPaket[] = array(
									'ID' => $paket->getField("PAKET_ID"),
									'Nama Paket' => $paket->getField("NAMA"),
								);
				}
			}
		} else {
			$dataPaket[] = array();
		}

		$key = $_GET["key"];
		$countData = $paket->countRow();

		$cekKey = $this->_cekKey($key, $message);

		$this->_inputRequestApi($key, $this->subArraysToString($dataPaket), 'get_pakettender'); // insert to table KEY_REQUESTs
		if ($cekKey == 0): // false
			$this->response([
				$this->title_api => [
					'key'			=> $key,
					'urlApi'		=> $this->REQUEST_URI,
					'reqDate'		=> date('Y-m-d H:i:s'),
					'totalRecord' 	=> 0,
					'status' 		=> 'failed',
					'message' 		=> $message,
					'data' 			=> array(),
				]
			],404);
		else:
			$this->_cekData($countData,$message);
			$this->response([
				$this->title_api => [
					'key'			=> $key,
					'urlApi'		=> $this->REQUEST_URI,
					'reqDate'		=> date('Y-m-d H:i:s'),
					'totalRecord' 	=> $countData,
					'status' 		=> 'success',
					'message' 		=> $message,
					'data' 			=> $dataPaket,
				]
			],200);
		endif;

	}
	// --------------------------------- END PAKET TENDER ---------------------------------

	// --------------------------------- PAKET NON TENDER ---------------------------------
	public function post_paketnontender() { echo "Use GET Method"; } // method POST
	public function get_paketnontender()
	{
		$this->load->model("Paket");
		$paket = new Paket();
		$paket->selectByParamsMonitoring(array("JENIS_PENGADAAN" => "LELANG", "A.PAKET_METODE_LELANG_ID|| IN " => "(2,4,5,8)", "PUBLISH_PAKET" => "1"));
		$dataPaket[] = array();

		if ($paket->countRow() > 0) {
			while ($paket->nextRow()) {
				if ($paket->getField("PAKET_ID") > 0) {
				$dataPaket[] = array(
									'ID' => $paket->getField("PAKET_ID"),
									'Nama Paket' => $paket->getField("NAMA"),
								);
				}
			}
		} else {
			$dataPaket[] = array();
		}

		$key = $_GET["key"];
		$countData = $paket->countRow();

		$cekKey = $this->_cekKey($key, $message);

		$this->_inputRequestApi($key, $this->subArraysToString($dataPaket), 'get_paketnontender'); // insert to table KEY_REQUESTs
		if ($cekKey == 0): // false
			$this->response([
				$this->title_api => [
					'key'			=> $key,
					'urlApi'		=> $this->REQUEST_URI,
					'reqDate'		=> date('Y-m-d H:i:s'),
					'totalRecord' 	=> 0,
					'status' 		=> 'failed',
					'message' 		=> $message,
					'data' 			=> array(),
				]
			],404);
		else:
			$this->_cekData($countData,$message);
			$this->response([
				$this->title_api => [
					'key'			=> $key,
					'urlApi'		=> $this->REQUEST_URI,
					'reqDate'		=> date('Y-m-d H:i:s'),
					'totalRecord' 	=> $countData,
					'status' 		=> 'success',
					'message' 		=> $message,
					'data' 			=> $dataPaket,
				]
			],200);
		endif;

	}
	// --------------------------------- END PAKET NON TENDER ---------------------------------

	// --------------------------------- PAKET JADWAL ---------------------------------
	public function post_paketjadwal() { echo "Use GET Method"; } // method POST
	public function get_paketjadwal($id=null)
	{
		$this->load->model("PaketTahap");
		$paket_tahap_jadwal = new PaketTahap();
		$dataPaket[] = array();

		$paket_tahap_jadwal->selectByParamsJadwal(array("TAMPILKAN" => "1"), -1, -1, " AND PAKET_ID = '".$id."' ");

		if ($paket_tahap_jadwal->countRow() > 0) {
			while ($paket_tahap_jadwal->nextRow()) {
				if ($paket_tahap_jadwal->getField("PAKET_ID") > 0) {
				$dataPaket[] = array(
									'Nama Jadwal' => $paket_tahap_jadwal->getField("NAMA"),
									'Tanggal Awal' => getFormattedDate($paket_tahap_jadwal->getField("TANGGAL_AWAL")).' '.addWIB($paket_tahap_jadwal->getField("JAM_AWAL")),
									'Tanggal Akhir' => getFormattedDate($paket_tahap_jadwal->getField("TANGGAL_AKHIR")).' '.addWIB($paket_tahap_jadwal->getField("JAM_AKHIR")),
								);
				}
			}
		} else {
			$dataPaket[] = array();
		}

		$key = $_GET["key"];
		$countData = $paket_tahap_jadwal->countRow();

		$cekKey = $this->_cekKey($key, $message);

		$this->_inputRequestApi($key, $this->subArraysToString($dataPaket), 'get_paketjadwal'); // insert to table KEY_REQUESTs
		if ($cekKey == 0 || $id == ''): // false cause key not found, data not found
			if ($id == '') { $message = 'ID not set';}
			$this->response([
				$this->title_api => [
					'key'			=> $key,
					'urlApi'		=> $this->REQUEST_URI,
					'reqDate'		=> date('Y-m-d H:i:s'),
					'totalRecord' 	=> 0,
					'status' 		=> 'failed',
					'message' 		=> $message,
					'data' 			=> array(),
				]
			],404);
		else:
			$this->_cekData($countData,$message);
			$this->response([
				$this->title_api => [
					'key'			=> $key,
					'urlApi'		=> $this->REQUEST_URI,
					'reqDate'		=> date('Y-m-d H:i:s'),
					'totalRecord' 	=> $countData,
					'status' 		=> 'success',
					'message' 		=> $message,
					'data' 			=> $dataPaket,
				]
			],200);
		endif;

	}
	// --------------------------------- PAKET JADWAL ---------------------------------

	// --------------------------------- PAKET PESERTA  ---------------------------------
	public function post_paketpeserta() { echo "Use GET Method"; } // method POST
	public function get_paketpeserta($id=null)
	{
		$this->load->model("PaketRekanan");
		$paket_rekanan = new PaketRekanan();
		$paket_rekanan->selectByParams(array("PAKET_ID" => $id));
		$dataPaket[] = array();

		$this->load->model("Paket");
		$paket = new Paket();
		$paket->selectByParamsMonitoring(array("PAKET_ID" => $id));

		if ($paket_rekanan->countRow() > 0) {
			$paket->firstRow();
			$paket_metode_lelang = $paket->getField("PAKET_METODE_LELANG_ID");

			$i=1;
			while ($paket_rekanan->nextRow()) {
				if ($paket_metode_lelang == 1) { // Tender
					$dataPaket[] = array(
						'Peserta' => ''.$i.'',
						'Tanggal Daftar' => getFormattedDate($paket_rekanan->getField("TANGGAL_DAFTAR")).' '.$paket_rekanan->getField("JAM_DAFTAR"),
						// 'Metode' => $paket_metode_lelang,
					);
				} else {
					$dataPaket[] = array(
						'Peserta' => ''.$i.'',
						'Tanggal Undang' => getFormattedDate($paket_rekanan->getField("TANGGAL_UNDANG")).' '.$paket_rekanan->getField("JAM_UNDANG"),
						// 'Metode' => $paket_metode_lelang,
					);
				}
			 $i++;
			}
		} else {
			$dataPaket[] = array();
		}

		$key = $_GET["key"];
		$countData = $paket_rekanan->countRow();

		$cekKey = $this->_cekKey($key, $message);

		$this->_inputRequestApi($key, $this->subArraysToString($dataPaket), 'get_paketpesertal'); // insert to table KEY_REQUESTs
		if ($cekKey == 0 || $id == ''): // false cause key not found, data not found
			if ($id == '') { $message = 'ID not set';}
			$this->response([
				$this->title_api => [
					'key'			=> $key,
					'urlApi'		=> $this->REQUEST_URI,
					'reqDate'		=> date('Y-m-d H:i:s'),
					'totalRecord' 	=> 0,
					'status' 		=> 'failed',
					'message' 		=> $message,
					'data' 			=> array(),
				]
			],404);
		else:
			$this->_cekData($countData,$message);
			$this->response([
				$this->title_api => [
					'key'			=> $key,
					'urlApi'		=> $this->REQUEST_URI,
					'reqDate'		=> date('Y-m-d H:i:s'),
					'totalRecord' 	=> $countData,
					'status' 		=> 'success',
					'message' 		=> $message,
					'data' 			=> $dataPaket,
				]
			],200);
		endif;

	}
	// --------------------------------- END PAKET PESERTA ---------------------------------

	// --------------------------------- PAKET AANWIJZING  ---------------------------------
	public function post_paketaanwijzing() { echo "Use GET Method"; } // method POST
	public function get_paketaanwijzing($id=null)
	{
		$this->load->model("Rekanan");
		$this->load->model("PaketAanwijzing");
		$paket_aanwijzing_rekanan = new PaketAanwijzing();
		$rekananNama = new Rekanan();
		$paket_aanwijzing_rekanan->selectByParamsPeserta(array("A.PAKET_ID" => $id, 'A.PARENT_ID' => 0),'GROUP BY A.REKANAN_KODE');
		$dataPaket[] = array();


		$i=1;
		if ($paket_aanwijzing_rekanan->countRow() > 0) {
			while ($paket_aanwijzing_rekanan->nextRow()) {
				$rekananNama->selectByParams(array('A.KODE' => $paket_aanwijzing_rekanan->getField("REKANAN_KODE")));
				$rekananNama->firstRow();
				$dataPaket[] = array(
									'Kode Aanwijzing' => $paket_aanwijzing_rekanan->getField("REKANAN_KODE"),
									'Nama Penyedia' => $rekananNama->getField("NAMA"),
								);
			 $i++;
			}
		} else {
			$dataPaket[] = array( );
		}
		$key = $_GET["key"];
		$countData = $paket_aanwijzing_rekanan->countRow();

		$cekKey = $this->_cekKey($key, $message);

		$this->_inputRequestApi($key, $this->subArraysToString($dataPaket), 'get_paketaanwijzing'); // insert to table KEY_REQUESTs
		if ($cekKey == 0 || $id == '' && $paket_aanwijzing_rekanan->countRow() == 0): // false cause key not found, data not found
			if ($id == '') { $message = 'ID not set';}
			$this->response([
				$this->title_api => [
					'key'			=> $key,
					'urlApi'		=> $this->REQUEST_URI,
					'reqDate'		=> date('Y-m-d H:i:s'),
					'totalRecord' 	=> 0,
					'status' 		=> 'failed',
					'message' 		=> $message,
					'data' 			=> array(),
				]
			],404);
		else:
			$this->_cekData($countData,$message);
			$this->response([
				$this->title_api => [
					'key'			=> $key,
					'urlApi'		=> $this->REQUEST_URI,
					'reqDate'		=> date('Y-m-d H:i:s'),
					'totalRecord' 	=> $countData,
					'status' 		=> 'success',
					'message' 		=> $message,
					'data' 			=> $dataPaket,
				]
			],200);
		endif;

	}
	// --------------------------------- END PAKET AANWIJZING ---------------------------------

	// --------------------------------- PAKET PEMENANG  ---------------------------------
	public function post_paketpemenang() { echo "Use GET Method"; } // method POST
	public function get_paketpemenang($id=null)
	{
		$this->load->model("Paketpemenang");
		$getpaket_pemenang = new Paketpemenang();
		$getpaket_pemenang->selectByParams(array("PAKET_ID" => $id), -1, -1);
		$dataPaket[] = array();

		if ($getpaket_pemenang->countRow() > 0) {
			$i=1;
			while ($getpaket_pemenang->nextRow()) {
				$dataPaket[] = array(
									'Urutan' => $getpaket_pemenang->getField("PERINGKAT"),
									'Nama Pemenang' => $getpaket_pemenang->getField("NAMA"),
								);
			 $i++;
			}
		} else {
				$dataPaket[] = array();
		}

		$key = $_GET["key"];
		$countData = $getpaket_pemenang->countRow();

		$cekKey = $this->_cekKey($key, $message);

		$this->_inputRequestApi($key, $this->subArraysToString($dataPaket), 'get_paketpemenang'); // insert to table KEY_REQUESTs
		if ($cekKey == 0 || $id == ''): // false cause key not found, data not found
			if ($id == '') { $message = 'ID not set';}
			$this->response([
				$this->title_api => [
					'key'			=> $key,
					'urlApi'		=> $this->REQUEST_URI,
					'reqDate'		=> date('Y-m-d H:i:s'),
					'totalRecord' 	=> 0,
					'status' 		=> 'failed',
					'message' 		=> $message,
					'data' 			=> array(),
				]
			],404);
		else:
			$this->_cekData($countData,$message);
			$this->response([
				$this->title_api => [
					'key'			=> $key,
					'urlApi'		=> $this->REQUEST_URI,
					'reqDate'		=> date('Y-m-d H:i:s'),
					'totalRecord' 	=> $countData,
					'status' 		=> 'success',
					'message' 		=> $message,
					'data' 			=> $dataPaket,
				]
			],200);
		endif;

	}
	// --------------------------------- END PAKET PEMENANG ---------------------------------

	// --------------------------------- PAKET LAPORAN JUMLAH  ---------------------------------
	public function post_paketlaporanjumlah() { echo "Use GET Method"; } // method POST
	public function get_paketlaporanjumlah($getTahun=null)
	{
		$this->load->model("Paket");
		$dataPaket[] = array();

		// Laporan Berdasarkan Metode Pengadaan

		$countTender = new Paket();
		$sumTender = new Paket();
		$countTender = $countTender->getCountByParams(array("A.PAKET_METODE_LELANG_ID" => "1", "TAHUN_PERMOHONAN" => $getTahun));
		$sumTender = $sumTender->getSumByParams(array("A.PAKET_METODE_LELANG_ID" => "1", "TAHUN_PERMOHONAN" => $getTahun));

		$countKompetisi = new Paket();
		$sumKompetisi = new Paket();
		$countKompetisi = $countKompetisi->getCountByParams(array("A.PAKET_METODE_LELANG_ID" => "8", "TAHUN_PERMOHONAN" => $getTahun));
		$sumKompetisi = $sumKompetisi->getSumByParams(array("A.PAKET_METODE_LELANG_ID" => "8", "TAHUN_PERMOHONAN" => $getTahun));

		$countTenderTerbatas = new Paket();
		$sumTenderTerbatas = new Paket();
		$countTenderTerbatas = $countTenderTerbatas->getCountByParams(array("A.PAKET_METODE_LELANG_ID" => "3", "TAHUN_PERMOHONAN" => $getTahun));
		$sumTenderTerbatas = $sumTenderTerbatas->getSumByParams(array("A.PAKET_METODE_LELANG_ID" => "3", "TAHUN_PERMOHONAN" => $getTahun));

		$countTenderCepat = new Paket();
		$sumTenderCepat = new Paket();
		$countTenderCepat = $countTenderCepat->getCountByParams(array("A.PAKET_METODE_LELANG_ID" => "7", "TAHUN_PERMOHONAN" => $getTahun));
		$sumTenderCepat = $sumTenderCepat->getSumByParams(array("A.PAKET_METODE_LELANG_ID" => "7", "TAHUN_PERMOHONAN" => $getTahun));

		$countPengadaanLangsung = new Paket();
		$sumPengadaanLangsung = new Paket();
		$countPengadaanLangsung = $countPengadaanLangsung->getCountByParams(array("A.PAKET_METODE_LELANG_ID" => "2", "TAHUN_PERMOHONAN" => $getTahun));
		$sumPengadaanLangsung = $sumPengadaanLangsung->getSumByParams(array("A.PAKET_METODE_LELANG_ID" => "2", "TAHUN_PERMOHONAN" => $getTahun));

		$countPenunjukanLangsung = new Paket();
		$sumPenunjukanLangsung = new Paket();
		$countPenunjukanLangsung = $countPenunjukanLangsung->getCountByParams(array("A.PAKET_METODE_LELANG_ID" => "5", "TAHUN_PERMOHONAN" => $getTahun));
		$sumPenunjukanLangsung = $sumPenunjukanLangsung->getSumByParams(array("A.PAKET_METODE_LELANG_ID" => "5", "TAHUN_PERMOHONAN" => $getTahun));

		$countPembelianLangsung = new Paket();
		$sumPembelianLangsung = new Paket();
		$countPembelianLangsung = $countPembelianLangsung->getCountByParams(array("A.PAKET_METODE_LELANG_ID" => "6", "TAHUN_PERMOHONAN" => $getTahun));
		$sumPembelianLangsung = $sumPembelianLangsung->getSumByParams(array("A.PAKET_METODE_LELANG_ID" => "6", "TAHUN_PERMOHONAN" => $getTahun));

		$countPembelianOffline = new Paket();
		$sumPembelianOffline = new Paket();
		$countPembelianOffline = $countPembelianOffline->getCountByParams(array("A.PAKET_METODE_LELANG_ID" => "9", "TAHUN_PERMOHONAN" => $getTahun));
		$sumPembelianOffline = $sumPembelianOffline->getSumByParams(array("A.PAKET_METODE_LELANG_ID" => "9", "TAHUN_PERMOHONAN" => $getTahun));

		$dataPaket[] = array(
							'Tender' => array (
												  'Tahun' => $getTahun,
												  'Jumlah' => $countTender,
												  'Nilai'  => $sumTender ?: 0,
													),
							'Kompetisi' => array (
												  'Tahun' => $getTahun,
												  'Jumlah' => $countKompetisi,
												  'Nilai'  => $sumKompetisi ?: 0,
													),
							// 'e-Tender Terbatas' => array (
							// 					  'Tahun' => $getTahun,
							// 					  'Jumlah' => $countTenderTerbatas,
							// 					  'Nilai'  => $sumTenderTerbatas,
							// 						),
							// 'e-Tender Ceapt' => array (
							// 					  'Tahun' => $getTahun,
							// 					  'Jumlah' => $countTenderCepat,
							// 					  'Nilai'  => $sumTenderCepat,
							// 						),
							'Penunjukan Langsung' => array (
												  'Tahun' => $getTahun,
												  'Jumlah' => $countPenunjukanLangsung,
												  'Nilai'  => $sumPenunjukanLangsung ?: 0,
													),
							'Pengadaan Langsung' => array (
												  'Tahun' => $getTahun,
												  'Jumlah' => $countPengadaanLangsung,
												  'Nilai'  => $sumPengadaanLangsung ?: 0,
													),
							'Pembelian Langsung' => array (
												  'Tahun' => $getTahun,
												  'Jumlah' => $countPembelianLangsung,
												  'Nilai'  => $sumPembelianLangsung ?: 0,
													),
							'Pembelian Offline' => array (
												  'Tahun' => $getTahun,
												  'Jumlah' => $countPembelianOffline,
												  'Nilai'  => $sumPembelianOffline ?: 0,
													),
						);

		$key = $_GET["key"];
		$countData = count($dataPaket);

		$cekKey = $this->_cekKey($key, $message);

		$this->_inputRequestApi($key, $this->subArraysToString($dataPaket), 'get_paketlaporanjumlah'); // insert to table KEY_REQUESTs
		if ($cekKey == 0 || $getTahun == ''): // false cause key not found, data not found
			if ($getTahun == '') { $message = 'Tahun not set';}
			$this->response([
				$this->title_api => [
					'key'			=> $key,
					'urlApi'		=> $this->REQUEST_URI,
					'reqDate'		=> date('Y-m-d H:i:s'),
					'totalRecord' 	=> 0,
					'status' 		=> 'failed',
					'message' 		=> $message,
					'data' 			=> array(),
				]
			],404);
		else:
			$this->_cekData($countData,$message);
			$this->response([
				$this->title_api => [
					'key'			=> $key,
					'urlApi'		=> $this->REQUEST_URI,
					'reqDate'		=> date('Y-m-d H:i:s'),
					'totalRecord' 	=> $countData,
					'status' 		=> 'success',
					'message' 		=> $message,
					'data' 			=> $dataPaket,
				]
			],200);
		endif;

	}
	// --------------------------------- END PAKET LAPORAN JUMLAH ---------------------------------

	// --------------------------------- PAKET LAPORAN TOTAL JENIS PENGADAAN  ---------------------------------
	public function post_paketlaporantotaljenis() { echo "Use GET Method"; } // method POST
	public function get_paketlaporantotaljenis($getTahun=null)
	{
		$this->load->model("Paket");
		$dataPaket[] = array();

		$getDataChartPie = new Paket();
		$getDataChartPie->getDashboardPie('all',$getTahun);

		if ($getDataChartPie->countRow() > 0) {
			while($getDataChartPie->nextRow())
			{
			    // $pieLabel[] = $getDataChartPie->getField('paket_jenis_id_nama');
			    // $pieValue[] = $getDataChartPie->getField('total');
				$dataPaket[] = array(
								'Tahun' => $getTahun,
								'Jenis Pengadaan' => $getDataChartPie->getField('paket_jenis_id_nama'),
								'Total' => $getDataChartPie->getField('total'),
								);
			}
		} else {
			$dataPaket[] = array();
		}

		$key = $_GET["key"];
		$countData = $getDataChartPie->countRow();

		$cekKey = $this->_cekKey($key, $message);

		$this->_inputRequestApi($key, $this->subArraysToString($dataPaket), 'get_paketlaporantotaljenis'); // insert to table KEY_REQUESTs
		if ($cekKey == 0 || $getTahun == ''): // false cause key not found, data not found
			if ($getTahun == '') { $message = 'Tahun not set';}
			$this->response([
				$this->title_api => [
					'key'			=> $key,
					'urlApi'		=> $this->REQUEST_URI,
					'reqDate'		=> date('Y-m-d H:i:s'),
					'totalRecord' 	=> 0,
					'status' 		=> 'failed',
					'message' 		=> $message,
					'data' 			=> array(),
				]
			],404);
		else:
			$this->_cekData($countData,$message);
			$this->response([
				$this->title_api => [
					'key'			=> $key,
					'urlApi'		=> $this->REQUEST_URI,
					'reqDate'		=> date('Y-m-d H:i:s'),
					'totalRecord' 	=> $countData,
					'status' 		=> 'success',
					'message' 		=> $message,
					'data' 			=> $dataPaket,
				]
			],200);
		endif;

	}
	// --------------------------------- END PAKET LAPORAN TOTAL JENIS PENGADAAN ---------------------------------

	// --------------------------------- PAKET LAPORAN PAKET REALISASI  ---------------------------------
	public function post_paketlaporanrealisasi() { echo "Use GET Method"; } // method POST
	public function get_paketlaporanrealisasi($getTahun=null)
	{
		$this->load->model("Paket");
		$dataPaket[] = array();

		// Laporan Berdasarkan Realisasi Paket
		$getDataChartBar2 = new Paket();
	  $getDataChartBar2->getDashboardBar2('all',$getTahun);
		// echo $getDataChartBar2->query; die;
		if ($getDataChartBar2->countRow() > 0) {
			while($getDataChartBar2->nextRow())
			  {
			    // $bar2Label[] = $getDataChartBar2->getField('user_nama').' ('.$getDataChartBar2->getField('total_rencana').' Paket)';
			    // $bar2Label[] = $getDataChartBar2->getField('user_jabatan');
			    // $bar2PaketRencana[] = $getDataChartBar2->getField('total_rencana');
			    // $bar2PaketRealisasi[] = $getDataChartBar2->getField('total_realisasi');
			    // $bar2SisaPaketRencana[] = $getDataChartBar2->getField('total_rencana') - $getDataChartBar2->getField('total_realisasi');

				$dataPaket[] = array(
								'Tahun' => $getTahun,
								'Pengguna' => $getDataChartBar2->getField('user_jabatan'),
								'Total Rencana' => $getDataChartBar2->getField('total_rencana'),
								'Total Realisasi' => $getDataChartBar2->getField('total_realisasi'),
								'Sisa Realisasi' => $getDataChartBar2->getField('total_rencana') - $getDataChartBar2->getField('total_realisasi'),
								);
			}
		} else {
			$dataPaket[] = array();
		}
		// EndLaporan Berdasarkan Realisasi Paket
		// echo "<pre>"; print_r($dataPaket); die;
		$key = $_GET["key"];
		$countData = $getDataChartBar2->countRow();

		$cekKey = $this->_cekKey($key, $message);

		$this->_inputRequestApi($key, $this->subArraysToString($dataPaket), 'get_paketlaporanrealisasi'); // insert to table KEY_REQUESTs
		if ($cekKey == 0 || $getTahun == ''): // false cause key not found, data not found
			if ($getTahun == '') { $message = 'Tahun not set';}
			$this->response([
				$this->title_api => [
					'key'			=> $key,
					'urlApi'		=> $this->REQUEST_URI,
					'reqDate'		=> date('Y-m-d H:i:s'),
					'totalRecord' 	=> 0,
					'status' 		=> 'failed',
					'message' 		=> $message,
					'data' 			=> array(),
				]
			],404);
		else:
			$this->_cekData($countData,$message);
			$this->response([
				$this->title_api => [
					'key'			=> $key,
					'urlApi'		=> $this->REQUEST_URI,
					'reqDate'		=> date('Y-m-d H:i:s'),
					'totalRecord' 	=> $countData,
					'status' 		=> 'success',
					'message' 		=> $message,
					'data' 			=> $dataPaket,
				]
			],200);
		endif;

	}
	// --------------------------------- END PAKET LAPORAN PAKET REALISASI ---------------------------------


	private function _cekKey($key,&$message)
  {
        $this->load->model("Key");
        $keyModel = new Key();
        $keyModelData = new Key();

        if (!$key) {
            $message = 'No Key Found';
            $ret     = 0;
        }
        else
        {
	  		$cekKeyModel = $keyModel->getCountByParams(array('key' => $key));

            if ($cekKeyModel>0):
            	$keyModelData->selectByParams(array('key' => $key, 'active' => '1')); $keyModelData->firstRow();
	            $activeStatus = $keyModelData->getField("ACTIVE");
	            if ($activeStatus == '1') {
	                $message = 'Key is Found';
	                $ret     = 1;
	            } else {
	            	$message = 'Key is not active';
	                $ret     = 0;
	            }
            else:
                $message = 'Key is wrong';
                $ret     = 0;
            endif;
        }

        return $ret;
    }

    private function _cekData($count,&$message)
    {
        if ($count>0):
            $message = 'Data Found'; $ret     = 1;
        else:
            $message = 'Data Not Found'; $ret     = 0;
        endif;

        return $ret;
    }

    private function _inputRequestApi($key, $data, $modul)
    {
		$this->load->model('Key');
		$keyModel	= new Key();
    	$keyModel->selectByParams(array('key' => $key));
    	$keyModel->firstRow();
	  	$keyID 		= $keyModel->getField("ID");
	  	$keyClient 	= $keyModel->getField("CLIENT");

		$keyInsert	= new Key();
		$keyInsert->setField("KEY_ID", $keyID);
		$keyInsert->setField("KEY", $key);
		$keyInsert->setField("CLIENT", $keyClient);
		$keyInsert->setField("URL_API", $this->REQUEST_URI);
		$keyInsert->setField("REQ_DATE", date('Y-m-d H:i:s'));
		$keyInsert->setField("IP", $this->REMOTE_ADDR);
		$keyInsert->setField("BROWSER", $this->HTTP_USER_AGENT);
		$keyInsert->setField("DATA", $data);
		$keyInsert->setField("MODULE_API", $modul);
		$keyInsert->insert();
        // return $ret;
    }

    function html_table($data = array())
	{
	    $rows = array();
	    foreach ($data as $row) {
	        $cells = array();
	        foreach ($row as $cell) {
	            $cells[] = "<td>{$cell}</td>";
	        }
	        $rows[] = "<tr>" . implode('', $cells) . "</tr>";
	    }
	    return "<table class='hci-table'>" . implode('', $rows) . "</table>";
	}

	function subArraysToString($ar, $sep = ', ') {
	    $str = '';
	    foreach ($ar as $val) {
	        $str .= implode($sep, $val);
	        $str .= $sep; // add separator between sub-arrays
	    }
	    $str = rtrim($str, $sep); // remove last separator
	    return $str;
	}

}
