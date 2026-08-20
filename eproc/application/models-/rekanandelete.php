<?php
/**
 * @package 	eProcurement Application
 * @author 		eproc2025
 * @since 		25. Version 3.1
 * 
 */

include_once('entity.php');

class Rekanandelete extends Entity{ 

	var $query; 

    function __construct(){
	  parent::__construct();
	}
	 
	function delete()
	{
        $str = "DELETE FROM BANNER
                WHERE 
                  BANNER_ID = ".$this->getField("BANNER_ID").""; 
				  
		$this->query = $str;
        return $this->execQuery($str);
    }
 	
    function selectByParams($rekananid,$limit=-1,$from=-1, $statement='', $order="ORDER BY URUT ASC")
	{
		$str = " select a.* from ( 
					SELECT 1 URUT, COUNT(REKANAN_ID) TOTAL, 'Ijin Usaha' Field FROM REKANAN_IJIN_USAHA WHERE IJIN_USAHA_ID != 99 AND REKANAN_ID = ".$rekananid."
					union
					SELECT 2 URUT, COUNT(REKANAN_ID) TOTAL, 'Bidang Usaha' Field FROM REKANAN_BIDANG_USAHA WHERE REKANAN_ID = ".$rekananid."
					union
					SELECT 3 URUT, COUNT(REKANAN_ID) TOTAL, 'Landasan Hukum' Field FROM REKANAN_AKTA WHERE REKANAN_ID = ".$rekananid."
					union
					SELECT 4 URUT, COUNT(REKANAN_ID) TOTAL, 'Pengurus Perusahaan' Field FROM REKANAN_PENGURUS WHERE REKANAN_ID = ".$rekananid."
					union
					SELECT 5 URUT, COUNT(REKANAN_ID) TOTAL, 'Kepemilikan Saham' Field FROM REKANAN_SAHAM WHERE REKANAN_ID = ".$rekananid."
					union
					SELECT 6 URUT, COUNT(REKANAN_ID) TOTAL, 'SBU' Field FROM REKANAN_IJIN_USAHA WHERE IJIN_USAHA_ID = 99 AND REKANAN_ID = ".$rekananid."
					union
					SELECT 7 URUT, COUNT(REKANAN_ID) TOTAL, 'Rekening Koran' Field FROM REKANAN_REKENING_KORAN WHERE REKANAN_ID = ".$rekananid."
					union
					SELECT 8 URUT, COUNT(REKANAN_ID) TOTAL, 'Neraca' Field FROM REKANAN_NERACA WHERE REKANAN_ID = ".$rekananid."
					union
					SELECT 9 URUT, COUNT(REKANAN_ID) TOTAL, 'SPT Tahunan, PPH & PPN' Field FROM REKANAN_PAJAK WHERE REKANAN_ID = ".$rekananid."
					union
					SELECT 10 URUT, COUNT(REKANAN_ID) TOTAL, 'Tenaga Ahli' Field FROM REKANAN_TENAGA_AHLI WHERE REKANAN_ID = ".$rekananid." 
					union
					SELECT 11 URUT, COUNT(REKANAN_ID) TOTAL, 'Pengalaman' Field FROM REKANAN_PENGALAMAN WHERE REKANAN_ID = ".$rekananid."
					union
					SELECT 12 URUT, COUNT(REKANAN_ID) TOTAL, 'Peralatan' Field FROM REKANAN_PERALATAN WHERE REKANAN_ID = ".$rekananid."
					union
					SELECT 13 URUT, COUNT(REKANAN_ID) TOTAL, 'Dokumen Teknis Perusahaan' Field FROM REKANAN_SERTIFIKAT WHERE REKANAN_ID = ".$rekananid."
					union 
					select 14 URUT, count(rekanan_id) total, 'User Login' Field FROM REKANAN WHERE REKANAN_ID = ".$rekananid."
					) a
					order by a.urut asc  "; 
		 
		$this->query = $str;
			
		$str .= $statement." ".$order;
				
