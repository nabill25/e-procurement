<?php 
/**
 * @package 	eProcurement Application
 * @author 		eproc2025
 * @since 		25. Version 3.1
 * 
 */
  
  include_once('entity.php');

  class Paketkajiulang extends Entity{ 

	var $query;
    function __construct(){
  		parent::__construct();
	}
	
	function insert()
	{
		$this->setField("PAKET_KAJI_ULANG_ID", $this->getNextId("PAKET_KAJI_ULANG_ID","PAKET_KAJI_ULANG"));

		$str = "
			INSERT INTO PAKET_KAJI_ULANG (PAKET_KAJI_ULANG_ID, PERMOHONAN_PAKET_ID, NAMA,
					   UKURAN, TIPE, PATH_FILE,TANGGAL_UPLOAD, KETERANGAN,STATUS, PARENT_ID, USER_LOGIN_ID, CREATED_BY, CREATED_DATE)
			 	VALUES (
					   ".$this->getField("PAKET_KAJI_ULANG_ID").", 
					   '".$this->getField("PERMOHONAN_PAKET_ID")."', 
					   '".$this->getField("NAMA")."',
					   '".$this->getField("UKURAN")."', 
					   '".$this->getField("TIPE")."', 
					   '".$this->getField("PATH_FILE")."',
					   NOW(), 
					   '".$this->getField("KETERANGAN")."',
					   1, ".$this->getField("PARENT_ID").", 
					   ".$this->getField("USER_LOGIN_ID").", 
					   ".$this->getField("CREATED_BY").", 
					   CURRENT_TIMESTAMP
				)";

		$this->query = $str;
		// echo $str;die();
		return $this->execQuery($str);
  }

  function insertNoFile()
	{
		$this->setField("PAKET_KAJI_ULANG_ID", $this->getNextId("PAKET_KAJI_ULANG_ID","PAKET_KAJI_ULANG"));

		$str = "
			INSERT INTO PAKET_KAJI_ULANG (PAKET_KAJI_ULANG_ID, PERMOHONAN_PAKET_ID, KETERANGAN,STATUS, PARENT_ID, USER_LOGIN_ID, CREATED_BY, CREATED_DATE)
			 	VALUES (
					   ".$this->getField("PAKET_KAJI_ULANG_ID").", 
					   '".$this->getField("PERMOHONAN_PAKET_ID")."', 
					   '".$this->getField("KETERANGAN")."',
					   1, ".$this->getField("PARENT_ID").", 
					   ".$this->getField("USER_LOGIN_ID").", 
					   ".$this->getField("CREATED_BY").", 
					   CURRENT_TIMESTAMP
				)";

		$this->query = $str;
		// echo $str;die();
		return $this->execQuery($str);
   }
 
  function update()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$str = "UPDATE PAKET_KAJI_ULANG SET
				  NAMA = '".$this->getField("NAMA")."',
				  UKURAN = '".$this->getField("UKURAN")."',
				  TIPE = '".$this->getField("TIPE")."',
				  PATH_FILE = '".$this->getField("PATH_FILE")."',
				  TANGGAL_UPLOAD = NOW(),
				  KETERANGAN = '".$this->getField("KETERANGAN")."'
				WHERE PAKET_KAJI_ULANG_ID = '".$this->getField("PAKET_KAJI_ULANG_ID")."'
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
        $str = "DELETE FROM PAKET_KAJI_ULANG
                WHERE 
                  PAKET_KAJI_ULANG_ID = '".$this->getField("PAKET_KAJI_ULANG_ID")."'"; 
		$this->query = $str;
        return $this->execQuery($str);
    }

	function deleteParentChild()
	{
        $str = "DELETE FROM PAKET_KAJI_ULANG
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
    function selectByParams($paramsArray=array(),$limit=-1,$from=-1, $statement='', $order=" ORDER BY PAKET_KAJI_ULANG_ID ASC ")
	{
		$str = "SELECT A.*, C.USER_NAMA
				FROM PAKET_KAJI_ULANG A  
				JOIN USER_LOGIN C ON A.CREATED_BY=C.USER_LOGIN_ID
				WHERE 1=1 "; 
		//PAKET_KAJI_ULANG_ID IS NOT NULL
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
  
    function getCountByParams($paramsArray=array())
	{
		$str = "SELECT COUNT(PAKET_KAJI_ULANG_ID) AS ROWCOUNT FROM PAKET_KAJI_ULANG A WHERE PAKET_KAJI_ULANG_ID IS NOT NULL "; 
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
 
  } 
?>