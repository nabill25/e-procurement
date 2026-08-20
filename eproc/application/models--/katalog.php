<?php
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 * 
 */

  include_once('entity.php');

  class Katalog extends Entity{

	var $query;

    function __construct(){
      $this->Entity();
    }

    function canInsert(){
      return true;
    }

    function insert(){
      if(!$this->canInsert())
        showMessageDlg("Data Users tidak dapat di-insert",true);
      else{
	  	/*Auto-generate primary key(s) by next max value (integer) */
		    $this->setField("KATALOGID", $this->getNextId("KATALOGID","KATALOG"));

		$str = "
				INSERT INTO KATALOG (
				   KATALOGID, NOPRODUK, NAMAPRODUK, HARGA, MEREK, MODELTYPE, DIAMETER, PANJANG, LEBAR, TINGGI,
           UNITPENGUKURAN, TKDNPRODUK, BERLAKUSAMPAI, JENISPRODUK, LAMAGARANSI, LAMAGARANSI2, JUMLAHSTOCK, KEMASAN, STATUS,
           KETERANGANTAMBAHAN, CREATED_DATE, CREATED_BY, REKANAN_ID, JUMLAHSTOCK_READY)
  			 	VALUES (
				  ".$this->getField("KATALOGID").",
				  '".$this->getField("NOPRODUK")."',
				  '".$this->getField("NAMAPRODUK")."',
  			  '".$this->getField("HARGA")."',
				  '".$this->getField("MEREK")."',
				  '".$this->getField("MODELTYPE")."',
				  '".$this->getField("DIAMETER")."',
				  '".$this->getField("PANJANG")."',
				  ".$this->getField("LEBAR").",
          ".$this->getField("TINGGI").",
          '".$this->getField("UNITPENGUKURAN")."',
          '".$this->getField("TKDNPRODUK")."',
          ".$this->getField("BERLAKUSAMPAI").",
          '".$this->getField("JENISPRODUK")."',
          '".$this->getField("LAMAGARANSI")."',
          '".$this->getField("LAMAGARANSI2")."',
          ".$this->getField("JUMLAHSTOCK").",
          '".$this->getField("KEMASAN")."',
          '".$this->getField("STATUS")."',
          '".$this->getField("KETERANGANTAMBAHAN")."',
          NOW(),
          '".$this->getField("CREATED_BY")."',
          '".$this->getField("CREATED_BY")."',
          '".$this->getField("JUMLAHSTOCK_READY")."'

				)";
    		// echo $str;exit;
        $this->query = $str;
		    $this->id = $this->getField("KATALOGID");
        return $this->execQuery($str);
      }
    }

    function insertRiwayatHarga(){
      if(!$this->canInsert())
        showMessageDlg("Data Users tidak dapat di-insert",true);
      else{
      /*Auto-generate primary key(s) by next max value (integer) */
        $this->setField("RIWAYATID", $this->getNextId("RIWAYATID","KATALOG_RIWAYAT_HARGA"));

    $str = "
        INSERT INTO KATALOG_RIWAYAT_HARGA (
           RIWAYATID, HARGABARU, HARGALAMA, KATALOGID, CREATED_DATE, CREATED_BY)
          VALUES (
          ".$this->getField("RIWAYATID").",
          '".$this->getField("HARGABARU")."',
          '".$this->getField("HARGALAMA")."',
          '".$this->getField("KATALOGID")."',
          NOW(),
          '".$this->getField("CREATED_BY")."'

        )";
        // echo $str;exit;
        $this->query = $str;
        return $this->execQuery($str);
      }
    }

    function insertLaporan(){
      if(!$this->canInsert())
        showMessageDlg("Data Users tidak dapat di-insert",true);
      else{
      /*Auto-generate primary key(s) by next max value (integer) */
        $this->setField("LAPORANID", $this->getNextId("LAPORANID","KATALOG_LAPORAN"));

    $str = "
        INSERT INTO KATALOG_LAPORAN (
           LAPORANID, NAMA, EMAIL, TELEPON, ALASAN, JENISLAPORAN, CREATED_DATE, BROWSER, KATALOGID)
          VALUES (
          ".$this->getField("LAPORANID").",
          '".$this->getField("NAMA")."',
          '".$this->getField("EMAIL")."',
          '".$this->getField("TELEPON")."',
          '".$this->getField("ALASAN")."',
          '".$this->getField("JENISLAPORAN")."',
          NOW(),
          '".$this->getField("BROWSER")."',
          '".$this->getField("KATALOGID")."'

        )";
        // echo $str;exit;
        $this->query = $str;
        return $this->execQuery($str);
      }
    }

    function canUpdate(){
      return true;
    }

    function update(){

		$str = "
				UPDATE KATALOG
				SET
					   NOPRODUK    	= '".$this->getField("NOPRODUK")."',
					   NAMAPRODUK       	= '".$this->getField("NAMAPRODUK")."',
					   HARGA    	= ".$this->getField("HARGA").",
					   MEREK     	= '".$this->getField("MEREK")."',
					   MODELTYPE    	= '".$this->getField("MODELTYPE")."',
             DIAMETER    = '".$this->getField("DIAMETER")."',
             PANJANG        = '".$this->getField("PANJANG")."',
             LEBAR        = '".$this->getField("LEBAR")."',
             TINGGI        = '".$this->getField("TINGGI")."',
             UNITPENGUKURAN         = '".$this->getField("UNITPENGUKURAN")."',
             TKDNPRODUK         = '".$this->getField("TKDNPRODUK")."',
             BERLAKUSAMPAI        = ".$this->getField("BERLAKUSAMPAI").",
             JENISPRODUK        = '".$this->getField("JENISPRODUK")."',
             LAMAGARANSI        = '".$this->getField("LAMAGARANSI")."',
             LAMAGARANSI2         = '".$this->getField("LAMAGARANSI2")."',
             JUMLAHSTOCK        = ".$this->getField("JUMLAHSTOCK").",
             KEMASAN        = '".$this->getField("KEMASAN")."',
             STATUS         = '".$this->getField("STATUS")."',
             KETERANGANTAMBAHAN         = '".$this->getField("KETERANGANTAMBAHAN")."',
             UPDATED_BY         = '".$this->getField("CREATED_BY")."',
         	   UPDATED_DATE 				= NOW(),
             JUMLAHSTOCK_READY = '".$this->getField("JUMLAHSTOCK_READY")."'
				WHERE  KATALOGID   	= ".$this->getField("KATALOGID")."
 				";
				  // echo $str; die();
          $this->query = $str;
        return $this->execQuery($str);
    }

    function updatePublish(){

    $str = "
        UPDATE KATALOG
        SET
             PUBLISH        = '".$this->getField("PUBLISH")."',
             PUBLISH_BY     = '".$this->getField("PUBLISH_BY")."',
             PUBLISH_DATE   = NOW()
        WHERE  KATALOGID    = ".$this->getField("KATALOGID")."
        ";
          // echo $str; die();
          $this->query = $str;
        return $this->execQuery($str);
    }

    function selectById($username){
      $str = "SELECT * FROM USER_LOGIN
              WHERE
                USER_LOGIN = '".$username."'";

		$this->query = $str;

      return $this->select($str);
    }

    function selectByParams($paramsArray=array(),$limit=-1,$from=-1, $stat="", $order="ORDER BY A.KATALOGID ASC"){
      $str = "SELECT
					A.*, B.USER_NAMA, CASE WHEN A.PUBLISH IS NULL THEN '0' ELSE A.PUBLISH END PUBLISH_STATUS,
          (SELECT COUNT(Z.FOTOID) COUNTFOTO FROM KATALOG_FOTO Z WHERE Z.KATALOGID=A.KATALOGID ) FOTO,
          (SELECT COUNT(Z.LAMPIRANID) COUNTFOTO FROM KATALOG_LAMPIRAN Z WHERE Z.KATALOGID=A.KATALOGID ) KATALOG
				FROM KATALOG A
				LEFT JOIN USER_LOGIN B ON A.REKANAN_ID = B.REKANAN_ID
				WHERE 1=1 ".$stat;
        foreach ($paramsArray as $key => $val) {
          // $str .= " AND $key = '$val' ";
          // ikn 20190218
          $pecah = explode("||", $key);
          if (count($pecah) > 1) {
            $str .= "AND $pecah[0] $pecah[1] $val ";
          } else {
            $str .= " AND $key = '$val' ";
          }
        }
      $str .= " ".$order;
        // echo $str; die();
      $this->query = $str;
      return $this->selectLimit($str,$limit,$from);
    }

    function selectByParamsRiwayatHarga($paramsArray=array(),$limit=-1,$from=-1, $stat="", $order="ORDER BY A.RIWAYATID ASC"){
      $str = "SELECT A.* FROM KATALOG_RIWAYAT_HARGA A
        WHERE 1=1  ".$stat;
        foreach ($paramsArray as $key => $val) {
          // $str .= " AND $key = '$val' ";
          // ikn 20190218
          $pecah = explode("||", $key);
          if (count($pecah) > 1) {
            $str .= "AND $pecah[0] $pecah[1] $val ";
          } else {
            $str .= " AND $key = '$val' ";
          }
        }
      $str .= " ".$order;
        // echo $str; die();
      $this->query = $str;
      return $this->selectLimit($str,$limit,$from);
    }

    function selectByParamsViewKatalog($paramsArray=array(),$limit=-1,$from=-1, $stat=""){
      $str = "SELECT A.* FROM VIEW_KATALOG A
        WHERE 1=1 ".$stat;
        foreach ($paramsArray as $key => $val) {
          $str .= " AND $key = '$val' ";
        }
      $str .= " ".$order;
        // echo $str; die();
      $this->query = $str;
      return $this->selectLimit($str,$limit,$from);
    }

    // function selectByParamsViewKatalog2($paramsArray=array(),$limit=-1,$from=-1, $stat=""){
    function selectByParamsViewKatalog2($paramsArray=array(),$limit=-1,$from=-1, $stat="", $order="ORDER BY a.rekanan_id ASC"){
      $str = "SELECT a.rekanan_id,a.user_nama, count(a.katalogid) total_katalog
              FROM view_katalog a
              WHERE 1=1 ".$stat."
              GROUP BY a.user_nama, a.rekanan_id ".$order;
        // echo $str; die();
      $this->query = $str;
      return $this->selectLimit($str,$limit,$from);
    }

    function selectByParamsPenawaran($paramsArray=array(),$limit=-1,$from=-1, $stat="", $order="ORDER BY A.CREATED_DATE ASC"){
      $str = "SELECT a.*, x.alasan from (
              select a.paket_id, a.total,
                (select created_date from katalog_rekanan where paket_id=a.paket_id limit 1),
                (select rekanan_id from katalog_rekanan where paket_id=a.paket_id limit 1),
                (select nama as nama_paket from paket where paket_id=a.paket_id limit 1),
                (select status from katalog_rekanan where paket_id=a.paket_id limit 1),
                (select status as status_str from katalog_rekanan where paket_id=a.paket_id limit 1),
                (select noinvoice from katalog_rekanan where paket_id=a.paket_id limit 1)
                from
                (
                  select paket_id, count(katalogid) total
                  from katalog_rekanan
                  group by paket_id
                ) a
              ) a
	      join paket x on a.paket_id=x.paket_id
               WHERE 1=1 ".$stat;
        foreach ($paramsArray as $key => $val) {
          // $str .= " AND $key = '$val' ";
          // ikn 20190218
          $pecah = explode("||", $key);
          if (count($pecah) > 1) {
            $str .= "AND $pecah[0] $pecah[1] $val ";
          } else {
            $str .= " AND $key = '$val' ";
          }
        }
      $str .= " ".$order;
        // echo $str; die();
      $this->query = $str;
      return $this->selectLimit($str,$limit,$from);
    }

    function getCountByParamsPenawaran($paramsArray=array(), $varStatement=""){
      $str = "SELECT COUNT(REKANAN_ID) AS ROWCOUNT FROM (
              select a.* from (
              select a.paket_id, a.noinvoice, a.total, a.status,
                (select created_date from katalog_rekanan where noinvoice=a.noinvoice limit 1),
                (select rekanan_id from katalog_rekanan where noinvoice=a.noinvoice limit 1)
                from
                (
                  select paket_id, noinvoice, count(katalogid) total, status
                  from katalog_rekanan
                  group by paket_id, noinvoice, status
                ) a
              ) a
	      join paket x on a.paket_id=x.paket_id
	      where x.alasan is null 
              ) A WHERE 1=1 ".$varStatement;
      foreach ($paramsArray as $key => $value) {
        $str .= " AND $key = '$val' ";
      }
      // echo $str; die();
      $this->select($str);
      if($this->firstRow())
        return $this->getField("ROWCOUNT");
      else
         return 0;
    }

    function getCountByParamsViewKatalog2($paramsArray=array(), $varStatement=""){
      $str = "SELECT COUNT(REKANAN_ID) AS ROWCOUNT FROM
              (SELECT a.rekanan_id,a.user_nama, count(a.katalogid) total_katalog
              FROM view_katalog a
              GROUP BY a.user_nama, a.rekanan_id
              ) A WHERE 1=1 ".$varStatement;
      foreach ($paramsArray as $key => $value) {
        $str .= " AND $key = '$val' ";
      }
      $this->select($str);
      if($this->firstRow())
        return $this->getField("ROWCOUNT");
      else
         return 0;
    }

    function selectByParamsViewKatalogSearch($paramsArray=array(),$limit=-1,$from=-1, $stat="",$order=" ORDER BY A.PUBLISH DESC"){
      $str = "SELECT A.* FROM VIEW_KATALOG_SEARCH A
        WHERE 1=1 ".$stat;
        foreach ($paramsArray as $key => $val) {
          $str .= " AND $key = '$val' ";
        }
      $str .= " ".$order;
        // echo $str; die();
      $this->query = $str;
      return $this->selectLimit($str,$limit,$from);
    }

    function getCountByParamsViewKatalogSearch($paramsArray=array(), $varStatement=""){
      $str = "SELECT COUNT(KATALOGID) AS ROWCOUNT FROM
              (SELECT A.KATALOGID FROM VIEW_KATALOG_SEARCH A WHERE 1=1 ".$varStatement."
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

    function selectByParamsViewKatalogByKategori($paramsArray=array(),$limit=-1,$from=-1, $stat=""){
      $str = "SELECT A.* FROM VIEW_KATALOG_BY_KATEGORI A
        WHERE 1=1 ".$stat;
        foreach ($paramsArray as $key => $val) {
          $str .= " AND $key = '$val' ";
        }
      $str .= " ".$order;
        // echo $str; die();
      $this->query = $str;
      return $this->selectLimit($str,$limit,$from);
    }

    function selectByParamsViewKatalogByKategori2($paramsArray=array(),$limit=-1,$from=-1, $stat=""){
      $str = "SELECT A.NAMAPRODUK, A.USER_NAMA, A.HARGA, A.KATALOGID
              FROM VIEW_KATALOG_BY_KATEGORI A
              WHERE 1=1 ".$stat;
        foreach ($paramsArray as $key => $val) {
          $str .= " AND $key = '$val' ";
        }
      $str .= " ".$order;
      $str .= " GROUP BY A.NAMAPRODUK, A.USER_NAMA, A.HARGA, A.KATALOGID";
        // echo $str; die();
      $this->query = $str;
      return $this->selectLimit($str,$limit,$from);
    }

    function getCountByParamsViewKatalogByKategori($paramsArray=array(), $statement='')
    {
      $str = "SELECT COUNT(*) AS ROWCOUNT
              FROM ( SELECT A.* FROM VIEW_KATALOG_BY_KATEGORI A
              WHERE 1=1 ";
      while(list($key,$val)=each($paramsArray))
      {
        // $str .= " AND $key = '$val' ";
        // ikn 20190218
        $pecah = explode("||", $key);
        if (count($pecah) > 1) {
          $str .= "AND $pecah[0] $pecah[1] $val ";
        } else {
          $str .= " AND $key = '$val' ";
        }
      }
      $str .= $statement;
      $str .= ') A where 1 = 1';

      $this->select($str);
      $this->query = $str;
      // echo $str; exit();
      if($this->firstRow())
        return $this->getField("ROWCOUNT");
      else
        return 0;
    }

    function getCountByParamsViewKatalogByKategori2($paramsArray=array(), $statement='')
    {
      $str = "SELECT COUNT(*) AS ROWCOUNT
              FROM ( SELECT A.NAMAPRODUK, A.USER_NAMA, A.HARGA, A.KATALOGID
              FROM VIEW_KATALOG_BY_KATEGORI A
              WHERE 1=1 ";
      while(list($key,$val)=each($paramsArray))
      {
        // $str .= " AND $key = '$val' ";
        // ikn 20190218
        $pecah = explode("||", $key);
        if (count($pecah) > 1) {
          $str .= "AND $pecah[0] $pecah[1] $val ";
        } else {
          $str .= " AND $key = '$val' ";
        }
      }
      $str .= $statement;
      $str .= " GROUP BY A.NAMAPRODUK, A.USER_NAMA, A.HARGA, A.KATALOGID";
      $str .= ') A where 1 = 1';

      $this->select($str);
      $this->query = $str;
      // echo $str; exit();
      if($this->firstRow())
        return $this->getField("ROWCOUNT");
      else
        return 0;
    }

    function getCountByParams($paramsArray=array(), $varStatement=""){
      $str = "SELECT COUNT(KATALOGID) AS ROWCOUNT FROM KATALOG A WHERE 1=1 ".$varStatement;
      foreach ($paramsArray as $key => $value) {
        $str .= " AND $key = '$val' ";
      }
      $this->select($str);
      if($this->firstRow())
        return $this->getField("ROWCOUNT");
      else
         return 0;
    }

    function delete()
    {
      $str = "DELETE FROM KATALOG
                  WHERE
                    KATALOGID = ".$this->getField("KATALOGID")."";
                    // echo $str; die();
      $this->query = $str;
          return $this->execQuery($str);
      }

    function deleteAll()
    {
      $katalog_foto             = "DELETE FROM KATALOG_FOTO WHERE KATALOGID = ".$this->getField("KATALOGID")."";
      $this->query = $katalog_foto;
      $this->execQuery($katalog_foto);

      $katalog_kategori_rekanan = "DELETE FROM KATALOG_KATEGORI_REKANAN WHERE KATALOGID = ".$this->getField("KATALOGID")."";
      $this->query = $katalog_kategori_rekanan;
      $this->execQuery($katalog_kategori_rekanan);

      $katalog_lampiran         = "DELETE FROM KATALOG_LAMPIRAN WHERE KATALOGID = ".$this->getField("KATALOGID")."";
      $this->query = $katalog_lampiran;
      $this->execQuery($katalog_lampiran);

      $katalog_riwayat_harga    = "DELETE FROM KATALOG_RIWAYAT_HARGA WHERE KATALOGID = ".$this->getField("KATALOGID")."";
      $this->query = $katalog_riwayat_harga;
      $this->execQuery($katalog_riwayat_harga);

      $str                      = "DELETE FROM KATALOG WHERE KATALOGID = ".$this->getField("KATALOGID")."";
      $this->query = $str;
          return $this->execQuery($str);
      }


  }
?>
