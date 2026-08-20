<?php 
/**
 * @package 	eProcurement Application
 * @author 		eproc2025
 * @since 		25. Version 3.1
 * 
 */
  
  include_once('entity.php');

  class PermohonanPaket extends Entity{ 

	var $query;
    /**
    * Class constructor.
    **/
    function PermohonanPaket()
	{
      $this->Entity(); 
    }
	
	function insert()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$this->setField("PERMOHONAN_PAKET_ID", $this->getNextId("PERMOHONAN_PAKET_ID","PERMOHONAN_PAKET")); 

		$str = "
		
			INSERT INTO PERMOHONAN_PAKET (
			   PERMOHONAN_PAKET_ID, USER_LOGIN_ID, UNIT_KERJA_ID, 
			   NOTA_DINAS, NAMA, KETERANGAN, NO_PPA, TANGGAL, LAST_CREATE_USER, NILAI)
 
  			 	VALUES (
				  '".$this->getField("PERMOHONAN_PAKET_ID")."',
  				  '".$this->getField("USER_LOGIN_ID")."',
   				  '".$this->getField("UNIT_KERJA_ID")."',
				  '".$this->getField("NOTA_DINAS")."',
				  '".$this->getField("NAMA")."',
  				  '".$this->getField("KETERANGAN")."',
  				  '".$this->getField("NO_PPA")."',
  				  ".$this->getField("TANGGAL").",
				  '".$this->getField("LAST_CREATE_USER")."',
				  ".$this->getField("NILAI")."
				)"; 
				
		$this->query = $str;
		echo $str;exit;
		$this->id = $this->getField("PERMOHONAN_PAKET_ID");
		
		return $this->execQuery($str);
    }


    function update()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$str = "		
				 UPDATE  PERMOHONAN_PAKET
				SET    
					   NOTA_DINAS  = '".$this->getField("NOTA_DINAS")."',
					   NAMA      = '".$this->getField("NAMA")."',
					   KETERANGAN    = '".$this->getField("KETERANGAN")."',
					   NO_PPA    = '".$this->getField("NO_PPA")."',
					   TANGGAL    = ".$this->getField("TANGGAL")."
				WHERE  PERMOHONAN_PAKET_ID   =  ".$this->getField("PERMOHONAN_PAKET_ID")."
			  
				"; 
				echo $str;
				$this->query = $str;
		return $this->execQuery($str);
    }
	
	function delete()
	{
        $str = "DELETE FROM PERMOHONAN_PAKET
                WHERE 
                  PERMOHONAN_PAKET_ID = ".$this->getField("PERMOHONAN_PAKET_ID").""; 
				  
		$this->query = $str;
        return $this->execQuery($str);
    }

    /** 
    * Cari record berdasarkan array parameter dan limit tampilan 
    * @param array paramsArray Array of parameter. Contoh array("id"=>"xxx","PERMOHONAN_PAKET_METODE_EVALUASI_ID"=>"yyy") 
    * @param int limit Jumlah maksimal record yang akan diambil 
    * @param int from Awal record yang diambil 
    * @return boolean True jika sukses, false jika tidak 
    **/ 
    function selectByParams($paramsArray=array(),$limit=-1,$from=-1, $statement='')
	{
		$str = "
					SELECT 
					A.PERMOHONAN_PAKET_ID, A.USER_LOGIN_ID, A.UNIT_KERJA_ID, 
			   		A.NOTA_DINAS, A.NAMA, A.KETERANGAN, A.NO_PPA, A.TANGGAL, C.NAMA UNIT_KERJA, B.NAMA USER_LOGIN,
                    CASE WHEN D.PAKET_ID IS NULL THEN 0 ELSE 1 END STATUS,
					CASE WHEN D.PAKET_ID IS NULL THEN 'Paket belum diproses' ELSE '<a onclick=\"top.location.href = ''index.php?pg=ebc1536e4e96c8379aa257db020a8eef&reqId=' || D.PAKET_ID || '''\">Detil Paket</a>' END STATUS_KETERANGAN,
					MD5(A.PERMOHONAN_PAKET_ID || 'EPROC') PERMOHONAN_PAKET_ID_ENCRYPT
					FROM PERMOHONAN_PAKET A 
                    LEFT JOIN V_OAUTH_USER B ON TO_CHAR(A.USER_LOGIN_ID) = B.NIPP
                    LEFT JOIN UNIT_KERJA C ON A.UNIT_KERJA_ID = C.UNIT_KERJA_ID
                    LEFT JOIN PAKET D ON A.PERMOHONAN_PAKET_ID = D.PERMOHONAN_PAKET_ID
				    WHERE A.PERMOHONAN_PAKET_ID IS NOT NULL "; 
		
		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}
		$this->query = $str;
		$str .= $statement." ORDER BY A.PERMOHONAN_PAKET_ID DESC";
		
				
		return $this->selectLimit($str,$limit,$from); 
    }

    function selectByParamsSimple($paramsArray=array(),$limit=-1,$from=-1, $statement='')
	{
		$str = "
					SELECT 
					A.PERMOHONAN_PAKET_ID, A.USER_LOGIN_ID, A.UNIT_KERJA_ID, 
			   		A.NOTA_DINAS, A.NAMA, A.KETERANGAN, A.NO_PPA, A.TANGGAL
					FROM PERMOHONAN_PAKET A 
				    WHERE A.PERMOHONAN_PAKET_ID IS NOT NULL "; 
		
		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}
		$this->query = $str;
			
		$str .= $statement." ORDER BY A.PERMOHONAN_PAKET_ID DESC";
		
				
		return $this->selectLimit($str,$limit,$from); 
    }

   
    function getCountByParams($paramsArray=array(), $statement='')
	{
		$str = "SELECT COUNT(A.PERMOHONAN_PAKET_ID) AS ROWCOUNT 
					FROM    PERMOHONAN_PAKET A
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