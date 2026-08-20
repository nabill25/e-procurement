<?php 
/**
 * @package 	eProcurement Application
 * @author 		eproc2025
 * @since 		25. Version 3.1
 * 
 */
  
  include_once('entity.php');

  class Paketpemenangpurchasing extends Entity { 

	var $query; 

	function insert()
	{ 
		/*Auto-generate primary key(s) by next max value (integer) */
		$this->setField("PAKET_PEMENANG_PURCHASING_ID", $this->getNextId("PAKET_PEMENANG_PURCHASING_ID","PAKET_PEMENANG_PURCHASING")); 

		$str = "
			INSERT INTO PAKET_PEMENANG_PURCHASING (
			   PAKET_PEMENANG_PURCHASING_ID, REKANAN_ID, PAKET_ID, 
			   NAMA, NPWP, TELEPON, ALAMAT, EMAIL, JENIS, CREATED_BY, CREATED_DATE, NILAI_PEMBELIAN
			   )
  			 	VALUES (
				  ".$this->getField("PAKET_PEMENANG_PURCHASING_ID").",
  				".$this->getField("REKANAN_ID").",
   				".$this->getField("PAKET_ID").",
				  '".$this->getField("NAMA")."',
				  '".$this->getField("NPWP")."',
				  '".$this->getField("TELEPON")."',
				  '".$this->getField("ALAMAT")."',
				  '".$this->getField("EMAIL")."',
				  '".$this->getField("JENIS")."',
				  ".$this->getField("CREATED_BY").",
				  CURRENT_TIMESTAMP,
				  ".$this->getField("NILAI_PEMBELIAN")."
				)"; 
				
		$this->query = $str;
		return $this->execQuery($str);
  }

  function update()
	{ 
		$str = "
   	UPDATE  PAKET_PEMENANG_PURCHASING
    SET
         REKANAN_ID        = ".$this->getField("REKANAN_ID").",
         PAKET_ID        = ".$this->getField("PAKET_ID").",
         NAMA        = '".$this->getField("NAMA")."',
         NPWP        = '".$this->getField("NPWP")."',
         TELEPON        = '".$this->getField("TELEPON")."',
         ALAMAT        = '".$this->getField("ALAMAT")."',
         EMAIL        = '".$this->getField("EMAIL")."',
         JENIS        = '".$this->getField("JENIS")."',
         UPDATED_BY    = ".$this->getField("CREATED_BY").",
         NILAI_PEMBELIAN    = ".$this->getField("NILAI_PEMBELIAN").",
         UPDATED_DATE  = CURRENT_TIMESTAMP
    WHERE  PAKET_ID =  ".$this->getField("PAKET_ID")." AND CREATED_BY =  ".$this->getField("CREATED_BY")."
    "; 
    // echo $str;
    $this->query = $str;
    if ($this->execQuery($str)) { 
      return TRUE;
    } else { 
      return FALSE;
    }
  }
 
	function delete()
	{
    $str = "DELETE FROM PAKET_PEMENANG_PURCHASING
            WHERE PAKET_PEMENANG_PURCHASING_ID = ".$this->getField("PAKET_PEMENANG_PURCHASING_ID").""; 
				  
		$this->query = $str;
    return $this->execQuery($str);
  }
 
  function selectByParams($paramsArray=array(),$limit=-1,$from=-1, $statement='')
	{
		$str = "
					SELECT * FROM PAKET_PEMENANG_PURCHASING A
				    WHERE A.PAKET_ID IS NOT NULL "; 
		
		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}
		$this->query = $str;
			
		$str .= $statement." ORDER BY A.PAKET_PEMENANG_PURCHASING_ID ASC";
				// echo $str; die();
		return $this->selectLimit($str,$limit,$from); 
  }

} 
?>