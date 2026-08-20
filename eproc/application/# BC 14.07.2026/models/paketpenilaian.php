<?php 
/**
 * @package 	eProcurement Application
 * @author 		eproc2025
 * @since 		25. Version 3.1
 * 
 */
  
include_once('entity.php');

class PaketPenilaian extends Entity{ 

	var $query;
    /**
    * Class constructor.
    **/
    function __construct(){
  		parent::__construct();
  	}

    function PaketPenilaian()
	{
      $this->Entity(); 
    }

    /** 
    * Hitung jumlah record berdasarkan parameter (array). 
    * @param array paramsArray Array of parameter. Contoh array("id"=>"xxx","NAMA"=>"yyy") 
    * @return long Jumlah record yang sesuai kriteria 
    **/ 
    function getCountByParams($paramsArray=array())
	{
		$str = "SELECT COUNT(PPT_ID) AS ROWCOUNT FROM PAKET_PENILAIAN_TEMPLATE WHERE PPT_PARENT_ID <> '0' "; 
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

	function selectTemplate($paramsArray=array(),$limit=-1,$from=-1, $statement='', $order="GROUP BY A.TEMPLATE ORDER BY A.TEMPLATE ASC")
	{
		$str = "
			SELECT A.TEMPLATE
			FROM PAKET_PENILAIAN_TEMPLATE A 
				"; 
		
		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}
		
		$str .= $statement." ".$order;
		// echo $str; die();
		$this->query = $str;
		return $this->selectLimit($str,$limit,$from); 
  }

	function selectParent($paramsArray=array(),$limit=-1,$from=-1, $statement='', $order="ORDER BY A.PPT_ID ASC")
	{
		$str = "
			SELECT A.PPT_ID, A.KODE, A.PPT_PARENT_ID, A.NAMA, A.NOTE, A.NILAI, A.PRESENTASI, A.NOTE_5, A.NOTE_4, A.NOTE_3, A.NOTE_2, A.NOTE_1
			FROM PAKET_PENILAIAN_TEMPLATE A
			WHERE A.PPT_PARENT_ID = '0' 
				"; 
		
		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}
		
