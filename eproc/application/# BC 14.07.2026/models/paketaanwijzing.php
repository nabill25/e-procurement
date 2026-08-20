<?php 
/**
 * @package 	eProcurement Application
 * @author 		eproc2025
 * @since 		25. Version 3.1
 * 
 */
  
  include_once('entity.php');

  class PaketAanwijzing extends Entity{ 

	var $query;
    /**
    * Class constructor.
    **/ 
    function __construct(){
  		parent::__construct();
	}
	
	function insert()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$this->setField("PAKET_AANWIJZING_ID", $this->getNextId("PAKET_AANWIJZING_ID","PAKET_AANWIJZING"));

		$str = "
			INSERT INTO PAKET_AANWIJZING (
   			           PAKET_AANWIJZING_ID, PAKET_ID, NAMA,
					   UKURAN, TIPE, PATH_FILE,
					   TANGGAL_UPLOAD, KETERANGAN,
					   STATUS, REKANAN_USER_ID, REKANAN_KODE, PARENT_ID, USER_LOGIN_ID, CREATED_BY, CREATED_DATE)
			 	VALUES (
					   ".$this->getField("PAKET_AANWIJZING_ID").", '".$this->getField("PAKET_ID")."', '".$this->getField("NAMA")."',
					   '".$this->getField("UKURAN")."', '".$this->getField("TIPE")."', '".$this->getField("PATH_FILE")."',
					   NOW(), '".$this->getField("KETERANGAN")."',
					   1, ".$this->getField("REKANAN_USER_ID").", '".$this->getField("REKANAN_KODE")."', ".$this->getField("PARENT_ID").", ".$this->getField("USER_LOGIN_ID").", ".$this->getField("CREATED_BY").", CURRENT_TIMESTAMP
				)";

		$this->query = $str;
		// echo $str;die();
		return $this->execQuery($str);
  }

  function insertNoFile()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$this->setField("PAKET_AANWIJZING_ID", $this->getNextId("PAKET_AANWIJZING_ID","PAKET_AANWIJZING"));

		$str = "
			INSERT INTO PAKET_AANWIJZING (
   			           PAKET_AANWIJZING_ID, PAKET_ID,  
					   TANGGAL_UPLOAD, KETERANGAN,
					   STATUS, REKANAN_USER_ID, REKANAN_KODE, PARENT_ID, USER_LOGIN_ID, CREATED_BY, CREATED_DATE)
			 	VALUES (
					   ".$this->getField("PAKET_AANWIJZING_ID").", '".$this->getField("PAKET_ID")."', 
					   NOW(), '".$this->getField("KETERANGAN")."',
					   1, ".$this->getField("REKANAN_USER_ID").", '".$this->getField("REKANAN_KODE")."', ".$this->getField("PARENT_ID").", ".$this->getField("USER_LOGIN_ID").", ".$this->getField("CREATED_BY").", CURRENT_TIMESTAMP
				)";

		$this->query = $str;
		// echo $str;die();
		return $this->execQuery($str);
   }

  function insertRekanan()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$this->setField("PAKET_AANWIJZING_ID", $this->getNextId("PAKET_AANWIJZING_ID","PAKET_AANWIJZING"));

		$str = "
			INSERT INTO PAKET_AANWIJZING (
   			           PAKET_AANWIJZING_ID, PAKET_ID,  
					   TANGGAL_UPLOAD, KETERANGAN,
					   STATUS, REKANAN_USER_ID, REKANAN_KODE, PARENT_ID, CREATED_BY, CREATED_DATE)
			 	VALUES (
					   ".$this->getField("PAKET_AANWIJZING_ID").", '".$this->getField("PAKET_ID")."', NOW(), 
					   '".$this->getField("KETERANGAN")."',
					   1, ".$this->getField("REKANAN_USER_ID").", '".$this->getField("REKANAN_KODE")."', ".$this->getField("PARENT_ID").", ".$this->getField("CREATED_BY").", CURRENT_TIMESTAMP
				)";

		$this->query = $str;
		// echo $str;die();
		return $this->execQuery($str);
  }

  function update()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$str = "UPDATE PAKET_AANWIJZING SET
				  NAMA = '".$this->getField("NAMA")."',
				  UKURAN = '".$this->getField("UKURAN")."',
				  TIPE = '".$this->getField("TIPE")."',
				  PATH_FILE = '".$this->getField("PATH_FILE")."',
				  TANGGAL_UPLOAD = NOW(),
				  KETERANGAN = '".$this->getField("KETERANGAN")."'
				WHERE PAKET_AANWIJZING_ID = '".$this->getField("PAKET_AANWIJZING_ID")."'
				"; 
				$this->query = $str;
		return $this->execQuery($str);
    }

  function updateTanggapan()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$str = "UPDATE PAKET_AANWIJZING SET
				  NAMA = '".$this->getField("NAMA")."',
				  UKURAN = '".$this->getField("UKURAN")."',
				  TIPE = '".$this->getField("TIPE")."',
				  PATH_FILE = '".$this->getField("PATH_FILE")."',
				  TANGGAL_UPLOAD = NOW(),
				  KETERANGAN = '".$this->getField("KETERANGAN")."',
				  CREATED_BY = ".$this->getField("CREATED_BY").",
				  CREATED_DATE = CURRENT_TIMESTAMP
				WHERE PAKET_AANWIJZING_ID = '".$this->getField("PAKET_AANWIJZING_ID")."'
				"; 
				$this->query = $str;
		return $this->execQuery($str);
  }

  function updateTanggapanNoFile()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$str = "UPDATE PAKET_AANWIJZING SET
				  KETERANGAN = '".$this->getField("KETERANGAN")."',
				  CREATED_BY = ".$this->getField("CREATED_BY").",
				  CREATED_DATE = CURRENT_TIMESTAMP
				WHERE PAKET_AANWIJZING_ID = '".$this->getField("PAKET_AANWIJZING_ID")."'
				"; 
				$this->query = $str;
		return $this->execQuery($str);
  }

    function updateByField()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$str = "UPDATE AANWIJZING A SET
				  ".$this->getField("FIELD")." = ".$this->getField("FIELD_VALUE")."
				WHERE PAKET_ID = ".$this->getField("PAKET_ID")."
				"; 
				$this->query = $str;
	
		return $this->execQuery($str);
    }
		
	function delete()
	{
        $str = "DELETE FROM PAKET_AANWIJZING
                WHERE 
                  PAKET_AANWIJZING_ID = '".$this->getField("PAKET_AANWIJZING_ID")."'"; 
		$this->query = $str;
        return $this->execQuery($str);
    }

  function deletekualifikasi()
	{
        $str = "DELETE FROM PAKET_AANWIJZING_KUALIFIKASI
                WHERE 
                  PAKET_AANWIJZING_KUALIFIKASI_ID = '".$this->getField("PAKET_AANWIJZING_KUALIFIKASI_ID")."'"; 
		$this->query = $str;
        return $this->execQuery($str);
    }

  function deleteKualifikasiParentChild()
	{
        $str = "DELETE FROM PAKET_AANWIJZING_KUALIFIKASI
                WHERE 
                  PARENT_ID = '".$this->getField("PAKET_AANWIJZING_KUALIFIKASI_ID")."'"; 
				  
		$this->query = $str;
        return $this->execQuery($str);
    }

	function deleteParentChild()
	{
        $str = "DELETE FROM PAKET_AANWIJZING
                WHERE 
                  PAKET_ID = '".$this->getField("PAKET_ID")."'"; 
				  
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
  function selectByParams($paramsArray=array(),$limit=-1,$from=-1, $statement='', $order=" ORDER BY PAKET_AANWIJZING_ID ASC ")
	{
		$str = "SELECT REPLACE(A.REKANAN_KODE,SUBSTR(A.REKANAN_KODE, 8),'*********') KODE_CUT, A.*, C.NAMA || ' ' || B.NAMA NAMA_PENYEDIA
						FROM PAKET_AANWIJZING A 
						LEFT JOIN REKANAN B ON A.REKANAN_USER_ID = B.REKANAN_ID 
						LEFT JOIN REKANAN_TIPE C ON  B.REKANAN_TIPE_ID = C.REKANAN_TIPE_ID
						WHERE 1=1 "; 
		//PAKET_AANWIJZING_ID IS NOT NULL
		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}
		
		$str .= $statement." ".$order;
		// echo $str;
		$this->query = $str;
				//CAST(KODE AS INT)
				//." ORDER BY KODE ASC"
			
		return $this->selectLimit($str,$limit,$from); 
  }

  function selectByParamsKualifikasi($paramsArray=array(),$limit=-1,$from=-1, $statement='', $order=" ORDER BY PAKET_AANWIJZING_KUALIFIKASI_ID ASC ")
	{
		$str = "SELECT REPLACE(A.REKANAN_KODE,SUBSTR(A.REKANAN_KODE, 8),'*********') KODE_CUT, A.*
						FROM PAKET_AANWIJZING_KUALIFIKASI A WHERE 1=1 "; 
		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}
		
		$str .= $statement." ".$order;
		// echo $str;
		$this->query = $str;
			
		return $this->selectLimit($str,$limit,$from); 
  }

  function insertKualifikasiRekanan()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$this->setField("PAKET_AANWIJZING_KUALIFIKASI_ID", $this->getNextId("PAKET_AANWIJZING_KUALIFIKASI_ID","PAKET_AANWIJZING_KUALIFIKASI"));

		$str = "
			INSERT INTO PAKET_AANWIJZING_KUALIFIKASI (
   			           PAKET_AANWIJZING_KUALIFIKASI_ID, PAKET_ID,  
					   TANGGAL_UPLOAD, KETERANGAN,
					   STATUS, REKANAN_USER_ID, REKANAN_KODE, PARENT_ID, CREATED_BY, CREATED_DATE)
			 	VALUES (
					   ".$this->getField("PAKET_AANWIJZING_KUALIFIKASI_ID").", '".$this->getField("PAKET_ID")."', NOW(), 
					   '".$this->getField("KETERANGAN")."',
					   1, ".$this->getField("REKANAN_USER_ID").", '".$this->getField("REKANAN_KODE")."', ".$this->getField("PARENT_ID").", ".$this->getField("CREATED_BY").", CURRENT_TIMESTAMP
				)";

		$this->query = $str;
		// echo $str;die();
		return $this->execQuery($str);
  }

  function insertKualifikasi()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$this->setField("PAKET_AANWIJZING_KUALIFIKASI_ID", $this->getNextId("PAKET_AANWIJZING_KUALIFIKASI_ID","PAKET_AANWIJZING_KUALIFIKASI"));

		$str = "
			INSERT INTO PAKET_AANWIJZING_KUALIFIKASI (
   			           PAKET_AANWIJZING_KUALIFIKASI_ID, PAKET_ID, NAMA,
					   UKURAN, TIPE, PATH_FILE,
					   TANGGAL_UPLOAD, KETERANGAN,
					   STATUS, REKANAN_USER_ID, REKANAN_KODE, PARENT_ID, USER_LOGIN_ID, CREATED_BY, CREATED_DATE)
			 	VALUES (
					   ".$this->getField("PAKET_AANWIJZING_KUALIFIKASI_ID").", '".$this->getField("PAKET_ID")."', '".$this->getField("NAMA")."',
					   '".$this->getField("UKURAN")."', '".$this->getField("TIPE")."', '".$this->getField("PATH_FILE")."',
					   NOW(), '".$this->getField("KETERANGAN")."',
					   1, ".$this->getField("REKANAN_USER_ID").", '".$this->getField("REKANAN_KODE")."', ".$this->getField("PARENT_ID").", ".$this->getField("USER_LOGIN_ID").", ".$this->getField("CREATED_BY").", CURRENT_TIMESTAMP
				)";

		$this->query = $str;
		// echo $str;die();
		return $this->execQuery($str);
  }

  function insertKualifikasiNoFile()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$this->setField("PAKET_AANWIJZING_KUALIFIKASI_ID", $this->getNextId("PAKET_AANWIJZING_KUALIFIKASI_ID","PAKET_AANWIJZING_KUALIFIKASI"));

		$str = "
			INSERT INTO PAKET_AANWIJZING_KUALIFIKASI (
   			           PAKET_AANWIJZING_KUALIFIKASI_ID, PAKET_ID,  
					   TANGGAL_UPLOAD, KETERANGAN,
					   STATUS, REKANAN_USER_ID, REKANAN_KODE, PARENT_ID, USER_LOGIN_ID, CREATED_BY, CREATED_DATE)
			 	VALUES (
					   ".$this->getField("PAKET_AANWIJZING_KUALIFIKASI_ID").", '".$this->getField("PAKET_ID")."', 
					   NOW(), '".$this->getField("KETERANGAN")."',
					   1, ".$this->getField("REKANAN_USER_ID").", '".$this->getField("REKANAN_KODE")."', ".$this->getField("PARENT_ID").", ".$this->getField("USER_LOGIN_ID").", ".$this->getField("CREATED_BY").", CURRENT_TIMESTAMP
				)";

		$this->query = $str;
		// echo $str;die();
		return $this->execQuery($str);
   }

  function selectByParamsPeserta($paramsArray=array(), $order=" ORDER BY PAKET_AANWIJZING_ID ASC ")
	{
		$str = "SELECT A.REKANAN_KODE
						FROM PAKET_AANWIJZING A WHERE 1=1 AND A.REKANAN_KODE IS NOT NULL AND A.REKANAN_KODE <> '' "; 
		//PAKET_AANWIJZING_ID IS NOT NULL
		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}
		
		$str .= $statement." ".$order;
		// echo $str;
		$this->query = $str;
				//CAST(KODE AS INT)
				//." ORDER BY KODE ASC"
			
		return $this->selectLimit($str,$limit,$from); 
  }

  function selectByParamsPeserta2($paramsArray=array(), $order=" ORDER BY PAKET_AANWIJZING_ID ASC ")
	{
		$str = "SELECT A.REKANAN_KODE, B.NAMA
				FROM PAKET_AANWIJZING A JOIN REKANAN B ON A.REKANAN_USER_ID=B.REKANAN_ID
				WHERE 1=1 AND A.REKANAN_KODE IS NOT NULL AND A.REKANAN_KODE <> '' "; 
		//PAKET_AANWIJZING_ID IS NOT NULL
		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}
		
		$str .= $statement." ".$order;
		// echo $str;
		$this->query = $str;
				//CAST(KODE AS INT)
				//." ORDER BY KODE ASC"
			
		return $this->selectLimit($str,$limit,$from); 
  }

  function selectByParamsPeserta3($paramsArray=array(), $order=" ORDER BY PAKET_AANWIJZING_ID ASC ")
	{
		$str = "SELECT REPLACE(A.REKANAN_KODE,SUBSTR(A.REKANAN_KODE, 8),'*********') KODE_CUT, A.REKANAN_KODE, C.NAMA || ' ' || B.NAMA NAMA_PENYEDIA, A.REKANAN_USER_ID 
			FROM ( 
						SELECT A.REKANAN_KODE, A.REKANAN_USER_ID
						FROM PAKET_AANWIJZING A WHERE 1=1 AND A.REKANAN_KODE IS NOT NULL AND A.REKANAN_KODE <> '' 
						 "; 
		//PAKET_AANWIJZING_ID IS NOT NULL
		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}
		
		$str .= $statement." ".$order.") A LEFT JOIN REKANAN B ON A.REKANAN_USER_ID = B.REKANAN_ID 
						LEFT JOIN REKANAN_TIPE C ON  B.REKANAN_TIPE_ID = C.REKANAN_TIPE_ID";
		// echo $str;
		$this->query = $str;
				//CAST(KODE AS INT)
				//." ORDER BY KODE ASC"
			
		return $this->selectLimit($str,$limit,$from); 
  }

    function selectByParamsRoom($paramsArray=array(),$limit=-1,$from=-1, $statement='')
	{
		$str = "SELECT   (SELECT 'BAB ' || KODE
							FROM PAKET_AANWIJZING X
						   WHERE X.PAKET_AANWIJZING_ID = A.PARENT_ID) || ' - Pasal ' || KODE NAMA
					FROM PAKET_AANWIJZING A
				   WHERE NOT PARENT_ID = 0
				"; 
		
		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}
		
		$this->query = $str;
		$str .= $statement." ORDER BY PAKET_AANWIJZING_ID ASC ";
				
		return $this->selectLimit($str,$limit,$from); 
    }
    
	function selectByParamsLike($paramsArray=array(),$limit=-1,$from=-1, $statement='')
	{
		$str = "SELECT PAKET_AANWIJZING_ID, NAMA
				FROM PAKET_AANWIJZING WHERE PAKET_AANWIJZING_ID IS NOT NULL"; 
		
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
		$str = "SELECT COUNT(PAKET_AANWIJZING_ID) AS ROWCOUNT FROM PAKET_AANWIJZING A WHERE PAKET_AANWIJZING_ID IS NOT NULL "; 
		while(list($key,$val)=each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}
		// echo $str; die(); 

		$this->select($str); 
		$this->query = $str;
		// echo $str; exit();
		if($this->firstRow()) 
			return $this->getField("ROWCOUNT"); 
		else 
			return 0;  
    }

    function getCountByParamsLike($paramsArray=array())
	{
		$str = "SELECT COUNT(PAKET_AANWIJZING_ID) AS ROWCOUNT FROM PAKET_AANWIJZING WHERE PAKET_AANWIJZING_ID IS NOT NULL "; 
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