<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * @package 	eProcurement Application
 * @author 		eproc2025
 * @since 		25. Version 3.1
 * 
 */

include_once("functions/string.func.php");
include_once("functions/date.func.php");
include_once("functions/default.func.php");

class katalog_kategori_json extends CI_Controller {

	function __construct() {
		parent::__construct(); $this->db->query("SET TIME ZONE 'Asia/Jakarta'"); 
		
		//kauth
		if (!$this->kauth->getInstance()->hasIdentity())
		{
			// trow to unauthenticated page!
			//redirect('Login');
		}       
		
		/* GLOBAL VARIABLE */
		$this->ID = $this->kauth->getInstance()->getIdentity()->REKANAN_ID;   
	}	

	function json() 
	{
		$this->load->model("KatalogKategori");
		
		$reqSearch = $this->input->get("reqSearch");
		// echo $reqSearch;exit;
		$i = 0;
		
		$katalog_kategori = new KatalogKategori();
		$katalog_kategori->selectByParamsAll(array("KATEGORI_PARENT_ID" => "0"), -1, -1, "");

		while($katalog_kategori->nextRow())
		{
			$arr_json[$i]['id'] = $katalog_kategori->getField("KATEGORI_ID");
			$arr_json[$i]['text'] = $katalog_kategori->getField("NAMA_VIEW");
			if ($katalog_kategori->getField("KATEGORI_STATUS") == '1') {
				$arr_json[$i]['aktif'] = '<img src="images/centang-cetak.png">';
			} else {
				$arr_json[$i]['aktif'] = '<img src="images/uncentang-cetak.png">';
			}
			
			$parent = new KatalogKategori();
			// $parent->selectByParamsAll(array("KATEGORI_PARENT_ID" => $katalog_kategori->getField("KATEGORI_ID")), -1, -1, " AND ((UPPER(NAMA) LIKE '%".strtoupper($reqSearch)."%') OR (UPPER(KODE) LIKE '%".strtoupper($reqSearch)."%') OR EXISTS(SELECT 1 FROM KATALOG_KATEGORI X WHERE UPPER(X.NAMA) LIKE '%".strtoupper($reqSearch)."' )) ");
			$parent->selectByParamsAll(array("KATEGORI_PARENT_ID" => $katalog_kategori->getField("KATEGORI_ID")), -1, -1, " AND UPPER(NAMA) LIKE '%".strtoupper($reqSearch)."%' ");
			
			$j=0;
			while($parent->nextRow())
			{
				$arr_parent[$j]['id'] = $parent->getField("KATEGORI_ID");
				$arr_parent[$j]['text'] = $parent->getField("NAMA_VIEW");			
				// $arr_parent[$j]['aktif'] = $parent->getField("KATEGORI_STATUS");	
				if ($parent->getField("KATEGORI_STATUS") == '1') {
					$arr_parent[$j]['aktif'] = '<img src="images/centang-cetak.png">';
				} else {
					$arr_parent[$j]['aktif'] = '<img src="images/uncentang-cetak.png">';
				}		 
				$arr_parent[$j]['children'] = $arr_child;
				
				unset($child);
				unset($arr_child);
				$j++;
			}
			$arr_json[$i]['children'] = $arr_parent;
		
			unset($parent);
			unset($arr_parent);
			$i++;
		}
		echo json_encode($arr_json);
	}

