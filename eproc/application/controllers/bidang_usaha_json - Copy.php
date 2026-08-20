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

include_once("WEB-INF/functions/default.func.php");
include_once("WEB-INF/classes/utils/Validate.php");

class bidang_usaha_json extends CI_Controller {

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
	
	function bidang_usaha_combo_checkbox_json() 
	{
		$this->load->library("kauth");  $userLogin = new kauth(); 
		$this->load->model("BidangUsaha");
		
		$bidang_usaha = new BidangUsaha();
		
		/* LOGIN CHECK */
		if ($userLogin->checkUserLogin()) 
		{ 
			$userLogin->retrieveUserInfo();
		}
		
		$reqMode= httpFilterGet("reqMode");
		$reqId= httpFilterGet("reqId");
		
		$i = 0;
		
		function checkVariabel($text, $search)
		{
			$arrText = explode(",",$text);
			for($i=0;$i<count($arrText);$i++)
			{
				if(trim($arrText[$i]) == trim($search))
					return true;	
			}
			return false;
		}
		
		$bidang_usaha->selectByParams(array("BIDANG_USAHA_PARENT_ID" => 0), -1,-1, $statement);
		
		while($bidang_usaha->nextRow())
		{
			$arr_json[$i]['id'] = $bidang_usaha->getField("BIDANG_USAHA_ID");
			$arr_json[$i]['text'] = $bidang_usaha->getField("NAMA_TANPA_KOMA");
			$parent = new BidangUsaha();
			$parent->selectByParams(array("BIDANG_USAHA_PARENT_ID" => $bidang_usaha->getField("BIDANG_USAHA_ID")));	
			$j=0;
			while($parent->nextRow())
			{
				$arr_parent[$j]['id'] = $parent->getField("BIDANG_USAHA_ID");
				$arr_parent[$j]['text'] = $parent->getField("NAMA_TANPA_KOMA");	
				$arr_parent[$j]['checked'] = checkVariabel($reqId, $parent->getField("BIDANG_USAHA_ID"));		
		
				$child = new BidangUsaha();
				$child->selectByParams(array("BIDANG_USAHA_PARENT_ID" => $parent->getField("BIDANG_USAHA_ID")));	
				$k=0;
				while($child->nextRow())
				{
					$arr_child[$k]['id'] = $child->getField("BIDANG_USAHA_ID");
					$arr_child[$k]['text'] = $child->getField("NAMA_TANPA_KOMA");		
					$arr_child[$k]['checked'] = checkVariabel($reqId, $child->getField("BIDANG_USAHA_ID"));			
					
		
					$sub = new BidangUsaha();
					$sub->selectByParams(array("BIDANG_USAHA_PARENT_ID" => $child->getField("BIDANG_USAHA_ID")));	
					$l=0;
					while($sub->nextRow())
					{
						$arr_sub[$l]['id'] = $sub->getField("BIDANG_USAHA_ID");
						$arr_sub[$l]['text'] = $sub->getField("NAMA_TANPA_KOMA");	
						$arr_sub[$l]['checked'] = checkVariabel($reqId, $sub->getField("BIDANG_USAHA_ID"));													
						
						$detil = new BidangUsaha();
						$detil->selectByParams(array("BIDANG_USAHA_PARENT_ID" => $sub->getField("BIDANG_USAHA_ID")));	
						$m=0;
						while($detil->nextRow())
						{
							$arr_detil[$m]['id'] = $detil->getField("BIDANG_USAHA_ID");
							$arr_detil[$m]['text'] = $detil->getField("NAMA_TANPA_KOMA");
							$arr_detil[$m]['checked'] = checkVariabel($reqId, $detil->getField("BIDANG_USAHA_ID"));												
							
							$m++;
						}
						
						$arr_sub[$l]['children'] = $arr_detil;
						
						unset($detil);
						unset($arrdetil);
						
						$l++;
					}
					$arr_child[$k]['children'] = $arr_sub;
					
					unset($sub);
					unset($arr_sub);
					$k++;
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
	
	function bidang_usaha_combo_json() 
	{
		$this->load->library("kauth");  $userLogin = new kauth(); 
		$this->load->model("BidangUsaha");
		
		
		/* create objects */
		$bidang_usaha = new BidangUsaha();
		
		/* LOGIN CHECK */
		if ($userLogin->checkUserLogin()) 
		{ 
			$userLogin->retrieveUserInfo();
		}
		
		$reqMode= httpFilterGet("reqMode");
		
		$i = 0;
		$arr_json[$i]['id'] = "";
		$arr_json[$i]['text'] = "Semua";
		$i++;
		
		$bidang_usaha->selectByParams(array("BIDANG_USAHA_PARENT_ID" => 0), -1,-1, $statement);
		
		while($bidang_usaha->nextRow())
		{
			$arr_json[$i]['id'] = $bidang_usaha->getField("BIDANG_USAHA_ID");
			$arr_json[$i]['text'] = $bidang_usaha->getField("NAMA");
			
			$parent = new BidangUsaha();
			$parent->selectByParams(array("BIDANG_USAHA_PARENT_ID" => $bidang_usaha->getField("BIDANG_USAHA_ID")));	
			$j=0;
			while($parent->nextRow())
			{
				$arr_parent[$j]['id'] = $parent->getField("BIDANG_USAHA_ID");
				$arr_parent[$j]['text'] = $parent->getField("NAMA");			
		
				$child = new BidangUsaha();
				$child->selectByParams(array("BIDANG_USAHA_PARENT_ID" => $parent->getField("BIDANG_USAHA_ID")));	
				$k=0;
				while($child->nextRow())
				{
					$arr_child[$k]['id'] = $child->getField("BIDANG_USAHA_ID");
					$arr_child[$k]['text'] = $child->getField("NAMA");			
					
		
					$sub = new BidangUsaha();
					$sub->selectByParams(array("BIDANG_USAHA_PARENT_ID" => $child->getField("BIDANG_USAHA_ID")));	
					$l=0;
					while($sub->nextRow())
					{
						$arr_sub[$l]['id'] = $sub->getField("BIDANG_USAHA_ID");
						$arr_sub[$l]['text'] = $sub->getField("NAMA");											
						
						$detil = new BidangUsaha();
						$detil->selectByParams(array("BIDANG_USAHA_PARENT_ID" => $sub->getField("BIDANG_USAHA_ID")));	
						$m=0;
						while($detil->nextRow())
						{
							$arr_detil[$m]['id'] = $detil->getField("BIDANG_USAHA_ID");
							$arr_detil[$m]['text'] = $detil->getField("NAMA");											
							
							$m++;
						}
						
						$arr_sub[$l]['children'] = $arr_detil;
						
						unset($detil);
						unset($arrdetil);
						
						$l++;
					}
					$arr_child[$k]['children'] = $arr_sub;
					
					unset($sub);
					unset($arr_sub);
					$k++;
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
	
	function bidang_usaha_json() 
	{
		$this->load->library("kauth");  $userLogin = new kauth(); 
		$this->load->model("BidangUsaha");
		
		$bidang_usaha = new BidangUsaha();
		
		$reqRow = httpFilterGet("reqRow");
		$reqKey = httpFilterGet("reqKey");
		
		function getGroupBySatuanKerja($id_induk, $x)
		{
			$child = new BidangUsaha();	
			$child->selectByParams(array("BIDANG_USAHA_PARENT_ID" => $id_induk, $arrCompanyID, $arrCompanyName), -1, -1);
			//echo $child->query;
			while($child->nextRow())
			{
			  $param_key = $child->getField('BIDANG_USAHA_ID');
			  $param_key_id = $param_key;
				  
			  $arrCompanyID[] =  $child->getField("BIDANG_USAHA_ID");
			  $arrCompanyName[] =  "[".$child->getField('KODE')."]".$child->getField("NAMA");
			  $x++;
			  $_SESSION['set_id'] = $x;
			getGroupBySatuanKerja($param_key, $x, $arrCompanyID[], $arrCompanyName[]);
			//echo $_SESSION['set_id'].'---';
				
			}	
			unset($child);
		}
		
		$i = $_SESSION['set_id'] = 0;
		
		$bidang_usaha->selectByParams(array(), -1, -1, $reqKey);
		while($bidang_usaha->nextRow())
		{
			$arrMenu[] = array("BIDANG_USAHA_ID" => $bidang_usaha->getField("BIDANG_USAHA_ID"), 
							   "BIDANG_USAHA_PARENT_ID" => $bidang_usaha->getField("BIDANG_USAHA_PARENT_ID"), 
							   "KODE" => $bidang_usaha->getField("KODE"), 
							   "NAMA" => $bidang_usaha->getField("NAMA")
							   );
		}
		
		$arrIndex = in_array_column(0, "SATKER_ID_PARENT", $arrMenu);
		if(is_array($arrIndex))
		{
		  for($i=0;$i<count($arrIndex);$i++)
		  {
			  $id_menu = $arrMenu[$arrIndex[$i]]["SATKER_ID"];
			  $uraian = trim(str_replace("'", "", $arrMenu[$arrIndex[$i]]["NAMA"]));
			  
			  $arrCompanyID[] =  $arrMenu[$arrIndex[$i]]["BIDANG_USAHA_ID"];
			  $arrCompanyName[] =  "[".$arrMenu[$arrIndex[$i]]["KODE"]."]".$arrMenu[$arrIndex[$i]]["NAMA"];
			  //drawSatkerByParentArray($id_menu, $link, $arrMenu);
		  }
		}
			$arrFinal = array("BIDANG_USAHA_ID" => $arrCompanyID, 
							  "BIDANG_USAHA_NAMA" => $arrCompanyName);
			echo json_encode($arrFinal);
	}
	
	function bidang_usaha_panel_combo_json() 
	{
		$this->load->library("kauth");  $userLogin = new kauth(); 
		$this->load->model("BidangUsaha");
		
		$bidang_usaha = new BidangUsaha();
		
		/* LOGIN CHECK */
		if ($userLogin->checkUserLogin()) 
		{ 
			$userLogin->retrieveUserInfo();
		}
		
		$reqMode= httpFilterGet("reqMode");
		$reqId = httpFilterGet("reqId");
		
		$i = 0;
		
		$bidang_usaha->selectByParamsPanelBidangUsaha(array("BIDANG_USAHA_PARENT_ID" => 0, "PANEL_ID" => $reqId), -1,-1, $statement);
		
		while($bidang_usaha->nextRow())
		{
			$arr_json[$i]['id'] = $bidang_usaha->getField("BIDANG_USAHA_ID");
			$arr_json[$i]['text'] = $bidang_usaha->getField("NAMA");
			
			$parent = new BidangUsaha();
			$parent->selectByParamsPanelBidangUsaha(array("BIDANG_USAHA_PARENT_ID" => $bidang_usaha->getField("BIDANG_USAHA_ID"), "PANEL_ID" => $reqId));	
			$j=0;
			while($parent->nextRow())
			{
				$arr_parent[$j]['id'] = $parent->getField("BIDANG_USAHA_ID");
				$arr_parent[$j]['text'] = $parent->getField("NAMA");			
		
				$child = new BidangUsaha();
				$child->selectByParamsPanelBidangUsaha(array("BIDANG_USAHA_PARENT_ID" => $parent->getField("BIDANG_USAHA_ID"), "PANEL_ID" => $reqId));	
				$k=0;
				while($child->nextRow())
				{
					$arr_child[$k]['id'] = $child->getField("BIDANG_USAHA_ID");
					$arr_child[$k]['text'] = $child->getField("NAMA");			
					
		
					$sub = new BidangUsaha();
					$sub->selectByParamsPanelBidangUsaha(array("BIDANG_USAHA_PARENT_ID" => $child->getField("BIDANG_USAHA_ID"), "PANEL_ID" => $reqId));	
					$l=0;
					while($sub->nextRow())
					{
						$arr_sub[$l]['id'] = $sub->getField("BIDANG_USAHA_ID");
						$arr_sub[$l]['text'] = $sub->getField("NAMA");											
						
						$detil = new BidangUsaha();
						$detil->selectByParamsPanelBidangUsaha(array("BIDANG_USAHA_PARENT_ID" => $sub->getField("BIDANG_USAHA_ID"), "PANEL_ID" => $reqId));	
						$m=0;
						while($detil->nextRow())
						{
							$arr_detil[$m]['id'] = $detil->getField("BIDANG_USAHA_ID");
							$arr_detil[$m]['text'] = $detil->getField("NAMA");											
							
							$m++;
						}
						
						$arr_sub[$l]['children'] = $arr_detil;
						
						unset($detil);
						unset($arrdetil);
						
						$l++;
					}
					$arr_child[$k]['children'] = $arr_sub;
					
					unset($sub);
					unset($arr_sub);
					$k++;
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
	
	function bidang_usaha_panel_rekanan_combo_json() 
	{
		$this->load->library("kauth");  $userLogin = new kauth(); 
		$this->load->model("BidangUsaha");
		
		$bidang_usaha = new BidangUsaha();
		
		/* LOGIN CHECK */
		if ($userLogin->checkUserLogin()) 
		{ 
			$userLogin->retrieveUserInfo();
		}
		
		$reqMode= httpFilterGet("reqMode");
		$reqId = httpFilterGet("reqId");
		
		$i = 0;
		
		$bidang_usaha->selectByParamsPanelRekananBidangUsaha(array("BIDANG_USAHA_PARENT_ID" => 0, "MD5(PANEL_REKANAN_ID)" => $reqId), -1,-1, $statement);
		
		while($bidang_usaha->nextRow())
		{
			$arr_json[$i]['id'] = $bidang_usaha->getField("BIDANG_USAHA_ID");
			$arr_json[$i]['text'] = $bidang_usaha->getField("NAMA");
			
			$parent = new BidangUsaha();
			$parent->selectByParamsPanelRekananBidangUsaha(array("BIDANG_USAHA_PARENT_ID" => $bidang_usaha->getField("BIDANG_USAHA_ID"), "MD5(PANEL_REKANAN_ID)" => $reqId));	
			$j=0;
			while($parent->nextRow())
			{
				$arr_parent[$j]['id'] = $parent->getField("BIDANG_USAHA_ID");
				$arr_parent[$j]['text'] = $parent->getField("NAMA");			
		
				$child = new BidangUsaha();
				$child->selectByParamsPanelRekananBidangUsaha(array("BIDANG_USAHA_PARENT_ID" => $parent->getField("BIDANG_USAHA_ID"), "MD5(PANEL_REKANAN_ID)" => $reqId));	
				$k=0;
				while($child->nextRow())
				{
					$arr_child[$k]['id'] = $child->getField("BIDANG_USAHA_ID");
					$arr_child[$k]['text'] = $child->getField("NAMA");			
					
		
					$sub = new BidangUsaha();
					$sub->selectByParamsPanelRekananBidangUsaha(array("BIDANG_USAHA_PARENT_ID" => $child->getField("BIDANG_USAHA_ID"), "MD5(PANEL_REKANAN_ID)" => $reqId));	
					$l=0;
					while($sub->nextRow())
					{
						$arr_sub[$l]['id'] = $sub->getField("BIDANG_USAHA_ID");
						$arr_sub[$l]['text'] = $sub->getField("NAMA");											
						
						$detil = new BidangUsaha();
						$detil->selectByParamsPanelRekananBidangUsaha(array("BIDANG_USAHA_PARENT_ID" => $sub->getField("BIDANG_USAHA_ID"), "MD5(PANEL_REKANAN_ID)" => $reqId));	
						$m=0;
						while($detil->nextRow())
						{
							$arr_detil[$m]['id'] = $detil->getField("BIDANG_USAHA_ID");
							$arr_detil[$m]['text'] = $detil->getField("NAMA");											
							
							$m++;
						}
						
						$arr_sub[$l]['children'] = $arr_detil;
						
						unset($detil);
						unset($arrdetil);
						
						$l++;
					}
					$arr_child[$k]['children'] = $arr_sub;
					
					unset($sub);
					unset($arr_sub);
					$k++;
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
	
	function master_bidang_usaha_add()
	{
		$this->load->library("kauth");  $userLogin = new kauth(); 
		$this->load->model("BidangUsaha");
		
		/* create objects */
		$bidang_usaha		= new BidangUsaha();
		
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
		
		$submitSimpan = httpFilterPost("submitSimpan");
		
		
		$reqParamKey	= httpFilterRequest("reqParamKey");
		
		$reqBidangTahun 	= httpFilterPost("reqBidangTahun");
		$reqBidangUsaha 		= httpFilterPost("reqBidangUsaha");
		$reqBidangId 		= httpFilterPost("reqBidangId");
		
		$tempBidangTahun	 = $reqBidangTahun ;
		$tempBidangUsaha	 	 = $reqBidangUsaha ;
		
		/* VALIDATION */
		$validate->setValidate(array(
								$reqBidangTahun,
								$reqBidangUsaha
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
			//echo  $bidang_usaha->getMaxIdTree($reqParamKey);
			//$bidang_usaha->setField("SATKER_ID", $bidang_usaha->getMaxIdTree($reqParamKey)+1); 
			$bidang_usaha->setField("BIDANG_USAHA_ID", $reqBidangId);
			$bidang_usaha->setField("BIDANG_USAHA_PARENT_ID", $reqParamKey);
			$bidang_usaha->setField("NAMA", $reqBidangUsaha);
			$bidang_usaha->setField("KODE", $reqBidangTahun);
			
			//$isAvalaible = $bidang_usahaCheck->checkSatkerAvalaible();		
			$bidang_usaha->insert();
			//echo $bidang_usaha->query;
			
			echo '<script language="javascript">';
			echo "window.opener.location.href = '".base_url()."main/loadUrl/main/master_bidang_usaha/?reqStatus=simpan';";
			echo "window.close();";
			echo '</script>';	
			
		}
	}
	
	function master_bidang_usaha_edit()
	{
		$this->load->library("kauth");  $userLogin = new kauth(); 
		$this->load->model("BidangUsaha");
		
		/* create objects */
		$bidang_usaha		= new BidangUsaha();
		
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
		
		$submitSimpan = httpFilterPost("submitSimpan");
		
		
		$reqParamKey	= httpFilterRequest("reqParamKey");
		$reqBidangTahun 	= httpFilterPost("reqBidangTahun");
		$reqBidangUsaha 		= httpFilterPost("reqBidangUsaha");
		
		$tempBidangTahun	 = $reqBidangTahun ;
		$tempBidangUsaha	 	 = $reqBidangUsaha ;
		
		/* VALIDATION */
		$validate->setValidate(array(
								$reqBidangTahun,
								$reqBidangUsaha
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
			//echo  $bidang_usaha->getMaxIdTree($reqParamKey);
			//$bidang_usaha->setField("SATKER_ID", $bidang_usaha->getMaxIdTree($reqParamKey)+1); 
			$bidang_usaha->setField("BIDANG_USAHA_ID", $reqParamKey);
			$bidang_usaha->setField("NAMA", $reqBidangUsaha);
			$bidang_usaha->setField("KODE", $reqBidangTahun);
			
			//$isAvalaible = $bidang_usahaCheck->checkSatkerAvalaible();		
			$bidang_usaha->update();
			//echo $bidang_usaha->query;
			echo '<script language="javascript">';
			echo "window.opener.location.href = '".base_url()."main/loadUrl/main/master_bidang_usaha/?reqStatus=simpan';";
			echo "window.close();";
			echo '</script>';	
			
		}
	}
	
}
?>
