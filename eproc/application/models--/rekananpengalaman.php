<?php
/**
 * @package 	eProcurement Application
 * @author 		eproc2025
 * @since 		25. Version 3.1
 *
 */

  include_once('entity.php');

  class RekananPengalaman extends Entity{

	var $query;
    /**
    * Class constructor.
    **/
    function __construct(){
  		parent::__construct();
  	}

    function RekananPengalaman()
	{
      $this->Entity();
    }

	function insert()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$this->setField("REKANAN_PENGALAMAN_ID", $this->getNextId("REKANAN_PENGALAMAN_ID","REKANAN_PENGALAMAN"));

		$str = "
				INSERT INTO REKANAN_PENGALAMAN (
					REKANAN_PENGALAMAN_ID,
					REKANAN_ID,
					NAMA,
					LOKASI,
					PEMBERI_TUGAS,
					PEMBERI_TUGAS_ALAMAT,
					KONTRAK_NOMOR,
					KONTRAK_TANGGAL,
					KONTRAK_NILAI,
					KONTRAK_JO,
					KONTRAK_KETERANGAN,
					KONTRAK_STATUS,
					PROGRESS,
					PROGRESS_TANGGAL,
					BA_TANGGAL,
   					PATH_FILE, TIPE, UKURAN,
					PATH_FILE_BA, TIPE_BA, UKURAN_BA,
					NAMA_FILE,
					NAMA_FILE_BA,
					CREATED_BY, CREATED_DATE)
				VALUES ( '".$this->getField("REKANAN_PENGALAMAN_ID")."',
					'".$this->getField("REKANAN_ID")."',
					'".$this->getField("NAMA")."',
					'".$this->getField("LOKASI")."',
					'".$this->getField("PEMBERI_TUGAS")."',
					'".$this->getField("PEMBERI_TUGAS_ALAMAT")."',
					'".$this->getField("KONTRAK_NOMOR")."',
					".$this->getField("KONTRAK_TANGGAL").",
					'".$this->getField("KONTRAK_NILAI")."',
					".$this->getField("KONTRAK_JO").",
					'".$this->getField("KONTRAK_KETERANGAN")."',
					'".$this->getField("KONTRAK_STATUS")."',
					".$this->getField("PROGRESS").",
					".$this->getField("PROGRESS_TANGGAL").",
					".$this->getField("BA_TANGGAL").",
					'".$this->getField("PATH_FILE")."',
					'".$this->getField("TIPE")."',
					".$this->getField("UKURAN").",
					'".$this->getField("PATH_FILE_BA")."',
					'".$this->getField("TIPE_BA")."',
					".$this->getField("UKURAN_BA").",
					'".$this->getField("NAMA_FILE")."',
					'".$this->getField("NAMA_FILE_BA")."',
					".$this->getField("CREATED_BY").",
					CURRENT_TIMESTAMP
					)
		";
		//echo $str;
		$this->query = $str;
		//
		$this->id = $this->getField("REKANAN_PENGALAMAN_ID");
		return $this->execQuery($str);
    }

    function update()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$str = "UPDATE REKANAN_PENGALAMAN
				SET
					NAMA = '".$this->getField("NAMA")."',
					LOKASI = '".$this->getField("LOKASI")."',
					PEMBERI_TUGAS = '".$this->getField("PEMBERI_TUGAS")."',
					PEMBERI_TUGAS_ALAMAT = '".$this->getField("PEMBERI_TUGAS_ALAMAT")."',
					KONTRAK_NOMOR = '".$this->getField("KONTRAK_NOMOR")."',
					KONTRAK_TANGGAL = ".$this->getField("KONTRAK_TANGGAL").",
					KONTRAK_NILAI = '".$this->getField("KONTRAK_NILAI")."',
					KONTRAK_JO = ".$this->getField("KONTRAK_JO").",
					KONTRAK_KETERANGAN = '".$this->getField("KONTRAK_KETERANGAN")."',
					KONTRAK_STATUS = '".$this->getField("KONTRAK_STATUS")."',
					PROGRESS = ".$this->getField("PROGRESS").",
					PROGRESS_TANGGAL = ".$this->getField("PROGRESS_TANGGAL").",
					BA_TANGGAL = ".$this->getField("BA_TANGGAL").",
					PATH_FILE_BA = '".$this->getField("PATH_FILE_BA")."',
					TIPE_BA = '".$this->getField("TIPE_BA")."',
					UKURAN_BA = ".$this->getField("UKURAN_BA").",
					PATH_FILE = '".$this->getField("PATH_FILE")."',
					TIPE = '".$this->getField("TIPE")."',
					UKURAN = '".$this->getField("UKURAN")."',
					NAMA_FILE = '".$this->getField("NAMA_FILE")."',
					NAMA_FILE_BA = '".$this->getField("NAMA_FILE_BA")."',
					UPDATED_BY = ".$this->getField("CREATED_BY").",
					UPDATED_DATE = CURRENT_TIMESTAMP
				WHERE REKANAN_PENGALAMAN_ID = '".$this->getField("REKANAN_PENGALAMAN_ID")."'
					 AND REKANAN_ID = '".$this->getField("REKANAN_ID")."'
				";
				// echo $str; die();
				$this->query = $str;
		return $this->execQuery($str);
    }

    function updateNilai()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$str = "UPDATE REKANAN_PENGALAMAN
				SET
					KONTRAK_NILAI = '".$this->getField("KONTRAK_NILAI")."',
					KONTRAK_NILAI_SEBELUMNYA = '".$this->getField("KONTRAK_NILAI_SEBELUMNYA")."',
					UPDATED_BY = '".$this->getField("UPDATED_BY")."',
					UPDATED_DATE = CURRENT_DATE
				WHERE REKANAN_PENGALAMAN_ID = '".$this->getField("REKANAN_PENGALAMAN_ID")."'
				";

				$this->query = $str;
		return $this->execQuery($str);
    }

	function delete()
	{
		$str1 = "DELETE FROM REKANAN_PENGALAMAN_BIDANG
                WHERE
                  REKANAN_PENGALAMAN_ID = '".$this->getField("REKANAN_PENGALAMAN_ID")."'";

		$this->query = $str1;
        $this->execQuery($str1);

        $str = "DELETE FROM REKANAN_PENGALAMAN
                WHERE
                  REKANAN_PENGALAMAN_ID = '".$this->getField("REKANAN_PENGALAMAN_ID")."' AND REKANAN_ID = '".$this->getField("REKANAN_ID")."' ";

		$this->query = $str;
        return $this->execQuery($str);
    }

	function delete_bidang()
	{
		$str = "DELETE FROM REKANAN_PENGALAMAN_BIDANG
                WHERE
                  REKANAN_PENGALAMAN_ID = '".$this->getField("REKANAN_PENGALAMAN_ID")."'";

		$this->query = $str;
        return $this->execQuery($str);
    }

    /**
    * Cari record berdasarkan array parameter dan limit tampilan
    * @param array paramsArray Array of parameter. Contoh array("id"=>"xxx","NAMA"=>"yyy")
    * @param int limit Jumlah maksimal record yang akan diambil
    * @param int from Awal record yang diambil
    * @return boolean True jika sukses, false jika tidak
    **/
    function selectByParams($paramsArray=array(),$limit=-1,$from=-1, $statement='')
	{
		$str = "
			SELECT
				REKANAN_PENGALAMAN_ID,
				AMBIL_PENGALAMAN_BIDANG_USAHA(REKANAN_PENGALAMAN_ID) PENGALAMAN_BIDANG,
				REKANAN_ID,
				NAMA,
				LOKASI,
				PEMBERI_TUGAS,
				PEMBERI_TUGAS_ALAMAT,
				KONTRAK_NOMOR,
				TO_CHAR(KONTRAK_TANGGAL, 'YYYY-MM-DD') KONTRAK_TANGGAL,
				KONTRAK_NILAI,
				KONTRAK_JO,
				KONTRAK_KETERANGAN,
				KONTRAK_STATUS,
				PROGRESS,
				TO_CHAR(PROGRESS_TANGGAL, 'YYYY-MM-DD') PROGRESS_TANGGAL,
				TO_CHAR(BA_TANGGAL, 'YYYY-MM-DD') BA_TANGGAL,
   				PATH_FILE, TIPE, UKURAN,  CASE KONTRAK_JO WHEN 100 THEN 'Kontraktor Utama'
									ELSE 'Sub Kontraktor' END JO,
				PATH_FILE_BA, TIPE_BA, UKURAN_BA, NAMA_FILE, NAMA_FILE_BA
			FROM REKANAN_PENGALAMAN A
			WHERE REKANAN_PENGALAMAN_ID IS NOT NULL
		";

		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}

		$this->query = $str;
		$str .= $statement." ORDER BY REKANAN_PENGALAMAN_ID ASC";

		return $this->selectLimit($str,$limit,$from);
    }

    function selectByParamsSyarat($paramsArray=array(),$limit=-1,$from=-1, $statement='')
	{
		$str = "
			SELECT
                A.REKANAN_PENGALAMAN_ID,
                AMBIL_PENGALAMAN_BIDANG_USAHA(A.REKANAN_PENGALAMAN_ID) PENGALAMAN_BIDANG,
				A.REKANAN_ID,
				NAMA,
				LOKASI,
				PEMBERI_TUGAS,
				PEMBERI_TUGAS_ALAMAT,
				KONTRAK_NOMOR,
				KONTRAK_TANGGAL,
				KONTRAK_NILAI,
				KONTRAK_JO,
				KONTRAK_KETERANGAN,
				KONTRAK_STATUS,
				PROGRESS,
				PROGRESS_TANGGAL,
				BA_TANGGAL,
   				PATH_FILE, TIPE, UKURAN, DECODE(KONTRAK_JO, 100, 'Kontraktor Utama', 'Sub Kontraktor') JO,
				PATH_FILE_BA, TIPE_BA, UKURAN_BA,
                B.CATATAN,
                B.REKANAN_DAFTAR_PENGALAMAN_ID, NAMA_FILE, NAMA_FILE_BA
			FROM REKANAN_PENGALAMAN A
            INNER JOIN REKANAN_DAFTAR_PENGALAMAN B ON A.REKANAN_PENGALAMAN_ID = B.REKANAN_PENGALAMAN_ID AND A.REKANAN_ID = B.REKANAN_ID
			WHERE A.REKANAN_PENGALAMAN_ID IS NOT NULL
		";

		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}

		$this->query = $str;
		$str .= $statement." ORDER BY A.REKANAN_PENGALAMAN_ID ASC";

		return $this->selectLimit($str,$limit,$from);
    }

    function selectByParamsNilai($paramsArray=array(),$limit=-1,$from=-1, $statement='', $bidang_usaha_id='',$panel_rekanan_id='')
	{
		$str = "
			SELECT
                A.REKANAN_PENGALAMAN_ID,
                AMBIL_PENGALAMAN_BIDANG_USAHA(A.REKANAN_PENGALAMAN_ID) PENGALAMAN_BIDANG,
                REKANAN_ID,
                NAMA,
                LOKASI,
                PEMBERI_TUGAS,
                PEMBERI_TUGAS_ALAMAT,
                KONTRAK_NOMOR,
                KONTRAK_TANGGAL,
                KONTRAK_NILAI,
                KONTRAK_JO,
                KONTRAK_KETERANGAN,
                KONTRAK_STATUS,
                PROGRESS,
                PROGRESS_TANGGAL,
                BA_TANGGAL, NAMA_FILE, NAMA_FILE_BA,
   				PATH_FILE, TIPE, UKURAN, DECODE(KONTRAK_JO, 100, 'Kontraktor Utama', 'Sub Kontraktor') JO,
				PATH_FILE_BA, TIPE_BA, UKURAN_BA, NILAI, NILAI_JENIS, NILAI_TOTAL,
				COALESCE(KONTRAK_NILAI_SEBELUMNYA, KONTRAK_NILAI) KONTRAK_NILAI_SEBELUMNYA
			FROM REKANAN_PENGALAMAN A LEFT JOIN PANEL_REKANAN_BID_USAHA_NILAI B ON A.REKANAN_PENGALAMAN_ID = B.REKANAN_PENGALAMAN_ID AND B.PANEL_BIDANG_USAHA_ID = '".$bidang_usaha_id."' AND B.PANEL_REKANAN_ID = '".$panel_rekanan_id."'
			WHERE A.REKANAN_PENGALAMAN_ID IS NOT NULL
		";

		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}

		$this->query = $str;
		$str .= $statement." ORDER BY REKANAN_PENGALAMAN_ID ASC";

		return $this->selectLimit($str,$limit,$from);
    }

	function selectByParamsRekananBidangUsaha($paramsArray=array(),$limit=-1,$from=-1, $statement='')
	{
		$str = "
				SELECT AMBIL_BIDANG_USAHA_NAMA(BIDANG_USAHA_ID) NAMA FROM REKANAN_BIDANG_USAHA WHERE REKANAN_BIDANG_USAHA_ID IS NOT NULL ";

		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}

		$this->query = $str;
		$str .= $statement." ORDER BY REKANAN_BIDANG_USAHA_ID ASC";
		//echo $str;
		return $this->selectLimit($str,$limit,$from);
    }

	function selectByParamsPengalamanKualifikasi($rekanan_id, $paket_id, $statement="")
	{
		$str = "
                SELECT AMBIL_PENGALAMAN_BIDANG_USAHA(A.REKANAN_PENGALAMAN_ID) BIDANG_USAHA, A.REKANAN_PENGALAMAN_ID,
                       A.NAMA, A.LOKASI, A.KONTRAK_NILAI, CASE WHEN KONTRAK_JO = 100 THEN 'KONTRAKTOR UTAMA'
					   									       ELSE 'SUB KONTRAKTOR' END JO
			     FROM REKANAN_PENGALAMAN A
                 WHERE REKANAN_ID = '".$rekanan_id."'
                    ".$statement;
				/*
				DIHILANGKAN
				AND EXISTS (SELECT 1
                FROM PAKET_BIDANG_USAHA X
                WHERE X.BIDANG_USAHA_ID = A.BIDANG_USAHA_ID AND PAKET_ID = '".$paket_id."')
				*/
		$this->query = $str;
		return $this->selectLimit($str, -1, -1);
    }


	function selectByParamsLike($paramsArray=array(),$limit=-1,$from=-1, $statement='')
	{
		$str = "
			SELECT
				REKANAN_PENGALAMAN_ID,
				REKANAN_ID,
				NAMA,
				LOKASI,
				PEMBERI_TUGAS,
				PEMBERI_TUGAS_ALAMAT,
				KONTRAK_NOMOR,
				KONTRAK_TANGGAL,
				KONTRAK_NILAI,
				KONTRAK_JO,
				KONTRAK_KETERANGAN,
				KONTRAK_STATUS,
				PROGRESS,
				PROGRESS_TANGGAL,
				BA_TANGGAL
			FROM REKANAN_PENGALAMAN
			WHERE REKANAN_PENGALAMAN_ID IS NOT NULL
		";

		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key LIKE '%$val%' ";
		}

		$this->query = $str;
		$str .= $statement." ORDER BY REKANAN_PENGALAMAN_ID ASC";
		return $this->selectLimit($str,$limit,$from);
    }
    /**
    * Hitung jumlah record berdasarkan parameter (array).
    * @param array paramsArray Array of parameter. Contoh array("id"=>"xxx","NAMA"=>"yyy")
    * @return long Jumlah record yang sesuai kriteria
    **/
    function getCountByParams($paramsArray=array(),$statement='')
	{
		$str = "SELECT COUNT(REKANAN_PENGALAMAN_ID) AS ROWCOUNT FROM REKANAN_PENGALAMAN A WHERE REKANAN_PENGALAMAN_ID IS NOT NULL ";
		while(list($key,$val)=each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}
		$str .= $statement;
		$this->select($str);
		if($this->firstRow())
			return $this->getField("ROWCOUNT");
		else
			return 0;
    }

    function getCountByParamsLike($paramsArray=array())
	{
		$str = "SELECT COUNT(REKANAN_PENGALAMAN_ID) AS ROWCOUNT FROM REKANAN_PENGALAMAN WHERE REKANAN_PENGALAMAN_ID IS NOT NULL ";
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
