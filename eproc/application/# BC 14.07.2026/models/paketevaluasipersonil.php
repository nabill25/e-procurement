<?php 
/**
 * @package 	eProcurement Application
 * @author 		eproc2025
 * @since 		25. Version 3.1
 * 
 */
  
  include_once('entity.php');

  class PaketEvaluasiPersonil extends Entity{ 

	var $query;
    /**
    * Class constructor.
    **/
    function PaketEvaluasiPersonil()
	{
      $this->Entity(); 
    }
	
	function insert()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$this->setField("PAKET_EVAL_PERSONIL_ID", $this->getNextId("PAKET_EVAL_PERSONIL_ID","PAKET_EVAL_PERSONIL")); 

		$str = "
				INSERT INTO PAKET_EVAL_PERSONIL (
				   PAKET_EVAL_PERSONIL_ID, PAKET_ID, JABATAN, 
				   PENDIDIKAN, PENGALAMAN, JUMLAH, 
				   NILAI, NILAI_MINIMUM, SKA, CV, NILAI_MINIMAL)
				VALUES (".$this->getField("PAKET_EVAL_PERSONIL_ID").", ".$this->getField("PAKET_ID").", '".$this->getField("JABATAN")."', 
				   '".$this->getField("PENDIDIKAN")."', '".$this->getField("PENGALAMAN")."', '".$this->getField("JUMLAH")."',
				   '".$this->getField("NILAI")."', '".$this->getField("NILAI_MINIMUM")."', '".$this->getField("SKA")."', '".$this->getField("CV")."', '".$this->getField("NILAI_MINIMAL")."' )"; 
			
		$this->query = $str;
		return $this->execQuery($str);
    }

    function updateData()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$str = "UPDATE PAKET_EVAL_PERSONIL SET
				  JABATAN = '".$this->getField("JABATAN")."',
				  PENDIDIKAN = '".$this->getField("PENDIDIKAN")."',
				  PENGALAMAN = '".$this->getField("PENGALAMAN")."',
				  JUMLAH = '".$this->getField("JUMLAH")."',
				  NILAI = '".$this->getField("NILAI")."',
				  NILAI_MINIMUM = '".$this->getField("NILAI_MINIMUM")."',
				  NILAI_MINIMAL = '".$this->getField("NILAI_MINIMAL")."',
				  SKA = '".$this->getField("SKA")."',
				  CV = '".$this->getField("CV")."'
				WHERE PAKET_EVAL_PERSONIL_ID = '".$this->getField("PAKET_EVAL_PERSONIL_ID")."'
				"; 
				$this->query = $str;
		return $this->execQuery($str);
    }
	
	function update()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$str = "UPDATE PAKET_EVAL_PERSONIL SET
				  NILAI_MINIMUM = '".$this->getField("NILAI_MINIMUM")."'
				WHERE PAKET_ID = '".$this->getField("PAKET_ID")."'
				"; 
				$this->query = $str;
		return $this->execQuery($str);
    }
	
	function delete()
	{
        $str = "DELETE FROM PAKET_EVAL_PERSONIL
                WHERE 
                  PAKET_ID = '".$this->getField("PAKET_ID")."'"; 
				  
		$this->query = $str;
        return $this->execQuery($str);
    }
	
	function deleteIn()
	{
            if($this->getField("PAKET_EVAL_PERSONIL_ID")=='')
            {
                $notInString ='';
            }
            else
            {
                $notInString = " AND PAKET_EVAL_PERSONIL_ID NOT IN (".$this->getField("PAKET_EVAL_PERSONIL_ID").")";
            }
        $str = "DELETE FROM PAKET_EVAL_PERSONIL
                WHERE 
                  PAKET_ID = '".$this->getField("PAKET_ID")."' ".$notInString ; 
				  
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
		$str = "SELECT PAKET_EVAL_PERSONIL_ID, PAKET_ID, JABATAN, 
                   PENDIDIKAN, PENGALAMAN, JUMLAH, 
                   NILAI, NILAI_MINIMUM, NILAI_MINIMAL, SKA, CV, (SELECT NAMA FROM PENDIDIKAN X WHERE X.PENDIDIKAN_ID = PENDIDIKAN) PENDIDIKAN_NAMA,
                   (SELECT SUM(JUMLAH) FROM PAKET_EVAL_PERSONIL X WHERE X.PAKET_ID = A.PAKET_ID) JUMLAH_PERSONIL,
                   (SELECT COUNT(PAKET_EVAL_PERSONIL_ID) FROM REKANAN_EVAL_PERSONIL X WHERE X.PAKET_EVAL_PERSONIL_ID = A.PAKET_EVAL_PERSONIL_ID) JUMLAH_ENTRI
                FROM PAKET_EVAL_PERSONIL A WHERE PAKET_EVAL_PERSONIL_ID IS NOT NULL"; 
		
		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}
		
		$this->query = $str;
		$str .= $statement." ORDER BY PAKET_EVAL_PERSONIL_ID ASC";
				
		return $this->selectLimit($str,$limit,$from); 
    }
    
	function selectByParamsLike($paramsArray=array(),$limit=-1,$from=-1, $statement='')
	{
		$str = "SELECT PAKET_EVAL_PERSONIL_ID, NAMA
				FROM PAKET_EVAL_PERSONIL WHERE PAKET_EVAL_PERSONIL_ID IS NOT NULL"; 
		
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
		$str = "SELECT COUNT(PAKET_EVAL_PERSONIL_ID) AS ROWCOUNT FROM PAKET_EVAL_PERSONIL WHERE PAKET_EVAL_PERSONIL_ID IS NOT NULL "; 
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
		$str = "SELECT COUNT(PAKET_EVAL_PERSONIL_ID) AS ROWCOUNT FROM PAKET_EVAL_PERSONIL WHERE PAKET_EVAL_PERSONIL_ID IS NOT NULL "; 
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