		$str .= $statement." ".$order;
		// echo $str; die();
		$this->query = $str;
		return $this->selectLimit($str,$limit,$from); 
  }

    function selectChild($paramsArray=array(),$limit=-1,$from=-1, $statement='', $order="ORDER BY A.PPT_ID ASC")
	{
		$str = "
			SELECT A.PPT_ID, A.KODE, A.PPT_PARENT_ID, A.NAMA, A.NOTE, A.NILAI, A.PRESENTASI, A.NOTE_5, A.NOTE_4, A.NOTE_3, A.NOTE_2, A.NOTE_1
			FROM PAKET_PENILAIAN_TEMPLATE A
			WHERE A.PPT_PARENT_ID <> '0' 
				"; 
		
		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}
		
		$str .= $statement." ".$order;
		// echo $str; die();
		$this->query = $str;
		return $this->selectLimit($str,$limit,$from); 
    }

    function selectPenilaian($paramsArray=array(),$limit=-1,$from=-1, $statement='', $order="ORDER BY A.PPT_ID ASC")
	{
		$str = "
			SELECT A.PPT_ID, A.PAKET_ID, A.REKANAN_ID, A.NILAI, A.NAMA, A.NOTE, A.PPT_PARENT_ID, A.TEMPLATE, A.APPROVAL_UNIT, A.APPROVAL_KASUBDIT_ID, A.APPROVAL_PPK_ID,
B.USER_NAMA NAMA_PIC_UNIT, B.USER_JABATAN JABATAN_UNIT, C.USER_NAMA NAMA_KASUBDIT, C.USER_JABATAN JABATAN_KASUBDIT, D.USER_NAMA NAMA_PPK, D.USER_JABATAN JABATAN_PPK
			FROM PAKET_PENILAIAN_REKANAN A
			LEFT JOIN USER_LOGIN B ON A.APPROVAL_UNIT_ID=B.USER_LOGIN_ID
			LEFT JOIN USER_LOGIN C ON A.APPROVAL_KASUBDIT_ID=C.USER_LOGIN_ID
			LEFT JOIN USER_LOGIN D ON A.APPROVAL_PPK_ID=D.USER_LOGIN_ID
			WHERE A.PPT_PARENT_ID IS NOT NULL
				"; 
		
		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}
		
		$str .= $statement." ".$order;
		// echo $str; die();
		$this->query = $str;
		return $this->selectLimit($str,$limit,$from); 
    }

    function insert()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$this->setField("PPR_ID", $this->getNextId("PPR_ID","PAKET_PENILAIAN_REKANAN")); 

		$str = "
				INSERT INTO PAKET_PENILAIAN_REKANAN (
					PPR_ID, 
					PAKET_ID, 
					REKANAN_ID,
					NILAI, 
					CREATED_BY, 
					CREATED_DATE,
					PPT_ID,
					NAMA,
					NOTE,
					PPT_PARENT_ID,
					PRESENTASI,
					CONTRACTINGREKANANID ) 
				VALUES ( '".$this->getField("PPR_ID")."', 
						'".$this->getField("PAKET_ID")."',
						'".$this->getField("REKANAN_ID")."', 
						'".$this->getField("NILAI")."',
						'".$this->getField("CREATED_BY")."',
						CURRENT_DATE,
						'".$this->getField("PPT_ID")."',
						'".$this->getField("NAMA")."',
						'".$this->getField("NOTE")."',
						'".$this->getField("PPT_PARENT_ID")."',
						".$this->getField("PRESENTASI").",
						".$this->getField("CONTRACTINGREKANANID")."
						)
		"; 
		$this->query = $str;
		// echo $str;exit;
		return $this->execQuery($str);
    }

    function delete()
	{
        $str = "DELETE FROM PAKET_PENILAIAN_REKANAN
                WHERE  
                  CREATED_BY = '".$this->getField("CREATED_BY")."' AND
                  REKANAN_ID = ".$this->getField("REKANAN_ID")." AND
                  PPT_PARENT_ID = ".$this->getField("PPT_PARENT_ID")." AND
                  CONTRACTINGREKANANID = ".$this->getField("CONTRACTINGREKANANID")."
                  "; 
				  
		$this->query = $str;
        return $this->execQuery($str);
    }

  function deleteAll()
	{
        $str = "DELETE FROM PAKET_PENILAIAN_REKANAN
                WHERE  
                  REKANAN_ID = ".$this->getField("REKANAN_ID")." AND
                  PPT_PARENT_ID = ".$this->getField("PPT_PARENT_ID")." AND
                  CONTRACTINGREKANANID = ".$this->getField("CONTRACTINGREKANANID")."
                  "; 
				  
		$this->query = $str;
        return $this->execQuery($str);
    }

 //  function hasilNilai($paket_id,$rekanan_id,$limit=-1,$from=-1)
	// {
 //        $str = " SELECT z.NAMA, y.NILAI, y.TOTAL_RECORD, y.HASIL,
	// 				case 
	// 					when y.HASIL >= 5 then 'A'
	// 					when (y.HASIL >= 4) and (y.HASIL < 5) then 'B'
	// 					when (y.HASIL >= 3) and (y.HASIL < 4) then 'C'
	// 					when (y.HASIL >= 2) and (y.HASIL < 3) then 'D'
	// 					else 'E'
	// 				end as GRADE
	// 				from (
	// 					select x.PPT_PARENT_ID, x.NILAI, x.TOTAL_RECORD, (x.NILAI/x.TOTAL_RECORD) HASIL 
	// 						from (
	// 							select a.PPT_PARENT_ID, sum(a.NILAI) NILAI, count(a.PPT_PARENT_ID) TOTAL_RECORD  
	// 							from (
	// 							select a.REKANAN_ID, a.NILAI, a.PPT_ID, a.PPT_PARENT_ID
	// 							from public.PAKET_PENILAIAN_REKANAN a
	// 							where a.PAKET_ID=".$paket_id." and a.REKANAN_ID=".$rekanan_id."
	// 						) a 
	// 						group by a.PPT_PARENT_ID 
	// 					) x
	// 				) y
	// 				join PAKET_PENILAIAN_TEMPLATE z on y.PPT_PARENT_ID=z.PPT_ID
	// 				order by z.KODE asc 
 //                  "; 
	// 			  // echo $str;
	// 	$this->query = $str;
 //        return $this->selectLimit($str,$limit,$from); 
 //  }

  function getHasil($contractingrekananid,$rekanan_id,$limit=-1,$from=-1)
	{
    $str = " SELECT a.*, concat(b.kode,'. ',b.nama) nama  from view_penilaian_rekanan_hitung a
						join paket_penilaian_template b on a.ppt_parent_id=b.ppt_id
						WHERE a.contractingrekananid=".$contractingrekananid." and a.rekanan_id=".$rekanan_id."
						ORDER BY b.kode asc 
              "; 
		  // echo $str;
		$this->query = $str;
    return $this->selectLimit($str,$limit,$from); 
  }

  function hasilNilai($paket_id,$rekanan_id,$limit=-1,$from=-1)
	{
        $str = " SELECT z.NAMA, y.NILAI, y.TOTAL_RECORD, y.HASIL,
					case 
						when y.HASIL >= 5 then 'A'
						when (y.HASIL >= 4) and (y.HASIL < 5) then 'B'
						when (y.HASIL >= 3) and (y.HASIL < 4) then 'C'
						when (y.HASIL >= 2) and (y.HASIL < 3) then 'D'
						else 'E'
					end as GRADE
					from (
						select x.PPT_PARENT_ID, x.NILAI, x.TOTAL_RECORD, (x.NILAI/x.TOTAL_RECORD) HASIL 
							from (
								select a.PPT_PARENT_ID, sum(a.NILAI) NILAI, count(a.PPT_PARENT_ID) TOTAL_RECORD  
								from (
								select a.REKANAN_ID, a.NILAI, a.PPT_ID, a.PPT_PARENT_ID
								from public.PAKET_PENILAIAN_REKANAN a
								where a.PAKET_ID=".$paket_id." and a.REKANAN_ID=".$rekanan_id."
							) a 
							group by a.PPT_PARENT_ID 
						) x
					) y
					join PAKET_PENILAIAN_TEMPLATE z on y.PPT_PARENT_ID=z.PPT_ID
					order by z.KODE asc 
                  "; 
				  // echo $str;
		$this->query = $str;
        return $this->selectLimit($str,$limit,$from); 
    }

  function getHasilByRekananId($rekanan_id,$limit=-1,$from=-1)
	{
    $str = " SELECT a.*, b.nama  from view_penilaian_rekanan_hitung a
						join paket_penilaian_template b on a.ppt_parent_id=b.ppt_id
						WHERE a.rekanan_id=".$rekanan_id."
              "; 
		  // echo $str;
		$this->query = $str;
    return $this->selectLimit($str,$limit,$from); 
  }

  function getHasilByPaketPengadaan($rekanan_id,$limit=-1,$from=-1)
	{
    $str = " SELECT b.nama, a.rekanan_id, a.contractingrekananid from view_penilaian_rekanan_hitung a 
						join contracting_rekanan b on a.contractingrekananid=b.contractingrekananid
						WHERE a.rekanan_id=".$rekanan_id."
						group by b.nama, a.rekanan_id, a.contractingrekananid
              "; 
		  // echo $str;
		$this->query = $str;
    return $this->selectLimit($str,$limit,$from); 
  }

    function getGroupPaketByRekanan($rekanan_id,$limit=-1,$from=-1)
	{
        $str = " SELECT b.nama, a.paket_id from 
				 PAKET_PENILAIAN_REKANAN a
			  	 join PAKET b on a.PAKET_ID=b.PAKET_ID
				 where a.REKANAN_ID=".$rekanan_id."
				 GROUP BY b.NAMA, a.PAKET_ID
				 ORDER BY a.PAKET_ID ASC
               "; 
				  // echo $str;
		$this->query = $str;
        return $this->selectLimit($str,$limit,$from); 
    }

  function setApprovalPpk() // Done
  {
    $str = "UPDATE PAKET_PENILAIAN_REKANAN 
            SET APPROVAL_PPK = '".$this->getField("APPROVAL_PPK")."', 
                APPROVAL_PPK_DATE = CURRENT_TIMESTAMP ,
            		APPROVAL_PPK_ID = ".$this->getField("CREATED_BY")."
            WHERE CONTRACTINGREKANANID = ".$this->getField("CONTRACTINGREKANANID")."
            ";
    $this->query = $str;
    if ($this->execQuery($str)) {
      return TRUE;
    } else {
      return FALSE;
    }
  }

  function setApprovalKasubdit() // Done
  {
    $str = "UPDATE PAKET_PENILAIAN_REKANAN 
            SET APPROVAL_KASUBDIT = '".$this->getField("APPROVAL_KASUBDIT")."', 
                APPROVAL_KASUBDIT_DATE = CURRENT_TIMESTAMP,
            		APPROVAL_KASUBDIT_ID = ".$this->getField("CREATED_BY")."
            WHERE CONTRACTINGREKANANID = ".$this->getField("CONTRACTINGREKANANID")."
            ";
    $this->query = $str;
    if ($this->execQuery($str)) {
      return TRUE;
    } else {
      return FALSE;
    }
  }

  function setApprovalUnit() // Done
  {
    $str = "UPDATE PAKET_PENILAIAN_REKANAN 
            SET APPROVAL_UNIT = '".$this->getField("APPROVAL_UNIT")."', 
                APPROVAL_UNIT_DATE = CURRENT_TIMESTAMP,
            		APPROVAL_UNIT_ID = ".$this->getField("CREATED_BY")."
            WHERE CONTRACTINGREKANANID = ".$this->getField("CONTRACTINGREKANANID")."
            ";
    $this->query = $str;
    if ($this->execQuery($str)) {
      return TRUE;
    } else {
      return FALSE;
    }
  }
}
?>