	function jsonaktif() 
	{
		$this->load->model("KatalogKategori");
		
		$reqSearch = $this->input->get("reqSearch");
		// echo $reqSearch;exit;
		$i = 0;
		
		$katalog_kategori = new KatalogKategori();

		// if ($reqSearch == '' || $reqSearch == null) {
			$katalog_kategori->selectByParams(array("KATEGORI_PARENT_ID" => "0"), -1, -1, "");
		// } else {
		// 	$katalog_kategori->selectByParams(array(), -1, -1, " AND ((UPPER(NAMA) LIKE '%".strtoupper($reqSearch)."%') OR (UPPER(KODE) LIKE '%".strtoupper($reqSearch)."%') OR EXISTS(SELECT 1 FROM BIDANG_USAHA X WHERE UPPER(X.NAMA) LIKE '%".strtoupper($reqSearch)."%' AND X.KATEGORI_ID LIKE A.KATEGORI_ID || '%')) ");

		// }
		// echo $katalog_kategori->query;exit;
		while($katalog_kategori->nextRow())
		{
			$arr_json[$i]['id'] = $katalog_kategori->getField("KATEGORI_ID");
			$arr_json[$i]['text'] = $katalog_kategori->getField("NAMA_VIEW");
			if ($katalog_kategori->getField("KATEGORI_STATUS") == '1') {
				$arr_json[$i]['aktif'] = '<img src="images/centang-cetak.png">';
			} else {
				$arr_json[$i]['aktif'] = '<img src="images/uncentang-cetak.png">';
			}
			
			$parent = new KatalogKategori();
			$parent->selectByParams(array("KATEGORI_PARENT_ID" => $katalog_kategori->getField("KATEGORI_ID")), -1, -1, " AND ((UPPER(NAMA) LIKE '%".strtoupper($reqSearch)."%') OR (UPPER(KODE) LIKE '%".strtoupper($reqSearch)."%') OR EXISTS(SELECT 1 FROM BIDANG_USAHA X WHERE UPPER(X.NAMA) LIKE '%".strtoupper($reqSearch)."%' AND X.KATEGORI_ID LIKE A.KATEGORI_ID || '%')) ");
			
			$j=0;
			while($parent->nextRow())
			{
				$arr_parent[$j]['id'] = $parent->getField("KATEGORI_ID");
				$arr_parent[$j]['text'] = $parent->getField("NAMA_VIEW");			
				// $arr_parent[$j]['aktif'] = $parent->getField("KATEGORI_STATUS");	
				if ($parent->getField("KATEGORI_STATUS") == '1') {
					$arr_parent[$j]['aktif'] = '<img src="images/centang-cetak.png">';
				} else {
					$arr_parent[$j]['aktif'] = '<img src="images/uncentang-cetak.png">';
				}		 
				$arr_parent[$j]['children'] = $arr_child;
				
				unset($child);
				unset($arr_child);
				$j++;
			}
			$arr_json[$i]['children'] = $arr_parent;
		
			unset($parent);
			unset($arr_parent);
			$i++;
		}
		echo json_encode($arr_json);
	} 
	
	function add()
	{
		$this->load->library("kauth");  $userLogin = new kauth(); 
		$this->load->model("KatalogKategori");
		
		/* create objects */
		$katalog_kategori		= new KatalogKategori();
		
		
		$reqSubmit = $this->input->post("reqSubmit");
		
		$reqKategoriId	= $this->input->post("reqKategoriId");
		$reqParentId	= $this->input->post("reqParentId");
		$reqId			= $this->input->post("reqId");
		
		$reqNamaKategori1 		= $this->input->post("reqNamaKategori1");
		$reqNamaKategori2 		= $this->input->post("reqNamaKategori2");
		$reqNama 				= $this->input->post("reqNama");
		$reqKode 				= $this->input->post("reqKode");
		$reqKategoriStatus 		= $this->input->post("reqKategoriStatus");
		
		/* ACTION BY REQMODE */
		if($reqId == ''){
			$katalog_kategori->setField("KATEGORI_ID", $reqKategoriId);
			$katalog_kategori->setField("NAMA_KATEGORI_1", $reqNamaKategori1);
			$katalog_kategori->setField("NAMA_KATEGORI_2", $reqNamaKategori2);
			$katalog_kategori->setField("KATEGORI_PARENT_ID", $reqParentId);
			$katalog_kategori->setField("KODE", $reqKode);
			$katalog_kategori->setField("NAMA", $reqNama);
			$katalog_kategori->setField("KATEGORI_STATUS", $reqKategoriStatus);
			//$isAvalaible = $katalog_kategoriCheck->checkSatkerAvalaible();		
			$katalog_kategori->insert();
			//echo $katalog_kategori->query;
			echo "Data berhasil disimpan";
		}
		else
		{
			$katalog_kategori->setField("KATEGORI_ID", $reqId);
			$katalog_kategori->setField("NAMA_KATEGORI_1", $reqNamaKategori1);
			$katalog_kategori->setField("NAMA_KATEGORI_2", $reqNamaKategori2);
			$katalog_kategori->setField("KATEGORI_PARENT_ID", $reqParentId);
			$katalog_kategori->setField("KODE", $reqKode);
			$katalog_kategori->setField("NAMA", $reqNama);
			$katalog_kategori->setField("KATEGORI_STATUS", $reqKategoriStatus);	
			$katalog_kategori->update();
			echo "Data berhasil diupdate";
			
		}
		
		//echo $record;
		
		
	}


