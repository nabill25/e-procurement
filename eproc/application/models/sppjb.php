<?php 
/**
 * @package 	eProcurement Application
 * @author 		eproc2025
 * @since 		25. Version 3.1
 * 
 */
  
  include_once('entity.php');

  class Sppjb extends Entity{ 

	var $query;
    /**
    * Class constructor.
    **/
    function Sppjb()
	{
      $this->Entity(); 
    }
	
	function insert()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$this->setField("SPPJB_ID", $this->getNextId("SPPJB_ID","SPPJB")); 

		$str = "
		INSERT INTO SPPJB (
						   SPPJB_ID, PAKET_ID, KODE, TANGGAL, NAMA_DIRUT, ALAMAT_DIRUT, 
						   KOTA_DIRUT, PPN, PERSEN_JAMINAN, TMT_JAMINAN, JANGKA_WAKTU, PENANDA_TANGAN, 
						   PENANDA_TANGAN_JABATAN, JANGKA_WAKTU_JAMINAN,PAKET_PEMENANG_ID) 
 			 	VALUES (
					  ".$this->getField("SPPJB_ID").",
					  ".$this->getField("PAKET_ID").",
					  '".$this->getField("KODE")."',
					  ".$this->getField("TANGGAL").",
					  '".$this->getField("NAMA_DIRUT")."',
					  '".$this->getField("ALAMAT_DIRUT")."',
					  '".$this->getField("KOTA_DIRUT")."',
					  '".$this->getField("PPN")."',
					  ".$this->getField("PERSEN_JAMINAN").",
					  ".$this->getField("TMT_JAMINAN").",
					  '".$this->getField("JANGKA_WAKTU")."',
					  '".$this->getField("PENANDA_TANGAN")."',
					  '".$this->getField("PENANDA_TANGAN_JABATAN")."',
					  '".$this->getField("JANGKA_WAKTU_JAMINAN")."',
					  '".$this->getField("PAKET_PEMENANG_ID")."'
				)"; 
		$this->query = $str;
		$this->id = $this->getField("SPPJB_ID");
		return $this->execQuery($str);
    }
	
	 function update()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$str = "UPDATE SPPJB SET
					 KODE		= '".$this->getField("KODE")."',
					 TANGGAL	= ".$this->getField("TANGGAL").",
					 NAMA_DIRUT = '".$this->getField("NAMA_DIRUT")."',
					 ALAMAT_DIRUT = '".$this->getField("ALAMAT_DIRUT")."',
					 KOTA_DIRUT = '".$this->getField("KOTA_DIRUT")."',
					 PPN		= '".$this->getField("PPN")."',
					 PERSEN_JAMINAN = ".$this->getField("PERSEN_JAMINAN").",
					 TMT_JAMINAN 	= ".$this->getField("TMT_JAMINAN").",
					 JANGKA_WAKTU 	= '".$this->getField("JANGKA_WAKTU")."',
					 PENANDA_TANGAN	= '".$this->getField("PENANDA_TANGAN")."',
					 PENANDA_TANGAN_JABATAN	= '".$this->getField("PENANDA_TANGAN_JABATAN")."',
					 JANGKA_WAKTU_JAMINAN = '".$this->getField("JANGKA_WAKTU_JAMINAN")."',
					 PAKET_PEMENANG_ID = '".$this->getField("PAKET_PEMENANG_ID")."'
				WHERE PAKET_ID = ".$this->getField("PAKET_ID")."
				"; 
				$this->query = $str;
		return $this->execQuery($str);
    }
	
	function delete()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$str = "DELETE FROM SPPJB  
				WHERE PAKET_ID = ".$this->getField("PAKET_ID")."
				"; 
				$this->query = $str;
		return $this->execQuery($str);
    }
	
    function selectByParams($paramsArray=array(),$limit=-1,$from=-1, $statement='',  $order="ORDER BY SPPJB_ID ASC")
	{
		$str = "SELECT SPPJB_ID, PAKET_ID, KODE, TANGGAL, NAMA_DIRUT, ALAMAT_DIRUT, 
					   KOTA_DIRUT, PPN, PERSEN_JAMINAN, TMT_JAMINAN, JANGKA_WAKTU, PENANDA_TANGAN, 
					   PENANDA_TANGAN_JABATAN, JANGKA_WAKTU_JAMINAN
				  FROM SPPJB
				WHERE 1 = 1
				"; 
		
		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}
		
		$this->query = $str;
		$str .= $statement." ".$order;
				
		return $this->selectLimit($str,$limit,$from); 
    }
	
	function selectByParamsMonitoring($paramsArray=array(),$limit=-1,$from=-1, $statement='',  $order="ORDER BY SPPJB_ID ASC")
	{
		$str = " SELECT SPPJB_ID, A.PAKET_ID, KODE, A.TANGGAL, NAMA_DIRUT, ALAMAT_DIRUT, 
					   KOTA_DIRUT, PPN, PERSEN_JAMINAN, TMT_JAMINAN, JANGKA_WAKTU, PENANDA_TANGAN, 
					   PENANDA_TANGAN_JABATAN, B.NAMA NAMA_PAKET, B.NILAI_NEGOSIASI NILAI_PEKERJAAN, JANGKA_WAKTU_JAMINAN
				  FROM SPPJB A
				  LEFT JOIN PAKET B ON A.PAKET_ID=B.PAKET_ID
				  WHERE 1=1
				"; 
		
		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}
		
		$this->query = $str;
		$str .= $statement." ".$order;
				
		return $this->selectLimit($str,$limit,$from); 
    }
	
	function selectByParamsCetak($paramsArray=array(),$limit=-1,$from=-1, $statement='',  $order="ORDER BY SPPJB_ID ASC")
	{
		$str = " SELECT SPPJB_ID, A.PAKET_ID, A.KODE, A.TANGGAL, D.NAMA ||' '|| C.NAMA NAMA_REKANAN, C.ALAMAT, C.KOTA,
					   KOTA_DIRUT, PPN, PERSEN_JAMINAN, TMT_JAMINAN, JANGKA_WAKTU, PENANDA_TANGAN, 
					   PENANDA_TANGAN_JABATAN, B.NAMA NAMA_PAKET, B.NILAI_NEGOSIASI NILAI_PEKERJAAN, JANGKA_WAKTU_JAMINAN, C.REKANAN_ID
				  FROM SPPJB A
				  LEFT JOIN PAKET B ON A.PAKET_ID=B.PAKET_ID
				  LEFT JOIN REKANAN C ON C.REKANAN_ID = B.REKANAN_ID_PEMENANG
				  LEFT JOIN REKANAN_TIPE D ON D.REKANAN_TIPE_ID=C.REKANAN_TIPE_ID
				  WHERE 1=1
				"; 
		
		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}
		
		$this->query = $str;
		$str .= $statement." ".$order;
				
		return $this->selectLimit($str,$limit,$from); 
    }
	
	function selectByParamsCetakLot($paramsArray=array(),$limit=-1,$from=-1, $statement='',  $order="ORDER BY SPPJB_ID ASC")
	{
		$str = " SELECT SPPJB_ID, A.PAKET_ID, A.KODE, A.TANGGAL, E.NAMA ||' '|| D.NAMA NAMA_REKANAN, D.ALAMAT, D.KOTA,
					   KOTA_DIRUT, PPN, PERSEN_JAMINAN, TMT_JAMINAN, JANGKA_WAKTU, PENANDA_TANGAN, 
					   PENANDA_TANGAN_JABATAN, B.NAMA NAMA_PAKET, 
					   F.QUANTITY * F.UNIT_PRICE NILAI_PEKERJAAN, JANGKA_WAKTU_JAMINAN, C.LOKASI, D.REKANAN_ID
				  FROM SPPJB A
				  LEFT JOIN PAKET B ON A.PAKET_ID=B.PAKET_ID
				  LEFT JOIN PAKET_PENAWARAN C ON C.PAKET_ID = B.PAKET_ID
				  LEFT JOIN REKANAN D ON D.REKANAN_ID = C.REKANAN_ID_PEMENANG
				  LEFT JOIN REKANAN_TIPE E ON E.REKANAN_TIPE_ID=D.REKANAN_TIPE_ID
				  LEFT JOIN PAKET_NEGOSIASI F ON C.PAKET_PENAWARAN_ID = F.PAKET_PENAWARAN_ID
				  WHERE 1=1
				"; 
		
		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}
		
		$this->query = $str;
		$str .= $statement." ".$order;
				
		return $this->selectLimit($str,$limit,$from); 
    }
	
	
	function getCountByParams($paramsArray=array())
	{
		$str = "SELECT COUNT(SPPJB_ID) AS ROWCOUNT FROM SPPJB A
				WHERE 1 = 1 "; 
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
	
	function getCountByParamsMonitoring($paramsArray=array())
	{
		$str = "SELECT COUNT(SPPJB_ID) AS ROWCOUNT  
				FROM SPPJB A
				LEFT JOIN PAKET B ON A.PAKET_ID=B.PAKET_ID
				WHERE 1=1 "; 
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