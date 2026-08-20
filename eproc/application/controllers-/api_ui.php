<?php
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

class Api_ui extends Rest {

	function __construct() {
		parent::__construct(); $this->db->query("SET TIME ZONE 'Asia/Jakarta'");
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
		$this->title_api = 'eProcurement UI';

		$this->pathFileRUP = 'uploads/api_rup/';
		$this->pathLogs = 'logs/api_rup/';


		$this->load->library('excel'); // Load library PHPExcel
	}

	/* GUIDE API UI
			1. Import RUP: http://depok:8888/api_ui/rup
			2. GET PR by RUP: http://depok:8888/api_ui/updatepr/8274->(IDSIRUP)
			3. Update HPS by Permohonan ID : http://depok:8888/api_ui/updatehps/231->(PERMOHONANID-OPSIONAL)
	*/

	public function get_index()
	{
		echo MESSAGE_NOUSER;
	}

	// --------------------------------- 4. UPDATE FILE ESIGN -------------------------------------------

	public function get_updatefileesign()
	{
    $this->load->library("libapiui");
    $this->load->model(array("Queryfree","PermohonanPaketAnalisaFile"));
    $permohonan_paket_file = new Queryfree();
    $libapiui = new libapiui();

    $permohonan_paket_file->selectByParams("SELECT permohonan_paket_analisa_file_id, esign_id, esign_nomor_surat, esign_status, esign_path_file FROM permohonan_paket_analisa_file WHERE esign_status != 'Selesai'");

		while($permohonan_paket_file->nextRow())
	  {
    	$cekEsign = $libapiui->postEsignCekStatus($permohonan_paket_file->getField("ESIGN_ID"),$fileName);

      if ($cekEsign->data->status == 'Selesai') { // Update ke DB
        $permohonan_paket_fileU = new PermohonanPaketAnalisaFile();
        $permohonan_paket_fileU->setField('PERMOHONAN_PAKET_ANALISA_FILE_ID', $permohonan_paket_file->getField("PERMOHONAN_PAKET_ANALISA_FILE_ID"));
        $permohonan_paket_fileU->setField('ESIGN_PATH_FILE', $fileName);
        $permohonan_paket_fileU->setField('ESIGN_STATUS', $cekEsign->data->status);
        $permohonan_paket_fileU->setField('UPDATED_BY', 0);
        $permohonan_paket_fileU->updateEsign400Close();
				echo "ID (".$permohonan_paket_file->getField("PERMOHONAN_PAKET_ANALISA_FILE_ID").") update status (".$cekEsign->data->status.")-> updatefileesign --- ";
      }
    }
  }

	// --------------------------------- 3. UPDATE HPS BY PERMOHONAN ID ---------------------------------
	

	public function get_updatehpsall()
	{
      $this->load->model(array("Permohonanpaket","Importsirup"));
    $permohonanpaket = new Permohonanpaket();
    $permohonanpaket->selectUpdateHPS(array());

		while($permohonanpaket->nextRow())
    {
    	$this->get_updatehps($permohonanpaket->getField("PERMOHONAN_PAKET_ID"));
    }
	}

	public function get_updatehps($permohonanpaketid=null)
	{
		if ($permohonanpaketid) {
      $this->load->model(array("Permohonanpaket","Importsirup"));
      $this->load->library("libapiui");
      $permohonanpaket = new Permohonanpaket();
      $libapiui = new libapiui();
      $permohonanpaket->selectPermohonanForUpdateHPS(array("PERMOHONAN_PAKET_ID" => $permohonanpaketid));
      $permohonanpaket->firstRow();

      $sirup = new Importsirup();
      $sirup->selectByParamsDetailRUP(array("ID" => $permohonanpaket->getField("SIRUP_ID")));
      $sirup->firstRow();

      $reqTahun = $sirup->getField("TAHUN");
      $reqKodeSA = $sirup->getField("KODE_SA");
      $reqKodePR = $sirup->getField("KODE_PR");
      $dataPR = $libapiui->getPR($reqTahun,$reqKodeSA);
			// echo "<pre>"; print_r($dataPR); die;
      $hasil = $this->findByField($dataPR, 'PR_NO', $reqKodePR);
    	$currency = $hasil['Mata Uang'];
    	$totalRAB = $hasil['Total RAB'];
      if ($hasil['Total Termasuk Pajak']) {
      	$pagupr = str_replace(".","",$hasil['Total Termasuk Pajak']);
      	$updateHPS = new Permohonanpaket();
				$updateHPS->setField('PERMOHONAN_PAKET_ID', $permohonanpaketid);
				$updateHPS->setField('NILAI_HPS_PR', $pagupr);
				$updateHPS->setField('NILAI_RAB_PR', $totalRAB);
				$updateHPS->setField('NILAI_MATA_UANG', $currency);
				// $updateHPS->updateHPS();	
				if($updateHPS->updateHPS())
				{
					echo "Kode PR (".$reqKodePR.") berhasil diupdate -> updatehpsall --- ";
				} else {
					echo "Kode PR (".$reqKodePR.") gagal diupdate -> updatehpsall --- ";
				}
      }
		} else {
			echo "Kode PR (".$reqKodePR.") gagal diupdate -> updatehpsall --- ";
		}

		// continue;
	} 

