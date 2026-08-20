<?php 
/**
 * @package 	eProcurement Application
 * @author 		eproc2025
 * @since 		25. Version 3.1
 * 
 */
  
  include_once('entity.php');

  class PenilaianRekananTemplate extends Entity{ 

	var $query;
    /**
    * Class constructor.
    **/
    function PenilaianRekananTemplate()
	{
      $this->Entity(); 
    }
	
	function insert()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		//$this->setField("PENILAIAN_REKANAN_TEMPLATE_ID", $this->getNextId("PENILAIAN_REKANAN_TEMPLATE_ID","PENILAIAN_REKANAN_TEMPLATE")); 

		$str = "
				INSERT INTO PENILAIAN_REKANAN_TEMPLATE (
				   PENILAIAN_REKANAN_TEMPLATE_ID, PRT_PARENT_ID, 
   					KODE, NAMA) 
				VALUES (
				  (SELECT PENILAIAN_REK_TEMP_GENERATE('".$this->getField("PENILAIAN_REKANAN_TEMPLATE_ID")."') FROM DUAL),
				  '".$this->getField("PRT_PARENT_ID")."', 
				  '".$this->getField("KODE")."', 
				  '".$this->getField("NAMA")."'
				)"; 
				
		$this->query = $str;
		return $this->execQuery($str);
    }

    function update()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$str = "UPDATE PENILAIAN_REKANAN_TEMPLATE SET
				  NAMA = '".$this->getField("NAMA")."'
				WHERE PENILAIAN_REKANAN_TEMPLATE_ID = '".$this->getField("PENILAIAN_REKANAN_TEMPLATE_ID")."'
				"; 
				$this->query = $str;
		return $this->execQuery($str);
    }
	
	function delete()
	{
        $str = "DELETE FROM PENILAIAN_REKANAN_TEMPLATE
                WHERE 
                  PENILAIAN_REKANAN_TEMPLATE_ID = '".$this->getField("PENILAIAN_REKANAN_TEMPLATE_ID")."'"; 
				  
		$this->query = $str;
        return $this->execQuery($str);
    }

	function deleteParentChild()
	{
        $str = "DELETE FROM PENILAIAN_REKANAN_TEMPLATE
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
    function selectByParams($paramsArray=array(),$limit=-1,$from=-1, $statement='')
	{
		$str = "SELECT PENILAIAN_REKANAN_TEMPLATE_ID, PRT_PARENT_ID, 
   					KODE, NAMA, NILAI, PROSENTASE
				FROM PENILAIAN_REKANAN_TEMPLATE WHERE 1=1 "; 
		//PENILAIAN_REKANAN_TEMPLATE_ID IS NOT NULL
		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}
		
		$str .= $statement." ORDER BY PENILAIAN_REKANAN_TEMPLATE_ID asc ";
		$this->query = $str;
		return $this->selectLimit($str,$limit,$from); 
    }

    function selectByParamsRoom($paramsArray=array(),$limit=-1,$from=-1, $statement='')
	{
		$str = "SELECT   (SELECT 'BAB ' || KODE
							FROM PENILAIAN_REKANAN_TEMPLATE X
						   WHERE X.PENILAIAN_REKANAN_TEMPLATE_ID = A.PRT_PARENT_ID) || ' - Pasal ' || KODE NAMA
					FROM PENILAIAN_REKANAN_TEMPLATE A
				   WHERE NOT PRT_PARENT_ID = 0
				"; 
		
		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}
		
		$this->query = $str;
		$str .= $statement." ORDER BY PENILAIAN_REKANAN_TEMPLATE_ID ASC ";
				
		return $this->selectLimit($str,$limit,$from); 
    }
    
	function selectByParamsLike($paramsArray=array(),$limit=-1,$from=-1, $statement='')
	{
		$str = "SELECT PENILAIAN_REKANAN_TEMPLATE_ID, NAMA
				FROM PENILAIAN_REKANAN_TEMPLATE WHERE PENILAIAN_REKANAN_TEMPLATE_ID IS NOT NULL"; 
		
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
		$str = "SELECT COUNT(PENILAIAN_REKANAN_TEMPLATE_ID) AS ROWCOUNT FROM PENILAIAN_REKANAN_TEMPLATE WHERE PENILAIAN_REKANAN_TEMPLATE_ID IS NOT NULL "; 
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
		$str = "SELECT COUNT(PENILAIAN_REKANAN_TEMPLATE_ID) AS ROWCOUNT FROM PENILAIAN_REKANAN_TEMPLATE WHERE PENILAIAN_REKANAN_TEMPLATE_ID IS NOT NULL "; 
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