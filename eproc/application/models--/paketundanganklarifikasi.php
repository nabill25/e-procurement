<?php
/**
 * @package 	eProcurement Application
 * @author 		eproc2025
 * @since 		25. Version 3.1
 * 
 */

include_once('entity.php');

class Paketundanganklarifikasi extends Entity{ 

	var $query;

    function __construct(){
	  parent::__construct();
	}
	
	function insert()
	{
		$this->setField("PAKET_UNDANGAN_KLARIFIKASI_ID", $this->getNextId("PAKET_UNDANGAN_KLARIFIKASI_ID","PAKET_UNDANGAN_KLARIFIKASI")); 

		$str = "
		
			INSERT INTO PAKET_UNDANGAN_KLARIFIKASI (
			   PAKET_UNDANGAN_KLARIFIKASI_ID, PAKET_ID, REKANAN_ID, TANGGAL_UNDANGAN, TEMPAT, PELAKSANAAN,
			   KETERANGAN, PESERTA, CREATED_BY, CREATED_DATE, JAM)
 
  			 	VALUES (
				  '".$this->getField("PAKET_UNDANGAN_KLARIFIKASI_ID")."',
  				  ".$this->getField("PAKET_ID").",
  				  ".$this->getField("REKANAN_ID").",
  				  ".$this->getField("TANGGAL_UNDANGAN").",
   				  '".$this->getField("TEMPAT")."',
   				  '".$this->getField("PELAKSANAAN")."',
   				  '".$this->getField("KETERANGAN")."',
   				  '".$this->getField("PESERTA")."',
				  ".$this->getField("CREATED_BY").", 
				  CURRENT_TIMESTAMP,
				  '".$this->getField("JAM")."'
				)"; 
				
		$this->query = $str;
		$this->id = $this->getField("PAKET_UNDANGAN_KLARIFIKASI_ID");
		//echo $str;exit;
		return $this->execQuery($str);
    }


    function update()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$str = "		
				 UPDATE  PAKET_UNDANGAN_KLARIFIKASI
				SET    
					   TANGGAL_UNDANGAN  = ".$this->getField("TANGGAL_UNDANGAN").",
					   TEMPAT      = '".$this->getField("TEMPAT")."', 
					   JAM      = '".$this->getField("JAM")."', 
					   PELAKSANAAN      = '".$this->getField("PELAKSANAAN")."', 
					   KETERANGAN      = '".$this->getField("KETERANGAN")."', 
					   PESERTA      = '".$this->getField("PESERTA")."', 
					   UPDATED_BY      = '".$this->getField("CREATED_BY")."', 
					   UPDATED_DATE	   =  CURRENT_TIMESTAMP
				WHERE  PAKET_ID   =  ".$this->getField("PAKET_ID")." AND REKANAN_ID = ".$this->getField("REKANAN_ID")."
			  
				"; 
				$this->query = $str;
		return $this->execQuery($str);
    }
	
	function delete()
	{
        $str = "DELETE FROM PAKET_UNDANGAN_KLARIFIKASI
                WHERE 
                  PAKET_UNDANGAN_KLARIFIKASI_ID = ".$this->getField("PAKET_UNDANGAN_KLARIFIKASI_ID").""; 
				  
		$this->query = $str;
        return $this->execQuery($str);
    }
 
    function selectByParams($paramsArray=array(),$limit=-1,$from=-1, $statement='', $order="")
	{
		$str = "
					SELECT * FROM PAKET_UNDANGAN_KLARIFIKASI
				    WHERE PAKET_UNDANGAN_KLARIFIKASI_ID IS NOT NULL "; 
		
		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}
		$this->query = $str;
			
		$str .= $statement." ".$order;
				
		return $this->selectLimit($str,$limit,$from); 
    }

   
    function getCountByParams($paramsArray=array(), $statement='')
	{
		$str = "SELECT COUNT(A.PAKET_UNDANGAN_KLARIFIKASI_ID) AS ROWCOUNT 
					FROM PAKET_UNDANGAN_KLARIFIKASI A
					WHERE 1 = 1".$statement; 
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

  } 
?>