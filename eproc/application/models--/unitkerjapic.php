<?php 
/**
 * @package 	eProcurement Application
 * @author 		eproc2025
 * @since 		25. Version 3.1
 * 
 */
  
  include_once('entity.php');

  class UnitKerjaPic extends Entity{ 

	var $query;
    /**
    * Class constructor.
    **/
    function UnitKerjaPic()
	{
      $this->Entity(); 
    }
	
	function insert()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$this->setField("UNIT_KERJA_PIC_ID", $this->getNextId("UNIT_KERJA_PIC_ID","UNIT_KERJA_PIC")); 

		$str = "
				INSERT INTO UNIT_KERJA_PIC (
					   UNIT_KERJA_PIC_ID, UNIT_KERJA_ID, NIP, 
					   NAMA) 
					VALUES ( '".$this->getField("UNIT_KERJA_PIC_ID")."', '".$this->getField("UNIT_KERJA_ID")."', '".$this->getField("NIP")."', 
					'".$this->getField("NAMA")."')
		"; 
				
		$this->id = $this->getField("UNIT_KERJA_PIC_ID");
		$this->query = $str;
		return $this->execQuery($str);
    }

    function update()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$str = "
				UPDATE UNIT_KERJA_PIC
				SET    UNIT_KERJA_ID     = '".$this->getField("UNIT_KERJA_ID")."',
					   NIP               = '".$this->getField("NIP")."',
					   NAMA              = '".$this->getField("NAMA")."'
				WHERE  UNIT_KERJA_PIC_ID = '".$this->getField("UNIT_KERJA_PIC_ID")."'
				"; 
				$this->query = $str;
		return $this->execQuery($str);
    }
	
	function delete()
	{
        $str = "DELETE FROM UNIT_KERJA_PIC
                WHERE 
                  UNIT_KERJA_PIC_ID = '".$this->getField("UNIT_KERJA_PIC_ID")."'"; 
				  
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
			UNIT_KERJA_PIC_ID, UNIT_KERJA_ID, NIP, 
			   NAMA
			FROM UNIT_KERJA_PIC A
			WHERE UNIT_KERJA_PIC_ID IS NOT NULL		
		"; 
		
		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}
		
		$this->query = $str;
		$str .= $statement." ORDER BY UNIT_KERJA_ID ASC";
				
		return $this->selectLimit($str,$limit,$from); 
    }
    
	function selectByParamsLike($paramsArray=array(),$limit=-1,$from=-1, $statement='')
	{
		$str = "
			SELECT 
			UNIT_KERJA_PIC_ID, UNIT_KERJA_ID, NIP, 
			   NAMA
			FROM UNIT_KERJA_PIC A
			WHERE UNIT_KERJA_ID IS NOT NULL		
		"; 
		
		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key LIKE '%$val%' ";
		}
		
		$this->query = $str;
		$str .= $statement." ORDER BY UNIT_KERJA_ID ASC";
		return $this->selectLimit($str,$limit,$from); 
    }	
    /** 
    * Hitung jumlah record berdasarkan parameter (array). 
    * @param array paramsArray Array of parameter. Contoh array("id"=>"xxx","NAMA"=>"yyy") 
    * @return long Jumlah record yang sesuai kriteria 
    **/ 
    function getCountByParams($paramsArray=array())
	{		
		$str = "SELECT COUNT(UNIT_KERJA_PIC_ID) AS ROWCOUNT FROM UNIT_KERJA_PIC WHERE UNIT_KERJA_PIC_ID IS NOT NULL "; 
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
		$str = "SELECT COUNT(UNIT_KERJA_PIC_ID) AS ROWCOUNT FROM UNIT_KERJA_PIC WHERE UNIT_KERJA_PIC_ID IS NOT NULL "; 
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