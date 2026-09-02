<?php
/**
 * @package 	eProcurement Application
 * @author 		eproc2025
 * @since 		25. Version 3.1
 *
 * Model fitur baru: Pencatatan Tindak Lanjut Kelengkapan Dokumen Penyedia.
 * Gaya penulisan meniru model Rekamjejak.php / Masterpengaturan.php yang
 * sudah ada (PK integer di-generate lewat getNextId, query SQL manual).
 *
 * Tabel: REKANAN_TINDAK_LANJUT (lihat sql/buat_tabel_rekanan_tindak_lanjut.sql)
 */

  include_once('entity.php');

  class Rekanantindaklanjut extends Entity{

	var $query;
	var $id;

    function __construct()
	{
	 parent::__construct();
    }

	function insert()
	{
		$this->setField("ID", $this->getNextId("ID", "REKANAN_TINDAK_LANJUT"));

		$emailTerkirim = $this->getField("EMAIL_TERKIRIM") ? "TRUE" : "FALSE";
		$emailTerkirimDate = $this->getField("EMAIL_TERKIRIM") ? "NOW()" : "NULL";
		$createdBy = $this->getField("CREATED_BY");
		$createdBy = ($createdBy === null || $createdBy === '') ? "NULL" : "'".$createdBy."'";

		$str = "
			INSERT INTO REKANAN_TINDAK_LANJUT (
			   ID, REKANAN_ID, STATUS, JENIS, CATATAN, PIHAK,
			   CREATED_BY, CREATED_DATE,
			   EMAIL_TUJUAN, EMAIL_TERKIRIM, EMAIL_TERKIRIM_DATE)
			VALUES (
			  '".$this->getField("ID")."',
			  '".$this->getField("REKANAN_ID")."',
			  '".$this->getField("STATUS")."',
			  '".$this->getField("JENIS")."',
			  '".str_replace("'", "''", strip_tags($this->getField("CATATAN")))."',
			  '".$this->getField("PIHAK")."',
			  ".$createdBy.",
			  NOW(),
			  '".str_replace("'", "''", $this->getField("EMAIL_TUJUAN"))."',
			  ".$emailTerkirim.",
			  ".$emailTerkirimDate."
			)";

		$this->query = $str;
		$this->id = $this->getField("ID");
		return $this->execQuery($str);
    }

	/**
	 * Seluruh riwayat untuk 1 rekanan, urut lama -> baru (buat timeline).
	 */
	function selectByRekananId($rekananId)
	{
		$str = " SELECT A.*
				 FROM REKANAN_TINDAK_LANJUT A
				 WHERE A.REKANAN_ID = '".intval($rekananId)."'
				 ORDER BY A.CREATED_DATE ASC, A.ID ASC ";

		$this->query = $str;
		return $this->selectLimit($str);
	}

	/**
	 * Baris paling baru untuk 1 rekanan. Dipakai untuk tahu status "sekarang".
	 */
	function selectTerakhirByRekananId($rekananId)
	{
		$str = " SELECT A.*
				 FROM REKANAN_TINDAK_LANJUT A
				 WHERE A.REKANAN_ID = '".intval($rekananId)."'
				 ORDER BY A.CREATED_DATE DESC, A.ID DESC ";

		$this->query = $str;
		return $this->selectLimit($str, 1);
	}

	/**
	 * Hitung berapa kali penyedia ini sudah dikirimi permintaan/pengingat
	 * (JENIS PERMINTAAN atau REMINDER). Dipakai buat kolom "sudah difollow up
	 * berapa kali" di layar verifikator.
	 */
	function hitungFollowUp($rekananId)
	{
		$str = " SELECT COUNT(*) AS JML
				 FROM REKANAN_TINDAK_LANJUT
				 WHERE REKANAN_ID = '".intval($rekananId)."'
				   AND JENIS IN ('PERMINTAAN', 'REMINDER') ";
		$this->select($str);
		if ($this->firstRow()) {
			return (int) $this->getField("JML");
		}
		return 0;
	}

	/**
	 * USER_LOGIN + USER_NAMA verifikator yang paling terakhir mengirim
	 * PERMINTAAN / SELESAI untuk rekanan ini. Dipakai untuk menentukan
	 * ke mana email "penyedia sudah melengkapi" dikirim (ke orang yang
	 * memang sedang menangani, bukan broadcast ke semua verifikator).
	 */
	function selectVerifikatorPenangani($rekananId)
	{
		$str = " SELECT U.USER_LOGIN, U.USER_NAMA
				 FROM REKANAN_TINDAK_LANJUT T
				 JOIN USER_LOGIN U ON U.USER_LOGIN_ID = T.CREATED_BY
				 WHERE T.REKANAN_ID = '".intval($rekananId)."'
				   AND T.JENIS IN ('PERMINTAAN', 'SELESAI')
				 ORDER BY T.CREATED_DATE DESC, T.ID DESC ";

		$this->query = $str;
		return $this->selectLimit($str, 1);
	}

	/**
	 * Daftar rekanan yang perlu dikirimi email pengingat otomatis oleh cron:
	 * baris terakhirnya PERLU_DILENGKAPI, dan sudah lewat > $hariJeda hari
	 * sejak kejadian terakhir (permintaan/pengingat sebelumnya).
	 *
	 * Return array of rows (rekanan_id, email, nama, kode, catatan, hari_diam).
	 */
	function selectAntrianReminder($hariJeda = 7, $maksReminder = 3)
	{
		$str = "
			SELECT R.REKANAN_ID, R.EMAIL, R.NAMA, R.KODE,
				   T.CATATAN,
				   EXTRACT(DAY FROM (NOW() - T.CREATED_DATE))::INT AS HARI_DIAM
			FROM REKANAN R
			JOIN (
				SELECT DISTINCT ON (REKANAN_ID) REKANAN_ID, STATUS, CATATAN, CREATED_DATE
				FROM REKANAN_TINDAK_LANJUT
				ORDER BY REKANAN_ID, CREATED_DATE DESC, ID DESC
			) T ON T.REKANAN_ID = R.REKANAN_ID
			WHERE T.STATUS = 'PERLU_DILENGKAPI'
			  AND T.CREATED_DATE < (NOW() - (INTERVAL '1 day' * ".intval($hariJeda)."))
			  AND R.EMAIL IS NOT NULL
			  AND R.EMAIL <> ''
			  AND (
				SELECT COUNT(*) FROM REKANAN_TINDAK_LANJUT X
				WHERE X.REKANAN_ID = R.REKANAN_ID AND X.JENIS = 'REMINDER'
			  ) < ".intval($maksReminder)."
			ORDER BY T.CREATED_DATE ASC ";

		$this->query = $str;
		return $this->selectLimit($str);
	}

  }
?>
