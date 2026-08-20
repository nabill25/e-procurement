<?php
/**
 * @package 	eProcurement Application
 * @author 		eproc2025
 * @since 		25. Version 3.1
 * 
 */

  include_once('entity.php');

  class Katalogrekanan extends Entity{ 

	var $query; 

    function __construct(){
  		parent::__construct();
	}
	
	function insert()
	{
	 	$this->setField("KATALOGREKANANID", $this->getNextId("KATALOGREKANANID","KATALOG_REKANAN"));
		$str = "
		INSERT INTO  KATALOG_REKANAN (
		   KATALOGREKANANID, KATALOGID, PAKET_ID, NAMAPRODUK, MEREK, MODELTYPE, HARGA, REKANAN_ID, CREATED_BY, CREATED_DATE, BROWSER, QTY, STATUS) 
 			 	VALUES (
				  '".$this->getField("KATALOGREKANANID")."',
  				  '".$this->getField("KATALOGID")."',
				  '".$this->getField("PAKET_ID")."',
				  '".$this->getField("NAMAPRODUK")."',
				  '".$this->getField("MEREK")."',
				  '".$this->getField("MODELTYPE")."',
				  '".$this->getField("HARGA")."',
				  '".$this->getField("REKANAN_ID")."',
				  '".$this->getField("CREATED_BY")."',
  				  NOW(),
				  '".$this->getField("BROWSER")."',
				  '".$this->getField("QTY")."',
				  '".$this->getField("STATUS")."'
				)"; 
				// echo $str; die();
		$this->query = $str;
		return $this->execQuery($str);
    } 

    function updateQty()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$str = "UPDATE KATALOG_REKANAN SET
				  QTY = '".$this->getField("QTYUPDATE")."',
				  UPDATED_BY = '".$this->getField("CREATED_BY")."',
				  UPDATED_DATE = NOW()
				WHERE KATALOGID = '".$this->getField("KATALOGID")."' AND PAKET_ID = '".$this->getField("PAKET_ID")."'
				"; 
				$this->query = $str;
				
		return $this->execQuery($str);
    } 

    function updateStatus()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$str = "UPDATE KATALOG_REKANAN SET
				  STATUS = '".$this->getField("STATUS")."',
				  UPDATED_BY = '".$this->getField("CREATED_BY")."',
				  NOINVOICE = '".$this->getField("NOINVOICE")."',
				  UPDATED_DATE = NOW()
				WHERE STATUS = '".$this->getField("STATUSAWAL")."' AND PAKET_ID = '".$this->getField("PAKET_ID")."'
				"; 
				// echo $str; die();
				$this->query = $str;
				
		return $this->execQuery($str);
    } 

    function updateStatusAja()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$str = "UPDATE KATALOG_REKANAN SET
				  STATUS = '".$this->getField("STATUS")."',
				  UPDATED_BY = '".$this->getField("UPDATED_BY")."',
				  UPDATED_DATE = NOW()
				WHERE STATUS = '".$this->getField("STATUSAWAL")."' AND PAKET_ID = '".$this->getField("PAKET_ID")."'
				"; 
				// echo $str; die();
				$this->query = $str;
				
		return $this->execQuery($str);
    } 

    function updateHargaNego()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$str = "UPDATE KATALOG_REKANAN SET
				  HARGA_NEGO = '".$this->getField("HARGA_NEGO")."',
				  UPDATED_BY = '".$this->getField("CREATED_BY")."',
				  UPDATED_DATE = NOW()
				WHERE KATALOGREKANANID = '".$this->getField("KATALOGREKANANID")."'"; 
				// echo $str; die();
				$this->query = $str;
				
		return $this->execQuery($str);
    } 
	
	function delete()
	{ 
		
		$str = " DELETE FROM KATALOG_REKANAN WHERE
					KATALOGREKANANID = '".$this->getField("KATALOGREKANANID")."' AND 
				  	CREATED_BY = '".$this->getField("CREATED_BY")."'
					"; 
				  
		$this->query = $str;
        return $this->execQuery($str);
  }
	
	function deleteKatalogRekanan()
	{ 
		
		$str = " DELETE FROM KATALOG_REKANAN WHERE
					KATALOGREKANANID = '".$this->getField("KATALOGREKANANID")."' AND 
				  	PAKET_ID = '".$this->getField("PAKET_ID")."'
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
		$str = "SELECT A.*, B.NILAI, B.NILAI_OWNER_ESTIMATE, C.USER_NAMA, D.NPWP, D.ALAMAT, D.TELEPON_KODE, D.TELEPON, D.EMAIL,
					   D.KOTA, D.KODEPOS, D.WEBSITE, D.KONTAK_PERSON, D.KONTAK_PERSON_HP, D.PKP, D.BANK_REKENING, D.BANK_PEMILIK ,E.NAMA
				FROM KATALOG_REKANAN A 
				INNER JOIN PAKET B ON A.PAKET_ID=B.PAKET_ID
				INNER JOIN USER_LOGIN C ON A.REKANAN_ID=C.REKANAN_ID
				INNER JOIN REKANAN D ON C.REKANAN_ID=D.REKANAN_ID
				LEFT JOIN BANK E ON D.BANK_ID=E.BANK_ID
				WHERE 1=1 
				"; 
		
		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}
		
		$str .= $statement." ORDER BY KATALOGREKANANID ASC";
		// echo $str; die();
		$this->query = $str;
				
		return $this->selectLimit($str,$limit,$from); 
    }

    function selectByParamsGroupByPenyedia($paramsArray=array(),$varStatement)
	{ 
		$str = "SELECT COUNT(A.REKANAN_ID) AS ROWCOUNT 
				FROM 
				(
				SELECT A.REKANAN_ID 
				FROM KATALOG_REKANAN A WHERE 1=1 ".$varStatement." 
				GROUP BY A.REKANAN_ID
				) A"; 
		// echo $str; die();
		$this->select($str);
		if($this->firstRow())
			return $this->getField("ROWCOUNT");
		else
		 	return 0;
    }

    function getCountByParams($paramsArray=array(), $varStatement=""){
      $str = "SELECT COUNT(KATALOGREKANANID) AS ROWCOUNT FROM KATALOG_REKANAN A WHERE 1=1 ".$varStatement;
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