	// --------------------------------- 2. UPDATE PR BY RUP ---------------------------------
	public function get_updatepr($sirupId)
	{
		if ($sirupId) {
      $this->load->model("Importsirup");
      $this->load->library("libapiui");
      $sirup = new Importsirup();
      $libapiui = new libapiui();
      $sirup->selectByParamsDetailRUP(array("ID" => $sirupId));
      $sirup->firstRow();

      $reqTahun = $sirup->getField("TAHUN");
      $reqKodeSA = $sirup->getField("KODE_SA");
      $reqKodePR = $sirup->getField("KODE_PR");
      $reqKodeRUP = $sirup->getField("KODE_RUP");
      $dataPR = $libapiui->getPRByRUP($reqTahun,$reqKodeRUP);
      // echo "<pre>"; print_r($dataPR); die;
      $hasil = $this->parsingAPI($dataPR);
      if ($hasil['PR_NO'] != '' && $hasil['TOTAL_WITH_TAX'] != '') {
      	$pagupr = str_replace(".","",$hasil['TOTAL_WITH_TAX']);
      	$updateKodePR = new Importsirup();
				$updateKodePR->setField('ID', $sirupId);
				$updateKodePR->setField('NILAI_PAGU_PR', $pagupr);
				$updateKodePR->setField('KODE_PR', $hasil['PR_NO']);
				if($updateKodePR->updatepr())
				{
					echo "Data berhasil diproses";
				} else {
					echo "Data gagal diproses";
				}

      	
      } else {
      	echo "Kode PR belum tersedia";
      }
      // echo $hasil['Total Termasuk Pajak']; die;
		}
		redirect('/main/index/permohonan_paket_usulan_pengguna/?sirupId='.$sirupId);
	}

	private function parsingAPI(array $data)
  {
    include_once("functions/date.func.php");
    $hasil = [];
    foreach ($data as $row) {
      if ($row->AUTHORIZATION_STATUS != 'SYSTEM_SAVED') {
          $hasil['PR_NO'] = $row->PR_NO;
          $hasil['TOTAL_WITH_TAX'] = currencyToPage($row->TOTAL_WITH_TAX);
      }
    }
    return $hasil; // hasil berupa array (bisa kosong atau berisi data)
  }

	private function findByField(array $data, string $field, $value, bool $singleResult = false)
  {
    include_once("functions/date.func.php");
    $hasil = [];
    foreach ($data as $row) {
      if ($row->$field == $value) {
          $hasil['Nomor PR'] = $row->PR_NO;
          $hasil['SA'] = $row->SA.' - '.$row->SA_DESC;
          $hasil['Organisasi'] = $row->ORG_ID.' - '.$row->ORG_NAME; 
          // $hasil['REQUISITION_HEADER_ID'] = $row->REQUISITION_HEADER_ID;
          $hasil['Deskripsi'] = $row->PR_DESC;
          $hasil['Mata Uang'] = $row->CURRENCY_CODE;
          $hasil['Tanggal Pembuatan'] = getFormattedDate($row->PR_CREATION_DATE);
          $hasil['Status Persetujuan'] = $row->AUTHORIZATION_STATUS;
          $hasil['Total Sebelum Pajak'] = currencyToPage($row->TOTAL_WITHOUT_TAX);
          $hasil['Pajak Dapat Diklaim'] = currencyToPage($row->RECOVERABLE_TAX_TAX);
          $hasil['Pajak Tidak Dapat Diklaim'] = currencyToPage($row->NONRECOVERABLE_TAX);
          $hasil['Total Termasuk Pajak'] = currencyToPage($row->TOTAL_WITH_TAX);
          $hasil['Total RAB'] = $row->TOTAL_RAB;
      }
    }
    return $hasil; // hasil berupa array (bisa kosong atau berisi data)
  }



