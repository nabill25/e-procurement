<?php

include_once("functions/string.func.php");
include_once("functions/date.func.php");

class api_akmal extends CI_Controller {

	function __construct() {
		parent::__construct(); $this->db->query("SET TIME ZONE 'Asia/Jakarta'"); 
		
		//kauth
		if (!$this->kauth->getInstance()->hasIdentity())
		{ 
		}       
		
		/* GLOBAL VARIABLE */
		$this->ID = isset($this->kauth->getInstance()->getIdentity()->REKANAN_ID)?$this->kauth->getInstance()->getIdentity()->REKANAN_ID:'';   
	}	
	
	function registered_vendor() 
	{
		$this->load->model('Contracting');
		$contracting = new Contracting();
		
		$contracting->selectJenisPekerjaan();
		$data =  $this->db->query("select rt.nama ||' '|| r.nama as nama,r.alamat ,r.telepon_kode || r.telepon as no_telp,r.email,
string_agg(rbu.bidang_usaha_id,', ') as kbli
from rekanan r
left join rekanan_tipe rt on r.rekanan_tipe_id =rt.rekanan_tipe_id
left join user_login ul on ul.rekanan_id =r.rekanan_id
left join rekanan_bidang_usaha rbu on rbu.rekanan_id =r.rekanan_id 
where ul.user_status =1 and r.status_validasi =1
group by rt.nama ||' '|| r.nama ,r.alamat ,r.telepon_kode || r.telepon ,r.email")->result_array();
		
		echo json_encode($data);
	}
	
}
?>
