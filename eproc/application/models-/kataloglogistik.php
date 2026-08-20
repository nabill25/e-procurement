<?php
/**
 * @package 	eProcurement Application
 * @author 		eproc2025
 * @since 		25. Version 3.1
 * 
 */
  include_once('entity.php');

  class Kataloglogistik extends Entity{ 

	var $query; 

    function __construct(){
  		parent::__construct();
	}
	
	function insert()
	{
	 	$this->setField("LOGISTIKID", $this->getNextId("LOGISTIKID","KATALOG_LOGISTIK"));
		$str = "
		INSERT INTO  KATALOG_LOGISTIK (
		   LOGISTIKID, PAKET_ID, CREATED_BY, CREATED_DATE) 
 			 	VALUES (
				  '".$this->getField("LOGISTIKID")."',
  				  '".$this->getField("PAKET_ID")."',
				  '".$this->getField("CREATED_BY")."',
  				  NOW()
				)"; 
				// echo $str; die();
		$this->query = $str;
		return $this->execQuery($str);
    }   

    function updateongkir()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$str = "UPDATE KATALOG_LOGISTIK SET
				  ONGKOS_KIRIM = '".$this->getField("ONGKOS_KIRIM")."',
				  UPDATED_BY = '".$this->getField("UPDATED_BY")."', 
				  UPDATED_DATE = NOW()
				WHERE PAKET_ID = '".$this->getField("PAKET_ID")."'
				"; 
				// echo $str; die();
				$this->query = $str;
				
		return $this->execQuery($str);
    }

    function updateSP()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$str = "UPDATE KATALOG_LOGISTIK SET
				  FILE_SURAT_PESANAN = '".$this->getField("FILE_SURAT_PESANAN")."',
				  PATH_FILE_SURAT_PESANAN = '".$this->getField("PATH_FILE_SURAT_PESANAN")."',
				  UPDATED_BY = '".$this->getField("UPDATED_BY")."', 
				  UPDATED_DATE = NOW()
				WHERE PAKET_ID = '".$this->getField("PAKET_ID")."'
				"; 
				// echo $str; die();
				$this->query = $str;
				
		return $this->execQuery($str);
    }

  function updateBuktiKirim()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$str = "UPDATE KATALOG_LOGISTIK SET
				  FILE_BUKTI_KIRIM = '".$this->getField("FILE_BUKTI_KIRIM")."',
				  PATH_FILE_BUKTI_KIRIM = '".$this->getField("PATH_FILE_BUKTI_KIRIM")."',
				  ESTIMASI_SAMPAI = ".$this->getField("ESTIMASI_SAMPAI").",
				  UPDATED_BY = '".$this->getField("UPDATED_BY")."', 
				  UPDATED_DATE = NOW()
				WHERE PAKET_ID = '".$this->getField("PAKET_ID")."'
				"; 
				// echo $str; die();
				$this->query = $str;
				
		return $this->execQuery($str);
  }

  function updateBuktiTerima()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$str = "UPDATE KATALOG_LOGISTIK SET
				  FILE_BUKTI_TERIMA = '".$this->getField("FILE_BUKTI_TERIMA")."',
				  PATH_FILE_BUKTI_TERIMA = '".$this->getField("PATH_FILE_BUKTI_TERIMA")."', 
				  UPDATED_BY = '".$this->getField("UPDATED_BY")."', 
				  UPDATED_DATE = NOW()
				WHERE PAKET_ID = '".$this->getField("PAKET_ID")."'
				"; 
				// echo $str; die();
				$this->query = $str;
				
		return $this->execQuery($str);
  }

    function update()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$str = "UPDATE KATALOG_LOGISTIK SET
				  ONGKOS_KIRIM = '".$this->getField("ONGKOS_KIRIM")."',
				  ESTIMASI_SAMPAI = '".$this->getField("ESTIMASI_SAMPAI")."',
				  FILE_BUKTI_KIRIM = '".$this->getField("FILE_BUKTI_KIRIM")."',
				  FILE_BUKTI_TERIMA = '".$this->getField("FILE_BUKTI_TERIMA")."',
				  UPDATED_BY = '".$this->getField("CREATED_BY")."', 
				  UPDATED_DATE = NOW()
				WHERE PAKET_ID = '".$this->getField("PAKET_ID")."'
				"; 
				// echo $str; die();
				$this->query = $str;
				
		return $this->execQuery($str);
    }  

    function deleteKatalogLogistik()
	{ 
		
		$str = " DELETE FROM KATALOG_LOGISTIK WHERE
					PAKET_ID = '".$this->getField("PAKET_ID")."' AND 
				  	CREATED_BY = '".$this->getField("CREATED_BY")."'
					"; 
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
		$str = "SELECT A.*
				FROM KATALOG_LOGISTIK A  
				WHERE 1=1 
				"; 
		
		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}
		
		$str .= $statement." ORDER BY LOGISTIKID ASC";
		// echo $str; die();
		$this->query = $str;
				
		return $this->selectLimit($str,$limit,$from); 
    } 

    function getCountByParams($paramsArray=array(), $varStatement=""){
      $str = "SELECT COUNT(LOGISTIKID) AS ROWCOUNT FROM KATALOG_LOGISTIK A WHERE 1=1 ".$varStatement;
      foreach ($paramsArray as $key => $value) {
        $str .= " AND $key = '$value' ";
      }
      // echo $str;
      $this->select($str);
      if($this->firstRow())
        return $this->getField("ROWCOUNT");
      else
         return 0;
    }
 
  } 
?>