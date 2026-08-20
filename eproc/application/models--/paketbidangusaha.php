<?php
/**
 * @package 	eProcurement Application
 * @author 		eproc2025
 * @since 		25. Version 3.1
 * 
 */

  include_once('entity.php');

  class PaketBidangUsaha extends Entity{

	var $query;
    /**
    * Class constructor.
    **/
    function __construct(){
		parent::__construct();
	}

    function PaketBidangUsaha()
	{
      $this->Entity();
    }

	function insert()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$this->setField("PAKET_BIDANG_USAHA_ID", $this->getNextId("PAKET_BIDANG_USAHA_ID","PAKET_BIDANG_USAHA"));

		$str = "
		INSERT INTO  PAKET_BIDANG_USAHA (
		   PAKET_BIDANG_USAHA_ID, PAKET_ID, BIDANG_USAHA_ID, CREATED_BY, CREATED_DATE )
 			 	VALUES (
				  ".$this->getField("PAKET_BIDANG_USAHA_ID").",
  				  ".$this->getField("PAKET_ID").",
				  '".$this->getField("BIDANG_USAHA_ID")."',
				  ".$this->getField("CREATED_BY").",
				  CURRENT_TIMESTAMP
				)";

		$this->query = $str;

		return $this->execQuery($str);
    }

    function update()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$str = "UPDATE PAKET_BIDANG_USAHA SET
				  PAKET_ID = ".$this->getField("PAKET_ID").",
				  BIDANG_USAHA_ID = '".$this->getField("BIDANG_USAHA_ID")."'
				WHERE PAKET_BIDANG_USAHA_ID = ".$this->getField("PAKET_BIDANG_USAHA_ID")."
				";
				$this->query = $str;
		return $this->execQuery($str);
    }

	function delete()
	{
        $str = "DELETE FROM PAKET_BIDANG_USAHA
                WHERE
                  PAKET_ID = ".$this->getField("PAKET_ID")."";

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
		$str = "SELECT PAKET_BIDANG_USAHA_ID, PAKET_ID, BIDANG_USAHA_ID, AMBIL_BIDANG_USAHA_NAMA(BIDANG_USAHA_ID) NAMA
				FROM PAKET_BIDANG_USAHA WHERE PAKET_BIDANG_USAHA_ID IS NOT NULL";

		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}

		$str .= $statement." ORDER BY PAKET_BIDANG_USAHA_ID ASC";
		// echo $str; die();
		$this->query = $str;

		return $this->selectLimit($str,$limit,$from);
    }

    function selectByParamsRekananTanpaBidangUsaha($paramsArray=array(),$limit=-1,$from=-1, $id='', $statement='')
	{
		$str = "SELECT DISTINCT A.REKANAN_ID, NAMA, TRIM(ALAMAT) ALAMAT, EMAIL
				   FROM REKANAN A
				  WHERE NOT EXISTS(SELECT 1 FROM PAKET_REKANAN X WHERE A.REKANAN_ID = X.REKANAN_ID AND PAKET_ID = ".$id.") ".$statement;

		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}
		// echo $str; die();
		$this->query = $str;
		$str .= " ORDER BY NAMA ASC";

		return $this->selectLimit($str,$limit,$from);
    }

    function selectByParamsRekananTanpaBidangUsahaIkun($paramsArray=array(),$limit=-1,$from=-1, $id='', $statement='')
	{
		$str = "SELECT DISTINCT A.REKANAN_ID, NAMA, TRIM(ALAMAT) ALAMAT, EMAIL,
				(SELECT
				TO_CHAR(TANGGAL, 'DD-MM-YYYY') TGL_AWAL_SIUP
				FROM REKANAN_IJIN_USAHA WHERE IJIN_USAHA_ID = 2 AND A.REKANAN_ID=REKANAN_ID LIMIT 1) SIUP_TGL_AWAL,
				(SELECT
				TO_CHAR(TANGGAL_BERAKHIR, 'DD-MM-YYYY') TGL_AKHIR_SIUP
				FROM REKANAN_IJIN_USAHA WHERE IJIN_USAHA_ID = 2 AND A.REKANAN_ID=REKANAN_ID LIMIT 1) SIUP_TGL_AKHIR,
				(SELECT
				TO_CHAR(TANGGAL, 'DD-MM-YYYY') TGL_AWAL_SIUP
				FROM REKANAN_IJIN_USAHA WHERE IJIN_USAHA_ID = 3 AND A.REKANAN_ID=REKANAN_ID LIMIT 1) IUJK_TGL_AWAL,
				(SELECT
				TO_CHAR(TANGGAL_BERAKHIR, 'DD-MM-YYYY') TGL_AKHIR_SIUP
				FROM REKANAN_IJIN_USAHA WHERE IJIN_USAHA_ID = 3 AND A.REKANAN_ID=REKANAN_ID LIMIT 1) IUJK_TGL_AKHIR,
				(SELECT
				TO_CHAR(TANGGAL, 'DD-MM-YYYY') TGL_AWAL_SIUP
				FROM REKANAN_IJIN_USAHA WHERE IJIN_USAHA_ID = 99 AND A.REKANAN_ID=REKANAN_ID LIMIT 1) SBUJK_TGL_AWAL,
				(SELECT
				TO_CHAR(TANGGAL_BERAKHIR, 'DD-MM-YYYY') TGL_AKHIR_SIUP
				FROM REKANAN_IJIN_USAHA WHERE IJIN_USAHA_ID = 99 AND A.REKANAN_ID=REKANAN_ID LIMIT 1) SBUJK_TGL_AKHIR,
				TO_CHAR(NOW(), 'DD-MM-YYYY') NOW
				   FROM REKANAN A
					 JOIN user_login C on A.REKANAN_ID=C.REKANAN_ID
				  WHERE A.status_validasi=1
					  AND c.user_status=1 
					  AND NOT EXISTS(SELECT 1 FROM PAKET_REKANAN X WHERE A.REKANAN_ID = X.REKANAN_ID AND PAKET_ID = ".$id.") ".$statement;

		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}
		// echo $str; die();
		$this->query = $str;
		$str .= " ORDER BY NAMA ASC";

		return $this->selectLimit($str,$limit,$from);
    }

    function selectByParamsRekanan($paramsArray=array(),$limit=-1,$from=-1, $id='', $statement='')
	{
		$str = "SELECT DISTINCT A.REKANAN_ID, NAMA, TRIM(ALAMAT) ALAMAT, EMAIL
				   FROM REKANAN_BIDANG_USAHA A, REKANAN B
				  WHERE A.REKANAN_ID = B.REKANAN_ID
					AND EXISTS (
						   SELECT 1
							 FROM PAKET_BIDANG_USAHA X
							WHERE X.BIDANG_USAHA_ID = A.BIDANG_USAHA_ID
							  AND PAKET_ID = ".$id.")  AND NOT EXISTS(SELECT 1 FROM PAKET_REKANAN X WHERE A.REKANAN_ID = X.REKANAN_ID AND PAKET_ID = ".$id.") ".$statement;
		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}
		// echo $str; exit();
		$this->query = $str;
		$str .= "GROUP BY 
					    A.REKANAN_ID, 
					    B.NAMA, 
					    B.ALAMAT, 
					    B.EMAIL
						HAVING COUNT(DISTINCT A.BIDANG_USAHA_ID) = (
						    SELECT COUNT(DISTINCT BIDANG_USAHA_ID)
						    FROM PAKET_BIDANG_USAHA
						    WHERE PAKET_ID = ".$id."
						)
						ORDER BY NAMA ASC";

		return $this->selectLimit($str,$limit,$from);
    }

    function selectByParamsRekananIkun($paramsArray=array(),$limit=-1,$from=-1, $id='', $statement='')
	{
		$str = "SELECT DISTINCT A.REKANAN_ID, B.NAMA, TRIM(ALAMAT) ALAMAT, EMAIL,
				(SELECT COUNT(PAKET_REKANAN_ID) TOTAL_UNDANG FROM PAKET_REKANAN WHERE A.REKANAN_ID=REKANAN_ID AND TANGGAL_UNDANG IS NOT NULL ) TOTAL_UNDANG,
				(SELECT COUNT(PAKET_PEMENANG_ID) TOTAL_PEMENANG FROM PAKET_PEMENANG WHERE A.REKANAN_ID=REKANAN_ID AND PERINGKAT='1' AND PUBLISH = '1') TOTAL_PEMENANG,
				(SELECT
				TO_CHAR(TANGGAL, 'DD-MM-YYYY') TGL_AWAL_SIUP
				FROM REKANAN_IJIN_USAHA WHERE IJIN_USAHA_ID = 2 AND A.REKANAN_ID=REKANAN_ID LIMIT 1) SIUP_TGL_AWAL,
				(SELECT
				TO_CHAR(TANGGAL_BERAKHIR, 'DD-MM-YYYY') TGL_AKHIR_SIUP
				FROM REKANAN_IJIN_USAHA WHERE IJIN_USAHA_ID = 2 AND A.REKANAN_ID=REKANAN_ID LIMIT 1) SIUP_TGL_AKHIR,
				(SELECT
				TO_CHAR(TANGGAL, 'DD-MM-YYYY') TGL_AWAL_SIUP
				FROM REKANAN_IJIN_USAHA WHERE IJIN_USAHA_ID = 3 AND A.REKANAN_ID=REKANAN_ID LIMIT 1) IUJK_TGL_AWAL,
				(SELECT
				TO_CHAR(TANGGAL_BERAKHIR, 'DD-MM-YYYY') TGL_AKHIR_SIUP
				FROM REKANAN_IJIN_USAHA WHERE IJIN_USAHA_ID = 3 AND A.REKANAN_ID=REKANAN_ID LIMIT 1) IUJK_TGL_AKHIR,
				(SELECT
				TO_CHAR(TANGGAL, 'DD-MM-YYYY') TGL_AWAL_SIUP
				FROM REKANAN_IJIN_USAHA WHERE IJIN_USAHA_ID = 99 AND A.REKANAN_ID=REKANAN_ID LIMIT 1) SBUJK_TGL_AWAL,
				(SELECT
				TO_CHAR(TANGGAL_BERAKHIR, 'DD-MM-YYYY') TGL_AKHIR_SIUP
				FROM REKANAN_IJIN_USAHA WHERE IJIN_USAHA_ID = 99 AND A.REKANAN_ID=REKANAN_ID LIMIT 1) SBUJK_TGL_AKHIR,
				TO_CHAR(NOW(), 'DD-MM-YYYY') NOW
				   FROM REKANAN_BIDANG_USAHA A, REKANAN B, USER_LOGIN C, REKANAN_KUALIFIKASI D
				  WHERE A.REKANAN_ID = B.REKANAN_ID AND A.REKANAN_ID = C.REKANAN_ID AND B.REKANAN_KUALIFIKASI_ID=D.REKANAN_KUALIFIKASI_ID AND C.USER_STATUS = '1' AND A.VALIDASI='1' AND B.STATUS_VALIDASI=1
					AND EXISTS (
						   SELECT 1
							 FROM PAKET_BIDANG_USAHA X
							WHERE X.BIDANG_USAHA_ID = A.BIDANG_USAHA_ID
							  AND PAKET_ID = ".$id.")  AND NOT EXISTS(SELECT 1 FROM PAKET_REKANAN X WHERE A.REKANAN_ID = X.REKANAN_ID AND PAKET_ID = ".$id.") ".$statement;
		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}
		$this->query = $str;
		$str .= "ORDER BY NAMA ASC";
		// echo $str; exit();

		return $this->selectLimit($str,$limit,$from);
  }

  function selectByParamsRekananIkun2($paramsArray=array(),$limit=-1,$from=-1, $id='', $statement='')
	{
		$str = "SELECT DISTINCT A.REKANAN_ID, B.NAMA, TRIM(ALAMAT) ALAMAT, EMAIL,
				(SELECT COUNT(PAKET_REKANAN_ID) TOTAL_UNDANG FROM PAKET_REKANAN WHERE A.REKANAN_ID=REKANAN_ID AND TANGGAL_UNDANG IS NOT NULL ) TOTAL_UNDANG,
				(SELECT COUNT(PAKET_PEMENANG_ID) TOTAL_PEMENANG FROM PAKET_PEMENANG WHERE A.REKANAN_ID=REKANAN_ID AND PERINGKAT='1' AND PUBLISH = '1') TOTAL_PEMENANG,
				(SELECT
				TO_CHAR(TANGGAL, 'DD-MM-YYYY') TGL_AWAL_SIUP
				FROM REKANAN_IJIN_USAHA WHERE IJIN_USAHA_ID = 2 AND A.REKANAN_ID=REKANAN_ID LIMIT 1) SIUP_TGL_AWAL,
				(SELECT
				TO_CHAR(TANGGAL_BERAKHIR, 'DD-MM-YYYY') TGL_AKHIR_SIUP
				FROM REKANAN_IJIN_USAHA WHERE IJIN_USAHA_ID = 2 AND A.REKANAN_ID=REKANAN_ID LIMIT 1) SIUP_TGL_AKHIR,
				(SELECT
				TO_CHAR(TANGGAL, 'DD-MM-YYYY') TGL_AWAL_SIUP
				FROM REKANAN_IJIN_USAHA WHERE IJIN_USAHA_ID = 3 AND A.REKANAN_ID=REKANAN_ID LIMIT 1) IUJK_TGL_AWAL,
				(SELECT
				TO_CHAR(TANGGAL_BERAKHIR, 'DD-MM-YYYY') TGL_AKHIR_SIUP
				FROM REKANAN_IJIN_USAHA WHERE IJIN_USAHA_ID = 3 AND A.REKANAN_ID=REKANAN_ID LIMIT 1) IUJK_TGL_AKHIR,
				(SELECT
				TO_CHAR(TANGGAL, 'DD-MM-YYYY') TGL_AWAL_SIUP
				FROM REKANAN_IJIN_USAHA WHERE IJIN_USAHA_ID = 99 AND A.REKANAN_ID=REKANAN_ID LIMIT 1) SBUJK_TGL_AWAL,
				(SELECT
				TO_CHAR(TANGGAL_BERAKHIR, 'DD-MM-YYYY') TGL_AKHIR_SIUP
				FROM REKANAN_IJIN_USAHA WHERE IJIN_USAHA_ID = 99 AND A.REKANAN_ID=REKANAN_ID LIMIT 1) SBUJK_TGL_AKHIR,
				TO_CHAR(NOW(), 'DD-MM-YYYY') NOW
				   FROM REKANAN_BIDANG_USAHA A, REKANAN B, USER_LOGIN C, REKANAN_KUALIFIKASI D
				  WHERE A.REKANAN_ID = B.REKANAN_ID AND A.REKANAN_ID = C.REKANAN_ID AND B.REKANAN_KUALIFIKASI_ID=D.REKANAN_KUALIFIKASI_ID AND C.USER_STATUS = '1' AND A.VALIDASI='1' AND B.STATUS_VALIDASI=1
					AND EXISTS (
						   SELECT 1
							 FROM PAKET_BIDANG_USAHA X
							WHERE X.BIDANG_USAHA_ID = A.BIDANG_USAHA_ID
							  AND PAKET_ID = ".$id.")  AND NOT EXISTS(SELECT 1 FROM PAKET_REKANAN X WHERE A.REKANAN_ID = X.REKANAN_ID AND PAKET_ID = ".$id.") ".$statement;
		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}
		$this->query = $str;
		$str .= "GROUP BY 
					    A.REKANAN_ID, 
					    B.NAMA, 
					    B.ALAMAT, 
					    B.EMAIL
						HAVING COUNT(DISTINCT A.BIDANG_USAHA_ID) = (
						    SELECT COUNT(DISTINCT BIDANG_USAHA_ID)
						    FROM PAKET_BIDANG_USAHA
						    WHERE PAKET_ID = ".$id."
						)
						ORDER BY NAMA ASC";
		// echo $str; exit();

		return $this->selectLimit($str,$limit,$from);
  }

    function selectByParamsRekananCount($paramsArray=array(),$limit=-1,$from=-1, $id='', $statement='')
	{
		$str = "SELECT COUNT(A.REKANAN_ID) AS ROWCOUNT
				   FROM REKANAN_BIDANG_USAHA A, REKANAN B
				  WHERE A.REKANAN_ID = B.REKANAN_ID
					AND EXISTS (
						   SELECT 1
							 FROM PAKET_BIDANG_USAHA X
							WHERE X.BIDANG_USAHA_ID = A.BIDANG_USAHA_ID
							  AND PAKET_ID = ".$id.")  AND NOT EXISTS(SELECT 1 FROM PAKET_REKANAN X WHERE A.REKANAN_ID = X.REKANAN_ID AND PAKET_ID = ".$id.") ".$statement;
		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}
		// echo $str; exit();

	    $this->select($str);
			if($this->firstRow())
				return $this->getField("ROWCOUNT");
			else
				return 0;
    }

    function selectByParamsRekananCountDaftar($paramsArray=array(),$limit=-1,$from=-1, $id='', $statement='')
	{
		$str = "SELECT COUNT(A.REKANAN_ID) AS ROWCOUNT FROM (
				SELECT A.* FROM (
					SELECT A.rekanan_id, B.status_validasi, C.user_status, A.validasi,
					(select max(aa.kontrak_nilai)
						from rekanan_pengalaman aa
						left join rekanan_pengalaman_bidang bb on aa.rekanan_pengalaman_id = bb.rekanan_pengalaman_id
						where bb.bidang_usaha_id is not null and bb.bidang_usaha_id = A.bidang_usaha_id and aa.rekanan_id=A.rekanan_id
						group by aa.rekanan_pengalaman_id,aa.rekanan_id, aa.kontrak_nilai
						order by aa.kontrak_nilai desc
						limit 1) nilai, b.rekanan_kualifikasi_id
					FROM REKANAN_BIDANG_USAHA A, REKANAN B, USER_LOGIN C
					  WHERE A.REKANAN_ID = B.REKANAN_ID AND B.REKANAN_ID=C.REKANAN_ID
						AND EXISTS (
							   SELECT 1
								 FROM PAKET_BIDANG_USAHA X
								WHERE X.BIDANG_USAHA_ID = A.BIDANG_USAHA_ID
								  AND PAKET_ID = ".$id.")  AND NOT EXISTS(SELECT 1 FROM PAKET_REKANAN X WHERE A.REKANAN_ID = X.REKANAN_ID AND PAKET_ID = ".$id.")
				) A  where 1=1 ".$statement;
		while(list($key,$val) = each($paramsArray))
		{
			$pecah = explode("||", $key);
		    if (count($pecah) > 1) {
		        $str .= "AND $pecah[0] $pecah[1] $val ";
		    } else {
		        $str .= " AND $key = '$val' ";
		    }
		}

		$str .= " ) A ";

		// echo $str; exit();

	    $this->select($str);
			if($this->firstRow())
				return $this->getField("ROWCOUNT");
			else
				return 0;
    }

    function selectByParamsRekananCountDaftar2($paramsArray=array(),$limit=-1,$from=-1, $id='', $statement='')
	{
		$str = "SELECT COUNT(A.REKANAN_ID) AS ROWCOUNT FROM (
				SELECT A.rekanan_id FROM (
					SELECT A.rekanan_id, B.status_validasi, C.user_status, A.validasi, BIDANG_USAHA_ID,
					(select max(aa.kontrak_nilai)
						from rekanan_pengalaman aa
						left join rekanan_pengalaman_bidang bb on aa.rekanan_pengalaman_id = bb.rekanan_pengalaman_id
						where bb.bidang_usaha_id is not null and bb.bidang_usaha_id = A.bidang_usaha_id and aa.rekanan_id=A.rekanan_id
						group by aa.rekanan_pengalaman_id,aa.rekanan_id, aa.kontrak_nilai
						order by aa.kontrak_nilai desc
						limit 1) nilai, b.rekanan_kualifikasi_id
					FROM REKANAN_BIDANG_USAHA A, REKANAN B, USER_LOGIN C
					  WHERE A.REKANAN_ID = B.REKANAN_ID AND B.REKANAN_ID=C.REKANAN_ID
						AND EXISTS (
							   SELECT 1
								 FROM PAKET_BIDANG_USAHA X
								WHERE X.BIDANG_USAHA_ID = A.BIDANG_USAHA_ID
								  AND PAKET_ID = ".$id.")  AND NOT EXISTS(SELECT 1 FROM PAKET_REKANAN X WHERE A.REKANAN_ID = X.REKANAN_ID AND PAKET_ID = ".$id.")
				) A  where 1=1 ".$statement;
		while(list($key,$val) = each($paramsArray))
		{
			$pecah = explode("||", $key);
		    if (count($pecah) > 1) {
		        $str .= "AND $pecah[0] $pecah[1] $val ";
		    } else {
		        $str .= " AND $key = '$val' ";
		    }
		}

		$str .= " ) A ";

		// echo $str; exit();

	    $this->select($str);
			if($this->firstRow())
				return $this->getField("ROWCOUNT");
			else
				return 0;
    }



	function selectByParamsLike($paramsArray=array(),$limit=-1,$from=-1, $statement='')
	{
		$str = "SELECT  PAKET_BIDANG_USAHA_ID, PAKET_ID, BIDANG_USAHA_ID
				FROM PAKET_BIDANG_USAHA WHERE PAKET_BIDANG_USAHA_ID IS NOT NULL";

		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key LIKE '%$val%' ";
		}

		$this->query = $str;
		$str .= $statement." ORDER BY PAKET_BIDANG_USAHA_ID ASC";
		return $this->selectLimit($str,$limit,$from);
    }
    /**
    * Hitung jumlah record berdasarkan parameter (array).
    * @param array paramsArray Array of parameter. Contoh array("id"=>"xxx","NAMA"=>"yyy")
    * @return long Jumlah record yang sesuai kriteria
    **/
    function getCountByParams($paramsArray=array())
	{
		$str = "SELECT COUNT(PAKET_BIDANG_USAHA_ID) AS ROWCOUNT FROM PAKET_BIDANG_USAHA WHERE PAKET_BIDANG_USAHA_ID IS NOT NULL ";
		while(list($key,$val)=each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}

		$this->select($str);
		if($this->firstRow())
			return $this->getField("ROWCOUNT");
		else
			return 0;
    }

    function getCountByParamsLike($paramsArray=array())
	{
		$str = "SELECT COUNT(PAKET_BIDANG_USAHA_ID) AS ROWCOUNT FROM PAKET_BIDANG_USAHA WHERE PAKET_BIDANG_USAHA_ID IS NOT NULL ";
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

    // ikn 20200127
    function selectCountByParamsBidangUsaha($paramsArray=array(),$limit=-1,$from=-1, $statement='')
	{
		$str = "SELECT count(a.bidang_usaha_id), a.* from (
				SELECT a.bidang_usaha_id
				from rekanan_bidang_usaha a
					join rekanan b on a.rekanan_id=b.rekanan_id
					join user_login c on a.rekanan_id=c.rekanan_id
					join rekanan_kualifikasi d on b.rekanan_kualifikasi_id=d.rekanan_kualifikasi_id
					join rekanan_tipe e on b.rekanan_tipe_id=e.rekanan_tipe_id
				where a.ijin_usaha_id in (1,2,3,99)
					  and b.status_validasi=1
					  and c.user_status=1
					  and a.validasi='1'
            AND b.rekanan_id not in (SELECT rekanan_id FROM blacklist
            where current_date between tanggal_mulai and tanggal_selesai )
			";

		// while(list($key,$val) = each($paramsArray))
		foreach ($paramsArray as $key => $val) {
			$str .= " AND $key = '$val' ";
		}

		$str .= $statement." ) a group by a.bidang_usaha_id";
		// echo $str;
		$this->query = $str;

		return $this->selectLimit($str,$limit,$from);
    }

    // ikn 20260413
    function selectCountByParamsBidangUsaha2($paramsArray=array(),$limit=-1,$from=-1, $statement='',$count)
	{
		$str = "SELECT count(*) from (
				SELECT a.rekanan_id
				from rekanan_bidang_usaha a
					join rekanan b on a.rekanan_id=b.rekanan_id
					join user_login c on a.rekanan_id=c.rekanan_id
					join rekanan_kualifikasi d on b.rekanan_kualifikasi_id=d.rekanan_kualifikasi_id
					join rekanan_tipe e on b.rekanan_tipe_id=e.rekanan_tipe_id
				where a.ijin_usaha_id in (1,2,3,99)
					  and b.status_validasi=1
					  and c.user_status=1
					  and a.validasi='1'
            AND b.rekanan_id not in (SELECT rekanan_id FROM blacklist
            where current_date between tanggal_mulai and tanggal_selesai )
			";

		// while(list($key,$val) = each($paramsArray))
		foreach ($paramsArray as $key => $val) {
			$str .= " AND $key = '$val' ";
		}

		$str .= $statement." group by a.rekanan_id HAVING COUNT(DISTINCT A.bidang_usaha_id) = ".$count." ) a";
		// echo $str;
		$this->query = $str;

		return $this->selectLimit($str,$limit,$from);
    }

    function selectCountByParamsBidangUsaha2view($paramsArray=array(),$limit=-1,$from=-1, $statement='',$count)
	{
		$str = "SELECT * from (
				SELECT a.rekanan_id
				from rekanan_bidang_usaha a
					join rekanan b on a.rekanan_id=b.rekanan_id
					join user_login c on a.rekanan_id=c.rekanan_id
					join rekanan_kualifikasi d on b.rekanan_kualifikasi_id=d.rekanan_kualifikasi_id
					join rekanan_tipe e on b.rekanan_tipe_id=e.rekanan_tipe_id
				where a.ijin_usaha_id in (1,2,3,99)
					  and b.status_validasi=1
					  and c.user_status=1
					  and a.validasi='1'
            AND b.rekanan_id not in (SELECT rekanan_id FROM blacklist
            where current_date between tanggal_mulai and tanggal_selesai )
			";

		// while(list($key,$val) = each($paramsArray))
		foreach ($paramsArray as $key => $val) {
			$str .= " AND $key = '$val' ";
		}

		$str .= $statement." group by a.rekanan_id HAVING COUNT(DISTINCT A.bidang_usaha_id) = ".$count." ) a join rekanan b on a.rekanan_id=b.rekanan_id";
		// echo $str;
		$this->query = $str;

		return $this->selectLimit($str,$limit,$from);
    }

    function selectCountByParamsBidangUsahaSortlist($paramsArray=array(),$limit=-1,$from=-1, $statement='')
	{
		$str = "SELECT count(a.bidang_usaha_id) from (
				SELECT a.* from (
					SELECT a.bidang_usaha_id, a.rekanan_id,
					(select max(aa.kontrak_nilai)
					from rekanan_pengalaman aa
					left join rekanan_pengalaman_bidang bb on aa.rekanan_pengalaman_id = bb.rekanan_pengalaman_id
					where bb.bidang_usaha_id is not null and bb.bidang_usaha_id = a.bidang_usaha_id and aa.rekanan_id=a.rekanan_id
					group by aa.rekanan_pengalaman_id,aa.rekanan_id, aa.kontrak_nilai
					order by aa.kontrak_nilai desc
					limit 1) nilai, b.rekanan_kualifikasi_id
									from rekanan_bidang_usaha a
										join rekanan b on a.rekanan_id=b.rekanan_id
										join user_login c on a.rekanan_id=c.rekanan_id
										join rekanan_kualifikasi d on b.rekanan_kualifikasi_id=d.rekanan_kualifikasi_id
										join rekanan_tipe e on b.rekanan_tipe_id=e.rekanan_tipe_id
									where a.ijin_usaha_id in (1,2,3,99)
										  and b.status_validasi=1
										  and c.user_status=1
					  					and a.validasi='1'
                      AND b.rekanan_id not in (SELECT rekanan_id FROM blacklist
                      where current_date between tanggal_mulai and tanggal_selesai )
				) a where 1=1
			";

		// while(list($key,$val) = each($paramsArray))
		foreach ($paramsArray as $key => $val) {
			$pecah = explode("||", $key);
		    if (count($pecah) > 1) {
		        $str .= "AND $pecah[0] $pecah[1] $val ";
		    } else {
		        $str .= " AND $key = '$val' ";
		    }
		}

		$str .= $statement." ) a group by a.bidang_usaha_id";
// 		echo $str;
		/* Catatan
		 	Peserta yang cocok adalah :
		 	1. Punya Bidang Usaha di Table rekanan_bidang_usaha (ambil SIUP, NIB, SIUJK dan SBUJK / value(1,2,3,99) )
		 	2. status sudah di validasi di table rekanan (value=1)
		 	3. status sudah di verifikasi di table user_login (value=1)
		 	4. sesuai dengan kualifikasi usaha (kecil, non-kecil, kecil/non-kecil)
		 	5. mempunyai pengalaman di bidang usaha sesuai dengan bidang usaha yg di input dan Nilai Kontrak >= HPS/3
		*/
		$this->query = $str;

		return $this->selectLimit($str,$limit,$from);
    }

    function selectByParamsBidangUsaha($paramsArray=array(),$limit=-1,$from=-1, $statement='')
	{
		$str = "SELECT a.rekanan_id, a.bidang_usaha_id,
				d.nama rekanan_kualifikasi, b.rekanan_kualifikasi_id, f.nama ijin_usaha,
				b.kode,e.nama rekanan_tipe_char, b.nama,
				case when b.status_validasi = '0' then 'Belum'
					when b.status_validasi = '1' then 'Validasi'
					else 'Hapus'
				end status_validasi_char, b.status_validasi,
				case when c.user_status = '0' then 'Baru/Revisi'
					when c.user_status = '1' then 'Valid'
					else 'Submit berkas'
				end user_status_char, c.user_status
				from rekanan_bidang_usaha a
					join rekanan b on a.rekanan_id=b.rekanan_id
					join user_login c on a.rekanan_id=c.rekanan_id
					join rekanan_kualifikasi d on b.rekanan_kualifikasi_id=d.rekanan_kualifikasi_id
					join rekanan_tipe e on b.rekanan_tipe_id=e.rekanan_tipe_id
					join ijin_usaha f on a.ijin_usaha_id=f.ijin_usaha_id
				where a.ijin_usaha_id in (1,2,3,99)
					  and b.status_validasi=1
					  and c.user_status=1
					  and a.validasi='1'
            AND b.rekanan_id not in (SELECT rekanan_id FROM blacklist
            where current_date between tanggal_mulai and tanggal_selesai )
			";

		// while(list($key,$val) = each($paramsArray))
		foreach ($paramsArray as $key => $val) {
			$str .= " AND $key = '$val' ";
		}

		$str .= $statement." order by a.rekanan_id desc";
		// echo $str;
		$this->query = $str;

		return $this->selectLimit($str,$limit,$from);
    }

    function selectByParamsBidangUsahaSortList($paramsArray=array(),$limit=-1,$from=-1, $statement='')
	{
		$str = "SELECT a.* from (
					SELECT a.rekanan_id, a.bidang_usaha_id,
					d.nama rekanan_kualifikasi, b.rekanan_kualifikasi_id, f.nama ijin_usaha,
					b.kode,e.nama rekanan_tipe_char, b.nama,
					case when b.status_validasi = '0' then 'Belum'
						when b.status_validasi = '1' then 'Validasi'
						else 'Hapus'
					end status_validasi_char, b.status_validasi,
					case when c.user_status = '0' then 'Baru/Revisi'
						when c.user_status = '1' then 'Valid'
						else 'Submit berkas'
					end user_status_char, c.user_status,
					(select max(aa.kontrak_nilai)
						from rekanan_pengalaman aa
						left join rekanan_pengalaman_bidang bb on aa.rekanan_pengalaman_id = bb.rekanan_pengalaman_id
						where bb.bidang_usaha_id is not null and bb.bidang_usaha_id = a.bidang_usaha_id and aa.rekanan_id=a.rekanan_id
						group by aa.rekanan_pengalaman_id,aa.rekanan_id, aa.kontrak_nilai
						order by aa.kontrak_nilai desc
						limit 1) nilai
					from rekanan_bidang_usaha a
						join rekanan b on a.rekanan_id=b.rekanan_id
						join user_login c on a.rekanan_id=c.rekanan_id
						join rekanan_kualifikasi d on b.rekanan_kualifikasi_id=d.rekanan_kualifikasi_id
						join rekanan_tipe e on b.rekanan_tipe_id=e.rekanan_tipe_id
						join ijin_usaha f on a.ijin_usaha_id=f.ijin_usaha_id
					where a.ijin_usaha_id in (1,2,3,99)
						  and b.status_validasi=1
						  and c.user_status=1
					  	and a.validasi='1'
              AND b.rekanan_id not in (SELECT rekanan_id FROM blacklist
              where current_date between tanggal_mulai and tanggal_selesai )
				) a where 1=1

			";

		// while(list($key,$val) = each($paramsArray))
		foreach ($paramsArray as $key => $val) {
			$pecah = explode("||", $key);
		    if (count($pecah) > 1) {
		        $str .= "AND $pecah[0] $pecah[1] $val ";
		    } else {
		        $str .= " AND $key = '$val' ";
		    }
		}

		$str .= $statement." order by a.rekanan_id desc";
		// echo $str;
		$this->query = $str;

		return $this->selectLimit($str,$limit,$from);
    }
  }
?>