	// --------------------------------- 1. RUP ---------------------------------
	public function get_rup()
	{
    ini_set('memory_limit', '512M');  // atau 1G kalau perlu
		set_time_limit(0);                // biar tidak timeout

    // A [id] => 124
    // B [tahun] => 2026
    // C [kode_rup] =>
    // D [kode_sa] => 10701
    // E [kode_dpsj] => %%
    // F [no_urut] =>
    // G [kategori_paket_id] => 223
    // H [nama_paket] => Pengadaan Konsumsi Humas UI Open Day
    // I [nilai_pagu] => 3933000
    // J [list_kegiatan] => {"16931|UI Open Days |3.933.000"}
    // K [waktu_awal] => 2025-06
    // L [waktu_akhir] => 2025-07
    // M [status_proses] => verifikasi
    // N [created_by] => candra.nurcahyo
    // O [created_at] => 2025-10-14 10:10:13
    // P [updated_by] => system
    // Q [updated_at] => 2025-11-06 20:50:54.483475
    // R [name] =>
    // S [kategori_paket] => Pengadaan Konsumsi
    // T [nama_sa] => FIB
    // U [nama_dpsj] => {"Humas dan Protokol"}
    // V [metode_pemilihan] => Pembelian Langsung
    // W [nama_jenis_pekerjaan] => Jasa Lainnya
    // X [hasil_verifikasi] =>

    $this->load->library("libapiui");
    $libapiui = new libapiui();
    $data = $libapiui->getRUP();
    // echo "<pre>"; print_r($data); die;
    if ($data->status = 'success') {
      echo count($data->result).' data<br>';

			$excel = $this->excel;
      // ======== SHEET 1 ========
      $sheet1 = $excel->setActiveSheetIndex(0);
      $sheet1->setTitle('SIRUP');

      // Header
      $sheet1->setCellValue('A1', 'id');
      $sheet1->setCellValue('B1', 'tahun');
      $sheet1->setCellValue('C1', 'kode_rup');
      $sheet1->setCellValue('D1', 'kode_sa');
      $sheet1->setCellValue('E1', 'kode_dpsj');
      $sheet1->setCellValue('F1', 'no_urut');
      $sheet1->setCellValue('G1', 'kategori_paket_id');
      $sheet1->setCellValue('H1', 'nama_paket');
      $sheet1->setCellValue('I1', 'nilai_pagu');
			$sheet1->setCellValue('J1', 'list_kegiatan');
      $sheet1->setCellValue('K1', 'waktu_awal');
      $sheet1->setCellValue('L1', 'waktu_akhir');
      $sheet1->setCellValue('M1', 'status_proses');
      $sheet1->setCellValue('N1', 'created_by');
      $sheet1->setCellValue('O1', 'created_at');
      $sheet1->setCellValue('P1', 'updated_by');
      $sheet1->setCellValue('Q1', 'updated_at');
      $sheet1->setCellValue('R1', 'name');
      $sheet1->setCellValue('S1', 'kategori_paket');
			$sheet1->setCellValue('T1', 'nama_sa');
			$sheet1->setCellValue('U1', 'nama_dpsj');
			$sheet1->setCellValue('V1', 'metode_pemilihan');
			$sheet1->setCellValue('W1', 'nama_jenis_pekerjaan');
      $sheet1->setCellValue('X1', 'hasil_verifikasi');
      $sheet1->setCellValue('Y1', 'sumber_dana');
			$row = 2;
      foreach ($data->result as $key => $value) {
				$sheet1->setCellValue("A$row", $value->id);
				$sheet1->setCellValue("B$row", $value->tahun);
				$sheet1->setCellValue("C$row", $value->kode_rup);
				$sheet1->setCellValue("D$row", $value->kode_sa);
				$sheet1->setCellValue("E$row", $value->kode_dpsj);
				$sheet1->setCellValue("F$row", $value->no_urut);
				$sheet1->setCellValue("G$row", $value->kategori_paket_id);
				$sheet1->setCellValue("H$row", $value->nama_paket);
				$sheet1->setCellValue("I$row", $value->nilai_pagu);
				$sheet1->setCellValue("J$row", $value->list_kegiatan);
				$sheet1->setCellValue("K$row", $value->waktu_awal);
				$sheet1->setCellValue("L$row", $value->waktu_akhir);
				$sheet1->setCellValue("M$row", $value->status_proses);
				$sheet1->setCellValue("N$row", $value->created_by);
				$sheet1->setCellValue("O$row", $value->created_at);
				$sheet1->setCellValue("P$row", $value->updated_by);
				$sheet1->setCellValue("Q$row", $value->updated_at);
				$sheet1->setCellValue("R$row", $value->name);
				$sheet1->setCellValue("S$row", $value->kategori_paket);
				$sheet1->setCellValue("T$row", $value->nama_sa);
				$sheet1->setCellValue("U$row", $value->nama_dpsj);
				$sheet1->setCellValue("V$row", $value->metode_pemilihan);
				$sheet1->setCellValue("W$row", $value->nama_jenis_pekerjaan);
				$sheet1->setCellValue("X$row", $value->hasil_verifikasi);
				$sheet1->setCellValue("Y$row", $value->sumber_dana);
				$row++;
      }

			// ===== Simpan ke folder =====
			$filename = 'RUP_'. date('Ymd') .'_'. date('His') .'.xlsx';
      $filepath = FCPATH . $this->pathFileRUP . $filename;

			// Pastikan folder exports/ ada dan bisa ditulisi
      if (!is_dir(FCPATH . $this->pathFileRUP)) {
          mkdir(FCPATH . $this->pathFileRUP, 0777, true);
      }

			$writer = PHPExcel_IOFactory::createWriter($excel, 'Excel2007');
      $writer->save($filepath);

      if(file_exists($filepath)) {
      	// $message = "File berhasil disimpan di: " . $filepath;
      	$message = "File berhasil disimpan";
      	$this->__writeLogs("RUP","SUKSES_GENERATE_EXCEL",$filepath); // Write Logs
				// Next insert to table temporary
				$this->get_importSirup($filename);
      } else {
      	$message = "File gagal disimpan";
      	$this->__writeLogs("RUP","GAGAL_GENERATE_EXCEL",$filepath);
      }
      	echo $message;

    }
	}
	// --------------------------------- END RUP ---------------------------------

