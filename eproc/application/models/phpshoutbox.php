<?php
/**
 * @package 	eProcurement Application
 * @author 		eproc2025
 * @since 		25. Version 3.1
 * 
 */
  
  include_once('entity.php');

  class PhpShoutbox extends Entity{ 

	var $query;
    /**
    * Class constructor.
    **/
    function __construct(){
  		parent::__construct();
  	}
  	
    function PhpShoutbox()
	{
      $this->Entity(); 
    }
	
	function insert()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		//$this->setField("JAM", $this->getNextId("JAM","PHPSHOUTBOX")); 

		$str = "
				INSERT INTO PHPSHOUTBOX (
				   JAM, NAMA, PESAN, 
   					IP_ADDRESS, PAKET_ID, HALAMAN, KODE, NIP) 
				VALUES (
				  '".$this->getField("JAM")."',
				  '".$this->getField("NAMA")."', 
				  '".$this->getField("PESAN")."', 
				  '".$this->getField("IP_ADDRESS")."', 
				  '".$this->getField("PAKET_ID")."', 
				  '".$this->getField("HALAMAN")."', 
				  '".$this->getField("KODE")."',
				  '".$this->getField("NIP")."'
				)"; 
		$this->query = $str;
		return $this->execQuery($str);
    }

    function update()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$str = "UPDATE PHPSHOUTBOX SET
				  NAMA = '".$this->getField("NAMA")."'
				WHERE JAM = '".$this->getField("JAM")."'
				"; 
				$this->query = $str;
		return $this->execQuery($str);
    }
	
	function delete()
	{
        $str = "DELETE FROM PHPSHOUTBOX
                WHERE 
                  JAM = '".$this->getField("JAM")."'"; 
				  
		$this->query = $str;
        return $this->execQuery($str);
    }

	function deleteParentChild()
	{
        $str = "DELETE FROM PHPSHOUTBOX
                WHERE 
                  NAMA = '".$this->getField("NAMA")."'"; 
				  
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
    function selectByParams($paramsArray=array(),$limit=-1,$from=-1, $statement='', $order=' ORDER BY JAM ASC ')
	{
		$str = "SELECT JAM, NAMA, PESAN, 
   					IP_ADDRESS, PAKET_ID, HALAMAN, KODE, TO_CHAR(WAKTU, 'HH24:MI:SS') WAKTU, TO_CHAR(WAKTU, 'DD/MM/YYYY HH24:MI:SS') WAKTU_INFORMASI
				FROM PHPSHOUTBOX A WHERE 1=1 "; 
		//JAM IS NOT NULL
		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}
		
		$str .= $statement." ".$order;
		$this->query = $str;
		return $this->selectLimit($str,$limit,$from); 
	}

    function selectByParamsRekanan($paramsArray=array(),$limit=-1,$from=-1, $statement='', $order=' ORDER BY B.NAMA ASC ')
	{
		$str = "SELECT A.NAMA KODE, (SELECT X.NAMA FROM REKANAN_TIPE X WHERE X.REKANAN_TIPE_ID = B.REKANAN_TIPE_ID)|| '. ' || B.NAMA NAMA
				FROM PHPSHOUTBOX A INNER JOIN REKANAN B ON A.NAMA = B.KODE WHERE 1=1
				 "; 
		//JAM IS NOT NULL
		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}
		
		$str .= $statement." GROUP BY A.NAMA, B.NAMA, B.REKANAN_TIPE_ID ".$order;
		$this->query = $str;
		return $this->selectLimit($str,$limit,$from); 
	}

	function selectByParamsRekananAanwijzing($paramsArray=array(),$limit=-1,$from=-1, $statement='', $order=' ORDER BY A.NAMA ASC ')
	{
		$str = "SELECT A.KODE_REKANAN, A.KODE, A.NAMA, B.KODE_QR FROM
				(
				SELECT A.PAKET_ID, A.NAMA KODE, D.KODE_REKANAN, (SELECT X.NAMA FROM REKANAN_TIPE X WHERE X.REKANAN_TIPE_ID = B.REKANAN_TIPE_ID)|| '. ' || B.NAMA NAMA, C.USER_LOGIN_ID
					FROM PHPSHOUTBOX A 
					INNER JOIN PAKET_REKANAN D ON A.NAMA = D.KODE_REKANAN
					INNER JOIN REKANAN B ON D.REKANAN_ID = B.REKANAN_ID 
					INNER JOIN USER_LOGIN C ON C.REKANAN_ID = B.REKANAN_ID 
					WHERE 1 = 1
				GROUP BY A.PAKET_ID, A.NAMA, D.KODE_REKANAN,B.NAMA, B.REKANAN_TIPE_ID, C.USER_LOGIN_ID
				) A LEFT JOIN PAKET_AANWIJZING_VALIDASI B ON A.USER_LOGIN_ID = B.USER_LOGIN_ID AND A.PAKET_ID = B.PAKET_ID
				WHERE 1 = 1
				 "; 
		//JAM IS NOT NULL
		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}
		
		$str .= $statement."  ".$order;
		// echo $str; die();
		$this->query = $str;
		return $this->selectLimit($str,$limit,$from); 
	}

	// IKN 20190909
	function selectByParamsRekananAanwijzing2($paramsArray=array(),$limit=-1,$from=-1, $statement='', $order=' ORDER BY A.NAMA ASC ')
	{
		$str = "SELECT A.KODE_REKANAN, A.KODE, A.NAMA FROM
				(
				SELECT A.PAKET_ID, A.NAMA KODE, D.KODE_REKANAN, (SELECT X.NAMA FROM REKANAN_TIPE X WHERE X.REKANAN_TIPE_ID = B.REKANAN_TIPE_ID)|| '. ' || B.NAMA NAMA, C.USER_LOGIN_ID
					FROM PHPSHOUTBOX A 
					INNER JOIN PAKET_REKANAN D ON A.NAMA = D.KODE_REKANAN
					INNER JOIN REKANAN B ON D.REKANAN_ID = B.REKANAN_ID 
					INNER JOIN USER_LOGIN C ON C.REKANAN_ID = B.REKANAN_ID 
					WHERE 1 = 1
				GROUP BY A.PAKET_ID, A.NAMA, D.KODE_REKANAN,B.NAMA, B.REKANAN_TIPE_ID, C.USER_LOGIN_ID
				) A LEFT JOIN PAKET_AANWIJZING_VALIDASI B ON A.USER_LOGIN_ID = B.USER_LOGIN_ID AND A.PAKET_ID = B.PAKET_ID
				WHERE 1 = 1
				 "; 
		//JAM IS NOT NULL
		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}
		
		$str .= $statement."  ".$order;
		// echo $str; die();
		$this->query = $str;
		return $this->selectLimit($str,$limit,$from); 
	}


 	function selectByParamsKonfirmasi($paramsArray=array(),$limit=-1,$from=-1, $statement='', $order='')
	{
		$str = "
				SELECT PAKET_ID, KODE, HALAMAN, INFORMASI, KODE_HALAMAN FROM
				(
				SELECT A.PAKET_ID, A.NAMA KODE, HALAMAN, '' INFORMASI, A.KODE KODE_HALAMAN
				FROM PHPSHOUTBOX A INNER JOIN PAKET_REKANAN B ON A.PAKET_ID = B.PAKET_ID AND A.NAMA = B.KODE_REKANAN WHERE 1 = 1 AND PESAN = 'CONFIRMED'
                UNION ALL                
				SELECT PAKET_ID, 'KEHADIRAN' KODE, TO_NUMBER(A.KODE_REKANAN, '9999999999') HALAMAN, TO_CHAR(COALESCE(AANWIJZING, 0), '9999999') INFORMASI, 0 KODE_HALAMAN
				FROM PAKET_REKANAN A INNER JOIN REKANAN B ON A.REKANAN_ID = B.REKANAN_ID  AND A.TANGGAL_DAFTAR IS NOT NULL AND A.LULUS_PENDAFTARAN = 1
				UNION ALL				
                SELECT PAKET_ID, 'PESAN' KODE, HALAMAN, INFORMASI, A.KODE KODE_HALAMAN
				FROM
				(				
                SELECT A.PAKET_ID, HALAMAN, A.KODE, COUNT(1), (SELECT COUNT(1) JUMLAH_REKANAN
			                    FROM PHPSHOUTBOX X INNER JOIN PAKET_REKANAN Y ON X.PAKET_ID = Y.PAKET_ID AND X.NAMA = Y.KODE_REKANAN WHERE 1=1 AND NOT PESAN = 'CONFIRMED' AND X.PAKET_ID = A.PAKET_ID AND X.HALAMAN = A.HALAMAN AND X.KODE = A.KODE AND NOT JAM IS NULL) 
                || '/' || 
                (SELECT COUNT(1) JUMLAH_REKANAN
                                FROM PHPSHOUTBOX X LEFT JOIN PAKET_REKANAN Y ON X.PAKET_ID = Y.PAKET_ID AND X.NAMA = Y.KODE_REKANAN WHERE 1=1 AND NOT PESAN = 'CONFIRMED' AND REKANAN_ID IS NULL AND X.PAKET_ID = A.PAKET_ID AND X.HALAMAN = A.HALAMAN AND X.KODE = A.KODE AND NOT JAM IS NULL)                
                INFORMASI
                FROM PHPSHOUTBOX A INNER JOIN PAKET_REKANAN B ON A.PAKET_ID = B.PAKET_ID AND A.NAMA = B.KODE_REKANAN WHERE 1 = 1 AND NOT PESAN = 'CONFIRMED' AND NOT JAM IS NULL
                GROUP BY A.PAKET_ID, HALAMAN, A.KODE                
                ) A
                ) A WHERE 1 = 1
		";
		
		//JAM IS NOT NULL
		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}
		$str .= $statement." ".$order;
		$this->query = $str;
		return $this->selectLimit($str,$limit,$from); 
	}

 	function selectByParamsKonfirmasiRekanan($paramsArray=array(),$limit=-1,$from=-1, $rekanan_kode='', $order='')
	{
		$str = "
				SELECT PAKET_ID, KODE, HALAMAN, INFORMASI, KODE_HALAMAN FROM
				(
				SELECT A.PAKET_ID, A.NAMA KODE, HALAMAN, '' INFORMASI, A.KODE KODE_HALAMAN
				FROM PHPSHOUTBOX A INNER JOIN PAKET_REKANAN B ON A.PAKET_ID = B.PAKET_ID AND A.NAMA = B.KODE_REKANAN WHERE 1 = 1 AND PESAN = 'CONFIRMED' AND A.NAMA = '".$rekanan_kode."'
				UNION ALL				
                SELECT PAKET_ID, 'PESAN' KODE, HALAMAN, INFORMASI, A.KODE KODE_HALAMAN
				FROM
				(				
                SELECT A.PAKET_ID, HALAMAN, A.KODE, COUNT(1), (SELECT COUNT(1) JUMLAH_REKANAN
								FROM PHPSHOUTBOX X INNER JOIN PAKET_REKANAN Y ON X.PAKET_ID = Y.PAKET_ID AND X.NAMA = Y.KODE_REKANAN WHERE 1=1 AND NOT PESAN = 'CONFIRMED' AND X.PAKET_ID = A.PAKET_ID AND X.HALAMAN = A.HALAMAN AND X.KODE = A.KODE AND X.NAMA = '".$rekanan_kode."' AND NOT JAM IS NULL) 
                || '/' || 
                (SELECT COUNT(1) JUMLAH_REKANAN
                                FROM PHPSHOUTBOX X LEFT JOIN PAKET_REKANAN Y ON X.PAKET_ID = Y.PAKET_ID AND X.NAMA = Y.KODE_REKANAN WHERE 1=1 AND NOT PESAN = 'CONFIRMED' AND REKANAN_ID IS NULL AND X.PAKET_ID = A.PAKET_ID AND X.HALAMAN = A.HALAMAN AND X.KODE = A.KODE AND NOT JAM IS NULL)                
                INFORMASI
                FROM PHPSHOUTBOX A INNER JOIN PAKET_REKANAN B ON A.PAKET_ID = B.PAKET_ID AND A.NAMA = B.KODE_REKANAN WHERE 1 = 1 AND NOT PESAN = 'CONFIRMED' AND NOT JAM IS NULL
                GROUP BY A.PAKET_ID, HALAMAN, A.KODE                
                ) A
                ) A WHERE 1 = 1	
		";
		
		//JAM IS NOT NULL
		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}
		$str .= " ".$order;
		//echo $str;
		$this->query = $str;
		return $this->selectLimit($str,$limit,$from); 
	}
		    
	function selectByParamsLike($paramsArray=array(),$limit=-1,$from=-1, $statement='')
	{
		$str = "SELECT JAM, NAMA
				FROM PHPSHOUTBOX WHERE JAM IS NOT NULL"; 
		
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
    * @param array paramsArray Array of parameter. Contoh array("id"=>"xxx","NAMA"=>"yyy") 
    * @return long Jumlah record yang sesuai kriteria 
    **/ 
    function getCountByParams($paramsArray=array())
	{
		$str = "SELECT COUNT(JAM) AS ROWCOUNT FROM PHPSHOUTBOX WHERE JAM IS NOT NULL "; 
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

    function getPesanMasuk($paket_id, $halaman, $kode)
	{
		$str = "
				SELECT A.JUMLAH_REKANAN || '/' || B.JUMLAH_PANITIA PESAN FROM
				(
				SELECT COUNT(1) JUMLAH_REKANAN
								FROM PHPSHOUTBOX A INNER JOIN REKANAN B ON A.NAMA = B.KODE WHERE 1=1 AND PAKET_ID = ".$paket_id." AND HALAMAN = ".$halaman." AND A.KODE = '".$kode."' AND NOT PESAN = 'CONFIRMED'
				) A,
				(
				SELECT COUNT(1) JUMLAH_PANITIA
								FROM PHPSHOUTBOX A LEFT JOIN REKANAN B ON A.NAMA = B.KODE WHERE 1=1 AND PAKET_ID = ".$paket_id." AND HALAMAN = ".$halaman." AND A.KODE = '".$kode."' AND REKANAN_ID IS NULL AND NOT PESAN = 'CONFIRMED'
				) B
		 "; 
		$this->select($str); 
		if($this->firstRow()) 
			return $this->getField("PESAN"); 
		else 
			return "0/0"; 
    }

    function getPesanMasukRekanan($paket_id, $halaman, $kode, $rekanan_kode)
	{
		$str = "
				SELECT A.JUMLAH_REKANAN || '/' || B.JUMLAH_PANITIA PESAN FROM
				(
				SELECT COUNT(1) JUMLAH_REKANAN
								FROM PHPSHOUTBOX A INNER JOIN REKANAN B ON A.NAMA = B.KODE WHERE 1=1 AND PAKET_ID = ".$paket_id." AND HALAMAN = ".$halaman." AND A.KODE = '".$kode."' AND A.NAMA = '".$rekanan_kode."' AND NOT PESAN = 'CONFIRMED'
				) A,
				(
				SELECT COUNT(1) JUMLAH_PANITIA
								FROM PHPSHOUTBOX A LEFT JOIN REKANAN B ON A.NAMA = B.KODE WHERE 1=1 AND PAKET_ID = ".$paket_id." AND HALAMAN = ".$halaman." AND A.KODE = '".$kode."' AND REKANAN_ID IS NULL AND NOT PESAN = 'CONFIRMED'
				) B
		 "; 
		$this->select($str); 
		if($this->firstRow()) 
			return $this->getField("PESAN"); 
		else 
			return "0/0"; 
    }

    function getCountByParamsLike($paramsArray=array())
	{
		$str = "SELECT COUNT(JAM) AS ROWCOUNT FROM PHPSHOUTBOX WHERE JAM IS NOT NULL "; 
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