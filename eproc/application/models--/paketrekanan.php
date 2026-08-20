<?php
/**
 * @package 	eProcurement Application
 * @author 		eproc2025
 * @since 		25. Version 3.1
 *
 */

  include_once('entity.php');

  class PaketRekanan extends Entity{

	var $query;
    /**
    * Class constructor.
    **/

    function __construct(){
	  parent::__construct();
	}
    function PaketRekanan()
	{
      $this->Entity();
    }

	function insert()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$this->setField("PAKET_REKANAN_ID", $this->getNextId("PAKET_REKANAN_ID","PAKET_REKANAN"));

		$str = "
				INSERT INTO PAKET_REKANAN (
				   PAKET_REKANAN_ID, PAKET_ID, REKANAN_ID,
				   TANGGAL_UNDANG, TANGGAL_DAFTAR, NILAI_PENAWARAN,
				   KODE_REKANAN, PAKTA, SPM,
				   NILAI_PENGALAMAN, NILAI_PERSONIL, NILAI_PERALATAN,
				   NILAI_KEUANGAN, NILAI_SERTIFIKAT_LAIN, LULUS_KUALIFIKASI,
				   LULUS_PENAWARAN, LULUS_PENAWARAN_URUT, LULUS_ADMINISTRASI,
				   LULUS_KEMAMPUAN_DASAR, LULUS_KUALIFIKASI_REKANAN, LULUS_KEWAJARAN_HARGA,
				   STATUS_BAYAR, DI_EMAIL)
				VALUES (".$this->getField("PAKET_REKANAN_ID").", ".$this->getField("PAKET_ID").", ".$this->getField("REKANAN_ID").",
				   '".$this->getField("TANGGAL_UNDANG")."', '".$this->getField("TANGGAL_DAFTAR")."', ".$this->getField("NILAI_PENAWARAN").",
				   '".$this->getField("KODE_REKANAN")."', '".$this->getField("PAKTA")."', '".$this->getField("SPM")."',
				   ".$this->getField("NILAI_PENGALAMAN").", ".$this->getField("NILAI_PERSONIL").", ".$this->getField("NILAI_PERALATAN").",
				   ".$this->getField("NILAI_KEUANGAN").", ".$this->getField("NILAI_SERTIFIKAT_LAIN").", ".$this->getField("LULUS_KUALIFIKASI").",
				   ".$this->getField("LULUS_PENAWARAN").", ".$this->getField("LULUS_PENAWARAN_URUT").", ".$this->getField("LULUS_ADMINISTRASI").",
				   ".$this->getField("LULUS_KEMAMPUAN_DASAR").", ".$this->getField("LULUS_KUALIFIKASI_REKANAN").", ".$this->getField("LULUS_KEWAJARAN_HARGA").",
				   ".$this->getField("STATUS_BAYAR").", ".$this->getField("DI_EMAIL").")
			";

		$this->query = $str;

		return $this->execQuery($str);
    }

	function insertUndang()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$this->setField("PAKET_REKANAN_ID", $this->getNextId("PAKET_REKANAN_ID","PAKET_REKANAN"));

		$str = "
				INSERT INTO PAKET_REKANAN (
				   PAKET_REKANAN_ID, PAKET_ID, REKANAN_ID,
				   TANGGAL_UNDANG, DI_EMAIL)
				VALUES (".$this->getField("PAKET_REKANAN_ID").", ".$this->getField("PAKET_ID").", ".$this->getField("REKANAN_ID").",
				   CURRENT_DATE, ".$this->getField("DI_EMAIL").")
			";
		// echo $str; die();
		$this->query = $str;

		return $this->execQuery($str);
    }

    // IKN 2019-08-30
    function insertUndang2()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$this->setField("PAKET_REKANAN_ID", $this->getNextId("PAKET_REKANAN_ID","PAKET_REKANAN"));

		$str = "
				INSERT INTO PAKET_REKANAN (
				   PAKET_REKANAN_ID, PAKET_ID, REKANAN_ID,
				   TANGGAL_UNDANG, TANGGAL_DAFTAR, DI_EMAIL,LULUS_PENDAFTARAN,LULUS_KUALIFIKASI,KODE_REKANAN)
				VALUES (".$this->getField("PAKET_REKANAN_ID").", ".$this->getField("PAKET_ID").", ".$this->getField("REKANAN_ID").",
				   NOW(), NOW(), ".$this->getField("DI_EMAIL").", ".$this->getField("LULUS_PENDAFTARAN").", ".$this->getField("LULUS_KUALIFIKASI").", '".$this->getField("KODE_REKANAN")."' || LPAD('".$this->getField("PAKET_REKANAN_ID")."', '6', '0'))
			";
		// echo $str; die();
		$this->query = $str;

		return $this->execQuery($str);
    }

	function insertUndangPengadaan()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$this->setField("PAKET_REKANAN_ID", $this->getNextId("PAKET_REKANAN_ID","PAKET_REKANAN"));

		$str = "
				INSERT INTO PAKET_REKANAN (
				   PAKET_REKANAN_ID, PAKET_ID, REKANAN_ID,
				   TANGGAL_UNDANG, TANGGAL_DAFTAR, LULUS_PENDAFTARAN, LULUS_KUALIFIKASI, DI_EMAIL)
				VALUES (".$this->getField("PAKET_REKANAN_ID").", ".$this->getField("PAKET_ID").", ".$this->getField("REKANAN_ID").",
				   CURRENT_DATE, CURRENT_DATE, 1, 1, ".$this->getField("DI_EMAIL").")
			";

		$this->query = $str;

		return $this->execQuery($str);
    }

	function insertDaftar()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$this->setField("PAKET_REKANAN_ID", $this->getNextId("PAKET_REKANAN_ID","PAKET_REKANAN"));

		$str = "
				INSERT INTO PAKET_REKANAN (
				   PAKET_REKANAN_ID, PAKET_ID, REKANAN_ID,
				   TANGGAL_DAFTAR, DI_EMAIL, KODE_REKANAN)
				VALUES (".$this->getField("PAKET_REKANAN_ID").", ".$this->getField("PAKET_ID").", ".$this->getField("REKANAN_ID").",
				   NOW(), 0, '".$this->getField("KODE_REKANAN")."' || LPAD('".$this->getField("PAKET_REKANAN_ID")."', '6', '0'))
			";

		$this->query = $str;
		return $this->execQuery($str);
    }

    function updateDaftar()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$str = "UPDATE PAKET_REKANAN  SET
					TANGGAL_DAFTAR = CURRENT_DATE,
					KODE_REKANAN = '".$this->getField("KODE_REKANAN")."' || LPAD(CAST(PAKET_REKANAN_ID AS TEXT), 6, '0'),
					DI_EMAIL = 0,
					LULUS_PENDAFTARAN = NULL,
					LULUS_PENDAFTARAN_KETERANGAN = NULL
				WHERE PAKET_ID = ".$this->getField("PAKET_ID")." AND REKANAN_ID = ".$this->getField("REKANAN_ID")."
				";
				$this->query = $str;
		return $this->execQuery($str);
    }

  function insertDaftar2()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$this->setField("PAKET_REKANAN_ID", $this->getNextId("PAKET_REKANAN_ID","PAKET_REKANAN"));

		$str = "
				INSERT INTO PAKET_REKANAN (
				   PAKET_REKANAN_ID, PAKET_ID, REKANAN_ID,
				   TANGGAL_DAFTAR, DI_EMAIL, KODE_REKANAN, LULUS_PENDAFTARAN, LULUS_KUALIFIKASI)
				VALUES (".$this->getField("PAKET_REKANAN_ID").", ".$this->getField("PAKET_ID").", ".$this->getField("REKANAN_ID").",
				   NOW(), 0, '".$this->getField("KODE_REKANAN")."' || LPAD('".$this->getField("PAKET_REKANAN_ID")."', '6', '0'), 1, 1)
			";

		$this->query = $str;
		return $this->execQuery($str);
  }

  function insertDaftar3()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$this->setField("PAKET_REKANAN_ID", $this->getNextId("PAKET_REKANAN_ID","PAKET_REKANAN"));

		$str = "
				INSERT INTO PAKET_REKANAN (
				   PAKET_REKANAN_ID, PAKET_ID, REKANAN_ID,
				   TANGGAL_DAFTAR, DI_EMAIL, KODE_REKANAN, LULUS_PENDAFTARAN)
				VALUES (".$this->getField("PAKET_REKANAN_ID").", ".$this->getField("PAKET_ID").", ".$this->getField("REKANAN_ID").",
				   NOW(), 0, '".$this->getField("KODE_REKANAN")."' || LPAD('".$this->getField("PAKET_REKANAN_ID")."', '6', '0'), 2)
			";

		$this->query = $str;
		return $this->execQuery($str);
  }

    function updateDaftar2()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$str = "UPDATE PAKET_REKANAN  SET
					TANGGAL_DAFTAR = CURRENT_DATE,
					KODE_REKANAN = '".$this->getField("KODE_REKANAN")."' || LPAD(CAST(PAKET_REKANAN_ID AS TEXT), 6, '0'),
					DI_EMAIL = 0,
					LULUS_PENDAFTARAN = NULL,
					LULUS_PENDAFTARAN_KETERANGAN = NULL
				WHERE PAKET_ID = ".$this->getField("PAKET_ID")." AND REKANAN_ID = ".$this->getField("REKANAN_ID")."
				";
				$this->query = $str;
		return $this->execQuery($str);
    }


    function update()
	{

		/*Auto-generate primary key(s) by next max value (integer) */
		$str = "UPDATE PAKET_REKANAN A SET
				  ".$this->getField("FIELD")." = ".$this->getField("FIELD_VALUE")."
				WHERE A.PAKET_REKANAN_ID = ".$this->getField("PAKET_REKANAN_ID")."
				";
				// echo $str; die();
				$this->query = $str;
		return $this->execQuery($str);
    }

    // ikn 20191216
    function updateIkn()
	{

		/*Auto-generate primary key(s) by next max value (integer) */
		$str = "UPDATE PAKET_REKANAN SET
				  ".$this->getField("FIELD")." = ".$this->getField("FIELD_VALUE").", LAST_DATE = CURRENT_TIMESTAMP
				WHERE PAKET_REKANAN_ID = ".$this->getField("PAKET_REKANAN_ID")."
				";
				// echo $str; die();
				$this->query = $str;
		return $this->execQuery($str);
    }

    function updateByRekananPaket()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$str = "UPDATE PAKET_REKANAN A SET
				  ".$this->getField("FIELD")." = '".$this->getField("FIELD_VALUE")."'
				WHERE REKANAN_ID = ".$this->getField("REKANAN_ID")." AND
					  PAKET_ID  = ".$this->getField("PAKET_ID")."
				";
				$this->query = $str;
		//echo $str;
		return $this->execQuery($str);
    }

  // ikn 27082023
  function updateLulusKualifikasiPra()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$str = "UPDATE PAKET_REKANAN A SET
				  LULUS_KUALIFIKASI_PRA = '".$this->getField("LULUS_KUALIFIKASI_PRA")."'
				WHERE PAKET_REKANAN_ID = ".$this->getField("PAKET_REKANAN_ID")."
				";
				$this->query = $str;
		//echo $str;
		return $this->execQuery($str);
  }

	function updateTwoField()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$str = "UPDATE PAKET_REKANAN A SET
				  ".$this->getField("FIELD1")." = ".$this->getField("FIELD1_VALUE").",
				  ".$this->getField("FIELD2")." = '".$this->getField("FIELD2_VALUE")."',
				  LAST_DATE = CURRENT_TIMESTAMP,
				  LAST_BY = ".$this->USER_LOGIN_ID."
				WHERE PAKET_REKANAN_ID = ".$this->getField("PAKET_REKANAN_ID")."
				";
				// echo $str;
				$this->query = $str;
		return $this->execQuery($str);
    }

   function updateOneField()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$str = "UPDATE PAKET_REKANAN A SET
				  ".$this->getField("FIELD1")." = ".$this->getField("FIELD1_VALUE").",
				  LAST_DATE = CURRENT_TIMESTAMP,
				  LAST_BY = ".$this->USER_LOGIN_ID."
				WHERE PAKET_REKANAN_ID = ".$this->getField("PAKET_REKANAN_ID")."
				";
				// echo $str;
				$this->query = $str;
		return $this->execQuery($str);
    }

	 function updatePenawaran()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$str = "UPDATE PAKET_REKANAN  SET
					KIRIM_PENAWARAN = 1,
					KIRIM_PENAWARAN_TANGGAL = CURRENT_TIMESTAMP,
					NILAI_PENAWARAN = ".$this->getField("NILAI_PENAWARAN").",
					LAST_DATE = CURRENT_TIMESTAMP,
				  LAST_BY = ".$this->USER_LOGIN_ID."
				WHERE PAKET_ID = ".$this->getField("PAKET_ID")." AND REKANAN_ID = ".$this->getField("REKANAN_ID")."
				";
				// echo $str; die();
				$this->query = $str;

		return $this->execQuery($str);
    }

	function updatePenawaranRincian()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$str = "UPDATE PAKET_REKANAN A SET
					KIRIM_PENAWARAN = 1,
					KIRIM_PENAWARAN_TANGGAL = CURRENT_TIMESTAMP,
					NILAI_PENAWARAN = (SELECT SUM(JUMLAH_KOREKSI) FROM REKANAN_PAKET_PENAWARAN X WHERE X.PAKET_REKANAN_ID=A.PAKET_REKANAN_ID),
					LAST_DATE = CURRENT_TIMESTAMP,
				  LAST_BY = ".$this->USER_LOGIN_ID."
				WHERE PAKET_ID = ".$this->getField("PAKET_ID")." AND REKANAN_ID = ".$this->getField("REKANAN_ID")."
				";
				$this->query = $str;

		return $this->execQuery($str);
    }

	 function updateByPaket()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$str = "UPDATE PAKET_REKANAN A SET
				  ".$this->getField("FIELD")." = '".$this->getField("FIELD_VALUE")."'
				WHERE PAKET_ID  = ".$this->getField("PAKET_ID")."
				";
				$this->query = $str;
		// echo $str; die();
		return $this->execQuery($str);
    }

	function delete()
	{
        $str = "DELETE FROM PAKET_REKANAN
                WHERE
                  PAKET_REKANAN_ID = ".$this->getField("PAKET_REKANAN_ID")."";

		$this->query = $str;
        return $this->execQuery($str);
    }

	function deleteByPaket()
	{
        $str = "DELETE FROM PAKET_REKANAN
                WHERE
                  PAKET_ID = ".$this->getField("PAKET_ID")."";

		$this->query = $str;
        return $this->execQuery($str);
    }

	function deleteByPaketNew()
	{
        $str = "DELETE FROM PAKET_REKANAN
                WHERE
                  PAKET_ID = ".$this->getField("PAKET_ID")." AND REKANAN_ID NOT IN (".$this->getField("REKANAN_ID").")
				";

		$this->query = $str;
		//echo $str;
        return $this->execQuery($str);
    }

    /**
    * Cari record berdasarkan array parameter dan limit tampilan
    * @param array paramsArray Array of parameter. Contoh array("id"=>"xxx","NAMA"=>"yyy")
    * @param int limit Jumlah maksimal record yang akan diambil
    * @param int from Awal record yang diambil
    * @return boolean True jika sukses, false jika tidak
    **/



    function selectByParamsEmail($paramsArray=array(),$limit=-1,$from=-1, $statement='')
	{
		$str = " SELECT A.PAKET_REKANAN_ID, EMAIL, LULUS_KUALIFIKASI, LULUS_KUALIFIKASI_KETERANGAN, NAMA, WHATSAPP, A.REKANAN_ID, A.KODE_REKANAN, A.TANGGAL_DAFTAR,
				TO_CHAR(A.TANGGAL_DAFTAR, 'DDMMYYYY') TANGGAL_DAFTAR_ENKRIPSI
				FROM PAKET_REKANAN A, REKANAN B WHERE PAKET_REKANAN_ID IS NOT NULL AND A.REKANAN_ID = B.REKANAN_ID ";

		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}

		$this->query = $str;
		$str .= $statement." ORDER BY PAKET_REKANAN_ID ASC";

		return $this->selectLimit($str,$limit,$from);
    }


    function selectByParams($paramsArray=array(),$limit=-1,$from=-1, $statement='', $paketId='', $order='ORDER BY PAKET_REKANAN_ID ASC')
	{
		$str = "
				SELECT REPLACE(B.KODE,SUBSTR(B.KODE, 10),'*********') KODE_CUT, A.PAKET_REKANAN_ID, PAKET_ID, A.REKANAN_ID, B.NAMA REKANAN,
			   (SELECT X.NAMA FROM REKANAN_TIPE X WHERE X.REKANAN_TIPE_ID = B.REKANAN_TIPE_ID)|| '. ' || B.NAMA FULL_NAMA_REKANAN,
			   (SELECT X.NAMA FROM PAKET_METODE_LELANG X WHERE X.PAKET_METODE_LELANG_ID = (SELECT PAKET_METODE_LELANG_ID FROM PAKET X WHERE X.PAKET_ID = A.PAKET_ID)) PROSES_PEMILIHAN,
			   (SELECT NAMA FROM PAKET X WHERE X.PAKET_ID = A.PAKET_ID) NAMA_PEKERJAAN,
				   TO_CHAR(TANGGAL_UNDANG, 'YYYY-MM-DD') TANGGAL_UNDANG, TO_CHAR(TANGGAL_UNDANG, 'HH24:MI:SS') JAM_UNDANG,  TO_CHAR(A.TANGGAL_DAFTAR, 'YYYY-MM-DD') TANGGAL_DAFTAR, TO_CHAR(A.TANGGAL_DAFTAR, 'HH24:MI:SS') JAM_DAFTAR, NILAI_PENAWARAN,
				   C.UNIT_PRICE, C.JUMLAH,
					C.UNIT_PRICE_KOREKSI,
					C.JUMLAH_KOREKSI,
				   KODE_REKANAN, PAKTA, SPM,
				   NILAI_PENGALAMAN, NILAI_PERSONIL, NILAI_PERALATAN,
				   NILAI_KEUANGAN, NILAI_SERTIFIKAT_LAIN, LULUS_KUALIFIKASI,
				   LULUS_PENAWARAN, LULUS_PENAWARAN_URUT, LULUS_ADMINISTRASI,
				   LULUS_KEMAMPUAN_DASAR, LULUS_KUALIFIKASI_REKANAN, LULUS_KEWAJARAN_HARGA,
				   STATUS_BAYAR, DI_EMAIL, DI_EMAIL_NEGOSIASI, DI_EMAIL_NEGOSIASI_2, B.ALAMAT, B.EMAIL, B.NPWP, LULUS_KUALIFIKASI_KETERANGAN, COALESCE(LULUS_PENDAFTARAN, 2) LULUS_PENDAFTARAN, LULUS_PENDAFTARAN_KETERANGAN,
				   B.WHATSAPP, B.KODE, A.AANWIJZING, A.LULUS_PENAWARAN_KETERANGAN, A.HADIR_PEMBUKAAN_PENAWARAN, A.HADIR_PEMBUKAAN_PENAWARAN2,
				   A.KIRIM_PENAWARAN_PASSWORD, TO_CHAR(A.KIRIM_PENAWARAN_TANGGAL, 'DD-MM-YYYY HH24:MI') KIRIM_PENAWARAN_TANGGAL,
				   A.KIRIM_PENAWARAN_PASSWORD2,
				   A.KIRIM_PENAWARAN_LENGKAP, A.KIRIM_PENAWARAN_LENGKAP2,
				   A.KIRIM_PENAWARAN_ALASAN, A.KIRIM_PENAWARAN_ALASAN2,
				   A.NILAI_PENAWARAN_SEBELUMNYA,A.NILAI_PENAWARAN, A.DI_EMAIL_PEMENANG, A.LULUS_KUALIFIKASI_PRA
				FROM PAKET_REKANAN A
				LEFT JOIN REKANAN B ON A.REKANAN_ID = B.REKANAN_ID
				LEFT JOIN REKANAN_PAKET_PENAWARAN C ON A.PAKET_REKANAN_ID=C.PAKET_REKANAN_ID
				WHERE
				A.PAKET_REKANAN_ID IS NOT NULL
				";

		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}

		$str .= $statement." ".$order;
		// echo $str; die();
		$this->query = $str;
		return $this->selectLimit($str,$limit,$from);
    }

    function selectByParams2($paramsArray=array(),$limit=-1,$from=-1, $statement='', $paketId='', $order='ORDER BY UNIT_PRICE ASC')
	{
		$str = "
				SELECT A.PAKET_REKANAN_ID, PAKET_ID, A.REKANAN_ID, B.NAMA REKANAN,
			   (SELECT X.NAMA FROM REKANAN_TIPE X WHERE X.REKANAN_TIPE_ID = B.REKANAN_TIPE_ID)|| '. ' || B.NAMA FULL_NAMA_REKANAN,
			   (SELECT X.NAMA FROM PAKET_METODE_LELANG X WHERE X.PAKET_METODE_LELANG_ID = (SELECT PAKET_METODE_LELANG_ID FROM PAKET X WHERE X.PAKET_ID = A.PAKET_ID)) PROSES_PEMILIHAN,
			   (SELECT NAMA FROM PAKET X WHERE X.PAKET_ID = A.PAKET_ID) NAMA_PEKERJAAN,
				   TO_CHAR(TANGGAL_UNDANG, 'YYYY-MM-DD') TANGGAL_UNDANG, TO_CHAR(TANGGAL_UNDANG, 'HH24:MI:SS') JAM_UNDANG,  TO_CHAR(A.TANGGAL_DAFTAR, 'YYYY-MM-DD') TANGGAL_DAFTAR, TO_CHAR(A.TANGGAL_DAFTAR, 'HH24:MI:SS') JAM_DAFTAR, NILAI_PENAWARAN,
				   C.UNIT_PRICE,
					C.UNIT_PRICE_KOREKSI,
					C.JUMLAH_KOREKSI,
				   KODE_REKANAN, PAKTA, SPM,
				   NILAI_PENGALAMAN, NILAI_PERSONIL, NILAI_PERALATAN,
				   NILAI_KEUANGAN, NILAI_SERTIFIKAT_LAIN, LULUS_KUALIFIKASI,
				   LULUS_PENAWARAN, LULUS_PENAWARAN_URUT, LULUS_ADMINISTRASI,
				   LULUS_KEMAMPUAN_DASAR, LULUS_KUALIFIKASI_REKANAN, LULUS_KEWAJARAN_HARGA,
				   STATUS_BAYAR, DI_EMAIL, DI_EMAIL_NEGOSIASI, B.ALAMAT, B.EMAIL, B.NPWP, LULUS_KUALIFIKASI_KETERANGAN, COALESCE(LULUS_PENDAFTARAN, 2) LULUS_PENDAFTARAN, LULUS_PENDAFTARAN_KETERANGAN,
				   B.WHATSAPP, B.KODE, A.AANWIJZING, A.LULUS_PENAWARAN_KETERANGAN, A.HADIR_PEMBUKAAN_PENAWARAN,
				   A.KIRIM_PENAWARAN_PASSWORD, TO_CHAR(A.KIRIM_PENAWARAN_TANGGAL, 'DD-MM-YYYY HH24:MI') KIRIM_PENAWARAN_TANGGAL,
				   A.KIRIM_PENAWARAN_PASSWORD2,
				   A.KIRIM_PENAWARAN_LENGKAP, A.KIRIM_PENAWARAN_LENGKAP2,
				   A.KIRIM_PENAWARAN_ALASAN, A.KIRIM_PENAWARAN_ALASAN2,
				   A.NILAI_PENAWARAN_SEBELUMNYA,A.NILAI_PENAWARAN, A.DI_EMAIL_PEMENANG, C.PAKET_PENAWARAN_ID
				FROM PAKET_REKANAN A
				LEFT JOIN REKANAN B ON A.REKANAN_ID = B.REKANAN_ID
				LEFT JOIN REKANAN_PAKET_PENAWARAN C ON A.PAKET_REKANAN_ID=C.PAKET_REKANAN_ID
				WHERE
				A.PAKET_REKANAN_ID IS NOT NULL
				";

		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}

		$str .= $statement." ".$order;
		// echo $str; die();
		$this->query = $str;
		return $this->selectLimit($str,$limit,$from);
    }

    function selectByParams3($paramsArray=array(),$limit=-1,$from=-1, $statement='', $paketId='', $order='')
	{
		$str = "
				SELECT A.PAKET_REKANAN_ID, PAKET_ID, A.REKANAN_ID, B.NAMA REKANAN,
			   (SELECT X.NAMA FROM REKANAN_TIPE X WHERE X.REKANAN_TIPE_ID = B.REKANAN_TIPE_ID)|| '. ' || B.NAMA FULL_NAMA_REKANAN,
			   (SELECT X.NAMA FROM PAKET_METODE_LELANG X WHERE X.PAKET_METODE_LELANG_ID = (SELECT PAKET_METODE_LELANG_ID FROM PAKET X WHERE X.PAKET_ID = A.PAKET_ID)) PROSES_PEMILIHAN,
			   (SELECT NAMA FROM PAKET X WHERE X.PAKET_ID = A.PAKET_ID) NAMA_PEKERJAAN,
				   TO_CHAR(TANGGAL_UNDANG, 'YYYY-MM-DD') TANGGAL_UNDANG, TO_CHAR(TANGGAL_UNDANG, 'HH24:MI:SS') JAM_UNDANG,  TO_CHAR(A.TANGGAL_DAFTAR, 'YYYY-MM-DD') TANGGAL_DAFTAR, TO_CHAR(A.TANGGAL_DAFTAR, 'HH24:MI:SS') JAM_DAFTAR, NILAI_PENAWARAN,
				   C.UNIT_PRICE,
					C.UNIT_PRICE_KOREKSI,
					C.JUMLAH_KOREKSI,
				   KODE_REKANAN, PAKTA, SPM,
				   NILAI_PENGALAMAN, NILAI_PERSONIL, NILAI_PERALATAN,
				   NILAI_KEUANGAN, NILAI_SERTIFIKAT_LAIN, LULUS_KUALIFIKASI,
				   LULUS_PENAWARAN, LULUS_PENAWARAN_URUT, LULUS_ADMINISTRASI,
				   LULUS_KEMAMPUAN_DASAR, LULUS_KUALIFIKASI_REKANAN, LULUS_KEWAJARAN_HARGA,
				   STATUS_BAYAR, DI_EMAIL, DI_EMAIL_NEGOSIASI, B.ALAMAT, B.EMAIL, B.NPWP, LULUS_KUALIFIKASI_KETERANGAN, COALESCE(LULUS_PENDAFTARAN, 2) LULUS_PENDAFTARAN, LULUS_PENDAFTARAN_KETERANGAN,
				   B.WHATSAPP, B.KODE, A.AANWIJZING, A.LULUS_PENAWARAN_KETERANGAN, A.HADIR_PEMBUKAAN_PENAWARAN,
				   A.KIRIM_PENAWARAN_PASSWORD, TO_CHAR(A.KIRIM_PENAWARAN_TANGGAL, 'DD-MM-YYYY HH24:MI') KIRIM_PENAWARAN_TANGGAL,
				   A.KIRIM_PENAWARAN_PASSWORD2,
				   A.KIRIM_PENAWARAN_LENGKAP, A.KIRIM_PENAWARAN_LENGKAP2,
				   A.KIRIM_PENAWARAN_ALASAN, A.KIRIM_PENAWARAN_ALASAN2,
				   A.NILAI_PENAWARAN_SEBELUMNYA,A.NILAI_PENAWARAN, A.DI_EMAIL_PEMENANG, C.PAKET_PENAWARAN_ID
				FROM PAKET_REKANAN A
				LEFT JOIN REKANAN B ON A.REKANAN_ID = B.REKANAN_ID
				LEFT JOIN REKANAN_PAKET_PENAWARAN C ON A.PAKET_REKANAN_ID=C.PAKET_REKANAN_ID
				WHERE
				A.PAKET_REKANAN_ID IS NOT NULL
				";

		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}

		$str .= $statement." ".$order;
		// echo $str; die();
		$this->query = $str;
		return $this->selectLimit($str,$limit,$from);
  }

  function selectByParams4($paramsArray=array(),$limit=-1,$from=-1, $statement='', $paketId='', $order='') // dengan nilai negosiasi
	{
		$str = "
				SELECT A.PAKET_REKANAN_ID, A.PAKET_ID, A.REKANAN_ID, B.NAMA REKANAN,
			   (SELECT X.NAMA FROM REKANAN_TIPE X WHERE X.REKANAN_TIPE_ID = B.REKANAN_TIPE_ID)|| '. ' || B.NAMA FULL_NAMA_REKANAN,
			   (SELECT X.NAMA FROM PAKET_METODE_LELANG X WHERE X.PAKET_METODE_LELANG_ID = (SELECT PAKET_METODE_LELANG_ID FROM PAKET X WHERE X.PAKET_ID = A.PAKET_ID)) PROSES_PEMILIHAN,
			   (SELECT NAMA FROM PAKET X WHERE X.PAKET_ID = A.PAKET_ID) NAMA_PEKERJAAN,
				   TO_CHAR(TANGGAL_UNDANG, 'YYYY-MM-DD') TANGGAL_UNDANG, TO_CHAR(TANGGAL_UNDANG, 'HH24:MI:SS') JAM_UNDANG,  TO_CHAR(A.TANGGAL_DAFTAR, 'YYYY-MM-DD') TANGGAL_DAFTAR, TO_CHAR(A.TANGGAL_DAFTAR, 'HH24:MI:SS') JAM_DAFTAR, NILAI_PENAWARAN,
				   C.UNIT_PRICE,
					C.UNIT_PRICE_KOREKSI,
					C.JUMLAH_KOREKSI,
				   KODE_REKANAN, PAKTA, SPM,
				   NILAI_PENGALAMAN, NILAI_PERSONIL, NILAI_PERALATAN,
				   NILAI_KEUANGAN, NILAI_SERTIFIKAT_LAIN, LULUS_KUALIFIKASI,
				   LULUS_PENAWARAN, LULUS_PENAWARAN_URUT, LULUS_ADMINISTRASI,
				   LULUS_KEMAMPUAN_DASAR, LULUS_KUALIFIKASI_REKANAN, LULUS_KEWAJARAN_HARGA,
				   STATUS_BAYAR, DI_EMAIL, DI_EMAIL_NEGOSIASI, B.ALAMAT, B.EMAIL, B.NPWP, LULUS_KUALIFIKASI_KETERANGAN, COALESCE(LULUS_PENDAFTARAN, 2) LULUS_PENDAFTARAN, LULUS_PENDAFTARAN_KETERANGAN,
				   B.WHATSAPP, B.KODE, A.AANWIJZING, A.LULUS_PENAWARAN_KETERANGAN, A.HADIR_PEMBUKAAN_PENAWARAN,
				   A.KIRIM_PENAWARAN_PASSWORD, TO_CHAR(A.KIRIM_PENAWARAN_TANGGAL, 'DD-MM-YYYY HH24:MI') KIRIM_PENAWARAN_TANGGAL,
				   A.KIRIM_PENAWARAN_PASSWORD2,
				   A.KIRIM_PENAWARAN_LENGKAP, A.KIRIM_PENAWARAN_LENGKAP2,
				   A.KIRIM_PENAWARAN_ALASAN, A.KIRIM_PENAWARAN_ALASAN2,
				   A.NILAI_PENAWARAN_SEBELUMNYA,A.NILAI_PENAWARAN, A.DI_EMAIL_PEMENANG, C.PAKET_PENAWARAN_ID, ((D.UNIT_PRICE * D.QUANTITY)) NILAI_NEGOSIASI
				FROM PAKET_REKANAN A
				LEFT JOIN REKANAN B ON A.REKANAN_ID = B.REKANAN_ID
				LEFT JOIN REKANAN_PAKET_PENAWARAN C ON A.PAKET_REKANAN_ID=C.PAKET_REKANAN_ID
				LEFT JOIN PAKET_NEGOSIASI D ON C.PAKET_PENAWARAN_ID=D.PAKET_PENAWARAN_ID
				WHERE
				A.PAKET_REKANAN_ID IS NOT NULL
				";

		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}

		$str .= $statement." ".$order;
		// echo $str; die();
		$this->query = $str;
		return $this->selectLimit($str,$limit,$from);
  }

    // IKN 2019-08-30
    function selectByParamsPenunjukanLang($paramsArray=array(),$limit=-1,$from=-1, $statement='', $paketId='', $order='ORDER BY PAKET_REKANAN_ID ASC')
	{
		$str = "
				SELECT A.PAKET_REKANAN_ID, PAKET_ID, A.REKANAN_ID, B.NAMA REKANAN,
			   (SELECT X.NAMA FROM REKANAN_TIPE X WHERE X.REKANAN_TIPE_ID = B.REKANAN_TIPE_ID)|| '. ' || B.NAMA FULL_NAMA_REKANAN,
			   (SELECT X.NAMA FROM PAKET_METODE_LELANG X WHERE X.PAKET_METODE_LELANG_ID = (SELECT PAKET_METODE_LELANG_ID FROM PAKET X WHERE X.PAKET_ID = A.PAKET_ID)) PROSES_PEMILIHAN,
			   (SELECT NAMA FROM PAKET X WHERE X.PAKET_ID = A.PAKET_ID) NAMA_PEKERJAAN,
				   TO_CHAR(TANGGAL_UNDANG, 'YYYY-MM-DD') TANGGAL_UNDANG,  TO_CHAR(A.TANGGAL_DAFTAR, 'YYYY-MM-DD') TANGGAL_DAFTAR, NILAI_PENAWARAN,
				   KODE_REKANAN, PAKTA, SPM,
				   NILAI_PENGALAMAN, NILAI_PERSONIL, NILAI_PERALATAN,
				   NILAI_KEUANGAN, NILAI_SERTIFIKAT_LAIN, LULUS_KUALIFIKASI,
				   LULUS_PENAWARAN, LULUS_PENAWARAN_URUT, LULUS_ADMINISTRASI,
				   LULUS_KEMAMPUAN_DASAR, LULUS_KUALIFIKASI_REKANAN, LULUS_KEWAJARAN_HARGA,
				   STATUS_BAYAR, DI_EMAIL, DI_EMAIL_NEGOSIASI, B.ALAMAT, B.EMAIL, B.NPWP, LULUS_KUALIFIKASI_KETERANGAN, COALESCE(LULUS_PENDAFTARAN, 2) LULUS_PENDAFTARAN, LULUS_PENDAFTARAN_KETERANGAN,
				   B.WHATSAPP, B.KODE, A.AANWIJZING, A.LULUS_PENAWARAN_KETERANGAN, A.HADIR_PEMBUKAAN_PENAWARAN,
				   A.KIRIM_PENAWARAN_PASSWORD, TO_CHAR(A.KIRIM_PENAWARAN_TANGGAL, 'DD-MM-YYYY HH24:MI') KIRIM_PENAWARAN_TANGGAL,
				   A.KIRIM_PENAWARAN_PASSWORD2,
				   A.KIRIM_PENAWARAN_LENGKAP, A.KIRIM_PENAWARAN_LENGKAP2,
				   A.KIRIM_PENAWARAN_ALASAN, A.KIRIM_PENAWARAN_ALASAN2,
				   A.NILAI_PENAWARAN_SEBELUMNYA, A.DI_EMAIL_PEMENANG
				FROM PAKET_REKANAN A, REKANAN B
				WHERE A.PAKET_REKANAN_ID IS NOT NULL AND A.REKANAN_ID = B.REKANAN_ID
				";

		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}

		$str .= $statement." ".$order;
		// echo $str; die();
		$this->query = $str;
		return $this->selectLimit($str,$limit,$from);
    }

     function selectByParamsSimple($paramsArray=array(),$limit=-1,$from=-1, $statement='', $order=' ORDER BY PAKET_REKANAN_ID ASC ')
	{
		$str = "SELECT PAKET_REKANAN_ID, KODE_REKANAN, KIRIM_PENAWARAN_KODE, BIDDING_RESET, NILAI_PENAWARAN
				FROM PAKET_REKANAN A WHERE 1 = 1 ";

		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}

		$str .= $statement." ".$order;

		$this->query = $str;
		return $this->selectLimit($str,$limit,$from);
    }


    function selectByParamsPaketLelang($paramsArray=array(),$limit=-1,$from=-1, $statement='')
	{
		$str = "SELECT PAKET_REKANAN_ID, TANGGAL_DAFTAR, KODE_REKANAN, AANWIJZING, COALESCE(LULUS_KUALIFIKASI, 1) LULUS_KUALIFIKASI,
				LULUS_KUALIFIKASI_KETERANGAN, COALESCE(LULUS_PENDAFTARAN, 2) LULUS_PENDAFTARAN, LULUS_PENDAFTARAN_KETERANGAN,
				COALESCE(KIRIM_PENAWARAN, 0) KIRIM_PENAWARAN, KIRIM_PENAWARAN_TANGGAL, KIRIM_PENAWARAN_KODE, KIRIM_PENAWARAN_PASSWORD,
				TO_CHAR(TANGGAL_DAFTAR, 'DDMMYYYY') TANGGAL_DAFTAR_ENKRIPSI, KIRIM_PENAWARAN_LENGKAP, KIRIM_PENAWARAN_LENGKAP2, KIRIM_PENAWARAN_ALASAN,
				KIRIM_PENAWARAN_ALASAN2, LULUS_PENAWARAN_SAMPUL1, KIRIM_PENAWARAN_PASSWORD2
				FROM PAKET_REKANAN A WHERE PAKET_REKANAN_ID IS NOT NULL ";

		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}

		$this->query = $str;
		$str .= $statement." ORDER BY PAKET_REKANAN_ID ASC";

		return $this->selectLimit($str,$limit,$from);
    }

    function selectByParamsPaketLelangV2($paramsArray=array(),$limit=-1,$from=-1, $statement='')
	{
		$str = "SELECT PAKET_REKANAN_ID, TO_CHAR(TANGGAL_DAFTAR, 'YYYY-MM-DD') TANGGAL_DAFTAR, KODE_REKANAN, AANWIJZING,
                CASE WHEN PAKET_METODE_KUALIFIKASI_ID = 1 THEN COALESCE(LULUS_KUALIFIKASI, 0) ELSE COALESCE(LULUS_KUALIFIKASI, 1) END LULUS_KUALIFIKASI,
				LULUS_KUALIFIKASI_KETERANGAN, COALESCE(LULUS_PENDAFTARAN, 2) LULUS_PENDAFTARAN, LULUS_PENDAFTARAN_KETERANGAN,
				COALESCE(KIRIM_PENAWARAN, 0) KIRIM_PENAWARAN, KIRIM_PENAWARAN_TANGGAL, KIRIM_PENAWARAN_KODE, KIRIM_PENAWARAN_PASSWORD,
				TO_CHAR(TANGGAL_DAFTAR, 'DDMMYYYY') TANGGAL_DAFTAR_ENKRIPSI, KIRIM_PENAWARAN_LENGKAP, KIRIM_PENAWARAN_LENGKAP2, KIRIM_PENAWARAN_ALASAN,
				KIRIM_PENAWARAN_ALASAN2, LULUS_PENAWARAN_SAMPUL1, KIRIM_PENAWARAN_PASSWORD2
				FROM PAKET_REKANAN A INNER JOIN PAKET B ON A.PAKET_ID = B.PAKET_ID WHERE PAKET_REKANAN_ID IS NOT NULL ";

		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}

		$this->query = $str;
		$str .= $statement." ORDER BY PAKET_REKANAN_ID ASC";

		return $this->selectLimit($str,$limit,$from);
    }

	function selectUrutPenawaran($paramsArray=array(),$limit=-1,$from=-1, $statement='')
	{
		$str = "SELECT   PAKET_REKANAN_ID, KODE_REKANAN, NILAI_PENAWARAN, C.NAMA, (B.NILAI - NILAI_PENAWARAN) NILAI_URUT, A.REKANAN_ID, A.DI_EMAIL, DI_EMAIL_NEGOSIASI_2,
					(SELECT X.NAMA FROM REKANAN_TIPE X WHERE X.REKANAN_TIPE_ID = C.REKANAN_TIPE_ID)|| '. ' || C.NAMA FULL_NAMA_REKANAN
					FROM PAKET_REKANAN A INNER JOIN PAKET B ON A.PAKET_ID = B.PAKET_ID
					INNER JOIN REKANAN C ON A.REKANAN_ID=C.REKANAN_ID
				   WHERE 1 = 1
				 ";

		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}
		// echo $str; die();
		$this->query = $str;
		$str .= $statement." ORDER BY NILAI_PENAWARAN ASC, KIRIM_PENAWARAN_TANGGAL ASC ";


		return $this->selectLimit($str,$limit,$from);
    }

    // ikn 20190910
    function selectUrutPenawaran2($paramsArray=array(),$limit=-1,$from=-1, $statement='')
	{
		$str = "SELECT   PAKET_REKANAN_ID, KODE_REKANAN, A.REKANAN_ID, NILAI_PENAWARAN, (B.NILAI - NILAI_PENAWARAN) NILAI_URUT, D.NAMA || ' ' || C.NAMA NAMA
					FROM PAKET_REKANAN A INNER JOIN PAKET B ON A.PAKET_ID = B.PAKET_ID
					INNER JOIN REKANAN C ON A.REKANAN_ID = C.REKANAN_ID
					LEFT JOIN REKANAN_TIPE D ON  C.REKANAN_TIPE_ID = D.REKANAN_TIPE_ID
				   WHERE 1 = 1
				 ";

		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}

		$this->query = $str;
		$str .= $statement." ORDER BY NILAI_PENAWARAN ASC, KIRIM_PENAWARAN_TANGGAL ASC ";


		return $this->selectLimit($str,$limit,$from);
    }

    function selectByParamsPaketLelangPengadaan($paramsArray=array(),$limit=-1,$from=-1, $statement='')
	{
		$str = "SELECT PAKET_REKANAN_ID, NILAI_PENAWARAN, TANGGAL_DAFTAR, KODE_REKANAN, AANWIJZING,
				LULUS_KUALIFIKASI LULUS_KUALIFIKASI, LULUS_KUALIFIKASI_KETERANGAN, COALESCE(LULUS_PENDAFTARAN, 2) LULUS_PENDAFTARAN, LULUS_PENDAFTARAN_KETERANGAN,
				COALESCE(KIRIM_PENAWARAN, 0) KIRIM_PENAWARAN
				FROM PAKET_REKANAN A WHERE PAKET_REKANAN_ID IS NOT NULL ";

		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}

		$this->query = $str;
		$str .= $statement." ORDER BY PAKET_REKANAN_ID ASC";

		return $this->selectLimit($str,$limit,$from);
    }

    function selectByParamsPembukaanAuction($paket_id, $paket_rekanan_id)
	{
		$str = "
				SELECT
				(SELECT SUM (ROWCOUNT) FROM
				(SELECT SUM(STATUS) AS ROWCOUNT FROM REKANAN_EVAL_ADMIN_TAWAR WHERE REKANAN_EVAL_ADMIN_TAWAR_ID IS NOT NULL AND PAKET_REKANAN_ID = ".$paket_rekanan_id." AND STATUS = 1
				UNION ALL
				SELECT SUM(STATUS) AS ROWCOUNT FROM REKANAN_EVAL_TEKNIS_TAWAR WHERE REKANAN_EVAL_TEKNIS_TAWAR_ID IS NOT NULL AND PAKET_REKANAN_ID = ".$paket_rekanan_id."  AND STATUS = 1) A) REKANAN,
				(SELECT SUM (ROWCOUNT) FROM
				(SELECT COUNT(PAKET_ID) AS ROWCOUNT FROM PAKET_EVAL_ADMIN_TAWAR WHERE PAKET_EVAL_ADMIN_TAWAR_ID IS NOT NULL AND PAKET_ID = ".$paket_id."
				UNION ALL
				SELECT COUNT(PAKET_ID) AS ROWCOUNT FROM PAKET_EVAL_TEKNIS_TAWAR WHERE PAKET_EVAL_TEKNIS_TAWAR_ID IS NOT NULL AND PAKET_ID = ".$paket_id.") A) PAKET
				 FROM DUAL
		 ";

		$this->query = $str;

		return $this->selectLimit($str,-1,-1);
    }

    function selectNilaiPenawaran($paramsArray=array(),$limit=-1,$from=-1, $statement='')
	{
		$str = "SELECT   PAKET_REKANAN_ID, NILAI_PENAWARAN
					FROM PAKET_REKANAN
				   WHERE TANGGAL_DAFTAR IS NOT NULL
					 AND NOT COALESCE (NILAI_PENAWARAN, 0) = 0
				 ";

		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}
		// echo $str; die();
		$this->query = $str;
		$str .= $statement." ORDER BY NILAI_PENAWARAN ASC ";


		return $this->selectLimit($str,$limit,$from);
    }

    // IKN 2019.08.29
    function selectNilaiPenawaran2($paramsArray=array(),$limit=-1,$from=-1, $statement='')
	{
		$str = "SELECT
					a.PAKET_REKANAN_ID,
					a.NILAI_PENAWARAN,
					b.JUMLAH_KOREKSI
				FROM
					PAKET_REKANAN a INNER JOIN
					REKANAN_PAKET_PENAWARAN b
					ON a.PAKET_REKANAN_ID=b.PAKET_REKANAN_ID
				WHERE
					a.TANGGAL_DAFTAR IS NOT NULL
					AND NOT COALESCE (a.NILAI_PENAWARAN, 0 ) = 0
				 ";

		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}
		// echo $str; die();
		$this->query = $str;
		$str .= $statement." ORDER BY B.JUMLAH_KOREKSI ASC ";


		return $this->selectLimit($str,$limit,$from);
    }

    function selectNilaiPenawaran3($paramsArray=array(),$limit=-1,$from=-1, $statement='')
	{
		$str = "SELECT
					a.PAKET_REKANAN_ID,
					a.NILAI_PENAWARAN,
					b.JUMLAH_KOREKSI
				FROM
					PAKET_REKANAN a INNER JOIN
					REKANAN_PAKET_PENAWARAN b
					ON a.PAKET_REKANAN_ID=b.PAKET_REKANAN_ID
				WHERE
					a.TANGGAL_DAFTAR IS NOT NULL
					AND NOT COALESCE (a.NILAI_PENAWARAN, 0 ) = 0
				 ";

		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}
		// echo $str; die();
		$this->query = $str;
		$str .= $statement." ORDER BY A.NILAI_PENAWARAN ASC ";


		return $this->selectLimit($str,$limit,$from);
    }

    function selectNilaiPenawaran4($paramsArray=array(),$limit=-1,$from=-1, $statement='')
	{
		$str = "SELECT
					PAKET_REKANAN_ID
				FROM view_sistem_nilai
				WHERE 1=1
				 ";

		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}
		// echo $str; die();
		$this->query = $str;
		// $str .= $statement." ORDER BY A.NILAI_PENAWARAN ASC ";


		return $this->selectLimit($str,$limit,$from);
    }

	function selectByParamsLike($paramsArray=array(),$limit=-1,$from=-1, $statement='')
	{
		$str = "SELECT  PAKET_REKANAN_ID, PAKET_ID, REKANAN_ID,
				   TANGGAL_UNDANG, TANGGAL_DAFTAR, NILAI_PENAWARAN,
				   KODE_REKANAN, PAKTA, SPM,
				   NILAI_PENGALAMAN, NILAI_PERSONIL, NILAI_PERALATAN,
				   NILAI_KEUANGAN, NILAI_SERTIFIKAT_LAIN, LULUS_KUALIFIKASI,
				   LULUS_PENAWARAN, LULUS_PENAWARAN_URUT, LULUS_ADMINISTRASI,
				   LULUS_KEMAMPUAN_DASAR, LULUS_KUALIFIKASI_REKANAN, LULUS_KEWAJARAN_HARGA,
				   STATUS_BAYAR, DI_EMAIL
				FROM PAKET_REKANAN WHERE PAKET_REKANAN_ID IS NOT NULL";

		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key LIKE '%$val%' ";
		}

		$this->query = $str;
		$str .= $statement." ORDER BY PAKET_REKANAN_ID ASC";
		return $this->selectLimit($str,$limit,$from);
    }

    function selectByParamsPaket($paramsArray=array(),$limit=-1,$from=-1, $statement='')
	{
		$str = "SELECT
					PAKET_ID,
					TRIM (
						TO_CHAR(
							B.USER_LOGIN_ID,
							'999999999999'
						)
					) USER_LOGIN_ID,
					'REKANAN' JENIS,
					D.NAMA || ' ' ||B.USER_NAMA NAMA,
					C.NPWP,
					C.TELEPON
				FROM
					PAKET_REKANAN A
				INNER JOIN USER_LOGIN B ON A .REKANAN_ID = B.REKANAN_ID
				INNER JOIN REKANAN C ON B.REKANAN_ID=C.REKANAN_ID
				INNER JOIN REKANAN_TIPE D ON C.REKANAN_TIPE_ID=D.REKANAN_TIPE_ID
				AND A .LULUS_PENDAFTARAN = 1
				AND A .AANWIJZING = 1";

		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}
		// echo $str; die();
		$this->query = $str;
		$str .= $statement." ORDER BY PAKET_REKANAN_ID ASC";
		return $this->selectLimit($str,$limit,$from);
  }

  function selectByParamsPaket2($paramsArray=array(),$limit=-1,$from=-1, $statement='')
	{
		$str = "SELECT A.*, C.NAMA || ' ' || B.NAMA REKANAN_NAMA,
						D.JUMLAH_KOREKSI
						FROM PAKET_REKANAN A
						JOIN REKANAN B ON A.REKANAN_ID = B.REKANAN_ID
						JOIN REKANAN_TIPE C ON B.REKANAN_TIPE_ID=C.rekanan_tipe_id
						JOIN REKANAN_PAKET_PENAWARAN D ON A.PAKET_REKANAN_ID=D.PAKET_REKANAN_ID
						WHERE A.PAKET_REKANAN_ID IS NOT NULL
						AND A.LULUS_KUALIFIKASI = 1
						AND A.LULUS_PENAWARAN = 1
						AND A.LULUS_ADMINISTRASI = 1
						AND A.LULUS_PENDAFTARAN = 1 ";

		while(list($key,$val) = each($paramsArray))
		{
			// $str .= " AND $key = '$val' ";
			$pecah = explode("||", $key);
	    if (count($pecah) > 1) {
	        $str .= "AND $pecah[0] $pecah[1] $val ";
	    } else {
	        $str .= " AND $key = '$val' ";
	    }
		}
		// $str .= $statement." ORDER BY NILAI_PENAWARAN ASC";
		$str .= $statement;
		// echo $str; die();
		$this->query = $str;
		return $this->selectLimit($str,$limit,$from);
  }

  function selectByParamsPaketPemenang($paramsArray=array(),$limit=-1,$from=-1, $statement='')
	{
		$str = "SELECT A.*, C.NAMA || ' ' || B.NAMA REKANAN_NAMA,
						D.JUMLAH_KOREKSI
						FROM PAKET_REKANAN A
						JOIN REKANAN B ON A.REKANAN_ID = B.REKANAN_ID
						JOIN REKANAN_TIPE C ON B.REKANAN_TIPE_ID=C.rekanan_tipe_id
						JOIN REKANAN_PAKET_PENAWARAN D ON A.PAKET_REKANAN_ID=D.PAKET_REKANAN_ID
						WHERE A.PAKET_REKANAN_ID IS NOT NULL
						AND A.LULUS_KUALIFIKASI = 1
						AND A.LULUS_PENAWARAN = 1
						AND A.LULUS_ADMINISTRASI = 1
						AND A.LULUS_PENDAFTARAN = 1 ";

		while(list($key,$val) = each($paramsArray))
		{
			// $str .= " AND $key = '$val' ";
			$pecah = explode("||", $key);
	    if (count($pecah) > 1) {
	        $str .= "AND $pecah[0] $pecah[1] $val ";
	    } else {
	        $str .= " AND $key = '$val' ";
	    }
		}
		// echo $str; die();
		$this->query = $str;
		$str .= $statement." ORDER BY NILAI_PENAWARAN ASC LIMIT 1";
		return $this->selectLimit($str,$limit,$from);
  }

    /**
    * Hitung jumlah record berdasarkan parameter (array).
    * @param array paramsArray Array of parameter. Contoh array("id"=>"xxx","NAMA"=>"yyy")
    * @return long Jumlah record yang sesuai kriteria
    **/

    function getPaketRekananId($paket_id, $rekanan_id, $statement='')
	{
		$str = "SELECT PAKET_REKANAN_ID AS ROWCOUNT FROM PAKET_REKANAN
				WHERE PAKET_REKANAN_ID IS NOT NULL AND PAKET_ID = '".$paket_id."' AND REKANAN_ID = '".$rekanan_id."' ".$statement;

		$this->select($str);
		if($this->firstRow())
			return $this->getField("ROWCOUNT");
		else
			return 0;
    }

    function getPaketRekananBayar($paket_id, $rekanan_id, $statement='')
	{
		$str = "SELECT STATUS_BAYAR AS ROWCOUNT FROM PAKET_REKANAN
				WHERE PAKET_REKANAN_ID IS NOT NULL AND PAKET_ID = '".$paket_id."' AND REKANAN_ID = '".$rekanan_id."' ".$statement;

		$this->select($str);
		if($this->firstRow())
			return $this->getField("ROWCOUNT");
		else
			return 0;
    }

    function getCountByParamsHitungRekanan($paramsArray=array())
	{
		$str = "SELECT COUNT(1) AS ROWCOUNT
				 FROM PAKET_REKANAN A
				 LEFT JOIN REKANAN B ON A.REKANAN_ID=B.REKANAN_ID
				 LEFT JOIN PAKET C ON A.PAKET_ID=C.PAKET_ID
				 LEFT JOIN PAKET_METODE_LELANG D ON C.PAKET_METODE_LELANG_ID = D.PAKET_METODE_LELANG_ID
				 WHERE 1=1";
		while(list($key,$val)=each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}

		$this->select($str);
		$this->query = $str;
		if($this->firstRow())
			return $this->getField("ROWCOUNT");
		else
			return 0;
    }

    function getLulusPendaftaran($rekanan_id, $paket_id, $statement='')
	{
		$str = "SELECT LULUS_PENDAFTARAN AS ROWCOUNT FROM PAKET_REKANAN
				WHERE PAKET_REKANAN_ID IS NOT NULL AND PAKET_ID = '".$paket_id."' AND REKANAN_ID = '".$rekanan_id."' ".$statement;

		$this->select($str);
		if($this->firstRow())
			return $this->getField("ROWCOUNT");
		else
			return "";
    }

    function getCountByParams($paramsArray=array(), $statement='')
	{
		$str = "SELECT COUNT(PAKET_REKANAN_ID) AS ROWCOUNT FROM PAKET_REKANAN A WHERE PAKET_REKANAN_ID IS NOT NULL ".$statement;
		while(list($key,$val)=each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}
		// echo $str; die();
		$this->select($str);
		if($this->firstRow())
			return $this->getField("ROWCOUNT");
		else
			return 0;
    }

    function getCountByParamsLike($paramsArray=array())
	{
		$str = "SELECT COUNT(PAKET_REKANAN_ID) AS ROWCOUNT FROM PAKET_REKANAN WHERE PAKET_REKANAN_ID IS NOT NULL ";
		while(list($key,$val)=each($paramsArray))
		{
			$str .= " AND $key LIKE '%$val%' ";
		}

		$this->select($str);
		if($this->firstRow())
			return $this->getField("ROWCOUNT");
		else
			return 0;
    }
  }
?>
