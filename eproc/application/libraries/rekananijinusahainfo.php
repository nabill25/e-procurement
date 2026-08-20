<?php
/**
 * @package 	eProcurement Application
 * @author 		eproc2025
 * @since 		25. Version 3.1
 * 
 */


  class rekananijinusahainfo{
	var $id_ijin_usaha;
	var $masa_berakhir;
    function rekananijinusahainfo(){
		 $this->emptyProps();
    }

    function emptyProps(){
		$this->id_ijin_usaha = "";
		$this->masa_berakhir = "";				
    }
		
    function getRekananIjinUsaha($rekanan_id){			
			
		$CI =& get_instance();
		$CI->load->model("RekananIjinUsaha");	
		
		$rekanan_ijin_usaha = new RekananIjinUsaha();
		$rekanan_ijin_usaha->selectByParams(array("REKANAN_ID" => $rekanan_id, "IJIN_USAHA_ID"=>1));
		if ($rekanan_ijin_usaha->firstRow()) {           
			$this->id_ijin_usaha = $rekanan_ijin_usaha->getField("REKANAN_IJIN_USAHA_ID");
			$this->masa_berakhir_ijin_usaha = $rekanan_ijin_usaha->getField("INFO_TANGGAL_BERAKHIR");
		}
		
		$this->query = $rekanan_ijin_usaha->query;
		
		unset($rekanan_ijin_usaha);
    }
		   
}
	
  $ijin_usaha_tanggal_berakhir = new rekananijinusahainfo();

?>