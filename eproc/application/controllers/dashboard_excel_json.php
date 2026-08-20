<?php
/* INCLUDE FILE */
include_once("functions/string.func.php");
include_once("functions/default.func.php");
include_once("functions/date.func.php");
include_once("functions/pdf.func.php");
include_once("functions/encrypt2.func.php");

defined('BASEPATH') OR exit('No direct script access allowed');

class dashboard_excel_json extends CI_Controller {

	protected $date;
	protected $fileNameSupplier;
	protected $fileNamePO;

	function __construct() {
		parent::__construct(); $this->db->query("SET TIME ZONE 'Asia/Jakarta'"); 
		
		//kauth
		if (!$this->kauth->getInstance()->hasIdentity())
		{
		}       
		
		$this->ID = $this->kauth->getInstance()->getIdentity()->REKANAN_ID;  
		$this->USER_LOGIN_ID =  $this->kauth->getInstance()->getIdentity()->USER_LOGIN_ID;
		$this->USER_LOGIN =  $this->kauth->getInstance()->getIdentity()->USER_LOGIN;
		$this->USER_NAMA =  $this->kauth->getInstance()->getIdentity()->USER_NAMA;
		$this->load->library('excel'); // Load library PHPExcel
		$this->load->model(array("Queryfree"));
	}
	 
	function index() 
	{
		echo "Hallo world!";
	}	

