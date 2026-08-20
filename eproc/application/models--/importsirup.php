<?php
/**
 * @package 	eProcurement Application
 * @author 		eproc2025
 * @since 		25. Version 3.1
 *
 */

  include_once('entity.php');

  class Importsirup extends Entity
  {

	var $query;

  function __construct(){
  		parent::__construct();
	}


	function insert()
	{
		$str = "
		INSERT INTO IMPORT_SIRUP (
		   ID, TAHUN, KODE_RUP, KODE_SA, KODE_DPSJ, NO_URUT, KATEGORI_PAKET_ID, NAMA_PAKET, NILAI_PAGU, LIST_KEGIATAN, WAKTU_AWAL,
       WAKTU_AKHIR, STATUS_PROSES, CREATED_BY, CREATED_AT, UPDATED_BY, UPDATED_AT, NAME, KATEGORI_PAKET, NAMA_SA, NAMA_DPSJ,
       METODE_PEMILIHAN, NAMA_JENIS_PEKERJAAN, HASIL_VERIFIKASI, IMPORT_DATE, IMPORT_FILE, SUMBER_DANA)
 			 	VALUES (
				  ".$this->getField("ID").",
          '".$this->getField("TAHUN")."',
          '".$this->getField("KODE_RUP")."',
          '".$this->getField("KODE_SA")."',
          '".$this->getField("KODE_DPSJ")."',
          '".$this->getField("NO_URUT")."',
          ".$this->getField("KATEGORI_PAKET_ID").",
          '".$this->getField("NAMA_PAKET")."',
          ".$this->getField("NILAI_PAGU").",
          '".$this->getField("LIST_KEGIATAN")."',
          '".$this->getField("WAKTU_AWAL")."',
          '".$this->getField("WAKTU_AKHIR")."',
          '".$this->getField("STATUS_PROSES")."',
          '".$this->getField("CREATED_BY")."',
          '".$this->getField("CREATED_AT")."',
          '".$this->getField("UPDATED_BY")."',
          '".$this->getField("UPDATED_AT")."',
          '".$this->getField("NAME")."',
          '".$this->getField("KATEGORI_PAKET")."',
          '".$this->getField("NAMA_SA")."',
          '".$this->getField("NAMA_DPSJ")."',
          '".$this->getField("METODE_PEMILIHAN")."',
          '".$this->getField("NAMA_JENIS_PEKERJAAN")."',
          '".$this->getField("HASIL_VERIIFIKASI")."',
          CURRENT_TIMESTAMP,
          '".$this->getField("IMPORT_FILE")."',
          '".$this->getField("SUMBER_DANA")."'
				)";
		$this->query = $str;
		$this->id = $this->getField("BANK_ID");
		// echo $str;exit;
		return $this->execQuery($str);
  }

  function update()
  {
   /*Auto-generate primary key(s) by next max value (integer) */
   $str = "UPDATE IMPORT_SIRUP SET
                TAHUN = '".$this->getField("TAHUN")."',
                KODE_RUP = '".$this->getField("KODE_RUP")."',
                KODE_SA = '".$this->getField("KODE_SA")."',
                KODE_DPSJ = '".$this->getField("KODE_DPSJ")."',
                NO_URUT = '".$this->getField("NO_URUT")."',
                KATEGORI_PAKET_ID = ".$this->getField("KATEGORI_PAKET_ID").",
                NAMA_PAKET = '".$this->getField("NAMA_PAKET")."',
                NILAI_PAGU = ".$this->getField("NILAI_PAGU").",
                LIST_KEGIATAN = '".$this->getField("LIST_KEGIATAN")."',
                WAKTU_AWAL = '".$this->getField("WAKTU_AWAL")."',
                WAKTU_AKHIR = '".$this->getField("WAKTU_AKHIR")."',
                STATUS_PROSES = '".$this->getField("STATUS_PROSES")."',
                CREATED_BY = '".$this->getField("CREATED_BY")."',
                CREATED_AT = '".$this->getField("CREATED_AT")."',
                UPDATED_BY = '".$this->getField("UPDATED_BY")."',
                UPDATED_AT = '".$this->getField("UPDATED_AT")."',
                NAME = '".$this->getField("NAME")."',
                KATEGORI_PAKET = '".$this->getField("KATEGORI_PAKET")."',
                NAMA_SA = '".$this->getField("NAMA_SA")."',
                NAMA_DPSJ = '".$this->getField("NAMA_DPSJ")."',
                METODE_PEMILIHAN = '".$this->getField("METODE_PEMILIHAN")."',
                NAMA_JENIS_PEKERJAAN = '".$this->getField("NAMA_JENIS_PEKERJAAN")."',
                HASIL_VERIFIKASI = '".$this->getField("HASIL_VERIFIKASI")."',
                IMPORT_DATE_UPDATE = CURRENT_TIMESTAMP,
                IMPORT_FILE_UPDATE = '".$this->getField("IMPORT_FILE")."',
                SUMBER_DANA = '".$this->getField("SUMBER_DANA")."'
       WHERE ID = ".$this->getField("ID")."
       ";
       // echo $str; die;
       $this->query = $str;
   return $this->execQuery($str);
 }

 function updatepr()
  {
   /*Auto-generate primary key(s) by next max value (integer) */
   $str = "UPDATE IMPORT_SIRUP SET
                NILAI_PAGU_PR = ".$this->getField("NILAI_PAGU_PR").",
                KODE_PR = '".$this->getField("KODE_PR")."'
       WHERE ID = ".$this->getField("ID")."
       ";
       // echo $str; die;
       $this->query = $str;
   return $this->execQuery($str);
 }

  function selectByParams($paramsArray=array(),$limit=-1,$from=-1, $statement='',  $order="ORDER BY ID ASC")
	{
		$str = "SELECT A.* FROM (
              SELECT A.ID, A.TAHUN, A.KODE_RUP, A.KODE_SA, A.KODE_DPSJ, A.NO_URUT, A.KATEGORI_PAKET_ID, A.NAMA_PAKET, A.NILAI_PAGU, A.LIST_KEGIATAN, A.WAKTU_AWAL,
              A.WAKTU_AKHIR, A.STATUS_PROSES, A.CREATED_BY, A.CREATED_AT, A.UPDATED_BY, A.UPDATED_AT, A.NAME, A.KATEGORI_PAKET, A.NAMA_SA, A.NAMA_DPSJ,
              A.METODE_PEMILIHAN, A.NAMA_JENIS_PEKERJAAN, A.HASIL_VERIFIKASI, A.IMPORT_DATE, A.IMPORT_FILE, B.PERMOHONAN_PAKET_ID, C.PERMOHONAN_PAKET_ANALISA_ID, A.KODE_PR, B.KODE_PR KODE_PR_PERMOHONAN, A.KODE_PR KODE_PR_RUP, A.NILAI_PAGU_PR, A.SUMBER_DANA
              FROM IMPORT_SIRUP A
              LEFT JOIN PERMOHONAN_PAKET B ON A.ID=B.SIRUP_ID
              LEFT JOIN PERMOHONAN_PAKET_ANALISA C ON B.PERMOHONAN_PAKET_ANALISA_ID=C.PERMOHONAN_PAKET_ANALISA_ID
            ) A
				WHERE 1 = 1 
				";

		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}
		$this->query = $str;
		$str .= $statement." ".$order;
    // echo $str; 
		return $this->selectLimit($str,$limit,$from);
  }

  function selectByParamsDetailRUP($paramsArray=array(),$limit=-1,$from=-1, $statement='',  $order="ORDER BY ID ASC")
	{
		$str = "SELECT A.* FROM (
              SELECT A.ID, A.TAHUN, A.KODE_RUP, A.KODE_SA, A.KODE_DPSJ, A.NO_URUT, A.KATEGORI_PAKET_ID, A.NAMA_PAKET, A.NILAI_PAGU, A.LIST_KEGIATAN, A.WAKTU_AWAL,
              A.WAKTU_AKHIR, A.STATUS_PROSES, A.CREATED_BY, A.CREATED_AT, A.UPDATED_BY, A.UPDATED_AT, A.NAME, A.KATEGORI_PAKET, A.NAMA_SA, A.NAMA_DPSJ,
              A.METODE_PEMILIHAN, A.NAMA_JENIS_PEKERJAAN, A.HASIL_VERIFIKASI, A.IMPORT_DATE, A.IMPORT_FILE, B.PERMOHONAN_PAKET_ID, B.PERMOHONAN_PAKET_ANALISA_ID, A.KODE_PR, A.KODE_PR KODE_PR_RUP, B.KODE_PR KODE_PR_PERMOHONAN, B.KODE_RUP KODE_RUP_PERMOHONAN,A.NILAI_PAGU_PR, A.SUMBER_DANA, B.NILAI_RAB_PR, B.NAMA NAMA_PAKET_PERMOHONAN, B.TANGGAL_WAKTU_PELAKSANAAN, B.LOKASI_PEKERJAAN, B.PENGADAAN_BYPASS, C.JK_NAME
    					FROM IMPORT_SIRUP A
              LEFT JOIN PERMOHONAN_PAKET B ON A.ID=B.SIRUP_ID
              LEFT JOIN CONTRACTING_JENIS_KONTRAK C ON B.JENIS_KONTRAK=C.CONTRACTINGJENISKONTRAKID
            ) A
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

	function getCountByParams($paramsArray=array(), $statement='')
	{
		$str = "SELECT COUNT(A.ID) AS ROWCOUNT FROM (
                SELECT A.* FROM (
                SELECT A.ID, A.TAHUN, A.KODE_RUP, A.KODE_SA, A.KODE_DPSJ, A.NO_URUT, A.KATEGORI_PAKET_ID, A.NAMA_PAKET, A.NILAI_PAGU, A.LIST_KEGIATAN, A.WAKTU_AWAL,
                      A.WAKTU_AKHIR, A.STATUS_PROSES, A.CREATED_BY, A.CREATED_AT, A.UPDATED_BY, A.UPDATED_AT, A.NAME, A.KATEGORI_PAKET, A.NAMA_SA, A.NAMA_DPSJ,
                      A.METODE_PEMILIHAN, A.NAMA_JENIS_PEKERJAAN, A.HASIL_VERIFIKASI, A.IMPORT_DATE, A.IMPORT_FILE, B.PERMOHONAN_PAKET_ID, C.PERMOHONAN_PAKET_ANALISA_ID
                      FROM IMPORT_SIRUP A
                      LEFT JOIN PERMOHONAN_PAKET B ON A.ID=B.SIRUP_ID
                      LEFT JOIN PERMOHONAN_PAKET_ANALISA C ON B.PERMOHONAN_PAKET_ANALISA_ID=C.PERMOHONAN_PAKET_ANALISA_ID
                ) A where 1=1 ";
		while(list($key,$val)=each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}
        $str .=  " ".$statement.") A";
		$this->select($str);
		if($this->firstRow())
			return $this->getField("ROWCOUNT");
		else
			return 0;
  }

  function selectByParamsPersiapan($paramsArray=array(),$limit=-1,$from=-1, $statement='',  $order="ORDER BY ID ASC")
	{
		$str = "SELECT A.* FROM (
              SELECT A.PERMOHONAN_PAKET_ID, A.USER_LOGIN_ID, A.UNIT_KERJA_ID, A.NAMA, A.LAST_CREATE_USER, A.LAST_CREATE_DATE, A.NILAI, A.APPROVAL APPROVAL_PERMOHONAN_PAKET,
              A.PERMOHONAN_PAKET_ANALISA_ID, A.TAHUN_ANGGARAN, A.JENIS_BARANG_JASA, D.NAMA JENIS_BARANG_JASA_NAMA, A.PERKIRAAN_BIAYA_HARGA, A.WAKTU_PENGGUNA_BARANGJASA,
              A.RENCANA_PENGADAAN, A.CREATED_BY, A.CREATED_DATE, A.KODE_RUP, A.KODE_PR, A.SIRUP_ID, A.STRATEGI_PENGADAAN, B.KODE_SA, B.KODE_DPSJ, B.NO_URUT, B.KATEGORI_PAKET_ID, B.LIST_KEGIATAN,B.STATUS_PROSES, B.CREATED_BY CREATED_BY_USER,  B.CREATED_AT, B.UPDATED_BY, B.UPDATED_AT, B.NAME, B.KATEGORI_PAKET, B.NAMA_SA, B.NAMA_DPSJ,
              B.METODE_PEMILIHAN, C.APPROVAL APPROVAL_PERMOHONAN_PAKET_ANALISA, E.KETERANGAN APPROVAL_PERMOHONAN_PAKET_ANALISA_TEXT, C.PUBLISH, (SELECT COUNT(AA.PERMOHONAN_PAKET_ID) TOTAL FROM PERMOHONAN_PAKET_APPROVAL AA WHERE AA.PERMOHONAN_PAKET_ID=A.PERMOHONAN_PAKET_ID) TOTAL_APPROVED, A.NILAI_RAB_PR, A.NILAI_HPS_PR
              FROM PERMOHONAN_PAKET A
              JOIN IMPORT_SIRUP B ON B.ID=A.SIRUP_ID
              JOIN PERMOHONAN_PAKET_ANALISA C on A.PERMOHONAN_PAKET_ANALISA_ID=C.PERMOHONAN_PAKET_ANALISA_ID
              JOIN PAKET_JENIS D on A.JENIS_BARANG_JASA=D.PAKET_JENIS_ID
              JOIN PERMOHONAN_PAKET_ANALISA_MATRIX E ON C.APPROVAL=E.PERMOHONAN_PAKET_ANALISA_MATRIX_ID
            ) A
				WHERE 1 = 1
				";

		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}
    // echo $str; die;
		$this->query = $str;
		$str .= $statement." ".$order;

		return $this->selectLimit($str,$limit,$from);
  }

  function getCountByParamsPersiapan($paramsArray=array(), $statement='')
	{
		$str = "SELECT COUNT(A.PERMOHONAN_PAKET_ID) AS ROWCOUNT FROM (
              SELECT A.PERMOHONAN_PAKET_ID, A.USER_LOGIN_ID, A.UNIT_KERJA_ID, A.NAMA, A.LAST_CREATE_USER, A.LAST_CREATE_DATE, A.NILAI, A.APPROVAL APPROVAL_PERMOHONAN_PAKET,
              A.PERMOHONAN_PAKET_ANALISA_ID, A.TAHUN_ANGGARAN, A.JENIS_BARANG_JASA, D.NAMA JENIS_BARANG_JASA_NAMA, A.PERKIRAAN_BIAYA_HARGA, A.WAKTU_PENGGUNA_BARANGJASA,
              A.RENCANA_PENGADAAN, A.CREATED_BY, A.CREATED_DATE, A.KODE_RUP, A.KODE_PR, A.SIRUP_ID,A.STRATEGI_PENGADAAN,B.KODE_SA, B.KODE_DPSJ, B.NO_URUT, B.KATEGORI_PAKET_ID, B.LIST_KEGIATAN,B.STATUS_PROSES, B.CREATED_BY CREATED_BY_USER, B.CREATED_AT, B.UPDATED_BY, B.UPDATED_AT, B.NAME, B.KATEGORI_PAKET, B.NAMA_SA, B.NAMA_DPSJ,
              B.METODE_PEMILIHAN, C.APPROVAL APPROVAL_PERMOHONAN_PAKET_ANALISA, C.PUBLISH
              FROM PERMOHONAN_PAKET A
              JOIN IMPORT_SIRUP B ON B.ID=A.SIRUP_ID
              JOIN PERMOHONAN_PAKET_ANALISA C on A.PERMOHONAN_PAKET_ANALISA_ID=C.PERMOHONAN_PAKET_ANALISA_ID
              JOIN PAKET_JENIS D on A.JENIS_BARANG_JASA=D.PAKET_JENIS_ID
          ) A
				WHERE 1 = 1 ".$statement;
		while(list($key,$val)=each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}
    // echo $str; die;
		$this->select($str);
		if($this->firstRow())
			return $this->getField("ROWCOUNT");
		else
			return 0;
  }

  function selectSA($paramsArray=array(),$limit=-1,$from=-1, $statement='',  $order="")
  {
    $str = "SELECT A.* FROM (
            SELECT concat(kode_sa || ' - ' || nama_sa) nama, kode_sa from import_sirup
            group by kode_sa, nama_sa
            order by kode_sa asc 
            ) A WHERE 1=1
        ";

    while(list($key,$val) = each($paramsArray))
    {
      $str .= " AND $key = '$val' ";
    }
    $this->query = $str;
    $str .= $statement." ".$order;

    return $this->selectLimit($str,$limit,$from);
  }

  function selectDPSJ($paramsArray=array(),$limit=-1,$from=-1, $statement='',  $order="")
  {
    $str = "SELECT A.* FROM (
            SELECT concat(kode_dpsj || ' - ' || nama_dpsj) nama, kode_dpsj
            from import_sirup
            where kode_dpsj <> '%%'
            group by kode_dpsj, nama_dpsj
            order by kode_dpsj asc 
            ) A WHERE 1=1
        ";
    while(list($key,$val) = each($paramsArray))
    {
      $str .= " AND $key = '$val' ";
    }
    $this->query = $str;
    $str .= $statement." ".$order;

    return $this->selectLimit($str,$limit,$from);
  }

}
?>
