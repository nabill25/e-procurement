<?php
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 * 
 */
  
  include_once('entity.php');

  class Contractingsuratpesanan extends Entity{

	var $query;
     
  function __construct(){
    $this->Entity();
  }

  function canInsert(){
    return true;
  }  

  function insert()
  { 
    /*Auto-generate primary key(s) by next max value (integer) */
    $this->setField("SURATPESANANID", $this->getNextId("SURATPESANANID","CONTRACTING_SURAT_PESANAN")); 

    $str = "
    INSERT INTO CONTRACTING_SURAT_PESANAN 
    ( SURATPESANANID, CONTRACTINGREKANANPROSES1ID, NOMOR_SURAT, TANGGAL, CREATED_BY, CREATED_DATE) 
      VALUES (
          ".$this->getField("SURATPESANANID").",
          ".$this->getField("CONTRACTINGREKANANPROSES1ID").",
          '".$this->getField("NOMOR_SURAT")."',
          ".$this->getField("TANGGAL").",
          ".$this->getField("CREATED_BY").",
          CURRENT_TIMESTAMP
      )"; 
    // echo $str; die();
    $this->query = $str;
    if ($this->execQuery($str)) { 
      $this->id = $this->getField("SURATPESANANID");
      return TRUE;
    } else { 
      return FALSE;
    }
  }

  function inserMaterial()
  { 
    /*Auto-generate primary key(s) by next max value (integer) */
    $this->setField("SURATPESANANMATERIALID", $this->getNextId("SURATPESANANMATERIALID","CONTRACTING_SURAT_PESANAN_MATERIAL")); 

    $str = "
    INSERT INTO CONTRACTING_SURAT_PESANAN_MATERIAL 
    ( SURATPESANANMATERIALID, SURATPESANANID, MATERIALID, NAMA, HARGA_SATUAN, KETERANGAN, QTY, TOTAL, CREATED_BY, CREATED_DATE, SATUAN,SIFAT) 
      VALUES (
          ".$this->getField("SURATPESANANMATERIALID").",
          ".$this->getField("SURATPESANANID").",
          ".$this->getField("MATERIALID").",
          '".$this->getField("NAMA")."',
          ".$this->getField("HARGA_SATUAN").",
          '".$this->getField("KETERANGAN")."',
          ".$this->getField("QTY").",
          ".$this->getField("TOTAL").",
          ".$this->getField("CREATED_BY").",
          CURRENT_TIMESTAMP,
          '".$this->getField("SATUAN")."',
          '".$this->getField("SIFAT")."'
      )"; 
    // echo $str; die();
    $this->query = $str;
    if ($this->execQuery($str)) { 
      return TRUE;
    } else { 
      return FALSE;
    }
  }

  function update()
  { 
    $str = "
   UPDATE  CONTRACTING_SURAT_PESANAN
    SET
         NOMOR_SURAT        = '".$this->getField("NOMOR_SURAT")."',
         TANGGAL        = ".$this->getField("TANGGAL").",
         UPDATED_BY    = '".$this->getField("CREATED_BY")."',
         UPDATED_DATE  = CURRENT_TIMESTAMP
    WHERE  SURATPESANANID =  ".$this->getField("SURATPESANANID")."
    "; 
    
    $this->query = $str;
    if ($this->execQuery($str)) { 
      return TRUE;
    } else { 
      return FALSE;
    }
  } 

  function updateSuratPesananMaterial()
  { 
    $str = "
   UPDATE  CONTRACTING_SURAT_PESANAN_MATERIAL
    SET
         STATUS_TERIMA        = '".$this->getField("STATUS_TERIMA")."',
         STATUS_KETERANGAN        = '".$this->getField("STATUS_KETERANGAN")."',
         PERSENTASE        = '".$this->getField("PRESENTASE")."',
         TANGGAL_TERIMA        = ".$this->getField("TANGGAL_TERIMA").",
         UPDATED_BY    = '".$this->getField("CREATED_BY")."',
         UPDATED_DATE  = CURRENT_TIMESTAMP
    WHERE  SURATPESANANMATERIALID =  ".$this->getField("SURATPESANANMATERIALID")."
    "; 
    // echo $str; die;
    $this->query = $str;
    if ($this->execQuery($str)) { 
      return TRUE;
    } else { 
      return FALSE;
    }
  } 
  
  function selectByParams($paramsArray=array(),$limit=-1,$from=-1, $stat="", $order="ORDER BY A.SURATPESANANID ASC"){
    $str = "SELECT
				A.*
			FROM CONTRACTING_SURAT_PESANAN A 
			WHERE 1=1 ".$stat;
      foreach ($paramsArray as $key => $val) {
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

  function selectByParamsSuratPesananMaterial($paramsArray=array(),$limit=-1,$from=-1, $stat="", $order="ORDER BY A.SURATPESANANMATERIALID ASC"){
    $str = "SELECT
        A.*
      FROM CONTRACTING_SURAT_PESANAN_MATERIAL A 
      WHERE 1=1 ".$stat;
      foreach ($paramsArray as $key => $val) {
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

  function selectByParamsSuratPesanan($paramsArray=array(),$limit=-1,$from=-1, $stat="", $order="ORDER BY A.SURATPESANANID ASC"){
    $str = "SELECT A.SURATPESANANID, A.CONTRACTINGREKANANPROSES1ID, A.NOMOR_SURAT, A.TANGGAL, A.CREATED_BY, A.CREATED_DATE ,
            B.PAKET_ID, B.CONTRACTINGREKANANID, CONCAT(D.NAMA, ' ', C.NAMA) REKANAN, B.REKANAN_ID,
  (SELECT SUM(Z.TOTAL) FROM CONTRACTING_SURAT_PESANAN_MATERIAL Z WHERE A.SURATPESANANID = Z.SURATPESANANID ) TOTAL
            FROM 
            CONTRACTING_SURAT_PESANAN A 
            JOIN CONTRACTING_REKANAN_PROSES1 B ON A.CONTRACTINGREKANANPROSES1ID = B.CONTRACTINGREKANANPROSES1ID
            JOIN REKANAN C ON B.REKANAN_ID = C.REKANAN_ID
            JOIN REKANAN_TIPE D ON C.REKANAN_TIPE_ID = D.REKANAN_TIPE_ID 
            WHERE A.SURATPESANANID IN (SELECT X.SURATPESANANID FROM CONTRACTING_SURAT_PESANAN_MATERIAL X WHERE A.SURATPESANANID=X.SURATPESANANID)  ".$stat;
      foreach ($paramsArray as $key => $val) {
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

  public function delMaterialAll()
  {
    $strDel = "
    DELETE FROM CONTRACTING_SURAT_PESANAN_MATERIAL WHERE SURATPESANANID=".$this->getField("SURATPESANANID")." "; 
    $this->query = $str;
    return $this->execQuery($strDel);
  }

  public function delSuratPesanan()
  {
    // Hapus Surat Pesanan yang gak ada material nya
    $strDelKosong = "DELETE FROM CONTRACTING_SURAT_PESANAN WHERE SURATPESANANID NOT IN (SELECT A.SURATPESANANID 
                     FROM CONTRACTING_SURAT_PESANAN A 
                     WHERE A.SURATPESANANID IN (SELECT X.SURATPESANANID FROM CONTRACTING_SURAT_PESANAN_MATERIAL X WHERE A.SURATPESANANID = X.SURATPESANANID))";
    $this->query = $strDelKosong;
    $this->execQuery($strDelKosong);
    // END

    $strDel = "
    DELETE FROM CONTRACTING_SURAT_PESANAN WHERE SURATPESANANID=".$this->getField("SURATPESANANID")." "; 
    $this->query = $strDel;
    return $this->execQuery($strDel);
  }

  function delete()
  {
    $str = "DELETE FROM CONTRACTING_SURAT_PESANAN_MATERIAL
                WHERE
                  SURATPESANANMATERIALID = ".$this->getField("SURATPESANANMATERIALID")."";
                  // echo $str; die();
    $this->query = $str;
        return $this->execQuery($str);
  }
 
  }
?>
