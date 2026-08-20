<?php
/**
 * @package 	eProcurement Application
 * @author 		eproc2025
 * @since 		25. Version 3.1
 * 
 */

  include_once('entity.php');

  class RekananPotensi extends Entity {

	var $query;
  function __construct(){
		parent::__construct();
	}

  function RekananSaham()
	{
    $this->Entity();
  }

  function selectByParams($view,$paramsArray=array(),$limit=-1,$from=-1, $statement='')
	{
		$str = " SELECT A.*, C.NAMA || ' ' || B.NAMA NAMA_PENYEDIA, B.NPWP, B.ALAMAT,
						 B.TELEPON_KODE || ' ' || B.TELEPON TELEPON, B.EMAIL, B.STATUS_VALIDASI, B.KOTA, B.WEBSITE   FROM ".$view." A
						 JOIN REKANAN B ON A.REKANAN_ID=B.REKANAN_ID
						 LEFT JOIN REKANAN_TIPE C ON  B.REKANAN_TIPE_ID = C.REKANAN_TIPE_ID
		 				 WHERE 1=1";

		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}

		$this->query = $str;
		$str .= $statement;

		return $this->selectLimit($str,$limit,$from);
  }

  function selectByParamsAllSearch($paramsArray=array(),$limit=-1,$from=-1, $statement='')
	{
		$str = " SELECT 
						    A.REKANAN_ID,
						    LOWER(B.NAMA || ' ' || A.NAMA) AS NAMA,
						    A.NPWP,
						    A.ALAMAT,
						    A.TELEPON_KODE || ' ' || A.TELEPON AS TELEPON,
						    A.EMAIL,
						    A.KOTA,
						    A.STATUS_VALIDASI,
						    A.WEBSITE,
						    A.REKANAN_KUALIFIKASI_ID,
						    C.NAMA AS NAMA_KUALIFIKASI,
						    string_agg(DISTINCT pengurus.nama, ' || ') AS keywords_pengurus,
						    string_agg(DISTINCT pengalaman.nama, ' || ') AS keywords_pengalaman,
						    string_agg(DISTINCT sertifikat.nama, ' || ') AS keywords_sertifikat,
						    string_agg(DISTINCT kbli.bidang_usaha_nama, ' || ') AS keywords_kbli
						FROM REKANAN A
						JOIN REKANAN_TIPE B ON A.REKANAN_TIPE_ID = B.REKANAN_TIPE_ID
						LEFT JOIN REKANAN_KUALIFIKASI C ON A.REKANAN_KUALIFIKASI_ID = C.REKANAN_KUALIFIKASI_ID
						LEFT JOIN view_potensi_admin_pengurus pengurus
						    ON pengurus.rekanan_id = A.rekanan_id
						LEFT JOIN view_potensi_teknis_pengalaman pengalaman
						    ON pengalaman.rekanan_id = A.rekanan_id
						LEFT JOIN view_potensi_teknis_sertifikat sertifikat
						    ON sertifikat.rekanan_id = A.rekanan_id
						LEFT JOIN view_potensi_kbli kbli
						    ON kbli.rekanan_id = A.rekanan_id
						WHERE 1=1 
							";

		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}

		$str .= $statement. ' 
						GROUP BY
						    A.REKANAN_ID,
						    B.NAMA,
						    A.NAMA,
						    A.NPWP,
						    A.ALAMAT,
						    A.TELEPON_KODE,
						    A.TELEPON,
						    A.EMAIL,
						    A.KOTA,
						    A.STATUS_VALIDASI,
						    A.WEBSITE,
						    A.REKANAN_KUALIFIKASI_ID,
						    C.NAMA';
		$this->query = $str;
		// echo $str; die;
		return $this->selectLimit($str,$limit,$from);
  }

 //  function selectByParamsAllSearch($paramsArray=array(),$limit=-1,$from=-1, $statement='')
	// {
	// 	$str = " SELECT A.* FROM (
	// 					  SELECT A.*,
	// 						(SELECT string_agg(distinct G.nama, ' || ' order by G.nama) as keywords_pengurus
	// 						    FROM view_potensi_admin_pengurus G
	// 								WHERE A.REKANAN_ID = G.REKANAN_ID
	// 						    GROUP BY G.rekanan_id) AS keywords_pengurus,
	// 						(SELECT string_agg(distinct G.nama, ' || ' order by G.nama) as keywords_pengalaman
	// 						    FROM view_potensi_teknis_pengalaman G
	// 							  WHERE A.REKANAN_ID = G.REKANAN_ID
	// 						    GROUP BY G.rekanan_id ) AS keywords_pengalaman,
	// 						(SELECT string_agg(distinct G.nama, ' || ' order by G.nama) as keywords_sertifikat
	// 						    FROM view_potensi_teknis_sertifikat G
	// 								WHERE A.REKANAN_ID = G.REKANAN_ID
	// 						    GROUP BY G.rekanan_id
	// 						) AS keywords_sertifikat,
	// 						(SELECT string_agg(distinct G.bidang_usaha_nama, ' || ' order by G.bidang_usaha_nama) as keywords_kbli
	// 						FROM view_potensi_kbli G
	// 						WHERE A.REKANAN_ID = G.REKANAN_ID
	// 						GROUP BY G.rekanan_id
	// 						) AS keywords_kbli
	// 						FROM (
	// 							SELECT A.REKANAN_ID, LOWER(B.NAMA || ' ' || A.NAMA) NAMA, A.NPWP, A.ALAMAT, A.TELEPON_KODE || ' ' || A.TELEPON TELEPON,
	// 							A.EMAIL, A.KOTA, STATUS_VALIDASI, A.WEBSITE, A.REKANAN_KUALIFIKASI_ID, C.NAMA NAMA_KUALIFIKASI
	// 							FROM REKANAN A
	// 							JOIN REKANAN_TIPE B ON A.REKANAN_TIPE_ID=B.REKANAN_TIPE_ID
	// 							JOIN REKANAN_KUALIFIKASI C ON A.REKANAN_KUALIFIKASI_ID=C.REKANAN_KUALIFIKASI_ID
	// 						) A
	// 					 ) A WHERE 1=1
	// 						";

	// 	while(list($key,$val) = each($paramsArray))
	// 	{
	// 		$str .= " AND $key = '$val' ";
	// 	}

	// 	$this->query = $str;
	// 	$str .= $statement;
	// 	// echo $str;
	// 	return $this->selectLimit($str,$limit,$from);
 //  }

  function getCountselectByParamsAllSearch($paramsArray=array(), $varStatement=""){
    $str = "SELECT COUNT(REKANAN_ID) AS ROWCOUNT FROM
            (
            	SELECT 
						    A.REKANAN_ID,
						    LOWER(B.NAMA || ' ' || A.NAMA) AS NAMA,
						    A.NPWP,
						    A.ALAMAT,
						    A.TELEPON_KODE || ' ' || A.TELEPON AS TELEPON,
						    A.EMAIL,
						    A.KOTA,
						    A.STATUS_VALIDASI,
						    A.WEBSITE,
						    A.REKANAN_KUALIFIKASI_ID,
						    C.NAMA AS NAMA_KUALIFIKASI,
						    string_agg(DISTINCT pengurus.nama, ' || ') AS keywords_pengurus,
						    string_agg(DISTINCT pengalaman.nama, ' || ') AS keywords_pengalaman,
						    string_agg(DISTINCT sertifikat.nama, ' || ') AS keywords_sertifikat,
						    string_agg(DISTINCT kbli.bidang_usaha_nama, ' || ') AS keywords_kbli
						FROM REKANAN A
						JOIN REKANAN_TIPE B ON A.REKANAN_TIPE_ID = B.REKANAN_TIPE_ID
						LEFT JOIN REKANAN_KUALIFIKASI C ON A.REKANAN_KUALIFIKASI_ID = C.REKANAN_KUALIFIKASI_ID
						LEFT JOIN view_potensi_admin_pengurus pengurus
						    ON pengurus.rekanan_id = A.rekanan_id
						LEFT JOIN view_potensi_teknis_pengalaman pengalaman
						    ON pengalaman.rekanan_id = A.rekanan_id
						LEFT JOIN view_potensi_teknis_sertifikat sertifikat
						    ON sertifikat.rekanan_id = A.rekanan_id
						LEFT JOIN view_potensi_kbli kbli
						    ON kbli.rekanan_id = A.rekanan_id 
						 WHERE 1=1 ";
    foreach ($paramsArray as $key => $value) {
      $str .= " AND $key = '$val' ";
    }
    $str .= $varStatement. ' 
						GROUP BY
						    A.REKANAN_ID,
						    B.NAMA,
						    A.NAMA,
						    A.NPWP,
						    A.ALAMAT,
						    A.TELEPON_KODE,
						    A.TELEPON,
						    A.EMAIL,
						    A.KOTA,
						    A.STATUS_VALIDASI,
						    A.WEBSITE,
						    A.REKANAN_KUALIFIKASI_ID,
						    C.NAMA ) A';
		// $str .= $varStatement;
    // echo $str;  die;
    $this->select($str);
    if($this->firstRow())
      return $this->getField("ROWCOUNT");
    else
       return 0;
  }

  function selectByParamsAdmin($paramsArray=array(),$limit=-1,$from=-1, $statement='')
	{
		$str = " SELECT A.ID, A.REKANAN_ID, A.NAMA, A.JABATAN, 'VIEW_POTENSI_ADMIN_PENGURUS' TABEL,
						 C.NAMA || ' ' || B.NAMA NAMA_PENYEDIA, B.NPWP, B.ALAMAT,
						 B.TELEPON_KODE || ' ' || B.TELEPON TELEPON, B.EMAIL, B.STATUS_VALIDASI, B.KOTA, B.WEBSITE FROM VIEW_POTENSI_ADMIN_PENGURUS A
						 JOIN REKANAN B ON A.REKANAN_ID=B.REKANAN_ID
						 LEFT JOIN REKANAN_TIPE C ON  B.REKANAN_TIPE_ID = C.REKANAN_TIPE_ID
		 				 WHERE 1=1";

		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}

		$this->query = $str;
		$str .= $statement;

		return $this->selectLimit($str,$limit,$from);
  }

  function getCountselectByParamsAdmin($paramsArray=array(), $varStatement=""){
    $str = "SELECT COUNT(REKANAN_ID) AS ROWCOUNT FROM
            (
             SELECT A.ID, A.REKANAN_ID, A.NAMA, '' JABATAN, 'VIEW_POTENSI_ADMIN_PENGURUS' TABEL,
						 C.NAMA || ' ' || B.NAMA NAMA_PENYEDIA, B.NPWP, B.ALAMAT,
						 B.TELEPON_KODE || ' ' || B.TELEPON TELEPON, B.EMAIL, B.STATUS_VALIDASI, B.KOTA, B.WEBSITE FROM VIEW_POTENSI_ADMIN_PENGURUS A
						 JOIN REKANAN B ON A.REKANAN_ID=B.REKANAN_ID
						 LEFT JOIN REKANAN_TIPE C ON  B.REKANAN_TIPE_ID = C.REKANAN_TIPE_ID
		 				 WHERE 1=1 ".$varStatement."
            ) A WHERE 1=1 ";
    foreach ($paramsArray as $key => $value) {
      $str .= " AND $key = '$val' ";
    }
    $this->select($str);
    if($this->firstRow())
      return $this->getField("ROWCOUNT");
    else
       return 0;
  }

  function selectByParamsTeknik($paramsArray=array(),$limit=-1,$from=-1, $statement='')
	{
		$str = " SELECT A.ID, A.REKANAN_ID, A.NAMA, '' JABATAN, A.TABEL, C.NAMA || ' ' || B.NAMA NAMA_PENYEDIA, B.NPWP, B.ALAMAT,
							B.TELEPON_KODE || ' ' || B.TELEPON TELEPON, B.EMAIL, B.STATUS_VALIDASI, B.KOTA, B.WEBSITE FROM (
							SELECT A.ID, A.REKANAN_ID, A.NAMA, 'VIEW_POTENSI_TEKNIS_PENGALAMAN' TABEL FROM VIEW_POTENSI_TEKNIS_PENGALAMAN A
							UNION
							SELECT B.ID, B.REKANAN_ID, B.NAMA, 'VIEW_POTENSI_TEKNIS_SERTIFIKAT' TABEL FROM VIEW_POTENSI_TEKNIS_SERTIFIKAT B
							) A
							JOIN REKANAN B ON A.REKANAN_ID=B.REKANAN_ID
							LEFT JOIN REKANAN_TIPE C ON  B.REKANAN_TIPE_ID = C.REKANAN_TIPE_ID
							WHERE 1=1";

		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}

		$this->query = $str;
		$str .= $statement; // ORDER BY A.TABEL ASC

		return $this->selectLimit($str,$limit,$from);
  }

  function getCountselectByParamsTeknik($paramsArray=array(), $varStatement=""){
    $str = "SELECT COUNT(REKANAN_ID) AS ROWCOUNT FROM
            (
             SELECT A.ID, A.REKANAN_ID, A.NAMA, '' JABATAN, A.TABEL, C.NAMA || ' ' || B.NAMA NAMA_PENYEDIA, B.NPWP, B.ALAMAT,
							B.TELEPON_KODE || ' ' || B.TELEPON TELEPON, B.EMAIL, B.STATUS_VALIDASI, B.KOTA, B.WEBSITE FROM (
							SELECT A.ID, A.REKANAN_ID, A.NAMA, 'VIEW_POTENSI_TEKNIS_PENGALAMAN' TABEL FROM VIEW_POTENSI_TEKNIS_PENGALAMAN A
							UNION
							SELECT B.ID, B.REKANAN_ID, B.NAMA, 'VIEW_POTENSI_TEKNIS_SERTIFIKAT' TABEL FROM VIEW_POTENSI_TEKNIS_SERTIFIKAT B
							) A
							JOIN REKANAN B ON A.REKANAN_ID=B.REKANAN_ID
							LEFT JOIN REKANAN_TIPE C ON  B.REKANAN_TIPE_ID = C.REKANAN_TIPE_ID
							WHERE 1=1 ".$varStatement."
            ) A WHERE 1=1 ";
    foreach ($paramsArray as $key => $value) {
      $str .= " AND $key = '$val' ";
    }
    $this->select($str);
    if($this->firstRow())
      return $this->getField("ROWCOUNT");
    else
       return 0;
  }

  function selectByParamsKbli($paramsArray=array(),$limit=-1,$from=-1, $statement='')
	{
		$str = " SELECT A.*, C.NAMA || ' ' || B.NAMA NAMA_PENYEDIA, B.NPWP, B.ALAMAT,
							B.TELEPON_KODE || ' ' || B.TELEPON TELEPON, B.EMAIL, B.STATUS_VALIDASI, B.KOTA, B.WEBSITE FROM (
							SELECT A.ID, A.REKANAN_ID, A.NAMA, 'VIEW_POTENSI_TEKNIS_PENGALAMAN' TABEL FROM VIEW_POTENSI_TEKNIS_PENGALAMAN A
							UNION
							SELECT B.ID, B.REKANAN_ID, B.NAMA, 'VIEW_POTENSI_TEKNIS_SERTIFIKAT' TABEL FROM VIEW_POTENSI_TEKNIS_SERTIFIKAT B
							) A
							JOIN REKANAN B ON A.REKANAN_ID=B.REKANAN_ID
							LEFT JOIN REKANAN_TIPE C ON  B.REKANAN_TIPE_ID = C.REKANAN_TIPE_ID
							WHERE 1=1";

		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}

		$this->query = $str;
		$str .= $statement; // ORDER BY A.TABEL ASC

		return $this->selectLimit($str,$limit,$from);
  }

  function getCountselectByParamsKbli($paramsArray=array(), $varStatement=""){
    $str = "SELECT COUNT(REKANAN_ID) AS ROWCOUNT FROM
            (
             SELECT '' from dual
							WHERE 1=1 ".$varStatement."
            ) A WHERE 1=1 ";
    foreach ($paramsArray as $key => $value) {
      $str .= " AND $key = '$val' ";
    }
    $this->select($str);
    if($this->firstRow())
      return $this->getField("ROWCOUNT");
    else
       return 0;
  }

  function selectByParamsAll($paramsArray=array(),$limit=-1,$from=-1, $statement='')
	{
		$str = " SELECT A.ID, A.REKANAN_ID, A.NAMA, A.JABATAN, A.TABEL,
						  C.NAMA || ' ' || B.NAMA NAMA_PENYEDIA, B.NPWP, B.ALAMAT,
							B.TELEPON_KODE || ' ' || B.TELEPON TELEPON, B.EMAIL, B.STATUS_VALIDASI, B.KOTA, B.WEBSITE FROM (
							SELECT A.ID, A.REKANAN_ID, A.NAMA, '' JABATAN, 'VIEW_POTENSI_TEKNIS_PENGALAMAN' TABEL FROM VIEW_POTENSI_TEKNIS_PENGALAMAN A
							UNION
							SELECT B.ID, B.REKANAN_ID, B.NAMA, '' JABATAN, 'VIEW_POTENSI_TEKNIS_SERTIFIKAT' TABEL FROM VIEW_POTENSI_TEKNIS_SERTIFIKAT B
							UNION
							SELECT C.ID, C.REKANAN_ID, C.NAMA, C.JABATAN, 'VIEW_POTENSI_ADMIN_PENGURUS' TABEL FROM VIEW_POTENSI_ADMIN_PENGURUS C
							) A
							JOIN REKANAN B ON A.REKANAN_ID=B.REKANAN_ID
							LEFT JOIN REKANAN_TIPE C ON  B.REKANAN_TIPE_ID = C.REKANAN_TIPE_ID
							WHERE 1=1  ";

		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}
		$this->query = $str;
		$str .= $statement; // ORDER BY A.TABEL ASC
		// echo $str;

		return $this->selectLimit($str,$limit,$from);
  }

  function getCountselectByParamsAll($paramsArray=array(), $varStatement=""){
    $str = "SELECT COUNT(REKANAN_ID) AS ROWCOUNT FROM
            (
             SELECT A.ID, A.REKANAN_ID, A.NAMA, A.JABATAN, A.TABEL,
						  C.NAMA || ' ' || B.NAMA NAMA_PENYEDIA, B.NPWP, B.ALAMAT,
							B.TELEPON_KODE || ' ' || B.TELEPON TELEPON, B.EMAIL, B.STATUS_VALIDASI, B.KOTA, B.WEBSITE FROM (
							SELECT A.ID, A.REKANAN_ID, A.NAMA, '' JABATAN, 'VIEW_POTENSI_TEKNIS_PENGALAMAN' TABEL FROM VIEW_POTENSI_TEKNIS_PENGALAMAN A
							UNION
							SELECT B.ID, B.REKANAN_ID, B.NAMA, '' JABATAN, 'VIEW_POTENSI_TEKNIS_SERTIFIKAT' TABEL FROM VIEW_POTENSI_TEKNIS_SERTIFIKAT B
							UNION
							SELECT C.ID, C.REKANAN_ID, C.NAMA, C.JABATAN, 'VIEW_POTENSI_ADMIN_PENGURUS' TABEL FROM VIEW_POTENSI_ADMIN_PENGURUS C
							) A
							JOIN REKANAN B ON A.REKANAN_ID=B.REKANAN_ID
							LEFT JOIN REKANAN_TIPE C ON  B.REKANAN_TIPE_ID = C.REKANAN_TIPE_ID
							WHERE 1=1  ".$varStatement."
            ) A WHERE 1=1 ";
    foreach ($paramsArray as $key => $value) {
      $str .= " AND $key = '$val' ";
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
