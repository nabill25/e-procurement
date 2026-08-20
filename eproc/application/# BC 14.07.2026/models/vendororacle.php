<?php
/**
 * @package 	eProcurement Application
 * @author 		eproc2025
 * @since 		25. Version 3.1
 *
 */

  include_once('entity.php');

  class Vendororacle extends Entity
  {

	var $query;

  function __construct(){
  		parent::__construct();
	}


	function insert()
	{
		$this->setField("REKANAN_ORACLE_ID", $this->getNextId("REKANAN_ORACLE_ID","REKANAN_ORACLE"));

		$str = "
		INSERT INTO REKANAN_ORACLE (REKANAN_ORACLE_ID, REKANAN_ID, JENIS, KODE_ORACLE, CATATAN, CREATED_BY,CREATED_DATE)
 			 	VALUES (
          ".$this->getField("REKANAN_ORACLE_ID").",
          ".$this->getField("REKANAN_ID").",
          '".$this->getField("JENIS")."',
          '".$this->getField("KODE_ORACLE")."',
          '".$this->getField("CATATAN")."',
				  ".$this->getField("CREATED_BY").",
          CURRENT_TIMESTAMP
				)";
		$this->query = $str;
		// echo $str;exit;
		return $this->execQuery($str);
  }

	 function update()
	{
		$str = "UPDATE REKANAN_ORACLE SET
         KODE_ORACLE = '".$this->getField("KODE_ORACLE")."',
         CATATAN = '".$this->getField("CATATAN")."',
         UPDATED_BY = '".$this->getField("CREATED_BY")."',
         UPDATED_DATE = CURRENT_TIMESTAMP
				WHERE REKANAN_ORACLE_ID = ".$this->getField("REKANAN_ORACLE_ID")."
				";
				$this->query = $str;
		return $this->execQuery($str);
  }

  function selectByParams($paramsArray=array(),$limit=-1,$from=-1, $statement='',  $order="ORDER BY REKANAN_ORACLE_ID ASC")
	{
		$str = "SELECT A.* FROM (
              SELECT aa.id, aa.jenis, aa.kode_eproc, concat(cc.nama || ' ' || aa.nama) nama, aa.npwp, aa.alamat, aa.rekanan_tipe_id, bb.rekanan_oracle_id, bb.kode_oracle, bb.catatan  from
              (
              	select a.rekanan_id id, '1' jenis, a.kode kode_eproc, a.nama, a.npwp, a.alamat, a.rekanan_tipe_id
              	from rekanan a
              	union all
              	select b.rekanan_retail_id id, '2' jenis, b.kode kode_eproc, b.nama, b.npwp, b.alamat, b.rekanan_tipe_id  from rekanan_retail b
              ) aa
              left join
              rekanan_oracle bb on aa.id=bb.rekanan_id and aa.jenis=bb.jenis
              join rekanan_tipe cc on aa.rekanan_tipe_id = cc.rekanan_tipe_id
            ) A where 1=1
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
		$str = "SELECT COUNT(ID) AS ROWCOUNT FROM (
              SELECT aa.*, bb.rekanan_oracle_id, bb.kode_oracle, bb.catatan  from
              (
                select a.rekanan_id id, '1' jenis, a.kode kode_eproc, a.nama, a.npwp, a.alamat
                from rekanan a
                union all
                select b.rekanan_retail_id id, '2' jenis, b.kode kode_eproc, b.nama, b.npwp, b.alamat  from rekanan_retail b
              ) aa
              left join
              rekanan_oracle bb on aa.id=bb.rekanan_id and aa.jenis=bb.jenis
            ) A where 1=1 ";
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
