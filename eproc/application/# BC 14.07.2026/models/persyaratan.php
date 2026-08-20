<?php 
/**
 * @package     eProcurement Application
 * @author      eproc2025
 * @since       25. Version 3.1
 * 
 */
  
  include_once('entity.php');

  class Persyaratan extends Entity{ 

	var $query;
    /**
    * Class constructor.
    **/
    function Persyaratan()
	{
      $this->Entity(); 
    }
	
	function insert()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$this->setField("PERSYARATAN_ID", $this->getNextId("PERSYARATAN_ID","PERSYARATAN")); 

		$str = "
		INSERT INTO PERSYARATAN (
   			        PERSYARATAN_ID, NAMA) 
			 	VALUES (
				  ".$this->getField("PERSYARATAN_ID").",
				  '".$this->getField("NAMA")."'
				)"; 
				
		$this->query = $str;
		return $this->execQuery($str);
    }

    function update()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$str = "UPDATE PERSYARATAN SET
				  NAMA = '".$this->getField("NAMA")."'
				WHERE PERSYARATAN_ID = ".$this->getField("PERSYARATAN_ID")."
				"; 
				$this->query = $str;
		return $this->execQuery($str);
    }
	
	function delete()
	{
        $str = "DELETE FROM PERSYARATAN
                WHERE 
                  PERSYARATAN_ID = ".$this->getField("PERSYARATAN_ID").""; 
				  
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
		$str = "SELECT PERSYARATAN_ID, NAMA
				FROM PERSYARATAN WHERE PERSYARATAN_ID IS NOT NULL"; 
		
		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}
		
		$this->query = $str;
		$str .= $statement." ORDER BY NAMA ASC";
				
		return $this->selectLimit($str,$limit,$from); 
    }

    function selectByParamsPersyaratanBelumTerpilih($panelid, $panelBidangUsahaId)
	{
		$str = " SELECT A.PERSYARATAN_ID, NAMA, B.PERSYARATAN_ID PERSYARATAN_ID_TERPILIH, B.KETERANGAN
				  FROM PERSYARATAN A 
				  LEFT JOIN PANEL_BIDANG_USAHA_SYARAT B ON A.PERSYARATAN_ID = B.PERSYARATAN_ID AND B.PANEL_BIDANG_USAHA_ID = '".$panelBidangUsahaId."'
				 WHERE A.PERSYARATAN_ID NOT IN (
         		 SELECT     REGEXP_SUBSTR ((SELECT    SYARAT_TEKNIS_TENAGA_AHLI
                                            || ','
                                            || SYARAT_TEKNIS_PERALATAN
                                            || ','
                                            || SYARAT_TEKNIS_SERTIFIKAT
                                            || ','
                                            || SYARAT_REKENING_KORAN
                                            || ','
                                            || SYARAT_KEUANGAN_SPT
                                            || ','
                                            || SYARAT_KEUANGAN_PPN
                                            || ','
                                            || SYARAT_KEUANGAN_PPH
                                            || ','
                                            || SYARAT_KEUANGAN_PPN_BULAN
                                            || ','
                                            || SYARAT_KEUANGAN_PPH_BULAN
                                            || ','
                                            || SYARAT_TEKNIS_SERTIFIKAT_INFO
                                            || ','
                                            || SYARAT_KEUANGAN_PKP
                                            || ','
                                            || SYARAT_ADM_KUALIFIKASI
                                            || ','
                                            || SYARAT_IJIN_SIUJK
                                            || ','
                                            || SYARAT_IJIN_SIUI
                                            || ','
                                            || SYARAT_IJIN_LAIN
                                            || ','
                                            || SYARAT_NERACA
                                            || ','
                                            || SYARAT_SBU
                                       FROM PANEL WHERE PANEL_ID = ".$panelid."),
                                    '[^,]+',
                                    1,
                                    LEVEL
                                   )
                      FROM DUAL
          CONNECT BY REGEXP_SUBSTR ((SELECT    SYARAT_TEKNIS_TENAGA_AHLI
                                            || ','
                                            || SYARAT_TEKNIS_PERALATAN
                                            || ','
                                            || SYARAT_TEKNIS_SERTIFIKAT
                                            || ','
                                            || SYARAT_REKENING_KORAN
                                            || ','
                                            || SYARAT_KEUANGAN_SPT
                                            || ','
                                            || SYARAT_KEUANGAN_PPN
                                            || ','
                                            || SYARAT_KEUANGAN_PPH
                                            || ','
                                            || SYARAT_KEUANGAN_PPN_BULAN
                                            || ','
                                            || SYARAT_KEUANGAN_PPH_BULAN
                                            || ','
                                            || SYARAT_TEKNIS_SERTIFIKAT_INFO
                                            || ','
                                            || SYARAT_KEUANGAN_PKP
                                            || ','
                                            || SYARAT_ADM_KUALIFIKASI
                                            || ','
                                            || SYARAT_IJIN_SIUJK
                                            || ','
                                            || SYARAT_IJIN_SIUI
                                            || ','
                                            || SYARAT_IJIN_LAIN
                                            || ','
                                            || SYARAT_NERACA
                                            || ','
                                            || SYARAT_SBU
                                       FROM PANEL WHERE PANEL_ID = ".$panelid."),
                                    '[^,]+',
                                    1,
                                    LEVEL
                                   ) IS NOT NULL) "; 
		
		$str .= " ORDER BY A.PERSYARATAN_ID ASC";
		return $this->selectLimit($str,-1,-1); 
    }	
    
	function selectByParamsLike($paramsArray=array(),$limit=-1,$from=-1, $statement='')
	{
		$str = "SELECT PERSYARATAN_ID, NAMA
				FROM PERSYARATAN WHERE PERSYARATAN_ID IS NOT NULL"; 
		
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
		$str = "SELECT COUNT(PERSYARATAN_ID) AS ROWCOUNT FROM PERSYARATAN WHERE PERSYARATAN_ID IS NOT NULL "; 
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
		$str = "SELECT COUNT(PERSYARATAN_ID) AS ROWCOUNT FROM PERSYARATAN WHERE PERSYARATAN_ID IS NOT NULL "; 
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