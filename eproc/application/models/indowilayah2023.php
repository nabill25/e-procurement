<?php 
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 * 
 */
  
  include_once('entity.php');

  class Indowilayah2023 extends Entity{ 

	var $query;

    function __construct(){
      parent::__construct();
    } 
	
    function selectProvinsi($paramsArray=array(),$limit=-1,$from=-1, $statement='')
	{
		$str = "SELECT namapropinsi from indo_wilayah_2023 group by namapropinsi order by namapropinsi asc "; 
		
		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}
		
		$this->query = $str; 
				
		return $this->selectLimit($str,$limit,$from); 
    }

    function selectKabKot($provinsi,$limit=-1,$from=-1)
	{
		$str = "SELECT namakabkota from indo_wilayah_2023 
				where namapropinsi = '".$provinsi."'
				group by namakabkota
				order by namakabkota asc"; 
		
		$this->query = $str; 
				
		return $this->selectLimit($str,$limit,$from); 
    }

    function selectKecamatan($namapropinsi,$namakabkota,$limit=-1,$from=-1)
	{
		$str = "SELECT namakecamatan from indo_wilayah_2023 
				where namakabkota = '".$namakabkota."' and namapropinsi='".$namapropinsi."'
				group by namakecamatan
				order by namakecamatan asc"; 
		
		$this->query = $str; 
				
		return $this->selectLimit($str,$limit,$from); 
    }

    function selectKelurahan($namapropinsi,$namakabkota,$namakecamatan,$limit=-1,$from=-1)
	{
		$str = "SELECT kelurahan from indo_wilayah_2023 
				where namakabkota = '".$namakabkota."' and namapropinsi='".$namapropinsi."' and namakecamatan='".$namakecamatan."'
				group by kelurahan
				order by kelurahan asc"; 
		// echo $str;
		$this->query = $str; 
				
		return $this->selectLimit($str,$limit,$from); 
    }

    
    
  } 
?>