	function addParent()
	{
		$this->load->library("kauth");  $userLogin = new kauth(); 
		$this->load->model("KatalogKategori");
		
		$katalog_kategori		= new KatalogKategori();
		
		
		$reqSubmit = $this->input->post("reqSubmit");
		
		$reqNamaKategori1 		= $this->input->post("reqNamaKategori1");
		$reqKode 				= $this->input->post("reqKode");
		$reqKategoriStatus 		= $this->input->post("reqKategoriStatus");
		$reqKategoriParentId 	= $this->input->post("reqKategoriParentId");

		$katalog_kategori->setField("NAMA_KATEGORI_1", $reqNamaKategori1);
		$katalog_kategori->setField("NAMA_KATEGORI_2", $reqNamaKategori1);
		$katalog_kategori->setField("KATEGORI_PARENT_ID", $reqKategoriParentId);
		$katalog_kategori->setField("KODE", $reqKode);
		$katalog_kategori->setField("NAMA", $reqNamaKategori1);
		$katalog_kategori->setField("KATEGORI_STATUS", $reqKategoriStatus);
  		$katalog_kategori->setField('CREATED_BY', $this->USER_LOGIN_ID);
  		if ($katalog_kategori->insertParent()) {
			echo "Data berhasil disimpan";
  		} else {
			echo "Data gagal disimpan";
  		}

	}
	
	function master_bidang_usaha_edit()
	{
		$this->load->library("kauth");  $userLogin = new kauth(); 
		$this->load->model("KatalogKategori");
		
		/* create objects */
		$katalog_kategori		= new KatalogKategori();
		
		/* LOGIN CHECK */
		if ($userLogin->checkUserLogin()) 
		{ 
			$userLogin->retrieveUserInfo();
			if($userLogin->userLevel == 6)
			{
				echo '<script language="javascript">';
				echo 'alert("Anda tidak berhak mengakses halaman ini. IP address anda telah kami catat sebagai rekanan yang mencoba membuka halaman administrator.");';
				echo 'top.location.href = "main/index";';
				echo '</script>';
				exit;		
			}
		}
		
		$submitSimpan = $this->input->post("submitSimpan");
		
		
		$reqParamKey	= $this->input->post("reqParamKey");
		$reqBidangTahun 	= $this->input->post("reqBidangTahun");
		$reqKatalogKategori 		= $this->input->post("reqKatalogKategori");
		
		$tempBidangTahun	 = $reqBidangTahun ;
		$tempKatalogKategori	 	 = $reqKatalogKategori ;
		
		/* VALIDATION */
		$validate->setValidate(array(
								$reqBidangTahun,
								$reqKatalogKategori
								));
		$validate->setMessage(array(
								'Anda belum mengisi Nama Satker',
								'Anda belum mengisi Kode Satker'
								));
		// trigger the validation
		if($submitSimpan == 'Simpan')
		{
			$validate->notEmpty();
			$alertMsg .= $validate->getMessage();
		}
		
		/* ACTION BY REQMODE */
		if($submitSimpan == 'Simpan' && $validate->statNotEmpty){
			//echo  $katalog_kategori->getMaxIdTree($reqParamKey);
			//$katalog_kategori->setField("SATKER_ID", $katalog_kategori->getMaxIdTree($reqParamKey)+1); 
			$katalog_kategori->setField("KATEGORI_ID", $reqParamKey);
			$katalog_kategori->setField("NAMA", $reqKatalogKategori);
			$katalog_kategori->setField("KODE", $reqBidangTahun);
			
			//$isAvalaible = $katalog_kategoriCheck->checkSatkerAvalaible();		
			$katalog_kategori->update();
			//echo $katalog_kategori->query;
			echo '<script language="javascript">';
			echo "window.opener.location.href = '".base_url()."main/loadUrl/main/master_bidang_usaha/?reqStatus=simpan';";
			echo "window.close();";
			echo '</script>';	
			
		}
	}
	
	function delete()
	{
		$this->load->model("KatalogKategori");
		$katalog_kategori = new KatalogKategori();
		/* PARAMETERS */
		$reqId			= $this->input->get('reqId');
		
		$katalog_kategori->selectByParams(array("KATEGORI_ID" => $reqId));
		$katalog_kategori->firstRow();
		//echo $katalog_kategori->query;exit;
	
		$katalog_kategori->setField("KATEGORI_ID", $reqId);
		
		if($katalog_kategori->delete())
		{
			echo "Data berhasil dihapus";
		}
		else
		{
			echo "Data gagal dihapus";
		}
	}

	function combo()
	{
		$this->load->model("KatalogKategori");
		$katalog_kategori = new KatalogKategori();
		
		$level = $this->input->get("level");
		$id = $this->input->get("id");

		if ($level == '1') {
			$katalog_kategori->selectByParamsLevel($level);
		} else {
			$katalog_kategori->selectByParamsLevel2($id);
		}
		
		// echo $user_login->query;exit;

		$arr_json = array();
		$i = 0;
		while($katalog_kategori->nextRow())
		{
			$arr_json[$i]['id'] = $katalog_kategori->getField("NAMA_KATEGORI");
			$arr_json[$i]['text'] = trim($katalog_kategori->getField("NAMA_KATEGORI"));
			$i++;
		}
		echo json_encode($arr_json);
	}
	
}
?>
