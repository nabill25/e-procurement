<?php
/**
 * @package 	eProcurement Application
 * @author 		eproc2025
 * @since 		25. Version 3.1
 *
 */

  include_once('entity.php');

  class RekananIjinUsaha extends Entity{

	var $query;
    /**
    * Class constructor.
    **/
    function __construct(){
  		parent::__construct();
  	}

    function RekananIjinUsaha()
	{
      $this->Entity();
    }

	function insert()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$this->setField("REKANAN_IJIN_USAHA_ID", $this->getNextId("REKANAN_IJIN_USAHA_ID","REKANAN_IJIN_USAHA"));

		$str = "

				INSERT INTO  REKANAN_IJIN_USAHA (
					   REKANAN_IJIN_USAHA_ID, IJIN_USAHA_ID,
					   NO_IJIN, TANGGAL, TANGGAL_BERAKHIR, REKANAN_ID,
					   INSTANSI)
 			  	VALUES (
				  ".$this->getField("REKANAN_IJIN_USAHA_ID").",
				  ".$this->getField("IJIN_USAHA_ID").",
				  '".$this->getField("NO_IJIN")."',
   				  ".$this->getField("TANGGAL").",
				  ".$this->getField("TANGGAL_BERAKHIR").",
				  '".$this->getField("REKANAN_ID")."',
				  '".$this->getField("INSTANSI")."')";
				//echo $str;
		$this->query = $str;
		$this->id = $this->getField("REKANAN_IJIN_USAHA_ID");
		return $this->execQuery($str);
    }

	function insert_onedha()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$this->setField("REKANAN_IJIN_USAHA_ID", $this->getNextId("REKANAN_IJIN_USAHA_ID","REKANAN_IJIN_USAHA"));

		$str = "

				INSERT INTO  REKANAN_IJIN_USAHA (
					   REKANAN_IJIN_USAHA_ID, IJIN_USAHA_ID,
					   NO_IJIN, TANGGAL, REKANAN_ID,
					   INSTANSI,
					   NAMA_FILE, PATH_FILE, TIPE, UKURAN, CREATED_BY, CREATED_DATE, PKKPR, TANGGAL_PKKPR, TANGGAL_PKKPR_BERAKHIR, PATH_FILE2)
 			  	VALUES (
				  ".$this->getField("REKANAN_IJIN_USAHA_ID").",
				  ".$this->getField("IJIN_USAHA_ID").",
				  '".$this->getField("NO_IJIN")."',
   				  ".$this->getField("TANGGAL").", 
				  '".$this->getField("REKANAN_ID")."',
				  '".$this->getField("INSTANSI")."',
				  '".$this->getField("NAMA_FILE")."',
				  '".$this->getField("PATH_FILE")."',
				  '".$this->getField("TIPE")."',
				  '".$this->getField("UKURAN")."',
          		  ".$this->getField("CREATED_BY").",
				  CURRENT_TIMESTAMP,
          		  '".$this->getField("PKKPR")."',
          		  ".$this->getField("TANGGAL_PKKPR").",
          		  ".$this->getField("TANGGAL_PKKPR_BERAKHIR").",
          		  '".$this->getField("PATH_FILE2")."'
				  )";
				//
		$this->query = $str;
		//echo $str;exit;
		$this->id = $this->getField("REKANAN_IJIN_USAHA_ID");
		return $this->execQuery($str);
    }

    function insert_onedha2()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$this->setField("REKANAN_IJIN_USAHA_ID", $this->getNextId("REKANAN_IJIN_USAHA_ID","REKANAN_IJIN_USAHA"));

		$str = "

				INSERT INTO  REKANAN_IJIN_USAHA (
					   REKANAN_IJIN_USAHA_ID, IJIN_USAHA_ID,
					   NO_IJIN, TANGGAL, TANGGAL_BERAKHIR, REKANAN_ID,
					   INSTANSI,
					   NAMA_FILE, PATH_FILE, TIPE, UKURAN, CREATED_BY, CREATED_DATE)
 			  	VALUES (
				  ".$this->getField("REKANAN_IJIN_USAHA_ID").",
				  ".$this->getField("IJIN_USAHA_ID").",
				  '".$this->getField("NO_IJIN")."',
   				  ".$this->getField("TANGGAL").", 
   				  ".$this->getField("TANGGAL_BERAKHIR").", 
				  '".$this->getField("REKANAN_ID")."',
				  '".$this->getField("INSTANSI")."',
				  '".$this->getField("NAMA_FILE")."',
				  '".$this->getField("PATH_FILE")."',
				  '".$this->getField("TIPE")."',
				  '".$this->getField("UKURAN")."',
          		  ".$this->getField("CREATED_BY").",
				  CURRENT_TIMESTAMP
				  )";
				//
		$this->query = $str;
		//echo $str;exit;
		$this->id = $this->getField("REKANAN_IJIN_USAHA_ID");
		return $this->execQuery($str);
    }

    function update()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$str = "
				UPDATE  REKANAN_IJIN_USAHA
				SET
					   NO_IJIN          	  = '".$this->getField("NO_IJIN")."',
					   TANGGAL        		  = '".$this->getField("TANGGAL")."',
					   TANGGAL_BERAKHIR 	  = '".$this->getField("TANGGAL_BERAKHIR")."',
					   INSTANSI               = '".$this->getField("INSTANSI")."',
						NAMA_FILE = '".$this->getField("NAMA_FILE")."',
						PATH_FILE = '".$this->getField("PATH_FILE")."',
						TIPE = '".$this->getField("TIPE")."',
						UKURAN = '".$this->getField("UKURAN")."'
				WHERE  REKANAN_ID  			  = ".$this->getField("REKANAN_ID")."

			 ";
				$this->query = $str;
				//echo $str;
		return $this->execQuery($str);
    }

	function update_onedha()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$str = "
				UPDATE  REKANAN_IJIN_USAHA
				SET
					   NO_IJIN          	  = '".$this->getField("NO_IJIN")."',
					   TANGGAL        		  = ".$this->getField("TANGGAL").",
					   TANGGAL_BERAKHIR 	  = ".$this->getField("TANGGAL_BERAKHIR").",
					   INSTANSI               = '".$this->getField("INSTANSI")."',
						NAMA_FILE = '".$this->getField("NAMA_FILE")."',
						PATH_FILE = '".$this->getField("PATH_FILE")."',
						TIPE = '".$this->getField("TIPE")."',
						UKURAN = '".$this->getField("UKURAN")."',
						UPDATED_BY = ".$this->getField("CREATED_BY").",
						UPDATED_DATE = CURRENT_TIMESTAMP,
						PKKPR = '".$this->getField("PKKPR")."',
						PATH_FILE2 = '".$this->getField("PATH_FILE2")."',
						TANGGAL_PKKPR = ".$this->getField("TANGGAL_PKKPR").",
						TANGGAL_PKKPR_BERAKHIR = ".$this->getField("TANGGAL_PKKPR_BERAKHIR")."
				WHERE  REKANAN_ID  			  = ".$this->getField("REKANAN_ID")." AND IJIN_USAHA_ID = ".$this->getField("IJIN_USAHA_ID")."

			 ";
				$this->query = $str;
				//echo $str;
		return $this->execQuery($str);
    }

	function update_registrasi()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$str = "
				UPDATE  REKANAN_IJIN_USAHA
				SET
					   NO_IJIN          	  = '".$this->getField("NO_IJIN")."',
					   TANGGAL        		  = ".$this->getField("TANGGAL").",
					   TANGGAL_BERAKHIR 	  = ".$this->getField("TANGGAL_BERAKHIR").",
					   INSTANSI               = '".$this->getField("INSTANSI")."',
						NAMA_FILE 			  = '".$this->getField("NAMA_FILE")."',
						PATH_FILE 			  = '".$this->getField("PATH_FILE")."',
						TIPE 				  = '".$this->getField("TIPE")."',
						UKURAN 				  = '".$this->getField("UKURAN")."',
						IJIN_USAHA_ID 		  = ".$this->getField("IJIN_USAHA_ID")."
				WHERE  REKANAN_ID  			  = ".$this->getField("REKANAN_ID")." AND
					   IJIN_USAHA_ID  	 	  = ".$this->getField("IJIN_USAHA_ID_TEMP")."

			 ";
				$this->query = $str;
				//echo $str;
		return $this->execQuery($str);
    }

	function update_onedhav2()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$str = "
				UPDATE  REKANAN_IJIN_USAHA
				SET
			    NO_IJIN          	  = '".$this->getField("NO_IJIN")."',
			    TANGGAL        		  = ".$this->getField("TANGGAL").",
			    TANGGAL_BERAKHIR 	  = ".$this->getField("TANGGAL_BERAKHIR").",
			    INSTANSI               = '".$this->getField("INSTANSI")."',
				  PATH_FILE 			  = '".$this->getField("PATH_FILE")."',
					TIPE 				  = '".$this->getField("TIPE")."',
          UKURAN 				  = '".$this->getField("UKURAN")."',
					NAMA_FILE 			  = '".$this->getField("NAMA_FILE")."',
					UPDATED_BY 			  = ".$this->getField("CREATED_BY").",
					UPDATED_DATE 			  = CURRENT_TIMESTAMP,
          NAMA_PEMEGANG 				  = '".$this->getField("NAMA_PEMEGANG")."'
				WHERE  REKANAN_ID  		 		= ".$this->getField("REKANAN_ID")." AND
					   IJIN_USAHA_ID  	 		= ".$this->getField("IJIN_USAHA_ID")." AND
					   REKANAN_IJIN_USAHA_ID  	= ".$this->getField("REKANAN_IJIN_USAHA_ID")."

			 ";
				$this->query = $str;
				//echo $str;
		return $this->execQuery($str);
    }


	function delete()
	{
        $str = "DELETE FROM REKANAN_IJIN_USAHA
                WHERE
                  REKANAN_IJIN_USAHA_ID = ".$this->getField("REKANAN_IJIN_USAHA_ID")."";

		$this->query = $str;
        return $this->execQuery($str);
    }

    function deleteIjin($str='')
	{
        $str = "DELETE FROM REKANAN_IJIN_USAHA
                WHERE
                  REKANAN_ID = '".$this->getField("REKANAN_ID")."' ".$str;

		$this->query = $str;
		//echo $str;exit;
        return $this->execQuery($str);
    }

    /**
    * Cari record berdasarkan array parameter dan limit tampilan
    * @param array paramsArray Array of parameter. Contoh array("id"=>"xxx","IJIN_USAHA_ID"=>"yyy")
    * @param int limit Jumlah maksimal record yang akan diambil
    * @param int from Awal record yang diambil
    * @return boolean True jika sukses, false jika tidak
    **/
    function selectByParams($paramsArray=array(),$limit=-1,$from=-1, $statement='')
	{
		/* MENCEGAH HALAMAN LAIN ERROR SEHINGGA DIBUAT BEDA DENGAN  IJIN_USAHA_ID_RELASI */
		$str = "
				SELECT
				REKANAN_IJIN_USAHA_ID, R.IJIN_USAHA_ID,
			   NO_IJIN, TO_CHAR(TANGGAL, 'YYYY-MM-DD')TANGGAL, TO_CHAR(TANGGAL_BERAKHIR, 'YYYY-MM-DD')TANGGAL_BERAKHIR,  REKANAN_ID,
			   INSTANSI, S.NAMA IJIN_USAHA,
   				PATH_FILE, TIPE, UKURAN, NAMA_FILE,
                   TO_DATE(TO_CHAR(CURRENT_DATE, 'YYYY-MM-DD'),'YYYY-MM-DD') now,
				   CASE SIGN((EXTRACT(epoch FROM (SELECT (TANGGAL_BERAKHIR - TO_DATE(TO_CHAR(CURRENT_DATE, 'YYYY-MM-DD'),'YYYY-MM-DD'))))/86400))
							WHEN -1 THEN 'Ijin ' || S.NAMA || ' usaha anda telah berakhir'
							WHEN 0 THEN 'Ijin ' || S.NAMA || ' usaha anda akan berakhir besok' END INFO_TANGGAL_BERAKHIR, NAMA_PEMEGANG,
							PKKPR, PATH_FILE2, TANGGAL_PKKPR, TANGGAL_PKKPR_BERAKHIR
                FROM REKANAN_IJIN_USAHA R
                     LEFT JOIN (SELECT IJIN_USAHA_ID IJIN_USAHA_ID_RELASI, NAMA FROM IJIN_USAHA) S
         			 ON R.IJIN_USAHA_ID = S.IJIN_USAHA_ID_RELASI
                WHERE REKANAN_IJIN_USAHA_ID IS NOT NULL ";

		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}

		$this->query = $str;
		$str .= $statement." ORDER BY R.IJIN_USAHA_ID ASC";
		//echo $str;
		return $this->selectLimit($str,$limit,$from);
    }

    function selectByParamsRekananBidangUsaha($paramsArray=array(),$limit=-1,$from=-1, $statement='')
	{
		$str = "
				SELECT AMBIL_BIDANG_USAHA_NAMA(BIDANG_USAHA_ID) NAMA FROM REKANAN_IJIN_USAHA_BIDANG_USAHA WHERE REKANAN_IJIN_USAHA_BIDANG_USAHA_ID IS NOT NULL ";

		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}

		$this->query = $str;
		$str .= $statement." ORDER BY REKANAN_IJIN_USAHA_BIDANG_USAHA_ID ASC";
		//echo $str;
		return $this->selectLimit($str,$limit,$from);
    }

	function selectByParamsLike($paramsArray=array(),$limit=-1,$from=-1, $statement='')
	{
		$str = " SELECT
				REKANAN_IJIN_USAHA_ID, IJIN_USAHA_ID, REKANAN_IJIN_USAHA_TIPE_ID,
				   REKANAN_IJIN_USAHA_KUALIFIKASI_ID, KODE, NAMA,
				   NPWP, ALAMAT, TELEPON_KODE,
				   TELEPON, FAX_KODE, FAX,
				   EMAIL, ALAMAT_PUSAT, TELEPON_KODE_PUSAT,
				   TELEPON_PUSAT, FAX_KODE_PUSAT, FAX_PUSAT,
				   EMAIL_PUSAT, STATUS_PERUSAHAAN, STATUS_VALIDASI,
				   STATUS_CP, TANGGAL_DAFTAR, TANGGAL_VALIDASI,
				   PKP, PKP_TANGGAL, KOTA,
				   SURAT_KUASA
				FROM  REKANAN_IJIN_USAHA
		        WHERE REKANAN_IJIN_USAHA_ID IS NOT NULL";

		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key LIKE '%$val%' ";
		}

		$this->query = $str;
		$str .= $statement." ORDER BY NAMA ASC";
		return $this->selectLimit($str,$limit,$from);
    }
    /**
    * Hitung jumlah record berdasarkan parameter (array).
    * @param array paramsArray Array of parameter. Contoh array("id"=>"xxx","IJIN_USAHA_ID"=>"yyy")
    * @return long Jumlah record yang sesuai kriteria
    **/
    function getCountByParams($paramsArray=array(), $stat='')
	{
		$str = "SELECT COUNT(REKANAN_IJIN_USAHA_ID) AS ROWCOUNT FROM REKANAN_IJIN_USAHA WHERE REKANAN_IJIN_USAHA_ID IS NOT NULL ".$stat;
		while(list($key,$val)=each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}

		$str .= $stat;

		$this->select($str);
		if($this->firstRow())
			return $this->getField("ROWCOUNT");
		else
			return 0;
    }

    function getCountByParamsLike($paramsArray=array())
	{
		$str = "SELECT COUNT(REKANAN_IJIN_USAHA_ID) AS ROWCOUNT FROM REKANAN_IJIN_USAHA WHERE REKANAN_IJIN_USAHA_ID IS NOT NULL ";
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