	public function get_importSirup($fileNameCron=null)
	{
		// $this->db->db_debug = FALSE; // penting!
		$fileName = $fileNameCron;

		ob_end_clean();
    set_time_limit(0);

		sleep(1);
		$this->load->model("Importsirup");

		require_once APPPATH . "/third_party/PHPExcel.php";
    $filepath = FCPATH . $this->pathFileRUP . $fileName;

		if (file_exists($filepath)) {
			$inputFileName = $filepath;
			$sheetname = array('PR_HEADER','PR_HEADER_ATTACHMENT','PR_LINE','PR_DISTRIBUTION');

			$failed = 0;

				try {
          $inputFileType = PHPExcel_IOFactory::identify($inputFileName);
          $objReader = PHPExcel_IOFactory::createReader($inputFileType);
					$objReader->setLoadSheetsOnly($valueFile);
          $objPHPExcel = $objReader->load($inputFileName);
          $allDataInSheet = $objPHPExcel->getActiveSheet()->toArray(null, true, true, true);
					// echo "<pre>"; print_r($allDataInSheet); die;

          foreach ($allDataInSheet as $key => $value)
					{
          	if ($key >= 2)
						{
          		echo "Proses import RUP " .$value['A'] . " ";
  						flush();

            	// Parsing data ke Parameter
            	$reqA = $value['A'];
            	$reqB = $value['B'];
          		$reqC = $value['C'];
          		$reqD = $value['D'];
            	$reqE = $value['E'];
            	$reqF = $value['F'];
            	$reqG = $value['G'];
            	$reqH = str_replace("'", "''", $value['H']);
            	$reqI = $value['I'];
            	$reqJ = $value['J'];
            	$reqK = $value['K'].'-01';
            	$reqL = $value['L'].'-01';
            	$reqM = $value['M'];
							$reqN = $value['N'];
							$reqO = $value['O'];
							$reqP = $value['P'];
							$reqQ = $value['Q'];
							$reqR = $value['R'];
							$reqS = $value['S'];
							$reqT = $value['T'];
							$reqU = $value['U'];
							$reqV = $value['V'];
							$reqW = $value['W'];
            	$reqX = $value['X'];
            	$reqY = $value['Y'];
							$reqDate = date('Y-m-d H:i:s');
							$reqFile = $fileName;

							$textNya   	= $fileName." ### Import RUP Baris ID-".$reqA." ### ".date('Y-m-d H:i:s');

            	if ($value['A']) // Jika REQUISITION_HEADER_ID tidak kosong
            	{
									$importRUP = new Importsirup();

									$importRUP->setField('ID', $reqA);
									$importRUP->setField('TAHUN', $reqB);
									$importRUP->setField('KODE_RUP', $reqC);
									$importRUP->setField('KODE_SA', $reqD);
									$importRUP->setField('KODE_DPSJ', $reqE);
									$importRUP->setField('NO_URUT', $reqF);
									$importRUP->setField('KATEGORI_PAKET_ID', $reqG);
									$importRUP->setField('NAMA_PAKET', $reqH);
									$importRUP->setField('NILAI_PAGU', $reqI);
									$importRUP->setField('LIST_KEGIATAN', $reqJ);
									$importRUP->setField('WAKTU_AWAL', $reqK);
									$importRUP->setField('WAKTU_AKHIR', $reqL);
									$importRUP->setField('STATUS_PROSES', $reqM);
									$importRUP->setField('CREATED_BY', $reqN);
									$importRUP->setField('CREATED_AT', $reqO);
									$importRUP->setField('UPDATED_BY', $reqP);
									$importRUP->setField('UPDATED_AT', $reqQ);
									$importRUP->setField('NAME', $reqR);
									$importRUP->setField('KATEGORI_PAKET', $reqS);
									$importRUP->setField('NAMA_SA', $reqT);
									$importRUP->setField('NAMA_DPSJ', $reqU);
									$importRUP->setField('METODE_PEMILIHAN', $reqV);
									$importRUP->setField('NAMA_JENIS_PEKERJAAN', $reqW);
									$importRUP->setField('HASIL_VERIIFIKASI', $reqX);
									$importRUP->setField('SUMBER_DANA', $reqY);
									$importRUP->setField('IMPORT_FILE', $fileName);

									$cekRUP = new Importsirup();
									$cekRUP->selectByParams(array("ID" => $reqA));

									// cek data di db, kalau gak ada INSERT, kalau ada UPDATE
									if ($cekRUP->countRow() == 0)
									{
 										if($importRUP->insert())
 										{
											// $handle = fopen($filepath, "a+");
											$text = "Sukses Insert: ".$textNya;
											echo "✅ Sukses...<br>";
											$arr = array(' ', '<br>');
											$logtext = str_replace($arr, "", $text);
 										} else {
											// $handle = fopen($filepath, "a+");
											$text = "Gagal Insert: ".$textNya;
											echo "❌ Gagal...<br>";
											$arr = array(' ', '<br>');
											$logtext = str_replace($arr, "", $text);
											$failed++;
 										}
									} else
									{
										if($importRUP->update())
 										{
											// $handle = fopen($filepath, "a+");
											$text = "Sukses Update: ".$textNya;
											echo "✅ Sukses...<br>";
											$arr = array(' ', '<br>');
											$logtext = str_replace($arr, "", $text);
 										} else {
											// $handle = fopen($filepath, "a+");
											$text = "Gagal Update: ".$textNya;
											echo "❌ Gagal...<br>";
											$arr = array(' ', '<br>');
											$logtext = str_replace($arr, "", $text);
											$failed++;
 										}
									}
										// echo $logtext;
								} else {
								}

								$this->__writeLogsImport("RUP","IMPORT_TO_DATABASE",$logtext); // Write Logs
							}
						}


       	} catch (Exception $e) {
          	die('Error loading file "' . pathinfo($inputFileName, PATHINFO_BASENAME)
                      . '": ' .$e->getMessage());
      	}
			}

	}

