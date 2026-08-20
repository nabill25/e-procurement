<?php 
/**
 * @package 	eProcurement Application
 * @author 		eproc2025
 * @since 		25. Version 3.1
 * 
 */
  
  include_once('entity.php');

  class RekananTenagaAhliPengalaman extends Entity{ 

	var $query;
    /**
    * Class constructor.
    **/
    function RekananTenagaAhliPengalaman()
	{
      $this->Entity(); 
    }
	
	function insert()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$this->setField("REKANAN_TENAGA_AHLI_PENG_ID", $this->getNextId("REKANAN_TENAGA_AHLI_PENG_ID","REKANAN_TENAGA_AHLI_PENG")); 

		$str = "
				INSERT INTO REKANAN_TENAGA_AHLI_PENG (
					REKANAN_TENAGA_AHLI_PENG_ID, 
					REKANAN_TENAGA_AHLI_ID, 
					POSISI, 
					PENGALAMAN, PEKERJAAN, PERIODE, INSTANSI, NAMA_PERUSAHAAN, CREATED_BY, CREATED_DATE)
				VALUES ( ".$this->getField("REKANAN_TENAGA_AHLI_PENG_ID").", 
					".$this->getField("REKANAN_TENAGA_AHLI_ID").",
					'".$this->getField("POSISI")."', 
					".$this->getField("PENGALAMAN").",
					'".$this->getField("PEKERJAAN")."',
					".$this->getField("PERIODE").",
					'".$this->getField("INSTANSI")."',
					'".$this->getField("NAMA_PERUSAHAAN")."',
					".$this->getField("CREATED_BY").",
					CURRENT_TIMESTAMP
					)
		"; 
				// echo $str; die();
		$this->query = $str;
		return $this->execQuery($str);
    }

    function update()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$str = "UPDATE REKANAN_TENAGA_AHLI_PENG 
				SET
					REKANAN_TENAGA_AHLI_ID = '".$this->getField("REKANAN_TENAGA_AHLI_ID")."',
					POSISI = '".$this->getField("POSISI")."',
					PENGALAMAN = '".$this->getField("PENGALAMAN")."',
					PEKERJAAN = '".$this->getField("PEKERJAAN")."',
					PERIODE = '".$this->getField("PERIODE")."',
					INSTANSI = '".$this->getField("INSTANSI")."',
					NAMA_PERUSAHAAN = '".$this->getField("NAMA_PERUSAHAAN")."',
					UPDATED_BY = ".$this->getField("CREATED_BY").",
					UPDATED_DATE = CURRENT_TIMESTAMP
				WHERE REKANAN_TENAGA_AHLI_PENG_ID = '".$this->getField("REKANAN_TENAGA_AHLI_PENG_ID")."'
				"; 
				$this->query = $str;
		return $this->execQuery($str);
    }
	
	function delete()
	{
        $str = "DELETE FROM REKANAN_TENAGA_AHLI_PENG A
                WHERE 
                  REKANAN_TENAGA_AHLI_PENG_ID = '".$this->getField("REKANAN_TENAGA_AHLI_PENG_ID")."' 
				  AND EXISTS(SELECT 1 FROM REKANAN_TENAGA_AHLI X WHERE X.REKANAN_TENAGA_AHLI_ID = A.REKANAN_TENAGA_AHLI_ID 
				  	AND X.REKANAN_ID = '".$this->getField("REKANAN_ID")."')"; 
				  
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
				REKANAN_TENAGA_AHLI_PENG_ID, 
				REKANAN_TENAGA_AHLI_ID, 
				POSISI, 
				PENGALAMAN, PEKERJAAN, PERIODE, INSTANSI, NAMA_PERUSAHAAN
			FROM REKANAN_TENAGA_AHLI_PENG
			WHERE REKANAN_TENAGA_AHLI_PENG_ID IS NOT NULL		
		"; 
		
		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}
		
		$this->query = $str;
		$str .= $statement." ORDER BY REKANAN_TENAGA_AHLI_PENG_ID ASC";
				
		return $this->selectLimit($str,$limit,$from); 
    }
    
    function selectByParamsExtended($paramsArray=array(),$limit=-1,$from=-1, $statement='')
	{
		$str = "
			SELECT 
				REKANAN_TENAGA_AHLI_PENG_ID, 
				REKANAN_TENAGA_AHLI_ID, 
				POSISI, 
				PENGALAMAN, PEKERJAAN, PERIODE, INSTANSI, NAMA_PERUSAHAAN
			FROM REKANAN_TENAGA_AHLI_PENG
			WHERE REKANAN_TENAGA_AHLI_PENG_ID IS NOT NULL		
		"; 
		
		while(list($key,$val) = each($paramsArray))
		{
                    if(is_array($val))
                    {
                        $opr = $val[2] ? $val[2]:"=";
                        $str .= $val[1] ? ("AND {$key} {$opr} '{$val[0]}'") : ("AND {$key} {$opr} {$val[0]}");
                    }
                    else
                    {
                        $str .= " AND $key = '$val' ";
                    }
		}
		
		$this->query = $str;
		$str .= $statement." ORDER BY REKANAN_TENAGA_AHLI_PENG_ID ASC";
				// echo $str;
		return $this->selectLimit($str,$limit,$from); 
    }
    
	function selectByParamsLike($paramsArray=array(),$limit=-1,$from=-1, $statement='')
	{
		$str = "
			SELECT 
				REKANAN_TENAGA_AHLI_PENG_ID, 
				REKANAN_TENAGA_AHLI_ID, 
				POSISI, 
				PENGALAMAN
			FROM REKANAN_TENAGA_AHLI_PENG
			WHERE REKANAN_TENAGA_AHLI_PENG_ID IS NOT NULL		
		"; 
		
		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key LIKE '%$val%' ";
		}
		
		$this->query = $str;
		$str .= $statement." ORDER BY REKANAN_TENAGA_AHLI_PENG_ID ASC";
		return $this->selectLimit($str,$limit,$from); 
    }	
    /** 
    * Hitung jumlah record berdasarkan parameter (array). 
    * @param array paramsArray Array of parameter. Contoh array("id"=>"xxx","NAMA"=>"yyy") 
    * @return long Jumlah record yang sesuai kriteria 
    **/ 
    function getCountByParams($paramsArray=array())
	{		
		$str = "SELECT COUNT(REKANAN_TENAGA_AHLI_PENG_ID) AS ROWCOUNT FROM REKANAN_TENAGA_AHLI_PENG WHERE REKANAN_TENAGA_AHLI_PENG_ID IS NOT NULL "; 
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
		$str = "SELECT COUNT(REKANAN_TENAGA_AHLI_PENG_ID) AS ROWCOUNT FROM REKANAN_TENAGA_AHLI_PENG WHERE REKANAN_TENAGA_AHLI_PENG_ID IS NOT NULL "; 
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