		return $this->selectLimit($str,$limit,$from); 
    }

   
    function getCountByParams($paramsArray=array(), $statement='')
	{
		$str = "SELECT COUNT(A.BANNER_ID) AS ROWCOUNT 
					FROM    BANNER A
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

  function deleteData()
	{
		switch ($this->getField("ACTION")) {
			case '1': // Ijin Usaha
				$str = "INSERT INTO ZDEL_REKANAN_IJIN_USAHA SELECT * FROM REKANAN_IJIN_USAHA WHERE IJIN_USAHA_ID != 99 AND  REKANAN_ID = ".$this->getField("REKANAN_ID")."";
				$this->query = $str;
	     	if ($this->execQuery($str)) {
	     		$str2 = "DELETE FROM REKANAN_IJIN_USAHA WHERE IJIN_USAHA_ID != 99 AND  REKANAN_ID = ".$this->getField("REKANAN_ID")."";
					$this->query = $str2;
					return $this->execQuery($str2);
	     	} else {
	     		return false;
	     	}
				break;

			case '2': // Bidang Usaha
				$str = "INSERT INTO ZDEL_REKANAN_BIDANG_USAHA SELECT * FROM REKANAN_BIDANG_USAHA WHERE REKANAN_ID = ".$this->getField("REKANAN_ID")."";
				$this->query = $str;
	     	if ($this->execQuery($str)) {
	     		$str2 = "DELETE FROM REKANAN_BIDANG_USAHA WHERE REKANAN_ID = ".$this->getField("REKANAN_ID")."";
					$this->query = $str2;
					return $this->execQuery($str2);
	     	} else {
	     		return false;
	     	}
				break;

			case '3': // Landasan Hukum
				$str = "INSERT INTO ZDEL_REKANAN_AKTA SELECT * FROM REKANAN_AKTA WHERE REKANAN_ID = ".$this->getField("REKANAN_ID")."";
				$this->query = $str;
	     	if ($this->execQuery($str)) {
	     		$str2 = "DELETE FROM REKANAN_AKTA WHERE REKANAN_ID = ".$this->getField("REKANAN_ID")."";
					$this->query = $str2;
					return $this->execQuery($str2);
	     	} else {
	     		return false;
	     	}
				break;

			case '4': // Pengurus Perusahaan
				$str = "INSERT INTO ZDEL_REKANAN_PENGURUS SELECT * FROM REKANAN_PENGURUS WHERE REKANAN_ID = ".$this->getField("REKANAN_ID")."";
				$this->query = $str;
	     	if ($this->execQuery($str)) {
	     		$str2 = "DELETE FROM REKANAN_PENGURUS WHERE REKANAN_ID = ".$this->getField("REKANAN_ID")."";
					$this->query = $str2;
					return $this->execQuery($str2);
	     	} else {
	     		return false;
	     	}
				break;

			case '5': // Kepemilikan Saham
				$str = "INSERT INTO ZDEL_REKANAN_SAHAM SELECT * FROM REKANAN_SAHAM WHERE REKANAN_ID = ".$this->getField("REKANAN_ID")."";
				$this->query = $str;
	     	if ($this->execQuery($str)) {
	     		$str2 = "DELETE FROM REKANAN_SAHAM WHERE REKANAN_ID = ".$this->getField("REKANAN_ID")."";
					$this->query = $str2;
					return $this->execQuery($str2);
	     	} else {
	     		return false;
	     	}
				break;

			case '6': // SBU
				$str = "INSERT INTO ZDEL_REKANAN_IJIN_USAHA SELECT * FROM REKANAN_IJIN_USAHA WHERE IJIN_USAHA_ID = 99 AND REKANAN_ID = ".$this->getField("REKANAN_ID")."";
				$this->query = $str;
	     	if ($this->execQuery($str)) {
	     		$str2 = "DELETE FROM REKANAN_IJIN_USAHA WHERE IJIN_USAHA_ID = 99 AND REKANAN_ID = ".$this->getField("REKANAN_ID")."";
					$this->query = $str2;
					return $this->execQuery($str2);
	     	} else {
	     		return false;
	     	}
				break;

			case '7': // Rekening Koran
				$str = "INSERT INTO ZDEL_REKANAN_REKENING_KORAN SELECT * FROM REKANAN_REKENING_KORAN WHERE REKANAN_ID = ".$this->getField("REKANAN_ID")."";
				$this->query = $str;
	     	if ($this->execQuery($str)) {
	     		$str2 = "DELETE FROM REKANAN_REKENING_KORAN WHERE REKANAN_ID = ".$this->getField("REKANAN_ID")."";
					$this->query = $str2;
					return $this->execQuery($str2);
	     	} else {
	     		return false;
	     	}
				break;

			case '8': // Neraca
				$str = "INSERT INTO ZDEL_REKANAN_NERACA SELECT * FROM REKANAN_NERACA WHERE REKANAN_ID = ".$this->getField("REKANAN_ID")."";
				$this->query = $str;
	     	if ($this->execQuery($str)) {
	     		$str2 = "DELETE FROM REKANAN_NERACA WHERE REKANAN_ID = ".$this->getField("REKANAN_ID")."";
					$this->query = $str2;
					return $this->execQuery($str2);
	     	} else {
	     		return false;
	     	}
				break;

			case '9': // SPT Tahunan, PPH & PPN
				$str = "INSERT INTO ZDEL_REKANAN_PAJAK SELECT * FROM REKANAN_PAJAK WHERE REKANAN_ID = ".$this->getField("REKANAN_ID")."";
				$this->query = $str;
	     	if ($this->execQuery($str)) {
	     		$str2 = "DELETE FROM REKANAN_PAJAK WHERE REKANAN_ID = ".$this->getField("REKANAN_ID")."";
					$this->query = $str2;
					return $this->execQuery($str2);
	     	} else {
	     		return false;
	     	}
				break;

			case '10': // Tenaga Ahli
				$strInsert1 = "INSERT INTO ZDEL_REKANAN_TENAGA_AHLI_PEND SELECT * FROM REKANAN_TENAGA_AHLI_PEND WHERE REKANAN_TENAGA_AHLI_ID in (select rekanan_tenaga_ahli_id  from REKANAN_TENAGA_AHLI where rekanan_id = ".$this->getField("REKANAN_ID").")";
				$this->query = $strInsert1;
				$this->execQuery($strInsert1);

				$strInsert2 = "INSERT INTO ZDEL_REKANAN_TENAGA_AHLI_PENG SELECT * FROM REKANAN_TENAGA_AHLI_PENG WHERE REKANAN_TENAGA_AHLI_ID in (select rekanan_tenaga_ahli_id  from REKANAN_TENAGA_AHLI where rekanan_id = ".$this->getField("REKANAN_ID").")";
				$this->query = $strInsert2;
				$this->execQuery($strInsert2);

				$strInsert3 = "INSERT INTO ZDEL_REKANAN_TENAGA_AHLI_SERT SELECT * FROM REKANAN_TENAGA_AHLI_SERT WHERE REKANAN_TENAGA_AHLI_ID in (select rekanan_tenaga_ahli_id  from REKANAN_TENAGA_AHLI where rekanan_id = ".$this->getField("REKANAN_ID").")";
				$this->query = $strInsert3;
				$this->execQuery($strInsert3);

				$strInsert4 = "INSERT INTO ZDEL_REKANAN_TENAGA_AHLI SELECT * FROM REKANAN_TENAGA_AHLI WHERE REKANAN_ID  = ".$this->getField("REKANAN_ID")."";
				$this->query = $strInsert4;

	     	if ($this->execQuery($strInsert4)) {
	     		// 1 
	     		$str1 = "DELETE FROM REKANAN_TENAGA_AHLI_PEND WHERE REKANAN_TENAGA_AHLI_ID in (select rekanan_tenaga_ahli_id  from REKANAN_TENAGA_AHLI where rekanan_id = ".$this->getField("REKANAN_ID").")";
					$this->query = $str1;
					$this->execQuery($str1);

					// 2
	     		$str2 = "DELETE FROM REKANAN_TENAGA_AHLI_PENG WHERE REKANAN_TENAGA_AHLI_ID in (select rekanan_tenaga_ahli_id  from REKANAN_TENAGA_AHLI where rekanan_id = ".$this->getField("REKANAN_ID").")";
					$this->query = $str2;
					$this->execQuery($str2);

					// 3
	     		$str3 = "DELETE FROM REKANAN_TENAGA_AHLI_SERT WHERE REKANAN_TENAGA_AHLI_ID in (select rekanan_tenaga_ahli_id  from REKANAN_TENAGA_AHLI where rekanan_id = ".$this->getField("REKANAN_ID").")";
					$this->query = $str3;
					$this->execQuery($str3);

					// 4
	     		$str4 = "DELETE FROM REKANAN_TENAGA_AHLI WHERE REKANAN_ID = ".$this->getField("REKANAN_ID")."";
					$this->query = $str4;

					return $this->execQuery($str4);
	     	} else {
	     		return false;
	     	}
				break;

			case '11': // Pengalaman
				$strInsert1 = "INSERT INTO ZDEL_REKANAN_PENGALAMAN_BIDANG SELECT * FROM REKANAN_PENGALAMAN_BIDANG WHERE REKANAN_PENGALAMAN_ID in (select rekanan_pengalaman_id  FROM REKANAN_PENGALAMAN WHERE rekanan_id = ".$this->getField("REKANAN_ID").")";
				$this->query = $strInsert1;
				$this->execQuery($strInsert1);

				$strInsert2 = "INSERT INTO ZDEL_REKANAN_PENGALAMAN SELECT * FROM REKANAN_PENGALAMAN WHERE REKANAN_ID  = ".$this->getField("REKANAN_ID")."";
				$this->query = $strInsert2;

	     	if ($this->execQuery($strInsert2)) {
	     		// 1 
	     		$str1 = "DELETE FROM REKANAN_PENGALAMAN_BIDANG WHERE REKANAN_PENGALAMAN_ID in (select rekanan_pengalaman_id  FROM REKANAN_PENGALAMAN WHERE rekanan_id = ".$this->getField("REKANAN_ID").")";
					$this->query = $str1;
					$this->execQuery($str1);

					// 2
	     		$str2 = "DELETE FROM REKANAN_PENGALAMAN WHERE REKANAN_ID = ".$this->getField("REKANAN_ID")."";
					$this->query = $str2;

					return $this->execQuery($str2);
	     	} else {
	     		return false;
	     	}
				break;

			case '12': // Peralatan
				$str = "INSERT INTO ZDEL_REKANAN_PERALATAN SELECT * FROM REKANAN_PERALATAN WHERE REKANAN_ID = ".$this->getField("REKANAN_ID")."";
				$this->query = $str;
	     	if ($this->execQuery($str)) {
	     		$str2 = "DELETE FROM REKANAN_PERALATAN WHERE REKANAN_ID = ".$this->getField("REKANAN_ID")."";
					$this->query = $str2;
					return $this->execQuery($str2);
	     	} else {
	     		return false;
	     	}
				break;

			case '13': // Dokumen Teknis Perusahaan
				$str = "INSERT INTO ZDEL_REKANAN_SERTIFIKAT SELECT * FROM REKANAN_SERTIFIKAT WHERE REKANAN_ID = ".$this->getField("REKANAN_ID")."";
				$this->query = $str;
	     	if ($this->execQuery($str)) {
	     		$str2 = "DELETE FROM REKANAN_SERTIFIKAT WHERE REKANAN_ID = ".$this->getField("REKANAN_ID")."";
					$this->query = $str2;
					return $this->execQuery($str2);
	     	} else {
	     		return false;
	     	}
				break;

			case '14': // Profile & User Login
				$str = "INSERT INTO ZDEL_USER_LOGIN SELECT * FROM USER_LOGIN WHERE REKANAN_ID = ".$this->getField("REKANAN_ID")."";
				$this->query = $str;
	     	$this->execQuery($str);

				$str2 = "INSERT INTO ZDEL_REKANAN SELECT * FROM REKANAN WHERE REKANAN_ID = ".$this->getField("REKANAN_ID")."";
				$this->query = $str2;

	     	if ($this->execQuery($str2)) {
	     		$strDel = "DELETE FROM USER_LOGIN WHERE REKANAN_ID = ".$this->getField("REKANAN_ID")."";
					$this->query = $strDel;
					$this->execQuery($strDel);

					$strDel2 = "DELETE FROM REKANAN WHERE REKANAN_ID = ".$this->getField("REKANAN_ID")."";
					$this->query = $strDel2;
					return $this->execQuery($strDel2);

	     	} else {
	     		return false;
	     	}
				break;

			// case '15': // User Login
			// 	$str = "INSERT INTO ZDEL_REKANAN SELECT * FROM REKANAN WHERE REKANAN_ID = ".$this->getField("REKANAN_ID")."";
			// 	$this->query = $str;
	  //    	if ($this->execQuery($str)) {
	  //    		$str2 = "DELETE FROM REKANAN WHERE REKANAN_ID = ".$this->getField("REKANAN_ID")."";
			// 		$this->query = $str2;
			// 		return $this->execQuery($str2);
	  //    	} else {
	  //    		return false;
	  //    	}
			// 	break;
			
			default:
     		return false;
				break;
		}
				  
		// $this->query = $str;
  //   return $this->execQuery($str);
  }

 } 
?>