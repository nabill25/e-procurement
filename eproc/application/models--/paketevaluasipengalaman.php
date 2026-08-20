<?php 
/**
 * @package 	eProcurement Application
 * @author 		eproc2025
 * @since 		25. Version 3.1
 * 
 */
  
  include_once('entity.php');

  class PaketEvaluasiPengalaman extends Entity{ 

	var $query;
    /**
    * Class constructor.
    **/
    function PaketEvaluasiPengalaman()
	{
      $this->Entity(); 
    }
	
	function insert()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$this->setField("PAKET_EVAL_PENGALAMAN_ID", $this->getNextId("PAKET_EVAL_PENGALAMAN_ID","PAKET_EVAL_PENGALAMAN")); 

		$str = "
				INSERT INTO PAKET_EVAL_PENGALAMAN (
				   PAKET_EVAL_PENGALAMAN_ID, PAKET_ID, BP_NILAI, 
				   BP_SUB_SAMA_PERSEN, BP_SUB_BEDA_PERSEN, NK_NILAI, 
				   NK1_RP, NK1_PERSEN, NK2_RPMIN, 
				   NK2_RPMAX, NK2_PERSEN, NK3_RP, 
				   NK3_PERSEN, STBU_NILAI, STBU_UTAMA_PERSEN, 
				   STBU_SUB_PERSEN, NILAI_MINIMAL, NK_NILAI_PROSENTASE, BP_NILAI_PROSENTASE, 
                   STBU_NILAI_PROSENTASE) 
				VALUES (".$this->getField("PAKET_EVAL_PENGALAMAN_ID").", ".$this->getField("PAKET_ID").", ".$this->getField("BP_NILAI").", 
				   ".$this->getField("BP_SUB_SAMA_PERSEN").", ".$this->getField("BP_SUB_BEDA_PERSEN").", ".$this->getField("NK_NILAI").", 
				   ".$this->getField("NK1_RP").", ".$this->getField("NK1_PERSEN").", ".$this->getField("NK2_RPMIN").", 
				   ".$this->getField("NK2_RPMAX").", ".$this->getField("NK2_PERSEN").", ".$this->getField("NK3_RP").", 
				   ".$this->getField("NK3_PERSEN").", ".$this->getField("STBU_NILAI").", ".$this->getField("STBU_UTAMA_PERSEN").", 
				   ".$this->getField("STBU_SUB_PERSEN").", ".$this->getField("NILAI_MINIMAL").", 
				   ".$this->getField("NK_NILAI_PROSENTASE").", ".$this->getField("BP_NILAI_PROSENTASE").", ".$this->getField("STBU_NILAI_PROSENTASE").")
				"; 
				
		$this->query = $str;
		return $this->execQuery($str);
    }

    function update()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$str = "UPDATE PAKET_EVAL_PENGALAMAN SET
				   BP_NILAI = '".$this->getField("BP_NILAI")."',
				   BP_SUB_SAMA_PERSEN = '".$this->getField("BP_SUB_SAMA_PERSEN")."',
				   BP_SUB_BEDA_PERSEN = '".$this->getField("BP_SUB_BEDA_PERSEN")."',
				   NK_NILAI = '".$this->getField("NK_NILAI")."',
				   NK1_RP = '".$this->getField("NK1_RP")."', 
				   NK1_PERSEN = '".$this->getField("NK1_PERSEN")."',
				   NK2_RPMIN = '".$this->getField("NK2_RPMIN")."',
				   NK2_RPMAX = '".$this->getField("NK2_RPMAX")."',
				   NK2_PERSEN = '".$this->getField("NK2_PERSEN")."',
				   NK3_RP = '".$this->getField("NK3_RP")."',
				   NK3_PERSEN = '".$this->getField("NK3_PERSEN")."',
				   STBU_NILAI = '".$this->getField("STBU_NILAI")."',
				   STBU_UTAMA_PERSEN = '".$this->getField("STBU_UTAMA_PERSEN")."',
				   STBU_SUB_PERSEN = '".$this->getField("STBU_SUB_PERSEN")."',
				   NILAI_MINIMAL = '".$this->getField("NILAI_MINIMAL")."',
				   NILAI_MAKSIMUM = '".$this->getField("NILAI_MAKSIMUM")."',
				   JUMLAH_PENGALAMAN_A = '".$this->getField("JUMLAH_PENGALAMAN_A")."', 
				   PROSENTASE_PENGALAMAN_A = '".$this->getField("PROSENTASE_PENGALAMAN_A")."', 
				   JUMLAH_PENGALAMAN_B = '".$this->getField("JUMLAH_PENGALAMAN_B")."', 
   				   PROSENTASE_PENGALAMAN_B = '".$this->getField("PROSENTASE_PENGALAMAN_B")."', 
				   JUMLAH_PENGALAMAN_C = '".$this->getField("JUMLAH_PENGALAMAN_C")."', 
				   PROSENTASE_PENGALAMAN_C = '".$this->getField("PROSENTASE_PENGALAMAN_C")."', 
				   JUMLAH_PENGALAMAN_D = '".$this->getField("JUMLAH_PENGALAMAN_D")."', 
				   PROSENTASE_PENGALAMAN_D = '".$this->getField("PROSENTASE_PENGALAMAN_D")."',
				   NK_NILAI_PROSENTASE = '".$this->getField("NK_NILAI_PROSENTASE")."', 
				   BP_NILAI_PROSENTASE = '".$this->getField("BP_NILAI_PROSENTASE")."', 
                   STBU_NILAI_PROSENTASE = '".$this->getField("STBU_NILAI_PROSENTASE")."'
				WHERE PAKET_ID = '".$this->getField("PAKET_ID")."'
				"; 
				$this->query = $str;
		return $this->execQuery($str);
    }
	
	function delete()
	{
        $str = "DELETE FROM PAKET_EVAL_PENGALAMAN
                WHERE 
                  PAKET_EVAL_PENGALAMAN_ID = '".$this->getField("PAKET_EVAL_PENGALAMAN_ID")."'"; 
				  
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
				PAKET_EVAL_PENGALAMAN_ID, PAKET_ID, BP_NILAI, 
				   BP_SUB_SAMA_PERSEN, BP_SUB_BEDA_PERSEN, NK_NILAI, 
				   NK1_RP, NK1_PERSEN, NK2_RPMIN, 
				   NK2_RPMAX, NK2_PERSEN, NK3_RP, 
				   NK3_PERSEN, STBU_NILAI, STBU_UTAMA_PERSEN, 
				   STBU_SUB_PERSEN, NILAI_MINIMAL, NILAI_MAKSIMUM, 
				   JUMLAH_PENGALAMAN_A, PROSENTASE_PENGALAMAN_A, JUMLAH_PENGALAMAN_B, 
   				   PROSENTASE_PENGALAMAN_B, JUMLAH_PENGALAMAN_C, PROSENTASE_PENGALAMAN_C, 
				   JUMLAH_PENGALAMAN_D, PROSENTASE_PENGALAMAN_D, NK_NILAI_PROSENTASE, BP_NILAI_PROSENTASE, 
                   STBU_NILAI_PROSENTASE
				FROM PAKET_EVAL_PENGALAMAN WHERE PAKET_EVAL_PENGALAMAN_ID IS NOT NULL "; 
		
		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}
		
		$this->query = $str;
		$str .= $statement." ORDER BY PAKET_EVAL_PENGALAMAN_ID ASC";
				
		return $this->selectLimit($str,$limit,$from); 
    }
    
	function selectByParamsLike($paramsArray=array(),$limit=-1,$from=-1, $statement='')
	{
		$str = "SELECT 
				PAKET_EVAL_PENGALAMAN_ID, PAKET_ID, BP_NILAI, 
				   BP_SUB_SAMA_PERSEN, BP_SUB_BEDA_PERSEN, NK_NILAI, 
				   NK1_RP, NK1_PERSEN, NK2_RPMIN, 
				   NK2_RPMAX, NK2_PERSEN, NK3_RP, 
				   NK3_PERSEN, STBU_NILAI, STBU_UTAMA_PERSEN, 
				   STBU_SUB_PERSEN, NILAI_MINIMAL,NILAI_MAKSIMUM
				FROM PAKET_EVAL_PENGALAMAN WHERE PAKET_EVAL_PENGALAMAN_ID IS NOT NULL"; 
		
		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key LIKE '%$val%' ";
		}
		
		$this->query = $str;
		$str .= $statement." ORDER BY PAKET_EVAL_PENGALAMAN_ID ASC";
		return $this->selectLimit($str,$limit,$from); 
    }	
    /** 
    * Hitung jumlah record berdasarkan parameter (array). 
    * @param array paramsArray Array of parameter. Contoh array("id"=>"xxx","NAMA"=>"yyy") 
    * @return long Jumlah record yang sesuai kriteria 
    **/ 
    function getCountByParams($paramsArray=array())
	{
		$str = "SELECT COUNT(PAKET_EVAL_PENGALAMAN_ID) AS ROWCOUNT FROM PAKET_EVAL_PENGALAMAN WHERE PAKET_EVAL_PENGALAMAN_ID IS NOT NULL "; 
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
		$str = "SELECT COUNT(PAKET_EVAL_PENGALAMAN_ID) AS ROWCOUNT FROM PAKET_EVAL_PENGALAMAN WHERE PAKET_EVAL_PENGALAMAN_ID IS NOT NULL "; 
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