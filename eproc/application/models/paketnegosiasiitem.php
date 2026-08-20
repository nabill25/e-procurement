<?php
/**
 * @package 	eProcurement Application
 * @author 		eproc2025
 * @since 		25. Version 3.1
 *
 */

  include_once('entity.php');

  class Paketnegosiasiitem extends Entity{

	var $query; 

  function Paketnegosiasiitem()
	{
    $this->Entity();
  }
 
	function inserItem()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$this->setField("PAKET_NEGOSIASI_ITEM_ID", $this->getNextId("PAKET_NEGOSIASI_ITEM_ID","PAKET_NEGOSIASI_ITEM"));

		$str = "
		INSERT INTO PAKET_NEGOSIASI_ITEM (
   			           PAKET_NEGOSIASI_ITEM_ID, PAKET_ID, URAIAN, VOLUME,
   					   SATUAN_VOLUME, DURASI, SATUAN_DURASI, HARGA_SATUAN, JUMLAH_HARGA, NILAI_PENAWARAN, JUMLAH_PENAWARAN, NILAI_NEGOSIASI, JUMLAH_NEGOSIASI, IMPORT_DATE, FILE_NAMA, STATUS_NEGO)
			 	VALUES (
					   ".$this->getField("PAKET_NEGOSIASI_ITEM_ID").", ".$this->getField("PAKET_ID").", '".$this->getField("URAIAN")."', ".$this->getField("VOLUME").",
					   '".$this->getField("SATUAN_VOLUME")."', ".$this->getField("DURASI").", '".$this->getField("SATUAN_DURASI")."',
					   ".$this->getField("HARGA_SATUAN").", ".$this->getField("JUMLAH_HARGA").", ".$this->getField("NILAI_PENAWARAN").", ".$this->getField("JUMLAH_PENAWARAN").",
					   ".$this->getField("NILAI_NEGOSIASI").",
					   ".$this->getField("JUMLAH_NEGOSIASI").",
					   CURRENT_TIMESTAMP,
					   '".$this->getField("FILE_NAMA")."',
					   '".$this->getField("STATUS_NEGO")."'
				)";
		$this->query = $str;
		// echo $str;
		return $this->execQuery($str);
  }

  function update()
	{
		$str = "UPDATE PAKET_NEGOSIASI_ITEM SET
				  URAIAN = '".$this->getField("URAIAN")."',
				  VOLUME = ".$this->getField("VOLUME").",
				  SATUAN_VOLUME = '".$this->getField("SATUAN_VOLUME")."',
				  DURASI = ".$this->getField("DURASI").",
				  SATUAN_DURASI = '".$this->getField("SATUAN_DURASI")."',
				  HARGA_SATUAN = ".$this->getField("HARGA_SATUAN").",
				  JUMLAH_HARGA = ".$this->getField("JUMLAH_HARGA").",
				  NILAI_PENAWARAN = ".$this->getField("NILAI_PENAWARAN").",
				  JUMLAH_PENAWARAN = ".$this->getField("JUMLAH_PENAWARAN").",
				  NILAI_NEGOSIASI = ".$this->getField("NILAI_NEGOSIASI").",
				  JUMLAH_NEGOSIASI = ".$this->getField("JUMLAH_NEGOSIASI").",
				  UPDATED_BY = ".$this->getField("UPDATED_BY").",
				  UPDATED_DATE = CURRENT_TIMESTAMP
				WHERE PAKET_NEGOSIASI_ITEM_ID = ".$this->getField("PAKET_NEGOSIASI_ITEM_ID")."
				";
				$this->query = $str;
		return $this->execQuery($str);
  }

  function updateNilaiNego()
	{
		$str = "UPDATE PAKET_NEGOSIASI_ITEM SET
				  NILAI_NEGOSIASI = ".$this->getField("NILAI_NEGOSIASI").",
				  JUMLAH_NEGOSIASI = ".$this->getField("JUMLAH_NEGOSIASI").",
				  UPDATED_BY = ".$this->getField("UPDATED_BY").",
				  UPDATED_DATE = CURRENT_TIMESTAMP
				WHERE PAKET_NEGOSIASI_ITEM_ID = ".$this->getField("PAKET_NEGOSIASI_ITEM_ID")."
				";
				$this->query = $str;
		return $this->execQuery($str);
  }

  function updateTotalNego()
	{
		$str = "UPDATE PAKET_NEGOSIASI SET
				  UNIT_PRICE = ".$this->getField("UNIT_PRICE").",
				  PPN_JUMLAH_HARGA_SATUAN = ".$this->getField("PPN_JUMLAH_HARGA_SATUAN").",
				  PPN_JUMLAH_HARGA_PENAWARAN = ".$this->getField("PPN_JUMLAH_HARGA_PENAWARAN").",
				  PPN_JUMLAH_HARGA_NEGO = ".$this->getField("PPN_JUMLAH_HARGA_NEGO").",
				  PPN = ".$this->getField("PPN").",
				  UPDATED_BY = ".$this->getField("UPDATED_BY").",
				  UPDATED_DATE = CURRENT_TIMESTAMP
				WHERE PAKET_ID = ".$this->getField("PAKET_ID")."
				";
				$this->query = $str;
		return $this->execQuery($str);
  }

  function updateTotalNegoRekanan()
	{
		$str = "UPDATE PAKET_NEGOSIASI SET
				  UNIT_PRICE = ".$this->getField("UNIT_PRICE").",
				  PPN_JUMLAH_HARGA_NEGO = ".$this->getField("PPN_JUMLAH_HARGA_NEGO").",
				  UPDATED_BY = ".$this->getField("UPDATED_BY").",
				  UPDATED_DATE = CURRENT_TIMESTAMP
				WHERE PAKET_ID = ".$this->getField("PAKET_ID")."
				";
				$this->query = $str;
		return $this->execQuery($str);
  }

  function updateNegoTerima()
	{
		$str = "UPDATE PAKET_NEGOSIASI SET
				  SETUJUI = '1',
				  UPDATED_BY = ".$this->getField("UPDATED_BY").",
				  UPDATED_DATE = CURRENT_TIMESTAMP
				WHERE PAKET_ID = ".$this->getField("PAKET_ID")."
				";
				$this->query = $str;
		return $this->execQuery($str);
  }

  function updateNegoRekanan()
	{
		$str = "UPDATE PAKET_NEGOSIASI_ITEM SET
				  NILAI_NEGOSIASI = ".$this->getField("NILAI_NEGOSIASI").",
				  JUMLAH_NEGOSIASI = ".$this->getField("JUMLAH_NEGOSIASI").",
				  STATUS_NEGO = '".$this->getField("STATUS_NEGO")."',
				  APPROVED_BY = ".$this->getField("UPDATED_BY").",
				  APPROVED_DATE = CURRENT_TIMESTAMP
				WHERE PAKET_NEGOSIASI_ITEM_ID = ".$this->getField("PAKET_NEGOSIASI_ITEM_ID")."
				";
				$this->query = $str;
		return $this->execQuery($str);
  }

  function updateStatusAll()
	{
		// $str = "UPDATE PAKET_NEGOSIASI_ITEM SET
		// 			  STATUS_NEGO = '".$this->getField("STATUS_NEGO")."',
		// 			  UPDATED_BY = ".$this->getField("UPDATED_BY").",
		// 			  UPDATED_DATE = CURRENT_TIMESTAMP
		// 				WHERE PAKET_ID = ".$this->getField("PAKET_ID")." AND (STATUS_NEGO = '0' OR STATUS_NEGO = '3')
		// 		";
		$str = "UPDATE PAKET_NEGOSIASI_ITEM SET
					  STATUS_NEGO = '".$this->getField("STATUS_NEGO")."',
					  UPDATED_BY = ".$this->getField("UPDATED_BY").",
					  UPDATED_DATE = CURRENT_TIMESTAMP
						WHERE PAKET_ID = ".$this->getField("PAKET_ID")."
				";
				$this->query = $str;
		return $this->execQuery($str);
  }

   
	function deleteItem()
	{
    $str = "DELETE FROM PAKET_NEGOSIASI_ITEM WHERE PAKET_NEGOSIASI_ITEM_ID = ".$this->getField("PAKET_NEGOSIASI_ITEM_ID")."";
		$this->query = $str;
    return $this->execQuery($str);
  }

  /**
  * Cari record berdasarkan array parameter dan limit tampilan
  * @param array paramsArray Array of parameter. Contoh array("id"=>"xxx","KODE"=>"yyy")
  * @param int limit Jumlah maksimal record yang akan diambil
  * @param int from Awal record yang diambil
  * @return boolean True jika sukses, false jika tidak
  **/
  function selectByParams($paramsArray=array(),$limit=-1,$from=-1, $statement='', $order='')
	{
		$str = "SELECT
					PAKET_NEGOSIASI_ITEM_ID, PAKET_ID, URAIAN, VOLUME, SATUAN_VOLUME, DURASI, SATUAN_DURASI, HARGA_SATUAN, JUMLAH_HARGA, NILAI_PENAWARAN, JUMLAH_PENAWARAN,	ROUND(NILAI_PENAWARAN/HARGA_SATUAN*100,2) PERSENTASE_PENAWARAN, NILAI_NEGOSIASI, JUMLAH_NEGOSIASI, ROUND(NILAI_NEGOSIASI/HARGA_SATUAN*100,2) PERSENTASE_NEGOSIASI, IMPORT_DATE, FILE_NAMA, STATUS_NEGO, STATUS_NEGO STATUS_NEGO_ID, APPROVED_BY, APPROVED_DATE, UPDATED_BY, UPDATED_DATE
				FROM PAKET_NEGOSIASI_ITEM A WHERE PAKET_NEGOSIASI_ITEM_ID IS NOT NULL ";

		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}

    $str .= $statement." ".$order;
		$this->query = $str;
		// echo $str; die;
		return $this->selectLimit($str,$limit,$from);
  } 

  function getCountByParams($paramsArray=array(),$statement='', $order='')
	{
		$str = "SELECT COUNT(PAKET_NEGOSIASI_ITEM_ID) AS ROWCOUNT FROM (
		SELECT * FROM PAKET_NEGOSIASI_ITEM WHERE PAKET_NEGOSIASI_ITEM_ID IS NOT NULL ";
		while(list($key,$val)=each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}

   		 $str .= $statement;
  		$str .= ') A where 1 = 1';
		$this->select($str);
		if($this->firstRow())
			return $this->getField("ROWCOUNT");
		else
			return 0;
  } 
}
?>