	public function laporan($tahun) // For CronJob /day
	{
		ini_set('memory_limit', '1024M');
		set_time_limit(0);                // biar tidak timeout
		$cacheMethod = PHPExcel_CachedObjectStorageFactory::cache_to_phpTemp;
		$cacheSettings = array('memoryCacheSize' => '256MB');

		PHPExcel_Settings::setCacheStorageMethod($cacheMethod, $cacheSettings);

		$headerStyleBlue = [
		    'font' => [
		        'bold' => true,
		        'color' => ['rgb' => 'FFFFFF'], // teks putih
		        'size' => 11,
		        'name' => 'Calibri'
		    ],
		    'alignment' => [
		        'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
		        'vertical'   => PHPExcel_Style_Alignment::VERTICAL_CENTER,
		        'wrap'       => true
		    ],
		    'fill' => [
		        'type' => PHPExcel_Style_Fill::FILL_SOLID,
		        'startcolor' => [
		            'rgb' => '0e3a80' // biru header
		        ]
		    ],
		    'borders' => [
		        'allborders' => [
		            'style' => PHPExcel_Style_Border::BORDER_THIN,
		            'color' => ['rgb' => '000000']
		        ]
		    ]
		];

		$headerStyleGreen = [
		    'font' => [
		        'bold' => true,
		        'color' => ['rgb' => 'FFFFFF'], // teks putih
		        'size' => 11,
		        'name' => 'Calibri'
		    ],
		    'alignment' => [
		        'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
		        'vertical'   => PHPExcel_Style_Alignment::VERTICAL_CENTER,
		        'wrap'       => true
		    ],
		    'fill' => [
		        'type' => PHPExcel_Style_Fill::FILL_SOLID,
		        'startcolor' => [
		            'rgb' => '08820e' 
		        ]
		    ],
		    'borders' => [
		        'allborders' => [
		            'style' => PHPExcel_Style_Border::BORDER_THIN,
		            'color' => ['rgb' => '000000']
		        ]
		    ]
		];

		$headerStyleOrange = [
		    'font' => [
		        'bold' => true,
		        'color' => ['rgb' => 'FFFFFF'], // teks putih
		        'size' => 11,
		        'name' => 'Calibri'
		    ],
		    'alignment' => [
		        'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
		        'vertical'   => PHPExcel_Style_Alignment::VERTICAL_CENTER,
		        'wrap'       => true
		    ],
		    'fill' => [
		        'type' => PHPExcel_Style_Fill::FILL_SOLID,
		        'startcolor' => [
		            'rgb' => 'c77a06' 
		        ]
		    ],
		    'borders' => [
		        'allborders' => [
		            'style' => PHPExcel_Style_Border::BORDER_THIN,
		            'color' => ['rgb' => '000000']
		        ]
		    ]
		];


		$dataExport = new Queryfree();
		$dataExport->selectByParams("SELECT A.CONTRACTINGREKANANID, A.NAMA, H.NAMA PAKET_METODE_LELANG_STR, I.NAMA PAKET_JENIS_STR, A.NILAI, A.REKANAN_ID, CONCAT(M.NAMA,'. ',L.NAMA) REKANAN_NAMA,
A.JNS_KONTRAK, B.PIC_KONTRAK, A.PIC_PENGENDALI, A.PIC_PENYELESAIAN,
C.CR_SPPBJ_CODE, C.CR_SPPBJ_TANGGAL, C.CR_SPPBJ_JAMINAN_PELAKSANA, C.CR_SPPBJ_JAMINAN_BESAR, 
C.CR_SPPBJ_JAMINAN_JANGKA_DARI, C.CR_SPPBJ_JAMINAN_JANGKA_SAMPAI, C.CR_SPPBJ_JAMINAN_NILAI, C.CR_SPPBJ_PPN, 
C.CR_SPPBJ_NILAI, C.CR_SPPBJ_APPROVED_DATE, C.CR_SPPBJ_JAMINAN_MAKSIMAL_PENYERAHAN, 
C.CR_JENIS_PENGADAAN, C.CONTRACTINGJENISKONTRAKID, J.JK_NAME, C.CR_WAKTU_PELAKSANAAN_DARI, C.CR_WAKTU_PELAKSANAAN_SAMPAI, C.CR_NILAI_KONTRAK,
C.CR_METODE_PEMBAYARAN, C.CR_LEGAL_NOMOR_PKS, C.CR_LEGAL_TANGGAL, C.CR_PO, C.CR_TGL_HASIL_TERIMA_PEMILIHAN, 
C.CR_PENYELESAIAN_KONTRAK_AWAL, C.CR_PENYELESAIAN_KONTRAK_AKHIR, C.CR_MASA_GARANSI, C.CR_MASA_GARANSI_PERIODE, 
C.CR_NAMA_KEGIATAN, G.KEGIATAN, K.CATATAN, C.CR_NAMA_PENGAWAS_UNIT_KERJA, D.KODE_RUP, D.KODE_PR, D.NILAI NILAI_HPS, D.NILAI_HPS_PR, D.NILAI_RAB_PR,
B.BIDDING, CASE WHEN B.BIDDING = '1' THEN (SELECT NILAI_PENAWARAN FROM PAKET_REKANAN BB WHERE BB.PAKET_ID=A.PAKET_ID AND BB.REKANAN_ID=A.REKANAN_ID)
ELSE (SELECT (BB.UNIT_PRICE * BB.QUANTITY) TOTAL FROM PAKET_NEGOSIASI BB WHERE BB.PAKET_ID=A.PAKET_ID) END NILAI_PEMILIHAN,
E.NAMA_DPSJ, DD.SUMBER_DANA_KETERANGAN, D.NILAI_MATA_UANG, B.USER_LOGIN_ID, F.USER_NAMA PIC_PAKET, N.NOMOR NOMOR_BG, N.TANGGAL_JAMINAN, N.TANGGAL_KONFIRMASI_KEBANK,
N.TANGGAL_KONFIRMASI_OLEH_BANK, N.KONFIRMASI, N.CREATED_DATE TANGGAL_BG_UPLOAD, P.NOMOR NOMOR_JAMPEM, P.NILAI NILAI_JAMPEM, 
P.TANGGAL_MULAI TANGGAL_MULAI_JAMPEM, P.TANGGAL_AKHIR TANGGAL_AKHIR_JAMPEM
FROM 
CONTRACTING_REKANAN A 
LEFT JOIN PAKET B ON A.PAKET_ID=B.PAKET_ID
LEFT JOIN CONTRACTING_REKANAN_PROSES1 C ON A.CONTRACTINGREKANANID=C.CONTRACTINGREKANANID
LEFT JOIN PERMOHONAN_PAKET D ON B.PERMOHONAN_PAKET_ID=D.PERMOHONAN_PAKET_ID
LEFT JOIN PERMOHONAN_PAKET_ANALISA DD ON D.PERMOHONAN_PAKET_ANALISA_ID=DD.PERMOHONAN_PAKET_ANALISA_ID
LEFT JOIN IMPORT_SIRUP E ON D.SIRUP_ID=E.ID
LEFT JOIN USER_LOGIN F ON B.USER_LOGIN_ID=F.USER_LOGIN_ID
LEFT JOIN CONTRACTING_KEGIATAN G ON C.CR_NAMA_KEGIATAN=G.CONTRACTING_KEGIATAN_ID
LEFT JOIN PAKET_METODE_LELANG H ON B.PAKET_METODE_LELANG_ID = H.PAKET_METODE_LELANG_ID
LEFT JOIN PAKET_JENIS I ON B.PAKET_JENIS_ID=I.PAKET_JENIS_ID
LEFT JOIN CONTRACTING_JENIS_KONTRAK J ON C.CONTRACTINGJENISKONTRAKID=J.CONTRACTINGJENISKONTRAKID
LEFT JOIN CONTRACTING_STATUS_KONTRAK K ON C.CONTRACTINGSTATUSKONTRAKID=K.CONTRACTINGSTATUSKONTRAKID
LEFT JOIN REKANAN L ON A.REKANAN_ID=L.REKANAN_ID
LEFT JOIN REKANAN_TIPE M ON L.REKANAN_TIPE_ID=M.REKANAN_TIPE_ID
LEFT JOIN CONTRACTING_JAMINAN N ON A.CONTRACTINGREKANANID=N.CONTRACTINGREKANANID
LEFT JOIN CONTRACTING_JAMINAN_PEMELIHARAAN P ON A.CONTRACTINGREKANANID=P.CONTRACTINGREKANANID");

		if ($dataExport->countRow() > 0) {
			while ($dataExport->nextRow()) {
				$dataLaporan[] = array(
									'NONE' => '-',
									'CONTRACTINGREKANANID' => $dataExport->getField("CONTRACTINGREKANANID"),
									'NAMA' => $dataExport->getField("NAMA"),
									'PAKET_METODE_LELANG_STR' => $dataExport->getField("PAKET_METODE_LELANG_STR"),
									'PAKET_JENIS_STR' => $dataExport->getField("PAKET_JENIS_STR"),
									'NILAI' => $dataExport->getField("NILAI"),
									'REKANAN_ID' => $dataExport->getField("REKANAN_ID"),
									'REKANAN_NAMA' => $dataExport->getField("REKANAN_NAMA"),
									'JNS_KONTRAK' => $dataExport->getField("JNS_KONTRAK"),
									'PIC_KONTRAK' => $dataExport->getField("PIC_KONTRAK"),
									'PIC_PENGENDALI' => $dataExport->getField("PIC_PENGENDALI"),
									'PIC_PENYELESAIAN' => $dataExport->getField("PIC_PENYELESAIAN"),
									'CR_SPPBJ_CODE' => $dataExport->getField("CR_SPPBJ_CODE"),
									'CR_SPPBJ_TANGGAL' => $dataExport->getField("CR_SPPBJ_TANGGAL"),
									'CR_SPPBJ_JAMINAN_PELAKSANA' => $dataExport->getField("CR_SPPBJ_JAMINAN_PELAKSANA"),
									'CR_SPPBJ_JAMINAN_BESAR' => $dataExport->getField("CR_SPPBJ_JAMINAN_BESAR"),
									'CR_SPPBJ_JAMINAN_JANGKA_DARI' => $dataExport->getField("CR_SPPBJ_JAMINAN_JANGKA_DARI"),
									'CR_SPPBJ_JAMINAN_JANGKA_SAMPAI' => $dataExport->getField("CR_SPPBJ_JAMINAN_JANGKA_SAMPAI"),
									'CR_SPPBJ_JAMINAN_NILAI' => $dataExport->getField("CR_SPPBJ_JAMINAN_NILAI"),
									'CR_SPPBJ_PPN' => $dataExport->getField("CR_SPPBJ_PPN"),
									'CR_SPPBJ_NILAI' => $dataExport->getField("CR_SPPBJ_NILAI"),
									'CR_SPPBJ_APPROVED_DATE' => $dataExport->getField("CR_SPPBJ_APPROVED_DATE"),
									'CR_SPPBJ_JAMINAN_MAKSIMAL_PENYERAHAN' => $dataExport->getField("CR_SPPBJ_JAMINAN_MAKSIMAL_PENYERAHAN"),
									'CR_JENIS_PENGADAAN' => $dataExport->getField("CR_JENIS_PENGADAAN"),
									'CONTRACTINGJENISKONTRAKID' => $dataExport->getField("CONTRACTINGJENISKONTRAKID"),
									'JK_NAME' => $dataExport->getField("JK_NAME"),
									'CR_WAKTU_PELAKSANAAN_DARI' => $dataExport->getField("CR_WAKTU_PELAKSANAAN_DARI"),
									'CR_WAKTU_PELAKSANAAN_SAMPAI' => $dataExport->getField("CR_WAKTU_PELAKSANAAN_SAMPAI"),
									'CR_NILAI_KONTRAK' => $dataExport->getField("CR_NILAI_KONTRAK"),
									'CR_METODE_PEMBAYARAN' => $dataExport->getField("CR_METODE_PEMBAYARAN"),
									'CR_LEGAL_NOMOR_PKS' => $dataExport->getField("CR_LEGAL_NOMOR_PKS"),
									'CR_LEGAL_TANGGAL' => $dataExport->getField("CR_LEGAL_TANGGAL"),
									'CR_PO' => $dataExport->getField("CR_PO"),
									'CR_TGL_HASIL_TERIMA_PEMILIHAN' => $dataExport->getField("CR_TGL_HASIL_TERIMA_PEMILIHAN"),
									'CR_PENYELESAIAN_KONTRAK_AWAL' => $dataExport->getField("CR_PENYELESAIAN_KONTRAK_AWAL"),
									'CR_PENYELESAIAN_KONTRAK_AKHIR' => $dataExport->getField("CR_PENYELESAIAN_KONTRAK_AKHIR"),
									'CR_MASA_GARANSI' => $dataExport->getField("CR_MASA_GARANSI"),
									'CR_MASA_GARANSI_PERIODE' => $dataExport->getField("CR_MASA_GARANSI_PERIODE"),
									'CR_NAMA_KEGIATAN' => $dataExport->getField("CR_NAMA_KEGIATAN"),
									'KEGIATAN' => $dataExport->getField("KEGIATAN"),
									'CATATAN' => $dataExport->getField("CATATAN"),
									'CR_NAMA_PENGAWAS_UNIT_KERJA' => $dataExport->getField("CR_NAMA_PENGAWAS_UNIT_KERJA"),
									'KODE_RUP' => $dataExport->getField("KODE_RUP"),
									'KODE_PR' => $dataExport->getField("KODE_PR"),
									'NILAI_HPS' => $dataExport->getField("NILAI_HPS"),
									'NILAI_HPS_PR' => $dataExport->getField("NILAI_HPS_PR"),
									'NILAI_RAB_PR' => $dataExport->getField("NILAI_RAB_PR"),
									'BIDDING' => $dataExport->getField("BIDDING"),
									'NILAI_PEMILIHAN' => $dataExport->getField("NILAI_PEMILIHAN"),
									'NAMA_DPSJ' => $dataExport->getField("NAMA_DPSJ"),
									'SUMBER_DANA' => $dataExport->getField("SUMBER_DANA_KETERANGAN"),
									'NILAI_MATA_UANG' => $dataExport->getField("NILAI_MATA_UANG"),
									'USER_LOGIN_ID' => $dataExport->getField("USER_LOGIN_ID"),
									'USER_NAMA PIC_PAKET' => $dataExport->getField("PIC_PAKET"),
									'NOMOR NOMOR_BG' => $dataExport->getField("NOMOR_BG"),
									'TANGGAL_JAMINAN' => $dataExport->getField("TANGGAL_JAMINAN"),
									'TANGGAL_KONFIRMASI_KEBANK' => $dataExport->getField("TANGGAL_KONFIRMASI_KEBANK"),
									'TANGGAL_KONFIRMASI_OLEH_BANK' => $dataExport->getField("TANGGAL_KONFIRMASI_OLEH_BANK"),
									'KONFIRMASI' => $dataExport->getField("KONFIRMASI"),
									'TANGGAL_BG_UPLOAD' => $dataExport->getField("TANGGAL_BG_UPLOAD"),
									'NOMOR NOMOR_JAMPEM' => $dataExport->getField("NOMOR_JAMPEM"),
									'NILAI_JAMPEM' => $dataExport->getField("NILAI_JAMPEM"),
									'TANGGAL_MULAI_JAMPEM' => $dataExport->getField("TANGGAL_MULAI_JAMPEM"),
									'TANGGAL_AKHIR_JAMPEM' => $dataExport->getField("TANGGAL_AKHIR_JAMPEM"),
								);
			}
			$countData = $dataExport->countRow();
		} else {
			$dataExport[] = array();
			$countData = 0;
		}
		// echo "<pre>"; print_r($dataLaporan); die();

        $excel = $this->excel;
        // ======== SHEET 1 ========
        $sheet1 = $excel->setActiveSheetIndex(0);
        $sheet1->setTitle('Laporan Kontrak '.$tahun);

        // Header
        $sheet1->setCellValue('A2', 'Nomor');
		$sheet1->setCellValue('B2', '2.1 NO FOLDER');
		$sheet1->setCellValue('C2', '2.1 TGL LAP PEMILIHAN');
		$sheet1->setCellValue('D2', '2.1 TANGGAL CREATE PO');
		$sheet1->setCellValue('E2', '2 PR');
		$sheet1->setCellValue('F2', 'PO');
		$sheet1->setCellValue('G2', 'DPSJ');
		$sheet1->setCellValue('H2', 'Nama Paket Pengadaan');
		$sheet1->setCellValue('I2', 'PENGADAAN UMUM/PENGADAAN KHUSUS/NILAI DIBAWAH 25 JUTA (ASSET/INV)');
		$sheet1->setCellValue('J2', 'NAMA KEGIATAN');
		$sheet1->setCellValue('K2', 'METODE PEMILIHAN');
		$sheet1->setCellValue('L2', 'JENIS PENGADAAN');
		$sheet1->setCellValue('M2', 'JENIS KONTRAK');
		$sheet1->setCellValue('N2', 'KONTRAK');
		$sheet1->setCellValue('O2', 'NAMA PENYEDIA');
		$sheet1->setCellValue('P2', 'CARA PEMBAYARAN');
		$sheet1->setCellValue('Q2', 'SUMBER DANA');
		$sheet1->setCellValue('R2', 'MATA UANG');
		$sheet1->setCellValue('S2', '2/2.1 NILAI HPS PLUS PPN (NILAI PR)');
		$sheet1->setCellValue('T2', '2.1 NILAI HASIL PEMILIHAN');
		$sheet1->setCellValue('U2', '2.1 EFISIENSI HPS VS PEMILIHAN');
		$sheet1->setCellValue('V2', 'NILAI KONTRAK AWAL (+PPN)');
		$sheet1->setCellValue('W2', 'PIC 2.1');
		$sheet1->setCellValue('X2', 'NILAI ADENDUM 1');
		$sheet1->setCellValue('Y2', 'NILAI ADENDUM 2');
		$sheet1->setCellValue('Z2', 'NILAI ADENDUM 3');
		$sheet1->setCellValue('AA2', 'NILAI ADENDUM 4');
		$sheet1->setCellValue('AB2', 'NILAI KONTRAK AKHIR SETELAH ADENDUM PLUS PPN (NILAI PO)');
		$sheet1->setCellValue('AC2', 'EFISIENSI PEMILIHAN VS KONTRAK (ADENDUM)');
		$sheet1->setCellValue('AD2', 'EFISIENSI HPS S.D KONTRAK (ADENDUM)');
		$sheet1->setCellValue('AE2', 'NO. KONTRAK / ADDENDUM');
		$sheet1->setCellValue('AF2', 'TGL RAPAT e-Purchasing/TGL KAJUL PAKET');
		$sheet1->setCellValue('AG2', 'TANGGAL RANCANGAN KONTRAK');
		$sheet1->setCellValue('AH2', 'TGL KONTRAK/ADDENDUM');
		$sheet1->setCellValue('AI2', '2.1 TGL MULAI KONTRAK');
		$sheet1->setCellValue('AJ2', 'REMINDER JADWAL KIRIM/PENYELESAIAN PEKERJAAN');
		$sheet1->setCellValue('AK2', 'TGL PENGIRIMAN BARANG/JASA');
		$sheet1->setCellValue('AL2', 'TANGGAL BATAS PENYELESAIAN ADMINISTRASI');
		$sheet1->setCellValue('AM2', '2.1 TGL AKHIR KONTRAK');
		$sheet1->setCellValue('AN2', '2.1 WAPEL');
		$sheet1->setCellValue('AO2', 'SPPBJ');
		$sheet1->setCellValue('AP2', 'BG');
		$sheet1->setCellValue('AQ2', 'NO BG');
		$sheet1->setCellValue('AR2', 'NILAI BG');
		$sheet1->setCellValue('AS2', 'MASA BERLAKU (MULAI)');
		$sheet1->setCellValue('AT2', 'MASA BERLAKU (AKHIR)');
		$sheet1->setCellValue('AU2', 'SELISIH BERLAKU BG');
		$sheet1->setCellValue('AV2', 'TGL KONFIRMASI BG KE BANK');
		$sheet1->setCellValue('AW2', 'KONFIRMASI BG OLEH BANK');
		$sheet1->setCellValue('AX2', 'BG CONFIRMED?');
		$sheet1->setCellValue('AY2', 'TGL KIRIM EMAIL KE PENYEDIA');
		$sheet1->setCellValue('AZ2', 'TGL TERIMA KONTRAK/SPK DARI PENYEDIA');
		$sheet1->setCellValue('BA2', 'SELISIH TTD KONTRAK');
		$sheet1->setCellValue('BB2', 'TGL PARAF KASUBDIT');
		$sheet1->setCellValue('BC2', 'TGL PPK TTD');
		$sheet1->setCellValue('BD2', 'LAMA PROSES');
		$sheet1->setCellValue('BE2', '2.1 STATUS PAKET ADMIN KONTRAK');
		$sheet1->setCellValue('BF2', 'STATUS ADENDUM');
		$sheet1->setCellValue('BG2', 'PIC 2.2');
		$sheet1->setCellValue('BH2', '2.2 NAMA PENGAWAS UKER 1');
		$sheet1->setCellValue('BI2', '2.2 MASA PEMELIHARAAN');
		$sheet1->setCellValue('BJ2', 'NO BG JAMINAN PEMELIHARAAN');
		$sheet1->setCellValue('BK2', 'NILAI BG JAMINAN PEMELIHARAAN');
		$sheet1->setCellValue('BL2', 'TGL AWAL BG JAMINAN PEMELIHARAAN');
		$sheet1->setCellValue('BM2', 'TGL AKHIR BG JAMINAN PEMELIHARAAN');
		$sheet1->setCellValue('BN2', '2.2 LANJUT KE SHEET BULANAN/TRIWULAN/QUARTAL/SEMESTER (NO URUT SHEET)');
		$sheet1->setCellValue('BO2', '2.2 TGL SELESAI SESUAI DI LAPANGAN');
		$sheet1->setCellValue('BP2', '2.2 SELISIH SELESAI PEKERJAAN VS TGL KONTRAK');
		$sheet1->setCellValue('BQ2', '2.2 TGL LAPORAN DITERIMA DARI PENYEDIA');
		$sheet1->setCellValue('BR2', 'NILAI DENDA KETERLAMBATAN (JIKA ADA)');
		$sheet1->setCellValue('BS2', '2.2 STATUS PENGENDALIAN KONTRAK');
		$sheet1->setCellValue('BT2', '2.2 KOMEN KASUB PROSES PENGENDALIAN');
		$sheet1->setCellValue('BU2', '2.2 KETERANGAN TAMBAHAN');
		$sheet1->setCellValue('BV2', '2.3 TGL PERMINTAAN RECEIPT DARI 2.2');
		$sheet1->setCellValue('BW2', '2.3 TGL DITERIMA LAPORAN DARI SEKSI 2.22');
		$sheet1->setCellValue('BX2', '2.3 PIC Admin Penagihan');
		$sheet1->setCellValue('BY2', '2.3 TGL CREATE RECEIPT IPROC');
		$sheet1->setCellValue('BZ2', '2.3 NO RECEIPT');
		$sheet1->setCellValue('CA2', '2.3 Nilai BAP');
		$sheet1->setCellValue('CB2', 'SELISIH NILAI KONTRAK VS BAP');
		$sheet1->setCellValue('CC2', 'SELISIH NILAI HPS VS BAP');
		$sheet1->setCellValue('CD2', '2.3 TGL DIKIRIM KE PENYEDIA');
		$sheet1->setCellValue('CE2', '2.3 TGL DITERIMA DARI PENYEDIA (LPHP, BAST & BAP)');
		$sheet1->setCellValue('CF2', '2.3 TANGGAL DITERIMA SETELAH REVISI DOKUMEN');
		$sheet1->setCellValue('CG2', 'SELISIH HARI (DIKIRIM VS DITERIMA DARI PENYEDIA)');
		$sheet1->setCellValue('CH2', '2.3 TANGGAL DOKUMEN TAGIHAN DISERAHKAN KE DKA');
		$sheet1->setCellValue('CI2', '2.3 TANGGAL DI RETUR OLEH DKA');
		$sheet1->setCellValue('CJ2', '2.3 KETERANGAN REVISI BERKAS');
		$sheet1->setCellValue('CK2', '2.3 TANGGAL DOKUMEN REVISI DISERAHKAN KEMBALI KE DKA');
		$sheet1->setCellValue('CL2', '2.3 TANGGAL PAYMENT (BNI DIRECT)');
		$sheet1->setCellValue('CM2', 'NILAI DIBAYAR DKA');
		$sheet1->setCellValue('CN2', 'SELISIH NILAI BAP VS PAYMENT DKA');
		$sheet1->setCellValue('CO2', 'SELISIH SUBMIT DOKUMEN VS DIBAYAR OLEH DKA');
		$sheet1->setCellValue('CP2', '2.3 STATUS PAKET ADM PENAGIHAN');
		$sheet1->setCellValue('CQ2', '2.3 KOMEN KASUB PROSES PENAGIHAN');
		$sheet1->setCellValue('CR2', '2.3 KETERANGAN TAMBAHAN SIE 2.3');
		$sheet1->setCellValue('CS2', '2.3 STATUS (OPEN/CLOSED)'); 

        // Terapkan style header
		$sheet1->getStyle('A2:AT2')->applyFromArray($headerStyleBlue);
		$sheet1->getStyle('U2')->applyFromArray($headerStyleGreen);
		$sheet1->getStyle('V2:AA2')->applyFromArray($headerStyleBlue);
		$sheet1->getStyle('AB2:AD2')->applyFromArray($headerStyleGreen);
		$sheet1->getStyle('AE2:AM2')->applyFromArray($headerStyleBlue);
		$sheet1->getStyle('AN2')->applyFromArray($headerStyleGreen);
		$sheet1->getStyle('AO2:AZ2')->applyFromArray($headerStyleBlue); 
		$sheet1->getStyle('BA2:BC2')->applyFromArray($headerStyleGreen);
		$sheet1->getStyle('BD2:BE2')->applyFromArray($headerStyleBlue); 
		$sheet1->getStyle('BF2')->applyFromArray($headerStyleGreen);
		$sheet1->getStyle('BG2:BH2')->applyFromArray($headerStyleBlue); 
		$sheet1->getStyle('BI2:BM2')->applyFromArray($headerStyleOrange);
		$sheet1->getStyle('BN2')->applyFromArray($headerStyleGreen);
		$sheet1->getStyle('BO2')->applyFromArray($headerStyleOrange);
		$sheet1->getStyle('BP2')->applyFromArray($headerStyleGreen);
		$sheet1->getStyle('BQ2:BR2')->applyFromArray($headerStyleOrange);
		$sheet1->getStyle('BS2')->applyFromArray($headerStyleGreen);
		$sheet1->getStyle('BT2:CA2')->applyFromArray($headerStyleOrange);
		$sheet1->getStyle('CB2:CC2')->applyFromArray($headerStyleGreen);
		$sheet1->getStyle('CD2:CF2')->applyFromArray($headerStyleOrange);
		$sheet1->getStyle('CG2')->applyFromArray($headerStyleGreen);
		$sheet1->getStyle('CH2:CM2')->applyFromArray($headerStyleOrange);
		$sheet1->getStyle('CN2')->applyFromArray($headerStyleGreen);
		$sheet1->getStyle('CO2')->applyFromArray($headerStyleBlue); 
		$sheet1->getStyle('CP2')->applyFromArray($headerStyleGreen);
		$sheet1->getStyle('CQ2:CR2')->applyFromArray($headerStyleOrange);
		$sheet1->getStyle('CS2')->applyFromArray($headerStyleGreen);

        $row = 3;
        foreach ($dataLaporan as $data) 
        { 
	        $sheet1->setCellValue("A$row", $data['NONE']);
			$sheet1->setCellValue("B$row", $data['NONE']);
			$sheet1->setCellValue("C$row", $data['CR_TGL_HASIL_TERIMA_PEMILIHAN']);
			$sheet1->setCellValue("D$row", $data['NONE']);
			$sheet1->setCellValue("E$row", $data['KODE_PR']);
			$sheet1->setCellValue("F$row", $data['CR_PO']);
			$sheet1->setCellValue("G$row", $data['NAMA_DPSJ']);
			$sheet1->setCellValue("H$row", $data['NAMA']);
			$sheet1->setCellValue("I$row", $data['PIC_PAKET']);
			$sheet1->setCellValue("J$row", $data['KEGIATAN']);
			$sheet1->setCellValue("K$row", $data['PAKET_METODE_LELANG_STR']);
			$sheet1->setCellValue("L$row", $data['PAKET_JENIS_STR']);
			$sheet1->setCellValue("M$row", $data['JK_NAME']);
			$sheet1->setCellValue("N$row", $data['CATATAN']);
			$sheet1->setCellValue("O$row", $data['REKANAN_NAMA']);
			$sheet1->setCellValue("P$row", $data['CR_METODE_PEMBAYARAN']);
			$sheet1->setCellValue("Q$row", $data['SUMBER_DANA']);
			$sheet1->setCellValue("R$row", $data['NILAI_MATA_UANG']);
			$sheet1->setCellValue("S$row", $data['NILAI_HPS_PR']);
			$sheet1->setCellValue("T$row", $data['NILAI_PEMILIHAN']);
			$sheet1->setCellValue("U$row", $data['NILAI_HPS_PR'] - $data['NILAI_PEMILIHAN']);
			$sheet1->setCellValue("V$row", $data['CR_SPPBJ_NILAI']);
			$sheet1->setCellValue("W$row", $data['PIC_KONTRAK']);
			$sheet1->setCellValue("X$row", $data['NONE']);
			$sheet1->setCellValue("Y$row", $data['NONE']);
			$sheet1->setCellValue("Z$row", $data['NONE']);
			$sheet1->setCellValue("AA$row", $data['NONE']);
			$sheet1->setCellValue("AB$row", $data['NONE']);
			$sheet1->setCellValue("AC$row", $data['NONE']);
			$sheet1->setCellValue("AD$row", $data['NONE']);
			$sheet1->setCellValue("AE$row", $data['CR_LEGAL_NOMOR_PKS']);
			$sheet1->setCellValue("AF$row", $data['NONE']);
			$sheet1->setCellValue("AG$row", $data['NONE']);
			$sheet1->setCellValue("AH$row", $data['CR_WAKTU_PELAKSANAAN_DARI']);
			$sheet1->setCellValue("AI$row", $data['CR_WAKTU_PELAKSANAAN_DARI']);
			$sheet1->setCellValue("AJ$row", $data['CR_PENYELESAIAN_KONTRAK_AWAL']);
			$sheet1->setCellValue("AK$row", $data['NONE']);
			$sheet1->setCellValue("AL$row", $data['CR_PENYELESAIAN_KONTRAK_AKHIR']);
			$sheet1->setCellValue("AM$row", $data['CR_WAKTU_PELAKSANAAN_SAMPAI']);
			$sheet1->setCellValue("AN$row", $data['NONE']);
			$sheet1->setCellValue("AO$row", $data['NONE']);
			$sheet1->setCellValue("AP$row", $data['NONE']);
			$sheet1->setCellValue("AQ$row", $data['NOMOR_BG']);
			$sheet1->setCellValue("AR$row", $data['NONE']);
			$sheet1->setCellValue("AS$row", $data['TANGGAL_JAMINAN']);
			$sheet1->setCellValue("AT$row", $data['NONE']);
			$sheet1->setCellValue("AU$row", $data['NONE']);
			$sheet1->setCellValue("AV$row", $data['TANGGAL_KONFIRMASI_KEBANK']);
			$sheet1->setCellValue("AW$row", $data['TANGGAL_KONFIRMASI_OLEH_BANK']);
			$sheet1->setCellValue("AX$row", $data['KONFIRMASI']);
			$sheet1->setCellValue("AY$row", $data['NONE']);
			$sheet1->setCellValue("AZ$row", $data['TANGGAL_BG_UPLOAD']);
			$sheet1->setCellValue("BA$row", $data['NONE']);
			$sheet1->setCellValue("BB$row", $data['NONE']);
			$sheet1->setCellValue("BC$row", $data['NONE']);
			$sheet1->setCellValue("BD$row", $data['NONE']);
			$sheet1->setCellValue("BE$row", $data['NONE']);
			$sheet1->setCellValue("BF$row", $data['NONE']);
			$sheet1->setCellValue("BG$row", $data['PIC_PENGENDALI']);
			$sheet1->setCellValue("BH$row", $data['CR_NAMA_PENGAWAS_UNIT_KERJA']);
			$sheet1->setCellValue("BI$row", $data['NONE']);
			$sheet1->setCellValue("BJ$row", $data['NOMOR_JAMPEM']);
			$sheet1->setCellValue("BK$row", $data['NILAI_JAMPEM']);
			$sheet1->setCellValue("BL$row", $data['TANGGAL_MULAI_JAMPEM']);
			$sheet1->setCellValue("BM$row", $data['TANGGAL_AKHIR_JAMPEM']);
			$sheet1->setCellValue("BN$row", $data['NONE']);
			$sheet1->setCellValue("BO$row", $data['NONE']);
			$sheet1->setCellValue("BP$row", $data['NONE']);
			$sheet1->setCellValue("BQ$row", $data['NONE']);
			$sheet1->setCellValue("BR$row", $data['NONE']);
			$sheet1->setCellValue("BS$row", $data['NONE']);
			$sheet1->setCellValue("BT$row", $data['NONE']);
			$sheet1->setCellValue("BU$row", $data['NONE']);
			$sheet1->setCellValue("BV$row", $data['NONE']);
			$sheet1->setCellValue("BW$row", $data['NONE']);
			$sheet1->setCellValue("BX$row", $data['NONE']);
			$sheet1->setCellValue("BY$row", $data['NONE']);
			$sheet1->setCellValue("BZ$row", $data['NONE']);
			$sheet1->setCellValue("CA$row", $data['NONE']);
			$sheet1->setCellValue("CB$row", $data['NONE']);
			$sheet1->setCellValue("CC$row", $data['NONE']);
			$sheet1->setCellValue("CD$row", $data['NONE']);
			$sheet1->setCellValue("CE$row", $data['NONE']);
			$sheet1->setCellValue("CF$row", $data['NONE']);
			$sheet1->setCellValue("CG$row", $data['NONE']);
			$sheet1->setCellValue("CH$row", $data['NONE']);
			$sheet1->setCellValue("CI$row", $data['NONE']);
			$sheet1->setCellValue("CJ$row", $data['NONE']);
			$sheet1->setCellValue("CK$row", $data['NONE']);
			$sheet1->setCellValue("CL$row", $data['NONE']);
			$sheet1->setCellValue("CM$row", $data['NONE']);
			$sheet1->setCellValue("CN$row", $data['NONE']);
			$sheet1->setCellValue("CO$row", $data['NONE']);
			$sheet1->setCellValue("CP$row", $data['NONE']);
			$sheet1->setCellValue("CQ$row", $data['NONE']);
			$sheet1->setCellValue("CR$row", $data['NONE']);
			$sheet1->setCellValue("CS$row", $data['NONE']); 
            $row++;
        }

         
        // ===== Simpan ke folder =====
        $filename = 'laporan_kontrak_'.$tahun.'.xlsx'; 

        // Bersihkan output buffer jika ada
		if (ob_get_length()) {
		    ob_end_clean();
		}

		// Header download
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment;filename="'.$filename.'"');
		header('Cache-Control: max-age=0');
		header('Cache-Control: max-age=1');
		header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
		header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
		header('Cache-Control: cache, must-revalidate');
		header('Pragma: public');

        $writer = PHPExcel_IOFactory::createWriter($excel, 'Excel2007');
        $writer->save('php://output');  
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

    private function __saveToTableLogs($jenis,$filename,$path,$note,$data) {
		$keyInsert	= new Integrate();
		$keyInsert->setField("TYPE", $jenis);
		$keyInsert->setField("FILE_NAME", $filename);
		$keyInsert->setField("FILE_PATH", $path);
		$keyInsert->setField("NOTE", $note);
		if($keyInsert->insert()) {
			if ($jenis == 'SUPPLIER') {
				foreach ($data as $key => $value) {
					$logInsert	= new Integrate();
					$logInsert->setField("REKANAN_ID", $value['rekanan_id']);
					$logInsert->setField("STATUS_GENERATE_EXCEL", '1');
					$logInsert->setField("FILE_NAME", $filename);
					$logInsert->insertLog();
				}
			} else if ($jenis == 'PO') {
				foreach ($data as $key => $value) {
					$logInsert	= new Integrate();
					$logInsert->setField("PAKET_ID", $value['paket_id']);
					$logInsert->setField("STATUS_GENERATE_EXCEL", '1');
					$logInsert->setField("FILE_NAME", $filename);
					$logInsert->insertLogPO();
				}
			}

		}
    } 

    private function __updateToTableLogs($status,$note) {
		$keyInsert	= new Integrate();
		$keyInsert->setField("SEND_STATUS", $status);
		$keyInsert->setField("SEND_NOTE", $note);
		$keyInsert->updateStatus();
    }

	function logs_file_delete($file)
	{
        $dir   = 'integration/logs/';
		if ($file) {
			unlink($dir.$file);
		}
		redirect(base_url('integration/index/integration_monitoring'));
	}  
			
}
?>