<?php 
/**
 * @package 	eProcurement Application
 * @author 		eproc2025
 * @since 		25. Version 3.1
 * 
 */
  
  include_once('entity.php');

  class PaketKriteria extends Entity{ 

	var $query;
    /**
    * Class constructor.
    **/
    function __construct(){
		parent::__construct();
	}
	
    function PaketKriteria()
	{
      $this->Entity(); 
    }
	
	function insertPaketEvaluasiKeuangan()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$this->setField("PAKET_EVAL_KEUANGAN_ID", $this->getNextId("PAKET_EVAL_KEUANGAN_ID","PAKET_EVAL_KEUANGAN")); 

		$str = "		
			INSERT INTO PAKET_EVAL_KEUANGAN (
			   PAKET_EVAL_KEUANGAN_ID, PAKET_ID)  
  			 	VALUES (
				  ".$this->getField("PAKET_EVAL_KEUANGAN_ID").",
  				  ".$this->getField("PAKET_ID").")"; 
				
		$this->query = $str;
		return $this->execQuery($str);
    }

	function insertPaketKriteriaEvaluasi()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$this->setField("PAKET_KRITERIA_EVAL_ID", $this->getNextId("PAKET_KRITERIA_EVAL_ID","PAKET_KRITERIA_EVAL")); 

		$str = "		
			INSERT INTO PAKET_KRITERIA_EVAL (
			   PAKET_KRITERIA_EVAL_ID, PAKET_ID)  
  			 	VALUES (
				  ".$this->getField("PAKET_KRITERIA_EVAL_ID").",
  				  ".$this->getField("PAKET_ID").")"; 
				
		$this->query = $str;
		return $this->execQuery($str);
    }

	function insertPaketEvaluasiKD()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$this->setField("PAKET_EVAL_KD_ID", $this->getNextId("PAKET_EVAL_KD_ID","PAKET_EVAL_KD")); 

		$str = "		
			INSERT INTO PAKET_EVAL_KD (
			   PAKET_EVAL_KD_ID, PAKET_ID)  
  			 	VALUES (
				  ".$this->getField("PAKET_EVAL_KD_ID").",
  				  ".$this->getField("PAKET_ID").")"; 
				
		$this->query = $str;
		return $this->execQuery($str);
    }

	function insertPaketEvaluasiPengalaman()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$this->setField("PAKET_EVAL_PENGALAMAN_ID", $this->getNextId("PAKET_EVAL_PENGALAMAN_ID","PAKET_EVAL_PENGALAMAN")); 

		$str = "		
			INSERT INTO PAKET_EVAL_PENGALAMAN (
			   PAKET_EVAL_PENGALAMAN_ID, PAKET_ID)  
  			 	VALUES (
				  ".$this->getField("PAKET_EVAL_PENGALAMAN_ID").",
  				  ".$this->getField("PAKET_ID").")"; 
				
		$this->query = $str;
		return $this->execQuery($str);
    }

	function insertPaketEvaluasiPersonil()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$this->setField("PAKET_EVAL_PERSONIL_ID", $this->getNextId("PAKET_EVAL_PERSONIL_ID","PAKET_EVAL_PERSONIL")); 

		$str = "		
			INSERT INTO PAKET_EVAL_PERSONIL (
			   PAKET_EVAL_PERSONIL_ID, PAKET_ID)  
  			 	VALUES (
				  ".$this->getField("PAKET_EVAL_PERSONIL_ID").",
  				  ".$this->getField("PAKET_ID").")"; 
				
		$this->query = $str;
		return $this->execQuery($str);
    }
	
	function insertPaketEvaluasiPeralatan()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$this->setField("PAKET_EVAL_PERALATAN_ID", $this->getNextId("PAKET_EVAL_PERALATAN_ID","PAKET_EVAL_PERALATAN")); 

		$str = "		
			INSERT INTO PAKET_EVAL_PERALATAN (
			   PAKET_EVAL_PERALATAN_ID, PAKET_ID)  
  			 	VALUES (
				  ".$this->getField("PAKET_EVAL_PERALATAN_ID").",
  				  ".$this->getField("PAKET_ID").")"; 
				
		$this->query = $str;
		return $this->execQuery($str);
    }

	function insertPaketEvaluasiSertifikatLain()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$this->setField("PAKET_EVAL_SERTIFIKAT_LAIN_ID", $this->getNextId("PAKET_EVAL_SERTIFIKAT_LAIN_ID","PAKET_EVAL_SERTIFIKAT_LAIN")); 

		$str = "		
			INSERT INTO PAKET_EVAL_SERTIFIKAT_LAIN (
			   PAKET_EVAL_SERTIFIKAT_LAIN_ID, PAKET_ID)  
  			 	VALUES (
				  ".$this->getField("PAKET_EVAL_SERTIFIKAT_LAIN_ID").",
  				  ".$this->getField("PAKET_ID").")"; 
				
		$this->query = $str;
		return $this->execQuery($str);
    }

  } 
?>