	// ================== FOR LOGS FILE & TABLE ==================================================
 	private function __writeLogs($jenis,$status,$path) {
		 $filepath = FCPATH . $this->pathLogs . $jenis .'_LOGS_'. date('Y-m-d') . '.txt';
		 $handle = fopen($filepath, "a+");

		 $text  = '';
		 $text .= $jenis."----
			 STATUS:".$status." ### TIME:".date('h:i:s')." ### DATE:".date('Y-m-d')." ### PATH:". $path."

			 -----------------------------------------------------------------------------------------";
		 $arr = array(' ', '<br>');
		 $logtext = str_replace($arr, "", $text);
		 fwrite($handle, $logtext . "\r\n");
		 fclose($handle);
		 if (!is_dir($filepath))
		 {
			 // mkdir($dir, 0777, true);
			 // chmod($filepath, 0777, true);  //changed to add the zero
		 }
	 }

	 private function __writeLogsImport($jenis,$status,$path) {
      $filepath = FCPATH . $this->pathLogs . $jenis .'_LOGS_'. date('Y-m-d') . '.txt';
      $handle = fopen($filepath, "a+");

      $text  = '';
      $text .= $path;
      $arr = array(' ', '<br>');
      $logtext = str_replace($arr, "", $text);
      fwrite($handle, $logtext . "\r\n");
      fclose($handle);
      if (!is_dir($filepath))
      {
        // mkdir($dir, 0777, true);
        // chmod($filepath, 0777, true);  //changed to add the zero
      }
    }

}
