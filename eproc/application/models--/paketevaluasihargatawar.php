<?php 
/**
 * @package 	eProcurement Application
 * @author 		eproc2025
 * @since 		25. Version 3.1
 * 
 */
  
  include_once('entity.php');

  class PaketEvaluasiHargaTawar extends Entity{ 

	var $query;
    /**
    * Class constructor.
    **/
    function __construct(){
		parent::__construct();
	}
	
    function PaketEvaluasiHargaTawar()
	{
      $this->Entity(); 
    }
	
	function insert()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$this->setField("PAKET_EVAL_HARGA_TAWAR_ID", $this->getNextId("PAKET_EVAL_HARGA_TAWAR_ID","PAKET_EVAL_HARGA_TAWAR")); 

		$str = "INSERT INTO PAKET_EVAL_HARGA_TAWAR (
				   PAKET_EVAL_HARGA_TAWAR_ID, PAKET_ID, NAMA,WAJIB, CREATED_BY, CREATED_DATE) 
				VALUES (
				  ".$this->getField("PAKET_EVAL_HARGA_TAWAR_ID").",
				  ".$this->getField("PAKET_ID").",
				  '".$this->getField("NAMA")."',
				  '".$this->getField("WAJIB")."',
				  ".$this->getField("CREATED_BY").",
				  CURRENT_TIMESTAMP
				)"; 
				
		$this->query = $str;
		return $this->execQuery($str);
    }

    function update()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$str = "UPDATE AGAMA SET
				  NAMA = '".$this->getField("NAMA")."'
				WHERE AGAMA_ID = '".$this->getField("AGAMA_ID")."'
				"; 
				$this->query = $str;
		return $this->execQuery($str);
    }
	
	function delete()
	{
        $str = "DELETE FROM PAKET_EVAL_HARGA_TAWAR
                WHERE 
                  PAKET_ID = '".$this->getField("PAKET_ID")."'"; 
				  
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
		$str = "SELECT 
				PAKET_EVAL_HARGA_TAWAR_ID, PAKET_ID, NAMA, WAJIB
				FROM PAKET_EVAL_HARGA_TAWAR WHERE PAKET_EVAL_HARGA_TAWAR_ID IS NOT NULL"; 
		
		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}
		
		$this->query = $str;
		$str .= $statement." ORDER BY PAKET_EVAL_HARGA_TAWAR_ID ASC";
				
		return $this->selectLimit($str,$limit,$from); 
    }

    function selectByParamsRekananDokumen($rekananUserId, $paramsArray=array(),$limit=-1,$from=-1, $statement='')
	{
		$str = "SELECT 
                PAKET_EVAL_HARGA_TAWAR_ID, A.PAKET_ID, A.NAMA, B.UKURAN,  TO_CHAR(B.TANGGAL_UPLOAD, 'DD-MM-YYYY HH24:MI') TANGGAL_UPLOAD, B.PATH_FILE, B.PAKET_DOKUMEN_ID, COALESCE(B.KETERANGAN, 'N/A') KETERANGAN, WAJIB
                FROM PAKET_EVAL_HARGA_TAWAR A
                LEFT JOIN PAKET_DOKUMEN B ON A.PAKET_ID = B.PAKET_ID AND JENIS_DOKUMEN = 'PENAWARAN_HARGA' AND TRIM(A.NAMA) = TRIM(B.NAMA) AND REKANAN_USER_ID = '".$rekananUserId."'
                 WHERE 1 = 1"; 
		
		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}
		// echo $str; die();
		$this->query = $str;
		$str .= $statement." ORDER BY A.PAKET_EVAL_HARGA_TAWAR_ID ASC";
		return $this->selectLimit($str,$limit,$from); 
    }	

    function selectByParamsRekananDokumenBackup($rekananUserId, $paramsArray=array(),$limit=-1,$from=-1, $statement='')
	{
		$str = "SELECT 
                PAKET_EVAL_HARGA_TAWAR_ID, A.PAKET_ID, A.NAMA, B.UKURAN, TO_CHAR(B.TANGGAL_UPLOAD, 'DD-MM-YYYY HH24:MI') TANGGAL_UPLOAD, B.PATH_FILE, B.PAKET_DOKUMEN_ID, COALESCE(B.KETERANGAN, 'N/A') KETERANGAN
                FROM PAKET_EVAL_HARGA_TAWAR A
                LEFT JOIN PAKET_DOKUMEN_BACKUP B ON A.PAKET_ID = B.PAKET_ID AND JENIS_DOKUMEN = 'PENAWARAN_HARGA' AND TRIM(A.NAMA) = TRIM(B.NAMA) AND REKANAN_USER_ID = '".$rekananUserId."'
                 WHERE 1 = 1"; 
		
		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}
		
		$this->query = $str;
		$str .= $statement." ORDER BY A.PAKET_EVAL_HARGA_TAWAR_ID ASC";
		return $this->selectLimit($str,$limit,$from); 
    }	
	
	function selectByParamsLike($paramsArray=array(),$limit=-1,$from=-1, $statement='')
	{
		$str = "SELECT AGAMA_ID, NAMA
				FROM AGAMA WHERE AGAMA_ID IS NOT NULL"; 
		
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
		$str = "SELECT COUNT(PAKET_EVAL_HARGA_TAWAR_ID) AS ROWCOUNT FROM PAKET_EVAL_HARGA_TAWAR WHERE PAKET_EVAL_HARGA_TAWAR_ID IS NOT NULL "; 
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